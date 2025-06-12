-- Table des devis sans stock
CREATE TABLE IF NOT EXISTS `quotes_nostock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_number` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `quote_date` date NOT NULL,
  `valid_until` date DEFAULT NULL,
  `payment_terms` text DEFAULT NULL,
  `delivery_terms` text DEFAULT NULL,
  `delivery_location` varchar(255) DEFAULT NULL,
  `apply_tva` tinyint(1) NOT NULL DEFAULT 0,
  `tva_rate` decimal(5,2) DEFAULT 0.00,
  `tva_amount` decimal(10,2) DEFAULT 0.00,
  `total_ht` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_ttc` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `validation_status` tinyint(1) DEFAULT NULL,
  `validation_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `validated_at` datetime DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quote_number` (`quote_number`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `quotes_nostock_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des articles des devis sans stock
CREATE TABLE IF NOT EXISTS `quote_nostock_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(20) DEFAULT 'unité',
  `line_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `position` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `quote_id` (`quote_id`),
  CONSTRAINT `quote_nostock_items_ibfk_1` FOREIGN KEY (`quote_id`) REFERENCES `quotes_nostock` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 