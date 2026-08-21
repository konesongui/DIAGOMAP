<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Intelligence_model extends CI_Model {

    // Table par défaut : services (intelligence)
    protected $table = 'intelligence';

    /**
     * Changer la table cible
     */
    public function set_table($table) {
        $this->table = $table;
    }

    public function get($id = null) {
        if ($id) {
            return $this->db->where('id', $id)->get($this->table)->row_array();
        }
        if ($this->table === 'ia_conversations') {
            $this->db->order_by('created_at', 'DESC');
        } else {
            $this->db->order_by('name', 'ASC');
        }
        return $this->db->get($this->table)->result_array();
    }

    public function get_by_user($user_id, $limit = 20) {
        $this->db->where('user_id', $user_id);
        $this->db->order_by('created_at', 'DESC');
        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get($this->table)->result_array();
    }

    public function get_history($user_id = null, $limit = 20) {
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        $this->db->order_by('created_at', 'DESC');
        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get('ia_conversations')->result_array();
    }

    public function clear_history($user_id = null) {
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        return $this->db->delete('ia_conversations');
    }

    public function add($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        $this->db->where('id', $id)->update($this->table, $data);
    }

    public function delete($id) {
        $this->db->where('id', $id)->delete($this->table);
    }
}