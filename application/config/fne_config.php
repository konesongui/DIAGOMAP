<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Configuration API FNE
|--------------------------------------------------------------------------
*/

$config['fne_api_key'] = 'toCgyP5vdqXavkY16dg5qn7eae3N8bjZ'; // À remplacer par la clé réelle
$config['fne_test_url'] = 'http://54.247.95.108/ws';
$config['fne_production_url'] = ''; // À remplacer après validation DGI
$config['fne_point_of_sale'] = 'DIAGOMA';
$config['fne_establishment'] = 'INTENDANT';

// Configuration des templates FNE
$config['fne_templates'] = [
    'B2B' => 'Business to Business',
    'B2C' => 'Business to Consumer',
    'B2G' => 'Business to Government',
    'B2F' => 'Business to Foreign'
];

// Mapping des méthodes de paiement
$config['fne_payment_methods'] = [
    'cash' => 'cash',
    'card' => 'card',
    'check' => 'check',
    'bank_transfer' => 'transfer',
    'mobile_money' => 'mobile-money'
];
