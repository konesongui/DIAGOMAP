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

    public function getListData()
    {
        // Récupération des paramètres de DataTables
        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        $search = $this->input->post('search')['value'];

        // Comptage total des enregistrements
        $total_records = $this->db->count_all('stock');

        // Construction de la requête principale
        $this->db->start_cache();
        
        $this->db->select('
            stock.id,
            item.name as article,
            item_category.item_category as category,
            item.unit,
            COALESCE(stock.weighted_avg_price, 0) as cout_moyen,
            COALESCE(stock.current_quantity, 0) as quantity_available
        ');
        $this->db->from('stock');
        $this->db->join('item', 'item.id = stock.item_id', 'inner');
        $this->db->join('item_category', 'item_category.id = item.item_category_id', 'left');
        $this->db->where('stock.status', 1);
        
        // Ajout de la recherche
        if($search) {
            $this->db->group_start();
            $this->db->or_like('item.name', $search);
            $this->db->or_like('item_category.item_category', $search);
            $this->db->group_end();
        }
        
        $this->db->stop_cache();

        // Comptage des enregistrements filtrés
        $filtered_records = $this->db->get()->num_rows();
        
        // Tri et pagination
        $this->db->order_by('item.name', 'ASC');
        if($length != -1) {
            $this->db->limit($length, $start);
        }

        // Exécution de la requête
        $query = $this->db->get();
        
        $this->db->flush_cache();
        
        // Préparation des données
        $data = [];
        foreach($query->result() as $row) {
            $data[] = [
                'article' => $row->article,
                'category' => $row->category,
                'unit' => $row->unit,
                'cout_moyen' => number_format($row->cout_moyen, 2, ',', ' '),
                'quantity_available' => number_format($row->quantity_available, 0, ',', ' '),
            ];
        }

        // Préparation de la réponse
        $response = [
            'draw' => $draw,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $filtered_records,
            'data' => $data
        ];

        return json_encode($response);
    }


    public function getItemByCategory($item_category_id)
    {   
        // var_dump($item_category_id);
        // exit;    
        $this->db->select('item.*, stock.current_quantity, stock.weighted_avg_price');
        $this->db->from('item');
        $this->db->join('stock', 'item.id = stock.item_id', 'left');
        $this->db->where('item.item_category_id', $item_category_id);
        $this->db->where('stock.current_quantity >', 0);
        $this->db->where('stock.status', 1);
        return $this->db->get()->result();
    }

}
