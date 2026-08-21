<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$language = $this->customlib->getLanguage();
$language_name = $language["short_code"];
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-ioxhost"></i> <?php echo $this->lang->line('front_office'); ?></h1>
    </section>
    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('clients', 'can_add')) {?>

                <!-- left column -->
            <?php }?>

            <div class="col-md-12<?php
if ($this->rbac->hasPrivilege('clients', 'can_add')) {
    echo "8";
} else {
    echo "12";
}
?>">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Liste des offres d'emploi</h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('visitor'); ?> <?php echo $this->lang->line('list'); ?></div>
                        <div class="mailbox-messages table-responsive">
                            <table id="jobsTable" class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Département</th>
                                        <th>Lieu</th>
                                        <th>Description</th>
                                        <th>Date limite</th>
                                        <th>Statut</th>
                                        <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (empty($joblist)) {
                                        ?>

                                                                            <?php
                                    } else {
                                        foreach ($joblist as $key => $value) {

                                            ?>
                                            <tr>
                                                <td class="mailbox-name"><?php echo $value['title']; ?></td>
                                                <td class="mailbox-name"><?php echo $value['department']; ?></td>
                                                <td class="mailbox-name"><?php echo $value['location']; ?> </td>
                                                <td class="mailbox-name"><?php echo $value['description']; ?> </td>
                                                <td class="mailbox-name"><?php echo $value['deadline']; ?> </td>
                                                <td class="mailbox-name"><?php echo $value['status']; ?> </td>


                                                <td class="mailbox-date pull-right white-space-nowrap">

                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/candidatures/postuler/<?php echo $value['id']; ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="Postuler">
                                                        <i class="fa fa-reorder"></i>
                                                    </a>
                                                    <?php if ($this->rbac->hasPrivilege('section', 'can_edit')) { ?>
                                                        <a data-placement="left" href="<?php echo base_url(); ?>admin/recrutements/edit/<?php echo $value['id']; ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } ?>

                                                    <?php if ($this->rbac->hasPrivilege('section', 'can_delete')) { ?>
                                                        <a data-placement="left" href="<?php echo base_url(); ?>admin/recrutements/delete/<?php echo $value['id']; ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm'); ?>');">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td>


                                            </tr>
                                            <?php
}
}
?>

                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) col-8 end-->
            <!-- right column -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- new END -->
<div id="visitordetails" class="modal fade" role="dialog">
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
</div><!-- /.content-wrapper -->
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.js"></script>


