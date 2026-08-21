<style>
    .label-success {
        background-color: #5cb85c;
        padding: 5px 10px;
        border-radius: 3px;
    }

    .label-warning {
        background-color: #f0ad4e;
        padding: 5px 10px;
        border-radius: 3px;
    }

    .btn-valider {
        margin-top: 5px;
        font-size: 11px;
        padding: 2px 8px;
    }
</style>
<div class="content-wrapper" style="min-height: 946px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> <?php echo $this->lang->line('inventory'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">

                <!-- general form elements -->
                <div class="box box-primary" id="exphead">
                    <div class="box-header ptbnull">

                        <h3 class="box-title titlefix">Devis fournisseurs</h3>
                        <?php if ($this->rbac->hasPrivilege('quote_supplier', 'can_add')) {
                            ?>
                            <a href="<?php echo site_url('admin/devissupplier/create') ?>" type="button" class="btn btn-primary btn-sm" style="margin-left: 626px"><i class="fa fa-plus"></i> Ajouter un devis fournisseur</a>
                        <?php }
                        ?>
                    </div><!-- /.box-header -->
                    <div class="box-body  ">
                        <div class="mailbox-messages table-responsive">
                            <div class="download_label">Liste des devis fournisseurs</div>

                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Fournisseur</th>
                                    <th>Description</th>
                                    <th>Montant Total</th>

                                    <th><?php echo $this->lang->line('date'); ?></th>
                                    <th>Fichier</th>
                                    <th>Statut Dévis</th>
                                    <th class="text-right no-print"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($devissupplierlist)) { ?>
                                    <!-- Aucun résultat -->
                                <?php } else {
                                    $count = 1;
                                    foreach ($devissupplierlist as $devissupplier) { ?>
                                        <tr>
                                            <td class="mailbox-name">
                                                <a href="#" data-toggle="popover" class="detail_popover">
                                                    <?php echo $devissupplier['ref'] ?>
                                                </a>
                                            </td>
                                            <td class="mailbox-name">
                                                <a href="#" data-toggle="popover" class="detail_popover">
                                                    <?php echo $devissupplier['name'] ?>
                                                </a>
                                            </td>
                                            <td class="mailbox-name">
                                                <a href="#" data-toggle="popover" class="detail_popover">
                                                    <?php echo $devissupplier['article'] ?>
                                                </a>
                                            </td>
                                            <td class="mailbox-name">
                                                <a href="#" data-toggle="popover" class="detail_popover">
                                                    <?php echo $devissupplier['montant'] ?>
                                                </a>
                                            </td>

                                            <td class="mailbox-name">
                                                <?php if ($devissupplier['date'] != "") { ?>
                                                    <i class=""></i> <?php echo $devissupplier['date'] ?>
                                                <?php } ?>
                                            </td>
                                            <td class="mailbox-name">
                                                <?php if ($devissupplier['image'] !== "") { ?>
                                                    <a data-placement="left" onclick="getRecord(<?php echo $devissupplier['id']; ?>)" class="btn btn-default btn-xs" data-target="#pdfdetails" data-toggle="modal" title="<?php echo $this->lang->line('view'); ?>">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/devissupplier/download/<?php echo $devissupplier['image']; ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('download'); ?>">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                            <td class="mailbox-name">
                                                <?php if ($devissupplier['payment_status'] == 'validé'): ?>
                                                    <span class="label label-success">
            <i class="fa fa-check"></i> Validé
        </span>
                                                <?php else: ?>
                                                    <span class="label label-warning">
            <i class="fa fa-clock-o"></i> Non validé
        </span>
                                                    <?php if ($this->rbac->hasPrivilege('quote_supplier', 'can_edit')) { ?>
                                                        <br>
                                                        <a href="<?php echo base_url(); ?>admin/devissupplier/markAsValidated/<?php echo $devissupplier['id'] ?>"
                                                           class="btn btn-success btn-xs btn-valider"
                                                           data-toggle="tooltip"
                                                           title="Marquer comme validé"
                                                           onclick="return confirm('Êtes-vous sûr de vouloir valider ce devis ?');">
                                                            <i class="fa fa-check"></i> Valider
                                                        </a>
                                                    <?php } ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="mailbox-date pull-right no-print">
                                                <?php if ($this->rbac->hasPrivilege('quote_supplier', 'can_edit')) { ?>
                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/devissupplier/edit/<?php echo $devissupplier['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } ?>
                                                <?php if ($this->rbac->hasPrivilege('quote_supplier', 'can_delete')) { ?>
                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/devissupplier/delete/<?php echo $devissupplier['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $count++;
                                    }
                                } ?>
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
<div id="pdfdetails" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('details'); ?></h4>
            </div>
            <div class="modal-body" id="getdetails">

            </div>
        </div>
    </div>
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

<script type="text/javascript">

    $(function () {

        $(".timepicker").timepicker({

        });
    });

    function getRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/devissupplier/details/' + id,
            success: function (result) {

                $('#getdetails').html(result);
            }

        });
    }

</script>
