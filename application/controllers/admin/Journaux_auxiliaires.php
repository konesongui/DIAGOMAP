<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Journaux_auxiliaires extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("journaux_auxiliaires_model");
    }

    // ========================================== //
    // INDEX - LISTE DES JOURNAUX                 //
    // ========================================== //
    public function index() {
        $this->session->set_userdata('top_menu', 'finance');
        $this->session->set_userdata('sub_menu', 'admin/frontoffice/journaux_auxiliaires');

        $data['journaux'] = $this->journaux_auxiliaires_model->get_all();
        $data['stats'] = $this->journaux_auxiliaires_model->get_stats();
        $data['types'] = $this->journaux_auxiliaires_model->get_types();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/journaux_auxiliaires', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // AJOUTER UN JOURNAL (AJAX)                  //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        $this->form_validation->set_rules('code', 'Code', 'required|trim');
        $this->form_validation->set_rules('libelle', 'Libellé', 'required|trim');
        $this->form_validation->set_rules('type', 'Type', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        // Vérifier si le code existe déjà
        $code = strtoupper($this->input->post('code'));
        if ($this->journaux_auxiliaires_model->code_exists($code)) {
            echo json_encode(['success' => false, 'message' => 'Ce code de journal existe déjà']);
            return;
        }

        $data = array(
            'code' => $code,
            'libelle' => ucfirst($this->input->post('libelle')),
            'type' => $this->input->post('type'),
            'compte_contrepartie' => $this->input->post('compte_contrepartie'),
            'description' => $this->input->post('description'),
            'actif' => $this->input->post('actif') ? 1 : 0,
            'user_id' => $this->session->userdata('admin_id') ?? 1,
            'entreprise_id' => $this->session->userdata('entreprise_id') ?? null,
            'date_creation' => date('Y-m-d H:i:s')
        );

        $id = $this->journaux_auxiliaires_model->add($data);

        if ($id) {
            echo json_encode(['success' => true, 'message' => 'Journal ajouté avec succès', 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
        }
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UN JOURNAL         //
    // ========================================== //
    public function get_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        $data = $this->journaux_auxiliaires_model->get_by_id($id);

        if ($data) {
            echo json_encode(['success' => true, 'journal' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Journal non trouvé']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UN JOURNAL (AJAX)            //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        $id = $this->input->post('edit_id');

        if (empty($id) || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            return;
        }

        $this->form_validation->set_rules('code', 'Code', 'required|trim');
        $this->form_validation->set_rules('libelle', 'Libellé', 'required|trim');
        $this->form_validation->set_rules('type', 'Type', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $code = strtoupper($this->input->post('code'));

        // Vérifier si le code existe déjà (hors de l'élément en cours)
        if ($this->journaux_auxiliaires_model->code_exists($code, $id)) {
            echo json_encode(['success' => false, 'message' => 'Ce code de journal existe déjà']);
            return;
        }

        $data = array(
            'code' => $code,
            'libelle' => ucfirst($this->input->post('libelle')),
            'type' => $this->input->post('type'),
            'compte_contrepartie' => $this->input->post('compte_contrepartie'),
            'description' => $this->input->post('description'),
            'actif' => $this->input->post('actif') ? 1 : 0
        );

        $result = $this->journaux_auxiliaires_model->update($id, $data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Journal mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    public function delete($id) {
        $this->journaux_auxiliaires_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/frontoffice/journaux_auxiliaires');
    }

    // ========================================== //
    // VOIR LES ÉCRITURES D'UN JOURNAL            //
    // ========================================== //
    public function ecritures($id) {
        $data['journal'] = $this->journaux_auxiliaires_model->get_by_id($id);
        $data['ecritures'] = $this->journaux_auxiliaires_model->get_ecritures($id);
        $data['total_debit'] = $this->journaux_auxiliaires_model->sum_debit($id);
        $data['total_credit'] = $this->journaux_auxiliaires_model->sum_credit($id);

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/journaux_auxiliaires_ecritures', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // EXPORT EXCEL (CSV)                         //
    // ========================================== //
    public function export_excel() {
        $data = $this->journaux_auxiliaires_model->get_all();

        $filename = 'journaux_auxiliaires_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'Code', 'Libellé', 'Type', 'Compte contrepartie',
            'Description', 'Actif', 'Date création'
        ]);

        foreach ($data as $item) {
            fputcsv($output, [
                $item['code'] ?? '',
                $item['libelle'] ?? '',
                $item['type'] ?? '',
                $item['compte_contrepartie'] ?? '',
                $item['description'] ?? '',
                ($item['actif'] ?? 0) ? 'Oui' : 'Non',
                isset($item['date_creation']) ? date('d/m/Y', strtotime($item['date_creation'])) : ''
            ]);
        }

        fclose($output);
        exit;
    }
}