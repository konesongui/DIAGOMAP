<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['fne_api'] = array(
    // Environnements
    'test_url' => 'http://54.247.95.108/ws',
    'prod_url' => '', // À définir après validation DGI
    'api_key' => 'toCgyP5vdqXavkY16dg5qn7eae3N8bjZ',
    'point_of_sale' => 'DIAGOMA',
    'establishment' => 'INTENDANT',

    // Endpoints
    'endpoints' => array(
        'sign_invoice' => '/external/invoices/sign',
        'refund_invoice' => '/external/invoices/{id}/refund'
    ),

    // Types de documents
    'invoice_types' => array(
        'sale' => 'sale',      // Facture de vente
        'purchase' => 'purchase', // Bordereau d'achat
        'refund' => 'refund'   // Avoir
    ),

    // Méthodes de paiement
    'payment_methods' => array(
        'cash' => 'cash',
        'card' => 'card',
        'check' => 'check',
        'mobile_money' => 'mobile-money',
        'transfer' => 'transfer',
        'deferred' => 'deferred'
    ),

    // Templates
    'templates' => array(
        'B2B' => 'B2B',
        'B2C' => 'B2C',
        'B2G' => 'B2G',
        'B2F' => 'B2F'
    ),

    // Taxes
    'taxes' => array(
        'TVA' => 'TVA',
        'TVAB' => 'TVAB',
        'TVAC' => 'TVAC',
        'TVAD' => 'TVAD'
    ),

    // Statuts
    'status_codes' => array(
        'success' => array(200, 201),
        'client_error' => array(400, 401, 403, 404),
        'server_error' => array(500, 502, 503)
    )
);
