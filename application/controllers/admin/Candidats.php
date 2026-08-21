<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Candidats extends Admin_Controller {

    function __construct() {
        parent::__construct();

        $this->load->helper('url');
    }

    function index() {
        if (!$this->rbac->hasPrivilege('clients', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'candidats/index');
        $data['title'] = 'Item Supplier List';
        $joboffers_result = $this->Recrutements_model->getcandidats();
        $data['candidatslist'] = $joboffers_result;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/frontoffice/candidats', $data);
        $this->load->view('layout/footer', $data);
    }



    function candidature() {
        if (!$this->rbac->hasPrivilege('clients', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'candidatures/index');
        $data['title'] = 'Item Supplier List';
        $joboffers_result = $this->Recrutements_model->get();
        $data['joblist'] = $joboffers_result;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/frontoffice/candidature_postuler', $data);
        $this->load->view('layout/footer', $data);
    }



    function delete($id) {
        if (!$this->rbac->hasPrivilege('clients', 'can_delete')) {
            access_denied();
        }
        $data['title'] = 'Item Supplier List';
        $this->Recrutements_model->remove($id);
        redirect('admin/candidature/index');
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



    function postuler($id)
    {
        // Vérifier les privilèges
        if (!$this->rbac->hasPrivilege('clients', 'can_edit')) {
            access_denied();
        }

        // Charger l'offre d'emploi
        $data['title'] = 'Postuler à une offre d\'emploi';
        $data['id'] = $id;
        $data['jobs'] = $this->Recrutements_model->get($id);

        // Si le formulaire est soumis
        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            // Gestion upload CV
            $resumeName = null;
            if (!empty($_FILES['resume']['name'])) {
                $config['upload_path']   = './uploads/resumes/';
                $config['allowed_types'] = 'pdf|doc|docx';
                $config['max_size']      = 2048;
                $this->load->library('upload', $config);

                if ($this->upload->do_upload('resume')) {
                    $uploadData = $this->upload->data();
                    $resumeName = $uploadData['file_name'];
                }
            }

            // Préparer les données
            $applicationData = array(
                'job_id'          => $id,
                'job_name'  => $this->input->post('job_name'),
                'candidate_name'  => $this->input->post('candidate_name'),
                'candidate_email' => $this->input->post('candidate_email'),
                'image' => $this->input->post('file'),
                'cover_letter'    => $this->input->post('cover_letter'),
            );
            $applicationData = $this->Dispatch_model->insert('dispatch_receive', $applicationData);
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $applicationData . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/dispatch_receive/" . $img_name);
                $this->Recrutements_model->image_add('dispatch', $applicationData, $img_name);
            }

            // Sauvegarder la candidature
            $this->Recrutements_model->insertApplication($applicationData);

            // Message succès
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Votre candidature a été envoyée avec succès.</div>');
            redirect('admin/candidatures/index');
        } else {
            // Afficher le formulaire
            $this->load->view('layout/header', $data);
            $this->load->view('admin/frontoffice/candidature_postuler', $data);
            $this->load->view('layout/footer', $data);
        }
    }


    public function download($documents) {
        $this->load->helper('download');
        $filepath = "./uploads/front_office/dispatch_receive/" . $documents;
        $data = file_get_contents($filepath);
        $name = $documents;
        force_download($name, $data);
    }

    public function handle_upload($str,$var)
    {

        $image_validate = $this->config->item('file_validate');
        $result = $this->filetype_model->get();
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type         = $_FILES[$var]['type'];
            $file_size         = $_FILES[$var]["size"];
            $file_name         = $_FILES[$var]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES[$var]['tmp_name'])) {

                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'File Type Not Allowed');
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'Extension Not Allowed');
                    return false;
                }
                if ($file_size > $result->file_size) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }

            } else {
                $this->form_validation->set_message('handle_upload', "File Type / Extension Error Uploading  Image");
                return false;
            }

            return true;
        }
        return true;

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
            $this->load->view('admin/frontoffice/candidaturesview', $data);
            $this->load->view('layout/footer', $data);
        }
    }


}

?>