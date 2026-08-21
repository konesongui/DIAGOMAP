<!-- ============================================================
     PAGE : Gestion des rapports
     DESCRIPTION : Interface moderne pour la gestion des rapports
     ============================================================ -->

<style>
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
    .stat-card:nth-child(2) { border-left-color: #f59e0b; }
    .stat-card:nth-child(3) { border-left-color: #8b5cf6; }
    .stat-card:nth-child(4) { border-left-color: #10b981; }
    .stat-card:nth-child(5) { border-left-color: #6b7280; }

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
        position: relative;
        overflow: hidden;
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
    .badge-status.en-attente { background: #fef3c7; color: #92400e; }
    .badge-status.en-cours { background: #dbeafe; color: #1d4ed8; }
    .badge-status.termine { background: #d1fae5; color: #065f46; }
    .badge-status.archive { background: #e2e8f0; color: #475569; }

    .badge-priority {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }
    .badge-priority.basse { background: #e2e8f0; color: #475569; }
    .badge-priority.normale { background: #dbeafe; color: #1d4ed8; }
    .badge-priority.haute { background: #fef3c7; color: #92400e; }
    .badge-priority.urgente { background: #fef2f2; color: #991b1b; }

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

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .card-modern .card-header { flex-direction: column; align-items: stretch; }
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-bar .filter-group { width: 100%; }
        .filter-bar .filter-group select, .filter-bar .filter-group input { width: 100%; min-width: unset; }
        .table-modern thead th, .table-modern tbody td { padding: 8px 10px; font-size: 12px; }
        .btn-add-modern { width: 100%; text-align: center; }
        .modal-chic .modal-body { padding: 16px; }
        .modal-chic .modal-footer { flex-direction: column; }
        .modal-chic .modal-footer .btn { width: 100%; }
        .export-group { margin-left: 0; width: 100%; flex-wrap: wrap; }
        .btn-export { flex: 1; justify-content: center; padding: 6px 12px; font-size: 11px; }
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card-modern">
                    <div class="card-header">
                        <h3>
                            <i class="fa fa-bar-chart"></i> Gestion des rapports
                            <span style="font-size: 14px; font-weight: 400; color: #94a3b8; margin-left: 8px;">
                                (<?php echo isset($stats['total']) ? $stats['total'] : 0; ?>)
                            </span>
                        </h3>
                        <div class="d-flex align-items-center gap-3" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url(); ?>admin/admin/hub" class="btn-back" title="Retour au tableau de bord">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <button type="button" class="btn-add-modern" onclick="openAddModal()">
                                <i class="fa fa-plus-circle" style="margin-right: 6px;"></i> Nouveau rapport
                            </button>
                        </div>
                    </div>

                    <div style="padding: 20px 24px 0;">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-file-text-o"></i> Total</h4>
                                    <p class="number"><?php echo isset($stats['total']) ? $stats['total'] : 0; ?></p>
                                </div>

                            </div>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-clock-o" style="color:#f59e0b;"></i> En attente</h4>
                                    <p class="number"><?php echo isset($stats['en_attente']) ? $stats['en_attente'] : 0; ?></p>
                                </div>

                            </div>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-spinner" style="color:#8b5cf6;"></i> En cours</h4>
                                    <p class="number"><?php echo isset($stats['en_cours']) ? $stats['en_cours'] : 0; ?></p>
                                </div>

                            </div>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-check-circle" style="color:#10b981;"></i> Terminés</h4>
                                    <p class="number"><?php echo isset($stats['termine']) ? $stats['termine'] : 0; ?></p>
                                </div>

                            </div>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-archive" style="color:#6b7280;"></i> Archivés</h4>
                                    <p class="number"><?php echo isset($stats['archive']) ? $stats['archive'] : 0; ?></p>
                                </div>

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

                        <div class="filter-bar">
                            <div class="filter-group">
                                <label><i class="fa fa-filter"></i> Type :</label>
                                <select id="filterType" onchange="applyFilters()">
                                    <option value="">Tous</option>
                                    <?php if (isset($types) && is_array($types)) : ?>
                                        <?php foreach ($types as $type) : ?>
                                            <option value="<?php echo $type; ?>"><?php echo $this->rapports_model->get_type_label($type); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label><i class="fa fa-tag"></i> Statut :</label>
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
                                    <button class="btn-export btn-excel" onclick="exportData('excel')">
                                        <i class="fa fa-file-excel-o"></i> CSV
                                    </button>
                                    <button class="btn-export btn-pdf" onclick="exportData('pdf')">
                                        <i class="fa fa-file-pdf-o"></i> PDF
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-modern" id="rapportsTable">
                                <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 20%;">Titre</th>
                                    <th style="width: 15%;">Type</th>
                                    <th style="width: 12%;">Statut</th>
                                    <th style="width: 12%;">Priorité</th>
                                    <th style="width: 12%;">Période</th>
                                    <th style="width: 12%;">Date</th>
                                    <th style="text-align: center; width: 100px;">Action</th>
                                </tr>
                                </thead>
                                <tbody id="rapportsTableBody">
                                <?php if (empty($rapports)) : ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted" style="padding: 60px 0;">
                                            <i class="fa fa-bar-chart" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
                                            <p style="font-size: 16px; color: #64748b;">Aucun rapport enregistré</p>
                                            <p style="font-size: 13px; color: #94a3b8;">Cliquez sur "Nouveau rapport" pour en créer un</p>
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($rapports as $index => $rapport) : ?>
                                        <tr data-type="<?php echo isset($rapport['type_rapport']) ? $rapport['type_rapport'] : ''; ?>"
                                            data-status="<?php echo isset($rapport['statut']) ? $rapport['statut'] : ''; ?>"
                                            data-date="<?php echo isset($rapport['date_creation']) ? $rapport['date_creation'] : ''; ?>"
                                            data-id="<?php echo isset($rapport['id']) ? $rapport['id'] : ''; ?>">

                                            <td><?php echo $index + 1; ?></td>

                                            <td>
                                                <div>
                                                    <strong><?php echo isset($rapport['titre']) ? htmlspecialchars($rapport['titre']) : ''; ?></strong>
                                                    <?php if (!empty($rapport['description'])) : ?>
                                                        <br>
                                                        <small style="color: #94a3b8; font-size: 11px;">
                                                            <?php echo isset($rapport['description']) ? htmlspecialchars(substr($rapport['description'], 0, 50)) : ''; ?>...
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <td>
                                                    <span style="background: #f1f5f9; padding: 3px 12px; border-radius: 12px; font-size: 11px; color: #475569;">
                                                        <?php echo isset($rapport['type_rapport']) ? $this->rapports_model->get_type_label($rapport['type_rapport']) : ''; ?>
                                                    </span>
                                            </td>

                                            <td>
                                                    <span class="badge-status <?php
                                                    $status = isset($rapport['statut']) ? $rapport['statut'] : 'en_attente';
                                                    if ($status == 'en_attente') echo 'en-attente';
                                                    elseif ($status == 'en_cours') echo 'en-cours';
                                                    elseif ($status == 'termine') echo 'termine';
                                                    elseif ($status == 'archive') echo 'archive';
                                                    ?>">
                                                        <?php echo isset($statuses[$status]) ? $statuses[$status] : $status; ?>
                                                    </span>
                                            </td>

                                            <td>
                                                    <span class="badge-priority <?php
                                                    $priority = isset($rapport['priorite']) ? $rapport['priorite'] : 'normale';
                                                    if ($priority == 'basse') echo 'basse';
                                                    elseif ($priority == 'normale') echo 'normale';
                                                    elseif ($priority == 'haute') echo 'haute';
                                                    elseif ($priority == 'urgente') echo 'urgente';
                                                    ?>">
                                                        <?php echo isset($priorities[$priority]) ? $priorities[$priority] : $priority; ?>
                                                    </span>
                                            </td>

                                            <td style="font-size: 12px; color: #64748b;">
                                                <?php if (!empty($rapport['periode_debut']) && !empty($rapport['periode_fin'])) : ?>
                                                    <?php echo date('d/m/Y', strtotime($rapport['periode_debut'])); ?> - <?php echo date('d/m/Y', strtotime($rapport['periode_fin'])); ?>
                                                <?php else : ?>
                                                    —
                                                <?php endif; ?>
                                            </td>

                                            <td style="font-size: 12px; color: #64748b;">
                                                <?php echo !empty($rapport['date_creation']) ? date('d/m/Y', strtotime($rapport['date_creation'])) : ''; ?>
                                            </td>

                                            <td style="text-align: center;">
                                                <div class="dropdown">
                                                    <button class="btn-action-dropdown dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                        Actions <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu actions-menu dropdown-menu-right">
                                                        <li>
                                                            <a onclick="viewRapport(<?php echo isset($rapport['id']) ? $rapport['id'] : 0; ?>)" style="cursor: pointer;">
                                                                <i class="fa fa-eye" style="color: #8b5cf6;"></i> Voir détails
                                                            </a>
                                                        </li>
                                                        <li role="separator" class="divider"></li>

                                                        <?php if (!empty($rapport['fichier'])) : ?>
                                                            <li>
                                                                <a href="<?php echo base_url('admin/rapports/download/' . $rapport['fichier']); ?>">
                                                                    <i class="fa fa-download" style="color: #10b981;"></i> Télécharger
                                                                </a>
                                                            </li>
                                                            <li role="separator" class="divider"></li>
                                                        <?php endif; ?>

                                                        <li>
                                                            <a onclick="openEditModal(<?php echo isset($rapport['id']) ? $rapport['id'] : 0; ?>)">
                                                                <i class="fa fa-pencil" style="color: #3b82f6;"></i> Modifier
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#" class="text-danger" onclick="confirmDelete(event, <?php echo isset($rapport['id']) ? $rapport['id'] : 0; ?>, '<?php echo isset($rapport['titre']) ? htmlspecialchars($rapport['titre']) : ''; ?>')">
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

<div class="spinner-overlay" id="spinnerOverlay">
    <div class="spinner-box">
        <i class="fa fa-spinner"></i>
        <p style="margin-top:15px;font-weight:500;color:#1e293b;">Chargement en cours...</p>
    </div>
</div>

<div id="rapportFormModal" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-md" style="max-width: 720px;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 id="formModalTitle" style="margin: 0; color: white; font-size: 18px; font-weight: 600;">
                        <i class="fa fa-plus-circle" id="formModalIcon" style="color: #60a5fa;"></i>
                        <span id="formTitleText">Nouveau rapport</span>
                    </h4>
                    <div class="modal-subtitle" id="formModalSubtitle" style="color: rgba(255,255,255,0.7); font-size: 12px; margin-top: 2px;">
                        Remplissez les informations ci-dessous
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"
                        style="color: #ffffff; opacity: 0.8; font-size: 32px; font-weight: 300; text-shadow: none; border: none; border-radius: 50%; padding: 57px; margin: 16px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; flex-shrink: 0; line-height: 1;"
                        onmouseover="this.style.opacity='1'; this.style.background='rgba(255,255,255,0.25)'; this.style.transform='rotate(90deg)';"
                        onmouseout="this.style.opacity='0.8'; this.style.background='rgba(255,255,255,0.1)'; this.style.transform='rotate(0)';">
                    <span style="line-height: 1; font-size: 28px;">&times;</span>
                </button>
            </div>

            <form id="rapportForm" method="post" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" id="edit_id" value="">
                <div class="modal-body">
                    <div style="background: #f8fafc; padding: 15px; border-radius: 6px; margin-bottom: 16px;">
                        <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px;">
                            <i class="fa fa-info-circle" style="margin-right: 8px; color: #3B82F6;"></i> INFORMATIONS DU RAPPORT
                        </h5>

                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Titre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_titre" name="titre" placeholder="Titre du rapport" style="height: 38px; font-size: 13px;" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Type de rapport <span class="text-danger">*</span></label>
                                    <select class="form-control" id="edit_type_rapport" name="type_rapport" style="height: 38px; font-size: 13px;" required>
                                        <option value="">Sélectionner...</option>
                                        <option value="finance">Rapport financier</option>
                                        <option value="statistique">Rapport statistique</option>
                                        <option value="projet">Rapport de projet</option>
                                        <option value="activite">Rapport d'activité</option>
                                        <option value="rh">Rapport RH</option>
                                        <option value="vente">Rapport de vente</option>
                                        <option value="inventaire">Rapport d'inventaire</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Priorité</label>
                                    <select class="form-control" id="edit_priorite" name="priorite" style="height: 38px; font-size: 13px;">
                                        <option value="basse">Basse</option>
                                        <option value="normale" selected>Normale</option>
                                        <option value="haute">Haute</option>
                                        <option value="urgente">Urgente</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Période début</label>
                                    <input type="text" class="form-control date" id="edit_periode_debut" name="periode_debut" readonly style="height: 38px; font-size: 13px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Période fin</label>
                                    <input type="text" class="form-control date" id="edit_periode_fin" name="periode_fin" readonly style="height: 38px; font-size: 13px;">
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="2" placeholder="Description du rapport..." style="font-size: 13px; resize: vertical; min-height: 50px;"></textarea>
                        </div>
                    </div>

                    <div style="background: #eff6ff; padding: 15px; border-radius: 6px; margin-bottom: 0; border-left: 4px solid #3B82F6;">
                        <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dbeafe; padding-bottom: 6px;">
                            <i class="fa fa-cog" style="margin-right: 8px; color: #3B82F6;"></i> PARAMÈTRES & FICHIER
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Statut</label>
                                    <select class="form-control" id="edit_statut" name="statut" style="height: 38px; font-size: 13px;">
                                        <option value="en_attente">En attente</option>
                                        <option value="en_cours">En cours</option>
                                        <option value="termine">Terminé</option>
                                        <option value="archive">Archivé</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">
                                        <i class="fa fa-paperclip" style="margin-right: 6px; color: #3b82f6;"></i>
                                        Fichier joint
                                    </label>
                                    <div class="custom-file-upload" style="display: flex; align-items: center; gap: 12px;">
                                        <button type="button" class="btn btn-primary" style="background: #3b82f6; border: none; border-radius: 8px; padding: 8px 20px; color: #fff; font-size: 13px; display: flex; align-items: center; gap: 8px; white-space: nowrap;" onclick="document.getElementById('edit_fichier').click();">
                                            <i class="fa fa-cloud-upload"></i> Parcourir
                                        </button>
                                        <span id="file_name_display" style="font-size: 13px; color: #64748b; flex: 1; padding: 6px 12px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; min-height: 38px; display: flex; align-items: center;">
                                            <i class="fa fa-file-o" style="color: #94a3b8; margin-right: 8px;"></i>
                                            Aucun fichier sélectionné
                                        </span>
                                        <button type="button" class="btn btn-sm btn-danger" style="border: none; border-radius: 8px; padding: 8px 14px; color: #dc2626; background: #fef2f2; display: none;" id="clear_file_btn" onclick="clearFile()">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                    <input class="form-control" type="file" name="fichier" id="edit_fichier" style="display: none;" onchange="updateFileDisplay(this)">
                                    <small style="color: #94a3b8; font-size: 11px; display: block; margin-top: 4px;">
                                        <i class="fa fa-info-circle"></i> Formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG | Max: 10MB
                                    </small>
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

<div id="rapportDetailsModal" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 800px;">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; border-radius: 8px 8px 0 0;">
                <h4 style="margin: 0; color: white;">
                    <i class="fa fa-info-circle" style="color: #60a5fa;"></i> Détails du rapport
                </h4>
                <button type="button" class="close" data-dismiss="modal" style="color:white; opacity: 0.8;">&times;</button>
            </div>
            <div class="modal-body" id="detailsContent" style="padding: 24px; background: #fafcff;"></div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        $('#rapportFormModal').on('hidden.bs.modal', function() {
            $('#rapportForm')[0].reset();
            $('#edit_id').val('');
            $('#file_name_display').html('<i class="fa fa-file-o" style="color: #94a3b8; margin-right: 8px;"></i> Aucun fichier sélectionné');
            $('#clear_file_btn').hide();
            $('.text-danger').html('');
        });

        $('#rapportForm').on('submit', function(e) {
            e.preventDefault();
            var editId = $('#edit_id').val();
            if (editId && editId !== '') {
                submitEditForm($(this));
            } else {
                submitAddForm($(this));
            }
        });

        $('#filterType, #filterStatus, #filterDateFrom, #filterDateTo').on('change', function() {
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

    function updateFileDisplay(input) {
        var display = document.getElementById('file_name_display');
        var clearBtn = document.getElementById('clear_file_btn');
        var file = input.files[0];

        if (file) {
            var ext = file.name.split('.').pop().toLowerCase();
            var iconMap = {
                'pdf': 'fa-file-pdf-o',
                'doc': 'fa-file-word-o',
                'docx': 'fa-file-word-o',
                'xls': 'fa-file-excel-o',
                'xlsx': 'fa-file-excel-o',
                'jpg': 'fa-file-image-o',
                'jpeg': 'fa-file-image-o',
                'png': 'fa-file-image-o'
            };
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
        document.getElementById('edit_fichier').value = '';
        updateFileDisplay(document.getElementById('edit_fichier'));
    }

    // ========================================== //
    // OUVERTURE MODAL - AJOUT                    //
    // ========================================== //
    function openAddModal() {
        $('#formModalIcon').removeClass('fa-pencil-square-o').addClass('fa-plus-circle');
        $('#formTitleText').text('Nouveau rapport');
        $('#formModalSubtitle').text('Remplissez les informations ci-dessous');
        $('#formSubmitText').text('Enregistrer');
        $('#formSubmitBtn').removeClass('btn-warning').addClass('btn-success');
        $('#rapportForm').attr('action', '<?php echo site_url('admin/rapports/add_ajax'); ?>');
        $('#rapportForm')[0].reset();
        $('#edit_id').val('');
        $('#edit_statut').val('en_attente');
        $('#edit_priorite').val('normale');
        $('#file_name_display').html('<i class="fa fa-file-o" style="color: #94a3b8; margin-right: 8px;"></i> Aucun fichier sélectionné');
        $('#clear_file_btn').hide();
        $('.text-danger').html('');
        $('#rapportFormModal').modal('show');
    }

    // ========================================== //
    // OUVERTURE MODAL - MODIFICATION (AJAX)      //
    // ========================================== //
    function openEditModal(id) {
        showSpinner();

        Swal.fire({
            title: 'Chargement...',
            text: 'Récupération des données du rapport',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        var url = '<?php echo base_url(); ?>admin/rapports/get_rapport_data/' + id;

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                hideSpinner();
                Swal.close();

                if (response.success) {
                    var data = response.rapport;
                    fillEditForm(data);
                    $('#rapportFormModal').modal('show');
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
        $('#formTitleText').text('Modifier le rapport');
        $('#formModalSubtitle').text('Mettez à jour les informations');
        $('#formSubmitText').text('Mettre à jour');
        $('#formSubmitBtn').removeClass('btn-success').addClass('btn-warning');
        $('#rapportForm').attr('action', '<?php echo site_url('admin/rapports/update_ajax'); ?>');

        $('#edit_id').val(data.id);
        $('#edit_titre').val(data.titre || '');
        $('#edit_type_rapport').val(data.type_rapport || '');
        $('#edit_description').val(data.description || '');
        $('#edit_statut').val(data.statut || 'en_attente');
        $('#edit_priorite').val(data.priorite || 'normale');

        if (data.periode_debut) {
            $('#edit_periode_debut').val(formatDate(data.periode_debut));
        }
        if (data.periode_fin) {
            $('#edit_periode_fin').val(formatDate(data.periode_fin));
        }

        if (data.fichier) {
            var display = document.getElementById('file_name_display');
            display.innerHTML = '<i class="fa fa-file-o" style="color: #3b82f6; margin-right: 8px;"></i> ' + data.fichier + ' <span style="color: #94a3b8; font-size: 11px;">(fichier existant)</span>';
            display.style.color = '#1e293b';
        }

        $('.text-danger').html('');
        $('.form-group').removeClass('has-error');
        $('.form-control').css('border-color', '');
    }

    // ========================================== //
    // SOUMISSION DU FORMULAIRE - AJOUT (AJAX)    //
    // ========================================== //
    function submitAddForm(form) {
        if (!validateForm()) return false;
        showSpinner();

        var formData = new FormData(form[0]);

        $.ajax({
            url: '<?php echo site_url('admin/rapports/add_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner();
                if (response.success) {
                    $('#rapportFormModal').modal('hide');
                    showSuccess(response.message || 'Rapport ajouté avec succès');
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
    // SOUMISSION DU FORMULAIRE - ÉDITION (AJAX)  //
    // ========================================== //
    function submitEditForm(form) {
        if (!validateForm()) return false;
        showSpinner();

        var formData = new FormData(form[0]);

        $.ajax({
            url: '<?php echo site_url('admin/rapports/update_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner();
                if (response.success) {
                    $('#rapportFormModal').modal('hide');
                    showSuccess(response.message || 'Rapport mis à jour avec succès');
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

        var titre = $('#edit_titre').val().trim();
        if (titre === '') {
            $('#edit_titre').css('border-color', '#ef4444');
            $('#edit_titre').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez renseigner le titre');
            return false;
        }

        var type_rapport = $('#edit_type_rapport').val();
        if (type_rapport === '') {
            $('#edit_type_rapport').css('border-color', '#ef4444');
            $('#edit_type_rapport').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez sélectionner un type de rapport');
            return false;
        }

        return isValid;
    }

    // ========================================== //
    // FILTRES                                    //
    // ========================================== //
    function applyFilters() {
        const type = document.getElementById('filterType').value;
        const status = document.getElementById('filterStatus').value;
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        const rows = document.querySelectorAll('#rapportsTableBody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            if (row.cells.length === 1 && row.cells[0].colSpan === 8) return;

            const rowType = row.dataset.type || '';
            const rowStatus = row.dataset.status || '';
            const rowDate = row.dataset.date || '';

            let show = true;
            if (type && rowType !== type) show = false;
            if (status && rowStatus !== status) show = false;
            if (dateFrom && rowDate < dateFrom) show = false;
            if (dateTo && rowDate > dateTo) show = false;

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        let noResult = document.getElementById('noResultMessage');
        if (!noResult) {
            noResult = document.createElement('tr');
            noResult.id = 'noResultMessage';
            noResult.innerHTML = '<td colspan="8" class="text-center text-muted" style="padding:40px 0;">Aucun rapport ne correspond aux filtres</td>';
            document.querySelector('#rapportsTableBody').appendChild(noResult);
        }
        noResult.style.display = visibleCount === 0 ? '' : 'none';
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
        const type_rapport = document.getElementById('filterType').value;
        const statut = document.getElementById('filterStatus').value;
        const date_from = document.getElementById('filterDateFrom').value;
        const date_to = document.getElementById('filterDateTo').value;

        const params = `?type_rapport=${encodeURIComponent(type_rapport)}&statut=${encodeURIComponent(statut)}&date_from=${encodeURIComponent(date_from)}&date_to=${encodeURIComponent(date_to)}`;

        showSpinner();

        var url = '<?php echo base_url("admin/rapports/export_"); ?>' + type + params;
        window.location.href = url;

        setTimeout(function() {
            hideSpinner();
        }, 3000);
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    function confirmDelete(event, id, titre) {
        event.preventDefault();

        Swal.fire({
            title: 'Confirmation de suppression',
            text: 'Supprimer définitivement le rapport "' + titre + '" ?',
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
                window.location.href = '<?php echo base_url("admin/rapports/delete/"); ?>' + id;
            }
        });
    }

    // ========================================== //
    // VOIR DÉTAILS                               //
    // ========================================== //
    function viewRapport(id) {
        showSpinner();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/rapports/details/' + id,
            success: function(result) {
                hideSpinner();
                $('#detailsContent').html(result);
                $('#rapportDetailsModal').modal('show');
            },
            error: function() {
                hideSpinner();
                showError('Impossible de charger les détails');
            }
        });
    }

    // ========================================== //
    // GESTION DU SPINNER                         //
    // ========================================== //
    function showSpinner() { $('#spinnerOverlay').addClass('active'); }
    function hideSpinner() { $('#spinnerOverlay').removeClass('active'); }

    // ========================================== //
    // NOTIFICATIONS (SweetAlert)                 //
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