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



INSERT INTO notification_setting (
    id, 
    type, 
    is_mail, 
    is_sms, 
    is_notification, 
    display_notification, 
    display_sms, 
    subject, 
    template_id, 
    template, 
    variables, 
    created_at
) VALUES (
    13,  -- ou l'ID suivant dans votre séquence
    'send_quote', 
    1,  -- is_mail
    0,  -- is_sms
    0,  -- is_notification
    0,  -- display_notification
    0,  -- display_sms
    'Votre devis {{quotation_number}}', 
    '', 
    'Bonjour {{client_name}},<br>Vous trouverez ci-joint notre devis n°{{quotation_number}} du {{quotation_date}}.<br>Montant total : {{total_amount}} {{currency}}<br>Validité : {{validity_days}} jours.<br>Pour toute question, n''hésitez pas à nous contacter.<br>Cordialement,<br>{{company_name}}<br>{{company_phone}}', 
    '{{client_name}} {{quotation_number}} {{quotation_date}} {{total_amount}} {{currency}} {{validity_days}} {{company_name}} {{company_phone}}', 
    NOW()
);


-- Template pour l'envoi de facture
INSERT INTO notification_setting (
    id, 
    type, 
    is_mail, 
    is_sms, 
    is_notification, 
    display_notification, 
    display_sms, 
    subject, 
    template_id, 
    template, 
    variables, 
    created_at
) VALUES (
    14,  -- ID suivant dans la séquence
    'send_invoice', 
    1,  -- is_mail
    0,  -- is_sms
    0,  -- is_notification
    0,  -- display_notification
    0,  -- display_sms
    'Votre facture {{invoice_number}}', 
    '', 
    'Bonjour {{client_name}},<br>Vous trouverez ci-joint notre facture n°{{invoice_number}} du {{invoice_date}}.<br>Montant total : {{total_amount}} {{currency}}<br>Date d''échéance : {{due_date}}<br>Pour toute question, n''hésitez pas à nous contacter.<br>Cordialement,<br>{{company_name}}<br>{{company_phone}}', 
    '{{client_name}} {{invoice_number}} {{invoice_date}} {{total_amount}} {{currency}} {{due_date}} {{company_name}} {{company_phone}}', 
    NOW()
);

-- Template pour l'envoi de bon de livraison
INSERT INTO notification_setting (
    id, 
    type, 
    is_mail, 
    is_sms, 
    is_notification, 
    display_notification, 
    display_sms, 
    subject, 
    template_id, 
    template, 
    variables, 
    created_at
) VALUES (
    15,  -- ID suivant dans la séquence
    'send_delivery', 
    1,  -- is_mail
    0,  -- is_sms
    0,  -- is_notification
    0,  -- display_notification
    0,  -- display_sms
    'Votre bon de livraison {{delivery_number}}', 
    '', 
    'Bonjour {{client_name}},<br>Vous trouverez ci-joint le bon de livraison n°{{delivery_number}} du {{delivery_date}}.<br>Détail de la livraison :<br>{{delivery_details}}<br>Pour toute question, n''hésitez pas à nous contacter.<br>Cordialement,<br>{{company_name}}<br>{{company_phone}}', 
    '{{client_name}} {{delivery_number}} {{delivery_date}} {{delivery_details}} {{company_name}} {{company_phone}}', 
    NOW()
);


CREATE TABLE IF NOT EXISTS `quotes_nostock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_number` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `quote_date` date NULL,
  `valid_until` date DEFAULT NULL,
  `payment_terms` text DEFAULT NULL,
  `delivery_terms` text DEFAULT NULL,
  `delivery_location` varchar(255) DEFAULT NULL,
  `apply_tva` tinyint(1) NULL DEFAULT 0,
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


INSERT INTO notification_setting (
    id, 
    type, 
    is_mail, 
    is_sms, 
    is_notification, 
    display_notification, 
    display_sms, 
    subject, 
    template_id, 
    template, 
    variables, 
    created_at
) VALUES (
    16,  -- ou l'ID suivant dans votre séquence
    'send_quote_no_stock', 
    1,  -- is_mail
    0,  -- is_sms
    0,  -- is_notification
    0,  -- display_notification
    0,  -- display_sms
    'Votre devis {{quotation_number}}', 
    '', 
    'Bonjour {{cli  ent_name}},<br>Vous trouverez ci-joint notre devis n°{{quotation_number}} du {{quotation_date}}.<br>Montant total : {{total_amount}} {{currency}}<br>Validité : {{validity_days}} jours.<br>Pour toute question, n''hésitez pas à nous contacter.<br>Cordialement,<br>{{company_name}}<br>{{company_phone}}',
    '{{client_name}} {{quotation_number}} {{quotation_date}} {{total_amount}} {{currency}} {{validity_days}} {{company_name}} {{company_phone}}', 
    NOW()
);





/*07032026*/
  -- Ajouter les colonnes FNE à la table invoices
ALTER TABLE invoices
ADD COLUMN fne_certified TINYINT(1) DEFAULT 0 AFTER updated_at,
ADD COLUMN fne_reference VARCHAR(100) NULL AFTER fne_certified,
ADD COLUMN fne_token TEXT NULL AFTER fne_reference,
ADD COLUMN fne_balance_sticker INT DEFAULT 0 AFTER fne_token,
ADD COLUMN fne_certified_at DATETIME NULL AFTER fne_balance_sticker,
ADD COLUMN fne_response_data TEXT NULL AFTER fne_certified_at,
ADD COLUMN paid_at DATETIME NULL AFTER fne_response_data;

