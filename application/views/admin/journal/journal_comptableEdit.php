<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> Clients</h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('clients', 'can_add') || $this->rbac->hasPrivilege('clients', 'can_edit')) { ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Modifications du journal comptable</h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <form action="<?php echo site_url("admin/journal/edit/" . $id) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="box-body">

                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="form-group">
                                    <label for="code_journal">Code du Journal</label><small class="req"> *</small>
                                    <input autofocus="" id="code_journal" name="code_journal" placeholder="" type="text" class="form-control"  value="<?php echo set_value('code_journal', $journaliste['code_journal']); ?>" />
                                    <span class="text-danger"><?php echo form_error('code_journal'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="libelle_journal"> Libellé du Journal</label>
                                    <input id="libelle_journal" name="libelle_journal" placeholder="" type="text" class="form-control"  value="<?php echo set_value('libelle_journal', $journaliste['libelle_journal']); ?>" />
                                    <span class="text-danger"><?php echo form_error('libelle_journal'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="type_journal">Type de Journal</label>
                                     <select name="type_journal" required class="form-control" value="<?php echo set_value('type_journal', $journaliste['type_journal']); ?>" />
                                    <?php
                                    $types = ['Général', 'Ventes', 'Achats', 'Caisse', 'Banque'];
                                    foreach ($types as $type) {
                                        $selected = (isset($journaliste) && $journaliste['type_journal'] == $type) ? 'selected' : '';
                                        echo "<option value=\"$type\" $selected>$type</option>";
                                    }
                                    ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('type_journal'); ?></span>
                                </div>

                            </div><!-- /.box-body -->
                            <div class="box-footer">

                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                <a href="<?php echo base_url() ?>admin/journal" type="button" class="btn btn-primary btn-xs" style="width: 99px;height: 23px">
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
                        <h3 class="box-title titlefix">Journal comptable</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body  ">
                        <div class="mailbox-messages table-responsive">
                            <div class="download_label">Journal comptable</div>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                <tr>
                                    <th>Code du Journal</th>

                                    <th>Libellé du Journal</th>
                                    <th>Type de Journal</th>
                                    <th class="text-right no-print"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($journalist)) {
                                    ?>

                                    <?php
                                } else {
                                    $count = 1;
                                    foreach ($journalist as $journali) {
                                        ?>
                                        <tr>
                                            <td class="mailbox-name"><?php echo $journali['code_journal'] ?></td>

                                            <td class="mailbox-name"><?php echo $journali['libelle_journal'] ?></td>
                                            <td class="mailbox-name"><?php echo $journali['type_journal'] ?></td>
                                            <td class="mailbox-date pull-right no-print">
                                                <?php if ($this->rbac->hasPrivilege('clients', 'can_edit')) { ?>
                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/journal/edit/<?php echo $journali['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } if ($this->rbac->hasPrivilege('clients', 'can_delete')) { ?>
                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/journal/delete/<?php echo $journali['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
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

