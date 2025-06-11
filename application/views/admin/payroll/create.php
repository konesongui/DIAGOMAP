<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>


<div class="content-wrapper" style="min-height: 393px;">
    <section class="content-header">
        <h1><i class="fa fa-sitemap"></i> <?php echo $this->lang->line('human_resource'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h3 class="box-title"><?php echo $this->lang->line('staff'); ?> <?php echo $this->lang->line('details'); ?></h3>
                            </div>
                            <div class="col-md-8 ">
                                <div class="btn-group pull-right">
                                    <a href="<?php echo base_url() ?>admin/payroll" type="button" class="btn btn-primary btn-xs">
                                        <i class="fa fa-arrow-left"></i> </a>
                                </div>
                            </div>
                        </div>
                    </div><!--./box-header-->
                    <div class="box-body" style="padding-top:0;">
                        <div class="row">
                            <div class="col-md-8 col-sm-12">
                                <div class="sfborder">
                                    <div class="col-md-2">
                                        <div class="row">
                                            <?php
                                            $image = $result['image'];
                                            if (!empty($image)) {

                                                $file = $result['image'];
                                            } else {

                                                $file = "no_image.png";
                                            }
                                            ?>
                                            <img width="115" height="115" class="round5" src="<?php echo base_url() . "uploads/staff_images/" . $file ?>" alt="No Image">
                                        </div>
                                    </div>

                                    <div class="col-md-10">
                                        <div class="row">
                                            <table class="table mb0 font13">
                                                <tbody>
                                                <tr>
                                                    <th class="bozero"><?php echo $this->lang->line("name"); ?></th>
                                                    <td class="bozero"><?php echo $result["name"] . " " . $result["surname"] ?></td>
                                                    <th class="bozero"><?php echo $this->lang->line('staff_id'); ?></th>
                                                    <td class="bozero"><?php echo $result["employee_id"] ?></td>
                                                </tr>
                                                <tr>
                                                    <?php if ($sch_setting->staff_phone) { ?>
                                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                                    <?php } ?>
                                                    <td><?php echo $result["contact_no"] ?></td>
                                                    <th><?php echo $this->lang->line('email'); ?></th>
                                                    <td><?php echo $result["email"] ?>                                   </td>
                                                </tr>
                                                <tr>
                                                    <?php if ($sch_setting->staff_epf_no) { ?>
                                                        <th><?php echo $this->lang->line('epf_no'); ?></th>
                                                        <td><?php echo $result["epf_no"] ?></td>
                                                    <?php } ?>
                                                    <th><?php echo $this->lang->line('role'); ?></th>
                                                    <td><?php echo $result["user_type"] ?></td>
                                                </tr>
                                                <tr>
                                                    <?php if ($sch_setting->staff_department) { ?>
                                                        <th><?php echo $this->lang->line('department'); ?></th>
                                                        <td><?php echo $result["department"] ?></td>
                                                    <?php } if ($sch_setting->staff_designation) { ?>
                                                        <th><?php echo $this->lang->line('designation'); ?></th>
                                                        <td><?php echo $result["designation"] ?>   </td>
                                                    <?php } ?>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div></div><!--./col-md-8-->
                            <div class="col-md-4 col-sm-12">

                                <div class="sfborder relative overvisible">
                                    <div class="letest">
                                        <div class="rotatetest"><?php echo $this->lang->line("attendance") ?></div>
                                    </div>
                                    <div class="padd-en-rtl33">
                                        <table class="table mb0 font13" >
                                            <tr>
                                                <th  class="bozero"><?php echo $this->lang->line('month'); ?></th>
                                                <?php foreach ($attendanceType as $key => $value) { ?>
                                                    <th class="bozero"><span data-toggle="tooltip" title="<?php echo $value["type"]; ?>"><?php echo strip_tags($value["key_value"]); ?></span></th>
                                                <?php }
                                                ?>

                                                <th class="bozero"><span data-toggle="tooltip" title="<?php echo $this->lang->line('approved'); ?> <?php echo $this->lang->line('leave'); ?>">V</span></th>
                                            </tr>
                                            <?php
                                            foreach ($monthAttendance as $attendence_key => $attendence_value) {
                                                ?><tr>
                                                <td><?php echo date("F", strtotime($attendence_key)); ?></td>
                                                <td><?php echo $attendence_value['présent'] ?></td>
                                                <td><?php echo $attendence_value['en retard']; ?></td>
                                                <td><?php echo $attendence_value['absent']; ?></td>
                                                <td><?php echo $attendence_value['demi-journée']; ?></td>
                                                <td><?php echo $attendence_value['congé']; ?></td>
                                                <td><?php echo $monthLeaves[date("m", strtotime($attendence_key))]; ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr>


                                            </tr>

                                        </table>
                                    </div>
                                </div>

                            </div>
                            <!--./col-md-8-->
                            <div class="col-md-12">
                                <div style="background: #dadada; height: 1px; width: 100%; clear: both; margin-bottom: 10px;"></div>
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <form class="form-horizontal" action="<?php echo site_url('admin/payroll/payslip') ?>" method="post"  id="employeeform">
                        <div class="box-header">
                            <div class="row display-flex">
                                <!-- <div class="col-md-4 col-sm-4">
                                    <h3 class="box-title"><?php echo $this->lang->line('earning'); ?></h3>
                                    <button type="button" onclick="add_more()" class="plusign"><i class="fa fa-plus"></i></button>
                                    <div class="sameheight">
                                        <div class="feebox">
                                            <table class="table3" id="tableID">
                                                <tr id="row0">
                                                    <td><input type="text" class="form-control" id="allowance_type" name="allowance_type[]" placeholder="Type"></td>
                                                    <td><input type="text" id="allowance_amount" name="allowance_amount[]" class="form-control" value="0"></td>

                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>--><!--./col-md-4-->
                                <!-- <div class="col-md-4 col-sm-4">

                                    <h3 class="box-title"><?php echo $this->lang->line('deduction'); ?></h3>
                                    <button type="button" onclick="add_more_deduction()" class="plusign"><i class="fa fa-plus"></i></button>
                                    <div class="sameheight">
                                        <div class="feebox">
                                            <table class="table3" id="tableID2">
                                                <tr id="deduction_row0">
                                                    <td><input type="text" id="deduction_type" name="deduction_type[]" class="form-control" placeholder="Type"></td>
                                                    <td><input type="text" id="deduction_amount" name="deduction_amount[]" class="form-control" value="0"></td>

                                                </tr>

                                            </table>
                                        </div>
                                    </div>
                                </div>--><!--./col-md-4-->
                                <div class="col-md-4 col-sm-4">

                                    <h3 class="box-title"><?php echo $this->lang->line('payroll'); ?> <?php echo $this->lang->line('summary'); ?>(<?php echo $currency_symbol ?>)</h3>
                                    <!--<button onclick="calculerImpot()">Calculer</button>-->
                                    <button type="button" onclick="add_allowance()" class="plusign"><i class="fa fa-calculator"></i> <?php echo $this->lang->line('calculate'); ?></button>

                                    <div class="sameheight">
                                        <div class="payrollbox feebox">
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('basic_salary'); ?></label>
                                                <div class="col-sm-8" hidden>
                                                    <input class="form-control" name="basic" value="<?php
                                                    if (!empty($result["basic_salary"])) {
                                                        echo $result["basic_salary"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="basic"  type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Catégorie salariale</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="categorie_salaire"  value="<?php
                                                    if (!empty($result["categorie_salaire"])) {
                                                        echo $result["categorie_salaire"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="salaire"  type="text" />

                                                </div>
                                            </div>
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Sursalaire</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="sursalaire"  value="<?php
                                                    if (!empty($result["sursalaire"])) {
                                                        echo $result["sursalaire"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="sursalaire"  type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Prime Ancien</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="prime_anc"  value="<?php
                                                    if (!empty($result["prime_anc"])) {
                                                        echo $result["prime_anc"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="prime_anc"  type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Prime Imdémités</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="taxes"  value="<?php
                                                    if (!empty($result["taxes"])) {
                                                        echo $result["taxes"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="taxes"  type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('earning'); ?></label>
                                                <div class="col-sm-8">
                                                    <input class="form-control"  value="<?php
                                                    if (!empty($result["total_allowance"])) {
                                                        echo $result["total_allowance"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" name="total_allowance" id="total_allowance"  type="text" />
                                                </div>
                                            </div><!--./form-group-->
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Prime Transport</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="prime_trans"  value="<?php
                                                    if (!empty($result["prime_trans"])) {
                                                        echo $result["prime_trans"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="prime_trans"  type="text" />
                                                </div>
                                            </div>


                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Forfait Hs</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="forfait_hs"  value="<?php
                                                    if (!empty($result["forfait_hs"])) {
                                                        echo $result["forfait_hs"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="forfait_hs"  type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Prime Resp</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="prime_resp"  value="<?php
                                                    if (!empty($result["prime_resp"])) {
                                                        echo $result["prime_resp"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="prime_resp"  type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Prime Rend</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="prime_rend"  value="<?php
                                                    if (!empty($result["prime_rend"])) {
                                                        echo $result["prime_rend"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="prime_rend"  type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Prime Risque</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="prime_risque"  value="<?php
                                                    if (!empty($result["prime_risque"])) {
                                                        echo $result["prime_risque"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="prime_risque"  type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Prime Assi</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="prime_assi" value="<?php
                                                    if (!empty($result["prime_assi"])) {
                                                        echo $result["prime_assi"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="prime_assi"  type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Prime Grati</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="prime_grati" value="<?php
                                                    if (!empty($result["prime_grati"])) {
                                                        echo $result["prime_grati"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="prime_grati"  type="text" />
                                                </div>
                                            </div>

                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Congé</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="conge" value="<?php
                                                    if (!empty($result["conge"])) {
                                                        echo $result["conge"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="conge"  type="text" />
                                                </div>
                                            </div>

                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">PRIME T</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="primet" value="<?php
                                                    if (!empty($result["conge"])) {
                                                        echo $result["conge"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="prime"  type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Imp. sur Salaire</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="imp_sal" value="0" id="imp_sal"  type="text" />
                                                </div>
                                            </div>

                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Imp. sur Trait. et Sal. (IS) Employé</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="imp_sal" value="0" id="imp_sal"  type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">contra_nat</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="contra_nat" value="0" id="contra_nat"  type="text" />
                                                </div>
                                            </div>


                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Cmu</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="cmu" id="cmu_s" value="0" type="text" />
                                                </div>
                                            </div><!--./form-group-->
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">CNPS, Regime Retraite EMPLOYE</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="cnps_regims" value="0" id="regime_em"  type="text" />
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">CNPS, Regime Retraite EMPLOYEUR</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="cnps_regim" value="0" id="regime_s"  type="text" />
                                                </div>
                                            </div>

                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">CNPS, Travail</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="cnps_tra" value="0" id="cnps_tra"  type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">CNPS, Prest Famille</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="cnps_pres" value="0" id="cnps_pres"  type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">FDFP Tax Apprentissage</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="fdfp_taxe" value="<?php
                                                    if (!empty($result["fdfp_taxe"])) {
                                                        echo $result["fdfp_taxe"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="fdfp_taxe"  type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">FDFP Form Prof Continue</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="fdfp_form" value="<?php
                                                    if (!empty($result["fdfp_form"])) {
                                                        echo $result["fdfp_form"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="fdfp_form"  type="text" />
                                                </div>
                                            </div>






                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Part IGR</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="part_igr" value="<?php
                                                    if (!empty($result["part_igr"])) {
                                                        echo $result["part_igr"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="part_igr"  type="text" />
                                                </div>
                                            </div>

                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">avan_acom</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="avan_acom" id="avance_acom" value="<?php
                                                    if (!empty($result["avan_acom"])) {
                                                        echo $result["avan_acom"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="avan_acom"  type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Autre Revenu</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="autre_reve" id="autre_reve" value="<?php
                                                    if (!empty($result["autre_reve"])) {
                                                        echo $result["autre_reve"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="autre_reve"  type="text" />
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">ITS</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="its" id="total_impot" value="0"  type="text" />
                                                </div>
                                            </div>

                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Bonus</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="bonus" id="bonus" value="<?php
                                                    if (!empty($result["bonus"])) {
                                                        echo $result["bonus"];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>" id="bonus"  type="text" />
                                                </div>
                                            </div>


                                            <!--./form-group-->

                                            <!--<div class="form-group">
                                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('deduction'); ?></label>
                                                <div class="col-sm-8 deductiondred">
                                                    <input class="form-control" name="total_deduction" id="total_deduction" type="text" style="color:#f50000" />
                                                </div>
                                            </div>--><!--./form-group-->

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Total brute</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="gross_salary" id="gross_salary"  type="text" />
                                                </div>
                                            </div><!--./form-group-->
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Total brute Fiscal</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="gross" id="sj" value="0" type="text" />
                                                </div>
                                            </div><!--./form-group-->
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Total brute Social</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="gross_social" id="total_social" value="0" type="text" />
                                                </div>
                                            </div><!--./form-group-->
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Total revenu</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="total_revenu" id="total_revenu" value="0" type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label">Cnps</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" name="total" id="cnps" value="0" type="text" />

                                                    <p id="result"></p>

                                                </div>
                                            </div><!--./form-group-->
                                            <!-- <div class="form-group">
                                                 <label class="col-sm-4 control-label">Regime</label>
                                                 <div class="col-sm-8">
                                                     <input class="form-control" name="regim" id="gross_regime" value="0" type="text" />
                                                 </div>
                                             </div>--><!--./form-group-->
                                            <div class="form-group" hidden>
                                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('tax'); ?></label>
                                                <div class="col-sm-8 deductiondred">
                                                    <input class="form-control" name="tax" id="tax" value="0" type="text" />
                                                </div>
                                            </div><!--./form-group-->

                                            <hr/>

                                            <div class="form-group">
                                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('net_salary'); ?></label>
                                                <div class="col-sm-8 net_green">
                                                    <input class="form-control greentest"  name="net_salary" id="net_salary"  type="text" />
                                                    <span class="text-danger" id="err"><?php echo form_error('net_salary'); ?></span>

                                                    <input class="form-control" name="staff_id" value="<?php echo $result["id"]; ?>"  type="hidden" />

                                                    <input class="form-control" name="month" value="<?php echo $month; ?>"  type="hidden" />
                                                    <input class="form-control" name="year" value="<?php echo $year; ?>"  type="hidden" />

                                                    <input class="form-control" name="status" value="generated"  type="hidden" />

                                                </div>
                                            </div><!--./form-group-->
                                        </div>
                                    </div>
                                </div><!--./col-md-4-->
                                <div class="col-md-12 col-sm-12">

                                    <button type="submit" id="contact_submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                </div><!--./col-md-12-->
                    </form>
                </div><!--./row-->
            </div><!--./box-header-->
        </div>
</div>
<!--/.col (left) -->
</div>
</section>
</div>

<script type="text/javascript">


    function add_allowance() {

        var basic_pay = $("#categorie_salaire").val();
        var sursalaire = $("#sursalaire").val();
        var prime_anc = $("#prime_anc").val();
        var allocance = $("#allocance").val();
        var prime_trans = $("#prime_trans").val();
        var forfait_hs = $("#forfait_hs").val();
        var autre_reve = $("#autre_reve").val();
        var prime_resp = $("#prime_resp").val();
        var prime_rend = $("#prime_rend").val();
        var prime_risque = $("#prime_risque").val();
        var prime_assi = $("#prime_assi").val();
        var prime_grati = $("#prime_grati").val();
        var conge = $("#conge").val();
        var partigr = $("#part_igr").val();
        var allowance_type = document.getElementsByName('allowance_type[]');
        var allowance_amount = document.getElementsByName('allowance_amount[]');
        //var leave_deduction = $("#leave_deduction").val();
        var tax = $("#tax").val();
        if (tax == '') {

        }

        var total_allowance = 0;
        var cmu = 500;
        var prime= 20000;




        var deduction_type = document.getElementsByName('deduction_type[]');
        var deduction_amount = document.getElementsByName('deduction_amount[]');

        var total_deduction = 0;

        for (var i = 0; i < allowance_amount.length; i++) {

            var inp = allowance_amount[i];

            if (inp.value == '') {

                var inpvalue = 0;
            } else {
                var inpvalue = inp.value;
            }

            total_allowance += parseFloat(inpvalue);

        }

        var basic_pay = parseFloat(document.getElementById("salaire").value);
        var impot = 0;
        var salaire = basic_pay;

        if (basic_pay > 8000000) {
            impot += (basic_pay - 8000000) * 0.32;
            salaire = 8000000;
        }

        if (basic_pay > 2400000) {
            impot += (basic_pay - 2400000) * 0.28;
            salaire = 2400000;
        }

        if (basic_pay > 800000) {
            impot += (basic_pay - 800000) * 0.24;
            salaire = 800000;
        }

        if (basic_pay > 240000) {
            impot += (basic_pay - 240000) * 0.21;
            salaire = 240000;
        }

        if (basic_pay > 75000) {
            impot += (basic_pay - 75000) * 0.16;
            salaire = 75000;
        }

        document.getElementById("result").innerText = "Impôt: " + impot.toFixed(2);



        for (var j = 0; j < deduction_amount.length; j++) {


            var inpd = deduction_amount[j];

            if (inpd.value == '') {

                var inpdvalue = 0;

            } else {

                var inpdvalue = inpd.value;
            }
            total_deduction += parseFloat(inpdvalue);
        }



//total_deduction += parseInt(leave_deduction) ;

        // var gross_salary = parseFloat(basic_pay) + parseFloat(total_allowance) - parseFloat(total_deduction);
        var gross_salary = parseFloat(basic_pay) + parseFloat(sursalaire) + parseFloat(prime_anc) + parseFloat(tax)  + parseFloat(prime_trans) + parseFloat(forfait_hs) + parseFloat(prime_resp) + parseFloat(prime_rend) + parseFloat(prime_risque) + parseFloat(prime_assi) + parseFloat(prime_grati);
        //var gross_social = parseFloat(basic_pay) + parseFloat(sursalaire) + parseFloat(prime_anc) + parseFloat(tax)  + parseFloat(prime_trans - prime) + parseFloat(forfait_hs) + parseFloat(prime_resp) + parseFloat(prime_rend) + parseFloat(prime_risque) + parseFloat(prime_assi) + parseFloat(prime_grati);
        //var gross_regime = parseFloat(gross_social) * "0.63";
        // var total_deduction= parseFloat(gross_regime) + parseFloat(autre_reve);
        var cmu_s = parseFloat(partigr) *  parseFloat(cmu);
        var tax = parseFloat(tax);
        var total_impot = parseFloat(impot);


        var prime = parseFloat(prime_trans) - parseFloat(prime);

        var total_social = parseFloat(gross_salary);
        var cnps = total_social * 0.0630;
        var cnps_acci = basic_pay * 0.03;
        var prest_famille = basic_pay * 0.0575;
        var taxe_apprend = gross_salary * 0.0075;
        var fdfp_form = gross_salary * 0.012;


        var total_revenu = parseFloat(cmu) + parseFloat(cnps) + parseFloat(cnps_acci) + parseFloat(prest_famille) + parseFloat(taxe_apprend) + parseFloat(fdfp_form);

        var regime_s = parseFloat(total_social) * 0.0630;
        var regime_em = parseFloat(total_social) * 0.077;
        var net_salary = parseFloat(gross_salary) - parseFloat(total_revenu);

        $("#total_allowance").val(total_allowance.toFixed(2));
        $("#cnps_acci").val(cnps_acci.toFixed(2));
        $("#prest_famille").val(prest_famille.toFixed(2));
        $("#taxe_apprend").val(taxe_apprend.toFixed(2));
        $("#fdfp_form").val(fdfp_form.toFixed(2));
        $("#total_deduction").val(total_deduction.toFixed(2));
        $("#total_allow").html(total_allowance.toFixed(2));
        //$("#total_deduc").html(total_deduction.toFixed(2));
        $("#gross_salary").val(gross_salary.toFixed(2));
        $("#cnps").val(cnps.toFixed(2));
        // $("#gross_social").val(gross_social.toFixed(2));
        // $("#gross_regime").val(gross_regime.toFixed(2));

        $("#cmu_s").val(cmu_s.toFixed(2));
        $("#total_revenu").val(total_revenu.toFixed(2));
        $("#tax").val(tax.toFixed(2));
        $("#prime").val(prime.toFixed(2));
        $("#regime_s").val(regime_s.toFixed(2));
        $("#regime_em").val(regime_em.toFixed(2));
        $("#total_impot").val(total_impot.toFixed(2));
        $("#total_social").val(total_social.toFixed(2));

        $("#net_salary").val(net_salary.toFixed(2));

    }
    function add_more() {

        var table = document.getElementById("tableID");
        var table_len = (table.rows.length);
        var id = parseInt(table_len);
        var row = table.insertRow(table_len).outerHTML = "<tr id='row" + id + "'><td><input type='text' class='form-control' id='allowance_type' name='allowance_type[]' placeholder='Type'></td><td><input type='text' class='form-control' id='allowance_amount' name='allowance_amount[]'  value='0'></td><td><button type='button' onclick='delete_row(" + id + ")' class='closebtn'><i class='fa fa-remove'></i></button></td></tr>";
    }

    function delete_row(id) {


        var table = document.getElementById("tableID");
        var rowCount = table.rows.length;
        $("#row" + id).html("");
//table.deleteRow(id);
    }


    function add_more_deduction() {

        var table = document.getElementById("tableID2");
        var table_len = (table.rows.length);
        var id = parseInt(table_len);
        var row = table.insertRow(table_len).outerHTML = "<tr id='deduction_row" + id + "'><td><input type='text' class='form-control' id='deduction_type' name='deduction_type[]' placeholder='Type'></td><td><input type='text' id='deduction_amount' name='deduction_amount[]' class='form-control' value='0'></td><td><button type='button' onclick='delete_deduction_row(" + id + ")' class='closebtn'><i class='fa fa-remove'></i></button></td></tr>";

    }

    function delete_deduction_row(id) {


        var table = document.getElementById("tableID2");
        var rowCount = table.rows.length;
        $("#deduction_row" + id).html("");
//table.deleteRow(id);
    }



    $("#contact_submit").click(function (event) {

        var net = $("#net_salary").val();
        if (net == "") {

            $("#err").html("<?php echo $this->lang->line('net_salary') . ' ' . $this->lang->line('should_not_be_empty'); ?>");
            $("#net_salary").focus();
            return false;
            event.preventDefault();
        } else {
            $("#err").html("");
        }
    });
</script>




