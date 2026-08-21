<!-- ============================================================
     PAGE : Rapports d'amortissement
     DESCRIPTION : Interface moderne pour les rapports d'amortissement
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

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        padding: 16px 20px;
        box-shadow: var(--shadow-soft);
        border-left: 5px solid var(--primary-light);
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-hover);
    }

    .stat-card .stat-info h4 {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 500;
        margin: 0 0 4px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-card .stat-info .number {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        line-height: 1.2;
    }

    .stat-card .stat-icon {
        font-size: 28px;
        opacity: 0.7;
    }

    .stat-card:nth-child(1) { border-left-color: #3b82f6; }
    .stat-card:nth-child(2) { border-left-color: #10b981; }
    .stat-card:nth-child(3) { border-left-color: #f59e0b; }
    .stat-card:nth-child(4) { border-left-color: #8b5cf6; }

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

    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        margin-bottom: 24px;
        padding: 14px 18px;
        background: #ffffff;
        border-radius: var(--radius-md);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid var(--border-light);
    }

    .filter-bar .filter-group {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .filter-bar .filter-group label {
        font-size: 13px;
        font-weight: 500;
        color: #475569;
        margin: 0;
        white-space: nowrap;
    }

    .filter-bar .filter-group select,
    .filter-bar .filter-group input {
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-light);
        padding: 6px 14px;
        font-size: 13px;
        background: #ffffff;
        transition: var(--transition);
        min-width: 130px;
        height: 36px;
    }

    .filter-bar .btn-filter {
        background: var(--primary-dark);
        color: #ffffff;
        border: none;
        border-radius: var(--radius-sm);
        padding: 6px 18px;
        font-size: 13px;
        font-weight: 500;
        transition: var(--transition);
        cursor: pointer;
        height: 36px;
    }

    .filter-bar .btn-reset {
        background: #e2e8f0;
        color: #475569;
        border: none;
        border-radius: var(--radius-sm);
        padding: 6px 16px;
        font-size: 13px;
        font-weight: 500;
        transition: var(--transition);
        cursor: pointer;
        height: 36px;
    }

    .export-group {
        display: flex;
        gap: 6px;
        align-items: center;
        margin-left: auto;
    }

    .export-group .export-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-right: 4px;
    }

    .btn-export {
        border: none;
        border-radius: var(--radius-sm);
        padding: 6px 16px;
        font-size: 12px;
        font-weight: 500;
        transition: var(--transition);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .btn-excel {
        background: linear-gradient(135deg, #217346 0%, #1e7e3a 100%);
        color: #ffffff;
        box-shadow: 0 3px 12px rgba(33, 115, 70, 0.3);
    }

    .btn-excel:hover {
        box-shadow: 0 6px 24px rgba(33, 115, 70, 0.4);
        color: #ffffff;
    }

    .btn-pdf {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: #ffffff;
        box-shadow: 0 3px 12px rgba(220, 38, 38, 0.3);
    }

    .btn-pdf:hover {
        box-shadow: 0 6px 24px rgba(220, 38, 38, 0.4);
        color: #ffffff;
    }

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

    .badge-type {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }
    .badge-type.effectif { background: #d1fae5; color: #065f46; }
    .badge-type.previsionnel { background: #fef3c7; color: #92400e; }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .card-modern .card-header { flex-direction: column; align-items: stretch; }
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-bar .filter-group { width: 100%; }
        .filter-bar .filter-group select, .filter-bar .filter-group input { width: 100%; min-width: unset; }
        .export-group { margin-left: 0; width: 100%; flex-wrap: wrap; }
        .btn-export { flex: 1; justify-content: center; padding: 6px 12px; font-size: 11px; }
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
                            <i class="fa fa-line-chart"></i> Rapports d'amortissement
                            <span style="font-size: 14px; font-weight: 400; color: #94a3b8; margin-left: 8px;">
                                (<?php echo isset($stats['total']) ? $stats['total'] : 0; ?>)
                            </span>
                        </h3>
                        <div class="d-flex align-items-center gap-3" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url(); ?>admin/admin/comptabilite" class="btn-back" title="Retour au tableau de bord">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div style="padding: 20px 24px 0;">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h4><i class="fa fa-file-text-o"></i> Total amortissements</h4>
                                    <p class="number"><?php echo isset($stats['total']) ? $stats['total'] : 0; ?></p>
                                </div>
                                <div class="stat-icon"><i class="fa fa-file-text-o"></i></div>
                            </div>
                            <div class="stat-card" style="border-left-color: #10b981;">
                                <div class="stat-info">
                                    <h4><i class="fa fa-check-circle" style="color:#10b981;"></i> Effectifs</h4>
                                    <p class="number"><?php echo isset($stats['effectif']) ? $stats['effectif'] : 0; ?></p>
                                </div>
                                <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
                            </div>
                            <div class="stat-card" style="border-left-color: #f59e0b;">
                                <div class="stat-info">
                                    <h4><i class="fa fa-clock-o" style="color:#f59e0b;"></i> Prévisionnels</h4>
                                    <p class="number"><?php echo isset($stats['previsionnel']) ? $stats['previsionnel'] : 0; ?></p>
                                </div>
                                <div class="stat-icon"><i class="fa fa-clock-o"></i></div>
                            </div>
                            <div class="stat-card" style="border-left-color: #8b5cf6;">
                                <div class="stat-info">
                                    <h4><i class="fa fa-money" style="color:#8b5cf6;"></i> Montant total</h4>
                                    <p class="number" style="font-size: 16px;">
                                        <?php echo isset($stats['total_montant']) ? number_format($stats['total_montant'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                    </p>
                                </div>
                                <div class="stat-icon"><i class="fa fa-money"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Filtres -->
                        <div class="filter-bar">
                            <div class="filter-group">
                                <label><i class="fa fa-calendar"></i> Période :</label>
                                <input type="date" id="filterDateFrom" onchange="applyFilters()" placeholder="Du">
                                <span style="color:#94a3b8;font-size:13px;">→</span>
                                <input type="date" id="filterDateTo" onchange="applyFilters()" placeholder="Au">
                            </div>
                            <div class="filter-group">
                                <label><i class="fa fa-tag"></i> Catégorie :</label>
                                <select id="filterCategory" onchange="applyFilters()">
                                    <option value="">Toutes</option>
                                    <?php if (isset($categories) && is_array($categories)) : ?>
                                        <?php foreach ($categories as $cat) : ?>
                                            <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div style="display:flex;gap:6px;margin-left:auto;flex-wrap:wrap;">
                                <button class="btn-filter" onclick="applyFilters()"><i class="fa fa-search"></i> Filtrer</button>
                                <button class="btn-reset" onclick="resetFilters()"><i class="fa fa-undo"></i> Réinitialiser</button>

                                <div class="export-group">
                                    <span class="export-label"><i class="fa fa-download"></i> Exporter</span>
                                    <button class="btn-export btn-excel" onclick="exportData('excel')">
                                        <i class="fa fa-file-excel-o"></i> CSV
                                    </button>
                                    <button class="btn-export btn-pdf" onclick="exportData('pdf')">
                                        <i class="fa fa-file-pdf-o"></i> PDF
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau -->
                        <div class="table-responsive">
                            <table class="table table-modern" id="amortissementsTable">
                                <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 20%;">Immobilisation</th>
                                    <th style="width: 12%;">Code</th>
                                    <th style="width: 15%;">Catégorie</th>
                                    <th style="width: 13%;">Période</th>
                                    <th style="width: 15%;">Montant</th>
                                    <th style="width: 10%;">Type</th>
                                    <th style="text-align: center; width: 100px;">Action</th>
                                </tr>
                                </thead>
                                <tbody id="amortissementsTableBody">
                                <?php if (empty($amortissements)) : ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted" style="padding: 60px 0;">
                                            <i class="fa fa-line-chart" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
                                            <p style="font-size: 16px; color: #64748b;">Aucun amortissement enregistré</p>
                                            <p style="font-size: 13px; color: #94a3b8;">Les amortissements seront générés automatiquement</p>
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($amortissements as $index => $amort) : ?>
                                        <tr data-date_debut="<?php echo isset($amort['periode_debut']) ? $amort['periode_debut'] : ''; ?>"
                                            data-date_fin="<?php echo isset($amort['periode_fin']) ? $amort['periode_fin'] : ''; ?>"
                                            data-categorie="<?php echo isset($amort['categorie']) ? $amort['categorie'] : ''; ?>"
                                            data-id="<?php echo isset($amort['id']) ? $amort['id'] : ''; ?>">

                                            <td><?php echo $index + 1; ?></td>

                                            <td>
                                                <strong><?php echo isset($amort['immobilisation_nom']) ? htmlspecialchars($amort['immobilisation_nom']) : '—'; ?></strong>
                                                <?php if (!empty($amort['description'])) : ?>
                                                    <br>
                                                    <small style="color: #94a3b8; font-size: 11px;">
                                                        <?php echo isset($amort['description']) ? htmlspecialchars(substr($amort['description'], 0, 40)) : ''; ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                    <span style="background: #f1f5f9; padding: 2px 10px; border-radius: 4px; font-size: 11px; color: #475569;">
                                                        <?php echo isset($amort['immobilisation_code']) ? htmlspecialchars($amort['immobilisation_code']) : '—'; ?>
                                                    </span>
                                            </td>

                                            <td>
                                                    <span style="background: #f1f5f9; padding: 3px 12px; border-radius: 12px; font-size: 11px; color: #475569;">
                                                        <?php echo isset($amort['categorie']) ? htmlspecialchars($amort['categorie']) : '—'; ?>
                                                    </span>
                                            </td>

                                            <td style="font-size: 12px; color: #64748b;">
                                                <?php if (!empty($amort['periode_debut']) && !empty($amort['periode_fin'])) : ?>
                                                    <?php echo date('d/m/Y', strtotime($amort['periode_debut'])); ?>
                                                    <span style="color: #94a3b8; font-size: 10px;">→</span>
                                                    <?php echo date('d/m/Y', strtotime($amort['periode_fin'])); ?>
                                                <?php else : ?>
                                                    —
                                                <?php endif; ?>
                                            </td>

                                            <td style="font-weight: 600; color: #f59e0b;">
                                                <?php echo isset($amort['montant']) ? number_format($amort['montant'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                            </td>

                                            <td>
                                                    <span class="badge-type <?php echo isset($amort['type']) ? $amort['type'] : 'effectif'; ?>">
                                                        <?php echo isset($amort['type']) ? ucfirst($amort['type']) : 'Effectif'; ?>
                                                    </span>
                                            </td>

                                            <td style="text-align: center;">
                                                <a onclick="viewDetails(<?php echo isset($amort['id']) ? $amort['id'] : 0; ?>)"
                                                   style="cursor: pointer; color: #3b82f6; margin: 0 4px;">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Détails -->
<div id="amortissementDetailsModal" class="modal fade modal-chic" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 800px;">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; border-radius: 8px 8px 0 0;">
                <h4 style="margin: 0; color: white;">
                    <i class="fa fa-info-circle" style="color: #60a5fa;"></i> Détails de l'amortissement
                </h4>
                <button type="button" class="close" data-dismiss="modal" style="color:white; opacity: 0.8;">&times;</button>
            </div>
            <div class="modal-body" id="detailsContent" style="padding: 24px; background: #fafcff;"></div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- SCRIPTS                                    -->
<!-- ========================================== -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    // ========================================== //
    // FILTRES                                    //
    // ========================================== //
    function applyFilters() {
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;
        const category = document.getElementById('filterCategory').value;

        const rows = document.querySelectorAll('#amortissementsTableBody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            if (row.cells.length === 1 && row.cells[0].colSpan === 8) return;

            const rowDateFrom = row.dataset.date_debut || '';
            const rowDateTo = row.dataset.date_fin || '';
            const rowCategory = row.dataset.categorie || '';

            let show = true;
            if (dateFrom && rowDateFrom < dateFrom) show = false;
            if (dateTo && rowDateTo > dateTo) show = false;
            if (category && rowCategory !== category) show = false;

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        let noResult = document.getElementById('noResultMessage');
        if (!noResult) {
            noResult = document.createElement('tr');
            noResult.id = 'noResultMessage';
            noResult.innerHTML = '<td colspan="8" class="text-center text-muted" style="padding:40px 0;">Aucun amortissement ne correspond aux filtres</td>';
            document.querySelector('#amortissementsTableBody').appendChild(noResult);
        }
        noResult.style.display = visibleCount === 0 ? '' : 'none';
    }

    function resetFilters() {
        document.getElementById('filterDateFrom').value = '';
        document.getElementById('filterDateTo').value = '';
        document.getElementById('filterCategory').value = '';
        applyFilters();
    }

    // ========================================== //
    // EXPORTATION                                //
    // ========================================== //
    function exportData(type) {
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;
        const category = document.getElementById('filterCategory').value;

        const params = `?date_from=${encodeURIComponent(dateFrom)}&date_to=${encodeURIComponent(dateTo)}&categorie=${encodeURIComponent(category)}`;

        var url = '<?php echo base_url("admin/amortissements/export_"); ?>' + type + params;
        window.location.href = url;
    }

    // ========================================== //
    // VOIR DÉTAILS                               //
    // ========================================== //
    function viewDetails(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/amortissements/details/' + id,
            success: function(result) {
                $('#detailsContent').html(result);
                $('#amortissementDetailsModal').modal('show');
            },
            error: function() {
                alert('Impossible de charger les détails');
            }
        });
    }
</script>