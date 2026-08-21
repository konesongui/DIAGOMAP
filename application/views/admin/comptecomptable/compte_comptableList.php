
<div class="content-wrapper" style="min-height: 946px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i>comptes comptables</h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('comptable', 'can_add')) { ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Créer un comptes comptables</h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <form  action="<?php echo site_url('admin/comptecomptable/create') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php echo $this->session->flashdata('msg') ?>
                                <?php } ?>
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="form-group">
                                    <label for="numero">Numero</label><small class="req"> *</small>
                                    <input autofocus="" id="numero" name="numero" placeholder="" type="text" class="form-control"  value="<?php echo set_value('numero'); ?>" />
                                    <span class="text-danger"><?php echo form_error('numero'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="intitule">Intitulé</label><small class="req"> *</small>
                                    <input autofocus="" id="intitule" name="intitule" placeholder="" type="text" class="form-control"  value="<?php echo set_value('intitule'); ?>" />
                                    <span class="text-danger"><?php echo form_error('intitule'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="classe">Type de Journal</label><small class="req"> *</small>
                                    <select name="classe" id="classe" required class="form-control">
                                        <?php for ($i = 1; $i <= 7; $i++): ?>
                                            <option value="<?= $i ?>">Classe <?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('classe'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="type_compte">Type de compte *</label>
                                    <select name="type_compte" id="type_compte" required class="form-group">
                                        <option value="actif">Actif</option>
                                        <option value="passif">Passif</option>
                                        <option value="charge">Charge</option>
                                        <option value="produit">Produit</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="est_actif">Actif ?</label>
                                    <input type="checkbox" name="est_actif" id="est_actif" value="1" checked class="form-group">
                                </div>

                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                <button type="reset"  class="btn btn-secondary bg-black"><i class="fa fa-refresh" aria-hidden="true"></i></button>
                            </div>
                        </form>
                    </div>
                </div><!--/.col (right) -->
                <!-- left column -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('comptable', 'can_add')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->
                <div class="box box-primary" id="exphead">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">comptes comptables</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body  ">
                        <div class="mailbox-messages table-responsive">
                            <div class="download_label">comptes comptables</div>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                <tr>
                                    <th>Numéro du compte</th>
                                    <th>Intitulé</th>
                                    <th>Classe</th>
                                    <th>Type de compte</th>
                                    <th>Actif</th>


                                    <th class="text-right no-print"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($compte_comptablelist)) {
                                    ?>

                                    <?php
                                } else {
                                    $count = 1;
                                    foreach ($compte_comptablelist as $comptable) {
                                        ?>
                                        <tr>

                                            <td class="mailbox-name"><?php echo $comptable['numero'] ?></td>

                                            <td class="mailbox-name"><?php echo $comptable['intitule'] ?></td>
                                            <td class="mailbox-name"><?php echo $comptable['classe'] ?></td>
                                            <td class="mailbox-name"><?php echo $comptable['type_compte'] ?></td>
                                            <td class="mailbox-name"><?php echo $comptable['est_actif'] ?></td>



                                            <td class="mailbox-date pull-right no-print">
                                                <?php if ($this->rbac->hasPrivilege('comptable', 'can_edit')) { ?>
                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/comptecomptable/edit/<?php echo $comptable['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } if ($this->rbac->hasPrivilege('comptable', 'can_delete')) { ?>
                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/comptecomptable/delete/<?php echo $comptable['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $count++;
                                }
                                ?>

                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
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

<script type="text/javascript">
    var base_url = '<?php echo base_url() ?>';


</script>
