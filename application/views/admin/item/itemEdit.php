<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-object-group"></i> <?php echo $this->lang->line('inventory'); ?></h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('item', 'can_add') || $this->rbac->hasPrivilege('item', 'can_edit')): ?>
                <div class="col-md-12"><!-- largeur max -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('edit_item'); ?></h3>
                        </div>

                        <form id="form1" action="<?php echo site_url('admin/item/edit/' . $id) ?>" method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php if ($this->session->flashdata('msg')): ?>
                                    <?php echo $this->session->flashdata('msg') ?>
                                <?php endif; ?>

                                <?php if (isset($error_message)): ?>
                                    <div class='alert alert-danger'><?php echo $error_message ?></div>
                                <?php endif; ?>

                                <?php echo $this->customlib->getCSRF(); ?>
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">

                                <!-- Champs en ligne (4 par ligne) -->
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="name"><?php echo $this->lang->line('name'); ?><small class="req"> *</small></label>
                                        <input autofocus id="name" name="name" type="text" class="form-control" value="<?php echo set_value('name', $item['name']); ?>">
                                        <span class="text-danger"><?php echo form_error('name'); ?></span>
                                    </div>

                                    <div class="form-group col-md-4">
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

                                    <div class="form-group col-md-4">
                                        <label for="unit">Unité<small class="req"> *</small></label>
                                        <input id="unit" name="unit" type="text" class="form-control" value="<?php echo set_value('unit', $item['unit']); ?>">
                                        <span class="text-danger"><?php echo form_error('unit'); ?></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="description"><?php echo $this->lang->line('description'); ?></label>
                                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo set_value('description', $item['description']); ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                <a href="<?php echo base_url() ?>admin/item" type="button" class="btn btn-primary btn-xs" style="width: 99px;height: 23px">
                                    <i class="fa fa-arrow-left"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
