<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mariages_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ========================================== //
    // RÉCUPÉRER TOUS LES MARIAGES                //
    // ========================================== //
    public function get_all($id = null) {
        $this->db->select('m.*, 
                          ma.nom as mari_nom_complet, ma.prenom as mari_prenom_complet, ma.code_membre as mari_code,
                          f.nom as femme_nom_complet, f.prenom as femme_prenom_complet, f.code_membre as femme_code');
        $this->db->from('mariages m');
        $this->db->join('membres ma', 'm.mari_id = ma.id', 'left');
        $this->db->join('membres f', 'm.femme_id = f.id', 'left');
        $this->db->where('m.deleted', 0);

        if ($id != null) {
            $this->db->where('m.id', $id);
            $query = $this->db->get();
            if ($query->num_rows() == 1) {
                return $query->row_array();
            }
            return null;
        }

        $this->db->order_by('m.date_mariage', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER UN MARIAGE PAR ID                //
    // ========================================== //
    public function get_by_id($id) {
        $this->db->select('m.*, 
                          ma.nom as mari_nom_complet, ma.prenom as mari_prenom_complet, ma.code_membre as mari_code,
                          f.nom as femme_nom_complet, f.prenom as femme_prenom_complet, f.code_membre as femme_code');
        $this->db->from('mariages m');
        $this->db->join('membres ma', 'm.mari_id = ma.id', 'left');
        $this->db->join('membres f', 'm.femme_id = f.id', 'left');
        $this->db->where('m.id', $id);
        $this->db->where('m.deleted', 0);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row_array();
        }
        return null;
    }

    // ========================================== //
    // GÉNÉRER UN CODE DE MARIAGE                 //
    // ========================================== //
    public function generate_code() {
        $prefix = 'MAR';
        $year = date('Y');
        $last = $this->db->query("SELECT MAX(CAST(SUBSTRING(code_mariage, 8) AS UNSIGNED)) as last FROM mariages WHERE code_mariage LIKE '$prefix-$year-%'")->row()->last;
        $next = str_pad(($last + 1), 4, '0', STR_PAD_LEFT);
        return $prefix . '-' . $year . '-' . $next;
    }

    // ========================================== //
    // AJOUTER UN MARIAGE                         //
    // ========================================== //
    public function add($data) {
        $data['code_mariage'] = $this->generate_code();
        $this->db->insert('mariages', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UN MARIAGE                   //
    // ========================================== //
    public function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('mariages', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UN MARIAGE (SOFT DELETE)         //
    // ========================================== //
    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->update('mariages', array('deleted' => 1));
        return $this->db->affected_rows();
    }

    // ========================================== //
    // STATISTIQUES DES MARIAGES                  //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total
        $this->db->select('COUNT(*) as total');
        $this->db->from('mariages');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total'] = (int)$query->row()->total;

        // Par statut
        $statuses = ['planifie', 'effectue', 'annule', 'reporte'];
        foreach ($statuses as $status) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('mariages');
            $this->db->where('deleted', 0);
            $this->db->where('statut', $status);
            $query = $this->db->get();
            $stats['statut_' . $status] = (int)$query->row()->total;
        }

        // Par type
        $types = ['civil', 'religieux', 'traditionnel', 'mixte'];
        foreach ($types as $type) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('mariages');
            $this->db->where('deleted', 0);
            $this->db->where('type_mariage', $type);
            $query = $this->db->get();
            $stats['type_' . $type] = (int)$query->row()->total;
        }

        // Certificats générés
        $this->db->select('COUNT(*) as total');
        $this->db->from('mariages');
        $this->db->where('deleted', 0);
        $this->db->where('certificat_genere', 1);
        $query = $this->db->get();
        $stats['certificats'] = (int)$query->row()->total;

        return $stats;
    }

    // ========================================== //
    // RÉCUPÉRER LES MARIAGES FILTRÉS             //
    // ========================================== //
    public function get_filtered($statut = null, $type = null, $date_from = null, $date_to = null) {
        $this->db->select('m.*');
        $this->db->from('mariages m');
        $this->db->where('m.deleted', 0);

        if (!empty($statut)) {
            $this->db->where('m.statut', $statut);
        }

        if (!empty($type)) {
            $this->db->where('m.type_mariage', $type);
        }

        if (!empty($date_from)) {
            $this->db->where('m.date_mariage >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('m.date_mariage <=', $date_to);
        }

        $this->db->order_by('m.date_mariage', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES TYPES DE MARIAGE             //
    // ========================================== //
    public function get_types() {
        return [
            'civil' => 'Civil',
            'religieux' => 'Religieux',
            'traditionnel' => 'Traditionnel',
            'mixte' => 'Mixte'
        ];
    }

    // ========================================== //
    // RÉCUPÉRER LES STATUTS                      //
    // ========================================== //
    public function get_statuses() {
        return [
            'planifie' => 'Planifié',
            'effectue' => 'Effectué',
            'annule' => 'Annulé',
            'reporte' => 'Reporté'
        ];
    }

    // ========================================== //
    // GET STATUS LABEL                           //
    // ========================================== //
    public function get_status_label($statut) {
        $labels = [
            'planifie' => 'Planifié',
            'effectue' => 'Effectué',
            'annule' => 'Annulé',
            'reporte' => 'Reporté'
        ];
        return $labels[$statut] ?? $statut;
    }

    // ========================================== //
    // GET STATUS BADGE CLASS                     //
    // ========================================== //
    public function get_status_badge($statut) {
        $badges = [
            'planifie' => 'planifie',
            'effectue' => 'effectue',
            'annule' => 'annule',
            'reporte' => 'reporte'
        ];
        return $badges[$statut] ?? 'planifie';
    }

    // ========================================== //
    // GET TYPE LABEL                             //
    // ========================================== //
    public function get_type_label($type) {
        $labels = [
            'civil' => 'Civil',
            'religieux' => 'Religieux',
            'traditionnel' => 'Traditionnel',
            'mixte' => 'Mixte'
        ];
        return $labels[$type] ?? $type;
    }

    // ========================================== //
    // GET TYPE BADGE CLASS                       //
    // ========================================== //
    public function get_type_badge($type) {
        $badges = [
            'civil' => 'civil',
            'religieux' => 'religieux',
            'traditionnel' => 'traditionnel',
            'mixte' => 'mixte'
        ];
        return $badges[$type] ?? 'religieux';
    }

    // ========================================== //
    // RECHERCHER DES MARIAGES                    //
    // ========================================== //
    public function search($keyword) {
        $this->db->select('m.*');
        $this->db->from('mariages m');
        $this->db->where('m.deleted', 0);
        $this->db->group_start();
        $this->db->like('m.code_mariage', $keyword);
        $this->db->or_like('m.mari_nom', $keyword);
        $this->db->or_like('m.mari_prenom', $keyword);
        $this->db->or_like('m.femme_nom', $keyword);
        $this->db->or_like('m.femme_prenom', $keyword);
        $this->db->or_like('m.pasteur_officiant', $keyword);
        $this->db->or_like('m.lieu_mariage', $keyword);
        $this->db->group_end();
        $this->db->order_by('m.date_mariage', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // COMPTER PAR STATUT                         //
    // ========================================== //
    public function count_by_status($statut) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('mariages');
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
        $this->db->from('mariages');
        $this->db->where('deleted', 0);
        $this->db->where('type_mariage', $type);
        $query = $this->db->get();
        return (int)$query->row()->total;
    }

    // ========================================== //
    // GÉNÉRER UN CERTIFICAT                      //
    // ========================================== //
    public function generer_certificat($id) {
        $this->db->where('id', $id);
        $this->db->update('mariages', array('certificat_genere' => 1));
        return $this->db->affected_rows();
    }
}