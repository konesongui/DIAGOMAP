<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'welcome/index';
$route['user/resetpassword/([a-z]+)/(:any)'] = 'site/resetpassword/$1/$2';
$route['admin/resetpassword/(:any)'] = 'site/admin_resetpassword/$1';
$route['admin/unauthorized'] = 'admin/admin/unauthorized';
$route['parent/unauthorized'] = 'parent/parents/unauthorized';
$route['student/unauthorized'] = 'user/user/unauthorized';
$route['teacher/unauthorized'] = 'teacher/teacher/unauthorized';
$route['accountant/unauthorized'] = 'accountant/accountant/unauthorized';
$route['librarian/unauthorized'] = 'librarian/librarian/unauthorized';
$route['404_override'] = 'welcome/show_404';
$route['translate_uri_dashes'] = FALSE;
$route['cron/(:any)'] = 'cron/index/$1';
$route['admin/barcode/(:any)'] = 'admin/BarcodeController::generateBarcode/$1';
$route['/qr-form'] = 'BarcodeController/index';

$route['/generate-qr'] = 'BarcodeController/generateQr';


$route['admin/intelligence/chat_ui'] = 'intelligence/chat_ui';
$route['admin/intelligence/chat'] = 'intelligence/chat';
$route['admin/intelligence/history'] = 'intelligence/history';

//======= front url rewriting==========
$route['page/(:any)'] = 'welcome/page/$1';
$route['read/(:any)'] = 'welcome/read/$1';
$route['online_admission'] = 'welcome/admission';
$route['frontend'] = 'welcome';

// Routes pour les visiteurs
$route['admin/visitors'] = 'admin/visitors/index';
$route['admin/visitors/delete/(:num)'] = 'admin/visitors/delete/$1';
$route['admin/visitors/edit/(:num)'] = 'admin/visitors/edit/$1';
$route['admin/visitors/details/(:num)'] = 'admin/visitors/details/$1';
$route['admin/visitors/download/(:any)'] = 'admin/visitors/download/$1';
$route['admin/visitors/imagedelete/(:num)/(:any)'] = 'admin/visitors/imagedelete/$1/$2';
$route['admin/visitors/get_visitor_data/(:num)'] = 'admin/visitors/get_visitor_data/$1';
// NOUVELLES ROUTES
$route['admin/visitors/get_visitor_data/(:num)'] = 'admin/visitors/get_visitor_data/$1';
$route['admin/visitors/update_ajax'] = 'admin/visitors/update_ajax';
$route['admin/visitors/export_excel'] = 'admin/visitors/export_excel';
$route['admin/visitors/export_pdf'] = 'admin/visitors/export_pdf';