-- Table pour les paramètres FNE
CREATE TABLE IF NOT EXISTS fne_settings (
    id INT(11) NOT NULL AUTO_INCREMENT,
    api_key VARCHAR(255) NOT NULL,
    test_url VARCHAR(255) DEFAULT 'http://54.247.95.108/ws/external/invoices/sign',
    prod_url VARCHAR(255) NULL,
    environment ENUM('test', 'prod') DEFAULT 'test',
    point_of_sale VARCHAR(100) DEFAULT 'PDV001',
    establishment VARCHAR(100) DEFAULT 'ETABLISSEMENT001',
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insérer les paramètres par défaut
INSERT INTO fne_settings (api_key, created_at)
VALUES ('toCgyP5vdqXavkY16dg5qn7eae3N8bjZ', NOW());

-- Table pour l'historique des certifications FNE
CREATE TABLE IF NOT EXISTS fne_certification_log (
    id INT(11) NOT NULL AUTO_INCREMENT,
    invoice_id INT(11) NOT NULL,
    request_data TEXT NULL,
    response_data TEXT NULL,
    http_code INT(3) NULL,
    status VARCHAR(50) NULL,
    error_message TEXT NULL,
    created_at DATETIME NULL,
    PRIMARY KEY (id),
    KEY idx_invoice_id (invoice_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE job_applications (
    id INT(11) NOT NULL AUTO_INCREMENT,
    job_id INT(11) NOT NULL,
    job_name VARCHAR(255) NOT NULL,
    candidate_name VARCHAR(255) NOT NULL,
    candidate_email VARCHAR(255) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    cover_letter TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id)
);

ALTER TABLE job_applications
ADD CONSTRAINT fk_job_application
FOREIGN KEY (job_id) REFERENCES job_offers(id)
ON DELETE CASCADE;

-- Table des objectifs annuels (saisie par le directeur)
CREATE TABLE `annual_objectives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` decimal(15,2) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table des attributions aux commerciaux (par responsable commercial)
CREATE TABLE `objective_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `annual_objective_id` int(11) NOT NULL,
  `commercial_name` varchar(255) NOT NULL,   -- ou commercial_id si table staff
  `amount` decimal(15,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_annual` (`annual_objective_id`),
  CONSTRAINT `fk_annual` FOREIGN KEY (`annual_objective_id`) REFERENCES `annual_objectives` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `duration` varchar(100) DEFAULT NULL COMMENT 'ex: 2 jours, 10 heures',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `quote_items`
ADD COLUMN `item_type` ENUM('product','service') NOT NULL DEFAULT 'product' AFTER `item_id`;

ALTER TABLE `quote_items`
ADD COLUMN `service_id` int(11) DEFAULT NULL AFTER `item_id`,
ADD COLUMN `item_type` enum('product','service') NOT NULL DEFAULT 'product',
ADD FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE SET NULL;

INSERT INTO `permission_group` (`name`, `short_code`, `created_at`) VALUES
('services', 'services', NOW());



//

ALTER TABLE `order_items`
ADD COLUMN `service_id` int(11) DEFAULT NULL AFTER `item_id`,
ADD COLUMN `item_type` enum('product','service') NOT NULL DEFAULT 'product' AFTER `service_id`;


ALTER TABLE `delivery_items`
ADD COLUMN `service_id` int(11) DEFAULT NULL,
ADD COLUMN `item_type` enum('product','service') DEFAULT 'product';


ALTER TABLE `stock_removal_items`
ADD COLUMN `item_type` enum('product','service') DEFAULT 'product';

ALTER TABLE `invoice_items`
ADD COLUMN `service_id` int(11) DEFAULT NULL,
ADD COLUMN `item_type` enum('product','service') NOT NULL DEFAULT 'product';

//

ALTER TABLE `stock` ADD `real_quantity` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `current_quantity`;

//09062026
ALTER TABLE `permission_category` ADD `enable_validate` TINYINT(1) NOT NULL DEFAULT '0' AFTER `enable_delete`;

ALTER TABLE `roles_permissions` ADD `can_validate` TINYINT(1) NOT NULL DEFAULT '0' AFTER `can_delete`;

//11062026

ALTER TABLE `item` ADD `real_quantity` INT(11) NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `stock_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL, -- 'stock_entry', 'stock_exit', 'stock_reel_update', 'threshold_update'
  `quantity_change` decimal(10,2) DEFAULT NULL,
  `old_value` decimal(10,2) DEFAULT NULL,  -- ancienne valeur (stock, seuil, etc.)
  `new_value` decimal(10,2) DEFAULT NULL,  -- nouvelle valeur
  `field_name` varchar(50) DEFAULT NULL,   -- 'current_quantity', 'real_quantity', 'stock_threshold'
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SELECT * FROM stock_audit LIMIT 5;

INSERT INTO `leave_types` (`type`, `ndays`, `is_active`) VALUES ('Congé annuel', 0, 'yes');

CREATE TABLE `ia_conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'ID de l''employé qui interroge',
  `session_id` varchar(255) DEFAULT NULL COMMENT 'Identifiant de session (pour suivre une conversation)',
  `question` text NOT NULL COMMENT 'Question posée par l''employé',
  `response` text COMMENT 'Réponse générée par l''IA',
  `context` text COMMENT 'Contexte utilisé (documents, requêtes SQL, etc.)',
  `model_used` varchar(100) DEFAULT NULL COMMENT 'Modèle IA utilisé (ex: gpt-4, claude-3)',
  `tokens_used` int(11) DEFAULT NULL COMMENT 'Nombre de tokens consommés',
  `response_time` float DEFAULT NULL COMMENT 'Temps de réponse en secondes',
  `status` enum('success','error','pending') DEFAULT 'pending',
  `error_message` text COMMENT 'Message d''erreur si échec',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `session_id` (`session_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

//200726
-- Générer toutes les commandes ALTER TABLE
SELECT CONCAT('ALTER TABLE `', TABLE_NAME, '` ADD COLUMN `entreprise_id` INT NULL DEFAULT 1 AFTER `id`;') as sql_command
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME NOT IN (
    'compte_entreprise', 'migrations', 'sessions', 'captcha',
    'languages', 'roles', 'sch_settings', 'staff'
)
AND TABLE_NAME NOT LIKE '%_backup%'
AND TABLE_NAME NOT LIKE '%_tmp%';



-- Vérifier si la colonne existe
SHOW COLUMNS FROM `sch_settings` LIKE 'entreprise_id';

-- Si elle n'existe pas, l'ajouter
ALTER TABLE `sch_settings` ADD COLUMN `entreprise_id` INT NULL AFTER `id`;
UPDATE `sch_settings` SET `entreprise_id` = 1 WHERE `entreprise_id` IS NULL;


-- Ajouter les nouvelles colonnes à la table visitors_book
ALTER TABLE `visitors_book`
ADD COLUMN `firstname` VARCHAR(100) NULL DEFAULT NULL AFTER `name`,
ADD COLUMN `organisation` VARCHAR(200) NULL DEFAULT NULL AFTER `email`,
ADD COLUMN `function` VARCHAR(150) NULL DEFAULT NULL AFTER `organisation`,
ADD COLUMN `id_type` VARCHAR(50) NULL DEFAULT NULL AFTER `id_proof`,
ADD COLUMN `access_level` VARCHAR(100) NULL DEFAULT NULL AFTER `id_type`,
ADD COLUMN `badge` VARCHAR(50) NULL DEFAULT NULL AFTER `access_level`;


-- Corriger les données existantes
UPDATE general_calls SET call_type = 'Incoming' WHERE call_type = '1' OR call_type = 'entrant' OR call_type = 'Entrant';
UPDATE general_calls SET call_type = 'Outgoing' WHERE call_type = '2' OR call_type = 'sortant' OR call_type = 'Sortant';
UPDATE general_calls SET call_type = 'Missed' WHERE call_type = '3' OR call_type = 'manqué' OR call_type = 'Manqué';

-- Vérifier les données
SELECT call_type, COUNT(*) as total FROM general_calls WHERE deleted = 0 GROUP BY call_type;


CREATE TABLE `couriers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `courier_type` varchar(100) NOT NULL COMMENT 'Type de courrier (Reçu, Envoyé, Interne)',
  `sender_name` varchar(100) NOT NULL COMMENT 'Nom de l\'expéditeur/destinataire',
  `reference` varchar(50) DEFAULT NULL COMMENT 'Numéro de référence',
  `address` varchar(255) DEFAULT NULL COMMENT 'Adresse',
  `date_received` date DEFAULT NULL COMMENT 'Date de réception/envoi',
  `description` text COMMENT 'Description du courrier',
  `note` text COMMENT 'Notes supplémentaires',
  `status` enum('pending','processed','archived') DEFAULT 'pending' COMMENT 'Statut du courrier',
  `attachment` varchar(100) DEFAULT NULL COMMENT 'Nom du fichier attaché',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` tinyint(1) DEFAULT '0' COMMENT '0=actif, 1=supprimé',
  PRIMARY KEY (`id`),
  KEY `idx_courier_type` (`courier_type`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`date_received`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;



CREATE TABLE `demandes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `categorie` varchar(100) NOT NULL,
  `priorite` varchar(50) NOT NULL DEFAULT 'normale',
  `description` text NOT NULL,
  `statut` varchar(50) NOT NULL DEFAULT 'en_attente',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


CREATE TABLE `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `categorie` varchar(100) NOT NULL,
  `description` text,
  `fichier` varchar(255) DEFAULT NULL,
  `type_fichier` varchar(50) DEFAULT NULL,
  `taille` varchar(50) DEFAULT NULL,
  `statut` enum('actif','archive','supprime') DEFAULT 'actif',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_categorie` (`categorie`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


CREATE TABLE `rendez_vous` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `description` text,
  `date_rendez_vous` date NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `participants` text,
  `couleur` varchar(20) DEFAULT '#3b82f6',
  `statut` enum('planifie','en_cours','termine','annule') DEFAULT 'planifie',
  `rappel` tinyint(1) DEFAULT '0',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_date` (`date_rendez_vous`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
' ||
 //230726

CREATE TABLE `reunions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `description` text,
  `date_reunion` date NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `participants` text,
  `ordre_du_jour` text,
  `compte_rendu` text,
  `couleur` varchar(20) DEFAULT '#8b5cf6',
  `statut` enum('planifiee','en_cours','terminee','annulee') DEFAULT 'planifiee',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_date` (`date_reunion`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


CREATE TABLE `rapports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `type_rapport` varchar(100) NOT NULL,
  `description` text,
  `fichier` varchar(255) DEFAULT NULL,
  `type_fichier` varchar(50) DEFAULT NULL,
  `taille` varchar(50) DEFAULT NULL,
  `periode_debut` date DEFAULT NULL,
  `periode_fin` date DEFAULT NULL,
  `statut` enum('en_attente','en_cours','termine','archive') DEFAULT 'en_attente',
  `priorite` enum('basse','normale','haute','urgente') DEFAULT 'normale',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type_rapport`),
  KEY `idx_statut` (`statut`),
  KEY `idx_periode` (`periode_debut`, `periode_fin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


CREATE TABLE `immobilisations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text,
  `categorie` varchar(100) NOT NULL,
  `type_immobilisation` enum('corporelle','incorporelle','financiere') DEFAULT 'corporelle',
  `date_acquisition` date NOT NULL,
  `date_mise_en_service` date DEFAULT NULL,
  `valeur_originale` decimal(15,2) NOT NULL,
  `valeur_residuelle` decimal(15,2) DEFAULT '0.00',
  `duree_amortissement` int(11) DEFAULT NULL,
  `taux_amortissement` decimal(5,2) DEFAULT NULL,
  `mode_amortissement` enum('lineaire','degresif','variable') DEFAULT 'lineaire',
  `amortissement_cumule` decimal(15,2) DEFAULT '0.00',
  `valeur_nette` decimal(15,2) DEFAULT '0.00',
  `statut` enum('actif','amorti','ceder','sortie') DEFAULT 'actif',
  `fournisseur_id` int(11) DEFAULT NULL,
  `num_facture` varchar(50) DEFAULT NULL,
  `num_serie` varchar(100) DEFAULT NULL,
  `localisation` varchar(255) DEFAULT NULL,
  `responsable` varchar(100) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code`),
  KEY `idx_categorie` (`categorie`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table des amortissements
CREATE TABLE `amortissements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `immobilisation_id` int(11) NOT NULL,
  `periode_debut` date NOT NULL,
  `periode_fin` date NOT NULL,
  `montant` decimal(15,2) NOT NULL,
  `type` enum('previsionnel','effectif') DEFAULT 'effectif',
  `description` text,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_immobilisation` (`immobilisation_id`),
  FOREIGN KEY (`immobilisation_id`) REFERENCES `immobilisations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table des cessions
CREATE TABLE `cessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `immobilisation_id` int(11) NOT NULL,
  `date_cession` date NOT NULL,
  `montant_cession` decimal(15,2) NOT NULL,
  `acheteur` varchar(255) NOT NULL,
  `motif` text,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_immobilisation` (`immobilisation_id`),
  FOREIGN KEY (`immobilisation_id`) REFERENCES `immobilisations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


-- Table des immobilisations
CREATE TABLE IF NOT EXISTS `immobilisations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text,
  `categorie` varchar(100) NOT NULL,
  `type_immobilisation` enum('corporelle','incorporelle','financiere') DEFAULT 'corporelle',
  `date_acquisition` date NOT NULL,
  `date_mise_en_service` date DEFAULT NULL,
  `valeur_originale` decimal(15,2) NOT NULL,
  `valeur_residuelle` decimal(15,2) DEFAULT '0.00',
  `duree_amortissement` int(11) DEFAULT NULL,
  `taux_amortissement` decimal(5,2) DEFAULT NULL,
  `mode_amortissement` enum('lineaire','degresif','variable') DEFAULT 'lineaire',
  `amortissement_cumule` decimal(15,2) DEFAULT '0.00',
  `valeur_nette` decimal(15,2) DEFAULT '0.00',
  `statut` enum('actif','amorti','ceder','sortie') DEFAULT 'actif',
  `fournisseur_id` int(11) DEFAULT NULL,
  `num_facture` varchar(50) DEFAULT NULL,
  `num_serie` varchar(100) DEFAULT NULL,
  `localisation` varchar(255) DEFAULT NULL,
  `responsable` varchar(100) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code`),
  KEY `idx_categorie` (`categorie`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table des amortissements
CREATE TABLE IF NOT EXISTS `amortissements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `immobilisation_id` int(11) NOT NULL,
  `periode_debut` date NOT NULL,
  `periode_fin` date NOT NULL,
  `montant` decimal(15,2) NOT NULL,
  `type` enum('previsionnel','effectif') DEFAULT 'effectif',
  `description` text,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_immobilisation` (`immobilisation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Table des cessions
CREATE TABLE IF NOT EXISTS `cessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `immobilisation_id` int(11) NOT NULL,
  `date_cession` date NOT NULL,
  `montant_cession` decimal(15,2) NOT NULL,
  `acheteur` varchar(255) NOT NULL,
  `motif` text,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_immobilisation` (`immobilisation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Insérer quelques données de test
INSERT INTO `immobilisations` (`code`, `nom`, `description`, `categorie`, `date_acquisition`, `valeur_originale`, `duree_amortissement`, `statut`) VALUES
('IMM-2026-0001', 'Ordinateur Dell XPS', 'Ordinateur portable pour direction', 'Informatique', '2026-01-15', 1500000, 3, 'actif'),
('IMM-2026-0002', 'Véhicule Toyota', 'Véhicule de service', 'Véhicule', '2026-02-20', 8500000, 5, 'actif');

//pas
CREATE TABLE `rapports_cultes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_culte` date NOT NULL,
  `type_culte` enum('matin','soir','jeunesse','enfants','autre') DEFAULT 'matin',
  `theme` varchar(255) NOT NULL,
  `predicateur` varchar(100) NOT NULL,
  `passage_biblique` varchar(255) DEFAULT NULL,
  `nombre_hommes` int(11) DEFAULT '0',
  `nombre_femmes` int(11) DEFAULT '0',
  `nombre_enfants` int(11) DEFAULT '0',
  `nombre_visiteurs` int(11) DEFAULT '0',
  `total_personnes` int(11) DEFAULT '0',
  `offrande` decimal(15,2) DEFAULT '0.00',
  `dime` decimal(15,2) DEFAULT '0.00',
  `actions_de_grace` decimal(15,2) DEFAULT '0.00',
  `autres_offrandes` decimal(15,2) DEFAULT '0.00',
  `total_finances` decimal(15,2) DEFAULT '0.00',
  `premiere_communion` int(11) DEFAULT '0',
  `baptemes` int(11) DEFAULT '0',
  `mariages` int(11) DEFAULT '0',
  `funerailles` int(11) DEFAULT '0',
  `priere_malades` int(11) DEFAULT '0',
  `nouvelles_conversions` int(11) DEFAULT '0',
  `rencontres_maison` int(11) DEFAULT '0',
  `visites_malades` int(11) DEFAULT '0',
  `remarques` text,
  `responsable_culte` varchar(100) DEFAULT NULL,
  `statut` enum('brouillon','valide','archive') DEFAULT 'brouillon',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_date` (`date_culte`),
  KEY `idx_type` (`type_culte`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


CREATE TABLE `membres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_membre` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `sexe` enum('M','F') DEFAULT 'M',
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(150) DEFAULT NULL,
  `nationalite` varchar(50) DEFAULT NULL,
  `profession` varchar(100) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `date_bapteme` date DEFAULT NULL,
  `date_affiliation` date DEFAULT NULL,
  `statut_membre` enum('actif','inactif','transfert','decede') DEFAULT 'actif',
  `role` enum('membre','diacre','ancien','pasteur','evangeliste','autre') DEFAULT 'membre',
  `departement` varchar(100) DEFAULT NULL,
  `groupe_cellule` varchar(100) DEFAULT NULL,
  `nom_conjoint` varchar(100) DEFAULT NULL,
  `nombre_enfants` int(11) DEFAULT '0',
  `photo` varchar(255) DEFAULT NULL,
  `remarques` text,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code_membre`),
  KEY `idx_statut` (`statut_membre`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


CREATE TABLE `groupes_cellules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `type` enum('groupe','cellule') DEFAULT 'cellule',
  `description` text,
  `responsable` varchar(100) DEFAULT NULL,
  `jour_reunion` varchar(20) DEFAULT NULL,
  `heure_reunion` time DEFAULT NULL,
  `lieu_reunion` varchar(255) DEFAULT NULL,
  `quartier` varchar(100) DEFAULT NULL,
  `nombre_membres` int(11) DEFAULT '0',
  `membres` text,
  `statut` enum('actif','inactif','suspendu') DEFAULT 'actif',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


CREATE TABLE `evenements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `description` text,
  `type_evenement` enum('culte','conférence','séminaire','formation','concert','réveil','jeunesse','enfants','autre') DEFAULT 'culte',
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `organisateur` varchar(100) DEFAULT NULL,
  `contact_organisateur` varchar(20) DEFAULT NULL,
  `email_organisateur` varchar(100) DEFAULT NULL,
  `nombre_participants` int(11) DEFAULT '0',
  `participants` text,
  `couleur` varchar(20) DEFAULT '#3b82f6',
  `image` varchar(255) DEFAULT NULL,
  `statut` enum('planifie','en_cours','termine','annule') DEFAULT 'planifie',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type_evenement`),
  KEY `idx_date` (`date_debut`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


CREATE TABLE `offrandes_dimes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_transaction` varchar(50) NOT NULL,
  `type` enum('offrande','dime','action_de_grace','autre') DEFAULT 'offrande',
  `categorie` varchar(100) DEFAULT NULL,
  `montant` decimal(15,2) NOT NULL,
  `date_transaction` date NOT NULL,
  `membre_id` int(11) DEFAULT NULL,
  `membre_nom` varchar(100) DEFAULT NULL,
  `membre_code` varchar(50) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mode_paiement` enum('especes','cheque','virement','mobile_money','carte') DEFAULT 'especes',
  `reference_paiement` varchar(100) DEFAULT NULL,
  `description` text,
  `reçu` tinyint(1) DEFAULT '0',
  `statut` enum('valide','en_attente','annule') DEFAULT 'valide',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code_transaction`),
  KEY `idx_type` (`type`),
  KEY `idx_date` (`date_transaction`),
  KEY `idx_statut` (`statut`),
  KEY `idx_membre` (`membre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


CREATE TABLE `baptemes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_bapteme` varchar(50) NOT NULL,
  `membre_id` int(11) DEFAULT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `sexe` enum('M','F') DEFAULT 'M',
  `date_naissance` date DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `date_bapteme` date NOT NULL,
  `heure_bapteme` time DEFAULT NULL,
  `lieu_bapteme` varchar(255) DEFAULT NULL,
  `pasteur_officiant` varchar(100) DEFAULT NULL,
  `type_bapteme` enum('adulte','enfant','immersion','aspersion') DEFAULT 'immersion',
  `nombre_participants` int(11) DEFAULT '0',
  `participants` text,
  `temoignage` text,
  `parrains` varchar(255) DEFAULT NULL,
  `marraines` varchar(255) DEFAULT NULL,
  `certificat_genere` tinyint(1) DEFAULT '0',
  `statut` enum('planifie','effectue','annule','reporte') DEFAULT 'planifie',
  `couleur` varchar(20) DEFAULT '#3b82f6',
  `observations` text,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code_bapteme`),
  KEY `idx_date` (`date_bapteme`),
  KEY `idx_statut` (`statut`),
  KEY `idx_membre` (`membre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


CREATE TABLE `mariages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_mariage` varchar(50) NOT NULL,
  `mari_id` int(11) DEFAULT NULL,
  `mari_nom` varchar(100) NOT NULL,
  `mari_prenom` varchar(100) NOT NULL,
  `mari_date_naissance` date DEFAULT NULL,
  `mari_telephone` varchar(20) DEFAULT NULL,
  `mari_email` varchar(100) DEFAULT NULL,
  `mari_profession` varchar(100) DEFAULT NULL,
  `femme_id` int(11) DEFAULT NULL,
  `femme_nom` varchar(100) NOT NULL,
  `femme_prenom` varchar(100) NOT NULL,
  `femme_date_naissance` date DEFAULT NULL,
  `femme_telephone` varchar(20) DEFAULT NULL,
  `femme_email` varchar(100) DEFAULT NULL,
  `femme_profession` varchar(100) DEFAULT NULL,
  `date_mariage` date NOT NULL,
  `heure_mariage` time DEFAULT NULL,
  `lieu_mariage` varchar(255) DEFAULT NULL,
  `pasteur_officiant` varchar(100) DEFAULT NULL,
  `type_mariage` enum('civil','religieux','traditionnel','mixte') DEFAULT 'religieux',
  `nombre_invites` int(11) DEFAULT '0',
  `invites` text,
  `temoins_mari` varchar(255) DEFAULT NULL,
  `temoins_femme` varchar(255) DEFAULT NULL,
  `statut` enum('planifie','effectue','annule','reporte') DEFAULT 'planifie',
  `couleur` varchar(20) DEFAULT '#8b5cf6',
  `observations` text,
  `certificat_genere` tinyint(1) DEFAULT '0',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code_mariage`),
  KEY `idx_date` (`date_mariage`),
  KEY `idx_statut` (`statut`),
  KEY `idx_mari` (`mari_id`),
  KEY `idx_femme` (`femme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


CREATE TABLE `funerailles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_funerailles` varchar(50) NOT NULL,
  `defunt_id` int(11) DEFAULT NULL,
  `defunt_nom` varchar(100) NOT NULL,
  `defunt_prenom` varchar(100) NOT NULL,
  `defunt_sexe` enum('M','F') DEFAULT 'M',
  `defunt_date_naissance` date DEFAULT NULL,
  `defunt_date_deces` date NOT NULL,
  `defunt_telephone` varchar(20) DEFAULT NULL,
  `defunt_email` varchar(100) DEFAULT NULL,
  `defunt_adresse` varchar(255) DEFAULT NULL,
  `defunt_profession` varchar(100) DEFAULT NULL,
  `defunt_photo` varchar(255) DEFAULT NULL,
  `date_funerailles` date NOT NULL,
  `heure_funerailles` time DEFAULT NULL,
  `lieu_funerailles` varchar(255) DEFAULT NULL,
  `pasteur_officiant` varchar(100) DEFAULT NULL,
  `type_ceremonie` enum('enterrement','incineration','depot_urne','autre') DEFAULT 'enterrement',
  `nombre_participants` int(11) DEFAULT '0',
  `participants` text,
  `famille_proche` text,
  `conjoint` varchar(100) DEFAULT NULL,
  `enfants` text,
  `observations` text,
  `statut` enum('planifie','effectue','annule','reporte') DEFAULT 'planifie',
  `couleur` varchar(20) DEFAULT '#6b7280',
  `certificat_genere` tinyint(1) DEFAULT '0',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code_funerailles`),
  KEY `idx_date` (`date_funerailles`),
  KEY `idx_statut` (`statut`),
  KEY `idx_defunt` (`defunt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


-- Création de la base de données
CREATE DATABASE IF NOT EXISTS `tontine_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `tontine_db`;

-- Table des groupes
CREATE TABLE IF NOT EXISTS `tontine_groupes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `created_at` datetime NOT NULL,
    `deleted` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insertion des groupes par défaut
INSERT INTO `tontine_groupes` (`nom`, `description`, `created_at`) VALUES
('Groupe A', 'Groupe A - Tontine mensuelle', NOW()),
('Groupe B', 'Groupe B - Tontine hebdomadaire', NOW()),
('Groupe C', 'Groupe C - Tontine trimestrielle', NOW());

-- Table des membres
CREATE TABLE IF NOT EXISTS `tontine_membres` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(100) NOT NULL,
    `prenom` varchar(100) NOT NULL,
    `telephone` varchar(20) NOT NULL,
    `email` varchar(100) DEFAULT NULL,
    `adresse` text DEFAULT NULL,
    `profession` varchar(100) DEFAULT NULL,
    `groupe_id` int(11) DEFAULT NULL,
    `date_adhesion` date NOT NULL,
    `statut` enum('actif','inactif','suspendu') DEFAULT 'actif',
    `created_at` datetime NOT NULL,
    `updated_at` datetime DEFAULT NULL,
    `deleted` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `telephone` (`telephone`),
    KEY `groupe_id` (`groupe_id`),
    CONSTRAINT `tontine_membres_ibfk_1` FOREIGN KEY (`groupe_id`) REFERENCES `tontine_groupes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insertion des membres de test
INSERT INTO `tontine_membres` (`nom`, `prenom`, `telephone`, `email`, `profession`, `groupe_id`, `date_adhesion`, `statut`, `created_at`) VALUES
('Dupont', 'Jean', '771234567', 'jean.dupont@email.com', 'Commerçant', 1, CURDATE(), 'actif', NOW()),
('Martin', 'Marie', '778765432', 'marie.martin@email.com', 'Enseignante', 1, CURDATE(), 'actif', NOW()),
('Diop', 'Amadou', '779998877', 'amadou.diop@email.com', 'Informaticien', 2, CURDATE(), 'actif', NOW());

-- Table des cotisations
CREATE TABLE IF NOT EXISTS `tontine_cotisations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `membre_id` int(11) NOT NULL,
    `cycle_id` int(11) DEFAULT NULL,
    `montant` decimal(15,2) NOT NULL,
    `date_paiement` date NOT NULL,
    `statut` enum('paye','en_attente','annule') DEFAULT 'en_attente',
    `mode_paiement` enum('especes','mobile_money','virement','cheque') DEFAULT 'especes',
    `reference` varchar(50) DEFAULT NULL,
    `created_at` datetime NOT NULL,
    `deleted` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `membre_id` (`membre_id`),
    CONSTRAINT `tontine_cotisations_ibfk_1` FOREIGN KEY (`membre_id`) REFERENCES `tontine_membres` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table des cycles
CREATE TABLE IF NOT EXISTS `tontine_cycles` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(100) NOT NULL,
    `type` enum('quotidien','hebdomadaire','mensuel','trimestriel','annuel') NOT NULL,
    `montant` decimal(15,2) NOT NULL,
    `date_debut` date NOT NULL,
    `date_fin` date DEFAULT NULL,
    `statut` enum('en_attente','en_cours','termine','annule') DEFAULT 'en_attente',
    `max_membres` int(11) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `created_at` datetime NOT NULL,
    `deleted` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table des collectes
CREATE TABLE IF NOT EXISTS `tontine_collectes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `cycle_id` int(11) NOT NULL,
    `membre_id` int(11) NOT NULL,
    `montant` decimal(15,2) NOT NULL,
    `date_collecte` date NOT NULL,
    `statut` enum('effectue','en_attente','annule') DEFAULT 'en_attente',
    `created_at` datetime NOT NULL,
    `deleted` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `cycle_id` (`cycle_id`),
    KEY `membre_id` (`membre_id`),
    CONSTRAINT `tontine_collectes_ibfk_1` FOREIGN KEY (`cycle_id`) REFERENCES `tontine_cycles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `tontine_collectes_ibfk_2` FOREIGN KEY (`membre_id`) REFERENCES `tontine_membres` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


//240726
-- ============================================================
-- TABLE : tickets_categories
-- ============================================================
CREATE TABLE IF NOT EXISTS `tickets_categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `couleur` varchar(20) DEFAULT '#3b82f6',
    `icon` varchar(50) DEFAULT 'fa-ticket',
    `created_at` datetime NOT NULL,
    `deleted` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insertion des catégories par défaut
INSERT INTO `tickets_categories` (`nom`, `description`, `couleur`, `icon`, `created_at`) VALUES
('Informatique', 'Problèmes techniques et informatiques', '#3b82f6', 'fa-desktop', NOW()),
('RH', 'Questions relatives aux ressources humaines', '#10b981', 'fa-users', NOW()),
('Administratif', 'Questions administratives', '#f59e0b', 'fa-building', NOW()),
('Financier', 'Questions financières et comptables', '#8b5cf6', 'fa-money', NOW()),
('Logistique', 'Questions logistiques', '#06b6d4', 'fa-truck', NOW());

-- ============================================================
-- TABLE : tickets_priorites
-- ============================================================
CREATE TABLE IF NOT EXISTS `tickets_priorites` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(50) NOT NULL,
    `niveau` int(11) DEFAULT '1',
    `couleur` varchar(20) DEFAULT '#94a3b8',
    `created_at` datetime NOT NULL,
    `deleted` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tickets_priorites` (`nom`, `niveau`, `couleur`, `created_at`) VALUES
('Basse', 1, '#94a3b8', NOW()),
('Moyenne', 2, '#3b82f6', NOW()),
('Haute', 3, '#f59e0b', NOW()),
('Urgente', 4, '#ef4444', NOW());

-- ============================================================
-- TABLE : tickets_statuts
-- ============================================================
CREATE TABLE IF NOT EXISTS `tickets_statuts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(50) NOT NULL,
    `couleur` varchar(20) DEFAULT '#94a3b8',
    `created_at` datetime NOT NULL,
    `deleted` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tickets_statuts` (`nom`, `couleur`, `created_at`) VALUES
('Ouvert', '#3b82f6', NOW()),
('En cours', '#f59e0b', NOW()),
('En attente', '#8b5cf6', NOW()),
('Résolu', '#10b981', NOW()),
('Fermé', '#94a3b8', NOW());

-- ============================================================
-- TABLE : tickets
-- ============================================================
CREATE TABLE IF NOT EXISTS `tickets` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ticket_number` varchar(50) NOT NULL,
    `titre` varchar(200) NOT NULL,
    `description` text NOT NULL,
    `categorie_id` int(11) NOT NULL,
    `priorite_id` int(11) NOT NULL,
    `statut_id` int(11) NOT NULL DEFAULT '1',
    `created_by` int(11) NOT NULL,
    `assigned_to` int(11) DEFAULT NULL,
    `date_creation` datetime NOT NULL,
    `date_echeance` datetime DEFAULT NULL,
    `date_resolution` datetime DEFAULT NULL,
    `temps_passe` int(11) DEFAULT '0',
    `fichier_joint` varchar(255) DEFAULT NULL,
    `notes` text DEFAULT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime DEFAULT NULL,
    `deleted` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `ticket_number` (`ticket_number`),
    KEY `categorie_id` (`categorie_id`),
    KEY `priorite_id` (`priorite_id`),
    KEY `statut_id` (`statut_id`),
    KEY `created_by` (`created_by`),
    KEY `assigned_to` (`assigned_to`),
    CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `tickets_categories` (`id`),
    CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`priorite_id`) REFERENCES `tickets_priorites` (`id`),
    CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`statut_id`) REFERENCES `tickets_statuts` (`id`),
    CONSTRAINT `tickets_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `staff` (`id`),
    CONSTRAINT `tickets_ibfk_5` FOREIGN KEY (`assigned_to`) REFERENCES `staff` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================================
-- TABLE : tickets_reponses
-- ============================================================
CREATE TABLE IF NOT EXISTS `tickets_reponses` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ticket_id` int(11) NOT NULL,
    `staff_id` int(11) NOT NULL,
    `message` text NOT NULL,
    `fichier_joint` varchar(255) DEFAULT NULL,
    `est_interne` tinyint(1) DEFAULT '0',
    `created_at` datetime NOT NULL,
    `deleted` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `ticket_id` (`ticket_id`),
    KEY `staff_id` (`staff_id`),
    CONSTRAINT `tickets_reponses_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
    CONSTRAINT `tickets_reponses_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Supprimer les tables si elles existent
DROP TABLE IF EXISTS tickets_reponses;
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS tickets_categories;
DROP TABLE IF EXISTS tickets_priorites;
DROP TABLE IF EXISTS tickets_statuts;

-- Créer les tables
CREATE TABLE `tickets_categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(100) NOT NULL,
    `couleur` varchar(20) DEFAULT '#3b82f6',
    `created_at` datetime NOT NULL,
    `deleted` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tickets_categories` (`nom`, `couleur`, `created_at`) VALUES
('Informatique', '#3b82f6', NOW()),
('RH', '#10b981', NOW()),
('Administratif', '#f59e0b', NOW()),
('Financier', '#8b5cf6', NOW()),
('Logistique', '#06b6d4', NOW());

CREATE TABLE `tickets_priorites` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(50) NOT NULL,
    `niveau` int(11) DEFAULT '1',
    `couleur` varchar(20) DEFAULT '#94a3b8',
    `created_at` datetime NOT NULL,
    `deleted` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tickets_priorites` (`nom`, `niveau`, `couleur`, `created_at`) VALUES
('Basse', 1, '#94a3b8', NOW()),
('Moyenne', 2, '#3b82f6', NOW()),
('Haute', 3, '#f59e0b', NOW()),
('Urgente', 4, '#ef4444', NOW());

CREATE TABLE `tickets_statuts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(50) NOT NULL,
    `couleur` varchar(20) DEFAULT '#94a3b8',
    `created_at` datetime NOT NULL,
    `deleted` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tickets_statuts` (`nom`, `couleur`, `created_at`) VALUES
('Ouvert', '#3b82f6', NOW()),
('En cours', '#f59e0b', NOW()),
('En attente', '#8b5cf6', NOW()),
('Résolu', '#10b981', NOW()),
('Fermé', '#94a3b8', NOW());

CREATE TABLE `tickets` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ticket_number` varchar(50) NOT NULL,
    `titre` varchar(200) NOT NULL,
    `description` text NOT NULL,
    `categorie_id` int(11) DEFAULT NULL,
    `priorite_id` int(11) DEFAULT NULL,
    `statut_id` int(11) DEFAULT '1',
    `created_by` int(11) DEFAULT NULL,
    `assigned_to` int(11) DEFAULT NULL,
    `date_creation` datetime NOT NULL,
    `date_echeance` datetime DEFAULT NULL,
    `date_resolution` datetime DEFAULT NULL,
    `notes` text DEFAULT NULL,
    `fichier` varchar(255) DEFAULT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime DEFAULT NULL,
    `deleted` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `ticket_number` (`ticket_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tickets_reponses` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ticket_id` int(11) NOT NULL,
    `staff_id` int(11) NOT NULL,
    `message` text NOT NULL,
    `est_interne` tinyint(1) DEFAULT '0',
    `created_at` datetime NOT NULL,
    `deleted` tinyint(1) DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


//nouveau
-- ============================================================
-- 1. TABLE : categorie_membre (à créer EN PREMIER)
-- ============================================================

CREATE TABLE IF NOT EXISTS `categorie_membre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text,
  `montant_defaut` decimal(10,2) DEFAULT NULL,
  `couleur` varchar(7) DEFAULT '#1a472a',
  `statut` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion des catégories par défaut
INSERT INTO `categorie_membre` (`nom`, `description`, `montant_defaut`, `couleur`) VALUES
('Adhérent standard', 'Adhérent ordinaire', 25000.00, '#1a472a'),
('Adhérent bienfaiteur', 'Soutien financier de l\'association', 50000.00, '#d97706'),
('Adhérent honoraire', 'Ancien membre du bureau', 0.00, '#7c3aed'),
('Adhérent étudiant', 'Tarif réduit pour étudiants', 15000.00, '#2563eb'),
('Adhérent famille', 'Pour les familles', 35000.00, '#059669');

-- ============================================================
-- 2. TABLE : association_membres (APRÈS la création de categorie_membre)
-- ============================================================

CREATE TABLE IF NOT EXISTS `association_membres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matricule` varchar(20) DEFAULT NULL,
  `civilite` enum('M','Mme','Mlle','Dr','Pr') DEFAULT 'M',
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(100) DEFAULT NULL,
  `nationalite` varchar(50) DEFAULT NULL,
  `profession` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `telephone2` varchar(20) DEFAULT NULL,
  `adresse` text,
  `code_postal` varchar(10) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `pays` varchar(50) DEFAULT 'Côte d\'Ivoire',
  `type_adhérent` enum('actif','bienfaiteur','honoraire','ancien','en_attente') DEFAULT 'actif',
  `categorie_id` int(11) DEFAULT NULL,
  `statut` tinyint(1) DEFAULT 1 COMMENT '1=actif, 0=inactif',
  `date_adhesion` date DEFAULT NULL,
  `date_expiration` date DEFAULT NULL,
  `montant_cotisation` decimal(10,2) DEFAULT NULL,
  `mode_paiement` enum('especes','cheque','virement','cb','autre') DEFAULT 'especes',
  `photo` varchar(255) DEFAULT NULL,
  `commentaire` text,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_nom_prenom` (`nom`,`prenom`),
  KEY `idx_type_adhérent` (`type_adhérent`),
  KEY `idx_statut` (`statut`),
  KEY `idx_categorie` (`categorie_id`),
  CONSTRAINT `fk_membre_categorie` FOREIGN KEY (`categorie_id`) REFERENCES `categorie_membre` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 3. TABLE : historique_adhérents (journal des modifications)
-- ============================================================

CREATE TABLE IF NOT EXISTS `historique_adhérents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `membre_id` int(11) NOT NULL,
  `action` enum('creation','modification','suppression','reactivation','desactivation') NOT NULL,
  `champs_modifies` text,
  `anciennes_valeurs` text,
  `nouvelles_valeurs` text,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `membre_id` (`membre_id`),
  CONSTRAINT `fk_historique_membre` FOREIGN KEY (`membre_id`) REFERENCES `association_membres` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 4. TABLE : document_adhérents (documents liés aux adhérents)
-- ============================================================

CREATE TABLE IF NOT EXISTS `document_adhérents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `membre_id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `type_document` enum('photo','cni','passeport','permis','cv','attestation','autre') DEFAULT 'autre',
  `fichier` varchar(255) NOT NULL,
  `description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `membre_id` (`membre_id`),
  CONSTRAINT `fk_document_membre` FOREIGN KEY (`membre_id`) REFERENCES `association_membres` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 5. TABLE : paiements_cotisations (historique des paiements)
-- ============================================================

CREATE TABLE IF NOT EXISTS `paiements_cotisations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `membre_id` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `mode_paiement` enum('especes','cheque','virement','cb','autre') DEFAULT 'especes',
  `reference` varchar(100) DEFAULT NULL,
  `date_paiement` date NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('paye','impaye','annule','rembourse') DEFAULT 'paye',
  `commentaire` text,
  `recu_fiscal` tinyint(1) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `membre_id` (`membre_id`),
  CONSTRAINT `fk_paiement_membre` FOREIGN KEY (`membre_id`) REFERENCES `association_membres` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- TABLE : association_membres
-- DESCRIPTION : Gestion complète des adhérents de l'association
-- ============================================================

CREATE TABLE IF NOT EXISTS `association_membres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matricule` varchar(20) DEFAULT NULL,
  `civilite` enum('M','Mme','Mlle','Dr','Pr') DEFAULT 'M',
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(100) DEFAULT NULL,
  `nationalite` varchar(50) DEFAULT NULL,
  `profession` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `telephone2` varchar(20) DEFAULT NULL,
  `adresse` text,
  `code_postal` varchar(10) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `pays` varchar(50) DEFAULT 'Côte d\'Ivoire',
  `type_membre` enum('actif','bienfaiteur','honoraire','ancien','en_attente') DEFAULT 'actif',
  `categorie_id` int(11) DEFAULT NULL,
  `statut` tinyint(1) DEFAULT 1 COMMENT '1=actif, 0=inactif',
  `date_adhesion` date DEFAULT NULL,
  `date_expiration` date DEFAULT NULL,
  `montant_cotisation` decimal(10,2) DEFAULT NULL,
  `mode_paiement` enum('especes','cheque','virement','cb','autre') DEFAULT 'especes',
  `photo` varchar(255) DEFAULT NULL,
  `commentaire` text,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_nom_prenom` (`nom`,`prenom`),
  KEY `idx_type_membre` (`type_membre`),
  KEY `idx_statut` (`statut`),
  KEY `idx_categorie_id` (`categorie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE : association_categories
-- DESCRIPTION : Catégories personnalisables des adhérents
-- ============================================================

CREATE TABLE IF NOT EXISTS `association_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text,
  `montant_defaut` decimal(10,2) DEFAULT NULL,
  `couleur` varchar(7) DEFAULT '#1a472a',
  `statut` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE : association_membres_logs
-- DESCRIPTION : Journal des modifications des adhérents
-- ============================================================

CREATE TABLE IF NOT EXISTS `association_membres_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `membre_id` int(11) NOT NULL,
  `action` enum('creation','modification','suppression','reactivation','desactivation') NOT NULL,
  `champs_modifies` text,
  `anciennes_valeurs` text,
  `nouvelles_valeurs` text,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `membre_id` (`membre_id`),
  KEY `idx_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- INSERTION DES CATÉGORIES PAR DÉFAUT
-- ============================================================

INSERT INTO `association_categories` (`nom`, `description`, `montant_defaut`, `couleur`) VALUES
('Standard', 'Adhérent standard', 50000, '#1a472a'),
('Étudiant', 'Adhérent étudiant', 25000, '#2563eb'),
('Famille', 'Adhésion familiale (2 adultes + enfants)', 75000, '#7c3aed'),
('Bienfaiteur', 'Membre bienfaiteur', 100000, '#d97706'),
('Honoraire', 'Membre honoraire', 0, '#dc2626'),
('Entreprise', 'Adhésion entreprise', 150000, '#059669');


ALTER TABLE `enquiry` ADD `is_read` TINYINT(1) NOT NULL DEFAULT '0' AFTER `status`;


ALTER TABLE `enquiry` ADD `assigned_to` INT(11) NULL AFTER `status`;
ALTER TABLE `enquiry` ADD `is_read` TINYINT(1) NOT NULL DEFAULT '0' AFTER `assigned_to`;
ALTER TABLE `enquiry` ADD `read_at` DATETIME NULL AFTER `is_read`;


ALTER TABLE `staff_leave_request` ADD `assigned_to` INT(11) NULL AFTER `status`;
ALTER TABLE `staff_leave_request` ADD `is_read` TINYINT(1) NOT NULL DEFAULT '0' AFTER `assigned_to`;
ALTER TABLE `staff_leave_request` ADD `read_at` DATETIME NULL AFTER `is_read`;


//29
-- Table des journaux auxiliaires
CREATE TABLE IF NOT EXISTS `journaux_auxiliaires` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `code` varchar(20) NOT NULL COMMENT 'Code unique du journal (ACHATS, VENTES, etc.)',
    `libelle` varchar(100) NOT NULL COMMENT 'Libellé du journal',
    `type` enum('ACHATS','VENTES','BANQUE','CAISSE','PAIE','OPD','A-NOUVEAUX','AUTRE') NOT NULL,
    `compte_contrepartie` varchar(20) DEFAULT NULL COMMENT 'Compte de contrepartie par défaut',
    `description` text DEFAULT NULL,
    `actif` tinyint(1) DEFAULT 1,
    `date_creation` datetime DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des écritures comptables (si elle n'existe pas déjà)
CREATE TABLE IF NOT EXISTS `ecritures_comptables` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `journal_id` int(11) NOT NULL,
    `compte_debit` varchar(20) NOT NULL,
    `compte_credit` varchar(20) NOT NULL,
    `montant_debit` decimal(15,2) NOT NULL DEFAULT 0.00,
    `montant_credit` decimal(15,2) NOT NULL DEFAULT 0.00,
    `date_ecriture` date NOT NULL,
    `libelle` text NOT NULL,
    `piece_justificative` varchar(50) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `date_creation` datetime DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `journal_id` (`journal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion des journaux OHADA par défaut
INSERT INTO `journaux_auxiliaires` (`code`, `libelle`, `type`, `compte_contrepartie`, `description`, `actif`) VALUES
('ACHATS', 'Journal des achats', 'ACHATS', '401', 'Enregistrement des factures fournisseurs', 1),
('VENTES', 'Journal des ventes', 'VENTES', '411', 'Enregistrement des factures clients', 1),
('BANQUE', 'Journal des banques', 'BANQUE', '512', 'Opérations bancaires', 1),
('CAISSE', 'Journal de caisse', 'CAISSE', '571', 'Opérations en espèces', 1),
('PAIE', 'Journal des paies', 'PAIE', '421', 'Salaires et charges sociales', 1),
('OPD', 'Opérations diverses', 'OPD', NULL, 'Opérations diverses non classées', 1),
('A-NOUVEAUX', 'Ouverture de l\'exercice', 'A-NOUVEAUX', NULL, 'Ouverture et reprise des comptes', 1);


//04082026

-- =========================================
-- Tables pour la vitrine: demo + newsletter
-- =========================================

CREATE TABLE IF NOT EXISTS `demo_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(40) NOT NULL,
  `company` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `status` VARCHAR(30) NOT NULL DEFAULT 'nouvelle',
  `source_url` VARCHAR(255) DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_demo_requests_email` (`email`),
  KEY `idx_demo_requests_status` (`status`),
  KEY `idx_demo_requests_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(150) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `source_url` VARCHAR(255) DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_newsletter_email` (`email`),
  KEY `idx_newsletter_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

//06082026

-- OHADA accounting schema for Diagoma
-- Apply on your online server after backup.

CREATE TABLE IF NOT EXISTS `chart_of_accounts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entreprise_id` INT(11) NOT NULL DEFAULT 0,
  `numero_compte` VARCHAR(20) NOT NULL,
  `libelle_compte` VARCHAR(255) NOT NULL,
  `classe` VARCHAR(5) NOT NULL,
  `type_compte` VARCHAR(20) NOT NULL DEFAULT 'bilan',
  `compte_parent` VARCHAR(20) NOT NULL DEFAULT '',
  `nature` VARCHAR(20) NOT NULL DEFAULT 'debit',
  `allow_posting` TINYINT(1) NOT NULL DEFAULT 1,
  `status` VARCHAR(20) NOT NULL DEFAULT 'active',
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_chart_company_account` (`entreprise_id`,`numero_compte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ohada_tiers` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entreprise_id` INT(11) NOT NULL DEFAULT 0,
  `code` VARCHAR(30) NOT NULL,
  `libelle` VARCHAR(150) NOT NULL,
  `type` VARCHAR(30) NOT NULL DEFAULT 'AUTRE',
  `compte_collectif` VARCHAR(20) NOT NULL DEFAULT '',
  `telephone` VARCHAR(50) NOT NULL DEFAULT '',
  `email` VARCHAR(150) NOT NULL DEFAULT '',
  `adresse` VARCHAR(255) NOT NULL DEFAULT '',
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ohada_tiers_company` (`entreprise_id`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ohada_analytique` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entreprise_id` INT(11) NOT NULL DEFAULT 0,
  `code` VARCHAR(30) NOT NULL,
  `libelle` VARCHAR(150) NOT NULL,
  `type` VARCHAR(30) NOT NULL DEFAULT 'CENTRE_COUT',
  `description` TEXT NULL,
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ohada_analytique_company` (`entreprise_id`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ohada_exercices` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entreprise_id` INT(11) NOT NULL DEFAULT 0,
  `libelle` VARCHAR(100) NOT NULL,
  `date_debut` DATE NOT NULL,
  `date_fin` DATE NOT NULL,
  `statut` VARCHAR(20) NOT NULL DEFAULT 'ouvert',
  `is_active` TINYINT(1) NOT NULL DEFAULT 0,
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ohada_exercices_company` (`entreprise_id`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ohada_parametres` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entreprise_id` INT(11) NOT NULL DEFAULT 0,
  `referentiel` VARCHAR(30) NOT NULL DEFAULT 'SYSCOHADA',
  `devise` VARCHAR(10) NOT NULL DEFAULT 'XAF',
  `pays` VARCHAR(80) NOT NULL DEFAULT 'Cameroun',
  `longueur_compte` INT(11) NOT NULL DEFAULT 8,
  `utiliser_analytique` TINYINT(1) NOT NULL DEFAULT 1,
  `utiliser_tiers` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ohada_parametres_company` (`entreprise_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ohada_notes_annexes` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entreprise_id` INT(11) NOT NULL DEFAULT 0,
  `titre` VARCHAR(180) NOT NULL,
  `contenu` LONGTEXT NOT NULL,
  `ordre_affichage` INT(11) NOT NULL DEFAULT 1,
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ohada_notes_company` (`entreprise_id`,`ordre_affichage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ohada_journaux_config` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entreprise_id` INT(11) NOT NULL DEFAULT 0,
  `module_code` VARCHAR(30) NOT NULL,
  `journal_code` VARCHAR(20) NOT NULL,
  `libelle` VARCHAR(150) NOT NULL DEFAULT '',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ohada_journal_config_company` (`entreprise_id`,`module_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ohada_rapprochements` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entreprise_id` INT(11) NOT NULL DEFAULT 0,
  `date_operation` DATE NOT NULL,
  `reference` VARCHAR(60) NOT NULL DEFAULT '',
  `libelle` VARCHAR(255) NOT NULL DEFAULT '',
  `montant` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `statut` VARCHAR(30) NOT NULL DEFAULT 'en_attente',
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ohada_rapprochements_company` (`entreprise_id`,`date_operation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `journaux_auxiliaires` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entreprise_id` INT(11) NOT NULL DEFAULT 0,
  `code` VARCHAR(20) NOT NULL,
  `libelle` VARCHAR(100) NOT NULL,
  `type` VARCHAR(30) NOT NULL DEFAULT 'AUTRE',
  `compte_contrepartie` VARCHAR(20) NOT NULL DEFAULT '',
  `description` TEXT NULL,
  `actif` TINYINT(1) NOT NULL DEFAULT 1,
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `user_id` INT(11) NOT NULL DEFAULT 0,
  `date_creation` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_journal_company_code` (`entreprise_id`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ecritures_comptables` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entreprise_id` INT(11) NOT NULL DEFAULT 0,
  `journal_id` INT(11) NOT NULL DEFAULT 0,
  `exercice_id` INT(11) NULL DEFAULT NULL,
  `tier_id` INT(11) NULL DEFAULT NULL,
  `analytic_id` INT(11) NULL DEFAULT NULL,
  `compte_debit` VARCHAR(20) NOT NULL,
  `compte_credit` VARCHAR(20) NOT NULL,
  `montant` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `montant_debit` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `montant_credit` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `date_ecriture` DATE NOT NULL,
  `libelle` TEXT NULL,
  `reference_piece` VARCHAR(50) NOT NULL DEFAULT '',
  `piece_justificative` VARCHAR(255) NOT NULL DEFAULT '',
  `income_id` INT(11) NULL DEFAULT NULL,
  `user_id` INT(11) NOT NULL DEFAULT 0,
  `status` VARCHAR(20) NOT NULL DEFAULT 'posted',
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ecritures_company_date` (`entreprise_id`,`date_ecriture`),
  KEY `idx_ecritures_company_journal` (`entreprise_id`,`journal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Compatible alter statements for older MySQL/MariaDB versions used by phpMyAdmin
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journaux_auxiliaires' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `journaux_auxiliaires` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 0 AFTER `id`',
  'SELECT ''journaux_auxiliaires.entreprise_id already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journaux_auxiliaires' AND COLUMN_NAME = 'deleted') = 0,
  'ALTER TABLE `journaux_auxiliaires` ADD COLUMN `deleted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `actif`',
  'SELECT ''journaux_auxiliaires.deleted already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journaux_auxiliaires' AND COLUMN_NAME = 'user_id') = 0,
  'ALTER TABLE `journaux_auxiliaires` ADD COLUMN `user_id` INT(11) NOT NULL DEFAULT 0 AFTER `deleted`',
  'SELECT ''journaux_auxiliaires.user_id already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'journaux_auxiliaires' AND COLUMN_NAME = 'updated_at') = 0,
  'ALTER TABLE `journaux_auxiliaires` ADD COLUMN `updated_at` DATETIME NOT NULL DEFAULT ''1970-01-01 00:00:00'' AFTER `date_creation`',
  'SELECT ''journaux_auxiliaires.updated_at already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ecritures_comptables' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `ecritures_comptables` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 0 AFTER `id`',
  'SELECT ''ecritures_comptables.entreprise_id already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ecritures_comptables' AND COLUMN_NAME = 'exercice_id') = 0,
  'ALTER TABLE `ecritures_comptables` ADD COLUMN `exercice_id` INT(11) NULL DEFAULT NULL AFTER `journal_id`',
  'SELECT ''ecritures_comptables.exercice_id already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ecritures_comptables' AND COLUMN_NAME = 'tier_id') = 0,
  'ALTER TABLE `ecritures_comptables` ADD COLUMN `tier_id` INT(11) NULL DEFAULT NULL AFTER `exercice_id`',
  'SELECT ''ecritures_comptables.tier_id already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ecritures_comptables' AND COLUMN_NAME = 'analytic_id') = 0,
  'ALTER TABLE `ecritures_comptables` ADD COLUMN `analytic_id` INT(11) NULL DEFAULT NULL AFTER `tier_id`',
  'SELECT ''ecritures_comptables.analytic_id already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ecritures_comptables' AND COLUMN_NAME = 'montant_debit') = 0,
  'ALTER TABLE `ecritures_comptables` ADD COLUMN `montant_debit` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `montant`',
  'SELECT ''ecritures_comptables.montant_debit already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ecritures_comptables' AND COLUMN_NAME = 'montant_credit') = 0,
  'ALTER TABLE `ecritures_comptables` ADD COLUMN `montant_credit` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `montant_debit`',
  'SELECT ''ecritures_comptables.montant_credit already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ecritures_comptables' AND COLUMN_NAME = 'reference_piece') = 0,
  'ALTER TABLE `ecritures_comptables` ADD COLUMN `reference_piece` VARCHAR(50) NOT NULL DEFAULT '''' AFTER `libelle`',
  'SELECT ''ecritures_comptables.reference_piece already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ecritures_comptables' AND COLUMN_NAME = 'piece_justificative') = 0,
  'ALTER TABLE `ecritures_comptables` ADD COLUMN `piece_justificative` VARCHAR(255) NOT NULL DEFAULT '''' AFTER `reference_piece`',
  'SELECT ''ecritures_comptables.piece_justificative already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ecritures_comptables' AND COLUMN_NAME = 'user_id') = 0,
  'ALTER TABLE `ecritures_comptables` ADD COLUMN `user_id` INT(11) NOT NULL DEFAULT 0 AFTER `income_id`',
  'SELECT ''ecritures_comptables.user_id already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ecritures_comptables' AND COLUMN_NAME = 'status') = 0,
  'ALTER TABLE `ecritures_comptables` ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT ''posted'' AFTER `user_id`',
  'SELECT ''ecritures_comptables.status already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ecritures_comptables' AND COLUMN_NAME = 'deleted') = 0,
  'ALTER TABLE `ecritures_comptables` ADD COLUMN `deleted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`',
  'SELECT ''ecritures_comptables.deleted already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ecritures_comptables' AND COLUMN_NAME = 'created_at') = 0,
  'ALTER TABLE `ecritures_comptables` ADD COLUMN `created_at` DATETIME NOT NULL DEFAULT ''1970-01-01 00:00:00'' AFTER `deleted`',
  'SELECT ''ecritures_comptables.created_at already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ecritures_comptables' AND COLUMN_NAME = 'updated_at') = 0,
  'ALTER TABLE `ecritures_comptables` ADD COLUMN `updated_at` DATETIME NOT NULL DEFAULT ''1970-01-01 00:00:00'' AFTER `created_at`',
  'SELECT ''ecritures_comptables.updated_at already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE `ecritures_comptables` SET `montant_debit` = `montant` WHERE `montant_debit` = 0;
UPDATE `ecritures_comptables` SET `montant_credit` = `montant` WHERE `montant_credit` = 0;

INSERT INTO `journaux_auxiliaires`
(`entreprise_id`,`code`,`libelle`,`type`,`compte_contrepartie`,`description`,`actif`,`deleted`,`user_id`,`date_creation`,`updated_at`)
SELECT 0,'ACHATS','Journal des achats','ACHATS','401','Enregistrement des factures fournisseurs',1,0,0,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM `journaux_auxiliaires` WHERE `entreprise_id` = 0 AND `code` = 'ACHATS');

INSERT INTO `journaux_auxiliaires`
(`entreprise_id`,`code`,`libelle`,`type`,`compte_contrepartie`,`description`,`actif`,`deleted`,`user_id`,`date_creation`,`updated_at`)
SELECT 0,'VENTES','Journal des ventes','VENTES','411','Enregistrement des factures clients',1,0,0,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM `journaux_auxiliaires` WHERE `entreprise_id` = 0 AND `code` = 'VENTES');

