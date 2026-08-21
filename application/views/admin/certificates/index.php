<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-certificate"></i>
                            Gestion des Modèles de Certificats
                        </h3>
                        <?php if ($this->rbac->hasPrivilege('certificate', 'can_add')): ?>
                            <div class="pull-right">
                                <a href="<?php echo base_url('admin/certificate_manager/create'); ?>" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Nouveau Modèle
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="box-body">
                        <?php if ($this->session->flashdata('msg')): ?>
                            <?php echo $this->session->flashdata('msg'); ?>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Titre</th>
                                    <th>Code</th>
                                    <th>Statut</th>
                                    <th>Créé le</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($certificates as $cert): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            $icon = isset($types[$cert->template_type]['icon']) ? $types[$cert->template_type]['icon'] : 'fa-file';
                                            $name = isset($types[$cert->template_type]['name']) ? $types[$cert->template_type]['name'] : $cert->template_type;
                                            ?>
                                            <i class="fa <?php echo $icon; ?>"></i>
                                            <?php echo $name; ?>
                                        </td>
                                        <td><?php echo $cert->title; ?></td>
                                        <td><code><?php echo $cert->generated_code; ?></code></td>
                                        <td>
                                            <?php if ($cert->is_active): ?>
                                                <span class="label label-success">Actif</span>
                                            <?php else: ?>
                                                <span class="label label-danger">Inactif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($cert->created_at)); ?></td>
                                        <td class="text-right">
                                            <button class="btn btn-default btn-xs view-certificate" data-id="<?php echo $cert->id; ?>" title="Aperçu">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <?php if ($this->rbac->hasPrivilege('certificate', 'can_edit')): ?>
                                                <a href="<?php echo base_url('admin/certificate_manager/edit/'.$cert->id); ?>" class="btn btn-default btn-xs" title="Modifier">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($this->rbac->hasPrivilege('certificate', 'can_delete')): ?>
                                                <a href="<?php echo base_url('admin/certificate_manager/delete/'.$cert->id); ?>"
                                                   class="btn btn-default btn-xs"
                                                   title="Supprimer"
                                                   onclick="return confirm('<?php echo $this->lang->line('delete_confirm'); ?>');">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Aperçu -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Aperçu du Certificat</h4>
            </div>
            <div class="modal-body" id="previewContent">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-3x"></i>
                    <p>Chargement...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.view-certificate').on('click', function() {
            var certificateId = $(this).data('id');
            $('#previewModal').modal('show');

            $.ajax({
                url: '<?php echo base_url("admin/certificate_manager/preview"); ?>',
                method: 'post',
                data: {certificate_id: certificateId},
                success: function(data) {
                    $('#previewContent').html(data);
                }
            });
        });
    });
</script>