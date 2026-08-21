<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-credit-card"></i> <?php echo $this->lang->line('expenses'); ?> <small><?php echo $this->lang->line('student_fee'); ?></small></h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php
            if ($this->rbac->hasPrivilege('depense', 'can_add') || $this->rbac->hasPrivilege('depense', 'can_edit')) {
                ?>
                <div class="col-md-12">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('edit_expense'); ?></h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->

                        <form action="<?php echo site_url("admin/expense/edit_bank/" . $id) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8"  enctype="multipart/form-data">
                            <div class="box-body">

                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php echo $this->session->flashdata('msg') ?>
                                <?php } ?>
                                <?php
                                if (isset($error_message)) {
                                    echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                                }
                                ?>
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                    <input id="name" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name', $expense['name']); ?>" />
                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('expense_head'); ?></label> <small class="req">*</small>

                                    <select id="exp_head_id" name="exp_head_id" class="form-control" required>
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($expheadlist as $exphead): ?>
                                            <option
                                                    value="<?php echo $exphead['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($exphead['exp_category'], ENT_QUOTES, 'UTF-8'); ?>"
                                                <?php echo (isset($edit_data['exp_head_id']) && $edit_data['exp_head_id'] == $exphead['id']) ? 'selected="selected"' : ''; ?>>
                                                <?php echo htmlspecialchars($exphead['exp_category'], ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" name="exp_category_name" id="exp_category_name" value="<?php echo isset($edit_data['exp_category']) ? htmlspecialchars($edit_data['exp_category'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                                    <span class="text-danger"><?php echo form_error('exp_head_id'); ?></span>
                                </div>
                                <div class="form-group" hidden>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('invoice_no'); ?></label>
                                    <input id="invoice_no" name="invoice_no" placeholder="" type="text" class="form-control"  value="<?php echo set_value('invoice_no', $expense['invoice_no']); ?>" />
                                    <span class="text-danger"><?php echo form_error('invoice_no'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                    <input id="date" name="date" placeholder="" type="text" class="form-control date"  value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($expense['date']))); ?>" readonly="readonly" />
                                    <span class="text-danger"><?php echo form_error('date'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('amount'); ?></label><small class="req"> *</small>
                                    <input id="amount" name="amount" placeholder="" type="text" class="form-control"  value="<?php echo set_value('amount', $expense['amount']); ?>" />
                                    <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('attach_document'); ?></label>
                                    <input id="documents" name="documents" placeholder="" type="file" class="filestyle form-control"  value="<?php echo set_value('documents'); ?>" />
                                    <span class="text-danger"><?php echo form_error('documents'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea class="form-control" id="description" name="description" placeholder="" rows="3" placeholder="Enter ..."><?php echo set_value('description'); ?><?php echo set_value('description', $expense['note']) ?></textarea>
                                    <span class="text-danger"><?php echo form_error('description'); ?></span>
                                </div>
                            </div><!-- /.box-body -->

                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                <a href="<?php echo base_url() ?>admin/expense/bank" type="button" class="btn btn-primary btn-xs" style="width: 99px;height: 23px">
                                    <i class="fa fa-arrow-left"></i> </a>
                            </div>
                        </form>
                    </div>

                </div><!--/.col (right) -->
                <!-- left column -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('depense', 'can_add') || $this->rbac->hasPrivilege('depense', 'can_edit')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->

            </div><!--/.col (left) -->
            <!-- right column -->

        </div>
        <div class="row">
            <div class="col-md-12">
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script type="text/javascript">

</script>
<script>
    ( function ( $ ) {
        'use strict';
        $(document).ready(function () {
            initDatatable('expense-list','admin/expense/getexpenselist',[],[],100);
        });
    } ( jQuery ) )
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var expHeadSelect = document.getElementById('exp_head_id');
        var expCategoryName = document.getElementById('exp_category_name');

        // Si on est en mode édition et qu'une valeur est déjà sélectionnée
        if (expHeadSelect.value) {
            var selectedOption = expHeadSelect.options[expHeadSelect.selectedIndex];
            var categoryName = selectedOption.getAttribute('data-name');
            expCategoryName.value = categoryName;
        }

        // Écouter les changements pour mettre à jour le champ caché
        expHeadSelect.addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var categoryName = selectedOption.getAttribute('data-name');
            expCategoryName.value = categoryName;
        });
    });
</script>