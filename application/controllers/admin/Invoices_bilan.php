<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Contrôleur de gestion des factures (CodeIgniter 3)
 * — Liste / CRUD / Paiements
 * — Relances automatiques par e‑mail (avant échéance, jour J, après échéance)
 */
class Invoiceitem extends Admin_Controller
{
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
        $this->session->set_userdata('sub_menu', 'Invoices_bilan/index');

        $data = [
            'title' => 'Liste des factures',
            'title_list' => 'Dernières factures',
        ];

        $this->load->view('layout/header', $data);
        $this->load->view('admin/invoice/invoice_report', $data);
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

            $data = [
                'invoice_id'   => $this->input->post('invoice_id'),
                'amount'       => $this->input->post('amount'),
                'payment_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('payment_date')))),
                'method'       => $this->input->post('method'),
                'reference'    => $this->input->post('reference'),
                'notes'        => $this->input->post('notes'),
            ];

            $invoice = $this->invoice_model->getInvoiceWithItems($data['invoice_id']);
            if (!$invoice) throw new Exception('Facture introuvable');
            if ($data['amount'] > $invoice['remaining_amount']) {
                throw new Exception('Montant supérieur au reste à payer');
            }

            if (!$this->invoice_model->addPayment($data)) {
                throw new Exception("Erreur lors de l'enregistrement du paiement");
            }

            // Écriture comptable (journal)
            $j = [
                'date_operation'   => date('d-m-y', $this->customlib->datetostrtotime($this->input->post('payment_date'))),
                'reference_piece'  => 'RECU-'.$data['invoice_id'],
                'libelle_operation'=> 'Ventes de marchandises',
                'compte_id_revenu' => 701,
                'compte_id'        => 701,
                'montant_revenu'   => $this->input->post('amount'),
                'montant'          => $this->input->post('amount'),
            ];
            $this->journal_model->save($j);

            $response['status']  = 'success';
            $response['message'] = 'Le paiement a été enregistré avec succès';
        } catch (Exception $e) {
            $response['message'] = 'Erreur: '.$e->getMessage();
            log_message('error', 'Payment Add Error: '.$e->getMessage());
        }

        echo json_encode($response);
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

    public function sendEmail_old050925()
    {
        if (!$this->rbac->hasPrivilege('facture', 'can_edit')) {
            access_denied();
        }

        $invoice_id = $this->input->post('id', 0);
        $response = ['status' => 'fail', 'message' => ''];

        try {
            $data['invoice'] = $this->invoice_model->getInvoiceWithItems($invoice_id);
            if (!$data['invoice']) throw new Exception('Facture introuvable');
            if (empty($data['invoice']['customer_email'])) throw new Exception("Le client n'a pas d'adresse email");

            $company = $this->setting_model->get();
            $data['company'] = $company[0];
            $data['totalAsletter'] = $this->asLetters((float)$data['invoice']['total_ttc']);
            $data['user'] = $this->customlib->getUserData();

            if ($data['invoice']) {
                $payload = [
                    'id'               => $data['invoice']['id'],
                    'data'             => $data,
                    'credential_for'   => 'sendInvoice',
                    'client_name'      => $data['invoice']['customer_name'].' '.$data['invoice']['customer_last_name'],
                    'quotation_number' => $data['invoice']['invoice_number'],
                    'quotation_date'   => !empty($data['invoice']['invoice_date']) ? date('d/m/Y', strtotime($data['invoice']['invoice_date'])) : 'N/A',
                    'email'            => $data['invoice']['customer_email'],
                ];
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
}
