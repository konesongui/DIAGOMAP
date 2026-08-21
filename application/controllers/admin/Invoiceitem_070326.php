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
        'api_key' => 'toCgyP5vdqXavkY16dg5qn7eae3N8bjZ', // À configurer dans les paramètres
        'timeout' => 30
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

        // Email par défaut (si non configuré dans application/config/email.php)
        // $this->email->initialize([
        //     'protocol'  => 'smtp',
        //     'smtp_host' => 'smtp.votredomaine.com',
        //     'smtp_user' => 'no-reply@votredomaine.com',
        //     'smtp_pass' => '********',
        //     'smtp_port' => 587,
        //     'mailtype'  => 'html',
        //     'charset'   => 'utf-8',
        //     'newline'   => "\r\n",
        // ]);
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

    /**
     * Certification FNE d'une facture avec débogage amélioré
     */
    public function certifyFNE()
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_edit')) {
            access_denied();
        }

        $response = ['status' => 'fail', 'message' => 'Erreur lors de la certification FNE'];

        try {
            $invoice_id = $this->input->post('id');

            if (empty($invoice_id)) {
                throw new Exception('ID de facture manquant');
            }

            // Récupérer la facture complète
            $invoice = $this->invoice_model->getInvoiceWithItems($invoice_id);
            if (!$invoice) {
                throw new Exception('Facture introuvable');
            }

            // Vérifier si déjà certifiée
            if (!empty($invoice['fne_reference'])) {
                throw new Exception('Cette facture est déjà certifiée FNE');
            }

            // Préparer les données
            $fne_data = $this->prepareFNEData($invoice);

            // Valider les données
            $validation_errors = $this->validateFNEData($fne_data);
            if (!empty($validation_errors)) {
                throw new Exception("Données invalides:\n- " . implode("\n- ", $validation_errors));
            }

            // Sauvegarder pour debug
            $debug_file = FCPATH . 'uploads/fne_debug_' . $invoice_id . '_' . date('YmdHis') . '.json';
            file_put_contents($debug_file, json_encode($fne_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // Appeler l'API
            $fne_response = $this->callFNEAPI($fne_data);

            if ($fne_response['status'] === 'success') {
                // Mettre à jour la facture
                $update_data = [
                    'fne_certified' => 1,
                    'fne_reference' => $fne_response['data']['reference'] ?? '',
                    'fne_token' => $fne_response['data']['token'] ?? '',
                    'fne_balance_sticker' => $fne_response['data']['balance_sticker'] ?? 0,
                    'fne_certified_at' => date('Y-m-d H:i:s'),
                    'fne_response_data' => json_encode($fne_response['data'])
                ];

                if ($this->invoice_model->updateFNEStatus($invoice_id, $update_data)) {
                    $response = [
                        'status' => 'success',
                        'message' => 'Facture certifiée FNE avec succès',
                        'data' => [
                            'reference' => $update_data['fne_reference'],
                            'token' => $update_data['fne_token'],
                            'balance_sticker' => $update_data['fne_balance_sticker']
                        ]
                    ];
                } else {
                    throw new Exception('Erreur lors de la mise à jour de la facture');
                }
            } else {
                throw new Exception($fne_response['message']);
            }

        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            if (isset($debug_file)) {
                $response['debug_file'] = basename($debug_file);
            }
            log_message('error', 'FNE Certification Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    /**
     * Tester le format des données FNE sans appeler l'API
     */
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
            .valid { color: green; }
            .invalid { color: red; }
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
            echo "<ul class='error'>";
            foreach ($validation_errors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
        }

        echo "<h2>Payload envoyé à l'API</h2>";
        echo "<pre>" . htmlspecialchars(json_encode($fne_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . "</pre>";

        echo "<h2>Vérification des valeurs</h2>";
        echo "<table>";
        echo "<tr><th>Champ</th><th>Valeur</th><th>Statut</th></tr>";

        $checks = [
            'invoiceType' => ['sale', 'purchase'],
            'paymentMethod' => ['cash', 'card', 'check', 'mobile-money', 'transfer', 'deferred'],
            'template' => ['B2B', 'B2C', 'B2F', 'B2G'],
            'isRne' => ['boolean'],
            'clientCompanyName' => ['required'],
            'pointOfSale' => ['required'],
            'establishment' => ['required']
        ];

        foreach ($checks as $field => $expected) {
            $value = $fne_data[$field] ?? 'MANQUANT';
            $status = '';

            if (!isset($fne_data[$field])) {
                $status = "<span class='error'>✗ Champ manquant</span>";
            } elseif (in_array('required', $expected) && empty($value)) {
                $status = "<span class='error'>✗ Champ vide</span>";
            } elseif (in_array('boolean', $expected)) {
                $status = is_bool($value) ? "<span class='valid'>✓ Booléen valide</span>" : "<span class='error'>✗ Doit être un booléen</span>";
            } elseif (in_array($value, $expected)) {
                $status = "<span class='valid'>✓ Valeur valide</span>";
            } elseif (!in_array('required', $expected) && !in_array('boolean', $expected)) {
                $status = "<span class='invalid'>✗ Valeur doit être parmi: " . implode(', ', $expected) . "</span>";
            } else {
                $status = "<span class='valid'>✓ OK</span>";
            }

            echo "<tr>";
            echo "<td>$field</td>";
            echo "<td>" . htmlspecialchars(var_export($value, true)) . "</td>";
            echo "<td>$status</td>";
            echo "</tr>";
        }

        // Vérification des items
        if (isset($fne_data['items']) && is_array($fne_data['items'])) {
            foreach ($fne_data['items'] as $i => $item) {
                echo "<tr><td colspan='3' style='background:#e8e8e8'><strong>Item #" . ($i+1) . "</strong></td></tr>";

                $item_checks = [
                    'taxes' => ['array'],
                    'description' => ['required'],
                    'quantity' => ['number', 'positive'],
                    'amount' => ['number', 'positive']
                ];

                foreach ($item_checks as $field => $rules) {
                    $value = $item[$field] ?? 'MANQUANT';
                    $status = '';

                    if (!isset($item[$field])) {
                        $status = "<span class='error'>✗ Champ manquant</span>";
                    } elseif (in_array('required', $rules) && empty($value)) {
                        $status = "<span class='error'>✗ Champ vide</span>";
                    } elseif (in_array('array', $rules)) {
                        $status = is_array($value) ? "<span class='valid'>✓ Tableau valide</span>" : "<span class='error'>✗ Doit être un tableau</span>";
                        if (is_array($value) && empty($value)) {
                            $status = "<span class='error'>✗ Tableau vide</span>";
                        }
                    } elseif (in_array('number', $rules)) {
                        $status = is_numeric($value) ? "<span class='valid'>✓ Nombre valide</span>" : "<span class='error'>✗ Doit être un nombre</span>";
                        if (is_numeric($value) && in_array('positive', $rules) && $value <= 0) {
                            $status = "<span class='error'>✗ Doit être positif</span>";
                        }
                    }

                    echo "<tr>";
                    echo "<td style='padding-left:30px'>$field</td>";
                    echo "<td>" . htmlspecialchars(var_export($value, true)) . "</td>";
                    echo "<td>$status</td>";
                    echo "</tr>";
                }
            }
        }

        echo "</table>";

        echo "<h2>Informations de debug</h2>";
        echo "<p><strong>URL API:</strong> " . $this->fne_config['test_url'] . "</p>";
        echo "<p><strong>API Key:</strong> " . substr($this->fne_config['api_key'], 0, 5) . "..." . substr($this->fne_config['api_key'], -5) . "</p>";

        echo "</body></html>";
    }

    public function certifyFNE_15()
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_edit')) {
            access_denied();
        }

        $response = ['status' => 'fail', 'message' => 'Erreur lors de la certification FNE'];

        try {
            $invoice_id = $this->input->post('id');

            if (empty($invoice_id)) {
                throw new Exception('ID de facture manquant');
            }

            // Récupérer la facture complète
            $invoice = $this->invoice_model->getInvoiceWithItems($invoice_id);
            if (!$invoice) {
                throw new Exception('Facture introuvable');
            }

            // Vérifier si la facture est déjà certifiée FNE
            if (!empty($invoice['fne_reference'])) {
                throw new Exception('Cette facture est déjà certifiée FNE');
            }

            // Préparer les données pour l'API FNE
            $fne_data = $this->prepareFNEData($invoice);

            // Appeler l'API FNE
            $fne_response = $this->callFNEAPI($fne_data);

            // Traiter la réponse FNE
            if ($fne_response['status'] === 'success') {
                // Mettre à jour la facture avec les données FNE
                $update_data = [
                    'fne_certified' => 1,
                    'fne_reference' => $fne_response['data']['reference'],
                    'fne_token' => $fne_response['data']['token'],
                    'fne_balance_sticker' => $fne_response['data']['balance_sticker'],
                    'fne_certified_at' => date('Y-m-d H:i:s'),
                    'fne_response_data' => json_encode($fne_response['data'])
                ];

                if ($this->invoice_model->updateFNEStatus($invoice_id, $update_data)) {
                    $response = [
                        'status' => 'success',
                        'message' => 'Facture certifiée FNE avec succès',
                        'data' => $fne_response['data']
                    ];
                } else {
                    throw new Exception('Erreur lors de la mise à jour de la facture');
                }
            } else {
                throw new Exception($fne_response['message']);
            }

        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            log_message('error', 'FNE Certification Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    /**
     * Préparer les données pour l'API FNE
     */
    private function prepareFNEData_15($invoice)
    {
        // Déterminer le template FNE selon le type de client
        $template = 'B2C'; // Par défaut
        if (!empty($invoice['customer_ncc'])) {
            $template = 'B2B';
        }

        // Préparer les articles
        $items = [];
        foreach ($invoice['items'] as $item) {
            $items[] = [
                'taxes' => ['TVA'], // À adapter selon votre logique TVA
                'reference' => $item['item_reference'] ?? '',
                'description' => $item['item_name'],
                'quantity' => (float)$item['quantity'],
                'amount' => (float)$item['unit_price'],
                'discount' => 0, // À adapter si vous avez des remises
                'measurementUnit' => $item['unit'] ?? 'pcs'
            ];
        }

        // Données de base FNE
        $fne_data = [
            'invoiceType' => 'sale',
            'paymentMethod' => $this->mapPaymentMethod($invoice['method'] ?? 'cash'),
            'template' => $template,
            'isRne' => false,
            'clientCompanyName' => $invoice['customer_name'],
            'clientPhone' => $invoice['customer_phone'] ?? '',
            'clientEmail' => $invoice['customer_email'] ?? '',
            'pointOfSale' => 'Point de vente principal', // À configurer
            'establishment' => 'Établissement principal', // À configurer
            'commercialMessage' => 'Merci pour votre confiance',
            'footer' => 'Service client: contact@votreentreprise.ci',
            'foreignCurrency' => '',
            'foreignCurrencyRate' => 0,
            'items' => $items,
            'discount' => 0
        ];

        // Ajouter NCC client si B2B
        if ($template === 'B2B' && !empty($invoice['customer_ncc'])) {
            $fne_data['clientNcc'] = $invoice['customer_ncc'];
        }

        return $fne_data;
    }

    /**
     * Appeler l'API FNE
     */

    /**
     * Appeler l'API FNE avec gestion d'erreur améliorée
     */
    /**
     * Appeler l'API FNE avec gestion d'erreur améliorée
     */
    /**
     * Appeler l'API FNE
     */
    private function callFNEAPI($data)
    {
        $ch = curl_init();

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $this->fne_config['api_key']
        ];

        $json_data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Log pour débogage
        log_message('debug', 'FNE Request URL: ' . $this->fne_config['test_url']);
        log_message('debug', 'FNE Request Data: ' . $json_data);

        curl_setopt_array($ch, [
            CURLOPT_URL => $this->fne_config['test_url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->fne_config['timeout'],
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE => true
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        // Log de la réponse
        log_message('debug', 'FNE Response Code: ' . $http_code);
        log_message('debug', 'FNE Response Body: ' . $response);

        if ($error) {
            throw new Exception('Erreur de connexion FNE: ' . $error);
        }

        $result = json_decode($response, true);

        if ($http_code >= 200 && $http_code < 300) {
            return [
                'status' => 'success',
                'data' => $result,
                'http_code' => $http_code
            ];
        } else {
            $error_message = "Erreur FNE ({$http_code})";

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
                'http_code' => $http_code
            ];
        }
    }

    /**
     * Valider les données FNE avant envoi
     */
    /**
     * Valider les données avant envoi à l'API FNE
     */
    private function validateFNEData($data)
    {
        $errors = [];

        // Vérifier les champs requis selon la documentation
        $required_fields = [
            'invoiceType' => 'string',
            'paymentMethod' => 'string',
            'template' => 'string',
            'isRne' => 'boolean',
            'clientCompanyName' => 'string',
            'pointOfSale' => 'string',
            'establishment' => 'string',
            'items' => 'array'
        ];

        foreach ($required_fields as $field => $type) {
            if (!isset($data[$field])) {
                $errors[] = "Le champ '$field' est requis";
            } elseif ($type == 'array' && empty($data[$field])) {
                $errors[] = "Le champ '$field' ne peut pas être vide";
            } elseif ($type == 'string' && trim($data[$field]) === '') {
                $errors[] = "Le champ '$field' ne peut pas être vide";
            }
        }

        // Vérifier invoiceType
        if (isset($data['invoiceType']) && !in_array($data['invoiceType'], ['sale', 'purchase'])) {
            $errors[] = "invoiceType doit être 'sale' ou 'purchase'";
        }

        // Vérifier paymentMethod
        $valid_methods = ['cash', 'card', 'check', 'mobile-money', 'transfer', 'deferred'];
        if (isset($data['paymentMethod']) && !in_array($data['paymentMethod'], $valid_methods)) {
            $errors[] = "paymentMethod doit être: " . implode(', ', $valid_methods);
        }

        // Vérifier template
        $valid_templates = ['B2B', 'B2C', 'B2F', 'B2G'];
        if (isset($data['template']) && !in_array($data['template'], $valid_templates)) {
            $errors[] = "template doit être: " . implode(', ', $valid_templates);
        }

        // Vérifier les items
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $index => $item) {
                if (!isset($item['taxes']) || !is_array($item['taxes']) || empty($item['taxes'])) {
                    $errors[] = "Item $index: taxes est requis (tableau non vide)";
                }

                if (!isset($item['description']) || empty($item['description'])) {
                    $errors[] = "Item $index: description est requise";
                }

                if (!isset($item['quantity']) || $item['quantity'] <= 0) {
                    $errors[] = "Item $index: quantity doit être > 0";
                }

                if (!isset($item['amount']) || $item['amount'] <= 0) {
                    $errors[] = "Item $index: amount doit être > 0";
                }
            }
        }

        return $errors;
    }

    private function callFNEAPI_15($data)
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $this->fne_config['test_url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->fne_config['timeout'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $this->fne_config['api_key']
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception('Erreur de connexion FNE: ' . $error);
        }

        $result = json_decode($response, true);

        if ($http_code === 200 || $http_code === 201) {
            return [
                'status' => 'success',
                'data' => $result
            ];
        } else {
            $error_message = $result['message'] ?? 'Erreur inconnue FNE';
            return [
                'status' => 'fail',
                'message' => "FNE Error {$http_code}: {$error_message}",
                'details' => $result
            ];
        }
    }

    /**
     * Mapper les méthodes de paiement vers le format FNE
     */
    private function mapPaymentMethod($method)
    {
        $mapping = [
            'cash' => 'cash',
            'card' => 'card',
            'check' => 'check',
            'mobile-money' => 'mobile-money',
            'transfer' => 'transfer',
            'deferred' => 'deferred'
        ];

        return $mapping[$method] ?? 'cash';
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
            $invoice = $this->invoice_model->getInvoiceWithItems($invoice_id);
            if (!$invoice) {
                throw new Exception('Facture introuvable');
            }

            $response = [
                'status' => 'success',
                'data' => [
                    'certified' => !empty($invoice['fne_certified']),
                    'reference' => $invoice['fne_reference'] ?? '',
                    'token' => $invoice['fne_token'] ?? '',
                    'balance_sticker' => $invoice['fne_balance_sticker'] ?? 0,
                    'certified_at' => $invoice['fne_certified_at'] ?? ''
                ]
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
                $fne_data = $this->prepareFNEData($invoice);
                $fne_response = $this->callFNEAPI($fne_data);

                if ($fne_response['status'] === 'success') {
                    $update_data = [
                        'fne_certified' => 1,
                        'fne_reference' => $fne_response['data']['reference'],
                        'fne_token' => $fne_response['data']['token'],
                        'fne_balance_sticker' => $fne_response['data']['balance_sticker'],
                        'fne_certified_at' => date('Y-m-d H:i:s'),
                        'fne_response_data' => json_encode($fne_response['data'])
                    ];

                    $this->invoice_model->updateFNEStatus($invoice_id, $update_data);
                    log_message('info', "Certification FNE automatique réussie pour la facture #{$invoice_id}");
                }
            }
        } catch (Exception $e) {
            log_message('error', "Erreur certification FNE automatique: " . $e->getMessage());
        }
    }

    /* =========================================================
     * RELANCES AUTOMATIQUES
     * =======================================================*/

    /**
     * CRON: /index.php/invoiceitem/relance_factures (exécution quotidienne)
     * Niveaux de relance :
     *  - J-3 (rappel avant échéance)
     *  - J  (jour d'échéance)
     *  - J+7 et J+15 (en retard)
     */
    public function relance_factures()
    {
        // Optionnel: protéger pour exécution via CLI uniquement
        // if (!$this->input->is_cli_request()) show_error('CLI only', 403);

        // Récupère les factures non payées et calcule les deltas de jours
        $today = new DateTime('today');

        $invoices = $this->db
            ->select('id, invoice_number, customer_id, customer_email, customer_name, total_ttc, due_date, status')
            ->from('invoices')
            ->where('status', Invoice_model::STATUS_PENDING) // Non payée
            ->get()->result_array();

        foreach ($invoices as $inv) {
            if (empty($inv['due_date'])) continue;
            $due = DateTime::createFromFormat('Y-m-d', $inv['due_date']);
            if (!$due) continue;

            $diff = (int)$today->diff($due)->format('%r%a'); // négatif = en retard

            // Niveaux de relance
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

    /** Envoi d'une relance e‑mail formatée */
    private function envoyerRelance(array $facture, $niveau = 'J')
    {
        if (empty($facture['customer_email'])) {
            log_message('error', 'Relance: email manquant pour facture ID '.$facture['id']);
            return false;
        }

        $subject = 'Relance facture '.$facture['invoice_number'];

        $badge = [
            'J-3'    => 'Rappel avant échéance',
            'J'      => 'Échéance aujourd\'hui',
            'RETARD' => 'Relance — facture en retard',
        ];

        $message = '
            <div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#222">
                <p>Bonjour '.html_escape($facture['customer_name']).',</p>
                <p><strong>'.$badge[$niveau].'</strong></p>
                <p>Nous vous rappelons que votre facture <strong>'.html_escape($facture['invoice_number']).'</strong><br>
                d\'un montant de <strong>'.number_format((float)$facture['total_ttc'], 0, ',', ' ')." FCFA".'</strong>
                arrive à échéance le <strong>'.date('d/m/Y', strtotime($facture['due_date']))."</strong>.".'</p>
                <p>Merci de procéder au règlement dans les meilleurs délais.</p>
                <p>Cordialement,<br>Service Facturation</p>
            </div>';

        $this->email->clear(true);
        $this->email->from('no-reply@votredomaine.com', 'Service Facturation');
        $this->email->to($facture['customer_email']);
        $this->email->subject($subject);
        $this->email->message($message);

        if (!$this->email->send()) {
            log_message('error', 'Echec relance facture '.$facture['id'].' : '.$this->email->print_debugger());
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
            'title'       => 'Nouvelle facture',
            'title_list'  => 'Dernières factures',
            'itemcatlist' => $this->itemcategory_model->get(),
            'clients'     => $this->clients_model->get(),
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
                'customer_id'    => $this->input->post('customer'),
                'user_name'      => $this->input->post('user_name'),
                'invoice_number' => $this->invoice_model->generateInvoiceNumber(),
                'invoice_date'   => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('invoice_date')))),
                'due_date'       => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('due_date')))),
                'method'         => $this->input->post('method'),
                'apply_tva'      => $this->input->post('apply_tva') ? 1 : 0,
                'tva_rate'       => $this->input->post('tva_rate'),
                'notes'          => $this->input->post('notes'),
                'reference'      => $this->input->post('reference'),
                'status'         => Invoice_model::STATUS_PENDING,
                'created_at'     => date('Y-m-d H:i:s'),
                'items'          => [],
            ];

            $total_ht = 0;
            $categories = (array)$this->input->post('item_category_id');
            $items      = (array)$this->input->post('item_id');
            $quantities = (array)$this->input->post('quantity');
            $prices     = (array)$this->input->post('price');
            $units      = (array)$this->input->post('unit');

            foreach ($categories as $i => $category_id) {
                if (empty($items[$i]) || empty($quantities[$i]) || empty($prices[$i])) {
                    throw new Exception("Données d'article manquantes");
                }
                $qte = (float)$quantities[$i];
                $pu  = (float)$prices[$i];
                $lt  = $qte * $pu;
                $total_ht += $lt;

                $data['items'][] = [
                    'category_id' => $category_id,
                    'item_id'     => $items[$i],
                    'quantity'    => $qte,
                    'unit_price'  => $pu,
                    'unit'        => isset($units[$i]) ? $units[$i] : '',
                    'line_total'  => $lt,
                ];
            }

            $data['total_ht']         = $total_ht;
            $data['tva_amount']       = $data['apply_tva'] ? ($total_ht * $data['tva_rate'] / 100) : 0;
            $data['total_ttc']        = $total_ht + $data['tva_amount'];
            $data['remaining_amount'] = $data['total_ttc'];
            $data['amount_paid']      = 0;

            $insert_id = $this->invoice_model->add($data);

            $mouvement_data = [
                'type_mouvement' => 'depense',
                'montant'        => $total_ht + $data['tva_amount'],
                'description'    => $this->input->post('name') ?? 'Dépense caisse',
                'reference'      => 'EXP-' . $insert_id,
                'category'     => 'Vente de produit',
                'date_mouvement' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'mode_paiement'  => $this->input->post('mode_paiement') ?? 'cash',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ];
            $this->db->insert('mouvements', $mouvement_data);


            if (!$insert_id) {
                throw new Exception("Erreur lors de l'enregistrement");
            }

            $response['status'] = 'success';
            $response['message'] = 'La facture a été créée avec succès';
            $response['invoice_id'] = $insert_id;
        } catch (Exception $e) {
            $response['message'] = 'Erreur: '.$e->getMessage();
            log_message('error', 'Invoice Add Error: '.$e->getMessage());
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

        $data['payments']  = $this->invoice_model->getPayments($id);
        $data['title']     = 'Détails de la facture';
        $data['page_title'] = 'Facture '.$data['invoice']['invoice_number'];

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
                'title'       => 'Modifier la facture',
                'invoice'     => $invoice,
                'clients'     => $this->clients_model->get(),
                'itemcatlist' => $this->itemcategory_model->get(),
                'itemList'    => $this->item_model->get(),
            ];

            $this->load->view('layout/header', $data);
            $this->load->view('admin/invoice/edit', $data);
            $this->load->view('layout/footer');
        } catch (Exception $e) {
            log_message('error', 'Invoice Edit Error: '.$e->getMessage());
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
                'id'            => $id,
                'customer_id'   => $this->input->post('customer'),
                'user_name'     => $this->input->post('user_name'),
                'invoice_date'  => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('invoice_date')))),
                'due_date'      => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('due_date')))),
                'method'        => $this->input->post('method'),
                'apply_tva'     => $this->input->post('apply_tva') ? 1 : 0,
                'tva_rate'      => $this->input->post('tva_rate'),
                'notes'         => $this->input->post('notes'),
                'reference'     => $this->input->post('reference'),
                'items'         => [],
            ];

            $total_ht = 0;
            $categories = (array)$this->input->post('item_category_id');
            $items      = (array)$this->input->post('item_id');
            $quantities = (array)$this->input->post('quantity');
            $prices     = (array)$this->input->post('price');
            $units      = (array)$this->input->post('unit');

            foreach ($categories as $i => $category_id) {
                if (empty($items[$i]) || empty($quantities[$i]) || empty($prices[$i])) {
                    throw new Exception("Données d'article manquantes");
                }
                $qte = (float)$quantities[$i];
                $pu  = (float)$prices[$i];
                $lt  = $qte * $pu;
                $total_ht += $lt;

                $data['items'][] = [
                    'category_id' => $category_id,
                    'item_id'     => $items[$i],
                    'quantity'    => $qte,
                    'unit_price'  => $pu,
                    'unit'        => isset($units[$i]) ? $units[$i] : '',
                    'line_total'  => $lt,
                ];
            }

            $data['total_ht']         = $total_ht;
            $data['tva_amount']       = $data['apply_tva'] ? ($total_ht * $data['tva_rate'] / 100) : 0;
            $data['total_ttc']        = $total_ht + $data['tva_amount'];
            $data['remaining_amount'] = $data['total_ttc'];
            $data['amount_paid']      = 0;

            if (!$this->invoice_model->update($data)) {
                throw new Exception('Erreur lors de la mise à jour');
            }

            $response['status'] = 'success';
            $response['message'] = 'La facture a été mise à jour avec succès';
        } catch (Exception $e) {
            $response['message'] = 'Erreur: '.$e->getMessage();
            log_message('error', 'Invoice Update Error: '.$e->getMessage());
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
                'status'           => Invoice_model::STATUS_CANCELLED,
                'cancelled_at'     => date('Y-m-d H:i:s'),
                'cancelled_reason' => $reason,
            ];
            if (!$this->invoice_model->cancel($id, $data)) {
                throw new Exception("Erreur lors de l'annulation");
            }

            $response['status'] = 'success';
            $response['message'] = 'La facture a été annulée avec succès';
        } catch (Exception $e) {
            $response['message'] = 'Erreur: '.$e->getMessage();
            log_message('error', 'Invoice Cancel Error: '.$e->getMessage());
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

            $invoice_id   = $this->input->post('invoice_id');
            $amount       = (float)$this->input->post('amount');
            $payment_date = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('payment_date'))));
            $method       = $this->input->post('method');
            $reference    = $this->input->post('reference');
            $source_type  = $this->input->post('source_type'); // 'caisse' ou 'banque'
            $source_id    = $this->input->post('source_id');   // ID de la source
            $notes        = $this->input->post('notes');

            // Vérifier qu'une source est sélectionnée
            if (empty($source_id)) {
                throw new Exception('Veuillez sélectionner une source de paiement');
            }

            // Récupérer la facture
            $invoice = $this->invoice_model->getInvoiceWithItems($invoice_id);
            if (!$invoice) {
                throw new Exception('Facture introuvable');
            }

            // Vérifier que le montant ne dépasse pas le reste à payer
            if ($amount > $invoice['remaining_amount']) {
                throw new Exception('Montant supérieur au reste à payer (' . number_format($invoice['remaining_amount'], 0, ',', ' ') . ' FCFA)');
            }

            // Démarrer une transaction
            $this->db->trans_begin();

            // Générer une référence si non fournie
            if (empty($reference)) {
                $reference = 'PAY-' . date('YmdHis') . '-' . $invoice_id;
            }

            // --- 1. AJOUTER LE PAIEMENT ---
            $payment_data = [
                'invoice_id'   => $invoice_id,
                'amount'       => $amount,
                'payment_date' => $payment_date,
                'method'       => $method,
                'source_type'  => $source_type,
                'source_id'    => $source_id,
                'reference'    => $reference,
                'notes'        => $notes,
                'created_at'   => date('Y-m-d H:i:s')
            ];

            if (!$this->invoice_model->addPayment($payment_data)) {
                throw new Exception("Erreur lors de l'enregistrement du paiement");
            }

            // --- 2. METTRE À JOUR LA SOURCE (CAISSE OU BANQUE) ---
            if ($source_type == 'caisse') {
                // Récupérer la caisse
                $caisse = $this->db->where('id', $source_id)->get('income')->row();
                if (!$caisse) {
                    throw new Exception('Caisse introuvable');
                }

                $old_balance = (float)($caisse->amount_re ?? 0);
                $new_balance = $old_balance + $amount;

                // Mettre à jour le solde de la caisse
                $this->db->where('id', $source_id)
                    ->update('income', [
                        'amount_re' => $new_balance,
                        'last_operation_date' => date('Y-m-d H:i:s')
                    ]);

                // Enregistrer dans operation_caisse
                $operation_caisse_data = [
                    'reference'           => $reference,
                    'type_operation'       => 'entree',
                    'montant'              => $amount,
                    'designation'          => 'Paiement facture #' . $invoice['invoice_number'] . ' - Client: ' . ($invoice['customer_name'] ?? ''),
                    'caisse_id'            => $source_id,
                    'date'                 => $payment_date . ' ' . date('H:i:s'),
                    'entree'               => $amount,
                    'sortie'               => 0,
                    'note'                 => $notes,
                    'est_actif'            => 1,
                    'created_at'           => date('Y-m-d H:i:s'),
                    'updated_at'           => date('Y-m-d H:i:s'),
                    'category'             => 'Vente de produit',
                    'solde_avant_operation' => $old_balance,
                    'solde_apres_operation' => $new_balance
                ];

                $this->db->insert('operation_caisse', $operation_caisse_data);
                $operation_id = $this->db->insert_id();

                // Enregistrer dans mouvements
                $mouvement_data = [
                    'type_mouvement'        => 'entree',
                    'montant'               => $amount,
                    'description'           => 'Paiement facture #' . $invoice['invoice_number'],
                    'reference'             => $reference,
                    'date_mouvement'        => $payment_date,
                    'mode_paiement'         => $method,
                    'created_at'            => date('Y-m-d H:i:s'),
                    'operation_id'          => $operation_id,
                    'solde_avant_operation' => $old_balance,
                    'solde_apres_operation' => $new_balance
                ];
                $this->db->insert('mouvements', $mouvement_data);

            } else if ($source_type == 'banque') {
                // Récupérer la banque
                $banque = $this->db->where('id', $source_id)->get('banks')->row();
                if (!$banque) {
                    throw new Exception('Banque introuvable');
                }

                $old_balance = (float)($banque->balance ?? 0);
                $new_balance = $old_balance + $amount;

                // Mettre à jour le solde de la banque
                $this->db->where('id', $source_id)
                    ->update('banks', [
                        'balance' => $new_balance,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                // Enregistrer dans la table bank (opérations bancaires)
                $bank_operation_data = [
                    'bank_id'          => $source_id,
                    'date'             => $payment_date . ' ' . date('H:i:s'),
                    'transaction_type' => 'Virement entrant',
                    'designation'      => 'Crédit',
                    'name'             => 'Paiement facture #' . $invoice['invoice_number'],
                    'nom'              => $invoice['customer_name'] ?? 'Client',
                    'amount'           => $amount,
                    'reference'        => $reference,
                    'payment_mode'     => $method,
                    'note'             => $notes,
                    'created_at'       => date('Y-m-d H:i:s')
                ];
                $this->db->insert('bank', $bank_operation_data);
                $bank_operation_id = $this->db->insert_id();

                // Enregistrer dans mouvements
                $mouvement_data = [
                    'type_mouvement'        => 'entree',
                    'montant'               => $amount,
                    'description'           => 'Paiement facture #' . $invoice['invoice_number'],
                    'reference'             => $reference,
                    'date_mouvement'        => $payment_date,
                    'mode_paiement'         => $method,
                    'created_at'            => date('Y-m-d H:i:s'),
                    'bank_operation_id'     => $bank_operation_id,
                    'solde_avant_operation' => $old_balance,
                    'solde_apres_operation' => $new_balance
                ];
                $this->db->insert('mouvements', $mouvement_data);
            }

            // --- 3. ÉCRITURES COMPTABLES ---
            $entries_payment = [
                [
                    'date'        => $payment_date,
                    'invoice_id'  => $invoice_id,
                    'account'     => ($source_type == 'caisse') ? '511' : '512', // 511=Caisse, 512=Banque
                    'debit'       => $amount,
                    'credit'      => 0,
                    'description' => 'Paiement reçu facture #' . $invoice['invoice_number'],
                    'created_at'  => date('Y-m-d H:i:s'),
                ],
                [
                    'date'        => $payment_date,
                    'invoice_id'  => $invoice_id,
                    'account'     => '411', // Client
                    'debit'       => 0,
                    'credit'      => $amount,
                    'description' => 'Paiement client facture #' . $invoice['invoice_number'],
                    'created_at'  => date('Y-m-d H:i:s'),
                ]
            ];

            // Ajouter l'écriture de TVA si applicable
            if ($invoice['apply_tva'] && $invoice['tva_amount'] > 0) {
                $entries_payment[] = [
                    'date'        => $payment_date,
                    'invoice_id'  => $invoice_id,
                    'account'     => '4457', // TVA collectée
                    'debit'       => 0,
                    'credit'      => $invoice['tva_amount'] * ($amount / $invoice['total_ttc']), // TVA proportionnelle
                    'description' => 'TVA sur paiement facture #' . $invoice['invoice_number'],
                    'created_at'  => date('Y-m-d H:i:s'),
                ];
            }

            $this->db->insert_batch('accounting_entries', $entries_payment);

            // --- 4. VÉRIFIER SI LA FACTURE EST COMPLÈTEMENT PAYÉE ---
            $new_remaining = $invoice['remaining_amount'] - $amount;
            if ($new_remaining <= 0) {
                // Mettre à jour le statut de la facture
                $this->db->where('id', $invoice_id)
                    ->update('invoices', [
                        'status' => Invoice_model::STATUS_PAID,
                        'paid_at' => date('Y-m-d H:i:s')
                    ]);

                // Certification FNE automatique si paiement complet
                $this->certifyFNEAutomatic($invoice_id);
            } else {
                // Mettre à jour le statut en partiellement payé
                $this->db->where('id', $invoice_id)
                    ->update('invoices', [
                        'status' => Invoice_model::STATUS_PARTIAL
                    ]);
            }

            // Valider la transaction
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                throw new Exception('Erreur lors de la transaction');
            } else {
                $this->db->trans_commit();
            }

            $response['status']  = 'success';
            $response['message'] = '✅ Paiement enregistré avec succès !';
            $response['data'] = [
                'invoice_id' => $invoice_id,
                'remaining' => $new_remaining,
                'source_type' => $source_type,
                'new_balance' => ($source_type == 'caisse') ? $new_balance : $new_balance
            ];

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $response['message'] = '❌ Erreur: ' . $e->getMessage();
            log_message('error', 'Payment Add Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

  public function setPayment_20()
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

        $invoice_id   = $this->input->post('invoice_id');
        $amount       = (float)$this->input->post('amount');
        $payment_date = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('payment_date'))));
        $method       = $this->input->post('method');
        $reference    = $this->input->post('reference');
        $caisse_id    = $this->input->post('caisse_id'); // Nouveau champ
        $notes        = $this->input->post('notes');

        $invoice = $this->invoice_model->getInvoiceWithItems($invoice_id);
        if (!$invoice) throw new Exception('Facture introuvable');
        if ($amount > $invoice['remaining_amount']) {
            throw new Exception('Montant supérieur au reste à payer');
        }

        // --- Ajouter le paiement ---
        $payment_data = [
            'invoice_id'   => $invoice_id,
            'amount'       => $amount,
            'payment_date' => $payment_date,
            'method'       => $method,
            'source_id'    => $source_id,
            'reference'    => $reference,
            'notes'        => $notes,
        ];

        if (!$this->invoice_model->addPayment($payment_data)) {
            throw new Exception("Erreur lors de l'enregistrement du paiement");
        }

        // --- Certification FNE automatique si paiement complet ---
        $new_remaining = $invoice['remaining_amount'] - $amount;
        if ($new_remaining <= 0) {
            // La facture est maintenant entièrement payée, on la certifie FNE
            $this->certifyFNEAutomatic($invoice_id);
        }

        $entries_payment = [
            [
                'date'        => $payment_date,
                'invoice_id'  => $invoice_id,
                'account'     => '511', // Banque / caisse
                'debit'       => $amount,
                'credit'      => 0,
                'description' => 'Paiement reçu facture #'.$invoice['invoice_number'],
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'date'        => $payment_date,
                'invoice_id'  => $invoice_id,
                'account'     => '411', // Client
                'debit'       => 0,
                'credit'      => $amount,
                'description' => 'Paiement client facture #'.$invoice['invoice_number'],
                'created_at'  => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->insert_batch('accounting_entries', $entries_payment);

        $old_balance = $caisse->amount_re ?? 0;
        $new_balance = $old_balance + $amount;
        // 6. Enregistrer dans operation_caisse
        $operation_caisse_data = [
            'reference'                => $reference ?: 'FACT-' . $invoice['invoice_number'],
            'type_operation'           => 'ENTREE',
            'montant'                  => $amount,
            'designation'              => 'Paiement facture #' . $invoice['invoice_number'] . ' - Client: ' . ($invoice['customer_name'] ?? ''),
            'caisse_id'                => $invoice['caisse_id'],

            'date'                     => $payment_date . ' ' . date('H:i:s'),
            'entree'                   => $amount,
            'sortie'                   => 0,

            'note'                     => $notes,
            'est_actif'                => 1,
            'created_at'               => date('Y-m-d H:i:s'),
            'updated_at'               => date('Y-m-d H:i:s'),
            'category'                 => 'Vente de produit',
            'exp_head_id'              => '511', // Code compte caisse/banque
        ];

        $this->db->insert('operation_caisse', $operation_caisse_data);
        $operation_id = $this->db->insert_id();

        // 7. Mettre à jour le solde de la caisse
        // Mettre à jour le solde de la caisse
        $this->db->where('id', 'caisse_id');
        $this->db->set('amount_re', $new_balance);
        $this->db->set('last_operation_date', date('Y-m-d H:i:s'));
        $this->db->update('income');

        // 🔹 Enregistrement dans mouvements
        $mouvement_data = [
            'type_mouvement' => 'entree',
            'montant'        => $amount,
            'reference_piece' => $reference,
            'category'       => 'Vente de produit',
            'date_operation' => $payment_date,
            'created_at'     => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('journal_comptable', $mouvement_data);

        $response['status']  = 'success';
        $response['message'] = 'Le paiement a été enregistré avec succès (journal + comptabilité + TVA).';
        $response['data'] = [
            'invoice_id' => $invoice_id,
            'remaining' => $new_remaining
        ];

    } catch (Exception $e) {
        $response['message'] = 'Erreur: '.$e->getMessage();
        log_message('error', 'Payment Add Error: '.$e->getMessage());
    }

    echo json_encode($response);
}

    public function setPayment_170126()
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

            $invoice_id   = $this->input->post('invoice_id');
            $amount       = (float)$this->input->post('amount');
            $payment_date = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('payment_date'))));
            $method       = $this->input->post('method');
            $reference    = $this->input->post('reference');
            $notes        = $this->input->post('notes');

            $invoice = $this->invoice_model->getInvoiceWithItems($invoice_id);
            if (!$invoice) throw new Exception('Facture introuvable');
            if ($amount > $invoice['remaining_amount']) {
                throw new Exception('Montant supérieur au reste à payer');
            }

            // --- Ajouter le paiement ---
            $payment_data = [
                'invoice_id'   => $invoice_id,
                'amount'       => $amount,
                'payment_date' => $payment_date,
                'method'       => $method,
                'reference'    => $reference,
                'notes'        => $notes,
            ];

            if (!$this->invoice_model->addPayment($payment_data)) {
                throw new Exception("Erreur lors de l'enregistrement du paiement");
            }

            // --- Certification FNE automatique si paiement complet ---
            $new_remaining = $invoice['remaining_amount'] - $amount;
            if ($new_remaining <= 0) {
                // La facture est maintenant entièrement payée, on la certifie FNE
                $this->certifyFNEAutomatic($invoice_id);
            }

            $entries_payment = [
                [
                    'date'        => $payment_date,
                    'invoice_id'  => $invoice_id,
                    'account'     => '511', // Banque / caisse
                    'debit'       => $amount,
                    'credit'      => 0,
                    'description' => 'Paiement reçu facture #'.$invoice['invoice_number'],
                    'created_at'  => date('Y-m-d H:i:s'),
                ],
                [
                    'date'        => $payment_date,
                    'invoice_id'  => $invoice_id,
                    'account'     => '411', // Client
                    'debit'       => 0,
                    'credit'      => $amount,
                    'description' => 'Paiement client facture #'.$invoice['invoice_number'],
                    'created_at'  => date('Y-m-d H:i:s'),
                ],
            ];
            $this->db->insert_batch('accounting_entries', $entries_payment);

            // 🔹 Enregistrement dans mouvements
            $mouvement_data = [
                'type_mouvement' => 'entree',
                'montant'        => $amount,
                'reference_piece' => $reference,
                'category'       => 'Vente de produit',
                'date_operation' => $payment_date,
                'created_at'     => date('Y-m-d H:i:s'),
            ];
            $this->db->insert('journal_comptable', $mouvement_data);

            $response['status']  = 'success';
            $response['message'] = 'Le paiement a été enregistré avec succès (journal + comptabilité + TVA).';
            $response['data'] = [
                'invoice_id' => $invoice_id,
                'remaining' => $new_remaining
            ];

        } catch (Exception $e) {
            $response['message'] = 'Erreur: '.$e->getMessage();
            log_message('error', 'Payment Add Error: '.$e->getMessage());
        }

        echo json_encode($response);
    }

    public function showInvoiceReport($invoice_id = null)
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Invoiceitem/showInvoiceReport');

        // Charger le modèle
        $this->load->model('invoice_model');

        // Vérifier que l'ID de la facture est fourni
        if (!$invoice_id) {
            show_error("Aucune facture sélectionnée !");
            return;
        }

        // Récupérer la facture avec ses articles
        $invoice = $this->invoice_model->getInvoiceWithItems($invoice_id);
        if (!$invoice) {
            show_error("Facture introuvable !");
            return;
        }

        // Récupérer les écritures comptables liées à cette facture
        if (method_exists($this->invoice_model, 'getAccountingEntries')) {
            $entries = $this->invoice_model->getAccountingEntries($invoice_id);
        } else {
            $entries = [];
        }

        // Préparer les données pour la vue
        $data = [
            'invoice' => $invoice,
            'entries' => $entries,
            'sch_setting' => $this->sch_setting, // logo, paramètres école, etc.
        ];

        // Charger la vue
        $this->load->view('layout/header', $data);
        $this->load->view('admin/invoice/invoice_report', $data);
        $this->load->view('layout/footer');
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

    public function addPaymentForm()
    {
        $data['rowID']     = (!empty($this->input->post('rowID')) && (int)$this->input->post('rowID') > 0) ? (int)$this->input->post('rowID') : 0;
        $data['remaining'] = (!empty($this->input->post('remaining')) && $this->input->post('remaining') > 0) ? $this->input->post('remaining') : 0;

        $this->load->view('admin/invoice/paymentForm', $data);
    }

    /** Conversion nombre → lettres (FR) */
    public function asLetters($number)
    {
        $convert = explode('.', $number);
        $num[17] = ['zero','un','deux','trois','quatre','cinq','six','sept','huit','neuf','dix','onze','douze','treize','quatorze','quinze','seize'];
        $num[100] = [20=>'vingt',30=>'trente',40=>'quarante',50=>'cinquante',60=>'soixante',70=>'soixante-dix',80=>'quatre-vingt',90=>'quatre-vingt-dix'];
        if (isset($convert[1]) && $convert[1] != '') return self::asLetters($convert[0]).' et '.self::asLetters($convert[1]);
        if ($number < 0) return 'moins '.self::asLetters(-$number);
        if ($number < 17) return $num[17][$number];
        elseif ($number < 20) return 'dix-'.self::asLetters($number-10);
        elseif ($number < 100) {
            if ($number%10 == 0) return $num[100][$number];
            elseif (substr($number, -1) == 1) {
                if(((int)($number/10)*10)<70) return self::asLetters((int)($number/10)*10).'-et-un';
                elseif ($number == 71) return 'soixante-et-onze';
                elseif ($number == 81) return 'quatre-vingt-un';
                elseif ($number == 91) return 'quatre-vingt-onze';
            }
            elseif ($number < 70) return self::asLetters($number-$number%10).'-'.self::asLetters($number%10);
            elseif ($number < 80) return self::asLetters(60).'-'.self::asLetters($number%20);
            else return self::asLetters(80).'-'.self::asLetters($number%20);
        }
        elseif ($number == 100) return 'cent';
        elseif ($number < 200) return self::asLetters(100).' '.self::asLetters($number%100);
        elseif ($number < 1000) return self::asLetters((int)($number/100)).' '.self::asLetters(100).($number%100 > 0 ? ' '.self::asLetters($number%100): '');
        elseif ($number == 1000) return 'mille';
        elseif ($number < 2000) return self::asLetters(1000).' '.self::asLetters($number%1000).' ';
        elseif ($number < 1000000) return self::asLetters((int)($number/1000)).' '.self::asLetters(1000).($number%1000 > 0 ? ' '.self::asLetters($number%1000): '');
        elseif ($number == 1000000) return 'millions';
        elseif ($number < 2000000) return self::asLetters(1000000).' '.self::asLetters($number%1000000);
        elseif ($number < 1000000000) return self::asLetters((int)($number/1000000)).' '.self::asLetters(1000000).($number%1000000 > 0 ? ' '.self::asLetters($number%1000000): '');
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
            // Récupération de la facture
            $data['invoice'] = $this->invoice_model->getInvoiceWithItems($invoice_id);
            if (!$data['invoice']) {
                throw new Exception('Facture introuvable');
            }

            // Vérification email client
            if (empty($data['invoice']['customer_email'])) {
                throw new Exception("Le client n'a pas d'adresse email");
            }

            // Récupération société
            $company = $this->setting_model->get();
            $data['company'] = $company[0];

            // Total en lettres
            $data['totalAsletter'] = $this->asLetters((float)$data['invoice']['total_ttc']);

            // 🔹 User connecté
            $data['user'] = $this->customlib->getUserData();

            if ($data['invoice']) {
                $payload = [
                    'id'               => $data['invoice']['id'],
                    'data'             => $data,
                    'credential_for'   => 'sendInvoice',
                    'client_name'      => $data['invoice']['customer_name'].' '.$data['invoice']['customer_last_name'],
                    'quotation_number' => $data['invoice']['invoice_number'],
                    'quotation_date'   => !empty($data['invoice']['invoice_date'])
                        ? date('d/m/Y', strtotime($data['invoice']['invoice_date']))
                        : 'N/A',
                    'email'            => $data['invoice']['customer_email'],

                    // 🔹 Ajout des infos utilisateur pour Reply-To
                    'user_name'        => $data['user']['username'],
                    'user_email'       => $data['user']['email'],
                ];

                // Envoi
                $this->mailsmsconf->mailsms('send_invoice', $payload);
            }

            $response['status'] = 'success';
            $response['message'] = 'La facture a été envoyée avec succès';
        } catch (Exception $e) {
            $response['message'] = 'Erreur: '.$e->getMessage();
            log_message('error', 'Invoice Email Error: '.$e->getMessage());
        }

        echo json_encode($response);
    }

    public function validateInvoice()
    {
        if (!$this->input->is_ajax_request()) {
            show_error('Accès non autorisé', 403);
        }

        $invoiceId = $this->input->post('id');
        if (!$invoiceId) {
            echo json_encode(['status' => 'error', 'message' => 'ID manquant']);
            return;
        }

        $this->load->model('invoice_model');
        $invoice = $this->invoice_model->get($invoiceId);

        if (!$invoice) {
            echo json_encode(['status' => 'error', 'message' => 'Facture introuvable']);
            return;
        }

        if ($invoice->status == 2) { // déjà validée
            echo json_encode(['status' => 'error', 'message' => 'Facture déjà validée']);
            return;
        }

        // ⚡ Mettre à jour le statut
        $this->invoice_model->update($invoiceId, ['status' => 2]);

        // ⚡ Générer les écritures comptables
        $this->load->model('accounting_model');
        $this->accounting_model->createInvoiceEntries($invoice);

        echo json_encode(['status' => 'success']);
    }

    public function createInvoiceEntries($invoice)
    {
        // Exemple simple avec TVA
        $totalHT  = $invoice->total_ht;
        $tva      = $invoice->tva_amount;
        $totalTTC = $invoice->total_ttc;

        // Débit Client (411)
        $this->db->insert('accounting_entries', [
            'invoice_id' => $invoice->id,
            'account'    => '411',
            'type'       => 'debit',
            'amount'     => $totalTTC,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Crédit Ventes (701)
        $this->db->insert('accounting_entries', [
            'invoice_id' => $invoice->id,
            'account'    => '701',
            'type'       => 'credit',
            'amount'     => $totalHT,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Crédit TVA collectée (4457)
        if ($tva > 0) {
            $this->db->insert('accounting_entries', [
                'invoice_id' => $invoice->id,
                'account'    => '4457',
                'type'       => 'credit',
                'amount'     => $tva,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /* =========================================================
 * RELANCES AUTOMATIQUES
 * =======================================================*/

    /**
     * CRON: /index.php/invoiceitem/process_reminders (exécution quotidienne)
     * Niveaux de relance :
     *  - J-3 (rappel avant échéance)
     *  - J  (jour d'échéance)
     *  - J+3, J+7, J+15 (en retard)
     */
    public function process_reminders()
    {
        // Protéger pour exécution via CLI uniquement (optionnel)
        // if (!$this->input->is_cli_request()) show_error('Accès non autorisé', 403);

        $log = [];
        $today = new DateTime('today');

        // Récupérer les factures non payées avec les infos client
        $invoices = $this->db
            ->select('
            invoices.id, 
            invoices.invoice_number, 
            invoices.customer_id, 
            invoices.total_ttc, 
            invoices.due_date, 
            invoices.status,
            invoices.remaining_amount,
            clients.item_supplier as customer_name,
            clients.lastname as customer_last_name,
            clients.email as customer_email,
            clients.phone as customer_phone
        ')
            ->from('invoices')
            ->join('clients', 'clients.id = invoices.customer_id')
            ->where_in('invoices.status', [self::STATUS_PENDING, self::STATUS_PARTIAL, self::STATUS_OVERDUE])
            ->where('clients.email IS NOT NULL')
            ->where('clients.email !=', '')
            ->get()->result_array();

        foreach ($invoices as $invoice) {
            if (empty($invoice['due_date'])) continue;

            $due = DateTime::createFromFormat('Y-m-d', $invoice['due_date']);
            if (!$due) continue;

            $diff = (int)$today->diff($due)->format('%r%a'); // négatif = en retard

            // Niveaux de relance
            $reminder_sent = false;

            if ($diff === 3) {
                $reminder_sent = $this->send_reminder($invoice, 'J-3');
            } elseif ($diff === 0) {
                $reminder_sent = $this->send_reminder($invoice, 'J');
            } elseif ($diff === -3) {
                $reminder_sent = $this->send_reminder($invoice, 'J+3');
            } elseif ($diff === -7) {
                $reminder_sent = $this->send_reminder($invoice, 'J+7');
            } elseif ($diff === -15) {
                $reminder_sent = $this->send_reminder($invoice, 'J+15');
            }

            if ($reminder_sent) {
                // Enregistrer dans l'historique des relances
                $this->log_reminder($invoice['id'], $diff);
                $log[] = "Relance envoyée pour facture #{$invoice['invoice_number']} (J=" . $diff . ")";
            }
        }

        // Si appelé via AJAX ou CLI
        if ($this->input->is_ajax_request()) {
            echo json_encode(['status' => 'success', 'log' => $log]);
        } else {
            echo "Traitement des relances terminé.\n";
            echo implode("\n", $log);
        }
    }

    /**
     * Envoi d'une relance par email
     */
    private function send_reminder($invoice, $level = 'J')
    {
        if (empty($invoice['customer_email'])) {
            log_message('error', 'Relance: email manquant pour facture #' . $invoice['invoice_number']);
            return false;
        }

        // Récupérer les informations de l'entreprise
        $company = $this->setting_model->get();
        $company_name = $company[0]['name'] ?? 'Notre entreprise';
        $company_email = $company[0]['email'] ?? 'no-reply@' . $_SERVER['HTTP_HOST'];

        // Configurer les niveaux de relance
        $reminder_levels = [
            'J-3' => [
                'subject' => 'Rappel : Votre facture arrive à échéance dans 3 jours',
                'title' => 'Rappel avant échéance',
                'message' => 'Nous vous rappelons que votre facture arrive à échéance dans 3 jours.',
                'urgency' => 'info'
            ],
            'J' => [
                'subject' => 'Échéance aujourd\'hui : Votre facture est à payer',
                'title' => 'Échéance aujourd\'hui',
                'message' => 'Votre facture arrive à échéance aujourd\'hui.',
                'urgency' => 'warning'
            ],
            'J+3' => [
                'subject' => 'Relance : Facture échue depuis 3 jours',
                'title' => 'Première relance',
                'message' => 'Votre facture est échue depuis 3 jours.',
                'urgency' => 'danger'
            ],
            'J+7' => [
                'subject' => 'Relance importante : Facture échue depuis 7 jours',
                'title' => 'Deuxième relance',
                'message' => 'Votre facture est échue depuis 7 jours. Nous vous remercions de procéder au règlement dans les plus brefs délais.',
                'urgency' => 'danger'
            ],
            'J+15' => [
                'subject' => 'Dernière relance avant procédure : Facture échue depuis 15 jours',
                'title' => 'Dernière relance',
                'message' => 'Votre facture est échue depuis 15 jours. À défaut de règlement sous 8 jours, nous nous verrons contraints de prendre des mesures de recouvrement.',
                'urgency' => 'danger'
            ]
        ];

        $level_info = $reminder_levels[$level] ?? $reminder_levels['J'];

        // Calculer le nombre de jours de retard
        $due_date = new DateTime($invoice['due_date']);
        $today = new DateTime();
        $days_late = $due_date < $today ? $today->diff($due_date)->days : 0;

        // Formatage du montant
        $amount_formatted = number_format((float)$invoice['total_ttc'], 0, ',', ' ') . ' FCFA';
        $remaining_formatted = number_format((float)$invoice['remaining_amount'], 0, ',', ' ') . ' FCFA';

        // Construire le message HTML
        $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.5; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: ' . ($level == 'J-3' ? '#17a2b8' : ($level == 'J' ? '#ffc107' : '#dc3545')) . '; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background-color: #f8f9fa; padding: 30px; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 5px 5px; }
            .invoice-details { background-color: white; padding: 20px; margin: 20px 0; border-radius: 5px; border-left: 4px solid ' . ($level == 'J-3' ? '#17a2b8' : ($level == 'J' ? '#ffc107' : '#dc3545')) . '; }
            .amount { font-size: 24px; font-weight: bold; color: ' . ($level == 'J-3' ? '#17a2b8' : ($level == 'J' ? '#ffc107' : '#dc3545')) . '; }
            .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6; font-size: 12px; color: #6c757d; text-align: center; }
            .button { display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
            .button:hover { background-color: #0056b3; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>' . html_escape($level_info['title']) . '</h2>
            </div>
            <div class="content">
                <p>Bonjour <strong>' . html_escape(trim($invoice['customer_name'] . ' ' . $invoice['customer_last_name'])) . '</strong>,</p>
                
                <p>' . html_escape($level_info['message']) . '</p>
                
                <div class="invoice-details">
                    <h3 style="margin-top: 0;">Détails de la facture</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0;"><strong>N° Facture :</strong></td>
                            <td style="padding: 8px 0;">' . html_escape($invoice['invoice_number']) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0;"><strong>Date d\'échéance :</strong></td>
                            <td style="padding: 8px 0;">' . date('d/m/Y', strtotime($invoice['due_date'])) . '</td>
                        </tr>
                        ' . ($days_late > 0 ? '
                        <tr>
                            <td style="padding: 8px 0;"><strong>Retard :</strong></td>
                            <td style="padding: 8px 0;">' . $days_late . ' jour(s)</td>
                        </tr>' : '') . '
                        <tr>
                            <td style="padding: 8px 0;"><strong>Montant total :</strong></td>
                            <td style="padding: 8px 0;" class="amount">' . $amount_formatted . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0;"><strong>Reste à payer :</strong></td>
                            <td style="padding: 8px 0;" class="amount">' . $remaining_formatted . '</td>
                        </tr>
                    </table>
                    
                    <div style="text-align: center;">
                        <a href="' . base_url('admin/invoiceitem/view/' . $invoice['id']) . '" class="button">Voir ma facture</a>
                    </div>
                </div>
                
                <h4>Moyens de paiement</h4>
                <ul>
                    <li><strong>Virement bancaire :</strong> IBAN: ' . ($company[0]['iban'] ?? 'FR76 XXXX XXXX XXXX XXXX XXXX XXX') . '</li>
                    <li><strong>Mobile Money :</strong> ' . ($company[0]['phone'] ?? '') . '</li>
                    <li><strong>Paiement en ligne :</strong> ' . base_url('payment/invoice/' . $invoice['id']) . '</li>
                </ul>
                
                <p>Pour toute question concernant cette facture, n\'hésitez pas à nous contacter :</p>
                <ul>
                    <li><strong>Email :</strong> ' . $company_email . '</li>
                    <li><strong>Téléphone :</strong> ' . ($company[0]['phone'] ?? '') . '</li>
                </ul>
                
                <p>Nous vous remercions pour votre confiance et votre fidélité.</p>
                
                <p>Cordialement,<br>
                <strong>Service Facturation</strong><br>
                ' . $company_name . '</p>
            </div>
            <div class="footer">
                <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
                <p>' . $company_name . ' - ' . ($company[0]['address'] ?? '') . '</p>
            </div>
        </div>
    </body>
    </html>';

        // Version texte pour les clients sans HTML
        $text_message = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $message));

        $this->email->clear(true);
        $this->email->from($company_email, $company_name);
        $this->email->to($invoice['customer_email']);
        $this->email->subject($level_info['subject']);
        $this->email->message($message);
        $this->email->set_alt_message($text_message);

        if (!$this->email->send()) {
            log_message('error', 'Échec relance facture #' . $invoice['invoice_number'] . ' : ' . $this->email->print_debugger());
            return false;
        }

        return true;
    }

    /**
     * Enregistrer l'historique des relances
     */
    private function log_reminder($invoice_id, $days_delta)
    {
        $level = '';
        if ($days_delta == 3) $level = 'before_3days';
        elseif ($days_delta == 0) $level = 'due_date';
        elseif ($days_delta == -3) $level = 'late_3days';
        elseif ($days_delta == -7) $level = 'late_7days';
        elseif ($days_delta == -15) $level = 'late_15days';
        else $level = 'other';

        $data = [
            'invoice_id' => $invoice_id,
            'reminder_level' => $level,
            'days_delta' => $days_delta,
            'sent_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Créer la table si elle n'existe pas
        if (!$this->db->table_exists('invoice_reminders')) {
            $this->load->dbforge();
            $this->dbforge->add_field([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'invoice_id' => ['type' => 'INT', 'constraint' => 11],
                'reminder_level' => ['type' => 'VARCHAR', 'constraint' => 50],
                'days_delta' => ['type' => 'INT', 'constraint' => 11],
                'sent_at' => ['type' => 'DATETIME'],
                'created_at' => ['type' => 'DATETIME']
            ]);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('invoice_reminders', TRUE);
        }

        $this->db->insert('invoice_reminders', $data);
    }

    /**
     * Configurer le rappel automatique depuis l'interface
     */
    public function configure_reminders()
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_edit')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Invoiceitem/configure_reminders');

        if ($this->input->post()) {
            $settings = [
                'reminder_before_days' => $this->input->post('reminder_before_days'),
                'reminder_on_due_date' => $this->input->post('reminder_on_due_date') ? 1 : 0,
                'reminder_after_days_1' => $this->input->post('reminder_after_days_1'),
                'reminder_after_days_2' => $this->input->post('reminder_after_days_2'),
                'reminder_after_days_3' => $this->input->post('reminder_after_days_3'),
                'reminder_enabled' => $this->input->post('reminder_enabled') ? 1 : 0,
                'reminder_sender_email' => $this->input->post('reminder_sender_email'),
                'reminder_sender_name' => $this->input->post('reminder_sender_name')
            ];

            // Sauvegarder les paramètres
            foreach ($settings as $key => $value) {
                $this->setting_model->set($key, $value);
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success">Paramètres de relance enregistrés</div>');
            redirect('admin/invoiceitem/configure_reminders');
        }

        $data['title'] = 'Configuration des relances';
        $data['settings'] = $this->setting_model->get();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/invoice/reminder_config', $data);
        $this->load->view('layout/footer');
    }

    /**
     * Afficher l'historique des relances
     */
    public function reminder_history($invoice_id = null)
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_view')) {
            access_denied();
        }

        $this->db->select('
        invoice_reminders.*,
        invoices.invoice_number,
        clients.item_supplier as customer_name,
        clients.lastname as customer_last_name
    ')
            ->from('invoice_reminders')
            ->join('invoices', 'invoices.id = invoice_reminders.invoice_id')
            ->join('clients', 'clients.id = invoices.customer_id');

        if ($invoice_id) {
            $this->db->where('invoice_reminders.invoice_id', $invoice_id);
        }

        $this->db->order_by('invoice_reminders.sent_at', 'DESC');
        $reminders = $this->db->get()->result_array();

        if ($this->input->is_ajax_request()) {
            echo json_encode(['data' => $reminders]);
        } else {
            $data['reminders'] = $reminders;
            $data['title'] = 'Historique des relances';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/invoice/reminder_history', $data);
            $this->load->view('layout/footer');
        }
    }

    /**
     * Test d'envoi de relance (pour débogage)
     */
    public function test_reminder($invoice_id)
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_edit')) {
            access_denied();
        }

        $invoice = $this->db
            ->select('
            invoices.*,
            clients.item_supplier as customer_name,
            clients.lastname as customer_last_name,
            clients.email as customer_email
        ')
            ->from('invoices')
            ->join('clients', 'clients.id = invoices.customer_id')
            ->where('invoices.id', $invoice_id)
            ->get()
            ->row_array();

        if ($this->send_reminder($invoice, 'J+7')) {
            echo "Relance de test envoyée avec succès à " . $invoice['customer_email'];
        } else {
            echo "Échec de l'envoi de la relance de test";
        }
    }

    /**
     * Préparer les données pour l'API FNE selon le format exact requis
     */
    /**
     * Préparer les données pour l'API FNE selon le format EXACT requis
     * Basé sur le message d'erreur :
     * - paymentMethod: card, check, cash, mobile-money, transfer, deferred
     * - invoiceType: sale, purchase
     * - template: B2B, B2F, B2G, B2C
     * - items: must contain exactly one tax (TVA, TVAB, TVAC, TVAD, TVAE)
     */
    /**
     * Préparer les données pour l'API FNE selon la documentation officielle
     *
     * @param array $invoice Les données de la facture
     * @return array Données formatées pour l'API
     */
    private function prepareFNEData($invoice)
    {
        // Récupérer les informations de l'entreprise
        $company = $this->setting_model->get();
        $company = $company[0];

        // Déterminer le template selon le type de client
        // B2B: client avec NCC, B2C: particulier, B2F: international, B2G: gouvernement
        $template = 'B2C'; // Par défaut

        if (!empty($invoice['customer_ncc'])) {
            $template = 'B2B'; // Entreprise avec NCC
        } elseif (!empty($invoice['customer_country']) && $invoice['customer_country'] != 'CI') {
            $template = 'B2F'; // Client international
        }

        // Déterminer le type de facture
        $invoiceType = 'sale'; // vente

        // Mapper la méthode de paiement selon les valeurs exactes
        $paymentMethod = $this->mapPaymentMethodFNE($invoice['method'] ?? 'cash');

        // Déterminer la taxe à appliquer selon le taux
        $taxCode = $this->getTaxCode($invoice);

        // Préparer les articles avec le format exact
        $items = [];
        foreach ($invoice['items'] as $item) {
            $items[] = [
                'taxes' => [$taxCode], // Doit être un tableau avec une seule valeur
                'reference' => $item['item_reference'] ?? 'REF-' . str_pad($item['item_id'], 5, '0', STR_PAD_LEFT),
                'description' => $item['item_name'],
                'quantity' => (float)$item['quantity'],
                'amount' => (float)$item['unit_price'], // Prix unitaire HT
                'discount' => 0, // Remise en pourcentage ou montant? À vérifier
                'measurementUnit' => $this->getMeasurementUnitFNE($item['unit'] ?? 'pcs')
            ];
        }

        // Nom du client (obligatoire)
        $clientCompanyName = trim($invoice['customer_name'] . ' ' . ($invoice['customer_last_name'] ?? ''));
        if (empty($clientCompanyName)) {
            $clientCompanyName = 'Client';
        }

        // Point de vente et établissement (doivent être des chaînes simples)
        $pointOfSale = !empty($company['point_of_sale']) ? $company['point_of_sale'] : 'PDV001';
        $establishment = !empty($company['establishment']) ? $company['establishment'] : 'ETABLISSEMENT001';

        // Construire le payload exact selon la documentation
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
            'discount' => 0 // Remise globale
        ];

        // Ajouter NCC client si B2B
        if ($template === 'B2B' && !empty($invoice['customer_ncc'])) {
            $fne_data['clientNcc'] = $invoice['customer_ncc'];
        }

        return $fne_data;
    }

    /**
     * Déterminer le code TVA selon le taux et la configuration
     *
     * TVA: 18% (normal)
     * TVAB: 9% (réduit)
     * TVAC: 0% (exonération conventionnelle)
     * TVAD: 0% (exonération légale pour TEE/RME)
     * TVAE: 0% (exonération)
     */
    private function getTaxCode($invoice)
    {
        if (!$invoice['apply_tva'] || $invoice['tva_rate'] <= 0) {
            return 'TVAE'; // Exonéré
        }

        $tva_rate = (float)$invoice['tva_rate'];

        if ($tva_rate == 18) {
            return 'TVA';
        } elseif ($tva_rate == 9) {
            return 'TVAB';
        } elseif ($tva_rate == 0) {
            // Vérifier le type d'exonération
            if (!empty($invoice['exoneration_type'])) {
                if ($invoice['exoneration_type'] == 'conventionnelle') {
                    return 'TVAC';
                } elseif ($invoice['exoneration_type'] == 'legale') {
                    return 'TVAD';
                }
            }
            return 'TVAE';
        }

        return 'TVA'; // Par défaut
    }

    /**
     * Mapper les méthodes de paiement au format FNE
     */
    /**
     * Mapper les méthodes de paiement au format EXACT de l'API FNE
     * Les valeurs acceptées sont : card, check, cash, mobile-money, transfer, deferred
     */
    /**
     * Mapper les méthodes de paiement selon les valeurs exactes de l'API
     *
     * Valeurs possibles:
     * - cash: espèce
     * - card: carte bancaire
     * - check: chèque
     * - mobile-money: mobile money
     * - transfer: virement bancaire
     * - deferred: à terme
     */
    private function mapPaymentMethodFNE($method)
    {
        $method = strtolower(trim($method));

        $mapping = [
            'cash' => 'cash',
            'espèce' => 'cash',
            'espèces' => 'cash',
            'espece' => 'cash',
            'especes' => 'cash',
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
            'orange money' => 'mobile-money',
            'mtn money' => 'mobile-money',
            'wave' => 'mobile-money',
            'transfer' => 'transfer',
            'virement' => 'transfer',
            'deferred' => 'deferred',
            'terme' => 'deferred',
            'crédit' => 'deferred',
            'credit' => 'deferred'
        ];

        return $mapping[$method] ?? 'cash';
    }

    /**
     * Obtenir l'unité de mesure au format FNE
     */
    private function getMeasurementUnitFNE($unit)
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
            'service' => 'service'
        ];

        return $units[$unit] ?? 'pcs';
    }

    /**
     * Convertir un nombre en lettres
     */
    private function convertToWords($number)
    {
        $f = new NumberFormatter('fr', NumberFormatter::SPELLOUT);
        return $f->format($number);
    }

}