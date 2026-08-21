<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Contrôleur de gestion des factures (CodeIgniter 3)
 * — Liste / CRUD / Paiements
 * — Relances automatiques par e‑mail (avant échéance, jour J, après échéance)
 * — Intégration FNE (Facture Normalisée Electronique)
 */
class Invoiceitem extends Admin_Controller
{
    // Configuration FNE
    private $fne_config = [
        'test_url' => 'http://54.247.95.108/ws/external/invoices/sign',
        'prod_url' => '',
        'api_key' => 'toCgyP5vdqXavkY16dg5qn7eae3N8bjZ',
        'timeout' => 30,
        'environment' => 'test',
        'point_of_sale' => 'PDV001',
        'establishment' => 'ETABLISSEMENT001'
    ];

    public function __construct()
    {
        parent::__construct();

        // Helpers / libs
        $this->load->helper(['form', 'url']);
        $this->config->load('app-config');
        $this->load->library(['Enc_lib', 'mailsmsconf', 'encoding_lib', 'customlib', 'form_validation', 'email']);

        // Models
        $this->load->model('invoice_model');
        $this->load->model('clients_model');
        $this->load->model('itemcategory_model');
        $this->load->model('item_model');
        $this->load->model('journal_model');
        $this->load->model('setting_model');

        // Charger la configuration FNE
        $this->load_fne_config();
    }

    /**
     * Charger la configuration FNE depuis la base de données
     */
    private function load_fne_config()
    {
        // Vérifier si la table fne_settings existe
        if ($this->db->table_exists('fne_settings')) {
            $settings = $this->db->get('fne_settings')->row_array();
            if ($settings) {
                $this->fne_config['api_key'] = $settings['api_key'] ?? $this->fne_config['api_key'];
                $this->fne_config['test_url'] = $settings['test_url'] ?? $this->fne_config['test_url'];
                $this->fne_config['prod_url'] = $settings['prod_url'] ?? '';
                $this->fne_config['environment'] = $settings['environment'] ?? 'test';
                $this->fne_config['point_of_sale'] = $settings['point_of_sale'] ?? 'PDV001';
                $this->fne_config['establishment'] = $settings['establishment'] ?? 'ETABLISSEMENT001';
            }
        }
    }

    /** Liste des factures */
    public function index()
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Invoiceitem/index');

        $data = [
            'title' => 'Liste des factures',
            'title_list' => 'Dernières factures',
        ];

