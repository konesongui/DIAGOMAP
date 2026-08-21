<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Objectifs extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('designation_model');
        $this->load->model('staff_model');
        $this->load->model('Objectifs_model');   // notre nouveau modèle
        $this->load->helper('url');
    }

    // Affichage principal : liste des objectifs annuels + popup d'attributions
    function index() {
        if (!$this->rbac->hasPrivilege('clients', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'objectifs/index');

        $data['stff_list'] = $this->staff_model->get(); // pour la liste des commerciaux
        $data['annual_objectives'] = $this->Objectifs_model->getAnnualObjectives();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/objectifs/objectifsList', $data);
        $this->load->view('layout/footer', $data);
    }

    // AJOUTER un objectif annuel (directeur)
    public function create() {
        if (!$this->rbac->hasPrivilege('clients', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('amount', 'Montant', 'trim|required|numeric');
        $this->form_validation->set_rules('date', 'Date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            // En cas d'erreur, on recharge la vue avec les erreurs
            $data['stff_list'] = $this->staff_model->get();
            $data['annual_objectives'] = $this->Objectifs_model->getAnnualObjectives();
            $this->load->view('layout/header', $data);
            $this->load->view('admin/objectifs/objectifsList', $data);
            $this->load->view('layout/footer');
        } else {
            $data = array(
                'amount' => $this->input->post('amount'),
                'date'   => $this->input->post('date')
            );
            $this->Objectifs_model->addAnnualObjective($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Objectif annuel ajouté.</div>');
            redirect('admin/objectifs/index');
        }
    }

    // MODIFIER un objectif annuel
    public function edit($id) {
        if (!$this->rbac->hasPrivilege('clients', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('amount', 'Montant', 'trim|required|numeric');
        $this->form_validation->set_rules('date', 'Date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $data['objective'] = $this->Objectifs_model->getAnnualObjective($id);
            $data['stff_list'] = $this->staff_model->get();
            $data['annual_objectives'] = $this->Objectifs_model->getAnnualObjectives();
            $this->load->view('layout/header', $data);
            $this->load->view('admin/objectifs/objectifsEdit', $data);
            $this->load->view('layout/footer');
        } else {
            $data = array(
                'amount' => $this->input->post('amount'),
                'date'   => $this->input->post('date')
            );
            $this->Objectifs_model->updateAnnualObjective($id, $data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Objectif annuel modifié.</div>');
            redirect('admin/objectifs/index');
        }
    }

    // SUPPRIMER un objectif annuel (et ses attributions par CASCADE)
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('clients', 'can_delete')) {
            access_denied();
        }
        $this->Objectifs_model->deleteAnnualObjective($id);
        redirect('admin/objectifs/index');
    }

    // --- GESTION DES ATTRIBUTIONS (AJAX) ---
    public function get_assignments($annual_id) {
        $assignments = $this->Objectifs_model->getAssignments($annual_id);
        echo json_encode($assignments);
    }

    public function add_assignment() {
        $annual_id = $this->input->post('annual_objective_id');
        $new_amount = (float)$this->input->post('amount');

        // Récupérer le montant de l'objectif annuel
        $annual_obj = $this->Objectifs_model->getAnnualObjective($annual_id);
        if (!$annual_obj) {
            echo json_encode(['status' => 'error', 'message' => 'Objectif annuel introuvable.']);
            return;
        }
        $max_amount = (float)$annual_obj['amount'];

        // Calculer la somme des attributions existantes pour cet objectif
        $existing_assignments = $this->Objectifs_model->getAssignments($annual_id);
        $total_existing = array_sum(array_column($existing_assignments, 'amount'));

        if ($total_existing + $new_amount > $max_amount) {
            $remaining = $max_amount - $total_existing;
            echo json_encode(['status' => 'error', 'message' => "Le total des attributions dépasse l'objectif annuel ($max_amount FCFA). Il reste $remaining FCFA disponible."]);
            return;
        }

        $data = array(
            'annual_objective_id' => $annual_id,
            'commercial_name'     => $this->input->post('commercial_name'),
            'amount'              => $new_amount,
            'start_date'          => $this->input->post('start_date'),
            'end_date'            => $this->input->post('end_date')
        );
        $insert = $this->Objectifs_model->addAssignment($data);
        echo json_encode(['status' => $insert ? 'success' : 'error']);
    }

    public function add_assignment_180526() {
        $data = array(
            'annual_objective_id' => $this->input->post('annual_objective_id'),
            'commercial_name'     => $this->input->post('commercial_name'),
            'amount'              => $this->input->post('amount'),
            'start_date'          => $this->input->post('start_date'),
            'end_date'            => $this->input->post('end_date')
        );
        $insert = $this->Objectifs_model->addAssignment($data);
        echo json_encode(['status' => $insert ? 'success' : 'error']);
    }

    public function update_assignment() {
        $id = $this->input->post('id');
        $new_amount = (float)$this->input->post('amount');

        // Récupérer l'attribution existante
        $assignment = $this->Objectifs_model->getAssignment($id);
        if (!$assignment) {
            echo json_encode(['status' => 'error', 'message' => 'Attribution introuvable.']);
            return;
        }
        $annual_id = $assignment['annual_objective_id'];

        // Récupérer le montant annuel
        $annual_obj = $this->Objectifs_model->getAnnualObjective($annual_id);
        if (!$annual_obj) {
            echo json_encode(['status' => 'error', 'message' => 'Objectif annuel introuvable.']);
            return;
        }
        $max_amount = (float)$annual_obj['amount'];

        // Calculer la somme des autres attributions (sans celle en cours)
        $all_assignments = $this->Objectifs_model->getAssignments($annual_id);
        $total_others = 0;
        foreach ($all_assignments as $ass) {
            if ($ass['id'] != $id) {
                $total_others += $ass['amount'];
            }
        }

        if ($total_others + $new_amount > $max_amount) {
            $remaining = $max_amount - $total_others;
            echo json_encode(['status' => 'error', 'message' => "Le total des attributions dépasserait l'objectif annuel ($max_amount FCFA). Il reste $remaining FCFA disponible."]);
            return;
        }

        $data = array(
            'commercial_name' => $this->input->post('commercial_name'),
            'amount'          => $new_amount,
            'start_date'      => $this->input->post('start_date'),
            'end_date'        => $this->input->post('end_date')
        );
        $this->Objectifs_model->updateAssignment($id, $data);
        echo json_encode(['status' => 'success']);
    }

    public function update_assignment180526() {
        $id = $this->input->post('id');
        $data = array(
            'commercial_name' => $this->input->post('commercial_name'),
            'amount'          => $this->input->post('amount'),
            'start_date'      => $this->input->post('start_date'),
            'end_date'        => $this->input->post('end_date')
        );
        $this->Objectifs_model->updateAssignment($id, $data);
        echo json_encode(['status' => 'success']);
    }

    public function delete_assignment($id) {
        $this->Objectifs_model->deleteAssignment($id);
        echo json_encode(['status' => 'success']);
    }
}
?>