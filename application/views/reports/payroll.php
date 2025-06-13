<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

?>
<?php
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
    'December' => 'Décembre',
];


?>
<?php



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

    <section class="content-header">
        <h1>
            <i class="fa fa-bus"></i> <?php echo $this->lang->line('transport'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('reports/_finance'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>

                    <form role="form" action="<?php echo site_url('report/payroll') ?>" method="post" class="">
                        <div class="box-body row">

                            <?php echo $this->customlib->getCSRF(); ?>

                            <div class="col-sm-6 col-md-3" >
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('search') . " " . $this->lang->line('type'); ?></label>
                                    <select class="form-control" name="search_type" onchange="showdate(this.value)">

                                        <?php foreach ($searchlist as $key => $search) {
                                            ?>
                                            <option value="<?php echo $key ?>" <?php
                                            if ((isset($search_type)) && ($search_type == $key)) {

                                                echo "selected";
                                            }
                                            ?>><?php echo $search ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('search_type'); ?></span>
                                </div>
                            </div>

                            <div id='date_result'>

                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>


                    <div class="">
                        <div class="box-header ptbnull"></div>
                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><i class="fa fa-money"></i> Livre de paie</h3>
                        </div>
                        <div class="box-body table-responsive">
                            <div class="download_label">
                                <div class="col-md-4 col-xs-2 col-sm-6">
                                    <img style="width: 150px; height: 70px !important;" src="<?= base_url() . "/uploads/school_content/admin_logo/" . $sch_setting->admin_logo ?>" alt="Image banniere" />
                                </div>
                                <br/><br/><br/><br/>
                                <?php
                                echo "Livre de paie <br/><br/>";
                                echo "period:"; $this->customlib->get_postmessage();
                                ;
                                ?></div>


                            <table class="table table-striped table-bordered table-hover example"/>

                            <thead>
                            <tr>


                                <th>Matricule</th>

                                <th><?php echo $this->lang->line('name'); ?> et prénom</th>
                                <th>Total Gains</th>
                                <!--<th><?php echo $this->lang->line('role'); ?></th>
                                    <th><?php echo $this->lang->line('designation'); ?></th>-->
                                <!--<th><?php echo $this->lang->line('month'); ?> - <?php echo $this->lang->line('year') ?></th>-->

                                <!--<th><?php echo $this->lang->line('payslip'); ?> #</th>-->
                                <th><?php echo $this->lang->line('basic_salary'); ?> </th>

                                <!---<th class="text text-right"><?php echo $this->lang->line('earning'); ?></th>-->
                                <!-- <th class="text text-right"> </th>-->
                                <!--<th>Autre revenu</th>-->
                                <th>Cnps</th>
                                <th>Cmu</th>
                                <th>ITS</th>
                                <th>Total revenu</th>
                                <th><?php echo $this->lang->line('net_salary'); ?></th>
                            </tr>
                            </thead>
                            <tbody>


                            <?php
                            $basic = 0;
                            $salaire_brute = 0;
                            $total_rev = 0;
                            $autre_r = 0;
                            $gross = 0;
                            $gros = 0;
                            $cmu = 0;
                            $cnps = 0;
                            $its = 0;
                            $g = 0;
                            $net = 0;
                            $earnings = 0;
                            $deduction = 0;
                            $tax = 0;

                            if (empty($payrollList)) {
                                ?>

                                <?php
                            } else {
                                $count = 1;

                                foreach ($payrollList as $key => $value) {


                                    $basic += $value["categorie_salaire"];
                                    $salaire_brute += $value["cnps_regim"];
                                    $total_rev += $value["total_revenu"];
                                    $gross += $value["gross_salary"];
                                    $gros += $tt;
                                    $cmu += $value["cmu"];
                                    $its += $value["its"];
                                    $cnps += $value["cnps_regim"];
                                    $g += $y;
                                    $autre_r += $value["autre_reve"];
                                    $net += $value["net_salary"];

                                    $earnings += $value["total_allowance"];
                                    $deduction += $value["total_deduction"];
                                    if ($value["tax"] != '') {
                                        $taxdata = $value["tax"];
                                    } else {
                                        $taxdata = 0;
                                    }
                                    $tax += $taxdata;
                                    $total = 0;
                                    $grd_total = 0;
                                    ?>
                                    <tr>

                                        <td style="text-transform: capitalize;">
                                            <span data-toggle="popover" class="detail_popover" data-original-title="" title=""><a href="<?php echo base_url() ?>admin/staff/profile/<?php echo $value['staff_id']; ?>"><?php echo $value['employee_id']; ?></a></span>

                                        </td>
                                        <td style="text-transform: capitalize;">
                                            <span data-toggle="popover" class="detail_popover" data-original-title="" title=""><a href="<?php echo base_url() ?>admin/staff/profile/<?php echo $value['staff_id']; ?>"><?php echo $value['name'] . " " . $value['surname'].")"; ?></a></span>

                                        </td>
                                        <td>
                                            <?php echo $value['gross_salary']; ?>
                                        </td>
                                        <!-- <td>
                                            <?php echo $value['user_type']; ?>
                                        </td>-->
                                        <!--<td>
                                                    <span  data-original-title="" title=""><?php
                                        echo $value['designation'];
                                        ;
                                        ?></span>

                                        </td>-->
                                        <!-- <td>

                                            <?php echo $value['month']= $moisEn[$value['month']] . " - " . $value['year']; ?>
                                        </td>-->
                                        <!-- <td>

                                            <span data-toggle="popover" class="detail_popover" data-original-title="" title=""><a href="#"><?php echo $value['id']; ?></a></span>
                                            <div class="fee_detail_popover" style="display: none"><?php echo $this->lang->line('mode'); ?>: <?php
                                        if (array_key_exists($value["payment_mode"], $payment_mode)) {
                                            echo $payment_mode[$value["payment_mode"]];
                                        }
                                        ?></div>

                                        </td>-->
                                        <td>
                                            <?php echo number_format($value['categorie_salaire'], 2, '.', ''); ?>
                                        </td>

                                        <!-- <td class="text text-right">
                                            <?php echo (number_format($value['total_allowance'], 2, '.', '')); ?>
                                        </td>-->

                                        <!--<td class="text text-right">
                                            <?php echo number_format(($value['gross_salary'] * 0.0630) + $value['autre_reve'] , 2, '.', ''); ?>
                                        </td>-->


                                        <td>
                                            <?php echo $value['cnps_regim']; ?>
                                        </td>
                                        <td>
                                            <?php echo $value['cmu']; ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo  $value['its']

                                            ?>
                                        </td>
                                        <!--<td class="text text-right">
                                            <?php
                                        $impot = 0;

                                        if ($value['categorie_salaire'] > 8000000) {
                                            $impot += ($value['categorie_salaire'] - 8000000) * 0.32;
                                            $value['categorie_salaire'] = 8000000;
                                        }

                                        if ($value['categorie_salaire'] > 2400000) {
                                            $impot += ($value['categorie_salaire'] - 2400000) * 0.28;
                                            $value['categorie_salaire'] = 2400000;
                                        }

                                        if ($value['categorie_salaire'] > 800000) {
                                            $impot += ($value['categorie_salaire'] - 800000) * 0.24;
                                            $value['categorie_salaire'] = 800000;
                                        }

                                        if ($value['categorie_salaire'] > 240000) {
                                            $impot += ($value['categorie_salaire'] - 240000) * 0.21;
                                            $value['categorie_salaire'] = 240000;
                                        }

                                        if ($value['categorie_salaire'] > 75000) {
                                            $impot += ($value['categorie_salaire'] - 70000) * 0.16;
                                            $value['categorie_salaire'] = 75000;
                                        }

                                        echo $tt = $impot + ($value['gross_salary'] * 0.0630) + $value['autre_reve'];

                                        ?>
                                        </td>-->
                                        <td>
                                            <?php echo $value['total_revenu']; ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $value["net_salary"];

                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                    $count++;
                                }
                                ?>
                                <tr class="box box-solid total-bg">

                                    <!-- <td class="text text-right"><?php echo ($currency_symbol . number_format($earnings, 2, '.', '')); ?></td>-->
                                    <td></td>



                                    <td><?php echo $this->lang->line('grand_total'); ?> </td>
                                    <td><?php echo (number_format($gross, 2, '.', '')); ?></td>
                                    <td><?php echo (number_format($basic, 2, '.', '')); ?></td>
                                    <td><?php echo (number_format($cnps, 2, '.', '')); ?></td>

                                    <td><?php echo (number_format($cmu, 2, '.', '')); ?></td>

                                    <td><?php echo (number_format($its, 2, '.', '')); ?></td>
                                    <td><?php echo (number_format($total_rev, 2, '.', '')); ?></td>
                                    <td><?php echo (number_format($net, 2, '.', '')); ?></td>

                                </tr>
                            <?php } ?>


                            </tbody>



                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
</section>
</div>

<script>
    <?php
    if ($search_type == 'period') {
    ?>

    $(document).ready(function () {
        showdate('period');
    });

    <?php
    }
    ?>

</script>