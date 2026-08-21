<!-- ============================================================
     PAGE : Gestion des journaux auxiliaires OHADA
     DESCRIPTION : Interface pour la gestion des journaux ACHATS,
     VENTES, BANQUE, CAISSE, PAIE, OPD, A-NOUVEAUX
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
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        padding: 16px 20px;
        box-shadow: var(--shadow-soft);
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
        cursor: pointer;
    }

    .btn-add-modern:hover {
        background: #2563eb;
        color: #fff;
        box-shadow: 0 6px 18px rgba(59, 130, 246, 0.4);
        transform: translateY(-2px);
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

    .badge-ohada {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }
    .badge-ohada.ACHATS { background: #dbeafe; color: #1e40af; }
    .badge-ohada.VENTES { background: #d1fae5; color: #065f46; }
    .badge-ohada.BANQUE { background: #dbeafe; color: #1e40af; }
    .badge-ohada.CAISSE { background: #fef3c7; color: #92400e; }
    .badge-ohada.PAIE { background: #fef2f2; color: #991b1b; }
    .badge-ohada.OPD { background: #e2e8f0; color: #475569; }
    .badge-ohada.A-NOUVEAUX { background: #1e293b; color: #ffffff; }
    .badge-ohada.AUTRE { background: #e2e8f0; color: #475569; }

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

    .dropdown-menu.actions-menu li a.text-danger:hover {
        background: #fef2f2;
        color: #dc2626;
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

    .legende-journaux {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 12px 16px;
        background: #ffffff;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-light);
        margin-bottom: 20px;
    }

    .legende-journaux .badge {
        font-size: 12px;
        padding: 6px 14px;
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
        .legende-journaux { flex-direction: column; align-items: center; }
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card-modern">
                    <div class="card-header">
                        <h3>
                            <i class="fa fa-book"></i> Journaux auxiliaires (OHADA)
                            <span style="font-size: 14px; font-weight: 400; color: #94a3b8; margin-left: 8px;">
                                (<?php echo isset($stats['total']) ? $stats['total'] : 0; ?>)
                            </span>
                        </h3>
                        <div class="d-flex align-items-center gap-3" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url(); ?>admin/admin/comptabilite" class="btn-back" title="Retour au tableau de bord">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <button type="button" class="btn-add-modern" onclick="openAddModal()">
                                <i class="fa fa-plus-circle" style="margin-right: 6px;"></i> Nouveau journal
                            </button>
                            <a href="<?php echo base_url('admin/frontoffice/journaux_auxiliaires/export_excel'); ?>" class="btn-back" style="background: rgba(255,255,255,0.2);">
                                <i class="fa fa-file-excel-o"></i> Export
                            </a>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div style="padding: 20px 24px 0;">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-files-o"></i> Total</h4>
                                    <p class="number"><?php echo isset($stats['total']) ? $stats['total'] : 0; ?></p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-check-circle" style="color:#10b981;"></i> Actifs</h4>
                                    <p class="number"><?php echo isset($stats['actifs']) ? $stats['actifs'] : 0; ?></p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-shopping-cart" style="color:#3b82f6;"></i> ACHATS</h4>
                                    <p class="number"><?php echo isset($stats['type_achats']) ? $stats['type_achats'] : 0; ?></p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-tag" style="color:#10b981;"></i> VENTES</h4>
                                    <p class="number"><?php echo isset($stats['type_ventes']) ? $stats['type_ventes'] : 0; ?></p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-university" style="color:#8b5cf6;"></i> BANQUE</h4>
                                    <p class="number"><?php echo isset($stats['type_banque']) ? $stats['type_banque'] : 0; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Légende OHADA -->
                    <div style="padding: 0 24px;">
                        <div class="legende-journaux">
                            <span class="badge badge-primary">ACHATS</span>
                            <span class="badge badge-success">VENTES</span>
                            <span class="badge badge-info">BANQUE</span>
                            <span class="badge badge-warning">CAISSE</span>
                            <span class="badge badge-danger">PAIE</span>
                            <span class="badge badge-secondary">OPD</span>
                            <span class="badge badge-dark">A-NOUVEAUX</span>
                            <span class="badge badge-light text-dark">AUTRE</span>
                            <span style="font-size: 12px; color: #94a3b8; margin-left: auto;">
                                <i class="fa fa-info-circle"></i> Journaux obligatoires OHADA
                            </span>
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

                        <!-- Filtres -->
                        <div class="filter-bar">
                            <div class="filter-group">
                                <label><i class="fa fa-filter"></i> Type :</label>
                                <select id="filterType" onchange="applyFilters()">
                                    <option value="">Tous</option>
                                    <option value="ACHATS">ACHATS</option>
                                    <option value="VENTES">VENTES</option>
                                    <option value="BANQUE">BANQUE</option>
                                    <option value="CAISSE">CAISSE</option>
                                    <option value="PAIE">PAIE</option>
                                    <option value="OPD">OPD</option>
                                    <option value="A-NOUVEAUX">A-NOUVEAUX</option>
                                    <option value="AUTRE">AUTRE</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label><i class="fa fa-toggle-on"></i> Actif :</label>
                                <select id="filterActif" onchange="applyFilters()">
                                    <option value="">Tous</option>
                                    <option value="1">Actif</option>
                                    <option value="0">Inactif</option>
                                </select>
                            </div>
                            <div style="display:flex;gap:6px;margin-left:auto;flex-wrap:wrap;">
                                <button class="btn-filter" onclick="applyFilters()"><i class="fa fa-search"></i> Filtrer</button>
                                <button class="btn-reset" onclick="resetFilters()"><i class="fa fa-undo"></i> Réinitialiser</button>
                            </div>
                        </div>

                        <!-- Tableau -->
                        <div class="table-responsive">
                            <table class="table table-modern example" id="journauxTable">
                                <thead>
                                <tr>
                                    <th style="width: 10%;">Code</th>
                                    <th style="width: 25%;">Libellé</th>
                                    <th style="width: 15%;">Type</th>
                                    <th style="width: 15%;">Compte contrepartie</th>
                                    <th style="width: 15%;">Description</th>
                                    <th style="width: 10%;">Actif</th>
                                    <th style="text-align: center; width: 100px;">Action</th>
                                </tr>
                                </thead>
                                <tbody id="journauxTableBody">
                                <?php if (empty($journaux)) : ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted" style="padding: 60px 0;">
                                            <i class="fa fa-book" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
                                            <p style="font-size: 16px; color: #64748b;">Aucun journal enregistré</p>
                                            <p style="font-size: 13px; color: #94a3b8;">Cliquez sur "Nouveau journal" pour en créer un</p>
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($journaux as $journal) : ?>
                                        <tr data-type="<?php echo isset($journal['type']) ? $journal['type'] : ''; ?>"
                                            data-actif="<?php echo isset($journal['actif']) ? $journal['actif'] : 0; ?>"
                                            data-id="<?php echo isset($journal['id']) ? $journal['id'] : ''; ?>">

                                            <td><strong><?php echo isset($journal['code']) ? htmlspecialchars($journal['code']) : ''; ?></strong></td>

                                            <td>
                                                <strong><?php echo isset($journal['libelle']) ? htmlspecialchars($journal['libelle']) : ''; ?></strong>
                                            </td>

                                            <td>
                                                <span class="badge-ohada <?php echo isset($journal['type']) ? $journal['type'] : 'AUTRE'; ?>">
                                                    <?php echo isset($journal['type']) ? htmlspecialchars($journal['type']) : ''; ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?php echo isset($journal['compte_contrepartie']) && !empty($journal['compte_contrepartie']) ? htmlspecialchars($journal['compte_contrepartie']) : '<span style="color: #94a3b8;">-</span>'; ?>
                                            </td>

                                            <td>
                                                <?php echo isset($journal['description']) && !empty($journal['description']) ? htmlspecialchars(substr($journal['description'], 0, 50)) . (strlen($journal['description']) > 50 ? '...' : '') : '<span style="color: #94a3b8;">-</span>'; ?>
                                            </td>

                                            <td>
                                                <?php if (isset($journal['actif']) && $journal['actif'] == 1): ?>
                                                    <span class="badge badge-success"><i class="fa fa-check"></i> Oui</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger"><i class="fa fa-times"></i> Non</span>
                                                <?php endif; ?>
                                            </td>

                                            <td style="text-align: center;">
                                                <div class="dropdown">
                                                    <button class="btn-action-dropdown dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                        Actions <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu actions-menu dropdown-menu-right">
                                                        <li>
                                                            <a onclick="viewEcritures(<?php echo isset($journal['id']) ? $journal['id'] : 0; ?>)" style="cursor: pointer;">
                                                                <i class="fa fa-eye" style="color: #8b5cf6;"></i> Voir écritures
                                                            </a>
                                                        </li>
                                                        <li role="separator" class="divider"></li>
                                                        <li>
                                                            <a onclick="openEditModal(<?php echo isset($journal['id']) ? $journal['id'] : 0; ?>)">
                                                                <i class="fa fa-pencil" style="color: #3b82f6;"></i> Modifier
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#" class="text-danger" onclick="confirmDelete(event, <?php echo isset($journal['id']) ? $journal['id'] : 0; ?>, '<?php echo isset($journal['code']) ? htmlspecialchars($journal['code']) : ''; ?>')">
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

<!-- Spinner -->
<div class="spinner-overlay" id="spinnerOverlay">
    <div class="spinner-box">
        <i class="fa fa-spinner"></i>
        <p style="margin-top:15px;font-weight:500;color:#1e293b;">Chargement en cours...</p>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL AJOUT / MODIFICATION                 -->
<!-- ========================================== -->
<div id="journalFormModal" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 id="formModalTitle" style="margin: 0; color: white; font-size: 18px; font-weight: 600;">
                        <i class="fa fa-plus-circle" id="formModalIcon" style="color: #60a5fa;"></i>
                        <span id="formTitleText">Nouveau journal auxiliaire</span>
                    </h4>
                    <div class="modal-subtitle" id="formModalSubtitle" style="color: rgba(255,255,255,0.7); font-size: 12px; margin-top: 2px;">
                        Remplissez les informations ci-dessous
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form id="journalForm" method="post">
                <input type="hidden" name="edit_id" id="edit_id" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Code du journal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_code" name="code" placeholder="Ex: ACHATS, VENTES, BANQUE..." required style="text-transform: uppercase;">
                                <small class="text-muted">Code unique en majuscules</small>
                            </div>

                            <div class="form-group">
                                <label>Libellé <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_libelle" name="libelle" placeholder="Ex: Journal des achats" required>
                            </div>

                            <div class="form-group">
                                <label>Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_type" name="type" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="ACHATS">ACHATS</option>
                                    <option value="VENTES">VENTES</option>
                                    <option value="BANQUE">BANQUE</option>
                                    <option value="CAISSE">CAISSE</option>
                                    <option value="PAIE">PAIE</option>
                                    <option value="OPD">OPD (Opérations Diverses)</option>
                                    <option value="A-NOUVEAUX">A-NOUVEAUX (Ouverture)</option>
                                    <option value="AUTRE">AUTRE</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Compte de contrepartie</label>
                                <input type="text" class="form-control" id="edit_compte_contrepartie" name="compte_contrepartie" placeholder="Ex: 401 Fournisseurs">
                                <small class="text-muted">Compte par défaut pour les écritures</small>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="3" placeholder="Description du journal..."></textarea>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="edit_actif" name="actif" checked>
                                    <label class="custom-control-label" for="edit_actif">Journal actif</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message OHADA -->
                    <div class="alert alert-info" style="margin-top: 10px; font-size: 13px;">
                        <i class="fa fa-info-circle"></i>
                        <strong>OHADA :</strong> Les journaux auxiliaires obligatoires sont :
                        <strong>ACHATS</strong>, <strong>VENTES</strong>, <strong>BANQUE</strong>, <strong>CAISSE</strong>,
                        <strong>PAIE</strong>, <strong>OPD</strong> et <strong>A-NOUVEAUX</strong>.
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
<!-- SCRIPTS                                    -->
<!-- ========================================== -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    // ========================================== //
    // DOCUMENT READY                             //
    // ========================================== //
    $(document).ready(function() {
        $('#journalFormModal').on('hidden.bs.modal', function() {
            $('#journalForm')[0].reset();
            $('#edit_id').val('');
            $('#edit_actif').prop('checked', true);
            $('.text-danger').html('');
            $('.form-group').removeClass('has-error');
            $('.form-control').css('border-color', '');
        });

        $('#journalForm').on('submit', function(e) {
            e.preventDefault();
            var editId = $('#edit_id').val();
            if (editId && editId !== '') {
                submitEditForm($(this));
            } else {
                submitAddForm($(this));
            }
        });

        $('#filterType, #filterActif').on('change', function() {
            applyFilters();
        });

        // Mettre en majuscule le code
        $('#edit_code').on('input', function() {
            $(this).val($(this).val().toUpperCase());
        });
    });

    // ========================================== //
    // OUVERTURE MODAL - AJOUT                    //
    // ========================================== //
    function openAddModal() {
        $('#formModalIcon').removeClass('fa-pencil-square-o').addClass('fa-plus-circle');
        $('#formTitleText').text('Nouveau journal auxiliaire');
        $('#formModalSubtitle').text('Remplissez les informations ci-dessous');
        $('#formSubmitText').text('Enregistrer');
        $('#formSubmitBtn').removeClass('btn-warning').addClass('btn-success');
        $('#journalForm')[0].reset();
        $('#edit_id').val('');
        $('#edit_actif').prop('checked', true);
        $('.text-danger').html('');
        $('.form-group').removeClass('has-error');
        $('.form-control').css('border-color', '');
        $('#journalFormModal').modal('show');
    }

    // ========================================== //
    // OUVERTURE MODAL - MODIFICATION (AJAX)      //
    // ========================================== //
    function openEditModal(id) {
        showSpinner();

        Swal.fire({
            title: 'Chargement...',
            text: 'Récupération des données',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: '<?php echo base_url(); ?>admin/frontoffice/journaux_auxiliaires/get_data/' + id,
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                hideSpinner();
                Swal.close();

                if (response.success) {
                    var data = response.journal;
                    fillEditForm(data);
                    $('#journalFormModal').modal('show');
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
        $('#formTitleText').text('Modifier le journal');
        $('#formModalSubtitle').text('Mettez à jour les informations');
        $('#formSubmitText').text('Mettre à jour');
        $('#formSubmitBtn').removeClass('btn-success').addClass('btn-warning');

        $('#edit_id').val(data.id);
        $('#edit_code').val(data.code || '');
        $('#edit_libelle').val(data.libelle || '');
        $('#edit_type').val(data.type || '');
        $('#edit_compte_contrepartie').val(data.compte_contrepartie || '');
        $('#edit_description').val(data.description || '');
        $('#edit_actif').prop('checked', data.actif == 1);
    }

    // ========================================== //
    // SOUMISSION - AJOUT (AJAX)                  //
    // ========================================== //
    function submitAddForm(form) {
        if (!validateForm()) return false;
        showSpinner();

        var formData = new FormData(form[0]);

        $.ajax({
            url: '<?php echo site_url('admin/frontoffice/journaux_auxiliaires/add_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner();
                if (response.success) {
                    $('#journalFormModal').modal('hide');
                    showSuccess(response.message || 'Journal ajouté avec succès');
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
            url: '<?php echo site_url('admin/frontoffice/journaux_auxiliaires/update_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner();
                if (response.success) {
                    $('#journalFormModal').modal('hide');
                    showSuccess(response.message || 'Journal mis à jour avec succès');
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
        var code = $('#edit_code').val().trim();
        if (code === '') {
            showError('Veuillez renseigner le code du journal');
            $('#edit_code').css('border-color', '#ef4444');
            return false;
        }

        var libelle = $('#edit_libelle').val().trim();
        if (libelle === '') {
            showError('Veuillez renseigner le libellé');
            $('#edit_libelle').css('border-color', '#ef4444');
            return false;
        }

        var type = $('#edit_type').val();
        if (type === '') {
            showError('Veuillez sélectionner un type');
            $('#edit_type').css('border-color', '#ef4444');
            return false;
        }

        return true;
    }

    // ========================================== //
    // FILTRES                                    //
    // ========================================== //
    function applyFilters() {
        const type = document.getElementById('filterType').value;
        const actif = document.getElementById('filterActif').value;

        const rows = document.querySelectorAll('#journauxTableBody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            if (row.cells.length === 1 && row.cells[0].colSpan === 7) return;

            const rowType = row.dataset.type || '';
            const rowActif = row.dataset.actif || '';

            let show = true;
            if (type && rowType !== type) show = false;
            if (actif !== '' && rowActif !== actif) show = false;

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        let noResult = document.getElementById('noResultMessage');
        if (!noResult) {
            noResult = document.createElement('tr');
            noResult.id = 'noResultMessage';
            noResult.innerHTML = '<td colspan="7" class="text-center text-muted" style="padding:40px 0;">Aucun journal ne correspond aux filtres</td>';
            document.querySelector('#journauxTableBody').appendChild(noResult);
        }
        noResult.style.display = visibleCount === 0 ? '' : 'none';
    }

    function resetFilters() {
        document.getElementById('filterType').value = '';
        document.getElementById('filterActif').value = '';
        applyFilters();
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    function confirmDelete(event, id, code) {
        event.preventDefault();

        Swal.fire({
            title: 'Confirmation de suppression',
            text: 'Supprimer définitivement le journal "' + code + '" ?',
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
                window.location.href = '<?php echo base_url("admin/frontoffice/journaux_auxiliaires/delete/"); ?>' + id;
            }
        });
    }

    // ========================================== //
    // VOIR LES ÉCRITURES                         //
    // ========================================== //
    function viewEcritures(id) {
        window.location.href = '<?php echo base_url("admin/frontoffice/journaux_auxiliaires/ecritures/"); ?>' + id;
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

    function handleAjaxError(xhr, status, error) {
        var errorMsg = 'Une erreur est survenue lors de la communication avec le serveur.';
        if (xhr.status === 404) errorMsg = 'La page demandée est introuvable.';
        else if (xhr.status === 500) errorMsg = 'Erreur interne du serveur.';
        else if (xhr.status === 403) errorMsg = 'Vous n\'avez pas les droits nécessaires.';
        else if (xhr.status === 0) errorMsg = 'Impossible de se connecter au serveur.';
        showError(errorMsg);
    }
</script>