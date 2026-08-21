-- Table pour stocker les tokens QR générés pour les sessions de scan
-- Exécutez ce script dans votre base de données

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
  INDEX `idx_is_used` (`is_used`),
  CONSTRAINT `fk_qr_tokens_employee` FOREIGN KEY (`employee_id`) REFERENCES `staff`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;