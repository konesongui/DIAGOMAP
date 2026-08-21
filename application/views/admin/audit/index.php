<!-- ============================================================
     PAGE : Journal d'audit - Design modernisé
     DESCRIPTION : Interface moderne avec les mêmes données que l'original
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

    /* ========================================== */
    /* CARTE PRINCIPALE                           */
    /* ========================================== */
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

    .btn-clear {
        background: white;
        border: none;
        color: #dc2626;
        font-weight: 500;
        border-radius: 6px;
        padding: 6px 20px;
        font-size: 13px;
        transition: var(--transition);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-clear:hover {
        background: #dc2626;
        color: #fff;
        box-shadow: 0 6px 18px rgba(220, 38, 38, 0.25);
        transform: translateY(-2px);
    }

    .btn-clear:hover i {
        color: #fff;
    }

    /* ========================================== */
    /* BADGES                                     */
    /* ========================================== */
    .badge-action {
        display: inline-block;
        padding: 3px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }
    .badge-action.insert { background: #d1fae5; color: #065f46; }
    .badge-action.update { background: #dbeafe; color: #1d4ed8; }
    .badge-action.delete { background: #fef2f2; color: #991b1b; }
    .badge-action.login { background: #fef3c7; color: #92400e; }
    .badge-action.logout { background: #e2e8f0; color: #475569; }
    .badge-action.other { background: #f1f5f9; color: #64748b; }

    .badge-platform {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 500;
    }
    .badge-platform.web { background: #dbeafe; color: #1d4ed8; }
    .badge-platform.mobile { background: #fef3c7; color: #92400e; }
    .badge-platform.api { background: #f3e8ff; color: #7c3aed; }

    /* ========================================== */
    /* TABLE                                      */
    /* ========================================== */
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

    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    /* ========================================== */
    /* FILTRES                                    */
    /* ========================================== */
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        margin-bottom: 20px;
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

    .filter-bar .btn-filter:hover {
        background: #1e2a5a;
        transform: translateY(-1px);
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

    .filter-bar .btn-reset:hover {
        background: #cbd5e1;
    }

    /* ========================================== */
    /* EXPORT                                     */
    /* ========================================== */
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

    .export-divider {
        width: 1px;
        height: 28px;
        background: var(--border-light);
        margin: 0 4px;
    }

    /* ========================================== */
    /* STATS MINI                                 */
    /* ========================================== */
    .stats-mini {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        background: rgba(255, 255, 255, 0.08);
        padding: 4px 16px;
        border-radius: 30px;
    }

    .stats-mini .stat-item {
        display: flex;
        align-items: center;
        gap: 6px;
        color: rgba(255, 255, 255, 0.8);
        font-size: 12px;
        font-weight: 400;
    }

    .stats-mini .stat-item .stat-number {
        font-weight: 600;
        color: #ffffff;
        font-size: 15px;
    }

    .stats-mini .stat-item .stat-label {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.5);
    }

    .stats-divider {
        width: 1px;
        height: 24px;
        background: rgba(255, 255, 255, 0.15);
    }

    /* ========================================== */
    /* RESPONSIVE                                 */
    /* ========================================== */
    @media (max-width: 768px) {
        .card-modern .card-header {
            flex-direction: column;
            align-items: stretch;
        }
        .filter-bar {
            flex-direction: column;
            align-items: stretch;
        }
        .filter-bar .filter-group {
            width: 100%;
        }
        .filter-bar .filter-group select,
        .filter-bar .filter-group input {
            width: 100%;
            min-width: unset;
        }
        .export-group {
            margin-left: 0 !important;
            width: 100%;
            flex-wrap: wrap;
        }
        .export-group .btn-export {
            flex: 1;
            justify-content: center;
            padding: 6px 12px;
            font-size: 11px;
        }
        .export-group .export-label {
            width: 100%;
            text-align: center;
        }
        .export-divider {
            display: none;
        }
        .table-modern thead th,
        .table-modern tbody td {
            padding: 8px 10px;
            font-size: 12px;
        }
        .btn-clear {
            width: 100%;
            justify-content: center;
        }
        .stats-mini {
            width: 100%;
            justify-content: center;
            padding: 6px 12px;
        }
        .stats-mini .stat-item .stat-number {
            font-size: 13px;
        }
        .stats-mini .stat-item .stat-label {
            font-size: 10px;
        }
        .stats-divider {
            display: none;
        }
    }

    @media (max-width: 480px) {
        .export-group .btn-export {
            font-size: 10px;
            padding: 4px 10px;
        }
        .export-group .btn-export i {
            font-size: 12px;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">

                <!-- ========================================== -->
                <!-- CARTE PRINCIPALE                           -->
                <!-- ========================================== -->
                <div class="card-modern">
                    <div class="card-header">
                        <h3>
                            <i class="fa fa-calendar-check-o"></i>
                            <?php echo $this->lang->line('audit') . " " . $this->lang->line('trail') . " " . $this->lang->line('report') . " " . $this->lang->line('list'); ?>
                        </h3>
                        <div class="d-flex align-items-center gap-3" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url(); ?>admin/admin/hub" class="btn-back" title="Retour au tableau de bord">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <button class="btn-clear clear_audit_trail" onclick="confirmClearAudit()">
                                <i class="fa fa-trash"></i> <?php echo $this->lang->line('clear_audit_trail_record'); ?>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- ========================================== -->
                        <!-- BARRE DE FILTRES                          -->
                        <!-- ========================================== -->
                       <!-- <div class="filter-bar">
                            <div class="filter-group">
                                <label><i class="fa fa-filter"></i> Action :</label>
                                <select id="filterAction" onchange="applyFilters()">
                                    <option value="">Toutes</option>
                                    <option value="insert">Insertion</option>
                                    <option value="update">Modification</option>
                                    <option value="delete">Suppression</option>
                                    <option value="login">Connexion</option>
                                    <option value="logout">Déconnexion</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label><i class="fa fa-calendar"></i> Période :</label>
                                <input type="date" id="filterDateFrom" onchange="applyFilters()" placeholder="Du">
                                <span style="color:#94a3b8;font-size:13px;">→</span>
                                <input type="date" id="filterDateTo" onchange="applyFilters()" placeholder="Au">
                            </div>
                            <div style="display:flex;gap:6px;margin-left:auto;flex-wrap:wrap;">
                                <button class="btn-filter" onclick="applyFilters()"><i class="fa fa-search"></i> Filtrer</button>
                                <button class="btn-reset" onclick="resetFilters()"><i class="fa fa-undo"></i> Réinitialiser</button>

                                <div class="export-group">
                                    <span class="export-label"><i class="fa fa-download"></i> Exporter</span>
                                    <div class="export-divider"></div>
                                    <button class="btn-export btn-excel" onclick="exportData('excel')">
                                        <i class="fa fa-file-excel-o"></i> CSV
                                    </button>
                                    <button class="btn-export btn-pdf" onclick="exportData('pdf')">
                                        <i class="fa fa-file-pdf-o"></i> PDF
                                    </button>
                                </div>
                            </div>
                        </div>-->

                        <!-- ========================================== -->
                        <!-- TABLE                                     -->
                        <!-- ========================================== -->
                        <div class="table-responsive">
                            <div class="download_label">
                                <?php echo $this->lang->line('audit') . " " . $this->lang->line('trail') . " " . $this->lang->line('report') . " " . $this->lang->line('list'); ?>
                            </div>
                            <table class="table table-modern all-list"
                                   data-export-title="<?php echo $this->lang->line('audit') . ' ' . $this->lang->line('trail') . ' ' . $this->lang->line('report') . ' ' . $this->lang->line('list'); ?>"
                                   id="auditTable">
                                <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('message'); ?></th>
                                    <th><?php echo $this->lang->line('users'); ?></th>
                                    <th><?php echo $this->lang->line('ip_address'); ?></th>
                                    <th><?php echo $this->lang->line('action'); ?></th>
                                    <th><?php echo $this->lang->line('platform'); ?></th>
                                    <th><?php echo $this->lang->line('agent'); ?></th>
                                    <th><?php echo $this->lang->line('date') . " " . $this->lang->line('time'); ?></th>
                                </tr>
                                </thead>
                            </table>
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
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    (function ($) {
        'use strict';
        $(document).ready(function () {
            // Détruire l'instance existante si présente
            if ($.fn.DataTable.isDataTable('#auditTable')) {
                $('#auditTable').DataTable().destroy();
            }
            initDatatable('all-list', 'admin/audit/getDatatable', [], [], 100);
        });
    }(jQuery));

    // ========================================== //
    // FILTRES                                    //
    // ========================================== //
    function applyFilters() {
        const action = document.getElementById('filterAction').value;
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        var table = $('#auditTable').DataTable();
        table.ajax.url('<?php echo base_url('admin/audit/getDatatable'); ?>?action=' + action + '&date_from=' + dateFrom + '&date_to=' + dateTo).load();
    }

    function resetFilters() {
        document.getElementById('filterAction').value = '';
        document.getElementById('filterDateFrom').value = '';
        document.getElementById('filterDateTo').value = '';
        applyFilters();
    }

    // ========================================== //
    // EXPORTATION                                //
    // ========================================== //
    function exportData(type) {
        const action = document.getElementById('filterAction').value;
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        const params = `?action=${encodeURIComponent(action)}&date_from=${encodeURIComponent(dateFrom)}&date_to=${encodeURIComponent(dateTo)}`;

        var url = '<?php echo base_url("admin/audit/export_"); ?>' + type + params;
        window.location.href = url;
    }

    // ========================================== //
    // VIDER LE JOURNAL D'AUDIT                   //
    // ========================================== //
    function confirmClearAudit() {
        Swal.fire({
            title: 'Confirmation',
            text: '<?php echo $this->lang->line('audit_trail_delete'); ?>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Oui, vider',
            cancelButtonText: 'Annuler',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/audit/delete/',
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == "fail") {
                            Swal.fire({
                                title: 'Erreur',
                                text: data.message || 'Une erreur est survenue',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                title: 'Succès',
                                text: data.message || 'Journal d\'audit vidé avec succès',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            setTimeout(function() {
                                window.location.reload(true);
                            }, 2000);
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Erreur',
                            text: 'Une erreur est survenue lors de la communication avec le serveur',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }

    // ========================================== //
    // CONFIRMATION ORIGINALE POUR COMPATIBILITÉ //
    // ========================================== //
    $(function() {
        $('.clear_audit_trail').on('click', function(e) {
            e.preventDefault();
            confirmClearAudit();
        });
    });
</script>