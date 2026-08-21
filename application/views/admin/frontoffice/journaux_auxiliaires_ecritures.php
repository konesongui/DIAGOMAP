<!-- ============================================================
     PAGE : DÉTAILS DU JOURNAL - ÉCRITURES COMPTABLES
     DESCRIPTION : Liste des écritures d'un journal spécifique
     ============================================================ -->

<style>
    :root {
        --primary-dark: #273772;
        --primary-light: #3b82f6;
        --primary-gradient: linear-gradient(135deg, #273772 0%, #1a2558 100%);
        --bg-light: #f8fafc;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --border-light: #e2e8f0;
        --shadow-soft: 0 8px 30px rgba(0, 0, 0, 0.06);
        --shadow-hover: 0 12px 40px rgba(0, 0, 0, 0.1);
        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 8px;
        --transition: all 0.25s ease;
    }

    .content-wrapper {
        background: #f1f5f9;
        padding: 20px 15px;
        min-height: 100vh;
    }

    .card-modern {
        border: none;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        background: #ffffff;
        margin-bottom: 30px;
        overflow: hidden;
    }

    .card-modern .card-header {
        background: var(--primary-gradient);
        padding: 18px 24px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .card-modern .card-header h3 {
        color: #ffffff;
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-modern .card-header h3 i {
        color: #60a5fa;
    }

    .card-modern .card-body {
        padding: 20px 24px;
        background: var(--bg-light);
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 30px;
        padding: 5px 16px;
        font-size: 13px;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.5);
        text-decoration: none;
    }

    .info-journal {
        background: #ffffff;
        border-radius: var(--radius-md);
        padding: 20px 24px;
        margin-bottom: 24px;
        border: 1px solid var(--border-light);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .info-journal .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-journal .info-item .label {
        font-size: 11px;
        text-transform: uppercase;
        color: var(--text-muted);
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .info-journal .info-item .value {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-dark);
        margin-top: 2px;
    }

    .info-journal .info-item .value .badge-ohada {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-ohada.ACHATS { background: #dbeafe; color: #1e40af; }
    .badge-ohada.VENTES { background: #d1fae5; color: #065f46; }
    .badge-ohada.BANQUE { background: #dbeafe; color: #1e40af; }
    .badge-ohada.CAISSE { background: #fef3c7; color: #92400e; }
    .badge-ohada.PAIE { background: #fef2f2; color: #991b1b; }
    .badge-ohada.OPD { background: #e2e8f0; color: #475569; }
    .badge-ohada.A-NOUVEAUX { background: #1e293b; color: #ffffff; }
    .badge-ohada.AUTRE { background: #e2e8f0; color: #475569; }

    .stats-mini {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    .stats-mini .stat-item {
        background: #ffffff;
        border-radius: var(--radius-sm);
        padding: 12px 16px;
        border: 1px solid var(--border-light);
        text-align: center;
    }

    .stats-mini .stat-item .number {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-dark);
    }

    .stats-mini .stat-item .label {
        font-size: 11px;
        text-transform: uppercase;
        color: var(--text-muted);
        font-weight: 500;
    }

    .stats-mini .stat-item.total { border-left: 4px solid #3b82f6; }
    .stats-mini .stat-item.debit { border-left: 4px solid #ef4444; }
    .stats-mini .stat-item.credit { border-left: 4px solid #10b981; }
    .stats-mini .stat-item.solde { border-left: 4px solid #8b5cf6; }

    .table-modern {
        border-collapse: separate;
        border-spacing: 0 4px;
        width: 100%;
        margin-bottom: 0;
    }

    .table-modern thead th {
        background: #f1f5f9;
        color: var(--text-dark);
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 14px;
        border: none;
        border-bottom: 2px solid var(--border-light);
        white-space: nowrap;
    }

    .table-modern tbody td {
        background: #ffffff;
        padding: 10px 14px;
        border: none;
        border-bottom: 1px solid #eef2f6;
        vertical-align: middle;
        font-size: 13px;
        color: var(--text-dark);
    }

    .table-modern tbody tr:hover td {
        background: #f8fafc;
        transition: background 0.15s ease;
    }

    .table-modern tbody tr td:last-child {
        font-weight: 600;
    }

    .table-modern tbody tr td.debit {
        color: #ef4444;
    }

    .table-modern tbody tr td.credit {
        color: #10b981;
    }

    .text-muted-empty {
        color: #94a3b8;
        font-size: 14px;
        padding: 40px 0;
        text-align: center;
    }

    .btn-export {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 30px;
        padding: 5px 16px;
        font-size: 13px;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-export:hover {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.5);
        text-decoration: none;
    }

    @media (max-width: 768px) {
        .info-journal { grid-template-columns: 1fr; }
        .stats-mini { grid-template-columns: 1fr 1fr; }
        .card-modern .card-header { flex-direction: column; align-items: stretch; }
        .btn-back, .btn-export { width: 100%; justify-content: center; }
        .table-modern thead th, .table-modern tbody td { padding: 8px 10px; font-size: 12px; }
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card-modern">
                    <div class="card-header">
                        <h3>
                            <i class="fa fa-book"></i>
                            <span id="journalCode"><?php echo isset($journal['code']) ? htmlspecialchars($journal['code']) : ''; ?></span>
                            <span style="font-size: 14px; font-weight: 400; color: #94a3b8; margin-left: 8px;">
                                - Écritures comptables
                            </span>
                            <span style="font-size: 12px; font-weight: 400; color: #94a3b8; margin-left: 8px;">
                                (<?php echo isset($ecritures) ? count($ecritures) : 0; ?> écritures)
                            </span>
                        </h3>
                        <div class="d-flex align-items-center gap-3" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url('admin/frontoffice/journaux_auxiliaires'); ?>" class="btn-back">
                                <i class="fa fa-arrow-left"></i> Retour aux journaux
                            </a>
                            <a href="#" class="btn-export" onclick="exportEcritures()">
                                <i class="fa fa-file-excel-o"></i> Export CSV
                            </a>
                        </div>
                    </div>

                    <div class="card-body">

                        <!-- ========== INFO JOURNAL ========== -->
                        <div class="info-journal">
                            <div class="info-item">
                                <span class="label"><i class="fa fa-tag"></i> Code</span>
                                <span class="value" style="font-size: 18px; color: var(--primary-dark);">
                                    <?php echo isset($journal['code']) ? htmlspecialchars($journal['code']) : '-'; ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="label"><i class="fa fa-font"></i> Libellé</span>
                                <span class="value"><?php echo isset($journal['libelle']) ? htmlspecialchars($journal['libelle']) : '-'; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="label"><i class="fa fa-tasks"></i> Type</span>
                                <span class="value">
                                    <span class="badge-ohada <?php echo isset($journal['type']) ? $journal['type'] : 'AUTRE'; ?>">
                                        <?php echo isset($journal['type']) ? htmlspecialchars($journal['type']) : '-'; ?>
                                    </span>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="label"><i class="fa fa-exchange"></i> Compte contrepartie</span>
                                <span class="value">
                                    <?php echo isset($journal['compte_contrepartie']) && !empty($journal['compte_contrepartie']) ? htmlspecialchars($journal['compte_contrepartie']) : '<span style="color: #94a3b8;">-</span>'; ?>
                                </span>
                            </div>
                            <div class="info-item" style="grid-column: span 2;">
                                <span class="label"><i class="fa fa-info-circle"></i> Description</span>
                                <span class="value" style="font-weight: 400; font-size: 14px;">
                                    <?php echo isset($journal['description']) && !empty($journal['description']) ? htmlspecialchars($journal['description']) : '<span style="color: #94a3b8;">Aucune description</span>'; ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="label"><i class="fa fa-toggle-on"></i> Statut</span>
                                <span class="value">
                                    <?php if (isset($journal['actif']) && $journal['actif'] == 1): ?>
                                        <span class="badge badge-success"><i class="fa fa-check"></i> Actif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><i class="fa fa-times"></i> Inactif</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="label"><i class="fa fa-calendar"></i> Date création</span>
                                <span class="value" style="font-weight: 400;">
                                    <?php echo isset($journal['date_creation']) ? date('d/m/Y à H:i', strtotime($journal['date_creation'])) : '-'; ?>
                                </span>
                            </div>
                        </div>

                        <!-- ========== STATISTIQUES ========== -->
                        <div class="stats-mini">
                            <div class="stat-item total">
                                <div class="number"><?php echo isset($ecritures) ? count($ecritures) : 0; ?></div>
                                <div class="label">Total écritures</div>
                            </div>
                            <div class="stat-item debit">
                                <div class="number" style="color: #ef4444;">
                                    <?php echo number_format(isset($total_debit) ? $total_debit : 0, 0, ',', ' '); ?> FCFA
                                </div>
                                <div class="label">Total débits</div>
                            </div>
                            <div class="stat-item credit">
                                <div class="number" style="color: #10b981;">
                                    <?php echo number_format(isset($total_credit) ? $total_credit : 0, 0, ',', ' '); ?> FCFA
                                </div>
                                <div class="label">Total crédits</div>
                            </div>
                            <div class="stat-item solde">
                                <div class="number" style="color: <?php echo (isset($total_debit) && isset($total_credit) && ($total_debit - $total_credit) >= 0) ? '#10b981' : '#ef4444'; ?>;">
                                    <?php
                                    $solde = (isset($total_debit) ? $total_debit : 0) - (isset($total_credit) ? $total_credit : 0);
                                    echo number_format($solde, 0, ',', ' '); ?> FCFA
                                </div>
                                <div class="label">Solde</div>
                            </div>
                        </div>

                        <!-- ========== TABLEAU DES ÉCRITURES ========== -->
                        <?php if (empty($ecritures)) : ?>
                            <div class="text-muted-empty">
                                <i class="fa fa-file-text-o" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
                                <p style="font-size: 16px; color: #64748b;">Aucune écriture dans ce journal</p>
                                <p style="font-size: 13px; color: #94a3b8;">Les écritures apparaîtront ici une fois saisies</p>
                            </div>
                        <?php else : ?>
                            <div class="table-responsive">
                                <table class="table table-modern" id="ecrituresTable">
                                    <thead>
                                    <tr>
                                        <th style="width: 8%;">#</th>
                                        <th style="width: 15%;">Date</th>
                                        <th style="width: 12%;">Pièce</th>
                                        <th style="width: 20%;">Libellé</th>
                                        <th style="width: 15%;">Compte débit</th>
                                        <th style="width: 15%;">Compte crédit</th>
                                        <th style="width: 15%;" class="text-right">Montant (FCFA)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $total = 0;
                                    $counter = 1;
                                    foreach ($ecritures as $ecriture):
                                        $montant = isset($ecriture['montant_debit']) && $ecriture['montant_debit'] > 0
                                            ? $ecriture['montant_debit']
                                            : (isset($ecriture['montant_credit']) ? $ecriture['montant_credit'] : 0);
                                        $isDebit = isset($ecriture['montant_debit']) && $ecriture['montant_debit'] > 0;
                                        $total += $montant;
                                        ?>
                                        <tr>
                                            <td><?php echo $counter++; ?></td>
                                            <td><?php echo isset($ecriture['date_ecriture']) ? date('d/m/Y', strtotime($ecriture['date_ecriture'])) : '-'; ?></td>
                                            <td>
                                                <?php if (isset($ecriture['piece_justificative']) && !empty($ecriture['piece_justificative'])): ?>
                                                    <span class="badge badge-light" style="font-size: 11px;">
                                                            <?php echo htmlspecialchars($ecriture['piece_justificative']); ?>
                                                        </span>
                                                <?php else: ?>
                                                    <span style="color: #94a3b8;">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo isset($ecriture['libelle']) ? htmlspecialchars(substr($ecriture['libelle'], 0, 50)) . (strlen($ecriture['libelle']) > 50 ? '...' : '') : '-'; ?></td>
                                            <td>
                                                <?php if (isset($ecriture['compte_debit']) && !empty($ecriture['compte_debit'])): ?>
                                                    <strong style="color: #ef4444;"><?php echo htmlspecialchars($ecriture['compte_debit']); ?></strong>
                                                <?php else: ?>
                                                    <span style="color: #94a3b8;">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (isset($ecriture['compte_credit']) && !empty($ecriture['compte_credit'])): ?>
                                                    <strong style="color: #10b981;"><?php echo htmlspecialchars($ecriture['compte_credit']); ?></strong>
                                                <?php else: ?>
                                                    <span style="color: #94a3b8;">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right <?php echo $isDebit ? 'debit' : 'credit'; ?>">
                                                <strong>
                                                    <?php echo number_format($montant, 0, ',', ' '); ?> FCFA
                                                    <?php if ($isDebit): ?>
                                                        <span style="font-size: 10px; color: #ef4444;">(D)</span>
                                                    <?php else: ?>
                                                        <span style="font-size: 10px; color: #10b981;">(C)</span>
                                                    <?php endif; ?>
                                                </strong>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <!-- Total ligne -->
                                    <tr style="background: #f1f5f9; font-weight: 700;">
                                        <td colspan="6" class="text-right" style="font-size: 14px; border-top: 3px solid var(--primary-dark);">
                                            TOTAL GÉNÉRAL
                                        </td>
                                        <td class="text-right" style="border-top: 3px solid var(--primary-dark); font-size: 14px; color: var(--primary-dark);">
                                            <?php echo number_format($total, 0, ',', ' '); ?> FCFA
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <!-- ========== INFORMATION OHADA ========== -->
                        <div class="alert alert-info" style="margin-top: 20px; font-size: 13px; background: #eff6ff; border-color: #3b82f6; color: #1e40af;">
                            <i class="fa fa-info-circle"></i>
                            <strong>OHADA :</strong>
                            Chaque écriture doit être justifiée par une pièce justificative numérotée.
                            Le total des débits doit être égal au total des crédits pour chaque journal.
                            <?php if (isset($total_debit) && isset($total_credit) && ($total_debit - $total_credit) != 0): ?>
                                <br>
                                <span style="color: #dc2626; font-weight: 600;">
                                    ⚠️ ATTENTION : Le solde du journal n'est pas équilibré (Débits - Crédits = <?php echo number_format($total_debit - $total_credit, 0, ',', ' '); ?> FCFA)
                                </span>
                            <?php else: ?>
                                <br>
                                <span style="color: #10b981; font-weight: 600;">
                                    ✅ Le journal est équilibré (Débits = Crédits)
                                </span>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- ========================================== -->
<!-- SCRIPTS                                    -->
<!-- ========================================== -->
<script type="text/javascript">
    // ========================================== //
    // EXPORT DES ÉCRITURES EN CSV                //
    // ========================================== //
    function exportEcritures() {
        var code = '<?php echo isset($journal['code']) ? $journal['code'] : 'journal'; ?>';
        var rows = document.querySelectorAll('#ecrituresTable tbody tr');
        var csv = [];

        // En-têtes
        csv.push('Date;Pièce;Libellé;Compte débit;Compte crédit;Montant;Type');

        // Données
        rows.forEach(function(row) {
            // Ignorer la ligne de total
            if (row.cells.length === 7 && row.cells[0].textContent.includes('TOTAL')) {
                return;
            }

            var cols = row.querySelectorAll('td');
            if (cols.length === 7) {
                var date = cols[1].textContent.trim();
                var piece = cols[2].textContent.trim();
                var libelle = cols[3].textContent.trim().replace(/"/g, '""');
                var compteDebit = cols[4].textContent.trim().replace('strong', '').replace(/[<>]/g, '');
                var compteCredit = cols[5].textContent.trim().replace('strong', '').replace(/[<>]/g, '');
                var montant = cols[6].textContent.trim().replace(/\s/g, '').replace('FCFA', '').replace(/\([DC]\)/g, '').trim();
                var type = cols[6].textContent.includes('(D)') ? 'Débit' : 'Crédit';

                csv.push(date + ';' + piece + ';' + libelle + ';' + compteDebit + ';' + compteCredit + ';' + montant + ';' + type);
            }
        });

        var blob = new Blob(['\uFEFF' + csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'ecritures_' + code + '_' + new Date().toISOString().slice(0,10) + '.csv';
        link.click();
    }

    // ========================================== //
    // IMPRESSION                                 //
    // ========================================== //
    function printEcritures() {
        window.print();
    }

    $(document).ready(function() {
        // Ajouter un bouton d'impression si nécessaire
        $('.btn-export').after('<a href="#" class="btn-export" onclick="printEcritures()" style="margin-left: 6px;"><i class="fa fa-print"></i> Imprimer</a>');
    });
</script>

<!-- Styles pour l'impression -->
<style media="print">
    .btn-back, .btn-export, .btn-print {
        display: none !important;
    }
    .card-modern .card-header {
        background: #273772 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .card-modern .card-header h3 {
        color: white !important;
    }
    .badge-ohada {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .stats-mini .stat-item {
        border: 1px solid #e2e8f0 !important;
    }
    .table-modern thead th {
        background: #f1f5f9 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .info-journal {
        border: 1px solid #e2e8f0 !important;
    }
    .alert-info {
        background: #eff6ff !important;
        border-color: #3b82f6 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .stat-item {
        border: 1px solid #e2e8f0 !important;
    }
    .stat-item .number {
        font-size: 16px !important;
    }
    body {
        background: white !important;
        padding: 10px !important;
    }
    .content-wrapper {
        background: white !important;
        padding: 0 !important;
    }
    .card-modern {
        box-shadow: none !important;
        border: 1px solid #e2e8f0 !important;
    }
    .card-modern .card-body {
        background: white !important;
    }
    .table-modern tbody td {
        background: white !important;
    }
    .table-modern tbody tr:hover td {
        background: white !important;
    }
    .table-modern tbody tr td:last-child {
        font-weight: 600;
    }
    .table-modern tbody tr td.debit {
        color: #ef4444 !important;
    }
    .table-modern tbody tr td.credit {
        color: #10b981 !important;
    }
    .badge {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .badge-success {
        background: #10b981 !important;
        color: white !important;
    }
    .badge-danger {
        background: #ef4444 !important;
        color: white !important;
    }
    .badge-light {
        background: #f1f5f9 !important;
        color: #475569 !important;
    }
</style>