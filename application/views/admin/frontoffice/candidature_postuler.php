<div class="content-wrapper" style="min-height: 348px;">  
    <section class="content-header">
        <h1>
            <i class="fa fa-ioxhost"></i> <?php echo $this->lang->line('front_office'); ?> </section>
    <section class="content">       
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('file', 'can_add') || $this->rbac->hasPrivilege('file', 'can_edit')) { ?>
                <div class="col-md-4" style="margin-left: 331px">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Dépot de candidature</h3>
                        </div><!-- /.box-header -->
                        <form id="form1" action="<?php echo site_url('admin/candidatures/postuler/' . $jobs['id']) ?>"   method="post" accept-charset="utf-8" enctype="multipart/form-data" >
                            <div class="box-body">
                                <?php echo $this->session->flashdata('msg') ?>

                                <div class="form-group" hidden>
                                    <label for="pwd">Nom complet</label> <small class="req"> *</small>
                                    <input type="text" class="form-control" value="<?php echo $this->customlib->getAdminSessionUserName(); ?>" name="candidate_name">
                                    <span class="text-danger"><?php echo form_error('candidate_name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="pwd">Email</label> <small class="req"> *</small>
                                    <input type="text" class="form-control" name="candidate_email">
                                    <span class="text-danger"><?php echo form_error('candidate_email'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="pwd">Offre</label> <small class="req"> *</small>
                                    <input type="text" class="form-control" value="<?php echo set_value('title', $jobs['title']); ?>" name="job_name" readonly>
                                    <span class="text-danger"><?php echo form_error('job_name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="pwd">CV (PDF)</label> <small class="req"> *</small>

                                    <input type="file" class="filestyle form-control" name="file">
                                    <span class="text-danger"><?php echo form_error('resume'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="pwd">Lettre de motivation</label>
                                    <textarea class="form-control" name="cover_letter"></textarea>
                                      </div>




                            </div><!-- /.box-body -->


                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right">Envoyer ma candidature</button>
                                <a href="<?php echo base_url() ?>admin/candidatures/index" type="button" class="btn btn-primary btn-xs" style="width: 99px;height: 23px">
                                    <i class="fa fa-arrow-left"></i> </a>
                            </div>
                        </form>
                    </div>

                </div><!--/.col (right) -->
                <!-- left column -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('file', 'can_add') || $this->rbac->hasPrivilege('file', 'can_edit')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->

            </div><!--/.col (left) -->

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


