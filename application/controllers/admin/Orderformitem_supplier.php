<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Orderformitem_supplier extends Admin_Controller {

    /**
     * Constructor - Loads necessary helpers and performs initialization
     */
    function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('order_supplier_model');
        $this->load->model('itemcategory_model');
        $this->load->model('item_model');
        $this->load->model('itemsupplier_model');
    }

    /**
     * Main index method - Handles item listing and creation
     */
    function index() {
        // Check view permission
        if (!$this->rbac->hasPrivilege('order_item', 'can_view')) {
            access_denied();
        }

        // Set menu active states
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Orderformitem_supplier/index');
        
        // Initialize page data
        $data = [
            'title' => 'Add Item',
            'title_list' => 'Recent Items',
        ];

        // Load views
        $this->load->view('layout/header', $data);
        $this->load->view('admin/itemorder_supplier/list', $data);
        $this->load->view('layout/footer', $data);
    }

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
    public function form() {
        // Définition des menus actifs
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Orderformitem/index');

        // Préparation des données pour la vue
        $data = [
            'title' => 'Ajouter un article au devis',
            'title_list' => 'Derniers articles ajoutés au devis',
            'itemcatlist' => $this->itemcategory_model->get(),
            'supplier' => $this->itemsupplier_model->get()
        ];

        // Chargement des vues
        $this->load->view('layout/header', $data);
        $this->load->view('admin/itemorder/form', $data);
        $this->load->view('layout/footer', $data);
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
        $result = $this->order_supplier_model->getListData();
        
        // Les données sont déjà au format JSON, on les renvoie directement
        echo $result;
    }


    /**
     * Affiche les détails d'une entrée de stock
     * 
     * @param int $id ID de l'entrée de stock
     * @return void
     */
    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('order_item', 'can_view')) {
            access_denied();
        }

        $data['order'] = $this->order_supplier_supplier_model->getOrderWithItems($id);

        // var_dump($data);
        // exit;
        
        if (!$data['order']) {
            show_error('Bon de commande non trouvé', 404);
        }

        $data['title'] = 'Détails du bon de commande';
        $data['page_title'] = 'Détails du bon de commande ' . $data['order']['order_number'];

        $this->load->view('layout/header');
        $this->load->view('admin/itemorder_supplier/view', $data);
        $this->load->view('layout/footer');
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