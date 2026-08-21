<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Comptes extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('mailsmsconf');  // ← ajoutez cette ligne
        $this->load->library('enc_lib'); // <-- AJOUT
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

    public function ajaxSearch()
    {
        $expense_head = $this->comptes_model->getDatatableExpenseHead();
        $expense_head = json_decode($expense_head);
        $dt_data      = array();

        if (!empty($expense_head->data)) {

            $permission_edit = $this->rbac->hasPrivilege('comptes', 'can_edit');
            $permission_delete = $this->rbac->hasPrivilege('comptes', 'can_delete');
            $permission_view = $this->rbac->hasPrivilege('comptes', 'can_view');

            foreach ($expense_head->data as $exhead_value) {

                // --- DESIGN CHIC POUR LES FORFAITS ---
                $forfait_badge = $this->styleForfait($exhead_value->forfait);

                // ======================
                // ACTIONS
                // ======================
                $action = "";

                if ($permission_view) {
                    $action .= "<button onclick='viewCompanyModal($exhead_value->id)' class='btn btn-view btn-xs' data-toggle='tooltip' title='Voir détails'>
                    <i class='fa fa-eye'></i>
                </button> ";
                }

                if ($permission_edit) {
                    $action .= "<button onclick='editCompanyModal($exhead_value->id)' class='btn btn-edit btn-xs' data-toggle='tooltip' title='Modifier'>
                    <i class='fa fa-pencil'></i>
                </button> ";
                }

                // Bouton Activer / Suspendre selon le statut
                if ($permission_edit) {
                    if ($exhead_value->statut == 'actif') {
                        $action .= "<button onclick='toggleStatus($exhead_value->id, \"actif\")' class='btn btn-suspend btn-xs' title='Suspendre'>
                        <i class='fa fa-pause'></i>
                    </button> ";
                    } elseif ($exhead_value->statut == 'suspendu') {
                        $action .= "<button onclick='toggleStatus($exhead_value->id, \"suspendu\")' class='btn btn-activate btn-xs' title='Activer'>
                        <i class='fa fa-play'></i>
                    </button> ";
                    }
                    elseif ($exhead_value->statut == 'expiré') {
                        $action .= "<button onclick='toggleStatus($exhead_value->id, \"expiré\")' class='btn btn-activate btn-xs' title='Activer'>
                        <i class='fa fa-play'></i>
                    </button> ";
                    }
                }

                if ($permission_delete) {
                    $action .= "<a href='" . site_url('admin/comptes/delete/'.$exhead_value->id) . "' class='btn btn-deactivate btn-xs' data-toggle='tooltip' title='Supprimer' onclick='return confirm(\"Voulez-vous supprimer ?\");'>
                    <i class='fa fa-remove'></i>
                </a>";
                }

                // ======================
                // CHAMPS
                // ======================
                $nom = "<a href='#' data-toggle='popover' class='detail_popover'>{$exhead_value->nom}</a>";
                $email = "<a href='#' data-toggle='popover' class='detail_popover'>{$exhead_value->email}</a>";
                $telephone = "<a href='#' data-toggle='popover' class='detail_popover'>{$exhead_value->telephone}</a>";
                $adresse = "<a href='#' data-toggle='popover' class='detail_popover'>{$exhead_value->adresse}</a>";

                $forfait = $forfait_badge;

                $date_debut = "<span class='label label-primary'>{$exhead_value->date_debut}</span>";
                $date_expiration = "<span class='label label-warning'>{$exhead_value->date_expiration}</span>";

                // statut coloré
                $statut_class = ($exhead_value->statut == 'actif') ? "success" : "danger";
                $statut = "<span class='label label-{$statut_class}'>{$exhead_value->statut}</span>";

                $dt_data[] = array(
                    $nom,
                    $email,
                    $telephone,
                    $adresse,
                    $forfait,
                    $date_debut,
                    $date_expiration,
                    $statut,
                    $action
                );
            }
        }

        $json_data = array(
            "draw"            => intval($expense_head->draw),
            "recordsTotal"    => intval($expense_head->recordsTotal),
            "recordsFiltered" => intval($expense_head->recordsFiltered),
            "data"            => $dt_data,
        );

        echo json_encode($json_data);
    }




    /**
     * STYLE CHIC POUR LES FORFAITS
     * Exemple de badges :
     *  - BASIC      → bleu
     *  - STANDARD   → vert
     *  - PREMIUM    → violet
     *  - PRO        → rouge
     */

    public function toggle_status($id)
    {
        $new_status = $this->input->post('new_status', true);

        // Valider le statut
        if (!in_array($new_status, ['actif','expiré','suspendu'])) {
            echo json_encode(['success' => false, 'message' => 'Statut invalide']);
            return;
        }

        $update = $this->db->where('id', $id)
            ->update('compte_entreprise', ['statut' => $new_status]);

        if ($update) {
            echo json_encode(['success' => true, 'message' => 'Statut mis à jour']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }




    private function styleForfait($forfait)
    {
        $forfait = strtolower(trim($forfait));

        switch ($forfait) {
            case "basic":
                $class = "primary";
                break;

            case "standard":
                $class = "success";
                break;

            case "premium":
                $class = "info";
                break;

            case "pro":
                $class = "danger";
                break;

            default:
                $class = "default";
                break;
        }

        return "<span class='badge badge-{$class}' style='padding:6px 10px;font-size:13px;border-radius:8px;text-transform:uppercase;letter-spacing:1px;'>
                {$forfait}
            </span>";
    }


    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('comptes', 'can_view')) {
            access_denied();
        }
        $data['title']    = 'Expense Head List';
        $category         = $this->comptes_model->get($id);
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
        $data['title'] = 'Comptes List';
        $this->comptes_model->remove($id);
        redirect('admin/comptes/index');
    }

    public function create_12()
    {
        if (!$this->rbac->hasPrivilege('comptes', 'can_add')) {
            access_denied();
        }

        $data['title'] = 'Add Expense Head';

        // ===================== VALIDATION =====================
        $this->form_validation->set_rules('nom', 'Nom entreprise', 'trim|required|xss_clean');
        $this->form_validation->set_rules('admin_email', 'Email admin', 'trim|required|valid_email|xss_clean');
        $this->form_validation->set_rules('admin_username', 'Nom admin', 'trim|required|xss_clean');
        // Ajoutez ici les autres règles si nécessaire (contact_nom, téléphone, etc.)

        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/comptes/comptes_create', $data);
            $this->load->view('layout/footer', $data);
            return;
        }

        // ===================== GÉNÉRATION MOT DE PASSE =====================
        $plainPassword = $this->generateRandomPassword(12);
        $hashedPassword = $this->enc_lib->passHashEnc($plainPassword); // ← hashage compatible avec l'application

        // ===================== DONNÉES DE L'ENTREPRISE =====================
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
            'created_at'              => date('Y-m-d H:i:s'),
            'updated_at'              => date('Y-m-d H:i:s'),
        ];

        // Gestion du logo
        if (!empty($_FILES['logo']['name'])) {
            $config['upload_path']   = './uploads/front_office/logo_entreprises/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size']      = 2048;
            $config['file_name']     = time() . '_' . $_FILES['logo']['name'];
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('logo')) {
                $uploadData = $this->upload->data();
                $companyData['logo'] = $uploadData['file_name'];
            } else {
                $companyData['logo'] = "";
            }
        }

        // ===================== TRANSACTION =====================
        $this->db->trans_start();

        // Insertion entreprise
        $this->comptes_model->add($companyData);
        $entrepriseId = $this->db->insert_id();

        if (!$entrepriseId) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la création de l\'entreprise.</div>');
            redirect('admin/comptes/index');
        }

        // Insertion admin dans la table `staff`
        // IMPORTANT : tous les champs NOT NULL sans valeur par défaut doivent être remplis
        $staffData = [
            'employee_id'          => 'ADMIN-' . time(),
            'lang_id'              => 1,
            'department'           => 0,
            'designation'          => 0,
            'qualification'        => '',
            'work_exp'             => '',
            'name'                 => $this->input->post('admin_username'),
            'surname'              => '',
            'father_name'          => '',
            'mother_name'          => '',
            'contact_no'           => $this->input->post('telephone') ?: '',
            'emergency_contact_no' => '',
            'email'                => $this->input->post('admin_email'),
            'dob'                  => '2000-01-01',
            'marital_status'       => '',
            'date_of_joining'      => date('Y-m-d'),
            'date_of_leaving'      => '0000-00-00',
            'local_address'        => $this->input->post('adresse') ?: '',
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
            'entreprise_id'        => $entrepriseId // ← Lien avec l'entreprise
        ];

        $this->db->insert('staff', $staffData);
        $staffId = $this->db->insert_id();

        if ($this->db->trans_complete() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la création du compte admin.</div>');
            redirect('admin/comptes/index');
        }

        // ===================== ENVOI DE L'EMAIL (via mailsmsconf) =====================
        $teacher_login_detail = [
            'id'             => $staffId,
            'credential_for' => 'staff',
            'username'       => $this->input->post('admin_username'),
            'password'       => $plainPassword, // ← en clair pour l'email
            'contact_no'     => $this->input->post('telephone') ?: '',
            'email'          => $this->input->post('admin_email')
        ];

        $this->mailsmsconf->mailsms('login_credential', $teacher_login_detail);

        // ===================== SUCCÈS =====================
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Compte créé avec succès ! Les identifiants ont été envoyés par email.</div>');
        redirect('admin/comptes/index');
    }

    public function create()
    {
        if (!$this->rbac->hasPrivilege('comptes', 'can_add')) {
            access_denied();
        }

        $data['title'] = 'Add Expense Head';

        // Validation
        $this->form_validation->set_rules('nom', 'Nom entreprise', 'trim|required|xss_clean');
        $this->form_validation->set_rules('admin_email', 'Email admin', 'trim|required|valid_email|xss_clean');
        $this->form_validation->set_rules('admin_username', 'Nom admin', 'trim|required|xss_clean');

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
            'created_at'              => date('Y-m-d H:i:s'),
            'updated_at'              => date('Y-m-d H:i:s'),
        ];

        // Gestion du logo
        if (!empty($_FILES['logo']['name'])) {
            $config['upload_path']   = './uploads/front_office/logo_entreprises/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size']      = 2048;
            $config['file_name']     = time() . '_' . $_FILES['logo']['name'];
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('logo')) {
                $uploadData = $this->upload->data();
                $companyData['logo'] = $uploadData['file_name'];
            } else {
                $companyData['logo'] = "";
            }
        }

        // ===================== TRANSACTION =====================
        $this->db->trans_start();

        // 1. Insertion entreprise
        $this->comptes_model->add($companyData);
        $entrepriseId = $this->db->insert_id();

        if (!$entrepriseId) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la création de l\'entreprise.</div>');
            redirect('admin/comptes/index');
        }

        // 2. Insertion admin dans staff
        $staffData = [
            'employee_id'          => 'ADMIN-' . time(),
            'lang_id'              => 1,
            'department'           => 0,
            'designation'          => 0,
            'qualification'        => '',
            'work_exp'             => '',
            'name'                 => $this->input->post('admin_username'),
            'surname'              => '',
            'father_name'          => '',
            'mother_name'          => '',
            'contact_no'           => $this->input->post('telephone') ?: '',
            'emergency_contact_no' => '',
            'email'                => $this->input->post('admin_email'),
            'dob'                  => '2000-01-01',
            'marital_status'       => '',
            'date_of_joining'      => date('Y-m-d'),
            'date_of_leaving'      => '0000-00-00',
            'local_address'        => $this->input->post('adresse') ?: '',
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
        $this->db->insert('staff', $staffData);
        $staffId = $this->db->insert_id();

        // 3. Attribution du rôle Admin
        $roleData = ['staff_id' => $staffId, 'role_id' => 1];
        $this->db->insert('staff_roles', $roleData);

        // 4. Duplication des permissions
        $permissions = $this->db->get_where('roles_permissions', ['role_id' => 1, 'entreprise_id' => 1])->result_array();
        if (!empty($permissions)) {
            foreach ($permissions as $perm) {
                unset($perm['id']);
                $perm['entreprise_id'] = $entrepriseId;
                $this->db->insert('roles_permissions', $perm);
            }
        }

        // ==================== 5. CRÉATION DU SETTINGS DE L'ENTREPRISE ====================
        $default_lang_id = 1;
        $default_session_id = 1;

        $settingsData = [
            'name'                      => $this->input->post('nom'),
            'email'                     => $this->input->post('email') ?: $this->input->post('admin_email'),
            'phone'                     => $this->input->post('telephone') ?: '',
            'address'                   => $this->input->post('adresse') ?: '',
            'lang_id'                   => $default_lang_id,
            'session_id'                => $default_session_id,
            'currency'                  => 'XOF',
            'currency_symbol'           => 'FCFA',
            'date_format'               => 'd-m-Y',
            'time_format'               => 'H:i:s',
            'start_month'               => 'January',
            'start_week'                => 'Monday',
            'is_rtl'                    => 'disabled',
            'theme'                     => 'default.jpg',
            'biometric'                 => 0,
            'biometric_device'          => '',
            'currency_place'            => 'after_number',
            'dise_code'                 => '',
            'attendence_type'           => 0,
            'fee_due_days'              => 0,
            'adm_auto_insert'           => 1,
            'adm_prefix'                => 'ssadm' . date('y'),
            'adm_start_from'            => '1',
            'adm_no_digit'              => 6,
            'adm_update_status'         => 1,
            'staffid_auto_insert'       => 1,
            'staffid_prefix'            => 'staff' . date('y'),
            'staffid_start_from'        => '1',
            'staffid_no_digit'          => 6,
            'staffid_update_status'     => 1,
            'class_teacher'             => 'no',
            'is_duplicate_fees_invoice' => 0,
            'is_student_house'          => 1,
            'is_blood_group'            => 1,
            'online_admission'          => 0,
            'online_admission_payment'  => '',
            'online_admission_amount'   => 0,
            'online_admission_instruction' => '',
            'online_admission_conditions'  => '',
            'timezone'                  => 'UTC',
            'cron_secret_key'           => md5(uniqid(rand(), true)),
            'image'                     => '',
            'admin_logo'                => '',
            'admin_small_logo'          => '',
            'app_logo'                  => '',
            'app_primary_color_code'    => '#273772',
            'app_secondary_color_code'  => '#ffc107',
            'mobile_api_url'            => '',
            'student_profile_edit'      => 0,
            'my_question'               => 0,
            'roll_no'                   => 1,
            'category'                  => 1,
            'cast'                      => 1,
            'religion'                  => 1,
            'mobile_no'                 => 1,
            'student_email'             => 1,
            'admission_date'            => 1,
            'lastname'                  => 1,
            'middlename'                => 1,
            'student_photo'             => 1,
            'student_height'            => 1,
            'student_weight'            => 1,
            'measurement_date'          => 1,
            'father_name'               => 1,
            'father_phone'              => 1,
            'father_occupation'         => 1,
            'father_pic'                => 1,
            'mother_name'               => 1,
            'mother_phone'              => 1,
            'mother_occupation'         => 1,
            'mother_pic'                => 1,
            'guardian_name'             => 1,
            'guardian_relation'         => 1,
            'guardian_phone'            => 1,
            'guardian_email'            => 1,
            'guardian_pic'              => 1,
            'guardian_occupation'       => 1,
            'guardian_address'          => 1,
            'current_address'           => 1,
            'permanent_address'         => 1,
            'route_list'                => 1,
            'hostel_id'                 => 1,
            'bank_account_no'           => 1,
            'ifsc_code'                 => 1,
            'bank_name'                 => 1,
            'national_identification_no' => 1,
            'local_identification_no'   => 1,
            'rte'                       => 1,
            'previous_school_details'   => 1,
            'student_note'              => 1,
            'upload_documents'          => 1,
            'staff_designation'         => 1,
            'staff_department'          => 1,
            'staff_last_name'           => 1,
            'staff_father_name'         => 1,
            'staff_mother_name'         => 1,
            'staff_date_of_joining'     => 1,
            'staff_phone'               => 1,
            'staff_emergency_contact'   => 1,
            'staff_marital_status'      => 1,
            'staff_photo'               => 1,
            'staff_current_address'     => 1,
            'staff_permanent_address'   => 1,
            'staff_qualification'       => 1,
            'staff_work_experience'     => 1,
            'staff_note'                => 1,
            'staff_epf_no'              => 1,
            'staff_basic_salary'        => 1,
            'staff_contract_type'       => 1,
            'staff_work_shift'          => 1,
            'staff_work_location'       => 1,
            'staff_leaves'              => 1,
            'staff_account_details'     => 1,
            'staff_social_media'        => 1,
            'staff_upload_documents'    => 1,
            'entreprise_id'             => $entrepriseId
        ];

        $this->db->insert('sch_settings', $settingsData);

        // Vérification de la transaction
        if ($this->db->trans_complete() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la création du compte. Veuillez réessayer.</div>');
            redirect('admin/comptes/index');
        }

        // ===================== ENVOI DE L'EMAIL =====================
        $teacher_login_detail = [
            'id'             => $staffId,
            'credential_for' => 'staff',
            'username'       => $this->input->post('admin_username'),
            'password'       => $plainPassword,
            'contact_no'     => $this->input->post('telephone') ?: '',
            'email'          => $this->input->post('admin_email')
        ];
        $this->mailsmsconf->mailsms('login_credential', $teacher_login_detail);

        // ===================== SUCCÈS =====================
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Compte créé avec succès ! Les identifiants ont été envoyés par email.</div>');
        redirect('admin/comptes/index');
    }
    public function create_15072026()
    {
        if (!$this->rbac->hasPrivilege('comptes', 'can_add')) {
            access_denied();
        }

        $data['title'] = 'Add Expense Head';

        // ===================== VALIDATION =====================
        $this->form_validation->set_rules('nom', 'Nom entreprise', 'trim|required|xss_clean');
        $this->form_validation->set_rules('admin_email', 'Email admin', 'trim|required|valid_email|xss_clean');
        $this->form_validation->set_rules('admin_username', 'Nom admin', 'trim|required|xss_clean');
        // Ajoutez ici les autres règles si nécessaire

        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/comptes/comptes_create', $data);
            $this->load->view('layout/footer', $data);
            return;
        }

        // ===================== GÉNÉRATION MOT DE PASSE =====================
        $plainPassword = $this->generateRandomPassword(12);
        $hashedPassword = $this->enc_lib->passHashEnc($plainPassword); // Hash compatible

        // ===================== DONNÉES DE L'ENTREPRISE =====================
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
            'created_at'              => date('Y-m-d H:i:s'),
            'updated_at'              => date('Y-m-d H:i:s'),
        ];

        // Gestion du logo
        if (!empty($_FILES['logo']['name'])) {
            $config['upload_path']   = './uploads/front_office/logo_entreprises/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size']      = 2048;
            $config['file_name']     = time() . '_' . $_FILES['logo']['name'];
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('logo')) {
                $uploadData = $this->upload->data();
                $companyData['logo'] = $uploadData['file_name'];
            } else {
                $companyData['logo'] = "";
            }
        }

        // ===================== TRANSACTION =====================
        $this->db->trans_start();

        // 1. Insertion entreprise
        $this->comptes_model->add($companyData);
        $entrepriseId = $this->db->insert_id();

        if (!$entrepriseId) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la création de l\'entreprise.</div>');
            redirect('admin/comptes/index');
        }

        // 2. Insertion admin dans `staff` (avec tous les champs obligatoires)
        $staffData = [
            'employee_id'          => 'ADMIN-' . time(),
            'lang_id'              => 1,
            'department'           => 0,
            'designation'          => 0,
            'qualification'        => '',
            'work_exp'             => '',
            'name'                 => $this->input->post('admin_username'),
            'surname'              => '',
            'father_name'          => '',
            'mother_name'          => '',
            'contact_no'           => $this->input->post('telephone') ?: '',
            'emergency_contact_no' => '',
            'email'                => $this->input->post('admin_email'),
            'dob'                  => '2000-01-01',
            'marital_status'       => '',
            'date_of_joining'      => date('Y-m-d'),
            'date_of_leaving'      => '0000-00-00',
            'local_address'        => $this->input->post('adresse') ?: '',
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

        $this->db->insert('staff', $staffData);
        $staffId = $this->db->insert_id();

        if (!$staffId) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la création de l\'administrateur.</div>');
            redirect('admin/comptes/index');
        }

        // 3. Attribution du rôle Admin (role_id = 1) dans staff_roles
        $roleData = [
            'staff_id' => $staffId,
            'role_id'  => 1 // ID du rôle "Admin" dans votre table roles
        ];
        $this->db->insert('staff_roles', $roleData);

        // 4. Duplication des permissions du rôle Admin (role_id=1) pour la nouvelle entreprise
        // Récupération des permissions associées à role_id=1 et entreprise_id=1 (entreprise par défaut)
        $permissions = $this->db->get_where('roles_permissions', ['role_id' => 1, 'entreprise_id' => 1])->result_array();
        if (!empty($permissions)) {
            foreach ($permissions as $perm) {
                unset($perm['id']); // Supprimer l'ID existant pour générer un nouveau
                $perm['entreprise_id'] = $entrepriseId;
                $this->db->insert('roles_permissions', $perm);
            }
        }

        // Vérification de la transaction
        if ($this->db->trans_complete() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la création du compte. Veuillez réessayer.</div>');
            redirect('admin/comptes/index');
        }

        // ===================== ENVOI DE L'EMAIL (via mailsmsconf) =====================
        $teacher_login_detail = [
            'id'             => $staffId,
            'credential_for' => 'staff',
            'username'       => $this->input->post('admin_username'),
            'password'       => $plainPassword, // Mot de passe en clair pour l'email
            'contact_no'     => $this->input->post('telephone') ?: '',
            'email'          => $this->input->post('admin_email')
        ];

        $this->mailsmsconf->mailsms('login_credential', $teacher_login_detail);

        // ===================== SUCCÈS =====================
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Compte créé avec succès ! Les identifiants ont été envoyés par email.</div>');
        redirect('admin/comptes/index');
    }
    public function create_110626()
    {
        if (!$this->rbac->hasPrivilege('comptes', 'can_add')) {
            access_denied();
        }

        $data['title'] = 'Add Expense Head';

        // Validation
        $this->form_validation->set_rules('nom', 'Nom entreprise', 'trim|required|xss_clean');
        $this->form_validation->set_rules('admin_email', 'Email admin', 'trim|required|valid_email|xss_clean');
        $this->form_validation->set_rules('admin_username', 'Nom admin', 'trim|required|xss_clean');
        // Ajoutez d'autres validations si besoin

        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/comptes/comptes_create', $data);
            $this->load->view('layout/footer', $data);
            return;
        }

        // ==============================
        // 1. Génération du mot de passe
        // ==============================
        $plainPassword = $this->generateRandomPassword(12);
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        // ==============================
        // 2. Données de l'entreprise
        // ==============================
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
            'created_at'              => date('Y-m-d H:i:s'),
            'updated_at'              => date('Y-m-d H:i:s'),
        ];

        // Logo
        if (!empty($_FILES['logo']['name'])) {
            $config['upload_path']   = './uploads/front_office/logo_entreprises/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size']      = 2048;
            $config['file_name']     = time() . '_' . $_FILES['logo']['name'];
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('logo')) {
                $uploadData = $this->upload->data();
                $companyData['logo'] = $uploadData['file_name'];
            } else {
                $companyData['logo'] = "";
            }
        }

        // ==============================
        // 3. Transaction (entreprise + admin)
        // ==============================
        $this->db->trans_start();

        // Insertion entreprise
        $this->comptes_model->add($companyData);
        $entrepriseId = $this->db->insert_id();

        if (!$entrepriseId) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la création de l\'entreprise.</div>');
            redirect('admin/comptes/index');
        }

        // ---------- Insertion admin dans `staff` ----------
        $staffData = [
            'employee_id'      => 'ADMIN-' . time(),
            'lang_id'          => 1,
            'department'       => 0,
            'designation'      => 0,
            'qualification'    => '',
            'work_exp'         => '',
            'name'             => $this->input->post('admin_username'),
            'surname'          => '',
            'father_name'      => '',
            'mother_name'      => '',
            'contact_no'       => $this->input->post('telephone') ?: '',
            'emergency_contact_no' => '',
            'email'            => $this->input->post('admin_email'),
            'dob'              => '2000-01-01',
            'marital_status'   => '',
            'date_of_joining'  => date('Y-m-d'),
            'date_of_leaving'  => '0000-00-00',
            'local_address'    => $this->input->post('adresse') ?: '',
            'permanent_address'=> '',
            'note'             => '',
            'image'            => '',
            'password'         => $hashedPassword,
            'gender'           => 'male',
            'account_title'    => '',
            'bank_account_no'  => '',
            'bank_name'        => '',
            'ifsc_code'        => '',
            'bank_branch'      => '',
            'payscale'         => '',
            'basic_salary'     => '0',
            'sursalaire'       => '0',
            'conge'            => '0',
            'categorie_salaire'=> '',
            'categorie_lettre' => '',
            'prime_anc'        => '0',
            'prime_trans'      => '0',
            'forfait_hs'       => '0',
            'prime_resp'       => '0',
            'prime_rend'       => '0',
            'prime_risque'     => '0',
            'prime_assi'       => '0',
            'prime_grati'      => '0',
            'imp_sal'          => '0',
            'contra_nat'       => '0',
            'imp_revenu'       => '0',
            'crns'             => '0',
            'cnps_no'          => '',
            'cmu'              => '0',
            'cnps_regim'       => '0',
            'cnps_tra'         => '0',
            'cnps_pres'        => '0',
            'fdfp_taxe'        => '0',
            'fdfp_form'        => '0',
            'avan_acom'        => '0',
            'autre_reve'       => '',
            'tax'              => '0',
            'bonus'            => '0',
            'epf_no'           => '',
            'contract_type'    => '',
            'shift'            => '',
            'location'         => '',
            'facebook'         => '',
            'twitter'          => '',
            'linkedin'         => '',
            'instagram'        => '',
            'resume'           => '',
            'joining_letter'   => '',
            'resignation_letter' => '',
            'other_document_name' => '',
            'other_document_file' => '',
            'user_id'          => 0, // si non utilisé
            'is_active'        => 1,
            'verification_code'=> '',
            'disable_at'       => null,
            'reason'           => null,
            'deleted'          => 0,
            'part_igr'         => '0',
            'responsable'      => '',
            'salaire_base'     => '0',
            'file_name'        => '',
            'file_size'        => 0,
            'upload_date'      => date('Y-m-d H:i:s'),
            'cmu_enfant'       => '0',
            'taxes'            => '0',
            'leaving_reason'   => null,
            'created_at'       => date('Y-m-d H:i:s'),
            'nationalite'      => '',
            'entreprise_id'    => $entrepriseId // 🔗 lien avec l'entreprise
        ];

        $this->db->insert('staff', $staffData);

        if ($this->db->trans_complete() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la création du compte admin.</div>');
            redirect('admin/comptes/index');
        }

        // ==============================
        // 4. Envoi de l'email
        // ==============================
        $this->load->library('email');
        // Configuration (à mettre dans config/email.php plus tard)
        $config['protocol']  = 'smtp';
        $config['smtp_host'] = 'smtp.votredomaine.com';
        $config['smtp_user'] = 'no-reply@votredomaine.com';
        $config['smtp_pass'] = 'password';
        $config['smtp_port'] = 587;
        $config['mailtype']  = 'html';
        $config['charset']   = 'utf-8';
        $this->email->initialize($config);

        $this->email->from('no-reply@votredomaine.com', 'Votre ERP');
        $this->email->to($this->input->post('admin_email'));
        $this->email->subject('Bienvenue - Vos accès ERP');

        $message = "
    <html>
    <body>
        <h2>Bienvenue sur l'ERP</h2>
        <p>Votre compte a été créé pour l'entreprise <strong>{$this->input->post('nom')}</strong>.</p>
        <p>Voici vos identifiants :</p>
        <ul>
            <li>Email : {$this->input->post('admin_email')}</li>
            <li>Mot de passe : <strong>{$plainPassword}</strong></li>
        </ul>
        <p>Connectez-vous sur : <a href='".base_url()."'>".base_url()."</a></p>
        <p>Nous vous recommandons de changer votre mot de passe dès la première connexion.</p>
    </body>
    </html>
    ";
        $this->email->message($message);

        if ($this->email->send()) {
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Compte créé avec succès ! Un email contenant les identifiants a été envoyé.</div>');
        } else {
            $this->session->set_flashdata('msg', '<div class="alert alert-warning">Compte créé, mais l\'email n\'a pas pu être envoyé. Vérifiez votre configuration SMTP.</div>');
        }

        redirect('admin/comptes/index');
    }

    /**
     * Envoie un email avec les identifiants de connexion
     * @param string $to           Email du destinataire
     * @param string $companyName  Nom de l'entreprise
     * @param string $login        Nom d'utilisateur
     * @param string $plainPassword Mot de passe en clair
     * @return bool                True si l'envoi a réussi
     */
    private function sendCredentialsEmail($to, $companyName, $login, $plainPassword)
    {
        // 1. Charger la librairie email
        $this->load->library('email');

        // 2. Configuration SMTP (adaptez à vos paramètres)
        $config['protocol']  = 'smtp';
        $config['smtp_host'] = 'mail.diagomap.com';  // ex: smtp.gmail.com
        $config['smtp_user'] = 'info@diagomap.com';
        $config['smtp_pass'] = 'dX4$wRyExMTzp94';
        $config['smtp_port'] = 587;   // ou 465 pour SSL
        $config['smtp_crypto'] = 'tls'; // ou 'ssl'
        $config['mailtype']  = 'html';
        $config['charset']   = 'utf-8';
        $config['newline']   = "\r\n";

        $this->email->initialize($config);

        // 3. Expéditeur et destinataire
        $this->email->from('no-reply@votre-erp.com', 'Votre ERP SaaS');
        $this->email->to($to);

        // 4. Sujet
        $this->email->subject('Bienvenue - Vos accès à l\'ERP');

        // 5. Corps du message en HTML
        $message = "
    <html>
    <head><meta charset='UTF-8'></head>
    <body style='font-family: Arial, sans-serif; padding:20px;'>
        <h2>Bienvenue sur notre ERP, <strong>{$companyName}</strong> !</h2>
        <p>Votre compte a été créé avec succès. Voici vos identifiants de connexion :</p>
        <ul style='list-style:none; padding:0;'>
            <li><strong>🔗 Lien de connexion :</strong> <a href='".base_url()."'>".base_url()."</a></li>
            <li><strong>👤 Identifiant :</strong> {$login}</li>
            <li><strong>🔑 Mot de passe provisoire :</strong> <span style='background:#f0f0f0;padding:4px 8px;border-radius:4px;'>{$plainPassword}</span></li>
        </ul>
        <p style='color:red;'><strong>⚠️ Important :</strong> Pour des raisons de sécurité, nous vous recommandons de <strong>changer votre mot de passe</strong> dès votre première connexion.</p>
        <p>L'équipe support reste à votre disposition.</p>
        <hr>
        <p style='font-size:12px;color:#888;'>Cet email a été généré automatiquement, merci de ne pas y répondre.</p>
    </body>
    </html>
    ";

        $this->email->message($message);

        // 6. Envoi et gestion du résultat
        if ($this->email->send()) {
            log_message('info', 'Email de bienvenue envoyé à ' . $to);
            return true;
        } else {
            log_message('error', 'Échec envoi email pour ' . $to . ' : ' . $this->email->print_debugger());
            return false;
        }
    }

    /**
     * Génère un mot de passe aléatoire sécurisé
     * @param int $length Longueur souhaitée
     * @return string
     */
    private function generateRandomPassword($length = 12)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        return substr(str_shuffle($chars), 0, $length);
    }

    public function create_old()
    {
        if (!$this->rbac->hasPrivilege('comptes', 'can_add')) {
            access_denied();
        }
        $data['title']        = 'Add Expense Head';

        $this->form_validation->set_rules('expensehead', $this->lang->line('expense_head'), 'trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/comptes/comptes_create', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'nom' => $this->input->post('nom'),
                'email' => $this->input->post('email'),
                'telephone' => $this->input->post('telephone'),
                'adresse' => $this->input->post('adresse'),
                'forfait' => $this->input->post('forfait'),
                'slug' => $this->input->post('slug'),
                'date_debut' => $this->input->post('date_debut'),
                'date_expiration' => $this->input->post('date_expiration'),
                'created_at' => date('Y-m-d H:i:s'),
                'ncc' => $this->input->post('ncc'),
                'rccm' => $this->input->post('rccm'),
                'contact_nom' => $this->input->post('contact_nom'),
                'limite_utilisateurs' => $this->input->post('limite_utilisateurs'),
                'fne_api_key' => $this->input->post('fne_api_key'),
                'fne_point_vente' => $this->input->post('fne_point_vente'),
                'fne_establishment' => $this->input->post('fne_establishment'),
                'admin_username' => $this->input->post('admin_username'),
                'admin_email' => $this->input->post('admin_email'),
                'admin_password' => $this->input->post('admin_password'),
                'statut' => $this->input->post('statut'),
            );
            if (!empty($_FILES['logo']['name'])) {

                $config['upload_path']   = './uploads/front_office/logo_entreprises/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['max_size']      = 2048;
                $config['file_name']     = time() . '_' . $_FILES['logo']['name'];

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('logo')) {
                    $uploadData = $this->upload->data();
                    $data['logo'] = $uploadData['file_name'];
                } else {
                    $data['logo'] = "";
                }
            }


            $this->comptes_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/comptes/index');
        }
    }


    public function handle_upload()
    {
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $allowedExts = array('jpg', 'jpeg', 'png');
            $temp        = explode(".", $_FILES["file"]["name"]);
            $extension   = end($temp);
            if ($_FILES["file"]["error"] > 0) {
                $error .= "Error opening the file<br />";
            }
            if ($_FILES["file"]["type"] != 'image/gif' &&
                $_FILES["file"]["type"] != 'image/jpeg' &&
                $_FILES["file"]["type"] != 'image/png') {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }
            if (!in_array($extension, $allowedExts)) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }
            if ($_FILES["file"]["size"] > 102400) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . " 1MB");
                return false;
            }
            return true;
        } else {
            $this->form_validation->set_message('handle_upload', $this->lang->line('logo_file_is_required'));
            return false;
        }
    }

    public function ajax_view($id)
    {
        if (!$this->rbac->hasPrivilege('comptes', 'can_view')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            return;
        }

        // Charger le modèle si pas déjà fait
        if (!isset($this->comptes_model)) {
            $this->load->model('comptes_model');
        }

        $company = $this->comptes_model->get($id);

        if ($company) {
            // Calcul des jours restants
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

        // Charger le modèle si pas déjà fait
        if (!isset($this->comptes_model)) {
            $this->load->model('comptes_model');
        }

        $company = $this->comptes_model->get($id);

        if ($company) {
            $data['company'] = $company;
            $data['id'] = $id;
            $html = $this->load->view('admin/comptes/partials/company_edit_form', $data, true);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'html' => $html]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Entreprise non trouvée']);
        }
    }

    public function edite($id)
    {
        // Vérification des privilèges
        if (!$this->rbac->hasPrivilege('comptes', 'can_edit')) {
            access_denied();
        }

        if ($this->input->post()) {
            // Récupération des données du formulaire
            $updateData = array(
                'id'                  => $id,
                'nom'                 => $this->input->post('nom'),
                'slug'                => $this->input->post('slug'),
                'ncc'                 => $this->input->post('ncc'),
                'rccm'                => $this->input->post('rccm'),
                'contact_nom'         => $this->input->post('contact_nom'),
                'email'               => $this->input->post('email'),
                'telephone'           => $this->input->post('telephone'),
                'adresse'             => $this->input->post('adresse'),
                'forfait'             => $this->input->post('forfait'),
                'limite_utilisateurs' => $this->input->post('limite_utilisateurs'),
                'date_debut'          => $this->input->post('date_debut'),
                'date_expiration'     => $this->input->post('date_expiration'),
                'statut'              => $this->input->post('statut'),
                'fne_api_key'         => $this->input->post('fne_api_key'),
                'fne_point_vente'     => $this->input->post('fne_point_vente'),
                'fne_establishment'   => $this->input->post('fne_establishment'),
                'admin_username'      => $this->input->post('admin_username'),
                'admin_email'         => $this->input->post('admin_email'),
            );

            // Gestion du logo
            if (!empty($_FILES['logo']['name'])) {
                $config['upload_path'] = './uploads/logos/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['max_size'] = 2048;
                $config['file_name'] = 'logo_' . $id . '_' . time();

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('logo')) {
                    $upload_data = $this->upload->data();
                    $updateData['logo'] = $upload_data['file_name'];
                }
            }

            // Gestion du mot de passe admin (seulement si fourni)
            if ($this->input->post('admin_password')) {
                $updateData['admin_password'] = password_hash($this->input->post('admin_password'), PASSWORD_DEFAULT);
            }

            // Mise à jour via le modèle
            $this->comptes_model->update($id, $updateData);

            // Réponse JSON pour AJAX
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => true, 'message' => 'Entreprise modifiée avec succès']);
                return;
            } else {
                $this->session->set_flashdata('msg', '<div class="alert alert-success">Entreprise modifiée avec succès</div>');
                redirect('admin/comptes/index');
            }
        }
    }



    public function edit($id)
    {
        // Vérification des privilèges
        if (!$this->rbac->hasPrivilege('comptes', 'can_edit')) {
            access_denied();
        }

        // Titre
        $data['title'] = 'Modifier un Compte';

        // Récupération du compte existant
        $data['compte'] = $this->comptes_model->get($id);

        if (empty($data['compte'])) {
            show_404();
        }

        // Validation
        $this->form_validation->set_rules('nom', 'Nom', 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|xss_clean');
        $this->form_validation->set_rules('telephone', 'Téléphone', 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

            // Affichage du formulaire
            $this->load->view('layout/header', $data);
            $this->load->view('admin/comptes/comptesEdit', $data);
            $this->load->view('layout/footer');

        } else {

            // Préparation des données mises à jour
            $updateData = [
                'id'              => $id, // IMPORTANT pour que add() fasse UPDATE
                'nom'             => $this->input->post('nom'),
                'email'           => $this->input->post('email'),
                'telephone'       => $this->input->post('telephone'),
                'adresse'         => $this->input->post('adresse'),
                'ncc'             => $this->input->post('ncc'),
                'date_debut'      => $this->input->post('date_debut'),
                'date_expiration' => $this->input->post('date_expiration'),
                'forfait'         => $this->input->post('forfait'),
                'rccm'             => $this->input->post('rccm'),
                'contact_nom'             => $this->input->post('contact_nom'),
                'limite_utilisateurs'             => $this->input->post('limite_utilisateurs'),
                'limite_stock'             => $this->input->post('limite_stock'),
                'limite_factures_mensuelles'             => $this->input->post('limite_factures_mensuelles'),
                'fne_api_key'             => $this->input->post('fne_api_key'),
                'fne_point_vente'             => $this->input->post('fne_point_vente'),
                'fne_establishment'             => $this->input->post('fne_establishment'),
                'slug'             => $this->input->post('slug'),
                'admin_username'             => $this->input->post('admin_username'),
                'admin_email'             => $this->input->post('admin_email'),
                'admin_password'             => $this->input->post('admin_password'),
                'nombre_utilisateurs_actuels'             => $this->input->post('nombre_utilisateurs_actuels'),
                'nombre_factures_mois'             => $this->input->post('nombre_factures_mois'),
                'derniere_connexion'             => $this->input->post('derniere_connexion'),

                'statut'          => $this->input->post('statut'),

            ];

            /** 🔹 Upload du logo si un fichier est envoyé */
            if (!empty($_FILES['logo']['name'])) {

                $config['upload_path']   = './uploads/logos/';
                $config['allowed_types'] = 'jpg|jpeg|png';
                $config['max_size']      = 2048;

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('logo')) {
                    $fileData = $this->upload->data();
                    $updateData['logo'] = $fileData['file_name'];
                }
            }

            // Mise à jour via add() (car ton add() gère update aussi)
            $this->comptes_model->add($updateData);

            // Message de succès
            $this->session->set_flashdata(
                'msg',
                '<div class="alert alert-success">Compte mis à jour avec succès.</div>'
            );

            // Redirection
            redirect('admin/comptes/index');
        }
    }






    public function edit_old($id)
    {
        if (!$this->rbac->hasPrivilege('souscription', 'can_edit')) {
            access_denied();
        }
        $data['title']        = 'Edit Expense Head';
        $comptes_result      = $this->comptes_model->get();
        $data['compteslist'] = $comptes_result;
        $data['id']           = $id;
        $comptes             = $this->comptes_model->get($id);
        $data['comptesLis']  = $comptes;
        $this->form_validation->set_rules('expensehead', $this->lang->line('expense_head'), 'trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/comptes/comptesEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id'           => $id,
                'nom' => $this->input->post('nom'),
                'email'  => $this->input->post('email'),
                'telephone' => $this->input->post('telephone'),
                'adresse' => $this->input->post('adresse'),
                'logo' => $this->input->post('logo'),
                'date_debut'  => $this->input->post('date_debut'),
                'date_expiration'  => $this->input->post('date_expiration'),
                'forfait'  => $this->input->post('forfait'),
                'statut' => $this->input->post('statut'),

            );
            $this->comptes_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/comptes/index');
        }
    }

    /**
     * Déconnecter une entreprise
     */
    public function deconnecter_entreprise($entreprise_id) {
        if (!$this->rbac->hasPrivilege('comptes', 'can_edit')) {
            access_denied();
        }

        $this->load->model('comptes_model');

        if ($this->comptes_model->forcer_deconnexion_entreprise($entreprise_id)) {
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Entreprise déconnectée avec succès.</div>');
        } else {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la déconnexion.</div>');
        }

        redirect('admin/comptes/dashboard');
    }

    /**
     * API pour déconnexion via AJAX
     */
    public function ajax_deconnecter_entreprise() {
        if (!$this->rbac->hasPrivilege('comptes', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Accès refusé']);
            return;
        }

        $entreprise_id = $this->input->post('entreprise_id');
        $this->load->model('comptes_model');

        if ($this->comptes_model->forcer_deconnexion_entreprise($entreprise_id)) {
            echo json_encode(['success' => true, 'message' => 'Entreprise déconnectée avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la déconnexion']);
        }
    }

}
