<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Proforma extends Admin_Controller
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

        $this->load->model('proforma_model');
        $this->load->model('itemcategory_model');
        $this->load->model('item_model');
        $this->load->model('clients_model');
        $this->load->model('stock_model');

        $this->load->library('customlib');

        // Charger la session pour les préférences de filtre
        $this->load->library('session');
    }


    /**
     * Main index method - Handles item listing and creation
     */

    function index()
    {
        if (!$this->rbac->hasPrivilege('devis', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Proforma/index');

        $data = [
            'title' => 'Add Item',
            'title_list' => 'Recent Items',
            'clients' => $this->clients_model->get(), // <-- Ajout de la liste des clients
        ];

        $this->load->view('layout/header', $data);
        $this->load->view('admin/proforma/list', $data);
        $this->load->view('layout/footer', $data);
    }



    /**
     * GET ITEM BY CATEGORY
     *
     * @return  JSON   $data
     */
    public function getItemByCategory()
    {
        $item_category_id = $this->input->get('item_category_id');
        $data             = $this->stock_model->getItemByCategory($item_category_id);
        echo json_encode($data);
    }


    /**
     * GET STOCK ENTRY DATA - With user filtering
     * IN JSON FORMAT
     *
     * @return  JSON   $response
     */

    public function data()
    {
        // Récupère l'utilisateur connecté
        $current_user = $this->session->userdata('admin')['username'];

        // Récupérer le mode de filtre depuis la session ou la requête
        $filter_mode = $this->input->post('filter_mode');
        if ($filter_mode) {
            $this->session->set_userdata('quote_filter_mode', $filter_mode);
        } else {
            $filter_mode = $this->session->userdata('quote_filter_mode') ?: 'my'; // Par défaut 'my'
        }

        // Vérifier les privilèges RBAC
        $is_superadmin = $this->rbac->hasPrivilege('superadmin');
        $is_admin = $this->rbac->hasPrivilege('admin');
        $is_admin_user = ($is_superadmin || $is_admin);

        // Si c'est un admin et que le filtre est 'all', on voit tout
        if ($is_admin_user && $filter_mode === 'all') {
            // Admin voit tout
            $result = $this->proforma_model->getListDataForAdmin();
        } else {
            // Utilisateur normal ou admin en mode 'my' - filtrer par son nom
            $result = $this->proforma_model->getListDataForUser($current_user);
        }

        echo $result;
    }


    /**
     * Ajoute un nouveau devis (produits + services)
     */
    public function add()
    {
        if (!$this->rbac->hasPrivilege('devis', 'can_view')) {
            access_denied();
        }

        $response = ['status' => 'fail', 'message' => '', 'error' => []];

        try {
            if (!$this->input->post()) {
                throw new Exception('Aucune donnée reçue');
            }

            // Validation
            $this->form_validation->set_rules('customer', 'Client', 'required|trim');
            $this->form_validation->set_rules('quote_date', 'Date', 'required');
            $this->form_validation->set_rules('item_type[]', 'Type (produit/service)', 'required');
            $this->form_validation->set_rules('item_name[]', 'Article/Service', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantité', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('price[]', 'Prix unitaire', 'required|numeric|greater_than[0]');

            // Validation conditionnelle : la catégorie est requise seulement pour les produits
            $item_types = $this->input->post('item_type');
            foreach ($item_types as $index => $type) {
                if ($type === 'product') {
                    $this->form_validation->set_rules("item_category[$index]", 'Catégorie', 'required');
                }
            }

            // Validation taxe personnalisée
            if ($this->input->post('tax_option') === 'other') {
                $this->form_validation->set_rules('other_tax_name', 'Nom de la taxe', 'required|trim');
                $this->form_validation->set_rules('other_tax_rate', 'Taux de taxe', 'required|numeric|greater_than[0]');
            }

            if ($this->form_validation->run() == false) {
                $response['error'] = $this->form_validation->error_array();
                echo json_encode($response);
                return;
            }

            // Client
            $customer_id = $this->input->post('customer');
            if ($customer_id === "new") {
                $customer_id = $this->createNewClient();
            }

            // Récupération des champs répétés
            $item_types    = $this->input->post('item_type');
            $categories    = $this->input->post('item_category'); // seulement pour produits
            $items         = $this->input->post('item_name');
            $quantities    = $this->input->post('quantity');
            $prices        = $this->input->post('price');
            $units         = $this->input->post('unit');
            $discounts     = $this->input->post('discount') ?: [];
            $discount_types = $this->input->post('discount_type') ?: [];

            // Calcul des totaux (avec remises globales et taxes)
            $totals = $this->calculateTotalsWithBossFormula($quantities, $prices, $discounts, $discount_types);

            // Gestion des taxes
            $tax_option = $this->input->post('tax_option');
            $tva_rate = 0;
            $tva_amount = 0;
            $other_tax_name = '';
            $other_tax_rate = 0;
            $other_tax_amount = 0;

            switch ($tax_option) {
                case 'tva':
                    $tva_rate = $this->input->post('tva_rate') ?: 18;
                    $tva_amount = $totals['total_after_discount'] * ($tva_rate / 100);
                    break;
                case 'other':
                    $other_tax_name = $this->input->post('other_tax_name');
                    $other_tax_rate = $this->input->post('other_tax_rate');
                    $other_tax_amount = $totals['total_after_discount'] * ($other_tax_rate / 100);
                    break;
            }

            $total_ttc = $totals['total_after_discount'] + $tva_amount + $other_tax_amount;

            // Enregistrement du devis
            $quote_data = [
                'quote_number'          => $this->generateQuoteNumber(),
                'customer_id'           => $customer_id,
                'user_name'             => $this->input->post('user_name'),
                'objet'                 => $this->input->post('objet'),
                'payment_method'        => $this->input->post('payment_method'),
                'quote_date'            => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('quote_date')))),
                'valid_until'           => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('valid_until')))),
                'payment_terms'         => $this->input->post('payment_terms'),
                'delivery_terms'        => $this->input->post('delivery_terms'),
                'delivery_location'     => $this->input->post('delivery_location'),
                'tax_option'            => $tax_option,
                'tva_rate'              => $tva_rate,
                'tva_amount'            => $tva_amount,
                'other_tax_name'        => $other_tax_name,
                'other_tax_rate'        => $other_tax_rate,
                'other_tax_amount'      => $other_tax_amount,
                'total_ht'              => $totals['total_ht'],
                'total_discount'        => $totals['total_discount'],
                'total_after_discount'  => $totals['total_after_discount'],
                'total_tax_amount'      => $tva_amount + $other_tax_amount,
                'total_ttc'             => $total_ttc,
                'global_discount_type'  => $this->input->post('global_discount_type'),
                'global_discount_amount'=> $this->input->post('global_discount_amount') ?: 0,
                'status'                => 2,
                'created_at'            => date('Y-m-d H:i:s')
            ];

            $this->db->trans_start();
            $this->db->insert('proforma', $quote_data);
            $quote_id = $this->db->insert_id();

            // Préparation et insertion des lignes (produits ou services)
            $quote_items = $this->prepareQuoteItemsWithBossFormula(
                $quote_id, $item_types, $categories, $items, $quantities, $prices, $units, $discounts, $discount_types
            );
            if (!empty($quote_items)) {
                $this->db->insert_batch('proforma_items', $quote_items);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                throw new Exception('Erreur lors de l\'enregistrement du proforma');
            }

            $response['status'] = 'success';
            $response['message'] = 'Proforma créé avec succès';
            $response['quote_id'] = $quote_id;
            $response['redirect_url'] = base_url('admin/proforma');

        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
    }

    /**
     * Met à jour un devis existant (produits + services)
     */
    public function update()
    {
        $response = ['status' => 'fail', 'message' => '', 'error' => []];

        try {
            if (!$this->input->post()) {
                throw new Exception('Aucune donnée reçue');
            }

            $quote_id = $this->input->post('id');

            // Vérifier l'accès au devis
            $quote = $this->proforma_model->getQuoteWithItems($quote_id);
            if (!$quote) {
                throw new Exception('Proforma introuvable');
            }
            // (contrôle d'accès utilisateur déjà présent dans votre code)

            // Validation (identique à add)
            $this->form_validation->set_rules('customer', 'Client', 'required|trim');
            $this->form_validation->set_rules('quote_date', 'Date', 'required');
            $this->form_validation->set_rules('item_type[]', 'Type (produit/service)', 'required');
            $this->form_validation->set_rules('item_name[]', 'Article/Service', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantité', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('price[]', 'Prix unitaire', 'required|numeric|greater_than[0]');

            $item_types = $this->input->post('item_type');
            foreach ($item_types as $index => $type) {
                if ($type === 'product') {
                    $this->form_validation->set_rules("item_category[$index]", 'Catégorie', 'required');
                }
            }

            if ($this->input->post('tax_option') === 'other') {
                $this->form_validation->set_rules('other_tax_name', 'Nom de la taxe', 'required|trim');
                $this->form_validation->set_rules('other_tax_rate', 'Taux de taxe', 'required|numeric|greater_than[0]');
            }

            if ($this->form_validation->run() == false) {
                $response['error'] = $this->form_validation->error_array();
                echo json_encode($response);
                return;
            }

            // Client
            $customer_id = $this->input->post('customer');
            if ($customer_id === "new") {
                $customer_id = $this->createNewClient();
            }

            // Données des lignes
            $item_types    = $this->input->post('item_type');
            $categories    = $this->input->post('item_category');
            $items         = $this->input->post('item_name');
            $quantities    = $this->input->post('quantity');
            $prices        = $this->input->post('price');
            $units         = $this->input->post('unit');
            $discounts     = $this->input->post('discount') ?: [];
            $discount_types = $this->input->post('discount_type') ?: [];

            // Calcul des totaux (remises globales incluses)
            $totals = $this->calculateTotalsWithBossFormula($quantities, $prices, $discounts, $discount_types);

            // Taxes
            $tax_option = $this->input->post('tax_option');
            $tva_rate = 0;
            $tva_amount = 0;
            $other_tax_name = '';
            $other_tax_rate = 0;
            $other_tax_amount = 0;

            switch ($tax_option) {
                case 'tva':
                    $tva_rate = $this->input->post('tva_rate') ?: 18;
                    $tva_amount = $totals['total_after_discount'] * ($tva_rate / 100);
                    break;
                case 'other':
                    $other_tax_name = $this->input->post('other_tax_name');
                    $other_tax_rate = $this->input->post('other_tax_rate');
                    $other_tax_amount = $totals['total_after_discount'] * ($other_tax_rate / 100);
                    break;
            }

            $total_ttc = $totals['total_after_discount'] + $tva_amount + $other_tax_amount;

            // Mise à jour du devis
            $quote_data = [
                'customer_id'           => $customer_id,
                'user_name'             => $this->input->post('user_name'),
                'objet'                 => $this->input->post('objet'),
                'payment_method'        => $this->input->post('payment_method'),
                'quote_date'            => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('quote_date')))),
                'valid_until'           => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('valid_until')))),
                'payment_terms'         => $this->input->post('payment_terms'),
                'delivery_terms'        => $this->input->post('delivery_terms'),
                'delivery_location'     => $this->input->post('delivery_location'),
                'tax_option'            => $tax_option,
                'tva_rate'              => $tva_rate,
                'tva_amount'            => $tva_amount,
                'other_tax_name'        => $other_tax_name,
                'other_tax_rate'        => $other_tax_rate,
                'other_tax_amount'      => $other_tax_amount,
                'total_ht'              => $totals['total_ht'],
                'total_discount'        => $totals['total_discount'],
                'total_after_discount'  => $totals['total_after_discount'],
                'total_tax_amount'      => $tva_amount + $other_tax_amount,
                'total_ttc'             => $total_ttc,
                'global_discount_type'  => $this->input->post('global_discount_type'),
                'global_discount_amount'=> $this->input->post('global_discount_amount') ?: 0,
                'updated_at'            => date('Y-m-d H:i:s')
            ];

            $this->db->trans_start();
            $this->db->where('id', $quote_id)->update('proforma', $quote_data);

            // Supprimer les anciennes lignes
            $this->db->where('quote_id', $quote_id)->delete('proforma_items');

            // Insérer les nouvelles lignes
            $quote_items = $this->prepareQuoteItemsWithBossFormula(
                $quote_id, $item_types, $categories, $items, $quantities, $prices, $units, $discounts, $discount_types
            );
            if (!empty($quote_items)) {
                $this->db->insert_batch('proforma_items', $quote_items);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                throw new Exception('Erreur lors de la mise à jour du proforma');
            }

            $response['status'] = 'success';
            $response['message'] = 'Proforma mis à jour avec succès';
            $response['quote_id'] = $quote_id;
            $response['redirect_url'] = base_url('admin/proforma');

        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
    }

