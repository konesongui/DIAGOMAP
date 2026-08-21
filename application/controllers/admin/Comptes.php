<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Comptes extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('mailsmsconf');
        $this->load->library('enc_lib');
        $this->load->model('comptes_model');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('comptes', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Comptes');
        $this->session->set_userdata('sub_menu', 'comptes/index');
        $data['title'] = 'Expense Head List';

        $this->load->view('layout/header', $data);
        $this->load->view('admin/comptes/comptesList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function dashboard()
    {
        if (!$this->rbac->hasPrivilege('comptes', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Comptes');
        $this->session->set_userdata('sub_menu', 'comptes/dashboad');
        $data['title'] = 'Expense Head List';

        $this->load->view('layout/header', $data);
        $this->load->view('admin/comptes/dashboard', $data);
        $this->load->view('layout/footer', $data);
    }

    public function succursales()
    {
        if (!$this->canCurrentEntrepriseManageBranches()) {
            access_denied();
        }

        $headOffice = $this->getCurrentEntrepriseAccount();
        $this->session->set_userdata('top_menu', 'Succursales');
        $this->session->set_userdata('sub_menu', 'comptes/succursales');

        $data['title'] = 'Gestion des succursales';
        $data['head_office'] = $headOffice;
        $data['branches'] = $this->comptes_model->getBranchesByHeadOffice((int) $headOffice->id);

        $this->load->view('layout/header', $data);
        $this->load->view('admin/comptes/succursales_list', $data);
        $this->load->view('layout/footer', $data);
    }

    public function create_succursale()
    {
        if (!$this->rbac->hasPrivilege('comptes', 'can_add')) {
            access_denied();
        }

        if (!$this->canCurrentEntrepriseManageBranches()) {
            access_denied();
        }

        $currentOffice = $this->getCurrentEntrepriseAccount();
        
        $data['title'] = 'Ajouter une succursale';
        $data['branch_mode'] = true;
        $data['can_company_create'] = true;
        $data['form_action'] = site_url('admin/comptes/create_succursale');
        $data['current_head_office'] = $currentOffice;
        $data['head_offices'] = [
            ['id' => $currentOffice->id, 'nom' => $currentOffice->nom]
        ];
        $data['prefill_type'] = 'succursale';
        $data['prefill_parent'] = $currentOffice->id;

        // Validation
        $this->form_validation->set_rules('nom', 'Nom succursale', 'trim|required|xss_clean');
        $this->form_validation->set_rules('admin_email', 'Email admin', 'trim|required|valid_email|xss_clean');
        $this->form_validation->set_rules('admin_username', 'Nom admin', 'trim|required|xss_clean');
        $this->form_validation->set_rules('code_succursale', 'Code succursale', 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/comptes/comptes_create', $data);
            $this->load->view('layout/footer', $data);
            return;
        }

        // Génération du mot de passe
        $plainPassword = $this->generateRandomPassword(12);
        $hashedPassword = $this->enc_lib->passHashEnc($plainPassword);

        // Données de la succursale
        $companyData = [
            'nom'                     => $this->input->post('nom'),
            'email'                   => $this->input->post('email'),
            'telephone'               => $this->input->post('telephone'),
            'adresse'                 => $this->input->post('adresse'),
            'forfait'                 => $this->input->post('forfait'),
            'slug'                    => $this->input->post('slug'),
            'date_debut'              => $this->input->post('date_debut'),
            'date_expiration'         => $this->input->post('date_expiration'),
            'statut'                  => $this->input->post('statut'),
            'ncc'                     => $this->input->post('ncc'),
            'rccm'                    => $this->input->post('rccm'),
            'contact_nom'             => $this->input->post('contact_nom'),
            'limite_utilisateurs'     => $this->input->post('limite_utilisateurs'),
            'fne_api_key'             => $this->input->post('fne_api_key'),
            'fne_point_vente'         => $this->input->post('fne_point_vente'),
            'fne_establishment'       => $this->input->post('fne_establishment'),
            'admin_username'          => $this->input->post('admin_username'),
            'admin_email'             => $this->input->post('admin_email'),
            'admin_password'          => $hashedPassword,
            'type_structure'          => 'succursale',
            'parent_entreprise_id'    => $currentOffice->id,
            'code_succursale'         => $this->input->post('code_succursale'),
            'can_manage_succursales'  => 0,
            'created_at'              => date('Y-m-d H:i:s'),
            'updated_at'              => date('Y-m-d H:i:s'),
        ];

        // Gestion du logo
        if (!empty($_FILES['logo']['name'])) {
            $config['upload_path']   = './uploads/front_office/logo_entreprises/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size']      = 2048;
            $config['file_name']     = time() . '_' . $_FILES['logo']['name'];
            
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }
            
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('logo')) {
                $uploadData = $this->upload->data();
                $companyData['logo'] = $uploadData['file_name'];
            } else {
                $companyData['logo'] = "";
            }
        }

        // Transaction
        $this->db->trans_start();

        $entrepriseId = $this->comptes_model->add($companyData);

        if (!$entrepriseId) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la création de la succursale.</div>');
            redirect('admin/comptes/succursales');
            return;
        }

        // Insertion admin
        $staffData = $this->prepareStaffData($this->input->post(), $hashedPassword, $entrepriseId);
        $this->db->insert('staff', $staffData);
        $staffId = $this->db->insert_id();

        if (!$staffId) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la création de l\'administrateur.</div>');
            redirect('admin/comptes/succursales');
            return;
        }

        // Attribution du rôle
        $this->db->insert('staff_roles', ['staff_id' => $staffId, 'role_id' => 1]);

        // Création des settings par défaut pour l'entreprise
        $settingsInserted = $this->createDefaultEntrepriseSettings(
            $entrepriseId,
            $this->input->post('nom'),
            $this->input->post('email') ?: $this->input->post('admin_email'),
            $this->input->post('telephone') ?: '',
            $this->input->post('adresse') ?: ''
        );

        if (!$settingsInserted) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la création des réglages de l\'entreprise.</div>');
            redirect('admin/comptes/succursales');
            return;
        }

        // Duplication des permissions du siège
        $this->clonePermissions($currentOffice->id, $entrepriseId);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la création.</div>');
            redirect('admin/comptes/succursales');
            return;
        }

        $this->db->trans_commit();

        // Envoi de l'email via la configuration mail/sms de l'application
        $this->mailsmsconf->mailsms('login_credential', [
            'id' => $staffId,
            'credential_for' => 'staff',
            'username' => $this->input->post('admin_username'),
            'password' => $plainPassword,
            'contact_no' => $this->input->post('telephone') ?: '',
            'email' => $this->input->post('admin_email')
        ]);

        $this->session->set_flashdata('msg', '<div class="alert alert-success">Succursale créée avec succès ! Les identifiants ont été envoyés par email.</div>');
        redirect('admin/comptes/succursales');
    }

    public function create()
    {
        if (!$this->rbac->hasPrivilege('comptes', 'can_add')) {
            access_denied();
        }

        $data['title'] = 'Ajouter une entreprise';
        $data['branch_mode'] = false;
        $data['can_company_create'] = true;
        $data['form_action'] = site_url('admin/comptes/create');
        $data['head_offices'] = $this->comptes_model->getHeadOfficeOptions();
        $data['prefill_type'] = '';
        $data['prefill_parent'] = '';

        // Validation
        $this->form_validation->set_rules('nom', 'Nom entreprise', 'trim|required|xss_clean');
        $this->form_validation->set_rules('admin_email', 'Email admin', 'trim|required|valid_email|xss_clean');
        $this->form_validation->set_rules('admin_username', 'Nom admin', 'trim|required|xss_clean');
        $this->form_validation->set_rules('type_structure', 'Type de structure', 'trim|required|in_list[siege,succursale]');
        
        if ($this->input->post('type_structure') === 'succursale') {
            $this->form_validation->set_rules('parent_entreprise_id', 'Siège de rattachement', 'trim|required|numeric');
            $this->form_validation->set_rules('code_succursale', 'Code succursale', 'trim|required|xss_clean');
        }

        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/comptes/comptes_create', $data);
            $this->load->view('layout/footer', $data);
            return;
        }

        // Génération du mot de passe
        $plainPassword = $this->generateRandomPassword(12);
        $hashedPassword = $this->enc_lib->passHashEnc($plainPassword);

        // Données de l'entreprise
        $companyData = [
            'nom'                     => $this->input->post('nom'),
            'email'                   => $this->input->post('email'),
            'telephone'               => $this->input->post('telephone'),
            'adresse'                 => $this->input->post('adresse'),
            'forfait'                 => $this->input->post('forfait'),
            'slug'                    => $this->input->post('slug'),
            'date_debut'              => $this->input->post('date_debut'),
            'date_expiration'         => $this->input->post('date_expiration'),
            'statut'                  => $this->input->post('statut'),
            'ncc'                     => $this->input->post('ncc'),
            'rccm'                    => $this->input->post('rccm'),
            'contact_nom'             => $this->input->post('contact_nom'),
            'limite_utilisateurs'     => $this->input->post('limite_utilisateurs'),
            'fne_api_key'             => $this->input->post('fne_api_key'),
            'fne_point_vente'         => $this->input->post('fne_point_vente'),
            'fne_establishment'       => $this->input->post('fne_establishment'),
            'admin_username'          => $this->input->post('admin_username'),
            'admin_email'             => $this->input->post('admin_email'),
            'admin_password'          => $hashedPassword,
            'type_structure'          => $this->input->post('type_structure'),
            'created_at'              => date('Y-m-d H:i:s'),
            'updated_at'              => date('Y-m-d H:i:s'),
        ];

        // Si c'est une succursale
        if ($this->input->post('type_structure') === 'succursale') {
            $companyData['parent_entreprise_id'] = $this->input->post('parent_entreprise_id');
            $companyData['code_succursale'] = $this->input->post('code_succursale');
            $companyData['can_manage_succursales'] = 0;
        } else {
            $companyData['parent_entreprise_id'] = null;
            $companyData['code_succursale'] = '';
            $companyData['can_manage_succursales'] = $this->input->post('can_manage_succursales') ? 1 : 0;
        }

        // Gestion du logo
        if (!empty($_FILES['logo']['name'])) {
            $config['upload_path']   = './uploads/front_office/logo_entreprises/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size']      = 2048;
            $config['file_name']     = time() . '_' . $_FILES['logo']['name'];
            
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }
            
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('logo')) {
                $uploadData = $this->upload->data();
                $companyData['logo'] = $uploadData['file_name'];
            } else {
                $companyData['logo'] = "";
            }
        }

        // Transaction
        $this->db->trans_start();

        $entrepriseId = $this->comptes_model->add($companyData);

        if (!$entrepriseId) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la création de l\'entreprise.</div>');
            redirect('admin/comptes/index');
            return;
        }

        // Insertion admin
        $staffData = $this->prepareStaffData($this->input->post(), $hashedPassword, $entrepriseId);
        $this->db->insert('staff', $staffData);
        $staffId = $this->db->insert_id();

        if (!$staffId) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la création de l\'administrateur.</div>');
            redirect('admin/comptes/index');
            return;
        }

        // Attribution du rôle
        $this->db->insert('staff_roles', ['staff_id' => $staffId, 'role_id' => 1]);

        // Création des settings par défaut pour l'entreprise
        $settingsInserted = $this->createDefaultEntrepriseSettings(
            $entrepriseId,
            $this->input->post('nom'),
            $this->input->post('email') ?: $this->input->post('admin_email'),
            $this->input->post('telephone') ?: '',
            $this->input->post('adresse') ?: ''
        );

        if (!$settingsInserted) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la création des réglages de l\'entreprise.</div>');
            redirect('admin/comptes/index');
            return;
        }

        // Duplication des permissions
        $sourceId = ($this->input->post('type_structure') === 'succursale') 
            ? $this->input->post('parent_entreprise_id') 
            : 1;
        $this->clonePermissions($sourceId, $entrepriseId);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la création.</div>');
            redirect('admin/comptes/index');
            return;
        }

        $this->db->trans_commit();

        // Envoi de l'email via la configuration mail/sms de l'application
        $this->mailsmsconf->mailsms('login_credential', [
            'id' => $staffId,
            'credential_for' => 'staff',
            'username' => $this->input->post('admin_username'),
            'password' => $plainPassword,
            'contact_no' => $this->input->post('telephone') ?: '',
            'email' => $this->input->post('admin_email')
        ]);

        $this->session->set_flashdata('msg', '<div class="alert alert-success">Compte créé avec succès ! Les identifiants ont été envoyés par email.</div>');
        redirect('admin/comptes/index');
    }

    private function prepareStaffData($postData, $hashedPassword, $entrepriseId)
    {
        return [
            'employee_id'          => $this->generateUniqueEmployeeId(),
            'lang_id'              => 1,
            'department'           => 0,
            'designation'          => 0,
            'qualification'        => '',
            'work_exp'             => '',
            'name'                 => $postData['admin_username'],
            'surname'              => '',
            'father_name'          => '',
            'mother_name'          => '',
            'contact_no'           => $postData['telephone'] ?: '',
            'emergency_contact_no' => '',
            'email'                => $postData['admin_email'],
            'dob'                  => '2000-01-01',
            'marital_status'       => '',
            'date_of_joining'      => date('Y-m-d'),
            'date_of_leaving'      => '0000-00-00',
            'local_address'        => $postData['adresse'] ?: '',
            'permanent_address'    => '',
            'note'                 => '',
            'image'                => '',
            'password'             => $hashedPassword,
            'gender'               => 'male',
            'account_title'        => '',
            'bank_account_no'      => '',
            'bank_name'            => '',
            'ifsc_code'            => '',
            'bank_branch'          => '',
            'payscale'             => '',
            'basic_salary'         => '0',
            'sursalaire'           => '0',
            'conge'                => '0',
            'categorie_salaire'    => '',
            'categorie_lettre'     => '',
            'prime_anc'            => '0',
            'prime_trans'          => '0',
            'forfait_hs'           => '0',
            'prime_resp'           => '0',
            'prime_rend'           => '0',
            'prime_risque'         => '0',
            'prime_assi'           => '0',
            'prime_grati'          => '0',
            'imp_sal'              => '0',
            'contra_nat'           => '0',
            'imp_revenu'           => '0',
            'crns'                 => '0',
            'cnps_no'              => '',
            'cmu'                  => '0',
            'cnps_regim'           => '0',
            'cnps_tra'             => '0',
            'cnps_pres'            => '0',
            'fdfp_taxe'            => '0',
            'fdfp_form'            => '0',
            'avan_acom'            => '0',
            'autre_reve'           => '',
            'tax'                  => '0',
            'bonus'                => '0',
            'epf_no'               => '',
            'contract_type'        => '',
            'shift'                => '',
            'location'             => '',
            'facebook'             => '',
            'twitter'              => '',
            'linkedin'             => '',
            'instagram'            => '',
            'resume'               => '',
            'joining_letter'       => '',
            'resignation_letter'   => '',
            'other_document_name'  => '',
            'other_document_file'  => '',
            'user_id'              => 0,
            'is_active'            => 1,
            'verification_code'    => '',
            'disable_at'           => null,
            'reason'               => null,
            'deleted'              => 0,
            'part_igr'             => '0',
            'responsable'          => '',
            'salaire_base'         => '0',
            'file_name'            => '',
            'file_size'            => 0,
            'upload_date'          => date('Y-m-d H:i:s'),
            'cmu_enfant'           => '0',
            'taxes'                => '0',
            'leaving_reason'       => null,
            'created_at'           => date('Y-m-d H:i:s'),
            'nationalite'          => '',
            'entreprise_id'        => $entrepriseId
        ];
    }

    private function generateUniqueEmployeeId()
    {
        $prefix = 'ADMIN-';
        $value = $prefix . date('YmdHis') . '-' . mt_rand(1000, 9999);

        $exists = $this->db->select('id')->from('staff')->where('employee_id', $value)->limit(1)->get()->row();
        if ($exists) {
            return $this->generateUniqueEmployeeId();
        }

        return $value;
    }

    private function createDefaultEntrepriseSettings($entrepriseId, $companyName, $email, $phone, $address)
    {
        $settingsData = [
            'name' => $companyName,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'lang_id' => 1,
            'session_id' => 1,
            'currency' => 'XOF',
            'currency_symbol' => 'FCFA',
            'date_format' => 'd-m-Y',
            'time_format' => 'H:i:s',
            'start_month' => 'January',
            'start_week' => 'Monday',
            'is_rtl' => 'disabled',
            'theme' => 'default.jpg',
            'biometric' => 0,
            'biometric_device' => '',
            'currency_place' => 'after_number',
            'dise_code' => '',
            'attendence_type' => 0,
            'fee_due_days' => 0,
            'adm_auto_insert' => 1,
            'adm_prefix' => 'ssadm' . date('y'),
            'adm_start_from' => '1',
            'adm_no_digit' => 6,
            'adm_update_status' => 1,
            'staffid_auto_insert' => 1,
            'staffid_prefix' => 'staff' . date('y'),
            'staffid_start_from' => '1',
            'staffid_no_digit' => 6,
            'staffid_update_status' => 1,
            'class_teacher' => 'no',
            'is_duplicate_fees_invoice' => 0,
            'is_student_house' => 1,
            'is_blood_group' => 1,
            'online_admission' => 0,
            'online_admission_payment' => '',
            'online_admission_amount' => 0,
            'online_admission_instruction' => '',
            'online_admission_conditions' => '',
            'timezone' => 'UTC',
            'cron_secret_key' => md5(uniqid(rand(), true)),
            'image' => '',
            'admin_logo' => '',
            'admin_small_logo' => '',
            'app_logo' => '',
            'app_primary_color_code' => '#273772',
            'app_secondary_color_code' => '#ffc107',
            'mobile_api_url' => '',
            'student_profile_edit' => 0,
            'my_question' => 0,
            'roll_no' => 1,
            'category' => 1,
            'cast' => 1,
            'religion' => 1,
            'mobile_no' => 1,
            'student_email' => 1,
            'admission_date' => 1,
            'lastname' => 1,
            'middlename' => 1,
            'student_photo' => 1,
            'student_height' => 1,
            'student_weight' => 1,
            'measurement_date' => 1,
            'father_name' => 1,
            'father_phone' => 1,
            'father_occupation' => 1,
            'father_pic' => 1,
            'mother_name' => 1,
            'mother_phone' => 1,
            'mother_occupation' => 1,
            'mother_pic' => 1,
            'guardian_name' => 1,
            'guardian_relation' => 1,
            'guardian_phone' => 1,
            'guardian_email' => 1,
            'guardian_pic' => 1,
            'guardian_occupation' => 1,
            'guardian_address' => 1,
            'current_address' => 1,
            'permanent_address' => 1,
            'route_list' => 1,
            'hostel_id' => 1,
            'bank_account_no' => 1,
            'ifsc_code' => 1,
            'bank_name' => 1,
            'national_identification_no' => 1,
            'local_identification_no' => 1,
            'rte' => 1,
            'previous_school_details' => 1,
            'student_note' => 1,
            'upload_documents' => 1,
            'staff_designation' => 1,
            'staff_department' => 1,
            'staff_last_name' => 1,
            'staff_father_name' => 1,
            'staff_mother_name' => 1,
            'staff_date_of_joining' => 1,
            'staff_phone' => 1,
            'staff_emergency_contact' => 1,
            'staff_marital_status' => 1,
            'staff_photo' => 1,
            'staff_current_address' => 1,
            'staff_permanent_address' => 1,
            'staff_qualification' => 1,
            'staff_work_experience' => 1,
            'staff_note' => 1,
            'staff_epf_no' => 1,
            'staff_basic_salary' => 1,
            'staff_contract_type' => 1,
            'staff_work_shift' => 1,
            'staff_work_location' => 1,
            'staff_leaves' => 1,
            'staff_account_details' => 1,
            'staff_social_media' => 1,
            'staff_upload_documents' => 1,
            'entreprise_id' => $entrepriseId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('sch_settings', $settingsData);
        return (bool) $this->db->insert_id();
    }

    private function clonePermissions($sourceId, $targetId)
    {
        $permissions = $this->db->get_where('roles_permissions', [
            'role_id' => 1, 
            'entreprise_id' => (int)$sourceId
        ])->result_array();

        if (empty($permissions)) {
            $permissions = $this->db->get_where('roles_permissions', [
                'role_id' => 1, 
                'entreprise_id' => 1
            ])->result_array();
        }

        if (!empty($permissions)) {
            foreach ($permissions as $perm) {
                unset($perm['id']);
                $perm['entreprise_id'] = (int)$targetId;
                $this->db->insert('roles_permissions', $perm);
            }
        }
    }

    private function sendCredentialsEmail($to, $companyName, $login, $plainPassword)
    {
        $this->load->library('email');
        
        $config['protocol']  = 'smtp';
        $config['smtp_host'] = 'mail.diagomap.com';
        $config['smtp_user'] = 'info@diagomap.com';
        $config['smtp_pass'] = 'dX4$wRyExMTzp94';
        $config['smtp_port'] = 587;
        $config['smtp_crypto'] = 'tls';
        $config['mailtype']  = 'html';
        $config['charset']   = 'utf-8';
        $config['newline']   = "\r\n";

        $this->email->initialize($config);
        $this->email->from('no-reply@diagomap.com', 'Diagomap ERP');
        $this->email->to($to);
        $this->email->subject('Bienvenue - Vos accès à l\'ERP');

        $message = "
        <html>
        <head><meta charset='UTF-8'></head>
        <body style='font-family: Arial, sans-serif; padding:20px;'>
            <h2>Bienvenue sur Diagomap ERP, <strong>{$companyName}</strong> !</h2>
            <p>Votre compte a été créé avec succès. Voici vos identifiants de connexion :</p>
            <ul style='list-style:none; padding:0;'>
                <li><strong>🔗 Lien de connexion :</strong> <a href='".base_url()."'>".base_url()."</a></li>
                <li><strong>👤 Identifiant :</strong> {$login}</li>
                <li><strong>🔑 Mot de passe provisoire :</strong> <span style='background:#f0f0f0;padding:4px 8px;border-radius:4px;'>{$plainPassword}</span></li>
            </ul>
            <p style='color:red;'><strong>⚠️ Important :</strong> Changez votre mot de passe dès la première connexion.</p>
            <hr>
            <p style='font-size:12px;color:#888;'>Cet email a été généré automatiquement.</p>
        </body>
        </html>
        ";

        $this->email->message($message);
        $this->email->send();
    }

    private function generateRandomPassword($length = 12)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        return substr(str_shuffle($chars), 0, $length);
    }

    public function ajaxSearch()
    {
        if (!$this->rbac->hasPrivilege('comptes', 'can_view')) {
            echo json_encode(['error' => 'Accès refusé']);
            return;
        }

        $search = $this->input->post('search')['value'] ?? '';
        $order_column = $this->input->post('order')[0]['column'] ?? 0;
        $order_dir = $this->input->post('order')[0]['dir'] ?? 'asc';
        $limit = $this->input->post('length') ?? 10;
        $offset = $this->input->post('start') ?? 0;

        $this->db->select('c.*, p.nom as parent_nom')
            ->from('compte_entreprise c')
            ->join('compte_entreprise p', 'c.parent_entreprise_id = p.id', 'left');

        if (!empty($search)) {
            $this->db->group_start()
                ->like('c.nom', $search)
                ->or_like('c.email', $search)
                ->or_like('c.telephone', $search)
                ->or_like('c.adresse', $search)
                ->or_like('c.forfait', $search)
                ->or_like('c.statut', $search)
                ->group_end();
        }

        $records_filtered = $this->db->count_all_results('', false);

        $this->db->order_by($order_column == 0 ? 'c.nom' : 'c.id', $order_dir);
        $this->db->limit($limit, $offset);
        $companies = $this->db->get()->result();

        $records_total = $this->db->count_all('compte_entreprise');

        $dt_data = [];
        $permission_edit = $this->rbac->hasPrivilege('comptes', 'can_edit');
        $permission_delete = $this->rbac->hasPrivilege('comptes', 'can_delete');
        $permission_view = $this->rbac->hasPrivilege('comptes', 'can_view');

        foreach ($companies as $company) {
            $action = "";

            if ($permission_view) {
                $action .= "<button onclick='viewCompanyModal($company->id)' class='btn btn-view btn-xs' data-toggle='tooltip' title='Voir détails'>
                    <i class='fa fa-eye'></i>
                </button> ";
            }

            if ($permission_edit) {
                $action .= "<button onclick='editCompanyModal($company->id)' class='btn btn-edit btn-xs' data-toggle='tooltip' title='Modifier'>
                    <i class='fa fa-pencil'></i>
                </button> ";
            }

            if ($permission_edit) {
                if ($company->statut == 'actif') {
                    $action .= "<button onclick='toggleStatus($company->id, \"actif\")' class='btn btn-suspend btn-xs' title='Suspendre'>
                        <i class='fa fa-pause'></i>
                    </button> ";
                } elseif ($company->statut == 'suspendu' || $company->statut == 'expiré') {
                    $action .= "<button onclick='toggleStatus($company->id, \"suspendu\")' class='btn btn-activate btn-xs' title='Activer'>
                        <i class='fa fa-play'></i>
                    </button> ";
                }
            }

            if ($permission_delete) {
                $action .= "<a href='" . site_url('admin/comptes/delete/'.$company->id) . "' class='btn btn-deactivate btn-xs' data-toggle='tooltip' title='Supprimer' onclick='return confirm(\"Voulez-vous supprimer ?\");'>
                    <i class='fa fa-remove'></i>
                </a>";
            }

            $statut_class = ($company->statut == 'actif') ? "success" : (($company->statut == 'suspendu') ? 'warning' : 'danger');
            
            $dt_data[] = array(
                html_escape($company->nom),
                html_escape($company->email),
                html_escape($company->telephone),
                html_escape($company->adresse),
                $this->formatStructureBadge($company),
                !empty($company->parent_nom) ? html_escape($company->parent_nom) : '-',
                $this->styleForfait($company->forfait),
                html_escape($company->date_debut),
                html_escape($company->date_expiration),
                "<span class='label label-{$statut_class}'>" . html_escape($company->statut) . "</span>",
                $action
            );
        }

        echo json_encode([
            "draw" => intval($this->input->post('draw')),
            "recordsTotal" => intval($records_total),
            "recordsFiltered" => intval($records_filtered),
            "data" => $dt_data,
        ]);
    }

    private function styleForfait($forfait)
    {
        $forfait = strtolower(trim($forfait));
        $class = 'default';
        
        switch ($forfait) {
            case "basic": $class = "primary"; break;
            case "standard": $class = "success"; break;
            case "premium": $class = "info"; break;
            case "pro": $class = "danger"; break;
        }

        return "<span class='badge badge-{$class}' style='padding:6px 10px;font-size:13px;border-radius:8px;text-transform:uppercase;letter-spacing:1px;'>{$forfait}</span>";
    }

    private function formatStructureBadge($company)
    {
        $type = isset($company->type_structure) && $company->type_structure === 'succursale' ? 'succursale' : 'siege';
        $label = $type === 'succursale' ? 'Succursale' : 'Siège';
        $class = $type === 'succursale' ? 'warning' : 'primary';
        $code = !empty($company->code_succursale) ? '<br><small>' . html_escape($company->code_succursale) . '</small>' : '';
        return "<span class='label label-{$class}'>{$label}</span>{$code}";
    }

    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('comptes', 'can_view')) {
            access_denied();
        }
        $data['title'] = 'Expense Head List';
        $category = $this->comptes_model->get($id);
        $data['category'] = $category;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/comptes/comptesView', $data);
        $this->load->view('layout/footer', $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('comptes', 'can_delete')) {
            access_denied();
        }
        $this->comptes_model->remove($id);
        redirect('admin/comptes/index');
    }

    public function toggle_status($id)
    {
        $new_status = $this->input->post('new_status', true);

        if (!in_array($new_status, ['actif','expiré','suspendu'])) {
            echo json_encode(['success' => false, 'message' => 'Statut invalide']);
            return;
        }

        $update = $this->db->where('id', $id)->update('compte_entreprise', ['statut' => $new_status]);

        if ($update) {
            echo json_encode(['success' => true, 'message' => 'Statut mis à jour']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    public function ajax_view($id)
    {
        if (!$this->rbac->hasPrivilege('comptes', 'can_view')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            return;
        }

        $company = $this->comptes_model->get($id);

        if ($company) {
            $jours_restants = 0;
            if (!empty($company->date_expiration)) {
                $date_expiration = new DateTime($company->date_expiration);
                $aujourdhui = new DateTime();
                $diff = $date_expiration->diff($aujourdhui);
                $jours_restants = $diff->days;
                if ($date_expiration < $aujourdhui) {
                    $jours_restants = -$jours_restants;
                }
            }

            $data['company'] = $company;
            $data['jours_restants'] = $jours_restants;
            $data['branches'] = $this->comptes_model->getBranchesByHeadOffice((int) $id);

            $html = $this->load->view('admin/comptes/partials/company_details', $data, true);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'html' => $html]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Entreprise non trouvée']);
        }
    }

    public function ajax_edit($id)
    {
        if (!$this->rbac->hasPrivilege('comptes', 'can_edit')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            return;
        }

        $company = $this->comptes_model->get($id);

        if ($company) {
            $data['company'] = $company;
            $data['id'] = $id;
            $data['head_offices'] = $this->comptes_model->getHeadOfficeOptions((int) $id);
            $data['branch_relation'] = $this->comptes_model->getBranchRelation((int) $id);
            $html = $this->load->view('admin/comptes/partials/company_edit_form', $data, true);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'html' => $html]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Entreprise non trouvée']);
        }
    }

    public function update_ajax()
    {
        if (!$this->rbac->hasPrivilege('comptes', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            return;
        }

        $companyId = (int) $this->input->post('id');
        $company = $this->comptes_model->get($companyId);
        if (!$company) {
            echo json_encode(['success' => false, 'message' => 'Entreprise introuvable']);
            return;
        }

        $updateData = array(
            'nom' => $this->input->post('nom', true),
            'email' => $this->input->post('email', true),
            'telephone' => $this->input->post('telephone', true),
            'adresse' => $this->input->post('adresse', true),
            'forfait' => $this->input->post('forfait', true),
            'statut' => $this->input->post('statut', true),
            'date_debut' => $this->input->post('date_debut', true),
            'date_expiration' => $this->input->post('date_expiration', true),
            'rccm' => $this->input->post('rccm', true),
            'ncc' => $this->input->post('ncc', true),
            'contact_nom' => $this->input->post('contact_nom', true),
            'limite_utilisateurs' => $this->input->post('limite_utilisateurs', true),
            'slug' => $this->input->post('slug', true),
            'admin_username' => $this->input->post('admin_username', true),
            'admin_email' => $this->input->post('admin_email', true),
            'updated_at' => date('Y-m-d H:i:s'),
        );

        if (!empty($_FILES['logo']['name'])) {
            $config['upload_path']   = './uploads/front_office/logo_entreprises/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size']      = 2048;
            $config['file_name']     = time() . '_' . $_FILES['logo']['name'];
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('logo')) {
                $uploadData = $this->upload->data();
                $updateData['logo'] = $uploadData['file_name'];
            }
        }

        if ($this->input->post('admin_password')) {
            $hashedPassword = $this->enc_lib->passHashEnc((string) $this->input->post('admin_password'));
            $updateData['admin_password'] = $hashedPassword;
        }

        $updated = $this->comptes_model->update($companyId, $updateData);

        if ($updated) {
            echo json_encode(['success' => true, 'message' => 'Entreprise mise à jour avec succès.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour.']);
        }
    }

    private function getCurrentEntrepriseSession()
    {
        $admin = $this->session->userdata('admin');
        return is_array($admin) ? $admin : array();
    }

    private function getCurrentEntrepriseId()
    {
        $admin = $this->getCurrentEntrepriseSession();
        if (!empty($admin['entreprise_id'])) {
            return (int) $admin['entreprise_id'];
        }
        return (int) $this->session->userdata('entreprise_id');
    }

    private function getCurrentEntrepriseAccount()
    {
        $entrepriseId = $this->getCurrentEntrepriseId();
        if ($entrepriseId <= 0) {
            return null;
        }
        return $this->comptes_model->get($entrepriseId);
    }

    private function canCurrentEntrepriseManageBranches()
    {
        $company = $this->getCurrentEntrepriseAccount();
        if (!$company) {
            return false;
        }

        if (!empty($company->type_structure) && $company->type_structure === 'succursale') {
            return false;
        }

        if (!empty($company->statut) && in_array(strtolower((string) $company->statut), array('suspendu', 'expiré'), true)) {
            return false;
        }

        if ($this->db->field_exists('can_manage_succursales', 'compte_entreprise')) {
            return isset($company->can_manage_succursales) && (int) $company->can_manage_succursales === 1;
        }

        return false;
    }
}