<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Document d'acceptation de permission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .school-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .school-address {
            font-size: 14px;
            color: #7f8c8d;
        }
        .document-title {
            font-size: 22px;
            font-weight: bold;
            margin: 30px 0;
            text-align: center;
            text-decoration: underline;
            color: #27ae60;
        }
        .document-ref {
            text-align: right;
            margin-bottom: 20px;
            font-size: 14px;
            color: #7f8c8d;
        }
        .content {
            margin: 30px 0;
        }
        .intro-text {
            font-size: 16px;
            margin-bottom: 25px;
            text-align: justify;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            box-shadow: 0 2px 3px rgba(0,0,0,0.1);
        }
        .info-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }
        .info-table .label {
            font-weight: bold;
            width: 30%;
            background-color: #f8f9fa;
            color: #2c3e50;
        }
        .info-table .value {
            background-color: #ffffff;
        }
        .status-approved {
            color: #27ae60;
            font-weight: bold;
            font-size: 18px;
            text-transform: uppercase;
        }
        .approval-stamp {
            display: inline-block;
            padding: 5px 15px;
            border: 2px solid #27ae60;
            color: #27ae60;
            font-weight: bold;
            border-radius: 5px;
            margin: 10px 0;
        }
        .signature {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 10px;
            text-align: center;
            font-size: 14px;
            color: #7f8c8d;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #95a5a6;
            border-top: 1px solid #ecf0f1;
            padding-top: 20px;
        }
        .validity-period {
            background-color: #f1f9f1;
            padding: 10px;
            border-left: 4px solid #27ae60;
            margin: 20px 0;
            font-size: 14px;
        }
        .print-button {
            display: block;
            margin: 20px auto;
            padding: 12px 30px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .print-button:hover {
            background-color: #2980b9;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 20px;
                background: white;
            }
            .info-table {
                box-shadow: none;
            }
            .print-button {
                display: none;
            }
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(39, 174, 96, 0.1);
            white-space: nowrap;
            pointer-events: none;
            z-index: 1000;
        }
    </style>
</head>
<body>
<!-- Filigrane APPROUVÉ -->
<div class="watermark no-print">APPROUVÉ</div>

<!-- Bouton d'impression (visible seulement à l'écran) -->
<div class="no-print" style="text-align: center; margin-bottom: 20px;">
    <button onclick="window.print()" class="print-button">
        <i class="fa fa-print"></i> Imprimer ce document
    </button>
    <button onclick="window.close()" class="print-button" style="background-color: #95a5a6; margin-left: 10px;">
        <i class="fa fa-times"></i> Fermer
    </button>
</div>

<!-- En-tête du document -->
<div class="header">
    <div class="school-name"><?php echo $school['name']; ?></div>
    <div class="school-address"><?php echo $school['address']; ?></div>
    <div class="school-address">Tél: <?php echo $school['phone']; ?> | Email: <?php echo $school['email']; ?></div>
</div>

<!-- Référence du document -->
<div class="document-ref">
    N° Réf: PERM-<?php echo str_pad($enquiry['id'], 5, '0', STR_PAD_LEFT); ?> / <?php echo date('Y'); ?><br>
    Date d'émission: <?php echo date('d/m/Y'); ?>
</div>

<!-- Titre du document -->
<div class="document-title">
    DOCUMENT D'ACCEPTATION DE PERMISSION
</div>

<!-- Contenu principal -->
<div class="content">
    <div class="intro-text">
        Nous soussignés, <strong><?php echo $school['name']; ?></strong>, attestons que la demande de permission soumise par :
    </div>

    <!-- Tableau des informations -->
    <table class="info-table">
        <tr>
            <td class="label">Nom complet du demandeur :</td>
            <td class="value"><strong><?php echo $enquiry['name']; ?></strong></td>
        </tr>
        <tr>
            <td class="label">Téléphone :</td>
            <td class="value"><?php echo $enquiry['contact']; ?></td>
        </tr>
        <tr>
            <td class="label">Email :</td>
            <td class="value"><?php echo $enquiry['email'] ?: 'Non renseigné'; ?></td>
        </tr>
        <tr>
            <td class="label">Adresse :</td>
            <td class="value"><?php echo $enquiry['address'] ?: 'Non renseignée'; ?></td>
        </tr>
        <tr>
            <td class="label">Référence :</td>
            <td class="value"><?php echo $enquiry['reference']; ?></td>
        </tr>
        <tr>
            <td class="label">Motif de la permission :</td>
            <td class="value"><?php echo $enquiry['source']; ?></td>
        </tr>
        <tr>
            <td class="label">Date de début :</td>
            <td class="value"><?php echo date('d/m/Y', strtotime($enquiry['date'])); ?></td>
        </tr>
        <tr>
            <td class="label">Date de fin :</td>
            <td class="value"><?php echo date('d/m/Y', strtotime($enquiry['follow_up_date'])); ?></td>
        </tr>
        <tr>
            <td class="label">Description :</td>
            <td class="value"><?php echo nl2br($enquiry['description'] ?: 'Aucune description'); ?></td>
        </tr>
        <tr>
            <td class="label">Note complémentaire :</td>
            <td class="value"><?php echo nl2br($enquiry['note'] ?: 'Aucune note'); ?></td>
        </tr>
    </table>

    <!-- Statut et période de validité -->
    <div class="validity-period">
        <strong>Statut de la demande :</strong>
        <span class="status-approved">APPROUVÉE</span><br>
        <strong>Période de validité :</strong>
        Du <?php echo date('d/m/Y', strtotime($enquiry['date'])); ?> au <?php echo date('d/m/Y', strtotime($enquiry['follow_up_date'])); ?>
    </div>

    <p style="text-align: justify; margin-top: 20px;">
        La présente attestation est délivrée pour servir et valoir ce que de droit.
        Elle certifie que la demande de permission a été examinée et approuvée par
        l'administration de l'établissement conformément au règlement intérieur.
    </p>
</div>

<!-- Espaces pour signatures -->
<div class="signature">
    <div class="signature-box">
        <div class="signature-line">
            Signature du demandeur<br>
            <small>(Précédée de la mention "Lu et approuvé")</small>
        </div>
    </div>
    <div class="signature-box">
        <div class="signature-line">
            Signature du responsable<br>
            <small><?php echo $school['name']; ?></small>
        </div>
    </div>
</div>

<!-- Cachet de l'établissement -->
<div style="text-align: center; margin-top: 30px; font-style: italic; color: #7f8c8d;">
    <div style="border: 1px dashed #bdc3c7; padding: 10px; display: inline-block;">
        Cachet de l'établissement
    </div>
</div>

<!-- Pied de page -->
<div class="footer">
    <p>Document généré le <?php echo date('d/m/Y à H:i'); ?> - Document officiel de <?php echo $school['name']; ?></p>
    <p>Ce document est valable uniquement pour la période indiquée et doit être présenté sur demande.</p>
    <p><?php echo $school['name']; ?> - <?php echo $school['phone']; ?> - <?php echo $school['email']; ?></p>
</div>

<script>
    // Impression automatique (optionnel - décommentez si nécessaire)
    // window.onload = function() {
    //     window.print();
    // }

    // Détection de l'impression
    window.onbeforeprint = function() {
        console.log('Préparation de l\'impression...');
    };

    window.onafterprint = function() {
        console.log('Impression terminée');
    };
</script>
</body>
</html>