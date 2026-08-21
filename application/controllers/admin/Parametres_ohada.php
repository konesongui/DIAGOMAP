<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Parametres_ohada extends Ohada_Admin_Controller
{
    protected $page_title = 'Parametres OHADA';
    protected $page_subtitle = 'Configuration du referentiel comptable et des options de gestion.';

    public function index()
    {
        $this->setMenuState('admin/frontoffice/parametres_ohada');
        $settings = $this->ohada_model->get_settings();
        $fields = '<div class="row">';
        $fields .= '<div class="col-md-4"><div class="form-group"><label>Referentiel</label><select name="referentiel" class="form-control"><option value="SYSCOHADA"' . ((isset($settings['referentiel']) ? $settings['referentiel'] : 'SYSCOHADA') === 'SYSCOHADA' ? ' selected' : '') . '>SYSCOHADA</option><option value="SYCEBNL"' . ((isset($settings['referentiel']) ? $settings['referentiel'] : '') === 'SYCEBNL' ? ' selected' : '') . '>SYCEBNL</option></select></div></div>';
        $fields .= '<div class="col-md-4"><div class="form-group"><label>Devise</label><input type="text" name="devise" class="form-control" value="' . html_escape(isset($settings['devise']) ? $settings['devise'] : 'XAF') . '"></div></div>';
        $fields .= '<div class="col-md-4"><div class="form-group"><label>Pays</label><input type="text" name="pays" class="form-control" value="' . html_escape(isset($settings['pays']) ? $settings['pays'] : 'Cameroun') . '"></div></div>';
        $fields .= '<div class="col-md-4"><div class="form-group"><label>Longueur comptes</label><input type="number" name="longueur_compte" class="form-control" value="' . html_escape(isset($settings['longueur_compte']) ? $settings['longueur_compte'] : 8) . '"></div></div>';
        $fields .= '<div class="col-md-4"><div class="checkbox" style="margin-top:34px;"><label><input type="checkbox" name="utiliser_analytique" value="1"' . (!empty($settings['utiliser_analytique']) ? ' checked' : '') . '> Activer analytique</label></div></div>';
        $fields .= '<div class="col-md-4"><div class="checkbox" style="margin-top:34px;"><label><input type="checkbox" name="utiliser_tiers" value="1"' . (!empty($settings['utiliser_tiers']) ? ' checked' : '') . '> Activer tiers</label></div></div>';
        $fields .= '</div>';

        $this->renderOhadaForm(array(
            'title' => $this->page_title,
            'subtitle' => $this->page_subtitle,
            'form_action' => site_url('admin/frontoffice/parametres_ohada/save'),
            'cancel_url' => site_url('admin/admin/comptabilite'),
            'fields_html' => $fields,
            'submit_label' => 'Sauvegarder',
            'info_message' => '<a href="' . site_url('admin/frontoffice/parametres_ohada/reset') . '" class="btn btn-danger btn-sm">Reinitialiser les parametres</a>',
        ));
    }

    public function save()
    {
        $this->ohada_model->save_settings(array(
            'referentiel' => $this->input->post('referentiel', true),
            'devise' => $this->input->post('devise', true),
            'pays' => $this->input->post('pays', true),
            'longueur_compte' => $this->input->post('longueur_compte', true),
            'utiliser_analytique' => $this->input->post('utiliser_analytique'),
            'utiliser_tiers' => $this->input->post('utiliser_tiers'),
        ));
        $this->redirectBackToModule('admin/frontoffice/parametres_ohada', 'Parametres OHADA sauvegardes avec succes.');
    }

    public function reset()
    {
        $this->ohada_model->reset_settings();
        $this->redirectBackToModule('admin/frontoffice/parametres_ohada', 'Parametres reinitialises avec succes.');
    }
}

