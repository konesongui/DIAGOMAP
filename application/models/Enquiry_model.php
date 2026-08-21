<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class enquiry_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session      = $this->setting_model->getCurrentSession();
        $this->current_session_name = $this->setting_model->getCurrentSessionName();
        $this->start_month          = $this->setting_model->getStartMonth();
    }

    public function getclasses($id = null)
    {
        $this->db->select()->from('classes');
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

    public function get_enquiry_type()
    {
        $this->db->select('*');
        $this->db->from('enquiry_type');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getComplaintSource()
    {
        $this->db->select('*');
        $this->db->from('source');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getComplaintType()
    {
        $this->db->select('*');
        $this->db->from('complaint_type');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_reference()
    {
        $this->db->select('*');
        $this->db->from('reference');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert('enquiry', $data);
        $id        = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On  enquiry id " . $id;
        $action    = "Insert";
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

    public function get($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('enquiry');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return false;
    }


    public function addtraining_request($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert('trainingreq', $data);
        $id        = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On  enquiry id " . $id;
        $action    = "Insert";
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


    public function adds($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert('projects', $data);
        $id        = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On  projects id " . $id;
        $action    = "Insert";
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

    public function getenquiry_list($id = null, $status = 'active')
    {

        if (!empty($id) and !empty($status)) {

            $this->db->where("enquiry.id", $id);
        }

        $query = $this->db->select('enquiry.*,classes.class as classname')->
            join("classes", "enquiry.class = classes.id", "left")->
            where('enquiry.status', $status)->order_by("enquiry.id", "desc")->
            get("enquiry");

        if (!empty($id) and !empty($status)) {

            return $query->row_array();
        } else {

            return $query->result_array();
        }
    }




    public function getrequest_list_by_user($staff_id)
    {
        $this->db->select('*');
        $this->db->from('trainingreq'); // ← adapte si ton nom de table est différent
        $this->db->where('created_by', $staff_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getrequest_list($id = null, $status = 'active')
    {

        if (!empty($id) and !empty($status)) {

            $this->db->where("trainingreq.id", $id);
        }

        $query = $this->db->select('trainingreq.*,classes.class as classname')->
        join("classes", "trainingreq.class = classes.id", "left")->
        where('trainingreq.status', $status)->order_by("trainingreq.id", "desc")->
        get("trainingreq");

        if (!empty($id) and !empty($status)) {

            return $query->row_array();
        } else {

            return $query->result_array();
        }
    }





    public function getprojects_list($id = null, $status = 'active')
    {

        if (!empty($id) and !empty($status)) {

            $this->db->where("projects.id", $id);
        }

        $query = $this->db->select('projects.*,classes.class as classname')->
        join("classes", "projects.class = classes.id", "left")->
        where('projects.status', $status)->order_by("projects.id", "desc")->
        get("projects");

        if (!empty($id) and !empty($status)) {

            return $query->row_array();
        } else {

            return $query->result_array();
        }
    }

    public function getfollow_list($id = null, $status = 'active')
    {
        $this->db->select('
    p.id,
    p.titre,
    p.enquiry_id,
    pa.employee_id,
    p.start_date,
    p.chef_projet,
    p.statut,
     p.client,
     p.montant,
    

   pa.project_id,
    p.due_date,
    p.priority,
    GROUP_CONCAT(pa.employee_id SEPARATOR ", ") AS assigned_employees
');
        $this->db->from('projects_up p','p.enquiry_id','p.chef_projet','p.statut','p.client','p.montant');
        $this->db->join('project_assignments pa', 'pa.project_id = p.id', 'left');
        $this->db->join('staff s', 's.id = pa.employee_id', 'left');



        if ($id != null) {
            $this->db->where('p.id', $id);
        }

        if ($status != null) {
            // À activer seulement si status existe dans projects_up
            // $this->db->where('p.status', $status);
        }

        $this->db->group_by('p.id');
        $this->db->order_by('p.id', 'DESC');

        $query = $this->db->get();

        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getFollowByEnquiry($id)
    {

        $query = $this->db->select("*")->where("enquiry_id", $id)->order_by("id", "desc")->get("follow_up");

        return $query->row_array();
    }

    public function getFollowByProjects($id)
    {

        $query = $this->db->select("*")->where("enquiry_id", $id)->order_by("id", "desc")->get("projects_up");

        return $query->row_array();
    }

    public function searchTrainingreqByUser_old($user_id, $source, $date_from, $date_to, $status) {
        $this->db->where("created_by", $user_id);
        $this->db->where("status", $status);
        $this->db->where("source", $source);
        $this->db->where("date >=", $date_from);
        $this->db->where("date <=", $date_to);
        $query = $this->db->get("trainingreq");
        return $query->result_array();
    }

    public function searchTrainingreq($source, $date_from, $date_to, $status = null, $staff_id = null)
    {
        $this->db->select('*');
        $this->db->from('trainingreq');

        if (!empty($source)) {
            $this->db->where('assigned', $source);
        }

        if (!empty($date_from) && !empty($date_to)) {
            $this->db->where('date >=', $date_from);
            $this->db->where('date <=', $date_to);
        }

        if (!empty($status)) {
            $this->db->where('status', $status);
        }

        if (!empty($staff_id)) {
            $this->db->where('created_by', $staff_id);
        }

        $query = $this->db->get();
        return $query->result_array();
    }



    public function getfollow_up_list($enquiry_id, $follow_up = null)
    {
        $this->db->select()->from('follow_up');
        if ($follow_up != null) {
            $this->db->where('follow_up.id', $follow_up);
            $this->db->where('follow_up.enquiry_id', $enquiry_id);
            $this->db->order_by('follow_up.id desc');
        } else {
            $this->db->where('follow_up.enquiry_id', $enquiry_id);
            $this->db->order_by('follow_up.id desc');
        }
        $query = $this->db->get();
        if ($follow_up != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function gettraining_up_list($training_id, $follow_up = null)
    {
        $this->db->select()->from('trainingreq_up');
        if ($follow_up != null) {
            $this->db->where('trainingreq_up.id', $follow_up);
            $this->db->where('trainingreq_up.trainingreq_id', $training_id);
            $this->db->order_by('follow_up.id desc');
        } else {
            $this->db->where('trainingreq_up.trainingreq_id', $training_id);
            $this->db->order_by('trainingreq_up.id desc');
        }
        $query = $this->db->get();
        if ($follow_up != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }


    public function getprojects_up_list($projects_id, $projects_up = null)
    {
        $this->db->select()->from('projects_up');
        if ($projects_up != null) {
            $this->db->where('projects_up.id', $projects_up);
            $this->db->where('projects_up.enquiry_id', $projects_id);
            $this->db->order_by('projects_up.id desc');
        } else {
            $this->db->where('projects_up.enquiry_id', $projects_id);
            $this->db->order_by('projects_up.id desc');
        }
        $query = $this->db->get();
        if ($projects_up != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }


    public function add_follow_up($data)
    {
        $this->db->insert('follow_up', $data);
    }

    public function add_trainingreq_up($data)
    {
        $this->db->insert('trainingreq_up', $data);
    }
    public function add_projects_up($data)
    {
        $this->db->insert('projects_up', $data);
        return $this->db->insert_id(); // retourne l'ID de la ligne insérée
    }


    public function add_projects_up_old($data)
    {
        $this->db->insert('projects_up', $data);
    }

    public function follow_up_update($enquiry_id, $follow_up_id, $data)
    {
        $this->db->where('id', $follow_up_id);
        $this->db->where('enquiry_id', $enquiry_id);
        $this->db->update('follow_up', $data);
        redirect('admin/enquiry/follow_up_edit/' . $enquiry_id . '/' . $follow_up_id . '');
    }

    public function projects_up_update($enquiry_id, $follow_up_id, $data)
    {
        $this->db->where('id', $follow_up_id);
        $this->db->where('enquiry_id', $enquiry_id);
        $this->db->update('projects_up', $data);
        redirect('admin/projects/projects_up_edit/' . $enquiry_id . '/' . $follow_up_id . '');
    }





    public function enquiry_training_update($id, $data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->update('trainingreq', $data);
        $message   = UPDATE_RECORD_CONSTANT . " On  training id " . $id;
        $action    = "Update";
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




    public function enquiry_update($id, $data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->update('enquiry', $data);
        $message   = UPDATE_RECORD_CONSTANT . " On  enquiry id " . $id;
        $action    = "Update";
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


    public function projects_update($id, $data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->update('projects', $data);
        $message   = UPDATE_RECORD_CONSTANT . " On  projects id " . $id;
        $action    = "Update";
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

    public function enquiry_delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('enquiry');
        $message   = DELETE_RECORD_CONSTANT . " On  enquiry id " . $id;
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
            //return $return_value;
        }
    }

    public function enquiry_trainingreq_delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('trainingreq');
        $message   = DELETE_RECORD_CONSTANT . " On  training id " . $id;
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
            //return $return_value;
        }
    }


    public function follow_delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('projects_up');
        $message   = DELETE_RECORD_CONSTANT . " On  projects_up id " . $id;
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
            //return $return_value;
        }
    }

    public function projects_delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('projects');
        $message   = DELETE_RECORD_CONSTANT . " On  projects id " . $id;
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
            //return $return_value;
        }
    }

    public function delete_follow_up($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('follow_up');
    }

    public function delete_trainingreq_follow_up($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('trainingreq_up');
    }


    public function delete_projects_up($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('projects_up');
    }

    public function next_follow_up_date($enquiry_id)
    {
        $this->db->select('max(`id`) as id');
        $this->db->from('follow_up');
        $this->db->where('enquiry_id', $enquiry_id);
        $query = $this->db->get();
        $data  = $query->row_array();
        $id    = $data['id'];
        $this->db->select('*');
        $this->db->from('follow_up');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function next_projects_up_date($enquiry_id)
    {
        $this->db->select('max(`id`) as id');
        $this->db->from('projects_up');
        $this->db->where('enquiry_id', $enquiry_id);
        $query = $this->db->get();
        $data  = $query->row_array();
        $id    = $data['id'];
        $this->db->select('*');
        $this->db->from('projects_up');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }


    public function changeStatus($data)
    {

        $this->db->where("id", $data["id"])->update("enquiry", $data);
    }

    public function changetrainingStatus($data)
    {

        $this->db->where("id", $data["id"])->update("trainingreq", $data);
    }

    public function changeprojectsStatus($data)
    {

        $this->db->where("id", $data["id"])->update("projects", $data);
    }
    public function searchProjects($source, $date_from, $date_to, $status = 'active')
    {

        $condition = 0;

        if (!empty($source)) {

            $condition = 1;
            $this->db->where("source", $source);
        }
        if (!empty($status)) {

            if ($status != 'all') {
                $condition = 1;
                $this->db->where("status", $status);
            } else {

                $condition = 1;
            }
        }

        if ((!empty($date_from)) && (!empty($date_to))) {
            $condition = 1;
            $this->db->where("date >= ", $date_from);
            $this->db->where("date <= ", $date_to);
        }

        if ($condition == 0) {

            $this->db->where("projects.status", "active");
        }

        $query = $this->db->select('projects.*,classes.class as classname')->join("classes", "classes.id = projects.class", "left")->get("projects");
        return $query->result_array();
    }
    public function searchEnquiry($source, $date_from, $date_to, $status = 'active')
    {

        $condition = 0;

        if (!empty($source)) {

            $condition = 1;
            $this->db->where("source", $source);
        }
        if (!empty($status)) {

            if ($status != 'all') {
                $condition = 1;
                $this->db->where("status", $status);
            } else {

                $condition = 1;
            }
        }

        if ((!empty($date_from)) && (!empty($date_to))) {
            $condition = 1;
            $this->db->where("date >= ", $date_from);
            $this->db->where("date <= ", $date_to);
        }

        if ($condition == 0) {

            $this->db->where("enquiry.status", "active");
        }

        $query = $this->db->select('enquiry.*,classes.class as classname')->join("classes", "classes.id = enquiry.class", "left")->get("enquiry");
        return $query->result_array();
    }



    public function searchTrainingreq_old($date_from, $date_to, $status, $staff_id = null)
    {
        $this->db->select('*');
        $this->db->from('trainingreq'); // ton nom de table ici

        if (!empty($source)) {
            $this->db->where('assigned');
        }

        if (!empty($date_from) && !empty($date_to)) {
            $this->db->where('date >=', $date_from);
            $this->db->where('date <=', $date_to);
        }

        if (!empty($status)) {
            $this->db->where('status', $status);
        }

        if (!empty($staff_id)) {
            $this->db->where('created_by', $staff_id);
        }

        $query = $this->db->get();
        return $query->result_array();
    }


    public function searchTrainingeq_old($source, $date_from, $date_to, $status = 'active')
    {

        $condition = 0;

        if (!empty($source)) {

            $condition = 1;
            $this->db->where("source", $source);
        }
        if (!empty($status)) {

            if ($status != 'all') {
                $condition = 1;
                $this->db->where("status", $status);
            } else {

                $condition = 1;
            }
        }

        if ((!empty($date_from)) && (!empty($date_to))) {
            $condition = 1;
            $this->db->where("date >= ", $date_from);
            $this->db->where("date <= ", $date_to);
        }

        if ($condition == 0) {

            $this->db->where("trainingreq.status", "active");
        }

        $query = $this->db->select('trainingreq.*,classes.class as classname')->join("classes", "classes.id = trainingreq.class", "left")->get("trainingreq");
        return $query->result_array();
    }

    public function get_approved_enquiry($id) {
        $this->db->where('id', $id);
        $this->db->where('status', 'active');
        $query = $this->db->get('enquiry');
        return $query->row_array();
    }

    /**
     * Récupère une seule enquête par son ID
     * @param int $id
     * @return array|null
     */
    public function get_enquiry_by_id($id) {
        $this->db->select('*');
        $this->db->from('enquiry');
        $this->db->where('id', $id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return null;
    }


    public function getPendingCount() {
        $this->db->where('status', 'pending');
        return $this->db->count_all_results('enquiry');
    }

    /**
     * Récupérer les demandes en attente pour l'affichage
     */
    public function getPendingList($limit = 5) {
        $this->db->select('id, name, contact, source, date, status');
        $this->db->where('status', 'pending');
        $this->db->order_by('date', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get('enquiry');
        return $query->result_array();
    }

    /**
     * Récupérer les demandes en attente par type (permission/demission)
     * Si vous avez un champ 'type' dans votre table
     */
    public function getPendingByType($type = null) {
        $this->db->where('status', 'pending');
        if ($type) {
            $this->db->where('source', $type); // ou 'type' selon votre base
        }
        return $this->db->count_all_results('enquiry');
    }

    /**
     * Récupérer toutes les demandes non lues
     */
    public function getUnreadCount() {
        $this->db->where('is_read', 0);
        return $this->db->count_all_results('enquiry');
    }

    /**
     * Marquer comme lu
     */
    public function markAsRead($id) {
        $this->db->where('id', $id);
        $this->db->update('enquiry', array('is_read' => 1));
        return $this->db->affected_rows();
    }

    /**
     * Marquer toutes les demandes comme lues
     */
    public function markAllAsRead() {
        $this->db->where('status', 'pending');
        $this->db->update('enquiry', array('is_read' => 1));
        return $this->db->affected_rows();
    }


}


