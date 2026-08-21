<?php
// ===============================
// Connexion BDD
// ===============================
$conn = new mysqli("localhost", "root", "", "diago_test");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// ===============================
// INFORMATIONS CAISSES (table income)
// ===============================

// D'abord, voyons ce qu'il y a dans la table income
$sqlTest = "SHOW COLUMNS FROM income";
$resultTest = $conn->query($sqlTest);
$incomeColumns = [];
if ($resultTest) {
    while($row = $resultTest->fetch_assoc()) {
        $incomeColumns[] = $row['Field'];
    }
}

// Maintenant, récupérons les données avec les bons champs
$sqlCaisses = "SELECT 
                id,
                name,
                montant,
                total_entrees,
                total_sorties,
                solde_actuel,
                is_locked,
                est_fermee,
                last_operation_date,
                date_fermeture,
                date_reouverture,
                est_actif,
                amount_re
               FROM income 
               WHERE (est_actif = 1 OR est_actif IS NULL)
               ORDER BY created_at DESC";
$resultCaisses = $conn->query($sqlCaisses);

// Debug: Vérifier si des caisses sont trouvées
$caissesCount = $resultCaisses ? $resultCaisses->num_rows : 0;

// Total des fonds en caisse - différentes méthodes de calcul
$sqlTotalCaisse1 = "SELECT SUM(COALESCE(solde_actuel, 0)) AS total_caisse 
                   FROM income 
                   WHERE (est_fermee = 0 OR est_fermee IS NULL)";
$resTotalCaisse1 = $conn->query($sqlTotalCaisse1);
$total_caisse1 = ($resTotalCaisse1 && ($row = $resTotalCaisse1->fetch_assoc())) ? (float)$row['total_caisse'] : 0;

$sqlTotalCaisse2 = "SELECT SUM(COALESCE(amount_re, 0)) AS total_caisse 
                   FROM income 
                   WHERE (est_fermee = 0 OR est_fermee IS NULL)";
$resTotalCaisse2 = $conn->query($sqlTotalCaisse2);
$total_caisse2 = ($resTotalCaisse2 && ($row = $resTotalCaisse2->fetch_assoc())) ? (float)$row['total_caisse'] : 0;

// Utiliser la méthode qui donne le meilleur résultat
$total_caisse = ($total_caisse1 > 0) ? $total_caisse1 : $total_caisse2;

// ===============================
// OPÉRATIONS DE CAISSE (entrées/sorties) - table operation_caisse
// ===============================
$sqlOperationsCaisse = "SELECT 
                         oc.*,
                         i.name as caisse_name
                        FROM operation_caisse oc
                        LEFT JOIN income i ON oc.caisse_id = i.id
                        ORDER BY oc.created_at DESC 
                        LIMIT 100";
$resultOperationsCaisse = $conn->query($sqlOperationsCaisse);

// Total des entrées de caisse
$sqlTotalEntreesCaisse = "SELECT SUM(COALESCE(montant, 0)) AS total_entrees 
                          FROM operation_caisse 
                          WHERE type_operation = 'entrée'";
$resEntreesCaisse = $conn->query($sqlTotalEntreesCaisse);
$total_entrees_caisse = ($resEntreesCaisse && ($row = $resEntreesCaisse->fetch_assoc())) ? (float)$row['total_entrees'] : 0;

// Total des sorties de caisse
$sqlTotalSortiesCaisse = "SELECT SUM(COALESCE(montant, 0)) AS total_sorties 
                          FROM operation_caisse 
                          WHERE type_operation = 'sortie'";
$resSortiesCaisse = $conn->query($sqlTotalSortiesCaisse);
$total_sorties_caisse = ($resSortiesCaisse && ($row = $resSortiesCaisse->fetch_assoc())) ? (float)$row['total_sorties'] : 0;

// ===============================
// INFORMATIONS BANQUES (table banks - création des banques)
// ===============================

// Vérifier d'abord la structure de la table banks
$sqlTestBanks = "SHOW COLUMNS FROM banks";
$resultTestBanks = $conn->query($sqlTestBanks);
$banksColumns = [];
if ($resultTestBanks) {
    while($row = $resultTestBanks->fetch_assoc()) {
        $banksColumns[] = $row['Field'];
    }
}

// Récupérer toutes les banques
$sqlBanques = "SELECT 
                id,
                name,
                account_number,
                balance,
                currency,
                status,
                created_at
               FROM banks 
               WHERE status = 1 
               ORDER BY created_at DESC";
$resultBanques = $conn->query($sqlBanques);

// Total des fonds en banque
$sqlTotalBanque = "SELECT SUM(COALESCE(balance, 0)) AS total_banque 
                   FROM banks 
                   WHERE status = 1";
$resTotalBanque = $conn->query($sqlTotalBanque);
$total_banque = ($resTotalBanque && ($row = $resTotalBanque->fetch_assoc())) ? (float)$row['total_banque'] : 0;

