<?php
/**
 * Contrôleur Sanction
 */
class Sanction extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->config->load("payroll");
        $this->load->model('designation_model');
        $this->load->model('staff_model');
    }

    // Liste et ajout / modification
    function sanction() {
        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/sanction/sanction');

        $designation = $this->designation_model->gets();
        $staffRole   = $this->staff_model->getStaffRole();

        $data = array(
            'title'       => $this->lang->line('add') . " " . $this->lang->line('designation'),
            'designation' => $designation,
            'staffrole'   => $staffRole,
        );

        $this->form_validation->set_rules('type',   'Titre', 'required');
        $this->form_validation->set_rules('role',   'Rôle', 'required');
        $this->form_validation->set_rules('empname','Nom employé', 'required');
        $this->form_validation->set_rules('action', 'Action', 'required');
        $this->form_validation->set_rules('date',   'Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view("layout/header");
            $this->load->view("admin/staff/sanction", $data);
            $this->load->view("layout/footer");
        } else {
            $designationid = $this->input->post('designationid');
            $insert_data = array(
                'designation' => $this->input->post('type'),
                'role'        => $this->input->post('role'),
                'empname'     => $this->input->post('empname'),
                'action'      => $this->input->post('action'),
                'reason'      => $this->input->post('reason'),
                'date'        => $this->input->post('date'),
                'is_active'   => 'yes'
            );

            if (!empty($designationid)) {
                $insert_data['id'] = $designationid;
                if (!$this->rbac->hasPrivilege('sanction', 'can_edit')) {
                    access_denied();
                }
            } else {
                if (!$this->rbac->hasPrivilege('sanction', 'can_add')) {
                    access_denied();
                }
            }

            $this->designation_model->addSanction($insert_data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
            redirect("admin/sanction/sanction");
        }
    }

    // Édition
    function sanctionedit($id) {
        $result = $this->designation_model->gets($id);
        $data["title"] = $this->lang->line('edit') . " " . $this->lang->line('designation');
        $data["result"] = $result;
        $data["designation"] = $this->designation_model->gets();
        $data["staffrole"] = $this->staff_model->getStaffRole();
        $this->load->view("layout/header");
        $this->load->view("admin/staff/sanction", $data);
        $this->load->view("layout/footer");
    }

    // Suppression
    function sanctiondelete($id) {
        $this->designation_model->deleteSanction($id);
        redirect('admin/sanction/sanction');
    }

    // Impression individuelle par employé
    /**
     * Affiche les sanctions d'un employé dans une vue imprimable
     * @param string $empname_encoded (nom encodé en base64)
     */
    function imprimer_employe($empname_encoded) {
        // Décodage du nom
        $empname = base64_decode($empname_encoded);

        // Récupération des sanctions
        $sanctions = $this->designation_model->getSanctionsByEmploye($empname);

        $data = array(
            'empname'   => $empname,
            'sanctions' => $sanctions,
            'title'     => 'Sanctions de ' . $empname
        );

        $this->load->view('admin/staff/imprimer_sanction_employe', $data);
    }
}