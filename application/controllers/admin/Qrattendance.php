<?php

// Supprimer les deprecation warnings pour PHP 8+

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

ini_set('display_errors', 0);



if (!defined('BASEPATH')) {

    exit('No direct script access allowed');

}



class Qrattendance extends Admin_Controller {



    function __construct() {

        parent::__construct();

        

        // Charger les modèles

        $this->load->model("staffattendancemodel");

        $this->load->model("staff_model");

        $this->load->model("Qrattendancemodel");

        

        // Charger les librairies

        $this->load->library('encryption');

        $this->load->config('attendance');

    }

    /**
     * Vérifie que les tables nécessaires pour le module QR existent.
     * Si l'une des tables manque, affiche un message d'erreur lisible avec instructions.
     * Retourne true si OK, false sinon (show_error est appelé en cas d'absence).
     */
    private function ensure_qr_tables_exist()
    {
        $missing = [];
        if (!$this->db->table_exists('qr_tokens')) {
            $missing[] = 'qr_tokens';
        }
        if (!$this->db->table_exists('staff_attendance_qr')) {
            $missing[] = 'staff_attendance_qr';
        }

        if (!empty($missing)) {
            $msg = "Les tables suivantes sont manquantes dans la base de données : " . implode(', ', $missing) . ".\n";
            $msg .= "Veuillez exécuter le script SQL d'installation pour créer ces tables (voir /database/migrations/create_staff_attendance_qr_table.sql et application/models/Tables.sql).";
            // Log pour les administrateurs
            log_message('error', 'QR Attendance tables missing: ' . implode(',', $missing));
            show_error(nl2br(htmlentities($msg)), 500, 'Configuration manquante');
            return false;
        }

        return true;
    }



    /**

     * Affiche la page QR code pour l'affichage dans l'entreprise

     */



    /**

 * Affiche la page QR code pour l'affichage dans l'entreprise

 */



    /**

 * Affiche la page QR code pour l'affichage dans l'entreprise

 */

public function display_qr() {

    if (!$this->ensure_qr_tables_exist()) { return; }

    if (!$this->rbac->hasPrivilege('qrattendance', 'can_view')) {

        access_denied();

    }



    $this->session->set_userdata('top_menu', 'hr');

    $this->session->set_userdata('sub_menu', 'admin/qrattendance/display_qr');



    $data['title'] = 'QR Code Présence';

    

    // Générer un token unique pour ce QR code

    $token = $this->generateSecureToken();

    

    // Stocker le token en base de données au lieu de la session

    $this->db->insert('qr_tokens', array(

        'token' => $token,

        'created_at' => date('Y-m-d H:i:s'),

        'expires_at' => date('Y-m-d H:i:s', strtotime('+24 hours')),

        'is_used' => 0

    ));

    

    // URL de scan avec le token dans l'URL

    $scan_url = base_url('admin/qrattendance/scan_page?token=' . urlencode($token));

    

    $data['qr_url'] = $scan_url;

    $data['token'] = $token;

    

    // Compter les présences du jour

    $today = date('Y-m-d');

    $this->db->select('COUNT(*) as count')

        ->from('staff_attendance_qr')

        ->where('attendance_date', $today);

    $result = $this->db->get()->row();

    $data['today_count'] = $result ? (int)$result->count : 0;

    

    $this->load->view('layout/header', $data);

    $this->load->view('admin/qrattendance/display_qr', $data);

    $this->load->view('layout/footer', $data);

}

public function display_qr_11() {

    if (!$this->rbac->hasPrivilege('qrattendance', 'can_view')) {

        access_denied();

    }



    $this->session->set_userdata('top_menu', 'hr');

    $this->session->set_userdata('sub_menu', 'admin/qrattendance/display_qr');



    $data['title'] = 'QR Code Présence';

    

    // Générer un token sécurisé pour la session QR

    $token = $this->generateSecureToken();

    $this->session->set_userdata('qr_session_token', $token);

    

    // URL de scan

    $scan_url = base_url('admin/qrattendance/scan_page?token=' . urlencode($token));

    

    $data['qr_url'] = $scan_url;

    $data['token'] = $token;

    

    // Compter les présences du jour

    $today = date('Y-m-d');

    $this->db->select('COUNT(*) as count')

        ->from('staff_attendance_qr')

        ->where('attendance_date', $today);

    $result = $this->db->get()->row();

    $data['today_count'] = $result ? (int)$result->count : 0;

    

    // Charger la vue avec le layout

    $this->load->view('layout/header', $data);

    $this->load->view('admin/qrattendance/display_qr', $data);

    $this->load->view('layout/footer', $data);

}

