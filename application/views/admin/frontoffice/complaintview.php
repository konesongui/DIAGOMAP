<div class="content-wrapper">


    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-list-alt"></i> <?php echo $this->lang->line('complain'); ?> <?php echo $this->lang->line('list'); ?></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                        <?php if ($this->rbac->hasPrivilege('reclamation', 'can_add')) { ?>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#complaintFormModal" style="margin-top: -5px; font-weight: bold; font-size: 11px; border-radius: 4px; background-color: #28a745;margin-left:65%; border-color: #28a745; color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                                <i class="fa fa-exclamation-triangle" style="margin-right: 5px;"></i> + AJOUTER UNE RÉCLAMATION
                            </button>
                        <?php } ?>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <!-- Affichage des messages flash -->
                        <?php if ($this->session->flashdata('msg')) { ?>
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <i class="fa fa-check-circle"></i> <?php echo $this->session->flashdata('msg'); ?>
                            </div>
                        <?php } ?>

                        <?php if ($this->session->flashdata('error')) { ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <i class="fa fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
                            </div>
                        <?php } ?>

                        <div class="download_label"><?php echo $this->lang->line('complain'); ?> <?php echo $this->lang->line('list'); ?></div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-bordered example" style="width:100%;">
                                <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('complain'); ?> #</th>
                                    <th><?php echo $this->lang->line('complain_type'); ?></th>
                                    <th><?php echo $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('phone'); ?></th>
                                    <th><?php echo $this->lang->line('date'); ?></th>
                                    <th class="text-right" style="width: 150px;"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (empty($complaint_list)) {
                                    echo '<tr><td colspan="6" class="text-center">Aucune réclamation trouvée</td></tr>';
                                } else {
                                    foreach ($complaint_list as $key => $value) {
                                        ?>
                                        <tr>
                                            <td class="mailbox-name">
                                                <span class="label bg-gray">#<?php echo $value['id']; ?></span>
                                            </td>
                                            <td class="mailbox-name"><?php echo $value['complaint_type']; ?></td>
                                            <td class="mailbox-name">
                                                <?php echo $value['name']; ?>
                                                <?php if(!empty($value['email'])){ ?>
                                                    <br><a href="mailto:<?php echo $value['email']; ?>" class="text-muted">
                                                        <i class="fa fa-envelope-o"></i> <?php echo $value['email']; ?>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                            <td class="mailbox-name">
                                                <a href="tel:<?php echo $value['contact']; ?>">
                                                    <i class="fa fa-phone"></i> <?php echo $value['contact']; ?>
                                                </a>
                                            </td>
                                            <td class="mailbox-name">
                                                <i class="fa fa-calendar"></i>
                                                <?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value['date'])); ?>
                                            </td>
                                            <td class="text-right white-space-nowrap">
                                                <a onclick="getRecord(<?php echo $value['id']; ?>)" class="btn btn-xs btn-info" data-target="#complaintdetails" title="<?php echo $this->lang->line('view') ?>" data-toggle="modal">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                <?php if ($value['image'] !== "") { ?>
                                                    <a href="<?php echo base_url(); ?>admin/complaint/download/<?php echo $value['image']; ?>" class="btn btn-xs btn-primary" title="<?php echo $this->lang->line('download')?>">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                <?php } ?>

                                                <?php if ($this->rbac->hasPrivilege('reclamation', 'can_edit')) { ?>
                                                    <a href="<?php echo base_url(); ?>admin/complaint/edit/<?php echo $value['id']; ?>" class="btn btn-xs btn-warning" title="<?php echo $this->lang->line('edit') ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } ?>

                                                <?php if ($this->rbac->hasPrivilege('reclamation', 'can_delete')) { ?>
                                                    <?php if ($value['image'] !== "") { ?>
                                                        <a href="<?php echo base_url(); ?>admin/complaint/imagedelete/<?php echo $value['id']; ?>/<?php echo $value['image']; ?>" class="btn btn-xs btn-danger" onclick="return confirmDelete(event, '<?php echo addslashes($value['name']); ?>')" title="<?php echo $this->lang->line('delete')?>">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php } else { ?>
                                                        <a href="<?php echo base_url(); ?>admin/complaint/delete/<?php echo $value['id']; ?>" class="btn btn-xs btn-danger" onclick="return confirmDelete(event, '<?php echo addslashes($value['name']); ?>')" title="<?php echo $this->lang->line('delete')?>">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php } ?>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.table-responsive -->
                    </div><!-- /.box-body -->

                    <?php if (!empty($complaint_list)) { ?>
                        <div class="box-footer clearfix">
                            <div class="pull-right">
                                <small>Total: <?php echo count($complaint_list); ?> réclamation(s)</small>
                            </div>
                        </div>
                    <?php } ?>
                </div><!-- /.box -->
            </div><!--/.col (left) -->
        </div><!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- Modal Formulaire Ajout Réclamation -->
<div id="complaintFormModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #28a745; color: white; border-radius: 4px 4px 0 0;">
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.9;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Ajouter une nouvelle réclamation</h4>
            </div>
            <form id="complaintForm" action="<?php echo site_url('admin/complaint') ?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="complaint"><?php echo $this->lang->line('complain_type'); ?></label>
                                <input type="text" class="form-control" value="<?php echo set_value('complaint'); ?>" name="complaint" placeholder="Ex: Problème technique, Retard, etc.">
                                <span class="text-danger"><?php echo form_error('complaint'); ?></span>
                            </div>

                            <div class="form-group" hidden>
                                <label for="source"><?php echo $this->lang->line('source'); ?></label>
                                <input type="text" class="form-control" value="<?php echo set_value('source'); ?>" name="source" placeholder="Source">
                                <span class="text-danger"><?php echo form_error('source'); ?></span>
                            </div>

                            <div class="form-group">
                                <label for="name"><?php echo $this->lang->line('complain_by'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="<?php echo set_value('name'); ?>" name="name" required placeholder="Nom du plaignant">
                                <span class="text-danger"><?php echo form_error('name'); ?></span>
                            </div>

                            <div class="form-group">
                                <label for="contact"><?php echo $this->lang->line('phone'); ?></label>
                                <input type="text" class="form-control" value="<?php echo set_value('contact'); ?>" name="contact" placeholder="Ex: 0123456789">
                            </div>

                            <div class="form-group">
                                <label for="date"><?php echo $this->lang->line('date'); ?></label>
                                <input type="text" class="form-control date" value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>" name="date" id="date" readonly>
                                <span class="text-danger"><?php echo form_error('date'); ?></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="description"><?php echo $this->lang->line('description'); ?></label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Description détaillée de la réclamation..."><?php echo set_value('description'); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="action_taken"><?php echo $this->lang->line('action_taken'); ?></label>
                                <input type="text" class="form-control" value="<?php echo set_value('action_taken'); ?>" name="action_taken" placeholder="Action entreprise">
                                <span class="text-danger"><?php echo form_error('action_taken'); ?></span>
                            </div>

                            <div class="form-group" hidden>
                                <label for="assigned"><?php echo $this->lang->line('assigned'); ?></label>
                                <input type="text" class="form-control" value="<?php echo set_value('assigned'); ?>" name="assigned" placeholder="Assigné à">
                                <span class="text-danger"><?php echo form_error('assigned'); ?></span>
                            </div>

                            <div class="form-group">
                                <label for="note"><?php echo $this->lang->line('note'); ?></label>
                                <textarea class="form-control" id="note" name="note" rows="3" placeholder="Notes supplémentaires..."><?php echo set_value('note'); ?></textarea>
                                <span class="text-danger"><?php echo form_error('note'); ?></span>
                            </div>

                            <div class="form-group">
                                <label for="file"><?php echo $this->lang->line('attach_document'); ?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-file"></i></span>
                                    <input class="filestyle form-control" type='file' name='file' data-buttonText="Choisir un fichier" data-iconName="fa fa-upload">
                                </div>
                                <span class="text-danger"><?php echo form_error('file'); ?></span>
                                <small class="text-muted">Formats acceptés : PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                            </div>

                            <div class="form-group">
                                <div class="callout callout-info" style="background-color: #d9edf7; border-left-color: #31708f; padding: 10px; border-radius: 3px;">
                                    <i class="fa fa-info-circle"></i>
                                    <strong>Note:</strong> Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Annuler
                    </button>
                    <button type="reset" class="btn btn-warning">
                        <i class="fa fa-refresh"></i> Réinitialiser
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Détails Réclamation -->
<div id="complaintdetails" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary" style="background-color: #3c8dbc; color: white;">
                <button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-info-circle"></i> <?php echo $this->lang->line('details'); ?></h4>
            </div>
            <div class="modal-body" id="getdetails"></div>
        </div>
    </div>
</div>

<!-- Styles et Scripts -->
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.js"></script>

<!-- Ajout de SweetAlert2 pour de meilleures alertes -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Style forcé pour le bouton */
    .content-header .btn-success {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
        z-index: 9999 !important;
    }

    .content-header .row {
        margin: 0;
    }

    .content-header .col-xs-8,
    .content-header .col-xs-4 {
        padding: 0;
    }

    /* Style pour le tableau */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .table {
        width: 100% !important;
        margin-bottom: 0;
        background: white;
    }

    .table thead tr th {
        background: #f4f4f4;
        text-align: center;
        vertical-align: middle;
    }

    .table tbody tr td {
        vertical-align: middle;
    }

    /* Style pour les actions */
    .btn-xs {
        margin: 0 2px;
        padding: 4px 8px;
    }

    .btn-xs i {
        font-size: 12px;
    }

    /* Style pour la modal */
    .modal-lg {
        width: 80%;
        max-width: 900px;
    }

    /* Style pour les alertes */
    .alert {
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    /* Style pour les badges */
    .label {
        font-size: 11px;
        padding: 3px 6px;
        border-radius: 3px;
    }

    .bg-gray {
        background-color: #9e9e9e;
        color: white;
    }

    /* Style pour la zone de téléchargement de fichier */
    .filestyle {
        border-radius: 0 3px 3px 0;
    }

    .input-group-addon {
        background-color: #f4f4f4;
        border: 1px solid #d2d6de;
    }

    .white-space-nowrap {
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        .modal-lg {
            width: 95%;
        }

        .content-header .col-xs-4 {
            text-align: left !important;
            margin-top: 10px;
        }

        .content-header .btn-success {
            width: 100%;
            margin-top: 5px !important;
            white-space: normal;
            font-size: 14px !important;
        }

        .table-responsive {
            border: none;
        }

        .table thead tr th {
            font-size: 12px;
        }

        .table tbody tr td {
            font-size: 12px;
        }

        .btn-xs {
            margin: 2px;
        }
    }
</style>

<script type="text/javascript">
    $(document).ready(function() {
        // Initialisation des datepickers
        $('.date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true
        });

        // Initialisation du filestyle pour les fichiers
        if ($.fn.filestyle) {
            $('.filestyle').filestyle({
                buttonText: 'Choisir un fichier',
                buttonName: 'btn-default',
                iconName: 'fa fa-upload',
                size: 'sm'
            });
        }

        // Réinitialisation du formulaire quand la modal est fermée
        $('#complaintFormModal').on('hidden.bs.modal', function() {
            $('#complaintForm')[0].reset();
            $('.text-danger').html('');
            if ($.fn.filestyle) {
                $('.filestyle').filestyle('clear');
            }
        });

        // Empêcher l'ouverture automatique de la modal lors du chargement de la page
        <?php
        // Ne montre la modal que s'il y a des erreurs de validation ET que ce n'est pas une suppression
        if (validation_errors() && !$this->input->get('delete')) {
        ?>
        $('#complaintFormModal').modal('show');
        <?php } ?>

        // Initialisation des tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });

    // Fonction pour voir les détails d'une réclamation
    function getRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/complaint/details/' + id,
            type: 'GET',
            dataType: 'html',
            success: function(result) {
                $('#getdetails').html(result);
                $('#complaintdetails').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible de charger les détails de la réclamation'
                });
            }
        });
    }

    // Fonction de confirmation de suppression améliorée
    function confirmDelete(event, complainantName) {
        event.preventDefault(); // Empêche le lien de s'exécuter immédiatement
        var url = event.currentTarget.href;

        Swal.fire({
            title: 'Confirmation de suppression',
            text: `Êtes-vous sûr de vouloir supprimer la réclamation de "${complainantName}" ?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });

        return false;
    }

    // Fonction pour prévisualiser le fichier avant upload
    function previewFile(input) {
        if (input.files && input.files[0]) {
            var fileName = input.files[0].name;
            var fileSize = (input.files[0].size / 1024).toFixed(2) + ' KB';
            var fileInfo = `<small class="text-success"><i class="fa fa-check"></i> Fichier: ${fileName} (${fileSize})</small>`;

            // Ajouter l'info après l'input file
            $(input).closest('.form-group').find('.file-info').remove();
            $(input).closest('.form-group').append(`<div class="file-info" style="margin-top: 5px;">${fileInfo}</div>`);
        }
    }
</script>

<!-- Script original pour la compatibilité -->
<script type="text/javascript">
    // Garder la fonction originale pour la compatibilité
    function getRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/complaint/details/' + id,
            success: function (result) {
                $('#getdetails').html(result);
            }
        });
    }
</script>