<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Notes_annexes extends Ohada_Admin_Controller
{
    protected $page_title = 'Notes annexes';
    protected $page_subtitle = 'Documentation des informations complementaires des etats financiers.';

    public function index()
    {
        $this->setMenuState('admin/frontoffice/notes_annexes');
        $notes = $this->ohada_model->get_notes();
        $rows = array();
        foreach ($notes as $note) {
            $preview = strip_tags($note['contenu']);
            if (strlen($preview) > 120) {
                $preview = substr($preview, 0, 117) . '...';
            }
            $rows[] = array(
                (int) $note['ordre_affichage'],
                html_escape($note['titre']),
                html_escape($preview),
                html_escape(isset($note['updated_at']) ? $note['updated_at'] : ''),
                '<a class="btn btn-xs btn-primary" href="' . site_url('admin/frontoffice/notes_annexes/edit/' . $note['id']) . '"><i class="fa fa-pencil"></i></a> ' .
                '<a class="btn btn-xs btn-danger" href="' . site_url('admin/frontoffice/notes_annexes/delete/' . $note['id']) . '" onclick="return confirm(\'Supprimer cette note ?\')"><i class="fa fa-trash"></i></a>',
            );
        }

        $this->renderOhadaPage(array(
            'cards' => array(
                array('label' => 'Notes', 'value' => count($notes)),
                array('label' => 'Entreprise', 'value' => $this->ohada_model->get_entreprise_id()),
            ),
            'actions_html' => '<a href="' . site_url('admin/frontoffice/notes_annexes/add') . '" class="btn btn-primary"><i class="fa fa-plus"></i> Nouvelle note</a> <a href="' . site_url('admin/frontoffice/notes_annexes/export_pdf') . '" class="btn btn-default">Export PDF</a>',
            'table_headers' => array('Ordre', 'Titre', 'Contenu', 'Maj', 'Actions'),
            'table_rows' => $rows,
            'empty_message' => 'Aucune note annexe disponible.',
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
        $note = $id ? $this->ohada_model->get_note($id) : array();
        if ($id && empty($note)) {
            show_404();
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('titre', 'Titre', 'required|trim');
            $this->form_validation->set_rules('contenu', 'Contenu', 'required|trim');
            $this->form_validation->set_rules('ordre_affichage', 'Ordre', 'required|integer');
            if ($this->form_validation->run()) {
                $this->ohada_model->save_note(array(
                    'titre' => $this->input->post('titre', true),
                    'contenu' => $this->input->post('contenu', false),
                    'ordre_affichage' => $this->input->post('ordre_affichage', true),
                ), $id);
                $this->redirectBackToModule('admin/frontoffice/notes_annexes', 'Note annexe enregistree avec succes.');
                return;
            }
        }

        $fields = validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : '';
        $fields .= '<div class="form-group"><label>Titre</label><input type="text" name="titre" class="form-control" value="' . html_escape(set_value('titre', isset($note['titre']) ? $note['titre'] : '')) . '"></div>';
        $fields .= '<div class="form-group"><label>Contenu</label><textarea name="contenu" rows="8" class="form-control">' . html_escape(set_value('contenu', isset($note['contenu']) ? $note['contenu'] : '')) . '</textarea></div>';
        $fields .= '<div class="form-group"><label>Ordre d affichage</label><input type="number" name="ordre_affichage" class="form-control" value="' . html_escape(set_value('ordre_affichage', isset($note['ordre_affichage']) ? $note['ordre_affichage'] : 1)) . '"></div>';

        $this->renderOhadaForm(array(
            'title' => $id ? 'Modifier la note annexe' : 'Nouvelle note annexe',
            'subtitle' => 'Ajoutez des informations qualitatives aux etats financiers.',
            'form_action' => $id ? site_url('admin/frontoffice/notes_annexes/edit/' . $id) : site_url('admin/frontoffice/notes_annexes/add'),
            'cancel_url' => site_url('admin/frontoffice/notes_annexes'),
            'fields_html' => $fields,
        ));
    }

    public function delete($id)
    {
        $this->ohada_model->soft_delete('ohada_notes_annexes', (int) $id);
        $this->redirectBackToModule('admin/frontoffice/notes_annexes', 'Note supprimee avec succes.');
    }

    public function export_pdf()
    {
        $notes = $this->ohada_model->get_notes();
        $rows = array();
        foreach ($notes as $note) {
            $rows[] = array($note['ordre_affichage'], $note['titre'], strip_tags($note['contenu']));
        }
        $html = $this->buildSimplePdfHtml('Notes annexes', array('Ordre', 'Titre', 'Contenu'), $rows, 'Etat des notes annexes');
        $this->streamPdfLike('notes_annexes_' . date('Ymd') . '.pdf', 'Notes annexes', $html);
    }
}
