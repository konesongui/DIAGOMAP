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
// GESTION DES FILTRES DE DATE
// ===============================
// Valeurs par défaut : année en cours
$date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-01-01');
$date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-12-31');

// Vérifier que les dates sont valides
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_debut)) $date_debut = date('Y-01-01');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_fin)) $date_fin = date('Y-12-31');

// Pour l'affichage
$date_debut_aff = date('d/m/Y', strtotime($date_debut));
$date_fin_aff = date('d/m/Y', strtotime($date_fin));

// ===============================
// GESTION DES EXPORTS
// ===============================
if (isset($_GET['export']) && in_array($_GET['export'], ['excel', 'pdf'])) {
    // Récupération des mêmes données filtrées pour l'export
    // Les fonctions d'export seront appelées plus bas après calcul des données
    $export_type = $_GET['export'];
    // On continue le script pour calculer les données, puis on appelle l'export
}

// ===============================
// FONCTIONS UTILITAIRES
// ===============================
function formatMontant($montant) {
    return number_format($montant, 0, ",", " ") . " FCFA";
}

function getMoisLabels($debut, $fin) {
    $labels = [];
    $current = new DateTime($debut);
    $end = new DateTime($fin);
    $end->modify('last day of this month');
    while ($current <= $end) {
        $labels[] = $current->format('M Y');
        $current->modify('first day of next month');
    }
    return $labels;
}

// ===============================
// REQUÊTES AVEC FILTRES DE DATE
// ===============================

// 1. CHIFFRE D'AFFAIRES RÉALISÉ, ENCAISSÉ ET CRÉANCE
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

// 2. OBJECTIFS COMMERCIAUX (sur la période filtrée)
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
$ecart_objectif = $ca_realise - $objectif_periode;
$reste_objectif = max(0, $objectif_periode - $ca_realise);

// 3. ÉVOLUTION MENSUELLE (CA réalisé, encaissé, créance) avec groupement année-mois
$sqlEvolutionMensuelle = "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as annee_mois,
        SUM(ca_realise) AS ca_realise,
        SUM(ca_encaisse) AS ca_encaisse
    FROM (
        SELECT 
            created_at,
            total_ttc AS ca_realise,
            amount_paid AS ca_encaisse
        FROM invoices
        WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
        
        UNION ALL
        
        SELECT 
            created_at,
            total_ttc AS ca_realise,
            amount_paid AS ca_encaisse
        FROM quotes_selling
        WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
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
        $mois_labels_evolution[] = date('M Y', strtotime($row['annee_mois'] . '-01'));
        $ca_realise_mensuel[] = (float)$row['ca_realise'];
        $ca_encaisse_mensuel[] = (float)$row['ca_encaisse'];
        $creance_mensuel[] = (float)$row['ca_realise'] - (float)$row['ca_encaisse'];
    }
}

// 4. TOP 5 CLIENTS PAR CRÉANCE
$sqlTopClientsCreance = "
    SELECT 
        client_nom,
        SUM(ca_realise_client) AS ca_realise_client,
        SUM(ca_encaisse_client) AS ca_encaisse_client
    FROM (
        SELECT 
            c.item_supplier AS client_nom,
            i.total_ttc AS ca_realise_client,
            i.amount_paid AS ca_encaisse_client
        FROM invoices i
        JOIN clients c ON i.customer_id = c.id
        WHERE i.created_at BETWEEN '$date_debut' AND '$date_fin'
        
        UNION ALL
        
        SELECT 
            c.item_supplier AS client_nom,
            q.total_ttc AS ca_realise_client,
            q.amount_paid AS ca_encaisse_client
        FROM quotes_selling q
        JOIN clients c ON q.customer_id = c.id
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

// 5. ÉVOLUTION DU TAUX D'ENCAISSEMENT
$sqlTauxMensuel = "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as annee_mois,
        CASE 
            WHEN SUM(ca_realise) > 0 THEN ROUND((SUM(ca_encaisse) / SUM(ca_realise)) * 100, 2)
            ELSE 0
        END AS taux_encaissement
    FROM (
        SELECT 
            created_at,
            total_ttc AS ca_realise,
            amount_paid AS ca_encaisse
        FROM invoices
        WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
        
        UNION ALL
        
        SELECT 
            created_at,
            total_ttc AS ca_realise,
            amount_paid AS ca_encaisse
        FROM quotes_selling
        WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
    ) AS all_transactions
    GROUP BY annee_mois
    ORDER BY annee_mois ASC
";
$resTaux = $conn->query($sqlTauxMensuel);
$taux_mensuel_labels = [];
$taux_mensuel_values = [];
if ($resTaux && $resTaux->num_rows > 0) {
    while ($row = $resTaux->fetch_assoc()) {
        $taux_mensuel_labels[] = date('M Y', strtotime($row['annee_mois'] . '-01'));
        $taux_mensuel_values[] = (float)$row['taux_encaissement'];
    }
}

// 6. RÉPARTITION DU CA PAR STATUT DE PAIEMENT
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
$total_ca_status = array_sum($status_ca);
$status_percentages = [];
foreach($status_ca as $ca) {
    $status_percentages[] = $total_ca_status > 0 ? round(($ca / $total_ca_status) * 100, 1) : 0;
}

// 7. DÉPENSES (FACTURES FOURNISSEUR) avec filtres
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

// Évolution mensuelle des achats
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
        $achats_mensuel_labels[] = date('M Y', strtotime($row['annee_mois'] . '-01'));
        $achats_mensuel[] = (float)$row['total_achats'];
        $paye_achats_mensuel[] = (float)$row['paye_achats'];
        $reste_achats_mensuel[] = (float)$row['total_achats'] - (float)$row['paye_achats'];
    }
}

