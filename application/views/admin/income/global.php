<?php
// ===============================
// Connexion BDD
// ===============================
$CI = &get_instance();    

// Récupération de l'entreprise connectée
$userdata = $this->customlib->getUserData();
$entreprise_id = $userdata['entreprise_id'] ?? 0;

// Connexion avec les paramètres de CodeIgniter
$conn = new mysqli($CI->db->hostname, $CI->db->username, $CI->db->password, $CI->db->database);

// Vérification
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

// Activer les erreurs (optionnel)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ===============================
// FILTRE PÉRIODE
// ===============================
$date_debut = $_GET['date_debut'] ?? null;
$date_fin   = $_GET['date_fin'] ?? null;

if (!$date_debut && !$date_fin) {
    $date_debut = date('Y-m-01');
    $date_fin = date('Y-m-t');
}

if ($date_debut && $date_fin && strtotime($date_debut) > strtotime($date_fin)) {
    $tmp = $date_debut;
    $date_debut = $date_fin;
    $date_fin = $tmp;
}

if ($date_debut && $date_fin) {
    $date_condition = "oc.date BETWEEN '$date_debut' AND '$date_fin'";
    $period_label = "Du ".date('d/m/Y',strtotime($date_debut))." au ".date('d/m/Y',strtotime($date_fin));
} elseif ($date_debut) {
    $date_condition = "oc.date >= '$date_debut'";
    $period_label = "À partir du ".date('d/m/Y',strtotime($date_debut));
} elseif ($date_fin) {
    $date_condition = "oc.date <= '$date_fin'";
    $period_label = "Jusqu'au ".date('d/m/Y',strtotime($date_fin));
} else {
    $date_condition = "1=1";
    $period_label = "Toutes périodes";
}

// ===============================
// VÉRIFICATION DES COLONNES ENTREPRISE_ID
// ===============================
function column_exists($conn, $table, $column) {
    $check = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return ($check && $check->num_rows > 0);
}

$has_entreprise_id = [];
$has_entreprise_id['income'] = column_exists($conn, 'income', 'entreprise_id');
$has_entreprise_id['operation_caisse'] = column_exists($conn, 'operation_caisse', 'entreprise_id');
$has_entreprise_id['bank'] = column_exists($conn, 'bank', 'entreprise_id');
$has_entreprise_id['banks'] = column_exists($conn, 'banks', 'entreprise_id');
$has_entreprise_id['transfert_caisse'] = column_exists($conn, 'transfert_caisse', 'entreprise_id');

// Construction des filtres entreprise
$filtre_entreprise_caisse = "";
$filtre_entreprise_operation = "";
$filtre_entreprise_bank = "";
$filtre_entreprise_banques = "";
$filtre_entreprise_transfert = "";

if ($entreprise_id > 0) {
    if ($has_entreprise_id['income']) {
        $filtre_entreprise_caisse = " AND income.entreprise_id = $entreprise_id";
    }
    if ($has_entreprise_id['operation_caisse']) {
        $filtre_entreprise_operation = " AND oc.entreprise_id = $entreprise_id";
    }
    if ($has_entreprise_id['bank']) {
        $filtre_entreprise_bank = " AND b.entreprise_id = $entreprise_id";
    }
    if ($has_entreprise_id['banks']) {
        $filtre_entreprise_banques = " AND banks.entreprise_id = $entreprise_id";
    }
    if ($has_entreprise_id['transfert_caisse']) {
        $filtre_entreprise_transfert = " AND tc.entreprise_id = $entreprise_id";
    }
}

// ===============================
// INFORMATIONS CAISSES (table income)
// ===============================
$sqlCaisses = "SELECT 
                id,
                name,
                montant,
                total_entrees,
                total_sorties,
                amount_re,
                is_locked,
                est_fermee,
                last_operation_date,
                date_fermeture,
                date_reouverture
               FROM income 
               WHERE est_actif = 1" . $filtre_entreprise_caisse . "
               ORDER BY created_at DESC";
$resultCaisses = $conn->query($sqlCaisses);

// Total des fonds en caisse
$sqlTotalCaisse = "SELECT SUM(COALESCE(amount_re, 0)) AS total_caisse 
                   FROM income 
                   WHERE (est_fermee = 0 OR est_fermee IS NULL)" . $filtre_entreprise_caisse;
