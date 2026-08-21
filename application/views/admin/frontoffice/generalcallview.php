<!-- ============================================================
     PAGE : Gestion des appels téléphoniques
     DESCRIPTION : Interface moderne avec tableau de bord, filtres et formulaire design
     ============================================================ -->

<style>
    /* Variables et couleurs */
    :root {
        --primary-dark: #273772;
        --primary-light: #3b82f6;
        --primary-gradient: linear-gradient(135deg, #273772 0%, #1a2558 100%);
        --bg-light: #f8fafc;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --border-light: #e2e8f0;
        --shadow-soft: 0 8px 30px rgba(0, 0, 0, 0.06);
        --shadow-hover: 0 12px 40px rgba(0, 0, 0, 0.1);
        --shadow-modal: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 8px;
        --transition: all 0.25s ease;
    }

    .content-wrapper {
        background: #f1f5f9;
        padding: 20px 15px;
    }

    /* ========================================== */
    /* STATISTIQUES                               */
    /* ========================================== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        padding: 20px 24px;
        box-shadow: var(--shadow-soft);
        /*border-left: 5px solid var(--primary-light);*/
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-hover);
    }

    .stat-card .stat-info h4 {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
        margin: 0 0 4px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-card .stat-info .number {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        line-height: 1.2;
    }

    .stat-card .stat-icon {
        font-size: 32px;
        opacity: 0.7;
    }

    .stat-card:nth-child(1) { border-left-color: #3b82f6; }
    .stat-card:nth-child(1) .stat-icon { color: #3b82f6; }
    .stat-card:nth-child(2) { border-left-color: #10b981; }
    .stat-card:nth-child(2) .stat-icon { color: #10b981; }
    .stat-card:nth-child(3) { border-left-color: #f59e0b; }
    .stat-card:nth-child(3) .stat-icon { color: #f59e0b; }
    .stat-card:nth-child(4) { border-left-color: #8b5cf6; }
    .stat-card:nth-child(4) .stat-icon { color: #8b5cf6; }
    .stat-card:nth-child(5) { border-left-color: #ef4444; }
    .stat-card:nth-child(5) .stat-icon { color: #ef4444; }

    /* ========================================== */
    /* FILTRES                                    */
    /* ========================================== */
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 39px;
        align-items: center;
        margin-bottom: 24px;
        padding: 16px 21px;
        background: #ffffff;
        border-radius: var(--radius-md);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid var(--border-light);
    }

    .filter-bar .filter-group {
        margin-left: -18px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-bar .filter-group label {
        font-size: 13px;
        font-weight: 500;
        color: #475569;
        margin: 0;
        white-space: nowrap;
    }

    .filter-bar .filter-group select,
    .filter-bar .filter-group input {
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-light);
        padding: 6px 21px;
        font-size: 13px;
        background: #ffffff;
        transition: var(--transition);
        min-width: 140px;
        height: 38px;
    }

    .filter-bar .filter-group select:focus,
    .filter-bar .filter-group input:focus {
        outline: none;
        border-color: var(--primary-light);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    .filter-bar .btn-filter {
        background: var(--primary-dark);
        color: #ffffff;
        margin-left: -2%;
        margin-top: -76px;
        border: none;
        border-radius: var(--radius-sm);
        padding: 6px 20px;
        font-size: 13px;
        font-weight: 500;
        transition: var(--transition);
        cursor: pointer;
        height: 38px;
    }

    .filter-bar .btn-filter:hover {
        background: #1e2a5a;
        transform: translateY(-1px);
    }

    .filter-bar .btn-reset {
        margin-right:-15px ;
        margin-top: -1px;
        background: #e2e8f0;
        color: #475569;
        border: none;
        border-radius: var(--radius-sm);
        padding: 6px 18px;
        font-size: 13px;
        font-weight: 500;
        transition: var(--transition);
        cursor: pointer;
        height: 38px;
    }

    .filter-bar .btn-reset:hover {
        background: #cbd5e1;
    }

    /* ========================================== */
    /* BOUTONS D'EXPORTATION                      */
    /* ========================================== */
    .export-group {
        margin-right: -20px;
        margin-top: -116px;
        display: flex;
        gap: 8px;
        align-items: center;
        margin-left: auto;
    }

    .export-group .export-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-right: 4px;
    }

    .btn-export {
        border: none;
        border-radius: var(--radius-sm);
        padding: 7px 18px;
        font-size: 13px;
        font-weight: 500;
        transition: var(--transition);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    .btn-export::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
        opacity: 0;
        transition: var(--transition);
    }

    .btn-export:hover::after {
        opacity: 1;
    }

    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    .btn-excel {
        background: linear-gradient(135deg, #217346 0%, #1e7e3a 100%);
        color: #ffffff;
        box-shadow: 0 3px 12px rgba(33, 115, 70, 0.3);
    }

    .btn-excel:hover {
        box-shadow: 0 6px 24px rgba(33, 115, 70, 0.4);
        color: #ffffff;
    }

    .btn-excel i {
        font-size: 16px;
        color: #a8e6b8;
    }

    .btn-pdf {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: #ffffff;
        box-shadow: 0 3px 12px rgba(220, 38, 38, 0.3);
    }

    .btn-pdf:hover {
        box-shadow: 0 6px 24px rgba(220, 38, 38, 0.4);
        color: #ffffff;
    }

    .btn-pdf i {
        font-size: 16px;
        color: #fca5a5;
    }

    .export-divider {
        width: 1px;
        height: 30px;
        background: var(--border-light);
        margin: 0 4px;
    }

    /* ========================================== */
    /* CARTE PRINCIPALE                           */
    /* ========================================== */
    .card-modern {
        border: none;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        background: #ffffff;
        margin-bottom: 30px;
        overflow: hidden;
    }

    .card-modern .card-header {
        background: var(--primary-gradient);
        padding: 20px 24px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .card-modern .card-header h3 {
        color: #ffffff;
        font-size: 20px;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-modern .card-header h3 i {
        color: #60a5fa;
    }

    .card-modern .card-body {
        padding: 24px;
        background: var(--bg-light);
    }

    /* ========================================== */
    /* TABLE                                      */
    /* ========================================== */
    .table-modern {
        border-collapse: separate;
        border-spacing: 0 6px;
        width: 100%;
        margin-bottom: 0;
    }

    .table-modern thead th {
        background: #f1f5f9;
        color: var(--text-dark);
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 16px;
        border: none;
        border-bottom: 2px solid var(--border-light);
        white-space: nowrap;
    }

    .table-modern tbody td {
        /*background: #ffffff;*/
        padding: 12px 16px;
        border: none;
        border-bottom: 1px solid #eef2f6;
        vertical-align: middle;
        font-size: 14px;
        color: var(--text-dark);
    }

    .table-modern tbody tr:hover td {
        background: #f8fafc;
        transition: background 0.15s ease;
    }

    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    .badge-call-type {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    .badge-call-type.incoming { background: #dbeafe; color: #1d4ed8; }
    .badge-call-type.outgoing { background: #d1fae5; color: #059669; }
    .badge-call-type.missed { background: #fef3c7; color: #d97706; }
    .badge-call-type.other { background: #f1f5f9; color: #64748b; }

    /* ========================================== */
    /* BOUTONS D'ACTION                           */
    /* ========================================== */
    .btn-action-dropdown {
        background: transparent;
        border: 1px solid var(--border-light);
        border-radius: var(--radius-sm);
        padding: 6px 14px;
        color: #475569;
        font-size: 13px;
        transition: var(--transition);
    }

    .btn-action-dropdown:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .btn-action-dropdown .caret {
        margin-left: 6px;
    }

    .dropdown-menu.actions-menu {
        border-radius: var(--radius-md);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border-light);
        padding: 6px 0;
        min-width: 180px;
    }

    .dropdown-menu.actions-menu li a {
        padding: 8px 20px;
        font-size: 14px;
        color: var(--text-dark);
        transition: background 0.15s;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        text-decoration: none;
    }

    .dropdown-menu.actions-menu li a:hover {
        background: #f1f5f9;
    }

    .dropdown-menu.actions-menu li a.text-danger:hover {
        background: #fef2f2;
        color: #dc2626;
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 30px;
        padding: 6px 18px;
        font-size: 14px;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.5);
        text-decoration: none;
    }

    .btn-add-modern {
        background: white;
        border: none;
        color: var(--primary-dark);
        font-weight: 500;
        border-radius: 6px;
        padding: 8px 22px;
        font-size: 13px;
        transition: var(--transition);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-add-modern:hover {
        background: #2563eb;
        color: #fff;
        box-shadow: 0 6px 18px rgba(59, 130, 246, 0.4);
        transform: translateY(-2px);
    }

    /* ========================================== */
    /* MODAL - FORMULAIRE CHIC                    */
    /* ========================================== */
    .modal-chic .modal-content {
        border: none;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-modal);
        overflow: hidden;
    }

    .modal-chic .modal-header {
        background: var(--primary-gradient);
        padding: 20px 28px;
        border-bottom: none;
        position: relative;
    }

    .modal-chic .modal-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #60a5fa, #a78bfa, #60a5fa);
        background-size: 200% 100%;
        animation: shimmer 3s ease-in-out infinite;
    }

    @keyframes shimmer {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .modal-chic .modal-header .close {
        color: #ffffff;
        opacity: 0.7;
        font-size: 28px;
        text-shadow: none;
        transition: var(--transition);
        padding: 0;
        margin: -6px -8px -6px auto;
    }

    .modal-chic .modal-header .close:hover {
        opacity: 1;
        transform: rotate(90deg);
    }

    .modal-chic .modal-header h4 {
        color: #ffffff;
        font-weight: 600;
        font-size: 20px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modal-chic .modal-header h4 i {
        color: #60a5fa;
        font-size: 22px;
    }

    .modal-chic .modal-header .modal-subtitle {
        color: rgba(255,255,255,0.7);
        font-size: 13px;
        font-weight: 400;
        margin-top: 4px;
    }

    .modal-chic .modal-body {
        padding: 30px 28px 20px;
        background: #fafcff;
    }

    .modal-chic .form-group {
        margin-bottom: 20px;
        position: relative;
    }

    .modal-chic .form-group label {
        font-weight: 600;
        font-size: 14px;
        color: var(--text-dark);
        margin-bottom: 6px;
        display: block;
        letter-spacing: 0.3px;
    }

    .modal-chic .form-group label .text-danger {
        color: #ef4444;
        font-weight: 700;
    }

    .modal-chic .form-group label .field-icon {
        margin-right: 6px;
        color: var(--primary-light);
        font-size: 14px;
        width: 18px;
        display: inline-block;
    }

    .modal-chic .form-control {
        border: 2px solid #e2e8f0;
        border-radius: var(--radius-sm);
        padding: 10px 16px;
        font-size: 14px;
        transition: var(--transition);
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        height: 44px;
        color: var(--text-dark);
    }

    .modal-chic .form-control:focus {
        border-color: var(--primary-light);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
        outline: none;
    }

    .modal-chic .form-control::placeholder {
        color: #a0aec0;
        font-size: 13px;
    }

    .modal-chic textarea.form-control {
        height: auto;
        min-height: 80px;
        resize: vertical;
    }

    .modal-chic .radio-group {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        padding-top: 6px;
    }

    .modal-chic .radio-group .radio-inline {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 14px;
        color: var(--text-dark);
    }

    .modal-chic .radio-group .radio-inline input[type="radio"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--primary-light);
    }

    .modal-chic .call-type-badge {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    .modal-chic .call-type-badge.incoming { background: #dbeafe; color: #1d4ed8; }
    .modal-chic .call-type-badge.outgoing { background: #d1fae5; color: #059669; }
    .modal-chic .call-type-badge.missed { background: #fef3c7; color: #d97706; }

    .modal-chic .modal-footer {
        padding: 16px 28px 24px;
        border-top: 1px solid #eef2f6;
        background: #ffffff;
        border-radius: 0 0 var(--radius-lg) var(--radius-lg);
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .modal-chic .modal-footer .btn {
        border-radius: var(--radius-sm);
        padding: 10px 24px;
        font-weight: 500;
        font-size: 14px;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-width: 100px;
        justify-content: center;
    }

    .modal-chic .modal-footer .btn-default {
        background: #f1f5f9;
        color: #475569;
        border: none;
    }

    .modal-chic .modal-footer .btn-default:hover {
        background: #e2e8f0;
        transform: translateY(-1px);
    }

    .modal-chic .modal-footer .btn-warning {
        background: #fef3c7;
        color: #92400e;
        border: none;
    }

    .modal-chic .modal-footer .btn-warning:hover {
        background: #fde68a;
        transform: translateY(-1px);
    }

    .modal-chic .modal-footer .btn-success {
        background: var(--primary-gradient);
        color: #ffffff;
        border: none;
        box-shadow: 0 4px 12px rgba(39, 55, 114, 0.3);
    }

    .modal-chic .modal-footer .btn-success:hover {
        box-shadow: 0 6px 20px rgba(39, 55, 114, 0.4);
        transform: translateY(-2px);
    }

    .modal-chic .modal-footer .btn i {
        font-size: 15px;
    }

    .modal-chic .modal-content {
        animation: modalSlideUp 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes modalSlideUp {
        from {
            opacity: 0;
            transform: translateY(30px) scale(0.96);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Loading spinner */
    .spinner-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .spinner-overlay.active {
        display: flex;
    }

    .spinner-box {
        background: white;
        padding: 30px 40px;
        border-radius: 16px;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .spinner-box .fa-spinner {
        font-size: 40px;
        color: var(--primary-dark);
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .card-modern .card-header { flex-direction: column; align-items: stretch; }
        .table-modern thead th, .table-modern tbody td { padding: 10px 12px; font-size: 13px; }
        .btn-add-modern { width: 100%; text-align: center; }
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-bar .filter-group { width: 100%; }
        .filter-bar .filter-group select, .filter-bar .filter-group input { width: 100%; min-width: unset; }
        .export-group { margin-left: 0 !important; width: 100%; flex-wrap: wrap; }
        .export-group .btn-export { flex: 1; justify-content: center; padding: 8px 14px; font-size: 12px; }
        .export-group .export-label { width: 100%; text-align: center; }
        .export-divider { display: none; }
        .modal-chic .modal-body { padding: 20px 16px; }
        .modal-chic .modal-header { padding: 16px 20px; }
        .modal-chic .modal-footer { flex-direction: column; }
        .modal-chic .modal-footer .btn { width: 100%; }
    }

    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
        .export-group .btn-export { font-size: 11px; padding: 6px 12px; }
        .export-group .btn-export i { font-size: 13px; }
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">

                <!-- ========================================== -->
                <!-- CARTE PRINCIPALE                           -->
                <!-- ========================================== -->
                <div class="card-modern">
                    <div class="card-header">
                        <h3>
                            <i class="fa fa-phone-square"></i> Gestion des appels
                        </h3>
                        <div class="d-flex align-items-center gap-3" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url(); ?>admin/admin/hub" class="btn-back" title="Retour au tableau de bord">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <?php if ($this->rbac->hasPrivilege('journal_appels', 'can_add')) : ?>
                                <button type="button" class="btn-add-modern" onclick="openAddModal()">
                                    <i class="fa fa-phone" style="margin-right: 6px;"></i> Ajouter un appel
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- STATISTIQUES                               -->
                    <!-- ========================================== -->
                    <div class="stats-grid" style="padding: 20px 24px 0;">
                        <?php
                        $total_calls = count($call_list ?? []);
                        $incoming = 0;
                        $outgoing = 0;
                        $missed = 0;
                        $today_calls = 0;
                        $today = date('Y-m-d');

                        foreach ($call_list as $call) {
                            if ($call['call_type'] == 1) $incoming++;
                            elseif ($call['call_type'] == 2) $outgoing++;
                            elseif ($call['call_type'] == 3) $missed++;

                            if (isset($call['date']) && $call['date'] === $today) $today_calls++;
                        }
                        ?>
                        <div class="stat-card">
                            <div class="stat-info">
                                <h4><i class="fa fa-phone"></i> Total appels</h4>
                                <p class="number"><?php echo $total_calls; ?></p>
                            </div>

                        </div>

                        <div class="stat-card">
                            <div class="stat-info">
                                <h4><i class="fa fa-arrow-circle-down" style="color:#3b82f6;"></i> Appels entrants</h4>
                                <p class="number"><?php echo $incoming; ?></p>
                            </div>

                        </div>

                        <div class="stat-card">
                            <div class="stat-info">
                                <h4><i class="fa fa-arrow-circle-up" style="color:#10b981;"></i> Appels sortants</h4>
                                <p class="number"><?php echo $outgoing; ?></p>
                            </div>

                        </div>



                        <div class="stat-card">
                            <div class="stat-info">
                                <h4><i class="fa fa-calendar-day" style="color:#8b5cf6;"></i> Aujourd'hui</h4>
                                <p class="number"><?php echo $today_calls; ?></p>
                            </div>

                        </div>
                    </div>

                    <div class="card-body">
                        <?php if ($this->session->flashdata('msg')) : ?>
                           <!-- <div class="alert alert-success alert-dismissible fade in">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <i class="fa fa-check-circle"></i> <?php echo $this->session->flashdata('msg'); ?>
                            </div>-->
                        <?php endif; ?>
                        <?php if ($this->session->flashdata('error')) : ?>
                            <div class="alert alert-danger alert-dismissible fade in">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <i class="fa fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
                            </div>
                        <?php endif; ?>

                        <!-- ========================================== -->
                        <!-- BARRE DE FILTRES + EXPORT                 -->
                        <!-- ========================================== -->
                        <div class="filter-bar">
                            <div class="filter-group">
                               <label> Type d'appel :</label>
                                <select id="filterCallType" onchange="applyFilters()">
                                    <option value="">Tous</option>
                                    <option value="1">Entrant</option>
                                    <option value="2">Sortant</option>

                                </select>
                            </div>
                            <div class="filter-group">
                                <label> Période :</label>
                                <input type="date" id="filterDateFrom" onchange="applyFilters()" placeholder="Du">
                                <span style="color:#94a3b8;font-size:13px;">→</span>
                                <input type="date" id="filterDateTo" onchange="applyFilters()" placeholder="Au">
                            </div>
                            <div style="display:flex;gap:8px;margin-left:auto;flex-wrap:wrap;">
                               
                                <div class="export-group">
                                    <button class="btn-reset" onclick="resetFilters()"><i class="fa fa-undo"></i> Réinitialiser</button>

                                    <div class="export-divider"></div>
                                    <button class="btn-export btn-excel" onclick="exportData('excel')">
                                        <i class="fa fa-file-excel-o"></i> CSV
                                    </button>
                                    <button class="btn-export btn-pdf" onclick="exportData('pdf')">
                                        <i class="fa fa-file-pdf-o"></i> PDF
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- TABLE                                     -->
                        <!-- ========================================== -->
                        <div class="table-responsive">
                            <table class="table table-modern example" id="callsTable">
                                <thead>
                                <tr>
                                    <th>Type d'appel</th>
                                    <th><?php echo $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('phone'); ?></th>
                                    <th><?php echo $this->lang->line('date'); ?></th>
                                    <th>Durée</th>
                                    <th><?php echo $this->lang->line('description'); ?></th>
                                    <th><?php echo $this->lang->line('note'); ?></th>
                                    <th style="text-align: center; width: 100px;"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                                </thead>
                                <tbody id="callTableBody">
                                <?php if (empty($call_list)) : ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted" style="padding: 40px 0;">
                                            <i class="fa fa-phone" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
                                            <p style="font-size: 16px; color: #64748b;">Aucun appel enregistré</p>
                                            <p style="font-size: 13px; color: #94a3b8;">Commencez par ajouter un nouvel appel</p>
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($call_list as $call) :
                                        $callTypeLabel = '';
                                        $badgeClass = 'other';
                                        if ($call['call_type'] == 1) {
                                            $callTypeLabel = 'Entrant';
                                            $badgeClass = 'incoming';
                                        } elseif ($call['call_type'] == 2) {
                                            $callTypeLabel = 'Sortant';
                                            $badgeClass = 'outgoing';
                                        } elseif ($call['call_type'] == 3) {
                                            $callTypeLabel = 'Manqué';
                                            $badgeClass = 'missed';
                                        }
                                        ?>
                                        <tr data-call-type="<?php echo $call['call_type']; ?>"
                                            data-date="<?php echo $call['date']; ?>">
                                            <td>
                                                <span class="badge-call-type <?php echo $badgeClass; ?>">
                                                    <i class="fa <?php
                                                    echo $call['call_type'] == 1 ? 'fa-arrow-circle-down' :
                                                        ($call['call_type'] == 2 ? 'fa-arrow-circle-up' : 'fa-phone');
                                                    ?>"></i>
                                                    <?php echo $callTypeLabel; ?>
                                                </span>
                                            </td>

                                            <td><?php echo htmlspecialchars($call['name']); ?></td>
                                            <td><?php echo htmlspecialchars($call['contact']); ?></td>
                                            <td><?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($call['date'])); ?></td>
                                            <td><?php echo htmlspecialchars($call['call_dureation'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($call['description'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($call['note'] ?? ''); ?></td>
                                            <td style="text-align: center;">
                                                <div class="dropdown">
                                                    <button class="btn-action-dropdown dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu actions-menu dropdown-menu-right">

                                                        <li role="separator" class="divider"></li>

                                                        <?php if ($this->rbac->hasPrivilege('journal_appels', 'can_edit')) : ?>
                                                            <li>
                                                                <a onclick="openEditModal(<?php echo $call['id']; ?>)">
                                                                    <i class="fa fa-pencil" style="color: #3b82f6;"></i> Modifier
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>

                                                        <?php if ($this->rbac->hasPrivilege('journal_appels', 'can_delete')) : ?>
                                                            <li>
                                                                <a href="#" class="text-danger" onclick="confirmDelete(event, '<?php echo htmlspecialchars($call['name']); ?>', <?php echo $call['id']; ?>)">
                                                                    <i class="fa fa-trash"></i> Supprimer
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- ========================================== -->
<!-- SPINNER DE CHARGEMENT                      -->
<!-- ========================================== -->
<div class="spinner-overlay" id="spinnerOverlay">
    <div class="spinner-box">
        <i class="fa fa-spinner"></i>
        <p style="margin-top:15px;font-weight:500;color:#1e293b;">Chargement en cours...</p>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL - FORMULAIRE (AJOUT / MODIFICATION)  -->
<!-- ========================================== -->
<div id="callFormModal" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-md" style="max-width: 720px;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 id="formModalTitle">
                        <i class="fa fa-phone" id="formModalIcon"></i>
                        <span id="formTitleText">Nouvel appel</span>
                    </h4>
                    <div class="modal-subtitle" id="formModalSubtitle">Enregistrez un nouvel appel téléphonique</div>
                </div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form id="callForm" action="<?php echo site_url('admin/generalcall'); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" id="edit_id" value="">

                <!-- En-tête avec croix de fermeture -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 25px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 8px 8px 0 0;">
                    <h4 style="margin: 0; color: #1e293b; font-weight: 600; font-size: 18px;">
                        <i class="fa fa-phone" style="margin-right: 8px; color: #3B82F6;"></i> Nouvel appel
                    </h4>
                    <button type="button" onclick="fermerFormulaireAppel()" style="font-size: 30px; font-weight: 700; color: #64748b; background: transparent; border: none; cursor: pointer; padding: 0 8px; line-height: 1; transition: color 0.2s;"
                            onmouseover="this.style.color='#ef4444'"
                            onmouseout="this.style.color='#64748b'">
                        &times;
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Informations de l'appel -->
                    <div style="background: #f8fafc; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                        <h5 style="margin: 0 0 15px 0; color: #1e293b; font-size: 14px; font-weight: 600; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px;">
                            <i class="fa fa-info-circle" style="margin-right: 8px; color: #3B82F6;"></i> INFORMATIONS DE L'APPEL
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_name" name="name" placeholder="Nom complet" style="height: 43px; font-size: 13px;" required>
                                    <?php echo form_error('name', '<span class="text-danger">', '</span>'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Téléphone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_contact" name="contact" placeholder="Ex: 0123456789" style="height: 43px; font-size: 13px;" required>
                                    <?php echo form_error('contact', '<span class="text-danger">', '</span>'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Date <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control date" id="edit_date" name="date" readonly required style="height: 43px; font-size: 13px;">
                                    <?php echo form_error('date', '<span class="text-danger">', '</span>'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Durée de l'appel</label>
                                    <input type="text" class="form-control" id="edit_call_duration" name="call_dureation" placeholder="Ex: 5 min" style="height: 43px; font-size: 13px;">
                                    <?php echo form_error('call_dureation', '<span class="text-danger">', '</span>'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Type d'appel <span class="text-danger">*</span></label>
                                    <div class="radio-group" style="display: flex; gap: 20px; flex-wrap: wrap; padding-top: 4px;">
                                        <!-- Incoming = 1 -->
                                        <label class="radio-inline" style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 14px;">
                                            <input type="radio" name="call_type" value="1" <?php echo set_radio('call_type', '1'); ?> required>
                                            <span class="call-type-badge incoming">
                                    <i class="fa fa-arrow-circle-down"></i> Entrant
                                </span>
                                        </label>
                                        <!-- Outgoing = 2 -->
                                        <label class="radio-inline" style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 14px;">
                                            <input type="radio" name="call_type" value="2" <?php echo set_radio('call_type', '2'); ?> required>
                                            <span class="call-type-badge outgoing">
                                    <i class="fa fa-arrow-circle-up"></i> Sortant
                                </span>
                                        </label>
                                        <!-- Missed = 3 -->

                                    </div>
                                    <?php echo form_error('call_type', '<span class="text-danger">', '</span>'); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description et notes -->
                    <div style="background: #eff6ff; padding: 15px; border-radius: 6px; margin-bottom: 0; border-left: 4px solid #3B82F6;">
                        <h5 style="margin: 0 0 15px 0; color: #1e293b; font-size: 14px; font-weight: 600; border-bottom: 2px solid #dbeafe; padding-bottom: 8px;">
                            <i class="fa fa-file-text" style="margin-right: 8px; color: #3B82F6;"></i> DÉTAILS DE L'APPEL
                        </h5>
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="font-weight: 600; font-size: 13px; color: #334155;">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3" placeholder="Description de l'appel..." style="font-size: 13px; resize: vertical;"></textarea>
                            <?php echo form_error('description', '<span class="text-danger">', '</span>'); ?>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-weight: 600; font-size: 13px; color: #334155;">Note</label>
                            <textarea class="form-control" id="edit_note" name="note" rows="2" placeholder="Notes supplémentaires..." style="font-size: 13px; resize: vertical;"></textarea>
                            <?php echo form_error('note', '<span class="text-danger">', '</span>'); ?>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="padding: 15px 25px; border-top: 1px solid #e2e8f0; background: #f8fafc; border-radius: 0 0 8px 8px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</button>
                    <button type="reset" class="btn btn-warning"><i class="fa fa-refresh"></i> Réinitialiser</button>
                    <button type="submit" class="btn btn-success" id="formSubmitBtn">
                        <i class="fa fa-save"></i> <span id="formSubmitText">Enregistrer</span>
                    </button>
                </div>
            </form>

            <script>
                function fermerFormulaireAppel() {
                    // Si vous utilisez Bootstrap Modal
                    var modal = document.getElementById('callForm').closest('.modal');
                    if (modal) {
                        $(modal).modal('hide');
                    } else {
                        // Sinon, fermer le formulaire
                        document.getElementById('callForm').reset();
                        // Ou masquer la div parente
                        var container = document.getElementById('callForm').parentNode;
                        if (container) {
                            container.style.display = 'none';
                        }
                    }
                }
            </script>

           </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL DÉTAILS                              -->
<!-- ========================================== -->
<div id="calldetails" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 800px;">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; border-radius: 8px 8px 0 0;">
                <h4 style="margin: 0; color: white;">
                    <i class="fa fa-info-circle" style="color: #60a5fa;"></i> Détails de l'appel
                </h4>
                <button type="button" class="close" data-dismiss="modal" style="color:white; opacity: 0.8;">&times;</button>
            </div>
            <div class="modal-body" id="getdetails" style="padding: 24px; background: #fafcff;"></div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- SCRIPTS                                    -->
<!-- ========================================== -->
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    $(function () {
        // Date picker
        $('.date').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true
        });

        // Reset form on modal close
        $('#callFormModal').on('hidden.bs.modal', function() {
            $('#callForm')[0].reset();
            $('#edit_id').val('');
            $('#edit_date').val('<?php echo date($this->customlib->getSchoolDateFormat()); ?>');
        });

        // Focus effect
        $('.modal-chic .form-control').on('focus', function() {
            $(this).closest('.form-group').addClass('focused');
        }).on('blur', function() {
            $(this).closest('.form-group').removeClass('focused');
        });
    });

    // ========================================== //
    // EXPORTATION                                //
    // ========================================== //
    function exportData(type) {
        const callType = document.getElementById('filterCallType').value;
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        const params = `?call_type=${encodeURIComponent(callType)}&date_from=${encodeURIComponent(dateFrom)}&date_to=${encodeURIComponent(dateTo)}`;

        document.getElementById('spinnerOverlay').classList.add('active');

        var url = '<?php echo base_url("admin/generalcall/export_"); ?>' + type + params;
        window.location.href = url;

        setTimeout(function() {
            document.getElementById('spinnerOverlay').classList.remove('active');
        }, 3000);
    }

    // ========================================== //
    // OUVERTURE MODAL - AJOUT                    //
    // ========================================== //
    function openAddModal() {
        $('#formModalIcon').removeClass('fa-pencil-square-o').addClass('fa-phone');
        $('#formTitleText').text('Nouvel appel');
        $('#formModalSubtitle').text('Enregistrez un nouvel appel téléphonique');
        $('#formSubmitText').text('Enregistrer');
        $('#callForm').attr('action', '<?php echo site_url('admin/generalcall'); ?>');
        $('#callForm')[0].reset();
        $('#edit_id').val('');
        $('#edit_date').val('<?php echo date($this->customlib->getSchoolDateFormat()); ?>');
        $('#callFormModal').modal('show');
    }

    // ========================================== //
    // OUVERTURE MODAL - MODIFICATION (AJAX)      //
    // ========================================== //
    function openEditModal(id) {
        document.getElementById('spinnerOverlay').classList.add('active');

        Swal.fire({
            title: 'Chargement...',
            text: 'Récupération des données de l\'appel',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        var url = '<?php echo base_url(); ?>admin/generalcall/get_call_data/' + id;

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                document.getElementById('spinnerOverlay').classList.remove('active');
                Swal.close();

                if (response.success) {
                    var data = response.call;

                    $('#formModalIcon').removeClass('fa-phone').addClass('fa-pencil-square-o');
                    $('#formTitleText').text('Modifier l\'appel');
                    $('#formModalSubtitle').text('Mettez à jour les informations de l\'appel');
                    $('#formSubmitText').text('Mettre à jour');
                    $('#callForm').attr('action', '<?php echo site_url('admin/generalcall/update_ajax'); ?>');

                    $('#edit_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_contact').val(data.contact);
                    $('#edit_call_duration').val(data.call_dureation);
                    $('#edit_description').val(data.description);
                    $('#edit_note').val(data.note);

                    // Formatage de la date
                    if (data.date) {
                        var dateParts = data.date.split('-');
                        var formattedDate = dateParts[2] + '/' + dateParts[1] + '/' + dateParts[0];
                        $('#edit_date').val(formattedDate);
                    }

                    // Sélectionner le type d'appel
                    if (data.call_type) {
                        $('input[name="call_type"][value="' + data.call_type + '"]').prop('checked', true);
                    }

                    $('#callFormModal').modal('show');
                } else {
                    Swal.fire({
                        title: 'Erreur',
                        text: response.message || 'Impossible de charger les données de l\'appel',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                document.getElementById('spinnerOverlay').classList.remove('active');
                Swal.close();

                Swal.fire({
                    title: 'Erreur',
                    text: 'Une erreur est survenue lors du chargement des données.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    // ========================================== //
    // FILTRES                                    //
    // ========================================== //
    function applyFilters() {
        const callType = document.getElementById('filterCallType').value;
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        const rows = document.querySelectorAll('#callTableBody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            if (row.cells.length === 1 && row.cells[0].colSpan === 8) return;

            const rowCallType = row.dataset.callType || '';
            const rowDate = row.dataset.date || '';

            let show = true;

            if (callType && rowCallType !== callType) show = false;
            if (dateFrom && rowDate < dateFrom) show = false;
            if (dateTo && rowDate > dateTo) show = false;

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        let noResult = document.getElementById('noResultMessage');
        if (!noResult) {
            noResult = document.createElement('tr');
            noResult.id = 'noResultMessage';
            noResult.innerHTML = '<td colspan="8" class="text-center text-muted" style="padding:40px 0;">Aucun appel ne correspond aux filtres</td>';
            document.querySelector('#callTableBody').appendChild(noResult);
        }
        noResult.style.display = visibleCount === 0 ? '' : 'none';
    }

    function resetFilters() {
        document.getElementById('filterCallType').value = '';
        document.getElementById('filterDateFrom').value = '';
        document.getElementById('filterDateTo').value = '';
        applyFilters();
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    function confirmDelete(event, name, id) {
        event.preventDefault();

        Swal.fire({
            title: 'Confirmation',
            text: 'Supprimer définitivement l\'appel de "' + name + '" ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?php echo base_url("admin/generalcall/delete/"); ?>' + id;
            }
        });
    }

    // ========================================== //
    // DÉTAILS                                    //
    // ========================================== //
    function getRecord(id) {
        document.getElementById('spinnerOverlay').classList.add('active');

        $.ajax({
            url: '<?php echo base_url(); ?>admin/generalcall/details/' + id,
            success: function (result) {
                document.getElementById('spinnerOverlay').classList.remove('active');
                $('#getdetails').html(result);
                $('#calldetails').modal('show');
            },
            error: function() {
                document.getElementById('spinnerOverlay').classList.remove('active');
                Swal.fire({
                    title: 'Erreur',
                    text: 'Impossible de charger les détails de l\'appel',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    $('#callForm').on('submit', function(e) {

        // Seulement pour la modification (update_ajax)
        if ($(this).attr('action').indexOf('update_ajax') !== -1) {

            e.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',

                beforeSend: function () {
                    $('#formSubmitBtn').prop('disabled', true);
                },

                success: function(response) {

                    $('#formSubmitBtn').prop('disabled', false);

                    if (response.success) {

                        $('#callFormModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Si vous voulez mettre à jour le tableau,
                        // on le fera après. Pour l'instant on ne recharge pas la page.

                    } else {

                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            html: response.message
                        });

                    }
                },

                error: function() {

                    $('#formSubmitBtn').prop('disabled', false);

                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Une erreur est survenue.'
                    });

                }

            });

        }

    });
</script>