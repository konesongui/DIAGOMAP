<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de congé</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            margin: 2cm;
        }
        .header {
            text-align: right;
            margin-bottom: 40px;
        }
        .subject {
            margin: 30px 0;
            font-weight: bold;
        }
        .content {
            margin-top: 30px;
        }
        .signature {
            margin-top: 60px;
            text-align: right;
        }
        .date {
            margin-bottom: 20px;
        }
        .greeting {
            margin: 20px 0;
        }
        .footer {
            margin-top: 80px;
            font-size: 10px;
            text-align: center;
        }
        .bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="header">
    <strong>LA RESIDENCE HOTEL SANTA ADELINA</strong><br>
    <!-- Ajoutez ici votre logo si nécessaire -->
</div>

<div class="date">
    Abidjan, le <?php echo $current_date; ?>
</div>

<div class="subject">
    Objet : Autorisation de congé annuel
</div>

<div class="greeting">
    Mademoiselle,<br>
    <?php echo $staff->name . ' ' . $staff->surname; ?>
</div>

<div class="content">
    <p>Suite à votre demande de congé, nous avons le plaisir de vous informer que vous êtes autorisée à bénéficier de votre congé annuel pour la période allant du <strong><?php echo $leave_from_formatted; ?></strong> au <strong><?php echo $leave_to_formatted; ?></strong>, soit une durée de <strong><?php echo $leave_days; ?> jours</strong>.</p>
    <p>Vous partirez le <?php echo $leave_from_formatted; ?> après votre service et reviendrez le <?php echo $leave_to_formatted; ?> à partir de 08H.</p>
    <p>Nous vous souhaitons un excellent congé et un bon repos.</p>
</div>

<div class="signature">
    Veuillez agréer, Mademoiselle, l’expression de nos salutations distinguées.<br><br><br>
    <strong>Mme AMON YOLANDE</strong><br>
    <!-- Ajoutez un cachet ou signature si nécessaire -->
</div>

<div class="footer">
    LA RESIDENCE HOTEL SANTA ADELINA
</div>
</body>
</html>