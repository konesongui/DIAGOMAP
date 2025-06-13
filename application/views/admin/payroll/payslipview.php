<?php

$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<?php
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

$total_brute = $result["categorie_salaire"] + $result["sursalaire"] + $result["prime_anc"] + $result["prime_trans"] + $result["forfait_hs"] + $result["prime_resp"] + $result["prime_rend"] + $result["prime_risque"] + $result["prime_assi"] + $result["prime_grati"] + $result["conge"];

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

//prime risque


if ($result["prime_risque"] > $total_pourcentage) {
    $risq = $result["prime_risque"] - $total_pourcentage;
} else {
    $risq= 0;
}

$final_risq=$risq;

//fin prime risque

//prime assiduité


if ($result["prime_assi"] > $total_pourcentage) {
    $assi = $result["prime_assi"] - $total_pourcentage;
} else {
    $assi = 0;
}

$final_assi = $assi;

//fin prime assiduite





?>





<style type="text/css">
    @media print {
        .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
            float: left;
        }
        .col-sm-12 {
            width: 100%;
        }
        .col-sm-11 {
            width: 91.66666667%;
        }
        .col-sm-10 {
            width: 83.33333333%;
        }
        .col-sm-9 {
            width: 75%;
        }
        .col-sm-8 {
            width: 66.66666667%;
        }
        .col-sm-7 {
            width: 58.33333333%;
        }
        .col-sm-6 {
            width: 50%;
        }
        .col-sm-5 {
            width: 41.66666667%;
        }
        .col-sm-4 {
            width: 33.33333333%;
        }
        .col-sm-3 {
            width: 25%;
        }
        .col-sm-2 {
            width: 16.66666667%;
        }
        .col-sm-1 {
            width: 8.33333333%;
        }
        .col-sm-pull-12 {
            right: 100%;
        }
        .col-sm-pull-11 {
            right: 91.66666667%;
        }
        .col-sm-pull-10 {
            right: 83.33333333%;
        }
        .col-sm-pull-9 {
            right: 75%;
        }
        .col-sm-pull-8 {
            right: 66.66666667%;
        }
        .col-sm-pull-7 {
            right: 58.33333333%;
        }
        .col-sm-pull-6 {
            right: 50%;
        }
        .col-sm-pull-5 {
            right: 41.66666667%;
        }
        .col-sm-pull-4 {
            right: 33.33333333%;
        }
        .col-sm-pull-3 {
            right: 25%;
        }
        .col-sm-pull-2 {
            right: 16.66666667%;
        }
        .col-sm-pull-1 {
            right: 8.33333333%;
        }
        .col-sm-pull-0 {
            right: auto;
        }
        .col-sm-push-12 {
            left: 100%;
        }
        .col-sm-push-11 {
            left: 91.66666667%;
        }
        .col-sm-push-10 {
            left: 83.33333333%;
        }
        .col-sm-push-9 {
            left: 75%;
        }
        .col-sm-push-8 {
            left: 66.66666667%;
        }
        .col-sm-push-7 {
            left: 58.33333333%;
        }
        .col-sm-push-6 {
            left: 50%;
        }
        .col-sm-push-5 {
            left: 41.66666667%;
        }
        .col-sm-push-4 {
            left: 33.33333333%;
        }
        .col-sm-push-3 {
            left: 25%;
        }
        .col-sm-push-2 {
            left: 16.66666667%;
        }
        .col-sm-push-1 {
            left: 8.33333333%;
        }
        .col-sm-push-0 {
            left: auto;
        }
        .col-sm-offset-12 {
            margin-left: 100%;
        }
        .col-sm-offset-11 {
            margin-left: 91.66666667%;
        }
        .col-sm-offset-10 {
            margin-left: 83.33333333%;
        }
        .col-sm-offset-9 {
            margin-left: 75%;
        }
        .col-sm-offset-8 {
            margin-left: 66.66666667%;
        }
        .col-sm-offset-7 {
            margin-left: 58.33333333%;
        }
        .col-sm-offset-6 {
            margin-left: 50%;
        }
        .col-sm-offset-5 {
            margin-left: 41.66666667%;
        }
        .col-sm-offset-4 {
            margin-left: 33.33333333%;
        }
        .col-sm-offset-3 {
            margin-left: 25%;
        }
        .col-sm-offset-2 {
            margin-left: 16.66666667%;
        }
        .col-sm-offset-1 {
            margin-left: 8.33333333%;
        }
        .col-sm-offset-0 {
            margin-left: 0%;
        }
        .visible-xs {
            display: none !important;
        }
        .hidden-xs {
            display: block !important;
        }
        table.hidden-xs {
            display: table;
        }
        tr.hidden-xs {
            display: table-row !important;
        }
        th.hidden-xs,
        td.hidden-xs {
            display: table-cell !important;
        }
        .hidden-xs.hidden-print {
            display: none !important;
        }
        .hidden-sm {
            display: none !important;
        }
        .visible-sm {
            display: block !important;
        }
        table.visible-sm {
            display: table;
        }
        tr.visible-sm {
            display: table-row !important;
        }
        th.visible-sm,
        td.visible-sm {
            display: table-cell !important;
        }

    }

