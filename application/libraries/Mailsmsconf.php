<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mailsmsconf {
  
    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->config->load("mailsms");
        $this->CI->load->library('smsgateway');
        $this->CI->load->library('mailgateway');
        $this->CI->load->model('examresult_model');
        $this->CI->load->model('student_model');
        $this->config_mailsms = $this->CI->config->item('mailsms');
        $this->sch_setting = $this->CI->setting_model->getSetting();
    }
 
    public function mailsms($send_for, $sender_details, $date = null, $exam_schedule_array = null) {
        
        // var_dump($sender_details);
        // exit;

        $send_for = $this->config_mailsms[$send_for];
        
        // var_dump($send_for);
        // exit;

        $chk_mail_sms = $this->CI->customlib->sendMailSMS($send_for);
        $sms_detail = $this->CI->smsconfig_model->getActiveSMS();

        // var_dump($chk_mail_sms);
        // var_dump($sms_detail);
        // exit;
       
       
        if (!empty($chk_mail_sms)) {
            if ($send_for == "student_admission") {
                if ($chk_mail_sms['mail'] && $chk_mail_sms['template'] != "") {
                    $this->CI->mailgateway->sentRegisterMail($sender_details['student_id'], $sender_details['email'], $chk_mail_sms['template'], $chk_mail_sms['subject']);
                }
                if ($chk_mail_sms['sms'] && $chk_mail_sms['template'] != "" && !empty($sms_detail)) {
                  
                    $this->CI->smsgateway->sentRegisterSMS($sender_details['student_id'], $sender_details['contact_no'], $chk_mail_sms['template'],$chk_mail_sms['template_id']);
                }
            } elseif ($send_for == "exam_result") {

                $this->sendResult($chk_mail_sms, $sender_details, $chk_mail_sms['template'], $chk_mail_sms['subject'],$chk_mail_sms['template_id']);
            } elseif ($send_for == "login_credential") {

                if ($chk_mail_sms['mail'] && $chk_mail_sms['template'] != "") {

                    $this->CI->mailgateway->sendLoginCredential($chk_mail_sms, $sender_details, $chk_mail_sms['template'] , $chk_mail_sms['subject']);
                }
                if ($chk_mail_sms['sms'] && $chk_mail_sms['template'] != "" && !empty($sms_detail)) {
                    $this->CI->smsgateway->sendLoginCredential($chk_mail_sms, $sender_details, $chk_mail_sms['template'],$chk_mail_sms['template_id']);
                }
            } elseif ($send_for == "fee_submission") {

                if ($chk_mail_sms['mail'] && $chk_mail_sms['template'] != "") {
                    $this->CI->mailgateway->sentAddFeeMail($sender_details, $chk_mail_sms['template'], $chk_mail_sms['subject']);
                }

                if ($chk_mail_sms['sms'] && $chk_mail_sms['template'] != "" && !empty($sms_detail)) {

                    $this->CI->smsgateway->sentAddFeeSMS($sender_details, $chk_mail_sms['template'],$chk_mail_sms['template_id']);
                }

                if ($chk_mail_sms['notification'] && $chk_mail_sms['template'] != "") {
                    $this->CI->smsgateway->sentAddFeeNotification($sender_details, $chk_mail_sms['template'], $chk_mail_sms['subject']);
                }
            } elseif ($send_for == "absent_attendence") {

                $this->sendAbsentAttendance($chk_mail_sms, $sender_details, $date, $chk_mail_sms['template'], $exam_schedule_array, $chk_mail_sms['subject'],$chk_mail_sms['template_id']);
            } elseif ($send_for == "fees_reminder") {

                if ($chk_mail_sms['mail'] && $chk_mail_sms['template'] != "") {
                    $this->CI->mailgateway->sentMail($sender_details, $chk_mail_sms['template'], $chk_mail_sms['subject']);
                }

                if ($chk_mail_sms['sms'] && $chk_mail_sms['template'] != "" && !empty($sms_detail)) {
                   
                    $this->CI->smsgateway->sendSMS($sender_details->guardian_phone, $sender_details,$chk_mail_sms['template_id'], $chk_mail_sms['template']);
                }

                if ($chk_mail_sms['notification'] && $chk_mail_sms['template'] != "") {
                    $this->CI->smsgateway->sentNotification($sender_details->parent_app_key, $chk_mail_sms['template'], $sender_details, $chk_mail_sms['subject'], $chk_mail_sms['template']);
                }
            } elseif ($send_for == "homework") {

                $this->sendHomework($chk_mail_sms, $sender_details, $chk_mail_sms['template'], $chk_mail_sms['subject'], $chk_mail_sms['template_id']);
            } elseif ($send_for == "online_examination_publish_exam") {

                $this->sendOnlineexam($chk_mail_sms, $sender_details, $chk_mail_sms['template'], $chk_mail_sms['subject'],$chk_mail_sms['template_id']);
            } elseif ($send_for == "online_examination_publish_result") {

                $this->sendOnlineexam($chk_mail_sms, $sender_details, $chk_mail_sms['template'], $chk_mail_sms['subject'], $chk_mail_sms['template_id']);
            }  elseif ($send_for == "forgot_password") {
                $school_name = $this->CI->setting_model->getCurrentSchoolName();
                $sender_details['school_name'] = $school_name;

                $msg = ($this->getForgotPasswordContent($sender_details, $chk_mail_sms['template']));


                if ($chk_mail_sms['mail'] && $chk_mail_sms['template'] != "") {
                    if (!empty($sender_details['email'])) {
                        $subject = $chk_mail_sms['subject'];
                        $this->CI->mailer->send_mail($sender_details['email'], $subject, $msg);
                    }
                }
            }  elseif ($send_for == "online_admission_form_submission") {

                $this->sendOnlineadmission($chk_mail_sms, $sender_details, $chk_mail_sms['template'], $chk_mail_sms['subject'], $chk_mail_sms['template_id']);
            }  elseif ($send_for == "online_admission_fees_submission") {

                $this->sendOnlineadmissionFees($chk_mail_sms, $sender_details, $chk_mail_sms['template'], $chk_mail_sms['subject'],$chk_mail_sms['template_id']);
            } elseif ($send_for == "send_quotes") {
                $this->CI->mailgateway->sendQuote($chk_mail_sms, $sender_details, $chk_mail_sms['template'] , $chk_mail_sms['subject']);
            }
            elseif ($send_for == "send_delivery") {
                $this->CI->mailgateway->sendDelivery($chk_mail_sms, $sender_details, $chk_mail_sms['template'] , $chk_mail_sms['subject']);
            } elseif ($send_for == "send_invoice") {
                $this->CI->mailgateway->sendInvoice($chk_mail_sms, $sender_details, $chk_mail_sms['template'] , $chk_mail_sms['subject']);
            }elseif ($send_for == "send_quote_no_stock") {
                $this->CI->mailgateway->sendQuoteNoStock($chk_mail_sms, $sender_details, $chk_mail_sms['template'] , $chk_mail_sms['subject']);
            }
        } elseif ($send_for == "send_payslip") {
            $this->CI->mailgateway->sendPayslip($chk_mail_sms, $sender_details, $chk_mail_sms['template'], $chk_mail_sms['subject']);
        }

        else {

            }
        }


    public function mailsmsalumnistudent($sender_details) {
        if ($sender_details['email_value'] == 'yes') {
            $this->CI->mailgateway->sentMailToAlumni($sender_details);
        }
        if ($sender_details['sms_value'] == 'yes') {
            $this->CI->smsgateway->sentSMSToAlumni($sender_details);
        }
    }

    public function sendResult($chk_mail_sms, $exam_result, $template, $subject, $template_id) {
        if ($chk_mail_sms['mail'] or $chk_mail_sms['sms'] or $chk_mail_sms['notification']) {
             $sms_detail = $this->CI->smsconfig_model->getActiveSMS(); 
            if (!empty($exam_result['exam_result'])) {
                foreach ($exam_result['exam_result'] as $res_key => $res_value) {

                    $detail = array(
                        'student_name' => $this->CI->customlib->getFullName($res_value->firstname,$res_value->middlename,$res_value->lastname,$this->sch_setting->middlename,$this->sch_setting->lastname),
                        'exam_roll_no' => $res_value->exam_roll_no,
                        'email' => $res_value->email,
                        'exam' => $exam_result['exam']->exam,
                        'guardian_phone' => $res_value->guardian_phone,
                        'guardian_email' => $res_value->guardian_email,
                        'app_key' => $res_value->app_key,
                        'parent_app_key' => $res_value->parent_app_key,
                    );

                    if ($chk_mail_sms['mail'] && $detail['guardian_email'] != "") {

                        $this->CI->mailgateway->sentExamResultMail($detail, $template, $subject);
                    }
                    if ($chk_mail_sms['mail'] && $detail['email'] != "") {

                        $this->CI->mailgateway->sentExamResultMailStudent($detail, $template, $subject);
                    }
                    if ($chk_mail_sms['sms'] && $detail['guardian_phone'] != ""  && !empty($sms_detail)) {
                        $this->CI->smsgateway->sentExamResultSMS($detail, $template, $template_id);
                    }
                    if ($chk_mail_sms['notification'] && ($detail['parent_app_key'] != "" || $detail['app_key'] != "")) {
                        $this->CI->smsgateway->sentExamResultNotification($detail, $template, $subject);
                    }
                }
            }
        }
    }

    public function sendAbsentAttendance($chk_mail_sms, $student_session_array, $date, $template, $subject_attendence, $subject,$template_id) {

        if ($chk_mail_sms['mail'] or $chk_mail_sms['sms'] or $chk_mail_sms['notification']) {
            $student_result = $this->getAbsentStudentlist($student_session_array);
            $sms_detail = $this->CI->smsconfig_model->getActiveSMS();
            if (!empty($student_result)) {

                foreach ($student_result as $student_result_k => $student_result_v) {
                    $detail = array(
                        'date' => $date,
                        'parent_app_key' => $student_result_v->parent_app_key,
                        'mobileno' => $student_result_v->mobileno,
                        'email' => $student_result_v->email,
                        'father_name' => $student_result_v->father_name,
                        'father_phone' => $student_result_v->father_phone,
                        'father_occupation' => $student_result_v->father_occupation,
                        'mother_name' => $student_result_v->mother_name,
                        'mother_phone' => $student_result_v->mother_phone,
                        'guardian_name' => $student_result_v->guardian_name,
                        'guardian_phone' => $student_result_v->guardian_phone,
                        'guardian_occupation' => $student_result_v->guardian_occupation,
                        'guardian_email' => $student_result_v->guardian_email,
                    );
                    if (isset($subject_attendence) && !empty($subject_attendence)) {
                        $detail['time_from'] = $subject_attendence->time_from;
                        $detail['time_to'] = $subject_attendence->time_to;
                        $detail['subject_name'] = $subject_attendence->name;
                        $detail['subject_code'] = $subject_attendence->code;
                        $detail['subject_type'] = $subject_attendence->type;
                    }

                    $detail['student_name'] = $this->CI->customlib->getFullName($student_result_v->firstname,$student_result_v->middlename,$student_result_v->lastname,$this->sch_setting->middlename,$this->sch_setting->lastname);

                    if ($chk_mail_sms['mail']) {
                        $this->CI->mailgateway->sentAbsentStudentMail($detail, $template, $subject);
                    }
                    if ($chk_mail_sms['sms'] && !empty($sms_detail)) {

                        $this->CI->smsgateway->sentAbsentStudentSMS($detail, $template, $template_id);
                    }
                    if ($chk_mail_sms['notification']) {

                        $this->CI->smsgateway->sentAbsentStudentNotification($detail, $template, $subject);
                    }
                }
            }
        }
    }

    public function getAbsentStudentlist($student_session_array) {

        $result = $this->CI->student_model->getStudentListBYStudentsessionID($student_session_array);
        if (!empty($result)) {
            return $result;
        }
        return false;
    }
      public function sendHomework($chk_mail_sms, $student_details, $template, $subject, $template_id) {
 
 

        $student_sms_list = array();
        $student_email_list = array();
        $student_notification_list = array();
        if ($chk_mail_sms['mail'] or $chk_mail_sms['sms'] or $chk_mail_sms['notification']) {
            $class_id = ($student_details['class_id']);
            $section_id = ($student_details['section_id']);
            $homework_date = $student_details['homework_date'];
            $submit_date = $student_details['submit_date'];
            $subject = $student_details['subject'];
            $student_list = $this->CI->student_model->getStudentByClassSectionID($class_id, $section_id);
            $sms_detail = $this->CI->smsconfig_model->getActiveSMS();
            if (!empty($student_list)) {

                foreach ($student_list as $student_key => $student_value) {

                    if ($student_value['app_key'] != "") {
                        $student_notification_list[] = array(
                            'app_key' => $student_value['app_key'],
                            'class' => $student_value['class'],
                            'section' => $student_value['section'],
                            'homework_date' => $homework_date,
                            'submit_date' => $submit_date,
                            'subject' => $subject,
                            'admission_no' => $student_value['admission_no'],
                            'student_name' => $this->CI->customlib->getFullName($student_value['firstname'],$student_value['middlename'],$student_value['lastname'],$this->sch_setting->middlename,$this->sch_setting->lastname),
                        );
                    }
                    if ($student_value['parent_app_key'] != "") {
                        $student_notification_list[] = array(
                            'app_key' => $student_value['parent_app_key'],
                            'class' => $student_value['class'],
                            'section' => $student_value['section'],
                            'homework_date' => $homework_date,
                            'submit_date' => $submit_date,
                            'subject' => $subject,
                            'admission_no' => $student_value['admission_no'],
                            'student_name' => $this->CI->customlib->getFullName($student_value['firstname'],$student_value['middlename'],$student_value['lastname'],$this->sch_setting->middlename,$this->sch_setting->lastname),
                        );
                    }

                    if ($student_value['email'] != "") {
                        $student_email_list[$student_value['email']] = array(
                            'class' => $student_value['class'],
                            'section' => $student_value['section'],
                            'homework_date' => $homework_date,
                            'submit_date' => $submit_date,
                            'subject' => $subject,
                            'admission_no' => $student_value['admission_no'],
                            'student_name' => $this->CI->customlib->getFullName($student_value['firstname'],$student_value['middlename'],$student_value['lastname'],$this->sch_setting->middlename,$this->sch_setting->lastname),
                        );
                    }
                    if ($student_value['guardian_email'] != "") {
                        $student_email_list[$student_value['guardian_email']] = array(
                            'class' => $student_value['class'],
                            'section' => $student_value['section'],
                            'homework_date' => $homework_date,
                            'submit_date' => $submit_date,
                            'subject' => $subject,
                            'admission_no' => $student_value['admission_no'],
                            'student_name' =>$this->CI->customlib->getFullName($student_value['firstname'],$student_value['middlename'],$student_value['lastname'],$this->sch_setting->middlename,$this->sch_setting->lastname),
                        );
                    }
                    if ($student_value['mobileno'] != "") {
                        $student_sms_list[$student_value['mobileno']] = array(
                            'class' => $student_value['class'],
                            'section' => $student_value['section'],
                            'homework_date' => $homework_date,
                            'submit_date' => $submit_date,
                            'subject' => $subject,
                            'admission_no' => $student_value['admission_no'],
                            'student_name' => $this->CI->customlib->getFullName($student_value['firstname'],$student_value['middlename'],$student_value['lastname'],$this->sch_setting->middlename,$this->sch_setting->lastname),
                        );
                    }
                    if ($student_value['guardian_phone'] != "") {
                        $student_sms_list[$student_value['guardian_phone']] = array(
                            'class' => $student_value['class'],
                            'section' => $student_value['section'],
                            'homework_date' => $homework_date,
                            'submit_date' => $submit_date,
                            'subject' => $subject,
                            'admission_no' => $student_value['admission_no'],
                            'student_name' => $this->CI->customlib->getFullName($student_value['firstname'],$student_value['middlename'],$student_value['lastname'],$this->sch_setting->middlename,$this->sch_setting->lastname),
                        );
                    }
                }
           
             
                if ($chk_mail_sms['mail']) {

                    if ($student_email_list) {
                        $this->CI->mailgateway->sentHomeworkStudentMail($student_email_list, $template, $subject);
                    }
                }

                if ($chk_mail_sms['sms'] && !empty($sms_detail)) {
                  
                    if ($student_sms_list) {
                        $this->CI->smsgateway->sentHomeworkStudentSMS($student_sms_list, $template, $template_id);
                    }
                }

                if ($chk_mail_sms['notification']) {

                    if (!empty($student_notification_list)) {
                        $this->CI->smsgateway->sentHomeworkStudentNotification($student_notification_list, $template, $subject);
                    }
                }
            }
        }
    }


    public function sendOnlineexam($chk_mail_sms, $student_details, $template, $subject, $template_id) {
        
        $student_sms_list = array();
        $student_email_list = array();
        $student_notification_list = array();
        if ($chk_mail_sms['mail'] or $chk_mail_sms['sms'] or $chk_mail_sms['notification']) {
            $student_list=$this->CI->onlineexam_model->getstudentByexam_id($student_details['exam_id']);        
            $sms_detail = $this->CI->smsconfig_model->getActiveSMS();
            if (!empty($student_list)) {
                foreach ($student_list as $student_key => $student_value) {

                    if ($student_value['app_key'] != "") {
                       $student_details['app_key']=$student_value['app_key'];
                        $student_notification_list[] = $student_details;
                    }
                    if ($student_value['parent_app_key'] != "") {
                        $student_details['app_key']=$student_value['app_key'];
                        $student_notification_list[] = $student_details;
                    }

                    if ($student_value['email'] != "") {
                        $student_email_list[$student_value['email']] = $student_details;
                    }
                    if ($student_value['guardian_email'] != "") {
                        $student_email_list[$student_value['guardian_email']] = $student_details;
                    }
                    if ($student_value['mobileno'] != "") {
                        $student_sms_list[$student_value['mobileno']] =$student_details;
                    }
                    if ($student_value['guardian_phone'] != "") {
                        $student_sms_list[$student_value['guardian_phone']] = $student_details;
                    }
                }
              
                if ($chk_mail_sms['mail']) {

                    if ($student_email_list) {
                        $this->CI->mailgateway->sentOnlineexamStudentMail($student_email_list, $template, $subject);
                    }
                }

                if ($chk_mail_sms['sms'] && !empty($sms_detail)) {

                    if ($student_sms_list) {
                        $this->CI->smsgateway->sentOnlineexamStudentSMS($student_sms_list, $template, $template_id);
                    }
                }

                if ($chk_mail_sms['notification']) {

                    if (!empty($student_notification_list)) {
                        $this->CI->smsgateway->sentOnlineexamStudentNotification($student_notification_list, $template, $subject);
                    }
                }
            }
        }
    }

   
    public function getForgotPasswordContent($student_result_detail, $template) {
      
        foreach ($student_result_detail as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        return $template;
    }

    public function sendOnlineadmission($chk_mail_sms, $student_details, $template, $subject,$template_id) {

        $student_sms_list = array();
        $student_email_list = array();
        $student_notification_list = array();
        if ($chk_mail_sms['mail'] or $chk_mail_sms['sms'] or $chk_mail_sms['notification']) {
            $sms_detail = $this->CI->smsconfig_model->getActiveSMS();
                if ($chk_mail_sms['mail']) {

                    $this->CI->mailgateway->sentOnlineadmissionStudentMail($student_details, $template, $subject);
                }

                if ($chk_mail_sms['sms'] && !empty($sms_detail)) {
                 
                     $this->CI->smsgateway->sentOnlineadmissionStudentSMS($student_details, $template,$template_id);
                }               
            
        }
    }
    public function login_notification($user_data, $user_type = 'staff') {
        // Utiliser Mailgateway pour envoyer l'email
        $this->CI->load->library('mailgateway');
        return $this->CI->mailgateway->sendLoginNotification($user_data, $user_type);
    }

    public function login_notification_15072026($user_data, $user_type = 'staff') {

        // ===== LOG DE DÉBUT =====
        log_message('debug', '=== DÉBUT login_notification ===');
        log_message('debug', 'user_data: ' . print_r($user_data, true));
        log_message('debug', 'user_type: ' . $user_type);

        // Vérifier que l'email existe
        if (empty($user_data['email'])) {
            log_message('error', 'login_notification: Email vide');
            return false;
        }

        log_message('debug', 'Email: ' . $user_data['email']);

        // Récupérer les paramètres de l'école
        $school_name = $this->CI->setting_model->getCurrentSchoolName();
        log_message('debug', 'School name: ' . $school_name);

        // Date et heure de connexion
        $login_time = date('d/m/Y à H:i:s');
        $ip_address = $this->CI->input->ip_address() ?: $_SERVER['REMOTE_ADDR'] ?? 'Inconnue';

        // Récupérer le nom
        $name = $user_data['name'] ?? 'Utilisateur';
        if ($user_type == 'staff' && !empty($user_data['surname'])) {
            $name = $user_data['name'] . ' ' . $user_data['surname'];
        } elseif ($user_type == 'student') {
            $name = $user_data['firstname'] ?? $user_data['name'] ?? 'Étudiant';
        } elseif ($user_type == 'parent') {
            $name = $user_data['guardian_name'] ?? 'Parent';
        }

        $subject = "🔐 Connexion réussie à " . $school_name;

        $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background-color: #f4f7fc; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #1e293b, #0f172a); padding: 20px 30px; text-align: center; border-radius: 12px 12px 0 0; }
            .header h1 { color: #FFD700; margin: 0; font-size: 22px; }
            .body { padding: 25px 30px; }
            .body h2 { color: #1e293b; margin-top: 0; }
            .body p { color: #475569; line-height: 1.6; }
            .info-box { background: #f8fafc; border-left: 4px solid #3B82F6; padding: 15px 20px; border-radius: 8px; margin: 15px 0; }
            .info-box p { margin: 5px 0; }
            .footer { background: #f1f5f9; padding: 12px 30px; text-align: center; color: #94a3b8; font-size: 12px; border-radius: 0 0 12px 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🔐 Connexion réussie</h1>
            </div>
            <div class='body'>
                <h2>Bonjour " . $name . " 👋</h2>
                <p>Vous venez de vous connecter avec succès à la plateforme <strong>" . $school_name . "</strong>.</p>
                <div class='info-box'>
                    <p><strong>📅 Date et heure :</strong> " . $login_time . "</p>
                    <p><strong>🖥️ Adresse IP :</strong> " . $ip_address . "</p>
                    <p><strong>📧 Email :</strong> " . $user_data['email'] . "</p>
                    <p><strong>👤 Type :</strong> " . ucfirst($user_type) . "</p>
                </div>
                <p style='color: #64748b; font-size: 14px;'>
                    ⚠️ Si vous n'êtes pas à l'origine de cette connexion, contactez l'administrateur.
                </p>
            </div>
            <div class='footer'>
                &copy; " . date('Y') . " " . $school_name . " - Tous droits réservés.
            </div>
        </div>
    </body>
    </html>";

        // ===== ENVOI AVEC LOGS DÉTAILLÉS =====
        log_message('debug', '=== TENTATIVE D\'ENVOI ===');
        log_message('debug', 'To: ' . $user_data['email']);
        log_message('debug', 'Subject: ' . $subject);

        $this->CI->load->library('email');
        $this->CI->email->clear();
        $this->CI->email->from($this->CI->config->item('smtp_user'), $school_name);
        $this->CI->email->to($user_data['email']);
        $this->CI->email->subject($subject);
        $this->CI->email->message($message);

        // Envoyer
        $send = $this->CI->email->send();

        if ($send) {
            log_message('info', '✅ Email de connexion envoyé à ' . $user_data['email']);
            return true;
        } else {
            log_message('error', '❌ Échec d\'envoi d\'email de connexion à ' . $user_data['email']);
            log_message('error', 'Erreur SMTP: ' . $this->CI->email->print_debugger());
            return false;
        }
    }

    public function sendOnlineadmissionFees($chk_mail_sms, $student_details, $template, $subject,$template_id) {
        $student_sms_list = array();
        $student_email_list = array();
        $student_notification_list = array();
        if ($chk_mail_sms['mail'] or $chk_mail_sms['sms'] or $chk_mail_sms['notification']) {
            $sms_detail = $this->CI->smsconfig_model->getActiveSMS();
                if ($chk_mail_sms['mail']) {

                    $this->CI->mailgateway->sentOnlineadmissionFeesMail($student_details, $template, $subject);
                }

                if ($chk_mail_sms['sms'] && !empty($sms_detail)) {

                        $this->CI->smsgateway->sentOnlineadmissionFeesSMS($student_details, $template, $template_id);
                }               
            
        }
    }


}
