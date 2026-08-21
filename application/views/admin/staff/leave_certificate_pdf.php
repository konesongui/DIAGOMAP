<?php

$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de congé</title>
    <style>
        .signature-block {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            border-top: 1px dashed #aaa;
            padding-top: 30px;
        }

        .signature-block .sig-item {
            text-align: center;
            width: 45%;
        }

        .signature-block .sig-item .ligne {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            margin: 2cm;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 15px;
        }
        .header .logo {
            max-height: 80px;
            max-width: 100px;
            object-fit: contain;
        }
        .header .infos {
            text-align: right;
            font-size: 13px;
        }
        .header .infos .nom {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header .infos .coords {
            color: #555;
            margin-top: 5px;
        }
        .subject {
            margin: 30px 0;
            font-weight: bold;
        }
        .content {
            margin-top: 30px;
        }
        .signature-block {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            border-top: 1px dashed #aaa;
            padding-top: 30px;
        }
        .signature-block .sig-item {
            text-align: center;
            width: 45%;
        }
        .signature-block .sig-item .ligne {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        .signature-block .sig-item .nom {
            font-weight: bold;
            margin-top: 5px;
        }
        .date {
            margin-bottom: 20px;
        }
        .greeting {
            margin: 20px 0;
        }
        .footer {
            margin-top: 40px;
            font-size: 10px;
            text-align: center;
            color: #888;
        }
        .bold {
            font-weight: bold;
        }
        .signature-info {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>

<!-- ==================== EN‑TÊTE ==================== -->
<div class="header">
    <!-- Logo -->
    <?php
    $logo_src = '';
    if (!empty($sch_setting->admin_logo)) {
        // Chemin absolu vers le fichier (FCPATH est la racine de votre projet CodeIgniter)
        $logo_path = FCPATH . 'uploads/school_content/admin_logo/' . $sch_setting->admin_logo;
        if (file_exists($logo_path)) {
            $logo_data = base64_encode(file_get_contents($logo_path));
            $logo_src = 'data:image/' . pathinfo($logo_path, PATHINFO_EXTENSION) . ';base64,' . $logo_data;
        }
    }
    ?>
    <?php if (!empty($logo_src)): ?>
        <img class="logo" src="<?= $logo_src ?>" alt="Logo entreprise" />
    <?php else: ?>
        <!-- Placeholder si le logo est absent -->
        <div style="width:100px;"></div>
    <?php endif; ?>

    <!-- Coordonnées -->
    <div class="infos">
        <div class="nom">
            <?= strtoupper(!empty($sch_setting->name) ? $sch_setting->name : 'LA RESIDENCE HOTEL SANTA ADELINA') ?>
        </div>
        <div class="coords">
            <?php if (!empty($sch_setting->address)): ?>
                <div><?= $sch_setting->address ?></div>
            <?php endif; ?>
            <?php if (!empty($sch_setting->phone)): ?>
                <div>Tél. : <?= $sch_setting->phone ?></div>
            <?php endif; ?>
            <?php if (!empty($sch_setting->email)): ?>
                <div>Email : <?= $sch_setting->email ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ==================== CORPS ==================== -->
<div class="date">
    Abidjan, le <?php echo $current_date; ?>
</div>

<div class="subject">
    Objet : Autorisation de <?php echo $leave_type->type; ?>
</div>

<div class="greeting">
    <?php echo ($staff->gender == 'Male') ? 'Monsieur,' : 'Madame,'; ?><br>
    <?php echo $staff->name . ' ' . $staff->surname; ?>
</div>
<div class="content">
    <p>Suite à votre demande de congé, nous avons le plaisir de vous informer que vous êtes autorisée à bénéficier de votre autorisation <?php echo strtolower($leave_type->type); ?> pour la période allant du <strong><?php echo $leave_from_formatted; ?></strong> au <strong><?php echo $leave_to_formatted; ?></strong>, soit une durée de <strong><?php echo $leave_days; ?> jours</strong>.</p>
    <p>Vous partirez le <?php echo $leave_from_formatted; ?> après votre service et reviendrez le <?php echo $leave_to_formatted; ?> à partir de 08H.</p>
    <p>Nous vous souhaitons un excellent congé et un bon repos.</p>
</div>

<!-- ==================== SIGNATURES ==================== -->

<table style="width:100%; margin-top:70px; border-collapse:collapse;">
    <tr>
        <td style="width:50%; text-align:center; vertical-align:top;">
            <div style="margin-bottom:50px;">&nbsp;</div>
            <div style="border-top:1px solid #000; width:70%; margin:0 auto;"></div>
            <div style="margin-top:8px; font-weight:bold;">
                Signature de l'employé
            </div>
        </td>

        <td style="width:50%; text-align:center; vertical-align:top;">
            <div style="margin-bottom:50px;">&nbsp;</div>
            <div style="border-top:1px solid #000; width:70%; margin:0 auto;"></div>
            <div style="margin-top:8px; font-weight:bold;">
                <?= strtoupper(!empty($sch_setting->director) ? $sch_setting->director : 'LA RESIDENCE HOTEL SANTA ADELINA') ?>
            </div>
            <div style="font-size:11px; color:#555;">
                <?= $sch_setting->director_title ?>
            </div>
        </td>
    </tr>
</table>
<!-- ==================== PIED DE PAGE ==================== -->
<div class="footer">
    <?= strtoupper(!empty($sch_setting->name) ? $sch_setting->name : 'LE NOM DE L ENTREPRISE') ?>
</div>

</body>
</html>