<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Demande_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ========================================== //
    // RÉCUPÉRER TOUTES LES DEMANDES              //
    // ========================================== //
    public function get_all($id = null) {
        $this->db->select('*');
        $this->db->from('demandes');
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
    // RÉCUPÉRER UNE DEMANDE PAR ID               //
    // ========================================== //
    public function get_by_id($id) {
        $this->db->select('*');
        $this->db->from('demandes');
        $this->db->where('id', $id);
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    // ========================================== //
    // AJOUTER UNE DEMANDE                        //
    // ========================================== //
    public function add($data) {
        $this->db->insert('demandes', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UNE DEMANDE                  //
    // ========================================== //
    public function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('demandes', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UNE DEMANDE (SOFT DELETE)        //
    // ========================================== //
    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->update('demandes', array('deleted' => 1));
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER DÉFINITIVEMENT UNE DEMANDE       //
    // ========================================== //
    public function delete_permanent($id) {
        $this->db->where('id', $id);
        $this->db->delete('demandes');
        return $this->db->affected_rows();
    }

    // ========================================== //
    // STATISTIQUES DES DEMANDES                  //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total
        $this->db->select('COUNT(*) as total');
        $this->db->from('demandes');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total'] = (int)$query->row()->total;

        // En attente
        $this->db->select('COUNT(*) as total');
        $this->db->from('demandes');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'en_attente');
        $query = $this->db->get();
        $stats['en_attente'] = (int)$query->row()->total;

        // En cours
        $this->db->select('COUNT(*) as total');
        $this->db->from('demandes');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'en_cours');
        $query = $this->db->get();
        $stats['en_cours'] = (int)$query->row()->total;

        // Terminé
        $this->db->select('COUNT(*) as total');
        $this->db->from('demandes');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'termine');
        $query = $this->db->get();
        $stats['termine'] = (int)$query->row()->total;

        // Rejeté
        $this->db->select('COUNT(*) as total');
        $this->db->from('demandes');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'rejete');
        $query = $this->db->get();
        $stats['rejete'] = (int)$query->row()->total;

        return $stats;
    }

    // ========================================== //
    // STATISTIQUES PAR CATÉGORIE                 //
    // ========================================== //
    public function get_stats_by_category() {
        $this->db->select('categorie, COUNT(*) as total');
        $this->db->from('demandes');
        $this->db->where('deleted', 0);
        $this->db->group_by('categorie');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // STATISTIQUES PAR PRIORITÉ                  //
    // ========================================== //
    public function get_stats_by_priority() {
        $this->db->select('priorite, COUNT(*) as total');
        $this->db->from('demandes');
        $this->db->where('deleted', 0);
        $this->db->group_by('priorite');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES DEMANDES FILTRÉES            //
    // ========================================== //
    public function get_filtered($statut = null, $categorie = null, $priorite = null) {
        $this->db->select('*');
        $this->db->from('demandes');
        $this->db->where('deleted', 0);

        if (!empty($statut)) {
            $this->db->where('statut', $statut);
        }

        if (!empty($categorie)) {
            $this->db->where('categorie', $categorie);
        }

        if (!empty($priorite)) {
            $this->db->where('priorite', $priorite);
        }

        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RECHERCHER DES DEMANDES                    //
    // ========================================== //
    public function search($keyword) {
        $this->db->select('*');
        $this->db->from('demandes');
        $this->db->where('deleted', 0);
        $this->db->group_start();
        $this->db->like('titre', $keyword);
        $this->db->or_like('description', $keyword);
        $this->db->or_like('categorie', $keyword);
        $this->db->group_end();
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES DEMANDES PAR STATUT          //
    // ========================================== //
    public function get_by_status($statut) {
        $this->db->select('*');
        $this->db->from('demandes');
        $this->db->where('deleted', 0);
        $this->db->where('statut', $statut);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES DEMANDES PAR CATÉGORIE       //
    // ========================================== //
    public function get_by_category($categorie) {
        $this->db->select('*');
        $this->db->from('demandes');
        $this->db->where('deleted', 0);
        $this->db->where('categorie', $categorie);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES DEMANDES PAR PRIORITÉ        //
    // ========================================== //
    public function get_by_priority($priorite) {
        $this->db->select('*');
        $this->db->from('demandes');
        $this->db->where('deleted', 0);
        $this->db->where('priorite', $priorite);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES DEMANDES RÉCENTES            //
    // ========================================== //
    public function get_recent($limit = 10) {
        $this->db->select('*');
        $this->db->from('demandes');
        $this->db->where('deleted', 0);
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // COMPTER LES DEMANDES PAR STATUT            //
    // ========================================== //
    public function count_by_status($statut) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('demandes');
        $this->db->where('deleted', 0);
        $this->db->where('statut', $statut);
        $query = $this->db->get();
        return (int)$query->row()->total;
    }

    // ========================================== //
    // COMPTER LES DEMANDES PAR CATÉGORIE         //
    // ========================================== //
    public function count_by_category($categorie) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('demandes');
        $this->db->where('deleted', 0);
        $this->db->where('categorie', $categorie);
        $query = $this->db->get();
        return (int)$query->row()->total;
    }

    // ========================================== //
    // COMPTER LES DEMANDES PAR PRIORITÉ          //
    // ========================================== //
    public function count_by_priority($priorite) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('demandes');
        $this->db->where('deleted', 0);
        $this->db->where('priorite', $priorite);
        $query = $this->db->get();
        return (int)$query->row()->total;
    }

    // ========================================== //
    // COMPTER LES DEMANDES DU JOUR               //
    // ========================================== //
    public function count_today() {
        $today = date('Y-m-d');
        $this->db->select('COUNT(*) as total');
        $this->db->from('demandes');
        $this->db->where('deleted', 0);
        $this->db->where('DATE(date_creation)', $today);
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
        $this->db->update('demandes', array('deleted' => 1));
        return $this->db->affected_rows();
    }

    // ========================================== //
    // GET STATUS LABEL                           //
    // ========================================== //
    public function get_status_label($statut) {
        $labels = [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'rejete' => 'Rejeté'
        ];
        return $labels[$statut] ?? $statut;
    }

    // ========================================== //
    // GET STATUS BADGE CLASS                     //
    // ========================================== //
    public function get_status_badge($statut) {
        $badges = [
            'en_attente' => 'en-attente',
            'en_cours' => 'en-cours',
            'termine' => 'termine',
            'rejete' => 'rejete'
        ];
        return $badges[$statut] ?? 'en-attente';
    }

    // ========================================== //
    // GET PRIORITY LABEL                         //
    // ========================================== //
    public function get_priority_label($priorite) {
        $labels = [
            'basse' => 'Basse',
            'normale' => 'Normale',
            'haute' => 'Haute',
            'urgente' => 'Urgente'
        ];
        return $labels[$priorite] ?? $priorite;
    }

    // ========================================== //
    // GET PRIORITY BADGE CLASS                   //
    // ========================================== //
    public function get_priority_badge($priorite) {
        $badges = [
            'basse' => 'basse',
            'normale' => 'normale',
            'haute' => 'haute',
            'urgente' => 'urgente'
        ];
        return $badges[$priorite] ?? 'normale';
    }

    // ========================================== //
    // GET CATEGORY LABEL                         //
    // ========================================== //
    public function get_category_label($categorie) {
        $labels = [
            'comptabilite' => 'Comptabilité',
            'ressources_humaines' => 'Ressources Humaines',
            'informatique' => 'Informatique',
            'logistique' => 'Logistique',
            'communication' => 'Communication',
            'autre' => 'Autre'
        ];
        return $labels[$categorie] ?? $categorie;
    }
}