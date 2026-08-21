<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Evenements extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("evenements_model");
        $this->upload_path = "./uploads/evenements/";
    }

    // ========================================== //
    // INDEX - LISTE DES ÉVÉNEMENTS               //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('evenements', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'eglise');
        $this->session->set_userdata('sub_menu', 'admin/evenements');

        $data['evenements'] = $this->evenements_model->get_all();
        $data['stats'] = $this->evenements_model->get_stats();
        $data['types'] = $this->evenements_model->get_types();
        $data['statuses'] = $this->evenements_model->get_statuses();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/evenements', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // AJOUTER UN ÉVÉNEMENT (AJAX)                //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('evenements', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('titre', 'Titre', 'required');
        $this->form_validation->set_rules('date_debut', 'Date début', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = array(
            'titre' => $this->input->post('titre'),
            'description' => $this->input->post('description'),
            'type_evenement' => $this->input->post('type_evenement') ?? 'culte',
            'date_debut' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_debut'))),
            'date_fin' => !empty($this->input->post('date_fin')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_fin'))) : null,
            'heure_debut' => $this->input->post('heure_debut'),
            'heure_fin' => $this->input->post('heure_fin'),
            'lieu' => $this->input->post('lieu'),
            'adresse' => $this->input->post('adresse'),
            'organisateur' => $this->input->post('organisateur'),
            'contact_organisateur' => $this->input->post('contact_organisateur'),
            'email_organisateur' => $this->input->post('email_organisateur'),
            'nombre_participants' => $this->input->post('nombre_participants') ?? 0,
            'participants' => $this->input->post('participants'),
            'couleur' => $this->input->post('couleur') ?? '#3b82f6',
            'statut' => $this->input->post('statut') ?? 'planifie',
            'user_id' => $this->session->userdata('admin_id') ?? 1,
            'deleted' => 0
        );

        $id = $this->evenements_model->add($data);

        if ($id) {
            // Gestion de l'image
            if (isset($_FILES["image"]) && !empty($_FILES['image']['name'])) {
                $this->upload_image($id);
            }

            echo json_encode(['success' => true, 'message' => 'Événement ajouté avec succès', 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
        }
    }

    // ========================================== //
    // UPLOAD DE L'IMAGE                          //
    // ========================================== //
    private function upload_image($evenement_id, $update = false) {
        if (!isset($_FILES["image"]) || empty($_FILES['image']['name'])) {
            return false;
        }

        if (!is_dir($this->upload_path)) {
            mkdir($this->upload_path, 0777, true);
        }

        $file = $_FILES["image"];
        $fileInfo = pathinfo($file["name"]);
        $extension = strtolower($fileInfo['extension']);
        $filename = 'evenement_' . $evenement_id . '_' . time() . '.' . $extension;

        if (move_uploaded_file($file["tmp_name"], $this->upload_path . $filename)) {
            if ($update) {
                $old_image = $this->evenements_model->get_by_id($evenement_id);
                if ($old_image && !empty($old_image['image'])) {
                    $old_path = $this->upload_path . $old_image['image'];
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
            }

            $this->evenements_model->update($evenement_id, array('image' => $filename));
            return true;
        }
        return false;
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UN ÉVÉNEMENT (AJAX)//
    // ========================================== //
    public function get_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('evenements', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $data = $this->evenements_model->get_by_id($id);

        if ($data) {
            echo json_encode(['success' => true, 'evenement' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Événement non trouvé']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UN ÉVÉNEMENT (AJAX)          //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('evenements', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('titre', 'Titre', 'required');
        $this->form_validation->set_rules('date_debut', 'Date début', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = array(
            'titre' => $this->input->post('titre'),
            'description' => $this->input->post('description'),
            'type_evenement' => $this->input->post('type_evenement') ?? 'culte',
            'date_debut' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_debut'))),
            'date_fin' => !empty($this->input->post('date_fin')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_fin'))) : null,
            'heure_debut' => $this->input->post('heure_debut'),
            'heure_fin' => $this->input->post('heure_fin'),
            'lieu' => $this->input->post('lieu'),
            'adresse' => $this->input->post('adresse'),
            'organisateur' => $this->input->post('organisateur'),
            'contact_organisateur' => $this->input->post('contact_organisateur'),
            'email_organisateur' => $this->input->post('email_organisateur'),
            'nombre_participants' => $this->input->post('nombre_participants') ?? 0,
            'participants' => $this->input->post('participants'),
            'couleur' => $this->input->post('couleur') ?? '#3b82f6',
            'statut' => $this->input->post('statut') ?? 'planifie'
        );

        // Gestion de l'image
        if (isset($_FILES["image"]) && !empty($_FILES['image']['name'])) {
            $this->upload_image($id, true);
        }

        $result = $this->evenements_model->update($id, $data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Événement mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('evenements', 'can_delete')) {
            access_denied();
        }

        $this->evenements_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/evenements');
    }

    // ========================================== //
    // DETAILS D'UN ÉVÉNEMENT (MODAL)             //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('evenements', 'can_view')) {
            access_denied();
        }

        $data['evenement'] = $this->evenements_model->get_by_id($id);
        $this->load->view('admin/frontoffice/evenements_details', $data);
    }

    // ========================================== //
    // EXPORT EXCEL                               //
    // ========================================== //
    public function export_excel() {
        $type = $this->input->get('type');
        $statut = $this->input->get('statut');

        $data = $this->evenements_model->get_filtered($type, $statut);

        $filename = 'evenements_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'Titre', 'Type', 'Date début', 'Date fin', 'Heure début', 'Heure fin',
            'Lieu', 'Organisateur', 'Contact', 'Participants', 'Statut', 'Description'
        ]);

        $typeLabels = $this->evenements_model->get_types();
        $statusLabels = $this->evenements_model->get_statuses();

        foreach ($data as $item) {
            fputcsv($output, [
                $item['titre'] ?? '',
                $typeLabels[$item['type_evenement']] ?? $item['type_evenement'],
                !empty($item['date_debut']) ? date('d/m/Y', strtotime($item['date_debut'])) : '',
                !empty($item['date_fin']) ? date('d/m/Y', strtotime($item['date_fin'])) : '',
                $item['heure_debut'] ?? '',
                $item['heure_fin'] ?? '',
                $item['lieu'] ?? '',
                $item['organisateur'] ?? '',
                $item['contact_organisateur'] ?? '',
                $item['nombre_participants'] ?? 0,
                $statusLabels[$item['statut']] ?? $item['statut'],
                $item['description'] ?? ''
            ]);
        }

        fclose($output);
        exit;
    }

    // ========================================== //
    // EXPORT PDF                                 //
    // ========================================== //
    public function export_pdf() {
        $type = $this->input->get('type');
        $statut = $this->input->get('statut');

        $data['evenements'] = $this->evenements_model->get_filtered($type, $statut);
        $data['title'] = 'Liste des événements';
        $data['date_generated'] = date('d/m/Y H:i');
        $data['stats'] = $this->evenements_model->get_stats();

        $html = $this->load->view('admin/frontoffice/evenements_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('evenements_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('evenements_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }
}