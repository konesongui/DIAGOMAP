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
                        <h3 class="box-title titlefix">Liste des candidats</h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label">Liste des candidats</div>
                        <div class="mailbox-messages table-responsive">
                            <table id="jobsTable" class="table table-hover table-striped table-bordered example">
                                <thead>
                                    <tr>
                                        <th>Offre</th>
                                        <th>Candidats</th>
                                        <th>Email</th>
                                        <th>Cv</th>
                                        <th>Lettre de motivation</th>
                                        <th>Date d'application</th>
                                       <!-- <th class="text-right"><?php echo $this->lang->line('action'); ?></th>-->
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($candidatslist)) : ?>
                                    <?php foreach ($candidatslist as $value) : ?>
                                        <tr>
                                            <td class="mailbox-name"><?= htmlspecialchars($value['job_name']); ?></td>
                                            <td class="mailbox-name"><?= htmlspecialchars($value['candidate_name']); ?></td>
                                            <td class="mailbox-name"><?= htmlspecialchars($value['candidate_email']); ?></td>

                                            <td class="mailbox-name">
                                                <?php if (!empty($value['image'])) : ?>
                                                    <a data-placement="left"
                                                       href="<?= base_url('admin/candidatures/download/' . urlencode($value['image'])); ?>"
                                                       class="btn btn-default btn-xs"
                                                       data-toggle="tooltip"
                                                       title="<?= $this->lang->line('download'); ?>">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                <?php else : ?>
                                                    <span class="badge badge-secondary">Aucun CV</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="mailbox-name"><?= nl2br(htmlspecialchars($value['cover_letter'])); ?></td>
                                            <td class="mailbox-name"><?= htmlspecialchars($value['applied_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Aucun candidat trouvé</td>
                                    </tr>
                                <?php endif; ?>
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


