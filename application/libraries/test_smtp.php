<?php
// test_smtp.php
require_once 'system/autoload.php';

$config = array(
    'protocol' => 'smtp',
    'smtp_host' => 'ssl://smtp.gmail.com',
    'smtp_port' => 465,
    'smtp_user' => 'ton_email@gmail.com',
    'smtp_pass' => 'ton_mot_de_passe_app',
    'smtp_timeout' => 30,
    'mailtype' => 'html',
    'charset' => 'utf-8'
);

$ci =& get_instance();
$ci->load->library('email', $config);
$ci->email->set_newline("\r\n");

$ci->email->from('ton_email@gmail.com', 'Test SMTP');
$ci->email->to('destinataire@test.com');
$ci->email->subject('Test SMTP Configuration');
$ci->email->message('Test d\'envoi SMTP');

if ($ci->email->send()) {
    echo "SUCCÈS - Email envoyé";
    echo $ci->email->print_debugger();
} else {
    echo "ÉCHEC - Email non envoyé";
    echo $ci->email->print_debugger();
}
?>