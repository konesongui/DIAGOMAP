<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class General_call_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_session_name = $this->setting_model->getCurrentSessionName();
        $this->start_month = $this->setting_model->getStartMonth();
    }

    // ========================================== //
    // MÉTHODE PRIVÉE POUR APPLIQUER LE FILTRE    //
    // ========================================== //
    private function _apply_entreprise_filter($table = null) {
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (empty($entreprise_id)) {
            return;
        }

        $table_prefix = $table ? $table . '.' : '';
        $table_name = $table ? $table : 'general_calls';
        $columns = $this->db->list_fields($table_name);
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        if ($hasEntrepriseId) {
            $this->db->where($table_prefix . 'entreprise_id', $entreprise_id);
        }
    }

    // ========================================== //
    // VÉRIFIER SI LA TABLE A LA COLONNE          //
    // ========================================== //
    private function _has_entreprise_column($table) {
        if (empty($table)) {
            return false;
        }
        $columns = $this->db->list_fields($table);
        return in_array('entreprise_id', $columns);
    }

    // ========================================== //
    // AJOUTER UN APPEL                           //
    // ========================================== //
    function add($data) {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Ajout de l'entreprise_id si non fourni
        if (!isset($data['entreprise_id']) || empty($data['entreprise_id'])) {
            $entreprise_id = $this->session->userdata('entreprise_id');
            if ($this->_has_entreprise_column('general_calls') && !empty($entreprise_id)) {
                $data['entreprise_id'] = $entreprise_id;
            }
        }

        $this->db->insert('general_calls', $data);
        $id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On Phone Call Log id " . $id;
        $action = "Insert";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $id;
        }
    }

    // ========================================== //
    // LISTE DES APPELS                           //
    // ========================================== //
    public function call_list($id = null) {
        $this->db->select('*')->from('general_calls');

        // Appliquer le filtre entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('general_calls')) {
            $this->db->where('general_calls.entreprise_id', $entreprise_id);
        }

        if ($id != null) {
            $this->db->where('general_calls.id', $id);
            $query = $this->db->get();
            if ($query->num_rows() == 1) {
                return $query->row_array();
            }
            return null;
        } else {
            $this->db->order_by('general_calls.id', 'DESC');
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    // ========================================== //
    // DATATABLE AJAX                             //
    // ========================================== //
    public function getcalllist($id = null) {
        // Récupérer l'entreprise_id depuis la session
        $entreprise_id = $this->session->userdata('entreprise_id');

        if ($id != null) {
            $this->datatables->where('general_calls.id', $id);
        }

        // Appliquer le filtre entreprise sur la requête DataTables
        if (!empty($entreprise_id) && $this->_has_entreprise_column('general_calls')) {
            $this->datatables->where('general_calls.entreprise_id', $entreprise_id);
        }

        $this->datatables
            ->select('general_calls.id, general_calls.name, general_calls.contact, general_calls.call_type, general_calls.note, general_calls.description, general_calls.follow_up_date, general_calls.date, general_calls.call_dureation')
            ->searchable('general_calls.name, general_calls.contact, general_calls.date, general_calls.follow_up_date, general_calls.call_type')
            ->orderable('general_calls.name, general_calls.contact, general_calls.date, general_calls.follow_up_date, general_calls.call_type')
            ->from('general_calls');
        return $this->datatables->generate('json');
    }

    // ========================================== //
    // RÉCUPÉRER UN APPEL PAR ID (AJAX)           //
    // ========================================== //
    public function get_call_by_id($id) {
        $this->db->select('*');
        $this->db->from('general_calls');
        $this->db->where('id', $id);

        // Appliquer le filtre entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('general_calls')) {
            $this->db->where('general_calls.entreprise_id', $entreprise_id);
        }

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        }
        return null;
    }

    // ========================================== //
    // METTRE À JOUR UN APPEL                     //
    // ========================================== //
    public function call_update($id, $data) {
        // Vérifier que l'appel appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('general_calls')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('general_calls')->row_array();

        if (!$check) {
            return false; // Appel non trouvé ou accès non autorisé
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        $this->db->update('general_calls', $data);
        $message = UPDATE_RECORD_CONSTANT . " On Phone Call Log id " . $id;
        $action = "Update";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $this->db->affected_rows();
        }
    }

    // ========================================== //
    // SUPPRIMER UN APPEL                         //
    // ========================================== //
    function delete($id) {
        // Vérifier que l'appel appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('general_calls')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('general_calls')->row_array();

        if (!$check) {
            return false; // Appel non trouvé ou accès non autorisé
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        $this->db->delete('general_calls');
        $message = DELETE_RECORD_CONSTANT . " On Phone Call Log id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    // ========================================== //
    // RÉCUPÉRER LES APPELS FILTRÉS               //
    // ========================================== //
    public function get_filtered_calls($call_type = null, $date_from = null, $date_to = null) {
        $this->db->select('*');
        $this->db->from('general_calls');

        // Appliquer le filtre entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('general_calls')) {
            $this->db->where('general_calls.entreprise_id', $entreprise_id);
        }

        if (!empty($call_type)) {
            $this->db->where('call_type', $call_type);
        }

        if (!empty($date_from)) {
            $this->db->where('date >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('date <=', $date_to);
        }

        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // COMPTER LES APPELS PAR TYPE                //
    // ========================================== //
    public function count_by_type($call_type = null) {
        $this->db->select('COUNT(*) as total');
        $this->db->from('general_calls');

        // Appliquer le filtre entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('general_calls')) {
            $this->db->where('general_calls.entreprise_id', $entreprise_id);
        }

        if ($call_type !== null) {
            $this->db->where('call_type', $call_type);
        }
        $query = $this->db->get();
        return $query->row()->total;
    }

    // ========================================== //
    // COMPTER LES APPELS DU JOUR                 //
    // ========================================== //
    public function count_today() {
        $today = date('Y-m-d');
        $this->db->select('COUNT(*) as total');
        $this->db->from('general_calls');

        // Appliquer le filtre entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('general_calls')) {
            $this->db->where('general_calls.entreprise_id', $entreprise_id);
        }

        $this->db->where('date', $today);
        $query = $this->db->get();
        return $query->row()->total;
    }

    // ========================================== //
    // COMPTER LE TOTAL DES APPELS                //
    // ========================================== //
    public function count_total() {
        $this->db->select('COUNT(*) as total');
        $this->db->from('general_calls');

        // Appliquer le filtre entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('general_calls')) {
            $this->db->where('general_calls.entreprise_id', $entreprise_id);
        }

        $query = $this->db->get();
        return $query->row()->total;
    }

    // ========================================== //
    // STATISTIQUES DES APPELS                    //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total des appels
        $stats['total'] = $this->count_total();

        // Appels entrants (type 1)
        $stats['incoming'] = $this->count_by_type(1);

        // Appels sortants (type 2)
        $stats['outgoing'] = $this->count_by_type(2);

        // Appels manqués (type 3)
        $stats['missed'] = $this->count_by_type(3);

        // Appels du jour
        $stats['today'] = $this->count_today();

        return $stats;
    }

    // ========================================== //
    // GET CALL TYPE LABEL                        //
    // ========================================== //
    public function get_call_type_label($call_type) {
        $labels = array(
            1 => 'Entrant',
            2 => 'Sortant',
            3 => 'Manqué'
        );
        return $labels[$call_type] ?? 'Inconnu';
    }

    // ========================================== //
    // GET CALL TYPE BADGE CLASS                  //
    // ========================================== //
    public function get_call_type_badge($call_type) {
        $badges = array(
            1 => 'incoming',
            2 => 'outgoing',
            3 => 'missed'
        );
        return $badges[$call_type] ?? 'other';
    }

    // ========================================== //
    // RECHERCHE D'APPELS                         //
    // ========================================== //
    public function search_calls($keyword) {
        $this->db->select('*');
        $this->db->from('general_calls');

        // Appliquer le filtre entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('general_calls')) {
            $this->db->where('general_calls.entreprise_id', $entreprise_id);
        }

        $this->db->group_start();
        $this->db->like('name', $keyword);
        $this->db->or_like('contact', $keyword);
        $this->db->or_like('description', $keyword);
        $this->db->or_like('note', $keyword);
        $this->db->group_end();
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // GET APPELS PAR DATE                        //
    // ========================================== //
    public function get_calls_by_date($date) {
        $this->db->select('*');
        $this->db->from('general_calls');

        // Appliquer le filtre entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('general_calls')) {
            $this->db->where('general_calls.entreprise_id', $entreprise_id);
        }

        $this->db->where('date', $date);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // GET APPELS PAR PÉRIODE                     //
    // ========================================== //
    public function get_calls_by_period($start_date, $end_date) {
        $this->db->select('*');
        $this->db->from('general_calls');

        // Appliquer le filtre entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('general_calls')) {
            $this->db->where('general_calls.entreprise_id', $entreprise_id);
        }

        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // GET DERNIERS APPELS                        //
    // ========================================== //
    public function get_recent_calls($limit = 10) {
        $this->db->select('*');
        $this->db->from('general_calls');

        // Appliquer le filtre entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('general_calls')) {
            $this->db->where('general_calls.entreprise_id', $entreprise_id);
        }

        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // SUPPRESSION MULTIPLE                       //
    // ========================================== //
    public function delete_multiple($ids) {
        if (empty($ids)) {
            return false;
        }

        // Vérifier que tous les appels appartiennent à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('general_calls')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where_in('id', $ids);
        $check = $this->db->get('general_calls')->result_array();

        if (count($check) != count($ids)) {
            return false; // Certains appels ne sont pas accessibles
        }

        $this->db->trans_start();
        $this->db->where_in('id', $ids);
        $this->db->delete('general_calls');
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return false;
        }
        return true;
    }

    // ========================================== //
    // EXPORT DES APPELS (retour tableau)         //
    // ========================================== //
    public function export_calls($call_type = null, $date_from = null, $date_to = null) {
        return $this->get_filtered_calls($call_type, $date_from, $date_to);
    }
}