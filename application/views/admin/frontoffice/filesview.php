<!-- ============================================================
     PAGE : Gestion des courriers
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
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        padding: 16px 20px;
        box-shadow: var(--shadow-soft);
        border-left: 5px solid var(--primary-light);
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
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 500;
        margin: 0 0 4px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-card .stat-info .number {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        line-height: 1.2;
    }

    .stat-card .stat-icon {
        font-size: 28px;
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
        gap: 12px;
        align-items: center;
        margin-bottom: 24px;
        padding: 14px 18px;
        background: #ffffff;
        border-radius: var(--radius-md);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid var(--border-light);
    }

    .filter-bar .filter-group {
        display: flex;
        align-items: center;
        gap: 6px;
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
        padding: 6px 14px;
        font-size: 13px;
        background: #ffffff;
        transition: var(--transition);
        min-width: 130px;
        height: 36px;
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
        padding: 6px 18px;
        font-size: 13px;
        font-weight: 500;
        transition: var(--transition);
        cursor: pointer;
        height: 36px;
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
        padding: 6px 16px;
        font-size: 13px;
        font-weight: 500;
        transition: var(--transition);
        cursor: pointer;
        height: 36px;
    }

    .filter-bar .btn-reset:hover {
        background: #cbd5e1;
    }

    /* ========================================== */
    /* BOUTONS D'EXPORTATION                      */
    /* ========================================== */
    .export-group {
        display: flex;
        gap: 6px;
        align-items: center;
        margin-left: auto;
    }

    .export-group .export-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-right: 4px;
    }

    .btn-export {
        border: none;
        border-radius: var(--radius-sm);
        padding: 6px 16px;
        font-size: 12px;
        font-weight: 500;
        transition: var(--transition);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
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
        font-size: 14px;
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
        font-size: 14px;
        color: #fca5a5;
    }

    .export-divider {
        width: 1px;
        height: 28px;
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
        padding: 18px 24px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .card-modern .card-header h3 {
        color: #ffffff;
        font-size: 18px;
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
        padding: 20px 24px;
        background: var(--bg-light);
    }

    /* ========================================== */
    /* TABLE                                      */
    /* ========================================== */
    .table-modern {
        border-collapse: separate;
        border-spacing: 0 4px;
        width: 100%;
        margin-bottom: 0;
    }

    .table-modern thead th {
        background: #f1f5f9;
        color: var(--text-dark);
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 14px;
        border: none;
        border-bottom: 2px solid var(--border-light);
        white-space: nowrap;
    }

    .table-modern tbody td {
        background: #ffffff;
        padding: 10px 14px;
        border: none;
        border-bottom: 1px solid #eef2f6;
        vertical-align: middle;
        font-size: 13px;
        color: var(--text-dark);
    }

    .table-modern tbody tr:hover td {
        background: #f8fafc;
        transition: background 0.15s ease;
    }

    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    /* Badges */
    .badge-courier-type {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
    }
    .badge-courier-type.incoming { background: #dbeafe; color: #1d4ed8; }
    .badge-courier-type.outgoing { background: #d1fae5; color: #059669; }
    .badge-courier-type.internal { background: #fef3c7; color: #d97706; }
    .badge-courier-type.other { background: #f1f5f9; color: #64748b; }

    .badge-status {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }
    .badge-status.pending { background: #fef3c7; color: #92400e; }
    .badge-status.processed { background: #d1fae5; color: #065f46; }
    .badge-status.archived { background: #e2e8f0; color: #475569; }

    /* ========================================== */
    /* BOUTONS D'ACTION                           */
    /* ========================================== */
    .btn-action-dropdown {
        background: transparent;
        border: 1px solid var(--border-light);
        border-radius: var(--radius-sm);
        padding: 4px 12px;
        color: #475569;
        font-size: 12px;
        transition: var(--transition);
    }

    .btn-action-dropdown:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .btn-action-dropdown .caret {
        margin-left: 4px;
    }

    .dropdown-menu.actions-menu {
        border-radius: var(--radius-md);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border-light);
        padding: 4px 0;
        min-width: 170px;
    }

    .dropdown-menu.actions-menu li a {
        padding: 6px 18px;
        font-size: 13px;
        color: var(--text-dark);
        transition: background 0.15s;
        display: flex;
        align-items: center;
        gap: 8px;
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
        padding: 5px 16px;
        font-size: 13px;
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
        padding: 6px 20px;
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
        padding: 18px 24px;
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
        font-size: 26px;
        text-shadow: none;
        transition: var(--transition);
        padding: 0;
        margin: -4px -6px -4px auto;
    }

    .modal-chic .modal-header .close:hover {
        opacity: 1;
        transform: rotate(90deg);
    }

    .modal-chic .modal-header h4 {
        color: #ffffff;
        font-weight: 600;
        font-size: 18px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-chic .modal-header h4 i {
        color: #60a5fa;
        font-size: 20px;
    }

    .modal-chic .modal-header .modal-subtitle {
        color: rgba(255,255,255,0.7);
        font-size: 12px;
        font-weight: 400;
        margin-top: 2px;
    }

    .modal-chic .modal-body {
        padding: 24px 24px 16px;
        background: #fafcff;
    }

    .modal-chic .form-group {
        margin-bottom: 16px;
        position: relative;
    }

    .modal-chic .form-group label {
        font-weight: 600;
        font-size: 13px;
        color: var(--text-dark);
        margin-bottom: 4px;
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
        font-size: 13px;
        width: 16px;
        display: inline-block;
    }

    .modal-chic .form-control {
        border: 2px solid #e2e8f0;
        border-radius: var(--radius-sm);
        padding: 8px 14px;
        font-size: 13px;
        transition: var(--transition);
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        height: 40px;
        color: var(--text-dark);
    }

    .modal-chic .form-control:focus {
        border-color: var(--primary-light);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        outline: none;
    }

    .modal-chic .form-control::placeholder {
        color: #a0aec0;
        font-size: 12px;
    }

    .modal-chic textarea.form-control {
        height: auto;
        min-height: 70px;
        resize: vertical;
    }

    .modal-chic .modal-footer {
        padding: 14px 24px 20px;
        border-top: 1px solid #eef2f6;
        background: #ffffff;
        border-radius: 0 0 var(--radius-lg) var(--radius-lg);
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .modal-chic .modal-footer .btn {
        border-radius: var(--radius-sm);
        padding: 8px 20px;
        font-weight: 500;
        font-size: 13px;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-width: 80px;
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
        font-size: 14px;
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
        .table-modern thead th, .table-modern tbody td { padding: 8px 10px; font-size: 12px; }
        .btn-add-modern { width: 100%; text-align: center; }
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-bar .filter-group { width: 100%; }
        .filter-bar .filter-group select, .filter-bar .filter-group input { width: 100%; min-width: unset; }
        .export-group { margin-left: 0 !important; width: 100%; flex-wrap: wrap; }
        .export-group .btn-export { flex: 1; justify-content: center; padding: 6px 12px; font-size: 11px; }
        .export-group .export-label { width: 100%; text-align: center; }
        .export-divider { display: none; }
        .modal-chic .modal-body { padding: 16px; }
        .modal-chic .modal-header { padding: 14px 16px; }
        .modal-chic .modal-footer { flex-direction: column; }
        .modal-chic .modal-footer .btn { width: 100%; }
    }

    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
        .export-group .btn-export { font-size: 10px; padding: 4px 10px; }
        .export-group .btn-export i { font-size: 12px; }
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
                            <i class="fa fa-envelope"></i> Gestion des courriers
                        </h3>
                        <div class="d-flex align-items-center gap-3" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url(); ?>admin/admin/hub" class="btn-back" title="Retour au tableau de bord">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <?php if ($this->rbac->hasPrivilege('courriers', 'can_add')) : ?>
                                <button type="button" class="btn-add-modern" onclick="openAddModal()">
                                    <i class="fa fa-plus-circle" style="margin-right: 6px;"></i> Ajouter un courrier
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- STATISTIQUES                               -->
                    <!-- ========================================== -->
                    <div style="padding: 20px 24px 0;">
                        <div class="stats-grid">
                            <?php
                            $stats = $stats ?? ['total' => 0, 'incoming' => 0, 'outgoing' => 0, 'internal' => 0, 'today' => 0];
                            ?>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-envelope"></i> Total courriers</h4>
                                    <p class="number"><?php echo $stats['total'] ?? 0; ?></p>
                                </div>
                                <div class="stat-icon"><i class="fa fa-envelope-o"></i></div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-arrow-circle-down" style="color:#3b82f6;"></i> Reçus</h4>
                                    <p class="number"><?php echo $stats['incoming'] ?? 0; ?></p>
                                </div>
                                <div class="stat-icon"><i class="fa fa-arrow-circle-down"></i></div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-arrow-circle-up" style="color:#10b981;"></i> Envoyés</h4>
                                    <p class="number"><?php echo $stats['outgoing'] ?? 0; ?></p>
                                </div>
                                <div class="stat-icon"><i class="fa fa-arrow-circle-up"></i></div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-exchange" style="color:#f59e0b;"></i> Internes</h4>
                                    <p class="number"><?php echo $stats['internal'] ?? 0; ?></p>
                                </div>
                                <div class="stat-icon"><i class="fa fa-exchange"></i></div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-calendar-day" style="color:#8b5cf6;"></i> Aujourd'hui</h4>
                                    <p class="number"><?php echo $stats['today'] ?? 0; ?></p>
                                </div>
                                <div class="stat-icon"><i class="fa fa-clock-o"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <?php if ($this->session->flashdata('msg')) : ?>
                            <div class="alert alert-success alert-dismissible fade in">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <i class="fa fa-check-circle"></i> <?php echo $this->session->flashdata('msg'); ?>
                            </div>
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
                                <label><i class="fa fa-filter"></i> Type :</label>
                                <select id="filterType" onchange="applyFilters()">
                                    <option value="">Tous</option>
                                    <?php
                                    $courier_types = $courier_types ?? [];
                                    foreach ($courier_types as $type) :
                                        ?>
                                        <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label><i class="fa fa-tag"></i> Statut :</label>
                                <select id="filterStatus" onchange="applyFilters()">
                                    <option value="">Tous</option>
                                    <option value="pending">En attente</option>
                                    <option value="processed">Traité</option>
                                    <option value="archived">Archivé</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label><i class="fa fa-calendar"></i> Période :</label>
                                <input type="date" id="filterDateFrom" onchange="applyFilters()" placeholder="Du">
                                <span style="color:#94a3b8;font-size:13px;">→</span>
                                <input type="date" id="filterDateTo" onchange="applyFilters()" placeholder="Au">
                            </div>
                            <div style="display:flex;gap:6px;margin-left:auto;flex-wrap:wrap;">
                                <button class="btn-filter" onclick="applyFilters()"><i class="fa fa-search"></i> Filtrer</button>
                                <button class="btn-reset" onclick="resetFilters()"><i class="fa fa-undo"></i> Réinitialiser</button>

                                <div class="export-group">
                                    <span class="export-label"><i class="fa fa-download"></i> Exporter</span>
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
                            <table class="table table-modern" id="courierTable">
                                <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Nom</th>
                                    <th>Référence</th>
                                    <th>Date</th>
                                    <th>Adresse</th>
                                    <th>Statut</th>
                                    <th style="text-align: center; width: 100px;">Action</th>
                                </tr>
                                </thead>
                                <tbody id="courierTableBody">
                                <?php if (empty($courier_list)) : ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted" style="padding: 40px 0;">
                                            <i class="fa fa-envelope" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
                                            <p style="font-size: 16px; color: #64748b;">Aucun courrier enregistré</p>
                                            <p style="font-size: 13px; color: #94a3b8;">Commencez par ajouter un nouveau courrier</p>
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($courier_list as $courier) :
                                        $badgeClass = 'other';
                                        $type = strtolower($courier['courier_type'] ?? '');
                                        if (strpos($type, 'reçu') !== false || strpos($type, 'incoming') !== false) $badgeClass = 'incoming';
                                        elseif (strpos($type, 'envoi') !== false || strpos($type, 'outgoing') !== false) $badgeClass = 'outgoing';
                                        elseif (strpos($type, 'interne') !== false || strpos($type, 'internal') !== false) $badgeClass = 'internal';

                                        $statusLabels = [
                                            'pending' => 'En attente',
                                            'processed' => 'Traité',
                                            'archived' => 'Archivé'
                                        ];
                                        $statusClass = [
                                            'pending' => 'pending',
                                            'processed' => 'processed',
                                            'archived' => 'archived'
                                        ];
                                        ?>
                                        <tr data-type="<?php echo htmlspecialchars($courier['courier_type'] ?? ''); ?>"
                                            data-date="<?php echo $courier['date_received'] ?? ''; ?>"
                                            data-status="<?php echo $courier['status'] ?? ''; ?>"
                                            data-id="<?php echo $courier['id']; ?>">
                                            <td>
                                                    <span class="badge-courier-type <?php echo $badgeClass; ?>">
                                                        <i class="fa <?php
                                                        echo $badgeClass == 'incoming' ? 'fa-arrow-circle-down' :
                                                            ($badgeClass == 'outgoing' ? 'fa-arrow-circle-up' :
                                                                ($badgeClass == 'internal' ? 'fa-exchange' : 'fa-envelope-o'));
                                                        ?>"></i>
                                                        <?php echo htmlspecialchars($courier['courier_type']); ?>
                                                    </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($courier['sender_name']); ?></td>
                                            <td><?php echo htmlspecialchars($courier['reference'] ?? ''); ?></td>
                                            <td><?php echo !empty($courier['date_received']) ? date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($courier['date_received'])) : ''; ?></td>
                                            <td><?php echo htmlspecialchars($courier['address'] ?? ''); ?></td>
                                            <td>
                                                    <span class="badge-status <?php echo $statusClass[$courier['status']] ?? 'pending'; ?>">
                                                        <?php echo $statusLabels[$courier['status']] ?? $courier['status']; ?>
                                                    </span>
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="dropdown">
                                                    <button class="btn-action-dropdown dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                        Actions <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu actions-menu dropdown-menu-right">
                                                        <li>
                                                            <a onclick="getRecord(<?php echo $courier['id']; ?>)" style="cursor: pointer;">
                                                                <i class="fa fa-eye" style="color: #8b5cf6;"></i> Voir détails
                                                            </a>
                                                        </li>
                                                        <li role="separator" class="divider"></li>

                                                        <?php if ($this->rbac->hasPrivilege('courriers', 'can_edit')) : ?>
                                                            <li>
                                                                <a onclick="openEditModal(<?php echo $courier['id']; ?>)">
                                                                    <i class="fa fa-pencil" style="color: #3b82f6;"></i> Modifier
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>

                                                        <?php if (!empty($courier['attachment'])) : ?>
                                                            <li>
                                                                <a href="<?php echo base_url('admin/couriers/download/' . $courier['attachment']); ?>">
                                                                    <i class="fa fa-download" style="color: #10b981;"></i> Télécharger
                                                                </a>
                                                            </li>
                                                            <li role="separator" class="divider"></li>
                                                        <?php endif; ?>

                                                        <?php if ($this->rbac->hasPrivilege('courriers', 'can_delete')) : ?>
                                                            <li>
                                                                <a href="#" class="text-danger" onclick="confirmDelete(event, '<?php echo htmlspecialchars($courier['sender_name']); ?>', <?php echo $courier['id']; ?>)">
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
<div id="courierFormModal" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-md" style="max-width: 720px;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 id="formModalTitle">
                        <i class="fa fa-envelope" id="formModalIcon"></i>
                        <span id="formTitleText">Nouveau courrier</span>
                    </h4>
                    <div class="modal-subtitle" id="formModalSubtitle">Enregistrez un nouveau courrier</div>
                </div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form id="courierForm" action="<?php echo site_url('admin/couriers/add_ajax'); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" id="edit_id" value="">
                <div class="modal-body">
                    <!-- Informations du courrier -->
                    <div style="background: #f8fafc; padding: 15px; border-radius: 6px; margin-bottom: 16px;">
                        <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px;">
                            <i class="fa fa-info-circle" style="margin-right: 8px; color: #3B82F6;"></i> INFORMATIONS DU COURRIER
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Type de courrier <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_courier_type" name="courier_type" placeholder="Ex: Reçu, Envoi, Interne..." style="height: 38px; font-size: 13px;" required>
                                    <?php echo form_error('courier_type', '<span class="text-danger">', '</span>'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_sender_name" name="sender_name" placeholder="Nom de l'expéditeur/destinataire" style="height: 38px; font-size: 13px;" required>
                                    <?php echo form_error('sender_name', '<span class="text-danger">', '</span>'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Référence</label>
                                    <input type="text" class="form-control" id="edit_reference" name="reference" placeholder="Numéro de référence" style="height: 38px; font-size: 13px;">
                                    <?php echo form_error('reference', '<span class="text-danger">', '</span>'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Date <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control date" id="edit_date_received" name="date_received" readonly required style="height: 38px; font-size: 13px;">
                                    <?php echo form_error('date_received', '<span class="text-danger">', '</span>'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Adresse</label>
                                    <input type="text" class="form-control" id="edit_address" name="address" placeholder="Adresse complète" style="height: 38px; font-size: 13px;">
                                    <?php echo form_error('address', '<span class="text-danger">', '</span>'); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description, note et statut -->
                    <div style="background: #eff6ff; padding: 15px; border-radius: 6px; margin-bottom: 0; border-left: 4px solid #3B82F6;">
                        <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dbeafe; padding-bottom: 6px;">
                            <i class="fa fa-file-text" style="margin-right: 8px; color: #3B82F6;"></i> DÉTAILS SUPPLÉMENTAIRES
                        </h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Description</label>
                                    <textarea class="form-control" id="edit_description" name="description" rows="2" placeholder="Description du courrier..." style="font-size: 13px; resize: vertical; min-height: 50px;"></textarea>
                                    <?php echo form_error('description', '<span class="text-danger">', '</span>'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Statut</label>
                                    <select class="form-control" id="edit_status" name="status" style="height: 38px; font-size: 13px;">
                                        <option value="pending">En attente</option>
                                        <option value="processed">Traité</option>
                                        <option value="archived">Archivé</option>
                                    </select>
                                    <?php echo form_error('status', '<span class="text-danger">', '</span>'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Document attaché</label>
                                    <input class="form-control" type="file" name="attachment" id="edit_attachment" style="height: 38px; padding: 3px 8px; font-size: 12px;">
                                    <?php echo form_error('attachment', '<span class="text-danger">', '</span>'); ?>
                                    <small style="color: #94a3b8; font-size: 11px;">Formats acceptés: PDF, DOC, DOCX, JPG, PNG</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Note</label>
                            <textarea class="form-control" id="edit_note" name="note" rows="2" placeholder="Notes supplémentaires..." style="font-size: 13px; resize: vertical; min-height: 40px;"></textarea>
                            <?php echo form_error('note', '<span class="text-danger">', '</span>'); ?>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</button>
                    <button type="reset" class="btn btn-warning"><i class="fa fa-refresh"></i> Réinitialiser</button>
                    <button type="submit" class="btn btn-success" id="formSubmitBtn">
                        <i class="fa fa-save"></i> <span id="formSubmitText">Enregistrer</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL DÉTAILS                              -->
<!-- ========================================== -->
<div id="courierdetails" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 800px;">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; border-radius: 8px 8px 0 0;">
                <h4 style="margin: 0; color: white;">
                    <i class="fa fa-info-circle" style="color: #60a5fa;"></i> Détails du courrier
                </h4>
                <button type="button" class="close" data-dismiss="modal" style="color:white; opacity: 0.8;">&times;</button>
            </div>
            <div class="modal-body" id="getdetails" style="padding: 24px; background: #fafcff;"></div>
        </div>
    </div>
</div>

<!-- SCRIPTS                                    -->
<!-- ========================================== -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    // ========================================== //
    // DOCUMENT READY                             //
    // ========================================== //
    $(document).ready(function() {
        // Initialisation des datepickers
        $('.date').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });

        // Initialisation de DataTable
        initDataTable();

        // Focus effect pour les champs du modal
        $('.modal-chic .form-control').on('focus', function() {
            $(this).closest('.form-group').addClass('focused');
        }).on('blur', function() {
            $(this).closest('.form-group').removeClass('focused');
        });

        // Reset du formulaire à la fermeture du modal
        $('#courierFormModal').on('hidden.bs.modal', function() {
            $('#courierForm')[0].reset();
            $('#edit_id').val('');
            $('#edit_attachment').val('');
            $('#edit_date_received').val(getCurrentDate());
            $('.text-danger').html('');
            $('.form-group').removeClass('has-error');
            $('.form-control').css('border-color', '');
        });

        // Intercepter la soumission du formulaire
        $('#courierForm').on('submit', function(e) {
            e.preventDefault();
            var editId = $('#edit_id').val();
            if (editId && editId !== '') {
                submitEditForm($(this));
            } else {
                submitAddForm($(this));
            }
        });
    });

    // ========================================== //
    // INITIALISATION DATATABLE                   //
    // ========================================== //
    function initDataTable() {
        if ($.fn.DataTable.isDataTable('#courierTable')) {
            $('#courierTable').DataTable().destroy();
        }

        $('#courierTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?php echo base_url('admin/couriers/get_courier_list'); ?>",
                "type": "POST",
                "dataType": "json",
                "data": function(d) {
                    d.filter_type = $('#filterType').val();
                    d.filter_status = $('#filterStatus').val();
                    d.date_from = $('#filterDateFrom').val();
                    d.date_to = $('#filterDateTo').val();
                }
            },
            "columns": [
                { "data": 0 },
                { "data": 1 },
                { "data": 2 },
                { "data": 3 },
                { "data": 4 },
                { "data": 5 },
                { "data": 6, "orderable": false }
            ],
            "order": [[3, 'desc']],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json",
                "processing": "Chargement en cours...",
                "search": "Rechercher :",
                "lengthMenu": "Afficher _MENU_ éléments",
                "info": "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
                "infoEmpty": "Affichage de 0 à 0 sur 0 élément",
                "infoFiltered": "(filtré de _MAX_ éléments au total)",
                "zeroRecords": "Aucun courrier trouvé",
                "emptyTable": "Aucun courrier enregistré"
            },
            "responsive": true,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
            "drawCallback": function() {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    }

    // ========================================== //
    // FONCTIONS UTILITAIRES                      //
    // ========================================== //

    function getCurrentDate() {
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();
        return dd + '/' + mm + '/' + yyyy;
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        var parts = dateStr.split('-');
        if (parts.length === 3) {
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }
        return dateStr;
    }

    // ========================================== //
    // OUVERTURE MODAL - AJOUT                    //
    // ========================================== //
    function openAddModal() {
        $('#formModalIcon').removeClass('fa-pencil-square-o').addClass('fa-envelope');
        $('#formTitleText').text('Nouveau courrier');
        $('#formModalSubtitle').text('Enregistrez un nouveau courrier');
        $('#formSubmitText').text('Enregistrer');
        $('#formSubmitBtn').removeClass('btn-warning').addClass('btn-success');
        $('#courierForm').attr('action', '<?php echo site_url('admin/couriers/add_ajax'); ?>');
        $('#courierForm')[0].reset();
        $('#edit_id').val('');
        $('#edit_date_received').val(getCurrentDate());
        $('#edit_attachment').val('');
        $('#edit_status').val('pending');
        $('.text-danger').html('');
        $('.form-group').removeClass('has-error');
        $('.form-control').css('border-color', '');
        $('#courierFormModal').modal('show');
    }

    // ========================================== //
    // OUVERTURE MODAL - MODIFICATION (AJAX)      //
    // ========================================== //
    function openEditModal(id) {
        showSpinner();

        Swal.fire({
            title: 'Chargement...',
            text: 'Récupération des données du courrier',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        var url = '<?php echo base_url(); ?>admin/couriers/get_courier_data/' + id;

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                hideSpinner();
                Swal.close();

                if (response.success) {
                    var data = response.courier;
                    fillEditForm(data);
                    $('#courierFormModal').modal('show');
                } else {
                    showError(response.message || 'Impossible de charger les données du courrier');
                }
            },
            error: function(xhr, status, error) {
                hideSpinner();
                Swal.close();
                handleAjaxError(xhr, status, error);
            }
        });
    }

    // ========================================== //
    // REMPLIR LE FORMULAIRE D'ÉDITION            //
    // ========================================== //
    function fillEditForm(data) {
        $('#formModalIcon').removeClass('fa-envelope').addClass('fa-pencil-square-o');
        $('#formTitleText').text('Modifier le courrier');
        $('#formModalSubtitle').text('Mettez à jour les informations du courrier');
        $('#formSubmitText').text('Mettre à jour');
        $('#formSubmitBtn').removeClass('btn-success').addClass('btn-warning');
        $('#courierForm').attr('action', '<?php echo site_url('admin/couriers/update_ajax'); ?>');

        $('#edit_id').val(data.id);
        $('#edit_courier_type').val(data.courier_type || '');
        $('#edit_sender_name').val(data.sender_name || '');
        $('#edit_reference').val(data.reference || '');
        $('#edit_address').val(data.address || '');
        $('#edit_description').val(data.description || '');
        $('#edit_note').val(data.note || '');
        $('#edit_status').val(data.status || 'pending');

        if (data.date_received) {
            $('#edit_date_received').val(formatDate(data.date_received));
        } else {
            $('#edit_date_received').val(getCurrentDate());
        }

        $('.text-danger').html('');
        $('.form-group').removeClass('has-error');
        $('.form-control').css('border-color', '');
    }

    // ========================================== //
    // SOUMISSION DU FORMULAIRE - AJOUT (AJAX)    //
    // ========================================== //
    function submitAddForm(form) {
        if (!validateForm()) {
            return false;
        }

        showSpinner();

        var formData = new FormData(form[0]);

        $.ajax({
            url: '<?php echo site_url('admin/couriers/add_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner();

                if (response.success) {
                    $('#courierFormModal').modal('hide');
                    showSuccess(response.message || 'Courrier ajouté avec succès');
                    $('#courierTable').DataTable().ajax.reload(null, false);
                } else {
                    showError(response.message || 'Une erreur est survenue lors de l\'ajout');
                }
            },
            error: function(xhr, status, error) {
                hideSpinner();
                handleAjaxError(xhr, status, error);
            }
        });
    }

    // ========================================== //
    // SOUMISSION DU FORMULAIRE - ÉDITION (AJAX)  //
    // ========================================== //
    function submitEditForm(form) {
        if (!validateForm()) {
            return false;
        }

        showSpinner();

        var formData = new FormData(form[0]);

        $.ajax({
            url: '<?php echo site_url('admin/couriers/update_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner();

                if (response.success) {
                    $('#courierFormModal').modal('hide');
                    showSuccess(response.message || 'Courrier mis à jour avec succès');
                    $('#courierTable').DataTable().ajax.reload(null, false);
                } else {
                    showError(response.message || 'Une erreur est survenue lors de la mise à jour');
                }
            },
            error: function(xhr, status, error) {
                hideSpinner();
                handleAjaxError(xhr, status, error);
            }
        });
    }

    // ========================================== //
    // VALIDATION DU FORMULAIRE                   //
    // ========================================== //
    function validateForm() {
        var isValid = true;

        $('.text-danger').html('');
        $('.form-group').removeClass('has-error');
        $('.form-control').css('border-color', '');

        var courierType = $('#edit_courier_type').val().trim();
        if (courierType === '') {
            $('#edit_courier_type').css('border-color', '#ef4444');
            $('#edit_courier_type').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez renseigner le type de courrier');
            return false;
        }

        var senderName = $('#edit_sender_name').val().trim();
        if (senderName === '') {
            $('#edit_sender_name').css('border-color', '#ef4444');
            $('#edit_sender_name').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez renseigner le nom');
            return false;
        }

        var date = $('#edit_date_received').val();
        if (date === '' || date === 'dd/mm/yyyy') {
            $('#edit_date_received').css('border-color', '#ef4444');
            $('#edit_date_received').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez sélectionner une date');
            return false;
        }

        return isValid;
    }

    // ========================================== //
    // FILTRES                                    //
    // ========================================== //
    function applyFilters() {
        $('#courierTable').DataTable().ajax.reload();
    }

    function resetFilters() {
        document.getElementById('filterType').value = '';
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterDateFrom').value = '';
        document.getElementById('filterDateTo').value = '';
        applyFilters();
    }

    // ========================================== //
    // EXPORTATION                                //
    // ========================================== //
    function exportData(type) {
        const filterType = document.getElementById('filterType').value;
        const filterStatus = document.getElementById('filterStatus').value;
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        const params = `?type=${encodeURIComponent(filterType)}&status=${encodeURIComponent(filterStatus)}&date_from=${encodeURIComponent(dateFrom)}&date_to=${encodeURIComponent(dateTo)}`;

        showSpinner();

        var url = '<?php echo base_url("admin/couriers/export_"); ?>' + type + params;
        window.location.href = url;

        setTimeout(function() {
            hideSpinner();
        }, 3000);
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    function confirmDelete(event, name, id) {
        event.preventDefault();

        Swal.fire({
            title: 'Confirmation de suppression',
            text: 'Supprimer définitivement le courrier de "' + name + '" ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                showSpinner();
                window.location.href = '<?php echo base_url("admin/couriers/delete/"); ?>' + id;
            }
        });
    }

    // ========================================== //
    // DÉTAILS D'UN COURRIER (MODAL)              //
    // ========================================== //
    function getRecord(id) {
        showSpinner();

        $.ajax({
            url: '<?php echo base_url(); ?>admin/couriers/details/' + id,
            success: function(result) {
                hideSpinner();
                $('#getdetails').html(result);
                $('#courierdetails').modal('show');
            },
            error: function() {
                hideSpinner();
                showError('Impossible de charger les détails du courrier');
            }
        });
    }

    // ========================================== //
    // GESTION DU SPINNER                         //
    // ========================================== //
    function showSpinner() {
        $('#spinnerOverlay').addClass('active');
    }

    function hideSpinner() {
        $('#spinnerOverlay').removeClass('active');
    }

    // ========================================== //
    // NOTIFICATIONS (SweetAlert)                 //
    // ========================================== //
    function showSuccess(message) {
        Swal.fire({
            title: 'Succès !',
            text: message || 'Opération effectuée avec succès',
            icon: 'success',
            timer: 3000,
            showConfirmButton: false,
            position: 'top-end'
        });
    }

    function showError(message) {
        Swal.fire({
            title: 'Erreur',
            text: message || 'Une erreur est survenue',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3b82f6'
        });
    }

    // ========================================== //
    // GESTION DES ERREURS AJAX                   //
    // ========================================== //
    function handleAjaxError(xhr, status, error) {
        console.error('=== ERREUR AJAX DÉTAILLÉE ===');
        console.error('Status:', status);
        console.error('Erreur:', error);
        console.error('Status code:', xhr.status);

        var errorMsg = 'Une erreur est survenue lors de la communication avec le serveur.';

        if (xhr.status === 404) {
            errorMsg = 'La page demandée est introuvable.';
        } else if (xhr.status === 500) {
            errorMsg = 'Erreur interne du serveur. Veuillez réessayer plus tard.';
        } else if (xhr.status === 403) {
            errorMsg = 'Vous n\'avez pas les droits nécessaires pour effectuer cette action.';
        } else if (xhr.status === 0) {
            errorMsg = 'Impossible de se connecter au serveur. Vérifiez votre connexion.';
        }

        if (xhr.responseText) {
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.message) {
                    errorMsg = response.message;
                }
            } catch (e) {
                console.error('Réponse brute:', xhr.responseText.substring(0, 200));
            }
        }

        showError(errorMsg);
    }
</script>