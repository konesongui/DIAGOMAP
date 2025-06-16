<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Comptecomptable extends Admin_Controller {

    function __construct() {
        parent::__construct();

        $this->load->helper('url');
    }

    function index() {
        if (!$this->rbac->hasPrivilege('clients', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'income');
        $this->session->set_userdata('sub_menu', 'comptecomptable/index');
        $data['title'] = 'Item Supplier List';
        $comptecomtable_result = $this->comptecomptable_model->get();
        $data['compte_comptablelist'] = $comptecomtable_result;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/comptecomptable/compte_comptableList', $data);
        $this->load->view('layout/footer', $data);
    }

    function delete($id) {
        if (!$this->rbac->hasPrivilege('clients', 'can_delete')) {
            access_denied();
        }
        $data['title'] = 'Compte comptable';
        $this->comptecomptable_model->remove($id);
        redirect('admin/comptecomptable/index');
    }

    function create() {
        if (!$this->rbac->hasPrivilege('clients', 'can_add')) {
            access_denied();
        }
        $data['title'] = 'Ajouté un compte comptable';
        $comptable_result = $this->comptecomptable_model->get();
        $data['comptablelist'] = $comptable_result;

        $this->form_validation->set_rules('numero', $this->lang->line('name'), 'trim|xss_clean');


        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/comptecomptable/compte_comptableList', $data);
            $this->load->view('layout/footer', $data);
        } else {


            $data = array(
                'numero' => $this->input->post('numero'),
                'intitule' => $this->input->post('intitule'),
                'classe' => $this->input->post('classe'),
                'type_compte' => $this->input->post('type_compte'),
                'est_actif' => $this->input->post('est_actif')  ? 1 : 0,

                );
            $this->comptecomptable_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/comptecomptable/index');
        }
    }

    function edit($id) {
        if (!$this->rbac->hasPrivilege('clients', 'can_edit')) {
            access_denied();
        }
        $data['title'] = 'Edit journal comptable';
        $comptecompt_result = $this->comptecomptable_model->get();
        $data['comptecomptalist'] = $comptecompt_result;
        $data['id'] = $id;
        $store = $this->comptecomptable_model->get($id);
        $data['compteliste'] = $store;

        $this->form_validation->set_rules('numero', $this->lang->line('name'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/comptecomptable/compte_comptableEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {


            $data = array(
                'id' => $id,
                'numero' => $this->input->post('numero'),
                'intitule' => $this->input->post('intitule'),
                'classe' => $this->input->post('classe'),
                'type_compte' => $this->input->post('type_compte'),
                'est_actif' => $this->input->post('est_actif')  ? 1 : 0,

            );
            $this->comptecomptable_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/comptecomptable/index');
        }
    }

}

?>