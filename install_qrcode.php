<?php
/**
 * Script d'installation du système QR Code Attendance
 * Exécutez ce script une seule fois pour configurer le système
 * 
 * Accès : http://localhost/diagoma/install_qrcode.php
 */

// Connexion à la base de données
$config = include 'application/config/database.php';
$db_config = $config['default'];

$conn = mysqli_connect(
    $db_config['hostname'],
    $db_config['username'],
    $db_config['password'],
    $db_config['database']
);

if (!$conn) {
    die("Erreur de connexion: " . mysqli_connect_error());
}

$conn->set_charset("utf8mb4");

// Créer les tables nécessaires
$sql1 = "
CREATE TABLE IF NOT EXISTS `staff_attendance_qr` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `staff_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `arrival_time` time,
  `departure_time` time,
  `scan_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('arrival','departure','complete') DEFAULT 'arrival',
  `notes` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`staff_id`) REFERENCES `staff`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_daily_attendance` (`staff_id`, `attendance_date`),
  INDEX `idx_attendance_date` (`attendance_date`),
  INDEX `idx_staff_id` (`staff_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_date_range` (`attendance_date`, `arrival_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

$sql2 = "
CREATE TABLE IF NOT EXISTS `qr_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `token` varchar(128) NOT NULL UNIQUE,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `used_at` datetime DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  INDEX `idx_token` (`token`),
  INDEX `idx_expires_at` (`expires_at`),
  INDEX `idx_is_used` (`is_used`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

// Exécuter la création des deux tables
$success1 = mysqli_query($conn, $sql1);
$err1 = $success1 ? null : mysqli_error($conn);
$success2 = mysqli_query($conn, $sql2);
$err2 = $success2 ? null : mysqli_error($conn);

if ($success1 && $success2) {
    $message = "✅ Tables créées avec succès !";
    $success = true;
} else {
    $message = "❌ Erreur : ";
    if (!$success1) { $message .= "staff_attendance_qr: " . $err1 . "; "; }
    if (!$success2) { $message .= "qr_tokens: " . $err2 . "; "; }
    $success = false;
}

if (mysqli_query($conn, $sql)) {
    $message = "✅ Table créée avec succès !";
    $success = true;
} else {
    $message = "❌ Erreur : " . mysqli_error($conn);
    $success = false;
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - QR Code Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #28669e 0%, #1a3f5c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .install-card {
            max-width: 600px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .install-header {
            background: linear-gradient(135deg, #28669e 0%, #fec32e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .install-body {
            padding: 30px;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #28669e;
            border-radius: 5px;
        }
        .success-message {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .error-message {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .btn-next {
            background: linear-gradient(135deg, #28669e 0%, #fec32e 100%);
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <div class="install-card">
        <div class="install-header">
            <h1><i class="fas fa-qrcode"></i> Installation QR Code Attendance</h1>
            <p>Système de présence par QR Code</p>
        </div>

        <div class="install-body">
            <?php if ($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> <strong>Installation réussie !</strong>
                </div>

                <h4>Prochaines étapes :</h4>

                <div class="step">
                    <h5><i class="fas fa-check"></i> Étape 1 : Routes</h5>
                    <p>Ajoutez les routes suivantes à <code>application/config/routes.php</code> :</p>
                    <pre><code class="bg-light p-2">$route['admin/qrattendance/display'] = 'admin/Qrattendance/display_qr';
$route['admin/qrattendance/scan'] = 'admin/Qrattendance/scan_page';
$route['admin/qrattendance/process'] = 'admin/Qrattendance/process_scan';
$route['admin/qrattendance/today'] = 'admin/Qrattendance/today_attendance';
$route['admin/qrattendance/report'] = 'admin/Qrattendance/attendance_report';</code></pre>
                </div>

                <div class="step">
                    <h5><i class="fas fa-check"></i> Étape 2 : Vérification</h5>
                    <p>Fichiers créés :</p>
                    <ul>
                        <li>✅ Controller : <code>application/controllers/admin/Qrattendance.php</code></li>
                        <li>✅ Vue QR : <code>application/views/admin/qrattendance/display_qr.php</code></li>
                        <li>✅ Vue Scan : <code>application/views/admin/qrattendance/scan_page.php</code></li>
                        <li>✅ Vue Aujourd'hui : <code>application/views/admin/qrattendance/today_attendance.php</code></li>
                        <li>✅ Vue Rapport : <code>application/views/admin/qrattendance/attendance_report.php</code></li>
                    </ul>
                </div>

                <div class="step">
                    <h5><i class="fas fa-check"></i> Étape 3 : Accès</h5>
                    <p>Vous pouvez maintenant accéder à :</p>
                    <ul>
                        <li><a href="<?php echo base_url('admin/qrattendance/display_qr'); ?>" target="_blank">Affichage QR Code</a></li>
                        <li><a href="<?php echo base_url('admin/qrattendance/today_attendance'); ?>" target="_blank">Présences du jour</a></li>
                        <li><a href="<?php echo base_url('admin/qrattendance/attendance_report'); ?>" target="_blank">Rapport</a></li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <strong>⚠️ Important :</strong> Supprimez ce fichier (install_qrcode.php) après l'installation pour des raisons de sécurité.
                </div>

            <?php else: ?>
                <div class="error-message">
                    <i class="fas fa-times-circle"></i> <strong>Erreur d'installation</strong>
                    <p><?php echo $message; ?></p>
                </div>
                <p>Vérifiez que :</p>
                <ul>
                    <li>La configuration de base de données est correcte</li>
                    <li>Vous avez les permissions nécessaires</li>
                    <li>La table <code>staff</code> existe</li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
