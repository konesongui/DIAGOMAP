<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Site extends Public_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->check_installation();
        if ($this->config->item('installed') == true) {
            $this->db->reconnect();
        }

        $this->load->model("staff_model");
        $this->load->model("demorequest_model");
        $this->load->library('Auth');
        $this->load->library('Enc_lib');
        $this->load->library('customlib');
        $this->load->library('captchalib');
        $this->load->library('mailsmsconf');
        $this->load->library('mailer');
        $this->load->config('ci-blog');
        $this->mailer;
        $this->sch_setting = $this->setting_model->getSetting();
    }

    public function vitrine()
    {
        $school = $this->setting_model->get();

        $data = array(
            'title' => 'Site vitrine',
            'school' => !empty($school) ? $school[0] : array(),
        );

        $this->load->view('front/site_vitrine', $data);
    }

    public function submit_demo_request()
    {
        if (strtoupper($this->input->server('REQUEST_METHOD')) !== 'POST') {
            redirect('site');
            return;
        }

        $this->form_validation->set_rules('full_name', 'Nom complet', 'trim|required|min_length[3]|max_length[150]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[150]');
        $this->form_validation->set_rules('phone', 'Telephone', 'trim|required|max_length[40]');
        $this->form_validation->set_rules('company', 'Organisation', 'trim|required|max_length[150]');
        $this->form_validation->set_rules('message', 'Besoin principal', 'trim|required|max_length[1000]');

        if (!$this->has_table('demo_requests')) {
            $db_name = isset($this->db->database) ? $this->db->database : 'inconnue';
            $this->session->set_flashdata('demo_error', 'La table demo_requests est absente dans la base active (' . $db_name . ').');
            redirect(site_url('site') . '#demo');
            return;
        }

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('demo_error', strip_tags(validation_errors(' ', ' ')));
            redirect(site_url('site') . '#demo');
            return;
        }

        $payload = array(
            'full_name'  => $this->input->post('full_name', true),
            'email'      => $this->input->post('email', true),
            'phone'      => $this->input->post('phone', true),
            'company'    => $this->input->post('company', true),
            'message'    => $this->input->post('message', true),
            'source_url' => current_url(),
            'ip_address' => $this->input->ip_address(),
            'status'     => 'nouvelle',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );

        $insert_id = $this->demorequest_model->addDemoRequest($payload);
        if ($insert_id) {
            $this->session->set_flashdata('demo_success', 'Votre demande de demo a ete envoyee avec succes.');
        } else {
            $this->session->set_flashdata('demo_error', 'Impossible d\'enregistrer votre demande pour le moment.');
        }

        redirect(site_url('site') . '#demo');
    }

    public function subscribe_newsletter()
    {
        if (strtoupper($this->input->server('REQUEST_METHOD')) !== 'POST') {
            redirect('site');
            return;
        }

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[150]');

        if (!$this->has_table('newsletter_subscribers')) {
            $db_name = isset($this->db->database) ? $this->db->database : 'inconnue';
            $this->session->set_flashdata('newsletter_error', 'La table newsletter_subscribers est absente dans la base active (' . $db_name . ').');
            redirect(site_url('site') . '#newsletter');
            return;
        }

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('newsletter_error', strip_tags(validation_errors(' ', ' ')));
            redirect(site_url('site') . '#newsletter');
            return;
        }

        $inserted = $this->demorequest_model->addNewsletterSubscription(array(
            'email'      => $this->input->post('email', true),
            'source_url' => current_url(),
            'ip_address' => $this->input->ip_address(),
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ));

        if ($inserted) {
            $this->session->set_flashdata('newsletter_success', 'Abonnement enregistre. Merci.');
        } else {
            $this->session->set_flashdata('newsletter_error', 'Cet email est deja abonne ou une erreur est survenue.');
        }

        redirect(site_url('site') . '#newsletter');
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

    private function check_installation()
    {
        if ($this->uri->segment(1) !== 'install') {
            $this->load->config('migration');
            if ($this->config->item('installed') == false && $this->config->item('migration_enabled') == false) {
                redirect(base_url() . 'install/start');
            } else {
                if (is_dir(APPPATH . 'controllers/install')) {
                    echo '<h3>Delete the install folder from application/controllers/install</h3>';
                    die;
                }
            }
        }
    }



    public function login()
    {

        $app_name = $this->setting_model->get();
        $app_name = $app_name[0]['name'];

        if ($this->auth->logged_in()) {
            $this->auth->is_logged_in(true);
        }

        $data          = array();
        $data['title'] = 'Login';
        $school        = $this->setting_model->get();

        $data['name'] = $app_name;

        $notice_content     = $this->config->item('ci_front_notice_content');
        $notices            = $this->cms_program_model->getByCategory($notice_content, array('start' => 0, 'limit' => 5));
        $data['notice']     = $notices;
        $data['school']     = $school[0];
        $is_captcha         = $this->captchalib->is_captcha('login');
        $data["is_captcha"] = $is_captcha;
        if ($this->captchalib->is_captcha('login')) {
            $this->form_validation->set_rules('captcha', $this->lang->line('captcha'), 'trim|required|callback_check_captcha');
        }
        $this->form_validation->set_rules('username', $this->lang->line('username'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $captcha =  $this->captchalib->generate_captcha();
            $data['captcha_image'] = isset($captcha['image'])?$captcha['image']:"";
            $data['name']          = $app_name;
            $this->load->view('admin/login', $data);
        } else {
            $login_post = array(
                'email'    => $this->input->post('username'),
                'password' => $this->input->post('password'),
            );
            $data['captcha_image'] = $this->captchalib->generate_captcha()['image'];
            $setting_result        = $this->setting_model->get();
            $result                = $this->staff_model->checkLogin($login_post);

            if (is_object($result) && isset($result->status) && $result->status === 'error') {
                $data['name']          = $app_name;
                $data['error_message'] = $result->message;
                $this->load->view('admin/login', $data);
                return;
            }

            $default_setting = (!empty($setting_result) && isset($setting_result[0])) ? $setting_result[0] : array();
            $tenant_setting = $default_setting;

            if (!empty($result)) {
                $tenant_entreprise_id = isset($result->entreprise_id) ? (int) $result->entreprise_id : 0;
                if ($tenant_entreprise_id > 0) {
                    $scoped_setting = $this->setting_model->getSettingByEntrepriseId($tenant_entreprise_id);
                    if (!empty($scoped_setting)) {
                        $tenant_setting = $scoped_setting;
                    }
                }
            }

            if (!empty($result->language_id)) {
                $lang_array = array('lang_id' => $result->language_id, 'language' => $result->language);
            } else {
                $lang_array = array(
                    'lang_id' => $tenant_setting['lang_id'] ?? 0,
                    'language' => $tenant_setting['language'] ?? 'English',
                );
            }

            if ($result) {
                if ($result->is_active) {
                    if ($result->surname != "") {
                        $logusername = $result->name . " " . $result->surname;
                    } else {
                        $logusername = $result->name;
                    }

                    // =========================================================
                    // CRÉATION DE LA SESSION AVEC ENTREPRISE_ID
                    // =========================================================
                    $session_data = array(
                        'id'              => $result->id,
                        'username'        => $logusername,
                        'email'           => $result->email,
                        'roles'           => $result->roles,
                        'date_format'     => $tenant_setting['date_format'] ?? 'd-m-Y',
                        'currency_symbol' => $tenant_setting['currency_symbol'] ?? '$',
                        'currency_place'  => $tenant_setting['currency_place'] ?? 'before_number',
                        'start_month'     => $tenant_setting['start_month'] ?? 'January',
                        'start_week'      => date("w", strtotime($tenant_setting['start_week'] ?? 'Monday')),
                        'school_name'     => $tenant_setting['name'] ?? '',
                        'timezone'        => $tenant_setting['timezone'] ?? 'UTC',
                        'sch_name'        => $tenant_setting['name'] ?? '',
                        'language'        => $lang_array,
                        'is_rtl'          => $tenant_setting['is_rtl'] ?? 'disabled',
                        'theme'           => $tenant_setting['theme'] ?? 'theme1',
                        'gender'          => $result->gender,
                        'entreprise_id'   => $result->entreprise_id ?? 0, // 👈 AJOUT
                        'type_structure'  => $result->type_structure ?? 'siege',
                        'parent_entreprise_id' => $result->parent_entreprise_id ?? 0,
                        'code_succursale' => $result->code_succursale ?? '',
                        'can_manage_succursales' => $result->can_manage_succursales ?? 0,
                    );

                    $this->session->set_userdata('admin', $session_data);
                    $this->session->set_userdata('entreprise_id', $result->entreprise_id ?? 0); // 👈 AJOUT

                    // 📧 ENVOI DE LA NOTIFICATION DE CONNEXION
                    $user_data = array(
                        'id' => $result->id,
                        'name' => $result->name,
                        'surname' => $result->surname ?? '',
                        'email' => $result->email,
                        'gender' => $result->gender ?? ''
                    );

                    $this->load->library('mailgateway');
                    $this->mailgateway->sendLoginNotification($user_data, 'staff');

                    $role = $this->customlib->getStaffRole();
                    $role_name = json_decode($role)->name;
                    $this->customlib->setUserLog($this->input->post('username'), $role_name);

                    if (isset($_SESSION['redirect_to'])) {
                        redirect($_SESSION['redirect_to']);
                    } else {
                        redirect('admin/admin/dashboard');
                    }
                }
            }
            else {
                $data['name']          = $app_name;
                $data['error_message'] = $this->lang->line('invalid_username_or_password');
                $this->load->view('admin/login', $data);
            }
        }
    }

    public function login_14072026()
    {

        $app_name = $this->setting_model->get();
        $app_name = $app_name[0]['name'];

        if ($this->auth->logged_in()) {
            $this->auth->is_logged_in(true);
        }

        $data          = array();
        $data['title'] = 'Login';
        $school        = $this->setting_model->get();

        $data['name'] = $app_name;

        $notice_content     = $this->config->item('ci_front_notice_content');
        $notices            = $this->cms_program_model->getByCategory($notice_content, array('start' => 0, 'limit' => 5));
        $data['notice']     = $notices;
        $data['school']     = $school[0];
        $is_captcha         = $this->captchalib->is_captcha('login');
        $data["is_captcha"] = $is_captcha;
        if ($this->captchalib->is_captcha('login')) {
            $this->form_validation->set_rules('captcha', $this->lang->line('captcha'), 'trim|required|callback_check_captcha');
        }
        $this->form_validation->set_rules('username', $this->lang->line('username'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $captcha =  $this->captchalib->generate_captcha();
            $data['captcha_image'] = isset($captcha['image'])?$captcha['image']:"";
            $data['name']          = $app_name;
            $this->load->view('admin/login', $data);
        } else {
            $login_post = array(
                'email'    => $this->input->post('username'),
                'password' => $this->input->post('password'),
            );
            $data['captcha_image'] = $this->captchalib->generate_captcha()['image'];
            $setting_result        = $this->setting_model->get();
            $result                = $this->staff_model->checkLogin($login_post);

            if (!empty($result->language_id)) {
                $lang_array = array('lang_id' => $result->language_id, 'language' => $result->language);
            } else {
                $lang_array = array('lang_id' => $setting_result[0]['lang_id'], 'language' => $setting_result[0]['language']);
            }

            if ($result) {
                if ($result->is_active) {
                    if ($result->surname != "") {
                        $logusername = $result->name . " " . $result->surname;
                    } else {
                        $logusername = $result->name;
                    }

                    $session_data = array(
                        'id'              => $result->id,
                        'username'        => $logusername,
                        'email'           => $result->email,
                        'roles'           => $result->roles,
                        'date_format'     => $setting_result[0]['date_format'],
                        'currency_symbol' => $setting_result[0]['currency_symbol'],
                        'currency_place'  => $setting_result[0]['currency_place'],
                        'start_month'     => $setting_result[0]['start_month'],
                        'start_week'      => date("w", strtotime($setting_result[0]['start_week'])),
                        'school_name'     => $setting_result[0]['name'],
                        'timezone'        => $setting_result[0]['timezone'],
                        'sch_name'        => $setting_result[0]['name'],
                        'language'        => $lang_array,
                        'is_rtl'          => $setting_result[0]['is_rtl'],
                        'theme'           => $setting_result[0]['theme'],
                        'gender'          => $result->gender,
                    );
                    $language_result1 = $this->language_model->get($lang_array['lang_id']);
                    if ($this->customlib->get_rtl_languages($language_result1['short_code'])) {
                        $session_data['is_rtl'] = 'enabled';
                    } else {
                        $session_data['is_rtl'] = 'disabled';
                    }

                    $this->session->set_userdata('admin', $session_data);

                    $role      = $this->customlib->getStaffRole();
                    $role_name = json_decode($role)->name;
                    $this->customlib->setUserLog($this->input->post('username'), $role_name);

                    if (isset($_SESSION['redirect_to'])) {
                        redirect($_SESSION['redirect_to']);
                    } else {
                        redirect('admin/admin/dashboard');
                    }

                } else {
                    $data['name']          = $app_name;
                    $data['error_message'] = $this->lang->line('your_account_is_disabled_please_contact_to_administrator');

                    $this->load->view('admin/login', $data);
                }
            } else {
                $data['name']          = $app_name;
                $data['error_message'] = $this->lang->line('invalid_username_or_password');
                $this->load->view('admin/login', $data);
            }
        }
    }

    public function logout()
    {
        $admin_session   = $this->session->userdata('admin');
        $student_session = $this->session->userdata('student');
        $this->auth->logout();
        if ($admin_session) {
            redirect('login');
        } else if ($student_session) {
            redirect('site/userlogin');
        } else {
            redirect('site/userlogin');
        }
    }

    public function forgotpassword()
    {

        $app_name     = $this->setting_model->get();
        $data['name'] = $app_name[0]['name'];
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|valid_email|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $this->load->view('admin/forgotpassword', $data);
        } else {
            $email = $this->input->post('email');

            $result = $this->staff_model->getByEmail($email);

            if ($result && $result->email != "") {
                if ($result->is_active == '1') {
                    $verification_code = $this->enc_lib->encrypt(uniqid(mt_rand()));
                    $update_record     = array('id' => $result->id, 'verification_code' => $verification_code);
                    $this->staff_model->add($update_record);
                    $name           = $result->name;
                    $resetPassLink  = site_url('admin/resetpassword') . "/" . $verification_code;
                    $sender_details = array('resetPassLink' => $resetPassLink, 'name' => $name, 'username' => $result->email, 'email' => $email);
                    $this->mailsmsconf->mailsms('forgot_password', $sender_details);
                    $this->session->set_flashdata('message', $this->lang->line('please_check_your_email_to_recover_your_password'));
                } else {
                    $this->session->set_flashdata('disable_message', $this->lang->line('your_account_is_disabled_please_contact_to_administrator'));
                }

                redirect('site/login', 'refresh');
            } else {

                $data = array(
                    'error_message' => $this->lang->line('incorrect') . " " . $this->lang->line('email'),
                );
            }
            $this->load->view('admin/forgotpassword', $data);
        }
    }

    //reset password - final step for forgotten password
    public function admin_resetpassword($verification_code = null)
    {
        $app_name     = $this->setting_model->get();
        $data['name'] = $app_name[0]['name'];
        if (!$verification_code) {
            show_404();
        }

        $user = $this->staff_model->getByVerificationCode($verification_code);

        if ($user) {
            //if the code is valid then display the password reset form
            $this->form_validation->set_rules('password', $this->lang->line('password'), 'required');
            $this->form_validation->set_rules('confirm_password', $this->lang->line('confirm_password'), 'required|matches[password]');
            if ($this->form_validation->run() == false) {

                $data['verification_code'] = $verification_code;
                //render
                $this->load->view('admin/admin_resetpassword', $data);
            } else {

                // finally change the password
                $password      = $this->input->post('password');
                $update_record = array(
                    'id'                => $user->id,
                    'password'          => $this->enc_lib->passHashEnc($password),
                    'verification_code' => "",
                );

                $change = $this->staff_model->update($update_record);
                if ($change) {
                    //if the password was successfully changed
                    $this->session->set_flashdata('message', $this->lang->line("password_reset_successfully"));
                    redirect('site/login', 'refresh');
                } else {
                    $this->session->set_flashdata('message', $this->lang->line("something_went_wrong"));
                    redirect('admin_resetpassword/' . $verification_code, 'refresh');
                }
            }
        } else {
            //if the code is invalid then send them back to the forgot password page
            $this->session->set_flashdata('message', $this->lang->line('invalid_link'));
            redirect("site/forgotpassword", 'refresh');
        }
    }

    //reset password - final step for forgotten password
    public function resetpassword($role = null, $verification_code = null)
    {
        $app_name     = $this->setting_model->get();
        $data['name'] = $app_name[0]['name'];
        if (!$role || !$verification_code) {
            show_404();
        }

        $user = $this->user_model->getUserByCodeUsertype($role, $verification_code);

        if ($user) {
            //if the code is valid then display the password reset form
            $this->form_validation->set_rules('password', $this->lang->line('password'), 'required');
            $this->form_validation->set_rules('confirm_password', $this->lang->line('confirm_password'), 'required|matches[password]');
            if ($this->form_validation->run() == false) {

                $data['role']              = $role;
                $data['verification_code'] = $verification_code;
                //render
                $this->load->view('resetpassword', $data);
            } else {

                // finally change the password

                $update_record = array(
                    'id'                => $user->user_tbl_id,
                    'password'          => $this->input->post('password'),
                    'verification_code' => "",
                );

                $change = $this->user_model->saveNewPass($update_record);
                if ($change) {
                    //if the password was successfully changed
                    $this->session->set_flashdata('message', $this->lang->line('password_reset_successfully'));
                    redirect('site/userlogin', 'refresh');
                } else {
                    $this->session->set_flashdata('message', $this->lang->line("something_went_wrong"));
                    redirect('user/resetpassword/' . $role . '/' . $verification_code, 'refresh');
                }
            }
        } else {
            //if the code is invalid then send them back to the forgot password page
            $this->session->set_flashdata('message', $this->lang->line('invalid_link'));
            redirect("site/ufpassword", 'refresh');
        }
    }

    public function ufpassword()
    {

        $app_name     = $this->setting_model->get();
        $data['name'] = $app_name[0]['name'];
        $this->form_validation->set_rules('username', $this->lang->line('email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('user[]', $this->lang->line('user_type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $this->load->view('ufpassword', $data);
        } else {
            $email    = $this->input->post('username');
            $usertype = $this->input->post('user[]');
            $result   = $this->user_model->forgotPassword($usertype[0], $email);
            if ($result && $result->email != "") {

                $verification_code = $this->enc_lib->encrypt(uniqid(mt_rand()));
                $update_record     = array('id' => $result->user_tbl_id, 'verification_code' => $verification_code);
                $this->user_model->updateVerCode($update_record);

                if ($usertype[0] == "student") {
                    $name     = $this->customlib->getFullName($result->firstname, $result->middlename, $result->lastname, $this->sch_setting->middlename, $this->sch_setting->lastname);
                    $username = $result->username;
                } else {
                    $name     = $result->guardian_name;
                    $username = $result->username;
                }

                $resetPassLink  = site_url('user/resetpassword') . '/' . $usertype[0] . "/" . $verification_code;
                $sender_details = array('resetPassLink' => $resetPassLink, 'name' => $name, 'username' => $username, 'email' => $email);
                $this->mailsmsconf->mailsms('forgot_password', $sender_details);
                $this->session->set_flashdata('message', $this->lang->line("please_check_your_email_to_recover_your_password"));
                redirect('site/userlogin', 'refresh');
            } else {
                $data = array(
                    'name'          => $app_name[0]['name'],
                    'error_message' => $this->lang->line('invalid_email_or_user_type'),
                );
            }

            $this->load->view('ufpassword', $data);
        }
    }

    public function userlogin()
    {
        if ($this->auth->user_logged_in()) {
            $this->auth->user_redirect();
        }
        $data               = array();
        $data['title']      = 'Login';
        $school             = $this->setting_model->get();
        $data['name']       = $school[0]['name'];
        $notice_content     = $this->config->item('ci_front_notice_content');
        $notices            = $this->cms_program_model->getByCategory($notice_content, array('start' => 0, 'limit' => 5));
        $data['notice']     = $notices;
        $data['school']     = $school[0];
        $is_captcha         = $this->captchalib->is_captcha('userlogin');
        $data["is_captcha"] = $is_captcha;
        if ($is_captcha) {
            $this->form_validation->set_rules('captcha', $this->lang->line('captcha'), 'trim|required|callback_check_captcha');
        }
        $this->form_validation->set_rules('username', $this->lang->line('username'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $data['captcha_image'] = $this->captchalib->generate_captcha()['image'];
            $this->load->view('userlogin', $data);
        } else {
            $login_post = array(
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password'),
            );
            $data['captcha_image'] = $this->captchalib->generate_captcha()['image'];
            $login_details         = $this->user_model->checkLogin($login_post);

            if (isset($login_details) && !empty($login_details)) {
                $user = $login_details[0];
                if ($user->is_active == "yes") {
                    if ($user->role == "student") {
                        $result = $this->user_model->read_user_information($user->id);
                    } else if ($user->role == "parent") {
                        $result = $this->user_model->checkLoginParent($login_post);
                    }

                    if ($result != false) {
                        $setting_result = $this->setting_model->get();
                        if ($result[0]->lang_id == 0) {
                            $language = array('lang_id' => $setting_result[0]['lang_id'], 'language' => $setting_result[0]['language']);
                        } else {
                            $language = array('lang_id' => $result[0]->lang_id, 'language' => $result[0]->language);
                        }

                        if ($result[0]->role == "parent") {
                            $username = $result[0]->guardian_name;
                            if ($result[0]->guardian_relation == "Father") {
                                $image = $result[0]->father_pic;
                            } else if ($result[0]->guardian_relation == "Mother") {
                                $image = $result[0]->mother_pic;
                            } else if ($result[0]->guardian_relation == "Other") {
                                $image = $result[0]->guardian_pic;
                            }
                        } elseif ($result[0]->role == "student") {
                            $image    = $result[0]->image;
                            $username = $this->customlib->getFullName($result[0]->firstname,$result[0]->middlename,$result[0]->lastname,$this->sch_setting->middlename,$this->sch_setting->lastname);
                            $defaultclass = $this->user_model->get_studentdefaultClass($result[0]->user_id);
                            $this->customlib->setUserLog($result[0]->username, $result[0]->role,$defaultclass['id']);
                        }

                        $session_data = array(
                            'id'              => $result[0]->id,
                            'login_username'  => $result[0]->username,
                            'student_id'      => $result[0]->user_id,
                            'role'            => $result[0]->role,
                            'username'        => $username,
                            'date_format'     => $setting_result[0]['date_format'],
                            'start_week'      => date("w", strtotime($setting_result[0]['start_week'])),
                            'currency_symbol' => $setting_result[0]['currency_symbol'],
                            'timezone'        => $setting_result[0]['timezone'],
                            'sch_name'        => $setting_result[0]['name'],
                            'language'        => $language,
                            'is_rtl'          => $setting_result[0]['is_rtl'],
                            'theme'           => $setting_result[0]['theme'],
                            'image'           =>  $image,
                            'gender'          => $result[0]->gender,
                        );
                        if($session_data['is_rtl'] == "disabled"){

                            $language_result1 = $this->language_model->get($language['lang_id']);
                            if ($this->customlib->get_rtl_languages($language_result1['short_code'])) {
                                $session_data['is_rtl'] = 'enabled';
                            } else {
                                $session_data['is_rtl'] = 'disabled';
                            }
                        }

                        $this->session->set_userdata('student', $session_data);

                        // ============================================================
                        // 📧 ENVOI DE LA NOTIFICATION DE CONNEXION PAR EMAIL
                        // ============================================================
                        $user_data = array(
                            'id' => $result[0]->id,
                            'name' => $result[0]->firstname ?? $result[0]->name ?? '',
                            'firstname' => $result[0]->firstname ?? '',
                            'middlename' => $result[0]->middlename ?? '',
                            'lastname' => $result[0]->lastname ?? '',
                            'guardian_name' => $result[0]->guardian_name ?? '',
                            'email' => $result[0]->email ?? $result[0]->guardian_email ?? '',
                            'gender' => $result[0]->gender ?? ''
                        );
                        $user_type = $result[0]->role; // 'student' ou 'parent'

                        // Charger la librairie mailgateway et envoyer
                        $this->load->library('mailgateway');
                        $this->mailgateway->sendLoginNotification($user_data, $user_type);
                        // ============================================================

                        if ($result[0]->role == "parent") {
                            $this->customlib->setUserLog($result[0]->username, $result[0]->role);
                        }
                        redirect('user/user/choose');
                    } else {
                        $data['error_message'] = 'Account Suspended';
                        $this->load->view('userlogin', $data);
                    }
                } else {
                    $data['error_message'] = $this->lang->line('your_account_is_disabled_please_contact_to_administrator');
                    $this->load->view('userlogin', $data);
                }
            } else {
                $data['error_message'] = $this->lang->line('invalid_username_or_password');
                $this->load->view('userlogin', $data);
            }
        }
    }

    public function userlogin_14072026()
    {
        if ($this->auth->user_logged_in()) {
            $this->auth->user_redirect();
        }
        $data               = array();
        $data['title']      = 'Login';
        $school             = $this->setting_model->get();
        $data['name']       = $school[0]['name'];
        $notice_content     = $this->config->item('ci_front_notice_content');
        $notices            = $this->cms_program_model->getByCategory($notice_content, array('start' => 0, 'limit' => 5));
        $data['notice']     = $notices;
        $data['school']     = $school[0];
        $is_captcha         = $this->captchalib->is_captcha('userlogin');
        $data["is_captcha"] = $is_captcha;
        if ($is_captcha) {
            $this->form_validation->set_rules('captcha', $this->lang->line('captcha'), 'trim|required|callback_check_captcha');
        }
        $this->form_validation->set_rules('username', $this->lang->line('username'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $data['captcha_image'] = $this->captchalib->generate_captcha()['image'];
            $this->load->view('userlogin', $data);
        } else {
            $login_post = array(
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password'),
            );
            $data['captcha_image'] = $this->captchalib->generate_captcha()['image'];
            $login_details         = $this->user_model->checkLogin($login_post);

            if (isset($login_details) && !empty($login_details)) {
                $user = $login_details[0];
                if ($user->is_active == "yes") {
                    if ($user->role == "student") {
                        $result = $this->user_model->read_user_information($user->id);
                    } else if ($user->role == "parent") {
                        $result = $this->user_model->checkLoginParent($login_post);
                    }

                    if ($result != false) {
                        $setting_result = $this->setting_model->get();
                        if ($result[0]->lang_id == 0) {
                            $language = array('lang_id' => $setting_result[0]['lang_id'], 'language' => $setting_result[0]['language']);
                        } else {
                            $language = array('lang_id' => $result[0]->lang_id, 'language' => $result[0]->language);
                        }

                        if ($result[0]->role == "parent") {
                            $username = $result[0]->guardian_name;
                            if ($result[0]->guardian_relation == "Father") {
                                $image = $result[0]->father_pic;
                            } else if ($result[0]->guardian_relation == "Mother") {
                                $image = $result[0]->mother_pic;
                            } else if ($result[0]->guardian_relation == "Other") {
                                $image = $result[0]->guardian_pic;
                            }
                        } elseif ($result[0]->role == "student") {
                            $image    = $result[0]->image;
							$username = $this->customlib->getFullName($result[0]->firstname,$result[0]->middlename,$result[0]->lastname,$this->sch_setting->middlename,$this->sch_setting->lastname);
                            $defaultclass = $this->user_model->get_studentdefaultClass($result[0]->user_id);
                            $this->customlib->setUserLog($result[0]->username, $result[0]->role,$defaultclass['id']);
                        }

                        $session_data = array(
                            'id'              => $result[0]->id,
                            'login_username'  => $result[0]->username,
                            'student_id'      => $result[0]->user_id,
                            'role'            => $result[0]->role,
                            'username'        => $username,
                            'date_format'     => $setting_result[0]['date_format'],
                            'start_week'      => date("w", strtotime($setting_result[0]['start_week'])),
                            'currency_symbol' => $setting_result[0]['currency_symbol'],
                            'timezone'        => $setting_result[0]['timezone'],
                            'sch_name'        => $setting_result[0]['name'],
                            'language'        => $language,
                            'is_rtl'          => $setting_result[0]['is_rtl'],
                            'theme'           => $setting_result[0]['theme'],
                            'image'           =>  $image,
                            'gender'          => $result[0]->gender,
                        );
                        if($session_data['is_rtl'] == "disabled"){

                        $language_result1 = $this->language_model->get($language['lang_id']);
                        if ($this->customlib->get_rtl_languages($language_result1['short_code'])) {
                            $session_data['is_rtl'] = 'enabled';
                        } else {
                            $session_data['is_rtl'] = 'disabled';
                        }
                        }

                        $this->session->set_userdata('student', $session_data);
                        if ($result[0]->role == "parent") {
                            $this->customlib->setUserLog($result[0]->username, $result[0]->role);
                        }
                        redirect('user/user/choose');
                    } else {
                        $data['error_message'] = 'Account Suspended';
                        $this->load->view('userlogin', $data);
                    }
                } else {
                    $data['error_message'] = $this->lang->line('your_account_is_disabled_please_contact_to_administrator');
                    $this->load->view('userlogin', $data);
                }
            } else {
                $data['error_message'] = $this->lang->line('invalid_username_or_password');
                $this->load->view('userlogin', $data);
            }
        }
    }

    public function savemulticlass()
    {

        $student_id = '';
        $this->form_validation->set_rules('student_id', $this->lang->line('student'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

            $msg = array(
                'student_id' => form_error('student_id'),
            );

            $array = array('status' => '0', 'error' => $msg, 'message' => '');
        } else {

            $data = array(
                'student_id' => date('Y-m-d', strtotime($this->input->post('student_id'))),
            );

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function check_captcha($captcha)
    {
        if ($captcha != $this->session->userdata('captchaCode')):
            $this->form_validation->set_message('check_captcha', $this->lang->line('incorrect_captcha'));
            return false;
        else:
            return true;
        endif;
    }

    public function refreshCaptcha()
    {
        $captcha = $this->captchalib->generate_captcha();
        echo $captcha['image'];
    }

    /**
     * Envoie une notification de connexion réussie par email
     * @param object $user_data Données de l'utilisateur connecté
     * @param string $user_type Type d'utilisateur (staff, student, parent)
     */
    private function _send_login_notification($user_data, $user_type = 'staff') {
        // Récupérer les paramètres de l'école
        $school_name = $this->setting_model->getCurrentSchoolName();
        $school_logo = base_url('uploads/school_content/admin_logo/' . $this->setting_model->getAdminlogo());

        // Date et heure de connexion
        $login_time = date('d/m/Y à H:i:s');
        $ip_address = $this->input->ip_address();
        $user_agent = $this->input->user_agent();
        $browser = $this->_getBrowser($user_agent);

        // Récupérer l'email et le nom
        $email = $user_data->email;
        $name = $user_data->name;
        if ($user_type == 'staff') {
            if (!empty($user_data->surname)) {
                $name = $user_data->name . ' ' . $user_data->surname;
            }
        } elseif ($user_type == 'student') {
            $name = $this->customlib->getFullName(
                $user_data->firstname,
                $user_data->middlename,
                $user_data->lastname,
                $this->sch_setting->middlename,
                $this->sch_setting->lastname
            );
        } elseif ($user_type == 'parent') {
            $name = $user_data->guardian_name;
        }

        // Construction du sujet
        $subject = "🔐 Connexion réussie à " . $school_name;

        // Construction du message HTML
        $message = "<html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background-color: #f4f7fc; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden; }
            .header { background: linear-gradient(135deg, #1e293b, #0f172a); padding: 25px 30px; text-align: center; }
            .header h1 { color: #FFD700; margin: 0; font-size: 24px; }
            .header img { max-height: 80px; margin-bottom: 10px; }
            .body { padding: 30px; }
            .body h2 { color: #1e293b; margin-top: 0; }
            .body p { color: #475569; line-height: 1.6; }
            .info-box { background: #f8fafc; border-left: 4px solid #3B82F6; padding: 15px 20px; border-radius: 8px; margin: 15px 0; }
            .info-box p { margin: 5px 0; }
            .info-box strong { color: #1e293b; }
            .badge { display: inline-block; background: #10B981; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
            .footer { background: #f1f5f9; padding: 15px 30px; text-align: center; color: #94a3b8; font-size: 12px; }
            .footer a { color: #3B82F6; text-decoration: none; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <img src='{$school_logo}' alt='Logo' style='max-height:80px;'>
                <h1>🔐 Connexion réussie</h1>
            </div>
            <div class='body'>
                <h2>Bonjour " . $name . " 👋</h2>
                <p>Vous vous êtes connecté avec succès à la plateforme <strong>{$school_name}</strong>.</p>
                
                <div class='info-box'>
                    <p><strong>📅 Date et heure :</strong> {$login_time}</p>
                    <p><strong>🖥️ Adresse IP :</strong> {$ip_address}</p>
                    <p><strong>🌐 Navigateur :</strong> {$browser}</p>
                    <p><strong>📧 Email :</strong> {$email}</p>
                    <p><strong>👤 Type d'utilisateur :</strong> " . ucfirst($user_type) . "</p>
                </div>
                
                <p style='color: #64748b; font-size: 14px;'>
                    <span class='badge'>✓ Sécurisé</span> 
                    Si vous n'êtes pas à l'origine de cette connexion, veuillez contacter immédiatement l'administrateur.
                </p>
                
                <p style='margin-top: 20px;'>
                    <strong>Besoin d'aide ?</strong> Contactez le support à 
                    <a href='mailto:" . $this->config->item('smtp_user') . "' style='color: #3B82F6;'>" . $this->config->item('smtp_user') . "</a>
                </p>
            </div>
            <div class='footer'>
                &copy; " . date('Y') . " {$school_name} - Tous droits réservés.
            </div>
        </div>
    </body>
    </html>";

        // Envoyer l'email
        $this->load->library('email');
        $this->email->from($this->config->item('smtp_user'), $school_name);
        $this->email->to($email);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->send();
    }

    /**
     * Récupère le navigateur à partir du User-Agent
     */
    private function _getBrowser($user_agent) {
        $browser = 'Inconnu';
        if (strpos($user_agent, 'Firefox') !== false) $browser = 'Mozilla Firefox';
        elseif (strpos($user_agent, 'Chrome') !== false) $browser = 'Google Chrome';
        elseif (strpos($user_agent, 'Safari') !== false) $browser = 'Apple Safari';
        elseif (strpos($user_agent, 'Edge') !== false) $browser = 'Microsoft Edge';
        elseif (strpos($user_agent, 'Opera') !== false) $browser = 'Opera';
        elseif (strpos($user_agent, 'MSIE') !== false) $browser = 'Internet Explorer';

        // Version du navigateur
        preg_match('/[0-9.]+/', $user_agent, $version);
        if (!empty($version)) {
            $browser .= ' (v' . $version[0] . ')';
        }

        return $browser;
    }

}
