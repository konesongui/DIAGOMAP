<style>
    /* Styles pour les icônes avec fond coloré (uniquement le rond) */
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
    .icon-ca, .icon-croissance, .icon-clients, .icon-impayes {
        background: white;
        color: white;
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
</style>

<?php
// ===============================
// Connexion BDD (CodeIgniter)
// ===============================
$CI = &get_instance();
$conn = new mysqli($CI->db->hostname, $CI->db->username, $CI->db->password, $CI->db->database);
if ($conn->connect_error) die("Erreur de connexion: " . $conn->connect_error);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ===============================
// KPIs
// ===============================
$sqlCA = "SELECT SUM(amount_paid) AS total_annuel FROM invoices WHERE YEAR(created_at) = YEAR(CURDATE())";
$resultCA = $conn->query($sqlCA);
$ca_total = ($resultCA && ($r = $resultCA->fetch_assoc())) ? (float)$r['total_annuel'] : 0;

// Croissance mensuelle
$sqlMoisCourant = "SELECT SUM(amount_paid) AS ca FROM invoices WHERE YEAR(created_at)=YEAR(CURDATE()) AND MONTH(created_at)=MONTH(CURDATE())";
$res1 = $conn->query($sqlMoisCourant);
$ca_mois_courant = ($res1 && ($r1 = $res1->fetch_assoc())) ? (float)$r1['ca'] : 0;

$sqlMoisPrecedent = "SELECT SUM(amount_paid) AS ca FROM invoices WHERE (YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())-1) OR (MONTH(CURDATE()) = 1 AND YEAR(created_at) = YEAR(CURDATE())-1 AND MONTH(created_at) = 12)";
$res2 = $conn->query($sqlMoisPrecedent);
$ca_mois_precedent = ($res2 && ($r2 = $res2->fetch_assoc())) ? (float)$r2['ca'] : 0;
$taux_croissance = ($ca_mois_precedent > 0) ? round((($ca_mois_courant - $ca_mois_precedent) / $ca_mois_precedent) * 100, 2) : 0;

// Nouveaux clients
$sqlClients = "SELECT COUNT(*) AS nb FROM clients WHERE YEAR(created_at) = YEAR(CURDATE())";
$resClients = $conn->query($sqlClients);
$nouveaux_clients = ($resClients && ($r3 = $resClients->fetch_assoc())) ? (int)$r3['nb'] : 0;

// Factures impayées
$sqlImpaye = "SELECT COUNT(*) AS nb FROM invoices WHERE status = 1";
$resImpaye = $conn->query($sqlImpaye);
$factures_impayees = ($resImpaye && ($r4 = $resImpaye->fetch_assoc())) ? (int)$r4['nb'] : 0;

// CA mensuel
$sqlMensuel = "SELECT MONTH(created_at) AS mois, SUM(amount_paid) AS montant FROM invoices WHERE YEAR(created_at)=YEAR(CURDATE()) GROUP BY mois ORDER BY mois ASC";
$resMensuel = $conn->query($sqlMensuel);
$mois = []; $ca_mensuel = [];
$moisNoms = [1=>"Jan","Fév","Mar","Avr","Mai","Juin","Juil","Août","Sept","Oct","Nov","Déc"];
if ($resMensuel) {
    while ($r5 = $resMensuel->fetch_assoc()) {
        $mois[] = $moisNoms[(int)$r5['mois']];
        $ca_mensuel[] = (float)$r5['montant'];
    }
}

// CA par ville
$sqlVille = "SELECT c.ville, SUM(i.amount_paid) AS total FROM invoices i JOIN clients c ON i.customer_id = c.id WHERE YEAR(i.created_at)=YEAR(CURDATE()) GROUP BY c.ville";
$resVille = $conn->query($sqlVille);
$villes = []; $ca_villes = [];
if ($resVille) {
    while ($r6 = $resVille->fetch_assoc()) {
        $villes[] = $r6['ville'] ?? "Inconnu";
        $ca_villes[] = (float)$r6['total'];
    }
}

// ===============================
// RÉPARTITION PAR COMMERCIAL (nouvelles tables)
// ===============================
// Ventes réelles
$sqlCom = "SELECT user_name, SUM(amount_paid) AS total FROM invoices WHERE YEAR(created_at)=YEAR(CURDATE()) GROUP BY user_name";
$resCom = $conn->query($sqlCom);
$ca_commerciaux = [];
if ($resCom) {
    while ($r7 = $resCom->fetch_assoc()) {
        $user = trim($r7['user_name'] ?? 'Inconnu');
        $ca_commerciaux[$user] = (float)$r7['total'];
    }
}

// Objectifs attribués
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

// Objectif annuel directeur
$sqlDir = "SELECT amount, date FROM annual_objectives WHERE YEAR(date)=YEAR(CURDATE()) ORDER BY date DESC LIMIT 1";
$resDir = $conn->query($sqlDir);
$directeur = $resDir && ($dir = $resDir->fetch_assoc()) ? $dir : null;
$objectif_directeur_montant = $directeur ? (float)$directeur['amount'] : 0;
$objectif_directeur_date = $directeur ? $directeur['date'] : null;
$pourcentage_global = ($objectif_directeur_montant > 0) ? min(100, ($ca_total / $objectif_directeur_montant) * 100) : 0;
$bar_color = ($pourcentage_global >= 100) ? '#28a745' : '#007bff';

// ===============================
// TOP 10 PRODUITS (version robuste)
// ===============================
// Détection colonne montant dans invoice_items
$possible_columns = ['line_total', 'total', 'subtotal', 'amount'];
$montant_column = null;
$cols = $conn->query("SHOW COLUMNS FROM invoice_items");
if ($cols) while ($col = $cols->fetch_assoc()) {
    if (in_array($col['Field'], $possible_columns)) { $montant_column = $col['Field']; break; }
}
if (!$montant_column) $montant_column = 'line_total';

// Détection colonne date dans invoices
$possible_date = ['invoice_date', 'created_at', 'date'];
$date_column = null;
$cols2 = $conn->query("SHOW COLUMNS FROM invoices");
if ($cols2) while ($col2 = $cols2->fetch_assoc()) {
    if (in_array($col2['Field'], $possible_date)) { $date_column = $col2['Field']; break; }
}
if (!$date_column) $date_column = 'created_at';

$sqlTop = "SELECT i.name AS product_name, SUM(ii.{$montant_column}) AS montant_vendu
           FROM invoice_items ii
           INNER JOIN item i ON ii.item_id = i.id
           INNER JOIN invoices inv ON ii.invoice_id = inv.id
           WHERE YEAR(inv.{$date_column}) = YEAR(CURDATE())
           GROUP BY ii.item_id ORDER BY montant_vendu DESC LIMIT 10";
$resTop = $conn->query($sqlTop);
$produits = []; $ca_produits = [];
if ($resTop && $resTop->num_rows > 0) {
    while ($p = $resTop->fetch_assoc()) {
        $produits[] = $p['product_name'] ?? "Inconnu";
        $ca_produits[] = (float)$p['montant_vendu'];
    }
} else {
    // fallback sans filtre année
    $sqlFallback = "SELECT i.name AS product_name, SUM(ii.{$montant_column}) AS montant_vendu FROM invoice_items ii INNER JOIN item i ON ii.item_id = i.id INNER JOIN invoices inv ON ii.invoice_id = inv.id GROUP BY ii.item_id ORDER BY montant_vendu DESC LIMIT 10";
    $resFall = $conn->query($sqlFallback);
    if ($resFall) while ($p = $resFall->fetch_assoc()) {
        $produits[] = $p['product_name'] ?? "Inconnu";
        $ca_produits[] = (float)$p['montant_vendu'];
    }
}

// ===============================
// STOCK FAIBLE
// ===============================
$sqlStock = "SELECT i.name, s.current_quantity, s.weighted_avg_price FROM stock s INNER JOIN item i ON s.item_id = i.id WHERE s.current_quantity <= 10 ORDER BY s.current_quantity ASC LIMIT 10";
$resStock = $conn->query($sqlStock);
$produits_stock_faible = [];
if ($resStock) while ($st = $resStock->fetch_assoc()) $produits_stock_faible[] = $st;

// ===============================
// PAIEMENTS CLIENTS AVEC FILTRE PAR PÉRIODE
// ===============================
$date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-01-01');
$date_fin   = isset($_GET['date_fin'])   ? $_GET['date_fin']   : date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_debut)) $date_debut = date('Y-01-01');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_fin))   $date_fin   = date('Y-m-d');

