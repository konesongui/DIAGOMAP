<div class="content-wrapper" style="min-height: 348px;">  
    <section class="content-header">
        <h1>
            <i class="fa fa-ioxhost"></i> <?php echo $this->lang->line('front_office'); ?> </section>
    <section class="content">       
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('file', 'can_add') || $this->rbac->hasPrivilege('file', 'can_edit')) { ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Editer un candidat</h3>
                        </div><!-- /.box-header -->
                        <form id="form1" action="<?php echo site_url('admin/recrutements/edit/' . $jobs['id']) ?>"   method="post" accept-charset="utf-8" enctype="multipart/form-data" >
                            <div class="box-body">
                                <?php echo $this->session->flashdata('msg') ?>

                                <div class="form-group">
                                    <label for="pwd">Titre du poste</label> <small class="req"> *</small>
                                    <input type="text" class="form-control" value="<?php echo set_value('title', $jobs['title']); ?>" name="title">
                                    <span class="text-danger"><?php echo form_error('title'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="pwd">Département</label> <small class="req"> *</small>
                                    <input type="text" class="form-control" value="<?php echo set_value('department', $jobs['department']); ?>" name="department">
                                    <span class="text-danger"><?php echo form_error('department'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="pwd">Lieu</label>
                                    <input type="text" class="form-control" value="<?php echo set_value('location', $jobs['location']); ?>" name="location">
                                </div>
                                <div class="form-group">
                                    <label for="pwd">Description du poste</label></label>
                                    <input type="text" class="form-control" value="<?php echo set_value('description', $jobs['description']); ?>" name="description">
                                </div>

                                <div class="form-group" hidden>
                                    <label for="email">Date limite de candidature</label>
                                    <input type="text" class="form-control" value="<?php echo set_value('deadline', $jobs['deadline']); ?>" name="deadline">
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Statut</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="open" <?= $jobs['status'] === 'open' ? 'selected' : '' ?>>Ouvert</option>
                                        <option value="closed" <?= $jobs['status'] === 'closed' ? 'selected' : '' ?>>Fermé</option>
                                    </select>
                                </div>

                            </div><!-- /.box-body -->


                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                <a href="<?php echo base_url() ?>admin/recrutements/index" type="button" class="btn btn-primary btn-xs" style="width: 99px;height: 23px">
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
                <div class="box box-primary"> 
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Liste des candidats</h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label">Liste des candidats</div>
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


                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (empty($jobslist)) {
                                    ?>

                                    <?php
                                } else {
                                    foreach ($jobslist as $key => $value) {

                                        ?>
                                        <tr>
                                            <td class="mailbox-name"><?php echo $value['title']; ?></td>
                                            <td class="mailbox-name"><?php echo $value['department']; ?></td>
                                            <td class="mailbox-name"><?php echo $value['location']; ?> </td>
                                            <td class="mailbox-name"><?php echo $value['description']; ?> </td>
                                            <td class="mailbox-name"><?php echo $value['deadline']; ?> </td>
                                            <td class="mailbox-name"><?php echo $value['status']; ?> </td>



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


