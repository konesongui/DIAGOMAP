<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Balance_generale_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Ohada_model', 'ohada_model');
    }

    public function get_report($date_debut, $date_fin, $classe = null, $include_zero = false)
    {
        return $this->ohada_model->get_balance_generale_report($date_debut, $date_fin, (string) $classe, $include_zero);
    }

    public function get_balance($date_debut, $date_fin, $classe = null)
    {
        $report = $this->get_report($date_debut, $date_fin, $classe);
        return $report['rows'];
    }

    public function get_total_debit($date_debut, $date_fin, $classe = null)
    {
        $report = $this->get_report($date_debut, $date_fin, $classe);
        return $report['totals']['mouvement_debit'];
    }

    public function get_total_credit($date_debut, $date_fin, $classe = null)
    {
        $report = $this->get_report($date_debut, $date_fin, $classe);
        return $report['totals']['mouvement_credit'];
    }

    public function get_total_solde_debiteur($date_debut, $date_fin, $classe = null)
    {
        $report = $this->get_report($date_debut, $date_fin, $classe);
        return $report['totals']['cloture_debit'];
    }

    public function get_total_solde_crediteur($date_debut, $date_fin, $classe = null)
    {
        $report = $this->get_report($date_debut, $date_fin, $classe);
        return $report['totals']['cloture_credit'];
    }

    public function get_total_ouverture_debit($date_debut, $date_fin, $classe = null)
    {
        $report = $this->get_report($date_debut, $date_fin, $classe);
        return $report['totals']['ouverture_debit'];
    }

    public function get_total_ouverture_credit($date_debut, $date_fin, $classe = null)
    {
        $report = $this->get_report($date_debut, $date_fin, $classe);
        return $report['totals']['ouverture_credit'];
    }

    public function get_classes()
    {
        return array_keys($this->ohada_model->get_default_classes());
    }

    public function count_mouvements($date_debut, $date_fin)
    {
        return $this->ohada_model->count_mouvements($date_debut, $date_fin);
    }

    public function count_ecritures($date_debut, $date_fin)
    {
        return $this->ohada_model->count_ecritures($date_debut, $date_fin);
    }
}
