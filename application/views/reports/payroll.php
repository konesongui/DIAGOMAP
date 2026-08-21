<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

$moisEn = [
    'January' => 'Janvier', 'February' => 'Février', 'March' => 'Mars',
    'April' => 'Avril', 'May' => 'Mai', 'June' => 'Juin',
    'July' => 'Juillet', 'August' => 'Août', 'September' => 'Septembre',
    'October' => 'Octobre', 'November' => 'Novembre', 'December' => 'Décembre',
];

// CALCULS DU BULLETIN DE PAIE
$total_brute = $result["categorie_salaire"] + $result["sursalaire"] + $result["prime_anc"] +
    $result["prime_trans"] + $result["forfait_hs"] + $result["prime_resp"] +
    $result["prime_rend"] + $result["prime_risque"] + $result["prime_assi"] +
    $result["prime_grati"] + $result["conge"];

$total_pourcentage = $total_brute * 0.1;
$primet = 30000;

// Prime transport (exonérée jusqu'à 30,000)
$final_trans = ($result["prime_trans"] > $primet) ? $result["prime_trans"] - $primet : 0;

// Prime rendement (exonérée jusqu'à 10% du total brut)
$final_rend = ($result["prime_rend"] > $total_pourcentage) ? $result["prime_rend"] - $total_pourcentage : 0;

// Prime risque (exonérée jusqu'à 10% du total brut)
$final_risq = ($result["prime_risque"] > $total_pourcentage) ? $result["prime_risque"] - $total_pourcentage : 0;

// Prime assiduité (exonérée jusqu'à 10% du total brut)
$final_assi = ($result["prime_assi"] > $total_pourcentage) ? $result["prime_assi"] - $total_pourcentage : 0;

// Forfait HS (exonéré jusqu'à 10% du total brut)
$final_forfait = ($result["forfait_hs"] > $total_pourcentage) ? $result["forfait_hs"] - $total_pourcentage : 0;

// Sursalaire (exonéré jusqu'à 10% du total brut)
$final_sura = ($result["sursalaire"] > $total_pourcentage) ? $result["sursalaire"] - $total_pourcentage : 0;

// TOTAL FISCAL (base imposable)
$total_fiscal = $result["categorie_salaire"] + $result["prime_anc"] + $final_trans +
    $final_rend + $final_risq + $final_assi + $final_sura;

// TOTAL BRUT SOCIAL (base CNPS/CMU)
$total_social = $total_brute - $result["prime_trans"];

// CALCUL ITS (Impôt sur le Traitement des Salaires)
$impot = 0;
$salaire_restant = $total_fiscal;

if ($salaire_restant > 8000000) {
    $impot += ($salaire_restant - 8000000) * 0.32;
    $salaire_restant = 8000000;
}

if ($salaire_restant > 2400000) {
    $impot += ($salaire_restant - 2400000) * 0.28;
    $salaire_restant = 2400000;
}

if ($salaire_restant > 800000) {
    $impot += ($salaire_restant - 800000) * 0.24;
    $salaire_restant = 800000;
}

if ($salaire_restant > 240000) {
    $impot += ($salaire_restant - 240000) * 0.21;
    $salaire_restant = 240000;
}

if ($salaire_restant > 75000) {
    $impot += ($salaire_restant - 75000) * 0.16;
}

// Réduction familiale
$coef = $result["part_igr"] ?? 1;
$reductions = [1=>0, 1.5=>5500, 2=>11000, 2.5=>16500, 3=>22000,
    3.5=>27500, 4=>33000, 4.5=>38500, 5=>44000];

$reduction = $reductions[$coef] ?? 0;
$impot_net = max($impot - $reduction, 0);
$its = round($impot_net, 2);

// CALCUL DES COTISATIONS SOCIALES
$cnps = $total_social * 0.08;  // Patronale 8%
$cmu = $total_social * 0.05;   // CMU 5%

// TOTAL REVENU IMPOSABLE
$total_revenu = $its + $cmu + $cnps;

// SALAIRE NET
$net_salary = $total_brute - $cnps - $cmu - $its;
?>

