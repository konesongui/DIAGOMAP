<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #3c8dbc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #3c8dbc;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .no-print {
            display: none;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
        /* Pour l'impression, on force les bordures */
        @media print {
            table { border-collapse: collapse; }
            th, td { border: 1px solid #000; }
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Liste des sanctions disciplinaires</h1>
    <p><strong>Employé :</strong> <?php echo htmlspecialchars($empname, ENT_QUOTES, 'UTF-8'); ?></p>
</div>

<?php if (empty($sanctions)) { ?>
    <p>Aucune sanction enregistrée pour cet employé.</p>
<?php } else { ?>
    <table>
        <thead>
        <tr>
            <th>N°</th>
            <th>Titre</th>
            <th>Action</th>
            <th>Motif</th>
            <th>Date</th>
        </tr>
        </thead>
        <tbody>
        <?php $i = 1; foreach ($sanctions as $s) { ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $s['designation']; ?></td>
                <td><?php echo $s['action']; ?></td>
                <td><?php echo $s['reason']; ?></td>
                <td><?php echo $s['date']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>

<div class="footer">
    <p>Document imprimé le <?php echo date('d/m/Y à H:i'); ?></p>
</div>

<!-- Boutons d'action (non imprimés) -->
<div class="no-print" style="text-align:center; margin-top:20px;">
    <button onclick="window.print();" class="btn btn-primary" style="padding:8px 20px; background:#3c8dbc; color:white; border:none; border-radius:4px; cursor:pointer;">
        <i class="fa fa-print"></i> Imprimer
    </button>
    <button onclick="window.close();" class="btn btn-default" style="padding:8px 20px; background:#f4f4f4; border:1px solid #ccc; border-radius:4px; cursor:pointer; margin-left:10px;">
        <i class="fa fa-times"></i> Fermer
    </button>
</div>

<!-- Script pour lancer automatiquement l'impression -->
<script type="text/javascript">
    window.onload = function() {
        // Petit délai pour laisser la page se charger complètement
        setTimeout(function() {
            window.print();
        }, 500);
    };
</script>
</body>
</html>