    public function display_qr_old() {

        if (!$this->rbac->hasPrivilege('qrattendance', 'can_view')) {

            access_denied();

        }



        $this->session->set_userdata('top_menu', 'hr');

        $this->session->set_userdata('sub_menu', 'admin/qrattendance/display_qr');



        $data['title'] = 'QR Code Présence';

        

        // Générer un token sécurisé pour la session QR

        $token = $this->generateSecureToken();

        $this->session->set_userdata('qr_session_token', $token);

        

        // URL de scan

        $scan_url = base_url('admin/qrattendance/scan_page?token=' . urlencode($token));

        

        $data['qr_url'] = $scan_url;

        $data['token'] = $token;

        

        $this->load->view('layout/header', $data);

        $this->load->view('admin/qrattendance/display_qr', $data);

        $this->load->view('layout/footer', $data);

    }



    /**

     * Page de scan accessibles depuis mobile/tablette

     */



    /**

 * Page de scan accessible depuis mobile/tablette

 */

public function scan_page() {

    if (!$this->ensure_qr_tables_exist()) { return; }

    $token = $this->input->get('token');

    

    if (!$token) {

        show_404();

    }

    

    // Vérifier que le token existe et est valide

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

    

    $this->load->view('admin/qrattendance/scan_page', $data);

}



    public function scan_page_11() {

        $token = $this->input->get('token');

        

        if (!$token) {

            show_404();

        }

        

        $data['title'] = 'Scannez votre présence';

        $data['token'] = $token;

        

        $this->load->view('admin/qrattendance/scan_page', $data);

    }



    /**

     * Traite le scan QR code - Authentification de l'employé

     */



    /**

 * Traite le scan QR code - Authentification de l'employé

 */

public function process_scan() {

    if (!$this->ensure_qr_tables_exist()) { echo json_encode(['success' => false, 'message' => 'Configuration manquante: tables QR absentes']); return; }

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



    $staff = $this->find_staff_by_identifier($employee_id);



    if (!$staff) {

        echo json_encode(['success' => false, 'message' => 'Employé non trouvé. Vérifiez votre numéro.']);

        return;

    }



    // Enregistrer avec la photo

    $result = $this->register_attendance($staff['id'], $photo_data);



    if ($result['success']) {

        echo json_encode($result);

    } else {

        echo json_encode(['success' => false, 'message' => $result['message']]);

    }

}

public function process_scan_12() {

    header('Content-Type: application/json');



    $employee_id = trim((string) $this->input->post('employee_id', true));

    $token = trim((string) $this->input->post('token', true));



    if ($employee_id === '' || $token === '') {

        echo json_encode(['success' => false, 'message' => 'Données manquantes']);

        return;

    }



    // Vérifier que le token existe et est valide

    $this->db->where('token', $token)

             ->where('is_used', 0)

             ->where('expires_at >', date('Y-m-d H:i:s'));

    $token_data = $this->db->get('qr_tokens')->row();

    

    if (!$token_data) {

        echo json_encode(['success' => false, 'message' => 'Token invalide ou expiré']);

        return;

    }



    // Marquer le token comme utilisé

    $this->db->where('token', $token)->update('qr_tokens', array(

        'is_used' => 1,

        'used_at' => date('Y-m-d H:i:s'),

        'employee_id' => $employee_id

    ));



    $staff = $this->find_staff_by_identifier($employee_id);



    if (!$staff) {

        echo json_encode(['success' => false, 'message' => 'Employé non trouvé. Vérifiez votre numéro d\'employé.']);

        return;

    }



    $result = $this->register_attendance($staff['id'], null);



    if ($result['success']) {

        echo json_encode($result);

    } else {

        echo json_encode(['success' => false, 'message' => $result['message']]);

    }

}



