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
        .quote-block {
            margin-bottom: 40px;
            border: 1px solid #ddd;
            padding: 20px;
            page-break-inside: avoid;
        }
        .quote-header {
            border-bottom: 2px solid black;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .company-info { float: left; width: 50%; }
        .quote-title { float: right; text-align: right; width: 50%; }
        .clearfix { clear: both; }
        .customer-info, .quote-details { margin: 15px 0; }
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
    </style>
</head>
<body>
<?php foreach ($quotes as $index => $quote): ?>
    <div class="quote-block">
        <div class="quote-header">
            <div class="company-info">
                <h2><?php echo html_escape($company['name']); ?></h2>
                <p>
                    <?php echo nl2br(html_escape($company['address'])); ?><br>
                    Tél : <?php echo html_escape($company['phone']); ?><br>
                    Email : <?php echo html_escape($company['email']); ?><br>
                    NIF : <?php echo html_escape($company['nif'] ?? ''); ?>
                </p>
            </div>
            <div class="quote-title">
                <h2>DEVIS N° <?php echo html_escape($quote['quote_number']); ?></h2>
                <p>
                    Date : <?php echo date('d/m/Y', strtotime($quote['quote_date'])); ?><br>
                    Validité : <?php echo date('d/m/Y', strtotime($quote['valid_until'])); ?><br>
                    Statut :
                    <?php
                    $status_label = '';
                    switch($quote['status']) {
                        case 1: $status_label = 'En attente'; break;
                        case 2: $status_label = 'Validé'; break;
                        case 3: $status_label = 'Rejeté'; break;
                        case 4: $status_label = 'En cours'; break;
                        case 5: $status_label = 'Livré'; break;
                        case 6: $status_label = 'Annulé'; break;
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

        <div class="quote-details">
            <table>
                <thead>
                <tr>
                    <th>Article</th>
                    <th>Catégorie</th>
                    <th>Qté</th>
                    <th>Unité</th>
                    <th class="text-right">Prix unitaire HT</th>
                    <th class="text-right">Total HT</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($quote['items'] as $item): ?>
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
                    <td class="text-right"><?php echo number_format($quote['total_ht'], 0, ',', ' '); ?> FCFA</td>
                </tr>
                <?php if (($quote['tva_amount'] ?? 0) > 0): ?>
                    <tr>
                        <td colspan="5" class="text-right"><strong>TVA (<?php echo $quote['tva_rate']; ?>%)</strong></td>
                        <td class="text-right"><?php echo number_format($quote['tva_amount'], 0, ',', ' '); ?> FCFA</td>
                    </tr>
                <?php endif; ?>
                <tr class="total-row">
                    <td colspan="5" class="text-right"><strong>Total TTC</strong></td>
                    <td class="text-right"><?php echo number_format($quote['total_ttc'], 0, ',', ' '); ?> FCFA</td>
                </tr>
                </tfoot>
            </table>

            <?php if (!empty($quote['notes'])): ?>
                <div class="notes" style="margin-top:15px;">
                    <strong>Notes :</strong><br>
                    <?php echo nl2br(html_escape($quote['notes'])); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <?php echo $company['name']; ?> – Service client : <?php echo $company['email']; ?> – Tél : <?php echo $company['phone']; ?>
            <br>Document généré le <?php echo $print_date; ?>
        </div>
    </div>
    <?php if ($index < count($quotes) - 1): ?>
        <div class="page-break"></div>
    <?php endif; ?>
<?php endforeach; ?>
<script>
    window.print();
</script>
</body>
</html>