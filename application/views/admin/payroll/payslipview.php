<?php

$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

// Conversion en objet DateTime
$payment_date = new DateTime($result["payment_date"]);

// Premier jour du mois
$date_from = $payment_date->format("Y-m-01");

// Dernier jour du mois
$date_to = $payment_date->format("Y-m-t");

$date = new \DateTime($result['month']);
$formatter = new \IntlDateFormatter(
    'fr_FR',
    \IntlDateFormatter::LONG,
    \IntlDateFormatter::NONE,
    'Europe/Paris',
    \IntlDateFormatter::GREGORIAN,
    'MMMM'
);

// Date d'embauche
$date_embauche = new DateTime($result['date_of_joining']);
$today = new DateTime();

// Calcul de l'ancienneté en années
$anciennete = $date_embauche->diff($today)->y;

// Définition du taux de prime en fonction de l'ancienneté
$taux_prime = 0;

if ($anciennete >= 3 && $anciennete <= 5) {
    $taux_prime = 0.05; // 5%
} elseif ($anciennete >= 6 && $anciennete <= 10) {
    $taux_prime = 0.10; // 10%
} elseif ($anciennete >= 11 && $anciennete <= 15) {
    $taux_prime = 0.15; // 15%
} elseif ($anciennete > 15) {
    $taux_prime = 0.20; // 20%
}

// Calcul de la prime d'ancienneté
$prime_anciennete = $result['categorie_salaire'] * $taux_prime;
$total_brute = $result["categorie_salaire"] + $result["sursalaire"] + $prime_anciennete + $result["prime_trans"] + $result["autre_reve"] + $result["forfait_hs"] + $result["prime_resp"] + $result["prime_rend"] + $result["prime_risque"] + $result["prime_assi"] + $result["prime_grati"] + $result["conge"];

$total_pourcentage = $total_brute * 0.1;
$primet = 30000;
$primeresp = 0;
$primerisq = 0;

//prime transport
if ($result["prime_trans"] > $primet) {
    $trans = $result["prime_trans"] - $primet;
} else {
    $trans = 0;
}
$final_trans = $trans;

$prime_anc = isset($result["prime_anc"]) ? floatval($result["prime_anc"]) : 0;

$total_pourcentage = isset($total_pourcentage) ? floatval($total_pourcentage) : 0;

if ($prime_anciennete > $total_pourcentage) {
    $ancien = $prime_anciennete - $total_pourcentage;
} else {
    $ancien = 0;
}

$final_anc = $ancien;

//prime rendement
if ($result["prime_rend"] > $total_pourcentage) {
    $rends = $result["prime_rend"] - $total_pourcentage;
} else {
    $rends = 0;
}
$final_rend = $rends;

//prime responsabilité
if ($result["prime_resp"] > $total_pourcentage) {
    $respo = $result["prime_resp"] - $total_pourcentage;
} else {
    $respo = 0;
}
$final_respo = $respo;

//prime risque
if ($result["prime_risque"] > $total_pourcentage) {
    $risq = $result["prime_risque"] - $total_pourcentage;
} else {
    $risq = 0;
}
$final_risq = $risq;

//autre revenu
if ($result["autre_reve"] > $total_pourcentage) {
    $autre = $result["autre_reve"] - $total_pourcentage;
} else {
    $autre = 0;
}
$final_autres = $autre;

//prime assiduité
if ($result["prime_assi"] > $total_pourcentage) {
    $assi = $result["prime_assi"] - $total_pourcentage;
} else {
    $assi = 0;
}
$final_assi = $assi;

//forfait heures sup
if ($result["forfait_hs"] > $total_pourcentage) {
    $forfait = $result["forfait_hs"] - $total_pourcentage;
} else {
    $forfait = 0;
}
$final_forfait = $forfait;

//sursalaire
if ($result["sursalaire"] > $total_pourcentage) {
    $sura = $result["sursalaire"] - $total_pourcentage;
} else {
    $sura = 0;
}
$final_sura = $sura;

//total fiscal
$total_fiscal = $result["categorie_salaire"] + $final_trans + $final_anc + $final_rend + $final_risq + $final_respo + $final_assi + $result["sursalaire"] + $final_autres;

