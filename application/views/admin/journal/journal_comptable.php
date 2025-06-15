
<div class="content-wrapper" style="min-height: 946px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i>Journal comptable</h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('clients', 'can_add')) { ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Créer un journal</h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <form  action="<?php echo site_url('admin/clients/create') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php echo $this->session->flashdata('msg') ?>
                                <?php } ?>
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="form-group">
                                    <label for="code_journal">Code du Journal</label><small class="req"> *</small>
                                    <input autofocus="" id="code_journal" name="code_journal" placeholder="" type="text" class="form-control"  value="<?php echo set_value('code_journal'); ?>" />
                                    <span class="text-danger"><?php echo form_error('code_journal'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="libelle_journal">Libellé du Journal</label><small class="req"> *</small>
                                    <input autofocus="" id="libelle_journal" name="libelle_journal" placeholder="" type="text" class="form-control"  value="<?php echo set_value('libelle_journal'); ?>" />
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
            if ($this->rbac->hasPrivilege('clients', 'can_add')) {
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
                                <?php if (empty($itemsupplierlist)) {
                                    ?>

                                    <?php
                                } else {
                                    $count = 1;
                                    foreach ($itemsupplierlist as $supplier) {
                                        ?>
                                        <tr>


                                            <!-- <td class="mailbox-name">

                                                    <a href="#" data-toggle="popover" class="detail_popover" >
                                                        <?php echo $supplier['item_supplier'] ?>
                                                        <br>
                                                    </a>
                                                    <?php
                                            if ($supplier['phone'] != "") {
                                                ?>
                                                        <i class="fa fa-phone-square"></i> <?php echo $supplier['phone'] ?>
                                                        <br>
                                                        <?php
                                            }
                                            ?>
                                                    <?php
                                            if ($supplier['email'] != "") {
                                                ?>
                                                        <i class="fa fa-envelope"></i> <?php echo $supplier['email'] ?>

                                                        <?php
                                            }
                                            ?>

                                                    <div class="fee_detail_popover" style="display: none">
                                                        <?php
                                            if ($supplier['description'] == "") {
                                                ?>
                                                            <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                            <?php
                                            } else {
                                                ?>
                                                            <p class="text text-info"><?php echo $supplier['description']; ?></p>
                                                            <?php
                                            }
                                            ?>
                                                    </div>
                                                </td>-->
                                            <td class="mailbox-name"><?php echo $supplier['item_supplier'] ?></td>

                                            <td class="mailbox-name"><?php echo $supplier['phone'] ?></td>
                                            <td class="mailbox-name"><?php echo $supplier['email'] ?></td>
                                            <td class="mailbox-name"><?php echo $supplier['address'] ?></td>
                                            <td class="mailbox-name"><?php echo $supplier['comptec'] ?></td>


                                            <td class="mailbox-date pull-right no-print">
                                                <?php if ($this->rbac->hasPrivilege('clients', 'can_edit')) { ?>
                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/clients/edit/<?php echo $supplier['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } if ($this->rbac->hasPrivilege('clients', 'can_delete')) { ?>
                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/clients/delete/<?php echo $supplier['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
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
<script type="text/javascript">
    $(document).ready(function () {
        $("#btnreset").click(function () {
            $("#form1")[0].reset();
        });
    });

</script>

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

<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';


</script>
