<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tickets extends Admin_Controller {

    protected $tickets_model;

    public function __construct() {
        parent::__construct();
        
        $this->load->model('tickets_model');
        $this->tickets_model = $this->tickets_model;
    }

    public function index() {
        if (!$this->rbac->hasPrivilege('tickets', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/tickets');

        if (!$this->tickets_model->tables_available()) {
            $data = array(
                'tickets' => array(),
                'stats' => array('total' => 0, 'statuts' => array(), 'urgents' => 0),
                'setup_error' => 'Les tables du module tickets ne sont pas installées. Importez application/sql/create_tickets_tables.sql dans la base diama.'
            );
            $this->load->view('layout/header', $data);
            $this->load->view('admin/frontoffice/tickets', $data);
            $this->load->view('layout/footer', $data);
            return;
        }

        try {
            $data['tickets'] = $this->tickets_model->get_all();
            $data['stats'] = $this->tickets_model->get_stats();
            $data['categories'] = $this->tickets_model->get_categories();
            $data['statuts'] = $this->tickets_model->get_statuts();
            $data['priorites'] = $this->tickets_model->get_priorites();
            $data['staff'] = $this->tickets_model->get_staff();
        } catch (Throwable $e) {
            log_message('error', 'Erreur Tickets::index - ' . $e->getMessage());
            $data = array(
                'tickets' => array(),
                'stats' => array('total' => 0, 'statuts' => array(), 'urgents' => 0),
                'categories' => array(),
                'statuts' => array(),
                'priorites' => array(),
                'staff' => array(),
                'query_error' => 'La lecture des tables tickets a échoué. Vérifiez que le SQL a été importé dans la base diama.'
            );
        }

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/tickets', $data);
        $this->load->view('layout/footer');
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('tickets', 'can_add')) {
            access_denied();
            return;
        }

        $data['title'] = 'Nouveau ticket';
        $data['categories'] = $this->tickets_model->get_categories();
        $data['priorites'] = $this->tickets_model->get_priorites();
        $data['staff'] = $this->tickets_model->get_staff();
        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/tickets');
        $this->load->view('layout/header', $data);
        $this->load->view('admin/frontoffice/ticket_add', $data);
        $this->load->view('layout/footer', $data);
    }


    // ========================================== //
    // AJOUTER UN TICKET (AJAX)                   //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('tickets', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('titre', 'Titre', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('categorie_id', 'Catégorie', 'required');
        $this->form_validation->set_rules('priorite_id', 'Priorité', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $ticket_data = array(
            'ticket_number' => $this->tickets_model->generate_ticket_number(),
            'titre' => $this->input->post('titre'),
            'description' => $this->input->post('description'),
            'categorie_id' => $this->input->post('categorie_id'),
            'priorite_id' => $this->input->post('priorite_id'),
            'statut_id' => 1, // Ouvert
            'created_by' => (int) (($this->session->userdata('admin')['id'] ?? 0) ?: 1),
            'assigned_to' => $this->input->post('assigned_to') ?: null,
            'date_echeance' => !empty($this->input->post('date_echeance')) ? date('Y-m-d H:i:s', strtotime($this->input->post('date_echeance'))) : null,
            'notes' => $this->input->post('notes'),
            'date_creation' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'deleted' => 0
        );

        $ticket_id = $this->tickets_model->add($ticket_data);

        if ($ticket_id) {
            // Ajouter une réponse initiale si présente
            if ($this->input->post('message_initial')) {
                $reponse_data = array(
                    'ticket_id' => $ticket_id,
                    'staff_id' => (int) (($this->session->userdata('admin')['id'] ?? 0) ?: 1),
                    'message' => $this->input->post('message_initial'),
                    'est_interne' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                );
                $this->tickets_model->add_reponse($reponse_data);
            }

            // Gestion du fichier
            if (isset($_FILES["fichier"]) && !empty($_FILES['fichier']['name'])) {
                $this->upload_file($ticket_id);
            }

            echo json_encode(['success' => true, 'message' => 'Ticket créé avec succès', 'id' => $ticket_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la création du ticket']);
        }
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UN TICKET (AJAX)  //
    // ========================================== //
    public function get_ticket_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('tickets', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            return;
        }

        $data = $this->tickets_model->get_by_id($id);

        if ($data && !empty($data)) {
            $ticket = [
                'id' => (int)$data['id'],
                'ticket_number' => (string)($data['ticket_number'] ?? ''),
                'titre' => (string)($data['titre'] ?? ''),
                'description' => (string)($data['description'] ?? ''),
                'categorie_id' => (int)($data['categorie_id'] ?? 0),
                'priorite_id' => (int)($data['priorite_id'] ?? 0),
                'statut_id' => (int)($data['statut_id'] ?? 1),
                'assigned_to' => $data['assigned_to'] ? (int)$data['assigned_to'] : null,
                'date_echeance' => (string)($data['date_echeance'] ?? ''),
                'notes' => (string)($data['notes'] ?? ''),
                'fichier' => (string)($data['fichier'] ?? '')
            ];

            echo json_encode([
                'success' => true,
                'ticket' => $ticket
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ticket non trouvé']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UN TICKET (AJAX)             //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('tickets', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('titre', 'Titre', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('categorie_id', 'Catégorie', 'required');
        $this->form_validation->set_rules('priorite_id', 'Priorité', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $ticket_data = array(
            'titre' => $this->input->post('titre'),
            'description' => $this->input->post('description'),
            'categorie_id' => $this->input->post('categorie_id'),
            'priorite_id' => $this->input->post('priorite_id'),
            'statut_id' => $this->input->post('statut_id') ?? 1,
            'assigned_to' => $this->input->post('assigned_to') ?: null,
            'date_echeance' => !empty($this->input->post('date_echeance')) ? date('Y-m-d H:i:s', strtotime($this->input->post('date_echeance'))) : null,
            'notes' => $this->input->post('notes'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        // Gestion du fichier
        if (isset($_FILES["fichier"]) && !empty($_FILES['fichier']['name'])) {
            $this->upload_file($id, true);
        }

        $result = $this->tickets_model->update($id, $ticket_data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Ticket mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // AJOUTER UNE RÉPONSE (AJAX)                 //
    // ========================================== //
    public function repondre_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('tickets', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $ticket_id = $this->input->post('ticket_id');
        $message = $this->input->post('message');
        $est_interne = $this->input->post('est_interne') ? 1 : 0;

        if (empty($message)) {
            echo json_encode(['success' => false, 'message' => 'Le message est requis']);
            return;
        }

        $reponse_data = array(
            'ticket_id' => $ticket_id,
            'staff_id' => $this->session->userdata('admin_id') ?? 1,
            'message' => $message,
            'est_interne' => $est_interne,
            'created_at' => date('Y-m-d H:i:s')
        );

        $id = $this->tickets_model->add_reponse($reponse_data);

        if ($id) {
            // Changer le statut si demandé
            if ($this->input->post('statut_id')) {
                $this->tickets_model->changer_statut($ticket_id, $this->input->post('statut_id'));
            }

            echo json_encode(['success' => true, 'message' => 'Réponse ajoutée avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout de la réponse']);
        }
    }

    // ========================================== //
    // CHANGER LE STATUT D'UN TICKET              //
    // ========================================== //
    public function changer_statut($id, $statut_id) {
        if (!$this->rbac->hasPrivilege('tickets', 'can_edit')) {
            access_denied();
        }

        $this->tickets_model->changer_statut($id, $statut_id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Statut du ticket modifié avec succès</div>');
        redirect('admin/tickets');
    }

    // ========================================== //
    // SUPPRESSION D'UN TICKET                    //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('tickets', 'can_delete')) {
            access_denied();
        }

        $this->tickets_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/tickets');
    }

    // ========================================== //
    // TÉLÉCHARGER UN FICHIER                     //
    // ========================================== //
    public function download($filename) {
        $filepath = $this->upload_path . $filename;

        if (file_exists($filepath)) {
            $this->load->helper('download');
            $data = file_get_contents($filepath);
            force_download($filename, $data);
        } else {
            show_404();
        }
    }

    // ========================================== //
    // DÉTAILS D'UN TICKET (MODAL)                //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('tickets', 'can_view')) {
            access_denied();
        }

        $data['ticket'] = $this->tickets_model->get_by_id($id);
        $data['reponses'] = $this->tickets_model->get_reponses($id);
        $data['statuts'] = $this->tickets_model->get_statuts();
        $this->load->view('admin/frontoffice/tickets_details', $data);
    }

    // ========================================== //
    // UPLOAD DE FICHIER                          //
    // ========================================== //
    private function upload_file($ticket_id, $update = false) {
        if (!isset($_FILES["fichier"]) || empty($_FILES['fichier']['name'])) {
            return false;
        }

        // Créer le dossier s'il n'existe pas
        if (!is_dir($this->upload_path)) {
            mkdir($this->upload_path, 0777, true);
        }

        $file = $_FILES["fichier"];
        $fileInfo = pathinfo($file["name"]);
        $extension = strtolower($fileInfo['extension']);
        $filename = 'ticket_' . $ticket_id . '_' . time() . '.' . $extension;
        $file_size = $file["size"];
        $file_type = $extension;

        if (move_uploaded_file($file["tmp_name"], $this->upload_path . $filename)) {
            if ($update) {
                // Supprimer l'ancien fichier
                $old_ticket = $this->tickets_model->get_by_id($ticket_id);
                if ($old_ticket && !empty($old_ticket['fichier'])) {
                    $old_path = $this->upload_path . $old_ticket['fichier'];
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
            }

            // Mettre à jour les informations du fichier
            $data = array(
                'fichier' => $filename
            );
            $this->tickets_model->update($ticket_id, $data);
            return true;
        }
        return false;
    }

    // ========================================== //
    // EXPORT EXCEL (CSV)                         //
    // ========================================== //
    public function export_excel() {
        $statut = $this->input->get('statut');
        $priorite = $this->input->get('priorite');
        $categorie = $this->input->get('categorie');
        $assigne = $this->input->get('assigne');

        $data = $this->tickets_model->get_filtered($statut, $priorite, $categorie, $assigne);

        $filename = 'tickets_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, ['#', 'Numéro', 'Titre', 'Catégorie', 'Priorité', 'Statut', 'Assigné à', 'Date création']);

        $i = 1;
        foreach ($data as $item) {
            fputcsv($output, [
                $i++,
                $item['ticket_number'] ?? '',
                $item['titre'] ?? '',
                $item['categorie_nom'] ?? '',
                $item['priorite_nom'] ?? '',
                $item['statut_nom'] ?? '',
                isset($item['assigne_prenom']) ? $item['assigne_prenom'] . ' ' . $item['assigne_nom'] : '',
                !empty($item['date_creation']) ? date('d/m/Y H:i', strtotime($item['date_creation'])) : ''
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
        $priorite = $this->input->get('priorite');
        $categorie = $this->input->get('categorie');
        $assigne = $this->input->get('assigne');

        $data['tickets'] = $this->tickets_model->get_filtered($statut, $priorite, $categorie, $assigne);
        $data['title'] = 'Liste des tickets';
        $data['date_generated'] = date('d/m/Y H:i');
        $data['stats'] = $this->tickets_model->get_stats();
        $data['statuts'] = $this->tickets_model->get_statuts();
        $data['priorites'] = $this->tickets_model->get_priorites();

        $html = $this->load->view('admin/frontoffice/tickets_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('tickets_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('tickets_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }
}