$sqlPay = "SELECT c.id AS client_id, c.item_supplier AS client_name, MAX(i.due_date) AS derniere_echeance,
                  SUM(i.total_ttc) AS total_a_payer, SUM(i.amount_paid) AS deja_paye, SUM(i.remaining_amount) AS reste_a_payer
           FROM invoices i LEFT JOIN clients c ON i.customer_id = c.id
           WHERE i.invoice_date BETWEEN ? AND ?
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
        <!-- Ligne KPI -->
        <div class="row">
            <div class="col-md-3 col-sm-6"><div class="small-box"><div class="inner"><h3><?= number_format($ca_total,0,","," ") ?> FCFA</h3><p>Chiffre d'affaires (Année en cours)</p></div><div class="icon icon-ca"><i class="fa fa-line-chart"></i></div></div></div>
            <div class="col-md-3 col-sm-6"><div class="small-box"><div class="inner"><h3><?= $taux_croissance ?>%</h3><p>Croissance</p></div><div class="icon icon-croissance"><i class="fa fa-line-chart"></i></div></div></div>
            <div class="col-md-3 col-sm-6"><div class="small-box"><div class="inner"><h3><?= $nouveaux_clients ?></h3><p>Nouveaux Clients</p></div><div class="icon icon-clients"><i class="fa fa-users"></i></div></div></div>
            <div class="col-md-3 col-sm-6"><div class="small-box"><div class="inner"><h3><?= $factures_impayees ?></h3><p>Factures impayées</p></div><div class="icon icon-impayes"><i class="fa fa-exclamation-triangle"></i></div></div></div>
        </div>

        <!-- Graphiques CA mensuel / Ville -->
        <div class="row">
            <div class="col-md-6"><div class="box box-primary"><div class="box-header"><h3 class="box-title">Chiffre d’affaires par mois</h3></div><div class="box-body"><canvas id="chartCA"></canvas></div></div></div>
            <div class="col-md-6"><div class="box box-danger"><div class="box-header"><h3 class="box-title">Répartition par ville</h3></div><div class="box-body"><canvas id="chartVille"></canvas></div></div></div>
        </div>

        <div class="row">
            <div class="col-md-12"><div class="box box-success"><div class="box-header"><h3 class="box-title">Évolution du CA</h3></div><div class="box-body"><canvas id="chartEvolution"></canvas></div></div></div>

            <!-- Répartition par commercial -->
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">Répartition par commercial</h3>
                        <div class="box-tools pull-right"><small>Objectif annuel (directeur) : <strong><?= number_format($objectif_directeur_montant,0,',',' ') ?> FCFA</strong> <?php if($objectif_directeur_date) echo "(fixé le ".date("d/m/Y",strtotime($objectif_directeur_date)).")"; ?></small></div>
                    </div>
                    <div class="box-body">
                        <div style="margin-bottom:15px;"><div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:5px;"><span>Progression globale</span><span><strong><?= number_format($ca_total,0,',',' ') ?> FCFA</strong> / <strong><?= number_format($objectif_directeur_montant,0,',',' ') ?> FCFA</strong> (<?= number_format($pourcentage_global,1,',',' ') ?>%)</span></div><div class="global-progress"><div class="global-progress-bar" style="width:<?= $pourcentage_global ?>%; background:<?= $bar_color ?>;"></div></div></div>
                        <div style="height:250px; margin-bottom:20px;"><canvas id="chartCommercial"></canvas></div>
                        <div class="commercial-details" style="max-height:250px; overflow-y:auto;">
                            <?php foreach($commerciaux as $com): $ca_atteint = $ca_commerciaux[$com]??0; $obj = $objectifs[$com]??0; $pct = $obj>0 ? min(100,($ca_atteint/$obj)*100) : 0; $reste = max(0,$obj-$ca_atteint); $couleur = $pct>=100 ? '#28a745' : ($pct>=80 ? '#ffc107' : '#dc3545'); ?>
                                <div class="commercial-item" style="margin-bottom:12px; padding:10px; border-left:4px solid <?= $couleur ?>; background:#f8f9fa; border-radius:4px;">
                                    <div style="font-weight:bold; color:#333; margin-bottom:5px; font-size:0.95em;"><?= htmlspecialchars($com) ?></div>
                                    <div style="display:flex; justify-content:space-between; font-size:0.85em; margin-bottom:8px;"><span>Objectif attribué : <strong><?= number_format($obj,0,',',' ') ?> FCFA</strong></span><span>Réalisé : <strong><?= number_format($ca_atteint,0,',',' ') ?> FCFA</strong></span></div>
                                    <div style="margin-top:5px;"><div style="background:#e9ecef; border-radius:10px; height:8px; overflow:hidden; margin-bottom:5px;"><div style="height:100%; background:<?= $couleur ?>; width:<?= $pct ?>%; border-radius:10px; transition:width 0.5s ease;"></div></div><div style="display:flex; justify-content:space-between; font-size:0.8em; color:#6c757d;"><span><strong><?= number_format($pct,1,',',' ') ?>%</strong></span><span><?php if($pct>=100): ?><span style="color:#28a745;">+<?= number_format($ca_atteint-$obj,0,',',' ') ?> FCFA</span><?php else: ?>Reste : <?= number_format($reste,0,',',' ') ?> FCFA<?php endif; ?></span></div></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top 10 Produits -->
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header"><h3 class="box-title">Top 10 Produits vendus (Montant généré)</h3></div>
                    <div class="box-body">
                        <?php if(!empty($produits)): ?>
                            <canvas id="chartProduit"></canvas>
                        <?php else: ?>
                            <div class="alert alert-warning"><i class="fa fa-warning"></i> Aucune donnée de vente par produit trouvée. Vérifiez les noms des colonnes dans vos tables.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Stock faible -->
            <div class="col-md-12">
                <div class="box box-danger">
                    <div class="box-header"><h3 class="box-title">Stock faible / À réapprovisionner</h3></div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped"><thead><tr><th>Produit</th><th>Stock actuel</th><th>Prix moyen (PMP)</th></tr></thead><tbody>
                            <?php if(!empty($produits_stock_faible)): foreach($produits_stock_faible as $p): ?>
                                <tr><td><?= htmlspecialchars($p['name']) ?></td><td><span class="label label-danger"><?= $p['current_quantity'] ?></span></td><td><?= number_format($p['weighted_avg_price'],2,',',' ') ?> FCFA</td></tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="3" class="text-center">Aucun produit en stock faible</td></tr>
                            <?php endif; ?>
                            </tbody></table>
                    </div>
                </div>
            </div>

            <!-- Paiements clients avec filtre -->
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header"><h3 class="box-title">Récapitulatif des paiements clients</h3></div>
                    <div class="box-body">
                        <form method="GET" action="" class="form-inline" style="margin-bottom:15px;">
                            <div class="form-group"><label for="date_debut">Du :</label> <input type="date" name="date_debut" id="date_debut" class="form-control" value="<?= htmlspecialchars($date_debut) ?>"></div>
                            <div class="form-group"><label for="date_fin">Au :</label> <input type="date" name="date_fin" id="date_fin" class="form-control" value="<?= htmlspecialchars($date_fin) ?>"></div>
                            <button type="submit" class="btn btn-primary">Filtrer</button>
                            <a href="?" class="btn btn-default">Réinitialiser</a>
                        </form>
                        <table class="table table-bordered table-striped">
                            <thead><tr><th>Client</th><th>Dernière échéance</th><th>Total à payer</th><th>Déjà payé</th><th>Reste à payer</th></tr></thead>
                            <tbody>
                            <?php if(!empty($clients_paiements)): foreach($clients_paiements as $c): $classEcheance = ""; if($c['echeance'] && (strtotime($c['echeance']) <= time() || strtotime($c['echeance']) <= strtotime("+5 days"))) $classEcheance = "alert-date"; ?>
                                <tr>
                                    <td><?= htmlspecialchars($c['name']) ?></td>
                                    <td><?php if($c['echeance']) echo "<span class='$classEcheance'>".date("d/m/Y",strtotime($c['echeance']))."</span>"; else echo "—"; ?></td>
                                    <td><span class="label label-primary"><?= number_format($c['total'],0,',',' ') ?> FCFA</span></td>
                                    <td><span class="label label-success"><?= number_format($c['paye'],0,',',' ') ?> FCFA</span></td>
                                    <td><?php if($c['reste']>0): ?><span class="label label-danger"><?= number_format($c['reste'],0,',',' ') ?> FCFA</span><?php else: ?><span class="label label-success">Soldé</span><?php endif; ?></td>
                                </tr>
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
    // CA mensuel
    new Chart(document.getElementById('chartCA'), { type:'bar', data:{ labels:<?= json_encode($mois) ?>, datasets:[{ label:'CA (FCFA)', data:<?= json_encode($ca_mensuel) ?>, backgroundColor:'rgba(54,162,235,0.6)' }] }, options:{ responsive:true, maintainAspectRatio:false } });
    // CA par ville
    new Chart(document.getElementById('chartVille'), { type:'pie', data:{ labels:<?= json_encode($villes) ?>, datasets:[{ data:<?= json_encode($ca_villes) ?>, backgroundColor:['#36A2EB','#FF6384','#FFCE56','#4BC0C0','#9966FF'] }] }, options:{ responsive:true, maintainAspectRatio:false } });
    // Évolution CA
    new Chart(document.getElementById('chartEvolution'), { type:'line', data:{ labels:<?= json_encode($mois) ?>, datasets:[{ label:'Évolution du CA', data:<?= json_encode($ca_mensuel) ?>, borderColor:'rgba(75,192,192,1)', fill:false, tension:0.1 }] }, options:{ responsive:true, maintainAspectRatio:false } });
    // Commercial
    new Chart(document.getElementById('chartCommercial'), { type:'bar', data:{ labels:<?= json_encode($commerciaux) ?>, datasets:[{ label:'Objectif', data:<?= json_encode(array_map(function($c) use ($objectifs){ return $objectifs[$c]??0; }, $commerciaux)) ?>, backgroundColor:'rgba(54,162,235,0.7)', borderColor:'#36A2EB', borderWidth:1 },{ label:'Réalisé', data:<?= json_encode($ca_commerciaux) ?>, backgroundColor:'rgba(75,192,192,0.7)', borderColor:'#4BC0C0', borderWidth:1 }] }, options:{ responsive:true, maintainAspectRatio:false, scales:{ y:{ beginAtZero:true, title:{ display:true, text:'Montant (FCFA)' }, ticks:{ callback:function(v){ return v.toLocaleString('fr-FR')+' FCFA'; } } }, x:{ ticks:{ maxRotation:0, autoSkip:false, font:{ size:11 } } } }, plugins:{ tooltip:{ callbacks:{ label:function(ctx){ let label=ctx.dataset.label||''; let val=ctx.parsed.y; let commercial=ctx.label; let objectifs=<?= json_encode($objectifs) ?>; let realises=<?= json_encode($ca_commerciaux) ?>; let obj=objectifs[commercial]||0; let real=realises[ctx.dataIndex]||0; let pct=obj>0?(real/obj*100):0; if(ctx.datasetIndex===1) return [`${label}: ${val.toLocaleString('fr-FR')} FCFA`,`Objectif: ${obj.toLocaleString('fr-FR')} FCFA`,`Atteint: ${pct.toFixed(1)}%`]; return `${label}: ${val.toLocaleString('fr-FR')} FCFA`; } } } } } });
    // Top 10 Produits
    <?php if(!empty($produits)): ?>
    new Chart(document.getElementById('chartProduit'), { type:'bar', data:{ labels:<?= json_encode($produits) ?>, datasets:[{ label:'Montant (FCFA)', data:<?= json_encode($ca_produits) ?>, backgroundColor:['#FF9F40','#4BC0C0','#9966FF','#FF6384','#36A2EB','#FFCE56','#8BC34A','#FF5722','#9E9E9E','#607D8B'] }] }, options:{ responsive:true, maintainAspectRatio:false, indexAxis:'y', plugins:{ legend:{ display:false }, tooltip:{ callbacks:{ label:function(ctx){ return 'Montant: '+ctx.raw.toLocaleString('fr-FR')+' FCFA'; } } } }, scales:{ x:{ beginAtZero:true, title:{ display:true, text:'Montant (FCFA)' }, ticks:{ callback:function(v){ return v.toLocaleString('fr-FR'); } } }, y:{ ticks:{ callback:function(v,i,vals){ let l=this.getLabelForValue(v); return l.length>40?l.substring(0,37)+'...':l; }, font:{ size:11 } } } } } });
    <?php endif; ?>
</script>
</body>
</html>