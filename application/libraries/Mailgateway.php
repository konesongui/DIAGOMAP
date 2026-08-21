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
                'margin_bottom'  => 15,
                'default_font' => 'dejavusans',
                'autoPageBreak' => true
            ];

            $mpdf = new \Mpdf\Mpdf($config);

            // ⭐ CHARGER LES DONNÉES COMPLÈTES
            $data = $sender_details['data'];

            // ⭐ PASSER TOUTES LES VARIABLES NÉCESSAIRES À LA VUE
            $view_data = array(
                // Informations entreprise
                'companyName' => $data['company']['name'],
                'companyLogo' => $data['company']['admin_logo'],
                'companyAddress' => $data['company']['address'],
                'companyPhone' => $data['company']['phone'],
                'sch_setting' => $data['company'], // ou votre objet settings

                // Informations employé
                'employeeName' => $data['staff']['name'],
                'employeeSurname' => $data['staff']['surname'],
                'cnpsNo' => $data['staff']['cnps_no'],
                'employeeId' => $data['staff']['employee_id'],
                'employeeDesignation' => $data['staff']['designation'],
                'employeeDepartment' => $data['staff']['department'],
                'employeeMonth' => $sender_details['payslip_month'],
                'employeeYear' => $sender_details['payslip_year'],

                // Données du bulletin
                'basicSalary' => $data['payslip']['categorie_salaire'],
                'surSalary' => $data['payslip']['sursalaire'],
                'primeTransport' => $data['payslip']['prime_trans'],
                'primeTechnique' => $data['payslip']['primet'],
                'forfaitHS' => $data['payslip']['forfait_hs'],
                'primeResponsabilite' => $data['payslip']['prime_resp'],
                'primeGratification' => $data['payslip']['prime_grati'],
                'primeAssiduite' => $data['payslip']['prime_assi'],
                'primeRisque' => $data['payslip']['prime_risque'],
                'primeFonction' => $data['payslip']['prime_fonction'],
                'primeRendement' => $data['payslip']['prime_rend'],
                'bonus' => $data['payslip']['bonus'],

                // Déductions
                'cnpsRegime' => $data['payslip']['cnps_regim'],
                'cnpsAccident' => $data['payslip']['cnps_tra'],
                'impotRevenu' => $data['payslip']['imp_revenu'],
                'cmu' => $data['payslip']['cmu'],
                'avancesAcomptes' => $data['payslip']['avan_acom'],

                // Totaux
                'grossSocial' => $data['payslip']['gross_social'],
                'grossFiscal' => $data['payslip']['total_fiscal'],
                'grossIts' => $data['payslip']['tax'],
                'grossSalary' => $data['payslip']['gross_salary'],
                'totalDeduction' => $data['payslip']['total_revenu'],
                'netSalary' => $data['payslip']['net_salary'],

                // Autres
                'paymentDate' => $data['payslip']['payment_date'],
                'cnpsNo' => $data['staff']['cnps_no'] ?? '',
                'PaymentMode' => $data['staff']['payment_mode'] ?? '',

                // Allocations
                'positive_allowance' => $data['positive_allowance'] ?? array(),
                'negative_allowance' => $data['negative_allowance'] ?? array(),

                // Symbol monétaire
                'currency_symbol' => $this->_CI->customlib->getSchoolCurrencyFormat()
            );

            // ⭐ CHARGER LA VUE AVEC TOUTES LES DONNÉES
            $html = $this->_CI->load->view('admin/payroll/payslippdf', $view_data, true);

            $mpdf->WriteHTML($html);

            // ⭐ DOSSIER BULLETINS
            $upload_dir = FCPATH . 'uploads/payslips';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // ⭐ NOM DU FICHIER
            $filename = 'Bulletin_Paie_' . $data['staff']['employee_id'] . '_' . $sender_details['payslip_month'] . '_' . $sender_details['payslip_year'] . '.pdf';
            $filepath = $upload_dir . '/' . $filename;

            $mpdf->Output($filepath, 'F');

            log_message('debug', '✅ PDF bulletin généré avec données: ' . $filepath);
            return $filepath;

        } catch (\Exception $e) {
            log_message('error', '❌ Erreur génération PDF bulletin: ' . $e->getMessage());
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





    public function sendQuote_($chk_mail_sms, $sender_details, $template, $subject)
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

    public function sendQuote($chk_mail_sms, $sender_details, $template, $subject) {
        // Générer le PDF
        $pdf = $this->generateQuotePDF($sender_details);
        if (!$pdf) {
            log_message('error', 'Mailgateway: Échec génération PDF pour devis ' . ($sender_details['quotation_number'] ?? 'inconnu'));
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

        // Préparation du message
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
                ($sender_details['data']['quote']['customer_name'] ?? '') . ' ' . ($sender_details['data']['quote']['customer_last_name'] ?? ''),
                $sender_details['data']['quote']['quote_number'] ?? '',
                !empty($sender_details['data']['quote']['quote_date']) ? date('d/m/Y', strtotime($sender_details['data']['quote']['quote_date'])) : 'N/A',
                number_format((float)($sender_details['data']['quote']['total_ttc'] ?? 0), 2, ',', ' '),
                $sender_details['data']['company']['currency'] ?? '',
                $this->calculateValidityDays(
                    $sender_details['data']['quote']['quote_date'] ?? null,
                    $sender_details['data']['quote']['valid_until'] ?? null
                ),
                $sender_details['data']['company']['name'] ?? '',
                $sender_details['data']['company']['phone'] ?? ''
            ],
            $template
        );

        $send_to = $sender_details['email'] ?? '';
        if (empty($this->_CI->mail_config) || empty($send_to)) {
            log_message('error', 'Mailgateway: Mail non configuré ou destinataire vide');
            return false;
        }

        // Appel de l'envoi et capture du retour
        $sent = $this->_CI->mailer->send_mail($send_to, $subject, $msg, $attachments);

        if (!$sent) {
            log_message('error', 'Mailgateway: Échec envoi email à ' . $send_to . ' pour devis ' . ($sender_details['quotation_number'] ?? ''));
            // Optionnel : supprimer le PDF temporaire
            if (file_exists($pdf)) unlink($pdf);
            return false;
        }

        log_message('info', 'Mailgateway: Devis ' . ($sender_details['quotation_number'] ?? '') . ' envoyé à ' . $send_to);
        return true;
    }


    public function sendQuote_26($chk_mail_sms, $sender_details, $template, $subject) {
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
        $pdf = $this->generatePayslipPDF($sender_details);

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
        $subject = str_replace('
        {{payslip_number}}', $sender_details['data']['quote']['quote_number'], $subject);


        // Remplacer les variables dans le template en conservant les sauts de ligne
        $msg = str_replace(
            [
                '{{client_name}}',
                '{{payslip_number}}',
                '{{quotation_date}}',
                '{{net_salary}}',
                '{{currency}}',
                '{{validity_days}}',
                '{{company_name}}',
                '{{company_phone}}'
            ],
            [
                $sender_details['data']['quote']['customer_name'].' '.$sender_details['data']['quote']['customer_last_name'],
                $sender_details['data']['quote']['quote_number'],
                $sender_details['data']['quote']['quote_date'],
                number_format($sender_details['data']['quote']['net_salary'], 2, ',', ' '),
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


    /**
     * Envoie une notification de connexion réussie par email
     * @param array $user_data Données de l'utilisateur
     * @param string $user_type Type d'utilisateur (staff, student, parent)
     */

    /**
     * Envoie une notification de connexion réussie par email
     * @param array $user_data Données de l'utilisateur
     * @param string $user_type Type d'utilisateur (staff, student, parent)
     */

    public function sendLoginNotification($user_data, $user_type = 'staff') {

        // Vérifier que l'email existe
        if (empty($user_data['email'])) {
            log_message('error', 'sendLoginNotification: Email vide');
            return false;
        }

        // Récupérer les paramètres de l'école
        $school_name = $this->_CI->setting_model->getCurrentSchoolName();
        $school_logo = base_url('uploads/school_content/admin_logo/' . $this->_CI->setting_model->getAdminlogo());

        // Récupérer l'email de l'administrateur (à personnaliser)
        $admin_email = $this->_CI->setting_model->getAdminEmail();

        // Date et heure de connexion
        $login_time = date('d/m/Y à H:i:s');
        $ip_address = $this->_CI->input->ip_address() ?: $_SERVER['REMOTE_ADDR'] ?? 'Inconnue';
        $user_agent = $this->_CI->input->user_agent() ?: $_SERVER['HTTP_USER_AGENT'] ?? 'Inconnu';

        // Récupérer le navigateur
        $browser = $this->_getBrowser($user_agent);

        // Récupérer le nom de l'utilisateur
        $name = $user_data['name'] ?? 'Utilisateur';
        if ($user_type == 'staff' && !empty($user_data['surname'])) {
            $name = $user_data['name'] . ' ' . $user_data['surname'];
        } elseif ($user_type == 'student') {
            $name = $user_data['firstname'] ?? $user_data['name'] ?? 'Étudiant';
        } elseif ($user_type == 'parent') {
            $name = $user_data['guardian_name'] ?? 'Parent';
        }

        // Construction du sujet
        $subject = "🔐 Connexion réussie à " . $school_name;

        // Construction du message HTML avec logo
        $message = "
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7fc; padding: 20px; margin: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: linear-gradient(135deg, #1e293b, #0f172a); padding: 25px 30px; text-align: center; }
        .header img { max-height: 80px; margin-bottom: 10px; }
        .header h1 { color: #FFD700; margin: 0; font-size: 24px; }
        .body { padding: 30px; }
        .body h2 { color: #1e293b; margin-top: 0; font-size: 20px; }
        .body p { color: #475569; line-height: 1.6; }
        .info-box { background: #f8fafc; border-left: 4px solid #3B82F6; padding: 15px 20px; border-radius: 8px; margin: 15px 0; }
        .info-box p { margin: 8px 0; }
        .info-box strong { color: #1e293b; }
        .badge { display: inline-block; background: #10B981; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .footer { background: #f1f5f9; padding: 15px 30px; text-align: center; color: #94a3b8; font-size: 12px; }
        .footer a { color: #3B82F6; text-decoration: none; }
        .alert-box { background: #fef2f2; border-left: 4px solid #ef4444; padding: 12px 16px; border-radius: 8px; margin: 15px 0; }
        .alert-box p { color: #991b1b; margin: 0; font-size: 13px; }
        .user-type-badge { display: inline-block; background: #3B82F6; color: white; padding: 2px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <img src='{$school_logo}' alt='Logo " . $school_name . "' style='max-height:80px;'>
            <h1>🔐 Connexion réussie</h1>
        </div>
        <div class='body'>
            <h2>Bonjour " . $name . " 👋</h2>
            <p>Vous venez de vous connecter avec succès à la plateforme <strong>" . $school_name . "</strong>.</p>
            
            <div class='info-box'>
                <p><strong>📅 Date et heure :</strong> " . $login_time . "</p>
                <p><strong>🖥️ Adresse IP :</strong> " . $ip_address . "</p>
                <p><strong>🌐 Navigateur :</strong> " . $browser . "</p>
                <p><strong>📧 Email :</strong> " . $user_data['email'] . "</p>
                <p><strong>👤 Type d'utilisateur :</strong> <span class='user-type-badge'>" . ucfirst($user_type) . "</span></p>
            </div>
            
            <div class='alert-box'>
                <p>⚠️ <strong>Si vous n'êtes pas à l'origine de cette connexion</strong>, veuillez contacter immédiatement l'administrateur.</p>
            </div>
            
            <p style='margin-top: 20px; color: #64748b; font-size: 14px;'>
                <span class='badge'>✓ Sécurisé</span> Cette notification a été envoyée automatiquement.
            </p>
            
            <p style='margin-top: 20px;'>
                <strong>Besoin d'aide ?</strong> Contactez le support à 
                <a href='mailto:" . $admin_email . "' style='color: #3B82F6;'>" . $admin_email . "</a>
            </p>
        </div>
        <div class='footer'>
            &copy; " . date('Y') . " " . $school_name . " - Tous droits réservés.
            <br>
            <small>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</small>
        </div>
    </div>
</body>
</html>";

        // ===== ENVOI VIA MAILER SANS CC =====
        $send_to = $user_data['email'];

        log_message('debug', 'sendLoginNotification - Tentative d\'envoi à: ' . $send_to);
        log_message('debug', 'sendLoginNotification - Sujet: ' . $subject);

        // Utiliser le mailer qui est déjà chargé dans le constructeur
        if (!empty($this->_CI->mail_config) && !empty($send_to)) {
            // send_mail($to, $subject, $message, $attachments = null, $cc = null, $reply_to = null, $reply_name = null)
            $result = $this->_CI->mailer->send_mail($send_to, $subject, $message);

            if ($result) {
                log_message('info', '✅ Email de connexion envoyé à ' . $send_to);
                return true;
            } else {
                log_message('error', '❌ Échec d\'envoi d\'email de connexion à ' . $send_to);
                return false;
            }
        } else {
            log_message('error', '❌ Mail_config vide ou destinataire vide');
            log_message('error', 'mail_config: ' . print_r($this->_CI->mail_config, true));
            return false;
        }
    }

    public function sendLoginNotification_aveccopy($user_data, $user_type = 'staff') {

        // Vérifier que l'email existe
        if (empty($user_data['email'])) {
            log_message('error', 'sendLoginNotification: Email vide');
            return false;
        }

        // Récupérer les paramètres de l'école
        $school_name = $this->_CI->setting_model->getCurrentSchoolName();
        $school_logo = base_url('uploads/school_content/admin_logo/' . $this->_CI->setting_model->getAdminlogo());

        // Récupérer l'email de l'administrateur (à personnaliser)
        // Option 1 : depuis la table settings
        $admin_email = $this->_CI->setting_model->getAdminEmail(); // à adapter selon votre modèle
        // Option 2 : email fixe
        // $admin_email = 'admin@votre-ecole.com';
        // Option 3 : récupérer le premier admin
        // $admin = $this->_CI->staff_model->getAdmin();
        // $admin_email = $admin->email ?? '';

        // Date et heure de connexion
        $login_time = date('d/m/Y à H:i:s');
        $ip_address = $this->_CI->input->ip_address() ?: $_SERVER['REMOTE_ADDR'] ?? 'Inconnue';
        $user_agent = $this->_CI->input->user_agent() ?: $_SERVER['HTTP_USER_AGENT'] ?? 'Inconnu';

        // Récupérer le navigateur
        $browser = $this->_getBrowser($user_agent);

        // Récupérer le nom de l'utilisateur
        $name = $user_data['name'] ?? 'Utilisateur';
        if ($user_type == 'staff' && !empty($user_data['surname'])) {
            $name = $user_data['name'] . ' ' . $user_data['surname'];
        } elseif ($user_type == 'student') {
            $name = $user_data['firstname'] ?? $user_data['name'] ?? 'Étudiant';
        } elseif ($user_type == 'parent') {
            $name = $user_data['guardian_name'] ?? 'Parent';
        }

        // Construction du sujet
        $subject = "🔐 Connexion réussie à " . $school_name;

        // Construction du message HTML avec logo
        $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background-color: #f4f7fc; padding: 20px; margin: 0; }
            .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden; }
            .header { background: linear-gradient(135deg, #1e293b, #0f172a); padding: 25px 30px; text-align: center; }
            .header img { max-height: 80px; margin-bottom: 10px; }
            .header h1 { color: #FFD700; margin: 0; font-size: 24px; }
            .body { padding: 30px; }
            .body h2 { color: #1e293b; margin-top: 0; font-size: 20px; }
            .body p { color: #475569; line-height: 1.6; }
            .info-box { background: #f8fafc; border-left: 4px solid #3B82F6; padding: 15px 20px; border-radius: 8px; margin: 15px 0; }
            .info-box p { margin: 8px 0; }
            .info-box strong { color: #1e293b; }
            .badge { display: inline-block; background: #10B981; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
            .footer { background: #f1f5f9; padding: 15px 30px; text-align: center; color: #94a3b8; font-size: 12px; }
            .footer a { color: #3B82F6; text-decoration: none; }
            .alert-box { background: #fef2f2; border-left: 4px solid #ef4444; padding: 12px 16px; border-radius: 8px; margin: 15px 0; }
            .alert-box p { color: #991b1b; margin: 0; font-size: 13px; }
            .user-type-badge { display: inline-block; background: #3B82F6; color: white; padding: 2px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <img src='{$school_logo}' alt='Logo " . $school_name . "' style='max-height:80px;'>
                <h1>🔐 Connexion réussie</h1>
            </div>
            <div class='body'>
                <h2>Bonjour " . $name . " 👋</h2>
                <p>Vous venez de vous connecter avec succès à la plateforme <strong>" . $school_name . "</strong>.</p>
                
                <div class='info-box'>
                    <p><strong>📅 Date et heure :</strong> " . $login_time . "</p>
                    <p><strong>🖥️ Adresse IP :</strong> " . $ip_address . "</p>
                    <p><strong>🌐 Navigateur :</strong> " . $browser . "</p>
                    <p><strong>📧 Email :</strong> " . $user_data['email'] . "</p>
                    <p><strong>👤 Type d'utilisateur :</strong> <span class='user-type-badge'>" . ucfirst($user_type) . "</span></p>
                </div>
                
                <div class='alert-box'>
                    <p>⚠️ <strong>Si vous n'êtes pas à l'origine de cette connexion</strong>, veuillez contacter immédiatement l'administrateur.</p>
                </div>
                
                <p style='margin-top: 20px; color: #64748b; font-size: 14px;'>
                    <span class='badge'>✓ Sécurisé</span> Cette notification a été envoyée automatiquement.
                </p>
                
                <p style='margin-top: 20px;'>
                    <strong>Besoin d'aide ?</strong> Contactez le support à 
                    <a href='mailto:" . $admin_email . "' style='color: #3B82F6;'>" . $admin_email . "</a>
                </p>
            </div>
            <div class='footer'>
                &copy; " . date('Y') . " " . $school_name . " - Tous droits réservés.
                <br>
                <small>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</small>
            </div>
        </div>
    </body>
    </html>";

        // ===== ENVOI VIA MAILER AVEC CC =====
        $send_to = $user_data['email'];
        $cc = !empty($admin_email) ? $admin_email : null;

        log_message('debug', 'sendLoginNotification - Tentative d\'envoi à: ' . $send_to);
        log_message('debug', 'sendLoginNotification - CC: ' . ($cc ?? 'Aucun'));
        log_message('debug', 'sendLoginNotification - Sujet: ' . $subject);

        // Utiliser le mailer qui est déjà chargé dans le constructeur
        if (!empty($this->_CI->mail_config) && !empty($send_to)) {
            // send_mail($to, $subject, $message, $attachments = null, $cc = null, $reply_to = null, $reply_name = null)
            $result = $this->_CI->mailer->send_mail($send_to, $subject, $message, null, $cc);

            if ($result) {
                log_message('info', '✅ Email de connexion envoyé à ' . $send_to . ' (CC: ' . ($cc ?? 'Aucun') . ')');
                return true;
            } else {
                log_message('error', '❌ Échec d\'envoi d\'email de connexion à ' . $send_to);
                return false;
            }
        } else {
            log_message('error', '❌ Mail_config vide ou destinataire vide');
            log_message('error', 'mail_config: ' . print_r($this->_CI->mail_config, true));
            return false;
        }
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
    public function sendLoginNotification17072026($user_data, $user_type = 'staff') {

        // Vérifier que l'email existe
        if (empty($user_data['email'])) {
            log_message('error', 'sendLoginNotification: Email vide');
            return false;
        }

        // Récupérer les paramètres de l'école
        $school_name = $this->_CI->setting_model->getCurrentSchoolName();

        // Date et heure de connexion
        $login_time = date('d/m/Y à H:i:s');
        $ip_address = $this->_CI->input->ip_address() ?: $_SERVER['REMOTE_ADDR'] ?? 'Inconnue';
        $user_agent = $this->_CI->input->user_agent() ?: $_SERVER['HTTP_USER_AGENT'] ?? 'Inconnu';

        // Récupérer le nom de l'utilisateur
        $name = $user_data['name'] ?? 'Utilisateur';
        if ($user_type == 'staff' && !empty($user_data['surname'])) {
            $name = $user_data['name'] . ' ' . $user_data['surname'];
        } elseif ($user_type == 'student') {
            $name = $user_data['firstname'] ?? $user_data['name'] ?? 'Étudiant';
        } elseif ($user_type == 'parent') {
            $name = $user_data['guardian_name'] ?? 'Parent';
        }

        // Construction du message
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

        // ===== ENVOI VIA MAILER =====
        $send_to = $user_data['email'];

        log_message('debug', 'sendLoginNotification - Tentative d\'envoi à: ' . $send_to);
        log_message('debug', 'sendLoginNotification - Sujet: ' . $subject);

        // Utiliser le mailer qui est déjà chargé
        if (!empty($this->_CI->mail_config) && !empty($send_to)) {
            $result = $this->_CI->mailer->send_mail($send_to, $subject, $message);

            if ($result) {
                log_message('info', '✅ Email de connexion envoyé à ' . $send_to);
                return true;
            } else {
                log_message('error', '❌ Échec d\'envoi d\'email de connexion à ' . $send_to);
                return false;
            }
        } else {
            log_message('error', '❌ Mail_config vide ou destinataire vide');
            return false;
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
