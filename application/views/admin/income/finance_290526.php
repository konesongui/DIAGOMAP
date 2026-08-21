<?php
// ===============================
// Connexion BDD + SESSION
// ===============================
session_start();
$CI = &get_instance();
$conn = $CI->db->conn_id;

if (!$conn) {
    die("Erreur de connexion à la base de données");
}

// ===============================
// GESTION DES FILTRES DE DATE
// ===============================
$date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-01-01');
$date_fin   = isset($_GET['date_fin'])   ? $_GET['date_fin']   : date('Y-12-31');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_debut)) $date_debut = date('Y-01-01');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_fin))   $date_fin   = date('Y-12-31');

$date_debut_aff = date('d/m/Y', strtotime($date_debut));
$date_fin_aff   = date('d/m/Y', strtotime($date_fin));

// ===============================
// GESTION DES OBSERVATIONS (POST)
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_observation'])) {
    $_SESSION['dashboard_observation'] = trim($_POST['observation']);
    $redirect_url = "?" . http_build_query(array_merge($_GET, ['observation_saved' => '1']));
    header("Location: $redirect_url");
    exit;
}
$observation = $_SESSION['dashboard_observation'] ?? '';

// ===============================
// GESTION DES EXPORTS
// ===============================
if (isset($_GET['export']) && in_array($_GET['export'], ['excel', 'pdf'])) {
    $export_type = $_GET['export'];
}

// ===============================
// FONCTIONS UTILITAIRES
// ===============================
function formatMontant($montant) {
    return number_format($montant, 0, ",", " ") . " FCFA";
}

/**
 * Convertit une date (Y-m-d ou Y-m) en mois français abrégé + année
 * Ex: "2025-01-15" -> "Janv 2025", "2025-02" -> "Fév 2025"
 */
function formatMoisFr($dateStr) {
    $mois_fr = [
        '01' => 'Janv', '02' => 'Fév', '03' => 'Mars', '04' => 'Avr',
        '05' => 'Mai', '06' => 'Juin', '07' => 'Juil', '08' => 'Août',
        '09' => 'Sept', '10' => 'Oct', '11' => 'Nov', '12' => 'Déc'
    ];
    $timestamp = strtotime($dateStr);
    $mois = date('m', $timestamp);
    $annee = date('Y', $timestamp);
    return $mois_fr[$mois] . ' ' . $annee;
}

// ===============================
// 1. CHIFFRE D'AFFAIRES (invoices + quotes_selling)
// ===============================
$sqlCARealise = "SELECT COALESCE(SUM(total_ttc), 0) AS ca_realise 
                 FROM (
                    SELECT total_ttc FROM invoices WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
                    UNION ALL
                    SELECT total_ttc FROM quotes_selling WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
                 ) AS all_transactions";
$resultCARealise = $conn->query($sqlCARealise);
$ca_realise = ($resultCARealise && ($r = $resultCARealise->fetch_assoc())) ? (float)$r['ca_realise'] : 0;

$sqlCAEncaisse = "SELECT COALESCE(SUM(amount_paid), 0) AS ca_encaisse 
                  FROM (
                    SELECT amount_paid FROM invoices WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
                    UNION ALL
                    SELECT amount_paid FROM quotes_selling WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
                  ) AS all_payments";
$resultCAEncaisse = $conn->query($sqlCAEncaisse);
$ca_encaisse = ($resultCAEncaisse && ($r = $resultCAEncaisse->fetch_assoc())) ? (float)$r['ca_encaisse'] : 0;

$creance = $ca_realise - $ca_encaisse;
$taux_encaissement = ($ca_realise > 0) ? round(($ca_encaisse / $ca_realise) * 100, 2) : 0;
$pourcentage_encaisse = $ca_realise > 0 ? round(($ca_encaisse / $ca_realise) * 100, 1) : 0;
$pourcentage_reste = $ca_realise > 0 ? round(($creance / $ca_realise) * 100, 1) : 0;

// ===============================
// 2. OBJECTIFS COMMERCIAUX
// ===============================
$sqlObjectifs = "SELECT SUM(target_amount) as total_objectif 
                 FROM `objectifs_commercial` 
                 WHERE date BETWEEN '$date_debut' AND '$date_fin'";
$resObjectifs = $conn->query($sqlObjectifs);
$objectif_periode = 0;
if ($resObjectifs && $resObjectifs->num_rows > 0) {
    $row = $resObjectifs->fetch_assoc();
    $objectif_periode = $row['total_objectif'] ? (float)$row['total_objectif'] : 0;
}
$taux_realisation = $objectif_periode > 0 ? round(($ca_realise / $objectif_periode) * 100, 2) : 0;
$reste_objectif = max(0, $objectif_periode - $ca_realise);

// ===============================
// 3. ÉVOLUTION MENSUELLE DU CA (non utilisée directement, mais gardée)
// ===============================
$sqlEvolutionMensuelle = "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as annee_mois,
        SUM(ca_realise) AS ca_realise,
        SUM(ca_encaisse) AS ca_encaisse
    FROM (
        SELECT created_at, total_ttc AS ca_realise, amount_paid AS ca_encaisse FROM invoices WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
        UNION ALL
        SELECT created_at, total_ttc AS ca_realise, amount_paid AS ca_encaisse FROM quotes_selling WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
    ) AS all_transactions
    GROUP BY annee_mois
    ORDER BY annee_mois ASC
