
<div class="content-wrapper" style="min-height: 946px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> <?php echo $this->lang->line('inventory'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('souscription', 'can_add')) { ?>
                <div class="col-md-12">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Informations Administratives</h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <form  action="<?php echo site_url('admin/comptes/create') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                        <div class="box-body">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php echo $this->session->flashdata('msg') ?>
                                <?php } ?>
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="form-group col-md-4">
                                    <label for="nom">Nom de l'entreprise</label>
                                    <input type="text" name="nom" class="form-control">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="email">Email de contact</label>
                                    <input type="email" name="email" class="form-control">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="telephone">Téléphone</label>
                                    <input type="text" name="telephone" class="form-control">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="adresse">Adresse</label>
                                    <input type="text" name="adresse" class="form-control">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="logo">Logo (facultatif)</label>
                                    <input class="filestyle form-control" type='file' name="logo"/>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="forfait">Forfait</label>
                                    <select name="forfait" class="form-control">
                                        <option value="basic">Basic</option>
                                        <option value="pro">Pro</option>
                                        <option value="premium">Premium</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="date_debut">Date de début</label>
                                    <input type="date" name="date_debut" class="form-control">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="date_expiration">Date d'expiration</label>
                                    <input type="date" name="date_expiration" class="form-control">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="statut">Statut</label>
                                    <select name="statut" class="form-control">
                                        <option value="actif">Actif</option>
                                        <option value="expiré">Expiré</option>
                                        <option value="suspendu">Suspendu</option>
                                    </select>
                                </div>


                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                 <a href="<?php echo base_url('admin/comptes/index'); ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Retour à la liste
                                </a>
                            </div>
                        </form>
                    </div>
                </div><!--/.col (right) -->
                <!-- left column -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('souscription', 'can_add')) {
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



