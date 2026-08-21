<div class="content-wrapper" style="min-height: 946px;">
    <!-- Content Header (Page header) -->

    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> Liste des clients
        </h1>
    </section>


    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary" id="exphead">

                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Liste des clients</h3>
                        <div class="box-tools pull-right pt3">
                            <?php if ($this->rbac->hasPrivilege('clients', 'can_add')) { ?>
                                <button type="button" class="btn btn-sm"
                                        style="background: linear-gradient(135deg, #4a4a4a, #2e2e2e);
                                        color: #fff;
                                       border: none;
                                       border-radius: 8px;
                                       padding: 6px 14px;
                                       font-weight: 500;
                                       transition: 0.3s;"
                                        onmouseover="this.style.background='#1a1a1a'"
                                        onmouseout="this.style.background='linear-gradient(135deg, #4a4a4a, #2e2e2e)'"
                                        data-toggle="modal"
                                        data-target="#addClientModal">
                                    <i class="fa fa-plus"></i> Ajouter un client
                                </button>

                            <?php } ?>
                           <!-- <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>admin/clients/import" autocomplete="off">
                                <i class="fa fa-upload"></i> Importer des clients
                            </a>-->
                        </div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <?php if ($this->session->flashdata('msg')) { ?>
                            <?php echo $this->session->flashdata('msg') ?>
                        <?php } ?>
                        <?php if (isset($error_message)) { echo "<div class='alert alert-danger'>" . $error_message . "</div>"; } ?>

                        <div class="mailbox-messages table-responsive">
                            <table class="table table-striped table-bordered table-hover example" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Responsable</th>
                                    <th>Contact</th>
                                    <th>Email</th>
                                    <th>NCC</th>
                                    <th>Regime d'Imposition</th>
                                    <th>Ville</th>
                                    <th><?php echo $this->lang->line('address'); ?></th>
                                    <th>Compte contribuable</th>
                                    <th>Date</th>
                                    <th class="text-right no-print"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($itemsupplierlist)) {
                                    foreach ($itemsupplierlist as $supplier) { ?>
                                        <tr>
                                            <td><?php echo $supplier['item_supplier'] ?></td>
                                            <td><?php echo $supplier['contact_person_name'] ?></td>
                                            <td><?php echo $supplier['phone'] ?></td>
                                            <td><?php echo $supplier['email'] ?></td>
                                            <td><?php echo $supplier['ncc'] ?></td>
                                            <td><?php echo $supplier['regime_imposition'] ?></td>
                                            <td><?php echo $supplier['ville'] ?></td>
                                            <td><?php echo $supplier['address'] ?></td>
                                            <td><?php echo $supplier['comptec'] ?></td>
                                            <td>
                                                <?php echo date("d/m/Y", strtotime($supplier['created_at'])); ?>
                                            </td>

                                            <td class="text-right no-print">
                                                <?php if ($this->rbac->hasPrivilege('clients', 'can_edit')) { ?>
                                                    <a href="<?php echo base_url(); ?>admin/clients/edit/<?php echo $supplier['id'] ?>" class="btn btn-default btn-xs" title="Modifier">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } if ($this->rbac->hasPrivilege('clients', 'can_delete')) { ?>
                                                    <a href="<?php echo base_url(); ?>admin/clients/delete/<?php echo $supplier['id'] ?>" class="btn btn-default btn-xs" title="Supprimer" onclick="return confirm('Confirmer la suppression ?');">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php }
                                } ?>
                                </tbody>
                            </table>
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div>
        </div>
    </section><!-- /.content -->
</div>

<!-- MODAL AJOUT CLIENT -->
<div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="addClientModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?php echo site_url('admin/clients/create') ?>" id="clientForm" method="post">

                <div class="modal-header">
                    <h4 class="modal-title" id="addClientModalLabel">Ajouter un nouveau client</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <?php if ($this->session->flashdata('msg')) { ?>
                        <?php echo $this->session->flashdata('msg') ?>
                    <?php } ?>
                    <?php if (isset($error_message)) { echo "<div class='alert alert-danger'>" . $error_message . "</div>"; } ?>



                    <div class="form-group row">
                        <div class="col-md-4">
                            <label>Client <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Nom du responsable</label>
                            <input type="text" name="contact_person_name" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Téléphone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                    </div>

                    <div class="form-group row">
                        <div class="col-md-4">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" required class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Ville</label>
                            <input type="text" name="ville" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Compte contribuable</label>
                            <input type="text" name="comptec" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>NCC</label>
                            <input type="text" name="ncc" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Regime d'Imposition</label>
                            <input type="text" name="regime_imposition" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <label>Adresse</label>
                            <textarea name="address" class="form-control"></textarea>
                        </div>
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>




<script>
    $(document).ready(function() {
        emptyDatatable('book-list','data');
    });

    ( function ( $ ) {
        'use strict';
        $(document).ready(function () {
            initDatatable('book-list','admin/clients/index',[],[],100);
        });
    } ( jQuery ) )
</script>

