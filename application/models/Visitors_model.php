<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class visitors_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_session_name = $this->setting_model->getCurrentSessionName();
        $this->start_month = $this->setting_model->getStartMonth();
    }

    // ========================================== //
    // MÉTHODE PRIVÉE POUR APPLIQUER LE FILTRE    //
    // ========================================== //
    private function _apply_entreprise_filter($table = null) {
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (empty($entreprise_id)) {
            return;
        }

        $table_prefix = $table ? $table . '.' : '';
        $table_name = $table ? $table : 'visitors_book';
        $columns = $this->db->list_fields($table_name);
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        if ($hasEntrepriseId) {
            $this->db->where($table_prefix . 'entreprise_id', $entreprise_id);
        }
    }

    // ========================================== //
    // VÉRIFIER SI LA TABLE A LA COLONNE          //
    // ========================================== //
    private function _has_entreprise_column($table) {
        if (empty($table)) {
            return false;
        }
        $columns = $this->db->list_fields($table);
        return in_array('entreprise_id', $columns);
    }

    function add($data) {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Ajout de l'entreprise_id si non fourni
        if (!isset($data['entreprise_id']) || empty($data['entreprise_id'])) {
            $entreprise_id = $this->session->userdata('entreprise_id');
            if ($this->_has_entreprise_column('visitors_book') && !empty($entreprise_id)) {
                $data['entreprise_id'] = $entreprise_id;
            }
        }

        $this->db->insert('visitors_book', $data);
        $query = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On  visitors  id " . $query;
        $action = "Insert";
        $record_id = $query;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    function ads($data) {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Ajout de l'entreprise_id si non fourni
        if (!isset($data['entreprise_id']) || empty($data['entreprise_id'])) {
            $entreprise_id = $this->session->userdata('entreprise_id');
            if ($this->_has_entreprise_column('recrutement') && !empty($entreprise_id)) {
                $data['entreprise_id'] = $entreprise_id;
            }
        }

        $this->db->insert('recrutement', $data);
        $query = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On  recrutement  id " . $query;
        $action = "Insert";
        $record_id = $query;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    function categorie($data) {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Ajout de l'entreprise_id si non fourni
        if (!isset($data['entreprise_id']) || empty($data['entreprise_id'])) {
            $entreprise_id = $this->session->userdata('entreprise_id');
            if ($this->_has_entreprise_column('categorie_salaire') && !empty($entreprise_id)) {
                $data['entreprise_id'] = $entreprise_id;
            }
        }

        $this->db->insert('categorie_salaire', $data);
        $query = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On  categorie  id " . $query;
        $action = "Insert";
        $record_id = $query;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    function ad($data) {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Ajout de l'entreprise_id si non fourni
        if (!isset($data['entreprise_id']) || empty($data['entreprise_id'])) {
            $entreprise_id = $this->session->userdata('entreprise_id');
            if ($this->_has_entreprise_column('projets') && !empty($entreprise_id)) {
                $data['entreprise_id'] = $entreprise_id;
            }
        }

        $this->db->insert('projets', $data);
        $query = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On  projets  id " . $query;
        $action = "Insert";
        $record_id = $query;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    function adds($data) {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Ajout de l'entreprise_id si non fourni
        if (!isset($data['entreprise_id']) || empty($data['entreprise_id'])) {
            $entreprise_id = $this->session->userdata('entreprise_id');
            if ($this->_has_entreprise_column('file') && !empty($entreprise_id)) {
                $data['entreprise_id'] = $entreprise_id;
            }
        }

        $this->db->insert('file', $data);
        $query = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On  file  id " . $query;
        $action = "Insert";
        $record_id = $query;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function getPurpose() {
        $this->db->select('*');
        $this->db->from('visitors_purpose');

        // Appliquer le filtre entreprise si la colonne existe
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('visitors_purpose')) {
            $this->db->where('visitors_purpose.entreprise_id', $entreprise_id);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    public function visitors_list($id = null) {
        $this->db->select('*');
        $this->db->from('visitors_book');

        // Appliquer le filtre entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('visitors_book')) {
            $this->db->where('visitors_book.entreprise_id', $entreprise_id);
        }

        if ($id != null) {
            $this->db->where('visitors_book.id', $id);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->order_by('visitors_book.id', 'desc');
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function visitor_list($id = null) {
        $this->db->select('*');
        $this->db->from('projets');

        // Appliquer le filtre entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('projets')) {
            $this->db->where('projets.entreprise_id', $entreprise_id);
        }

        if ($id != null) {
            $this->db->where('projets.id', $id);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->order_by('projets.id', 'desc');
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function visitors_lists($id = null) {
        $this->db->select('*');
        $this->db->from('file');

        // Appliquer le filtre entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('file')) {
            $this->db->where('file.entreprise_id', $entreprise_id);
        }

        if ($id != null) {
            $this->db->where('file.id', $id);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->order_by('file.id', 'desc');
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function visitors_listes($id = null) {
        $this->db->select('*');
        $this->db->from('recrutement');

        // Appliquer le filtre entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('recrutement')) {
            $this->db->where('recrutement.entreprise_id', $entreprise_id);
        }

        if ($id != null) {
            $this->db->where('recrutement.id', $id);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->order_by('recrutement.id', 'desc');
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function categorie_listes($id = null) {
        $this->db->select('*');
        $this->db->from('categorie_salaire');

        // Appliquer le filtre entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('categorie_salaire')) {
            $this->db->where('categorie_salaire.entreprise_id', $entreprise_id);
        }

        if ($id != null) {
            $this->db->where('categorie_salaire.id', $id);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->order_by('categorie_salaire.id', 'desc');
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    // 🔹 Récupérer tous les employés avec leurs catégories
    public function get_staff_with_categorie($id = null) {
        $this->db->select('staff.*, categorie_salaire.categorie, categorie_salaire.salaire');
        $this->db->from('staff');
        $this->db->join('categorie_salaire', 'staff.categorie_salaire = categorie_salaire.id', 'left');

        // Appliquer le filtre entreprise sur staff
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id)) {
            if ($this->_has_entreprise_column('staff')) {
                $this->db->where('staff.entreprise_id', $entreprise_id);
            }
            if ($this->_has_entreprise_column('categorie_salaire')) {
                $this->db->where('categorie_salaire.entreprise_id', $entreprise_id);
            }
        }

        if ($id != null) {
            $this->db->where('staff.id', $id);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->order_by('staff.id', 'desc');
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function deletes($id) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('file')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('file')->row_array();

        if (!$check) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Accès non autorisé</div>');
            redirect('admin/files');
            return false;
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        $this->db->delete('file');
        $message = DELETE_RECORD_CONSTANT . " On  file  id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/files');
    }

    public function deletecate($id) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('categorie_salaire')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('categorie_salaire')->row_array();

        if (!$check) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Accès non autorisé</div>');
            redirect('admin/categorie');
            return false;
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        $this->db->delete('categorie_salaire');
        $message = DELETE_RECORD_CONSTANT . " On  categorie  id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/categorie');
    }

    public function deleted($id) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('projets')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('projets')->row_array();

        if (!$check) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Accès non autorisé</div>');
            redirect('admin/projets');
            return false;
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        $this->db->delete('projets');
        $message = DELETE_RECORD_CONSTANT . " On  projets  id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/projets');
    }

    public function delete($id) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('visitors_book')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('visitors_book')->row_array();

        if (!$check) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Accès non autorisé</div>');
            redirect('admin/visitors');
            return false;
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        $this->db->delete('visitors_book');
        $message = DELETE_RECORD_CONSTANT . " On  visitors  id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/visitors');
    }

    public function updated($id, $data) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('projets')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('projets')->row_array();

        if (!$check) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Accès non autorisé</div>');
            redirect('admin/projets');
            return false;
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        $this->db->update('projets', $data);

        $message = UPDATE_RECORD_CONSTANT . " On  projets id " . $id;
        $action = "Update";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
        redirect('admin/projets');
    }

    public function updates($id, $data) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('file')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('file')->row_array();

        if (!$check) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Accès non autorisé</div>');
            redirect('admin/files');
            return false;
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        $this->db->update('file', $data);

        $message = UPDATE_RECORD_CONSTANT . " On  file id " . $id;
        $action = "Update";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
        redirect('admin/files');
    }

    public function updatede($id, $data) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('recrutement')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('recrutement')->row_array();

        if (!$check) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Accès non autorisé</div>');
            redirect('admin/recrutement');
            return false;
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        $this->db->update('recrutement', $data);

        $message = UPDATE_RECORD_CONSTANT . " On  recrutement id " . $id;
        $action = "Update";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
        redirect('admin/recrutement');
    }

    public function updatecat($id, $data) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('categorie_salaire')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('categorie_salaire')->row_array();

        if (!$check) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Accès non autorisé</div>');
            redirect('admin/categorie');
            return false;
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        $this->db->update('categorie_salaire', $data);

        $message = UPDATE_RECORD_CONSTANT . " On  categorie id " . $id;
        $action = "Update";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
        redirect('admin/categorie');
    }

    public function update($id, $data) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('visitors_book')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('visitors_book')->row_array();

        if (!$check) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Accès non autorisé</div>');
            redirect('admin/visitors');
            return false;
        }

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        $this->db->update('visitors_book', $data);

        $message = UPDATE_RECORD_CONSTANT . " On  visitors id " . $id;
        $action = "Update";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
        redirect('admin/visitors');
    }

    public function image_add($visitor_id, $image) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('visitors_book')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $visitor_id);
        $check = $this->db->get('visitors_book')->row_array();

        if ($check) {
            $array = array('id' => $visitor_id);
            $this->db->set('image', $image);
            $this->db->where($array);
            $this->db->update('visitors_book');
        }
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
    }

    public function image_ad($visitor_id, $image) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('projets')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $visitor_id);
        $check = $this->db->get('projets')->row_array();

        if ($check) {
            $array = array('id' => $visitor_id);
            $this->db->set('image', $image);
            $this->db->where($array);
            $this->db->update('projets');
        }
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
    }

    public function image_adds($visitor_id, $image) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('file')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $visitor_id);
        $check = $this->db->get('file')->row_array();

        if ($check) {
            $array = array('id' => $visitor_id);
            $this->db->set('image', $image);
            $this->db->where($array);
            $this->db->update('file');
        }
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
    }

    public function image_update($visitor_id, $image) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('visitors_book')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $visitor_id);
        $check = $this->db->get('visitors_book')->row_array();

        if ($check) {
            $array = array('id' => $visitor_id);
            $this->db->set('image', $image);
            $this->db->where($array);
            $this->db->update('visitors_book');
        }
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
    }

    public function image_updates($visitor_id, $image) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('file')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $visitor_id);
        $check = $this->db->get('file')->row_array();

        if ($check) {
            $array = array('id' => $visitor_id);
            $this->db->set('image', $image);
            $this->db->where($array);
            $this->db->update('file');
        }
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
    }

    public function image_updated($visitor_id, $image) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('projets')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $visitor_id);
        $check = $this->db->get('projets')->row_array();

        if ($check) {
            $array = array('id' => $visitor_id);
            $this->db->set('image', $image);
            $this->db->where($array);
            $this->db->update('projets');
        }
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
    }

    public function image_delete($id, $img_name) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('categorie_salaire')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('categorie_salaire')->row_array();

        if ($check) {
            $file = "./uploads/front_office/visitors/" . $img_name;
            if (file_exists($file)) {
                unlink($file);
            }
            $this->db->where('id', $id);
            $this->db->delete('categorie_salaire');
        }

        $controller_name = $this->uri->segment(2);
        $this->session->set_flashdata('msg', '<div class="alert alert-success"> ' . ucfirst($controller_name) . '' . $this->lang->line('success_message') . '</div>');
        redirect('admin/' . $controller_name);
    }

    public function image_deleted($id, $img_name) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('projets')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('projets')->row_array();

        if ($check) {
            $file = "./uploads/front_office/projets/" . $img_name;
            if (file_exists($file)) {
                unlink($file);
            }
            $this->db->where('id', $id);
            $this->db->delete('projets');
        }

        $controller_name = $this->uri->segment(2);
        $this->session->set_flashdata('msg', '<div class="alert alert-success"> ' . ucfirst($controller_name) . '' . $this->lang->line('success_message') . '</div>');
        redirect('admin/' . $controller_name);
    }

    public function images_delete($id, $img_name) {
        // Vérifier que l'enregistrement appartient à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('file')) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('file')->row_array();

        if ($check) {
            $file = "./uploads/front_office/files/" . $img_name;
            if (file_exists($file)) {
                unlink($file);
            }
            $this->db->where('id', $id);
            $this->db->delete('file');
        }

        $controller_name = $this->uri->segment(2);
        $this->session->set_flashdata('msg', '<div class="alert alert-success"> ' . ucfirst($controller_name) . '' . $this->lang->line('success_message') . '</div>');
        redirect('admin/' . $controller_name);
    }

    // ========================================== //
    // Récupérer un visiteur par ID               //
    // ========================================== //
    function get_visitor_by_id($id) {
        $this->db->select('*');
        $this->db->from('visitors_book');
        $this->db->where('id', $id);

        // Appliquer le filtre entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('visitors_book')) {
            $this->db->where('visitors_book.entreprise_id', $entreprise_id);
        }

        $query = $this->db->get();
        return $query->row_array();
    }

    // ========================================== //
    // Filtrer les visiteurs                      //
    // ========================================== //
    function get_filtered_visitors($purpose = '', $date_from = '', $date_to = '', $status = '') {
        $this->db->select('*');
        $this->db->from('visitors_book');

        // Appliquer le filtre entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && $this->_has_entreprise_column('visitors_book')) {
            $this->db->where('visitors_book.entreprise_id', $entreprise_id);
        }

        if (!empty($purpose)) {
            $this->db->like('purpose', $purpose);
        }

        if (!empty($date_from)) {
            $this->db->where('date >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('date <=', $date_to);
        }

        if ($status == 'active') {
            $this->db->where('out_time IS NULL OR out_time = ""');
        } elseif ($status == 'completed') {
            $this->db->where('out_time IS NOT NULL AND out_time != ""');
        }

        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }


}