
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary border0 mb0 margesection">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('finance') ?></h3>

            </div>
            <div class="">
                <ul class="reportlists">

                    <!--<li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/finance/reportduefees'); ?>"><a href="<?php echo site_url('studentfee/reportduefees'); ?>"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('balance_fees_statement'); ?></a></li>-->

                    <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/finance/expense'); ?>"><a href="<?php echo base_url(); ?>report/caisse"><i class="fa fa-file-text-o"></i> Livre de caisse</a></li>

                    <?php
                    if ($this->rbac->hasPrivilege('fees_statement', 'can_view')) {
                        ?>
                        <!-- <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/finance/reportbyname'); ?>"><a href="<?php echo base_url(); ?>studentfee/reportbyname"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('fees_statement'); ?></a></li>-->
                        <?php
                    }
                    if ($this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
                        ?>

                        <!--<li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/finance/studentacademicreport'); ?>"><a href="<?php echo base_url(); ?>admin/transaction/studentacademicreport"><i class="fa fa-file-text-o"></i>
                                <?php echo $this->lang->line('balance_fees_report'); ?></a></li>-->
                        <?php
                    }
                    if ($this->rbac->hasPrivilege('fees_collection_report', 'can_view')) {
                        ?>

                        <!--<li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/finance/collection_report'); ?>"><a href="<?php echo base_url(); ?>studentfee/collection_report"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('fees') . " " . $this->lang->line('collection') . " " . $this->lang->line('report'); ?></a></li>-->
                    <?php } if ($this->rbac->hasPrivilege('online_fees_collection_report', 'can_view')) { ?>
                        <!--   <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/finance/onlinefees_report'); ?>"><a href="<?php echo base_url(); ?>report/onlinefees_report"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('online') . " " . $this->lang->line('fees') . " " . $this->lang->line('collection') . " " . $this->lang->line('report'); ?></a></li>-->
                        <?php
                    }
                    if ($this->rbac->hasPrivilege('income_report', 'can_view')) {
                        ?>
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/finance/income'); ?>"><a href="<?php echo base_url(); ?>report/income"><i class="fa fa-file-text-o"></i> Caisse</a></li>
                        <?php
                    }
                    if ($this->rbac->hasPrivilege('expense_report', 'can_view')) {
                        ?>
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/finance/expense'); ?>"><a href="<?php echo base_url(); ?>report/expense"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('expense'); ?></a></li>

                        <?php
                    }
                    if ($this->rbac->hasPrivilege('payroll_report', 'can_view')) {
                        ?>
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/finance/payroll'); ?>"><a href="<?php echo base_url(); ?>report/payroll"><i class="fa fa-file-text-o"></i> Livre de paie</a></li>
                        <?php
                    }

                    if ($this->rbac->hasPrivilege('payroll_report', 'can_view')) {
                        ?>
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/finance/charge_its'); ?>"><a href="<?php echo base_url(); ?>report/charge_its"><i class="fa fa-file-text-o"></i>Charge ITS</a></li>
                        <?php
                    }

                    if ($this->rbac->hasPrivilege('payroll_report', 'can_view')) {
                        ?>

                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/finance/charge_cnps_cmu'); ?>"><a href="<?php echo base_url(); ?>report/charge_cnps_cmu"><i class="fa fa-file-text-o"></i>Charge CNPS & CMU-Employé</a></li>

                        <?php
                    }

                    if ($this->rbac->hasPrivilege('payroll_report', 'can_view')) {
                        ?>

                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/finance/charge_cnps_cmu_em'); ?>"><a href="<?php echo base_url(); ?>report/charge_cnps_cmu_em"><i class="fa fa-file-text-o"></i>Charge CNPS & CMU-Employeur</a></li>

                        <?php
                    }

                    if ($this->rbac->hasPrivilege('payroll_report', 'can_view')) {
                        ?>

                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/finance/charge_fdfp'); ?>"><a href="<?php echo base_url(); ?>report/charge_fdfp"><i class="fa fa-file-text-o"></i>Charge FDFP</a></li>
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/finance/bilan_comptable'); ?>"><a href="<?php echo base_url(); ?>report/bilan_comptable"><i class="fa fa-file-text-o"></i>Bilan Comptable</a></li>

                        <?php
                    }
                    if ($this->rbac->hasPrivilege('income_group_report', 'can_view')) {
                        ?>
                        <!--<li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/finance/incomegroup'); ?>"><a href="<?php echo base_url(); ?>report/incomegroup"><i class="fa fa-file-text-o"></i> Caisse budgetaire par catégorie</a></li>-->
                        <?php
                    }
                    if ($this->rbac->hasPrivilege('expense_group_report', 'can_view')) {
                        ?>
                        <!-- <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/finance/expensegroup'); ?>"><a href="<?php echo base_url(); ?>report/expensegroup"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('expense') . " " . $this->lang->line('group') . " " . $this->lang->line('report'); ?></a></li>	-->
                        <?php
                    }
                    if ($this->rbac->hasPrivilege('online_admission', 'can_view')) {
                        ?>
                        <!--  <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/finance/onlineadmission'); ?>"><a href="<?php echo base_url(); ?>report/onlineadmission"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('online_admission') . " " . $this->lang->line('fees') . " " . $this->lang->line('collection') . " " . $this->lang->line('report'); ?></a></li>-->
                    <?php } ?>

                </ul>
            </div>
        </div>
    </div>
</div>