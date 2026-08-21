<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tickets_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function tables_available()
    {
        foreach (array('tickets', 'tickets_categories', 'tickets_priorites', 'tickets_statuts', 'tickets_reponses') as $table) {
            if (!$this->db->table_exists($table)) {
                return false;
            }
        }

        return true;
    }

    public function generate_ticket_number()
    {
        if (!$this->db->table_exists('tickets')) {
            return false;
        }

        do {
            $ticket_number = 'TKT-' . date('YmdHis') . '-' . mt_rand(100, 999);
            $exists = $this->db->where('ticket_number', $ticket_number)->count_all_results('tickets');
        } while ($exists > 0);

        return $ticket_number;
    }

    public function add($data)
    {
        if (!$this->db->table_exists('tickets')) {
            return false;
        }

        return $this->db->insert('tickets', $data) ? $this->db->insert_id() : false;
    }

    public function add_reponse($data)
    {
        if (!$this->db->table_exists('tickets_reponses')) {
            return false;
        }

        return $this->db->insert('tickets_reponses', $data) ? $this->db->insert_id() : false;
    }

    public function get_all() {
        if (!$this->tables_available()) {
            return array();
        }

        $this->db->select('t.*, 
                          tc.nom as categorie_nom, 
                          tc.couleur as categorie_couleur,
                          tp.nom as priorite_nom, 
                          tp.couleur as priorite_couleur,
                          ts.nom as statut_nom, 
                          ts.couleur as statut_couleur');
        $this->db->from('tickets t');
        $this->db->join('tickets_categories tc', 't.categorie_id = tc.id', 'left');
        $this->db->join('tickets_priorites tp', 't.priorite_id = tp.id', 'left');
        $this->db->join('tickets_statuts ts', 't.statut_id = ts.id', 'left');
        $this->db->where('t.deleted', 0);
        $this->db->order_by('t.id', 'DESC');
        $query = $this->db->get();
        return $query ? $query->result_array() : array();
    }

    public function get_stats() {
        $stats = array('total' => 0, 'statuts' => array(), 'urgents' => 0);

        if (!$this->tables_available()) {
            return $stats;
        }

        // Total
        $this->db->where('deleted', 0);
        $stats['total'] = (int) $this->db->count_all_results('tickets');

        // Par statut
        $statuts = $this->get_statuts();
        foreach ($statuts as $statut) {
            $this->db->where('statut_id', $statut['id']);
            $this->db->where('deleted', 0);
            $stats['statuts'][$statut['nom']] = $this->db->count_all_results('tickets');
        }

        // Urgents (priorite_id = 4)
        $this->db->where('priorite_id', 4);
        $this->db->where('deleted', 0);
        $stats['urgents'] = $this->db->count_all_results('tickets');

        return $stats;
    }

    public function get_categories() {
        if (!$this->db->table_exists('tickets_categories')) {
            return array();
        }

        $this->db->where('deleted', 0);
        $this->db->order_by('nom', 'ASC');
        $query = $this->db->get('tickets_categories');
        return $query ? $query->result_array() : array();
    }

    public function get_statuts() {
        if (!$this->db->table_exists('tickets_statuts')) {
            return array();
        }

        $this->db->where('deleted', 0);
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get('tickets_statuts');
        return $query ? $query->result_array() : array();
    }

    public function get_priorites() {
        if (!$this->db->table_exists('tickets_priorites')) {
            return array();
        }

        $this->db->where('deleted', 0);
        $this->db->order_by('niveau', 'ASC');
        $query = $this->db->get('tickets_priorites');
        return $query ? $query->result_array() : array();
    }

    // ========================================== //
    // RÉCUPÉRER LE STAFF - CORRIGÉ             //
    // ========================================== //
    public function get_staff() {
        // Vérifier si la table staff existe
        if (!$this->db->table_exists('staff')) {
            return array();
        }

        $this->db->select('id, name, surname, email, is_active');
        $this->db->where('is_active', 1);
        $this->db->where('deleted', 0);
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get('staff');
        $results = $query ? $query->result_array() : array();

        // Formater les résultats
        $formatted = array();
        foreach ($results as $row) {
            $formatted[] = array(
                'id' => $row['id'],
                'nom' => $row['surname'] . ' ' . $row['name'], // Prénom + Nom
                'prenom' => $row['surname'],
                'nom_famille' => $row['name'],
                'email' => $row['email']
            );
        }

        return $formatted;
    }
}