<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Funerailles_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->upload_path = "./uploads/funerailles/";
    }

    // ========================================== //
    // RÉCUPÉRER TOUTES LES FUNÉRAILLES           //
    // ========================================== //
    public function get_all($id = null) {
        $this->db->select('f.*, m.nom as membre_nom, m.prenom as membre_prenom, m.code_membre');
        $this->db->from('funerailles f');
        $this->db->join('membres m', 'f.defunt_id = m.id', 'left');
        $this->db->where('f.deleted', 0);

        if ($id != null) {
            $this->db->where('f.id', $id);
            $query = $this->db->get();
            if ($query->num_rows() == 1) {
                return $query->row_array();
            }
            return null;
        }

        $this->db->order_by('f.date_funerailles', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER UNE FUNÉRAILLE PAR ID            //
    // ========================================== //
    public function get_by_id($id) {
        $this->db->select('f.*, m.nom as membre_nom, m.prenom as membre_prenom, m.code_membre');
        $this->db->from('funerailles f');
        $this->db->join('membres m', 'f.defunt_id = m.id', 'left');
        $this->db->where('f.id', $id);
        $this->db->where('f.deleted', 0);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row_array();
        }
        return null;
    }

    // ========================================== //
    // GÉNÉRER UN CODE DE FUNÉRAILLES             //
    // ========================================== //
    public function generate_code() {
        $prefix = 'FUN';
        $year = date('Y');
        $last = $this->db->query("SELECT MAX(CAST(SUBSTRING(code_funerailles, 8) AS UNSIGNED)) as last FROM funerailles WHERE code_funerailles LIKE '$prefix-$year-%'")->row()->last;
        $next = str_pad(($last + 1), 4, '0', STR_PAD_LEFT);
        return $prefix . '-' . $year . '-' . $next;
    }

    // ========================================== //
    // AJOUTER UNE FUNÉRAILLE                     //
    // ========================================== //
    public function add($data) {
        $data['code_funerailles'] = $this->generate_code();
        $this->db->insert('funerailles', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UNE FUNÉRAILLE               //
    // ========================================== //
    public function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('funerailles', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UNE FUNÉRAILLE (SOFT DELETE)     //
    // ========================================== //
    public function delete($id) {
        $funerailles = $this->get_by_id($id);
        if ($funerailles && !empty($funerailles['defunt_photo'])) {
            $this->delete_file($funerailles['defunt_photo']);
        }

        $this->db->where('id', $id);
        $this->db->update('funerailles', array('deleted' => 1));
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
    // STATISTIQUES DES FUNÉRAILLES               //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total
        $this->db->select('COUNT(*) as total');
        $this->db->from('funerailles');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total'] = (int)$query->row()->total;

        // Par statut
        $statuses = ['planifie', 'effectue', 'annule', 'reporte'];
        foreach ($statuses as $status) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('funerailles');
            $this->db->where('deleted', 0);
            $this->db->where('statut', $status);
            $query = $this->db->get();
            $stats['statut_' . $status] = (int)$query->row()->total;
        }

        // Par type
        $types = ['enterrement', 'incineration', 'depot_urne', 'autre'];
        foreach ($types as $type) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('funerailles');
            $this->db->where('deleted', 0);
            $this->db->where('type_ceremonie', $type);
            $query = $this->db->get();
            $stats['type_' . $type] = (int)$query->row()->total;
        }

        // Par sexe
        $sexes = ['M', 'F'];
        foreach ($sexes as $sexe) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('funerailles');
            $this->db->where('deleted', 0);
            $this->db->where('defunt_sexe', $sexe);
            $query = $this->db->get();
            $stats['sexe_' . $sexe] = (int)$query->row()->total;
        }

        // Certificats générés
        $this->db->select('COUNT(*) as total');
        $this->db->from('funerailles');
        $this->db->where('deleted', 0);
        $this->db->where('certificat_genere', 1);
        $query = $this->db->get();
        $stats['certificats'] = (int)$query->row()->total;

        return $stats;
    }

    // ========================================== //
    // RÉCUPÉRER LES FUNÉRAILLES FILTRÉES         //
    // ========================================== //
    public function get_filtered($statut = null, $type = null, $date_from = null, $date_to = null) {
        $this->db->select('f.*');
        $this->db->from('funerailles f');
        $this->db->where('f.deleted', 0);

        if (!empty($statut)) {
            $this->db->where('f.statut', $statut);
        }

        if (!empty($type)) {
            $this->db->where('f.type_ceremonie', $type);
        }

        if (!empty($date_from)) {
            $this->db->where('f.date_funerailles >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('f.date_funerailles <=', $date_to);
        }

        $this->db->order_by('f.date_funerailles', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES TYPES DE CÉRÉMONIE           //
    // ========================================== //
    public function get_types() {
        return [
            'enterrement' => 'Enterrement',
            'incineration' => 'Incinération',
            'depot_urne' => 'Dépôt d\'urne',
            'autre' => 'Autre'
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
            'enterrement' => 'Enterrement',
            'incineration' => 'Incinération',
            'depot_urne' => 'Dépôt d\'urne',
            'autre' => 'Autre'
        ];
        return $labels[$type] ?? $type;
    }

    // ========================================== //
    // GET TYPE BADGE CLASS                       //
    // ========================================== //
    public function get_type_badge($type) {
        $badges = [
            'enterrement' => 'enterrement',
            'incineration' => 'incineration',
            'depot_urne' => 'depot_urne',
            'autre' => 'autre'
        ];
        return $badges[$type] ?? 'autre';
    }

    // ========================================== //
    // RECHERCHER DES FUNÉRAILLES                 //
    // ========================================== //
    public function search($keyword) {
        $this->db->select('f.*');
        $this->db->from('funerailles f');
        $this->db->where('f.deleted', 0);
        $this->db->group_start();
        $this->db->like('f.code_funerailles', $keyword);
        $this->db->or_like('f.defunt_nom', $keyword);
        $this->db->or_like('f.defunt_prenom', $keyword);
        $this->db->or_like('f.pasteur_officiant', $keyword);
        $this->db->or_like('f.lieu_funerailles', $keyword);
        $this->db->group_end();
        $this->db->order_by('f.date_funerailles', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // UPLOAD DE LA PHOTO                         //
    // ========================================== //
    public function upload_photo($id, $file) {
        if (!is_dir($this->upload_path)) {
            mkdir($this->upload_path, 0777, true);
        }

        $fileInfo = pathinfo($file["name"]);
        $extension = strtolower($fileInfo['extension']);
        $filename = 'defunt_' . $id . '_' . time() . '.' . $extension;

        if (move_uploaded_file($file["tmp_name"], $this->upload_path . $filename)) {
            $this->update($id, array('defunt_photo' => $filename));
            return true;
        }
        return false;
    }
}