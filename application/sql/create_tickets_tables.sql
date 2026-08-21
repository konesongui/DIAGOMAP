CREATE TABLE IF NOT EXISTS `tickets_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `couleur` varchar(20) NOT NULL DEFAULT '#3498db',
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `tickets_categories` (`id`, `nom`, `couleur`, `deleted`) VALUES
(1, 'Technique', '#3498db', 0), (2, 'Question', '#2ecc71', 0), (3, 'Incident', '#e74c3c', 0);

CREATE TABLE IF NOT EXISTS `tickets_priorites` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `niveau` int NOT NULL DEFAULT 1,
  `couleur` varchar(20) NOT NULL DEFAULT '#3498db',
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `tickets_priorites` (`id`, `nom`, `niveau`, `couleur`, `deleted`) VALUES
(1, 'Basse', 1, '#2ecc71', 0), (2, 'Normale', 2, '#3498db', 0), (3, 'Haute', 3, '#f39c12', 0), (4, 'Urgente', 4, '#e74c3c', 0);

CREATE TABLE IF NOT EXISTS `tickets_statuts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `couleur` varchar(20) NOT NULL DEFAULT '#3498db',
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `tickets_statuts` (`id`, `nom`, `couleur`, `deleted`) VALUES
(1, 'Ouvert', '#3498db', 0), (2, 'En cours', '#f39c12', 0), (3, 'Résolu', '#2ecc71', 0), (4, 'Fermé', '#7f8c8d', 0);

CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(50) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `categorie_id` int DEFAULT NULL,
  `priorite_id` int DEFAULT NULL,
  `statut_id` int NOT NULL DEFAULT 1,
  `created_by` int DEFAULT NULL,
  `assigned_to` int DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `date_echeance` datetime DEFAULT NULL,
  `date_resolution` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `fichier` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), UNIQUE KEY `ticket_number` (`ticket_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tickets_reponses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ticket_id` int NOT NULL,
  `staff_id` int NOT NULL,
  `message` text NOT NULL,
  `est_interne` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;