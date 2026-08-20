<!DOCTYPE html>
<html <?php echo $this->customlib->getRTL(); ?>>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $this->customlib->getAppName(); ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="Cache-control" content="no-cache">
    <meta name="theme-color" content="#424242" />

    <?php
    // ============================================================
    // RÉCUPÉRATION ET VÉRIFICATION DES LOGOS
    // ============================================================
    ob_start();
    $logo_name = $this->setting_model->getAdminlogo();
    ob_end_clean();

    ob_start();
    $small_logo_name = $this->setting_model->getAdminsmalllogo();
    ob_end_clean();

    // Vérification du logo principal
    $logo_path = FCPATH . 'uploads/school_content/admin_logo/' . $logo_name;
    $logo_exists = (!empty($logo_name) && file_exists($logo_path));

    // URL du logo
    $logo_url = ($logo_exists) ? base_url() . 'uploads/school_content/admin_logo/' . $logo_name : base_url() . 'uploads/school_content/default_logo.png';

    // Vérification du small logo
    $small_logo_path = FCPATH . 'uploads/school_content/admin_small_logo/' . $small_logo_name;
    $small_logo_exists = (!empty($small_logo_name) && file_exists($small_logo_path));

    $small_logo_url = ($small_logo_exists) ? base_url() . 'uploads/school_content/admin_small_logo/' . $small_logo_name : $logo_url;
    ?>

    <link href="<?php echo $logo_url; ?>" rel="shortcut icon" type="image/x-icon">
    <link rel="manifest" href="<?php echo base_url(); ?>manifest.json">

    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/jquery.mCustomScrollbar.min.css">
    <?php $this->load->view('layout/theme'); ?>

    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/ionicons.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/iCheck/flat/blue.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/morris/morris.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/datepicker/datepicker3.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/colorpicker/bootstrap-colorpicker.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/daterangepicker/daterangepicker-bs3.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/custom_style.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/datepicker/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/dropify.min.css">
    <link href="<?php echo base_url(); ?>backend/dist/css/nprogress.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>backend/dist/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>backend/dist/datatables/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>backend/dist/datatables/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>backend/dist/datatables/css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>backend/dist/datatables/css/rowReorder.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>backend/dist/css/bootstrap-select.min.css">

    <script src="<?php echo base_url(); ?>backend/custom/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>backend/dist/js/moment.min.js"></script>
    <script src="<?php echo base_url(); ?>backend/datepicker/js/bootstrap-datetimepicker.js"></script>
    <script src="<?php echo base_url(); ?>backend/plugins/colorpicker/bootstrap-colorpicker.js"></script>
    <script src="<?php echo base_url(); ?>backend/datepicker/date.js"></script>
    <script src="<?php echo base_url(); ?>backend/dist/js/jquery-ui.min.js"></script>
    <script src="<?php echo base_url(); ?>backend/js/school-custom.js"></script>
    <script src="<?php echo base_url(); ?>backend/js/school-admin-custom.js"></script>
    <script src="<?php echo base_url(); ?>backend/js/sstoast.js"></script>

    <!-- fullCalendar -->
    <link rel="stylesheet" href="<?php echo base_url() ?>backend/fullcalendar/dist/fullcalendar.min.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>backend/fullcalendar/dist/fullcalendar.print.min.css" media="print">

    <script type="text/javascript">
        var baseurl = "<?php echo base_url(); ?>";
        var start_week=<?php echo $this->customlib->getStartWeek();?>;
        var chk_validate="<?php echo $this->config->item('SSLK')?>";
        window.diagomaOfflineConfig = {
            baseUrl: "<?php echo base_url(); ?>",
            appName: "<?php echo html_escape($this->customlib->getAppName()); ?>",
            offlineFallbackUrl: "<?php echo base_url(); ?>offline.html"
        };
    </script>

    <style type="text/css">
        :root {
            --app-primary: #1b4f80;
            --app-primary-dark: #143d63;
            --app-primary-light: #2d6ea8;
            --app-primary-soft: #eaf3fb;
        }

        .diagoma-offline-banner {
            position: fixed;
            top: 12px;
            right: 12px;
            z-index: 1055;
            min-width: 280px;
            max-width: 420px;
            padding: 12px 14px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.18);
            color: #fff;
            display: none;
            align-items: center;
            gap: 10px;
            font-size: 13px;
        }

        .diagoma-offline-banner.is-visible {
            display: flex;
        }

        .diagoma-offline-banner.is-online {
            background: linear-gradient(135deg, #0f766e, #0d9488);
        }

        .diagoma-offline-banner.is-offline {
            background: linear-gradient(135deg, #b45309, #d97706);
        }

        .diagoma-offline-banner.is-error {
            background: linear-gradient(135deg, #b91c1c, #dc2626);
        }

        .diagoma-offline-banner .diagoma-offline-banner__content {
            flex: 1;
            line-height: 1.35;
        }

        .diagoma-offline-banner .diagoma-offline-banner__title {
            display: block;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .diagoma-offline-banner .diagoma-offline-banner__action {
            border: 1px solid rgba(255, 255, 255, 0.35);
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 600;
        }

        .diagoma-offline-banner .diagoma-offline-banner__action:hover,
        .diagoma-offline-banner .diagoma-offline-banner__action:focus {
            color: #fff;
            background: rgba(255, 255, 255, 0.2);
        }

        /* ===== NOTIFICATIONS COMPLÈTES ===== */

        /* Badge de notification */
        .notification-badge {
            background: linear-gradient(135deg, #EF4444, #DC2626) !important;
            color: white !important;
            font-size: 10px !important;
            font-weight: bold !important;
            padding: 2px 6px !important;
            border-radius: 10px !important;
            position: absolute !important;
            top: 0px !important;
            right: 5px !important;
            min-width: 18px !important;
            height: 18px !important;
            text-align: center !important;
            line-height: 14px !important;
            border: 2px solid white !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
            animation: pulse-notification 2s infinite !important;
            z-index: 10 !important;
        }

        .notification-badge.zero {
            background: #22c55e !important;
            animation: none !important;
        }

        .unified-notification > a {
            position: relative !important;
        }

        .notification-category-tabs {
            display: flex !important;
            margin-top: 12px !important;
            gap: 5px !important;
            border-top: 1px solid rgba(255,255,255,0.2) !important;
            padding-top: 10px !important;
        }

        .notification-category-tabs .category-tab {
            flex: 1 !important;
            padding: 8px 12px !important;
            border: none !important;
            border-radius: 8px !important;
            background: rgba(255,255,255,0.15) !important;
            color: rgba(255,255,255,0.85) !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            text-align: center !important;
            position: relative !important;
        }

        .notification-category-tabs .category-tab:hover {
            background: rgba(255,255,255,0.25) !important;
        }

        .notification-category-tabs .category-tab.active {
            background: rgba(255,255,255,0.3) !important;
            color: white !important;
        }

        .notification-category-tabs .category-tab.has-unread:not(.active) {
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.45) !important;
        }

        .notification-category-tabs .notification-badge {
            top: -6px !important;
            right: -6px !important;
        }

        .notification-category-panel {
            display: none !important;
        }

        .notification-category-panel.active {
            display: block !important;
        }

        .notification-section-header {
            padding: 15px 18px 10px !important;
            border-bottom: 1px solid #eef2f7 !important;
            background: #ffffff !important;
        }

        .notification-section-title {
            margin: 0 !important;
            font-size: 15px !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 10px !important;
        }

        .notification-section-title i {
            margin-right: 8px !important;
            color: var(--app-primary) !important;
        }

        .notification-section-title .notification-count {
            background: #eef2ff !important;
            color: var(--app-primary) !important;
        }

        .notification-section-subtitle {
            display: block !important;
            margin-top: 6px !important;
            color: #64748b !important;
            font-size: 12px !important;
        }

        .notification-section-header .notification-tabs {
            margin-top: 10px !important;
            padding-top: 0 !important;
            border-top: none !important;
        }

        .notification-section-header .notification-tabs .tab-btn {
            background: #f1f5f9 !important;
            color: #475569 !important;
        }

        .notification-section-header .notification-tabs .tab-btn:hover {
            background: #e2e8f0 !important;
        }

        .notification-section-header .notification-tabs .tab-btn.active {
            background: var(--app-primary) !important;
            color: white !important;
        }

        .notification-section-header .notification-tabs .tab-btn .tab-badge {
            background: rgba(39,55,114,0.12) !important;
            color: inherit !important;
        }

        .notification-section-header .notification-tabs .tab-btn.active .tab-badge {
            background: rgba(255,255,255,0.2) !important;
        }

        @keyframes pulse-notification {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        /* Dropdown */
        .notification-dropdown {
            width: 420px !important;
            max-height: 500px !important;
            padding: 0 !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15) !important;
            border: none !important;
            overflow: hidden !important;
        }

        /* En-tête avec onglets */
        .notification-header {
            background: linear-gradient(135deg, var(--app-primary-dark) 0%, var(--app-primary) 100%) !important;
            color: white !important;
            padding: 15px 20px !important;
        }

        .notification-header h4 {
            margin: 0 !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }

        .notification-header h4 i {
            margin-right: 8px !important;
        }

        .notification-header .notification-count {
            background: rgba(255,255,255,0.2) !important;
            padding: 2px 10px !important;
            border-radius: 12px !important;
            font-size: 12px !important;
        }

        .notification-tabs {
            display: flex !important;
            margin-top: 12px !important;
            gap: 5px !important;
            border-top: 1px solid rgba(255,255,255,0.2) !important;
            padding-top: 10px !important;
        }

        .notification-tabs .tab-btn {
            flex: 1 !important;
            padding: 6px 12px !important;
            border: none !important;
            border-radius: 6px !important;
            background: rgba(255,255,255,0.15) !important;
            color: rgba(255,255,255,0.8) !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            text-align: center !important;
        }

        .notification-tabs .tab-btn:hover {
            background: rgba(255,255,255,0.25) !important;
        }

        .notification-tabs .tab-btn.active {
            background: rgba(255,255,255,0.3) !important;
            color: white !important;
        }

        .notification-tabs .tab-btn .tab-badge {
            background: rgba(255,255,255,0.3) !important;
            padding: 1px 8px !important;
            border-radius: 10px !important;
            font-size: 10px !important;
            margin-left: 5px !important;
        }

        /* ===== CORRECTION : Sélecteurs plus spécifiques pour les onglets ===== */
        /* Au lieu de .tab-content, utiliser .notification-dropdown .tab-content */
        .notification-dropdown .tab-content {
            display: none !important;
        }

        .notification-dropdown .tab-content.active {
            display: block !important;
        }

        /* Liste des notifications */
        .notification-list-wrapper {
            max-height: 320px !important;
            overflow-y: auto !important;
        }

        .notification-list {
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .notification-item {
            display: flex !important;
            padding: 12px 15px !important;
            border-bottom: 1px solid #f0f2f5 !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            align-items: flex-start !important;
            gap: 12px !important;
            position: relative !important;
        }

        .notification-item:hover {
            background: #f8fafc !important;
        }

        .notification-item.stock-alert-out {
            background: #fff1f2 !important;
            border-left: 3px solid #ef4444 !important;
        }

        .notification-item.stock-alert-low {
            background: #fffbeb !important;
            border-left: 3px solid #f59e0b !important;
        }

        .notification-item.unread {
            background: #eff6ff !important;
            border-left: 3px solid var(--app-primary) !important;
        }

        .notification-item .notification-icon-wrapper {
            width: 36px !important;
            height: 36px !important;
            background: #e8edf5 !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: var(--app-primary) !important;
            font-size: 16px !important;
            flex-shrink: 0 !important;
            position: relative !important;
        }

        .notification-dot {
            position: absolute !important;
            top: -3px !important;
            right: -3px !important;
            width: 10px !important;
            height: 10px !important;
            background: #ef4444 !important;
            border-radius: 50% !important;
            border: 2px solid white !important;
            animation: pulse-dot 1.5s infinite !important;
        }

        @keyframes pulse-dot {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }

        .notification-item .notification-content {
            flex: 1 !important;
            min-width: 0 !important;
        }

        .notification-item .notification-title {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            margin-bottom: 4px !important;
            flex-wrap: wrap !important;
            gap: 5px !important;
        }

        .notification-item .notification-title a {
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            text-decoration: none !important;
        }

        .notification-item .notification-title a:hover {
            color: #273772 !important;
        }

        .notification-type {
            font-size: 10px !important;
            padding: 2px 10px !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
        }

        .type-permission {
            background: #dbeafe !important;
            color: #273772 !important;
        }

        .type-demission {
            background: #fce4ec !important;
            color: #c62828 !important;
        }

        .type-leave {
            background: #e0f2fe !important;
            color: #0369a1 !important;
        }

        .type-stock-out {
            background: #fee2e2 !important;
            color: #b91c1c !important;
        }

        .type-stock-low {
            background: #fef3c7 !important;
            color: #b45309 !important;
        }

        .type-leave i {
            margin-right: 3px !important;
        }

        .notification-details {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 12px !important;
            font-size: 12px !important;
            color: #64748b !important;
        }

        .notification-details span i {
            margin-right: 4px !important;
            color: #273772 !important;
        }

        .notification-status-badge {
            display: inline-block !important;
            font-size: 10px !important;
            padding: 2px 10px !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
            margin-top: 4px !important;
        }

        .status-pending {
            background: #fef3c7 !important;
            color: #d97706 !important;
        }

        .status-approve {
            background: #d1fae5 !important;
            color: #065f46 !important;
        }

        .status-completed {
            background: #dbeafe !important;
            color: #273772 !important;
        }

        .status-disapprove {
            background: #fce4ec !important;
            color: #c62828 !important;
        }

        /* Bouton marquer comme lu */
        .btn-mark-read-single {
            position: absolute !important;
            right: 10px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            background: none !important;
            border: none !important;
            color: #94a3b8 !important;
            opacity: 0 !important;
            transition: all 0.3s ease !important;
            cursor: pointer !important;
            padding: 5px !important;
        }

        .notification-item:hover .btn-mark-read-single {
            opacity: 1 !important;
        }

        .btn-mark-read-single:hover {
            color: #22c55e !important;
            transform: translateY(-50%) scale(1.2) !important;
        }

        /* Historique */
        .history-list .history-item {
            background: #fafbfc !important;
            border-left: 3px solid #94a3b8 !important;
        }

        .history-list .history-item:hover {
            background: #f1f3f4 !important;
        }

        .history-icon i {
            color: #22c55e !important;
        }

        .notification-read-at {
            font-size: 11px !important;
            color: #94a3b8 !important;
            margin-top: 3px !important;
        }

        .notification-read-at i {
            margin-right: 4px !important;
        }

        /* Pied du dropdown */
        .notification-footer {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 10px 15px !important;
            border-top: 1px solid #f0f2f5 !important;
            background: #fafbfc !important;
            border-radius: 0 0 12px 12px !important;
        }

        .notification-footer a {
            font-size: 12px !important;
            color: #273772 !important;
            text-decoration: none !important;
            font-weight: 500 !important;
            transition: all 0.2s ease !important;
        }

        .notification-footer a:hover {
            color: #273772 !important;
            text-decoration: underline !important;
        }

        .notification-footer .btn-mark-read {
            color: #ef4444 !important;
        }

        .notification-footer .btn-mark-read:hover {
            color: #dc2626 !important;
        }

        /* État vide */
        .empty-notifications {
            text-align: center !important;
            padding: 40px 20px !important;
            color: #94a3b8 !important;
        }

        .empty-notifications i {
            font-size: 48px !important;
            margin-bottom: 15px !important;
            display: block !important;
            color: #22c55e !important;
        }

        .empty-notifications p {
            font-size: 16px !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            margin: 0 0 5px 0 !important;
        }

        .empty-notifications span {
            font-size: 13px !important;
            color: #94a3b8 !important;
        }

        .notification-loading {
            text-align: center !important;
            padding: 30px !important;
            color: #94a3b8 !important;
        }

        .notification-loading i {
            font-size: 24px !important;
            margin-right: 10px !important;
            color: #273772 !important;
        }

        .assistant-chat-launcher {
            position: relative !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .assistant-chat-launcher > a {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #273772 !important;
            font-size: 18px !important;
            width: 40px !important;
            height: 40px !important;
            border-radius: 50% !important;
            transition: all 0.3s ease !important;
            background: rgba(39, 55, 114, 0.06) !important;
            border: 1px solid rgba(39, 55, 114, 0.12) !important;
        }

        .assistant-chat-launcher > a:hover,
        .assistant-chat-launcher > a:focus {
            background: linear-gradient(135deg, #e0ecff, #f4f8ff) !important;
            color: #1d4ed8 !important;
            transform: translateY(-1px) scale(1.02) !important;
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.18) !important;
        }

        .assistant-chat-popup {
            position: fixed !important;
            right: 22px !important;
            bottom: 22px !important;
            width: min(420px, calc(100vw - 24px)) !important;
            height: min(620px, calc(100vh - 120px)) !important;
            background: #fff !important;
            border-radius: 18px !important;
            box-shadow: 0 25px 60px rgba(15, 23, 42, 0.25) !important;
            border: 1px solid rgba(148, 163, 184, 0.28) !important;
            z-index: 2147483647 !important;
            display: none !important;
            overflow: hidden !important;
        }

        .assistant-chat-popup.is-open {
            display: block !important;
        }

        .assistant-chat-header {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 12px 16px !important;
            background: linear-gradient(135deg, #1d4ed8, #2563eb) !important;
            color: #fff !important;
            font-weight: 700 !important;
        }

        .assistant-chat-header-title {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            font-size: 15px !important;
        }

        .assistant-chat-close {
            background: rgba(255,255,255,0.12) !important;
            color: #fff !important;
            border: none !important;
            border-radius: 8px !important;
            width: 32px !important;
            height: 32px !important;
            font-size: 16px !important;
            cursor: pointer !important;
        }

        .assistant-chat-embedded {
            height: calc(100% - 58px) !important;
            overflow: auto !important;
            background: #f7f9fc !important;
        }

        .assistant-chat-embedded .content-wrapper,
        .assistant-chat-embedded .content-header,
        .assistant-chat-embedded .content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: none !important;
            background: transparent !important;
        }

        .assistant-chat-embedded .content-header {
            display: none !important;
        }

        .assistant-chat-embedded .content {
            padding: 0 12px 12px !important;
        }

        .assistant-chat-embedded .chat-container {
            max-width: none !important;
            margin: 0 !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            min-height: 100% !important;
        }

        /* Icône unique de notification */
        .unified-notification > a .fa-bell {
            color: #273772 !important;
            font-size: 18px !important;
            transition: all 0.3s ease !important;
        }

        .unified-notification:hover > a .fa-bell {
            color: #273772 !important;
            transform: scale(1.1) !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .notification-dropdown {
                width: 340px !important;
                right: -80px !important;
            }

            .notification-item {
                padding: 10px 12px !important;
            }
        }

        @media (max-width: 480px) {
            .notification-dropdown {
                width: 300px !important;
                right: -100px !important;
            }
        }

        /* ===== STYLES EXISTANTS ===== */
        .school-name-3d {
            position: relative;
            display: inline-block;
            font-family: 'Arial Black', 'Impact', sans-serif;
            font-size: 1.5rem;
            font-weight: 900;
            color: black;
            text-transform: uppercase;
            transform: perspective(500px) rotateX(10deg);
            animation: float 3s ease-in-out infinite;
            padding: 10px 20px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        @media (max-width: 768px) {
            .school-name-3d {
                font-size: 1.8rem;
                padding: 8px 15px;
            }
        }

        @media (max-width: 480px) {
            .school-name-3d {
                font-size: 1.5rem;
                padding: 5px 10px;
            }
        }

        .main-header, .main-header .navbar {
            background: linear-gradient(90deg, white, white);
            color: black !important;
        }

        .main-header .logo {
            background-color: transparent !important;
            color: black !important;
        }

        .navbar-nav > li > a, .sidebar-session {
            color: black !important;
        }

        .navbar-nav > li > a:hover {
            color: #FFD700 !important;
        }

        .cal15 .fa-calendar {
            color: var(--app-primary) !important;
        }

        .todo-indicator {
            background: linear-gradient(135deg, #EF4444, #DC2626) !important;
            color: white !important;
            font-size: 11px !important;
            font-weight: bold !important;
            padding: 2px 6px !important;
            border-radius: 10px !important;
            position: absolute !important;
            top: 8px !important;
            right: 8px !important;
            min-width: 18px !important;
            height: 18px !important;
            text-align: center !important;
            line-height: 14px !important;
            border: 2px solid white !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
            animation: pulse 2s infinite !important;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        .todoview.plr10.ssnoti {
            background-color: #F3F4F6 !important;
            color: #374151 !important;
            font-weight: 500 !important;
            border-bottom: 1px solid #E5E7EB !important;
            padding: 12px 15px !important;
        }

        .todoview.plr10.ssnoti a {
            color: var(--app-primary) !important;
            font-weight: 500 !important;
            text-decoration: none !important;
        }

        .todoview.plr10.ssnoti a:hover {
            color: var(--app-primary) !important;
            text-decoration: underline !important;
        }

        .todolist {
            max-height: 300px !important;
            overflow-y: auto !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .todolist li {
            padding: 10px 15px !important;
            border-bottom: 1px solid #F3F4F6 !important;
            transition: background 0.2s ease !important;
        }

        .todolist li:hover {
            background-color: #F9FAFB !important;
        }

        .todolist .checkbox {
            margin: 0 !important;
        }

        .todolist .checkbox label {
            color: #1F2937 !important;
            font-weight: normal !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
        }

        .todolist input[type="checkbox"] {
            margin-right: 10px !important;
            width: 16px !important;
            height: 16px !important;
            accent-color: #10B981 !important;
        }

        .alert-sql-mode {
            position: fixed !important;
            top: 60px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            z-index: 9999 !important;
            min-width: 300px !important;
            max-width: 500px !important;
            background: #ef4444 !important;
            color: white !important;
            border: none !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2) !important;
            animation: slideDown 0.3s ease !important;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        /* ===== HEADER MODERN UI ===== */
        .main-header {
            box-shadow: 0 8px 20px rgba(15, 45, 78, 0.12);
            border-bottom: 1px solid #d9e6f6;
        }

        .main-header,
        .main-header .navbar {
            background: linear-gradient(135deg, #f8fbff 0%, #edf4ff 100%) !important;
            color: #143a62 !important;
        }

        .main-header .logo {
            background: linear-gradient(135deg, var(--app-primary-dark) 0%, var(--app-primary) 100%) !important;
            border-right: 1px solid rgba(255, 255, 255, 0.12);
            transition: all 0.25s ease;
        }

        .main-header .logo:hover {
            filter: brightness(1.05);
        }

        .main-header .logo .logo-mini,
        .main-header .logo .logo-lg {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 50px;
        }

        .main-header .logo img {
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.12));
        }

        .main-header .sidebar-toggle {
            color: var(--app-primary) !important;
            border-right: 1px solid #d8e5f5;
            transition: all 0.25s ease;
        }

        .main-header .sidebar-toggle:hover {
            background: #ffffff !important;
            color: var(--app-primary-dark) !important;
        }

        /* ===== NOUVELLE STRUCTURE DE BARRE (flex, alignée, responsive) ===== */
        .main-header .navbar {
            display: flex !important;
            align-items: center !important;
            min-height: 60px;
            padding: 0 !important;
        }

        .header-row {
            display: flex;
            align-items: center;
            flex: 1 1 auto;
            min-width: 0;
            gap: 16px;
            padding: 6px 18px;
        }

        .header-brand {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            margin-left: 48px;
        }

        .school-name-3d.header-badge {
            margin: 0 !important;
            background: linear-gradient(135deg, var(--app-primary) 0%, var(--app-primary-light) 100%) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 18px rgba(27, 79, 128, 0.25);
            transform: none;
            animation: none;
            font-size: 1.1rem;
            font-weight: 700;
            padding: 9px 18px;
            white-space: nowrap;
        }

        .header-search-wrap {
            flex: 1 1 auto;
            display: flex;
            justify-content: center;
            min-width: 0;
        }

        .header-employee-search {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 280px; /* Réduit de 380px à 280px */
            padding: 0;
        }

        .header-employee-search form {
            width: 100%;
            margin: 0;
        }

        .header-employee-search .input-group {
            display: flex;
            align-items: center;
            background: #f8fbff;
            border: 1px solid #d9e6f6;
            border-radius: 10px; /* Légèrement réduit */
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(15, 45, 78, 0.08);
            width: 100%;
        }

        .header-employee-search .input-group-addon {
            background: transparent;
            border: none;
            color: var(--app-primary);
            padding: 0 10px; /* Réduit de 12px à 10px */
            font-size: 13px; /* Réduit de 14px à 13px */
            display: flex;
            align-items: center;
        }

        .header-employee-search input {
            border: none;
            background: transparent;
            box-shadow: none;
            height: 34px; /* Réduit de 38px à 34px */
            width: 100%;
            color: #1f2d3d;
            font-size: 12px; /* Réduit de 13px à 12px */
            padding: 0 8px 0 0; /* Réduit de 12px à 8px */
        }

        .header-employee-search input:focus {
            outline: none;
        }

        .header-actions {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            margin-left: auto;
        }

        .header-actions .navbar-custom-menu {
            display: flex;
            align-items: center;
        }

        .header-actions .headertopmenu {
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .header-actions .headertopmenu > li {
            display: flex;
            align-items: center;
        }

        .header-actions .langdiv {
            display: flex;
            align-items: center;
            margin-right: 4px;
        }

        .header-actions .user-menu {
            display: flex;
            align-items: center;
        }

        @media (max-width: 991px) {
            .header-row {
                flex-wrap: wrap;
                row-gap: 10px;
                padding: 10px 14px;
            }

            .header-brand {
                order: 1;
            }

            .header-actions {
                order: 2;
                margin-left: auto;
            }

            .header-search-wrap {
                order: 3;
                width: 100%;
                justify-content: flex-start;
            }

            .header-employee-search {
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .school-name-3d.header-badge {
                font-size: 0.95rem;
                padding: 7px 12px;
            }
        }

        .headertopmenu > li > a {
            color: var(--app-primary) !important;
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            transition: all 0.25s ease;
        }

        .headertopmenu > li > a:hover {
            color: var(--app-primary-dark) !important;
            background: #ffffff !important;
            box-shadow: 0 6px 14px rgba(23, 67, 107, 0.12);
            transform: translateY(-1px);
        }

        .navbar-custom-menu .user-menu > a {
            border-radius: 14px;
            margin-left: 6px;
            background: #ffffff;
            box-shadow: 0 6px 14px rgba(20, 58, 98, 0.1);
            display: inline-flex;
            align-items: center;
            padding: 6px 8px !important;
        }

        .topuser-image {
            border: 2px solid #e2ecf9;
            box-shadow: 0 2px 8px rgba(20, 58, 98, 0.16);
        }

        .dropdown-user.menuboxshadow {
            border-radius: 12px !important;
            border: 1px solid #d8e5f6 !important;
            box-shadow: 0 12px 28px rgba(20, 58, 98, 0.15) !important;
        }

        .langdiv .bootstrap-select > .dropdown-toggle {
            border: 1px solid #d4e2f3 !important;
            border-radius: 10px !important;
            background: #ffffff !important;
            min-height: 36px;
        }

        /* Couleur primaire globale des headers et composants principaux */
        .content-header h1,
        .content-header h1 i {
            color: var(--app-primary) !important;
        }

        .content-header h1 .global-back-btn {
            margin-left: 10px;
            border: 1px solid #cfe0f3;
            border-radius: 8px;
            color: var(--app-primary) !important;
            background: #ffffff;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            transition: all 0.2s ease;
        }

        .content-header h1 .global-back-btn:hover {
            color: #ffffff !important;
            background: var(--app-primary);
            border-color: var(--app-primary);
        }

        .box.box-primary {
            border-top-color: var(--app-primary) !important;
        }

        .box.box-primary > .box-header {
            background: linear-gradient(135deg, var(--app-primary-dark) 0%, var(--app-primary) 100%) !important;
            color: #ffffff !important;
            border-bottom: none !important;
        }

        .box.box-primary > .box-header,
        .box.box-primary > .box-header .box-title,
        .box.box-primary > .box-header a,
        .box.box-primary > .box-header i {
            color: #ffffff !important;
        }

        .nav-tabs-custom > .nav-tabs > li.active {
            border-top-color: var(--app-primary) !important;
        }

        .nav-tabs-custom > .nav-tabs > li.active > a,
        .nav-tabs-custom > .nav-tabs > li.active:hover > a {
            color: var(--app-primary) !important;
        }

        .btn-primary,
        .btn-info {
            background-color: var(--app-primary) !important;
            border-color: var(--app-primary) !important;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-info:hover,
        .btn-info:focus {
            background-color: var(--app-primary-dark) !important;
            border-color: var(--app-primary-dark) !important;
        }

        @media (max-width: 991px) {
            .headertopmenu > li > a {
                width: 38px;
                height: 38px;
            }
        }

        /* ===== AFFICHAGE DU PAYS DANS LE HEADER ===== */

/* Badge pays sur l'avatar */
.user-country-badge {
    position: absolute !important;
    bottom: 0 !important;
    right: 0 !important;
    background: white !important;
    border-radius: 50% !important;
    padding: 2px !important;
    font-size: 14px !important;
    border: 2px solid white !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    line-height: 1 !important;
    z-index: 2 !important;
}

.user-country-badge .flag-icon {
    border-radius: 50% !important;
    display: block !important;
}

/* Affichage du pays à côté du nom */
.user-country-display-header {
    display: inline-block !important;
    background: #f0f4ff !important;
    padding: 2px 10px !important;
    border-radius: 15px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #273772 !important;
    margin-left: 8px !important;
    border: 1px solid #d4e2f3 !important;
    vertical-align: middle !important;
}

.user-country-display-header .flag-icon {
    margin-right: 5px !important;
    font-size: 14px !important;
}

.user-country-display-header small {
    font-weight: 400 !important;
}

/* Animation d'apparition du pays */
@keyframes fadeInCountry {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.user-country-display-header {
    animation: fadeInCountry 0.3s ease forwards;
}

.user-country-badge {
    animation: fadeInCountry 0.3s ease forwards;
}

/* Responsive */
@media (max-width: 768px) {
    .user-country-display-header {
        font-size: 10px !important;
        padding: 1px 6px !important;
        margin-left: 4px !important;
    }
    
    .user-country-display-header .flag-icon {
        font-size: 10px !important;
        margin-right: 3px !important;
    }
    
    .user-country-display-header small {
        display: none !important;
    }
    
    .user-country-badge {
        font-size: 10px !important;
        padding: 1px !important;
    }
}

@media (max-width: 480px) {
    .user-country-display-header {
        font-size: 9px !important;
        padding: 1px 5px !important;
        display: inline-block !important;
    }
    
    .user-country-display-header .flag-icon {
        font-size: 9px !important;
    }
}
    </style>
</head>
<body class="hold-transition skin-blue fixed sidebar-mini">

<?php
if (!$this->config->item('SSLK') == "") {
    ?>
    <!--<div class="topaleart">
        <div class="slidealert">
            <div class="alert alert-dismissible topaleart-inside">
                <p class="palert"><strong>Alert!</strong> You are using unregistered version of Smart School. Please <a  href="#" class="purchasemodal">click here</a> to register your purchase code for Smart School.</p>
            </div></div>
    </div>-->
    <?php
}
?>

<script>
    function collapseSidebar() {
        if (Boolean(sessionStorage.getItem('sidebar-toggle-collapsed'))) {
            sessionStorage.setItem('sidebar-toggle-collapsed', '');
        } else {
            sessionStorage.setItem('sidebar-toggle-collapsed', '1');
        }
    }

    function checksidebar() {
        if (Boolean(sessionStorage.getItem('sidebar-toggle-collapsed'))) {
            var body = document.getElementsByTagName('body')[0];
            body.className = body.className + ' sidebar-collapse';
        }
    }
    checksidebar();
</script>

<div class="wrapper">
    <header class="main-header" id="alert">
        <a href="<?php echo base_url(); ?>admin/admin/dashboard" class="logo">
            <span class="logo-mini" style="background-color: white">
                <?php if (!empty($small_logo_url)) { ?>
                    <img src="<?php echo $small_logo_url; ?>" alt="<?php echo $this->customlib->getAppName(); ?>" style="max-height: 50px; width: auto;" />
                <?php } else { ?>
                    <img src="<?php echo $logo_url; ?>" alt="<?php echo $this->customlib->getAppName(); ?>" style="max-height: 50px; width: auto;" />
                <?php } ?>
            </span>
            <span class="logo-lg">
                <?php if (!empty($logo_url)) { ?>
                    <img src="<?php echo $logo_url; ?>" style="width: 53px; height: auto;" alt="<?php echo $this->customlib->getAppName(); ?>" />
                <?php } else { ?>
                    <span style="color: #273772; font-weight: bold; font-size: 18px;">
                        <?php echo $this->customlib->getAppName(); ?>
                    </span>
                <?php } ?>
            </span>
        </a>
        <nav class="navbar navbar-static-top" role="navigation">
            <a onclick="collapseSidebar()" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="header-row">
                <div class="header-brand">
                    <div class="school-name-3d header-badge">
                        <?php
                        $schoolName = $this->setting_model->getCurrentSchoolName();
                        echo $schoolName;
                        ?>
                    </div>
                </div>

                <div class="header-search-wrap">
                    <div class="header-employee-search">
                        <form id="headerEmployeeSearchForm" method="post" action="javascript:void(0);" autocomplete="off">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text" id="headerEmployeeSearchInput" placeholder="Rechercher un employé..." aria-label="Rechercher un employé">
                            </div>
                        </form>
                    </div>
                </div>

                <div class="header-actions">
                    <div class="navbar-custom-menu">
                        <?php if($this->rbac->hasPrivilege('language_switcher','can_view')){ ?>
                            <div class="langdiv">
                                <select class="languageselectpicker" onchange="set_languages(this.value)" type="text" id="languageSwitcher">
                                    <?php $this->load->view('admin/language/languageSwitcher')?>
                                </select>
                            </div>
                        <?php } ?>

                        <ul class="nav navbar-nav headertopmenu">
                            <?php
                            if ($this->module_lib->hasActive('calendar_to_do_list')) {
                                if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_view')) { ?>
                                    <li class="cal15">
                                        <a data-placement="bottom" data-toggle="tooltip" title="<?php echo $this->lang->line('calendar') ?>" href="<?php echo base_url() ?>admin/calendar/events">
                                            <i class="fa fa-calendar"></i>
                                        </a>
                                    </li>
                                <?php }
                            } ?>

                            <!-- ============================================ -->
                            <!-- NOTIFICATIONS CENTRALISÉES -->
                            <!-- ============================================ -->
                            <li class="dropdown notification-icon unified-notification">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="globalNotificationToggle">
                                    <i class="fa fa-bell"></i>
                                    <span class="notification-badge" id="globalNotificationBadge" style="display: none;">0</span>
                                </a>
                                <ul class="dropdown-menu notification-dropdown unified-notification-dropdown">
                                    <div class="notification-header">
                                        <h4>
                                            <i class="fa fa-bell"></i>
                                            Notifications
                                            <span class="notification-count" id="globalNotificationCount">0</span>
                                        </h4>
                                        <div class="notification-category-tabs">
                                            <button type="button" class="category-tab active" data-category="enquiry" id="enquiryCategoryTab">
                                                Permissions
                                                <span class="notification-badge" id="enquiryNotificationBadge" style="display: none;">0</span>
                                            </button>
                                            <button type="button" class="category-tab" data-category="leave" id="leaveCategoryTab">
                                                Conges
                                                <span class="notification-badge" id="leaveNotificationBadge" style="display: none;">0</span>
                                            </button>
                                            <button type="button" class="category-tab" data-category="stock" id="stockCategoryTab">
                                                Stock
                                                <span class="notification-badge" id="stockNotificationBadge" style="display: none;">0</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="notification-category-panel enquiry-notification active" id="enquiryCategoryPanel">
                                        <div class="notification-section-header">
                                            <h5 class="notification-section-title">
                                                <span><i class="fa fa-bell"></i> Demandes d'admission</span>
                                                <span class="notification-count" id="enquiryNotificationCount">0</span>
                                            </h5>
                                            <div class="notification-tabs">
                                                <button type="button" class="tab-btn active" data-tab="new" id="tabNew">
                                                    <i class="fa fa-bell-o"></i> Nouvelles
                                                    <span class="tab-badge" id="newTabBadge">0</span>
                                                </button>
                                                <button type="button" class="tab-btn" data-tab="history" id="tabHistory">
                                                    <i class="fa fa-history"></i> Historique
                                                    <span class="tab-badge" id="historyTabBadge">0</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="notification-list-wrapper">
                                            <div class="tab-content active" id="tabNewContent">
                                                <div id="enquiryNotificationList">
                                                    <div class="notification-loading">
                                                        <i class="fa fa-spinner fa-spin"></i> Chargement...
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-content" id="tabHistoryContent">
                                                <div id="enquiryHistoryList">
                                                    <div class="notification-loading">
                                                        <i class="fa fa-spinner fa-spin"></i> Chargement...
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="notification-footer">
                                            <a href="<?php echo base_url(); ?>admin/enquiry" class="btn-view-all">
                                                <i class="fa fa-eye"></i> Voir toutes les demandes
                                            </a>
                                            <a href="#" id="markAllEnquiryRead" class="btn-mark-read">
                                                <i class="fa fa-check"></i> Tout marquer
                                            </a>
                                        </div>
                                    </div>

                                    <div class="notification-category-panel leave-notification" id="leaveCategoryPanel">
                                        <div class="notification-section-header">
                                            <h5 class="notification-section-title">
                                                <span><i class="fa fa-calendar-check-o"></i> Demandes de conge</span>
                                                <span class="notification-count" id="leaveNotificationCount">0</span>
                                            </h5>
                                            <div class="notification-tabs">
                                                <button type="button" class="tab-btn active" data-tab="leave-new" id="tabLeaveNew">
                                                    <i class="fa fa-bell-o"></i> Nouvelles
                                                    <span class="tab-badge" id="leaveNewTabBadge">0</span>
                                                </button>
                                                <button type="button" class="tab-btn" data-tab="leave-history" id="tabLeaveHistory">
                                                    <i class="fa fa-history"></i> Historique
                                                    <span class="tab-badge" id="leaveHistoryTabBadge">0</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="notification-list-wrapper">
                                            <div class="tab-content active" id="tabLeaveNewContent">
                                                <div id="leaveNotificationList">
                                                    <div class="notification-loading">
                                                        <i class="fa fa-spinner fa-spin"></i> Chargement...
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-content" id="tabLeaveHistoryContent">
                                                <div id="leaveHistoryList">
                                                    <div class="notification-loading">
                                                        <i class="fa fa-spinner fa-spin"></i> Chargement...
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="notification-footer">
                                            <a href="<?php echo base_url(); ?>admin/leaverequest/leaverequest" class="btn-view-all">
                                                <i class="fa fa-eye"></i> Voir toutes les demandes
                                            </a>
                                            <a href="#" id="markAllLeaveRead" class="btn-mark-read">
                                                <i class="fa fa-check"></i> Tout marquer
                                            </a>
                                        </div>
                                    </div>

                                    <div class="notification-category-panel stock-notification" id="stockCategoryPanel">
                                        <div class="notification-section-header">
                                            <h5 class="notification-section-title">
                                                <span><i class="fa fa-cubes"></i> Alertes stock</span>
                                                <span class="notification-count" id="stockNotificationCount">0</span>
                                            </h5>
                                            <span class="notification-section-subtitle">
                                                Produits presque en rupture et en rupture
                                            </span>
                                        </div>
                                        <div class="notification-list-wrapper">
                                            <div id="stockNotificationList">
                                                <div class="notification-loading">
                                                    <i class="fa fa-spinner fa-spin"></i> Chargement...
                                                </div>
                                            </div>
                                        </div>
                                        <div class="notification-footer">
                                            <a href="<?php echo base_url(); ?>admin/itemstock" class="btn-view-all">
                                                <i class="fa fa-eye"></i> Voir tout le stock
                                            </a>
                                            <span style="font-size: 12px; color: #64748b; font-weight: 500;">
                                                Seuils actifs
                                            </span>
                                        </div>
                                    </div>
                                </ul>
                            </li>
                            <li class="assistant-chat-launcher">
                                <a href="#" id="assistantChatTrigger" title="Assistant IA" aria-label="Assistant IA">
                                    <i class="fa fa-robot"></i>
                                </a>
                            </li>
                            <?php
                            if ($this->module_lib->hasActive('calendar_to_do_list')) {
                                if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_view')) { ?>
                                    <li class="dropdown" data-placement="bottom" data-toggle="tooltip" title="<?php echo $this->lang->line('task') ?>">
                                        <a href="#" class="dropdown-toggle todoicon" data-toggle="dropdown">
                                            <i class="fa fa-check-square-o"></i>
                                            <?php
                                            $userdata = $this->customlib->getUserData();
                                            $count = $this->customlib->countincompleteTask($userdata["id"]);
                                            if ($count > 0) { ?>
                                                <span class="todo-indicator"><?php echo $count ?></span>
                                            <?php } ?>
                                        </a>
                                        <ul class="dropdown-menu menuboxshadow">
                                            <li class="todoview plr10 ssnoti">
                                                <?php echo $this->lang->line('today_you_have'); ?> <?php echo $count; ?> <?php echo $this->lang->line('pending_task'); ?>
                                                <a href="<?php echo base_url() ?>admin/calendar/events" class="pull-right pt0"><?php echo $this->lang->line('view'); ?> <?php echo $this->lang->line('all'); ?></a>
                                            </li>
                                            <li>
                                                <ul class="todolist">
                                                    <?php
                                                    $tasklist = $this->customlib->getincompleteTask($userdata["id"]);
                                                    foreach ($tasklist as $key => $value) { ?>
                                                        <li>
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input type="checkbox" id="newcheck<?php echo $value["id"] ?>" onclick="markc('<?php echo $value["id"] ?>')" name="eventcheck" value="<?php echo $value["id"]; ?>">
                                                                    <?php echo $value["event_title"] ?>
                                                                </label>
                                                            </div>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                <?php }
                            } ?>

                            <?php if ($this->module_lib->hasActive('chat')) {
                                if($this->rbac->hasPrivilege('chat','can_view')){ ?>
                                    <li class="cal15">
                                        <a data-placement="bottom" data-toggle="tooltip" title="" href="<?php echo base_url()?>admin/chat" data-original-title="<?php echo $this->lang->line('chat')?>" class="todoicon">
                                            <i class="fa fa-whatsapp"></i>
                                        </a>
                                    </li>
                                <?php }
                            } ?>

                            <?php
                            $file = "";
                            $result = $this->customlib->getUserData();
                            $image = $result["image"];
                            $role = $result["user_type"];
                            $id = $result["id"];
                            if (!empty($image)) {
                                $file = "uploads/staff_images/" . $image;
                            } else {
                                if($result['gender']=='Female'){
                                    $file = "uploads/staff_images/default_female.jpg";
                                } else {
                                    $file = "uploads/staff_images/default_male.jpg";
                                }
                            }
                            ?>
                            <!-- ===== PROFIL UTILISATEUR AVEC PAYS ===== -->
                            <li class="dropdown user-menu">
                                <a class="dropdown-toggle" style="position: relative;" data-toggle="dropdown" href="#" aria-expanded="false">
                                    <img src="<?php echo base_url() . $file; ?>" class="topuser-image" alt="User Image">
                                    <!-- Badge pays sur l'avatar -->
                                    <?php if (isset($user_country) && !empty($user_country['country_code']) && $user_country['country_code'] != '??'): ?>
                                        <!--<span class="user-country-badge">
                                            <i class="flag-icon flag-icon-<?php echo strtolower($user_country['country_code']); ?>"></i>
                                        </span>-->
                                    <?php endif; ?>
                                </a>
                                <ul class="dropdown-menu dropdown-user menuboxshadow">
                                    <li>
                                        <div class="sstopuser">
                                            <div class="ssuserleft">
                                                <a href="<?php echo base_url() . "admin/staff/profile/" . $id ?>">
                                                    <img src="<?php echo base_url() . $file; ?>" alt="User Image">
                                                </a>
                                            </div>
                                            <div class="sstopuser-test">
                                                <h4 style="color: #0c0c0c" class="text-capitalize">
                                                    <?php echo $this->customlib->getAdminSessionUserName(); ?>
                                                    <!-- AFFICHAGE DU PAYS À CÔTÉ DU NOM -->
                                                    <?php if (isset($user_country) && !empty($user_country['country']) && $user_country['country'] != 'Non détecté'): ?>
                                                        <span class="user-country-display-header">
                                                            <i class="flag-icon flag-icon-<?php echo strtolower($user_country['country_code']); ?>"></i>
                                                            <?php echo $user_country['country']; ?>
                                                            <?php if (!empty($user_country['city'])): ?>
                                                                <small style="color: #95a5a6; font-size: 11px;">(<?php echo $user_country['city']; ?>)</small>
                                                            <?php endif; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </h4>
                                                <h5 style="color: black"><?php echo $role; ?></h5>
                                            </div>
                                            <div class="divider"></div>
                                            <div class="sspass">
                                                <a href="<?php echo base_url() . "admin/staff/profile/" . $id ?>" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('my_profile'); ?>">
                                                    <i class="fa fa-user"></i><?php echo $this->lang->line('profile'); ?>
                                                </a>
                                                <a class="pl25" href="<?php echo base_url(); ?>admin/admin/changepass" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('change_password'); ?>">
                                                    <i class="fa fa-key"></i><?php echo $this->lang->line('password'); ?>
                                                </a>
                                                <a class="pull-right" href="<?php echo base_url(); ?>site/logout">
                                                    <i class="fa fa-sign-out fa-fw"></i><?php echo $this->lang->line('logout'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div id="assistantChatPopup" class="assistant-chat-popup" aria-live="polite" aria-label="Chat assistant">
        <div class="assistant-chat-header">
            <div class="assistant-chat-header-title">
                <i class="fa fa-robot"></i>
                <span>Assistant IA</span>
            </div>
            <button type="button" class="assistant-chat-close" id="assistantChatClose" aria-label="Fermer">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="assistant-chat-embedded">
            <?php $this->load->view('admin/intelligence/chat'); ?>
        </div>
    </div>

    <?php $this->load->view('layout/sidebar'); ?>

    <!-- Alert ONLY_FULL_GROUP_BY -->
    <?php if ($mysqlVersion && $sqlMode && strpos($sqlMode->mode, 'ONLY_FULL_GROUP_BY') !== false) { ?>
        <div class="alert alert-sql-mode alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <i class="fa fa-exclamation-triangle mr-2"></i>
            Smart School may not work properly because ONLY_FULL_GROUP_BY is enabled, consult with your hosting provider to disable ONLY_FULL_GROUP_BY in sql_mode configuration.
        </div>
    <?php } ?>

    <script>
        // =============================================
        // NOTIFICATIONS DES DEMANDES D'ADMISSION
        // =============================================
        $(document).ready(function() {
            var $assistantPopup = $('#assistantChatPopup');
            var $assistantTrigger = $('#assistantChatTrigger');
            var $assistantClose = $('#assistantChatClose');

            function toggleAssistantPopup(forceOpen) {
                var shouldOpen = typeof forceOpen === 'boolean' ? forceOpen : !$assistantPopup.hasClass('is-open');
                $assistantPopup.toggleClass('is-open', shouldOpen);
            }

            $assistantTrigger.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleAssistantPopup();
            });

            $assistantClose.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleAssistantPopup(false);
            });

            $(document).on('click', function(e) {
                if ($assistantPopup.hasClass('is-open') && !$(e.target).closest('#assistantChatPopup, #assistantChatTrigger').length) {
                    toggleAssistantPopup(false);
                }
            });

            // Bouton retour global dans chaque entete de page/menu
            (function addGlobalBackButtons() {
                var fallbackUrl = "<?php echo base_url('admin/admin/dashboard'); ?>";
                $('.content-header h1').each(function() {
                    var $title = $(this);
                    if ($title.find('.global-back-btn').length) {
                        return;
                    }

                    var $btn = $('<a href="#" class="btn btn-default btn-xs global-back-btn pull-right"><i class="fa fa-arrow-left"></i> Retour</a>');
                    $btn.on('click', function(e) {
                        e.preventDefault();
                        if (window.history.length > 1) {
                            window.history.back();
                        } else {
                            window.location.href = fallbackUrl;
                        }
                    });

                    $title.append($btn);
                });
            })();

            // Charger les notifications au chargement
            loadEnquiryNotifications();
            loadEnquiryHistory();

            // Charger les notifications de congé
            loadLeaveNotifications();
            loadLeaveHistory();

            // Rafraîchir toutes les 30 secondes
            setInterval(function() {
                loadEnquiryNotifications();
                loadLeaveNotifications();
                loadStockNotifications();
            }, 30000);

            // Garder le dropdown ouvert pendant les interactions internes
            $('.notification-dropdown').on('click', function(e) {
                e.stopPropagation();
            });

            // ===== GESTION DES CATÉGORIES =====
            $('.category-tab').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var category = $(this).data('category');

                $('.category-tab').removeClass('active');
                $(this).addClass('active');

                $('.notification-category-panel').removeClass('active');
                $('#' + category + 'CategoryPanel').addClass('active');

                // Forcer le dropdown à rester ouvert — Bootstrap peut parfois le fermer
                var $dropdownLi = $('#globalNotificationToggle').closest('.dropdown');
                if ($dropdownLi.length) {
                    $dropdownLi.addClass('open');
                    $('#globalNotificationToggle').attr('aria-expanded', 'true');
                }
            });

            // ===== GESTION DES ONGLETS POUR LES ADMISSIONS =====
            $('.tab-btn:not([data-tab^="leave-"])').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var tab = $(this).data('tab');

                $('#enquiryCategoryPanel .tab-btn:not([data-tab^="leave-"])').removeClass('active');
                $(this).addClass('active');

                // Ensure dropdown stays open after switching tabs
                var $dropdownLi2 = $('#globalNotificationToggle').closest('.dropdown');
                if ($dropdownLi2.length) {
                    $dropdownLi2.addClass('open');
                    $('#globalNotificationToggle').attr('aria-expanded', 'true');
                }

                $('#enquiryCategoryPanel .tab-content:not([id^="tabLeave"])').removeClass('active');
                $('#tab' + tab.charAt(0).toUpperCase() + tab.slice(1) + 'Content').addClass('active');
            });

            // ===== GESTION DES ONGLETS POUR LES CONGÉS =====
            $('.tab-btn[data-tab^="leave-"]').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var tab = $(this).data('tab');

                $('#leaveCategoryPanel .tab-btn[data-tab^="leave-"]').removeClass('active');
                $(this).addClass('active');

                $('#leaveCategoryPanel .tab-content[id^="tabLeave"]').removeClass('active');
                $('#tab' + tab.charAt(0).toUpperCase() + tab.slice(1) + 'Content').addClass('active');
            });

            function toggleNotificationBadge(selector, count) {
                var $badge = $(selector);

                if (count > 0) {
                    $badge.text(count > 9 ? '9+' : count).show().removeClass('zero');
                } else {
                    $badge.text('0').addClass('zero');
                    setTimeout(function() {
                        if (parseInt($badge.text(), 10) === 0) {
                            $badge.hide();
                        }
                    }, 3000);
                }
            }

            function refreshGlobalNotificationSummary() {
                var total =
                    (parseInt($('#enquiryNotificationCount').text(), 10) || 0) +
                    (parseInt($('#leaveNotificationCount').text(), 10) || 0) +
                    (parseInt($('#stockNotificationCount').text(), 10) || 0);

                toggleNotificationBadge('#globalNotificationBadge', total);
                $('#globalNotificationCount').text(total);
                $('.unified-notification > a .fa-bell').css('color', total > 0 ? '#EF4444' : '#273772');
            }

            // ===== CHARGER LES NOTIFICATIONS D'ADMISSION =====
            function loadEnquiryNotifications() {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/enquiry/get_enquiry_notifications',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            var unread = response.total_unread || 0;

                            toggleNotificationBadge('#enquiryNotificationBadge', unread);
                            $('#enquiryNotificationCount').text(unread);
                            $('#newTabBadge').text(unread);
                            $('#historyTabBadge').text(response.history_count || 0);

                            $('#enquiryNotificationList').html(response.html);
                            updateEnquiryCounters();
                        }
                    },
                    error: function() {
                        $('#enquiryNotificationList').html(
                            '<div class="empty-notifications">' +
                            '<i class="fa fa-exclamation-circle" style="color: #ef4444;"></i>' +
                            '<p>Erreur de chargement</p>' +
                            '</div>'
                        );
                    }
                });
            }

            // ===== CHARGER L'HISTORIQUE DES ADMISSIONS =====
            function loadEnquiryHistory() {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/enquiry/get_notification_history',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#enquiryHistoryList').html(response.html);
                        }
                    },
                    error: function() {
                        $('#enquiryHistoryList').html(
                            '<div class="empty-notifications">' +
                            '<i class="fa fa-exclamation-circle" style="color: #ef4444;"></i>' +
                            '<p>Erreur de chargement</p>' +
                            '</div>'
                        );
                    }
                });
            }

            // ===== CHARGER LES NOTIFICATIONS DE CONGÉ =====
            function loadLeaveNotifications() {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/leaverequest/get_leave_notifications',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            var unread = response.total_unread || 0;

                            toggleNotificationBadge('#leaveNotificationBadge', unread);
                            $('#leaveNotificationCount').text(unread);
                            $('#leaveNewTabBadge').text(unread);
                            $('#leaveHistoryTabBadge').text(response.history_count || 0);

                            $('#leaveNotificationList').html(response.html);
                            updateLeaveCounters();
                        }
                    },
                    error: function() {
                        $('#leaveNotificationList').html(
                            '<div class="empty-notifications">' +
                            '<i class="fa fa-exclamation-circle" style="color: #ef4444;"></i>' +
                            '<p>Erreur de chargement</p>' +
                            '</div>'
                        );
                    }
                });
            }

            // ===== CHARGER LES ALERTES STOCK =====
            function loadStockNotifications() {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/itemstock/get_stock_notifications',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            var total = response.total_alerts || 0;

                            toggleNotificationBadge('#stockNotificationBadge', total);
                            $('#stockNotificationCount').text(total);
                            $('#stockNotificationList').html(response.html);
                            updateStockCounters();
                        }
                    },
                    error: function() {
                        $('#stockNotificationList').html(
                            '<div class="empty-notifications">' +
                            '<i class="fa fa-exclamation-circle" style="color: #ef4444;"></i>' +
                            '<p>Erreur de chargement</p>' +
                            '</div>'
                        );
                    }
                });
            }

            // ===== CHARGER L'HISTORIQUE DES CONGÉS =====
            function loadLeaveHistory() {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/leaverequest/get_leave_history',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#leaveHistoryList').html(response.html);
                        }
                    },
                    error: function() {
                        $('#leaveHistoryList').html(
                            '<div class="empty-notifications">' +
                            '<i class="fa fa-exclamation-circle" style="color: #ef4444;"></i>' +
                            '<p>Erreur de chargement</p>' +
                            '</div>'
                        );
                    }
                });
            }

            // ===== MARQUER UNE NOTIFICATION D'ADMISSION COMME LUE =====
            $(document).on('click', '.enquiry-notification .btn-mark-read-single', function(e) {
                e.stopPropagation();
                var id = $(this).data('id');
                var item = $(this).closest('.notification-item');

                $.ajax({
                    url: '<?php echo base_url(); ?>admin/enquiry/mark_enquiry_read/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            item.removeClass('unread');
                            item.find('.notification-dot').remove();
                            item.find('.btn-mark-read-single').remove();
                            var remaining = response.remaining || 0;
                            updateEnquiryBadge(remaining);
                            updateEnquiryCounters();
                            refreshGlobalNotificationSummary();
                            loadEnquiryNotifications();
                            if (typeof successMsg !== 'undefined') {
                                successMsg('Demande marquée comme lue');
                            }
                        }
                    }
                });
            });

            // ===== MARQUER UNE NOTIFICATION DE CONGÉ COMME LUE =====
            $(document).on('click', '.leave-notification .btn-mark-read-single', function(e) {
                e.stopPropagation();
                var id = $(this).data('id');
                var item = $(this).closest('.notification-item');

                $.ajax({
                    url: '<?php echo base_url(); ?>admin/leaverequest/mark_leave_read/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            item.removeClass('unread');
                            item.find('.notification-dot').remove();
                            item.find('.btn-mark-read-single').remove();
                            var remaining = response.remaining || 0;
                            updateLeaveBadge(remaining);
                            updateLeaveCounters();
                            refreshGlobalNotificationSummary();
                            loadLeaveNotifications();
                            if (typeof successMsg !== 'undefined') {
                                successMsg('Demande de congé marquée comme lue');
                            }
                        }
                    }
                });
            });

            // ===== MARQUER TOUTES LES ADMISSIONS COMME LUES =====
            $('#markAllEnquiryRead').on('click', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/enquiry/mark_all_enquiry_read',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            updateEnquiryBadge(0);
                            updateEnquiryCounters();
                            loadEnquiryNotifications();
                            if (typeof successMsg !== 'undefined') {
                                successMsg(response.message);
                            }
                        }
                    }
                });
            });

            // ===== MARQUER TOUTES LES CONGÉS COMME LUES =====
            $('#markAllLeaveRead').on('click', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/leaverequest/mark_all_leave_read',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            updateLeaveBadge(0);
                            updateLeaveCounters();
                            loadLeaveNotifications();
                            if (typeof successMsg !== 'undefined') {
                                successMsg(response.message);
                            }
                        }
                    }
                });
            });

            // ===== METTRE À JOUR LES BADGES =====
            function updateEnquiryBadge(count) {
                toggleNotificationBadge('#enquiryNotificationBadge', count);
                $('#enquiryNotificationCount').text(count);
                $('#newTabBadge').text(count);
                refreshGlobalNotificationSummary();
            }

            function updateLeaveBadge(count) {
                toggleNotificationBadge('#leaveNotificationBadge', count);
                $('#leaveNotificationCount').text(count);
                $('#leaveNewTabBadge').text(count);
                refreshGlobalNotificationSummary();
            }

            function updateStockBadge(count) {
                toggleNotificationBadge('#stockNotificationBadge', count);
                $('#stockNotificationCount').text(count);
                refreshGlobalNotificationSummary();
            }

            // ===== METTRE À JOUR LES COMPTEURS EN TEMPS RÉEL =====
            function updateEnquiryCounters() {
                var unread = parseInt($('#enquiryNotificationCount').text(), 10) || 0;
                $('#enquiryCategoryTab').toggleClass('has-unread', unread > 0);
                refreshGlobalNotificationSummary();
            }

            function updateLeaveCounters() {
                var unread = parseInt($('#leaveNotificationCount').text(), 10) || 0;
                $('#leaveCategoryTab').toggleClass('has-unread', unread > 0);
                refreshGlobalNotificationSummary();
            }

            function updateStockCounters() {
                var total = parseInt($('#stockNotificationCount').text(), 10) || 0;
                $('#stockCategoryTab').toggleClass('has-unread', total > 0);
                refreshGlobalNotificationSummary();
            }

            // ===== CLIC SUR UNE NOTIFICATION D'ADMISSION =====
            $(document).on('click', '.enquiry-notification .notification-item', function() {
                var $item = $(this);
                var id = $item.data('id');
                if (id && $item.hasClass('unread')) {
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/enquiry/mark_enquiry_read/' + id,
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            if (response.status == 'success') {
                                $item.removeClass('unread');
                                $item.find('.notification-dot').remove();
                                var remaining = response.remaining || 0;
                                updateEnquiryBadge(remaining);
                                updateEnquiryCounters();
                                refreshGlobalNotificationSummary();
                                loadEnquiryNotifications();
                            }
                        }
                    });
                }
            });

            // Intercepter le clic sur le lien interne pour s'assurer que la notification est marquée avant navigation
            $(document).on('click', '.enquiry-notification .notification-item a', function(e) {
                var $link = $(this);
                var $item = $link.closest('.notification-item');
                var href = $link.attr('href');
                var id = $item.data('id');

                if (id && $item.hasClass('unread')) {
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/enquiry/mark_enquiry_read/' + id,
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            if (response.status == 'success') {
                                $item.removeClass('unread');
                                $item.find('.notification-dot').remove();
                                var remaining = response.remaining || 0;
                                updateEnquiryBadge(remaining);
                                updateEnquiryCounters();
                                refreshGlobalNotificationSummary();
                                // after marking, navigate to the link
                                window.location.href = href;
                            } else {
                                // if error, still navigate
                                window.location.href = href;
                            }
                        },
                        error: function() {
                            window.location.href = href;
                        }
                    });
                }
                // else allow default navigation
            });

            // ===== CLIC SUR UNE NOTIFICATION DE CONGÉ =====
            $(document).on('click', '.leave-notification .notification-item', function() {
                var $item = $(this);
                var id = $item.data('id');
                if (id && $item.hasClass('unread')) {
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/leaverequest/mark_leave_read/' + id,
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            if (response.status == 'success') {
                                $item.removeClass('unread');
                                $item.find('.notification-dot').remove();
                                var remaining = response.remaining || 0;
                                updateLeaveBadge(remaining);
                                updateLeaveCounters();
                                refreshGlobalNotificationSummary();
                                loadLeaveNotifications();
                            }
                        }
                    });
                }
            });

            // Intercepter le clic sur le lien interne pour les notifications de congé
            $(document).on('click', '.leave-notification .notification-item a', function(e) {
                var $link = $(this);
                var $item = $link.closest('.notification-item');
                var href = $link.attr('href');
                var id = $item.data('id');

                if (id && $item.hasClass('unread')) {
                    e.preventDefault();
                    $.ajax({
                        url: '<?php echo base_url(); ?>admin/leaverequest/mark_leave_read/' + id,
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            if (response.status == 'success') {
                                $item.removeClass('unread');
                                $item.find('.notification-dot').remove();
                                var remaining = response.remaining || 0;
                                updateLeaveBadge(remaining);
                                updateLeaveCounters();
                                refreshGlobalNotificationSummary();
                                window.location.href = href;
                            } else {
                                window.location.href = href;
                            }
                        },
                        error: function() {
                            window.location.href = href;
                        }
                    });
                }
            });
                            }
                        }
                    });
                }
            });

            // ===== INITIALISATION DES TOOLTIPS =====
            $('[data-toggle="tooltip"]').tooltip();

            // ===== INITIALISATION DES COMPTEURS =====
            updateEnquiryCounters();
            updateLeaveCounters();
            updateStockCounters();

            loadStockNotifications();

            // =============================================
            // FONCTIONS EXISTANTES
            // =============================================
            function defoult(id){
                var defoult = $('#languageSwitcher').val();
                $.ajax({
                    type: "POST",
                    url: base_url + "admin/language/default_language/"+id,
                    data: {},
                    success: function (data) {
                        successMsg("Status Change Successfully");
                        $('#languageSwitcher').html(data);
                    }
                });
                window.location.reload('true');
            }

            function set_languages(lang_id){
                $.ajax({
                    type: "POST",
                    url: base_url + "admin/language/user_language/"+lang_id,
                    data: {},
                    success: function (data) {
                        successMsg("Status Change Successfully");
                        window.location.reload('true');
                    }
                });
            }

            // Fermer une notification (existante)
            $(document).on('click', '.close_notice', function(e) {
                e.stopPropagation();
                var noticeId = $(this).data('noticeid');
                var notificationItem = $(this).closest('.notification-item');
                var currentBadge = $('#notificationBadge');
                var currentCount = parseInt(currentBadge.text()) || 0;

                $.ajax({
                    type: "POST",
                    url: base_url + "admin/notification/mark_as_read",
                    data: { notice_id: noticeId },
                    dataType: "json",
                    success: function(response) {
                        if (response.status == "success") {
                            notificationItem.fadeOut(300, function() {
                                $(this).remove();
                                var newCount = currentCount - 1;
                                if (newCount > 0) {
                                    currentBadge.text(newCount > 99 ? '99+' : newCount);
                                } else {
                                    currentBadge.remove();
                                }
                                if (typeof successMsg !== 'undefined') {
                                    successMsg(response.msg);
                                }
                            });
                        }
                    }
                });
            });

            $('#headerEmployeeSearchForm').on('submit', function(e) {
                e.preventDefault();
                var searchTerm = $.trim($('#headerEmployeeSearchInput').val());
                if (!searchTerm) {
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: base_url + 'admin/staff/quick_search',
                    data: { search_text: searchTerm },
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.status === 'success' && response.data && response.data.length > 0) {
                            var staff = response.data[0];
                            var profileUrl = staff.profile_url || (base_url + 'admin/staff/profile/' + staff.id);
                            window.location.href = profileUrl;
                            return;
                        }

                        if (typeof errorMsg !== 'undefined') {
                            errorMsg('Aucun employé trouvé pour cette recherche.');
                        } else {
                            alert('Aucun employé trouvé pour cette recherche.');
                        }
                    },
                    error: function() {
                        if (typeof errorMsg !== 'undefined') {
                            errorMsg('Erreur pendant la recherche de l\'employé.');
                        } else {
                            alert('Erreur pendant la recherche de l\'employé.');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>