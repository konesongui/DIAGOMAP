<?php
$formID = 'serviceForm';
?>
<style>
    .modal-lg {
        max-width: 800px;
    }
    .btn-soft {
        padding: 2px 8px;
        font-size: 12px;
    }
    .dt-buttons {
        margin-bottom: 15px;
    }
    .dt-buttons .btn {
        margin-right: 5px;
    }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-cogs"></i> Services</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Liste des services</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-primary btn-sm" id="btn-add-service">
                                <i class="fa fa-plus"></i> Ajouter un service
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="services-table" width="100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Prix unitaire (FCFA)</th>
                                <th>Durée</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Ajouter / Modifier -->
<div class="modal fade" id="serviceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Service</h4>
            </div>
            <form id="<?= $formID ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="service_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nom <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Prix unitaire (FCFA) <span class="text-danger">*</span></label>
                                <input type="number" name="unit_price" id="unit_price" class="form-control" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Durée (ex: 2 heures, 1 jour)</label>
                                <input type="text" name="duration" id="duration" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Styles DataTables Buttons -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">

<!-- Scripts DataTables et extensions -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap.min.css">

<!-- Buttons extensions -->
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    var base_url = '<?= base_url() ?>';
    $(document).ready(function() {
        var table = $('#services-table').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": base_url + "admin/services/ajax_list",
                "type": "GET",
                "dataSrc": function(json) {
                    return json;
                }
            },
            "columns": [
                { "data": "id" },
                { "data": "name" },
                { "data": "description" },
                {
                    "data": "unit_price",
                    "render": function(data) { return parseFloat(data).toFixed(2) + ' FCFA'; }
                },
                { "data": "duration" },
                {
                    "data": null,
                    "orderable": false,
                    "render": function(data, type, row) {
                        return '<button class="btn btn-info btn-sm btn-edit" data-id="'+row.id+'"><i class="fa fa-edit"></i> Modifier</button> ' +
                            '<button class="btn btn-danger btn-sm btn-delete" data-id="'+row.id+'"><i class="fa fa-trash"></i> Supprimer</button>';
                    }
                }
            ],
            "language": {
                "url": base_url + "assets/js/french.json"
            },
            "dom": 'Bfrtip',
            "buttons": [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Services',
                    exportOptions: {
                        columns: [0,1,2,3,4] // Exporter les colonnes ID, Nom, Description, Prix, Durée (sans Actions)
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Services',
                    orientation: 'portrait',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0,1,2,3,4]
                    },
                    customize: function(doc) {
                        doc.content[1].table.widths = ['5%', '20%', '35%', '15%', '15%'];
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Imprimer',
                    className: 'btn btn-info btn-sm',
                    exportOptions: {
                        columns: [0,1,2,3,4]
                    }
                }
            ]
        });

        // Ouvrir modal pour ajouter
        $('#btn-add-service').click(function() {
            $('#service_id').val('');
            $('#name').val('');
            $('#description').val('');
            $('#unit_price').val('');
            $('#duration').val('');
            $('#serviceModal').modal('show');
        });

        // Ouvrir modal pour modifier
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            $.get(base_url + 'admin/services/ajax_edit/' + id, function(data) {
                $('#service_id').val(data.id);
                $('#name').val(data.name);
                $('#description').val(data.description);
                $('#unit_price').val(data.unit_price);
                $('#duration').val(data.duration);
                $('#serviceModal').modal('show');
            }, 'json');
        });

        // Soumission du formulaire (ajout/modification)
        $('#<?= $formID ?>').submit(function(e) {
            e.preventDefault();
            var id = $('#service_id').val();
            var url = id ? base_url + 'admin/services/ajax_update' : base_url + 'admin/services/ajax_add';
            var formData = $(this).serialize();
            $.post(url, formData, function(response) {
                if (response.status === 'success') {
                    Swal.fire('Succès', response.message, 'success');
                    $('#serviceModal').modal('hide');
                    table.ajax.reload();
                } else {
                    Swal.fire('Erreur', response.message, 'error');
                }
            }, 'json');
        });

        // Suppression
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Confirmation',
                text: 'Voulez-vous vraiment supprimer ce service ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(base_url + 'admin/services/ajax_delete/' + id, function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Succès', response.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Erreur', response.message, 'error');
                        }
                    }, 'json');
                }
            });
        });
    });
</script>