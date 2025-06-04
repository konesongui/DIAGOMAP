<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Itemstock extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('itemstock_model');
        $this->load->model('stock_model');

    }



    /**
     * Main index method - Handles item listing and creation
     */
    function index() {
        // Check view permission
        if (!$this->rbac->hasPrivilege('item_stock', 'can_view')) {
            access_denied();
        }

        // Set menu active states
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Itemstock/index');
        
        // Initialize page data
        $data = [
            'title' => 'Stock',
            'title_list' => 'Recent Stock',
        ];

        // Load views
        $this->load->view('layout/header', $data);
        $this->load->view('admin/itemstock/list', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getItemByCategory()
    {
        $item_category_id = $this->input->get('item_category_id');
        $data             = $this->item_model->getItemByCategory($item_category_id);
        echo json_encode($data);
    }


    /**
     * GET STOCK DATA
     * IN JSON FORMAT
     * 
     * @return  JSON   $response
     */
    public function data()
    {   
        // var_dump($this->stock_model);
        // exit();
        // Récupère les données du modèle
        $result = $this->stock_model->getListData();
        
        // Les données sont déjà au format JSON, on les renvoie directement
        echo $result;
    }


}
