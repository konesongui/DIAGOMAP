<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Attestationrh extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Staff_model');
        $this->load->library('mailer');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('staff', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'hr');
        $this->session->set_userdata('sub_menu', 'admin/attestationrh');

        $data['title'] = 'Attestations RH';
        $data['staffs'] = $this->Staff_model->getAll_users(null, 1);
        $data['document_types'] = array(
            'travail' => array(
                'label' => 'Attestation de travail',
                'icon' => 'fa-file-text-o'
            ),
            'fin_contrat' => array(
                'label' => 'Attestation de fin de contrat',
                'icon' => 'fa-sign-out'
            ),
            'salaire' => array(
                'label' => 'Attestation de salaire',
                'icon' => 'fa-money'
            )
        );

        $this->load->view('layout/header', $data);
        $this->load->view('admin/attestationrh/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function print_document($staff_id = 0, $type = 'travail')
    {
        if (!$this->rbac->hasPrivilege('staff', 'can_view')) {
            access_denied();
        }

        // CORRECTION: Récupération propre des paramètres
        $staff_id = (int) $staff_id;
        $type = $this->clean_document_type($type);

        // Si l'ID est 0, essayer de le récupérer depuis l'URL ou POST
        if ($staff_id <= 0) {
            // Vérifier dans l'URL
            $segments = $this->uri->segment_array();
            foreach ($segments as $segment) {
                if (is_numeric($segment) && (int)$segment > 0) {
                    $staff_id = (int)$segment;
                    break;
                }
            }
            
            // Vérifier dans POST/GET
            if ($staff_id <= 0) {
                $post_staff_id = $this->input->get_post('staff_id', true);
                if ($post_staff_id !== null && $post_staff_id !== '') {
                    $staff_id = (int) $post_staff_id;
                }
            }
        }

        // Récupérer le type depuis l'URL si nécessaire
        if ($type === 'travail' || $type === '') {
            $type_param = $this->uri->segment(5);
            if ($type_param !== null && $type_param !== '') {
                $type = $this->clean_document_type($type_param);
            }
            
            $post_type = $this->input->get_post('type', true);
            if ($post_type !== null && $post_type !== '') {
                $type = $this->clean_document_type($post_type);
            }
        }

        // Récupérer les données de l'employé
        $staff = $this->get_staff_record($staff_id);
        if (!$staff) {
            $this->session->set_flashdata('error', 'Employé introuvable pour cette attestation.');
            redirect('admin/attestationrh');
            return;
        }

        // Préparer les données pour la vue
        $data['title'] = 'Attestation';
        $data['staff'] = $staff;
        $data['type'] = $type;
        $data['document_title'] = $this->get_document_title($type);
        $data['document_code'] = $this->generate_document_code($staff, $type);
        $data['document_html'] = $this->build_document_html($staff, $type, $data['document_code']);
        $data['company_name'] = $this->get_company_name();

        // CORRECTION: Ajouter la vue d'impression
        $this->load->view('admin/attestationrh/print_document', $data);
    }

    public function send_mail($staff_id = 0, $type = 'travail')
    {
        if (!$this->rbac->hasPrivilege('staff', 'can_view')) {
            access_denied();
        }

        $staff = $this->get_staff_record((int) $staff_id);
        if (!$staff) {
            $this->session->set_flashdata('error', 'Employé introuvable pour l\'envoi de cette attestation.');
            redirect('admin/attestationrh');
            return;
        }

        $email = trim((string) ($staff['email'] ?? ''));
        $type = $this->clean_document_type($type);

        if ($email === '') {
            $this->session->set_flashdata('error', 'Cet employé n\'a pas d\'adresse e-mail enregistrée.');
            redirect('admin/attestationrh');
            return;
        }

        // CORRECTION: Construire le HTML complet pour l'email
        $document_code = $this->generate_document_code($staff, $type);
        $document_html = $this->build_document_html($staff, $type, $document_code);
        $company = $this->get_company_name();
        $subject = $this->get_document_title($type) . ' - ' . $company;

        // CORRECTION: Construire un email complet avec CSS
        $full_html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .doc { max-width: 800px; margin: 0 auto; padding: 20px; }
                .header { border-bottom: 2px solid #1b4f80; padding-bottom: 15px; margin-bottom: 20px; }
                .company { font-size: 12px; color: #56708d; }
                h1 { color: #1b4f80; margin: 10px 0 5px; }
                .doc-code { background: #eef4ff; padding: 8px 12px; border-radius: 5px; display: inline-block; }
                .content { line-height: 2; }
                .signature { margin-top: 30px; padding-top: 15px; border-top: 1px solid #dfe7f3; }
            </style>
        </head>
        <body>
            <div class="doc">
                ' . $document_html . '
            </div>
        </body>
        </html>';

        // CORRECTION: Utiliser la méthode send_mail appropriée
        $sent = $this->mailer->send_mail($email, $subject, $full_html);

        if ($sent) {
            $this->session->set_flashdata('success', 'L\'attestation a bien été envoyée par e-mail à ' . $email . '.');
        } else {
            $this->session->set_flashdata('error', 'L\'envoi par e-mail a échoué. Vérifiez la configuration SMTP.');
        }

        redirect('admin/attestationrh');
    }

    private function get_staff_record($staff_id)
    {
        if ($staff_id <= 0) {
            return null;
        }

        // Essayer avec getAll_users d'abord
        $staff = $this->Staff_model->getAll_users($staff_id, 1);
        if (is_array($staff) && !empty($staff)) {
            return $staff[0];
        }

        // Essayer avec get
        return $this->Staff_model->get($staff_id);
    }

    private function clean_document_type($type)
    {
        $allowed = array('travail', 'fin_contrat', 'salaire');
        $value = strtolower(trim((string) $type));
        return in_array($value, $allowed, true) ? $value : 'travail';
    }

    private function get_document_title($type)
    {
        $titles = array(
            'travail' => 'Attestation de travail',
            'fin_contrat' => 'Attestation de fin de contrat',
            'salaire' => 'Attestation de salaire'
        );

        return $titles[$type] ?? 'Attestation';
    }

    private function get_company_name()
    {
        $settings = $this->setting_model->get();
        if (is_array($settings) && !empty($settings)) {
            foreach ($settings as $row) {
                if (!empty($row['name'])) {
                    return $row['name'];
                }
            }
        }

        if (isset($this->sch_setting_detail->name)) {
            return $this->sch_setting_detail->name;
        }

        return 'Entreprise';
    }

    private function generate_document_code($staff, $type)
    {
        $type_code = array(
            'travail' => 'TRV',
            'fin_contrat' => 'FCT',
            'salaire' => 'SAL'
        );

        $employee_ref = trim((string) ($staff['employee_id'] ?? $staff['id'] ?? ''));
        $employee_ref = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($employee_ref));
        $employee_ref = $employee_ref !== '' ? $employee_ref : 'GEN';

        $serial = date('Ymd');
        $type_code = $type_code[$type] ?? 'DOC';

        return 'AT-' . $serial . '-' . $type_code . '-' . $employee_ref;
    }

    private function build_document_html($staff, $type, $document_code = null)
    {
        $company = $this->get_company_name();
        $date = date('d/m/Y');
        $full_name = trim((string) (($staff['name'] ?? '') . ' ' . ($staff['surname'] ?? '')));
        $full_name = $full_name !== '' ? $full_name : ($staff['name'] ?? 'Employé');
        $employee_id = trim((string) ($staff['employee_id'] ?? ''));
        $department = trim((string) ($staff['department'] ?? ($staff['department_name'] ?? '')));
        $designation = trim((string) ($staff['designation'] ?? ''));
        $start_date = !empty($staff['date_of_joining']) ? date('d/m/Y', strtotime($staff['date_of_joining'])) : 'N/A';
        $end_date = !empty($staff['date_of_leaving']) ? date('d/m/Y', strtotime($staff['date_of_leaving'])) : 'N/A';
        $document_code = $document_code ?: $this->generate_document_code($staff, $type);

        // Construction du contenu selon le type
        if ($type === 'fin_contrat') {
            $body = "<p>Nous certifions que <strong>{$full_name}</strong> a occupé le poste de <strong>{$designation}</strong> au sein de <strong>{$company}</strong>, dans le département <strong>{$department}</strong>.</p>
            <p>Son contrat a pris fin le <strong>{$end_date}</strong>.</p>
            <p>Cette attestation est délivrée à titre informatif et pour servir de justification auprès des organismes concernés.</p>";
        } elseif ($type === 'salaire') {
            $salary = !empty($staff['basic_salary']) ? number_format((float) $staff['basic_salary'], 0, ',', ' ') . ' FCFA' : 'Non renseigné';
            $body = "<p>Nous certifions que <strong>{$full_name}</strong>, matricule <strong>{$employee_id}</strong>, est employé(e) au sein de <strong>{$company}</strong>.</p>
            <p>Le salaire mensuel de référence est de <strong>{$salary}</strong>.</p>
            <p>Cette attestation est délivrée à titre de justificatif pour l'organisme concerné.</p>";
        } else {
            $body = "<p>Nous certifions que <strong>{$full_name}</strong>, matricule <strong>{$employee_id}</strong>, travaille au sein de <strong>{$company}</strong> depuis le <strong>{$start_date}</strong>.</p>
            <p>Il/Elle occupe actuellement le poste de <strong>{$designation}</strong> dans le département <strong>{$department}</strong>.</p>
            <p>Cette attestation est délivrée à l'intéressé(e) pour servir à l'usage de droit.</p>";
        }

        return '<div class="header">
            <div class="company">' . htmlspecialchars($company, ENT_QUOTES, 'UTF-8') . ' • ' . htmlspecialchars($date, ENT_QUOTES, 'UTF-8') . '</div>
            <h1>' . htmlspecialchars($this->get_document_title($type), ENT_QUOTES, 'UTF-8') . '</h1>
            <div class="doc-code"><strong>Code d\'attestation :</strong> ' . htmlspecialchars($document_code, ENT_QUOTES, 'UTF-8') . '</div>
        </div>
        <div class="content">
            ' . $body . '
        </div>
        <div class="signature">
            <div>Fait à ' . htmlspecialchars($company, ENT_QUOTES, 'UTF-8') . ', le ' . htmlspecialchars($date, ENT_QUOTES, 'UTF-8') . '</div>
            <div class="name">Direction / Ressources Humaines</div>
        </div>';
    }
}
?>