<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

// Fonction pour vérifier si une ligne doit être affichée
function shouldDisplayLine($value) {
    return !empty($value) && floatval($value) > 0;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Bulletins de paie multiples</title>
    <style>

        /* RESET */
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        /* IMPRESSION */
        @media print{

            @page{
                size:A4;
                margin:1cm 0.8cm;
            }

            body{
                font-family:Arial, Helvetica, sans-serif;
                font-size:8.5pt;
                color:#000;
                background:#fff;
                width:100%;
            }

            .page-break{
                page-break-after:always;
            }

            .payslip-container{
                width:100%;
                margin:0;
                padding:0;
                page-break-inside:avoid;
            }

        }

        /* BODY */
        body{
            font-family:Arial, Helvetica, sans-serif;
            font-size:8.5pt;
            line-height:1.3;
            margin:10px;
            background:#fff;
        }

        /* CONTAINER */
        .payslip-container{
            width:100%;
            max-width:100%;
            margin:0 auto 20px;
            border:1.5px solid #000;
            padding:6px;
        }

        /* HEADER */
        .payslip-header{
            width:100%;
            border-collapse:collapse;
            margin-bottom:8px;
        }

        .payslip-header td{
            vertical-align:top;
            padding:3px;
        }

        /* LOGO */
        .logo-cell{
            width:18%;
        }

        .logo-img{
            width:70px;
            height:70px;
            object-fit:contain;
            margin-bottom:3px;
        }

        /* COMPANY INFO */
        .company-info{
            font-size:7.5pt;
            line-height:1.2;
        }

        .company-info p{
            margin:1px 0;
        }

        /* EMPLOYEE INFO */
        .employee-cell{
            width:82%;
            border:1.5px solid #000;
            padding:5px !important;
        }

        /* TITLE */
        .payslip-title{
            background:#e9ecef;
            font-weight:bold;
            font-size:10pt;
            text-align:center;
            padding:3px;
            margin-bottom:4px;
            border:1px solid #000;
            letter-spacing:0.5px;
        }

        /* EMPLOYEE TABLE */
        .employee-info-table{
            width:100%;
            border-collapse:collapse;
        }

        .employee-info-table td{
            padding:2px 2px;
            font-size:7.5pt;
        }

        .employee-info-table .label{
            font-weight:bold;
            width:14%;
            white-space:nowrap;
        }

        .employee-info-table .value{
            width:36%;
        }

        /* SEPARATOR */
        .separator{
            border-top:1px solid #000;
            margin:6px 0;
        }

        /* TABLEAU SALAIRE */
        .salary-table{
            width:100%;
            border-collapse:collapse;
            margin:6px 0;
            font-size:8pt;
            table-layout:fixed;
            border:1.5px solid #000;
        }

        .salary-table th,
        .salary-table td{
            border:1px solid #000;
            padding:3px 4px;
            vertical-align:middle;
            word-break:break-word;
        }

        .salary-table th{
            background:#e9ecef;
            font-weight:bold;
            text-align:center;
        }

        .salary-table td.amount{
            text-align:right;
            font-family:"Courier New", monospace;
        }

        /* LARGEURS CORRIGÉES (100%) */

        .salary-table th:nth-child(1),
        .salary-table td:nth-child(1){
            width:24%;
        }

        .salary-table th:nth-child(2),
        .salary-table td:nth-child(2){
            width:11%;
        }

        .salary-table th:nth-child(3),
        .salary-table td:nth-child(3){
            width:8%;
        }

        .salary-table th:nth-child(4),
        .salary-table td:nth-child(4){
            width:13%;
        }

        .salary-table th:nth-child(5),
        .salary-table td:nth-child(5){
            width:13%;
        }

        .salary-table th:nth-child(6),
        .salary-table td:nth-child(6){
            width:8%;
        }

        .salary-table th:nth-child(7),
        .salary-table td:nth-child(7){
            width:23%;
        }

        /* FORCER LA BORDURE DROITE */

        .salary-table th:last-child,
        .salary-table td:last-child{
            border-right:1px solid #000;
        }

        /* TOTAL */
        .total-row td{
            font-weight:bold;
            background:#f2f2f2;
        }

        /* NET */
        .net-row td{
            font-weight:bold;
            background:#d4edda;
            font-size:9pt;
        }

        /* SIGNATURE */
        .signature-section{
            margin-top:14px;
            display:flex;
            justify-content:space-between;
            padding:0 10px;
        }

        .signature-box{
            text-align:center;
            width:45%;
        }

        .signature-line{
            border-top:1px solid #000;
            width:100%;
            margin:4px 0 2px;
        }

        .signature-text{
            font-style:italic;
            font-size:7.5pt;
        }

        /* UTILS */
        .text-bold{
            font-weight:bold;
        }

        .text-center{
            text-align:center;
        }

        .text-right{
            text-align:right;
        }

        .no-wrap{
            white-space:nowrap;
        }

    </style>
</head>
<body>
<?php
$count = 1;
$total = count($payslips);
$sch_setting = $this->setting_model->getSetting();

foreach ($payslips as $payslip):
    $result = $payslip;

    // Vos calculs existants (inchangés)
    $payment_date = new DateTime($result["payment_date"]);
    $date_from = $payment_date->format("Y-m-01");
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

    // Calcul de l'ancienneté
    $date_embauche = new DateTime($result['date_of_joining']);
    $today = new DateTime();
    $anciennete = $date_embauche->diff($today)->y;

    // Calcul de la prime d'ancienneté
    $taux_prime = 0;
    if ($anciennete >= 3 && $anciennete <= 5) {
        $taux_prime = 0.05;
    } elseif ($anciennete >= 6 && $anciennete <= 10) {
        $taux_prime = 0.10;
    } elseif ($anciennete >= 11 && $anciennete <= 15) {
        $taux_prime = 0.15;
    } elseif ($anciennete > 15) {
        $taux_prime = 0.20;
    }

    $prime_anciennete = $result['categorie_salaire'] * $taux_prime;

    // Calculs divers
    $total_brute = $result["categorie_salaire"] + $result["sursalaire"] + $prime_anciennete + $result["prime_trans"] + $result["autre_reve"] + $result["forfait_hs"] + $result["prime_resp"] + $result["prime_rend"] + $result["prime_risque"] + $result["prime_assi"] + $result["prime_grati"] + ($result["conge"] ?? 0);

    $total_pourcentage = $total_brute * 0.1;
    $primet = 30000;

    // Prime transport
    if ($result["prime_trans"] > $primet) {
        $trans = $result["prime_trans"] - $primet;
    } else {
        $trans = 0;
    }
    $final_trans = $trans;

    // Prime ancienneté ajustée
    if ($prime_anciennete > $total_pourcentage) {
        $ancien = $prime_anciennete - $total_pourcentage;
    } else {
        $ancien = 0;
    }
    $final_anc = $ancien;

    // Prime rendement
    if ($result["prime_rend"] > $total_pourcentage) {
        $rends = $result["prime_rend"] - $total_pourcentage;
    } else {
        $rends = 0;
    }
    $final_rend = $rends;

    // Prime responsabilité
    if ($result["prime_resp"] > $total_pourcentage) {
        $respo = $result["prime_resp"] - $total_pourcentage;
    } else {
        $respo = 0;
    }
    $final_respo = $respo;

    // Prime risque
    if ($result["prime_risque"] > $total_pourcentage) {
        $risq = $result["prime_risque"] - $total_pourcentage;
    } else {
        $risq = 0;
    }
    $final_risq = $risq;

    // Autres revenus
    if ($result["autre_reve"] > $total_pourcentage) {
        $autre = $result["autre_reve"] - $total_pourcentage;
    } else {
        $autre = 0;
    }
    $final_autres = $autre;

    // Prime assiduité
    if ($result["prime_assi"] > $total_pourcentage) {
        $assi = $result["prime_assi"] - $total_pourcentage;
    } else {
        $assi = 0;
    }
    $final_assi = $assi;

    // Forfait HS
    if ($result["forfait_hs"] > $total_pourcentage) {
        $forfait = $result["forfait_hs"] - $total_pourcentage;
    } else {
        $forfait = 0;
    }
    $final_forfait = $forfait;

    // Sursalaire
    if ($result["sursalaire"] > $total_pourcentage) {
        $sura = $result["sursalaire"] - $total_pourcentage;
    } else {
        $sura = 0;
    }
    $final_sura = $sura;

    // Total fiscal
    $total_fiscal = $result["categorie_salaire"] + $final_trans + $final_anc + $final_rend + $final_risq + $final_respo + $final_assi + $result["sursalaire"] + $final_autres;

    // Total social
    $total_social = $total_brute - $result["prime_trans"];

    // CNPS Retraite
    $plafond_cnps = 3375000;
    if ($total_social < $plafond_cnps) {
        $cnps_retraite_base = $total_social;
    } else {
        $cnps_retraite_base = $plafond_cnps;
    }
    $etraite = $cnps_retraite_base;

    // Calcul ITS
    $impot = 0;
    $categorie_salaire_impot = $total_fiscal;

    if ($categorie_salaire_impot > 8000000) {
        $impot += ($categorie_salaire_impot - 8000000) * 0.32;
        $categorie_salaire_impot = 8000000;
    }
    if ($categorie_salaire_impot > 2400000) {
        $impot += ($categorie_salaire_impot - 2400000) * 0.28;
        $categorie_salaire_impot = 2400000;
    }
    if ($categorie_salaire_impot > 800000) {
        $impot += ($categorie_salaire_impot - 800000) * 0.24;
        $categorie_salaire_impot = 800000;
    }
    if ($categorie_salaire_impot > 240000) {
        $impot += ($categorie_salaire_impot - 240000) * 0.21;
        $categorie_salaire_impot = 240000;
    }
    if ($categorie_salaire_impot > 75000) {
        $impot += ($categorie_salaire_impot - 75000) * 0.16;
    }

    // Réduction selon nombre de parts
    $coef = $result["part_igr"];
    $reduction = 0;
    switch (true) {
        case ($coef >= 5): $reduction = 44000; break;
        case ($coef == 4.5): $reduction = 38500; break;
        case ($coef == 4): $reduction = 33000; break;
        case ($coef == 3.5): $reduction = 27500; break;
        case ($coef == 3): $reduction = 22000; break;
        case ($coef == 2.5): $reduction = 16500; break;
        case ($coef == 2): $reduction = 11000; break;
        case ($coef == 1.5): $reduction = 5500; break;
        default: $reduction = 0;
    }

    $impot_net = max($impot - $reduction, 0);
    $its = round($impot_net, 2);

    // Calcul CMU
    $cmu_unit = 500;
    $epf_no = !empty($result["epf_no"]) ? $result["epf_no"] : 1;
    $cmu_total = $epf_no * $cmu_unit;

    // Calculs des retenues
    $retrai_regime = $etraite * 0.0630;
    $its_patronal = $total_fiscal * 0.012;
    $retrait = $etraite * 0.077;
    $travail = $result["categorie_salaire"] * 0.04;
    $famille = $result["categorie_salaire"] * 0.0575;
    $taxe = $total_fiscal * 0.004;
    $tax = $total_fiscal * 0.006;

    $tota_retenus = $retrai_regime + $impot_net + $cmu_total;
    $tota_retenues = $its_patronal + $retrait + $cmu_total + $travail + $famille + $taxe + $tax;
    $salaire_net = $total_brute - $tota_retenus;
    ?>

    <div class="payslip-container">
        <!-- EN-TÊTE -->
        <table class="payslip-header">
            <tr>
                <td class="logo-cell">
                    <img src="<?= base_url() . "/uploads/school_content/admin_logo/" . $sch_setting->admin_logo ?>"
                         alt="Logo" class="logo-img">
                    <div class="company-info">
                        <p><span class="text-bold">Tél :</span> <?= $sch_setting->phone ?></p>
                        <p><span class="text-bold">Adr :</span> <?= substr($sch_setting->address, 0, 20) ?></p>
                        <p><span class="text-bold">Email :</span> <?= $sch_setting->email ?></p>
                    </div>
                </td>

                <td class="employee-cell">
                    <div class="payslip-title">
                        BULLETIN DE PAIE - <?php echo strtoupper(ucfirst($formatter->format($date))) ?> <?php echo $result["year"] ?>
                    </div>

                    <table class="employee-info-table">
                        <tr>
                            <td class="label">Matricule :</td>
                            <td class="value"><?= $result["employee_id"] ?></td>
                            <td class="label">Nom & Prénoms :</td>
                            <td class="value"><?= $result["surname"] . " " . $result["name"] ?></td>
                        </tr>
                        <tr>
                            <td class="label">Statut :</td>
                            <td class="value"><?= $result["marital_status"] ?></td>
                            <td class="label">Catégorie :</td>
                            <td class="value"><?= $result["categorie_lettre"] ?></td>
                        </tr>
                        <tr>
                            <td class="label">CNPS N° :</td>
                            <td class="value"><?= $result["cnps_no"] ?></td>
                            <td class="label">Mode paie :</td>
                            <td class="value"><?= $result["payment_mode"] ?></td>
                        </tr>
                        <tr>
                            <td class="label">Part IGR :</td>
                            <td class="value"><?= $result["part_igr"] ?></td>
                            <td class="label">Nbre enfant :</td>
                            <td class="value"><?= $result["epf_no"] ?></td>
                        </tr>
                        <tr>
                            <td class="label">Adresse :</td>
                            <td class="value"><?= substr($result["address"] ?? '', 0, 15) ?></td>
                            <td class="label">Email :</td>
                            <td class="value"><?= $result["email"] ?></td>
                        </tr>
                        <tr>
                            <td class="label">Date embauche :</td>
                            <td class="value"><?= $result["date_of_joining"] ?></td>
                            <td class="label">Ancienneté :</td>
                            <td class="value"><?= $anciennete ?> ans</td>
                        </tr>
                        <tr>
                            <td class="label">Fonction :</td>
                            <td class="value"><?= $result["designation"] ?></td>
                            <td class="label">Services :</td>
                            <td class="value"><?= $result["department"] ?></td>
                        </tr>
                        <tr>
                            <td class="label">Date :</td>
                            <td class="value"><?= $payment_date->format("d/m/Y") ?></td>
                            <td class="label">Période :</td>
                            <td class="value">du <?= date("d/m/Y", strtotime($date_from)) ?> au <?= date("d/m/Y", strtotime($date_to)) ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="separator"></div>

        <!-- TABLEAU PRINCIPAL - TOUTES LES BORDURES VISIBLES -->
        <table class="salary-table">
            <thead>
            <tr>
                <th rowspan="2">DÉSIGNATION</th>
                <th rowspan="2">BASE</th>
                <th colspan="3">PART.SALARIALE</th>
                <th colspan="2">PART.PATRONALE</th>
            </tr>
            <tr>
                <th>Nb/tx</th>
                <th>GAINS</th>
                <th>RETENUES</th>
                <th>Nb/tx</th>
                <th>RETENUES</th>
            </tr>
            </thead>
            <tbody>
            <!-- SALAIRES ET PRINCIPALES PRIMES avec conditions -->
            <?php if (shouldDisplayLine($result["categorie_salaire"])): ?>
                <tr>
                    <td>Salaire catégoriel</td>
                    <td class="amount"><?= number_format($result["categorie_salaire"], 0, '', '.') ?></td>
                    <td class="amount">30</td>
                    <td class="amount"><?= number_format($result["categorie_salaire"], 0, '', '.') ?></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                </tr>
            <?php endif; ?>

            <?php if (shouldDisplayLine($result["sursalaire"])): ?>
                <tr>
                    <td>Sursalaire</td>
                    <td class="amount"><?= number_format($result["sursalaire"], 0, '', '.') ?></td>
                    <td class="amount">30</td>
                    <td class="amount"><?= number_format($result["sursalaire"], 0, '', '.') ?></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                </tr>
            <?php endif; ?>

            <?php if (shouldDisplayLine($prime_anciennete)): ?>
                <tr>
                    <td>Prime d'ancienneté</td>
                    <td class="amount"><?= number_format($prime_anciennete, 0, '', '.') ?></td>
                    <td class="amount">1</td>
                    <td class="amount"><?= number_format($prime_anciennete, 0, '', '.') ?></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                </tr>
            <?php endif; ?>

            <?php if (shouldDisplayLine($result["prime_trans"])): ?>
                <tr>
                    <td>Prime de transport</td>
                    <td class="amount"><?= number_format($result["prime_trans"], 0, '', '.') ?></td>
                    <td class="amount">1</td>
                    <td class="amount"><?= number_format($result["prime_trans"], 0, '', '.') ?></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                </tr>
            <?php endif; ?>

            <?php if (shouldDisplayLine($result["forfait_hs"])): ?>
                <tr>
                    <td>Forfait HS</td>
                    <td class="amount"><?= number_format($result["forfait_hs"], 0, '', '.') ?></td>
                    <td class="amount">1</td>
                    <td class="amount"><?= number_format($result["forfait_hs"], 0, '', '.') ?></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                </tr>
            <?php endif; ?>

            <?php if (shouldDisplayLine($result["prime_resp"])): ?>
                <tr>
                    <td>Prime de responsabilité</td>
                    <td class="amount"><?= number_format($result["prime_resp"], 0, '', '.') ?></td>
                    <td class="amount">1</td>
                    <td class="amount"><?= number_format($result["prime_resp"], 0, '', '.') ?></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                </tr>
            <?php endif; ?>

            <?php if (shouldDisplayLine($result["prime_rend"])): ?>
                <tr>
                    <td>Prime de rendement</td>
                    <td class="amount"><?= number_format($result["prime_rend"], 0, '', '.') ?></td>
                    <td class="amount">1</td>
                    <td class="amount"><?= number_format($result["prime_rend"], 0, '', '.') ?></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                </tr>
            <?php endif; ?>

            <?php if (shouldDisplayLine($result["autre_reve"])): ?>
                <tr>
                    <td>Prime de fonction</td>
                    <td class="amount"><?= number_format($result["autre_reve"], 0, '', '.') ?></td>
                    <td class="amount">1</td>
                    <td class="amount"><?= number_format($result["autre_reve"], 0, '', '.') ?></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                </tr>
            <?php endif; ?>

            <?php if (shouldDisplayLine($result["prime_risque"]) || shouldDisplayLine($final_risq)): ?>
                <tr>
                    <td>Prime de risque</td>
                    <td class="amount"><?= number_format($result["prime_risque"], 0, '', '.') ?></td>
                    <td class="amount">1</td>
                    <td class="amount"><?= number_format($final_risq, 0, '', '.') ?></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                </tr>
            <?php endif; ?>

            <?php if (shouldDisplayLine($result["prime_assi"])): ?>
                <tr>
                    <td>Prime d'assiduité</td>
                    <td class="amount"><?= number_format($result["prime_assi"], 0, '', '.') ?></td>
                    <td class="amount">1</td>
                    <td class="amount"><?= number_format($result["prime_assi"], 0, '', '.') ?></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                </tr>
            <?php endif; ?>

            <?php if (shouldDisplayLine($result["prime_grati"])): ?>
                <tr>
                    <td>Prime gratification</td>
                    <td class="amount"><?= number_format($result["prime_grati"], 0, '', '.') ?></td>
                    <td class="amount">1</td>
                    <td class="amount"><?= number_format($result["prime_grati"], 0, '', '.') ?></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                </tr>
            <?php endif; ?>

            <!-- TOTAL BRUT -->
            <?php if (shouldDisplayLine($total_brute)): ?>
                <tr class="total-row">
                    <td><strong>TOTAL BRUT</strong></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"><strong><?= number_format($total_brute, 0, '', '.') ?></strong></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                </tr>
            <?php endif; ?>

            <!-- RETENUES -->
            <?php if (shouldDisplayLine($its) || shouldDisplayLine($its_patronal)): ?>
                <tr>
                    <td>ITS</td>
                    <td class="amount"><?= number_format($total_fiscal, 0, '', '.') ?></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"><?= number_format($its, 0, '', '.') ?></td>
                    <td class="amount">1,2</td>
                    <td class="amount"><?= number_format($its_patronal, 0, '', '.') ?></td>
                </tr>
            <?php endif; ?>

            <?php if (shouldDisplayLine($cmu_total)): ?>
                <tr>
                    <td>CMU</td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"><?= number_format($cmu_total, 0, '', '.') ?></td>
                    <td class="amount"></td>
                    <td class="amount"><?= number_format($cmu_total, 0, '', '.') ?></td>
                </tr>
            <?php endif; ?>

            <?php if (shouldDisplayLine($etraite) || shouldDisplayLine($retrai_regime) || shouldDisplayLine($retrait)): ?>
                <tr>
                    <td>CNPS Retraite</td>
                    <td class="amount"><?= number_format($etraite, 0, '', '.') ?></td>
                    <td class="amount">6,30</td>
                    <td class="amount"></td>
                    <td class="amount"><?= number_format($retrai_regime, 0, '', '.') ?></td>
                    <td class="amount">7,70</td>
                    <td class="amount"><?= number_format($retrait, 0, '', '.') ?></td>
                </tr>
            <?php endif; ?>

            <?php if (shouldDisplayLine($travail)): ?>
                <tr>
                    <td>CNPS Accident Travail</td>
                    <td class="amount"><?= number_format($result["categorie_salaire"], 0, '', '.') ?></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount">3,00</td>
                    <td class="amount"><?= number_format($travail, 0, '', '.') ?></td>
                </tr>
            <?php endif; ?>

            <?php if (shouldDisplayLine($famille)): ?>
                <tr>
                    <td>CNPS Prest. Familiales</td>
                    <td class="amount"><?= number_format($result["categorie_salaire"], 0, '', '.') ?></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount">5,75</td>
                    <td class="amount"><?= number_format($famille, 0, '', '.') ?></td>
                </tr>
            <?php endif; ?>

            <?php if (shouldDisplayLine($taxe)): ?>
                <tr>
                    <td>FDFP Taxe Apprentissage</td>
                    <td class="amount"><?= number_format($total_fiscal, 0, '', '.') ?></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount">0,04</td>
                    <td class="amount"><?= number_format($taxe, 0, '', '.') ?></td>
                </tr>
            <?php endif; ?>

            <?php if (shouldDisplayLine($tax)): ?>
                <tr>
                    <td>FDFP Formation Continue</td>
                    <td class="amount"><?= number_format($total_fiscal, 0, '', '.') ?></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount">1,20</td>
                    <td class="amount"><?= number_format($tax, 0, '', '.') ?></td>
                </tr>
            <?php endif; ?>

            <!-- TOTAL RETENUES -->
            <?php if (shouldDisplayLine($tota_retenus) || shouldDisplayLine($tota_retenues)): ?>
                <tr class="total-row">
                    <td><strong>TOTAL RETENUES</strong></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"><strong><?= number_format($tota_retenus, 0, '', '.') ?></strong></td>
                    <td class="amount"></td>
                    <td class="amount"><strong><?= number_format($tota_retenues, 0, '', '.') ?></strong></td>
                </tr>
            <?php endif; ?>

            <!-- NET À PAYER -->
            <?php if (shouldDisplayLine($salaire_net)): ?>
                <tr class="net-row">
                    <td colspan="2"><strong>NET À PAYER</strong></td>
                    <td colspan="2" class="amount"><strong><?= number_format($salaire_net, 0, '', '.') ?> <?= $currency_symbol ?></strong></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                    <td class="amount"></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- SIGNATURE -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-text">Signature employé</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-text">Signature employeur</div>
            </div>
        </div>
    </div>

    <?php if ($count < $total): ?>
    <div class="page-break"></div>
<?php endif; ?>

    <?php $count++; endforeach; ?>

<script type="text/javascript">
    window.onload = function() {
        window.print();
    }
</script>
</body>
</html>