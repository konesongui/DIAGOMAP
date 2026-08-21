<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Demorequest_model extends CI_Model
{
    public function addDemoRequest($data)
    {
        $ok = $this->db->insert('demo_requests', $data);
        return $ok ? $this->db->insert_id() : false;
    }

    public function addNewsletterSubscription($data)
    {
        $email = isset($data['email']) ? trim((string)$data['email']) : '';
        if ($email === '') {
            return false;
        }

        $existing = $this->db->get_where('newsletter_subscribers', array('email' => $email))->row_array();
        if (!empty($existing)) {
            return false;
        }

        return $this->db->insert('newsletter_subscribers', $data);
    }

    public function getAllDemoRequests()
    {
        return $this->db
            ->from('demo_requests')
            ->order_by('id', 'DESC')
            ->get()
            ->result_array();
    }

    public function getDashboardStats()
    {
        $stats = array(
            'total' => (int)$this->db->count_all('demo_requests'),
            'acceptée' => 0,
            'nouvelle' => 0,
            'refusée' => 0
        );

        $result = $this->db
            ->select('LOWER(status) as status, COUNT(*) as total')
            ->from('demo_requests')
            ->group_by('status')
            ->get()
            ->result_array();

        foreach ($result as $row) {
            $status = strtolower(trim((string)($row['status'] ?? '')));
            if ($status === 'acceptée' || $status === 'acceptee' || $status === 'accepted') {
                $stats['acceptée'] = (int)($row['total'] ?? 0);
            } elseif ($status === 'refusée' || $status === 'refusee' || $status === 'refused') {
                $stats['refusée'] = (int)($row['total'] ?? 0);
            } elseif ($status === 'nouvelle' || $status === 'pending' || $status === 'en attente' || $status === 'en_attente') {
                $stats['nouvelle'] = (int)($row['total'] ?? 0);
            }
        }

        return $stats;
    }

    public function getDemoRequestById($id)
    {
        $id = (int)$id;
        if ($id <= 0) {
            return null;
        }

        return $this->db->where('id', $id)->get('demo_requests')->row_array();
    }

    public function updateDemoRequest($id, $data)
    {
        $id = (int)$id;
        if ($id <= 0 || empty($data)) {
            return false;
        }

        return $this->db->where('id', $id)->update('demo_requests', $data);
    }

    public function deleteDemoRequest($id)
    {
        return $this->db->where('id', (int)$id)->delete('demo_requests');
    }
}
