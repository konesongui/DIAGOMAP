<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Membres extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("membres_model");
        $this->upload_path = "./uploads/membres/";
    }

    // ========================================== //
    // INDEX - LISTE DES MEMBRES                  //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('membres', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'eglise');
        $this->session->set_userdata('sub_menu', 'admin/membres');

        $data['membres'] = $this->membres_model->get_all();
        $data['stats'] = $this->membres_model->get_stats();
        $data['roles'] = $this->membres_model->get_roles();
        $data['statuses'] = $this->membres_model->get_statuses();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/membres', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // AJOUTER UN MEMBRE (AJAX)                   //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('membres', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('nom', 'Nom', 'required');
        $this->form_validation->set_rules('prenom', 'Prénom', 'required');
        $this->form_validation->set_rules('sexe', 'Sexe', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = array(
            'nom' => $this->input->post('nom'),
            'prenom' => $this->input->post('prenom'),
            'sexe' => $this->input->post('sexe'),
            'date_naissance' => !empty($this->input->post('date_naissance')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_naissance'))) : null,
            'lieu_naissance' => $this->input->post('lieu_naissance'),
            'nationalite' => $this->input->post('nationalite'),
            'profession' => $this->input->post('profession'),
            'adresse' => $this->input->post('adresse'),
            'telephone' => $this->input->post('telephone'),
            'email' => $this->input->post('email'),
            'date_bapteme' => !empty($this->input->post('date_bapteme')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_bapteme'))) : null,
            'date_affiliation' => !empty($this->input->post('date_affiliation')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_affiliation'))) : null,
            'statut_membre' => $this->input->post('statut_membre') ?? 'actif',
            'role' => $this->input->post('role') ?? 'membre',
            'departement' => $this->input->post('departement'),
            'groupe_cellule' => $this->input->post('groupe_cellule'),
            'nom_conjoint' => $this->input->post('nom_conjoint'),
            'nombre_enfants' => $this->input->post('nombre_enfants') ?? 0,
            'remarques' => $this->input->post('remarques'),
            'user_id' => $this->session->userdata('admin_id') ?? 1,
            'deleted' => 0
        );

        $id = $this->membres_model->add($data);

        if ($id) {
            // Gestion de la photo
            if (isset($_FILES["photo"]) && !empty($_FILES['photo']['name'])) {
                $this->upload_photo($id);
            }

            echo json_encode(['success' => true, 'message' => 'Membre ajouté avec succès', 'id' => $id, 'code' => $data['code_membre']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
        }
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UN MEMBRE (AJAX)   //
    // ========================================== //
    public function get_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('membres', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $data = $this->membres_model->get_by_id($id);

        if ($data) {
            echo json_encode(['success' => true, 'membre' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Membre non trouvé']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UN MEMBRE (AJAX)             //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('membres', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('nom', 'Nom', 'required');
        $this->form_validation->set_rules('prenom', 'Prénom', 'required');
        $this->form_validation->set_rules('sexe', 'Sexe', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = array(
            'nom' => $this->input->post('nom'),
            'prenom' => $this->input->post('prenom'),
            'sexe' => $this->input->post('sexe'),
            'date_naissance' => !empty($this->input->post('date_naissance')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_naissance'))) : null,
            'lieu_naissance' => $this->input->post('lieu_naissance'),
            'nationalite' => $this->input->post('nationalite'),
            'profession' => $this->input->post('profession'),
            'adresse' => $this->input->post('adresse'),
            'telephone' => $this->input->post('telephone'),
            'email' => $this->input->post('email'),
            'date_bapteme' => !empty($this->input->post('date_bapteme')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_bapteme'))) : null,
            'date_affiliation' => !empty($this->input->post('date_affiliation')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_affiliation'))) : null,
            'statut_membre' => $this->input->post('statut_membre') ?? 'actif',
            'role' => $this->input->post('role') ?? 'membre',
            'departement' => $this->input->post('departement'),
            'groupe_cellule' => $this->input->post('groupe_cellule'),
            'nom_conjoint' => $this->input->post('nom_conjoint'),
            'nombre_enfants' => $this->input->post('nombre_enfants') ?? 0,
            'remarques' => $this->input->post('remarques')
        );

        // Gestion de la photo
        if (isset($_FILES["photo"]) && !empty($_FILES['photo']['name'])) {
            $this->upload_photo($id, true);
        }

        $result = $this->membres_model->update($id, $data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Membre mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // UPLOAD DE PHOTO                            //
    // ========================================== //
    private function upload_photo($membre_id, $update = false) {
        if (!isset($_FILES["photo"]) || empty($_FILES['photo']['name'])) {
            return false;
        }

        if (!is_dir($this->upload_path)) {
            mkdir($this->upload_path, 0777, true);
        }

        $file = $_FILES["photo"];
        $fileInfo = pathinfo($file["name"]);
        $extension = strtolower($fileInfo['extension']);
        $filename = 'membre_' . $membre_id . '_' . time() . '.' . $extension;

        if (move_uploaded_file($file["tmp_name"], $this->upload_path . $filename)) {
            if ($update) {
                $old_photo = $this->membres_model->get_by_id($membre_id);
                if ($old_photo && !empty($old_photo['photo'])) {
                    $old_path = $this->upload_path . $old_photo['photo'];
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
            }

            $this->membres_model->update($membre_id, array('photo' => $filename));
            return true;
        }
        return false;
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('membres', 'can_delete')) {
            access_denied();
        }

        $this->membres_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/membres');
    }

    // ========================================== //
    // DETAILS D'UN MEMBRE (MODAL)                //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('membres', 'can_view')) {
            access_denied();
        }

        $data['membre'] = $this->membres_model->get_by_id($id);
        $this->load->view('admin/frontoffice/membres_details', $data);
    }

    // ========================================== //
    // EXPORT EXCEL                               //
    // ========================================== //
    public function export_excel() {
        $statut = $this->input->get('statut');
        $role = $this->input->get('role');
        $sexe = $this->input->get('sexe');

        $data = $this->membres_model->get_filtered($statut, $role, $sexe);

        $filename = 'membres_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'Code', 'Nom', 'Prénom', 'Sexe', 'Date naissance', 'Téléphone', 'Email',
            'Statut', 'Rôle', 'Département', 'Cellule', 'Baptême', 'Affiliation'
        ]);

        $statusLabels = $this->membres_model->get_statuses();
        $roleLabels = $this->membres_model->get_roles();

        foreach ($data as $item) {
            fputcsv($output, [
                $item['code_membre'] ?? '',
                $item['nom'] ?? '',
                $item['prenom'] ?? '',
                $item['sexe'] == 'M' ? 'Homme' : 'Femme',
                !empty($item['date_naissance']) ? date('d/m/Y', strtotime($item['date_naissance'])) : '',
                $item['telephone'] ?? '',
                $item['email'] ?? '',
                $statusLabels[$item['statut_membre']] ?? $item['statut_membre'],
                $roleLabels[$item['role']] ?? $item['role'],
                $item['departement'] ?? '',
                $item['groupe_cellule'] ?? '',
                !empty($item['date_bapteme']) ? date('d/m/Y', strtotime($item['date_bapteme'])) : '',
                !empty($item['date_affiliation']) ? date('d/m/Y', strtotime($item['date_affiliation'])) : ''
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
        $role = $this->input->get('role');
        $sexe = $this->input->get('sexe');

        $data['membres'] = $this->membres_model->get_filtered($statut, $role, $sexe);
        $data['title'] = 'Liste des membres';
        $data['date_generated'] = date('d/m/Y H:i');
        $data['stats'] = $this->membres_model->get_stats();

        $html = $this->load->view('admin/frontoffice/membres_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('membres_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('membres_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }
}