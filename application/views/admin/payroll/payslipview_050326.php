    <?php
    
    $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
    ?>
    

    <?php


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
    //echo ucfirst($formatter->format($date)); // ex: Avril
    ?>
    
    <?php
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
    $total_brute = $result["categorie_salaire"] + $result["sursalaire"]+ $prime_anciennete + $result["prime_trans"]+ $result["autre_reve"] + $result["forfait_hs"] + $result["prime_resp"] + $result["prime_rend"] + $result["prime_risque"] + $result["prime_assi"] + $result["prime_grati"] + $result["conge"];
    
    
    $total_pourcentage = $total_brute * 0.1;
    $primet= 30000;
    $primeresp= 0;
    $primerisq= 0;
    
    //prime transport
    if ($result["prime_trans"] > $primet) {
        $trans = $result["prime_trans"] - $primet;
    } else {
        $trans =0;
    }
    $final_trans=$trans;
    //fin prime transport
    
    
    
    //fin prime transport
    $prime_anc = isset($result["prime_anc"]) ? floatval($result["prime_anc"]) : 0;
    
    $total_pourcentage = isset($total_pourcentage) ? floatval($total_pourcentage) : 0;
    
    if ($prime_anciennete > $total_pourcentage) {
        $ancien = $prime_anciennete - $total_pourcentage;
    } else {
        $ancien = 0;
    }
    
    $final_anc = $ancien;
    
    
    //prime rendement
    if ($result["prime_rend"] > $total_pourcentage)
    {
        $rends= $result["prime_rend"] - $total_pourcentage;
    }
    else{
        $rends = 0;
    }
    $final_rend= $rends;
    
    //fin prime rendement
    if ($result["prime_resp"] > $total_pourcentage)
    {
        $respo= $result["prime_resp"] - $total_pourcentage;
    }
    else{
        $respo = 0;
    }
    $final_respo= $respo;
    
    //prime risque
    
    
    if ($result["prime_risque"] > $total_pourcentage) {
        $risq = $result["prime_risque"] - $total_pourcentage;
    } else {
        $risq= 0;
    }
    $final_risq=$risq;
    
    
    if ($result["autre_reve"] > $total_pourcentage) {
        $autre = $result["autre_reve"] - $total_pourcentage;
    } else {
        $autre= 0;
    }
    
    $final_autres=$autre;
    
    //fin prime risque
    
    //prime assiduité
    
    
    if ($result["prime_assi"] > $total_pourcentage) {
        $assi = $result["prime_assi"] - $total_pourcentage;
    } else {
        $assi = 0;
    }
    
    $final_assi = $assi;
    
    if ($result["forfait_hs"] > $total_pourcentage) {
        $assi = $result["forfait_hs"] - $total_pourcentage;
    } else {
        $forfait = 0;
    }
    
    $final_forfait = $forfait;
    
    
    if ($result["sursalaire"] > $total_pourcentage) {
        $sura = $result["sursalaire"] - $total_pourcentage;
    } else {
        $sura = 0;
    }
    
    $final_sura = $sura;
    
    //fin prime assiduite
    
    //debut total fiscal
    
    //$total_fiscal = $result["categorie_salaire"] + $final_trans + $final_anc + $final_rend + $final_risq + $final_respo  + $final_assi + $result["sursalaire"] ;
    $total_fiscal = $result["categorie_salaire"] + $final_trans + $final_anc + $final_rend + $final_risq + $final_respo  + $final_assi + $result["sursalaire"] + $final_autres;
    
    //fin fiscal
    
    //debut total brute social
    $total_social= $total_brute - $result["prime_trans"];
    
    //social
    
    // Plafond CNPS retraite = 45 * 75000
    $plafond_cnps = 3375000; // 3 375 000
    
    // Comparer et affecter selon le plafond
    if ($total_social < $plafond_cnps) {
        $cnps_retraite_base = $total_social;
    } else {
        $cnps_retraite_base = $plafond_cnps;
    }
    $etraite =$cnps_retraite_base;
    
    
    //debut ITS
    
    
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
    $coef = $result["part_igr"]; // ex: 7
    $reduction = 0;
    
    // Réduction plafonnée à 5 parts
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
    
    // Calcul de l'ITS (impôt net)
    $impot_net = max($impot - $reduction, 0);
    $its = round($impot_net, 2);
    ?>

    <style type="text/css">
        @media print {
            /* Format de page A4 */
            @page {
                size: A4;
                margin: 10mm; /* réduit les marges */
            }

            body {
                font-size: 12px;
                line-height: 1.3;
            }

            /* Empêcher la coupure des blocs entreprise / employé */
            .payslip_address,
            .header-section,
            .employee-info,
            .company-info,
            .row {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            /* Forcer tout sur une seule page */
            .print-page {
                page-break-after: avoid;
                page-break-before: avoid;
            }

            /* Ton code Bootstrap print déjà existant */
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

            /* Responsive print */
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

        /* Style du tableau sans bordures */
        .payslip-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0; /* Réduit la marge */
            border: none;
        }

        .payslip-table th, .payslip-table td {
            border: none;
            padding: 4px 6px; /* Réduit le padding vertical */
            text-align: left;
            font-size: 11px;
            line-height: 1.2; /* Réduit l'espacement des lignes dans les cellules */
        }

        .payslip-table thead th {
            background-color: #e9ecef;
            font-weight: bold;
            vertical-align: middle;
            padding: 6px; /* Garde un peu plus de padding pour l'en-tête */
        }

        /* Pour réduire encore plus l'espacement si nécessaire */
        .payslip-table tbody tr {
            /* Supprimé: border-bottom: 10px solid transparent; */
            /* Option: espacement minimal si vraiment nécessaire */
            border-bottom: 2px solid transparent; /* Réduit à 2px si vous voulez un peu d'espace */
        }

        /* Pour alterner les couleurs des lignes sans trop d'espace */
        .payslip-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Pour les lignes de total - garder un peu d'espace avant/après */
        .total-row {
            border-top: 2px solid #ddd;
            border-bottom: 2px solid #ddd;
            margin-top: 4px;
            margin-bottom: 4px;
        }
    </style>


    </style>
    
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $this->lang->line('payslip'); ?></title>
        <style>
            /* Styles généraux */
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

            /* Responsive adjustments */
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
        <!--<div class="section-title">DÉTAILS DE RÉMUNÉRATION</div>-->
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
            <tr>
                <td>Salaire catégoriel</td>
                <td class="amount"><?php $categorie_salaire = $result["categorie_salaire"]; echo number_format($categorie_salaire, 0, '', '.'); ?></td>
                <td>30</td>
                <td class="amount"><?php echo number_format($categorie_salaire, 0, '', '.'); ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
    
            <!-- Salaire de base
            <tr>
                <td>Salaire de base</td>
                <td class="amount"><?php $salaire_base = $result["salaire_base"]; echo number_format($salaire_base, 0, '', '.'); ?></td>
                <td>30</td>
                <td class="amount"><?php echo number_format($salaire_base, 0, '', '.'); ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>-->
    
    
    
            <!-- Sursalaire -->
            <tr>
                <td>Sursalaire</td>
                <td class="amount"><?php $sursalaire = $result["sursalaire"]; echo number_format($sursalaire, 0, '', '.'); ?></td>
                <td>30</td>
                <td class="amount"><?php echo number_format($sursalaire, 0, '', '.'); ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
    
            <!-- Prime d'anciennete -->
            <tr>
                <td>Prime d'ancienneté</td>
                <td class="amount"><?= number_format($prime_anciennete, 0, '', '.') ?></td>
                <td>1</td>
                <td class="amount"><?= number_format($prime_anciennete, 0, '', '.') ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
    
    
            <!-- les indemmites
            <tr>
                <td>les indemmites</td>
                <td class="amount"><?php $tax	=	$result["tax"]; echo  number_format($tax, 0, '', '.'); ?> </td>
                <td>1</td>
                <td class="amount"><?php $tax	=	$result["tax"]; echo  number_format($tax, 0, '', '.'); ?> </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>-->
    
            <!-- <?php echo $this->lang->line('earning'); ?> -->
           <!-- <tr>
                <td><?php echo $this->lang->line('earning'); ?></td>
                <td class="amount"><?php $allocance	=	$result["total_allowance"] ;echo number_format($allocance, 0, '', '.');  ?></td>
                <td>1</td>
                <td class="amount"><?php echo number_format($result["total_allowance"], 0, '', '.');  ?> </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>-->
    
            <!-- Prime de transport -->
            <tr>
                <td>Prime de transport</td>
                <td class="amount"><?php $prime_trans	=	$result["prime_trans"]; echo  number_format($prime_trans, 0, '', '.'); ?></td>
                <td>1</td>
                <td class="amount"><?php $prime_trans	=	$result["prime_trans"]; echo  number_format($prime_trans, 0, '', '.'); ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
    
            <!-- Forfait heures suppl -->
            <tr>
                <td>Forfait heures suppl</td>
                <td class="amount"><?php $forfait_hs	=	$result["forfait_hs"]; echo  number_format($forfait_hs, 0, '', '.'); ?></td>
                <td>1</td>
                <td class="amount"><?php $forfait_hs	=	$result["forfait_hs"]; echo  number_format($forfait_hs, 0, '', '.'); ?>  </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
    
            <!-- Prime de responsabilité -->
            <tr>
                <td>Prime de responsabilité</td>
                <td class="amount"><?php $prime_resp	=	$result["prime_resp"]; echo  number_format($prime_resp, 0, '', '.'); ?> </td>
                <td>1</td>
                <td class="amount"><?php $prime_resp	=	$result["prime_resp"]; echo  number_format($prime_resp, 0, '', '.'); ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
    
            <!-- Prime de rendement -->
            <tr>
                <td>Prime de rendement</td>
                <td class="amount"><?php $prime_rend	=	$result["prime_rend"]; echo ($prime_rend); ?></td>
                <td>1</td>
                <td class="amount"><?php $prime_rend	=	$result["prime_rend"]; echo  number_format($prime_rend);?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
    
            <!-- Prime de fonction -->
            <tr>
                <td>Prime de fonction</td>
                <td class="amount"><?php $autre_reve	=	$result["autre_reve"]; echo  number_format($autre_reve, 0, '', '.'); ?></td>
                <td>1</td>
                <td class="amount"><?php $autre_reve	=	$result["autre_reve"]; echo  number_format($autre_reve, 0, '', '.'); ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
    
            <!-- Prime de risque -->
            <tr>
                <td>Prime de risque</td>
                <td class="amount"><?php $prime_risque	=	$result["prime_risque"]; echo  number_format($prime_risque, 0, '', '.'); ?></td>
                <td>1</td>
                <td class="amount"><?php $prime_risque	=	$final_risq; echo  number_format($prime_risque, 0, '', '.');  ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
    
            <!-- Prime d'assiduité -->
            <tr>
                <td>Prime d'assiduité</td>
                <td class="amount"><?php $prime_assi	=	$result["prime_assi"]; echo  number_format($prime_assi, 0, '', '.'); ?></td>
                <td>1</td>
                <td class="amount"><?php $prime_assi	=	$result["prime_assi"]; echo  number_format($prime_assi, 0, '', '.');  ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
    
            <!-- Prime Gratification -->
            <tr>
                <td>Prime Gratification</td>
                <td class="amount"><?php $prime_grati	=	$result["prime_grati"]; echo  number_format($prime_grati, 0, '', '.'); ?></td>
                <td>1</td>
                <td class="amount"><?php $prime_grati	=	$result["prime_grati"]; echo  number_format($prime_grati, 0, '', '.'); ?> </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
    
            <!-- Congé -->
           <!-- <tr>
                <td>Congé</td>
                <td class="amount"><?php $conge	=	$result["conge"]; echo  number_format($conge, 0, '', '.'); ?></td>
                <td>1</td>
                <td class="amount"><?php $conge	=	$result["conge"]; echo  number_format($conge, 0, '', '.'); ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>-->
    
    
    
            <!-- Continuer avec les autres lignes de rémunération ici -->
            <!-- ... (toutes les autres lignes du tableau) ... -->
    
            <!-- Total Brut -->
            <tr class="total-row">
                <td><strong>Total Brut</strong></td>
                <td></td>
                <td></td>
                <td class="amount"><strong><?php echo number_format($total_brute, 0, '', '.'); ?></strong></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
    
            <!-- Total Brut Fiscal -->
            <tr class="total-row">
                <td><strong>Total Brut Fiscal</strong></td>
                <td></td>
                <td></td>
                <td class="amount"><strong><?php echo number_format($total_fiscal, 0, '', '.'); ?></strong></td>
    
                <td></td>
    
                <td></td>
                <td></td>
            </tr>
    
            <!-- Retenues -->
            <!-- ... (toutes les retenues) ... -->
            <!-- Total Brute Social -->
            <tr class="total-row">
                <td><strong>Total Brute Social</strong></td>
                <td></td>
                <td></td>
                <td class="amount"><strong><?php echo number_format($total_social, 0, '', '.'); ?></strong></td>
    
                <td></td>
                 <td></td>
                <td></td>
            </tr>
    
            <!-- ITS -->
            <tr class="total-row">
                <td><strong>ITS</strong></td>
                <td class="amount"><?php $imp_sal	=	$total_fiscal; echo  number_format($imp_sal, 0, '', '.'); ?></td>
                <td></td>
                <td></td>
                <td class="amount"><strong><?php echo $its; ?></strong></td>
                <td>1,2</td>
                <td><?php $its_patronal	=	$total_fiscal * 0.012; echo  number_format($its_patronal, 0, '', '.'); ?></td>
            </tr>
    
            <!-- Total Brute Social
            <tr class="total-row">
                <td><strong>Impôt IGR</strong></td>
                <td class="amount"><?php $prime_grati	=	$total_fiscal; echo  number_format($prime_grati, 0, '', '.'); ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr> -->
    
            <!-- CMU -->
            <tr class="total-row">
                <td><strong>CMU</strong></td>
                <td class="amount">-</td>
                <td></td>
                <td></td>
                <?php
                // Définir le montant unitaire CMU
                $cmu_unit = 500;
    
                // Si epf_no est vide ou nul, on considère au moins 1 personne
                $epf_no = !empty($result["epf_no"]) ? $result["epf_no"] : 1;
    
                // Calcul du CMU total
                $cmu_total = $epf_no * $cmu_unit;
                ?>
                <td class="amount"><?php echo number_format($cmu_total, 0, '', '.'); ?></td>
                <td></td>
                <td class="amount"><?php echo number_format($cmu_total, 0, '', '.'); ?></td>
            </tr>
    
    
            <!-- CNPS, Régime de Retraite -->
            <tr class="total-row">
                <td><strong>CNPS, Régime de Retraite</strong></td>
                <td class="amount"><?php $retraite	= $etraite ; echo  number_format($retraite, 0, '', '.'); ?></td>
                <td>6,30</td>
                <td></td>
                <td class="amount"><?php $retrai_regime	= $etraite * 0.0630 ; echo  number_format($retrai_regime, 0, '', '.'); ?></td>
                <td>7,70</td>
                <td class="amount"><?php $retrait	= $etraite * 0.077; echo  number_format($retrait, 0, '', '.'); ?></td>
            </tr>
    
            <!-- CNPS, Accident Travail -->
            <tr class="total-row">
                <td><strong>CNPS, Accident Travail</strong></td>
                <td class="amount"><?php $cnps_tra	=	$result["categorie_salaire"]; echo  number_format($cnps_tra, 0, '', '.'); ?> </td>
                <td></td>
                <td></td>
                <td></td>
                <td>3,00</td>
                <td class="amount"><?php $travail	=  $result["categorie_salaire"] * 0.04; echo  number_format($travail, 0, '', '.'); ?></td>
            </tr>
    
            <!-- CNPS, Prest. Famil -->
            <tr class="total-row">
                <td><strong>CNPS, Prest. Famil</strong></td>
                <td class="amount"><?php $cnps_famille	=	$result["categorie_salaire"]; echo  number_format($cnps_famille, 0, '', '.'); ?> </td>
                <td></td>
                <td></td>
                <td></td>
                <td>5,75</td>
                <td class="amount"><?php $famille	= $result["categorie_salaire"] * 0.0575 ; echo  number_format($famille, 0, '', '.'); ?></td>
            </tr>
    
            <!-- FDFP, Taxe Apprentissage -->
            <tr class="total-row">
                <td><strong>FDFP, Taxe Apprentissage</strong></td>
                <!--<td class="amount"><?php $fdfp_tax	=	$total_fiscal ;
                    ; echo  number_format($fdfp_tax, 0, '', '.'); ?></td>-->
                <td class="amount"><?php $fdfp_tax	= $total_fiscal ; echo  number_format($fdfp_tax, 0, '', '.'); ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0,04 </td>
                <td class="amount"><?php $taxe	= $total_fiscal * 0.004 ; echo  number_format($taxe, 0, '', '.'); ?></td>
            </tr>
    
            <!-- FDFP, Form. Prof. Continue -->
            <tr class="total-row">
                <td><strong>FDFP, Form. Prof. Continue</strong></td>
                <td class="amount"><?php $fdfp_continue	=	$total_fiscal; echo  number_format($fdfp_continue, 0, '', '.'); ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td>1,20</td>
                <td><?php $tax	= $total_fiscal * 0.006 ; echo  number_format($tax, 0, '', '.'); ?></td>
            </tr>
            <!-- Total des retenues -->
            <tr class="total-row">
                <td><strong>Total des retenues</strong></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="amount"><strong><?php $tota_retenus = $retrai_regime + $impot_net + $cmu_total; echo number_format($tota_retenus, 0, '', '.'); ?></strong></td>
                <td></td>
                <td class="amount"><strong><?php $tota_retenues = $its_patronal + $retrait + $cmu_total + $travail + $famille + $taxe +  $tax; echo number_format($tota_retenues, 0, '', '.'); ?></strong></td>
    
            </tr>
    
            <!-- Net à payer -->
            <tr class="net-payer">
                <td colspan="3"><strong>NET À PAYER</strong></td>
                <td colspan="2" class="amount"><strong><?php $salaire_net = $total_brute - $tota_retenus; echo number_format($salaire_net, 0, '', '.'); ?> <?php echo $currency_symbol; ?></strong></td>
                <td></td>
                <td></td>
            </tr>
            </tbody>
        </table>
    
        <!-- Zone de signature -->
        <div class="signature-are">
            <!--<div style="display: flex; justify-content: space-between;">-->
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
    
        <!--<div class="footer-note">
            <p>Pour vous aider à faire valoir vos droits, conservez ce bulletin de paie sans limitation de durée.</p>
        </div>-->
    </div>
    </body>
    </html>