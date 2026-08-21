<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Journal extends Admin_Controller {

    function __construct() {
        parent::__construct();

        $this->load->helper('url');
    }

    function index() {
        if (!$this->rbac->hasPrivilege('clients', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Caisse');
        $this->session->set_userdata('sub_menu', 'income/index');
        $data['title'] = 'Item Supplier List';
        $journal_result = $this->journal_model->get();
        $data['journal_comptablelist'] = $journal_result;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/journal/journal_comptableList', $data);
        $this->load->view('layout/footer', $data);
    }

    function delete($id) {
        if (!$this->rbac->hasPrivilege('clients', 'can_delete')) {
            access_denied();
        }
        $data['title'] = 'Journal comptable';
        $this->journal_model->remove($id);
        redirect('admin/journal/index');
    }

    function create() {
        if (!$this->rbac->hasPrivilege('clients', 'can_add')) {
            access_denied();
        }
        $data['title'] = 'Ajouté un journal comptable';
        $comptable_result = $this->journal_model->get();
        $data['comptablelist'] = $comptable_result;

        $this->form_validation->set_rules('code_journal', $this->lang->line('name'), 'trim|required|xss_clean');


        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/journal/journal_comptableList', $data);
            $this->load->view('layout/footer', $data);
        } else {


            $data = array(
                'code_journal' => $this->input->post('code_journal'),
                'libelle_journal' => $this->input->post('libelle_journal'),
                'type_journal' => $this->input->post('type_journal'),

                );
            $this->journal_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/journal/index');
        }
    }

    function edit($id) {
        if (!$this->rbac->hasPrivilege('clients', 'can_edit')) {
            access_denied();
        }
        $data['title'] = 'Edit journal comptable';
        $journa_result = $this->journal_model->get();
        $data['journalist'] = $journa_result;
        $data['id'] = $id;
        $store = $this->journal_model->get($id);
        $data['journaliste'] = $store;

        $this->form_validation->set_rules('code_journal', $this->lang->line('name'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/journal/journal_comptableEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {


            $data = array(
                'id' => $id,

                'code_journal' => $this->input->post('code_journal'),
                'libelle_journal' => $this->input->post('libelle_journal'),
                'type_journal' => $this->input->post('type_journal'),

            );
            $this->journal_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/journal/index');
        }
    }

}

?>