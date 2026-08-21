<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Intelligence extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('intelligence_model');
        // Vérification des droits : par défaut pour les services (à adapter selon vos besoins)
        if (!$this->rbac->hasPrivilege('services_commercial', 'can_view')) {
            access_denied();
        }
    }

    /**
     * Liste des services (CRUD principal)
     */
    public function index() {
        $this->session->set_userdata('top_menu', 'RH');
        $this->session->set_userdata('sub_menu', 'intelligence/index');
        $data['services'] = $this->intelligence_model->get(); // table par défaut 'intelligence'
        $this->load->view('layout/header', $data);
        $this->load->view('admin/intelligence/list');
        $this->load->view('layout/footer');
    }

    /**
     * Interface de chat IA
     */
    public function chat_ui() {
        // Vérification de permission spécifique
        if (!$this->rbac->hasPrivilege('ia_chat', 'can_use')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'RH');
        $this->session->set_userdata('sub_menu', 'intelligence/chat_ui');
        $this->load->view('layout/header');
        $this->load->view('admin/intelligence/chat');
        $this->load->view('layout/footer');
    }

    public function history() {
        $this->output->set_content_type('application/json');

        if (!$this->rbac->hasPrivilege('ia_chat', 'can_use')) {
            return $this->output->set_output(json_encode(['error' => 'Accès non autorisé']));
        }

        $user_id = $this->session->userdata('user_id') ?: 0;
        $this->intelligence_model->set_table('ia_conversations');
        $history = $this->intelligence_model->get_history($user_id, 20);

        return $this->output->set_output(json_encode([
            'status' => 'success',
            'history' => array_reverse($history)
        ]));
    }

    // ========== CRUD AJAX (services) ==========
    public function ajax_list() {
        $this->output->set_content_type('application/json');
        $services = $this->intelligence_model->get();
        echo json_encode($services);
    }

    public function ajax_add() {
        $this->output->set_content_type('application/json');
        $this->form_validation->set_rules('name', 'Nom', 'required|trim');
        $this->form_validation->set_rules('unit_price', 'Prix unitaire', 'required|numeric');
        if ($this->form_validation->run() == true) {
            $data = [
                'name'         => $this->input->post('name'),
                'description'  => $this->input->post('description'),
                'unit_price'   => $this->input->post('unit_price'),
                'duration'     => $this->input->post('duration'),
                'created_at'   => date('Y-m-d H:i:s')
            ];
            $id = $this->intelligence_model->add($data);
            echo json_encode(['status' => 'success', 'message' => 'Service ajouté', 'id' => $id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
        }
    }

    public function ajax_edit($id) {
        $this->output->set_content_type('application/json');
        $service = $this->intelligence_model->get($id);
        echo json_encode($service);
    }

    public function ajax_update() {
        $this->output->set_content_type('application/json');
        $id = $this->input->post('id');
        $this->form_validation->set_rules('name', 'Nom', 'required|trim');
        $this->form_validation->set_rules('unit_price', 'Prix unitaire', 'required|numeric');
        if ($this->form_validation->run() == true) {
            $data = [
                'name'         => $this->input->post('name'),
                'description'  => $this->input->post('description'),
                'unit_price'   => $this->input->post('unit_price'),
                'duration'     => $this->input->post('duration'),
                'updated_at'   => date('Y-m-d H:i:s')
            ];
            $this->intelligence_model->update($id, $data);
            echo json_encode(['status' => 'success', 'message' => 'Service mis à jour']);
        } else {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
        }
    }

    public function ajax_delete($id) {
        $this->output->set_content_type('application/json');
        $this->intelligence_model->delete($id);
        echo json_encode(['status' => 'success', 'message' => 'Service supprimé']);
    }

    // ========== CRUD non-AJAX (formulaires) ==========
    public function create() {
        if (!$this->rbac->hasPrivilege('services_commercial', 'can_add')) access_denied();
        $this->form_validation->set_rules('name', 'Nom', 'required|trim');
        $this->form_validation->set_rules('unit_price', 'Prix unitaire', 'required|numeric');

        if ($this->form_validation->run() == true) {
            $data = [
                'name'         => $this->input->post('name'),
                'description'  => $this->input->post('description'),
                'unit_price'   => $this->input->post('unit_price'),
                'duration'     => $this->input->post('duration'),
                'created_at'   => date('Y-m-d H:i:s')
            ];
            $insert_id = $this->intelligence_model->add($data);

            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'Service ajouté', 'id' => $insert_id]);
                return;
            }
            $this->session->set_flashdata('msg', 'Service ajouté');
            redirect('admin/intelligence');
        } else {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => validation_errors()]);
                return;
            }
            $this->load->view('layout/header');
            $this->load->view('admin/intelligence/form');
            $this->load->view('layout/footer');
        }
    }

    public function edit($id) {
        if (!$this->rbac->hasPrivilege('services_commercial', 'can_edit')) access_denied();
        $this->form_validation->set_rules('name', 'Nom', 'required|trim');
        $this->form_validation->set_rules('unit_price', 'Prix unitaire', 'required|numeric');

        if ($this->form_validation->run() == true) {
            $update = [
                'name'         => $this->input->post('name'),
                'description'  => $this->input->post('description'),
                'unit_price'   => $this->input->post('unit_price'),
                'duration'     => $this->input->post('duration'),
                'updated_at'   => date('Y-m-d H:i:s')
            ];
            $this->intelligence_model->update($id, $update);

            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'success', 'message' => 'Service modifié']);
                return;
            }
            $this->session->set_flashdata('msg', 'Service modifié');
            redirect('admin/intelligence');
        } else {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => validation_errors()]);
                return;
            }
            $data['service'] = $this->intelligence_model->get($id);
            $this->load->view('layout/header', $data);
            $this->load->view('admin/intelligence/form', $data);
            $this->load->view('layout/footer');
        }
    }

    public function delete($id) {
        if (!$this->rbac->hasPrivilege('services_commercial', 'can_delete')) access_denied();
        $this->intelligence_model->delete($id);

        if ($this->input->is_ajax_request()) {
            echo json_encode(['status' => 'success', 'message' => 'Service supprimé']);
            return;
        }
        $this->session->set_flashdata('msg', 'Service supprimé');
        redirect('admin/intelligence');
    }

    // Méthodes _16 (si vous les utilisez, adaptez les redirects)
    public function create_16() {
        if (!$this->rbac->hasPrivilege('services_commercial', 'can_add')) access_denied();
        $this->form_validation->set_rules('name', 'Nom', 'required|trim');
        $this->form_validation->set_rules('unit_price', 'Prix unitaire', 'required|numeric');
        if ($this->form_validation->run() == true) {
            $data = [
                'name'         => $this->input->post('name'),
                'description'  => $this->input->post('description'),
                'unit_price'   => $this->input->post('unit_price'),
                'duration'     => $this->input->post('duration'),
                'created_at'   => date('Y-m-d H:i:s')
            ];
            $this->intelligence_model->add($data);
            $this->session->set_flashdata('msg', 'Service ajouté');
            redirect('admin/intelligence');
        }
        $this->load->view('layout/header');
        $this->load->view('admin/intelligence/form');
        $this->load->view('layout/footer');
    }

    public function edit_16($id) {
        if (!$this->rbac->hasPrivilege('services', 'can_edit')) access_denied();
        $data['service'] = $this->intelligence_model->get($id);
        $this->form_validation->set_rules('name', 'Nom', 'required|trim');
        if ($this->form_validation->run() == true) {
            $update = [
                'name'         => $this->input->post('name'),
                'description'  => $this->input->post('description'),
                'unit_price'   => $this->input->post('unit_price'),
                'duration'     => $this->input->post('duration'),
                'updated_at'   => date('Y-m-d H:i:s')
            ];
            $this->intelligence_model->update($id, $update);
            $this->session->set_flashdata('msg', 'Service modifié');
            redirect('admin/intelligence');
        }
        $this->load->view('layout/header', $data);
        $this->load->view('admin/intelligence/form', $data);
        $this->load->view('layout/footer');
    }

    public function delete_16($id) {
        if (!$this->rbac->hasPrivilege('services', 'can_delete')) access_denied();
        $this->intelligence_model->delete($id);
        $this->session->set_flashdata('msg', 'Service supprimé');
        redirect('admin/intelligence');
    }

    // Récupération des services pour datalist (AJAX)
    public function get_services_json() {
        $services = $this->intelligence_model->get();
        echo json_encode($services);
    }

    public function get_service_details() {
        $name = $this->input->post('name');
        if ($name) {
            $service = $this->db->where('name', $name)->get('intelligence')->row();
            echo json_encode($service);
        } else {
            echo json_encode(null);
        }
    }

    // ========== MÉTHODES POUR L'IA ==========

    private function _get_ai_settings() {
       $entreprise_id = 0;
       $adminSession = $this->session->userdata('admin');

       if (is_array($adminSession) && !empty($adminSession['entreprise_id'])) {
           $entreprise_id = (int) $adminSession['entreprise_id'];
       }

       if ($entreprise_id <= 0) {
           $entreprise_id = (int) ($this->session->userdata('entreprise_id') ?? 0);
       }

       $settings = [
           'ai_enabled' => 1,
           'ai_api_key' => $this->config->item('openai_api_key'),
           'ai_model' => $this->config->item('openai_model'),
           'ai_api_url' => $this->config->item('openai_api_url'),
           'ai_system_prompt' => null,
       ];

       if ($entreprise_id > 0) {
           $row = $this->db->select('ai_enabled, ai_api_key, ai_model, ai_api_url, ai_system_prompt')
               ->from('sch_settings')
               ->where('entreprise_id', $entreprise_id)
               ->order_by('id', 'DESC')
               ->limit(1)
               ->get()
               ->row_array();

           if (!empty($row)) {
               if (isset($row['ai_enabled'])) {
                   $settings['ai_enabled'] = (int) $row['ai_enabled'];
               }
               if (!empty($row['ai_api_key'])) {
                   $settings['ai_api_key'] = $row['ai_api_key'];
               }
               if (!empty($row['ai_model'])) {
                   $settings['ai_model'] = $row['ai_model'];
               }
               if (!empty($row['ai_api_url'])) {
                   $settings['ai_api_url'] = $row['ai_api_url'];
               }
               if (!empty($row['ai_system_prompt'])) {
                   $settings['ai_system_prompt'] = $row['ai_system_prompt'];
               }
           }
       }

       return $settings;
    }

    /**
     * Appel à l'API OpenAI via cURL
     */
    private function _call_openai($messages) {
       $aiSettings = $this->_get_ai_settings();
       if ((int) $aiSettings['ai_enabled'] === 0) {
           throw new Exception('L\'assistant IA est désactivé dans les paramètres de l\'entreprise.');
       }

       $api_key = trim((string) ($aiSettings['ai_api_key'] ?? '')) ?: $this->config->item('openai_api_key');
       $url = trim((string) ($aiSettings['ai_api_url'] ?? '')) ?: $this->config->item('openai_api_url');
       $model = trim((string) ($aiSettings['ai_model'] ?? '')) ?: $this->config->item('openai_model');

       if (empty($api_key) || empty($url) || empty($model)) {
           throw new Exception('Clé API, modèle ou URL OpenAI non configuré.');
       }

       $payload = [
           'model' => $model,
           'messages' => $messages,
           'temperature' => 0.2,
           'max_tokens' => 1000,
       ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Erreur API : ' . $response);
        }

        $data = json_decode($response, true);
        return $data['choices'][0]['message']['content'] ?? null;
    }

    /**
     * Point d'entrée AJAX pour le chat
     */
    private function _build_database_context($question) {
        $tables = $this->db->query('SHOW TABLES')->result_array();
        $keywords = [
            'user', 'staff', 'employee', 'leave', 'conge', 'attendance', 'salary',
            'pay', 'department', 'service', 'role', 'permission', 'invoice', 'client',
            'student', 'course', 'campus', 'document', 'holiday', 'absence'
        ];

        $relevantTables = [];
        foreach ($tables as $row) {
            $table = array_values($row)[0];
            $lowerTable = strtolower($table);
            $matchesKeywords = false;
            foreach ($keywords as $keyword) {
                if (strpos($lowerTable, strtolower($keyword)) !== false) {
                    $matchesKeywords = true;
                    break;
                }
            }

            if (!$matchesKeywords && !empty($question)) {
                $lowerQuestion = strtolower($question);
                $matchesQuestion = false;
                foreach ($keywords as $keyword) {
                    if (strpos($lowerQuestion, strtolower($keyword)) !== false && strpos($lowerTable, strtolower($keyword)) !== false) {
                        $matchesQuestion = true;
                        break;
                    }
                }
                if (!$matchesQuestion) {
                    continue;
                }
            }

            try {
                $columns = $this->db->query('SHOW COLUMNS FROM `' . $table . '`')->result_array();
            } catch (Exception $e) {
                continue;
            }

            $columnNames = array_map(function ($column) {
                return $column['Field'];
            }, $columns);

            if (!empty($columnNames)) {
                $relevantTables[] = [
                    'table' => $table,
                    'columns' => array_slice($columnNames, 0, 12)
                ];
            }

            if (count($relevantTables) >= 8) {
                break;
            }
        }

        if (empty($relevantTables)) {
            return 'Aucune table métier pertinente détectée dans la base courante. La réponse doit rester générale et inviter l\'utilisateur à préciser la donnée souhaitée.';
        }

        $summary = "Contexte base de données disponible :\n";
        foreach ($relevantTables as $data) {
            $summary .= '- ' . $data['table'] . ' (' . implode(', ', $data['columns']) . ')\n';
        }

        return $summary;
    }

    public function chat() {
        $this->output->set_content_type('application/json');

        if (!$this->rbac->hasPrivilege('ia_chat', 'can_use')) {
            return $this->output->set_output(json_encode(['error' => 'Accès non autorisé']));
        }

        $question = trim((string) $this->input->post('question'));
        if (empty($question)) {
            return $this->output->set_output(json_encode(['error' => 'Question vide']));
        }

        $user_id = $this->session->userdata('user_id') ?: 0;
        $start = microtime(true);

        try {
            $databaseContext = $this->_build_database_context($question);
            $recentHistory = $this->intelligence_model->get_history($user_id, 5);
            $historyText = '';
            if (!empty($recentHistory)) {
                $historyText = "Historique récent de l'utilisateur :\n";
                foreach (array_reverse($recentHistory) as $entry) {
                    $historyText .= '- Q: ' . trim(substr($entry['question'], 0, 200)) . '\n';
                    if (!empty($entry['response'])) {
                        $historyText .= '  R: ' . trim(substr($entry['response'], 0, 200)) . '\n';
                    }
                }
            }

            $aiSettings = $this->_get_ai_settings();
            $customPrompt = trim((string) ($aiSettings['ai_system_prompt'] ?? ''));
            $systemPrompt = "Tu es un assistant RH interne et tu dois répondre à partir des données disponibles dans la base de données de l'application. " .
                "Reste précis, clair et professionnel. " .
                "Quand la question exige une donnée exacte, propose la requête SQL correcte que l'utilisateur ou un administrateur pourra exécuter, mais n'exécute jamais la requête toi-même. " .
                "N'invente jamais une information. " .
                "Voici le contexte de la base de données :\n" . $databaseContext;

            if (!empty($customPrompt)) {
                $systemPrompt = $customPrompt . "\n\n" . $systemPrompt;
            }

            if (!empty($historyText)) {
                $systemPrompt .= "\n" . $historyText;
            }

            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $question]
            ];

            $answer = $this->_call_openai($messages);
            $status = 'success';
            $error = null;
        } catch (Exception $e) {
            $answer = 'Je suis connecté à la base de données de l’application et je peux vous guider sur les données RH. La configuration OpenAI est actuellement indisponible, donc je vous recommande de vérifier la clé API ou d’utiliser une requête SQL ciblée pour obtenir la donnée exacte.';
            $status = 'success';
            $error = $e->getMessage();
        }

        $responseTime = microtime(true) - $start;

        $this->intelligence_model->set_table('ia_conversations');
        $logData = [
            'user_id'       => $user_id,
            'session_id'    => session_id(),
            'question'      => $question,
            'response'      => $answer,
            'context'       => $databaseContext ?? null,
            'model_used'    => $aiSettings['ai_model'] ?? $this->config->item('openai_model'),
            'tokens_used'   => 0,
            'response_time' => $responseTime,
            'status'        => $status,
            'error_message' => $error,
            'ip_address'    => $this->input->ip_address(),
            'user_agent'    => $this->input->user_agent()
        ];
        $this->intelligence_model->add($logData);
        $this->intelligence_model->set_table('ia_conversations');

        return $this->output->set_output(json_encode([
            'status' => $status,
            'answer' => $answer,
            'error'  => $error
        ]));
    }
}