<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Groupes extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("groupes_model");
    }

    // ========================================== //
    // INDEX - LISTE DES GROUPES                  //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('groupes', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'eglise');
        $this->session->set_userdata('sub_menu', 'admin/groupes');

        $data['groupes'] = $this->groupes_model->get_all();
        $data['stats'] = $this->groupes_model->get_stats();
        $data['types'] = $this->groupes_model->get_types();
        $data['statuses'] = $this->groupes_model->get_statuses();
        $data['quartiers'] = $this->groupes_model->get_quartiers();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/groupes', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // AJOUTER UN GROUPE (AJAX)                   //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('groupes', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('nom', 'Nom du groupe', 'required');
        $this->form_validation->set_rules('type', 'Type', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = array(
            'nom' => $this->input->post('nom'),
            'type' => $this->input->post('type') ?? 'cellule',
            'description' => $this->input->post('description'),
            'responsable' => $this->input->post('responsable'),
            'jour_reunion' => $this->input->post('jour_reunion'),
            'heure_reunion' => $this->input->post('heure_reunion'),
            'lieu_reunion' => $this->input->post('lieu_reunion'),
            'quartier' => $this->input->post('quartier'),
            'nombre_membres' => $this->input->post('nombre_membres') ?? 0,
            'membres' => $this->input->post('membres'),
            'statut' => $this->input->post('statut') ?? 'actif',
            'user_id' => $this->session->userdata('admin_id') ?? 1,
            'deleted' => 0
        );

        $id = $this->groupes_model->add($data);

        if ($id) {
            echo json_encode(['success' => true, 'message' => 'Groupe ajouté avec succès', 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
        }
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UN GROUPE (AJAX)   //
    // ========================================== //
    public function get_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('groupes', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $data = $this->groupes_model->get_by_id($id);

        if ($data) {
            echo json_encode(['success' => true, 'groupe' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Groupe non trouvé']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UN GROUPE (AJAX)             //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('groupes', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('nom', 'Nom du groupe', 'required');
        $this->form_validation->set_rules('type', 'Type', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = array(
            'nom' => $this->input->post('nom'),
            'type' => $this->input->post('type') ?? 'cellule',
            'description' => $this->input->post('description'),
            'responsable' => $this->input->post('responsable'),
            'jour_reunion' => $this->input->post('jour_reunion'),
            'heure_reunion' => $this->input->post('heure_reunion'),
            'lieu_reunion' => $this->input->post('lieu_reunion'),
            'quartier' => $this->input->post('quartier'),
            'nombre_membres' => $this->input->post('nombre_membres') ?? 0,
            'membres' => $this->input->post('membres'),
            'statut' => $this->input->post('statut') ?? 'actif'
        );

        $result = $this->groupes_model->update($id, $data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Groupe mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('groupes', 'can_delete')) {
            access_denied();
        }

        $this->groupes_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/groupes');
    }

    // ========================================== //
    // DETAILS D'UN GROUPE (MODAL)                //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('groupes', 'can_view')) {
            access_denied();
        }

        $data['groupe'] = $this->groupes_model->get_by_id($id);
        $this->load->view('admin/frontoffice/groupes_details', $data);
    }

    // ========================================== //
    // EXPORT EXCEL                               //
    // ========================================== //
    public function export_excel() {
        $type = $this->input->get('type');
        $statut = $this->input->get('statut');

        $data = $this->groupes_model->get_filtered($type, $statut);

        $filename = 'groupes_cellules_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'Nom', 'Type', 'Responsable', 'Jour réunion', 'Heure', 'Lieu', 'Quartier',
            'Nombre membres', 'Statut', 'Description'
        ]);

        $typeLabels = $this->groupes_model->get_types();
        $statusLabels = $this->groupes_model->get_statuses();

        foreach ($data as $item) {
            fputcsv($output, [
                $item['nom'] ?? '',
                $typeLabels[$item['type']] ?? $item['type'],
                $item['responsable'] ?? '',
                $item['jour_reunion'] ?? '',
                $item['heure_reunion'] ?? '',
                $item['lieu_reunion'] ?? '',
                $item['quartier'] ?? '',
                $item['nombre_membres'] ?? 0,
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

        $data['groupes'] = $this->groupes_model->get_filtered($type, $statut);
        $data['title'] = 'Liste des groupes et cellules';
        $data['date_generated'] = date('d/m/Y H:i');
        $data['stats'] = $this->groupes_model->get_stats();

        $html = $this->load->view('admin/frontoffice/groupes_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('groupes_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('groupes_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }
}