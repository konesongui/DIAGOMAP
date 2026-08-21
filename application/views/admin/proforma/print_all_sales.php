<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <style>
        @media print {
            body { margin: 0; padding: 0; }
            .page-break { page-break-before: always; }
            .no-print { display: none; }
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        .sale-block {
            margin-bottom: 40px;
            border: 1px solid #ddd;
            padding: 20px;
            page-break-inside: avoid;
        }
        .sale-header {
            border-bottom: 2px solid black;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .company-info { float: left; width: 50%; }
        .sale-title { float: right; text-align: right; width: 50%; }
        .clearfix { clear: both; }
        .customer-info, .sale-details { margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        h2 { color: black; margin: 0 0 10px 0; }
        .payment-info { margin-top: 15px; background-color: #f9f9f9; padding: 10px; }
    </style>
</head>
<body>
<?php foreach ($sales as $index => $sale): ?>
    <div class="sale-block">
        <div class="sale-header">
            <div class="company-info">
                <h2><?php echo html_escape($company['name']); ?></h2>
                <p>
                    <?php echo nl2br(html_escape($company['address'])); ?><br>
                    Tél : <?php echo html_escape($company['phone']); ?><br>
                    Email : <?php echo html_escape($company['email']); ?><br>
                    NIF : <?php echo html_escape($company['nif'] ?? ''); ?>
                </p>
            </div>
            <div class="sale-title">
                <h2>BON DE VENTE N° <?php echo html_escape($sale['quote_number']); ?></h2>
                <p>
                    Date : <?php echo date('d/m/Y', strtotime($sale['quote_date'])); ?><br>
                    Échéance : <?php echo date('d/m/Y', strtotime($sale['valid_until'])); ?><br>
                    Statut vente :
                    <?php
                    $status_label = '';
                    switch($sale['status']) {
                        case 1: $status_label = 'En attente'; break;
                        case 2: $status_label = 'Validé'; break;
                        case 3: $status_label = 'Rejeté'; break;
                        case 4: $status_label = 'En cours'; break;
                        case 5: $status_label = 'Livré'; break;
                        case 6: $status_label = 'Annulé'; break;
                        default: $status_label = 'Inconnu';
                    }
                    echo $status_label;
                    ?><br>
                    Paiement :
                    <?php
                    $payment_status = $sale['payment_status'] ?? 'pending';
                    if ($payment_status == 'paid') echo 'Payé';
                    elseif ($payment_status == 'partial') echo 'Partiel';
                    else echo 'En attente';
                    ?>
                </p>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="customer-info">
            <strong>Client :</strong><br>
            <?php echo html_escape($customer['name']); ?><br>
            <?php echo nl2br(html_escape($customer['address'])); ?><br>
            Tél : <?php echo html_escape($customer['phone']); ?><br>
            Email : <?php echo html_escape($customer['email']); ?>
        </div>

        <div class="sale-details">
            <table>
                <thead>
                <tr>
                    <th>Article</th>
                    <th>Catégorie</th>
                    <th>Qté</th>
                    <th>Unité</th>
                    <th class="text-right">Prix unitaire TTC</th>
                    <th class="text-right">Total TTC</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($sale['items'] as $item): ?>
                    <tr>
                        <td><?php echo html_escape($item['item_name']); ?></td>
                        <td><?php echo html_escape($item['category_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo html_escape($item['unit']); ?></td>
                        <td class="text-right"><?php echo number_format($item['unit_price_ttc'] ?? $item['unit_price'], 0, ',', ' '); ?> FCFA</td>
                        <td class="text-right"><?php echo number_format($item['line_total_ttc_brut'] ?? ($item['quantity'] * ($item['unit_price_ttc'] ?? $item['unit_price'])), 0, ',', ' '); ?> FCFA</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5" class="text-right"><strong>Total HT</strong></td>
                    <td class="text-right"><?php echo number_format($sale['total_ht'], 0, ',', ' '); ?> FCFA</td>
                </tr>
                <?php if (($sale['tva_amount'] ?? 0) > 0): ?>
                    <tr>
                        <td colspan="5" class="text-right"><strong>TVA (<?php echo $sale['tva_rate']; ?>%)</strong></td>
                        <td class="text-right"><?php echo number_format($sale['tva_amount'], 0, ',', ' '); ?> FCFA</td>
                    </tr>
                <?php endif; ?>
                <tr class="total-row">
                    <td colspan="5" class="text-right"><strong>Total TTC</strong></td>
                    <td class="text-right"><?php echo number_format($sale['total_ttc'], 0, ',', ' '); ?> FCFA</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right"><strong>Déjà payé</strong></td>
                    <td class="text-right"><?php echo number_format($sale['amount_paid'] ?? 0, 0, ',', ' '); ?> FCFA</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right"><strong>Reste à payer</strong></td>
                    <td class="text-right"><?php echo number_format($sale['remaining_amount'] ?? 0, 0, ',', ' '); ?> FCFA</td>
                </tr>
                </tfoot>
            </table>

            <?php if (!empty($sale['notes'])): ?>
                <div class="notes" style="margin-top:15px;">
                    <strong>Notes :</strong><br>
                    <?php echo nl2br(html_escape($sale['notes'])); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <?php echo $company['name']; ?> – Service client : <?php echo $company['email']; ?> – Tél : <?php echo $company['phone']; ?>
            <br>Document généré le <?php echo $print_date; ?>
        </div>
    </div>
    <?php if ($index < count($sales) - 1): ?>
        <div class="page-break"></div>
    <?php endif; ?>
<?php endforeach; ?>
<script>
    window.print();
</script>
</body>
</html>