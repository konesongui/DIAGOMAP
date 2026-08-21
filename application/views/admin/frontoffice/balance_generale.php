<!DOCTYPE html>
<html>
<head>
    <title>Balance generale OHADA</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #273772; border-bottom: 3px solid #273772; padding-bottom: 10px; }
        .info, .equilibre, .non-equilibre { padding: 10px 15px; border-radius: 4px; margin: 15px 0; }
        .info { background: #e8f0fe; }
        .equilibre { background: #e8f5e9; border-left: 4px solid #2e7d32; }
        .non-equilibre { background: #ffebee; border-left: 4px solid #d32f2f; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 13px; }
        th { background: #273772; color: white; padding: 10px; text-align: left; }
        td { padding: 8px 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f5f5f5; }
        .total { background: #f0f0f0; font-weight: bold; }
        .filters { display: flex; gap: 15px; flex-wrap: wrap; margin: 15px 0; padding: 15px; background: #f8f9fa; border-radius: 4px; }
        .filters label { font-weight: bold; }
        .filters input, .filters select { padding: 5px 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { padding: 6px 15px; background: #273772; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn-secondary { background: #6c757d; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 15px; margin: 15px 0; }
        .stat { background: #f8f9fa; padding: 15px; border-radius: 4px; text-align: center; border-left: 4px solid #273772; }
        .stat .number { font-size: 22px; font-weight: bold; color: #273772; }
        .stat .label { font-size: 12px; color: #666; text-transform: uppercase; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
<div class="container">
    <h1>Balance generale OHADA</h1>

    <div class="info">
        <strong>Periode :</strong> <?php echo date('d/m/Y', strtotime($date_debut)); ?> au <?php echo date('d/m/Y', strtotime($date_fin)); ?>
    </div>

    <div class="stats">
        <div class="stat"><div class="number"><?php echo (int) $stats['total_comptes']; ?></div><div class="label">Comptes</div></div>
        <div class="stat"><div class="number"><?php echo (int) $stats['total_mouvements']; ?></div><div class="label">Mouvements</div></div>
        <div class="stat"><div class="number"><?php echo number_format($total_ouverture_debit - $total_ouverture_credit, 0, ',', ' '); ?></div><div class="label">Solde ouverture net</div></div>
        <div class="stat"><div class="number"><?php echo number_format($total_solde_debiteur - $total_solde_crediteur, 0, ',', ' '); ?></div><div class="label">Solde cloture net</div></div>
    </div>

    <div class="filters">
        <div>
            <label>Du :</label>
            <input type="date" id="filterDateDebut" value="<?php echo $date_debut; ?>">
        </div>
        <div>
            <label>Au :</label>
            <input type="date" id="filterDateFin" value="<?php echo $date_fin; ?>">
        </div>
        <div>
            <label>Classe :</label>
            <select id="filterClasse">
                <option value="">Toutes</option>
                <?php foreach ($classes as $cls) : ?>
                    <option value="<?php echo $cls; ?>" <?php echo ($cls == $classe_selected) ? 'selected' : ''; ?>>Classe <?php echo $cls; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn" onclick="applyFilters()">Filtrer</button>
        <button class="btn btn-secondary" onclick="resetFilters()">Reinitialiser</button>
        <button class="btn" onclick="verifierEquilibre()">Verifier equilibre</button>
    </div>

    <?php $diff = $total_debit - $total_credit; ?>
    <div class="<?php echo abs($diff) < 0.0001 ? 'equilibre' : 'non-equilibre'; ?>">
        <strong>Ouverture debit :</strong> <?php echo number_format($total_ouverture_debit, 0, ',', ' '); ?> FCFA |
        <strong>Ouverture credit :</strong> <?php echo number_format($total_ouverture_credit, 0, ',', ' '); ?> FCFA |
        <strong>Mouvements debit :</strong> <?php echo number_format($total_debit, 0, ',', ' '); ?> FCFA |
        <strong>Mouvements credit :</strong> <?php echo number_format($total_credit, 0, ',', ' '); ?> FCFA |
        <strong>Cloture debit :</strong> <?php echo number_format($total_solde_debiteur, 0, ',', ' '); ?> FCFA |
        <strong>Cloture credit :</strong> <?php echo number_format($total_solde_crediteur, 0, ',', ' '); ?> FCFA
        <br>
        <?php echo abs($diff) < 0.0001 ? 'Balance equilibree' : 'Balance non equilibree (ecart: ' . number_format($diff, 0, ',', ' ') . ' FCFA)'; ?>
    </div>

    <?php if (empty($balance)) : ?>
        <p style="text-align:center;padding:40px 0;color:#999;">Aucune donnee pour cette periode</p>
    <?php else : ?>
        <table>
            <thead>
            <tr>
                <th>Compte</th>
                <th>Libelle</th>
                <th>Classe</th>
                <th class="text-right">Ouverture debit</th>
                <th class="text-right">Ouverture credit</th>
                <th class="text-right">Mouvement debit</th>
                <th class="text-right">Mouvement credit</th>
                <th class="text-right">Cloture debit</th>
                <th class="text-right">Cloture credit</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($balance as $row) : ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['compte']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['libelle']); ?></td>
                    <td><?php echo htmlspecialchars($row['classe']); ?></td>
                    <td class="text-right"><?php echo number_format($row['solde_ouverture_debit'], 0, ',', ' '); ?></td>
                    <td class="text-right"><?php echo number_format($row['solde_ouverture_credit'], 0, ',', ' '); ?></td>
                    <td class="text-right"><?php echo number_format($row['mouvement_debit'], 0, ',', ' '); ?></td>
                    <td class="text-right"><?php echo number_format($row['mouvement_credit'], 0, ',', ' '); ?></td>
                    <td class="text-right"><?php echo number_format($row['solde_cloture_debit'], 0, ',', ' '); ?></td>
                    <td class="text-right"><?php echo number_format($row['solde_cloture_credit'], 0, ',', ' '); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="total">
                <td colspan="3" class="text-right">TOTAUX</td>
                <td class="text-right"><?php echo number_format($total_ouverture_debit, 0, ',', ' '); ?></td>
                <td class="text-right"><?php echo number_format($total_ouverture_credit, 0, ',', ' '); ?></td>
                <td class="text-right"><?php echo number_format($total_debit, 0, ',', ' '); ?></td>
                <td class="text-right"><?php echo number_format($total_credit, 0, ',', ' '); ?></td>
                <td class="text-right"><?php echo number_format($total_solde_debiteur, 0, ',', ' '); ?></td>
                <td class="text-right"><?php echo number_format($total_solde_crediteur, 0, ',', ' '); ?></td>
            </tr>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
function applyFilters() {
    var date_debut = document.getElementById('filterDateDebut').value;
    var date_fin = document.getElementById('filterDateFin').value;
    var classe = document.getElementById('filterClasse').value;
    var url = '<?php echo base_url("admin/frontoffice/balance_generale"); ?>?date_debut=' + date_debut + '&date_fin=' + date_fin;
    if (classe) { url += '&classe=' + classe; }
    window.location.href = url;
}

function resetFilters() {
    window.location.href = '<?php echo base_url("admin/frontoffice/balance_generale"); ?>';
}

function verifierEquilibre() {
    var date_debut = document.getElementById('filterDateDebut').value;
    var date_fin = document.getElementById('filterDateFin').value;
    window.location.href = '<?php echo base_url("admin/frontoffice/balance_generale/verifier"); ?>?date_debut=' + date_debut + '&date_fin=' + date_fin;
}
</script>
</body>
</html>
