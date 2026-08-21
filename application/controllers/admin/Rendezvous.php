<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rendezvous extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("rendezvous_model");
    }

    // ========================================== //
    // INDEX - LISTE DES RENDEZ-VOUS              //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('rendez_vous', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/rendezvous');

        $data['rendezvous'] = $this->rendezvous_model->get_all();
        $data['stats'] = $this->rendezvous_model->get_stats();
        $data['statuses'] = $this->rendezvous_model->get_statuses();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/rendezvous', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // AJOUTER UN RENDEZ-VOUS (AJAX)              //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('rendez_vous', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('titre', 'Titre', 'required');
        $this->form_validation->set_rules('date_rendez_vous', 'Date', 'required');
        $this->form_validation->set_rules('heure_debut', 'Heure début', 'required');
        $this->form_validation->set_rules('heure_fin', 'Heure fin', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $rendezvous_data = array(
            'titre' => $this->input->post('titre'),
            'description' => $this->input->post('description'),
            'date_rendez_vous' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_rendez_vous'))),
            'heure_debut' => $this->input->post('heure_debut'),
            'heure_fin' => $this->input->post('heure_fin'),
            'lieu' => $this->input->post('lieu'),
            'participants' => $this->input->post('participants'),
            'couleur' => $this->input->post('couleur') ?? '#3b82f6',
            'statut' => $this->input->post('statut') ?? 'planifie',
            'rappel' => $this->input->post('rappel') ?? 0,
            'user_id' => $this->session->userdata('admin_id') ?? 1,
            'deleted' => 0
        );

        $rendezvous_id = $this->rendezvous_model->add($rendezvous_data);

        if ($rendezvous_id) {
            echo json_encode(['success' => true, 'message' => 'Rendez-vous ajouté avec succès', 'id' => $rendezvous_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du rendez-vous']);
        }
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UN RENDEZ-VOUS (AJAX) //
    // ========================================== //
    public function get_rendezvous_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('rendez_vous', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            return;
        }

        $data = $this->rendezvous_model->get_by_id($id);

        if ($data && !empty($data)) {
            $rendezvous = [
                'id' => (int)$data['id'],
                'titre' => (string)($data['titre'] ?? ''),
                'description' => (string)($data['description'] ?? ''),
                'date_rendez_vous' => (string)($data['date_rendez_vous'] ?? ''),
                'heure_debut' => (string)($data['heure_debut'] ?? ''),
                'heure_fin' => (string)($data['heure_fin'] ?? ''),
                'lieu' => (string)($data['lieu'] ?? ''),
                'participants' => (string)($data['participants'] ?? ''),
                'couleur' => (string)($data['couleur'] ?? '#3b82f6'),
                'statut' => (string)($data['statut'] ?? 'planifie'),
                'rappel' => (int)($data['rappel'] ?? 0)
            ];

            echo json_encode([
                'success' => true,
                'rendezvous' => $rendezvous
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Rendez-vous non trouvé']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UN RENDEZ-VOUS (AJAX)        //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('rendez_vous', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('titre', 'Titre', 'required');
        $this->form_validation->set_rules('date_rendez_vous', 'Date', 'required');
        $this->form_validation->set_rules('heure_debut', 'Heure début', 'required');
        $this->form_validation->set_rules('heure_fin', 'Heure fin', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $rendezvous_data = array(
            'titre' => $this->input->post('titre'),
            'description' => $this->input->post('description'),
            'date_rendez_vous' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_rendez_vous'))),
            'heure_debut' => $this->input->post('heure_debut'),
            'heure_fin' => $this->input->post('heure_fin'),
            'lieu' => $this->input->post('lieu'),
            'participants' => $this->input->post('participants'),
            'couleur' => $this->input->post('couleur') ?? '#3b82f6',
            'statut' => $this->input->post('statut') ?? 'planifie',
            'rappel' => $this->input->post('rappel') ?? 0
        );

        $result = $this->rendezvous_model->update($id, $rendezvous_data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Rendez-vous mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // SUPPRESSION D'UN RENDEZ-VOUS               //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('rendez_vous', 'can_delete')) {
            access_denied();
        }

        $this->rendezvous_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/rendezvous');
    }

    // ========================================== //
    // DETAILS D'UN RENDEZ-VOUS (MODAL)           //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('rendez_vous', 'can_view')) {
            access_denied();
        }

        $data['rendezvous'] = $this->rendezvous_model->get_by_id($id);
        $this->load->view('admin/frontoffice/rendezvous_details', $data);
    }

    // ========================================== //
    // EXPORT EXCEL (CSV)                         //
    // ========================================== //
    public function export_excel() {
        $statut = $this->input->get('statut');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $data = $this->rendezvous_model->get_filtered($statut, $date_from, $date_to);

        $filename = 'rendezvous_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, ['#', 'Titre', 'Description', 'Date', 'Heure début', 'Heure fin', 'Lieu', 'Participants', 'Statut']);

        $statusLabels = $this->rendezvous_model->get_statuses();
        $i = 1;
        foreach ($data as $item) {
            fputcsv($output, [
                $i++,
                $item['titre'] ?? '',
                $item['description'] ?? '',
                !empty($item['date_rendez_vous']) ? date('d/m/Y', strtotime($item['date_rendez_vous'])) : '',
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

        $data['rendezvous'] = $this->rendezvous_model->get_filtered($statut, $date_from, $date_to);
        $data['title'] = 'Liste des rendez-vous';
        $data['date_generated'] = date('d/m/Y H:i');
        $data['stats'] = $this->rendezvous_model->get_stats();

        $html = $this->load->view('admin/frontoffice/rendezvous_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('rendezvous_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('rendezvous_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }
}