//total brute social
$total_social = $total_brute - $result["prime_trans"];

// Plafond CNPS retraite = 45 * 75000
$plafond_cnps = 3375000; // 3 375 000

// Comparer et affecter selon le plafond
if ($total_social < $plafond_cnps) {
    $cnps_retraite_base = $total_social;
} else {
    $cnps_retraite_base = $plafond_cnps;
}
$etraite = $cnps_retraite_base;

// Calcul ITS
$impot = 0;
$categorie_salaire = $total_fiscal;

// Barème progressif
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
$coef = $result["part_igr"];
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

$impot_net = max($impot - $reduction, 0);
$its = round($impot_net, 2);

// Calcul CMU
$cmu_unit = 500;
$epf_no = !empty($result["epf_no"]) ? $result["epf_no"] : 1;
$cmu_total = $epf_no * $cmu_unit;

// Calcul des retenues
$retrai_regime = $etraite * 0.0630;
$tota_retenus = $retrai_regime + $impot_net + $cmu_total;
$salaire_net = $total_brute - $tota_retenus;

// Calcul des charges patronales
$its_patronal = $total_fiscal * 0.012;
$retrait = $etraite * 0.077;
$travail = $result["categorie_salaire"] * 0.04;
$famille = $result["categorie_salaire"] * 0.0575;
$taxe = $total_fiscal * 0.004;
$tax = $total_fiscal * 0.006;
$tota_retenues = $its_patronal + $retrait + $cmu_total + $travail + $famille + $taxe + $tax;

// Fonction pour vérifier si une ligne doit être affichée
function shouldDisplayLine($value) {
    return !empty($value) && $value > 0;
}
?>

<style type="text/css">
    @media print {
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-size: 12px;
            line-height: 1.3;
        }

        .payslip_address,
        .header-section,
        .employee-info,
        .company-info,
        .row {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .print-page {
            page-break-after: avoid;
            page-break-before: avoid;
        }

        .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6,
        .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
            float: left;
        }
        .col-sm-12 { width: 100%; }
        .col-sm-11 { width: 91.66666667%; }
        .col-sm-10 { width: 83.33333333%; }
        .col-sm-9 { width: 75%; }
        .col-sm-8 { width: 66.66666667%; }
        .col-sm-7 { width: 58.33333333%; }
        .col-sm-6 { width: 50%; }
        .col-sm-5 { width: 41.66666667%; }
        .col-sm-4 { width: 33.33333333%; }
        .col-sm-3 { width: 25%; }
        .col-sm-2 { width: 16.66666667%; }
        .col-sm-1 { width: 8.33333333%; }

        .visible-xs { display: none !important; }
        .hidden-xs { display: block !important; }
        table.hidden-xs { display: table; }
        tr.hidden-xs { display: table-row !important; }
        th.hidden-xs, td.hidden-xs { display: table-cell !important; }
        .hidden-xs.hidden-print { display: none !important; }
        .hidden-sm { display: none !important; }
        .visible-sm { display: block !important; }
        table.visible-sm { display: table; }
        tr.visible-sm { display: table-row !important; }
        th.visible-sm, td.visible-sm { display: table-cell !important; }
    }

    .payslip-table {
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0;
        border: none;
    }

    .payslip-table th, .payslip-table td {
        border: none;
        padding: 4px 6px;
        text-align: left;
        font-size: 11px;
        line-height: 1.2;
    }

    .payslip-table thead th {
        background-color: #e9ecef;
        font-weight: bold;
        vertical-align: middle;
        padding: 6px;
    }

    .payslip-table tbody tr {
        border-bottom: 2px solid transparent;
    }

    .payslip-table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .total-row {
        border-top: 2px solid #ddd;
        border-bottom: 2px solid #ddd;
        margin-top: 4px;
        margin-bottom: 4px;
    }
