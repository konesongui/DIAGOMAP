<?php
class Auth extends CI_Controller {

    public function login($entreprise_uuid) {
        // Vérifier que l'entreprise existe et est active
        $master_db = $this->load->database('master', TRUE);
        $entreprise = $master_db->get_where('entreprises', [
            'uuid' => $entreprise_uuid,
            'statut' => 'actif'
        ])->row();

        if (!$entreprise) {
            show_error('Entreprise non trouvée ou inactive');
        }

        // Configurer la base de données cliente
        $this->multi_tenant_model->set_entreprise($entreprise->id);

        // Rediriger vers le login normal
        redirect('auth/login_form');
    }
}