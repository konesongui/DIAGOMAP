<!DOCTYPE html>
<html>
<head>
    <title>Devis <?php echo $quote['quote_number']; ?></title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .quote-info {
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
            width: 300px;
            margin-left: auto;
        }
        .totals table {
            margin-bottom: 0;
        }
        .notes {
            margin-top: 30px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DEVIS</h1>
        <h2><?php echo $quote['quote_number']; ?></h2>
    </div>

    <div class="company-info">
        <h3>Votre Entreprise</h3>
        <p>Adresse de l'entreprise<br>
        Téléphone: XX XX XX XX XX<br>
        Email: contact@entreprise.com</p>
    </div>

    <div class="quote-info">
        <p><strong>Date d'émission:</strong> <?php echo $quote['issue_date']; ?></p>
        <p><strong>Validité:</strong> <?php echo $quote['valid_until']; ?></p>
    </div>

    <div class="customer-info">
        <h3>Client</h3>
        <p>
            <?php echo $quote['customer']['name']; ?><br>
            <?php echo $quote['customer']['email']; ?><br>
            <?php echo $quote['customer']['address']; ?>
        </p>
    </div>

    <table>
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
            <?php foreach ($quote['items'] as $item): ?>
            <tr>
                <td><?php echo $item['category']; ?></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo $item['unit']; ?></td>
                <td><?php echo $item['price']; ?></td>
                <td><?php echo $item['total']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td><strong>Total HT</strong></td>
                <td><?php echo $quote['totals']['ht']; ?></td>
            </tr>
            <?php if ($quote['tva_info']['applied'] === 'Oui'): ?>
            <tr>
                <td>TVA (<?php echo $quote['tva_info']['rate']; ?>)</td>
                <td><?php echo $quote['totals']['tva']; ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td><strong>Total TTC</strong></td>
                <td><?php echo $quote['totals']['ttc']; ?></td>
            </tr>
        </table>
    </div>

    <?php if (!empty($quote['notes'])): ?>
    <div class="notes">
        <h4>Notes</h4>
        <p><?php echo nl2br($quote['notes']); ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>Ce devis est valable jusqu'au <?php echo $quote['valid_until']; ?></p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Imprimer</button>
    </div>
</body>
</html> 