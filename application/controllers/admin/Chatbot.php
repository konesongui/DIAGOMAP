
<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Chatbot extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("Visitors_model");
    }

    public function send()
    {
        $json = $this->request->getJSON();
        $userMessage = trim($json->message ?? '');

        // Local-only assistant: simple rule-based responder (no external API required)
        if ($userMessage === '') {
            $reply = "Bonjour ! Comment puis-je vous aider aujourd'hui ?";
            return $this->response->setJSON(['reply' => $reply]);
        }

        $reply = $this->local_assistant_respond($userMessage);
        return $this->response->setJSON(['reply' => $reply]);

        // API key removed for local-only assistant. External API calls disabled.
        // $apiKey originally stored here has been removed to avoid using external services.

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $apiKey",
            "Content-Type: application/json"
        ]);

        $postData = json_encode([
            "model" => "gpt-3.5-turbo",
            "messages" => [
                ["role" => "system", "content" => "You are a helpful assistant."],
                ["role" => "user", "content" => $userMessage]
            ]
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        $reply = $data['choices'][0]['message']['content'] ?? 'Sorry, I didn\'t understand.';

        return $this->response->setJSON(['reply' => $reply]);
    }


    /**
     * Basic rule-based local assistant responder
     * Keeps everything in PHP so no external API or extra installs are required.
     */
    private function local_assistant_respond($message)
    {
        $m = mb_strtolower($message, 'UTF-8');

        // Greeting
        if (preg_match('/\b(bonjour|salut|hello|hi)\b/u', $m)) {
            return "Bonjour ! Je suis l'assistant local. Que puis-je faire pour vous ?";
        }

        // Help keywords
        if (preg_match('/\b(aide|help|support|comment|quoi)\b/u', $m)) {
            return "Je peux :\n- Donner des indications sur l'application (ex : 'où est la gestion des comptes')\n- Renvoyer vers la documentation locale (README.md, QRCODE_ATTENDANCE_GUIDE.md)\n- Fournir des réponses simples basées sur mots-clés. Posez une question claire.";
        }

        // Modules pointer
        if (strpos($m, 'compte') !== false || strpos($m, 'comptes') !== false) {
            return "La gestion des comptes se trouve dans le contrôleur 'Comptes' (application/controllers/admin/Comptes.php).";
        }

        if (strpos($m, 'qr') !== false || strpos($m, 'présence') !== false || strpos($m, 'pointage') !== false) {
            return "Le module de pointage QR est dans Qrattendance. Voir le fichier QRCODE_ATTENDANCE_GUIDE.md à la racine pour la configuration.";
        }

        if (preg_match('/\b(upload|télécharg|fichier)\b/u', $m)) {
            return "Les uploads sont stockés dans le dossier 'uploads/'. Vérifiez les permissions et les champs 'file' dans les formulaires.";
        }

        // Default fallback
        return "Désolé, je n'ai pas de réponse précise. Essayez de reformuler ou demandez 'aide' pour les options.";
    }

    function index() {

        if (!$this->rbac->hasPrivilege('visiteurs', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'receptioniste');
        $this->session->set_userdata('sub_menu', 'admin/chatbot');
        $this->form_validation->set_rules('purpose', $this->lang->line('purpose'), 'required');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_handle_upload[file]');

        if ($this->form_validation->run() == FALSE) {
            $data['visitor_list'] = $this->Visitors_model->visitors_list();
            $data['Purpose'] = $this->Visitors_model->getPurpose();
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/chatbot', $data);
            $this->load->view('layout/footer');
        } else {
            $visitors = array(
                'purpose' => $this->input->post('purpose'),
                'name' => $this->input->post('name'),
                'contact' => $this->input->post('contact'),
                'id_proof' => $this->input->post('id_proof'),
                'no_of_pepple' => $this->input->post('pepples'),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'in_time' => $this->input->post('time'),
                'out_time' => $this->input->post('out_time'),
                'note' => $this->input->post('note')
            );

            $visitor_id = $this->Visitors_model->add($visitors);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $visitor_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/visitors/" . $img_name);
                $this->Visitors_model->image_add($visitor_id, $img_name);
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/visitors');
        }
    }

    public function delete($id) {
        if (!$this->rbac->hasPrivilege('visiteurs', 'can_delete')) {
            access_denied();
        }

        $this->Visitors_model->delete($id);
    }

    public function edit($id) {
        if (!$this->rbac->hasPrivilege('visiteurs', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('purpose', $this->lang->line('purpose'), 'required');

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required');

        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_handle_upload[file]');
        if ($this->form_validation->run() == FALSE) {

            $data['Purpose'] = $this->Visitors_model->getPurpose();
            $data['visitor_list'] = $this->Visitors_model->visitors_list();
            $data['visitor_data'] = $this->Visitors_model->visitors_list($id);
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/visitoreditview', $data);
            $this->load->view('layout/footer');
        } else {

            $visitors = array(
                'purpose' => $this->input->post('purpose'),
                'name' => $this->input->post('name'),
                'contact' => $this->input->post('contact'),
                'id_proof' => $this->input->post('id_proof'),
                'no_of_pepple' => $this->input->post('pepples'),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'in_time' => $this->input->post('time'),
                'out_time' => $this->input->post('out_time'),
                'note' => $this->input->post('note')
            );
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);

                $img_name = 'id' . $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/visitors/" . $img_name);
                $this->Visitors_model->image_update($id, $img_name);
            }
            $this->Visitors_model->update($id, $visitors);
            redirect('admin/visitors');
        }
    }

    public function details($id) {
        if (!$this->rbac->hasPrivilege('visiteurs', 'can_view')) {
            access_denied();
        }

        $data['data'] = $this->Visitors_model->visitors_list($id);
        $this->load->view('admin/frontoffice/Visitormodelview', $data);
    }

    public function download($documents) {
        $this->load->helper('download');
        $filepath = "./uploads/front_office/visitors/" . $documents;
        $data = file_get_contents($filepath);
        $name = $documents;
        force_download($name, $data);
    }

    public function imagedelete($id, $image) {
        if (!$this->rbac->hasPrivilege('visiteurs', 'can_delete')) {
            access_denied();
        }
        $this->Visitors_model->image_delete($id, $image);
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
