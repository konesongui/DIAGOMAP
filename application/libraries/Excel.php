<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Excel {
    private $data;
    private $headers;
    private $filename;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->helper('download');
    }

    public function set_data($data) {
        $this->data = $data;
        return $this;
    }

    public function set_headers($headers) {
        $this->headers = $headers;
        return $this;
    }

    public function set_filename($filename) {
        $this->filename = $filename;
        return $this;
    }

    public function generate_csv() {
        $filename = $this->filename ?: 'export_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        if (!empty($this->headers)) {
            fputcsv($output, $this->headers);
        }

        foreach ($this->data as $row) {
            fputcsv($output, (array)$row);
        }

        fclose($output);
        exit;
    }


    // ========================================== //
// EXPORT XML (COMPATIBLE EXCEL)              //
// ========================================== //
    public function export_excel() {
        // Récupérer les filtres
        $purpose = $this->input->get('purpose');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        $status = $this->input->get('status');

        // Récupérer les données
        $data = $this->visitors_model->get_filtered_visitors($purpose, $date_from, $date_to, $status);

        $filename = 'visiteurs_' . date('Y-m-d') . '.xls';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo '<table border="1">';
        echo '<tr>
            <th style="background:#273772;color:white;">Motif</th>
            <th style="background:#273772;color:white;">Nom</th>
            <th style="background:#273772;color:white;">Téléphone</th>
            <th style="background:#273772;color:white;">Date</th>
            <th style="background:#273772;color:white;">Arrivée</th>
            <th style="background:#273772;color:white;">Sortie</th>
            <th style="background:#273772;color:white;">Observation</th>
          </tr>';

        foreach ($data as $visitor) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($visitor['purpose']) . '</td>';
            echo '<td>' . htmlspecialchars($visitor['name']) . '</td>';
            echo '<td>' . htmlspecialchars($visitor['contact']) . '</td>';
            echo '<td>' . date('d/m/Y', strtotime($visitor['date'])) . '</td>';
            echo '<td>' . htmlspecialchars($visitor['in_time']) . '</td>';
            echo '<td>' . (htmlspecialchars($visitor['out_time']) ?: 'En cours') . '</td>';
            echo '<td>' . htmlspecialchars($visitor['note']) . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        exit;
    }
}