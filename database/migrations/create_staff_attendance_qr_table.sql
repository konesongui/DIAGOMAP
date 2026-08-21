-- Table pour stocker les données de présence QR Code
-- Exécutez ce script dans votre base de données

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
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index supplémentaires pour les performances
ALTER TABLE `staff_attendance_qr` ADD INDEX `idx_date_range` (`attendance_date`, `arrival_time`);
