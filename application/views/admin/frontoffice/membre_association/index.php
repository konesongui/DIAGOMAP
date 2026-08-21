<!-- ============================================================
     PAGE : Gestion des adhérents (vue unique avec modals)
     DESCRIPTION : Liste + modals pour ajout, modification, détails
     ROUTE : admin/frontoffice/membre_association
     ============================================================ -->

<style>
    /* ===== STYLES GÉNÉRAUX ===== */
    .content-wrapper {
        background: #f0f4f9;
        padding-bottom: 40px;
        min-height: 100vh;
    }

    .card-modern {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.08);
        background: #ffffff;
        margin: 20px 0 30px;
        overflow: hidden;
    }

    .card-modern .card-header {
        background: linear-gradient(135deg, #1a472a 0%, #2d6a4f 100%);
        padding: 20px 32px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }

    .card-modern .card-header .header-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .card-modern .card-header .header-left .brand-icon {
        width: 44px;
        height: 44px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #fff;
        backdrop-filter: blur(4px);
    }

    .card-modern .card-header h2 {
        color: #ffffff;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.3px;
    }

    .card-modern .card-header h2 small {
        font-weight: 400;
        font-size: 14px;
        opacity: 0.7;
        margin-left: 8px;
        letter-spacing: 0;
    }

    .card-modern .card-body {
        padding: 28px;
        background: #fafcff;
    }

    /* ===== STATS ===== */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: white;
        padding: 18px 20px;
        border-radius: 14px;
        border: 1px solid #e8f0fe;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
    }

    .stat-card .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .stat-card .stat-icon.green { background: #e8f5e9; color: #1a472a; }
    .stat-card .stat-icon.blue { background: #e3f2fd; color: #1565c0; }
    .stat-card .stat-icon.orange { background: #fff3e0; color: #e65100; }
    .stat-card .stat-icon.purple { background: #f3e5f5; color: #6a1b9a; }

    .stat-card .stat-info h4 {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
        color: #1e293b;
    }

    .stat-card .stat-info span {
        font-size: 13px;
        color: #94a3b8;
    }

    /* ===== BARRE D'OUTILS ===== */
    .toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding: 16px 20px;
        background: white;
        border-radius: 14px;
        border: 1px solid #e8f0fe;
    }

    .toolbar .search-wrapper {
        flex: 1;
        min-width: 200px;
        position: relative;
    }

    .toolbar .search-wrapper input {
        width: 100%;
        padding: 10px 16px 10px 42px;
        border: 1.5px solid #e2e8f0;
        border-radius: 30px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #f8fafc;
    }

    .toolbar .search-wrapper input:focus {
        border-color: #1a472a;
        box-shadow: 0 0 0 4px rgba(26, 71, 42, 0.08);
        outline: none;
        background: white;
    }

    .toolbar .search-wrapper .search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .toolbar .filter-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .toolbar .filter-group select {
        padding: 9px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 30px;
        font-size: 13px;
        background: #f8fafc;
        color: #1e293b;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 140px;
    }

    .toolbar .filter-group select:focus {
        border-color: #1a472a;
        outline: none;
        background: white;
    }

    .btn-add-member {
        background: #1a472a;
        color: white;
        border: none;
        border-radius: 30px;
        padding: 10px 24px;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .btn-add-member:hover {
        background: #0d2818;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(26, 71, 42, 0.25);
    }

    .btn-add-member i {
        font-size: 16px;
    }

    .btn-export {
        background: #f1f5f9;
        color: #1e293b;
        border: 1px solid #e2e8f0;
        border-radius: 30px;
        padding: 10px 20px;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-export:hover {
        background: #e2e8f0;
        color: #1e293b;
        text-decoration: none;
    }

    /* ===== TABLE ===== */
    .table-container {
        overflow-x: auto;
        background: white;
        border-radius: 14px;
        border: 1px solid #e8f0fe;
    }

    .table-container table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .table-container table thead {
        background: #f8fafc;
        border-bottom: 2px solid #e8f0fe;
    }

    .table-container table thead th {
        padding: 14px 16px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-container table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .table-container table tbody tr:hover {
        background: #f8fafc;
    }

    .table-container table tbody tr:last-child td {
        border-bottom: none;
    }

    .membre-photo {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        background: #e8f0fe;
        border: 2px solid white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .membre-photo-placeholder {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e8f0fe;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 16px;
    }

    .badge-status {
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }

    .badge-status.active {
        background: #dcfce7;
        color: #166534;
    }

    .badge-status.inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-type {
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        background: #e8f0fe;
        color: #475569;
    }

    .actions-cell {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .actions-cell .btn-action {
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-size: 14px;
        cursor: pointer;
    }

    .actions-cell .btn-action.view {
        background: #e8f0fe;
        color: #1565c0;
    }

    .actions-cell .btn-action.view:hover {
        background: #1565c0;
        color: white;
    }

    .actions-cell .btn-action.edit {
        background: #fff3e0;
        color: #e65100;
    }

    .actions-cell .btn-action.edit:hover {
        background: #e65100;
        color: white;
    }

    .actions-cell .btn-action.delete {
        background: #fee2e2;
        color: #991b1b;
    }

    .actions-cell .btn-action.delete:hover {
        background: #991b1b;
        color: white;
    }

    .actions-cell .btn-action.toggle {
        background: #f3e8ff;
        color: #6a1b9a;
    }

    .actions-cell .btn-action.toggle:hover {
        background: #6a1b9a;
        color: white;
    }

    /* ===== PAGINATION ===== */
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-top: 20px;
    }

    .pagination-wrapper .info-text {
        color: #94a3b8;
        font-size: 14px;
    }

    /* ===== MODALS ===== */
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 25px 60px rgba(0,0,0,0.15);
    }

    .modal-header {
        border-bottom: 1px solid #e8f0fe;
        padding: 20px 28px;
        background: linear-gradient(135deg, #1a472a 0%, #2d6a4f 100%);
        border-radius: 16px 16px 0 0;
    }

    .modal-header .modal-title {
        color: white;
        font-weight: 600;
        font-size: 18px;
    }

    .modal-header .close {
        color: white;
        opacity: 0.7;
        text-shadow: none;
    }

    .modal-header .close:hover {
        opacity: 1;
    }

    .modal-body {
        padding: 28px;
        max-height: 70vh;
        overflow-y: auto;
    }

    .modal-footer {
        border-top: 1px solid #e8f0fe;
        padding: 16px 28px;
        background: #f8fafc;
        border-radius: 0 0 16px 16px;
    }

    /* ===== FORMULAIRE ===== */
    .form-section-title {
        font-size: 14px;
        font-weight: 600;
        color: #1a472a;
        margin: 20px 0 14px;
        padding-bottom: 6px;
        border-bottom: 2px solid #e8f5e9;
    }

    .form-section-title i {
        margin-right: 8px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        font-weight: 500;
        font-size: 13px;
        color: #1e293b;
        margin-bottom: 4px;
        display: block;
    }

    .form-group label .required {
        color: #ef4444;
        margin-left: 2px;
    }

    .form-group .form-control {
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        padding: 9px 14px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-group .form-control:focus {
        border-color: #1a472a;
        box-shadow: 0 0 0 4px rgba(26, 71, 42, 0.08);
    }

    .form-group .form-control.is-invalid {
        border-color: #ef4444;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.08);
    }

    .form-group .invalid-feedback {
        color: #ef4444;
        font-size: 12px;
        margin-top: 4px;
        display: none;
    }

    .form-group .invalid-feedback.show {
        display: block;
    }

    .form-group select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 40px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    /* ===== DÉTAIL MEMBRE ===== */
    .detail-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e8f0fe;
        margin: 0 auto 16px;
        display: block;
    }

    .detail-avatar-placeholder {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: #e8f0fe;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 40px;
        color: #94a3b8;
    }

    .detail-name {
        text-align: center;
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .detail-matricule {
        text-align: center;
        color: #94a3b8;
        font-size: 13px;
        margin-bottom: 16px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-top: 16px;
    }

    .detail-item {
        background: #f8fafc;
        padding: 12px 16px;
        border-radius: 10px;
    }

    .detail-item .label {
        font-size: 11px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .detail-item .value {
        font-size: 14px;
        color: #1e293b;
        font-weight: 500;
        margin-top: 2px;
        word-break: break-word;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .card-modern .card-header {
            padding: 16px 20px;
            flex-direction: column;
            align-items: stretch;
        }

        .card-modern .card-header .header-left {
            justify-content: center;
        }

        .card-modern .card-body {
            padding: 16px;
        }

        .toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .toolbar .filter-group {
            flex-direction: column;
        }

        .toolbar .filter-group select {
            width: 100%;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }

        .stats-row {
            grid-template-columns: 1fr 1fr;
        }

        .actions-cell .btn-action {
            width: 30px;
            height: 30px;
            font-size: 12px;
        }

        .modal-body {
            padding: 16px;
        }
    }

    @media (max-width: 576px) {
        .stats-row {
            grid-template-columns: 1fr;
        }

        .table-container table {
            font-size: 12px;
        }

        .table-container table thead th,
        .table-container table tbody td {
            padding: 10px 12px;
        }

        .btn-add-member {
            width: 100%;
            justify-content: center;
        }

        .btn-export {
            width: 100%;
            justify-content: center;
        }
    }

    /* ===== TOAST ===== */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 400px;
        width: 100%;
    }

    .toast {
        padding: 14px 20px;
        border-radius: 12px;
        color: white;
        font-weight: 500;
        font-size: 14px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        animation: slideInRight 0.4s ease;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .toast.success { background: #1a472a; }
    .toast.error { background: #dc2626; }
    .toast.info { background: #2563eb; }

    .toast i { font-size: 20px; }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(40px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Carte principale -->
        <div class="card-modern">
            <!-- En-tête -->
            <div class="card-header">
                <div class="header-left">
                    <div class="brand-icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <h2>
                        Gestion des adhérents
                        <small>Association</small>
                    </h2>
                </div>
                <div>
                    <a href="<?php echo base_url('admin/frontoffice/membre_association/export_csv'); ?>" class="btn-export">
                        <i class="fa fa-file-excel-o"></i> Exporter CSV
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Statistiques -->
                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="fa fa-users"></i></div>
                        <div class="stat-info">
                            <h4><?php echo $stats['total'] ?? 0; ?></h4>
                            <span>Total adhérents</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="fa fa-check-circle"></i></div>
                        <div class="stat-info">
                            <h4><?php echo $stats['actifs'] ?? 0; ?></h4>
                            <span>Actifs</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange"><i class="fa fa-times-circle"></i></div>
                        <div class="stat-info">
                            <h4><?php echo $stats['inactifs'] ?? 0; ?></h4>
                            <span>Inactifs</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple"><i class="fa fa-calendar-plus-o"></i></div>
                        <div class="stat-info">
                            <h4><?php echo $stats['nouveaux_mois'] ?? 0; ?></h4>
                            <span>Nouveaux ce mois</span>
                        </div>
                    </div>
                </div>

                <!-- Barre d'outils -->
                <div class="toolbar">
                    <div class="search-wrapper">
                        <i class="fa fa-search search-icon"></i>
                        <input type="text" id="searchInput" placeholder="Rechercher un adhérent..." value="<?php echo $this->input->get('search'); ?>">
                    </div>
                    <div class="filter-group">
                        <select id="filterType">
                            <option value="">Tous les types</option>
                            <option value="actif">Actif</option>
                            <option value="bienfaiteur">Bienfaiteur</option>
                            <option value="honoraire">Honoraire</option>
                            <option value="ancien">Ancien</option>
                            <option value="en_attente">En attente</option>
                        </select>
                        <select id="filterStatut">
                            <option value="">Tous les statuts</option>
                            <option value="1">Actif</option>
                            <option value="0">Inactif</option>
                        </select>
                        <select id="filterCategorie">
                            <option value="">Toutes les catégories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat->id; ?>"><?php echo $cat->nom; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn-add-member" data-toggle="modal" data-target="#addModal">
                        <i class="fa fa-plus"></i> Ajouter
                    </button>
                </div>

                <!-- Tableau -->
                <div class="table-container">
                    <table>
                        <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Matricule</th>
                            <th>Nom & Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                        </thead>
                        <tbody id="membresTableBody">
                        <?php if (!empty($membres)): ?>
                            <?php foreach ($membres as $m): ?>
                                <tr data-id="<?php echo $m->id; ?>">
                                    <td>
                                        <?php if ($m->photo && file_exists('./' . $m->photo)): ?>
                                            <img src="<?php echo base_url($m->photo); ?>" alt="Photo" class="membre-photo">
                                        <?php else: ?>
                                            <div class="membre-photo-placeholder">
                                                <i class="fa fa-user"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo $m->matricule; ?></strong></td>
                                    <td>
                                        <strong><?php echo $m->prenom . ' ' . $m->nom; ?></strong>
                                        <?php if ($m->categorie_nom): ?>
                                            <br><small style="color: #94a3b8; font-size: 11px;">
                                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:<?php echo $m->categorie_couleur ?? '#1a472a'; ?>;margin-right:4px;"></span>
                                                <?php echo $m->categorie_nom; ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $m->email ?: '-'; ?></td>
                                    <td><?php echo $m->telephone ?: '-'; ?></td>
                                    <td><span class="badge-type"><?php echo ucfirst($m->type_membre); ?></span></td>
                                    <td>
                                            <span class="badge-status <?php echo $m->statut == 1 ? 'active' : 'inactive'; ?>">
                                                <?php echo $m->statut == 1 ? 'Actif' : 'Inactif'; ?>
                                            </span>
                                    </td>
                                    <td>
                                        <div class="actions-cell" style="justify-content: center;">
                                            <button class="btn-action view" onclick="viewMembre(<?php echo $m->id; ?>)" title="Voir">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <button class="btn-action edit" onclick="editMembre(<?php echo $m->id; ?>)" title="Modifier">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <button class="btn-action toggle" onclick="toggleStatus(<?php echo $m->id; ?>)" title="<?php echo $m->statut == 1 ? 'Désactiver' : 'Activer'; ?>">
                                                <i class="fa <?php echo $m->statut == 1 ? 'fa-pause' : 'fa-play'; ?>"></i>
                                            </button>
                                            <button class="btn-action delete" onclick="deleteMembre(<?php echo $m->id; ?>)" title="Supprimer">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">
                                    <i class="fa fa-users" style="font-size: 40px; display: block; margin-bottom: 12px;"></i>
                                    Aucun adhérent trouvé.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    <div class="info-text">
                        Affichage de <?php echo count($membres); ?> adhérent(s)
                    </div>
                    <?php echo $pagination; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     MODAL : AJOUT
     ============================================================ -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-user-plus"></i> Ajouter un adhérent</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addForm" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Civilité</label>
                            <select name="civilite" class="form-control">
                                <option value="M">M</option>
                                <option value="Mme">Mme</option>
                                <option value="Mlle">Mlle</option>
                                <option value="Dr">Dr</option>
                                <option value="Pr">Pr</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Photo</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, GIF (max 2MB)</small>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="fa fa-id-card"></i> Identité</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom <span class="required">*</span></label>
                            <input type="text" name="nom" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label>Prénom <span class="required">*</span></label>
                            <input type="text" name="prenom" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Date de naissance</label>
                            <input type="date" name="date_naissance" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Lieu de naissance</label>
                            <input type="text" name="lieu_naissance" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nationalité</label>
                            <input type="text" name="nationalite" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Profession</label>
                            <input type="text" name="profession" class="form-control">
                        </div>
                    </div>

                    <div class="form-section-title"><i class="fa fa-address-card"></i> Coordonnées</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="text" name="telephone" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Téléphone 2</label>
                            <input type="text" name="telephone2" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Adresse</label>
                            <input type="text" name="adresse" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Code postal</label>
                            <input type="text" name="code_postal" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Ville</label>
                            <input type="text" name="ville" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Pays</label>
                        <input type="text" name="pays" class="form-control" value="Côte d'Ivoire">
                    </div>

                    <div class="form-section-title"><i class="fa fa-cog"></i> Informations associatives</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Type de membre</label>
                            <select name="type_membre" class="form-control">
                                <option value="actif">Actif</option>
                                <option value="bienfaiteur">Bienfaiteur</option>
                                <option value="honoraire">Honoraire</option>
                                <option value="ancien">Ancien</option>
                                <option value="en_attente">En attente</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Catégorie</label>
                            <select name="categorie_id" class="form-control">
                                <option value="">Sélectionner...</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat->id; ?>"><?php echo $cat->nom; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Date d'adhésion</label>
                            <input type="date" name="date_adhesion" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Date d'expiration</label>
                            <input type="date" name="date_expiration" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Montant cotisation (FCFA)</label>
                            <input type="number" name="montant_cotisation" class="form-control" step="100">
                        </div>
                        <div class="form-group">
                            <label>Mode de paiement</label>
                            <select name="mode_paiement" class="form-control">
                                <option value="especes">Espèces</option>
                                <option value="cheque">Chèque</option>
                                <option value="virement">Virement</option>
                                <option value="cb">CB</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Statut</label>
                        <select name="statut" class="form-control">
                            <option value="1">Actif</option>
                            <option value="0">Inactif</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Commentaire</label>
                        <textarea name="commentaire" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success" id="saveMemberBtn" style="background:#1a472a;border-color:#1a472a;">
                    <i class="fa fa-save"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     MODAL : MODIFICATION
     ============================================================ -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-pencil"></i> Modifier l'adhérent</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="editId">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Civilité</label>
                            <select name="civilite" id="editCivilite" class="form-control">
                                <option value="M">M</option>
                                <option value="Mme">Mme</option>
                                <option value="Mlle">Mlle</option>
                                <option value="Dr">Dr</option>
                                <option value="Pr">Pr</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Photo</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            <small class="text-muted">Laisser vide pour conserver l'actuelle</small>
                            <div id="editCurrentPhoto"></div>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="fa fa-id-card"></i> Identité</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom <span class="required">*</span></label>
                            <input type="text" name="nom" id="editNom" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label>Prénom <span class="required">*</span></label>
                            <input type="text" name="prenom" id="editPrenom" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Date de naissance</label>
                            <input type="date" name="date_naissance" id="editDateNaissance" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Lieu de naissance</label>
                            <input type="text" name="lieu_naissance" id="editLieuNaissance" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nationalité</label>
                            <input type="text" name="nationalite" id="editNationalite" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Profession</label>
                            <input type="text" name="profession" id="editProfession" class="form-control">
                        </div>
                    </div>

                    <div class="form-section-title"><i class="fa fa-address-card"></i> Coordonnées</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="editEmail" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="text" name="telephone" id="editTelephone" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Téléphone 2</label>
                            <input type="text" name="telephone2" id="editTelephone2" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Adresse</label>
                            <input type="text" name="adresse" id="editAdresse" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Code postal</label>
                            <input type="text" name="code_postal" id="editCodePostal" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Ville</label>
                            <input type="text" name="ville" id="editVille" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Pays</label>
                        <input type="text" name="pays" id="editPays" class="form-control">
                    </div>

                    <div class="form-section-title"><i class="fa fa-cog"></i> Informations associatives</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Type de membre</label>
                            <select name="type_membre" id="editTypeMembre" class="form-control">
                                <option value="actif">Actif</option>
                                <option value="bienfaiteur">Bienfaiteur</option>
                                <option value="honoraire">Honoraire</option>
                                <option value="ancien">Ancien</option>
                                <option value="en_attente">En attente</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Catégorie</label>
                            <select name="categorie_id" id="editCategorieId" class="form-control">
                                <option value="">Sélectionner...</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat->id; ?>"><?php echo $cat->nom; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Date d'adhésion</label>
                            <input type="date" name="date_adhesion" id="editDateAdhesion" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Date d'expiration</label>
                            <input type="date" name="date_expiration" id="editDateExpiration" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Montant cotisation (FCFA)</label>
                            <input type="number" name="montant_cotisation" id="editMontantCotisation" class="form-control" step="100">
                        </div>
                        <div class="form-group">
                            <label>Mode de paiement</label>
                            <select name="mode_paiement" id="editModePaiement" class="form-control">
                                <option value="especes">Espèces</option>
                                <option value="cheque">Chèque</option>
                                <option value="virement">Virement</option>
                                <option value="cb">CB</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Statut</label>
                        <select name="statut" id="editStatut" class="form-control">
                            <option value="1">Actif</option>
                            <option value="0">Inactif</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Commentaire</label>
                        <textarea name="commentaire" id="editCommentaire" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="updateMemberBtn" style="background:#1a472a;border-color:#1a472a;">
                    <i class="fa fa-save"></i> Mettre à jour
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     MODAL : DÉTAIL
     ============================================================ -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-id-card"></i> Détails de l'adhérent</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <div style="text-align: center;">
                    <div id="viewPhoto"></div>
                    <div class="detail-name" id="viewNomPrenom"></div>
                    <div class="detail-matricule" id="viewMatricule"></div>
                </div>
                <div class="detail-grid" id="viewDetailsGrid"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     TOASTS (notifications)
     ============================================================ -->
<div class="toast-container" id="toastContainer"></div>

<!-- ============================================================
     SCRIPT
     ============================================================ -->
<script>
    // ===== FILTRES =====
    function applyFilters() {
        const search = document.getElementById('searchInput').value;
        const type = document.getElementById('filterType').value;
        const statut = document.getElementById('filterStatut').value;
        const categorie = document.getElementById('filterCategorie').value;

        let url = '<?php echo base_url("admin/frontoffice/membre_association"); ?>?';
        if (search) url += 'search=' + encodeURIComponent(search) + '&';
        if (type) url += 'type_membre=' + encodeURIComponent(type) + '&';
        if (statut !== '') url += 'statut=' + encodeURIComponent(statut) + '&';
        if (categorie) url += 'categorie_id=' + encodeURIComponent(categorie);

        window.location.href = url;
    }

    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') applyFilters();
    });

    document.getElementById('filterType').addEventListener('change', applyFilters);
    document.getElementById('filterStatut').addEventListener('change', applyFilters);
    document.getElementById('filterCategorie').addEventListener('change', applyFilters);

    // ===== TOAST =====
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = 'toast ' + type;
        const icon = type === 'success' ? 'fa-check-circle' :
            type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
        toast.innerHTML = '<i class="fa ' + icon + '"></i> ' + message;
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(40px)';
            setTimeout(() => toast.remove(), 400);
        }, 4000);
    }

    // ===== AJOUT =====
    document.getElementById('saveMemberBtn').addEventListener('click', function() {
        const form = document.getElementById('addForm');
        const formData = new FormData(form);

        // Réinitialiser les erreurs
        document.querySelectorAll('#addForm .is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('#addForm .invalid-feedback').forEach(el => el.classList.remove('show'));

        fetch('<?php echo base_url("admin/frontoffice/membre_association/add"); ?>', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    $('#addModal').modal('hide');
                    form.reset();
                    // Recharger la page pour mettre à jour la liste
                    setTimeout(() => window.location.reload(), 800);
                } else if (data.status === 'error' && data.errors) {
                    // Afficher les erreurs de validation
                    for (const [field, message] of Object.entries(data.errors)) {
                        const input = form.querySelector('[name="' + field + '"]');
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.parentElement.querySelector('.invalid-feedback');
                            if (feedback) {
                                feedback.textContent = message;
                                feedback.classList.add('show');
                            }
                        }
                    }
                } else {
                    showToast(data.message || 'Une erreur est survenue.', 'error');
                }
            })
            .catch(error => {
                showToast('Erreur réseau.', 'error');
                console.error(error);
            });
    });

    // ===== VISUALISER =====
    function viewMembre(id) {
        fetch('<?php echo base_url("admin/frontoffice/membre_association/get_membre"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const m = data.data;
                    // Photo
                    const photoDiv = document.getElementById('viewPhoto');
                    if (m.photo && m.photo !== '') {
                        photoDiv.innerHTML = '<img src="<?php echo base_url(); ?>' + m.photo + '" class="detail-avatar">';
                    } else {
                        photoDiv.innerHTML = '<div class="detail-avatar-placeholder"><i class="fa fa-user"></i></div>';
                    }

                    document.getElementById('viewNomPrenom').textContent = m.civilite + ' ' + m.prenom + ' ' + m.nom;
                    document.getElementById('viewMatricule').textContent = 'Matricule: ' + m.matricule;

                    const grid = document.getElementById('viewDetailsGrid');
                    const fields = [
                        { label: 'Email', value: m.email || '-' },
                        { label: 'Téléphone', value: m.telephone || '-' },
                        { label: 'Téléphone 2', value: m.telephone2 || '-' },
                        { label: 'Date de naissance', value: m.date_naissance || '-' },
                        { label: 'Lieu de naissance', value: m.lieu_naissance || '-' },
                        { label: 'Nationalité', value: m.nationalite || '-' },
                        { label: 'Profession', value: m.profession || '-' },
                        { label: 'Adresse', value: m.adresse || '-' },
                        { label: 'Ville', value: m.ville || '-' },
                        { label: 'Pays', value: m.pays || '-' },
                        { label: 'Type de membre', value: m.type_membre || '-' },
                        { label: 'Catégorie', value: m.categorie_nom || '-' },
                        { label: 'Date d\'adhésion', value: m.date_adhesion || '-' },
                        { label: 'Date d\'expiration', value: m.date_expiration || '-' },
                        { label: 'Montant cotisation', value: m.montant_cotisation ? m.montant_cotisation + ' FCFA' : '-' },
                        { label: 'Mode de paiement', value: m.mode_paiement || '-' },
                        { label: 'Statut', value: m.statut == 1 ? 'Actif' : 'Inactif' },
                        { label: 'Commentaire', value: m.commentaire || '-' }
                    ];

                    grid.innerHTML = fields.map(f =>
                        '<div class="detail-item">' +
                        '<div class="label">' + f.label + '</div>' +
                        '<div class="value">' + f.value + '</div>' +
                        '</div>'
                    ).join('');

                    $('#viewModal').modal('show');
                } else {
                    showToast(data.message || 'Erreur lors du chargement.', 'error');
                }
            })
            .catch(error => {
                showToast('Erreur réseau.', 'error');
                console.error(error);
            });
    }

    // ===== MODIFIER =====
    function editMembre(id) {
        fetch('<?php echo base_url("admin/frontoffice/membre_association/get_membre"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const m = data.data;
                    document.getElementById('editId').value = m.id;
                    document.getElementById('editCivilite').value = m.civilite || 'M';
                    document.getElementById('editNom').value = m.nom || '';
                    document.getElementById('editPrenom').value = m.prenom || '';
                    document.getElementById('editDateNaissance').value = m.date_naissance || '';
                    document.getElementById('editLieuNaissance').value = m.lieu_naissance || '';
                    document.getElementById('editNationalite').value = m.nationalite || '';
                    document.getElementById('editProfession').value = m.profession || '';
                    document.getElementById('editEmail').value = m.email || '';
                    document.getElementById('editTelephone').value = m.telephone || '';
                    document.getElementById('editTelephone2').value = m.telephone2 || '';
                    document.getElementById('editAdresse').value = m.adresse || '';
                    document.getElementById('editCodePostal').value = m.code_postal || '';
                    document.getElementById('editVille').value = m.ville || '';
                    document.getElementById('editPays').value = m.pays || 'Côte d\'Ivoire';
                    document.getElementById('editTypeMembre').value = m.type_membre || 'actif';
                    document.getElementById('editCategorieId').value = m.categorie_id || '';
                    document.getElementById('editDateAdhesion').value = m.date_adhesion || '';
                    document.getElementById('editDateExpiration').value = m.date_expiration || '';
                    document.getElementById('editMontantCotisation').value = m.montant_cotisation || '';
                    document.getElementById('editModePaiement').value = m.mode_paiement || 'especes';
                    document.getElementById('editStatut').value = m.statut || 1;
                    document.getElementById('editCommentaire').value = m.commentaire || '';

                    // Photo actuelle
                    const photoDiv = document.getElementById('editCurrentPhoto');
                    if (m.photo && m.photo !== '') {
                        photoDiv.innerHTML = '<br><img src="<?php echo base_url(); ?>' + m.photo + '" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid #e8f0fe;">';
                    } else {
                        photoDiv.innerHTML = '';
                    }

                    // Réinitialiser les erreurs
                    document.querySelectorAll('#editForm .is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    document.querySelectorAll('#editForm .invalid-feedback').forEach(el => el.classList.remove('show'));

                    $('#editModal').modal('show');
                } else {
                    showToast(data.message || 'Erreur lors du chargement.', 'error');
                }
            })
            .catch(error => {
                showToast('Erreur réseau.', 'error');
                console.error(error);
            });
    }

    document.getElementById('updateMemberBtn').addEventListener('click', function() {
        const form = document.getElementById('editForm');
        const formData = new FormData(form);

        // Réinitialiser les erreurs
        document.querySelectorAll('#editForm .is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('#editForm .invalid-feedback').forEach(el => el.classList.remove('show'));

        fetch('<?php echo base_url("admin/frontoffice/membre_association/edit"); ?>', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    $('#editModal').modal('hide');
                    setTimeout(() => window.location.reload(), 800);
                } else if (data.status === 'error' && data.errors) {
                    for (const [field, message] of Object.entries(data.errors)) {
                        const input = form.querySelector('[name="' + field + '"]');
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.parentElement.querySelector('.invalid-feedback');
                            if (feedback) {
                                feedback.textContent = message;
                                feedback.classList.add('show');
                            }
                        }
                    }
                } else if (data.status === 'info') {
                    showToast(data.message, 'info');
                    $('#editModal').modal('hide');
                } else {
                    showToast(data.message || 'Une erreur est survenue.', 'error');
                }
            })
            .catch(error => {
                showToast('Erreur réseau.', 'error');
                console.error(error);
            });
    });

    // ===== SUPPRIMER =====
    function deleteMembre(id) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet adhérent ? Cette action est irréversible.')) return;

        fetch('<?php echo base_url("admin/frontoffice/membre_association/delete"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    // Supprimer la ligne du tableau
                    const row = document.querySelector('tr[data-id="' + id + '"]');
                    if (row) row.remove();
                } else {
                    showToast(data.message || 'Erreur lors de la suppression.', 'error');
                }
            })
            .catch(error => {
                showToast('Erreur réseau.', 'error');
                console.error(error);
            });
    }

    // ===== CHANGER STATUT =====
    function toggleStatus(id) {
        fetch('<?php echo base_url("admin/frontoffice/membre_association/toggle_status"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    showToast(data.message || 'Erreur lors du changement de statut.', 'error');
                }
            })
            .catch(error => {
                showToast('Erreur réseau.', 'error');
                console.error(error);
            });
    }

    // Initialisation des valeurs de filtres depuis l'URL
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('search')) {
            document.getElementById('searchInput').value = urlParams.get('search');
        }
        if (urlParams.get('type_membre')) {
            document.getElementById('filterType').value = urlParams.get('type_membre');
        }
        if (urlParams.get('statut') !== null && urlParams.get('statut') !== '') {
            document.getElementById('filterStatut').value = urlParams.get('statut');
        }
        if (urlParams.get('categorie_id')) {
            document.getElementById('filterCategorie').value = urlParams.get('categorie_id');
        }
    });

    console.log('🏛️ Gestion des adhérents - Vue unique avec modals');
    console.log('📊 ' + document.querySelectorAll('#membresTableBody tr').length + ' adhérents affichés');
</script>