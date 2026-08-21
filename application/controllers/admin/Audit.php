<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
class Audit extends Admin_Controller {

    public function __construct() {
        parent::__construct();
    }
 
    public function unauthorized() {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function index($offset = 0) {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'audit/index');
        $data['title'] = 'Audit Trail Report';
        $data['title_list'] = 'Audit Trail List';       
        $this->load->view('layout/header');
        $this->load->view('admin/audit/index');
        $this->load->view('layout/footer');
    }

    public function getDatatable() {
        $audit = $this->audit_model->getAllRecord();
        $audit = json_decode($audit);

        $dt_data = array();
        if (!empty($audit->data)) {
            foreach ($audit->data as $key => $value) {

                $date = date($this->customlib->getSchoolDateFormat(), strtotime($value->time));
                $time = date('H:i:s', strtotime($value->time));

                $row = array();
                $row[] = $value->message;
                $row[] = $value->name;
                $row[] = $value->ip_address;
                $row[] = $value->action;
                $row[] = $value->platform;
                $row[] = $value->agent;
                $row[] = $date . " " . $time;


                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw" => intval($audit->draw),
            "recordsTotal" => intval($audit->recordsTotal),
            "recordsFiltered" => intval($audit->recordsFiltered),
            "data" => $dt_data,
        );
        echo json_encode($json_data);
    }
	
	public function delete() {       
		$this->audit_model->audittrail_delete();
        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));        
        echo json_encode($array);
    }


    public function getAllRecord() {
        $this->load->library('datatables');

        $this->datatables
            ->select('audit_trail.id, audit_trail.message, audit_trail.action, audit_trail.ip_address, audit_trail.platform, audit_trail.agent, audit_trail.time, users.name')
            ->join('users', 'users.id = audit_trail.user_id', 'left')
            ->searchable('audit_trail.message, audit_trail.action, audit_trail.ip_address, users.name')
            ->orderable('audit_trail.message, audit_trail.action, audit_trail.ip_address, users.name, audit_trail.time')
            ->from('audit_trail');

        return $this->datatables->generate('json');
    }

    // ========================================== //
    // RÉCUPÉRER LES LOGS FILTRÉS POUR DATATABLE //
    // ========================================== //
    public function getFilteredDatatable($action = null, $date_from = null, $date_to = null) {
        $this->load->library('datatables');

        $this->datatables
            ->select('audit_trail.id, audit_trail.message, audit_trail.action, audit_trail.ip_address, audit_trail.platform, audit_trail.agent, audit_trail.time, users.name')
            ->join('users', 'users.id = audit_trail.user_id', 'left')
            ->searchable('audit_trail.message, audit_trail.action, audit_trail.ip_address, users.name')
            ->orderable('audit_trail.message, audit_trail.action, audit_trail.ip_address, users.name, audit_trail.time')
            ->from('audit_trail');

        // Appliquer les filtres
        if (!empty($action)) {
            $this->datatables->like('audit_trail.action', $action);
        }

        if (!empty($date_from)) {
            $this->datatables->where('audit_trail.time >=', $date_from . ' 00:00:00');
        }

        if (!empty($date_to)) {
            $this->datatables->where('audit_trail.time <=', $date_to . ' 23:59:59');
        }

        return $this->datatables->generate('json');
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES FILTRÉES             //
    // ========================================== //
    public function get_filtered_data($action = null, $date_from = null, $date_to = null) {
        $this->db->select('audit_trail.*, users.name');
        $this->db->from('audit_trail');
        $this->db->join('users', 'users.id = audit_trail.user_id', 'left');

        if (!empty($action)) {
            $this->db->like('audit_trail.action', $action);
        }

        if (!empty($date_from)) {
            $this->db->where('audit_trail.time >=', $date_from . ' 00:00:00');
        }

        if (!empty($date_to)) {
            $this->db->where('audit_trail.time <=', $date_to . ' 23:59:59');
        }

        $this->db->order_by('audit_trail.time', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // STATISTIQUES DES LOGS                      //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total des logs
        $this->db->select('COUNT(*) as total');
        $this->db->from('audit_trail');
        $query = $this->db->get();
        $stats['total'] = (int)$query->row()->total;

        // Logs d'aujourd'hui
        $today = date('Y-m-d');
        $this->db->select('COUNT(*) as total');
        $this->db->from('audit_trail');
        $this->db->where('DATE(time)', $today);
        $query = $this->db->get();
        $stats['today'] = (int)$query->row()->total;

        // Par type d'action
        $actions = ['insert', 'update', 'delete', 'login', 'logout'];
        foreach ($actions as $action) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('audit_trail');
            $this->db->like('action', $action);
            $query = $this->db->get();
            $stats[$action] = (int)$query->row()->total;
        }

        return $stats;
    }

    // ========================================== //
    // COMPTER TOUS LES LOGS                      //
    // ========================================== //
    public function count_all() {
        $this->db->select('COUNT(*) as total');
        $this->db->from('audit_trail');
        $query = $this->db->get();
        return (int)$query->row()->total;
    }

    // ========================================== //
    // VIDER LE JOURNAL D'AUDIT                   //
    // ========================================== //
    public function audittrail_delete() {
        $this->db->truncate('audit_trail');
        return $this->db->affected_rows();
    }

}
