<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bon de commande <?php echo $quotation_number; ?></title>
    <style>
        body {
            font-family: dejavusans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: right;
            margin-bottom: 20px;
        }
        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }
        .info {
            margin: 20px 0;
        }
        .info-row {
            margin: 5px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 8px;
        }
        .table th {
            background-color: #f0f0f0;
        }
        .total {
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
        }
        .validity {
            margin-top: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div><?php echo $data['company']['name']; ?></div>
        <div>Tél: <?php echo $data['company']['phone']; ?></div>
    </div>

    <div class="title">BON DE COMMANDE</div>

    <div class="info">
        <div class="info-row"><strong>N° Bon de commande:</strong> <?php echo $quotation_number; ?></div>
        <div class="info-row"><strong>Date:</strong> <?php echo $quotation_date; ?></div>
        <div class="info-row"><strong>Client:</strong> <?php echo $client_name; ?></div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['quote']['items'] as $item): ?>
            <tr>
                <td><?php echo $item['description']; ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo number_format($item['unit_price'], 2, ',', ' ') . ' ' . $data['company']['currency']; ?></td>
                <td><?php echo number_format($item['total'], 2, ',', ' ') . ' ' . $data['company']['currency']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total">
        Total TTC: <?php echo number_format($data['quote']['total_ttc'], 2, ',', ' ') . ' ' . $data['company']['currency']; ?>
    </div>

    <div class="validity">
        Validité du bon de commande: <?php echo $this->calculateValidityDays($data['quote']['quote_date'], $data['quote']['valid_until']); ?> jours
    </div>
</body>
</html> 