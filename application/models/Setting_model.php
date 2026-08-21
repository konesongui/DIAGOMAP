<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Setting_model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    protected function getCurrentEntrepriseId() {
        $entreprise_id = 0;

        $admin_session = $this->session->userdata('admin');
        if (is_array($admin_session) && isset($admin_session['entreprise_id'])) {
            $entreprise_id = (int) $admin_session['entreprise_id'];
        }

        if ($entreprise_id <= 0) {
            $entreprise_id = (int) ($this->session->userdata('entreprise_id') ?? 0);
        }

        if ($entreprise_id <= 0 && is_array($admin_session) && !empty($admin_session['id'])) {
            $staff_row = $this->db->select('entreprise_id')->from('staff')->where('id', (int) $admin_session['id'])->limit(1)->get()->row_array();
            if (!empty($staff_row['entreprise_id'])) {
                $entreprise_id = (int) $staff_row['entreprise_id'];
                $this->session->set_userdata('entreprise_id', $entreprise_id);
                $admin_session['entreprise_id'] = $entreprise_id;
                $this->session->set_userdata('admin', $admin_session);
            }
        }

        return $entreprise_id;
    }

    private function ensureEntrepriseSettingExists($entreprise_id) {
        $entreprise_id = (int) $entreprise_id;
        if ($entreprise_id <= 0) {
            return;
        }

        $exists = $this->db->select('id')
            ->from('sch_settings')
            ->where('entreprise_id', $entreprise_id)
            ->limit(1)
            ->get()
            ->row_array();

        if (!empty($exists['id'])) {
            return;
        }

        $template = $this->db->from('sch_settings')
            ->where('entreprise_id', 1)
            ->order_by('id', 'ASC')
            ->limit(1)
            ->get()
            ->row_array();

        if (empty($template)) {
            $template = $this->db->from('sch_settings')
                ->order_by('id', 'ASC')
                ->limit(1)
                ->get()
                ->row_array();
        }

        if (!empty($template)) {
            unset($template['id']);
            $template['entreprise_id'] = $entreprise_id;
            $this->db->insert('sch_settings', $template);
        }
    }

    public function ensureAiSettingsColumns() {
        $columns = $this->db->query("SHOW COLUMNS FROM sch_settings")->result_array();
        $existing = [];

        foreach ($columns as $column) {
            $existing[] = strtolower($column['Field']);
        }

        $definitions = [
            'ai_enabled' => "TINYINT(1) NOT NULL DEFAULT 1",
            'ai_api_key' => "VARCHAR(500) NULL DEFAULT NULL",
            'ai_model' => "VARCHAR(100) NULL DEFAULT 'gpt-4'",
            'ai_api_url' => "VARCHAR(255) NULL DEFAULT 'https://api.openai.com/v1/chat/completions'",
            'ai_system_prompt' => "TEXT NULL DEFAULT NULL",
        ];

        foreach ($definitions as $field => $definition) {
            if (in_array(strtolower($field), $existing, true)) {
                continue;
            }

            $this->db->query('ALTER TABLE sch_settings ADD COLUMN `' . $field . '` ' . $definition);
        }
    }

    public function ensureReminderSettingsColumns() {
        $columns = $this->db->query("SHOW COLUMNS FROM sch_settings")->result_array();
        $existing = [];

        foreach ($columns as $column) {
            $existing[] = strtolower($column['Field']);
        }

        $definitions = [
            'reminder_enabled' => "TINYINT(1) NOT NULL DEFAULT 0",
            'reminder_before_days' => "INT(11) NOT NULL DEFAULT 0",
            'reminder_on_due_date' => "TINYINT(1) NOT NULL DEFAULT 0",
            'reminder_after_days_1' => "INT(11) NOT NULL DEFAULT 0",
            'reminder_after_days_2' => "INT(11) NOT NULL DEFAULT 0",
            'reminder_after_days_3' => "INT(11) NOT NULL DEFAULT 0",
            'reminder_sender_email' => "VARCHAR(255) NULL DEFAULT NULL",
            'reminder_sender_name' => "VARCHAR(255) NULL DEFAULT NULL",
        ];

        foreach ($definitions as $field => $definition) {
            if (in_array(strtolower($field), $existing, true)) {
                continue;
            }

            $this->db->query('ALTER TABLE sch_settings ADD COLUMN `' . $field . '` ' . $definition);
        }
    }

    // Ensure columns required for automatic backups exist
    public function ensureBackupSettingsColumns() {
        $columns = $this->db->query("SHOW COLUMNS FROM sch_settings")->result_array();
        $existing = [];

        foreach ($columns as $column) {
            $existing[] = strtolower($column['Field']);
        }

        $definitions = [
            'auto_backup'      => "TINYINT(1) NOT NULL DEFAULT 0",
            'backup_time'      => "TIME NULL DEFAULT NULL",
            'backup_frequency' => "VARCHAR(20) NULL DEFAULT 'daily'",
            'backup_weekday'   => "VARCHAR(10) NULL DEFAULT NULL",
        ];

        foreach ($definitions as $field => $definition) {
            if (in_array(strtolower($field), $existing, true)) {
                continue;
            }

            $this->db->query('ALTER TABLE sch_settings ADD COLUMN `' . $field . '` ' . $definition);
        }
    }

    public function set($key, $value, $entreprise_id = null) {
        if (empty($key)) {
            return false;
        }

        $this->ensureAiSettingsColumns();
        $this->ensureReminderSettingsColumns();

        $entreprise_id = $entreprise_id !== null ? (int) $entreprise_id : $this->getCurrentEntrepriseId();

        if ($entreprise_id > 0) {
            $this->ensureEntrepriseSettingExists($entreprise_id);
            $this->db->where('entreprise_id', $entreprise_id);
        } else {
            $this->db->order_by('id', 'ASC')->limit(1);
        }

        $setting = $this->db->select('id')->from('sch_settings')->get()->row_array();
        if (!empty($setting['id'])) {
            return (bool) $this->db->where('id', $setting['id'])->update('sch_settings', [$key => $value]);
        }

        $insert_data = [$key => $value];
        if ($entreprise_id > 0) {
            $insert_data['entreprise_id'] = $entreprise_id;
        }
        $this->db->insert('sch_settings', $insert_data);

        return $this->db->insert_id();
    }

    public function getMysqlVersion() {
        $mysqlVersion = $this->db->query('SELECT VERSION() as version')->row();
        return $mysqlVersion;
    }

    public function getSqlMode() {

        $sqlMode = $this->db->query('SELECT @@sql_mode as mode')->row();
        return $sqlMode;
    }

    public function get($id = null) {

        $entreprise_id = $this->getCurrentEntrepriseId();
        $this->ensureAiSettingsColumns();

        if ($entreprise_id > 0) {
           $this->ensureEntrepriseSettingExists($entreprise_id);
        }

        $this->db->select('sch_settings.id,sch_settings.lang_id,sch_settings.languages,sch_settings.class_teacher,sch_settings.is_rtl,sch_settings.cron_secret_key, sch_settings.timezone,
          sch_settings.name,sch_settings.email,sch_settings.biometric,sch_settings.biometric_device,sch_settings.time_format,sch_settings.phone,languages.language,sch_settings.attendence_type,
          sch_settings.address,sch_settings.dise_code,sch_settings.rccm,sch_settings.site_web,sch_settings.director,sch_settings.director_title,sch_settings.company_activity,company_supplier,sch_settings.bank,sch_settings.registre_commerce,sch_settings.compte_contribuable,sch_settings.forme_jurique,sch_settings.cnps_number,sch_settings.boite_postal,sch_settings.regime_imposition,sch_settings.centre_impot,sch_settings.date_format,sch_settings.currency,sch_settings.currency_symbol,sch_settings.currency_place,sch_settings.start_month,sch_settings.start_week,sch_settings.session_id,sch_settings.fee_due_days,sch_settings.image,sch_settings.theme,sessions.session,sch_settings.online_admission,sch_settings.is_duplicate_fees_invoice,sch_settings.is_student_house,sch_settings.is_blood_group,sch_settings.admin_logo,sch_settings.admin_small_logo,sch_settings.mobile_api_url,sch_settings.app_primary_color_code,sch_settings.app_secondary_color_code,sch_settings.app_logo,sch_settings.student_profile_edit,sch_settings.ai_enabled,sch_settings.ai_api_key,sch_settings.ai_model,sch_settings.ai_api_url,sch_settings.ai_system_prompt,sch_settings.reminder_enabled,sch_settings.reminder_before_days,sch_settings.reminder_on_due_date,sch_settings.reminder_after_days_1,sch_settings.reminder_after_days_2,sch_settings.reminder_after_days_3,sch_settings.reminder_sender_email,sch_settings.reminder_sender_name'
        );
        $this->db->from('sch_settings');
        $this->db->join('sessions', 'sessions.id = sch_settings.session_id');
        $this->db->join('languages', 'languages.id = sch_settings.lang_id');

        if ($entreprise_id > 0) {
           $this->db->where('sch_settings.entreprise_id', $entreprise_id);
        }

        if ($id != null) {
           $this->db->where('sch_settings.id', $id);
        } else {
           $this->db->order_by('sch_settings.id');
        }
        $query = $this->db->get();

        if ($id != null) {
           return $query->row_array();
        } else {
           $session_array = $this->session->has_userdata('session_array');
           $result = $query->result_array();

           if (empty($result)) {
               return [];
           }

           if (!isset($result[0]['session_id']) || !isset($result[0]['session'])) {
               return $result;
           }

           $result[0]['current_session'] = array(
               'session_id' => $result[0]['session_id'],
               'session' => $result[0]['session']
           );

           if ($session_array) {
               $session_array = $this->session->userdata('session_array');
               $result[0]['session_id'] = $session_array['session_id'];
               $result[0]['session'] = $session_array['session'];
           }

           return $result;
        }
    }

    public function get_studentlang($id) {
        $data = $this->db->select('users.lang_id')->from('users')->where('user_id', $id)->get()->row_array();
        return $data;
    }

    public function get_parentlang($id) {
        $data = $this->db->select('users.lang_id')->from('users')->where('id', $id)->get()->row_array();
        return $data;
    }

    public function get_stafflang($id) {
        $data = $this->db->select('staff.lang_id')->from('staff')->where('id', $id)->get()->row_array();
        return $data;
    }

    public function getSchoolDetail($id = null) {

        $entreprise_id = $this->getCurrentEntrepriseId();

        $this->db->select('sch_settings.id,sch_settings.lang_id,sch_settings.is_rtl,sch_settings.timezone,
          sch_settings.name,sch_settings.email,sch_settings.biometric,sch_settings.biometric_device,sch_settings.phone,languages.language,
          sch_settings.address,sch_settings.dise_code,sch_settings.bank,sch_settings.compt_bank,sch_settings.director,sch_settings.director_title,sch_settings.regime_imposition,sch_settings.centre_impot,sch_settings.rccm,sch_settings.site_web,sch_settings.company_activity,company_supplier,sch_settings.registre_commerce,sch_settings.compte_contribuable,sch_settings.forme_jurique,sch_settings.cnps_number,sch_settings.boite_postal,sch_settings.date_format,sch_settings.currency,sch_settings.currency_symbol,sch_settings.start_month,sch_settings.start_week,sch_settings.session_id,sch_settings.image,sch_settings.theme,sessions.session'
        );
        $this->db->from('sch_settings');
        $this->db->join('sessions', 'sessions.id = sch_settings.session_id');
        $this->db->join('languages', 'languages.id = sch_settings.lang_id');

        if ($entreprise_id > 0) {
            $this->db->where('sch_settings.entreprise_id', $entreprise_id);
        }

        if ($id != null) {
            $this->db->where('sch_settings.id', $id);
        }

        $this->db->order_by('sch_settings.id');
        $query = $this->db->get();
        return $query->row();
    }


    public function getSetting() {
        $entreprise_id = $this->getCurrentEntrepriseId();
        $this->ensureAiSettingsColumns();
        $this->ensureReminderSettingsColumns();
        // Ensure backup columns exist so they can be selected and returned to the UI
        $this->ensureBackupSettingsColumns();

        if ($entreprise_id > 0) {
           $this->ensureEntrepriseSettingExists($entreprise_id);
        }

        $this->db->select('sch_settings.id,sch_settings.attendence_type,sch_settings.lang_id,sch_settings.is_rtl,sch_settings.fee_due_days,sch_settings.class_teacher,sch_settings.cron_secret_key,sch_settings.timezone,
          sch_settings.name,sch_settings.email,sch_settings.biometric,sch_settings.biometric_device,sch_settings.phone,sch_settings.adm_prefix,sch_settings.adm_start_from,languages.language,sch_settings.adm_no_digit,sch_settings.adm_update_status,sch_settings.adm_auto_insert,sch_settings.staffid_prefix,sch_settings.staffid_start_from,sch_settings.staffid_auto_insert,sch_settings.staffid_no_digit,sch_settings.staffid_update_status,
          sch_settings.address,sch_settings.dise_code,sch_settings.date_format,sch_settings.bank,sch_settings.director,sch_settings.director_title,sch_settings.compt_bank,sch_settings.regime_imposition,sch_settings.centre_impot,sch_settings.rccm,sch_settings.site_web,sch_settings.company_activity,company_supplier,sch_settings.registre_commerce,sch_settings.compte_contribuable,sch_settings.forme_jurique,sch_settings.cnps_number,sch_settings.boite_postal,sch_settings.currency,sch_settings.currency_place,sch_settings.currency_symbol,sch_settings.start_month,sch_settings.start_week,sch_settings.session_id,sch_settings.image,sch_settings.theme,sessions.session,online_admission,sch_settings.is_duplicate_fees_invoice,sch_settings.is_student_house,sch_settings.is_blood_group,sch_settings.roll_no,sch_settings.lastname,sch_settings.middlename,sch_settings.category,sch_settings.cast,sch_settings.religion,sch_settings.mobile_no,sch_settings.student_email,sch_settings.admission_date,sch_settings.student_photo,sch_settings.student_height,sch_settings.student_weight,sch_settings.measurement_date,sch_settings.father_name,sch_settings.father_phone,sch_settings.father_occupation,sch_settings.father_pic,sch_settings.mother_name,sch_settings.mother_phone,sch_settings.mother_occupation,sch_settings.mother_pic,sch_settings.guardian_phone,sch_settings.guardian_name,sch_settings.guardian_relation,sch_settings.guardian_email,sch_settings.guardian_pic,sch_settings.guardian_occupation,sch_settings.guardian_address,sch_settings.current_address,sch_settings.permanent_address,sch_settings.route_list,sch_settings.hostel_id,sch_settings.bank_account_no,sch_settings.bank_name,sch_settings.ifsc_code,sch_settings.national_identification_no,sch_settings.local_identification_no,sch_settings.rte,sch_settings.previous_school_details,sch_settings.student_note,sch_settings.upload_documents,sch_settings.staff_designation,sch_settings.staff_department,sch_settings.staff_last_name,sch_settings.staff_father_name,sch_settings.staff_mother_name,sch_settings.staff_date_of_joining,sch_settings.staff_phone,sch_settings.staff_emergency_contact,sch_settings.staff_marital_status,sch_settings.staff_photo,sch_settings.staff_current_address,sch_settings.staff_permanent_address,sch_settings.staff_qualification,sch_settings.staff_work_experience,sch_settings.staff_note,sch_settings.staff_epf_no,sch_settings.staff_basic_salary,sch_settings.staff_contract_type,sch_settings.staff_work_shift,sch_settings.staff_work_location,sch_settings.staff_leaves,sch_settings.staff_account_details,sch_settings.staff_social_media,sch_settings.staff_upload_documents,sch_settings.admin_logo,sch_settings.admin_small_logo,sch_settings.mobile_api_url,sch_settings.app_primary_color_code,sch_settings.app_secondary_color_code,sch_settings.app_logo,languages.short_code as `language_code`,sch_settings.student_profile_edit,sch_settings.my_question,sch_settings.online_admission_payment,online_admission_amount,sch_settings.online_admission_instruction,sch_settings.online_admission_conditions,sch_settings.ai_enabled,sch_settings.ai_api_key,sch_settings.ai_model,sch_settings.ai_api_url,          sch_settings.ai_system_prompt,sch_settings.reminder_enabled,sch_settings.reminder_before_days,sch_settings.reminder_on_due_date,sch_settings.reminder_after_days_1,sch_settings.reminder_after_days_2,sch_settings.reminder_after_days_3,sch_settings.reminder_sender_email,sch_settings.reminder_sender_name, sch_settings.auto_backup, sch_settings.backup_time, sch_settings.backup_frequency, sch_settings.backup_weekday');

        $this->db->from('sch_settings');
        $this->db->join('sessions', 'sessions.id = sch_settings.session_id', 'left');
        $this->db->join('languages', 'languages.id = sch_settings.lang_id', 'left');

        // ===== AJOUT : Filtrer par entreprise_id =====
        if ($entreprise_id > 0) {
            $this->db->where('sch_settings.entreprise_id', $entreprise_id);
        }

        $this->db->order_by('sch_settings.id');
        $query = $this->db->get();

        return $query->row();
    }
    public function getSetting_170626() {

        $entreprise_id = $this->getCurrentEntrepriseId();

        if ($entreprise_id > 0) {
            $this->ensureEntrepriseSettingExists($entreprise_id);
        }

        $this->db->select('sch_settings.id,sch_settings.attendence_type,sch_settings.lang_id,sch_settings.is_rtl,sch_settings.fee_due_days,sch_settings.class_teacher,sch_settings.cron_secret_key,sch_settings.timezone,
          sch_settings.name,sch_settings.email,sch_settings.biometric,sch_settings.biometric_device,sch_settings.phone,sch_settings.adm_prefix,sch_settings.adm_start_from,languages.language,sch_settings.adm_no_digit,sch_settings.adm_update_status,sch_settings.adm_auto_insert,sch_settings.staffid_prefix,sch_settings.staffid_start_from,sch_settings.staffid_auto_insert,sch_settings.staffid_no_digit,sch_settings.staffid_update_status,
          sch_settings.address,sch_settings.dise_code,sch_settings.date_format,sch_settings.bank,sch_settings.director,sch_settings.director_title,sch_settings.compt_bank,sch_settings.regime_imposition,sch_settings.centre_impot,sch_settings.rccm,sch_settings.site_web,sch_settings.company_activity,company_supplier,sch_settings.registre_commerce,sch_settings.compte_contribuable,sch_settings.forme_jurique,sch_settings.cnps_number,sch_settings.boite_postal,sch_settings.currency,sch_settings.currency_place,sch_settings.currency_symbol,sch_settings.start_month,sch_settings.start_week,sch_settings.session_id,sch_settings.image,sch_settings.theme,sessions.session,online_admission,sch_settings.is_duplicate_fees_invoice,sch_settings.is_student_house,sch_settings.is_blood_group,sch_settings.roll_no,sch_settings.lastname,sch_settings.middlename,sch_settings.category,sch_settings.cast,sch_settings.religion,sch_settings.mobile_no,sch_settings.student_email,sch_settings.admission_date,sch_settings.student_photo,sch_settings.student_height,sch_settings.student_weight,sch_settings.measurement_date,sch_settings.father_name,sch_settings.father_phone,sch_settings.father_occupation,sch_settings.father_pic,sch_settings.mother_name,sch_settings.mother_phone,sch_settings.mother_occupation,sch_settings.mother_pic,sch_settings.guardian_phone,sch_settings.guardian_name,sch_settings.guardian_relation,sch_settings.guardian_email,sch_settings.guardian_pic,sch_settings.guardian_occupation,sch_settings.guardian_address,sch_settings.current_address,sch_settings.permanent_address,sch_settings.route_list,sch_settings.hostel_id,sch_settings.bank_account_no,sch_settings.bank_name,sch_settings.ifsc_code,sch_settings.national_identification_no,sch_settings.local_identification_no,sch_settings.rte,sch_settings.previous_school_details,sch_settings.student_note,sch_settings.upload_documents,sch_settings.staff_designation,sch_settings.staff_department,sch_settings.staff_last_name,sch_settings.staff_father_name,sch_settings.staff_mother_name,sch_settings.staff_date_of_joining,sch_settings.staff_phone,sch_settings.staff_emergency_contact,sch_settings.staff_marital_status,sch_settings.staff_photo,sch_settings.staff_current_address,sch_settings.staff_permanent_address,sch_settings.staff_qualification,sch_settings.staff_work_experience,sch_settings.staff_note,sch_settings.staff_epf_no,sch_settings.staff_basic_salary,sch_settings.staff_contract_type,sch_settings.staff_work_shift,sch_settings.staff_work_location,sch_settings.staff_leaves,sch_settings.staff_account_details,sch_settings.staff_social_media,sch_settings.staff_upload_documents,sch_settings.admin_logo,sch_settings.admin_small_logo,sch_settings.mobile_api_url,sch_settings.app_primary_color_code,sch_settings.app_secondary_color_code,sch_settings.app_logo,languages.short_code as `language_code`,sch_settings.student_profile_edit,sch_settings.my_question,sch_settings.online_admission_payment,online_admission_amount,sch_settings.online_admission_instruction,sch_settings.online_admission_conditions');

        $this->db->from('sch_settings');
        $this->db->join('sessions', 'sessions.id = sch_settings.session_id');
        $this->db->join('languages', 'languages.id = sch_settings.lang_id');

        if ($entreprise_id > 0) {
            $this->db->where('sch_settings.entreprise_id', $entreprise_id);
        }

        $this->db->order_by('sch_settings.id');
        $query = $this->db->get();
        return $query->row();
    }

    public function remove($id) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('sch_settings');
        $message = DELETE_RECORD_CONSTANT . " On settings id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }

    public function add($data)
    {
        // Ensure backup-related columns exist before saving settings
        $this->ensureBackupSettingsColumns();
        try {
            $this->db->trans_start();
            $this->db->trans_strict(false);

            //======================= RÉCUPÉRATION DE L'ENTREPRISE_ID ========================
            $entreprise_id = $this->getCurrentEntrepriseId();

            // Ajouter l'entreprise_id si elle n'existe pas déjà
            if ($entreprise_id > 0 && !isset($data['entreprise_id'])) {
                $data['entreprise_id'] = $entreprise_id;
            }

            //======================= Code Start ===========================
            if (isset($data['id'])) {
                $this->db->where('id', $data['id']);

                if ($entreprise_id > 0) {
                    $this->db->where('entreprise_id', $entreprise_id);
                }

                $this->db->update('sch_settings', $data);
                $message = UPDATE_RECORD_CONSTANT . " On settings id " . $data['id'];
                $action = "Update";
                $record_id = $insert_id = $data['id'];
                $this->log($message, $record_id, $action);
            } else {
                // Pour un insert, on ajoute l'entreprise_id
                $this->db->insert('sch_settings', $data);
                $insert_id = $this->db->insert_id();
                $message = INSERT_RECORD_CONSTANT . " On settings id " . $insert_id;
                $action = "Insert";
                $record_id = $insert_id;
                $this->log($message, $record_id, $action);
            }
            //====================== Code End ==============================

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return false;
            } else {
                return $insert_id ?? $record_id;
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Erreur dans Setting_model->add(): ' . $e->getMessage());
            return false;
        }
    }

    public function add_17($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('sch_settings', $data);
            $message = UPDATE_RECORD_CONSTANT . " On settings id " . $data['id'];
            $action = "Update";
            $record_id = $insert_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('sch_settings', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On settings id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);

            // return $insert_id;
        }
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $insert_id;
        }
    }

    public function getCurrentSession() {
        $session_result = $this->get();

        return $session_result[0]['session_id'];
    }

    public function getOnlineAdmissionStatus() {
        $setting_result = $this->get();

        if ($setting_result[0]['online_admission']) {
            return true;
        }
        return false;
    }

    public function getCurrentSessionName() {
        $session_result = $this->get();
        return $session_result[0]['session'];
    }

    public function getCurrentSchoolName_170726() {
        $session_result = $this->get();
        return $session_result[0]['name'];
    }

    public function getCurrentSchoolName() {
        // Récupérer l'entreprise_id de l'utilisateur connecté
        $userdata = $this->customlib->getUserData();
        $entreprise_id = $userdata['entreprise_id'] ?? 0;

        if ($entreprise_id == 0) {
            $entreprise_id = $this->session->userdata('entreprise_id') ?? 0;
        }

        // Si l'utilisateur a une entreprise, récupérer son nom
        if ($entreprise_id > 0) {
            $this->db->select('name');
            $this->db->from('sch_settings');
            $this->db->where('entreprise_id', $entreprise_id);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                return $query->row()->name;
            }
        }

        // Fallback : récupérer le premier enregistrement
        $this->db->select('name');
        $this->db->from('sch_settings');
        $this->db->order_by('id');
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->name;
        }

        return 'Mon Entreprise';
    }

    public function getStartMonth() {
        $session_result = $this->get();
        return $session_result[0]['start_month'];
    }

    public function getCurrentSessiondata() {
        $session_result = $this->get();
        return $session_result[0];
    }

    public function getCurrency() {
        $session_result = $this->get();
        return $session_result[0]['currency'];
    }

    public function getCurrencySymbol() {
        $session_result = $this->get();
        return $session_result[0]['currency_symbol'];
    }

    public function getDateYmd() {
        return date('Y-m-d');
    }

    public function getDateDmy() {
        return date('d-m-Y');
    }

    public function add_cronsecretkey($data, $id) {
        $entreprise_id = $this->getCurrentEntrepriseId();

        $this->db->where("id", $id);

        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }

        $this->db->update("sch_settings", $data);
    }

    public function getLanguage() {

        $query = $this->db->select('languages.language,languages.short_code')->where('id', $this->session->userdata['admin']['language']['lang_id'])->get('languages');
        return $query->row_array();
    }


    public function getuserLanguage() {

        $query = $this->db->select('languages.language,languages.short_code')->where('id', $this->session->userdata['student']['language']['lang_id'])->get('languages');
        return $query->row_array();
    }

    public function getAdminlogo() {
        $entreprise_id = $this->getCurrentEntrepriseId();
        $this->db->select('admin_logo')->from('sch_settings');

        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }

        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $logo = $query->row_array();
        $logo_name = isset($logo['admin_logo']) ? $logo['admin_logo'] : '';
        echo $logo_name;
        return $logo_name;
    }

    public function getAdminsmalllogo() {
        $entreprise_id = $this->getCurrentEntrepriseId();
        $this->db->select('admin_small_logo')->from('sch_settings');

        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }

        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $logo = $query->row_array();
        $small_logo_name = isset($logo['admin_small_logo']) ? $logo['admin_small_logo'] : '';
        echo $small_logo_name;
        return $small_logo_name;
    }

    public function get_appname() {
        $entreprise_id = $this->getCurrentEntrepriseId();
        $this->db->select('name')->from('sch_settings');

        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }

        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        $name = $query->row_array();
        echo isset($name['name']) ? $name['name'] : '';
    }

    public function getSettingIdByEntreprise($entreprise_id) {
        $entreprise_id = (int) $entreprise_id;
        if ($entreprise_id <= 0) {
            return null;
        }

        $row = $this->db->select('id')
            ->from('sch_settings')
            ->where('entreprise_id', $entreprise_id)
            ->order_by('id', 'ASC')
            ->limit(1)
            ->get()
            ->row_array();

        return isset($row['id']) ? (int) $row['id'] : null;
    }

    public function getSettingByEntrepriseId($entreprise_id) {
        $entreprise_id = (int) $entreprise_id;
        if ($entreprise_id <= 0) {
            return null;
        }

        $this->ensureEntrepriseSettingExists($entreprise_id);

        return $this->db->select('sch_settings.*, languages.language')
            ->from('sch_settings')
            ->join('languages', 'languages.id = sch_settings.lang_id', 'left')
            ->where('sch_settings.entreprise_id', $entreprise_id)
            ->order_by('sch_settings.id', 'ASC')
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function check_haederimage($type) {
        $check = $this->db->select('*')->from('print_headerfooter')->where('print_type', $type)->get()->row_array();


        if (empty($check['header_image'])) {
            return 0;
        } else {
            return 1;
        }
    }

    public function add_printheader($data) {

        $this->db->where('print_type', $data['print_type']);
        $this->db->update('print_headerfooter', $data);

    }

    public function get_printheader() {
        return $this->db->select('*')->from('print_headerfooter')->get()->result_array();
    }

    public function get_receiptheader() {
        $image = $this->db->select('header_image')->from('print_headerfooter')->where('print_type', 'student_receipt')->get()->row_array();
        echo $image['header_image'];
    }

    public function unlink_receiptheader() {
        $image = $this->db->select('header_image')->from('print_headerfooter')->where('print_type', 'student_receipt')->get()->row_array();
        return $image['header_image'];
    }

    public function get_receiptfooter() {
        $image = $this->db->select('footer_content')->from('print_headerfooter')->where('print_type', 'student_receipt')->get()->row_array();
        echo $image['footer_content'];
    }

    public function get_payslipheader() {
        $image = $this->db->select('header_image')->from('print_headerfooter')->where('print_type', 'staff_payslip')->get()->row_array();
        echo $image['header_image'];
    }

    public function unlink_payslipheader() {
        $image = $this->db->select('header_image')->from('print_headerfooter')->where('print_type', 'staff_payslip')->get()->row_array();
        return $image['header_image'];
    }

    public function get_payslipfooter() {
        $image = $this->db->select('footer_content')->from('print_headerfooter')->where('print_type', 'staff_payslip')->get()->row_array();
        echo $image['footer_content'];
    }

    public function unlink_onlinereceiptheader() {
        $image = $this->db->select('header_image')->from('print_headerfooter')->where('print_type', 'online_admission_receipt')->get()->row_array();
        return $image['header_image'];
    }

    public function get_onlineadmissionheader() {
        $image = $this->db->select('header_image')->from('print_headerfooter')->where('print_type', 'online_admission_receipt')->get()->row_array();
        echo $image['header_image'];
    }

    public function get_onlineadmissionfooter() {
        $image = $this->db->select('footer_content')->from('print_headerfooter')->where('print_type', 'online_admission_receipt')->get()->row_array();
        echo $image['footer_content'];
    }

    /**
     * Récupère l'email de l'administrateur principal
     * @return string Email de l'administrateur
     */
    public function getAdminEmail() {
        // Option 1 : Récupérer depuis la table settings
        $setting = $this->get();
        if (!empty($setting[0]['email'])) {
            return $setting[0]['email'];
        }

        // Option 2 : Récupérer le premier admin depuis la table staff
        $this->db->select('email');
        $this->db->from('staff');
        $this->db->join('staff_roles', 'staff_roles.staff_id = staff.id');
        $this->db->where('staff_roles.role_id', 1); // 1 = admin
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->row();

        if (!empty($result) && !empty($result->email)) {
            return $result->email;
        }

        // Option 3 : Email par défaut
        return 'admin@votre-ecole.com';
    }

}
