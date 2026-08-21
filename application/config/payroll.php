<?php

$config['staffattendance'] = array(
    'present' => 1,
    'half_day' => 4,
    'late' => 2,
    'absent' => 3,
    'holiday' => 5
);

$config['contracttype'] = array(
    'cdi'        => lang('permanent'),
    'cdd'        => lang('probation'),
    'stage'      => lang('stage'),
   // 'free-lance' => lang('free_lance'),
);


$config['status'] = array(
    'approve'       => lang('approve'),        // Approuvé
    'disapprove'    => lang('disapprove'),     // Refusé
    'pending'       => lang('pending'),        // En attente
    'in_progress'   => lang('in_progress'),    // En cours
    'on_hold'       => lang('on_hold'),        // En pause
    'completed'     => lang('completed'),      // Terminé
    'cancelled'     => lang('cancelled'),      // Annulé
    'review'        => lang('review'),         // En révision
    'draft'         => lang('draft'),          // Brouillon
    'archived'      => lang('archived'),       // Archivé
);

$status_colors = array(
    'approve'       => 'success',   // vert
    'disapprove'    => 'danger',    // rouge
    'pending'       => 'warning',   // jaune
    'in_progress'   => 'primary',   // bleu
    'on_hold'       => 'secondary', // gris
    'completed'     => 'success',   // vert
    'cancelled'     => 'danger',    // rouge
    'review'        => 'info',      // bleu clair
    'draft'         => 'dark',      // noir/gris foncé
    'archived'      => 'secondary', // gris
);

$config['marital_status'] = array(
    'Single' => lang('single'),
    'Married' => lang('married'),
    'Widowed' => lang('widowed'),
    'Seperated' => lang('seperated'),
    'Not Specified' => lang('not_specified'),
);

$config['payroll_status'] = array(
    'generated' => lang('generated'),
    'paid' => lang('paid'),
    'unpaid' => lang('unpaid'),
    'not_generate' => lang('not_generated'),
);
$config['payment_mode'] = array(
    'Espèce' => lang('cash'),
    'Cheque' => lang('cheque'),
    'mobile_money' => lang('mobile_money'),
    'Virement' => lang('transfer_to_bank_account'),
);
$config['enquiry_status'] = array(
    'approve'       => lang('approve'),        // Approuvé
    'disapprove'    => lang('disapprove'),     // Refusé
    'pending'       => lang('pending'),        // En attente
    'in_progress'   => lang('in_progress'),    // En cours
    'on_hold'       => lang('on_hold'),        // En pause
    'completed'     => lang('completed'),      // Terminé
    'cancelled'     => lang('cancelled'),      // Annulé
    'review'        => lang('review'),         // En révision
    'draft'         => lang('draft'),          // Brouillon
    'archived'      => lang('archived'),
);





$config['search_type'] = array(
    'today' => lang('today'),
    'this_week' => lang('this_week'),
    'last_week' => lang('last_week'),
    'this_month' => lang('this_month'),
    'last_month' => lang('last_month'),
    'last_3_month' => lang('last_3_month'),
    'last_6_month' => lang('last_6_month'),
    'last_12_month' => lang('last_12_month'),
    'this_year' => lang('this_year'),
    'last_year' => lang('last_year'),
    'period' => lang('period'),
);
