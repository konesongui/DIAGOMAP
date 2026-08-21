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
        if (!$this->rbac->hasPrivilege('order_item', 'can_view')) {
            access_denied();
        }

        // Set menu active states
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Orderformitem/index');

        // 🔹 Vérifier les privilèges RBAC
        $is_superadmin = $this->rbac->hasPrivilege('superadmin');
        $is_admin = $this->rbac->hasPrivilege('admin');
        $is_admin_user = ($is_superadmin || $is_admin);

        // 🔹 Récupérer le nom de l'utilisateur connecté
        $current_user = $this->session->userdata('admin')['username'];

        // Initialize page data
        $data = [
            'title' => 'Bons de commande',
            'title_list' => 'Bons de commande récents',
            'current_user' => $current_user,
            'is_admin_user' => $is_admin_user
        ];

        // Load views
        $this->load->view('layout/header', $data);
        $this->load->view('admin/itemorder/list', $data);
        $this->load->view('layout/footer', $data);
    }

    function index_() {
        // Check view permission
        if (!$this->rbac->hasPrivilege('order_item', 'can_view')) {
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

            // 🔹 Récupérer le nom de l'utilisateur connecté
            $current_user = $this->session->userdata('admin')['username'];

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
                'user_name' => $current_user,
                'status' => 1,
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

    public function add_() {
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
                'status' => 1,
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

        // 🔹 Récupérer le nom de l'utilisateur connecté
        $current_user = $this->session->userdata('admin')['username'];

        // 🔹 Vérifier les privilèges RBAC
        $is_superadmin = $this->rbac->hasPrivilege('superadmin');
        $is_admin = $this->rbac->hasPrivilege('admin');
        $is_admin_user = ($is_superadmin || $is_admin);

        // Préparation des données pour la vue
        $data = [
            'title' => 'Ajouter un bon de commande',
            'title_list' => 'Derniers bons de commande',
            'itemcatlist' => $this->itemcategory_model->get(),
            'supplier' => $this->itemsupplier_model->get(),
            'current_user' => $current_user,
            'is_admin_user' => $is_admin_user
        ];

        // Chargement des vues
        $this->load->view('layout/header', $data);
        $this->load->view('admin/itemorder/form', $data);
        $this->load->view('layout/footer', $data);
    }

    public function form_() {
        // Définition des menus actifs
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Orderformitem/index');

        // Préparation des données pour la vue
        $data = [
            'title' => 'Ajouter un bon de commande',
            'title_list' => 'Derniers bons de commande',
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
        // 🔹 Récupère l'utilisateur connecté
        $current_user = $this->session->userdata('admin')['username'];

        // 🔹 Vérifier les privilèges RBAC
        $is_superadmin = $this->rbac->hasPrivilege('superadmin');
        $is_admin = $this->rbac->hasPrivilege('admin');
        $is_admin_user = ($is_superadmin || $is_admin);

        // Appeler la méthode du modèle selon le rôle
        if ($is_admin_user) {
            // Admin voit tout
            $result = $this->order_model->getListDataForAdmin();
        } else {
            // Utilisateur normal - filtrer par son nom
            $result = $this->order_model->getListDataForUser($current_user);
        }

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

        $data['order'] = $this->order_model->getOrderWithItems($id);

        if (!$data['order']) {
            show_error('Bon de commande non trouvé', 404);
        }

        // 🔹 VÉRIFICATION DE L'UTILISATEUR ET DU RÔLE
        $current_user = $this->session->userdata('admin')['username'];
        $is_superadmin = $this->rbac->hasPrivilege('superadmin');
        $is_admin = $this->rbac->hasPrivilege('admin');
        $is_admin_user = ($is_superadmin || $is_admin);

        if (!$is_admin_user && $data['order']['user_name'] != $current_user) {
            $this->session->set_flashdata('error', 'Vous n\'avez pas accès à ce bon de commande');
            redirect('admin/orderformitem');
        }

        $data['title'] = 'Détails du bon de commande';
        $data['page_title'] = 'Détails du bon de commande ' . $data['order']['order_number'];
        $data['is_admin_user'] = $is_admin_user;

        $this->load->view('layout/header', $data);
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

        // 🔹 VÉRIFICATION DE L'UTILISATEUR ET DU RÔLE
        $current_user = $this->session->userdata('admin')['username'];
        $order_data = $this->order_model->getOrderWithItems($id);

        if ($order_data) {
            $is_superadmin = $this->rbac->hasPrivilege('superadmin');
            $is_admin = $this->rbac->hasPrivilege('admin');
            $is_admin_user = ($is_superadmin || $is_admin);

            if (!$is_admin_user && $order_data['user_name'] != $current_user) {
                show_error('Vous n\'avez pas accès à ce bon de commande', 403);
                return;
            }
        }

        // Charger la vue d'impression
        $this->load->view('admin/itemorder/print', ['order' => $order]);
    }

    /**
     * Affiche le formulaire d'édition d'un bon de commande
     *
     * @param int $id ID du bon de commande
     * @return void
     */
    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('order_item', 'can_edit')) {
            access_denied();
        }

        $data['order'] = $this->order_model->getOrderWithItems($id);

        if (!$data['order']) {
            $this->session->set_flashdata('error', 'Bon de commande non trouvé');
            redirect('admin/orderformitem');
        }

        // 🔹 VÉRIFICATION DE L'UTILISATEUR ET DU RÔLE
        $current_user = $this->session->userdata('admin')['username'];
        $is_superadmin = $this->rbac->hasPrivilege('superadmin');
        $is_admin = $this->rbac->hasPrivilege('admin');
        $is_admin_user = ($is_superadmin || $is_admin);

        if (!$is_admin_user && $data['order']['user_name'] != $current_user) {
            $this->session->set_flashdata('error', 'Vous n\'avez pas accès à ce bon de commande');
            redirect('admin/orderformitem');
        }

        // 🔹 Récupérer le nom de l'utilisateur connecté
        $data['current_user'] = $current_user;
        $data['is_admin_user'] = $is_admin_user;

        // Préparation des données pour la vue
        $data['title'] = 'Modifier le bon de commande';
        $data['itemcatlist'] = $this->itemcategory_model->get();
        $data['clients'] = $this->clients_model->get();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/itemorder/edit', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Met à jour un bon de commande
     *
     * @return void
     */
    public function update()
    {
        if (!$this->rbac->hasPrivilege('order_item', 'can_edit')) {
            access_denied();
        }

        $response = ['status' => 'fail', 'message' => '', 'error' => []];

        try {
            // Vérification des données POST
            if (!$this->input->post()) {
                throw new Exception('Aucune donnée reçue');
            }

            $order_id = $this->input->post('id');

            // Vérifier l'accès au bon de commande
            $order = $this->order_model->getOrderWithItems($order_id);
            if (!$order) {
                throw new Exception('Bon de commande non trouvé');
            }

            // 🔹 VÉRIFICATION DE L'UTILISATEUR ET DU RÔLE
            $current_user = $this->session->userdata('admin')['username'];
            $is_superadmin = $this->rbac->hasPrivilege('superadmin');
            $is_admin = $this->rbac->hasPrivilege('admin');
            $is_admin_user = ($is_superadmin || $is_admin);

            if (!$is_admin_user && $order['user_name'] != $current_user) {
                throw new Exception('Vous n\'avez pas accès à ce bon de commande');
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

            // Préparation des données
            $data = [
                'id' => $order_id,
                'customer' => $this->input->post('customer'),
                'user_name' => $this->input->post('user_name'),
                'designation' => $this->input->post('designation'),
                'order_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('order_date')))),
                'valid_until' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('valid_until')))),
                'payment_term' => $this->input->post('payment_term'),
                'delivery_term' => $this->input->post('delivery_term'),
                'delivery_location' => $this->input->post('delivery_location'),
                'apply_tva' => $this->input->post('apply_tva') ? 1 : 0,
                'tva_rate' => $this->input->post('tva_rate'),
                'tva_amount' => $this->input->post('tva_amount'),
                'total_ht' => $this->input->post('total_ht'),
                'total_ttc' => $this->input->post('total_ttc'),
                'items' => []
            ];

            // Traitement des articles
            $categories = $this->input->post('item_category_id');
            $items = $this->input->post('item_id');
            $quantities = $this->input->post('quantity');
            $prices = $this->input->post('price');
            $units = $this->input->post('unit');

            foreach ($categories as $index => $category_id) {
                $data['items'][] = [
                    'category_id' => $category_id,
                    'item_id' => $items[$index],
                    'quantity' => floatval($quantities[$index]),
                    'price' => floatval($prices[$index]),
                    'unit' => $units[$index] ?? '',
                    'line_total' => floatval($quantities[$index]) * floatval($prices[$index])
                ];
            }

            // Mise à jour dans le modèle
            if ($this->order_model->update($data)) {
                $response['status'] = 'success';
                $response['message'] = 'Bon de commande mis à jour avec succès';
            } else {
                throw new Exception('Erreur lors de la mise à jour');
            }

        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Order Update Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    /**
     * Supprime un bon de commande
     *
     * @param int $id ID du bon de commande
     * @return void
     */
    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('order_item', 'can_delete')) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Accès refusé']);
                return;
            }
            access_denied();
        }

        $response = ['status' => 'fail', 'message' => ''];

        try {
            // Vérifier que le bon de commande existe
            $order = $this->order_model->getOrderWithItems($id);
            if (!$order) {
                throw new Exception('Bon de commande non trouvé');
            }

            // 🔹 VÉRIFICATION DE L'UTILISATEUR ET DU RÔLE
            $current_user = $this->session->userdata('admin')['username'];
            $is_superadmin = $this->rbac->hasPrivilege('superadmin');
            $is_admin = $this->rbac->hasPrivilege('admin');
            $is_admin_user = ($is_superadmin || $is_admin);

            if (!$is_admin_user && $order['user_name'] != $current_user) {
                throw new Exception('Vous n\'avez pas accès à ce bon de commande');
            }

            // Vérifier le statut
            if ((int)$order['status'] !== 1) {
                throw new Exception('Ce bon de commande ne peut pas être supprimé');
            }

            // Suppression transactionnelle
            $this->db->trans_start();

            // Supprimer les articles liés
            $this->db->where('order_id', $id)->delete('order_items');

            // Supprimer le bon de commande
            $this->db->where('id', $id)->delete('orders');

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                throw new Exception("Erreur lors de la suppression du bon de commande");
            }

            $response['status'] = 'success';
            $response['message'] = 'Bon de commande supprimé avec succès';

        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            log_message('error', 'Order Delete Error: ' . $e->getMessage());
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode($response);
        } else {
            if ($response['status'] === 'success') {
                $this->session->set_flashdata('success', $response['message']);
            } else {
                $this->session->set_flashdata('error', $response['message']);
            }
            redirect('admin/orderformitem');
        }
    }
}