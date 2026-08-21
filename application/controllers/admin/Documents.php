<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Documents extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("documents_model");
        $this->upload_path = "./uploads/front_office/documents/";
    }

    // ========================================== //
    // INDEX - LISTE DES DOCUMENTS                //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('documents', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/documents');

        $data['documents'] = $this->documents_model->get_all();
        $data['stats'] = $this->documents_model->get_stats();
        $data['categories'] = $this->documents_model->get_categories();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/documents', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // AJOUTER UN DOCUMENT (AJAX)                 //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('documents', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('titre', 'Titre', 'required');
        $this->form_validation->set_rules('categorie', 'Catégorie', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $document_data = array(
            'titre' => $this->input->post('titre'),
            'categorie' => $this->input->post('categorie'),
            'description' => $this->input->post('description'),
            'statut' => $this->input->post('statut') ?? 'actif',
            'user_id' => $this->session->userdata('admin_id') ?? 1,
            'deleted' => 0
        );

        $document_id = $this->documents_model->add($document_data);

        if ($document_id) {
            // Gestion du fichier
            if (isset($_FILES["fichier"]) && !empty($_FILES['fichier']['name'])) {
                $this->upload_file($document_id);
            }

            echo json_encode(['success' => true, 'message' => 'Document ajouté avec succès', 'id' => $document_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du document']);
        }
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UN DOCUMENT (AJAX)//
    // ========================================== //
    public function get_document_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('documents', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            return;
        }

        $data = $this->documents_model->get_by_id($id);

        if ($data && !empty($data)) {
            $document = [
                'id' => (int)$data['id'],
                'titre' => (string)($data['titre'] ?? ''),
                'categorie' => (string)($data['categorie'] ?? ''),
                'description' => (string)($data['description'] ?? ''),
                'statut' => (string)($data['statut'] ?? 'actif'),
                'fichier' => (string)($data['fichier'] ?? '')
            ];

            echo json_encode([
                'success' => true,
                'document' => $document
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Document non trouvé']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UN DOCUMENT (AJAX)           //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('documents', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('titre', 'Titre', 'required');
        $this->form_validation->set_rules('categorie', 'Catégorie', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $document_data = array(
            'titre' => $this->input->post('titre'),
            'categorie' => $this->input->post('categorie'),
            'description' => $this->input->post('description'),
            'statut' => $this->input->post('statut') ?? 'actif'
        );

        // Gestion du fichier
        if (isset($_FILES["fichier"]) && !empty($_FILES['fichier']['name'])) {
            $this->upload_file($id, true);
        }

        $result = $this->documents_model->update($id, $document_data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Document mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // SUPPRESSION D'UN DOCUMENT                  //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('documents', 'can_delete')) {
            access_denied();
        }

        $this->documents_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/documents');
    }

    // ========================================== //
    // TÉLÉCHARGER UN DOCUMENT                    //
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
    // DETAILS D'UN DOCUMENT (MODAL)              //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('documents', 'can_view')) {
            access_denied();
        }

        $data['document'] = $this->documents_model->get_by_id($id);
        $this->load->view('admin/frontoffice/document_details', $data);
    }

    // ========================================== //
    // UPLOAD DE FICHIER                          //
    // ========================================== //
    private function upload_file($document_id, $update = false) {
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
        $filename = 'doc_' . $document_id . '_' . time() . '.' . $extension;
        $file_size = $file["size"];
        $file_type = $extension;

        if (move_uploaded_file($file["tmp_name"], $this->upload_path . $filename)) {
            if ($update) {
                // Supprimer l'ancien fichier
                $old_file = $this->documents_model->get_by_id($document_id);
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
            $this->documents_model->update($document_id, $data);
            return true;
        }
        return false;
    }

    // ========================================== //
    // EXPORT EXCEL (CSV)                         //
    // ========================================== //
    public function export_excel() {
        $categorie = $this->input->get('categorie');
        $statut = $this->input->get('statut');

        $data = $this->documents_model->get_filtered($categorie, $statut);

        $filename = 'documents_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, ['#', 'Titre', 'Catégorie', 'Description', 'Type', 'Taille', 'Statut', 'Date']);

        $statusLabels = [
            'actif' => 'Actif',
            'archive' => 'Archivé',
            'supprime' => 'Supprimé'
        ];

        $i = 1;
        foreach ($data as $item) {
            $size = $this->documents_model->format_size($item['taille'] ?? 0);
            fputcsv($output, [
                $i++,
                $item['titre'] ?? '',
                $this->documents_model->get_category_label($item['categorie']),
                $item['description'] ?? '',
                $item['type_fichier'] ?? '',
                $size,
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
        $categorie = $this->input->get('categorie');
        $statut = $this->input->get('statut');

        $data['documents'] = $this->documents_model->get_filtered($categorie, $statut);
        $data['title'] = 'Liste des documents';
        $data['date_generated'] = date('d/m/Y H:i');
        $data['stats'] = $this->documents_model->get_stats();

        $html = $this->load->view('admin/frontoffice/document_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('documents_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('documents_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }
}