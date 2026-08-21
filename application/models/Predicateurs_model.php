<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Predicateurs_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->upload_path = "./uploads/predicateurs/";
    }

    // ========================================== //
    // RÉCUPÉRER TOUS LES PRÉDICATEURS            //
    // ========================================== //
    public function get_all($id = null) {
        $this->db->select('*');
        $this->db->from('predicateurs');
        $this->db->where('deleted', 0);

        if ($id != null) {
            $this->db->where('id', $id);
            $query = $this->db->get();
            if ($query->num_rows() == 1) {
                return $query->row_array();
            }
            return null;
        }

        $this->db->order_by('nom', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER UN PRÉDICATEUR PAR ID            //
    // ========================================== //
    public function get_by_id($id) {
        $this->db->select('*');
        $this->db->from('predicateurs');
        $this->db->where('id', $id);
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row_array();
        }
        return null;
    }

    // ========================================== //
    // AJOUTER UN PRÉDICATEUR                     //
    // ========================================== //
    public function add($data) {
        $this->db->insert('predicateurs', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UN PRÉDICATEUR               //
    // ========================================== //
    public function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('predicateurs', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UN PRÉDICATEUR (SOFT DELETE)     //
    // ========================================== //
    public function delete($id) {
        $predicateur = $this->get_by_id($id);
        if ($predicateur && !empty($predicateur['photo'])) {
            $this->delete_file($predicateur['photo']);
        }

        $this->db->where('id', $id);
        $this->db->update('predicateurs', array('deleted' => 1));
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
    // STATISTIQUES DES PRÉDICATEURS              //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total
        $this->db->select('COUNT(*) as total');
        $this->db->from('predicateurs');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total'] = (int)$query->row()->total;

        // Par statut
        $statuses = ['actif', 'inactif', 'vacation', 'retraite'];
        foreach ($statuses as $status) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('predicateurs');
            $this->db->where('deleted', 0);
            $this->db->where('statut', $status);
            $query = $this->db->get();
            $stats['statut_' . $status] = (int)$query->row()->total;
        }

        // Par sexe
        $sexes = ['M', 'F'];
        foreach ($sexes as $sexe) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('predicateurs');
            $this->db->where('deleted', 0);
            $this->db->where('sexe', $sexe);
            $query = $this->db->get();
            $stats['sexe_' . $sexe] = (int)$query->row()->total;
        }

        // Années d'expérience moyennes
        $this->db->select('AVG(annees_experience) as moyenne');
        $this->db->from('predicateurs');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['experience_moyenne'] = (float)$query->row()->moyenne;

        return $stats;
    }

    // ========================================== //
    // RÉCUPÉRER LES PRÉDICATEURS FILTRÉS         //
    // ========================================== //
    public function get_filtered($statut = null, $sexe = null, $specialite = null) {
        $this->db->select('*');
        $this->db->from('predicateurs');
        $this->db->where('deleted', 0);

        if (!empty($statut)) {
            $this->db->where('statut', $statut);
        }

        if (!empty($sexe)) {
            $this->db->where('sexe', $sexe);
        }

        if (!empty($specialite)) {
            $this->db->like('specialite', $specialite);
        }

        $this->db->order_by('nom', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES STATUTS                      //
    // ========================================== //
    public function get_statuses() {
        return [
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'vacation' => 'Vacation',
            'retraite' => 'Retraite'
        ];
    }

    // ========================================== //
    // GET STATUS LABEL                           //
    // ========================================== //
    public function get_status_label($statut) {
        $labels = [
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'vacation' => 'Vacation',
            'retraite' => 'Retraite'
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
            'vacation' => 'vacation',
            'retraite' => 'retraite'
        ];
        return $badges[$statut] ?? 'actif';
    }

    // ========================================== //
    // RECHERCHER DES PRÉDICATEURS                //
    // ========================================== //
    public function search($keyword) {
        $this->db->select('*');
        $this->db->from('predicateurs');
        $this->db->where('deleted', 0);
        $this->db->group_start();
        $this->db->like('nom', $keyword);
        $this->db->or_like('prenom', $keyword);
        $this->db->or_like('specialite', $keyword);
        $this->db->or_like('telephone', $keyword);
        $this->db->or_like('email', $keyword);
        $this->db->group_end();
        $this->db->order_by('nom', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // COMPTER PAR STATUT                         //
    // ========================================== //
    public function count_by_status($statut) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('predicateurs');
        $this->db->where('deleted', 0);
        $this->db->where('statut', $statut);
        $query = $this->db->get();
        return (int)$query->row()->total;
    }
}