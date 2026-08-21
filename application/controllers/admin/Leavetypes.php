<?php


class LeaveTypes extends Admin_Controller {

    function __construct() {

        parent::__construct();

        $this->load->helper('file');
        $this->config->load("payroll");

        $this->load->model('leavetypes_model');
        $this->load->model('staff_model');
    }

    public function activate($id)
    {
        $data = array('is_active' => 'yes');
        $this->leavetypes_model->update($id, $data);
        $this->session->set_flashdata('msg', 'Le congé a été activé avec succès.');
        redirect('admin/leavetypes');
    }

    public function deactivate($id)
    {
        $data = array('is_active' => 'no');
        $this->leavetypes_model->update($id, $data);
        $this->session->set_flashdata('msg', 'Le congé a été désactivé avec succès.');
        redirect('admin/leavetypes');
    }

    function index() {

        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/leavetypes');
        $data["title"] = $this->lang->line('add') . " " . $this->lang->line('leave') . " " . $this->lang->line('type');

        $LeaveTypes = $this->leavetypes_model->getLeaveType();

        $data["leavetype"] = $LeaveTypes;
        $this->load->view("layout/header");
        $this->load->view("admin/staff/leavetypes", $data);
        $this->load->view("layout/footer");
    }

    function createLeaveType() {


        $this->form_validation->set_rules(
                'type', $this->lang->line('leave_type'), array('required',
            array('check_exists', array($this->leavetypes_model, 'valid_leave_type'))
                )
        );
        $data["title"] = $this->lang->line('add') . " " . $this->lang->line('leave') . " " . $this->lang->line('type');
        if ($this->form_validation->run()) {

            $type = $this->input->post("type");
            $ndays = $this->input->post("ndays");
            $leavetypeid = $this->input->post("leavetypeid");
            $status = $this->input->post("status");
            if (!empty($leavetypeid)) {
                $data = array(
                    'type' => $type,
                    'ndays' => $ndays, // <-- nouveau champ
                    'is_active' => 'yes',
                    'id' => $leavetypeid
                );
            } else {
                $data = array(
                    'type' => $type,
                    'ndays' => $ndays, // <-- nouveau champ
                    'is_active' => 'yes'
                );
            }

            $insert_id = $this->leavetypes_model->addLeaveType($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
            redirect("admin/leavetypes");
        } else {

            $LeaveTypes = $this->leavetypes_model->getLeaveType();
            $data["leavetype"] = $LeaveTypes;
            $this->load->view("layout/header");
            $this->load->view("admin/staff/leavetypes", $data);
            $this->load->view("layout/footer");
        }
    }

    function leaveedit($id) {

        $result = $this->staff_model->getLeaveType($id);

        $data["title"] = $this->lang->line('edit') . " " . $this->lang->line('leave') . " " . $this->lang->line('type');
        $data["result"] = $result;

        $LeaveTypes = $this->leavetypes_model->getLeaveType();
        $data["leavetype"] = $LeaveTypes;
        $this->load->view("layout/header");
        $this->load->view("admin/staff/leavetypes", $data);
        $this->load->view("layout/footer");
    }

    function leavedelete($id) {

        $this->leavetypes_model->deleteLeaveType($id);
        redirect('admin/leavetypes');
    }

}

?>