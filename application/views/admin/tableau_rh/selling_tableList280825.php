<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        padding: 8px;
        border: 1px solid #ccc;
    }

    .yes { color: green; font-weight: bold; }
    .no { color: red; font-weight: bold; }
</style>
<?php
$conn = new mysqli("localhost", "root", "", "diago");

// Total CA de l'année en cours
$sqlCA = "SELECT SUM(total_ttc) AS total_annuel 
          FROM invoices 
          WHERE YEAR(created_at) = YEAR(CURDATE())";

$resultCA = $conn->query($sqlCA);
$totalAnnuel = 0;
if ($resultCA && $row17 = $resultCA->fetch_assoc()) {
    $totalAnnuel = $row17['total_annuel'];
}
?>

<?php

function traduireMois($mois)
{
    $mois_fr = [
        '01' => 'Janvier', '02' => 'Février', '03' => 'Mars',
        '04' => 'Avril', '05' => 'Mai', '06' => 'Juin',
        '07' => 'Juillet', '08' => 'Août', '09' => 'Septembre',
        '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre',
    ];

    // $mois est sous forme "2025-07"
    $parts = explode('-', $mois);
    return $mois_fr[$parts[1]];
}

// Exemple : traduction du tableau
$moisLabels10 = array_map('traduireMois', $moisLabels10);


?>
<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<!-- Content Wrapper. Contains page content -->
<?php
$conn = new mysqli("localhost","root","","diago");
$sql1 = "SELECT SUM(total_ttc) AS total_amount  FROM invoices";

$result1 = $conn->query($sql1);

$sql2 = "SELECT SUM(remaining_amount) AS remain_amount  FROM invoices";
$result2 = $conn->query($sql2);

$sql3 = "SELECT SUM(amount_paid) AS amount_paid  FROM invoices";
$result3 = $conn->query($sql3);


$sql4 = "SELECT SUM(amount_paid) AS montant_mois_courant 
        FROM invoices 
        WHERE MONTH(created_at) = MONTH(CURDATE()) 
          AND YEAR(created_at) = YEAR(CURDATE())";
$result4 = $conn->query($sql4);

$sql5 = "SELECT SUM(amount_paid) AS chiffre_affaire 
        FROM invoices 
        WHERE YEAR(created_at) = YEAR(CURDATE())";
$result5 = $conn->query($sql5);

$sql6 = "SELECT MONTH(created_at) AS mois, 
               SUM(amount_paid) AS chiffre_affaire
        FROM invoices
        WHERE YEAR(created_at) = YEAR(CURDATE()) 
        GROUP BY mois
        ORDER BY mois ASC";
$result6 = $conn->query($sql6);
$mois = [];
$montants = [];

while ($row6 = $result6->fetch_assoc()) {
    // Convertir numéro du mois en nom (facultatif)
    $moisNoms = [1 => "Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sept", "Oct", "Nov", "Déc"];
    $mois[] = $moisNoms[(int)$row6['mois']];
    $montants[] = (float)$row6['chiffre_affaire'];
}

$sql7 = "SELECT MONTH(created_at) AS mois, 
               SUM(amount_paid) AS chiffre_affaire
        FROM invoices
        WHERE YEAR(created_at) = YEAR(CURDATE()) 
        GROUP BY mois
        ORDER BY mois ASC";
$result7 = $conn->query($sql7);

$mois = [];
$montants = [];

while ($row7 = $result7->fetch_assoc()) {
    // Convertir numéro du mois en nom (facultatif)
    $moisNoms = [1 => "Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sept", "Oct", "Nov", "Déc"];
    $mois[] = $moisNoms[(int)$row7['mois']];
    $montants[] = (float)$row7['chiffre_affaire'];
}

$sql7 = "SELECT item_id, current_quantity, initial_quantity 
        FROM stock 
        WHERE current_quantity <= 100";
$result7 = $conn->query($sql7);
$moisLabelsFr = array_map(function($mois) {
    $moisEn = [
        'January' => 'Janvier',
        'February' => 'Février',
        'March' => 'Mars',
        'April' => 'Avril',
        'May' => 'Mai',
        'June' => 'Juin',
        'July' => 'Juillet',
        'August' => 'Août',
        'September' => 'Septembre',
        'October' => 'Octobre',
        'November' => 'Novembre',
        'December' => 'Décembre'
    ];

    $timestamp = strtotime($mois . '-01');
    $moisAnglais = date('F', $timestamp); // Exemple : 'May'
    return $moisEn[$moisAnglais] ?? $mois;
}, $moisLabels);



$sql_objectifs = "SELECT role AS commercial, objectif_annuel FROM objectifs_commercial";
$result_objectifs = $conn->query($sql_objectifs);

$objectifs = [];
if ($result_objectifs && $result_objectifs->num_rows > 0) {
    while ($row = $result_objectifs->fetch_assoc()) {
        $objectifs[$row['commercial']] = (float) $row['objectif_annuel'];
    }
}
// 🔄 Requête pour les ventes
$sql = "
    SELECT 
        user_name AS commercial,
        invoice_date,
        DATE_FORMAT(invoice_date, '%Y-%m') AS mois,
        amount_paid
    FROM invoices
     WHERE invoice_date >= '2025-01-01'
    ORDER BY invoice_date ASC
