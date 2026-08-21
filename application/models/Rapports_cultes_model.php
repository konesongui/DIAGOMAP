<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rapports_cultes_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ========================================== //
    // RÉCUPÉRER TOUS LES RAPPORTS                //
    // ========================================== //
    public function get_all($id = null) {
        $this->db->select('*');
        $this->db->from('rapports_cultes');
        $this->db->where('deleted', 0);

        if ($id != null) {
            $this->db->where('id', $id);
            $query = $this->db->get();
            return $query->row_array();
        }

        $this->db->order_by('date_culte', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER UN RAPPORT PAR ID                //
    // ========================================== //
    public function get_by_id($id) {
        $this->db->select('*');
        $this->db->from('rapports_cultes');
        $this->db->where('id', $id);
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    // ========================================== //
    // AJOUTER UN RAPPORT                         //
    // ========================================== //
    public function add($data) {
        $this->db->insert('rapports_cultes', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UN RAPPORT                   //
    // ========================================== //
    public function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('rapports_cultes', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UN RAPPORT (SOFT DELETE)         //
    // ========================================== //
    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->update('rapports_cultes', array('deleted' => 1));
        return $this->db->affected_rows();
    }

    // ========================================== //
    // STATISTIQUES DES RAPPORTS                  //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total rapports
        $this->db->select('COUNT(*) as total');
        $this->db->from('rapports_cultes');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total'] = (int)$query->row()->total;

        // Par type de culte
        $types = ['matin', 'soir', 'jeunesse', 'enfants', 'autre'];
        foreach ($types as $type) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('rapports_cultes');
            $this->db->where('deleted', 0);
            $this->db->where('type_culte', $type);
            $query = $this->db->get();
            $stats['type_' . $type] = (int)$query->row()->total;
        }

        // Total participants
        $this->db->select('SUM(total_personnes) as total_personnes');
        $this->db->from('rapports_cultes');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total_personnes'] = (int)$query->row()->total_personnes;

        // Total offrandes
        $this->db->select('SUM(total_finances) as total_finances');
        $this->db->from('rapports_cultes');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total_finances'] = (float)$query->row()->total_finances;

        // Nouvelles conversions
        $this->db->select('SUM(nouvelles_conversions) as total_conversions');
        $this->db->from('rapports_cultes');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total_conversions'] = (int)$query->row()->total_conversions;

        // Baptêmes
        $this->db->select('SUM(baptemes) as total_baptemes');
        $this->db->from('rapports_cultes');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total_baptemes'] = (int)$query->row()->total_baptemes;

        return $stats;
    }

    // ========================================== //
    // RÉCUPÉRER LES RAPPORTS FILTRÉS             //
    // ========================================== //
    public function get_filtered($type_culte = null, $date_from = null, $date_to = null, $statut = null) {
        $this->db->select('*');
        $this->db->from('rapports_cultes');
        $this->db->where('deleted', 0);

        if (!empty($type_culte)) {
            $this->db->where('type_culte', $type_culte);
        }

        if (!empty($date_from)) {
            $this->db->where('date_culte >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('date_culte <=', $date_to);
        }

        if (!empty($statut)) {
            $this->db->where('statut', $statut);
        }

        $this->db->order_by('date_culte', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES TYPES DE CULTE               //
    // ========================================== //
    public function get_types() {
        return [
            'matin' => 'Culte du matin',
            'soir' => 'Culte du soir',
            'jeunesse' => 'Culte des jeunes',
            'enfants' => 'Culte des enfants',
            'autre' => 'Autre'
        ];
    }

    // ========================================== //
    // RÉCUPÉRER LES STATUTS                      //
    // ========================================== //
    public function get_statuses() {
        return [
            'brouillon' => 'Brouillon',
            'valide' => 'Validé',
            'archive' => 'Archivé'
        ];
    }

    // ========================================== //
    // GET TYPE LABEL                             //
    // ========================================== //
    public function get_type_label($type) {
        $labels = [
            'matin' => 'Culte du matin',
            'soir' => 'Culte du soir',
            'jeunesse' => 'Culte des jeunes',
            'enfants' => 'Culte des enfants',
            'autre' => 'Autre'
        ];
        return $labels[$type] ?? $type;
    }

    // ========================================== //
    // GET STATUS LABEL                           //
    // ========================================== //
    public function get_status_label($statut) {
        $labels = [
            'brouillon' => 'Brouillon',
            'valide' => 'Validé',
            'archive' => 'Archivé'
        ];
        return $labels[$statut] ?? $statut;
    }

    // ========================================== //
    // GET STATUS BADGE CLASS                     //
    // ========================================== //
    public function get_status_badge($statut) {
        $badges = [
            'brouillon' => 'brouillon',
            'valide' => 'valide',
            'archive' => 'archive'
        ];
        return $badges[$statut] ?? 'brouillon';
    }

    // ========================================== //
    // CALCULER LE TOTAL DES PERSONNES            //
    // ========================================== //
    public function calculer_total_personnes($data) {
        return ($data['nombre_hommes'] ?? 0) +
            ($data['nombre_femmes'] ?? 0) +
            ($data['nombre_enfants'] ?? 0) +
            ($data['nombre_visiteurs'] ?? 0);
    }

    // ========================================== //
    // CALCULER LE TOTAL FINANCIER                //
    // ========================================== //
    public function calculer_total_finances($data) {
        return ($data['offrande'] ?? 0) +
            ($data['dime'] ?? 0) +
            ($data['actions_de_grace'] ?? 0) +
            ($data['autres_offrandes'] ?? 0);
    }

    // ========================================== //
    // RECHERCHER DES RAPPORTS                    //
    // ========================================== //
    public function search($keyword) {
        $this->db->select('*');
        $this->db->from('rapports_cultes');
        $this->db->where('deleted', 0);
        $this->db->group_start();
        $this->db->like('theme', $keyword);
        $this->db->or_like('predicateur', $keyword);
        $this->db->or_like('passage_biblique', $keyword);
        $this->db->or_like('responsable_culte', $keyword);
        $this->db->group_end();
        $this->db->order_by('date_culte', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // STATISTIQUES PAR MOIS                      //
    // ========================================== //
    public function get_stats_mensuelles($mois, $annee) {
        $this->db->select('*');
        $this->db->from('rapports_cultes');
        $this->db->where('deleted', 0);
        $this->db->where('MONTH(date_culte)', $mois);
        $this->db->where('YEAR(date_culte)', $annee);
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES RAPPORTS PAR PRÉDICATEUR    //
    // ========================================== //
    public function get_by_predicateur($predicateur) {
        $this->db->select('*');
        $this->db->from('rapports_cultes');
        $this->db->where('deleted', 0);
        $this->db->like('predicateur', $predicateur);
        $this->db->order_by('date_culte', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }
}