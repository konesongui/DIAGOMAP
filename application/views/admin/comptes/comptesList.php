<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des entreprises</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* ========================================
           VARIABLES & RESET
        ======================================== */
        :root {
            --primary: #1a3c5e;
            --primary-light: #2c5a8c;
            --primary-dark: #0f2640;
            --primary-gradient: linear-gradient(135deg, #1a3c5e 0%, #2c5a8c 100%);
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --shadow: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.1);
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0f4f8;
            color: var(--gray-800);
            line-height: 1.6;
        }

        .content-wrapper {
            padding: 25px 30px;
            background: #f0f4f8;
            min-height: 100vh;
        }

        /* HEADER */
        .page-header {
            background: var(--primary-gradient);
            padding: 28px 35px;
            border-radius: var(--radius);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(26, 60, 94, 0.25);
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
            pointer-events: none;
        }

        .page-header .header-content {
            position: relative;
            z-index: 1;
        }

        .page-header h1 {
            color: #fff;
            font-size: 26px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .page-header h1 i {
            margin-right: 14px;
            opacity: 0.9;
        }

        .page-header .subtitle {
            color: rgba(255,255,255,0.75);
            font-size: 14px;
            margin-top: 4px;
        }

        .page-header .subtitle i {
            margin-right: 6px;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            position: relative;
            z-index: 1;
            flex-wrap: wrap;
        }

        .btn-header {
            background: rgba(255,255,255,0.12);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.15);
            padding: 9px 20px;
            border-radius: 8px;
            transition: var(--transition);
            font-weight: 500;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-header:hover {
            background: rgba(255,255,255,0.2);
            color: #fff;
            transform: translateY(-2px);
            text-decoration: none;
        }

        .btn-header-primary {
            background: #fff;
            color: var(--primary);
            border: none;
        }

        .btn-header-primary:hover {
            background: #f0f4ff;
            color: var(--primary-dark);
        }

        /* STATS CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fff;
            border-radius: var(--radius);
            padding: 22px 24px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
            border: 1px solid rgba(226, 232, 240, 0.6);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-card .stat-icon.blue {
            background: #dbeafe;
            color: #2563eb;
        }

        .stat-card .stat-icon.green {
            background: #d1fae5;
            color: #059669;
        }

        .stat-card .stat-icon.orange {
            background: #fef3c7;
            color: #d97706;
        }

        .stat-card .stat-icon.red {
            background: #fee2e2;
            color: #dc2626;
        }

        .stat-card .stat-number {
            font-size: 32px;
            font-weight: 800;
            color: var(--gray-900);
            letter-spacing: -1px;
            line-height: 1.2;
        }

        .stat-card .stat-label {
            font-size: 14px;
            color: var(--gray-500);
            font-weight: 500;
            margin-top: 2px;
        }

        .stat-card .stat-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 4px 14px;
            border-radius: 20px;
            background: var(--gray-100);
            color: var(--gray-600);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .stat-card .stat-badge.green {
            background: #d1fae5;
            color: #059669;
        }

        .stat-card .stat-badge.red {
            background: #fee2e2;
            color: #dc2626;
        }

        .stat-card .stat-badge.orange {
            background: #fef3c7;
            color: #d97706;
        }

        /* TABLE */
        .table-card {
            background: #fff;
            border-radius: var(--radius);
            padding: 0;
            box-shadow: var(--shadow);
            border: 1px solid rgba(226, 232, 240, 0.6);
            overflow: hidden;
        }

        .table-card .table-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            background: var(--gray-50);
        }

        .table-card .table-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--gray-800);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-card .table-title i {
            color: var(--primary);
        }

        .table-card .table-title .count-badge {
            background: var(--primary);
            color: #fff;
            font-size: 12px;
            padding: 2px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .table-tools {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .table-tools .search-box {
            position: relative;
        }

        .table-tools .search-box input {
            padding: 8px 16px 8px 38px;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            font-size: 13px;
            width: 240px;
            transition: var(--transition);
            background: #fff;
        }

        .table-tools .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26, 60, 94, 0.1);
        }

        .table-tools .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
        }

        .btn-add {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: var(--transition);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-add:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 60, 94, 0.3);
            color: #fff;
            text-decoration: none;
        }

        .table-responsive {
            padding: 0 25px 25px;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--gray-50);
            color: var(--gray-600);
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 16px;
            border-bottom: 2px solid var(--gray-200);
            white-space: nowrap;
        }

        .table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            border-bottom: 1px solid var(--gray-100);
        }

        .table tbody tr:hover {
            background: #f8faff;
        }

        .table .company-name {
            font-weight: 600;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .table .company-name i {
            color: var(--primary);
            opacity: 0.6;
        }

        .table .contact-info {
            font-size: 12px;
            line-height: 1.8;
        }

        .table .contact-info i {
            color: var(--gray-400);
            width: 18px;
        }

        /* BADGES */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-primary {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-secondary {
            background: var(--gray-100);
            color: var(--gray-600);
        }

        .badge .status-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .badge .status-dot.active { background: #10b981; }
        .badge .status-dot.inactive { background: #f59e0b; }
        .badge .status-dot.expired { background: #ef4444; }
        .badge .status-dot.suspended { background: #94a3b8; }

        .badge-forfait {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-forfait.starter {
            background: #e3f2fd;
            color: #1a3c5e;
            border: 1px solid #bbdefb;
        }

        .badge-forfait.pro {
            background: #ede7f6;
            color: #4a148c;
            border: 1px solid #d1c4e9;
        }

        .badge-forfait.premium {
            background: linear-gradient(135deg, #fef7e0, #fff3c4);
            color: #7c5c00;
            border: 1px solid #f0e3a0;
        }

        .expiry-alert {
            background: #fee2e2;
            color: #991b1b;
            padding: 3px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            animation: pulse-alert 2s ease-in-out infinite;
            display: inline-block;
        }

        @keyframes pulse-alert {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        /* ACTION BUTTONS */
        .action-group {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid var(--gray-200);
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            color: var(--gray-500);
            text-decoration: none;
            font-size: 13px;
            cursor: pointer;
        }

        .btn-action:hover {
            background: var(--gray-50);
            border-color: var(--gray-300);
            text-decoration: none;
            transform: translateY(-1px);
        }

        .btn-action.view:hover {
            background: #dbeafe;
            border-color: #93c5fd;
            color: #2563eb;
        }

        .btn-action.edit:hover {
            background: #d1fae5;
            border-color: #6ee7b7;
            color: #059669;
        }

        .btn-action.delete:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #dc2626;
        }

        .btn-action.suspend:hover {
            background: #fef3c7;
            border-color: #fcd34d;
            color: #d97706;
        }

        .btn-action.activate:hover {
            background: #d1fae5;
            border-color: #6ee7b7;
            color: #059669;
        }

        /* MODAL */
        .modal-content {
            border-radius: var(--radius);
            border: none;
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            background: var(--primary-gradient);
            color: #fff;
            border-radius: var(--radius) var(--radius) 0 0;
            padding: 18px 24px;
            border: none;
        }

        .modal-header .close {
            color: #fff;
            opacity: 0.8;
            text-shadow: none;
        }

        .modal-header .close:hover {
            opacity: 1;
        }

        .modal-title {
            font-weight: 600;
            font-size: 18px;
        }

        /* RESPONSIVE */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 15px;
            }

            .page-header {
                padding: 20px;
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .header-actions {
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .stat-card {
                padding: 16px;
            }

            .stat-card .stat-number {
                font-size: 24px;
            }

            .table-card .table-header {
                flex-direction: column;
                align-items: stretch;
                padding: 15px 20px;
            }

            .table-tools {
                flex-direction: column;
                width: 100%;
            }

            .table-tools .search-box {
                width: 100%;
            }

            .table-tools .search-box input {
                width: 100%;
            }

            .btn-add {
                width: 100%;
                justify-content: center;
            }

            .table-responsive {
                padding: 0 15px 15px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        @media print {
            .no-print {
                display: none !important;
            }
            
            .page-header {
                background: #1a3c5e !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>

<body>

<div class="content-wrapper">

    <?php
    $can_add_company = !empty($this->rbac) && method_exists($this->rbac, 'hasPrivilege') && $this->rbac->hasPrivilege('comptes', 'can_add');
    $csrfName = (!empty($this->security) && method_exists($this->security, 'get_csrf_token_name')) ? $this->security->get_csrf_token_name() : 'csrf';
    $csrfHash = (!empty($this->security) && method_exists($this->security, 'get_csrf_hash')) ? $this->security->get_csrf_hash() : '';

    $total_entreprises = 0;
    $entreprises_actives = 0;
    $abonnements_expires = 0;
    $abonnements_bientot_expire = 0;
    $entreprises = [];

    if (!empty($this->db) && method_exists($this->db, 'query')) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM compte_entreprise");
        if ($query !== false && $query->num_rows() > 0) {
            $row = $query->row_array();
            $total_entreprises = (int) ($row['total'] ?? 0);
        }

        $query = $this->db->query("SELECT COUNT(*) AS total FROM compte_entreprise WHERE statut = 'actif'");
        if ($query !== false && $query->num_rows() > 0) {
            $row = $query->row_array();
            $entreprises_actives = (int) ($row['total'] ?? 0);
        }

        $query = $this->db->query("SELECT COUNT(*) AS total FROM compte_entreprise WHERE date_expiration < CURDATE() AND statut != 'expire'");
        if ($query !== false && $query->num_rows() > 0) {
            $row = $query->row_array();
            $abonnements_expires = (int) ($row['total'] ?? 0);
        }

        $query = $this->db->query("SELECT COUNT(*) AS total FROM compte_entreprise WHERE date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND statut = 'actif'");
        if ($query !== false && $query->num_rows() > 0) {
            $row = $query->row_array();
            $abonnements_bientot_expire = (int) ($row['total'] ?? 0);
        }

        $query = $this->db->query("SELECT * FROM compte_entreprise ORDER BY date_debut DESC");
        if ($query !== false && $query->num_rows() > 0) {
            $entreprises = $query->result_array();
        }
    }
    ?>

    <!-- HEADER -->
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fa fa-building"></i> Gestion des entreprises</h1>
            <div class="subtitle">
                <i class="fa fa-calendar"></i> <?php echo date('d/m/Y H:i'); ?> · 
                <i class="fa fa-users"></i> Gestion des comptes entreprises
            </div>
        </div>
        <div class="header-actions">
            <button class="btn-header" onclick="window.location.reload();">
                <i class="fa fa-refresh"></i> Actualiser
            </button>
            <button class="btn-header" onclick="window.print();">
                <i class="fa fa-print"></i> Imprimer
            </button>
            <?php if ($can_add_company): ?>
                <a href="<?= site_url('admin/comptes/create') ?>" class="btn-header btn-header-primary">
                    <i class="fa fa-plus"></i> Ajouter une entreprise
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon blue"><i class="fa fa-building"></i></div>
                <span class="stat-badge"><i class="fa fa-database"></i> Total</span>
            </div>
            <div class="stat-number"><?= $total_entreprises ?></div>
            <div class="stat-label">Entreprises enregistrées</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon green"><i class="fa fa-check-circle"></i></div>
                <span class="stat-badge green"><i class="fa fa-check"></i> Actif</span>
            </div>
            <div class="stat-number"><?= $entreprises_actives ?></div>
            <div class="stat-label">Entreprises actives</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon orange"><i class="fa fa-clock-o"></i></div>
                <span class="stat-badge orange"><i class="fa fa-hourglass-half"></i> Bientôt</span>
            </div>
            <div class="stat-number"><?= $abonnements_bientot_expire ?></div>
            <div class="stat-label">Expirent dans 7 jours</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon red"><i class="fa fa-exclamation-triangle"></i></div>
                <span class="stat-badge red"><i class="fa fa-warning"></i> Expiré</span>
            </div>
            <div class="stat-number"><?= $abonnements_expires ?></div>
            <div class="stat-label">Abonnements expirés</div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-card">
        <div class="table-header">
            <h4 class="table-title">
                <i class="fa fa-list"></i> Liste des entreprises
                <span class="count-badge"><?= count($entreprises) ?></span>
            </h4>
            <div class="table-tools">
                <div class="search-box">
                    <i class="fa fa-search"></i>
                    <input type="text" id="tableSearch" placeholder="Rechercher..." onkeyup="filterTable()">
                </div>
                <?php if ($can_add_company): ?>
                    <a href="<?= site_url('admin/comptes/create') ?>" class="btn-add">
                        <i class="fa fa-plus"></i> Nouvelle entreprise
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table" id="entrepriseTable">
                <thead>
                    <tr>
                        <th style="width:18%;">Entreprise</th>
                        <th style="width:18%;">Contact</th>
                        <th style="width:10%;">Forfait</th>
                        <th style="width:10%;">Début</th>
                        <th style="width:12%;">Expiration</th>
                        <th style="width:12%;">Statut</th>
                        <th style="width:20%; text-align:center;" class="no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($entreprises)): ?>
                        <?php foreach ($entreprises as $entreprise): 
                            $isExpiring = false;
                            if (!empty($entreprise['date_expiration'])) {
                                $dateExpiration = strtotime($entreprise['date_expiration']);
                                $today = strtotime(date("Y-m-d"));
                                $isExpiring = ($dateExpiration <= $today || $dateExpiration <= strtotime("+7 days", $today));
                            }
                            
                            $statut_class = '';
                            $statut_dot = '';
                            switch($entreprise['statut']) {
                                case 'actif': 
                                    $statut_class = "badge-success"; 
                                    $statut_dot = "active";
                                    break;
                                case 'inactif': 
                                    $statut_class = "badge-warning"; 
                                    $statut_dot = "inactive";
                                    break;
                                case 'expire': 
                                    $statut_class = "badge-danger"; 
                                    $statut_dot = "expired";
                                    break;
                                case 'expiré': 
                                    $statut_class = "badge-danger"; 
                                    $statut_dot = "expired";
                                    break;
                                case 'suspendu': 
                                    $statut_class = "badge-secondary"; 
                                    $statut_dot = "suspended";
                                    break;
                                default: 
                                    $statut_class = "badge-info";
                                    $statut_dot = "active";
                            }

                            $forfait_classes = [
                                'starter' => 'starter',
                                'pro' => 'pro',
                                'premium' => 'premium',
                                'enterprise' => 'enterprise'
                            ];
                            $forfait_class = $forfait_classes[strtolower($entreprise['forfait'] ?? '')] ?? 'starter';
                        ?>
                        <tr>
                            <td>
                                <span class="company-name">
                                    <i class="fa fa-building-o"></i>
                                    <?php echo htmlspecialchars($entreprise['nom'] ?? ''); ?>
                                </span>
                            </td>
                            <td>
                                <div class="contact-info">
                                    <div><i class="fa fa-envelope-o"></i> <?php echo htmlspecialchars($entreprise['email'] ?? ''); ?></div>
                                    <div><i class="fa fa-phone"></i> <?php echo htmlspecialchars($entreprise['telephone'] ?? ''); ?></div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-forfait <?php echo $forfait_class; ?>">
                                    <?php echo strtoupper($entreprise['forfait'] ?? 'N/A'); ?>
                                </span>
                            </td>
                            <td><?php echo !empty($entreprise['date_debut']) ? date("d/m/Y", strtotime($entreprise['date_debut'])) : '-'; ?></td>
                            <td>
                                <?php if (!empty($entreprise['date_expiration'])): ?>
                                    <span class="<?php echo $isExpiring ? 'expiry-alert' : ''; ?>">
                                        <?php echo date("d/m/Y", strtotime($entreprise['date_expiration'])); ?>
                                    </span>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?php echo $statut_class; ?>">
                                    <span class="status-dot <?php echo $statut_dot; ?>"></span>
                                    <?php echo strtoupper($entreprise['statut'] ?? 'N/A'); ?>
                                </span>
                            </td>
                            <td style="text-align:center;" class="no-print">
                                <div class="action-group">
                                    <a href="javascript:void(0)" class="btn-action view" title="Voir" onclick="viewCompany(<?php echo (int)$entreprise['id']; ?>)">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn-action edit" title="Modifier" onclick="editCompany(<?php echo (int)$entreprise['id']; ?>)">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <?php 
                                    $status = strtolower($entreprise['statut'] ?? '');
                                    if ($status === 'actif'): ?>
                                        <a href="javascript:void(0)" class="btn-action suspend" title="Suspendre" onclick="toggleStatus(<?php echo (int)$entreprise['id']; ?>, 'actif')">
                                            <i class="fa fa-pause"></i>
                                        </a>
                                    <?php elseif ($status === 'suspendu' || $status === 'inactif'): ?>
                                        <a href="javascript:void(0)" class="btn-action activate" title="Activer" onclick="toggleStatus(<?php echo (int)$entreprise['id']; ?>, 'suspendu')">
                                            <i class="fa fa-play"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="javascript:void(0)" class="btn-action delete" title="Supprimer" onclick="deleteCompany(<?php echo (int)$entreprise['id']; ?>)">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:60px 20px; color:#94a3b8;">
                                <i class="fa fa-building" style="font-size:48px; display:block; margin-bottom:16px; opacity:0.3;"></i>
                                <h4 style="color:#64748b; margin-bottom:8px;">Aucune entreprise</h4>
                                <p style="color:#94a3b8;">Aucune entreprise enregistrée pour le moment</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- MODAL VIEW -->
<div class="modal fade" id="viewCompanyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-building"></i> Détails de l'entreprise</h4>
            </div>
            <div class="modal-body" id="viewCompanyContent">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-2x"></i> Chargement...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="editCompanyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-edit"></i> Modifier l'entreprise</h4>
            </div>
            <div class="modal-body" id="editCompanyContent">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-2x"></i> Chargement...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<script>
// Recherche en temps réel
function filterTable() {
    var input = document.getElementById('tableSearch');
    var filter = input.value.toLowerCase();
    var rows = document.querySelectorAll('#entrepriseTable tbody tr');
    
    rows.forEach(function(row) {
        var text = row.textContent.toLowerCase();
        row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
    });
}

// Voir entreprise
function viewCompany(id) {
    $('#viewCompanyContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Chargement...</div>');
    $('#viewCompanyModal').modal('show');
    
    $.ajax({
        url: '<?php echo site_url("admin/comptes/ajax_view/"); ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#viewCompanyContent').html(response.html);
            } else {
                $('#viewCompanyContent').html('<div class="alert alert-danger">' + response.message + '</div>');
            }
        },
        error: function() {
            $('#viewCompanyContent').html('<div class="alert alert-danger">Erreur lors du chargement des détails</div>');
        }
    });
}

// Modifier entreprise
function editCompany(id) {
    $('#editCompanyContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Chargement...</div>');
    $('#editCompanyModal').modal('show');
    
    $.ajax({
        url: '<?php echo site_url("admin/comptes/ajax_edit/"); ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#editCompanyContent').html(response.html);
                // Ré-attacher les événements si nécessaire
            } else {
                $('#editCompanyContent').html('<div class="alert alert-danger">' + response.message + '</div>');
            }
        },
        error: function() {
            $('#editCompanyContent').html('<div class="alert alert-danger">Erreur lors du chargement du formulaire</div>');
        }
    });
}

// Changer le statut
function toggleStatus(id, currentStatus) {
    var newStatus = '';
    var actionText = '';
    var statusLower = currentStatus ? currentStatus.toLowerCase() : '';
    
    if (statusLower === 'actif') {
        newStatus = 'suspendu';
        actionText = 'suspendre';
    } else if (statusLower === 'suspendu' || statusLower === 'inactif') {
        newStatus = 'actif';
        actionText = 'activer';
    } else {
        alert('Impossible de changer ce statut !');
        return;
    }
    
    if (confirm('Êtes-vous sûr de vouloir ' + actionText + ' cette entreprise ?')) {
        $.ajax({
            url: '<?php echo site_url("admin/comptes/toggle_status/"); ?>' + id,
            type: 'POST',
            data: {
                new_status: newStatus,
                <?php echo $csrfName; ?>: '<?php echo $csrfHash; ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    window.location.reload();
                } else {
                    alert('Erreur: ' + response.message);
                }
            },
            error: function() {
                alert('Erreur lors de la modification du statut');
            }
        });
    }
}

// Supprimer entreprise
function deleteCompany(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette entreprise ? Cette action est irréversible.')) {
        window.location.href = '<?php echo site_url("admin/comptes/delete/"); ?>' + id;
    }
}

// Auto-fermeture des messages
$(document).ready(function() {
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>

</body>
</html>