<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon de livraison #<?= $delivery['delivery_number'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .delivery-info {
            margin-bottom: 20px;
        }
        .customer-info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .totals {
            width: 50%;
            margin-left: auto;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()">Imprimer</button>
    </div>

    <div class="header">
        <h1>Bon de Livraison</h1>
        <h2>N° <?= $delivery['delivery_number'] ?></h2>
    </div>

    <div class="company-info">
        <h3>Votre Entreprise</h3>
        <p>
            Adresse de l'entreprise<br>
            Code postal Ville<br>
            Téléphone: XX XX XX XX XX<br>
            Email: contact@entreprise.com
        </p>
    </div>

    <div class="delivery-info">
        <p>
            <strong>Date de livraison:</strong> <?= date('d/m/Y', strtotime($delivery['delivery_date'])) ?><br>
            <strong>Validité:</strong> <?= date('d/m/Y', strtotime($delivery['valid_until'])) ?><br>
            <strong>Désignation:</strong> <?= $delivery['designation'] ?>
        </p>
    </div>

    <div class="customer-info">
        <h3>Client</h3>
        <p>
            <strong><?= $delivery['customer_name'] ?></strong><br>
            <?= $delivery['customer_address'] ?><br>
            <?= $delivery['customer_zip'] ?> <?= $delivery['customer_city'] ?><br>
            Téléphone: <?= $delivery['customer_phone'] ?><br>
            Email: <?= $delivery['customer_email'] ?>
        </p>
        <p>
            <strong>Lieu de livraison:</strong><br>
            <?= $delivery['delivery_location'] ?>
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Article</th>
                <th>Description</th>
                <th>Quantité</th>
                <th>Prix unitaire HT</th>
                <th>Total HT</th>
                <th>TVA</th>
                <th>Total TTC</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($delivery['items'] as $item): ?>
            <tr>
                <td><?= $item['product_name'] ?></td>
                <td><?= $item['description'] ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= number_format($item['unit_price'], 2, ',', ' ') ?> €</td>
                <td><?= number_format($item['total_ht'], 2, ',', ' ') ?> €</td>
                <td><?= number_format($item['vat_amount'], 2, ',', ' ') ?> €</td>
                <td><?= number_format($item['total_ttc'], 2, ',', ' ') ?> €</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <th>Total HT</th>
            <td><?= number_format($delivery['amount_ht'], 2, ',', ' ') ?> €</td>
        </tr>
        <tr>
            <th>Total TVA</th>
            <td><?= number_format($delivery['vat_amount'], 2, ',', ' ') ?> €</td>
        </tr>
        <tr>
            <th>Total TTC</th>
            <td><?= number_format($delivery['amount_ttc'], 2, ',', ' ') ?> €</td>
        </tr>
    </table>

    <div class="footer">
        <p>
            <strong>Conditions de paiement:</strong><br>
            <?= $delivery['payment_terms'] ?>
        </p>
        <p>
            Signature du livreur: _________________<br>
            Signature du client: _________________
        </p>
    </div>
</body>
</html> 