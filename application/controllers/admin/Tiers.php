<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tiers extends Ohada_Admin_Controller
{
    protected $page_title = 'Gestion des tiers';
    protected $page_subtitle = 'Clients, fournisseurs et autres tiers rattaches a l entreprise.';

    public function index()
    {
        $this->setMenuState('admin/frontoffice/tiers');
        $type = trim((string) $this->input->get('type'));
        $tiers = $this->ohada_model->get_tiers($type);
        $rows = array();
        foreach ($tiers as $tier) {
            $rows[] = array(
                html_escape($tier['code']),
                html_escape($tier['libelle']),
                html_escape($tier['type']),
                html_escape($tier['compte_collectif']),
                html_escape($tier['telephone']),
                html_escape($tier['email']),
                '<a class="btn btn-xs btn-primary" href="' . site_url('admin/frontoffice/tiers/edit/' . $tier['id']) . '"><i class="fa fa-pencil"></i></a> ' .
                '<a class="btn btn-xs btn-danger" href="' . site_url('admin/frontoffice/tiers/delete/' . $tier['id']) . '" onclick="return confirm(\'Supprimer ce tiers ?\')"><i class="fa fa-trash"></i></a>',
            );
        }
        $this->renderOhadaPage(array(
            'cards' => array(array('label' => 'Tiers', 'value' => count($tiers))),
            'actions_html' => '<a href="' . site_url('admin/frontoffice/tiers/add') . '" class="btn btn-primary"><i class="fa fa-plus"></i> Nouveau tiers</a> <a href="' . site_url('admin/frontoffice/tiers/export_csv') . '" class="btn btn-default">Export CSV</a>',
            'table_headers' => array('Code', 'Libelle', 'Type', 'Compte collectif', 'Telephone', 'Email', 'Actions'),
            'table_rows' => $rows,
            'empty_message' => 'Aucun tiers disponible.',
        ));
    }

    public function type($type)
    {
        $_GET['type'] = $type;
        $this->index();
    }

    public function add()
    {
        $this->save();
    }

    public function edit($id)
    {
        $this->save((int) $id);
    }

    protected function save($id = null)
    {
        $tier = $id ? $this->ohada_model->get_tier($id) : array();
        if ($id && empty($tier)) {
            show_404();
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('code', 'Code', 'required|trim');
            $this->form_validation->set_rules('libelle', 'Libelle', 'required|trim');
            $this->form_validation->set_rules('type', 'Type', 'required|trim');
            if ($this->form_validation->run()) {
                $this->ohada_model->save_tier(array(
                    'code' => $this->input->post('code', true),
                    'libelle' => $this->input->post('libelle', true),
                    'type' => $this->input->post('type', true),
                    'compte_collectif' => $this->input->post('compte_collectif', true),
                    'telephone' => $this->input->post('telephone', true),
                    'email' => $this->input->post('email', true),
                    'adresse' => $this->input->post('adresse', true),
                ), $id);
                $this->redirectBackToModule('admin/frontoffice/tiers', 'Tiers enregistre avec succes.');
                return;
            }
        }

        $fields = validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : '';
        $fields .= '<div class="row">';
        $fields .= '<div class="col-md-4"><div class="form-group"><label>Code</label><input type="text" name="code" class="form-control" value="' . html_escape(set_value('code', isset($tier['code']) ? $tier['code'] : '')) . '"></div></div>';
        $fields .= '<div class="col-md-8"><div class="form-group"><label>Libelle</label><input type="text" name="libelle" class="form-control" value="' . html_escape(set_value('libelle', isset($tier['libelle']) ? $tier['libelle'] : '')) . '"></div></div>';
        $fields .= '<div class="col-md-4"><div class="form-group"><label>Type</label><select name="type" class="form-control">';
        foreach (array('CLIENT' => 'Client', 'FOURNISSEUR' => 'Fournisseur', 'AUTRE' => 'Autre') as $key => $label) {
            $selected = set_value('type', isset($tier['type']) ? $tier['type'] : '') === $key ? ' selected' : '';
            $fields .= '<option value="' . html_escape($key) . '"' . $selected . '>' . html_escape($label) . '</option>';
        }
        $fields .= '</select></div></div>';
        $fields .= '<div class="col-md-4"><div class="form-group"><label>Compte collectif</label><input type="text" name="compte_collectif" class="form-control" value="' . html_escape(set_value('compte_collectif', isset($tier['compte_collectif']) ? $tier['compte_collectif'] : '')) . '"></div></div>';
        $fields .= '<div class="col-md-4"><div class="form-group"><label>Telephone</label><input type="text" name="telephone" class="form-control" value="' . html_escape(set_value('telephone', isset($tier['telephone']) ? $tier['telephone'] : '')) . '"></div></div>';
        $fields .= '<div class="col-md-6"><div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="' . html_escape(set_value('email', isset($tier['email']) ? $tier['email'] : '')) . '"></div></div>';
        $fields .= '<div class="col-md-6"><div class="form-group"><label>Adresse</label><input type="text" name="adresse" class="form-control" value="' . html_escape(set_value('adresse', isset($tier['adresse']) ? $tier['adresse'] : '')) . '"></div></div>';
        $fields .= '</div>';

        $this->renderOhadaForm(array(
            'title' => $id ? 'Modifier le tiers' : 'Nouveau tiers',
            'subtitle' => 'Rattachez les partenaires comptables aux comptes collectifs OHADA.',
            'form_action' => $id ? site_url('admin/frontoffice/tiers/edit/' . $id) : site_url('admin/frontoffice/tiers/add'),
            'cancel_url' => site_url('admin/frontoffice/tiers'),
            'fields_html' => $fields,
        ));
    }

    public function delete($id)
    {
        $this->ohada_model->soft_delete('ohada_tiers', (int) $id);
        $this->redirectBackToModule('admin/frontoffice/tiers', 'Tiers supprime avec succes.');
    }

    public function export_csv()
    {
        $tiers = $this->ohada_model->get_tiers();
        $rows = array();
        foreach ($tiers as $tier) {
            $rows[] = array($tier['code'], $tier['libelle'], $tier['type'], $tier['compte_collectif'], $tier['telephone'], $tier['email'], $tier['adresse']);
        }
        $this->streamCsv('tiers_' . date('Ymd') . '.csv', array('Code', 'Libelle', 'Type', 'Compte collectif', 'Telephone', 'Email', 'Adresse'), $rows);
    }
}

