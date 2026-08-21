<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sitecontent extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('sitecontent_model');
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('superadmin', 'can_view')) {
            access_denied();
        }
        $data = $this->sitecontent_model->getContent();
        $this->load->view('layout/header', ['title' => 'Gestion du site']);
        $this->load->view('admin/sitecontent/index', ['content' => $data]);
        $this->load->view('layout/footer');
    }

    public function save()
    {
        if (!$this->rbac->hasPrivilege('superadmin', 'can_edit')) {
            access_denied();
        }

        // Determine entreprise_id similar to other controllers
        $entreprise_id = 0;
        if (isset($this->customlib) && method_exists($this->customlib, 'getUserData')) {
            $userdata = $this->customlib->getUserData();
            $entreprise_id = isset($userdata['entreprise_id']) ? (int)$userdata['entreprise_id'] : 0;
        }
        if ($entreprise_id == 0) {
            $entreprise_id = $this->session->userdata('entreprise_id') ?? 0;
        }

        $menus = $this->input->post('menu', true);
        $menus_out = [];
        if (is_array($menus)) {
            foreach ($menus as $m) {
                $title = trim($m['title'] ?? '');
                $url = trim($m['url'] ?? '');
                $ext = isset($m['ext']) && $m['ext'] ? true : false;
                $new_tab = isset($m['new_tab']) && $m['new_tab'] ? true : false;
                if ($title !== '') {
                    $menus_out[] = ['title' => $title, 'page_url' => $url, 'ext_url' => $ext, 'open_new_tab' => $new_tab];
                }
            }
        }

        $blocks = [];
        $block_titles = $this->input->post('block_title', true) ?? [];
        $block_contents = $this->input->post('block_content', true) ?? [];

        // Server-side validation for uploaded files
        $allowed_image_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_image_size = 2 * 1024 * 1024; // 2MB
        $allowed_video_types = ['video/mp4', 'video/webm', 'video/ogg'];
        $max_video_size = 50 * 1024 * 1024; // 50MB

        $errors = [];

        for ($i = 0; $i < count($block_titles); $i++) {
            $bt = trim($block_titles[$i]);
            $bc = trim($block_contents[$i]);
            if ($bt === '' && $bc === '') continue;
            $image_path = null;
            $video_path = null;
            // image file input names are image_0, image_1 ...
            $img_field = 'image_' . $i;
            $vid_field = 'video_' . $i;

            // Validate image if present
            if (isset($_FILES[$img_field]) && $_FILES[$img_field]['error'] == 0) {
                $file = $_FILES[$img_field];
                $mime = '';
                if (function_exists('mime_content_type')) {
                    $mime = @mime_content_type($file['tmp_name']);
                }
                if (empty($mime) && isset($file['type'])) {
                    $mime = $file['type'];
                }
                if (!in_array($mime, $allowed_image_types)) {
                    $errors[] = "Image #" . ($i+1) . " : type de fichier non autorisé ({$mime})";
                }
                if ($file['size'] > $max_image_size) {
                    $errors[] = "Image #" . ($i+1) . " : taille trop grande (max 2MB)";
                }
            }

            // Validate video if present
            if (isset($_FILES[$vid_field]) && $_FILES[$vid_field]['error'] == 0) {
                $file = $_FILES[$vid_field];
                $mime = '';
                if (function_exists('mime_content_type')) {
                    $mime = @mime_content_type($file['tmp_name']);
                }
                if (empty($mime) && isset($file['type'])) {
                    $mime = $file['type'];
                }
                if (!in_array($mime, $allowed_video_types)) {
                    $errors[] = "Vidéo #" . ($i+1) . " : type de fichier non autorisé ({$mime})";
                }
                if ($file['size'] > $max_video_size) {
                    $errors[] = "Vidéo #" . ($i+1) . " : taille trop grande (max 50MB)";
                }
            }

            // If validation passed, save files
            if (!isset($_FILES[$img_field]) || $_FILES[$img_field]['error'] != 0) {
                // no image
            } else {
                $r = $this->sitecontent_model->saveUploadedFile($img_field, 'image');
                if ($r) $image_path = $r;
            }
            if (!isset($_FILES[$vid_field]) || $_FILES[$vid_field]['error'] != 0) {
                // no video
            } else {
                $r = $this->sitecontent_model->saveUploadedFile($vid_field, 'video');
                if ($r) $video_path = $r;
            }

            $blocks[] = [
                'title' => $bt,
                'content' => $bc,
                'image' => $image_path,
                'video' => $video_path
            ];
        }

        // Handle validation errors
        if (!empty($errors)) {
            $msg = implode("\n", $errors);
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'errors' => $errors, 'message' => $msg]);
                return;
            } else {
                $this->session->set_flashdata('error', $msg);
                redirect('admin/sitecontent');
                return;
            }
        }

        $to_save = ['menus' => $menus_out, 'blocks' => $blocks];
        $ok = $this->sitecontent_model->saveContent($to_save);

        if ($this->input->is_ajax_request()) {
            if ($ok) {
                echo json_encode(['success' => true, 'message' => 'Contenu du site enregistré avec succès']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement']);
            }
            return;
        }

        if ($ok) {
            $this->session->set_flashdata('msg', 'Contenu du site enregistré avec succès');
        } else {
            $this->session->set_flashdata('error', 'Erreur lors de l\'enregistrement');
        }
        redirect('admin/sitecontent');
    }

}
