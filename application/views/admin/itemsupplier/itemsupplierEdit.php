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
            <?php if ($this->rbac->hasPrivilege('supplier', 'can_add') || $this->rbac->hasPrivilege('supplier', 'can_edit')) { ?>
                <div class="col-md-12">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('edit_item_supplier'); ?></h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <form action="<?php echo site_url("admin/itemsupplier/edit/" . $id) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="box-body">

                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1"> Fournisseur</label><small class="req"> *</small>
                                    <input autofocus="" id="name" name="name" placeholder="name" type="text" class="form-control"  value="<?php echo set_value('itemsupplier', $itemsupplier['item_supplier']); ?>" />
                                    <span class="text-danger"><?php echo form_error('itemsupplier'); ?></span>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1"> Nom du responsable</label>
                                    <input id="contact_person_name" name="contact_person_name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('contact_person_name', $itemsupplier['contact_person_name']); ?>" />
                                    <span class="text-danger"><?php echo form_error('contact_person_name'); ?></span>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1">Compte contribuable/CNI</label><small class="req"> *</small>
                                    <input autofocus="" id="lastname" name="lastname" placeholder="" type="text" class="form-control"  value="<?php echo set_value('lastname', $itemsupplier['lastname']); ?>" />
                                    <span class="text-danger"><?php echo form_error('lastname'); ?></span>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1"> <?php echo $this->lang->line('phone'); ?></label>
                                    <input id="phone" name="phone" placeholder="" type="text" class="form-control"  value="<?php echo set_value('phone', $itemsupplier['phone']); ?>" />
                                    <span class="text-danger"><?php echo form_error('phone'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('email'); ?></label>
                                    <input id="email" name="email" placeholder="" type="text" class="form-control"  value="<?php echo set_value('email', $itemsupplier['email']); ?>" />
                                    <span class="text-danger"><?php echo form_error('email'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('address'); ?></label>
                                    <textarea class="form-control" id="address" name="address" placeholder="" rows="2" placeholder="Enter ..."><?php echo set_value('address', $itemsupplier['address']); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('address'); ?></span>
                                </div>

                                <div class="form-group" hidden>
                                    <label for="exampleInputEmail1"> <?php echo $this->lang->line('contact_person_phone'); ?></label>
                                    <input id="contact_person_phone" name="contact_person_phone" placeholder="" type="text" class="form-control"  value="<?php echo set_value('contact_person_phone', $itemsupplier['contact_person_phone']); ?>" />
                                    <span class="text-danger"><?php echo form_error('contact_person_phone'); ?></span>
                                </div>
                                <div class="form-group" hidden>
                                    <label for="exampleInputEmail1"> <?php echo $this->lang->line('contact_person_email'); ?></label>
                                    <input id="contact_person_email" name="contact_person_email" placeholder="" type="text" class="form-control"  value="<?php echo set_value('contact_person_email', $itemsupplier['contact_person_email']); ?>" />
                                    <span class="text-danger"><?php echo form_error('contact_person_email'); ?></span>
                                </div>

                                <div class="form-group" hidden>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea class="form-control" id="description" name="description" placeholder="" rows="3" placeholder="Enter ..."><?php echo set_value('description', $itemsupplier['description']); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('description'); ?></span>
                                </div>

                                <div class="form-group" hidden>
                                    <label for="exampleInputEmail1">Statut</label><small class="req"> *</small>

                                    <select  id="status" name="status" class="form-control" value="<?php echo set_value('status', $itemsupplier['status']); ?>">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <option value="Actif">Actif</option>
                                        <option value="Inactif">Inactif</option>

                                    </select>
                                    <span class="text-danger"><?php echo form_error('item_category_id'); ?></span>
                                </div>
                            </div><!-- /.box-body -->
                            <div class="box-footer">

                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                <a href="<?php echo base_url() ?>admin/itemsupplier" type="button" class="btn btn-primary btn-xs" style="width: 99px;height: 23px">
                                    <i class="fa fa-arrow-left"></i> </a>
                            </div>
                        </form>
                    </div>
                </div><!--/.col (right) -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('supplier', 'can_add') || $this->rbac->hasPrivilege('supplier', 'can_edit')) {
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

