<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Certificate_generator extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('certificate_model');
        $this->load->library('certificate_generator');
        $this->load->model('staff_model'); // Pour récupérer les données du personnel
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('certificate_generate', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/certificate_generator');

        $this->data['certificates'] = $this->certificate_model->get_all();
        $this->data['staff_list'] = $this->staff_model->getStaff(); // Récupérer la liste du personnel

        $this->load->view('layout/header');
        $this->load->view('admin/certificates/generate', $this->data);
        $this->load->view('layout/footer');
    }

    public function generate()
    {
        if (!$this->rbac->hasPrivilege('certificate_generate', 'can_view')) {
            access_denied();
        }

        $certificate_id = $this->input->post('certificate_id');
        $staff_id = $this->input->post('staff_id');

        $certificate = $this->certificate_model->get($certificate_id);
        $staff = $this->staff_model->get($staff_id);

        // Préparer les données selon le type de certificat
        $data = $this->prepare_certificate_data($certificate->template_type, $staff);

        // Générer le PDF
        $pdf = $this->certificate_generator->generate($certificate, $data);

        // Sauvegarder l'historique
        $this->save_certificate_history($certificate_id, $staff_id, $data);

        // Télécharger ou afficher le PDF
        $pdf->download('certificat_' . $certificate->generated_code . '_' . $staff->id . '.pdf');
    }

    private function prepare_certificate_data($type, $staff)
    {
        $data = array();

        switch($type) {
            case 'work':
                $data = array(
                    'employee_name' => $staff->name,
                    'position' => $staff->designation_name,
                    'start_date' => date('d/m/Y', strtotime($staff->date_of_joining)),
                    'end_date' => 'aujourd\'hui'
                );
                break;
            case 'training':
                $data = array(
                    'participant_name' => $staff->name,
                    'training_name' => $this->input->post('training_name'),
                    'duration' => $this->input->post('duration'),
                    'completion_date' => date('d/m/Y')
                );
                break;
            case 'internship':
                $data = array(
                    'intern_name' => $staff->name,
                    'department' => $staff->department_name,
                    'start_date' => date('d/m/Y', strtotime($this->input->post('start_date'))),
                    'end_date' => date('d/m/Y', strtotime($this->input->post('end_date')))
                );
                break;
        }

        return $data;
    }

    private function save_certificate_history($certificate_id, $staff_id, $data)
    {
        $history = array(
            'certificate_id' => $certificate_id,
            'staff_id' => $staff_id,
            'generated_data' => json_encode($data),
            'generated_by' => $this->session->userdata('admin_id'),
            'generated_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert('certificate_history', $history);
    }
}