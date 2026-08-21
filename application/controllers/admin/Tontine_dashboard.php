<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tontine_membres extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("tontine_membres_model");
        $this->load->model("tontine_groupes_model");
    }

    // Liste des membres
    public function index() {
        if (!$this->rbac->hasPrivilege('tontine_membres', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'tontine');
        $this->session->set_userdata('sub_menu', 'admin/tontine_membres');

        // Récupération des filtres
        $search = $this->input->get('search');
        $statut = $this->input->get('statut');
        $groupe = $this->input->get('groupe');
        $date_adhesion = $this->input->get('date_adhesion');

        // Récupération des données
        $data['membres'] = $this->tontine_membres_model->get_membres($search, $statut, $groupe, $date_adhesion);
        $data['total_membres'] = $this->tontine_membres_model->count_membres($search, $statut, $groupe, $date_adhesion);
        $data['groupes'] = $this->tontine_groupes_model->get_all();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/tontine_membres', $data);
        $this->load->view('layout/footer');
    }

    // Ajouter un membre
    public function ajouter() {
        if (!$this->rbac->hasPrivilege('tontine_membres', 'can_add')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'tontine');
        $this->session->set_userdata('sub_menu', 'admin/tontine_membres');

        if ($this->input->post()) {
            $this->form_validation->set_rules('nom', 'Nom', 'required');
            $this->form_validation->set_rules('prenom', 'Prénom', 'required');
            $this->form_validation->set_rules('telephone', 'Téléphone', 'required|is_unique[tontine_membres.telephone]');

            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'nom' => $this->input->post('nom'),
                    'prenom' => $this->input->post('prenom'),
                    'telephone' => $this->input->post('telephone'),
                    'email' => $this->input->post('email'),
                    'adresse' => $this->input->post('adresse'),
                    'profession' => $this->input->post('profession'),
                    'groupe_id' => $this->input->post('groupe_id'),
                    'date_adhesion' => date('Y-m-d'),
                    'statut' => 'actif',
                    'created_at' => date('Y-m-d H:i:s')
                );

                $id = $this->tontine_membres_model->ajouter($data);
                if ($id) {
                    $this->session->set_flashdata('success', 'Membre ajouté avec succès');
                    redirect('admin/tontine_membres');
                } else {
                    $this->session->set_flashdata('error', 'Erreur lors de l\'ajout');
                }
            }
        }

        $data['groupes'] = $this->tontine_groupes_model->get_all();
        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/tontine_membres_ajouter', $data);
        $this->load->view('layout/footer');
    }

    // Voir un membre
    public function voir($id) {
        if (!$this->rbac->hasPrivilege('tontine_membres', 'can_view')) {
            access_denied();
        }

        $data['membre'] = $this->tontine_membres_model->get_membre($id);
        if (empty($data['membre'])) {
            show_404();
        }

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/tontine_membres_voir', $data);
        $this->load->view('layout/footer');
    }
}