</style>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $this->lang->line('payslip'); ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 10px;
            background-color: #fff;
        }

        .payslip-container {
            border: 2px solid #000;
            border-radius: 8px;
            padding: 15px;
            margin: 0 auto;
            max-width: 1000px;
            background-color: #fff;
        }

        .header-section {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }

        .company-logo {
            width: 104px;
            height: 115px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .company-info {
            border: 1px solid #000;
            border-radius: 15px;
            padding: 15px;
            background-color: #f8f9fa;
        }

        .main-title {
            text-align: center;
            margin: -11px 0;
            font-weight: bold;
            text-transform: uppercase;
            padding: -19px;
            background-color: #f0f0f0;
            border-radius: 5px;
        }

        .section-title {
            background-color: #e9ecef;
            padding: 8px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #ddd;
            margin: 20px 0 10px;
            border-radius: 4px;
        }

        .employee-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .employee-info td {
            padding: 5px;
            vertical-align: top;
        }

        .employee-info td:first-child {
            font-weight: bold;
            width: 15%;
        }

        .payslip-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .payslip-table th, .payslip-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            font-size: 11px;
        }

        .payslip-table thead th {
            background-color: #e9ecef;
            font-weight: bold;
            vertical-align: middle;
        }

        .text-right {
            text-align: right;
        }

        .amount {
            font-family: 'Courier New', monospace;
        }

        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .signature-area {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px dashed #000;
        }

        .footer-note {
            text-align: center;
            font-style: italic;
            margin-top: 15px;
            padding: 10px;
            font-size: 11px;
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
            }

            .payslip-container {
                border: none;
                padding: 0;
            }
        }

        .sub-header {
            font-weight: bold;
            background-color: #e9ecef;
        }

        .net-payer {
            background-color: #d4edda !important;
            font-weight: bold;
            font-size: 13px;
        }
    </style>
