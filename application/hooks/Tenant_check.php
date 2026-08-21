<?php
class Tenant_check {

    public function check_tenant() {
        $CI =& get_instance();

        // Vérifier si on est sur une route cliente
        $segment1 = $CI->uri->segment(1);

        if ($segment1 == 'app') {
            $entreprise_uuid = $CI->uri->segment(2);

            // Vérifier et charger la bonne base
            $CI->load->model('multi_tenant_model');
            if (!$CI->multi_tenant_model->set_entreprise_by_uuid($entreprise_uuid)) {
                show_error('Accès non autorisé');
            }
        }
    }
}