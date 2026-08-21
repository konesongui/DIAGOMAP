<?php

class Staff_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function getCurrentEntrepriseId()
    {
        $entreprise_id = 0;
        $admin_session = $this->session->userdata('admin');

        if (is_array($admin_session) && isset($admin_session['entreprise_id'])) {
            $entreprise_id = (int) $admin_session['entreprise_id'];
        }

        if ($entreprise_id <= 0) {
            $entreprise_id = (int) ($this->session->userdata('entreprise_id') ?? 0);
        }

        return $entreprise_id;
    }

    private function applyStaffEntrepriseScope($tableAlias = 'staff')
    {
        $entreprise_id = $this->getCurrentEntrepriseId();
        if ($entreprise_id > 0) {
            $this->db->where($tableAlias . '.entreprise_id', $entreprise_id);
        }
    }

    public function get($id = null)
    {

        $this->db->select('staff.*,languages.language,roles.name as user_type,roles.id as role_id')->from('staff')->join("staff_roles", "staff_roles.staff_id = staff.id", "left")->join("roles", "staff_roles.role_id = roles.id", "left")->join("languages", "languages.id = staff.lang_id", "left");

        if ($id != null) {
            $this->db->where('staff.id', $id);
        } else {
            $this->db->where('staff.is_active', 1);
            $this->db->order_by('staff.id');
        }
        $this->applyStaffEntrepriseScope('staff');
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getrat()
    {

        $this->db->select('staff.id,staff.employee_id,CONCAT_WS(" ",staff.name,staff.surname,"(",staff.employee_id,")") as name,roles.name as user_type,roles.id as role_id,staff_rating.rate,staff_rating.status,staff_rating.comment,staff_rating.id as rate_id,CONCAT_WS(" ",students.firstname,students.middlename,students.lastname,"(",students.admission_no,")") as student_name')->from('staff')->join("staff_roles", "staff_roles.staff_id = staff.id", "left")->join("roles", "staff_roles.role_id = roles.id", "left")->join("staff_rating", "staff_rating.staff_id = staff.id", "inner")->join("users", "users.id=staff_rating.user_id", "left")->join("students", "students.id=users.user_id", "left");
        $this->db->where('staff.is_active', 1);
        $this->db->where_not_in('roles.id', 7);

        $this->db->order_by('staff.id');
        $query = $this->db->get();
        return $query->result_array();

    }

    public function getTodayDayAttendance()
    {

        $date = date('Y-m-d');

        $this->db->select('staff_id');
        $this->db->from("staff_attendance");
        $this->db->where('date = ', $date);
        $this->db->where("(staff_attendance_type_id='1' OR staff_attendance_type_id='2' OR staff_attendance_type_id='4')");
        $this->db->group_by('staff_attendance.staff_id');
        $query = $this->db->get();
        $q     = $query->result_array();
        return count($q);
    }

    public function getTotalStaff()
    {

        $this->db->select('staff.id');
        $this->db->from("staff");

        $this->db->where("staff.is_active", 1);

        $query = $this->db->get();

        $q = $query->result_array();
        return count($q);
    }

    public function user_reviewlist($id)
    {
        $this->db->select('staff_rating.rate,staff_rating.comment,staff_rating.role,students.firstname as firstname,students.middlename, students.lastname as lastname,students.guardian_name')->from('staff_rating')->join("users", "users.id = staff_rating.user_id", "inner")->join("staff", "staff_rating.staff_id = staff.id", "inner")->join("students", "students.id = staff_rating.user_id", "left");
        $this->db->where('staff.is_active', 1);
        $this->db->where('staff_rating.staff_id', $id);
        $this->db->where('staff_rating.status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getAll_16($id = null, $is_active = null)
    {

        $this->db->select("staff.*,staff_designation.designation,department.department_name as department, roles.id as role_id, roles.name as role");
        $this->db->from('staff');
        $this->db->join('staff_designation', "staff_designation.id = staff.designation", "left");
        $this->db->join('staff_roles', "staff_roles.staff_id = staff.id", "left");
        $this->db->join('roles', "roles.id = staff_roles.role_id", "left");
        $this->db->join('department', "department.id = staff.department", "left");

        if ($id != null) {
            $this->db->where('staff.id', $id);
        } else {
            if ($is_active != null) {

                $this->db->where('staff.is_active', $is_active);
            }
            $this->db->order_by('staff.id');
        }
        $this->applyStaffEntrepriseScope('staff');
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getAll_users($id = null, $is_active = null)
    {

        $this->db->select("staff.*,staff_designation.designation,department.department_name as department, roles.id as role_id, roles.name as role");
        $this->db->from('staff');
        $this->db->join('staff_designation', "staff_designation.id = staff.designation", "left");
        $this->db->join('staff_roles', "staff_roles.staff_id = staff.id", "left");
        $this->db->join('roles', "roles.id = staff_roles.role_id", "left");
        $this->db->join('department', "department.id = staff.department", "left");

        if ($id != null) {
            $this->db->where('staff.id', $id);
        } else {
            if ($is_active != null) {

                $this->db->where('staff.is_active', $is_active);
            }
            $this->db->where('roles.id!=', 7);
            $this->db->order_by('staff.id');
        }
        $this->applyStaffEntrepriseScope('staff');
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getBirthDayStaff($dob, $is_active = 1, $email = false, $contact_no = false)
    {

        $this->db->select("staff.*,staff_designation.designation,department.department_name as department, roles.id as role_id, roles.name as role");
        $this->db->from('staff');
        $this->db->join('staff_designation', "staff_designation.id = staff.designation", "left");
        $this->db->join('staff_roles', "staff_roles.staff_id = staff.id", "left");
        $this->db->join('roles', "roles.id = staff_roles.role_id", "left");
        $this->db->join('department', "department.id = staff.department", "left");

        $this->db->where('staff.is_active', $is_active);
        $this->db->where("DATE_FORMAT(staff.dob,'%m-%d') = DATE_FORMAT('" . $dob . "','%m-%d')");
        if ($email) {
            $this->db->where('staff.email !=', "");
        }
        if ($contact_no) {
            $this->db->where('staff.contact_no !=', "");
        }
        $this->db->order_by('staff.id');

        $query = $this->db->get();

        return $query->result_array();
    }

    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('staff', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On staff id " . $data['id'];
            $action    = "Update";
            $record_id = $id = $data['id'];
            $this->log($message, $record_id, $action);

        } else {
            $this->db->insert('staff', $data);
            $id        = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On staff id " . $id;
            $action    = "Insert";
            $record_id = $id;
            $this->log($message, $record_id, $action);

        }
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /*Optional*/

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;

        } else {
            return $id;
        }
    }

    // Ajoutez cette méthode à votre Staff_model.php
    public function save_staff_documents($documents)
    {
        if (!empty($documents)) {
            return $this->db->insert_batch('staff_documents', $documents);
        }
        return false;
    }

// Méthode pour récupérer les documents d'un staff
    public function get_staff_documents($staff_id)
    {
        return $this->db->where('staff_id', $staff_id)
            ->get('staff_documents')
            ->result();
    }

    public function update($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $data['id']);
        $query     = $this->db->update('staff', $data);
        $message   = UPDATE_RECORD_CONSTANT . " On staff id " . $data['id'];
        $action    = "Update";
        $record_id = $data['id'];
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /*Optional*/

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;

        } else {
            //return $return_value;
        }
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function getByVerificationCode($ver_code)
    {
        $condition = "verification_code =" . "'" . $ver_code . "'";
        $this->db->select('*');
        $this->db->from('staff');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function batchInsert($data, $roles = array(), $leave_array = array(), $data_setting = array())
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Insertion de l'employé
        $this->db->insert('staff', $data);
        $staff_id          = $this->db->insert_id();

        // Insertion du rôle
        $roles['staff_id'] = $staff_id;
        $this->db->insert_batch('staff_roles', array($roles));

        // Mise à jour des paramètres automatiques
        if (!empty($data_setting)) {
            if ($data_setting['staffid_auto_insert']) {
                if ($data_setting['staffid_update_status'] == 0) {
                    $data_setting['staffid_update_status'] = 1;
                    $this->setting_model->add($data_setting);
                }
            }
        }

        // Si $leave_array est vide, on initialise automatiquement avec la durée définie dans 'leaves'
        if (empty($leave_array)) {
            $this->db->select('id, ndays'); // colonne "durée" bien présente ?
            $query = $this->db->get('leave_types');
            $leaves = $query->result_array();

            foreach ($leaves as $leave) {
                $leave_array[] = array(
                    'staff_id'      => $staff_id,
                    'leave_type_id' => $leave['id'],
                    'alloted_leave' => $leave['ndays'],
                );
            }
        } else {
            // Si déjà fourni manuellement, on associe simplement le staff_id
            foreach ($leave_array as $key => $value) {
                $leave_array[$key]['staff_id'] = $staff_id;
            }
        }

        // Insertion des types de congés alloués
        if (!empty($leave_array)) {
            $this->db->insert_batch('staff_leave_details', $leave_array);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();

      return $staff_id;
        }
    }

    public function batchInsert_old($data, $roles = array(), $leave_array = array(), $data_setting = array())
    {

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->insert('staff', $data);
        $staff_id          = $this->db->insert_id();
        $roles['staff_id'] = $staff_id;
        $this->db->insert_batch('staff_roles', array($roles));
        if (!empty($data_setting)) {
            if ($data_setting['staffid_auto_insert']) {
                if ($data_setting['staffid_update_status'] == 0) {
                    $data_setting['staffid_update_status'] = 1;
                    $this->setting_model->add($data_setting);
                }
            }
        }

        if (!empty($leave_array)) {
            foreach ($leave_array as $key => $value) {
                $leave_array[$key]['staff_id'] = $staff_id;
            }

            $this->db->insert_batch('staff_leave_details', $leave_array);
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {

            $this->db->trans_rollback();
            return false;
        } else {

            $this->db->trans_commit();
            return $staff_id;
        }
    }

    public function adddoc($data)
    {

        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('staff_documents', $data);
        } else {
            $this->db->insert('staff_documents', $data);
            return $this->db->insert_id();
        }
    }

    public function remove($id)
    {

        $this->db->trans_start();
        $this->db->trans_strict(false);
        $sql   = "DELETE FROM custom_field_values WHERE id IN (select * from (SELECT t2.id as `id` FROM `custom_fields` INNER JOIN custom_field_values as t2 on t2.custom_field_id=custom_fields.id WHERE custom_fields.belong_to='staff' and t2.belong_table_id IN (" . $id . ")) as m2)";
        $query = $this->db->query($sql);

        $this->db->where('id', $id);
        $this->db->delete('staff');

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {

            $this->db->trans_rollback();
            return false;
        } else {

            $this->db->trans_commit();
            return $staff_id;
        }

    }

    public function add_staff_leave_details($data2)
    {

        if (isset($data2['id'])) {
            $this->db->where('id', $data2['id']);
            $this->db->update('staff_leave_details', $data2);
        } else {
            $this->db->insert('staff_leave_details', $data2);
            return $this->db->insert_id();
        }
    }

    public function getPayroll($id = null)
    {

        $this->db->select()->from('staff_payroll');
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

    public function getLeaveType($id = null)
    {

        $this->db->select()->from('leave_types');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('is_active', 'yes');
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function valid_employee_id($str)
    {
        $name     = $this->input->post('name');
        $id       = $this->input->post('employee_id');
        $staff_id = $this->input->post('editid');

        if ((!isset($id))) {
            $id = 0;
        }
        if (!isset($staff_id)) {
            $staff_id = 0;
        }

        if ($this->check_data_exists($name, $id, $staff_id)) {
            $this->form_validation->set_message('username_check', 'Record already exists');
            return false;

        } else {

            return true;
        }

    }

    public function check_data_exists($name, $id, $staff_id)
    {

        if ($staff_id != 0) {
            $data  = array('id != ' => $staff_id, 'employee_id' => $id);
            $query = $this->db->where($data)->get('staff');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {

            $this->db->where('employee_id', $id);
            $query = $this->db->get('staff');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function import_check_data_exists($name, $id)
    {
        $this->db->where('employee_id', $id);
        $query = $this->db->get('staff');
        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function import_check_email_exists($name, $id)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('staff');
        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function valid_email_id($str)
    {
        $email    = $this->input->post('email');
        $id       = $this->input->post('employee_id');
        $staff_id = $this->input->post('editid');

        if (!isset($id)) {
            $id = 0;
        }
        if (!isset($staff_id)) {
            $staff_id = 0;
        }

        if ($this->check_email_exists($email, $id, $staff_id)) {
            $this->form_validation->set_message('check_exists', 'Email already exists');
            return false;
        } else {
            return true;
        }
    }

    public function check_email_exists($email, $id, $staff_id)
    {

        if ($staff_id != 0) {
            $data  = array('id != ' => $staff_id, 'email' => $email);
            $query = $this->db->where($data)->get('staff');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {

            $this->db->where('email', $email);
            $query = $this->db->get('staff');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getStaffRole($id = null)
    {

        $userdata = $this->customlib->getUserData();
        if ($userdata["role_id"] != 7) {
            $this->db->where("id !=", 7);
        }

        $this->db->select('roles.id,roles.name as type')->from('roles');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
        }
        $this->db->where("is_active", "yes");
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function count_leave($month, $year, $staff_id)
    {

        $query1 = $this->db->select('sum(leave_days) as tl')->where(array('month(date)' => $month, 'year(date)' => $year, 'staff_id' => $staff_id, 'status' => 'approve'))->get("staff_leave_request");
        return $query1->row_array();
    }

    public function alloted_leave($staff_id)
    {

        $query2 = $this->db->select('sum(alloted_leave) as alloted_leave')->where(array('staff_id' => $staff_id))->get("staff_leave_details");

        return $query2->result_array();
    }

    public function allotedLeaveType($id)
    {

        $query = $this->db->select('staff_leave_details.*,leave_types.type')->where(array('staff_id' => $id))->join("leave_types", "staff_leave_details.leave_type_id = leave_types.id")->get("staff_leave_details");

        return $query->result_array();
    }

    public function getAllotedLeave($staff_id)
    {

        $query = $this->db->select('*')->join("leave_types", "staff_leave_details.leave_type_id = leave_types.id")->where("staff_id", $staff_id)->get("staff_leave_details");

        return $query->result_array();
    }

    public function getEmployee($role, $active = 1, $class_id = null)
    {
        $i             = 1;
        $custom_fields = $this->customfield_model->get_custom_fields('staff', 1);

        $field_k_array = array();
        $join_array    = "";
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_k_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $this->db->join('custom_field_values as ' . $tb_counter, 'staff.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');

                $i++;
            }
        }


        $field_var = count($field_k_array) > 0 ? "," . implode(',', $field_k_array) : "";

        $this->db->select("staff.*,staff_designation.designation,department.department_name as department,roles.name as user_type" . $field_var)->from('staff');
        $this->db->join('staff_designation', "staff_designation.id = staff.designation", "left");
        $this->db->join('staff_roles', "staff_roles.staff_id = staff.id", "left");
        $this->db->join('roles', "roles.id = staff_roles.role_id", "left");
        $this->db->join('department', "department.id = staff.department", "left");

        if ($class_id != "") {
            $this->db->join('class_teacher', 'staff.id=class_teacher.staff_id', 'left');
            $this->db->or_where('class_teacher.class_id', $student_current_class->class_id);
        }
        $this->db->where("staff.is_active", $active);
                $this->applyStaffEntrepriseScope('staff');
        if($role != ""){
        $this->db->where("roles.id", $role);
          }
        $query = $this->db->get();

        return $query->result_array();
    }

    public function getEmployeeByRoleID($role, $active = 1)
    {

        $this->db->select("staff.*,staff_designation.designation,department.department_name as department, roles.id as role_id, roles.name as role")->join('staff_designation', "staff_designation.id = staff.designation", "left")->join('staff_roles', "staff_roles.staff_id = staff.id", "left")->join('roles', "roles.id = staff_roles.role_id", "left")->join('department', "department.id = staff.department", "left")->where("staff.is_active", $active)->where("roles.id", $role);
        $this->applyStaffEntrepriseScope('staff');
        $query = $this->db->get("staff");

        return $query->result_array();
    }

    public function getStaffDesignation()
    {

        $query = $this->db->select('*')->where("is_active", "yes")->get("staff_designation");
        return $query->result_array();
    }

    public function getDepartment()
    {
        $query = $this->db->select('*')->where("is_active", "yes")->get('department');
        return $query->result_array();
    }

    public function getCategorie()
    {
        $query = $this->db->select('*')->where("is_active", "yes")->get('categorie_salaire');
        return $query->result_array();
    }

    public function getLeaveRecord($id) {
        $this->db->select('staff_leave_request.*, staff.name, staff.surname, staff.employee_id, leave_types.type')
            ->from('staff_leave_request')
            ->join('staff', 'staff.id = staff_leave_request.staff_id')
            ->join('leave_types', 'leave_types.id = staff_leave_request.leave_type_id')
            ->where('staff_leave_request.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function getLeaveTypeById($id) {
        return $this->db->get_where('leave_types', ['id' => $id])->row();
    }


    public function getLeaveRecord150626($id)
    {

        $query = $this->db->select('leave_types.type,leave_types.id as lid,roles.id as staff_role,staff.name,staff.surname,staff.id as staff_id,roles.name as user_type,staff.employee_id,staff_leave_request.*')->join("leave_types", "leave_types.id = staff_leave_request.leave_type_id")->join("staff", "staff.id = staff_leave_request.staff_id")->join("staff_roles", "staff.id = staff_roles.staff_id")->join("roles", "staff_roles.role_id = roles.id")->where("staff_leave_request.id", $id)->get("staff_leave_request");

        return $query->row();
    }

    public function getStaffId($empid)
    {

        $data  = array('employee_id' => $empid);
        $query = $this->db->select('id')->where($data)->get("staff");
        return $query->row_array();
    }

    public function getProfile($id)
    {

        $this->db->select('staff.*,staff_designation.designation as designation,staff_roles.role_id, department.department_name as department,roles.name as user_type');
        $this->db->join("staff_designation", "staff_designation.id = staff.designation", "left");
        $this->db->join("department", "department.id = staff.department", "left");
        $this->db->join("staff_roles", "staff_roles.staff_id = staff.id", "left");
        $this->db->join("roles", "staff_roles.role_id = roles.id", "left");
        $this->db->where("staff.id", $id);
        $this->applyStaffEntrepriseScope('staff');
        $this->db->from('staff');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_stafflang($id)
    {

    }

    public function searchFullText($searchterm, $active)
    {
        if (trim((string) $searchterm) === '') {
            return array();
        }

        $i             = 1;
        $custom_fields = $this->customfield_model->get_custom_fields('staff', 1);

        $field_k_array = array();
        $join_array    = "";
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_k_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $join_array .= " LEFT JOIN `custom_field_values` as `" . $tb_counter . "` ON `staff`.`id` = `" . $tb_counter . "`.`belong_table_id` AND `" . $tb_counter . "`.`custom_field_id` = " . $custom_fields_value->id;

                $i++;
            }
        }

        $field_var = count($field_k_array) > 0 ? "," . implode(',', $field_k_array) : "";

        $query = "SELECT `staff`.*, `staff_designation`.`designation` as `designation`, `department`.`department_name` as `department`,`roles`.`name` as user_type " . $field_var . "  FROM `staff` " . $join_array . " LEFT JOIN `staff_designation` ON `staff_designation`.`id` = `staff`.`designation` LEFT JOIN `staff_roles` ON `staff_roles`.`staff_id` = `staff`.`id` LEFT JOIN `roles` ON `staff_roles`.`role_id` = `roles`.`id` LEFT JOIN `department` ON `department`.`id` = `staff`.`department` WHERE 1=1";

        if ($active !== null && $active !== '' && strtolower((string) $active) !== 'null') {
            $query .= " AND `staff`.`is_active` = " . $this->db->escape($active);
        }

        $entreprise_id = $this->getCurrentEntrepriseId();
        if ($entreprise_id > 0) {
            $query .= " AND `staff`.`entreprise_id` = " . (int) $entreprise_id;
        }

        $query .= " and (CONCAT(`staff`.`name`,' ',`staff`.`surname`) LIKE '%" . $this->db->escape_like_str($searchterm) . "%' ESCAPE '!' OR `staff`.`surname` LIKE '%" . $this->db->escape_like_str($searchterm) . "%' ESCAPE '!' OR `staff`.`employee_id` LIKE '%" . $this->db->escape_like_str($searchterm) . "%' ESCAPE '!' OR `staff`.`local_address` LIKE '%" . $this->db->escape_like_str($searchterm) . "%' ESCAPE '!'  OR `staff`.`contact_no` LIKE '%" . $this->db->escape_like_str($searchterm) . "%' ESCAPE '!' OR `staff`.`email` LIKE '%" . $this->db->escape_like_str($searchterm) . "%' ESCAPE '!' OR `roles`.`name` LIKE '%" . $this->db->escape_like_str($searchterm) . "%' ESCAPE '!')";

        $query = $this->db->query($query);
        return $query->result_array();
    }

    public function searchByEmployeeId($employee_id)
    {

        $this->db->select('*');
        $this->db->from('staff');
        $this->db->like('staff.employee_id', $employee_id);
        $this->db->like('staff.is_active', 1);
        $this->applyStaffEntrepriseScope('staff');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getStaffDoc_($id)
    {

        $this->db->select('*');
        $this->db->from('staff_documents');
        $this->db->where('staff_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function count_attendance($year, $staff_id, $att_type)
    {
        $query = $this->db->select('count(*) as attendence')->where(array('staff_id' => $staff_id, 'year(date)' => $year, 'staff_attendance_type_id' => $att_type))->get("staff_attendance");

        return $query->row()->attendence;
    }

    public function getStaffPayroll($id)
    {
        $this->db->select('*');
        $this->db->from('staff_payslip');
        $this->db->where('staff_id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function doc_delete($id, $doc, $file)
    {

        if ($doc == 1) {

            $data = array('resume' => '');
        } else
        if ($doc == 2) {

            $data = array('joining_letter' => '');
        } else
        if ($doc == 3) {

            $data = array('resignation_letter' => '');
        } else
        if ($doc == 4) {

            $data = array('other_document_name' => '', 'other_document_file' => '');
        }
        unlink(BASEPATH . "uploads/staff_documents/" . $file);
        $this->db->where('id', $id)->update("staff", $data);
    }

    public function getLeaveDetails($id)
    {

        $query = $this->db->select('staff_leave_details.alloted_leave,staff_leave_details.id as altid,leave_types.type,leave_types.id')->join("leave_types", "staff_leave_details.leave_type_id = leave_types.id", "inner")->where("staff_leave_details.staff_id", $id)->get("staff_leave_details");

        return $query->result_array();
    }

    public function disablestaff($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        $query = $this->db->where("id", $data['id'])->update("staff", $data);
        $message   = UPDATE_RECORD_CONSTANT . " On  disable Staff id " . $data['id'];
        $action    = "Update";
        $record_id = $insert_id = $data['id'];
        $this->log($message, $record_id, $action);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

    }

    public function enablestaff($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        $data = array('is_active' => 1, 'disable_at' => '');
        $query = $this->db->where("id", $id)->update("staff", $data);
        $message   = UPDATE_RECORD_CONSTANT . " On  Enable Staff id " . $id;
        $action    = "Update";
        $record_id = $insert_id = $id;
        $this->log($message, $record_id, $action);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    public function getByEmail($email)
    {
        $this->db->select('staff.*,languages.language,languages.id as language_id');
        $this->db->from('staff')->join('languages', 'languages.id=staff.lang_id', 'left');
        $this->db->where('email', $email);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function checkLogin($data)
    {
        $record = $this->getByEmail($data['email']);

        if ($record) {
            $pass_verify = $this->enc_lib->passHashDyc($data['password'], $record->password);

            if ($pass_verify) {
                // Vérification du staff
                if ($record->is_active != 1) {
                    return (object) array(
                        'status' => 'error',
                        'message' => 'Votre compte est désactivé.'
                    );
                }

                // Vérification de l'entreprise avec une requête SQL directe
                if (!empty($record->entreprise_id) && $record->entreprise_id > 0) {
                    // Requête directe pour éviter les problèmes d'encodage
                    $companySelect = 'id, statut, nom, type_structure, parent_entreprise_id, code_succursale';
                    if ($this->db->field_exists('can_manage_succursales', 'compte_entreprise')) {
                        $companySelect .= ', can_manage_succursales';
                    }
                    $sql = "SELECT " . $companySelect . " FROM compte_entreprise WHERE id = ?";
                    $query = $this->db->query($sql, array($record->entreprise_id));

                    if ($query->num_rows() > 0) {
                        $entreprise = $query->row();
                        $statut = strtolower(trim($entreprise->statut));

                        // Vérification stricte
                        if ($statut == 'suspendu') {
                            return (object) array(
                                'status' => 'error',
                                'message' => 'Votre entreprise "' . $entreprise->nom . '" est suspendue.'
                            );
                        }

                        if ($statut == 'expiré') {
                            return (object) array(
                                'status' => 'error',
                                'message' => 'Votre abonnement pour "' . $entreprise->nom . '" a expiré.'
                            );
                        }

                        if (!empty($entreprise->type_structure) && $entreprise->type_structure === 'succursale' && !empty($entreprise->parent_entreprise_id)) {
                            $parent = $this->db->select('statut, nom')
                                ->from('compte_entreprise')
                                ->where('id', (int) $entreprise->parent_entreprise_id)
                                ->limit(1)
                                ->get()
                                ->row();

                            if ($parent) {
                                $parent_statut = strtolower(trim($parent->statut));
                                if ($parent_statut == 'suspendu') {
                                    return (object) array(
                                        'status' => 'error',
                                        'message' => 'Le siège "' . $parent->nom . '" de votre succursale est suspendu.'
                                    );
                                }

                                if ($parent_statut == 'expiré') {
                                    return (object) array(
                                        'status' => 'error',
                                        'message' => 'Le siège "' . $parent->nom . '" de votre succursale a expiré.'
                                    );
                                }
                            }
                        }

                        $record->entreprise_nom = $entreprise->nom;
                        $record->type_structure = isset($entreprise->type_structure) ? $entreprise->type_structure : 'siege';
                        $record->parent_entreprise_id = isset($entreprise->parent_entreprise_id) ? (int) $entreprise->parent_entreprise_id : 0;
                        $record->code_succursale = isset($entreprise->code_succursale) ? $entreprise->code_succursale : '';
                        $record->can_manage_succursales = isset($entreprise->can_manage_succursales) ? (int) $entreprise->can_manage_succursales : 0;
                    }
                }

                $roles = $this->staffroles_model->getStaffRoles($record->id);
                $record->roles = !empty($roles) ? array($roles[0]->name => $roles[0]->role_id) : array();
                return $record;
            }
        }
        return false;
    }

    public function checkLogin_12($data)
    {
        // 1. Récupérer l'utilisateur par email (méthode originale)
        $record = $this->getByEmail($data['email']);

        if ($record) {
            // 2. Vérifier le mot de passe (méthode originale)
            $pass_verify = $this->enc_lib->passHashDyc($data['password'], $record->password);

            if ($pass_verify) {
                // ===== AJOUT : VÉRIFICATION DU STAFF ACTIF =====
                if ($record->is_active != 1) {
                    return (object) array(
                        'status' => 'error',
                        'message' => 'Votre compte personnel est désactivé. Veuillez contacter l\'administrateur.'
                    );
                }

                // ===== AJOUT : VÉRIFICATION DU STATUT DE L'ENTREPRISE =====
                // Identifier le super admin (ID = 1 ou role_id = 7)
                $is_super_admin = false;

                // Vérification 1 : ID = 1
                if ($record->id == 1) {
                    $is_super_admin = true;
                }

                // Vérification 2 : Récupérer le rôle de l'utilisateur
                if (!$is_super_admin) {
                    $roles = $this->staffroles_model->getStaffRoles($record->id);
                    if (!empty($roles)) {
                        // Vérifier si l'utilisateur a le rôle Super Admin (role_id = 7)
                        foreach ($roles as $role) {
                            if ($role->role_id == 7) {
                                $is_super_admin = true;
                                break;
                            }
                        }
                    }
                }

                // Vérification du statut de l'entreprise (uniquement pour les non-super-admins)
                if (!$is_super_admin && !empty($record->entreprise_id) && $record->entreprise_id > 0) {
                    $this->db->select('statut, nom');
                    $this->db->from('compte_entreprise');
                    $this->db->where('id', $record->entreprise_id);
                    $entreprise = $this->db->get()->row();

                    if ($entreprise) {
                        if ($entreprise->statut == 'suspendu') {
                            return (object) array(
                                'status' => 'error',
                                'message' => 'Votre entreprise "' . $entreprise->nom . '" est suspendue. Veuillez contacter l\'administrateur.'
                            );
                        }

                        if ($entreprise->statut == 'expiré') {
                            return (object) array(
                                'status' => 'error',
                                'message' => 'Votre abonnement pour "' . $entreprise->nom . '" a expiré. Veuillez renouveler votre abonnement.'
                            );
                        }
                    } else {
                        // Si l'entreprise n'existe pas dans compte_entreprise
                        return (object) array(
                            'status' => 'error',
                            'message' => 'Votre entreprise n\'est pas reconnue. Veuillez contacter l\'administrateur.'
                        );
                    }
                }

                // ===== SUITE : Récupération des rôles (méthode originale) =====
                $roles = $this->staffroles_model->getStaffRoles($record->id);
                $record->roles = array($roles[0]->name => $roles[0]->role_id);
                return $record;
            }
        }
        return false;
    }

    public function checkLogin_19($data)
    {

        $record = $this->getByEmail($data['email']);
        if ($record) {
            $pass_verify = $this->enc_lib->passHashDyc($data['password'], $record->password);
            if ($pass_verify) {
                $roles = $this->staffroles_model->getStaffRoles($record->id);
                $record->roles = array($roles[0]->name => $roles[0]->role_id);
                return $record;
            }
        }
        return false;
    }

    public function getStaffbyrole($id)
    {
        $this->db->select('staff.*,staff_designation.designation as designation,staff_roles.role_id, department.department_name as department,roles.name as user_type');
        $this->db->join("staff_designation", "staff_designation.id = staff.designation", "left");
        $this->db->join("department", "department.id = staff.department", "left");
        $this->db->join("staff_roles", "staff_roles.staff_id = staff.id", "left");
        $this->db->join("roles", "staff_roles.role_id = roles.id", "left");
        $this->db->where("staff_roles.role_id", $id);
        $this->db->where("staff.is_active", "1");
        $this->db->from('staff');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function searchNameLike($searchterm)
    {
        $this->db->select('staff.*')->from('staff');
        $this->db->group_start();
        $this->db->like('staff.name', $searchterm);
        $this->db->group_end();
        $this->db->where("staff.is_active", "1");
        $this->db->order_by('staff.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function update_role($role_data)
    {
        $this->db->where("staff_id", $role_data["staff_id"])->update("staff_roles", $role_data);
    }

    public function check_staffid_exists($employee_id)
    {
        $this->db->where(array('employee_id' => $employee_id));
        $query = $this->db->get('staff');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }

    }

    public function lastRecord()
    {
        $last_row = $this->db->select('*')->order_by('id', "desc")->limit(1)->get('staff')->row();
        return $last_row;
    }

    public function ratingapr($id, $approve)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $id)->update("staff_rating", $approve);
        $message   = UPDATE_RECORD_CONSTANT . " On staff rating id " . $id;
        $action    = "Update";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /*Optional*/

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;

        } else {
            //return $return_value;
        }
    }

    public function rating_remove($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('staff_rating');
        $message   = DELETE_RECORD_CONSTANT . " On staff rating id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /*Optional*/
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }

    public function get_RatedStaffByUser($user_id)
    {
        $this->db->select('staff_rating.staff_id')->from('staff_rating');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function all_rating()
    {
        $this->db->select('sum(`rate`) as rate, count(*) as total,staff_id')->from('staff_rating');
        $this->db->where('status', '1');
        $this->db->group_by('staff_id');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_ratingbyuser($user_id, $role)
    {
        $this->db->select('*')->from('staff_rating');
        $this->db->where('user_id', $user_id);
        $this->db->where('role', $role);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function staff_ratingById($id)
    {
        $this->db->select('sum(`rate`) as rate, count(*) as total')->from('staff_rating');
        $this->db->where('staff_id', $id);
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_StaffNameById($id)
    {
        return $this->db->select("CONCAT_WS(' ',name,surname) as name,employee_id,id")->from('staff')->where('id', $id)->get()->row_array();
    }


    public function staff_report()
    {
        $i             = 1;
        $custom_fields = $this->customfield_model->get_custom_fields('staff', 1);

        $field_k_array = array();
        $join_array    = "";

        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push(
                    $field_k_array,
                    '`' . $tb_counter . '`.`field_value` as `' . $custom_fields_value->name . '`'
                );
                $join_array .= " 
                LEFT JOIN `custom_field_values` as `" . $tb_counter . "` 
                ON `staff`.`id` = `" . $tb_counter . "`.`belong_table_id` 
                AND `" . $tb_counter . "`.`custom_field_id` = " . $custom_fields_value->id;
                $i++;
            }
        }

        $field_var = count($field_k_array) > 0 ? "," . implode(',', $field_k_array) : "";

        // 🔹 Affiche tous les utilisateurs sans condition
        $query = "
        SELECT 
            `staff`.*, 
            `staff_designation`.`designation` AS `designation`, 
            `department`.`department_name` AS `department`,
            `roles`.`name` AS user_type 
            $field_var,
            GROUP_CONCAT(leave_type_id,'@',alloted_leave) AS leaves  
        FROM `staff`
        $join_array
        LEFT JOIN `staff_designation` ON `staff_designation`.`id` = `staff`.`designation`
        LEFT JOIN `staff_roles` ON `staff_roles`.`staff_id` = `staff`.`id`
        LEFT JOIN `roles` ON `staff_roles`.`role_id` = `roles`.`id`
        LEFT JOIN `department` ON `department`.`id` = `staff`.`department`
        LEFT JOIN `staff_leave_details` ON `staff_leave_details`.`staff_id` = `staff`.`id`
        GROUP BY `staff`.`id`
    ";

        $query = $this->db->query($query);
        return $query->result_array();
    }

    public function staff_report_221025($condition)
    {
        $i             = 1;
        $custom_fields = $this->customfield_model->get_custom_fields('staff', 1);

        $field_k_array = array();
        $join_array    = "";
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_k_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $join_array .= " LEFT JOIN `custom_field_values` as `" . $tb_counter . "` ON `staff`.`id` = `" . $tb_counter . "`.`belong_table_id` AND `" . $tb_counter . "`.`custom_field_id` = " . $custom_fields_value->id;

                $i++;
            }
        }

        $field_var = count($field_k_array) > 0 ? "," . implode(',', $field_k_array) : "";

        $query = "SELECT `staff`.*, `staff_designation`.`designation` as `designation`, `department`.`department_name` as `department`,`roles`.`name` as user_type " . $field_var . ",GROUP_CONCAT(leave_type_id,'@',alloted_leave) as leaves  FROM `staff` " . $join_array . " LEFT JOIN `staff_designation` ON `staff_designation`.`id` = `staff`.`designation` LEFT JOIN `staff_roles` ON `staff_roles`.`staff_id` = `staff`.`id` LEFT JOIN `roles` ON `staff_roles`.`role_id` = `roles`.`id` LEFT JOIN `department` ON `department`.`id` = `staff`.`department` left join staff_leave_details ON staff_leave_details.staff_id=staff.id WHERE 1  " . $condition . " group by staff.id";

        $query = $this->db->query($query);

        return $query->result_array();
    }

    public function inventry_staff()
    {
        return $this->db->select("CONCAT_WS(' ',staff.name,staff.surname) as name,staff.employee_id")->from('staff')->where('staff.is_active', 1)->get()->result_array();

    }

    /**
     * Récupère les employés filtrés par statut, période et recherche
     * @param string $status Statut (1=actif, 0=inactif, null=tous)
     * @param string $date_from Date de début (YYYY-MM-DD)
     * @param string $date_to Date de fin (YYYY-MM-DD)
     * @param string $search_term Terme de recherche
     * @param string $role ID du rôle
     * @return array Liste des employés filtrés
     */
    public function getFilteredStaff($status = null, $date_from = null, $date_to = null, $search_term = null, $role = null)
    {
        $i = 1;
        $custom_fields = $this->customfield_model->get_custom_fields('staff', 1);

        $field_k_array = array();
        $join_array = "";
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_k_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $join_array .= " LEFT JOIN `custom_field_values` as `" . $tb_counter . "` ON `staff`.`id` = `" . $tb_counter . "`.`belong_table_id` AND `" . $tb_counter . "`.`custom_field_id` = " . $custom_fields_value->id;
                $i++;
            }
        }

        $field_var = count($field_k_array) > 0 ? "," . implode(',', $field_k_array) : "";

        $this->db->select("staff.*, staff_designation.designation, department.department_name as department, roles.id as role_id, roles.name as role" . $field_var)
            ->from('staff')
            ->join('staff_designation', "staff_designation.id = staff.designation", "left")
            ->join('staff_roles', "staff_roles.staff_id = staff.id", "left")
            ->join('roles', "roles.id = staff_roles.role_id", "left")
            ->join('department', "department.id = staff.department", "left");

        // Ajouter les jointures pour les champs personnalisés
        if (!empty($custom_fields)) {
            $this->db->join('custom_field_values as table_custom_1', 'staff.id = table_custom_1.belong_table_id AND table_custom_1.custom_field_id = ' . $custom_fields[0]->id, 'left');
            // Ajouter d'autres jointures si nécessaire
        }

        // Filtre par statut
        if ($status !== null && $status !== '') {
            $this->db->where('staff.is_active', $status);
        }

        $this->applyStaffEntrepriseScope('staff');

        // Filtre par rôle
        if (!empty($role)) {
            $this->db->where('roles.id', $role);
        }

        // Filtre par période (date d'enregistrement)
        if (!empty($date_from) && !empty($date_to)) {
            $date_from = date('Y-m-d', strtotime($date_from));
            $date_to = date('Y-m-d', strtotime($date_to));
            $this->db->where('DATE(staff.created_at) >=', $date_from);
            $this->db->where('DATE(staff.created_at) <=', $date_to);
        } elseif (!empty($date_from)) {
            $date_from = date('Y-m-d', strtotime($date_from));
            $this->db->where('DATE(staff.created_at) >=', $date_from);
        } elseif (!empty($date_to)) {
            $date_to = date('Y-m-d', strtotime($date_to));
            $this->db->where('DATE(staff.created_at) <=', $date_to);
        }

        // Recherche par terme
        if (!empty($search_term)) {
            $this->db->group_start();
            $this->db->like('staff.name', $search_term);
            $this->db->or_like('staff.surname', $search_term);
            $this->db->or_like('staff.employee_id', $search_term);
            $this->db->or_like('staff.email', $search_term);
            $this->db->or_like('staff.contact_no', $search_term);
            $this->db->or_like('staff_designation.designation', $search_term);
            $this->db->or_like('department.department_name', $search_term);
            $this->db->group_end();
        }

        $this->db->order_by('staff.id', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Récupère tous les employés sans filtre (pour compatibilité)
     */

    public function getStaffDetails($staff_id) {
        $this->db->select('*');
        $this->db->from('staff');
        $this->db->where('id', $staff_id);
        $this->applyStaffEntrepriseScope('staff');
        $query = $this->db->get();
        return $query->row();
    }

    public function getAll($id = null, $is_active = null)
    {
        $this->db->select("staff.*,staff_designation.designation,department.department_name as department, roles.id as role_id, roles.name as role");
        $this->db->from('staff');
        $this->db->join('staff_designation', "staff_designation.id = staff.designation", "left");
        $this->db->join('staff_roles', "staff_roles.staff_id = staff.id", "left");
        $this->db->join('roles', "roles.id = staff_roles.role_id", "left");
        $this->db->join('department', "department.id = staff.department", "left");

        if ($id != null) {
            $this->db->where('staff.id', $id);
        } else {
            // Exclure le Super Admin (rôle avec name = 'Super Admin')
            $this->db->where("roles.name != 'Super Admin'");

            if ($is_active !== null) {
                $this->db->where('staff.is_active', $is_active);
            }
            $this->db->order_by('staff.id', 'DESC');
        }
        $this->applyStaffEntrepriseScope('staff');
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }


    // models/Staff_model.php

    /**
     * Calcule le nombre de jours de congé annuel acquis à une date donnée
     * @param int $staff_id
     * @param string $date_reference (format Y-m-d) - par défaut aujourd'hui
     * @return int nombre de jours ouvrables acquis (arrondi à l'entier supérieur)
     */
    public function calculer_conge_annuel_acquis($staff_id, $date_reference = null) {
        if (!$date_reference) $date_reference = date('Y-m-d');

        $staff = $this->db->get_where('staff', ['id' => $staff_id])->row();
        if (!$staff || empty($staff->date_of_joining)) return 0;

        $date_embauche = $staff->date_of_joining;
        $diff = date_diff(date_create($date_embauche), date_create($date_reference));
        $mois_travailles = ($diff->y * 12) + $diff->m; // nombre de mois complets

        // 2,2 jours par mois (arrondi à l'entier supérieur)
        $jours_acquis = ceil($mois_travailles * 2.2); // on arrondit au supérieur pour le salarié

        // Ajout des jours supplémentaires liés à l'ancienneté (si votre config existe)
        $config = $this->config->item('anciennete_supplement');
        if ($config) {
            $annees_anciennete = $diff->y;
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
     * Récupère le nombre de jours déjà pris (approuvés) pour le congé annuel
     * @param int $staff_id
     * @return int
     */
    public function get_jours_conge_annuel_pris($staff_id) {
        // Récupérer l'ID du type "Congé annuel"
        $type = $this->db->get_where('leave_types', ['type' => 'Congé annuel'])->row();
        if (!$type) return 0;
        $type_id = $type->id;

        $this->db->select_sum('leave_days');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('leave_type_id', $type_id);
        $this->db->where('status', 'approve');
        $query = $this->db->get('staff_leave_request');
        return (int) $query->row()->leave_days;
    }

}
