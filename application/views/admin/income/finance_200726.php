<?php
// ===============================
// Connexion BDD + SESSION
// ===============================
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$CI = &get_instance();
$db = $CI->db;

if (!$db->conn_id) {
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
// FONCTIONS UTILITAIRES
// ===============================
function formatMontant($montant) {
    return number_format($montant, 0, ",", " ") . " FCFA";
}

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
// 1. CHIFFRE D'AFFAIRES
// ===============================
$sqlCARealise = "SELECT COALESCE(SUM(montant), 0) AS ca_realise FROM (
                    SELECT total_ttc AS montant FROM invoices WHERE created_at BETWEEN ? AND ?
                    UNION ALL
                    SELECT total_ttc AS montant FROM quotes_selling WHERE created_at BETWEEN ? AND ?
                    UNION ALL
                    SELECT montant FROM operation_caisse 
                    WHERE type_operation = 'ENTREE' 
                      AND date BETWEEN ? AND ?
                      AND (deleted = 0 OR deleted IS NULL)
                      AND (reference NOT LIKE 'TRF%' OR reference IS NULL)
                 ) AS all_entries";
$query = $db->query($sqlCARealise, [$date_debut, $date_fin, $date_debut, $date_fin, $date_debut, $date_fin]);
$row = $query->row_array();
$ca_realise = $row ? (float)$row['ca_realise'] : 0;

$sqlCAEncaisse = "SELECT COALESCE(SUM(montant), 0) AS ca_encaisse FROM (
                    SELECT amount_paid AS montant FROM invoices WHERE created_at BETWEEN ? AND ?
                    UNION ALL
                    SELECT amount_paid AS montant FROM quotes_selling WHERE created_at BETWEEN ? AND ?
                    UNION ALL
                    SELECT montant FROM operation_caisse 
                    WHERE type_operation = 'ENTREE' 
                      AND date BETWEEN ? AND ?
                      AND (deleted = 0 OR deleted IS NULL)
                      AND (reference NOT LIKE 'TRF%' OR reference IS NULL)
                 ) AS all_payments";
$query = $db->query($sqlCAEncaisse, [$date_debut, $date_fin, $date_debut, $date_fin, $date_debut, $date_fin]);
$row = $query->row_array();
$ca_encaisse = $row ? (float)$row['ca_encaisse'] : 0;

$creance = $ca_realise - $ca_encaisse;
$taux_encaissement = ($ca_realise > 0) ? round(($ca_encaisse / $ca_realise) * 100, 2) : 0;
$pourcentage_encaisse = $ca_realise > 0 ? round(($ca_encaisse / $ca_realise) * 100, 1) : 0;
$pourcentage_reste = $ca_realise > 0 ? round(($creance / $ca_realise) * 100, 1) : 0;

// ===============================
// 2. OBJECTIFS COMMERCIAUX
// ===============================
$sqlObjectifs = "SELECT SUM(target_amount) as total_objectif 
                 FROM `objectifs_commercial` 
                 WHERE date BETWEEN ? AND ?";
$query = $db->query($sqlObjectifs, [$date_debut, $date_fin]);
$row = $query->row_array();
$objectif_periode = $row ? (float)$row['total_objectif'] : 0;

$taux_realisation = $objectif_periode > 0 ? round(($ca_realise / $objectif_periode) * 100, 2) : 0;
$reste_objectif = max(0, $objectif_periode - $ca_realise);

// ===============================
// 3. ÉVOLUTION MENSUELLE DU CA
// ===============================
$sqlEvolutionMensuelle = "
    SELECT 
        annee_mois,
        SUM(ca_realise) AS ca_realise,
        SUM(ca_encaisse) AS ca_encaisse
    FROM (
        SELECT DATE_FORMAT(created_at, '%Y-%m') as annee_mois, total_ttc AS ca_realise, amount_paid AS ca_encaisse
        FROM invoices WHERE created_at BETWEEN ? AND ?
        UNION ALL
        SELECT DATE_FORMAT(created_at, '%Y-%m'), total_ttc, amount_paid
        FROM quotes_selling WHERE created_at BETWEEN ? AND ?
        UNION ALL
        SELECT DATE_FORMAT(date, '%Y-%m'), montant, montant
        FROM operation_caisse 
        WHERE type_operation = 'ENTREE' 
          AND date BETWEEN ? AND ?
          AND (deleted = 0 OR deleted IS NULL)
          AND (reference NOT LIKE 'TRF%' OR reference IS NULL)
    ) AS all_transactions
    GROUP BY annee_mois
    ORDER BY annee_mois ASC";
$query = $db->query($sqlEvolutionMensuelle, [$date_debut, $date_fin, $date_debut, $date_fin, $date_debut, $date_fin]);
$mois_labels_evolution = [];
$ca_realise_mensuel = [];
$ca_encaisse_mensuel = [];
$creance_mensuel = [];
foreach ($query->result_array() as $row) {
    $mois_labels_evolution[] = formatMoisFr($row['annee_mois'] . '-01');
    $ca_realise_mensuel[] = (float)$row['ca_realise'];
    $ca_encaisse_mensuel[] = (float)$row['ca_encaisse'];
    $creance_mensuel[] = (float)$row['ca_realise'] - (float)$row['ca_encaisse'];
}

