<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mariages extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("mariages_model");
        $this->load->model("membres_model");
    }

    // ========================================== //
    // INDEX - LISTE DES MARIAGES                 //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('mariages', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'eglise');
        $this->session->set_userdata('sub_menu', 'admin/mariages');

        $data['mariages'] = $this->mariages_model->get_all();
        $data['stats'] = $this->mariages_model->get_stats();
        $data['types'] = $this->mariages_model->get_types();
        $data['statuses'] = $this->mariages_model->get_statuses();
        $data['membres'] = $this->membres_model->get_all() ?: array();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/mariages', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // AJOUTER UN MARIAGE (AJAX)                  //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('mariages', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('mari_nom', 'Nom du mari', 'required');
        $this->form_validation->set_rules('mari_prenom', 'Prénom du mari', 'required');
        $this->form_validation->set_rules('femme_nom', 'Nom de la femme', 'required');
        $this->form_validation->set_rules('femme_prenom', 'Prénom de la femme', 'required');
        $this->form_validation->set_rules('date_mariage', 'Date du mariage', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        // Récupérer les infos des membres si sélectionnés
        $mari_id = $this->input->post('mari_id');
        $femme_id = $this->input->post('femme_id');

        $mari_nom = $this->input->post('mari_nom');
        $mari_prenom = $this->input->post('mari_prenom');
        $femme_nom = $this->input->post('femme_nom');
        $femme_prenom = $this->input->post('femme_prenom');

        if (!empty($mari_id)) {
            $mari = $this->membres_model->get_by_id($mari_id);
            if ($mari) {
                $mari_nom = $mari['nom'];
                $mari_prenom = $mari['prenom'];
            }
        }

        if (!empty($femme_id)) {
            $femme = $this->membres_model->get_by_id($femme_id);
            if ($femme) {
                $femme_nom = $femme['nom'];
                $femme_prenom = $femme['prenom'];
            }
        }

        $data = array(
            'mari_id' => $mari_id ?? null,
            'mari_nom' => $mari_nom,
            'mari_prenom' => $mari_prenom,
            'mari_date_naissance' => !empty($this->input->post('mari_date_naissance')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('mari_date_naissance'))) : null,
            'mari_telephone' => $this->input->post('mari_telephone'),
            'mari_email' => $this->input->post('mari_email'),
            'mari_profession' => $this->input->post('mari_profession'),
            'femme_id' => $femme_id ?? null,
            'femme_nom' => $femme_nom,
            'femme_prenom' => $femme_prenom,
            'femme_date_naissance' => !empty($this->input->post('femme_date_naissance')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('femme_date_naissance'))) : null,
            'femme_telephone' => $this->input->post('femme_telephone'),
            'femme_email' => $this->input->post('femme_email'),
            'femme_profession' => $this->input->post('femme_profession'),
            'date_mariage' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_mariage'))),
            'heure_mariage' => $this->input->post('heure_mariage'),
            'lieu_mariage' => $this->input->post('lieu_mariage'),
            'pasteur_officiant' => $this->input->post('pasteur_officiant'),
            'type_mariage' => $this->input->post('type_mariage') ?? 'religieux',
            'nombre_invites' => $this->input->post('nombre_invites') ?? 0,
            'invites' => $this->input->post('invites'),
            'temoins_mari' => $this->input->post('temoins_mari'),
            'temoins_femme' => $this->input->post('temoins_femme'),
            'statut' => $this->input->post('statut') ?? 'planifie',
            'couleur' => $this->input->post('couleur') ?? '#8b5cf6',
            'observations' => $this->input->post('observations'),
            'user_id' => $this->session->userdata('admin_id') ?? 1,
            'deleted' => 0
        );

        $id = $this->mariages_model->add($data);

        if ($id) {
            echo json_encode(['success' => true, 'message' => 'Mariage ajouté avec succès', 'id' => $id, 'code' => $data['code_mariage']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
        }
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UN MARIAGE (AJAX)  //
    // ========================================== //
    public function get_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('mariages', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $data = $this->mariages_model->get_by_id($id);

        if ($data) {
            echo json_encode(['success' => true, 'mariage' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Mariage non trouvé']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UN MARIAGE (AJAX)            //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('mariages', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('mari_nom', 'Nom du mari', 'required');
        $this->form_validation->set_rules('mari_prenom', 'Prénom du mari', 'required');
        $this->form_validation->set_rules('femme_nom', 'Nom de la femme', 'required');
        $this->form_validation->set_rules('femme_prenom', 'Prénom de la femme', 'required');
        $this->form_validation->set_rules('date_mariage', 'Date du mariage', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $mari_id = $this->input->post('mari_id');
        $femme_id = $this->input->post('femme_id');

        $data = array(
            'mari_id' => $mari_id ?? null,
            'mari_nom' => $this->input->post('mari_nom'),
            'mari_prenom' => $this->input->post('mari_prenom'),
            'mari_date_naissance' => !empty($this->input->post('mari_date_naissance')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('mari_date_naissance'))) : null,
            'mari_telephone' => $this->input->post('mari_telephone'),
            'mari_email' => $this->input->post('mari_email'),
            'mari_profession' => $this->input->post('mari_profession'),
            'femme_id' => $femme_id ?? null,
            'femme_nom' => $this->input->post('femme_nom'),
            'femme_prenom' => $this->input->post('femme_prenom'),
            'femme_date_naissance' => !empty($this->input->post('femme_date_naissance')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('femme_date_naissance'))) : null,
            'femme_telephone' => $this->input->post('femme_telephone'),
            'femme_email' => $this->input->post('femme_email'),
            'femme_profession' => $this->input->post('femme_profession'),
            'date_mariage' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_mariage'))),
            'heure_mariage' => $this->input->post('heure_mariage'),
            'lieu_mariage' => $this->input->post('lieu_mariage'),
            'pasteur_officiant' => $this->input->post('pasteur_officiant'),
            'type_mariage' => $this->input->post('type_mariage') ?? 'religieux',
            'nombre_invites' => $this->input->post('nombre_invites') ?? 0,
            'invites' => $this->input->post('invites'),
            'temoins_mari' => $this->input->post('temoins_mari'),
            'temoins_femme' => $this->input->post('temoins_femme'),
            'statut' => $this->input->post('statut') ?? 'planifie',
            'couleur' => $this->input->post('couleur') ?? '#8b5cf6',
            'observations' => $this->input->post('observations')
        );

        $result = $this->mariages_model->update($id, $data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Mariage mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('mariages', 'can_delete')) {
            access_denied();
        }

        $this->mariages_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/mariages');
    }

    // ========================================== //
    // DETAILS D'UN MARIAGE (MODAL)               //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('mariages', 'can_view')) {
            access_denied();
        }

        $data['mariage'] = $this->mariages_model->get_by_id($id);
        $this->load->view('admin/frontoffice/mariages_details', $data);
    }

    // ========================================== //
    // GÉNÉRER UN CERTIFICAT                      //
    // ========================================== //
    public function generer_certificat($id) {
        if (!$this->rbac->hasPrivilege('mariages', 'can_edit')) {
            access_denied();
        }

        $mariage = $this->mariages_model->get_by_id($id);
        if (!$mariage) {
            $this->session->set_flashdata('error', '<div class="alert alert-danger">Mariage non trouvé</div>');
            redirect('admin/mariages');
        }

        $this->mariages_model->generer_certificat($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Certificat de mariage généré avec succès</div>');
        redirect('admin/mariages');
    }

    // ========================================== //
    // EXPORT EXCEL                               //
    // ========================================== //
    public function export_excel() {
        $statut = $this->input->get('statut');
        $type = $this->input->get('type');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $data = $this->mariages_model->get_filtered($statut, $type, $date_from, $date_to);

        $filename = 'mariages_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'Code', 'Mari', 'Femme', 'Date mariage', 'Lieu', 'Pasteur',
            'Type', 'Invités', 'Témoins mari', 'Témoins femme', 'Statut', 'Certificat'
        ]);

        $typeLabels = $this->mariages_model->get_types();
        $statusLabels = $this->mariages_model->get_statuses();

        foreach ($data as $item) {
            fputcsv($output, [
                $item['code_mariage'] ?? '',
                ($item['mari_nom'] ?? '') . ' ' . ($item['mari_prenom'] ?? ''),
                ($item['femme_nom'] ?? '') . ' ' . ($item['femme_prenom'] ?? ''),
                !empty($item['date_mariage']) ? date('d/m/Y', strtotime($item['date_mariage'])) : '',
                $item['lieu_mariage'] ?? '',
                $item['pasteur_officiant'] ?? '',
                $typeLabels[$item['type_mariage']] ?? $item['type_mariage'],
                $item['nombre_invites'] ?? 0,
                $item['temoins_mari'] ?? '',
                $item['temoins_femme'] ?? '',
                $statusLabels[$item['statut']] ?? $item['statut'],
                $item['certificat_genere'] ? 'Oui' : 'Non'
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
        $type = $this->input->get('type');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $data['mariages'] = $this->mariages_model->get_filtered($statut, $type, $date_from, $date_to);
        $data['title'] = 'Liste des mariages';
        $data['date_generated'] = date('d/m/Y H:i');
        $data['stats'] = $this->mariages_model->get_stats();

        $html = $this->load->view('admin/frontoffice/mariages_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('mariages_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('mariages_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }
}