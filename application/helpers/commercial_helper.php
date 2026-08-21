<?php
  if (!function_exists('format_price')) {
    function format_price($price) {
      return number_format($price, 2, ',', ' ').' €';
    }
  }

  if (!function_exists('calculate_tax')) {
    function calculate_tax($price_ht, $tax_rate) {
      return $price_ht * ($tax_rate / 100);
    }
  }

  if (!function_exists('generate_reference')) {
    function generate_reference($prefix, $id) {
      return $prefix.str_pad($id, 6, '0', STR_PAD_LEFT);
    }
  }

  if (!function_exists('get_status_badge')) {
    function get_status_badge($status) {
      $classes = [
        'pending' => 'badge badge-warning',
        'confirmed' => 'badge badge-primary',
        'shipped' => 'badge badge-info',
        'invoiced' => 'badge badge-success',
        'cancelled' => 'badge badge-danger',
        'unpaid' => 'badge badge-danger',
        'paid' => 'badge badge-success'
      ];
      
      $texts = [
        'pending' => 'En attente',
        'confirmed' => 'Confirmé',
        'shipped' => 'Expédié',
        'invoiced' => 'Facturé',
        'cancelled' => 'Annulé',
        'unpaid' => 'Impayé',
        'paid' => 'Payé'
      ];
      
      return '<span class="'.$classes[$status].'">'.$texts[$status].'</span>';
    }

  }