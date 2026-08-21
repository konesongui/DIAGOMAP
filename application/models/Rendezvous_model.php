<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rendezvous_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ========================================== //
    // RÉCUPÉRER TOUS LES RENDEZ-VOUS             //
    // ========================================== //
    public function get_all($id = null) {
        $this->db->select('*');
        $this->db->from('rendez_vous');
        $this->db->where('deleted', 0);

        if ($id != null) {
            $this->db->where('id', $id);
            $query = $this->db->get();
            return $query->row_array();
        }

        $this->db->order_by('date_rendez_vous', 'ASC');
        $this->db->order_by('heure_debut', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER UN RENDEZ-VOUS PAR ID            //
    // ========================================== //
    public function get_by_id($id) {
        $this->db->select('*');
        $this->db->from('rendez_vous');
        $this->db->where('id', $id);
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    // ========================================== //
    // AJOUTER UN RENDEZ-VOUS                     //
    // ========================================== //
    public function add($data) {
        $this->db->insert('rendez_vous', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UN RENDEZ-VOUS               //
    // ========================================== //
    public function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('rendez_vous', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UN RENDEZ-VOUS (SOFT DELETE)     //
    // ========================================== //
    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->update('rendez_vous', array('deleted' => 1));
        return $this->db->affected_rows();
    }

    // ========================================== //
    // STATISTIQUES DES RENDEZ-VOUS               //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total
        $this->db->select('COUNT(*) as total');
        $this->db->from('rendez_vous');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total'] = (int)$query->row()->total;

        // Planifiés
        $this->db->select('COUNT(*) as total');
        $this->db->from('rendez_vous');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'planifie');
        $query = $this->db->get();
        $stats['planifie'] = (int)$query->row()->total;

        // En cours
        $this->db->select('COUNT(*) as total');
        $this->db->from('rendez_vous');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'en_cours');
        $query = $this->db->get();
        $stats['en_cours'] = (int)$query->row()->total;

        // Terminés
        $this->db->select('COUNT(*) as total');
        $this->db->from('rendez_vous');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'termine');
        $query = $this->db->get();
        $stats['termine'] = (int)$query->row()->total;

        // Annulés
        $this->db->select('COUNT(*) as total');
        $this->db->from('rendez_vous');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'annule');
        $query = $this->db->get();
        $stats['annule'] = (int)$query->row()->total;

        // Aujourd'hui
        $today = date('Y-m-d');
        $this->db->select('COUNT(*) as total');
        $this->db->from('rendez_vous');
        $this->db->where('deleted', 0);
        $this->db->where('date_rendez_vous', $today);
        $query = $this->db->get();
        $stats['today'] = (int)$query->row()->total;

        // Cette semaine
        $start_of_week = date('Y-m-d', strtotime('monday this week'));
        $end_of_week = date('Y-m-d', strtotime('sunday this week'));
        $this->db->select('COUNT(*) as total');
        $this->db->from('rendez_vous');
        $this->db->where('deleted', 0);
        $this->db->where('date_rendez_vous >=', $start_of_week);
        $this->db->where('date_rendez_vous <=', $end_of_week);
        $query = $this->db->get();
        $stats['this_week'] = (int)$query->row()->total;

        return $stats;
    }

    // ========================================== //
    // RÉCUPÉRER LES RENDEZ-VOUS FILTRÉS          //
    // ========================================== //
    public function get_filtered($statut = null, $date_from = null, $date_to = null) {
        $this->db->select('*');
        $this->db->from('rendez_vous');
        $this->db->where('deleted', 0);

        if (!empty($statut)) {
            $this->db->where('statut', $statut);
        }

        if (!empty($date_from)) {
            $this->db->where('date_rendez_vous >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('date_rendez_vous <=', $date_to);
        }

        $this->db->order_by('date_rendez_vous', 'ASC');
        $this->db->order_by('heure_debut', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES RENDEZ-VOUS PAR DATE         //
    // ========================================== //
    public function get_by_date($date) {
        $this->db->select('*');
        $this->db->from('rendez_vous');
        $this->db->where('deleted', 0);
        $this->db->where('date_rendez_vous', $date);
        $this->db->order_by('heure_debut', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES RENDEZ-VOUS PAR PÉRIODE      //
    // ========================================== //
    public function get_by_period($start_date, $end_date) {
        $this->db->select('*');
        $this->db->from('rendez_vous');
        $this->db->where('deleted', 0);
        $this->db->where('date_rendez_vous >=', $start_date);
        $this->db->where('date_rendez_vous <=', $end_date);
        $this->db->order_by('date_rendez_vous', 'ASC');
        $this->db->order_by('heure_debut', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES STATUTS                      //
    // ========================================== //
    public function get_statuses() {
        return [
            'planifie' => 'Planifié',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'annule' => 'Annulé'
        ];
    }

    // ========================================== //
    // GET STATUS LABEL                           //
    // ========================================== //
    public function get_status_label($statut) {
        $labels = [
            'planifie' => 'Planifié',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'annule' => 'Annulé'
        ];
        return $labels[$statut] ?? $statut;
    }

    // ========================================== //
    // GET STATUS BADGE CLASS                     //
    // ========================================== //
    public function get_status_badge($statut) {
        $badges = [
            'planifie' => 'planifie',
            'en_cours' => 'en-cours',
            'termine' => 'termine',
            'annule' => 'annule'
        ];
        return $badges[$statut] ?? 'planifie';
    }

    // ========================================== //
    // RECHERCHER DES RENDEZ-VOUS                 //
    // ========================================== //
    public function search($keyword) {
        $this->db->select('*');
        $this->db->from('rendez_vous');
        $this->db->where('deleted', 0);
        $this->db->group_start();
        $this->db->like('titre', $keyword);
        $this->db->or_like('description', $keyword);
        $this->db->or_like('lieu', $keyword);
        $this->db->or_like('participants', $keyword);
        $this->db->group_end();
        $this->db->order_by('date_rendez_vous', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // COMPTER LES RENDEZ-VOUS PAR STATUT         //
    // ========================================== //
    public function count_by_status($statut) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('rendez_vous');
        $this->db->where('deleted', 0);
        $this->db->where('statut', $statut);
        $query = $this->db->get();
        return (int)$query->row()->total;
    }

    // ========================================== //
    // COMPTER LES RENDEZ-VOUS DU JOUR            //
    // ========================================== //
    public function count_today() {
        $today = date('Y-m-d');
        $this->db->select('COUNT(*) as total');
        $this->db->from('rendez_vous');
        $this->db->where('deleted', 0);
        $this->db->where('date_rendez_vous', $today);
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
        $this->db->update('rendez_vous', array('deleted' => 1));
        return $this->db->affected_rows();
    }
}