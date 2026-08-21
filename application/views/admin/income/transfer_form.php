<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$language = $this->customlib->getLanguage();
$language_name = $language["short_code"];
?>
<style>
    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .badge {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
    }

    .badge-caisse {
        background-color: #28a745;
        color: white;
    }

    .badge-banque {
        background-color: #273772;
        color: white;
    }

    .option-balance {
        font-size: 12px;
        color: #666;
        float: right;
    }

    /* Améliorations pour le formulaire */
    .form-group .row {
        margin-left: -5px;
        margin-right: -5px;
    }

    .form-group .row > [class*="col-"] {
        padding-left: 5px;
        padding-right: 5px;
    }

    .balance-info {
        font-size: 12px;
        padding: 5px;
        background: #f8f9fa;
        border-radius: 4px;
        margin-top: 5px;
    }

    .balance-info i {
        margin-right: 5px;
    }

    /* Style pour les sélecteurs */
    select.form-control {
        padding: 6px 12px;
        height: 34px;
    }

    /* Styles pour les boutons d'export */
    .export-buttons {
        margin-bottom: 15px;
    }

    .export-buttons .btn {
        margin-right: 5px;
        margin-bottom: 5px;
    }

    .dt-buttons {
        margin-bottom: 10px;
    }

    .dataTables_wrapper .dt-buttons {
        float: none;
        text-align: left;
    }

    /* Style pour le DataTable à 100% */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    #transfertTable {
        width: 100% !important;
    }

    #transfertTable_wrapper {
        width: 100%;
    }

    /* Style pour le popup */
    .modal-lg-custom {
        width: 700px;
    }

    .modal-header {
        background-color: #273772;
        color: white;
        border-radius: 3px 3px 0 0;
    }

    .modal-header .close {
        color: white;
        opacity: 0.8;
    }

    .modal-header .close:hover {
        opacity: 1;
    }

    .btn-transfer {
        margin-bottom: 15px;
        background-color: #273772;
        border-color: #273772;
        color: white;
        padding: 10px 20px;
        font-weight: bold;
    }

    .btn-transfer:hover {
        background-color: #273772;
        border-color: #273772;
        color: white;
    }

    .btn-transfer i {
        margin-right: 8px;
    }
</style>
<style type="text/css">
    @media print {
        .no-print {
            visibility: hidden !important;
            display:none !important;
        }
        .dataTables_length,
        .dataTables_filter,
        .dataTables_info,
        .dataTables_paginate,
        .dt-buttons {
            display: none !important;
        }
        .box-header {
            border-bottom: 2px solid #000;
        }
        .table th {
            background-color: #f2f2f2 !important;
            -webkit-print-color-adjust: exact;
        }
    }

    /* Style pour le PDF */
    .pdf-header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #273772;
        padding-bottom: 10px;
    }

    .pdf-title {
        font-size: 18px;
        font-weight: bold;
        color: #273772;
    }

    .pdf-subtitle {
        font-size: 14px;
        color: #666;
    }

    .pdf-footer {
        text-align: center;
        margin-top: 20px;
        padding-top: 10px;
        border-top: 1px solid #ddd;
        font-size: 11px;
        color: #666;
    }
