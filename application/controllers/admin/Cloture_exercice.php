<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cloture_exercice extends Ohada_Admin_Controller
{
    protected $page_title = 'Cloture d exercice';
    protected $page_subtitle = 'Verification des prealables et pilotage de la cloture annuelle.';

    public function index()
    {
        $this->setMenuState('admin/frontoffice/cloture_exercice');
        $summary = $this->ohada_model->get_cloture_summary();
        $exercises = $this->ohada_model->get_exercises();
        $rows = array();
        foreach ($exercises as $exercise) {
            $rows[] = array(
                html_escape($exercise['libelle']),
                html_escape($exercise['date_debut']),
                html_escape($exercise['date_fin']),
                html_escape($exercise['statut']),
                !empty($exercise['is_active']) ? 'Oui' : 'Non',
                '<a class="btn btn-xs btn-warning" href="' . site_url('admin/frontoffice/cloture_exercice/cloturer?id=' . $exercise['id']) . '">Cloturer</a> ' .
                '<a class="btn btn-xs btn-success" href="' . site_url('admin/frontoffice/cloture_exercice/rouvrir?id=' . $exercise['id']) . '">Rouvrir</a>',
            );
        }

        $this->renderOhadaPage(array(
            'cards' => array(
                array('label' => 'Exercice actif', 'value' => $summary['active_exercise']),
                array('label' => 'Ecritures', 'value' => $summary['entries']),
                array('label' => 'Notes annexes', 'value' => $summary['notes']),
            ),
            'actions_html' => '<a href="' . site_url('admin/frontoffice/cloture_exercice/verifier') . '" class="btn btn-default">Verifier</a> <a href="' . site_url('admin/frontoffice/cloture_exercice/export_pdf') . '" class="btn btn-default">Export PDF</a>',
            'table_headers' => array('Exercice', 'Date debut', 'Date fin', 'Statut', 'Actif', 'Actions'),
            'table_rows' => $rows,
            'empty_message' => 'Aucun exercice comptable configure.',
            'info_message' => 'Les operations de cloture changent le statut de l exercice pour preparer le report a nouveau.',
        ));
    }

    public function verifier()
    {
        header('Content-Type: application/json');
        echo json_encode($this->ohada_model->get_cloture_summary());
    }

    public function cloturer()
    {
        $id = (int) $this->input->get('id');
        $this->ohada_model->update_exercise_status($id, 'cloture');
        $this->redirectBackToModule('admin/frontoffice/cloture_exercice', 'Exercice cloture avec succes.');
    }

    public function rouvrir()
    {
        $id = (int) $this->input->get('id');
        $this->ohada_model->update_exercise_status($id, 'ouvert');
        $this->redirectBackToModule('admin/frontoffice/cloture_exercice', 'Exercice reouvert avec succes.');
    }

    public function export_pdf()
    {
        $exercises = $this->ohada_model->get_exercises();
        $rows = array();
        foreach ($exercises as $exercise) {
            $rows[] = array($exercise['libelle'], $exercise['date_debut'], $exercise['date_fin'], $exercise['statut'], !empty($exercise['is_active']) ? 'Oui' : 'Non');
        }
        $html = $this->buildSimplePdfHtml('Cloture d exercice', array('Exercice', 'Date debut', 'Date fin', 'Statut', 'Actif'), $rows, 'Etat des exercices comptables');
        $this->streamPdfLike('cloture_exercice_' . date('Ymd') . '.pdf', 'Cloture d exercice', $html);
    }
}

