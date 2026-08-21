<?php

$dID = 'deliveryDatatable';

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> Bons de commande d'achat
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Bons de commande d'achat</h3>
                        <div class="box-tools pull-right">
                            <div class="form-group btn-sm" style="display: inline-block; margin-right: 10px;">
                                <select id="statusFilter" class="form-control">
                                    <option value="">Tous les statuts</option>
                                    <option value="1">Non livré</option>
                                    <!-- <option value="2">En cours de livraison</option> -->
                                    <option value="6">Partiellement livré</option>
                                    <option value="3">Livré</option>
                                    <option value="4">Annulée</option>
                                </select>
                            </div>
                            <?php if ($this->rbac->hasPrivilege('deliveryitem_supplier', 'can_add')) { ?>
                               <a href="<?php echo base_url(); ?>admin/deliveryitem_supplier/form" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Nouveau bon de commande d'achat
                                </a>
                            <?php } ?>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="mailbox-messages table-responsive">
                            <table class="table table-striped table-bordered table-hover <?= $dID ?>" data-export-title="<?php echo $this->lang->line('issue_item'); ?>">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Fournisseur</th>
                                        <th>Date d'édition</th>
                                        <!--<th>Terme de paiement</th>-->
                                        <th>Lieu de livraison fournisseur</th>
                                        <th>Ajouté lé</th>
                                        <th>Montant total</th>
                                        <th>Statut</th>
                                        <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (right) -->
        </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- SweetAlert2 Joseph -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="<?= base_url('assets/js/delivery_supplier/index.js') ?>"></script>