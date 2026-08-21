<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mailer {

    public $mail_config;
    private $sch_setting;

    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->load->model('emailconfig_model');
        $this->CI->mail_config = $this->CI->emailconfig_model->getActiveEmail();
        $this->CI->load->model('setting_model');
        $this->sch_setting = $this->CI->setting_model->get();
    }

    /**
     * Envoi d'un mail avec possibilité de définir un Reply-To personnalisé
     *
     * @param string $toemail    Adresse destinataire
     * @param string $subject    Sujet du mail
     * @param string $body       Contenu HTML du mail
     * @param array  $FILES      Pièces jointes
     * @param string $cc         Adresse(s) en copie
     * @param string $reply_to   Adresse email pour le "Reply-To"
     * @param string $reply_name Nom affiché pour le "Reply-To"
     * @return bool
     */
    public function send_mail($toemail, $subject, $body, $FILES = array(), $cc = "", $reply_to = null, $reply_name = null) {

        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';

        $school_name  = $this->sch_setting[0]['name'];
        $school_email = $this->sch_setting[0]['email'];

        if ($this->CI->mail_config->email_type == "smtp") {
            $mail->IsSMTP();
            $mail->SMTPAuth   = ($this->CI->mail_config->smtp_auth != "") ? $this->CI->mail_config->smtp_auth : "";
            $mail->SMTPSecure = $this->CI->mail_config->ssl_tls;
            $mail->Host       = $this->CI->mail_config->smtp_server;
            $mail->Port       = $this->CI->mail_config->smtp_port;
            $mail->Username   = $this->CI->mail_config->smtp_username;
            $mail->Password   = $this->CI->mail_config->smtp_password;

            $mail->SetFrom($this->CI->mail_config->smtp_username, $school_name);

            // Gestion du Reply-To
            if ($reply_to) {
                $mail->AddReplyTo($reply_to, $reply_name ?? $reply_to);
            } else {
                $mail->AddReplyTo($this->CI->mail_config->smtp_username, $school_name);
            }

        } else {
            $mail->isSMTP();
            $mail->Host        = 'localhost';
            $mail->SMTPAuth    = false;
            $mail->SMTPAutoTLS = false;
            $mail->Port        = 25;

            $mail->SetFrom($school_email, $school_name);

            // Gestion du Reply-To
            if ($reply_to) {
                $mail->AddReplyTo($reply_to, $reply_name ?? $reply_to);
            } else {
                $mail->AddReplyTo($school_email, $school_name);
            }
        }

        // Destinataire
        $mail->AddAddress($toemail);

        // CC
        if (!empty($cc)) {
            $mail->AddCC($cc);
        }

        // Sujet et contenu
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->IsHTML(true);

        // Gestion des pièces jointes
        if (!empty($FILES) && isset($FILES['files'])) {
            foreach ($FILES['files']['name'] as $key => $value) {
                if (!empty($FILES['files']['tmp_name'][$key])) {
                    $mail->AddAttachment(
                        $FILES['files']['tmp_name'][$key],
                        $FILES['files']['name'][$key],
                        'base64',
                        $FILES['files']['type'][$key]
                    );
                }
            }
        }

        return $mail->Send();
    }

}