// ===============================
// 4. TOP CLIENTS PAR CRÉANCE
// ===============================
$sqlClientsCreance = "
    SELECT 
        client_nom,
        SUM(ca_realise_client) AS ca_realise_client,
        SUM(ca_encaisse_client) AS ca_encaisse_client
    FROM (
        SELECT c.item_supplier AS client_nom, i.total_ttc AS ca_realise_client, i.amount_paid AS ca_encaisse_client
        FROM invoices i JOIN clients c ON i.customer_id = c.id
        WHERE i.created_at BETWEEN ? AND ?
        UNION ALL
        SELECT c.item_supplier AS client_nom, q.total_ttc AS ca_realise_client, q.amount_paid AS ca_encaisse_client
        FROM quotes_selling q JOIN clients c ON q.customer_id = c.id
        WHERE q.created_at BETWEEN ? AND ?
    ) AS all_transactions
    GROUP BY client_nom
    HAVING (SUM(ca_realise_client) - SUM(ca_encaisse_client)) > 0
    ORDER BY (SUM(ca_realise_client) - SUM(ca_encaisse_client)) DESC
    LIMIT 20";
$query = $db->query($sqlClientsCreance, [$date_debut, $date_fin, $date_debut, $date_fin]);
$all_clients_creance = [];
foreach ($query->result_array() as $row) {
    $all_clients_creance[] = [
        'nom' => $row['client_nom'] ?: 'Client Inconnu',
        'creance' => (float)($row['ca_realise_client'] - $row['ca_encaisse_client'])
    ];
}
// Top 5
$top_clients = array_slice(array_column($all_clients_creance, 'nom'), 0, 5);
$creance_clients = array_slice(array_column($all_clients_creance, 'creance'), 0, 5);
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
        annee_mois,
        CASE WHEN SUM(ca_realise) > 0 THEN ROUND((SUM(ca_encaisse) / SUM(ca_realise)) * 100, 2) ELSE 0 END AS taux_encaissement
    FROM (
        SELECT DATE_FORMAT(created_at, '%Y-%m') as annee_mois, total_ttc AS ca_realise, amount_paid AS ca_encaisse
        FROM invoices WHERE created_at BETWEEN ? AND ?
        UNION ALL
        SELECT DATE_FORMAT(created_at, '%Y-%m'), total_ttc, amount_paid
        FROM quotes_selling WHERE created_at BETWEEN ? AND ?
        UNION ALL
        SELECT DATE_FORMAT(date, '%Y-%m'), montant, montant
        FROM operation_caisse 
        WHERE type_operation = 'ENTREE' 
          AND date BETWEEN ? AND ?
          AND (deleted = 0 OR deleted IS NULL)
          AND (reference NOT LIKE 'TRF%' OR reference IS NULL)
    ) AS all_transactions
    GROUP BY annee_mois
    ORDER BY annee_mois ASC";
$query = $db->query($sqlTauxMensuel, [$date_debut, $date_fin, $date_debut, $date_fin, $date_debut, $date_fin]);
$taux_mensuel_labels = [];
$taux_mensuel_values = [];
foreach ($query->result_array() as $row) {
    $taux_mensuel_labels[] = formatMoisFr($row['annee_mois'] . '-01');
    $taux_mensuel_values[] = (float)$row['taux_encaissement'];
}

// ===============================
// 6. STATUTS PAIEMENT
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
        SELECT total_ttc, amount_paid FROM invoices WHERE created_at BETWEEN ? AND ?
        UNION ALL
        SELECT total_ttc, amount_paid FROM quotes_selling WHERE created_at BETWEEN ? AND ?
    ) AS all_transactions
    GROUP BY 
        CASE 
            WHEN (total_ttc - amount_paid) = 0 THEN 'Payé'
            WHEN amount_paid > 0 THEN 'Partiellement payé'
            ELSE 'Non payé'
        END";
$query = $db->query($sqlRepartitionStatus, [$date_debut, $date_fin, $date_debut, $date_fin]);
$status_labels = [];
$status_ca = [];
$status_encaisse = [];
$status_reste = [];
foreach ($query->result_array() as $row) {
    $status_labels[] = $row['status_paiement'];
    $status_ca[] = (float)$row['ca_total'];
    $status_encaisse[] = (float)$row['ca_encaisse'];
    $status_reste[] = (float)$row['reste'];
}

// ===============================
// 7. DÉPENSES : FACTURES FOURNISSEUR
// ===============================
$sqlTotalAchats = "SELECT COALESCE(SUM(total_ttc), 0) AS total_achats 
                   FROM invoices_supplier 
                   WHERE created_at BETWEEN ? AND ?";
$query = $db->query($sqlTotalAchats, [$date_debut, $date_fin]);
$row = $query->row_array();
$total_achats_ttc = $row ? (float)$row['total_achats'] : 0;

$sqlAchatsPaye = "SELECT COALESCE(SUM(amount_paid), 0) AS achats_paye 
                  FROM invoices_supplier 
                  WHERE created_at BETWEEN ? AND ?";
$query = $db->query($sqlAchatsPaye, [$date_debut, $date_fin]);
$row = $query->row_array();
$achats_paye = $row ? (float)$row['achats_paye'] : 0;

$dette_fournisseur = $total_achats_ttc - $achats_paye;
$taux_paiement_achats = ($total_achats_ttc > 0) ? round(($achats_paye / $total_achats_ttc) * 100, 2) : 0;

$sqlEvolutionAchats = "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as annee_mois,
        SUM(total_ttc) AS total_achats,
        SUM(amount_paid) AS paye_achats
    FROM invoices_supplier
    WHERE created_at BETWEEN ? AND ?
    GROUP BY annee_mois
    ORDER BY annee_mois ASC";