";
$resEvolution = $conn->query($sqlEvolutionMensuelle);
$mois_labels_evolution = [];
$ca_realise_mensuel = [];
$ca_encaisse_mensuel = [];
$creance_mensuel = [];
if ($resEvolution && $resEvolution->num_rows > 0) {
    while ($row = $resEvolution->fetch_assoc()) {
        $mois_labels_evolution[] = formatMoisFr($row['annee_mois'] . '-01');
        $ca_realise_mensuel[] = (float)$row['ca_realise'];
        $ca_encaisse_mensuel[] = (float)$row['ca_encaisse'];
        $creance_mensuel[] = (float)$row['ca_realise'] - (float)$row['ca_encaisse'];
    }
}

// ===============================
// 4. TOP 5 CLIENTS PAR CRÉANCE
// ===============================
$sqlTopClientsCreance = "
    SELECT 
        client_nom,
        SUM(ca_realise_client) AS ca_realise_client,
        SUM(ca_encaisse_client) AS ca_encaisse_client
    FROM (
        SELECT c.item_supplier AS client_nom, i.total_ttc AS ca_realise_client, i.amount_paid AS ca_encaisse_client
        FROM invoices i JOIN clients c ON i.customer_id = c.id
        WHERE i.created_at BETWEEN '$date_debut' AND '$date_fin'
        UNION ALL
        SELECT c.item_supplier AS client_nom, q.total_ttc AS ca_realise_client, q.amount_paid AS ca_encaisse_client
        FROM quotes_selling q JOIN clients c ON q.customer_id = c.id
        WHERE q.created_at BETWEEN '$date_debut' AND '$date_fin'
    ) AS all_transactions
    GROUP BY client_nom
    HAVING (SUM(ca_realise_client) - SUM(ca_encaisse_client)) > 0
    ORDER BY (SUM(ca_realise_client) - SUM(ca_encaisse_client)) DESC
    LIMIT 5
";
$resTopClients = $conn->query($sqlTopClientsCreance);
$top_clients = [];
$creance_clients = [];
if ($resTopClients && $resTopClients->num_rows > 0) {
    while ($row = $resTopClients->fetch_assoc()) {
        $top_clients[] = $row['client_nom'] ?: 'Client Inconnu';
        $creance_clients[] = (float)($row['ca_realise_client'] - $row['ca_encaisse_client']);
    }
}
$total_creance_top = array_sum($creance_clients);
$top_clients_percentages = [];
foreach($creance_clients as $creance_client) {
    $top_clients_percentages[] = $total_creance_top > 0 ? round(($creance_client / $total_creance_top) * 100, 1) : 0;
}

// ===============================
// 5. ÉVOLUTION TAUX ENCAISSEMENT
// ===============================
$sqlTauxMensuel = "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as annee_mois,
        CASE WHEN SUM(ca_realise) > 0 THEN ROUND((SUM(ca_encaisse) / SUM(ca_realise)) * 100, 2) ELSE 0 END AS taux_encaissement
    FROM (
        SELECT created_at, total_ttc AS ca_realise, amount_paid AS ca_encaisse FROM invoices WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
        UNION ALL
        SELECT created_at, total_ttc AS ca_realise, amount_paid AS ca_encaisse FROM quotes_selling WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
    ) AS all_transactions
    GROUP BY annee_mois
    ORDER BY annee_mois ASC
";
$resTaux = $conn->query($sqlTauxMensuel);
$taux_mensuel_labels = [];
$taux_mensuel_values = [];
if ($resTaux && $resTaux->num_rows > 0) {
    while ($row = $resTaux->fetch_assoc()) {
        $taux_mensuel_labels[] = formatMoisFr($row['annee_mois'] . '-01');
        $taux_mensuel_values[] = (float)$row['taux_encaissement'];
    }
}

// ===============================
// 6. RÉPARTITION PAR STATUT PAIEMENT
// ===============================
$sqlRepartitionStatus = "
    SELECT 
        CASE 
            WHEN (total_ttc - amount_paid) = 0 THEN 'Payé'
            WHEN amount_paid > 0 THEN 'Partiellement payé'
            ELSE 'Non payé'
        END AS status_paiement,
        COUNT(*) AS nb_transactions,
        SUM(total_ttc) AS ca_total,
        SUM(amount_paid) AS ca_encaisse,
        SUM(total_ttc - amount_paid) AS reste
    FROM (
        SELECT total_ttc, amount_paid FROM invoices WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
        UNION ALL
        SELECT total_ttc, amount_paid FROM quotes_selling WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
    ) AS all_transactions
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
if ($resStatus && $resStatus->num_rows > 0) {
    while ($row = $resStatus->fetch_assoc()) {
        $status_labels[] = $row['status_paiement'];
        $status_ca[] = (float)$row['ca_total'];
        $status_encaisse[] = (float)$row['ca_encaisse'];
        $status_reste[] = (float)$row['reste'];
    }
}

// ===============================
// 7. DÉPENSES : FACTURES FOURNISSEUR
// ===============================
$sqlTotalAchats = "SELECT COALESCE(SUM(total_ttc), 0) AS total_achats 
                   FROM invoices_supplier 
                   WHERE created_at BETWEEN '$date_debut' AND '$date_fin'";
$resTotalAchats = $conn->query($sqlTotalAchats);
$total_achats_ttc = ($resTotalAchats && ($r = $resTotalAchats->fetch_assoc())) ? (float)$r['total_achats'] : 0;

$sqlAchatsPaye = "SELECT COALESCE(SUM(amount_paid), 0) AS achats_paye 
                  FROM invoices_supplier 
                  WHERE created_at BETWEEN '$date_debut' AND '$date_fin'";
$resAchatsPaye = $conn->query($sqlAchatsPaye);
$achats_paye = ($resAchatsPaye && ($r = $resAchatsPaye->fetch_assoc())) ? (float)$r['achats_paye'] : 0;