";
$result = $conn->query($sql);

// 📦 Traitement des données
$commerciaux = [];
$moisLabels = [];
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $nom = $row['commercial'];
        $mois = $row['mois'];
        $date = $row['invoice_date'];
        $montant = (float)$row['amount_paid'];

        // Initialisation
        if (!isset($data[$nom])) {
            $data[$nom] = [
                'objectif' => $objectifs[$nom] ?? 0,
                'mois' => [],
                'ventes_mensuelles' => [],
                'total' => 0
            ];
        }

        // Ajout de la date complète pour affichage (optionnel)
        $data[$nom]['mois'][$date] = $montant;

        // Ventes mensuelles (pour le chart)
        if (!isset($data[$nom]['ventes_mensuelles'][$mois])) {
            $data[$nom]['ventes_mensuelles'][$mois] = 0;
        }
        $data[$nom]['ventes_mensuelles'][$mois] += $montant;

        $data[$nom]['total'] += $montant;

        if (!in_array($nom, $commerciaux)) $commerciaux[] = $nom;
        if (!in_array($mois, $moisLabels)) $moisLabels[] = $mois;
    }
}

sort($moisLabels);
sort($commerciaux);
// Traduction des mois en français pour l'affichage du graphique
$moisLabelsFr = array_map('traduireMois', $moisLabels);

?>

