<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bulletin de paie</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .content { margin: 20px 0; }
        .footer { margin-top: 30px; font-size: 12px; color: #666; }
        .alert { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Votre bulletin de paie</h2>
    </div>
    <div class="content">
        <p>Bonjour <strong><?php echo $staff_name; ?></strong>,</p>

        <div class="alert">
            <p>Votre bulletin de paie pour le mois de <strong><?php echo date('F Y'); ?></strong> est disponible en pièce jointe.</p>
        </div>

        <p>Vous trouverez ci-joint le fichier PDF de votre bulletin de paie.</p>

        <p>Pour toute question concernant votre bulletin, veuillez contacter le service administratif.</p>

        <p>Cordialement,<br>Le service administratif</p>
    </div>
    <div class="footer">
        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
    </div>
</div>
</body>
</html>