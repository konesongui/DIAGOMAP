
<div class="content-wrapper" style="min-height: 946px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i>Journal comptable</h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('income', 'can_add')) { ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Créer un journal</h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <form  action="<?php echo site_url('admin/journal/create') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php echo $this->session->flashdata('msg') ?>
                                <?php } ?>
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="form-group">
                                    <label for="code_journal">Code du Journal</label><small class="req"> *</small>
                                    <input autofocus="" id="code_journal" name="code_journal" placeholder="" type="text" class="form-control"  value="<?php echo set_value('code_journal', $journallist['code_journal']); ?>" />
                                    <span class="text-danger"><?php echo form_error('code_journal'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="libelle_journal">Libellé du Journal</label><small class="req"> *</small>
                                    <input autofocus="" id="libelle_journal" name="libelle_journal" placeholder="" type="text" class="form-control"  value="<?php echo set_value('journal', $journallist['libelle_journal']); ?>"/>
                                    <span class="text-danger"><?php echo form_error('libelle_journal'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="type_journal"> Type de Journal</label><small class="req"> *</small>
                                    <select name="type_journal" required class="form-control"  value="<?php echo set_value('libelle_journal'); ?>" />
                                    <option value="Général">Général</option>
                                    <option value="Ventes">Ventes</option>
                                    <option value="Achats">Achats</option>
                                    <option value="Caisse">Caisse</option>
                                    <option value="Banque">Banque</option>
                                    <option value="Banque">Banque</option>

                                    </select>
                                    <span class="text-danger"><?php echo form_error('type_journal'); ?></span>
                                </div>

                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </form>
                    </div>
                </div><!--/.col (right) -->
                <!-- left column -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('income', 'can_add')) {
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
                                    <th>Date de création</th>


                                    <th class="text-right no-print"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($journallist)) {
                                    ?>

                                    <?php
                                } else {
                                    $count = 1;
                                    foreach ($journallist as $journali) {
                                        ?>
                                        <tr>
                                            <td class="mailbox-name"><?php echo $journali['code_journal'] ?></td>

                                            <td class="mailbox-name"><?php echo $journali['libelle_journal'] ?></td>
                                            <td class="mailbox-name"><?php echo $journali['type_journal'] ?></td>
                                            <td class="mailbox-name"><?php echo $journali['date_creation'] ?></td>



                                            <td class="mailbox-date pull-right no-print">
                                                <?php if ($this->rbac->hasPrivilege('income', 'can_edit')) { ?>
                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/journal/edit/<?php echo $journali['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } if ($this->rbac->hasPrivilege('income', 'can_delete')) { ?>
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

            <!-- right column -->

        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div>
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
