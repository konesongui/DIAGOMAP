<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Tableau_rh extends Admin_Controller
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

        $this->load->model('quote_selling_model');
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
        // Check view permission
        if (!$this->rbac->hasPrivilege('pdv', 'can_view')) {
            access_denied();
        }

        // Set menu active states
        $this->session->set_userdata('top_menu', 'Hr');
        $this->session->set_userdata('sub_menu', 'Tableau_rh/index');

        // Initialize page data
        $data = [
            'title' => 'Add Item',
            'title_list' => 'Recent Items',
        ];

        // Load views
        $this->load->view('layout/header', $data);
        $this->load->view('admin/tableau_rh/tableau_rh', $data);
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
            $filter_mode = $this->session->userdata('quote_filter_mode') ?: 'my';
        }

        // Vérifier les privilèges RBAC
        $is_superadmin = $this->rbac->hasPrivilege('superadmin');
        $is_admin = $this->rbac->hasPrivilege('admin');
        $is_admin_user = ($is_superadmin || $is_admin);

        // Si c'est un admin et que le filtre est 'all', on voit tout
        if ($is_admin_user && $filter_mode === 'all') {
            // Admin voit tout
            $result = $this->quote_selling_model->getListDataForAdmin();
        } else {
            // Utilisateur normal ou admin en mode 'my' - filtrer par son nom
            $result = $this->quote_selling_model->getListDataForUser($current_user);
        }

        echo $result;
    }


    public function get_caisse_solde() {
        $this->output->set_content_type('application/json');

        $caisse_id = $this->input->post('caisse_id');

        if (!$caisse_id) {
            echo json_encode(['status' => 'error', 'message' => 'Caisse non spécifiée']);
            return;
        }

        // Utiliser le modèle Income_model pour récupérer la caisse
        $this->load->model('income_model');
        $caisse = $this->income_model->get_caisse_by_id($caisse_id);

        $solde = $caisse ? ($caisse['montant_initial'] ?? 0) : 0;

        echo json_encode([
            'status' => 'success',
            'solde' => $solde
        ]);
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
    public function form_selling()
    {

        // Définition des menus actifs
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Selling/index');

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
        $this->load->view('admin/quote/form_selling', $data);
        $this->load->view('layout/footer', $data);
    }


    /**
     * STOCK ENTRY TOOL FORM
     */

    public function add()
    {
        if (!$this->rbac->hasPrivilege('pdv', 'can_view')) {
            access_denied();
        }

        $response = ['status' => 'fail', 'message' => '', 'error' => []];

        try {
            if (!$this->input->post()) {
                throw new Exception('Aucune donnée reçue');
            }

            // Validation
            $this->form_validation->set_rules('customer', 'Client');
            $this->form_validation->set_rules('quote_date', 'Date', 'required');
            $this->form_validation->set_rules('item_category[]', 'Catégorie', 'required');
            $this->form_validation->set_rules('item_name[]', 'Article', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantité', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('price[]', 'Prix TTC', 'required|numeric|greater_than[0]');

            // Validation pour la taxe personnalisée
            $tax_option = $this->input->post('tax_option');
            if ($tax_option === 'other') {
                $this->form_validation->set_rules('other_tax_name', 'Nom de la taxe', 'required|trim');
                $this->form_validation->set_rules('other_tax_rate', 'Taux de taxe', 'required|numeric|greater_than[0]|less_than[101]');
            }

            // Validation pour la source de paiement si montant payé > 0
            $amount_paid = floatval($this->input->post('amount_paid') ?: 0);
            $payment_method_type = $this->input->post('payment_method_type');

            if ($amount_paid > 0) {
                if (empty($payment_method_type)) {
                    throw new Exception('Veuillez sélectionner un mode de paiement');
                }

                if ($payment_method_type == 'cash') {
                    $caisse_id = $this->input->post('caisse_id');
                    if (empty($caisse_id)) {
                        throw new Exception('Veuillez sélectionner une caisse');
                    }
                } elseif ($payment_method_type == 'bank') {
                    $banque_id = $this->input->post('banque_id');
                    if (empty($banque_id)) {
                        throw new Exception('Veuillez sélectionner une banque');
                    }
                }
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

            // Données
            $categories = $this->input->post('item_category');
            $items = $this->input->post('item_name');
            $quantities = $this->input->post('quantity');
            $prices_ttc = $this->input->post('price');
            $units = $this->input->post('unit');
            $discounts = $this->input->post('discount') ?: [];
            $discount_types = $this->input->post('discount_type') ?: [];

            // Récupérer le taux de taxe
            $tax_option = $this->input->post('tax_option');
            $tax_rate = 0;

            switch ($tax_option) {
                case 'tva':
                    $tax_rate = 0.18;
                    break;
                case 'other':
                    $tax_rate = floatval($this->input->post('other_tax_rate') ?: 0) / 100;
                    break;
                case 'none':
                default:
                    $tax_rate = 0;
                    break;
            }

            // Calcul total avec la nouvelle formule (prix TTC -> HT)
            $totals = $this->calculateTotalsWithNewTVAFormula($quantities, $prices_ttc, $discounts, $discount_types, $tax_rate);

            // Gestion des taxes
            $other_tax_name = '';
            $other_tax_rate = 0;
            $tva_amount = 0;
            $tax_amount = 0;

            switch ($tax_option) {
                case 'tva':
                    $tva_rate = 18;
                    $tax_amount = $totals['total_ht_final'] * 0.18;
                    $tva_amount = $tax_amount;
                    break;

                case 'other':
                    $other_tax_name = $this->input->post('other_tax_name');
                    $other_tax_rate = $this->input->post('other_tax_rate');
                    $tax_amount = $totals['total_ht_final'] * ($other_tax_rate / 100);
                    break;

                case 'none':
                default:
                    $tax_amount = 0;
                    break;
            }

            // Total TTC final = HT final + taxes
            $total_ttc_final = $totals['total_ht_final'] + $tax_amount;

            // ARRONDIR TOUS LES TOTAUX À 2 DÉCIMALES
            $totals['total_ttc_brut'] = round($totals['total_ttc_brut'], 2);
            $totals['total_ht_brut'] = round($totals['total_ht_brut'], 2);
            $totals['total_discount'] = round($totals['total_discount'], 2);
            $totals['total_ht_final'] = round($totals['total_ht_final'], 2);
            $tax_amount = round($tax_amount, 2);
            $total_ttc_final = round($total_ttc_final, 2);

            // Données de paiement (avec arrondi pour éviter les erreurs de précision)
            // Données de paiement (avec arrondi pour éviter les erreurs de précision)
            $amount_paid = round(floatval($this->input->post('amount_paid') ?: 0), 2);
            $total_ttc_final = round($total_ttc_final, 2);

// Calcul du reste à payer et de la monnaie
            $remaining_amount = round($total_ttc_final - $amount_paid, 2);
            $change_amount = 0;

// Statut du paiement - CORRIGÉ
            if ($amount_paid >= $total_ttc_final) {
                $payment_status = 'paid';
                $sale_status = 2;
                $remaining_amount = 0;
                $change_amount = round($amount_paid - $total_ttc_final, 2);
                // Forcer le montant payé à la valeur exacte
                $amount_paid = $total_ttc_final;
            } elseif ($amount_paid > 0) {
                $payment_status = 'partial';
                $sale_status = 2;
            } else {
                $payment_status = 'pending';
                $sale_status = 2;
            }

// S'assurer que remaining_amount n'est jamais négatif
            $remaining_amount = max($remaining_amount, 0);
            // Statut du paiement (CORRIGÉ - sans tolérance)
            if ($amount_paid >= $total_ttc_final) {
                $payment_status = 'paid';
                $sale_status = 2; // Payé totalement
            } elseif ($amount_paid > 0) {
                $payment_status = 'partial';
                $sale_status = 2; // Paiement partiel
            } else {
                $payment_status = 'pending';
                $sale_status = 2; // En attente
            }

            // Démarrer la transaction
            $this->db->trans_begin();

            // Enregistrement de la vente
            $quote_data = [
                'quote_number'          => $this->generateQuoteNumber(),
                'customer_id'           => $customer_id,
                'user_name'             => $this->input->post('user_name'),
                'objet'                 => $this->input->post('objet'),
                'payment_method'        => $this->input->post('payment_method'),
                'payment_method_type'   => $payment_method_type,
                'quote_date'            => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('quote_date')))),
                'valid_until'           => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('valid_until')))),
                'payment_terms'         => $this->input->post('payment_terms'),
                'delivery_terms'        => $this->input->post('delivery_terms'),
                'delivery_location'     => $this->input->post('delivery_location'),
                'tax_option'            => $tax_option,
                'tva_rate'              => ($tax_option == 'tva') ? 18 : 0,
                'tva_amount'            => $tva_amount,
                'other_tax_name'        => $other_tax_name,
                'other_tax_rate'        => $other_tax_rate,
                'other_tax_amount'      => ($tax_option == 'other') ? $tax_amount : 0,
                'total_ttc_brut'        => $totals['total_ttc_brut'],
                'total_ht_brut'         => $totals['total_ht_brut'],
                'total_discount'        => $totals['total_discount'],
                'total_ht'              => $totals['total_ht_final'],
                'total_tax_amount'      => $tax_amount,
                'total_ttc'             => $total_ttc_final,
                'global_discount_type'  => $this->input->post('global_discount_type'),
                'global_discount_amount'=> $this->input->post('global_discount_amount') ?: 0,
                'amount_paid'           => $amount_paid,
                'remaining_amount'      => $remaining_amount,
                'change_amount'         => $change_amount,
                'payment_status'        => $payment_status,
                'status'                => $sale_status,
                'created_at'            => date('Y-m-d H:i:s')
            ];

            $this->db->insert('quotes_selling', $quote_data);
            $quote_id = $this->db->insert_id();

            if (!$quote_id) {
                throw new Exception('Erreur lors de l\'insertion de la vente');
            }

            // Articles
            $quote_items = $this->prepareQuoteItemsWithNewTVAFormula(
                $quote_id, $categories, $items, $quantities, $prices_ttc, $units, $discounts, $discount_types, $tax_rate
            );

            if (!empty($quote_items)) {
                $this->db->insert_batch('quote_items_selling', $quote_items);

                // Mise à jour du stock
                foreach ($quote_items as $item) {
                    $this->db->set('current_quantity', 'current_quantity - ' . $item['quantity'], FALSE)
                        ->where('item_id', $item['item_id'])
                        ->update('stock');
                }
            }

            // Enregistrement du paiement si montant payé > 0
            if ($amount_paid > 0) {
                $payment_reference = 'VENTE-' . date('YmdHis') . '-' . $quote_id;

                if ($payment_method_type == 'cash') {
                    $caisse_id = $this->input->post('caisse_id');
                    $caisse = $this->db->where('id', $caisse_id)->get('income')->row();

                    if (!$caisse) {
                        throw new Exception('Caisse introuvable');
                    }

                    $old_balance = (float)($caisse->amount_re ?? 0);
                    $new_balance = $old_balance + $amount_paid;

                    // Mettre à jour le solde de la caisse
                    $this->db->where('id', $caisse_id)
                        ->update('income', [
                            'amount_re' => $new_balance,
                            'last_operation_date' => date('Y-m-d H:i:s')
                        ]);

                    // Enregistrer dans operation_caisse
                    $operation_caisse_data = [
                        'reference'              => $payment_reference,
                        'type_operation'         => 'entree',
                        'montant'                => $amount_paid,
                        'designation'            => 'Vente point de vente #' . $quote_data['quote_number'] . ' - Client: ' . ($this->getCustomerName($customer_id)),
                        'caisse_id'              => $caisse_id,
                        'date'                   => date('Y-m-d H:i:s'),
                        'entree'                 => $amount_paid,
                        'sortie'                 => 0,
                        'note'                   => 'Paiement vente',
                        'est_actif'              => 1,
                        'created_at'             => date('Y-m-d H:i:s'),
                        'updated_at'             => date('Y-m-d H:i:s'),
                        'category'               => 'Vente de produit',
                        'solde_avant_operation'  => $old_balance,
                        'solde_apres_operation'  => $new_balance
                    ];

                    $this->db->insert('operation_caisse', $operation_caisse_data);
                    $operation_id = $this->db->insert_id();

                    // Enregistrer dans mouvements
                    $mouvement_data = [
                        'type_mouvement'         => 'entree',
                        'montant'                => $amount_paid,
                        'description'            => 'Vente point de vente #' . $quote_data['quote_number'],
                        'reference'              => $payment_reference,
                        'date_mouvement'         => date('Y-m-d'),
                        'mode_paiement'          => 'cash',
                        'created_at'             => date('Y-m-d H:i:s'),
                        'operation_id'           => $operation_id,
                        'solde_avant_operation'  => $old_balance,
                        'solde_apres_operation'  => $new_balance
                    ];
                    $this->db->insert('mouvements', $mouvement_data);

                } elseif ($payment_method_type == 'bank') {
                    $banque_id = $this->input->post('banque_id');
                    $banque = $this->db->where('id', $banque_id)->get('banks')->row();

                    if (!$banque) {
                        throw new Exception('Banque introuvable');
                    }

                    $old_balance = (float)($banque->balance ?? 0);
                    $new_balance = $old_balance + $amount_paid;

                    // Mettre à jour le solde de la banque
                    $this->db->where('id', $banque_id)
                        ->update('banks', [
                            'balance' => $new_balance,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                    // Enregistrer dans la table bank (opérations bancaires)
                    $bank_operation_data = [
                        'bank_id'          => $banque_id,
                        'date'             => date('Y-m-d H:i:s'),
                        'transaction_type' => 'Virement entrant',
                        'designation'      => 'Crédit',
                        'name'             => 'Vente point de vente #' . $quote_data['quote_number'],
                        'nom'              => $this->getCustomerName($customer_id),
                        'amount'           => $amount_paid,
                        'reference'        => $payment_reference,
                        'payment_mode'     => 'bank',
                        'note'             => 'Paiement vente',
                        'created_at'       => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('bank', $bank_operation_data);
                    $bank_operation_id = $this->db->insert_id();

                    // Enregistrer dans mouvements
                    $mouvement_data = [
                        'type_mouvement'         => 'entree',
                        'montant'                => $amount_paid,
                        'description'            => 'Vente point de vente #' . $quote_data['quote_number'],
                        'reference'              => $payment_reference,
                        'date_mouvement'         => date('Y-m-d'),
                        'mode_paiement'          => 'bank',
                        'created_at'             => date('Y-m-d H:i:s'),
                        'bank_operation_id'      => $bank_operation_id,
                        'solde_avant_operation'  => $old_balance,
                        'solde_apres_operation'  => $new_balance
                    ];
                    $this->db->insert('mouvements', $mouvement_data);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                throw new Exception('Erreur lors de l\'enregistrement de la vente');
            }

            $response['status'] = 'success';
            $response['message'] = 'Vente enregistrée avec succès';
            $response['quote_id'] = $quote_id;
            $response['redirect_url'] = base_url('admin/selling');

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $response['message'] = $e->getMessage();
            log_message('error', 'Selling Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    /**
     * Calcule les totaux avec la nouvelle formule TVA
     * Prix TTC saisi -> déduction TVA -> HT -> remises -> TVA sur HT -> TTC final
     */


    /**
     * Prépare les articles avec la nouvelle formule TVA
     */
    /**
     * Calcule les totaux avec la nouvelle formule TVA
     * Prix TTC saisi -> déduction TVA -> HT -> remises -> TVA sur HT -> TTC final
     */
    private function calculateTotalsWithNewTVAFormula($quantities, $prices_ttc, $discounts, $discount_types, $tax_rate)
    {
        $total_ttc_brut = 0;
        $total_ht_brut = 0;
        $total_discount = 0;
        $total_ht_final = 0;

        foreach ($quantities as $i => $qty) {
            $qty = floatval($qty);
            $price_ttc = floatval($prices_ttc[$i]);
            $discount = floatval($discounts[$i] ?? 0);
            $discount_type = $discount_types[$i] ?? 'percent';

            // Calcul du prix HT à partir du prix TTC
            $price_ht = $price_ttc;
            if ($tax_rate > 0) {
                $price_ht = $price_ttc / (1 + $tax_rate);
            }

            // Application de la remise sur le prix HT
            $discount_amount_unit = 0;
            if ($discount_type === 'percent') {
                $discount_amount_unit = $price_ht * ($discount / 100);
            } else {
                $discount_amount_unit = min($discount, $price_ht);
            }

            $unit_price_ht_net = $price_ht - $discount_amount_unit;

            // Totaux par ligne (arrondis immédiatement)
            $line_ttc_brut = round($price_ttc * $qty, 2);
            $line_ht_brut = round($price_ht * $qty, 2);
            $line_discount_total = round($discount_amount_unit * $qty, 2);
            $line_ht_final = round($unit_price_ht_net * $qty, 2);

            $total_ttc_brut += $line_ttc_brut;
            $total_ht_brut += $line_ht_brut;
            $total_discount += $line_discount_total;
            $total_ht_final += $line_ht_final;
        }

        // Remise globale
        $global_discount_amount = floatval($this->input->post('global_discount_amount') ?: 0);
        $global_discount_type = $this->input->post('global_discount_type') ?: 'percent';
        $global_discount = 0;

        if ($global_discount_amount > 0) {
            if ($global_discount_type === 'percent') {
                $global_discount = round($total_ht_final * ($global_discount_amount / 100), 2);
            } else {
                $global_discount = min($global_discount_amount, $total_ht_final);
            }
        }

        $total_discount = round($total_discount + $global_discount, 2);
        $total_ht_final = round(max($total_ht_final - $global_discount, 0), 2);

        return [
            'total_ttc_brut' => $total_ttc_brut,
            'total_ht_brut' => $total_ht_brut,
            'total_discount' => $total_discount,
            'total_ht_final' => $total_ht_final
        ];
    }

    /**
     * Prépare les articles avec la nouvelle formule TVA
     */
    private function prepareQuoteItemsWithNewTVAFormula($quote_id, $categories, $items, $quantities, $prices_ttc, $units, $discounts, $discount_types, $tax_rate)
    {
        $quote_items = [];

        foreach ($items as $i => $item_name) {
            $category_name = $categories[$i];
            $category_id = $this->getOrCreateCategory($category_name);
            $item_id = $this->getOrCreateItem($item_name, $category_id, $units[$i] ?? '', $prices_ttc[$i]);

            $qty = floatval($quantities[$i]);
            $price_ttc = floatval($prices_ttc[$i]);
            $discount = floatval($discounts[$i] ?? 0);
            $discount_type = $discount_types[$i] ?? 'percent';

            // Calcul du prix HT
            $price_ht = $price_ttc;
            if ($tax_rate > 0) {
                $price_ht = $price_ttc / (1 + $tax_rate);
            }

            // Application de la remise
            $discount_amount_unit = 0;
            if ($discount_type === 'percent') {
                $discount_amount_unit = $price_ht * ($discount / 100);
            } else {
                $discount_amount_unit = min($discount, $price_ht);
            }

            $unit_price_ht_net = $price_ht - $discount_amount_unit;

            $line_ttc_brut = round($price_ttc * $qty, 2);
            $line_ht_brut = round($price_ht * $qty, 2);
            $line_discount_total = round($discount_amount_unit * $qty, 2);
            $line_ht_final = round($unit_price_ht_net * $qty, 2);

            $quote_items[] = [
                'quote_id' => $quote_id,
                'category_id' => $category_id,
                'item_id' => $item_id,
                'category_name' => $category_name,
                'item_name' => $item_name,
                'quantity' => $qty,
                'unit_price_ttc' => round($price_ttc, 2),
                'unit_price_ht' => round($price_ht, 2),
                'unit' => $units[$i] ?? '',
                'discount_type' => $discount_type,
                'discount' => $discount,
                'discount_amount' => $line_discount_total,
                'line_total' => $line_ht_brut,
                'line_total_after_discount' => $line_ht_final,
                'line_total_ttc_brut' => $line_ttc_brut,
                'line_total_ht_brut' => $line_ht_brut,
                'line_total_ht_final' => $line_ht_final,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }

        return $quote_items;
    }


    /**
     * Récupère le nom du client par son ID
     */
    private function getCustomerName($customer_id)
    {
        $customer = $this->db->select('item_supplier, lastname')
            ->where('id', $customer_id)
            ->get('clients')
            ->row();

        if ($customer) {
            return $customer->item_supplier . ' ' . ($customer->lastname ?? '');
        }

        return 'Client';
    }





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
            $originalQuote = $this->quote_selling_model->getQuoteWithItems($id);
            if (!$originalQuote) {
                throw new Exception('Devis original non trouvé');
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
                throw new Exception('Erreur lors de la duplication du devis');
            }

            $response['success'] = true;
            $response['message'] = 'Devis dupliqué avec succès';
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

        $this->db->insert('quotes_selling', $quoteData);
        return $this->db->insert_id();
    }

    /**
     * Duplique les lignes du devis
     */
    private function duplicateQuoteItems($originalQuoteId, $newQuoteId)
    {
        // Récupérer les lignes originales
        $originalItems = $this->db
            ->where('quote_id', $originalQuoteId)
            ->get('quote_items_selling')
            ->result_array();

        if (empty($originalItems)) {
            return;
        }

        $newItems = [];
        foreach ($originalItems as $item) {
            $newItems[] = [
                'quote_id'                  => $newQuoteId,
                'category_id'               => $item['category_id'],
                'item_id'                   => $item['item_id'],
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

        $this->db->insert_batch('quote_items_selling', $newItems);
    }

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

        $global_discount_amount = floatval($this->input->post('global_discount_amount') ?: 0);
        $global_discount_type = $this->input->post('global_discount_type') ?: 'percent';
        $global_discount = 0;

        if ($global_discount_amount > 0) {
            $global_discount = ($global_discount_type === 'percent')
                ? $total_after_discount * ($global_discount_amount / 100)
                : min($global_discount_amount, $total_after_discount);
        }

        $final_total_discount = $total_discount + $global_discount;
        $final_total_after_discount = max($total_after_discount - $global_discount, 0);

        $tax_option = $this->input->post('tax_option');
        $tva_amount = 0;
        $other_tax_amount = 0;
        $total_tax_amount = 0;

        switch ($tax_option) {
            case 'tva':
                $tva_rate = floatval($this->input->post('tva_rate') ?: 18);
                $tva_amount = $final_total_after_discount * ($tva_rate / 100);
                $total_tax_amount = $tva_amount;
                break;

            case 'other':
                $other_tax_rate = floatval($this->input->post('other_tax_rate') ?: 0);
                $other_tax_amount = $final_total_after_discount * ($other_tax_rate / 100);
                $total_tax_amount = $other_tax_amount;
                break;

            case 'none':
            default:
                $tva_amount = 0;
                $other_tax_amount = 0;
                $total_tax_amount = 0;
                break;
        }

        $total_ttc = $final_total_after_discount + $total_tax_amount;

        return [
            'total_ht' => $total_ht,
            'total_discount' => $final_total_discount,
            'total_after_discount' => $final_total_after_discount,
            'tva_amount' => $tva_amount,
            'other_tax_amount' => $other_tax_amount,
            'total_tax_amount' => $total_tax_amount,
            'total_ttc' => $total_ttc
        ];
    }

    private function prepareQuoteItemsWithBossFormula($quote_id, $categories, $items, $quantities, $prices, $units, $discounts, $discount_types)
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

    private function getOrCreateCategory($category_name)
    {
        $category = $this->db->where('item_category', $category_name)->get('item_category')->row();

        if (!$category) {
            $this->db->insert('item_category', ['item_category' => $category_name]);
            return $this->db->insert_id();
        }

        return $category->id;
    }

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

            $stock_data = [
                'item_id'           => $item_id,
                'initial_quantity'  => 0,
                'current_quantity'  => 0,
                'created_at'        => date('Y-m-d H:i:s')
            ];
            $this->db->insert('stock', $stock_data);

            return $item_id;
        }

        return $item->id;
    }

    private function generateQuoteNumber()
    {
        $prefix = 'VENTE';
        $date = date('Ym');

        $this->db->like('quote_number', $prefix . '-' . $date, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('quotes_selling');

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
     * Affiche les détails d'un devis
     *
     * @param int $id ID du devis
     * @return void
     */

    public function view_selling($id)
    {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('pdv', 'can_view')) {
            access_denied();
        }

        // Récupération des données du devis
        $data['quote'] = $this->quote_selling_model->getQuoteWithItems($id);

        // Vérification si le devis existe
        if (!$data['quote']) {
            $this->session->set_flashdata('error', 'Devis non trouvé');
            redirect('admin/selling');
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
        $data['title'] = 'Détails du devis';
        $data['page_title'] = 'Détails du devis ' . $data['quote']['quote_number'];

        // Chargement des vues
        $this->load->view('layout/header', $data);
        $this->load->view('admin/quote/view_selling', $data);
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
        if (!$this->rbac->hasPrivilege('pdv', 'can_edit')) {
            access_denied();
        }

        $quote_id = $this->input->post('id', 0);

        // Initialisation de la réponse
        $response = ['status' => 'fail', 'message' => ''];

        try {
            // Récupération des données du devis
            $data['quote'] = $this->quote_model->getQuoteWithItems($quote_id);
            if (!$data['quote']) {
                throw new Exception('Devis introuvable');
            }

            // VÉRIFICATION DE L'UTILISATEUR ET DU RÔLE
            $current_user = $this->session->userdata('admin')['username'];
            $role_id = $this->session->userdata('admin')['role_id'];
            $is_admin = ($role_id == 7 || $role_id == 1);

            if (!$is_admin && $data['quote']['user_name'] != $current_user) {
                throw new Exception('Vous n\'avez pas accès à ce devis');
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

                $this->mailsmsconf->mailsms('send_quote', $quote_detail);
            }

            $response['status'] = 'success';
            $response['message'] = 'Le devis a été envoyé avec succès';

        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Quote Email Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    public function delete($id)
    {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('pdv', 'can_delete')) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Accès refusé']);
                return;
            }
            access_denied();
        }

        try {
            // Vérifier que le devis existe
            $quote = $this->quote_model->getQuoteWithItems($id);
            if (!$quote) {
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'error', 'message' => 'Devis introuvable']);
                    return;
                }
                $this->session->set_flashdata('error', 'Devis introuvable');
                redirect('admin/quoteitem');
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
                    echo json_encode(['status' => 'error', 'message' => 'Ce devis ne peut pas être supprimé']);
                    return;
                }
                $this->session->set_flashdata('error', 'Ce devis ne peut pas être supprimé');
                redirect('admin/quoteitem');
            }

            // Suppression transactionnelle
            $this->db->trans_start();

            // Supprimer les articles liés
            $this->db->where('quote_id', $id)->delete('quote_items');

            // Supprimer le devis
            $this->db->where('id', $id)->delete('quotes');

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                throw new Exception("Erreur lors de la suppression du devis");
            }

            // Réponse pour AJAX
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'Devis supprimé avec succès']);
                return;
            }

            // Réponse pour requête normale
            $this->session->set_flashdata('success', 'Devis supprimé avec succès');
            redirect('admin/quoteitem');

        } catch (Exception $e) {
            log_message('error', 'Quote Delete Error: ' . $e->getMessage());

            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Une erreur est survenue lors de la suppression du devis']);
                return;
            }

            $this->session->set_flashdata('error', 'Une erreur est survenue lors de la suppression du devis');
            redirect('admin/quoteitem');
        }
    }



    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('pdv', 'can_edit')) {
            access_denied();
        }

        try {
            $quote = $this->quote_selling_model->getQuoteWithItems($id);
            if (!$quote) {
                throw new Exception('Devis introuvable');
            }

            // Récupération des caisses et banques
            $caisses = $this->db->where('est_actif', 1)->where('is_deleted', 'no')->get('income')->result();
            $banques = $this->db->where('status', 1)->get('banks')->result();

            $data = [
                'title'        => 'Modifier le devis',
                'quote'        => $quote,
                'itemcatlist'  => $this->itemcategory_model->get(),
                'itemList'     => $this->item_model->get(),
                'clients'      => $this->clients_model->get(),
                'caisses'      => $caisses,
                'banques'      => $banques,
            ];

            $this->load->view('layout/header', $data);
            $this->load->view('admin/quote/edit_selling', $data);
            $this->load->view('layout/footer', $data);

        } catch (Exception $e) {
            log_message('error', 'Quote Edit Error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Une erreur est survenue lors de l\'édition du devis');
            redirect('admin/selling');
        }
    }





    /**
     * Met à jour un devis existant
     *
     * @param int $id ID du devis
     * @return void
     */

    public function update_22()
    {
        $response = ['status' => 'fail', 'message' => '', 'error' => []];

        try {
            if (!$this->input->post()) {
                throw new Exception('Aucune donnée reçue');
            }

            $quote_id = $this->input->post('id');

            // Vérifier que le devis existe
            $old_quote = $this->quote_selling_model->getQuoteWithItems($quote_id);
            if (!$old_quote) {
                throw new Exception('Devis introuvable');
            }

            // Validation des champs obligatoires
            $this->form_validation->set_rules('customer', 'Client', 'required|trim');
            $this->form_validation->set_rules('quote_date', 'Date', 'required');
            $this->form_validation->set_rules('item_category[]', 'Catégorie', 'required');
            $this->form_validation->set_rules('item_name[]', 'Article', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantité', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('price[]', 'Prix TTC', 'required|numeric|greater_than[0]');

            // Validation taxe personnalisée
            $tax_option = $this->input->post('tax_option');
            if ($tax_option === 'other') {
                $this->form_validation->set_rules('other_tax_name', 'Nom de la taxe', 'required|trim');
                $this->form_validation->set_rules('other_tax_rate', 'Taux de taxe', 'required|numeric|greater_than[0]|less_than[101]');
            }

            if ($this->form_validation->run() == false) {
                $response['error'] = $this->form_validation->error_array();
                echo json_encode($response);
                return;
            }

            // Récupération des données
            $categories     = $this->input->post('item_category');
            $items          = $this->input->post('item_name');
            $quantities     = $this->input->post('quantity');
            $prices_ttc     = $this->input->post('price');
            $units          = $this->input->post('unit') ?: [];
            $discounts      = $this->input->post('discount') ?: [];
            $discount_types = $this->input->post('discount_type') ?: [];

            // Gestion du client (nouveau ou existant)
            $customer_id = $this->input->post('customer');
            if ($customer_id === "new") {
                $customer_id = $this->createNewClient();
            }

            // Taux de taxe
            $tax_rate = 0;
            switch ($tax_option) {
                case 'tva':   $tax_rate = 0.18; break;
                case 'other': $tax_rate = floatval($this->input->post('other_tax_rate') ?: 0) / 100; break;
                default:      $tax_rate = 0; break;
            }

            // Calcul des totaux
            $totals = $this->calculateTotalsWithNewTVAFormula($quantities, $prices_ttc, $discounts, $discount_types, $tax_rate);

            // Montants des taxes
            $other_tax_name = '';
            $other_tax_rate = 0;
            $tva_amount = 0;
            $tax_amount = 0;

            switch ($tax_option) {
                case 'tva':
                    $tax_amount = $totals['total_ht_final'] * 0.18;
                    $tva_amount = $tax_amount;
                    break;
                case 'other':
                    $other_tax_name = $this->input->post('other_tax_name');
                    $other_tax_rate = $this->input->post('other_tax_rate');
                    $tax_amount = $totals['total_ht_final'] * ($other_tax_rate / 100);
                    break;
                default:
                    $tax_amount = 0;
                    break;
            }

            $total_ttc_final = $totals['total_ht_final'] + $tax_amount;

            // Arrondis
            $totals['total_ttc_brut'] = round($totals['total_ttc_brut'], 2);
            $totals['total_ht_brut']  = round($totals['total_ht_brut'], 2);
            $totals['total_discount'] = round($totals['total_discount'], 2);
            $totals['total_ht_final'] = round($totals['total_ht_final'], 2);
            $tax_amount               = round($tax_amount, 2);
            $total_ttc_final          = round($total_ttc_final, 2);

            // Paiement
            $amount_paid = round(floatval($this->input->post('amount_paid') ?: 0), 2);
            $remaining_amount = max(round($total_ttc_final - $amount_paid, 2), 0);
            $change_amount = ($amount_paid > $total_ttc_final) ? round($amount_paid - $total_ttc_final, 2) : 0;
            $payment_method_type = $this->input->post('payment_method_type');

            if ($amount_paid >= $total_ttc_final) {
                $payment_status = 'paid';
                $sale_status = 2;
                $remaining_amount = 0;
                $change_amount = round($amount_paid - $total_ttc_final, 2);
                $amount_paid = $total_ttc_final;
            } elseif ($amount_paid > 0) {
                $payment_status = 'partial';
                $sale_status = 2;
            } else {
                $payment_status = 'pending';
                $sale_status = 2;
            }

            // Mise à jour du devis
            $quote_data = [
                'customer_id'           => $customer_id,
                'user_name'             => $this->input->post('user_name'),
                'objet'                 => $this->input->post('objet'),
                'payment_method'        => $this->input->post('payment_method'),
                'payment_method_type'   => $payment_method_type,
                'quote_date'            => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('quote_date')))),
                'valid_until'           => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('valid_until')))),
                'payment_terms'         => $this->input->post('payment_terms'),
                'delivery_terms'        => $this->input->post('delivery_terms'),
                'delivery_location'     => $this->input->post('delivery_location'),
                'tax_option'            => $tax_option,
                'tva_rate'              => ($tax_option == 'tva') ? 18 : 0,
                'tva_amount'            => $tva_amount,
                'other_tax_name'        => $other_tax_name,
                'other_tax_rate'        => $other_tax_rate,
                'other_tax_amount'      => ($tax_option == 'other') ? $tax_amount : 0,
                'total_ttc_brut'        => $totals['total_ttc_brut'],
                'total_ht_brut'         => $totals['total_ht_brut'],
                'total_discount'        => $totals['total_discount'],
                'total_ht'              => $totals['total_ht_final'],
                'total_tax_amount'      => $tax_amount,
                'total_ttc'             => $total_ttc_final,
                'global_discount_type'  => $this->input->post('global_discount_type'),
                'global_discount_amount'=> $this->input->post('global_discount_amount') ?: 0,
                'amount_paid'           => $amount_paid,
                'remaining_amount'      => $remaining_amount,
                'change_amount'         => $change_amount,
                'payment_status'        => $payment_status,
                'status'                => $sale_status,
                'updated_at'            => date('Y-m-d H:i:s')
            ];

            // Transaction
            $this->db->trans_start();

            // Mise à jour du devis
            $this->db->where('id', $quote_id)->update('quotes_selling', $quote_data);

            // Supprimer les anciens articles
            $this->db->where('quote_id', $quote_id)->delete('quote_items_selling');

            // Recréer les articles (avec unit_price_ttc)
            $quote_items = $this->prepareQuoteItemsWithNewTVAFormula(
                $quote_id, $categories, $items, $quantities, $prices_ttc, $units, $discounts, $discount_types, $tax_rate
            );
            if (!empty($quote_items)) {
                $this->db->insert_batch('quote_items_selling', $quote_items);
            }

            // Gestion du paiement (annulation ancien, création nouveau)
            $old_payment = $this->db->select('amount_paid, payment_method_type, caisse_id, banque_id')
                ->where('id', $quote_id)
                ->get('quotes_selling')
                ->row();

            // Annuler l'ancien paiement si existant
            if ($old_payment && $old_payment->amount_paid > 0) {
                // Logique d'annulation (sortie de caisse/banque)
                if ($old_payment->payment_method_type == 'cash' && $old_payment->caisse_id) {
                    $caisse = $this->db->where('id', $old_payment->caisse_id)->get('income')->row();
                    if ($caisse) {
                        $new_balance = (float)$caisse->amount_re - $old_payment->amount_paid;
                        $this->db->where('id', $old_payment->caisse_id)->update('income', ['amount_re' => $new_balance]);
                    }
                } elseif ($old_payment->payment_method_type == 'bank' && $old_payment->banque_id) {
                    $banque = $this->db->where('id', $old_payment->banque_id)->get('banks')->row();
                    if ($banque) {
                        $new_balance = (float)$banque->balance - $old_payment->amount_paid;
                        $this->db->where('id', $old_payment->banque_id)->update('banks', ['balance' => $new_balance]);
                    }
                }
            }

            // Créer le nouveau paiement
            if ($amount_paid > 0) {
                $payment_reference = 'VENTE-EDIT-' . date('YmdHis') . '-' . $quote_id;
                if ($payment_method_type == 'cash') {
                    $caisse_id = $this->input->post('caisse_id');
                    $caisse = $this->db->where('id', $caisse_id)->get('income')->row();
                    if ($caisse) {
                        $old_balance = (float)$caisse->amount_re;
                        $new_balance = $old_balance + $amount_paid;
                        $this->db->where('id', $caisse_id)->update('income', ['amount_re' => $new_balance]);
                        // Enregistrement opération caisse (optionnel mais recommandé)
                    }
                } elseif ($payment_method_type == 'bank') {
                    $banque_id = $this->input->post('banque_id');
                    $banque = $this->db->where('id', $banque_id)->get('banks')->row();
                    if ($banque) {
                        $old_balance = (float)$banque->balance;
                        $new_balance = $old_balance + $amount_paid;
                        $this->db->where('id', $banque_id)->update('banks', ['balance' => $new_balance]);
                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                throw new Exception('Erreur lors de la mise à jour du devis');
            }

            $response['status'] = 'success';
            $response['message'] = 'Vente mise à jour avec succès';
            $response['redirect_url'] = base_url('admin/selling');

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $response['message'] = $e->getMessage();
            log_message('error', 'Selling Update Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }
    public function update()
    {
        $response = ['status' => 'fail', 'message' => '', 'error' => []];

        try {
            if (!$this->input->post()) {
                throw new Exception('Aucune donnée reçue');
            }

            $quote_id = $this->input->post('id');

            // Vérifier que le devis existe
            $old_quote = $this->quote_selling_model->getQuoteWithItems($quote_id);
            if (!$old_quote) {
                throw new Exception('Devis introuvable');
            }

            // Validation des champs obligatoires
            $this->form_validation->set_rules('customer', 'Client', 'required|trim');
            $this->form_validation->set_rules('quote_date', 'Date', 'required');
            $this->form_validation->set_rules('item_category[]', 'Catégorie', 'required');
            $this->form_validation->set_rules('item_name[]', 'Article', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantité', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('price[]', 'Prix TTC', 'required|numeric|greater_than[0]');

            $tax_option = $this->input->post('tax_option');
            if ($tax_option === 'other') {
                $this->form_validation->set_rules('other_tax_name', 'Nom de la taxe', 'required|trim');
                $this->form_validation->set_rules('other_tax_rate', 'Taux de taxe', 'required|numeric|greater_than[0]|less_than[101]');
            }

            if ($this->form_validation->run() == false) {
                $response['error'] = $this->form_validation->error_array();
                echo json_encode($response);
                return;
            }

            // Récupération des données
            $categories     = $this->input->post('item_category');
            $items          = $this->input->post('item_name');
            $quantities     = $this->input->post('quantity');
            $prices_ttc     = $this->input->post('price');
            $units          = $this->input->post('unit') ?: [];
            $discounts      = $this->input->post('discount') ?: [];
            $discount_types = $this->input->post('discount_type') ?: [];

            // Gestion du client (nouveau ou existant)
            $customer_id = $this->input->post('customer');
            if ($customer_id === "new") {
                $customer_id = $this->createNewClient();
            }

            // Taux de taxe
            $tax_rate = 0;
            switch ($tax_option) {
                case 'tva':   $tax_rate = 0.18; break;
                case 'other': $tax_rate = floatval($this->input->post('other_tax_rate') ?: 0) / 100; break;
                default:      $tax_rate = 0; break;
            }

            // Calcul des totaux
            $totals = $this->calculateTotalsWithNewTVAFormula($quantities, $prices_ttc, $discounts, $discount_types, $tax_rate);

            // Montants des taxes
            $other_tax_name = '';
            $other_tax_rate = 0;
            $tva_amount = 0;
            $tax_amount = 0;

            switch ($tax_option) {
                case 'tva':
                    $tax_amount = $totals['total_ht_final'] * 0.18;
                    $tva_amount = $tax_amount;
                    break;
                case 'other':
                    $other_tax_name = $this->input->post('other_tax_name');
                    $other_tax_rate = $this->input->post('other_tax_rate');
                    $tax_amount = $totals['total_ht_final'] * ($other_tax_rate / 100);
                    break;
                default:
                    $tax_amount = 0;
                    break;
            }

            $total_ttc_final = $totals['total_ht_final'] + $tax_amount;

            // Arrondis
            $totals['total_ttc_brut'] = round($totals['total_ttc_brut'], 2);
            $totals['total_ht_brut']  = round($totals['total_ht_brut'], 2);
            $totals['total_discount'] = round($totals['total_discount'], 2);
            $totals['total_ht_final'] = round($totals['total_ht_final'], 2);
            $tax_amount               = round($tax_amount, 2);
            $total_ttc_final          = round($total_ttc_final, 2);

            // Paiement
            $amount_paid = round(floatval($this->input->post('amount_paid') ?: 0), 2);
            $remaining_amount = max(round($total_ttc_final - $amount_paid, 2), 0);
            $change_amount = ($amount_paid > $total_ttc_final) ? round($amount_paid - $total_ttc_final, 2) : 0;
            $payment_method_type = $this->input->post('payment_method_type');
            $caisse_id = $this->input->post('caisse_id');
            $banque_id = $this->input->post('banque_id');

            if ($amount_paid >= $total_ttc_final) {
                $payment_status = 'paid';
                $sale_status = 2;
                $remaining_amount = 0;
                $change_amount = round($amount_paid - $total_ttc_final, 2);
                $amount_paid = $total_ttc_final;
            } elseif ($amount_paid > 0) {
                $payment_status = 'partial';
                $sale_status = 2;
            } else {
                $payment_status = 'pending';
                $sale_status = 2;
            }

            // Mise à jour du devis
            $quote_data = [
                'customer_id'           => $customer_id,
                'user_name'             => $this->input->post('user_name'),
                'objet'                 => $this->input->post('objet'),
                'payment_method'        => $this->input->post('payment_method'),
                'payment_method_type'   => $payment_method_type,
                'caisse_id'             => ($payment_method_type == 'cash') ? $caisse_id : null,
                'banque_id'             => ($payment_method_type == 'bank') ? $banque_id : null,
                'quote_date'            => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('quote_date')))),
                'valid_until'           => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('valid_until')))),
                'payment_terms'         => $this->input->post('payment_terms'),
                'delivery_terms'        => $this->input->post('delivery_terms'),
                'delivery_location'     => $this->input->post('delivery_location'),
                'tax_option'            => $tax_option,
                'tva_rate'              => ($tax_option == 'tva') ? 18 : 0,
                'tva_amount'            => $tva_amount,
                'other_tax_name'        => $other_tax_name,
                'other_tax_rate'        => $other_tax_rate,
                'other_tax_amount'      => ($tax_option == 'other') ? $tax_amount : 0,
                'total_ttc_brut'        => $totals['total_ttc_brut'],
                'total_ht_brut'         => $totals['total_ht_brut'],
                'total_discount'        => $totals['total_discount'],
                'total_ht'              => $totals['total_ht_final'],
                'total_tax_amount'      => $tax_amount,
                'total_ttc'             => $total_ttc_final,
                'global_discount_type'  => $this->input->post('global_discount_type'),
                'global_discount_amount'=> $this->input->post('global_discount_amount') ?: 0,
                'amount_paid'           => $amount_paid,
                'remaining_amount'      => $remaining_amount,
                'change_amount'         => $change_amount,
                'payment_status'        => $payment_status,
                'status'                => $sale_status,
                'updated_at'            => date('Y-m-d H:i:s')
            ];

            // Démarrer la transaction
            $this->db->trans_start();

            // 1. Mise à jour du devis principal
            $this->db->where('id', $quote_id)->update('quotes_selling', $quote_data);

            // 2. Supprimer les anciens articles
            $this->db->where('quote_id', $quote_id)->delete('quote_items_selling');

            // 3. Recréer les articles (avec unit_price_ttc)
            $quote_items = $this->prepareQuoteItemsWithNewTVAFormula(
                $quote_id, $categories, $items, $quantities, $prices_ttc, $units, $discounts, $discount_types, $tax_rate
            );
            if (!empty($quote_items)) {
                $this->db->insert_batch('quote_items_selling', $quote_items);
            }

            // 4. Gestion complète du paiement (annulation ancien + création nouveau)
            $old_payment = $this->db->select('amount_paid, payment_method_type, caisse_id, banque_id')
                ->where('id', $quote_id)
                ->get('quotes_selling')
                ->row();

            // --- Annulation de l'ancien paiement (sortie) ---
            if ($old_payment && $old_payment->amount_paid > 0) {
                $ref_annul = 'ANNUL-MODIF-' . date('YmdHis') . '-' . $quote_id;

                if ($old_payment->payment_method_type == 'cash' && $old_payment->caisse_id) {
                    $caisse = $this->db->where('id', $old_payment->caisse_id)->get('income')->row();
                    if ($caisse) {
                        $old_balance = (float)$caisse->amount_re;
                        $new_balance = $old_balance - $old_payment->amount_paid;
                        $this->db->where('id', $old_payment->caisse_id)->update('income', ['amount_re' => $new_balance]);

                        // Opération de sortie dans operation_caisse
                        $this->db->insert('operation_caisse', [
                            'reference'              => $ref_annul,
                            'type_operation'         => 'sortie',
                            'montant'                => $old_payment->amount_paid,
                            'designation'            => 'Annulation ancien paiement - Vente #' . $old_quote['quote_number'],
                            'caisse_id'              => $old_payment->caisse_id,
                            'date'                   => date('Y-m-d H:i:s'),
                            'entree'                 => 0,
                            'sortie'                 => $old_payment->amount_paid,
                            'note'                   => 'Annulation suite modification',
                            'est_actif'              => 1,
                            'created_at'             => date('Y-m-d H:i:s'),
                            'category'               => 'Annulation vente',
                            'solde_avant_operation'  => $old_balance,
                            'solde_apres_operation'  => $new_balance
                        ]);
                        $op_id = $this->db->insert_id();

                        // Mouvement correspondant
                        $this->db->insert('mouvements', [
                            'type_mouvement'         => 'sortie',
                            'montant'                => $old_payment->amount_paid,
                            'description'            => 'Annulation paiement vente',
                            'reference'              => $ref_annul,
                            'date_mouvement'         => date('Y-m-d'),
                            'mode_paiement'          => 'cash',
                            'created_at'             => date('Y-m-d H:i:s'),
                            'operation_id'           => $op_id,
                            'solde_avant_operation'  => $old_balance,
                            'solde_apres_operation'  => $new_balance
                        ]);
                    }
                } elseif ($old_payment->payment_method_type == 'bank' && $old_payment->banque_id) {
                    $banque = $this->db->where('id', $old_payment->banque_id)->get('banks')->row();
                    if ($banque) {
                        $old_balance = (float)$banque->balance;
                        $new_balance = $old_balance - $old_payment->amount_paid;
                        $this->db->where('id', $old_payment->banque_id)->update('banks', ['balance' => $new_balance]);

                        // Opération bancaire (sortie)
                        $this->db->insert('bank', [
                            'bank_id'          => $old_payment->banque_id,
                            'date'             => date('Y-m-d H:i:s'),
                            'transaction_type' => 'Virement sortant',
                            'designation'      => 'Débit',
                            'name'             => 'Annulation paiement vente',
                            'nom'              => $this->getCustomerName($customer_id),
                            'amount'           => -$old_payment->amount_paid,
                            'reference'        => $ref_annul,
                            'payment_mode'     => 'bank',
                            'note'             => 'Annulation modification',
                            'created_at'       => date('Y-m-d H:i:s')
                        ]);
                        $bank_op_id = $this->db->insert_id();

                        // Mouvement
                        $this->db->insert('mouvements', [
                            'type_mouvement'         => 'sortie',
                            'montant'                => $old_payment->amount_paid,
                            'description'            => 'Annulation paiement vente',
                            'reference'              => $ref_annul,
                            'date_mouvement'         => date('Y-m-d'),
                            'mode_paiement'          => 'bank',
                            'created_at'             => date('Y-m-d H:i:s'),
                            'bank_operation_id'      => $bank_op_id,
                            'solde_avant_operation'  => $old_balance,
                            'solde_apres_operation'  => $new_balance
                        ]);
                    }
                }
            }

            // --- Création du nouveau paiement (entrée) ---
            if ($amount_paid > 0) {
                $payment_reference = 'VENTE-EDIT-' . date('YmdHis') . '-' . $quote_id;

                if ($payment_method_type == 'cash' && $caisse_id) {
                    $caisse = $this->db->where('id', $caisse_id)->get('income')->row();
                    if (!$caisse) throw new Exception('Caisse introuvable');

                    $old_balance = (float)$caisse->amount_re;
                    $new_balance = $old_balance + $amount_paid;
                    $this->db->where('id', $caisse_id)->update('income', ['amount_re' => $new_balance]);

                    $this->db->insert('operation_caisse', [
                        'reference'              => $payment_reference,
                        'type_operation'         => 'entree',
                        'montant'                => $amount_paid,
                        'designation'            => 'Modification vente #' . $old_quote['quote_number'],
                        'caisse_id'              => $caisse_id,
                        'date'                   => date('Y-m-d H:i:s'),
                        'entree'                 => $amount_paid,
                        'sortie'                 => 0,
                        'note'                   => 'Paiement après modification',
                        'est_actif'              => 1,
                        'created_at'             => date('Y-m-d H:i:s'),
                        'category'               => 'Vente de produit',
                        'solde_avant_operation'  => $old_balance,
                        'solde_apres_operation'  => $new_balance
                    ]);
                    $op_id = $this->db->insert_id();

                    $this->db->insert('mouvements', [
                        'type_mouvement'         => 'entree',
                        'montant'                => $amount_paid,
                        'description'            => 'Paiement vente modifiée',
                        'reference'              => $payment_reference,
                        'date_mouvement'         => date('Y-m-d'),
                        'mode_paiement'          => 'cash',
                        'created_at'             => date('Y-m-d H:i:s'),
                        'operation_id'           => $op_id,
                        'solde_avant_operation'  => $old_balance,
                        'solde_apres_operation'  => $new_balance
                    ]);

                } elseif ($payment_method_type == 'bank' && $banque_id) {
                    $banque = $this->db->where('id', $banque_id)->get('banks')->row();
                    if (!$banque) throw new Exception('Banque introuvable');

                    $old_balance = (float)$banque->balance;
                    $new_balance = $old_balance + $amount_paid;
                    $this->db->where('id', $banque_id)->update('banks', ['balance' => $new_balance]);

                    $this->db->insert('bank', [
                        'bank_id'          => $banque_id,
                        'date'             => date('Y-m-d H:i:s'),
                        'transaction_type' => 'Virement entrant',
                        'designation'      => 'Crédit',
                        'name'             => 'Vente modifiée #' . $old_quote['quote_number'],
                        'nom'              => $this->getCustomerName($customer_id),
                        'amount'           => $amount_paid,
                        'reference'        => $payment_reference,
                        'payment_mode'     => 'bank',
                        'note'             => 'Paiement modification',
                        'created_at'       => date('Y-m-d H:i:s')
                    ]);
                    $bank_op_id = $this->db->insert_id();

                    $this->db->insert('mouvements', [
                        'type_mouvement'         => 'entree',
                        'montant'                => $amount_paid,
                        'description'            => 'Paiement vente modifiée',
                        'reference'              => $payment_reference,
                        'date_mouvement'         => date('Y-m-d'),
                        'mode_paiement'          => 'bank',
                        'created_at'             => date('Y-m-d H:i:s'),
                        'bank_operation_id'      => $bank_op_id,
                        'solde_avant_operation'  => $old_balance,
                        'solde_apres_operation'  => $new_balance
                    ]);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                throw new Exception('Erreur lors de la mise à jour du devis');
            }

            $response['status'] = 'success';
            $response['message'] = 'Vente mise à jour avec succès';
            $response['redirect_url'] = base_url('admin/selling');

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $response['message'] = $e->getMessage();
            log_message('error', 'Selling Update Error: ' . $e->getMessage());
        }

        echo json_encode($response);
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
        if (!$this->rbac->hasPrivilege('pdv', 'can_edit')) {
            access_denied();
        }

        $id = $this->input->post('id');
        $order_number = $this->input->post('order_number');

        $response = ['status' => 'fail', 'message' => ''];

        try {
            // Vérifier que l'ID est valide
            if (empty($id)) {
                throw new Exception('ID du devis manquant');
            }

            // Vérification que le devis existe
            $quote = $this->quote_selling_model->getQuoteWithItems($id);
            if (!$quote) {
                throw new Exception('Devis non trouvé');
            }

            // ð¹ VÉRIFICATION DE L'UTILISATEUR ET DU RÔLE
            $current_user = $this->session->userdata('admin')['username'];
            $role_id = $this->session->userdata('admin')['role_id'];
            $is_admin = ($role_id == 7 || $role_id == 1);

            if (!$is_admin && $quote['user_name'] != $current_user) {
                throw new Exception('Vous n\'avez pas accès à ce devis');
            }

            // Vérification que le devis n'est pas déjà validé ou rejeté
            if ($this->quote_selling_model->isQuoteValidated($id)) {
                throw new Exception('Ce devis est déjà validé');
            }
            if ($this->quote_selling_model->isQuoteRejected($id)) {
                throw new Exception('Ce devis a été rejeté');
            }

            // Validation du devis
            $data = [
                'status' => Quote_model::STATUS_VALIDATED,
                'validated_at' => date('Y-m-d H:i:s'),
            ];

            if (!$this->quote_selling_model->validateQuote($id, $data)) {
                throw new Exception('Erreur lors de la validation du devis');
            }

            // Créer la commande à partir du devis
            $this->createOrderFromQuote($id, $order_number);

            // Créer la sortie de stock
            $this->createStockRemovalFromQuote($id);

            $response['status'] = 'success';
            $response['message'] = 'Devis validé avec succès';

        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
            log_message('error', 'Quote Validation Error: ' . $e->getMessage());
        }

        echo json_encode($response);
    }

    private function createOrderFromQuote($quote_id, $order_number)
    {
        // Récupération des données du devis
        $quote = $this->quote_selling_model->getQuoteWithItems($quote_id);
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
        $quote = $this->quote_selling_model->getQuoteWithItems($quote_id);
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
        if (!$this->rbac->hasPrivilege('pdv', 'can_edit')) {
            access_denied();
        }

        $id = $this->input->post('id');

        $response = ['status' => 'fail', 'message' => ''];

        try {
            // Vérification que le devis existe
            $quote = $this->quote_selling_model->getQuoteWithItems($id);
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
            if ($this->quote_selling_model->isQuoteValidated($id)) {
                throw new Exception('Ce devis est déjà validé');
            }
            if ($this->quote_selling_model->isQuoteRejected($id)) {
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

            if (!$this->quote_selling_model->rejectQuote($id, $data)) {
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
    public function print_selling()
    {
        $id = $this->input->post('id');

        // Récupération des données de la facture
        $data['quote_selling'] = $this->quote_selling_model->getQuoteWithItems($id);

        if (!$data['quote_selling']) {
            show_404();
            return;
        }

        // VÉRIFICATION DE L'UTILISATEUR ET DU RÔLE
       //$current_user = $this->session->userdata('admin')['username'];
       //$role_id = $this->session->userdata('admin')['role_id'];
       //$is_admin = ($role_id == 7 || $role_id == 1);
         //if (!$is_admin && $data['quote']['user_name'] != $current_user) {
          //echo json_encode(['status' => 'error', 'message' => 'Vous n\'avez pas accès à ce devis']);
         //return;
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
            $data['quote_selling']['quote_number'],
            $data['quote_selling']['customer_name']
        );
        $data['qrCodePath'] = $qrPath;

        // Chargement de la vue d'impression
        $invoice_page = $this->load->view('admin/quote/print_selling', $data, true);
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

    public function getListDataForAdmin()
    {
        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        $search = $this->input->post('search')['value'];
        $status = $this->input->post('status');

        $this->db->start_cache();

        $this->db->select('
        quotes_selling.*,
        clients.item_supplier as customer_name,
        clients.lastname as customer_last_name,
        clients.email as customer_email,
        clients.phone as customer_phone,
        clients.address as customer_address,
        -- 🔹 Ajout des champs de paiement
        quotes_selling.amount_paid,
        quotes_selling.change_amount,
        quotes_selling.remaining_amount,
        quotes_selling.payment_status,
        quotes_selling.payment_method_type
    ');
        $this->db->from('quotes_selling');
        $this->db->join('clients', 'clients.id = quotes_selling.customer_id', 'left');

        if($search) {
            $this->db->group_start();
            $this->db->like('quotes_selling.quote_number', $search);
            $this->db->or_like('quotes_selling.objet', $search);
            $this->db->or_like('clients.item_supplier', $search);
            $this->db->or_like('clients.email', $search);
            $this->db->group_end();
        }

        if($status !== '' && (int)$status > 0) {
            $this->db->where('quotes_selling.status', $status);
        }

        $this->db->stop_cache();

        // Compter le total
        $total_records = $this->db->count_all_results('quotes_selling', false);

        $filtered_records = $this->db->get()->num_rows();

        $this->db->order_by('quotes_selling.created_at', 'DESC');
        if($length != -1) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();
        $this->db->flush_cache();

        $status_labels = [
            1 => ['label' => 'En attente de validation', 'class' => 'label-warning'],
            2 => ['label' => 'Validé', 'class' => 'label-success'],
            3 => ['label' => 'Rejeté', 'class' => 'label-danger'],
            4 => ['label' => 'En cours de traitement', 'class' => 'label-info'],
            5 => ['label' => 'Livré', 'class' => 'label-success'],
            6 => ['label' => 'Annulé', 'class' => 'label-default']
        ];

        // 🔹 Labels pour le statut de paiement
        $payment_status_labels = [
            'pending' => ['label' => '⏳ En attente', 'class' => 'label-warning'],
            'partial' => ['label' => '💸 Paiement partiel', 'class' => 'label-info'],
            'paid' => ['label' => '✅ Payé', 'class' => 'label-success']
        ];

        $data = [];
        foreach($query->result() as $row) {
            $status_info = isset($status_labels[$row->status]) ? $status_labels[$row->status] : ['label' => 'Inconnu', 'class' => 'label-default'];

            // 🔹 Récupération des informations de paiement
            $amount_paid = isset($row->amount_paid) ? floatval($row->amount_paid) : 0;
            $change_amount = isset($row->change_amount) ? floatval($row->change_amount) : 0;
            $remaining_amount = isset($row->remaining_amount) ? floatval($row->remaining_amount) : 0;
            $payment_status = isset($row->payment_status) ? $row->payment_status : 'pending';

            $payment_status_info = isset($payment_status_labels[$payment_status]) ? $payment_status_labels[$payment_status] : ['label' => '⏳ En attente', 'class' => 'label-warning'];

            $data[] = [
                'id' => $row->id,
                'quote_number' => $row->quote_number,
                'designation' => $row->objet,
                'customer' => [
                    'name' => $row->customer_name,
                    'lastname' => $row->customer_last_name,
                    'email' => $row->customer_email,
                    'phone' => $row->customer_phone,
                    'address' => $row->customer_address
                ],
                'dates' => [
                    'creation' => date('d/m/Y', strtotime($row->created_at)),
                    'quote_date' => $row->quote_date ? date('d/m/Y', strtotime($row->quote_date)) : 'Non définie',
                    'valid_until' => $row->valid_until ? date('d/m/Y', strtotime($row->valid_until)) : 'Non définie',
                ],
                'amount' => [
                    'ht' => number_format($row->total_ht, 2, ',', ' '),
                    'tva' => number_format($row->tva_amount, 2, ',', ' '),
                    'ttc' => number_format($row->total_ttc, 2, ',', ' ')
                ],
                // 🔹 NOUVEAU : Informations de paiement
                'payment' => [
                    'amount_paid' => number_format($amount_paid, 2, ',', ' '),
                    'amount_paid_raw' => $amount_paid,
                    'change_amount' => number_format($change_amount, 2, ',', ' '),
                    'change_amount_raw' => $change_amount,
                    'remaining_amount' => number_format($remaining_amount, 2, ',', ' '),
                    'remaining_amount_raw' => $remaining_amount,
                    'payment_status' => $payment_status,
                    'payment_status_label' => $payment_status_info['label'],
                    'payment_status_class' => $payment_status_info['class'],
                    'payment_method_type' => isset($row->payment_method_type) ? $row->payment_method_type : null,
                ],
                'status' => [
                    'code' => $row->status,
                    'label' => $status_info['label'],
                    'class' => $status_info['class']
                ],
                'user_name' => $row->user_name
            ];
        }

        return json_encode([
            'draw' => $draw,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $filtered_records,
            'data' => $data
        ]);
    }

    /**
     * Imprime toutes les ventes d’un client en un seul bloc
     * Utilisation : POST /admin/selling/printAllByClient avec customer_id
     */
    public function printAllByClient() {
        if (!$this->rbac->hasPrivilege('pdv', 'can_view')) {
            access_denied();
        }

        $customer_id = $this->input->post('customer_id');
        if (!$customer_id) {
            echo json_encode(['status' => '0', 'message' => 'Aucun client sélectionné.', 'page' => '']);
            return;
        }

        // Récupérer les ventes du client
        $sales = $this->quote_selling_model->getSalesByCustomer($customer_id);
        if (empty($sales)) {
            echo json_encode(['status' => '0', 'message' => 'Ce client n’a aucune vente.', 'page' => '']);
            return;
        }

        // Récupérer les infos de l'entreprise
        $company = $this->setting_model->get();
        $company = $company[0];

        // Infos du client (depuis la première vente)
        $customer = [
            'name'    => $sales[0]['customer_name'] . ' ' . $sales[0]['customer_last_name'],
            'email'   => $sales[0]['customer_email'],
            'phone'   => $sales[0]['customer_phone'],
            'address' => $sales[0]['customer_address']
        ];

        $data = [
            'sales'      => $sales,
            'customer'   => $customer,
            'company'    => $company,
            'print_date' => date('d/m/Y H:i:s'),
            'title'      => 'Toutes les ventes de ' . $customer['name']
        ];

        // Générer la vue d'impression
        $page = $this->load->view('admin/quote/print_all_sales', $data, true);
        echo json_encode(['status' => '1', 'page' => $page]);
    }

// Méthode pour les utilisateurs normaux (identique mais avec filtre user_name)
    public function getListDataForUser($username)
    {
        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        $search = $this->input->post('search')['value'];
        $status = $this->input->post('status');

        $this->db->start_cache();

        $this->db->select('
        quotes_selling.*,
        clients.item_supplier as customer_name,
        clients.lastname as customer_last_name,
        clients.email as customer_email,
        clients.phone as customer_phone,
        clients.address as customer_address,
        quotes_selling.amount_paid,
        quotes_selling.change_amount,
        quotes_selling.remaining_amount,
        quotes_selling.payment_status,
        quotes_selling.payment_method_type
    ');
        $this->db->from('quotes_selling');
        $this->db->join('clients', 'clients.id = quotes_selling.customer_id', 'left');

        // 🔹 FILTRE PAR UTILISATEUR
        $this->db->where('quotes_selling.user_name', $username);

        if($search) {
            $this->db->group_start();
            $this->db->like('quotes_selling.quote_number', $search);
            $this->db->or_like('quotes_selling.objet', $search);
            $this->db->or_like('clients.item_supplier', $search);
            $this->db->or_like('clients.email', $search);
            $this->db->group_end();
        }

        if($status !== '' && (int)$status > 0) {
            $this->db->where('quotes_selling.status', $status);
        }

        $this->db->stop_cache();

        // Compter le total
        $this->db->where('quotes_selling.user_name', $username);
        $total_records = $this->db->count_all_results('quotes_selling', false);

        $filtered_records = $this->db->get()->num_rows();

        $this->db->order_by('quotes_selling.created_at', 'DESC');
        if($length != -1) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();
        $this->db->flush_cache();

        $status_labels = [
            1 => ['label' => 'En attente de validation', 'class' => 'label-warning'],
            2 => ['label' => 'Validé', 'class' => 'label-success'],
            3 => ['label' => 'Rejeté', 'class' => 'label-danger'],
            4 => ['label' => 'En cours de traitement', 'class' => 'label-info'],
            5 => ['label' => 'Livré', 'class' => 'label-success'],
            6 => ['label' => 'Annulé', 'class' => 'label-default']
        ];

        $payment_status_labels = [
            'pending' => ['label' => '⏳ En attente', 'class' => 'label-warning'],
            'partial' => ['label' => '💸 Paiement partiel', 'class' => 'label-info'],
            'paid' => ['label' => '✅ Payé', 'class' => 'label-success']
        ];

        $data = [];
        foreach($query->result() as $row) {
            $status_info = isset($status_labels[$row->status]) ? $status_labels[$row->status] : ['label' => 'Inconnu', 'class' => 'label-default'];

            $amount_paid = isset($row->amount_paid) ? floatval($row->amount_paid) : 0;
            $change_amount = isset($row->change_amount) ? floatval($row->change_amount) : 0;
            $remaining_amount = isset($row->remaining_amount) ? floatval($row->remaining_amount) : 0;
            $payment_status = isset($row->payment_status) ? $row->payment_status : 'pending';

            $payment_status_info = isset($payment_status_labels[$payment_status]) ? $payment_status_labels[$payment_status] : ['label' => '⏳ En attente', 'class' => 'label-warning'];

            $data[] = [
                'id' => $row->id,
                'quote_number' => $row->quote_number,
                'designation' => $row->objet,
                'customer' => [
                    'name' => $row->customer_name,
                    'lastname' => $row->customer_last_name,
                    'email' => $row->customer_email,
                    'phone' => $row->customer_phone,
                    'address' => $row->customer_address
                ],
                'dates' => [
                    'creation' => date('d/m/Y', strtotime($row->created_at)),
                    'quote_date' => $row->quote_date ? date('d/m/Y', strtotime($row->quote_date)) : 'Non définie',
                    'valid_until' => $row->valid_until ? date('d/m/Y', strtotime($row->valid_until)) : 'Non définie',
                ],
                'amount' => [
                    'ht' => number_format($row->total_ht, 2, ',', ' '),
                    'tva' => number_format($row->tva_amount, 2, ',', ' '),
                    'ttc' => number_format($row->total_ttc, 2, ',', ' ')
                ],
                'payment' => [
                    'amount_paid' => number_format($amount_paid, 2, ',', ' '),
                    'amount_paid_raw' => $amount_paid,
                    'change_amount' => number_format($change_amount, 2, ',', ' '),
                    'change_amount_raw' => $change_amount,
                    'remaining_amount' => number_format($remaining_amount, 2, ',', ' '),
                    'remaining_amount_raw' => $remaining_amount,
                    'payment_status' => $payment_status,
                    'payment_status_label' => $payment_status_info['label'],
                    'payment_status_class' => $payment_status_info['class'],
                    'payment_method_type' => isset($row->payment_method_type) ? $row->payment_method_type : null,
                ],
                'status' => [
                    'code' => $row->status,
                    'label' => $status_info['label'],
                    'class' => $status_info['class']
                ],
                'user_name' => $row->user_name
            ];
        }

        return json_encode([
            'draw' => $draw,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $filtered_records,
            'data' => $data
        ]);
    }

}