<style>
    .alert-date {
        color: #fff;
        background: #d9534f;
        padding: 5px 10px;
        border-radius: 4px;
        animation: blink 1s infinite;
    }

    @keyframes blink {
        0%   { background-color: #d9534f; }
        50%  { background-color: #c9302c; }
        100% { background-color: #d9534f; }
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    .chart-placeholder {
        height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9f9f9;
        border-radius: 5px;
        color: #999;
        font-style: italic;
    }

    .progress-bar-objectif {
        background: linear-gradient(90deg, #00c0ef 0%, #0073b7 100%);
    }

    .achievement-badge {
        font-size: 16px;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: bold;
    }

    .achievement-excellent {
        background-color: #00a65a;
        color: white;
    }

    .achievement-good {
        background-color: #00c0ef;
        color: white;
    }

    .achievement-average {
        background-color: #f39c12;
        color: white;
    }

    .achievement-poor {
        background-color: #dd4b39;
        color: white;
    }

    .comparison-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .objectif-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .objectif-title {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 5px;
    }
    .objectif-value {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .objectif-progress {
        height: 10px;
        background: rgba(255,255,255,0.3);
        border-radius: 5px;
        overflow: hidden;
        margin: 10px 0;
    }
    .objectif-progress-bar {
        height: 100%;
        background: #ffd700;
        border-radius: 5px;
        transition: width 0.5s;
    }
    .objectif-stats {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        font-size: 13px;
    }
    .objectif-stat-item {
        text-align: center;
        flex: 1;
    }
    .objectif-stat-label {
        opacity: 0.8;
        font-size: 11px;
    }
    .objectif-stat-value {
        font-weight: bold;
        font-size: 16px;
    }

    .achievement-badge {
        font-size: 16px;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: bold;
    }
    .achievement-excellent {
        background-color: #00a65a;
        color: white;
    }
    .achievement-good {
        background-color: #00c0ef;
        color: white;
    }
    .achievement-average {
        background-color: #f39c12;
        color: white;
    }
    .achievement-poor {
        background-color: #dd4b39;
        color: white;
    }

    .comparison-row {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 5px solid #ffc107;
    }
    .comparison-item {
        text-align: center;
        padding: 10px;
    }
    .comparison-label {
        font-size: 14px;
        color: #666;
        margin-bottom: 5px;
    }
    .comparison-value {
        font-size: 20px;
        font-weight: bold;
    }
    .comparison-ecart {
        font-size: 20px;
        font-weight: bold;
    }
    .text-success { color: #00a65a; }
    .text-danger { color: #dd4b39; }
    .text-warning { color: #f39c12; }
</style>


<?php
// ===============================
// Connexion BDD
// ===============================
$CI = &get_instance();
$conn = $CI->db->conn_id;

// Vérifier la connexion
if (!$conn) {
    die("Erreur de connexion à la base de données");
}


// ===============================
// CHIFFRE D'AFFAIRES RÉALISÉ, ENCAISSÉ ET CRÉANCE
// ===============================

// 1. CHIFFRE D'AFFAIRES RÉALISÉ (Total TTC de toutes les factures)
$sqlCARealise = "SELECT SUM(total_ttc) AS ca_realise 
                 FROM invoices 
                 WHERE YEAR(created_at) = YEAR(CURDATE())";
$resultCARealise = $conn->query($sqlCARealise);
$ca_realise = ($resultCARealise && ($r = $resultCARealise->fetch_assoc())) ? (float)$r['ca_realise'] : 0;

// 2. CHIFFRE D'AFFAIRES ENCAISSÉ (Montant déjà payé)
$sqlCAEncaisse = "SELECT SUM(amount_paid) AS ca_encaisse 
                  FROM invoices 
                  WHERE YEAR(created_at) = YEAR(CURDATE())";
$resultCAEncaisse = $conn->query($sqlCAEncaisse);
$ca_encaisse = ($resultCAEncaisse && ($r = $resultCAEncaisse->fetch_assoc())) ? (float)$r['ca_encaisse'] : 0;

// 3. CRÉANCE (CORRIGÉE) = CA Réalisé - CA Encaissé
$creance = $ca_realise - $ca_encaisse;

// Taux d'encaissement
$taux_encaissement = ($ca_realise > 0) ? round(($ca_encaisse / $ca_realise) * 100, 2) : 0;

// Calcul des pourcentages pour le camembert
$pourcentage_encaisse = $ca_realise > 0 ? round(($ca_encaisse / $ca_realise) * 100, 1) : 0;
$pourcentage_reste = $ca_realise > 0 ? round(($creance / $ca_realise) * 100, 1) : 0;

// ===============================
// OBJECTIFS COMMERCIAUX - CORRIGÉ
// ===============================
$annee_en_cours = date('Y');

// Requête corrigée : somme directe des objectifs de l'année en cours
$sqlObjectifs = "SELECT SUM(target_amount) as total_objectif 
                 FROM `objectifs_commercial` 
                 WHERE YEAR(date) = $annee_en_cours";
$resObjectifs = $conn->query($sqlObjectifs);

$objectif_annee_courante = 0;

if ($resObjectifs && $resObjectifs->num_rows > 0) {
    $row = $resObjectifs->fetch_assoc();
    $objectif_annee_courante = $row['total_objectif'] ? (float)$row['total_objectif'] : 0;
}

// Taux de réalisation de l'objectif
$taux_realisation = $objectif_annee_courante > 0 ? round(($ca_realise / $objectif_annee_courante) * 100, 2) : 0;
$ecart_objectif = $ca_realise - $objectif_annee_courante;
$reste_objectif = max(0, $objectif_annee_courante - $ca_realise);

// ===============================
// ÉVOLUTION MENSUELLE (CA réalisé, encaissé, créance)
// ===============================
$sqlEvolutionMensuelle = "
    SELECT 
        MONTH(created_at) AS mois,
        SUM(total_ttc) AS ca_realise,
        SUM(amount_paid) AS ca_encaisse
    FROM invoices
    WHERE YEAR(created_at) = YEAR(CURDATE())
    GROUP BY MONTH(created_at)
    ORDER BY mois ASC
";
$resEvolution = $conn->query($sqlEvolutionMensuelle);

$mois_noms = [1 => "Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sept", "Oct", "Nov", "Déc"];
$mois_labels = [];
$ca_realise_mensuel = array_fill(0, 12, 0);
$ca_encaisse_mensuel = array_fill(0, 12, 0);
$creance_mensuel = array_fill(0, 12, 0);

$has_data_evolution = false;
if ($resEvolution && $resEvolution->num_rows > 0) {
    $has_data_evolution = true;
    while ($row = $resEvolution->fetch_assoc()) {
        $mois_index = (int)$row['mois'] - 1;
        $ca_realise_mensuel[$mois_index] = (float)$row['ca_realise'];
        $ca_encaisse_mensuel[$mois_index] = (float)$row['ca_encaisse'];
        // Créance mensuelle = CA Réalisé mensuel - CA Encaissé mensuel
        $creance_mensuel[$mois_index] = $ca_realise_mensuel[$mois_index] - $ca_encaisse_mensuel[$mois_index];
    }
    $mois_labels = $mois_noms;
}

// ===============================
// TOP 5 CLIENTS PAR CRÉANCE
// ===============================
// CORRIGÉ : recalculer la créance par client avec la même logique
$sqlTopClientsCreance = "
    SELECT 
        c.item_supplier AS client,
        SUM(i.total_ttc) AS ca_realise_client,
        SUM(i.amount_paid) AS ca_encaisse_client
    FROM invoices i
    JOIN clients c ON i.customer_id = c.id
    WHERE YEAR(i.created_at) = YEAR(CURDATE())
    GROUP BY c.id, c.item_supplier
    HAVING (SUM(i.total_ttc) - SUM(i.amount_paid)) > 0
    ORDER BY (SUM(i.total_ttc) - SUM(i.amount_paid)) DESC
    LIMIT 5
";
$resTopClients = $conn->query($sqlTopClientsCreance);

$top_clients = [];
$creance_clients = [];
$has_top_clients = false;
if ($resTopClients && $resTopClients->num_rows > 0) {
    $has_top_clients = true;
    while ($row = $resTopClients->fetch_assoc()) {
        $top_clients[] = $row['client'] ?: 'Client Inconnu';
        $creance_clients[] = (float)($row['ca_realise_client'] - $row['ca_encaisse_client']);
    }
}

// Calcul des pourcentages pour le top clients
$total_creance_top = array_sum($creance_clients);
$top_clients_percentages = [];
foreach($creance_clients as $creance_client) {
    $top_clients_percentages[] = $total_creance_top > 0 ? round(($creance_client / $total_creance_top) * 100, 1) : 0;
}

// ===============================
// ÉVOLUTION DU TAUX D'ENCAISSEMENT
// ===============================
$sqlTauxMensuel = "
    SELECT 
        MONTH(created_at) AS mois,
        CASE 
            WHEN SUM(total_ttc) > 0 THEN ROUND((SUM(amount_paid) / SUM(total_ttc)) * 100, 2)
            ELSE 0
        END AS taux_encaissement
    FROM invoices
    WHERE YEAR(created_at) = YEAR(CURDATE())
    GROUP BY MONTH(created_at)
    ORDER BY mois ASC
";
$resTaux = $conn->query($sqlTauxMensuel);

$taux_mensuel = array_fill(0, 12, 0);
$has_taux_data = false;
if ($resTaux && $resTaux->num_rows > 0) {
    $has_taux_data = true;
    while ($row = $resTaux->fetch_assoc()) {
        $mois_index = (int)$row['mois'] - 1;
        $taux_mensuel[$mois_index] = (float)$row['taux_encaissement'];
    }
}

// ===============================
// RÉPARTITION DU CA PAR STATUT DE PAIEMENT (CORRIGÉ)
// ===============================
// On utilise la même logique : créance = total_ttc - amount_paid
$sqlRepartitionStatus = "
    SELECT 
        CASE 
            WHEN (total_ttc - amount_paid) = 0 THEN 'Payé'
            WHEN amount_paid > 0 THEN 'Partiellement payé'
            ELSE 'Non payé'
        END AS status_paiement,
        COUNT(*) AS nb_factures,
        SUM(total_ttc) AS ca_total,
        SUM(amount_paid) AS ca_encaisse,
        SUM(total_ttc - amount_paid) AS reste
    FROM invoices
    WHERE YEAR(created_at) = YEAR(CURDATE())
    GROUP BY 
        CASE 
            WHEN (total_ttc - amount_paid) = 0 THEN 'Payé'
            WHEN amount_paid > 0 THEN 'Partiellement payé'
            ELSE 'Non payé'
        END
";
$resStatus = $conn->query($sqlRepartitionStatus);

$status_labels = [];
$status_ca = [];
$status_encaisse = [];
$status_reste = [];
$has_status_data = false;
if ($resStatus && $resStatus->num_rows > 0) {
    $has_status_data = true;
    while ($row = $resStatus->fetch_assoc()) {
        $status_labels[] = $row['status_paiement'];
        $status_ca[] = (float)$row['ca_total'];
        $status_encaisse[] = (float)$row['ca_encaisse'];
        $status_reste[] = (float)$row['reste'];
    }
}

// Calcul des pourcentages pour les statuts
$total_ca_status = array_sum($status_ca);
$status_percentages = [];
foreach($status_ca as $ca) {
    $status_percentages[] = $total_ca_status > 0 ? round(($ca / $total_ca_status) * 100, 1) : 0;
}

// ===============================
// DATES ET PÉRIODE EN COURS
// ===============================
$mois_en_cours = date('F');
$date_du_jour = date('d/m/Y');
$semaine_en_cours = date('W');
$jour_semaine = date('l');

// Vérifier si on a des données pour afficher les graphiques
$has_any_data = $ca_realise > 0 || $ca_encaisse > 0 || $creance > 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - CA & Créances - <?= $annee_en_cours ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .content-wrapper { padding:20px; background:#f4f6f9; }
        .small-box { border-radius: 10px; color:#fff; padding:20px; position:relative; overflow:hidden; margin-bottom:20px;}
        .small-box .icon { position:absolute; top:10px; right:10px; font-size:40px; opacity:0.3; }
        .box { border-radius:10px; box-shadow:0 3px 6px rgba(0,0,0,0.1); margin-bottom:20px;}
        canvas { width:100% !important; height:300px !important; }
        .bg-aqua { background: #00c0ef !important; }
        .bg-green { background: #00a65a !important; }
        .bg-red { background: #dd4b39 !important; }
        .bg-purple { background: #605ca8 !important; }
        .bg-yellow { background: #f39c12 !important; }
        .bg-blue { background: #0073b7 !important; }

        .info-box {
            min-height: 90px;
            background: #fff;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .info-box-icon {
            float: left;
            height: 60px;
            width: 60px;
            text-align: center;
            line-height: 60px;
            border-radius: 5px;
            color: #fff;
            font-size: 30px;
        }
        .info-box-content { margin-left: 70px; }
        .info-box-text { font-size: 14px; color: #666; }
        .info-box-number { font-weight: bold; font-size: 18px; margin-top: 5px; }

        .date-info {
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            border: 1px solid #e0e0e0;
        }
        .date-header {
            color: #007bff;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .date-content {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .date-item {
            flex: 1;
            min-width: 150px;
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        .date-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        .date-value {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        .date-value.year {
            color: #dc3545;
        }
        .date-value.month {
            color: #28a745;
        }
        .date-value.day {
            color: #17a2b8;
        }
        .date-value.week {
            color: #ffc107;
        }

        .objectif-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .objectif-title {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        .objectif-value {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .objectif-progress {
            height: 10px;
            background: rgba(255,255,255,0.3);
            border-radius: 5px;
            overflow: hidden;
            margin: 10px 0;
        }
        .objectif-progress-bar {
            height: 100%;
            background: #ffd700;
            border-radius: 5px;
            transition: width 0.5s;
        }
        .objectif-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            font-size: 13px;
        }
        .objectif-stat-item {
            text-align: center;
            flex: 1;
        }
        .objectif-stat-label {
            opacity: 0.8;
            font-size: 11px;
        }
        .objectif-stat-value {
            font-weight: bold;
            font-size: 16px;
        }

        .achievement-badge {
            font-size: 16px;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        .achievement-excellent {
            background-color: #00a65a;
            color: white;
        }
        .achievement-good {
            background-color: #00c0ef;
            color: white;
        }
        .achievement-average {
            background-color: #f39c12;
            color: white;
        }
        .achievement-poor {
            background-color: #dd4b39;
            color: white;
        }

        .comparison-row {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 5px solid #ffc107;
        }
        .comparison-item {
            text-align: center;
            padding: 10px;
        }
        .comparison-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .comparison-value {
            font-size: 20px;
            font-weight: bold;
        }
        .comparison-ecart {
            font-size: 20px;
            font-weight: bold;
        }
        .text-success { color: #00a65a; }
        .text-danger { color: #dd4b39; }
        .text-warning { color: #f39c12; }
    </style>
</head>
<body>

<div class="content-wrapper">
    <!-- SECTION DATES ET PÉRIODES -->
    <section class="content-header">
        <h1><i class="fa fa-money"></i> Chiffre d'affaires & Créances</h1>
        <div class="date-info">
            <div class="date-header">Période en cours</div>
            <div class="date-content">
                <div class="date-item">
                    <div class="date-label">Année</div>
                    <div class="date-value year"><?= $annee_en_cours ?></div>
                </div>
                <div class="date-item">
                    <div class="date-label">Mois</div>
                    <div class="date-value month"><?= $mois_en_cours ?></div>
                </div>
                <div class="date-item">
                    <div class="date-label">Date</div>
                    <div class="date-value day"><?= $date_du_jour ?></div>
                </div>
                <div class="date-item">
                    <div class="date-label">Semaine</div>
                    <div class="date-value week"><?= $jour_semaine ?> (S<?= $semaine_en_cours ?>)</div>
                </div>
                <div class="date-item">
                    <div class="date-label">Factures</div>
                    <div class="date-value">
                        <?php
                        $sqlNbFactures = "SELECT COUNT(*) as nb FROM invoices WHERE YEAR(created_at) = YEAR(CURDATE())";
                        $resNbFactures = $conn->query($sqlNbFactures);
                        $nb_factures = ($resNbFactures && ($row = $resNbFactures->fetch_assoc())) ? $row['nb'] : 0;
                        echo $nb_factures;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <!-- KPIs PRINCIPAUX -->
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3><?= number_format($ca_realise, 0, ",", " ") ?> FCFA</h3>
                        <p>CA Réalisé <?= $annee_en_cours ?></p>
                        <small>Total des factures TTC</small>
                    </div>
                    <div class="icon"><i class="fa fa-line-chart"></i></div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?= number_format($ca_encaisse, 0, ",", " ") ?> FCFA</h3>
                        <p>CA Encaissé <?= $annee_en_cours ?></p>
                        <small>Taux: <?= $taux_encaissement ?>%</small>
                    </div>
                    <div class="icon"><i class="fa fa-credit-card"></i></div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3><?= number_format($creance, 0, ",", " ") ?> FCFA</h3>
                        <p>Créance <?= $annee_en_cours ?></p>
                        <small>Reste à encaisser</small>
                    </div>
                    <div class="icon"><i class="fa fa-exclamation-triangle"></i></div>
                </div>
            </div>
        </div>

        <!-- LIGNE DE COMPARAISON AVEC L'OBJECTIF (BIEN VISIBLE) -->
        <div class="row">
            <div class="col-md-12">
                <div class="comparison-row">
                    <div class="row">
                        <div class="col-md-3 comparison-item">
                            <div class="comparison-label"><i class="fa fa-bullseye"></i> OBJECTIF ANNUEL</div>
                            <div class="comparison-value" style="color: #0073b7;"><?= number_format($objectif_annee_courante, 0, ",", " ") ?> FCFA</div>
                        </div>
                        <div class="col-md-3 comparison-item">
                            <div class="comparison-label"><i class="fa fa-line-chart"></i> CA RÉALISÉ</div>
                            <div class="comparison-value" style="color: #00a65a;"><?= number_format($ca_realise, 0, ",", " ") ?> FCFA</div>
                        </div>
                        <div class="col-md-2 comparison-item">
                            <div class="comparison-label"><i class="fa fa-percent"></i> TAUX</div>
                            <div class="comparison-value" style="color: <?= $taux_realisation >= 100 ? '#00a65a' : ($taux_realisation >= 70 ? '#f39c12' : '#dd4b39') ?>;">
                                <?= $taux_realisation ?>%
                            </div>
                        </div>
                        <div class="col-md-3 comparison-item">
                            <div class="comparison-label"><i class="fa fa-clock-o"></i> SOLDE</div>
                            <div class="comparison-value text-danger"><?= number_format($reste_objectif, 0, ",", " ") ?> FCFA</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($has_any_data): ?>
            <!-- Graphique principal : CA Total vs Encaissé vs Reste (Cercle) -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-pie-chart"></i> Répartition du CA: Total vs Encaissé vs Reste - <?= $annee_en_cours ?></h3>
                        </div>
                        <div class="box-body">
                            <div class="chart-container">
                                <canvas id="chartRepartitionCA"></canvas>
                            </div>
                            <div class="table-responsive" style="margin-top: 20px;">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Indicateur</th>
                                        <th>Montant</th>
                                        <th>Pourcentage</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="active">
                                        <td><span class="label label-primary">CA Total</span></td>
                                        <td><strong><?= number_format($ca_realise, 0, ",", " ") ?> FCFA</strong></td>
                                        <td><span class="badge bg-blue">100%</span></td>
                                    </tr>
                                    <tr class="success">
                                        <td><span class="label label-success">Déjà encaissé</span></td>
                                        <td><?= number_format($ca_encaisse, 0, ",", " ") ?> FCFA</td>
                                        <td><span class="badge bg-green"><?= $pourcentage_encaisse ?>%</span></td>
                                    </tr>
                                    <tr class="danger">
                                        <td><span class="label label-danger">Reste à encaisser</span></td>
                                        <td><?= number_format($creance, 0, ",", " ") ?> FCFA</td>
                                        <td><span class="badge bg-red"><?= $pourcentage_reste ?>%</span></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deuxième ligne de graphiques -->
            <div class="row">
                <div class="col-md-6" style="width: 100%">
                    <div class="box box-warning">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-pie-chart"></i> Top 5 clients par créance - <?= $annee_en_cours ?></h3>
                        </div>
                        <div class="box-body">
                            <div class="chart-container">
                                <?php if ($has_top_clients): ?>
                                    <canvas id="chartTopClientsCreance"></canvas>
                                <?php else: ?>
                                    <div class="chart-placeholder">
                                        <p>Aucun client avec créance</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau détaillé des statuts -->
            <?php if ($has_status_data): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-default">
                            <div class="box-header">
                                <h3 class="box-title"><i class="fa fa-list"></i> Détail par statut de paiement</h3>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>

                                        <th>Statut</th>
                                        <th>CA Total</th>
                                        <th>Déjà encaissé</th>
                                        <th>Reste à encaisser</th>
                                        <th>% Encaissé</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php for($i = 0; $i < count($status_labels); $i++):
                                            $pourcentage = $status_ca[$i] > 0 ? round(($status_encaisse[$i] / $status_ca[$i]) * 100, 2) : 0;
                                            ?>
                                            <tr>
                                                <td>
                                                <span class="label
                                                    <?= $status_labels[$i] == 'Payé' ? 'label-success' :
                                                    ($status_labels[$i] == 'Partiellement payé' ? 'label-warning' : 'label-danger') ?>">
                                                    <?= $status_labels[$i] ?>
                                                </span>
                                                </td>
                                                <td><?= number_format($status_ca[$i], 0, ",", " ") ?> FCFA</td>
                                                <td><?= number_format($status_encaisse[$i], 0, ",", " ") ?> FCFA</td>
                                                <td><?= number_format($status_reste[$i], 0, ",", " ") ?> FCFA</td>
                                                <td>
                                                    <div class="progress" style="margin-bottom: 0; height: 20px;">
                                                        <div class="progress-bar
                                                        <?= $status_labels[$i] == 'Payé' ? 'progress-bar-success' :
                                                            ($status_labels[$i] == 'Partiellement payé' ? 'progress-bar-warning' : 'progress-bar-danger') ?>"
                                                             role="progressbar"
                                                             style="width: <?= $pourcentage ?>%"
                                                             aria-valuenow="<?= $pourcentage ?>"
                                                             aria-valuemin="0"
                                                             aria-valuemax="100">
                                                            <?= $pourcentage ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endfor; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Message si pas de données -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-default">
                        <div class="box-body text-center" style="padding: 40px;">
                            <i class="fa fa-database fa-4x text-muted" style="margin-bottom: 20px;"></i>
                            <h3>Aucune donnée disponible</h3>
                            <p class="text-muted">Aucune facture n'a été enregistrée pour l'année <?= $annee_en_cours ?>.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Vérifier que Chart.js est chargé
    if (typeof Chart !== 'undefined') {
        console.log('Chart.js chargé avec succès');

        // 1. CAMEMBOUR PRINCIPAL : CA Total vs Encaissé vs Reste
        <?php if ($has_any_data): ?>
        try {
            new Chart(document.getElementById('chartRepartitionCA').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: [
                        'CA Total (100%)',
                        'Déjà encaissé (<?= $pourcentage_encaisse ?>%)',
                        'Reste à encaisser (<?= $pourcentage_reste ?>%)'
                    ],
                    datasets: [{
                        data: [<?= $ca_realise ?>, <?= $ca_encaisse ?>, <?= $creance ?>],
                        backgroundColor: [
                            'rgba(0, 115, 183, 0.7)',  // Bleu pour CA Total
                            'rgba(40, 167, 69, 0.8)',  // Vert pour encaissé
                            'rgba(220, 53, 69, 0.8)'   // Rouge pour reste
                        ],
                        borderColor: [
                            'rgb(0, 115, 183)',
                            'rgb(40, 167, 69)',
                            'rgb(220, 53, 69)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            const value = data.datasets[0].data[i];
                                            return {
                                                text: label + ' - ' + value.toLocaleString('fr-FR') + ' FCFA',
                                                fillStyle: data.datasets[0].backgroundColor[i],
                                                strokeStyle: data.datasets[0].borderColor[i],
                                                lineWidth: 2,
                                                hidden: false,
                                                index: i
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label.split('(')[0].trim();
                                    const value = context.raw;
                                    const total = <?= $ca_realise ?>;
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value.toLocaleString('fr-FR')} FCFA (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
            console.log('Graphique CA Total vs Encaissé vs Reste créé');
        } catch (e) {
            console.error('Erreur création graphique principal:', e);
        }
        <?php endif; ?>

        // 2. Top 5 clients par créance - Version camembert avec pourcentages
        <?php if ($has_top_clients): ?>
        try {
            new Chart(document.getElementById('chartTopClientsCreance').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($top_clients) ?>,
                    datasets: [{
                        data: <?= json_encode($creance_clients) ?>,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)'
                        ],
                        borderColor: [
                            'rgb(255, 99, 132)',
                            'rgb(54, 162, 235)',
                            'rgb(255, 206, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(153, 102, 255)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            const value = data.datasets[0].data[i];
                                            const percentage = <?= json_encode($top_clients_percentages) ?>[i];
                                            return {
                                                text: `${label}: ${percentage}% (${value.toLocaleString('fr-FR')} FCFA)`,
                                                fillStyle: data.datasets[0].backgroundColor[i],
                                                strokeStyle: data.datasets[0].borderColor[i],
                                                lineWidth: 2,
                                                hidden: false,
                                                index: i
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw;
                                    const percentage = <?= json_encode($top_clients_percentages) ?>[context.dataIndex];
                                    return `${label}: ${value.toLocaleString('fr-FR')} FCFA (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
            console.log('Graphique Top Clients (camembert) créé');
        } catch (e) {
            console.error('Erreur création graphique Top Clients:', e);
        }
        <?php endif; ?>
    } else {
        console.error('Chart.js non chargé');
    }

    // Auto-refresh toutes les 60 secondes
    //setTimeout(function() {
      //  location.reload();
    //}, 60000);
</script>
</body>
</html>