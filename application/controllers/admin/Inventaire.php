<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Inventaire extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('inventaire_model');
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
        $this->session->set_userdata('sub_menu', 'Inventaire/index');
        
        // Initialize page data
        $data = [
            'title' => 'Stock',
            'title_list' => 'Recent Stock',
        ];

        // Load views
        $this->load->view('layout/header', $data);
        $this->load->view('admin/inventaire/list', $data);
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
    /**
     * GET STOCK DATA
     * IN JSON FORMAT
     *
     * @return  JSON   $response
     */
    public function data()
    {
        try {
            // Vérification que le modèle existe
            if (!isset($this->stock_model)) {
                $this->load->model('stock_model');
            }

            // Récupération des données
            $result = $this->stock_model->getListData();

            // Vérification que $result est valide
            if ($result === false) {
                throw new Exception('Le modèle n\'a pas retourné de données');
            }

            // S'assurer que c'est du JSON valide
            json_decode($result);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('La réponse n\'est pas un JSON valide: ' . json_last_error_msg());
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output($result);

        } catch (Exception $e) {
            // Retourner une erreur JSON lisible par DataTables
            $error_response = [
                'draw' => intval($this->input->post('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ];

            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode($error_response));
        }
    }



    /**
     * GET STOCK DATA WITH PROFIT
     * IN JSON FORMAT
     */
    /**
     * GET FULL STOCK DATA WITH INITIAL, OUTPUT, AVAILABLE QUANTITIES AND PROFIT
     * IN JSON FORMAT FOR DATATABLES
     */
    public function get_full_stock_data()
    {
        try {
            if (!isset($this->stock_model)) {
                $this->load->model('stock_model');
            }

            $draw = intval($this->input->post('draw'));
            $start = intval($this->input->post('start'));
            $length = intval($this->input->post('length'));
            $search = $this->input->post('search')['value'] ?? '';
            $show_zero_stock = $this->input->post('show_zero_stock') == '1';

            $result = $this->stock_model->getFullStockData($show_zero_stock, $search, $start, $length, $draw);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($result));

        } catch (Exception $e) {
            $error_response = [
                'draw' => intval($this->input->post('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ];

            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode($error_response));
        }
    }

    /**
     * GET FULL STOCK DATA WITH INITIAL, OUTPUT, AVAILABLE QUANTITIES AND PROFIT
     * Utilisé par la vue État de stock pour DataTables
     */

    public function data_with_profit()
    {
        try {
            if (!isset($this->stock_model)) {
                $this->load->model('stock_model');
            }

            // Récupérer les paramètres DataTables
            $draw = intval($this->input->post('draw'));
            $start = intval($this->input->post('start'));
            $length = intval($this->input->post('length'));
            $search = $this->input->post('search')['value'];
            $show_zero_stock = $this->input->post('show_zero_stock');

            // Construire la requête avec bénéfice
            $this->db->select('
            item.id as item_id,
            item.name as article,
            item_category.item_category as category,
            item.unit,
            item.purchase_price,
            item.unit_price as selling_price,
            COALESCE(stock.current_quantity, 0) as quantite_disponible,
            (COALESCE(item.unit_price, 0) - COALESCE(item.purchase_price, 0)) as marge_unitaire,
            (COALESCE(item.unit_price, 0) - COALESCE(item.purchase_price, 0)) * COALESCE(stock.current_quantity, 0) as benefice_potentiel,
            COALESCE(item.purchase_price, 0) * COALESCE(stock.current_quantity, 0) as valeur_stock_achat,
            COALESCE(item.unit_price, 0) * COALESCE(stock.current_quantity, 0) as valeur_stock_vente
        ');
            $this->db->from('stock');
            $this->db->join('item', 'item.id = stock.item_id');
            $this->db->join('item_category', 'item_category.id = item.item_category_id', 'left');
            $this->db->where('stock.status', 1);

            if (!$show_zero_stock) {
                $this->db->where('stock.current_quantity >', 0);
            }

            // Recherche
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('item.name', $search);
                $this->db->or_like('item_category.item_category', $search);
                $this->db->group_end();
            }

            // Compter le total filtré
            $temp_db = clone $this->db;
            $filtered_records = $temp_db->count_all_results('', false);

            // Pagination
            $this->db->order_by('item.name', 'ASC');
            if ($length != -1) {
                $this->db->limit($length, $start);
            }

            $query = $this->db->get();
            $data = [];

            foreach($query->result() as $row) {
                $data[] = [
                    'article' => $row->article,
                    'category' => $row->category ?? '',
                    'unit' => $row->unit ?? '',
                    'quantite_disponible' => number_format($row->quantite_disponible, 0, ',', ' '),
                    'prix_achat' => number_format($row->purchase_price, 2, ',', ' '),
                    'prix_vente' => number_format($row->selling_price, 2, ',', ' '),
                    'marge_unitaire' => number_format($row->marge_unitaire, 2, ',', ' '),
                    'benefice_potentiel' => number_format($row->benefice_potentiel, 0, ',', ' '),
                    'valeur_stock_achat' => number_format($row->valeur_stock_achat, 0, ',', ' '),
                    'valeur_stock_vente' => number_format($row->valeur_stock_vente, 0, ',', ' ')
                ];
            }

            // Compter le total des enregistrements
            $total_records = $this->db->where('stock.status', 1)->count_all_results('stock');

            $response = [
                'draw' => $draw,
                'recordsTotal' => $total_records,
                'recordsFiltered' => $filtered_records,
                'data' => $data
            ];

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            $error_response = [
                'draw' => intval($this->input->post('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Erreur serveur: ' . $e->getMessage()
            ];

            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode($error_response));
        }
    }

    /**
     * Get potential profit totals for dashboard
     */
    public function get_profit_totals()
    {
        $this->output->set_content_type('application/json');

        try {
            $sql = "SELECT 
                    SUM(COALESCE(item.purchase_price, 0) * COALESCE(stock.current_quantity, 0)) as total_valeur_achat,
                    SUM(COALESCE(item.unit_price, 0) * COALESCE(stock.current_quantity, 0)) as total_valeur_vente,
                    SUM((COALESCE(item.unit_price, 0) - COALESCE(item.purchase_price, 0)) * COALESCE(stock.current_quantity, 0)) as total_benefice_potentiel,
                    COUNT(DISTINCT item.id) as total_articles,
                    SUM(CASE WHEN stock.current_quantity = 0 THEN 1 ELSE 0 END) as articles_rupture
                FROM stock
                JOIN item ON item.id = stock.item_id
                WHERE stock.status = 1";

            $query = $this->db->query($sql);
            $result = $query->row();

            if ($result && $result->total_valeur_vente > 0) {
                $result->marge_moyenne = ($result->total_benefice_potentiel / $result->total_valeur_vente) * 100;
            } else {
                $result->marge_moyenne = 0;
            }

            echo json_encode(['status' => 'success', 'data' => $result]);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get profit by category
     */
    public function get_profit_by_category()
    {
        $this->output->set_content_type('application/json');

        try {
            $sql = "SELECT 
                    ic.item_category as category_name,
                    SUM(COALESCE(i.purchase_price, 0) * COALESCE(s.current_quantity, 0)) as valeur_achat,
                    SUM(COALESCE(i.unit_price, 0) * COALESCE(s.current_quantity, 0)) as valeur_vente,
                    SUM((COALESCE(i.unit_price, 0) - COALESCE(i.purchase_price, 0)) * COALESCE(s.current_quantity, 0)) as benefice_potentiel,
                    COUNT(DISTINCT i.id) as nb_articles
                FROM stock s
                JOIN item i ON i.id = s.item_id
                JOIN item_category ic ON ic.id = i.item_category_id
                WHERE s.status = 1 AND s.current_quantity > 0
                GROUP BY ic.id
                ORDER BY benefice_potentiel DESC";

            $query = $this->db->query($sql);
            $result = $query->result();

            echo json_encode(['status' => 'success', 'data' => $result]);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


    public function get_inventory_datatable11()
    {
        $this->load->model('Stock_model');
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $search = $this->input->post('search')['value'] ?? '';
        $show_zero_stock = $this->input->post('show_zero_stock') == '1';

        $data = $this->Stock_model->getInventoryData($show_zero_stock, $search, $start, $length, $draw);
        echo json_encode($data);
    }

    public function get_inventory_datatable1106()
    {
        $this->load->model('Stock_model');
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $search = $this->input->post('search')['value'] ?? '';
        $show_zero_stock = $this->input->post('show_zero_stock') == '1';

        // Récupération des filtres période
        $last_modified_start = $this->input->post('last_modified_start');
        $last_modified_end = $this->input->post('last_modified_end');

        $data = $this->Stock_model->getInventoryData($show_zero_stock, $search, $start, $length, $draw, $last_modified_start, $last_modified_end);
        echo json_encode($data);
    }

    public function get_inventory_datatable()
    {
        $this->load->model('Stock_model');
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $search = $this->input->post('search')['value'] ?? '';
        $show_zero_stock = $this->input->post('show_zero_stock') == '1';
        $last_modified_start = $this->input->post('last_modified_start');
        $last_modified_end = $this->input->post('last_modified_end');

        // Appel à la méthode avancée avec filtres de période
        $data = $this->Stock_model->getInventoryData($show_zero_stock, $search, $start, $length, $draw, $last_modified_start, $last_modified_end);
        echo json_encode($data);
    }


    // Dans controllers/admin/Itemstock.php

    public function update_threshold()
    {
        $item_id = $this->input->post('item_id');
        $threshold = $this->input->post('threshold');
        if ($item_id && is_numeric($threshold)) {
            $this->db->where('id', $item_id)->update('item', ['stock_threshold' => $threshold]);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Données invalides']);
        }
    }


    public function update_real_stock11()
    {
        $item_id = $this->input->post('item_id');
        $real_quantity = (int) $this->input->post('real_quantity');

        if (!$item_id || $real_quantity < 0) {
            echo json_encode(['status' => 'error', 'message' => 'Données invalides']);
            return;
        }

        $this->db->where('id', $item_id);
        $this->db->update('item', ['real_quantity' => $real_quantity]);

        if ($this->db->affected_rows() >= 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Aucune modification']);
        }
    }

    public function update_inventory_sync()
    {
        $item_id = $this->input->post('item_id');
        $new_stock_reel = (int) $this->input->post('new_stock_reel');

        if (!$item_id || $new_stock_reel < 0) {
            echo json_encode(['status' => 'error', 'message' => 'Données invalides']);
            return;
        }

        // Récupérer l'ancien stock réel et l'ancien stock théorique (somme des current_quantity)
        $old_real = $this->db->select('real_quantity')->from('item')->where('id', $item_id)->get()->row()->real_quantity ?? 0;
        $old_theorique = $this->db->select('SUM(current_quantity) as total')->from('stock')->where('item_id', $item_id)->get()->row()->total ?? 0;

        // Mise à jour du stock réel dans la table item
        $this->db->where('id', $item_id)->update('item', ['real_quantity' => $new_stock_reel]);

        // Mise à jour du stock théorique (current_quantity) dans la table stock
        // Si vous avez plusieurs lignes stock pour le même article, vous pouvez :
        //   - les mettre toutes à jour pour que la somme = $new_stock_reel (ou répartir)
        //   - ou ne modifier que la première ligne (ex: FIFO)
        // Ici, on suppose une seule ligne par article (le plus simple)
        $this->db->where('item_id', $item_id);
        $this->db->update('stock', ['current_quantity' => $new_stock_reel]);

        // Journaliser (audit)
        $this->load->model('Stock_model');
        $this->Stock_model->logAudit($item_id, 'stock_reel_update', 'real_quantity', $old_real, $new_stock_reel);
        $this->Stock_model->logAudit($item_id, 'stock_theorique_sync', 'current_quantity', $old_theorique, $new_stock_reel);

        echo json_encode(['status' => 'success']);
    }

    public function get_audit_trail()
    {
        $this->load->model('Stock_model');
        $item_id = $this->input->post('item_id');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $audit = $this->Stock_model->getAuditTrail($item_id, $start_date, $end_date);
        echo json_encode(['status' => 'success', 'data' => $audit]);
    }

    /**
     * Mise à jour du stock réel (avec audit)
     */
    public function update_real_stock()
    {
        $item_id = $this->input->post('item_id');
        $new_real = (int) $this->input->post('real_quantity');

        // Récupérer l'ancienne valeur
        $old_real = $this->db->select('real_quantity')->from('item')->where('id', $item_id)->get()->row()->real_quantity ?? 0;

        if ($new_real == $old_real) {
            echo json_encode(['status' => 'success', 'message' => 'Aucun changement']);
            return;
        }

        $this->db->where('id', $item_id)->update('item', ['real_quantity' => $new_real]);

        // Log dans l'audit
        $this->load->model('Stock_model');
        $this->Stock_model->logAudit($item_id, 'stock_reel_update', 'real_quantity', $old_real, $new_real);

        echo json_encode(['status' => 'success']);
    }


}
