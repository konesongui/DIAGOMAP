<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Stock_model extends MY_Model {

    protected $stock_table = 'stock';
    protected $current_session;

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    protected function applyEntrepriseScope($tableAlias = null, $field = 'entreprise_id')
    {
        $entreprise_id = (int) $this->getCurrentEntrepriseId();

        if ($entreprise_id > 0 && $this->db->field_exists('entreprise_id', 'stock')) {
            $whereField = ($tableAlias !== null && $tableAlias !== '') ? $tableAlias . '.' . $field : $field;
            $this->db->where($whereField, $entreprise_id);
        }

        return $this;
    }

    public function getListData()
    {
        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        $search = $this->input->post('search')['value'];
        $show_zero_stock = $this->input->post('show_zero_stock');

        $entreprise_id = (int) $this->getCurrentEntrepriseId();

        $this->db->from('stock');
        $this->db->join('item', 'item.id = stock.item_id', 'left');
        if ($entreprise_id > 0) {
           $this->db->where('(COALESCE(stock.entreprise_id, item.entreprise_id) = ' . $entreprise_id . ')', null, false);
        }

        // REQUÊTE DE COMPTAGE TOTAL (avec le bon contexte d'entreprise)
        $total_db = clone $this->db;
        $total_records = $total_db->count_all_results('stock');

        // REQUÊTE PRINCIPALE
        $this->db->select('
        item.name as article,
        item_category.item_category as category,
        item.unit,
        COALESCE(stock.initial_quantity, 0) as quantite_initiale,
        COALESCE(stock.current_quantity, 0) as quantite_disponible,
        (COALESCE(stock.initial_quantity, 0) - COALESCE(stock.current_quantity, 0)) as quantite_sortie,
        COALESCE(stock.weighted_avg_price, 0) as cout_moyen
    ');
        $this->db->from('stock');
        $this->db->join('item', 'item.id = stock.item_id');
        $this->db->join('item_category', 'item_category.id = item.item_category_id', 'left');
        $this->db->where('stock.status', 1);

        // FILTRE STOCK ZÉRO
        if ($show_zero_stock != '1') {
            $this->db->where('stock.current_quantity >', 0);
        }

        // RECHERCHE
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('item.name', $search);
            $this->db->or_like('item_category.item_category', $search);
            $this->db->group_end();
        }

        // COMPTAGE FILTRÉ (avant pagination)
        $temp_query = clone $this->db;
        $filtered_records = $temp_query->get()->num_rows();

        // PAGINATION
        $this->db->order_by('item.name', 'ASC');
        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();

        // FORMATAGE DES DONNÉES
        $data = [];
        foreach($query->result() as $row) {
            $data[] = [
                'article' => $row->article,
                'category' => $row->category ?? '',
                'unit' => $row->unit ?? '',
                'quantite_initiale' => number_format($row->quantite_initiale, 0, ',', ' '),
                'quantite_sortie' => number_format($row->quantite_sortie, 0, ',', ' '),
                'quantite_disponible' => number_format($row->quantite_disponible, 0, ',', ' '),
                'cout_moyen' => number_format($row->cout_moyen, 2, ',', ' '),
            ];
        }

        return json_encode([
            'draw' => $draw,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $filtered_records,
            'data' => $data
        ]);
    }

    public function getStockThresholdNotifications($limit = 8)
    {
        $entreprise_id = (int) $this->getCurrentEntrepriseId();

        $this->db->select('
            item.id,
            item.name,
            item.stock_threshold,
            item_category.item_category,
            COALESCE(SUM(stock.current_quantity), 0) as current_quantity
        ', false);
        $this->db->from('stock');
        $this->db->join('item', 'item.id = stock.item_id');
        $this->db->join('item_category', 'item_category.id = item.item_category_id', 'left');
        $this->db->where('stock.status', 1);
        if ($entreprise_id > 0) {
            $this->db->where('(COALESCE(stock.entreprise_id, item.entreprise_id) = ' . $entreprise_id . ')', null, false);
        }
        $this->db->group_by('item.id');
        $this->db->group_by('item.name');
        $this->db->group_by('item.stock_threshold');
        $this->db->group_by('item_category.item_category');
        $this->db->having('current_quantity = 0 OR (stock_threshold > 0 AND current_quantity <= stock_threshold)', null, false);
        $this->db->order_by('current_quantity', 'ASC');
        $this->db->order_by('item.name', 'ASC');

        $query = $this->db->get();
        $rows = $query->result_array();

        $alerts = [];
        $rupture_count = 0;
        $near_count = 0;

        foreach ($rows as $row) {
            $quantity = (float) ($row['current_quantity'] ?? 0);
            $threshold = (float) ($row['stock_threshold'] ?? 0);
            $status = ($quantity <= 0) ? 'rupture' : 'presque';

            if ($status === 'rupture') {
                $rupture_count++;
            } else {
                $near_count++;
            }

            $alerts[] = [
                'id' => (int) $row['id'],
                'name' => $row['name'],
                'category' => $row['item_category'] ?? '',
                'current_quantity' => $quantity,
                'stock_threshold' => $threshold,
                'status' => $status,
            ];
        }

        return [
            'total_alerts' => count($rows),
            'rupture_count' => $rupture_count,
            'near_count' => $near_count,
            'alerts' => array_slice($alerts, 0, (int) $limit),
        ];
    }



    public function getItemByCategory($item_category_id)
    {
        $this->db->select('item.*, stock.current_quantity, stock.weighted_avg_price');
        $this->db->from('item');
        $this->db->join('stock', 'item.id = stock.item_id', 'left');
        $this->db->where('item.item_category_id', $item_category_id);
        $this->db->where('stock.current_quantity >', 0);
        $this->db->where('stock.status', 1);
        $this->applyEntrepriseScope('stock');
        return $this->db->get()->result();
    }

    /**
     * Récupère tous les articles avec stock > 0 pour les formulaires
     */
    public function getAvailableItems()
    {
        $this->db->select('item.*, stock.current_quantity, stock.weighted_avg_price');
        $this->db->from('item');
        $this->db->join('stock', 'item.id = stock.item_id', 'inner');
        $this->db->where('stock.current_quantity >', 0);
        $this->db->where('stock.status', 1);
        $this->applyEntrepriseScope('stock');
        $this->db->order_by('item.name', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Récupère les données du bénéfice potentiel pour l'état de stock
     */
    public function getStockWithProfit($show_zero_stock = false) {
        $this->db->select('
        item.id as item_id,
        item.name as article,
        item_category.item_category as category,
        item.unit,
        item.purchase_price,
        item.unit_price as selling_price,
        COALESCE(stock.current_quantity, 0) as quantite_disponible,
        COALESCE(stock.weighted_avg_price, 0) as cout_moyen,
        (COALESCE(item.unit_price, 0) - COALESCE(item.purchase_price, 0)) as marge_unitaire,
        (COALESCE(item.unit_price, 0) - COALESCE(item.purchase_price, 0)) * COALESCE(stock.current_quantity, 0) as benefice_potentiel,
        COALESCE(item.purchase_price, 0) * COALESCE(stock.current_quantity, 0) as valeur_stock_achat,
        COALESCE(item.unit_price, 0) * COALESCE(stock.current_quantity, 0) as valeur_stock_vente
    ');
        $this->db->from('stock');
        $this->db->join('item', 'item.id = stock.item_id');
        $this->db->join('item_category', 'item_category.id = item.item_category_id', 'left');
        $this->db->where('stock.status', 1);
        $this->applyEntrepriseScope('stock');

        if (!$show_zero_stock) {
            $this->db->where('stock.current_quantity >', 0);
        }

        $this->db->order_by('item.name', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Récupère les totaux du bénéfice potentiel
     */
    public function getPotentialProfitTotals() {
        $sql = "SELECT 
                SUM(COALESCE(item.purchase_price, 0) * COALESCE(stock.current_quantity, 0)) as total_valeur_achat,
                SUM(COALESCE(item.unit_price, 0) * COALESCE(stock.current_quantity, 0)) as total_valeur_vente,
                SUM((COALESCE(item.unit_price, 0) - COALESCE(item.purchase_price, 0)) * COALESCE(stock.current_quantity, 0)) as total_benefice_potentiel,
                COUNT(DISTINCT item.id) as total_articles,
                SUM(CASE WHEN stock.current_quantity = 0 THEN 1 ELSE 0 END) as articles_rupture
            FROM stock
            JOIN item ON item.id = stock.item_id
            WHERE stock.status = 1";

        if ($this->db->field_exists('entreprise_id', 'stock')) {
            $entreprise_id = (int) $this->getCurrentEntrepriseId();
            if ($entreprise_id > 0) {
                $sql .= " AND stock.entreprise_id = " . (int) $entreprise_id;
            }
        }

        $query = $this->db->query($sql);
        $result = $query->row();

        // Calculer la marge moyenne
        if ($result && $result->total_valeur_vente > 0) {
            $result->marge_moyenne = ($result->total_benefice_potentiel / $result->total_valeur_vente) * 100;
        } else {
            $result->marge_moyenne = 0;
        }

        return $result;
    }

    /**
     * Récupère le bénéfice potentiel par catégorie
     */
    public function getProfitByCategory() {
        $sql = "SELECT 
                ic.item_category as category_name,
                SUM(COALESCE(i.purchase_price, 0) * COALESCE(s.current_quantity, 0)) as valeur_achat,
                SUM(COALESCE(i.unit_price, 0) * COALESCE(s.current_quantity, 0)) as valeur_vente,
                SUM((COALESCE(i.unit_price, 0) - COALESCE(i.purchase_price, 0)) * COALESCE(s.current_quantity, 0)) as benefice_potentiel,
                COUNT(DISTINCT i.id) as nb_articles
            FROM stock s
            JOIN item i ON i.id = s.item_id
            JOIN item_category ic ON ic.id = i.item_category_id
            WHERE s.status = 1 AND s.current_quantity > 0";

        if ($this->db->field_exists('entreprise_id', 'stock')) {
            $entreprise_id = (int) $this->getCurrentEntrepriseId();
            if ($entreprise_id > 0) {
                $sql .= " AND s.entreprise_id = " . (int) $entreprise_id;
            }
        }

        $sql .= " GROUP BY ic.id ORDER BY benefice_potentiel DESC";

        $query = $this->db->query($sql);
        return $query->result();
    }

    /**
     * Récupère toutes les données de stock avec quantités et bénéfice potentiel
     * pour l'état de stock principal
     */

    public function getFullStockData($show_zero_stock = false, $search = '', $start = 0, $length = 25, $draw = 1)
    {
        $entreprise_id = (int) $this->getCurrentEntrepriseId();

        $this->db->select('
        item.name as article,
        item_category.item_category as category,
        item.unit,
        COALESCE(stock.initial_quantity, 0) as quantite_initiale,
        COALESCE(stock.current_quantity, 0) as quantite_disponible,
        (COALESCE(stock.initial_quantity, 0) - COALESCE(stock.current_quantity, 0)) as quantite_sortie,
        COALESCE(item.purchase_price, 0) as prix_achat,
        COALESCE(item.unit_price, 0) as prix_vente,
        (COALESCE(item.unit_price, 0) - COALESCE(item.purchase_price, 0)) as marge_unitaire,
        (COALESCE(item.unit_price, 0) - COALESCE(item.purchase_price, 0)) * COALESCE(stock.current_quantity, 0) as benefice_potentiel
    ');
        $this->db->from('stock');
        $this->db->join('item', 'item.id = stock.item_id');
        $this->db->join('item_category', 'item_category.id = item.item_category_id', 'left');
        $this->db->where('stock.status', 1);
        if ($entreprise_id > 0) {
            $this->db->where('(COALESCE(stock.entreprise_id, item.entreprise_id) = ' . $entreprise_id . ')', null, false);
        }

        if (!$show_zero_stock) {
            $this->db->where('stock.current_quantity >', 0);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('item.name', $search);
            $this->db->or_like('item_category.item_category', $search);
            $this->db->group_end();
        }

        // Compter le nombre total d'enregistrements après filtres (pour recordsFiltered)
        $filtered_db = clone $this->db;
        $filtered_records = $filtered_db->count_all_results();

        // Pagination et tri
        $this->db->order_by('item.name', 'ASC');
        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();
        $data = [];

        foreach ($query->result() as $row) {
            $data[] = [
                'article' => $row->article,
                'category' => $row->category ?? '',
                'unit' => $row->unit ?? '',
                'quantite_initiale' => number_format($row->quantite_initiale, 0, ',', ' '),
                'quantite_sortie' => number_format($row->quantite_sortie, 0, ',', ' '),
                'quantite_disponible' => number_format($row->quantite_disponible, 0, ',', ' '),
                'prix_achat' => number_format($row->prix_achat, 0, ',', ' '),
                'prix_vente' => number_format($row->prix_vente, 0, ',', ' '),
                'marge_unitaire' => number_format($row->marge_unitaire, 0, ',', ' '),
                'benefice_potentiel' => number_format($row->benefice_potentiel, 0, ',', ' ')
            ];
        }

        // Total des enregistrements sans filtre (pour recordsTotal)
        $total_query = clone $this->db;
        if ($entreprise_id > 0) {
            $total_query->where('(COALESCE(stock.entreprise_id, item.entreprise_id) = ' . $entreprise_id . ')', null, false);
        }
        $total_records = $total_query->count_all_results('stock');

        return [
            'draw' => $draw,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $filtered_records,
            'data' => $data
        ];
    }

    public function getFullStockData_($show_zero_stock = false, $search = '', $start = 0, $length = 25, $draw = 1)
    {
        $this->db->select('
        item.name as article,
        item_category.item_category as category,
        item.unit,
        COALESCE(stock.initial_quantity, 0) as quantite_initiale,
        COALESCE(stock.current_quantity, 0) as quantite_disponible,
        (COALESCE(stock.initial_quantity, 0) - COALESCE(stock.current_quantity, 0)) as quantite_sortie,
        COALESCE(item.purchase_price, 0) as prix_achat,
        COALESCE(item.unit_price, 0) as prix_vente,
        (COALESCE(item.unit_price, 0) - COALESCE(item.purchase_price, 0)) as marge_unitaire,
        (COALESCE(item.unit_price, 0) - COALESCE(item.purchase_price, 0)) * COALESCE(stock.current_quantity, 0) as benefice_potentiel
    ');
        $this->db->from('stock');
        $this->db->join('item', 'item.id = stock.item_id');
        $this->db->join('item_category', 'item_category.id = item.item_category_id', 'left');
        $this->db->where('stock.status', 1);

        if (!$show_zero_stock) {
            $this->db->where('stock.current_quantity >', 0);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('item.name', $search);
            $this->db->or_like('item_category.item_category', $search);
            $this->db->group_end();
        }

        // Comptage total filtré
        $count_query = clone $this->db;
        $filtered_records = $count_query->count_all_results();

        $this->db->order_by('item.name', 'ASC');
        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();
        $data = [];
        foreach ($query->result() as $row) {
            $data[] = [
                'article' => $row->article,
                'category' => $row->category ?? '',
                'unit' => $row->unit ?? '',
                'quantite_initiale' => number_format($row->quantite_initiale, 0, ',', ' '),
                'quantite_sortie' => number_format($row->quantite_sortie, 0, ',', ' '),
                'quantite_disponible' => number_format($row->quantite_disponible, 0, ',', ' '),
                'prix_achat' => number_format($row->prix_achat, 0, ',', ' '),
                'prix_vente' => number_format($row->prix_vente, 0, ',', ' '),
                'marge_unitaire' => number_format($row->marge_unitaire, 0, ',', ' '),
                'benefice_potentiel' => number_format($row->benefice_potentiel, 0, ',', ' ')
            ];
        }

        $total_records = $this->db->count_all('stock');

        return [
            'draw' => $draw,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $filtered_records,
            'data' => $data
        ];
    }
    public function getInventoryData11($show_zero_stock = false, $search = '', $start = 0, $length = 25, $draw = 1)
    {
        $this->db->select('
        item.id as item_id,
        item.name as article,
        COALESCE(item.stock_threshold, 5) as stock_threshold,
        COALESCE(item.real_quantity, 0) as stock_reel,
        COALESCE(SUM(stock.initial_quantity), 0) as total_entree,
        COALESCE(SUM(stock.current_quantity), 0) as total_stock,
        COALESCE(SUM(stock.initial_quantity) - SUM(stock.current_quantity), 0) as total_sortie
    ');
        $this->db->from('stock');
        $this->db->join('item', 'item.id = stock.item_id');
        $this->db->where('stock.status', 1);
        $this->db->group_by('stock.item_id');

        if (!$show_zero_stock) {
            $this->db->having('total_stock >', 0);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('item.name', $search);
            $this->db->or_like('item_category.item_category', $search);
            $this->db->group_end();
        }

        $count_query = clone $this->db;
        $filtered_records = $count_query->count_all_results('', false);

        $this->db->order_by('item.name', 'ASC');
        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();
        $data = [];

        foreach ($query->result() as $row) {
            $ecart = $row->total_stock - $row->stock_reel;
            $data[] = [
                'item_id'            => $row->item_id,
                'article'            => $row->article,
                'quantite_initiale'  => number_format($row->total_entree, 0, ',', ' '),
                'quantite_sortie'    => number_format($row->total_sortie, 0, ',', ' '),
                'quantite_disponible'=> number_format($row->total_stock, 0, ',', ' '),
                'stock_reel'         => number_format($row->stock_reel, 0, ',', ' '),
                'ecart'              => $ecart,
                'stock_threshold'    => (int) $row->stock_threshold
            ];
        }

        $total_records = $this->db->select('COUNT(DISTINCT stock.item_id) as count')
            ->from('stock')
            ->where('status', 1)
            ->get()
            ->row()
            ->count;

        return [
            'draw'            => (int) $draw,
            'recordsTotal'    => (int) $total_records,
            'recordsFiltered' => (int) $filtered_records,
            'data'            => $data
        ];
    }
    public function getInventoryData110626($show_zero_stock = false, $search = '', $start = 0, $length = 25, $draw = 1)
    {
        $this->db->select('
        item.id as item_id,
        item.name as article,
        item_category.item_category as category,
        item.unit,
        COALESCE(item.stock_threshold, 5) as stock_threshold,
        COALESCE(SUM(stock.initial_quantity), 0) as total_entree,
        COALESCE(SUM(stock.current_quantity), 0) as total_stock,
        COALESCE(SUM(stock.initial_quantity) - SUM(stock.current_quantity), 0) as total_sortie
    ');
        $this->db->from('stock');
        $this->db->join('item', 'item.id = stock.item_id');
        $this->db->join('item_category', 'item_category.id = item.item_category_id', 'left');
        $this->db->where('stock.status', 1);
        $this->db->group_by('stock.item_id');

        if (!$show_zero_stock) {
            $this->db->having('total_stock >', 0);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('item.name', $search);
            $this->db->or_like('item_category.item_category', $search);
            $this->db->group_end();
        }

        // Compter le nombre total après filtres (sans pagination)
        $count_query = clone $this->db;
        $filtered_records = $count_query->count_all_results('', false);

        $this->db->order_by('item.name', 'ASC');
        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();
        $data = [];

        foreach ($query->result() as $row) {
            $data[] = [
                'item_id'            => $row->item_id,
                'article'            => $row->article,
                'category'           => $row->category ?? '',
                'unit'               => $row->unit ?? '',
                'quantite_initiale'  => number_format($row->total_entree, 0, ',', ' '),
                'quantite_sortie'    => number_format($row->total_sortie, 0, ',', ' '),
                'quantite_disponible'=> number_format($row->total_stock, 0, ',', ' '),
                'stock_reel'         => number_format($row->total_stock, 0, ',', ' '), // on utilise current_quantity
                'ecart'              => 0,
                'stock_threshold'    => (int) $row->stock_threshold
            ];
        }

        $total_records = $this->db->select('COUNT(DISTINCT stock.item_id) as count')
            ->from('stock')
            ->where('status', 1)
            ->get()
            ->row()
            ->count;

        return [
            'draw'            => (int) $draw,
            'recordsTotal'    => (int) $total_records,
            'recordsFiltered' => (int) $filtered_records,
            'data'            => $data
        ];
    }

    /**
     * Enregistre une action dans le journal d'audit
     */
    public function logAudit($item_id, $action, $field_name, $old_value, $new_value, $quantity_change = null)
    {
        $data = [
            'item_id' => $item_id,
            'action' => $action,
            'field_name' => $field_name,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'quantity_change' => $quantity_change,
            'user_id' => $this->session->userdata('admin_id'),
            'ip_address' => $this->input->ip_address(),
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('stock_audit', $data);
    }

    /**
     * Récupère l'audit pour un article (optionnel avec période)
     */
    public function getAuditTrail11062026($item_id = null, $start_date = null, $end_date = null)
    {
        $this->db->select('sa.*, i.name as item_name, CONCAT(u.username, " (", u.role, ")") as user')
            ->from('stock_audit sa')
            ->join('item i', 'i.id = sa.item_id')
            ->join('users u', 'u.id = sa.user_id', 'left')
            ->order_by('sa.created_at', 'DESC');

        if ($item_id) {
            $this->db->where('sa.item_id', $item_id);
        }
        if ($start_date) {
            $this->db->where('DATE(sa.created_at) >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('DATE(sa.created_at) <=', $end_date);
        }

        return $this->db->get()->result();
    }

    public function getAuditTrail($item_id = null, $start_date = null, $end_date = null)
    {
        $this->db->select('sa.*, i.name as item_name, 
        CONCAT(ul.user, " (", ul.role, ")") as user')
            ->from('stock_audit sa')
            ->join('item i', 'i.id = sa.item_id')
            ->join('userlog ul', 'ul.id = sa.user_id', 'left')
            ->order_by('sa.created_at', 'DESC');

        if ($item_id) {
            $this->db->where('sa.item_id', $item_id);
        }
        if ($start_date) {
            $this->db->where('DATE(sa.created_at) >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('DATE(sa.created_at) <=', $end_date);
        }

        return $this->db->get()->result();
    }

    /**
     * Retourne les données de l'inventaire avec les filtres avancés
     * (catégorie, fournisseur, plage de stock, écart)
     */
    public function getInventoryDataAdvanced($filters, $start, $length, $draw, $show_zero_stock)
    {
        $this->db->select('
        item.id as item_id,
        item.name as article,
        item_category.item_category as category,
        item_supplier.item_supplier as supplier,
        COALESCE(item.stock_threshold, 5) as stock_threshold,
        COALESCE(item.real_quantity, 0) as stock_reel,
        COALESCE(SUM(stock.initial_quantity), 0) as total_entree,
        COALESCE(SUM(stock.current_quantity), 0) as total_stock,
        COALESCE(SUM(stock.initial_quantity) - SUM(stock.current_quantity), 0) as total_sortie
    ');
        $this->db->from('stock');
        $this->db->join('item', 'item.id = stock.item_id');
        $this->db->join('item_category', 'item_category.id = item.item_category_id', 'left');
        $this->db->join('item_supplier', 'item_supplier.id = item.supplier_id', 'left');
        $this->db->where('stock.status', 1);
        $this->db->group_by('stock.item_id');

        // Filtre rupture
        if (!$show_zero_stock) {
            $this->db->having('total_stock >', 0);
        }

        // Filtres avancés
        if (!empty($filters['category_id'])) {
            $this->db->where('item.item_category_id', $filters['category_id']);
        }
        if (!empty($filters['supplier_id'])) {
            $this->db->where('item.supplier_id', $filters['supplier_id']);
        }
        if (!empty($filters['min_stock'])) {
            $this->db->having('total_stock >=', $filters['min_stock']);
        }
        if (!empty($filters['max_stock'])) {
            $this->db->having('total_stock <=', $filters['max_stock']);
        }
        if (isset($filters['ecart_min']) && $filters['ecart_min'] !== '') {
            // L'écart sera calculé après, on utilise HAVING avec une sous-requête ou on filtre en PHP
            // pour simplifier, on filtre en PHP après exécution
        }

        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('item.name', $filters['search']);
            $this->db->or_like('item_category.item_category', $filters['search']);
            $this->db->or_like('item_supplier.item_supplier', $filters['search']);
            $this->db->group_end();
        }

        // Date de dernière modification (via audit)
        if (!empty($filters['last_modified_start']) || !empty($filters['last_modified_end'])) {
            $this->db->join('(SELECT item_id, MAX(created_at) as last_modified FROM stock_audit GROUP BY item_id) as audit', 'audit.item_id = item.id', 'left');
            if (!empty($filters['last_modified_start'])) {
                $this->db->where('audit.last_modified >=', $filters['last_modified_start'] . ' 00:00:00');
            }
            if (!empty($filters['last_modified_end'])) {
                $this->db->where('audit.last_modified <=', $filters['last_modified_end'] . ' 23:59:59');
            }
        }

        $count_query = clone $this->db;
        $filtered_records = $count_query->count_all_results('', false);

        $this->db->order_by('item.name', 'ASC');
        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();
        $data = [];

        foreach ($query->result() as $row) {
            $ecart = $row->total_stock - $row->stock_reel;
            // Appliquer filtre écart min/max en PHP (plus simple)
            if ((isset($filters['ecart_min']) && $filters['ecart_min'] !== '' && $ecart < $filters['ecart_min']) ||
                (isset($filters['ecart_max']) && $filters['ecart_max'] !== '' && $ecart > $filters['ecart_max'])) {
                continue;
            }
            $data[] = [
                'item_id'            => $row->item_id,
                'article'            => $row->article,
                'category'           => $row->category ?? '',
                'supplier'           => $row->supplier ?? '',
                'quantite_initiale'  => number_format($row->total_entree, 0, ',', ' '),
                'quantite_sortie'    => number_format($row->total_sortie, 0, ',', ' '),
                'quantite_disponible'=> number_format($row->total_stock, 0, ',', ' '),
                'stock_reel'         => number_format($row->stock_reel, 0, ',', ' '),
                'ecart'              => $ecart,
                'stock_threshold'    => (int) $row->stock_threshold
            ];
        }

        $total_records = $this->db->select('COUNT(DISTINCT stock.item_id) as count')
            ->from('stock')
            ->where('status', 1)
            ->get()
            ->row()
            ->count;

        return [
            'draw'            => (int) $draw,
            'recordsTotal'    => (int) $total_records,
            'recordsFiltered' => (int) $filtered_records,
            'data'            => $data
        ];
    }

    public function getInventoryData($show_zero_stock = false, $search = '', $start = 0, $length = 25, $draw = 1, $last_modified_start = null, $last_modified_end = null)
    {
        $this->db->select('
        item.id as item_id,
        item.name as article,
        COALESCE(item.stock_threshold, 5) as stock_threshold,
        COALESCE(item.real_quantity, 0) as stock_reel,
        COALESCE(SUM(stock.initial_quantity), 0) as total_entree,
        COALESCE(SUM(stock.current_quantity), 0) as total_stock,
        COALESCE(SUM(stock.initial_quantity) - SUM(stock.current_quantity), 0) as total_sortie
    ');
        $this->db->from('stock');
        $this->db->join('item', 'item.id = stock.item_id');
        $this->db->where('stock.status', 1);
        $this->db->group_by('stock.item_id');

        if (!$show_zero_stock) {
            $this->db->having('total_stock >', 0);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('item.name', $search);
            $this->db->group_end();
        }

        // Filtre par période (basé sur la dernière modification dans stock_audit)
        if (!empty($last_modified_start) || !empty($last_modified_end)) {
            $this->db->join('(SELECT item_id, MAX(created_at) as last_modified FROM stock_audit GROUP BY item_id) as audit', 'audit.item_id = item.id', 'left');
            if (!empty($last_modified_start)) {
                $this->db->where('audit.last_modified >=', $last_modified_start . ' 00:00:00');
            }
            if (!empty($last_modified_end)) {
                $this->db->where('audit.last_modified <=', $last_modified_end . ' 23:59:59');
            }
        }

        $count_query = clone $this->db;
        $filtered_records = $count_query->count_all_results('', false);

        $this->db->order_by('item.name', 'ASC');
        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();
        $data = [];

        foreach ($query->result() as $row) {
            $ecart = $row->total_stock - $row->stock_reel;
            $data[] = [
                'item_id'            => $row->item_id,
                'article'            => $row->article,
                'quantite_initiale'  => number_format($row->total_entree, 0, ',', ' '),
                'quantite_sortie'    => number_format($row->total_sortie, 0, ',', ' '),
                'quantite_disponible'=> number_format($row->total_stock, 0, ',', ' '),
                'stock_reel'         => number_format($row->stock_reel, 0, ',', ' '),
                'ecart'              => $ecart,
                'stock_threshold'    => (int) $row->stock_threshold
            ];
        }

        $total_records = $this->db->select('COUNT(DISTINCT stock.item_id) as count')
            ->from('stock')
            ->where('status', 1)
            ->get()
            ->row()
            ->count;

        return [
            'draw'            => (int) $draw,
            'recordsTotal'    => (int) $total_records,
            'recordsFiltered' => (int) $filtered_records,
            'data'            => $data
        ];
    }

    public function getInventoryData1106($show_zero_stock = false, $search = '', $start = 0, $length = 25, $draw = 1, $last_modified_start = null, $last_modified_end = null)
    {
        $this->db->select('
        item.id as item_id,
        item.name as article,
        COALESCE(item.stock_threshold, 5) as stock_threshold,
        COALESCE(item.real_quantity, 0) as stock_reel,
        COALESCE(SUM(stock.initial_quantity), 0) as total_entree,
        COALESCE(SUM(stock.current_quantity), 0) as total_stock,
        COALESCE(SUM(stock.initial_quantity) - SUM(stock.current_quantity), 0) as total_sortie,
        MAX(stock.updated_at) as last_updated
    ');
        $this->db->from('stock');
        $this->db->join('item', 'item.id = stock.item_id');
        $this->db->join('item_category', 'item_category.id = item.item_category_id', 'left');
        $this->db->where('stock.status', 1);
        $this->db->group_by('stock.item_id');

        // Filtre rupture
        if (!$show_zero_stock) {
            $this->db->having('total_stock >', 0);
        }

        // Filtre recherche
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('item.name', $search);
            $this->db->or_like('item_category.item_category', $search);
            $this->db->group_end();
        }

        // Filtre par date de dernière modification (basé sur la table stock.updated_at)
        if (!empty($last_modified_start)) {
            $this->db->having('MAX(stock.updated_at) >=', $last_modified_start . ' 00:00:00');
        }
        if (!empty($last_modified_end)) {
            $this->db->having('MAX(stock.updated_at) <=', $last_modified_end . ' 23:59:59');
        }

        // Compter les enregistrements filtrés
        $count_query = clone $this->db;
        $filtered_records = $count_query->count_all_results('', false);

        // Pagination et tri
        $this->db->order_by('item.name', 'ASC');
        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();
        $data = [];

        foreach ($query->result() as $row) {
            $ecart = $row->total_stock - $row->stock_reel;
            $data[] = [
                'item_id'            => $row->item_id,
                'article'            => $row->article,
                'quantite_initiale'  => number_format($row->total_entree, 0, ',', ' '),
                'quantite_sortie'    => number_format($row->total_sortie, 0, ',', ' '),
                'quantite_disponible'=> number_format($row->total_stock, 0, ',', ' '),
                'stock_reel'         => number_format($row->stock_reel, 0, ',', ' '),
                'ecart'              => $ecart,
                'stock_threshold'    => (int) $row->stock_threshold,
                'last_updated'       => $row->last_updated
            ];
        }

        // Total des enregistrements sans filtre
        $total_records = $this->db->select('COUNT(DISTINCT stock.item_id) as count')
            ->from('stock')
            ->where('status', 1)
            ->get()
            ->row()
            ->count;

        return [
            'draw'            => (int) $draw,
            'recordsTotal'    => (int) $total_records,
            'recordsFiltered' => (int) $filtered_records,
            'data'            => $data
        ];
    }



}