$dette_fournisseur = $total_achats_ttc - $achats_paye;
$taux_paiement_achats = ($total_achats_ttc > 0) ? round(($achats_paye / $total_achats_ttc) * 100, 2) : 0;

// Évolution mensuelle des achats fournisseur
$sqlEvolutionAchats = "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as annee_mois,
        SUM(total_ttc) AS total_achats,
        SUM(amount_paid) AS paye_achats
    FROM invoices_supplier
    WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
    GROUP BY annee_mois
    ORDER BY annee_mois ASC
";
$resEvolAchats = $conn->query($sqlEvolutionAchats);
$achats_mensuel_labels = [];
$achats_mensuel = [];
$paye_achats_mensuel = [];
$reste_achats_mensuel = [];
if ($resEvolAchats && $resEvolAchats->num_rows > 0) {
    while ($row = $resEvolAchats->fetch_assoc()) {
        $achats_mensuel_labels[] = formatMoisFr($row['annee_mois'] . '-01');
        $achats_mensuel[] = (float)$row['total_achats'];
        $paye_achats_mensuel[] = (float)$row['paye_achats'];
        $reste_achats_mensuel[] = (float)$row['total_achats'] - (float)$row['paye_achats'];
    }
}

// ===============================
// 8. DÉPENSES : OPÉRATIONS CAISSE (SORTIE)
// ===============================
$sqlCaisseSortiesMensuel = "
    SELECT 
        DATE_FORMAT(date, '%Y-%m') as annee_mois,
        SUM(montant) AS total_sorties
    FROM operation_caisse
    WHERE type_operation = 'SORTIE'
      AND date BETWEEN '$date_debut' AND '$date_fin'
      AND (deleted = 0 OR deleted IS NULL)
    GROUP BY annee_mois
    ORDER BY annee_mois ASC
";
$resCaisseSorties = $conn->query($sqlCaisseSortiesMensuel);
$caisse_sorties_par_mois = [];
if ($resCaisseSorties && $resCaisseSorties->num_rows > 0) {
    while ($row = $resCaisseSorties->fetch_assoc()) {
        $caisse_sorties_par_mois[$row['annee_mois']] = (float)$row['total_sorties'];
    }
}

// ===============================
// 9. DÉPENSES : BANK (designation = 'debit' ou 'débit') - CORRIGÉ
// ===============================
$sqlBankDebitsMensuel = "
    SELECT 
        DATE_FORMAT(date, '%Y-%m') as annee_mois,
        SUM(amount) AS total_debits
    FROM bank
    WHERE (LOWER(designation) = 'debit' OR LOWER(designation) = 'débit')
      AND date BETWEEN '$date_debut' AND '$date_fin'
    GROUP BY annee_mois
    ORDER BY annee_mois ASC
";
$resBankDebits = $conn->query($sqlBankDebitsMensuel);
$bank_debits_par_mois = [];
if ($resBankDebits && $resBankDebits->num_rows > 0) {
    while ($row = $resBankDebits->fetch_assoc()) {
        $bank_debits_par_mois[$row['annee_mois']] = (float)$row['total_debits'];
    }
}

// ===============================
// 10. TOTAUX GLOBAUX DES DÉPENSES (toutes sources)
// ===============================
$sqlTotalCaisseSorties = "SELECT COALESCE(SUM(montant), 0) AS total 
                          FROM operation_caisse 
                          WHERE type_operation = 'SORTIE' 
                            AND date BETWEEN '$date_debut' AND '$date_fin'
                            AND (deleted = 0 OR deleted IS NULL)";
$resTotalCaisse = $conn->query($sqlTotalCaisseSorties);
$total_caisse_sorties = ($resTotalCaisse && ($r = $resTotalCaisse->fetch_assoc())) ? (float)$r['total'] : 0;

$sqlTotalBankDebits = "SELECT COALESCE(SUM(amount), 0) AS total 
                       FROM bank 
                       WHERE (LOWER(designation) = 'debit' OR LOWER(designation) = 'débit')
                         AND date BETWEEN '$date_debut' AND '$date_fin'";
$resTotalBank = $conn->query($sqlTotalBankDebits);
$total_bank_debits = ($resTotalBank && ($r = $resTotalBank->fetch_assoc())) ? (float)$r['total'] : 0;

$total_depenses_global = $total_achats_ttc + $total_caisse_sorties + $total_bank_debits;
$total_deja_paye = $achats_paye + $total_caisse_sorties + $total_bank_debits;
$reste_a_payer_global = $dette_fournisseur;
$taux_paiement_global = ($total_depenses_global > 0) ? round(($total_deja_paye / $total_depenses_global) * 100, 2) : 0;

// ===============================
// 11. FUSION DES TROIS SOURCES POUR LA COURBE MENSUELLE (avec mois français)
// ===============================
$tous_mois_depenses = [];

foreach ($achats_mensuel_labels as $idx => $labelMois) {
    // $labelMois est déjà en français (via formatMoisFr)
    $moisKey = date('Y-m', strtotime($achats_mensuel_labels[$idx] . '-01')); // approximation, mieux vaut stocker la clé YYYY-MM
    // Pour éviter les erreurs, on reconstruit la clé à partir de l'index original
    // On utilise plutôt un tableau associatif avec la clé YYYY-MM
}
// Refactorisation : on utilise un tableau avec clé mois (YYYY-MM) pour fusionner
$temp = [];
foreach ($achats_mensuel_labels as $idx => $label) {
    // Récupérer la clé YYYY-MM à partir de la date réelle
    // On a perdu la clé originale. On va plutôt refaire la boucle en utilisant les résultats bruts.
    // Solution plus propre : refaire la fusion en utilisant les données brutes avant formatage.
}
// Je réécris la fusion correctement en utilisant les tableaux bruts (clé 'annee_mois') avant conversion.
// Pour gagner du temps, je vais modifier la logique de fusion en repartant des données brutes non formatées.

