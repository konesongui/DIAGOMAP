<!-- ============================================================
     PAGE : Gestion des prédicateurs
     DESCRIPTION : Interface moderne pour la gestion des prédicateurs
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

    .stat-card:nth-child(1) { border-left-color: #3b82f6; }
    .stat-card:nth-child(2) { border-left-color: #10b981; }
    .stat-card:nth-child(3) { border-left-color: #f59e0b; }
    .stat-card:nth-child(4) { border-left-color: #8b5cf6; }

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
    .badge-status.actif { background: #d1fae5; color: #065f46; }
    .badge-status.inactif { background: #fef3c7; color: #92400e; }
    .badge-status.vacation { background: #dbeafe; color: #1d4ed8; }
    .badge-status.retraite { background: #e2e8f0; color: #475569; }

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

    .photo-upload-wrapper {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid var(--border-light);
        background: #f1f5f9;
        cursor: pointer;
        transition: var(--transition);
    }

    .photo-upload-wrapper:hover {
        border-color: var(--primary-light);
    }

    .photo-upload-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .photo-upload-wrapper .photo-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-size: 40px;
        color: #94a3b8;
    }

    .photo-upload-wrapper .photo-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.6);
        color: white;
        padding: 4px;
        font-size: 10px;
        text-align: center;
        opacity: 0;
        transition: var(--transition);
    }

    .photo-upload-wrapper:hover .photo-overlay {
        opacity: 1;
    }

    .photo-upload-wrapper input[type="file"] {
        display: none;
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
                            <i class="fa fa-microphone"></i> Gestion des prédicateurs
                            <span style="font-size: 14px; font-weight: 400; color: #94a3b8; margin-left: 8px;">
                                (<?php echo isset($stats['total']) ? $stats['total'] : 0; ?>)
                            </span>
                        </h3>
                        <div class="d-flex align-items-center gap-3" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url(); ?>admin/admin/hub" class="btn-back" title="Retour au tableau de bord">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <button type="button" class="btn-add-modern" onclick="openAddModal()">
                                <i class="fa fa-plus-circle" style="margin-right: 6px;"></i> Nouveau prédicateur
                            </button>
                        </div>
                    </div>

                    <!-- ===== STATISTIQUES ===== -->
                    <div style="padding: 20px 24px 0;">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-microphone"></i> Total</h4>
                                    <p class="number"><?php echo isset($stats['total']) ? $stats['total'] : 0; ?></p>
                                </div>
                                <div class="stat-icon"><i class="fa fa-microphone"></i></div>
                            </div>
                            <div class="stat-card" style="border-left-color: #10b981;">
                                <div class="stat-info">
                                    <h4><i class="fa fa-check-circle" style="color:#10b981;"></i> Actifs</h4>
                                    <p class="number"><?php echo isset($stats['statut_actif']) ? $stats['statut_actif'] : 0; ?></p>
                                </div>
                                <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
                            </div>
                            <div class="stat-card" style="border-left-color: #3b82f6;">
                                <div class="stat-info">
                                    <h4><i class="fa fa-male" style="color:#3b82f6;"></i> Hommes</h4>
                                    <p class="number"><?php echo isset($stats['sexe_M']) ? $stats['sexe_M'] : 0; ?></p>
                                </div>
                                <div class="stat-icon"><i class="fa fa-male"></i></div>
                            </div>
                            <div class="stat-card" style="border-left-color: #f59e0b;">
                                <div class="stat-info">
                                    <h4><i class="fa fa-female" style="color:#f59e0b;"></i> Femmes</h4>
                                    <p class="number"><?php echo isset($stats['sexe_F']) ? $stats['sexe_F'] : 0; ?></p>
                                </div>
                                <div class="stat-icon"><i class="fa fa-female"></i></div>
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
                                <label><i class="fa fa-venus-mars"></i> Sexe :</label>
                                <select id="filterSexe" onchange="applyFilters()">
                                    <option value="">Tous</option>
                                    <option value="M">Homme</option>
                                    <option value="F">Femme</option>
                                </select>
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
                            <table class="table table-modern" id="predicateursTable">
                                <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 18%;">Nom</th>
                                    <th style="width: 10%;">Titre</th>
                                    <th style="width: 12%;">Spécialité</th>
                                    <th style="width: 10%;">Expérience</th>
                                    <th style="width: 15%;">Contact</th>
                                    <th style="width: 10%;">Statut</th>
                                    <th style="text-align: center; width: 100px;">Action</th>
                                </tr>
                                </thead>
                                <tbody id="predicateursTableBody">
                                <?php if (empty($predicateurs)) : ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted" style="padding: 60px 0;">
                                            <i class="fa fa-microphone" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
                                            <p style="font-size: 16px; color: #64748b;">Aucun prédicateur enregistré</p>
                                            <p style="font-size: 13px; color: #94a3b8;">Cliquez sur "Nouveau prédicateur" pour en ajouter un</p>
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($predicateurs as $index => $predicateur) : ?>
                                        <tr data-status="<?php echo isset($predicateur['statut']) ? $predicateur['statut'] : ''; ?>"
                                            data-sexe="<?php echo isset($predicateur['sexe']) ? $predicateur['sexe'] : ''; ?>"
                                            data-id="<?php echo isset($predicateur['id']) ? $predicateur['id'] : ''; ?>">

                                            <td><?php echo $index + 1; ?></td>

                                            <td>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <?php if (!empty($predicateur['photo'])) : ?>
                                                        <img src="<?php echo base_url('uploads/predicateurs/' . $predicateur['photo']); ?>"
                                                             style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border-light);">
                                                    <?php else : ?>
                                                        <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #273772, #3b82f6); display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: 600;">
                                                            <?php echo isset($predicateur['nom']) ? strtoupper(substr($predicateur['nom'], 0, 1)) : '?'; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <strong><?php echo isset($predicateur['nom']) ? htmlspecialchars($predicateur['nom']) : ''; ?>
                                                            <?php echo isset($predicateur['prenom']) ? htmlspecialchars($predicateur['prenom']) : ''; ?></strong>
                                                        <?php if (!empty($predicateur['titre'])) : ?>
                                                            <br>
                                                            <small style="color: #94a3b8; font-size: 11px;">
                                                                <?php echo isset($predicateur['titre']) ? htmlspecialchars($predicateur['titre']) : ''; ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>

                                            <td><?php echo isset($predicateur['titre']) ? htmlspecialchars($predicateur['titre']) : '—'; ?></td>

                                            <td>
                                                    <span style="background: #f1f5f9; padding: 3px 12px; border-radius: 12px; font-size: 11px; color: #475569;">
                                                        <?php echo isset($predicateur['specialite']) ? htmlspecialchars($predicateur['specialite']) : '—'; ?>
                                                    </span>
                                            </td>

                                            <td>
                                                    <span style="font-weight: 600; color: #f59e0b;">
                                                        <?php echo isset($predicateur['annees_experience']) ? $predicateur['annees_experience'] . ' ans' : '—'; ?>
                                                    </span>
                                            </td>

                                            <td>
                                                <?php if (!empty($predicateur['telephone'])) : ?>
                                                    <div style="font-size: 12px;">
                                                        <i class="fa fa-phone" style="color: #3b82f6;"></i> <?php echo isset($predicateur['telephone']) ? htmlspecialchars($predicateur['telephone']) : ''; ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($predicateur['email'])) : ?>
                                                    <div style="font-size: 11px; color: #94a3b8;">
                                                        <i class="fa fa-envelope"></i> <?php echo isset($predicateur['email']) ? htmlspecialchars($predicateur['email']) : ''; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                    <span class="badge-status <?php echo isset($predicateur['statut']) ? $this->predicateurs_model->get_status_badge($predicateur['statut']) : 'actif'; ?>">
                                                        <?php echo isset($statuses[$predicateur['statut']]) ? $statuses[$predicateur['statut']] : $predicateur['statut']; ?>
                                                    </span>
                                            </td>

                                            <td style="text-align: center;">
                                                <div class="dropdown">
                                                    <button class="btn-action-dropdown dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                        Actions <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu actions-menu dropdown-menu-right">
                                                        <li>
                                                            <a onclick="viewPredicateur(<?php echo isset($predicateur['id']) ? $predicateur['id'] : 0; ?>)" style="cursor: pointer;">
                                                                <i class="fa fa-eye" style="color: #8b5cf6;"></i> Voir détails
                                                            </a>
                                                        </li>
                                                        <li role="separator" class="divider"></li>
                                                        <li>
                                                            <a onclick="openEditModal(<?php echo isset($predicateur['id']) ? $predicateur['id'] : 0; ?>)">
                                                                <i class="fa fa-pencil" style="color: #3b82f6;"></i> Modifier
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#" class="text-danger" onclick="confirmDelete(event, <?php echo isset($predicateur['id']) ? $predicateur['id'] : 0; ?>, '<?php echo isset($predicateur['nom']) ? htmlspecialchars($predicateur['nom']) . ' ' . htmlspecialchars($predicateur['prenom']) : ''; ?>')">
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
<div id="predicateurFormModal" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 id="formModalTitle" style="margin: 0; color: white; font-size: 18px; font-weight: 600;">
                        <i class="fa fa-plus-circle" id="formModalIcon" style="color: #60a5fa;"></i>
                        <span id="formTitleText">Nouveau prédicateur</span>
                    </h4>
                    <div class="modal-subtitle" id="formModalSubtitle" style="color: rgba(255,255,255,0.7); font-size: 12px; margin-top: 2px;">
                        Remplissez les informations du prédicateur
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form id="predicateurForm" method="post" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" id="edit_id" value="">
                <div class="modal-body">
                    <div class="row">
                        <!-- ===== COLONNE GAUCHE ===== -->
                        <div class="col-md-7">
                            <!-- Photo -->
                            <div class="text-center mb-3">
                                <div class="photo-upload-wrapper" onclick="document.getElementById('photoInput').click()">
                                    <div id="photoPreview">
                                        <div class="photo-placeholder">
                                            <i class="fa fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="photo-overlay">Changer la photo</div>
                                    <input type="file" id="photoInput" name="photo" accept="image/*" onchange="previewPhoto(this)">
                                </div>
                                <small style="color: #94a3b8; font-size: 11px;">Cliquez sur la photo pour changer</small>
                            </div>

                            <div style="background: #f8fafc; padding: 15px; border-radius: 6px; margin-bottom: 16px;">
                                <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px;">
                                    <i class="fa fa-id-card" style="margin-right: 8px; color: #3B82F6;"></i> IDENTITÉ
                                </h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Nom <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="edit_nom" name="nom" placeholder="Nom" style="height: 38px; font-size: 13px;" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Prénom <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="edit_prenom" name="prenom" placeholder="Prénom" style="height: 38px; font-size: 13px;" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Titre</label>
                                            <input type="text" class="form-control" id="edit_titre" name="titre" placeholder="Ex: Pasteur, Évangéliste..." style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Sexe</label>
                                            <select class="form-control" id="edit_sexe" name="sexe" style="height: 38px; font-size: 13px;">
                                                <option value="M">Homme</option>
                                                <option value="F">Femme</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Date naissance</label>
                                            <input type="text" class="form-control date" id="edit_date_naissance" name="date_naissance" readonly style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Spécialité</label>
                                            <input type="text" class="form-control" id="edit_specialite" name="specialite" placeholder="Ex: Prédication, Enseignement..." style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Biographie</label>
                                    <textarea class="form-control" id="edit_biographie" name="biographie" rows="2" placeholder="Biographie du prédicateur..." style="font-size: 13px; resize: vertical;"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- ===== COLONNE DROITE ===== -->
                        <div class="col-md-5">
                            <!-- Contact -->
                            <div style="background: #eff6ff; padding: 15px; border-radius: 6px; border-left: 4px solid #3B82F6; margin-bottom: 16px;">
                                <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dbeafe; padding-bottom: 6px;">
                                    <i class="fa fa-phone" style="margin-right: 8px; color: #3B82F6;"></i> CONTACT
                                </h5>

                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Téléphone</label>
                                    <input type="text" class="form-control" id="edit_telephone" name="telephone" placeholder="Téléphone" style="height: 38px; font-size: 13px;">
                                </div>

                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Email</label>
                                    <input type="email" class="form-control" id="edit_email" name="email" placeholder="Email" style="height: 38px; font-size: 13px;">
                                </div>

                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Adresse</label>
                                    <input type="text" class="form-control" id="edit_adresse" name="adresse" placeholder="Adresse" style="height: 38px; font-size: 13px;">
                                </div>
                            </div>

                            <!-- Ministère -->
                            <div style="background: #f0fdf4; padding: 15px; border-radius: 6px; border-left: 4px solid #10b981; margin-bottom: 16px;">
                                <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dcfce7; padding-bottom: 6px;">
                                    <i class="fa fa-cross" style="margin-right: 8px; color: #10b981;"></i> MINISTÈRE
                                </h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Années d'expérience</label>
                                            <input type="number" class="form-control" id="edit_experience" name="annees_experience" placeholder="0" style="height: 38px; font-size: 13px;" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Statut</label>
                                            <select class="form-control" id="edit_statut" name="statut" style="height: 38px; font-size: 13px;">
                                                <?php if (isset($statuses) && is_array($statuses)) : ?>
                                                    <?php foreach ($statuses as $key => $label) : ?>
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
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Date ordination</label>
                                            <input type="text" class="form-control date" id="edit_ordination" name="date_ordination" readonly style="height: 38px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" style="margin-bottom: 12px;">
                                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Date affiliation</label>
                                            <input type="text" class="form-control date" id="edit_affiliation" name="date_affiliation" readonly style="height: 38px; font-size: 13px;">
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
<div id="predicateurDetailsModal" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 900px;">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; border-radius: 8px 8px 0 0;">
                <h4 style="margin: 0; color: white;">
                    <i class="fa fa-info-circle" style="color: #60a5fa;"></i> Détails du prédicateur
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

        $('#predicateurFormModal').on('hidden.bs.modal', function() {
            $('#predicateurForm')[0].reset();
            $('#edit_id').val('');
            $('#edit_statut').val('actif');
            $('#edit_sexe').val('M');
            $('#photoPreview').html('<div class="photo-placeholder"><i class="fa fa-user"></i></div>');
            $('.text-danger').html('');
            $('.form-group').removeClass('has-error');
            $('.form-control').css('border-color', '');
        });

        $('#predicateurForm').on('submit', function(e) {
            e.preventDefault();
            var editId = $('#edit_id').val();
            if (editId && editId !== '') {
                submitEditForm($(this));
            } else {
                submitAddForm($(this));
            }
        });

        $('#filterStatus, #filterSexe').on('change', function() {
            applyFilters();
        });
    });

    // ========================================== //
    // PREVIEW PHOTO                              //
    // ========================================== //
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#photoPreview').html('<img src="' + e.target.result + '" style="width: 100%; height: 100%; object-fit: cover;">');
            };
            reader.readAsDataURL(input.files[0]);
        }
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
        $('#formModalIcon').removeClass('fa-pencil-square-o').addClass('fa-plus-circle');
        $('#formTitleText').text('Nouveau prédicateur');
        $('#formModalSubtitle').text('Remplissez les informations du prédicateur');
        $('#formSubmitText').text('Enregistrer');
        $('#formSubmitBtn').removeClass('btn-warning').addClass('btn-success');
        $('#predicateurForm').attr('action', '<?php echo site_url('admin/predicateurs/add_ajax'); ?>');
        $('#predicateurForm')[0].reset();
        $('#edit_id').val('');
        $('#edit_statut').val('actif');
        $('#edit_sexe').val('M');
        $('#photoPreview').html('<div class="photo-placeholder"><i class="fa fa-user"></i></div>');
        $('.text-danger').html('');
        $('#predicateurFormModal').modal('show');
    }

    // ========================================== //
    // OUVERTURE MODAL - MODIFICATION (AJAX)      //
    // ========================================== //
    function openEditModal(id) {
        showSpinner();

        Swal.fire({
            title: 'Chargement...',
            text: 'Récupération des données du prédicateur',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: '<?php echo base_url(); ?>admin/predicateurs/get_data/' + id,
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                hideSpinner();
                Swal.close();

                if (response.success) {
                    var data = response.predicateur;
                    fillEditForm(data);
                    $('#predicateurFormModal').modal('show');
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
        $('#formTitleText').text('Modifier le prédicateur');
        $('#formModalSubtitle').text('Mettez à jour les informations');
        $('#formSubmitText').text('Mettre à jour');
        $('#formSubmitBtn').removeClass('btn-success').addClass('btn-warning');
        $('#predicateurForm').attr('action', '<?php echo site_url('admin/predicateurs/update_ajax'); ?>');

        $('#edit_id').val(data.id);
        $('#edit_nom').val(data.nom);
        $('#edit_prenom').val(data.prenom);
        $('#edit_titre').val(data.titre);
        $('#edit_sexe').val(data.sexe);
        $('#edit_specialite').val(data.specialite);
        $('#edit_biographie').val(data.biographie);
        $('#edit_telephone').val(data.telephone);
        $('#edit_email').val(data.email);
        $('#edit_adresse').val(data.adresse);
        $('#edit_experience').val(data.annees_experience);
        $('#edit_statut').val(data.statut);

        if (data.date_naissance) {
            $('#edit_date_naissance').val(formatDate(data.date_naissance));
        }
        if (data.date_ordination) {
            $('#edit_ordination').val(formatDate(data.date_ordination));
        }
        if (data.date_affiliation) {
            $('#edit_affiliation').val(formatDate(data.date_affiliation));
        }

        if (data.photo) {
            $('#photoPreview').html('<img src="<?php echo base_url('uploads/predicateurs/'); ?>' + data.photo + '" style="width: 100%; height: 100%; object-fit: cover;">');
        }

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
            url: '<?php echo site_url('admin/predicateurs/add_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner();
                if (response.success) {
                    $('#predicateurFormModal').modal('hide');
                    showSuccess(response.message || 'Prédicateur ajouté avec succès');
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
            url: '<?php echo site_url('admin/predicateurs/update_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner();
                if (response.success) {
                    $('#predicateurFormModal').modal('hide');
                    showSuccess(response.message || 'Prédicateur mis à jour avec succès');
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

        var prenom = $('#edit_prenom').val().trim();
        if (prenom === '') {
            $('#edit_prenom').css('border-color', '#ef4444');
            $('#edit_prenom').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez renseigner le prénom');
            return false;
        }

        return isValid;
    }

    // ========================================== //
    // FILTRES                                    //
    // ========================================== //
    function applyFilters() {
        const status = document.getElementById('filterStatus').value;
        const sexe = document.getElementById('filterSexe').value;

        const rows = document.querySelectorAll('#predicateursTableBody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            if (row.cells.length === 1 && row.cells[0].colSpan === 8) return;

            const rowStatus = row.dataset.status || '';
            const rowSexe = row.dataset.sexe || '';

            let show = true;
            if (status && rowStatus !== status) show = false;
            if (sexe && rowSexe !== sexe) show = false;

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        let noResult = document.getElementById('noResultMessage');
        if (!noResult) {
            noResult = document.createElement('tr');
            noResult.id = 'noResultMessage';
            noResult.innerHTML = '<td colspan="8" class="text-center text-muted" style="padding:40px 0;">Aucun prédicateur ne correspond aux filtres</td>';
            document.querySelector('#predicateursTableBody').appendChild(noResult);
        }
        noResult.style.display = visibleCount === 0 ? '' : 'none';
    }

    function resetFilters() {
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterSexe').value = '';
        applyFilters();
    }

    // ========================================== //
    // EXPORTATION                                //
    // ========================================== //
    function exportData(type) {
        const statut = document.getElementById('filterStatus').value;
        const sexe = document.getElementById('filterSexe').value;

        const params = `?statut=${encodeURIComponent(statut)}&sexe=${encodeURIComponent(sexe)}`;

        showSpinner();

        var url = '<?php echo base_url("admin/predicateurs/export_"); ?>' + type + params;
        window.location.href = url;

        setTimeout(function() {
            hideSpinner();
        }, 3000);
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    function confirmDelete(event, id, nom) {
        event.preventDefault();

        Swal.fire({
            title: 'Confirmation de suppression',
            text: 'Supprimer définitivement le prédicateur "' + nom + '" ?',
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
                window.location.href = '<?php echo base_url("admin/predicateurs/delete/"); ?>' + id;
            }
        });
    }

    // ========================================== //
    // VOIR DÉTAILS                               //
    // ========================================== //
    function viewPredicateur(id) {
        showSpinner();
        $.ajax({
            url: '<?php echo base_url(); ?>admin/predicateurs/details/' + id,
            success: function(result) {
                hideSpinner();
                $('#detailsContent').html(result);
                $('#predicateurDetailsModal').modal('show');
            },
            error: function() {
                hideSpinner();
                showError('Impossible de charger les détails');
            }
        });
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