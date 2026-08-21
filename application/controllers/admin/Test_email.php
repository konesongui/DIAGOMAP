<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Test_email extends Admin_Controller {
// Crée un fichier test_email.php dans ton contrôleur
public function testSimpleEmail()
{
    $this->load->library('mailer');

    $result = $this->mailer->send_mail(
        'ton.email@test.com', // ⭐ METS TON EMAIL ICI
        'Test Email Direct',
        'Ceci est un test d\'envoi d\'email direct',
        null, // pas de pièce jointe
        null, // pas de CC
        'test@entreprise.ci',
        'Testeur'
    );

    echo $result ? 'Email envoyé' : 'Échec envoi';
    log_message('debug', 'Test email result: ' . ($result ? 'SUCCESS' : 'FAILED'));
}
}