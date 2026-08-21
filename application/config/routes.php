<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'welcome/index';
$route['admin/admin/refreshUserCountry'] = 'admin/admin/refreshUserCountry';

// Routes pour le QR attendance (PUBLIC - sans login)
$route['qrattendance_public/scan_page'] = 'qrattendance_public/scan_page';
$route['qrattendance_public/process_scan'] = 'qrattendance_public/process_scan';

// ===== NOUVELLES ROUTES POUR ENLEVER /site/ =====
$route['login'] = 'site/login';
$route['logout'] = 'site/logout';
$route['register'] = 'site/register';
$route['resetpassword/([a-z]+)/(:any)'] = 'site/resetpassword/$1/$2';
$route['admin_resetpassword/(:any)'] = 'site/admin_resetpassword/$1';
$route['forgotpassword'] = 'site/forgotpassword';
$route['dashboard'] = 'site/dashboard';
$route['site'] = 'site/vitrine';

// Vos routes existantes
$route['admin/resetpassword/(:any)'] = 'site/admin_resetpassword/$1';
$route['admin/attestationrh'] = 'admin/attestationrh/index';
$route['admin/attestationrh/print_document'] = 'admin/attestationrh/print_document';
$route['admin/attestationrh/print_document/(:num)/(:any)'] = 'admin/attestationrh/print_document/$1/$2';
$route['admin/attestationrh/send_mail'] = 'admin/attestationrh/send_mail';
$route['admin/attestationrh/send_mail/(:num)/(:any)'] = 'admin/attestationrh/send_mail/$1/$2';
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

$route['admin/intelligence'] = 'admin/Intelligence/index';
$route['admin/intelligence/index'] = 'admin/Intelligence/index';
$route['admin/intelligence/chat_ui'] = 'admin/Intelligence/chat_ui';
$route['admin/intelligence/chat'] = 'admin/Intelligence/chat';
$route['admin/intelligence/history'] = 'admin/Intelligence/history';

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
$route['admin/visitors/update_ajax'] = 'admin/visitors/update_ajax';
$route['admin/visitors/export_excel'] = 'admin/visitors/export_excel';
$route['admin/visitors/export_pdf'] = 'admin/visitors/export_pdf';

$route['admin/couriers'] = 'admin/couriers/index';
$route['admin/couriers/add_ajax'] = 'admin/couriers/add_ajax';
$route['admin/couriers/get_courier_data/(:num)'] = 'admin/couriers/get_courier_data/$1';
$route['admin/couriers/update_ajax'] = 'admin/couriers/update_ajax';
$route['admin/couriers/delete/(:num)'] = 'admin/couriers/delete/$1';
$route['admin/couriers/delete_attachment/(:num)'] = 'admin/couriers/delete_attachment/$1';
$route['admin/couriers/download/(:any)'] = 'admin/couriers/download/$1';
$route['admin/couriers/details/(:num)'] = 'admin/couriers/details/$1';
$route['admin/couriers/export_excel'] = 'admin/couriers/export_excel';
$route['admin/couriers/export_pdf'] = 'admin/couriers/export_pdf';
$route['admin/couriers/get_courier_list'] = 'admin/couriers/get_courier_list';

$route['admin/dispatch/get_dispatch_data/(:num)'] = 'admin/dispatch/get_dispatch_data/$1';
$route['admin/dispatch/update_ajax'] = 'admin/dispatch/update_ajax';
$route['admin/dispatch/add_ajax'] = 'admin/dispatch/add_ajax';
$route['admin/dispatch/export_excel'] = 'admin/dispatch/export_excel';
$route['admin/dispatch/export_pdf'] = 'admin/dispatch/export_pdf';

// Routes pour la gestion des demandes
$route['admin/demande'] = 'admin/demande/index';
$route['admin/demande/add_ajax'] = 'admin/demande/add_ajax';
$route['admin/demande/get_demande_data/(:num)'] = 'admin/demande/get_demande_data/$1';
$route['admin/demande/update_ajax'] = 'admin/demande/update_ajax';
$route['admin/demande/delete/(:num)'] = 'admin/demande/delete/$1';
$route['admin/demande/details/(:num)'] = 'admin/demande/details/$1';
$route['admin/demande/export_excel'] = 'admin/demande/export_excel';
$route['admin/demande/export_pdf'] = 'admin/demande/export_pdf';

