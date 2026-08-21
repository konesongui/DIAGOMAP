<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> <?php echo $this->lang->line('inventory'); ?>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Bouton ouvrir le formulaire -->


        <!-- Tableau sur toute la largeur -->
        <div class="box box-primary" id="exphead" style="margin-top:15px;">
            <div class="box-header ptbnull">
                <h3 class="box-title titlefix" style="color:red">Produits vendus</h3>
            </div>
            <div class="box-body">
                <div class="mailbox-messages">
                    <div class="download_label"><?php echo $this->lang->line('item_category_list'); ?></div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover example">  <?php if ($this->rbac->hasPrivilege('item_category', 'can_add')) { ?>
                                <button type="button" class="btn btn-primary no-print" data-toggle="modal" data-target="#addCategoryModal" style="margin-left: 794px">
                                    <i class="fa fa-plus"></i> Ajouté un produit
                                </button>
                            <?php } ?>
                            <thead>
                            <tr>
                                <th><?php echo $this->lang->line('item_category'); ?></th>
                                <th>Description</th>
                                <th>Date</th>
                                <th class="text-right no-print"><?php echo $this->lang->line('action'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($categorylist)) {
                                foreach ($categorylist as $category) { ?>
                                    <tr>
                                        <td>
                                            <a href="#" data-toggle="popover" class="detail_popover">
                                                <?php echo $category['item_category']; ?>
                                            </a>
                                            <div class="fee_detail_popover" style="display:none">
                                                <?php if ($category['description'] == "") { ?>
                                                    <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                <?php } else { ?>
                                                    <p class="text text-info"><?php echo $category['description']; ?></p>
                                                <?php } ?>
                                            </div>
                                        </td>

                                        <td>
                                            <a href="#" data-toggle="popover" class="detail_popover">
                                                <?php echo $category['description']; ?>
                                            </a>
                                            <div class="fee_detail_popover" style="display:none">
                                                <?php if ($category['description'] == "") { ?>
                                                    <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                <?php } else { ?>
                                                    <p class="text text-info"><?php echo $category['description']; ?></p>
                                                <?php } ?>
                                            </div>
                                        </td>

                                        <td>
                                            <a href="#" data-toggle="popover" class="detail_popover">
                                                <?php echo date("d/m/Y", strtotime($category['created_at'])); ?>
                                            </a>
                                            <div class="fee_detail_popover" style="display:none">
                                                <?php if ($category['created_at'] == "") { ?>
                                                    <p class="text text-danger">Date</p>
                                                <?php } else { ?>
                                                    <p class="text text-info">
                                                        <?php echo date("d/m/Y", strtotime($category['created_at'])); ?>
                                                    </p>
                                                <?php } ?>
                                            </div>
                                        </td>

                                        <td class="text-right no-print">
                                            <?php if ($this->rbac->hasPrivilege('item_category', 'can_edit')) { ?>
                                                <a href="<?php echo base_url(); ?>admin/itemcategory/edit/<?php echo $category['id']; ?>" class="btn btn-default btn-xs" title="<?php echo $this->lang->line('edit'); ?>">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            <?php }
                                            if ($this->rbac->hasPrivilege('item_category', 'can_delete')) { ?>
                                                <a href="<?php echo base_url(); ?>admin/itemcategory/delete/<?php echo $category['id']; ?>" class="btn btn-default btn-xs" onclick="return confirm('<?php echo $this->lang->line('delete_confirm'); ?>');" title="<?php echo $this->lang->line('delete'); ?>">
                                                    <i class="fa fa-remove"></i>
                                                </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php }
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>

<!-- Modal Ajouter Catégorie -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?php echo site_url('admin/itemcategory/create'); ?>" method="post" accept-charset="utf-8">
                <div class="modal-header">
                    <h4 class="modal-title" id="addCategoryModalLabel">
                       Ajouté un produit
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('item_category'); ?><small class="req"> *</small></label>
                        <input type="text" name="itemcategory" class="form-control" value="<?php echo set_value('itemcategory'); ?>" required>
                        <span class="text-danger"><?php echo form_error('itemcategory'); ?></span>
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('description'); ?></label>
                        <textarea name="description" class="form-control" rows="3"><?php echo set_value('description'); ?></textarea>
                        <span class="text-danger"><?php echo form_error('description'); ?></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
