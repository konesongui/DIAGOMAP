<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Leaverequest_model extends MY_model {

    public function staff_leave_request($id = null) {

        if ($id != null) {
            $this->db->where("staff_leave_request.staff_id", $id);
        }

        $query = $this->db->select('staff.name,staff.surname,staff.employee_id,staff_leave_request.*,leave_types.type')->join("staff", "staff.id = staff_leave_request.staff_id")->join("leave_types", "leave_types.id = staff_leave_request.leave_type_id")->where("staff.is_active", "1")->order_by("staff_leave_request.id", "desc")->get("staff_leave_request");

        return $query->result_array();
    }

    public function user_leave_request($id = null) {


        $query = $this->db->select('staff.name,staff.surname,staff.employee_id,staff_leave_request.*,leave_types.type')->join("staff", "staff.id = staff_leave_request.staff_id")->join("leave_types", "leave_types.id = staff_leave_request.leave_type_id")->where("staff.is_active", "1")->where("staff.id", $id)->order_by("staff_leave_request.id", "desc")->get("staff_leave_request");

        return $query->result_array();
    }

    public function allotedLeaveType($id) {

        $query = $this->db->select('staff_leave_details.*,leave_types.type,leave_types.id as typeid')->where(array('staff_id' => $id))->join("leave_types", "staff_leave_details.leave_type_id = leave_types.id")->get("staff_leave_details");

        return $query->result_array();
    }

    public function myallotedLeaveType_290626($id, $leave_type_id)
    {
        // Vérifie si une ligne existe déjà dans staff_leave_details pour cet employé et ce type
        $query = $this->db->select('staff_leave_details.*, leave_types.type, leave_types.id as typeid, leave_types.ndays')
            ->where(array('staff_id' => $id, 'leave_types.id' => $leave_type_id))
            ->join("leave_types", "staff_leave_details.leave_type_id = leave_types.id", "right")
            ->get("staff_leave_details");

        $result = $query->row_array();

        // Si aucun congé enregistré pour ce type, mais il existe dans leave_types
        if (empty($result['alloted_leave']) && isset($result['ndays'])) {
            $result['alloted_leave'] = $result['ndays'];
        }

        return $result;
    }


    public function myallotedLeaveType_old($id, $leave_type_id) {

        $query = $this->db->select('staff_leave_details.*,leave_types.type,leave_types.id as typeid')->where(array('staff_id' => $id, 'leave_types.id' => $leave_type_id))->join("leave_types", "staff_leave_details.leave_type_id = leave_types.id")->get("staff_leave_details");

        return $query->row_array();
    }

    public function countLeavesData($staff_id, $leave_type_id) {

        $query1 = $this->db->select('sum(leave_days) as approve_leave')->where(array('staff_id' => $staff_id, 'status!=' => 'disapprove', 'leave_type_id' => $leave_type_id))->get("staff_leave_request");
        return $query1->row_array();
    }

    public function changeLeaveStatus($data, $staff_id) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $staff_id)->update("staff_leave_request", $data);
        $message = UPDATE_RECORD_CONSTANT . " On staff leave request id " . $staff_id;
        $action = "Update";
        $record_id = $staff_id;
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

    public function getLeaveSummary() {

        $query = $this->db->select('*')->get("staff");

        return $query->result_array();
    }

    public function leave_remove($id) {

        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('staff_leave_request');
        $message = DELETE_RECORD_CONSTANT . " On staff leave request id " . $id;
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

    // Dans le modèle (ex: Leaverequest_model)

    /**
     * Récupère l'ID du type "Congé annuel"
     */
    public function get_conge_annuel_type_id() {
        $this->db->select('id');
        $this->db->where('LOWER(type)', 'congé annuel');
        $query = $this->db->get('leave_types');
        if ($query->num_rows() > 0) {
            return $query->row()->id;
        }
        return null; // ou créez le type automatiquement
    }

    /**
     * Calcule les jours de congé annuel acquis pour un employé à une date donnée
     */
    public function calculer_conge_annuel_acquis($staff_id, $date_reference = null) {
        if (!$date_reference) $date_reference = date('Y-m-d');

        // Récupérer l'employé
        $this->db->select('date_of_joining');
        $this->db->where('id', $staff_id);
        $staff = $this->db->get('staff')->row();
        if (!$staff || empty($staff->date_of_joining)) return 0;

        $date_embauche = $staff->date_of_joining;
        $diff = date_diff(date_create($date_embauche), date_create($date_reference));
        $mois_travailles = ($diff->y * 12) + $diff->m;

        // Acquisition de base : 2,2 jours par mois
        $jours_acquis = floor($mois_travailles * 2.2);

        // Jours supplémentaires pour ancienneté
        $annees_anciennete = $diff->y;
        $config = $this->config->item('anciennete_supplement');
        if ($config) {
            $supplement = 0;
            foreach ($config as $seuil => $jours) {
                if ($annees_anciennete >= $seuil) {
                    $supplement = max($supplement, $jours);
                }
            }
            $jours_acquis += $supplement;
        }

        return $jours_acquis;
    }

    /**
     * Récupère le nombre de jours de congé annuel déjà pris (approuvés)
     */
    public function get_jours_conge_annuel_pris($staff_id) {
        $conge_annuel_id = $this->get_conge_annuel_type_id();
        if (!$conge_annuel_id) return 0;

        $this->db->select_sum('leave_days');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('leave_type_id', $conge_annuel_id);
        $this->db->where('status', 'approve');
        $query = $this->db->get('staff_leave_request');
        return (int) $query->row()->leave_days;
    }

    /**
     * Surcharge de la méthode myallotedLeaveType pour gérer le congé annuel
     */
    // models/Staff_model.php (ou Leaverequest_model)

    public function myallotedLeaveType($staff_id, $leave_type_id) {
        // Vérifier si le type demandé est "Congé annuel"
        $type = $this->db->get_where('leave_types', ['id' => $leave_type_id])->row();
        if ($type && strtolower($type->type) == 'congé annuel') {
            $acquis = $this->calculer_conge_annuel_acquis($staff_id);
            $pris = $this->get_jours_conge_annuel_pris($staff_id);
            $solde = $acquis - $pris;
            if ($solde < 0) $solde = 0;
            return ['alloted_leave' => $solde];
        } else {
            // Pour les autres types (maternité, etc.), comportement existant.
            // Si vous avez une table leave_allotment, interrogez-la
            $query = $this->db->get_where('leave_allotment', [
                'staff_id' => $staff_id,
                'leave_type_id' => $leave_type_id
            ]);
            $result = $query->row_array();
            if ($result) {
                return $result;
            } else {
                // Fallback : on prend le ndays par défaut depuis leave_types
                $type = $this->db->get_where('leave_types', ['id' => $leave_type_id])->row();
                return ['alloted_leave' => $type ? $type->ndays : 0];
            }
        }
    }

    function addLeaveRequest($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {

            $this->db->where("id", $data["id"]);
            $this->db->update("staff_leave_request", $data);
            $message = UPDATE_RECORD_CONSTANT . " On staff leave request id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
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
        } else {

            $this->db->insert("staff_leave_request", $data);
            $id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On staff leave request id " . $id;
            $action = "Insert";
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
    }

    /**
     * Récupère les congés pour le calendrier avec filtres optionnels
     * @param int|null $staff_id
     * @param int|null $leave_type_id
     * @param string|null $status
     * @param string $start_date
     * @param string $end_date
     * @return array
     */
    public function get_calendar_events($staff_id = null, $leave_type_id = null, $status = null, $start_date = null, $end_date = null) {
        $this->db->select('
        slr.id,
        slr.staff_id,
        slr.leave_type_id,
        slr.leave_from,
        slr.leave_to,
        slr.leave_days,
        slr.status,
        slr.employee_remark,
        slr.admin_remark,
        CONCAT(s.name, " ", s.surname) AS employee_name,
        lt.type AS leave_type
    ');
        $this->db->from('staff_leave_request slr');
        $this->db->join('staff s', 's.id = slr.staff_id', 'left');
        $this->db->join('leave_types lt', 'lt.id = slr.leave_type_id', 'left');

        // Filtres
        if ($staff_id) {
            $this->db->where('slr.staff_id', $staff_id);
        }
        if ($leave_type_id) {
            $this->db->where('slr.leave_type_id', $leave_type_id);
        }
        if ($status) {
            $this->db->where('slr.status', $status);
        } else {
            // Par défaut, on n'affiche que les approuvés + en attente (pas les refusés)
            $this->db->where_in('slr.status', ['approve', 'pending']);
        }

        // Période (congés qui chevauchent la période demandée)
        if ($start_date && $end_date) {
            $this->db->where('slr.leave_from <=', $end_date);
            $this->db->where('slr.leave_to >=', $start_date);
        }

        $this->db->order_by('slr.leave_from', 'ASC');
        $query = $this->db->get();

        return $query->result_array();
    }

}

?>