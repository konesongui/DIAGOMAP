<!-- ============================================================
     PAGE : Gestion des courriers dispatch (version modernisée)
     DESCRIPTION : Interface moderne avec formulaire en modal
     ============================================================ -->

<style>
    /* Variables et couleurs */
    :root {
        --primary-dark: #273772;
        --primary-light: #273772;
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
       /* border-left: 5px solid var(--primary-light);*/
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
        margin-top: -49px;
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
        margin-top: -59px;
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

    /* Style pour le bouton de parcours */
    .custom-file-upload .btn-primary {
        transition: all 0.3s ease;
    }

    .custom-file-upload .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
    }

    .custom-file-upload .btn-primary:active {
        transform: translateY(0);
    }

    /* Animation pour le nom du fichier */
    #file_name_display {
        transition: all 0.3s ease;
    }

    #file_name_display .fa {
        transition: all 0.3s ease;
    }

    /* Style pour le bouton de suppression */
    #clear_file_btn {
        transition: all 0.3s ease;
    }

    #clear_file_btn:hover {
        background: #fee2e2 !important;
        transform: scale(1.05);
    }

    /* Drag and drop */
    .file-upload-wrapper .drop-zone.dragover {
        border-color: #3b82f6;
        background: #eff6ff;
        transform: scale(1.02);
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
                            <i class="fa fa-ioxhost"></i> <?php echo $this->lang->line('postal_dispatch'); ?>
                        </h3>
                        <div class="d-flex align-items-center gap-3" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url(); ?>admin/admin/hub" class="btn-back" title="Retour au tableau de bord">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <?php if ($this->rbac->hasPrivilege('postal_dispatch', 'can_add')) : ?>
                                <button type="button" class="btn-add-modern" onclick="openAddModal()">
                                    <i class="fa fa-plus-circle" style="margin-right: 6px;"></i> <?php echo $this->lang->line('add'); ?>
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
                            $stats = $stats ?? ['total' => 0, 'today' => 0, 'pending' => 0, 'processed' => 0];
                            ?>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-envelope"></i> Total dispatch</h4>
                                    <p class="number"><?php echo $stats['total'] ?? 0; ?></p>
                                </div>

                            </div>

                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-calendar-day" style="color:#10b981;"></i> Aujourd'hui</h4>
                                    <p class="number"><?php echo $stats['today'] ?? 0; ?></p>
                                </div>

                            </div>

                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-hourglass-half" style="color:#f59e0b;"></i> En attente</h4>
                                    <p class="number"><?php echo $stats['pending'] ?? 0; ?></p>
                                </div>

                            </div>

                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-check-circle" style="color:#3b82f6;"></i> Traités</h4>
                                    <p class="number"><?php echo $stats['processed'] ?? 0; ?></p>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <?php if ($this->session->flashdata('msg')) : ?>
                            <!--<div class="alert alert-success alert-dismissible fade in">
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
                                <label><i class="fa fa-filter"></i> <?php echo $this->lang->line('from_title'); ?> :</label>
                                <select id="filterFrom" onchange="applyFilters()">
                                    <option value="">Tous</option>
                                    <?php if (!empty($DispatchList)) : ?>
                                        <?php foreach ($DispatchList as $item) : ?>
                                            <option value="<?php echo htmlspecialchars($item->from_title); ?>"><?php echo htmlspecialchars($item->from_title); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label><i class="fa fa-calendar"></i> Période :</label>
                                <input type="date" id="filterDateFrom" onchange="applyFilters()" placeholder="Du">
                                <span style="color:#94a3b8;font-size:13px;">→</span>
                                <input type="date" id="filterDateTo" onchange="applyFilters()" placeholder="Au">
                            </div>
                            <div style="display:flex;gap:6px;margin-left:auto;flex-wrap:wrap;">
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
                            <table class="table table-modern example" id="dispatchTable">
                                <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('to_title'); ?></th>
                                    <th><?php echo $this->lang->line('reference_no'); ?></th>
                                    <th><?php echo $this->lang->line('from_title'); ?></th>
                                    <th><?php echo $this->lang->line('date'); ?></th>
                                    <th style="text-align: center; width: 120px;"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                                </thead>
                                <tbody id="dispatchTableBody">
                                <?php if (empty($DispatchList)) : ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted" style="padding: 40px 0;">
                                            <i class="fa fa-envelope" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
                                            <p style="font-size: 16px; color: #64748b;">Aucun dispatch enregistré</p>
                                            <p style="font-size: 13px; color: #94a3b8;">Commencez par ajouter un nouveau dispatch</p>
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($DispatchList as $value) : ?>
                                        <tr data-from="<?php echo htmlspecialchars($value->from_title ?? ''); ?>"
                                            data-date="<?php echo $value->date ?? ''; ?>"
                                            data-id="<?php echo $value->id; ?>">

                                            <td><?php echo htmlspecialchars($value->to_title); ?></td>

                                            <td><?php echo htmlspecialchars($value->reference_no); ?></td>

                                            <td><?php echo htmlspecialchars($value->from_title); ?></td>

                                            <td><?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date)); ?></td>

                                            <td style="text-align: center;">
                                                <div class="dropdown">
                                                    <button class="btn-action-dropdown dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                        Actions <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu actions-menu dropdown-menu-right">
                                                        <!-- Voir détails -->
                                                        <li>
                                                            <a onclick="getRecord(<?php echo $value->id; ?>)" style="cursor: pointer;">
                                                                <i class="fa fa-eye" style="color: #8b5cf6;"></i> Voir détails
                                                            </a>
                                                        </li>
                                                        <li role="separator" class="divider"></li>

                                                        <!-- Télécharger le document -->
                                                        <?php if (!empty($value->image)) : ?>
                                                            <li>
                                                                <a href="<?php echo base_url('admin/dispatch/download/' . $value->image); ?>">
                                                                    <i class="fa fa-download" style="color: #10b981;"></i> Télécharger
                                                                </a>
                                                            </li>
                                                            <li role="separator" class="divider"></li>
                                                        <?php endif; ?>

                                                        <!-- Modifier -->
                                                        <?php if ($this->rbac->hasPrivilege('postal_dispatch', 'can_edit')) : ?>
                                                            <li>
                                                                <a onclick="openEditModal(<?php echo $value->id; ?>)">
                                                                    <i class="fa fa-pencil" style="color: #3b82f6;"></i> Modifier
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>

                                                        <!-- Supprimer -->
                                                        <?php if ($this->rbac->hasPrivilege('postal_dispatch', 'can_delete')) : ?>
                                                            <li>
                                                                <a href="#" class="text-danger" onclick="confirmDelete(event, <?php echo $value->id; ?>, '<?php echo htmlspecialchars($value->image ?? ''); ?>')">
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
<!-- ========================================== -->
<!-- MODAL - FORMULAIRE DISPATCH (AVEC CROIX)    -->
<!-- ========================================== -->
<div id="dispatchFormModal" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-md" style="max-width: 720px;">
        <div class="modal-content">
            <!-- ===== EN-TÊTE AVEC CROIX DE FERMETURE ===== -->
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: linear-gradient(135deg, #1e293b, #0f172a); border-radius: 8px 8px 0 0;">
                <div>
                    <h4 id="formModalTitle" style="margin: 0; color: white; font-size: 18px; font-weight: 600;">
                        <i class="fa fa-plus-circle" id="formModalIcon" style="color: #60a5fa;"></i>
                        <span id="formTitleText">Ajouter un dispatch</span>
                    </h4>
                    <div class="modal-subtitle" id="formModalSubtitle" style="color: rgba(255,255,255,0.7); font-size: 12px; margin-top: 2px;">
                        Remplissez les informations ci-dessous
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal"
                        style="color: white; opacity: 0.8; font-size: 28px; text-shadow: none; background: none; border: none; padding: 0; margin: 0; line-height: 1; cursor: pointer; transition: all 0.3s ease;"
                        onmouseover="this.style.opacity='1'; this.style.transform='rotate(90deg)';"
                        onmouseout="this.style.opacity='0.8'; this.style.transform='rotate(0)';">
                    &times;
                </button>
            </div>

            <!-- ===== FORMULAIRE ===== -->
            <form id="dispatchForm" action="<?php echo site_url('admin/dispatch'); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" id="edit_id" value="">
                <div class="modal-body" style="padding: 20px 24px; background: #fafcff;">

                    <!-- Informations du dispatch -->
                    <div style="background: #f8fafc; padding: 15px; border-radius: 6px; margin-bottom: 16px;">
                        <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px;">
                            <i class="fa fa-info-circle" style="margin-right: 8px; color: #3B82F6;"></i> INFORMATIONS DU DISPATCH
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;"><?php echo $this->lang->line('to_title'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_to_title" name="to_title" placeholder="Destinataire" style="height: 38px; font-size: 13px;" required>
                                    <span class="text-danger"><?php echo form_error('to_title'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;"><?php echo $this->lang->line('reference_no'); ?></label>
                                    <input type="text" class="form-control" id="edit_ref_no" name="ref_no" placeholder="Numéro de référence" style="height: 38px; font-size: 13px;">
                                    <span class="text-danger"><?php echo form_error('ref_no'); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;"><?php echo $this->lang->line('from_title'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_from" name="from" placeholder="Expéditeur" style="height: 38px; font-size: 13px;" required>
                                    <span class="text-danger"><?php echo form_error('from'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;"><?php echo $this->lang->line('date'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control date" id="edit_date" name="date" readonly required style="height: 38px; font-size: 13px;">
                                    <span class="text-danger"><?php echo form_error('date'); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;"><?php echo $this->lang->line('address'); ?></label>
                                    <textarea class="form-control" id="edit_address" name="address" rows="2" placeholder="Adresse complète" style="font-size: 13px; resize: vertical; min-height: 50px;"></textarea>
                                    <span class="text-danger"><?php echo form_error('address'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Note et document -->
                    <div style="background: #eff6ff; padding: 15px; border-radius: 6px; margin-bottom: 0; border-left: 4px solid #3B82F6;">
                        <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dbeafe; padding-bottom: 6px;">
                            <i class="fa fa-file-text" style="margin-right: 8px; color: #3B82F6;"></i> DÉTAILS SUPPLÉMENTAIRES
                        </h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;"><?php echo $this->lang->line('note'); ?></label>
                                    <textarea class="form-control" id="edit_note" name="note" rows="2" placeholder="Notes supplémentaires..." style="font-size: 13px; resize: vertical; min-height: 50px;"></textarea>
                                    <span class="text-danger"><?php echo form_error('note'); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-weight: 600; font-size: 12px; color: #334155;">
                                <i class="fa fa-paperclip" style="margin-right: 6px; color: #3b82f6;"></i>
                                <?php echo $this->lang->line('attach_document'); ?>
                            </label>
                            <div class="custom-file-upload" style="display: flex; align-items: center; gap: 12px;">
                                <button type="button" class="btn btn-primary"
                                        style="background: #3b82f6; border: none; border-radius: 8px; padding: 8px 20px; color: #fff; font-size: 13px; display: flex; align-items: center; gap: 8px; white-space: nowrap; transition: all 0.3s ease;"
                                        onmouseover="this.style.background='#2563eb'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 16px rgba(59,130,246,0.3)';"
                                        onmouseout="this.style.background='#3b82f6'; this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                                        onclick="document.getElementById('edit_file').click();">
                                    <i class="fa fa-cloud-upload"></i> Parcourir
                                </button>
                                <span id="file_name_display" style="font-size: 13px; color: #64748b; flex: 1; padding: 6px 12px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; min-height: 38px; display: flex; align-items: center;">
                                    <i class="fa fa-file-o" style="color: #94a3b8; margin-right: 8px;"></i>
                                    Aucun fichier sélectionné
                                </span>
                                <button type="button" class="btn btn-sm btn-danger"
                                        style="border: none; border-radius: 8px; padding: 8px 14px; color: #dc2626; background: #fef2f2; display: none; transition: all 0.3s ease;"
                                        onmouseover="this.style.background='#fee2e2'; this.style.transform='scale(1.05)';"
                                        onmouseout="this.style.background='#fef2f2'; this.style.transform='scale(1)';"
                                        id="clear_file_btn"
                                        onclick="clearFile()">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                            <input class="form-control" type="file" name="file" id="edit_file"
                                   style="display: none;"
                                   onchange="updateFileDisplay(this)">
                            <span class="text-danger"><?php echo form_error('file'); ?></span>
                            <small style="color: #94a3b8; font-size: 11px; display: block; margin-top: 4px;">
                                <i class="fa fa-info-circle"></i> Formats: PDF, DOC, DOCX, JPG, PNG | Taille max: 5MB
                            </small>
                        </div>

                        <script type="text/javascript">
                            function updateFileDisplay(input) {
                                var display = document.getElementById('file_name_display');
                                var clearBtn = document.getElementById('clear_file_btn');
                                var file = input.files[0];

                                if (file) {
                                    var iconMap = {
                                        'pdf': 'fa-file-pdf-o',
                                        'doc': 'fa-file-word-o',
                                        'docx': 'fa-file-word-o',
                                        'jpg': 'fa-file-image-o',
                                        'jpeg': 'fa-file-image-o',
                                        'png': 'fa-file-image-o',
                                        'gif': 'fa-file-image-o',
                                        'xls': 'fa-file-excel-o',
                                        'xlsx': 'fa-file-excel-o',
                                        'zip': 'fa-file-archive-o',
                                        'rar': 'fa-file-archive-o'
                                    };
                                    var ext = file.name.split('.').pop().toLowerCase();
                                    var iconClass = iconMap[ext] || 'fa-file-o';

                                    var size = (file.size / 1024).toFixed(1);
                                    if (size > 1024) {
                                        size = (size / 1024).toFixed(1) + ' MB';
                                    } else {
                                        size = size + ' KB';
                                    }

                                    display.innerHTML = '<i class="fa ' + iconClass + '" style="color: #3b82f6; margin-right: 8px;"></i> ' +
                                        file.name + ' <span style="color: #94a3b8; font-size: 11px;">(' + size + ')</span>';
                                    display.style.color = '#1e293b';
                                    clearBtn.style.display = 'inline-block';
                                } else {
                                    display.innerHTML = '<i class="fa fa-file-o" style="color: #94a3b8; margin-right: 8px;"></i> Aucun fichier sélectionné';
                                    display.style.color = '#64748b';
                                    clearBtn.style.display = 'none';
                                }
                            }

                            function clearFile() {
                                document.getElementById('edit_file').value = '';
                                updateFileDisplay(document.getElementById('edit_file'));
                            }
                        </script>
                    </div>
                </div>

                <!-- ===== FOOTER ===== -->
                <div class="modal-footer" style="padding: 14px 24px 20px; border-top: 1px solid #eef2f6; background: #ffffff; border-radius: 0 0 8px 8px; display: flex; gap: 8px; flex-wrap: wrap;">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px; padding: 8px 20px; font-weight: 500; font-size: 13px; background: #f1f5f9; color: #475569; border: none; transition: all 0.3s ease;" onmouseover="this.style.background='#e2e8f0';" onmouseout="this.style.background='#f1f5f9';">
                        <i class="fa fa-times"></i> Annuler
                    </button>
                    <button type="reset" class="btn btn-warning" style="border-radius: 6px; padding: 8px 20px; font-weight: 500; font-size: 13px; background: #fef3c7; color: #92400e; border: none; transition: all 0.3s ease;" onmouseover="this.style.background='#fde68a';" onmouseout="this.style.background='#fef3c7';">
                        <i class="fa fa-refresh"></i> Réinitialiser
                    </button>
                    <button type="submit" class="btn btn-success" id="formSubmitBtn" style="border-radius: 6px; padding: 8px 24px; font-weight: 500; font-size: 13px; background: linear-gradient(135deg, #273772, #1a2558); color: #ffffff; border: none; box-shadow: 0 4px 12px rgba(39, 55, 114, 0.3); transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 6px 20px rgba(39,55,114,0.4)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 4px 12px rgba(39,55,114,0.3)'; this.style.transform='translateY(0)';">
                        <i class="fa fa-save"></i> <span id="formSubmitText"><?php echo $this->lang->line('save'); ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Animation pour la croix de fermeture */
    .modal-chic .close {
        transition: all 0.3s ease;
        font-size: 28px;
        color: white;
        opacity: 0.8;
        background: none;
        border: none;
        padding: 0;
        margin: 0;
        line-height: 1;
        cursor: pointer;
    }

    .modal-chic .close:hover {
        opacity: 1;
        transform: rotate(90deg);
        color: #60a5fa;
    }

    /* Animation d'apparition du modal */
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

    .modal-chic .modal-content {
        animation: modalSlideUp 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    /* Style du bouton de fermeture */
    .modal-header .close {
        position: relative;
        z-index: 10;
    }

    .modal-header .close:focus {
        outline: none;
    }
</style>

<!-- ========================================== -->
<!-- MODAL DÉTAILS                              -->
<!-- ========================================== -->
<div id="receviedetails" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 800px;">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; border-radius: 8px 8px 0 0;">
                <h4 style="margin: 0; color: white;">
                    <i class="fa fa-info-circle" style="color: #60a5fa;"></i> <?php echo $this->lang->line('details'); ?>
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

        // Focus effect pour les champs du modal
        $('.modal-chic .form-control').on('focus', function() {
            $(this).closest('.form-group').addClass('focused');
        }).on('blur', function() {
            $(this).closest('.form-group').removeClass('focused');
        });

        // Reset du formulaire à la fermeture du modal
        $('#dispatchFormModal').on('hidden.bs.modal', function() {
            $('#dispatchForm')[0].reset();
            $('#edit_id').val('');
            $('#edit_file').val('');
            $('#edit_date').val(getCurrentDate());
            $('.text-danger').html('');
            $('.form-group').removeClass('has-error');
            $('.form-control').css('border-color', '');
        });

        // Intercepter la soumission du formulaire
        $('#dispatchForm').on('submit', function(e) {
            e.preventDefault();
            var editId = $('#edit_id').val();
            if (editId && editId !== '') {
                submitEditForm($(this));
            } else {
                submitAddForm($(this));
            }
        });

        // Appliquer les filtres automatiquement
        $('#filterFrom, #filterDateFrom, #filterDateTo').on('change', function() {
            applyFilters();
        });
    });

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
        $('#formModalIcon').removeClass('fa-pencil-square-o').addClass('fa-plus-circle');
        $('#formTitleText').text('<?php echo $this->lang->line('add'); ?> <?php echo $this->lang->line('postal_dispatch'); ?>');
        $('#formModalSubtitle').text('Remplissez les informations ci-dessous');
        $('#formSubmitText').text('<?php echo $this->lang->line('save'); ?>');
        $('#formSubmitBtn').removeClass('btn-warning').addClass('btn-success');
        $('#dispatchForm').attr('action', '<?php echo site_url('admin/dispatch'); ?>');
        $('#dispatchForm')[0].reset();
        $('#edit_id').val('');
        $('#edit_date').val(getCurrentDate());
        $('#edit_file').val('');
        $('.text-danger').html('');
        $('.form-group').removeClass('has-error');
        $('.form-control').css('border-color', '');
        $('#dispatchFormModal').modal('show');
    }

    // ========================================== //
    // OUVERTURE MODAL - MODIFICATION (AJAX)      //
    // ========================================== //
    function openEditModal(id) {
        showSpinner();

        Swal.fire({
            title: 'Chargement...',
            text: 'Récupération des données du dispatch',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        var url = '<?php echo base_url(); ?>admin/dispatch/get_dispatch_data/' + id;

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                hideSpinner();
                Swal.close();

                if (response.success) {
                    var data = response.dispatch;
                    fillEditForm(data);
                    $('#dispatchFormModal').modal('show');
                } else {
                    showError(response.message || 'Impossible de charger les données du dispatch');
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
        $('#formModalIcon').removeClass('fa-plus-circle').addClass('fa-pencil-square-o');
        $('#formTitleText').text('<?php echo $this->lang->line('edit'); ?> <?php echo $this->lang->line('postal_dispatch'); ?>');
        $('#formModalSubtitle').text('Mettez à jour les informations du dispatch');
        $('#formSubmitText').text('<?php echo $this->lang->line('update'); ?>');
        $('#formSubmitBtn').removeClass('btn-success').addClass('btn-warning');
        $('#dispatchForm').attr('action', '<?php echo site_url('admin/dispatch/update_ajax'); ?>');

        $('#edit_id').val(data.id);
        $('#edit_to_title').val(data.to_title || '');
        $('#edit_ref_no').val(data.reference_no || '');
        $('#edit_from').val(data.from_title || '');
        $('#edit_address').val(data.address || '');
        $('#edit_note').val(data.note || '');

        if (data.date) {
            $('#edit_date').val(formatDate(data.date));
        } else {
            $('#edit_date').val(getCurrentDate());
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
            url: '<?php echo site_url('admin/dispatch/add_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner();

                if (response.success) {
                    $('#dispatchFormModal').modal('hide');
                    showSuccess(response.message || 'Dispatch ajouté avec succès');

                    setTimeout(function() {
                        location.reload();
                    }, 1500);
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
            url: '<?php echo site_url('admin/dispatch/update_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner();

                if (response.success) {
                    $('#dispatchFormModal').modal('hide');
                    showSuccess(response.message || 'Dispatch mis à jour avec succès');

                    setTimeout(function() {
                        location.reload();
                    }, 1500);
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

        var toTitle = $('#edit_to_title').val().trim();
        if (toTitle === '') {
            $('#edit_to_title').css('border-color', '#ef4444');
            $('#edit_to_title').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez renseigner le destinataire');
            return false;
        }

        var from = $('#edit_from').val().trim();
        if (from === '') {
            $('#edit_from').css('border-color', '#ef4444');
            $('#edit_from').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez renseigner l\'expéditeur');
            return false;
        }

        var date = $('#edit_date').val();
        if (date === '' || date === 'dd/mm/yyyy') {
            $('#edit_date').css('border-color', '#ef4444');
            $('#edit_date').closest('.form-group').addClass('has-error');
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
        const from = document.getElementById('filterFrom').value.toLowerCase();
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        const rows = document.querySelectorAll('#dispatchTableBody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            if (row.cells.length === 1 && row.cells[0].colSpan === 5) return;

            const rowFrom = (row.dataset.from || '').toLowerCase();
            const rowDate = row.dataset.date || '';

            let show = true;

            if (from && rowFrom !== from) show = false;
            if (dateFrom && rowDate < dateFrom) show = false;
            if (dateTo && rowDate > dateTo) show = false;

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        let noResult = document.getElementById('noResultMessage');
        if (!noResult) {
            noResult = document.createElement('tr');
            noResult.id = 'noResultMessage';
            noResult.innerHTML = '<td colspan="5" class="text-center text-muted" style="padding:40px 0;">' +
                '<i class="fa fa-envelope" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>' +
                '<p style="font-size: 16px; color: #64748b;">Aucun dispatch ne correspond aux filtres</p>' +
                '<p style="font-size: 13px; color: #94a3b8;">Essayez de modifier vos critères de recherche</p>' +
                '</td>';
            document.querySelector('#dispatchTableBody').appendChild(noResult);
        }
        noResult.style.display = visibleCount === 0 ? '' : 'none';
    }

    function resetFilters() {
        document.getElementById('filterFrom').value = '';
        document.getElementById('filterDateFrom').value = '';
        document.getElementById('filterDateTo').value = '';
        applyFilters();
    }

    // ========================================== //
    // EXPORTATION                                //
    // ========================================== //
    function exportData(type) {
        const filterFrom = document.getElementById('filterFrom').value;
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        const params = `?from=${encodeURIComponent(filterFrom)}&date_from=${encodeURIComponent(dateFrom)}&date_to=${encodeURIComponent(dateTo)}`;

        showSpinner();

        var url = '<?php echo base_url("admin/dispatch/export_"); ?>' + type + params;
        window.location.href = url;

        setTimeout(function() {
            hideSpinner();
        }, 3000);
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    function confirmDelete(event, id, image) {
        event.preventDefault();

        let deleteUrl = '<?php echo base_url("admin/dispatch/delete/"); ?>' + id;
        if (image && image !== '') {
            deleteUrl = '<?php echo base_url("admin/dispatch/imagedelete/"); ?>' + id + '/' + image;
        }

        Swal.fire({
            title: 'Confirmation de suppression',
            text: 'Supprimer définitivement ce dispatch ?',
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
                window.location.href = deleteUrl;
            }
        });
    }

    // ========================================== //
    // DÉTAILS D'UN DISPATCH (MODAL)              //
    // ========================================== //
    function getRecord(id) {
        showSpinner();

        $.ajax({
            url: '<?php echo base_url(); ?>admin/dispatch/details/' + id + '/dispatch',
            success: function(result) {
                hideSpinner();
                $('#getdetails').html(result);
                $('#receviedetails').modal('show');
            },
            error: function() {
                hideSpinner();
                showError('Impossible de charger les détails du dispatch');
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

<script type="text/javascript">
    function updateFileDisplay(input) {
        var display = document.getElementById('file_name_display');
        var clearBtn = document.getElementById('clear_file_btn');
        var file = input.files[0];

        if (file) {
            var iconMap = {
                'pdf': 'fa-file-pdf-o',
                'doc': 'fa-file-word-o',
                'docx': 'fa-file-word-o',
                'jpg': 'fa-file-image-o',
                'jpeg': 'fa-file-image-o',
                'png': 'fa-file-image-o',
                'gif': 'fa-file-image-o',
                'xls': 'fa-file-excel-o',
                'xlsx': 'fa-file-excel-o',
                'zip': 'fa-file-archive-o',
                'rar': 'fa-file-archive-o'
            };
            var ext = file.name.split('.').pop().toLowerCase();
            var iconClass = iconMap[ext] || 'fa-file-o';

            var size = (file.size / 1024).toFixed(1);
            if (size > 1024) {
                size = (size / 1024).toFixed(1) + ' MB';
            } else {
                size = size + ' KB';
            }

            display.innerHTML = '<i class="fa ' + iconClass + '" style="color: #3b82f6; margin-right: 8px;"></i> ' +
                file.name + ' <span style="color: #94a3b8; font-size: 11px;">(' + size + ')</span>';
            display.style.color = '#1e293b';
            clearBtn.style.display = 'inline-block';
        } else {
            display.innerHTML = '<i class="fa fa-file-o" style="color: #94a3b8; margin-right: 8px;"></i> Aucun fichier sélectionné';
            display.style.color = '#64748b';
            clearBtn.style.display = 'none';
        }
    }

    function clearFile() {
        document.getElementById('edit_file').value = '';
        updateFileDisplay(document.getElementById('edit_file'));
    }
</script>