<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Support_messages_model extends CI_Model
{
    public function table_available()
    {
        return $this->db->table_exists('support_messages');
    }

    public function get_all($entreprise_id)
    {
        if (!$this->table_available()) {
            return array();
        }

        return $this->db->where('entreprise_id', (int) $entreprise_id)->order_by('id', 'DESC')->get('support_messages')->result_array();
    }

    public function get_active($entreprise_id)
    {
        if (!$this->table_available()) {
            return null;
        }

        $now = date('Y-m-d H:i:s');
        $this->db->where('active', 1);
        $this->db->where_in('entreprise_id', array(0, (int) $entreprise_id));
        $this->db->where('(start_at IS NULL OR start_at <= ' . $this->db->escape($now) . ')', null, false);
        $this->db->where('(end_at IS NULL OR end_at >= ' . $this->db->escape($now) . ')', null, false);
        $this->db->order_by('entreprise_id', 'DESC')->order_by('id', 'DESC');

        return $this->db->get('support_messages')->row_array();
    }

    public function create($data)
    {
        return $this->db->insert('support_messages', $data) ? $this->db->insert_id() : false;
    }

    public function update($id, $entreprise_id, $data)
    {
        return $this->db->where('id', (int) $id)->where('entreprise_id', (int) $entreprise_id)->update('support_messages', $data);
    }

    public function delete($id, $entreprise_id)
    {
        return $this->db->where('id', (int) $id)->where('entreprise_id', (int) $entreprise_id)->delete('support_messages');
    }
}