</head>
<body>
<div class="container-fluid">

    <div class="row payslip_print" id="payslip_print">
        <div class="col-md-12">
            <div class="card card-body">

                <div class="row">
                    <div class="col-md-12" style="border: 1px solid black; padding: 6px; border-radius: 0px;">

                        <div class="row">

                            <!-- Bloc entreprise -->
                            <div class="col-md-4 col-sm-6 col-xs-12 payslip_address"
                                 style="border: 0px solid black; padding: 1px; border-radius: 0px; margin-bottom: 10px;">

                                <div class="text-center">
                                    <img src="<?= base_url() . "/uploads/school_content/admin_logo/" . $sch_setting->admin_logo ?>"
                                         alt="Logo entreprise"
                                         style="width: 100px; height: 100px; object-fit: contain; margin-bottom: 10px;" />
                                    <div style="flex: 3;">
                                        <p><strong>Téléphone :</strong> <?= $sch_setting->phone ?></p>
                                        <p><strong>Adresse   :</strong> <?= strtoupper($sch_setting->address) ?></p>
                                        <p><strong>Email     :</strong> <?= $sch_setting->email ?></p>
                                    </div>
                                </div>

                            </div>

                            <!-- Bloc employé -->
                            <div class="col-md-8 col-sm-6 col-xs-12 payslip_address"
                                 style="border: 1px solid black; padding: -19px; border-radius: 0px; margin-bottom: 10px;margin: -4px">
                                <div class="section-title"> BULLETIN DE PAIE - <?php echo ucfirst($formatter->format($date)) ?> <?php echo $result["year"] ?></div>
                                <p>
                                    <strong>Matricule :</strong> <?= $result["employee_id"] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <?= $result["surname"] . " " . $result["name"] ?>
                                </p>

                                <p><strong>Statut  :</strong> <?= $result["marital_status"] ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Catégorie salariale :</strong>  <?= $result["categorie_lettre"] ?>
                                    <br><strong>CNPS N° :</strong> <?= $result["cnps_no"] ?>
                                    <br><strong>Mode de paie :</strong> <?= $result["payment_mode"] ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Part IGR :</strong> <?= $result["part_igr"] ?><br>
                                    <strong>Nombre d'enfant :</strong> <?= $result["epf_no"] ?><br>
                                    <strong>Date d'embouche :</strong> <?= $result["date_of_joining"] ?><br>
                                    <strong>Anciennété :</strong> <?= $anciennete ?> ans
                                </p>

                                <p>
                                    <?php if ($sch_setting->staff_designation) { ?>
                                        <strong><?= $this->lang->line('designation'); ?> :</strong> <?= $result["designation"] ?>
                                    <?php } ?>
                                    <?php if ($sch_setting->staff_department) { ?>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?= $this->lang->line('department'); ?> :</strong> <?= $result["department"] ?>
                                    <?php } ?>
                                </p>

                                <p>
                                    <strong>Date :</strong> <?= $payment_date->format("d/m/Y") ?>
                                    &nbsp;&nbsp;&nbsp;
                                    <strong>Période du :</strong> <?= date("d/m/Y", strtotime($date_from)) ?>
                                    au <?= date("d/m/Y", strtotime($date_to)) ?>
                                </p>

                            </div>
                        </div>

                    </div>
                </div>

                <style>
                    .table-condensed>thead>tr>th, .table-condensed>tbody>tr>th, .table-condensed>tfoot>tr>th, .table-condensed>thead>tr>td, .table-condensed>tbody>tr>td, .table-condensed>tfoot>tr>td { padding: 2px 5px; }
                </style>

                <!-- Détails de rémunération -->
                <table class="payslip-table">
                    <thead>
                    <tr class="sub-header">
                        <th rowspan="2">DÉSIGNATION</th>
                        <th rowspan="2">BASE</th>
                        <th colspan="3">PART.SALARIALE</th>
                        <th colspan="2">PART.PATRONALE</th>
                    </tr>
                    <tr class="sub-header">
                        <th>Nbre/taux</th>
                        <th>GAINS</th>
                        <th>RETENUES</th>
                        <th>Nbre/taux</th>
                        <th>RETENUES</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Salaire catégoriel -->
                    <?php if (shouldDisplayLine($result["categorie_salaire"])): ?>
                        <tr>
                            <td>Salaire catégoriel</td>
                            <td class="amount"><?php echo number_format($result["categorie_salaire"], 0, '', '.'); ?></td>
                            <td>30</td>
                            <td class="amount"><?php echo number_format($result["categorie_salaire"], 0, '', '.'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <!-- Sursalaire -->
                    <?php if (shouldDisplayLine($result["sursalaire"])): ?>
                        <tr>
                            <td>Sursalaire</td>
                            <td class="amount"><?php echo number_format($result["sursalaire"], 0, '', '.'); ?></td>
                            <td>30</td>
                            <td class="amount"><?php echo number_format($result["sursalaire"], 0, '', '.'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <!-- Prime d'anciennete -->
                    <?php if (shouldDisplayLine($prime_anciennete)): ?>
                        <tr>
                            <td>Prime d'ancienneté</td>
                            <td class="amount"><?= number_format($prime_anciennete, 0, '', '.') ?></td>
                            <td>1</td>
                            <td class="amount"><?= number_format($prime_anciennete, 0, '', '.') ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <!-- Prime de transport -->
                    <?php if (shouldDisplayLine($result["prime_trans"])): ?>
                        <tr>
                            <td>Prime de transport</td>
                            <td class="amount"><?php echo number_format($result["prime_trans"], 0, '', '.'); ?></td>
                            <td>1</td>
                            <td class="amount"><?php echo number_format($result["prime_trans"], 0, '', '.'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <!-- Forfait heures suppl -->
                    <?php if (shouldDisplayLine($result["forfait_hs"])): ?>
                        <tr>
                            <td>Forfait heures suppl</td>
                            <td class="amount"><?php echo number_format($result["forfait_hs"], 0, '', '.'); ?></td>
                            <td>1</td>
                            <td class="amount"><?php echo number_format($result["forfait_hs"], 0, '', '.'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <!-- Prime de responsabilité -->
                    <?php if (shouldDisplayLine($result["prime_resp"])): ?>
                        <tr>
                            <td>Prime de responsabilité</td>
                            <td class="amount"><?php echo number_format($result["prime_resp"], 0, '', '.'); ?></td>
                            <td>1</td>
                            <td class="amount"><?php echo number_format($result["prime_resp"], 0, '', '.'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <!-- Prime de rendement -->
                    <?php if (shouldDisplayLine($result["prime_rend"])): ?>
                        <tr>
                            <td>Prime de rendement</td>
                            <td class="amount"><?php echo number_format($result["prime_rend"], 0, '', '.'); ?></td>
                            <td>1</td>
                            <td class="amount"><?php echo number_format($result["prime_rend"], 0, '', '.'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <!-- Prime de fonction -->
                    <?php if (shouldDisplayLine($result["autre_reve"])): ?>
                        <tr>
                            <td>Prime de fonction</td>
                            <td class="amount"><?php echo number_format($result["autre_reve"], 0, '', '.'); ?></td>
                            <td>1</td>
                            <td class="amount"><?php echo number_format($result["autre_reve"], 0, '', '.'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <!-- Prime de risque -->
                    <?php if (shouldDisplayLine($final_risq)): ?>
                        <tr>
                            <td>Prime de risque</td>
                            <td class="amount"><?php echo number_format($result["prime_risque"], 0, '', '.'); ?></td>
                            <td>1</td>
                            <td class="amount"><?php echo number_format($final_risq, 0, '', '.'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <!-- Prime d'assiduité -->
                    <?php if (shouldDisplayLine($result["prime_assi"])): ?>
                        <tr>
                            <td>Prime d'assiduité</td>
                            <td class="amount"><?php echo number_format($result["prime_assi"], 0, '', '.'); ?></td>
                            <td>1</td>
                            <td class="amount"><?php echo number_format($result["prime_assi"], 0, '', '.'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <!-- Prime Gratification -->
                    <?php if (shouldDisplayLine($result["prime_grati"])): ?>
                        <tr>
                            <td>Prime Gratification</td>
                            <td class="amount"><?php echo number_format($result["prime_grati"], 0, '', '.'); ?></td>
                            <td>1</td>
                            <td class="amount"><?php echo number_format($result["prime_grati"], 0, '', '.'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <!-- Total Brut -->
                    <?php if (shouldDisplayLine($total_brute)): ?>
                        <tr class="total-row">
                            <td><strong>Total Brut</strong></td>
                            <td></td>
                            <td></td>
                            <td class="amount"><strong><?php echo number_format($total_brute, 0, '', '.'); ?></strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <!-- Total Brut Fiscal -->
                    <?php if (shouldDisplayLine($total_fiscal)): ?>
                        <tr class="total-row">
                            <td><strong>Total Brut Fiscal</strong></td>
                            <td></td>
                            <td></td>
                            <td class="amount"><strong><?php echo number_format($total_fiscal, 0, '', '.'); ?></strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <!-- Total Brute Social -->
                    <?php if (shouldDisplayLine($total_social)): ?>
                        <tr class="total-row">
                            <td><strong>Total Brute Social</strong></td>
                            <td></td>
                            <td></td>
                            <td class="amount"><strong><?php echo number_format($total_social, 0, '', '.'); ?></strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    <!-- ITS -->
                    <?php if (shouldDisplayLine($its) || shouldDisplayLine($its_patronal)): ?>
                        <tr class="total-row">
                            <td><strong>ITS</strong></td>
                            <td class="amount"><?php echo number_format($total_fiscal, 0, '', '.'); ?></td>
                            <td></td>
                            <td></td>
                            <td class="amount"><strong><?php echo $its; ?></strong></td>
                            <td>1,2</td>
                            <td><?php echo number_format($its_patronal, 0, '', '.'); ?></td>
                        </tr>
                    <?php endif; ?>

                    <!-- CMU -->
                    <?php if (shouldDisplayLine($cmu_total)): ?>
                        <tr class="total-row">
                            <td><strong>CMU</strong></td>
                            <td class="amount">-</td>
                            <td></td>
                            <td></td>
                            <td class="amount"><?php echo number_format($cmu_total, 0, '', '.'); ?></td>
                            <td></td>
                            <td class="amount"><?php echo number_format($cmu_total, 0, '', '.'); ?></td>
                        </tr>
                    <?php endif; ?>

                    <!-- CNPS, Régime de Retraite -->
                    <?php if (shouldDisplayLine($etraite) || shouldDisplayLine($retrai_regime) || shouldDisplayLine($retrait)): ?>
                        <tr class="total-row">
                            <td><strong>CNPS, Régime de Retraite</strong></td>
                            <td class="amount"><?php echo number_format($etraite, 0, '', '.'); ?></td>
                            <td>6,30</td>
                            <td></td>
                            <td class="amount"><?php echo number_format($retrai_regime, 0, '', '.'); ?></td>
                            <td>7,70</td>
                            <td class="amount"><?php echo number_format($retrait, 0, '', '.'); ?></td>
                        </tr>
                    <?php endif; ?>

                    <!-- CNPS, Accident Travail -->
                    <?php if (shouldDisplayLine($result["categorie_salaire"]) || shouldDisplayLine($travail)): ?>
                        <tr class="total-row">
                            <td><strong>CNPS, Accident Travail</strong></td>
                            <td class="amount"><?php echo number_format($result["categorie_salaire"], 0, '', '.'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>3,00</td>
                            <td class="amount"><?php echo number_format($travail, 0, '', '.'); ?></td>
                        </tr>
                    <?php endif; ?>

                    <!-- CNPS, Prest. Famil -->
                    <?php if (shouldDisplayLine($result["categorie_salaire"]) || shouldDisplayLine($famille)): ?>
                        <tr class="total-row">
                            <td><strong>CNPS, Prest. Famil</strong></td>
                            <td class="amount"><?php echo number_format($result["categorie_salaire"], 0, '', '.'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>5,75</td>
                            <td class="amount"><?php echo number_format($famille, 0, '', '.'); ?></td>
                        </tr>
                    <?php endif; ?>

                    <!-- FDFP, Taxe Apprentissage -->
                    <?php if (shouldDisplayLine($total_fiscal) || shouldDisplayLine($taxe)): ?>
                        <tr class="total-row">
                            <td><strong>FDFP, Taxe Apprentissage</strong></td>
                            <td class="amount"><?php echo number_format($total_fiscal, 0, '', '.'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>0,04</td>
                            <td class="amount"><?php echo number_format($taxe, 0, '', '.'); ?></td>
                        </tr>
                    <?php endif; ?>

                    <!-- FDFP, Form. Prof. Continue -->
                    <?php if (shouldDisplayLine($total_fiscal) || shouldDisplayLine($tax)): ?>
                        <tr class="total-row">
                            <td><strong>FDFP, Form. Prof. Continue</strong></td>
                            <td class="amount"><?php echo number_format($total_fiscal, 0, '', '.'); ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>1,20</td>
                            <td><?php echo number_format($tax, 0, '', '.'); ?></td>
                        </tr>
                    <?php endif; ?>

                    <!-- Total des retenues -->
                    <?php if (shouldDisplayLine($tota_retenus) || shouldDisplayLine($tota_retenues)): ?>
                        <tr class="total-row">
                            <td><strong>Total des retenues</strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="amount"><strong><?php echo number_format($tota_retenus, 0, '', '.'); ?></strong></td>
                            <td></td>
                            <td class="amount"><strong><?php echo number_format($tota_retenues, 0, '', '.'); ?></strong></td>
                        </tr>
                    <?php endif; ?>

                    <!-- Net à payer -->
                    <?php if (shouldDisplayLine($salaire_net)): ?>
                        <tr class="net-payer">
                            <td colspan="3"><strong>NET À PAYER</strong></td>
                            <td colspan="2" class="amount"><strong><?php echo number_format($salaire_net, 0, '', '.'); ?> <?php echo $currency_symbol; ?></strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>

                <!-- Zone de signature -->
                <div class="signature-area">
                    <div style="display: flex; justify-content: space-between;">
                        <div style="text-align: center; width: 45%;">
                            <p>_____________________________________</p>
                            <p>Signature employé</p>
                        </div>
                        <div style="text-align: center; width: 45%;">
                            <p>_____________________________________</p>
                            <p>Signature employeur</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</body>
</html>