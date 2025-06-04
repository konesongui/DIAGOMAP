-- Table principale des entrées de stock
CREATE TABLE `stock_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference` varchar(50) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `grand_total` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL COMMENT 'ID de l\'utilisateur qui a créé l\'entrée',
  `updated_by` int(11) DEFAULT NULL COMMENT 'ID de l\'utilisateur qui a modifié l\'entrée',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Date de suppression si soft delete',
  `deleted_by` int(11) DEFAULT NULL COMMENT 'ID de l\'utilisateur qui a supprimé l\'entrée',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des articles des entrées de stock
CREATE TABLE `stock_entry_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_entry_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `line_total` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL COMMENT 'ID de l\'utilisateur qui a créé la ligne',
  `updated_by` int(11) DEFAULT NULL COMMENT 'ID de l\'utilisateur qui a modifié la ligne',
  PRIMARY KEY (`id`),
  KEY `stock_entry_id` (`stock_entry_id`),
  KEY `category_id` (`category_id`),
  KEY `item_id` (`item_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `stock_entry_items_ibfk_1` FOREIGN KEY (`stock_entry_id`) REFERENCES `stock_entries` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `stock_entries`
ADD CONSTRAINT `stock_entries_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
ADD CONSTRAINT `stock_entries_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

ALTER TABLE `stock_entry_items`
ADD CONSTRAINT `stock_entry_items_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);



CREATE TABLE `stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `store_id` int(11) NULL,
  `initial_quantity` int(11) NULL DEFAULT 0,
  `current_quantity` int(11) NULL DEFAULT 0 COMMENT 'Quantité disponible réelle',
  `weighted_avg_price` decimal(12,2) DEFAULT NULL COMMENT 'Prix moyen pondéré (PMP)',
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_store` (`item_id`,`store_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `quotes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `quote_number` VARCHAR(20) NOT NULL COMMENT 'Format: CO-YYYY-NNN',
  `customer_id` INT NOT NULL,
  `quote_date` DATE NOT NULL,
  `valid_until` DATE DEFAULT NULL COMMENT 'Date limite de validité',
  `rejected_at` DATE DEFAULT NULL COMMENT 'Date de rejet',
  `validated_at` DATE DEFAULT NULL COMMENT 'Date de validation',
  `payment_terms` VARCHAR(100) DEFAULT NULL COMMENT 'Conditions de paiement',
  `delivery_terms` VARCHAR(100) DEFAULT NULL,
  `delivery_location` VARCHAR(100) DEFAULT NULL,
  `designation` VARCHAR(255) DEFAULT NULL,
  `apply_tva` TINYINT(1) NOT NULL DEFAULT 1,
  `tva_rate` DECIMAL(5,2) NOT NULL DEFAULT 20.00,
  `tva_amount` DECIMAL(10,2) DEFAULT 0.00,
  `total_ht` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_ttc` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `notes` TEXT DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(),
  `created_by` INT DEFAULT NULL,
  `updated_by` INT DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_by` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quote_number` (`quote_number`),
  KEY `customer_id` (`customer_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  KEY `deleted_by` (`deleted_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `quote_items` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `quote_id` INT NOT NULL,
  `category_id` INT(11) DEFAULT NULL,
  `item_id` INT(11) DEFAULT NULL,
  `quantity` DECIMAL(10,2) NOT NULL,
  `unit_price` DECIMAL(12,2) NOT NULL,
  `unit` VARCHAR(20) DEFAULT 'Unité',
  `line_total` DECIMAL(12,2) NOT NULL,
  `delivered_quantity` DECIMAL(10,2) DEFAULT 0.00,
  `position` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(),
  `created_by` INT DEFAULT NULL,
  `updated_by` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quote_id` (`quote_id`),
  KEY `item_id` (`item_id`),
  KEY `category_id` (`category_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `orders` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `order_number` VARCHAR(20) NOT NULL COMMENT 'Format: CO-YYYY-NNN',
  `quote_id` INT DEFAULT NULL COMMENT 'Devis d\'origine',
  `customer_id` INT NOT NULL,
  `order_date` DATE NOT NULL,
  `valid_until` DATE DEFAULT NULL COMMENT 'Date limite de validité',
  `payment_terms` VARCHAR(100) DEFAULT NULL COMMENT 'Conditions de paiement',
  `delivery_terms` VARCHAR(100) DEFAULT NULL,
  `delivery_location` VARCHAR(100) DEFAULT NULL,
  `designation` VARCHAR(255) DEFAULT NULL,
  `apply_tva` TINYINT(1) NOT NULL DEFAULT 1,
  `tva_rate` DECIMAL(5,2) NOT NULL DEFAULT 20.00,
  `tva_amount` DECIMAL(10,2) DEFAULT 0.00,
  `total_ht` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_ttc` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `notes` TEXT DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(),
  `created_by` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `customer_id` (`customer_id`),
  KEY `quote_id` (`quote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `order_items` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `order_id` INT NOT NULL,
  `category_id` INT(11) DEFAULT NULL,
  `item_id` INT(11) DEFAULT NULL,
  `quantity` DECIMAL(10,2) NOT NULL,
  `unit_price` DECIMAL(12,2) NOT NULL,
  `unit` VARCHAR(20) DEFAULT 'Unité',
  `line_total` DECIMAL(12,2) NOT NULL,
  `delivered_quantity` DECIMAL(10,2) DEFAULT 0.00,
  `position` INT(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `item_id` (`item_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `deliveries` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `delivery_number` VARCHAR(20) NOT NULL COMMENT 'Format: BL-YYYY-NNN',
  `customer_id` INT NOT NULL,
  `order_id` INT NULL,
  `designation` VARCHAR(255) DEFAULT NULL,
  `delivery_date` DATE NOT NULL,
  `shipping_method` VARCHAR(50) DEFAULT NULL,
  `tracking_number` VARCHAR(100) DEFAULT NULL,
  `deadline` DATE DEFAULT NULL,
  `delivery_address` VARCHAR(255) DEFAULT NULL,
  `delivery_at` DATE DEFAULT NULL COMMENT 'Date de livraison',
  `cancelled_at` DATE DEFAULT NULL COMMENT 'Date de suppression',
  `cancelled_reason` TEXT DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `apply_tva` TINYINT(1) NOT NULL DEFAULT 1,
  `tva_rate` DECIMAL(5,2) NOT NULL DEFAULT 20.00,
  `tva_amount` DECIMAL(10,2) DEFAULT 0.00,
  `total_ht` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_ttc` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `created_by` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_number` (`delivery_number`),
  KEY `order_id` (`order_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `delivery_items` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `delivery_id` INT NOT NULL,
  `category_id` INT(11) DEFAULT NULL,
  `item_id` INT(11) DEFAULT NULL,
  `quantity` DECIMAL(10,2) NOT NULL,
  `delivered_quantity` DECIMAL(10,2) DEFAULT 0.00,
  `unit_price` DECIMAL(12,2) NOT NULL,
  `unit` VARCHAR(20) DEFAULT 'Unité',
  `line_total` DECIMAL(12,2) NOT NULL,
  `position` INT(11) DEFAULT 0,
  `batch_number` VARCHAR(50) DEFAULT NULL COMMENT 'Numéro de lot',
  `serial_numbers` TEXT DEFAULT NULL COMMENT 'Numéros de série (JSON)',
  PRIMARY KEY (`id`),
  KEY `delivery_id` (`delivery_id`),
  KEY `item_id` (`item_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `invoices` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `customer_id` INT NOT NULL,
  `invoice_number` VARCHAR(20) NOT NULL COMMENT 'Format: FAC-YYYY-NNN',
  `delivery_id` INT NOT NULL,
  `invoice_date` DATE NOT NULL,
  `due_date` DATE NOT NULL COMMENT 'Date d\'échéance',
  `payment_method` ENUM('cash','credit_card','transfer','check','online') DEFAULT NULL,
  `apply_tva` TINYINT(1) NOT NULL DEFAULT 1,
  `tva_rate` DECIMAL(5,2) NOT NULL DEFAULT 20.00,
  `tva_amount` DECIMAL(10,2) DEFAULT 0.00,
  `total_ht` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_ttc` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `remaining_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `amount_paid` DECIMAL(12,2) DEFAULT 0.00,
  `notes` TEXT DEFAULT NULL,
  `cancelled_at` DATE DEFAULT NULL COMMENT 'Date d\'annulation',
  `cancelled_reason` TEXT DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(),
  `created_by` INT DEFAULT NULL,
  `updated_by` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `delivery_id` (`delivery_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `invoice_items` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `invoice_id` INT NOT NULL,
  `category_id` INT(11) DEFAULT NULL,
  `item_id` INT(11) DEFAULT NULL,
  `quantity` DECIMAL(10,2) NOT NULL,
  `unit_price` DECIMAL(12,2) NOT NULL,
  `unit` VARCHAR(20) DEFAULT 'Unité',
  `line_total` DECIMAL(12,2) NOT NULL,
  `position` INT(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `item_id` (`item_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `payments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `invoice_id` INT NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `payment_date` DATE NOT NULL,
  `method` ENUM('cash','credit_card','transfer','check','online') NOT NULL,
  `reference` VARCHAR(100) DEFAULT NULL COMMENT 'Référence paiement',
  `notes` TEXT DEFAULT NULL,
  `recorded_by` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `quotes`
ADD CONSTRAINT `quotes_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `clients` (`id`),
ADD CONSTRAINT `quotes_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
ADD CONSTRAINT `quotes_updated_by_fk` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`),
ADD CONSTRAINT `quotes_deleted_by_fk` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`);

ALTER TABLE `quote_items`
ADD CONSTRAINT `quote_items_quote_fk` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `quote_items_category_fk` FOREIGN KEY (`category_id`) REFERENCES `item_category` (`id`),
ADD CONSTRAINT `quote_items_item_fk` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`),
ADD CONSTRAINT `quote_items_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
ADD CONSTRAINT `quote_items_updated_by_fk` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);


ALTER TABLE `orders`
ADD CONSTRAINT `orders_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `clients` (`id`),
ADD CONSTRAINT `orders_quote_fk` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`),
ADD CONSTRAINT `orders_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `order_items`
ADD CONSTRAINT `order_items_order_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `order_items_category_fk` FOREIGN KEY (`category_id`) REFERENCES `item_category` (`id`),
ADD CONSTRAINT `order_items_item_fk` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`);

ALTER TABLE `deliveries`
ADD CONSTRAINT `deliveries_order_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
ADD CONSTRAINT `deliveries_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `clients` (`id`),
ADD CONSTRAINT `deliveries_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `delivery_items`
ADD CONSTRAINT `delivery_items_delivery_fk` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `delivery_items_order_item_fk` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`),
ADD CONSTRAINT `delivery_items_category_fk` FOREIGN KEY (`category_id`) REFERENCES `item_category` (`id`),
ADD CONSTRAINT `delivery_items_item_fk` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`);

ALTER TABLE `invoices`
ADD CONSTRAINT `invoices_order_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
ADD CONSTRAINT `invoices_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `clients` (`id`),
ADD CONSTRAINT `invoices_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `invoice_items`
ADD CONSTRAINT `invoice_items_invoice_fk` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `invoice_items_order_item_fk` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`),
ADD CONSTRAINT `invoice_items_category_fk` FOREIGN KEY (`category_id`) REFERENCES `item_category` (`id`),
ADD CONSTRAINT `invoice_items_item_fk` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`);

ALTER TABLE `payments`
ADD CONSTRAINT `payments_invoice_fk` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
ADD CONSTRAINT `payments_recorded_by_fk` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);


CREATE TABLE `stock_removals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference` varchar(50) DEFAULT NULL,
  `origin` varchar(255) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `grand_total` decimal(12,2) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL COMMENT 'ID de l\'utilisateur qui a créé l\'entrée',
  `updated_by` int(11) DEFAULT NULL COMMENT 'ID de l\'utilisateur qui a modifié l\'entrée',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Date de suppression si soft delete',
  `deleted_by` int(11) DEFAULT NULL COMMENT 'ID de l\'utilisateur qui a supprimé l\'entrée',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des articles des sorties de stock
CREATE TABLE `stock_removal_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_removal_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `line_total` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL COMMENT 'ID de l\'utilisateur qui a créé la ligne',
  `updated_by` int(11) DEFAULT NULL COMMENT 'ID de l\'utilisateur qui a modifié la ligne',
  PRIMARY KEY (`id`),
  KEY `stock_removal_id` (`stock_removal_id`),
  KEY `category_id` (`category_id`),
  KEY `item_id` (`item_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `stock_removal_items_ibfk_1` FOREIGN KEY (`stock_removal_id`) REFERENCES `stock_removals` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `stock_removals`
ADD CONSTRAINT `stock_removals_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
ADD CONSTRAINT `stock_removals_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

ALTER TABLE `stock_removal_items`
ADD CONSTRAINT `stock_removal_items_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);
