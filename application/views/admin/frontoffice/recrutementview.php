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
                <div class="col-md-12">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Créer une nouvelle offre d'emploi</h3>
                        </div><!-- /.box-header -->
                        <form id="employeeform" action="<?php echo base_url() ?>admin/recrutements/create" method="post" accept-charset="utf-8" enctype="multipart/form-data">

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

                                <div class="form-group col-md-4">
                                    <label for="title" class="form-label">Titre du poste</label>
                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo set_value('title'); ?>" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="department" class="form-label">Département</label>
                                    <input type="text" class="form-control" id="department" name="department" value="<?php echo set_value('department'); ?>" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="location" class="form-label">Lieu</label>
                                    <input type="text" class="form-control" id="location" name="location" value="<?php echo set_value('location'); ?>" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="description" class="form-label">Description du poste</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" required><?php echo set_value('description'); ?></textarea>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="deadline" class="form-label">Date limite de candidature</label>
                                    <input type="date" class="form-control" id="deadline" name="deadline" value="<?php echo set_value('deadline'); ?>" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="status" class="form-label">Statut</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="open" <?php echo set_select('status', 'open', TRUE); ?>>Ouvert</option>
                                        <option value="closed" <?php echo set_select('status', 'closed'); ?>>Fermé</option>
                                    </select>
                                </div>

                            </div><!-- /.box-body -->

                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                <button type="reset" class="btn btn-secondary bg-red">Annuler</button>
                            </div>
                        </form>

                    </div>

                </div><!--/.col (right) -->
                <!-- left column -->
            <?php }?>

            <div class="col-md-<?php
if ($this->rbac->hasPrivilege('clients', 'can_add')) {
    echo "8";
} else {
    echo "12";
}
?>">
                <!-- general form elements -->
                <div class="box box-primary" hidden>
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Liste des Offres d'emploi</h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('visitor'); ?> <?php echo $this->lang->line('list'); ?></div>
                        <div class="mailbox-messages table-responsive">
                            <table class="table table-hover table-striped table-bordered example">
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
                                                    <?php if ($this->rbac->hasPrivilege('section', 'can_edit')) { ?>
                                                        <a data-placement="left" href="<?php echo base_url(); ?>admin/candidatures/edit/<?php echo $value['id']; ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } ?>

                                                    <?php if ($this->rbac->hasPrivilege('section', 'can_delete')) { ?>
                                                        <a data-placement="left" href="<?php echo base_url(); ?>admin/candidatures/delete/<?php echo $value['id']; ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm'); ?>');">
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


