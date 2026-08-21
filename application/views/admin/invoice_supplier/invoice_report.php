<?php

$dID = 'invoiceDatatable';
$modalID = 'addPaymentModal';
$modalContentID = 'addPaymentContent';

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> Factures
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $title_list; ?></h3>
                        <div class="box-tools pull-right">
                            <div class="form-group btn-sm" style="display: inline-block; margin-right: 10px;">
                                <select id="statusFilter" class="form-control">
                                    <option value="">Tous les statuts</option>
                                    <option value="1">Non payée</option>
                                    <option value="2">Payée</option>
                                    <option value="3">Partiellement payée</option>
                                    <option value="5">Annulée</option>
                                </select>
                            </div>
                            <?php if ($this->rbac->hasPrivilege('facture', 'can_add')) { ?>
                                <!--<a href="<?php echo base_url(); ?>admin/invoiceitem/form" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Nouvelle facture
                                </a>-->
                            <?php } ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="mailbox-messages table-responsive">
                            <table class="table table-striped table-bordered table-hover <?= $dID ?>" data-export-title="<?php echo $this->lang->line('issue_item'); ?>">
                                <thead>
                                    <tr>
                                      <th>N° Facture</th>
                                      <th>Client</th>
                                      <th>Date</th>
                                      <th>Échéance</th>
                                      <th>Montant HT</th>
                                      <th>TVA</th>
                                      <th>Total TTC</th>
                                      <th>Payé</th>
                                      <th>Reste</th>
                                        <th>Suivie par</th>
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

    <div id="<?= $modalID ?>" class="modal fade" data-backdrop="false">
        <div class="modal-dialog">
            <div class="modal-content" id="<?= $modalContentID ?>">

            </div>
        </div>
    </div>

</div><!-- /.content-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="<?= base_url('assets/js/invoice/index.js') ?>"></script>