<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Fichiers extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        if (!$this->rbac->hasPrivilege('section', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Academics');
        $this->session->set_userdata('sub_menu', 'fichiers/index');
        $data['title'] = 'Section List';

        $section_result      = $this->section_model->get();
        $data['sectionlist'] = $section_result;
        $this->form_validation->set_rules('section', $this->lang->line('section'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('fichiers/fichiersList', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'section' => $this->input->post('section'),
                'type_file' => $this->input->post('type_file'),
                'date' => $this->input->post('date'),
                'description' => $this->input->post('description'),
                'documents' => $this->input->post('documents'),
            );
            $this->section_model->add($data);

            if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {
                $fileInfo = pathinfo($_FILES["documents"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["documents"]["tmp_name"], "./uploads/school_income/" . $img_name);
                $data_img = array('id' => $id, 'documents' => 'uploads/school_income/' . $img_name);
                $this->income_model->add($data_img);
            }
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('fichiers/index');
        }

    }


    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('section', 'can_view')) {
            access_denied();
        }
        $data['title']   = 'Section List';
        $section         = $this->section_model->get($id);
        $data['section'] = $section;
        $data['type_file'] = $type_file;
        $data['date'] = $date;
        $data['description'] = $description;
        $data['documents'] = $documents;
        $this->load->view('layout/header', $data);
        $this->load->view('fichiers/fichiersShow', $data);
        $this->load->view('layout/footer', $data);
    }

    public function handle_upload()
    {

        $image_validate = $this->config->item('file_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {

            $file_type = $_FILES["documents"]['type'];
            $file_size = $_FILES["documents"]["size"];
            $file_name = $_FILES["documents"]["name"];

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

    public function download($documents)
    {
        $this->load->helper('download');
        $filepath = "./uploads/school_income/" . $this->uri->segment(6);
        $data     = file_get_contents($filepath);
        $name     = $this->uri->segment(6);
        force_download($name, $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('section', 'can_delete')) {
            access_denied();
        }
        $data['title'] = 'Section List';
        $this->section_model->remove($id);

        $student_delete=$this->student_model->getUndefinedStudent();
        if(!empty($student_delete)){
            $delte_student_array=array();
            foreach ($student_delete as $student_key => $student_value) {

                $delte_student_array[]=$student_value->id;
            }
            $this->student_model->bulkdelete($delte_student_array);
        }        
        redirect('fichiers/index');
    }

    public function getByClass()
    {
        $class_id = $this->input->get('class_id');
        $data     = $this->section_model->getClassBySection($class_id);
        echo json_encode($data);
    }

    public function getClassTeacherSection()
    {
        $class_id = $this->input->get('class_id');
        $data     = array();
        $userdata = $this->customlib->getUserData();
        if (($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
            $id    = $userdata["id"];
            $query = $this->db->where("staff_id", $id)->where("class_id", $class_id)->get("class_teacher");

            if ($query->num_rows() > 0) {

                $data = $this->section_model->getClassTeacherSection($class_id);
            } else {

                $data = $this->section_model->getSubjectTeacherSection($class_id, $id);
            }
        } else {
            $data = $this->section_model->getClassBySection($class_id);
        }
        echo json_encode($data);
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('section', 'can_edit')) {
            access_denied();
        }
        $data['title']       = 'Section List';
        $section_result      = $this->section_model->get();
        $data['sectionlist'] = $section_result;
        $data['title']       = 'Edit Section';
        $data['id']          = $id;
        $section             = $this->section_model->get($id);
        $data['section']     = $section;
        $this->form_validation->set_rules('section', $this->lang->line('section'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('fichiers/fichiersEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id'      => $id,
                'section' => $this->input->post('section'),
                'type_file' => $this->input->post('type_file'),
                'date' => $this->input->post('date'),
                'description' => $this->input->post('description'),
                'documents' => $this->input->post('documents'),
            );
            $this->section_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('fichiers/index');
        }
    }

}
