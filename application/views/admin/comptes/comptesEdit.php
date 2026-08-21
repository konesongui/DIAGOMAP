<!-- Content Wrapper. Contains page content -->


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-credit-card"></i>Editer compte</h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php
            if ($this->rbac->hasPrivilege('souscription', 'can_add') || $this->rbac->hasPrivilege('souscription', 'can_edit')) {
                ?>
                <div class="col-md-12">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Editer compte</h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <form action="<?php echo site_url("admin/comptes/edit/" . $id); ?>"
                              id="employeeform" name="employeeform" method="post" accept-charset="utf-8">

                            <div class="box-body">
                                <?php echo validation_errors(); ?>

                                <?php if ($this->session->flashdata('msg')): ?>
                                    <?php echo $this->session->flashdata('msg'); ?>
                                <?php endif; ?>

                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Nom de l'entreprise <small class="req"> *</small></label>
                                        <input id="nom" name="nom" type="text" class="form-control"
                                               value="<?php echo set_value('nom', $comptesLis['nom']); ?>" />
                                        <span class="text-danger"><?php echo form_error('nom'); ?></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Email de contact <small class="req"> *</small></label>
                                        <input id="email" name="email" type="text" class="form-control"
                                               value="<?php echo set_value('email', $comptesLis['email']); ?>" />
                                        <span class="text-danger"><?php echo form_error('email'); ?></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Téléphone <small class="req"> *</small></label>
                                        <input id="telephone" name="telephone" type="text" class="form-control"
                                               value="<?php echo set_value('telephone', $comptesLis['telephone']); ?>" />
                                        <span class="text-danger"><?php echo form_error('telephone'); ?></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Adresse <small class="req"> *</small></label>
                                        <input id="adresse" name="adresse" type="text" class="form-control"
                                               value="<?php echo set_value('adresse', $comptesLis['adresse']); ?>" />
                                        <span class="text-danger"><?php echo form_error('adresse'); ?></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Logo (facultatif)</label>
                                        <input id="logo" name="logo" type="text" class="form-control"
                                               value="<?php echo set_value('logo', $comptesLis['logo']); ?>" />
                                        <span class="text-danger"><?php echo form_error('logo'); ?></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Forfait <small class="req"> *</small></label>
                                        <input id="forfait" name="forfait" type="text" class="form-control"
                                               value="<?php echo set_value('forfait', $comptesLis['forfait']); ?>" />
                                        <span class="text-danger"><?php echo form_error('forfait'); ?></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Date de début <small class="req"> *</small></label>
                                        <input id="date_debut" name="date_debut" type="text" class="form-control"
                                               value="<?php echo set_value('date_debut', $comptesLis['date_debut']); ?>" />
                                        <span class="text-danger"><?php echo form_error('date_debut'); ?></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Date d'expiration <small class="req"> *</small></label>
                                        <input id="date_expiration" name="date_expiration" type="text" class="form-control"
                                               value="<?php echo set_value('date_expiration', $comptesLis['date_expiration']); ?>" />
                                        <span class="text-danger"><?php echo form_error('date_expiration'); ?></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Statut <small class="req"> *</small></label>
                                        <input id="statut" name="statut" type="text" class="form-control"
                                               value="<?php echo set_value('statut', $comptesLis['statut']); ?>" />
                                        <span class="text-danger"><?php echo form_error('statut'); ?></span>
                                    </div>
                                </div>
                            </div><!-- /.box-body -->

                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right">
                                    <?php echo $this->lang->line('save'); ?>
                                </button>

                                <a href="<?php echo base_url('admin/comptes/index'); ?>"
                                   class="btn btn-primary btn-xs" style="width: 99px; height: 23px;">
                                    <i class="fa fa-arrow-left"></i>
                                </a>
                            </div>
                        </form>

                    </div>
                </div><!--/.col (right) -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('souscription', 'can_add') || $this->rbac->hasPrivilege('souscription', 'can_edit')) {
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
