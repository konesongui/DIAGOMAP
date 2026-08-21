<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content-header">
        <h1>
            <i class="fa fa-credit-card"></i>
            <?php echo $this->lang->line('expenses'); ?>
            <small><?php echo $this->lang->line('student_fee'); ?></small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Liste des dépenses</h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('depense', 'can_add')) { ?>
                                <!-- Bouton pour ouvrir le popup -->
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#expenseModal">
                                    <i class="fa fa-plus"></i> Ajouter une dépense
                                </button>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if ($this->session->flashdata('msg')) { ?>
                            <?php echo $this->session->flashdata('msg') ?>
                        <?php } ?>
                        <?php if (isset($error_message)) { echo "<div class='alert alert-danger'>" . $error_message . "</div>"; } ?>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover expense-list" data-export-title="<?php echo $this->lang->line('expense_list'); ?>">
                                <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('date'); ?></th>
                                    <th><?php echo $this->lang->line('amount'); ?></th>
                                    <th><?php echo $this->lang->line('category'); ?></th>
                                    <th><?php echo $this->lang->line('description'); ?></th>

                                   <!-- <th>Caisse impactée</th>
                                    <th>Utilisateur</th>-->
                                    <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div><!-- /.box-body -->
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Popup -->
<div class="modal fade" id="expenseModal" tabindex="-1" role="dialog" aria-labelledby="expenseModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="form1" action="<?php echo base_url() ?>admin/expense" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4 class="modal-title" id="expenseModalLabel"><?php echo $this->lang->line('add_expense'); ?></h4>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <?php if ($this->session->flashdata('msg')) { echo $this->session->flashdata('msg'); } ?>
                    <?php if (isset($error_message)) { echo "<div class='alert alert-danger'>" . $error_message . "</div>"; } ?>
                    <?php echo $this->customlib->getCSRF(); ?>



                    <div class="form-group">
                        <label>Caisses *</label>
                        <select id="inc_head_id" name="inc_head_id" class="form-control" required>
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($incomelist as $exphead) { ?>
                                <option value="<?php echo $exphead['id'] ?>"><?php echo $exphead['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group" hidden>
                        <label>Utilisateur</label>
                        <input id="user" name="user" type="text" class="form-control" value="<?php echo $this->customlib->getAdminSessionUserName(); ?>" readonly />
                    </div>

                    <div class="form-group">
                        <label><?php echo $this->lang->line('name'); ?> *</label>
                        <input id="name" name="name" type="text" class="form-control" required />
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo $this->lang->line('expense_head'); ?></label> <small class="req">*</small>


                        <select id="exp_head_id" name="exp_head_id" class="form-control" required>
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($expheadlist as $exphead): ?>
                                <option
                                        value="<?php echo $exphead['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($exphead['exp_category'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($exphead['exp_category'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="exp_category_name" id="exp_category_name">
                        <span class="text-danger"><?php echo form_error('exp_head_id'); ?></span>
                    </div>

                    <div class="form-group">
                        <label><?php echo $this->lang->line('date'); ?> *</label>
                        <input id="date" name="date" type="text" class="form-control date" value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>" readonly required />
                    </div>

                    <div class="form-group">
                        <label><?php echo $this->lang->line('amount'); ?> *</label>
                        <input id="amount" name="amount" type="number" class="form-control" required />
                    </div>

                    <div class="form-group" hidden>
                        <label><?php echo $this->lang->line('attach_document'); ?></label>
                        <input id="documents" name="documents" type="file" class="filestyle form-control" />
                    </div>

                    <div class="form-group">
                        <label><?php echo $this->lang->line('description'); ?></label>
                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info"><?php echo $this->lang->line('save'); ?></button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function ($) {
        'use strict';
        $(document).ready(function () {
            initDatatable('expense-list','admin/expense/getexpenselist',[],[],100);
        });
    })(jQuery);
</script>
<script>
    document.getElementById('exp_head_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var categoryName = selectedOption.getAttribute('data-name');
        document.getElementById('exp_category_name').value = categoryName || '';
    });
</script>
