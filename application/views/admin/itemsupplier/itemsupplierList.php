<div class="content-wrapper" style="min-height: 946px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> <?php echo $this->lang->line('inventory'); ?>
            <small>Gestion des fournisseurs</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary" id="exphead">

                    <div class="box-header with-border ptbnull supplier-header">
                        <h3 class="box-title titlefix mb-0">
                            <i class="fa fa-truck text-primary"></i> Liste des fournisseurs
                        </h3>

                        <?php if ($this->rbac->hasPrivilege('supplier', 'can_add')) { ?>
                            <button type="button" class="btn btn-primary btn-sm supplier-add-btn" data-toggle="modal" data-target="#addSupplierModal">
                                <i class="fa fa-plus"></i> Ajouter un fournisseur
                            </button>
                        <?php } ?>
                    </div>

                    <div class="box-body">
                        <div class="mailbox-messages table-responsive">
                            <div class="download_label">Liste des fournisseurs</div>

                            <table class="table table-striped table-bordered table-hover example align-middle">
                                <thead>
                                    <tr class="bg-light">
                                        <th><i class="fa fa-building-o"></i> Nom</th>
                                        <th><i class="fa fa-user"></i> Responsable</th>
                                        <th><i class="fa fa-id-card-o"></i> Compte contribuable / CNI</th>
                                        <th><i class="fa fa-phone"></i> Téléphone</th>
                                        <th><i class="fa fa-envelope"></i> Email</th>
                                        <th><i class="fa fa-map-marker"></i> <?php echo $this->lang->line('address'); ?></th>
                                        <th class="text-right no-print" style="width:110px;"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($itemsupplierlist)) {
                                    foreach ($itemsupplierlist as $supplier) { ?>
                                        <tr>
                                            <td><strong><?php echo $supplier['item_supplier']; ?></strong></td>
                                            <td><?php echo $supplier['contact_person_name'] ? $supplier['contact_person_name'] : '<span class="text-muted">—</span>'; ?></td>
                                            <td><?php echo $supplier['lastname']; ?></td>
                                            <td>
                                                <?php if (!empty($supplier['phone'])) { ?>
                                                    <a href="tel:<?php echo $supplier['phone']; ?>"><?php echo $supplier['phone']; ?></a>
                                                <?php } else { ?>
                                                    <span class="text-muted">—</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($supplier['email'])) { ?>
                                                    <a href="mailto:<?php echo $supplier['email']; ?>"><?php echo $supplier['email']; ?></a>
                                                <?php } else { ?>
                                                    <span class="text-muted">—</span>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo $supplier['address'] ? $supplier['address'] : '<span class="text-muted">—</span>'; ?></td>
                                            <td class="text-right no-print">
                                                <div class="btn-group">
                                                    <?php if ($this->rbac->hasPrivilege('supplier', 'can_edit')) { ?>
                                                        <a href="<?php echo base_url(); ?>admin/itemsupplier/edit/<?php echo $supplier['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="Modifier">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } if ($this->rbac->hasPrivilege('supplier', 'can_delete')) { ?>
                                                        <a href="<?php echo base_url(); ?>admin/itemsupplier/delete/<?php echo $supplier['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="Supprimer" onclick="return confirm('Voulez-vous vraiment supprimer ce fournisseur ?');">
                                                            <i class="fa fa-remove text-danger"></i>
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fa fa-info-circle"></i> Aucun fournisseur enregistré pour le moment.
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Modal Ajout Fournisseur -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?php echo site_url('admin/itemsupplier/create') ?>" id="employeeform" method="post" accept-charset="utf-8">

                <div class="modal-header supplier-modal-header">
                    <h4 class="modal-title" id="addSupplierModalLabel">
                        <i class="fa fa-plus-circle"></i> Ajouter un fournisseur
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body supplier-modal-body">
                    <?php echo $this->customlib->getCSRF(); ?>

                    <div class="supplier-section-title">
                        <i class="fa fa-info-circle"></i> Informations générales
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Fournisseur <small class="req text-danger">*</small></label>
                            <div class="input-icon-group">
                                <span class="input-icon"><i class="fa fa-building-o"></i></span>
                                <input type="text" name="name" class="form-control" placeholder="Nom du fournisseur" value="<?php echo set_value('name'); ?>" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Nom du responsable</label>
                            <div class="input-icon-group">
                                <span class="input-icon"><i class="fa fa-user"></i></span>
                                <input type="text" name="contact_person_name" class="form-control" placeholder="Nom du responsable" value="<?php echo set_value('contact_person_name'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Compte contribuable / CNI <small class="req text-danger">*</small></label>
                            <div class="input-icon-group">
                                <span class="input-icon"><i class="fa fa-id-card-o"></i></span>
                                <input type="text" name="lastname" class="form-control" placeholder="Numéro de compte contribuable ou CNI" value="<?php echo set_value('lastname'); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="supplier-section-title">
                        <i class="fa fa-address-book"></i> Coordonnées
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Téléphone</label>
                            <div class="input-icon-group">
                                <span class="input-icon"><i class="fa fa-phone"></i></span>
                                <input type="text" name="phone" class="form-control" placeholder="Ex : 07 00 00 00 00" value="<?php echo set_value('phone'); ?>">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <div class="input-icon-group">
                                <span class="input-icon"><i class="fa fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="exemple@domaine.com" value="<?php echo set_value('email'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Adresse</label>
                            <div class="input-icon-group input-icon-group-textarea">
                                <span class="input-icon input-icon-top"><i class="fa fa-map-marker"></i></span>
                                <textarea class="form-control" name="address" rows="2" placeholder="Adresse complète"><?php echo set_value('address'); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="supplier-required-note">
                        <small class="req text-danger">*</small> <small class="text-muted">Champs obligatoires</small>
                    </div>
                </div>

                <div class="modal-footer supplier-modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* ===== En-tête de la carte ===== */
    #exphead .box-header {
        padding: 15px 20px;
    }
    #exphead .box-title {
        font-weight: 600;
    }
    .supplier-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }
    .supplier-add-btn {
        margin-left: auto;
        border-radius: 6px;
        padding: 8px 18px;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }

    /* ===== Tableau ===== */
    .table thead th {
        vertical-align: middle;
        font-weight: 600;
        color: #444;
    }
    .table td, .table th {
        vertical-align: middle;
    }
    .btn-group .btn + .btn {
        margin-left: 4px;
    }

    /* ===== Modal ===== */
    #addSupplierModal .modal-content {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }
    .supplier-modal-header {
        background: #3c8dbc;
        color: #fff;
        border-bottom: none;
        padding: 18px 25px;
    }
    .supplier-modal-header .modal-title {
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .supplier-modal-header .close {
        color: #fff;
        opacity: 0.9;
        text-shadow: none;
    }
    .supplier-modal-header .close:hover {
        opacity: 1;
    }
    .supplier-modal-body {
        padding: 25px 30px;
        background: #fafbfc;
    }
    .supplier-section-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #3c8dbc;
        margin: 5px 0 15px;
        padding-bottom: 6px;
        border-bottom: 1px solid #e3e8ee;
    }
    .supplier-section-title:not(:first-child) {
        margin-top: 20px;
    }
    #addSupplierModal .form-group {
        margin-bottom: 18px;
    }
    #addSupplierModal .form-group label {
        font-weight: 600;
        color: #333;
        font-size: 13px;
        margin-bottom: 6px;
    }
    .input-icon-group {
        position: relative;
    }
    .input-icon-group .input-icon {
        position: absolute;
        top: 0;
        left: 0;
        width: 38px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9aa5b1;
        pointer-events: none;
    }
    .input-icon-group-textarea .input-icon-top {
        align-items: flex-start;
        padding-top: 10px;
        height: auto;
    }
    .input-icon-group .form-control {
        padding-left: 38px;
        border-radius: 6px;
        border: 1px solid #dde3ea;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .input-icon-group .form-control:focus {
        border-color: #3c8dbc;
        box-shadow: 0 0 0 3px rgba(60,141,188,0.15);
    }
    .supplier-required-note {
        margin-top: 5px;
    }
    .supplier-modal-footer {
        background: #fff;
        border-top: 1px solid #eee;
        padding: 15px 25px;
    }
    .supplier-modal-footer .btn {
        border-radius: 6px;
        padding: 8px 20px;
        font-weight: 500;
    }
</style>