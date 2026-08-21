<?php
// Récupération des données depuis votre système
$companyName = $company['name'] ?? "Carrefour Market Angré";
$companyAddress = $company['address'] ?? "Centre Commercial KOKOH MALI";
$companyPhone = $company['phone'] ?? "07 06 45 79 44";
$companyHours = "7 jours / 7 - 24H / 24H";

$quoteNumber = $quote_selling['quote_number'] ?? "3163";
$quoteDate = !empty($quote_selling['quote_date']) ? date('d/m/Y', strtotime($quote_selling['quote_date'])) : date('d/m/Y');
$quoteTime = !empty($quote_selling['created_at']) ? date('H:i', strtotime($quote_selling['created_at'])) : date('H:i');
$cashierId = $quote_selling['user_name'] ?? "2";
$employeeId = $quote_selling['employee_id'] ?? "2";
$transactionId = $quote_selling['id'] ?? rand(10000, 99999);
$storeCode = "10206";

$items = $quote_selling['items'] ?? [];
$totalItems = count($items);
$total_ttc = (!empty($quote_selling['total_ttc']) && floatval($quote_selling['total_ttc']) > 0) ? floatval($quote_selling['total_ttc']) : 0;
$tva_amount = (!empty($quote_selling['tva_amount']) && floatval($quote_selling['tva_amount']) > 0) ? floatval($quote_selling['tva_amount']) : 0;
$tva_rate = $quote_selling['tva_rate'] ?? 18;
$amount_paid = (!empty($quote_selling['amount_paid']) && floatval($quote_selling['amount_paid']) > 0) ? floatval($quote_selling['amount_paid']) : $total_ttc;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ticket de caisse</title>
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .ticket {
                width: 100%;
                margin: 0;
                padding: 2mm;
            }
            .no-print {
                display: none;
            }
        }

        body {
            margin: 0;
            padding: 0;
            background: #fff;
            font-family: 'Courier New', monospace;
        }

        .ticket {
            width: 270px;
            max-width: 100%;
            margin: 0 auto;
            padding: 8px 5px;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            line-height: 1.3;
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }

        .shop-name {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .shop-address {
            font-size: 9px;
        }

        .separator {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .separator-dotted {
            border-top: 1px dotted #000;
            margin: 5px 0;
        }

        .info-line {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .items-table {
            width: 100%;
            margin: 5px 0;
        }

        .items-table th, .items-table td {
            text-align: left;
            padding: 2px 0;
        }

        .items-table th:nth-child(2), .items-table td:nth-child(2),
        .items-table th:nth-child(3), .items-table td:nth-child(3) {
            text-align: center;
        }

        .items-table th:last-child, .items-table td:last-child {
            text-align: right;
        }

        .total-line {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 8px;
            padding-top: 5px;
            border-top: 1px dashed #000;
            font-size: 9px;
        }

        .barcode {
            text-align: center;
            margin: 5px 0;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
<div class="ticket">
    <!-- En-tête -->
    <div class="header">
        <div class="shop-name">BIENVENUE</div>
        <div class="shop-name"><?= htmlspecialchars($companyName) ?></div>
        <div class="shop-address"><?= htmlspecialchars($companyAddress) ?></div>
        <div class="shop-address">Tel: <?= htmlspecialchars($companyPhone) ?></div>
        <div class="shop-address"><?= $companyHours ?></div>
    </div>

    <!-- Numéro ticket -->
    <div class="text-center" style="font-size: 18px; font-weight: bold; margin: 5px 0;">
        <?= $quoteNumber ?>
    </div>

    <div class="separator"></div>

    <!-- Liste des articles -->
    <table class="items-table">
        <thead>
        <tr>
            <th>Designation</th>
            <th>Qté</th>
            <th>Prix</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars(substr($item['item_name'] ?? '', 0, 20)) ?></td>
                <td><?= number_format(floatval($item['quantity'] ?? 0), 0, ',', ' ') ?></td>
                <td><?= number_format(floatval($item['unit_price'] ?? 0), 0, ',', ' ') ?></td>
                <td><?= number_format(floatval($item['line_total_after_discount'] ?? 0), 0, ',', ' ') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="separator-dotted"></div>

    <!-- Totaux -->
    <div class="info-line">
        <span>Total</span>
        <span><?= number_format($total_ttc, 0, ',', ' ') ?></span>
    </div>

    <div class="info-line">
        <span>Nombre d'articles</span>
        <span><?= $totalItems ?></span>
    </div>

    <div class="separator-dotted"></div>

    <!-- TVA -->
    <div class="info-line">
        <span>TVA incluse</span>
        <span></span>
    </div>
    <div class="info-line">
        <span><?= $tva_rate ?> % de <?= number_format($total_ttc, 0, ',', ' ') ?></span>
        <span><?= number_format($tva_amount, 0, ',', ' ') ?></span>
    </div>

    <div class="separator"></div>

    <!-- Informations paiement -->
    <div class="info-line">
        <span>Montant payé</span>
        <span><?= number_format($amount_paid, 0, ',', ' ') ?></span>
    </div>

    <?php if (!empty($quote['change_amount']) && $quote['change_amount'] > 0): ?>
        <div class="info-line">
            <span>Monnaie rendue</span>
            <span><?= number_format($quote['change_amount'], 0, ',', ' ') ?></span>
        </div>
    <?php endif; ?>

    <div class="separator"></div>

    <!-- Date et infos caisse -->
    <div class="info-line">
        <span>Date</span>
        <span><?= $quoteDate ?> <?= $quoteTime ?></span>
    </div>
    <div class="info-line">
        <span>Mag. Caj</span>
        <span><?= $storeCode ?> <?= $cashierId ?></span>
    </div>
    <div class="info-line">
        <span>Emp</span>
        <span><?= $employeeId ?></span>
    </div>
    <div class="info-line">
        <span>Transac</span>
        <span><?= $transactionId ?></span>
    </div>

    <div class="separator"></div>

    <!-- Pied de page -->
    <div class="footer">
        <div><?= htmlspecialchars($companyName) ?></div>
        <div>Nous remercions de votre visite</div>
        <div class="barcode">*<?= $quoteNumber ?>*</div>
    </div>
</div>

<!-- Bouton impression (visible uniquement à l'écran) -->
<div class="no-print" style="text-align: center; margin-top: 20px; padding: 10px;">
    <button onclick="window.print();" style="padding: 10px 20px; font-size: 16px;">
        🖨️ Imprimer le ticket
    </button>
</div>

<script>
    // Impression automatique au chargement
    window.onload = function() {
        window.print();
    }
</script>
</body>
</html>