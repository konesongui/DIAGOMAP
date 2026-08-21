<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Exercices_comptables extends Ohada_Admin_Controller
{
    protected $page_title = 'Exercices comptables';
    protected $page_subtitle = 'Gestion des periodes comptables et exercice actif.';

    public function index()
    {
        $this->setMenuState('admin/frontoffice/exercices_comptables');
        $rows_data = $this->ohada_model->get_exercises();
        $rows = array();
        foreach ($rows_data as $exercise) {
            $rows[] = array(
                html_escape($exercise['libelle']),
                html_escape($exercise['date_debut']),
                html_escape($exercise['date_fin']),
                html_escape($exercise['statut']),
                !empty($exercise['is_active']) ? 'Oui' : 'Non',
                '<a class="btn btn-xs btn-primary" href="' . site_url('admin/frontoffice/exercices_comptables/edit/' . $exercise['id']) . '"><i class="fa fa-pencil"></i></a> ' .
                '<a class="btn btn-xs btn-success" href="' . site_url('admin/frontoffice/exercices_comptables/activer/' . $exercise['id']) . '"><i class="fa fa-check"></i></a> ' .
                '<a class="btn btn-xs btn-danger" href="' . site_url('admin/frontoffice/exercices_comptables/delete/' . $exercise['id']) . '" onclick="return confirm(\'Supprimer cet exercice ?\')"><i class="fa fa-trash"></i></a>',
            );
        }

        $this->renderOhadaPage(array(
            'cards' => array(array('label' => 'Exercices', 'value' => count($rows_data))),
            'actions_html' => '<a href="' . site_url('admin/frontoffice/exercices_comptables/add') . '" class="btn btn-primary"><i class="fa fa-plus"></i> Nouvel exercice</a>',
            'table_headers' => array('Libelle', 'Date debut', 'Date fin', 'Statut', 'Actif', 'Actions'),
            'table_rows' => $rows,
            'empty_message' => 'Aucun exercice comptable disponible.',
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
        $exercise = $id ? $this->ohada_model->get_exercise($id) : array();
        if ($id && empty($exercise)) {
            show_404();
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('libelle', 'Libelle', 'required|trim');
            $this->form_validation->set_rules('date_debut', 'Date debut', 'required');
            $this->form_validation->set_rules('date_fin', 'Date fin', 'required');
            if ($this->form_validation->run()) {
                $this->ohada_model->save_exercise(array(
                    'libelle' => $this->input->post('libelle', true),
                    'date_debut' => $this->input->post('date_debut', true),
                    'date_fin' => $this->input->post('date_fin', true),
                    'statut' => $this->input->post('statut', true),
                    'is_active' => $this->input->post('is_active') ? 1 : 0,
                ), $id);
                $this->redirectBackToModule('admin/frontoffice/exercices_comptables', 'Exercice enregistre avec succes.');
                return;
            }
        }

        $fields = validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : '';
        $checked = set_value('is_active', isset($exercise['is_active']) ? $exercise['is_active'] : 0) ? ' checked' : '';
        $fields .= '<div class="row">';
        $fields .= '<div class="col-md-6"><div class="form-group"><label>Libelle</label><input type="text" name="libelle" class="form-control" value="' . html_escape(set_value('libelle', isset($exercise['libelle']) ? $exercise['libelle'] : '')) . '"></div></div>';
        $fields .= '<div class="col-md-3"><div class="form-group"><label>Date debut</label><input type="date" name="date_debut" class="form-control" value="' . html_escape(set_value('date_debut', isset($exercise['date_debut']) ? $exercise['date_debut'] : '')) . '"></div></div>';
        $fields .= '<div class="col-md-3"><div class="form-group"><label>Date fin</label><input type="date" name="date_fin" class="form-control" value="' . html_escape(set_value('date_fin', isset($exercise['date_fin']) ? $exercise['date_fin'] : '')) . '"></div></div>';
        $fields .= '<div class="col-md-6"><div class="form-group"><label>Statut</label><select name="statut" class="form-control">';
        foreach (array('ouvert' => 'Ouvert', 'cloture' => 'Cloture') as $key => $label) {
            $selected = set_value('statut', isset($exercise['statut']) ? $exercise['statut'] : 'ouvert') === $key ? ' selected' : '';
            $fields .= '<option value="' . html_escape($key) . '"' . $selected . '>' . html_escape($label) . '</option>';
        }
        $fields .= '</select></div></div>';
        $fields .= '<div class="col-md-6"><div class="checkbox" style="margin-top:34px;"><label><input type="checkbox" name="is_active" value="1"' . $checked . '> Definir comme exercice actif</label></div></div>';
        $fields .= '</div>';

        $this->renderOhadaForm(array(
            'title' => $id ? 'Modifier un exercice' : 'Nouvel exercice comptable',
            'subtitle' => 'Controlez l exercice actif utilise pour les ecritures et clotures.',
            'form_action' => $id ? site_url('admin/frontoffice/exercices_comptables/edit/' . $id) : site_url('admin/frontoffice/exercices_comptables/add'),
            'cancel_url' => site_url('admin/frontoffice/exercices_comptables'),
            'fields_html' => $fields,
        ));
    }

    public function delete($id)
    {
        $this->ohada_model->soft_delete('ohada_exercices', (int) $id);
        $this->redirectBackToModule('admin/frontoffice/exercices_comptables', 'Exercice supprime avec succes.');
    }

    public function activer($id)
    {
        $this->ohada_model->activate_exercise((int) $id);
        $this->redirectBackToModule('admin/frontoffice/exercices_comptables', 'Exercice actif mis a jour.');
    }
}

