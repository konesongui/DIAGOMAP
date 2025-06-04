<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Devis <?php echo $quote['quote_number']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
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
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Devis <?php echo $quote['quote_number']; ?></h1>
        </div>

        <div class="company-info">
            <h2><?php echo $company['name']; ?></h2>
            <p><?php echo $company['address']; ?></p>
            <p>Tél: <?php echo $company['phone']; ?></p>
            <p>Email: <?php echo $company['email']; ?></p>
        </div>

        <div class="quote-info">
            <p>Cher client,</p>
            <p>Veuillez trouver ci-joint le devis <?php echo $quote['quote_number']; ?> concernant <?php echo $quote['designation']; ?>.</p>
            <p>Ce devis est valable pendant 30 jours à compter de sa date d'émission.</p>
        </div>

        <div class="footer">
            <p>Ce message est généré automatiquement, merci de ne pas y répondre.</p>
            <p>Pour toute question, veuillez nous contacter directement.</p>
        </div>
    </div>
</body>
</html> 