<?php
/**
 * Modèle pour QR Code Attendance
 */
class Qrattendancemodel extends MY_Model {

    public function __construct() {
        parent::__construct();         
    }

    protected function applyEntrepriseScope($tableAlias = null, $field = 'entreprise_id')
    {
        $admin_session = $this->session->userdata('admin');
        $entreprise_id = 0;

        if (is_array($admin_session) && !empty($admin_session['entreprise_id'])) {
            $entreprise_id = (int) $admin_session['entreprise_id'];
        } else {
            $entreprise_id = (int) ($this->session->userdata('entreprise_id') ?? 0);
        }

        if ($entreprise_id > 0 && $this->db->field_exists('entreprise_id', 'staff')) {
            $tableField = ($tableAlias !== null && $tableAlias !== '') ? $tableAlias . '.' . $field : $field;
            $this->db->where($tableField, $entreprise_id);
        }

        return $this;
    }

    /**
     * Obtenir les présences du jour
     */
    public function get_today_attendance($staff_id = null) {
        $today = date('Y-m-d');

        $this->db->select('
            sa.id,
            sa.staff_id,
            sa.arrival_time,
            sa.departure_time,
            sa.status,
            sa.photo_path,
            sa.verification_status,
            s.name,
            s.surname,
            s.employee_id,
            s.contact_no,
            TIME_FORMAT(TIMEDIFF(sa.departure_time, sa.arrival_time), "%H:%i:%s") as duration
        ')
        ->from('staff_attendance_qr sa')
        ->join('staff s', 's.id = sa.staff_id', 'inner')
        ->where('sa.attendance_date', $today);

        $this->applyEntrepriseScope('s');

        if ($staff_id) {
            $this->db->where('sa.staff_id', $staff_id);
        }

        $this->db->order_by('sa.arrival_time', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Obtenir les statistiques du jour
     */
    public function get_today_stats() {
        $today = date('Y-m-d');

        // Total des pointages du jour
        $this->db->select('COUNT(*) as total')
                 ->from('staff_attendance_qr sa')
                 ->join('staff s', 's.id = sa.staff_id', 'inner')
                 ->where('sa.attendance_date', $today);
        $this->applyEntrepriseScope('s');
        $result = $this->db->get()->row();
        $total = $result ? (int)$result->total : 0;

        // Arrivées (nombre de personnes qui ont pointé aujourd'hui)
        // C'est le même que total car chaque enregistrement est une arrivée
        $arrivals = $total;

        // Départs (nombre de personnes qui ont un départ enregistré)
        $this->db->select('COUNT(*) as total')
                 ->from('staff_attendance_qr sa')
                 ->join('staff s', 's.id = sa.staff_id', 'inner')
                 ->where('sa.attendance_date', $today)
                 ->where('sa.departure_time IS NOT NULL');
        $this->applyEntrepriseScope('s');
        $result = $this->db->get()->row();
        $departures = $result ? (int)$result->total : 0;

        // En attente (arrivées sans départ)
        $incomplete = $total - $departures;

        return [
            'total' => $total,
            'arrivals' => $arrivals,    // AJOUTÉ : cette clé était manquante
            'departures' => $departures,
            'incomplete' => $incomplete
        ];
    }

    /**
     * Enregistrer une présence
     */
    public function register_attendance($staff_id, $arrival_time, $departure_time = null) {
        $today = date('Y-m-d');

        $data = [
            'staff_id' => $staff_id,
            'attendance_date' => $today,
            'arrival_time' => $arrival_time,
            'departure_time' => $departure_time,
            'scan_date' => date('Y-m-d H:i:s'),
            'status' => $departure_time ? 'complete' : 'arrival'
        ];

        return $this->db->insert('staff_attendance_qr', $data);
    }

    /**
     * Mettre à jour le départ
     */
    public function update_departure($staff_id, $departure_time) {
        $today = date('Y-m-d');

        $data = [
            'departure_time' => $departure_time,
            'status' => 'complete'
        ];

        $this->db->where('staff_id', $staff_id)
                 ->where('attendance_date', $today)
                 ->update('staff_attendance_qr', $data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Obtenir le dernier scan du jour
     */
    public function get_today_record($staff_id) {
        $today = date('Y-m-d');

        $this->db->select('*')
                 ->from('staff_attendance_qr')
                 ->where('staff_id', $staff_id)
                 ->where('attendance_date', $today)
                 ->order_by('id', 'DESC')
                 ->limit(1);

        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Obtenir le rapport sur une période
     */
    public function get_report($start_date, $end_date, $staff_id = null) {
        $this->db->select('
            sa.id,
            sa.staff_id,
            sa.arrival_time,
            sa.departure_time,
            sa.attendance_date,
            sa.status,
            sa.verification_status,
            sa.verification_details,
            sa.photo_path,
            s.name,
            s.surname,
            s.employee_id,
            s.contact_no,
            TIME_FORMAT(TIMEDIFF(sa.departure_time, sa.arrival_time), "%H:%i:%s") as duration
        ')
        ->from('staff_attendance_qr sa')
        ->join('staff s', 's.id = sa.staff_id', 'inner')
        ->where('sa.attendance_date >=', $start_date)
        ->where('sa.attendance_date <=', $end_date);

        $this->applyEntrepriseScope('s');

        if ($staff_id) {
            $this->db->where('sa.staff_id', $staff_id);
        }

        $this->db->order_by('sa.attendance_date', 'DESC');
        $this->db->order_by('sa.arrival_time', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Vérifier les absences
     */
    public function get_absences($date) {
        $subquery = $this->db->select('staff_id')
                             ->from('staff_attendance_qr')
                             ->where('attendance_date', $date)
                             ->get_compiled_select();

        $this->db->select('s.id, s.name, s.surname, s.employee_id')
                 ->from('staff s')
                 ->where('s.is_active', 1)
                 ->where_not_in('s.id', $subquery);

        $this->applyEntrepriseScope('s');

        return $this->db->get()->result_array();
    }

    /**
     * Obtenir la durée moyenne de travail pour une période
     */
    public function get_average_duration($start_date, $end_date) {
        $this->db->select_avg('
            HOUR(TIMEDIFF(departure_time, arrival_time)) * 3600 + 
            MINUTE(TIMEDIFF(departure_time, arrival_time)) * 60 + 
            SECOND(TIMEDIFF(departure_time, arrival_time)) as avg_seconds
        ')
        ->from('staff_attendance_qr')
        ->where('attendance_date >=', $start_date)
        ->where('attendance_date <=', $end_date)
        ->where('departure_time IS NOT NULL');

        $result = $this->db->get()->row();
        
        if ($result && $result->avg_seconds) {
            $hours = floor($result->avg_seconds / 3600);
            $minutes = floor(($result->avg_seconds % 3600) / 60);
            $seconds = $result->avg_seconds % 60;
            return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
        }
        
        return '00:00:00';
    }
}
?>