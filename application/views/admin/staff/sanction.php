<style type="text/css">
    @media print {
        .no-print, .no-print * {
            display: none !important;
        }
    }
    .dataTables_filter {
        float: right;
    }
    .dataTables_filter input {
        margin-left: 5px;
    }
    .dt-buttons {
        margin-bottom: 10px;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-sitemap"></i> <?php echo $this->lang->line('human_resource'); ?></h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Liste des sanctions disciplinaires</h3>
                        <div class="box-tools pull-right no-print">
                            <?php if (($this->rbac->hasPrivilege('sanction', 'can_add'))) { ?>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#sanctionModal">
                                    <i class="fa fa-plus"></i> Ajouter une sanction
                                </button>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <!-- Filtres supplémentaires (facultatifs) -->
                        <div class="row no-print" style="margin-bottom:10px;">
                            <div class="col-md-3">
                                <label>Filtrer par action</label>
                                <select id="filterAction" class="form-control">
                                    <option value="">Toutes</option>
                                    <option value="Avertissement">Avertissement</option>
                                    <option value="Mise à pied disciplinaire">Mise à pied disciplinaire</option>
                                    <option value="Licenciement">Licenciement</option>
                                    <option value="Rétrogadation">Rétrogadation</option>
                                    <option value="Suspension">Suspension</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Filtrer par employé</label>
                                <input type="text" id="filterEmploye" class="form-control" placeholder="Nom de l'employé">
                            </div>
                        </div>

                        <div class="table-responsive mailbox-messages">
                            <table class="table table-striped table-bordered" id="sanctionTable">
                                <thead>
                                <tr>
                                    <th>Nom et prénom</th>
                                    <th>Designation</th>
                                    <th>Action</th>
                                    <th>Détails</th>
                                    <th>Date</th>
                                    <th class="text-right no-print">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($designation as $value) { ?>
                                    <tr>
                                        <td><?php echo $value['empname']; ?></td>
                                        <td><?php echo $value['designation']; ?></td>
                                        <td><?php echo $value['action']; ?></td>
                                        <td><?php echo $value['reason']; ?></td>
                                        <td><?php echo $value['date']; ?></td>
                                        <!--<td class="text-right no-print">
                                            <?php if ($this->rbac->hasPrivilege('sanction', 'can_edit')) { ?>
                                                <a href="<?php echo base_url(); ?>admin/sanction/sanctionedit/<?php echo $value['id']; ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="Modifier">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            <?php } ?>
                                            <?php if ($this->rbac->hasPrivilege('sanction', 'can_delete')) { ?>
                                                <a href="<?php echo base_url(); ?>admin/sanction/sanctiondelete/<?php echo $value['id']; ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="Supprimer" onclick="return confirm('<?php echo $this->lang->line('delete_confirm'); ?>');">
                                                    <i class="fa fa-remove"></i>
                                                </a>
                                            <?php } ?>
                                        </td>-->
                                        <td class="text-right no-print">
                                            <!-- Bouton Imprimer -->
                                            <a href="<?php echo base_url(); ?>admin/sanction/imprimer_employe/<?php echo base64_encode($value['empname']); ?>"
                                               class="btn btn-default btn-xs" target="_blank"
                                               data-toggle="tooltip" title="Imprimer les sanctions de cet employé">
                                                <i class="fa fa-print"></i>
                                            </a>
                                            <!-- Boutons Modifier / Supprimer existants -->
                                            <?php if ($this->rbac->hasPrivilege('sanction', 'can_edit')) { ?>
                                                <a href="<?php echo base_url(); ?>admin/sanction/sanctionedit/<?php echo $value['id']; ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="Modifier">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            <?php } ?>
                                            <?php if ($this->rbac->hasPrivilege('sanction', 'can_delete')) { ?>
                                                <a href="<?php echo base_url(); ?>admin/sanction/sanctiondelete/<?php echo $value['id']; ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="Supprimer" onclick="return confirm('<?php echo $this->lang->line('delete_confirm'); ?>');">
                                                    <i class="fa fa-remove"></i>
                                                </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- ============ MODAL AMÉLIORÉE ============ -->
<div class="modal fade" id="sanctionModal" tabindex="-1" role="dialog" aria-labelledby="sanctionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #3c8dbc; color: #fff; border-radius: 5px 5px 0 0;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="sanctionModalLabel">
                   
                    <?php echo (isset($result)) ? 'Modifier la sanction' : 'Ajouter une sanction'; ?>
                </h4>
            </div>
            <form id="formSanction" action="<?php echo site_url('admin/sanction/sanction'); ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body" style="padding: 25px 30px;">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <input type="hidden" name="designationid" id="designationid" value="<?php echo isset($result) ? $result['id'] : ''; ?>">

                    <!-- Ligne 1 : Rôle et Employé -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role"> Rôle <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-control select2" onchange="getEmployeeName(this.value)">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($staffrole as $rolevalue) { ?>
                                        <option value="<?php echo $rolevalue['id']; ?>" <?php echo (isset($result) && $result['role'] == $rolevalue['id']) ? 'selected' : ''; ?>>
                                            <?php echo $rolevalue['type']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="text-danger"><?php echo form_error('role'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="empname"> Employé <span class="text-danger">*</span></label>
                                <select name="empname" id="empname" class="form-control select2">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php if (isset($result)) { ?>
                                        <option value="<?php echo htmlspecialchars($result['empname'], ENT_QUOTES, 'UTF-8'); ?>" selected>
                                            <?php echo htmlspecialchars($result['empname'], ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="text-danger"><?php echo form_error('empname'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Ligne 2 : Titre -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="type"> Titre (désignation) <span class="text-danger">*</span></label>
                                <input type="text" name="type" id="type" class="form-control" placeholder="Ex: Retard répété, insubordination..." value="<?php echo isset($result) ? $result['designation'] : ''; ?>" />
                                <span class="text-danger"><?php echo form_error('type'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Ligne 3 : Action et Date -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="action"> Action <span class="text-danger">*</span></label>
                                <select name="action" id="action" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <option value="Avertissement" <?php echo (isset($result) && $result['action'] == 'Avertissement') ? 'selected' : ''; ?>>Avertissement</option>
                                    <option value="Mise à pied disciplinaire" <?php echo (isset($result) && $result['action'] == 'Mise à pied disciplinaire') ? 'selected' : ''; ?>>Mise à pied disciplinaire</option>
                                    <option value="Licenciement" <?php echo (isset($result) && $result['action'] == 'Licenciement') ? 'selected' : ''; ?>>Licenciement</option>
                                    <option value="Rétrogadation" <?php echo (isset($result) && $result['action'] == 'Rétrogadation') ? 'selected' : ''; ?>>Rétrogadation</option>
                                    <option value="Suspension" <?php echo (isset($result) && $result['action'] == 'Suspension') ? 'selected' : ''; ?>>Suspension</option>
                                </select>
                                <span class="text-danger"><?php echo form_error('action'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date"> Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" id="date" class="form-control" value="<?php echo isset($result) ? $result['date'] : set_value('date'); ?>" />
                                <span class="text-danger"><?php echo form_error('date'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Ligne 4 : Motif -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="reason"> Motif (raison)</label>
                                <textarea name="reason" id="reason" rows="4" class="form-control" placeholder="Décrire les faits, les circonstances..."><?php echo isset($result) ? $result['reason'] : ''; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Statut actif (caché) -->
                    <input type="hidden" name="status" value="yes">
                </div>
                <div class="modal-footer" style="background: #f5f5f5; border-radius: 0 0 5px 5px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    // Fonction pour charger les employés selon le rôle (existante)
    function getEmployeeName(role) {
        var base_url = '<?php echo base_url(); ?>';
        $("#empname").html('<option value=""><?php echo $this->lang->line('select'); ?></option>');
        if (role == '') return;
        $.ajax({
            type: "POST",
            url: base_url + "admin/staff/getEmployeeByRole",
            data: {'role': role},
            dataType: "json",
            success: function(data) {
                var div_data = '';
                $.each(data, function(i, obj) {
                    var fullname = (obj.name + " " + obj.surname).trim();
                    // Utilisation des guillemets doubles pour l'attribut value
                    div_data += "<option value=\"" + fullname + "\">" + fullname + " (" + obj.employee_id + ")</option>";
                });
                $('#empname').append(div_data);
            }
        });
    }

    // Initialisation de DataTable
    $(document).ready(function() {
        var table = $('#sanctionTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copyHtml5', text: 'Copier' },
                { extend: 'excelHtml5', text: 'Excel' },
                { extend: 'csvHtml5', text: 'CSV' },
                { extend: 'pdfHtml5', text: 'PDF' },
                { extend: 'print', text: 'Imprimer' }
            ],
            order: [[4, 'desc']], // tri par date décroissante
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' // traduction en français
            }
        });

        // Filtres personnalisés
        $('#filterAction').on('change', function() {
            table.column(2).search(this.value).draw();
        });
        $('#filterEmploye').on('keyup', function() {
            table.column(0).search(this.value).draw();
        });

        // Si on est en édition (présence de $result), on ouvre la modal automatiquement
        <?php if (isset($result)) { ?>
        $('#sanctionModal').modal('show');
        <?php } ?>
    });
</script>