$resTotalCaisse = $conn->query($sqlTotalCaisse);
$total_caisse = ($resTotalCaisse && ($row = $resTotalCaisse->fetch_assoc())) ? (float)$row['total_caisse'] : 0;

// ===============================
// OPÉRATIONS DE CAISSE (entrées/sorties) - AVEC FILTRE PÉRIODE ET ENTREPRISE
// ===============================
$sqlOperationsCaisse = "SELECT 
                         oc.*,
                         i.name as caisse_name
                        FROM operation_caisse oc
                        LEFT JOIN income i ON oc.caisse_id = i.id
                        WHERE " . $date_condition . "
                        AND (oc.designation NOT LIKE '%TRF%' 
                           AND oc.designation NOT LIKE '%transfert%'
                           AND oc.designation NOT LIKE '%Transfert%'
                           OR oc.designation IS NULL)" . $filtre_entreprise_operation . "
                        ORDER BY oc.date DESC 
                        LIMIT 100";
$resultOperationsCaisse = $conn->query($sqlOperationsCaisse);

// Totaux des entrées/sorties (hors transferts) avec filtre période
$sqlTotalEntreesCaisse = "SELECT SUM(COALESCE(oc.montant, 0)) AS total_entrees 
                          FROM operation_caisse oc
                          WHERE " . $date_condition . "
                          AND oc.type_operation = 'entrée'
                            AND (oc.designation NOT LIKE '%TRF%' 
                             AND oc.designation NOT LIKE '%transfert%'
                             AND oc.designation NOT LIKE '%Transfert%')" . $filtre_entreprise_operation;
$resEntreesCaisse = $conn->query($sqlTotalEntreesCaisse);
$total_entrees_caisse = ($resEntreesCaisse && ($row = $resEntreesCaisse->fetch_assoc())) ? (float)$row['total_entrees'] : 0;

$sqlTotalSortiesCaisse = "SELECT SUM(COALESCE(oc.montant, 0)) AS total_sorties 
                          FROM operation_caisse oc
                          WHERE " . $date_condition . "
                          AND oc.type_operation = 'sortie'
                            AND (oc.designation NOT LIKE '%TRF%' 
                             AND oc.designation NOT LIKE '%transfert%'
                             AND oc.designation NOT LIKE '%Transfert%')" . $filtre_entreprise_operation;
$resSortiesCaisse = $conn->query($sqlTotalSortiesCaisse);
$total_sorties_caisse = ($resSortiesCaisse && ($row = $resSortiesCaisse->fetch_assoc())) ? (float)$row['total_sorties'] : 0;

// ===============================
// INFORMATIONS BANQUES
// ===============================
$sqlBanques = "SELECT 
                id,
                name,
                account_number,
                balance,
                currency,
                status,
                created_at
               FROM banks 
               WHERE status = 1" . $filtre_entreprise_banques . "
               ORDER BY created_at DESC";
$resultBanques = $conn->query($sqlBanques);

$sqlTotalBanque = "SELECT SUM(COALESCE(balance, 0)) AS total_banque 
                   FROM banks 
                   WHERE status = 1" . $filtre_entreprise_banques;
$resTotalBanque = $conn->query($sqlTotalBanque);
$total_banque = ($resTotalBanque && ($row = $resTotalBanque->fetch_assoc())) ? (float)$row['total_banque'] : 0;

// ===============================
// OPÉRATIONS BANCAIRES - AVEC FILTRE PÉRIODE
// ===============================
$date_condition_bank = str_replace('oc.date', 'b.date', $date_condition);

$sqlOperationsBanque = "SELECT 
                         b.*,
                         banks.name as bank_name
                        FROM bank b
                        LEFT JOIN banks ON b.bank_id = banks.id
                        WHERE " . $date_condition_bank . "
                        AND (b.is_active = 'yes' OR b.is_active IS NULL)" . $filtre_entreprise_bank . "
                        ORDER BY b.date DESC 
                        LIMIT 100";
$resultOperationsBanque = $conn->query($sqlOperationsBanque);

// ===============================
// TRANSFERTS CAISSE/BANQUE - AVEC FILTRE PÉRIODE ET ENTREPRISE
// ===============================
$date_condition_transfert = str_replace('oc.date', 'tc.date', $date_condition);

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
                   WHERE " . $date_condition_transfert . $filtre_entreprise_transfert . "
                   ORDER BY tc.date DESC 
                   LIMIT 50";
