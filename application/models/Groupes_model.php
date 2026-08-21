<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Groupes_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ========================================== //
    // RÉCUPÉRER TOUS LES GROUPES                 //
    // ========================================== //
    public function get_all($id = null) {
        $this->db->select('*');
        $this->db->from('groupes_cellules');
        $this->db->where('deleted', 0);

        if ($id != null) {
            $this->db->where('id', $id);
            $query = $this->db->get();
            return $query->row_array();
        }

        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER UN GROUPE PAR ID                 //
    // ========================================== //
    public function get_by_id($id) {
        $this->db->select('*');
        $this->db->from('groupes_cellules');
        $this->db->where('id', $id);
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    // ========================================== //
    // AJOUTER UN GROUPE                          //
    // ========================================== //
    public function add($data) {
        $this->db->insert('groupes_cellules', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UN GROUPE                    //
    // ========================================== //
    public function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('groupes_cellules', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UN GROUPE (SOFT DELETE)          //
    // ========================================== //
    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->update('groupes_cellules', array('deleted' => 1));
        return $this->db->affected_rows();
    }

    // ========================================== //
    // STATISTIQUES DES GROUPES                   //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total
        $this->db->select('COUNT(*) as total');
        $this->db->from('groupes_cellules');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total'] = (int)$query->row()->total;

        // Par type
        $types = ['groupe', 'cellule'];
        foreach ($types as $type) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('groupes_cellules');
            $this->db->where('deleted', 0);
            $this->db->where('type', $type);
            $query = $this->db->get();
            $stats['type_' . $type] = (int)$query->row()->total;
        }

        // Par statut
        $statuses = ['actif', 'inactif', 'suspendu'];
        foreach ($statuses as $status) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('groupes_cellules');
            $this->db->where('deleted', 0);
            $this->db->where('statut', $status);
            $query = $this->db->get();
            $stats['statut_' . $status] = (int)$query->row()->total;
        }

        // Total membres
        $this->db->select('SUM(nombre_membres) as total_membres');
        $this->db->from('groupes_cellules');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total_membres'] = (int)$query->row()->total_membres;

        return $stats;
    }

    // ========================================== //
    // RÉCUPÉRER LES GROUPES FILTRÉS              //
    // ========================================== //
    public function get_filtered($type = null, $statut = null, $quartier = null) {
        $this->db->select('*');
        $this->db->from('groupes_cellules');
        $this->db->where('deleted', 0);

        if (!empty($type)) {
            $this->db->where('type', $type);
        }

        if (!empty($statut)) {
            $this->db->where('statut', $statut);
        }

        if (!empty($quartier)) {
            $this->db->like('quartier', $quartier);
        }

        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES TYPES                        //
    // ========================================== //
    public function get_types() {
        return [
            'groupe' => 'Groupe',
            'cellule' => 'Cellule'
        ];
    }

    // ========================================== //
    // RÉCUPÉRER LES STATUTS                      //
    // ========================================== //
    public function get_statuses() {
        return [
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'suspendu' => 'Suspendu'
        ];
    }

    // ========================================== //
    // GET STATUS LABEL                           //
    // ========================================== //
    public function get_status_label($statut) {
        $labels = [
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'suspendu' => 'Suspendu'
        ];
        return $labels[$statut] ?? $statut;
    }

    // ========================================== //
    // GET STATUS BADGE CLASS                     //
    // ========================================== //
    public function get_status_badge($statut) {
        $badges = [
            'actif' => 'actif',
            'inactif' => 'inactif',
            'suspendu' => 'suspendu'
        ];
        return $badges[$statut] ?? 'actif';
    }

    // ========================================== //
    // GET TYPE LABEL                             //
    // ========================================== //
    public function get_type_label($type) {
        $labels = [
            'groupe' => 'Groupe',
            'cellule' => 'Cellule'
        ];
        return $labels[$type] ?? $type;
    }

    // ========================================== //
    // RECHERCHER DES GROUPES                     //
    // ========================================== //
    public function search($keyword) {
        $this->db->select('*');
        $this->db->from('groupes_cellules');
        $this->db->where('deleted', 0);
        $this->db->group_start();
        $this->db->like('nom', $keyword);
        $this->db->or_like('responsable', $keyword);
        $this->db->or_like('quartier', $keyword);
        $this->db->or_like('lieu_reunion', $keyword);
        $this->db->group_end();
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // COMPTER PAR STATUT                         //
    // ========================================== //
    public function count_by_status($statut) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('groupes_cellules');
        $this->db->where('deleted', 0);
        $this->db->where('statut', $statut);
        $query = $this->db->get();
        return (int)$query->row()->total;
    }

    // ========================================== //
    // COMPTER PAR TYPE                           //
    // ========================================== //
    public function count_by_type($type) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('groupes_cellules');
        $this->db->where('deleted', 0);
        $this->db->where('type', $type);
        $query = $this->db->get();
        return (int)$query->row()->total;
    }

    // ========================================== //
    // RÉCUPÉRER LES QUARTIERS                    //
    // ========================================== //
    public function get_quartiers() {
        $this->db->distinct();
        $this->db->select('quartier');
        $this->db->from('groupes_cellules');
        $this->db->where('deleted', 0);
        $this->db->where('quartier !=', '');
        $this->db->where('quartier IS NOT NULL');
        $this->db->order_by('quartier', 'ASC');
        $query = $this->db->get();
        $result = $query->result_array();
        return array_column($result, 'quartier');
    }
}