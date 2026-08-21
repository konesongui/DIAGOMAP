<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_enterprise_branch_support extends CI_Migration
{
    public function up()
    {
        $this->load->dbforge();

        $this->create_or_update_compte_entreprise();
        $this->create_entreprise_succursales();
        $this->backfill_existing_enterprises();
    }

    public function down()
    {
        $this->load->dbforge();
        $this->dbforge->drop_table('entreprise_succursales', true);
    }

    protected function create_or_update_compte_entreprise()
    {
        $this->add_column_if_missing(
            'compte_entreprise',
            'type_structure',
            "ALTER TABLE `compte_entreprise` ADD `type_structure` ENUM('siege','succursale') NOT NULL DEFAULT 'siege' AFTER `slug`"
        );
        $this->add_column_if_missing(
            'compte_entreprise',
            'parent_entreprise_id',
            "ALTER TABLE `compte_entreprise` ADD `parent_entreprise_id` INT(11) NULL DEFAULT NULL AFTER `type_structure`"
        );
        $this->add_column_if_missing(
            'compte_entreprise',
            'code_succursale',
            "ALTER TABLE `compte_entreprise` ADD `code_succursale` VARCHAR(50) NOT NULL DEFAULT '' AFTER `parent_entreprise_id`"
        );
        $this->add_column_if_missing(
            'compte_entreprise',
            'can_manage_succursales',
            "ALTER TABLE `compte_entreprise` ADD `can_manage_succursales` TINYINT(1) NOT NULL DEFAULT 0 AFTER `code_succursale`"
        );

        $this->add_index_if_missing('compte_entreprise', 'idx_compte_entreprise_type_structure', "ALTER TABLE `compte_entreprise` ADD INDEX `idx_compte_entreprise_type_structure` (`type_structure`)");
        $this->add_index_if_missing('compte_entreprise', 'idx_compte_entreprise_parent', "ALTER TABLE `compte_entreprise` ADD INDEX `idx_compte_entreprise_parent` (`parent_entreprise_id`)");
        $this->add_index_if_missing('compte_entreprise', 'idx_compte_entreprise_code_succursale', "ALTER TABLE `compte_entreprise` ADD INDEX `idx_compte_entreprise_code_succursale` (`code_succursale`)");
    }

    protected function create_entreprise_succursales()
    {
        if (!$this->db->table_exists('entreprise_succursales')) {
            $this->dbforge->add_field(array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
                'siege_entreprise_id' => array('type' => 'INT', 'constraint' => 11, 'null' => false),
                'succursale_entreprise_id' => array('type' => 'INT', 'constraint' => 11, 'null' => false),
                'code_succursale' => array('type' => 'VARCHAR', 'constraint' => 50, 'null' => false, 'default' => ''),
                'nom_succursale' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => false),
                'relation_status' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => false, 'default' => 'active'),
                'inherit_settings' => array('type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1),
                'inherit_roles' => array('type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1),
                'inherit_ohada' => array('type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1),
                'created_at' => array('type' => 'DATETIME', 'null' => false),
                'updated_at' => array('type' => 'DATETIME', 'null' => false),
            ));
            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('siege_entreprise_id');
            $this->dbforge->add_key('succursale_entreprise_id');
            $this->dbforge->create_table('entreprise_succursales', true, array('ENGINE' => 'InnoDB', 'DEFAULT CHARSET' => 'utf8mb4'));
        }

        $this->add_index_if_missing('entreprise_succursales', 'uniq_entreprise_succursale_branch', "ALTER TABLE `entreprise_succursales` ADD UNIQUE INDEX `uniq_entreprise_succursale_branch` (`succursale_entreprise_id`)");
        $this->add_index_if_missing('entreprise_succursales', 'idx_entreprise_succursale_parent_code', "ALTER TABLE `entreprise_succursales` ADD INDEX `idx_entreprise_succursale_parent_code` (`siege_entreprise_id`, `code_succursale`)");
    }

    protected function backfill_existing_enterprises()
    {
        if ($this->column_exists('compte_entreprise', 'type_structure')) {
            $this->db->query("UPDATE `compte_entreprise` SET `type_structure` = 'siege' WHERE `type_structure` IS NULL OR `type_structure` = ''");
        }
    }

    protected function add_column_if_missing($table, $column, $sql)
    {
        if (!$this->column_exists($table, $column)) {
            $this->db->query($sql);
        }
    }

    protected function add_index_if_missing($table, $index_name, $sql)
    {
        $indexes = $this->db->query("SHOW INDEX FROM `" . $table . "` WHERE Key_name = " . $this->db->escape($index_name))->result_array();
        if (empty($indexes)) {
            $this->db->query($sql);
        }
    }

    protected function column_exists($table, $column)
    {
        return $this->db->field_exists($column, $table);
    }
}