<style type="text/css">
    /*REQUIRED*/
    .carousel-row {
        margin-bottom: 10px;
    }
    .text-primary{
        color: black;
        text-transform: uppercase;
    }
    .slide-row {
        padding: 0;
        background-color: #ffffff;
        min-height: 150px;
        border: 1px solid #e7e7e7;
        overflow: hidden;
        height: auto;
        position: relative;
    }
    .slide-carousel {
        width: 20%;
        float: left;
        display: inline-block;
    }
    .slide-carousel .carousel-indicators {
        margin-bottom: 0;
        bottom: 0;
        background: rgba(0, 0, 0, .5);
    }
    .slide-carousel .carousel-indicators li {
        border-radius: 0;
        width: 20px;
        height: 6px;
    }
    .slide-carousel .carousel-indicators .active {
        margin: 1px;
    }
    .slide-content {
        position: absolute;
        top: 0;
        left: 20%;
        display: block;
        float: left;
        width: 80%;
        max-height: 76%;
        padding: 1.5% 2% 2% 2%;
        overflow-y: auto;
    }
    .slide-content h4 {
        margin-bottom: 3px;
        margin-top: 0;
    }
    .slide-footer {
        position: absolute;
        bottom: 0;
        left: 20%;
        width: 78%;
        height: 20%;
        margin: 1%;
    }
    /* Scrollbars */
    .slide-content::-webkit-scrollbar {
        width: 5px;
    }
    .slide-content::-webkit-scrollbar-thumb:vertical {
        margin: 5px;
        background-color: #999;
        -webkit-border-radius: 5px;
    }
    .slide-content::-webkit-scrollbar-button:start:decrement,
    .slide-content::-webkit-scrollbar-button:end:increment {
        height: 5px;
        display: block;
    }
</style>