// Routes pour la gestion des documents
$route['admin/documents'] = 'admin/documents/index';
$route['admin/documents/add_ajax'] = 'admin/documents/add_ajax';
$route['admin/documents/get_document_data/(:num)'] = 'admin/documents/get_document_data/$1';
$route['admin/documents/update_ajax'] = 'admin/documents/update_ajax';
$route['admin/documents/delete/(:num)'] = 'admin/documents/delete/$1';
$route['admin/documents/download/(:any)'] = 'admin/documents/download/$1';
$route['admin/documents/details/(:num)'] = 'admin/documents/details/$1';
$route['admin/documents/export_excel'] = 'admin/documents/export_excel';
$route['admin/documents/export_pdf'] = 'admin/documents/export_pdf';

$route['admin/rendezvous'] = 'admin/rendezvous/index';
$route['admin/rendezvous/add_ajax'] = 'admin/rendezvous/add_ajax';
$route['admin/rendezvous/get_rendezvous_data/(:num)'] = 'admin/rendezvous/get_rendezvous_data/$1';
$route['admin/rendezvous/update_ajax'] = 'admin/rendezvous/update_ajax';
$route['admin/rendezvous/delete/(:num)'] = 'admin/rendezvous/delete/$1';
$route['admin/rendezvous/details/(:num)'] = 'admin/rendezvous/details/$1';
$route['admin/rendezvous/export_excel'] = 'admin/rendezvous/export_excel';
$route['admin/rendezvous/export_pdf'] = 'admin/rendezvous/export_pdf';

$route['admin/reunions'] = 'admin/reunions/index';
$route['admin/reunions/add_ajax'] = 'admin/reunions/add_ajax';
$route['admin/reunions/get_reunion_data/(:num)'] = 'admin/reunions/get_reunion_data/$1';
$route['admin/reunions/update_ajax'] = 'admin/reunions/update_ajax';
$route['admin/reunions/delete/(:num)'] = 'admin/reunions/delete/$1';
$route['admin/reunions/details/(:num)'] = 'admin/reunions/details/$1';
$route['admin/reunions/export_excel'] = 'admin/reunions/export_excel';
$route['admin/reunions/export_pdf'] = 'admin/reunions/export_pdf';

$route['admin/rapports'] = 'admin/rapports/index';
$route['admin/rapports/add_ajax'] = 'admin/rapports/add_ajax';
$route['admin/rapports/get_rapport_data/(:num)'] = 'admin/rapports/get_rapport_data/$1';
$route['admin/rapports/update_ajax'] = 'admin/rapports/update_ajax';
$route['admin/rapports/delete/(:num)'] = 'admin/rapports/delete/$1';
$route['admin/rapports/download/(:any)'] = 'admin/rapports/download/$1';
$route['admin/rapports/details/(:num)'] = 'admin/rapports/details/$1';
$route['admin/rapports/export_excel'] = 'admin/rapports/export_excel';
$route['admin/rapports/export_pdf'] = 'admin/rapports/export_pdf';

$route['admin/immobilisations'] = 'admin/immobilisations/index';
$route['admin/immobilisations/get_data/(:num)'] = 'admin/immobilisations/get_data/$1';
$route['admin/immobilisations/update_ajax'] = 'admin/immobilisations/update_ajax';
$route['admin/immobilisations/delete/(:num)'] = 'admin/immobilisations/delete/$1';
$route['admin/immobilisations/details/(:num)'] = 'admin/immobilisations/details/$1';
$route['admin/immobilisations/calculer_amortissement/(:num)'] = 'admin/immobilisations/calculer_amortissement/$1';
$route['admin/immobilisations/ceder'] = 'admin/immobilisations/ceder';
$route['admin/immobilisations/export_excel'] = 'admin/immobilisations/export_excel';
$route['admin/immobilisations/export_pdf'] = 'admin/immobilisations/export_pdf';
$route['admin/immobilisations/add_ajax'] = 'admin/immobilisations/add_ajax';

