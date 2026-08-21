<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tontine_membres extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("tontine_membres_model");
        $this->load->model("tontine_groupes_model");

        // Activation du débogage pour identifier l'erreur 500
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    // ========================================== //
    // INDEX - Liste des membres                  //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('tontine_membres', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'tontine');
        $this->session->set_userdata('sub_menu', 'admin/tontine_membres');

        try {
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

        } catch (Exception $e) {
            log_message('error', 'Erreur dans Tontine_membres::index - ' . $e->getMessage());
            show_error('Une erreur est survenue lors du chargement des membres');
        }
    }

    // ========================================== //
    // AJOUTER UN MEMBRE                          //
    // ========================================== //
    public function ajouter() {
        if (!$this->rbac->hasPrivilege('tontine_membres', 'can_add')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'tontine');
        $this->session->set_userdata('sub_menu', 'admin/tontine_membres');

        if ($this->input->post()) {
            try {
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
                        $this->session->set_flashdata('error', 'Erreur lors de l\'ajout du membre');
                    }
                }
            } catch (Exception $e) {
                log_message('error', 'Erreur dans Tontine_membres::ajouter - ' . $e->getMessage());
                $this->session->set_flashdata('error', 'Une erreur est survenue');
            }
        }

        $data['groupes'] = $this->tontine_groupes_model->get_all();
        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/tontine_membres_ajouter', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // VOIR UN MEMBRE                             //
    // ========================================== //
    public function voir($id) {
        if (!$this->rbac->hasPrivilege('tontine_membres', 'can_view')) {
            access_denied();
        }

        try {
            $data['membre'] = $this->tontine_membres_model->get_membre($id);
            if (empty($data['membre'])) {
                show_404();
            }

            // Récupération des statistiques du membre
            $data['statistiques'] = $this->tontine_membres_model->get_statistiques_membre($id);

            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/tontine_membres_voir', $data);
            $this->load->view('layout/footer');

        } catch (Exception $e) {
            log_message('error', 'Erreur dans Tontine_membres::voir - ' . $e->getMessage());
            show_404();
        }
    }

    // ========================================== //
    // MODIFIER UN MEMBRE                         //
    // ========================================== //
    public function modifier($id) {
        if (!$this->rbac->hasPrivilege('tontine_membres', 'can_edit')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'tontine');
        $this->session->set_userdata('sub_menu', 'admin/tontine_membres');

        try {
            $data['membre'] = $this->tontine_membres_model->get_membre($id);
            if (empty($data['membre'])) {
                show_404();
            }

            if ($this->input->post()) {
                $this->form_validation->set_rules('nom', 'Nom', 'required');
                $this->form_validation->set_rules('prenom', 'Prénom', 'required');
                $this->form_validation->set_rules('telephone', 'Téléphone', 'required');

                if ($this->form_validation->run() == TRUE) {
                    $update_data = array(
                        'nom' => $this->input->post('nom'),
                        'prenom' => $this->input->post('prenom'),
                        'telephone' => $this->input->post('telephone'),
                        'email' => $this->input->post('email'),
                        'adresse' => $this->input->post('adresse'),
                        'profession' => $this->input->post('profession'),
                        'groupe_id' => $this->input->post('groupe_id'),
                        'statut' => $this->input->post('statut'),
                        'updated_at' => date('Y-m-d H:i:s')
                    );

                    if ($this->tontine_membres_model->mettre_a_jour($id, $update_data)) {
                        $this->session->set_flashdata('success', 'Membre modifié avec succès');
                        redirect('admin/tontine_membres');
                    } else {
                        $this->session->set_flashdata('error', 'Erreur lors de la modification');
                    }
                }
            }

            $data['groupes'] = $this->tontine_groupes_model->get_all();
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/tontine_membres_modifier', $data);
            $this->load->view('layout/footer');

        } catch (Exception $e) {
            log_message('error', 'Erreur dans Tontine_membres::modifier - ' . $e->getMessage());
            show_404();
        }
    }

    // ========================================== //
    // SUPPRIMER UN MEMBRE                        //
    // ========================================== //
    public function supprimer($id) {
        if (!$this->rbac->hasPrivilege('tontine_membres', 'can_delete')) {
            access_denied();
        }

        try {
            if ($this->tontine_membres_model->supprimer($id)) {
                $this->session->set_flashdata('success', 'Membre supprimé avec succès');
            } else {
                $this->session->set_flashdata('error', 'Erreur lors de la suppression');
            }
        } catch (Exception $e) {
            log_message('error', 'Erreur dans Tontine_membres::supprimer - ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Une erreur est survenue');
        }

        redirect('admin/tontine_membres');
    }

    // ========================================== //
    // EXPORTER LES MEMBRES                       //
    // ========================================== //
    public function exporter() {
        if (!$this->rbac->hasPrivilege('tontine_membres', 'can_view')) {
            access_denied();
        }

        try {
            $membres = $this->tontine_membres_model->get_membres();

            // Création du fichier CSV
            $filename = 'membres_tontine_' . date('Y-m-d') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            $output = fopen('php://output', 'w');
            fputcsv($output, array('ID', 'Nom', 'Prénom', 'Téléphone', 'Email', 'Groupe', 'Statut', 'Date d\'adhésion'));

            foreach ($membres as $membre) {
                fputcsv($output, array(
                    $membre['id'],
                    $membre['nom'],
                    $membre['prenom'],
                    $membre['telephone'],
                    $membre['email'],
                    $membre['groupe_nom'] ?? '',
                    $membre['statut'],
                    $membre['date_adhesion']
                ));
            }
            fclose($output);
            exit;

        } catch (Exception $e) {
            log_message('error', 'Erreur dans Tontine_membres::exporter - ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Erreur lors de l\'exportation');
            redirect('admin/tontine_membres');
        }
    }
}