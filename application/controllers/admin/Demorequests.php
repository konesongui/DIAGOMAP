<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Demorequests extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('demorequest_model');
        $this->load->library('mailer');
        $this->ensure_demo_reply_columns();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('superadmin', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'front_cms');
        $this->session->set_userdata('sub_menu', 'admin/demorequests');

        if (!$this->has_table('demo_requests')) {
            $db_name = isset($this->db->database) ? $this->db->database : 'inconnue';
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">La table demo_requests est absente dans la base active (' . htmlspecialchars($db_name) . '). Executez le script SQL dans cette base.</div>');
            $data['requests'] = array();
            $data['stats'] = array(
                'total' => 0,
                'acceptée' => 0,
                'nouvelle' => 0,
                'refusée' => 0
            );
        } else {
            $data['requests'] = $this->demorequest_model->getAllDemoRequests();
            $data['stats'] = $this->demorequest_model->getDashboardStats();
        }

        $this->load->view('layout/header');
        $this->load->view('admin/demorequests/index', $data);
        $this->load->view('layout/footer');
    }

    public function reply($id = 0)
    {
        if (!$this->rbac->hasPrivilege('superadmin', 'can_view')) {
            access_denied();
        }

        $request_id = (int)$id;
        $request = $this->demorequest_model->getDemoRequestById($request_id);
        if (empty($request)) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Demande introuvable.</div>');
            redirect('admin/demorequests');
            return;
        }

        $email = trim((string)($request['email'] ?? ''));
        if ($email === '') {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Cette demande ne contient pas d’email valide.</div>');
            redirect('admin/demorequests');
            return;
        }

        $this->form_validation->set_rules('reply_subject', 'Objet', 'trim|required|max_length[150]');
        $this->form_validation->set_rules('reply_message', 'Message', 'trim|required');
        $this->form_validation->set_rules('status', 'Statut', 'trim|required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">' . strip_tags(validation_errors()) . '</div>');
            redirect('admin/demorequests');
            return;
        }

        $status = trim((string)$this->input->post('status', true));
        $status = in_array($status, array('nouvelle', 'acceptée', 'refusée'), true) ? $status : 'acceptée';
        $subject = trim((string)$this->input->post('reply_subject', true));
        $message = trim((string)$this->input->post('reply_message', true));

        $attachment = array();
        if (!empty($_FILES['attachment']['name'])) {
            $upload_dir = FCPATH . 'uploads/demo_replies/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($_FILES['attachment']['name']));
            $target = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
                $attachment = array(
                    'files' => array(
                        'name' => array($file_name),
                        'tmp_name' => array($target),
                        'type' => array($_FILES['attachment']['type'] ?? 'application/octet-stream')
                    )
                );
            }
        }

        $body = '<html><body style="font-family:Arial,sans-serif;line-height:1.6;">
            <p>Bonjour <strong>' . htmlspecialchars($request['full_name'], ENT_QUOTES, 'UTF-8') . '</strong>,</p>
            <div>' . nl2br(htmlspecialchars($message)) . '</div>
            <p>Merci,<br>Equipe DIAGO</p>
        </body></html>';

        $sent = $this->mailer->send_mail($email, $subject, $body, $attachment);

        if ($sent) {
            $this->demorequest_model->updateDemoRequest($request_id, array(
                'status' => $status,
                'reply_subject' => $subject,
                'reply_message' => $message,
                'reply_attachment' => !empty($attachment) ? basename($_FILES['attachment']['name']) : null,
                'reply_sent_at' => date('Y-m-d H:i:s'),
                'last_reply_status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ));

            $status_label = $status === 'acceptée' ? 'acceptée' : ($status === 'refusée' ? 'refusée' : 'en attente');
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Réponse ' . $status_label . ' envoyée par e-mail à <strong>' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</strong> avec succès.</div>');
        } else {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">L’envoi par e-mail à <strong>' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</strong> a échoué. Vérifiez la configuration SMTP.</div>');
        }

        redirect('admin/demorequests');
    }

    public function delete($id = 0)
    {
        if (!$this->rbac->hasPrivilege('superadmin', 'can_view')) {
            access_denied();
        }

        if ($id > 0 && $this->has_table('demo_requests')) {
            $this->demorequest_model->deleteDemoRequest((int)$id);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Demande supprimee avec succes.</div>');
        }

        redirect('admin/demorequests');
    }

    private function ensure_demo_reply_columns()
    {
        if (!$this->has_table('demo_requests')) {
            return;
        }

        $columns = array(
            'reply_subject' => 'VARCHAR(150) NULL DEFAULT NULL',
            'reply_message' => 'TEXT NULL DEFAULT NULL',
            'reply_attachment' => 'VARCHAR(255) NULL DEFAULT NULL',
            'reply_sent_at' => 'DATETIME NULL DEFAULT NULL',
            'last_reply_status' => 'VARCHAR(30) NULL DEFAULT NULL'
        );

        foreach ($columns as $column => $definition) {
            if (!$this->db->field_exists($column, 'demo_requests')) {
                $this->db->query('ALTER TABLE demo_requests ADD COLUMN `' . $column . '` ' . $definition);
            }
        }
    }

    private function has_table($table)
    {
        $table = trim((string)$table);
        if ($table === '') {
            return false;
        }

        if ($this->db->table_exists($table)) {
            return true;
        }

        $prefix = (string)$this->db->dbprefix;
        if ($prefix !== '' && $this->db->table_exists($prefix . $table)) {
            return true;
        }

        $escaped = $this->db->escape_like_str($table);
        $query   = $this->db->query("SHOW TABLES LIKE '" . $escaped . "'");
        if ($query && $query->num_rows() > 0) {
            return true;
        }

        if ($prefix !== '') {
            $escaped_prefixed = $this->db->escape_like_str($prefix . $table);
            $query_prefixed   = $this->db->query("SHOW TABLES LIKE '" . $escaped_prefixed . "'");
            if ($query_prefixed && $query_prefixed->num_rows() > 0) {
                return true;
            }
        }

        return false;
    }
}
