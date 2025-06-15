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

                    <form role="form" action="<?php echo site_url('report/charge_cnps_cmu_em') ?>" method="post" class="">
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
                            <h3 class="box-title titlefix"><i class="fa fa-money"></i><b style="color: black">CHARGE CNPS & CMU EMPLOYEUR</b></h3>
                        </div>
                        <div class="box-body table-responsive">
                            <div class="download_label">
                                <div class="col-md-4 col-xs-2 col-sm-6">
                                    <img style="width: 150px; height: 70px !important;" src="<?= base_url() . "/uploads/school_content/admin_logo/" . $sch_setting->admin_logo ?>" alt="Image banniere" />
                                </div>
                                <br/><br/><br/><br/>
                                <?php
                                echo "CHARGE CNPS & CMU EMPLOYEUR <br/><br/>";
                                echo "period:"; $this->customlib->get_postmessage();
                                ;
                                ?></div>


                            <table class="table table-striped table-bordered table-hover example"/>

                            <thead>
                            <tr>


                                <th class="text-primary">Matricule</th>
                                <th class="text-primary">Nom</th>
                                <th class="text-primary">Prénom</th>
                                <th class="text-primary">Cmu</th>
                                <th class="text-primary">Cnps régime de travaile</th>
                                <th class="text-primary">Cnps prestation</th>
                                <th class="text-primary">Cnps travail</th>

                                <th class="text-primary">Fdfp, Taxe apprentissage</th>
                                <th class="text-primary">Fdfp, Form pro Continue</th>

                            </tr>
                            </thead>
                            <tbody>


                            <?php

                            $cnps_regim = 0;
                            $cnps_tra = 0;
                            $cnps_pres = 0;
                            $cmu = 0;
                            $fdfp_taxe = 0;
                            $fdfp_form = 0;


                            if (empty($payrollList)) {
                                ?>

                                <?php
                            } else {
                                $count = 1;

                                foreach ($payrollList as $key => $value) {

                                    $cnps_regim += $value["cnps_regim"];
                                    $cnps_tra += $value["cnps_tra"];
                                    $cnps_pres += $value["cnps_pres"];
                                    $cmu += $value["cmu"];
                                    $fdfp_taxe += $value["fdfp_taxe"];
                                    $fdfp_form += $value["fdfp_form"];
                                    ?>
                                    <tr>

                                        <td>
                                            <span data-toggle="popover" class="detail_popover" data-original-title="" title=""><a href="<?php echo base_url() ?>admin/staff/profile/<?php echo $value['staff_id']; ?>"><?php echo $value['employee_id']; ?></a></span>

                                        </td>
                                        <td>
                                            <span data-toggle="popover" class="detail_popover" data-original-title="" title=""><a href="<?php echo base_url() ?>admin/staff/profile/<?php echo $value['staff_id']; ?>"><?php echo $value['surname']; ?></a></span>

                                        </td>
                                    <td>
                                        <span data-toggle="popover" class="detail_popover" data-original-title="" title=""><a href="<?php echo base_url() ?>admin/staff/profile/<?php echo $value['staff_id']; ?>"><?php echo $value['name']; ?></a></span>

                                    </td>
                                        <td>
                                            <?php
                                            echo  $value['cmu']

                                            ?>
                                        </td>

                                    <td>
                                        <?php
                                        echo  $value['cnps_regim']

                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo  $value['cnps_pres']

                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        echo  $value['cnps_tra']

                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo  $value['fdfp_taxe']

                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo  $value['fdfp_form']

                                        ?>
                                    </td>

                                    <?php
                                    $count++;
                                }
                                ?>
                                <tr class="box box-solid total-bg">
                                    <td></td>
                                    <td></td>





                                    <td><?php echo $this->lang->line('grand_total'); ?> </td>

                                    <td><?php echo (number_format($cmu, 2, '.', '')); ?></td>

                                    <td><?php echo (number_format($cnps_regim, 2, '.', '')); ?></td>
                                    <td><?php echo (number_format($cnps_pres, 2, '.', '')); ?></td>

                                    <td><?php echo (number_format($cnps_tra, 2, '.', '')); ?> </td>

                                    <td><?php echo (number_format($fdfp_taxe, 2, '.', '')); ?></td>
                                    <td><?php echo (number_format($fdfp_form, 2, '.', '')); ?></td>

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