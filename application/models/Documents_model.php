<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Documents_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->upload_path = "./uploads/front_office/documents/";
    }

    // ========================================== //
    // RÉCUPÉRER TOUS LES DOCUMENTS               //
    // ========================================== //
    public function get_all($id = null) {
        $this->db->select('*');
        $this->db->from('documents');
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
    // RÉCUPÉRER UN DOCUMENT PAR ID               //
    // ========================================== //
    public function get_by_id($id) {
        $this->db->select('*');
        $this->db->from('documents');
        $this->db->where('id', $id);
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    // ========================================== //
    // AJOUTER UN DOCUMENT                        //
    // ========================================== //
    public function add($data) {
        $this->db->insert('documents', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UN DOCUMENT                  //
    // ========================================== //
    public function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('documents', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UN DOCUMENT (SOFT DELETE)        //
    // ========================================== //
    public function delete($id) {
        // Récupérer le fichier avant suppression
        $doc = $this->get_by_id($id);
        if ($doc && !empty($doc['fichier'])) {
            $this->delete_file($doc['fichier']);
        }

        $this->db->where('id', $id);
        $this->db->update('documents', array('deleted' => 1, 'statut' => 'supprime'));
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER DÉFINITIVEMENT UN DOCUMENT       //
    // ========================================== //
    public function delete_permanent($id) {
        $doc = $this->get_by_id($id);
        if ($doc && !empty($doc['fichier'])) {
            $this->delete_file($doc['fichier']);
        }

        $this->db->where('id', $id);
        $this->db->delete('documents');
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
    // STATISTIQUES DES DOCUMENTS                 //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total
        $this->db->select('COUNT(*) as total');
        $this->db->from('documents');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total'] = (int)$query->row()->total;

        // Actifs
        $this->db->select('COUNT(*) as total');
        $this->db->from('documents');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'actif');
        $query = $this->db->get();
        $stats['actif'] = (int)$query->row()->total;

        // Archivés
        $this->db->select('COUNT(*) as total');
        $this->db->from('documents');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'archive');
        $query = $this->db->get();
        $stats['archive'] = (int)$query->row()->total;

        // Par catégorie
        $this->db->select('categorie, COUNT(*) as total');
        $this->db->from('documents');
        $this->db->where('deleted', 0);
        $this->db->group_by('categorie');
        $query = $this->db->get();
        $stats['categories'] = $query->result_array();

        // Taille totale
        $this->db->select('SUM(taille) as total_size');
        $this->db->from('documents');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total_size'] = $query->row()->total_size ?? 0;

        return $stats;
    }

    // ========================================== //
    // RÉCUPÉRER LES DOCUMENTS FILTRÉS            //
    // ========================================== //
    public function get_filtered($categorie = null, $statut = null, $search = null) {
        $this->db->select('*');
        $this->db->from('documents');
        $this->db->where('deleted', 0);

        if (!empty($categorie)) {
            $this->db->where('categorie', $categorie);
        }

        if (!empty($statut)) {
            $this->db->where('statut', $statut);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('titre', $search);
            $this->db->or_like('description', $search);
            $this->db->group_end();
        }

        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES CATÉGORIES                   //
    // ========================================== //
    public function get_categories() {
        $this->db->distinct();
        $this->db->select('categorie');
        $this->db->from('documents');
        $this->db->where('deleted', 0);
        $this->db->where('categorie !=', '');
        $this->db->order_by('categorie', 'ASC');
        $query = $this->db->get();
        $result = $query->result_array();
        return array_column($result, 'categorie');
    }

    // ========================================== //
    // RÉCUPÉRER LA TAILLE DU FICHIER             //
    // ========================================== //
    public function get_file_size($id) {
        $this->db->select('taille');
        $this->db->from('documents');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $row = $query->row();
        return $row->taille ?? null;
    }

    // ========================================== //
    // RECHERCHER DES DOCUMENTS                   //
    // ========================================== //
    public function search($keyword) {
        $this->db->select('*');
        $this->db->from('documents');
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
    // RÉCUPÉRER LES DOCUMENTS RÉCENTS            //
    // ========================================== //
    public function get_recent($limit = 10) {
        $this->db->select('*');
        $this->db->from('documents');
        $this->db->where('deleted', 0);
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // FORMATER LA TAILLE DU FICHIER              //
    // ========================================== //
    public function format_size($size) {
        if ($size >= 1073741824) {
            $size = number_format($size / 1073741824, 2) . ' GB';
        } elseif ($size >= 1048576) {
            $size = number_format($size / 1048576, 2) . ' MB';
        } elseif ($size >= 1024) {
            $size = number_format($size / 1024, 2) . ' KB';
        } else {
            $size = $size . ' B';
        }
        return $size;
    }

    // ========================================== //
    // GET CATEGORY LABEL                         //
    // ========================================== //
    public function get_category_label($categorie) {
        $labels = [
            'contrat' => 'Contrats',
            'facture' => 'Factures',
            'rapport' => 'Rapports',
            'projet' => 'Projets',
            'rh' => 'Ressources Humaines',
            'finance' => 'Finances',
            'marketing' => 'Marketing',
            'technique' => 'Technique',
            'autre' => 'Autre'
        ];
        return $labels[$categorie] ?? $categorie;
    }

    // ========================================== //
    // GET STATUS LABEL                           //
    // ========================================== //
    public function get_status_label($statut) {
        $labels = [
            'actif' => 'Actif',
            'archive' => 'Archivé',
            'supprime' => 'Supprimé'
        ];
        return $labels[$statut] ?? $statut;
    }

    // ========================================== //
    // GET STATUS BADGE CLASS                     //
    // ========================================== //
    public function get_status_badge($statut) {
        $badges = [
            'actif' => 'actif',
            'archive' => 'archive',
            'supprime' => 'supprime'
        ];
        return $badges[$statut] ?? 'actif';
    }

    // ========================================== //
    // GET ICON BY FILE TYPE                      //
    // ========================================== //
    public function get_file_icon($type) {
        $icons = [
            'pdf' => 'fa-file-pdf-o',
            'doc' => 'fa-file-word-o',
            'docx' => 'fa-file-word-o',
            'xls' => 'fa-file-excel-o',
            'xlsx' => 'fa-file-excel-o',
            'ppt' => 'fa-file-powerpoint-o',
            'pptx' => 'fa-file-powerpoint-o',
            'jpg' => 'fa-file-image-o',
            'jpeg' => 'fa-file-image-o',
            'png' => 'fa-file-image-o',
            'gif' => 'fa-file-image-o',
            'zip' => 'fa-file-archive-o',
            'rar' => 'fa-file-archive-o',
            'txt' => 'fa-file-text-o',
            'csv' => 'fa-file-text-o'
        ];
        return $icons[$type] ?? 'fa-file-o';
    }

    // ========================================== //
    // GET FILE TYPE COLOR                        //
    // ========================================== //
    public function get_file_color($type) {
        $colors = [
            'pdf' => '#dc2626',
            'doc' => '#2563eb',
            'docx' => '#2563eb',
            'xls' => '#16a34a',
            'xlsx' => '#16a34a',
            'ppt' => '#d97706',
            'pptx' => '#d97706',
            'jpg' => '#8b5cf6',
            'jpeg' => '#8b5cf6',
            'png' => '#8b5cf6',
            'zip' => '#6b7280',
            'rar' => '#6b7280'
        ];
        return $colors[$type] ?? '#6b7280';
    }
}