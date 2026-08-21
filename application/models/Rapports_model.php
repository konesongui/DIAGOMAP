<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rapports_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->upload_path = "./uploads/front_office/rapports/";
    }

    // ========================================== //
    // RÉCUPÉRER TOUS LES RAPPORTS                //
    // ========================================== //
    public function get_all($id = null) {
        $this->db->select('*');
        $this->db->from('rapports');
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
    // RÉCUPÉRER UN RAPPORT PAR ID                //
    // ========================================== //
    public function get_by_id($id) {
        $this->db->select('*');
        $this->db->from('rapports');
        $this->db->where('id', $id);
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    // ========================================== //
    // AJOUTER UN RAPPORT                         //
    // ========================================== //
    public function add($data) {
        $this->db->insert('rapports', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UN RAPPORT                   //
    // ========================================== //
    public function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('rapports', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UN RAPPORT (SOFT DELETE)         //
    // ========================================== //
    public function delete($id) {
        // Récupérer le fichier avant suppression
        $rapport = $this->get_by_id($id);
        if ($rapport && !empty($rapport['fichier'])) {
            $this->delete_file($rapport['fichier']);
        }

        $this->db->where('id', $id);
        $this->db->update('rapports', array('deleted' => 1));
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
    // STATISTIQUES DES RAPPORTS                  //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total
        $this->db->select('COUNT(*) as total');
        $this->db->from('rapports');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total'] = (int)$query->row()->total;

        // En attente
        $this->db->select('COUNT(*) as total');
        $this->db->from('rapports');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'en_attente');
        $query = $this->db->get();
        $stats['en_attente'] = (int)$query->row()->total;

        // En cours
        $this->db->select('COUNT(*) as total');
        $this->db->from('rapports');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'en_cours');
        $query = $this->db->get();
        $stats['en_cours'] = (int)$query->row()->total;

        // Terminés
        $this->db->select('COUNT(*) as total');
        $this->db->from('rapports');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'termine');
        $query = $this->db->get();
        $stats['termine'] = (int)$query->row()->total;

        // Archivés
        $this->db->select('COUNT(*) as total');
        $this->db->from('rapports');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'archive');
        $query = $this->db->get();
        $stats['archive'] = (int)$query->row()->total;

        // Par type
        $this->db->select('type_rapport, COUNT(*) as total');
        $this->db->from('rapports');
        $this->db->where('deleted', 0);
        $this->db->group_by('type_rapport');
        $query = $this->db->get();
        $stats['types'] = $query->result_array();

        return $stats;
    }

    // ========================================== //
    // RÉCUPÉRER LES RAPPORTS FILTRÉS             //
    // ========================================== //
    public function get_filtered($type_rapport = null, $statut = null, $date_from = null, $date_to = null) {
        $this->db->select('*');
        $this->db->from('rapports');
        $this->db->where('deleted', 0);

        if (!empty($type_rapport)) {
            $this->db->where('type_rapport', $type_rapport);
        }

        if (!empty($statut)) {
            $this->db->where('statut', $statut);
        }

        if (!empty($date_from)) {
            $this->db->where('date_creation >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('date_creation <=', $date_to);
        }

        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES TYPES DE RAPPORTS            //
    // ========================================== //
    public function get_types() {
        $this->db->distinct();
        $this->db->select('type_rapport');
        $this->db->from('rapports');
        $this->db->where('deleted', 0);
        $this->db->where('type_rapport !=', '');
        $this->db->order_by('type_rapport', 'ASC');
        $query = $this->db->get();
        $result = $query->result_array();
        return array_column($result, 'type_rapport');
    }

    // ========================================== //
    // RÉCUPÉRER LES STATUTS                      //
    // ========================================== //
    public function get_statuses() {
        return [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'archive' => 'Archivé'
        ];
    }

    // ========================================== //
    // RÉCUPÉRER LES PRIORITÉS                    //
    // ========================================== //
    public function get_priorities() {
        return [
            'basse' => 'Basse',
            'normale' => 'Normale',
            'haute' => 'Haute',
            'urgente' => 'Urgente'
        ];
    }

    // ========================================== //
    // GET STATUS LABEL                           //
    // ========================================== //
    public function get_status_label($statut) {
        $labels = [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'archive' => 'Archivé'
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
            'archive' => 'archive'
        ];
        return $badges[$statut] ?? 'en-attente';
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
    // GET TYPE LABEL                             //
    // ========================================== //
    public function get_type_label($type_rapport) {
        $labels = [
            'finance' => 'Rapport financier',
            'statistique' => 'Rapport statistique',
            'projet' => 'Rapport de projet',
            'activite' => 'Rapport d\'activité',
            'rh' => 'Rapport RH',
            'vente' => 'Rapport de vente',
            'inventaire' => 'Rapport d\'inventaire',
            'autre' => 'Autre'
        ];
        return $labels[$type_rapport] ?? $type_rapport;
    }

    // ========================================== //
    // RECHERCHER DES RAPPORTS                    //
    // ========================================== //
    public function search($keyword) {
        $this->db->select('*');
        $this->db->from('rapports');
        $this->db->where('deleted', 0);
        $this->db->group_start();
        $this->db->like('titre', $keyword);
        $this->db->or_like('description', $keyword);
        $this->db->or_like('type_rapport', $keyword);
        $this->db->group_end();
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // COMPTER LES RAPPORTS PAR STATUT            //
    // ========================================== //
    public function count_by_status($statut) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('rapports');
        $this->db->where('deleted', 0);
        $this->db->where('statut', $statut);
        $query = $this->db->get();
        return (int)$query->row()->total;
    }

    // ========================================== //
    // COMPTER LES RAPPORTS PAR TYPE              //
    // ========================================== //
    public function count_by_type($type_rapport) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('rapports');
        $this->db->where('deleted', 0);
        $this->db->where('type_rapport', $type_rapport);
        $query = $this->db->get();
        return (int)$query->row()->total;
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
    // GET FILE ICON BY TYPE                      //
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
            'zip' => 'fa-file-archive-o'
        ];
        return $icons[$type] ?? 'fa-file-o';
    }

    // ========================================== //
    // SUPPRESSION MULTIPLE                       //
    // ========================================== //
    public function delete_multiple($ids) {
        if (empty($ids)) {
            return false;
        }

        // Récupérer les fichiers
        $this->db->select('fichier');
        $this->db->from('rapports');
        $this->db->where_in('id', $ids);
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $files = $query->result_array();

        foreach ($files as $file) {
            if (!empty($file['fichier'])) {
                $this->delete_file($file['fichier']);
            }
        }

        $this->db->where_in('id', $ids);
        $this->db->update('rapports', array('deleted' => 1));
        return $this->db->affected_rows();
    }
}