INSERT INTO `journaux_auxiliaires`
(`entreprise_id`,`code`,`libelle`,`type`,`compte_contrepartie`,`description`,`actif`,`deleted`,`user_id`,`date_creation`,`updated_at`)
SELECT 0,'BANQUE','Journal des banques','BANQUE','512','Operations bancaires',1,0,0,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM `journaux_auxiliaires` WHERE `entreprise_id` = 0 AND `code` = 'BANQUE');

INSERT INTO `journaux_auxiliaires`
(`entreprise_id`,`code`,`libelle`,`type`,`compte_contrepartie`,`description`,`actif`,`deleted`,`user_id`,`date_creation`,`updated_at`)
SELECT 0,'CAISSE','Journal de caisse','CAISSE','571','Operations en especes',1,0,0,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM `journaux_auxiliaires` WHERE `entreprise_id` = 0 AND `code` = 'CAISSE');

INSERT INTO `journaux_auxiliaires`
(`entreprise_id`,`code`,`libelle`,`type`,`compte_contrepartie`,`description`,`actif`,`deleted`,`user_id`,`date_creation`,`updated_at`)
SELECT 0,'PAIE','Journal des paies','PAIE','421','Salaires et charges sociales',1,0,0,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM `journaux_auxiliaires` WHERE `entreprise_id` = 0 AND `code` = 'PAIE');

