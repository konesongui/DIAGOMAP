<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Orderformitem extends Admin_Controller {

    /**
     * Constructor - Loads necessary helpers and performs initialization
     */
    function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('order_model');
        $this->load->model('itemcategory_model');
        $this->load->model('item_model');
        $this->load->model('clients_model');
    }

    /**
     * Main index method - Handles item listing and creation
     */
    function index() {
        // Check view permission
        if (!$this->rbac->hasPrivilege('item', 'can_view')) {
            access_denied();
        }

        // Set menu active states
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Orderformitem/index');
        
        // Initialize page data
        $data = [
            'title' => 'Add Item',
            'title_list' => 'Recent Items',
        ];

        // Load views
        $this->load->view('layout/header', $data);
        $this->load->view('admin/itemorder/list', $data);
        $this->load->view('layout/footer', $data);
    }


    /**
     * GET ITEM BY CATEGORY
     * 
     * @return  JSON   $data
     */
    public function getItemByCategory()
    {   
        var_dump($this->input->get());
        exit;
        $item_category_id = $this->input->get('item_category_id');
        $data             = $this->stock_model->getItemByCategory($item_category_id);
        echo json_encode($data);
    }


    /**
     * GET STOCK ENTRY DATA
     * IN JSON FORMAT
     * 
     * @return  JSON   $response
     */
    public function data()
    {   
        // Récupère les données du modèle
        $result = $this->order_model->getListData();
        
        // Les données sont déjà au format JSON, on les renvoie directement
        echo $result;
    }


    /**
     * STOCK ENTRY TOOL FORM
     */
    public function form() {
        // Définition des menus actifs
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Orderformitem/index');
        
        // Préparation des données pour la vue
        $data = [
            'title' => 'Ajouter un article au devis',
            'title_list' => 'Derniers articles ajoutés au devis',
            'itemcatlist' => $this->itemcategory_model->get(),
            'clients' => $this->clients_model->get()
        ];

        // Chargement des vues
        $this->load->view('layout/header', $data);
        $this->load->view('admin/itemorder/form', $data);
        $this->load->view('layout/footer', $data);
    }

    
    
    /**
     * STOCK ENTRY TOOL FORM
     */

     public function add() {
        // Initialisation de la réponse
        $response = ['status' => 'fail', 'message' => '', 'error' => []];

        try {
            // Vérification des données POST
            if (!$this->input->post()) {
                throw new Exception('Aucune donnée reçue');
            }

            // Validation des champs obligatoires
            $this->form_validation->set_rules('designation', 'Désignation', 'required|trim');
            $this->form_validation->set_rules('customer', 'Client', 'required|trim');
            $this->form_validation->set_rules('order_date', 'Date', 'required');
            $this->form_validation->set_rules('item_category_id[]', 'Catégorie', 'required');
            $this->form_validation->set_rules('item_id[]', 'Article', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantité', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('price[]', 'Prix unitaire', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('total_ht', 'Total HT', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('total_ttc', 'Total TTC', 'required|numeric|greater_than[0]');

            if ($this->form_validation->run() == false) {
                $response['error'] = $this->form_validation->error_array();
                echo json_encode($response);
                return;
            }

            // Récupération et validation des données
            $data = [
                'designation' => $this->input->post('designation'),
                'customer_id' => $this->input->post('customer'),
                'order_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('order_date')))),
                'valid_until' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('valid_until')))),
                'payment_term' => $this->input->post('payment_term'),
                'delivery_term' => $this->input->post('delivery_term'),
                'delivery_location' => $this->input->post('delivery_location'),
                'apply_tva' => $this->input->post('apply_tva'),
                'tva_rate' => $this->input->post('tva_rate'),
                'tva_amount' => $this->input->post('tva_amount'),
                'total_ht' => $this->input->post('total_ht'),
                'total_ttc' => $this->input->post('total_ttc'),
                'status' => 1, // 1 = En attente
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
                    'price' => $price,
                    'unit' => $units[$index] ?? '',
                    'line_total' => $line_total
                ];
            }

            // var_dump($data);
            // exit;

            // Enregistrement des données
            $insert_id = $this->order_model->add($data);
            
            if (!$insert_id) {
                throw new Exception('Erreur lors de l\'enregistrement');
            }

            $response['status'] = 'success';
            $response['message'] = 'Le bon de commande a été enregistré avec succès';
            $response['order_id'] = $insert_id;

        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Order Add Error: ' . $e->getMessage());
        }

        // Retourner la réponse en JSON
        echo json_encode($response);
    }

    


    /**
     * Affiche les détails d'une entrée de stock
     * 
     * @param int $id ID de l'entrée de stock
     * @return void
     */
    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('Orderformitem', 'can_view')) {
            access_denied();
        }

        $data['order'] = $this->order_model->getOrderWithItems($id);

        // var_dump($data);
        // exit;
        
        if (!$data['order']) {
            show_error('Bon de commande non trouvé', 404);
        }

        $data['title'] = 'Détails du bon de commande';
        $data['page_title'] = 'Détails du bon de commande ' . $data['order']['order_number'];

        $this->load->view('layout/header');
        $this->load->view('admin/itemorder/view', $data);
        $this->load->view('layout/footer');
    }

    
    /**
     * Envoie le devis par email au client
     * 
     * @param int $quote_id ID du devis
     * @return void
     */
    public function sendEmail() {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Or', 'can_view')) {
            access_denied();
        }

        var_dump($this->input->post());
        exit;

        // Initialisation de la réponse
        $response = ['status' => 'fail', 'message' => ''];

        // try {
        //     // Récupération des données du devis
        //     $quote_data = $this->quote_model->getQuoteWithItems($quote_id);
        //     if (!$quote_data) {
        //         throw new Exception('Devis introuvable');
        //     }

        //     // Vérification de l'email du client
        //     if (empty($quote_data['customer_email'])) {
        //         throw new Exception('Le client n\'a pas d\'adresse email');
        //     }

        //     // Génération du PDF
        //     $this->pdf_quote->generateQuote($quote_data);

        //     // Création du dossier de stockage si nécessaire
        //     $upload_dir = FCPATH . 'uploads/quotes/';
        //     if (!is_dir($upload_dir)) {
        //         mkdir($upload_dir, 0777, true);
        //     }

        //     // Sauvegarde du PDF
        //     $pdf_filename = 'devis_' . $quote_data['quote_number'] . '.pdf';
        //     $pdf_path = $upload_dir . $pdf_filename;
        //     $this->pdf_quote->Output($pdf_path, 'F');

        //     // Configuration de l'email
        //     $this->load->library('email');
            
        //     $config = [
        //         'protocol' => 'smtp',
        //         'smtp_host' => $this->config->item('smtp_host'),
        //         'smtp_port' => $this->config->item('smtp_port'),
        //         'smtp_user' => $this->config->item('smtp_user'),
        //         'smtp_pass' => $this->config->item('smtp_pass'),
        //         'mailtype' => 'html',
        //         'charset' => 'utf-8',
        //         'wordwrap' => TRUE
        //     ];

        //     $this->email->initialize($config);

        //     // Préparation de l'email
        //     $this->email->from($this->config->item('smtp_user'), $this->config->item('company_name'));
        //     $this->email->to($quote_data['customer_email']);
        //     $this->email->subject('Devis ' . $quote_data['quote_number'] . ' - ' . $quote_data['designation']);

        //     // Préparation du message
        //     $message = $this->load->view('admin/quote/email_template', [
        //         'quote' => $quote_data,
        //         'company' => [
        //             'name' => $this->config->item('company_name'),
        //             'address' => $this->config->item('company_address'),
        //             'phone' => $this->config->item('company_phone'),
        //             'email' => $this->config->item('company_email')
        //         ]
        //     ], TRUE);

        //     $this->email->message($message);
        //     $this->email->attach($pdf_path);

        //     // Envoi de l'email
        //     if ($this->email->send()) {
        //         // Mise à jour du statut du devis
        //         $this->quote_model->updateStatus($quote_id, 1); // 1 = Envoyé
        //         $response['status'] = 'success';
        //         $response['message'] = 'Le devis a été envoyé avec succès';
        //     } else {
        //         throw new Exception('Erreur lors de l\'envoi de l\'email: ' . $this->email->print_debugger(['headers', 'subject', 'body']));
        //     }

        // } catch (Exception $e) {
        //     $response['message'] = 'Erreur: ' . $e->getMessage();
        //     log_message('error', 'Quote Email Error: ' . $e->getMessage());
        // }

        // // Suppression du fichier PDF temporaire
        // if (isset($pdf_path) && file_exists($pdf_path)) {
        //     unlink($pdf_path);
        // }

        // // Retourner la réponse en JSON
        // echo json_encode($response);
    }

    /**
     * Affiche le formulaire d'édition d'un devis
     * 
     * @param int $id ID du devis
     * @return void
     */
    public function edit($id) {
        if (!$this->rbac->hasPrivilege('Or', 'can_edit')) {
            access_denied();
        }

       
        // Vérifier le statut du devis
        $order = $this->order_model->getOrderWithItems($id);
        if (!$order) {
            $this->session->set_flashdata('error', 'Bon de commande introuvable');
            redirect('admin/orderformitem');
        }

        if ((int)$order['status'] !== 1) {
            $this->session->set_flashdata('error', 'Ce bon de commande ne peut plus être modifié');
            redirect('admin/orderformitem');
        }

        // Préparer les données pour la vue
        $data = [
            'title' => 'Modifier le bon de commande',
            'order' => $order,
            'itemcatlist' => $this->itemcategory_model->get(),
            'itemList' => $this->item_model->get(),
            'clients' => $this->clients_model->get()
        ];

        // Charger les vues
        $this->load->view('layout/header', $data);
        $this->load->view('admin/itemorder/edit', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Met à jour un devis existant
     * 
     * @param int $id ID du devis
     * @return void
     */
    public function update() {
        if (!$this->rbac->hasPrivilege('Or', 'can_edit')) {
            access_denied();
        }

        // var_dump($this->input->post());
        // exit;  

        // Récupérer l'ID du devis
        $id = $this->input->post('id');
        
        // Initialisation de la réponse
        $response = ['status' => 'fail', 'message' => '', 'error' => []];

        try {
            // Vérifier le statut du devis
            $order = $this->order_model->getOrderWithItems($id);
            if (!$order) {
                throw new Exception('Bon de commande introuvable');
            }

            if ((int)$order['status'] !== 1) {
                throw new Exception('Ce bon de commande ne peut plus être modifié');
            }

            // Validation des champs obligatoires
            $this->form_validation->set_rules('designation', 'Désignation', 'required|trim');
            $this->form_validation->set_rules('customer', 'Client', 'required|trim');
            $this->form_validation->set_rules('order_date', 'Date', 'required');
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
                'customer' => $this->input->post('customer'),
                'apply_tva' => $this->input->post('apply_tva'),
                'tva_amount' => $this->input->post('tva_amount'),
                'total_ht' => $this->input->post('total_ht'),
                'total_ttc' => $this->input->post('total_ttc'),
                'tva_rate' => $this->input->post('tva_rate'),
                'valid_until' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('valid_until')))),
                'payment_term' => $this->input->post('payment_term'),
                'delivery_term' => $this->input->post('delivery_term'),
                'delivery_location' => $this->input->post('delivery_location'),
                'order_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('order_date')))),
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
                    'price' => $price,
                    'unit' => $units[$index] ?? '',
                    'line_total' => $line_total
                ];
            }

            // var_dump($data);
            // exit;   
            // Mise à jour des données
            $update_success = $this->order_model->update($data);
            
            if (!$update_success) {
                throw new Exception('Erreur lors de la mise à jour');
            }

            $response['status'] = 'success';
            $response['message'] = 'Le bon de commande a été mis à jour avec succès';

        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Order Update Error: ' . $e->getMessage());
        }

        // Retourner la réponse en JSON
        echo json_encode($response);
    }

    /**
     * Crée un bon de commande à partir d'un devis validé
     * 
     * @param int $quote_id ID du devis
     * @return bool
     */
    private function createDeliveryFromOrder($order_id) {
        // Récupération des données de la commande
        $order = $this->order_model->getOrderWithItems($order_id);
        if (!$order) {
            return false;
        }

        // Génération du numéro de livraison
        $delivery_number = $this->generateDeliveryNumber();

        // Préparation des données de la livraison
        $delivery_data = [
            'delivery_number' => $delivery_number,
            'customer_id' => $order['customer_id'],
            'order_id' => $order_id,
            'delivery_date' => date('Y-m-d'),
            'shipping_method' => $order['delivery_term'],
            'delivery_address' => $order['delivery_location'],
            'status' => 1, // 0 = En préparation
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Début de la transaction
        $this->db->trans_start();

        try {
            // Insertion de la livraison
            $this->db->insert('deliveries', $delivery_data);
            $delivery_id = $this->db->insert_id();

            if (!$delivery_id) {
                throw new Exception('Erreur lors de la création de la livraison');
            }

            // Insertion des articles de la livraison
            foreach ($order['items'] as $item) {
                $delivery_item_data = [
                    'delivery_id' => $delivery_id,
                    'category_id' => $item['category_id'],
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'delivered_quantity' => 0,
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                    'position' => $item['position']
                ];

                if (!$this->db->insert('delivery_items', $delivery_item_data)) {
                    throw new Exception('Erreur lors de l\'ajout d\'un article à la livraison');
                }
            }

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Delivery Creation Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Génère un numéro unique pour une livraison
     * Format: BL-YYYYMM-XXXX où XXXX est un numéro séquentiel
     * 
     * @return string
     */
    private function generateDeliveryNumber() {
        $prefix = 'BL';  // BL pour Bon de Livraison
        $date = date('Ym');  // Format YYYYMM
        
        // Recherche le dernier numéro pour ce mois
        $this->db->like('delivery_number', $prefix . '-' . $date, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('deliveries');
        
        if ($query->num_rows() > 0) {
            // Extrait le numéro séquentiel de la dernière livraison
            $last_ref = $query->row()->delivery_number;
            $sequence = intval(substr($last_ref, -4)) + 1;
        } else {
            // Première livraison du mois
            $sequence = 1;
        }
        
        // Formate le numéro séquentiel sur 4 chiffres
        $sequence_padded = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        
        return $prefix . '-' . $date . '-' . $sequence_padded;
    }

    /**
     * Valide un devis en changeant son statut à 2 et crée un bon de commande
     * 
     * @param int $id ID du devis
     * @return void
     */
    public function validate($id) {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Or', 'can_edit')) {
            access_denied();
        }

        // Initialisation de la réponse
        $response = ['status' => 'fail', 'message' => ''];

        try {
            // Vérifier si le bon de commande existe
            $order = $this->order_model->getOrderWithItems($id);
            if (!$order) {
                throw new Exception('Bon de commande introuvable');
            }

            // Vérifier si le devis peut être validé
            if ((int)$order['status'] !== 1) {
                throw new Exception('Ce bon de commande ne peut plus être validé');
            }

            // Mise à jour du statut
            $update_success = $this->order_model->updateStatus($id, 2); // 2 = Validé
            
            if (!$update_success) {
                throw new Exception('Erreur lors de la validation du bon de commande');
            }

            // Création du bon de commande
            $order_created = $this->createDeliveryFromOrder($id);
            if (!$order_created) {
                throw new Exception('Erreur lors de la création du bon de commande');
            }

            $response['status'] = 'success';
            $response['message'] = 'Le bon de commande a été validé et le bon de commande a été créé avec succès';

        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Order Validate Error: ' . $e->getMessage());
        }

        // Retourner la réponse en JSON
        echo json_encode($response);
    }

    
    /**
     * Rejette un devis en changeant son statut à 3
     * 
     * @param int $id ID du devis
     * @return void
    */
    public function reject() {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Or', 'can_edit')) {
            access_denied();
        }
        
        $id = $this->input->post('id');

        // Initialisation de la réponse
        $response = ['status' => 'fail', 'message' => ''];

        try {
            // Vérifier si le devis existe
            $order = $this->order_model->getOrderWithItems($id);
            if (!$order) {
                throw new Exception('Bon de commande introuvable');
            }

            // Vérifier si le devis peut être rejeté
            if ((int)$order['status'] !== 1) {
                throw new Exception('Ce bon de commande ne peut plus être rejeté');
            }

            // Récupérer la raison du rejet
            $reject_note = $this->input->post('reason');
            if (empty($reject_note)) {
                throw new Exception('La raison du rejet est obligatoire');
            }

            // Mise à jour du statut et de la note
            $update_data = [
                'id'            => $id,
                'customer_id'   => $order['customer_id'],
                'reason'        => $reject_note
            ];
            
            $update_success = $this->order_model->rejectOrder($id, $update_data);
            
            if (!$update_success) {
                throw new Exception('Erreur lors du rejet du bon de commande');
            }

            $response['status'] = 'success';
            $response['message'] = 'Le bon de commande a été rejeté avec succès';

        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Order Reject Error: ' . $e->getMessage());
        }

        // Retourner la réponse en JSON
        echo json_encode($response);
    }


    /**
     * Affiche la page d'impression d'un devis
     * 
     * @param int $id ID du devis
     * @return void
     */
    public function print($id)
    {
        // Récupérer les données du devis
        $order = $this->order_model->getOrderForPrint($id);
        
        if (!$order) {
            show_404();
            return;
        }

        // Charger la vue d'impression
        $this->load->view('admin/itemorder/print', ['order' => $order]);
    }

}