$route['admin/amortissements'] = 'admin/amortissements/index';
$route['admin/amortissements/details/(:num)'] = 'admin/amortissements/details/$1';
$route['admin/amortissements/export_excel'] = 'admin/amortissements/export_excel';
$route['admin/amortissements/export_pdf'] = 'admin/amortissements/export_pdf';
$route['admin/amortissements/plan/(:num)'] = 'admin/amortissements/plan/$1';

$route['admin/rapports_cultes'] = 'admin/rapports_cultes/index';
$route['admin/rapports_cultes/add_ajax'] = 'admin/rapports_cultes/add_ajax';
$route['admin/rapports_cultes/get_data/(:num)'] = 'admin/rapports_cultes/get_data/$1';
$route['admin/rapports_cultes/update_ajax'] = 'admin/rapports_cultes/update_ajax';
$route['admin/rapports_cultes/delete/(:num)'] = 'admin/rapports_cultes/delete/$1';
$route['admin/rapports_cultes/details/(:num)'] = 'admin/rapports_cultes/details/$1';
$route['admin/rapports_cultes/export_excel'] = 'admin/rapports_cultes/export_excel';
$route['admin/rapports_cultes/export_pdf'] = 'admin/rapports_cultes/export_pdf';

$route['admin/membres'] = 'admin/membres/index';
$route['admin/membres/add_ajax'] = 'admin/membres/add_ajax';
$route['admin/membres/get_data/(:num)'] = 'admin/membres/get_data/$1';
$route['admin/membres/update_ajax'] = 'admin/membres/update_ajax';
$route['admin/membres/delete/(:num)'] = 'admin/membres/delete/$1';
$route['admin/membres/details/(:num)'] = 'admin/membres/details/$1';
$route['admin/membres/export_excel'] = 'admin/membres/export_excel';
$route['admin/membres/export_pdf'] = 'admin/membres/export_pdf';

$route['admin/groupes'] = 'admin/groupes/index';
$route['admin/groupes/add_ajax'] = 'admin/groupes/add_ajax';
$route['admin/groupes/get_data/(:num)'] = 'admin/groupes/get_data/$1';
$route['admin/groupes/update_ajax'] = 'admin/groupes/update_ajax';
$route['admin/groupes/delete/(:num)'] = 'admin/groupes/delete/$1';
$route['admin/groupes/details/(:num)'] = 'admin/groupes/details/$1';
$route['admin/groupes/export_excel'] = 'admin/groupes/export_excel';
$route['admin/groupes/export_pdf'] = 'admin/groupes/export_pdf';

$route['admin/evenements'] = 'admin/evenements/index';
$route['admin/evenements/add_ajax'] = 'admin/evenements/add_ajax';
$route['admin/evenements/get_data/(:num)'] = 'admin/evenements/get_data/$1';
$route['admin/evenements/update_ajax'] = 'admin/evenements/update_ajax';
$route['admin/evenements/delete/(:num)'] = 'admin/evenements/delete/$1';
$route['admin/evenements/details/(:num)'] = 'admin/evenements/details/$1';
$route['admin/evenements/export_excel'] = 'admin/evenements/export_excel';
$route['admin/evenements/export_pdf'] = 'admin/evenements/export_pdf';

$route['admin/offrandes'] = 'admin/offrandes/index';
$route['admin/offrandes/add_ajax'] = 'admin/offrandes/add_ajax';
$route['admin/offrandes/get_data/(:num)'] = 'admin/offrandes/get_data/$1';
$route['admin/offrandes/update_ajax'] = 'admin/offrandes/update_ajax';
$route['admin/offrandes/delete/(:num)'] = 'admin/offrandes/delete/$1';
$route['admin/offrandes/details/(:num)'] = 'admin/offrandes/details/$1';
$route['admin/offrandes/export_excel'] = 'admin/offrandes/export_excel';
$route['admin/offrandes/export_pdf'] = 'admin/offrandes/export_pdf';
$route['admin/offrandes/search_membres'] = 'admin/offrandes/search_membres';

$route['admin/predicateurs'] = 'admin/predicateurs/index';
$route['admin/predicateurs/add_ajax'] = 'admin/predicateurs/add_ajax';
$route['admin/predicateurs/get_data/(:num)'] = 'admin/predicateurs/get_data/$1';
$route['admin/predicateurs/update_ajax'] = 'admin/predicateurs/update_ajax';
$route['admin/predicateurs/delete/(:num)'] = 'admin/predicateurs/delete/$1';
$route['admin/predicateurs/details/(:num)'] = 'admin/predicateurs/details/$1';
$route['admin/predicateurs/export_excel'] = 'admin/predicateurs/export_excel';
$route['admin/predicateurs/export_pdf'] = 'admin/predicateurs/export_pdf';

