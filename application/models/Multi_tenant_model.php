<?php
class Multi_tenant_model extends CI_Model {

    private $current_entreprise;

    public function set_entreprise($entreprise_id) {
        // Récupérer infos entreprise depuis base MASTER
        $master_db = $this->load->database('master', TRUE);
        $entreprise = $master_db->get_where('entreprises', ['id' => $entreprise_id])->row();

        if ($entreprise) {
            $this->current_entreprise = $entreprise;

            // Configurer connexion base client
            $config = $this->config->item('database');
            $config['client']['database'] = $entreprise->database_name;

            return true;
        }
        return false;
    }

    public function get_client_db() {
        $config = $this->config->item('database');
        return $this->load->database('client', TRUE);
    }

    public function create_entreprise_database($entreprise_data) {
        $master_db = $this->load->database('master', TRUE);

        // Créer nom base unique
        $db_name = 'erp_' . $entreprise_data['uuid'];

        // Créer la base de données
        $master_db->query("CREATE DATABASE IF NOT EXISTS $db_name");

        // Exécuter le script SQL de structure (votre ERP)
        $this->create_erp_structure($db_name);

        // Ajouter entreprise dans base MASTER
        $entreprise_data['database_name'] = $db_name;
        $master_db->insert('entreprises', $entreprise_data);

        return $db_name;
    }

    private function create_erp_structure($db_name) {
        // Exécuter votre script SQL de création des tables
        $client_db = $this->load->database('client', TRUE);
        $client_db->database = $db_name;

        $sql_script = file_get_contents(APPPATH . 'database/erp_structure.sql');
        $client_db->query($sql_script);
    }
}