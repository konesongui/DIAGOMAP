<?php
$currency_symbol = 'FCA'; // Symbole de la devise
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><?php echo $title; ?></h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $page_title; ?></h3>
                    </div>
                    <div class="box-body">
                        <!-- Informations générales -->
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Informations de la commande</h4>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Référence</th>
                                        <td><?php echo $order['order_number']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date d'émission</th>
                                        <td><?php echo date('d/m/Y', strtotime($order['order_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date de validité</th>
                                        <td><?php echo date('d/m/Y', strtotime($order['valid_until'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Désignation</th>
                                        <td><?php echo $order['designation']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Statut</th>
                                        <td>
                                            <?php
                                            $status_labels = [
                                                1 => ['label' => 'En attente de validation', 'class' => 'label-warning'],
                                                2 => ['label' => 'Validée', 'class' => 'label-success'],
                                                3 => ['label' => 'Rejetée', 'class' => 'label-danger'],
                                                4 => ['label' => 'Validée', 'class' => 'label-info'],
                                                5 => ['label' => 'Livrée', 'class' => 'label-success'],
                                                6 => ['label' => 'Annulée', 'class' => 'label-default']
                                            ];
                                            $status = isset($status_labels[$order['status']]) ? $status_labels[$order['status']] : ['label' => 'Inconnu', 'class' => 'label-default'];
                                            ?>
                                            <span class="label <?php echo $status['class']; ?>"><?php echo $status['label']; ?></span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Informations client</h4>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Client</th>
                                        <td><?php echo $order['customer_name'] . ' ' . $order['customer_last_name']; ?></td>
                                    </tr>
                                    <?php if (!empty($order['customer_email'])) : ?>
                                    <tr>
                                        <th>Email</th>
                                        <td><?php echo $order['customer_email']; ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($order['customer_phone'])) : ?>
                                    <tr>
                                        <th>Téléphone</th>
                                        <td><?php echo $order['customer_phone']; ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($order['customer_address'])) : ?>
                                    <tr>
                                        <th>Adresse</th>
                                        <td><?php echo $order['customer_address']; ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>

                        <!-- Conditions de paiement et livraison -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h4>Conditions de paiement</h4>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Termes de paiement</th>
                                        <td><?php echo $order['payment_terms']; ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Conditions de livraison</h4>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Termes de livraison</th>
                                        <td><?php echo $order['delivery_terms']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Lieu de livraison</th>
                                        <td><?php echo $order['delivery_location']; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Articles de la commande -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h4>Articles</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
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
                                            <?php foreach ($order['items'] as $item) : ?>
                                            <tr>
                                                <td><?php echo $item['category_name']; ?></td>
                                                <td><?php echo $item['item_name']; ?></td>
                                                <td><?php echo $item['quantity']; ?></td>
                                                <td><?php echo $item['unit']; ?></td>
                                                <td><?php echo number_format($item['unit_price'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                                <td><?php echo number_format($item['line_total'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5" class="text-right">Total HT</th>
                                                <td><?php echo number_format($order['total_ht'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                            </tr>
                                            <?php if ($order['apply_tva']) : ?>
                                            <tr>
                                                <th colspan="5" class="text-right">TVA (<?php echo $order['tva_rate']; ?>%)</th>
                                                <td><?php echo number_format($order['tva_amount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                            </tr>
                                            <tr>
                                                <th colspan="5" class="text-right">Total TTC</th>
                                                <td><?php echo number_format($order['total_ttc'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                            </tr>
                                            <?php endif; ?>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Notes et informations supplémentaires -->
                        <?php if (!empty($order['notes'])) : ?>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h4>Notes</h4>
                                <div class="well">
                                    <?php echo nl2br($order['notes']); ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="box-footer">
                        <a href="<?php echo base_url('admin/orderformitem'); ?>" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.mt-4 {
    margin-top: 20px;
}
.table th {
    background-color: #f4f4f4;
}
.box-tools .btn {
    margin-left: 5px;
}
.well {
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
}
</style> 