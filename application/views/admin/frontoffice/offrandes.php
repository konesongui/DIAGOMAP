<!DOCTYPE html>
<html>
<head>
    <title>Gestion des offrandes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container" style="padding: 20px;">
    <h1>Gestion des offrandes et dîmes</h1>

    <div class="row">
        <div class="col-md-3">
            <div class="well text-center">
                <h3><?php echo isset($stats['total_montant']) ? number_format($stats['total_montant'], 0, ',', ' ') : 0; ?> FCFA</h3>
                <p>Total</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="well text-center">
                <h3><?php echo isset($stats['today_montant']) ? number_format($stats['today_montant'], 0, ',', ' ') : 0; ?> FCFA</h3>
                <p>Aujourd'hui</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="well text-center">
                <h3><?php echo isset($stats['month_montant']) ? number_format($stats['month_montant'], 0, ',', ' ') : 0; ?> FCFA</h3>
                <p>Ce mois</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="well text-center">
                <h3><?php echo isset($offrandes) ? count($offrandes) : 0; ?></h3>
                <p>Nombre total</p>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Montant</th>
                <th>Date</th>
                <th>Membre</th>
                <th>Statut</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($offrandes)) : ?>
                <?php foreach ($offrandes as $offrande) : ?>
                    <tr>
                        <td><?php echo $offrande['code_transaction'] ?? ''; ?></td>
                        <td><?php echo $offrande['type'] ?? ''; ?></td>
                        <td><?php echo isset($offrande['montant']) ? number_format($offrande['montant'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?></td>
                        <td><?php echo !empty($offrande['date_transaction']) ? date('d/m/Y', strtotime($offrande['date_transaction'])) : ''; ?></td>
                        <td><?php echo $offrande['membre_nom'] ?? 'Anonyme'; ?></td>
                        <td><?php echo $offrande['statut'] ?? ''; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="6" class="text-center">Aucune offrande enregistrée</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>