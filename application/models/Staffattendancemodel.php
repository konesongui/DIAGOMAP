<?php

class Staffattendancemodel extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_date = $this->setting_model->getDateYmd();
    }

    public function get($id = null) {
        $this->db->select()->join("staff", "staff.id = staff_attendance.staff_id")->from('staff_attendance');
        $this->db->where("staff.is_active", 1);
        if ($id != null) {
            $this->db->where('staff_attendance.id', $id);
        } else {
            $this->db->order_by('staff_attendance.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getUserType() {

        $query = $this->db->query("select distinct user_type from staff where is_active = 1");

        return $query->result_array();
    }

    public function searchAttendenceUserType($user_type, $date) {

        if ($user_type == "select") {

            $query = $this->db->query("select staff_attendance.id, staff_attendance.staff_attendance_type_id,staff_attendance.remark,staff.name,staff.surname,staff.employee_id,staff.contact_no,staff.email,roles.name as user_type,IFNULL(staff_attendance.date, 'xxx') as date,staff.id as staff_id from staff left join staff_roles on staff_roles.staff_id = staff.id left join roles on staff_roles.role_id = roles.id left join staff_attendance on (staff.id = staff_attendance.staff_id) and staff_attendance.date = " . $this->db->escape($date) . " where staff.is_active = 1");
        } else {

            $query = $this->db->query("select staff_attendance.staff_attendance_type_id,staff_attendance.remark,staff.name,staff.surname,staff.employee_id,staff.contact_no,staff.email,roles.name as user_type,IFNULL(staff_attendance.date, 'xxx') as date, IFNULL(staff_attendance.id, 0) as id, staff.id as staff_id from staff left join staff_roles on (staff.id = staff_roles.staff_id) left join roles on (roles.id = staff_roles.role_id) left join staff_attendance on (staff.id = staff_attendance.staff_id) and staff_attendance.date = " . $this->db->escape($date) . " where roles.name = " . $this->db->escape($user_type) . " and staff.is_active = 1");
        }
        return $query->result_array();
    }

    public function add($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('staff_attendance', $data);
            $message = UPDATE_RECORD_CONSTANT . " On staff attendance id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('staff_attendance', $data);
            $id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On staff attendance id " . $id;
            $action = "Insert";
            $record_id = $id;
            $this->log($message, $record_id, $action);
        }
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

    public function getStaffAttendanceType() {

        $query = $this->db->select('*')->where("is_active", 'yes')->get("staff_attendance_type");

        return $query->result_array();
    }

    public function getAttendanceTypeIdByKey($key, $default_id = 1) {
        $query = $this->db->select('id')
            ->from('staff_attendance_type')
            ->where('LOWER(key_value)', strtolower($key))
            ->where('is_active', 'yes')
            ->limit(1)
            ->get();

        $row = $query->row_array();

        return !empty($row) ? (int)$row['id'] : (int)$default_id;
    }

    public function getStaffForAttendanceByIdentifier($staff_id = null, $employee_id = null, $role_name = null) {
        $this->db->select('staff.id as staff_id, staff.employee_id, staff.name, staff.surname, roles.name as role_name');
        $this->db->from('staff');
        $this->db->join('staff_roles', 'staff_roles.staff_id = staff.id', 'left');
        $this->db->join('roles', 'roles.id = staff_roles.role_id', 'left');
        $this->db->where('staff.is_active', 1);

        if (!empty($staff_id)) {
            $this->db->where('staff.id', (int)$staff_id);
        } elseif (!empty($employee_id)) {
            $this->db->where('staff.employee_id', $employee_id);
        } else {
            return null;
        }

        if (!empty($role_name) && $role_name !== 'select') {
            $this->db->where('roles.name', $role_name);
        }

        $query = $this->db->limit(1)->get();

        return $query->row_array();
    }

    public function getAttendanceByStaffAndDate($staff_id, $date_ymd) {
        $query = $this->db->select('id, staff_attendance_type_id, remark, date')
            ->from('staff_attendance')
            ->where('staff_id', (int)$staff_id)
            ->where('date', $date_ymd)
            ->limit(1)
            ->get();

        return $query->row_array();
    }

    public function searchAttendanceReport($user_type, $date) {

        if ($user_type == "select") {

            $query = $this->db->query("select staff_attendance.staff_attendance_type_id,staff_attendance_type.type as `att_type`,staff_attendance_type.key_value as `key`,staff_attendance.remark,staff.name,staff.surname,staff.employee_id,staff.contact_no,staff.email,roles.name as user_type,IFNULL(staff_attendance.date, 'xxx') as date, IFNULL(staff_attendance.id, 0) as attendence_id, staff.id as id from staff left join staff_attendance on (staff.id = staff_attendance.staff_id) and staff_attendance.date = " . $this->db->escape($date) . " left join staff_attendance_type on staff_attendance_type.id = staff_attendance.staff_attendance_type_id left join staff_roles on staff_roles.staff_id = staff.id left join roles on staff_roles.role_id = roles.id where staff.is_active = 1");
        } else {

            $query = $this->db->query("select staff_attendance.staff_attendance_type_id,staff_attendance_type.type as `att_type`,staff_attendance_type.key_value as `key`,staff_attendance.remark,staff.name,staff.surname,staff.employee_id,staff.contact_no,staff.email,roles.name as user_type,IFNULL(staff_attendance.date, 'xxx') as date, IFNULL(staff_attendance.id, 0) as attendence_id, staff.id as id from staff  left join staff_roles on (staff.id = staff_roles.staff_id) left join roles on (roles.id = staff_roles.role_id) left join staff_attendance on (staff.id = staff_attendance.staff_id) and staff_attendance.date = " . $this->db->escape($date) . " left join staff_attendance_type on staff_attendance_type.id = staff_attendance.staff_attendance_type_id  where roles.name = '" . $user_type . "' and staff.is_active = 1 ");
        }

        return $query->result_array();
    }

    public function attendanceYearCount() {

        $query = $this->db->select("distinct year(date) as year")->get("staff_attendance");

        return $query->result_array();
    }

    public function searchStaffattendance($date, $staff_id, $active_staff = true) {

        $sql = "select staff_attendance.staff_attendance_type_id,staff_attendance_type.type as `att_type`,staff_attendance_type.key_value as `key`,staff_attendance.remark,staff.name,staff.surname,staff.contact_no,staff.email,roles.name as user_type,IFNULL(staff_attendance.date, 'xxx') as date, IFNULL(staff_attendance.id, 0) as attendence_id, staff.id as id from staff left join staff_attendance on (staff.id = staff_attendance.staff_id) and staff_attendance.date = " . $this->db->escape($date) . " left join staff_roles on staff_roles.staff_id = staff.id left join roles on staff_roles.role_id = roles.id left join staff_attendance_type on staff_attendance_type.id = staff_attendance.staff_attendance_type_id where staff.id = " . $this->db->escape($staff_id);
        if ($active_staff || !isset($active_staff)) {
            $sql .= " and staff.is_active = 1";
        }
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function getAbsentStats($staff_id, $month, $year) {
        $this->db->select('COUNT(*) as absent_count');
        $this->db->from('staff_attendance');
        $this->db->join('staff_attendance_type', 'staff_attendance_type.id = staff_attendance.staff_attendance_type_id');
        $this->db->where('staff_attendance.staff_id', $staff_id);
        $this->db->where('MONTH(staff_attendance.date)', $month);
        $this->db->where('YEAR(staff_attendance.date)', $year);
        $this->db->where('staff_attendance_type.key_value', 'A'); // 'A' pour Absent

        $query = $this->db->get();
        return $query->row_array();
    }

    public function getAbsentStaffByDate($date) {
        $this->db->select('staff.id, staff.name, staff.surname, staff.employee_id, staff_attendance.remark');
        $this->db->from('staff_attendance');
        $this->db->join('staff', 'staff.id = staff_attendance.staff_id');
        $this->db->join('staff_attendance_type', 'staff_attendance_type.id = staff_attendance.staff_attendance_type_id');
        $this->db->where('staff_attendance.date', $date);
        $this->db->where('staff_attendance_type.key_value', 'A');
        $this->db->where('staff.is_active', 1);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getAbsencesByFilters($employee_id = 'all', $date_from = null, $date_to = null) {
        $this->db->select('staff_attendance.date, staff.name as employee_name, staff.surname as employee_surname, staff.employee_id, staff_attendance.remark');
        $this->db->from('staff_attendance');
        $this->db->join('staff', 'staff.id = staff_attendance.staff_id');
        $this->db->where('staff_attendance.staff_attendance_type_id', 4); // 4 = Absent

        if ($employee_id != 'all') {
            $this->db->where('staff_attendance.staff_id', $employee_id);
        }

        if ($date_from) {
            $this->db->where('staff_attendance.date >=', $date_from);
        }

        if ($date_to) {
            $this->db->where('staff_attendance.date <=', $date_to);
        }

        $this->db->order_by('staff_attendance.date', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getGlobalStatistics($year, $month = 'all') {
        $result = array();

        // Total des absences
        $this->db->select('COUNT(*) as total');
        $this->db->from('staff_attendance');
        $this->db->where('staff_attendance_type_id', 4); // 4 = Absent
        $this->db->where('YEAR(date)', $year);

        if ($month != 'all') {
            $this->db->where('MONTH(date)', $month);
        }

        $query = $this->db->get();
        $result['summary']['total_absences'] = (int)$query->row()->total;

        // Détail par employé
        $this->db->reset_query();
        $this->db->select('staff.id, staff.name, staff.surname, staff.employee_id');
        $this->db->select('COUNT(staff_attendance.id) as total_absences');
        $this->db->from('staff');
        $this->db->join('staff_attendance', 'staff.id = staff_attendance.staff_id AND staff_attendance.staff_attendance_type_id = 4', 'left');
        $this->db->where('YEAR(staff_attendance.date)', $year);

        if ($month != 'all') {
            $this->db->where('MONTH(staff_attendance.date)', $month);
        }

        $this->db->group_by('staff.id');
        $this->db->order_by('total_absences', 'DESC');
        $query = $this->db->get();
        $result['employees'] = $query->result_array();

        return $result;
    }

    public function getEmployeeStatistics($employee_id, $year, $month = 'all') {
        $result = array();

        // Total des absences pour la période
        $this->db->select('COUNT(*) as total');
        $this->db->from('staff_attendance');
        $this->db->where('staff_id', $employee_id);
        $this->db->where('staff_attendance_type_id', 4); // 4 = Absent
        $this->db->where('YEAR(date)', $year);

        if ($month != 'all') {
            $this->db->where('MONTH(date)', $month);
        }

        $query = $this->db->get();
        $result['total_absences'] = (int)$query->row()->total;

        // Total annuel
        $this->db->reset_query();
        $this->db->select('COUNT(*) as total');
        $this->db->from('staff_attendance');
        $this->db->where('staff_id', $employee_id);
        $this->db->where('staff_attendance_type_id', 4); // 4 = Absent
        $this->db->where('YEAR(date)', $year);
        $query = $this->db->get();
        $annual_total = (int)$query->row()->total;

        $result['annual_total'] = $annual_total;
        $result['monthly_avg'] = round($annual_total / 12, 1);

        // Liste détaillée des absences
        $this->db->reset_query();
        $this->db->select('date, remark');
        $this->db->from('staff_attendance');
        $this->db->where('staff_id', $employee_id);
        $this->db->where('staff_attendance_type_id', 4); // 4 = Absent
        $this->db->where('YEAR(date)', $year);

        if ($month != 'all') {
            $this->db->where('MONTH(date)', $month);
        }

        $this->db->order_by('date', 'DESC');
        $query = $this->db->get();
        $result['absences'] = $query->result_array();

        return $result;
    }
}
