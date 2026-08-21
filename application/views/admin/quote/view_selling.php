<?php
$currency_symbol = 'FCFA'; // Symbole de la devise
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
                                <h4>Informations du devis</h4>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Référence</th>
                                        <td><?php echo $quote['quote_number']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date d'émission</th>
                                        <td><?php echo date('d/m/Y', strtotime($quote['quote_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date de validité</th>
                                        <td><?php echo date('d/m/Y', strtotime($quote['valid_until'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Objet</th>
                                        <td><?php echo $quote['objet']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Statut</th>
                                        <td>
                                            <?php
                                            $status_labels = [
                                                1 => ['label' => 'En attente de validation', 'class' => 'label-warning'],
                                                2 => ['label' => 'Validée', 'class' => 'label-success'],
                                                3 => ['label' => 'Rejetée', 'class' => 'label-danger'],
                                                4 => ['label' => 'En cours', 'class' => 'label-info'],
                                                5 => ['label' => 'Livrée', 'class' => 'label-success'],
                                                6 => ['label' => 'Annulée', 'class' => 'label-default']
                                            ];
                                            $status = isset($status_labels[$quote['status']]) ? $status_labels[$quote['status']] : ['label' => 'Inconnu', 'class' => 'label-default'];
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
                                        <td><?php echo $quote['customer_name'] . ' ' . $quote['customer_last_name']; ?></td>
                                    </tr>
                                    <?php if (!empty($quote['customer_email'])) : ?>
                                        <tr>
                                            <th>Email</th>
                                            <td><?php echo $quote['customer_email']; ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($quote['customer_phone'])) : ?>
                                        <tr>
                                            <th>Téléphone</th>
                                            <td><?php echo $quote['customer_phone']; ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($quote['customer_address'])) : ?>
                                        <tr>
                                            <th>Adresse</th>
                                            <td><?php echo $quote['customer_address']; ?></td>
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
                                        <td><?php echo $quote['payment_terms']; ?></td>
                                    </tr>
                                    <?php if (!empty($quote['payment_method'])): ?>
                                        <tr>
                                            <th>Mode de paiement</th>
                                            <td><?php echo $quote['payment_method']; ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($quote['payment_method_type'])): ?>
                                        <tr>
                                            <th>Type de paiement</th>
                                            <td>
                                                <?php
                                                if ($quote['payment_method_type'] == 'cash') {
                                                    echo '<span class="label label-success">💰 Espèces (Caisse)</span>';
                                                } elseif ($quote['payment_method_type'] == 'bank') {
                                                    echo '<span class="label label-primary">🏦 Paiement bancaire</span>';
                                                } else {
                                                    echo $quote['payment_method_type'];
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Conditions de livraison</h4>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Termes de livraison</th>
                                        <td><?php echo $quote['delivery_terms']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Lieu de livraison</th>
                                        <td><?php echo $quote['delivery_location']; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- 🔹 NOUVEAU : Informations de paiement (Montant payé, rendu, reste) -->
                        <?php
                        $amount_paid = isset($quote['amount_paid']) ? floatval($quote['amount_paid']) : 0;
                        $change_amount = isset($quote['change_amount']) ? floatval($quote['change_amount']) : 0;
                        $remaining_amount = isset($quote['remaining_amount']) ? floatval($quote['remaining_amount']) : 0;
                        $payment_status = isset($quote['payment_status']) ? $quote['payment_status'] : 'pending';

                        $payment_status_label = '';
                        $payment_status_class = '';
                        switch ($payment_status) {
                            case 'paid':
                                $payment_status_label = '✅ Payé totalement';
                                $payment_status_class = 'label-success';
                                break;
                            case 'partial':
                                $payment_status_label = '💸 Paiement partiel';
                                $payment_status_class = 'label-warning';
                                break;
                            case 'pending':
                            default:
                                $payment_status_label = '⏳ En attente de paiement';
                                $payment_status_class = 'label-default';
                                break;
                        }
                        ?>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="box box-success">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-money"></i> Informations de paiement</h3>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="small-box" style="margin-bottom: 0; border-radius: 5px; background: #d4edda;"> <!-- vert clair -->
                                                    <div class="inner" style="padding: 15px; color: #000;">
                                                        <h3 style="color: #000;"><?php echo number_format($amount_paid, 0, ',', ' ') . ' ' . $currency_symbol; ?></h3>
                                                        <p style="color: #000;">💰 Montant payé</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="small-box" style="margin-bottom: 0; border-radius: 5px; background: #fff3cd;"> <!-- jaune clair -->
                                                    <div class="inner" style="padding: 15px; color: #000;">
                                                        <h3 style="color: #000;"><?php echo number_format($change_amount, 0, ',', ' ') . ' ' . $currency_symbol; ?></h3>
                                                        <p style="color: #000;">🔄 Montant rendu</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="small-box" style="margin-bottom: 0; border-radius: 5px; background: #f8d7da;"> <!-- rouge clair -->
                                                    <div class="inner" style="padding: 15px; color: #000;">
                                                        <h3 style="color: #000;"><?php echo number_format($remaining_amount, 0, ',', ' ') . ' ' . $currency_symbol; ?></h3>
                                                        <p style="color: #000;">⚠️ Reste à payer</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="small-box" style="margin-bottom: 0; border-radius: 5px; background: #d1ecf1;"> <!-- bleu clair -->
                                                    <div class="inner" style="padding: 15px; color: #000;">
                                                        <h3 style="color: #000;">
                    <span class="<?php echo $payment_status_class; ?>" style="font-size: 18px; color: #000; background: transparent; padding: 0;">
                        <?php echo $payment_status_label; ?>
                    </span>
                                                        </h3>
                                                        <p style="color: #000;">📊 Statut</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tableau détaillé des paiements -->
                                        <div class="row mt-4" style="margin-top: 15px;">
                                            <div class="col-md-12">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                    <tr class="active">
                                                        <th>Total TTC</th>
                                                        <th>Montant payé</th>
                                                        <th>Montant rendu</th>
                                                        <th>Reste à payer</th>
                                                        <th>Statut</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td><strong><?php echo number_format($quote['total_ttc'], 2, ',', ' ') . ' ' . $currency_symbol; ?></strong></td>
                                                        <td class="text-success"><strong><?php echo number_format($amount_paid, 2, ',', ' ') . ' ' . $currency_symbol; ?></strong></td>
                                                        <td class="text-warning"><strong><?php echo number_format($change_amount, 2, ',', ' ') . ' ' . $currency_symbol; ?></strong></td>
                                                        <td class="text-danger"><strong><?php echo number_format($remaining_amount, 2, ',', ' ') . ' ' . $currency_symbol; ?></strong></td>
                                                        <td><span class="label <?php echo $payment_status_class; ?>"><?php echo $payment_status_label; ?></span></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- FIN NOUVEAU -->

                        <!-- Articles du devis avec remises -->
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
                                            <th>Remise</th>
                                            <th>Total HT</th>
                                            <th>Total après remise</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($quote['items'] as $item) :
                                            $discount_display = '';
                                            if ($item['discount'] > 0) {
                                                if ($item['discount_type'] === 'percent') {
                                                    $discount_display = $item['discount'] . '%';
                                                } else {
                                                    $discount_display = number_format($item['discount'], 2, ',', ' ') . ' ' . $currency_symbol;
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td><?php echo $item['category_name']; ?></td>
                                                <td><?php echo $item['item_name']; ?></td>
                                                <td><?php echo $item['quantity']; ?></td>
                                                <td><?php echo $item['unit']; ?></td>
                                                <td><?php echo number_format($item['unit_price'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                                <td>
                                                    <?php if ($discount_display): ?>
                                                        <span class="label label-warning"><?php echo $discount_display; ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">Aucune</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo number_format($item['line_total'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                                <td>
                                                    <?php if (isset($item['line_total_after_discount']) && $item['line_total_after_discount'] != $item['line_total']): ?>
                                                        <strong class="text-success"><?php echo number_format($item['line_total_after_discount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></strong>
                                                    <?php else: ?>
                                                        <?php echo number_format($item['line_total'], 2, ',', ' ') . ' ' . $currency_symbol; ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
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
                                                <td class="text-right"><?php echo number_format($quote['total_ht'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                            </tr>

                                            <!-- Affichage des remises individuelles -->
                                            <?php if (isset($quote['total_discount']) && $quote['total_discount'] > 0): ?>
                                                <tr>
                                                    <th>Total remises articles</th>
                                                    <td class="text-right text-danger">- <?php echo number_format($quote['total_discount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                                </tr>
                                            <?php endif; ?>

                                            <!-- Affichage de la remise globale -->
                                            <?php if (isset($quote['global_discount_amount']) && $quote['global_discount_amount'] > 0):
                                                $global_discount_display = '';
                                                if ($quote['global_discount_type'] === 'percent') {
                                                    $global_discount_display = $quote['global_discount_amount'] . '%';
                                                } else {
                                                    $global_discount_display = number_format($quote['global_discount_amount'], 2, ',', ' ') . ' ' . $currency_symbol;
                                                }
                                                ?>
                                                <tr>
                                                    <th>Remise globale <?php echo $global_discount_display; ?></th>
                                                    <td class="text-right text-danger">- <?php echo number_format($quote['global_discount_amount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                                </tr>
                                            <?php endif; ?>

                                            <!-- Total après remise -->
                                            <?php if (isset($quote['total_after_discount']) && $quote['total_after_discount'] != $quote['total_ht']): ?>
                                                <tr class="subtotal-row">
                                                    <th>Total après remise</th>
                                                    <td class="text-right text-success">
                                                        <strong><?php echo number_format($quote['total_after_discount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></strong>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>

                                            <!-- Affichage des taxes selon l'option -->
                                            <?php
                                            $tax_option = isset($quote['tax_option']) ? $quote['tax_option'] : 'none';
                                            if ($tax_option === 'tva'): ?>
                                                <tr>
                                                    <th>TVA (<?php echo isset($quote['tva_rate']) ? $quote['tva_rate'] : 18; ?>%)</th>
                                                    <td class="text-right"><?php echo number_format($quote['tva_amount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                                </tr>
                                            <?php elseif ($tax_option === 'other' && isset($quote['other_tax_amount']) && $quote['other_tax_amount'] > 0): ?>
                                                <tr>
                                                    <th>
                                                        <?php echo !empty($quote['other_tax_name']) ? htmlspecialchars($quote['other_tax_name']) : 'Autre taxe'; ?>
                                                        (<?php echo isset($quote['other_tax_rate']) ? number_format($quote['other_tax_rate'], 2, ',', ' ') : 0; ?>%)
                                                    </th>
                                                    <td class="text-right"><?php echo number_format($quote['other_tax_amount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                                </tr>
                                            <?php endif; ?>

                                            <!-- Affichage du montant total de taxe (pour compatibilité) -->
                                            <?php if (isset($quote['total_tax_amount']) && $quote['total_tax_amount'] > 0): ?>
                                                <tr class="tax-total-row">
                                                    <th>Total taxes</th>
                                                    <td class="text-right"><?php echo number_format($quote['total_tax_amount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                                </tr>
                                            <?php endif; ?>

                                            <tr class="total-row">
                                                <th>Total
                                                    <?php
                                                    if ($tax_option === 'tva') {
                                                        echo 'TTC';
                                                    } elseif ($tax_option === 'other') {
                                                        echo !empty($quote['other_tax_name']) ? htmlspecialchars($quote['other_tax_name']) . ' inclus' : 'TTC';
                                                    } else {
                                                        echo 'HT';
                                                    }
                                                    ?>
                                                </th>
                                                <td class="text-right">
                                                    <strong class="total-amount"><?php echo number_format($quote['total_ttc'], 2, ',', ' ') . ' ' . $currency_symbol; ?></strong>
                                                </td>
                                            </tr>

                                            <!-- Économie totale -->
                                            <?php
                                            $total_savings = 0;
                                            if (isset($quote['total_discount'])) {
                                                $total_savings += $quote['total_discount'];
                                            }
                                            if (isset($quote['global_discount_amount'])) {
                                                $total_savings += $quote['global_discount_amount'];
                                            }
                                            if ($total_savings > 0):
                                                ?>
                                                <tr class="savings-row">
                                                    <th>Économie totale</th>
                                                    <td class="text-right text-success">
                                                        <strong>+ <?php echo number_format($total_savings, 2, ',', ' ') . ' ' . $currency_symbol; ?></strong>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes et informations supplémentaires -->
                        <?php if (!empty($quote['notes'])) : ?>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h4>Notes</h4>
                                    <div class="well">
                                        <?php echo nl2br($quote['notes']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="box-footer">
                        <a href="<?php echo base_url('admin/selling'); ?>" class="btn btn-default">
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

    .subtotal-row {
        border-top: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
    }

    .subtotal-row td {
        font-weight: bold;
    }

    .tax-total-row {
        border-top: 1px solid #ddd;
    }

    .savings-row {
        background-color: #f0fff0;
    }

    .savings-row td {
        color: #28a745;
        font-weight: bold;
    }

    .text-danger {
        color: #dc3545;
    }

    .text-success {
        color: #28a745;
    }

    .text-warning {
        color: #ffc107;
    }

    .label-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .total-amount {
        font-size: 1.3em;
        color: #007bff;
    }

    /* Style pour les petites boîtes de paiement */
    .small-box {
        border-radius: 5px;
        color: #fff;
        text-align: center;
    }
    .small-box.bg-green {
        background-color: #28a745 !important;
    }
    .small-box.bg-yellow {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }
    .small-box.bg-red {
        background-color: #dc3545 !important;
    }
    .small-box.bg-info {
        background-color: #17a2b8 !important;
    }
    .small-box .inner h3 {
        font-size: 24px;
        font-weight: bold;
        margin: 0 0 5px 0;
        white-space: nowrap;
        padding: 0;
    }
    .small-box .inner p {
        font-size: 14px;
        margin: 0;
    }
</style>