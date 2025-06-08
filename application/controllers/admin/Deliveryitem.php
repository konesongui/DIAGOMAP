<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Deliveryitem extends Admin_Controller {

    /**
     * Constructeur - Charge les helpers et modèles nécessaires
     */
    function __construct() {
        parent::__construct();
        $this->load->helper('form');

        $this->config->load("app-config");
        $this->load->library('Enc_lib');
        $this->load->library('mailsmsconf');
        $this->load->library('encoding_lib');
        $this->load->library('customlib');

        $this->load->model('delivery_model');
        $this->load->model('itemcategory_model');
        $this->load->model('item_model');
        $this->load->model('clients_model');
        $this->load->model('stock_model');
    }

    /**
     * Méthode principale - Gère la liste des bons de livraison
     */
    function index() {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('delivery', 'can_view')) {
            access_denied();
        }

        // Définition des menus actifs
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Deliveryitem/index');
        
        // Initialisation des données de la page
        $data = [
            'title' => 'Ajouter un bon de livraison',
            'title_list' => 'Derniers bons de livraison',
        ];

        // Chargement des vues
        $this->load->view('layout/header', $data);
        $this->load->view('admin/itemdelivery/list', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Récupère les articles par catégorie
     * 
     * @return JSON $data
     */
    public function getItemByCategory()
    {   
        $item_category_id = $this->input->get('item_category_id');
        $data = $this->stock_model->getItemByCategory($item_category_id);
        echo json_encode($data);
    }

    /**
     * Récupère les données des bons de livraison
     * au format JSON
     * 
     * @return JSON $response
     */
    public function data()
    {   
        // Récupère les données du modèle
        $result = $this->delivery_model->getListData();
        
        // Les données sont déjà au format JSON, on les renvoie directement
        echo $result;
    }

    /**
     * Formulaire d'ajout de bon de livraison
     */
    public function form() {
        // Définition des menus actifs
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Deliveryitem/index');
        
        // Préparation des données pour la vue
        $data = [
            'title' => 'Ajouter un bon de livraison',
            'title_list' => 'Derniers bons de livraison',
            'itemcatlist' => $this->itemcategory_model->get(),
            'clients' => $this->clients_model->get()
        ];

        // Chargement des vues
        $this->load->view('layout/header', $data);
        $this->load->view('admin/itemdelivery/form', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Ajoute un nouveau bon de livraison
     */
    public function add() {
        // Initialisation de la réponse
        $response = ['status' => 'fail', 'message' => '', 'error' => []];

        // var_dump($this->input->post());
        // die();

        try {
            // Vérification des données POST
            if (!$this->input->post()) {
                throw new Exception('Aucune donnée reçue');
            }



            // Validation des champs obligatoires
            $this->form_validation->set_rules('designation', 'Désignation', 'required|trim');
            $this->form_validation->set_rules('customer', 'Client', 'required|trim');
            $this->form_validation->set_rules('delivery_date', 'Date de livraison', 'required');
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
                'designation' => $this->input->post('designation'),
                'customer_id' => $this->input->post('customer'),
                'delivery_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('delivery_date')))),
                'deadline' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('valid_until')))),
                'payment_term' => $this->input->post('payment_term'),
                'delivery_term' => $this->input->post('delivery_term'),
                'delivery_location' => $this->input->post('delivery_location'),
                'notes' => $this->input->post('notes'),
                'status' => 1, // 1 = En préparation
                'created_at' => date('Y-m-d H:i:s'),
                'items' => []
            ];

            // Validation des articles
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

                $data['items'][] = [
                    'category_id' => $category_id,
                    'item_id' => $items[$index],
                    'quantity' => $quantity,
                    'delivered_quantity' => 0,
                    'price' => $price,
                    'unit' => $units[$index] ?? '',
                    'line_total' => $line_total
                ];
            }

            // Enregistrement des données
            $insert_id = $this->delivery_model->add($data);
            
            if (!$insert_id) {
                throw new Exception('Erreur lors de l\'enregistrement');
            }

            $response['status'] = 'success';
            $response['message'] = 'Le bon de livraison a été enregistré avec succès';
            $response['delivery_id'] = $insert_id;

        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Delivery Add Error: ' . $e->getMessage());
        }

        // Retourner la réponse en JSON
        echo json_encode($response);
    }

    /**
     * Affiche les détails d'un bon de livraison
     * 
     * @param int $id ID du bon de livraison
     * @return void
     */
    public function view($id)
    {   
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Deliveryitem', 'can_view')) {
            access_denied();
        }

        // var_dump($id);
        // exit;

        // Récupération des données du bon de livraison
        $data['delivery'] = $this->delivery_model->getDeliveryWithItems($id);
        // var_dump($data['delivery']);
        // exit;
        // Vérification si le bon de livraison existe
        if (!$data['delivery']) {
            $this->session->set_flashdata('error', 'Bon de livraison non trouvé');
            redirect('admin/itemdelivery');
        }

        // Préparation des données pour la vue
        $data['title'] = 'Détails du bon de livraison';
        $data['page_title'] = 'Détails du bon de livraison ' . $data['delivery']['delivery_number'];

        // var_dump($data);
        // exit;
        // Chargement des vues
        $this->load->view('layout/header', $data);
        $this->load->view('admin/itemdelivery/view', $data);
        $this->load->view('layout/footer');
    }

    /**
     * Affiche le formulaire d'édition d'un bon de livraison
     * 
     * @param int $id ID du bon de livraison
     * @return void
     */
    public function edit($id)
    {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Deliveryitem', 'can_edit')) {
            access_denied();
        }

        try {
            // Vérifier le statut du bon de livraison
            $delivery = $this->delivery_model->getDeliveryWithItems($id);
            if (!$delivery) {
                $this->session->set_flashdata('error', 'Bon de livraison introuvable');
                redirect('admin/deliveryitem');
            }

            if ((int)$delivery['status'] !== 1) {
                $this->session->set_flashdata('error', 'Ce bon de livraison ne peut plus être modifié');
                redirect('admin/deliveryitem');
            }

            // Préparation des données pour la vue
            $data = [
                'title' => 'Modifier le bon de livraison',
                'delivery' => $delivery,
                'itemcatlist' => $this->itemcategory_model->get(),
                'itemList' => $this->item_model->get(),
                'clients' => $this->clients_model->get()
            ];

            // Chargement des vues
            $this->load->view('layout/header', $data);
            $this->load->view('admin/delivery/edit', $data);
            $this->load->view('layout/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Delivery Edit Error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Une erreur est survenue lors de l\'édition du bon de livraison');
            redirect('admin/deliveryitem');
        }
    }

    /**
     * Met à jour un bon de livraison existant
     */
    public function update() {
        if (!$this->rbac->hasPrivilege('Deliveryitem', 'can_edit')) {
            access_denied();
        }

        // Récupérer l'ID du bon de livraison
        $id = $this->input->post('id');
        
        // Initialisation de la réponse
        $response = ['status' => 'fail', 'message' => '', 'error' => []];

        try {
            // Vérifier le statut du bon de livraison
            $delivery = $this->delivery_model->getDeliveryWithItems($id);
            if (!$delivery) {
                throw new Exception('Bon de livraison introuvable');
            }

            if ((int)$delivery['status'] !== 1) {
                throw new Exception('Ce bon de livraison ne peut plus être modifié');
            }

            // Validation des champs obligatoires
            $this->form_validation->set_rules('designation', 'Désignation', 'required|trim');
            $this->form_validation->set_rules('customer', 'Client', 'required|trim');
            $this->form_validation->set_rules('delivery_date', 'Date de livraison', 'required');
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
                'id' => $this->input->post('id'),
                'designation' => $this->input->post('designation'),
                'customer_id' => $this->input->post('customer'),
                'delivery_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('delivery_date')))),
                'shipping_method' => $this->input->post('shipping_method'),
                'tracking_number' => $this->input->post('tracking_number'),
                'delivery_address' => $this->input->post('delivery_address'),
                'notes' => $this->input->post('notes'),
                'items' => []
            ];

            // Validation des articles
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

                $data['items'][] = [
                    'category_id' => $category_id,
                    'item_id' => $items[$index],
                    'quantity' => $quantity,
                    'delivered_quantity' => 0,
                    'price' => $price,
                    'unit' => $units[$index] ?? '',
                    'line_total' => $line_total
                ];
            }

            // Mise à jour des données
            $update_success = $this->delivery_model->update($data);
            
            if (!$update_success) {
                throw new Exception('Erreur lors de la mise à jour');
            }

            $response['status'] = 'success';
            $response['message'] = 'Le bon de livraison a été mis à jour avec succès';

        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Delivery Update Error: ' . $e->getMessage());
        }

        // Retourner la réponse en JSON
        echo json_encode($response);
    }

    /**
     * Crée une facture à partir d'un bon de livraison complété
     * 
     * @param int $delivery_id ID du bon de livraison
     * @return bool
     */
    private function createInvoiceFromDelivery($delivery_id) {
        // Récupération des données de la livraison
        $delivery = $this->delivery_model->getDeliveryWithItems($delivery_id);
        if (!$delivery) {
            return false;
        }

        // Génération du numéro de facture
        $invoice_number = $this->generateInvoiceNumber();

        // Préparation des données de la facture
        $invoice_data = [
            'customer_id' => $delivery['customer_id'],
            'invoice_number' => $invoice_number,
            'delivery_id' => $delivery_id,
            'invoice_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'apply_tva' => 1,
            'tva_rate' => 20.00,
            'total_ht' => $delivery['total_ht'],
            'total_ttc' => $delivery['total_ttc'],
            'remaining_amount' => $delivery['total_ttc'],
            'amount_paid' => 0.00,
            'status' => 1, // 0 = Non payée
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $this->session->userdata('admin_id')
        ];

        // Début de la transaction
        $this->db->trans_start();

        try {
            // Insertion de la facture
            $this->db->insert('invoices', $invoice_data);
            $invoice_id = $this->db->insert_id();

            if (!$invoice_id) {
                throw new Exception('Erreur lors de la création de la facture');
            }

            // Insertion des articles de la facture
            foreach ($delivery['items'] as $item) {
                $invoice_item_data = [
                    'invoice_id' => $invoice_id,
                    'category_id' => $item['category_id'],
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                    'position' => $item['position']
                ];

                if (!$this->db->insert('invoice_items', $invoice_item_data)) {
                    throw new Exception('Erreur lors de l\'ajout d\'un article à la facture');
                }
            }

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Invoice Creation Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Génère un numéro unique pour une facture
     * Format: FAC-YYYYMM-XXXX où XXXX est un numéro séquentiel
     * 
     * @return string
     */
    private function generateInvoiceNumber() {
        $prefix = 'FAC';  // FAC pour Facture
        $date = date('Ym');  // Format YYYYMM
        
        // Recherche le dernier numéro pour ce mois
        $this->db->like('invoice_number', $prefix . '-' . $date, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('invoices');
        
        if ($query->num_rows() > 0) {
            // Extrait le numéro séquentiel de la dernière facture
            $last_ref = $query->row()->invoice_number;
            $sequence = intval(substr($last_ref, -4)) + 1;
        } else {
            // Première facture du mois
            $sequence = 1;
        }
        
        // Formate le numéro séquentiel sur 4 chiffres
        $sequence_padded = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        
        return $prefix . '-' . $date . '-' . $sequence_padded;
    }

    /**
     * Valide un bon de livraison
     */
    public function completeDelivery() {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Deliveryitem', 'can_edit')) {
            access_denied();
        }

        $id = $this->input->post('id');

        $response = ['status' => 'fail', 'message' => ''];

        try {
            // Vérification que le bon de livraison existe
            $delivery = $this->delivery_model->getDeliveryWithItems($id);

            if (!$delivery) {
                throw new Exception('Bon de livraison non trouvé');
            }

            // Vérification que le bon de livraison n'est pas déjà complété ou annulé
            if ($this->delivery_model->isDeliveryCompleted($id)) {
                throw new Exception('Ce bon de livraison est déjà complété');
            }
            if ($this->delivery_model->isDeliveryCancelled($id)) {
                throw new Exception('Ce bon de livraison a été annulé');
            }

            // Préparation des données de complétion
            $data = [
                'status' => Delivery_model::STATUS_DELIVERED,
                'delivery_at' => date('Y-m-d H:i:s')
            ];

            // Mise à jour du statut et des quantités livrées
            if (!$this->delivery_model->completeDelivery($id, $data)) {
                throw new Exception('Erreur lors de la complétion du bon de livraison');
            }

            // Création de la facture
            if (!$this->createInvoiceFromDelivery($id)) {
                throw new Exception('Erreur lors de la création de la facture');
            }

            $response['status'] = 'success';
            $response['message'] = 'Bon de livraison complété et facture créée avec succès';

        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            log_message('error', 'Delivery Completion Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    /**
     * Rejette un bon de livraison
     */
    public function cancelDelivery() {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Deliveryitem', 'can_edit')) {
            access_denied();
        }

        // var_dump($this->input->post());
        // die();

        $id = $this->input->post('id');

        $response = ['status' => 'fail', 'message' => ''];

        try {
            // Vérification que le bon de livraison existe
            $delivery = $this->delivery_model->getDeliveryWithItems($id);
            if (!$delivery) {
                throw new Exception('Bon de livraison non trouvé');
            }
            
            // Récupération du motif de rejet
            $reason = $this->input->post('reason');
            if (empty($reason)) {
                throw new Exception('Le motif de rejet est requis');
            }

            // Rejet du bon de livraison
            $data = [
                'status'            => Delivery_model::STATUS_CANCELLED,
                'cancelled_at'      => date('Y-m-d H:i:s'),
                'cancelled_reason'  => $reason
            ];

            // var_dump($data);
            // die();

            if (!$this->delivery_model->cancelDelivery($id, $data)) {
                throw new Exception('Erreur lors du rejet du bon de livraison');
            }

            $response['status'] = 'success';
            $response['message'] = 'Bon de livraison rejeté avec succès';

        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            log_message('error', 'Delivery Rejection Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }



    /**
     * Affiche la page d'impression d'un bon de livraison
     * 
     * @param int $id ID de la factures
     */
    public function print() {

        $id = $this->input->post('id');

        // var_dump($this->input->post());
        // var_dump($id);
        // die();

        // Récupération des données de la facture
        $data['delivery'] = $this->delivery_model->getDeliveryWithItems($id);
        
        if (!$data['delivery']) {
            show_404();
            return;
        }

        // var_dump($data);
        // die();   

        // Récupération des données de la société
        $company = $this->setting_model->get();

        // Récupération des données de l'entrepris
        $data['company'] = $company[0];
        $data['totalAsletter'] = $this->asLetters(floatval($data['delivery']['total_ttc']));
        


        // var_dump($data['totalAsletter']);
        // die();

        // Chargement de la vue d'impression
        // $this->load->view('admin/invoice/print', $data);
        $invoice_page = $this->load->view('admin/itemdelivery/print', $data, true); 
        $array = array('status' => '1', 'error' => '', 'page' => $invoice_page);
        echo json_encode($array);
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


    public function partialDelivery($id) {

        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Deliveryitem', 'can_edit')) {
            access_denied();
        }
        
        // var_dump($id);
        // die();

        try {
            // Vérifier le statut du bon de livraison
            $delivery = $this->delivery_model->getDeliveryWithItems($id);
            // var_dump($delivery);
            // die();
            if (!$delivery) {
                $this->session->set_flashdata('error', 'Bon de livraison introuvable');
                redirect('admin/deliveryitem');
            }

            if ((int)$delivery['status'] !== 1 && (int)$delivery['status'] !== 6) {
                $this->session->set_flashdata('error', 'Ce bon de livraison ne peut plus être validé');
                redirect('admin/deliveryitem');
            }

            // Préparation des données pour la vue
            $data = [
                'title' => 'Livraison partielle',
                'delivery' => $delivery,
                'itemcatlist' => $this->itemcategory_model->get(),
                'itemList' => $this->item_model->get(),
                'clients' => $this->clients_model->get()
            ];

            // Chargement des vues
            $this->load->view('layout/header', $data);
            $this->load->view('admin/itemdelivery/validate', $data);
            $this->load->view('layout/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Delivery Edit Error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Une erreur est survenue lors de la livraison partielle');
            redirect('admin/deliveryitem');
        }
        
    }


  
    /**
     * Met à jour un bon de livraison existant avec gestion des livraisons partielles
     */
    public function setPartialDelivery() {
        if (!$this->rbac->hasPrivilege('Deliveryitem', 'can_edit')) {
            access_denied();
        }

        // var_dump($this->input->post());
        // die();
        
        $id = $this->input->post('id');
        $response = ['status' => 'fail', 'message' => '', 'error' => []];

        try {
            // Vérifier le statut du bon de livraison
            $delivery = $this->delivery_model->getDeliveryWithItems($id);
            if (!$delivery) {
                throw new Exception('Bon de livraison introuvable');
            }

            if ((int)$delivery['status'] !== 1) {
                throw new Exception('Ce bon de livraison ne peut plus être modifié');
            }

            // var_dump($delivery);
            // die();

            // Validation des champs obligatoires
            $this->form_validation->set_rules('item_category_id[]', 'Catégorie', 'required');
            $this->form_validation->set_rules('item_id[]', 'Article', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantité', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('quantity_delivered[]', 'Quantité livrée', 'numeric|greater_than_equal_to[0]');
            $this->form_validation->set_rules('price[]', 'Prix unitaire', 'required|numeric|greater_than[0]');

            if ($this->form_validation->run() == false) {
                $response['error'] = $this->form_validation->error_array();
                echo json_encode($response);
                return;
            }

            // Récupération des données
            $data = [
                'id' => $id,
                'designation' => $this->input->post('designation'),
                'items' => []
            ];

            // var_dump($data);
            // die();
            // Validation des articles
            $categories = $this->input->post('item_category_id');
            $items = $this->input->post('item_id');
            $quantities = $this->input->post('quantity');
            $delivered_quantities = $this->input->post('quantity_delivered');
            $prices = $this->input->post('price');
            $units = $this->input->post('unit');

           
            // var_dump($units);
            // die();

            // Construction du tableau d'articles avec gestion des livraisons partielles
            foreach ($categories as $index => $category_id) {
                if (empty($items[$index]) || empty($quantities[$index]) || empty($prices[$index])) {
                    throw new Exception('Données d\'article manquantes');
                }

                $quantity = floatval($quantities[$index]);
                $delivered_qty = floatval($delivered_quantities[$index] ?? 0);
                $price = floatval($prices[$index]);
                
                // Vérification que la quantité livrée ne dépasse pas la quantité commandée
                if ($delivered_qty > $quantity) {
                    throw new Exception("La quantité livrée ne peut pas dépasser la quantité commandée pour l'article ".$items[$index]);
                }

                $data['items'][] = [
                    'category_id' => $category_id,
                    'item_id' => $items[$index],
                    'quantity' => $quantity,
                    'delivered_quantity' => $delivered_qty,
                    'price' => $price,
                    'unit' => $units[$index] ?? '',
                    'line_total' => $quantity * $price
                ];
            }

            // var_dump($data);
            // die();

            // Mise à jour des données
            $update_success = $this->delivery_model->partialDelivery($data);
            
            if (!$update_success) {
                throw new Exception('Erreur lors de la mise à jour');
            }

            // Mettre à jour le statut global si tout est livré
            // $this->checkDeliveryCompletion($id);

            $response['status'] = 'success';
            $response['message'] = 'Le bon de livraison a été mis à jour avec succès';

        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Delivery Update Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }



    
    /**
     * Envoie le bon de livraison par email au client
     * 
     * @param int $delivery_id ID du bon de livraison
     * @return void
     */
    public function sendEmail()
    {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Deliveryitem', 'can_edit')) {
            access_denied();
        }

        $delivery_id = $this->input->post('id', 0);

        // var_dump($delivery_id);
        // exit;

        // Initialisation de la réponse
        $response = ['status' => 'fail', 'message' => ''];

        try {
            // Récupération des données du bon de livraison
            $data['delivery'] = $this->delivery_model->getDeliveryWithItems($delivery_id);
            if (!$data['delivery']) {
                throw new Exception('Devis introuvable');
            }

            // Vérification de l'email du client
            if (empty($data['delivery']['customer_email'])) {
                throw new Exception('Le client n\'a pas d\'adresse email');
            }

            // Récupération des données de la société
            $company = $this->setting_model->get();

            // Récupération des données de l'entrepris
            $data['company'] = $company[0];
            $data['totalAsletter'] = $this->asLetters(floatval($data['delivery']['total_ttc']));

            // Récupération des informations de l'utilisateur connecté
            $data['user'] = $this->customlib->getUserData();

            // var_dump($data);
            // exit;


            //===================
            if ($data['delivery']) {

                $delivery_detail = array(
                    'id'            => $data['delivery']['id'], 
                    'data'          => $data, 
                    'credential_for'=> 'sendDelivery', 
                    'client_name'       => $data['delivery']['customer_name'].' '.$data['delivery']['customer_last_name'], 
                    'quotation_number'  => $data['delivery']['delivery_number'], 
                    'quotation_date'    => !empty($data['delivery']['delivery_date']) ? date('d/m/Y', strtotime($data['delivery']['delivery_date'])) :"N/A", 
                    'email'             => $data['delivery']['customer_email']);

                $this->mailsmsconf->mailsms('send_delivery', $delivery_detail);
            }
            
            $response['status'] = 'success';
            $response['message'] = 'Le bon de livraison a été envoyé avec succès';

        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Delivery Email Error: ' . $e->getMessage());
        }

        // Retourner la réponse en JSON
        echo json_encode($response);
    }

}