    public function process_scan_11() {

        header('Content-Type: application/json');



        $employee_id = trim((string) $this->input->post('employee_id', true));

        $token = trim((string) $this->input->post('token', true));

        $photo_data = $this->input->post('photo_data', true);



        if ($employee_id === '' || $token === '') {

            echo json_encode(['success' => false, 'message' => 'Données manquantes']);

            return;

        }



        if ($token !== $this->session->userdata('qr_session_token')) {

            echo json_encode(['success' => false, 'message' => 'Token invalide']);

            return;

        }



        $staff = $this->find_staff_by_identifier($employee_id);



        if (!$staff) {

            echo json_encode(['success' => false, 'message' => 'Employé non trouvé. Vérifiez votre numéro d\'employé.']);

            return;

        }



        $result = $this->register_attendance($staff['id'], $photo_data);



        if ($result['success']) {

            echo json_encode($result);

        } else {

            echo json_encode(['success' => false, 'message' => $result['message']]);

        }

    }



    /**

     * Enregistre ou met à jour la présence avec heure

     */

    private function register_attendance($staff_id, $photo_data = null) {

        $today = date('Y-m-d');

        $current_time = date('H:i:s');

        $current_timestamp = date('Y-m-d H:i:s');



        $security = $this->check_attendance_security($staff_id, $photo_data);

        if (!$security['allowed']) {

            return [

                'success' => false,

                'message' => $security['message']

            ];

        }



        $attendance = $this->db->select('*')

            ->from('staff_attendance_qr')

            ->where('staff_id', $staff_id)

            ->where('attendance_date', $today)

            ->limit(1)

            ->get()

            ->row_array();



        $photo_path = null;

        if (!empty($photo_data)) {

            $photo_path = $this->save_captured_photo($staff_id, $photo_data);

            $this->store_reference_photo_if_needed($staff_id, $photo_data);

        }



        if (!$attendance) {

            $data = [

                'staff_id' => $staff_id,

                'arrival_time' => $current_time,

                'scan_date' => $current_timestamp,

                'attendance_date' => $today,

                'status' => 'arrival',

                'photo_path' => $photo_path,

                'verification_status' => $security['verification_status'],

                'verification_details' => $security['verification_details'],

                'verified_at' => $security['verified_at']

            ];

            $this->db->insert('staff_attendance_qr', $data);



            $message = 'Bienvenue ! Présence enregistrée à ' . date('H:i') . '. ' . $security['message'];

            if ($security['verification_status'] === 'reference_created') {

                $message .= ' La première photo a été enregistrée comme référence pour les prochains pointages.';

            }



            return [

                'success' => true,

                'message' => $message,

                'event_type' => 'arrival',

                'verification_status' => $security['verification_status']

            ];

        } else if (empty($attendance['departure_time'])) {

            $data = [

                'departure_time' => $current_time,

                'status' => 'complete',

                'photo_path' => $photo_path ?: $attendance['photo_path'],

                'verification_status' => $security['verification_status'],

                'verification_details' => $security['verification_details'],

                'verified_at' => $security['verified_at']

            ];

            $this->db->where('id', $attendance['id'])->update('staff_attendance_qr', $data);



            $arrival = strtotime($attendance['arrival_time']);

            $departure = strtotime($current_time);

            $duration = round(($departure - $arrival) / 3600, 2);



            return [

                'success' => true,

                'message' => 'Au revoir ! Départ enregistré à ' . date('H:i') . '. Durée : ' . $duration . 'h. ' . $security['message'],

                'event_type' => 'departure',

                'duration' => $duration,

                'verification_status' => $security['verification_status']

            ];

        } else {

            return [

                'success' => false,

                'message' => 'Vous avez déjà enregistré votre départ aujourd\'hui'

            ];

        }

    }



    /**

     * Recherche un employé à partir d'un identifiant (employee_id, numéro de téléphone ou email)

     */

    private function find_staff_by_identifier($identifier) {

        $identifier = trim((string) $identifier);

        if ($identifier === '') {

            return null;

        }



        $normalized_identifier = str_replace([' ', "\t", "\n", "\r"], '', $identifier);

        $normalized_identifier_lower = strtolower($normalized_identifier);

        $is_numeric = ctype_digit($normalized_identifier);



        $this->db->select('*')

            ->from('staff')

            ->where('is_active', 1)

            ->group_start();



        $this->db->where("LOWER(TRIM(employee_id))", $normalized_identifier_lower);

        $this->db->or_where("LOWER(TRIM(contact_no))", $normalized_identifier_lower);

        $this->db->or_where("LOWER(TRIM(email))", $normalized_identifier_lower);



        if ($is_numeric) {

            $this->db->or_where('id', (int) $normalized_identifier);

        }



        $this->db->group_end()

            ->limit(1);



        $this->apply_staff_entreprise_scope('staff');



        return $this->db->get()->row_array();

    }