$route['admin/baptemes'] = 'admin/baptemes/index';
$route['admin/baptemes/add_ajax'] = 'admin/baptemes/add_ajax';
$route['admin/baptemes/get_data/(:num)'] = 'admin/baptemes/get_data/$1';
$route['admin/baptemes/update_ajax'] = 'admin/baptemes/update_ajax';
$route['admin/baptemes/delete/(:num)'] = 'admin/baptemes/delete/$1';
$route['admin/baptemes/details/(:num)'] = 'admin/baptemes/details/$1';
$route['admin/baptemes/generer_certificat/(:num)'] = 'admin/baptemes/generer_certificat/$1';
$route['admin/baptemes/export_excel'] = 'admin/baptemes/export_excel';
$route['admin/baptemes/export_pdf'] = 'admin/baptemes/export_pdf';

$route['admin/mariages'] = 'admin/mariages/index';
$route['admin/mariages/add_ajax'] = 'admin/mariages/add_ajax';
$route['admin/mariages/get_data/(:num)'] = 'admin/mariages/get_data/$1';
$route['admin/mariages/update_ajax'] = 'admin/mariages/update_ajax';
$route['admin/mariages/delete/(:num)'] = 'admin/mariages/delete/$1';
$route['admin/mariages/details/(:num)'] = 'admin/mariages/details/$1';
$route['admin/mariages/generer_certificat/(:num)'] = 'admin/mariages/generer_certificat/$1';
$route['admin/mariages/export_excel'] = 'admin/mariages/export_excel';
$route['admin/mariages/export_pdf'] = 'admin/mariages/export_pdf';

$route['admin/funerailles'] = 'admin/funerailles/index';
$route['admin/funerailles/add_ajax'] = 'admin/funerailles/add_ajax';
$route['admin/funerailles/get_data/(:num)'] = 'admin/funerailles/get_data/$1';
$route['admin/funerailles/update_ajax'] = 'admin/funerailles/update_ajax';
$route['admin/funerailles/delete/(:num)'] = 'admin/funerailles/delete/$1';
$route['admin/funerailles/details/(:num)'] = 'admin/funerailles/details/$1';
$route['admin/funerailles/generer_certificat/(:num)'] = 'admin/funerailles/generer_certificat/$1';
$route['admin/funerailles/export_excel'] = 'admin/funerailles/export_excel';
$route['admin/funerailles/export_pdf'] = 'admin/funerailles/export_pdf';

// ========================================== //
// ROUTES TICKETS                             //
// ========================================== //
$route['admin/tickets'] = 'admin/Tickets/index';
$route['admin/tickets/add'] = 'admin/Tickets/add';
$route['admin/tickets/add_ajax'] = 'admin/Tickets/add_ajax';
$route['admin/tickets/get_ticket_data/(:num)'] = 'admin/Tickets/get_ticket_data/$1';
$route['admin/tickets/update_ajax'] = 'admin/Tickets/update_ajax';
$route['admin/tickets/repondre_ajax'] = 'admin/Tickets/repondre_ajax';
$route['admin/tickets/changer_statut/(:num)/(:num)'] = 'admin/Tickets/changer_statut/$1/$2';
$route['admin/tickets/delete/(:num)'] = 'admin/Tickets/delete/$1';
$route['admin/tickets/download/(:any)'] = 'admin/Tickets/download/$1';
$route['admin/tickets/details/(:num)'] = 'admin/Tickets/details/$1';
$route['admin/tickets/export_excel'] = 'admin/Tickets/export_excel';
$route['admin/tickets/export_pdf'] = 'admin/Tickets/export_pdf';

$route['admin/support_messages'] = 'admin/Support_messages/index';
$route['admin/support_messages/edit/(:num)'] = 'admin/Support_messages/edit/$1';
$route['admin/support_messages/save'] = 'admin/Support_messages/save';
$route['admin/support_messages/delete/(:num)'] = 'admin/Support_messages/delete/$1';

