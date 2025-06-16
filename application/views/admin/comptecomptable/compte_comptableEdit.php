<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> Compte comptable</h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('clients', 'can_add') || $this->rbac->hasPrivilege('clients', 'can_edit')) { ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Modifications du compte comptable</h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <form action="<?php echo site_url("admin/comptecomptable/edit/" . $id) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="box-body">

                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="form-group">
                                    <label for="numero">Numéro du compte</label><small class="req"> *</small>
                                    <input id="numero" name="numero" placeholder="" type="text" class="form-control"  value="<?php echo set_value('numero', $compteliste['numero']); ?>" />
                                    <span class="text-danger"><?php echo form_error('numero'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="intitule">Intitulé</label>
                                    <input id="intitule" name="intitule" placeholder="" type="text" class="form-control"  value="<?php echo set_value('intitule', $compteliste['intitule']); ?>" />
                                    <span class="text-danger"><?php echo form_error('intitule'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="classe">Classe</label>
                                    <select class="form-control" name="classe" id="classe" required>
                                        <?php for ($i = 1; $i <= 7; $i++): ?>
                                            <option value="<?= $i ?>" <?= $compteliste['classe'] == $i ? 'selected' : '' ?>>Classe <?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('classe'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="type_compte">Type de compte</label>
                                    <select name="type_compte" id="type_compte" required class="form-group">
                                        <?php foreach (['actif', 'passif', 'charge', 'produit'] as $type): ?>
                                            <option value="<?= $type ?>" <?= $compteliste['type_compte'] == $type ? 'selected' : '' ?>><?= ucfirst($type) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('type_compte'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="est_actif">Compte actif ?</label>
                                    <input type="checkbox" name="est_actif" class="form-group" id="est_actif" value="1" <?= $compteliste['est_actif'] ? 'checked' : '' ?>>
                                </div>

                            </div><!-- /.box-body -->
                            <div class="box-footer">

                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                <a href="<?php echo base_url() ?>admin/comptecomptable" type="button" class="btn btn-primary btn-xs" style="width: 99px;height: 23px">
                                    <i class="fa fa-arrow-left"></i> </a>
                            </div>
                        </form>
                    </div>
                </div><!--/.col (right) -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('clients', 'can_add') || $this->rbac->hasPrivilege('clients', 'can_edit')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->
                <div class="box box-primary" id="exphead">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Compte comptable</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body  ">
                        <div class="mailbox-messages table-responsive">
                            <div class="download_label">Compte comptable</div>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                <tr>
                                    <th>Numéro du compte</th>
                                    <th>Intitulé</th>
                                    <th>Classe</th>
                                    <th>Type de compte</th>
                                    <th>Actif</th>
                                    <th class="text-right no-print"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($comptecomptalist)) {
                                    ?>

                                    <?php
                                } else {
                                    $count = 1;
                                    foreach ($comptecomptalist as $compte) {
                                        ?>
                                        <tr>
                                            <td class="mailbox-name"><?php echo $compte['numero'] ?></td>

                                            <td class="mailbox-name"><?php echo $compte['intitule'] ?></td>
                                            <td class="mailbox-name"><?php echo $compte['classe'] ?></td>
                                            <td class="mailbox-name"><?php echo $compte['type_compte'] ?></td>
                                            <td class="mailbox-name"><?php echo $compte['est_actif'] ?></td>
                                            <td class="mailbox-date pull-right no-print">
                                                <?php if ($this->rbac->hasPrivilege('clients', 'can_edit')) { ?>
                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/comptecomptable/edit/<?php echo $compte['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } if ($this->rbac->hasPrivilege('clients', 'can_delete')) { ?>
                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/comptecomptable/delete/<?php echo $compte['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $count++;
                                }
                                ?>

                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div>

        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<script>
    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });
</script>

