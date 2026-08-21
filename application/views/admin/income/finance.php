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
// GESTION DE L'ENTREPRISE (MULTI-TENANT)
// ===============================
$entreprise_id = isset($_GET['entreprise_id']) ? (int)$_GET['entreprise_id'] : (isset($_SESSION['entreprise_id']) ? (int)$_SESSION['entreprise_id'] : 0);

if ($entreprise_id <= 0) {
    die("Aucune entreprise sélectionnée.");
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
    $_SESSION['dashboard_observation'][$entreprise_id] = trim($_POST['observation']);
    $redirect_url = "?" . http_build_query(array_merge($_GET, ['observation_saved' => '1']));
    header("Location: $redirect_url");
    exit;
}
$observation = $_SESSION['dashboard_observation'][$entreprise_id] ?? '';

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
// CLAUSE COMMUNE D'EXCLUSION DES TRANSFERTS
// ===============================
define('SQL_EXCLUDE_TRF_BANK', "AND (reference IS NULL OR (reference NOT LIKE 'TRF%' AND reference NOT LIKE '%transfert%' AND reference NOT LIKE '%Transfert%'))");

// ===============================
// 1. CA RÉALISÉ PAR UTILISATEUR (Factures + Services)
// ===============================

// 1a. CA des factures par utilisateur
$sqlCAFacturesByUser = "SELECT 
    user_name,
    COALESCE(SUM(total_ttc), 0) AS ca_factures,
    COALESCE(SUM(amount_paid), 0) AS encaisse_factures,
    COALESCE(SUM(remaining_amount), 0) AS reste_factures,
    COUNT(*) AS nb_factures
FROM invoices 
WHERE created_at BETWEEN ? AND ? 
  AND entreprise_id = ?
  AND cancelled_at IS NULL
GROUP BY user_name
ORDER BY ca_factures DESC";

$query = $db->query($sqlCAFacturesByUser, [$date_debut, $date_fin, $entreprise_id]);
$ca_factures_by_user = [];
if ($query) {
    foreach ($query->result_array() as $row) {
        $user = trim($row['user_name'] ?? 'Inconnu');
        $ca_factures_by_user[$user] = [
            'user_name' => $user,
            'ca_factures' => (float)$row['ca_factures'],
            'encaisse_factures' => (float)$row['encaisse_factures'],
            'reste_factures' => (float)$row['reste_factures'],
            'nb_factures' => (int)$row['nb_factures'],
            'ca_services' => 0,
            'encaisse_services' => 0,
            'reste_services' => 0,
            'nb_services' => 0
        ];
    }
}

// 1b. CA des services par utilisateur (quotes_selling - status=2)
$sqlCAServicesByUser = "SELECT 
    user_name,
    COALESCE(SUM(total_ttc), 0) AS ca_services,
    COALESCE(SUM(" . ($db->field_exists('amount_paid', 'quotes_selling') ? "amount_paid" : "total_ttc") . "), 0) AS encaisse_services,
    COALESCE(SUM(" . ($db->field_exists('remaining_amount', 'quotes_selling') ? "remaining_amount" : "total_ttc - amount_paid") . "), 0) AS reste_services,
    COUNT(*) AS nb_services
FROM quotes_selling 
WHERE created_at BETWEEN ? AND ? 
  AND entreprise_id = ?
  AND status = 2
GROUP BY user_name
ORDER BY ca_services DESC";

$query = $db->query($sqlCAServicesByUser, [$date_debut, $date_fin, $entreprise_id]);
if ($query) {
    foreach ($query->result_array() as $row) {
        $user = trim($row['user_name'] ?? 'Inconnu');
        if (!isset($ca_factures_by_user[$user])) {
            $ca_factures_by_user[$user] = [
                'user_name' => $user,
                'ca_factures' => 0,
                'encaisse_factures' => 0,
                'reste_factures' => 0,
                'nb_factures' => 0,
                'ca_services' => 0,
                'encaisse_services' => 0,
                'reste_services' => 0,
                'nb_services' => 0
            ];
        }
        $ca_factures_by_user[$user]['ca_services'] = (float)$row['ca_services'];
        $ca_factures_by_user[$user]['encaisse_services'] = (float)$row['encaisse_services'];
        $ca_factures_by_user[$user]['reste_services'] = (float)$row['reste_services'];
        $ca_factures_by_user[$user]['nb_services'] = (int)$row['nb_services'];
    }
}

// Calcul des totaux par utilisateur
$ca_by_user = [];
$total_ca_realise_global = 0;
$total_encaisse_global = 0;
$total_reste_global = 0;
$total_nb_transactions = 0;

foreach ($ca_factures_by_user as $user => $data) {
    $ca_total = $data['ca_factures'] + $data['ca_services'];
    $encaisse_total = $data['encaisse_factures'] + $data['encaisse_services'];
    $reste_total = $data['reste_factures'] + $data['reste_services'];
    $nb_total = $data['nb_factures'] + $data['nb_services'];
    
    $ca_by_user[$user] = [
        'user_name' => $user,
        'ca_factures' => $data['ca_factures'],
        'ca_services' => $data['ca_services'],
        'ca_total' => $ca_total,
        'encaisse_factures' => $data['encaisse_factures'],
        'encaisse_services' => $data['encaisse_services'],
        'encaisse_total' => $encaisse_total,
        'reste_factures' => $data['reste_factures'],
        'reste_services' => $data['reste_services'],
        'reste_total' => $reste_total,
        'nb_factures' => $data['nb_factures'],
        'nb_services' => $data['nb_services'],
        'nb_total' => $nb_total,
        'taux_encaissement' => $ca_total > 0 ? round(($encaisse_total / $ca_total) * 100, 2) : 0
    ];
    
    $total_ca_realise_global += $ca_total;
    $total_encaisse_global += $encaisse_total;
    $total_reste_global += $reste_total;
    $total_nb_transactions += $nb_total;
}

// Tri par CA total décroissant
usort($ca_by_user, function($a, $b) {
    return $b['ca_total'] - $a['ca_total'];
});

// ===============================
// 2. STATISTIQUES GLOBALES (pour les KPIs)
// ===============================

// 2a. CA global des factures
$sqlCAFactures = "SELECT COALESCE(SUM(total_ttc), 0) AS total FROM invoices WHERE created_at BETWEEN ? AND ? AND entreprise_id = ? AND cancelled_at IS NULL";
$query = $db->query($sqlCAFactures, [$date_debut, $date_fin, $entreprise_id]);
$row = $query->row_array();
$ca_factures_global = $row ? (float)$row['total'] : 0;

