<?php
$currency_symbol = 'FCA'; // Symbole de la devise

// var_dump($delivery);
// var_dump($totals);
// exit;
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
                        <!-- <div class="box-tools pull-right">
                            <a href="<?php echo base_url('admin/quoteitem/print/'.$quote['id']); ?>" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fa fa-print"></i> Imprimer
                            </a>
                            <a href="<?php echo base_url('admin/quoteitem/sendEmail/'.$quote['id']); ?>" class="btn btn-info btn-sm">
                                <i class="fa fa-envelope"></i> Envoyer par email
                            </a>
                        </div> -->
                    </div>
                    <div class="box-body">
                        <!-- Informations générales -->
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Informations du bon de livraison</h4>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Référence</th>
                                        <td><?php echo $delivery['delivery_number']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date d'émission</th>
                                        <td><?php echo date('d/m/Y', strtotime($delivery['delivery_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date de validité</th>
                                        <td><?php echo date('d/m/Y', strtotime($delivery['deadline'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Désignation</th>
                                        <td><?php echo $delivery['designation']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Statut</th>
                                        <td>
                                            <?php
                                            $status_labels = [
                                                1 => ['label' => 'En préparation', 'class' => 'label-warning'],
                                                2 => ['label' => 'En cours de livraison', 'class' => 'label-success'],
                                                3 => ['label' => 'Livré', 'class' => 'label-success'],
                                                4 => ['label' => 'Annulé', 'class' => 'label-danger'],
                                                5 => ['label' => 'Terminé', 'class' => 'label-success'],
                                            ];
                                            $status = isset($status_labels[$delivery['status']]) ? $status_labels[$delivery['status']] : ['label' => 'Inconnu', 'class' => 'label-default'];
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
                                        <td><?php echo $delivery['customer_name'] . ' ' . $delivery['customer_last_name']; ?></td>
                                    </tr>
                                    <?php if (!empty($delivery['customer_email'])) : ?>
                                    <tr>
                                        <th>Email</th>
                                        <td><?php echo $delivery['customer_email']; ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($delivery['customer_phone'])) : ?>
                                    <tr>
                                        <th>Téléphone</th>
                                        <td><?php echo $delivery['customer_phone']; ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($delivery['customer_address'])) : ?>
                                    <tr>
                                        <th>Adresse</th>
                                        <td><?php echo $delivery['customer_address']; ?></td>
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
                                        <td><?php echo $delivery['payment_terms']; ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Conditions de livraison</h4>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Termes de livraison</th>
                                        <td><?php echo $delivery['shipping_method']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Lieu de livraison</th>
                                        <td><?php echo $delivery['delivery_address']; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Articles du de -->
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
                                            <?php foreach ($delivery['items'] as $item) : ?>
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
                                                <td><?php echo number_format($delivery['total_ht'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                            </tr>
                                            <?php if ($delivery['apply_tva']) : ?>
                                            <tr>
                                                <th colspan="5" class="text-right">TVA (<?php echo $delivery['tva_rate']; ?>%)</th>
                                                <td><?php echo number_format($delivery['tva_amount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                            </tr>
                                            <tr>
                                                <th colspan="5" class="text-right">Total TTC</th>
                                                <td><?php echo number_format($delivery['total_ttc'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                            </tr>
                                            <?php endif; ?>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Récapitulatif des totaux -->
                        <div class="row mt-4">
                            <div class="col-md-6 col-md-offset-6">
                                <div class="box box-info">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Récapitulatif des totaux</h3>
                                    </div>
                                    <div class="box-body">
                                        <table class="table">
                                            <tr>
                                                <th>Total HT</th>
                                                <td class="text-right"><?php echo number_format($delivery['total_ht'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                            </tr>
                                            <?php if ($delivery['apply_tva']) : ?>
                                            <tr>
                                                <th>TVA (<?php echo $delivery['tva_rate']; ?>%)</th>
                                                <td class="text-right"><?php echo number_format($delivery['tva_amount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                            </tr>
                                            <tr class="total-row">
                                                <th>Total TTC</th>
                                                <td class="text-right"><?php echo number_format($delivery['total_ttc'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                            </tr>
                                            <?php endif; ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes et informations supplémentaires -->
                        <?php if (!empty($delivery['notes'])) : ?>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h4>Notes</h4>
                                <div class="well">
                                    <?php echo nl2br($delivery['notes']); ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="box-footer">
                        <a href="<?php echo base_url('admin/deliveryitem'); ?>" class="btn btn-default">
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

.box-info {
    border-top-color: #00c0ef;
}

.total-row {
    font-weight: bold;
    font-size: 1.1em;
    border-top: 2px solid #ddd;
}

.total-row td {
    font-size: 1.2em;
    color: #00c0ef;
}
</style> 