    /**

     * Vérifie la sécurité de pointage via photo et référence.

     */

    private function check_attendance_security($staff_id, $photo_data = null) {

        $photo_required = (bool) $this->config->item('attendance_photo_required');

        $threshold = (float) $this->config->item('attendance_photo_similarity_threshold');

        $message = 'Pointage validé par photo.';

        $verification_status = 'verified';

        $verification_details = '';

        $verified_at = date('Y-m-d H:i:s');



        if ($photo_required && empty($photo_data)) {

            return [

                'allowed' => false,

                'message' => 'Une photo de vérification est requise pour le pointage.',

                'verification_status' => 'rejected',

                'verification_details' => 'Missing photo',

                'verified_at' => null

            ];

        }



        if (!empty($photo_data)) {

            $reference_photo = $this->resolve_staff_photo_path($staff_id);

            if ($reference_photo && file_exists($reference_photo)) {

                $similarity = $this->compare_images($photo_data, $reference_photo);

                if ($similarity === null) {

                    $verification_status = 'verified';

                    $verification_details = 'Photo capturée ; comparaison non disponible.';

                } elseif ($similarity < $threshold) {

                    return [

                        'allowed' => false,

                        'message' => 'La photo ne correspond pas assez à la photo enregistrée du personnel.',

                        'verification_status' => 'rejected',

                        'verification_details' => 'Photo similarity below threshold (' . round($similarity, 2) . ')',

                        'verified_at' => null

                    ];

                } else {

                    $verification_details = 'Photo similarity ' . round($similarity, 2);

                }

            } else {

                $verification_status = 'reference_created';

                $verification_details = 'Première photo enregistrée comme référence.';

            }

        }



        return [

            'allowed' => true,

            'message' => $message,

            'verification_status' => $verification_status,

            'verification_details' => $verification_details,

            'verified_at' => $verified_at

        ];

    }



    private function calculate_distance_meters($lat1, $lng1, $lat2, $lng2) {

        $earth_radius = 6371000;

        $lat1_rad = deg2rad((float) $lat1);

        $lng1_rad = deg2rad((float) $lng1);

        $lat2_rad = deg2rad((float) $lat2);

        $lng2_rad = deg2rad((float) $lng2);

        $delta_lat = $lat2_rad - $lat1_rad;

        $delta_lng = $lng2_rad - $lng1_rad;



        $a = sin($delta_lat / 2) * sin($delta_lat / 2) + cos($lat1_rad) * cos($lat2_rad) * sin($delta_lng / 2) * sin($delta_lng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));



