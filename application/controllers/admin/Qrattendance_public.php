<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Qrattendance_public extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staffattendancemodel');
        $this->load->model('staff_model');
        $this->load->model('Qrattendancemodel');
        $this->load->library('encryption');
        $this->load->config('attendance');
        
        // 🔥 AJOUT : Permettre les requêtes CORS pour le mobile
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST');
        header('Access-Control-Allow-Headers: Content-Type');
    }

    public function scan_page()
    {
        $token = $this->input->get('token');

        if (!$token) {
            show_404();
            return;
        }

        // Vérifier le token
        $this->db->where('token', $token)
            ->where('is_used', 0)
            ->where('expires_at >', date('Y-m-d H:i:s'));
        $token_data = $this->db->get('qr_tokens')->row();

        if (!$token_data) {
            show_error('Ce code QR a expiré ou a déjà été utilisé. Veuillez contacter l\'administrateur.', 403);
            return;
        }

        $data['title'] = 'Scannez votre présence';
        $data['token'] = $token;

        // Charger la vue (sans le layout admin)
        $this->load->view('admin/qrattendance/scan_page', $data);
    }

    public function process_scan()
    {
        header('Content-Type: application/json');

        $employee_id = trim((string) $this->input->post('employee_id', true));
        $token = trim((string) $this->input->post('token', true));
        $photo_data = $this->input->post('photo_data', true);

        if ($employee_id === '' || $token === '') {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            return;
        }

        // Vérifier le token
        $this->db->where('token', $token)
            ->where('is_used', 0)
            ->where('expires_at >', date('Y-m-d H:i:s'));
        $token_data = $this->db->get('qr_tokens')->row();

        if (!$token_data) {
            echo json_encode(['success' => false, 'message' => 'Token invalide ou expiré']);
            return;
        }

        // Vérifier la photo
        if (empty($photo_data)) {
            echo json_encode(['success' => false, 'message' => 'Photo requise pour le pointage']);
            return;
        }

        // Marquer le token comme utilisé
        $this->db->where('token', $token)->update('qr_tokens', array(
            'is_used' => 1,
            'used_at' => date('Y-m-d H:i:s'),
            'employee_id' => $employee_id
        ));

        // Rechercher l'employé
        $staff = $this->find_staff_by_identifier($employee_id);

        if (!$staff) {
            echo json_encode(['success' => false, 'message' => 'Employé non trouvé. Vérifiez votre matricule.']);
            return;
        }

        // Enregistrer le pointage
        $result = $this->register_attendance($staff['id'], $photo_data);

        if ($result['success']) {
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => $result['message']]);
        }
    }

    /**
     * Recherche un employé par identifiant
     */
    protected function find_staff_by_identifier($identifier)
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return false;
        }

        // Recherche dans plusieurs champs
        $this->db->select('*')
            ->from('staff')
            ->where('is_active', 1)
            ->group_start()
                ->where('employee_id', $identifier)
                ->or_where('contact_no', $identifier)
                ->or_where('email', $identifier)
                ->or_where('id', $identifier)
            ->group_end()
            ->limit(1);

        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            return $query->row_array();
        }

        return false;
    }

    /**
     * Enregistre la présence avec photo
     */
    protected function register_attendance($staff_id, $photo_data)
    {
        $today = date('Y-m-d');
        $current_time = date('H:i:s');
        $current_timestamp = date('Y-m-d H:i:s');

        // Vérifier si l'employé a déjà pointé aujourd'hui
        $existing = $this->db->select('*')
            ->from('staff_attendance_qr')
            ->where('staff_id', $staff_id)
            ->where('attendance_date', $today)
            ->limit(1)
            ->get()
            ->row_array();

        // Sauvegarder la photo
        $photo_path = $this->save_photo($staff_id, $photo_data);

        if (!$existing) {
            // Nouvelle arrivée
            $data = [
                'staff_id' => $staff_id,
                'attendance_date' => $today,
                'arrival_time' => $current_time,
                'scan_date' => $current_timestamp,
                'status' => 'arrival',
                'photo_path' => $photo_path,
                'verification_status' => 'verified',
                'verification_details' => 'Photo capturée',
                'verified_at' => $current_timestamp
            ];
            
            $this->db->insert('staff_attendance_qr', $data);
            
            return [
                'success' => true,
                'message' => '✅ Arrivée enregistrée avec succès !',
                'event_type' => 'arrival'
            ];
            
        } elseif (empty($existing['departure_time'])) {
            // Départ
            $data = [
                'departure_time' => $current_time,
                'status' => 'complete',
                'photo_path' => $photo_path ?: $existing['photo_path']
            ];
            
            $this->db->where('id', $existing['id'])->update('staff_attendance_qr', $data);
            
            // Calculer la durée
            $arrival = strtotime($existing['arrival_time']);
            $departure = strtotime($current_time);
            $duration = round(($departure - $arrival) / 3600, 2);
            
            return [
                'success' => true,
                'message' => '✅ Départ enregistré ! Durée : ' . $duration . 'h',
                'event_type' => 'departure',
                'duration' => $duration
            ];
            
        } else {
            return [
                'success' => false,
                'message' => '⚠️ Vous avez déjà terminé votre journée.'
            ];
        }
    }

    /**
     * Sauvegarde la photo
     */
    private function save_photo($staff_id, $photo_data)
    {
        $upload_path = 'uploads/attendance_photos/';
        $directory = FCPATH . $upload_path . $staff_id . '/';
        
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $file_name = date('Ymd_His') . '_' . $staff_id . '.jpg';
        $file_path = $directory . $file_name;

        // Décoder la photo base64
        $photo_data = preg_replace('/^data:image\/(png|jpeg|jpg);base64,/', '', $photo_data);
        $photo_data = str_replace(' ', '+', $photo_data);
        $decoded = base64_decode($photo_data);

        if ($decoded === false) {
            return null;
        }

        file_put_contents($file_path, $decoded);

        return $upload_path . $staff_id . '/' . $file_name;
    }
}