INSERT INTO `journaux_auxiliaires`
(`entreprise_id`,`code`,`libelle`,`type`,`compte_contrepartie`,`description`,`actif`,`deleted`,`user_id`,`date_creation`,`updated_at`)
SELECT 0,'OPD','Operations diverses','OPD','','Operations diverses non classees',1,0,0,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM `journaux_auxiliaires` WHERE `entreprise_id` = 0 AND `code` = 'OPD');

INSERT INTO `journaux_auxiliaires`
(`entreprise_id`,`code`,`libelle`,`type`,`compte_contrepartie`,`description`,`actif`,`deleted`,`user_id`,`date_creation`,`updated_at`)
SELECT 0,'A-NOUVEAUX','Ouverture de l exercice','A-NOUVEAUX','','Reprise des comptes',1,0,0,NOW(),NOW()
WHERE NOT EXISTS (SELECT 1 FROM `journaux_auxiliaires` WHERE `entreprise_id` = 0 AND `code` = 'A-NOUVEAUX');


-- =========================================================
-- SUPPORT ENTREPRISES / SUCCURSALES
-- =========================================================

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'compte_entreprise' AND COLUMN_NAME = 'type_structure') = 0,
  'ALTER TABLE `compte_entreprise` ADD COLUMN `type_structure` ENUM(''siege'',''succursale'') NOT NULL DEFAULT ''siege'' AFTER `slug`',
  'SELECT ''compte_entreprise.type_structure already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'compte_entreprise' AND COLUMN_NAME = 'parent_entreprise_id') = 0,
  'ALTER TABLE `compte_entreprise` ADD COLUMN `parent_entreprise_id` INT(11) NULL DEFAULT NULL AFTER `type_structure`',
  'SELECT ''compte_entreprise.parent_entreprise_id already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'compte_entreprise' AND COLUMN_NAME = 'code_succursale') = 0,
  'ALTER TABLE `compte_entreprise` ADD COLUMN `code_succursale` VARCHAR(50) NOT NULL DEFAULT '''' AFTER `parent_entreprise_id`',
  'SELECT ''compte_entreprise.code_succursale already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'compte_entreprise' AND COLUMN_NAME = 'can_manage_succursales') = 0,
  'ALTER TABLE `compte_entreprise` ADD COLUMN `can_manage_succursales` TINYINT(1) NOT NULL DEFAULT 0 AFTER `code_succursale`',
  'SELECT ''compte_entreprise.can_manage_succursales already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE `compte_entreprise`
SET `type_structure` = 'siege'
WHERE `type_structure` IS NULL OR `type_structure` = '';

CREATE TABLE IF NOT EXISTS `entreprise_succursales` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `siege_entreprise_id` INT(11) NOT NULL,
  `succursale_entreprise_id` INT(11) NOT NULL,
  `code_succursale` VARCHAR(50) NOT NULL DEFAULT '',
  `nom_succursale` VARCHAR(255) NOT NULL,
  `relation_status` VARCHAR(20) NOT NULL DEFAULT 'active',
  `inherit_settings` TINYINT(1) NOT NULL DEFAULT 1,
  `inherit_roles` TINYINT(1) NOT NULL DEFAULT 1,
  `inherit_ohada` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_es_siege` (`siege_entreprise_id`),
  KEY `idx_es_succursale` (`succursale_entreprise_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'compte_entreprise' AND INDEX_NAME = 'idx_compte_entreprise_type_structure') = 0,
  'ALTER TABLE `compte_entreprise` ADD INDEX `idx_compte_entreprise_type_structure` (`type_structure`)',
  'SELECT ''idx_compte_entreprise_type_structure already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'compte_entreprise' AND INDEX_NAME = 'idx_compte_entreprise_parent') = 0,
  'ALTER TABLE `compte_entreprise` ADD INDEX `idx_compte_entreprise_parent` (`parent_entreprise_id`)',
  'SELECT ''idx_compte_entreprise_parent already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'compte_entreprise' AND INDEX_NAME = 'idx_compte_entreprise_code_succursale') = 0,
  'ALTER TABLE `compte_entreprise` ADD INDEX `idx_compte_entreprise_code_succursale` (`code_succursale`)',
  'SELECT ''idx_compte_entreprise_code_succursale already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'entreprise_succursales' AND INDEX_NAME = 'uniq_entreprise_succursale_branch') = 0,
  'ALTER TABLE `entreprise_succursales` ADD UNIQUE INDEX `uniq_entreprise_succursale_branch` (`succursale_entreprise_id`)',
  'SELECT ''uniq_entreprise_succursale_branch already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'entreprise_succursales' AND INDEX_NAME = 'idx_entreprise_succursale_parent_code') = 0,
  'ALTER TABLE `entreprise_succursales` ADD INDEX `idx_entreprise_succursale_parent_code` (`siege_entreprise_id`, `code_succursale`)',
  'SELECT ''idx_entreprise_succursale_parent_code already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;




-- =========================================================
-- SUPPORT ENTREPRISES / SUCCURSALES
-- Compatible phpMyAdmin / MySQL avec verification IF EXISTS
-- =========================================================

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'compte_entreprise' AND COLUMN_NAME = 'type_structure') = 0,
  'ALTER TABLE `compte_entreprise` ADD COLUMN `type_structure` ENUM(''siege'',''succursale'') NOT NULL DEFAULT ''siege'' AFTER `slug`',
  'SELECT ''compte_entreprise.type_structure already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'compte_entreprise' AND COLUMN_NAME = 'parent_entreprise_id') = 0,
  'ALTER TABLE `compte_entreprise` ADD COLUMN `parent_entreprise_id` INT(11) NULL DEFAULT NULL AFTER `type_structure`',
  'SELECT ''compte_entreprise.parent_entreprise_id already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'compte_entreprise' AND COLUMN_NAME = 'code_succursale') = 0,
  'ALTER TABLE `compte_entreprise` ADD COLUMN `code_succursale` VARCHAR(50) NOT NULL DEFAULT '''' AFTER `parent_entreprise_id`',
  'SELECT ''compte_entreprise.code_succursale already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'compte_entreprise' AND COLUMN_NAME = 'can_manage_succursales') = 0,
  'ALTER TABLE `compte_entreprise` ADD COLUMN `can_manage_succursales` TINYINT(1) NOT NULL DEFAULT 0 AFTER `code_succursale`',
  'SELECT ''compte_entreprise.can_manage_succursales already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE `compte_entreprise`