// 2b. CA global des services
$sqlCAServices = "SELECT COALESCE(SUM(total_ttc), 0) AS total FROM quotes_selling WHERE created_at BETWEEN ? AND ? AND entreprise_id = ? AND status = 2";
$query = $db->query($sqlCAServices, [$date_debut, $date_fin, $entreprise_id]);
$row = $query->row_array();
$ca_services_global = $row ? (float)$row['total'] : 0;

$ca_realise_global = $ca_factures_global + $ca_services_global;

// 2c. CA encaissé global
$sqlCAEncaisseFactures = "SELECT COALESCE(SUM(amount_paid), 0) AS total FROM invoices WHERE created_at BETWEEN ? AND ? AND entreprise_id = ? AND cancelled_at IS NULL";
$query = $db->query($sqlCAEncaisseFactures, [$date_debut, $date_fin, $entreprise_id]);
$row = $query->row_array();
$ca_encaisse_factures = $row ? (float)$row['total'] : 0;

$sqlCAEncaisseServices = "SELECT COALESCE(SUM(" . ($db->field_exists('amount_paid', 'quotes_selling') ? "amount_paid" : "total_ttc") . "), 0) AS total FROM quotes_selling WHERE created_at BETWEEN ? AND ? AND entreprise_id = ? AND status = 2";
$query = $db->query($sqlCAEncaisseServices, [$date_debut, $date_fin, $entreprise_id]);
$row = $query->row_array();
$ca_encaisse_services = $row ? (float)$row['total'] : 0;

$ca_encaisse_global = $ca_encaisse_factures + $ca_encaisse_services;

// 2d. Créance globale
$sqlResteFactures = "SELECT COALESCE(SUM(remaining_amount), 0) AS total FROM invoices WHERE created_at BETWEEN ? AND ? AND entreprise_id = ? AND cancelled_at IS NULL";
$query = $db->query($sqlResteFactures, [$date_debut, $date_fin, $entreprise_id]);
$row = $query->row_array();
$reste_factures_global = $row ? (float)$row['total'] : 0;

$sqlResteServices = "SELECT COALESCE(SUM(" . ($db->field_exists('remaining_amount', 'quotes_selling') ? "remaining_amount" : "total_ttc - amount_paid") . "), 0) AS total FROM quotes_selling WHERE created_at BETWEEN ? AND ? AND entreprise_id = ? AND status = 2";
$query = $db->query($sqlResteServices, [$date_debut, $date_fin, $entreprise_id]);
$row = $query->row_array();
$reste_services_global = $row ? (float)$row['total'] : 0;

$creance_global = $reste_factures_global + $reste_services_global;

// Taux d'encaissement global
$taux_encaissement_global = ($ca_realise_global > 0) ? round(($ca_encaisse_global / $ca_realise_global) * 100, 2) : 0;

// ===============================
// 3. PERFORMANCE MENSUELLE PAR UTILISATEUR
// ===============================
$sqlEvolutionMensuelleUser = "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as annee_mois,
        user_name,
        COALESCE(SUM(total_ttc), 0) AS ca_total
    FROM (
        SELECT created_at, user_name, total_ttc FROM invoices WHERE created_at BETWEEN ? AND ? AND entreprise_id = ? AND cancelled_at IS NULL
        UNION ALL
        SELECT created_at, user_name, total_ttc FROM quotes_selling WHERE created_at BETWEEN ? AND ? AND entreprise_id = ? AND status = 2
    ) AS all_ca
    GROUP BY annee_mois, user_name
    ORDER BY annee_mois ASC, user_name ASC";

$query = $db->query($sqlEvolutionMensuelleUser, [
    $date_debut, $date_fin, $entreprise_id,
    $date_debut, $date_fin, $entreprise_id
]);

$evolution_mensuelle_by_user = [];
$mois_labels_evolution = [];
if ($query) {
    foreach ($query->result_array() as $row) {
        $user = trim($row['user_name'] ?? 'Inconnu');
        $mois = $row['annee_mois'];
        $ca = (float)$row['ca_total'];
        
        if (!isset($evolution_mensuelle_by_user[$user])) {
            $evolution_mensuelle_by_user[$user] = [];
        }
        $evolution_mensuelle_by_user[$user][$mois] = $ca;
        
        if (!in_array($mois, $mois_labels_evolution)) {
            $mois_labels_evolution[] = $mois;
        }
    }
    sort($mois_labels_evolution);
}

// ===============================
// 4. NOMBRE D'UTILISATEURS ACTIFS
// ===============================
$sqlUsersActifs = "SELECT COUNT(DISTINCT user_name) as total FROM (
    SELECT user_name FROM invoices WHERE created_at BETWEEN ? AND ? AND entreprise_id = ? AND cancelled_at IS NULL
    UNION
    SELECT user_name FROM quotes_selling WHERE created_at BETWEEN ? AND ? AND entreprise_id = ? AND status = 2
) AS users";
$query = $db->query($sqlUsersActifs, [$date_debut, $date_fin, $entreprise_id, $date_debut, $date_fin, $entreprise_id]);
$row = $query->row_array();
$nb_users_actifs = $row ? (int)$row['total'] : 0;

$has_any_data = $ca_realise_global > 0 || $ca_encaisse_global > 0 || $creance_global > 0;

// ===============================
// 5. DÉPENSES : SORTIES CAISSE (hors TRF)
// ===============================
$sqlCaisseSortiesMensuel = "
    SELECT 
        DATE_FORMAT(date, '%Y-%m') as annee_mois,
        SUM(montant) AS total_sorties
    FROM operation_caisse
    WHERE type_operation = 'SORTIE'
      AND date BETWEEN ? AND ?
      AND entreprise_id = ?
      AND (deleted = 0 OR deleted IS NULL)
      AND (reference NOT LIKE 'TRF%' OR reference IS NULL)
    GROUP BY annee_mois
    ORDER BY annee_mois ASC";
$query = $db->query($sqlCaisseSortiesMensuel, [$date_debut, $date_fin, $entreprise_id]);
$caisse_sorties_par_mois = [];
foreach ($query->result_array() as $row) {
    $caisse_sorties_par_mois[$row['annee_mois']] = (float)$row['total_sorties'];
}

