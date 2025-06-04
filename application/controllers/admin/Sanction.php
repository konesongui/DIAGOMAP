<?php

/**
 * 
 */
class Sanction extends Admin_Controller {

    function __construct() {

        parent::__construct();

        $this->load->helper('file');
        $this->config->load("payroll");

        $this->load->model('designation_model');
        $this->load->model('staff_model');
    }

    function sanction() {

        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/sanction/sanction');
        $designation = $this->designation_model->gets();
        $userdata = $this->customlib->getUserData();
        $staffRole = $this->staff_model->getStaffRole();
        $data["staffrole"] = $staffRole;
        $data["title"] = $this->lang->line('add') . " " . $this->lang->line('designation');
        $data["designation"] = $designation;

        $this->form_validation->set_rules(
                'type', $this->lang->line('name'), array('required',
            array('check_exists', array($this->designation_model, 'valid_designation'))
                )
        );
        if ($this->form_validation->run()) {

            $type = $this->input->post("type");
            $role = $this->input->post("role");
            $empname = $this->input->post("empname");
            $action= $this->input->post("action");
            $reason = $this->input->post("reason");
            $date = $this->input->post("date");

            $status = $this->input->post("status");
            if (empty($designationid)) {

                if (!$this->rbac->hasPrivilege('sanction', 'can_add')) {
                    access_denied();
                }
            } else {

                if (!$this->rbac->hasPrivilege('sanction', 'can_edit')) {
                    access_denied();
                }
            }

            if (!empty($designationid)) {
                $data = array(
                    'designation' => $type,
                    'role' => $role,
                    'empname' => $empname,
                    'action' => $action,
                    'reason' => $reason,
                    'date' => $date,
                    'is_active' => 'yes',
                    'id' => $designationid
                );
            } else {

                $data = array(
                    'designation' => $type,
                    'role' => $role,
                    'empname' => $empname,
                    'action' => $action,
                    'reason' => $reason,
                    'date' => $date,
                    'is_active' => 'yes'
                );

            }
            $insert_id = $this->designation_model->addSanction($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
            redirect("admin/sanction/sanction");
        } else {

            $this->load->view("layout/header");
            $this->load->view("admin/staff/sanction", $data);
            $this->load->view("layout/footer");
        }
    }

    function sanctionedit($id) {

        $result = $this->designation_model->gets($id);
        $data["title"] = $this->lang->line('edit') . " " . $this->lang->line('designation');
        $data["result"] = $result;

        $designation = $this->designation_model->gets();
        $data["designation"] = $designation;
        $this->load->view("layout/header");
        $this->load->view("admin/staff/sanction", $data);
        $this->load->view("layout/footer");
    }

    function sanctiondelete($id) {

        $this->designation_model->deleteSanction($id);
        redirect('admin/sanction/sanction');
    }

}

?>