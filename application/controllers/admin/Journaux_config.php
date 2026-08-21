<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Journaux_config extends Ohada_Admin_Controller
{
    protected $page_title = 'Journaux par defaut';
    protected $page_subtitle = 'Affectez les journaux OHADA aux principaux cycles de gestion.';

    public function index()
    {
        $this->setMenuState('admin/frontoffice/journaux_config');
        $journals = $this->ohada_model->get_journals();
        $configs = $this->ohada_model->get_journal_configs();
        $config_map = array();
        foreach ($configs as $config) {
            $config_map[$config['module_code']] = $config['journal_code'];
        }

        $modules = array(
            'ACHATS' => 'Cycle achats',
            'VENTES' => 'Cycle ventes',
            'BANQUE' => 'Cycle banque',
            'CAISSE' => 'Cycle caisse',
            'PAIE' => 'Cycle paie',
            'OD' => 'Operations diverses',
        );

        $fields = '<div class="row">';
        foreach ($modules as $code => $label) {
            $fields .= '<div class="col-md-6"><div class="form-group"><label>' . html_escape($label) . '</label><select name="configs[' . html_escape($code) . ']" class="form-control"><option value="">Aucun journal</option>';
            foreach ($journals as $journal) {
                $selected = isset($config_map[$code]) && $config_map[$code] === $journal['code'] ? ' selected' : '';
                $fields .= '<option value="' . html_escape($journal['code']) . '"' . $selected . '>' . html_escape($journal['code'] . ' - ' . $journal['libelle']) . '</option>';
            }
            $fields .= '</select></div></div>';
        }
        $fields .= '</div>';

        $this->renderOhadaForm(array(
            'title' => $this->page_title,
            'subtitle' => $this->page_subtitle,
            'form_action' => site_url('admin/frontoffice/journaux_config/save'),
            'cancel_url' => site_url('admin/admin/comptabilite'),
            'fields_html' => $fields,
            'submit_label' => 'Sauvegarder les affectations',
        ));
    }

    public function save()
    {
        $configs = $this->input->post('configs');
        $rows = array();
        if (is_array($configs)) {
            foreach ($configs as $module_code => $journal_code) {
                if ($journal_code === '') {
                    continue;
                }
                $rows[] = array(
                    'module_code' => $module_code,
                    'journal_code' => $journal_code,
                    'libelle' => $module_code,
                );
            }
        }
        $this->ohada_model->save_journal_config($rows);
        $this->redirectBackToModule('admin/frontoffice/journaux_config', 'Configuration des journaux sauvegardee avec succes.');
    }
}
