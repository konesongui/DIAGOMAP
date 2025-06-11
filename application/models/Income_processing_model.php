<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Income_processing_model extends My_Model
{
    // Specify the table targeted
    protected $ma_table = 'income_processing';

    public function __construct()
    {
        parent::__construct();
    }

    public function delete_increase($id)
    {

        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);

        $this->db->delete('income_processing');

        $message   = DELETE_RECORD_CONSTANT . " On  Increase   id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {

            return $id;
        }
    }


}
