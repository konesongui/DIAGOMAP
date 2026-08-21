<!-- ============================================================
     PAGE : Gestion des immobilisations
     DESCRIPTION : Interface moderne pour la gestion des immobilisations
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

    .badge-status {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }
    .badge-status.actif { background: #d1fae5; color: #065f46; }
    .badge-status.amorti { background: #fef3c7; color: #92400e; }
    .badge-status.ceder { background: #fef2f2; color: #991b1b; }
    .badge-status.sortie { background: #e2e8f0; color: #475569; }

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
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card-modern">
                    <div class="card-header">
                        <h3>
                            <i class="fa fa-building"></i> Gestion des immobilisations
                            <span style="font-size: 14px; font-weight: 400; color: #94a3b8; margin-left: 8px;">
                                (<?php echo isset($stats['total']) ? $stats['total'] : 0; ?>)
                            </span>
                        </h3>
                        <div class="d-flex align-items-center gap-3" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url(); ?>admin/admin/comptabilite" class="btn-back" title="Retour au tableau de bord">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <button type="button" class="btn-add-modern" onclick="openAddModal()">
                                <i class="fa fa-plus-circle" style="margin-right: 6px;"></i> Nouvelle immobilisation
                            </button>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div style="padding: 20px 24px 0;">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-building"></i> Total</h4>
                                    <p class="number"><?php echo isset($stats['total']) ? $stats['total'] : 0; ?></p>
                                </div>

                            </div>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-check-circle" style="color:#10b981;"></i> Actives</h4>
                                    <p class="number"><?php echo isset($stats['actif']) ? $stats['actif'] : 0; ?></p>
                                </div>

                            </div>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-clock-o" style="color:#f59e0b;"></i> Amorties</h4>
                                    <p class="number"><?php echo isset($stats['amorti']) ? $stats['amorti'] : 0; ?></p>
                                </div>

                            </div>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-money" style="color:#8b5cf6;"></i> Valeur totale</h4>
                                    <p class="number" style="font-size: 16px;">
                                        <?php echo isset($stats['total_valeur']) ? number_format($stats['total_valeur'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                    </p>
                                </div>

                            </div>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-line-chart" style="color:#ef4444;"></i> Amortissement</h4>
                                    <p class="number" style="font-size: 16px;">
                                        <?php echo isset($stats['total_amortissement']) ? number_format($stats['total_amortissement'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                    </p>
                                </div>

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

                        <!-- Filtres -->
                        <div class="filter-bar">
                            <div class="filter-group">
                                <label><i class="fa fa-filter"></i> Catégorie :</label>
                                <select id="filterCategory" onchange="applyFilters()">
                                    <option value="">Toutes</option>
                                    <?php if (isset($categories) && is_array($categories)) : ?>
                                        <?php foreach ($categories as $cat) : ?>
                                            <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label><i class="fa fa-tag"></i> Statut :</label>
                                <select id="filterStatus" onchange="applyFilters()">
                                    <option value="">Tous</option>
                                    <option value="actif">Actif</option>
                                    <option value="amorti">Amorti</option>
                                    <option value="ceder">Cédé</option>
                                    <option value="sortie">Sortie</option>
                                </select>
                            </div>
                            <div style="display:flex;gap:6px;margin-left:auto;flex-wrap:wrap;">
                                <button class="btn-filter" onclick="applyFilters()"><i class="fa fa-search"></i> Filtrer</button>
                                <button class="btn-reset" onclick="resetFilters()"><i class="fa fa-undo"></i> Réinitialiser</button>
                            </div>
                        </div>

                        <!-- Tableau -->
                        <div class="table-responsive">
                            <table class="table table-modern example" id="immobilisationsTable">
                                <thead>
                                <tr>
                                    <th style="width: 10%;">Code</th>
                                    <th style="width: 20%;">Nom</th>
                                    <th style="width: 15%;">Catégorie</th>
                                    <th style="width: 15%;">Valeur originale</th>
                                    <th style="width: 15%;">Valeur nette</th>
                                    <th style="width: 12%;">Statut</th>
                                    <th style="text-align: center; width: 100px;">Action</th>
                                </tr>
                                </thead>
                                <tbody id="immobilisationsTableBody">
                                <?php if (empty($immobilisations)) : ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted" style="padding: 60px 0;">
                                            <i class="fa fa-building" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
                                            <p style="font-size: 16px; color: #64748b;">Aucune immobilisation enregistrée</p>
                                            <p style="font-size: 13px; color: #94a3b8;">Cliquez sur "Nouvelle immobilisation" pour en créer une</p>
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($immobilisations as $imm) : ?>
                                        <tr data-category="<?php echo isset($imm['categorie']) ? $imm['categorie'] : ''; ?>"
                                            data-status="<?php echo isset($imm['statut']) ? $imm['statut'] : ''; ?>"
                                            data-id="<?php echo isset($imm['id']) ? $imm['id'] : ''; ?>">

                                            <td><strong><?php echo isset($imm['code']) ? htmlspecialchars($imm['code']) : ''; ?></strong></td>

                                            <td>
                                                <strong><?php echo isset($imm['nom']) ? htmlspecialchars($imm['nom']) : ''; ?></strong>
                                                <?php if (!empty($imm['description'])) : ?>
                                                    <br>
                                                    <small style="color: #94a3b8; font-size: 11px;">
                                                        <?php echo isset($imm['description']) ? htmlspecialchars(substr($imm['description'], 0, 50)) : ''; ?>...
                                                    </small>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                    <span style="background: #f1f5f9; padding: 3px 12px; border-radius: 12px; font-size: 11px; color: #475569;">
                                                        <?php echo isset($imm['categorie']) ? htmlspecialchars($imm['categorie']) : ''; ?>
                                                    </span>
                                            </td>

                                            <td>
                                                <?php echo isset($imm['valeur_originale']) ? number_format($imm['valeur_originale'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                            </td>

                                            <td>
                                                <?php echo isset($imm['valeur_nette']) ? number_format($imm['valeur_nette'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                            </td>

                                            <td>
                                                    <span class="badge-status <?php echo isset($imm['statut']) ? $imm['statut'] : 'actif'; ?>">
                                                        <?php
                                                        $statusLabels = [
                                                            'actif' => 'Actif',
                                                            'amorti' => 'Amorti',
                                                            'ceder' => 'Cédé',
                                                            'sortie' => 'Sortie'
                                                        ];
                                                        echo isset($statusLabels[$imm['statut']]) ? $statusLabels[$imm['statut']] : $imm['statut'];
                                                        ?>
                                                    </span>
                                            </td>

                                            <td style="text-align: center;">
                                                <div class="dropdown">
                                                    <button class="btn-action-dropdown dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                        Actions <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu actions-menu dropdown-menu-right">
                                                        <li>
                                                            <a onclick="viewDetails(<?php echo isset($imm['id']) ? $imm['id'] : 0; ?>)" style="cursor: pointer;">
                                                                <i class="fa fa-eye" style="color: #8b5cf6;"></i> Voir détails
                                                            </a>
                                                        </li>
                                                        <li role="separator" class="divider"></li>
                                                        <li>
                                                            <a onclick="openEditModal(<?php echo isset($imm['id']) ? $imm['id'] : 0; ?>)">
                                                                <i class="fa fa-pencil" style="color: #3b82f6;"></i> Modifier
                                                            </a>
                                                        </li>
                                                        <?php if ($imm['statut'] == 'actif') : ?>
                                                            <li>
                                                                <a onclick="calculerAmortissement(<?php echo isset($imm['id']) ? $imm['id'] : 0; ?>)">
                                                                    <i class="fa fa-calculator" style="color: #f59e0b;"></i> Calculer amortissement
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a onclick="openCederModal(<?php echo isset($imm['id']) ? $imm['id'] : 0; ?>)" style="cursor: pointer;">
                                                                    <i class="fa fa-handshake-o" style="color: #10b981;"></i> Céder
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                        <li>
                                                            <a href="#" class="text-danger" onclick="confirmDelete(event, <?php echo isset($imm['id']) ? $imm['id'] : 0; ?>, '<?php echo isset($imm['nom']) ? htmlspecialchars($imm['nom']) : ''; ?>')">
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
<div id="immobilisationFormModal" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 id="formModalTitle" style="margin: 0; color: white; font-size: 18px; font-weight: 600;">
                        <i class="fa fa-plus-circle" id="formModalIcon" style="color: #60a5fa;"></i>
                        <span id="formTitleText">Nouvelle immobilisation</span>
                    </h4>
                    <div class="modal-subtitle" id="formModalSubtitle" style="color: rgba(255,255,255,0.7); font-size: 12px; margin-top: 2px;">
                        Remplissez les informations ci-dessous
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form id="immobilisationForm" method="post" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" id="edit_id" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Informations générales -->
                            <div style="background: #f8fafc; padding: 15px; border-radius: 6px; margin-bottom: 16px;">
                                <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px;">
                                    <i class="fa fa-info-circle" style="margin-right: 8px; color: #3B82F6;"></i> INFORMATIONS GÉNÉRALES
                                </h5>

                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_nom" name="nom" placeholder="Nom de l'immobilisation" style="height: 38px; font-size: 13px;" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Catégorie <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="edit_categorie" name="categorie" placeholder="Ex: Matériel, Véhicule..." style="height: 38px; font-size: 13px;" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Type</label>
                                            <select class="form-control" id="edit_type" name="type_immobilisation" style="height: 38px; font-size: 13px;">
                                                <option value="corporelle">Corporelle</option>
                                                <option value="incorporelle">Incorporelle</option>
                                                <option value="financiere">Financière</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" style="margin-bottom: 0;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Description</label>
                                    <textarea class="form-control" id="edit_description" name="description" rows="2" placeholder="Description..." style="font-size: 13px; resize: vertical; min-height: 50px;"></textarea>
                                </div>
                            </div>

                            <!-- Acquisition -->
                            <div style="background: #eff6ff; padding: 15px; border-radius: 6px; border-left: 4px solid #3B82F6;">
                                <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dbeafe; padding-bottom: 6px;">
                                    <i class="fa fa-shopping-cart" style="margin-right: 8px; color: #3B82F6;"></i> ACQUISITION
                                </h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Date d'acquisition <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control date" id="edit_date_acquisition" name="date_acquisition" readonly required style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Date mise en service</label>
                                            <input type="text" class="form-control date" id="edit_date_mise_service" name="date_mise_en_service" readonly style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Valeur originale <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="edit_valeur_originale" name="valeur_originale" placeholder="0" style="height: 38px; font-size: 13px;" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Valeur résiduelle</label>
                                            <input type="number" class="form-control" id="edit_valeur_residuelle" name="valeur_residuelle" placeholder="0" style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Durée (années)</label>
                                            <input type="number" class="form-control" id="edit_duree" name="duree_amortissement" placeholder="5" style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Mode amortissement</label>
                                            <select class="form-control" id="edit_mode" name="mode_amortissement" style="height: 38px; font-size: 13px;">
                                                <option value="lineaire">Linéaire</option>
                                                <option value="degresif">Dégressif</option>
                                                <option value="variable">Variable</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Fournisseur et facture -->
                            <div style="background: #f8fafc; padding: 15px; border-radius: 6px; margin-bottom: 16px;">
                                <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px;">
                                    <i class="fa fa-truck" style="margin-right: 8px; color: #3B82F6;"></i> FOURNISSEUR & FACTURE
                                </h5>

                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Fournisseur</label>
                                    <input type="text" class="form-control" id="edit_fournisseur" name="fournisseur_id" placeholder="Nom du fournisseur" style="height: 38px; font-size: 13px;">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">N° facture</label>
                                            <input type="text" class="form-control" id="edit_num_facture" name="num_facture" placeholder="Facture n°" style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">N° série</label>
                                            <input type="text" class="form-control" id="edit_num_serie" name="num_serie" placeholder="Numéro de série" style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Localisation et responsable -->
                            <div style="background: #eff6ff; padding: 15px; border-radius: 6px; border-left: 4px solid #3B82F6; margin-bottom: 16px;">
                                <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dbeafe; padding-bottom: 6px;">
                                    <i class="fa fa-map-marker" style="margin-right: 8px; color: #3B82F6;"></i> LOCALISATION & RESPONSABLE
                                </h5>

                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Localisation</label>
                                    <input type="text" class="form-control" id="edit_localisation" name="localisation" placeholder="Bâtiment, bureau..." style="height: 38px; font-size: 13px;">
                                </div>

                                <div class="form-group" style="margin-bottom: 0;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Responsable</label>
                                    <input type="text" class="form-control" id="edit_responsable" name="responsable" placeholder="Nom du responsable" style="height: 38px; font-size: 13px;">
                                </div>
                            </div>

                            <!-- Statut -->
                            <div style="background: #f0fdf4; padding: 15px; border-radius: 6px; border-left: 4px solid #10b981;">
                                <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dcfce7; padding-bottom: 6px;">
                                    <i class="fa fa-cog" style="margin-right: 8px; color: #10b981;"></i> STATUT
                                </h5>

                                <div class="form-group" style="margin-bottom: 0;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Statut</label>
                                    <select class="form-control" id="edit_statut" name="statut" style="height: 38px; font-size: 13px;">
                                        <option value="actif">Actif</option>
                                        <option value="amorti">Amorti</option>
                                        <option value="ceder">Cédé</option>
                                        <option value="sortie">Sortie</option>
                                    </select>
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

<!-- ========================================== -->
<!-- MODAL CESSION                              -->
<!-- ========================================== -->
<div id="cessionModal" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-md" style="max-width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 style="margin: 0; color: white; font-size: 18px; font-weight: 600;">
                        <i class="fa fa-handshake-o" style="color: #60a5fa;"></i> Céder l'immobilisation
                    </h4>
                    <div class="modal-subtitle" style="color: rgba(255,255,255,0.7); font-size: 12px; margin-top: 2px;">
                        Remplissez les informations de cession
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form id="cessionForm">
                <input type="hidden" name="id" id="cession_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label style="font-weight: 600; font-size: 13px; color: #334155;">Montant de cession <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="cession_montant" name="montant_cession" placeholder="0" style="height: 38px; font-size: 13px;" required>
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 600; font-size: 13px; color: #334155;">Acheteur <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cession_acheteur" name="acheteur" placeholder="Nom de l'acheteur" style="height: 38px; font-size: 13px;" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-weight: 600; font-size: 13px; color: #334155;">Motif</label>
                        <textarea class="form-control" id="cession_motif" name="motif" rows="2" placeholder="Motif de la cession..." style="font-size: 13px; resize: vertical;"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-check"></i> Confirmer la cession
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL DÉTAILS                              -->
<!-- ========================================== -->
<div id="immobilisationDetailsModal" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; border-radius: 8px 8px 0 0;">
                <h4 style="margin: 0; color: white;">
                    <i class="fa fa-info-circle" style="color: #60a5fa;"></i> Détails de l'immobilisation
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

        $('#immobilisationFormModal').on('hidden.bs.modal', function() {
            $('#immobilisationForm')[0].reset();
            $('#edit_id').val('');
            $('#edit_date_acquisition').val(getCurrentDate());
            $('.text-danger').html('');
            $('.form-group').removeClass('has-error');
            $('.form-control').css('border-color', '');
        });

        $('#immobilisationForm').on('submit', function(e) {
            e.preventDefault();
            var editId = $('#edit_id').val();
            if (editId && editId !== '') {
                submitEditForm($(this));
            } else {
                submitAddForm($(this));
            }
        });

        $('#cessionForm').on('submit', function(e) {
            e.preventDefault();
            submitCession($(this));
        });

        $('#filterCategory, #filterStatus').on('change', function() {
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
        $('#formTitleText').text('Nouvelle immobilisation');
        $('#formModalSubtitle').text('Remplissez les informations ci-dessous');
        $('#formSubmitText').text('Enregistrer');
        $('#formSubmitBtn').removeClass('btn-warning').addClass('btn-success');
        $('#immobilisationForm').attr('action', '<?php echo site_url('admin/immobilisations/add_ajax'); ?>');
        $('#immobilisationForm')[0].reset();
        $('#edit_id').val('');
        $('#edit_date_acquisition').val(getCurrentDate());
        $('#edit_statut').val('actif');
        $('#edit_type').val('corporelle');
        $('#edit_mode').val('lineaire');
        $('.text-danger').html('');
        $('.form-group').removeClass('has-error');
        $('.form-control').css('border-color', '');
        $('#immobilisationFormModal').modal('show');
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
            url: '<?php echo base_url(); ?>admin/immobilisations/get_data/' + id,
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                hideSpinner();
                Swal.close();

                if (response.success) {
                    var data = response.immobilisation;
                    fillEditForm(data);
                    $('#immobilisationFormModal').modal('show');
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
        $('#formTitleText').text('Modifier l\'immobilisation');
        $('#formModalSubtitle').text('Mettez à jour les informations');
        $('#formSubmitText').text('Mettre à jour');
        $('#formSubmitBtn').removeClass('btn-success').addClass('btn-warning');
        $('#immobilisationForm').attr('action', '<?php echo site_url('admin/immobilisations/update_ajax'); ?>');

        $('#edit_id').val(data.id);
        $('#edit_nom').val(data.nom || '');
        $('#edit_description').val(data.description || '');
        $('#edit_categorie').val(data.categorie || '');
        $('#edit_type').val(data.type_immobilisation || 'corporelle');
        $('#edit_valeur_originale').val(data.valeur_originale || '');
        $('#edit_valeur_residuelle').val(data.valeur_residuelle || 0);
        $('#edit_duree').val(data.duree_amortissement || '');
        $('#edit_mode').val(data.mode_amortissement || 'lineaire');
        $('#edit_fournisseur').val(data.fournisseur_id || '');
        $('#edit_num_facture').val(data.num_facture || '');
        $('#edit_num_serie').val(data.num_serie || '');
        $('#edit_localisation').val(data.localisation || '');
        $('#edit_responsable').val(data.responsable || '');
        $('#edit_statut').val(data.statut || 'actif');

        if (data.date_acquisition) {
            $('#edit_date_acquisition').val(formatDate(data.date_acquisition));
        }
        if (data.date_mise_en_service) {
            $('#edit_date_mise_service').val(formatDate(data.date_mise_en_service));
        }
    }

    // ========================================== //
    // SOUMISSION - AJOUT (AJAX)                  //
    // ========================================== //
    // ========================================== //
    // SOUMISSION DU FORMULAIRE - AJOUT (AJAX)    //
    // ========================================== //
    function submitAddForm(form) {
        if (!validateForm()) return false;
        showSpinner();

        var formData = new FormData(form[0]);

        $.ajax({
            url: '<?php echo site_url('admin/immobilisations/add_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner();
                console.log('Réponse reçue:', response);

                // Vérifier si la réponse est un objet JSON valide
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        console.error('Erreur de parsing JSON:', e);
                        showError('Erreur de format de réponse');
                        return;
                    }
                }

                // Vérifier si la réponse est un succès
                if (response.success === true) {
                    $('#immobilisationFormModal').modal('hide');
                    showSuccess(response.message || 'Immobilisation ajoutée avec succès');

                    // Recharger la page après 1.5 secondes
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    // Afficher l'erreur
                    var errorMsg = response.message || 'Erreur lors de l\'ajout';
                    showError(errorMsg);
                }
            },
            error: function(xhr, status, error) {
                hideSpinner();
                console.error('Erreur AJAX:', status, error);
                console.error('Réponse brute:', xhr.responseText);

                var errorMsg = 'Une erreur est survenue lors de la communication avec le serveur.';

                // Essayer de parser la réponse
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMsg = response.message;
                    }
                } catch(e) {
                    // Si ce n'est pas du JSON, afficher le texte brut
                    if (xhr.responseText && xhr.responseText.length < 200) {
                        errorMsg = xhr.responseText;
                    }
                }

                showError(errorMsg);
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
            url: '<?php echo site_url('admin/immobilisations/update_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner();
                if (response.success) {
                    $('#immobilisationFormModal').modal('hide');
                    showSuccess(response.message || 'Immobilisation mise à jour avec succès');
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

        var nom = $('#edit_nom').val().trim();
        if (nom === '') {
            $('#edit_nom').css('border-color', '#ef4444');
            $('#edit_nom').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez renseigner le nom');
            return false;
        }

        var categorie = $('#edit_categorie').val().trim();
        if (categorie === '') {
            $('#edit_categorie').css('border-color', '#ef4444');
            $('#edit_categorie').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez renseigner la catégorie');
            return false;
        }

        var date = $('#edit_date_acquisition').val();
        if (date === '' || date === 'dd/mm/yyyy') {
            $('#edit_date_acquisition').css('border-color', '#ef4444');
            $('#edit_date_acquisition').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez sélectionner une date');
            return false;
        }

        var valeur = $('#edit_valeur_originale').val();
        if (valeur === '' || parseFloat(valeur) <= 0) {
            $('#edit_valeur_originale').css('border-color', '#ef4444');
            $('#edit_valeur_originale').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez renseigner une valeur valide');
            return false;
        }

        return isValid;
    }

    // ========================================== //
    // CESSION                                    //
    // ========================================== //
    function openCederModal(id) {
        $('#cession_id').val(id);
        $('#cession_montant').val('');
        $('#cession_acheteur').val('');
        $('#cession_motif').val('');
        $('#cessionModal').modal('show');
    }

    function submitCession(form) {
        var id = $('#cession_id').val();
        var montant = $('#cession_montant').val();
        var acheteur = $('#cession_acheteur').val().trim();
        var motif = $('#cession_motif').val();

        if (montant === '' || parseFloat(montant) <= 0) {
            showError('Veuillez renseigner un montant valide');
            return;
        }

        if (acheteur === '') {
            showError('Veuillez renseigner le nom de l\'acheteur');
            return;
        }

        showSpinner();

        $.ajax({
            url: '<?php echo site_url('admin/immobilisations/ceder'); ?>',
            type: 'POST',
            data: {
                id: id,
                montant_cession: montant,
                acheteur: acheteur,
                motif: motif
            },
            dataType: 'json',
            success: function(response) {
                hideSpinner();
                if (response.success) {
                    $('#cessionModal').modal('hide');
                    showSuccess(response.message || 'Immobilisation cédée avec succès');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    showError(response.message || 'Erreur lors de la cession');
                }
            },
            error: function(xhr, status, error) {
                hideSpinner();
                handleAjaxError(xhr, status, error);
            }
        });
    }

    // ========================================== //
    // CALCULER AMORTISSEMENT                     //
    // ========================================== //
    function calculerAmortissement(id) {
        Swal.fire({
            title: 'Confirmation',
            text: 'Voulez-vous calculer l\'amortissement pour cette immobilisation ?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Oui, calculer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                showSpinner();
                window.location.href = '<?php echo base_url('admin/immobilisations/calculer_amortissement/'); ?>' + id;
            }
        });
    }

    // ========================================== //
    // FILTRES                                    //
    // ========================================== //
    function applyFilters() {
        const category = document.getElementById('filterCategory').value;
        const status = document.getElementById('filterStatus').value;

        const rows = document.querySelectorAll('#immobilisationsTableBody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            if (row.cells.length === 1 && row.cells[0].colSpan === 7) return;

            const rowCategory = row.dataset.category || '';
            const rowStatus = row.dataset.status || '';

            let show = true;
            if (category && rowCategory !== category) show = false;
            if (status && rowStatus !== status) show = false;

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        let noResult = document.getElementById('noResultMessage');
        if (!noResult) {
            noResult = document.createElement('tr');
            noResult.id = 'noResultMessage';
            noResult.innerHTML = '<td colspan="7" class="text-center text-muted" style="padding:40px 0;">Aucune immobilisation ne correspond aux filtres</td>';
            document.querySelector('#immobilisationsTableBody').appendChild(noResult);
        }
        noResult.style.display = visibleCount === 0 ? '' : 'none';
    }

    function resetFilters() {
        document.getElementById('filterCategory').value = '';
        document.getElementById('filterStatus').value = '';
        applyFilters();
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    function confirmDelete(event, id, nom) {
        event.preventDefault();

        Swal.fire({
            title: 'Confirmation de suppression',
            text: 'Supprimer définitivement l\'immobilisation "' + nom + '" ?',
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
                window.location.href = '<?php echo base_url("admin/immobilisations/delete/"); ?>' + id;
            }
        });
    }

    // ========================================== //
    // VOIR DÉTAILS                               //
    // ========================================== //
    function viewDetails(id) {
        showSpinner();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/immobilisations/details/' + id,
            success: function(result) {
                hideSpinner();
                $('#detailsContent').html(result);
                $('#immobilisationDetailsModal').modal('show');
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

    function handleAjaxError(xhr, status, error) {
        var errorMsg = 'Une erreur est survenue lors de la communication avec le serveur.';
        if (xhr.status === 404) errorMsg = 'La page demandée est introuvable.';
        else if (xhr.status === 500) errorMsg = 'Erreur interne du serveur.';
        else if (xhr.status === 403) errorMsg = 'Vous n\'avez pas les droits nécessaires.';
        else if (xhr.status === 0) errorMsg = 'Impossible de se connecter au serveur.';
        showError(errorMsg);
    }

    // ========================================== //
    // TEST DE LA SOUMISSION                       //
    // ========================================== //
    $(document).ready(function() {
        // Vérifier que le formulaire existe
        console.log('Formulaire trouvé:', $('#immobilisationForm').length);

        // Vérifier que le bouton submit existe
        console.log('Bouton submit trouvé:', $('#formSubmitBtn').length);

        // Tester la soumission manuelle
        $('#formSubmitBtn').on('click', function(e) {
            e.preventDefault();
            console.log('Bouton submit cliqué');

            // Vérifier les champs
            var nom = $('#edit_nom').val();
            console.log('Nom:', nom);

            var categorie = $('#edit_categorie').val();
            console.log('Catégorie:', categorie);

            var valeur = $('#edit_valeur_originale').val();
            console.log('Valeur:', valeur);

            var date = $('#edit_date_acquisition').val();
            console.log('Date:', date);

            // Si tous les champs sont remplis, soumettre
            if (nom && categorie && valeur && date) {
                submitForm();
            } else {
                alert('Veuillez remplir tous les champs obligatoires');
            }
        });
    });

    // ========================================== //
    // SOUMISSION DU FORMULAIRE                    //
    // ========================================== //
    function submitForm() {
        showSpinner();

        var formData = new FormData();
        formData.append('nom', $('#edit_nom').val());
        formData.append('categorie', $('#edit_categorie').val());
        formData.append('description', $('#edit_description').val());
        formData.append('type_immobilisation', $('#edit_type').val());
        formData.append('date_acquisition', $('#edit_date_acquisition').val());
        formData.append('date_mise_en_service', $('#edit_date_mise_service').val());
        formData.append('valeur_originale', $('#edit_valeur_originale').val());
        formData.append('valeur_residuelle', $('#edit_valeur_residuelle').val());
        formData.append('duree_amortissement', $('#edit_duree').val());
        formData.append('mode_amortissement', $('#edit_mode').val());
        formData.append('fournisseur_id', $('#edit_fournisseur').val());
        formData.append('num_facture', $('#edit_num_facture').val());
        formData.append('num_serie', $('#edit_num_serie').val());
        formData.append('localisation', $('#edit_localisation').val());
        formData.append('responsable', $('#edit_responsable').val());
        formData.append('statut', $('#edit_statut').val());

        console.log('Données envoyées:', formData);

        $.ajax({
            url: '<?php echo site_url('admin/immobilisations/add_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner();
                console.log('Réponse:', response);

                if (response.success) {
                    $('#immobilisationFormModal').modal('hide');
                    showSuccess(response.message || 'Immobilisation ajoutée avec succès');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showError(response.message || 'Erreur lors de l\'ajout');
                }
            },
            error: function(xhr, status, error) {
                hideSpinner();
                console.error('Erreur AJAX:', status, error);
                console.error('Réponse:', xhr.responseText);

                var errorMsg = 'Une erreur est survenue';
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMsg = response.message;
                    }
                } catch(e) {
                    errorMsg = 'Erreur serveur: ' + xhr.responseText.substring(0, 100);
                }
                showError(errorMsg);
            }
        });
    }
</script>