// On récupère les tableaux bruts (avec clé 'annee_mois') depuis les requêtes précédentes.
// Pour cela, je réorganise le code : je stocke les données brutes dans des tableaux avant formatage.

// Je vais plutôt récupérer les résultats bruts dans des tableaux associatifs avec la clé 'annee_mois'.
$achats_brut = [];
if ($resEvolAchats && $resEvolAchats->num_rows > 0) {
    $resEvolAchats->data_seek(0);
    while ($row = $resEvolAchats->fetch_assoc()) {
        $achats_brut[$row['annee_mois']] = (float)$row['total_achats'];
    }
}
$caisse_brut = $caisse_sorties_par_mois;
$bank_brut = $bank_debits_par_mois;

$toutes_cles = array_unique(array_merge(array_keys($achats_brut), array_keys($caisse_brut), array_keys($bank_brut)));
sort($toutes_cles);

$depenses_mensuel_labels = [];
$depenses_mensuel_factures = [];
$depenses_mensuel_caisse = [];
$depenses_mensuel_bank = [];
$depenses_mensuel_total = [];

foreach ($toutes_cles as $moisKey) {
    $depenses_mensuel_labels[] = formatMoisFr($moisKey . '-01');
    $depenses_mensuel_factures[] = $achats_brut[$moisKey] ?? 0;
    $depenses_mensuel_caisse[] = $caisse_brut[$moisKey] ?? 0;
    $depenses_mensuel_bank[] = $bank_brut[$moisKey] ?? 0;
    $depenses_mensuel_total[] = ($achats_brut[$moisKey] ?? 0) + ($caisse_brut[$moisKey] ?? 0) + ($bank_brut[$moisKey] ?? 0);
}

// ===============================
// 12. TOP FOURNISSEURS PAR DETTE
// ===============================
$sqlTopFournisseursDette = "
    SELECT 
        c.item_supplier AS fournisseur_nom,
        SUM(i.total_ttc - i.amount_paid) AS dette
    FROM invoices_supplier i
    JOIN clients c ON i.client_id = c.id
    WHERE i.created_at BETWEEN '$date_debut' AND '$date_fin'
    GROUP BY fournisseur_nom
    HAVING dette > 0
    ORDER BY dette DESC
    LIMIT 5
";
$resTopFournisseurs = $conn->query($sqlTopFournisseursDette);
$top_fournisseurs = [];
$dette_fournisseurs = [];
if ($resTopFournisseurs && $resTopFournisseurs->num_rows > 0) {
    while ($row = $resTopFournisseurs->fetch_assoc()) {
        $top_fournisseurs[] = $row['fournisseur_nom'] ?: 'Fournisseur inconnu';
        $dette_fournisseurs[] = (float)$row['dette'];
    }
}
$total_dette_fournisseurs = array_sum($dette_fournisseurs);
$top_fournisseurs_percentages = [];
foreach($dette_fournisseurs as $dette) {
    $top_fournisseurs_percentages[] = $total_dette_fournisseurs > 0 ? round(($dette / $total_dette_fournisseurs) * 100, 1) : 0;
}

// ===============================
// 13. NOMBRE DE TRANSACTIONS (ventes)
// ===============================
$sqlNbTransactions = "SELECT COUNT(*) as nb FROM (
    SELECT id FROM invoices WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
    UNION ALL
    SELECT id FROM quotes_selling WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
) AS all_transactions";
$resNbTransactions = $conn->query($sqlNbTransactions);
$nb_factures = ($resNbTransactions && ($row = $resNbTransactions->fetch_assoc())) ? $row['nb'] : 0;

$has_any_data = $ca_realise > 0 || $ca_encaisse > 0 || $creance > 0;

