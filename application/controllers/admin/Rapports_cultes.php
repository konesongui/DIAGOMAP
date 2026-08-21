<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rapports_cultes extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("rapports_cultes_model");
    }

    // ========================================== //
    // INDEX - LISTE DES RAPPORTS                 //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('rapports_cultes', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'eglise');
        $this->session->set_userdata('sub_menu', 'admin/rapports_cultes');

        $data['rapports'] = $this->rapports_cultes_model->get_all();
        $data['stats'] = $this->rapports_cultes_model->get_stats();
        $data['types'] = $this->rapports_cultes_model->get_types();
        $data['statuses'] = $this->rapports_cultes_model->get_statuses();

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/rapports_cultes', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // AJOUTER UN RAPPORT (AJAX)                  //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('rapports_cultes', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('date_culte', 'Date du culte', 'required');
        $this->form_validation->set_rules('type_culte', 'Type de culte', 'required');
        $this->form_validation->set_rules('theme', 'Thème', 'required');
        $this->form_validation->set_rules('predicateur', 'Prédicateur', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $nombre_hommes = (int)$this->input->post('nombre_hommes') ?? 0;
        $nombre_femmes = (int)$this->input->post('nombre_femmes') ?? 0;
        $nombre_enfants = (int)$this->input->post('nombre_enfants') ?? 0;
        $nombre_visiteurs = (int)$this->input->post('nombre_visiteurs') ?? 0;

        $offrande = (float)$this->input->post('offrande') ?? 0;
        $dime = (float)$this->input->post('dime') ?? 0;
        $actions_de_grace = (float)$this->input->post('actions_de_grace') ?? 0;
        $autres_offrandes = (float)$this->input->post('autres_offrandes') ?? 0;

        $data = array(
            'date_culte' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_culte'))),
            'type_culte' => $this->input->post('type_culte'),
            'theme' => $this->input->post('theme'),
            'predicateur' => $this->input->post('predicateur'),
            'passage_biblique' => $this->input->post('passage_biblique'),
            'nombre_hommes' => $nombre_hommes,
            'nombre_femmes' => $nombre_femmes,
            'nombre_enfants' => $nombre_enfants,
            'nombre_visiteurs' => $nombre_visiteurs,
            'total_personnes' => $nombre_hommes + $nombre_femmes + $nombre_enfants + $nombre_visiteurs,
            'offrande' => $offrande,
            'dime' => $dime,
            'actions_de_grace' => $actions_de_grace,
            'autres_offrandes' => $autres_offrandes,
            'total_finances' => $offrande + $dime + $actions_de_grace + $autres_offrandes,
            'premiere_communion' => (int)$this->input->post('premiere_communion') ?? 0,
            'baptemes' => (int)$this->input->post('baptemes') ?? 0,
            'mariages' => (int)$this->input->post('mariages') ?? 0,
            'funerailles' => (int)$this->input->post('funerailles') ?? 0,
            'priere_malades' => (int)$this->input->post('priere_malades') ?? 0,
            'nouvelles_conversions' => (int)$this->input->post('nouvelles_conversions') ?? 0,
            'rencontres_maison' => (int)$this->input->post('rencontres_maison') ?? 0,
            'visites_malades' => (int)$this->input->post('visites_malades') ?? 0,
            'remarques' => $this->input->post('remarques'),
            'responsable_culte' => $this->input->post('responsable_culte'),
            'statut' => $this->input->post('statut') ?? 'brouillon',
            'user_id' => $this->session->userdata('admin_id') ?? 1,
            'deleted' => 0
        );

        $id = $this->rapports_cultes_model->add($data);

        if ($id) {
            echo json_encode(['success' => true, 'message' => 'Rapport ajouté avec succès', 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
        }
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UN RAPPORT (AJAX) //
    // ========================================== //
    public function get_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('rapports_cultes', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $data = $this->rapports_cultes_model->get_by_id($id);

        if ($data) {
            echo json_encode(['success' => true, 'rapport' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Rapport non trouvé']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UN RAPPORT (AJAX)            //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('rapports_cultes', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('date_culte', 'Date du culte', 'required');
        $this->form_validation->set_rules('type_culte', 'Type de culte', 'required');
        $this->form_validation->set_rules('theme', 'Thème', 'required');
        $this->form_validation->set_rules('predicateur', 'Prédicateur', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $nombre_hommes = (int)$this->input->post('nombre_hommes') ?? 0;
        $nombre_femmes = (int)$this->input->post('nombre_femmes') ?? 0;
        $nombre_enfants = (int)$this->input->post('nombre_enfants') ?? 0;
        $nombre_visiteurs = (int)$this->input->post('nombre_visiteurs') ?? 0;

        $offrande = (float)$this->input->post('offrande') ?? 0;
        $dime = (float)$this->input->post('dime') ?? 0;
        $actions_de_grace = (float)$this->input->post('actions_de_grace') ?? 0;
        $autres_offrandes = (float)$this->input->post('autres_offrandes') ?? 0;

        $data = array(
            'date_culte' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_culte'))),
            'type_culte' => $this->input->post('type_culte'),
            'theme' => $this->input->post('theme'),
            'predicateur' => $this->input->post('predicateur'),
            'passage_biblique' => $this->input->post('passage_biblique'),
            'nombre_hommes' => $nombre_hommes,
            'nombre_femmes' => $nombre_femmes,
            'nombre_enfants' => $nombre_enfants,
            'nombre_visiteurs' => $nombre_visiteurs,
            'total_personnes' => $nombre_hommes + $nombre_femmes + $nombre_enfants + $nombre_visiteurs,
            'offrande' => $offrande,
            'dime' => $dime,
            'actions_de_grace' => $actions_de_grace,
            'autres_offrandes' => $autres_offrandes,
            'total_finances' => $offrande + $dime + $actions_de_grace + $autres_offrandes,
            'premiere_communion' => (int)$this->input->post('premiere_communion') ?? 0,
            'baptemes' => (int)$this->input->post('baptemes') ?? 0,
            'mariages' => (int)$this->input->post('mariages') ?? 0,
            'funerailles' => (int)$this->input->post('funerailles') ?? 0,
            'priere_malades' => (int)$this->input->post('priere_malades') ?? 0,
            'nouvelles_conversions' => (int)$this->input->post('nouvelles_conversions') ?? 0,
            'rencontres_maison' => (int)$this->input->post('rencontres_maison') ?? 0,
            'visites_malades' => (int)$this->input->post('visites_malades') ?? 0,
            'remarques' => $this->input->post('remarques'),
            'responsable_culte' => $this->input->post('responsable_culte'),
            'statut' => $this->input->post('statut') ?? 'brouillon'
        );

        $result = $this->rapports_cultes_model->update($id, $data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Rapport mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('rapports_cultes', 'can_delete')) {
            access_denied();
        }

        $this->rapports_cultes_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/rapports_cultes');
    }

    // ========================================== //
    // DETAILS D'UN RAPPORT (MODAL)               //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('rapports_cultes', 'can_view')) {
            access_denied();
        }

        $data['rapport'] = $this->rapports_cultes_model->get_by_id($id);
        $this->load->view('admin/frontoffice/rapports_cultes_details', $data);
    }

    // ========================================== //
    // EXPORT EXCEL                               //
    // ========================================== //
    public function export_excel() {
        $type_culte = $this->input->get('type_culte');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $data = $this->rapports_cultes_model->get_filtered($type_culte, $date_from, $date_to);

        $filename = 'rapports_cultes_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'Date', 'Type', 'Thème', 'Prédicateur', 'Passage biblique',
            'Hommes', 'Femmes', 'Enfants', 'Visiteurs', 'Total',
            'Offrande', 'Dîme', 'Actions de grâce', 'Autres', 'Total finances',
            '1ère Communion', 'Baptêmes', 'Mariages', 'Funérailles',
            'Prière malades', 'Nouvelles conversions', 'Rencontres maison',
            'Visites malades', 'Responsable', 'Statut', 'Remarques'
        ]);

        $typeLabels = $this->rapports_cultes_model->get_types();
        $statusLabels = $this->rapports_cultes_model->get_statuses();

        foreach ($data as $item) {
            fputcsv($output, [
                !empty($item['date_culte']) ? date('d/m/Y', strtotime($item['date_culte'])) : '',
                $typeLabels[$item['type_culte']] ?? $item['type_culte'],
                $item['theme'] ?? '',
                $item['predicateur'] ?? '',
                $item['passage_biblique'] ?? '',
                $item['nombre_hommes'] ?? 0,
                $item['nombre_femmes'] ?? 0,
                $item['nombre_enfants'] ?? 0,
                $item['nombre_visiteurs'] ?? 0,
                $item['total_personnes'] ?? 0,
                $item['offrande'] ?? 0,
                $item['dime'] ?? 0,
                $item['actions_de_grace'] ?? 0,
                $item['autres_offrandes'] ?? 0,
                $item['total_finances'] ?? 0,
                $item['premiere_communion'] ?? 0,
                $item['baptemes'] ?? 0,
                $item['mariages'] ?? 0,
                $item['funerailles'] ?? 0,
                $item['priere_malades'] ?? 0,
                $item['nouvelles_conversions'] ?? 0,
                $item['rencontres_maison'] ?? 0,
                $item['visites_malades'] ?? 0,
                $item['responsable_culte'] ?? '',
                $statusLabels[$item['statut']] ?? $item['statut'],
                $item['remarques'] ?? ''
            ]);
        }

        fclose($output);
        exit;
    }

    // ========================================== //
    // EXPORT PDF                                 //
    // ========================================== //
    public function export_pdf() {
        $type_culte = $this->input->get('type_culte');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $data['rapports'] = $this->rapports_cultes_model->get_filtered($type_culte, $date_from, $date_to);
        $data['title'] = 'Rapports des cultes';
        $data['date_generated'] = date('d/m/Y H:i');
        $data['stats'] = $this->rapports_cultes_model->get_stats();
        $data['types'] = $this->rapports_cultes_model->get_types();
        $data['statuses'] = $this->rapports_cultes_model->get_statuses();

        $html = $this->load->view('admin/frontoffice/rapports_cultes_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('rapports_cultes_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('rapports_cultes_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }
}