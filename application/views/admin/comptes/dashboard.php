<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Gestion des Entreprises</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <style>
        /* ===== RESET & BASE ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            color: #333;
        }

        .content-wrapper {
            padding: 20px;
            background: #f4f6f9;
            min-height: 100vh;
        }

        /* ===== HEADER ===== */
        .page-header {
            background: linear-gradient(135deg, #1a3c5e 0%, #2c5a8c 100%);
            padding: 25px 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .page-header h1 i {
            margin-right: 12px;
        }

        .page-header .subtitle {
            font-size: 14px;
            opacity: 0.8;
            margin-top: 4px;
        }

        .page-header .header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .page-header .btn-light {
            background: rgba(255,255,255,0.15);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 13px;
        }

        .page-header .btn-light:hover {
            background: rgba(255,255,255,0.25);
        }

        /* ===== STATS CARDS ===== */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px 22px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid #3b82f6;
            transition: all 0.3s ease;
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        }

        .stat-card .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 10px;
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
            font-size: 30px;
            font-weight: 800;
            color: #1e293b;
            line-height: 1.2;
        }

        .stat-card .stat-label {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
            margin-top: 2px;
        }

        .stat-card .stat-badge {
            position: absolute;
            top: 18px;
            right: 18px;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 12px;
            border-radius: 20px;
            background: #f1f5f9;
            color: #64748b;
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

        /* ===== CHARTS ===== */
        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .chart-box {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .chart-box .chart-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin: 0 0 18px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-box .chart-title i {
            margin-right: 8px;
            color: #2c5a8c;
        }

        .chart-box .chart-badge {
            font-size: 11px;
            padding: 3px 12px;
            border-radius: 20px;
            background: #f1f5f9;
            color: #64748b;
            font-weight: 500;
        }

        .chart-container {
            position: relative;
            height: 250px;
        }

        /* ===== TABLE ===== */
        .table-box {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .table-box .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .table-box .table-title {
            font-size: 17px;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        .table-box .table-title i {
            margin-right: 10px;
            color: #2c5a8c;
        }

        .table-box .table-tools {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .table-box .search-box {
            position: relative;
        }

        .table-box .search-box input {
            padding: 7px 14px 7px 32px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            width: 200px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .table-box .search-box input:focus {
            outline: none;
            border-color: #2c5a8c;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(44, 90, 140, 0.1);
        }

        .table-box .search-box i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .table-box .btn-add {
            background: #2c5a8c;
            color: #fff;
            border: none;
            padding: 7px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .table-box .btn-add:hover {
            background: #1a3c5e;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }

        .table tbody td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        .table .company-name {
            font-weight: 600;
            color: #1e293b;
        }

        .table .company-name i {
            margin-right: 6px;
            color: #2c5a8c;
            opacity: 0.6;
        }

        .table .contact-info {
            font-size: 12px;
            line-height: 1.6;
        }

        .table .contact-info i {
            color: #94a3b8;
            width: 16px;
        }

        .label {
            display: inline-block;
            padding: 3px 12px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .label-success {
            background: #d1fae5;
            color: #065f46;
        }

        .label-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .label-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .label-info {
            background: #dbeafe;
            color: #1e40af;
        }

        .label-primary {
            background: #dbeafe;
            color: #1e40af;
        }

        .label-default {
            background: #f1f5f9;
            color: #64748b;
        }

        .alert-date {
            background: #fee2e2;
            color: #991b1b;
            padding: 2px 10px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
            animation: blink 1.5s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .status-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            margin-right: 6px;
        }

        .status-dot.active { background: #10b981; }
        .status-dot.inactive { background: #f59e0b; }
        .status-dot.expired { background: #ef4444; }
        .status-dot.suspended { background: #94a3b8; }

        .table-actions {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .btn-action {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            color: #64748b;
            text-decoration: none;
            font-size: 13px;
        }

        .btn-action:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            text-decoration: none;
        }

        .btn-action.edit:hover {
            background: #dbeafe;
            border-color: #93c5fd;
            color: #2563eb;
        }

        .btn-action.delete:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #dc2626;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #94a3b8;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1200px) {
            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 992px) {
            .charts-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
                padding: 20px;
            }

            .page-header .header-actions {
                justify-content: center;
            }

            .stats-row {
                grid-template-columns: 1fr 1fr;
                gap: 15px;
            }

            .stat-card {
                padding: 16px;
            }

            .stat-card .stat-number {
                font-size: 24px;
            }

            .table-box .table-header {
                flex-direction: column;
                align-items: stretch;
            }

            .table-box .table-tools {
                flex-direction: column;
            }

            .table-box .search-box input {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .stats-row {
                grid-template-columns: 1fr;
            }

            .page-header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>

<?php
// ===============================
// CONNEXION BDD
// ===============================
$conn = new mysqli("localhost", "root", "", "diago");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// ===============================
// STATISTIQUES
// ===============================
$sqlTotal = "SELECT COUNT(*) AS total FROM compte_entreprise";
$resultTotal = $conn->query($sqlTotal);
$total_entreprises = ($resultTotal && ($r = $resultTotal->fetch_assoc())) ? (int)$r['total'] : 0;

$sqlActives = "SELECT COUNT(*) AS total FROM compte_entreprise WHERE statut = 'actif'";
$resultActives = $conn->query($sqlActives);
$entreprises_actives = ($resultActives && ($r = $resultActives->fetch_assoc())) ? (int)$r['total'] : 0;

$sqlExpires = "SELECT COUNT(*) AS total FROM compte_entreprise WHERE date_expiration < CURDATE() AND statut != 'expire'";
$resultExpires = $conn->query($sqlExpires);
$abonnements_expires = ($resultExpires && ($r = $resultExpires->fetch_assoc())) ? (int)$r['total'] : 0;

$sqlBientotExpire = "SELECT COUNT(*) AS total FROM compte_entreprise WHERE date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND statut = 'actif'";
$resultBientotExpire = $conn->query($sqlBientotExpire);
$abonnements_bientot_expire = ($resultBientotExpire && ($r = $resultBientotExpire->fetch_assoc())) ? (int)$r['total'] : 0;

// ===============================
// LISTE DES ENTREPRISES
// ===============================
$sqlEntreprises = "SELECT * FROM compte_entreprise ORDER BY date_debut DESC";
$resultEntreprises = $conn->query($sqlEntreprises);
$entreprises = [];
if ($resultEntreprises) {
    while ($row = $resultEntreprises->fetch_assoc()) {
        $entreprises[] = $row;
    }
}

// ===============================
// RÉPARTITION PAR FORFAIT
// ===============================
$sqlForfaits = "SELECT forfait, COUNT(*) as count FROM compte_entreprise GROUP BY forfait";
$resultForfaits = $conn->query($sqlForfaits);
$forfaits_data = [];
$forfait_colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
$color_index = 0;
if ($resultForfaits) {
    while ($row = $resultForfaits->fetch_assoc()) {
        $forfaits_data[] = [
            'label' => $row['forfait'],
            'value' => (int)$row['count'],
            'color' => $forfait_colors[$color_index % count($forfait_colors)]
        ];
        $color_index++;
    }
}

// ===============================
// ÉVOLUTION DES INSCRIPTIONS
// ===============================
$sqlEvolution = "SELECT 
                    DATE_FORMAT(date_debut, '%Y-%m') as mois, 
                    COUNT(*) as count 
                 FROM compte_entreprise 
                 WHERE date_debut >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                 GROUP BY mois 
                 ORDER BY mois ASC";
$resultEvolution = $conn->query($sqlEvolution);
$evolution_mois = [];
$evolution_count = [];
if ($resultEvolution) {
    while ($row = $resultEvolution->fetch_assoc()) {
        $evolution_mois[] = date('M Y', strtotime($row['mois'] . '-01'));
        $evolution_count[] = (int)$row['count'];
    }
}
?>

<!-- ===== CONTENT ===== -->
<div class="content-wrapper">

    <!-- HEADER -->
    <div class="page-header">
        <div>
            <h1><i class="fa fa-building"></i> Tableau de bord</h1>
            <div class="subtitle"><i class="fa fa-calendar"></i> <?php echo date('d/m/Y H:i'); ?> - Gestion des entreprises et abonnements</div>
        </div>
        <div class="header-actions">
            <button class="btn-light" onclick="window.location.reload();">
                <i class="fa fa-refresh"></i> Actualiser
            </button>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="stats-row">
        <div class="stat-card" style="border-left-color: #3b82f6;">
            <div class="stat-icon blue"><i class="fa fa-building"></i></div>
            <div class="stat-number"><?= $total_entreprises ?></div>
            <div class="stat-label">Total entreprises</div>
            <span class="stat-badge"><i class="fa fa-database"></i> Enregistrées</span>
        </div>

        <div class="stat-card" style="border-left-color: #10b981;">
            <div class="stat-icon green"><i class="fa fa-check-circle"></i></div>
            <div class="stat-number"><?= $entreprises_actives ?></div>
            <div class="stat-label">Entreprises actives</div>
            <span class="stat-badge green">
                <i class="fa fa-arrow-up"></i> 
                <?= $total_entreprises > 0 ? round(($entreprises_actives / $total_entreprises) * 100) : 0 ?>%
            </span>
        </div>

        <div class="stat-card" style="border-left-color: #f59e0b;">
            <div class="stat-icon orange"><i class="fa fa-clock-o"></i></div>
            <div class="stat-number"><?= $abonnements_bientot_expire ?></div>
            <div class="stat-label">Expirent bientôt</div>
            <span class="stat-badge orange"><i class="fa fa-hourglass-half"></i> 7 jours</span>
        </div>

        <div class="stat-card" style="border-left-color: #ef4444;">
            <div class="stat-icon red"><i class="fa fa-exclamation-triangle"></i></div>
            <div class="stat-number"><?= $abonnements_expires ?></div>
            <div class="stat-label">Abonnements expirés</div>
            <span class="stat-badge red"><i class="fa fa-arrow-down"></i> À renouveler</span>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="charts-row">
        <div class="chart-box">
            <div class="chart-title">
                <span><i class="fa fa-pie-chart"></i> Répartition par forfait</span>
                <span class="chart-badge"><i class="fa fa-chevron-circle-right"></i> Vue d'ensemble</span>
            </div>
            <div class="chart-container">
                <canvas id="chartForfaits"></canvas>
            </div>
        </div>

        <div class="chart-box">
            <div class="chart-title">
                <span><i class="fa fa-line-chart"></i> Évolution des inscriptions</span>
                <span class="chart-badge"><i class="fa fa-calendar"></i> 6 derniers mois</span>
            </div>
            <div class="chart-container">
                <canvas id="chartEvolution"></canvas>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-box">
        <div class="table-header">
            <h4 class="table-title">
                <i class="fa fa-list"></i> Liste des entreprises
                <span style="font-size:13px; font-weight:400; color:#94a3b8; margin-left:8px;">
                    (<?= count($entreprises) ?> entreprises)
                </span>
            </h4>
            <div class="table-tools">
                <div class="search-box">
                    <i class="fa fa-search"></i>
                    <input type="text" id="tableSearch" placeholder="Rechercher..." onkeyup="filterTable()">
                </div>
                <button class="btn-add" onclick="window.location.href='<?php echo site_url('admin/comptes/create'); ?>'">
                    <i class="fa fa-plus"></i> Nouvelle
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table" id="entrepriseTable">
                <thead>
                    <tr>
                        <th>Entreprise</th>
                        <th>Contact</th>
                        <th>Forfait</th>
                        <th>Début</th>
                        <th>Expiration</th>
                        <th>Statut</th>
                        <th style="text-align:center; width:80px;">Actions</th>
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
                                    $statut_class = "label-success"; 
                                    $statut_dot = "active";
                                    break;
                                case 'inactif': 
                                    $statut_class = "label-warning"; 
                                    $statut_dot = "inactive";
                                    break;
                                case 'expire': 
                                    $statut_class = "label-danger"; 
                                    $statut_dot = "expired";
                                    break;
                                case 'suspendu': 
                                    $statut_class = "label-default"; 
                                    $statut_dot = "suspended";
                                    break;
                                default: 
                                    $statut_class = "label-info";
                                    $statut_dot = "active";
                            }
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
                                <span class="label label-primary">
                                    <?php echo strtoupper($entreprise['forfait'] ?? 'N/A'); ?>
                                </span>
                            </td>
                            <td><?php echo !empty($entreprise['date_debut']) ? date("d/m/Y", strtotime($entreprise['date_debut'])) : '-'; ?></td>
                            <td>
                                <?php if (!empty($entreprise['date_expiration'])): ?>
                                    <span class="<?php echo $isExpiring ? 'alert-date' : ''; ?>">
                                        <?php echo date("d/m/Y", strtotime($entreprise['date_expiration'])); ?>
                                    </span>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="label <?php echo $statut_class; ?>">
                                    <span class="status-dot <?php echo $statut_dot; ?>"></span>
                                    <?php echo strtoupper($entreprise['statut'] ?? 'N/A'); ?>
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <div class="table-actions">
                                    <a href="<?php echo site_url('admin/comptes/edit/' . ($entreprise['id'] ?? 0)); ?>" 
                                       class="btn-action edit" title="Modifier">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <a href="<?php echo site_url('admin/comptes/delete/' . ($entreprise['id'] ?? 0)); ?>" 
                                       class="btn-action delete" title="Supprimer"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entreprise ? Cette action est irréversible.')">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:40px 0; color:#94a3b8;">
                                <i class="fa fa-building" style="font-size:36px; display:block; margin-bottom:10px; opacity:0.3;"></i>
                                Aucune entreprise enregistrée
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ===== SCRIPTS ===== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ===============================
    // CHART: RÉPARTITION PAR FORFAIT
    // ===============================
    const forfaitData = <?= json_encode($forfaits_data) ?>;
    
    if (forfaitData.length > 0) {
        const ctx1 = document.getElementById('chartForfaits').getContext('2d');
        new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: forfaitData.map(d => d.label.toUpperCase()),
                datasets: [{
                    data: forfaitData.map(d => d.value),
                    backgroundColor: forfaitData.map(d => d.color),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 12 }
                        }
                    }
                }
            }
        });
    }

    // ===============================
    // CHART: ÉVOLUTION DES INSCRIPTIONS
    // ===============================
    const evolutionLabels = <?= json_encode($evolution_mois) ?>;
    const evolutionValues = <?= json_encode($evolution_count) ?>;
    
    if (evolutionLabels.length > 0) {
        const ctx2 = document.getElementById('chartEvolution').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: evolutionLabels,
                datasets: [{
                    label: 'Nouvelles inscriptions',
                    data: evolutionValues,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 11 } },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }

    // ===============================
    // FILTRE RECHERCHE
    // ===============================
    function filterTable() {
        const input = document.getElementById('tableSearch');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('entrepriseTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            let found = false;
            for (let j = 0; j < cells.length - 1; j++) {
                if (cells[j] && cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
            rows[i].style.display = found ? '' : 'none';
        }
    }
</script>

</body>
</html>