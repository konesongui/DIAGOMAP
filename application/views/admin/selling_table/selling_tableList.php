<style>
    /* ==================== STYLES GLOBAUX ==================== */
    .small-box .icon {
        position: absolute;
        top: 18px;
        right: 18px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        transition: all 0.2s;
    }
    .icon-ca { background: #2ecc71 !important; }
    .icon-croissance { background: #3498db !important; }
    .icon-clients { background: #9b59b6 !important; }
    .icon-impayes { background: #e67e22 !important; }
    .icon-services { background: #8e44ad !important; }
    .icon-montant { background: #e74c3c !important; }
    .small-box .icon i {
        font-size: 28px;
        color: white !important;
        background: transparent !important;
        line-height: 1;
    }
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
    .small-box {
        border-radius: 10px;
        background: #ffffff;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        padding: 20px;
        position: relative;
        overflow: hidden;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }
    .small-box .inner h3 {
        margin: 0 0 10px 0;
        font-size: 17px;
        font-weight: bold;
        color: #2c3e50;
    }
    .small-box .inner p { color: #6c757d; margin-bottom: 0; }
    .small-box .icon {
        position: absolute;
        top: 5px;
        right: 3px;
        font-size: 45px;
        opacity: 0.8;
        border-radius: 50%;
        width: 52px;
        height: 49px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .global-progress {
        margin-top: 10px;
        margin-bottom: 15px;
        background: #e9ecef;
        border-radius: 10px;
        height: 12px;
        overflow: hidden;
    }
    .global-progress-bar {
        height: 100%;
        border-radius: 10px;
        transition: width 0.5s ease;
    }
    .box { border-radius: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.1); margin-bottom: 20px; }
    canvas { width: 100% !important; height: 300px !important; }
    .content-wrapper { padding: 20px; background: #f4f6f9; }

    .table-scroll {
        max-height: 400px;
        overflow-y: auto;
    }
    .table-scroll table {
        margin-bottom: 0;
    }
</style>

<?php
// ===============================
// CONNEXION BDD (CodeIgniter)
// ===============================
$CI = &get_instance();
$conn = new mysqli($CI->db->hostname, $CI->db->username, $CI->db->password, $CI->db->database);
if ($conn->connect_error) die("Erreur de connexion: " . $conn->connect_error);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ===============================
// 1. CHIFFRE D'AFFAIRES GLOBAL (Factures + Services)
// ===============================

// 1a. CA des factures (invoices) - Utilisation de amount_paid pour le CA réalisé
$sqlCAFactures = "SELECT SUM(amount_paid) AS total FROM invoices WHERE YEAR(created_at) = YEAR(CURDATE()) AND cancelled_at IS NULL";
$resCAFactures = $conn->query($sqlCAFactures);
$ca_factures = ($resCAFactures && ($r = $resCAFactures->fetch_assoc())) ? (float)$r['total'] : 0;

// 1b. CA des commandes de services (quotes_selling) - status = 2 (confirmé/vendu)
$sqlCAServices = "SELECT SUM(total_ttc) AS total FROM quotes_selling WHERE YEAR(created_at) = YEAR(CURDATE()) AND status = 2";
$resCAServices = $conn->query($sqlCAServices);
$ca_services = ($resCAServices && ($r = $resCAServices->fetch_assoc())) ? (float)$r['total'] : 0;

// CA total = factures + services
$ca_total = $ca_factures + $ca_services;

// ===============================
// 2. CROISSANCE MENSUELLE (Factures + Services)
// ===============================

// Mois courant - Factures
$sqlMoisCourantFactures = "SELECT SUM(amount_paid) AS ca FROM invoices WHERE YEAR(created_at)=YEAR(CURDATE()) AND MONTH(created_at)=MONTH(CURDATE()) AND cancelled_at IS NULL";
$res1a = $conn->query($sqlMoisCourantFactures);
$ca_factures_mois_courant = ($res1a && ($r1a = $res1a->fetch_assoc())) ? (float)$r1a['ca'] : 0;

// Mois courant - Services (status = 2)
$sqlMoisCourantServices = "SELECT SUM(total_ttc) AS ca FROM quotes_selling WHERE YEAR(created_at)=YEAR(CURDATE()) AND MONTH(created_at)=MONTH(CURDATE()) AND status = 2";
$res1b = $conn->query($sqlMoisCourantServices);
$ca_services_mois_courant = ($res1b && ($r1b = $res1b->fetch_assoc())) ? (float)$r1b['ca'] : 0;

$ca_mois_courant = $ca_factures_mois_courant + $ca_services_mois_courant;

// Mois précédent - Factures
$sqlMoisPrecedentFactures = "SELECT SUM(amount_paid) AS ca FROM invoices WHERE (YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())-1 AND cancelled_at IS NULL) OR (MONTH(CURDATE()) = 1 AND YEAR(created_at) = YEAR(CURDATE())-1 AND MONTH(created_at) = 12 AND cancelled_at IS NULL)";
$res2a = $conn->query($sqlMoisPrecedentFactures);
$ca_factures_mois_precedent = ($res2a && ($r2a = $res2a->fetch_assoc())) ? (float)$r2a['ca'] : 0;

// Mois précédent - Services (status = 2)
$sqlMoisPrecedentServices = "SELECT SUM(total_ttc) AS ca FROM quotes_selling WHERE (YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())-1 AND status = 2) OR (MONTH(CURDATE()) = 1 AND YEAR(created_at) = YEAR(CURDATE())-1 AND MONTH(created_at) = 12 AND status = 2)";
$res2b = $conn->query($sqlMoisPrecedentServices);
$ca_services_mois_precedent = ($res2b && ($r2b = $res2b->fetch_assoc())) ? (float)$r2b['ca'] : 0;

$ca_mois_precedent = $ca_factures_mois_precedent + $ca_services_mois_precedent;
$taux_croissance = ($ca_mois_precedent > 0) ? round((($ca_mois_courant - $ca_mois_precedent) / $ca_mois_precedent) * 100, 2) : 0;

// ===============================
// 3. NOUVEAUX CLIENTS + FACTURES IMPAYÉES
// ===============================
$sqlClients = "SELECT COUNT(*) AS nb FROM clients WHERE YEAR(created_at) = YEAR(CURDATE())";
$resClients = $conn->query($sqlClients);
$nouveaux_clients = ($resClients && ($r3 = $resClients->fetch_assoc())) ? (int)$r3['nb'] : 0;

$sqlImpaye = "SELECT COUNT(*) AS nb FROM invoices WHERE status = 1 AND cancelled_at IS NULL";
$resImpaye = $conn->query($sqlImpaye);
$factures_impayees = ($resImpaye && ($r4 = $resImpaye->fetch_assoc())) ? (int)$r4['nb'] : 0;

// ===============================
// 4. STATISTIQUES SERVICES VENDUS
// ===============================
// Total des commandes de services (status = 2)
$sqlTotalCommandes = "SELECT COUNT(*) AS total FROM quotes_selling WHERE YEAR(created_at) = YEAR(CURDATE()) AND status = 2";
$resTotalCommandes = $conn->query($sqlTotalCommandes);
$total_commandes = ($resTotalCommandes && ($r = $resTotalCommandes->fetch_assoc())) ? (int)$r['total'] : 0;

// Total des lignes de services (status = 2)
$sqlTotalLignes = "SELECT COUNT(*) AS total FROM quote_items_selling qis INNER JOIN quotes_selling qs ON qis.quote_id = qs.id WHERE YEAR(qs.created_at) = YEAR(CURDATE()) AND qs.status = 2";
$resTotalLignes = $conn->query($sqlTotalLignes);
$total_lignes = ($resTotalLignes && ($r = $resTotalLignes->fetch_assoc())) ? (int)$r['total'] : 0;

// Quantité totale de services vendus (status = 2)
$sqlTotalQuantite = "SELECT SUM(qis.quantity) AS total FROM quote_items_selling qis INNER JOIN quotes_selling qs ON qis.quote_id = qs.id WHERE YEAR(qs.created_at) = YEAR(CURDATE()) AND qs.status = 2";
$resTotalQuantite = $conn->query($sqlTotalQuantite);
$total_quantite = ($resTotalQuantite && ($r = $resTotalQuantite->fetch_assoc())) ? (float)$r['total'] : 0;

// Montant total des lignes de services (status = 2)
$sqlTotalMontantLignes = "SELECT SUM(qis.line_total) AS total FROM quote_items_selling qis INNER JOIN quotes_selling qs ON qis.quote_id = qs.id WHERE YEAR(qs.created_at) = YEAR(CURDATE()) AND qs.status = 2";
$resTotalMontantLignes = $conn->query($sqlTotalMontantLignes);
$total_montant_lignes = ($resTotalMontantLignes && ($r = $resTotalMontantLignes->fetch_assoc())) ? (float)$r['total'] : 0;

// ===============================
// 5. CA MENSUEL (Factures + Services)
// ===============================
$mois = []; $ca_mensuel = [];
$moisNoms = [1=>"Jan","Fév","Mar","Avr","Mai","Juin","Juil","Août","Sept","Oct","Nov","Déc"];

for ($m = 1; $m <= 12; $m++) {
    // CA factures du mois
    $sqlMoisFactures = "SELECT SUM(amount_paid) AS total FROM invoices WHERE YEAR(created_at)=YEAR(CURDATE()) AND MONTH(created_at)=$m AND cancelled_at IS NULL";
    $resMoisFactures = $conn->query($sqlMoisFactures);
    $ca_mois_factures = ($resMoisFactures && ($r = $resMoisFactures->fetch_assoc())) ? (float)$r['total'] : 0;

    // CA services du mois (status = 2)
    $sqlMoisServices = "SELECT SUM(total_ttc) AS total FROM quotes_selling WHERE YEAR(created_at)=YEAR(CURDATE()) AND MONTH(created_at)=$m AND status = 2";
    $resMoisServices = $conn->query($sqlMoisServices);
    $ca_mois_services = ($resMoisServices && ($r = $resMoisServices->fetch_assoc())) ? (float)$r['total'] : 0;

    $mois[] = $moisNoms[$m];
    $ca_mensuel[] = $ca_mois_factures + $ca_mois_services;
}

// ===============================
// 6. CA PAR VILLE (Factures uniquement)
// ===============================
$sqlVille = "SELECT c.ville, SUM(i.amount_paid) AS total FROM invoices i JOIN clients c ON i.customer_id = c.id WHERE YEAR(i.created_at)=YEAR(CURDATE()) AND i.cancelled_at IS NULL GROUP BY c.ville";
$resVille = $conn->query($sqlVille);
$villes = []; $ca_villes = [];
if ($resVille) {
    while ($r6 = $resVille->fetch_assoc()) {
        $villes[] = $r6['ville'] ?? "Inconnu";
        $ca_villes[] = (float)$r6['total'];
    }
}

// ===============================
// 7. PERFORMANCE PAR COMMERCIAL (Factures + Services)
// ===============================
$ca_commerciaux = [];

// CA des factures par commercial
$sqlComFactures = "SELECT user_name, SUM(amount_paid) AS total FROM invoices WHERE YEAR(created_at)=YEAR(CURDATE()) AND cancelled_at IS NULL GROUP BY user_name";
$resComFactures = $conn->query($sqlComFactures);
if ($resComFactures) {
    while ($r7 = $resComFactures->fetch_assoc()) {
        $user = trim($r7['user_name'] ?? 'Inconnu');
        $ca_commerciaux[$user] = (float)$r7['total'];
    }
}

// CA des services par commercial (status = 2)
$sqlComServices = "SELECT user_name, SUM(total_ttc) AS total FROM quotes_selling WHERE YEAR(created_at)=YEAR(CURDATE()) AND status = 2 GROUP BY user_name";
$resComServices = $conn->query($sqlComServices);
if ($resComServices) {
    while ($r7b = $resComServices->fetch_assoc()) {
        $user = trim($r7b['user_name'] ?? 'Inconnu');
        if (!isset($ca_commerciaux[$user])) $ca_commerciaux[$user] = 0;
        $ca_commerciaux[$user] += (float)$r7b['total'];
    }
}

// Objectifs
$objectifs = [];
$sqlGoals = "SELECT oa.commercial_name, SUM(oa.amount) AS total_objectif FROM objective_assignments oa INNER JOIN annual_objectives ao ON oa.annual_objective_id = ao.id WHERE YEAR(ao.date)=YEAR(CURDATE()) GROUP BY oa.commercial_name";
$resGoals = $conn->query($sqlGoals);
if ($resGoals) {
    while ($r8 = $resGoals->fetch_assoc()) {
        $com = trim($r8['commercial_name'] ?? 'Inconnu');
        $objectifs[$com] = (float)$r8['total_objectif'];
        if (!isset($ca_commerciaux[$com])) $ca_commerciaux[$com] = 0;
    }
}
$commerciaux = array_unique(array_merge(array_keys($ca_commerciaux), array_keys($objectifs)));
foreach ($commerciaux as $c) {
    if (!isset($objectifs[$c])) $objectifs[$c] = 0;
}

// Objectif directeur
$sqlDir = "SELECT amount, date FROM annual_objectives WHERE YEAR(date)=YEAR(CURDATE()) ORDER BY date DESC LIMIT 1";
$resDir = $conn->query($sqlDir);
$directeur = $resDir && ($dir = $resDir->fetch_assoc()) ? $dir : null;
$objectif_directeur_montant = $directeur ? (float)$directeur['amount'] : 0;
$objectif_directeur_date = $directeur ? $directeur['date'] : null;
$pourcentage_global = ($objectif_directeur_montant > 0) ? min(100, ($ca_total / $objectif_directeur_montant) * 100) : 0;
$bar_color = ($pourcentage_global >= 100) ? '#28a745' : '#007bff';

// ===============================
// 8. COMMANDES DE SERVICES PAR MOIS (status = 2)
// ===============================
$sqlCmdMensuel = "SELECT MONTH(created_at) AS mois, COUNT(*) AS total, SUM(total_ttc) AS montant FROM quotes_selling WHERE YEAR(created_at)=YEAR(CURDATE()) AND status = 2 GROUP BY mois ORDER BY mois ASC";
$resCmdMensuel = $conn->query($sqlCmdMensuel);
$cmd_mensuel = [];
$cmd_montant_mensuel = [];
for ($m = 1; $m <= 12; $m++) {
    $cmd_mensuel[$m] = 0;
    $cmd_montant_mensuel[$m] = 0;
}
if ($resCmdMensuel) {
    while ($d = $resCmdMensuel->fetch_assoc()) {
        $m = (int)$d['mois'];
        $cmd_mensuel[$m] = (int)$d['total'];
        $cmd_montant_mensuel[$m] = (float)$d['montant'];
    }
}

// ===============================
// 9. TOP 10 SERVICES LES PLUS VENDUS (Factures + Commandes status = 2)
// ===============================

// 9a. Services vendus via les commandes (quote_items_selling) avec status = 2
$sqlServicesCommandes = "SELECT 
    qis.item_name AS nom,
    SUM(qis.quantity) AS quantite_totale,
    SUM(qis.line_total) AS montant_total,
    COUNT(DISTINCT qis.quote_id) AS nb_commandes,
    'Commande' AS source
FROM quote_items_selling qis
INNER JOIN quotes_selling qs ON qis.quote_id = qs.id
WHERE YEAR(qs.created_at) = YEAR(CURDATE()) AND qs.status = 2
GROUP BY qis.item_id";

$resServicesCommandes = $conn->query($sqlServicesCommandes);
$services_commandes = [];
if ($resServicesCommandes) {
    while ($s = $resServicesCommandes->fetch_assoc()) {
        $services_commandes[] = $s;
    }
}

// 9b. Services vendus via les factures (invoice_items)
$possible_columns = ['line_total', 'total', 'subtotal', 'amount'];
$montant_column = null;
$cols = $conn->query("SHOW COLUMNS FROM invoice_items");
if ($cols) while ($col = $cols->fetch_assoc()) {
    if (in_array($col['Field'], $possible_columns)) { $montant_column = $col['Field']; break; }
}
if (!$montant_column) $montant_column = 'line_total';

$possible_date = ['invoice_date', 'created_at', 'date'];
$date_column = null;
$cols2 = $conn->query("SHOW COLUMNS FROM invoices");
if ($cols2) while ($col2 = $cols2->fetch_assoc()) {
    if (in_array($col2['Field'], $possible_date)) { $date_column = $col2['Field']; break; }
}
if (!$date_column) $date_column = 'created_at';

$sqlServicesFactures = "SELECT 
    s.name AS nom,
    SUM(ii.quantity) AS quantite_totale,
    SUM(ii.{$montant_column}) AS montant_total,
    COUNT(DISTINCT ii.invoice_id) AS nb_commandes,
    'Facture' AS source
FROM invoice_items ii
INNER JOIN services s ON ii.service_id = s.id
INNER JOIN invoices inv ON ii.invoice_id = inv.id
WHERE ii.item_type = 'service' AND YEAR(inv.{$date_column}) = YEAR(CURDATE()) AND inv.cancelled_at IS NULL
GROUP BY ii.service_id

UNION ALL

SELECT 
    i.name AS nom,
    SUM(ii.quantity) AS quantite_totale,
    SUM(ii.{$montant_column}) AS montant_total,
    COUNT(DISTINCT ii.invoice_id) AS nb_commandes,
    'Facture' AS source
FROM invoice_items ii
INNER JOIN item i ON ii.item_id = i.id
INNER JOIN invoices inv ON ii.invoice_id = inv.id
WHERE ii.item_type = 'product' AND YEAR(inv.{$date_column}) = YEAR(CURDATE()) AND inv.cancelled_at IS NULL
GROUP BY ii.item_id";

$resServicesFactures = $conn->query($sqlServicesFactures);
$services_factures = [];
if ($resServicesFactures) {
    while ($f = $resServicesFactures->fetch_assoc()) {
        $services_factures[] = $f;
    }
}

// 9c. Fusion des deux sources
$tous_services = [];

// Ajout des services des commandes
foreach ($services_commandes as $s) {
    $cle = $s['nom'];
    if (!isset($tous_services[$cle])) {
        $tous_services[$cle] = [
            'nom' => $s['nom'],
            'quantite_totale' => 0,
            'montant_total' => 0,
            'nb_commandes' => 0,
            'source' => []
        ];
    }
    $tous_services[$cle]['quantite_totale'] += (float)$s['quantite_totale'];
    $tous_services[$cle]['montant_total'] += (float)$s['montant_total'];
    $tous_services[$cle]['nb_commandes'] += (int)$s['nb_commandes'];
    $tous_services[$cle]['source'][] = 'Commande';
}

// Ajout des services des factures
foreach ($services_factures as $f) {
    $cle = $f['nom'];
    if (!isset($tous_services[$cle])) {
        $tous_services[$cle] = [
            'nom' => $f['nom'],
            'quantite_totale' => 0,
            'montant_total' => 0,
            'nb_commandes' => 0,
            'source' => []
        ];
    }
    $tous_services[$cle]['quantite_totale'] += (float)$f['quantite_totale'];
    $tous_services[$cle]['montant_total'] += (float)$f['montant_total'];
    $tous_services[$cle]['nb_commandes'] += (int)$f['nb_commandes'];
    $tous_services[$cle]['source'][] = 'Facture';
}

// Tri par montant total décroissant
usort($tous_services, function($a, $b) {
    return $b['montant_total'] <=> $a['montant_total'];
});
$top_services_global = array_slice($tous_services, 0, 10);

// Préparation des données pour le graphique
$items_global = [];
$montants_global = [];
$total_general_top10_global = 0;
foreach ($top_services_global as $item) {
    $items_global[] = $item['nom'];
    $montants_global[] = $item['montant_total'];
    $total_general_top10_global += $item['montant_total'];
}

// Fallback si aucune donnée
if (empty($items_global)) {
    $sqlTop = "(
        SELECT i.name AS product_name, SUM(ii.{$montant_column}) AS montant_vendu
        FROM invoice_items ii
        INNER JOIN item i ON ii.item_id = i.id
        INNER JOIN invoices inv ON ii.invoice_id = inv.id
        WHERE ii.item_type = 'product' AND YEAR(inv.{$date_column}) = YEAR(CURDATE()) AND inv.cancelled_at IS NULL
        GROUP BY ii.item_id
    )
    UNION ALL
    (
        SELECT s.name AS product_name, SUM(ii.{$montant_column}) AS montant_vendu
        FROM invoice_items ii
        INNER JOIN services s ON ii.service_id = s.id
        INNER JOIN invoices inv ON ii.invoice_id = inv.id
        WHERE ii.item_type = 'service' AND YEAR(inv.{$date_column}) = YEAR(CURDATE()) AND inv.cancelled_at IS NULL
        GROUP BY ii.service_id
    )
    ORDER BY montant_vendu DESC LIMIT 10";

    $resTop = $conn->query($sqlTop);
    if ($resTop && $resTop->num_rows > 0) {
        while ($row = $resTop->fetch_assoc()) {
            $items_global[] = $row['product_name'];
            $montants_global[] = (float)$row['montant_vendu'];
            $total_general_top10_global += (float)$row['montant_vendu'];
        }
    }
}

// ===============================
// 10. DERNIERS SERVICES VENDUS (status = 2)
// ===============================
$sqlDerniersServices = "SELECT 
    qs.quote_number AS numero_commande,
    c.item_supplier AS client,
    qs.objet,
    qs.quote_date AS date,
    qs.total_ttc AS montant_ttc,
    qis.item_name AS service,
    qis.quantity,
    qis.unit_price,
    qis.line_total
FROM quotes_selling qs
INNER JOIN quote_items_selling qis ON qs.id = qis.quote_id
LEFT JOIN clients c ON qs.customer_id = c.id
WHERE YEAR(qs.created_at) = YEAR(CURDATE()) AND qs.status = 2
ORDER BY qs.created_at DESC
LIMIT 10";
$resDerniersServices = $conn->query($sqlDerniersServices);
$derniers_services = [];
if ($resDerniersServices) {
    while ($d = $resDerniersServices->fetch_assoc()) {
        $derniers_services[] = $d;
    }
}

// ===============================
// 11. Stock faible
// ===============================
$sqlStock = "SELECT i.name, s.current_quantity, s.weighted_avg_price FROM stock s INNER JOIN item i ON s.item_id = i.id WHERE s.current_quantity <= 10 ORDER BY s.current_quantity ASC LIMIT 10";
$resStock = $conn->query($sqlStock);
$produits_stock_faible = [];
if ($resStock) while ($st = $resStock->fetch_assoc()) $produits_stock_faible[] = $st;

// ===============================
// 12. Paiements clients
// ===============================
$date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-01-01');
$date_fin   = isset($_GET['date_fin'])   ? $_GET['date_fin']   : date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_debut)) $date_debut = date('Y-01-01');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_fin))   $date_fin   = date('Y-m-d');

$sqlPay = "SELECT c.id AS client_id, c.item_supplier AS client_name, MAX(i.due_date) AS derniere_echeance,
                  SUM(i.total_ttc) AS total_a_payer, SUM(i.amount_paid) AS deja_paye, SUM(i.remaining_amount) AS reste_a_payer
           FROM invoices i LEFT JOIN clients c ON i.customer_id = c.id
           WHERE i.invoice_date BETWEEN ? AND ? AND i.cancelled_at IS NULL
           GROUP BY c.id, c.item_supplier";
$stmt = $conn->prepare($sqlPay);
$stmt->bind_param("ss", $date_debut, $date_fin);
$stmt->execute();
$result = $stmt->get_result();
$clients_paiements = [];
$total_general = ["total"=>0, "paye"=>0, "reste"=>0];
if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        $clients_paiements[] = [
            'id' => $r['client_id'],
            'name' => $r['client_name'],
            'echeance' => $r['derniere_echeance'],
            'total' => (float)$r['total_a_payer'],
            'paye' => (float)$r['deja_paye'],
            'reste' => (float)$r['reste_a_payer']
        ];
        $total_general['total'] += (float)$r['total_a_payer'];
        $total_general['paye'] += (float)$r['deja_paye'];
        $total_general['reste'] += (float)$r['reste_a_payer'];
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord commercial</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-dashboard"></i> Tableau de bord commercial</h1>
    </section>
    <section class="content">
        <!-- KPI - Chiffre d'affaires global -->
        <div class="row">
            <div class="col-md-3 col-sm-6"><div class="small-box"><div class="inner"><h3><?= number_format($ca_total,0,","," ") ?> FCFA</h3><p>Chiffre d'affaires réalisé</p></div><div class="icon icon-ca"><i class="fa fa-line-chart"></i></div></div></div>
            <div class="col-md-3 col-sm-6"><div class="small-box"><div class="inner"><h3><?= $taux_croissance ?>%</h3><p>Croissance mensuelle</p></div><div class="icon icon-croissance"><i class="fa fa-line-chart"></i></div></div></div>
            <div class="col-md-3 col-sm-6"><div class="small-box"><div class="inner"><h3><?= $nouveaux_clients ?></h3><p>Nouveaux Clients</p></div><div class="icon icon-clients"><i class="fa fa-users"></i></div></div></div>
            <div class="col-md-3 col-sm-6"><div class="small-box"><div class="inner"><h3><?= $factures_impayees ?></h3><p>Factures impayées</p></div><div class="icon icon-impayes"><i class="fa fa-exclamation-triangle"></i></div></div></div>
        </div>

        <!-- Graphiques CA mensuel + par ville -->
        <div class="row">
            <div class="col-md-6"><div class="box box-primary"><div class="box-header"><h3 class="box-title">Chiffre d'affaires par mois</h3></div><div class="box-body"><canvas id="chartCA"></canvas></div></div></div>
            <div class="col-md-6"><div class="box box-danger"><div class="box-header"><h3 class="box-title">Répartition par ville</h3></div><div class="box-body"><canvas id="chartVille"></canvas></div></div></div>
        </div>

        <!-- Évolution du CA + Services mensuels -->
        <div class="row">
            <div class="col-md-6"><div class="box box-success"><div class="box-header"><h3 class="box-title">Évolution du CA global</h3></div><div class="box-body"><canvas id="chartEvolution"></canvas></div></div></div>
            <div class="col-md-6"><div class="box box-info"><div class="box-header"><h3 class="box-title">Services vendus par mois</h3></div><div class="box-body"><canvas id="chartCommandesMensuel"></canvas></div></div></div>
        </div>

        <!-- Performance commercial -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">Performance par commercial</h3>
                        <div class="box-tools pull-right"><small>Objectif annuel: <strong><?= number_format($objectif_directeur_montant,0,',',' ') ?> FCFA</strong> <?php if($objectif_directeur_date) echo "(fixé le ".date("d/m/Y",strtotime($objectif_directeur_date)).")"; ?></small></div>
                    </div>
                    <div class="box-body">
                        <div style="margin-bottom:15px;">
                            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:5px;">
                                <span>Progression globale</span>
                                <span><strong><?= number_format($ca_total,0,',',' ') ?> FCFA</strong> / <strong><?= number_format($objectif_directeur_montant,0,',',' ') ?> FCFA</strong> (<?= number_format($pourcentage_global,1,',',' ') ?>%)</span>
                            </div>
                            <div class="global-progress"><div class="global-progress-bar" style="width:<?= $pourcentage_global ?>%; background:<?= $bar_color ?>;"></div></div>
                        </div>
                        <div style="height:300px; margin-bottom:20px;"><canvas id="chartCommercial"></canvas></div>
                        <div class="commercial-details" style="max-height:250px; overflow-y:auto;">
                            <?php foreach($commerciaux as $com): $ca_atteint = $ca_commerciaux[$com]??0; $obj = $objectifs[$com]??0; $pct = $obj>0 ? min(100,($ca_atteint/$obj)*100) : 0; $reste = max(0,$obj-$ca_atteint); $couleur = $pct>=100 ? '#28a745' : ($pct>=80 ? '#ffc107' : '#dc3545'); ?>
                                <div class="commercial-item" style="margin-bottom:12px; padding:10px; border-left:4px solid <?= $couleur ?>; background:#f8f9fa; border-radius:4px;">
                                    <div style="font-weight:bold; color:#333; margin-bottom:5px; font-size:0.95em;"><?= htmlspecialchars($com) ?></div>
                                    <div style="display:flex; justify-content:space-between; font-size:0.85em; margin-bottom:8px;">
                                        <span>Objectif : <strong><?= number_format($obj,0,',',' ') ?> FCFA</strong></span>
                                        <span>Réalisé : <strong><?= number_format($ca_atteint,0,',',' ') ?> FCFA</strong></span>
                                        <span>Écart : <strong style="color:<?= $pct>=100 ? '#28a745' : '#dc3545' ?>"><?= $pct>=100 ? '+'.number_format($ca_atteint-$obj,0,',',' ') : number_format($reste,0,',',' ') ?> FCFA</strong></span>
                                    </div>
                                    <div style="margin-top:5px;">
                                        <div style="background:#e9ecef; border-radius:10px; height:8px; overflow:hidden; margin-bottom:5px;">
                                            <div style="height:100%; background:<?= $couleur ?>; width:<?= $pct ?>%; border-radius:10px;"></div>
                                        </div>
                                        <div style="display:flex; justify-content:space-between; font-size:0.8em; color:#6c757d;">
                                            <span><strong><?= number_format($pct,1,',',' ') ?>%</strong></span>
                                            <span><?php if($pct>=100): ?><span style="color:#28a745;">✅ Objectif atteint</span><?php else: ?>⚠️ Reste : <?= number_format($reste,0,',',' ') ?> FCFA<?php endif; ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOP 10 Produits & Services vendus -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title">Top 10 produits/services vendus</h3>
                        <div class="box-tools pull-right">
                            <small>Factures + Commandes</small>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if (!empty($items_global)): ?>
                            <div style="width: 100%; overflow-x: auto;">
                                <canvas id="chartTopItems" style="height: 500px; min-width: 800px;"></canvas>
                            </div>
                            <div class="table-responsive" style="margin-top:20px;">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nom</th>
                                        <th>Quantité totale</th>
                                        <th>Nombre de ventes</th>
                                        <th>Montant</th>
                                        <th>Source</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($top_services_global as $idx => $item):
                                        $montant = $item['montant_total'];
                                        $sources = implode(' + ', array_unique($item['source']));
                                        ?>
                                        <tr>
                                            <td><?= $idx+1 ?></td>
                                            <td><strong><?= htmlspecialchars($item['nom']) ?></strong></td>
                                            <td><span class="label label-info"><?= number_format($item['quantite_totale'], 0, ',', ' ') ?></span></td>
                                            <td><span class="label label-primary"><?= $item['nb_commandes'] ?></span></td>
                                            <td><?= number_format($montant, 0, ',', ' ') ?> FCFA</td>
                                            <td><span class="label label-default"><?= $sources ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr class="active">
                                        <th colspan="4" class="text-right">TOTAL (top 10) :</th>
                                        <th><?= number_format($total_general_top10_global, 0, ',', ' ') ?> FCFA</th>
                                        <th colspan="2"></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning"><i class="fa fa-warning"></i> Aucune donnée de vente trouvée.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Derniers services vendus -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header"><h3 class="box-title">Derniers services vendus</h3></div>
                    <div class="box-body">
                        <div class="table-scroll">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Service</th>
                                    <th>Qté</th>
                                    <th>Total TTC</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($derniers_services)): foreach($derniers_services as $d): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($d['date'])) ?></td>
                                        <td><?= htmlspecialchars($d['service'] ?? 'N/A') ?></td>
                                        <td><span class="label label-info"><?= number_format($d['quantity'], 0, ',', ' ') ?></span></td>
                                        <td><?= number_format($d['montant_ttc'] ?? 0, 0, ',', ' ') ?> FCFA</td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="9" class="text-center">Aucun service vendu</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock faible -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-danger">
                    <div class="box-header"><h3 class="box-title">Stock faible / À réapprovisionner</h3></div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped">
                            <thead><tr><th>Produit</th><th>Stock actuel</th><th>Prix moyen (PMP)</th></tr></thead>
                            <tbody>
                            <?php if(!empty($produits_stock_faible)): foreach($produits_stock_faible as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['name']) ?></td>
                                    <td><span class="label label-danger"><?= (int)$p['current_quantity'] ?></span></td>
                                    <td><?= number_format((float)($p['weighted_avg_price'] ?? 0), 2, ',', ' ') ?> FCFA</td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="3" class="text-center">Aucun produit en stock faible</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paiements clients -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header"><h3 class="box-title">Récapitulatif des paiements clients</h3></div>
                    <div class="box-body">
                        <form method="GET" action="" class="form-inline" style="margin-bottom:15px;">
                            <div class="form-group"><label for="date_debut">Du :</label> <input type="date" name="date_debut" class="form-control" value="<?= htmlspecialchars($date_debut) ?>"></div>
                            <div class="form-group"><label for="date_fin">Au :</label> <input type="date" name="date_fin" class="form-control" value="<?= htmlspecialchars($date_fin) ?>"></div>
                            <button type="submit" class="btn btn-primary">Filtrer</button>
                            <a href="?" class="btn btn-default">Réinitialiser</a>
                        </form>
                        <table class="table table-bordered table-striped">
                            <thead><tr><th>Client</th><th>Dernière échéance</th><th>Total à payer</th><th>Déjà payé</th><th>Reste à payer</th></tr></thead>
                            <tbody>
                            <?php if(!empty($clients_paiements)): foreach($clients_paiements as $c): $classEcheance = (strtotime($c['echeance']) <= time()) ? "alert-date" : ""; ?>
                                <tr><td><?= htmlspecialchars($c['name']) ?></td><td><?= $c['echeance'] ? "<span class='$classEcheance'>".date("d/m/Y",strtotime($c['echeance']))."</span>" : "—" ?></td><td><span class="label label-primary"><?= number_format($c['total'],0,',',' ') ?> FCFA</span></td><td><span class="label label-success"><?= number_format($c['paye'],0,',',' ') ?> FCFA</span></td><td><?= $c['reste']>0 ? "<span class='label label-danger'>".number_format($c['reste'],0,',',' ')." FCFA</span>" : "<span class='label label-success'>✅ Soldé</span>" ?></td></tr>
                            <?php endforeach; ?>
                                <tr style="font-weight:bold; background:#f9f9f9;"><td colspan="2" class="text-right">TOTAL GÉNÉRAL :</td><td><?= number_format($total_general['total'],0,',',' ') ?> FCFA</td><td><?= number_format($total_general['paye'],0,',',' ') ?> FCFA</td><td><?= number_format($total_general['reste'],0,',',' ') ?> FCFA</td></tr>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center">Aucun paiement enregistré pour la période sélectionnée</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique CA mensuel
    new Chart(document.getElementById('chartCA'), { type:'bar', data:{ labels:<?= json_encode($mois) ?>, datasets:[{ label:'CA global (FCFA)', data:<?= json_encode($ca_mensuel) ?>, backgroundColor:'rgba(54,162,235,0.6)' }] }, options:{ responsive:true, maintainAspectRatio:false } });

    // Graphique CA par ville
    new Chart(document.getElementById('chartVille'), { type:'pie', data:{ labels:<?= json_encode($villes) ?>, datasets:[{ data:<?= json_encode($ca_villes) ?>, backgroundColor:['#36A2EB','#FF6384','#FFCE56','#4BC0C0','#9966FF'] }] }, options:{ responsive:true, maintainAspectRatio:false } });

    // Graphique évolution CA
    new Chart(document.getElementById('chartEvolution'), { type:'line', data:{ labels:<?= json_encode($mois) ?>, datasets:[{ label:'Évolution du CA global', data:<?= json_encode($ca_mensuel) ?>, borderColor:'rgba(75,192,192,1)', fill:false, tension:0.1 }] }, options:{ responsive:true, maintainAspectRatio:false } });

    // Graphique services mensuels
    <?php
    $cmd_mois_labels = [];
    $cmd_totaux = [];
    $cmd_montants = [];
    for($m = 1; $m <= 12; $m++) {
        $cmd_mois_labels[] = $moisNoms[$m];
        $cmd_totaux[] = $cmd_mensuel[$m] ?? 0;
        $cmd_montants[] = $cmd_montant_mensuel[$m] ?? 0;
    }
    ?>
    new Chart(document.getElementById('chartCommandesMensuel'), {
        type:'bar',
        data:{
            labels:<?= json_encode($cmd_mois_labels) ?>,
            datasets:[
                { label:'Services vendus', data:<?= json_encode($cmd_totaux) ?>, backgroundColor:'rgba(26,188,156,0.7)', yAxisID:'y' },
                { label:'Montant (FCFA)', data:<?= json_encode($cmd_montants) ?>, backgroundColor:'rgba(52,152,219,0.5)', yAxisID:'y1' }
            ]
        },
        options:{
            responsive:true,
            maintainAspectRatio:false,
            scales:{
                y:{ beginAtZero:true, position:'left', title:{ display:true, text:'Nombre' } },
                y1:{ beginAtZero:true, position:'right', grid:{ drawOnChartArea:false }, title:{ display:true, text:'Montant (FCFA)' }, ticks:{ callback:function(v){ return v.toLocaleString('fr-FR'); } } }
            },
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Graphique performance commerciale
    new Chart(document.getElementById('chartCommercial'), { 
        type:'bar', 
        data:{ 
            labels:<?= json_encode($commerciaux) ?>, 
            datasets:[
                { 
                    label:'Objectif', 
                    data:<?= json_encode(array_map(function($c) use ($objectifs){ return $objectifs[$c]??0; }, $commerciaux)) ?>, 
                    backgroundColor:'rgba(54,162,235,0.7)', 
                    borderColor:'#36A2EB', 
                    borderWidth:1 
                },
                { 
                    label:'Réalisé', 
                    data:<?= json_encode(array_map(function($c) use ($ca_commerciaux){ return $ca_commerciaux[$c]??0; }, $commerciaux)) ?>, 
                    backgroundColor:'rgba(75,192,192,0.7)', 
                    borderColor:'#4BC0C0', 
                    borderWidth:1 
                }
            ] 
        }, 
        options:{ 
            responsive:true, 
            maintainAspectRatio:false, 
            scales:{ 
                y:{ 
                    beginAtZero:true, 
                    title:{ display:true, text:'Montant (FCFA)' }, 
                    ticks:{ callback:function(v){ return v.toLocaleString('fr-FR')+' FCFA'; } } 
                }, 
                x:{ ticks:{ autoSkip:false, font:{ size:11 } } } 
            }, 
            plugins:{ 
                tooltip:{ 
                    callbacks:{ 
                        label:function(ctx){ 
                            let label=ctx.dataset.label||''; 
                            let val=ctx.parsed.y; 
                            let commercial=ctx.label; 
                            let obj=<?= json_encode($objectifs) ?>[commercial]||0; 
                            let real=<?= json_encode($ca_commerciaux) ?>[ctx.dataIndex]||0; 
                            let pct=obj>0?(real/obj*100):0; 
                            if(ctx.datasetIndex===1) return [`${label}: ${val.toLocaleString('fr-FR')} FCFA`,`Objectif: ${obj.toLocaleString('fr-FR')} FCFA`,`Atteint: ${pct.toFixed(1)}%`]; 
                            return `${label}: ${val.toLocaleString('fr-FR')} FCFA`; 
                        } 
                    } 
                } 
            } 
        } 
    });

    <?php if (!empty($items_global)): ?>
    var ctx = document.getElementById('chartTopItems').getContext('2d');
    var colorPalette = ['rgba(54,162,235,0.8)','rgba(255,99,132,0.8)','rgba(255,206,86,0.8)','rgba(75,192,192,0.8)','rgba(153,102,255,0.8)','rgba(255,159,64,0.8)','rgba(199,199,199,0.8)','rgba(83,102,255,0.8)','rgba(255,99,255,0.8)','rgba(99,255,132,0.8)'];
    var backgroundColors = <?= json_encode($items_global) ?>.map((_, idx) => colorPalette[idx % colorPalette.length]);
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($items_global) ?>,
            datasets: [{
                label: 'Montant (FCFA)',
                data: <?= json_encode($montants_global) ?>,
                backgroundColor: backgroundColors,
                borderColor: backgroundColors.map(c => c.replace('0.8','1')),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y',
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            let val = ctx.raw;
                            let total = <?= $total_general_top10_global ?>;
                            let pct = total>0 ? (val/total*100).toFixed(1) : 0;
                            let label = ctx.dataset.label || '';
                            return `${label}: ${val.toLocaleString('fr-FR')} FCFA (${pct}% du top 10)`;
                        },
                        title: function(tooltipItems) {
                            return tooltipItems[0].label;
                        }
                    }
                },
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: { display: true, text: 'Montant (FCFA)' },
                    ticks: { callback: function(v) { return v.toLocaleString('fr-FR'); } }
                },
                y: {
                    ticks: {
                        callback: function(val, index) {
                            let fullLabel = this.getLabelForValue(val);
                            return fullLabel.length > 25 ? fullLabel.substring(0, 22) + '…' : fullLabel;
                        },
                        font: { size: 10 },
                        autoSkip: false
                    }
                }
            },
            layout: {
                padding: { left: 10, right: 10, top: 10, bottom: 10 }
            }
        }
    });
    <?php endif; ?>
</script>
</body>
</html>