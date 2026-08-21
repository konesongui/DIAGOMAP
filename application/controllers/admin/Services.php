<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Services extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('services_model');
        if (!$this->rbac->hasPrivilege('services_commercial', 'can_view')) {
            access_denied();
        }
    }

    public function index() {
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'services/index');
        $data['services'] = $this->services_model->get();
        $this->load->view('layout/header', $data);
        $this->load->view('admin/services/list', $data);
        $this->load->view('layout/footer');
    }

    public function ajax_list() {
        $this->output->set_content_type('application/json');
        $services = $this->services_model->get();
        echo json_encode($services);
    }

    public function ajax_add() {
        $this->output->set_content_type('application/json');
        $this->form_validation->set_rules('name', 'Nom', 'required|trim');
        $this->form_validation->set_rules('unit_price', 'Prix unitaire', 'required|numeric');
        if ($this->form_validation->run() == true) {
            $data = [
                'name'         => $this->input->post('name'),
                'description'  => $this->input->post('description'),
                'unit_price'   => $this->input->post('unit_price'),
                'duration'     => $this->input->post('duration'),
                'created_at'   => date('Y-m-d H:i:s')
            ];
            $id = $this->services_model->add($data);
            echo json_encode(['status' => 'success', 'message' => 'Service ajouté', 'id' => $id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
        }
    }

    public function ajax_edit($id) {
        $this->output->set_content_type('application/json');
        $service = $this->services_model->get($id);
        echo json_encode($service);
    }

    public function ajax_update() {
        $this->output->set_content_type('application/json');
        $id = $this->input->post('id');
        $this->form_validation->set_rules('name', 'Nom', 'required|trim');
        $this->form_validation->set_rules('unit_price', 'Prix unitaire', 'required|numeric');
        if ($this->form_validation->run() == true) {
            $data = [
                'name'         => $this->input->post('name'),
                'description'  => $this->input->post('description'),
                'unit_price'   => $this->input->post('unit_price'),
                'duration'     => $this->input->post('duration'),
                'updated_at'   => date('Y-m-d H:i:s')
            ];
            $this->services_model->update($id, $data);
            echo json_encode(['status' => 'success', 'message' => 'Service mis à jour']);
        } else {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
        }
    }

    public function ajax_delete($id) {
        $this->output->set_content_type('application/json');
        $this->services_model->delete($id);
        echo json_encode(['status' => 'success', 'message' => 'Service supprimé']);
    }

    public function create() {
        if (!$this->rbac->hasPrivilege('services_commercial', 'can_add')) access_denied();
        $this->form_validation->set_rules('name', 'Nom', 'required|trim');
        $this->form_validation->set_rules('unit_price', 'Prix unitaire', 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = [
                'name'         => $this->input->post('name'),
                'description'  => $this->input->post('description'),
                'unit_price'   => $this->input->post('unit_price'),
                'duration'     => $this->input->post('duration'),
                'created_at'   => date('Y-m-d H:i:s')
            ];
            $insert_id = $this->services_model->add($data);

            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'Service ajouté', 'id' => $insert_id]);
                return;
            }
            $this->session->set_flashdata('msg', 'Service ajouté');
            redirect('admin/services');
        } else {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => validation_errors()]);
                return;
            }
            $this->load->view('layout/header');
            $this->load->view('admin/services/form');
            $this->load->view('layout/footer');
        }
    }

    public function edit($id) {
        if (!$this->rbac->hasPrivilege('services_commercial', 'can_edit')) access_denied();
        $this->form_validation->set_rules('name', 'Nom', 'required|trim');
        $this->form_validation->set_rules('unit_price', 'Prix unitaire', 'required|numeric');

        if ($this->form_validation->run() == true) {
            $update = [
                'name'         => $this->input->post('name'),
                'description'  => $this->input->post('description'),
                'unit_price'   => $this->input->post('unit_price'),
                'duration'     => $this->input->post('duration'),
                'updated_at'   => date('Y-m-d H:i:s')
            ];
            $this->services_model->update($id, $update);

            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'Service modifié']);
                return;
            }
            $this->session->set_flashdata('msg', 'Service modifié');
            redirect('admin/services');
        } else {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => validation_errors()]);
                return;
            }
            $data['service'] = $this->services_model->get($id);
            $this->load->view('layout/header', $data);
            $this->load->view('admin/services/form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function delete($id) {
        if (!$this->rbac->hasPrivilege('services_commercial', 'can_delete')) access_denied();
        $this->services_model->delete($id);

        if ($this->input->is_ajax_request()) {
            echo json_encode(['status' => 'success', 'message' => 'Service supprimé']);
            return;
        }
        $this->session->set_flashdata('msg', 'Service supprimé');
        redirect('admin/services');
    }

    public function create_16() {
        if (!$this->rbac->hasPrivilege('services_commercial', 'can_add')) access_denied();
        $this->form_validation->set_rules('name', 'Nom', 'required|trim');
        $this->form_validation->set_rules('unit_price', 'Prix unitaire', 'required|numeric');
        if ($this->form_validation->run() == true) {
            $data = [
                'name'         => $this->input->post('name'),
                'description'  => $this->input->post('description'),
                'unit_price'   => $this->input->post('unit_price'),
                'duration'     => $this->input->post('duration'),
                'created_at'   => date('Y-m-d H:i:s')
            ];
            $this->services_model->add($data);
            $this->session->set_flashdata('msg', 'Service ajouté');
            redirect('admin/services');
        }
        $this->load->view('layout/header');
        $this->load->view('admin/services/form');
        $this->load->view('layout/footer');
    }

    public function edit_16($id) {
        if (!$this->rbac->hasPrivilege('services', 'can_edit')) access_denied();
        $data['service'] = $this->services_model->get($id);
        $this->form_validation->set_rules('name', 'Nom', 'required|trim');
        if ($this->form_validation->run() == true) {
            $update = [
                'name'         => $this->input->post('name'),
                'description'  => $this->input->post('description'),
                'unit_price'   => $this->input->post('unit_price'),
                'duration'     => $this->input->post('duration'),
                'updated_at'   => date('Y-m-d H:i:s')
            ];
            $this->services_model->update($id, $update);
            $this->session->set_flashdata('msg', 'Service modifié');
            redirect('admin/services');
        }
        $this->load->view('layout/header', $data);
        $this->load->view('admin/services/form', $data);
        $this->load->view('layout/footer');
    }

    public function delete_16($id) {
        if (!$this->rbac->hasPrivilege('services', 'can_delete')) access_denied();
        $this->services_model->delete($id);
        $this->session->set_flashdata('msg', 'Service supprimé');
        redirect('admin/services');
    }

    // Récupération des services pour datalist (AJAX)
    public function get_services_json() {
        $services = $this->services_model->get();
        echo json_encode($services);
    }

    public function get_service_details() {
        $name = $this->input->post('name');
        if ($name) {
            $service = $this->db->where('name', $name)->get('services')->row();
            echo json_encode($service);
        } else {
            echo json_encode(null);
        }
    }


}