// ===============================
// OPÉRATIONS BANCAIRES (table bank - opérations)
// ===============================
// Vérifier la structure de la table bank
$sqlTestBank = "SHOW COLUMNS FROM bank";
$resultTestBank = $conn->query($sqlTestBank);
$bankColumns = [];
if ($resultTestBank) {
    while($row = $resultTestBank->fetch_assoc()) {
        $bankColumns[] = $row['Field'];
    }
}

$sqlOperationsBanque = "SELECT 
                         b.*,
                         banks.name as bank_name
                        FROM bank b
                        LEFT JOIN banks ON b.bank_id = banks.id
                        WHERE b.is_active = 'yes' OR b.is_active IS NULL
                        ORDER BY b.created_at DESC 
                        LIMIT 100";
$resultOperationsBanque = $conn->query($sqlOperationsBanque);

// ===============================
// TRANSFERTS CAISSE/BANQUE (table transfert_caisse)
// ===============================
// Vérifier la structure de la table transfert_caisse
$sqlTestTransfert = "SHOW COLUMNS FROM transfert_caisse";
$resultTestTransfert = $conn->query($sqlTestTransfert);
$transfertColumns = [];
if ($resultTestTransfert) {
    while($row = $resultTestTransfert->fetch_assoc()) {
        $transfertColumns[] = $row['Field'];
    }
}

$sqlTransferts = "SELECT 
                    tc.*,
                    CASE 
                        WHEN tc.from_type = 'caisse' THEN inc_from.name
                        WHEN tc.from_type = 'bank' THEN banks_from.name
                        ELSE 'N/A'
                    END as source_name,
                    CASE 
                        WHEN tc.to_type = 'caisse' THEN inc_to.name
                        WHEN tc.to_type = 'bank' THEN banks_to.name
                        ELSE 'N/A'
                    END as destination_name
                   FROM transfert_caisse tc
                   LEFT JOIN income inc_from ON tc.from_type = 'caisse' AND tc.from_id = inc_from.id
                   LEFT JOIN banks banks_from ON tc.from_type = 'bank' AND tc.from_id = banks_from.id
                   LEFT JOIN income inc_to ON tc.to_type = 'caisse' AND tc.to_id = inc_to.id
                   LEFT JOIN banks banks_to ON tc.to_type = 'bank' AND tc.to_id = banks_to.id
                   ORDER BY tc.date DESC 
                   LIMIT 50";
$resultTransferts = $conn->query($sqlTransferts);

// Total des transferts caisse vers banque
$sqlTotalCaisseVersBanque = "SELECT SUM(COALESCE(amount, 0)) AS total 
                             FROM transfert_caisse 
                             WHERE from_type = 'caisse' AND to_type = 'bank'";
$resCaisseVersBanque = $conn->query($sqlTotalCaisseVersBanque);
$total_caisse_vers_banque = ($resCaisseVersBanque && ($row = $resCaisseVersBanque->fetch_assoc())) ? (float)$row['total'] : 0;

// Total des transferts banque vers caisse
$sqlTotalBanqueVersCaisse = "SELECT SUM(COALESCE(amount, 0)) AS total 
                             FROM transfert_caisse 
                             WHERE from_type = 'bank' AND to_type = 'caisse'";
$resBanqueVersCaisse = $conn->query($sqlTotalBanqueVersCaisse);
$total_banque_vers_caisse = ($resBanqueVersCaisse && ($row = $resBanqueVersCaisse->fetch_assoc())) ? (float)$row['total'] : 0;

// ===============================
// LIQUIDITÉ TOTALE
// ===============================
$liquidite_totale = $total_caisse + $total_banque;

// ===============================
// STATISTIQUES SUPPLÉMENTAIRES
// ===============================

// Nombre de caisses actives
$sqlNbCaissesActives = "SELECT COUNT(*) as nb FROM income WHERE (est_fermee = 0 OR est_fermee IS NULL)";
$resNbCaissesActives = $conn->query($sqlNbCaissesActives);
$nb_caisses_actives = ($resNbCaissesActives && ($row = $resNbCaissesActives->fetch_assoc())) ? (int)$row['nb'] : 0;

// Nombre de banques actives
$sqlNbBanquesActives = "SELECT COUNT(*) as nb FROM banks WHERE status = 1";
$resNbBanquesActives = $conn->query($sqlNbBanquesActives);
$nb_banques_actives = ($resNbBanquesActives && ($row = $resNbBanquesActives->fetch_assoc())) ? (int)$row['nb'] : 0;

// Dernière opération
$sqlDerniereOperation = "SELECT 
                          MAX(GREATEST(
                            COALESCE((SELECT MAX(created_at) FROM operation_caisse), '2000-01-01'),
                            COALESCE((SELECT MAX(created_at) FROM bank), '2000-01-01'),
                            COALESCE((SELECT MAX(date) FROM transfert_caisse), '2000-01-01')
                          )) as derniere_operation";
$resDerniereOperation = $conn->query($sqlDerniereOperation);
$derniere_operation = ($resDerniereOperation && ($row = $resDerniereOperation->fetch_assoc())) ? $row['derniere_operation'] : 'N/A';

// ===============================
// STATISTIQUES PAR TYPE DE TRANSACTION
// ===============================

