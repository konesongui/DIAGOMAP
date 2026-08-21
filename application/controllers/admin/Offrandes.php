<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Offrandes extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("offrandes_model");
        $this->load->model("membres_model");
    }

    // ========================================== //
    // INDEX - LISTE DES OFFRANDES                //
    // ========================================== //
    public function index() {
        try {
            $data['offrandes'] = $this->offrandes_model->get_all() ?: array();
        } catch (Exception $e) {
            log_message('error', 'Erreur get_all: ' . $e->getMessage());
            $data['offrandes'] = array();
        }

        try {
            $data['stats'] = $this->offrandes_model->get_stats() ?: array();
        } catch (Exception $e) {
            log_message('error', 'Erreur get_stats: ' . $e->getMessage());
            $data['stats'] = array(
                'total' => 0,
                'total_montant' => 0,
                'today' => 0,
                'today_montant' => 0,
                'month' => 0,
                'month_montant' => 0
            );
        }

        $data['types'] = $this->offrandes_model->get_types();
        $data['modes'] = $this->offrandes_model->get_modes();
        $data['statuses'] = $this->offrandes_model->get_statuses();
        $data['membres'] = $this->membres_model->get_all() ?: array();

        // Vérifier que la vue existe
        if (!file_exists(APPPATH . 'views/admin/frontoffice/offrandes.php')) {
            show_error('La vue admin/frontoffice/offrandes.php n\'existe pas');
        }

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/offrandes', $data);
        $this->load->view('layout/footer');
    }
    // ========================================== //
    // AJOUTER UNE OFFERANDE (AJAX)               //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('offrandes', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('montant', 'Montant', 'required|numeric');
        $this->form_validation->set_rules('type', 'Type', 'required');
        $this->form_validation->set_rules('date_transaction', 'Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $membre_id = $this->input->post('membre_id');
        $membre_nom = $this->input->post('membre_nom');

        // Si un membre est sélectionné, on prend ses infos
        if (!empty($membre_id)) {
            $membre = $this->membres_model->get_by_id($membre_id);
            if ($membre) {
                $membre_nom = $membre['nom'] . ' ' . $membre['prenom'];
                $membre_code = $membre['code_membre'] ?? null;
                $telephone = $membre['telephone'] ?? null;
                $email = $membre['email'] ?? null;
            }
        }

        $data = array(
            'type' => $this->input->post('type'),
            'categorie' => $this->input->post('categorie'),
            'montant' => $this->input->post('montant'),
            'date_transaction' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_transaction'))),
            'membre_id' => $membre_id ?? null,
            'membre_nom' => $membre_nom ?? $this->input->post('membre_nom'),
            'membre_code' => $membre_code ?? null,
            'telephone' => $this->input->post('telephone') ?? $telephone ?? null,
            'email' => $this->input->post('email') ?? $email ?? null,
            'mode_paiement' => $this->input->post('mode_paiement') ?? 'especes',
            'reference_paiement' => $this->input->post('reference_paiement'),
            'description' => $this->input->post('description'),
            'reçu' => $this->input->post('reçu') ?? 0,
            'statut' => $this->input->post('statut') ?? 'valide',
            'user_id' => $this->session->userdata('admin_id') ?? 1,
            'deleted' => 0
        );

        $id = $this->offrandes_model->add($data);

        if ($id) {
            echo json_encode(['success' => true, 'message' => 'Offrande ajoutée avec succès', 'id' => $id, 'code' => $data['code_transaction']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
        }
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UNE OFFERANDE      //
    // ========================================== //
    public function get_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('offrandes', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $data = $this->offrandes_model->get_by_id($id);

        if ($data) {
            echo json_encode(['success' => true, 'offrande' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Offrande non trouvée']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UNE OFFERANDE (AJAX)         //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('offrandes', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('montant', 'Montant', 'required|numeric');
        $this->form_validation->set_rules('type', 'Type', 'required');
        $this->form_validation->set_rules('date_transaction', 'Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $membre_id = $this->input->post('membre_id');
        $membre_nom = $this->input->post('membre_nom');

        if (!empty($membre_id)) {
            $membre = $this->membres_model->get_by_id($membre_id);
            if ($membre) {
                $membre_nom = $membre['nom'] . ' ' . $membre['prenom'];
                $membre_code = $membre['code_membre'] ?? null;
                $telephone = $membre['telephone'] ?? null;
                $email = $membre['email'] ?? null;
            }
        }

        $data = array(
            'type' => $this->input->post('type'),
            'categorie' => $this->input->post('categorie'),
            'montant' => $this->input->post('montant'),
            'date_transaction' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_transaction'))),
            'membre_id' => $membre_id ?? null,
            'membre_nom' => $membre_nom ?? $this->input->post('membre_nom'),
            'membre_code' => $membre_code ?? null,
            'telephone' => $this->input->post('telephone') ?? $telephone ?? null,
            'email' => $this->input->post('email') ?? $email ?? null,
            'mode_paiement' => $this->input->post('mode_paiement') ?? 'especes',
            'reference_paiement' => $this->input->post('reference_paiement'),
            'description' => $this->input->post('description'),
            'reçu' => $this->input->post('reçu') ?? 0,
            'statut' => $this->input->post('statut') ?? 'valide'
        );

        $result = $this->offrandes_model->update($id, $data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Offrande mise à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('offrandes', 'can_delete')) {
            access_denied();
        }

        $this->offrandes_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/offrandes');
    }

    // ========================================== //
    // DETAILS D'UNE OFFERANDE (MODAL)            //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('offrandes', 'can_view')) {
            access_denied();
        }

        $data['offrande'] = $this->offrandes_model->get_by_id($id);
        $this->load->view('admin/frontoffice/offrandes_details', $data);
    }

    // ========================================== //
    // EXPORT EXCEL                               //
    // ========================================== //
    public function export_excel() {
        $type = $this->input->get('type');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        $statut = $this->input->get('statut');

        $data = $this->offrandes_model->get_filtered($type, $date_from, $date_to, $statut);

        $filename = 'offrandes_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'Code', 'Type', 'Montant', 'Date', 'Membre', 'Téléphone', 'Email',
            'Mode paiement', 'Référence', 'Statut', 'Description'
        ]);

        $typeLabels = $this->offrandes_model->get_types();
        $modeLabels = $this->offrandes_model->get_modes();
        $statusLabels = $this->offrandes_model->get_statuses();

        foreach ($data as $item) {
            fputcsv($output, [
                $item['code_transaction'] ?? '',
                $typeLabels[$item['type']] ?? $item['type'],
                $item['montant'] ?? 0,
                !empty($item['date_transaction']) ? date('d/m/Y', strtotime($item['date_transaction'])) : '',
                $item['membre_nom'] ?? '',
                $item['telephone'] ?? '',
                $item['email'] ?? '',
                $modeLabels[$item['mode_paiement']] ?? $item['mode_paiement'],
                $item['reference_paiement'] ?? '',
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
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        $statut = $this->input->get('statut');

        $data['offrandes'] = $this->offrandes_model->get_filtered($type, $date_from, $date_to, $statut);
        $data['title'] = 'Liste des offrandes et dîmes';
        $data['date_generated'] = date('d/m/Y H:i');
        $data['stats'] = $this->offrandes_model->get_stats();

        $html = $this->load->view('admin/frontoffice/offrandes_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('offrandes_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('offrandes_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }

    // ========================================== //
    // RECHERCHER DES MEMBRES (AJAX)              //
    // ========================================== //
    public function search_membres() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        $keyword = $this->input->get('q');
        $membres = $this->membres_model->search($keyword);

        $results = array();
        foreach ($membres as $membre) {
            $results[] = array(
                'id' => $membre['id'],
                'text' => $membre['nom'] . ' ' . $membre['prenom'] . ' (' . $membre['code_membre'] . ')',
                'nom' => $membre['nom'] . ' ' . $membre['prenom'],
                'code' => $membre['code_membre'],
                'telephone' => $membre['telephone'],
                'email' => $membre['email']
            );
        }

        echo json_encode(['results' => $results]);
    }
}