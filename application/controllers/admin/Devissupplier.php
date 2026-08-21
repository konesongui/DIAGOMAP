<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Devissupplier extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('Customlib');
        $this->config->load('app-config');
        $this->load->library("datatables");
        $this->load->helper('url');
    }

    function index() {
        if (!$this->rbac->hasPrivilege('quote_supplier', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'devissupplier/index');
        $data['title'] = 'Item Supplier List';
        $devissupplier_result = $this->devissupplier_model->get();
        $data['devissupplierlist'] = $devissupplier_result;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/devissupplier/devissupplierList', $data);
        $this->load->view('layout/footer', $data);
    }

    function delete($id) {
        if (!$this->rbac->hasPrivilege('quote_supplier', 'can_delete')) {
            access_denied();
        }
        $data['title'] = 'Item Supplier List';
        $this->devissupplier_model->remove($id);
        redirect('admin/devissupplier/index');
    }



    function create() {
        if (!$this->rbac->hasPrivilege('quote_supplier', 'can_add')) {
            access_denied();
        }
        $data['title'] = 'Add Item supplier';
        $itemsupplier_result = $this->itemsupplier_model->get();
        $data['itemsupplierlist'] = $itemsupplier_result;

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_handle_upload[file]');

        //$this->form_validation->set_rules('montant', $this->lang->line('phone'), 'trim|numeric|xss_clean');
        //$this->form_validation->set_rules('', $this->lang->line('email'), 'trim|xss_clean|valid_email');
        //$this->form_validation->set_rules('contact_person_phone', $this->lang->line('phone'), 'trim|numeric|xss_clean');
        //$this->form_validation->set_rules('contact_person_email', $this->lang->line('email'), 'trim|xss_clean|valid_email');


        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/devissupplier/formsupplierList', $data);
            $this->load->view('layout/footer', $data);
        } else {
                $data = array(
                'ref' => $this->input->post('ref'),
                'montant' => $this->input->post('montant'),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'article' => $this->input->post('article'),
                'name' => $this->input->post('name'),
                    'image' => $this->input->post('file'),
                    'payment_status' => $this->input->post('payment_status'), // Statut impayé par défaut


               /* 'contact_person_email' => $this->input->post('contact_person_email'),
                'description' => $this->input->post('description'),*/
              //  'status' => $this->input->post('status'),
            );
            $insert_id = $this->devissupplier_model->add($data);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/files/" . $img_name);
                $this->devissupplier_model->image_adds($insert_id, $img_name);
            }


                $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
                redirect('admin/devissupplier/index');



        }
    }
    public function view_pdf($filename) {
        // Vérifier les permissions (exemple)
        if (!$this->rbac->hasPrivilege('quote_supplier', 'can_view')) {
            show_404();
        }
        $file_path = FCPATH . 'uploads/front_office/files/' . $filename;
        if (!file_exists($file_path)) {
            show_404();
        }
        // Forcer le type MIME pour PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        readfile($file_path);
        exit;
    }


    public function markAsValidated($id) {
        if (!$this->rbac->hasPrivilege('quote_supplier', 'can_edit')) {
            access_denied();
        }

        $result = $this->devissupplier_model->update_payment_status($id, 'validé');

        if ($result) {
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Devis validé avec succès.</div>');
        } else {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la validation.</div>');
        }

        redirect('admin/devissupplier/index');
    }

    public function download($documents) {
        $this->load->helper('download');
        $filepath = "./uploads/front_office/files/" . $documents;
        $data = file_get_contents($filepath);
        $name = $documents;
        force_download($name, $data);
    }

    public function details($id) {
        if (!$this->rbac->hasPrivilege('item', 'can_view')) {
            access_denied();
        }

        $details_result = $this->devissupplier_model->get($id);
        $data['data'] = $details_result;
        $this->load->view('admin/devissupplier/pdfmodelview', $data);


    }


    public function handle_upload()
    {

        $image_validate = $this->config->item('file_validate');
        $result = $this->filetype_model->get();
        if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {
            $file_type         = $_FILES["documents"]['type'];
            $file_size         = $_FILES["documents"]["size"];
            $file_name         = $_FILES["documents"]["name"];
            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES['documents']['tmp_name'])) {

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


    function edit($id) {
        if (!$this->rbac->hasPrivilege('quote_supplier', 'can_edit')) {
            access_denied();
        }
        $data['title'] = 'Edit Item Supplier';
        $itemsupplier_result = $this->itemsupplier_model->get();
        $data['itemsupplierlist'] = $itemsupplier_result;
        $data['id'] = $id;
        $store = $this->devissupplier_model->get($id);
        $data['devissupplier'] = $store;

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        //$this->form_validation->set_rules('phone', $this->lang->line('phone'), 'trim|numeric|xss_clean');
        //$this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|xss_clean|valid_email');
        //$this->form_validation->set_rules('contact_person_phone', $this->lang->line('phone'), 'trim|numeric|xss_clean');
        //$this->form_validation->set_rules('contact_person_email', $this->lang->line('email'), 'trim|xss_clean|valid_email');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/devissupplier/devissupplierEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {


            $data = array(
                'id' => $id,
                'name' => $this->input->post('name'),
                'ref' => $this->input->post('ref'),
                'image' => $this->input->post('file'),
                'article' => $this->input->post('article'),
                'montant' => $this->input->post('montant'),
                'payment_status' => $this->input->post('payment_status'), // Nouveau champ
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),

            );

            $insert_id = $this->devissupplier_model->add($data);
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/files/" . $img_name);
                $this->devissupplier_model->image_adds($insert_id, $img_name);
            }
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/devissupplier/index');
        }
    }

}

?>