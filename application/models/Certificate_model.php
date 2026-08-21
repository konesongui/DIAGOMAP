<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Certificate_model extends CI_Model
{
    private $table = 'certificate_templates';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Récupère tous les certificats
     */
    public function get_all()
    {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Récupère un certificat par ID
     */
    public function get($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    /**
     * Ajoute un certificat
     */
    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Met à jour un certificat
     */
    public function update($data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $data['id']);
        return $this->db->update($this->table, $data);
    }

    /**
     * Supprime un certificat
     */
    public function delete($id)
    {
        // Supprimer les fichiers associés
        $certificate = $this->get($id);
        if ($certificate) {
            if ($certificate->logo_path && file_exists($certificate->logo_path)) {
                unlink($certificate->logo_path);
            }
            if ($certificate->signature_path && file_exists($certificate->signature_path)) {
                unlink($certificate->signature_path);
            }
        }

        return $this->db->where('id', $id)->delete($this->table);
    }

    /**
     * Met à jour les images
     */
    public function update_images($id, $logo_path = null, $signature_path = null)
    {
        $data = array();
        if ($logo_path) $data['logo_path'] = $logo_path;
        if ($signature_path) $data['signature_path'] = $signature_path;

        if (!empty($data)) {
            $this->db->where('id', $id);
            $this->db->update($this->table, $data);
        }
    }
}