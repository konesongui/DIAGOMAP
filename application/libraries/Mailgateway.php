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


    public function sendQuote($chk_mail_sms, $sender_details, $template, $subject) {
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

}
