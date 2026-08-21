<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Analytique extends Ohada_Admin_Controller
{
    protected $page_title = 'Comptabilite analytique';
    protected $page_subtitle = 'Centres de cout, projets et repartitions analytiques.';

    public function index()
    {
        $this->setMenuState('admin/frontoffice/analytique');
        $analytics = $this->ohada_model->get_analytics();
        $rows = array();
        foreach ($analytics as $analytic) {
            $rows[] = array(
                html_escape($analytic['code']),
                html_escape($analytic['libelle']),
                html_escape($analytic['type']),
                html_escape($analytic['description']),
                '<a class="btn btn-xs btn-primary" href="' . site_url('admin/frontoffice/analytique/edit/' . $analytic['id']) . '"><i class="fa fa-pencil"></i></a> ' .
                '<a class="btn btn-xs btn-danger" href="' . site_url('admin/frontoffice/analytique/delete/' . $analytic['id']) . '" onclick="return confirm(\'Supprimer cet axe ?\')"><i class="fa fa-trash"></i></a>',
            );
        }

        $this->renderOhadaPage(array(
            'cards' => array(array('label' => 'Axes analytiques', 'value' => count($analytics))),
            'actions_html' => '<a href="' . site_url('admin/frontoffice/analytique/add') . '" class="btn btn-primary"><i class="fa fa-plus"></i> Nouvel axe</a> <a href="' . site_url('admin/frontoffice/analytique/repartition') . '" class="btn btn-default">Repartition</a> <a href="' . site_url('admin/frontoffice/analytique/export_pdf') . '" class="btn btn-default">Export PDF</a>',
            'table_headers' => array('Code', 'Libelle', 'Type', 'Description', 'Actions'),
            'table_rows' => $rows,
            'empty_message' => 'Aucun axe analytique disponible.',
        ));
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
        $analytic = $id ? $this->ohada_model->get_analytic($id) : array();
        if ($id && empty($analytic)) {
            show_404();
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('code', 'Code', 'required|trim');
            $this->form_validation->set_rules('libelle', 'Libelle', 'required|trim');
            $this->form_validation->set_rules('type', 'Type', 'required|trim');
            if ($this->form_validation->run()) {
                $this->ohada_model->save_analytic(array(
                    'code' => $this->input->post('code', true),
                    'libelle' => $this->input->post('libelle', true),
                    'type' => $this->input->post('type', true),
                    'description' => $this->input->post('description', true),
                ), $id);
                $this->redirectBackToModule('admin/frontoffice/analytique', 'Axe analytique enregistre avec succes.');
                return;
            }
        }

        $fields = validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : '';
        $fields .= '<div class="row">';
        $fields .= '<div class="col-md-4"><div class="form-group"><label>Code</label><input type="text" name="code" class="form-control" value="' . html_escape(set_value('code', isset($analytic['code']) ? $analytic['code'] : '')) . '"></div></div>';
        $fields .= '<div class="col-md-8"><div class="form-group"><label>Libelle</label><input type="text" name="libelle" class="form-control" value="' . html_escape(set_value('libelle', isset($analytic['libelle']) ? $analytic['libelle'] : '')) . '"></div></div>';
        $fields .= '<div class="col-md-4"><div class="form-group"><label>Type</label><select name="type" class="form-control">';
        foreach (array('PROJET' => 'Projet', 'CENTRE_COUT' => 'Centre de cout', 'ENTITE' => 'Entite') as $key => $label) {
            $selected = set_value('type', isset($analytic['type']) ? $analytic['type'] : '') === $key ? ' selected' : '';
            $fields .= '<option value="' . html_escape($key) . '"' . $selected . '>' . html_escape($label) . '</option>';
        }
        $fields .= '</select></div></div>';
        $fields .= '<div class="col-md-8"><div class="form-group"><label>Description</label><textarea name="description" rows="3" class="form-control">' . html_escape(set_value('description', isset($analytic['description']) ? $analytic['description'] : '')) . '</textarea></div></div>';
        $fields .= '</div>';

        $this->renderOhadaForm(array(
            'title' => $id ? 'Modifier un axe analytique' : 'Nouvel axe analytique',
            'subtitle' => 'Structurez vos centres de cout et analyses par projet.',
            'form_action' => $id ? site_url('admin/frontoffice/analytique/edit/' . $id) : site_url('admin/frontoffice/analytique/add'),
            'cancel_url' => site_url('admin/frontoffice/analytique'),
            'fields_html' => $fields,
        ));
    }

    public function delete($id)
    {
        $this->ohada_model->soft_delete('ohada_analytique', (int) $id);
        $this->redirectBackToModule('admin/frontoffice/analytique', 'Axe analytique supprime avec succes.');
    }

    public function repartition()
    {
        $analytics = $this->ohada_model->get_analytics();
        $rows = array();
        foreach ($analytics as $analytic) {
            $rows[] = array($analytic['code'], $analytic['libelle'], $analytic['type'], $analytic['description']);
        }
        $html = $this->buildSimplePdfHtml('Repartition analytique', array('Code', 'Libelle', 'Type', 'Description'), $rows, 'Synthese des axes analytiques');
        $this->streamPdfLike('repartition_analytique_' . date('Ymd') . '.pdf', 'Repartition analytique', $html);
    }

    public function export_pdf()
    {
        $this->repartition();
    }
}

