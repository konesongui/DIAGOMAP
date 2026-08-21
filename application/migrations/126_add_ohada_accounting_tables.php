<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_ohada_accounting_tables extends CI_Migration
{
    public function up()
    {
        $this->load->dbforge();

        $this->create_chart_of_accounts();
        $this->create_or_update_journaux();
        $this->create_or_update_ecritures();
        $this->create_ohada_tiers();
        $this->create_ohada_analytique();
        $this->create_ohada_exercices();
        $this->create_ohada_parametres();
        $this->create_ohada_notes_annexes();
        $this->create_ohada_journaux_config();
        $this->create_ohada_rapprochements();
        $this->seed_default_journals();
    }

    public function down()
    {
        $this->load->dbforge();
        $this->dbforge->drop_table('ohada_rapprochements', true);
        $this->dbforge->drop_table('ohada_journaux_config', true);
        $this->dbforge->drop_table('ohada_notes_annexes', true);
        $this->dbforge->drop_table('ohada_parametres', true);
        $this->dbforge->drop_table('ohada_exercices', true);
        $this->dbforge->drop_table('ohada_analytique', true);
        $this->dbforge->drop_table('ohada_tiers', true);
        $this->dbforge->drop_table('chart_of_accounts', true);
    }

    protected function create_chart_of_accounts()
    {
        if (!$this->db->table_exists('chart_of_accounts')) {
            $this->dbforge->add_field(array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
                'entreprise_id' => array('type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 0),
                'numero_compte' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => false),
                'libelle_compte' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => false),
                'classe' => array('type' => 'VARCHAR', 'constraint' => 5, 'null' => false),
                'type_compte' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => false, 'default' => 'bilan'),
                'compte_parent' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => false, 'default' => ''),
                'nature' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => false, 'default' => 'debit'),
                'allow_posting' => array('type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1),
                'status' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => false, 'default' => 'active'),
                'deleted' => array('type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 0),
                'created_at' => array('type' => 'DATETIME', 'null' => false),
                'updated_at' => array('type' => 'DATETIME', 'null' => false),
            ));
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key(array('entreprise_id', 'numero_compte'));
            $this->dbforge->create_table('chart_of_accounts', true, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4'));
        }
    }

    protected function create_or_update_journaux()
    {
        if (!$this->db->table_exists('journaux_auxiliaires')) {
            $this->dbforge->add_field(array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
                'entreprise_id' => array('type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 0),
                'code' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => false),
                'libelle' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => false),
                'type' => array('type' => 'VARCHAR', 'constraint' => 30, 'null' => false, 'default' => 'AUTRE'),
                'compte_contrepartie' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => false, 'default' => ''),
                'description' => array('type' => 'TEXT', 'null' => true),
                'actif' => array('type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1),
                'deleted' => array('type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 0),
                'user_id' => array('type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 0),
                'date_creation' => array('type' => 'DATETIME', 'null' => false),
                'updated_at' => array('type' => 'DATETIME', 'null' => false),
            ));
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table('journaux_auxiliaires', true, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4'));
        } else {
            $this->add_column_if_missing('journaux_auxiliaires', 'entreprise_id', "ALTER TABLE `journaux_auxiliaires` ADD `entreprise_id` INT(11) NOT NULL DEFAULT 0 AFTER `id`");
            $this->add_column_if_missing('journaux_auxiliaires', 'deleted', "ALTER TABLE `journaux_auxiliaires` ADD `deleted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `actif`");
            $this->add_column_if_missing('journaux_auxiliaires', 'user_id', "ALTER TABLE `journaux_auxiliaires` ADD `user_id` INT(11) NOT NULL DEFAULT 0 AFTER `deleted`");
            $this->add_column_if_missing('journaux_auxiliaires', 'updated_at', "ALTER TABLE `journaux_auxiliaires` ADD `updated_at` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00' AFTER `date_creation`");
            if (!$this->column_exists('journaux_auxiliaires', 'date_creation')) {
                $this->db->query("ALTER TABLE `journaux_auxiliaires` ADD `date_creation` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00'");
            }
        }
    }

    protected function create_or_update_ecritures()
    {
        if (!$this->db->table_exists('ecritures_comptables')) {
            $this->dbforge->add_field(array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
                'entreprise_id' => array('type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 0),
                'journal_id' => array('type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 0),
                'exercice_id' => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                'tier_id' => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                'analytic_id' => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                'compte_debit' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => false),
                'compte_credit' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => false),
                'montant' => array('type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false, 'default' => '0.00'),
                'montant_debit' => array('type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false, 'default' => '0.00'),
                'montant_credit' => array('type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false, 'default' => '0.00'),
                'date_ecriture' => array('type' => 'DATE', 'null' => false),
                'libelle' => array('type' => 'TEXT', 'null' => true),
                'reference_piece' => array('type' => 'VARCHAR', 'constraint' => 50, 'null' => false, 'default' => ''),
                'piece_justificative' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => false, 'default' => ''),
                'income_id' => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                'user_id' => array('type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 0),
                'status' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => false, 'default' => 'posted'),
                'deleted' => array('type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 0),
                'created_at' => array('type' => 'DATETIME', 'null' => false),
                'updated_at' => array('type' => 'DATETIME', 'null' => false),
            ));
            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table('ecritures_comptables', true, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4'));
        } else {
            $this->add_column_if_missing('ecritures_comptables', 'entreprise_id', "ALTER TABLE `ecritures_comptables` ADD `entreprise_id` INT(11) NOT NULL DEFAULT 0 AFTER `id`");
            $this->add_column_if_missing('ecritures_comptables', 'exercice_id', "ALTER TABLE `ecritures_comptables` ADD `exercice_id` INT(11) NULL AFTER `journal_id`");
            $this->add_column_if_missing('ecritures_comptables', 'tier_id', "ALTER TABLE `ecritures_comptables` ADD `tier_id` INT(11) NULL AFTER `exercice_id`");
            $this->add_column_if_missing('ecritures_comptables', 'analytic_id', "ALTER TABLE `ecritures_comptables` ADD `analytic_id` INT(11) NULL AFTER `tier_id`");
            $this->add_column_if_missing('ecritures_comptables', 'montant_debit', "ALTER TABLE `ecritures_comptables` ADD `montant_debit` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `montant`");
            $this->add_column_if_missing('ecritures_comptables', 'montant_credit', "ALTER TABLE `ecritures_comptables` ADD `montant_credit` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `montant_debit`");
            $this->add_column_if_missing('ecritures_comptables', 'reference_piece', "ALTER TABLE `ecritures_comptables` ADD `reference_piece` VARCHAR(50) NOT NULL DEFAULT '' AFTER `libelle`");
            $this->add_column_if_missing('ecritures_comptables', 'piece_justificative', "ALTER TABLE `ecritures_comptables` ADD `piece_justificative` VARCHAR(255) NOT NULL DEFAULT '' AFTER `reference_piece`");
            $this->add_column_if_missing('ecritures_comptables', 'user_id', "ALTER TABLE `ecritures_comptables` ADD `user_id` INT(11) NOT NULL DEFAULT 0 AFTER `income_id`");
            $this->add_column_if_missing('ecritures_comptables', 'status', "ALTER TABLE `ecritures_comptables` ADD `status` VARCHAR(20) NOT NULL DEFAULT 'posted' AFTER `user_id`");
            $this->add_column_if_missing('ecritures_comptables', 'deleted', "ALTER TABLE `ecritures_comptables` ADD `deleted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`");
            $this->add_column_if_missing('ecritures_comptables', 'created_at', "ALTER TABLE `ecritures_comptables` ADD `created_at` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00' AFTER `deleted`");
            $this->add_column_if_missing('ecritures_comptables', 'updated_at', "ALTER TABLE `ecritures_comptables` ADD `updated_at` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00' AFTER `created_at`");
            $this->db->query("UPDATE `ecritures_comptables` SET `montant_debit` = `montant` WHERE `montant_debit` = 0");
            $this->db->query("UPDATE `ecritures_comptables` SET `montant_credit` = `montant` WHERE `montant_credit` = 0");
        }
    }

    protected function create_ohada_tiers()
    {
        if ($this->db->table_exists('ohada_tiers')) {
            return;
        }

        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
            'entreprise_id' => array('type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 0),
            'code' => array('type' => 'VARCHAR', 'constraint' => 30, 'null' => false),
            'libelle' => array('type' => 'VARCHAR', 'constraint' => 150, 'null' => false),
            'type' => array('type' => 'VARCHAR', 'constraint' => 30, 'null' => false, 'default' => 'AUTRE'),
            'compte_collectif' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => false, 'default' => ''),
            'telephone' => array('type' => 'VARCHAR', 'constraint' => 50, 'null' => false, 'default' => ''),
            'email' => array('type' => 'VARCHAR', 'constraint' => 150, 'null' => false, 'default' => ''),
            'adresse' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => false, 'default' => ''),
            'deleted' => array('type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 0),
            'created_at' => array('type' => 'DATETIME', 'null' => false),
            'updated_at' => array('type' => 'DATETIME', 'null' => false),
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('ohada_tiers', true, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4'));
    }

    protected function create_ohada_analytique()
    {
        if ($this->db->table_exists('ohada_analytique')) {
            return;
        }

        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
            'entreprise_id' => array('type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 0),
            'code' => array('type' => 'VARCHAR', 'constraint' => 30, 'null' => false),
            'libelle' => array('type' => 'VARCHAR', 'constraint' => 150, 'null' => false),
            'type' => array('type' => 'VARCHAR', 'constraint' => 30, 'null' => false, 'default' => 'CENTRE_COUT'),
            'description' => array('type' => 'TEXT', 'null' => true),
            'deleted' => array('type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 0),
            'created_at' => array('type' => 'DATETIME', 'null' => false),
            'updated_at' => array('type' => 'DATETIME', 'null' => false),
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('ohada_analytique', true, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4'));
    }

    protected function create_ohada_exercices()
    {
        if ($this->db->table_exists('ohada_exercices')) {
            return;
        }

        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
            'entreprise_id' => array('type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 0),
            'libelle' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => false),
            'date_debut' => array('type' => 'DATE', 'null' => false),
            'date_fin' => array('type' => 'DATE', 'null' => false),
            'statut' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => false, 'default' => 'ouvert'),
            'is_active' => array('type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 0),
            'deleted' => array('type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 0),
            'created_at' => array('type' => 'DATETIME', 'null' => false),
            'updated_at' => array('type' => 'DATETIME', 'null' => false),
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('ohada_exercices', true, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4'));
    }

    protected function create_ohada_parametres()
    {
        if ($this->db->table_exists('ohada_parametres')) {
            return;
        }

        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
            'entreprise_id' => array('type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 0),
            'referentiel' => array('type' => 'VARCHAR', 'constraint' => 30, 'null' => false, 'default' => 'SYSCOHADA'),
            'devise' => array('type' => 'VARCHAR', 'constraint' => 10, 'null' => false, 'default' => 'XAF'),
            'pays' => array('type' => 'VARCHAR', 'constraint' => 80, 'null' => false, 'default' => 'Cameroun'),
            'longueur_compte' => array('type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 8),
            'utiliser_analytique' => array('type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1),
            'utiliser_tiers' => array('type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1),
            'created_at' => array('type' => 'DATETIME', 'null' => false),
            'updated_at' => array('type' => 'DATETIME', 'null' => false),
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('ohada_parametres', true, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4'));
    }

    protected function create_ohada_notes_annexes()
    {
        if ($this->db->table_exists('ohada_notes_annexes')) {
            return;
        }

        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
            'entreprise_id' => array('type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 0),
            'titre' => array('type' => 'VARCHAR', 'constraint' => 180, 'null' => false),
            'contenu' => array('type' => 'LONGTEXT', 'null' => false),
            'ordre_affichage' => array('type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 1),
            'deleted' => array('type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 0),
            'created_at' => array('type' => 'DATETIME', 'null' => false),
            'updated_at' => array('type' => 'DATETIME', 'null' => false),
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('ohada_notes_annexes', true, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4'));
    }

    protected function create_ohada_journaux_config()
    {
        if ($this->db->table_exists('ohada_journaux_config')) {
            return;
        }

        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
            'entreprise_id' => array('type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 0),
            'module_code' => array('type' => 'VARCHAR', 'constraint' => 30, 'null' => false),
            'journal_code' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => false),
            'libelle' => array('type' => 'VARCHAR', 'constraint' => 150, 'null' => false, 'default' => ''),
            'created_at' => array('type' => 'DATETIME', 'null' => false),
            'updated_at' => array('type' => 'DATETIME', 'null' => false),
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('ohada_journaux_config', true, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4'));
    }

    protected function create_ohada_rapprochements()
    {
        if ($this->db->table_exists('ohada_rapprochements')) {
            return;
        }

        $this->dbforge->add_field(array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
            'entreprise_id' => array('type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 0),
            'date_operation' => array('type' => 'DATE', 'null' => false),
            'reference' => array('type' => 'VARCHAR', 'constraint' => 60, 'null' => false, 'default' => ''),
            'libelle' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => false, 'default' => ''),
            'montant' => array('type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false, 'default' => '0.00'),
            'statut' => array('type' => 'VARCHAR', 'constraint' => 30, 'null' => false, 'default' => 'en_attente'),
            'deleted' => array('type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 0),
            'created_at' => array('type' => 'DATETIME', 'null' => false),
            'updated_at' => array('type' => 'DATETIME', 'null' => false),
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('ohada_rapprochements', true, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4'));
    }

    protected function seed_default_journals()
    {
        $defaults = array(
            array('code' => 'ACHATS', 'libelle' => 'Journal des achats', 'type' => 'ACHATS', 'compte_contrepartie' => '401', 'description' => 'Enregistrement des factures fournisseurs'),
            array('code' => 'VENTES', 'libelle' => 'Journal des ventes', 'type' => 'VENTES', 'compte_contrepartie' => '411', 'description' => 'Enregistrement des factures clients'),
            array('code' => 'BANQUE', 'libelle' => 'Journal des banques', 'type' => 'BANQUE', 'compte_contrepartie' => '512', 'description' => 'Operations bancaires'),
            array('code' => 'CAISSE', 'libelle' => 'Journal de caisse', 'type' => 'CAISSE', 'compte_contrepartie' => '571', 'description' => 'Operations en especes'),
            array('code' => 'PAIE', 'libelle' => 'Journal des paies', 'type' => 'PAIE', 'compte_contrepartie' => '421', 'description' => 'Salaires et charges sociales'),
            array('code' => 'OPD', 'libelle' => 'Operations diverses', 'type' => 'OPD', 'compte_contrepartie' => '', 'description' => 'Operations diverses non classees'),
            array('code' => 'A-NOUVEAUX', 'libelle' => 'Ouverture de l exercice', 'type' => 'A-NOUVEAUX', 'compte_contrepartie' => '', 'description' => 'Reprise des comptes'),
        );

        foreach ($defaults as $row) {
            $this->db->where('code', $row['code']);
            if ($this->column_exists('journaux_auxiliaires', 'entreprise_id')) {
                $this->db->where('entreprise_id', 0);
            }
            $exists = $this->db->get('journaux_auxiliaires')->row_array();
            if ($exists) {
                continue;
            }

            $row['entreprise_id'] = 0;
            $row['actif'] = 1;
            $row['deleted'] = 0;
            $row['user_id'] = 0;
            $row['date_creation'] = date('Y-m-d H:i:s');
            $row['updated_at'] = date('Y-m-d H:i:s');
            $this->db->insert('journaux_auxiliaires', $row);
        }
    }

    protected function add_column_if_missing($table, $column, $sql)
    {
        if (!$this->column_exists($table, $column)) {
            $this->db->query($sql);
        }
    }

    protected function column_exists($table, $column)
    {
        return $this->db->table_exists($table) && in_array($column, $this->db->list_fields($table), true);
    }
}
