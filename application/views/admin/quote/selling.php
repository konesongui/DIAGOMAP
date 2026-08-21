<?php
$dID = 'quotesDatatable';
// Vérifier les privilèges RBAC
$is_superadmin = $this->rbac->hasPrivilege('superadmin');
$is_admin = $this->rbac->hasPrivilege('admin');
$is_admin_user = ($is_superadmin || $is_admin);
?>
    <style>
        /* Styles pour les exports */
        .btn-group .btn-success,
        .btn-group .btn-danger {
            margin-right: 5px;
        }
        .btn-group .btn-success:hover,
        .btn-group .btn-danger:hover {
            transform: translateY(-1px);
            transition: all 0.3s ease;
        }
        .dataTables_wrapper .dt-buttons {
            margin-bottom: 10px;
        }
        @media (max-width: 768px) {
            .btn-group .btn-success,
            .btn-group .btn-danger {
                padding: 4px 8px;
                font-size: 12px;
            }
        }
        /* Améliorations de l'en-tête */
        .box-tools-custom {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
        }
        .box-tools-custom .form-group,
        .box-tools-custom .btn-group,
        .box-tools-custom .btn,
        .box-tools-custom .badge,
        .box-tools-custom select,
        .box-tools-custom a {
            margin: 0 !important;
        }
        .box-tools-custom select.form-control {
            width: auto;
            min-width: 140px;
        }
        @media (max-width: 991px) {
            .box-tools-custom {
                justify-content: flex-start;
                margin-top: 10px;
            }
            .box-header .box-title {
                display: block;
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <section class="content-header">
            <h1><i class="fa fa-object-group"></i> Point de vente</h1>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Point de vente</h3>
                            <div class="box-tools pull-right box-tools-custom" style="right: 10px">
                                <!-- Filtre par statut -->
                                <div class="form-group btn-sm">
                                    <select id="statusFilter" class="form-control">
                                        <option value="">Tous les statuts</option>
                                        <option value="1">En attente de validation</option>
                                        <option value="2">Validé</option>
                                        <option value="3">Rejeté</option>
                                        <option value="4">En cours de traitement</option>
                                        <option value="5">Livré</option>
                                        <option value="6">Annulé</option>
                                    </select>
                                </div>

                                <!-- DROPDOWN pour le filtre admin -->
                                <?php if ($is_admin_user): ?>
                                    <div>
                                        <select id="adminFilterSelect" class="form-control">
                                            <option value="my">📄 Mes ventes</option>
                                            <option value="all">👥 Toutes les ventes</option>
                                        </select>
                                    </div>
                                <?php endif; ?>

                                <!-- Badge d'indication
                                <span id="filterBadge" class="badge bg-info">
                                <?php if (!$is_admin_user): ?>
                                    <i class="fa fa-user"></i> Mes ventes uniquement
                                <?php else: ?>
                                    <i class="fa fa-eye"></i> Vue admin
                                    <?php if ($is_superadmin): ?>
                                        <small>(Super Admin)</small>
                                    <?php elseif ($is_admin): ?>
                                        <small>(Admin)</small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </span> -->

                                <!-- DROPDOWN pour l'export -->
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-download"></i> Exporter <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" id="exportExcelBtn"><i class="fa fa-file-excel-o text-green"></i> Excel</a></li>
                                        <li><a href="#" id="exportPdfBtn"><i class="fa fa-file-pdf-o text-red"></i> PDF</a></li>
                                    </ul>
                                </div>

                                <!-- Sélecteur client -->
                                <div class="form-group btn-sm">
                                    <select id="customerSelect" class="form-control" style="width: 40px">
                                        <option value="">-- Sélectionner un client --</option>
                                        <?php
                                        $clients = $this->clients_model->get();
                                        foreach ($clients as $client) {
                                            $name = trim($client['item_supplier'] . ' ' . ($client['lastname'] ?? ''));
                                            echo '<option value="' . $client['id'] . '">' . html_escape($name) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Bouton imprimer toutes les ventes du client -->
                                <button id="printAllCustomerSales" class="btn btn-info btn-sm">
                                    <i class="fa fa-print"></i> Imprimer
                                </button>

                                <!-- Bouton Nouvelle vente -->
                                <?php if ($this->rbac->hasPrivilege('devis', 'can_add')) { ?>
                                    <a href="<?php echo site_url('admin/selling/form_selling') ?>" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus"></i> Nouvelle vente
                                    </a>
                                <?php } ?>
                            </div>
                        </div><!-- /.box-header -->

                        <div class="box-body">
                            <div class="mailbox-messages table-responsive">
                                <table class="table table-striped table-bordered table-hover <?= $dID ?>" data-export-title="Liste des ventes" id="mainDataTable">
                                    <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Dates</th>
                                        <th>Créé le</th>
                                        <th>Total TTC</th>
                                        <th>Montant payé</th>
                                        <th>Montant rendu</th>
                                        <th>Reste à payer</th>
                                        <th>Statut paiement</th>
                                        <th>Statut vente</th>
                                        <th>Suivi par</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div><!-- /.box-body -->
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <!-- Librairies pour l'export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>

    <script>
        // Variables globales
        var currentFilterMode = '<?php echo !$is_admin_user ? "my" : "my"; ?>';
        var base_url = '<?php echo base_url(); ?>';
        var baseurl = '<?php echo base_url(); ?>';
    </script>

    <script src="<?= base_url('assets/js/quote_selling/index.js') ?>"></script>

    <script>
        $(document).ready(function() {
            // Gestion du filtre admin (select)
            <?php if ($is_admin_user): ?>
            var adminSelect = $('#adminFilterSelect');
            adminSelect.val(currentFilterMode);
            function triggerAdminFilter(filterValue) {
                currentFilterMode = filterValue;
                var hiddenBtn = $('.filter-toggle[data-filter="' + filterValue + '"]');
                if (hiddenBtn.length) {
                    hiddenBtn.click();
                } else {
                    var table = $('#mainDataTable').DataTable();
                    if (table) table.ajax.reload();
                }
            }
            adminSelect.on('change', function() {
                triggerAdminFilter($(this).val());
            });
            <?php endif; ?>

            // Export Excel
            $('#exportExcelBtn').on('click', function(e) {
                e.preventDefault();
                var tableData = getCurrentTableData();
                if (tableData.data.length === 0) {
                    Swal.fire({ icon: 'warning', title: 'Aucune donnée', text: 'Il n\'y a aucune donnée à exporter!', confirmButtonText: 'OK' });
                    return;
                }
                var wsData = [tableData.headers, ...tableData.data];
                var ws = XLSX.utils.aoa_to_sheet(wsData);
                var wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Ventes');
                var fileName = 'liste_ventes_' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.xlsx';
                XLSX.writeFile(wb, fileName);
                Swal.fire({ icon: 'success', title: 'Export réussi', text: 'Fichier Excel généré!', timer: 2000, showConfirmButton: false });
            });

            // Export PDF
            $('#exportPdfBtn').on('click', function(e) {
                e.preventDefault();
                var tableData = getCurrentTableData();
                if (tableData.data.length === 0) {
                    Swal.fire({ icon: 'warning', title: 'Aucune donnée', text: 'Il n\'y a aucune donnée à exporter!', confirmButtonText: 'OK' });
                    return;
                }
                const { jsPDF } = window.jspdf;
                var doc = new jsPDF('landscape', 'mm', 'a4');
                var title = 'Liste des ventes';
                var dateStr = new Date().toLocaleDateString('fr-FR') + ' ' + new Date().toLocaleTimeString('fr-FR');
                doc.setFontSize(16);
                doc.text(title, 14, 15);
                doc.setFontSize(10);
                doc.text('Généré le: ' + dateStr, 14, 22);
                var columns = tableData.headers.map(h => ({ header: h, dataKey: h }));
                var rows = tableData.data.map(row => {
                    var obj = {};
                    tableData.headers.forEach((h, i) => obj[h] = row[i]);
                    return obj;
                });
                doc.autoTable({
                    columns: columns,
                    body: rows,
                    startY: 30,
                    theme: 'striped',
                    styles: { fontSize: 8, cellPadding: 2, overflow: 'linebreak' },
                    headerStyles: { fillColor: [41, 128, 185], textColor: 255, fontStyle: 'bold', halign: 'center' },
                    margin: { top: 30, left: 10, right: 10 },
                    didDrawPage: function(data) {
                        var pageCount = doc.internal.getNumberOfPages();
                        doc.setFontSize(8);
                        doc.text('Page ' + data.pageNumber + ' sur ' + pageCount, doc.internal.pageSize.width - 20, doc.internal.pageSize.height - 10);
                    }
                });
                var fileName = 'liste_ventes_' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.pdf';
                doc.save(fileName);
                Swal.fire({ icon: 'success', title: 'Export réussi', text: 'Fichier PDF généré!', timer: 2000, showConfirmButton: false });
            });

            // Fonction utilitaire pour récupérer les données du tableau (inchangée)
            function getCurrentTableData() {
                var data = [];
                var headers = [];
                $('#mainDataTable thead th').each(function(index) {
                    var headerText = $(this).text().trim();
                    if (headerText !== 'Actions' && !$(this).hasClass('text-right')) {
                        headers.push(headerText);
                    }
                });
                $('#mainDataTable tbody tr').each(function() {
                    var row = [];
                    $(this).find('td').each(function(index) {
                        if (index < headers.length) {
                            row.push($(this).text().trim());
                        }
                    });
                    if (row.length) data.push(row);
                });
                return { headers: headers, data: data };
            }

            // Impression groupée des ventes du client
            $('#printAllCustomerSales').on('click', function() {
                var customerId = $('#customerSelect').val();
                if (!customerId) {
                    Swal.fire('Attention', 'Veuillez sélectionner un client.', 'warning');
                    return;
                }
                Swal.fire({
                    title: 'Génération en cours...',
                    text: 'Veuillez patienter.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                $.ajax({
                    url: base_url + 'admin/selling/printAllByClient',
                    type: 'POST',
                    data: { customer_id: customerId },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if (response.status === '1') {
                            var win = window.open();
                            win.document.write(response.page);
                            win.document.close();
                        } else {
                            var msg = response.message || 'Impossible de générer l\'impression.';
                            Swal.fire('Information', msg, 'info');
                        }
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire('Erreur', 'Erreur de communication avec le serveur.', 'error');
                    }
                });
            });
        });
    </script>

    <!-- Boutons cachés pour compatibilité avec index.js (filtre admin) -->
<?php if ($is_admin_user): ?>
    <div style="display: none;">
        <div class="btn-group btn-sm" role="group">
            <button type="button" class="btn btn-primary filter-toggle" data-filter="my">Mes ventes</button>
            <button type="button" class="btn btn-default filter-toggle" data-filter="all">Toutes les ventes</button>
        </div>
    </div>
<?php endif; ?>