// Vérifier d'abord quels types de transaction existent
$sqlCheckTypes = "SELECT DISTINCT transaction_type FROM bank LIMIT 10";
$resultCheckTypes = $conn->query($sqlCheckTypes);
$existingTypes = [];
if ($resultCheckTypes) {
    while($row = $resultCheckTypes->fetch_assoc()) {
        $existingTypes[] = $row['transaction_type'];
    }
}

// Statistiques par jour (pour graphique)
$sqlStatsJour = "SELECT 
                    DATE(created_at) as date_jour,
                    SUM(CASE WHEN transaction_type IN ('Dépôt', 'dépôt', 'Crédit', 'Virement entrant') THEN amount ELSE 0 END) as depots,
                    SUM(CASE WHEN transaction_type IN ('Retrait', 'retrait', 'Débit', 'Virement sortant', 'Frais bancaires', 'Chèque', 'Prélèvement') THEN amount ELSE 0 END) as retraits,
                    SUM(CASE WHEN transaction_type = 'Frais bancaires' THEN amount ELSE 0 END) as frais,
                    SUM(CASE WHEN transaction_type IN ('Virement entrant', 'Virement sortant') THEN amount ELSE 0 END) as virements,
                    SUM(CASE WHEN transaction_type = 'Chèque' THEN amount ELSE 0 END) as cheques
                 FROM bank 
                 WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                 AND (is_active = 'yes' OR is_active IS NULL)
                 GROUP BY DATE(created_at)
                 ORDER BY date_jour ASC";
$resultStatsJour = $conn->query($sqlStatsJour);

$dates_jours = [];
$depots_jours = [];
$retraits_jours = [];
$frais_jours = [];
$virements_jours = [];
$cheques_jours = [];

if ($resultStatsJour) {
    while ($row = $resultStatsJour->fetch_assoc()) {
        $dates_jours[] = date('d/m', strtotime($row['date_jour']));
        $depots_jours[] = (float)$row['depots'];
        $retraits_jours[] = (float)$row['retraits'];
        $frais_jours[] = (float)$row['frais'];
        $virements_jours[] = (float)$row['virements'];
        $cheques_jours[] = (float)$row['cheques'];
    }
}

// Statistiques par type de transaction
$sqlStatsTypes = "SELECT 
                    transaction_type,
                    COUNT(*) as nombre_operations,
                    SUM(COALESCE(amount, 0)) as total_montant
                 FROM bank 
                 WHERE is_active = 'yes' OR is_active IS NULL
                 GROUP BY transaction_type
                 ORDER BY total_montant DESC";
$resultStatsTypes = $conn->query($sqlStatsTypes);

$types_transactions = [];
$nombre_operations = [];
$total_par_type = [];

if ($resultStatsTypes) {
    while ($row = $resultStatsTypes->fetch_assoc()) {
        $types_transactions[] = htmlspecialchars($row['transaction_type']);
        $nombre_operations[] = (int)$row['nombre_operations'];
        $total_par_type[] = (float)$row['total_montant'];
    }
}

// Totaux par type de transaction
$sqlTotauxTypes = "SELECT 
                    SUM(CASE WHEN transaction_type IN ('Dépôt', 'dépôt', 'Crédit', 'Virement entrant') THEN COALESCE(amount, 0) ELSE 0 END) as total_depots,
                    SUM(CASE WHEN transaction_type IN ('Retrait', 'retrait', 'Débit', 'Virement sortant') THEN COALESCE(amount, 0) ELSE 0 END) as total_retraits,
                    SUM(CASE WHEN transaction_type = 'Frais bancaires' THEN COALESCE(amount, 0) ELSE 0 END) as total_frais,
                    SUM(CASE WHEN transaction_type = 'Chèque' THEN COALESCE(amount, 0) ELSE 0 END) as total_cheques,
                    SUM(CASE WHEN transaction_type = 'Prélèvement' THEN COALESCE(amount, 0) ELSE 0 END) as total_prelevements
                 FROM bank 
                 WHERE is_active = 'yes' OR is_active IS NULL";
$resTotauxTypes = $conn->query($sqlTotauxTypes);
$totaux_types = $resTotauxTypes ? $resTotauxTypes->fetch_assoc() : [];

// Total des dépôts bancaires (pour compatibilité avec ancien code)
$total_depots_banque = $totaux_types['total_depots'] ?? 0;
$total_retraits_banque = $totaux_types['total_retraits'] ?? 0;

