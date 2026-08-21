
<div class="content-wrapper" style="min-height: 946px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> <?php echo $this->lang->line('inventory'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('quote_supplier', 'can_add')) { ?>
                <div class="col-md-12">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Devis fournisseur</h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <form  action="<?php echo site_url('admin/devissupplier/create') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php echo $this->session->flashdata('msg') ?>
                                <?php } ?>
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1">Fournisseur</label><small class="req"> *</small>
                                    <!--<input autofocus="" id="name" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" />-->
                                    <select class="form-control " name="name">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($itemsupplierlist as $key => $suppliers) {
                                            ?>
                                            <option value="<?php echo $suppliers['item_supplier']; ?>"><?php echo $suppliers['item_supplier'] .' ' . $suppliers['lastname']. ' (' . $suppliers['phone'] . ')'; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1">Référence</label><small class="req"> *</small>
                                    <input autofocus="" id="ref" name="ref" placeholder="" type="text" class="form-control"  value="<?php echo set_value('ref'); ?>" />
                                    <span class="text-danger"><?php echo form_error('ref'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1">Article</label>
                                    <input id="article" name="article" placeholder="" type="text" class="form-control"  value="<?php echo set_value('article'); ?>" />
                                    <span class="text-danger"><?php echo form_error('article'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1">Montant</label>
                                    <input id="text" name="montant" placeholder="" type="text" class="form-control"  value="<?php echo set_value('montant'); ?>" />
                                    <span class="text-danger"><?php echo form_error('montant'); ?></span>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('attach_document'); ?></label>
                                    <input class="filestyle form-control" type='file' name='file'  />
                                    <span class="text-danger"><?php echo form_error('documents'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1"> Date</label>
                                    <input id="date" name="date" placeholder="" type="text" class="form-control date"  value="<?php echo set_value('date'); ?>" />
                                    <span class="text-danger"><?php echo form_error('date'); ?></span>
                                </div>
                                <div class="form-group" hidden>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('contact_person_phone'); ?></label>
                                    <input id="contact_person_phone" name="contact_person_phone" placeholder="" type="text" class="form-control"  value="<?php echo set_value('contact_person_phone'); ?>" />
                                    <span class="text-danger"><?php echo form_error('contact_person_phone'); ?></span>
                                </div>
                                <div class="form-group" hidden>
                                    <label for="exampleInputEmail1"> <?php echo $this->lang->line('contact_person_email'); ?></label>
                                    <input id="contact_person_email" name="contact_person_email" placeholder="" type="email" class="form-control"  value="<?php echo set_value('contact_person_email'); ?>" />
                                    <span class="text-danger"><?php echo form_error('contact_person_email'); ?></span>
                                </div>
                                <div class="form-group  col-md-4" hidden>
                                    <label for="exampleInputEmail1">Autre détails</label>
                                    <textarea class="form-control" id="description" name="description" placeholder="" rows="3" placeholder="Enter ..."><?php echo set_value('description'); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('description'); ?></span>
                                </div>
                                <div class="form-group" hidden>
                                    <label for="exampleInputEmail1">Status</label><small class="req"> *</small>

                                    <select  id="status" name="status" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <option value="Activé">Activé</option>
                                        <option value="Désactivé">Désactivé</option>

                                    </select>
                                    <span class="text-danger"><?php echo form_error('item_category_id'); ?></span>
                                </div>
                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                <a href="<?php echo base_url('admin/devissupplier/index'); ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Retour à la liste
                                </a>
                            </div>

                        </form>
                    </div>
                </div><!--/.col (right) -->
                <!-- left column -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('quote_supplier', 'can_add')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->

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