$query = $db->query($sqlEvolutionAchats, [$date_debut, $date_fin]);
$achats_mensuel_labels = [];
$achats_mensuel = [];
$paye_achats_mensuel = [];
$reste_achats_mensuel = [];
$achats_mensuel_data = [];
foreach ($query->result_array() as $row) {
    $mois = $row['annee_mois'];
    $achats_mensuel_labels[] = formatMoisFr($mois . '-01');
    $achats_mensuel[] = (float)$row['total_achats'];
    $paye_achats_mensuel[] = (float)$row['paye_achats'];
    $reste_achats_mensuel[] = (float)$row['total_achats'] - (float)$row['paye_achats'];
    $achats_mensuel_data[$mois] = ['total' => (float)$row['total_achats'], 'paye' => (float)$row['paye_achats']];
}

// ===============================
// 8. DÉPENSES : SORTIES CAISSE (hors TRF)
// ===============================
$sqlCaisseSortiesMensuel = "
    SELECT 
        DATE_FORMAT(date, '%Y-%m') as annee_mois,
        SUM(montant) AS total_sorties
    FROM operation_caisse
    WHERE type_operation = 'SORTIE'
      AND date BETWEEN ? AND ?
      AND (deleted = 0 OR deleted IS NULL)
      AND (reference NOT LIKE 'TRF%' OR reference IS NULL)
    GROUP BY annee_mois
    ORDER BY annee_mois ASC";
$query = $db->query($sqlCaisseSortiesMensuel, [$date_debut, $date_fin]);
$caisse_sorties_par_mois = [];
foreach ($query->result_array() as $row) {
    $caisse_sorties_par_mois[$row['annee_mois']] = (float)$row['total_sorties'];
}

// ===============================
// 9. TOTAUX GLOBAUX DÉPENSES
// ===============================
$sqlTotalCaisseSorties = "SELECT COALESCE(SUM(montant), 0) AS total 
                          FROM operation_caisse 
                          WHERE type_operation = 'SORTIE' 
                            AND date BETWEEN ? AND ?
                            AND (deleted = 0 OR deleted IS NULL)
                            AND (reference NOT LIKE 'TRF%' OR reference IS NULL)";
$query = $db->query($sqlTotalCaisseSorties, [$date_debut, $date_fin]);
$row = $query->row_array();
$total_caisse_sorties = $row ? (float)$row['total'] : 0;

$total_depenses_global = $total_achats_ttc + $total_caisse_sorties;
$total_deja_paye = $achats_paye + $total_caisse_sorties;
$reste_a_payer_global = $dette_fournisseur;
$taux_paiement_global = ($total_depenses_global > 0) ? round(($total_deja_paye / $total_depenses_global) * 100, 2) : 0;

// ===============================
// 10. MONTANT NET
// ===============================
$montant_net = $ca_realise - $total_depenses_global;
$resultat_net_encaisse = $ca_encaisse - $total_deja_paye;

// ===============================
// 11. FUSION MENSUELLE DES DÉPENSES
// ===============================
$caisse_brut = $caisse_sorties_par_mois;
$toutes_cles = array_unique(array_merge(array_keys($achats_mensuel_data), array_keys($caisse_brut)));
sort($toutes_cles);

$depenses_mensuel_labels = [];
$depenses_mensuel_factures = [];
$depenses_mensuel_caisse = [];
$depenses_mensuel_total = [];
$decaissement_mensuel_total = [];

foreach ($toutes_cles as $moisKey) {
    $depenses_mensuel_labels[] = formatMoisFr($moisKey . '-01');
    $facture_total = isset($achats_mensuel_data[$moisKey]) ? $achats_mensuel_data[$moisKey]['total'] : 0;
    $caisse_total = $caisse_brut[$moisKey] ?? 0;
    $depenses_mensuel_factures[] = $facture_total;
    $depenses_mensuel_caisse[] = $caisse_total;
    $depenses_mensuel_total[] = $facture_total + $caisse_total;

    $paye_facture = isset($achats_mensuel_data[$moisKey]) ? $achats_mensuel_data[$moisKey]['paye'] : 0;
    $decaissement_mensuel_total[] = $paye_facture + $caisse_total;
}

// ===============================
// 12. FOURNISSEURS AVEC DETTE
// ===============================
$sqlFournisseursDette = "
    SELECT 
        s.item_supplier AS fournisseur_nom,
        SUM(i.total_ttc - i.amount_paid) AS dette
    FROM invoices_supplier i
    JOIN item_supplier s ON i.customer_id = s.id
    WHERE i.created_at BETWEEN ? AND ?
    GROUP BY s.id
    HAVING dette > 0
    ORDER BY dette DESC
    LIMIT 20";
$query = $db->query($sqlFournisseursDette, [$date_debut, $date_fin]);
$all_fournisseurs_dette = [];
foreach ($query->result_array() as $row) {
    $all_fournisseurs_dette[] = [
        'nom' => $row['fournisseur_nom'] ?: 'Fournisseur inconnu',
        'dette' => (float)$row['dette']
    ];
}

// ===============================
// 13. NOMBRE DE TRANSACTIONS
// ===============================
$sqlNbTransactions = "SELECT COUNT(*) as nb FROM (
    SELECT id FROM invoices WHERE created_at BETWEEN ? AND ?
    UNION ALL
    SELECT id FROM quotes_selling WHERE created_at BETWEEN ? AND ?
) AS all_transactions";
$query = $db->query($sqlNbTransactions, [$date_debut, $date_fin, $date_debut, $date_fin]);
$row = $query->row_array();
$nb_factures = $row ? (int)$row['nb'] : 0;

$has_any_data = $ca_realise > 0 || $ca_encaisse > 0 || $creance > 0;

