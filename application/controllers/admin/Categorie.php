<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Categorie extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("Visitors_model");
    }

    function index() {

        if (!$this->rbac->hasPrivilege('categorie_salaire', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/categorie');
        $this->form_validation->set_rules('categorie', $this->lang->line('purpose'), 'required');
        $this->form_validation->set_rules('salaire', $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_handle_upload[file]');

        if ($this->form_validation->run() == FALSE) {
            $data['categorie_list'] = $this->Visitors_model->categorie_listes();
            $data['Purpose'] = $this->Visitors_model->getPurpose();
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/categorieview', $data);
            $this->load->view('layout/footer');
        } else {
            $visitors = array(
                'categorie' => $this->input->post('categorie'),
                'salaire' => $this->input->post('salaire'),

                'status' => $this->input->post('status'),

                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),

                'note' => $this->input->post('note')
            );

            $visitor_id = $this->Visitors_model->categorie($visitors);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $visitor_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/files/" . $img_name);
                $this->Visitors_model->image_adds($visitor_id, $img_name);
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/categorie');
        }
    }

    public function delete($id) {
        if (!$this->rbac->hasPrivilege('categorie_salaire', 'can_delete')) {
            access_denied();
        }

        $this->Visitors_model->deletecate($id);
    }

    public function edit($id) {
        if (!$this->rbac->hasPrivilege('categorie_salaire', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('categorie', $this->lang->line('purpose'), 'required');

        $this->form_validation->set_rules('salaire', $this->lang->line('name'), 'required');

        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_handle_upload[file]');
        if ($this->form_validation->run() == FALSE) {

            $data['Purpose'] = $this->Visitors_model->getPurpose();
            $data['categorie_list'] = $this->Visitors_model->categorie_listes();
            $data['categorie_data'] = $this->Visitors_model->categorie_listes($id);
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/categorieeditview', $data);
            $this->load->view('layout/footer');
        } else {

            $categorie = array(
                'categorie' => $this->input->post('categorie'),
                'salaire' => $this->input->post('salaire'),

                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))), 'in_time' => $this->input->post('time'),

            );
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);

                $img_name = 'id' . $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/categorie/" . $img_name);
                $this->Visitors_model->image_updates($id, $img_name);
            }
            $this->Visitors_model->updatecat($id, $categorie);
            redirect('admin/categorie');
        }
    }

    public function details($id) {
        if (!$this->rbac->hasPrivilege('categorie_salaire', 'can_view')) {
            access_denied();
        }

        $data['data'] = $this->Visitors_model->visitors_lists($id);
        $this->load->view('admin/frontoffice/Filesmodelview', $data);
    }

    public function download($documents) {
        $this->load->helper('download');
        $filepath = "./uploads/front_office/files/" . $documents;
        $data = file_get_contents($filepath);
        $name = $documents;
        force_download($name, $data);
    }

    public function imagedelete($id, $image) {
        if (!$this->rbac->hasPrivilege('categorie_salaire', 'can_delete')) {
            access_denied();
        }
        $this->Visitors_model->image_delete($id, $image);
    }

    public function imagesdelete($id, $image) {
        if (!$this->rbac->hasPrivilege('categorie_salaire', 'can_delete')) {
            access_denied();
        }
        $this->Visitors_model->images_deletecat($id, $image);
    }

    public function check_default($post_string) {
        return $post_string == "" ? FALSE : TRUE;
    }

    public function handle_upload($str,$var)
    {

        $image_validate = $this->config->item('file_validate');
        $result = $this->filetype_model->get();
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type         = $_FILES[$var]['type'];
            $file_size         = $_FILES[$var]["size"];
            $file_name         = $_FILES[$var]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
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
                $this->form_validation->set_message('handle_upload', "File Type / Extension Error Uploading  Image");
                return false;
            }

            return true;
        }
        return true;

    }

}
