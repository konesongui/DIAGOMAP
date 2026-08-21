<!-- ============================================================
     PAGE : Liste des visiteurs avec modal d'édition et export
     DESCRIPTION : Interface moderne avec export PDF/Excel et édition en modal
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
      /*  border-left: 5px solid var(--primary-light);*/
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
        font-size: 13px;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        line-height: 1.2;
    }

    .stat-card .stat-icon {
        font-size: 32px;
        color: #273772;
        opacity: 0.7;
    }

    .stat-card:nth-child(1) { border-left-color: #e8ecef; }
    .stat-card:nth-child(2) { border-left-color: #10b981; }
    .stat-card:nth-child(3) { border-left-color: #f59e0b; }
    .stat-card:nth-child(4) { border-left-color: #8b5cf6; }

    /* ========================================== */
    /* FILTRES                                    */
    /* ========================================== */
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 39px;
        align-items: center;
        margin-bottom: 24px;
        padding: 16px 20px;
        background: #ffffff;
        border-radius: var(--radius-md);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid var(--border-light);
    }

    .filter-bar .filter-group {

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
        padding: 5px;
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

    .badge-purpose {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    .badge-purpose.meeting { background: #dbeafe; color: #1d4ed8; }
    .badge-purpose.delivery { background: #d1fae5; color: #059669; }
    .badge-purpose.visit { background: #fef3c7; color: #d97706; }
    .badge-purpose.other { background: #f1f5f9; color: #64748b; }

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
        color: #273772;
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

    .modal-chic .input-group .form-control {
        border-right: none;
        border-radius: var(--radius-sm) 0 0 var(--radius-sm);
    }

    .modal-chic .input-group .input-group-addon {
        background: #f1f5f9;
        border: 2px solid #e2e8f0;
        border-left: none;
        border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
        padding: 0 14px;
        color: var(--text-muted);
        font-size: 16px;
        display: flex;
        align-items: center;
    }

    .modal-chic .form-group.focused .form-control {
        border-color: var(--primary-light);
    }

    .modal-chic .modal-footer {
        padding: 16px 144px 24px;
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

    /* Animation pour la génération du badge */
    @keyframes badgePulse {
        0% { background-color: #d1fae5; transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { background-color: transparent; transform: scale(1); }
    }

    #edit_badge.badge-generated {
        animation: badgePulse 0.5s ease;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-ioxhost"></i> <?php echo $this->lang->line('front_office'); ?>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">

                <!-- ========================================== -->
                <!-- STATISTIQUES                               -->
                <!-- ========================================== -->


                <!-- ========================================== -->
                <!-- Carte principale                          -->
                <!-- ========================================== -->
                <div class="card-modern">
                    <div class="card-header">
                        <h3>
                            <i class="fa fa-address-book"></i> Gestion des Visiteurs
                        </h3>
                        <div class="d-flex align-items-center gap-3" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url(); ?>admin/admin/hub" class="btn-back" title="Retour au tableau de bord">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <?php if ($this->rbac->hasPrivilege('visiteurs', 'can_add')) : ?>
                                <button type="button" class="btn-add-modern" onclick="openAddModal()">
                                    <i class="fa fa-plus-circle" style="margin-right: 6px;"></i> Ajouter un visiteur
                                </button>
                            <?php endif; ?>
                        </div>
                    </div><br><br>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-info">
                                <h4><i class="fa fa-users"></i> Total visiteurs</h4>
                                <p class="number"><?php echo count($visitor_list ?? []); ?></p>
                            </div>
                            <div class="stat-icon"><i class="fa fa-address-book"></i></div>
                        </div>

                        <?php
                        $motifCounts = [];
                        foreach ($visitor_list as $v) {
                            $m = $v['purpose'] ?? 'Autre';
                            $motifCounts[$m] = ($motifCounts[$m] ?? 0) + 1;
                        }
                        arsort($motifCounts);
                        $topMotif = array_key_first($motifCounts);
                        $topCount = $motifCounts[$topMotif] ?? 0;
                        ?>
                        <div class="stat-card">
                            <div class="stat-info">
                                <h4><i class="fa fa-tag"></i> Motif principal</h4>
                                <p class="number"><?php echo htmlspecialchars($topMotif); ?> <small style="font-size:14px;font-weight:400;color:#64748b;">(<?php echo $topCount; ?>)</small></p>
                            </div>
                            <div class="stat-icon"><i class="fa fa-pie-chart"></i></div>
                        </div>

                        <?php
                        $today = date('Y-m-d');
                        $todayCount = 0;
                        foreach ($visitor_list as $v) {
                            if (isset($v['date']) && $v['date'] === $today) $todayCount++;
                        }
                        ?>
                        <div class="stat-card">
                            <div class="stat-info">
                                <h4><i class="fa fa-calendar-day"></i> Aujourd'hui</h4>
                                <p class="number"><?php echo $todayCount; ?></p>
                            </div>
                            <div class="stat-icon"><i class="fa fa-clock-o"></i></div>
                        </div>

                        <?php
                        $activeCount = 0;
                        foreach ($visitor_list as $v) {
                            if (empty($v['out_time'])) $activeCount++;
                        }
                        ?>
                        <div class="stat-card">
                            <div class="stat-info">
                                <h4><i class="fa fa-circle" style="color:#10b981;"></i> En cours</h4>
                                <p class="number"><?php echo $activeCount; ?></p>
                            </div>
                            <div class="stat-icon"><i class="fa fa-hourglass-half"></i></div>
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
                                <label><i class="fa fa-filter"></i> Motif :</label>
                                <select id="filterPurpose" onchange="applyFilters()">
                                    <option value="">Tous</option>
                                    <?php
                                    $purposes = array_unique(array_column($visitor_list ?? [], 'purpose'));
                                    foreach ($purposes as $p) : ?>
                                        <option value="<?php echo htmlspecialchars($p); ?>"><?php echo htmlspecialchars($p); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label><i class="fa fa-calendar"></i> Période :</label>
                                <input type="date" id="filterDateFrom" onchange="applyFilters()" placeholder="Du">
                                <span style="color:#94a3b8;font-size:13px;">→</span>
                                <input type="date" id="filterDateTo" onchange="applyFilters()" placeholder="Au">
                            </div>
                            <div class="filter-group">
                                <label><i class="fa fa-clock-o"></i> Statut :</label>
                                <select id="filterStatus" onchange="applyFilters()">
                                    <option value="">Tous</option>
                                    <option value="active">En cours</option>
                                    <option value="completed">Terminé</option>
                                </select>
                            </div>
                            <div style="display:flex;gap:8px;margin-left:auto;flex-wrap:wrap;">
                                <button class="btn-reset" onclick="resetFilters()"><i class="fa fa-undo"></i> Réinitialiser</button>

                                <div class="export-group">
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
                            <table class="table table-modern example" id="visitorsTable">
                                <thead>
                                <tr>
                                   <!-- <th><?php echo $this->lang->line('purpose'); ?></th>-->
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th><?php echo $this->lang->line('phone'); ?></th>
                                    <!--<th>Email</th>-->
                                    <th><?php echo $this->lang->line('date'); ?></th>
                                    <th><?php echo $this->lang->line('in_time'); ?></th>
                                    <th><?php echo $this->lang->line('out_time'); ?></th>
                                    <th>Badge</th>
                                    <!--<th><?php echo $this->lang->line('note'); ?></th>-->
                                    <th style="text-align: center; width: 120px;"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                                </thead>
                                <tbody id="visitorTableBody">
                                <?php if (empty($visitor_list)) : ?>
                                    <tr><td colspan="11" class="text-center text-muted"><?php echo $this->lang->line('no_record_found'); ?></td></tr>
                                <?php else : ?>
                                    <?php foreach ($visitor_list as $visitor) :
                                        $badgeClass = 'other';
                                        $purpose = strtolower($visitor['purpose'] ?? '');
                                        if (strpos($purpose, 'réunion') !== false || strpos($purpose, 'meeting') !== false) $badgeClass = 'meeting';
                                        elseif (strpos($purpose, 'livraison') !== false || strpos($purpose, 'delivery') !== false) $badgeClass = 'delivery';
                                        elseif (strpos($purpose, 'visite') !== false || strpos($purpose, 'visit') !== false) $badgeClass = 'visit';
                                        ?>
                                        <tr data-purpose="<?php echo htmlspecialchars($visitor['purpose']); ?>"
                                            data-date="<?php echo $visitor['date']; ?>"
                                            data-status="<?php echo empty($visitor['out_time']) ? 'active' : 'completed'; ?>">
                                           <!-- <td><span class="badge-purpose <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($visitor['purpose']); ?></span></td>-->
                                            <td><?php echo htmlspecialchars($visitor['name'] ); ?></td>
                                            <td><?php echo htmlspecialchars($visitor['firstname']); ?></td>
                                            <td><?php echo htmlspecialchars($visitor['contact']); ?></td>
                                            <!--<td><?php echo htmlspecialchars($visitor['email'] ?? ''); ?></td>-->
                                            <td><?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($visitor['date'])); ?></td>
                                            <td><?php echo htmlspecialchars($visitor['in_time']); ?></td>
                                            <td><?php echo htmlspecialchars($visitor['out_time']) ?: '<span style="color:#10b981;font-weight:500;"><i class="fa fa-circle" style="font-size:8px;"></i> En cours</span>'; ?></td>
                                            <td><?php echo htmlspecialchars($visitor['badge'] ?? ''); ?></td>
                                           <!-- <td><?php echo htmlspecialchars($visitor['note']); ?></td>-->
                                            <td style="text-align: center;">
                                                <div class="dropdown">
                                                    <button class="btn-action-dropdown dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                        Actions <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu actions-menu dropdown-menu-right">
                                                        <!-- NOUVEAU : Voir détails -->
                                                        <li>
                                                            <a onclick="getRecord(<?php echo $visitor['id']; ?>)" style="cursor: pointer;">
                                                                <i class="fa fa-eye" style="color: #8b5cf6;"></i> Voir détails
                                                            </a>
                                                        </li>
                                                        <li role="separator" class="divider"></li>

                                                        <?php if ($this->rbac->hasPrivilege('visiteurs', 'can_edit')) : ?>
                                                            <li>
                                                                <a onclick="openEditModal(<?php echo $visitor['id']; ?>)">
                                                                    <i class="fa fa-pencil" style="color: #3b82f6;"></i> Modifier
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>

                                                        <?php if (!empty($visitor['image'])) : ?>
                                                            <li>
                                                                <a href="<?php echo base_url('admin/visitors/download/' . $visitor['image']); ?>">
                                                                    <i class="fa fa-download" style="color: #10b981;"></i> Télécharger la pièce
                                                                </a>
                                                            </li>
                                                            <li role="separator" class="divider"></li>
                                                        <?php endif; ?>

                                                        <?php if ($this->rbac->hasPrivilege('visiteurs', 'can_delete')) : ?>
                                                            <li>
                                                                <a href="#" class="text-danger" onclick="confirmDelete(event, '<?php echo htmlspecialchars($visitor['name']); ?>', '<?php echo $visitor['id']; ?>', '<?php echo $visitor['image']; ?>')">
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
<div id="visitorFormModal" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-md" style="max-width: 700px;">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--primary-gradient); color: white; border-radius: 8px 8px 0 0;">
                <div>
                    <h4 id="formModalTitle" style="margin: 0; color: white;">
                        <i class="fa fa-user-plus" id="formModalIcon"></i>
                        <span id="formTitleText">Nouveau visiteur</span>
                    </h4>
                    <div class="modal-subtitle" id="formModalSubtitle" style="color: #94a3b8; font-size: 13px; margin-top: 4px;">
                        Remplissez les informations ci-dessous pour enregistrer un visiteur
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;">&times;</button>
            </div>

            <form id="visitorForm" action="<?php echo site_url('admin/visitors'); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" id="edit_id" value="">

                <!-- En-tête avec croix de fermeture -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 25px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; border-radius: 8px 8px 0 0;">
                    <h4 style="margin: 0; color: #1e293b; font-weight: 600; font-size: 18px;">
                        <i class="fa fa-user-plus" style="margin-right: 8px; color: #3B82F6;"></i> Nouveau visiteur
                    </h4>
                    <button type="button" onclick="fermerFormulaire()" style="font-size: 30px; font-weight: 700; color: #64748b; background: transparent; border: none; cursor: pointer; padding: 0 8px; line-height: 1; transition: color 0.2s;"
                            onmouseover="this.style.color='#ef4444'"
                            onmouseout="this.style.color='#64748b'">
                        &times;
                    </button>
                </div>

                <div class="modal-body" style="padding: 20px 25px;">

                    <!-- Section identité -->
                    <div style="background: #f8fafc; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                        <h5 style="margin: 0 0 15px 0; color: #1e293b; font-size: 14px; font-weight: 600; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px;">
                            <i class="fa fa-id-card" style="margin-right: 8px; color: #3B82F6;"></i> IDENTITÉ DU VISITEUR
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_name" name="name" placeholder="Nom du visiteur" style="height: 43px; font-size: 13px;" required>
                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_firstname" name="firstname" placeholder="Prénom du visiteur" style="height: 43px; font-size: 13px;" required>
                                    <span class="text-danger"><?php echo form_error('firstname'); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Téléphone</label>
                                    <input type="text" class="form-control" id="edit_contact" name="contact" placeholder="Ex: 07 00 00 00 00" style="height: 43px; font-size: 13px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Email</label>
                                    <input type="email" class="form-control" id="edit_email" name="email" placeholder="visiteur@email.com" style="height: 43px; font-size: 13px;">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Organisation</label>
                                    <input type="text" class="form-control" id="edit_organisation" name="organisation" placeholder="Nom de l'entreprise ou ONG" style="height: 43px; font-size: 13px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Fonction / Poste</label>
                                    <input type="text" class="form-control" id="edit_function" name="function" placeholder="DG, Partenaire financier..." style="height: 43px; font-size: 13px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section pièce d'identité -->
                    <div style="background: #f0fdf4; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #22c55e;">
                        <h5 style="margin: 0 0 15px 0; color: #1e293b; font-size: 14px; font-weight: 600; border-bottom: 2px solid #dcfce7; padding-bottom: 8px;">
                            <i class="fa fa-id-badge" style="margin-right: 8px; color: #22c55e;"></i> PIÈCE D'IDENTITÉ & SÉCURITÉ
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Type de pièce</label>
                                    <select name="id_type" class="form-control" id="edit_id_type" style="height: 43px; font-size: 13px;">
                                        <option value="">Choisir...</option>
                                        <option value="CNI">CNI</option>
                                        <option value="Passeport">Passeport</option>
                                        <option value="Permis">Permis de conduire</option>
                                        <option value="Carte séjour">Carte de séjour</option>
                                        <option value="Autre">Autre</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Numéro de pièce</label>
                                    <input type="text" class="form-control" id="edit_id_proof" name="id_proof" placeholder="CI-0123456789" style="height: 43px; font-size: 13px;">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Niveau d'accès autorisé</label>
                                    <select name="access_level" class="form-control" id="edit_access_level" style="height: 43px; font-size: 13px;">
                                        <option value="">Niveau...</option>
                                        <option value="Niveau 1">Niveau 1 - Accueil</option>
                                        <option value="Niveau 2">Niveau 2 - Bureau</option>
                                        <option value="Niveau 3">Niveau 3 - Tous les étages</option>
                                        <option value="Niveau 4">Niveau 4 - Zones sécurisées</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">N° de badge émis</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="edit_badge" name="badge" placeholder="B-001" style="height: 43px; font-size: 13px;">
                                        <span class="input-group-btn">
                                <button type="button" class="btn btn-primary" onclick="regenerateBadge()" style="height: 43px; border-radius: 0 4px 4px 0; background: #3B82F6; border: none; padding: 0 12px;">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            </span>
                                    </div>
                                    <small style="color: #94a3b8; font-size: 11px;">Généré automatiquement selon le niveau d'accès</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section informations de la visite -->
                    <div style="background: #eff6ff; padding: 15px; border-radius: 6px; margin-bottom: 15px; border-left: 4px solid #3B82F6;">
                        <h5 style="margin: 0 0 15px 0; color: #1e293b; font-size: 14px; font-weight: 600; border-bottom: 2px solid #dbeafe; padding-bottom: 8px;">
                            <i class="fa fa-calendar" style="margin-right: 8px; color: #3B82F6;"></i> INFORMATIONS DE LA VISITE
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <div class="form-group" style="margin-bottom: 12px;">
                                        <label style="font-weight: 600; font-size: 13px; color: #334155;">But / Motif <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control"
                                               id="edit_purpose"
                                               name="purpose"
                                               placeholder="Saisissez le motif de la visite..."
                                               style="height: 43px; font-size: 13px;"
                                               required
                                               list="purposeList">
                                        <datalist id="purposeList">
                                            <?php foreach ($Purpose as $value) { ?>
                                            <option value="<?php echo htmlspecialchars($value['visitors_purpose']); ?>">
                                                <?php } ?>
                                        </datalist>
                                        <span class="text-danger"><?php echo form_error('purpose'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Date <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control date" id="edit_date" name="date" readonly required style="height: 43px; font-size: 13px;">
                                    <span class="text-danger"><?php echo form_error('date'); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Heure d'arrivée <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control timepicker" id="edit_in_time" name="time" required placeholder="HH:MM" style="height: 43px; font-size: 13px;">
                                    <span class="text-danger"><?php echo form_error('time'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Heure de départ</label>
                                    <input type="text" class="form-control timepicker" id="edit_out_time" name="out_time" placeholder="HH:MM" style="height: 43px; font-size: 13px;">
                                    <span class="text-danger"><?php echo form_error('out_time'); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Nombre de personnes</label>
                                    <input type="number" class="form-control" id="edit_pepples" name="pepples" min="1" placeholder="1" style="height: 43px; font-size: 13px; width: 203%">
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-weight: 600; font-size: 13px; color: #334155;">Note</label>
                            <textarea class="form-control" id="edit_note" name="note" rows="2" placeholder="Ajouter une note..." style="font-size: 13px; resize: vertical;"></textarea>
                        </div>
                    </div>

                </div>

                <div class="modal-footer" style="padding: 15px 25px; border-top: 1px solid #e2e8f0; background: #f8fafc; border-radius: 0 0 8px 8px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="padding: 6px 18px;">
                        <i class="fa fa-times"></i> Annuler
                    </button>
                    <button type="reset" class="btn btn-warning" style="padding: 6px 18px;">
                        <i class="fa fa-refresh"></i> Réinitialiser
                    </button>
                    <button type="submit" class="btn btn-success" id="formSubmitBtn" style="padding: 6px 24px;">
                        <i class="fa fa-save"></i> <span id="formSubmitText">Enregistrer</span>
                    </button>
                </div>
            </form>

            <script>
                function fermerFormulaire() {
                    // Si vous utilisez Bootstrap Modal
                    $('#visitorForm').closest('.modal').modal('hide');
                    // Ou si c'est un simple formulaire
                    // document.getElementById('visitorForm').reset();
                    // ou fermer la div parente
                }
            </script>
              </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL DÉTAILS                              -->
<!-- ========================================== -->
<div id="visitordetails" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg" style="max-width: 800px;">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; border-radius: 8px 8px 0 0;">
                <h4 style="margin: 0; color: white;">
                    <i class="fa fa-info-circle" style="color: #60a5fa;"></i> Détails du visiteur
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
        // Timepicker
        $('.timepicker').timepicker({
            showInputs: false,
            defaultTime: false,
            showMeridian: false,
            minuteStep: 5
        });

        // Date picker
        $('.date').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true
        });

        // Focus effect
        $('.modal-chic .form-control').on('focus', function() {
            $(this).closest('.form-group').addClass('focused');
        }).on('blur', function() {
            $(this).closest('.form-group').removeClass('focused');
        });

        // Reset form on modal close
        $('#visitorFormModal').on('hidden.bs.modal', function() {
            $('#visitorForm')[0].reset();
            $('#edit_id').val('');
            $('#edit_file').val('');
            $('#edit_pepples').val(1);
            $('#edit_date').val('<?php echo date($this->customlib->getSchoolDateFormat()); ?>');
        });
    });

    // ========================================== //
    // EXPORTATION                                //
    // ========================================== //
    function exportData(type) {
        const purpose = document.getElementById('filterPurpose').value;
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;
        const status = document.getElementById('filterStatus').value;

        const params = `?purpose=${encodeURIComponent(purpose)}&date_from=${encodeURIComponent(dateFrom)}&date_to=${encodeURIComponent(dateTo)}&status=${encodeURIComponent(status)}`;

        // Afficher le spinner
        document.getElementById('spinnerOverlay').classList.add('active');

        var url = '<?php echo base_url("admin/visitors/export_"); ?>' + type + params;

        // Redirection vers le téléchargement
        window.location.href = url;

        // Cacher le spinner après un délai
        setTimeout(function() {
            document.getElementById('spinnerOverlay').classList.remove('active');
        }, 3000);
    }

    // ========================================== //
    // OUVERTURE MODAL - AJOUT                    //
    // ========================================== //
    function openAddModal() {
        $('#formModalIcon').removeClass('fa-pencil-square-o').addClass('fa-user-plus');
        $('#formTitleText').text('Nouveau visiteur');
        $('#formModalSubtitle').text('Remplissez les informations ci-dessous pour enregistrer un visiteur');
        $('#formSubmitText').text('Enregistrer');
        $('#visitorForm').attr('action', '<?php echo site_url('admin/visitors'); ?>');
        $('#visitorForm')[0].reset();
        $('#edit_id').val('');
        $('#edit_pepples').val(1);
        $('#edit_date').val('<?php echo date($this->customlib->getSchoolDateFormat()); ?>');
        $('#edit_file').val('');
        $('#visitorFormModal').modal('show');
    }

    // ========================================== //
    // OUVERTURE MODAL - MODIFICATION (AJAX)      //
    // ========================================== //
    function openEditModal(id) {
        // Afficher le spinner
        document.getElementById('spinnerOverlay').classList.add('active');

        // Afficher un message de chargement
        Swal.fire({
            title: 'Chargement...',
            text: 'Récupération des données du visiteur',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Construire l'URL
        var url = '<?php echo base_url(); ?>admin/visitors/get_visitor_data/' + id;
        console.log('Chargement des données depuis :', url);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Réponse reçue :', response);
                document.getElementById('spinnerOverlay').classList.remove('active');
                Swal.close();

                // Vérifier si la réponse est valide
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        console.error('Erreur de parsing JSON:', e);
                        Swal.fire({
                            title: 'Erreur',
                            text: 'Format de réponse invalide. Vérifiez les logs.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }
                }

                if (response.success) {
                    var data = response.visitor;
                    console.log('Données du visiteur :', data);

                    // Mettre à jour le titre et le bouton
                    $('#formModalIcon').removeClass('fa-user-plus').addClass('fa-pencil-square-o');
                    $('#formTitleText').text('Modifier le visiteur');
                    $('#formModalSubtitle').text('Mettez à jour les informations du visiteur');
                    $('#formSubmitText').text('Mettre à jour');
                    $('#visitorForm').attr('action', '<?php echo site_url('admin/visitors/update_ajax'); ?>');

                    // Remplir les champs
                    $('#edit_id').val(data.id);
                    $('#edit_purpose').val(data.purpose);
                    $('#edit_name').val(data.name);
                    $('#edit_firstname').val(data.firstname);
                    $('#edit_organisation').val(data.organisation);
                    $('#edit_function').val(data.function);
                    $('#edit_id_type').val(data.id_type);
                    $('#edit_access_level').val(data.access_level);
                    $('#edit_badge').val(data.badge);
                    $('#edit_email').val(data.email);
                    $('#edit_contact').val(data.contact || '');
                    $('#edit_id_proof').val(data.id_proof || '');
                    $('#edit_pepples').val(data.no_of_pepple || 1);

                    // Formatage de la date
                    if (data.date) {
                        var dateParts = data.date.split('-');
                        var formattedDate = dateParts[2] + '/' + dateParts[1] + '/' + dateParts[0];
                        $('#edit_date').val(formattedDate);
                    }

                    $('#edit_in_time').val(data.in_time || '');
                    $('#edit_out_time').val(data.out_time || '');
                    $('#edit_note').val(data.note || '');

                    // Ouvrir le modal
                    $('#visitorFormModal').modal('show');
                } else {
                    Swal.fire({
                        title: 'Erreur',
                        text: response.message || 'Impossible de charger les données du visiteur',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                document.getElementById('spinnerOverlay').classList.remove('active');
                Swal.close();

                console.error('=== ERREUR AJAX DÉTAILLÉE ===');
                console.error('Status:', status);
                console.error('Erreur:', error);
                console.error('Status code:', xhr.status);
                console.error('Response text:', xhr.responseText);
                console.error('Response headers:', xhr.getAllResponseHeaders());

                var errorMsg = 'Une erreur est survenue lors du chargement des données.\n\n';
                errorMsg += 'Status: ' + status + '\n';
                errorMsg += 'Erreur: ' + error + '\n';

                if (xhr.status === 404) {
                    errorMsg += '\nLa page demandée est introuvable.';
                } else if (xhr.status === 500) {
                    errorMsg += '\nErreur interne du serveur. Vérifiez les logs PHP.';
                }

                // Afficher la réponse brute si disponible
                if (xhr.responseText) {
                    errorMsg += '\n\nRéponse brute:\n' + xhr.responseText.substring(0, 500);
                }

                Swal.fire({
                    title: 'Erreur',
                    text: errorMsg,
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
        const purpose = document.getElementById('filterPurpose').value.toLowerCase();
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;
        const status = document.getElementById('filterStatus').value;

        const rows = document.querySelectorAll('#visitorTableBody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            if (row.cells.length === 1 && row.cells[0].colSpan === 11) return;

            const rowPurpose = (row.dataset.purpose || '').toLowerCase();
            const rowDate = row.dataset.date || '';
            const rowStatus = row.dataset.status || '';

            let show = true;

            if (purpose && rowPurpose !== purpose) show = false;
            if (dateFrom && rowDate < dateFrom) show = false;
            if (dateTo && rowDate > dateTo) show = false;
            if (status && rowStatus !== status) show = false;

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        let noResult = document.getElementById('noResultMessage');
        if (!noResult) {
            noResult = document.createElement('tr');
            noResult.id = 'noResultMessage';
            noResult.innerHTML = '<td colspan="11" class="text-center text-muted" style="padding:40px 0;">Aucun visiteur ne correspond aux filtres</td>';
            document.querySelector('#visitorTableBody').appendChild(noResult);
        }
        noResult.style.display = visibleCount === 0 ? '' : 'none';
    }

    function resetFilters() {
        document.getElementById('filterPurpose').value = '';
        document.getElementById('filterDateFrom').value = '';
        document.getElementById('filterDateTo').value = '';
        document.getElementById('filterStatus').value = '';
        applyFilters();
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    function confirmDelete(event, name, id, image) {
        event.preventDefault();

        let deleteUrl = '<?php echo base_url("admin/visitors/delete/"); ?>' + id;
        if (image && image !== '') {
            deleteUrl = '<?php echo base_url("admin/visitors/imagedelete/"); ?>' + id + '/' + image;
        }

        Swal.fire({
            title: 'Confirmation',
            text: 'Supprimer définitivement le visiteur "' + name + '" ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = deleteUrl;
            }
        });
    }

    // ========================================== //
    // DÉTAILS                                    //
    // ========================================== //
    function getRecord(id) {
        // Afficher le spinner
        document.getElementById('spinnerOverlay').classList.add('active');

        $.ajax({
            url: '<?php echo base_url(); ?>admin/visitors/details/' + id,
            success: function (result) {
                document.getElementById('spinnerOverlay').classList.remove('active');
                $('#getdetails').html(result);
                $('#visitordetails').modal('show');
            },
            error: function() {
                document.getElementById('spinnerOverlay').classList.remove('active');
                Swal.fire({
                    title: 'Erreur',
                    text: 'Impossible de charger les détails du visiteur',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    // ========================================== //
    // GÉNÉRATION AUTOMATIQUE DU BADGE            //
    // ========================================== //
    $(document).ready(function() {
        // Écouter le changement du niveau d'accès
        $('#edit_access_level').on('change', function() {
            generateBadgeNumber();
        });

        // Écouter le changement du type de pièce (optionnel)
        $('#edit_id_type').on('change', function() {
            // Re-générer le badge si le niveau est déjà sélectionné
            if ($('#edit_access_level').val() !== '') {
                generateBadgeNumber();
            }
        });
    });

    function generateBadgeNumber() {
        var accessLevel = $('#edit_access_level').val();
        var idType = $('#edit_id_type').val();

        if (accessLevel === '') {
            $('#edit_badge').val('');
            return;
        }

        // Récupérer le préfixe en fonction du niveau d'accès
        var prefix = getBadgePrefix(accessLevel);

        // Récupérer le type de pièce pour plus de personnalisation
        var idPrefix = getIdTypePrefix(idType);

        // Générer un numéro aléatoire unique
        var randomNumber = generateUniqueNumber();

        // Formater le badge
        var badgeNumber = prefix + '-' + idPrefix + '-' + randomNumber;

        // Mettre à jour le champ
        $('#edit_badge').val(badgeNumber);
    }

    function getBadgePrefix(level) {
        var prefixMap = {
            'Niveau 1': 'A',      // Accueil
            'Niveau 2': 'B',      // Bureau
            'Niveau 3': 'C',      // Tous les étages
            'Niveau 4': 'S'       // Zones sécurisées
        };

        // Normaliser le niveau pour la correspondance
        var levelLower = level.toLowerCase().trim();

        if (levelLower.includes('accueil')) return 'A';
        if (levelLower.includes('bureau')) return 'B';
        if (levelLower.includes('tous les étages')) return 'C';
        if (levelLower.includes('zones sécurisées')) return 'S';

        // Si le niveau n'est pas reconnu, utiliser la première lettre du niveau
        return level.charAt(0).toUpperCase();
    }

    function getIdTypePrefix(idType) {
        if (!idType) return 'V'; // Visiteur par défaut

        var prefixMap = {
            'CNI': 'CN',
            'Passeport': 'PP',
            'Permis': 'PR',
            'Carte séjour': 'CS',
            'Autre': 'AU'
        };

        return prefixMap[idType] || 'V';
    }

    function generateUniqueNumber() {
        // Générer un nombre aléatoire à 4 chiffres
        var random = Math.floor(1000 + Math.random() * 9000);

        // Ajouter un timestamp pour plus d'unicité (optionnel)
        var timestamp = Date.now().toString().slice(-4);

        // Combiner aléatoire + timestamp
        return random + '-' + timestamp;
    }

    // ========================================== //
    // GÉNÉRATION MANUELLE DU BADGE (Bouton)      //
    // ========================================== //
    function regenerateBadge() {
        generateBadgeNumber();
        // Notification visuelle
        var badgeField = $('#edit_badge');
        badgeField.css('background-color', '#d1fae5');
        setTimeout(function() {
            badgeField.css('background-color', '');
        }, 500);
    }

    // ========================================== //
    // GÉNÉRATION DU BADGE VIA AJAX (SERVER)      //
    // ========================================== //
    function generateBadgeServer() {
        var accessLevel = $('#edit_access_level').val();
        var idType = $('#edit_id_type').val();

        if (accessLevel === '') {
            $('#edit_badge').val('');
            return;
        }

        $.ajax({
            url: '<?php echo base_url(); ?>admin/visitors/generate_badge',
            type: 'POST',
            data: {
                access_level: accessLevel,
                id_type: idType
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#edit_badge').val(response.badge);
                    // Animation de confirmation
                    $('#edit_badge').css('background-color', '#d1fae5');
                    setTimeout(function() {
                        $('#edit_badge').css('background-color', '');
                    }, 500);
                }
            },
            error: function() {
                // Fallback: génération côté client
                generateBadgeNumber();
            }
        });
    }
</script>