</style>
<style>
    .spinner {
        margin: 0 auto;
        width: 50px;
        height: 50px;
        border: 6px solid #f3f3f3;
        border-top: 6px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Améliorations pour le tableau */
    .table th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .table td {
        vertical-align: middle;
    }

    /* Espacement amélioré */
    .box-body {
        padding: 15px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .transfer-type {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 12px;
        font-weight: normal;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content-header">
        <h1>
            <i class="fa fa-exchange"></i> Transferts Inter-comptes
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Historique des transferts -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Historique des transferts</h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('transfer', 'can_add')): ?>
                                <button class="btn btn-success btn-transfer" onclick="openTransferModal()" title="Nouveau transfert">
                                    <i class="fa fa-exchange"></i> Nouveau transfert
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-box-tool" onclick="refreshTable()" title="Actualiser">
                                <i class="fa fa-refresh"></i>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <!-- Boutons d'export -->
                        <!--<div class="export-buttons no-print">
                            <button class="btn btn-success" onclick="exportPDF()">
                                <i class="fa fa-file-pdf-o"></i> Exporter PDF
                            </button>
                            <button class="btn btn-success" onclick="exportExcel()">
                                <i class="fa fa-file-excel-o"></i> Exporter Excel
                            </button>
                            <button class="btn btn-primary" onclick="window.print()">
                                <i class="fa fa-print"></i> Imprimer
                            </button>
                        </div>-->

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="transfertTable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Compte source</th>
                                    <th>Compte destination</th>
                                    <th>Montant</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- Les données seront chargées via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- Modal Popup pour le formulaire de transfert -->
<div class="modal fade" id="transferModal" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel">
    <div class="modal-dialog modal-lg-custom" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="transferModalLabel">
                    <i class="fa fa-exchange"></i> Nouveau transfert inter-comptes
                </h4>
            </div>
            <div class="modal-body">
                <form id="form1" action="<?php echo site_url('admin/transfer/transfer_amount') ?>" name="transferForm" method="post" accept-charset="utf-8">

                    <div class="box-body">
                        <?php if ($this->session->flashdata('msg')): ?>
                            <?php echo $this->session->flashdata('msg') ?>
                        <?php endif; ?>

                        <?php echo $this->customlib->getCSRF(); ?>

                        <!-- Compte source -->
                        <div class="form-group">
                            <label>Transfert de :</label>
                            <div class="row">
                                <div class="col-md-5" style="padding-right: 5px;">
                                    <select name="from_type" id="from_type" class="form-control" onchange="loadAccounts('from')">
                                        <option value="caisse">Caisse</option>
                                        <option value="bank">Banque</option>
                                    </select>
                                </div>
                                <div class="col-md-7" style="padding-left: 5px;">
                                    <select name="from_id" id="from_id" class="form-control" required onchange="updateBalanceInfo()">
                                        <option value="">Chargement...</option>
                                    </select>
                                </div>
                            </div>
                            <div id="from_balance_info" class="balance-info text-muted" style="display: none;">
                                <i class="fa fa-wallet"></i> <span id="from_balance_text">Solde: 0 FCFA</span>
                            </div>
                        </div>

                        <!-- Compte destination -->
                        <div class="form-group">
                            <label>Vers :</label>
                            <div class="row">
                                <div class="col-md-5" style="padding-right: 5px;">
                                    <select name="to_type" id="to_type" class="form-control" onchange="loadAccounts('to')">
                                        <option value="caisse">Caisse</option>
                                        <option value="bank">Banque</option>
                                    </select>
                                </div>
                                <div class="col-md-7" style="padding-left: 5px;">
                                    <select name="to_id" id="to_id" class="form-control" required>
                                        <option value="">Chargement...</option>
                                    </select>
                                </div>
                            </div>
                            <div id="to_balance_info" class="balance-info text-muted" style="display: none;">
                                <i class="fa fa-wallet"></i> <span id="to_balance_text">Solde: 0 FCFA</span>
                            </div>
                        </div>

                        <!-- Montant -->
                        <div class="form-group">
                            <label>Montant à transférer :</label>
                            <div class="input-group">
                                <input type="number" name="amount" id="amount" min="0.01" step="0.01" required class="form-control" placeholder="0.00" />
                                <div class="input-group-addon">FCFA</div>
                            </div>
                            <small id="amount_info" class="text-muted" style="display: block; margin-top: 5px;"></small>
                        </div>

                        <!-- Messages -->
                        <div id="successMessage" class="alert alert-success" style="display:none; margin-top: 10px;"></div>
                        <div id="errorMessage" class="alert alert-danger" style="display:none; margin-top: 10px;"></div>

                        <!-- Loading spinner -->
                        <div id="loadingSpinner" style="display:none; text-align: center; margin-top: 20px;">
                            <div class="spinner"></div>
                            <p style="font-weight: bold;">Transfert en cours...</p>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fa fa-exchange"></i> Valider le transfert
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Inclure les bibliothèques nécessaires pour l'export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script>
    $(document).ready(function () {
        // Initialiser DataTable avec les colonnes modifiées et 100% de largeur
        var table = $('#transfertTable').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?php echo site_url('admin/transfer/gettransferlist'); ?>",
                "type": "GET",
                "error": function(xhr, error, thrown) {
                    console.error('Erreur DataTable:', error, thrown);
                    $('#transfertTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Erreur de chargement des données</td></tr>');
                }
            },
            "columns": [
                {
                    "data": "from_account",
                    "render": function(data, type, row) {
                        if (type === 'export' || type === 'sort') {
                            return data + ' (' + (row.from_type === 'bank' ? 'Banque' : 'Caisse') + ')';
                        }
                        var badge = row.from_type === 'bank' ?
                            '<span class="badge badge-banque">Banque</span>' :
                            '<span class="badge badge-caisse">Caisse</span>';
                        return '<strong>' + (data || 'N/A') + '</strong> ' + badge;
                    }
                },
                {
                    "data": "to_account",
                    "render": function(data, type, row) {
                        if (type === 'export' || type === 'sort') {
                            return data + ' (' + (row.to_type === 'bank' ? 'Banque' : 'Caisse') + ')';
                        }
                        var badge = row.to_type === 'bank' ?
                            '<span class="badge badge-banque">Banque</span>' :
                            '<span class="badge badge-caisse">Caisse</span>';
                        return '<strong>' + (data || 'N/A') + '</strong> ' + badge;
                    }
                },
                {
                    "data": "amount",
                    "render": function(data, type, row) {
                        var formatted = parseFloat(data || 0).toLocaleString('fr-FR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + ' FCFA';

                        if (type === 'export' || type === 'sort') {
                            return formatted;
                        }
                        return '<span class="text-success" style="font-weight: bold;">' + formatted + '</span>';
                    }
                },
                {
                    "data": "date",
                    "render": function(data, type, row) {
                        if (type === 'export' || type === 'sort') {
                            return data;
                        }
                        return '<small>' + (data || '') + '</small>';
                    }
                },
                {
                    "data": "transfer_type",
                    "render": function(data, type, row) {
                        if (type === 'export' || type === 'sort') {
                            return data;
                        }
                        var typeClass = '';
                        switch(data) {
                            case 'Caisse → Caisse': typeClass = 'badge-caisse'; break;
                            case 'Banque → Banque': typeClass = 'badge-banque'; break;
                            case 'Banque → Caisse': typeClass = 'badge-info'; break;
                            case 'Caisse → Banque': typeClass = 'badge-warning'; break;
                        }
                        return '<span class="badge ' + typeClass + '">' + (data || 'N/A') + '</span>';
                    }
                }
            ],
            "order": [[3, "desc"]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
            },
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "dom": 'Bfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Historique des Transferts',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Historique des Transferts',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (doc) {
                        doc.content[1].table.widths =
                            Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        doc.styles.tableHeader.fontSize = 10;
                        doc.styles.tableBodyEven.alignment = 'center';
                        doc.styles.tableBodyOdd.alignment = 'center';
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Imprimer',
                    className: 'btn btn-primary btn-sm',
                    title: 'Historique des Transferts',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (win) {
                        $(win.document.body).find('h1').css('text-align','center');
                        $(win.document.body).find('table').addClass('display').css('font-size', '10px');
                    }
                }
            ],
            // Forcer la largeur à 100%
            "scrollX": true,
            "autoWidth": false,
            "responsive": true
        });

        window.refreshTable = function() {
            table.ajax.reload(null, false);
        };

        // Gestion de la soumission du formulaire dans le modal
        $('#form1').submit(function(e) {
            e.preventDefault();

            console.log('Soumission formulaire');

            var formData = $(this).serialize();
            var submitBtn = $('#submitBtn');
            var loadingSpinner = $('#loadingSpinner');
            var errorMessage = $('#errorMessage');
            var successMessage = $('#successMessage');

            // Validation
            var fromId = $('#from_id').val();
            var toId = $('#to_id').val();
            var amount = parseFloat($('#amount').val());
            var maxAmount = parseFloat($('#amount').attr('max')) || 0;

            console.log('Validation:', {fromId, toId, amount, maxAmount});

            if (!fromId || !toId) {
                errorMessage.html('<i class="fa fa-exclamation-triangle"></i> Veuillez sélectionner les comptes source et destination').show();
                return;
            }

            if (fromId === toId && $('#from_type').val() === $('#to_type').val()) {
                errorMessage.html('<i class="fa fa-exclamation-triangle"></i> Impossible de transférer vers le même compte').show();
                return;
            }

            if (amount <= 0 || isNaN(amount)) {
                errorMessage.html('<i class="fa fa-exclamation-triangle"></i> Le montant doit être supérieur à 0').show();
                return;
            }

            if (amount > maxAmount) {
                errorMessage.html('<i class="fa fa-exclamation-triangle"></i> Le montant dépasse le solde disponible').show();
                return;
            }

            // Afficher le spinner
            submitBtn.prop('disabled', true);
            loadingSpinner.show();
            errorMessage.hide();
            successMessage.hide();

            console.log('Envoi AJAX avec données:', formData);

            // Soumettre via AJAX
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log('Réponse AJAX:', response);
                    loadingSpinner.hide();

                    if (response.status === 'success') {
                        // Afficher le message de succès
                        successMessage.html('<i class="fa fa-check-circle"></i> ' + response.message).show();

                        // Réinitialiser le formulaire (garder les sélections)
                        $('#amount').val('');

                        // Recharger les comptes (pour mettre à jour les soldes)
                        loadAccounts('from');
                        loadAccounts('to');

                        // Actualiser le tableau des transferts
                        $('#transfertTable').DataTable().ajax.reload(null, false);

                        // Fermer le modal après 2 secondes
                        setTimeout(function() {
                            $('#transferModal').modal('hide');
                            successMessage.hide();
                        }, 2000);
                    } else {
                        // Afficher l'erreur
                        errorMessage.html('<i class="fa fa-exclamation-triangle"></i> ' + response.message).show();
                    }

                    submitBtn.prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error('Erreur AJAX:', xhr.responseText);
                    loadingSpinner.hide();
                    errorMessage.html('<i class="fa fa-exclamation-triangle"></i> Erreur réseau ou serveur. Veuillez réessayer.').show();
                    submitBtn.prop('disabled', false);
                }
            });
        });
    });

    // Fonction pour ouvrir le modal de transfert
    function openTransferModal() {
        // Réinitialiser le formulaire
        $('#form1')[0].reset();
        $('#from_balance_info').hide();
        $('#to_balance_info').hide();
        $('#errorMessage').hide();
        $('#successMessage').hide();
        $('#loadingSpinner').hide();

        // Charger les comptes
        loadAccounts('from');
        loadAccounts('to');

        // Ouvrir le modal
        $('#transferModal').modal('show');
    }

    // Fonction pour exporter en PDF
    function exportPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'pt', 'a4');
        const table = document.getElementById('transfertTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        // En-tête du PDF
        doc.setFontSize(18);
        doc.setTextColor(0, 0, 0);
        doc.text('HISTORIQUE DES TRANSFERTS INTER-COMPTES', 40, 40);

        doc.setFontSize(12);
        doc.setTextColor(100, 100, 100);
        doc.text('Date d\'export: ' + new Date().toLocaleDateString('fr-FR'), 40, 60);

        // Préparer les données du tableau
        const headers = [
            ['Compte Source', 'Compte Destination', 'Montant (FCFA)', 'Date', 'Type']
        ];

        const data = [];
        for (let i = 0; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            if (cells.length > 0) {
                const rowData = [];
                // Source
                rowData.push(cells[0].innerText.replace(/\n/g, ' ').trim());
                // Destination
                rowData.push(cells[1].innerText.replace(/\n/g, ' ').trim());
                // Montant
                rowData.push(cells[2].innerText.replace(' FCFA', '').trim());
                // Date
                rowData.push(cells[3].innerText.trim());
                // Type
                rowData.push(cells[4].innerText.trim());
                data.push(rowData);
            }
        }

        // Créer le tableau dans le PDF
        doc.autoTable({
            head: headers,
            body: data,
            startY: 80,
            theme: 'striped',
            headStyles: {
                fillColor: [41, 128, 185],
                textColor: 255,
                fontStyle: 'bold'
            },
            alternateRowStyles: {
                fillColor: [245, 245, 245]
            },
            margin: { top: 80 },
            didDrawPage: function(data) {
                // Pied de page
                doc.setFontSize(10);
                doc.setTextColor(150);
                doc.text(
                    'Page ' + doc.internal.getNumberOfPages(),
                    doc.internal.pageSize.width / 2,
                    doc.internal.pageSize.height - 20,
                    { align: 'center' }
                );
            }
        });

        // Sauvegarder le PDF
        doc.save('historique_transferts_' + new Date().toISOString().slice(0,10) + '.pdf');
    }

    // Fonction pour exporter en Excel
    function exportExcel() {
        const table = document.getElementById('transfertTable');
        const ws = XLSX.utils.table_to_sheet(table, {raw: true});
        const wb = XLSX.utils.book_new();

        // Ajouter l'en-tête
        const header = [
            ['HISTORIQUE DES TRANSFERTS INTER-COMPTES'],
            ['Date d\'export: ' + new Date().toLocaleDateString('fr-FR')],
            [] // Ligne vide
        ];

        // Convertir la feuille en tableau
        const data = XLSX.utils.sheet_to_json(ws, {header: 1});

        // Fusionner les en-têtes et les données
        const finalData = header.concat(data);

        // Créer une nouvelle feuille avec les données finales
        const finalWs = XLSX.utils.aoa_to_sheet(finalData);

        // Fusionner les cellules d'en-tête
        finalWs['!merges'] = [
            XLSX.utils.decode_range("A1:E1"),
            XLSX.utils.decode_range("A2:E2")
        ];

        // Ajouter des styles (largeur de colonne)
        const wscols = [
            {wch: 30}, // Source
            {wch: 30}, // Destination
            {wch: 20}, // Montant
            {wch: 20}, // Date
            {wch: 20}  // Type
        ];
        finalWs['!cols'] = wscols;

        XLSX.utils.book_append_sheet(wb, finalWs, "Transferts");

        // Sauvegarder le fichier Excel
        XLSX.writeFile(wb, 'historique_transferts_' + new Date().toISOString().slice(0,10) + '.xlsx');
    }

    // Fonction pour charger les comptes selon le type
    function loadAccounts(type) {
        var selectType = $('#' + type + '_type').val();
        var csrf_token = $('input[name="csrf_test_name"]').val();

        console.log('Chargement comptes type:', selectType);

        $.ajax({
            url: '<?php echo site_url("admin/transfer/get_accounts"); ?>',
            type: 'POST',
            data: {
                type: selectType,
                csrf_test_name: csrf_token
            },
            beforeSend: function() {
                $('#' + type + '_id').html('<option value="">Chargement...</option>');
            },
            success: function(data) {
                console.log('Réponse get_accounts:', data.substring(0, 100));
                $('#' + type + '_id').html(data);

                // Mettre à jour les infos de solde
                if (type === 'from') {
                    updateBalanceInfo();
                } else {
                    updateDestinationInfo();
                }

                // Vérifier si on essaie de transférer vers le même compte
                checkSameAccount();
            },
            error: function(xhr, status, error) {
                console.error('Erreur chargement comptes:', error);
                $('#' + type + '_id').html('<option value="">Erreur de chargement</option>');
                $('#errorMessage').html('<i class="fa fa-exclamation-triangle"></i> Erreur lors du chargement des comptes').show();
            }
        });
    }

    // Mettre à jour les infos de solde du compte source
    function updateBalanceInfo() {
        var selectedOption = $('#from_id option:selected');
        var balance = parseFloat(selectedOption.data('balance')) || 0;

        console.log('Update balance info - balance:', balance, 'option val:', selectedOption.val());

        if (selectedOption.val() && selectedOption.val() !== '') {
            $('#from_balance_info').show();
            $('#from_balance_text').text('Solde: ' + balance.toLocaleString('fr-FR') + ' FCFA');

            // Mettre à jour le max de l'input
            $('#amount').attr('max', balance);

            // Mettre à jour l'info du montant
            if (balance > 0) {
                $('#amount_info').text('Maximum: ' + balance.toLocaleString('fr-FR') + ' FCFA');
                $('#amount_info').removeClass('text-danger').addClass('text-success');
            } else {
                $('#amount_info').text('Solde insuffisant');
                $('#amount_info').removeClass('text-success').addClass('text-danger');
            }

            if (balance <= 0) {
                $('#from_balance_info').removeClass('text-muted').addClass('text-danger');
                $('#submitBtn').prop('disabled', true);
            } else {
                $('#from_balance_info').removeClass('text-danger').addClass('text-muted');
                $('#submitBtn').prop('disabled', false);
            }
        } else {
            $('#from_balance_info').hide();
            $('#amount_info').text('');
            $('#submitBtn').prop('disabled', true);
        }
    }

    // Mettre à jour les infos du compte destination
    function updateDestinationInfo() {
        var selectedOption = $('#to_id option:selected');
        var balance = parseFloat(selectedOption.data('balance')) || 0;

        if (selectedOption.val() && selectedOption.val() !== '') {
            $('#to_balance_info').show();
            $('#to_balance_text').text('Solde: ' + balance.toLocaleString('fr-FR') + ' FCFA');
        } else {
            $('#to_balance_info').hide();
        }
    }

    // Vérifier si on essaie de transférer vers le même compte
    function checkSameAccount() {
        var fromType = $('#from_type').val();
        var fromId = $('#from_id').val();
        var toType = $('#to_type').val();
        var toId = $('#to_id').val();

        console.log('Check same account:', fromType, fromId, toType, toId);

        if (fromType === toType && fromId === toId && fromId !== '' && toId !== '') {
            $('#errorMessage').html('<i class="fa fa-exclamation-triangle"></i> Impossible de transférer vers le même compte').show();
            $('#submitBtn').prop('disabled', true);
        } else {
            $('#errorMessage').hide();
            // Le bouton est déjà géré par updateBalanceInfo()
        }
    }

    // Charger les comptes au démarrage
    $(window).on('load', function() {
        console.log('Page chargée');

        // Ne pas charger les comptes au démarrage car le formulaire est maintenant dans un popup
        // Ils seront chargés quand le popup s'ouvrira

        // Mettre à jour les infos quand le montant change (dans le modal)
        $(document).on('input', '#amount', function() {
            var amount = parseFloat($(this).val()) || 0;
            var maxAmount = parseFloat($(this).attr('max')) || 0;

            if (amount > maxAmount) {
                $(this).addClass('is-invalid');
                $('#errorMessage').html('<i class="fa fa-exclamation-triangle"></i> Le montant dépasse le solde disponible').show();
            } else {
                $(this).removeClass('is-invalid');
                $('#errorMessage').hide();
            }
        });

        // Vérifier les changements sur les sélecteurs (dans le modal)
        $(document).on('change', '#from_id, #to_id', function() {
            checkSameAccount();
        });

        // Réinitialiser le formulaire quand le modal est fermé
        $('#transferModal').on('hidden.bs.modal', function () {
            $('#form1')[0].reset();
            $('#from_balance_info').hide();
            $('#to_balance_info').hide();
            $('#errorMessage').hide();
            $('#successMessage').hide();
            $('#loadingSpinner').hide();
        });
    });
</script>