SET `type_structure` = 'siege'
WHERE `type_structure` IS NULL OR `type_structure` = '';

CREATE TABLE IF NOT EXISTS `entreprise_succursales` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `siege_entreprise_id` INT(11) NOT NULL,
  `succursale_entreprise_id` INT(11) NOT NULL,
  `code_succursale` VARCHAR(50) NOT NULL DEFAULT '',
  `nom_succursale` VARCHAR(255) NOT NULL,
  `relation_status` VARCHAR(20) NOT NULL DEFAULT 'active',
  `inherit_settings` TINYINT(1) NOT NULL DEFAULT 1,
  `inherit_roles` TINYINT(1) NOT NULL DEFAULT 1,
  `inherit_ohada` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_es_siege` (`siege_entreprise_id`),
  KEY `idx_es_succursale` (`succursale_entreprise_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'compte_entreprise' AND INDEX_NAME = 'idx_compte_entreprise_type_structure') = 0,
  'ALTER TABLE `compte_entreprise` ADD INDEX `idx_compte_entreprise_type_structure` (`type_structure`)',
  'SELECT ''idx_compte_entreprise_type_structure already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'compte_entreprise' AND INDEX_NAME = 'idx_compte_entreprise_parent') = 0,
  'ALTER TABLE `compte_entreprise` ADD INDEX `idx_compte_entreprise_parent` (`parent_entreprise_id`)',
  'SELECT ''idx_compte_entreprise_parent already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'compte_entreprise' AND INDEX_NAME = 'idx_compte_entreprise_code_succursale') = 0,
  'ALTER TABLE `compte_entreprise` ADD INDEX `idx_compte_entreprise_code_succursale` (`code_succursale`)',
  'SELECT ''idx_compte_entreprise_code_succursale already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'entreprise_succursales' AND INDEX_NAME = 'uniq_entreprise_succursale_branch') = 0,
  'ALTER TABLE `entreprise_succursales` ADD UNIQUE INDEX `uniq_entreprise_succursale_branch` (`succursale_entreprise_id`)',
  'SELECT ''uniq_entreprise_succursale_branch already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'entreprise_succursales' AND INDEX_NAME = 'idx_entreprise_succursale_parent_code') = 0,
  'ALTER TABLE `entreprise_succursales` ADD INDEX `idx_entreprise_succursale_parent_code` (`siege_entreprise_id`, `code_succursale`)',
  'SELECT ''idx_entreprise_succursale_parent_code already exists'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;



