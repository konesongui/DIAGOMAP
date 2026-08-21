<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Funerailles extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("funerailles_model");
        $this->load->model("membres_model");
    }

    // ========================================== //
    // INDEX - LISTE DES FUNÉRAILLES              //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('funerailles', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'eglise');
        $this->session->set_userdata('sub_menu', 'admin/funerailles');

        $data['funerailles'] = $this->funerailles_model->get_all();
        $data['stats'] = $this->funerailles_model->get_stats();
        $data['types'] = $this->funerailles_model->get_types();
        $data['statuses'] = $this->funerailles_model->get_statuses();
        $data['membres'] = $this->membres_model->get_all() ?: array();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/funerailles', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // AJOUTER UNE FUNÉRAILLE (AJAX)              //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('funerailles', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('defunt_nom', 'Nom du défunt', 'required');
        $this->form_validation->set_rules('defunt_prenom', 'Prénom du défunt', 'required');
        $this->form_validation->set_rules('date_funerailles', 'Date des funérailles', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $defunt_id = $this->input->post('defunt_id');
        $defunt_nom = $this->input->post('defunt_nom');
        $defunt_prenom = $this->input->post('defunt_prenom');

        if (!empty($defunt_id)) {
            $membre = $this->membres_model->get_by_id($defunt_id);
            if ($membre) {
                $defunt_nom = $membre['nom'];
                $defunt_prenom = $membre['prenom'];
            }
        }

        $data = array(
            'defunt_id' => $defunt_id ?? null,
            'defunt_nom' => $defunt_nom,
            'defunt_prenom' => $defunt_prenom,
            'defunt_sexe' => $this->input->post('defunt_sexe') ?? 'M',
            'defunt_date_naissance' => !empty($this->input->post('defunt_date_naissance')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('defunt_date_naissance'))) : null,
            'defunt_date_deces' => !empty($this->input->post('defunt_date_deces')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('defunt_date_deces'))) : date('Y-m-d'),
            'defunt_telephone' => $this->input->post('defunt_telephone'),
            'defunt_email' => $this->input->post('defunt_email'),
            'defunt_adresse' => $this->input->post('defunt_adresse'),
            'defunt_profession' => $this->input->post('defunt_profession'),
            'date_funerailles' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_funerailles'))),
            'heure_funerailles' => $this->input->post('heure_funerailles'),
            'lieu_funerailles' => $this->input->post('lieu_funerailles'),
            'pasteur_officiant' => $this->input->post('pasteur_officiant'),
            'type_ceremonie' => $this->input->post('type_ceremonie') ?? 'enterrement',
            'nombre_participants' => $this->input->post('nombre_participants') ?? 0,
            'participants' => $this->input->post('participants'),
            'famille_proche' => $this->input->post('famille_proche'),
            'conjoint' => $this->input->post('conjoint'),
            'enfants' => $this->input->post('enfants'),
            'observations' => $this->input->post('observations'),
            'statut' => $this->input->post('statut') ?? 'planifie',
            'couleur' => $this->input->post('couleur') ?? '#6b7280',
            'user_id' => $this->session->userdata('admin_id') ?? 1,
            'deleted' => 0
        );

        $id = $this->funerailles_model->add($data);

        if ($id) {
            // Gestion de la photo
            if (isset($_FILES["defunt_photo"]) && !empty($_FILES['defunt_photo']['name'])) {
                $this->funerailles_model->upload_photo($id, $_FILES["defunt_photo"]);
            }

            echo json_encode(['success' => true, 'message' => 'Funérailles ajoutées avec succès', 'id' => $id, 'code' => $data['code_funerailles']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
        }
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UNE FUNÉRAILLE     //
    // ========================================== //
    public function get_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('funerailles', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $data = $this->funerailles_model->get_by_id($id);

        if ($data) {
            echo json_encode(['success' => true, 'funerailles' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Funérailles non trouvées']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UNE FUNÉRAILLE (AJAX)        //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('funerailles', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('defunt_nom', 'Nom du défunt', 'required');
        $this->form_validation->set_rules('defunt_prenom', 'Prénom du défunt', 'required');
        $this->form_validation->set_rules('date_funerailles', 'Date des funérailles', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $data = array(
            'defunt_id' => $this->input->post('defunt_id') ?? null,
            'defunt_nom' => $this->input->post('defunt_nom'),
            'defunt_prenom' => $this->input->post('defunt_prenom'),
            'defunt_sexe' => $this->input->post('defunt_sexe') ?? 'M',
            'defunt_date_naissance' => !empty($this->input->post('defunt_date_naissance')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('defunt_date_naissance'))) : null,
            'defunt_date_deces' => !empty($this->input->post('defunt_date_deces')) ? date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('defunt_date_deces'))) : date('Y-m-d'),
            'defunt_telephone' => $this->input->post('defunt_telephone'),
            'defunt_email' => $this->input->post('defunt_email'),
            'defunt_adresse' => $this->input->post('defunt_adresse'),
            'defunt_profession' => $this->input->post('defunt_profession'),
            'date_funerailles' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_funerailles'))),
            'heure_funerailles' => $this->input->post('heure_funerailles'),
            'lieu_funerailles' => $this->input->post('lieu_funerailles'),
            'pasteur_officiant' => $this->input->post('pasteur_officiant'),
            'type_ceremonie' => $this->input->post('type_ceremonie') ?? 'enterrement',
            'nombre_participants' => $this->input->post('nombre_participants') ?? 0,
            'participants' => $this->input->post('participants'),
            'famille_proche' => $this->input->post('famille_proche'),
            'conjoint' => $this->input->post('conjoint'),
            'enfants' => $this->input->post('enfants'),
            'observations' => $this->input->post('observations'),
            'statut' => $this->input->post('statut') ?? 'planifie',
            'couleur' => $this->input->post('couleur') ?? '#6b7280'
        );

        // Gestion de la photo
        if (isset($_FILES["defunt_photo"]) && !empty($_FILES['defunt_photo']['name'])) {
            $this->funerailles_model->upload_photo($id, $_FILES["defunt_photo"]);
        }

        $result = $this->funerailles_model->update($id, $data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Funérailles mises à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('funerailles', 'can_delete')) {
            access_denied();
        }

        $this->funerailles_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/funerailles');
    }

    // ========================================== //
    // DETAILS D'UNE FUNÉRAILLE (MODAL)           //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('funerailles', 'can_view')) {
            access_denied();
        }

        $data['funerailles'] = $this->funerailles_model->get_by_id($id);
        $this->load->view('admin/frontoffice/funerailles_details', $data);
    }

    // ========================================== //
    // GÉNÉRER UN CERTIFICAT                      //
    // ========================================== //
    public function generer_certificat($id) {
        if (!$this->rbac->hasPrivilege('funerailles', 'can_edit')) {
            access_denied();
        }

        $funerailles = $this->funerailles_model->get_by_id($id);
        if (!$funerailles) {
            $this->session->set_flashdata('error', '<div class="alert alert-danger">Funérailles non trouvées</div>');
            redirect('admin/funerailles');
        }

        $this->funerailles_model->update($id, array('certificat_genere' => 1));
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Certificat de décès généré avec succès</div>');
        redirect('admin/funerailles');
    }

    // ========================================== //
    // EXPORT EXCEL                               //
    // ========================================== //
    public function export_excel() {
        $statut = $this->input->get('statut');
        $type = $this->input->get('type');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $data = $this->funerailles_model->get_filtered($statut, $type, $date_from, $date_to);

        $filename = 'funerailles_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'Code', 'Défunt', 'Sexe', 'Date décès', 'Date funérailles', 'Lieu',
            'Pasteur', 'Type', 'Participants', 'Conjoint', 'Enfants', 'Statut', 'Certificat'
        ]);

        $typeLabels = $this->funerailles_model->get_types();
        $statusLabels = $this->funerailles_model->get_statuses();

        foreach ($data as $item) {
            fputcsv($output, [
                $item['code_funerailles'] ?? '',
                ($item['defunt_nom'] ?? '') . ' ' . ($item['defunt_prenom'] ?? ''),
                $item['defunt_sexe'] == 'M' ? 'Homme' : 'Femme',
                !empty($item['defunt_date_deces']) ? date('d/m/Y', strtotime($item['defunt_date_deces'])) : '',
                !empty($item['date_funerailles']) ? date('d/m/Y', strtotime($item['date_funerailles'])) : '',
                $item['lieu_funerailles'] ?? '',
                $item['pasteur_officiant'] ?? '',
                $typeLabels[$item['type_ceremonie']] ?? $item['type_ceremonie'],
                $item['nombre_participants'] ?? 0,
                $item['conjoint'] ?? '',
                $item['enfants'] ?? '',
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

        $data['funerailles'] = $this->funerailles_model->get_filtered($statut, $type, $date_from, $date_to);
        $data['title'] = 'Liste des funérailles';
        $data['date_generated'] = date('d/m/Y H:i');
        $data['stats'] = $this->funerailles_model->get_stats();

        $html = $this->load->view('admin/frontoffice/funerailles_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('funerailles_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('funerailles_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }
}