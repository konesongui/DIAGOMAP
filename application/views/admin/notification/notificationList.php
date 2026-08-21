<!-- ============================================================
     PAGE : Panneau d'affichage / Notifications
     DESCRIPTION : Interface moderne pour la gestion des notifications
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
    /* NOTIFICATION CARDS                         */
    /* ========================================== */
    .notification-card {
        background: #ffffff;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-light);
        margin-bottom: 16px;
        transition: var(--transition);
        overflow: hidden;
    }

    .notification-card:hover {
        box-shadow: var(--shadow-hover);
        border-color: #dbeafe;
    }

    .notification-card .card-header {
        padding: 16px 20px;
        background: #f8fafc;
        border-bottom: 1px solid var(--border-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        cursor: pointer;
        transition: var(--transition);
    }

    .notification-card .card-header:hover {
        background: #f1f5f9;
    }

    .notification-card .card-header .title {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .notification-card .card-header .title .badge-status {
        font-size: 10px;
        padding: 2px 12px;
        border-radius: 12px;
        font-weight: 500;
    }
    .badge-status.publie { background: #d1fae5; color: #065f46; }
    .badge-status.brouillon { background: #fef3c7; color: #92400e; }
    .badge-status.archive { background: #e2e8f0; color: #475569; }

    .notification-card .card-header .actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .notification-card .card-header .actions a {
        color: #94a3b8;
        transition: var(--transition);
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 14px;
    }

    .notification-card .card-header .actions a:hover {
        color: var(--primary-dark);
        background: #e2e8f0;
    }

    .notification-card .card-header .actions a.text-danger:hover {
        color: #dc2626;
        background: #fef2f2;
    }

    .notification-card .card-body {
        padding: 20px;
    }

    .notification-card .card-body .content {
        color: #475569;
        line-height: 1.6;
        font-size: 14px;
    }

    .notification-card .card-body .content img {
        max-width: 100%;
        border-radius: 8px;
    }

    .notification-card .card-body .meta {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid var(--border-light);
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        font-size: 13px;
        color: #94a3b8;
    }

    .notification-card .card-body .meta .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .notification-card .card-body .meta .meta-item i {
        color: var(--primary-light);
        width: 16px;
    }

    .notification-card .card-body .recipients {
        margin-top: 12px;
        padding: 12px 16px;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid var(--border-light);
    }

    .notification-card .card-body .recipients .recipient-title {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .notification-card .card-body .recipients .recipient-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .notification-card .card-body .recipients .recipient-tags .tag {
        background: #e2e8f0;
        color: #475569;
        padding: 2px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }

    .notification-card .card-body .recipients .recipient-tags .tag.student {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .notification-card .card-body .recipients .recipient-tags .tag.parent {
        background: #d1fae5;
        color: #059669;
    }

    .notification-card .card-body .recipients .recipient-tags .tag.teacher {
        background: #fef3c7;
        color: #d97706;
    }

    .notification-card .card-body .recipients .recipient-tags .tag.admin {
        background: #f3e8ff;
        color: #7c3aed;
    }

    /* Chevron animation */
    .notification-card .card-header .chevron {
        transition: transform 0.3s ease;
    }

    .notification-card .card-header .chevron.rotated {
        transform: rotate(180deg);
    }

    /* Collapse animation */
    .notification-card .collapse-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease;
    }

    .notification-card .collapse-content.open {
        max-height: 2000px;
    }

    /* ========================================== */
    /* EMPTY STATE                                */
    /* ========================================== */
    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }

    .empty-state i {
        font-size: 64px;
        color: #cbd5e1;
        display: block;
        margin-bottom: 16px;
    }

    .empty-state h4 {
        font-size: 18px;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .empty-state p {
        font-size: 14px;
        color: var(--text-muted);
    }

    /* ========================================== */
    /* RESPONSIVE                                 */
    /* ========================================== */
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .card-modern .card-header { flex-direction: column; align-items: stretch; }
        .notification-card .card-header { flex-direction: column; align-items: stretch; }
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-bar .filter-group { width: 100%; }
        .filter-bar .filter-group select, .filter-bar .filter-group input { width: 100%; min-width: unset; }
        .export-group { margin-left: 0 !important; width: 100%; flex-wrap: wrap; }
        .export-group .btn-export { flex: 1; justify-content: center; padding: 6px 12px; font-size: 11px; }
        .export-group .export-label { width: 100%; text-align: center; }
        .export-divider { display: none; }
        .btn-add-modern { width: 100%; text-align: center; }
        .notification-card .card-body .meta { flex-direction: column; gap: 8px; }
    }

    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
        .export-group .btn-export { font-size: 10px; padding: 4px 10px; }
        .export-group .btn-export i { font-size: 12px; }
        .notification-card .card-header .title { font-size: 14px; }
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
                            <i class="fa fa-bullhorn"></i> Panneau d'affichage
                            <span style="font-size: 14px; font-weight: 400; color: #94a3b8; margin-left: 8px;">
                                (<?php echo isset($total_notifications) ? $total_notifications : (is_array($notificationlist) ? count($notificationlist) : 0); ?>)
                            </span>
                        </h3>
                        <div class="d-flex align-items-center gap-3" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url(); ?>admin/admin/hub" class="btn-back" title="Retour au tableau de bord">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <?php if ($this->rbac->hasPrivilege('panneau_notifications', 'can_add')) : ?>
                                <a href="<?php echo base_url(); ?>admin/notification/add" class="btn-add-modern">
                                    <i class="fa fa-plus-circle" style="margin-right: 6px;"></i> Nouvelle notification
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- STATISTIQUES                               -->
                    <!-- ========================================== -->
                    <div style="padding: 20px 24px 0;">
                        <div class="stats-grid">
                            <?php
                            $total = isset($notificationlist) ? count($notificationlist) : 0;
                            $publie = 0;
                            $brouillon = 0;
                            $archive = 0;
                            if (!empty($notificationlist)) {
                                foreach ($notificationlist as $n) {
                                    if (isset($n['status'])) {
                                        if ($n['status'] == 'publie') $publie++;
                                        elseif ($n['status'] == 'brouillon') $brouillon++;
                                        elseif ($n['status'] == 'archive') $archive++;
                                    }
                                }
                            }
                            ?>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-bullhorn"></i> Total</h4>
                                    <p class="number"><?php echo $total; ?></p>
                                </div>

                            </div>
                            <!--<div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-check-circle" style="color:#10b981;"></i> Publiées</h4>
                                    <p class="number"><?php echo $publie; ?></p>
                                </div>

                            </div>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-pencil-square-o" style="color:#f59e0b;"></i> Brouillons</h4>
                                    <p class="number"><?php echo $brouillon; ?></p>
                                </div>

                            </div>
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-archive" style="color:#6b7280;"></i> Archivées</h4>
                                    <p class="number"><?php echo $archive; ?></p>
                                </div>

                            </div>-->
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-calendar-day" style="color:#8b5cf6;"></i> Ce mois-ci</h4>
                                    <p class="number">
                                        <?php
                                        $this_month = 0;
                                        if (!empty($notificationlist)) {
                                            $month = date('Y-m');
                                            foreach ($notificationlist as $n) {
                                                if (isset($n['date']) && substr($n['date'], 0, 7) == $month) {
                                                    $this_month++;
                                                }
                                            }
                                        }
                                        echo $this_month;
                                        ?>
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

                        <!-- ========================================== -->
                        <!-- BARRE DE FILTRES                          -->
                        <!-- ========================================== -->
                        <div class="filter-bar">
                            <div class="filter-group">
                                <label><i class="fa fa-filter"></i> Statut :</label>
                                <select id="filterStatus" onchange="applyFilters()">
                                    <option value="">Tous</option>
                                    <option value="publie">Publié</option>
                                    <option value="brouillon">Brouillon</option>
                                    <option value="archive">Archivé</option>
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

                               <!-- <div class="export-group">
                                    <span class="export-label"><i class="fa fa-download"></i> Exporter</span>
                                    <div class="export-divider"></div>
                                    <button class="btn-export btn-excel" onclick="exportData('excel')">
                                        <i class="fa fa-file-excel-o"></i> CSV
                                    </button>
                                    <button class="btn-export btn-pdf" onclick="exportData('pdf')">
                                        <i class="fa fa-file-pdf-o"></i> PDF
                                    </button>
                                </div>-->
                            </div>
                        </div>

                        <!-- ========================================== -->
                        <!-- LISTE DES NOTIFICATIONS                    -->
                        <!-- ========================================== -->
                        <?php if (empty($notificationlist)) : ?>
                            <div class="empty-state">
                                <i class="fa fa-bullhorn"></i>
                                <h4>Aucune notification</h4>
                                <p>Commencez par créer une nouvelle notification</p>
                                <?php if ($this->rbac->hasPrivilege('panneau_notifications', 'can_add')) : ?>
                                    <a href="<?php echo base_url(); ?>admin/notification/add" class="btn-add-modern" style="display: inline-block; margin-top: 12px;">
                                        <i class="fa fa-plus-circle" style="margin-right: 6px;"></i> Nouvelle notification
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php else : ?>
                            <div id="notificationContainer">
                                <?php foreach ($notificationlist as $key => $notification) :
                                    $role_name = isset($notification["role_name"]) ? $notification["role_name"] : array();
                                    $user_id = $this->session->userdata('admin_id');
                                    $status = isset($notification['status']) ? $notification['status'] : 'publie';
                                    $statusLabels = [
                                        'publie' => 'Publié',
                                        'brouillon' => 'Brouillon',
                                        'archive' => 'Archivé'
                                    ];
                                    $statusBadge = [
                                        'publie' => 'publie',
                                        'brouillon' => 'brouillon',
                                        'archive' => 'archive'
                                    ];
                                    ?>
                                    <div class="notification-card"
                                         data-status="<?php echo $status; ?>"
                                         data-date="<?php echo isset($notification['date']) ? $notification['date'] : ''; ?>"
                                         data-id="<?php echo isset($notification['id']) ? $notification['id'] : ''; ?>">

                                        <!-- ===== HEADER ===== -->
                                        <div class="card-header" onclick="toggleNotification(this)">
                                            <div class="title">
                                                <i class="fa fa-bullhorn" style="color: #3b82f6;"></i>
                                                <?php echo isset($notification['title']) ? htmlspecialchars($notification['title']) : ''; ?>
                                                <span class="badge-status <?php echo isset($statusBadge[$status]) ? $statusBadge[$status] : 'publie'; ?>">
                                                    <?php echo isset($statusLabels[$status]) ? $statusLabels[$status] : $status; ?>
                                                </span>
                                            </div>
                                            <div class="actions">
                                                <?php if (($this->rbac->hasPrivilege('panneau_notifications', 'can_edit')) || (isset($notification["created_id"]) && $notification["created_id"] == $user_id)) : ?>
                                                    <a href="<?php echo base_url(); ?>admin/notification/edit/<?php echo isset($notification['id']) ? $notification['id'] : ''; ?>"
                                                       data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if (($this->rbac->hasPrivilege('panneau_notifications', 'can_delete')) || (isset($notification["created_id"]) && $notification["created_id"] == $user_id)) : ?>
                                                    <a href="<?php echo base_url(); ?>admin/notification/delete/<?php echo isset($notification['id']) ? $notification['id'] : ''; ?>"
                                                       data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"
                                                       onclick="return confirm('<?php echo $this->lang->line('delete_confirm'); ?>');"
                                                       class="text-danger">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <i class="fa fa-chevron-down chevron" style="color: #94a3b8;"></i>
                                            </div>
                                        </div>

                                        <!-- ===== BODY (collapse) ===== -->
                                        <div class="collapse-content">
                                            <div class="card-body">
                                                <div class="content">
                                                    <?php echo isset($notification['message']) ? $notification['message'] : ''; ?>
                                                </div>

                                                <!-- Meta informations -->
                                                <div class="meta">
                                                    <span class="meta-item">
                                                        <i class="fa fa-calendar-check-o"></i>
                                                        <?php echo $this->lang->line('publish_date'); ?> :
                                                        <?php echo isset($notification['publish_date']) ? date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($notification['publish_date'])) : ''; ?>
                                                    </span>
                                                    <span class="meta-item">
                                                        <i class="fa fa-calendar"></i>
                                                        <?php echo $this->lang->line('notice_date'); ?> :
                                                        <?php echo isset($notification['date']) ? date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($notification['date'])) : ''; ?>
                                                    </span>
                                                    <span class="meta-item">
                                                        <i class="fa fa-user"></i>
                                                        <?php echo $this->lang->line('created_by'); ?> :
                                                        <?php echo isset($notification["created_by"]) ? htmlspecialchars($notification["created_by"]) : ''; ?>
                                                    </span>
                                                </div>

                                                <!-- Destinataires -->
                                                <div class="recipients">
                                                    <div class="recipient-title">
                                                        <i class="fa fa-users"></i> Destinataires
                                                    </div>
                                                    <div class="recipient-tags">
                                                        <?php if (!empty($role_name)) : ?>
                                                            <?php foreach ($role_name as $role_value) : ?>
                                                                <span class="tag"><?php echo isset($role_value['name']) ? htmlspecialchars($role_value['name']) : ''; ?></span>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>

                                                        <?php if (isset($notification['visible_student']) && $notification['visible_student'] == "Yes") : ?>
                                                            <span class="tag student"><i class="fa fa-graduation-cap"></i> Étudiants</span>
                                                        <?php endif; ?>

                                                        <?php if (isset($notification['visible_parent']) && $notification['visible_parent'] == "Yes") : ?>
                                                            <span class="tag parent"><i class="fa fa-users"></i> Parents</span>
                                                        <?php endif; ?>

                                                        <?php if (isset($notification['visible_teacher']) && $notification['visible_teacher'] == "Yes") : ?>
                                                            <span class="tag teacher"><i class="fa fa-chalkboard-teacher"></i> Enseignants</span>
                                                        <?php endif; ?>

                                                        <?php if (empty($role_name) && (!isset($notification['visible_student']) || $notification['visible_student'] != "Yes") && (!isset($notification['visible_parent']) || $notification['visible_parent'] != "Yes")) : ?>
                                                            <span class="tag" style="color: #94a3b8;">Aucun destinataire</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- ========================================== -->
<!-- SCRIPTS                                    -->
<!-- ========================================== -->
<script type="text/javascript">
    // ========================================== //
    // TOGGLE NOTIFICATION                        //
    // ========================================== //
    function toggleNotification(element) {
        var collapseContent = element.closest('.notification-card').querySelector('.collapse-content');
        var chevron = element.querySelector('.chevron');

        if (collapseContent.classList.contains('open')) {
            collapseContent.classList.remove('open');
            chevron.classList.remove('rotated');
        } else {
            collapseContent.classList.add('open');
            chevron.classList.add('rotated');
        }
    }

    // ========================================== //
    // FILTRES                                    //
    // ========================================== //
    function applyFilters() {
        const status = document.getElementById('filterStatus').value;
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        const cards = document.querySelectorAll('.notification-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const cardStatus = card.dataset.status || '';
            const cardDate = card.dataset.date || '';

            let show = true;
            if (status && cardStatus !== status) show = false;
            if (dateFrom && cardDate < dateFrom) show = false;
            if (dateTo && cardDate > dateTo) show = false;

            card.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        // Afficher un message si aucun résultat
        let noResult = document.getElementById('noResultMessage');
        if (!noResult) {
            noResult = document.createElement('div');
            noResult.id = 'noResultMessage';
            noResult.className = 'empty-state';
            noResult.innerHTML = '<i class="fa fa-bullhorn"></i><h4>Aucune notification</h4><p>Aucune notification ne correspond aux filtres</p>';
            document.getElementById('notificationContainer').appendChild(noResult);
        }
        noResult.style.display = visibleCount === 0 ? '' : 'none';
    }

    function resetFilters() {
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterDateFrom').value = '';
        document.getElementById('filterDateTo').value = '';
        applyFilters();
    }

    // ========================================== //
    // EXPORTATION                                //
    // ========================================== //
    function exportData(type) {
        const status = document.getElementById('filterStatus').value;
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        const params = `?status=${encodeURIComponent(status)}&date_from=${encodeURIComponent(dateFrom)}&date_to=${encodeURIComponent(dateTo)}`;

        showSpinner();

        var url = '<?php echo base_url("admin/notification/export_"); ?>' + type + params;
        window.location.href = url;

        setTimeout(function() {
            hideSpinner();
        }, 3000);
    }

    // ========================================== //
    // SPINNER                                    //
    // ========================================== //
    function showSpinner() {
        // Créer le spinner s'il n'existe pas
        if (!document.getElementById('spinnerOverlay')) {
            var spinner = document.createElement('div');
            spinner.id = 'spinnerOverlay';
            spinner.className = 'spinner-overlay';
            spinner.innerHTML = '<div class="spinner-box"><i class="fa fa-spinner"></i><p style="margin-top:15px;font-weight:500;color:#1e293b;">Chargement en cours...</p></div>';
            document.body.appendChild(spinner);
        }
        document.getElementById('spinnerOverlay').classList.add('active');
    }

    function hideSpinner() {
        var spinner = document.getElementById('spinnerOverlay');
        if (spinner) {
            spinner.classList.remove('active');
        }
    }

    // ========================================== //
    // DOCUMENT READY                             //
    // ========================================== //
    $(document).ready(function() {
        // Appliquer les filtres automatiquement
        $('#filterStatus, #filterDateFrom, #filterDateTo').on('change', function() {
            applyFilters();
        });

        // Activer les tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Ouvrir la première notification par défaut
        var firstCard = document.querySelector('.notification-card');
        if (firstCard) {
            var header = firstCard.querySelector('.card-header');
            if (header) {
                setTimeout(function() {
                    toggleNotification(header);
                }, 300);
            }
        }
    });
</script>

<style>
    .spinner-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
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
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .spinner-box .fa-spinner {
        font-size: 40px;
        color: #273772;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>