$resultTransferts = $conn->query($sqlTransferts);

// Totaux transferts (réapprovisionnements) avec filtre période
$sqlTotalCaisseVersBanque = "SELECT SUM(COALESCE(tc.amount, 0)) AS total 
                             FROM transfert_caisse tc
                             WHERE " . $date_condition_transfert . "
                             AND tc.from_type = 'caisse' AND tc.to_type = 'bank'" . $filtre_entreprise_transfert;
$resCaisseVersBanque = $conn->query($sqlTotalCaisseVersBanque);
$total_caisse_vers_banque = ($resCaisseVersBanque && ($row = $resCaisseVersBanque->fetch_assoc())) ? (float)$row['total'] : 0;

$sqlTotalBanqueVersCaisse = "SELECT SUM(COALESCE(tc.amount, 0)) AS total 
                             FROM transfert_caisse tc
                             WHERE " . $date_condition_transfert . "
                             AND tc.from_type = 'bank' AND tc.to_type = 'caisse'" . $filtre_entreprise_transfert;
$resBanqueVersCaisse = $conn->query($sqlTotalBanqueVersCaisse);
$total_banque_vers_caisse = ($resBanqueVersCaisse && ($row = $resBanqueVersCaisse->fetch_assoc())) ? (float)$row['total'] : 0;

// ===============================
// LIQUIDITÉ TOTALE
// ===============================
$liquidite_totale = $total_caisse + $total_banque;

// ===============================
// STATISTIQUES SUPPLÉMENTAIRES
// ===============================
$sqlNbCaissesActives = "SELECT COUNT(*) as nb FROM income WHERE (est_fermee = 0 OR est_fermee IS NULL)" . $filtre_entreprise_caisse;
$resNbCaissesActives = $conn->query($sqlNbCaissesActives);
$nb_caisses_actives = ($resNbCaissesActives && ($row = $resNbCaissesActives->fetch_assoc())) ? (int)$row['nb'] : 0;

$sqlNbBanquesActives = "SELECT COUNT(*) as nb FROM banks WHERE status = 1" . $filtre_entreprise_banques;
$resNbBanquesActives = $conn->query($sqlNbBanquesActives);
$nb_banques_actives = ($resNbBanquesActives && ($row = $resNbBanquesActives->fetch_assoc())) ? (int)$row['nb'] : 0;

// Dernière opération avec date actuelle
$date_condition_derniere = str_replace('oc.date', 'created_at', $date_condition);
$date_condition_derniere = str_replace('date BETWEEN', 'created_at BETWEEN', $date_condition_derniere);
$date_condition_derniere = str_replace('date >=', 'created_at >=', $date_condition_derniere);
$date_condition_derniere = str_replace('date <=', 'created_at <=', $date_condition_derniere);

$sqlDerniereOperation = "SELECT 
                          MAX(GREATEST(
                            COALESCE((SELECT MAX(oc.created_at) FROM operation_caisse oc WHERE " . str_replace('oc.date', 'oc.created_at', $date_condition) . $filtre_entreprise_operation . "), '2000-01-01'),
                            COALESCE((SELECT MAX(b.date) FROM bank b WHERE " . str_replace('oc.date', 'b.date', $date_condition) . $filtre_entreprise_bank . "), '2000-01-01'),
                            COALESCE((SELECT MAX(tc.date) FROM transfert_caisse tc WHERE " . str_replace('oc.date', 'tc.date', $date_condition) . $filtre_entreprise_transfert . "), '2000-01-01')
                          )) as derniere_operation";
$resDerniereOperation = $conn->query($sqlDerniereOperation);
$derniere_operation = ($resDerniereOperation && ($row = $resDerniereOperation->fetch_assoc())) ? $row['derniere_operation'] : 'N/A';

// ===============================
// STATISTIQUES PAR SEMAINE POUR GRAPHIQUE - AVEC FILTRE PÉRIODE
// ===============================
$date_condition_stats = str_replace('oc.date', 'b.date', $date_condition);

