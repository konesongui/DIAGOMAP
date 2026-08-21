<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Training_request extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("enquiry_model");
        $this->load->model("staff_model");
        $this->config->load("payroll");
        $this->enquiry_status = $this->config->item('enquiry_status');
    }


    public function indexs()
    {
        if (!$this->rbac->hasPrivilege('training_enquiry', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/training_request');

        $data['class_list']     = $this->class_model->get();
        $data['source_select']  = "";
        $data['status']         = "active";
        $data['stff_list']      = $this->staff_model->get();

        $this->form_validation->set_rules('from_date', $this->lang->line('enquiry') . " " . $this->lang->line('from') . " " . $this->lang->line('date'), 'trim|xss_clean');
        $this->form_validation->set_rules('to_date', $this->lang->line('enquiry') . " " . $this->lang->line('to') . " " . $this->lang->line('date'), 'trim|xss_clean');

        $userdata = $this->customlib->getUserData();
        $userid   = $userdata['id'];
        $role_id  = $userdata['role_id']; // <-- ajout pour gestion des rôles

        if ($this->form_validation->run() == TRUE) {
            $source    = $this->input->post("source");
            $status    = $this->input->post("status");
            $date_from = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post("from_date")));
            $date_to   = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post("to_date")));

            $data["source_select"] = $source;
            $data["status"]        = $status;

            if ($this->rbac->hasPrivilege('training_enquiry', 'can_view_all') || $role_id == 7) {
                $trainingreq_list = $this->enquiry_model->searchTrainingreq($source, $date_from, $date_to, $status);
            } else {
                $trainingreq_list = $this->enquiry_model->searchTrainingreqByUser($userid, $source, $date_from, $date_to, $status);
            }

        } else {
            if ($this->rbac->hasPrivilege('training_enquiry', 'can_view_all') || $role_id == 7) {
                $trainingreq_list = $this->enquiry_model->getrequest_list();
            } else {
                $trainingreq_list = $this->enquiry_model->getrequest_list_by_user($userid);
            }
        }

        // Traitement des données
        $enquiry_list = array();
        foreach ($trainingreq_list as $key => $value) {
            $follow_up = $this->enquiry_model->getFollowByEnquiry($value["id"]);

            $enquiry_list[$key] = $value;
            $enquiry_list[$key]["created_by"]   = $value["created_by"];
            $enquiry_list[$key]["followupdate"] = isset($follow_up["date"]) ? $follow_up["date"] : '';
            $enquiry_list[$key]["next_date"]    = isset($follow_up["next_date"]) ? $follow_up["next_date"] : '';
            $enquiry_list[$key]["response"]     = isset($follow_up["response"]) ? $follow_up["response"] : '';
            $enquiry_list[$key]["note"]         = isset($follow_up["note"]) ? $follow_up["note"] : '';
            $enquiry_list[$key]["followup_by"]  = isset($follow_up["followup_by"]) ? $follow_up["followup_by"] : '';
        }

        $data['trainingreq_list'] = $enquiry_list;
        $data['enquiry_status']   = $this->enquiry_status;
        $data['Reference']        = $this->enquiry_model->get_reference();
        $data['sourcelist']       = $this->enquiry_model->getComplaintSource();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/training_requestview', $data);
        $this->load->view('layout/footer');
    }


    public function index() {
        if (!$this->rbac->hasPrivilege('training_enquiry', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/training_request');

        $data['class_list'] = $this->class_model->get();
        $data["source_select"] = "";
        $data["status"] = "active";
        $data['stff_list'] = $this->staff_model->get();

        $this->form_validation->set_rules('from_date', $this->lang->line('enquiry') . " " . $this->lang->line('from') . " " . $this->lang->line('date'), 'trim|xss_clean');
        $this->form_validation->set_rules('to_date', $this->lang->line('enquiry') . " " . $this->lang->line('to') . " " . $this->lang->line('date'), 'trim|xss_clean');

        $userdata = $this->customlib->getUserData();
        $userid = $userdata['id'];
        $role_id = $userdata['role_id'];
        $is_super_admin = ($role_id == 7); // ← adapte ici si nécessaire

        if ($this->form_validation->run() == TRUE) {
            $source = $this->input->post("source");
            $status = $this->input->post("status");
            $date_from = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post("from_date")));
            $date_to = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post("to_date")));
            $data["source_select"] = $source;
            $data["status"] = $status;

            $status_filter = ($status == 'all') ? null : $status;

            if ($is_super_admin) {
                $trainingreq_list = $this->enquiry_model->searchTrainingreq($source, $date_from, $date_to, $status_filter);
            } else {
                $trainingreq_list = $this->enquiry_model->searchTrainingreq($source, $date_from, $date_to, $status_filter, $userid);
            }

        } else {
            if ($is_super_admin) {
                $trainingreq_list = $this->enquiry_model->getrequest_list(); // 👈 super admin voit tout
            } else {
                $trainingreq_list = $this->enquiry_model->getrequest_list_by_user($userid);
            }
        }

        // Suivi
        $enquiry_list = array();
        foreach ($trainingreq_list as $key => $value) {
            $follow_up = $this->enquiry_model->getFollowByEnquiry($value["id"]);
            $enquiry_list[$key]["followupdate"] = isset($follow_up["date"]) ? $follow_up["date"] : '';
            $enquiry_list[$key]["next_date"] = isset($follow_up["next_date"]) ? $follow_up["next_date"] : '';
            $enquiry_list[$key]["response"] = isset($follow_up["response"]) ? $follow_up["response"] : '';
            $enquiry_list[$key]["note"] = isset($follow_up["note"]) ? $follow_up["note"] : '';
            $enquiry_list[$key]["followup_by"] = isset($follow_up["followup_by"]) ? $follow_up["followup_by"] : '';
        }

        $data['trainingreq_list'] = $trainingreq_list;
        $data['enquiry_status'] = $this->enquiry_status;
        $data['Reference'] = $this->enquiry_model->get_reference();
        $data['sourcelist'] = $this->enquiry_model->getComplaintSource();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/training_requestview', $data);
        $this->load->view('layout/footer');
    }



    public function indexe() {

        if (!$this->rbac->hasPrivilege('training_enquiry', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/training_request');
        $data['class_list'] = $this->class_model->get();
        $data["source_select"] = "";
        $data["status"] = "active";
		$data['stff_list'] = $this->staff_model->get();
             $this->form_validation->set_rules('from_date', $this->lang->line('enquiry')." ".$this->lang->line('from')." ".$this->lang->line('date'), 'trim|xss_clean');
              $this->form_validation->set_rules('to_date', $this->lang->line('enquiry')." ".$this->lang->line('to')." ".$this->lang->line('date'), 'trim|xss_clean');

        if ($this->form_validation->run() == TRUE) {
          
            $source = $this->input->post("source");
            $status = $this->input->post("status");
            $date_from = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post("from_date")));
            $date_to = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post("to_date"))); 
            $data["source_select"] = $source;
            $data["status"] = $status;
            $trainingreq_list = $this->enquiry_model->searchTrainingreq($source, $date_from, $date_to, $status);
        } else {
            $userdata      = $this->customlib->getUserData();

            $userid          = $userdata['id'];
            $trainingreq_list = $this->enquiry_model->getrequest_list();
        }
        foreach ($trainingreq_list as $key => $value) {
            $follow_up = $this->enquiry_model->getFollowByEnquiry($value["id"]);
            $enquiry_list[$key]["followupdate"] = isset($follow_up["date"])?$follow_up["date"]:'';
            $enquiry_list[$key]["next_date"] = isset($follow_up["next_date"])?$follow_up["next_date"]:'';
            $enquiry_list[$key]["response"] = isset($follow_up["response"])?$follow_up["response"]:'';
            $enquiry_list[$key]["note"] = isset($follow_up["note"])?$follow_up["note"]:'';
            $enquiry_list[$key]["followup_by"] = isset($follow_up["followup_by"])?$follow_up["followup_by"]:'';
        }
        $data['trainingreq_list'] = $trainingreq_list;
        $data['enquiry_status'] = $this->enquiry_status;
        $data['Reference'] = $this->enquiry_model->get_reference();
        $data['sourcelist'] = $this->enquiry_model->getComplaintSource();
        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/training_requestview', $data);
        $this->load->view('layout/footer');
    }

    public function add() {
        if (!$this->rbac->hasPrivilege('training_enquiry', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('training_name', $this->lang->line('Training'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('assigned', $this->lang->line('source'), 'trim|xss_clean');
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
            $userdata = $this->customlib->getUserData();
            $staff_id = $userdata['id']; // ou 'staff_id' selon ta structure

            $enquiry = array(
                'name' => $this->input->post('name'),
                'poste' => $this->input->post('poste'),
                'objectifs' => $this->input->post('objectifs'),
                'departement' => $this->input->post('departement'),
                'commentaires' => $this->input->post('commentaires'),
                'training_name' => $this->input->post('training_name'),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'assigned' => $this->input->post('assigned'),
                'created_by' => $staff_id, // 👈 staff connecté
                'status' => 'pending'
            );

            $this->enquiry_model->addtraining_request($enquiry);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }


    public function add_old() {
        if (!$this->rbac->hasPrivilege('training_enquiry', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('training_name', $this->lang->line('Training'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('assigned', $this->lang->line('source'), 'trim|xss_clean');
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
            $enquiry = array(
                'name' => $this->input->post('name'),
                'poste' => $this->input->post('poste'),
                 'objectifs' => $this->input->post('objectifs'),
                'departement' => $this->input->post('departement'),
                'commentaires' => $this->input->post('commentaires'),
                'training_name' => $this->input->post('training_name'),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'assigned' => $this->input->post('assigned'),
                'created_by' => $this->input->post('created_by'),

                'status' => 'pending'
            );
            $this->enquiry_model->addtraining_request($enquiry);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function delete($id) {
        if (!$this->rbac->hasPrivilege('training_enquiry', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->enquiry_model->enquiry_trainingreq_delete($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        }
        echo json_encode($array);
    }

    public function follow_up($training_id, $status) {

        if (!$this->rbac->hasPrivilege('training_follow', 'can_view')) {
            access_denied();
        }
        $data['id'] = $training_id;
        $data['training_data'] = $this->enquiry_model->getrequest_list($training_id, $status);
        $data['next_date'] = $this->enquiry_model->next_follow_up_date($training_id);
        $data['enquiry_status'] = $this->enquiry_status;
        $this->load->view('admin/frontoffice/trainingreq_up_modal', $data);
    }

    function trainingreq_up_insert() {
        if (!$this->rbac->hasPrivilege('training_follow', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('response', $this->lang->line('response'), 'trim|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('follow_up_date'), 'trim|xss_clean');
        $this->form_validation->set_rules('follow_up_date', $this->lang->line('next_follow_up_date'), 'trim|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            $msg = array(
                'response' => form_error('response'),
                'follow_up_date' => form_error('follow_up_date'),
                'date' => form_error('date'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $admin = $this->customlib->getLoggedInUserData();
            $follow_up = array(
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'next_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('follow_up_date'))),
                'response' => $this->input->post('response'),
                'note' => $this->input->post('note'),
                'followup_by' => $admin['username'],
                'trainingreq_id' => $this->input->post('trainingreq_id')
            );
            $this->enquiry_model->add_trainingreq_up($follow_up);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    function follow_up_list($id) {
        $data['id'] = $id;
        $data['follow_up_list'] = $this->enquiry_model->gettraining_up_list($id);
        $this->load->view('admin/frontoffice/trainingrequplist', $data);
    }

    function details($id, $status) {
        if (!$this->rbac->hasPrivilege('training_enquiry', 'can_view')) {
            access_denied();
        }
        $data['source'] = $this->enquiry_model->getComplaintSource();
        $data['enquiry_type'] = $this->enquiry_model->get_enquiry_type();
        $data['Reference'] = $this->enquiry_model->get_reference();
        $data['class_list'] = $this->enquiry_model->getclasses();
        $data['enquiry_data'] = $this->enquiry_model->getenquiry_list($id, $status);
		$data['stff_list'] = $this->staff_model->get();
        $this->load->view('admin/frontoffice/trainingreqeditmodalview', $data);
    }

    function detailstraining_request($id, $status) {
        if (!$this->rbac->hasPrivilege('training_enquiry', 'can_view')) {
            access_denied();
        }
        $data['source'] = $this->enquiry_model->getComplaintSource();
        $data['enquiry_type'] = $this->enquiry_model->get_enquiry_type();
        $data['Reference'] = $this->enquiry_model->get_reference();
        $data['class_list'] = $this->enquiry_model->getclasses();
        $data['training_data'] = $this->enquiry_model->getrequest_list($id, $status);
        $data['stff_list'] = $this->staff_model->get();
        $this->load->view('admin/frontoffice/trainingreqeditmodalview', $data);
    }



    function editpost($id) {
        if (!$this->rbac->hasPrivilege('training_enquiry', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|xss_clean');
        $this->form_validation->set_rules('contact', $this->lang->line('contact'), 'trim|xss_clean');
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
            $enquiry_update = array(
                'name' => $this->input->post('name'),
                'training_name' => $this->input->post('training_name'),
                'poste' => $this->input->post('poste'),
                'objectifs' => $this->input->post('objectifs'),
                'departement' => $this->input->post('reference'),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'commentaires' => $this->input->post('commentaires'),
                'follow_up_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('follow_up_date'))),
                'note' => $this->input->post('note'),
                'source' => $this->input->post('source'),
                'email' => $this->input->post('email'),
                'assigned' => $this->input->post('assigned'),
                'class' => $this->input->post('class'),
                'no_of_child' => $this->input->post('no_of_child')
            );
            $this->enquiry_model->enquiry_training_update($id, $enquiry_update);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function follow_up_delete($trainingreq_up_id, $trainingreq_id) {
        if (!$this->rbac->hasPrivilege('training_follow', 'can_delete')) {
            access_denied();
        }
        $this->enquiry_model->delete_trainingreq_follow_up($trainingreq_up_id);
        $data['id'] = $trainingreq_id;
        $data['follow_up_list'] = $this->enquiry_model->gettraining_up_list($trainingreq_id);
        $this->load->view('admin/frontoffice/trainingrequplist', $data);
    }

    public function check_default($post_string) {
        return $post_string == '' ? FALSE : TRUE;
    }

    public function change_status() {
        $id = $this->input->post("id");
        $status = $this->input->post("status");
        if (!empty($id)) {
            $data = array('id' => $id, 'status' => $status);
            $this->enquiry_model->changetrainingStatus($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => $this->lang->line('update_message'));
        }


        echo json_encode($array);

    }



}
