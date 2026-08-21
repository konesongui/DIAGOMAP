<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Accounting_model extends CI_Model
{
    protected $table = 'accounting_entries';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ajouter plusieurs écritures comptables (ligne par ligne pour éviter les blocages insert_batch)
     */
    public function add_entries($entries = [])
    {
        if (empty($entries)) {
            log_message('error', 'Accounting_model: tableau entries vide');
            return false;
        }

        foreach ($entries as $entry) {
            $this->db->insert($this->table, $entry);
            if ($this->db->affected_rows() == 0) {
                log_message('error', 'Accounting_model: insertion échouée → '.$this->db->last_query());
            } else {
                log_message('debug', 'Accounting_model: insertion OK → '.$this->db->last_query());
            }
        }
        return true;
    }

    public function get_by_invoice($invoice_id)
    {
        return $this->db->where('invoice_id', $invoice_id)
            ->order_by('id', 'ASC')
            ->get($this->table)
            ->result_array();
    }

    public function get_all()
    {
        return $this->db->order_by('date', 'ASC')
            ->order_by('id', 'ASC')
            ->get($this->table)
            ->result_array();
    }

    public function delete_by_invoice($invoice_id)
    {
        return $this->db->where('invoice_id', $invoice_id)
            ->delete($this->table);
    }
}
