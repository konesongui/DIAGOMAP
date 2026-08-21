<?php

// var_dump($dateStart);
// exit;

// Set all the CRUD variables Stock entry Tool
$dtStockEntryID = 'stockEntryDatatable';

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> Entrée de stock
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Liste des entrées de stock</h3>
                       <!-- <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('issue_item', 'can_add')) {
                                ?>
                                <a href="<?php echo site_url('admin/stockentry/form') ?>" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Ajouter une entrée de stock
                                </a>
                            <?php } ?>
                        </div>-->
                        <!-- Dans admin/stockentry/list.php -->
                        <div class="box-header with-border">
                            <!--<h3 class="box-title"><?php echo $title_list; ?></h3>-->
                            <div class="box-tools pull-right">
                                <a href="<?php echo site_url('admin/stockentry/form'); ?>" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Ajouter manuellement
                                </a>
                                <a href="<?php echo site_url('admin/stockentry/import'); ?>" class="btn btn-success btn-sm">
                                    <i class="fa fa-upload"></i> Importer
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover <?= $dtStockEntryID ?>">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Désignation</th>
                                        <th>Date</th>
                                        <th class="text-right">Montant</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/stockentry/index.js"></script>