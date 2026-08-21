<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reports_api extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        // Charger les modèles existants
        $this->load->model('staff_model');
        $this->load->model('Staffattendancemodel');
        $this->load->model('Leaverequest_model');
        $this->load->model('Payroll_model');

        // Autoriser les requêtes locales (JSON)
        $this->output->set_content_type('application/json');
    }

    // GET /admin/Reports_api/employees
    public function employees() {
        // Retourne la liste complète des employés (toutes entreprises)
        $list = $this->staff_model->getAll_16();
        $this->output->set_output(json_encode(['status' => 'ok', 'count' => count($list), 'data' => $list]));
    }

    // GET /admin/Reports_api/employee/{id}
    public function employee($id = null) {
        if (empty($id)) {
            $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'employee id required']));
            return;
        }
        $row = $this->staff_model->get($id);
        if (empty($row)) {
            $this->output->set_status_header(404)->set_output(json_encode(['status' => 'error', 'message' => 'employee not found']));
            return;
        }
        $this->output->set_output(json_encode(['status' => 'ok', 'data' => $row]));
    }

    // GET /admin/Reports_api/attendance?employee_id=&start_date=&end_date=
    public function attendance() {
        $employee_id = $this->input->get('employee_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (empty($start_date) || empty($end_date)) {
            $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'start_date and end_date are required (YYYY-MM-DD)']));
            return;
        }

        $this->db->select('sa.* , sat.type as attendance_type');
        $this->db->from('staff_attendance sa');
        $this->db->join('staff_attendance_type sat', 'sat.id = sa.staff_attendance_type_id', 'left');
        $this->db->where("sa.date >=", $start_date);
        $this->db->where("sa.date <=", $end_date);
        if (!empty($employee_id)) {
            $this->db->where('sa.staff_id', (int)$employee_id);
        }
        $this->db->order_by('sa.date', 'ASC');
        $q = $this->db->get()->result_array();

        $this->output->set_output(json_encode(['status' => 'ok', 'count' => count($q), 'data' => $q]));
    }

    // GET /admin/Reports_api/attendance_summary?start_date=&end_date=
    public function attendance_summary() {
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        if (empty($start_date) || empty($end_date)) {
            $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'start_date and end_date are required (YYYY-MM-DD)']));
            return;
        }

        $sql = "SELECT sa.staff_id,
                       COUNT(*) AS total_records,
                       SUM(CASE WHEN LOWER(sat.key_value) = 'present' THEN 1 ELSE 0 END) AS present_days,
                       SUM(CASE WHEN LOWER(sat.key_value) = 'absent' THEN 1 ELSE 0 END) AS absent_days,
                       SUM(CASE WHEN LOWER(sat.key_value) = 'late' THEN 1 ELSE 0 END) AS late_days
                FROM staff_attendance sa
                LEFT JOIN staff_attendance_type sat ON sat.id = sa.staff_attendance_type_id
                WHERE sa.date BETWEEN ? AND ?
                GROUP BY sa.staff_id
                ORDER BY present_days DESC";

        $q = $this->db->query($sql, [$start_date, $end_date])->result_array();
        $this->output->set_output(json_encode(['status' => 'ok', 'count' => count($q), 'data' => $q]));
    }

    // GET /admin/Reports_api/leaves?start_date=&end_date=&staff_id=
    public function leaves() {
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $staff_id = $this->input->get('staff_id');

        // If no dates provided, return recent 90 days
        if (empty($start_date) || empty($end_date)) {
            $end_date = date('Y-m-d');
            $start_date = date('Y-m-d', strtotime('-90 days'));
        }

        $events = $this->Leaverequest_model->get_calendar_events($staff_id ?: null, null, null, $start_date, $end_date);
        $this->output->set_output(json_encode(['status' => 'ok', 'count' => count($events), 'data' => $events]));
    }

    // GET /admin/Reports_api/payroll?month=&year=
    public function payroll() {
        $month = $this->input->get('month');
        $year = $this->input->get('year');

        if (empty($month) || empty($year)) {
            $this->output->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => 'month and year are required']));
            return;
        }

        // Requête simple sur staff_payslip
        $this->db->select('sp.*, s.name, s.surname');
        $this->db->from('staff_payslip sp');
        $this->db->join('staff s', 's.id = sp.staff_id', 'left');
        $this->db->where('sp.month', $month);
        $this->db->where('sp.year', $year);
        $this->db->order_by('sp.staff_id');
        $rows = $this->db->get()->result_array();

        $this->output->set_output(json_encode(['status' => 'ok', 'count' => count($rows), 'data' => $rows]));
    }

}

?>