<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> <?php echo $this->lang->line('inventory'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('quote_supplier', 'can_add') || $this->rbac->hasPrivilege('supplier', 'can_edit')) { ?>
                <div class="col-md-12">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Modifier le devis fournisseur</h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <form action="<?php echo site_url("admin/devissupplier/edit/" . $id) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="box-body">

                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1"> Fournisseur</label><small class="req"> *</small>
                                    <input autofocus="" id="name" name="name" placeholder="name" type="text" class="form-control"  value="<?php echo set_value('name', $devissupplier['name']); ?>" />
                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1">Référence</label><small class="req"> *</small>
                                    <input autofocus="" id="ref" name="ref" placeholder="" type="text" class="form-control"  value="<?php echo set_value('ref', $devissupplier['ref']); ?>" />
                                    <span class="text-danger"><?php echo form_error('ref'); ?></span>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1"> Article</label>
                                    <input id="phone" name="article" placeholder="" type="text" class="form-control"  value="<?php echo set_value('article', $devissupplier['article']); ?>" />
                                    <span class="text-danger"><?php echo form_error('article'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1">Montant</label>
                                    <input id="montant" name="montant" placeholder="" type="text" class="form-control"  value="<?php echo set_value('montant', $devissupplier['montant']); ?>" />
                                    <span class="text-danger"><?php echo form_error('montant'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('attach_document'); ?></label>
                                    <input id="documents" name="documents" placeholder="" type="file" class="filestyle form-control"  value="<?php echo set_value('documents', $devissupplier['documents']); ?>" />
                                    <span class="text-danger"><?php echo form_error('documents'); ?></span>
                                </div>
                                <div class="form-group col-md-4" hidden>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('address'); ?></label>
                                    <textarea class="form-control" id="address" name="address" placeholder="" rows="3" placeholder="Enter ..."><?php echo set_value('address', $devissupplier['address']); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('address'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1">Date</label>
                                    <input id="date" name="date" placeholder="" type="text" class="form-control date"  value="<?php echo set_value('date', $devissupplier['date']); ?>" />
                                    <span class="text-danger"><?php echo form_error('date'); ?></span>
                                </div>



                            </div><!-- /.box-body -->
                            <div class="box-footer">

                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                <a href="<?php echo base_url() ?>admin/devissupplier" type="button" class="btn btn-primary btn-xs" style="width: 99px;height: 23px">
                                    <i class="fa fa-arrow-left"></i> </a>
                            </div>
                        </form>
                    </div>
                </div><!--/.col (right) -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('quote_supplier', 'can_add') || $this->rbac->hasPrivilege('quote_supplier', 'can_edit')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->

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

