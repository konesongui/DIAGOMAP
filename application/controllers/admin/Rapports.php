<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rapports extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("rapports_model");
        $this->upload_path = "./uploads/front_office/rapports/";
    }

    // ========================================== //
    // INDEX - LISTE DES RAPPORTS                 //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('rapports', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/rapports');

        $data['rapports'] = $this->rapports_model->get_all();
        $data['stats'] = $this->rapports_model->get_stats();
        $data['types'] = $this->rapports_model->get_types();
        $data['statuses'] = $this->rapports_model->get_statuses();
        $data['priorities'] = $this->rapports_model->get_priorities();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/rapports', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // AJOUTER UN RAPPORT (AJAX)                  //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('rapports', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('titre', 'Titre', 'required');
        $this->form_validation->set_rules('type_rapport', 'Type de rapport', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $rapport_data = array(
            'titre' => $this->input->post('titre'),
            'type_rapport' => $this->input->post('type_rapport'),
            'description' => $this->input->post('description'),
            'statut' => $this->input->post('statut') ?? 'en_attente',
            'priorite' => $this->input->post('priorite') ?? 'normale',
            'periode_debut' => !empty($this->input->post('periode_debut')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('periode_debut'))) : null,
            'periode_fin' => !empty($this->input->post('periode_fin')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('periode_fin'))) : null,
            'user_id' => $this->session->userdata('admin_id') ?? 1,
            'deleted' => 0
        );

        $rapport_id = $this->rapports_model->add($rapport_data);

        if ($rapport_id) {
            // Gestion du fichier
            if (isset($_FILES["fichier"]) && !empty($_FILES['fichier']['name'])) {
                $this->upload_file($rapport_id);
            }

            echo json_encode(['success' => true, 'message' => 'Rapport ajouté avec succès', 'id' => $rapport_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du rapport']);
        }
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UN RAPPORT (AJAX) //
    // ========================================== //
    public function get_rapport_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('rapports', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            return;
        }

        $data = $this->rapports_model->get_by_id($id);

        if ($data && !empty($data)) {
            $rapport = [
                'id' => (int)$data['id'],
                'titre' => (string)($data['titre'] ?? ''),
                'type_rapport' => (string)($data['type_rapport'] ?? ''),
                'description' => (string)($data['description'] ?? ''),
                'statut' => (string)($data['statut'] ?? 'en_attente'),
                'priorite' => (string)($data['priorite'] ?? 'normale'),
                'periode_debut' => (string)($data['periode_debut'] ?? ''),
                'periode_fin' => (string)($data['periode_fin'] ?? ''),
                'fichier' => (string)($data['fichier'] ?? '')
            ];

            echo json_encode([
                'success' => true,
                'rapport' => $rapport
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Rapport non trouvé']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UN RAPPORT (AJAX)            //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('rapports', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('titre', 'Titre', 'required');
        $this->form_validation->set_rules('type_rapport', 'Type de rapport', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $rapport_data = array(
            'titre' => $this->input->post('titre'),
            'type_rapport' => $this->input->post('type_rapport'),
            'description' => $this->input->post('description'),
            'statut' => $this->input->post('statut') ?? 'en_attente',
            'priorite' => $this->input->post('priorite') ?? 'normale',
            'periode_debut' => !empty($this->input->post('periode_debut')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('periode_debut'))) : null,
            'periode_fin' => !empty($this->input->post('periode_fin')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('periode_fin'))) : null
        );

        // Gestion du fichier
        if (isset($_FILES["fichier"]) && !empty($_FILES['fichier']['name'])) {
            $this->upload_file($id, true);
        }

        $result = $this->rapports_model->update($id, $rapport_data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Rapport mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // SUPPRESSION D'UN RAPPORT                   //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('rapports', 'can_delete')) {
            access_denied();
        }

        $this->rapports_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/rapports');
    }

    // ========================================== //
    // TÉLÉCHARGER UN FICHIER                     //
    // ========================================== //
    public function download($filename) {
        $filepath = $this->upload_path . $filename;

        if (file_exists($filepath)) {
            $this->load->helper('download');
            $data = file_get_contents($filepath);
            force_download($filename, $data);
        } else {
            show_404();
        }
    }

    // ========================================== //
    // DETAILS D'UN RAPPORT (MODAL)               //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('rapports', 'can_view')) {
            access_denied();
        }

        $data['rapport'] = $this->rapports_model->get_by_id($id);
        $this->load->view('admin/frontoffice/rapports_details', $data);
    }

    // ========================================== //
    // UPLOAD DE FICHIER                          //
    // ========================================== //
    private function upload_file($rapport_id, $update = false) {
        if (!isset($_FILES["fichier"]) || empty($_FILES['fichier']['name'])) {
            return false;
        }

        // Créer le dossier s'il n'existe pas
        if (!is_dir($this->upload_path)) {
            mkdir($this->upload_path, 0777, true);
        }

        $file = $_FILES["fichier"];
        $fileInfo = pathinfo($file["name"]);
        $extension = strtolower($fileInfo['extension']);
        $filename = 'rapport_' . $rapport_id . '_' . time() . '.' . $extension;
        $file_size = $file["size"];
        $file_type = $extension;

        if (move_uploaded_file($file["tmp_name"], $this->upload_path . $filename)) {
            if ($update) {
                // Supprimer l'ancien fichier
                $old_file = $this->rapports_model->get_by_id($rapport_id);
                if ($old_file && !empty($old_file['fichier'])) {
                    $old_path = $this->upload_path . $old_file['fichier'];
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
            }

            // Mettre à jour les informations du fichier
            $data = array(
                'fichier' => $filename,
                'type_fichier' => $file_type,
                'taille' => $file_size
            );
            $this->rapports_model->update($rapport_id, $data);
            return true;
        }
        return false;
    }

    // ========================================== //
    // EXPORT EXCEL (CSV)                         //
    // ========================================== //
    public function export_excel() {
        $type_rapport = $this->input->get('type_rapport');
        $statut = $this->input->get('statut');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $data = $this->rapports_model->get_filtered($type_rapport, $statut, $date_from, $date_to);

        $filename = 'rapports_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, ['#', 'Titre', 'Type', 'Description', 'Statut', 'Priorité', 'Période', 'Date création']);

        $statusLabels = $this->rapports_model->get_statuses();
        $priorityLabels = $this->rapports_model->get_priorities();

        $i = 1;
        foreach ($data as $item) {
            fputcsv($output, [
                $i++,
                $item['titre'] ?? '',
                $this->rapports_model->get_type_label($item['type_rapport']),
                $item['description'] ?? '',
                $statusLabels[$item['statut']] ?? $item['statut'],
                $priorityLabels[$item['priorite']] ?? $item['priorite'],
                !empty($item['periode_debut']) && !empty($item['periode_fin']) ?
                    date('d/m/Y', strtotime($item['periode_debut'])) . ' - ' . date('d/m/Y', strtotime($item['periode_fin'])) : '',
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
        $type_rapport = $this->input->get('type_rapport');
        $statut = $this->input->get('statut');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $data['rapports'] = $this->rapports_model->get_filtered($type_rapport, $statut, $date_from, $date_to);
        $data['title'] = 'Liste des rapports';
        $data['date_generated'] = date('d/m/Y H:i');
        $data['stats'] = $this->rapports_model->get_stats();
        $data['statuses'] = $this->rapports_model->get_statuses();
        $data['priorities'] = $this->rapports_model->get_priorities();

        $html = $this->load->view('admin/frontoffice/rapports_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('rapports_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('rapports_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }
}