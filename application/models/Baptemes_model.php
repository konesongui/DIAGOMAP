<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Baptemes_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ========================================== //
    // RÉCUPÉRER TOUS LES BAPTÊMES                //
    // ========================================== //
    public function get_all($id = null) {
        $this->db->select('b.*, m.nom as membre_nom, m.prenom as membre_prenom, m.code_membre');
        $this->db->from('baptemes b');
        $this->db->join('membres m', 'b.membre_id = m.id', 'left');
        $this->db->where('b.deleted', 0);

        if ($id != null) {
            $this->db->where('b.id', $id);
            $query = $this->db->get();
            if ($query->num_rows() == 1) {
                return $query->row_array();
            }
            return null;
        }

        $this->db->order_by('b.date_bapteme', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER UN BAPTÊME PAR ID                //
    // ========================================== //
    public function get_by_id($id) {
        $this->db->select('b.*, m.nom as membre_nom, m.prenom as membre_prenom, m.code_membre');
        $this->db->from('baptemes b');
        $this->db->join('membres m', 'b.membre_id = m.id', 'left');
        $this->db->where('b.id', $id);
        $this->db->where('b.deleted', 0);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row_array();
        }
        return null;
    }

    // ========================================== //
    // GÉNÉRER UN CODE DE BAPTÊME                 //
    // ========================================== //
    public function generate_code() {
        $prefix = 'BAP';
        $year = date('Y');
        $last = $this->db->query("SELECT MAX(CAST(SUBSTRING(code_bapteme, 8) AS UNSIGNED)) as last FROM baptemes WHERE code_bapteme LIKE '$prefix-$year-%'")->row()->last;
        $next = str_pad(($last + 1), 4, '0', STR_PAD_LEFT);
        return $prefix . '-' . $year . '-' . $next;
    }

    // ========================================== //
    // AJOUTER UN BAPTÊME                         //
    // ========================================== //
    public function add($data) {
        $data['code_bapteme'] = $this->generate_code();
        $this->db->insert('baptemes', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UN BAPTÊME                   //
    // ========================================== //
    public function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('baptemes', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UN BAPTÊME (SOFT DELETE)         //
    // ========================================== //
    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->update('baptemes', array('deleted' => 1));
        return $this->db->affected_rows();
    }

    // ========================================== //
    // STATISTIQUES DES BAPTÊMES                  //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total
        $this->db->select('COUNT(*) as total');
        $this->db->from('baptemes');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total'] = (int)$query->row()->total;

        // Par statut
        $statuses = ['planifie', 'effectue', 'annule', 'reporte'];
        foreach ($statuses as $status) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('baptemes');
            $this->db->where('deleted', 0);
            $this->db->where('statut', $status);
            $query = $this->db->get();
            $stats['statut_' . $status] = (int)$query->row()->total;
        }

        // Par type
        $types = ['adulte', 'enfant', 'immersion', 'aspersion'];
        foreach ($types as $type) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('baptemes');
            $this->db->where('deleted', 0);
            $this->db->where('type_bapteme', $type);
            $query = $this->db->get();
            $stats['type_' . $type] = (int)$query->row()->total;
        }

        // Par sexe
        $sexes = ['M', 'F'];
        foreach ($sexes as $sexe) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('baptemes');
            $this->db->where('deleted', 0);
            $this->db->where('sexe', $sexe);
            $query = $this->db->get();
            $stats['sexe_' . $sexe] = (int)$query->row()->total;
        }

        // Certificats générés
        $this->db->select('COUNT(*) as total');
        $this->db->from('baptemes');
        $this->db->where('deleted', 0);
        $this->db->where('certificat_genere', 1);
        $query = $this->db->get();
        $stats['certificats'] = (int)$query->row()->total;

        return $stats;
    }

    // ========================================== //
    // RÉCUPÉRER LES BAPTÊMES FILTRÉS             //
    // ========================================== //
    public function get_filtered($statut = null, $type = null, $date_from = null, $date_to = null) {
        $this->db->select('b.*, m.nom as membre_nom, m.prenom as membre_prenom');
        $this->db->from('baptemes b');
        $this->db->join('membres m', 'b.membre_id = m.id', 'left');
        $this->db->where('b.deleted', 0);

        if (!empty($statut)) {
            $this->db->where('b.statut', $statut);
        }

        if (!empty($type)) {
            $this->db->where('b.type_bapteme', $type);
        }

        if (!empty($date_from)) {
            $this->db->where('b.date_bapteme >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('b.date_bapteme <=', $date_to);
        }

        $this->db->order_by('b.date_bapteme', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES TYPES DE BAPTÊME             //
    // ========================================== //
    public function get_types() {
        return [
            'adulte' => 'Adulte',
            'enfant' => 'Enfant',
            'immersion' => 'Immersion',
            'aspersion' => 'Aspersion'
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
            'adulte' => 'Adulte',
            'enfant' => 'Enfant',
            'immersion' => 'Immersion',
            'aspersion' => 'Aspersion'
        ];
        return $labels[$type] ?? $type;
    }

    // ========================================== //
    // GET TYPE BADGE CLASS                       //
    // ========================================== //
    public function get_type_badge($type) {
        $badges = [
            'adulte' => 'adulte',
            'enfant' => 'enfant',
            'immersion' => 'immersion',
            'aspersion' => 'aspersion'
        ];
        return $badges[$type] ?? 'immersion';
    }

    // ========================================== //
    // RECHERCHER DES BAPTÊMES                    //
    // ========================================== //
    public function search($keyword) {
        $this->db->select('b.*, m.nom as membre_nom, m.prenom as membre_prenom');
        $this->db->from('baptemes b');
        $this->db->join('membres m', 'b.membre_id = m.id', 'left');
        $this->db->where('b.deleted', 0);
        $this->db->group_start();
        $this->db->like('b.code_bapteme', $keyword);
        $this->db->or_like('b.nom', $keyword);
        $this->db->or_like('b.prenom', $keyword);
        $this->db->or_like('b.pasteur_officiant', $keyword);
        $this->db->or_like('b.lieu_bapteme', $keyword);
        $this->db->group_end();
        $this->db->order_by('b.date_bapteme', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // COMPTER PAR STATUT                         //
    // ========================================== //
    public function count_by_status($statut) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('baptemes');
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
        $this->db->from('baptemes');
        $this->db->where('deleted', 0);
        $this->db->where('type_bapteme', $type);
        $query = $this->db->get();
        return (int)$query->row()->total;
    }

    // ========================================== //
    // GÉNÉRER UN CERTIFICAT                      //
    // ========================================== //
    public function generer_certificat($id) {
        $this->db->where('id', $id);
        $this->db->update('baptemes', array('certificat_genere' => 1));
        return $this->db->affected_rows();
    }
}