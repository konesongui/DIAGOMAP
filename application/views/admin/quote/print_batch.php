<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Factures regroupées</title>
    <style>
        @media print { body { margin: 0; padding: 0; } .page-break { page-break-before: always; } .no-break { page-break-inside: avoid; } }
        body { font-family: Arial, sans-serif; margin: 20px; }
        .quote-container { margin-bottom: 40px; border: 1px solid #ddd; padding: 20px; background: #fff; }
        .header { text-align: center; margin-bottom: 20px; }
        .company-info { margin-bottom: 30px; }
        .customer-info { margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
        .totals { text-align: right; margin-top: 20px; }
        .footer { margin-top: 30px; font-size: 12px; text-align: center; }
        .badge-status { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .status-1 { background: #ffc107; color: #000; }
        .status-2 { background: #28a745; color: #fff; }
        .status-3 { background: #dc3545; color: #fff; }
        .status-4 { background: #17a2b8; color: #fff; }
        .status-5 { background: #007bff; color: #fff; }
        .status-6 { background: #6c757d; color: #fff; }
    </style>
</head>
<body>
<?php foreach ($quotes as $index => $quote): ?>
    <div class="quote-container <?= $index > 0 ? 'page-break' : '' ?>">
        <div class="header">
            <h2><?= htmlspecialchars($company['company_name']) ?></h2>
            <p><?= nl2br(htmlspecialchars($company['address'] ?? '')) ?></p>
            <p>Tél: <?= htmlspecialchars($company['phone'] ?? '') ?> | Email: <?= htmlspecialchars($company['email'] ?? '') ?></p>
            <h3>FACTURE PROFORMA / DEVIS N° <?= htmlspecialchars($quote['quote_number']) ?></h3>
        </div>
        <div class="customer-info">
            <strong>Client :</strong> <?= htmlspecialchars($quote['customer_name'] . ' ' . ($quote['customer_last_name'] ?? '')) ?><br>
            <strong>Date d'émission :</strong> <?= date('d/m/Y', strtotime($quote['quote_date'])) ?><br>
            <strong>Validité :</strong> <?= date('d/m/Y', strtotime($quote['valid_until'])) ?><br>
            <strong>Objet :</strong> <?= htmlspecialchars($quote['objet']) ?><br>
            <strong>Statut :</strong>
            <span class="badge-status status-<?= $quote['status'] ?>">
                <?php $statuses = [1=>'En attente',2=>'Validé',3=>'Rejeté',4=>'En cours',5=>'Livré',6=>'Annulé']; echo $statuses[$quote['status']]; ?>
            </span>
        </div>
        <table>
            <thead><tr><th>Article</th><th>Catégorie</th><th>Quantité</th><th>Unité</th><th>Prix unitaire</th><th>Remise</th><th>Total HT</th></tr></thead>
            <tbody>
            <?php foreach ($quote['items'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                    <td><?= htmlspecialchars($item['category_name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= htmlspecialchars($item['unit']) ?></td>
                    <td><?= number_format($item['unit_price'], 2, ',', ' ') ?></td>
                    <td><?= $item['discount'] ?> <?= $item['discount_type'] == 'percent' ? '%' : 'FCFA' ?></td>
                    <td><?= number_format($item['line_total_after_discount'], 2, ',', ' ') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="totals">
            <p>Total HT : <?= number_format($quote['total_ht'], 2, ',', ' ') ?> FCFA</p>
            <?php if ($quote['total_discount'] > 0): ?>
                <p>Remise totale : - <?= number_format($quote['total_discount'], 2, ',', ' ') ?> FCFA</p>
            <?php endif; ?>
            <p>Net à payer HT : <?= number_format($quote['total_after_discount'], 2, ',', ' ') ?> FCFA</p>
            <?php if ($quote['tax_option'] == 'tva' && $quote['tva_amount'] > 0): ?>
                <p>TVA (<?= $quote['tva_rate'] ?>%) : <?= number_format($quote['tva_amount'], 2, ',', ' ') ?> FCFA</p>
            <?php elseif ($quote['tax_option'] == 'other' && $quote['other_tax_amount'] > 0): ?>
                <p><?= htmlspecialchars($quote['other_tax_name']) ?> (<?= $quote['other_tax_rate'] ?>%) : <?= number_format($quote['other_tax_amount'], 2, ',', ' ') ?> FCFA</p>
            <?php endif; ?>
            <h3>TOTAL TTC : <?= number_format($quote['total_ttc'], 2, ',', ' ') ?> FCFA</h3>
            <p><em>Arrêté à : <?= ucfirst($quote['totalAsletter']) ?> FCFA</em></p>
        </div>
        <div class="footer">
            <p>Conditions de paiement : <?= nl2br(htmlspecialchars($quote['payment_terms'])) ?></p>
            <p>Lieu de livraison : <?= htmlspecialchars($quote['delivery_location']) ?></p>
            <p>Généré le <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
<?php endforeach; ?>
<script>window.print();</script>
</body>
</html>