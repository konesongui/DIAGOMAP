<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document d'acceptation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #fff;
            padding: 30px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #6C63FF;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            color: #2D3436;
        }
        .header .subtitle {
            font-size: 16px;
            color: #6C63FF;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 5px;
        }
        .header .info {
            font-size: 13px;
            color: #7f8c8d;
            margin-top: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 25px;
            border-radius: 30px;
            font-weight: bold;
            font-size: 16px;
            text-transform: uppercase;
            margin: 15px 0;
            background: #27ae60;
            color: white;
        }
        .info-section {
            margin: 25px 0;
        }
        .info-section h2 {
            font-size: 18px;
            color: #2D3436;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 30px;
        }
        .info-item {
            padding: 8px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .info-item .label {
            font-size: 11px;
            color: #7f8c8d;
            text-transform: uppercase;
            display: block;
        }
        .info-item .value {
            font-size: 15px;
            color: #2D3436;
            font-weight: 500;
        }
        .message-box {
            background: #f8f9fa;
            padding: 20px;
            border-left: 4px solid #6C63FF;
            margin: 25px 0;
        }
        .message-box h3 {
            color: #6C63FF;
            margin-bottom: 10px;
        }
        .signature {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #eee;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 30px;
            text-align: center;
        }
        .signature .line {
            border-bottom: 1px solid #333;
            width: 150px;
            margin: 30px auto 8px auto;
        }
        .signature .label {
            font-size: 12px;
            color: #7f8c8d;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            text-align: center;
            font-size: 12px;
            color: #95a5a6;
        }
        .no-print {
            text-align: right;
            margin-bottom: 20px;
        }
        .print-btn {
            background: #6C63FF;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }
        .print-btn:hover {
            background: #5A52D5;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 10px; }
            .container { border: none; padding: 20px; }
            .status-badge { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Bouton d'impression -->
    <div class="no-print">
        <button onclick="window.print()" class="print-btn">
            <i class="fa fa-print"></i> Imprimer
        </button>
        <button onclick="window.close()" class="print-btn" style="background: #95a5a6; margin-left: 10px;">
            <i class="fa fa-times"></i> Fermer
        </button>
    </div>

    <!-- En-tête -->
    <div class="header">
        <h1><?php echo $school_name; ?></h1>
        <div class="subtitle">Document d'acceptation</div>
        <div class="info">
            <i class="fa fa-map-marker"></i> <?php echo $school_address; ?> &nbsp;|&nbsp;
            <i class="fa fa-phone"></i> <?php echo $school_phone; ?> &nbsp;|&nbsp;
            <i class="fa fa-envelope"></i> <?php echo $school_email; ?>
        </div>
    </div>

    <!-- Statut -->
    <div style="text-align: center;">
            <span class="status-badge">
                <i class="fa fa-check-circle"></i> <?php echo ucfirst($enquiry['status']); ?>
            </span>
    </div>

    <!-- Message -->
    <div class="message-box">
        <h3><i class="fa fa-star"></i> Décision d'acceptation</h3>
        <p>Nous avons le plaisir de vous informer que votre demande d'admission a été <strong>acceptée</strong>.</p>
        <p>Votre dossier a été examiné avec attention.</p>
    </div>

    <!-- Informations -->
    <div class="info-section">
        <h2><i class="fa fa-user"></i> Informations du candidat</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Nom complet</span>
                <span class="value"><?php echo $enquiry['name']; ?></span>
            </div>
            <div class="info-item">
                <span class="label">Téléphone</span>
                <span class="value"><?php echo $enquiry['contact']; ?></span>
            </div>
            <div class="info-item">
                <span class="label">Email</span>
                <span class="value"><?php echo !empty($enquiry['email']) ? $enquiry['email'] : 'Non renseigné'; ?></span>
            </div>
            <div class="info-item">
                <span class="label">Référence</span>
                <span class="value"><?php echo !empty($enquiry['reference']) ? $enquiry['reference'] : 'Non renseigné'; ?></span>
            </div>
            <div class="info-item">
                <span class="label">Motif</span>
                <span class="value"><?php echo $enquiry['source']; ?></span>
            </div>
            <div class="info-item">
                <span class="label">Date de la demande</span>
                <span class="value"><?php echo date('d/m/Y', strtotime($enquiry['date'])); ?></span>
            </div>
        </div>
    </div>

    <!-- Signature -->
    <div class="signature">
        <div>
            <div class="line"></div>
            <span class="label">Signature du candidat</span>
        </div>
        <div>
            <div class="line"></div>
            <span class="label">Signature du responsable</span>
        </div>
        <div>
            <div class="line"></div>
            <span class="label">Cachet de l'établissement</span>
        </div>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        <div>Document généré le <?php echo $print_date; ?></div>
        <div style="margin-top: 5px;">Ce document est officiel</div>
    </div>
</div>
</body>
</html>