// ===============================
// 14. DÉPENSES PAR CATÉGORIE
// ===============================
$sqlCategories = "SELECT eh.id, eh.exp_category, SUM(oc.montant) as total
                  FROM operation_caisse oc
                  JOIN expense_head eh ON oc.exp_head_id = eh.id
                  WHERE oc.type_operation = 'SORTIE'
                    AND oc.date BETWEEN ? AND ?
                    AND (oc.deleted = 0 OR oc.deleted IS NULL)
                    AND (oc.reference NOT LIKE 'TRF%' OR oc.reference IS NULL)
                  GROUP BY eh.id, eh.exp_category
                  ORDER BY total DESC";
$query = $db->query($sqlCategories, [$date_debut, $date_fin]);
$categories_depenses = [];
foreach ($query->result_array() as $row) {
    $categories_depenses[] = [
        'id' => (int)$row['id'],
        'exp_category' => htmlspecialchars($row['exp_category'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        'total' => (float)$row['total']
    ];
}

$sqlOperationsDetail = "SELECT oc.id, oc.date, oc.note AS description, oc.montant, eh.id as cat_id, eh.exp_category
                        FROM operation_caisse oc
                        JOIN expense_head eh ON oc.exp_head_id = eh.id
                        WHERE oc.type_operation = 'SORTIE'
                          AND oc.date BETWEEN ? AND ?
                          AND (oc.deleted = 0 OR oc.deleted IS NULL)
                          AND (oc.reference NOT LIKE 'TRF%' OR oc.reference IS NULL)
                        ORDER BY oc.date DESC, eh.exp_category";
$query = $db->query($sqlOperationsDetail, [$date_debut, $date_fin]);
$operations_detail = [];
foreach ($query->result_array() as $row) {
    $operations_detail[] = [
        'id' => $row['id'],
        'date' => $row['date'],
        'description' => htmlspecialchars($row['description'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        'montant' => (float)$row['montant'],
        'cat_id' => (int)$row['cat_id'],
        'exp_category' => htmlspecialchars($row['exp_category'], ENT_QUOTES | ENT_HTML5, 'UTF-8')
    ];
}

// ===============================
// GESTION DES EXPORTS (Excel/PDF) - APRÈS TOUS LES CALCULS
// ===============================
if (isset($_GET['export']) && in_array($_GET['export'], ['excel', 'pdf'])) {
    $export_type = $_GET['export'];

    if ($export_type == 'excel') {
        // Export CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="export_dashboard_' . date('Ymd') . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Indicateur', 'Valeur']);
        fputcsv($output, ['CA Réalisé', $ca_realise]);
        fputcsv($output, ['CA Encaissé', $ca_encaisse]);
        fputcsv($output, ['Créance', $creance]);
        fputcsv($output, ['Objectif période', $objectif_periode]);
        fputcsv($output, ['Taux de réalisation', $taux_realisation . '%']);
        fputcsv($output, ['Total dépenses TTC', $total_depenses_global]);
        fputcsv($output, ['Déjà payé/décaissé', $total_deja_paye]);
        fputcsv($output, ['Reste à payer (dette)', $reste_a_payer_global]);
        fputcsv($output, ['Résultat brut', $montant_net]);
        fputcsv($output, ['Résultat net encaissé', $resultat_net_encaisse]);
        fputcsv($output, ['']);
        fputcsv($output, ['Dépenses mensuelles']);
        fputcsv($output, ['Mois', 'Factures fournisseur', 'Sorties caisse', 'Total']);
        foreach ($depenses_mensuel_labels as $idx => $label) {
            fputcsv($output, [$label, $depenses_mensuel_factures[$idx], $depenses_mensuel_caisse[$idx], $depenses_mensuel_total[$idx]]);
        }
        fclose($output);
        exit;
    } elseif ($export_type == 'pdf') {
        // Vérifier si mPDF est installé
        if (class_exists('Mpdf\Mpdf')) {
            $mpdf = new \Mpdf\Mpdf();
            $html = '<h1>Tableau de bord - CA & Créances & Dépenses</h1>';
            $html .= '<p>Période du ' . $date_debut_aff . ' au ' . $date_fin_aff . '</p>';
            $html .= '<table border="1" cellpadding="5"><tr><th>Indicateur</th><th>Valeur</th></tr>';
            $html .= '<tr><td>CA Réalisé</td><td>' . number_format($ca_realise,0,","," ") . ' FCFA</td></tr>';
            $html .= '<tr><td>CA Encaissé</td><td>' . number_format($ca_encaisse,0,","," ") . ' FCFA</td></tr>';
            $html .= '<tr><td>Créance</td><td>' . number_format($creance,0,","," ") . ' FCFA</td></tr>';
            $html .= '<tr><td>Objectif période</td><td>' . number_format($objectif_periode,0,","," ") . ' FCFA</td></tr>';
            $html .= '<tr><td>Taux de réalisation</td><td>' . $taux_realisation . '%</td></tr>';
            $html .= '<tr><td>Total dépenses TTC</td><td>' . number_format($total_depenses_global,0,","," ") . ' FCFA</td></tr>';
            $html .= '<tr><td>Déjà payé/décaissé</td><td>' . number_format($total_deja_paye,0,","," ") . ' FCFA</td></tr>';
            $html .= '<tr><td>Reste à payer (dette)</td><td>' . number_format($reste_a_payer_global,0,","," ") . ' FCFA</td></tr>';
            $html .= '<tr><td>Résultat brut</td><td>' . number_format($montant_net,0,","," ") . ' FCFA</td></tr>';
            $html .= '<tr><td>Résultat net encaissé</td><td>' . number_format($resultat_net_encaisse,0,","," ") . ' FCFA</td></tr>';
            $html .= '</table>';
            if (!empty($depenses_mensuel_labels)) {
                $html .= '<h3>Dépenses mensuelles</h3><table border="1" cellpadding="5"><tr><th>Mois</th><th>Factures</th><th>Caisse</th><th>Total</th></tr>';
                foreach ($depenses_mensuel_labels as $idx => $label) {
                    $html .= '<tr><td>' . $label . '</td><td>' . number_format($depenses_mensuel_factures[$idx],0,","," ") . '</td><td>' . number_format($depenses_mensuel_caisse[$idx],0,","," ") . '</td><td>' . number_format($depenses_mensuel_total[$idx],0,","," ") . '</td></tr>';
                }
                $html .= '</table>';
            }
            $mpdf->WriteHTML($html);
            $mpdf->Output('dashboard_' . date('Ymd') . '.pdf', 'D');
        } else {
            die('La bibliothèque mPDF n\'est pas installée. Veuillez l\'installer via Composer.');
        }
        exit;
    }
}

// ===============================
// FIN DES CALCULS - DÉBUT DE LA VUE HTML
// ===============================
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - CA & Créances & Dépenses</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        /* ... Vos styles ... */
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
        .icon-net { background: #3c8dbc; }
        .icon-achats { background: #f39c12; }
        .icon-achats-paye { background: #3c8dbc; }
        .icon-dette-fournisseur { background: #e74c3c; }
        .icon-resultat-net-encaisse { background: #8e44ad; }
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
        .btn-voir-plus {
            margin-top: 15px;
            width: 100%;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #007bff;
        }
        .btn-voir-plus:hover {
            background: #e9ecef;
            color: #0056b3;
        }
        .modal-body table { font-size: 13px; }
        .btn-actualiser {
            margin-left: 10px;
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }
        .btn-actualiser:hover { background-color: #5a6268; }
        .btn-categorie-detail {
            background: #e7f3ff;
            border: 1px solid #b6d4fe;
            color: #0a58ca;
            font-size: 12px;
            padding: 2px 10px;
            border-radius: 20px;
        }
        .btn-categorie-detail:hover { background: #cfe2ff; }
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
            <form method="GET" action="" class="form-inline" id="filterForm">
                <div class="form-group">
                    <label for="date_debut">Date début</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control" value="<?= htmlspecialchars($date_debut, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                    <label for="date_fin">Date fin</label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control" value="<?= htmlspecialchars($date_fin, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrer</button>
                <button type="button" class="btn btn-actualiser" onclick="window.location.href = window.location.pathname;"><i class="fa fa-refresh"></i> Actualiser</button>
                <a href="?export=excel&date_debut=<?= urlencode($date_debut) ?>&date_fin=<?= urlencode($date_fin) ?>" class="btn btn-success btn-export"><i class="fa fa-file-excel-o"></i> Export Excel</a>
                <a href="?export=pdf&date_debut=<?= urlencode($date_debut) ?>&date_fin=<?= urlencode($date_fin) ?>" class="btn btn-danger btn-export"><i class="fa fa-file-pdf-o"></i> Export PDF</a>
            </form>
            <p class="text-muted" style="margin-top:10px;">Période : du <strong><?= $date_debut_aff ?></strong> au <strong><?= $date_fin_aff ?></strong></p>
        </div>

        <!-- KPIs VENTES -->
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($ca_realise,0,","," ") ?> FCFA</h3>
                        <p>CA Réalisé</p>
                        <button class="btn btn-sm btn-voir-plus" onclick="$('#modalCaRealise').modal('show'); return false;"><i class="fa fa-eye"></i> Voir plus</button>
                    </div>
                    <div class="icon icon-ca-realise"><i class="fa fa-line-chart"></i></div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($ca_encaisse,0,","," ") ?> FCFA</h3>
                        <p>CA Encaissé</p>
                        <button class="btn btn-sm btn-voir-plus" onclick="$('#modalCaEncaisse').modal('show'); return false;"><i class="fa fa-eye"></i> Voir plus</button>
                    </div>
                    <div class="icon icon-ca-encaisse"><i class="fa fa-credit-card"></i></div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($creance,0,","," ") ?> FCFA</h3>
                        <p>Créance</p>
                        <button class="btn btn-sm btn-voir-plus" onclick="$('#modalCreance').modal('show'); return false;"><i class="fa fa-eye"></i> Voir plus</button>
                    </div>
                    <div class="icon icon-creance"><i class="fa fa-exclamation-triangle"></i></div>
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

        <!-- KPIs DÉPENSES GLOBALES -->
        <div class="row">
            <div class="col-md-12"><h3 class="page-header"><i class="fa fa-money"></i> Dépenses globales</h3></div>
            <div class="col-md-4 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($total_depenses_global,0,","," ") ?> FCFA</h3>
                        <p>Total dépenses TTC</p>
                        <small>Factures + Caisse (hors TRF)</small>
                        <button class="btn btn-sm btn-voir-plus" onclick="$('#modalTotalDepenses').modal('show'); return false;"><i class="fa fa-eye"></i> Voir plus</button>
                    </div>
                    <div class="icon icon-achats"><i class="fa fa-truck"></i></div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($total_deja_paye,0,","," ") ?> FCFA</h3>
                        <p>Déjà payé / décaissé</p>
                        <small>Taux: <?= $taux_paiement_global ?>%</small>
                        <button class="btn btn-sm btn-voir-plus" onclick="$('#modalDejaPaye').modal('show'); return false;"><i class="fa fa-eye"></i> Voir plus</button>
                    </div>
                    <div class="icon icon-achats-paye"><i class="fa fa-money"></i></div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($reste_a_payer_global,0,","," ") ?> FCFA</h3>
                        <p>Reste à payer (dette fournisseur)</p>
                        <small>Échéances fournisseurs uniquement</small>
                        <button class="btn btn-sm btn-voir-plus" onclick="$('#modalResteAPayer').modal('show'); return false;"><i class="fa fa-eye"></i> Voir plus</button>
                    </div>
                    <div class="icon icon-dette-fournisseur"><i class="fa fa-clock-o"></i></div>
                </div>
            </div>
        </div>

        <!-- Détail par source -->
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info">📊 <strong>Détail par source :</strong> Factures fournisseur : <?= formatMontant($total_achats_ttc) ?> | Sorties de caisse (hors TRF) : <?= formatMontant($total_caisse_sorties) ?></div>
            </div>
        </div>

        <!-- Courbe mensuelle des dépenses -->
        <?php if (!empty($depenses_mensuel_labels)): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header"><h3 class="box-title"><i class="fa fa-line-chart"></i> Évolution mensuelle des dépenses (Factures + Caisse)</h3></div>
                        <div class="box-body">
                            <canvas id="combinedExpensesChart" style="height:350px; width:100%;"></canvas>
                            <div class="table-responsive" style="margin-top:20px;">
                                <table class="table table-bordered table-striped">
                                    <thead><tr><th>Mois</th><th>Factures fournisseur</th><th>Sorties de caisse</th><th>Dépenses totales</th></tr></thead>
                                    <tbody>
                                    <?php foreach ($depenses_mensuel_labels as $idx => $label): ?>
                                        <tr>
                                            <td><?= $label ?></td>
                                            <td><?= formatMontant($depenses_mensuel_factures[$idx]) ?></td>
                                            <td><?= formatMontant($depenses_mensuel_caisse[$idx]) ?></td>
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

        <!-- Observations -->
        <div class="row">
            <div class="col-md-12">
                <div class="observation-box">
                    <form method="POST" action="">
                        <div class="form-group" style="width:80%;">
                            <label>📝 Observations sur les dépenses :</label>
                            <textarea name="observation" rows="2" class="form-control" style="width:100%;"><?= htmlspecialchars($observation, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></textarea>
                        </div>
                        <div class="form-group" style="margin-top:10px;">
                            <button type="submit" name="save_observation" class="btn btn-warning"><i class="fa fa-save"></i> Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MONTANT NET -->
        <div class="row">
            <div class="col-md-12">
                <div class="small-box" style="border-left: 5px solid #3c8dbc;">
                    <div class="inner">
                        <h3><?= number_format($montant_net,0,","," ") ?> FCFA</h3>
                        <p>Résultat brut (Bénéfice / Perte)</p>
                        <small><?= $montant_net >= 0 ? 'Bénéfice' : 'Perte' ?> généré(e) sur la période</small>
                    </div>
                    <div class="icon icon-net"><i class="fa fa-balance-scale"></i></div>
                </div>

                <div class="small-box" style="border-left: 5px solid #8e44ad;">
                    <div class="inner">
                        <h3><?= number_format($resultat_net_encaisse,0,","," ") ?> FCFA</h3>
                        <p>Résultat net (Encaissé - Décaissé)</p>
                        <small><?= $resultat_net_encaisse >= 0 ? 'Trésorerie positive' : 'Trésorerie négative' ?> sur la période</small>
                    </div>
                    <div class="icon icon-resultat-net-encaisse"><i class="fa fa-balance-scale"></i></div>
                </div>
            </div>
        </div>

    </section>
</div>

<!-- MODALES -->
<!-- Modale CA Réalisé -->
<div class="modal fade" id="modalCaRealise" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Détail du CA Réalisé</h4></div>
            <div class="modal-body">
                <h5>Évolution mensuelle</h5>
                <table class="table table-bordered"><thead><tr><th>Mois</th><th>Montant (FCFA)</th></tr></thead><tbody>
                    <?php if (!empty($mois_labels_evolution)): ?>
                        <?php foreach ($mois_labels_evolution as $idx => $label): ?>
                            <tr><td><?= $label ?></td><td><?= number_format($ca_realise_mensuel[$idx],0,","," ") ?> FCFA</td></tr>
                        <?php endforeach; ?>
                        <tr class="active"><td><strong>TOTAL</strong></td><td><strong><?= number_format($ca_realise,0,","," ") ?> FCFA</strong></td></tr>
                    <?php else: ?>
                        <tr><td colspan="2">Aucune donnée</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modale CA Encaissé -->
<div class="modal fade" id="modalCaEncaisse" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Détail du CA Encaissé</h4></div>
            <div class="modal-body">
                <h5>Évolution mensuelle</h5>
                <table class="table table-bordered"><thead><tr><th>Mois</th><th>Montant encaissé (FCFA)</th></tr></thead><tbody>
                    <?php if (!empty($mois_labels_evolution)): ?>
                        <?php foreach ($mois_labels_evolution as $idx => $label): ?>
                            <tr><td><?= $label ?></td><td><?= number_format($ca_encaisse_mensuel[$idx],0,","," ") ?> FCFA</td></tr>
                        <?php endforeach; ?>
                        <tr class="active"><td><strong>TOTAL</strong></td><td><strong><?= number_format($ca_encaisse,0,","," ") ?> FCFA</strong></td></tr>
                    <?php else: ?>
                        <tr><td colspan="2">Aucune donnée</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                <p class="text-muted">Taux d'encaissement global : <?= $taux_encaissement ?>%</p>
            </div>
        </div>
    </div>
</div>

<!-- Modale Créance -->
<div class="modal fade" id="modalCreance" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Détail de la Créance</h4></div>
            <div class="modal-body">
                <h5>Évolution mensuelle de la créance</h5>
                <table class="table table-bordered"><thead><tr><th>Mois</th><th>Créance (FCFA)</th></tr></thead><tbody>
                    <?php if (!empty($mois_labels_evolution)): ?>
                        <?php foreach ($mois_labels_evolution as $idx => $label): ?>
                            <tr><td><?= $label ?></td><td><?= number_format($creance_mensuel[$idx],0,","," ") ?> FCFA</td></tr>
                        <?php endforeach; ?>
                        <tr class="active"><td><strong>TOTAL</strong></td><td><strong><?= number_format($creance,0,","," ") ?> FCFA</strong></td></tr>
                    <?php else: ?>
                        <tr><td colspan="2">Aucune donnée</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                <h5>Clients avec créance</h5>
                <table class="table table-bordered table-striped"><thead><tr><th>Client</th><th>Créance (FCFA)</th></tr></thead><tbody>
                    <?php if (!empty($all_clients_creance)): ?>
                        <?php foreach ($all_clients_creance as $client): ?>
                            <tr><td><?= htmlspecialchars($client['nom'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></td><td><?= number_format($client['creance'],0,","," ") ?> FCFA</td></tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="2">Aucun client avec créance</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modale Total dépenses TTC -->
<div class="modal fade" id="modalTotalDepenses" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Détail des dépenses totales TTC</h4></div>
            <div class="modal-body">
                <h5>Répartition par source</h5>
                <table class="table table-bordered"><tr><th>Source</th><th>Montant (FCFA)</th></tr>
                    <tr><td>Factures fournisseur</td><td><?= number_format($total_achats_ttc,0,","," ") ?></td></tr>
                    <tr><td>Sorties de caisse (hors TRF)</td><td><?= number_format($total_caisse_sorties,0,","," ") ?></td></tr>
                    <tr class="active"><td><strong>TOTAL</strong></td><td><strong><?= number_format($total_depenses_global,0,","," ") ?> FCFA</strong></td></tr>
                </table>

                <?php if (!empty($categories_depenses)): ?>
                    <h5>Dépenses par catégorie (sorties de caisse)</h5>
                    <table class="table table-bordered table-striped">
                        <thead><tr><th>Catégorie</th><th>Montant total</th><th>Détail</th></tr></thead>
                        <tbody>
                        <?php foreach ($categories_depenses as $cat): ?>
                            <tr>
                                <td><?= $cat['exp_category'] ?></td>
                                <td><?= number_format($cat['total'],0,","," ") ?> FCFA</td>
                                <td><button class="btn btn-sm btn-categorie-detail" onclick="showCategoryDetails(<?= $cat['id'] ?>)"><i class="fa fa-search"></i> Voir les opérations</button></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">Aucune sortie de caisse par catégorie sur cette période.</div>
                <?php endif; ?>

                <h5>Évolution mensuelle des dépenses</h5>
                <div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Mois</th><th>Factures</th><th>Caisse</th><th>Total</th></tr></thead><tbody>
                        <?php if (!empty($depenses_mensuel_labels)): ?>
                            <?php foreach ($depenses_mensuel_labels as $idx => $label): ?>
                                <tr>
                                    <td><?= $label ?></td>
                                    <td><?= number_format($depenses_mensuel_factures[$idx],0,","," ") ?></td>
                                    <td><?= number_format($depenses_mensuel_caisse[$idx],0,","," ") ?></td>
                                    <td><strong><?= number_format($depenses_mensuel_total[$idx],0,","," ") ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4">Aucune donnée</td></tr>
                        <?php endif; ?>
                        </tbody></table></div>
            </div>
        </div>
    </div>
</div>

<!-- Modale Déjà payé / décaissé -->
<div class="modal fade" id="modalDejaPaye" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Détail des montants déjà payés / décaissés</h4></div>
            <div class="modal-body">
                <h5>Répartition par source</h5>
                <table class="table table-bordered"><tr><th>Source</th><th>Montant décaissé (FCFA)</th></tr>
                    <tr><td>Factures fournisseur (payé)</td><td><?= number_format($achats_paye,0,","," ") ?></td></tr>
                    <tr><td>Sorties de caisse (hors TRF)</td><td><?= number_format($total_caisse_sorties,0,","," ") ?></td></tr>
                    <tr class="active"><td><strong>TOTAL DÉCAISSÉ</strong></td><td><strong><?= number_format($total_deja_paye,0,","," ") ?> FCFA</strong></td></tr>
                </table>

                <?php if (!empty($categories_depenses)): ?>
                    <h5>Dépenses par catégorie (sorties de caisse)</h5>
                    <table class="table table-bordered table-striped">
                        <thead><tr><th>Catégorie</th><th>Montant total</th><th>Détail</th></tr></thead>
                        <tbody>
                        <?php foreach ($categories_depenses as $cat): ?>
                            <tr>
                                <td><?= $cat['exp_category'] ?></td>
                                <td><?= number_format($cat['total'],0,","," ") ?> FCFA</td>
                                <td><button class="btn btn-sm btn-categorie-detail" onclick="showCategoryDetails(<?= $cat['id'] ?>)"><i class="fa fa-search"></i> Voir les opérations</button></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">Aucune sortie de caisse par catégorie sur cette période.</div>
                <?php endif; ?>

                <h5>Évolution mensuelle du décaissé (toutes sources)</h5>
                <table class="table table-bordered"><thead><tr><th>Mois</th><th>Montant décaissé (FCFA)</th></tr></thead><tbody>
                    <?php if (!empty($depenses_mensuel_labels)): ?>
                        <?php foreach ($depenses_mensuel_labels as $idx => $label): ?>
                            <tr><td><?= $label ?></td><td><?= number_format($decaissement_mensuel_total[$idx],0,","," ") ?> FCFA</td></tr>
                        <?php endforeach; ?>
                        <tr class="active"><td><strong>TOTAL</strong></td><td><strong><?= number_format($total_deja_paye,0,","," ") ?> FCFA</strong></td></tr>
                    <?php else: ?>
                        <tr><td colspan="2">Aucune donnée</td></tr>
                    <?php endif; ?>
                    </tbody></table>
            </div>
        </div>
    </div>
</div>

<!-- Modale Reste à payer (dette fournisseur) -->
<div class="modal fade" id="modalResteAPayer" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Détail de la dette fournisseur</h4></div>
            <div class="modal-body">
                <h5>Fournisseurs avec dette</h5>
                <table class="table table-bordered table-striped"><thead><tr><th>Fournisseur</th><th>Dette (FCFA)</th></tr></thead><tbody>
                    <?php if (!empty($all_fournisseurs_dette)): ?>
                        <?php foreach ($all_fournisseurs_dette as $fourn): ?>
                            <tr><td><?= htmlspecialchars($fourn['nom'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></td><td><?= number_format($fourn['dette'],0,","," ") ?> FCFA</td></tr>
                        <?php endforeach; ?>
                        <tr class="active"><td><strong>TOTAL DETTE</strong></td><td><strong><?= number_format($reste_a_payer_global,0,","," ") ?> FCFA</strong></td></tr>
                    <?php else: ?>
                        <tr><td colspan="2">Aucune dette fournisseur pour la période sélectionnée.</td></tr>
                    <?php endif; ?>
                    </tbody></table>
                <p class="text-muted">* Dette calculée sur les factures fournisseur non entièrement payées.</p>
            </div>
        </div>
    </div>
</div>

<!-- Modale Détail des opérations par catégorie -->
<div class="modal fade" id="modalDetailsCategorie" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modalDetailsCategorieTitle">Opérations de la catégorie</h4>
            </div>
            <div class="modal-body">
                <div id="categoryDetailsContent">
                    <table class="table table-bordered table-striped" id="categoryOperationsTable">
                        <thead>
                        <tr><th>Date</th><th>Description</th><th>Montant (FCFA)</th></tr>
                        </thead>
                        <tbody id="categoryOperationsBody">
                        <!-- Rempli dynamiquement par JavaScript -->
                        </tbody>
                    </table>
                    <p class="text-muted" id="categoryTotalDisplay"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Données des opérations (passées depuis PHP)
    var operationsData = <?= json_encode($operations_detail) ?>;

    function showCategoryDetails(catId) {
        var filtered = operationsData.filter(function(op) {
            return op.cat_id === catId;
        });
        var catName = filtered.length > 0 ? filtered[0].exp_category : 'Catégorie ' + catId;
        document.getElementById('modalDetailsCategorieTitle').innerText = 'Opérations - ' + catName;
        var tbody = document.getElementById('categoryOperationsBody');
        tbody.innerHTML = '';
        var total = 0;
        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center">Aucune opération trouvée pour cette catégorie.</td></tr>';
        } else {
            filtered.forEach(function(op) {
                var row = document.createElement('tr');
                row.innerHTML = '<td>' + op.date + '</td><td>' + op.description + '</td><td>' + Number(op.montant).toLocaleString() + ' FCFA</td>';
                tbody.appendChild(row);
                total += op.montant;
            });
        }
        document.getElementById('categoryTotalDisplay').innerText = 'Total : ' + total.toLocaleString() + ' FCFA';
        $('#modalDetailsCategorie').modal('show');
    }

    // ================== GRAPHIQUES ==================
    // 1. Répartition du CA (graphique en anneau)
    <?php if ($has_any_data): ?>
    (function() {
        var canvas = document.getElementById('chartRepartitionCA');
        if (!canvas) {
            console.warn("Canvas chartRepartitionCA introuvable");
            return;
        }
        if (typeof Chart === 'undefined') {
            console.error('Chart.js non chargé');
            return;
        }
        new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['CA Total', 'Encaissé (<?= $pourcentage_encaisse ?>%)', 'Reste (<?= $pourcentage_reste ?>%)'],
                datasets: [{
                    data: [<?= $ca_realise ?>, <?= $ca_encaisse ?>, <?= $creance ?>],
                    backgroundColor: ['rgba(0,115,183,0.7)', 'rgba(40,167,69,0.8)', 'rgba(220,53,69,0.8)'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    })();
    <?php endif; ?>

    // 2. Évolution mensuelle des dépenses (graphique en ligne)
    <?php if (!empty($depenses_mensuel_labels)): ?>
    (function() {
        var canvas = document.getElementById('combinedExpensesChart');
        if (!canvas) {
            console.warn("Canvas combinedExpensesChart introuvable");
            return;
        }
        if (typeof Chart === 'undefined') {
            console.error('Chart.js non chargé');
            return;
        }
        new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= json_encode($depenses_mensuel_labels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                datasets: [
                    {
                        label: 'Factures fournisseur (TTC)',
                        data: <?= json_encode($depenses_mensuel_factures) ?>,
                        borderColor: '#f39c12',
                        backgroundColor: 'rgba(243,156,18,0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Sorties de caisse (hors TRF)',
                        data: <?= json_encode($depenses_mensuel_caisse) ?>,
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231,76,60,0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Dépenses totales',
                        data: <?= json_encode($depenses_mensuel_total) ?>,
                        borderColor: '#2c3e50',
                        backgroundColor: 'rgba(44,62,80,0.05)',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.3,
                        borderDash: [5,5]
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ctx.dataset.label + ': ' + ctx.raw.toLocaleString() + ' FCFA';
                            }
                        }
                    },
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Montant (FCFA)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    })();
    <?php else: ?>
    console.warn('Aucune donnée de dépenses mensuelles');
    <?php endif; ?>
</script>
</body>
</html>