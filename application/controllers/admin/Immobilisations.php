<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Immobilisations extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("immobilisations_model");
    }

    // ========================================== //
    // INDEX - LISTE DES IMMOBILISATIONS          //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('immobilisations', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'finance');
        $this->session->set_userdata('sub_menu', 'admin/immobilisations');

        $data['immobilisations'] = $this->immobilisations_model->get_all();
        $data['stats'] = $this->immobilisations_model->get_stats();
        $data['categories'] = $this->immobilisations_model->get_categories();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/immobilisations', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // AJOUTER UNE IMMOBILISATION (AJAX)          //
    // ========================================== //
    public function add_ajax_() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('immobilisations', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('nom', 'Nom', 'required');
        $this->form_validation->set_rules('categorie', 'Catégorie', 'required');
        $this->form_validation->set_rules('valeur_originale', 'Valeur originale', 'required|numeric');
        $this->form_validation->set_rules('date_acquisition', 'Date d\'acquisition', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = array(
            'nom' => $this->input->post('nom'),
            'description' => $this->input->post('description'),
            'categorie' => $this->input->post('categorie'),
            'type_immobilisation' => $this->input->post('type_immobilisation'),
            'date_acquisition' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_acquisition'))),
            'date_mise_en_service' => !empty($this->input->post('date_mise_en_service')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_mise_en_service'))) : null,
            'valeur_originale' => $this->input->post('valeur_originale'),
            'valeur_residuelle' => $this->input->post('valeur_residuelle') ?? 0,
            'duree_amortissement' => $this->input->post('duree_amortissement'),
            'taux_amortissement' => $this->input->post('taux_amortissement'),
            'mode_amortissement' => $this->input->post('mode_amortissement'),
            'fournisseur_id' => $this->input->post('fournisseur_id'),
            'num_facture' => $this->input->post('num_facture'),
            'num_serie' => $this->input->post('num_serie'),
            'localisation' => $this->input->post('localisation'),
            'responsable' => $this->input->post('responsable'),
            'statut' => 'actif',
            'user_id' => $this->session->userdata('admin_id') ?? 1,
            'deleted' => 0
        );

        $id = $this->immobilisations_model->add($data);

        if ($id) {
            echo json_encode(['success' => true, 'message' => 'Immobilisation ajoutée avec succès', 'id' => $id, 'code' => $data['code']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
        }
    }

    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('immobilisations', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('nom', 'Nom', 'required');
        $this->form_validation->set_rules('categorie', 'Catégorie', 'required');
        $this->form_validation->set_rules('valeur_originale', 'Valeur originale', 'required|numeric');
        $this->form_validation->set_rules('date_acquisition', 'Date d\'acquisition', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        // Récupérer et formater les données
        $date_acquisition = $this->input->post('date_acquisition');
        $date_mise_en_service = $this->input->post('date_mise_en_service');

        $data = array(
            'nom' => $this->input->post('nom'),
            'description' => $this->input->post('description'),
            'categorie' => $this->input->post('categorie'),
            'type_immobilisation' => $this->input->post('type_immobilisation') ?? 'corporelle',
            'date_acquisition' => !empty($date_acquisition) ? date('Y-m-d', $this->customlib->datetostrtotime($date_acquisition)) : date('Y-m-d'),
            'date_mise_en_service' => !empty($date_mise_en_service) ? date('Y-m-d', $this->customlib->datetostrtotime($date_mise_en_service)) : null,
            'valeur_originale' => $this->input->post('valeur_originale') ?? 0,
            'valeur_residuelle' => $this->input->post('valeur_residuelle') ?? 0,
            'duree_amortissement' => $this->input->post('duree_amortissement'),
            'taux_amortissement' => $this->input->post('taux_amortissement'),
            'mode_amortissement' => $this->input->post('mode_amortissement') ?? 'lineaire',
            'fournisseur_id' => $this->input->post('fournisseur_id'),
            'num_facture' => $this->input->post('num_facture'),
            'num_serie' => $this->input->post('num_serie'),
            'localisation' => $this->input->post('localisation'),
            'responsable' => $this->input->post('responsable'),
            'statut' => 'actif',
            'user_id' => $this->session->userdata('admin_id') ?? 1,
            'deleted' => 0
        );

        try {
            $id = $this->immobilisations_model->add($data);

            if ($id) {
                // Retourner une réponse JSON propre
                $response = array(
                    'success' => true,
                    'message' => 'Immobilisation ajoutée avec succès',
                    'id' => $id
                );
                echo json_encode($response);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
            }
        } catch (Exception $e) {
            log_message('error', 'Erreur dans Immobilisations::add_ajax: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }

    public function add($data) {
        // Log des données
        log_message('debug', 'Modèle add - Données: ' . print_r($data, true));

        // Vérifier que les données sont valides
        if (empty($data['nom'])) {
            log_message('error', 'Nom manquant');
            return false;
        }

        // Générer un code
        $data['code'] = 'IMM-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Insérer
        $this->db->insert('immobilisations', $data);
        $id = $this->db->insert_id();

        log_message('debug', 'ID inséré: ' . $id);

        return $id;
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UNE IMMOBILISATION //
    // ========================================== //
    public function get_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('immobilisations', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $data = $this->immobilisations_model->get_by_id($id);

        if ($data) {
            echo json_encode(['success' => true, 'immobilisation' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Immobilisation non trouvée']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UNE IMMOBILISATION (AJAX)    //
    // ========================================== //
    // ========================================== //
// METTRE À JOUR UNE IMMOBILISATION (AJAX)    //
// ========================================== //
    public function update_ajax() {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('immobilisations', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        // Log pour debug
        log_message('debug', 'Update immo ID: ' . $id);
        log_message('debug', 'POST data: ' . print_r($this->input->post(), true));

        if (empty($id) || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            return;
        }

        $this->form_validation->set_rules('nom', 'Nom', 'required');
        $this->form_validation->set_rules('categorie', 'Catégorie', 'required');
        $this->form_validation->set_rules('valeur_originale', 'Valeur originale', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $date_acquisition = $this->input->post('date_acquisition');
        $date_mise_en_service = $this->input->post('date_mise_en_service');

        $data = array(
            'nom' => $this->input->post('nom'),
            'description' => $this->input->post('description'),
            'categorie' => $this->input->post('categorie'),
            'type_immobilisation' => $this->input->post('type_immobilisation') ?? 'corporelle',
            'date_acquisition' => !empty($date_acquisition) ? date('Y-m-d', $this->customlib->datetostrtotime($date_acquisition)) : date('Y-m-d'),
            'date_mise_en_service' => !empty($date_mise_en_service) ? date('Y-m-d', $this->customlib->datetostrtotime($date_mise_en_service)) : null,
            'valeur_originale' => $this->input->post('valeur_originale') ?? 0,
            'valeur_residuelle' => $this->input->post('valeur_residuelle') ?? 0,
            'duree_amortissement' => $this->input->post('duree_amortissement'),
            'taux_amortissement' => $this->input->post('taux_amortissement'),
            'mode_amortissement' => $this->input->post('mode_amortissement') ?? 'lineaire',
            'fournisseur_id' => $this->input->post('fournisseur_id'),
            'num_facture' => $this->input->post('num_facture'),
            'num_serie' => $this->input->post('num_serie'),
            'localisation' => $this->input->post('localisation'),
            'responsable' => $this->input->post('responsable'),
            'statut' => $this->input->post('statut') ?? 'actif'
        );

        try {
            $result = $this->immobilisations_model->update($id, $data);
            log_message('debug', 'Update result: ' . $result);

            if ($result !== false) {
                echo json_encode(['success' => true, 'message' => 'Immobilisation mise à jour avec succès']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
        } catch (Exception $e) {
            log_message('error', 'Erreur dans Immobilisations::update_ajax: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('immobilisations', 'can_delete')) {
            access_denied();
        }

        $this->immobilisations_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/immobilisations');
    }

    // ========================================== //
    // CALCULER L'AMORTISSEMENT                   //
    // ========================================== //
    public function calculer_amortissement($id) {
        if (!$this->rbac->hasPrivilege('immobilisations', 'can_edit')) {
            access_denied();
        }

        $result = $this->immobilisations_model->calculer_amortissement($id);

        if ($result) {
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Amortissement calculé avec succès</div>');
        } else {
            $this->session->set_flashdata('error', '<div class="alert alert-danger">Erreur lors du calcul de l\'amortissement</div>');
        }

        redirect('admin/immobilisations');
    }

    // ========================================== //
    // CÉDER UNE IMMOBILISATION                   //
    // ========================================== //
    public function ceder() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('immobilisations', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('id');
        $montant = $this->input->post('montant_cession');
        $acheteur = $this->input->post('acheteur');
        $motif = $this->input->post('motif');

        if (empty($id) || empty($montant) || empty($acheteur)) {
            echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
            return;
        }

        $result = $this->immobilisations_model->ceder($id, $montant, $acheteur, $motif);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Immobilisation cédée avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la cession']);
        }
    }

    // ========================================== //
    // DÉTAILS D'UNE IMMOBILISATION               //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('immobilisations', 'can_view')) {
            access_denied();
        }

        $data['immobilisation'] = $this->immobilisations_model->get_by_id($id);
        $data['amortissements'] = $this->immobilisations_model->get_amortissements($id);
        $data['cessions'] = $this->immobilisations_model->get_cessions($id);
        $this->load->view('admin/frontoffice/immobilisations_details', $data);
    }

    // ========================================== //
    // EXPORT EXCEL                               //
    // ========================================== //
    public function export_excel() {
        $categorie = $this->input->get('categorie');
        $statut = $this->input->get('statut');

        $data = $this->immobilisations_model->get_filtered($categorie, $statut);

        $filename = 'immobilisations_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'Code', 'Nom', 'Catégorie', 'Type', 'Date acquisition',
            'Valeur originale', 'Valeur résiduelle', 'Amortissement cumulé',
            'Valeur nette', 'Statut', 'Numéro série', 'Localisation', 'Responsable'
        ]);

        foreach ($data as $item) {
            fputcsv($output, [
                $item['code'] ?? '',
                $item['nom'] ?? '',
                $item['categorie'] ?? '',
                $item['type_immobilisation'] ?? '',
                !empty($item['date_acquisition']) ? date('d/m/Y', strtotime($item['date_acquisition'])) : '',
                $item['valeur_originale'] ?? 0,
                $item['valeur_residuelle'] ?? 0,
                $item['amortissement_cumule'] ?? 0,
                $item['valeur_nette'] ?? 0,
                $item['statut'] ?? '',
                $item['num_serie'] ?? '',
                $item['localisation'] ?? '',
                $item['responsable'] ?? ''
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

        $data['immobilisations'] = $this->immobilisations_model->get_filtered($categorie, $statut);
        $data['title'] = 'Liste des immobilisations';
        $data['date_generated'] = date('d/m/Y H:i');

        $html = $this->load->view('admin/frontoffice/immobilisations_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('immobilisations_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('immobilisations_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }
}