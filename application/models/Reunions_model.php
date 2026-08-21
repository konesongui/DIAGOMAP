<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reunions_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ========================================== //
    // RÉCUPÉRER TOUTES LES RÉUNIONS              //
    // ========================================== //
    public function get_all($id = null) {
        $this->db->select('*');
        $this->db->from('reunions');
        $this->db->where('deleted', 0);

        if ($id != null) {
            $this->db->where('id', $id);
            $query = $this->db->get();
            return $query->row_array();
        }

        $this->db->order_by('date_reunion', 'ASC');
        $this->db->order_by('heure_debut', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER UNE RÉUNION PAR ID               //
    // ========================================== //
    public function get_by_id($id) {
        $this->db->select('*');
        $this->db->from('reunions');
        $this->db->where('id', $id);
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    // ========================================== //
    // AJOUTER UNE RÉUNION                        //
    // ========================================== //
    public function add($data) {
        $this->db->insert('reunions', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UNE RÉUNION                  //
    // ========================================== //
    public function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('reunions', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UNE RÉUNION (SOFT DELETE)        //
    // ========================================== //
    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->update('reunions', array('deleted' => 1));
        return $this->db->affected_rows();
    }

    // ========================================== //
    // STATISTIQUES DES RÉUNIONS                  //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total
        $this->db->select('COUNT(*) as total');
        $this->db->from('reunions');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total'] = (int)$query->row()->total;

        // Planifiées
        $this->db->select('COUNT(*) as total');
        $this->db->from('reunions');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'planifiee');
        $query = $this->db->get();
        $stats['planifiee'] = (int)$query->row()->total;

        // En cours
        $this->db->select('COUNT(*) as total');
        $this->db->from('reunions');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'en_cours');
        $query = $this->db->get();
        $stats['en_cours'] = (int)$query->row()->total;

        // Terminées
        $this->db->select('COUNT(*) as total');
        $this->db->from('reunions');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'terminee');
        $query = $this->db->get();
        $stats['terminee'] = (int)$query->row()->total;

        // Annulées
        $this->db->select('COUNT(*) as total');
        $this->db->from('reunions');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'annulee');
        $query = $this->db->get();
        $stats['annulee'] = (int)$query->row()->total;

        // Aujourd'hui
        $today = date('Y-m-d');
        $this->db->select('COUNT(*) as total');
        $this->db->from('reunions');
        $this->db->where('deleted', 0);
        $this->db->where('date_reunion', $today);
        $query = $this->db->get();
        $stats['today'] = (int)$query->row()->total;

        // Cette semaine
        $start_of_week = date('Y-m-d', strtotime('monday this week'));
        $end_of_week = date('Y-m-d', strtotime('sunday this week'));
        $this->db->select('COUNT(*) as total');
        $this->db->from('reunions');
        $this->db->where('deleted', 0);
        $this->db->where('date_reunion >=', $start_of_week);
        $this->db->where('date_reunion <=', $end_of_week);
        $query = $this->db->get();
        $stats['this_week'] = (int)$query->row()->total;

        return $stats;
    }

    // ========================================== //
    // RÉCUPÉRER LES RÉUNIONS FILTRÉES            //
    // ========================================== //
    public function get_filtered($statut = null, $date_from = null, $date_to = null) {
        $this->db->select('*');
        $this->db->from('reunions');
        $this->db->where('deleted', 0);

        if (!empty($statut)) {
            $this->db->where('statut', $statut);
        }

        if (!empty($date_from)) {
            $this->db->where('date_reunion >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('date_reunion <=', $date_to);
        }

        $this->db->order_by('date_reunion', 'ASC');
        $this->db->order_by('heure_debut', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES RÉUNIONS PAR DATE            //
    // ========================================== //
    public function get_by_date($date) {
        $this->db->select('*');
        $this->db->from('reunions');
        $this->db->where('deleted', 0);
        $this->db->where('date_reunion', $date);
        $this->db->order_by('heure_debut', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES RÉUNIONS PAR PÉRIODE         //
    // ========================================== //
    public function get_by_period($start_date, $end_date) {
        $this->db->select('*');
        $this->db->from('reunions');
        $this->db->where('deleted', 0);
        $this->db->where('date_reunion >=', $start_date);
        $this->db->where('date_reunion <=', $end_date);
        $this->db->order_by('date_reunion', 'ASC');
        $this->db->order_by('heure_debut', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES STATUTS                      //
    // ========================================== //
    public function get_statuses() {
        return [
            'planifiee' => 'Planifiée',
            'en_cours' => 'En cours',
            'terminee' => 'Terminée',
            'annulee' => 'Annulée'
        ];
    }

    // ========================================== //
    // GET STATUS LABEL                           //
    // ========================================== //
    public function get_status_label($statut) {
        $labels = [
            'planifiee' => 'Planifiée',
            'en_cours' => 'En cours',
            'terminee' => 'Terminée',
            'annulee' => 'Annulée'
        ];
        return $labels[$statut] ?? $statut;
    }

    // ========================================== //
    // GET STATUS BADGE CLASS                     //
    // ========================================== //
    public function get_status_badge($statut) {
        $badges = [
            'planifiee' => 'planifiee',
            'en_cours' => 'en-cours',
            'terminee' => 'terminee',
            'annulee' => 'annulee'
        ];
        return $badges[$statut] ?? 'planifiee';
    }

    // ========================================== //
    // RECHERCHER DES RÉUNIONS                    //
    // ========================================== //
    public function search($keyword) {
        $this->db->select('*');
        $this->db->from('reunions');
        $this->db->where('deleted', 0);
        $this->db->group_start();
        $this->db->like('titre', $keyword);
        $this->db->or_like('description', $keyword);
        $this->db->or_like('lieu', $keyword);
        $this->db->or_like('participants', $keyword);
        $this->db->group_end();
        $this->db->order_by('date_reunion', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // COMPTER LES RÉUNIONS PAR STATUT            //
    // ========================================== //
    public function count_by_status($statut) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('reunions');
        $this->db->where('deleted', 0);
        $this->db->where('statut', $statut);
        $query = $this->db->get();
        return (int)$query->row()->total;
    }

    // ========================================== //
    // COMPTER LES RÉUNIONS DU JOUR               //
    // ========================================== //
    public function count_today() {
        $today = date('Y-m-d');
        $this->db->select('COUNT(*) as total');
        $this->db->from('reunions');
        $this->db->where('deleted', 0);
        $this->db->where('date_reunion', $today);
        $query = $this->db->get();
        return (int)$query->row()->total;
    }

    // ========================================== //
    // SUPPRESSION MULTIPLE                       //
    // ========================================== //
    public function delete_multiple($ids) {
        if (empty($ids)) {
            return false;
        }

        $this->db->where_in('id', $ids);
        $this->db->update('reunions', array('deleted' => 1));
        return $this->db->affected_rows();
    }
}