// ======================== FONCTIONS PRIVÉES ========================

    /**
     * Prépare les lignes du devis (produits ou services)
     * @param int $quote_id
     * @param array $item_types
     * @param array $categories
     * @param array $items
     * @param array $quantities
     * @param array $prices
     * @param array $units
     * @param array $discounts
     * @param array $discount_types
     * @return array
     */
    private function prepareQuoteItemsWithBossFormula($quote_id, $item_types, $categories, $items, $quantities, $prices, $units, $discounts, $discount_types)
    {
        $quote_items = [];

        foreach ($items as $i => $item_name) {
            $type = $item_types[$i];
            $category_id = null;
            $item_id = null;
            $service_id = null;

            if ($type === 'product') {
                $category_name = $categories[$i] ?? '';
                if (empty($category_name)) {
                    continue; // sécurité
                }
                $category_id = $this->getOrCreateCategory($category_name);
                $item_id = $this->getOrCreateItem($item_name, $category_id, $units[$i] ?? '', $prices[$i]);
            } else {
                // Service : on récupère ou crée le service
                $service = $this->db->where('name', $item_name)->get('services')->row();
                if (!$service) {
                    $this->db->insert('services', [
                        'name'        => $item_name,
                        'unit_price'  => $prices[$i],
                        'duration'    => $units[$i] ?? null,
                        'created_at'  => date('Y-m-d H:i:s')
                    ]);
                    $service_id = $this->db->insert_id();
                } else {
                    $service_id = $service->id;
                }
            }

            $qty = floatval($quantities[$i]);
            $price = floatval($prices[$i]);
            $discount = floatval($discounts[$i] ?? 0);
            $discount_type = $discount_types[$i] ?? 'percent';

            $discount_amount_unit = ($discount_type === 'percent') ? $price * ($discount / 100) : min($discount, $price);
            $unit_price_net = $price - $discount_amount_unit;
            $line_total_ht = $price * $qty;
            $line_discount_total = $discount_amount_unit * $qty;
            $line_total_after_discount = $unit_price_net * $qty;

            $quote_items[] = [
                'quote_id'                  => $quote_id,
                'category_id'               => $category_id,
                'item_id'                   => $item_id,
                'service_id'                => $service_id,
                'item_type'                 => $type,
                'category_name'             => ($type === 'product') ? ($categories[$i] ?? null) : null,
                'item_name'                 => $item_name,
                'quantity'                  => $qty,
                'unit_price'                => $price,
                'unit'                      => $units[$i] ?? '',
                'discount_type'             => $discount_type,
                'discount'                  => $discount,
                'discount_amount'           => $line_discount_total,
                'line_total'                => $line_total_ht,
                'line_total_after_discount' => $line_total_after_discount,
                'created_at'                => date('Y-m-d H:i:s')
            ];
        }

        return $quote_items;
    }

    /**
     * Calcule les totaux (HT, remises, net, taxes) selon la formule
     */
    private function calculateTotalsWithBossFormula($quantities, $prices, $discounts, $discount_types)
    {
        $total_ht = 0;
        $total_discount = 0;
        $total_after_discount = 0;

        foreach ($quantities as $i => $qty) {
            $price = floatval($prices[$i]);
            $discount = floatval($discounts[$i] ?? 0);
            $discount_type = $discount_types[$i] ?? 'percent';

            $discount_amount_unit = ($discount_type === 'percent')
                ? $price * ($discount / 100)
                : min($discount, $price);

            $unit_price_net = $price - $discount_amount_unit;

            $line_total_ht = $price * $qty;
            $line_discount_total = $discount_amount_unit * $qty;
            $line_total_after_discount = $unit_price_net * $qty;

            $total_ht += $line_total_ht;
            $total_discount += $line_discount_total;
            $total_after_discount += $line_total_after_discount;
        }

        // Remise globale
        $global_discount_amount = floatval($this->input->post('global_discount_amount') ?: 0);
        $global_discount_type = $this->input->post('global_discount_type') ?: 'percent';
        $global_discount = 0;

        if ($global_discount_amount > 0) {
            if ($global_discount_type === 'percent') {
                $global_discount = $total_after_discount * ($global_discount_amount / 100);
            } else {
                $global_discount = min($global_discount_amount, $total_after_discount);
            }
        }

        $final_total_discount = $total_discount + $global_discount;
        $final_total_after_discount = max($total_after_discount - $global_discount, 0);

        return [
            'total_ht'              => $total_ht,
            'total_discount'        => $final_total_discount,
            'total_after_discount'  => $final_total_after_discount
        ];
    }

    /**
     * Crée une catégorie si elle n'existe pas
     */
    private function getOrCreateCategory($category_name)
    {
        $category = $this->db->where('item_category', $category_name)->get('item_category')->row();
        if (!$category) {
            $this->db->insert('item_category', ['item_category' => $category_name]);
            return $this->db->insert_id();
        }
        return $category->id;
    }

    /**
     * Crée un produit s'il n'existe pas
     */
    private function getOrCreateItem($item_name, $category_id, $unit, $price)
    {
        $item = $this->db->where('name', $item_name)
            ->where('item_category_id', $category_id)
            ->get('item')
            ->row();

        if (!$item) {
            $item_data = [
                'name'             => $item_name,
                'item_category_id' => $category_id,
                'unit'             => $unit,
                'unit_price'       => $price,
                'created_at'       => date('Y-m-d H:i:s')
            ];
            $this->db->insert('item', $item_data);
            $item_id = $this->db->insert_id();

            // Initialiser le stock
            $this->db->insert('stock', [
                'item_id'          => $item_id,
                'initial_quantity' => 0,
                'current_quantity' => 0,
                'created_at'       => date('Y-m-d H:i:s')
            ]);
            return $item_id;
        }
        return $item->id;
    }

    /**
     * Crée un nouveau client (à conserver, déjà présent dans votre code)
     */
    private function createNewClient()
    {
        $new_client_name  = trim($this->input->post('new_client_name'));
        $new_client_phone = trim($this->input->post('new_client_phone'));

        if (empty($new_client_name) || empty($new_client_phone)) {
            throw new Exception("Veuillez renseigner le nom et le téléphone du nouveau client.");
        }

        $clientData = [
            'item_supplier' => $new_client_name,
            'phone'         => $new_client_phone,
            'email'         => trim($this->input->post('new_client_email')),
            'created_at'    => date('Y-m-d H:i:s')
        ];

        $this->db->insert('clients', $clientData);
        return $this->db->insert_id();
    }

    /**
     * Génère un numéro de devis unique (déjà présent)
     */
    private function generateQuoteNumber()
    {
        $prefix = 'PFM';
        $date = date('Ym');
        $this->db->like('quote_number', $prefix . '-' . $date, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('proforma');
        if ($query->num_rows() > 0) {
            $last_ref = $query->row()->quote_number;
            $sequence = intval(substr($last_ref, -4)) + 1;
        } else {
            $sequence = 1;
        }
        $sequence_padded = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        return $prefix . '-' . $date . '-' . $sequence_padded;
    }

    /**
     * Définit le mode de filtre pour les admins
     */
    public function setFilterMode()
    {
        $filter_mode = $this->input->post('filter_mode');
        $response = ['status' => 'error', 'message' => ''];

        if ($filter_mode && in_array($filter_mode, ['all', 'my'])) {
            $this->session->set_userdata('quote_filter_mode', $filter_mode);
            $response = [
                'status' => 'success',
                'filter_mode' => $filter_mode,
                'message' => 'Mode de filtre mis à jour'
            ];
        }

        echo json_encode($response);
    }

    /**
     * Récupère le mode de filtre actuel
     */

    /**
     * Récupère les articles par nom de catégorie (pour datalist)
     */
    public function get_items_by_category_name() {
        $this->output->set_content_type('application/json');

        $category_name = $this->input->post('category_name');

        if (!$category_name) {
            echo json_encode([]);
            return;
        }

        $this->db->select('item.id, item.name, item.unit, item.unit_price, stock.current_quantity');
        $this->db->from('item');
        $this->db->join('item_category', 'item_category.id = item.item_category_id');
        $this->db->join('stock', 'stock.item_id = item.id', 'left');
        $this->db->where('item_category.item_category', $category_name);
        $this->db->order_by('item.name', 'ASC');
        $items = $this->db->get()->result();

        echo json_encode($items);
    }

    /**
     * Récupère les détails d'un article par son nom et sa catégorie
     */
    /**
     * Récupère les détails d'un article par son nom et sa catégorie
     */
    /**
     * Récupère les détails d'un article par son nom et sa catégorie
     */
    public function get_item_details() {
        $this->output->set_content_type('application/json');

        $item_name = $this->input->post('item_name');
        $category_name = $this->input->post('category_name');

        if (!$item_name || !$category_name) {
            echo json_encode(['status' => 'error', 'message' => 'Paramètres manquants']);
            return;
        }

        // Récupérer l'article
        $this->db->select('item.id, item.name, item.unit, item.unit_price as price, stock.current_quantity');
        $this->db->from('item');
        $this->db->join('item_category', 'item_category.id = item.item_category_id');
        $this->db->join('stock', 'stock.item_id = item.id', 'left');
        $this->db->where('item.name', $item_name);
        $this->db->where('item_category.item_category', $category_name);
        $item = $this->db->get()->row();

        if ($item) {
            echo json_encode([
                'status' => 'success',
                'item_id' => $item->id,
                'unit' => $item->unit,
                'price' => floatval($item->price), // S'assurer que le prix est bien un nombre
                'quantity' => $item->current_quantity ?: 0
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Article non trouvé']);
        }
    }

    public function getFilterMode()
    {
        $filter_mode = $this->session->userdata('quote_filter_mode') ?: 'my';
        echo json_encode(['filter_mode' => $filter_mode]);
    }


    /**
     * STOCK ENTRY TOOL FORM
     */
    public function form()
    {
        // Définition des menus actifs
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Proforma/index');

        // Récupérer le nom de l'utilisateur connecté
        $current_user = $this->session->userdata('admin')['username'];
        $role_id = $this->session->userdata('admin')['role_id'];
        $is_admin = ($role_id == 7 || $role_id == 1);

        // Préparation des données pour la vue
        $data = [
            'title' => 'Ajouter un article au devis',
            'title_list' => 'Derniers articles ajoutés au devis',
            'itemcatlist' => $this->itemcategory_model->get(),
            'itemlist' => $this->item_model->get(),
            'clients' => $this->clients_model->get(),
            'current_user' => $current_user,
            'is_admin' => $is_admin
        ];

        // Chargement des vues
        $this->load->view('layout/header', $data);
        $this->load->view('admin/proforma/form', $data);
        $this->load->view('layout/footer', $data);
    }


    /**
     * STOCK ENTRY TOOL FORM
     */

    /**
     * Duplique un devis existant
     *
     * @param int $id ID du devis à dupliquer
     * @return JSON
     */
    public function duplicate($id)
    {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('devis', 'can_add')) {
            echo json_encode([
                'success' => false,
                'message' => 'Accès refusé'
            ]);
            return;
        }

        $response = ['success' => false, 'message' => '', 'new_quote_id' => null, 'new_reference' => ''];

        try {
            // Vérifier que le devis existe
            $originalQuote = $this->quote_model->getQuoteWithItems($id);
            if (!$originalQuote) {
                throw new Exception('Proforma original non trouvé');
            }

            // VÉRIFICATION DE L'UTILISATEUR ET DU RÔLE
            //$current_user = $this->session->userdata('admin')['username'];
            //$role_id = $this->session->userdata('admin')['role_id'];
            //$is_admin = ($role_id == 7 || $role_id == 1);

            // Si ce n'est pas un admin et que l'utilisateur ne correspond pas
            //if (!$is_admin && $originalQuote['user_name'] != $current_user) {
              //  throw new Exception('Vous n\'avez pas accès à ce devis');
            //}

            // Démarrer la transaction
            $this->db->trans_start();

            // Générer une nouvelle référence
            $newReference = $this->generateQuoteNumber();

            // Dupliquer le devis principal
            $newQuoteId = $this->duplicateQuote($originalQuote, $newReference);

            // Dupliquer les lignes du devis
            $this->duplicateQuoteItems($id, $newQuoteId);

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                throw new Exception('Erreur lors de la duplication du proforma');
            }

            $response['success'] = true;
            $response['message'] = 'Proforma dupliqué avec succès';
            $response['new_quote_id'] = $newQuoteId;
            $response['new_reference'] = $newReference;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $response['message'] = $e->getMessage();
            log_message('error', 'Quote Duplication Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    /**
     * Duplique les données principales du devis
     */
    private function duplicateQuote($originalQuote, $newReference)
    {
        $quoteData = [
            'quote_number'          => $newReference,
            'customer_id'           => $originalQuote['customer_id'],
            'user_name'             => $originalQuote['user_name'],
            'objet'                 => $originalQuote['objet'] . ' (Copie)',
            'payment_method'        => $originalQuote['payment_method'],
            'quote_date'            => date('Y-m-d'),
            'valid_until'           => date('Y-m-d', strtotime('+30 days')),
            'payment_terms'         => $originalQuote['payment_terms'],
            'delivery_terms'        => $originalQuote['delivery_terms'],
            'delivery_location'     => $originalQuote['delivery_location'],
            'tax_option'            => $originalQuote['tax_option'],
            'tva_rate'              => $originalQuote['tva_rate'],
            'tva_amount'            => $originalQuote['tva_amount'],
            'other_tax_name'        => $originalQuote['other_tax_name'],
            'other_tax_rate'        => $originalQuote['other_tax_rate'],
            'other_tax_amount'      => $originalQuote['other_tax_amount'],
            'total_ht'              => $originalQuote['total_ht'],
            'total_discount'        => $originalQuote['total_discount'],
            'total_after_discount'  => $originalQuote['total_after_discount'],
            'total_tax_amount'      => $originalQuote['total_tax_amount'],
            'total_ttc'             => $originalQuote['total_ttc'],
            'global_discount_type'  => $originalQuote['global_discount_type'],
            'global_discount_amount'=> $originalQuote['global_discount_amount'],
            'status'                => 1,
            'created_at'            => date('Y-m-d H:i:s'),
            'updated_at'            => date('Y-m-d H:i:s')
        ];

        $this->db->insert('proforma', $quoteData);
        return $this->db->insert_id();
    }

    /**
     * Duplique les lignes du devis
     */
    private function duplicateQuoteItems($originalQuoteId, $newQuoteId)
    {
        $originalItems = $this->db->where('quote_id', $originalQuoteId)->get('quote_items')->result_array();
        if (empty($originalItems)) return;

        $newItems = [];
        foreach ($originalItems as $item) {
            $newItems[] = [
                'quote_id'                  => $newQuoteId,
                'category_id'               => $item['category_id'],
                'item_id'                   => $item['item_id'],
                'service_id'                => $item['service_id'],      // AJOUTÉ
                'item_type'                 => $item['item_type'],       // AJOUTÉ
                'category_name'             => $item['category_name'],
                'item_name'                 => $item['item_name'],
                'quantity'                  => $item['quantity'],
                'unit_price'                => $item['unit_price'],
                'unit'                      => $item['unit'],
                'discount_type'             => $item['discount_type'],
                'discount'                  => $item['discount'],
                'discount_amount'           => $item['discount_amount'],
                'line_total'                => $item['line_total'],
                'line_total_after_discount' => $item['line_total_after_discount'],
                'created_at'                => date('Y-m-d H:i:s')
            ];
        }
        $this->db->insert_batch('proforma_items', $newItems);
    }

    private function prepareQuoteItemsWithBossFormula_160526($quote_id, $categories, $items, $quantities, $prices, $units, $discounts, $discount_types)
    {
        $quote_items = [];

        foreach ($items as $i => $item_name) {
            $category_name = $categories[$i];
            $category_id = $this->getOrCreateCategory($category_name);
            $item_id = $this->getOrCreateItem($item_name, $category_id, $units[$i] ?? '', $prices[$i]);

            $qty = floatval($quantities[$i]);
            $price = floatval($prices[$i]);
            $discount = floatval($discounts[$i] ?? 0);
            $discount_type = $discount_types[$i] ?? 'percent';

            $discount_amount_unit = ($discount_type === 'percent')
                ? $price * ($discount / 100)
                : min($discount, $price);

            $unit_price_net = $price - $discount_amount_unit;

            $line_total_ht = $price * $qty;
            $line_discount_total = $discount_amount_unit * $qty;
            $line_total_after_discount = $unit_price_net * $qty;

            $quote_items[] = [
                'quote_id' => $quote_id,
                'category_id' => $category_id,
                'item_id' => $item_id,
                'category_name' => $category_name,
                'item_name' => $item_name,
                'quantity' => $qty,
                'unit_price' => $price,
                'unit' => $units[$i] ?? '',
                'discount_type' => $discount_type,
                'discount' => $discount,
                'discount_amount' => $line_discount_total,
                'line_total' => $line_total_ht,
                'line_total_after_discount' => $line_total_after_discount,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }

        return $quote_items;
    }

    /**
     * Affiche les détails d'un devis
     *
     * @param int $id ID du devis
     * @return void
     */

    public function view($id)
    {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('devis', 'can_view')) {
            access_denied();
        }

        // Récupération des données du devis
        $data['pro'] = $this->proforma_model->getQuoteWithItems($id);

        // Vérification si le devis existe
        if (!$data['pro']) {
            $this->session->set_flashdata('error', 'Proforma non trouvé');
            redirect('admin/proforma');
        }

        // ð¹ VÉRIFICATION DE L'UTILISATEUR ET DU RÔLE
        //$current_user = $this->session->userdata('admin')['username'];
        //$role_id = $this->session->userdata('admin')['role_id'];
        //$is_admin = ($role_id == 7 || $role_id == 1);

        //if (!$is_admin && $data['quote']['user_name'] != $current_user) {
          //  $this->session->set_flashdata('error', 'Vous n\'avez pas accès à ce devis');
            //redirect('admin/quoteitem');
        //}

        // Préparation des données pour la vue
        $data['title'] = 'Détails du proforma';
        $data['page_title'] = 'Détails du proforma ' . $data['pro']['quote_number'];

        // Chargement des vues
        $this->load->view('layout/header', $data);
        $this->load->view('admin/proforma/view', $data);
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
        if (!$this->rbac->hasPrivilege('devis', 'can_edit')) {
            access_denied();
        }

        $quote_id = $this->input->post('id', 0);

        // Initialisation de la réponse
        $response = ['status' => 'fail', 'message' => ''];

        try {
            // Récupération des données du devis
            $data['quote'] = $this->proforma_model->getQuoteWithItems($quote_id);
            if (!$data['quote']) {
                throw new Exception('Proforma introuvable');
            }

            // VÉRIFICATION DE L'UTILISATEUR ET DU RÔLE
            $current_user = $this->session->userdata('admin')['username'];
            $role_id = $this->session->userdata('admin')['role_id'];
            $is_admin = ($role_id == 7 || $role_id == 1);

            if (!$is_admin && $data['quote']['user_name'] != $current_user) {
                throw new Exception('Vous n\'avez pas accès à ce proforma');
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
            $data['user_name'] = $this->customlib->getAdminSessionUserName();
            $data['user_email'] = $data['user']['email'] ?? '';

            if ($data['quote']) {

                $quote_detail = array(
                    'id'            => $data['quote']['id'],
                    'data'          => $data,
                    'credential_for'=> 'sendQuote',
                    'client_name'       => $data['quote']['customer_name'].' '.$data['quote']['customer_last_name'],
                    'quotation_number'  => $data['quote']['quote_number'],
                    'quotation_date'    => !empty($data['quote']['quote_date']) ? date('d/m/Y', strtotime($data['quote']['quote_date'])) :"N/A",
                    'email'             => $data['quote']['customer_email']);

                $this->mailsmsconf->mailsms('send_quotes', $quote_detail);
            }

            $response['status'] = 'success';
            $response['message'] = 'Le proforma a été envoyé avec succès';

        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Quote Email Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    public function delete($id)
    {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('devis', 'can_delete')) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Accès refusé']);
                return;
            }
            access_denied();
        }

        try {
            // Vérifier que le devis existe
            $quote = $this->proforma_model->getQuoteWithItems($id);
            if (!$quote) {
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'error', 'message' => 'Proforma introuvable']);
                    return;
                }
                $this->session->set_flashdata('error', 'Proforma introuvable');
                redirect('admin/proforma');
            }

            // ð¹ VÉRIFICATION DE L'UTILISATEUR ET DU RÔLE
            //$current_user = $this->session->userdata('admin')['username'];
            //$role_id = $this->session->userdata('admin')['role_id'];
            //$is_admin = ($role_id == 7 || $role_id == 1);

            //if (!$is_admin && $quote['user_name'] != $current_user) {
              //  if ($this->input->is_ajax_request()) {
                //    echo json_encode(['status' => 'error', 'message' => 'Vous n\'avez pas accès à ce devis']);
                  //  return;
                //}
                //$this->session->set_flashdata('error', 'Vous n\'avez pas accès à ce devis');
                //redirect('admin/quoteitem');
            //}

            // Vérifier le statut
            if ((int)$quote['status'] !== 1) {
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'error', 'message' => 'Ce proforma ne peut pas être supprimé']);
                    return;
                }
                $this->session->set_flashdata('error', 'Ce proforma ne peut pas être supprimé');
                redirect('admin/proforma');
            }

            // Suppression transactionnelle
            $this->db->trans_start();

            // Supprimer les articles liés
            $this->db->where('quote_id', $id)->delete('proforma_items');

            // Supprimer le devis
            $this->db->where('id', $id)->delete('proforma');

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                throw new Exception("Erreur lors de la suppression du proforma");
            }

            // Réponse pour AJAX
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'Proforma supprimé avec succès']);
                return;
            }

            // Réponse pour requête normale
            $this->session->set_flashdata('success', 'Proforma supprimé avec succès');
            redirect('admin/proforma');

        } catch (Exception $e) {
            log_message('error', 'Quote Delete Error: ' . $e->getMessage());

            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Une erreur est survenue lors de la suppression du proforma']);
                return;
            }

            $this->session->set_flashdata('error', 'Une erreur est survenue lors de la suppression du proforma');
            redirect('admin/proforma');
        }
    }



    public function edit($id)
    {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('devis', 'can_edit')) {
            access_denied();
        }

        try {
            // Vérifier le statut du devis
            $quote = $this->proforma_model->getQuoteWithItems($id);
            if (!$quote) {
                $this->session->set_flashdata('error', 'Proforma introuvable');
                redirect('admin/proforma');
            }

            // ð¹ VÉRIFICATION DE L'UTILISATEUR ET DU RÔLE
            //$current_user = $this->session->userdata('admin')['username'];
            //$role_id = $this->session->userdata('admin')['role_id'];
            //$is_admin = ($role_id == 7 || $role_id == 1);

            //if (!$is_admin && $quote['user_name'] != $current_user) {
              //  $this->session->set_flashdata('error', 'Vous n\'avez pas accès à ce devis');
                //redirect('admin/quoteitem');
            //}

            if ((int)$quote['status'] === 0) {
                $this->session->set_flashdata('error', 'Ce proforma doit être validé avant modification');
                redirect('admin/proforma');
            }

            // Préparation des données pour la vue
            $data = [
                'title' => 'Modifier le proforma',
                'quote' => $quote,
                'itemcatlist' => $this->itemcategory_model->get(),
                'itemList' => $this->item_model->get(),
                'clients' => $this->clients_model->get(),
                //'current_user' => $current_user,
                //'is_admin' => $is_admin
            ];

            // Chargement des vues
            $this->load->view('layout/header', $data);
            $this->load->view('admin/proforma/edit', $data);
            $this->load->view('layout/footer', $data);
        } catch (Exception $e) {
            log_message('error', 'Quote Edit Error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Une erreur est survenue lors de l\'édition du proforma');
            redirect('admin/proforma');
        }
    }



    /**
     * Met à jour un devis existant
     *
     * @param int $id ID du devis
     * @return void
     */


    /**
     * Valide un devis
     *
     * @param int $id ID du devis
     * @return void
     */

    public function validate()
    {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('devis', 'can_edit')) {
            access_denied();
        }

        $id = $this->input->post('id');
        $order_number = $this->input->post('order_number');

        $response = ['status' => 'fail', 'message' => ''];

        try {
            // Vérifier que l'ID est valide
            if (empty($id)) {
                throw new Exception('ID du proforma manquant');
            }

            // Vérification que le devis existe
            $quote = $this->quote_model->getQuoteWithItems($id);
            if (!$quote) {
                throw new Exception('Proforma non trouvé');
            }

            // ð¹ VÉRIFICATION DE L'UTILISATEUR ET DU RÔLE
            //$current_user = $this->session->userdata('admin')['username'];
            //$role_id = $this->session->userdata('admin')['role_id'];
            //$is_admin = ($role_id == 7 || $role_id == 1);

            //if (!$is_admin && $quote['user_name'] != $current_user) {
              //  throw new Exception('Vous n\'avez pas accès à ce devis');
           // }

            // Vérification que le devis n'est pas déjà validé ou rejeté
            if ($this->quote_model->isQuoteValidated($id)) {
                throw new Exception('Ce proforma est déjà validé');
            }
            if ($this->quote_model->isQuoteRejected($id)) {
                throw new Exception('Ce proforma a été rejeté');
            }

            // Validation du devis
            $data = [
                'status' => Quote_model::STATUS_VALIDATED,
                'validated_at' => date('Y-m-d H:i:s'),
            ];

            if (!$this->quote_model->validateQuote($id, $data)) {
                throw new Exception('Erreur lors de la validation du proforma');
            }

            // Créer la commande à partir du devis
            $this->createOrderFromQuote($id, $order_number);

            // Créer la sortie de stock
            $this->createStockRemovalFromQuote($id);

            $response['status'] = 'success';
            $response['message'] = 'Proforma validé avec succès';

        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            log_message('error', 'Quote Validation Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    private function createOrderFromQuote($quote_id, $order_number)
    {
        // Récupération des données du devis
        $quote = $this->quote_model->getQuoteWithItems($quote_id);
        if (!$quote) {
            return false;
        }

        // Préparation des données de la commande
        $order_data = [
            'order_number'  => $order_number,
            'quote_id'      => $quote_id,
            'customer_id'   => $quote['customer_id'],
            'objet'       => $quote['objet'],
            'payment_method'       => $quote['payment_method'],
            'user_name'   => $quote['user_name'],
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
            'status'        => Quote_model::STATUS_IN_PROGRESS,
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
                    'service_id'    => $item['service_id'] ?? null,
                    'item_type'     => $item['item_type'] ?? 'product',
                    'quantity'      => $item['quantity'],
                    'unit_price'    => $item['unit_price'],
                    'unit'          => $item['unit'],
                    'line_total'    => $item['line_total'],
                    'position'      => $item['position'] ?? 0
                ];
                $this->db->insert('order_items', $order_item_data);
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

    private function createOrderFromQuote_250526($quote_id, $order_number)
    {
        // Récupération des données du devis
        $quote = $this->quote_model->getQuoteWithItems($quote_id);
        if (!$quote) {
            return false;
        }

        // Préparation des données de la commande
        $order_data = [
            'order_number'  => $order_number,
            'quote_id'      => $quote_id,
            'customer_id'   => $quote['customer_id'],
            'objet'       => $quote['objet'],
            'payment_method'       => $quote['payment_method'],
            'user_name'   => $quote['user_name'],
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
            'status'        => Quote_model::STATUS_IN_PROGRESS,
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

    private function createDeliveryFromOrder($quote_id, $order_id)
    {
        // Récupération des données de la commande
        $quote = $this->quote_model->getQuoteWithItems($quote_id);
        if (!$quote) {
            return false;
        }

        // Génération du numéro de livraison
        $delivery_number = $this->generateDeliveryNumber();

        // Préparation des données de la livraison
        $delivery_data = [
            'delivery_number'   => $delivery_number,
            'user_name'       => $quote['user_name'],
            'customer_id'       => $quote['customer_id'],
            'objet'       => $quote['objet'],
            'payment_method'       => $quote['payment_method'],
            'order_id'          => $order_id,
            'delivery_date'     => date('Y-m-d'),
            'shipping_method'   => $quote['payment_terms'],
            'tracking_number'   => $quote['delivery_terms'],
            'designation'   => $quote['designation'],
            'valid_until'   => $quote['valid_until'],
            'payment_terms'   => $quote['payment_terms'],
            'delivery_terms'   => $quote['delivery_terms'],
            'delivery_location'   => $quote['delivery_location'],
            'apply_tva'     => $quote['apply_tva'],
            'tva_rate'      => $quote['tva_rate'],
            'tva_amount'    => $quote['tva_amount'],
            'total_ht'      => $quote['total_ht'],
            'total_ttc'     => $quote['total_ttc'],
            'deadline'          => $quote['valid_until'],
            'delivery_address'  => $quote['delivery_location'],
            'notes'            => $quote['notes'] ?? '',
            'status'           => 1,
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
                    'item_id'            => $item['item_id'],
                    'service_id'         => $item['service_id'] ?? null,
                    'item_type'          => $item['item_type'] ?? 'product',
                    'quantity'           => $item['quantity'],
                    'delivered_quantity' => 0,
                    'unit_price'         => $item['unit_price'],
                    'line_total'         => $item['line_total'],
                    'position'           => $item['position'] ?? 0
                ];
                $this->db->insert('delivery_items', $delivery_item_data);
            }

            return true;
        } catch (Exception $e) {
            log_message('error', 'Delivery Creation Error: ' . $e->getMessage());
            return false;
        }
    }

    private function createDeliveryFromOrder_25($quote_id, $order_id)
    {
        // Récupération des données de la commande
        $quote = $this->quote_model->getQuoteWithItems($quote_id);
        if (!$quote) {
            return false;
        }

        // Génération du numéro de livraison
        $delivery_number = $this->generateDeliveryNumber();

        // Préparation des données de la livraison
        $delivery_data = [
            'delivery_number'   => $delivery_number,
            'user_name'       => $quote['user_name'],
            'customer_id'       => $quote['customer_id'],
            'objet'       => $quote['objet'],
            'payment_method'       => $quote['payment_method'],
            'order_id'          => $order_id,
            'delivery_date'     => date('Y-m-d'),
            'shipping_method'   => $quote['payment_terms'],
            'tracking_number'   => $quote['delivery_terms'],
            'designation'   => $quote['designation'],
            'valid_until'   => $quote['valid_until'],
            'payment_terms'   => $quote['payment_terms'],
            'delivery_terms'   => $quote['delivery_terms'],
            'delivery_location'   => $quote['delivery_location'],
            'apply_tva'     => $quote['apply_tva'],
            'tva_rate'      => $quote['tva_rate'],
            'tva_amount'    => $quote['tva_amount'],
            'total_ht'      => $quote['total_ht'],
            'total_ttc'     => $quote['total_ttc'],
            'deadline'          => $quote['valid_until'],
            'delivery_address'  => $quote['delivery_location'],
            'notes'            => $quote['notes'] ?? '',
            'status'           => 1,
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

    private function generateDeliveryNumber()
    {
        $prefix = 'BL';
        $date = date('Ym');

        $this->db->like('delivery_number', $prefix . '-' . $date, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('deliveries');

        if ($query->num_rows() > 0) {
            $last_ref = $query->row()->delivery_number;
            $sequence = intval(substr($last_ref, -4)) + 1;
        } else {
            $sequence = 1;
        }

        $sequence_padded = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        return $prefix . '-' . $date . '-' . $sequence_padded;
    }


    private function createStockRemovalFromQuote($quote_id)
    {
        // Récupérer les informations du devis
        $quote = $this->quote_model->getQuoteWithItems($quote_id);
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

            // Mettre à jour le stock
            $this->db->set(
                'current_quantity',
                'current_quantity - ' . $item['quantity'],
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


    private function createStockRemovalFromQuote250526($quote_id)
    {
        // Récupérer les informations du devis
        $quote = $this->quote_model->getQuoteWithItems($quote_id);
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

            // Mettre à jour le stock
            $this->db->set(
                'current_quantity',
                'current_quantity - ' . $item['quantity'],
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
        if (!$this->rbac->hasPrivilege('devis', 'can_edit')) {
            access_denied();
        }

        $id = $this->input->post('id');

        $response = ['status' => 'fail', 'message' => ''];

        try {
            // Vérification que le devis existe
            $quote = $this->quote_model->getQuoteWithItems($id);
            if (!$quote) {
                throw new Exception('Devis non trouvé');
            }

            // VÉRIFICATION DE L'UTILISATEUR ET DU RÔLE
            $current_user = $this->session->userdata('admin')['username'];
            $role_id = $this->session->userdata('admin')['role_id'];
            $is_admin = ($role_id == 7 || $role_id == 1);

            if (!$is_admin && $quote['user_name'] != $current_user) {
                throw new Exception('Vous n\'avez pas accès à ce devis');
            }

            // Vérification que le devis n'est pas déjà validé ou rejeté
            if ($this->quote_model->isQuoteValidated($id)) {
                throw new Exception('Ce devis est déjà validé');
            }
            if ($this->quote_model->isQuoteRejected($id)) {
                throw new Exception('Ce devis a déjà été rejeté');
            }

            // Récupération du motif de rejet
            $reason = $this->input->post('reason');
            if (empty($reason)) {
                throw new Exception('Le motif de rejet est requis');
            }

            // Rejet du devis
            $data = [
                'status'        => Quote_model::STATUS_REJECTED,
                'rejected_at'   => date('Y-m-d H:i:s'),
                'rejected_by'   => $current_user,
                'notes'         => $reason
            ];

            if (!$this->quote_model->rejectQuote($id, $data)) {
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
        $data['quote'] = $this->proforma_model->getQuoteWithItems($id);

        if (!$data['quote']) {
            show_404();
            return;
        }

        // VÉRIFICATION DE L'UTILISATEUR ET DU RÔLE
       // $current_user = $this->session->userdata('admin')['username'];
        //$role_id = $this->session->userdata('admin')['role_id'];
        //$is_admin = ($role_id == 7 || $role_id == 1);

       // if (!$is_admin && $data['quote']['user_name'] != $current_user) {
          //  echo json_encode(['status' => 'error', 'message' => 'Vous n\'avez pas accès à ce devis']);
           // return;
        //}

        // Récupération des données de la société
        $company = $this->setting_model->get();

        // Récupération des données de l'entreprise
        $data['company'] = $company[0];
        $data['totalAsletter'] = $this->asLetters(floatval($data['quote']['total_ttc']));

        // Récupération des informations de l'utilisateur connecté
        $data['user'] = $this->customlib->getUserData();

        // Génération du QR Code
        $this->load->library('qrcode_lib');
        $qrPath = $this->qrcode_lib->generate(
            $data['quote']['quote_number'],
            $data['quote']['customer_name']
        );
        $data['qrCodePath'] = $qrPath;

        // Chargement de la vue d'impression
        $invoice_page = $this->load->view('admin/proforma/print', $data, true);
        $array = array('status' => '1', 'error' => '', 'page' => $invoice_page);
        echo json_encode($array);
    }


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
            $items = $this->stock_model->getItemByCategory($category['id']);
            echo json_encode($items);
        } else {
            echo json_encode([]);
        }
    }

    /**
     * Récupère tous les devis d'un client donné (format JSON pour la modale)
     * @param int $client_id
     */
    public function get_client_quotes($client_id)
    {
        // Vérification des droits
        if (!$this->rbac->hasPrivilege('devis', 'can_view')) {
            echo json_encode(['status' => 'error', 'message' => 'Accès refusé']);
            return;
        }

        $current_user = $this->session->userdata('admin')['username'];
        $role_id = $this->session->userdata('admin')['role_id'];
        $is_admin = ($role_id == 7 || $role_id == 1);

        $this->db->select('q.id, q.quote_number, q.quote_date, q.total_ttc, q.status');
        $this->db->from('quotes q');
        $this->db->where('q.customer_id', $client_id);
        if (!$is_admin) {
            $this->db->where('q.user_name', $current_user);
        }
        $this->db->order_by('q.quote_date', 'DESC');
        $quotes = $this->db->get()->result_array();

        // Mapping statuts
        $status_labels = [
            1 => 'En attente',
            2 => 'Validé',
            3 => 'Rejeté',
            4 => 'En cours',
            5 => 'Livré',
            6 => 'Annulé'
        ];
        foreach ($quotes as &$q) {
            $q['status_label'] = $status_labels[$q['status']] ?? 'Inconnu';
            $q['quote_date_fr'] = date('d/m/Y', strtotime($q['quote_date']));
            $q['total_ttc_formatted'] = number_format($q['total_ttc'], 2, ',', ' ') . ' FCFA';
        }

        echo json_encode(['status' => 'success', 'quotes' => $quotes]);
    }

    /**
     * Imprime tous les devis d’un client en un seul bloc
     * Utilisation : POST /admin/quoteitem/printAllByClient avec customer_id
     */
    public function printAllByClient() {
        if (!$this->rbac->hasPrivilege('devis', 'can_view')) {
            access_denied();
        }

        $customer_id = $this->input->post('customer_id');
        if (!$customer_id) {
            echo json_encode(['status' => '0', 'message' => 'Aucun client sélectionné.', 'page' => '']);
            return;
        }

        // Récupérer les devis du client
        $quotes = $this->quote_model->getQuotesByCustomer($customer_id);
        if (empty($quotes)) {
            echo json_encode(['status' => '0', 'message' => 'Ce client n’a aucun devis.', 'page' => '']);
            return;
        }

        // Récupérer les infos de l'entreprise
        $company = $this->setting_model->get();
        $company = $company[0];

        // Infos du client (depuis le premier devis)
        $customer = [
            'name'    => $quotes[0]['customer_name'] . ' ' . $quotes[0]['customer_last_name'],
            'email'   => $quotes[0]['customer_email'],
            'phone'   => $quotes[0]['customer_phone'],
            'address' => $quotes[0]['customer_address']
        ];

        $data = [
            'quotes'     => $quotes,
            'customer'   => $customer,
            'company'    => $company,
            'print_date' => date('d/m/Y H:i:s'),
            'title'      => 'Tous les devis de ' . $customer['name']
        ];

        // Générer la vue d'impression
        $page = $this->load->view('admin/quote/print_all_quotes', $data, true);
        echo json_encode(['status' => '1', 'page' => $page]);
    }

    /**
     * Impression groupée de plusieurs devis (factures)
     * Reçoit un tableau d'IDs en POST (ids[])
     */


    public function print_batch()
    {
        $ids = $this->input->post('ids');
        if (empty($ids) || !is_array($ids)) {
            show_error('Aucun devis sélectionné', 400);
            exit;
        }

        // Vérifier les droits sur chaque devis
        $current_user = $this->session->userdata('admin')['username'];
        $role_id = $this->session->userdata('admin')['role_id'];
        $is_admin = ($role_id == 7 || $role_id == 1);

        $quotes_data = [];
        $company = $this->setting_model->get();
        $company = $company[0];

        foreach ($ids as $quote_id) {
            $quote = $this->quote_model->getQuoteWithItems($quote_id);
            if (!$quote) continue;

            if (!$is_admin && $quote['user_name'] != $current_user) {
                continue; // ignorer les devis non autorisés
            }

            $quote['totalAsletter'] = $this->asLetters(floatval($quote['total_ttc']));
            $quotes_data[] = $quote;
        }

        if (empty($quotes_data)) {
            show_error('Aucun devis accessible trouvé', 403);
            exit;
        }

        $data = [
            'quotes' => $quotes_data,
            'company' => $company,
            'print_date' => date('d/m/Y H:i:s')
        ];

        $this->load->view('admin/quote/print_batch', $data);
    }


}