// ===============================
// 6. DÉPENSES : SORTIES BANQUE
// ===============================
$sqlBankSortiesMensuel = "
    SELECT 
        DATE_FORMAT(date, '%Y-%m') as annee_mois,
        SUM(amount) AS total_sorties
    FROM bank
    WHERE transaction_type IN ('withdrawal', 'expense')
      AND date BETWEEN ? AND ?
      AND entreprise_id = ?
      AND (is_deleted = 'no' OR is_deleted IS NULL)
      " . SQL_EXCLUDE_TRF_BANK . "
    GROUP BY annee_mois
    ORDER BY annee_mois ASC";
$query = $db->query($sqlBankSortiesMensuel, [$date_debut, $date_fin, $entreprise_id]);
$bank_sorties_par_mois = [];
foreach ($query->result_array() as $row) {
    $bank_sorties_par_mois[$row['annee_mois']] = (float)$row['total_sorties'];
}

// ===============================
// 7. TOTAUX GLOBAUX DÉPENSES
// ===============================
$sqlTotalCaisseSorties = "SELECT COALESCE(SUM(montant), 0) AS total 
                          FROM operation_caisse 
                          WHERE type_operation = 'SORTIE' 
                            AND date BETWEEN ? AND ?
                            AND entreprise_id = ?
                            AND (deleted = 0 OR deleted IS NULL)
                            AND (reference NOT LIKE 'TRF%' OR reference IS NULL)";
$query = $db->query($sqlTotalCaisseSorties, [$date_debut, $date_fin, $entreprise_id]);
$row = $query->row_array();
$total_caisse_sorties = $row ? (float)$row['total'] : 0;

$sqlTotalBankSorties = "SELECT COALESCE(SUM(amount), 0) AS total 
                        FROM bank 
                        WHERE transaction_type IN ('withdrawal', 'expense')
                          AND date BETWEEN ? AND ?
                          AND entreprise_id = ?
                          AND (is_deleted = 'no' OR is_deleted IS NULL)
                          " . SQL_EXCLUDE_TRF_BANK . "";
$query = $db->query($sqlTotalBankSorties, [$date_debut, $date_fin, $entreprise_id]);
$row = $query->row_array();
$total_bank_sorties = $row ? (float)$row['total'] : 0;

$total_depenses_global = $total_caisse_sorties + $total_bank_sorties;
$total_deja_paye = $total_depenses_global;
$taux_paiement_global = ($total_depenses_global > 0) ? 100 : 0;

// ===============================
// 8. RÉSULTATS BRUT ET NET
// ===============================
$montant_net = $ca_realise_global - $total_depenses_global;
$resultat_net_encaisse = $ca_encaisse_global - $total_deja_paye;

// ===============================
// 9. FUSION MENSUELLE DES DÉPENSES
// ===============================
$caisse_brut = $caisse_sorties_par_mois;
$bank_brut = $bank_sorties_par_mois;
$toutes_cles = array_unique(array_merge(array_keys($caisse_brut), array_keys($bank_brut)));
sort($toutes_cles);

$depenses_mensuel_labels = [];
$depenses_mensuel_caisse = [];
$depenses_mensuel_bank = [];
$depenses_mensuel_total = [];
$decaissement_mensuel_total = [];

foreach ($toutes_cles as $moisKey) {
    $depenses_mensuel_labels[] = formatMoisFr($moisKey . '-01');
    $caisse_total = $caisse_brut[$moisKey] ?? 0;
    $bank_total = $bank_brut[$moisKey] ?? 0;
    $depenses_mensuel_caisse[] = $caisse_total;
    $depenses_mensuel_bank[] = $bank_total;
    $depenses_mensuel_total[] = $caisse_total + $bank_total;
    $decaissement_mensuel_total[] = $caisse_total + $bank_total;
}

// ===============================
// 10. DÉPENSES PAR CATÉGORIE (Caisse + Banque)
// ===============================
// Dépenses caisse par catégorie
$sqlCaisseCategories = "SELECT eh.id, eh.exp_category, SUM(oc.montant) as total
                        FROM operation_caisse oc
                        JOIN expense_head eh ON oc.exp_head_id = eh.id AND eh.entreprise_id = ?
                        WHERE oc.type_operation = 'SORTIE'
                          AND oc.date BETWEEN ? AND ?
                          AND oc.entreprise_id = ?
                          AND (oc.deleted = 0 OR oc.deleted IS NULL)
                          AND (oc.reference NOT LIKE 'TRF%' OR oc.reference IS NULL)
                        GROUP BY eh.id, eh.exp_category
                        ORDER BY total DESC";