$route['admin/selling/get_payment_sources'] = 'admin/selling/get_payment_sources';
$route['admin/selling/add_payment'] = 'admin/selling/add_payment';

$route['admin/enquiry/print_permission/(:num)'] = 'admin/enquiry/print_permission/$1';

$route['admin/leaverequest/get_leave_notifications'] = 'admin/leaverequest/get_leave_notifications';
$route['admin/leaverequest/mark_leave_read/(:num)'] = 'admin/leaverequest/mark_leave_read/$1';
$route['admin/leaverequest/mark_all_leave_read'] = 'admin/leaverequest/mark_all_leave_read';
$route['admin/leaverequest/get_leave_history'] = 'admin/leaverequest/get_leave_history';

// ================================================================
// ROUTES OHADA - MODULES COMPTABLES (CORRIGÉES)
// ================================================================

// ===== JOURNAUX AUXILIAIRES =====
// ================================================================
// ROUTES OHADA - JOURNAUX AUXILIAIRES
// ================================================================

// Journaux auxiliaires
// ================================================================
// ROUTES OHADA - JOURNAUX AUXILIAIRES
// ================================================================

// ----- Journaux auxiliaires (liste principale) -----
$route['admin/frontoffice/journaux_auxiliaires'] = 'admin/Journaux_auxiliaires/index';
$route['admin/frontoffice/journaux_auxiliaires/index'] = 'admin/Journaux_auxiliaires/index';

// ----- Actions AJAX -----
$route['admin/frontoffice/journaux_auxiliaires/add_ajax'] = 'admin/Journaux_auxiliaires/add_ajax';
$route['admin/frontoffice/journaux_auxiliaires/get_data/(:num)'] = 'admin/Journaux_auxiliaires/get_data/$1';
$route['admin/frontoffice/journaux_auxiliaires/update_ajax'] = 'admin/Journaux_auxiliaires/update_ajax';

// ----- Actions CRUD -----
$route['admin/frontoffice/journaux_auxiliaires/delete/(:num)'] = 'admin/Journaux_auxiliaires/delete/$1';

// ----- Écritures (détails du journal) -----
$route['admin/frontoffice/journaux_auxiliaires/ecritures/(:num)'] = 'admin/Journaux_auxiliaires/ecritures/$1';

// ----- Export -----
$route['admin/frontoffice/journaux_auxiliaires/export_excel'] = 'admin/Journaux_auxiliaires/export_excel';

// ===== BALANCE GÉNÉRALE =====
// Balance générale
// ================================================================
// ROUTES OHADA - BALANCE GÉNÉRALE
// ================================================================

$route['admin/frontoffice/balance_generale'] = 'admin/Balance_generale/index';
$route['admin/frontoffice/balance_generale/index'] = 'admin/Balance_generale/index';
$route['admin/frontoffice/balance_generale/export_excel'] = 'admin/Balance_generale/export_excel';
$route['admin/frontoffice/balance_generale/export_pdf'] = 'admin/Balance_generale/export_pdf';
$route['admin/frontoffice/balance_generale/verifier'] = 'admin/Balance_generale/verifier';

// ===== BALANCE AUXILIAIRE =====
$route['admin/frontoffice/balance_auxiliaire'] = 'admin/Balance_auxiliaire/index';
$route['admin/frontoffice/balance_auxiliaire/index'] = 'admin/Balance_auxiliaire/index';
$route['admin/frontoffice/balance_auxiliaire/export_pdf'] = 'admin/Balance_auxiliaire/export_pdf';
$route['admin/frontoffice/balance_auxiliaire/export_excel'] = 'admin/Balance_auxiliaire/export_excel';

// ===== GRAND LIVRE =====
$route['admin/frontoffice/grand_livre'] = 'admin/Grand_livre/index';
$route['admin/frontoffice/grand_livre/index'] = 'admin/Grand_livre/index';
$route['admin/frontoffice/grand_livre/detail/(:any)'] = 'admin/Grand_livre/detail/$1';
$route['admin/frontoffice/grand_livre/export_pdf'] = 'admin/Grand_livre/export_pdf';
$route['admin/frontoffice/grand_livre/export_excel'] = 'admin/Grand_livre/export_excel';

