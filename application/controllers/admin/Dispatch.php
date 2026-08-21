<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dispatch extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("Dispatch_model");
        $this->upload_path = "./uploads/front_office/dispatch_receive/";
    }

    // ========================================== //
    // INDEX - LISTE DES DISPATCH                 //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('postal_dispatch', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'front_office');
        $this->session->set_userdata('sub_menu', 'admin/dispatch');

        $data['DispatchList'] = $this->Dispatch_model->dispatch_list();
        $data['stats'] = $this->Dispatch_model->get_stats('dispatch');

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/dispatchview', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // AJOUTER UN DISPATCH (AJAX)                 //
    // ========================================== //
    public function add_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('postal_dispatch', 'can_add')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $this->form_validation->set_rules('to_title', $this->lang->line('to_title'), 'required');
        $this->form_validation->set_rules('from', $this->lang->line('from_title'), 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $dispatch_data = array(
            'reference_no' => $this->input->post('ref_no'),
            'to_title' => $this->input->post('to_title'),
            'address' => $this->input->post('address'),
            'note' => $this->input->post('note'),
            'from_title' => $this->input->post('from'),
            'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
            'type' => 'dispatch'
        );

        $dispatch_id = $this->Dispatch_model->insert('dispatch_receive', $dispatch_data);

        if ($dispatch_id) {
            // Gestion du fichier
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $this->upload_file($dispatch_id);
            }

            echo json_encode(['success' => true, 'message' => 'Dispatch ajouté avec succès', 'id' => $dispatch_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du dispatch']);
        }
    }

    // ========================================== //
    // RÉCUPÉRER LES DONNÉES D'UN DISPATCH (AJAX) //
    // ========================================== //
    public function get_dispatch_data($id) {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('postal_dispatch', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            return;
        }

        $data = $this->Dispatch_model->dis_rec_data($id, 'dispatch');

        if ($data && !empty($data)) {
            $dispatch = [
                'id' => (int)$data['id'],
                'to_title' => (string)($data['to_title'] ?? ''),
                'reference_no' => (string)($data['reference_no'] ?? ''),
                'from_title' => (string)($data['from_title'] ?? ''),
                'address' => (string)($data['address'] ?? ''),
                'note' => (string)($data['note'] ?? ''),
                'date' => (string)($data['date'] ?? ''),
                'image' => (string)($data['image'] ?? '')
            ];

            echo json_encode([
                'success' => true,
                'dispatch' => $dispatch
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Dispatch non trouvé']);
        }
    }

    // ========================================== //
    // METTRE À JOUR UN DISPATCH (AJAX)           //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('postal_dispatch', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('to_title', $this->lang->line('to_title'), 'required');
        $this->form_validation->set_rules('from', $this->lang->line('from_title'), 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $dispatch_data = array(
            'reference_no' => $this->input->post('ref_no'),
            'to_title' => $this->input->post('to_title'),
            'address' => $this->input->post('address'),
            'note' => $this->input->post('note'),
            'from_title' => $this->input->post('from'),
            'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
            'type' => 'dispatch'
        );

        // Gestion du fichier
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $this->upload_file($id, true);
        }

        $result = $this->Dispatch_model->update_dispatch('dispatch_receive', $id, 'dispatch', $dispatch_data);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Dispatch mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // ÉDITION D'UN DISPATCH (PAGE SÉPARÉE)       //
    // ========================================== //
    function editdispatch($id) {
        if (!$this->rbac->hasPrivilege('postal_dispatch', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('to_title', 'To Title', 'required');
        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_handle_upload[file]');

        if ($this->form_validation->run() == FALSE) {
            $data['DispatchList'] = $this->Dispatch_model->dispatch_list();
            $data['Dispatch_data'] = $this->Dispatch_model->dis_rec_data($id, 'dispatch');
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/dispatchedit', $data);
            $this->load->view('layout/footer');
        } else {
            $dispatch = array(
                'reference_no' => $this->input->post('ref_no'),
                'to_title' => $this->input->post('to_title'),
                'address' => $this->input->post('address'),
                'note' => $this->input->post('note'),
                'from_title' => $this->input->post('from'),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'type' => 'dispatch'
            );

            $this->Dispatch_model->update_dispatch('dispatch_receive', $id, 'dispatch', $dispatch);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/dispatch_receive/" . $img_name);
                $this->Dispatch_model->image_update('dispatch', $id, $img_name);
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/dispatch');
        }
    }

    // ========================================== //
    // TÉLÉCHARGER UN DOCUMENT                    //
    // ========================================== //
    public function download($documents) {
        $this->load->helper('download');
        $filepath = "./uploads/front_office/dispatch_receive/" . $documents;

        if (file_exists($filepath)) {
            $data = file_get_contents($filepath);
            force_download($documents, $data);
        } else {
            show_404();
        }
    }

    // ========================================== //
    // SUPPRESSION D'UN DISPATCH                  //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('postal_dispatch', 'can_delete')) {
            access_denied();
        }

        $this->Dispatch_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/dispatch');
    }

    // ========================================== //
    // SUPPRESSION DE L'IMAGE                     //
    // ========================================== //
    public function imagedelete($id, $image) {
        if (!$this->rbac->hasPrivilege('postal_dispatch', 'can_delete')) {
            access_denied();
        }

        $this->Dispatch_model->image_delete($id, $image);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/dispatch');
    }

    // ========================================== //
    // DETAILS D'UN DISPATCH (MODAL)              //
    // ========================================== //
    public function details($id, $type) {
        if (!$this->rbac->hasPrivilege('postal_dispatch', 'can_view')) {
            access_denied();
        }

        $data['data'] = $this->Dispatch_model->dis_rec_data($id, $type);
        $this->load->view('admin/frontoffice/dispacthreceviemodel', $data);
    }

    // ========================================== //
    // EXPORT EXCEL (CSV)                         //
    // ========================================== //
    public function export_excel() {
        $from = $this->input->get('from');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $data = $this->Dispatch_model->get_filtered('dispatch', $from, $date_from, $date_to);

        $filename = 'dispatch_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, ['Destinataire', 'Référence', 'Expéditeur', 'Date', 'Adresse', 'Note']);

        foreach ($data as $item) {
            fputcsv($output, [
                $item['to_title'] ?? '',
                $item['reference_no'] ?? '',
                $item['from_title'] ?? '',
                !empty($item['date']) ? date('d/m/Y', strtotime($item['date'])) : '',
                $item['address'] ?? '',
                $item['note'] ?? ''
            ]);
        }

        fclose($output);
        exit;
    }

    // ========================================== //
    // EXPORT PDF                                 //
    // ========================================== //
    public function export_pdf() {
        $from = $this->input->get('from');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $data['dispatches'] = $this->Dispatch_model->get_filtered('dispatch', $from, $date_from, $date_to);
        $data['title'] = 'Liste des dispatches';
        $data['date_generated'] = date('d/m/Y H:i');

        $html = $this->load->view('admin/frontoffice/dispatch_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('dispatch_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('dispatch_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }

    // ========================================== //
    // UPLOAD DE FICHIER                          //
    // ========================================== //
    private function upload_file($dispatch_id, $update = false) {
        if (!isset($_FILES["file"]) || empty($_FILES['file']['name'])) {
            return false;
        }

        // Créer le dossier s'il n'existe pas
        if (!is_dir($this->upload_path)) {
            mkdir($this->upload_path, 0777, true);
        }

        $fileInfo = pathinfo($_FILES["file"]["name"]);
        $extension = strtolower($fileInfo['extension']);
        $img_name = 'id' . $dispatch_id . '.' . $extension;

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $this->upload_path . $img_name)) {
            if ($update) {
                // Supprimer l'ancienne image
                $old_image = $this->Dispatch_model->get_image('dispatch', $dispatch_id);
                if ($old_image && file_exists($this->upload_path . $old_image)) {
                    unlink($this->upload_path . $old_image);
                }
                $this->Dispatch_model->image_update('dispatch', $dispatch_id, $img_name);
            } else {
                $this->Dispatch_model->image_add('dispatch', $dispatch_id, $img_name);
            }
            return true;
        }
        return false;
    }

    // ========================================== //
    // VALIDATION PERSONNALISÉE                   //
    // ========================================== //
    public function handle_upload($str, $var) {
        $image_validate = $this->config->item('file_validate');
        $result = $this->filetype_model->get();

        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {
            $file_type = $_FILES[$var]['type'];
            $file_size = $_FILES[$var]["size"];
            $file_name = $_FILES[$var]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES[$var]['tmp_name'])) {
                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'File Type Not Allowed');
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'Extension Not Allowed');
                    return false;
                }
                if ($file_size > $result->file_size) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', "File Type / Extension Error Uploading Image");
                return false;
            }
            return true;
        }
        return true;
    }
}