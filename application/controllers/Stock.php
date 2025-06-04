<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Stock extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('stock_model');
    }

    public function getList() {
        $data = $this->stock_model->getListData();
        echo $data;
    }

    public function getItemsByCategory() {
        $category_id = $this->input->get('category_id');
        
        if (!$category_id) {
            echo json_encode(['status' => 'error', 'message' => 'ID de catégorie requis']);
            return;
        }

        $items = $this->stock_model->getItemByCategory($category_id);
        echo json_encode(['status' => 'success', 'data' => $items]);
    }
} 