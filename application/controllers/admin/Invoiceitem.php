<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Invoiceitem extends Admin_Controller {
   
    public function __construct() {
        parent::__construct();
        $this->load->helper('form');

        $this->config->load("app-config");
        $this->load->library('Enc_lib');
        $this->load->library('mailsmsconf');
        $this->load->library('encoding_lib');
        $this->load->library('customlib');

        $this->load->model('invoice_model');
        $this->load->model('clients_model');
        $this->load->model('itemcategory_model');
        $this->load->model('item_model');
    }

    /**
     * Affiche la liste des factures
     */
    public function index() {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Invoiceitem', 'can_view')) {
            access_denied();
        }

        // Définition des menus actifs
        $this->session->set_userdata('top_menu', 'Caisse');
        $this->session->set_userdata('sub_menu', 'Invoiceitem/index');
        
        // Initialisation des données de la page
        $data = [
            'title' => 'Liste des factures',
            'title_list' => 'Dernières factures'
        ];

        // Chargement des vues
        $this->load->view('layout/header', $data);
        $this->load->view('admin/invoice/list', $data);
        $this->load->view('layout/footer');
    }

    /**
     * Récupère les données des factures au format JSON
     */
    public function data() {
        
        echo $this->invoice_model->getListData();
    }

    
    /**
     * INVOICE FORM
     */
    public function form() {
        // Définition des menus actifs
        $this->session->set_userdata('top_menu', 'Caisse');
        $this->session->set_userdata('sub_menu', 'Invoiceitem/index');
        
        // Préparation des données pour la vue
        $data = [
            'title' => 'Nouvelle facture',
            'title_list' => 'Dernières factures',
            'itemcatlist' => $this->itemcategory_model->get(),
            'clients' => $this->clients_model->get()
        ];

        // Chargement des vues
        $this->load->view('layout/header', $data);
        $this->load->view('admin/invoice/form', $data);
        $this->load->view('layout/footer', $data);
    }



    /**
     * Ajoute une nouvelle facture
     */
    public function add() {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Invoiceitem', 'can_add')) {
            access_denied();
        }

        $response = ['status' => 'fail', 'message' => '', 'error' => []];

        // var_dump($this->input->post());
        // die();

        try {
            // Validation des champs obligatoires
            $this->form_validation->set_rules('customer', 'Client', 'required|trim');
            $this->form_validation->set_rules('invoice_date', 'Date de facture', 'required');
            $this->form_validation->set_rules('due_date', 'Date d\'échéance', 'required');
            $this->form_validation->set_rules('item_category_id[]', 'Catégorie', 'required');
            $this->form_validation->set_rules('item_id[]', 'Article', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantité', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('price[]', 'Prix unitaire', 'required|numeric|greater_than[0]');

            if ($this->form_validation->run() == false) {
                $response['error'] = $this->form_validation->error_array();
                echo json_encode($response);
                return;
            }

            // Récupération et validation des données
            $data = [
                'customer_id'   => $this->input->post('customer'),
                'invoice_number'=> $this->invoice_model->generateInvoiceNumber(),
                'invoice_date'  => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('invoice_date')))),
                'due_date'      => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('due_date')))),
                'payment_method'=> $this->input->post('payment_method'),
                'apply_tva'     => $this->input->post('apply_tva') ? 1 : 0,
                'tva_rate'      => $this->input->post('tva_rate'),
                'notes'         => $this->input->post('notes'),
                'status'        => Invoice_model::STATUS_PENDING, // 0 = Non payée
                'created_at'    => date('Y-m-d H:i:s'),
                'items'         => []
            ];

            // Calcul des totaux
            $total_ht   = 0;
            $categories = $this->input->post('item_category_id');
            $items      = $this->input->post('item_id');
            $quantities = $this->input->post('quantity');
            $prices     = $this->input->post('price');
            $units      = $this->input->post('unit');

            if (!is_array($categories) || !is_array($items) || !is_array($quantities) || !is_array($prices)) {
                throw new Exception('Format de données invalide');
            }

            // Construction du tableau d'articles
            foreach ($categories as $index => $category_id) {
                if (empty($items[$index]) || empty($quantities[$index]) || empty($prices[$index])) {
                    throw new Exception('Données d\'article manquantes');
                }

                $quantity   = floatval($quantities[$index]);
                $price      = floatval($prices[$index]);
                $line_total = $quantity * $price;
                $total_ht   += $line_total;

                $data['items'][] = [
                    'category_id'   => $category_id,
                    'item_id'       => $items[$index],
                    'quantity'      => $quantity,
                    'unit_price'    => $price,
                    'unit'          => $units[$index] ?? '',
                    'line_total'    => $line_total
                ];
            }

            // Calcul des montants TVA et TTC
            $data['total_ht']           = $total_ht;
            $data['tva_amount']         = $data['apply_tva'] ? ($total_ht * $data['tva_rate'] / 100) : 0;
            $data['total_ttc']          = $total_ht + $data['tva_amount'];
            $data['remaining_amount']   = $data['total_ttc'];
            $data['amount_paid']        = 0;

            // var_dump($data);
            // die();
            // Enregistrement des données
            $insert_id = $this->invoice_model->add($data);
            
            if (!$insert_id) {
                throw new Exception('Erreur lors de l\'enregistrement');
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

    /**
     * Affiche les détails d'une facture
     * 
     * @param int $id ID de la facture
     */
    public function view($id) {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Invoiceitem', 'can_view')) {
            access_denied();
        }

        // Récupération des données de la facture
        $data['invoice'] = $this->invoice_model->getInvoiceWithItems($id);
        
        if (!$data['invoice']) {
            $this->session->set_flashdata('error', 'Facture non trouvée');
            redirect('admin/invoiceitem');
        }

        // Récupération des paiements
        $data['payments'] = $this->invoice_model->getPayments($id);

        // var_dump($id);
        // var_dump($data['payments']);
        // die();

        // Préparation des données pour la vue
        $data['title'] = 'Détails de la facture';
        $data['page_title'] = 'Facture ' . $data['invoice']['invoice_number'];

        // Chargement des vues
        $this->load->view('layout/header', $data);
        $this->load->view('admin/invoice/view', $data);
        $this->load->view('layout/footer');
    }

    /**
     * Affiche le formulaire d'édition d'une facture
     * 
     * @param int $id ID de la facture
     */
    public function edit($id) {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Invoiceitem', 'can_edit')) {
            access_denied();
        }

        try {
            // Vérifier le statut de la facture
            $invoice = $this->invoice_model->getInvoiceWithItems($id);
            if (!$invoice) {
                $this->session->set_flashdata('error', 'Facture introuvable');
                redirect('admin/invoiceitem');
            }

            if ($this->invoice_model->isPaid($id)) {
                $this->session->set_flashdata('error', 'Cette facture ne peut plus être modifiée car elle est déjà payée');
                redirect('admin/invoiceitem');
            }

            // Préparation des données pour la vue
            $data = [
                'title'         => 'Modifier la facture',
                'invoice'       => $invoice,
                'clients'       => $this->clients_model->get(),
                'itemcatlist'   => $this->itemcategory_model->get(),
                'itemList'      => $this->item_model->get()
            ];

            // Chargement des vues
            $this->load->view('layout/header', $data);
            $this->load->view('admin/invoice/edit', $data);
            $this->load->view('layout/footer');

        } catch (Exception $e) {
            log_message('error', 'Invoice Edit Error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Une erreur est survenue lors de l\'édition de la facture');
            redirect('admin/invoice');
        }
    }

    /**
     * Met à jour une facture existante
     */
    public function update() {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Invoiceitem', 'can_edit')) {
            access_denied();
        }

        $response = ['status' => 'fail', 'message' => '', 'error' => []];

        try {
            // Récupération de l'ID
            $id = $this->input->post('id');
            
            // Vérifier le statut de la facture
            if ($this->invoice_model->isPaid($id)) {
                throw new Exception('Cette facture ne peut plus être modifiée car elle est déjà payée');
            }

            // Validation des champs obligatoires
            $this->form_validation->set_rules('customer', 'Client', 'required|trim');
            $this->form_validation->set_rules('invoice_date', 'Date de facture', 'required');
            $this->form_validation->set_rules('due_date', 'Date d\'échéance', 'required');
            $this->form_validation->set_rules('item_category_id[]', 'Catégorie', 'required');
            $this->form_validation->set_rules('item_id[]', 'Article', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantité', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('price[]', 'Prix unitaire', 'required|numeric|greater_than[0]');

            if ($this->form_validation->run() == false) {
                $response['error'] = $this->form_validation->error_array();
                echo json_encode($response);
                return;
            }

            // Récupération et validation des données
            $data = [
                'id' => $id,
                'customer_id' => $this->input->post('customer'),
                'invoice_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('invoice_date')))),
                'due_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('due_date')))),
                'payment_method' => $this->input->post('payment_method'),
                'apply_tva' => $this->input->post('apply_tva') ? 1 : 0,
                'tva_rate' => $this->input->post('tva_rate'),
                'notes' => $this->input->post('notes'),
                'items' => []
            ];

            // Calcul des totaux
            $total_ht = 0;
            $categories = $this->input->post('item_category_id');
            $items = $this->input->post('item_id');
            $quantities = $this->input->post('quantity');
            $prices = $this->input->post('price');
            $units = $this->input->post('unit');

            if (!is_array($categories) || !is_array($items) || !is_array($quantities) || !is_array($prices)) {
                throw new Exception('Format de données invalide');
            }

            // Construction du tableau d'articles
            foreach ($categories as $index => $category_id) {
                if (empty($items[$index]) || empty($quantities[$index]) || empty($prices[$index])) {
                    throw new Exception('Données d\'article manquantes');
                }

                $quantity = floatval($quantities[$index]);
                $price = floatval($prices[$index]);
                $line_total = $quantity * $price;
                $total_ht += $line_total;

                $data['items'][] = [
                    'category_id' => $category_id,
                    'item_id' => $items[$index],
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'unit' => $units[$index] ?? '',
                    'line_total' => $line_total
                ];
            }

            // Calcul des montants TVA et TTC
            $data['total_ht'] = $total_ht;
            $data['tva_amount'] = $data['apply_tva'] ? ($total_ht * $data['tva_rate'] / 100) : 0;
            $data['total_ttc'] = $total_ht + $data['tva_amount'];
            $data['remaining_amount']   = $data['total_ttc'];
            $data['amount_paid']        = 0;

            // Mise à jour des données
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

    /**
     * Annule une facture
     */
    public function cancel() {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Invoiceitem', 'can_cancel')) {
            access_denied();
        }

        $response = ['status' => 'fail', 'message' => ''];

        try {
            $id = $this->input->post('id');

            // var_dump($id);
            // die();

            // Vérifier le statut de la facture
            if ($this->invoice_model->isPaid($id)) {
                throw new Exception('Cette facture ne peut pas être annulée car elle est déjà payée');
            }

            // Récupération du motif d'annulation
            $reason = $this->input->post('reason');
            if (empty($reason)) {
                throw new Exception("Le motif d'annulation est requis");
            }


            // Rejet du devis
            $data = [
                'status'            => Invoice_model::STATUS_CANCELLED,
                'cancelled_at'      => date('Y-m-d H:i:s'),
                'cancelled_reason'  => $reason  
            ];

            // var_dump($data);
            // exit;

            if (!$this->invoice_model->cancel($id, $data)) {
                throw new Exception('Erreur lors de l\'annulation');
            }

            $response['status'] = 'success';
            $response['message'] = 'La facture a été annulée avec succès';

        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Invoice Cancel Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    /**
     * Ajoute un paiement à une facture
     */
    public function setPayment() {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Invoiceitem', 'can_edit')) {
            access_denied();
        }

        // var_dump($this->input->post());
        // die();

        $response = ['status' => 'fail', 'message' => ''];

        try {
            // Validation des champs obligatoires
            $this->form_validation->set_rules('invoice_id', 'Facture', 'required|numeric');
            $this->form_validation->set_rules('amount', 'Montant', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('payment_date', 'Date de paiement', 'required');
            $this->form_validation->set_rules('method', 'Méthode de paiement', 'required');

            if ($this->form_validation->run() == false) {
                $response['error'] = $this->form_validation->error_array();
                echo json_encode($response);
                return;
            }

            // Récupération des données
            $data = [
                'invoice_id'    => $this->input->post('invoice_id'),
                'amount'        => $this->input->post('amount'),
                'payment_date'  => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('payment_date')))),
                'method'        => $this->input->post('method'),
                'reference'     => $this->input->post('reference'),
                'notes'         => $this->input->post('notes'),
            ];

            // var_dump($data);
            // die();

            // Vérification du montant
            $invoice = $this->invoice_model->getInvoiceWithItems($data['invoice_id']);
            if (!$invoice) {
                throw new Exception('Facture introuvable');
            }

            if ($data['amount'] > $invoice['remaining_amount']) {
                throw new Exception('Le montant du paiement ne peut pas être supérieur au montant restant à payer');
            }

            // Ajout du paiement
            if (!$this->invoice_model->addPayment($data)) {
                throw new Exception('Erreur lors de l\'enregistrement du paiement');
            }

            $response['status'] = 'success';
            $response['message'] = 'Le paiement a été enregistré avec succès';

        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Payment Add Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    /**
     * Affiche la page d'impression d'une facture
     * 
     * @param int $id ID de la factures
     */
    public function print() {

        $id = $this->input->post('id');

        // var_dump($this->input->post());
        // var_dump($id);
        // die();

        // Récupération des données de la facture
        $data['invoice'] = $this->invoice_model->getInvoiceWithItems($id);
        
        if (!$data['invoice']) {
            show_404();
            return;
        }

        // var_dump($data);
        // die();   

        // Récupération des paiements
        $data['payments'] = $this->invoice_model->getPayments($id);

        // Récupération des données de la société
        $company = $this->setting_model->get();

        // Récupération des données de l'entrepris
        $data['company'] = $company[0];
        $data['totalAsletter'] = $this->asLetters(floatval($data['invoice']['total_ttc']));
        


        // var_dump($data['totalAsletter']);
        // die();

        // Chargement de la vue d'impression
        // $this->load->view('admin/invoice/print', $data);
        $invoice_page = $this->load->view('admin/invoice/print', $data, true); 
        $array = array('status' => '1', 'error' => '', 'page' => $invoice_page);
        echo json_encode($array);
    }


    public function addPaymentForm()
    {
        // Try to get any row's id sent
        $data['rowID'] = ( ! empty($this->input->post('rowID')) && (int)$this->input->post('rowID') > 0) ? (int)$this->input->post('rowID') : 0;
        $data['remaining'] = ( ! empty($this->input->post('remaining')) && $this->input->post('remaining') > 0) ? $this->input->post('remaining') : 0;

        // var_dump($data['remaining']);
        // exit;

        // Load the form view with all the data required
        $this->load->view('admin/invoice/paymentForm', $data);
    }


    //------------------------------------
    // AJOUT D'UN NOUVEL ORDRE DE VIREMENT
    //------------------------------------
    public function asLetters($number) {

        $convert = explode('.', $number);
        $num[17] = array('zero', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit',
                         'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize');
                          
        $num[100] = array(20 => 'vingt', 30 => 'trente', 40 => 'quarante', 50 => 'cinquante',
                          60 => 'soixante', 70 => 'soixante-dix', 80 => 'quatre-vingt', 90 => 'quatre-vingt-dix');
                                          
        if (isset($convert[1]) && $convert[1] != '') {
          return self::asLetters($convert[0]).' et '.self::asLetters($convert[1]);
        }
        if ($number < 0) return 'moins '.self::asLetters(-$number);
        if ($number < 17) {
          return $num[17][$number];
        }
        elseif ($number < 20) {
          return 'dix-'.self::asLetters($number-10);
        }
        elseif ($number < 100) {
          if ($number%10 == 0) {
            return $num[100][$number];
          }
          elseif (substr($number, -1) == 1) {
            if( ((int)($number/10)*10)<70 ){
              return self::asLetters((int)($number/10)*10).'-et-un';
            }
            elseif ($number == 71) {
              return 'soixante-et-onze';
            }
            elseif ($number == 81) {
              return 'quatre-vingt-un';
            }
            elseif ($number == 91) {
              return 'quatre-vingt-onze';
            }
          }
          elseif ($number < 70) {
            return self::asLetters($number-$number%10).'-'.self::asLetters($number%10);
          }
          elseif ($number < 80) {
            return self::asLetters(60).'-'.self::asLetters($number%20);
          }
          else {
            return self::asLetters(80).'-'.self::asLetters($number%20);
          }
        }
        elseif ($number == 100) {
          return 'cent';
        }
        elseif ($number < 200) {
          return self::asLetters(100).' '.self::asLetters($number%100);
        }
        elseif ($number < 1000) {
          return self::asLetters((int)($number/100)).' '.self::asLetters(100).($number%100 > 0 ? ' '.self::asLetters($number%100): '');
        }
        elseif ($number == 1000){
          return 'mille';
        }
        elseif ($number < 2000) {
          return self::asLetters(1000).' '.self::asLetters($number%1000).' ';
        }
        elseif ($number < 1000000) {
          return self::asLetters((int)($number/1000)).' '.self::asLetters(1000).($number%1000 > 0 ? ' '.self::asLetters($number%1000): '');
        }
        elseif ($number == 1000000) {
          return 'millions';
        }
        elseif ($number < 2000000) {
          return self::asLetters(1000000).' '.self::asLetters($number%1000000);
        }
        elseif ($number < 1000000000) {
          return self::asLetters((int)($number/1000000)).' '.self::asLetters(1000000).($number%1000000 > 0 ? ' '.self::asLetters($number%1000000): '');
        }
    }
    // -----------------------------------


    /**
     * Envoie la facture par email au client
     * 
     * @param int $invoice_id ID du bon de livraison
     * @return void
     */
    public function sendEmail()
    {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Invoiceitem', 'can_edit')) {
            access_denied();
        }

        // var_dump($data);
        // exit;

        $invoice_id = $this->input->post('id', 0);

        // var_dump($invoice_id);
        // exit;

        // Initialisation de la réponse
        $response = ['status' => 'fail', 'message' => ''];

        try {
            // Récupération des données du bon de livraison
            $data['invoice'] = $this->invoice_model->getInvoiceWithItems($invoice_id);
            if (!$data['invoice']) {
                throw new Exception('Facture introuvable');
            }

            // Vérification de l'email du client
            if (empty($data['invoice']['customer_email'])) {
                throw new Exception('Le client n\'a pas d\'adresse email');
            }

            // Récupération des données de la société
            $company = $this->setting_model->get();

            // Récupération des données de l'entrepris
            $data['company'] = $company[0];
            $data['totalAsletter'] = $this->asLetters(floatval($data['invoice']['total_ttc']));

            // Récupération des informations de l'utilisateur connecté
            $data['user'] = $this->customlib->getUserData();

            // var_dump($data);
            // exit;


            //===================
            if ($data['invoice']) {

                $invoice_detail = array(
                    'id'            => $data['invoice']['id'], 
                    'data'          => $data, 
                    'credential_for'=> 'sendInvoice', 
                    'client_name'       => $data['invoice']['customer_name'].' '.$data['invoice']['customer_last_name'], 
                    'quotation_number'  => $data['invoice']['invoice_number'], 
                    'quotation_date'    => !empty($data['invoice']['invoice_date']) ? date('d/m/Y', strtotime($data['invoice']['invoice_date'])) :"N/A", 
                    'email'             => $data['invoice']['customer_email']);

                $this->mailsmsconf->mailsms('send_invoice', $invoice_detail);
            }
            
            $response['status'] = 'success';
            $response['message'] = 'La facture a été envoyée avec succès';

        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Invoice Email Error: ' . $e->getMessage());
        }

        // Retourner la réponse en JSON
        echo json_encode($response);
    }


}