</style>
<style type="text/css">
    table.table.table-hover thead{
        background-color: #e8e8e8;
    }
</style>

<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $this->lang->line('payslip'); ?></title>
</head>

<div class="container-fluid">


    <div class="row payslip_print" id="payslip_print">
        <div class="col-md-12">
            <div class="card card-body">


                <div class="row" style="margin-bottom: 5px;">
                    <div class="col-md-12">

                        <table width="100%">

                            <div class="row">
                                <div class="col-md-4 col-xs-6 col-sm-6">
                                    <img style="width: 100%; height: 80px !important;" src="<?= base_url() . "/uploads/school_content/admin_logo/" . $sch_setting->admin_logo ?>" alt="Image banniere" />
                                </div>
                                <div class="col-md-8 col-xs-6 col-sm-6 text-left payslip_address">
                                    <p>
                                        Téléphone : <b><?= $sch_setting->phone ?></b>
                                    </p>
                                    <p style="font-size: 9px">
                                        Adresse: <b><?= strtoupper($sch_setting->address) ?></b>
                                    </p>
                                    <p>
                                        E-mail : <b><?= $sch_setting->email ?></b>
                                    </p>
                                </div>
                            </div>
                            <tr>
                                <td align="center"><h3 style="margin: 10px 0 20px;"><?php echo $this->lang->line('payslip_for_the_period_of'); ?> <?php echo ucfirst($formatter->format($date)) ?> <?php echo $result["year"] ?></h3></td>
                            </tr>
                        </table>
                        <table class="table table-condensed borderless payslip_info">
                            <tr>
                                <td style="font-size: 10px">Matricule</td>
                                <td style="font-size: 10px">: <?php echo $result["employee_id"] ?></td>
                                <td style="font-size: 10px">Nom et prénom</td>
                                <td style="font-size: 10px">: <?php echo $result["surname"] . " " . $result["name"] ?></td>
                                <?php  if ($sch_setting->staff_designation) { ?>

                                    <th style="font-size: 10px"><?php echo $this->lang->line('designation'); ?></th>
                                    <td style="font-size: 10px"><?php echo $result["designation"] ?></td>
                                <?php } ?>
                                <?php  if ($sch_setting->staff_department) { ?>

                                    <th style="font-size: 10px"><?php echo $this->lang->line('department'); ?></th>
                                    <td style="font-size: 10px"><?php echo $result["department"] ?></td>
                                <?php } ?>
                            </tr>
                            <tr>


                            </tr>
                            <tr>
                                <td style="font-size: 10px">Date de paie</td>
                                <td style="font-size: 10px">:  <?php echo $result["payment_date"] ?></td>
                                <td style="font-size: 10px">CNPS N°</td>
                                <td style="font-size: 10px">:
                                    <?php echo $result["cnps_no"] ?>
                                </td>
                                <td style="font-size: 10px">Mode de paie</td>
                                <td style="font-size: 10px">: <?php echo $result["payment_mode"] ?></td>
                            </tr>
                            <tr>

                                <td  style="font-size: 10px">Nj de travaille</td>
                                <td>:
                                    <?php
                                    $days = ceil($salary_info->total_days / 8);
                                    echo $days;
                                    ?>
                                </td>

                                <td style="font-size: 10px">Categorie salariale</td>
                                <!--<td>:<?php echo $result["categorie_salaire"] ?></td>-->
                                <td>:1A</td>

                                <td style="font-size: 10px">Prime d'ancienneté</td>
                                <td>:<?php echo $result["prime_anc"] ?></td>

                                <td style="font-size: 10px">Nb d'enfant en charge</td>
                                <td>:<?php echo $result["part_igr"] ?></td><br/>



                            </tr>

                            <?php if(!empty($bankinfo->bank_name)){ ?>
                                <tr>
                                    <!--<td>Nom de la banque</td>
										<td>: <?php echo $bankinfo->holder_name; ?></td>-->
                                    <td style="font-size: 10px">Numéro bancaire</td>
                                    <td style="font-size: 10px">: <?php echo $bankinfo->account_number; ?></td>
                                </tr>

                            <?php } ?>
                        </table>
                    </div>
                </div>
                <style>
                    .table-condensed>thead>tr>th, .table-condensed>tbody>tr>th, .table-condensed>tfoot>tr>th, .table-condensed>thead>tr>td, .table-condensed>tbody>tr>td, .table-condensed>tfoot>tr>td { padding: 2px 5px; }
                </style>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-condensed borderless" style="border-color: #0c0c0c; font-size: 12px">
                            <thead class="thead-light" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px">
                            <tr style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px">

                                <th colspan="1" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"></th>
                                <th style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"></th>
                                <th colspan="3" class="text-center" style="border: 1px solid black; font-size: 9px">PART.SALARIALE </th>
                                <th class="text-center" style="border: 0px solid black; font-size: 9px">PART.PATRONALE </th>

                            </tr>
                            <tr style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px">

                                <th style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px">DESIGNATION</th>

                                <th  rowspan="2" style="border-color: #0c0c0c;border: 1px solid black;">BASE</th>
                                <th  rowspan="2" style="border-color: #0c0c0c;border: 1px solid black;">Nbre/taux</th>
                                <th  rowspan="2" style="border-color: #0c0c0c;border: 1px solid black;">GAINS</th>
                                <th  rowspan="2" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px">RETENUES</th>

                                <th  rowspan="2" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px">Nbre/taux</th>
                                <th  rowspan="2" style="border-color: #0c0c0c;border: 0px solid black; font-size: 12px">RETENUES</th>
                                <!--<th class="text-right">Cot.Patronal</th>
                                <th class="text-right">Déductions</th>-->
                            </tr>



                            </thead>
                            <tbody  style="border-color: #0c0c0c;border: 1px solid black;">
                            <tr style="border-color: #0c0c0c;border: 0px solid black;">
                                <td style="border-color: #0c0c0c;border: 1px solid black;">Salaire de base</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"><?php $categorie_salaire	=	$result["categorie_salaire"]; echo  number_format($categorie_salaire, 0, '', '.'); ?></td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"> 30 </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px">  <?php $categorie_salaire	=	$result["categorie_salaire"]; echo  number_format($categorie_salaire, 0, '', '.'); ?></td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"></td>

                            </tr>
                            <tr>
                                <td style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px">Sursalaire</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"><?php $sursalaire	=	$result["sursalaire"]; echo  number_format($sursalaire, 0, '', '.'); ?></td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"> 30</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"> <?php $sursalaire	=	$result["sursalaire"]; echo  number_format($sursalaire, 0, '', '.'); ?> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"></td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                            </tr>
                            <tr>
                                <td  style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">Prime d'anciennete</td>
                                <td class="text-right"><?php $prime_anc	=	$result["prime_anc"]; echo  number_format($prime_anc, 0, '', '.'); ?> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">2</td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: px solid black;"><?php $prime_anc	=	$result["prime_anc"]; echo  number_format($prime_anc, 0, '', '.'); ?> </td>

                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">   </td>

                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                            </tr>

                            <tr style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px">
                                <td  style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">les indemmites</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"><?php $tax	=	$result["tax"]; echo  number_format($tax, 0, '', '.'); ?> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"> <?php $tax	=	$result["tax"]; echo  number_format($tax, 0, '', '.'); ?> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                            </tr>



                            <tr style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px">
                                <td  style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"><?php echo $this->lang->line('earning'); ?></td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"><?php $allocance	=	$result["total_allowance"] ;echo number_format($allocance, 0, '', '.');  ?> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">1</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"> <?php echo number_format($result["total_allowance"], 0, '', '.');  ?> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                            </tr>


                            <tr>
                                <td  style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">Prime de transport</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"><?php $prime_trans	=	$result["prime_trans"]; echo  number_format($prime_trans, 0, '', '.'); ?></td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">30 </td>
                                <td class="text-right" style="border-color: #0c0c0c;border:1px solid black;"><?php $prime_trans	=	$result["prime_trans"]; echo  number_format($prime_trans, 0, '', '.'); ?>  </td>
                                <td class="text-right" hidden style="border-color: #0c0c0c;border: 1px solid black;">

                                </td>

                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">

                                </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                            </tr>
                            <tr>
                                <td  style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">Forfait heures suppl.</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"><?php $forfait_hs	=	$result["forfait_hs"]; echo  number_format($forfait_hs, 0, '', '.'); ?></td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"> 1 </td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;"><?php $forfait_hs	=	$result["forfait_hs"]; echo  number_format($forfait_hs, 0, '', '.'); ?>  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                            </tr>
                            <tr>
                                <td style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">Prime de responsabilité</td>
                                <td class="text-right"><?php $prime_resp	=	$result["prime_resp"]; echo  number_format($prime_resp, 0, '', '.'); ?> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">1</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">

                                    <?php


                                    ?>
                                </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                            </tr>
                            <!--<tr>
                                    <td style="border-color: #0c0c0c;border: 2px solid black;font-size: 12px">Bonus</td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;"></td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;font-size: 12px"><?php $bonus	=	$result["bonus"]; echo  number_format($bonus); ?>  </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;">- </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;">  </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;">  </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;">  </td>
                                </tr>-->
                            <tr>
                                <td style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">Prime de rendement</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"> <?php $prime_rend	=	$result["prime_rend"]; echo  number_format($prime_rend, 0, '', '.'); ?></td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"> 1</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px">  <?php $prime_rend	=	$result["prime_rend"]; echo  number_format($prime_rend, 0, '', '.');?></td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                            </tr>
                            <tr>
                                <td style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">Prime de risque</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"><?php $prime_risque	=	$result["prime_risque"]; echo  number_format($prime_risque, 0, '', '.'); ?></td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">1  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"><?php $prime_risque	=	$result["prime_risque"]; echo  number_format($prime_risque, 0, '', '.');  ?> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                            </tr>
                            <tr>
                                <td style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">Prime d'assiduité</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"><?php $prime_assi	=	$result["prime_assi"]; echo  number_format($prime_assi, 0, '', '.'); ?></td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"> 1 </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"><?php $prime_assi	=	$result["prime_assi"]; echo  number_format($prime_assi, 0, '', '.');  ?> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                            </tr>
                            <tr>
                                <td style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">Prime Gratification</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"><?php $prime_grati	=	$result["prime_grati"]; echo  number_format($prime_grati, 0, '', '.'); ?></td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">  1</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"> <?php $prime_grati	=	$result["prime_grati"]; echo  number_format($prime_grati, 0, '', '.'); ?> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                            </tr>
                            <tr>
                                <td style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">Congé</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"><?php $conge	=	$result["conge"]; echo  number_format($conge, 0, '', '.'); ?> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"> 1</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  <?php $conge	=	$result["conge"]; echo  number_format($conge, 0, '', '.'); ?></td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"> </td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                            </tr>
                            <tr style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px">
                                <td  style="border-color: #0c0c0c;border: 1px solid black;color: black;font-size: 12px;font-family: bold">Total Brute</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">


                                </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"> - </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"><?php  echo $total_brute; ?> </td>

                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"></td>
                                <td class="text-right" hidden style="border-color: #0c0c0c;border: 1px solid black;">
                                </td>
                                <td class="text-right" hidden style="border-color: #0c0c0c;border: 1px solid black;">
                                </td>
                                <td class="text-right" hidden style="border-color: #0c0c0c;border: 1px solid black;">


                                </td>


                            </tr>

                            <tr style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px">
                                <td  style="border-color: #0c0c0c;border: 1px solid black;color: black;font-size: 12px;font-family: bold">Total Brute Fiscal</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"> - </td>
                                <td class="text-right" hidden style="border-color: #0c0c0c;border: 1px solid black;"> - </td>
                                <td class="text-right" hidden style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px">  </td>

                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"> <?php $total_fiscal= $total_brute - $final_trans - $final_rend - $final_risq - $final_assi; echo $total_fiscal; ?> </td>



                                <!--<td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"> <?php $total_fiscal	=	$categorie_salaire + $sursalaire + $prime_anc + $tax + $allocance + $forfait_hs + ($resp - $total_pourcentage) + ($rend - $total_pourcentage) +  ($tran - $total_pourcentage) + ($risque - $total_pourcentage) + ($assi - $total_pourcentage) ; echo  number_format($total_fiscal, 0, '', '.') ; ?> </td>-->

                                <td class="text-right" hidden style="border-color: #0c0c0c;border: 1px solid black;">

                                </td>
                                <td class="text-right" hidden style="border-color: #0c0c0c;border: 1px solid black;">
                                </td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                            </tr>

                            <tr style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px">
                                <td  style="border-color: #0c0c0c;border: 1px solid black;color: black;font-size: 12px;font-family: bold">Total Brute Social</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"> - </td>
                                <td class="text-right" hidden style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px">  </td>

                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"> <?php  echo  $total_social= $total_brute - ($prime_trans - $prime); ?> </td>





                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                            </tr>
                            <tr>
                                <td style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">ITS</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"><?php $imp_sal	=	$total_brute; echo  number_format($imp_sal, 0, '', '.'); ?></td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"> 1,2</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"></td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">
                                    <?php
                                    $impot = "-";

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
                                        $salaire = 75000;
                                    }

                                    echo $impot;

                                    ?>
                                </td>



                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"></td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">  </td>

                                <!--<th style="border-color: #0c0c0c;border: 1px solid black;" class="text-right"><?php $retenu	= $imp_sal * 0.012; echo  number_format($retenu, 0, '', '.'); ?></th>-->
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                            </tr>

                            <!-- <tr>
                                    <td style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">Imp. sur Trait. et Sal. (IS) Employé</td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"><?php $imp_sal	=	$result["cnps_regim"]; echo  number_format($imp_sal, 0, '', '.'); ?></td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"></td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"></td>


                                    <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"></td>

                                    <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"> - </td>

                                </tr>-->

                            <tr>
                                <td style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">Impôt IGR</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"><?php $prime_grati	=	$impot * $result["part_igr"]; echo  number_format($prime_grati, 0, '', '.'); ?> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"> - </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right"  style="border-color: #0c0c0c;border: 1px solid black;">-  </td>
                            </tr>
                            <!-- <tr>
                                    <td style="border-color: #0c0c0c;border: 2px solid black;font-size: 12px">Imp. Gén. sur Revenu (IGR)</td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;"></td>
                                    <td class="text-right"style="border-color: #0c0c0c;border: 2px solid black;font-size: 12px"> <?php $imp_revenu	=	$result["imp_revenu"]; echo  number_format($imp_revenu); ?></td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;"> - </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;">  </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;">  </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;">  </td>
                                </tr>-->
                            <!-- <tr>
                                    <td style="border-color: #0c0c0c;border: 2px solid black;font-size: 12px">Cont. Recons. Nat. (CRNS)</td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;"></td>
                                    <td class="text-right"style="border-color: #0c0c0c;border: 2px solid black;font-size: 12px"> <?php $cmu	=	$result["contra_nat"]; echo  number_format($prime_anc); ?></td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;"> -</td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;">  </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;">  </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;">  </td>
                                </tr>-->
                            <tr>
                                <td style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">CMU</td>

                                <th style="border: 1px solid black; font-size: 12px" class="text-right"><?php $cmus	= $result["part_igr"] * "500" ; echo  number_format($cmus, 0, '', '.'); ?></th>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>

                                <th class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"><?php $cmu	= $result["part_igr"] * 500 ; echo  number_format($cmu, 0, '', '.'); ?></th>

                            </tr>
                            <tr>
                                <td style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">CNPS, Régime de Retraite</td>
                                <th class="text-right" style="border-color: #0c0c0c;border: 1px solid black; font-size: 12px"><?php $retraite	= $total_social ; echo  number_format($retraite, 0, '', '.'); ?></th>

                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">6,30</td>

                                <!--<td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;font-size: 12px"><?php $cnps_regim	=	$result["cnps_regim"]; echo  number_format($cnps_regim); ?>  </td>-->
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>

                                <th style="border: 1px solid black; font-size: 12px" class="text-right"><?php $retrai_regime	= $retraite * 0.0630 ; echo  number_format($retrai_regime, 0, '', '.'); ?></th>




                                <td  class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">7,70</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>


                                <th class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"><?php $retrait	= $total_social * 0.077; echo  number_format($retrait, 0, '', '.'); ?></th>

                            </tr>
                            <tr>
                                <td style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">CNPS, Accident Travail
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"><?php $cnps_tra	=	$categorie_salaire; echo  number_format($cnps_tra, 0, '', '.'); ?>  </td>

                                <td class="text-right"></td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"> - </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">3,00</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"> </td>


                                <th class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"><?php $travail	=  $categorie_salaire * 0.03; echo  number_format($travail, 0, '', '.'); ?></th>

                            </tr>
                            <tr>
                                <td style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">CNPS, Prest. Famil.</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"><?php $cnps_famille	=	$categorie_salaire; echo  number_format($cnps_famille, 0, '', '.'); ?>  </td>

                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"></td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"> - </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">5,75</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>


                                <th style="border-color: #0c0c0c;border: 1px solid black;" class="text-right"><?php $famille	= $categorie_salaire * 0.0575 ; echo  number_format($famille, 0, '', '.'); ?></th>

                            </tr>
                            <tr>
                                <td style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">FDFP, Taxe Apprentissage</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"><?php $fdfp_tax	=	$total_brute; echo  number_format($fdfp_tax, 0, '', '.'); ?>  </td>

                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"></td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"> -</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">0,75  </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>


                                <th class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"><?php $taxe	= $total_brute * 0.0075 ; echo  number_format($taxe, 0, '', '.'); ?></th>

                            </tr>
                            <tr>
                                <td style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">FDFP, Form. Prof. Continue</td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"><?php $fdfp_continue	=	$total_brute; echo  number_format($fdfp_continue, 0, '', '.'); ?>  </td>

                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"></td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px"> </td>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">-  </td>
                                <td class="text-right"> 1,20</td>
                                <td class="text-right" style="border: 1px solid black;">  </td>


                                <th class="text-right" style="border: 1px solid black;"><?php $tax	= $total_brute * 0.012 ; echo  number_format($tax, 0, '', '.'); ?></th>

                            </tr>
                            <!--<tr>
                                    <td style="border-color: #0c0c0c;border: 2px solid black;font-size: 12px">Avance/Acompte</td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;font-size: 12px"><?php $avan_acom	=	$result["avan_acom"]; echo  number_format($avan_acom); ?></td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;font-size: 12px">  </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;"> - </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;">  </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;">  </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;">  </td>
                                </tr>-->
                            <tr>
                                <th style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">Autres retenues</th>
                                <th class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"><?php $autre_reve	=	$result["autre_reve"]; echo  number_format($autre_reve, 0, '', '.'); ?></th>
                                <th class="text-right" style="border-color: #0c0c0c;border: 1px solid black;font-size: 12px">  1</th>
                                <th class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"> - </th>
                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;"><?php $autre_reve	=	$result["autre_reve"]; echo  number_format($autre_reve, 0, '', '.'); ?></td>

                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>

                                <td class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </td>
                                <th class="text-right" style="border-color: #0c0c0c;border: 1px solid black;">  </th>
                            </tr>
                            <!--   <tr>
                                    <td style="border-color: #0c0c0c;border: 2px solid black;font-size: 12px">Taxe</td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;"></td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;font-size: 12px"><?php echo $result["tax"] ?>  </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;"> - </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;">  </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;">  </td>
                                    <td class="text-right" style="border-color: #0c0c0c;border: 2px solid black;">  </td>
                                </tr>-->
                            <!--<tr>
									<td>Prêt</td>
									<td class="text-right"> </td>
									<td class="text-right"><?php if(!empty($salary_info->loan)) {
                                echo $salary_info->loan . " FCFA";
                            } ?> </td>
									<td class="text-right">  </td>
									<td class="text-right">  </td>
									<td class="text-right"> 10 </td>
									<td class="text-right">  </td>
								</tr>-->
                            <!--<tr>
									<td>Heures de travail (<?php echo $salary_info->total_days; ?> hrs)</td>
									<td class="text-right">
										<?php
                            if($a > 0) { echo round($a).' FCFA'; }
                            ?>
									</td>
									<td class="text-right">
										<?php
                            if($d > 0) { echo round($d).' FCFA'; }
                            ?>
									</td>
									<td class="text-right"> </td>
								</tr>-->
                            <!--<tr>
                                                <td>Without Pay( <?php echo $work_h_diff ?> hrs)</td>
                                                <td class="text-right"> </td>
                                                <td class="text-right"> <?php
                            /*if($d > 0) { echo round($d,2).' FCFA'; }*/
                            echo $salary_info->diduction .'FCFA';
                            ?> </td>

                                            </tr>-->
                            <!--<tr>
                                <td>Tax</td>
                                <td class="text-right">  </td>
                                <td class="text-right">  </td>
                                <td class="text-right">  </td>
                                <td class="text-right">  </td>
                                <td class="text-right">  </td>
                            </tr>-->

                            </tbody>
                            <tfoot style="border: 1px solid black; font-size: 12px" class="tfoot-light">
                            <tr >
                                <th style="border: 1px solid black; font-size: 12px"></th>
                                <th style="border: 1px solid black; font-size: 12px"></th>
                                <th style="border: 1px solid black; font-size: 12px"></th>
                                <th style="border: 1px solid black; font-size: 12px" class="text-right"><?php $total_gain	= $total_brute; echo  number_format($total_gain, 0, '', '.'); ?></th>

                                <th style="border: 1px solid black; font-size: 12px" class="text-right"><?php $tota_retenus	= $retrai_regime + $autre_reve + $impot; echo  number_format($tota_retenus, 0, '', '.'); ?></th>

                                <th style="border: 1px solid black; font-size: 12px" class="text-right"></th>
                                <th style="border: 1px solid black; font-size: 12px"></th>
                                <th style="border: 1px solid black; font-size: 12px" class="text-right"><?php $gross_salary	= $cmu + $retrait + $travail + $famille + $taxe + $tax ; echo  number_format($gross_salary, 0, '', '.'); ?></th>

                            </tr>

                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th style="border: 1px solid black; font-size: 12px">NET A PAYER</th>
                                <th class="text-right"> <?php $salaire_net	= $total_brute - $gross_salary; echo number_format($salaire_net, 0, '', '.');?> <?php echo $currency_symbol; ?></th>

                            </tr>
                            <tr style="border: 2px solid black; font-size: 12px">


                                <td align="center" hidden>
                                    <div style="position: absolute;left:15px"><?php $this->setting_model->get_payslipfooter(); ?> <p ></p></div>

                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        _____________________________________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      _____________________________________
                        <br>
                        Signature employée &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                            Signature employeur


                    </div>

                </div><br/>
                <td style="font-size: 10px">Période du</td>
                <td style="font-size: 10px">:  <?php echo $result["date_from"] ?>  au   <?php echo $result["date_to"] ?></td>

                <!--<p style="text-align: center">Pour vous aider à faire valoir vos droits, conservez ce bulletin  de paie sans limitation de durée.</p>-->
            </div>
            <!-- <div class="card card-body printableArea">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="pull-left " style="height:80px;margin-left:10px;">
                                        <img src="<?php echo base_url();?>assets/images/dri_Logo.png" style="position:absolute; top:0; left:0;width:250px;margin-left:15px;" />
                                    </div>
                                    <div class="pull-right">
                                        <h4 class="pull-right">Pay Slip for the period of <?php echo $salary_info->month;?> 2018</h4>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="pull-left">
                                        <address>
                                            <p class="text-muted m-l-5">Employee PIN: <?php echo $employee_info->em_code;?>
                                                <br/> Department:  <?php echo $employee_info->dep_name;?>
                                                <br/> Payment Date: <?php echo $salary_info->paid_date;?></p>
                                        </address>
                                    </div>
                                    <div class="pull-right text-right">
                                        <address>
                                            <p class="text-muted m-l-30">Employee Name:  <?php echo $employee_info->first_name .' '. $employee_info->last_name;?>
                                                <br/> Designation:   <?php echo $employee_info->des_name;?>
                                                <br/> Month:  <?php echo $salary_info->month;?></p>
                                        </address>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="table-responsive" style="clear: both;">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Description</th>
                                                    <th class="text-right">Earning</th>
                                                    <th class="text-right">Deduction</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Basic Salary</td>
                                                    <td class="text-right"> <?php echo $salaryvaluebyid->basic;?> USD</td>
                                                    <td class="text-right">  </td>
                                                </tr>
                                                <tr>
                                                    <td>Madical</td>
                                                    <td class="text-right"> <?php echo $salaryvaluebyid->medical;?> USD </td>
                                                    <td class="text-right">  </td>
                                                </tr>
                                                <tr>
                                                    <td>House Rent</td>
                                                    <td class="text-right"> <?php echo $salaryvaluebyid->house_rent;?> USD </td>
                                                    <td class="text-right">  </td>
                                                </tr>
                                                <tr>
                                                    <td>Conveyance</td>
                                                    <td class="text-right"> <?php echo $salaryvaluebyid->conveyance;?> USD </td>
                                                    <td class="text-right">  </td>
                                                </tr>
                                                <tr>
                                                    <td>Loan</td>
                                                    <td class="text-right"> </td>
                                                    <td class="text-right"><?php echo $salary_info->loan;?>  USD</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Total</th>
                                                    <th class="text-right"><?php echo $salaryvaluebyid->total;?> USD</th>
                                                    <th class="text-right"><?php echo $salary_info->diduction;?>  USD</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="pull-right m-t-30 text-right">
                                        <h3><b>Total :</b>  <?php echo $salary_info->total_pay;?> USD</h3>
                                    </div>
                                    <div class="clearfix"></div>
                                    <hr>
                                    <div class="text-right">
                                        <button id="print" class="btn btn-default btn-outline" type="button"> <span><i class="fa fa-print"></i> Print</span> </button>
                                    </div>
                                </div>
                            </div>
                        </div> -->
        </div>
    </div>



</html>