$query = $db->query($sqlCaisseCategories, [$entreprise_id, $date_debut, $date_fin, $entreprise_id]);
$categories_depenses = [];
foreach ($query->result_array() as $row) {
    $categories_depenses[] = [
        'source' => 'Caisse',
        'id' => (int)$row['id'],
        'exp_category' => htmlspecialchars($row['exp_category'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        'total' => (float)$row['total']
    ];
}

// Dépenses banque par catégorie
$sqlBankCategories = "SELECT 
                        COALESCE(category, 'Non catégorisé') AS exp_category,
                        SUM(amount) as total
                      FROM bank
                      WHERE transaction_type IN ('withdrawal', 'expense')
                        AND date BETWEEN ? AND ?
                        AND entreprise_id = ?
                        AND (is_deleted = 'no' OR is_deleted IS NULL)
                        " . SQL_EXCLUDE_TRF_BANK . "
                      GROUP BY category
                      ORDER BY total DESC";
$query = $db->query($sqlBankCategories, [$date_debut, $date_fin, $entreprise_id]);
foreach ($query->result_array() as $row) {
    $categories_depenses[] = [
        'source' => 'Banque',
        'id' => 0,
        'exp_category' => htmlspecialchars($row['exp_category'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        'total' => (float)$row['total']
    ];
}

// Trier par montant décroissant
usort($categories_depenses, function($a, $b) {
    return $b['total'] - $a['total'];
});

// ===============================
// 11. GESTION DES EXPORTS
// ===============================
if (isset($_GET['export']) && in_array($_GET['export'], ['excel', 'pdf'])) {
    $export_type = $_GET['export'];

    if ($export_type == 'excel') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="ca_par_utilisateur_' . date('Ymd') . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Utilisateur', 'CA Factures', 'CA Services', 'CA Total', 'Encaissé', 'Reste', 'Taux encaissement', 'Nb Factures', 'Nb Services', 'Total']);
        foreach ($ca_by_user as $user) {
            fputcsv($output, [
                $user['user_name'],
                $user['ca_factures'],
                $user['ca_services'],
                $user['ca_total'],
                $user['encaisse_total'],
                $user['reste_total'],
                $user['taux_encaissement'] . '%',
                $user['nb_factures'],
                $user['nb_services'],
                $user['nb_total']
            ]);
        }
        fputcsv($output, ['']);
        fputcsv($output, ['TOTAL GÉNÉRAL', $ca_factures_global, $ca_services_global, $ca_realise_global, $ca_encaisse_global, $creance_global, $taux_encaissement_global . '%', '', '', $total_nb_transactions]);
        fputcsv($output, ['']);
        fputcsv($output, ['DÉPENSES']);
        fputcsv($output, ['Total dépenses', $total_depenses_global]);
        fputcsv($output, ['Déjà payé/décaissé', $total_deja_paye]);
        fputcsv($output, ['Résultat brut', $montant_net]);
        fputcsv($output, ['Résultat net encaissé', $resultat_net_encaisse]);
        fputcsv($output, ['']);
        fputcsv($output, ['Dépenses mensuelles']);
        fputcsv($output, ['Mois', 'Sorties caisse', 'Sorties banque', 'Total']);
        foreach ($depenses_mensuel_labels as $idx => $label) {
            fputcsv($output, [$label, $depenses_mensuel_caisse[$idx], $depenses_mensuel_bank[$idx], $depenses_mensuel_total[$idx]]);
        }
        fputcsv($output, ['']);
        fputcsv($output, ['Dépenses par catégorie']);
        fputcsv($output, ['Source', 'Catégorie', 'Montant']);
        foreach ($categories_depenses as $cat) {
            fputcsv($output, [$cat['source'], $cat['exp_category'], $cat['total']]);
        }
        fclose($output);
        exit;
    } elseif ($export_type == 'pdf') {
        if (class_exists('Mpdf\Mpdf')) {
            $mpdf = new \Mpdf\Mpdf();
            $html = '<h1>Chiffre d\'affaires par utilisateur</h1>';
            $html .= '<p>Période du ' . $date_debut_aff . ' au ' . $date_fin_aff . '</p>';
            $html .= '<h2>CA par utilisateur</h2>';
            $html .= '<table border="1" cellpadding="5"><tr><th>Utilisateur</th><th>CA Factures</th><th>CA Services</th><th>CA Total</th><th>Encaissé</th><th>Reste</th><th>Taux</th></tr>';
            foreach ($ca_by_user as $user) {
                $html .= '<tr><td>' . htmlspecialchars($user['user_name']) . '</td><td>' . number_format($user['ca_factures'],0,","," ") . '</td><td>' . number_format($user['ca_services'],0,","," ") . '</td><td><strong>' . number_format($user['ca_total'],0,","," ") . '</strong></td><td>' . number_format($user['encaisse_total'],0,","," ") . '</td><td>' . number_format($user['reste_total'],0,","," ") . '</td><td>' . $user['taux_encaissement'] . '%</td></tr>';
            }
            $html .= '<tr style="font-weight:bold;"><td>TOTAL</td><td>' . number_format($ca_factures_global,0,","," ") . '</td><td>' . number_format($ca_services_global,0,","," ") . '</td><td>' . number_format($ca_realise_global,0,","," ") . '</td><td>' . number_format($ca_encaisse_global,0,","," ") . '</td><td>' . number_format($creance_global,0,","," ") . '</td><td>' . $taux_encaissement_global . '%</td></tr>';
            $html .= '</table>';
            
            $html .= '<h2>Dépenses globales</h2>';
            $html .= '<table border="1" cellpadding="5"><tr><th>Indicateur</th><th>Valeur</th></tr>';
            $html .= '<tr><td>Total dépenses</td><td>' . number_format($total_depenses_global,0,","," ") . ' FCFA</td></tr>';
            $html .= '<tr><td>Déjà payé/décaissé</td><td>' . number_format($total_deja_paye,0,","," ") . ' FCFA</td></tr>';
            $html .= '<tr><td>Résultat brut</td><td>' . number_format($montant_net,0,","," ") . ' FCFA</td></tr>';
            $html .= '<tr><td>Résultat net encaissé</td><td>' . number_format($resultat_net_encaisse,0,","," ") . ' FCFA</td></tr>';
            $html .= '</table>';
            
            if (!empty($depenses_mensuel_labels)) {
                $html .= '<h3>Dépenses mensuelles</h3><table border="1" cellpadding="5"><tr><th>Mois</th><th>Caisse</th><th>Banque</th><th>Total</th></tr>';
                foreach ($depenses_mensuel_labels as $idx => $label) {
                    $html .= '<tr><td>' . $label . '</td><td>' . number_format($depenses_mensuel_caisse[$idx],0,","," ") . '</td><td>' . number_format($depenses_mensuel_bank[$idx],0,","," ") . '</td><td>' . number_format($depenses_mensuel_total[$idx],0,","," ") . '</td></tr>';
                }
                $html .= '</table>';
            }
            
            if (!empty($categories_depenses)) {
                $html .= '<h3>Dépenses par catégorie</h3><table border="1" cellpadding="5"><tr><th>Source</th><th>Catégorie</th><th>Montant</th></tr>';
                foreach ($categories_depenses as $cat) {
                    $html .= '<tr><td>' . $cat['source'] . '</td><td>' . $cat['exp_category'] . '</td><td>' . number_format($cat['total'],0,","," ") . ' FCFA</td></tr>';
                }
                $html .= '</table>';
            }
            
            $mpdf->WriteHTML($html);
            $mpdf->Output('ca_par_utilisateur_' . date('Ymd') . '.pdf', 'D');
        } else {
            die('La bibliothèque mPDF n\'est pas installée.');
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CA par utilisateur</title>
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
        .icon-ca-total { background: #00a65a; }
        .icon-utilisateurs { background: #3c8dbc; }
        .icon-encaisse { background: #00c0ef; }
        .icon-creance { background: #dd4b39; }
        .icon-depenses { background: #f39c12; }
        .icon-net { background: #8e44ad; }
        .box { border-radius:10px; box-shadow:0 3px 6px rgba(0,0,0,0.1); margin-bottom:20px; }
        .btn-export { margin-left: 10px; }
        .progress-bar-custom { transition: width 0.8s ease; }
        .progress { height: 25px; border-radius: 12px; background: #e9ecef; }
        .progress-bar { font-weight: bold; line-height: 25px; }
        .tr-hover:hover { background-color: #f5f5f5; cursor: pointer; }
        .observation-box {
            background: #fff9e6;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .badge-source-caisse { background-color: #17a2b8; }
        .badge-source-banque { background-color: #6f42c1; }
        .btn-actualiser {
            margin-left: 10px;
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }
        .btn-actualiser:hover { background-color: #5a6268; }
        .btn-voir-plus {
            margin-top: 15px;
            width: 100%;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #007bff;
        }
        .btn-voir-plus:hover { background: #e9ecef; color: #0056b3; }
        .modal-body table { font-size: 13px; }
    </style>
</head>
<body>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-users"></i> Chiffre d'affaires par utilisateur</h1>
    </section>
    <section class="content" style="padding-top: 28px">
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

        <!-- KPIs -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= formatMontant($ca_realise_global) ?></h3>
                        <p>CA Total réalisé</p>
                       <!-- <small><?= $ca_factures_global > 0 ? 'Factures: ' . formatMontant($ca_factures_global) : '' ?></small>-->
                        <?php if ($ca_services_global > 0): ?>
                            <!--<br><small>Services: <?= formatMontant($ca_services_global) ?></small>-->
                        <?php endif; ?>
                    </div>
                    <div class="icon icon-ca-total"><i class="fa fa-line-chart"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= $nb_users_actifs ?></h3>
                        <p>Utilisateurs actifs</p>
                      <!--  <small>Sur la période sélectionnée</small>-->
                    </div>
                    <div class="icon icon-utilisateurs"><i class="fa fa-users"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= formatMontant($ca_encaisse_global) ?></h3>
                        <p>Montant encaissé</p>
                       <!-- <small>Taux: <?= $taux_encaissement_global ?>%</small>-->
                    </div>
                    <div class="icon icon-encaisse"><i class="fa fa-credit-card"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= formatMontant($creance_global) ?></h3>
                        <p>Créance totale</p>
                        <!--<small>Reste à recouvrer</small>-->
                    </div>
                    <div class="icon icon-creance"><i class="fa fa-exclamation-triangle"></i></div>
                </div>
            </div>
        </div>

        <?php if ($has_any_data && !empty($ca_by_user)): ?>
            <!-- Graphique CA par utilisateur -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header"><h3 class="box-title"><i class="fa fa-bar-chart"></i> CA par utilisateur</h3></div>
                        <div class="box-body">
                            <canvas id="chartCAUser" style="height:400px; width:100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Évolution mensuelle par utilisateur -->
            <?php if (!empty($mois_labels_evolution) && count($ca_by_user) > 1): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-success">
                        <div class="box-header"><h3 class="box-title"><i class="fa fa-line-chart"></i> Évolution mensuelle par utilisateur</h3></div>
                        <div class="box-body">
                            <canvas id="chartEvolutionUser" style="height:400px; width:100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Tableau détaillé -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-table"></i> Détail par utilisateur</h3>
                            <!--<div class="box-tools pull-right">
                                <span class="label label-primary">Total transactions: <?= $total_nb_transactions ?></span>
                            </div>-->
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Utilisateur</th>
                                            <th>CA Factures</th>
                                            <th>CA Services</th>
                                            <th>CA Total</th>
                                            <th>Encaissé</th>
                                            <th>Reste</th>
                                          <!--  <th>Taux encaissement</th>
                                            <th>Nb transactions</th>
                                            <th>Détail</th>-->
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php $rank = 1; ?>
                                    <?php foreach ($ca_by_user as $user): ?>
                                        <?php 
                                        $pourcentage_global = $ca_realise_global > 0 ? round(($user['ca_total'] / $ca_realise_global) * 100, 1) : 0;
                                        $badge_class = $user['taux_encaissement'] >= 90 ? 'success' : ($user['taux_encaissement'] >= 50 ? 'warning' : 'danger');
                                        ?>
                                        <tr class="tr-hover">
                                            <td><span class="label label-default">#<?= $rank++ ?></span></td>
                                            <td><strong><?= htmlspecialchars($user['user_name']) ?></strong></td>
                                            <td><?= formatMontant($user['ca_factures']) ?></td>
                                            <td><?= formatMontant($user['ca_services']) ?></td>
                                            <td><strong><?= formatMontant($user['ca_total']) ?></strong> <span class="text-muted">(<?= $pourcentage_global ?>%)</span></td>
                                            <td><span class="label label-success"><?= formatMontant($user['encaisse_total']) ?></span></td>
                                            <td><span class="label label-<?= $user['reste_total'] > 0 ? 'danger' : 'success' ?>"><?= formatMontant($user['reste_total']) ?></span></td>
                                            <!--  <td>
                                                <div class="progress" style="margin-bottom:0; height:20px;">
                                                    <div class="progress-bar progress-bar-<?= $badge_class ?> progress-bar-custom" style="width: <?= $user['taux_encaissement'] ?>%;">
                                                        <?= $user['taux_encaissement'] ?>%
                                                    </div>
                                                </div>
                                            </td>
                                          <td><span class="badge"><?= $user['nb_total'] ?></span></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="showUserDetails('<?= htmlspecialchars($user['user_name'], ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>')">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </td>-->
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="active" style="font-weight:bold;">
                                            <th colspan="2" class="text-right">TOTAUX :</th>
                                            <th><?= formatMontant($ca_factures_global) ?></th>
                                            <th><?= formatMontant($ca_services_global) ?></th>
                                            <th><?= formatMontant($ca_realise_global) ?></th>
                                            <th><?= formatMontant($ca_encaisse_global) ?></th>
                                            <th><?= formatMontant($creance_global) ?></th>
                                            <th><?= $taux_encaissement_global ?>%</th>
                                            <th><?= $total_nb_transactions ?></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-info text-center"><i class="fa fa-info-circle"></i> Aucune donnée de vente pour la période sélectionnée.</div>
        <?php endif; ?>

        <!-- ============================================ -->
        <!-- DÉPENSES GLOBALES -->
        <!-- ============================================ -->
        <div class="row">
            <div class="col-md-12"><h3 class="page-header"><i class="fa fa-money"></i> Dépenses globales</h3></div>
            <div class="col-md-6 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= formatMontant($total_depenses_global) ?></h3>
                        <p>Total dépenses</p>
                        <small>Caisse + Banque</small>
                        <button class="btn btn-sm btn-voir-plus" onclick="$('#modalTotalDepenses').modal('show'); return false;"><i class="fa fa-eye"></i> Voir plus</button>
                    </div>
                    <div class="icon icon-depenses"><i class="fa fa-truck"></i></div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= formatMontant($total_deja_paye) ?></h3>
                        <p>Déjà payé / décaissé</p>
                        <small>Taux: <?= $taux_paiement_global ?>%</small>
                        <button class="btn btn-sm btn-voir-plus" onclick="$('#modalDejaPaye').modal('show'); return false;"><i class="fa fa-eye"></i> Voir plus</button>
                    </div>
                    <div class="icon icon-depenses"><i class="fa fa-money"></i></div>
                </div>
            </div>
        </div>

        <!-- Dépenses par catégorie -->
        <?php if (!empty($categories_depenses)): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-warning">
                        <div class="box-header"><h3 class="box-title"><i class="fa fa-pie-chart"></i> Dépenses par catégorie</h3></div>
                        <div class="box-body">
                            <table class="table table-bordered table-striped">
                                <thead><tr><th>Source</th><th>Catégorie</th><th>Montant total</th></tr></thead>
                                <tbody>
                                <?php foreach ($categories_depenses as $cat): ?>
                                    <tr>
                                        <td><span class="badge badge-source-<?= $cat['source'] == 'Caisse' ? 'caisse' : 'banque' ?>"><?= $cat['source'] ?></span></td>
                                        <td><?= $cat['exp_category'] ?></td>
                                        <td><?= formatMontant($cat['total']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Évolution mensuelle des dépenses -->
        <?php if (!empty($depenses_mensuel_labels)): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header"><h3 class="box-title"><i class="fa fa-line-chart"></i> Évolution mensuelle des dépenses (Caisse + Banque)</h3></div>
                        <div class="box-body">
                            <canvas id="combinedExpensesChart" style="height:350px; width:100%;"></canvas>
                            <div class="table-responsive" style="margin-top:20px;">
                                <table class="table table-bordered table-striped">
                                    <thead><tr><th>Mois</th><th>Sorties de caisse</th><th>Sorties banque</th><th>Dépenses totales</th></tr></thead>
                                    <tbody>
                                    <?php foreach ($depenses_mensuel_labels as $idx => $label): ?>
                                        <tr>
                                            <td><?= $label ?></td>
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

        <!-- Résultat brut et net -->
        <div class="row">
            <div class="col-md-6">
                <div class="small-box" style="border-left: 5px solid #3c8dbc;">
                    <div class="inner">
                        <h3><?= formatMontant($montant_net) ?></h3>
                        <p>Résultat brut</p>
                        <small><?= $montant_net >= 0 ? 'Bénéfice' : 'Perte' ?> généré(e) sur la période</small>
                        <button class="btn btn-sm btn-voir-plus" onclick="$('#modalResultatBrut').modal('show'); return false;"><i class="fa fa-eye"></i> Voir plus</button>
                    </div>
                    <div class="icon icon-net"><i class="fa fa-balance-scale"></i></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="small-box" style="border-left: 5px solid #8e44ad;">
                    <div class="inner">
                        <h3><?= formatMontant($resultat_net_encaisse) ?></h3>
                        <p>Résultat net encaissé</p>
                        <small><?= $resultat_net_encaisse >= 0 ? 'Trésorerie positive' : 'Trésorerie négative' ?> sur la période</small>
                        <button class="btn btn-sm btn-voir-plus" onclick="$('#modalResultatNet').modal('show'); return false;"><i class="fa fa-eye"></i> Voir plus</button>
                    </div>
                    <div class="icon icon-net"><i class="fa fa-balance-scale"></i></div>
                </div>
            </div>
        </div>

    </section>
</div>

<!-- ============================================ -->
<!-- MODALES -->
<!-- ============================================ -->

<!-- Modal Détail utilisateur -->
<div class="modal fade" id="modalUserDetails" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-user"></i> Détail pour : <span id="userDetailName"></span></h4>
            </div>
            <div class="modal-body">
                <div id="userDetailContent">
                    <div class="text-center"><i class="fa fa-spinner fa-spin"></i> Chargement...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Total dépenses -->
<div class="modal fade" id="modalTotalDepenses" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Détail des dépenses totales</h4></div>
            <div class="modal-body">
                <h5>Répartition par source</h5>
                <table class="table table-bordered">
                    <tr><th>Source</th><th>Montant (FCFA)</th></tr>
                    <tr><td>Sorties de caisse</td><td><?= number_format($total_caisse_sorties,0,","," ") ?></td></tr>
                    <tr><td>Sorties banque</td><td><?= number_format($total_bank_sorties,0,","," ") ?></td></tr>
                    <tr class="active"><td><strong>TOTAL</strong></td><td><strong><?= number_format($total_depenses_global,0,","," ") ?> FCFA</strong></td></tr>
                </table>

                <?php if (!empty($categories_depenses)): ?>
                    <h5>Dépenses par catégorie</h5>
                    <table class="table table-bordered table-striped">
                        <thead><tr><th>Source</th><th>Catégorie</th><th>Montant total</th></tr></thead>
                        <tbody>
                        <?php foreach ($categories_depenses as $cat): ?>
                            <tr>
                                <td><span class="badge badge-source-<?= $cat['source'] == 'Caisse' ? 'caisse' : 'banque' ?>"><?= $cat['source'] ?></span></td>
                                <td><?= $cat['exp_category'] ?></td>
                                <td><?= number_format($cat['total'],0,","," ") ?> FCFA</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">Aucune dépense par catégorie sur cette période.</div>
                <?php endif; ?>

                <h5>Évolution mensuelle des dépenses</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead><tr><th>Mois</th><th>Caisse</th><th>Banque</th><th>Total</th></tr></thead>
                        <tbody>
                        <?php if (!empty($depenses_mensuel_labels)): ?>
                            <?php foreach ($depenses_mensuel_labels as $idx => $label): ?>
                                <tr>
                                    <td><?= $label ?></td>
                                    <td><?= number_format($depenses_mensuel_caisse[$idx],0,","," ") ?></td>
                                    <td><?= number_format($depenses_mensuel_bank[$idx],0,","," ") ?></td>
                                    <td><strong><?= number_format($depenses_mensuel_total[$idx],0,","," ") ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4">Aucune donnée</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Déjà payé / décaissé -->
<div class="modal fade" id="modalDejaPaye" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Détail des montants déjà payés / décaissés</h4></div>
            <div class="modal-body">
                <h5>Répartition par source</h5>
                <table class="table table-bordered">
                    <tr><th>Source</th><th>Montant décaissé (FCFA)</th></tr>
                    <tr><td>Sorties de caisse</td><td><?= number_format($total_caisse_sorties,0,","," ") ?></td></tr>
                    <tr><td>Sorties banque</td><td><?= number_format($total_bank_sorties,0,","," ") ?></td></tr>
                    <tr class="active"><td><strong>TOTAL DÉCAISSÉ</strong></td><td><strong><?= number_format($total_deja_paye,0,","," ") ?> FCFA</strong></td></tr>
                </table>

                <h5>Évolution mensuelle du décaissé</h5>
                <table class="table table-bordered">
                    <thead><tr><th>Mois</th><th>Montant décaissé (FCFA)</th></tr></thead>
                    <tbody>
                    <?php if (!empty($depenses_mensuel_labels)): ?>
                        <?php foreach ($depenses_mensuel_labels as $idx => $label): ?>
                            <tr><td><?= $label ?></td><td><?= number_format($decaissement_mensuel_total[$idx],0,","," ") ?> FCFA</td></tr>
                        <?php endforeach; ?>
                        <tr class="active"><td><strong>TOTAL</strong></td><td><strong><?= number_format($total_deja_paye,0,","," ") ?> FCFA</strong></td></tr>
                    <?php else: ?>
                        <tr><td colspan="2">Aucune donnée</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Résultat brut -->
<div class="modal fade" id="modalResultatBrut" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Détail du Résultat brut</h4></div>
            <div class="modal-body">
                <h5>Calcul du résultat brut</h5>
                <table class="table table-bordered">
                    <tr><th>Indicateur</th><th>Montant (FCFA)</th></tr>
                    <tr><td>CA Réalisé</td><td><?= number_format($ca_realise_global,0,","," ") ?></td></tr>
                    <tr><td>Total dépenses</td><td><?= number_format($total_depenses_global,0,","," ") ?></td></tr>
                    <tr class="info"><td><strong>Résultat brut</strong></td><td><strong><?= number_format($montant_net,0,","," ") ?> FCFA</strong></td></tr>
                </table>
                <div class="alert alert-<?= $montant_net >= 0 ? 'success' : 'danger' ?>">
                    <i class="fa fa-info-circle"></i> 
                    <?= $montant_net >= 0 ? '✅ Bénéfice généré sur la période' : '🔴 Perte enregistrée sur la période' ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Résultat net encaissé -->
<div class="modal fade" id="modalResultatNet" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Détail du Résultat net encaissé</h4></div>
            <div class="modal-body">
                <h5>Calcul du résultat net encaissé</h5>
                <table class="table table-bordered">
                    <tr><th>Indicateur</th><th>Montant (FCFA)</th></tr>
                    <tr><td>CA Encaissé</td><td><?= number_format($ca_encaisse_global,0,","," ") ?></td></tr>
                    <tr><td>Déjà payé / décaissé</td><td><?= number_format($total_deja_paye,0,","," ") ?></td></tr>
                    <tr class="info"><td><strong>Résultat net encaissé</strong></td><td><strong><?= number_format($resultat_net_encaisse,0,","," ") ?> FCFA</strong></td></tr>
                </table>
                <div class="alert alert-<?= $resultat_net_encaisse >= 0 ? 'success' : 'danger' ?>">
                    <i class="fa fa-info-circle"></i> 
                    <?= $resultat_net_encaisse >= 0 ? '✅ Trésorerie positive sur la période' : '🔴 Trésorerie négative sur la période' ?>
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
// Fonction pour afficher les détails d'un utilisateur UNIQUEMENT
function showUserDetails(userName) {
    $('#userDetailName').text(userName);
    $('#userDetailContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Chargement...</div>');
    
    <?php
    $user_data_js = [];
    foreach ($ca_by_user as $user) {
        $user_data_js[$user['user_name']] = [
            'user_name' => $user['user_name'],
            'ca_factures' => $user['ca_factures'],
            'ca_services' => $user['ca_services'],
            'ca_total' => $user['ca_total'],
            'encaisse_total' => $user['encaisse_total'],
            'reste_total' => $user['reste_total'],
            'nb_factures' => $user['nb_factures'],
            'nb_services' => $user['nb_services'],
            'taux_encaissement' => $user['taux_encaissement']
        ];
    }
    ?>
    var usersData = <?= json_encode($user_data_js) ?>;
    
    var data = usersData[userName];
    if (data) {
        var html = '<div class="row">';
        html += '<div class="col-md-6">';
        html += '<h5>📊 Résumé des performances</h5>';
        html += '<table class="table table-bordered">';
        html += '<tr><th>Indicateur</th><th>Valeur</th></tr>';
        html += '<tr><td>CA Factures</td><td><strong>' + formatNumber(data.ca_factures) + ' FCFA</strong></td></tr>';
        html += '<tr><td>CA Services</td><td><strong>' + formatNumber(data.ca_services) + ' FCFA</strong></td></tr>';
        html += '<tr class="info"><td><strong>CA TOTAL</strong></td><td><strong>' + formatNumber(data.ca_total) + ' FCFA</strong></td></tr>';
        html += '<tr><td>Montant encaissé</td><td><span class="label label-success">' + formatNumber(data.encaisse_total) + ' FCFA</span></td></tr>';
        html += '<tr><td>Reste à encaisser</td><td><span class="label label-' + (data.reste_total > 0 ? 'danger' : 'success') + '">' + formatNumber(data.reste_total) + ' FCFA</span></td></tr>';
        html += '<tr><td>Taux d\'encaissement</td><td><div class="progress"><div class="progress-bar progress-bar-' + (data.taux_encaissement >= 90 ? 'success' : (data.taux_encaissement >= 50 ? 'warning' : 'danger')) + '" style="width:' + data.taux_encaissement + '%;">' + data.taux_encaissement + '%</div></div></td></tr>';
        html += '</table>';
        html += '</div>';
        html += '<div class="col-md-6">';
        html += '<h5>📈 Détail des transactions</h5>';
        html += '<table class="table table-bordered">';
        html += '<tr><th>Type</th><th>Nombre</th></tr>';
        html += '<tr><td>Factures</td><td><span class="badge badge-primary">' + data.nb_factures + '</span></td></tr>';
        html += '<tr><td>Services</td><td><span class="badge badge-info">' + data.nb_services + '</span></td></tr>';
        html += '<tr class="info"><td><strong>Total</strong></td><td><span class="badge badge-success" style="font-size:14px;padding:8px 12px;">' + (data.nb_factures + data.nb_services) + '</span></td></tr>';
        html += '</table>';
        html += '<div class="alert alert-info" style="margin-top:15px;">';
        html += '<i class="fa fa-info-circle"></i> ' + (data.taux_encaissement >= 90 ? '✅ Excellent taux d\'encaissement !' : (data.taux_encaissement >= 50 ? '⚠️ Taux d\'encaissement moyen à améliorer' : '🔴 Taux d\'encaissement faible, attention !'));
        html += '</div>';
        html += '</div>';
        html += '</div>';
        $('#userDetailContent').html(html);
    } else {
        $('#userDetailContent').html('<div class="alert alert-danger">Aucune donnée trouvée pour cet utilisateur.</div>');
    }
    
    $('#modalUserDetails').modal('show');
}

function formatNumber(num) {
    return new Intl.NumberFormat('fr-FR').format(num);
}

// ================== GRAPHIQUES ==================

<?php if ($has_any_data && !empty($ca_by_user)): ?>
// 1. Graphique CA par utilisateur (barres)
(function() {
    var canvas = document.getElementById('chartCAUser');
    if (!canvas) { console.warn("Canvas chartCAUser introuvable"); return; }
    if (typeof Chart === 'undefined') { console.error('Chart.js non chargé'); return; }
    
    var labels = <?= json_encode(array_column($ca_by_user, 'user_name')) ?>;
    var caTotal = <?= json_encode(array_column($ca_by_user, 'ca_total')) ?>;
    var encaisseTotal = <?= json_encode(array_column($ca_by_user, 'encaisse_total')) ?>;
    var resteTotal = <?= json_encode(array_column($ca_by_user, 'reste_total')) ?>;
    
    new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'CA Total',
                    data: caTotal,
                    backgroundColor: 'rgba(0,166,90,0.7)',
                    borderColor: 'rgba(0,166,90,1)',
                    borderWidth: 1
                },
                {
                    label: 'Encaissé',
                    data: encaisseTotal,
                    backgroundColor: 'rgba(0,192,239,0.7)',
                    borderColor: 'rgba(0,192,239,1)',
                    borderWidth: 1
                },
                {
                    label: 'Reste',
                    data: resteTotal,
                    backgroundColor: 'rgba(221,75,57,0.7)',
                    borderColor: 'rgba(221,75,57,1)',
                    borderWidth: 1
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
                            return ctx.dataset.label + ': ' + ctx.raw.toLocaleString('fr-FR') + ' FCFA';
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
                            return value.toLocaleString('fr-FR');
                        }
                    }
                },
                x: {
                    ticks: {
                        autoSkip: false,
                        font: { size: 10 }
                    }
                }
            }
        }
    });
})();

<?php if (!empty($mois_labels_evolution) && count($ca_by_user) > 1): ?>
// 2. Graphique évolution mensuelle par utilisateur
(function() {
    var canvas = document.getElementById('chartEvolutionUser');
    if (!canvas) { console.warn("Canvas chartEvolutionUser introuvable"); return; }
    if (typeof Chart === 'undefined') { console.error('Chart.js non chargé'); return; }
    
    var moisLabels = <?= json_encode($mois_labels_evolution) ?>;
    var moisLabelsFr = moisLabels.map(function(m) {
        var parts = m.split('-');
        var moisFr = ['Janv','Fév','Mars','Avr','Mai','Juin','Juil','Août','Sept','Oct','Nov','Déc'];
        return moisFr[parseInt(parts[1])-1] + ' ' + parts[0];
    });
    
    var users = <?= json_encode(array_column($ca_by_user, 'user_name')) ?>;
    var colors = ['#00a65a','#00c0ef','#dd4b39','#f39c12','#3c8dbc','#605ca8','#d81b60','#ff851b','#01ff70','#39cccc'];
    
    var datasets = [];
    users.forEach(function(user, idx) {
        var data = [];
        moisLabels.forEach(function(mois) {
            var value = <?= json_encode($evolution_mensuelle_by_user) ?>[user] ? <?= json_encode($evolution_mensuelle_by_user) ?>[user][mois] || 0 : 0;
            data.push(value);
        });
        datasets.push({
            label: user,
            data: data,
            borderColor: colors[idx % colors.length],
            backgroundColor: 'transparent',
            borderWidth: 2,
            fill: false,
            tension: 0.3
        });
    });
    
    new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels: moisLabelsFr,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return ctx.dataset.label + ': ' + ctx.raw.toLocaleString('fr-FR') + ' FCFA';
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
                            return value.toLocaleString('fr-FR');
                        }
                    }
                }
            }
        }
    });
})();
<?php endif; ?>
<?php endif; ?>

<?php if (!empty($depenses_mensuel_labels)): ?>
// 3. Évolution mensuelle des dépenses
(function() {
    var canvas = document.getElementById('combinedExpensesChart');
    if (!canvas) { console.warn("Canvas combinedExpensesChart introuvable"); return; }
    if (typeof Chart === 'undefined') { console.error('Chart.js non chargé'); return; }
    new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= json_encode($depenses_mensuel_labels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
            datasets: [
                {
                    label: 'Sorties de caisse',
                    data: <?= json_encode($depenses_mensuel_caisse) ?>,
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231,76,60,0.1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'Sorties banque',
                    data: <?= json_encode($depenses_mensuel_bank) ?>,
                    borderColor: '#6f42c1',
                    backgroundColor: 'rgba(111,66,193,0.1)',
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
                    title: { display: true, text: 'Montant (FCFA)' },
                    ticks: { callback: function(value) { return value.toLocaleString(); } }
                }
            }
        }
    });
})();
<?php endif; ?>

</script>
</body>
</html>