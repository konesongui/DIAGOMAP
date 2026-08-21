<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Services_model extends CI_Model {

    protected $table = 'services';

    public function get($id = null) {
        if ($id) {
            return $this->db->where('id', $id)->get($this->table)->row_array();
        }
        return $this->db->order_by('name', 'ASC')->get($this->table)->result_array();
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