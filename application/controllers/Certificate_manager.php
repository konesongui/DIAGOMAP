<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Certificate_manager extends Admin_Controller
{
    private $upload_path = 'uploads/certificates/';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('certificate_model');
        $this->load->library('form_validation');
        $this->load->helper('string');

        // Charger la configuration des types
        $this->config->load('certificate_types');
        $this->certificate_types = $this->config->item('certificate_types');
    }

    /**
     * Liste des modèles de certificats
     */
    public function index()
    {
        if (!$this->rbac->hasPrivilege('certificate', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/certificate_manager');

        $this->data['certificates'] = $this->certificate_model->get_all();
        $this->data['types'] = $this->certificate_types;

        $this->load->view('layout/header');
        $this->load->view('admin/certificates/index', $this->data);
        $this->load->view('layout/footer');
    }

    /**
     * Créer un nouveau modèle de certificat
     */
    public function create()
    {
        if (!$this->rbac->hasPrivilege('certificate', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('template_type', 'Type de certificat', 'trim|required');
        $this->form_validation->set_rules('title', 'Titre', 'trim|required');
        $this->form_validation->set_rules('content_body', 'Contenu', 'trim|required');
        $this->form_validation->set_rules('signature_text', 'Texte de signature', 'trim');
        $this->form_validation->set_rules('header_color', 'Couleur d\'en-tête', 'trim');

        if ($this->form_validation->run() == true) {
            $data = array(
                'template_type' => $this->input->post('template_type'),
                'title' => $this->input->post('title'),
                'content_body' => $this->input->post('content_body'),
                'signature_text' => $this->input->post('signature_text'),
                'header_color' => $this->input->post('header_color') ?: '#453278',
                'generated_code' => random_string('alnum', 12),
                'is_active' => 1
            );

            $insert_id = $this->certificate_model->add($data);

            // Gestion des uploads
            $this->handle_uploads($insert_id);

            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/certificate_manager');
        }

        $this->data['types'] = $this->certificate_types;
        $this->data['generated_code'] = random_string('alnum', 12);

        $this->load->view('layout/header');
        $this->load->view('admin/certificates/form', $this->data);
        $this->load->view('layout/footer');
    }

    /**
     * Modifier un modèle de certificat
     */
    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('certificate', 'can_edit')) {
            access_denied();
        }

        $this->data['certificate'] = $this->certificate_model->get($id);

        if (!$this->data['certificate']) {
            show_404();
        }

        $this->form_validation->set_rules('title', 'Titre', 'trim|required');
        $this->form_validation->set_rules('content_body', 'Contenu', 'trim|required');

        if ($this->form_validation->run() == true) {
            $data = array(
                'id' => $id,
                'title' => $this->input->post('title'),
                'content_body' => $this->input->post('content_body'),
                'signature_text' => $this->input->post('signature_text'),
                'header_color' => $this->input->post('header_color') ?: '#453278'
            );

            $this->handle_uploads($id);
            $this->certificate_model->update($data);

            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/certificate_manager');
        }

        $this->data['types'] = $this->certificate_types;

        $this->load->view('layout/header');
        $this->load->view('admin/certificates/form', $this->data);
        $this->load->view('layout/footer');
    }

    /**
     * Supprimer un modèle de certificat
     */
    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('certificate', 'can_delete')) {
            access_denied();
        }

        $this->certificate_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/certificate_manager');
    }

    /**
     * Aperçu du certificat
     */
    public function preview()
    {
        $id = $this->input->post('certificate_id');
        $data['certificate'] = $this->certificate_model->get($id);

        // Données de démonstration pour l'aperçu
        $data['preview_data'] = $this->get_preview_data($data['certificate']->template_type);

        $this->load->view('admin/certificates/preview', $data);
    }

    /**
     * Générer un certificat
     */
    public function generate($id)
    {
        // Cette méthode sera appelée pour générer le PDF
        $certificate = $this->certificate_model->get($id);
        $data = $this->input->post('data'); // Données dynamiques

        $this->load->library('certificate_generator');
        $pdf = $this->certificate_generator->generate($certificate, $data);

        // Télécharger le PDF
        $pdf->download('certificat_' . $certificate->generated_code . '.pdf');
    }

    /**
     * Gestion des uploads (logo et signature)
     */
    private function handle_uploads($id)
    {
        // Créer le dossier si nécessaire
        if (!is_dir($this->upload_path)) {
            mkdir($this->upload_path, 0777, true);
        }

        $logo_path = null;
        $signature_path = null;

        // Upload du logo
        if (!empty($_FILES['logo']['name'])) {
            $config['upload_path'] = $this->upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['file_name'] = 'logo_' . $id;

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('logo')) {
                $upload_data = $this->upload->data();
                $logo_path = $this->upload_path . $upload_data['file_name'];
            }
        } elseif ($this->input->post('old_logo')) {
            $logo_path = $this->input->post('old_logo');
        }

        // Upload de la signature
        if (!empty($_FILES['signature']['name'])) {
            $config['upload_path'] = $this->upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['file_name'] = 'sign_' . $id;

            $this->upload->initialize($config);
            if ($this->upload->do_upload('signature')) {
                $upload_data = $this->upload->data();
                $signature_path = $this->upload_path . $upload_data['file_name'];
            }
        } elseif ($this->input->post('old_signature')) {
            $signature_path = $this->input->post('old_signature');
        }

        if ($logo_path || $signature_path) {
            $this->certificate_model->update_images($id, $logo_path, $signature_path);
        }
    }

    /**
     * Données de démonstration pour l'aperçu
     */
    private function get_preview_data($type)
    {
        $preview = array();

        switch($type) {
            case 'work':
                $preview = array(
                    'employee_name' => 'Jean Dupont',
                    'position' => 'Développeur Senior',
                    'start_date' => '01/01/2020',
                    'end_date' => '31/12/2023'
                );
                break;
            case 'training':
                $preview = array(
                    'participant_name' => 'Marie Martin',
                    'training_name' => 'Formation PHP Avancé',
                    'duration' => '40 heures',
                    'completion_date' => '15/03/2024'
                );
                break;
            case 'internship':
                $preview = array(
                    'intern_name' => 'Thomas Bernard',
                    'department' => 'Service Informatique',
                    'start_date' => '01/02/2024',
                    'end_date' => '30/04/2024'
                );
                break;
        }

        return $preview;
    }
}