-- =============================================================================
-- DIAGOMA – Corrections SaaS Multi-Tenant + Conformité OHADA
-- Fichier  : corrections_saas_ohada.sql
-- Date     : 2026-08-06
-- Usage    : phpMyAdmin → base diagoma → onglet SQL → Exécuter
--
-- SÉCURITÉ : Chaque bloc vérifie que la table ET la colonne existent
--            avant d'agir. Totalement idempotent (ré-exécutable sans risque).
-- =============================================================================

SET NAMES utf8mb4;

-- =============================================================================
-- HELPER : procédure interne pour ajouter entreprise_id si table et colonne
--          n'existent pas encore.  Utilisée via SET @sql + PREPARE.
-- =============================================================================

-- ─────────────────────────────────────────────────────────────────────────────
-- BLOC 1 – MODULE COMMERCIAL
-- ─────────────────────────────────────────────────────────────────────────────

-- 1.1  quotes ─ entreprise_id
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS   WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `quotes` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_quotes_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 1.1b quotes ─ supprimer unicité globale quote_number
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND INDEX_NAME = 'quote_number' AND NON_UNIQUE = 0) > 0,
  'ALTER TABLE `quotes` DROP INDEX `quote_number`',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 1.1c quotes ─ unicité quote_number par tenant
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND INDEX_NAME = 'uniq_quote_per_eid') = 0,
  'ALTER TABLE `quotes` ADD UNIQUE KEY `uniq_quote_per_eid` (`entreprise_id`, `quote_number`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- ─────────────────────────────────────────────────────────────────────────────
