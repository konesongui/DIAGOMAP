<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Couriers extends Admin_Controller {

    private $upload_path;

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("couriers_model");
        $this->load->helper('download');
        $this->upload_path = "./uploads/front_office/couriers/";
    }

    // ========================================== //
    // INDEX - LISTE DES COURRIERS                //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('courriers', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'receptioniste');
        $this->session->set_userdata('sub_menu', 'admin/couriers');

        $data['courier_list'] = $this->couriers_model->get_all();
        $data['stats'] = $this->couriers_model->get_stats();
        $data['courier_types'] = $this->couriers_model->get_courier_types();
        $data['statuses'] = ['pending' => 'En attente', 'processed' => 'Traité', 'archived' => 'Archivé'];

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/filesview', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // AJOUTER UN COURRIER (AJAX)                 //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('courriers', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('courier_type', 'Type de courrier', 'required');
        $this->form_validation->set_rules('sender_name', 'Nom', 'required');
        $this->form_validation->set_rules('date_received', 'Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $courier_data = array(
            'courier_type' => $this->input->post('courier_type'),
            'sender_name' => $this->input->post('sender_name'),
            'reference' => $this->input->post('reference'),
            'address' => $this->input->post('address'),
            'date_received' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_received'))),
            'description' => $this->input->post('description'),
            'note' => $this->input->post('note'),
            'status' => $this->input->post('status') ?? 'pending',
            'deleted' => 0
        );

        $courier_id = $this->couriers_model->add($courier_data);

        if ($courier_id) {
            // Gestion du fichier attaché
            if (isset($_FILES["attachment"]) && !empty($_FILES['attachment']['name'])) {
                $this->upload_file($courier_id);
            }

            echo json_encode(['success' => true, 'message' => 'Courrier ajouté avec succès', 'id' => $courier_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du courrier']);
        }
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UN COURRIER (AJAX) //
    // ========================================== //
    public function get_courier_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('courriers', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            return;
        }

        $data = $this->couriers_model->get_by_id($id);

        if ($data && !empty($data)) {
            $courier = [
                'id' => (int)$data['id'],
                'courier_type' => (string)($data['courier_type'] ?? ''),
                'sender_name' => (string)($data['sender_name'] ?? ''),
                'reference' => (string)($data['reference'] ?? ''),
                'address' => (string)($data['address'] ?? ''),
                'date_received' => (string)($data['date_received'] ?? ''),
                'description' => (string)($data['description'] ?? ''),
                'note' => (string)($data['note'] ?? ''),
                'status' => (string)($data['status'] ?? 'pending'),
                'attachment' => (string)($data['attachment'] ?? '')
            ];

            echo json_encode([
                'success' => true,
                'courier' => $courier
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Courrier non trouvé']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UN COURRIER (AJAX)           //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('courriers', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('courier_type', 'Type de courrier', 'required');
        $this->form_validation->set_rules('sender_name', 'Nom', 'required');
        $this->form_validation->set_rules('date_received', 'Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $courier_data = array(
            'courier_type' => $this->input->post('courier_type'),
            'sender_name' => $this->input->post('sender_name'),
            'reference' => $this->input->post('reference'),
            'address' => $this->input->post('address'),
            'date_received' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_received'))),
            'description' => $this->input->post('description'),
            'note' => $this->input->post('note'),
            'status' => $this->input->post('status') ?? 'pending'
        );

        // Gestion du fichier
        if (isset($_FILES["attachment"]) && !empty($_FILES['attachment']['name'])) {
            $this->upload_file($id, true);
        }

        $result = $this->couriers_model->update($id, $courier_data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Courrier mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // SUPPRESSION D'UN COURRIER                  //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('courriers', 'can_delete')) {
            access_denied();
        }

        $courier = $this->couriers_model->get_by_id($id);
        if ($courier && !empty($courier['attachment'])) {
            $this->delete_file($courier['attachment']);
        }

        $this->couriers_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/frontoffice/filesview');
    }

    // ========================================== //
    // SUPPRESSION DU FICHIER ATTACHÉ             //
    // ========================================== //
    public function delete_attachment($id) {
        if (!$this->rbac->hasPrivilege('courriers', 'can_edit')) {
            access_denied();
        }

        $courier = $this->couriers_model->get_by_id($id);
        if ($courier && !empty($courier['attachment'])) {
            $this->delete_file($courier['attachment']);
            $this->couriers_model->update($id, ['attachment' => '']);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Fichier supprimé avec succès</div>');
        }

        redirect('admin/frontoffice/filesview');
    }

    // ========================================== //
    // TÉLÉCHARGER UN FICHIER                     //
    // ========================================== //
    public function download($filename) {
        $filepath = $this->upload_path . $filename;

        if (file_exists($filepath)) {
            $data = file_get_contents($filepath);
            force_download($filename, $data);
        } else {
            show_404();
        }
    }

    // ========================================== //
    // DETAILS D'UN COURRIER (MODAL)              //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('courriers', 'can_view')) {
            access_denied();
        }

        $data['courier'] = $this->couriers_model->get_by_id($id);
        $this->load->view('admin/frontoffice/couriersmodelview', $data);
    }

    // ========================================== //
    // EXPORT EXCEL (CSV)                         //
    // ========================================== //
    public function export_excel() {
        $courier_type = $this->input->get('type');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        $status = $this->input->get('status');

        $data = $this->couriers_model->get_filtered($courier_type, $date_from, $date_to, $status);

        $filename = 'courriers_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        // En-têtes
        fputcsv($output, [
            'Type',
            'Nom',
            'Référence',
            'Date',
            'Adresse',
            'Description',
            'Note',
            'Statut'
        ]);

        $status_labels = [
            'pending' => 'En attente',
            'processed' => 'Traité',
            'archived' => 'Archivé'
        ];

        foreach ($data as $courier) {
            fputcsv($output, [
                $courier['courier_type'] ?? '',
                $courier['sender_name'] ?? '',
                $courier['reference'] ?? '',
                !empty($courier['date_received']) ? date('d/m/Y', strtotime($courier['date_received'])) : '',
                $courier['address'] ?? '',
                $courier['description'] ?? '',
                $courier['note'] ?? '',
                $status_labels[$courier['status']] ?? $courier['status']
            ]);
        }

        fclose($output);
        exit;
    }



    // ========================================== //
    // EXPORT PDF                                 //
    // ========================================== //
    public function export_pdf() {
        $courier_type = $this->input->get('type');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        $status = $this->input->get('status');

        $data['couriers'] = $this->couriers_model->get_filtered($courier_type, $date_from, $date_to, $status);
        $data['stats'] = $this->couriers_model->get_stats();
        $data['title'] = 'Liste des courriers';
        $data['date_generated'] = date('d/m/Y H:i');

        $html = $this->load->view('admin/frontoffice/couriers_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('courriers_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('courriers_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }

    // ========================================== //
    // UPLOAD DE FICHIER                          //
    // ========================================== //
    private function upload_file($courier_id, $update = false) {
        if (!isset($_FILES["attachment"]) || empty($_FILES['attachment']['name'])) {
            return false;
        }

        // Créer le dossier s'il n'existe pas
        if (!is_dir($this->upload_path)) {
            mkdir($this->upload_path, 0777, true);
        }

        $fileInfo = pathinfo($_FILES["attachment"]["name"]);
        $extension = strtolower($fileInfo['extension']);
        $filename = 'courier_' . $courier_id . '_' . time() . '.' . $extension;

        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $this->upload_path . $filename)) {
            if ($update) {
                // Supprimer l'ancien fichier
                $old_file = $this->couriers_model->get_attachment($courier_id);
                if ($old_file && file_exists($this->upload_path . $old_file)) {
                    unlink($this->upload_path . $old_file);
                }
            }
            $this->couriers_model->update($courier_id, ['attachment' => $filename]);
            return true;
        }
        return false;
    }

    // ========================================== //
    // SUPPRIMER UN FICHIER                       //
    // ========================================== //
    private function delete_file($filename) {
        $filepath = $this->upload_path . $filename;
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }

    // ========================================== //
    // DATATABLE AJAX                             //
    // ========================================== //
    // ========================================== //
// DATATABLE AJAX                             //
// ========================================== //
    public function get_courier_list() {
        // Récupérer les filtres
        $filter_type = $this->input->post('filter_type');
        $filter_status = $this->input->post('filter_status');
        $date_from = $this->input->post('date_from');
        $date_to = $this->input->post('date_to');

        $courier_list = $this->couriers_model->get_datatable($filter_type, $filter_status, $date_from, $date_to);
        $m = json_decode($courier_list);
        $dt_data = array();

        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                // Badge type
                $badgeClass = 'other';
                $type = strtolower($value->courier_type ?? '');
                if (strpos($type, 'reçu') !== false || strpos($type, 'incoming') !== false) $badgeClass = 'incoming';
                elseif (strpos($type, 'envoi') !== false || strpos($type, 'outgoing') !== false) $badgeClass = 'outgoing';
                elseif (strpos($type, 'interne') !== false || strpos($type, 'internal') !== false) $badgeClass = 'internal';

                $type_html = "<span class='badge-courier-type " . $badgeClass . "'>
                            <i class='fa " .
                    ($badgeClass == 'incoming' ? 'fa-arrow-circle-down' :
                        ($badgeClass == 'outgoing' ? 'fa-arrow-circle-up' :
                            ($badgeClass == 'internal' ? 'fa-exchange' : 'fa-envelope-o'))) . "'>
                            </i> " . htmlspecialchars($value->courier_type) . "
                        </span>";

                // Statut
                $status_labels = [
                    'pending' => 'En attente',
                    'processed' => 'Traité',
                    'archived' => 'Archivé'
                ];
                $status_class = [
                    'pending' => 'pending',
                    'processed' => 'processed',
                    'archived' => 'archived'
                ];
                $status_html = "<span class='badge-status " . ($status_class[$value->status] ?? 'pending') . "'>" .
                    ($status_labels[$value->status] ?? $value->status) . "</span>";

                // Boutons d'action
                $viewbtn = "<a data-placement='center' 
                       onclick='getRecord(" . $value->id . ")' 
                       class='btn btn-default btn-xs' 
                       data-target='#courierdetails' 
                       data-toggle='modal' 
                       title='Voir'>
                          <i class='fa fa-eye'></i>
                       </a>";

                $editbtn = '';
                if ($this->rbac->hasPrivilege('courriers', 'can_edit')) {
                    $editbtn = "<a onclick='openEditModal(" . $value->id . ")' 
                          class='btn btn-default btn-xs' 
                          data-toggle='tooltip' 
                          data-placement='center' 
                          title='Modifier' 
                          style='cursor:pointer;'>
                             <i class='fa fa-pencil'></i>
                       </a>";
                }

                $deletebtn = '';
                if ($this->rbac->hasPrivilege('courriers', 'can_delete')) {
                    $deletebtn = "<a onclick='confirmDelete(event, \"" . addslashes($value->sender_name) . "\", " . $value->id . ")' 
                             class='btn btn-default btn-xs' 
                             data-placement='center' 
                             title='Supprimer' 
                             style='cursor:pointer;'>
                                <i class='fa fa-trash'></i>
                          </a>";
                }

                $downloadbtn = '';
                if (!empty($value->attachment)) {
                    $downloadbtn = "<a href='" . base_url('admin/couriers/download/' . $value->attachment) . "' 
                              class='btn btn-default btn-xs' 
                              data-toggle='tooltip' 
                              data-placement='center' 
                              title='Télécharger'>
                                 <i class='fa fa-download'></i>
                              </a>";
                }

                $row = array();
                $row[] = $type_html;
                $row[] = $value->sender_name;
                $row[] = $value->reference ?? '';
                $row[] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date_received));
                $row[] = $value->address ?? '';
                $row[] = $status_html;
                $row[] = $viewbtn . ' ' . $editbtn . ' ' . $deletebtn . ' ' . $downloadbtn;

                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw" => intval($m->draw),
            "recordsTotal" => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data" => $dt_data,
        );

        echo json_encode($json_data);
    }

    // ========================================== //
    // VALIDATION PERSONNALISÉE                   //
    // ========================================== //
    public function check_default($post_string) {
        return $post_string == "" ? FALSE : TRUE;
    }
}