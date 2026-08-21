<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Clients_model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    public function get($id = null) {
        $this->db->select()->from('clients');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

// Dans Clients_model.php

    public function generate_client_code() {
        // Récupère le dernier ID inséré (ou le dernier code)
        $this->db->select_max('id');
        $query = $this->db->get('item_supplier')->row();
        $next_id = ($query->id ?? 0) + 1;

        // Format : CLI + année-mois-jour + numéro sur 4 chiffres
        $date_prefix = date('Ymd');
        return 'CLI-' . $date_prefix . '-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * This function will take the post data passed from the controller
     * If id is present, then it will do an update
     * else an insert. One function doing both add and edit.
     * @param $data
     */


    public function add($data) {
        // 1. Insérer sans le code client (ou avec valeur temporaire)
        $this->db->insert('clients', $data);
        $insert_id = $this->db->insert_id();

        // 2. Générer le code client basé sur l'ID réel
        $code_client = 'CLI-' . str_pad($insert_id, 6, '0', STR_PAD_LEFT);
        // Exemple : CLI-000042

        // 3. Mettre à jour l'enregistrement avec le code unique
        $this->db->where('id', $insert_id);
        $this->db->update('clients', ['code_client' => $code_client]);

        return $insert_id;
    }

    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('clients');
        $message = DELETE_RECORD_CONSTANT . " On item supplier id " . $id;
        $action = "Delete";
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
            //return $return_value;
        }
    }

    public function update($data, $id) {
        $this->db->where('id', $id);
        $this->db->update('clients', $data);   // adaptez le nom de la table si différent
        return $this->db->affected_rows();
    }



}
