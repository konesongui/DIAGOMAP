<?php


if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mailgateway {

    private $_CI;

    public function __construct() {
        $this->_CI = &get_instance();
        $this->_CI->load->model('setting_model');
        $this->_CI->load->model('studentfeemaster_model');
        $this->_CI->load->model('student_model');
        $this->_CI->load->model('teacher_model');
        $this->_CI->load->model('librarian_model');
        $this->_CI->load->model('accountant_model');
        $this->_CI->load->library('mailer');
        $this->_CI->mailer;
        $this->sch_setting = $this->_CI->setting_model->getSetting();
        // $this->load->library('customlib');
    }

    public function sentMail($sender_details, $template, $subject) {
        $msg = $this->getContent($sender_details, $template);

        $send_to = $sender_details->guardian_email;
        if (!empty($this->_CI->mail_config) && $send_to != "") {

            $this->_CI->mailer->send_mail($send_to, $subject, $msg);
        }
    }

    public function sentRegisterMail($id, $send_to, $template, $subject) {

        if (!empty($this->_CI->mail_config) && $send_to != "") {

            $msg = $this->getStudentRegistrationContent($id, $template);

            $this->_CI->mailer->send_mail($send_to, $subject, $msg);
        }
    }

    public function sendLoginCredential($chk_mail_sms, $sender_details, $template, $subject) {
        $msg = $this->getLoginCredentialContent($sender_details['credential_for'], $sender_details, $template);
        $send_to = $sender_details['email'];
        if (!empty($this->_CI->mail_config) && $send_to != "") {
            $this->_CI->mailer->send_mail($send_to, $subject, $msg);
        }
    }

    public function sentAddFeeMail($detail, $template, $subject) {
        $send_to = $detail->email;
        $msg = $this->getAddFeeContent($detail, $template);
        if (!empty($this->_CI->mail_config) && $send_to != "") {
            $this->_CI->mailer->send_mail($send_to, $subject, $msg);
        }
    }

    public function sentExamResultMail($detail, $template, $subject) {

        $msg = $this->getStudentResultContent($detail, $template);
        $send_to = $detail['guardian_email'];
        if (!empty($this->_CI->mail_config) && $send_to != "") {            
            $this->_CI->mailer->send_mail($send_to, $subject, $msg);
        }
    }

    public function sentExamResultMailStudent($detail, $template, $subject) {

        $msg = $this->getStudentResultContent($detail, $template);
        $send_to = $detail['email'];
        if (!empty($this->_CI->mail_config) && $send_to != "") {
            $this->_CI->mailer->send_mail($send_to, $subject, $msg);
        }
    }

    public function sentHomeworkStudentMail($detail, $template, $subject) {

        if (!empty($this->_CI->mail_config)) {
            foreach ($detail as $student_key => $student_value) {
                $send_to = $student_key;
                if ($send_to != "") {
                    $msg = $this->getHomeworkStudentContent($detail[$student_key], $template);
                    $this->_CI->mailer->send_mail($send_to, $subject, $msg);
                }
            }
        }
    }

     public function sentOnlineexamStudentMail($detail, $template, $subject) {

        if (!empty($this->_CI->mail_config)) {
            foreach ($detail as $student_key => $student_value) {
                $send_to = $student_key;
                if ($send_to != "") {
                    $msg = $this->getOnlineexamStudentContent($detail[$student_key], $template);
                    $this->_CI->mailer->send_mail($send_to, $subject, $msg);
                }
            }
        }
    }

    public function sentOnlineadmissionStudentMail($detail, $template, $subject) {

        if (!empty($this->_CI->mail_config)) {
            
                $send_to = $detail['email'];
                if ($send_to != "") {
                    $msg = $this->getOnlineadmissionStudentContent($detail, $template);
                    $this->_CI->mailer->send_mail($send_to, $subject, $msg);
                }
            
        }
    }

    public function getOnlineadmissionStudentContent($student_detail, $template) {

        foreach ($student_detail as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
       
        return $template;
    }
    public function sentOnlineadmissionFeesMail($detail, $template, $subject) {

        if (!empty($this->_CI->mail_config)) {
            
                $send_to = $detail['email'];
                if ($send_to != "") {
                    $msg = $this->getOnlineadmissionFeesContent($detail, $template);
                    $this->_CI->mailer->send_mail($send_to, $subject, $msg);
                }
            
        }
    }

    public function getOnlineadmissionFeesContent($student_detail, $template) {

        foreach ($student_detail as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
       
        return $template;
    }

    public function sentOnlineClassStudentMail($detail, $template) {

        if (!empty($this->_CI->mail_config)) {
            foreach ($detail as $student_key => $student_value) {
                $send_to = $student_key;
                if ($send_to != "") {
                    $msg = $this->getOnlineClassStudentContent($detail[$student_key], $template);

                    $subject = "Online Class";
                    $this->_CI->mailer->send_mail($send_to, $subject, $msg);
                }
            }
        }
    }

    public function sentOnlineMeetingStaffMail($detail, $template) {

        if (!empty($this->_CI->mail_config)) {
            foreach ($detail as $staff_key => $staff_value) {
                $send_to = $staff_key;
                if ($send_to != "") {
                    $msg = $this->getOnlineMeetingStaffContent($detail[$staff_key], $template);

                    $subject = "Online Meeting";
                    $this->_CI->mailer->send_mail($send_to, $subject, $msg);
                }
            }
        }
    }

    public function sentAbsentStudentMail($detail, $template, $subject) {

        $send_to = $detail['guardian_email'];
        $msg = $this->getAbsentStudentContent($detail, $template);

        if (!empty($this->_CI->mail_config) && $send_to != "") {
            $this->_CI->mailer->send_mail($send_to, $subject, $msg);
        }
    }
 
    public function getAddFeeContent($data, $template) {
        
        $currency_symbol = $this->sch_setting->currency_symbol;
        $school_name = $this->sch_setting->name;
        $invoice_data = json_decode($data->invoice);
        $data->invoice_id = $invoice_data->invoice_id;
        $data->sub_invoice_id = $invoice_data->sub_invoice_id;
        $fee = $this->_CI->studentfeemaster_model->getFeeByInvoice($data->invoice_id, $data->sub_invoice_id);
        $a = json_decode($fee->amount_detail);
        $record = $a->{$data->sub_invoice_id};
        $fee_amount = number_format((($record->amount + $record->amount_fine)), 2, '.', ',');

        $data->class = $fee->class;
        $data->section = $fee->section;
        $data->fee_amount = $currency_symbol . $fee_amount;
         $data->student_name = $this->_CI->customlib->getFullName($fee->firstname, $fee->middlename, $fee->lastname,$this->sch_setting->middlename,$this->sch_setting->lastname); 
        foreach ($data as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        return $template;
    }

    public function getHomeworkStudentContent($student_detail, $template) {

        foreach ($student_detail as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        return $template;
    }
     public function getOnlineexamStudentContent($student_detail, $template) {

        foreach ($student_detail as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        return $template;
    }

    public function getOnlineClassStudentContent($student_detail, $template) {

        foreach ($student_detail as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        return $template;
    }

    public function getOnlineMeetingStaffContent($student_detail, $template) {

        foreach ($student_detail as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        return $template;
    }

    public function getAbsentStudentContent($student_detail, $template) {

        $session_name = $this->_CI->setting_model->getCurrentSessionName();
        $student_detail['current_session_name'] = $session_name;
        foreach ($student_detail as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        return $template;
    }

    public function getStudentRegistrationContent($id, $template) {

        $session_name = $this->_CI->setting_model->getCurrentSessionName();
        $student = $this->_CI->student_model->get($id);
        $student['current_session_name'] = $session_name;
        $student['student_name'] = $this->_CI->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$this->sch_setting->middlename,$this->sch_setting->lastname); 
        foreach ($student as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        return $template;
    }

    public function getLoginCredentialContent($credential_for, $sender_details, $template) {

        if ($credential_for == "student") {
            $student = $this->_CI->student_model->get($sender_details['id']);
            $sender_details['url'] = site_url('site/userlogin');
            $sender_details['display_name'] = $this->_CI->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$this->sch_setting->middlename,$this->sch_setting->lastname); 
        } elseif ($credential_for == "parent") {
            $parent = $this->_CI->student_model->get($sender_details['id']);
            $sender_details['url'] = site_url('site/userlogin');
            $sender_details['display_name'] = $parent['guardian_name'];
        } elseif ($credential_for == "staff") {
            $staff = $this->_CI->staff_model->get($sender_details['id']);
            $sender_details['url'] = site_url('site/login');
            $sender_details['display_name'] = $staff['name'];
        }

        foreach ($sender_details as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        return $template;
    }

    public function getStudentResultContent($student_result_detail, $template) {
        foreach ($student_result_detail as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        return $template;
    }

    public function getContent($sender_details, $template) {
        foreach ($sender_details as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        return $template;
    }

    public function sentMailToAlumni($sender_details) {
        $send_to = $sender_details['email'];
        $subject = $sender_details['subject'];
        $msg = "Event From " . $sender_details['from_date'] . " To " . $sender_details['to_date'] . "<br><br>" .
                $sender_details['body'];

        if ($send_to != "") {
            $this->_CI->mailer->send_mail($send_to, $subject, $msg);
        }
    }


    /**
     * Envoi du bulletin de paie par email
     */
    public function sendPayslip($chk_mail_sms, $sender_details, $template, $subject)
    {
        log_message('debug', '🎯 === DÉBUT sendPayslip ===');
        log_message('debug', '📧 Email destinataire: ' . ($sender_details['email'] ?? 'NULL'));
        log_message('debug', '📝 Template length: ' . strlen($template ?? ''));
        log_message('debug', '📋 Sujet: ' . $subject);
        log_message('debug', '🔧 Mail config exists: ' . (!empty($this->_CI->mail_config) ? 'YES' : 'NO'));

        // Vérifications CRITIQUES
        if (empty($sender_details['email'])) {
            log_message('error', '❌ EMAIL DESTINATAIRE VIDE - Arrêt de l\'envoi');
            return false;
        }

        if (empty($template)) {
            log_message('error', '❌ TEMPLATE VIDE - Arrêt de l\'envoi');
            return false;
        }

        if (empty($this->_CI->mail_config)) {
            log_message('error', '❌ CONFIGURATION EMAIL NON DÉFINIE - Arrêt de l\'envoi');
            return false;
        }

        try {
            // Génération du PDF du bulletin de paie
            log_message('debug', '📄 Début génération PDF...');
            $pdf = $this->generatePayslipPDF($sender_details);  // ⭐ PAS generateQuotePDF

            if (!$pdf) {
                log_message('error', '❌ Impossible de générer le PDF du bulletin de paie');
                return false;
            }
            log_message('debug', '✅ PDF généré: ' . $pdf);

            // Vérifier que le fichier PDF existe
            if (!file_exists($pdf)) {
                log_message('error', '❌ Fichier PDF non trouvé: ' . $pdf);
                return false;
            }
            log_message('debug', '✅ Fichier PDF existe, taille: ' . filesize($pdf) . ' bytes');

            // Préparation des pièces jointes
            $attachments = [
                'files' => [
                    'name'     => ['bulletin_paie_' . $sender_details['employee_id'] . '_' . $sender_details['payslip_month'] . '_' . $sender_details['payslip_year'] . '.pdf'],
                    'type'     => ['application/pdf'],
                    'tmp_name' => [$pdf],
                    'error'    => [0],
                    'size'     => [filesize($pdf)]
                ]
            ];
            log_message('debug', '📎 Pièces jointes préparées');

            // Nom et email de l'utilisateur connecté
            $user_name  = $sender_details['data']['user_name']
                ?? ($sender_details['data']['user']['name'] ?? $this->_CI->customlib->getAdminSessionUserName())
                ?? 'RH';

            $user_email = $sender_details['data']['user_email']
                ?? ($sender_details['data']['user']['email'] ?? '')
                ?? 'rh@entreprise.ci';

            log_message('debug', '👤 User: ' . $user_name . ' <' . $user_email . '>');

            // Récupération des données du bulletin de paie
            $payslip = $sender_details['data']['payslip'];
            $staff = $sender_details['data']['staff'];
            $company = $sender_details['data']['company'];

            log_message('debug', '💰 Net salary: ' . ($payslip['net_salary'] ?? 'N/A'));
            log_message('debug', '🏢 Company: ' . ($company['name'] ?? 'N/A'));

            // Remplacement des variables dans le template
            $msg = str_replace(
                [
                    '{{user_name}}',
                    '{{user_email}}',
                    '{{staff_name}}',
                    '{{employee_id}}',
                    '{{payslip_month}}',
                    '{{payslip_year}}',
                    '{{net_salary}}',
                    '{{currency}}',
                    '{{company_name}}',
                    '{{company_phone}}',
                    '{{designation}}',
                    '{{department}}',
                    '{{payment_date}}',
                    '{{basic_salary}}',
                    '{{gross_salary}}'
                ],
                [
                    $user_name,
                    $user_email,
                    $sender_details['staff_name'] ?? 'N/A',
                    $sender_details['employee_id'] ?? 'N/A',
                    $this->getMonthName($payslip['month'] ?? ''),
                    $payslip['year'] ?? 'N/A',
                    number_format((float)($payslip['net_salary'] ?? 0), 2, ',', ' '),
                    $company['currency'] ?? 'FCFA',
                    $company['name'] ?? 'N/A',
                    $company['phone'] ?? 'N/A',
                    $staff['designation'] ?? 'N/A',
                    $staff['department'] ?? 'N/A',
                    !empty($payslip['payment_date']) ? date('d/m/Y', strtotime($payslip['payment_date'])) : 'N/A',
                    number_format((float)($payslip['basic'] ?? 0), 2, ',', ' '),
                    number_format((float)($payslip['gross_salary'] ?? 0), 2, ',', ' ')
                ],
                $template
            );

            log_message('debug', '✉️  Message template préparé, longueur: ' . strlen($msg) . ' caractères');

            $send_to = $sender_details['email'] ?? '';
            log_message('debug', '🎯 Envoi à: ' . $send_to);

            if (!empty($this->_CI->mail_config) && $send_to !== "") {
                log_message('debug', '🚀 Tentative d\'envoi email...');

                try {
                    // Envoi avec Reply-To
                    $result = $this->_CI->mailer->send_mail(
                        $send_to,
                        $subject,
                        $msg,
                        $attachments,
                        null,         // CC
                        $user_email,  // Reply-To (email de l'utilisateur connecté)
                        $user_name    // Nom affiché dans le Reply-To
                    );

                    log_message('debug', '📤 Résultat send_mail: ' . ($result ? 'SUCCÈS' : 'ÉCHEC'));

                    if ($result) {
                        log_message('debug', '✅ Email envoyé avec succès à: ' . $send_to);
                    } else {
                        log_message('error', '❌ Échec de l\'envoi email à: ' . $send_to);
                    }

                } catch (Exception $e) {
                    log_message('error', '❌ Exception lors de l\'envoi email: ' . $e->getMessage());
                    $result = false;
                }

                // Nettoyage du fichier PDF temporaire
                if (file_exists($pdf)) {
                    unlink($pdf);
                    log_message('debug', '🧹 Fichier PDF nettoyé: ' . $pdf);
                }

                log_message('debug', '✅ === FIN sendPayslip ===');
                return $result;
            } else {
                log_message('error', '❌ Conditions d\'envoi non remplies - Mail config: ' . (!empty($this->_CI->mail_config) ? 'OK' : 'NOK') . ', Send to: ' . $send_to);
                return false;
            }

        } catch (Exception $e) {
            log_message('error', '❌ Exception dans sendPayslip: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Génère le PDF du bulletin de paie
     */
    /**
     * Génère le PDF du bulletin de paie
     */
    /**
     * Génère le PDF du bulletin de paie
     */
    private function generatePayslipPDF($sender_details)
    {
        try {
            log_message('debug', '=== Génération PDF bulletin de paie ===');

            // Charger la bibliothèque mPDF
            require_once FCPATH . 'vendor/autoload.php';

            $config = [
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15,
                'default_font' => 'dejavusans',
                'autoPageBreak' => true
            ];

            $mpdf = new \Mpdf\Mpdf($config);

            // ⭐ TITRE BULLETIN DE PAIE
            $mpdf->SetTitle('Bulletin de Paie - ' . $sender_details['employee_id']);
            $mpdf->SetAuthor($sender_details['data']['company']['name']);

            // ⭐ CHARGER LES DONNÉES BULLETIN
            $data['result'] = $sender_details['data']['payslip'];
            $data['staff'] = $sender_details['data']['staff'];
            $data['company'] = $sender_details['data']['company'];

            // ⭐ CHARGER LA VUE BULLETIN DE PAIE
            $html = $this->_CI->load->view('admin/payroll/payslippdf', $data, true);

            $mpdf->WriteHTML($html);

            // ⭐ DOSSIER BULLETINS
            $upload_dir = FCPATH . 'uploads/payslips';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // ⭐ NOM DU FICHIER BULLETIN
            $filename = 'Bulletin_Paie_' . $sender_details['employee_id'] . '_' . $sender_details['payslip_month'] . '_' . $sender_details['payslip_year'] . '.pdf';
            $filepath = $upload_dir . '/' . $filename;

            $mpdf->Output($filepath, 'F');

            log_message('debug', 'PDF bulletin généré: ' . $filepath);
            return $filepath;

        } catch (\Exception $e) {
            log_message('error', 'Erreur génération PDF bulletin: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Convertit le mois numérique en nom de mois
     */
    private function getMonthName($month)
    {
        $months = [
            'January' => 'Janvier', 'February' => 'Février', 'March' => 'Mars',
            'April' => 'Avril', 'May' => 'Mai', 'June' => 'Juin',
            'July' => 'Juillet', 'August' => 'Août', 'September' => 'Septembre',
            'October' => 'Octobre', 'November' => 'Novembre', 'December' => 'Décembre'
        ];

        return $months[$month] ?? $month;
    }



    private function calculateNetSalary($sender_details)
    {
        $payslip = $sender_details['data']['payslip'];
        $positive_allowance = $sender_details['data']['positive_allowance'];
        $negative_allowance = $sender_details['data']['negative_allowance'];

        // Salaire de base
        $basic_salary = $payslip['basic'] ?? 0;

        // Total des allocations positives
        $total_positive = 0;
        if (!empty($positive_allowance)) {
            foreach ($positive_allowance as $allowance) {
                $total_positive += $allowance['amount'] ?? 0;
            }
        }

        // Total des allocations négatives (déductions)
        $total_negative = 0;
        if (!empty($negative_allowance)) {
            foreach ($negative_allowance as $deduction) {
                $total_negative += $deduction['amount'] ?? 0;
            }
        }

        // Net à payer = Salaire de base + Allocations positives - Déductions
        return ($basic_salary + $total_positive) - $total_negative;
    }

    public function sendQuote($chk_mail_sms, $sender_details, $template, $subject)
    {
        $pdf = $this->generateQuotePDF($sender_details);
        if (!$pdf) {
            log_message('error', 'Impossible de générer le PDF du devis');
            return false;
        }

        $attachments = [
            'files' => [
                'name'     => [basename($pdf)],
                'type'     => ['application/pdf'],
                'tmp_name' => [$pdf],
                'error'    => [0],
                'size'     => [filesize($pdf)]
            ]
        ];

        // Nom et email de l’utilisateur connecté
        $user_name  = $sender_details['data']['user_name']
            ?? ($sender_details['data']['user']['name'] ?? $this->customlib->getAdminSessionUserName())
            ?? 'Utilisateur';

        $user_email = $sender_details['data']['user_email']
            ?? ($sender_details['data']['user']['email'] ?? '')
            ?? 'noreply@entreprise.ci';

        // Remplacement dans le template
        $msg = str_replace(
            [
                '{{user_name}}',
                '{{user_email}}',
                '{{client_name}}',
                '{{quotation_number}}',
                '{{quotation_date}}',
                '{{total_amount}}',
                '{{currency}}',
                '{{validity_days}}',
                '{{company_name}}',
                '{{company_phone}}'
            ],
            [
                $user_name,
                $user_email,
                $sender_details['data']['quote']['customer_name'].' '.$sender_details['data']['quote']['customer_last_name'],
                $sender_details['data']['quote']['quote_number'],
                !empty($sender_details['data']['quote']['quote_date'])
                    ? date('d/m/Y', strtotime($sender_details['data']['quote']['quote_date']))
                    : 'N/A',
                number_format((float)$sender_details['data']['quote']['total_ttc'], 2, ',', ' '),
                $sender_details['data']['company']['currency'] ?? '',
                $this->calculateValidityDays(
                    $sender_details['data']['quote']['quote_date'] ?? null,
                    $sender_details['data']['quote']['valid_until'] ?? null
                ),
                $sender_details['data']['company']['name']  ?? '',
                $sender_details['data']['company']['phone'] ?? ''
            ],
            $template
        );

        $send_to = $sender_details['email'] ?? '';

        if (!empty($this->_CI->mail_config) && $send_to !== "") {
            // Ici on envoie avec Reply-To
            $this->_CI->mailer->send_mail(
                $send_to,
                $subject,
                $msg,
                $attachments,
                null,         // CC
                $user_email,  // Reply-To (email de l'utilisateur connecté)
                $user_name    // Nom affiché dans le Reply-To
            );
        }
    }



    public function sendQuote_o($chk_mail_sms, $sender_details, $template, $subject)
    {
        $pdf = $this->generateQuotePDF($sender_details);
        if (!$pdf) {
            log_message('error', 'Impossible de générer le PDF du devis');
            return false;
        }

        $attachments = [
            'files' => [
                'name'     => [basename($pdf)],
                'type'     => ['application/pdf'],
                'tmp_name' => [$pdf],
                'error'    => [0],
                'size'     => [filesize($pdf)]
            ]
        ];

        /// Récupération du nom et email de l'utilisateur
        $user_name  = $sender_details['data']['user_name']
            ?? ($sender_details['data']['user']['name'] ?? null)
            ?? $this->customlib->getAdminSessionUserName()
            ?? 'Utilisateur';

        $user_email = $sender_details['data']['user_email']
            ?? ($sender_details['data']['user']['email'] ?? null)
            ?? 'email@inconnu.com';

        $msg = str_replace(
            [
                '{{user_name}}',
                '{{user_email}}',
                '{{client_name}}',
                '{{quotation_number}}',
                '{{quotation_date}}',
                '{{total_amount}}',
                '{{currency}}',
                '{{validity_days}}',
                '{{company_name}}',
                '{{company_phone}}'
            ],
            [
                $user_name,
                $user_email,
                $sender_details['data']['quote']['customer_name'].' '.$sender_details['data']['quote']['customer_last_name'],
                $sender_details['data']['quote']['quote_number'],
                !empty($sender_details['data']['quote']['quote_date'])
                    ? date('d/m/Y', strtotime($sender_details['data']['quote']['quote_date']))
                    : 'N/A',
                number_format((float)$sender_details['data']['quote']['total_ttc'], 2, ',', ' '),
                $sender_details['data']['company']['currency'] ?? '',
                $this->calculateValidityDays(
                    $sender_details['data']['quote']['quote_date'] ?? null,
                    $sender_details['data']['quote']['valid_until'] ?? null
                ),
                $sender_details['data']['company']['name']  ?? '',
                $sender_details['data']['company']['phone'] ?? ''
            ],
            $template
        );

        $send_to = $sender_details['email'] ?? '';
        if (!empty($this->_CI->mail_config) && $send_to !== "") {
            $this->_CI->mailer->send_mail($send_to, $subject, $msg, $attachments);
        }
    }


    public function sendQuote_old($chk_mail_sms, $sender_details, $template, $subject) {
        // Générer le PDF du devis
        $pdf = $this->generateQuotePDF($sender_details);

        if (!$pdf) {
            log_message('error', 'Impossible de générer le PDF du devis');
            return false;
        }

        // Préparer les pièces jointes
        $attachments = array(
            'files' => array(
                'name' => array(basename($pdf)),
                'type' => array('application/pdf'),
                'tmp_name' => array($pdf),
                'error' => array(0),
                'size' => array(filesize($pdf))
            )
        );


        // var_dump($attachments);
        // // var_dump($sender_details);
        // exit;

        // Remplacer les variables dans le template en conservant les sauts de ligne
        $msg = str_replace(
            [
                '{{client_name}}',
                '{{quotation_number}}',
                '{{quotation_date}}',
                '{{total_amount}}',
                '{{currency}}',
                '{{validity_days}}',
                '{{company_name}}',
                '{{company_phone}}'
            ],
            [
                $sender_details['data']['quote']['customer_name'].' '.$sender_details['data']['quote']['customer_last_name'],
                $sender_details['data']['quote']['quote_number'],
                $sender_details['data']['quote']['quote_date'],
                number_format($sender_details['data']['quote']['total_ttc'], 2, ',', ' '),
                $sender_details['data']['company']['currency'],
                $this->calculateValidityDays($sender_details['data']['quote']['quote_date'], $sender_details['data']['quote']['valid_until']),
                $sender_details['data']['company']['name'],
                $sender_details['data']['company']['phone']
            ],
            $template
        );

        $send_to = $sender_details['email'];

        if (!empty($this->_CI->mail_config) && $send_to != "") {
            $this->_CI->mailer->send_mail($send_to, $subject, $msg, $attachments);
        }
    }

    // Fonction pour calculer la validité en jours
    private function calculateValidityDays($startDate, $endDate) {
        if (empty($startDate) || empty($endDate)) {
            return 0;
        }
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        return $end->diff($start)->days;
    }

    public function sendPay($chk_mail_sms, $sender_details, $template, $subject)
    {
        $pdf = $this->generatePaysPDF($sender_details);
        if (!$pdf) {
            log_message('error', 'Impossible de générer le PDF du devis');
            return false;
        }

        $attachments = [
            'files' => [
                'name'     => [basename($pdf)],
                'type'     => ['application/pdf'],
                'tmp_name' => [$pdf],
                'error'    => [0],
                'size'     => [filesize($pdf)]
            ]
        ];

        // Nom et email de l’utilisateur connecté
        $user_name  = $sender_details['data']['user_name']
            ?? ($sender_details['data']['user']['name'] ?? $this->customlib->getAdminSessionUserName())
            ?? 'Utilisateur';

        $user_email = $sender_details['data']['user_email']
            ?? ($sender_details['data']['user']['email'] ?? '')
            ?? 'noreply@entreprise.ci';

        // Remplacement dans le template
        $msg = str_replace(
            [
                '{{user_name}}',
                '{{user_email}}',
                '{{client_name}}',
                '{{quotation_number}}',
                '{{quotation_date}}',
                '{{total_amount}}',
                '{{currency}}',
                '{{validity_days}}',
                '{{company_name}}',
                '{{company_phone}}'
            ],
            [
                $user_name,
                $user_email,
                $sender_details['data']['quote']['customer_name'].' '.$sender_details['data']['quote']['customer_last_name'],
                $sender_details['data']['quote']['quote_number'],
                !empty($sender_details['data']['quote']['quote_date'])
                    ? date('d/m/Y', strtotime($sender_details['data']['quote']['quote_date']))
                    : 'N/A',
                number_format((float)$sender_details['data']['quote']['total_ttc'], 2, ',', ' '),
                $sender_details['data']['company']['currency'] ?? '',
                $this->calculateValidityDays(
                    $sender_details['data']['quote']['quote_date'] ?? null,
                    $sender_details['data']['quote']['valid_until'] ?? null
                ),
                $sender_details['data']['company']['name']  ?? '',
                $sender_details['data']['company']['phone'] ?? ''
            ],
            $template
        );

        $send_to = $sender_details['email'] ?? '';

        if (!empty($this->_CI->mail_config) && $send_to !== "") {
            // Ici on envoie avec Reply-To
            $this->_CI->mailer->send_mail(
                $send_to,
                $subject,
                $msg,
                $attachments,
                null,         // CC
                $user_email,  // Reply-To (email de l'utilisateur connecté)
                $user_name    // Nom affiché dans le Reply-To
            );
        }
    }

    private function generatePaysPDF($sender_details) {
        // Charger la bibliothèque mPDF
        require_once FCPATH . 'vendor/autoload.php';

        $data = [];
        // Configuration de mPDF avec des paramètres optimisés
        $config = [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_header' => 9,
            'margin_footer' => 9,
            'default_font' => 'dejavusans',
            'autoPageBreak' => true,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'compress' => true,
            'keepColumns' => true,
            'keep_table_proportions' => true,
            'shrink_tables_to_fit' => 1,
            'showImageErrors' => true,
            'debug' => false
        ];

        try {
            $mpdf = new \Mpdf\Mpdf($config);

            // Définir les informations du document
            $mpdf->SetTitle('Bulletin ' . $sender_details['quotation_number']);
            $mpdf->SetAuthor($sender_details['data']['company']['name']);

            $data['quote'] = $sender_details['data']['quote'];
            $data['company'] = $sender_details['data']['company'];
            $data['totalAsletter'] = $sender_details['data']['totalAsletter'];

            // Charger la vue
            $html = $this->_CI->load->view('admin/quote/printWithMpdf', $data, true);

            // Nettoyer le HTML avant de le passer à mPDF
            // $html = preg_replace('/\s+/', ' ', $html); // Supprimer les espaces multiples
            // $html = trim($html); // Supprimer les espaces au début et à la fin

            // Générer le PDF
            $mpdf->WriteHTML($html);

            // Créer le dossier uploads/quotes s'il n'existe pas
            $upload_dir = FCPATH . 'uploads/quotes';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Générer le nom du fichier
            $filename = 'Bulletin_' . $sender_details['quotation_number'] . '_' . date('Y-m-d') . '.pdf';
            $filepath = $upload_dir . '/' . $filename;

            // var_dump($filepath);
            // exit;

            // Sauvegarder le PDF
            // var_dump($mpdf->Output($filepath));
            // exit;
            $mpdf->Output($filepath, 'F');

            return $filepath;
        } catch (\Exception $e) {
            log_message('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
            return false;
        }
    }

    private function generateQuotePDF($sender_details) {
        // Charger la bibliothèque mPDF
        require_once FCPATH . 'vendor/autoload.php';

        $data = [];
        // Configuration de mPDF avec des paramètres optimisés
        $config = [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_header' => 9,
            'margin_footer' => 9,
            'default_font' => 'dejavusans',
            'autoPageBreak' => true,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'compress' => true,
            'keepColumns' => true,
            'keep_table_proportions' => true,
            'shrink_tables_to_fit' => 1,
            'showImageErrors' => true,
            'debug' => false
        ];

        try {
            $mpdf = new \Mpdf\Mpdf($config);
            
            // Définir les informations du document
            $mpdf->SetTitle('Devis ' . $sender_details['quotation_number']);
            $mpdf->SetAuthor($sender_details['data']['company']['name']);

            $data['quote'] = $sender_details['data']['quote'];
            $data['company'] = $sender_details['data']['company'];
            $data['totalAsletter'] = $sender_details['data']['totalAsletter'];

            // Charger la vue
            $html = $this->_CI->load->view('admin/quote/printWithMpdf', $data, true);
            
            // Nettoyer le HTML avant de le passer à mPDF
            // $html = preg_replace('/\s+/', ' ', $html); // Supprimer les espaces multiples
            // $html = trim($html); // Supprimer les espaces au début et à la fin
            
            // Générer le PDF
            $mpdf->WriteHTML($html);
            
            // Créer le dossier uploads/quotes s'il n'existe pas
            $upload_dir = FCPATH . 'uploads/quotes';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Générer le nom du fichier
            $filename = 'Devis_' . $sender_details['quotation_number'] . '_' . date('Y-m-d') . '.pdf';
            $filepath = $upload_dir . '/' . $filename;

            // var_dump($filepath);
            // exit;
            
            // Sauvegarder le PDF
            // var_dump($mpdf->Output($filepath));
            // exit;
            $mpdf->Output($filepath, 'F');
            
            return $filepath;
        } catch (\Exception $e) {
            log_message('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
            return false;
        }
    }


    public function sendDelivery($chk_mail_sms, $sender_details, $template, $subject) {
        // Générer le PDF du bon de livraison
        $pdf = $this->generateDeliveryPDF($sender_details);

        if (!$pdf) {
            log_message('error', 'Impossible de générer le PDF du bon de livraison');
            return false;
        }

        // Préparer les pièces jointes
        $attachments = array(
            'files' => array(
                'name'     => array(basename($pdf)),
                'type'     => array('application/pdf'),
                'tmp_name' => array($pdf),
                'error'    => array(0),
                'size'     => array(filesize($pdf))
            )
        );

        // Remplacer la variable dans le sujet
        $subject = str_replace('{{delivery_number}}', $sender_details['data']['delivery']['delivery_number'], $subject);

        // Remplacer les variables dans le template en conservant les sauts de ligne
        $msg = str_replace(
            [
                '{{client_name}}',
                '{{delivery_number}}',
                '{{delivery_date}}',
                '{{delivery_details}}',
                '{{company_name}}',
                '{{company_phone}}',
                '{{user_name}}',
                '{{user_email}}'
            ],
            [
                $sender_details['data']['delivery']['customer_name'].' '.$sender_details['data']['delivery']['customer_last_name'],
                $sender_details['data']['delivery']['delivery_number'],
                $sender_details['data']['delivery']['delivery_date'],
                $sender_details['data']['delivery']['delivery_address'].' '.$sender_details['data']['delivery']['shipping_method'],
                $sender_details['data']['company']['name'],
                $sender_details['data']['company']['phone'],
                $sender_details['data']['user']['username'],  // 🔹 Nom utilisateur connecté
                $sender_details['data']['user']['email']      // 🔹 Email utilisateur connecté
            ],
            $template
        );

        $send_to = $sender_details['email'];

        if (!empty($this->_CI->mail_config) && $send_to != "") {
            $this->_CI->mailer->send_mail(
                $send_to,
                $subject,
                $msg,
                $attachments,
                null, // CC
                $sender_details['data']['user']['email'],    // 🔹 Reply-To = email de l’utilisateur connecté
                $sender_details['data']['user']['username']  // 🔹 Nom affiché du Reply-To
            );
        }
    }

    public function sendDelivery_old050925($chk_mail_sms, $sender_details, $template, $subject) {
        // Générer le PDF du devis
        $pdf = $this->generateDeliveryPDF($sender_details);

        if (!$pdf) {
            log_message('error', 'Impossible de générer le PDF du devis');
            return false;
        }

        // Préparer les pièces jointes
        $attachments = array(
            'files' => array(
                'name' => array(basename($pdf)),
                'type' => array('application/pdf'),
                'tmp_name' => array($pdf),
                'error' => array(0),
                'size' => array(filesize($pdf))
            )
        );


        // var_dump($attachments);
        // var_dump($sender_details);
        // exit;

        // Remplacer la variable dans le sujet
        $subject = str_replace('{{delivery_number}}', $sender_details['data']['delivery']['delivery_number'], $subject);


        // Remplacer les variables dans le template en conservant les sauts de ligne
        $msg = str_replace(
            [
                '{{client_name}}',
                '{{delivery_number}}',
                '{{delivery_date}}',
                '{{delivery_details}}',
                '{{company_name}}',
                '{{company_phone}}'
            ],
            [
                $sender_details['data']['delivery']['customer_name'].' '.$sender_details['data']['delivery']['customer_last_name'],
                $sender_details['data']['delivery']['delivery_number'],
                $sender_details['data']['delivery']['delivery_date'],
                $sender_details['data']['delivery']['delivery_address'].' '. $sender_details['data']['delivery']['shipping_method'],
                $sender_details['data']['company']['name'],
                $sender_details['data']['company']['phone']

            ],
            $template
        );

        $send_to = $sender_details['email'];

        if (!empty($this->_CI->mail_config) && $send_to != "") {
            $this->_CI->mailer->send_mail($send_to, $subject, $msg, $attachments);
        }
    }

    private function generateDeliveryPDF($sender_details) {
        // Charger la bibliothèque mPDF
        require_once FCPATH . 'vendor/autoload.php';

        $data = [];
        // Configuration de mPDF avec des paramètres optimisés
        $config = [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_header' => 9,
            'margin_footer' => 9,
            'default_font' => 'dejavusans',
            'autoPageBreak' => true,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'compress' => true,
            'keepColumns' => true,
            'keep_table_proportions' => true,
            'shrink_tables_to_fit' => 1,
            'showImageErrors' => true,
            'debug' => false
        ];

        try {
            $mpdf = new \Mpdf\Mpdf($config);

            // Définir les informations du document
            $mpdf->SetTitle('Devis ' . $sender_details['delivery_number']);
            $mpdf->SetAuthor($sender_details['data']['company']['name']);

            $data['delivery'] = $sender_details['data']['delivery'];
            $data['company'] = $sender_details['data']['company'];
            $data['totalAsletter'] = $sender_details['data']['totalAsletter'];

            // Charger la vue
            $html = $this->_CI->load->view('admin/itemdelivery/printWithMpdf', $data, true);

            // Nettoyer le HTML avant de le passer à mPDF
            // $html = preg_replace('/\s+/', ' ', $html); // Supprimer les espaces multiples
            // $html = trim($html); // Supprimer les espaces au début et à la fin

            // Générer le PDF
            $mpdf->WriteHTML($html);

            // Créer le dossier uploads/deliveries s'il n'existe pas
            $upload_dir = FCPATH . 'uploads/deliveries';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Générer le nom du fichier
            $filename = 'Delivery_' . $sender_details['delivery_number'] . '_' . date('Y-m-d') . '.pdf';
            $filepath = $upload_dir . '/' . $filename;

            // var_dump($filepath);
            // exit;

            // Sauvegarder le PDF
            // var_dump($mpdf->Output($filepath));
            // exit;
            $mpdf->Output($filepath, 'F');

            return $filepath;
        } catch (\Exception $e) {
            log_message('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
            return false;
        }
    }

    public function sendInvoice($chk_mail_sms, $sender_details, $template, $subject) {
        // Générer le PDF de la facture
        $pdf = $this->generateInvoicePDF($sender_details);

        if (!$pdf) {
            log_message('error', 'Impossible de générer le PDF de la facture');
            return false;
        }

        // Préparer les pièces jointes
        $attachments = array(
            'files' => array(
                'name'     => array(basename($pdf)),
                'type'     => array('application/pdf'),
                'tmp_name' => array($pdf),
                'error'    => array(0),
                'size'     => array(filesize($pdf))
            )
        );

        // Remplacer la variable dans le sujet
        $subject = str_replace('{{invoice_number}}', $sender_details['data']['invoice']['invoice_number'], $subject);

        // Remplacer les variables dans le template en conservant les sauts de ligne
        $msg = str_replace(
            [
                '{{client_name}}',
                '{{invoice_number}}',
                '{{invoice_date}}',
                '{{total_amount}}',
                '{{due_date}}',
                '{{currency}}',
                '{{validity_days}}',
                '{{company_name}}',
                '{{company_phone}}',
                '{{user_name}}',
                '{{user_email}}'
            ],
            [
                $sender_details['data']['invoice']['customer_name'].' '.$sender_details['data']['invoice']['customer_last_name'],
                $sender_details['data']['invoice']['invoice_number'],
                $sender_details['data']['invoice']['invoice_date'],
                number_format($sender_details['data']['invoice']['total_ttc'], 2, ',', ' '),
                $sender_details['data']['invoice']['due_date'],
                $sender_details['data']['company']['currency'],
                $this->calculateValidityDays(
                    $sender_details['data']['invoice']['invoice_date'],
                    $sender_details['data']['invoice']['valid_until']
                ),
                $sender_details['data']['company']['name'],
                $sender_details['data']['company']['phone'],
                $sender_details['data']['user']['username'], // 🔹 Nom utilisateur connecté
                $sender_details['data']['user']['email']     // 🔹 Email utilisateur connecté
            ],
            $template
        );

        $send_to = $sender_details['email'];

        if (!empty($this->_CI->mail_config) && $send_to != "") {
            $this->_CI->mailer->send_mail(
                $send_to,
                $subject,
                $msg,
                $attachments,
                null, // CC
                $sender_details['data']['user']['email'],    // 🔹 Reply-To = email de l’utilisateur connecté
                $sender_details['data']['user']['username']  // 🔹 Nom affiché du Reply-To
            );
        }
    }


    public function sendInvoice_old050925($chk_mail_sms, $sender_details, $template, $subject) {
        // Générer le PDF du devis
        $pdf = $this->generateInvoicePDF($sender_details);

        if (!$pdf) {
            log_message('error', 'Impossible de générer le PDF du devis');
            return false;
        }
        
        // Préparer les pièces jointes
        $attachments = array(
            'files' => array(
                'name' => array(basename($pdf)),
                'type' => array('application/pdf'),
                'tmp_name' => array($pdf),
                'error' => array(0),
                'size' => array(filesize($pdf))
            )
        );


        // var_dump($pdf);
        // var_dump($attachments);
        // exit;

        // Remplacer la variable dans le sujet
        $subject = str_replace('{{invoice_number}}', $sender_details['data']['invoice']['invoice_number'], $subject);

        // Remplacer les variables dans le template en conservant les sauts de ligne
        $msg = str_replace(
            [
                '{{client_name}}',
                '{{invoice_number}}',
                '{{invoice_date}}',
                '{{total_amount}}',
                '{{due_date}}',
                '{{currency}}',
                '{{validity_days}}',
                '{{company_name}}',
                '{{company_phone}}'
            ],
            [   
                $sender_details['data']['invoice']['customer_name'].' '.$sender_details['data']['invoice']['customer_last_name'],
                $sender_details['data']['invoice']['invoice_number'],
                $sender_details['data']['invoice']['invoice_date'],
                number_format($sender_details['data']['invoice']['total_ttc'], 2, ',', ' '),
                $sender_details['data']['invoice']['due_date'],
                $sender_details['data']['company']['currency'],
                $this->calculateValidityDays($sender_details['data']['invoice']['invoice_date'], $sender_details['data']['invoice']['valid_until']),
                $sender_details['data']['company']['name'],
                $sender_details['data']['company']['phone']
            ],
            $template
        );
    
        $send_to = $sender_details['email'];
        
        if (!empty($this->_CI->mail_config) && $send_to != "") {
            $this->_CI->mailer->send_mail($send_to, $subject, $msg, $attachments);
        }
    }

    private function generateInvoicePDF($sender_details) {
        // Charger la bibliothèque mPDF
        require_once FCPATH . 'vendor/autoload.php';

        $data = [];
        // Configuration de mPDF avec des paramètres optimisés
        $config = [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_header' => 9,
            'margin_footer' => 9,
            'default_font' => 'dejavusans',
            'autoPageBreak' => true,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'compress' => true,
            'keepColumns' => true,
            'keep_table_proportions' => true,
            'shrink_tables_to_fit' => 1,
            'showImageErrors' => true,
            'debug' => false
        ];

        try {
            $mpdf = new \Mpdf\Mpdf($config);
            
            // Définir les informations du document
            $mpdf->SetTitle('Facture ' . $sender_details['invoice_number']);
            $mpdf->SetAuthor($sender_details['data']['company']['name']);

            $data['invoice'] = $sender_details['data']['invoice'];
            $data['company'] = $sender_details['data']['company'];
            $data['totalAsletter'] = $sender_details['data']['totalAsletter'];

            // Charger la vue
            $html = $this->_CI->load->view('admin/invoice/printWithMpdf', $data, true);
            
            // Nettoyer le HTML avant de le passer à mPDF
            // $html = preg_replace('/\s+/', ' ', $html); // Supprimer les espaces multiples
            // $html = trim($html); // Supprimer les espaces au début et à la fin
            
            // Générer le PDF
            $mpdf->WriteHTML($html);
            
            // Créer le dossier uploads/invoices s'il n'existe pas
            $upload_dir = FCPATH . 'uploads/invoices';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Générer le nom du fichier
            $filename = 'Facture_' . $sender_details['invoice_number'] . '_' . date('Y-m-d') . '.pdf';
            $filepath = $upload_dir . '/' . $filename;

            // var_dump($filepath);
            // exit;
            
            // Sauvegarder le PDF
            // var_dump($mpdf->Output($filepath));
            // exit;
            $mpdf->Output($filepath, 'F');
            
            return $filepath;
        } catch (\Exception $e) {
            log_message('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
            return false;
        }
    }


    
    public function sendQuoteNoStock($chk_mail_sms, $sender_details, $template, $subject) {
        // Générer le PDF du devis
        $pdf = $this->generateQuoteNoStockPDF($sender_details);

        if (!$pdf) {
            log_message('error', 'Impossible de générer le PDF du devis');
            return false;
        }
        
        // Préparer les pièces jointes
        $attachments = array(
            'files' => array(
                'name' => array(basename($pdf)),
                'type' => array('application/pdf'),
                'tmp_name' => array($pdf),
                'error' => array(0),
                'size' => array(filesize($pdf))
            )
        );

        // var_dump($sender_details['data']['quote']);
        // exit;

        // Remplacer la variable dans le sujet
        $subject = str_replace('{{quotation_number}}', $sender_details['data']['quote']['quote_number'], $subject);


        // Remplacer les variables dans le template en conservant les sauts de ligne
        $msg = str_replace(
            [
                '{{client_name}}',
                '{{quotation_number}}',
                '{{quotation_date}}',
                '{{total_amount}}',
                '{{currency}}',
                '{{validity_days}}',
                '{{company_name}}',
                '{{company_phone}}'
            ],
            [
                $sender_details['data']['quote']['customer_name'].' '.$sender_details['data']['quote']['customer_last_name'],
                $sender_details['data']['quote']['quote_number'],
                $sender_details['data']['quote']['quote_date'],
                number_format($sender_details['data']['quote']['total_ttc'], 2, ',', ' '),
                $sender_details['data']['company']['currency'],
                $this->calculateValidityDays($sender_details['data']['quote']['quote_date'], $sender_details['data']['quote']['valid_until']),
                $sender_details['data']['company']['name'],
                $sender_details['data']['company']['phone']
            ],
            $template
        );
    
        $send_to = $sender_details['email'];

        // var_dump($send_to);
        // exit;
        
        if (!empty($this->_CI->mail_config) && $send_to != "") {
            $this->_CI->mailer->send_mail($send_to, $subject, $msg, $attachments);
        }
    }

    private function generateQuoteNoStockPDF($sender_details) {
        // Charger la bibliothèque mPDF
        require_once FCPATH . 'vendor/autoload.php';

        $data = [];
        // Configuration de mPDF avec des paramètres optimisés
        $config = [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_header' => 9,
            'margin_footer' => 9,
            'default_font' => 'dejavusans',
            'autoPageBreak' => true,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'compress' => true,
            'keepColumns' => true,
            'keep_table_proportions' => true,
            'shrink_tables_to_fit' => 1,
            'showImageErrors' => true,
            'debug' => false
        ];

        try {
            $mpdf = new \Mpdf\Mpdf($config);
            
            // Définir les informations du document
            $mpdf->SetTitle('BDC ' . $sender_details['data']['quote']['quote_number']);
            $mpdf->SetAuthor($sender_details['data']['company']['name']);

            // Préparer les données pour la vue
            $data['quote'] = $sender_details['data']['quote'];
            $data['company'] = $sender_details['data']['company'];
            $data['totalAsletter'] = $sender_details['data']['totalAsletter'];
            
            // Les produits et catégories sont maintenant directement dans les données
            $data['items'] = $sender_details['data']['quote']['items'];
            $data['categories'] = $sender_details['data']['quote']['categories'];

            // Charger la vue
            $html = $this->_CI->load->view('admin/orders_purchases/printWithMpdf', $data, true);
            
            // Générer le PDF
            $mpdf->WriteHTML($html);
            
            // Créer le dossier uploads/quotes s'il n'existe pas
            $upload_dir = FCPATH . 'uploads/quotes';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Générer le nom du fichier
            //$filename = 'Bon_' . $sender_details['data']['quote']['quote_number'] . '_' . date('Y-m-d') . '.pdf';
            $filename = $sender_details['data']['quote']['quote_number'] . '_' . date('Y-m-d') . '.pdf';

            $filepath = $upload_dir . '/' . $filename;
            
            // Sauvegarder le PDF
            $mpdf->Output($filepath, 'F');
            
            return $filepath;
        } catch (\Exception $e) {
            log_message('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
            return false;
        }
    }

}
