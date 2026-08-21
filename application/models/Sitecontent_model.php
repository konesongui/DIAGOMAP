<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sitecontent_model extends CI_Model {

    protected $storage_dir;
    protected $json_path;
    protected $table = 'site_content';

    public function __construct()
    {
        parent::__construct();
        $this->storage_dir = FCPATH . 'uploads\site_content\\';
        if (!is_dir($this->storage_dir)) {
            @mkdir($this->storage_dir, 0755, true);
        }
        if (!is_dir($this->storage_dir . 'images\\')) {
            @mkdir($this->storage_dir . 'images\\', 0755, true);
        }
        if (!is_dir($this->storage_dir . 'videos\\')) {
            @mkdir($this->storage_dir . 'videos\\', 0755, true);
        }
        $this->json_path = $this->storage_dir . 'content.json';

        // Ensure DB table exists (will run only if DB is available)
        if ($this->db) {
            $this->ensureTableExists();

            // If DB exists, try migrating existing JSON into DB for current entreprise_id
            $entreprise_id = $this->getCurrentEntrepriseId();
            $this->migrateJsonToDbIfNeeded($entreprise_id);
        }
    }

    protected function getCurrentEntrepriseId()
    {
        // Prefer customlib user data when available
        if (isset($this->customlib) && method_exists($this->customlib, 'getUserData')) {
            $userdata = $this->customlib->getUserData();
            if (is_array($userdata) && isset($userdata['entreprise_id'])) {
                return (int)$userdata['entreprise_id'];
            }
        }
        $e = $this->session->userdata('entreprise_id');
        return ($e !== null) ? (int)$e : 0;
    }

    /**
     * Create the site_content table if it does not exist yet.
     */
    protected function ensureTableExists()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `entreprise_id` INT(11) NOT NULL DEFAULT 0,
            `content` LONGTEXT NOT NULL,
            `version` INT(11) NOT NULL DEFAULT 1,
            `created_at` DATETIME DEFAULT NULL,
            `updated_at` DATETIME DEFAULT NULL,
            PRIMARY KEY (`id`),
            INDEX (`entreprise_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        try {
            $this->db->query($sql);
        } catch (Exception $e) {
            // Quietly ignore DB creation errors; fallback to file storage
            log_message('error', 'Sitecontent_model::ensureTableExists error: ' . $e->getMessage());
        }
    }

    /**
     * If there's no DB row for this entreprise, migrate the existing JSON file into DB as version 1.
     */
    protected function migrateJsonToDbIfNeeded($entreprise_id = 0)
    {
        try {
            $exists = $this->db->where('entreprise_id', $entreprise_id)->count_all_results($this->table) > 0;
            if ($exists) return;

            if (file_exists($this->json_path)) {
                $raw = file_get_contents($this->json_path);
                $data = json_decode($raw, true);
                if (is_array($data)) {
                    $now = date('Y-m-d H:i:s');
                    $this->db->insert($this->table, [
                        'entreprise_id' => $entreprise_id,
                        'content' => json_encode($data, JSON_UNESCAPED_UNICODE),
                        'version' => 1,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Sitecontent_model::migrateJsonToDbIfNeeded error: ' . $e->getMessage());
        }
    }

    /**
     * Get content. Prefer DB-stored row for current entreprise_id, fallback to JSON file, else default.
     */
    public function getContent()
    {
        $entreprise_id = $this->getCurrentEntrepriseId();

        // Try DB first
        if ($this->db) {
            try {
                $row = $this->db->where('entreprise_id', $entreprise_id)->order_by('version', 'DESC')->order_by('id', 'DESC')->limit(1)->get($this->table)->row();
                if ($row && !empty($row->content)) {
                    $data = json_decode($row->content, true);
                    if (is_array($data)) return $data;
                }
            } catch (Exception $e) {
                log_message('error', 'Sitecontent_model::getContent db read error: ' . $e->getMessage());
            }
        }

        // Then fallback to file
        if (file_exists($this->json_path)) {
            $raw = file_get_contents($this->json_path);
            $data = json_decode($raw, true);
            if (is_array($data)) return $data;
        }

        // default structure
        return [
            'menus' => [
                ['title' => 'Accueil', 'url' => '', 'ext' => false, 'new_tab' => false],
                ['title' => 'À propos', 'url' => 'page/about', 'ext' => false, 'new_tab' => false],
                ['title' => 'Nos modules', 'url' => 'page/modules', 'ext' => false, 'new_tab' => false],
                ['title' => 'Demander un démo', 'url' => 'admin/demorequests', 'ext' => false, 'new_tab' => false]
            ],
            'blocks' => []
        ];
    }

    /**
     * Save content to DB (preferred) and write JSON backup file.
     * Uses entreprise_id scoping and versioning: inserts a new row with incremented version.
     * Returns true on success.
     */
    public function saveContent($data)
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $ok_db = false;

        $entreprise_id = $this->getCurrentEntrepriseId();

        if ($this->db) {
            try {
                // Find latest version for this entreprise
                $latest = $this->db->select('version')->where('entreprise_id', $entreprise_id)->order_by('version', 'DESC')->limit(1)->get($this->table)->row();
                $new_version = ($latest && isset($latest->version)) ? ((int)$latest->version + 1) : 1;
                $now = date('Y-m-d H:i:s');

                $ok_db = $this->db->insert($this->table, [
                    'entreprise_id' => $entreprise_id,
                    'content' => $json,
                    'version' => $new_version,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            } catch (Exception $e) {
                log_message('error', 'Sitecontent_model::saveContent db write error: ' . $e->getMessage());
                $ok_db = false;
            }
        }

        // Always write JSON backup to file as well
        $ok_file = @file_put_contents($this->json_path, $json) !== false;

        return ($ok_db || $ok_file);
    }

    public function saveUploadedFile($fieldname, $type = 'image')
    {
        if (!isset($_FILES[$fieldname])) return null;
        $file = $_FILES[$fieldname];
        if ($file['error'] !== UPLOAD_ERR_OK) return null;
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safe = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', basename($file['name']));
        $dest_dir = ($type === 'video') ? $this->storage_dir . 'videos\\' : $this->storage_dir . 'images\\';
        $dest = $dest_dir . $safe;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return str_replace(FCPATH, '', $dest);
        }
        return null;
    }

}