$sqlStatsSemaine = "SELECT 
                    YEARWEEK(b.date, 1) as annee_semaine,
                    MIN(DATE(b.date)) as debut_semaine,
                    MAX(DATE(b.date)) as fin_semaine,
                    SUM(CASE WHEN b.transaction_type IN ('Dépôt', 'dépôt', 'Crédit', 'Virement entrant') THEN b.amount ELSE 0 END) as depots,
                    SUM(CASE WHEN b.transaction_type IN ('Retrait', 'retrait', 'Débit', 'Virement sortant', 'Frais bancaires', 'Chèque', 'Prélèvement') THEN b.amount ELSE 0 END) as retraits,
                    SUM(CASE WHEN b.transaction_type = 'Frais bancaires' THEN b.amount ELSE 0 END) as frais,
                    SUM(CASE WHEN b.transaction_type IN ('Virement entrant', 'Virement sortant') THEN b.amount ELSE 0 END) as virements,
                    SUM(CASE WHEN b.transaction_type = 'Chèque' THEN b.amount ELSE 0 END) as cheques
                 FROM bank b
                 WHERE " . $date_condition_stats . "
                 AND (b.is_active = 'yes' OR b.is_active IS NULL)" . $filtre_entreprise_bank . "
                 GROUP BY YEARWEEK(b.date, 1)
                 ORDER BY annee_semaine ASC";
$resultStatsSemaine = $conn->query($sqlStatsSemaine);

$dates_semaines = [];
$depots_semaines = [];
$retraits_semaines = [];
$frais_semaines = [];
$virements_semaines = [];
$cheques_semaines = [];

if ($resultStatsSemaine && $resultStatsSemaine->num_rows > 0) {
    while ($row = $resultStatsSemaine->fetch_assoc()) {
        $debut = date('d', strtotime($row['debut_semaine']));
        $fin = date('d', strtotime($row['fin_semaine']));
        $mois = date('M', strtotime($row['debut_semaine']));
        $dates_semaines[] = "Sem $debut-$fin $mois";

        $depots_semaines[] = (float)$row['depots'];
        $retraits_semaines[] = (float)$row['retraits'];
        $frais_semaines[] = (float)$row['frais'];
        $virements_semaines[] = (float)$row['virements'];
        $cheques_semaines[] = (float)$row['cheques'];
    }
}

// ===============================
// STATISTIQUES PAR TYPE DE TRANSACTION - AVEC FILTRE PÉRIODE
// ===============================
$sqlStatsTypes = "SELECT 
                    b.transaction_type,
                    COUNT(*) as nombre_operations,
                    SUM(COALESCE(b.amount, 0)) as total_montant
                 FROM bank b
                 WHERE " . $date_condition_stats . "
                 AND (b.is_active = 'yes' OR b.is_active IS NULL)" . $filtre_entreprise_bank . "
                 GROUP BY b.transaction_type
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

// Totaux par type avec filtre période
$sqlTotauxTypes = "SELECT 
                    SUM(CASE WHEN b.transaction_type IN ('Dépôt', 'dépôt', 'Crédit', 'Virement entrant') THEN COALESCE(b.amount, 0) ELSE 0 END) as total_depots,
                    SUM(CASE WHEN b.transaction_type IN ('Retrait', 'retrait', 'Débit', 'Virement sortant') THEN COALESCE(b.amount, 0) ELSE 0 END) as total_retraits,
                    SUM(CASE WHEN b.transaction_type = 'Frais bancaires' THEN COALESCE(b.amount, 0) ELSE 0 END) as total_frais,
                    SUM(CASE WHEN b.transaction_type = 'Chèque' THEN COALESCE(b.amount, 0) ELSE 0 END) as total_cheques,
                    SUM(CASE WHEN b.transaction_type = 'Prélèvement' THEN COALESCE(b.amount, 0) ELSE 0 END) as total_prelevements
                 FROM bank b
                 WHERE " . $date_condition_stats . "
                 AND (b.is_active = 'yes' OR b.is_active IS NULL)" . $filtre_entreprise_bank;
$resTotauxTypes = $conn->query($sqlTotauxTypes);
$totaux_types = $resTotauxTypes ? $resTotauxTypes->fetch_assoc() : [];

// ===============================
// DATES POUR AFFICHAGE
// ===============================
$annee_en_cours = date('Y');
$mois_en_cours = date('F');
$date_du_jour = date('d/m/Y');
$semaine_en_cours = date('W');
$jour_semaine = date('l');

