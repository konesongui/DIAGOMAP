<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Follow extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("enquiry_model");
        $this->config->load("payroll");
        $this->projects_status = $this->config->item('projects_status');
    }

    public function index() {

        if (!$this->rbac->hasPrivilege('projet', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Service');
        $this->session->set_userdata('sub_menu', 'admin/follow_projects');
        $data['class_list'] = $this->class_model->get();
        $data["source_select"] = "";
        $data["status"] = "active";
		$data['stff_list'] = $this->staff_model->get();       
             $this->form_validation->set_rules('from_date', $this->lang->line('enquiry')." ".$this->lang->line('from')." ".$this->lang->line('date'), 'trim|required|xss_clean');
              $this->form_validation->set_rules('to_date', $this->lang->line('enquiry')." ".$this->lang->line('to')." ".$this->lang->line('date'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == TRUE) {
          
            $source = $this->input->post("source");
            $status = $this->input->post("status");
            $date_from = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post("from_date")));
            $date_to = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post("to_date"))); 
            $data["source_select"] = $source;
            $data["status"] = $status;           
            $follow_list = $this->enquiry_model->searchProjects($source, $date_from, $date_to, $status);
        } else {
            $follow_list = $this->enquiry_model->getfollow_list();
        }
        foreach ($projects_list as $key => $value) {
            $projects_up = $this->enquiry_model->getFollowByProjects($value["id"]);
            $projects_list[$key]["followupdate"] = isset($projects_up["date"])?$projects_up["date"]:'';
            $projects_list[$key]["next_date"] = isset($projects_up["next_date"])?$projects_up["next_date"]:'';
            $projects_list[$key]["response"] = isset($projects_up["response"])?$projects_up["response"]:'';
            $projects_list[$key]["note"] = isset($projects_up["note"])?$projects_up["note"]:'';
            $projects_list[$key]["followup_by"] = isset($projects_up["followup_by"])?$projects_up["followup_by"]:'';
        }
        $data['follow_list'] = $follow_list;
        $data['projects_status'] = $this->projects_status;
        $data['Reference'] = $this->enquiry_model->get_reference();
        $data['sourcelist'] = $this->enquiry_model->getComplaintSource();
        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/follow_projectsview', $data);
        $this->load->view('layout/footer');
    }

    public function add() {
        if (!$this->rbac->hasPrivilege('projet', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|xss_clean');
        $this->form_validation->set_rules('code', $this->lang->line('code'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('source', $this->lang->line('source'), 'trim|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $msg = array(
                'name' => form_error('name'),
                'contact' => form_error('contact'),
                'source' => form_error('source'),
                'date' => form_error('date'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $projects = array(
                'name' => $this->input->post('name'),
                'projet' => $this->input->post('projet'),
                'start_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('start_date'))),
                'end_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('end_date'))),
                'code' => $this->input->post('code'),
                'objet' => $this->input->post('objet'),
                'contact' => $this->input->post('contact'),
                'address' => $this->input->post('address'),
                'reference' => $this->input->post('reference'),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'description' => $this->input->post('description'),
                'follow_up_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('follow_up_date'))),
                'note' => $this->input->post('note'),
                'source' => $this->input->post('source'),
                'email' => $this->input->post('email'),
                'assigned' => $this->input->post('assigned'),
                'class' => $this->input->post('class'),
                'no_of_child' => $this->input->post('no_of_child'),
                'status' => 'pending'
            );
            $this->enquiry_model->adds($projects);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function delete($id) {
        if (!$this->rbac->hasPrivilege('projet', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->enquiry_model->projects_delete($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        }
        echo json_encode($array);
    }

    public function projects_up($projects_id, $status) {

        if (!$this->rbac->hasPrivilege('follow_up_admission_enquiry', 'can_view')) {
            access_denied();
        }
        $data['id'] = $projects_id;
        $data['projects_data'] = $this->enquiry_model->getprojects_list($projects_id, $status);
        $data['next_date'] = $this->enquiry_model->next_projects_up_date($projects_id);
        $data['projects_status'] = $this->projects_status;
        $this->load->view('admin/frontoffice/projects_up_modal', $data);
    }

    function projects_up_insert() {
        if (!$this->rbac->hasPrivilege('follow_up_admission_enquiry', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('response', $this->lang->line('response'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('follow_up_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('follow_up_date', $this->lang->line('next_follow_up_date'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $msg = array(
                'response' => form_error('response'),
                'follow_up_date' => form_error('follow_up_date'),
                'date' => form_error('date'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $admin = $this->customlib->getLoggedInUserData();
            $projects_up = array(
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'next_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('follow_up_date'))),
                'response' => $this->input->post('response'),
                'note' => $this->input->post('note'),
                'followup_by' => $admin['username'],
                'enquiry_id' => $this->input->post('enquiry_id')
            );
            $this->enquiry_model->add_projects_up($projects_up);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    function projects_up_list($id) {
        $data['id'] = $id;
        $data['projects_list'] = $this->enquiry_model->getprojects_up_list($id);
        $this->load->view('admin/frontoffice/projectsuplist', $data);
    }



    function details($id, $status) {
        if (!$this->rbac->hasPrivilege('projet', 'can_view')) {
            access_denied();
        }
        $data['source'] = $this->enquiry_model->getComplaintSource();
        $data['enquiry_type'] = $this->enquiry_model->get_enquiry_type();
        $data['Reference'] = $this->enquiry_model->get_reference();
        $data['class_list'] = $this->enquiry_model->getclasses();
        $data['projects_data'] = $this->enquiry_model->getprojects_list($id, $status);
		$data['stff_list'] = $this->staff_model->get();
        $this->load->view('admin/frontoffice/projectseditmodalview', $data);
    }

    function editpost($id) {
        if (!$this->rbac->hasPrivilege('projet', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('projet', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('contact', $this->lang->line('contact'), 'trim|xss_clean');
        $this->form_validation->set_rules('objet', $this->lang->line('source'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $msg = array(
                'projet' => form_error('projet'),
                'contact' => form_error('contact'),
                'objet' => form_error('objet'),
                'date' => form_error('date'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $projects_update = array(
                'projet' => $this->input->post('projet'),
                'objet' => $this->input->post('objet'),
                'start_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('start_date'))),
                'end_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('end_date'))),
                'address' => $this->input->post('address'),
                'reference' => $this->input->post('reference'),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'description' => $this->input->post('description'),
                'follow_up_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('follow_up_date'))),
                'note' => $this->input->post('note'),
                'source' => $this->input->post('source'),
                'email' => $this->input->post('email'),
                'assigned' => $this->input->post('assigned'),
                'class' => $this->input->post('class'),
                'no_of_child' => $this->input->post('no_of_child')
            );
            $this->enquiry_model->projects_update($id, $projects_update);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function projects_up_delete($follow_up_id, $enquiry_id) {
        if (!$this->rbac->hasPrivilege('follow_up_admission_enquiry', 'can_delete')) {
            access_denied();
        }
        $this->enquiry_model->delete_projects_up($follow_up_id);
        $data['id'] = $enquiry_id;
        $data['projects_up_list'] = $this->enquiry_model->getprojects_up_list($enquiry_id);
        $this->load->view('admin/frontoffice/projectsuplist', $data);
    }

    public function check_default($post_string) {
        return $post_string == '' ? FALSE : TRUE;
    }

    public function change_status() {
        $id = $this->input->post("id");
        $status = $this->input->post("status");
        if (!empty($id)) {
            $data = array('id' => $id, 'status' => $status);
            $this->enquiry_model->changeprojectsStatus($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => $this->lang->line('update_message'));
        }

        echo json_encode($array);
    }

}
