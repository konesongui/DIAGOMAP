<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Objectifs_model extends MY_Model {

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
        $this->db->select()->from('objectifs_commercial');
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


    /**
     * This function will take the post data passed from the controller
     * If id is present, then it will do an update
     * else an insert. One function doing both add and edit.
     * @param $data
     */
    public function add($data)
    {
        // Démarre une transaction pour garantir la cohérence
        $this->db->trans_start();

        if (isset($data['id']) && !empty($data['id'])) {
            // 🔄 Mise à jour de l'enregistrement existant
            $this->db->where('id', $data['id']);
            $this->db->update('objectifs_commercial', $data);

            // 🔧 Journalisation de la mise à jour
            $this->log(
                UPDATE_RECORD_CONSTANT . " sur l'objectif ID : " . $data['id'],
                $data['id'],
                'Update'
            );

            $record_id = $data['id'];
        } else {
            // ➕ Insertion d’un nouvel enregistrement
            $this->db->insert('objectifs_commercial', $data);
            $record_id = $this->db->insert_id();

            // 📝 Journalisation de l'insertion
            $this->log(
                INSERT_RECORD_CONSTANT . " sur l'objectif ID : " . $record_id,
                $record_id,
                'Insert'
            );
        }

        // Termine la transaction
        $this->db->trans_complete();

        // ❌ Vérifie si la transaction a échoué
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback(); // Annule tout changement
            return false;
        }

        // ✅ Retourne l'ID de l'enregistrement inséré ou mis à jour
        return $record_id;
    }



    /**
     * This function will delete the record based on the id
     * @param $id
     */



    public function remove($id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        $this->db->delete('objectifs_commercial');

        $message   = "Suppression de l'objectif commercial ID : " . $id;
        $this->log($message, $id, 'Suppression');

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            log_message('error', 'Erreur de suppression de l’objectif commercial ID : ' . $id);
            return false;
        }

        return true;
    }

    public function getAnnualObjectives() {
        $this->db->order_by('date', 'DESC');
        return $this->db->get('annual_objectives')->result_array();
    }

    public function getAnnualObjective($id) {
        return $this->db->get_where('annual_objectives', array('id' => $id))->row_array();
    }

    public function addAnnualObjective($data) {
        $this->db->insert('annual_objectives', $data);
        return $this->db->insert_id();
    }

    public function updateAnnualObjective($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('annual_objectives', $data);
    }

    public function deleteAnnualObjective($id) {
        $this->db->where('id', $id);
        $this->db->delete('annual_objectives');
    }

    // ------------------ ATTRIBUTIONS ------------------
    public function getAssignments($annual_objective_id) {
        $this->db->where('annual_objective_id', $annual_objective_id);
        $this->db->order_by('start_date', 'ASC');
        return $this->db->get('objective_assignments')->result_array();
    }

    public function getAssignment($id) {
        return $this->db->get_where('objective_assignments', array('id' => $id))->row_array();
    }

    public function addAssignment($data) {
        $this->db->insert('objective_assignments', $data);
        return $this->db->insert_id();
    }

    public function updateAssignment($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('objective_assignments', $data);
    }

    public function deleteAssignment($id) {
        $this->db->where('id', $id);
        $this->db->delete('objective_assignments');
    }





}
