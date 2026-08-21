<?php
/**
 * Configuration optionnelle pour le système QR Attendance
 * 
 * Lieu : application/config/qrcode_attendance.php
 * Vous pouvez charger cette config dans votre controller
 */

$config['qrcode_attendance'] = array(
    
    // Paramètres de génération QR
    'qr_size' => 400,
    'qr_error_correction' => 'H',
    
    // Délai d'expiration du token (en minutes)
    'token_expiry' => 60,
    
    // Limites de tentatives
    'max_login_attempts' => 5,
    'lockout_duration' => 15, // minutes
    
    // Heures d'entrée/sortie par défaut
    'default_arrival_time' => '08:00',
    'default_departure_time' => '17:00',
    
    // Permissions requises
    'required_privilege' => 'staff_attendance',
    'required_action' => 'can_view',
    
    // Notifications
    'send_email_on_scan' => false,
    'notification_email' => 'hr@example.com',
    
    // Format des horaires
    'time_format' => 'H:i:s',
    'date_format' => 'd/m/Y',
    
    // Couleurs du système
    'colors' => array(
        'primary' => '#28669e',
        'secondary' => '#fec32e',
        'success' => '#28a745',
        'danger' => '#dc3545',
        'warning' => '#ffc107',
        'info' => '#17a2b8',
    ),
    
    // Icônes Font Awesome
    'icons' => array(
        'qr_code' => 'fa-qrcode',
        'clock' => 'fa-clock-o',
        'chart' => 'fa-bar-chart',
        'arrival' => 'fa-sign-in-alt',
        'departure' => 'fa-sign-out-alt',
    ),
    
    // Messages personnalisés
    'messages' => array(
        'welcome' => 'Bienvenue ! Présence enregistrée à %s',
        'departure' => 'Au revoir ! Départ enregistré à %s',
        'invalid_employee' => 'Employé non trouvé',
        'invalid_password' => 'Mot de passe incorrect',
        'already_marked' => 'Vous avez déjà enregistré votre départ aujourd\'hui',
    ),
);

?>