// Debug info
$debug_info = [
    'caisses_count' => $caissesCount,
    'banques_count' => $resultBanques ? $resultBanques->num_rows : 0,
    'operations_caisse_count' => $resultOperationsCaisse ? $resultOperationsCaisse->num_rows : 0,
    'operations_banque_count' => $resultOperationsBanque ? $resultOperationsBanque->num_rows : 0,
    'transferts_count' => $resultTransferts ? $resultTransferts->num_rows : 0,
    'total_caisse' => $total_caisse,
    'total_banque' => $total_banque,
    'existing_transaction_types' => $existingTypes
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord financier</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .content-wrapper { padding:20px; background:#f4f6f9; }
        .small-box { border-radius: 10px; color:#fff; padding:20px; position:relative; overflow:hidden; margin-bottom:20px;}
        .small-box .icon { position:absolute; top:10px; right:10px; font-size:40px; opacity:0.3; }
        .box { border-radius:10px; box-shadow:0 3px 6px rgba(0,0,0,0.1); margin-bottom:20px;}
        .bg-aqua { background: #00c0ef !important; }
        .bg-green { background: #00a65a !important; }
        .bg-yellow { background: #f39c12 !important; }
        .bg-red { background: #dd4b39 !important; }
        .bg-purple { background: #605ca8 !important; }
        .bg-teal { background: #39cccc !important; }
        .bg-maroon { background: #d81b60 !important; }
        .bg-navy { background: #001f3f !important; }
        .bg-orange { background: #ff851b !important; }
        .bg-olive { background: #3d9970 !important; }
        canvas { width:100% !important; height:300px !important; }
        .table-responsive { margin-top: 20px; }
        .status-badge { padding: 3px 8px; border-radius: 12px; font-size: 12px; }
        .status-active { background-color: #28a745; color: white; }
        .status-inactive { background-color: #dc3545; color: white; }
        .status-locked { background-color: #ffc107; color: #212529; }
        .status-closed { background-color: #6c757d; color: white; }
        .info-box { min-height: 90px; background: #fff; border-radius: 5px; padding: 15px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .info-box-icon { float: left; height: 60px; width: 60px; text-align: center; line-height: 60px; border-radius: 5px; color: #fff; font-size: 30px; }
        .info-box-content { margin-left: 70px; }
        .info-box-text { font-size: 14px; color: #666; }
        .info-box-number { font-weight: bold; font-size: 18px; margin-top: 5px; }
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        /* NOUVEAUX STYLES POUR LES CARTES DE CAISSES ET BANQUES */
        .account-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        .account-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.12);
        }
        .account-card.caisse {
            border-left: 4px solid #17a2b8;
        }
        .account-card.bank {
            border-left: 4px solid #28a745;
        }
        .account-card.locked {
            border-left: 4px solid #ffc107;
            opacity: 0.8;
        }
        .account-card.closed {
            border-left: 4px solid #6c757d;
            opacity: 0.7;
        }
        .account-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        .account-name {
            font-weight: 600;
            font-size: 16px;
            color: #333;
            margin: 0;
        }
        .account-balance {
            font-size: 22px;
            font-weight: 700;
            color: #2c3e50;
            margin: 10px 0;
            text-align: right;
        }
        .account-balance.caisse {
            color: #17a2b8;
        }
        .account-balance.bank {
            color: #28a745;
        }
        .account-details {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .account-details .detail-item {
            margin-bottom: 3px;
            display: flex;
            justify-content: space-between;
        }
        .account-details .label {
            font-weight: 500;
            color: #555;
        }
        .account-details .value {
            color: #333;
        }
        .account-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .account-icon {
            font-size: 24px;
            color: #666;
            margin-right: 10px;
        }
        .account-icon.caisse {
            color: #17a2b8;
        }
        .account-icon.bank {
            color: #28a745;
        }
        .debug-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
            font-size: 12px;
        }
        .debug-info h5 {
            margin-top: 0;
            color: #dc3545;
        }
    </style>
</head>
<body>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-money"></i> Tableau de bord financier</h1>
        <p class="text-muted">Dernière activité : <?= $derniere_operation != 'N/A' ? date('d/m/Y H:i', strtotime($derniere_operation)) : 'Aucune' ?></p>
    </section>

    <!-- Debug info (à enlever en production) -->
    

    <section class="content">
        <!-- NOUVELLE SECTION : Cartes des caisses et banques (PREMIÈRE LIGNE) -->
        <div class="row">
            <!-- Caisses -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">
                            <i class="fa fa-briefcase"></i> Caisses
                            <span class="badge bg-primary"><?= $nb_caisses_actives ?> active(s)</span>
                        </h3>
                    </div>
                    <div class="box-body">
                        <?php if ($resultCaisses && $resultCaisses->num_rows > 0): ?>
                            <?php
                            // Réinitialiser le pointeur du résultat
                            $resultCaisses->data_seek(0);
                            while($caisse = $resultCaisses->fetch_assoc()):
                                $montant = (float)($caisse['montant'] ?? 0);
                                $total_entrees = (float)($caisse['total_entrees'] ?? 0);
                                $total_sorties = (float)($caisse['total_sorties'] ?? 0);
                                $solde_actuel = (float)($caisse['solde_actuel'] ?? 0);

                                // Déterminer le statut
                                $est_fermee = $caisse['est_fermee'] ?? 0;
                                $is_locked = $caisse['is_locked'] ?? 0;

                                if ($est_fermee == 1) {
                                    $status_class = 'status-closed';
                                    $status_text = 'FERMÉE';
                                    $card_class = 'closed';
                                } elseif ($is_locked == 1) {
                                    $status_class = 'status-locked';
                                    $status_text = 'VERROUILLÉE';
                                    $card_class = 'locked';
                                } else {
                                    $status_class = 'status-active';
                                    $status_text = 'ACTIVE';
                                    $card_class = '';
                                }
                                ?>
                                <div class="account-card caisse <?= $card_class ?>">
                                    <div class="account-header">
                                        <div style="display: flex; align-items: center;">
                                            <i class="fa fa-briefcase account-icon caisse"></i>
                                            <div>
                                                <div class="account-name"><?= htmlspecialchars($caisse['name'] ?? 'Caisse sans nom') ?></div>
                                                <?php if ($caisse['last_operation_date']): ?>
                                                    <div class="account-details">
                                                        <small>Dernière opération: <?= date('d/m H:i', strtotime($caisse['last_operation_date'])) ?></small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <span class="account-status <?= $status_class ?>"><?= $status_text ?></span>
                                    </div>

                                    <div class="account-balance caisse">
                                        <?= number_format($solde_actuel, 0, ",", " ") ?> FCFA
                                    </div>

                                    <div class="account-details">
                                        <div class="detail-item">
                                            <span class="label">Solde initial:</span>
                                            <span class="value"><?= number_format($montant, 0, ",", " ") ?> FCFA</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="label" style="color: #28a745;">Entrées:</span>
                                            <span class="value" style="color: #28a745;"><?= number_format($total_entrees, 0, ",", " ") ?> FCFA</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="label" style="color: #dc3545;">Sorties:</span>
                                            <span class="value" style="color: #dc3545;"><?= number_format($total_sorties, 0, ",", " ") ?> FCFA</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="alert alert-info" style="margin: 0;">
                                <i class="fa fa-info-circle"></i> Aucune caisse trouvée dans la base de données
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Banques -->
            <div class="col-md-6">
                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title">
                            <i class="fa fa-bank"></i> Comptes bancaires
                            <span class="badge bg-success"><?= $nb_banques_actives ?> actif(s)</span>
                        </h3>
                    </div>
                    <div class="box-body">
                        <?php if ($resultBanques && $resultBanques->num_rows > 0): ?>
                            <?php
                            // Réinitialiser le pointeur du résultat
                            $resultBanques->data_seek(0);
                            while($banque = $resultBanques->fetch_assoc()):
                                $status = $banque['status'] ?? 0;
                                $status_class = $status == 1 ? 'status-active' : 'status-inactive';
                                $status_text = $status == 1 ? 'ACTIF' : 'INACTIF';
                                ?>
                                <div class="account-card bank">
                                    <div class="account-header">
                                        <div style="display: flex; align-items: center;">
                                            <i class="fa fa-bank account-icon bank"></i>
                                            <div>
                                                <div class="account-name"><?= htmlspecialchars($banque['name'] ?? 'Banque sans nom') ?></div>
                                                <div class="account-details">
                                                    <small>Créé le: <?= date('d/m/Y', strtotime($banque['created_at'] ?? 'now')) ?></small>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="account-status <?= $status_class ?>"><?= $status_text ?></span>
                                    </div>

                                    <div class="account-balance bank">
                                        <?= number_format($banque['balance'] ?? 0, 0, ",", " ") ?> <?= $banque['currency'] ?? 'FCFA' ?>
                                    </div>

                                    <div class="account-details">
                                        <div class="detail-item">
                                            <span class="label">N° compte:</span>
                                            <span class="value"><?= htmlspecialchars($banque['account_number'] ?? 'N/A') ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="label">Devise:</span>
                                            <span class="value"><?= htmlspecialchars($banque['currency'] ?? 'FCFA') ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="alert alert-info" style="margin: 0;">
                                <i class="fa fa-info-circle"></i> Aucun compte bancaire trouvé dans la base de données
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPIs Financiers - MISE À JOUR -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3><?= number_format($liquidite_totale, 0, ",", " ") ?> FCFA</h3>
                        <p>Liquidité totale</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?= number_format($total_caisse, 0, ",", " ") ?> FCFA</h3>
                        <p>Total en caisse</p>
                        <small><?= $nb_caisses_actives ?> caisse(s) active(s)</small>
                    </div>
                    <div class="icon"><i class="fa fa-briefcase"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><?= number_format($total_banque, 0, ",", " ") ?> FCFA</h3>
                        <p>Total en banque</p>
                        <small><?= $nb_banques_actives ?> compte(s) actif(s)</small>
                    </div>
                    <div class="icon"><i class="fa fa-bank"></i></div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-purple">
                    <div class="inner">
                        <h3><?= number_format($total_caisse_vers_banque + $total_banque_vers_caisse, 0, ",", " ") ?> FCFA</h3>
                        <p>Transferts totaux</p>
                        <small>C→B: <?= number_format($total_caisse_vers_banque, 0, ",", " ") ?> | B→C: <?= number_format($total_banque_vers_caisse, 0, ",", " ") ?></small>
                    </div>
                    <div class="icon"><i class="fa fa-random"></i></div>
                </div>
            </div>
        </div>

        <!-- Le reste du code HTML reste inchangé -->
        <!-- ... (le reste du code HTML) ... -->

        <!-- Graphiques - MISE À JOUR -->
        <?php if (!empty($dates_jours)): ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="box box-info">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-line-chart"></i> Évolution des opérations bancaires (7 derniers jours)</h3>
                        </div>
                        <div class="box-body">
                            <div class="chart-container">
                                <canvas id="chartOperationsBanque"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="box box-success">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-pie-chart"></i> Répartition par type</h3>
                        </div>
                        <div class="box-body">
                            <div class="chart-container">
                                <canvas id="chartRepartitionTypes"></canvas>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                    <tr>
                                        <td><span class="label" style="background-color: #4BC0C0;">&nbsp;&nbsp;&nbsp;</span> Dépôts</td>
                                        <td class="text-right"><?= number_format($totaux_types['total_depots'] ?? 0, 0, ",", " ") ?> FCFA</td>
                                    </tr>
                                    <tr>
                                        <td><span class="label" style="background-color: #FF6384;">&nbsp;&nbsp;&nbsp;</span> Retraits</td>
                                        <td class="text-right"><?= number_format($totaux_types['total_retraits'] ?? 0, 0, ",", " ") ?> FCFA</td>
                                    </tr>
                                    <tr>
                                        <td><span class="label" style="background-color: #FFCE56;">&nbsp;&nbsp;&nbsp;</span> Frais bancaires</td>
                                        <td class="text-right"><?= number_format($totaux_types['total_frais'] ?? 0, 0, ",", " ") ?> FCFA</td>
                                    </tr>
                                    <tr>
                                        <td><span class="label" style="background-color: #9966FF;">&nbsp;&nbsp;&nbsp;</span> Chèques</td>
                                        <td class="text-right"><?= number_format($totaux_types['total_cheques'] ?? 0, 0, ",", " ") ?> FCFA</td>
                                    </tr>
                                    <tr>
                                        <td><span class="label" style="background-color: #36A2EB;">&nbsp;&nbsp;&nbsp;</span> Prélèvements</td>
                                        <td class="text-right"><?= number_format($totaux_types['total_prelevements'] ?? 0, 0, ",", " ") ?> FCFA</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tableau détaillé des types de transactions - NOUVEAU -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-list"></i> Statistiques par type de transaction</h3>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Type de transaction</th>
                                    <th>Nombre d'opérations</th>
                                    <th>Total montant</th>
                                    <th>Pourcentage</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $total_general = array_sum($total_par_type);
                                if (!empty($types_transactions)):
                                    for($i = 0; $i < count($types_transactions); $i++):
                                        $pourcentage = $total_general > 0 ? ($total_par_type[$i] / $total_general * 100) : 0;
                                        $badge_class = '';
                                        $type = $types_transactions[$i];

                                        // Déterminer la couleur selon le type
                                        if (in_array(strtolower($type), ['dépôt', 'dépots', 'crédit', 'virement entrant'])) {
                                            $badge_class = 'success';
                                        } elseif (in_array(strtolower($type), ['retrait', 'débit', 'virement sortant'])) {
                                            $badge_class = 'danger';
                                        } elseif (strtolower($type) == 'frais bancaires') {
                                            $badge_class = 'warning';
                                        } else {
                                            $badge_class = 'info';
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <span class="label label-<?= $badge_class ?>">
                                                    <?= $types_transactions[$i] ?>
                                                </span>
                                            </td>
                                            <td><?= $nombre_operations[$i] ?></td>
                                            <td><?= number_format($total_par_type[$i], 0, ",", " ") ?> FCFA</td>
                                            <td>
                                                <div class="progress" style="margin-bottom: 0; height: 20px;">
                                                    <div class="progress-bar progress-bar-<?= $badge_class ?>"
                                                         role="progressbar"
                                                         style="width: <?= $pourcentage ?>%"
                                                         aria-valuenow="<?= $pourcentage ?>"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                        <?= number_format($pourcentage, 1) ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endfor; ?>
                                    <tr style="background-color: #f9f9f9; font-weight: bold;">
                                        <td>TOTAL</td>
                                        <td><?= array_sum($nombre_operations) ?></td>
                                        <td><?= number_format($total_general, 0, ",", " ") ?> FCFA</td>
                                        <td>100%</td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Aucune transaction bancaire</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Première ligne : Caisses et Banques -->
        <div class="row">
            <!-- Liste des caisses -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-briefcase"></i> Caisses (<?= $nb_caisses_actives ?> actives)</h3>
                        <div class="box-tools pull-right">
                            <span class="label label-primary">Total: <?= number_format($total_caisse, 0, ",", " ") ?> FCFA</span>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Solde initial</th>
                                    <th>Entrées</th>
                                    <th>Sorties</th>
                                    <th>Solde actuel</th>
                                    <th>Statut</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if ($resultCaisses && $resultCaisses->num_rows > 0): ?>
                                    <?php while($caisse = $resultCaisses->fetch_assoc()):
                                        $montant = (float)$caisse['montant'];
                                        $total_entrees = (float)$caisse['total_entrees'];
                                        $total_sorties = (float)$caisse['total_sorties'];
                                        $solde_actuel = (float)$caisse['solde_actuel'];

                                        // Déterminer le statut
                                        if ($caisse['est_fermee'] == 1) {
                                            $status_class = 'status-closed';
                                            $status_text = 'Fermée';
                                        } elseif ($caisse['is_locked'] == 1) {
                                            $status_class = 'status-locked';
                                            $status_text = 'Verrouillée';
                                        } else {
                                            $status_class = 'status-active';
                                            $status_text = 'Active';
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($caisse['name']) ?></strong>
                                                <?php if ($caisse['last_operation_date']): ?>
                                                    <br><small>Dernière op: <?= date('d/m H:i', strtotime($caisse['last_operation_date'])) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= number_format($montant, 0, ",", " ") ?> FCFA</td>
                                            <td><span class="label label-success"><?= number_format($total_entrees, 0, ",", " ") ?> FCFA</span></td>
                                            <td><span class="label label-danger"><?= number_format($total_sorties, 0, ",", " ") ?> FCFA</span></td>
                                            <td><span class="label label-primary"><?= number_format($solde_actuel, 0, ",", " ") ?> FCFA</span></td>
                                            <td><span class="status-badge <?= $status_class ?>"><?= $status_text ?></span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Aucune caisse créée</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des banques -->
            <div class="col-md-6">
                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-bank"></i> Comptes bancaires (<?= $nb_banques_actives ?> actifs)</h3>
                        <div class="box-tools pull-right">
                            <span class="label label-success">Total: <?= number_format($total_banque, 0, ",", " ") ?> FCFA</span>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Nom banque</th>
                                    <th>N° compte</th>
                                    <th>Solde</th>
                                    <th>Devise</th>
                                    <th>Statut</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if ($resultBanques && $resultBanques->num_rows > 0): ?>
                                    <?php while($banque = $resultBanques->fetch_assoc()):
                                        $status_class = $banque['status'] == 1 ? 'status-active' : 'status-inactive';
                                        $status_text = $banque['status'] == 1 ? 'Actif' : 'Inactif';
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($banque['name']) ?></strong>
                                                <br><small>Créé le: <?= date('d/m/Y', strtotime($banque['created_at'])) ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($banque['account_number']) ?></td>
                                            <td><span class="label label-primary"><?= number_format($banque['balance'], 0, ",", " ") ?> <?= $banque['currency'] ?></span></td>
                                            <td><?= htmlspecialchars($banque['currency']) ?></td>
                                            <td><span class="status-badge <?= $status_class ?>"><?= $status_text ?></span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Aucun compte bancaire créé</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deuxième ligne : Opérations et Transferts -->
        <div class="row">
            <!-- Opérations de caisse récentes -->
            <div class="col-md-6">
                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-exchange"></i> Dernières opérations de caisse</h3>
                        <div class="box-tools pull-right">
                            <div class="btn-group">
                                <span class="label label-success">E: <?= number_format($total_entrees_caisse, 0, ",", " ") ?></span>
                                <span class="label label-danger">S: <?= number_format($total_sorties_caisse, 0, ",", " ") ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Caisse</th>
                                    <th>Type</th>
                                    <th>Montant</th>
                                    <th>Description</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if ($resultOperationsCaisse && $resultOperationsCaisse->num_rows > 0): ?>
                                    <?php while($operation = $resultOperationsCaisse->fetch_assoc()):
                                        $badge_class = $operation['type_operation'] == 'entrée' ? 'success' : 'danger';
                                        ?>
                                        <tr>
                                            <td><?= date('d/m H:i', strtotime($operation['created_at'])) ?></td>
                                            <td><?= htmlspecialchars($operation['caisse_name'] ?? 'N/A') ?></td>
                                            <td><span class="label label-<?= $badge_class ?>"><?= htmlspecialchars($operation['type_operation']) ?></span></td>
                                            <td><?= number_format($operation['montant'], 0, ",", " ") ?> FCFA</td>
                                            <td><?= htmlspecialchars($operation['designation'] ?? 'N/A') ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Aucune opération de caisse</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transferts récents -->
            <div class="col-md-6">
                <div class="box box-danger">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-random"></i> Derniers transferts</h3>
                        <div class="box-tools pull-right">
                            <span class="label label-info">C→B: <?= number_format($total_caisse_vers_banque, 0, ",", " ") ?></span>
                            <span class="label label-warning">B→C: <?= number_format($total_banque_vers_caisse, 0, ",", " ") ?></span>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Montant</th>
                                    <th>Source</th>
                                    <th>Destination</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if ($resultTransferts && $resultTransferts->num_rows > 0): ?>
                                    <?php while($transfert = $resultTransferts->fetch_assoc()):
                                        $type_transfert = $transfert['from_type'] . '_vers_' . $transfert['to_type'];
                                        $badge_class = $transfert['from_type'] == 'caisse' ? 'info' : 'warning';
                                        $libelle_type = ucfirst($transfert['from_type']) . ' → ' . ucfirst($transfert['to_type']);
                                        ?>
                                        <tr>
                                            <td><?= date('d/m H:i', strtotime($transfert['date'])) ?></td>
                                            <td><span class="label label-<?= $badge_class ?>"><?= $libelle_type ?></span></td>
                                            <td><?= number_format($transfert['amount'], 0, ",", " ") ?> FCFA</td>
                                            <td><?= htmlspecialchars($transfert['source_name'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($transfert['destination_name'] ?? 'N/A') ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Aucun transfert enregistré</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Troisième ligne : Opérations bancaires -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-teal">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-credit-card"></i> Opérations bancaires récentes</h3>
                        <div class="box-tools pull-right">
                            <div class="btn-group">
                                <span class="label label-success">Dépôts: <?= number_format($totaux_types['total_depots'] ?? 0, 0, ",", " ") ?></span>
                                <span class="label label-danger">Retraits: <?= number_format($totaux_types['total_retraits'] ?? 0, 0, ",", " ") ?></span>
                                <span class="label label-primary">Net: <?= number_format(($totaux_types['total_depots'] ?? 0) - ($totaux_types['total_retraits'] ?? 0), 0, ",", " ") ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Montant</th>
                                    <th>Banque</th>
                                    <th>Référence</th>
                                    <th>Description</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if ($resultOperationsBanque && $resultOperationsBanque->num_rows > 0): ?>
                                    <?php while($operation = $resultOperationsBanque->fetch_assoc()):
                                        // Déterminer la classe du badge selon le type de transaction
                                        $type = strtolower($operation['transaction_type']);
                                        if (in_array($type, ['dépôt', 'dépots', 'crédit', 'virement entrant'])) {
                                            $badge_class = 'success';
                                        } elseif (in_array($type, ['retrait', 'débit', 'virement sortant'])) {
                                            $badge_class = 'danger';
                                        } elseif ($type == 'frais bancaires') {
                                            $badge_class = 'warning';
                                        } elseif ($type == 'chèque') {
                                            $badge_class = 'info';
                                        } elseif ($type == 'prélèvement') {
                                            $badge_class = 'primary';
                                        } else {
                                            $badge_class = 'default';
                                        }
                                        ?>
                                        <tr>
                                            <td><?= date('d/m H:i', strtotime($operation['created_at'])) ?></td>
                                            <td><span class="label label-<?= $badge_class ?>"><?= htmlspecialchars($operation['transaction_type']) ?></span></td>
                                            <td><?= number_format($operation['amount'], 0, ",", " ") ?> FCFA</td>
                                            <td><?= htmlspecialchars($operation['bank_name'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($operation['reference'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($operation['designation'] ?? 'N/A') ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Aucune opération bancaire</td>
                                    </tr>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
    // Graphique des opérations bancaires par jour
    <?php if (!empty($dates_jours)): ?>
    new Chart(document.getElementById('chartOperationsBanque').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($dates_jours) ?>,
            datasets: [
                {
                    label: 'Dépôts',
                    data: <?= json_encode($depots_jours) ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: '#4BC0C0',
                    borderWidth: 1
                },
                {
                    label: 'Retraits',
                    data: <?= json_encode($retraits_jours) ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: '#FF6384',
                    borderWidth: 1
                },
                {
                    label: 'Frais bancaires',
                    data: <?= json_encode($frais_jours) ?>,
                    backgroundColor: 'rgba(255, 206, 86, 0.7)',
                    borderColor: '#FFCE56',
                    borderWidth: 1
                },
                {
                    label: 'Virements',
                    data: <?= json_encode($virements_jours) ?>,
                    backgroundColor: 'rgba(153, 102, 255, 0.7)',
                    borderColor: '#9966FF',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Montant (FCFA)'
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('fr-FR') + ' FCFA';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.dataset.label || '';
                            const value = context.parsed.y;
                            return `${label}: ${value.toLocaleString('fr-FR')} FCFA`;
                        }
                    }
                }
            }
        }
    });

    // Graphique de répartition par type
    const typeLabels = ['Dépôts', 'Retraits', 'Frais bancaires', 'Chèques', 'Prélèvements'];
    const typeData = [
        <?= $totaux_types['total_depots'] ?? 0 ?>,
        <?= $totaux_types['total_retraits'] ?? 0 ?>,
        <?= $totaux_types['total_frais'] ?? 0 ?>,
        <?= $totaux_types['total_cheques'] ?? 0 ?>,
        <?= $totaux_types['total_prelevements'] ?? 0 ?>
    ];

    // Filtrer les types qui ont des données
    const filteredLabels = [];
    const filteredData = [];
    const backgroundColors = ['#4BC0C0', '#FF6384', '#FFCE56', '#9966FF', '#36A2EB'];

    typeData.forEach((value, index) => {
        if (value > 0) {
            filteredLabels.push(typeLabels[index]);
            filteredData.push(value);
        }
    });

    if (filteredData.length > 0) {
        new Chart(document.getElementById('chartRepartitionTypes').getContext('2d'), {
            type: 'pie',
            data: {
                labels: filteredLabels,
                datasets: [{
                    data: filteredData,
                    backgroundColor: backgroundColors.slice(0, filteredLabels.length),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = filteredData.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value.toLocaleString('fr-FR')} FCFA (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    <?php endif; ?>

    // Rafraîchissement automatique toutes les 60 secondes
    setTimeout(function() {
        location.reload();
    }, 60000);
</script>
<script>
    // Rafraîchissement automatique toutes les 60 secondes
    setTimeout(function() {
        location.reload();
    }, 60000);
</script>

</body>
</html>