// Construction du filtre de période pour l'affichage des titres
$period_title = "";
if ($date_debut && $date_fin) {
    $period_title = " - " . date('d/m/Y', strtotime($date_debut)) . " au " . date('d/m/Y', strtotime($date_fin));
} elseif ($date_debut) {
    $period_title = " - À partir du " . date('d/m/Y', strtotime($date_debut));
} elseif ($date_fin) {
    $period_title = " - Jusqu'au " . date('d/m/Y', strtotime($date_fin));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord financier <?= $period_title ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .content-wrapper { padding:20px; background:#f4f6f9; }
        .small-box { border-radius: 10px; color:#fff; padding:20px; position:relative; overflow:hidden; margin-bottom:20px;}
        .small-box .icon { position:absolute; top:10px; right:10px; font-size:30px; opacity:0.3; }
        .box { border-radius:10px; box-shadow:0 3px 6px rgba(0,0,0,0.1); margin-bottom:20px; }
        .bg-aqua { background: #00c0ef !important; }
        .bg-green { background: #00a65a !important; }
        .bg-yellow { background: #f39c12 !important; }
        .bg-red { background: #dd4b39 !important; }
        .bg-purple { background: #605ca8 !important; }
        .bg-teal { background: #39cccc !important; }
        .bg-maroon { background: #d81b60 !important; }
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
        .chart-container { position: relative; height: 300px; width: 100%; }

        .account-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .account-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.12);
        }
        .account-card.caisse { border-left: 4px solid #17a2b8; }
        .account-card.bank { border-left: 4px solid #28a745; }
        .account-card.locked { border-left: 4px solid #ffc107; opacity: 0.8; }
        .account-card.closed { border-left: 4px solid #6c757d; opacity: 0.7; }

        .account-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }
        .account-name {
            font-weight: 600;
            font-size: 15px;
            color: #333;
            margin: 0;
        }
        .account-balance {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            margin: 6px 0 0 0;
            text-align: right;
        }
        .account-balance.caisse { color: #1a2639; }
        .account-balance.bank { color: #1a2639; }
        .account-details {
            font-size: 12px;
            color: #666;
            margin-top: 2px;
        }
        .account-status {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .account-status.status-active { background: #d5f5e3; color: #1e8449; }
        .account-status.status-locked { background: #fdebd0; color: #b9770e; }
        .account-status.status-closed { background: #eaecee; color: #5d6d7e; }
        .account-status.status-inactive { background: #fadbd8; color: #922b21; }

        .account-icon {
            font-size: 20px;
            color: #666;
            margin-right: 10px;
            min-width: 24px;
            text-align: center;
        }
        .account-icon.caisse { color: #17a2b8; }
        .account-icon.bank { color: #28a745; }

        /* Layout amélioré pour les caisses et banques */
        .account-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        @media (max-width: 768px) {
            .account-list {
                grid-template-columns: 1fr;
            }
        }

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
        .date-value.year { color: #dc3545; }
        .date-value.month { color: #28a745; }
        .date-value.day { color: #17a2b8; }
        .date-value.week { color: #ffc107; }

        .small-box {
            border-radius: 16px;
            background: #ffffff !important;
            border: 1px solid #e9ecef;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            padding: 20px 20px 16px 20px;
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
            transition: all 0.2s ease;
        }
        .small-box:hover {
            box-shadow: 0 6px 14px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }
        .small-box .inner {
            color: #1e2a3e !important;
            padding-right: 50px;
        }
        .small-box .inner h3 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: #0a1c2f;
            letter-spacing: -0.2px;
        }
        .small-box .inner p {
            font-size: 16px;
            font-weight: 500;
            margin: 0 0 6px 0;
            color: #2c3e50;
        }
        .small-box .inner small {
            font-size: 12px;
            color: #6c7a8a;
            font-weight: 400;
            display: inline-block;
            margin-top: 4px;
        }
        .small-box .icon {
            position: absolute;
            top: 18px;
            right: 7px;
            font-size: 30px;
            transition: all 0.2s;
            opacity: 0.75;
        }
        .small-box .icon i {
            background: transparent !important;
            text-shadow: none;
        }
        .small-box .icon .fa-money { color: #2ecc71; }
        .small-box .icon .fa-briefcase { color: #1abc9c; }
        .small-box .icon .fa-bank { color: #3498db; }
        .small-box .icon .fa-random { color: #9b59b6; }
        .bg-aqua, .bg-green, .bg-yellow, .bg-purple, .bg-red, .bg-teal, .bg-maroon {
            background-color: transparent !important;
        }
        @media (max-width: 768px) {
            .small-box .inner h3 { font-size: 22px; }
            .small-box .icon { font-size: 40px; top: 14px; right: 14px; }
            .small-box { padding: 16px; }
        }
        .entreprise-badge {
            background: #273772;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }
        .entreprise-badge i { margin-right: 8px; }
        .filter-info {
            background: #f8f9fa;
            padding: 8px 15px;
            border-radius: 5px;
            margin-top: 10px;
            border-left: 4px solid #273772;
        }
        .filter-info i { color: #273772; margin-right: 5px; }

        .box-header .box-title {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .box-header .box-title .badge {
            font-size: 12px;
            padding: 4px 10px;
        }

        /* Styles pour les filtres */
        .filter-bar {
            background: #fff;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
        }
        .filter-bar .form-group {
            margin-right: 15px;
        }
        .filter-bar label {
            font-weight: 600;
            color: #495057;
            margin-right: 8px;
        }
        .filter-bar .form-control {
            display: inline-block;
            width: auto;
            min-width: 180px;
        }
        .filter-bar .btn {
            margin-right: 5px;
        }
        .period-label {
            display: inline-block;
            padding: 5px 15px;
            background: #e9ecef;
            border-radius: 20px;
            font-size: 14px;
            color: #495057;
            margin-top: 10px;
        }
        .period-label i {
            margin-right: 5px;
            color: #007bff;
        }
    </style>
</head>
<body>
<div class="content-wrapper">
    <!-- SECTION FILTRES -->
    <section class="content-header">
        <h1><i class="fa fa-money"></i> Tableau de bord financier</h1>
        
        <!-- Barre de filtres -->
        <div class="filter-bar">
            <form method="GET" action="" class="form-inline">
                <div class="form-group">
                    <label for="date_debut"><i class="fa fa-calendar"></i> Date début</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control" 
                           value="<?= htmlspecialchars($date_debut ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="date_fin"><i class="fa fa-calendar"></i> Date fin</label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control" 
                           value="<?= htmlspecialchars($date_fin ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-filter"></i> Filtrer
                </button>
                <a href="?" class="btn btn-default">
                    <i class="fa fa-refresh"></i> Réinitialiser
                </a>
                <span class="period-label">
                    <i class="fa fa-clock-o"></i> Période: <strong><?= $period_label ?></strong>
                </span>
            </form>
        </div>

        <?php if ($entreprise_id > 0): ?>
            <div class="entreprise-badge">
                <i class="fa fa-building"></i> Entreprise ID: <?= $entreprise_id ?>
            </div>
        <?php endif; ?>

        <div class="date-info">
            <div class="date-header"><i class="fa fa-info-circle"></i> Informations sur la période</div>
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
                    <div class="date-value week"><?= $semaine_en_cours ?> (S<?= $semaine_en_cours ?>)</div>
                </div>
                <div class="date-item">
                    <div class="date-label">Dernière activité</div>
                    <div class="date-value"><?= $derniere_operation != 'N/A' ? date('d/m/Y H:i', strtotime($derniere_operation)) : 'Aucune' ?></div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <!-- KPI FINANCIERS -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($liquidite_totale, 0, ",", " ") ?> FCFA</h3>
                        <p>Liquidité totale</p>
                        <small>Période: <?= $period_label ?></small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($total_caisse, 0, ",", " ") ?> FCFA</h3>
                        <p>Total en caisse</p>
                        <small>Période: <?= $period_label ?></small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-briefcase"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($total_banque, 0, ",", " ") ?> FCFA</h3>
                        <p>Total en banque</p>
                        <small>Période: <?= $period_label ?></small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-bank"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($total_caisse_vers_banque + $total_banque_vers_caisse, 0, ",", " ") ?> FCFA</h3>
                        <p>Réapprovisionnements (transferts)</p>
                        <small>Période: <?= $period_label ?></small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-random"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION CAISSES ET BANQUES -->
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
                            <?php $resultCaisses->data_seek(0); ?>
                            <div class="account-list">
                                <?php while($caisse = $resultCaisses->fetch_assoc()): ?>
                                    <?php
                                    $amount_re = (float)($caisse['amount_re'] ?? 0);
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
                                            <div style="display: flex; align-items: center; min-width:0;">
                                                <i class="fa fa-briefcase account-icon caisse"></i>
                                                <div style="min-width:0;">
                                                    <div class="account-name" style="word-break:break-word;"><?= htmlspecialchars($caisse['name'] ?? 'Caisse') ?></div>
                                                    <?php if ($caisse['last_operation_date']): ?>
                                                        <div class="account-details">
                                                            <small><i class="fa fa-clock-o"></i> <?= date('d/m H:i', strtotime($caisse['last_operation_date'])) ?></small>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <span class="account-status <?= $status_class ?>" style="white-space:nowrap;"><?= $status_text ?></span>
                                        </div>
                                        <div class="account-balance caisse">
                                            <?= number_format($amount_re, 0, ",", " ") ?> FCFA
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info" style="margin: 0;">
                                <i class="fa fa-info-circle"></i> Aucune caisse trouvée
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
                            <?php $resultBanques->data_seek(0); ?>
                            <div class="account-list">
                                <?php while($banque = $resultBanques->fetch_assoc()): ?>
                                    <?php
                                    $status = $banque['status'] ?? 0;
                                    $status_class = $status == 1 ? 'status-active' : 'status-inactive';
                                    $status_text = $status == 1 ? 'ACTIF' : 'INACTIF';
                                    ?>
                                    <div class="account-card bank">
                                        <div class="account-header">
                                            <div style="display: flex; align-items: center; min-width:0;">
                                                <i class="fa fa-bank account-icon bank"></i>
                                                <div style="min-width:0;">
                                                    <div class="account-name" style="word-break:break-word;"><?= htmlspecialchars($banque['name'] ?? 'Banque') ?></div>
                                                    <div class="account-details">
                                                        <small><i class="fa fa-calendar"></i> <?= date('d/m/Y', strtotime($banque['created_at'] ?? 'now')) ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="account-status <?= $status_class ?>" style="white-space:nowrap;"><?= $status_text ?></span>
                                        </div>
                                        <div class="account-balance bank">
                                            <?= number_format($banque['balance'] ?? 0, 0, ",", " ") ?> <?= $banque['currency'] ?? 'FCFA' ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info" style="margin: 0;">
                                <i class="fa fa-info-circle"></i> Aucun compte bancaire trouvé
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRAPHIQUES -->
        <?php if (!empty($dates_semaines)): ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="box box-info">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-line-chart"></i> Évolution des opérations bancaires <?= $period_title ?></h3>
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
                            <h3 class="box-title"><i class="fa fa-pie-chart"></i> Répartition par type <?= $period_title ?></h3>
                        </div>
                        <div class="box-body">
                            <div class="chart-container">
                                <canvas id="chartRepartitionTypes"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- STATISTIQUES PAR TYPE -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-list"></i> Statistiques par type de transaction <?= $period_title ?></h3>
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
                                            <td><span class="label label-<?= $badge_class ?>"><?= $types_transactions[$i] ?></span></td>
                                            <td><?= $nombre_operations[$i] ?></td>
                                            <td><?= number_format($total_par_type[$i], 0, ",", " ") ?> FCFA</td>
                                            <td>
                                                <div class="progress" style="margin-bottom: 0; height: 20px;">
                                                    <div class="progress-bar progress-bar-<?= $badge_class ?>"
                                                         role="progressbar"
                                                         style="width: <?= $pourcentage ?>%">
                                                        <?= number_format($pourcentage, 1) ?>%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endfor; ?>
                                    <tr style="background-color: #f9f9f9; font-weight: bold;">
                                        <td>TOTAL <?= $period_label ?></td>
                                        <td><?= array_sum($nombre_operations) ?></td>
                                        <td><?= number_format($total_general, 0, ",", " ") ?> FCFA</td>
                                        <td>100%</td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Aucune transaction bancaire pour la période sélectionnée</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OPÉRATIONS RÉCENTES -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-teal">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-exchange"></i> Dernières opérations de caisse <?= $period_title ?></h3>
                        <div class="box-tools pull-right">
                            <span class="label label-success">E: <?= number_format($total_entrees_caisse, 0, ",", " ") ?></span>
                            <span class="label label-danger">S: <?= number_format($total_sorties_caisse, 0, ",", " ") ?></span>
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
                                    <?php while($operation = $resultOperationsCaisse->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i', strtotime($operation['date'])) ?></td>
                                            <td><?= htmlspecialchars($operation['caisse_name'] ?? 'N/A') ?></td>
                                            <td><span class="label label-<?= $operation['type_operation'] == 'entrée' ? 'success' : 'danger' ?>"><?= htmlspecialchars($operation['type_operation']) ?></span></td>
                                            <td><?= number_format($operation['montant'], 0, ",", " ") ?> FCFA</td>
                                            <td><?= htmlspecialchars($operation['designation'] ?? 'N/A') ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Aucune opération de caisse pour la période sélectionnée</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="box box-maroon">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-random"></i> Derniers réapprovisionnements (transferts) <?= $period_title ?></h3>
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
                                    <?php while($transfert = $resultTransferts->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i', strtotime($transfert['date'])) ?></td>
                                            <td><span class="label label-<?= $transfert['from_type'] == 'caisse' ? 'info' : 'warning' ?>"><?= ucfirst($transfert['from_type']) ?> → <?= ucfirst($transfert['to_type']) ?></span></td>
                                            <td><?= number_format($transfert['amount'], 0, ",", " ") ?> FCFA</td>
                                            <td><?= htmlspecialchars($transfert['source_name'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($transfert['destination_name'] ?? 'N/A') ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Aucun transfert pour la période sélectionnée</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OPÉRATIONS BANCAIRES RÉCENTES -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-red">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-credit-card"></i> Opérations bancaires récentes <?= $period_title ?></h3>
                        <div class="box-tools pull-right">
                            <span class="label label-success">Dépôts: <?= number_format($totaux_types['total_depots'] ?? 0, 0, ",", " ") ?></span>
                            <span class="label label-danger">Retraits: <?= number_format($totaux_types['total_retraits'] ?? 0, 0, ",", " ") ?></span>
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
                                    <?php while($operation = $resultOperationsBanque->fetch_assoc()): ?>
                                        <?php
                                        $type = strtolower($operation['transaction_type']);
                                        if (in_array($type, ['dépôt', 'dépots', 'crédit', 'virement entrant'])) {
                                            $badge_class = 'success';
                                        } elseif (in_array($type, ['retrait', 'débit', 'virement sortant'])) {
                                            $badge_class = 'danger';
                                        } elseif ($type == 'frais bancaires') {
                                            $badge_class = 'warning';
                                        } elseif ($type == 'chèque') {
                                            $badge_class = 'info';
                                        } else {
                                            $badge_class = 'default';
                                        }
                                        ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i', strtotime($operation['date'])) ?></td>
                                            <td><span class="label label-<?= $badge_class ?>"><?= htmlspecialchars($operation['transaction_type']) ?></span></td>
                                            <td><?= number_format($operation['amount'], 0, ",", " ") ?> FCFA</td>
                                            <td><?= htmlspecialchars($operation['bank_name'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($operation['reference'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($operation['designation'] ?? 'N/A') ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Aucune opération bancaire pour la période sélectionnée</td>
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
    // Graphique des opérations bancaires par semaine
    <?php if (!empty($dates_semaines)): ?>
    new Chart(document.getElementById('chartOperationsBanque').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($dates_semaines) ?>,
            datasets: [
                {
                    label: 'Dépôts',
                    data: <?= json_encode($depots_semaines) ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: '#4BC0C0',
                    borderWidth: 1
                },
                {
                    label: 'Retraits',
                    data: <?= json_encode($retraits_semaines) ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: '#FF6384',
                    borderWidth: 1
                },
                {
                    label: 'Frais bancaires',
                    data: <?= json_encode($frais_semaines) ?>,
                    backgroundColor: 'rgba(255, 206, 86, 0.7)',
                    borderColor: '#FFCE56',
                    borderWidth: 1
                },
                {
                    label: 'Virements',
                    data: <?= json_encode($virements_semaines) ?>,
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
</script>
</body>
</html> 