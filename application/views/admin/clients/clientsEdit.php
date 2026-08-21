<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1><i class="fa fa-object-group"></i> Clients</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('clients', 'can_add') || $this->rbac->hasPrivilege('clients', 'can_edit')) { ?>
                <div class="col-md-12">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Modifications</h3>
                        </div><!-- /.box-header -->

                        <!-- form start -->
                        <form action="<?php echo site_url("admin/clients/edit/" . $id) ?>"
                              id="employeeform"
                              name="employeeform"
                              method="post"
                              accept-charset="utf-8">

                            <div class="box-body">
                                <?php echo $this->customlib->getCSRF(); ?>

                                <!-- Ligne 1 -->
                                <div class="form-group row">
                                    <div class="col-md-3">
                                        <label>Client <small class="req"> *</small></label>
                                        <input id="name" name="name" placeholder="Nom du client" type="text"
                                               class="form-control"
                                               value="<?php echo set_value('itemsupplier', $itemsupplier['item_supplier']); ?>" />
                                        <span class="text-danger"><?php echo form_error('itemsupplier'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Nom du responsable</label>
                                        <input id="contact_person_name" name="contact_person_name" type="text"
                                               class="form-control"
                                               value="<?php echo set_value('contact_person_name', $itemsupplier['contact_person_name']); ?>" />
                                        <span class="text-danger"><?php echo form_error('contact_person_name'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label><?php echo $this->lang->line('phone'); ?></label>
                                        <input id="phone" name="phone" type="text" class="form-control"
                                               value="<?php echo set_value('phone', $itemsupplier['phone']); ?>" />
                                        <span class="text-danger"><?php echo form_error('phone'); ?></span>
                                    </div>

                                    <div class="col-md-3">
                                        <label>NCC</label>
                                        <input id="ncc" name="ncc" type="text" class="form-control"
                                               value="<?php echo set_value('ncc', $itemsupplier['ncc']); ?>" />
                                        <span class="text-danger"><?php echo form_error('ncc'); ?></span>
                                    </div>

                                    <div class="col-md-3">
                                        <label>Regime d'Imposition</label>
                                        <input id="phone" name="regime_imposition" type="text" class="form-control"
                                               value="<?php echo set_value('regime_imposition', $itemsupplier['regime_imposition']); ?>" />
                                        <span class="text-danger"><?php echo form_error('regime_imposition'); ?></span>
                                    </div>

                                    <div class="col-md-3">
                                        <label><?php echo $this->lang->line('email'); ?></label>
                                        <input id="email" name="email" type="text" class="form-control"
                                               value="<?php echo set_value('email', $itemsupplier['email']); ?>" />
                                        <span class="text-danger"><?php echo form_error('email'); ?></span>
                                    </div>
                                </div>

                                <!-- Ligne 2 -->
                                <div class="form-group row">


                                </div>

                                <!-- Ligne 3 -->
                                <div class="form-group row">
                                    <div class="col-md-3">
                                        <label>Ville</label>
                                        <input id="ville" name="ville" type="text" class="form-control"
                                               value="<?php echo set_value('ville', $itemsupplier['ville']); ?>" />
                                        <span class="text-danger"><?php echo form_error('ville'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Compte Contribuable</label>
                                        <input id="comptec" name="comptec" type="text" class="form-control"
                                               value="<?php echo set_value('comptec', $itemsupplier['comptec']); ?>" />
                                        <span class="text-danger"><?php echo form_error('comptec'); ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label><?php echo $this->lang->line('address'); ?></label>
                                        <input id="address" name="address" type="text" class="form-control"
                                               value="<?php echo set_value('address', $itemsupplier['address']); ?>" />
                                        <span class="text-danger"><?php echo form_error('address'); ?></span>
                                    </div>
                                </div>

                                <!-- Ligne 4 (Adresse seule) -->

                                <!-- Champs cachés -->


                            </div><!-- /.box-body -->

                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                <a href="<?php echo base_url() ?>admin/clients" class="btn btn-primary btn-xs">
                                    <i class="fa fa-arrow-left"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div><!--/.col (left form) -->
            <?php } ?>

            <!-- Colonne de droite -->
            <div class="col-md-<?php echo ($this->rbac->hasPrivilege('clients', 'can_add') || $this->rbac->hasPrivilege('clients', 'can_edit')) ? "6" : "12"; ?>">
                <!-- ICI tu peux ajouter un tableau ou autre contenu -->
            </div>
        </div><!-- /.row -->
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
