<!-- ============================================================
     PAGE : Gestion des demandes
     DESCRIPTION : Interface moderne pour la gestion des demandes
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
        min-height: 100vh;
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
    .stat-card:nth-child(2) { border-left-color: #f59e0b; }
    .stat-card:nth-child(2) .stat-icon { color: #f59e0b; }
    .stat-card:nth-child(3) { border-left-color: #10b981; }
    .stat-card:nth-child(3) .stat-icon { color: #10b981; }
    .stat-card:nth-child(4) { border-left-color: #ef4444; }
    .stat-card:nth-child(4) .stat-icon { color: #ef4444; }
    .stat-card:nth-child(5) { border-left-color: #8b5cf6; }
    .stat-card:nth-child(5) .stat-icon { color: #8b5cf6; }

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
    .badge-status.rejete { background: #fef2f2; color: #991b1b; }

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
        min-height: 80px;
        resize: vertical;
    }

    .modal-chic select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 32px;
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

    .modal-chic .modal-footer .btn-warning:hover {
        background: #fde68a;
        transform: translateY(-1px);
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
        .modal-chic .modal-body { padding: 16px; }
        .modal-chic .modal-header { padding: 14px 16px; }
        .modal-chic .modal-footer { flex-direction: column; }
        .modal-chic .modal-footer .btn { width: 100%; }
    }

    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
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
                            <i class="fa fa-tasks"></i> Mes demandes
                            <span style="font-size: 14px; font-weight: 400; color: #94a3b8; margin-left: 8px;">
                                (<?php echo $total_demandes ?? 0; ?>)
                            </span>
                        </h3>
                        <div class="d-flex align-items-center gap-3" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url(); ?>admin/admin/hub" class="btn-back" title="Retour au tableau de bord">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <button type="button" class="btn-add-modern" onclick="openAddModal()">
                                <i class="fa fa-plus-circle" style="margin-right: 6px;"></i> Nouvelle demande
                            </button>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- STATISTIQUES                               -->
                    <!-- ========================================== -->
                    <div style="padding: 20px 24px 0;">
                        <div class="stats-grid">
                            <?php
                            $stats = $stats ?? ['total' => 0, 'en_attente' => 0, 'en_cours' => 0, 'termine' => 0, 'rejete' => 0];
                            ?>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-list"></i> Total</h4>
                                    <p class="number"><?php echo $stats['total'] ?? 0; ?></p>
                                </div>

                            </div>

                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-clock-o" style="color:#f59e0b;"></i> En attente</h4>
                                    <p class="number"><?php echo $stats['en_attente'] ?? 0; ?></p>
                                </div>

                            </div>

                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-spinner" style="color:#3b82f6;"></i> En cours</h4>
                                    <p class="number"><?php echo $stats['en_cours'] ?? 0; ?></p>
                                </div>

                            </div>

                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-check-circle" style="color:#10b981;"></i> Terminées</h4>
                                    <p class="number"><?php echo $stats['termine'] ?? 0; ?></p>
                                </div>

                            </div>

                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-times-circle" style="color:#ef4444;"></i> Rejetées</h4>
                                    <p class="number"><?php echo $stats['rejete'] ?? 0; ?></p>
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
                        <!-- BARRE DE FILTRES                          -->
                        <!-- ========================================== -->
                        <div class="filter-bar">
                            <div class="filter-group">
                                <label><i class="fa fa-filter"></i> Statut :</label>
                                <select id="filterStatus" onchange="applyFilters()">
                                    <option value="">Tous</option>
                                    <option value="en_attente">En attente</option>
                                    <option value="en_cours">En cours</option>
                                    <option value="termine">Terminé</option>
                                    <option value="rejete">Rejeté</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label><i class="fa fa-tag"></i> Catégorie :</label>
                                <select id="filterCategory" onchange="applyFilters()">
                                    <option value="">Toutes</option>
                                    <option value="comptabilite">Comptabilité</option>
                                    <option value="ressources_humaines">Ressources Humaines</option>
                                    <option value="informatique">Informatique</option>
                                    <option value="logistique">Logistique</option>
                                    <option value="communication">Communication</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label><i class="fa fa-flag"></i> Priorité :</label>
                                <select id="filterPriority" onchange="applyFilters()">
                                    <option value="">Toutes</option>
                                    <option value="basse">Basse</option>
                                    <option value="normale">Normale</option>
                                    <option value="haute">Haute</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                            </div>
                            <div style="display:flex;gap:6px;margin-left:auto;flex-wrap:wrap;">
                                 <button class="btn-reset" onclick="resetFilters()"><i class="fa fa-undo"></i> Réinitialiser</button>
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- TABLE                                     -->
                        <!-- ========================================== -->
                        <div class="table-responsive">
                            <table class="table table-modern example" id="demandeTable">
                                <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 20%;">Titre</th>
                                    <th style="width: 15%;">Catégorie</th>
                                    <th style="width: 12%;">Priorité</th>
                                    <th style="width: 15%;">Statut</th>
                                    <th style="width: 15%;">Date</th>
                                    <th style="text-align: center; width: 100px;">Action</th>
                                </tr>
                                </thead>
                                <tbody id="demandeTableBody">
                                <?php if (empty($demandes)) : ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted" style="padding: 60px 0;">
                                            <i class="fa fa-tasks" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
                                            <p style="font-size: 16px; color: #64748b;">Aucune demande enregistrée</p>
                                            <p style="font-size: 13px; color: #94a3b8;">Cliquez sur "Nouvelle demande" pour en créer une</p>
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($demandes as $index => $demande) :
                                        $statusLabels = [
                                            'en_attente' => 'En attente',
                                            'en_cours' => 'En cours',
                                            'termine' => 'Terminé',
                                            'rejete' => 'Rejeté'
                                        ];
                                        $statusClass = [
                                            'en_attente' => 'en-attente',
                                            'en_cours' => 'en-cours',
                                            'termine' => 'termine',
                                            'rejete' => 'rejete'
                                        ];
                                        $priorityLabels = [
                                            'basse' => 'Basse',
                                            'normale' => 'Normale',
                                            'haute' => 'Haute',
                                            'urgente' => 'Urgente'
                                        ];
                                        $priorityClass = [
                                            'basse' => 'basse',
                                            'normale' => 'normale',
                                            'haute' => 'haute',
                                            'urgente' => 'urgente'
                                        ];
                                        $categoryLabels = [
                                            'comptabilite' => 'Comptabilité',
                                            'ressources_humaines' => 'Ressources Humaines',
                                            'informatique' => 'Informatique',
                                            'logistique' => 'Logistique',
                                            'communication' => 'Communication',
                                            'autre' => 'Autre'
                                        ];
                                        ?>
                                        <tr data-status="<?php echo $demande['statut'] ?? ''; ?>"
                                            data-category="<?php echo $demande['categorie'] ?? ''; ?>"
                                            data-priority="<?php echo $demande['priorite'] ?? ''; ?>"
                                            data-id="<?php echo $demande['id']; ?>">

                                            <td><?php echo $index + 1; ?></td>

                                            <td>
                                                <strong><?php echo htmlspecialchars($demande['titre']); ?></strong>
                                                <br>
                                                <small style="color: #94a3b8; font-size: 11px;">
                                                    <?php echo htmlspecialchars(substr($demande['description'] ?? '', 0, 60)); ?>...
                                                </small>
                                            </td>

                                            <td>
                                                    <span style="background: #f1f5f9; padding: 3px 12px; border-radius: 12px; font-size: 11px; color: #475569;">
                                                        <?php echo $categoryLabels[$demande['categorie']] ?? $demande['categorie']; ?>
                                                    </span>
                                            </td>

                                            <td>
                                                    <span class="badge-priority <?php echo $priorityClass[$demande['priorite']] ?? 'normale'; ?>">
                                                        <i class="fa <?php
                                                        echo $demande['priorite'] == 'urgente' ? 'fa-exclamation-triangle' :
                                                            ($demande['priorite'] == 'haute' ? 'fa-arrow-up' :
                                                                ($demande['priorite'] == 'basse' ? 'fa-arrow-down' : 'fa-minus'));
                                                        ?>"></i>
                                                        <?php echo $priorityLabels[$demande['priorite']] ?? $demande['priorite']; ?>
                                                    </span>
                                            </td>

                                            <td>
                                                    <span class="badge-status <?php echo $statusClass[$demande['statut']] ?? 'en-attente'; ?>">
                                                        <?php echo $statusLabels[$demande['statut']] ?? $demande['statut']; ?>
                                                    </span>
                                            </td>

                                            <td style="font-size: 12px; color: #64748b;">
                                                <?php echo !empty($demande['date_creation']) ? date('d/m/Y', strtotime($demande['date_creation'])) : date('d/m/Y'); ?>
                                            </td>

                                            <td style="text-align: center;">
                                                <div class="dropdown">
                                                    <button class="btn-action-dropdown dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                        Actions <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu actions-menu dropdown-menu-right">
                                                        <li>
                                                            <a onclick="viewDemande(<?php echo $demande['id']; ?>)" style="cursor: pointer;">
                                                                <i class="fa fa-eye" style="color: #8b5cf6;"></i> Voir détails
                                                            </a>
                                                        </li>
                                                        <li role="separator" class="divider"></li>
                                                        <li>
                                                            <a onclick="openEditModal(<?php echo $demande['id']; ?>)">
                                                                <i class="fa fa-pencil" style="color: #3b82f6;"></i> Modifier
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#" class="text-danger" onclick="confirmDelete(event, <?php echo $demande['id']; ?>, '<?php echo htmlspecialchars($demande['titre']); ?>')">
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
<div id="demandeFormModal" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-md" style="max-width: 720px;">
        <div class="modal-content">
            <!-- ===== EN-TÊTE AVEC CROIX DE FERMETURE VISIBLE ===== -->
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; background: linear-gradient(135deg, #1e293b, #0f172a); border-radius: 8px 8px 0 0; position: relative;">
                <div style="flex: 1;">
                    <h4 id="formModalTitle" style="margin: 0; color: white; font-size: 18px; font-weight: 600;">
                        <i class="fa fa-plus-circle" id="formModalIcon" style="color: #60a5fa; margin-right: 8px;"></i>
                        <span id="formTitleText">Nouvelle demande</span>
                    </h4>
                    <div class="modal-subtitle" id="formModalSubtitle" style="color: rgba(255,255,255,0.7); font-size: 12px; margin-top: 2px;">
                        Remplissez les informations ci-dessous
                    </div>
                </div>
                <!-- ===== CROIX DE FERMETURE ===== -->
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"
                        style="color: #ffffff; opacity: 0.8; font-size: 32px; font-weight: 300; text-shadow: none; border: none; border-radius: 50%; padding: 57px; margin: 16px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; flex-shrink: 0; line-height: 1;"
                        onmouseover="this.style.opacity='1'; this.style.background='rgba(255,255,255,0.25)'; this.style.transform='rotate(90deg)';"
                        onmouseout="this.style.opacity='0.8'; this.style.background='rgba(255,255,255,0.1)'; this.style.transform='rotate(0)';">
                    <span style="line-height: 1; font-size: 28px;">&times;</span>
                </button>
            </div>

            <!-- ===== FORMULAIRE ===== -->
            <form id="demandeForm" method="post" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" id="edit_id" value="">
                <div class="modal-body" style="padding: 24px 24px 16px; background: #fafcff;">

                    <!-- Informations de la demande -->
                    <div style="background: #f8fafc; padding: 15px; border-radius: 6px; margin-bottom: 16px;">
                        <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px;">
                            <i class="fa fa-info-circle" style="margin-right: 8px; color: #3B82F6;"></i> INFORMATIONS DE LA DEMANDE
                        </h5>

                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Titre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_titre" name="titre" placeholder="Résumé de votre demande" style="height: 38px; font-size: 13px;" required>
                            <span class="text-danger"><?php echo form_error('titre'); ?></span>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Catégorie <span class="text-danger">*</span></label>
                                    <select class="form-control" id="edit_categorie" name="categorie" style="height: 38px; font-size: 13px;" required>
                                        <option value="">Sélectionner...</option>
                                        <option value="comptabilite">Comptabilité</option>
                                        <option value="ressources_humaines">Ressources Humaines</option>
                                        <option value="informatique">Informatique</option>
                                        <option value="logistique">Logistique</option>
                                        <option value="communication">Communication</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('categorie'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 12px;">
                                    <label style="font-weight: 600; font-size: 12px; color: #334155;">Priorité <span class="text-danger">*</span></label>
                                    <select class="form-control" id="edit_priorite" name="priorite" style="height: 38px; font-size: 13px;" required>
                                        <option value="">Sélectionner...</option>
                                        <option value="basse">Basse</option>
                                        <option value="normale">Normale</option>
                                        <option value="haute">Haute</option>
                                        <option value="urgente">Urgente</option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('priorite'); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_description" name="description" rows="4" placeholder="Décrivez votre demande en détail..." style="font-size: 13px; resize: vertical; min-height: 80px;" required></textarea>
                            <span class="text-danger"><?php echo form_error('description'); ?></span>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-weight: 600; font-size: 12px; color: #334155;">Statut</label>
                            <select class="form-control" id="edit_statut" name="statut" style="height: 38px; font-size: 13px;">
                                <option value="en_attente">En attente</option>
                                <option value="en_cours">En cours</option>
                                <option value="termine">Terminé</option>
                                <option value="rejete">Rejeté</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ===== FOOTER ===== -->
                <div class="modal-footer" style="padding: 14px 24px 20px; border-top: 1px solid #eef2f6; background: #ffffff; border-radius: 0 0 8px 8px; display: flex; gap: 8px; flex-wrap: wrap;">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px; padding: 8px 20px; font-weight: 500; font-size: 13px; background: #f1f5f9; color: #475569; border: none; transition: all 0.3s ease; display: flex; align-items: center; gap: 6px;"
                            onmouseover="this.style.background='#e2e8f0';"
                            onmouseout="this.style.background='#f1f5f9';">
                        <i class="fa fa-times"></i> Annuler
                    </button>
                    <button type="reset" class="btn btn-warning" style="border-radius: 6px; padding: 8px 20px; font-weight: 500; font-size: 13px; background: #fef3c7; color: #92400e; border: none; transition: all 0.3s ease; display: flex; align-items: center; gap: 6px;"
                            onmouseover="this.style.background='#fde68a';"
                            onmouseout="this.style.background='#fef3c7';">
                        <i class="fa fa-refresh"></i> Réinitialiser
                    </button>
                    <button type="submit" class="btn btn-success" id="formSubmitBtn" style="border-radius: 6px; padding: 8px 24px; font-weight: 500; font-size: 13px; background: linear-gradient(135deg, #273772, #1a2558); color: #ffffff; border: none; box-shadow: 0 4px 12px rgba(39, 55, 114, 0.3); transition: all 0.3s ease; display: flex; align-items: center; gap: 6px; margin-left: auto;"
                            onmouseover="this.style.boxShadow='0 6px 20px rgba(39,55,114,0.4)'; this.style.transform='translateY(-2px)';"
                            onmouseout="this.style.boxShadow='0 4px 12px rgba(39,55,114,0.3)'; this.style.transform='translateY(0)';">
                        <i class="fa fa-save"></i> <span id="formSubmitText">Soumettre</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== STYLES SUPPLÉMENTAIRES ===== -->
<style>
    /* Animation du modal */
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

    /* Style du bouton de fermeture */
    .modal-chic .close {
        transition: all 0.3s ease;
        line-height: 1;
    }

    .modal-chic .close:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.3);
    }

    /* Ligne décorative sous le header */
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

    /* Effet de focus sur les champs */
    .modal-chic .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        outline: none;
    }

    /* Style des boutons au survol */
    .modal-chic .modal-footer .btn {
        transition: all 0.3s ease;
    }

    .modal-chic .modal-footer .btn:active {
        transform: scale(0.97);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .modal-chic .modal-header {
            padding: 14px 16px;
            flex-wrap: wrap;
        }

        .modal-chic .modal-header .close {
            width: 36px;
            height: 36px;
            font-size: 26px;
        }

        .modal-chic .modal-body {
            padding: 16px;
        }

        .modal-chic .modal-footer {
            flex-direction: column;
            padding: 12px 16px 16px;
        }

        .modal-chic .modal-footer .btn {
            width: 100%;
            justify-content: center;
            margin-left: 0 !important;
        }
    }
</style>

<!-- ========================================== -->
<!-- MODAL DÉTAILS                              -->
<!-- ========================================== -->
<div id="demandeDetailsModal" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 800px;">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; border-radius: 8px 8px 0 0;">
                <h4 style="margin: 0; color: white;">
                    <i class="fa fa-info-circle" style="color: #60a5fa;"></i> Détails de la demande
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    // ========================================== //
    // DOCUMENT READY                             //
    // ========================================== //
    $(document).ready(function() {
        // Focus effect pour les champs du modal
        $('.modal-chic .form-control').on('focus', function() {
            $(this).closest('.form-group').addClass('focused');
        }).on('blur', function() {
            $(this).closest('.form-group').removeClass('focused');
        });

        // Reset du formulaire à la fermeture du modal
        $('#demandeFormModal').on('hidden.bs.modal', function() {
            $('#demandeForm')[0].reset();
            $('#edit_id').val('');
            $('.text-danger').html('');
            $('.form-group').removeClass('has-error');
            $('.form-control').css('border-color', '');
        });

        // Intercepter la soumission du formulaire
        $('#demandeForm').on('submit', function(e) {
            e.preventDefault();
            var editId = $('#edit_id').val();
            if (editId && editId !== '') {
                submitEditForm($(this));
            } else {
                submitAddForm($(this));
            }
        });

        // Appliquer les filtres automatiquement
        $('#filterStatus, #filterCategory, #filterPriority').on('change', function() {
            applyFilters();
        });
    });

    // ========================================== //
    // OUVERTURE MODAL - AJOUT                    //
    // ========================================== //
    function openAddModal() {
        $('#formModalIcon').removeClass('fa-pencil-square-o').addClass('fa-plus-circle');
        $('#formTitleText').text('Nouvelle demande');
        $('#formModalSubtitle').text('Remplissez les informations ci-dessous');
        $('#formSubmitText').text('Soumettre');
        $('#formSubmitBtn').removeClass('btn-warning').addClass('btn-success');
        $('#demandeForm').attr('action', '<?php echo site_url('admin/demande/add_ajax'); ?>');
        $('#demandeForm')[0].reset();
        $('#edit_id').val('');
        $('#edit_statut').val('en_attente');
        $('.text-danger').html('');
        $('.form-group').removeClass('has-error');
        $('.form-control').css('border-color', '');
        $('#demandeFormModal').modal('show');
    }

    // ========================================== //
    // OUVERTURE MODAL - MODIFICATION (AJAX)      //
    // ========================================== //
    function openEditModal(id) {
        showSpinner();

        Swal.fire({
            title: 'Chargement...',
            text: 'Récupération des données de la demande',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        var url = '<?php echo base_url(); ?>admin/demande/get_demande_data/' + id;

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                hideSpinner();
                Swal.close();

                if (response.success) {
                    var data = response.demande;
                    fillEditForm(data);
                    $('#demandeFormModal').modal('show');
                } else {
                    showError(response.message || 'Impossible de charger les données de la demande');
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
        $('#formTitleText').text('Modifier la demande');
        $('#formModalSubtitle').text('Mettez à jour les informations de la demande');
        $('#formSubmitText').text('Mettre à jour');
        $('#formSubmitBtn').removeClass('btn-success').addClass('btn-warning');
        $('#demandeForm').attr('action', '<?php echo site_url('admin/demande/update_ajax'); ?>');

        $('#edit_id').val(data.id);
        $('#edit_titre').val(data.titre || '');
        $('#edit_categorie').val(data.categorie || '');
        $('#edit_priorite').val(data.priorite || '');
        $('#edit_description').val(data.description || '');
        $('#edit_statut').val(data.statut || 'en_attente');

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
            url: '<?php echo site_url('admin/demande/add_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner();

                if (response.success) {
                    $('#demandeFormModal').modal('hide');
                    showSuccess(response.message || 'Demande soumise avec succès');

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
            url: '<?php echo site_url('admin/demande/update_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                hideSpinner();

                if (response.success) {
                    $('#demandeFormModal').modal('hide');
                    showSuccess(response.message || 'Demande mise à jour avec succès');

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

        var titre = $('#edit_titre').val().trim();
        if (titre === '') {
            $('#edit_titre').css('border-color', '#ef4444');
            $('#edit_titre').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez renseigner le titre');
            return false;
        }

        var categorie = $('#edit_categorie').val();
        if (categorie === '') {
            $('#edit_categorie').css('border-color', '#ef4444');
            $('#edit_categorie').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez sélectionner une catégorie');
            return false;
        }

        var priorite = $('#edit_priorite').val();
        if (priorite === '') {
            $('#edit_priorite').css('border-color', '#ef4444');
            $('#edit_priorite').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez sélectionner une priorité');
            return false;
        }

        var description = $('#edit_description').val().trim();
        if (description === '') {
            $('#edit_description').css('border-color', '#ef4444');
            $('#edit_description').closest('.form-group').addClass('has-error');
            isValid = false;
            showError('Veuillez renseigner la description');
            return false;
        }

        return isValid;
    }

    // ========================================== //
    // FILTRES                                    //
    // ========================================== //
    function applyFilters() {
        const status = document.getElementById('filterStatus').value;
        const category = document.getElementById('filterCategory').value;
        const priority = document.getElementById('filterPriority').value;

        const rows = document.querySelectorAll('#demandeTableBody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            if (row.cells.length === 1 && row.cells[0].colSpan === 7) return;

            const rowStatus = row.dataset.status || '';
            const rowCategory = row.dataset.category || '';
            const rowPriority = row.dataset.priority || '';

            let show = true;

            if (status && rowStatus !== status) show = false;
            if (category && rowCategory !== category) show = false;
            if (priority && rowPriority !== priority) show = false;

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        let noResult = document.getElementById('noResultMessage');
        if (!noResult) {
            noResult = document.createElement('tr');
            noResult.id = 'noResultMessage';
            noResult.innerHTML = '<td colspan="7" class="text-center text-muted" style="padding:40px 0;">' +
                '<i class="fa fa-tasks" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>' +
                '<p style="font-size: 16px; color: #64748b;">Aucune demande ne correspond aux filtres</p>' +
                '<p style="font-size: 13px; color: #94a3b8;">Essayez de modifier vos critères de recherche</p>' +
                '</td>';
            document.querySelector('#demandeTableBody').appendChild(noResult);
        }
        noResult.style.display = visibleCount === 0 ? '' : 'none';
    }

    function resetFilters() {
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterCategory').value = '';
        document.getElementById('filterPriority').value = '';
        applyFilters();
    }

    // ========================================== //
    // SUPPRESSION                                //
    // ========================================== //
    function confirmDelete(event, id, titre) {
        event.preventDefault();

        Swal.fire({
            title: 'Confirmation de suppression',
            text: 'Supprimer définitivement la demande "' + titre + '" ?',
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
                window.location.href = '<?php echo base_url("admin/demande/delete/"); ?>' + id;
            }
        });
    }

    // ========================================== //
    // VOIR DÉTAILS                               //
    // ========================================== //
    function viewDemande(id) {
        showSpinner();

        $.ajax({
            url: '<?php echo base_url(); ?>admin/demande/details/' + id,
            success: function(result) {
                hideSpinner();
                $('#detailsContent').html(result);
                $('#demandeDetailsModal').modal('show');
            },
            error: function() {
                hideSpinner();
                showError('Impossible de charger les détails de la demande');
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