<div class="content-wrapper">

    <section class="content-header">
        <h1>
            <i class="fa fa-credit-card"></i> <?php echo $this->lang->line('expenses'); ?> <small><?php echo $this->lang->line('student_fee'); ?></small></h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php
            if ($this->rbac->hasPrivilege('panneau', 'can_add')) {
                ?>
                <div class="col-md-4" hidden>
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <?php while ($row1 = $result1->fetch_object()): ?>
                                <p> <?php echo $this->lang->line(''); ?>  <b>Total des ventes : <?php echo $row1->total_amount ?>  FCFA </b></p>
                            <?php endwhile; ?>
                        </div><!-- /.box-header -->
                        </div>

                </div><!--/.col (right) -->
                <div class="col-md-4" hidden>
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <?php while ($row2 = $result2->fetch_object()): ?>
                                <p> <?php echo $this->lang->line(''); ?>  <b>Total Montant restant : <?php echo $row2->remain_amount ?>  FCFA </b></p>
                            <?php endwhile; ?>
                        </div><!-- /.box-header -->
                    </div>

                </div><!--/.col (right) -->
                <div class="col-md-4" hidden>
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <?php while ($row3 = $result3->fetch_object()): ?>
                                <p> <?php echo $this->lang->line(''); ?>  <b>Total Montant payé : <?php echo $row3->amount_paid ?>  FCFA </b></p>
                            <?php endwhile; ?>
                        </div><!-- /.box-header -->
                    </div>

                </div><!--/.col (right) -->
                <div class="col-md-12 mt-3">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="alert alert-success text-center" style="font-size:18px; font-weight:bold;">
                            <?php while ($row4 = $result4->fetch_object()): ?>
                                <p>  <b>💰Vente Mensuel : <?php echo $row4->montant_mois_courant ?>  FCFA </b></p>

                            <?php endwhile; ?>
                        </div><!-- /.box-header -->
                    </div>

                </div>


                <!--/.col (right) -->
                <div class="col-md-4" hidden>
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <?php while ($row5 = $result5->fetch_object()): ?>
                                <p>  <b>Vente Annuel : <?php echo $row5->chiffre_affaire  ?>  FCFA </b></p>

                            <?php endwhile; ?>
                        </div><!-- /.box-header -->
                    </div>

                </div><!--/.col (right) -->

                <div class="col-md-12 mt-3">
                    <div class="alert alert-success text-center" style="font-size:18px; font-weight:bold;">
                        💰 Chiffre d’affaires total <?= date('Y') ?> :
                        <?= number_format($totalAnnuel, 0, ',', ' ') ?> FCFA
                    </div>
                </div><!--/.col (right) -->
               <!-- <div class="col-md-4">

                    <div class="box box-primary">
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                        <div class="box-header with-border">
                            <?php

                            if ($result7->num_rows > 0) {
                                echo "<h3 style='color:red'>⚠️ Alertes de stock :</h3>";
                                while ($row7 = $result7->fetch_assoc()) {
                                    $nom = $row7['item_id'];
                                    $qte = $row7['initial_quantity'];
                                    $seuil = $row7['current_quantity'];

                                    if ($qte == 0) {
                                        echo "<div style='color: red; font-weight: bold;'>❌ $nom est en rupture de stock !</div>";
                                    } else {
                                        echo "<div style='color: orange;'>⚠️ $nom est bientôt épuisé (stock restant : $qte unités)</div>";
                                    }
                                }
                            } else {
                                echo "<div style='color: green;'>✅ Tous les stocks sont suffisants.</div>";
                            }
                            ?>
                        </div>
                    </div>

                </div>--><!--/.col (right) -->


                <!--/.col (right) -->





                <hr>

                <div class="col-md-12">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                        <div class="box-header with-border">
                            <hr>
                            <h3>📊 Graphiques individuels des commerciaux</h3>
                            <?php foreach ($commerciaux as $i => $nom): ?>
                                <div class="box box-primary">
                                    <h4><?= $nom ?></h4>
                                    <canvas id="chartCommercial<?= $i ?>" height="100"></canvas>
                                </div>
                            <?php endforeach; ?>

                        </div><!-- /.box-header -->
                    </div>

                </div><!--/.col (right) -->

                <div class="col-md-12" hidden>
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3>📋 Tableau récapitulatif</h3>
                        </div>
                        <div class="box-body table-responsive">
                            <h2>📊 Tableau récapitulatif des commerciaux</h2>
                            <table>
                                <thead>
                                <tr>
                                    <th>Commercial</th>
                                    <th>Objectif Annuel</th>
                                   <!-- <th>Ventes par Mois</th>-->
                                    <th>Total Annuel</th>
                                    <th>Objectif Atteint</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($data as $nom => $infos): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($nom) ?></td>
                                        <td><?= number_format($infos['objectif'], 0, ',', ' ') ?> FCFA</td>
                                       <!-- <td>
                                            <?php foreach ($infos['mois'] as $mois => $val): ?>
                                                <?= $mois ?> : <?= number_format($val, 0, ',', ' ') ?> FCFA<br>
                                            <?php endforeach; ?>
                                        </td>-->
                                        <td><?= number_format($infos['total'], 0, ',', ' ') ?> FCFA</td>
                                        <td class="<?= $infos['total'] >= $infos['objectif'] ? 'yes' : 'no' ?>">
                                            <?= $infos['total'] >= $infos['objectif'] ? '✅ Oui' : '❌ Non' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>




                <!-- left column -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('panneau', 'can_add')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->

            </div><!--/.col (left) -->

        </div>
        <div class="row">
            <!-- left column -->

            <!-- right column -->
            <div class="col-md-12">


            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script>
    ( function ( $ ) {
        'use strict';
        $(document).ready(function () {
            initDatatable('expense-list','admin/expense/getexpenselist',[],[],10);
        });
    } ( jQuery ) )
</script>

<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
    const ctxCA = document.getElementById('chartCA').getContext('2d');

    const chartCA = new Chart(ctxCA, {
        type: 'bar',
        data: {
            labels: <?= json_encode($mois) ?>, // ✅ Les mois en bas
            datasets: [{
                label: 'Chiffre d’affaires (FCFA)',
                data: <?= json_encode($montants) ?>, // ✅ Les montants en haut
                backgroundColor: [
                    '#638fff', '#36a2eb', '#fed865', '#ffce56', '#2ecc71',
                    '#e67e22', '#9b59b6', '#1abc9c', '#f1c40f', '#34495e',
                    '#e74c3c', '#95a5a6'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                datalabels: {
                    anchor: 'end',
                    align: 'end',
                    formatter: function(value) {
                        return value.toLocaleString('fr-FR') + ' FCFA';
                    },
                    color: '#000',
                    font: {
                        weight: 'bold',
                        size: 11
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Mois'
                    }
                },
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
            }
        },
        plugins: [ChartDataLabels]
    });
</script>


<script>
    const ctxSales = document.getElementById('monthlySalesChart').getContext('2d');

    const chartSales = new Chart(ctxSales, {
        type: 'line',
        data: <?= json_encode($chartData) ?>,
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<script>
    const ctxSales2 = document.getElementById('monthlySalesChart1').getContext('2d');

    const chartSales2 = new Chart(ctxSales2, {
        type: 'line',
        data: <?= json_encode($chartData) ?>,
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.formattedValue + ' FCFA';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('fr-FR') + ' FCFA';
                        }
                    }
                }
            }
        }
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    <?php foreach ($commerciaux as $i => $nom): ?>
    const ctx<?= $i ?> = document.getElementById('chartCommercial<?= $i ?>').getContext('2d');
    new Chart(ctx<?= $i ?>, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_map(function($mois) {
                setlocale(LC_TIME, 'fr_FR.UTF-8'); // Pour les serveurs configurés en français
                $timestamp = strtotime($mois . '-01'); // Convertir '2025-07' en timestamp
                return ucfirst(strftime('%B', $timestamp)); // Exemple : 'Juillet'
            }, $moisLabels)) ?>,

            datasets: [{
                label: '<?= addslashes($nom) ?>',
                data: <?= json_encode(array_map(function($mois) use ($data, $nom) {
                    return $data[$nom]['ventes_mensuelles'][$mois] ?? 0;
                }, $moisLabels)) ?>,
                backgroundColor: '#<?= substr(md5($nom), 0, 6) ?>'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => value.toLocaleString('fr-FR') + ' FCFA'
                    }
                }
            }
        }
    });
    <?php endforeach; ?>
</script>





