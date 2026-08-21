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
        background: transparent; /* pas de fond par défaut */
        transition: all 0.2s;
    }

    /* Icônes spécifiques avec fond coloré */
    .icon-ca {
        background: #2ecc71 !important;  /* vert */
    }
    .icon-croissance {
        background: #3498db !important;  /* bleu */
    }
    .icon-clients {
        background: #9b59b6 !important;  /* violet */
    }
    .icon-impayes {
        background: #e67e22 !important;  /* orange */
    }

    /* Icône à l'intérieur : blanche, centrée */
    .small-box .icon i {
        font-size: 28px;
        color: white !important;
        background: transparent !important;
        line-height: 1;
    }

    /* Ajustement pour les icônes existantes (si besoin) */
    .small-box .icon .fa-money,
    .small-box .icon .fa-briefcase,
    .small-box .icon .fa-bank,
    .small-box .icon .fa-random {
        color: white !important;
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

    /* Nouveau style pour les cartes sans fond coloré */
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
    .small-box .inner p {
        color: #6c757d;
        margin-bottom: 0;
    }
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
    /* Couleurs de fond uniquement pour les icônes */
    .icon-ca { background: white; color: white; }
    .icon-croissance { background: white; color: white; }
    .icon-clients { background: white; color: white; }
    .icon-impayes { background: white; color: white; }
</style>

<?php
// ===============================
// Connexion BDD
// ===============================
$CI = &get_instance();

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
// KPIs
// ===============================

// Chiffre d’affaires annuel

// ===============================
// CHIFFRE D’AFFAIRES ANNUEL
// ===============================
$sqlCA = "SELECT SUM(amount_paid) AS total_annuel 
          FROM invoices 
          WHERE YEAR(created_at) = YEAR(CURDATE())";
$resultCA = $conn->query($sqlCA);
$ca_total = ($resultCA && ($r = $resultCA->fetch_assoc())) ? (float)$r['total_annuel'] : 0;


// ===============================
// CROISSANCE MENSUELLE
// ===============================
// Mois courant
$sqlMoisCourant = "SELECT SUM(amount_paid) AS ca 
                   FROM invoices 
                   WHERE YEAR(created_at)=YEAR(CURDATE()) 
                   AND MONTH(created_at)=MONTH(CURDATE())";
$res1 = $conn->query($sqlMoisCourant);
$ca_mois_courant = ($res1 && ($r1 = $res1->fetch_assoc())) ? (float)$r1['ca'] : 0;

// Mois précédent (gestion du passage décembre → janvier)
$sqlMoisPrecedent = "SELECT SUM(amount_paid) AS ca 
                     FROM invoices 
                     WHERE 
                        (YEAR(created_at) = YEAR(CURDATE()) 
                         AND MONTH(created_at) = MONTH(CURDATE()) - 1)
                        OR (MONTH(CURDATE()) = 1 AND YEAR(created_at) = YEAR(CURDATE()) - 1 AND MONTH(created_at) = 12)";
$res2 = $conn->query($sqlMoisPrecedent);
$ca_mois_precedent = ($res2 && ($r2 = $res2->fetch_assoc())) ? (float)$r2['ca'] : 0;

$taux_croissance = ($ca_mois_precedent > 0)
    ? round((($ca_mois_courant - $ca_mois_precedent) / $ca_mois_precedent) * 100, 2)
    : 0;


// ===============================
// NOUVEAUX CLIENTS DE L’ANNÉE
// ===============================
$sqlClients = "SELECT COUNT(*) AS nb 
               FROM clients 
               WHERE YEAR(created_at) = YEAR(CURDATE())";
$resClients = $conn->query($sqlClients);
$nouveaux_clients = ($resClients && ($r3 = $resClients->fetch_assoc())) ? (int)$r3['nb'] : 0;


// ===============================
// FACTURES IMPAYÉES
// ===============================
$sqlImpaye = "SELECT COUNT(*) AS nb 
              FROM invoices 
              WHERE status = 1";
$resImpaye = $conn->query($sqlImpaye);
$factures_impayees = ($resImpaye && ($r4 = $resImpaye->fetch_assoc())) ? (int)$r4['nb'] : 0;


// ===============================
// CHIFFRE D’AFFAIRES MENSUEL
// ===============================
$sqlMensuel = "SELECT MONTH(created_at) AS mois, SUM(amount_paid) AS montant
               FROM invoices
               WHERE YEAR(created_at)=YEAR(CURDATE())
               GROUP BY mois 
               ORDER BY mois ASC";
$resMensuel = $conn->query($sqlMensuel);

$mois = [];
$ca_mensuel = [];
$moisNoms = [1 => "Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sept", "Oct", "Nov", "Déc"];
if ($resMensuel) {
    while ($r5 = $resMensuel->fetch_assoc()) {
        $mois[] = $moisNoms[(int)$r5['mois']] ?? $r5['mois'];
        $ca_mensuel[] = (float)$r5['montant'];
    }
}


// ===============================
// CHIFFRE D’AFFAIRES PAR VILLE
// ===============================
$sqlVille = "SELECT c.ville, SUM(i.amount_paid) AS total 
             FROM invoices i
             JOIN clients c ON i.customer_id = c.id
             WHERE YEAR(i.created_at) = YEAR(CURDATE())
             GROUP BY c.ville";
$resVille = $conn->query($sqlVille);

$villes = [];
$ca_villes = [];
if ($resVille) {
    while ($r6 = $resVille->fetch_assoc()) {
        $villes[] = $r6['ville'] ?? "Inconnu";
        $ca_villes[] = (float)$r6['total'];
    }
}


// ===============================
// CHIFFRE D’AFFAIRES PAR COMMERCIAL + OBJECTIFS
// ===============================
// 1ï¸ÂÂÂÂâÂÂÂÂ£ Ventes réelles
// 1ï¸ÂÂÂÂâÂÂÂÂ£ Chiffre d’affaires par commercial
$sqlCom = "
    SELECT user_name, SUM(amount_paid) AS total
    FROM invoices
    WHERE YEAR(created_at) = YEAR(CURDATE())
    GROUP BY user_name
";
$resCom = $conn->query($sqlCom);

$ca_commerciaux = [];
if ($resCom) {
    while ($r7 = $resCom->fetch_assoc()) {
        $user = trim($r7['user_name'] ?? 'Inconnu');
        $ca_commerciaux[$user] = (float)$r7['total'];
    }
}

// 2ï¸ÂÂÂÂâÂÂÂÂ£ Objectifs commerciaux
$objectifs = [];
$sqlGoals = "SELECT user_name, target_amount FROM objectifs_commercial";
$resGoals = $conn->query($sqlGoals);

if ($resGoals) {
    $mois_actuel = (int)date('n'); // Mois en cours
    while ($r8 = $resGoals->fetch_assoc()) {
        $commercial = trim($r8['user_name']);
        // $objectif_cumule = (float)$r8['target_amount'] * $mois_actuel;
        $objectif_cumule = (float)$r8['target_amount'];
        $objectifs[$commercial] = $objectif_cumule;

        // Ajouter le commercial même sans vente
        if (!isset($ca_commerciaux[$commercial])) {
            $ca_commerciaux[$commercial] = 0;
        }
    }
}

// 3ï¸ÂÂÂÂâÂÂÂÂ£ Liste finale des commerciaux (tous, sans doublon)
$commerciaux = array_unique(array_merge(array_keys($ca_commerciaux), array_keys($objectifs)));

// 4ï¸ÂÂÂÂâÂÂÂÂ£ Objectif par défaut (si non défini)
foreach ($commerciaux as $commercial) {
    if (!isset($objectifs[$commercial])) {
        $objectifs[$commercial] = 0;
    }
}

// 5ï¸ÂÂÂÂâÂÂÂÂ£ Taux de réalisation (%)
$taux_realisation = [];
foreach ($commerciaux as $commercial) {
    $ca = $ca_commerciaux[$commercial] ?? 0;
    $obj = $objectifs[$commercial] ?? 1;
    $taux_realisation[$commercial] = $obj > 0 ? round(($ca / $obj) * 100, 2) : 0;
}


// ===============================
// TOP 10 PRODUITS DE L’ANNÉE (MONTANT GÉNÉRÉ)
// ===============================
$sqlTopProductsAnnuel = "
    SELECT i.name AS product_name, SUM(ii.line_total) AS montant_vendu
    FROM invoice_items ii
    INNER JOIN item i ON ii.item_id = i.id
    INNER JOIN invoices inv ON ii.invoice_id = inv.id
    WHERE YEAR(inv.invoice_date) = YEAR(CURDATE())
    GROUP BY ii.item_id
    ORDER BY montant_vendu DESC
    LIMIT 10
";
$resultTopProductsAnnuel = $conn->query($sqlTopProductsAnnuel);

$produits = [];
$ca_produits = []; // contiendra les montants
if ($resultTopProductsAnnuel) {
    while ($r9 = $resultTopProductsAnnuel->fetch_assoc()) {
        $produits[] = $r9['product_name'] ?? "Inconnu";
        $ca_produits[] = (float)$r9['montant_vendu'];
    }
}


// ===============================
// STOCK FAIBLE
// ===============================
$sqlStockFaible = "
    SELECT i.name, s.current_quantity, s.weighted_avg_price
    FROM stock s
    INNER JOIN item i ON s.item_id = i.id
    WHERE s.current_quantity <= 10
    ORDER BY s.current_quantity ASC
    LIMIT 10
";
$resultStockFaible = $conn->query($sqlStockFaible);
if (!$resultStockFaible) die("Erreur SQL : " . $conn->error);

$produits_stock_faible = [];
while ($r10 = $resultStockFaible->fetch_assoc()) {
    $produits_stock_faible[] = $r10;
}


// ===============================
// PAIEMENTS CLIENTS
// ===============================
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
    while ($r11 = $result->fetch_assoc()) {
        $clients_paiements[] = [
            'id' => $r11['client_id'],
            'name' => $r11['client_name'],
            'echeance' => $r11['derniere_echeance'],
            'total' => (float)$r11['total_a_payer'],
            'paye' => (float)$r11['deja_paye'],
            'reste' => (float)$r11['reste_a_payer']
        ];

        $total_general['total'] += (float)$r11['total_a_payer'];
        $total_general['paye'] += (float)$r11['deja_paye'];
        $total_general['reste'] += (float)$r11['reste_a_payer'];
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
        .small-box { border-radius: 10px; background: #ffffff; box-shadow: 0 3px 6px rgba(0,0,0,0.1); padding:20px; position:relative; overflow:hidden; margin-bottom:20px; border:1px solid #e9ecef; }
        .small-box .inner h3 { margin:0 0 10px 0; font-size:17px; font-weight:bold; color:#2c3e50; }
        .small-box .inner p { color:#6c757d; margin-bottom:0; }
        .small-box .icon { position:absolute; top:14px; right:2px; font-size:14px; opacity:0.8; border-radius:50%; width:51px; height:50px; display:flex; align-items:center; justify-content:center; }
        .icon-ca { background: white; color: #00a65a; }
        .icon-croissance { background: white; color: white; }
        .icon-clients { background: white; color: white; }
        .icon-impayes { background: white; color: white; }
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
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($ca_total, 0, ",", " ") ?> FCFA</h3>
                        <p>Chiffre d'affaires (Année en cours)</p>
                    </div>
                    <div class="icon icon-ca">
                        <i class="fa fa-line-chart"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= $taux_croissance ?>%</h3>
                        <p>Croissance</p>
                    </div>
                    <div class="icon icon-croissance">
                        <i class="fa fa-line-chart"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= $nouveaux_clients ?></h3>
                        <p>Nouveaux Clients</p>
                    </div>
                    <div class="icon icon-clients">
                        <i class="fa fa-users"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= $factures_impayees ?></h3>
                        <p>Factures impayées</p>
                    </div>
                    <div class="icon icon-impayes">
                        <i class="fa fa-exclamation-triangle"></i>
                    </div>
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
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header"><h3 class="box-title">Évolution du CA</h3></div>
                    <div class="box-body"><canvas id="chartEvolution"></canvas></div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">Répartition par commercial</h3>
                        <div class="box-tools pull-right">
                            <small>Année en cours</small>
                        </div>
                    </div>

                    <div class="box-body">
                        <!-- Graphique -->
                        <div style="height: 250px; margin-bottom: 20px;">
                            <canvas id="chartCommercial"></canvas>
                        </div>

                        <!-- Détails des commerciaux -->
                        <div class="commercial-details" style="max-height: 250px; overflow-y: auto;">
                            <?php foreach ($commerciaux as $commercial):
                                $ca_atteint = $ca_commerciaux[$commercial] ?? 0;
                                $objectif = $objectifs[$commercial] ?? 0;
                                $pourcentage = $objectif > 0 ? min(100, ($ca_atteint / $objectif) * 100) : 0;
                                $reste = max(0, $objectif - $ca_atteint);
                                $couleur = $pourcentage >= 100 ? '#28a745' : ($pourcentage >= 80 ? '#ffc107' : '#dc3545');
                                ?>
                                <div class="commercial-item"
                                     style="margin-bottom: 12px; padding: 10px;
                                             border-left: 4px solid <?= $couleur ?>;
                                             background: #f8f9fa; border-radius: 4px;">

                                    <div style="font-weight: bold; color: #333; margin-bottom: 5px; font-size: 0.95em;">
                                        <?= htmlspecialchars($commercial) ?>
                                        <?php if ($pourcentage >= 100): ?>
                                            <span style="color: #28a745; float: right;"></span>
                                        <?php endif; ?>
                                    </div>

                                    <div style="display: flex; justify-content: space-between; font-size: 0.85em; margin-bottom: 8px;">
                            <span>
                                Objectif annuel :
                              <strong><?= number_format($objectif, 0, ',', ' ') ?> FCFA</strong>
                            </span>
                                        <span>
                                Réalisé :
                                <strong><?= number_format((int)$ca_atteint, 0, ',', ' ') ?> FCFA</strong>
                            </span>
                                    </div>

                                    <div style="margin-top: 5px;">
                                        <div style="background: #e9ecef; border-radius: 10px; height: 8px; overflow: hidden; margin-bottom: 5px;">
                                            <div style="height: 100%; background: <?= $couleur ?>;
                                                    width: <?= $pourcentage ?>%; border-radius: 10px;
                                                    transition: width 0.5s ease;">
                                            </div>
                                        </div>

                                        <div style="display: flex; justify-content: space-between; font-size: 0.8em; color: #6c757d;">
                                            <span><strong><?= number_format($pourcentage, 1, ',', ' ') ?>%</strong></span>
                                            <span>
                                    <?php if ($pourcentage >= 100): ?>
                                        <span style="color: #28a745;">
                                            +<?= number_format($ca_atteint - $objectif, 0, ',', ' ') ?> FCFA
                                        </span>
                                    <?php else: ?>
                                        Reste : <?= number_format($reste, 0, ',', ' ') ?> FCFA
                                    <?php endif; ?>
                                </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>




            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header"><h3 class="box-title">Top 10 Produits vendus (Montant généré)</h3></div>
                    <div class="box-body"><canvas id="chartProduit"></canvas></div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="box box-danger">
                    <div class="box-header">
                        <h3 class="box-title">Stock faible / À réapprovisionner</h3>
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
                                    <td colspan="3" class="text-center">Aucun produit en stock faible</td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title">Récapitulatif des paiements clients</h3>
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
                                                <span class="label label-success">Soldé</span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <!-- âÂÂÂÂÂÂÂÂ Ligne Total Général -->
                                <tr style="font-weight:bold; background:#f9f9f9;">
                                    <td colspan="2" class="text-right">TOTAL GÉNÉRAL :</td>
                                    <td><?php echo number_format($total_general['total'], 0, ',', ' '); ?> FCFA</td>
                                    <td><?php echo number_format($total_general['paye'], 0, ',', ' '); ?> FCFA</td>
                                    <td><?php echo number_format($total_general['reste'], 0, ',', ' '); ?> FCFA</td>
                                </tr>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="5" class="text-center">Aucun paiement enregistré</td>
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
        type: 'bar',
        data: {
            labels: <?= json_encode($commerciaux) ?>,
            datasets: [
                {
                    label: 'Objectif',
                    data: <?= json_encode(array_map(function($commercial) use ($objectifs) {
                        return $objectifs[$commercial] ?? 0;
                    }, $commerciaux)) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: '#36A2EB',
                    borderWidth: 1
                },
                {
                    label: 'Réalisé',
                    data: <?= json_encode($ca_commerciaux) ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: '#4BC0C0',
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
                },
                x: {
                    ticks: {
                        maxRotation: 0,     // Pas de rotation
                        minRotation: 0,     // Pas de rotation
                        autoSkip: false,    // Ne pas sauter d'étiquettes
                        callback: function(value, index, values) {
                            // Retourner le nom normalement (horizontal)
                            return this.getLabelForValue(value);
                        },
                        font: {
                            size: 11,        // Taille de police ajustable
                            weight: 'normal'
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
                            const commercial = context.label;

                            // Calcul du pourcentage pour l'info-bulle
                            const objectifs = <?= json_encode($objectifs) ?>;
                            const realises = <?= json_encode($ca_commerciaux) ?>;
                            const objectif = objectifs[commercial] || 0;
                            const realise = realises[context.dataIndex] || 0;
                            const pourcentage = objectif > 0 ? (realise / objectif * 100) : 0;

                            if (context.datasetIndex === 1) { // Dataset "Réalisé"
                                return [
                                    `${label}: ${value.toLocaleString('fr-FR')} FCFA`,
                                    `Objectif: ${objectif.toLocaleString('fr-FR')} FCFA`,
                                    `Atteint: ${pourcentage.toFixed(1)}%`
                                ];
                            }
                            return `${label}: ${value.toLocaleString('fr-FR')} FCFA`;
                        }
                    }
                },
                legend: {
                    position: 'top',
                }
            }
        }
    });

    // Top 10 Produits : Montant généré (FCFA)
    new Chart(document.getElementById('chartProduit').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($produits) ?>,
            datasets: [{
                label: 'Montant (FCFA)',
                data: <?= json_encode($ca_produits) ?>,
                backgroundColor: ['#FF9F40','#4BC0C0','#9966FF','#FF6384','#36A2EB','#FFCE56','#8BC34A','#FF5722','#9E9E9E','#607D8B']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y', // Barres horizontales
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Montant: ' + context.raw.toLocaleString('fr-FR') + ' FCFA';
                        }
                    }
                }
            },
            scales: {
                x: {
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
                y: {
                    ticks: {
                        callback: function(value, index, values) {
                            const label = this.getLabelForValue(value);
                            if (label.length > 40) {
                                return label.substring(0, 37) + '...';
                            }
                            return label;
                        },
                        font: { size: 11 }
                    }
                }
            }
        }
    });
</script>
</body>
</html>