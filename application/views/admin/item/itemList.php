
<div class="content-wrapper" style="min-height: 946px;">
    <!-- Content Header (Page header) -->



    <section class="content">


        <!-- Tableau plein écran -->
        <div class="row">

            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-body table-responsive">
                        <table class="table table-hover table-striped table-bordered example">
                            <!-- Bouton Ajouter Produit -->
                            <?php if ($this->rbac->hasPrivilege('item', 'can_add')) { ?>
                                <div class="row mb-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Liste des produits</h3>
                                        </div>
                                        <div class="card-body table-responsive">
                                            <!-- ici ton tableau -->
                                        </div>
                                    </div>

                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-primary" data-toggle="modal" data-target="#addItemModal">
                                            <i class="fa fa-plus"></i> Ajouter un produit
                                        </button>
                                    </div>
                                </div>
                            <?php } ?>
                            <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Catégorie</th>
                                <th>Unité</th>
                                <th>Description</th>
                                <th class="text-right">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($itemlist)) {
                                foreach ($itemlist as $items) { ?>
                                    <tr>
                                        <td>
                                            <a href="#" data-toggle="popover" class="detail_popover"><?php echo $items['name'] ?></a>
                                            <div class="fee_detail_popover" style="display:none;">
                                                <?php if ($items['description'] == "") { ?>
                                                    <p class="text text-danger">Pas de description</p>
                                                <?php } else { ?>
                                                    <p class="text text-info"><?php echo $items['description']; ?></p>
                                                <?php } ?>
                                            </div>
                                        </td>
                                        <td><?php echo $items['item_category']; ?></td>
                                        <td><?php echo $items['unit']; ?></td>
                                        <td><?php echo $items['description']; ?></td>
                                        <td class="text-right">
                                            <?php if ($this->rbac->hasPrivilege('item', 'can_edit')) { ?>
                                                <a href="<?php echo base_url(); ?>admin/item/edit/<?php echo $items['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="Modifier">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            <?php } if ($this->rbac->hasPrivilege('item', 'can_delete')) { ?>
                                                <a href="<?php echo base_url(); ?>admin/item/delete/<?php echo $items['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="Supprimer" onclick="return confirm('Confirmer la suppression ?');">
                                                    <i class="fa fa-remove"></i>
                                                </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Ajouter Produit -->
        <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document"><!-- modal-lg pour plus de largeur -->
                <div class="modal-content">
                    <form id="form1" action="<?php echo base_url() ?>admin/item" method="post" accept-charset="utf-8">

                        <div class="modal-header">
                            <h5 class="modal-title" id="addItemModalLabel">Ajout d'un article</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <?php echo $this->customlib->getCSRF(); ?>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Produit <small class="req">*</small></label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Catégorie <small class="req">*</small></label>
                                    <select name="item_category_id" class="form-control" required>
                                        <option value="">Sélectionner</option>
                                        <?php foreach ($itemcatlist as $cat) { ?>
                                            <option value="<?= $cat['id'] ?>"><?= $cat['item_category'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Unité</label>
                                    <input type="text" class="form-control" name="unit">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </section>
</div>


