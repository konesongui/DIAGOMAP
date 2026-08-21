<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Couriers_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ========================================== //
    // RÉCUPÉRER TOUS LES COURRIERS               //
    // ========================================== //
    public function get_all($id = null) {
        $this->db->select('*');
        $this->db->from('couriers');
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
    // RÉCUPÉRER UN COURRIER PAR ID               //
    // ========================================== //
    public function get_by_id($id) {
        $this->db->select('*');
        $this->db->from('couriers');
        $this->db->where('id', $id);
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    // ========================================== //
    // AJOUTER UN COURRIER                        //
    // ========================================== //
    public function add($data) {
        $this->db->insert('couriers', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UN COURRIER                  //
    // ========================================== //
    public function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('couriers', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UN COURRIER (SOFT DELETE)        //
    // ========================================== //
    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->update('couriers', array('deleted' => 1));
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER DÉFINITIVEMENT UN COURRIER       //
    // ========================================== //
    public function delete_permanent($id) {
        // Récupérer le fichier attaché avant la suppression
        $courier = $this->get_by_id($id);
        if ($courier && !empty($courier['attachment'])) {
            $filepath = "./uploads/front_office/couriers/" . $courier['attachment'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }

        $this->db->where('id', $id);
        $this->db->delete('couriers');
        return $this->db->affected_rows();
    }

    // ========================================== //
    // RÉCUPÉRER LE FICHIER ATTACHÉ               //
    // ========================================== //
    public function get_attachment($id) {
        $this->db->select('attachment');
        $this->db->from('couriers');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $row = $query->row();
        return $row->attachment ?? null;
    }

    // ========================================== //
    // METTRE À JOUR LE FICHIER ATTACHÉ           //
    // ========================================== //
    public function update_attachment($id, $filename) {
        $this->db->where('id', $id);
        $this->db->update('couriers', array('attachment' => $filename));
        return $this->db->affected_rows();
    }

    // ========================================== //
    // STATISTIQUES DES COURRIERS                 //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total
        $this->db->select('COUNT(*) as total');
        $this->db->from('couriers');
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $stats['total'] = (int)$query->row()->total;

        // Reçus
        $this->db->select('COUNT(*) as total');
        $this->db->from('couriers');
        $this->db->where('deleted', 0);
        $this->db->group_start();
        $this->db->like('courier_type', 'reçu');
        $this->db->or_like('courier_type', 'incoming');
        $this->db->group_end();
        $query = $this->db->get();
        $stats['incoming'] = (int)$query->row()->total;

        // Envoyés
        $this->db->select('COUNT(*) as total');
        $this->db->from('couriers');
        $this->db->where('deleted', 0);
        $this->db->group_start();
        $this->db->like('courier_type', 'envoi');
        $this->db->or_like('courier_type', 'outgoing');
        $this->db->group_end();
        $query = $this->db->get();
        $stats['outgoing'] = (int)$query->row()->total;

        // Internes
        $this->db->select('COUNT(*) as total');
        $this->db->from('couriers');
        $this->db->where('deleted', 0);
        $this->db->group_start();
        $this->db->like('courier_type', 'interne');
        $this->db->or_like('courier_type', 'internal');
        $this->db->group_end();
        $query = $this->db->get();
        $stats['internal'] = (int)$query->row()->total;

        // Aujourd'hui
        $today = date('Y-m-d');
        $this->db->select('COUNT(*) as total');
        $this->db->from('couriers');
        $this->db->where('deleted', 0);
        $this->db->where('date_received', $today);
        $query = $this->db->get();
        $stats['today'] = (int)$query->row()->total;

        return $stats;
    }

    // ========================================== //
    // STATISTIQUES PAR STATUT                    //
    // ========================================== //
    public function get_stats_by_status() {
        $stats = array();

        $statuses = ['pending', 'processed', 'archived'];
        foreach ($statuses as $status) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('couriers');
            $this->db->where('deleted', 0);
            $this->db->where('status', $status);
            $query = $this->db->get();
            $stats[$status] = (int)$query->row()->total;
        }

        return $stats;
    }

    // ========================================== //
    // RÉCUPÉRER LES TYPES DE COURRIERS           //
    // ========================================== //
    public function get_courier_types() {
        $this->db->distinct();
        $this->db->select('courier_type');
        $this->db->from('couriers');
        $this->db->where('deleted', 0);
        $this->db->where('courier_type !=', '');
        $this->db->order_by('courier_type', 'ASC');
        $query = $this->db->get();
        $result = $query->result_array();
        return array_column($result, 'courier_type');
    }

    // ========================================== //
    // RÉCUPÉRER LES STATUTS                      //
    // ========================================== //
    public function get_statuses() {
        return [
            'pending' => 'En attente',
            'processed' => 'Traité',
            'archived' => 'Archivé'
        ];
    }

    // ========================================== //
    // RÉCUPÉRER LES COURRIERS FILTRÉS            //
    // ========================================== //
    public function get_filtered($courier_type = null, $date_from = null, $date_to = null, $status = null) {
        $this->db->select('*');
        $this->db->from('couriers');
        $this->db->where('deleted', 0);

        if (!empty($courier_type)) {
            $this->db->like('courier_type', $courier_type);
        }

        if (!empty($date_from)) {
            $this->db->where('date_received >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('date_received <=', $date_to);
        }

        if (!empty($status)) {
            $this->db->where('status', $status);
        }

        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RECHERCHER DES COURRIERS                   //
    // ========================================== //
    public function search($keyword) {
        $this->db->select('*');
        $this->db->from('couriers');
        $this->db->where('deleted', 0);
        $this->db->group_start();
        $this->db->like('courier_type', $keyword);
        $this->db->or_like('sender_name', $keyword);
        $this->db->or_like('reference', $keyword);
        $this->db->or_like('address', $keyword);
        $this->db->or_like('description', $keyword);
        $this->db->or_like('note', $keyword);
        $this->db->group_end();
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES COURRIERS PAR DATE           //
    // ========================================== //
    public function get_by_date($date) {
        $this->db->select('*');
        $this->db->from('couriers');
        $this->db->where('deleted', 0);
        $this->db->where('date_received', $date);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES COURRIERS PAR PÉRIODE        //
    // ========================================== //
    public function get_by_period($start_date, $end_date) {
        $this->db->select('*');
        $this->db->from('couriers');
        $this->db->where('deleted', 0);
        $this->db->where('date_received >=', $start_date);
        $this->db->where('date_received <=', $end_date);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES DERNIERS COURRIERS           //
    // ========================================== //
    public function get_recent($limit = 10) {
        $this->db->select('*');
        $this->db->from('couriers');
        $this->db->where('deleted', 0);
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // COMPTER LES COURRIERS PAR TYPE             //
    // ========================================== //
    public function count_by_type($courier_type) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('couriers');
        $this->db->where('deleted', 0);
        $this->db->like('courier_type', $courier_type);
        $query = $this->db->get();
        return (int)$query->row()->total;
    }

    // ========================================== //
    // COMPTER LES COURRIERS PAR STATUT           //
    // ========================================== //
    public function count_by_status($status) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('couriers');
        $this->db->where('deleted', 0);
        $this->db->where('status', $status);
        $query = $this->db->get();
        return (int)$query->row()->total;
    }

    // ========================================== //
    // COMPTER LES COURRIERS DU JOUR              //
    // ========================================== //
    public function count_today() {
        $today = date('Y-m-d');
        $this->db->select('COUNT(*) as total');
        $this->db->from('couriers');
        $this->db->where('deleted', 0);
        $this->db->where('date_received', $today);
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

        // Récupérer les fichiers attachés
        $this->db->select('attachment');
        $this->db->from('couriers');
        $this->db->where_in('id', $ids);
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        $files = $query->result_array();

        // Supprimer les fichiers physiques
        foreach ($files as $file) {
            if (!empty($file['attachment'])) {
                $filepath = "./uploads/front_office/couriers/" . $file['attachment'];
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
            }
        }

        // Soft delete
        $this->db->where_in('id', $ids);
        $this->db->update('couriers', array('deleted' => 1));
        return $this->db->affected_rows();
    }

    // ========================================== //
    // DATATABLE AJAX (Server-side)               //
    // ========================================== //
    public function get_datatable() {
        $this->load->library('datatables');

        $this->datatables
            ->select('couriers.id, couriers.courier_type, couriers.sender_name, couriers.reference, couriers.date_received, couriers.address, couriers.status, couriers.attachment')
            ->searchable('couriers.courier_type, couriers.sender_name, couriers.reference, couriers.date_received, couriers.address')
            ->orderable('couriers.courier_type, couriers.sender_name, couriers.date_received, couriers.status')
            ->where('couriers.deleted', 0)
            ->from('couriers');

        return $this->datatables->generate('json');
    }

    // ========================================== //
    // EXPORT DES DONNÉES                         //
    // ========================================== //
    public function export_data($courier_type = null, $date_from = null, $date_to = null, $status = null) {
        return $this->get_filtered($courier_type, $date_from, $date_to, $status);
    }

    // ========================================== //
    // GET COURIER TYPE LABEL                     //
    // ========================================== //
    public function get_type_label($courier_type) {
        $labels = [
            'Reçu' => 'Reçu',
            'Envoyé' => 'Envoyé',
            'Interne' => 'Interne'
        ];
        return $labels[$courier_type] ?? $courier_type;
    }

    // ========================================== //
    // GET STATUS LABEL                           //
    // ========================================== //
    public function get_status_label($status) {
        $labels = [
            'pending' => 'En attente',
            'processed' => 'Traité',
            'archived' => 'Archivé'
        ];
        return $labels[$status] ?? $status;
    }

    // ========================================== //
    // GET STATUS BADGE CLASS                     //
    // ========================================== //
    public function get_status_badge($status) {
        $badges = [
            'pending' => 'pending',
            'processed' => 'processed',
            'archived' => 'archived'
        ];
        return $badges[$status] ?? 'pending';
    }

    // ========================================== //
    // GET TYPE BADGE CLASS                       //
    // ========================================== //
    public function get_type_badge($courier_type) {
        $badges = [
            'Reçu' => 'incoming',
            'Envoyé' => 'outgoing',
            'Interne' => 'internal'
        ];
        $type_lower = strtolower($courier_type);
        if (strpos($type_lower, 'reçu') !== false || strpos($type_lower, 'incoming') !== false) return 'incoming';
        if (strpos($type_lower, 'envoi') !== false || strpos($type_lower, 'outgoing') !== false) return 'outgoing';
        if (strpos($type_lower, 'interne') !== false || strpos($type_lower, 'internal') !== false) return 'internal';
        return 'other';
    }
}