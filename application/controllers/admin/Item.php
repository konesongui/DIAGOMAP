<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Item extends Admin_Controller {

    /**
     * Constructor - Loads necessary helpers and performs initialization
     */
    function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('item_model');
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
        $this->session->set_userdata('sub_menu', 'Item/index');
        
        // Initialize page data
        $data = [
            'title' => 'Add Item',
            'title_list' => 'Recent Items',
            'itemlist' => [],
            'itemcatlist' => []
        ];

        // Set form validation rules
        $this->_set_validation_rules();

        // Process form submission if validation passes
        if ($this->form_validation->run()) {
            $this->_process_item_creation();
            return; // Redirect happens in _process_item_creation
        }

        // Get items and categories for the view
        $data['itemlist'] = $this->item_model->get();
        $data['itemcatlist'] = $this->itemcategory_model->get();

        // Load views
        $this->load->view('layout/header', $data);
        $this->load->view('admin/item/itemList', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Download file handler
     * @param string $file - File to download
     */
    public function download($file) {
        $this->load->helper('download');
        $filepath = "./uploads/inventory_items/" . $this->uri->segment(6);
        $data = file_get_contents($filepath);
        $name = $this->uri->segment();
        force_download($name, $data);
    }

    /**
     * Delete an item
     * @param int $id - Item ID to delete
     */
    function delete($id) {
        if (!$this->rbac->hasPrivilege('item', 'can_delete')) {
            access_denied();
        }
        
        $this->item_model->remove($id);
        redirect('admin/item/index');
    }

    /**
     * Get available quantity of an item via AJAX
     */
    function getAvailQuantity() {
        $item_id = $this->input->get('item_id');
        $data = $this->item_model->getItemAvailable($item_id);

        $available = max(0, ($data['added_stock'] - $data['issued']));
        echo json_encode(['available' => $available]);
    }

    /**
     * Edit an existing item
     * @param int $id - Item ID to edit
     */
    function edit($id) {
        if (!$this->rbac->hasPrivilege('item', 'can_edit')) {
            access_denied();
        }

        // Initialize data for view
        $data = [
            'title' => 'Edit Item',
            'id' => $id,
            'item' => $this->item_model->get($id),
            'itemlist' => $this->item_model->get(),
            'itemcatlist' => $this->itemcategory_model->get()
        ];

        // Set form validation rules
        $this->_set_validation_rules();

        // Process form submission if validation passes
        if ($this->form_validation->run()) {
            $this->_process_item_update($id);
            return; // Redirect happens in _process_item_update
        }

        // Load edit views
        $this->load->view('layout/header', $data);
        $this->load->view('admin/item/itemEdit', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * File upload validation handler
     */
    function handle_upload() {
        if (!isset($_FILES["file"]) || empty($_FILES['file']['name'])) {
            return true;
        }

        $allowedExts = ['jpg', 'jpeg', 'png'];
        $temp = explode(".", $_FILES["file"]["name"]);
        $extension = strtolower(end($temp));
        $error = '';

        // Check for upload errors
        if ($_FILES["file"]["error"] > 0) {
            $error .= "Error opening the file<br />";
        }

        // Check MIME type
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES["file"]["type"], $allowedMimeTypes)) {
            $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
            return false;
        }

        // Check file extension
        if (!in_array($extension, $allowedExts)) {
            $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
            return false;
        }

        // Check file size (10MB max)
        if ($_FILES["file"]["size"] > 10485760) {
            $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than'));
            return false;
        }

        return empty($error);
    }

    /**
     * Retourne la liste des articles au format JSON
     */
    public function getList() {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('item', 'can_view')) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Accès refusé']));
            return;
        }

        // Récupération des articles
        $items = $this->item_model->get();

        // Formatage des données
        $formatted_items = array_map(function($item) {
            return [
                'id' => $item['id'],
                'nom' => $item['name'],
                'categorie' => $item['item_category'],
                'magasin' => $item['item_store'],
                'stock_disponible' => $item['added_stock'] - ($item['issued'] - $item['returned']),
                'unite' => $item['unit'],
                'description' => $item['des']
            ];
        }, $items);

        // Envoi de la réponse
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'data' => $formatted_items
            ]));
    }

    // ==================== PRIVATE METHODS ==================== //

    /**
     * Set common form validation rules
     */
    private function _set_validation_rules() {
        $this->form_validation->set_rules('name', $this->lang->line('item'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('unit', $this->lang->line('unit'), 'trim|xss_clean');
        $this->form_validation->set_rules(
            'item_category_id', 
            $this->lang->line('item_category'), 
            [
                'required',
                ['check_exists', [$this->item_model, 'valid_check_exists']]
            ]
        );
    }

    /**
     * Process item creation form submission
     */
    private function _process_item_creation() {

        // var_dump($this->input->post());
        // exit;

        // Handle file upload if present    
        $item_data = [
            'item_category_id' => $this->input->post('item_category_id'),
            'name' => $this->input->post('name'),
            'unit' => $this->input->post('unit'),
            'description' => $this->input->post('description'),
        ];

        $this->item_model->add($item_data);
        $this->_set_success_message();
        redirect('admin/item/index');
    }

    /**
     * Process item update form submission
     * @param int $id - Item ID to update
     */
    private function _process_item_update($id) {
        $item_data = [
            'id' => $id,
            'item_category_id' => $this->input->post('item_category_id'),
            'name' => $this->input->post('name'),
            'unit' => $this->input->post('unit'),
            'description' => $this->input->post('description'),
        ];

        $this->item_model->add($item_data);

        // Handle item photo upload if present
        if (isset($_FILES["item_photo"]) && !empty($_FILES['item_photo']['name'])) {
            $this->_handle_item_photo_upload($id);
        }

        $this->_set_success_message();
        redirect('admin/item/index');
    }

    /**
     * Handle item photo upload
     * @param int $id - Item ID
     */
    private function _handle_item_photo_upload($id) {
        $fileInfo = pathinfo($_FILES["item_photo"]["name"]);
        $img_name = $id . '.' . $fileInfo['extension'];
        $upload_path = "./uploads/inventory_items/" . $img_name;
        
        if (move_uploaded_file($_FILES["item_photo"]["tmp_name"], $upload_path)) {
            $data_img = [
                'id' => $id,
                'item_photo' => 'uploads/inventory_items/' . $img_name
            ];
            $this->item_model->add($data_img);
        }
    }

    /**
     * Set success flash message
     */
    private function _set_success_message() {
        $this->session->set_flashdata('msg', 
            '<div class="alert alert-success text-left">' . 
            $this->lang->line('success_message') . 
            '</div>'
        );
    }
}