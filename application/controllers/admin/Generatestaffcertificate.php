<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Endroid\QrCode\QrCode;

class Generatestaffcertificate extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->sch_setting_detail = $this->setting_model->getSetting();

    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('generate_staff_id_card', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generatestaffcertificate');
        $idcardlist            = $this->Generatestaffidcard_model->getstaffidcard();
        $data['idcardlist']    = $idcardlist;
        $staffRole             = $this->staff_model->getStaffRole();
        $data['staffRolelist'] = $staffRole;
        $this->load->view('layout/header');
        $this->load->view('admin/generatestaffcertificate/generatestaffcertificateview', $data);
        $this->load->view('layout/footer');
    }

    public function search()
    {
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generatestaffcertificate');
        $staffRole               = $this->staff_model->getStaffRole();
        $data['staffRolelist']   = $staffRole;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $idcardlist              = $this->Generatestaffidcard_model->getstaffidcard();
        $data['idcardlist']      = $idcardlist;
        $this->form_validation->set_rules('id_card', $this->lang->line('id_card_template'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == true) {
            $role                 = $this->input->post('role_id');
            $data['role_id']      = $this->input->post('role_id');
            $id_card              = $this->input->post('id_card');
            $idcardResult         = $this->Generatestaffidcard_model->getidcardbyid($id_card);
            $data['idcardResult'] = $idcardResult;
            $resultlist           = $this->staff_model->getEmployee($role, 1);
            $data['resultlist']   = $resultlist;
        }

        $this->load->view('layout/header');
        $this->load->view('admin/generatestaffcertificate/generatestaffcertificateview', $data);
        $this->load->view('layout/footer');
    }

    public function badgeqr()
    {
        if (!$this->rbac->hasPrivilege('generate_staff_id_card', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generatestaffcertificate/badgeqr');

        $data['staffRolelist'] = $this->staff_model->getStaffRole();
        $this->load->view('layout/header');
        $this->load->view('admin/generatestaffcertificate/generatebadgeqrview', $data);
        $this->load->view('layout/footer');
    }

    public function searchbadgeqr()
    {
        if (!$this->rbac->hasPrivilege('generate_staff_id_card', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generatestaffcertificate/badgeqr');

        $data['staffRolelist'] = $this->staff_model->getStaffRole();
        $this->form_validation->set_rules('role_id', $this->lang->line('role'), 'trim|required|xss_clean');

        if ($this->form_validation->run() === true) {
            $role = $this->input->post('role_id');
            $data['role_id'] = $role;
            $data['resultlist'] = $this->staff_model->getEmployee($role, 1);
        }

        $this->load->view('layout/header');
        $this->load->view('admin/generatestaffcertificate/generatebadgeqrview', $data);
        $this->load->view('layout/footer');
    }

    public function generatebadgeqr()
    {
        if (!$this->rbac->hasPrivilege('generate_staff_id_card', 'can_view')) {
            access_denied();
        }

        $staffid = $this->input->post('data');
        $staff_array = json_decode($staffid);

        if (empty($staff_array) || !is_array($staff_array)) {
            echo '<div style="padding:20px; font-family:Arial;">Aucun employe selectionne.</div>';
            return;
        }

        $staffid_arr = array();
        foreach ($staff_array as $value) {
            if (isset($value->staff_id)) {
                $staffid_arr[] = (int)$value->staff_id;
            }
        }

        if (empty($staffid_arr)) {
            echo '<div style="padding:20px; font-family:Arial;">Aucun employe selectionne.</div>';
            return;
        }

        $data['sch_setting'] = $this->sch_setting_detail;
        $data['staffs'] = $this->Generatestaffidcard_model->getEmployee($staffid_arr, 1);
        $data['qr_codes'] = array();

        if (!empty($data['staffs'])) {
            foreach ($data['staffs'] as $staff_value) {
                $payload = $this->buildStaffQrPayload($staff_value);
                $data['qr_codes'][$staff_value->id] = $this->generateQrDataUri($payload);
            }
        }

        $badge_html = $this->load->view('admin/generatestaffcertificate/generatebadgeqrprintview', $data, true);
        echo $badge_html;
    }

    public function generatemultiple()
    {
        $staffid             = $this->input->post('data');
        $staff_array         = json_decode($staffid);
        $idcard              = $this->input->post('id_card');
        $include_qr          = (int)$this->input->post('include_qr') === 1;
        $staffid_arr         = array();
        $qr_codes            = array();
        $data['sch_setting'] = $this->setting_model->get();
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['id_card']     = $this->Generatestaffidcard_model->getidcardbyid($idcard);
        foreach ($staff_array as $key => $value) {
            $staffid_arr[] = $value->staff_id;
        }
        $data['staffs'] = $this->Generatestaffidcard_model->getEmployee($staffid_arr, 1);

        if ($include_qr && !empty($data['staffs'])) {
            foreach ($data['staffs'] as $staff_value) {
                $payload = $this->buildStaffQrPayload($staff_value);
                $qr_codes[$staff_value->id] = $this->generateQrDataUri($payload);
            }
        }

        $data['include_qr'] = $include_qr;
        $data['qr_codes'] = $qr_codes;

        $id_cards       = $this->load->view('admin/generatestaffcertificate/generatemultiplestaffcertificate', $data, true);
        echo $id_cards;
    }

    private function buildStaffQrPayload($staff_value)
    {
        $payload = array(
            'type' => 'staff_attendance',
            'staff_id' => (int)$staff_value->id,
            'employee_id' => (string)$staff_value->employee_id,
            'generated_at' => date('c')
        );

        return json_encode($payload);
    }

    private function generateQrDataUri($payload)
    {
        try {
            $qrCode = new QrCode($payload);
            $qrCode->setSize(180);
            $qrCode->setMargin(8);

            return $qrCode->writeDataUri();
        } catch (\Exception $e) {
            return '';
        }
    }
}
