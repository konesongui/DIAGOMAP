<!-- ============================================================
     PAGE : Gestion des mariages
     DESCRIPTION : Interface moderne pour la gestion des mariages
     ============================================================ -->

<style>
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
        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 8px;
        --transition: all 0.25s ease;
    }

    .content-wrapper {
        background: #f1f5f9;
        padding: 20px 15px;
        min-height: 100vh;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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
        font-size: 20px;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        line-height: 1.2;
    }

    .stat-card .stat-icon {
        font-size: 28px;
        opacity: 0.7;
    }

    .stat-card:nth-child(1) { border-left-color: #8b5cf6; }
    .stat-card:nth-child(2) { border-left-color: #10b981; }
    .stat-card:nth-child(3) { border-left-color: #f59e0b; }
    .stat-card:nth-child(4) { border-left-color: #ef4444; }

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

    .card-modern .card-body {
        padding: 20px 24px;
        background: var(--bg-light);
    }

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
    }

    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
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

    .btn-pdf {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: #ffffff;
        box-shadow: 0 3px 12px rgba(220, 38, 38, 0.3);
    }

    .btn-pdf:hover {
        box-shadow: 0 6px 24px rgba(220, 38, 38, 0.4);
        color: #ffffff;
    }

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

    .badge-status {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }
    .badge-status.planifie { background: #dbeafe; color: #1d4ed8; }
    .badge-status.effectue { background: #d1fae5; color: #065f46; }
    .badge-status.annule { background: #fef2f2; color: #991b1b; }
    .badge-status.reporte { background: #fef3c7; color: #92400e; }

    .badge-type {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }
    .badge-type.civil { background: #f1f5f9; color: #475569; }
    .badge-type.religieux { background: #f3e8ff; color: #7c3aed; }
    .badge-type.traditionnel { background: #fef3c7; color: #92400e; }
    .badge-type.mixte { background: #d1fae5; color: #059669; }

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
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-chic .modal-header .close {
        color: #ffffff;
        opacity: 0.7;
        font-size: 28px;
        text-shadow: none;
        transition: all 0.3s ease;
        padding: 0;
        margin: 0;
        background: rgba(255,255,255,0.1);
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        line-height: 1;
    }

    .modal-chic .modal-header .close:hover {
        opacity: 1;
        transform: rotate(90deg);
        background: rgba(255,255,255,0.2);
    }

    .modal-chic .modal-body {
        padding: 24px 24px 16px;
        background: #fafcff;
    }

    .modal-chic .form-group {
        margin-bottom: 16px;
    }

    .modal-chic .form-group label {
        font-weight: 600;
        font-size: 13px;
        color: var(--text-dark);
        margin-bottom: 4px;
        display: block;
    }

    .modal-chic .form-control {
        border: 2px solid #e2e8f0;
        border-radius: var(--radius-sm);
        padding: 8px 14px;
        font-size: 13px;
        transition: var(--transition);
        background: #ffffff;
        height: 40px;
        color: var(--text-dark);
        width: 100%;
    }

    .modal-chic .form-control:focus {
        border-color: var(--primary-light);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        outline: none;
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

    .modal-chic .modal-footer .btn-warning {
        background: #fef3c7;
        color: #92400e;
        border: none;
    }

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

    .select2-container .select2-selection--single {
        height: 40px !important;
        border-radius: var(--radius-sm) !important;
        border: 2px solid #e2e8f0 !important;
        padding: 4px 12px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .card-modern .card-header { flex-direction: column; align-items: stretch; }
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-bar .filter-group { width: 100%; }
        .filter-bar .filter-group select, .filter-bar .filter-group input { width: 100%; min-width: unset; }
        .export-group { margin-left: 0 !important; width: 100%; flex-wrap: wrap; }
        .export-group .btn-export { flex: 1; justify-content: center; padding: 6px 12px; font-size: 11px; }
        .export-group .export-label { width: 100%; text-align: center; }
        .export-divider { display: none; }
        .table-modern thead th, .table-modern tbody td { padding: 8px 10px; font-size: 12px; }
        .btn-add-modern { width: 100%; text-align: center; }
        .modal-chic .modal-body { padding: 16px; }
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
                <div class="card-modern">
                    <div class="card-header">
                        <h3>
                            <i class="fa fa-heart"></i> Gestion des mariages
                            <span style="font-size: 14px; font-weight: 400; color: #94a3b8; margin-left: 8px;">
                                (<?php echo isset($stats['total']) ? $stats['total'] : 0; ?>)
                            </span>
                        </h3>
                        <div class="d-flex align-items-center gap-3" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url(); ?>admin/admin/hub" class="btn-back" title="Retour au tableau de bord">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <button type="button" class="btn-add-modern" onclick="openAddModal()">
                                <i class="fa fa-plus-circle" style="margin-right: 6px;"></i> Nouveau mariage
                            </button>
                        </div>
                    </div>

                    <!-- ===== STATISTIQUES ===== -->
                    <div style="padding: 20px 24px 0;">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-heart"></i> Total</h4>
                                    <p class="number"><?php echo isset($stats['total']) ? $stats['total'] : 0; ?></p>
                                </div>
                                <div class="stat-icon"><i class="fa fa-heart"></i></div>
                            </div>
                            <div class="stat-card" style="border-left-color: #10b981;">
                                <div class="stat-info">
                                    <h4><i class="fa fa-check-circle" style="color:#10b981;"></i> Effectués</h4>
                                    <p class="number"><?php echo isset($stats['statut_effectue']) ? $stats['statut_effectue'] : 0; ?></p>
                                </div>
                                <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
                            </div>
                            <div class="stat-card" style="border-left-color: #f59e0b;">
                                <div class="stat-info">
                                    <h4><i class="fa fa-clock-o" style="color:#f59e0b;"></i> Planifiés</h4>
                                    <p class="number"><?php echo isset($stats['statut_planifie']) ? $stats['statut_planifie'] : 0; ?></p>
                                </div>
                                <div class="stat-icon"><i class="fa fa-clock-o"></i></div>
                            </div>
                            <div class="stat-card" style="border-left-color: #ef4444;">
                                <div class="stat-info">
                                    <h4><i class="fa fa-file-text-o" style="color:#ef4444;"></i> Certificats</h4>
                                    <p class="number"><?php echo isset($stats['certificats']) ? $stats['certificats'] : 0; ?></p>
                                </div>
                                <div class="stat-icon"><i class="fa fa-file-text-o"></i></div>
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

                        <!-- ===== FILTRES ===== -->
                        <div class="filter-bar">
                            <div class="filter-group">
                                <label><i class="fa fa-filter"></i> Statut :</label>
                                <select id="filterStatus" onchange="applyFilters()">
                                    <option value="">Tous</option>
                                    <?php if (isset($statuses) && is_array($statuses)) : ?>
                                        <?php foreach ($statuses as $key => $label) : ?>
                                            <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label><i class="fa fa-tag"></i> Type :</label>
                                <select id="filterType" onchange="applyFilters()">
                                    <option value="">Tous</option>
                                    <?php if (isset($types) && is_array($types)) : ?>
                                        <?php foreach ($types as $key => $label) : ?>
                                            <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
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

                        <!-- ===== TABLEAU ===== -->
                        <div class="table-responsive">
                            <table class="table table-modern" id="mariagesTable">
                                <thead>
                                <tr>
                                    <th style="width: 8%;">Code</th>
                                    <th style="width: 18%;">Mari</th>
                                    <th style="width: 18%;">Femme</th>
                                    <th style="width: 12%;">Date</th>
                                    <th style="width: 12%;">Lieu</th>
                                    <th style="width: 10%;">Type</th>
                                    <th style="width: 10%;">Statut</th>
                                    <th style="text-align: center; width: 100px;">Action</th>
                                </tr>
                                </thead>
                                <tbody id="mariagesTableBody">
                                <?php if (empty($mariages)) : ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted" style="padding: 60px 0;">
                                            <i class="fa fa-heart" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
                                            <p style="font-size: 16px; color: #64748b;">Aucun mariage enregistré</p>
                                            <p style="font-size: 13px; color: #94a3b8;">Cliquez sur "Nouveau mariage" pour en ajouter un</p>
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($mariages as $index => $mariage) : ?>
                                        <tr data-status="<?php echo isset($mariage['statut']) ? $mariage['statut'] : ''; ?>"
                                            data-type="<?php echo isset($mariage['type_mariage']) ? $mariage['type_mariage'] : ''; ?>"
                                            data-date="<?php echo isset($mariage['date_mariage']) ? $mariage['date_mariage'] : ''; ?>"
                                            data-id="<?php echo isset($mariage['id']) ? $mariage['id'] : ''; ?>">

                                            <td>
                                                    <span style="background: #f1f5f9; padding: 2px 10px; border-radius: 4px; font-size: 11px; color: #475569;">
                                                        <?php echo isset($mariage['code_mariage']) ? htmlspecialchars($mariage['code_mariage']) : ''; ?>
                                                    </span>
                                            </td>

                                            <td>
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: <?php echo isset($mariage['couleur']) ? $mariage['couleur'] : '#8b5cf6'; ?>;"></span>
                                                    <div>
                                                        <strong><?php echo isset($mariage['mari_nom']) ? htmlspecialchars($mariage['mari_nom']) : ''; ?>
                                                            <?php echo isset($mariage['mari_prenom']) ? htmlspecialchars($mariage['mari_prenom']) : ''; ?></strong>
                                                        <?php if (!empty($mariage['mari_code'])) : ?>
                                                            <br>
                                                            <small style="color: #94a3b8; font-size: 10px;">
                                                                <?php echo isset($mariage['mari_code']) ? htmlspecialchars($mariage['mari_code']) : ''; ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <div>
                                                    <strong><?php echo isset($mariage['femme_nom']) ? htmlspecialchars($mariage['femme_nom']) : ''; ?>
                                                        <?php echo isset($mariage['femme_prenom']) ? htmlspecialchars($mariage['femme_prenom']) : ''; ?></strong>
                                                    <?php if (!empty($mariage['femme_code'])) : ?>
                                                        <br>
                                                        <small style="color: #94a3b8; font-size: 10px;">
                                                            <?php echo isset($mariage['femme_code']) ? htmlspecialchars($mariage['femme_code']) : ''; ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <td>
                                                <?php echo !empty($mariage['date_mariage']) ? date('d/m/Y', strtotime($mariage['date_mariage'])) : ''; ?>
                                                <?php if (!empty($mariage['heure_mariage'])) : ?>
                                                    <br>
                                                    <small style="color: #94a3b8; font-size: 11px;">
                                                        <i class="fa fa-clock-o"></i> <?php echo substr($mariage['heure_mariage'], 0, 5); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>

                                            <td><?php echo isset($mariage['lieu_mariage']) ? htmlspecialchars($mariage['lieu_mariage']) : '—'; ?></td>

                                            <td>
                                                    <span class="badge-type <?php echo isset($mariage['type_mariage']) ? $this->mariages_model->get_type_badge($mariage['type_mariage']) : 'religieux'; ?>">
                                                        <?php echo isset($types[$mariage['type_mariage']]) ? $types[$mariage['type_mariage']] : $mariage['type_mariage']; ?>
                                                    </span>
                                            </td>

                                            <td>
                                                    <span class="badge-status <?php echo isset($mariage['statut']) ? $this->mariages_model->get_status_badge($mariage['statut']) : 'planifie'; ?>">
                                                        <?php echo isset($statuses[$mariage['statut']]) ? $statuses[$mariage['statut']] : $mariage['statut']; ?>
                                                    </span>
                                            </td>

                                            <td style="text-align: center;">
                                                <div class="dropdown">
                                                    <button class="btn-action-dropdown dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                        Actions <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu actions-menu dropdown-menu-right">
                                                        <li>
                                                            <a onclick="viewMariage(<?php echo isset($mariage['id']) ? $mariage['id'] : 0; ?>)" style="cursor: pointer;">
                                                                <i class="fa fa-eye" style="color: #8b5cf6;"></i> Voir détails
                                                            </a>
                                                        </li>
                                                        <li role="separator" class="divider"></li>
                                                        <li>
                                                            <a onclick="openEditModal(<?php echo isset($mariage['id']) ? $mariage['id'] : 0; ?>)">
                                                                <i class="fa fa-pencil" style="color: #3b82f6;"></i> Modifier
                                                            </a>
                                                        </li>
                                                        <?php if ($mariage['statut'] == 'effectue' && $mariage['certificat_genere'] == 0) : ?>
                                                            <li>
                                                                <a onclick="genererCertificat(<?php echo isset($mariage['id']) ? $mariage['id'] : 0; ?>, '<?php echo isset($mariage['mari_nom']) ? htmlspecialchars($mariage['mari_nom']) . ' ' . htmlspecialchars($mariage['mari_prenom']) : ''; ?>', '<?php echo isset($mariage['femme_nom']) ? htmlspecialchars($mariage['femme_nom']) . ' ' . htmlspecialchars($mariage['femme_prenom']) : ''; ?>')">
                                                                    <i class="fa fa-file-text-o" style="color: #10b981;"></i> Générer certificat
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        <?php if ($mariage['certificat_genere'] == 1) : ?>
                                                            <li>
                                                                <a href="#" onclick="showCertificat(<?php echo isset($mariage['id']) ? $mariage['id'] : 0; ?>)">
                                                                    <i class="fa fa-file-pdf-o" style="color: #dc2626;"></i> Voir certificat
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        <li>
                                                            <a href="#" class="text-danger" onclick="confirmDelete(event, <?php echo isset($mariage['id']) ? $mariage['id'] : 0; ?>, '<?php echo isset($mariage['code_mariage']) ? htmlspecialchars($mariage['code_mariage']) : ''; ?>')">
                                                                <i class="fa fa-trash"></i> Supprimer
                                                            </a>
                                                        </li>
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

<!-- ===== SPINNER ===== -->
<div class="spinner-overlay" id="spinnerOverlay">
    <div class="spinner-box">
        <i class="fa fa-spinner"></i>
        <p style="margin-top:15px;font-weight:500;color:#1e293b;">Chargement en cours...</p>
    </div>
</div>

<!-- ===== MODAL FORMULAIRE ===== -->
<div id="mariageFormModal" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 id="formModalTitle" style="margin: 0; color: white; font-size: 18px; font-weight: 600;">
                        <i class="fa fa-plus-circle" id="formModalIcon" style="color: #60a5fa;"></i>
                        <span id="formTitleText">Nouveau mariage</span>
                    </h4>
                    <div class="modal-subtitle" id="formModalSubtitle" style="color: rgba(255,255,255,0.7); font-size: 12px; margin-top: 2px;">
                        Enregistrez un nouveau mariage
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form id="mariageForm" method="post">
                <input type="hidden" name="edit_id" id="edit_id" value="">
                <div class="modal-body">
                    <div class="row">
                        <!-- ===== COLONNE GAUCHE - MARI ===== -->
                        <div class="col-md-6">
                            <div style="background: #f8fafc; padding: 15px; border-radius: 6px; margin-bottom: 16px;">
                                <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px;">
                                    <i class="fa fa-male" style="margin-right: 8px; color: #3B82F6;"></i> INFORMATIONS DU MARI
                                </h5>

                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Membre</label>
                                    <select class="form-control select2" id="edit_mari" name="mari_id" style="height: 38px; font-size: 13px; width: 100%;">
                                        <option value="">Sélectionner un membre</option>
                                        <?php if (isset($membres) && is_array($membres)) : ?>
                                            <?php foreach ($membres as $membre) : ?>
                                                <option value="<?php echo $membre['id']; ?>">
                                                    <?php echo htmlspecialchars($membre['nom'] . ' ' . $membre['prenom'] . ' (' . $membre['code_membre'] . ')'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Nom <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="edit_mari_nom" name="mari_nom" placeholder="Nom" style="height: 38px; font-size: 13px;" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Prénom <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="edit_mari_prenom" name="mari_prenom" placeholder="Prénom" style="height: 38px; font-size: 13px;" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Date naissance</label>
                                            <input type="text" class="form-control date" id="edit_mari_date_naissance" name="mari_date_naissance" readonly style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Profession</label>
                                            <input type="text" class="form-control" id="edit_mari_profession" name="mari_profession" placeholder="Profession" style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Téléphone</label>
                                            <input type="text" class="form-control" id="edit_mari_telephone" name="mari_telephone" placeholder="Téléphone" style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Email</label>
                                            <input type="email" class="form-control" id="edit_mari_email" name="mari_email" placeholder="Email" style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Témoins mari -->
                            <div style="background: #eff6ff; padding: 15px; border-radius: 6px; border-left: 4px solid #3B82F6;">
                                <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dbeafe; padding-bottom: 6px;">
                                    <i class="fa fa-users" style="margin-right: 8px; color: #3B82F6;"></i> TÉMOINS DU MARI
                                </h5>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Témoins</label>
                                    <input type="text" class="form-control" id="edit_temoins_mari" name="temoins_mari" placeholder="Noms séparés par des virgules" style="height: 38px; font-size: 13px;">
                                </div>
                            </div>
                        </div>

                        <!-- ===== COLONNE DROITE - FEMME ===== -->
                        <div class="col-md-6">
                            <div style="background: #f8fafc; padding: 15px; border-radius: 6px; margin-bottom: 16px;">
                                <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px;">
                                    <i class="fa fa-female" style="margin-right: 8px; color: #f59e0b;"></i> INFORMATIONS DE LA FEMME
                                </h5>

                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Membre</label>
                                    <select class="form-control select2" id="edit_femme" name="femme_id" style="height: 38px; font-size: 13px; width: 100%;">
                                        <option value="">Sélectionner un membre</option>
                                        <?php if (isset($membres) && is_array($membres)) : ?>
                                            <?php foreach ($membres as $membre) : ?>
                                                <option value="<?php echo $membre['id']; ?>">
                                                    <?php echo htmlspecialchars($membre['nom'] . ' ' . $membre['prenom'] . ' (' . $membre['code_membre'] . ')'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Nom <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="edit_femme_nom" name="femme_nom" placeholder="Nom" style="height: 38px; font-size: 13px;" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Prénom <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="edit_femme_prenom" name="femme_prenom" placeholder="Prénom" style="height: 38px; font-size: 13px;" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Date naissance</label>
                                            <input type="text" class="form-control date" id="edit_femme_date_naissance" name="femme_date_naissance" readonly style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Profession</label>
                                            <input type="text" class="form-control" id="edit_femme_profession" name="femme_profession" placeholder="Profession" style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Téléphone</label>
                                            <input type="text" class="form-control" id="edit_femme_telephone" name="femme_telephone" placeholder="Téléphone" style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Email</label>
                                            <input type="email" class="form-control" id="edit_femme_email" name="femme_email" placeholder="Email" style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Témoins femme -->
                            <div style="background: #f0fdf4; padding: 15px; border-radius: 6px; border-left: 4px solid #10b981;">
                                <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dcfce7; padding-bottom: 6px;">
                                    <i class="fa fa-users" style="margin-right: 8px; color: #10b981;"></i> TÉMOINS DE LA FEMME
                                </h5>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Témoins</label>
                                    <input type="text" class="form-control" id="edit_temoins_femme" name="temoins_femme" placeholder="Noms séparés par des virgules" style="height: 38px; font-size: 13px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ===== SECTION MARIAGE ===== -->
                    <div class="row" style="margin-top: 16px;">
                        <div class="col-md-12">
                            <div style="background: #fef3c7; padding: 15px; border-radius: 6px; border-left: 4px solid #f59e0b;">
                                <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #fde68a; padding-bottom: 6px;">
                                    <i class="fa fa-ring" style="margin-right: 8px; color: #f59e0b;"></i> INFORMATIONS DU MARIAGE
                                </h5>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Date mariage <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control date" id="edit_date_mariage" name="date_mariage" readonly required style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Heure</label>
                                            <input type="time" class="form-control" id="edit_heure_mariage" name="heure_mariage" style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Type de mariage</label>
                                            <select class="form-control" id="edit_type_mariage" name="type_mariage" style="height: 38px; font-size: 13px;">
                                                <?php if (isset($types) && is_array($types)) : ?>
                                                    <?php foreach ($types as $key => $label) : ?>
                                                        <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Lieu du mariage</label>
                                            <input type="text" class="form-control" id="edit_lieu_mariage" name="lieu_mariage" placeholder="Lieu du mariage" style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Pasteur officiant</label>
                                            <input type="text" class="form-control" id="edit_pasteur_mariage" name="pasteur_officiant" placeholder="Nom du pasteur" style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Nombre d'invités</label>
                                            <input type="number" class="form-control" id="edit_invites" name="nombre_invites" placeholder="0" style="height: 38px; font-size: 13px;" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Statut</label>
                                            <select class="form-control" id="edit_statut_mariage" name="statut" style="height: 38px; font-size: 13px;">
                                                <?php if (isset($statuses) && is_array($statuses)) : ?>
                                                    <?php foreach ($statuses as $key => $label) : ?>
                                                        <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Couleur</label>
                                            <input type="color" class="form-control" id="edit_couleur_mariage" name="couleur" value="#8b5cf6" style="height: 38px; padding: 2px; width: 100%;">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Liste des invités</label>
                                            <input type="text" class="form-control" id="edit_liste_invites" name="invites" placeholder="Noms séparés par des virgules" style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Observations</label>
                                            <textarea class="form-control" id="edit_observations_mariage" name="observations" rows="2" placeholder="Observations..." style="font-size: 13px; resize: vertical;"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

<!-- ===== MODAL DÉTAILS ===== -->
<div id="mariageDetailsModal" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; border-radius: 8px 8px 0 0;">
                <h4 style="margin: 0; color: white;">
                    <i class="fa fa-info-circle" style="color: #60a5fa;"></i> Détails du mariage
                </h4>
                <button type="button" class="close" data-dismiss="modal" style="color:white; opacity: 0.8;">&times;</button>
            </div>
            <div class="modal-body" id="detailsContent" style="padding: 24px; background: #fafcff;"></div>
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script type="text/javascript">
    // ========================================== //
    // DOCUMENT READY                             //
    // ========================================== //
    $(document).ready(function() {
        $('.date').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });

        $('.select2').select2({
            placeholder: "Sélectionner un membre",
            allowClear: true,
            dropdownParent: $('#mariageFormModal')
        });

        $('#mariageFormModal').on('hidden.bs.modal', function() {
            $('#mariageForm')[0].reset();
            $('#edit_id').val('');
            $('#edit_statut_mariage').val('planifie');
            $('#edit_type_mariage').val('religieux');
            $('#edit_couleur_mariage').val('#8b5cf6');
            $('.text-danger').html('');
            $('.form-group').removeClass('has-error');
            $('.form-control').css('border-color', '');
            $('.select2').val(null).trigger('change');
        });

        $('#mariageForm').on('submit', function(e) {
            e.preventDefault();
            var editId = $('#edit_id').val();
            if (editId && editId !== '') {
                submitEditForm($(this));
            } else {
                submitAddForm($(this));
            }
        });

        $('#filterStatus, #filterType, #filterDateFrom, #filterDateTo').on('change', function() {
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
        $('#formTitleText').text('Nouveau mariage');
        $('#formModalSubtitle').text('Enregistrez un nouveau mariage');
        $('#formSubmitText').text('Enregistrer');
        $('#formSubmitBtn').removeClass('btn-warning').addClass('btn-success');
        $('#mariageForm').attr('action', '<?php echo site_url('admin/mariages/add_ajax'); ?>');
        $('#mariageForm')[0].reset();
        $('#edit_id').val('');
        $('#edit_statut_mariage').val('planifie');
        $('#edit_type_mariage').val('religieux');
        $('#edit_couleur_mariage').val('#8b5cf6');
        $('#edit_date_mariage').val(getCurrentDate());
        $('.text-danger').html('');
        $('.form-group').removeClass('has-error');
        $('.form-control').css('border-color', '');
        $('.select2').val(null).trigger('change');
        $('#mariageFormModal').modal('show');
    }

    // ========================================== //
    // OUVERTURE MODAL - MODIFICATION (AJAX)      //
    // ========================================== //
    function openEditModal(id) {
        showSpinner();

        Swal.fire({
            title: 'Chargement...',
            text: 'Récupération des données du mariage',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: '<?php echo base_url(); ?>admin/mariages/get_data/' + id,
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                hideSpinner();
                Swal.close();

                if (response.success) {
                    var data = response.mariage;
                    fillEditForm(data);
                    $('#mariageFormModal').modal('show');
                } else {
                    showError(response.message || 'Impossible de charger les données');
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
        $('#formTitleText').text('Modifier le mariage');
        $('#formModalSubtitle').text('Mettez à jour les informations');
        $('#formSubmitText').text('Mettre à jour');
        $('#formSubmitBtn').removeClass('btn-success').addClass('btn-warning');
        $('#mariageForm').attr('action', '<?php echo site_url('admin/mariages/update_ajax'); ?>');

        $('#edit_id').val(data.id);
        $('#edit_mari').val(data.mari_id).trigger('change');
        $('#edit_mari_nom').val(data.mari_nom);
        $('#edit_mari_prenom').val(data.mari_prenom);
        $('#edit_mari_date_naissance').val(data.mari_date_naissance ? formatDate(data.mari_date_naissance) : '');
        $('#edit_mari_profession').val(data.mari_profession);
        $('#edit_mari_telephone').val(data.mari_telephone);
        $('#edit_mari_email').val(data.mari_email);
        $('#edit_femme').val(data.femme_id).trigger('change');
        $('#edit_femme_nom').val(data.femme_nom);
        $('#edit_femme_prenom').val(data.femme_prenom);
        $('#edit_femme_date_naissance').val(data.femme_date_naissance ? formatDate(data.femme_date_naissance) : '');
        $('#edit_femme_profession').val(data.femme_profession);
        $('#edit_femme_telephone').val(data.femme_telephone);
        $('#edit_femme_email').val(data.femme_email);
        $('#edit_date_mariage').val(formatDate(data.date_mariage));
        $('#edit_heure_mariage').val(data.heure_mariage || '');
        $('#edit_lieu_mariage').val(data.lieu_mariage);
        $('#edit_pasteur_mariage').val(data.pasteur_officiant);
        $('#edit_type_mariage').val(data.type_mariage);
        $('#edit_invites').val(data.nombre_invites);
        $('#edit_liste_invites').val(data.invites);
        $('#edit_temoins_mari').val(data.temoins_mari);
        $('#edit_temoins_femme').val(data.temoins_femme);
        $('#edit_statut_mariage').val(data.statut);
        $('#edit_couleur_mariage').val(data.couleur);
        $('#edit_observations_mariage').val(data.observations);

        $('.text-danger').html('');
        $('.form-group').removeClass('has-error');
        $('.form-control').css('border-color', '');
    }

    // ========================================== //
    // SOUMISSION - AJOUT (AJAX)                  //
    // ========================================== //
    function submitAddForm(form) {
        if (!validateForm()) return false;
        showSpinner();

        var formData = new FormData(form[0]);

        $.ajax({
            url: '<?php echo site_url('admin/mariages/add_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner();
                if (response.success) {
                    $('#mariageFormModal').modal('hide');
                    showSuccess(response.message || 'Mariage ajouté avec succès');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    showError(response.message || 'Erreur lors de l\'ajout');
                }
            },
            error: function(xhr, status, error) {
                hideSpinner();
                handleAjaxError(xhr, status, error);
            }
        });
    }

    // ========================================== //
    // SOUMISSION - ÉDITION (AJAX)                //
    // ========================================== //
    function submitEditForm(form) {
        if (!validateForm()) return false;
        showSpinner();

        var formData = new FormData(form[0]);

        $.ajax({
            url: '<?php echo site_url('admin/mariages/update_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner();
                if (response.success) {
                    $('#mariageFormModal').modal('hide');
                    showSuccess(response.message || 'Mariage mis à jour avec succès');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    showError(response.message || 'Erreur lors de la mise à jour');
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

        var mari_nom = $('#edit_mari_nom').val().trim();
        if (mari_nom === '') {
            $('#edit_mari_nom').css('border-color', '#ef4444');
            $('#edit_mari_nom').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez renseigner le nom du mari');
            return false;
        }

        var mari_prenom = $('#edit_mari_prenom').val().trim();
        if (mari_prenom === '') {
            $('#edit_mari_prenom').css('border-color', '#ef4444');
            $('#edit_mari_prenom').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez renseigner le prénom du mari');
            return false;
        }

        var femme_nom = $('#edit_femme_nom').val().trim();
        if (femme_nom === '') {
            $('#edit_femme_nom').css('border-color', '#ef4444');
            $('#edit_femme_nom').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez renseigner le nom de la femme');
            return false;
        }

        var femme_prenom = $('#edit_femme_prenom').val().trim();
        if (femme_prenom === '') {
            $('#edit_femme_prenom').css('border-color', '#ef4444');
            $('#edit_femme_prenom').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez renseigner le prénom de la femme');
            return false;
        }

        var date_mariage = $('#edit_date_mariage').val();
        if (date_mariage === '' || date_mariage === 'dd/mm/yyyy') {
            $('#edit_date_mariage').css('border-color', '#ef4444');
            $('#edit_date_mariage').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez sélectionner la date du mariage');
            return false;
        }

        return isValid;
    }

    // ========================================== //
    // FILTRES                                    //
    // ========================================== //
    function applyFilters() {
        const status = document.getElementById('filterStatus').value;
        const type = document.getElementById('filterType').value;
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        const rows = document.querySelectorAll('#mariagesTableBody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            if (row.cells.length === 1 && row.cells[0].colSpan === 8) return;

            const rowStatus = row.dataset.status || '';
            const rowType = row.dataset.type || '';
            const rowDate = row.dataset.date || '';

            let show = true;
            if (status && rowStatus !== status) show = false;
            if (type && rowType !== type) show = false;
            if (dateFrom && rowDate < dateFrom) show = false;
            if (dateTo && rowDate > dateTo) show = false;

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        let noResult = document.getElementById('noResultMessage');
        if (!noResult) {
            noResult = document.createElement('tr');
            noResult.id = 'noResultMessage';
            noResult.innerHTML = '<td colspan="8" class="text-center text-muted" style="padding:40px 0;">Aucun mariage ne correspond aux filtres</td>';
            document.querySelector('#mariagesTableBody').appendChild(noResult);
        }
        noResult.style.display = visibleCount === 0 ? '' : 'none';
    }

    function resetFilters() {
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterType').value = '';
        document.getElementById('filterDateFrom').value = '';
        document.getElementById('filterDateTo').value = '';
        applyFilters();
    }

    // ========================================== //
    // EXPORTATION                                //
    // ========================================== //
    function exportData(type) {
        const statut = document.getElementById('filterStatus').value;
        const filterType = document.getElementById('filterType').value;
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        const params = `?statut=${encodeURIComponent(statut)}&type=${encodeURIComponent(filterType)}&date_from=${encodeURIComponent(dateFrom)}&date_to=${encodeURIComponent(dateTo)}`;

        showSpinner();

        var url = '<?php echo base_url("admin/mariages/export_"); ?>' + type + params;
        window.location.href = url;

        setTimeout(function() {
            hideSpinner();
        }, 3000);
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    function confirmDelete(event, id, code) {
        event.preventDefault();

        Swal.fire({
            title: 'Confirmation de suppression',
            text: 'Supprimer définitivement le mariage "' + code + '" ?',
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
                window.location.href = '<?php echo base_url("admin/mariages/delete/"); ?>' + id;
            }
        });
    }

    // ========================================== //
    // VOIR DÉTAILS                               //
    // ========================================== //
    function viewMariage(id) {
        showSpinner();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/mariages/details/' + id,
            success: function(result) {
                hideSpinner();
                $('#detailsContent').html(result);
                $('#mariageDetailsModal').modal('show');
            },
            error: function() {
                hideSpinner();
                showError('Impossible de charger les détails');
            }
        });
    }

    // ========================================== //
    // GÉNÉRER CERTIFICAT                         //
    // ========================================== //
    function genererCertificat(id, mari, femme) {
        Swal.fire({
            title: 'Confirmation',
            text: 'Générer le certificat de mariage pour "' + mari + '" et "' + femme + '" ?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Oui, générer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                showSpinner();
                window.location.href = '<?php echo base_url("admin/mariages/generer_certificat/"); ?>' + id;
            }
        });
    }

    function showCertificat(id) {
        showSpinner();
        setTimeout(function() {
            hideSpinner();
            Swal.fire({
                title: 'Certificat de mariage',
                text: 'Le certificat de mariage est disponible.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        }, 1000);
    }

    // ========================================== //
    // SPINNER                                    //
    // ========================================== //
    function showSpinner() { $('#spinnerOverlay').addClass('active'); }
    function hideSpinner() { $('#spinnerOverlay').removeClass('active'); }

    // ========================================== //
    // NOTIFICATIONS                              //
    // ========================================== //
    function showSuccess(message) {
        Swal.fire({
            title: 'Succès !',
            text: message,
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
        var errorMsg = 'Une erreur est survenue lors de la communication avec le serveur.';
        if (xhr.status === 404) errorMsg = 'La page demandée est introuvable.';
        else if (xhr.status === 500) errorMsg = 'Erreur interne du serveur.';
        else if (xhr.status === 403) errorMsg = 'Vous n\'avez pas les droits nécessaires.';
        else if (xhr.status === 0) errorMsg = 'Impossible de se connecter au serveur.';
        showError(errorMsg);
    }
</script>