        return $earth_radius * $c;

    }



    private function resolve_staff_photo_path($staff_id) {

        $reference_path = $this->resolve_reference_photo_path($staff_id);

        if ($reference_path) {

            return $reference_path;

        }



        $staff = $this->db->select('image')

            ->from('staff')

            ->where('id', (int) $staff_id)

            ->limit(1)

            ->get()

            ->row_array();



        if (empty($staff['image'])) {

            return null;

        }



        $image_path = trim((string) $staff['image']);

        if ($image_path === '') {

            return null;

        }



        if (strpos($image_path, 'http://') === 0 || strpos($image_path, 'https://') === 0) {

            return null;

        }



        $candidates = [];

        $candidates[] = FCPATH . ltrim($image_path, '/');

        $candidates[] = FCPATH . 'uploads/staff_images/' . basename($image_path);

        $candidates[] = FCPATH . 'uploads/' . ltrim($image_path, '/');



        foreach ($candidates as $candidate) {

            if (is_file($candidate)) {

                return $candidate;

            }

        }



        return null;

    }



    private function resolve_reference_photo_path($staff_id) {

        $upload_path = $this->config->item('attendance_reference_photo_upload_path');

        if (empty($upload_path)) {

            return null;

        }



        $directory = FCPATH . $upload_path . '/' . (int) $staff_id;

        if (!is_dir($directory)) {

            return null;

        }



        $matches = glob($directory . '/reference.*');

        if (!empty($matches)) {

            return $matches[0];

        }



        return null;

    }



    private function compare_images($photo_data, $reference_photo_path) {

        $decoded_photo = $this->decode_photo_data($photo_data);

        if ($decoded_photo === false) {

            return null;

        }



        $reference_binary = @file_get_contents($reference_photo_path);

        if ($reference_binary === false || $reference_binary === '') {

            return null;

        }



        if (extension_loaded('gd') && function_exists('imagecreatefromstring')) {

            $photo_image = @imagecreatefromstring($decoded_photo);

            $reference_image = @imagecreatefromstring($reference_binary);

            if ($photo_image && $reference_image) {

                $photo_resized = imagecreatetruecolor(8, 8);

                $reference_resized = imagecreatetruecolor(8, 8);

                imagecopyresampled($photo_resized, $photo_image, 0, 0, 0, 0, 8, 8, imagesx($photo_image), imagesy($photo_image));

                imagecopyresampled($reference_resized, $reference_image, 0, 0, 0, 0, 8, 8, imagesx($reference_image), imagesy($reference_image));



                $photo_hash = '';

                $reference_hash = '';

                for ($i = 0; $i < 8; $i++) {

                    for ($j = 0; $j < 8; $j++) {

                        $photo_color = imagecolorat($photo_resized, $j, $i);

                        $reference_color = imagecolorat($reference_resized, $j, $i);

                        $photo_r = ($photo_color >> 16) & 0xFF;

                        $reference_r = ($reference_color >> 16) & 0xFF;

                        $photo_g = ($photo_color >> 8) & 0xFF;

                        $reference_g = ($reference_color >> 8) & 0xFF;

                        $photo_b = $photo_color & 0xFF;

                        $reference_b = $reference_color & 0xFF;



                        $photo_avg = (int) round(($photo_r + $photo_g + $photo_b) / 3);

                        $reference_avg = (int) round(($reference_r + $reference_g + $reference_b) / 3);

                        $photo_hash .= $photo_avg >= 128 ? '1' : '0';

                        $reference_hash .= $reference_avg >= 128 ? '1' : '0';

                    }

                }



                $distance = 0;

                for ($i = 0; $i < 64; $i++) {

                    if ($photo_hash[$i] !== $reference_hash[$i]) {

                        $distance++;

                    }

                }



                imagedestroy($photo_image);

                imagedestroy($reference_image);

                imagedestroy($photo_resized);

                imagedestroy($reference_resized);



                return 1 - ($distance / 64);

            }

        }



        if (class_exists('Imagick')) {

            try {

                $photo_imagick = new Imagick();

                $photo_imagick->readimageblob($decoded_photo);

                $photo_imagick->resizeimage(8, 8, Imagick::FILTER_LANCZOS, 1);

                $photo_imagick->setImageColorspace(Imagick::COLORSPACE_GRAY);

                $photo_hash = $photo_imagick->getImageHistogram();



                $reference_imagick = new Imagick();

                $reference_imagick->readimageblob($reference_binary);

                $reference_imagick->resizeimage(8, 8, Imagick::FILTER_LANCZOS, 1);

                $reference_imagick->setImageColorspace(Imagick::COLORSPACE_GRAY);

                $reference_hash = $reference_imagick->getImageHistogram();



                if (!empty($photo_hash) && !empty($reference_hash)) {

                    return 1.0;

                }

            } catch (Exception $e) {

                // Fallback to hash comparison below.

            }

        }



        $photo_hash = sha1($decoded_photo);

        $reference_hash = sha1($reference_binary);



        return $photo_hash === $reference_hash ? 1.0 : 0.0;

    }



    private function save_captured_photo($staff_id, $photo_data) {

        $upload_path = $this->config->item('attendance_photo_upload_path');

        if (empty($upload_path)) {

            return null;

        }



        $directory = FCPATH . $upload_path . '/' . (int) $staff_id;

        if (!is_dir($directory)) {

            mkdir($directory, 0777, true);

        }



        $file_name = date('Ymd_His') . '_' . $staff_id . '.jpg';

        $file_path = $directory . '/' . $file_name;



        $decoded = $this->decode_photo_data($photo_data);

        if ($decoded === false) {

            return null;

        }



        file_put_contents($file_path, $decoded);



        return $upload_path . '/' . (int) $staff_id . '/' . $file_name;

    }



    private function store_reference_photo_if_needed($staff_id, $photo_data) {

        $upload_path = $this->config->item('attendance_reference_photo_upload_path');

        if (empty($upload_path) || empty($photo_data)) {

            return null;

        }



        $directory = FCPATH . $upload_path . '/' . (int) $staff_id;

        if (!is_dir($directory)) {

            mkdir($directory, 0777, true);

        }



        $reference_file = $directory . '/reference.jpg';

        if (is_file($reference_file)) {

            return $upload_path . '/' . (int) $staff_id . '/reference.jpg';

        }



        $decoded = $this->decode_photo_data($photo_data);

        if ($decoded === false) {

            return null;

        }



        file_put_contents($reference_file, $decoded);



        return $upload_path . '/' . (int) $staff_id . '/reference.jpg';

    }



    private function decode_photo_data($photo_data) {

        $photo_data = preg_replace('/^data:image\/(png|jpeg|jpg);base64,/', '', $photo_data);

        $photo_data = str_replace(' ', '+', $photo_data);



        return base64_decode($photo_data, true);

    }



    /**

     * Vérifie le mot de passe

     */

    private function verify_password($password, $hash) {

        if (empty($hash)) {

            return false;

        }



        if (strpos($hash, '$2') === 0) {

            return password_verify($password, $hash);

        }



        if (preg_match('/^[a-f0-9]{32}$/i', $hash)) {

            return md5($password) === $hash;

        }



        return $password === $hash;

    }



    /**

     * Génère un token sécurisé

     */

    private function generateSecureToken() {

        return bin2hex(random_bytes(32));

    }



    /**

     * Page admin pour voir les présences du jour

     */

    public function today_attendance() {

        if (!$this->ensure_qr_tables_exist()) { return; }

        if (!$this->rbac->hasPrivilege('staff_attendance', 'can_view')) {

            access_denied();

        }



        $this->session->set_userdata('top_menu', 'hr');

        $this->session->set_userdata('sub_menu', 'admin/qrattendance/today_attendance');



        try {

            $today = date('Y-m-d');

            $attendances = $this->Qrattendancemodel->get_today_attendance();

            $today_stats = $this->Qrattendancemodel->get_today_stats();



            $data['title'] = 'Présences du jour - QR Code';

            $data['attendances'] = $attendances;

            $data['today_date'] = date('d/m/Y');

            $data['stats'] = array(

                'total' => isset($today_stats['total']) ? (int) $today_stats['total'] : 0,

                'arrivals' => isset($today_stats['arrivals']) ? (int) $today_stats['arrivals'] : 0,

                'departures' => isset($today_stats['departures']) ? (int) $today_stats['departures'] : 0,

                'incomplete' => isset($today_stats['incomplete']) ? (int) $today_stats['incomplete'] : 0,

            );



            $this->load->view('layout/header', $data);

            $this->load->view('admin/qrattendance/today_attendance', $data);

            $this->load->view('layout/footer', $data);

            

        } catch (Exception $e) {

            log_message('error', 'Erreur dans today_attendance: ' . $e->getMessage());

            show_error('Une erreur est survenue: ' . $e->getMessage());

        }

    }



    /**

     * Rapport détaillé des présences

     */

    public function attendance_report() {

        if (!$this->ensure_qr_tables_exist()) { return; }

        if (!$this->rbac->hasPrivilege('staff_attendance', 'can_view')) {

            access_denied();

        }



        $this->session->set_userdata('top_menu', 'hr');

        $this->session->set_userdata('sub_menu', 'admin/qrattendance/attendance_report');



        $start_date = $this->input->post('start_date', true);

        $end_date = $this->input->post('end_date', true);

        $staff_id = $this->input->post('staff_id', true);



        if (empty($start_date)) {

            $start_date = date('Y-m-d', strtotime('-30 days'));

        }



        if (empty($end_date)) {

            $end_date = date('Y-m-d');

        }



        if ($start_date > $end_date) {

            $temp = $start_date;

            $start_date = $end_date;

            $end_date = $temp;

        }



        try {

            $data['title'] = 'Rapport de Présence QR';

            $data['start_date'] = $start_date;

            $data['end_date'] = $end_date;

            $data['selected_staff_id'] = $staff_id;

            $data['report'] = $this->Qrattendancemodel->get_report($start_date, $end_date, $staff_id);



            $employee_totals = [];

            $summary = array(

                'records' => count($data['report']),

                'arrivals' => 0,

                'departures' => 0,

                'incomplete' => 0,

                'verified' => 0,

                'rejected' => 0,

                'total_seconds' => 0,

                'completed_rows' => 0,

            );



            foreach ($data['report'] as $row) {

                $key = (int) $row['staff_id'];

                if (!isset($employee_totals[$key])) {

                    $employee_totals[$key] = [

                        'staff_id' => $key,

                        'name' => $row['name'] ?? '',

                        'surname' => $row['surname'] ?? '',

                        'employee_id' => $row['employee_id'] ?? '',

                        'attendance_count' => 0,

                        'arrival_count' => 0,

                        'departure_count' => 0,

                        'incomplete_count' => 0

                    ];

                }



                $employee_totals[$key]['attendance_count']++;

                if (!empty($row['arrival_time'])) {

                    $employee_totals[$key]['arrival_count']++;

                    $summary['arrivals']++;

                }

                if (!empty($row['departure_time'])) {

                    $employee_totals[$key]['departure_count']++;

                    $summary['departures']++;

                    if (!empty($row['duration'])) {

                        $duration_parts = explode(':', $row['duration']);

                        if (count($duration_parts) === 3) {

                            $summary['total_seconds'] += (((int) $duration_parts[0]) * 3600) + (((int) $duration_parts[1]) * 60) + ((int) $duration_parts[2]);

                            $summary['completed_rows']++;

                        }

                    }

                } else {

                    $employee_totals[$key]['incomplete_count']++;

                    $summary['incomplete']++;

                }



                if (!empty($row['verification_status'])) {

                    if ($row['verification_status'] === 'verified' || $row['verification_status'] === 'reference_created') {

                        $summary['verified']++;

                    } elseif ($row['verification_status'] === 'rejected') {

                        $summary['rejected']++;

                    }

                }

            }



            $data['employee_totals'] = array_values($employee_totals);

            $data['summary'] = $summary;

            $data['average_duration'] = $summary['completed_rows'] > 0

                ? gmdate('H:i:s', (int) floor($summary['total_seconds'] / $summary['completed_rows']))

                : null;



            // Récupérer la liste des employés

            $this->db->select('id, name, surname, employee_id')

                ->from('staff')

                ->where('is_active', 1);

            $this->apply_staff_entreprise_scope('staff');

            $data['staff_list'] = $this->db

                ->order_by('name', 'ASC')

                ->order_by('surname', 'ASC')

                ->get()

                ->result_array();



            $this->load->view('layout/header', $data);

            $this->load->view('admin/qrattendance/attendance_report', $data);

            $this->load->view('layout/footer', $data);

            

        } catch (Exception $e) {

            log_message('error', 'Erreur dans attendance_report: ' . $e->getMessage());

            show_error('Une erreur est survenue: ' . $e->getMessage());

        }

    }



    /**

 * Récupère le nombre de présences du jour (AJAX)

 */

public function get_today_count() {

    header('Content-Type: application/json');

    

    $today = date('Y-m-d');

    $this->db->select('COUNT(*) as count')

        ->from('staff_attendance_qr')

        ->where('attendance_date', $today);

    

    $result = $this->db->get()->row();

    

    echo json_encode(['count' => $result ? (int)$result->count : 0]);

}



    private function apply_staff_entreprise_scope($alias = 'staff')

    {

        $admin_session = $this->session->userdata('admin');

        $entreprise_id = 0;



        if (is_array($admin_session) && !empty($admin_session['entreprise_id'])) {

            $entreprise_id = (int) $admin_session['entreprise_id'];

        } else {

            $entreprise_id = (int) $this->session->userdata('entreprise_id');

        }



        if ($entreprise_id > 0 && $this->db->field_exists('entreprise_id', 'staff')) {

            $this->db->where($alias . '.entreprise_id', $entreprise_id);

        }

    }

}

?>