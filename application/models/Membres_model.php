<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Membres_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->upload_path = "./uploads/membres/";
    }

    // ========================================== //
    // RÉCUPÉRER TOUS LES MEMBRES                 //
    // ========================================== //
    public function get_all($id = null) {
        $this->db->select('*');
        $this->db->from('membres');
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
    // RÉCUPÉRER UN MEMBRE PAR ID                 //
    // ========================================== //
    public function get_by_id($id) {
        $this->db->select('*');
        $this->db->from('membres');
        $this->db->where('id', $id);
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    // ========================================== //
    // GÉNÉRER UN CODE DE MEMBRE                  //
    // ========================================== //
    public function generate_code() {
        $prefix = 'M';
        $year = date('Y');
        $last = $this->db->query("SELECT MAX(CAST(SUBSTRING(code_membre, 6) AS UNSIGNED)) as last FROM membres WHERE code_membre LIKE '$prefix-$year-%'")->row()->last;
        $next = str_pad(($last + 1), 4, '0', STR_PAD_LEFT);
        return $prefix . '-' . $year . '-' . $next;
    }

    // ========================================== //
    // AJOUTER UN MEMBRE                          //
    // ========================================== //
    public function add($data) {
        $data['code_membre'] = $this->generate_code();
        $this->db->insert('membres', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UN MEMBRE                    //
    // ========================================== //
    public function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('membres', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UN MEMBRE (SOFT DELETE)          //
    // ========================================== //
    public function delete($id) {
        $membre = $this->get_by_id($id);
        if ($membre && !empty($membre['photo'])) {
            $this->delete_file($membre['photo']);
        }

        $this->db->where('id', $id);
        $this->db->update('membres', array('deleted' => 1));
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
    // STATISTIQUES DES MEMBRES                   //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total
        $this->db->select('COUNT(*) as total');
        $this->db->from('membres');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total'] = (int)$query->row()->total;

        // Par statut
        $statuses = ['actif', 'inactif', 'transfert', 'decede'];
        foreach ($statuses as $status) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('membres');
            $this->db->where('deleted', 0);
            $this->db->where('statut_membre', $status);
            $query = $this->db->get();
            $stats['statut_' . $status] = (int)$query->row()->total;
        }

        // Par sexe
        $sexes = ['M', 'F'];
        foreach ($sexes as $sexe) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('membres');
            $this->db->where('deleted', 0);
            $this->db->where('sexe', $sexe);
            $query = $this->db->get();
            $stats['sexe_' . $sexe] = (int)$query->row()->total;
        }

        // Par rôle
        $roles = ['membre', 'diacre', 'ancien', 'pasteur', 'evangeliste', 'autre'];
        foreach ($roles as $role) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('membres');
            $this->db->where('deleted', 0);
            $this->db->where('role', $role);
            $query = $this->db->get();
            $stats['role_' . $role] = (int)$query->row()->total;
        }

        // Par département
        $this->db->select('departement, COUNT(*) as total');
        $this->db->from('membres');
        $this->db->where('deleted', 0);
        $this->db->where('departement !=', '');
        $this->db->group_by('departement');
        $query = $this->db->get();
        $stats['departements'] = $query->result_array();

        // Par cellule
        $this->db->select('groupe_cellule, COUNT(*) as total');
        $this->db->from('membres');
        $this->db->where('deleted', 0);
        $this->db->where('groupe_cellule !=', '');
        $this->db->group_by('groupe_cellule');
        $query = $this->db->get();
        $stats['cellules'] = $query->result_array();

        return $stats;
    }

    // ========================================== //
    // RÉCUPÉRER LES MEMBRES FILTRÉS              //
    // ========================================== //
    public function get_filtered($statut = null, $role = null, $sexe = null, $search = null) {
        $this->db->select('*');
        $this->db->from('membres');
        $this->db->where('deleted', 0);

        if (!empty($statut)) {
            $this->db->where('statut_membre', $statut);
        }

        if (!empty($role)) {
            $this->db->where('role', $role);
        }

        if (!empty($sexe)) {
            $this->db->where('sexe', $sexe);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('nom', $search);
            $this->db->or_like('prenom', $search);
            $this->db->or_like('code_membre', $search);
            $this->db->or_like('telephone', $search);
            $this->db->or_like('email', $search);
            $this->db->group_end();
        }

        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES RÔLES                        //
    // ========================================== //
    public function get_roles() {
        return [
            'membre' => 'Membre',
            'diacre' => 'Diacre',
            'ancien' => 'Ancien',
            'pasteur' => 'Pasteur',
            'evangeliste' => 'Évangéliste',
            'autre' => 'Autre'
        ];
    }

    // ========================================== //
    // RÉCUPÉRER LES STATUTS                      //
    // ========================================== //
    public function get_statuses() {
        return [
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'transfert' => 'En transfert',
            'decede' => 'Décédé'
        ];
    }

    // ========================================== //
    // GET STATUS LABEL                           //
    // ========================================== //
    public function get_status_label($statut) {
        $labels = [
            'actif' => 'Actif',
            'inactif' => 'Inactif',
            'transfert' => 'En transfert',
            'decede' => 'Décédé'
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
            'transfert' => 'transfert',
            'decede' => 'decede'
        ];
        return $badges[$statut] ?? 'actif';
    }

    // ========================================== //
    // GET ROLE LABEL                             //
    // ========================================== //
    public function get_role_label($role) {
        $labels = [
            'membre' => 'Membre',
            'diacre' => 'Diacre',
            'ancien' => 'Ancien',
            'pasteur' => 'Pasteur',
            'evangeliste' => 'Évangéliste',
            'autre' => 'Autre'
        ];
        return $labels[$role] ?? $role;
    }

    // ========================================== //
    // GET ROLE BADGE CLASS                       //
    // ========================================== //
    public function get_role_badge($role) {
        $badges = [
            'membre' => 'membre',
            'diacre' => 'diacre',
            'ancien' => 'ancien',
            'pasteur' => 'pasteur',
            'evangeliste' => 'evangeliste',
            'autre' => 'autre'
        ];
        return $badges[$role] ?? 'membre';
    }

    // ========================================== //
    // RECHERCHER DES MEMBRES                     //
    // ========================================== //
    public function search($keyword) {
        $this->db->select('*');
        $this->db->from('membres');
        $this->db->where('deleted', 0);
        $this->db->group_start();
        $this->db->like('nom', $keyword);
        $this->db->or_like('prenom', $keyword);
        $this->db->or_like('code_membre', $keyword);
        $this->db->or_like('telephone', $keyword);
        $this->db->or_like('email', $keyword);
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
        $this->db->from('membres');
        $this->db->where('deleted', 0);
        $this->db->where('statut_membre', $statut);
        $query = $this->db->get();
        return (int)$query->row()->total;
    }

    // ========================================== //
    // COMPTER PAR RÔLE                           //
    // ========================================== //
    public function count_by_role($role) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('membres');
        $this->db->where('deleted', 0);
        $this->db->where('role', $role);
        $query = $this->db->get();
        return (int)$query->row()->total;
    }
}