-- 1.2  quotes_nostock
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes_nostock') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes_nostock' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `quotes_nostock` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_qns_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes_nostock') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes_nostock' AND INDEX_NAME = 'quote_number' AND NON_UNIQUE = 0) > 0,
  'ALTER TABLE `quotes_nostock` DROP INDEX `quote_number`',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes_nostock') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes_nostock' AND INDEX_NAME = 'uniq_qns_per_eid') = 0,
  'ALTER TABLE `quotes_nostock` ADD UNIQUE KEY `uniq_qns_per_eid` (`entreprise_id`, `quote_number`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- ─────────────────────────────────────────────────────────────────────────────
-- 1.3  orders
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `orders` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_orders_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND INDEX_NAME = 'order_number' AND NON_UNIQUE = 0) > 0,
  'ALTER TABLE `orders` DROP INDEX `order_number`',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND INDEX_NAME = 'uniq_order_per_eid') = 0,
  'ALTER TABLE `orders` ADD UNIQUE KEY `uniq_order_per_eid` (`entreprise_id`, `order_number`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- ─────────────────────────────────────────────────────────────────────────────
-- 1.4  deliveries
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'deliveries') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'deliveries' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `deliveries` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_deliveries_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'deliveries') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'deliveries' AND INDEX_NAME = 'delivery_number' AND NON_UNIQUE = 0) > 0,
  'ALTER TABLE `deliveries` DROP INDEX `delivery_number`',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'deliveries') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'deliveries' AND INDEX_NAME = 'uniq_delivery_per_eid') = 0,
  'ALTER TABLE `deliveries` ADD UNIQUE KEY `uniq_delivery_per_eid` (`entreprise_id`, `delivery_number`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- ─────────────────────────────────────────────────────────────────────────────
-- 1.5  invoices
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `invoices` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_invoices_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND INDEX_NAME = 'invoice_number' AND NON_UNIQUE = 0) > 0,
  'ALTER TABLE `invoices` DROP INDEX `invoice_number`',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND INDEX_NAME = 'uniq_invoice_per_eid') = 0,
  'ALTER TABLE `invoices` ADD UNIQUE KEY `uniq_invoice_per_eid` (`entreprise_id`, `invoice_number`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- ─────────────────────────────────────────────────────────────────────────────
-- 1.6  payments
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payments') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payments' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `payments` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_payments_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- =============================================================================
-- BLOC 2 – MODULE STOCK
-- =============================================================================

-- 2.1  stock
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `stock` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_stock_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- Reconstruire unicité item+store par tenant
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock' AND INDEX_NAME = 'item_store') > 0,
  'ALTER TABLE `stock` DROP INDEX `item_store`',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock' AND INDEX_NAME = 'uniq_stock_item_store_eid') = 0,
  'ALTER TABLE `stock` ADD UNIQUE KEY `uniq_stock_item_store_eid` (`entreprise_id`, `item_id`, `store_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 2.2  stock_entries
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock_entries') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock_entries' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `stock_entries` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_se_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 2.3  stock_removals
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock_removals') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock_removals' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `stock_removals` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_sr_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 2.4  stock_audit
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock_audit') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock_audit' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `stock_audit` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_sa_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- =============================================================================
-- BLOC 3 – IMMOBILISATIONS (OHADA Classe 2)
-- =============================================================================

-- 3.1  immobilisations
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'immobilisations') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'immobilisations' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `immobilisations` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_immo_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'immobilisations') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'immobilisations' AND INDEX_NAME = 'uk_code' AND NON_UNIQUE = 0) > 0,
  'ALTER TABLE `immobilisations` DROP INDEX `uk_code`',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'immobilisations') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'immobilisations' AND INDEX_NAME = 'uniq_immo_code_eid') = 0,
  'ALTER TABLE `immobilisations` ADD UNIQUE KEY `uniq_immo_code_eid` (`entreprise_id`, `code`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 3.2  amortissements
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'amortissements') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'amortissements' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `amortissements` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_amor_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 3.3  cessions
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cessions') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cessions' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `cessions` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_cess_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- =============================================================================
-- BLOC 4 – FNE (Certification fiscale – Cameroun)
-- =============================================================================

