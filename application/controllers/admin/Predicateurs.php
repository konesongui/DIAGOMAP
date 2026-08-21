<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Predicateurs extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("predicateurs_model");
        $this->upload_path = "./uploads/predicateurs/";
    }

    // ========================================== //
    // INDEX - LISTE DES PRÉDICATEURS             //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('predicateurs', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'eglise');
        $this->session->set_userdata('sub_menu', 'admin/predicateurs');

        $data['predicateurs'] = $this->predicateurs_model->get_all();
        $data['stats'] = $this->predicateurs_model->get_stats();
        $data['statuses'] = $this->predicateurs_model->get_statuses();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/predicateurs', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // AJOUTER UN PRÉDICATEUR (AJAX)              //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('predicateurs', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('nom', 'Nom', 'required');
        $this->form_validation->set_rules('prenom', 'Prénom', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = array(
            'nom' => $this->input->post('nom'),
            'prenom' => $this->input->post('prenom'),
            'titre' => $this->input->post('titre'),
            'sexe' => $this->input->post('sexe') ?? 'M',
            'date_naissance' => !empty($this->input->post('date_naissance')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_naissance'))) : null,
            'telephone' => $this->input->post('telephone'),
            'email' => $this->input->post('email'),
            'adresse' => $this->input->post('adresse'),
            'biographie' => $this->input->post('biographie'),
            'specialite' => $this->input->post('specialite'),
            'annees_experience' => $this->input->post('annees_experience') ?? 0,
            'statut' => $this->input->post('statut') ?? 'actif',
            'date_ordination' => !empty($this->input->post('date_ordination')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_ordination'))) : null,
            'date_affiliation' => !empty($this->input->post('date_affiliation')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_affiliation'))) : null,
            'user_id' => $this->session->userdata('admin_id') ?? 1,
            'deleted' => 0
        );

        $id = $this->predicateurs_model->add($data);

        if ($id) {
            // Gestion de la photo
            if (isset($_FILES["photo"]) && !empty($_FILES['photo']['name'])) {
                $this->upload_photo($id);
            }

            echo json_encode(['success' => true, 'message' => 'Prédicateur ajouté avec succès', 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
        }
    }

    // ========================================== //
    // UPLOAD DE LA PHOTO                         //
    // ========================================== //
    private function upload_photo($predicateur_id, $update = false) {
        if (!isset($_FILES["photo"]) || empty($_FILES['photo']['name'])) {
            return false;
        }

        if (!is_dir($this->upload_path)) {
            mkdir($this->upload_path, 0777, true);
        }

        $file = $_FILES["photo"];
        $fileInfo = pathinfo($file["name"]);
        $extension = strtolower($fileInfo['extension']);
        $filename = 'predicateur_' . $predicateur_id . '_' . time() . '.' . $extension;

        if (move_uploaded_file($file["tmp_name"], $this->upload_path . $filename)) {
            if ($update) {
                $old_photo = $this->predicateurs_model->get_by_id($predicateur_id);
                if ($old_photo && !empty($old_photo['photo'])) {
                    $old_path = $this->upload_path . $old_photo['photo'];
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
            }

            $this->predicateurs_model->update($predicateur_id, array('photo' => $filename));
            return true;
        }
        return false;
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UN PRÉDICATEUR    //
    // ========================================== //
    public function get_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('predicateurs', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $data = $this->predicateurs_model->get_by_id($id);

        if ($data) {
            echo json_encode(['success' => true, 'predicateur' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Prédicateur non trouvé']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UN PRÉDICATEUR (AJAX)        //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('predicateurs', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('nom', 'Nom', 'required');
        $this->form_validation->set_rules('prenom', 'Prénom', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = array(
            'nom' => $this->input->post('nom'),
            'prenom' => $this->input->post('prenom'),
            'titre' => $this->input->post('titre'),
            'sexe' => $this->input->post('sexe') ?? 'M',
            'date_naissance' => !empty($this->input->post('date_naissance')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_naissance'))) : null,
            'telephone' => $this->input->post('telephone'),
            'email' => $this->input->post('email'),
            'adresse' => $this->input->post('adresse'),
            'biographie' => $this->input->post('biographie'),
            'specialite' => $this->input->post('specialite'),
            'annees_experience' => $this->input->post('annees_experience') ?? 0,
            'statut' => $this->input->post('statut') ?? 'actif',
            'date_ordination' => !empty($this->input->post('date_ordination')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_ordination'))) : null,
            'date_affiliation' => !empty($this->input->post('date_affiliation')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_affiliation'))) : null
        );

        // Gestion de la photo
        if (isset($_FILES["photo"]) && !empty($_FILES['photo']['name'])) {
            $this->upload_photo($id, true);
        }

        $result = $this->predicateurs_model->update($id, $data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Prédicateur mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('predicateurs', 'can_delete')) {
            access_denied();
        }

        $this->predicateurs_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/predicateurs');
    }

    // ========================================== //
    // DETAILS D'UN PRÉDICATEUR (MODAL)           //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('predicateurs', 'can_view')) {
            access_denied();
        }

        $data['predicateur'] = $this->predicateurs_model->get_by_id($id);
        $this->load->view('admin/frontoffice/predicateurs_details', $data);
    }

    // ========================================== //
    // EXPORT EXCEL                               //
    // ========================================== //
    public function export_excel() {
        $statut = $this->input->get('statut');
        $sexe = $this->input->get('sexe');

        $data = $this->predicateurs_model->get_filtered($statut, $sexe);

        $filename = 'predicateurs_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'Nom', 'Prénom', 'Titre', 'Sexe', 'Téléphone', 'Email',
            'Spécialité', 'Années d\'expérience', 'Statut', 'Biographie'
        ]);

        $statusLabels = $this->predicateurs_model->get_statuses();

        foreach ($data as $item) {
            fputcsv($output, [
                $item['nom'] ?? '',
                $item['prenom'] ?? '',
                $item['titre'] ?? '',
                $item['sexe'] == 'M' ? 'Homme' : 'Femme',
                $item['telephone'] ?? '',
                $item['email'] ?? '',
                $item['specialite'] ?? '',
                $item['annees_experience'] ?? 0,
                $statusLabels[$item['statut']] ?? $item['statut'],
                $item['biographie'] ?? ''
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
        $sexe = $this->input->get('sexe');

        $data['predicateurs'] = $this->predicateurs_model->get_filtered($statut, $sexe);
        $data['title'] = 'Liste des prédicateurs';
        $data['date_generated'] = date('d/m/Y H:i');
        $data['stats'] = $this->predicateurs_model->get_stats();

        $html = $this->load->view('admin/frontoffice/predicateurs_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('predicateurs_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('predicateurs_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }
}