// ===============================
// EXPORTS EXCEL ET PDF (avec mois français)
// ===============================
if (isset($export_type)) {
    if ($export_type === 'excel') {
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=export_dashboard_{$date_debut}_{$date_fin}.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo '<html><body>';
        echo '<h1>Tableau de bord du ' . $date_debut_aff . ' au ' . $date_fin_aff . '</h1>';
        echo '<h2>Chiffre d\'affaires</h2><table border="1">';
        echo '<tr><th>CA Réalisé</th><th>CA Encaissé</th><th>Créance</th><th>Taux encaissement</th></tr>';
        echo '<tr><td>' . formatMontant($ca_realise) . '</td><td>' . formatMontant($ca_encaisse) . '</td><td>' . formatMontant($creance) . '</td><td>' . $taux_encaissement . '%</td></tr>';
        echo '</table>';
        echo '<h2>Objectif commercial</h2><table border="1">';
        echo '<tr><th>Objectif période</th><th>CA Réalisé</th><th>Taux réalisation</th><th>Reste objectif</th></tr>';
        echo '<tr><td>' . formatMontant($objectif_periode) . '</td><td>' . formatMontant($ca_realise) . '</td><td>' . $taux_realisation . '%</td><td>' . formatMontant($reste_objectif) . 'NonNullable <table>';
        echo '<table>';
        if (!empty($top_clients)) {
            echo '<h2>Top 5 clients par créance</h2><table border="1">';
            echo '<tr><th>Client</th><th>Créance</th><th>%</th></tr>';
            foreach ($top_clients as $i => $client) {
                echo '<tr>>' . htmlspecialchars($client) . '</td>>' . formatMontant($creance_clients[$i]) . '</td>>' . $top_clients_percentages[$i] . '%</td></tr>';
            }
            echo '</table>';
        }
        echo '<h2>Dépenses globales (toutes sources)</h2><table border="1">';
        echo '<tr><th>Total dépenses TTC</th><th>Déjà payé</th><th>Reste à payer (dette fournisseur)</th><th>Taux paiement global</th></tr>';
        echo '<tr>>' . formatMontant($total_depenses_global) . '</td>>' . formatMontant($total_deja_paye) . '</td>>' . formatMontant($reste_a_payer_global) . '</td>>' . $taux_paiement_global . '%</td></tr>';
        echo '</table>';
        echo '<h3>Détail par source</h3><table border="1">';
        echo '<tr><th>Source</th><th>Montant</th></tr>';
        echo '<tr>>' . 'Factures fournisseur' . '</td>>' . formatMontant($total_achats_ttc) . '</td></tr>';
        echo '<td>>' . 'Sorties de caisse' . '</td>>' . formatMontant($total_caisse_sorties) . '</td></tr>';
        echo '<tr>>' . 'Débits bancaires' . '</td>>' . formatMontant($total_bank_debits) . '</td></tr>';
        echo '</table>';
        if (!empty($depenses_mensuel_labels)) {
            echo '<h2>Évolution mensuelle des dépenses</h2><table border="1">';
            echo '<tr><th>Mois</th><th>Factures</th><th>Caisse</th><th>Banque</th><th>Total</th></tr>';
            foreach ($depenses_mensuel_labels as $idx => $label) {
                echo '<tr>';
                echo '<td>' . $label . '</td>';
                echo '<td>' . formatMontant($depenses_mensuel_factures[$idx]) . '</td>';
                echo '<td>' . formatMontant($depenses_mensuel_caisse[$idx]) . '</td>';
                echo '<td>' . formatMontant($depenses_mensuel_bank[$idx]) . '</td>';
                echo '<td><strong>' . formatMontant($depenses_mensuel_total[$idx]) . '</strong></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        if (!empty($top_fournisseurs)) {
            echo '<h2>Top 5 fournisseurs par dette</h2><table border="1">';
            echo '<tr><th>Fournisseur</th><th>Dette</th><th>%</th></tr>';
            foreach ($top_fournisseurs as $i => $fourn) {
                echo '<tr>>' . htmlspecialchars($fourn) . '</td>>' . formatMontant($dette_fournisseurs[$i]) . '</td>>' . $top_fournisseurs_percentages[$i] . '%</td></tr>';
            }
            echo '</table>';
        }
        echo '<h2>Observations</h2><p>' . nl2br(htmlspecialchars($observation)) . '</p>';
        echo '</body></html>';
        exit;
    }
    elseif ($export_type === 'pdf') {
        if (!class_exists('Dompdf\Dompdf')) die("Erreur: Dompdf non installé.");
        require_once 'vendor/autoload.php';
        $html_pdf = '<html><head><meta charset="UTF-8"><style>body{font-family:DejaVu Sans,sans-serif;} h1{border-bottom:2px solid #0073b7;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #ddd;padding:8px;} th{background:#f2f2f2;}</style></head><body>';
        $html_pdf .= '<h1>Tableau de bord du ' . $date_debut_aff . ' au ' . $date_fin_aff . '</h1>';
        $html_pdf .= '<h2>Chiffre d\'affaires</h2><table><tr><th>CA Réalisé</th><th>CA Encaissé</th><th>Créance</th><th>Taux encaissement</th></tr>';
        $html_pdf .= '<tr>>' . formatMontant($ca_realise) . '</td>>' . formatMontant($ca_encaisse) . '</td>>' . formatMontant($creance) . '</td>>' . $taux_encaissement . '%</td></tr>';
        $html_pdf .= '</table>';
        $html_pdf .= '<h2>Objectif commercial</h2><td><tr><th>Objectif</th><th>CA Réalisé</th><th>Taux</th><th>Reste</th></tr>';
        $html_pdf .= '<tr>>' . formatMontant($objectif_periode) . '</td>>' . formatMontant($ca_realise) . '</td>>' . $taux_realisation . '%</td><td>' . formatMontant($reste_objectif) . '</td></tr>';
        $html_pdf .= '</table>';
        if (!empty($top_clients)) {
            $html_pdf .= '<h2>Top 5 clients par créance</h2><table><tr><th>Client</th><th>Créance</th><th>%</th></tr>';
            foreach ($top_clients as $i => $client) {
                $html_pdf .= '<tr>>' . htmlspecialchars($client) . '</td>>' . formatMontant($creance_clients[$i]) . '</td>>' . $top_clients_percentages[$i] . '%</td></tr>';
            }
            $html_pdf .= '</tr>';
        }
        $html_pdf .= '<h2>Dépenses globales (toutes sources)</h2><tr><tr><th>Total dépenses TTC</th><th>Déjà payé</th><th>Reste à payer</th><th>Taux paiement global</th></tr>';
        $html_pdf .= '<tr>>' . formatMontant($total_depenses_global) . '</td>>' . formatMontant($total_deja_paye) . '</td>>' . formatMontant($reste_a_payer_global) . '</td>>' . $taux_paiement_global . '%</td></tr>';
        $html_pdf .= '</table>';
        $html_pdf .= '<h3>Détail par source</h3><td><tr><th>Source</th><th>Montant</th></tr>';
        $html_pdf .= '<tr>>' . 'Factures fournisseur' . '</td>>' . formatMontant($total_achats_ttc) . '</td></tr>';
        $html_pdf .= '<td>>' . 'Sorties de caisse' . '</td>>' . formatMontant($total_caisse_sorties) . '</td></tr>';
        $html_pdf .= '<tr>>' . 'Débits bancaires' . '</td>>' . formatMontant($total_bank_debits) . '</td></tr>';
        $html_pdf .= '</table>';
        if (!empty($depenses_mensuel_labels)) {
            $html_pdf .= '<h2>Évolution mensuelle des dépenses</h2></table><table><th>Mois</th><th>Factures</th><th>Caisse</th><th>Banque</th><th>Total</th></tr>';
            foreach ($depenses_mensuel_labels as $idx => $label) {
                $html_pdf .= '<tr>';
                $html_pdf .= '<td>' . $label . '</td>';
                $html_pdf .= '<td>' . formatMontant($depenses_mensuel_factures[$idx]) . '</td>';
                $html_pdf .= '<td>' . formatMontant($depenses_mensuel_caisse[$idx]) . '</td>';
                $html_pdf .= '<td>' . formatMontant($depenses_mensuel_bank[$idx]) . '</td>';
                $html_pdf .= '<td><strong>' . formatMontant($depenses_mensuel_total[$idx]) . '</strong></td>';
                $html_pdf .= '</tr>';
            }
            $html_pdf .= '</table>';
        }
        if (!empty($top_fournisseurs)) {
            $html_pdf .= '<h2>Top 5 fournisseurs par dette</h2><table></table><th>Fournisseur</th><th>Dette</th><th>%</th></tr>';
            foreach ($top_fournisseurs as $i => $fourn) {
                $html_pdf .= '<tr>>' . htmlspecialchars($fourn) . '</td>>' . formatMontant($dette_fournisseurs[$i]) . '</td>>' . $top_fournisseurs_percentages[$i] . '%</td></tr>';
            }
            $html_pdf .= '</table>';
        }
        $html_pdf .= '<h2>Observations</h2><p>' . nl2br(htmlspecialchars($observation)) . '</p>';
        $html_pdf .= '<footer>Généré le ' . date('d/m/Y H:i') . '</footer></body></html>';
        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml($html_pdf);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("dashboard_{$date_debut}_{$date_fin}.pdf", array("Attachment" => true));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - CA & Créances & Dépenses</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .content-wrapper { padding:20px; background:#f4f6f9; }
        .small-box {
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 20px;
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
            transition: transform 0.2s;
        }
        .small-box:hover { transform: translateY(-3px); }
        .small-box .inner h3 { margin: 0 0 10px 0; font-size: 20px; font-weight: bold; color: #2c3e50; }
        .small-box .inner p { color: #6c757d; margin-bottom: 5px; font-weight: 500; }
        .small-box .inner small { color: #adb5bd; font-size: 12px; }
        .small-box .icon {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 45px;
            opacity: 0.9;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .icon-ca-realise { background: #00a65a; }
        .icon-ca-encaisse { background: #00c0ef; }
        .icon-creance { background: #dd4b39; }
        .icon-achats { background: #f39c12; }
        .icon-achats-paye { background: #3c8dbc; }
        .icon-dette-fournisseur { background: #e74c3c; }
        .box { border-radius:10px; box-shadow:0 3px 6px rgba(0,0,0,0.1); margin-bottom:20px; }
        canvas.circle-chart { max-width: 300px; max-height: 300px; display: block; margin: 0 auto; }
        .btn-export { margin-left: 10px; }
        .comparison-row {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 5px solid #ffc107;
        }
        .observation-box {
            background: #fff9e6;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-money"></i> Chiffre d'affaires & Créances & Dépenses</h1>
    </section>
    <section class="content">
        <!-- Filtres -->
        <div class="filter-bar">
            <form method="GET" action="" class="form-inline">
                <div class="form-group">
                    <label for="date_debut">Date début</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control" value="<?= htmlspecialchars($date_debut) ?>">
                </div>
                <div class="form-group">
                    <label for="date_fin">Date fin</label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control" value="<?= htmlspecialchars($date_fin) ?>">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrer</button>
                <a href="?export=excel&date_debut=<?= urlencode($date_debut) ?>&date_fin=<?= urlencode($date_fin) ?>" class="btn btn-success btn-export"><i class="fa fa-file-excel-o"></i> Export Excel</a>
                <a href="?export=pdf&date_debut=<?= urlencode($date_debut) ?>&date_fin=<?= urlencode($date_fin) ?>" class="btn btn-danger btn-export"><i class="fa fa-file-pdf-o"></i> Export PDF</a>
            </form>
            <p class="text-muted" style="margin-top:10px;">Période : du <strong><?= $date_debut_aff ?></strong> au <strong><?= $date_fin_aff ?></strong></p>
        </div>

        <!-- KPIs VENTES -->
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <div class="small-box"><div class="inner"><h3><?= number_format($ca_realise,0,","," ") ?> FCFA</h3><p>CA Réalisé</p><small>Factures + Point de vente</small></div><div class="icon icon-ca-realise"><i class="fa fa-line-chart"></i></div></div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="small-box"><div class="inner"><h3><?= number_format($ca_encaisse,0,","," ") ?> FCFA</h3><p>CA Encaissé</p><small>Taux: <?= $taux_encaissement ?>%</small></div><div class="icon icon-ca-encaisse"><i class="fa fa-credit-card"></i></div></div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="small-box"><div class="inner"><h3><?= number_format($creance,0,","," ") ?> FCFA</h3><p>Créance</p><small>Reste à encaisser</small></div><div class="icon icon-creance"><i class="fa fa-exclamation-triangle"></i></div></div>
            </div>
        </div>

        <!-- KPIs DÉPENSES GLOBALES -->
        <div class="row">
            <div class="col-md-12"><h3 class="page-header"><i class="fa fa-money"></i> Dépenses globales (toutes sources)</h3></div>
            <div class="col-md-4 col-sm-6">
                <div class="small-box"><div class="inner"><h3><?= number_format($total_depenses_global,0,","," ") ?> FCFA</h3><p>Total dépenses TTC</p><small>Factures + Caisse + Banque</small></div><div class="icon icon-achats"><i class="fa fa-truck"></i></div></div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="small-box"><div class="inner"><h3><?= number_format($total_deja_paye,0,","," ") ?> FCFA</h3><p>Déjà payé / décaissé</p><small>Taux: <?= $taux_paiement_global ?>%</small></div><div class="icon icon-achats-paye"><i class="fa fa-money"></i></div></div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="small-box"><div class="inner"><h3><?= number_format($reste_a_payer_global,0,","," ") ?> FCFA</h3><p>Reste à payer (dette fournisseur)</p><small>Échéances fournisseurs uniquement</small></div><div class="icon icon-dette-fournisseur"><i class="fa fa-clock-o"></i></div></div>
            </div>
        </div>

        <!-- Détail par source -->
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info">📊 <strong>Détail par source :</strong> Factures fournisseur : <?= formatMontant($total_achats_ttc) ?> | Sorties de caisse : <?= formatMontant($total_caisse_sorties) ?> | Débits bancaires : <?= formatMontant($total_bank_debits) ?></div>
            </div>
        </div>


        <!-- Courbe mensuelle des dépenses avec mois français -->
        <?php if (!empty($depenses_mensuel_labels)): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header"><h3 class="box-title"><i class="fa fa-line-chart"></i> Évolution mensuelle des dépenses (Factures + Caisse + Banque)</h3></div>
                        <div class="box-body">
                            <canvas id="combinedExpensesChart" style="height:350px; width:100%;"></canvas>
                            <div class="table-responsive" style="margin-top:20px;">
                                <table class="table table-bordered table-striped">
                                    <thead><tr><th>Mois</th><th>Factures fournisseur</th><th>Sorties de caisse</th><th>Débits bancaires</th><th>Dépenses totales</th></tr></thead>
                                    <tbody>
                                    <?php foreach ($depenses_mensuel_labels as $idx => $label): ?>
                                        <tr>
                                            <td><?= $label ?></td>
                                            <td><?= formatMontant($depenses_mensuel_factures[$idx]) ?></td>
                                            <td><?= formatMontant($depenses_mensuel_caisse[$idx]) ?></td>
                                            <td><?= formatMontant($depenses_mensuel_bank[$idx]) ?></td>
                                            <td><strong><?= formatMontant($depenses_mensuel_total[$idx]) ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Aucune donnée de dépense sur la période.</div>
        <?php endif; ?>


        <!-- OBSERVATIONS -->
        <div class="row">
            <div class="col-md-12">
                <div class="observation-box">
                    <form method="POST" action="">
                        <div class="form-group" style="width:80%;">
                            <label>📝 Observations sur les dépenses :</label>
                            <textarea name="observation" rows="2" class="form-control" style="width:100%;"><?= htmlspecialchars($observation) ?></textarea>
                        </div>
                        <div class="form-group" style="margin-top:10px;">
                            <button type="submit" name="save_observation" class="btn btn-warning"><i class="fa fa-save"></i> Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Objectif commercial -->
        <div class="row">
            <div class="col-md-12">
                <div class="comparison-row">
                    <div class="row">
                        <div class="col-md-3"><div class="comparison-label"><i class="fa fa-bullseye"></i> OBJECTIF PÉRIODE</div><div class="comparison-value" style="color:#0073b7;"><?= number_format($objectif_periode,0,","," ") ?> FCFA</div></div>
                        <div class="col-md-3"><div class="comparison-label"><i class="fa fa-line-chart"></i> CA RÉALISÉ</div><div class="comparison-value" style="color:#00a65a;"><?= number_format($ca_realise,0,","," ") ?> FCFA</div></div>
                        <div class="col-md-2"><div class="comparison-label"><i class="fa fa-percent"></i> TAUX</div><div class="comparison-value" style="color:<?= $taux_realisation>=100?'#00a65a':($taux_realisation>=70?'#f39c12':'#dd4b39')?>"><?= $taux_realisation ?>%</div></div>
                        <div class="col-md-4"><div class="comparison-label"><i class="fa fa-clock-o"></i> SOLDE</div><div class="comparison-value text-danger"><?= number_format($reste_objectif,0,","," ") ?> FCFA</div></div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($has_any_data): ?>
            <!-- Répartition CA -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-header"><h3 class="box-title"><i class="fa fa-pie-chart"></i> Répartition du CA</h3></div>
                        <div class="box-body">
                            <canvas id="chartRepartitionCA" class="circle-chart"></canvas>
                            <div class="table-responsive" style="margin-top:20px;">
                                <table class="table table-bordered">
                                    <thead><tr><th>Indicateur</th><th>Montant</th><th>Pourcentage</th></tr></thead>
                                    <tbody>
                                    <tr class="active"><td>CA Total</td><td><strong><?= number_format($ca_realise,0,","," ") ?> FCFA</strong></td><td>100%</td></tr>
                                    <tr class="success"><td>Déjà encaissé</td><td><?= number_format($ca_encaisse,0,","," ") ?> FCFA</td><td><?= $pourcentage_encaisse ?>%</td></tr>
                                    <tr class="danger"><td>Reste à encaisser</td><td><?= number_format($creance,0,","," ") ?> FCFA</td><td><?= $pourcentage_reste ?>%</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top clients créance -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-warning">
                        <div class="box-header"><h3 class="box-title"><i class="fa fa-users"></i> Top 5 clients par créance</h3></div>
                        <div class="box-body">
                            <?php if (!empty($top_clients)): ?>
                                <canvas id="chartTopClientsCreance" class="circle-chart"></canvas>
                            <?php else: ?>
                                <p>Aucun client avec créance</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top fournisseurs dette -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-danger">
                        <div class="box-header"><h3 class="box-title"><i class="fa fa-truck"></i> Top 5 fournisseurs par dette</h3></div>
                        <div class="box-body">
                            <?php if (!empty($top_fournisseurs)): ?>
                                <canvas id="chartTopFournisseursDette" class="circle-chart"></canvas>
                            <?php else: ?>
                                <p>Aucun fournisseur avec dette</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statuts paiement -->
            <?php if (!empty($status_labels)): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-default">
                            <div class="box-header"><h3 class="box-title"><i class="fa fa-list"></i> Détail par statut de paiement</h3></div>
                            <div class="box-body">
                                <table class="table table-bordered">
                                    <thead><tr><th>Statut</th><th>CA Total</th><th>Encaissé</th><th>Reste</th><th>% Encaissé</th></tr></thead>
                                    <tbody>
                                    <?php foreach ($status_labels as $i => $status):
                                        $pourcentage = $status_ca[$i] > 0 ? round(($status_encaisse[$i] / $status_ca[$i]) * 100, 2) : 0;
                                        $badge_class = $status == 'Payé' ? 'success' : ($status == 'Partiellement payé' ? 'warning' : 'danger');
                                        ?>
                                        <tr>
                                            <td><span class="label label-<?= $badge_class ?>"><?= $status ?></span></td>
                                            <td><?= number_format($status_ca[$i],0,","," ") ?> FCFA</td>
                                            <td><?= number_format($status_encaisse[$i],0,","," ") ?> FCFA</td>
                                            <td><?= number_format($status_reste[$i],0,","," ") ?> FCFA</td>
                                            <td><div class="progress" style="margin-bottom:0; height:20px;"><div class="progress-bar progress-bar-<?= $badge_class ?>" style="width:<?= $pourcentage ?>%"><?= $pourcentage ?>%</div></div></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">Aucune donnée de vente pour la période sélectionnée.</div>
        <?php endif; ?>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    if (typeof Chart !== 'undefined') {
        <?php if ($has_any_data): ?>
        new Chart(document.getElementById('chartRepartitionCA').getContext('2d'), {
            type: 'doughnut',
            data: { labels: ['CA Total', 'Encaissé (<?= $pourcentage_encaisse ?>%)', 'Reste (<?= $pourcentage_reste ?>%)'], datasets: [{ data: [<?= $ca_realise ?>, <?= $ca_encaisse ?>, <?= $creance ?>], backgroundColor: ['rgba(0,115,183,0.7)', 'rgba(40,167,69,0.8)', 'rgba(220,53,69,0.8)'], borderWidth: 2 }] },
            options: { responsive: true, maintainAspectRatio: false }
        });
        <?php endif; ?>
        <?php if (!empty($top_clients)): ?>
        new Chart(document.getElementById('chartTopClientsCreance').getContext('2d'), {
            type: 'doughnut',
            data: { labels: <?= json_encode($top_clients) ?>, datasets: [{ data: <?= json_encode($creance_clients) ?>, backgroundColor: ['#FF6384','#36A2EB','#FFCE56','#4BC0C0','#9966FF'] }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
        <?php endif; ?>
        <?php if (!empty($top_fournisseurs)): ?>
        new Chart(document.getElementById('chartTopFournisseursDette').getContext('2d'), {
            type: 'doughnut',
            data: { labels: <?= json_encode($top_fournisseurs) ?>, datasets: [{ data: <?= json_encode($dette_fournisseurs) ?>, backgroundColor: ['#F39C12','#3498DB','#E74C3C','#2ECC71','#9B59B6'] }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
        <?php endif; ?>
        <?php if (!empty($depenses_mensuel_labels)): ?>
        new Chart(document.getElementById('combinedExpensesChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= json_encode($depenses_mensuel_labels) ?>,
                datasets: [
                    { label: 'Factures fournisseur (TTC)', data: <?= json_encode($depenses_mensuel_factures) ?>, borderColor: '#f39c12', backgroundColor: 'rgba(243,156,18,0.1)', borderWidth: 2, fill: false, tension: 0.3 },
                    { label: 'Sorties de caisse', data: <?= json_encode($depenses_mensuel_caisse) ?>, borderColor: '#e74c3c', backgroundColor: 'rgba(231,76,60,0.1)', borderWidth: 2, fill: false, tension: 0.3 },
                    { label: 'Débits bancaires', data: <?= json_encode($depenses_mensuel_bank) ?>, borderColor: '#3498db', backgroundColor: 'rgba(52,152,219,0.1)', borderWidth: 2, fill: false, tension: 0.3 },
                    { label: 'Dépenses totales', data: <?= json_encode($depenses_mensuel_total) ?>, borderColor: '#2c3e50', backgroundColor: 'rgba(44,62,80,0.05)', borderWidth: 3, fill: false, tension: 0.3, borderDash: [5,5] }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: { tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${ctx.raw.toLocaleString()} FCFA` } }, legend: { position: 'top' } },
                scales: { y: { beginAtZero: true, title: { display: true, text: 'Montant (FCFA)' }, ticks: { callback: (val) => val.toLocaleString() } } }
            }
        });
        <?php endif; ?>
    }
</script>
</body>
</html>