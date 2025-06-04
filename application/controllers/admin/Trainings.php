<?php


class Trainings extends Admin_Controller {

    function __construct() {

        parent::__construct();

        $this->load->helper('file');
        $this->config->load("payroll");

        $this->load->model('trainings_model');
        $this->load->model('staff_model');
    }

    function index() {

        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/trainings');
        $data["title"] = $this->lang->line('add') . " " . $this->lang->line('leave') . " " . $this->lang->line('type');

        $Trainings = $this->trainings_model->getTrainings();

        $data["training"] = $Trainings;
        $this->load->view("layout/header");
        $this->load->view("admin/staff/trainings", $data);
        $this->load->view("layout/footer");
    }

    function createTrainings() {


        $this->form_validation->set_rules(
                'type', $this->lang->line('leave_type'), array('required',
            array('check_exists', array($this->trainings_model, 'valid_training'))
                )
        );
        $data["title"] = $this->lang->line('add') . " " . $this->lang->line('leave') . " " . $this->lang->line('type');
        if ($this->form_validation->run()) {

            $type = $this->input->post("type");
            $training = $this->input->post("training");
            $number_per = $this->input->post("number_per");
            $amount = $this->input->post("amount");
            $start_date = $this->input->post("start_date");
            $end_date = $this->input->post("end_date");
            $resume = $this->input->post("resume");

            $trainingid = $this->input->post("trainingid");
            $status = $this->input->post("status");
            if (empty($trainingid)) {

                if (!$this->rbac->hasPrivilege('training', 'can_add')) {
                    access_denied();
                }
            } else {

                if (!$this->rbac->hasPrivilege('training', 'can_edit')) {
                    access_denied();
                }
            }

            if (!empty($trainingid)) {
                $data = array('type' => $type, 'is_active' => 'yes', 'id' => $trainingid);
                $data = array('training' => $type, 'is_active' => 'yes', 'id' => $trainingid);
                $data = array('number_per' => $type, 'is_active' => 'yes', 'id' => $trainingid);
                $data = array('amount' => $type, 'is_active' => 'yes', 'id' => $trainingid);
                $data = array('start_date' => $type, 'is_active' => 'yes', 'id' => $trainingid);
                $data = array('end_date' => $type, 'is_active' => 'yes', 'id' => $trainingid);
                $data = array('resume' => $type, 'is_active' => 'yes', 'id' => $trainingid);
            } else {

                $data = array(
                    'type' => $this->input->post('type'),
                    'training' => $this->input->post('training'),
                    'number_per' => $this->input->post('number_per'),
                    'amount' => $this->input->post('amount'),
                    'start_date' => $this->input->post('start_date'),
                    'end_date' => $this->input->post('end_date'),
                    'resume' => $this->input->post('resume'),



                );
            }

            $insert_id = $this->trainings_model->addTrainings($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
            redirect("admin/trainings");
        } else {

            $Trainings = $this->trainings_model->getTrainings();
            $data["training"] = $Trainings;
            $this->load->view("layout/header");
            $this->load->view("admin/staff/trainings", $data);
            $this->load->view("layout/footer");
        }
    }



    function trainingedit($id) {

        $result = $this->trainings_model->getTrainings($id);

        $data["title"] = $this->lang->line('edit') . " " . $this->lang->line('leave') . " " . $this->lang->line('type');
        $data["result"] = $result;

        $Trainings = $this->trainings_model->getTrainings();
        $data["training"] = $Trainings;
        $this->load->view("layout/header");
        $this->load->view("admin/staff/trainings", $data);
        $this->load->view("layout/footer");
    }

    function trainingdelete($id) {

        $this->trainings_model->deleteTrainings($id);
        redirect('admin/trainings');
    }

}

?>