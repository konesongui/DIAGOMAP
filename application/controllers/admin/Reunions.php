<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reunions extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("reunions_model");
    }

    // ========================================== //
    // INDEX - LISTE DES RÉUNIONS                 //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('reunions', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/reunions');

        $data['reunions'] = $this->reunions_model->get_all();
        $data['stats'] = $this->reunions_model->get_stats();
        $data['statuses'] = $this->reunions_model->get_statuses();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/reunions', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // AJOUTER UNE RÉUNION (AJAX)                 //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('reunions', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('titre', 'Titre', 'required');
        $this->form_validation->set_rules('date_reunion', 'Date', 'required');
        $this->form_validation->set_rules('heure_debut', 'Heure début', 'required');
        $this->form_validation->set_rules('heure_fin', 'Heure fin', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $reunion_data = array(
            'titre' => $this->input->post('titre'),
            'description' => $this->input->post('description'),
            'date_reunion' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_reunion'))),
            'heure_debut' => $this->input->post('heure_debut'),
            'heure_fin' => $this->input->post('heure_fin'),
            'lieu' => $this->input->post('lieu'),
            'participants' => $this->input->post('participants'),
            'ordre_du_jour' => $this->input->post('ordre_du_jour'),
            'compte_rendu' => $this->input->post('compte_rendu'),
            'couleur' => $this->input->post('couleur') ?? '#8b5cf6',
            'statut' => $this->input->post('statut') ?? 'planifiee',
            'user_id' => $this->session->userdata('admin_id') ?? 1,
            'deleted' => 0
        );

        $reunion_id = $this->reunions_model->add($reunion_data);

        if ($reunion_id) {
            echo json_encode(['success' => true, 'message' => 'Réunion ajoutée avec succès', 'id' => $reunion_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout de la réunion']);
        }
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UNE RÉUNION (AJAX)//
    // ========================================== //
    public function get_reunion_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('reunions', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            return;
        }

        $data = $this->reunions_model->get_by_id($id);

        if ($data && !empty($data)) {
            $reunion = [
                'id' => (int)$data['id'],
                'titre' => (string)($data['titre'] ?? ''),
                'description' => (string)($data['description'] ?? ''),
                'date_reunion' => (string)($data['date_reunion'] ?? ''),
                'heure_debut' => (string)($data['heure_debut'] ?? ''),
                'heure_fin' => (string)($data['heure_fin'] ?? ''),
                'lieu' => (string)($data['lieu'] ?? ''),
                'participants' => (string)($data['participants'] ?? ''),
                'ordre_du_jour' => (string)($data['ordre_du_jour'] ?? ''),
                'compte_rendu' => (string)($data['compte_rendu'] ?? ''),
                'couleur' => (string)($data['couleur'] ?? '#8b5cf6'),
                'statut' => (string)($data['statut'] ?? 'planifiee')
            ];

            echo json_encode([
                'success' => true,
                'reunion' => $reunion
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Réunion non trouvée']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UNE RÉUNION (AJAX)           //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('reunions', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('titre', 'Titre', 'required');
        $this->form_validation->set_rules('date_reunion', 'Date', 'required');
        $this->form_validation->set_rules('heure_debut', 'Heure début', 'required');
        $this->form_validation->set_rules('heure_fin', 'Heure fin', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $reunion_data = array(
            'titre' => $this->input->post('titre'),
            'description' => $this->input->post('description'),
            'date_reunion' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_reunion'))),
            'heure_debut' => $this->input->post('heure_debut'),
            'heure_fin' => $this->input->post('heure_fin'),
            'lieu' => $this->input->post('lieu'),
            'participants' => $this->input->post('participants'),
            'ordre_du_jour' => $this->input->post('ordre_du_jour'),
            'compte_rendu' => $this->input->post('compte_rendu'),
            'couleur' => $this->input->post('couleur') ?? '#8b5cf6',
            'statut' => $this->input->post('statut') ?? 'planifiee'
        );

        $result = $this->reunions_model->update($id, $reunion_data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Réunion mise à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // SUPPRESSION D'UNE RÉUNION                  //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('reunions', 'can_delete')) {
            access_denied();
        }

        $this->reunions_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/reunions');
    }

    // ========================================== //
    // DETAILS D'UNE RÉUNION (MODAL)              //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('reunions', 'can_view')) {
            access_denied();
        }

        $data['reunion'] = $this->reunions_model->get_by_id($id);
        $this->load->view('admin/frontoffice/reunions_details', $data);
    }

    // ========================================== //
    // EXPORT EXCEL (CSV)                         //
    // ========================================== //
    public function export_excel() {
        $statut = $this->input->get('statut');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $data = $this->reunions_model->get_filtered($statut, $date_from, $date_to);

        $filename = 'reunions_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, ['#', 'Titre', 'Description', 'Date', 'Heure début', 'Heure fin', 'Lieu', 'Participants', 'Statut']);

        $statusLabels = $this->reunions_model->get_statuses();
        $i = 1;
        foreach ($data as $item) {
            fputcsv($output, [
                $i++,
                $item['titre'] ?? '',
                $item['description'] ?? '',
                !empty($item['date_reunion']) ? date('d/m/Y', strtotime($item['date_reunion'])) : '',
                $item['heure_debut'] ?? '',
                $item['heure_fin'] ?? '',
                $item['lieu'] ?? '',
                $item['participants'] ?? '',
                $statusLabels[$item['statut']] ?? $item['statut']
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
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $data['reunions'] = $this->reunions_model->get_filtered($statut, $date_from, $date_to);
        $data['title'] = 'Liste des réunions';
        $data['date_generated'] = date('d/m/Y H:i');
        $data['stats'] = $this->reunions_model->get_stats();

        $html = $this->load->view('admin/frontoffice/reunions_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('reunions_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('reunions_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }
}