        $this->load->view('layout/header', $data);
        $this->load->view('admin/invoice/list', $data);
        $this->load->view('layout/footer');
    }

    /** Liste Achats */
    public function achat()
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Caisse');
        $this->session->set_userdata('sub_menu', 'Invoice_achat/achat');

        $data = [
            'title' => 'Liste des factures',
            'title_list' => 'Dernières factures',
        ];

        $this->load->view('layout/header', $data);
        $this->load->view('admin/invoice_achat/listachat', $data);
        $this->load->view('layout/footer');
    }

    /** Données JSON du tableau */
    public function data()
    {
        echo $this->invoice_model->getListData();
    }

    /* =========================================================
     * INTÉGRATION FNE - FACTURE NORMALISÉE ÉLECTRONIQUE
     * =======================================================*/

    /**
     * Certification FNE d'une facture
     */
    public function certifyFNE()
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_edit')) {
            access_denied();
        }

        $response = ['status' => 'fail', 'message' => ''];

        try {
            $invoice_id = $this->input->post('id');

            if (empty($invoice_id)) {
                throw new Exception('ID de facture manquant');
            }

            // Récupérer la facture
            $invoice = $this->invoice_model->getInvoiceWithItems($invoice_id);
            if (!$invoice) {
                throw new Exception('Facture introuvable');
            }

            // Vérifier si déjà certifiée
            if (!empty($invoice['fne_reference'])) {
                throw new Exception('Cette facture est déjà certifiée FNE');
            }

            // Préparer les données FNE
            $fne_data = $this->prepareFNEData($invoice);

            // Valider les données
            $validation_errors = $this->validateFNEData($fne_data);
            if (!empty($validation_errors)) {
                throw new Exception("Données invalides: " . implode(", ", $validation_errors));
            }

            // Journaliser la requête
            $this->log_fne_request($invoice_id, $fne_data);

            // Appeler l'API FNE
            $api_response = $this->callFNEAPI($fne_data);

            if ($api_response['status'] === 'success') {
                // Mettre à jour la facture
                $update_data = [
                    'reference' => $api_response['data']['reference'] ?? '',
                    'token' => $api_response['data']['token'] ?? '',
                    'balance_sticker' => $api_response['data']['balance_sticker'] ?? 0,
                ];

                if ($this->invoice_model->updateFNEStatus($invoice_id, $update_data)) {
                    $response = [
                        'status' => 'success',
                        'message' => 'Facture certifiée FNE avec succès',
                        'data' => [
                            'reference' => $update_data['reference'],
                            'token' => $update_data['token'],
                            'balance_sticker' => $update_data['balance_sticker'],
                        ],
                    ];
                } else {
                    throw new Exception('Erreur lors de la mise à jour en base de données');
                }
            } else {
                throw new Exception($api_response['message']);
            }

        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            log_message('error', 'FNE Certification Error: ' . $e->getMessage());
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Préparer les données pour l'API FNE
     */
    private function prepareFNEData($invoice)
    {
        // Récupérer les informations de l'entreprise
        $company = $this->setting_model->get();
        $company = $company[0] ?? [];

        // Déterminer le template selon le type de client
        $template = 'B2C'; // Par défaut
        if (!empty($invoice['customer_ncc'])) {
            $template = 'B2B'; // Entreprise avec NCC
        } elseif (!empty($invoice['customer_country']) && $invoice['customer_country'] != 'CI') {
            $template = 'B2F'; // Client international
        }

        // Déterminer le type de facture
        $invoiceType = 'sale'; // vente

        // Mapper la méthode de paiement
        $paymentMethod = $this->mapPaymentMethod($invoice['method'] ?? 'cash');

        // Déterminer la taxe
        $taxCode = $this->getTaxCode($invoice);

        // Préparer les articles
        $items = [];
        foreach ($invoice['items'] as $item) {
            $items[] = [
                'taxes' => [$taxCode],
                'reference' => $item['item_reference'] ?? 'REF-' . str_pad($item['item_id'], 5, '0', STR_PAD_LEFT),
                'description' => $item['item_name'],
                'quantity' => (float) $item['quantity'],
                'amount' => (float) $item['unit_price'],
                'discount' => 0,
                'measurementUnit' => $this->getMeasurementUnit($item['unit'] ?? 'pcs'),
            ];
        }

        // Nom du client
        $clientCompanyName = trim($invoice['customer_name'] . ' ' . ($invoice['customer_last_name'] ?? ''));
        if (empty($clientCompanyName)) {
            $clientCompanyName = 'Client';
        }

        // Point de vente et établissement
        $pointOfSale = $this->fne_config['point_of_sale'];
        $establishment = $this->fne_config['establishment'];

        // Construire le payload
        $fne_data = [
            'invoiceType' => $invoiceType,
            'paymentMethod' => $paymentMethod,
            'template' => $template,
            'isRne' => false,
            'clientCompanyName' => $clientCompanyName,
            'clientPhone' => $invoice['customer_phone'] ?? '',
            'clientEmail' => $invoice['customer_email'] ?? '',
            'pointOfSale' => $pointOfSale,
            'establishment' => $establishment,
            'commercialMessage' => 'Merci pour votre confiance',
            'footer' => 'Service client: ' . ($company['email'] ?? 'contact@entreprise.ci'),
            'foreignCurrency' => '',
            'foreignCurrencyRate' => 0,
            'items' => $items,
            'discount' => 0,
        ];

        // Ajouter NCC client si B2B
        if ($template === 'B2B' && !empty($invoice['customer_ncc'])) {
            $fne_data['clientNcc'] = $invoice['customer_ncc'];
        }

        return $fne_data;
    }

    /**
     * Déterminer le code TVA
     */
    private function getTaxCode($invoice)
    {
        if (!$invoice['apply_tva'] || $invoice['tva_rate'] <= 0) {
            return 'TVAE'; // Exonéré
        }

        $tva_rate = (float) $invoice['tva_rate'];

        if ($tva_rate == 18) {
            return 'TVA';
        } elseif ($tva_rate == 9) {
            return 'TVAB';
        } elseif ($tva_rate == 0) {
            return 'TVAC';
        }

        return 'TVA';
    }

    /**
     * Mapper les méthodes de paiement
     */
    private function mapPaymentMethod($method)
    {
        $method = strtolower(trim($method));

        $mapping = [
            'cash' => 'cash',
            'espèce' => 'cash',
            'espèces' => 'cash',
            'espece' => 'cash',
            'card' => 'card',
            'carte' => 'card',
            'carte bancaire' => 'card',
            'check' => 'check',
            'chèque' => 'check',
            'cheque' => 'check',
            'mobile-money' => 'mobile-money',
            'mobile money' => 'mobile-money',
            'mobile' => 'mobile-money',
            'momo' => 'mobile-money',
            'transfer' => 'transfer',
            'virement' => 'transfer',
            'deferred' => 'deferred',
            'terme' => 'deferred',
            'crédit' => 'deferred',
            'credit' => 'deferred',
        ];

        return $mapping[$method] ?? 'cash';
    }

    /**
     * Obtenir l'unité de mesure
     */
    private function getMeasurementUnit($unit)
    {
        $unit = strtolower(trim($unit));

        $units = [
            'pcs' => 'pcs',
            'piece' => 'pcs',
            'pièce' => 'pcs',
            'kg' => 'kg',
            'kilogramme' => 'kg',
            'l' => 'l',
            'litre' => 'l',
            'm' => 'm',
            'metre' => 'm',
            'm2' => 'm2',
            'm3' => 'm3',
            'heure' => 'heure',
            'h' => 'heure',
            'jour' => 'jour',
            'j' => 'jour',
            'mois' => 'mois',
            'service' => 'service',
        ];

        return $units[$unit] ?? 'pcs';
    }

    /**
     * Valider les données FNE
     */
    private function validateFNEData($data)
    {
        $errors = [];

        // Champs requis
        $required_fields = ['invoiceType', 'paymentMethod', 'template', 'clientCompanyName', 'items'];
        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[] = "Le champ '$field' est requis";
            }
        }

        // Vérifier invoiceType
        if (isset($data['invoiceType']) && !in_array($data['invoiceType'], ['sale', 'purchase'])) {
            $errors[] = "invoiceType doit être 'sale' ou 'purchase'";
        }

        // Vérifier paymentMethod
        $valid_methods = ['cash', 'card', 'check', 'mobile-money', 'transfer', 'deferred'];
        if (isset($data['paymentMethod']) && !in_array($data['paymentMethod'], $valid_methods)) {
            $errors[] = "paymentMethod invalide";
        }

        // Vérifier template
        $valid_templates = ['B2B', 'B2C', 'B2F', 'B2G'];
        if (isset($data['template']) && !in_array($data['template'], $valid_templates)) {
            $errors[] = "template invalide";
        }

        // Vérifier les items
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $index => $item) {
                if (!isset($item['taxes']) || !is_array($item['taxes']) || empty($item['taxes'])) {
                    $errors[] = "Item $index: taxes requis";
                }
                if (!isset($item['description']) || empty($item['description'])) {
                    $errors[] = "Item $index: description requise";
                }
                if (!isset($item['quantity']) || $item['quantity'] <= 0) {
                    $errors[] = "Item $index: quantité doit être > 0";
                }
                if (!isset($item['amount']) || $item['amount'] <= 0) {
                    $errors[] = "Item $index: montant doit être > 0";
                }
            }
        }

        return $errors;
    }

    /**
     * Appeler l'API FNE
     */
    private function callFNEAPI($data)
    {
        // Vérifier que cURL est installé
        if (!function_exists('curl_version')) {
            return ['status' => 'fail', 'message' => 'cURL n\'est pas installé'];
        }

        $ch = curl_init();

        $url = $this->fne_config['environment'] === 'prod' && !empty($this->fne_config['prod_url'])
            ? $this->fne_config['prod_url']
            : $this->fne_config['test_url'];

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $this->fne_config['api_key'],
        ];

        $json_data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->fne_config['timeout'],
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);

        curl_close($ch);

        // Journaliser la réponse
        $this->log_fne_response($data, $response, $http_code, $curl_error);

        if ($curl_error) {
            return [
                'status' => 'fail',
                'message' => 'Erreur de connexion: ' . $curl_error,
            ];
        }

        $result = json_decode($response, true);

        if ($http_code >= 200 && $http_code < 300) {
            return [
                'status' => 'success',
                'data' => $result,
                'http_code' => $http_code,
            ];
        } else {
            $error_message = "Erreur API ({$http_code})";
            if (isset($result['message'])) {
                $error_message .= ": " . $result['message'];
            }
            if (isset($result['error'])) {
                $error_message .= " - " . $result['error'];
            }

            return [
                'status' => 'fail',
                'message' => $error_message,
                'details' => $result,
                'http_code' => $http_code,
            ];
        }
    }

    /**
     * Journaliser la requête FNE
     */
    private function log_fne_request($invoice_id, $data)
    {
        if (!$this->db->table_exists('fne_certification_log')) {
            return;
        }

        $log_data = [
            'invoice_id' => $invoice_id,
            'request_data' => json_encode($data),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('fne_certification_log', $log_data);
    }

    /**
     * Journaliser la réponse FNE
     */
    private function log_fne_response($request_data, $response, $http_code, $error)
    {
        if (!$this->db->table_exists('fne_certification_log')) {
            return;
        }

        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-1 minute')))
            ->order_by('id', 'DESC')
            ->limit(1)
            ->update('fne_certification_log', [
                'response_data' => $response,
                'http_code' => $http_code,
                'status' => ($http_code >= 200 && $http_code < 300) ? 'success' : 'error',
                'error_message' => $error,
            ]);
    }

    /**
     * Récupérer le statut FNE d'une facture
     */
    public function getFNEStatus()
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_view')) {
            access_denied();
        }

        $invoice_id = $this->input->post('id');
        $response = ['status' => 'fail', 'message' => ''];

        try {
            $fne_status = $this->invoice_model->getFNEStatus($invoice_id);
            if (!$fne_status) {
                throw new Exception('Facture introuvable');
            }

            $response = [
                'status' => 'success',
                'data' => $fne_status,
            ];

        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
    }

    /**
     * Certification FNE automatique après paiement complet
     */
    private function certifyFNEAutomatic($invoice_id)
    {
        try {
            $invoice = $this->invoice_model->getInvoiceWithItems($invoice_id);

            // Vérifier si la facture n'est pas déjà certifiée
            if (empty($invoice['fne_reference'])) {
                log_message('info', "Tentative de certification FNE automatique pour la facture #{$invoice_id}");

                $fne_data = $this->prepareFNEData($invoice);
                $fne_response = $this->callFNEAPI($fne_data);

                if ($fne_response['status'] === 'success') {
                    $update_data = [
                        'reference' => $fne_response['data']['reference'],
                        'token' => $fne_response['data']['token'],
                        'balance_sticker' => $fne_response['data']['balance_sticker'],
                    ];

                    $this->invoice_model->updateFNEStatus($invoice_id, $update_data);
                    log_message('info', "Certification FNE automatique réussie pour la facture #{$invoice_id}");
                } else {
                    log_message('error', "Certification FNE automatique échouée: " . $fne_response['message']);
                }
            }
        } catch (Exception $e) {
            log_message('error', "Erreur certification FNE automatique: " . $e->getMessage());
        }
    }

    /**
     * Tester le format des données FNE
     */
    public function test_fne_format($invoice_id)
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_view')) {
            access_denied();
        }

        $invoice = $this->invoice_model->getInvoiceWithItems($invoice_id);
        if (!$invoice) {
            show_error('Facture non trouvée');
        }

        $fne_data = $this->prepareFNEData($invoice);
        $validation_errors = $this->validateFNEData($fne_data);

        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Test Format FNE</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .success { color: green; font-weight: bold; }
                .error { color: red; font-weight: bold; }
                pre { background: #f4f4f4; padding: 15px; border: 1px solid #ccc; overflow: auto; }
                table { border-collapse: collapse; width: 100%; }
                td, th { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body>
            <h1>Test du format FNE - Facture #{$invoice['invoice_number']}</h1>";

        echo "<h2>Validation</h2>";
        if (empty($validation_errors)) {
            echo "<p class='success'>✓ Les données sont valides !</p>";
        } else {
            echo "<p class='error'>✗ Erreurs de validation :</p>";
            echo "<ul>";
            foreach ($validation_errors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
        }

        echo "<h2>Payload envoyé à l'API</h2>";
        echo "<pre>" . htmlspecialchars(json_encode($fne_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . "</pre>";

        echo "<h2>Informations de debug</h2>";
        echo "<p><strong>URL API:</strong> " . $this->fne_config['test_url'] . "</p>";
        echo "<p><strong>API Key:</strong> " . substr($this->fne_config['api_key'], 0, 5) . "..." . substr($this->fne_config['api_key'], -5) . "</p>";

        echo "</body></html>";
    }

    /* =========================================================
     * RELANCES AUTOMATIQUES
     * =======================================================*/

    /**
     * CRON: /index.php/invoiceitem/relance_factures (exécution quotidienne)
     */
    public function relance_factures()
    {
        $today = new DateTime('today');

        $invoices = $this->db
            ->select('id, invoice_number, customer_id, customer_email, customer_name, total_ttc, due_date, status')
            ->from('invoices')
            ->where('status', Invoice_model::STATUS_PENDING)
            ->get()->result_array();

        foreach ($invoices as $inv) {
            if (empty($inv['due_date'])) continue;
            $due = DateTime::createFromFormat('Y-m-d', $inv['due_date']);
            if (!$due) continue;

            $diff = (int)$today->diff($due)->format('%r%a');

            if ($diff === 3) {
                $this->envoyerRelance($inv, 'J-3');
            } elseif ($diff === 0) {
                $this->envoyerRelance($inv, 'J');
            } elseif ($diff === -7 || $diff === -15) {
                $this->envoyerRelance($inv, 'RETARD');
            }
        }

        echo json_encode(['status' => 'ok', 'processed' => count($invoices)]);
    }

    /** Envoi d'une relance e‑mail */
    private function envoyerRelance(array $facture, $niveau = 'J')
    {
        if (empty($facture['customer_email'])) {
            return false;
        }

        $subject = 'Relance facture ' . $facture['invoice_number'];

        $badge = [
            'J-3' => 'Rappel avant échéance',
            'J' => 'Échéance aujourd\'hui',
            'RETARD' => 'Relance — facture en retard',
        ];

        $message = '
            <div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#222">
                <p>Bonjour ' . html_escape($facture['customer_name']) . ',</p>
                <p><strong>' . $badge[$niveau] . '</strong></p>
                <p>Nous vous rappelons que votre facture <strong>' . html_escape($facture['invoice_number']) . '</strong><br>
                d\'un montant de <strong>' . number_format((float)$facture['total_ttc'], 0, ',', ' ') . " FCFA" . '</strong>
                arrive à échéance le <strong>' . date('d/m/Y', strtotime($facture['due_date'])) . "</strong>." . '</p>
                <p>Merci de procéder au règlement dans les meilleurs délais.</p>
                <p>Cordialement,<br>Service Facturation</p>
            </div>';

        $this->email->clear(true);
        $this->email->from('no-reply@votredomaine.com', 'Service Facturation');
        $this->email->to($facture['customer_email']);
        $this->email->subject($subject);
        $this->email->message($message);

        if (!$this->email->send()) {
            log_message('error', 'Echec relance facture ' . $facture['id']);
            return false;
        }
        return true;
    }

    /* =========================================================
     * FORM / CRUD
     * =======================================================*/

    public function form()
    {
        $this->session->set_userdata('top_menu', 'Caisse');
        $this->session->set_userdata('sub_menu', 'Invoiceitem/index');

        $data = [
            'title' => 'Nouvelle facture',
            'title_list' => 'Dernières factures',
            'itemcatlist' => $this->itemcategory_model->get(),
            'clients' => $this->clients_model->get(),
        ];

        $this->load->view('layout/header', $data);
        $this->load->view('admin/invoice/form', $data);
        $this->load->view('layout/footer', $data);
    }

    /** Création */
    public function add()
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_add')) {
            access_denied();
        }

        $response = ['status' => 'fail', 'message' => '', 'error' => []];

        try {
            $this->form_validation->set_rules('customer', 'Client', 'required|trim');
            $this->form_validation->set_rules('invoice_date', 'Date de facture', 'required');
            $this->form_validation->set_rules('due_date', "Date d'échéance", 'required');
            $this->form_validation->set_rules('item_category_id[]', 'Catégorie', 'required');
            $this->form_validation->set_rules('item_id[]', 'Article', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantité', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('price[]', 'Prix unitaire', 'required|numeric|greater_than[0]');

            if ($this->form_validation->run() == false) {
                $response['error'] = $this->form_validation->error_array();
                echo json_encode($response);
                return;
            }

            $data = [
                'customer_id' => $this->input->post('customer'),
                'user_name' => $this->input->post('user_name'),
                'invoice_number' => $this->invoice_model->generateInvoiceNumber(),
                'invoice_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('invoice_date')))),
                'due_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('due_date')))),
                'method' => $this->input->post('method'),
                'apply_tva' => $this->input->post('apply_tva') ? 1 : 0,
                'tva_rate' => $this->input->post('tva_rate'),
                'notes' => $this->input->post('notes'),
                'reference' => $this->input->post('reference'),
                'status' => Invoice_model::STATUS_PENDING,
                'created_at' => date('Y-m-d H:i:s'),
                'items' => [],
            ];

            $total_ht = 0;
            $categories = (array)$this->input->post('item_category_id');
            $items = (array)$this->input->post('item_id');
            $quantities = (array)$this->input->post('quantity');
            $prices = (array)$this->input->post('price');
            $units = (array)$this->input->post('unit');

            foreach ($categories as $i => $category_id) {
                if (empty($items[$i]) || empty($quantities[$i]) || empty($prices[$i])) {
                    throw new Exception("Données d'article manquantes");
                }
                $qte = (float)$quantities[$i];
                $pu = (float)$prices[$i];
                $lt = $qte * $pu;
                $total_ht += $lt;

                $data['items'][] = [
                    'category_id' => $category_id,
                    'item_id' => $items[$i],
                    'quantity' => $qte,
                    'unit_price' => $pu,
                    'unit' => isset($units[$i]) ? $units[$i] : '',
                    'line_total' => $lt,
                ];
            }

            $data['total_ht'] = $total_ht;
            $data['tva_amount'] = $data['apply_tva'] ? ($total_ht * $data['tva_rate'] / 100) : 0;
            $data['total_ttc'] = $total_ht + $data['tva_amount'];
            $data['remaining_amount'] = $data['total_ttc'];
            $data['amount_paid'] = 0;

            $insert_id = $this->invoice_model->add($data);

            if (!$insert_id) {
                throw new Exception("Erreur lors de l'enregistrement");
            }

            $response['status'] = 'success';
            $response['message'] = 'La facture a été créée avec succès';
            $response['invoice_id'] = $insert_id;
        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Invoice Add Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    /** Affichage */
    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_view')) {
            access_denied();
        }

        $data['invoice'] = $this->invoice_model->getInvoiceWithItems($id);
        if (!$data['invoice']) {
            $this->session->set_flashdata('error', 'Facture non trouvée');
            redirect('admin/invoiceitem');
        }

        $data['payments'] = $this->invoice_model->getPayments($id);
        $data['title'] = 'Détails de la facture';
        $data['page_title'] = 'Facture ' . $data['invoice']['invoice_number'];

        $this->load->view('layout/header', $data);
        $this->load->view('admin/invoice/view', $data);
        $this->load->view('layout/footer');
    }

    /** Formulaire d'édition */
    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_edit')) {
            access_denied();
        }

        try {
            $invoice = $this->invoice_model->getInvoiceWithItems($id);
            if (!$invoice) {
                $this->session->set_flashdata('error', 'Facture introuvable');
                redirect('admin/invoiceitem');
            }
            if ($this->invoice_model->isPaid($id)) {
                $this->session->set_flashdata('error', 'Cette facture est déjà payée');
                redirect('admin/invoiceitem');
            }

            $data = [
                'title' => 'Modifier la facture',
                'invoice' => $invoice,
                'clients' => $this->clients_model->get(),
                'itemcatlist' => $this->itemcategory_model->get(),
                'itemList' => $this->item_model->get(),
            ];

            $this->load->view('layout/header', $data);
            $this->load->view('admin/invoice/edit', $data);
            $this->load->view('layout/footer');
        } catch (Exception $e) {
            log_message('error', 'Invoice Edit Error: ' . $e->getMessage());
            $this->session->set_flashdata('error', "Une erreur est survenue lors de l'édition de la facture");
            redirect('admin/invoice');
        }
    }

    /** Mise à jour */
    public function update()
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_edit')) {
            access_denied();
        }

        $response = ['status' => 'fail', 'message' => '', 'error' => []];

        try {
            $id = $this->input->post('id');
            if ($this->invoice_model->isPaid($id)) {
                throw new Exception('Cette facture ne peut plus être modifiée');
            }

            $this->form_validation->set_rules('customer', 'Client', 'required|trim');
            $this->form_validation->set_rules('invoice_date', 'Date de facture', 'required');
            $this->form_validation->set_rules('due_date', "Date d'échéance", 'required');
            $this->form_validation->set_rules('item_category_id[]', 'Catégorie', 'required');
            $this->form_validation->set_rules('item_id[]', 'Article', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantité', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('price[]', 'Prix unitaire', 'required|numeric|greater_than[0]');

            if ($this->form_validation->run() == false) {
                $response['error'] = $this->form_validation->error_array();
                echo json_encode($response);
                return;
            }

            $data = [
                'id' => $id,
                'customer_id' => $this->input->post('customer'),
                'user_name' => $this->input->post('user_name'),
                'invoice_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('invoice_date')))),
                'due_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('due_date')))),
                'method' => $this->input->post('method'),
                'apply_tva' => $this->input->post('apply_tva') ? 1 : 0,
                'tva_rate' => $this->input->post('tva_rate'),
                'notes' => $this->input->post('notes'),
                'reference' => $this->input->post('reference'),
                'items' => [],
            ];

            $total_ht = 0;
            $categories = (array)$this->input->post('item_category_id');
            $items = (array)$this->input->post('item_id');
            $quantities = (array)$this->input->post('quantity');
            $prices = (array)$this->input->post('price');
            $units = (array)$this->input->post('unit');

            foreach ($categories as $i => $category_id) {
                if (empty($items[$i]) || empty($quantities[$i]) || empty($prices[$i])) {
                    throw new Exception("Données d'article manquantes");
                }
                $qte = (float)$quantities[$i];
                $pu = (float)$prices[$i];
                $lt = $qte * $pu;
                $total_ht += $lt;

                $data['items'][] = [
                    'category_id' => $category_id,
                    'item_id' => $items[$i],
                    'quantity' => $qte,
                    'unit_price' => $pu,
                    'unit' => isset($units[$i]) ? $units[$i] : '',
                    'line_total' => $lt,
                ];
            }

            $data['total_ht'] = $total_ht;
            $data['tva_amount'] = $data['apply_tva'] ? ($total_ht * $data['tva_rate'] / 100) : 0;
            $data['total_ttc'] = $total_ht + $data['tva_amount'];
            $data['remaining_amount'] = $data['total_ttc'];
            $data['amount_paid'] = 0;

            if (!$this->invoice_model->update($data)) {
                throw new Exception('Erreur lors de la mise à jour');
            }

            $response['status'] = 'success';
            $response['message'] = 'La facture a été mise à jour avec succès';
        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Invoice Update Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    /** Annulation */
    public function cancel()
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_cancel')) {
            access_denied();
        }

        $response = ['status' => 'fail', 'message' => ''];

        try {
            $id = $this->input->post('id');
            if ($this->invoice_model->isPaid($id)) {
                throw new Exception('Cette facture est déjà payée');
            }
            $reason = $this->input->post('reason');
            if (empty($reason)) {
                throw new Exception("Le motif d'annulation est requis");
            }

            $data = [
                'status' => Invoice_model::STATUS_CANCELLED,
                'cancelled_at' => date('Y-m-d H:i:s'),
                'cancelled_reason' => $reason,
            ];
            if (!$this->invoice_model->cancel($id, $data)) {
                throw new Exception("Erreur lors de l'annulation");
            }

            $response['status'] = 'success';
            $response['message'] = 'La facture a été annulée avec succès';
        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Invoice Cancel Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    /** Paiement */
    public function setPayment()
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_edit')) {
            access_denied();
        }

        $response = ['status' => 'fail', 'message' => ''];

        try {
            $this->form_validation->set_rules('invoice_id', 'Facture', 'required|numeric');
            $this->form_validation->set_rules('amount', 'Montant', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('payment_date', 'Date de paiement', 'required');
            $this->form_validation->set_rules('method', 'Méthode de paiement', 'required');

            if ($this->form_validation->run() == false) {
                $response['error'] = $this->form_validation->error_array();
                echo json_encode($response);
                return;
            }

            $invoice_id = $this->input->post('invoice_id');
            $amount = (float)$this->input->post('amount');
            $payment_date = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('payment_date'))));
            $method = $this->input->post('method');
            $reference = $this->input->post('reference');
            $notes = $this->input->post('notes');

            $invoice = $this->invoice_model->getInvoiceWithItems($invoice_id);
            if (!$invoice) {
                throw new Exception('Facture introuvable');
            }

            if ($amount > $invoice['remaining_amount']) {
                throw new Exception('Montant supérieur au reste à payer');
            }

            // Démarrer une transaction
            $this->db->trans_begin();

            // Ajouter le paiement
            $payment_data = [
                'invoice_id' => $invoice_id,
                'amount' => $amount,
                'payment_date' => $payment_date,
                'method' => $method,
                'reference' => $reference,
                'notes' => $notes,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            if (!$this->invoice_model->addPayment($payment_data)) {
                throw new Exception("Erreur lors de l'enregistrement du paiement");
            }

            // Mettre à jour le statut de la facture
            $new_remaining = $invoice['remaining_amount'] - $amount;
            if ($new_remaining <= 0) {
                $this->db->where('id', $invoice_id)
                    ->update('invoices', [
                        'status' => Invoice_model::STATUS_PAID,
                        'paid_at' => date('Y-m-d H:i:s'),
                    ]);

                // Certification FNE automatique
                $this->certifyFNEAutomatic($invoice_id);
            } else {
                $this->db->where('id', $invoice_id)
                    ->update('invoices', [
                        'status' => Invoice_model::STATUS_PARTIAL,
                    ]);
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                throw new Exception('Erreur lors de la transaction');
            } else {
                $this->db->trans_commit();
            }

            $response['status'] = 'success';
            $response['message'] = '✅ Paiement enregistré avec succès !';

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $response['message'] = '❌ Erreur: ' . $e->getMessage();
            log_message('error', 'Payment Add Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    public function addPaymentForm()
    {
        $data['rowID'] = (!empty($this->input->post('rowID')) && (int)$this->input->post('rowID') > 0) ? (int)$this->input->post('rowID') : 0;
        $data['remaining'] = (!empty($this->input->post('remaining')) && $this->input->post('remaining') > 0) ? $this->input->post('remaining') : 0;

        $this->load->view('admin/invoice/paymentForm', $data);
    }

    /** Impression */
    public function print()
    {
        $id = $this->input->post('id');
        $data['invoice'] = $this->invoice_model->getInvoiceWithItems($id);
        if (!$data['invoice']) {
            show_404();
            return;
        }

        $data['payments'] = $this->invoice_model->getPayments($id);
        $company = $this->setting_model->get();
        $data['company'] = $company[0];
        $data['totalAsletter'] = $this->asLetters((float)$data['invoice']['total_ttc']);

        $page = $this->load->view('admin/invoice/print', $data, true);
        echo json_encode(['status' => '1', 'error' => '', 'page' => $page]);
    }

    /** Envoi manuel d'une facture par e‑mail */
    public function sendEmail()
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_edit')) {
            access_denied();
        }

        $invoice_id = $this->input->post('id', 0);
        $response = ['status' => 'fail', 'message' => ''];

        try {
            $data['invoice'] = $this->invoice_model->getInvoiceWithItems($invoice_id);
            if (!$data['invoice']) {
                throw new Exception('Facture introuvable');
            }

            if (empty($data['invoice']['customer_email'])) {
                throw new Exception("Le client n'a pas d'adresse email");
            }

            $company = $this->setting_model->get();
            $data['company'] = $company[0];
            $data['totalAsletter'] = $this->asLetters((float)$data['invoice']['total_ttc']);
            $data['user'] = $this->customlib->getUserData();

            $payload = [
                'id' => $data['invoice']['id'],
                'data' => $data,
                'credential_for' => 'sendInvoice',
                'client_name' => $data['invoice']['customer_name'] . ' ' . $data['invoice']['customer_last_name'],
                'quotation_number' => $data['invoice']['invoice_number'],
                'quotation_date' => !empty($data['invoice']['invoice_date']) ? date('d/m/Y', strtotime($data['invoice']['invoice_date'])) : 'N/A',
                'email' => $data['invoice']['customer_email'],
                'user_name' => $data['user']['username'],
                'user_email' => $data['user']['email'],
            ];

            $this->mailsmsconf->mailsms('send_invoice', $payload);

            $response['status'] = 'success';
            $response['message'] = 'La facture a été envoyée avec succès';
        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Invoice Email Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    /** Conversion nombre → lettres (FR) */
    public function asLetters($number)
    {
        $convert = explode('.', $number);
        $num[17] = ['zero', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize'];
        $num[100] = [20 => 'vingt', 30 => 'trente', 40 => 'quarante', 50 => 'cinquante', 60 => 'soixante', 70 => 'soixante-dix', 80 => 'quatre-vingt', 90 => 'quatre-vingt-dix'];

        if (isset($convert[1]) && $convert[1] != '') {
            return $this->asLetters($convert[0]) . ' et ' . $this->asLetters($convert[1]);
        }
        if ($number < 0) {
            return 'moins ' . $this->asLetters(-$number);
        }
        if ($number < 17) {
            return $num[17][$number];
        } elseif ($number < 20) {
            return 'dix-' . $this->asLetters($number - 10);
        } elseif ($number < 100) {
            if ($number % 10 == 0) {
                return $num[100][$number];
            } elseif (substr($number, -1) == 1) {
                if (((int)($number / 10) * 10) < 70) {
                    return $this->asLetters((int)($number / 10) * 10) . '-et-un';
                } elseif ($number == 71) {
                    return 'soixante-et-onze';
                } elseif ($number == 81) {
                    return 'quatre-vingt-un';
                } elseif ($number == 91) {
                    return 'quatre-vingt-onze';
                }
            } elseif ($number < 70) {
                return $this->asLetters($number - $number % 10) . '-' . $this->asLetters($number % 10);
            } elseif ($number < 80) {
                return $this->asLetters(60) . '-' . $this->asLetters($number % 20);
            } else {
                return $this->asLetters(80) . '-' . $this->asLetters($number % 20);
            }
        } elseif ($number == 100) {
            return 'cent';
        } elseif ($number < 200) {
            return $this->asLetters(100) . ' ' . $this->asLetters($number % 100);
        } elseif ($number < 1000) {
            return $this->asLetters((int)($number / 100)) . ' ' . $this->asLetters(100) . ($number % 100 > 0 ? ' ' . $this->asLetters($number % 100) : '');
        } elseif ($number == 1000) {
            return 'mille';
        } elseif ($number < 2000) {
            return $this->asLetters(1000) . ' ' . $this->asLetters($number % 1000) . ' ';
        } elseif ($number < 1000000) {
            return $this->asLetters((int)($number / 1000)) . ' ' . $this->asLetters(1000) . ($number % 1000 > 0 ? ' ' . $this->asLetters($number % 1000) : '');
        } elseif ($number == 1000000) {
            return 'millions';
        } elseif ($number < 2000000) {
            return $this->asLetters(1000000) . ' ' . $this->asLetters($number % 1000000);
        } elseif ($number < 1000000000) {
            return $this->asLetters((int)($number / 1000000)) . ' ' . $this->asLetters(1000000) . ($number % 1000000 > 0 ? ' ' . $this->asLetters($number % 1000000) : '');
        }
    }
}