-- 4.1  fne_settings
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'fne_settings') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'fne_settings' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `fne_settings` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD UNIQUE KEY `uniq_fne_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 4.2  fne_certification_log
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'fne_certification_log') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'fne_certification_log' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `fne_certification_log` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_fne_log_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- =============================================================================
-- BLOC 5 – MODULES SUPPORT
-- =============================================================================

-- 5.1  tickets
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tickets') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tickets' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `tickets` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_tickets_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tickets') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tickets' AND INDEX_NAME = 'ticket_number' AND NON_UNIQUE = 0) > 0,
  'ALTER TABLE `tickets` DROP INDEX `ticket_number`',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tickets') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tickets' AND INDEX_NAME = 'uniq_ticket_per_eid') = 0,
  'ALTER TABLE `tickets` ADD UNIQUE KEY `uniq_ticket_per_eid` (`entreprise_id`, `ticket_number`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 5.2  couriers
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'couriers') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'couriers' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `couriers` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_couriers_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 5.3  demandes
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'demandes') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'demandes' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `demandes` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_demandes_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 5.4  documents
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'documents') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'documents' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `documents` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_docs_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 5.5  services
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'services') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'services' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `services` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_services_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 5.6  rendez_vous
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'rendez_vous') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'rendez_vous' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `rendez_vous` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_rdv_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 5.7  reunions
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'reunions') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'reunions' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `reunions` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_reunions_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 5.8  rapports
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'rapports') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'rapports' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `rapports` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_rapports_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 5.9  annual_objectives
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'annual_objectives') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'annual_objectives' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `annual_objectives` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_annobj_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- =============================================================================
-- BLOC 6 – MODULES COMMUNAUTAIRES / ASSOCIATIFS
-- =============================================================================

-- 6.1  membres (église/organisation)
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'membres') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'membres' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `membres` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_membres_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'membres') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'membres' AND INDEX_NAME = 'uk_code' AND NON_UNIQUE = 0) > 0,
  'ALTER TABLE `membres` DROP INDEX `uk_code`',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'membres') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'membres' AND INDEX_NAME = 'uniq_membre_code_eid') = 0,
  'ALTER TABLE `membres` ADD UNIQUE KEY `uniq_membre_code_eid` (`entreprise_id`, `code_membre`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 6.2  offrandes_dimes
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'offrandes_dimes') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'offrandes_dimes' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `offrandes_dimes` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_offrandes_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'offrandes_dimes') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'offrandes_dimes' AND INDEX_NAME = 'uk_code' AND NON_UNIQUE = 0) > 0,
  'ALTER TABLE `offrandes_dimes` DROP INDEX `uk_code`',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'offrandes_dimes') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'offrandes_dimes' AND INDEX_NAME = 'uniq_offr_code_eid') = 0,
  'ALTER TABLE `offrandes_dimes` ADD UNIQUE KEY `uniq_offr_code_eid` (`entreprise_id`, `code_transaction`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 6.3  tontine_groupes
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tontine_groupes') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tontine_groupes' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `tontine_groupes` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_tg_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 6.4  tontine_membres
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tontine_membres') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tontine_membres' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `tontine_membres` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_tm_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tontine_membres') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tontine_membres' AND INDEX_NAME = 'telephone' AND NON_UNIQUE = 0) > 0,
  'ALTER TABLE `tontine_membres` DROP INDEX `telephone`',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tontine_membres') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tontine_membres' AND INDEX_NAME = 'uniq_tm_tel_eid') = 0,
  'ALTER TABLE `tontine_membres` ADD UNIQUE KEY `uniq_tm_tel_eid` (`entreprise_id`, `telephone`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 6.5  tontine_cycles
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tontine_cycles') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tontine_cycles' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `tontine_cycles` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_tc_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- 6.6  association_membres
SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'association_membres') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'association_membres' AND COLUMN_NAME = 'entreprise_id') = 0,
  'ALTER TABLE `association_membres` ADD COLUMN `entreprise_id` INT(11) NOT NULL DEFAULT 1 AFTER `id`, ADD INDEX `idx_am_eid` (`entreprise_id`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'association_membres') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'association_membres' AND INDEX_NAME = 'email' AND NON_UNIQUE = 0) > 0,
  'ALTER TABLE `association_membres` DROP INDEX `email`',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'association_membres') > 0
  AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'association_membres' AND INDEX_NAME = 'uniq_am_email_eid') = 0,
  'ALTER TABLE `association_membres` ADD UNIQUE KEY `uniq_am_email_eid` (`entreprise_id`, `email`)',
  'SELECT 1'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- =============================================================================
-- BLOC 7 – NOUVELLES TABLES OHADA (créées si elles n'existent pas)
-- =============================================================================

-- 7.1  Verrouillage des exercices clos (OHADA Art. 20 AUDCIF)
CREATE TABLE IF NOT EXISTS `ohada_exercices_lock` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entreprise_id` INT(11) NOT NULL,
  `exercice_id` INT(11) NOT NULL,
  `locked_at` DATETIME NOT NULL,
  `locked_by` INT(11) NOT NULL COMMENT 'user_id qui a verrouillé',
  `checksum_ecritures` VARCHAR(64) NOT NULL DEFAULT '' COMMENT 'SHA256 des écritures au moment du verrouillage',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_lock_exercice` (`exercice_id`),
  KEY `idx_lock_eid` (`entreprise_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Exercices comptables clos – intangibles OHADA Art. 20 AUDCIF';

-- 7.2  Lettrage des comptes de tiers (OHADA Classe 4)
CREATE TABLE IF NOT EXISTS `ohada_lettrage` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entreprise_id` INT(11) NOT NULL,
  `code_lettrage` VARCHAR(10) NOT NULL COMMENT 'Code alphabétique (AA, AB...)',
  `ecriture_id` INT(11) NOT NULL COMMENT 'FK ecritures_comptables',
  `tier_id` INT(11) NOT NULL COMMENT 'FK ohada_tiers',
  `montant` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `sens` ENUM('debit','credit') NOT NULL,
  `lettre_at` DATETIME NOT NULL,
  `lettre_by` INT(11) NOT NULL,
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lettrage_eid_tier` (`entreprise_id`, `tier_id`),
  KEY `idx_lettrage_code` (`entreprise_id`, `code_lettrage`),
  KEY `idx_lettrage_ecriture` (`ecriture_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Lettrage comptes tiers – OHADA Classe 4';

-- 7.3  Affectation du résultat (bilan OHADA complet)
CREATE TABLE IF NOT EXISTS `ohada_affectation_resultat` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entreprise_id` INT(11) NOT NULL,
  `exercice_id` INT(11) NOT NULL,
  `resultat_net` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `reserve_legale` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Min 5% OHADA',
  `reserve_statutaire` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `dividendes` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `report_nouveau` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `date_ag` DATE DEFAULT NULL COMMENT 'Date AG d affectation',
  `statut` ENUM('provisoire','valide') NOT NULL DEFAULT 'provisoire',
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_affectation_exercice` (`entreprise_id`, `exercice_id`),
  KEY `idx_affectation_eid` (`entreprise_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Affectation du résultat – OHADA Art. 30-36 AUDSC';

-- =============================================================================
-- BLOC 8 – VÉRIFICATION FINALE
-- Ce SELECT doit retourner 0 lignes si tout est correctement appliqué.
-- Les tables listées dans NOT IN sont légitimement sans entreprise_id
-- (tables globales, tables détail héritant du parent, ou tables système).
-- =============================================================================

SELECT
  t.TABLE_NAME AS `⚠ table_sans_entreprise_id`
FROM INFORMATION_SCHEMA.TABLES t
WHERE t.TABLE_SCHEMA = DATABASE()
  AND t.TABLE_TYPE = 'BASE TABLE'
  AND t.TABLE_NAME NOT IN (
    -- Tables système / globales
    'compte_entreprise','entreprise_succursales','migrations','sessions',
    'captcha','languages','roles','staff','users',
    'demo_requests','newsletter_subscribers',
    -- Nouvelles tables OHADA (bloc 7 – déjà scopées)
    'ohada_exercices_lock','ohada_lettrage','ohada_affectation_resultat',
    'chart_of_accounts','ohada_tiers','ohada_analytique','ohada_exercices',
    'ohada_parametres','ohada_notes_annexes','ohada_journaux_config',
    'ohada_rapprochements','journaux_auxiliaires','ecritures_comptables',
    -- Tables détail (héritent du parent)
    'quote_items','quote_nostock_items','order_items','delivery_items',
    'invoice_items','stock_entry_items','stock_removal_items',
    'tickets_reponses','tickets_categories','tickets_priorites','tickets_statuts',
    'tontine_cotisations','tontine_collectes',
    'association_membres_logs','document_adherents','paiements_cotisations',
    'fne_certification_log',
    'objective_assignments',
    'rapports_cultes','groupes_cellules','evenements',
    'baptemes','mariages','funerailles',
    'categorie_membre','association_categories',
    'ia_conversations','job_applications',
    'historique_adherents',
    'notification_setting','permission_group','permission_category',
    'roles_permissions','sch_settings'
  )
  AND NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS c
    WHERE c.TABLE_SCHEMA = DATABASE()
      AND c.TABLE_NAME = t.TABLE_NAME
      AND c.COLUMN_NAME = 'entreprise_id'
  )
ORDER BY t.TABLE_NAME;

-- =============================================================================
-- FIN – Si le SELECT du Bloc 8 est vide : corrections appliquées avec succès.
-- =============================================================================


DROP TABLE IF EXISTS `staff_attendance_qr`;
CREATE TABLE IF NOT EXISTS `staff_attendance_qr` (
  `id` int NOT NULL AUTO_INCREMENT,
  `staff_id` int NOT NULL,
  `attendance_date` date NOT NULL,
  `arrival_time` time DEFAULT NULL,
  `departure_time` time DEFAULT NULL,
  `scan_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('arrival','departure','complete') COLLATE utf8mb4_unicode_ci DEFAULT 'arrival',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `accuracy` decimal(10,2) DEFAULT NULL, 
  `photo_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verification_status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `verification_details` text COLLATE utf8mb4_unicode_ci,
  `verified_at` datetime DEFAULT NULL,
  `entreprise_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_daily_attendance` (`staff_id`,`attendance_date`),
  KEY `idx_attendance_date` (`attendance_date`),
  KEY `idx_staff_id` (`staff_id`),
  KEY `idx_status` (`status`),
  KEY `idx_date_range` (`attendance_date`,`arrival_time`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `qr_tokens` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `token` varchar(128) NOT NULL,
  `employee_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime DEFAULT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `used_at` datetime DEFAULT NULL,
  `last_used_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_qr_tokens_token` (`token`),
  KEY `idx_qr_tokens_is_used` (`is_used`),
  KEY `idx_qr_tokens_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

//14082026

ALTER TABLE sch_settings
  ADD COLUMN ai_enabled TINYINT(1) NOT NULL DEFAULT 1,
  ADD COLUMN ai_api_key VARCHAR(500) NULL DEFAULT NULL,
  ADD COLUMN ai_model VARCHAR(100) NULL DEFAULT 'gpt-4',
  ADD COLUMN ai_api_url VARCHAR(255) NULL DEFAULT 'https://api.openai.com/v1/chat/completions',
  ADD COLUMN ai_system_prompt TEXT NULL DEFAULT NULL;


  -- 1) Ajouter les champs IA dans sch_settings
ALTER TABLE `sch_settings`
  ADD COLUMN `ai_enabled` TINYINT(1) NOT NULL DEFAULT 1 AFTER `student_profile_edit`,
  ADD COLUMN `ai_api_key` VARCHAR(500) NULL DEFAULT NULL AFTER `ai_enabled`,
  ADD COLUMN `ai_model` VARCHAR(100) NULL DEFAULT 'gpt-4' AFTER `ai_api_key`,
  ADD COLUMN `ai_api_url` VARCHAR(255) NULL DEFAULT 'https://api.openai.com/v1/chat/completions' AFTER `ai_model`,
  ADD COLUMN `ai_system_prompt` TEXT NULL DEFAULT NULL AFTER `ai_api_url`;

-- 2) Ajouter les champs de relance dans sch_settings
ALTER TABLE `sch_settings`
  ADD COLUMN `reminder_enabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `ai_system_prompt`,
  ADD COLUMN `reminder_before_days` INT(11) NOT NULL DEFAULT 0 AFTER `reminder_enabled`,
  ADD COLUMN `reminder_on_due_date` TINYINT(1) NOT NULL DEFAULT 0 AFTER `reminder_before_days`,
  ADD COLUMN `reminder_after_days_1` INT(11) NOT NULL DEFAULT 0 AFTER `reminder_on_due_date`,
  ADD COLUMN `reminder_after_days_2` INT(11) NOT NULL DEFAULT 0 AFTER `reminder_after_days_1`,
  ADD COLUMN `reminder_after_days_3` INT(11) NOT NULL DEFAULT 0 AFTER `reminder_after_days_2`,
  ADD COLUMN `reminder_sender_email` VARCHAR(255) NULL DEFAULT NULL AFTER `reminder_after_days_3`,
  ADD COLUMN `reminder_sender_name` VARCHAR(255) NULL DEFAULT NULL AFTER `reminder_sender_email`;

-- 3) Créer la table d'historique des relances si elle n'existe pas
CREATE TABLE IF NOT EXISTS `invoice_reminders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` INT(11) NOT NULL,
  `reminder_level` VARCHAR(50) NOT NULL,
  `days_delta` INT(11) NOT NULL DEFAULT 0,
  `sent_at` DATETIME NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_invoice_reminders_invoice_id` (`invoice_id`),
  KEY `idx_invoice_reminders_sent_at` (`sent_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `sch_settings`
  ADD COLUMN `auto_backup` TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN `backup_time` TIME NULL DEFAULT NULL,
  ADD COLUMN `backup_frequency` VARCHAR(20) NULL DEFAULT 'daily',
  ADD COLUMN `backup_weekday` VARCHAR(10) NULL DEFAULT NULL;