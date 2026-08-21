<div class="row">
    <div class="col-md-12">
        <div class="box box-primary border0 mb0 margesection">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('finance') ?></h3>
            </div>

            <div class="box-body">

                <!-- Groupe 1 : Trésorerie -->
                <h4 class="text-primary"><i class="fa fa-money"></i> Trésorerie</h4>
                <ul class="reportlists">
                    <li class="col-lg-4 col-md-4 col-sm-6"><a href="<?php echo base_url(); ?>report/caisse"><i class="fa fa-file-text-o"></i> Livre de caisse</a></li>
                    <li class="col-lg-4 col-md-4 col-sm-6"><a href="<?php echo base_url(); ?>report/income"><i class="fa fa-file-text-o"></i> Caisse</a></li>
                    <li class="col-lg-4 col-md-4 col-sm-6"><a href="<?php echo base_url(); ?>report/expense"><i class="fa fa-file-text-o"></i> Dépense</a></li>
                </ul>
                <hr>

                <!-- Groupe 2 : États financiers -->
                <h4 class="text-primary"><i class="fa fa-balance-scale"></i> États financiers</h4>
                <ul class="reportlists">
                    <li class="col-lg-4 col-md-4 col-sm-6"><a href="<?php echo base_url(); ?>report/bilan_comptable"><i class="fa fa-file-text-o"></i> Bilan comptable par facture</a></li>
                    <li class="col-lg-4 col-md-4 col-sm-6"><a href="<?php echo base_url(); ?>report/grand_livre"><i class="fa fa-file-text-o"></i> Grand livre</a></li>
                    <li class="col-lg-4 col-md-4 col-sm-6"><a href="<?php echo base_url(); ?>report/payroll"><i class="fa fa-file-text-o"></i> livre de paie</a></li>
                    <li class="col-lg-4 col-md-4 col-sm-6"><a href="<?php echo base_url(); ?>report/balance_comptes"><i class="fa fa-file-text-o"></i> Balance des comptes</a></li>
                    <li class="col-lg-4 col-md-4 col-sm-6"><a href="<?php echo base_url(); ?>report/compte_resultat"><i class="fa fa-file-text-o"></i> Compte de résultat</a></li>
                    <li class="col-lg-4 col-md-4 col-sm-6"><a href="<?php echo base_url(); ?>report/flux"><i class="fa fa-file-text-o"></i> Tableau des flux de trésorerie simplifié</a></li>
                    <li class="col-lg-4 col-md-4 col-sm-6"><a href="<?php echo base_url(); ?>report/rapport_mensuel"><i class="fa fa-file-text-o"></i> Rapport Finance Centralisé</a></li>
                   <!-- <li class="col-lg-4 col-md-4 col-sm-6"><a href="<?php echo base_url(); ?>report/rapport_annuel"><i class="fa fa-file-text-o"></i> Rapport Annuel finance</a></li>-->

                </ul>
                <hr>

                <!-- Groupe 3 : Fiscalité -->
                <h4 class="text-primary"><i class="fa fa-file"></i> Fiscalité</h4>
                <ul class="reportlists">
                    <li class="col-lg-4 col-md-4 col-sm-6">
                        <a href="<?php echo base_url(); ?>report/tva">
                            <i class="fa fa-file-text-o"></i> Déclaration de TVA
                        </a>
                    </li>
                </ul>
                <hr>

                <!-- Groupe 4 : Suivi Clients -->
                <h4 class="text-primary"><i class="fa fa-users"></i> Suivi Clients</h4>
                <ul class="reportlists">
                    <li class="col-lg-4 col-md-4 col-sm-6"><a href="<?php echo base_url(); ?>report/recapt_facture_client"><i class="fa fa-file-text-o"></i> Recap Facture Client</a></li>
                    <li class="col-lg-4 col-md-4 col-sm-6"><a href="<?php echo base_url(); ?>report/balance_agee_client"><i class="fa fa-file-text-o"></i> Balance âgée des clients</a></li>
                </ul>

            </div>
        </div>
    </div>
</div>