// ===== BILAN COMPTABLE =====
$route['admin/frontoffice/bilan_comptable'] = 'admin/Bilan_comptable/index';
$route['admin/frontoffice/bilan_comptable/index'] = 'admin/Bilan_comptable/index';
$route['admin/frontoffice/bilan_comptable/generer'] = 'admin/Bilan_comptable/generer';
$route['admin/frontoffice/bilan_comptable/export_pdf'] = 'admin/Bilan_comptable/export_pdf';
$route['admin/frontoffice/bilan_comptable/export_excel'] = 'admin/Bilan_comptable/export_excel';
$route['admin/frontoffice/bilan_comptable/compare'] = 'admin/Bilan_comptable/compare';

// ===== COMPTE DE RÉSULTAT =====
$route['admin/frontoffice/compte_resultat'] = 'admin/Compte_resultat/index';
$route['admin/frontoffice/compte_resultat/index'] = 'admin/Compte_resultat/index';
$route['admin/frontoffice/compte_resultat/generer'] = 'admin/Compte_resultat/generer';
$route['admin/frontoffice/compte_resultat/export_pdf'] = 'admin/Compte_resultat/export_pdf';
$route['admin/frontoffice/compte_resultat/export_excel'] = 'admin/Compte_resultat/export_excel';
$route['admin/frontoffice/compte_resultat/sig'] = 'admin/Compte_resultat/sig';

// ===== TABLEAU DE FINANCEMENT (TAFIRE) =====
$route['admin/frontoffice/tafire'] = 'admin/Tafire/index';
$route['admin/frontoffice/tafire/index'] = 'admin/Tafire/index';
$route['admin/frontoffice/tafire/generer'] = 'admin/Tafire/generer';
$route['admin/frontoffice/tafire/export_pdf'] = 'admin/Tafire/export_pdf';
$route['admin/frontoffice/tafire/export_excel'] = 'admin/Tafire/export_excel';

// ===== NOTES ANNEXES =====
$route['admin/frontoffice/notes_annexes'] = 'admin/Notes_annexes/index';
$route['admin/frontoffice/notes_annexes/index'] = 'admin/Notes_annexes/index';
$route['admin/frontoffice/notes_annexes/add'] = 'admin/Notes_annexes/add';
$route['admin/frontoffice/notes_annexes/edit/(:num)'] = 'admin/Notes_annexes/edit/$1';
$route['admin/frontoffice/notes_annexes/delete/(:num)'] = 'admin/Notes_annexes/delete/$1';
$route['admin/frontoffice/notes_annexes/export_pdf'] = 'admin/Notes_annexes/export_pdf';

// ===== CLÔTURE D'EXERCICE =====
$route['admin/frontoffice/cloture_exercice'] = 'admin/Cloture_exercice/index';
$route['admin/frontoffice/cloture_exercice/index'] = 'admin/Cloture_exercice/index';
$route['admin/frontoffice/cloture_exercice/verifier'] = 'admin/Cloture_exercice/verifier';
$route['admin/frontoffice/cloture_exercice/cloturer'] = 'admin/Cloture_exercice/cloturer';
$route['admin/frontoffice/cloture_exercice/rouvrir'] = 'admin/Cloture_exercice/rouvrir';
$route['admin/frontoffice/cloture_exercice/export_pdf'] = 'admin/Cloture_exercice/export_pdf';

// ===== RAPPROCHEMENT BANCAIRE =====
$route['admin/frontoffice/rapprochement_bancaire'] = 'admin/Rapprochement_bancaire/index';
$route['admin/frontoffice/rapprochement_bancaire/index'] = 'admin/Rapprochement_bancaire/index';
$route['admin/frontoffice/rapprochement_bancaire/lettrage'] = 'admin/Rapprochement_bancaire/lettrage';
$route['admin/frontoffice/rapprochement_bancaire/delettrage/(:num)'] = 'admin/Rapprochement_bancaire/delettrage/$1';
$route['admin/frontoffice/rapprochement_bancaire/importer'] = 'admin/Rapprochement_bancaire/importer';
$route['admin/frontoffice/rapprochement_bancaire/export_pdf'] = 'admin/Rapprochement_bancaire/export_pdf';