<div class="content-wrapper" style="min-height: 946px;">
    <section class="content">
        <?php $this->load->view('reports/_finance'); ?>

        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">

                    <!-- En-tête avec titre, filtre et boutons d'exportation -->
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-money"></i> Livre de paie</h3>
                        <div class="pull-right box-tools">
                            <form role="form" action="<?php echo site_url('report/payroll') ?>" method="post" class="form-inline" style="display: inline-block;">
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="form-group">
                                    <select class="form-control input-sm" name="search_type" onchange="showdate(this.value)">
                                        <?php foreach ($searchlist as $key => $search) { ?>
                                            <option value="<?php echo $key ?>" <?php echo (isset($search_type) && $search_type == $key) ? "selected" : ""; ?>>
                                                <?php echo $search ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group" id='date_result'></div>

                                <div class="form-group">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm">
                                        <i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?>
                                    </button>
                                </div>
                            </form>

                            <!-- Boutons d'exportation -->
                            <div class="btn-group" style="margin-left: 10px;">
                                <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-download"></i> Exporter
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="#" onclick="exportToPDF()">
                                            <i class="fa fa-file-pdf-o"></i> Export PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="exportToExcel()">
                                            <i class="fa fa-file-excel-o"></i> Export Excel
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Corps du tableau -->
                    <div class="box-body table-responsive">
                        <div class="download_label">
                            <div class="col-md-4 col-xs-2 col-sm-6">
                                <img style="width: 150px; height: 70px !important;" src="<?= base_url() . "/uploads/school_content/admin_logo/" . $sch_setting->admin_logo ?>" alt="Logo" />
                            </div>
                            <br/><br/><br/><br/>
                            <?php
                            echo "Livre de paie <br/><br/>";
                            echo "Période: ";
                            $this->customlib->get_postmessage();
                            ?>
                        </div>

                        <!-- Tableau du livre de paie -->
                        <table class="table table-striped table-bordered table-hover example" id="livre-paie-table">
                            <thead>
                            <tr>
                                <th class="text text-left text-primary">Matricule</th>
                                <th class="text text-left text-primary">Nom et prénom</th>
                                <th class="text text-left text-primary">Total Brute</th>
                                <th class="text text-left text-primary">Total Social</th>
                                <th class="text text-left text-primary">Total Fiscal</th>
                                <th class="text text-left text-primary">Salaire de base</th>
                                <th class="text text-left text-primary">Cnps</th>
                                <th class="text text-left text-primary">Cmu</th>
                                <th class="text text-left text-primary">ITS</th>
                                <th class="text text-left text-primary">Total retenu</th>
                                <th class="text text-left text-primary">Salaire net</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($payrollList as $value) {

                                // Total brut recalculé (sans prendre gross_salary depuis la base)
                                $total_brute = $value['categorie_salaire'] + $value['sursalaire'] + $value['prime_anc'] +
                                    $value['prime_trans'] + $value['forfait_hs'] + $value['prime_resp'] +
                                    $value['prime_rend'] + $value['prime_risque'] + $value['prime_assi'] +
                                    $value['prime_grati'] + $value['conge'] + $value['autre_reve'];

                                // Total social (CNPS/CMU) = total brut - prime de transport
                                $total_social = $total_brute - $value['prime_trans'];
                                $total_revenu = $its + $cmu + $cnps;

                                // CNPS avec plafond retraite
                                // =============================
                                $plafond_cnps = 3375000; // 45 * 75 000

                                if ($total_social > $plafond_cnps) {
                                    $cnps_retraite_base = $plafond_cnps;
                                } else {
                                    $cnps_retraite_base = $total_social;
                                }

                                $etraite = $cnps_retraite_base;

                                // Cotisation CNPS sur base plafonnée
                                $cnps = $etraite * 0.063;  // 8%


                                // CNPS et CMU recalculés
                                //$cnps = $total_social * 0.08;  // 8%
                                $cmu  = 500;  // 5%


                                // Si epf_no est vide ou nul, on considère au moins 1 personne
                                $epf_no = !empty($result["epf_no"]) ? $result["epf_no"] : 1;

                                // Calcul du CMU total
                                $cmu_total = $epf_no * $cmu;

                                // =============================
                                // CALCUL DU TOTAL FISCAL
                                // =============================
                                $total_pourcentage = $total_brute * 0.1;
                                $primet = 30000;

                                // Prime transport
                                if ($value["prime_trans"] > $primet) {
                                    $trans = $value["prime_trans"] - $primet;
                                } else {
                                    $trans = 0;
                                }
                                $final_trans = $trans;

                                // Prime ancienneté
                                $prime_anc = isset($value["prime_anc"]) ? floatval($value["prime_anc"]) : 0;
                                if ($prime_anc > $total_pourcentage) {
                                    $ancien = $prime_anc - $total_pourcentage;
                                } else {
                                    $ancien = 0;
                                }
                                $final_anc = $ancien;

                                // Prime rendement
                                if ($value["prime_rend"] > $total_pourcentage) {
                                    $rends = $value["prime_rend"] - $total_pourcentage;
                                } else {
                                    $rends = 0;
                                }
                                $final_rend = $rends;

                                // Prime responsabilité
                                if ($value["prime_resp"] > $total_pourcentage) {
                                    $respo = $value["prime_resp"] - $total_pourcentage;
                                } else {
                                    $respo = 0;
                                }
                                $final_respo = $respo;

                                // Prime risque
                                if ($value["prime_risque"] > $total_pourcentage) {
                                    $risq = $value["prime_risque"] - $total_pourcentage;
                                } else {
                                    $risq = 0;
                                }
                                $final_risq = $risq;

                                // Autres revenus
                                if ($value["autre_reve"] > $total_pourcentage) {
                                    $autre = $value["autre_reve"] - $total_pourcentage;
                                } else {
                                    $autre = 0;
                                }
                                $final_autres = $autre;

                                // Prime assiduité
                                if ($value["prime_assi"] > $total_pourcentage) {
                                    $assi = $value["prime_assi"] - $total_pourcentage;
                                } else {
                                    $assi = 0;
                                }
                                $final_assi = $assi;

                                // Forfait heures sup
                                if ($value["forfait_hs"] > $total_pourcentage) {
                                    $forfait = $value["forfait_hs"] - $total_pourcentage;
                                } else {
                                    $forfait = 0;
                                }
                                $final_forfait = $forfait;

                                // Sursalaire
                                if ($value["sursalaire"] > $total_pourcentage) {
                                    $sura = $value["sursalaire"] - $total_pourcentage;
                                } else {
                                    $sura = 0;
                                }
                                $final_sura = $sura;

                                // Total fiscal (base imposable)
                                $total_fiscal = $value["categorie_salaire"] + $final_trans + $prime_anc + $final_rend +
                                    $final_risq + $final_respo + $final_assi + $value["sursalaire"] + $final_autres;

                                // =============================
                                // FIN CALCUL FISCAL

                                // =============================
                                // ITS avec barème progressif
                                // =============================
                                $impot = 0;
                                $categorie_salaire = $total_fiscal;

                                if ($categorie_salaire > 8000000) {
                                    $impot += ($categorie_salaire - 8000000) * 0.32;
                                    $categorie_salaire = 8000000;
                                }

                                if ($categorie_salaire > 2400000) {
                                    $impot += ($categorie_salaire - 2400000) * 0.28;
                                    $categorie_salaire = 2400000;
                                }

                                if ($categorie_salaire > 800000) {
                                    $impot += ($categorie_salaire - 800000) * 0.24;
                                    $categorie_salaire = 800000;
                                }

                                if ($categorie_salaire > 240000) {
                                    $impot += ($categorie_salaire - 240000) * 0.21;
                                    $categorie_salaire = 240000;
                                }

                                if ($categorie_salaire > 75000) {
                                    $impot += ($categorie_salaire - 75000) * 0.16;
                                    $categorie_salaire = 75000;
                                }

                                // Réduction selon nombre de parts
                                $coef = isset($value["part_igr"]) ? $value["part_igr"] : 1;
                                $reduction = 0;

                                switch (true) {
                                    case ($coef >= 5):
                                        $reduction = 44000;
                                        break;
                                    case ($coef == 4.5):
                                        $reduction = 38500;
                                        break;
                                    case ($coef == 4):
                                        $reduction = 33000;
                                        break;
                                    case ($coef == 3.5):
                                        $reduction = 27500;
                                        break;
                                    case ($coef == 3):
                                        $reduction = 22000;
                                        break;
                                    case ($coef == 2.5):
                                        $reduction = 16500;
                                        break;
                                    case ($coef == 2):
                                        $reduction = 11000;
                                        break;
                                    case ($coef == 1.5):
                                        $reduction = 5500;
                                        break;
                                    case ($coef == 1):
                                    default:
                                        $reduction = 0;
                                        break;
                                }

                                // ITS net
                                $impot_net = max($impot - $reduction, 0);
                                $its = round($impot_net, 2);

                                // Ici tu peux garder ton calcul ITS existant si nécessaire
                               // $its = $value['its']; // ou recalculer comme plus haut

                                // Salaire net
                                $net_salary = $total_brute - $cnps - $cmu - $its ;


                                // Totaux généraux
                                $totals['brute']  += $total_brute;
                                $totals['social'] += $total_social;
                                $totals['fiscal'] += $total_fiscal;
                                $totals['base']   += $value['categorie_salaire'];
                                $totals['cnps']   += $cnps;
                                $totals['cmu']    += $cmu_total;
                                $totals['its']    += $its;
                                $totals['revenu'] += $cnps + $cmu + $its; // revenu imposable
                                $totals['net']    += $net_salary;
                                ?>
                                <tr>
                                    <td><?php echo $value['employee_id']; ?></td>
                                    <td><?php echo $value['name'] . " " . $value['surname']; ?></td>
                                    <td><?php echo number_format($total_brute, 2, ',', ' '); ?></td>
                                    <td><?php echo number_format($total_social, 2, ',', ' '); ?></td>
                                    <td><?php echo number_format($total_fiscal, 2, ',', ' '); ?></td>

                                    <td><?php echo number_format($value['categorie_salaire'], 2, ',', ' '); ?></td>
                                    <td><?php echo number_format($cnps, 2, ',', ' '); ?></td>
                                    <td><?php echo number_format($cmu, 2, ',', ' '); ?></td>
                                    <td><?php echo number_format($its, 2, ',', ' '); ?></td>
                                    <td><?php echo number_format($totals['revenu'], 2, ',', ' '); ?></td>
                                    <td><?php echo number_format($net_salary, 2, ',', ' '); ?></td>
                                </tr>
                                <?php
                            }
                            ?>

                            <!-- Ligne des totaux -->
                            <tr class="box box-solid total-bg">
                                <td colspan="2" class="text text-left text-primary"><strong>GRAND TOTAL</strong></td>
                                <td><strong><?php echo number_format($totals['brute'], 2, ',', ' '); ?></strong></td>
                                <td><strong><?php echo number_format($totals['social'], 2, ',', ' '); ?></strong></td>
                                <td><strong><?php echo number_format($totals['fiscal'], 2, ',', ' '); ?></strong></td>

                                <td><strong><?php echo number_format($totals['base'], 2, ',', ' '); ?></strong></td>
                                <td><strong><?php echo number_format($totals['cnps'], 2, ',', ' '); ?></strong></td>
                                <td><strong><?php echo number_format($totals['cmu'], 2, ',', ' '); ?></strong></td>
                                <td><strong><?php echo number_format($totals['its'], 2, ',', ' '); ?></strong></td>
                                <td><strong><?php echo number_format($totals['revenu'], 2, ',', ' '); ?></strong></td>
                                <td><strong><?php echo number_format($totals['net'], 2, ',', ' '); ?></strong></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Scripts pour l'exportation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    <?php if ($search_type == 'period') { ?>
    $(document).ready(function () {
        showdate('period');
    });
    <?php } ?>

    // Fonction d'exportation PDF
    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Titre du document
        doc.setFontSize(16);
        doc.text('LIVRE DE PAIE', 105, 15, { align: 'center' });

        // Période
        doc.setFontSize(10);
        doc.text('Période: <?php echo $this->customlib->get_postmessage(); ?>', 14, 25);

        // Date d'exportation
        const exportDate = new Date().toLocaleDateString('fr-FR');
        doc.text('Exporté le: ' + exportDate, 14, 32);

        // Préparation des données du tableau
        const table = document.getElementById('livre-paie-table');
        const headers = [];
        const rows = [];

        // Récupération des en-têtes
        table.querySelectorAll('thead th').forEach(th => {
            headers.push(th.textContent.trim());
        });

        // Récupération des données
        table.querySelectorAll('tbody tr').forEach(tr => {
            const row = [];
            tr.querySelectorAll('td').forEach(td => {
                row.push(td.textContent.trim());
            });
            if (row.length > 0) {
                rows.push(row);
            }
        });

        // Génération du tableau PDF
        doc.autoTable({
            head: [headers],
            body: rows,
            startY: 40,
            styles: { fontSize: 8 },
            headStyles: { fillColor: [66, 139, 202] }
        });

        // Sauvegarde du PDF
        doc.save('livre_paie_<?php echo date('Y-m-d'); ?>.pdf');
    }

    // Fonction d'exportation Excel
    function exportToExcel() {
        // Préparation des données
        const data = [];
        const headers = [];

        // Récupération des en-têtes
        document.querySelectorAll('#livre-paie-table thead th').forEach(th => {
            headers.push(th.textContent.trim());
        });
        data.push(headers);

        // Récupération des données
        document.querySelectorAll('#livre-paie-table tbody tr').forEach(tr => {
            const row = [];
            tr.querySelectorAll('td').forEach(td => {
                row.push(td.textContent.trim());
            });
            if (row.length > 0) {
                data.push(row);
            }
        });

        // Création du workbook
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Livre de Paie');

        // Style pour les en-têtes
        if (!ws['!cols']) ws['!cols'] = [];
        headers.forEach((_, i) => {
            ws['!cols'][i] = { width: 15 };
        });

        // Sauvegarde du fichier Excel
        XLSX.writeFile(wb, 'livre_paie_<?php echo date('Y-m-d'); ?>.xlsx');
    }

    // Alternative simple pour Excel (méthode tableau HTML)
    function exportToExcelSimple() {
        const table = document.getElementById('livre-paie-table');
        const html = table.outerHTML;

        // Création d'un blob et téléchargement
        const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'livre_paie_<?php echo date('Y-m-d'); ?>.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
</script>

<style>
    .btn-group {
        margin-left: 10px;
    }
    .dropdown-menu li a {
        cursor: pointer;
    }
    .download_label {
        display: none; /* Masquer pour l'export */
    }
    @media print {
        .box-header, .btn-group {
            display: none !important;
        }
    }
</style>