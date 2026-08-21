<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Quoteitem_purchase extends Admin_Controller
{

    /**
     * Constructor - Loads necessary helpers and performs initialization
     */
    function __construct()
    {
        parent::__construct();
        $this->load->helper('form');

        $this->config->load("app-config");
        $this->load->library('Enc_lib');
        $this->load->library('mailsmsconf');
        $this->load->library('encoding_lib');
        $this->load->library('customlib');

        $this->load->model('quote_purchase_model');
        $this->load->model('itemcategory_model');
        $this->load->model('item_model');
        $this->load->model('itemsupplier_model');
        $this->load->model('stock_model');

        $this->load->library('customlib');
    }


    /**
     * Main index method - Handles item listing and creation
     */
    function index()
    {
        // Check view permission
        if (!$this->rbac->hasPrivilege('item', 'can_view')) {
            access_denied();
        }

        // Set menu active states
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Quoteitem_purchase/index');

        // Initialize page data
        $data = [
            'title' => 'Add Item',
            'title_list' => 'Recent Items',
        ];

        // Load views
        $this->load->view('layout/header', $data);
        $this->load->view('admin/quote_purchase/list', $data);
        $this->load->view('layout/footer', $data);
    }


    /**
     * GET ITEM BY CATEGORY
     * 
     * @return  JSON   $data
     */
    public function getItemByCategory()
    {
        // var_dump($this->input->get());
        // exit;
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
        $result = $this->quote_purchase_model->getListData();

        // Les données sont déjà au format JSON, on les renvoie directement
        echo $result;
    }


    /**
     * STOCK ENTRY TOOL FORM
     */
    public function form()
    {
        // Définition des menus actifs
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Quoteitem_purchase/index');

        // Préparation des données pour la vue
        $data = [
            'title' => 'Ajouter un article au devis',
            'title_list' => 'Derniers articles ajoutés au devis',
            'itemcatlist' => $this->itemcategory_model->get(),
            'itemlist' => $this->item_model->get(),
            'supplier' => $this->itemsupplier_model->get()
        ];

        // Chargement des vues
        $this->load->view('layout/header', $data);
        $this->load->view('admin/quote_purchase/form', $data);
        $this->load->view('layout/footer', $data);
    }


    /**
     * STOCK ENTRY TOOL FORM
     */

    public function add()
    {
        if (!$this->rbac->hasPrivilege('item', 'can_view')) {
            access_denied();
        }
        // Initialisation de la réponse
        $response = ['status' => 'fail', 'message' => '', 'error' => []];

        try {
            // Vérification des données POST
            if (!$this->input->post()) {
                throw new Exception('Aucune donnée reçue');
            }

            // var_dump($this->input->post());
            // exit;

            // Validation des champs obligatoires
            $this->form_validation->set_rules('customer', 'Fournisseur', 'required|trim');
            $this->form_validation->set_rules('quote_date', 'Date', 'required');
            $this->form_validation->set_rules('item_category[]', 'Catégorie', 'required');
            $this->form_validation->set_rules('item_name[]', 'Article', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantité', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('price[]', 'Prix unitaire', 'required|numeric|greater_than[0]');

            if ($this->form_validation->run() == false) {
                $response['error'] = $this->form_validation->error_array();
                echo json_encode($response);
                return;
            }

            // Récupération des données
            $categories = $this->input->post('item_category');
            $items = $this->input->post('item_name');
            $quantities = $this->input->post('quantity');
            $prices = $this->input->post('price');
            $units = $this->input->post('unit');

            if (!is_array($categories) || !is_array($items) || !is_array($quantities) || !is_array($prices)) {
                throw new Exception('Format de données invalide');
            }

            // Préparation des données du devis
            $quote_data = [
                'quote_number'=> $this->generateQuoteNumber(),
                'supplier_id' => $this->input->post('customer'),
                'quote_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('quote_date')))),
                'valid_until' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('valid_until')))),
                'payment_terms' => $this->input->post('payment_term'),
                'delivery_terms' => $this->input->post('delivery_term'),
                'delivery_location' => $this->input->post('delivery_location'),
                'apply_tva' => $this->input->post('apply_tva') ? 1 : 0,
                'tva_rate' => $this->input->post('tva_rate'),
                'tva_amount' => $this->input->post('tva_amount'),
                'total_ht' => $this->input->post('total_ht'),
                'total_ttc' => $this->input->post('total_ttc'),
                'status' => 1, // 1 = En attente
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Début de la transaction
            $this->db->trans_start();

            // Insertion du devis
            $this->db->insert('quotes_purchase', $quote_data);
            $quote_id = $this->db->insert_id();

            // Traitement des articles
            $quote_items = [];
            foreach ($categories as $index => $category_name) {
                // Vérifier si la catégorie existe
                $category = $this->db->where('item_category', $category_name)->get('item_category')->row();
                if (!$category) {
                    // Créer la catégorie si elle n'existe pas
                    $this->db->insert('item_category', ['item_category' => $category_name]);
                    $category_id = $this->db->insert_id();
                } else {
                    $category_id = $category->id;
                }

                // Vérifier si l'article existe
                $item = $this->db->where('name', $items[$index])
                                ->where('item_category_id', $category_id)
                                ->get('item')
                                ->row();
                
                if (!$item) {
                    // Créer l'article s'il n'existe pas
                    $item_data = [
                        'name' => $items[$index],
                        'item_category_id' => $category_id,
                        'unit' => $units[$index] ?? '',
                        'unit_price' => $prices[$index],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('item', $item_data);
                    $item_id = $this->db->insert_id();

                    // Ajouter l'article dans la table stock avec quantité 0
                    $stock_data = [
                        'item_id' => $item_id,
                        'initial_quantity' => 0,
                        'current_quantity' => 0,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('stock', $stock_data);
                } else {
                    $item_id = $item->id;
                }

                // Ajouter l'article au devis
                $quote_items[] = [
                    'quote_id' => $quote_id,
                    'category_id' => $category_id,
                    'item_id' => $item_id,
                    'quantity' => $quantities[$index],
                    'unit_price' => $prices[$index],
                    'unit' => $units[$index] ?? '',
                    'line_total' => $quantities[$index] * $prices[$index]
                ];
            }

            // Insertion des articles du devis
            if (!empty($quote_items)) {
                $this->db->insert_batch('quote_purchase_items', $quote_items);
            }

            // Fin de la transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                throw new Exception('Erreur lors de l\'enregistrement du devis');
            }

            $response['status'] = 'success';
            $response['message'] = 'Devis créé avec succès';
            $response['quote_id'] = $quote_id;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
    }



    /**
     * Génère un numéro unique pour une commande
     * Format: CMD-YYYYMM-XXXX où XXXX est un numéro séquentiel
     * 
     * @return string
     */
    private function generateQuoteNumber()
    {
        $prefix = 'DEV_ACH';  // DEV pour Devis
        $date = date('Ym');  // Format YYYYMM
        
        // Recherche le dernier numéro pour ce mois
        $this->db->like('quote_number', $prefix . '-' . $date, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('quotes_purchase');
        
        if ($query->num_rows() > 0) {
            // Extrait le numéro séquentiel de la dernière commande
            $last_ref = $query->row()->quote_number;
            $sequence = intval(substr($last_ref, -4)) + 1;
        } else {
            // Première commande du mois
            $sequence = 1;
        }
        
        // Formate le numéro séquentiel sur 4 chiffres
        $sequence_padded = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        
        return $prefix . '-' . $date . '-' . $sequence_padded;
    }



    /**
     * Affiche les détails d'un devis
     * 
     * @param int $id ID du devis
     * @return void
     */
    public function view($id)
    {
        // var_dump($id);
        // exit;

        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('item', 'can_view')) {
            access_denied();
        }

        // Récupération des données du devis
        $data['quote'] = $this->quote_purchase_model->getQuoteWithItems($id);

        // var_dump($data['quote']);
        // exit;

        // Vérification si le devis existe
        if (!$data['quote']) {
            $this->session->set_flashdata('error', 'Devis non trouvé');
            redirect('admin/quoteitem_purchase');
        }

        // Préparation des données pour la vue
        $data['title'] = 'Détails du devis';
        $data['page_title'] = 'Détails du devis ' . $data['quote']['quote_number'];

        // Chargement des vues
        $this->load->view('layout/header', $data);
        $this->load->view('admin/quote_purchase/view', $data);
        $this->load->view('layout/footer');
    }


    /**
     * Envoie le devis par email au client
     * 
     * @param int $quote_id ID du devis
     * @return void
     */
    public function sendEmail()
    {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Quoteitem', 'can_edit')) {
            access_denied();
        }

        $quote_id = $this->input->post('id', 0);

        // Initialisation de la réponse
        $response = ['status' => 'fail', 'message' => ''];

        try {
            // Récupération des données du devis
            $data['quote'] = $this->quote_purchase_model->getQuoteWithItems($quote_id);
            if (!$data['quote']) {
                throw new Exception('Devis introuvable');
            }

            // Vérification de l'email du client
            if (empty($data['quote']['customer_email'])) {
                throw new Exception('Le client n\'a pas d\'adresse email');
            }

            // Récupération des données de la société
            $company = $this->setting_model->get();

            // Récupération des données de l'entrepris
            $data['company'] = $company[0];
            $data['totalAsletter'] = $this->asLetters(floatval($data['quote']['total_ttc']));

            // Récupération des informations de l'utilisateur connecté
            $data['user'] = $this->customlib->getUserData();

            // var_dump($data);
            // exit;


            //===================
            if ($data['quote']) {

                $quote_detail = array(
                    'id'            => $data['quote']['id'], 
                    'data'          => $data, 
                    'credential_for'=> 'sendQuote', 
                    'supplier_name'       => $data['quote']['customer_name'].' '.$data['quote']['customer_last_name'],
                    'quotation_number'  => $data['quote']['quote_number'], 
                    'quotation_date'    => !empty($data['quote']['quote_date']) ? date('d/m/Y', strtotime($data['quote']['quote_date'])) :"N/A", 
                    'email'             => $data['quote']['customer_email']);

                $this->mailsmsconf->mailsms('send_quote', $quote_detail);
            }
            
            $response['status'] = 'success';
            $response['message'] = 'Le devis a été envoyé avec succès';

        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Quote Email Error: ' . $e->getMessage());
        }

        // Retourner la réponse en JSON
        echo json_encode($response);
    }

    /**
     * Affiche le formulaire d'édition d'un devis
     * 
     * @param int $id ID du devis
     * @return void
     */
    public function edit($id)
    {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Quoteitem', 'can_edit')) {
            access_denied();
        }

        try {
            // Vérifier le statut du devis
            $quote = $this->quote_purchase_model->getQuoteWithItems($id);
            if (!$quote) {
                $this->session->set_flashdata('error', 'Devis introuvable');
                redirect('admin/quoteitem_purchase');
            }

            if ((int)$quote['status'] !== 1) {
                $this->session->set_flashdata('error', 'Ce devis ne peut plus être modifié');
                redirect('admin/quoteitem_purchase');
            }

            // Préparation des données pour la vue
            $data = [
                'title' => 'Modifier le devis',
                'quote' => $quote,
                'itemcatlist' => $this->itemcategory_model->get(),
                'itemList' => $this->item_model->get(),
                'supplier' => $this->itemsupplier_model->get()
            ];

            // Chargement des vues
            $this->load->view('layout/header', $data);
            $this->load->view('admin/quote_purchase/edit', $data);
            $this->load->view('layout/footer', $data);
        } catch (Exception $e) {
            log_message('error', 'Quote Edit Error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Une erreur est survenue lors de l\'édition du devis');
            redirect('admin/quoteitem_purchase');
        }
    }



    /**
     * Met à jour un devis existant
     * 
     * @param int $id ID du devis
     * @return void
     */
    public function update()
    {
        // Initialisation de la réponse
        $response = ['status' => 'fail', 'message' => '', 'error' => []];

        try {
            // Vérification des données POST
            if (!$this->input->post()) {
                throw new Exception('Aucune donnée reçue');
            }

            // Validation des champs obligatoires
            $this->form_validation->set_rules('customer', 'Supplier', 'required|trim');
            $this->form_validation->set_rules('quote_date', 'Date', 'required');
            $this->form_validation->set_rules('item_category[]', 'Catégorie', 'required');
            $this->form_validation->set_rules('item_name[]', 'Article', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantité', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('price[]', 'Prix unitaire', 'required|numeric|greater_than[0]');

            if ($this->form_validation->run() == false) {
                $response['error'] = $this->form_validation->error_array();
                echo json_encode($response);
                return;
            }

            // Récupération des données
            $quote_id = $this->input->post('id');
            $categories = $this->input->post('item_category');
            $items = $this->input->post('item_name');
            $quantities = $this->input->post('quantity');
            $prices = $this->input->post('price');
            $units = $this->input->post('unit');

            if (!is_array($categories) || !is_array($items) || !is_array($quantities) || !is_array($prices)) {
                throw new Exception('Format de données invalide');
            }

            // Préparation des données du devis
            $quote_data = [
                'supplier_id' => $this->input->post('customer'),
                'quote_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('quote_date')))),
                'valid_until' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('valid_until')))),
                'payment_terms' => $this->input->post('payment_terms'),
                'delivery_terms' => $this->input->post('delivery_terms'),
                'delivery_location' => $this->input->post('delivery_location'),
                'apply_tva' => $this->input->post('apply_tva') ? 1 : 0,
                'tva_rate' => $this->input->post('tva_rate'),
                'tva_amount' => $this->input->post('tva_amount'),
                'total_ht' => $this->input->post('total_ht'),
                'total_ttc' => $this->input->post('total_ttc'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Début de la transaction
            $this->db->trans_start();

            // Mise à jour du devis
            $this->db->where('id', $quote_id)->update('quotes_purchase', $quote_data);

            // Suppression des anciens articles
            $this->db->where('quote_id', $quote_id)->delete('quote_purchase_items');

            // Traitement des articles
            $quote_items = [];
            foreach ($categories as $index => $category_name) {
                // Vérifier si la catégorie existe
                $category = $this->db->where('item_category', $category_name)->get('item_category')->row();
                if (!$category) {
                    // Créer la catégorie si elle n'existe pas
                    $this->db->insert('item_category', ['item_category' => $category_name]);
                    $category_id = $this->db->insert_id();
                } else {
                    $category_id = $category->id;
                }

                // Vérifier si l'article existe
                $item = $this->db->where('name', $items[$index])
                                ->where('item_category_id', $category_id)
                                ->get('item')
                                ->row();
                
                if (!$item) {
                    // Créer l'article s'il n'existe pas
                    $item_data = [
                        'name' => $items[$index],
                        'item_category_id' => $category_id,
                        'unit' => $units[$index] ?? '',
                        'unit_price' => $prices[$index],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('item', $item_data);
                    $item_id = $this->db->insert_id();
                } else {
                    $item_id = $item->id;
                }

                // Ajouter l'article au devis
                $quote_items[] = [
                    'quote_id' => $quote_id,
                    'category_id' => $category_id,
                    'item_id' => $item_id,
                    'quantity' => $quantities[$index],
                    'unit_price' => $prices[$index],
                    'unit' => $units[$index] ?? '',
                    'line_total' => $quantities[$index] * $prices[$index]
                ];
            }

            // Insertion des articles du devis
            if (!empty($quote_items)) {
                $this->db->insert_batch('quote_purchase_items', $quote_items);
            }

            // Fin de la transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                throw new Exception('Erreur lors de la mise à jour du devis');
            }

            $response['status'] = 'success';
            $response['message'] = 'Devis mis à jour avec succès';
            $response['quote_id'] = $quote_id;

        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
    }

    /**
     * Crée un bon de commande à partir d'un devis validé
     * 
     * @param int $quote_id ID du devis
     * @return bool
     */
    private function createDeliveryFromOrder($quote_id, $order_id)
    {
        // Récupération des données de la commande
        $quote = $this->quote_purchase_model->getQuoteWithItems($quote_id);
        if (!$quote) {
            return false;
        }

        // Génération du numéro de livraison
        $delivery_number = $this->generateDeliveryNumber();

        // Préparation des données de la livraison
        $delivery_data = [
            'delivery_number'   => $delivery_number,
            'supplier_id'       => $quote['supplier_id'],
            'order_id'          => $order_id,
            'delivery_date'     => date('Y-m-d'),
            'shipping_method'   => $quote['payment_terms'],
            'tracking_number'   => $quote['delivery_terms'],
            'designation'   => $quote['designation'],
            'apply_tva'     => $quote['apply_tva'],
            'tva_rate'      => $quote['tva_rate'],
            'tva_amount'    => $quote['tva_amount'],
            'total_ht'      => $quote['total_ht'],
            'total_ttc'     => $quote['total_ttc'],
            'deadline'          => $quote['valid_until'],
            'delivery_address'  => $quote['delivery_location'],
            'notes'            => $quote['notes'] ?? '',
            'status'           => Delivery_model::STATUS_PENDING, // 1 = En préparation
            'created_at'       => date('Y-m-d H:i:s'),
        ];

        try {
            // Insertion de la livraison
            $this->db->insert('deliveries', $delivery_data);
            $delivery_id = $this->db->insert_id();

            if (!$delivery_id) {
                throw new Exception('Erreur lors de la création de la livraison');
            }

            // Insertion des articles de la livraison
            foreach ($quote['items'] as $item) {
                $delivery_item_data = [
                    'delivery_id'        => $delivery_id,
                    'category_id'        => $item['category_id'],
                    'item_id'           => $item['item_id'],
                    'quantity'          => $item['quantity'],
                    'delivered_quantity' => 0,
                    'unit_price'        => $item['unit_price'],
                    'line_total'        => $item['line_total'],
                    'position'          => $item['position'] ?? 0
                ];

                if (!$this->db->insert('delivery_items', $delivery_item_data)) {
                    throw new Exception('Erreur lors de l\'ajout d\'un article à la livraison');
                }
            }

            return true;
        } catch (Exception $e) {
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
    private function generateDeliveryNumber()
    {
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
     * Crée un bon de commande à partir d'un devis validé
     * 
     * @param int $quote_id ID du devis
     * @return bool
     */
    private function createOrderFromQuote($quote_id, $order_number)
    {
        // Récupération des données du devis
        $quote = $this->quote_purchase_model->getQuoteWithItems($quote_id);
        if (!$quote) {
            return false;
        }

        // Préparation des données de la commande
        $order_data = [
            'order_number'  => $order_number,
            'quote_id'      => $quote_id,
            'supplier_id'   => $quote['supplier_id'],
            'order_date'    => date('Y-m-d'),
            'valid_until'   => $quote['valid_until'],
            'apply_tva'     => $quote['apply_tva'],
            'designation'   => $quote['designation'],
            'payment_terms'     => $quote['payment_terms'],
            'delivery_terms'    => $quote['delivery_terms'],
            'delivery_location' => $quote['delivery_location'],
            'tva_rate'      => $quote['tva_rate'],
            'tva_amount'    => $quote['tva_amount'],
            'total_ht'      => $quote['total_ht'],
            'total_ttc'     => $quote['total_ttc'],
            'notes'         => $quote['notes'],
            'status'        => Quote_purchase_model::STATUS_IN_PROGRESS, // 1 = En attente
            'created_at'    => date('Y-m-d H:i:s'),
        ];

        // Début de la transaction
        $this->db->trans_start();

        try {
            // Insertion de la commande
            $this->db->insert('orders', $order_data);
            $order_id = $this->db->insert_id();

            if (!$order_id) {
                throw new Exception('Erreur lors de la création de la commande');
            }

            // Insertion des articles de la commande
            foreach ($quote['items'] as $item) {
                $order_item_data = [
                    'order_id'      => $order_id,
                    'category_id'   => $item['category_id'],
                    'item_id'       => $item['item_id'],
                    'quantity'      => $item['quantity'],
                    'unit_price'    => $item['unit_price'],
                    'line_total'    => $item['line_total'],
                    'position'      => $item['position']
                ];

                if (!$this->db->insert('order_items', $order_item_data)) {
                    throw new Exception('Erreur lors de l\'ajout d\'un article à la commande');
                }
            }

            // Création du bon de livraison
            if (!$this->createDeliveryFromOrder($quote_id, $order_id)) {
                throw new Exception('Erreur lors de la création du bon de livraison');
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Erreur lors de la transaction');
            }

            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Order Creation Error: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Valide un devis
     * 
     * @param int $id ID du devis
     * @return void
     */
    public function validate()
    {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Quoteitem', 'can_edit')) {
            access_denied();
        }

        $id = $this->input->post('id');
        $order_number = $this->input->post('order_number');

        // var_dump($this->input->post());
        // exit;

        $response = ['status' => 'fail', 'message' => ''];

        // $this->createDeliveryFromOrder($id, 1);
        // exit;

        try {
            // Vérification que le devis existe
            $quote = $this->quote_purchase_model->getQuoteWithItems($id);
            if (!$quote) {
                throw new Exception('Devis non trouvé');
            }

            // Vérification que le devis n'est pas déjà validé ou rejeté
            if ($this->quote_purchase_model->isQuoteValidated($id)) {
                throw new Exception('Ce devis est déjà validé');
            }
            if ($this->quote_purchase_model->isQuoteRejected($id)) {
                throw new Exception('Ce devis a été rejeté');
            }

            // Validation du devis
            $data = [
                'status' => Quote_purchase_model::STATUS_VALIDATED,
                'validated_at' => date('Y-m-d H:i:s'),
            ];

            if (!$this->quote_purchase_model->validateQuote($id, $data)) {
                throw new Exception('Erreur lors de la validation du devis');
            }

            $response['status'] = 'success';
            $response['message'] = 'Devis validé avec succès';

            // Créer la commande à partir du devis
            $this->createOrderFromQuote($id, $order_number);

            // Créer la sortie de stock
            $this->createStockRemovalFromQuote($id);
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            log_message('error', 'Quote Validation Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

         
    /**
     * Crée une sortie de stock à partir d'un devis validé
     * 
     * @param int $quote_id ID du devis
     * @return void
     */
    private function createStockRemovalFromQuote($quote_id)
    {
        // Récupérer les informations du devis
        $quote = $this->quote_purchase_model->getQuoteWithItems($quote_id);
        // var_dump($quote['items']);
        // var_dump($quote);
        // exit;
        if (!$quote) {
            throw new Exception('Devis non trouvé pour la sortie de stock');
        }

        // Calculer le total du devis
        $grand_total = 0;
        foreach ($quote['items'] as $item) {
            $grand_total += $item['quantity'] * $item['unit_price'];
        }

        // Préparer les données pour la sortie de stock
        $stock_removal_data = [
            'reference'     => 'SR-' . date('Ymd') . '-' . str_pad($quote_id, 5, '0', STR_PAD_LEFT),
            'origin'        => 'Devis #' . $quote['quote_number'],
            'issue_date'    => date('Y-m-d'),
            'grand_total'   => $grand_total,
            'reason'        => $quote['designation'],
            'created_at'    => date('Y-m-d H:i:s')
        ];

        // Insérer la sortie de stock principale
        $this->db->trans_start();

        $this->db->insert('stock_removals', $stock_removal_data);
        $removal_id = $this->db->insert_id();

        // Insérer les articles de la sortie de stock
        foreach ($quote['items'] as $item) {
            $removal_item = [
                'stock_removal_id' => $removal_id,
                'category_id' => $item['category_id'],
                'item_id' => $item['item_id'],
                'unit' => $item['unit'],
                'quantity' => $item['quantity'],
                'price' => $item['unit_price'],
                'line_total' => $item['quantity'] * $item['unit_price'],
            ];

            $this->db->insert('stock_removal_items', $removal_item);

            // Mettre à jour le stock (supposons que vous avez une table 'stock' avec les champs item_id et quantity)
            $this->db->set('initial_quantity', 'GREATEST(initial_quantity - ' . $item['quantity'] . ', 0)', FALSE)
                ->set(
                    'current_quantity',
                    'CASE WHEN current_quantity >= ' . $item['quantity'] . ' 
                        THEN current_quantity - ' . $item['quantity'] . ' 
                        ELSE 0 END',
                    FALSE
                )
                ->where('item_id', $item['item_id'])
                ->update('stock');
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            throw new Exception('Erreur lors de la création de la sortie de stock');
        }
    }



    /**
     * Rejette un devis
     * 
     * @param int $id ID du devis
     * @return void
     */
    public function reject()
    {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('Quoteitem', 'can_edit')) {
            access_denied();
        }

        $id = $this->input->post('id');

        // var_dump($this->input->post());
        // exit;

        $response = ['status' => 'fail', 'message' => ''];

        try {
            // Vérification que le devis existe
            $quote = $this->quote_purchase_model->getQuoteWithItems($id);
            if (!$quote) {
                throw new Exception('Devis non trouvé');
            }

            // Vérification que le devis n'est pas déjà validé ou rejeté
            if ($this->quote_purchase_model->isQuoteValidated($id)) {
                throw new Exception('Ce devis est déjà validé');
            }
            if ($this->quote_purchase_model->isQuoteRejected($id)) {
                throw new Exception('Ce devis a déjà été rejeté');
            }

            // Récupération du motif de rejet
            $reason = $this->input->post('reason');
            if (empty($reason)) {
                throw new Exception('Le motif de rejet est requis');
            }

            // Rejet du devis
            $data = [
                'status'        => Quote_purchase_model::STATUS_REJECTED,
                'rejected_at'   => date('Y-m-d H:i:s'),
                'notes'         => $reason
            ];

            // var_dump($data);
            // exit;

            if (!$this->quote_purchase_model->rejectQuote($id, $data)) {
                throw new Exception('Erreur lors du rejet du devis');
            }

            $response['status'] = 'success';
            $response['message'] = 'Devis rejeté avec succès';
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            log_message('error', 'Quote Rejection Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }


    /**
     * Affiche la page d'impression d'un devis
     * 
     * @param int $id ID de la factures
     */
    public function print()
    {

        $id = $this->input->post('id');

        // Récupération des données de la facture
        $data['quote'] = $this->quote_purchase_model->getQuoteWithItems($id);

        if (!$data['quote']) {
            show_404();
            return;
        }

        // Récupération des données de la société
        $company = $this->setting_model->get();

        // Récupération des données de l'entreprise
        $data['company'] = $company[0];
        $data['totalAsletter'] = $this->asLetters(floatval($data['quote']['total_ttc']));

        // Récupération des informations de l'utilisateur connecté
        $data['user'] = $this->customlib->getUserData();
        // var_dump($userdata);
        // exit;

        // Chargement de la vue d'impression
        $invoice_page = $this->load->view('admin/quote_purchase/print', $data, true);
        $array = array('status' => '1', 'error' => '', 'page' => $invoice_page);
        echo json_encode($array);
    }


    //------------------------------------
    // AJOUT D'UN NOUVEL ORDRE DE VIREMENT
    //------------------------------------
    public function asLetters($number)
    {

        $convert = explode('.', $number);
        $num[17] = array(
            'zero',
            'un',
            'deux',
            'trois',
            'quatre',
            'cinq',
            'six',
            'sept',
            'huit',
            'neuf',
            'dix',
            'onze',
            'douze',
            'treize',
            'quatorze',
            'quinze',
            'seize'
        );

        $num[100] = array(
            20 => 'vingt',
            30 => 'trente',
            40 => 'quarante',
            50 => 'cinquante',
            60 => 'soixante',
            70 => 'soixante-dix',
            80 => 'quatre-vingt',
            90 => 'quatre-vingt-dix'
        );

        if (isset($convert[1]) && $convert[1] != '') {
            return self::asLetters($convert[0]) . ' et ' . self::asLetters($convert[1]);
        }
        if ($number < 0) return 'moins ' . self::asLetters(-$number);
        if ($number < 17) {
            return $num[17][$number];
        } elseif ($number < 20) {
            return 'dix-' . self::asLetters($number - 10);
        } elseif ($number < 100) {
            if ($number % 10 == 0) {
                return $num[100][$number];
            } elseif (substr($number, -1) == 1) {
                if (((int)($number / 10) * 10) < 70) {
                    return self::asLetters((int)($number / 10) * 10) . '-et-un';
                } elseif ($number == 71) {
                    return 'soixante-et-onze';
                } elseif ($number == 81) {
                    return 'quatre-vingt-un';
                } elseif ($number == 91) {
                    return 'quatre-vingt-onze';
                }
            } elseif ($number < 70) {
                return self::asLetters($number - $number % 10) . '-' . self::asLetters($number % 10);
            } elseif ($number < 80) {
                return self::asLetters(60) . '-' . self::asLetters($number % 20);
            } else {
                return self::asLetters(80) . '-' . self::asLetters($number % 20);
            }
        } elseif ($number == 100) {
            return 'cent';
        } elseif ($number < 200) {
            return self::asLetters(100) . ' ' . self::asLetters($number % 100);
        } elseif ($number < 1000) {
            return self::asLetters((int)($number / 100)) . ' ' . self::asLetters(100) . ($number % 100 > 0 ? ' ' . self::asLetters($number % 100) : '');
        } elseif ($number == 1000) {
            return 'mille';
        } elseif ($number < 2000) {
            return self::asLetters(1000) . ' ' . self::asLetters($number % 1000) . ' ';
        } elseif ($number < 1000000) {
            return self::asLetters((int)($number / 1000)) . ' ' . self::asLetters(1000) . ($number % 1000 > 0 ? ' ' . self::asLetters($number % 1000) : '');
        } elseif ($number == 1000000) {
            return 'millions';
        } elseif ($number < 2000000) {
            return self::asLetters(1000000) . ' ' . self::asLetters($number % 1000000);
        } elseif ($number < 1000000000) {
            return self::asLetters((int)($number / 1000000)) . ' ' . self::asLetters(1000000) . ($number % 1000000 > 0 ? ' ' . self::asLetters($number % 1000000) : '');
        }
    }
    // -----------------------------------


    /**
     * Génère un PDF du devis avec mPDF
     * 
     * @param int $id ID du devis
     * @return void
     */
    // public function printWithMPDF($id)
    // {   
    //     // Vérification des permissions
    //     if (!$this->rbac->hasPrivilege('Quoteitem', 'can_edit')) {
    //         access_denied();
    //     }

    //     try {
    //         // Récupération des données du devis
    //         $data['quote'] = $this->quote_model->getQuoteWithItems($id);

    //         if (!$data['quote']) {
    //             show_404();
    //             return;
    //         }

    //         // Récupération des données de la société
    //         $company = $this->setting_model->get();

    //         // Récupération des données de l'entreprise
    //         $data['company'] = $company[0];
    //         $data['totalAsletter'] = $this->asLetters(floatval($data['quote']['total_ttc']));

    //         // Récupération des informations de l'utilisateur connecté
    //         $data['user'] = $this->customlib->getUserData();

    //         // Charger la bibliothèque mPDF
    //         require_once FCPATH . 'vendor/autoload.php';

    //         // Configuration de mPDF avec des paramètres optimisés
    //         $config = [
    //             'mode' => 'utf-8',
    //             'format' => 'A4',
    //             'margin_left' => 15,
    //             'margin_right' => 15,
    //             'margin_top' => 15,
    //             'margin_bottom' => 15,
    //             'margin_header' => 9,
    //             'margin_footer' => 9,
    //             'default_font' => 'dejavusans',
    //             'autoPageBreak' => true,
    //             'autoScriptToLang' => true,
    //             'autoLangToFont' => true,
    //             'compress' => true,
    //             'keepColumns' => true,
    //             'keep_table_proportions' => true,
    //             'shrink_tables_to_fit' => 1,
    //             'showImageErrors' => true,
    //             'debug' => false
    //         ];

    //         $mpdf = new \Mpdf\Mpdf($config);
            
    //         // Définir les informations du document
    //         $mpdf->SetTitle('Devis ' . $data['quote']['quote_number']);
    //         $mpdf->SetAuthor($data['company']['name']);

    //         // Charger la vue
    //         $html = $this->load->view('admin/quote/printWithMpdf', $data, true);
            
    //         // Générer le PDF
    //         $mpdf->WriteHTML($html);
            
    //         // Créer le dossier uploads/quotes s'il n'existe pas
    //         $upload_dir = FCPATH . 'uploads/quotes';
    //         if (!file_exists($upload_dir)) {
    //             mkdir($upload_dir, 0777, true);
    //         }
            
    //         // Générer le nom du fichier
    //         $filename = 'Devis_' . $data['quote']['quote_number'] . '_' . date('Y-m-d') . '.pdf';
    //         $filepath = $upload_dir . '/' . $filename;
            
    //         // Sauvegarder le PDF
    //         $mpdf->Output($filepath, 'F');

    //         // Retourner le chemin du fichier
    //         $response = [
    //             'status' => 'success',
    //             'message' => 'PDF généré avec succès',
    //             'filepath' => base_url('uploads/quotes/' . $filename)
    //         ];

    //         echo json_encode($response);

    //     } catch (Exception $e) {
    //         log_message('error', 'Quote PDF Generation Error: ' . $e->getMessage());
    //         $response = [
    //             'status' => 'error',
    //             'message' => 'Une erreur est survenue lors de la génération du PDF'
    //         ];
    //         echo json_encode($response);
    //     }
    // }

    /**
     * Vérifie si une catégorie existe
     * @return JSON
     */
    public function checkCategory()
    {
        $category_name = $this->input->post('category_name');
        $category = $this->itemcategory_model->getCategoryByName($category_name);
        
        echo json_encode([
            'exists' => !empty($category),
            'category' => $category
        ]);
    }

    /**
     * Récupère les produits d'une catégorie
     * @return JSON
     */
    public function getItemsByCategory()
    {
        $category_name = $this->input->post('category_name');
        $category = $this->itemcategory_model->getCategoryByName($category_name);
        
        if ($category) {
            // $items = $this->item_model->getItemByCategory($category['id']);
            $items = $this->stock_model->getItemByCategory($category['id']);

            echo json_encode($items);
        } else {
            echo json_encode([]);
        }
    }

}
