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
