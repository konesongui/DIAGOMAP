<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Baptemes extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("baptemes_model");
        $this->load->model("membres_model");
    }

    // ========================================== //
    // INDEX - LISTE DES BAPTÊMES                 //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('baptemes', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'eglise');
        $this->session->set_userdata('sub_menu', 'admin/baptemes');

        $data['baptemes'] = $this->baptemes_model->get_all();
        $data['stats'] = $this->baptemes_model->get_stats();
        $data['types'] = $this->baptemes_model->get_types();
        $data['statuses'] = $this->baptemes_model->get_statuses();
        $data['membres'] = $this->membres_model->get_all() ?: array();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/baptemes', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // AJOUTER UN BAPTÊME (AJAX)                  //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('baptemes', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('nom', 'Nom', 'required');
        $this->form_validation->set_rules('prenom', 'Prénom', 'required');
        $this->form_validation->set_rules('date_bapteme', 'Date du baptême', 'required');

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
            }
        }

        $data = array(
            'membre_id' => $membre_id ?? null,
            'nom' => $this->input->post('nom'),
            'prenom' => $this->input->post('prenom'),
            'sexe' => $this->input->post('sexe') ?? 'M',
            'date_naissance' => !empty($this->input->post('date_naissance')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_naissance'))) : null,
            'telephone' => $this->input->post('telephone'),
            'email' => $this->input->post('email'),
            'adresse' => $this->input->post('adresse'),
            'date_bapteme' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_bapteme'))),
            'heure_bapteme' => $this->input->post('heure_bapteme'),
            'lieu_bapteme' => $this->input->post('lieu_bapteme'),
            'pasteur_officiant' => $this->input->post('pasteur_officiant'),
            'type_bapteme' => $this->input->post('type_bapteme') ?? 'immersion',
            'nombre_participants' => $this->input->post('nombre_participants') ?? 0,
            'participants' => $this->input->post('participants'),
            'temoignage' => $this->input->post('temoignage'),
            'parrains' => $this->input->post('parrains'),
            'marraines' => $this->input->post('marraines'),
            'statut' => $this->input->post('statut') ?? 'planifie',
            'couleur' => $this->input->post('couleur') ?? '#3b82f6',
            'observations' => $this->input->post('observations'),
            'user_id' => $this->session->userdata('admin_id') ?? 1,
            'deleted' => 0
        );

        $id = $this->baptemes_model->add($data);

        if ($id) {
            echo json_encode(['success' => true, 'message' => 'Baptême ajouté avec succès', 'id' => $id, 'code' => $data['code_bapteme']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
        }
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UN BAPTÊME (AJAX)  //
    // ========================================== //
    public function get_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('baptemes', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $data = $this->baptemes_model->get_by_id($id);

        if ($data) {
            echo json_encode(['success' => true, 'bapteme' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Baptême non trouvé']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UN BAPTÊME (AJAX)            //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('baptemes', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('nom', 'Nom', 'required');
        $this->form_validation->set_rules('prenom', 'Prénom', 'required');
        $this->form_validation->set_rules('date_bapteme', 'Date du baptême', 'required');

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
            }
        }

        $data = array(
            'membre_id' => $membre_id ?? null,
            'nom' => $this->input->post('nom'),
            'prenom' => $this->input->post('prenom'),
            'sexe' => $this->input->post('sexe') ?? 'M',
            'date_naissance' => !empty($this->input->post('date_naissance')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_naissance'))) : null,
            'telephone' => $this->input->post('telephone'),
            'email' => $this->input->post('email'),
            'adresse' => $this->input->post('adresse'),
            'date_bapteme' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_bapteme'))),
            'heure_bapteme' => $this->input->post('heure_bapteme'),
            'lieu_bapteme' => $this->input->post('lieu_bapteme'),
            'pasteur_officiant' => $this->input->post('pasteur_officiant'),
            'type_bapteme' => $this->input->post('type_bapteme') ?? 'immersion',
            'nombre_participants' => $this->input->post('nombre_participants') ?? 0,
            'participants' => $this->input->post('participants'),
            'temoignage' => $this->input->post('temoignage'),
            'parrains' => $this->input->post('parrains'),
            'marraines' => $this->input->post('marraines'),
            'statut' => $this->input->post('statut') ?? 'planifie',
            'couleur' => $this->input->post('couleur') ?? '#3b82f6',
            'observations' => $this->input->post('observations')
        );

        $result = $this->baptemes_model->update($id, $data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Baptême mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('baptemes', 'can_delete')) {
            access_denied();
        }

        $this->baptemes_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/baptemes');
    }

    // ========================================== //
    // DETAILS D'UN BAPTÊME (MODAL)               //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('baptemes', 'can_view')) {
            access_denied();
        }

        $data['bapteme'] = $this->baptemes_model->get_by_id($id);
        $this->load->view('admin/frontoffice/baptemes_details', $data);
    }

    // ========================================== //
    // GÉNÉRER UN CERTIFICAT                      //
    // ========================================== //
    public function generer_certificat($id) {
        if (!$this->rbac->hasPrivilege('baptemes', 'can_edit')) {
            access_denied();
        }

        $bapteme = $this->baptemes_model->get_by_id($id);
        if (!$bapteme) {
            $this->session->set_flashdata('error', '<div class="alert alert-danger">Baptême non trouvé</div>');
            redirect('admin/baptemes');
        }

        $this->baptemes_model->generer_certificat($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Certificat généré avec succès</div>');
        redirect('admin/baptemes');
    }

    // ========================================== //
    // EXPORT EXCEL                               //
    // ========================================== //
    public function export_excel() {
        $statut = $this->input->get('statut');
        $type = $this->input->get('type');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $data = $this->baptemes_model->get_filtered($statut, $type, $date_from, $date_to);

        $filename = 'baptemes_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'Code', 'Nom', 'Prénom', 'Sexe', 'Date baptême', 'Lieu', 'Pasteur',
            'Type', 'Participants', 'Parrains', 'Marraines', 'Statut', 'Certificat'
        ]);

        $typeLabels = $this->baptemes_model->get_types();
        $statusLabels = $this->baptemes_model->get_statuses();

        foreach ($data as $item) {
            fputcsv($output, [
                $item['code_bapteme'] ?? '',
                $item['nom'] ?? '',
                $item['prenom'] ?? '',
                $item['sexe'] == 'M' ? 'Homme' : 'Femme',
                !empty($item['date_bapteme']) ? date('d/m/Y', strtotime($item['date_bapteme'])) : '',
                $item['lieu_bapteme'] ?? '',
                $item['pasteur_officiant'] ?? '',
                $typeLabels[$item['type_bapteme']] ?? $item['type_bapteme'],
                $item['nombre_participants'] ?? 0,
                $item['parrains'] ?? '',
                $item['marraines'] ?? '',
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

        $data['baptemes'] = $this->baptemes_model->get_filtered($statut, $type, $date_from, $date_to);
        $data['title'] = 'Liste des baptêmes';
        $data['date_generated'] = date('d/m/Y H:i');
        $data['stats'] = $this->baptemes_model->get_stats();

        $html = $this->load->view('admin/frontoffice/baptemes_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('baptemes_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('baptemes_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }
}