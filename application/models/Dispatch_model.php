<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dispatch_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_session_name = $this->setting_model->getCurrentSessionName();
        $this->start_month = $this->setting_model->getStartMonth();
    }

    public function insert($table, $data) {

        if ($data['type'] == 'dispatch') {
            $title = "Postal Dispatch";
        } else {
            $title = "Postal Receive";
        }
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert($table, $data);

        $return_value = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On  Admission Enquiry  " . $title . " id " . $return_value;
        $action = "Insert";
        $record_id = $return_value;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $return_value;
        }
    }

    public function image_add($type, $dispatch_id, $image) {
        $array = array('id' => $dispatch_id, 'type' => $type);
        $this->db->set('image', $image);
        $this->db->where($array);
        $this->db->update('dispatch_receive');
    }

    public function dispatch_list() {
        $this->db->select('*');
        $this->db->where('type', 'dispatch');
        $this->db->from('dispatch_receive');
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->result();
    }

    public function receive_list() {
        $this->db->select('*');
        $this->db->where('type', 'receive');
        $this->db->order_by('id', 'desc');
        $this->db->from('dispatch_receive');
        $query = $this->db->get();
        return $query->result();
    }

    public function dis_rec_data($id, $type) {
        $this->db->select('*');
        $this->db->where('id', $id);
        $this->db->where('type', $type);
        $this->db->from('dispatch_receive');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function update_dispatch($table, $id, $type, $data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if ($data['type'] == 'dispatch') {
            $title = "Postal Dispatch";
        } else {
            $title = "Postal Receive";
        }
        $this->db->where('id', $id);
        $this->db->where('type', $type);
        $this->db->update($table, $data);
        $message = UPDATE_RECORD_CONSTANT . " On Admission Enquiry $title  id " . $id;
        $action = "Update";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }

    // ========================================== //
// STATISTIQUES DES DISPATCH                   //
// ========================================== //
    public function get_stats($type = 'dispatch') {
        $stats = array();

        // Total
        $this->db->select('COUNT(*) as total');
        $this->db->from('dispatch_receive');
        $this->db->where('type', $type);
        $query = $this->db->get();
        $stats['total'] = (int)$query->row()->total;

        // Aujourd'hui
        $today = date('Y-m-d');
        $this->db->select('COUNT(*) as total');
        $this->db->from('dispatch_receive');
        $this->db->where('type', $type);
        $this->db->where('date', $today);
        $query = $this->db->get();
        $stats['today'] = (int)$query->row()->total;

        // En attente (exemple - vous pouvez adapter selon votre logique)
        $stats['pending'] = 0;
        $stats['processed'] = 0;

        return $stats;
    }

// ========================================== //
// RÉCUPÉRER LES DISPATCH FILTRÉS             //
// ========================================== //
    public function get_filtered($type = 'dispatch', $from = null, $date_from = null, $date_to = null) {
        $this->db->select('*');
        $this->db->from('dispatch_receive');
        $this->db->where('type', $type);

        if (!empty($from)) {
            $this->db->like('from_title', $from);
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
// RÉCUPÉRER L'IMAGE D'UN DISPATCH            //
// ========================================== //
    public function get_image($type, $id) {
        $this->db->select('image');
        $this->db->from('dispatch_receive');
        $this->db->where('id', $id);
        $this->db->where('type', $type);
        $query = $this->db->get();
        $row = $query->row();
        return $row->image ?? null;
    }

    public function image_update($type, $id, $img_name) {
        $this->db->set('image', $img_name);
        $this->db->where('id', $id);
        $this->db->where('type', $type);
        $this->db->update('dispatch_receive');
    }

    public function image_delete($id, $img_name) {
        $file = "./uploads/front_office/dispatch_receive/" . $img_name;
        unlink($file);
        $this->db->where('id', $id);
        $this->db->delete('dispatch_receive');
        $controller_name = $this->uri->segment(2);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/' . $controller_name);
    }

    public function delete($id) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('dispatch_receive');
        $message = DELETE_RECORD_CONSTANT . " On Postal Dispatch id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
        $controller_name = $this->uri->segment(2);
        $this->session->set_flashdata('msg', '<div class="alert alert-success"> ' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/' . $controller_name);
    }

}
