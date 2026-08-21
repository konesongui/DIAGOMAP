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
        .invoice-block {
            margin-bottom: 40px;
            border: 1px solid #ddd;
            padding: 20px;
            page-break-inside: avoid;
        }
        .invoice-header {
            border-bottom: 2px solid black;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .company-info {
            float: left;
            width: 50%;
        }
        .invoice-title {
            float: right;
            text-align: right;
            width: 50%;
        }
        .clearfix { clear: both; }
        .customer-info, .invoice-details {
            margin: 15px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        h2 {
            color: black;
            margin: 0 0 10px 0;
        }
        .payment-history {
            margin-top: 15px;
            background-color: #f9f9f9;
            padding: 10px;
        }
    </style>
</head>
<body>
<?php foreach ($invoices as $index => $invoice): ?>
    <div class="invoice-block">
        <div class="invoice-header">
            <div class="company-info">
                <h2><?php echo html_escape($company['name']); ?></h2>
                <p>
                    <?php echo nl2br(html_escape($company['address'])); ?><br>
                    Tél : <?php echo html_escape($company['phone']); ?><br>
                    Email : <?php echo html_escape($company['email']); ?><br>
                    NIF : <?php echo html_escape($company['nif'] ?? ''); ?>
                </p>
            </div>
            <div class="invoice-title">
                <h2>FACTURE N° <?php echo html_escape($invoice['invoice_number']); ?></h2>
                <p>
                    Date : <?php echo date('d/m/Y', strtotime($invoice['invoice_date'])); ?><br>
                    Échéance : <?php echo date('d/m/Y', strtotime($invoice['due_date'])); ?><br>
                    Statut :
                    <?php
                    $status_label = '';
                    switch($invoice['status']) {
                        case 1: $status_label = 'Non payée'; break;
                        case 2: $status_label = 'Payée'; break;
                        case 3: $status_label = 'Partiellement payée'; break;
                        case 4: $status_label = 'En retard'; break;
                        case 5: $status_label = 'Annulée'; break;
                        default: $status_label = 'Inconnu';
                    }
                    echo $status_label;
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

        <div class="invoice-details">
            <table>
                <thead>
                <tr>
                    <th>Article</th>
                    <th>Catégorie</th>
                    <th>Quantité</th>
                    <th>Unité</th>
                    <th class="text-right">Prix unitaire HT</th>
                    <th class="text-right">Total HT</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($invoice['items'] as $item): ?>
                    <tr>
                        <td><?php echo html_escape($item['item_name']); ?></td>
                        <td><?php echo html_escape($item['category_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo html_escape($item['unit']); ?></td>
                        <td class="text-right"><?php echo number_format($item['unit_price'], 0, ',', ' '); ?> FCFA</td>
                        <td class="text-right"><?php echo number_format($item['line_total'], 0, ',', ' '); ?> FCFA</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5" class="text-right"><strong>Total HT</strong></td>
                    <td class="text-right"><?php echo number_format($invoice['total_ht'], 0, ',', ' '); ?> FCFA</td>
                </tr>
                <?php if ($invoice['tva_amount'] > 0): ?>
                    <tr>
                        <td colspan="5" class="text-right"><strong>TVA (<?php echo $invoice['tva_rate']; ?>%)</strong></td>
                        <td class="text-right"><?php echo number_format($invoice['tva_amount'], 0, ',', ' '); ?> FCFA</td>
                    </tr>
                <?php endif; ?>
                <tr class="total-row">
                    <td colspan="5" class="text-right"><strong>Total TTC</strong></td>
                    <td class="text-right"><?php echo number_format($invoice['total_ttc'], 0, ',', ' '); ?> FCFA</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right"><strong>Déjà payé</strong></td>
                    <td class="text-right"><?php echo number_format($invoice['amount_paid'], 0, ',', ' '); ?> FCFA</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right"><strong>Reste à payer</strong></td>
                    <td class="text-right"><?php echo number_format($invoice['remaining_amount'], 0, ',', ' '); ?> FCFA</td>
                </tr>
                </tfoot>
            </table>

            <?php if (!empty($invoice['payments'])): ?>
                <div class="payment-history">
                    <strong>Historique des paiements :</strong>
                    <table style="margin-top:5px;">
                        <thead>
                        <tr><th>Date</th><th>Montant</th><th>Mode</th><th>Référence</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($invoice['payments'] as $payment): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($payment['payment_date'])); ?></td>
                                <td><?php echo number_format($payment['amount'], 0, ',', ' '); ?> FCFA</td>
                                <td><?php echo html_escape($payment['method']); ?></td>
                                <td><?php echo html_escape($payment['reference'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if (!empty($invoice['notes'])): ?>
                <div class="notes" style="margin-top:15px;">
                    <strong>Notes :</strong><br>
                    <?php echo nl2br(html_escape($invoice['notes'])); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <?php echo $company['name']; ?> – Service client : <?php echo $company['email']; ?> – Tél : <?php echo $company['phone']; ?>
            <br>Facture générée le <?php echo $print_date; ?>
        </div>
    </div>
    <?php if ($index < count($invoices) - 1): ?>
        <div class="page-break"></div>
    <?php endif; ?>
<?php endforeach; ?>
<script>
    window.print();
</script>
</body>
</html>