// Top 4 fournisseurs par dette
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

// 8. Informations complémentaires
$sqlNbTransactions = "SELECT COUNT(*) as nb FROM (
    SELECT id FROM invoices WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
    UNION ALL
    SELECT id FROM quotes_selling WHERE created_at BETWEEN '$date_debut' AND '$date_fin'
) AS all_transactions";
$resNbTransactions = $conn->query($sqlNbTransactions);
$nb_factures = ($resNbTransactions && ($row = $resNbTransactions->fetch_assoc())) ? $row['nb'] : 0;

$has_any_data = $ca_realise > 0 || $ca_encaisse > 0 || $creance > 0;
$has_any_achats_data = $total_achats_ttc > 0;

// ===============================
// GESTION DES EXPORTS (Excel et PDF)
// ===============================
if (isset($export_type)) {
    if ($export_type === 'excel') {
        // Export Excel (HTML avec en-tête .xls)
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=export_dashboard_{$date_debut}_{$date_fin}.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '<html><body>';
        echo '<h1>Tableau de bord du ' . $date_debut_aff . ' au ' . $date_fin_aff . '</h1>';

        // KPIs Ventes
        echo '<h2>Chiffre d\'affaires</h2>';
        echo '<table border="1">';
        echo '<tr><th>CA Réalisé</th><th>CA Encaissé</th><th>Créance</th><th>Taux d\'encaissement</th></tr>';
        echo '<tr><td>' . formatMontant($ca_realise) . '</td><td>' . formatMontant($ca_encaisse) . '</td><td>' . formatMontant($creance) . '</td><td>' . $taux_encaissement . '%</td></tr>';
        echo '</table>';

        // Objectif
        echo '<h2>Objectif commercial</h2>';
        echo '<table border="1">';
        echo '<tr><th>Objectif période</th><th>CA Réalisé</th><th>Taux réalisation</th><th>Reste objectif</th></tr>';
        echo '<tr><td>' . formatMontant($objectif_periode) . '</td><td>' . formatMontant($ca_realise) . '</td><td>' . $taux_realisation . '%</td><td>' . formatMontant($reste_objectif) . '</td></tr>';
        echo '</table>';

        // Top clients créance
        if (!empty($top_clients)) {
            echo '<h2>Top 5 clients par créance</h2>';
            echo '<table border="1">';
            echo '<tr><th>Client</th><th>Créance</th><th>%</th></tr>';
            foreach ($top_clients as $i => $client) {
                echo '<tr><td>' . htmlspecialchars($client) . '</td><td>' . formatMontant($creance_clients[$i]) . '</td><td>' . $top_clients_percentages[$i] . '%</td></tr>';
            }
            echo '</table>';
        }

        // Statuts paiement
        if (!empty($status_labels)) {
            echo '<h2>Répartition par statut de paiement</h2>';
            echo '<table border="1">';
            echo '<tr><th>Statut</th><th>CA Total</th><th>Encaissé</th><th>Reste</th><th>% Encaissé</th></tr>';
            foreach ($status_labels as $i => $status) {
                $pourc_enc = $status_ca[$i] > 0 ? round(($status_encaisse[$i] / $status_ca[$i]) * 100, 2) : 0;
                echo '<tr>';
                echo '<td>' . htmlspecialchars($status) . '</td>';
                echo '<td>' . formatMontant($status_ca[$i]) . '</td>';
                echo '<td>' . formatMontant($status_encaisse[$i]) . '</td>';
                echo '<td>' . formatMontant($status_reste[$i]) . '</td>';
                echo '<td>' . $pourc_enc . '%</td>';
                echo '</tr>';
            }
            echo '</table>';
        }

        // Dépenses
        echo '<h2>Dépenses fournisseurs</h2>';
        echo '<table border="1">';
        echo '<tr><th>Total achats TTC</th><th>Déjà payé</th><th>Reste à payer (dette)</th><th>Taux paiement</th></tr>';
        echo '<tr><td>' . formatMontant($total_achats_ttc) . '</td><td>' . formatMontant($achats_paye) . '</td><td>' . formatMontant($dette_fournisseur) . '</td><td>' . $taux_paiement_achats . '%</td></tr>';
        echo '</table>';

        if (!empty($top_fournisseurs)) {
            echo '<h2>Top 5 fournisseurs par dette</h2>';
            echo '<table border="1">';
            echo '<tr><th>Fournisseur</th><th>Decanvastte</th><th>%</th></tr>';
            foreach ($top_fournisseurs as $i => $fourn) {
                echo '<tr><td>' . htmlspecialchars($fourn) . '</td><td>' . formatMontant($dette_fournisseurs[$i]) . '</td><td>' . $top_fournisseurs_percentages[$i] . '%</td></tr>';
            }
            echo '</table>';
        }

        echo '</body></html>';
        exit;

    } elseif ($export_type === 'pdf') {
        // Vérifier si Dompdf est disponible
        if (!class_exists('Dompdf\Dompdf')) {
            die("Erreur: La bibliothèque Dompdf n'est pas installée. Pour exporter en PDF, veuillez installer Dompdf via Composer.");
        }

        require_once 'vendor/autoload.php'; // Ajustez le chemin selon votre structure

        $html_pdf = '<html><head><meta charset="UTF-8"><style>
            body { font-family: DejaVu Sans, sans-serif; margin: 20px; }
            h1 { color: #333; border-bottom: 2px solid #0073b7; padding-bottom: 10px; }
            h2 { color: #555; margin-top: 20px; border-left: 5px solid #0073b7; padding-left: 10px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .montant { text-align: right; }
            footer { font-size: 10px; text-align: center; margin-top: 30px; color: #777; }
        </style></head><body>';

        $html_pdf .= '<h1>Tableau de bord du ' . $date_debut_aff . ' au ' . $date_fin_aff . '</h1>';

        // KPIs Ventes
        $html_pdf .= '<h2>Chiffre d\'affaires</h2>';
        $html_pdf .= '<table>';
        $html_pdf .= '<tr><th>CA Réalisé</th><th>CA Encaissé</th><th>Créance</th><th>Taux d\'encaissement</th></tr>';
        $html_pdf .= '<tr><td>' . formatMontant($ca_realise) . '</td><td>' . formatMontant($ca_encaisse) . '</td><td>' . formatMontant($creance) . '</td><td>' . $taux_encaissement . '%</td></tr>';
        $html_pdf .= '</table>';

        // Objectif
        $html_pdf .= '<h2>Objectif commercial</h2>';
        $html_pdf .= '<table>';
        $html_pdf .= '<tr><th>Objectif période</th><th>CA Réalisé</th><th>Taux réalisation</th><th>Reste objectif</th></tr>';
        $html_pdf .= '<tr><td>' . formatMontant($objectif_periode) . '</td><td>' . formatMontant($ca_realise) . '</td><td>' . $taux_realisation . '%</td><td>' . formatMontant($reste_objectif) . '</td></tr>';
        $html_pdf .= '</table>';

        // Top clients
        if (!empty($top_clients)) {
            $html_pdf .= '<h2>Top 5 clients par créance</h2>';
            $html_pdf .= '<table>';
            $html_pdf .= '<tr><th>Client</th><th>Créance</th><th>%</th></tr>';
            foreach ($top_clients as $i => $client) {
                $html_pdf .= '<tr><td>' . htmlspecialchars($client) . '</td><td>' . formatMontant($creance_clients[$i]) . '</td><td>' . $top_clients_percentages[$i] . '%</td></tr>';
            }
            $html_pdf .= '</table>';
        }

        // Statuts
        if (!empty($status_labels)) {
            $html_pdf .= '<h2>Répartition par statut de paiement</h2>';
            $html_pdf .= '<table>';
            $html_pdf .= '<tr><th>Statut</th><th>CA Total</th><th>Encaissé</th><th>Reste</th><th>% Encaissé</th></tr>';
            foreach ($status_labels as $i => $status) {
                $pourc_enc = $status_ca[$i] > 0 ? round(($status_encaisse[$i] / $status_ca[$i]) * 100, 2) : 0;
                $html_pdf .= '<tr>';
                $html_pdf .= '<td>' . htmlspecialchars($status) . '</td>';
                $html_pdf .= '<td>' . formatMontant($status_ca[$i]) . '</td>';
                $html_pdf .= '<td>' . formatMontant($status_encaisse[$i]) . '</td>';
                $html_pdf .= '<td>' . formatMontant($status_reste[$i]) . '</td>';
                $html_pdf .= '<td>' . $pourc_enc . '%</td>';
                $html_pdf .= '</tr>';
            }
            $html_pdf .= '</table>';
        }

        // Dépenses
        $html_pdf .= '<h2>Dépenses fournisseurs</h2>';
        $html_pdf .= '<table>';
        $html_pdf .= '<tr><th>Total achats TTC</th><th>Déjà payé</th><th>Reste à payer (dette)</th><th>Taux paiement</th></tr>';
        $html_pdf .= '<tr><td>' . formatMontant($total_achats_ttc) . '</td><td>' . formatMontant($achats_paye) . '</td><td>' . formatMontant($dette_fournisseur) . '</td><td>' . $taux_paiement_achats . '%</td></tr>';
        $html_pdf .= '</table>';

        if (!empty($top_fournisseurs)) {
            $html_pdf .= '<h2>Top 5 fournisseurs par dette</h2>';
            $html_pdf .= '<table>';
            $html_pdf .= '<tr><th>Fournisseur</th><th>Dette</th><th>%</th></tr>';
            foreach ($top_fournisseurs as $i => $fourn) {
                $html_pdf .= '<tr><td>' . htmlspecialchars($fourn) . '</td><td>' . formatMontant($dette_fournisseurs[$i]) . '</td><td>' . $top_fournisseurs_percentages[$i] . '%</td></tr>';
            }
            $html_pdf .= '</table>';
        }

        $html_pdf .= '<footer>Généré le ' . date('d/m/Y H:i') . '</footer>';
        $html_pdf .= '</body></html>';

        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml($html_pdf);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("dashboard_{$date_debut}_{$date_fin}.pdf", array("Attachment" => true));
        exit;
    }
}

// ===============================
// FIN EXPORT - AFFICHAGE NORMAL DU DASHBOARD
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
        .small-box:hover { transform: translateY(-3px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
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
        .icon-dette-fournisseur { background: #e74c3c; }A
        .box { border-radius:10px; box-shadow:0 3px 6px rgba(0,0,0,0.1); margin-bottom:20px; }



        /* 1. Pour les graphiques en barres / lignes (évolutions) */
        canvas.evolution-chart {
            width: 100% !important;
            height: 300px !important;
        }

        /* 2. Pour les graphiques circulaires (doughnut, pie) */
        canvas.circle-chart {
            width: auto !important;
            height: auto !important;
            max-width: 300px;
            max-height: 300px;
            display: block;
            margin: 0 auto;
        }

        .btn-export { margin-left: 10px; }
        .comparison-row {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 5px solid #ffc107;
        }
    </style>
</head>
<body>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-money"></i> Chiffre d'affaires & Créances & Dépenses</h1>
    </section>

    <section class="content">
        <!-- Formulaire de filtre par période -->
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
            <p class="text-muted" style="margin-top: 10px;">Période affichée : du <strong><?= $date_debut_aff ?></strong> au <strong><?= $date_fin_aff ?></strong></p>
        </div>

        <!-- KPIs VENTES -->
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($ca_realise, 0, ",", " ") ?> FCFA</h3>
                        <p>CA Réalisé</p>
                        <small>Factures + Point de vente</small>
                    </div>
                    <div class="icon icon-ca-realise"><i class="fa fa-line-chart"></i></div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($ca_encaisse, 0, ",", " ") ?> FCFA</h3>
                        <p>CA Encaissé</p>
                        <small>Taux: <?= $taux_encaissement ?>%</small>
                    </div>
                    <div class="icon icon-ca-encaisse"><i class="fa fa-credit-card"></i></div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($creance, 0, ",", " ") ?> FCFA</h3>
                        <p>Créance</p>
                        <small>Reste à encaisser</small>
                    </div>
                    <div class="icon icon-creance"><i class="fa fa-exclamation-triangle"></i></div>
                </div>
            </div>
        </div>

        <!-- KPIs DÉPENSES -->
        <div class="row">
            <div class="col-md-12"><h3 class="page-header"><i class="fa fa-shopping-cart"></i> Dépenses - Factures fournisseur</h3></div>
            <div class="col-md-4 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($total_achats_ttc, 0, ",", " ") ?> FCFA</h3>
                        <p>Total achats TTC</p>
                        <small>Factures fournisseur</small>
                    </div>
                    <div class="icon icon-achats"><i class="fa fa-truck"></i></div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($achats_paye, 0, ",", " ") ?> FCFA</h3>
                        <p>Déjà payé aux fournisseurs</p>
                        <small>Taux: <?= $taux_paiement_achats ?>%</small>
                    </div>
                    <div class="icon icon-achats-paye"><i class="fa fa-money"></i></div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($dette_fournisseur, 0, ",", " ") ?> FCFA</h3>
                        <p>Reste à payer (dette)</p>
                        <small>Échéances fournisseurs</small>
                    </div>
                    <div class="icon icon-dette-fournisseur"><i class="fa fa-clock-o"></i></div>
                </div>
            </div>
        </div>

        <!-- OBJECTIF COMMERCIAL -->
        <div class="row">
            <div class="col-md-12">
                <div class="comparison-row">
                    <div class="row">
                        <div class="col-md-3 comparison-item">
                            <div class="comparison-label"><i class="fa fa-bullseye"></i> OBJECTIF PÉRIODE</div>
                            <div class="comparison-value" style="color: #0073b7;"><?= number_format($objectif_periode, 0, ",", " ") ?> FCFA</div>
                        </div>
                        <div class="col-md-3 comparison-item">
                            <div class="comparison-label"><i class="fa fa-line-chart"></i> CA RÉALISÉ</div>
                            <div class="comparison-value" style="color: #00a65a;"><?= number_format($ca_realise, 0, ",", " ") ?> FCFA</div>
                        </div>
                        <div class="col-md-2 comparison-item">
                            <div class="comparison-label"><i class="fa fa-percent"></i> TAUX</div>
                            <div class="comparison-value" style="color: <?= $taux_realisation >= 100 ? '#00a65a' : ($taux_realisation >= 70 ? '#f39c12' : '#dd4b39') ?>;"><?= $taux_realisation ?>%</div>
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
            <!-- Graphique répartition CA -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-header" ><h3 class="box-title"><i class="fa fa-pie-chart"></i> Répartition du CA</h3></div>
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

            <!-- Top clients créance -->
            <div class="row">


            </div>

            <!-- Statuts paiement -->
            <?php if (!empty($status_labels)): ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-default">
                            <div class="box-header"><h3 class="box-title"><i class="fa fa-list"></i> Détail par statut de paiement</h3></div>
                            <div class="box-body">
                                <table class="table table-bordered">
                                    <thead><tr><th>Statut</th><th>CA Total</th><th>Déjà encaissé</th><th>Reste à encaisser</th><th>% Encaissé</th></tr></thead>
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
                                            <td>
                                                <div class="progress" style="margin-bottom:0; height:20px;">
                                                    <div class="progress-bar progress-bar-<?= $badge_class ?>" style="width:<?= $pourcentage ?>%"><?= $pourcentage ?>%</div>
                                                </div>
                                            </td>
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
            data: {
                labels: ['CA Total', 'Déjà encaissé (<?= $pourcentage_encaisse ?>%)', 'Reste à encaisser (<?= $pourcentage_reste ?>%)'],
                datasets: [{
                    data: [<?= $ca_realise ?>, <?= $ca_encaisse ?>, <?= $creance ?>],
                    backgroundColor: ['rgba(0,115,183,0.7)', 'rgba(40,167,69,0.8)', 'rgba(220,53,69,0.8)'],
                    borderWidth: 2
                }]
            },
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
    }
</script>
</body>
</html>