<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Recrutements extends Admin_Controller {

    function __construct() {
        parent::__construct();

        $this->load->helper('url');
    }

    function index() {
        if (!$this->rbac->hasPrivilege('clients', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'recrutements/index');
        $data['title'] = 'Item Supplier List';
        $joboffers_result = $this->Recrutements_model->get();
        $data['joblist'] = $joboffers_result;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/frontoffice/recrutementview', $data);
        $this->load->view('layout/footer', $data);
    }

    function delete($id) {
        if (!$this->rbac->hasPrivilege('clients', 'can_delete')) {
            access_denied();
        }
        $data['title'] = 'Item Supplier List';
        $this->Recrutements_model->remove($id);
        redirect('admin/recrutements/index');
    }

    function create() {
        // Vérifier si l'utilisateur a les privilèges nécessaires
        if (!$this->rbac->hasPrivilege('clients', 'can_add')) {
            access_denied();
        }

        // Données pour le titre et la liste des clients (ou autre entité pertinente)
        $data['title'] = 'Ajouter une offre d\'emploi';

        // Préparer les données à insérer dans la base de données
        $insertData = array(
            'title' => $this->input->post('title'),
            'department' => $this->input->post('department'),
            'location' => $this->input->post('location'),
            'description' => $this->input->post('description'),
            'deadline' => $this->input->post('deadline'),
            'status' => $this->input->post('status'),
        );

        // Ajouter les données à la base de données
        $this->Recrutements_model->add($insertData);

        // Message de succès
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');

        // Redirection après succès
        redirect('admin/recrutements/index');
    }

    function edit($id)
    {
        // Vérifier les privilèges de l'utilisateur
        if (!$this->rbac->hasPrivilege('clients', 'can_edit')) {
            access_denied();
        }

        // Titre de la page
        $data['title'] = 'Modifier une offre d\'emploi';
        $jobs_result = $this->Recrutements_model->get();
        $data['jobslist'] = $jobs_result;


        // Récupérer les données de l'offre
        $data['id'] = $id;
        $data['jobs'] = $this->Recrutements_model->get($id);


        // Si le formulaire est soumis (via POST)
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            // Données à mettre à jour
            $updatedData = array(
                'id'          => $id,
                'title'       => $this->input->post('title'),
                'department'  => $this->input->post('department'),
                'location'    => $this->input->post('location'),
                'description' => $this->input->post('description'),
                'deadline'    => $this->input->post('deadline'),
                'status'      => $this->input->post('status'),
            );

            // Mettre à jour l'offre
            $this->Recrutements_model->update($id, $updatedData);

            // Message de succès
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');

            // Redirection
            redirect('admin/recrutements/index');
        } else {
            // Affichage du formulaire
            $this->load->view('layout/header', $data);
            $this->load->view('admin/frontoffice/recrutementeditview', $data);
            $this->load->view('layout/footer', $data);
        }
    }


}

?>