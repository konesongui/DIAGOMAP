<style>.alert-date {
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

</style>


<?php
// ===============================
// Connexion BDD
// ===============================
$conn = new mysqli("localhost", "root", "", "diago");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// ===============================
// KPIs
// ===============================

// Chiffre d’affaires annuel
$sqlCA = "SELECT SUM(amount_paid) AS total_annuel 
          FROM invoices 
          WHERE YEAR(created_at) = YEAR(CURDATE())";
$resultCA = $conn->query($sqlCA);
$ca_total = ($resultCA && $row = $resultCA->fetch_assoc()) ? (float)$row['total_annuel'] : 0;

// Croissance = (CA mois courant / CA mois précédent -1) * 100
$sqlMoisCourant = "SELECT SUM(amount_paid) AS ca 
                   FROM invoices 
                   WHERE YEAR(created_at)=YEAR(CURDATE()) 
                   AND MONTH(created_at)=MONTH(CURDATE())";
$res1 = $conn->query($sqlMoisCourant);
$ca_mois_courant = ($res1 && $row = $res1->fetch_assoc()) ? (float)$row['ca'] : 0;

$sqlMoisPrecedent = "SELECT SUM(amount_paid) AS ca 
                     FROM invoices 
                     WHERE YEAR(created_at)=YEAR(CURDATE()) 
                     AND MONTH(created_at)=MONTH(CURDATE())-1";
$res2 = $conn->query($sqlMoisPrecedent);
$ca_mois_precedent = ($res2 && $row = $res2->fetch_assoc()) ? (float)$row['ca'] : 0;

$taux_croissance = ($ca_mois_precedent > 0)
    ? round((($ca_mois_courant - $ca_mois_precedent) / $ca_mois_precedent) * 100, 2)
    : 0;

// Nouveaux clients de l’année
$sqlClients = "SELECT COUNT(*) AS nb FROM clients WHERE YEAR(created_at) = YEAR(CURDATE())";
$resClients = $conn->query($sqlClients);
$nouveaux_clients = ($resClients && $row = $resClients->fetch_assoc()) ? (int)$row['nb'] : 0;

// Factures impayées
$sqlImpaye = "SELECT COUNT(*) AS nb FROM invoices WHERE status = 1";
$resImpaye = $conn->query($sqlImpaye);
$factures_impayees = ($resImpaye && $row = $resImpaye->fetch_assoc()) ? (int)$row['nb'] : 0;

// ===============================
// Données pour les graphiques
// ===============================

// CA mensuel
$sqlMensuel = "SELECT MONTH(created_at) AS mois, SUM(amount_paid) AS montant
               FROM invoices
               WHERE YEAR(created_at)=YEAR(CURDATE())
               GROUP BY mois ORDER BY mois ASC";
$resMensuel = $conn->query($sqlMensuel);
$mois = [];
$ca_mensuel = [];
$moisNoms = [1=>"Jan","Fév","Mar","Avr","Mai","Juin","Juil","Août","Sept","Oct","Nov","Déc"];
if ($resMensuel) {
    while ($row = $resMensuel->fetch_assoc()) {
        $mois[] = $moisNoms[(int)$row['mois']] ?? $row['mois'];
        $ca_mensuel[] = (float)$row['montant'];
    }
}

// CA par ville
$sqlVille = "SELECT c.ville, SUM(i.amount_paid) AS total 
             FROM invoices i
             JOIN clients c ON i.customer_id=c.id
             WHERE YEAR(i.created_at)=YEAR(CURDATE())
             GROUP BY c.ville";
$resVille = $conn->query($sqlVille);
$villes = [];
$ca_villes = [];
if ($resVille) {
    while ($row = $resVille->fetch_assoc()) {
        $villes[] = $row['ville'] ?? "Inconnu";
        $ca_villes[] = (float)$row['total'];
    }
}

// CA par commercial
$sqlCom = "SELECT user_name, SUM(amount_paid) AS total 
           FROM invoices 
           WHERE YEAR(created_at)=YEAR(CURDATE())
           GROUP BY user_name";
$resCom = $conn->query($sqlCom);
$commerciaux = [];
$ca_commerciaux = [];
if ($resCom) {
    while ($row = $resCom->fetch_assoc()) {
        $commerciaux[] = $row['user_name'] ?? "Inconnu";
        $ca_commerciaux[] = (float)$row['total'];
    }
}

// ===============================
// Top 10 Produits (CA annuel)
// ===============================
$sqlTopProductsAnnuel = "
    SELECT i.name AS product_name, SUM(ii.quantity) AS total_vendu
    FROM invoice_items ii
    INNER JOIN item i ON ii.item_id = i.id
    INNER JOIN invoices inv ON ii.invoice_id = inv.id
    WHERE YEAR(inv.invoice_date) = YEAR(CURDATE())
    GROUP BY ii.item_id
    ORDER BY total_vendu DESC
    LIMIT 10
";
$resultTopProductsAnnuel = $conn->query($sqlTopProductsAnnuel);

$produits = [];
$ca_produits = [];
if ($resultTopProductsAnnuel) {
    while ($row = $resultTopProductsAnnuel->fetch_assoc()) {
        $produits[] = $row['product_name'] ?? "Inconnu";
        $ca_produits[] = (float)$row['total_vendu'];
    }
}


// --- Produits avec stock faible (seuil = 10 par défaut) ---
$sqlStockFaible = "
    SELECT i.name, s.current_quantity, s.weighted_avg_price
    FROM stock s
    INNER JOIN item i ON s.item_id = i.id
    WHERE s.current_quantity <= 10
    ORDER BY s.current_quantity ASC
    LIMIT 10
";

$resultStockFaible = $conn->query($sqlStockFaible);

if (!$resultStockFaible) {
    die("Erreur SQL : " . $conn->error);
}

$produits_stock_faible = array();
while ($row = $resultStockFaible->fetch_assoc()) {
    $produits_stock_faible[] = $row;
}



// Récupérer les paiements clients
$sql = "
   SELECT 
    c.id AS client_id,
    c.item_supplier AS client_name,
    MAX(i.due_date) AS derniere_echeance,
    SUM(i.total_ttc) AS total_a_payer,
    SUM(i.amount_paid) AS deja_paye,
    SUM(i.remaining_amount) AS reste_a_payer
FROM invoices i
LEFT JOIN clients c ON i.customer_id = c.id
GROUP BY c.id, c.item_supplier

";


$result = $conn->query($sql);
$clients_paiements = [];
$total_general = ["total" => 0, "paye" => 0, "reste" => 0];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $clients_paiements[] = [
            'id'     => $row['client_id'],
            'name'   => $row['client_name'],
            'echeance' => $row['derniere_echeance'],
            'total'  => (float)$row['total_a_payer'],
            'paye'   => (float)$row['deja_paye'],
            'reste'  => (float)$row['reste_a_payer']
        ];
        // Ajout au total général
        $total_general['total'] += (float)$row['total_a_payer'];
        $total_general['paye']  += (float)$row['deja_paye'];
        $total_general['reste'] += (float)$row['reste_a_payer'];
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord commercial</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .content-wrapper { padding:20px; background:#f4f6f9; }
        .small-box { border-radius: 10px; color:#fff; padding:20px; position:relative; overflow:hidden; margin-bottom:20px;}
        .small-box .icon { position:absolute; top:10px; right:10px; font-size:40px; opacity:0.3; }
        .box { border-radius:10px; box-shadow:0 3px 6px rgba(0,0,0,0.1); margin-bottom:20px;}
        canvas { width:100% !important; height:300px !important; }
    </style>
</head>
<body>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-dashboard"></i> Tableau de bord commercial</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3><?= number_format($ca_total,0,","," ") ?> FCFA</h3>
                        <p>Chiffre d'affaires</p>
                    </div>
                    <div class="icon"><i class="fa fa-money"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?= $taux_croissance ?>%</h3>
                        <p>Croissance</p>
                    </div>
                    <div class="icon"><i class="fa fa-line-chart"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-orange">
                    <div class="inner">
                        <h3><?= $nouveaux_clients ?></h3>
                        <p>Nouveaux Clients</p>
                    </div>
                    <div class="icon"><i class="fa fa-users"></i></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3><?= $factures_impayees ?></h3>
                        <p>Factures impayées</p>
                    </div>
                    <div class="icon"><i class="fa fa-exclamation-triangle"></i></div>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header"><h3 class="box-title">Chiffre d’affaires par mois</h3></div>
                    <div class="box-body"><canvas id="chartCA"></canvas></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-danger">
                    <div class="box-header"><h3 class="box-title">Répartition par ville</h3></div>
                    <div class="box-body"><canvas id="chartVille"></canvas></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-success">
                    <div class="box-header"><h3 class="box-title">Évolution du CA</h3></div>
                    <div class="box-body"><canvas id="chartEvolution"></canvas></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-warning">
                    <div class="box-header"><h3 class="box-title">Répartition par commercial</h3></div>
                    <div class="box-body"><canvas id="chartCommercial"></canvas></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header"><h3 class="box-title">Top 10 Produits vendus</h3></div>
                    <div class="box-body"><canvas id="chartProduit"></canvas></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-danger">
                    <div class="box-header">
                        <h3 class="box-title">📉 Stock faible / À réapprovisionner</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Stock actuel</th>
                                <th>Prix moyen (PMP)</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($produits_stock_faible)) { ?>
                                <?php foreach ($produits_stock_faible as $prod) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($prod['name']); ?></td>
                                        <td><span class="label label-danger"><?php echo $prod['current_quantity']; ?></span></td>
                                        <td><?php echo number_format($prod['weighted_avg_price'], 2, ',', ' '); ?> FCFA</td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="3" class="text-center">✅ Aucun produit en stock faible</td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-10">
                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title">💰 Récapitulatif des paiements clients</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Client</th>
                                <th>Dernière échéance</th>
                                <th>Total à payer</th>
                                <th>Déjà payé</th>
                                <th>Reste à payer</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($clients_paiements)) { ?>
                                <?php foreach ($clients_paiements as $client) {
                                    $classEcheance = "";
                                    if ($client['echeance']) {
                                        $dateEcheance = strtotime($client['echeance']);
                                        $today = strtotime(date("Y-m-d"));

                                        // Échéance dépassée ou dans les 5 prochains jours
                                        if ($dateEcheance <= $today || $dateEcheance <= strtotime("+5 days", $today)) {
                                            $classEcheance = "alert-date";
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($client['name']); ?></td>
                                        <td>
                                            <?php if ($client['echeance']) { ?>
                                                <span class="<?php echo $classEcheance; ?>">
                        <?php echo date("d/m/Y", strtotime($client['echeance'])); ?>
                    </span>
                                            <?php } else { ?>
                                                —
                                            <?php } ?>
                                        </td>
                                        <td><span class="label label-primary">
                <?php echo number_format($client['total'], 0, ',', ' '); ?> FCFA
            </span></td>
                                        <td><span class="label label-success">
                <?php echo number_format($client['paye'], 0, ',', ' '); ?> FCFA
            </span></td>
                                        <td>
                                            <?php if ($client['reste'] > 0) { ?>
                                                <span class="label label-danger">
                        <?php echo number_format($client['reste'], 0, ',', ' '); ?> FCFA
                    </span>
                                            <?php } else { ?>
                                                <span class="label label-success">✅ Soldé</span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <!-- ✅ Ligne Total Général -->
                                <tr style="font-weight:bold; background:#f9f9f9;">
                                    <td colspan="2" class="text-right">TOTAL GÉNÉRAL :</td>
                                    <td><?php echo number_format($total_general['total'], 0, ',', ' '); ?> FCFA</td>
                                    <td><?php echo number_format($total_general['paye'], 0, ',', ' '); ?> FCFA</td>
                                    <td><?php echo number_format($total_general['reste'], 0, ',', ' '); ?> FCFA</td>
                                </tr>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="5" class="text-center">✅ Aucun paiement enregistré</td>
                                </tr>
                            <?php } ?>
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
    // CA par mois
    new Chart(document.getElementById('chartCA').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($mois) ?>,
            datasets: [{
                label: 'CA (FCFA)',
                data: <?= json_encode($ca_mensuel) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)'
            }]
        },
        options: { responsive:true, maintainAspectRatio:false }
    });

    // CA par ville
    new Chart(document.getElementById('chartVille').getContext('2d'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($villes) ?>,
            datasets: [{
                data: <?= json_encode($ca_villes) ?>,
                backgroundColor: ['#36A2EB','#FF6384','#FFCE56','#4BC0C0','#9966FF']
            }]
        },
        options: { responsive:true, maintainAspectRatio:false }
    });

    // Évolution du CA
    new Chart(document.getElementById('chartEvolution').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= json_encode($mois) ?>,
            datasets: [{
                label: 'Évolution du CA',
                data: <?= json_encode($ca_mensuel) ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                fill:false,
                tension:0.1
            }]
        },
        options: { responsive:true, maintainAspectRatio:false }
    });

    // Répartition par commercial
    new Chart(document.getElementById('chartCommercial').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($commerciaux) ?>,
            datasets: [{
                data: <?= json_encode($ca_commerciaux) ?>,
                backgroundColor: ['#FF6384','#36A2EB','#FFCE56','#4BC0C0','#8BC34A']
            }]
        },
        options: { responsive:true, maintainAspectRatio:false }
    });

    // Répartition par produit
    new Chart(document.getElementById('chartProduit').getContext('2d'), {
        type: 'bar', // tu peux mettre 'doughnut' si tu veux un camembert
        data: {
            labels: <?= json_encode($produits) ?>,
            datasets: [{
                label: 'Quantité vendue',
                data: <?= json_encode($ca_produits) ?>,
                backgroundColor: ['#FF9F40','#4BC0C0','#9966FF','#FF6384','#36A2EB','#FFCE56','#8BC34A','#FF5722','#9E9E9E','#607D8B']
            }]
        },
        options: {
            responsive:true,
            maintainAspectRatio:false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero:true }
            }
        }
    });
</script>
</body>
</html>
