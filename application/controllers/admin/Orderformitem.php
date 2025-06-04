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