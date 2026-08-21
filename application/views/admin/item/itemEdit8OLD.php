<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-object-group"></i> <?php echo $this->lang->line('inventory'); ?></h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php // Edit Form Column - Only show if user has privileges ?>
            <?php if ($this->rbac->hasPrivilege('item', 'can_add') || $this->rbac->hasPrivilege('item', 'can_edit')): ?>
                <div class="col-md-4">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('edit_item'); ?></h3>
                        </div>
                        
                        <form id="form1" action="<?php echo site_url('admin/item/edit/' . $id) ?>" method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php // Display flash messages if they exist ?>
                                <?php if ($this->session->flashdata('msg')): ?>
                                    <?php echo $this->session->flashdata('msg') ?>
                                <?php endif; ?>
                                
                                <?php if (isset($error_message)): ?>
                                    <div class='alert alert-danger'><?php echo $error_message ?></div>
                                <?php endif; ?>
                                
                                <?php echo $this->customlib->getCSRF(); ?>
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                
                                <!-- Name Field -->
                                <div class="form-group">
                                    <label for="name"><?php echo $this->lang->line('name'); ?><small class="req"> *</small></label>
                                    <input autofocus id="name" name="name" type="text" class="form-control" value="<?php echo set_value('name', $item['name']); ?>">
                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                </div>
                                
                                <!-- Category Field -->
                                <div class="form-group">
                                    <label for="item_category_id"><?php echo $this->lang->line('item_category'); ?><small class="req"> *</small></label>
                                    <select id="item_category_id" name="item_category_id" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($itemcatlist as $item_category): ?>
                                            <option value="<?php echo $item_category['id'] ?>" <?php echo set_value('item_category_id', $item['item_category_id']) == $item_category['id'] ? 'selected="selected"' : '' ?>>
                                                <?php echo $item_category['item_category'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('item_category_id'); ?></span>
                                </div>

                                <!-- Name Field -->
                                <div class="form-group">
                                    <label for="unit">Unité<small class="req"> *</small></label>
                                    <input autofocus id="unit" name="unit" type="text" class="form-control" value="Unité">
                                    <span class="text-danger"><?php echo form_error('unit'); ?></span>
                                </div>
                                
                                <!-- Description Field -->
                                <div class="form-group">
                                    <label for="description"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo set_value('description', $item['description']); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                <a href="<?php echo base_url() ?>admin/item" type="button" class="btn btn-primary btn-xs" style="width: 99px;height: 23px">
                                    <i class="fa fa-arrow-left"></i> </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Items List Column -->
            <?php 
            $col_class = ($this->rbac->hasPrivilege('item', 'can_add') || $this->rbac->hasPrivilege('item', 'can_edit')) ? '8' : '12';
            ?>
            <div class="col-md-<?php echo $col_class; ?>">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('item_list'); ?></h3>
                    </div>
                    
                    <div class="box-body">
                        <div class="mailbox-messages table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('item_list'); ?></div>
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th><?php echo $this->lang->line('category'); ?> de produit</th>
                                        <th><?php echo $this->lang->line('unit'); ?></th>
                                        <th>Description</th>
                                        <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($itemlist)): ?>
                                        <?php foreach ($itemlist as $items): ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a href="#" data-toggle="popover" class="detail_popover"><?php echo $items['name'] ?></a>

                                                    <div class="fee_detail_popover" style="display: none">
                                                        <?php
                                                        if ($items['description'] == "") {
                                                            ?>
                                                            <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <p class="text text-info"><?php echo $items['description']; ?></p>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </td>


                                                <td class="mailbox-name">
                                                    <?php echo $items['item_category']; ?>

                                                </td>
                                                <td class="mailbox-name">
                                                    <?php echo $items['unit']; ?>

                                                </td>
                                                <td class="mailbox-name">
                                                    <?php echo $items['description'] ; ?>
                                                </td>
                                                
                                                <td class="mailbox-date pull-right">
                                                    <?php if ($this->rbac->hasPrivilege('item', 'can_edit')) { ?> 
                                                        <a data-placement="left" href="<?php echo base_url(); ?>admin/item/edit/<?php echo $items['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php }if ($this->rbac->hasPrivilege('item', 'can_delete')) { ?>  
                                                        <a data-placement="left" href="<?php echo base_url(); ?>admin/item/delete/<?php echo $items['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                    <?php } ?>

                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No items found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function () {
    // Initialize popovers
    $('.detail_popover').popover({
        placement: 'right',
        trigger: 'hover',
        container: 'body',
        html: true,
        content: function () {
            return $(this).closest('td').find('.fee_detail_popover').html();
        }
    });
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>

<style>
.no-photo {
    width: 50px;
    height: 50px;
    background: #f4f4f4;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
}
</style>