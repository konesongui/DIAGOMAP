<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Demande extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("demande_model");
    }

    // ========================================== //
    // INDEX - LISTE DES DEMANDES                 //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('demandes', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/demande');

        $data['demandes'] = $this->demande_model->get_all();
        $data['stats'] = $this->demande_model->get_stats();
        $data['total_demandes'] = count($data['demandes']);

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/demande', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // AJOUTER UNE DEMANDE (AJAX)                 //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('demandes', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('titre', 'Titre', 'required');
        $this->form_validation->set_rules('categorie', 'Catégorie', 'required');
        $this->form_validation->set_rules('priorite', 'Priorité', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $demande_data = array(
            'titre' => $this->input->post('titre'),
            'categorie' => $this->input->post('categorie'),
            'priorite' => $this->input->post('priorite'),
            'description' => $this->input->post('description'),
            'statut' => $this->input->post('statut') ?? 'en_attente',
            'user_id' => $this->session->userdata('admin_id') ?? 1,
            'deleted' => 0
        );

        $demande_id = $this->demande_model->add($demande_data);

        if ($demande_id) {
            echo json_encode(['success' => true, 'message' => 'Demande soumise avec succès', 'id' => $demande_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout de la demande']);
        }
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UNE DEMANDE (AJAX) //
    // ========================================== //
    public function get_demande_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('demandes', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            return;
        }

        $data = $this->demande_model->get_by_id($id);

        if ($data && !empty($data)) {
            $demande = [
                'id' => (int)$data['id'],
                'titre' => (string)($data['titre'] ?? ''),
                'categorie' => (string)($data['categorie'] ?? ''),
                'priorite' => (string)($data['priorite'] ?? ''),
                'description' => (string)($data['description'] ?? ''),
                'statut' => (string)($data['statut'] ?? 'en_attente')
            ];

            echo json_encode([
                'success' => true,
                'demande' => $demande
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Demande non trouvée']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UNE DEMANDE (AJAX)           //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('demandes', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('titre', 'Titre', 'required');
        $this->form_validation->set_rules('categorie', 'Catégorie', 'required');
        $this->form_validation->set_rules('priorite', 'Priorité', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $demande_data = array(
            'titre' => $this->input->post('titre'),
            'categorie' => $this->input->post('categorie'),
            'priorite' => $this->input->post('priorite'),
            'description' => $this->input->post('description'),
            'statut' => $this->input->post('statut') ?? 'en_attente'
        );

        $result = $this->demande_model->update($id, $demande_data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Demande mise à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // SUPPRESSION D'UNE DEMANDE                  //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('demandes', 'can_delete')) {
            access_denied();
        }

        $this->demande_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/demande');
    }

    // ========================================== //
    // DETAILS D'UNE DEMANDE (MODAL)              //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('demandes', 'can_view')) {
            access_denied();
        }

        $data['demande'] = $this->demande_model->get_by_id($id);
        $this->load->view('admin/frontoffice/demande_details', $data);
    }

    // ========================================== //
    // EXPORT EXCEL (CSV)                         //
    // ========================================== //
    public function export_excel() {
        $statut = $this->input->get('statut');
        $categorie = $this->input->get('categorie');
        $priorite = $this->input->get('priorite');

        $data = $this->demande_model->get_filtered($statut, $categorie, $priorite);

        $filename = 'demandes_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, ['#', 'Titre', 'Catégorie', 'Priorité', 'Description', 'Statut', 'Date']);

        $statusLabels = [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'rejete' => 'Rejeté'
        ];
        $priorityLabels = [
            'basse' => 'Basse',
            'normale' => 'Normale',
            'haute' => 'Haute',
            'urgente' => 'Urgente'
        ];
        $categoryLabels = [
            'comptabilite' => 'Comptabilité',
            'ressources_humaines' => 'Ressources Humaines',
            'informatique' => 'Informatique',
            'logistique' => 'Logistique',
            'communication' => 'Communication',
            'autre' => 'Autre'
        ];

        $i = 1;
        foreach ($data as $item) {
            fputcsv($output, [
                $i++,
                $item['titre'] ?? '',
                $categoryLabels[$item['categorie']] ?? $item['categorie'],
                $priorityLabels[$item['priorite']] ?? $item['priorite'],
                $item['description'] ?? '',
                $statusLabels[$item['statut']] ?? $item['statut'],
                !empty($item['date_creation']) ? date('d/m/Y', strtotime($item['date_creation'])) : ''
            ]);
        }

        fclose($output);
        exit;
    }

    // ========================================== //
    // EXPORT PDF                                 //
    // ========================================== //
    public function export_pdf() {
        $statut = $this->input->get('statut');
        $categorie = $this->input->get('categorie');
        $priorite = $this->input->get('priorite');

        $data['demandes'] = $this->demande_model->get_filtered($statut, $categorie, $priorite);
        $data['title'] = 'Liste des demandes';
        $data['date_generated'] = date('d/m/Y H:i');
        $data['stats'] = $this->demande_model->get_stats();

        $html = $this->load->view('admin/frontoffice/demande_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('demandes_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('demandes_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }
}