// ===== GESTION DES TIERS =====
$route['admin/frontoffice/tiers'] = 'admin/Tiers/index';
$route['admin/frontoffice/tiers/index'] = 'admin/Tiers/index';
$route['admin/frontoffice/tiers/add'] = 'admin/Tiers/add';
$route['admin/frontoffice/tiers/edit/(:num)'] = 'admin/Tiers/edit/$1';
$route['admin/frontoffice/tiers/delete/(:num)'] = 'admin/Tiers/delete/$1';
$route['admin/frontoffice/tiers/type/(:any)'] = 'admin/Tiers/type/$1';
$route['admin/frontoffice/tiers/export_csv'] = 'admin/Tiers/export_csv';

// ===== COMPTABILITÉ ANALYTIQUE =====
$route['admin/frontoffice/analytique'] = 'admin/Analytique/index';
$route['admin/frontoffice/analytique/index'] = 'admin/Analytique/index';
$route['admin/frontoffice/analytique/add'] = 'admin/Analytique/add';
$route['admin/frontoffice/analytique/edit/(:num)'] = 'admin/Analytique/edit/$1';
$route['admin/frontoffice/analytique/delete/(:num)'] = 'admin/Analytique/delete/$1';
$route['admin/frontoffice/analytique/repartition'] = 'admin/Analytique/repartition';
$route['admin/frontoffice/analytique/export_pdf'] = 'admin/Analytique/export_pdf';

// ===== PARAMÈTRES OHADA =====
$route['admin/frontoffice/parametres_ohada'] = 'admin/Parametres_ohada/index';
$route['admin/frontoffice/parametres_ohada/index'] = 'admin/Parametres_ohada/index';
$route['admin/frontoffice/parametres_ohada/save'] = 'admin/Parametres_ohada/save';
$route['admin/frontoffice/parametres_ohada/reset'] = 'admin/Parametres_ohada/reset';

// ===== EXERCICES COMPTABLES =====
$route['admin/frontoffice/exercices_comptables'] = 'admin/Exercices_comptables/index';
$route['admin/frontoffice/exercices_comptables/index'] = 'admin/Exercices_comptables/index';
$route['admin/frontoffice/exercices_comptables/add'] = 'admin/Exercices_comptables/add';
$route['admin/frontoffice/exercices_comptables/edit/(:num)'] = 'admin/Exercices_comptables/edit/$1';
$route['admin/frontoffice/exercices_comptables/delete/(:num)'] = 'admin/Exercices_comptables/delete/$1';
$route['admin/frontoffice/exercices_comptables/activer/(:num)'] = 'admin/Exercices_comptables/activer/$1';

// ===== PLAN SYSCOHADA =====
$route['admin/frontoffice/syscohada'] = 'admin/Syscohada/index';
$route['admin/frontoffice/syscohada/index'] = 'admin/Syscohada/index';
$route['admin/frontoffice/syscohada/classes'] = 'admin/Syscohada/classes';
$route['admin/frontoffice/syscohada/comptes'] = 'admin/Syscohada/comptes';
$route['admin/frontoffice/syscohada/compte/(:any)'] = 'admin/Syscohada/compte/$1';
$route['admin/frontoffice/syscohada/import'] = 'admin/Syscohada/import';
$route['admin/frontoffice/syscohada/export'] = 'admin/Syscohada/export';

// ===== CONFIGURATION DES JOURNAUX =====
$route['admin/frontoffice/journaux_config'] = 'admin/Journaux_config/index';
$route['admin/frontoffice/journaux_config/index'] = 'admin/Journaux_config/index';
$route['admin/frontoffice/journaux_config/save'] = 'admin/Journaux_config/save';
$route['admin/frontoffice/journaux_config/reset'] = 'admin/Journaux_config/reset';

// ===== ROUTES QR CODE ATTENDANCE =====
$route['admin/qrattendance/display_qr'] = 'admin/Qrattendance/display_qr';
$route['admin/qrattendance/scan_page'] = 'admin/Qrattendance_public/scan_page';
$route['admin/qrattendance/process_scan'] = 'admin/Qrattendance_public/process_scan';
$route['admin/qrattendance/today_attendance'] = 'admin/Qrattendance/today_attendance';
$route['admin/qrattendance/attendance_report'] = 'admin/Qrattendance/attendance_report';