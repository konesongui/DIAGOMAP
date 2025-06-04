<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Stockentry extends Admin_Controller {

    /**
     * Constructor - Loads necessary helpers and performs initialization
     */
    function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('stockentry_model');
        $this->load->model('itemcategory_model');
        $this->load->model('staff_model');
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
        $this->session->set_userdata('sub_menu', 'Stockentry/index');
        
        // Initialize page data
        $data = [
            'title' => 'Stock Entry',
            'title_list' => 'Recent Stock Entry',
        ];

        // Load views
        $this->load->view('layout/header', $data);
        $this->load->view('admin/stockentry/list', $data);
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
        $result = $this->stockentry_model->getListData();
        
        // Les données sont déjà au format JSON, on les renvoie directement
        echo $result;
    }

    /**
     * STOCK ENTRY TOOL FORM
     */
    public function form() {
        // Définition des menus actifs
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Stockentry/index');
        
        // Préparation des données pour la vue
        $data = [
            'title' => 'Ajouter un article au stock',
            'title_list' => 'Derniers articles ajoutés au stock',
            'roles' => $this->role_model->get(),
            'itemcatlist' => $this->itemcategory_model->get(),
            'staff' => $this->staff_model->inventry_staff()
        ];

        // Chargement des vues
        $this->load->view('layout/header', $data);
        $this->load->view('admin/stockentry/form', $data);
        $this->load->view('layout/footer', $data);
    }

    
    
    /**
     * STOCK ENTRY TOOL FORM
     */
    public function add() {
        // Initialisation de la réponse
        $response = ['status' => 'fail', 'message' => ''];

        // var_dump($this->input->post());
        // exit;

        try {
            // Vérification des données POST
            if (!$this->input->post()) {
                throw new Exception('Aucune donnée reçue');
            }

            // Validation des champs obligatoires
            $this->form_validation->set_rules('designation', 'Désignation', 'required|trim');
            $this->form_validation->set_rules('issue_date', 'Date', 'required');
            $this->form_validation->set_rules('item_category_id[]', 'Catégorie', 'required');
            $this->form_validation->set_rules('item_id[]', 'Article', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantité', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('price[]', 'Prix unitaire', 'required|numeric|greater_than[0]');

            if ($this->form_validation->run() == false) {
                throw new Exception(validation_errors());
            }

            // Récupération et validation des données
            $data = [
                'designation' => $this->input->post('designation'),
                'issue_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('issue_date')))),
                'grand_total' => $this->input->post('grandtotal'),
                'items' => []
            ];

            // Validation des articles
            $categories = $this->input->post('item_category_id');
            $items = $this->input->post('item_id');
            $quantities = $this->input->post('quantity');
            $prices = $this->input->post('price');
            $units = $this->input->post('unit');

            if (!is_array($categories) || !is_array($items) || !is_array($quantities) || !is_array($prices) || !is_array($units)) {
                throw new Exception('Format de données invalide');
            }

            // Construction du tableau d'articles
            foreach ($categories as $index => $category_id) {
                if (empty($items[$index]) || empty($quantities[$index])) {
                    throw new Exception('Données d\'article manquantes');
                }

                $data['items'][] = [
                    'category_id' => $category_id,
                    'item_id' => $items[$index],
                    'quantity' => $quantities[$index],
                    'price' => $prices[$index] ?? 0,
                    'unit' => $units[$index] ?? '',
                    'line_total' => $quantities[$index] * ($prices[$index] ?? 0)
                ];
            }

            // var_dump($data);
            // exit;
            // Enregistrement des données
            $insert_id = $this->stockentry_model->add($data);
            
            if (!$insert_id) {
                throw new Exception('Erreur lors de l\'enregistrement');
            }

            $response['status'] = 'success';
            $response['message'] = 'L\'entrée de stock a été enregistrée avec succès';

        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Stockentry Add Error: ' . $e->getMessage());
        }

        // Retourner la réponse en JSON
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Affiche les détails d'une entrée de stock
     * 
     * @param int $id ID de l'entrée de stock
     * @return void
     */
    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('stock_entry', 'can_view')) {
            access_denied();
        }

        $data['entry'] = $this->stockentry_model->getEntryWithItems($id);
        
        if (!$data['entry']) {
            show_error('Entrée de stock non trouvée', 404);
        }

        $data['title'] = 'Détails de l\'entrée de stock';
        $data['page_title'] = 'Détails de l\'entrée ' . $data['entry']['reference'];

        $this->load->view('layout/header');
        $this->load->view('admin/stockentry/view', $data);
        $this->load->view('layout/footer');
    }

}