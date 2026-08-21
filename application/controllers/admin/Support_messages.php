<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Support_messages extends Admin_Controller
{
    protected $support_messages_model;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('support_messages_model');
    }

    private function can_manage()
    {
        return $this->rbac->hasPrivilege('support_messages', 'can_view');
    }

    private function entreprise_id()
    {
        $admin = $this->session->userdata('admin');
        return (int) ($admin['entreprise_id'] ?? $this->session->userdata('entreprise_id') ?? 0);
    }

    public function index()
    {
        if (!$this->can_manage()) {
            access_denied();
            return;
        }

        $data['messages'] = $this->support_messages_model->get_all($this->entreprise_id());
        $data['editing'] = null;
        $data['title'] = 'Messages de support';
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'admin/support_messages');
        $this->load->view('layout/header', $data);
        $this->load->view('admin/support_messages/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function edit($id)
    {
        if (!$this->can_manage()) {
            access_denied();
            return;
        }

        $messages = $this->support_messages_model->get_all($this->entreprise_id());
        $editing = null;
        foreach ($messages as $message) {
            if ((int) $message['id'] === (int) $id) {
                $editing = $message;
                break;
            }
        }

        $data['messages'] = $messages;
        $data['editing'] = $editing;
        $data['title'] = 'Messages de support';
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'admin/support_messages');
        $this->load->view('layout/header', $data);
        $this->load->view('admin/support_messages/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function save()
    {
        if (!$this->can_manage()) {
            access_denied();
            return;
        }

        $title = trim((string) $this->input->post('title', true));
        $message = trim((string) $this->input->post('message'));
        if ($title === '' || $message === '') {
            $this->session->set_flashdata('error_msg', 'Le titre et le message sont obligatoires.');
            redirect('admin/support_messages');
            return;
        }

        $data = array(
            'entreprise_id' => $this->entreprise_id(),
            'title' => $title,
            'message' => $message,
            'active' => $this->input->post('active') ? 1 : 0,
            'start_at' => $this->input->post('start_at') ?: null,
            'end_at' => $this->input->post('end_at') ?: null,
            'updated_at' => date('Y-m-d H:i:s')
        );
        $id = (int) $this->input->post('id');
        if ($id > 0) {
            $this->support_messages_model->update($id, $this->entreprise_id(), $data);
            $notice = 'Message mis à jour.';
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->support_messages_model->create($data);
            $notice = 'Message créé.';
        }

        $this->session->set_flashdata('success_msg', $notice);
        redirect('admin/support_messages');
    }

    public function delete($id)
    {
        if (!$this->can_manage()) {
            access_denied();
            return;
        }

        $this->support_messages_model->delete($id, $this->entreprise_id());
        $this->session->set_flashdata('success_msg', 'Message supprimé.');
        redirect('admin/support_messages');
    }
}