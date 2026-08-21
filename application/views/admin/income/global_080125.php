<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord financier - <?= $annee_en_cours ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* ========== STYLES GÉNÉRAUX ========== */
        .content-wrapper { padding:20px; background:#f4f6f9; }

        /* ========== CARTES KPI (SANS COULEUR DE FOND) ========== */
        .small-box {
            border-radius: 16px;
            background: #ffffff !important;
            border: 1px solid #e9ecef;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            padding: 20px 20px 16px 20px;
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
            transition: all 0.2s ease;
        }
        .small-box:hover {
            box-shadow: 0 6px 14px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }
        /* Zone texte (chiffres + libellés) */
        .small-box .inner {
            color: #1e2a3e !important;
            padding-right: 50px;
        }
        .small-box .inner h3 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: #0a1c2f;
            letter-spacing: -0.2px;
        }
        .small-box .inner p {
            font-size: 16px;
            font-weight: 500;
            margin: 0 0 6px 0;
            color: #2c3e50;
        }
        .small-box .inner small {
            font-size: 12px;
            color: #6c7a8a;
            font-weight: 400;
            display: inline-block;
            margin-top: 4px;
        }
        /* Icône (seulement la couleur, pas de fond) */
        .small-box .icon {
            position: absolute;
            top: 18px;
            right: 18px;
            font-size: 48px;
            transition: all 0.2s;
            opacity: 0.75;
        }
        .small-box .icon i {
            background: transparent !important;
            text-shadow: none;
        }
        /* Chaque icône prend sa couleur dédiée */
        .small-box .icon .fa-money { color: #2ecc71; }      /* Liquidité totale : vert */
        .small-box .icon .fa-briefcase { color: #1abc9c; }  /* Total caisse : turquoise */
        .small-box .icon .fa-bank { color: #3498db; }       /* Total banque : bleu */
        .small-box .icon .fa-random { color: #9b59b6; }     /* Transferts : violet */

        /* Suppression de toutes les couleurs de fond héritées */
        .bg-aqua, .bg-green, .bg-yellow, .bg-purple, .bg-red, .bg-teal, .bg-maroon {
            background-color: transparent !important;
        }

        /* Ajustement responsive */
        @media (max-width: 768px) {
            .small-box .inner h3 { font-size: 22px; }
            .small-box .icon { font-size: 40px; top: 14px; right: 14px; }
            .small-box { padding: 16px; }
        }

        /* Autres styles (conservés du design d'origine) */
        .box { border-radius:10px; box-shadow:0 3px 6px rgba(0,0,0,0.1); margin-bottom:20px; background:#fff; border:1px solid #e9ecef; }
        .box-header { padding:12px 15px; border-bottom:1px solid #f0f0f0; }
        .account-card { background: white; border-radius: 12px; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); border: 1px solid #e9ecef; transition: all 0.2s ease; }
        .account-card:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,0,0,0.06); }
        .account-card.caisse { border-left: 4px solid #1abc9c; }
        .account-card.bank { border-left: 4px solid #3498db; }
        .account-balance { font-size: 22px; font-weight: 700; text-align: right; }
        .account-balance.caisse { color: #1abc9c; }
        .account-balance.bank { color: #3498db; }
        .status-badge { padding: 4px 10px; border-radius: 30px; font-size: 11px; font-weight: 600; }
        .status-active { background: #e8f5e9; color: #2e7d32; }
        .status-closed { background: #f5f5f5; color: #616161; }
        .status-locked { background: #fff8e1; color: #f57c00; }
        .date-info { background: #fff; border-radius: 14px; padding: 16px 20px; margin-bottom: 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); border: 1px solid #e9ecef; }
        .chart-container { position: relative; height: 300px; width: 100%; }
        .table > thead > tr > th { border-bottom-width: 1px; background-color: #fafbfc; }
        .label { padding: 5px 10px; font-weight: 500; }
    </style>
</head>
<body>
<div class="content-wrapper">
    <!-- SECTION DATES ET PÉRIODES (inchangée mais épurée) -->
    <section class="content-header">
        <h1 style="margin-top:0;"><i class="fa fa-money" style="color:#2c3e50;"></i> Tableau de bord financier</h1>
        <div class="date-info">
            <div class="date-header" style="font-weight:600; margin-bottom:12px;">Période en cours</div>
            <div class="date-content" style="display:flex; flex-wrap:wrap; gap:15px;">
                <div class="date-item" style="flex:1; text-align:center;"><div class="date-label">Année</div><div class="date-value year" style="font-weight:600;"><?= $annee_en_cours ?></div></div>
                <div class="date-item" style="flex:1; text-align:center;"><div class="date-label">Mois</div><div class="date-value month" style="font-weight:600;"><?= $mois_en_cours ?></div></div>
                <div class="date-item" style="flex:1; text-align:center;"><div class="date-label">Date</div><div class="date-value day" style="font-weight:600;"><?= $date_du_jour ?></div></div>
                <div class="date-item" style="flex:1; text-align:center;"><div class="date-label">Semaine</div><div class="date-value week" style="font-weight:600;">S<?= $semaine_en_cours ?></div></div>
                <div class="date-item" style="flex:1; text-align:center;"><div class="date-label">Dernière activité</div><div class="date-value" style="font-weight:600;"><?= $derniere_operation != 'N/A' ? date('d/m/Y H:i', strtotime($derniere_operation)) : 'Aucune' ?></div></div>
            </div>
        </div>
    </section>

    <section class="content">
        <!-- SECTION CAISSES ET BANQUES (contenu original, gardé intact) -->
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header"><h3 class="box-title"><i class="fa fa-briefcase"></i> Caisses <span class="badge bg-primary"><?= $nb_caisses_actives ?> active(s)</span></h3></div>
                    <div class="box-body">
                        <?php if ($resultCaisses && $resultCaisses->num_rows > 0): ?>
                            <?php $resultCaisses->data_seek(0); ?>
                            <?php while($caisse = $resultCaisses->fetch_assoc()): ?>
                                <?php
                                $amount_re = (float)($caisse['amount_re'] ?? 0);
                                $est_fermee = $caisse['est_fermee'] ?? 0;
                                $is_locked = $caisse['is_locked'] ?? 0;
                                if ($est_fermee == 1) { $status_class = 'status-closed'; $status_text = 'FERMÉE'; $card_class = 'closed'; }
                                elseif ($is_locked == 1) { $status_class = 'status-locked'; $status_text = 'VERROUILLÉE'; $card_class = 'locked'; }
                                else { $status_class = 'status-active'; $status_text = 'ACTIVE'; $card_class = ''; }
                                ?>
                                <div class="account-card caisse <?= $card_class ?>">
                                    <div class="account-header" style="display:flex; justify-content:space-between;">
                                        <div><i class="fa fa-briefcase" style="color:#1abc9c; margin-right:8px;"></i> <strong><?= htmlspecialchars($caisse['name'] ?? 'Caisse') ?></strong><br><small>Dernière op: <?= $caisse['last_operation_date'] ? date('d/m H:i', strtotime($caisse['last_operation_date'])) : '--' ?></small></div>
                                        <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                                    </div>
                                    <div class="account-balance caisse"><?= number_format($amount_re, 0, ",", " ") ?> FCFA</div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?><div class="alert alert-info">Aucune caisse trouvée</div><?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-success">
                    <div class="box-header"><h3 class="box-title"><i class="fa fa-bank"></i> Comptes bancaires <span class="badge bg-success"><?= $nb_banques_actives ?> actif(s)</span></h3></div>
                    <div class="box-body">
                        <?php if ($resultBanques && $resultBanques->num_rows > 0): ?>
                            <?php $resultBanques->data_seek(0); ?>
                            <?php while($banque = $resultBanques->fetch_assoc()): ?>
                                <?php $status_class = ($banque['status'] ?? 0) == 1 ? 'status-active' : 'status-inactive'; $status_text = ($banque['status'] ?? 0) == 1 ? 'ACTIF' : 'INACTIF'; ?>
                                <div class="account-card bank">
                                    <div class="account-header" style="display:flex; justify-content:space-between;">
                                        <div><i class="fa fa-bank" style="color:#3498db; margin-right:8px;"></i> <strong><?= htmlspecialchars($banque['name'] ?? 'Banque') ?></strong><br><small>Créé le: <?= date('d/m/Y', strtotime($banque['created_at'] ?? 'now')) ?></small></div>
                                        <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                                    </div>
                                    <div class="account-balance bank"><?= number_format($banque['balance'] ?? 0, 0, ",", " ") ?> <?= $banque['currency'] ?? 'FCFA' ?></div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?><div class="alert alert-info">Aucun compte bancaire trouvé</div><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== 4 CARTES KPI (SANS COULEUR DE FOND, ICÔNES COLORÉES UNIQUEMENT) ========== -->
        <div class="row">
            <!-- LIQUIDITÉ TOTALE -->
            <div class="col-md-3 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($liquidite_totale, 0, ",", " ") ?> FCFA</h3>
                        <p>Liquidité totale</p>
                        <small>Année <?= $annee_en_cours ?></small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                </div>
            </div>
            <!-- TOTAL EN CAISSE -->
            <div class="col-md-3 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($total_caisse, 0, ",", " ") ?> FCFA</h3>
                        <p>Total en caisse</p>
                        <small><?= $nb_caisses_actives ?> caisse(s) active(s)</small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-briefcase"></i>
                    </div>
                </div>
            </div>
            <!-- TOTAL EN BANQUE -->
            <div class="col-md-3 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($total_banque, 0, ",", " ") ?> FCFA</h3>
                        <p>Total en banque</p>
                        <small><?= $nb_banques_actives ?> compte(s) actif(s)</small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-bank"></i>
                    </div>
                </div>
            </div>
            <!-- TRANSFERTS TOTAUX -->
            <div class="col-md-3 col-sm-6">
                <div class="small-box">
                    <div class="inner">
                        <h3><?= number_format($total_caisse_vers_banque + $total_banque_vers_caisse, 0, ",", " ") ?> FCFA</h3>
                        <p>Transferts totaux</p>
                        <small>Année <?= $annee_en_cours ?></small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-random"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRAPHIQUES & TABLEAUX (conservés à l'identique) -->
        <?php if (!empty($dates_semaines)): ?>
            <div class="row">
                <div class="col-md-8"><div class="box box-info"><div class="box-header"><h3 class="box-title"><i class="fa fa-line-chart"></i> Évolution des opérations bancaires - <?= $annee_en_cours ?></h3></div><div class="box-body"><div class="chart-container"><canvas id="chartOperationsBanque"></canvas></div></div></div></div>
                <div class="col-md-4"><div class="box box-success"><div class="box-header"><h3 class="box-title"><i class="fa fa-pie-chart"></i> Répartition par type - <?= $annee_en_cours ?></h3></div><div class="box-body"><div class="chart-container"><canvas id="chartRepartitionTypes"></canvas></div></div></div></div>
            </div>
        <?php endif; ?>

        <!-- Statistiques par type -->
        <div class="row"><div class="col-md-12"><div class="box box-warning"><div class="box-header"><h3 class="box-title"><i class="fa fa-list"></i> Statistiques par type de transaction - <?= $annee_en_cours ?></h3></div><div class="box-body"><div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Type de transaction</th><th>Nombre d'opérations</th><th>Total montant</th><th>Pourcentage</th></tr></thead><tbody>
                                <?php $total_general = array_sum($total_par_type); if (!empty($types_transactions)): for($i = 0; $i < count($types_transactions); $i++): $pourcentage = $total_general > 0 ? ($total_par_type[$i] / $total_general * 100) : 0; $badge_class = (in_array(strtolower($types_transactions[$i]), ['dépôt', 'dépots', 'crédit', 'virement entrant'])) ? 'success' : ((in_array(strtolower($types_transactions[$i]), ['retrait', 'débit', 'virement sortant'])) ? 'danger' : ((strtolower($types_transactions[$i]) == 'frais bancaires') ? 'warning' : 'info')); ?>
                                    <tr><td><span class="label label-<?= $badge_class ?>"><?= $types_transactions[$i] ?></span></td><td><?= $nombre_operations[$i] ?></td><td><?= number_format($total_par_type[$i], 0, ",", " ") ?> FCFA</td><td><div class="progress" style="margin-bottom:0; height:20px;"><div class="progress-bar progress-bar-<?= $badge_class ?>" style="width: <?= $pourcentage ?>%"><?= number_format($pourcentage, 1) ?>%</div></div></td></tr>
                                <?php endfor; ?>
                                    <tr style="background:#f9f9f9; font-weight:bold;"><td>TOTAL <?= $annee_en_cours ?></td><td><?= array_sum($nombre_operations) ?></td><td><?= number_format($total_general, 0, ",", " ") ?> FCFA</td><td>100%</td></tr>
                                <?php else: ?><tr><td colspan="4" class="text-center">Aucune transaction bancaire pour <?= $annee_en_cours ?></td></tr><?php endif; ?>
                                </tbody></table></div></div></div></div></div>

        <!-- Opérations récentes de caisse -->
        <div class="row"><div class="col-md-12"><div class="box box-teal"><div class="box-header"><h3 class="box-title"><i class="fa fa-exchange"></i> Dernières opérations de caisse</h3><div class="box-tools pull-right"><span class="label label-success">E: <?= number_format($total_entrees_caisse, 0, ",", " ") ?></span> <span class="label label-danger">S: <?= number_format($total_sorties_caisse, 0, ",", " ") ?></span></div></div><div class="box-body"><div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Date</th><th>Caisse</th><th>Type</th><th>Montant</th><th>Description</th></tr></thead><tbody>
                                <?php if ($resultOperationsCaisse && $resultOperationsCaisse->num_rows > 0): while($operation = $resultOperationsCaisse->fetch_assoc()): ?>
                                    <tr><td><?= date('d/m H:i', strtotime($operation['created_at'])) ?></td><td><?= htmlspecialchars($operation['caisse_name'] ?? 'N/A') ?></td><td><span class="label label-<?= $operation['type_operation'] == 'entrée' ? 'success' : 'danger' ?>"><?= htmlspecialchars($operation['type_operation']) ?></span></td><td><?= number_format($operation['montant'], 0, ",", " ") ?> FCFA</td><td><?= htmlspecialchars($operation['designation'] ?? 'N/A') ?></td></tr>
                                <?php endwhile; else: ?><tr><td colspan="5" class="text-center">Aucune opération de caisse</td></tr><?php endif; ?>
                                </tbody></table></div></div></div></div></div>

        <!-- Derniers transferts -->
        <div class="row"><div class="col-md-12"><div class="box box-maroon"><div class="box-header"><h3 class="box-title"><i class="fa fa-random"></i> Derniers transferts</h3><div class="box-tools pull-right"><span class="label label-info">C→B: <?= number_format($total_caisse_vers_banque, 0, ",", " ") ?></span> <span class="label label-warning">B→C: <?= number_format($total_banque_vers_caisse, 0, ",", " ") ?></span></div></div><div class="box-body"><div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Date</th><th>Type</th><th>Montant</th><th>Source</th><th>Destination</th></tr></thead><tbody>
                                <?php if ($resultTransferts && $resultTransferts->num_rows > 0): while($transfert = $resultTransferts->fetch_assoc()): ?>
                                    <tr><td><?= date('d/m H:i', strtotime($transfert['date'])) ?></td><td><span class="label label-<?= $transfert['from_type'] == 'caisse' ? 'info' : 'warning' ?>"><?= ucfirst($transfert['from_type']) ?> → <?= ucfirst($transfert['to_type']) ?></span></td><td><?= number_format($transfert['amount'], 0, ",", " ") ?> FCFA</td><td><?= htmlspecialchars($transfert['source_name'] ?? 'N/A') ?></td><td><?= htmlspecialchars($transfert['destination_name'] ?? 'N/A') ?></td></tr>
                                <?php endwhile; else: ?><tr><td colspan="5" class="text-center">Aucun transfert enregistré</td></tr><?php endif; ?>
                                </tbody></table></div></div></div></div></div>

        <!-- Opérations bancaires récentes -->
        <div class="row"><div class="col-md-12"><div class="box box-red"><div class="box-header"><h3 class="box-title"><i class="fa fa-credit-card"></i> Opérations bancaires récentes - <?= $annee_en_cours ?></h3><div class="box-tools pull-right"><span class="label label-success">Dépôts: <?= number_format($totaux_types['total_depots'] ?? 0, 0, ",", " ") ?></span> <span class="label label-danger">Retraits: <?= number_format($totaux_types['total_retraits'] ?? 0, 0, ",", " ") ?></span></div></div><div class="box-body"><div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Date</th><th>Type</th><th>Montant</th><th>Banque</th><th>Référence</th><th>Description</th></tr></thead><tbody>
                                <?php if ($resultOperationsBanque && $resultOperationsBanque->num_rows > 0): while($operation = $resultOperationsBanque->fetch_assoc()): $type = strtolower($operation['transaction_type']); $badge_class = (in_array($type, ['dépôt', 'dépots', 'crédit', 'virement entrant'])) ? 'success' : ((in_array($type, ['retrait', 'débit', 'virement sortant'])) ? 'danger' : (($type == 'frais bancaires') ? 'warning' : (($type == 'chèque') ? 'info' : 'default'))); ?>
                                    <tr><td><?= date('d/m H:i', strtotime($operation['created_at'])) ?></td><td><span class="label label-<?= $badge_class ?>"><?= htmlspecialchars($operation['transaction_type']) ?></span></td><td><?= number_format($operation['amount'], 0, ",", " ") ?> FCFA</td><td><?= htmlspecialchars($operation['bank_name'] ?? 'N/A') ?></td><td><?= htmlspecialchars($operation['reference'] ?? 'N/A') ?></td><td><?= htmlspecialchars($operation['designation'] ?? 'N/A') ?></td></tr>
                                <?php endwhile; else: ?><tr><td colspan="6" class="text-center">Aucune opération bancaire</td></tr><?php endif; ?>
                                </tbody></table></div></div></div></div></div>
    </section>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
    <?php if (!empty($dates_semaines)): ?>
    new Chart(document.getElementById('chartOperationsBanque').getContext('2d'), {
        type: 'bar', data: { labels: <?= json_encode($dates_semaines) ?>, datasets: [
                { label: 'Dépôts', data: <?= json_encode($depots_semaines) ?>, backgroundColor: 'rgba(75, 192, 192, 0.6)', borderColor: '#4BC0C0', borderWidth: 1 },
                { label: 'Retraits', data: <?= json_encode($retraits_semaines) ?>, backgroundColor: 'rgba(255, 99, 132, 0.6)', borderColor: '#FF6384', borderWidth: 1 },
                { label: 'Frais bancaires', data: <?= json_encode($frais_semaines) ?>, backgroundColor: 'rgba(255, 206, 86, 0.6)', borderColor: '#FFCE56', borderWidth: 1 },
                { label: 'Virements', data: <?= json_encode($virements_semaines) ?>, backgroundColor: 'rgba(153, 102, 255, 0.6)', borderColor: '#9966FF', borderWidth: 1 }
            ] }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Montant (FCFA)' }, ticks: { callback: function(v) { return v.toLocaleString('fr-FR') + ' FCFA'; } } } }, plugins: { tooltip: { callbacks: { label: function(ctx) { return `${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString('fr-FR')} FCFA`; } } } } }
    });
    const typeLabels = ['Dépôts', 'Retraits', 'Frais bancaires', 'Chèques', 'Prélèvements'];
    const typeData = [<?= $totaux_types['total_depots'] ?? 0 ?>, <?= $totaux_types['total_retraits'] ?? 0 ?>, <?= $totaux_types['total_frais'] ?? 0 ?>, <?= $totaux_types['total_cheques'] ?? 0 ?>, <?= $totaux_types['total_prelevements'] ?? 0 ?>];
    const filteredLabels = [], filteredData = [], bgColors = ['#4BC0C0', '#FF6384', '#FFCE56', '#9966FF', '#36A2EB'];
    typeData.forEach((v,i) => { if (v > 0) { filteredLabels.push(typeLabels[i]); filteredData.push(v); } });
    if (filteredData.length) new Chart(document.getElementById('chartRepartitionTypes').getContext('2d'), { type: 'pie', data: { labels: filteredLabels, datasets: [{ data: filteredData, backgroundColor: bgColors.slice(0, filteredLabels.length), borderWidth: 0 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' }, tooltip: { callbacks: { label: (ctx) => { const val = ctx.raw, total = filteredData.reduce((a,b)=>a+b,0), perc = total ? Math.round((val/total)*100) : 0; return `${ctx.label}: ${val.toLocaleString('fr-FR')} FCFA (${perc}%)`; } } } } } });
    <?php endif; ?>

</script>
</body>
</html>