<?php 
    $modalID = 'addPaymentModal'; 
    $modalContentID = 'addPaymentModalContent';
    $remaining = $invoice['remaining_amount'];

    // var_dump($payments);
    // die();
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo $title; ?>
            <small><?php echo $page_title; ?></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-dashboard"></i> Accueil</a></li>
            <li><a href="<?php echo base_url(); ?>admin/invoice">Factures</a></li>
            <li class="active"><?php echo $title; ?></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Détails de la facture</h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo base_url('admin/invoiceitem'); ?>" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> Retour à la liste
                            </a>
                            <a href="#" data-row-id="<?php echo $invoice['id']; ?>" class="btn btn-default btn-sm print-invoice" target="_blank">
                                <i class="fa fa-print"></i> Imprimer
                            </a>
                            <?php if ($this->rbac->hasPrivilege('invoice', 'can_edit') && (int)$invoice['status'] == 1 || (int)$invoice['status'] == 3 && $invoice['remaining_amount'] > 0) { ?>
                                <a data-toggle="modal" data-target="#<?php echo $modalID; ?>" data-row-id = "<?php echo $invoice['id']; ?>" data-toggle="tooltip" data-placement="left" title="Ajouter un paiement" type="button" data-remaining="<?php echo $remaining; ?>" class="btn btn-success btn-sm add-payment"><i class="fa fa-money me-2"></i> Ajouter un paiement</a>  
                                    
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Informations générales</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 30%">N° Facture</th>
                                        <td><?php echo $invoice['invoice_number']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Client</th>
                                        <td><?php echo $invoice['customer_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date de facture</th>
                                        <td><?php echo date('d/m/Y', strtotime($invoice['invoice_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date d'échéance</th>
                                        <td><?php echo date('d/m/Y', strtotime($invoice['due_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Statut</th>
                                        <td>
                                            <?php
                                            switch($invoice['status']) {
                                                case 1:
                                                    echo '<span class="label label-warning">Non payée</span>';
                                                    break;
                                                case 2:
                                                    echo '<span class="label label-success">Payée</span>';
                                                    break;
                                                case 3:
                                                    echo '<span class="label label-info">Partiellement payée</span>';
                                                    break;
                                                case 4:
                                                    echo '<span class="label label-danger">En retard</span>';
                                                    break;
                                                case 5:
                                                    echo '<span class="label label-danger">Annulée</span>';
                                                    break;
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Montants</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 30%">Total HT</th>
                                        <td><?php echo number_format($invoice['total_ht'], 2, ',', ' '); ?> </td>
                                    </tr>
                                    <?php if ($invoice['apply_tva']) { ?>
                                        <tr>
                                            <th>TVA (<?php echo $invoice['tva_rate']; ?>%)</th>
                                            <td><?php echo number_format($invoice['tva_amount'], 2, ',', ' '); ?> </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <th>Total TTC</th>
                                        <td><?php echo number_format($invoice['total_ttc'], 2, ',', ' '); ?> </td>
                                    </tr>
                                    <tr>
                                        <th>Montant payé</th>
                                        <td><?php echo number_format($invoice['amount_paid'], 2, ',', ' '); ?> </td>
                                    </tr>
                                    <tr>
                                        <th>Reste à payer</th>
                                        <td><?php echo number_format($invoice['remaining_amount'], 2, ',', ' '); ?> </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h4>Articles</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Catégorie</th>
                                                <th>Article</th>
                                                <th>Quantité</th>
                                                <th>Unité</th>
                                                <th>Prix unitaire</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($invoice['items'] as $item) { ?>
                                                <tr>
                                                    <td><?php echo $item['category_name']; ?></td>
                                                    <td><?php echo $item['item_name']; ?></td>
                                                    <td><?php echo number_format($item['quantity'], 2, ',', ' '); ?></td>
                                                    <td><?php echo $item['unit']; ?></td>
                                                    <td><?php echo number_format($item['unit_price'], 2, ',', ' '); ?> </td>
                                                    <td><?php echo number_format($item['line_total'], 2, ',', ' '); ?> </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($payments)) { ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Paiements</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Montant</th>
                                                    <th>Méthode</th>
                                                    <th>Référence</th>
                                                    <th>Notes</th>
                                                    <th>Enregistré par</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($payments as $payment) { ?>
                                                    <tr>
                                                        <td><?php echo date('d/m/Y', strtotime($payment['payment_date'])); ?></td>
                                                        <td><?php echo number_format($payment['amount'], 2, ',', ' '); ?> </td>
                                                        <td>
                                                            <?php
                                                            switch($payment['method']) {
                                                                case 'cash':
                                                                    echo 'Espèces';
                                                                    break;
                                                                case 'check':
                                                                    echo 'Chèque';
                                                                    break;
                                                                case 'bank_transfer':
                                                                    echo 'Virement';
                                                                    break;
                                                                case 'card':
                                                                    echo 'Carte bancaire';
                                                                    break;
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?php echo $payment['reference']; ?></td>
                                                        <td><?php echo $payment['notes']; ?></td>
                                                        <td><?php echo $payment['user_name']; ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if (!empty($invoice['notes'])) { ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Notes</h4>
                                    <div class="well">
                                        <?php echo nl2br($invoice['notes']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="<?= $modalID ?>" class="modal fade" data-backdrop="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="<?= $modalContentID ?>">

            </div>
        </div>
    </div>
</div><!-- /.content-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="<?= base_url('assets/js/invoice/addPayment.js') ?>"></script>