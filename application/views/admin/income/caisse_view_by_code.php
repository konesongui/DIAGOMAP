<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background: #f5f5f5; }
        .caisse-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-value { font-size: 24px; font-weight: bold; }
        .filter-form {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .table-operations {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .table-operations th { background: #667eea; color: white; }
        .text-entree { color: #28a745; font-weight: bold; }
        .text-sortie { color: #dc3545; font-weight: bold; }
        .btn-logout {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
        }
        .btn-logout:hover { background: rgba(255,255,255,0.3); color: white; }
    </style>
</head>
<body>
<div class="container mt-4">
    <!-- En-tête -->
    <div class="caisse-header">
        <div class="row">
            <div class="col-md-8">
                <h2><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($caisse['name']); ?></h2>
                <p class="mb-0">
                    <i class="fas fa-qrcode"></i> Code: <code><?php echo htmlspecialchars($caisse['caisse_code']); ?></code>
                </p>
            </div>
            <div class="col-md-4 text-right">
                <a href="<?php echo base_url('admin/income/logout_caisse_access'); ?>" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Quitter
                </a>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Total Entrées</div>
                <div class="stat-value text-success">
                    <?php echo number_format($totaux['total_entrees'], 0, ',', ' '); ?> FCFA
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Total Sorties</div>
                <div class="stat-value text-danger">
                    <?php echo number_format($totaux['total_sorties'], 0, ',', ' '); ?> FCFA
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Solde Net</div>
                <div class="stat-value <?php echo $totaux['solde_net'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                    <?php echo number_format($totaux['solde_net'], 0, ',', ' '); ?> FCFA
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filter-form">
        <form method="get" class="form-inline justify-content-between">
            <div>
                <label for="date_debut">Du:</label>
                <input type="date" name="date_debut" id="date_debut" class="form-control ml-2 mr-3"
                       value="<?php echo $date_debut; ?>">
                <label for="date_fin">Au:</label>
                <input type="date" name="date_fin" id="date_fin" class="form-control ml-2"
                       value="<?php echo $date_fin; ?>">
            </div>
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                <a href="<?php echo base_url('admin/income/view_by_code'); ?>" class="btn btn-secondary">
                    <i class="fas fa-refresh"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Tableau des opérations -->
    <div class="table-responsive table-operations">
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>RÉFÉRENCE</th>
                <th>DATE</th>
                <th>DÉSIGNATION</th>
                <th>NOM</th>
                <th>CATÉGORIE</th>
                <th>MODE PAIEMENT</th>
                <th>ENTRÉE</th>
                <th>SORTIE</th>
                <th>SOLDE AVANT</th>
                <th>SOLDE APRÈS</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $solde_courant = $solde_initial;
            $total_entrees = 0;
            $total_sorties = 0;

            if (!empty($operations)):
                foreach ($operations as $op):
                    $entree = floatval($op['entree']);
                    $sortie = floatval($op['sortie']);
                    $solde_avant = $solde_courant;
                    $solde_apres = $solde_courant + $entree - $sortie;

                    $total_entrees += $entree;
                    $total_sorties += $sortie;
                    $solde_courant = $solde_apres;
                    ?>
                    <tr>
                        <td><code><?php echo htmlspecialchars($op['reference'] ?? 'N/A'); ?></code></td>
                        <td><?php echo date('d/m/Y', strtotime($op['date'])); ?></td>
                        <td><?php echo htmlspecialchars($op['designation'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($op['nom'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($op['category_name'] ?? $op['category'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($op['mode_paiement'] ?? ''); ?></td>
                        <td class="text-entree">
                            <?php if ($entree > 0): ?>
                                <?php echo number_format($entree, 0, ',', ' '); ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="text-sortie">
                            <?php if ($sortie > 0): ?>
                                <?php echo number_format($sortie, 0, ',', ' '); ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?php echo number_format($solde_avant, 0, ',', ' '); ?></td>
                        <td class="<?php echo $solde_apres >= 0 ? 'text-success' : 'text-danger'; ?>">
                            <?php echo number_format($solde_apres, 0, ',', ' '); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Aucune opération trouvée
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
            <?php if (!empty($operations)): ?>
                <tfoot>
                <tr style="background: #f8f9fa; font-weight: bold;">
                    <td colspan="6" class="text-right">TOTAUX:</td>
                    <td class="text-entree"><?php echo number_format($total_entrees, 0, ',', ' '); ?> FCFA</td>
                    <td class="text-sortie"><?php echo number_format($total_sorties, 0, ',', ' '); ?> FCFA</td>
                    <td colspan="2"></td>
                </tr>
                <tr style="background: #e8f4fd;">
                    <td colspan="9" class="text-right"><strong>SOLDE FINAL:</strong></td>
                    <td class="<?php echo $solde_courant >= 0 ? 'text-success' : 'text-danger'; ?>">
                        <strong><?php echo number_format($solde_courant, 0, ',', ' '); ?> FCFA</strong>
                    </td>
                </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>

    <div class="text-center mt-4 text-muted">
        <small><i class="fas fa-lock"></i> Accès restreint - Seules les opérations de cette caisse sont visibles</small>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>