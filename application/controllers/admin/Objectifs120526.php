<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Objectifs extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('designation_model');
        $this->load->model('staff_model');
        $this->load->model('objectifs_model');

        $this->load->helper('url');
    }

    function index() {
        if (!$this->rbac->hasPrivilege('clients', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'objectifs/index');
        $designation = $this->designation_model->gets();
        $data['stff_list'] = $this->staff_model->get();
        $data["designation"] = $designation;
        $userdata = $this->customlib->getUserData();
        $staffRole = $this->staff_model->getStaffRole();
        $data["staffrole"] = $staffRole;

        $data['title'] = 'Item Supplier List';
        $objectifs_result = $this->Objectifs_model->get();
        $data['objectifslist'] = $objectifs_result;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/objectifs/objectifsList', $data);
        $this->load->view('layout/footer', $data);
    }



    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('clients', 'can_delete')) {
            access_denied();
        }

        // Vérifie si l'objectif existe
        $objectif = $this->objectifs_model->get($id);
        if (empty($objectif)) {
            show_error("L'objectif avec l'ID $id est introuvable.");
            return;
        }

        // Supprime l'enregistrement
        $this->objectifs_model->remove($id);

        // Redirige vers la liste
        redirect('admin/objectifs/index');
    }


    public function create()
    {
        // Vérifie les privilèges
        if (!$this->rbac->hasPrivilege('clients', 'can_add')) {
            access_denied();
        }

        $data['title'] = 'Ajouter un objectif';

        // Récupère la liste des objectifs existants
        $data['objectifslist'] = $this->Objectifs_model->get();

        // Définir les règles de validation du formulaire
        $this->form_validation->set_rules('user_name', 'Commerciaux', 'trim|required|xss_clean');
        $this->form_validation->set_rules('target_amount', 'target_amount', 'trim|required|numeric');
        $this->form_validation->set_rules('date', 'Date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            // En cas d’erreur de validation, on recharge le formulaire
            $this->load->view('layout/header', $data);
            $this->load->view('admin/objectifs/objectifsList', $data);
            $this->load->view('layout/footer');
        } else {
            // Traitement du formulaire valide
            $formData = array(
                'user_name'             => $this->input->post('user_name'),
                'target_amount'  => $this->input->post('target_amount'),
                'date'             => $this->input->post('date'),
            );

            $this->Objectifs_model->add($formData);

            // Message de succès
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');

            // Redirection après ajout
            redirect('admin/objectifs/index');
        }
    }





    function edit($id) {
        if (!$this->rbac->hasPrivilege('clients', 'can_edit')) {
            access_denied();
        }
        $data['title'] = 'Edit Item Supplier';

        $store = $this->objectifs_model->get($id);
        $data['objectifs'] = $store;

        $this->form_validation->set_rules('user_name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('target_amount', $this->lang->line('phone'), 'trim|numeric|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('email'), 'trim|xss_clean|valid_email');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/objectifs/objectifsEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {


            $data = array(
                'id' => $id,

                'user_name' => $this->input->post('user_name'),
                'target_amount' => $this->input->post('target_amount'),
                'date' => $this->input->post('date'),

            );
            $this->objectifs_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/objectifs/index');
        }
    }

}

?>