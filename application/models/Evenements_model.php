<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Evenements_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->upload_path = "./uploads/evenements/";
    }

    // ========================================== //
    // RÉCUPÉRER TOUS LES ÉVÉNEMENTS              //
    // ========================================== //
    public function get_all($id = null) {
        $this->db->select('*');
        $this->db->from('evenements');
        $this->db->where('deleted', 0);

        if ($id != null) {
            $this->db->where('id', $id);
            $query = $this->db->get();
            return $query->row_array();
        }

        $this->db->order_by('date_debut', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER UN ÉVÉNEMENT PAR ID              //
    // ========================================== //
    public function get_by_id($id) {
        $this->db->select('*');
        $this->db->from('evenements');
        $this->db->where('id', $id);
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    // ========================================== //
    // AJOUTER UN ÉVÉNEMENT                       //
    // ========================================== //
    public function add($data) {
        $this->db->insert('evenements', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UN ÉVÉNEMENT                 //
    // ========================================== //
    public function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('evenements', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UN ÉVÉNEMENT (SOFT DELETE)       //
    // ========================================== //
    public function delete($id) {
        $evenement = $this->get_by_id($id);
        if ($evenement && !empty($evenement['image'])) {
            $this->delete_file($evenement['image']);
        }

        $this->db->where('id', $id);
        $this->db->update('evenements', array('deleted' => 1));
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER LE FICHIER PHYSIQUE              //
    // ========================================== //
    private function delete_file($filename) {
        $filepath = $this->upload_path . $filename;
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }

    // ========================================== //
    // STATISTIQUES DES ÉVÉNEMENTS                //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total
        $this->db->select('COUNT(*) as total');
        $this->db->from('evenements');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total'] = (int)$query->row()->total;

        // Par type
        $types = ['culte', 'conférence', 'séminaire', 'formation', 'concert', 'réveil', 'jeunesse', 'enfants', 'autre'];
        foreach ($types as $type) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('evenements');
            $this->db->where('deleted', 0);
            $this->db->where('type_evenement', $type);
            $query = $this->db->get();
            $stats['type_' . $type] = (int)$query->row()->total;
        }

        // Par statut
        $statuses = ['planifie', 'en_cours', 'termine', 'annule'];
        foreach ($statuses as $status) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('evenements');
            $this->db->where('deleted', 0);
            $this->db->where('statut', $status);
            $query = $this->db->get();
            $stats['statut_' . $status] = (int)$query->row()->total;
        }

        // Événements à venir
        $today = date('Y-m-d');
        $this->db->select('COUNT(*) as total');
        $this->db->from('evenements');
        $this->db->where('deleted', 0);
        $this->db->where('date_debut >=', $today);
        $this->db->where('statut !=', 'annule');
        $query = $this->db->get();
        $stats['a_venir'] = (int)$query->row()->total;

        return $stats;
    }

    // ========================================== //
    // RÉCUPÉRER LES ÉVÉNEMENTS FILTRÉS           //
    // ========================================== //
    public function get_filtered($type = null, $statut = null, $date_from = null, $date_to = null) {
        $this->db->select('*');
        $this->db->from('evenements');
        $this->db->where('deleted', 0);

        if (!empty($type)) {
            $this->db->where('type_evenement', $type);
        }

        if (!empty($statut)) {
            $this->db->where('statut', $statut);
        }

        if (!empty($date_from)) {
            $this->db->where('date_debut >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('date_debut <=', $date_to);
        }

        $this->db->order_by('date_debut', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES TYPES D'ÉVÉNEMENTS           //
    // ========================================== //
    public function get_types() {
        return [
            'culte' => 'Culte',
            'conférence' => 'Conférence',
            'séminaire' => 'Séminaire',
            'formation' => 'Formation',
            'concert' => 'Concert',
            'réveil' => 'Réveil',
            'jeunesse' => 'Jeunesse',
            'enfants' => 'Enfants',
            'autre' => 'Autre'
        ];
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
    // GET TYPE LABEL                             //
    // ========================================== //
    public function get_type_label($type) {
        $labels = [
            'culte' => 'Culte',
            'conférence' => 'Conférence',
            'séminaire' => 'Séminaire',
            'formation' => 'Formation',
            'concert' => 'Concert',
            'réveil' => 'Réveil',
            'jeunesse' => 'Jeunesse',
            'enfants' => 'Enfants',
            'autre' => 'Autre'
        ];
        return $labels[$type] ?? $type;
    }

    // ========================================== //
    // GET TYPE BADGE CLASS                       //
    // ========================================== //
    public function get_type_badge($type) {
        $badges = [
            'culte' => 'culte',
            'conférence' => 'conference',
            'séminaire' => 'seminaire',
            'formation' => 'formation',
            'concert' => 'concert',
            'réveil' => 'reveil',
            'jeunesse' => 'jeunesse',
            'enfants' => 'enfants',
            'autre' => 'autre'
        ];
        return $badges[$type] ?? 'autre';
    }

    // ========================================== //
    // RECHERCHER DES ÉVÉNEMENTS                  //
    // ========================================== //
    public function search($keyword) {
        $this->db->select('*');
        $this->db->from('evenements');
        $this->db->where('deleted', 0);
        $this->db->group_start();
        $this->db->like('titre', $keyword);
        $this->db->or_like('description', $keyword);
        $this->db->or_like('lieu', $keyword);
        $this->db->or_like('organisateur', $keyword);
        $this->db->group_end();
        $this->db->order_by('date_debut', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // COMPTER PAR STATUT                         //
    // ========================================== //
    public function count_by_status($statut) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('evenements');
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
        $this->db->from('evenements');
        $this->db->where('deleted', 0);
        $this->db->where('type_evenement', $type);
        $query = $this->db->get();
        return (int)$query->row()->total;
    }

    // ========================================== //
    // RÉCUPÉRER LES ÉVÉNEMENTS À VENIR           //
    // ========================================== //
    public function get_upcoming($limit = 10) {
        $today = date('Y-m-d');
        $this->db->select('*');
        $this->db->from('evenements');
        $this->db->where('deleted', 0);
        $this->db->where('date_debut >=', $today);
        $this->db->where('statut !=', 'annule');
        $this->db->order_by('date_debut', 'ASC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result_array();
    }
}