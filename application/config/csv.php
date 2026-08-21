<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| CSV Configuration
|--------------------------------------------------------------------------
|
| Configuration pour l'importation de fichiers CSV
|
*/

$config['csv_delimiter'] = ',';
$config['csv_enclosure'] = '"';
$config['csv_escape'] = '\\';
$config['csv_max_size'] = 2048; // 2MB
$config['csv_allowed_types'] = 'csv';
$config['csv_upload_path'] = './uploads/csv/';