
<div class="content-wrapper" style="min-height: 946px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i>Commerciaux</h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('clients', 'can_add')) { ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Ajouté un objectif</h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <form  action="<?php echo site_url('admin/objectifs/create') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php echo $this->session->flashdata('msg') ?>
                                <?php } ?>
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Commercial</label><small class="req"> *</small>
                                    <select name="user_name" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($stff_list as $key => $stff_list_value) { ?>
                                            <option value="<?php echo $stff_list_value['name'].' '.$stff_list_value['surname']; ?>" ><?php echo $stff_list_value['name'].' '.$stff_list_value['surname']; ?></option>
                                        <?php }
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('user_name'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"> Objectfis Annuel</label><small class="req"> *</small>
                                    <input id="target_amount" name="target_amount" placeholder="" type="number" class="form-control" value="" />
                                    <span class="text-danger"><?php echo form_error('target_amount'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"> Date</label><small class="req"> *</small>
                                    <input id="date" name="date" placeholder="" type="date" class="form-control" value="" />
                                    <span class="text-danger"><?php echo form_error('date'); ?></span>
                                </div>


                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </form>
                    </div>
                </div><!--/.col (right) -->
                <!-- left column -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('clients', 'can_add')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->
                <div class="box box-primary" id="exphead">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Objectif commercial</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body  ">
                        <div class="mailbox-messages table-responsive">
                            <div class="download_label">Objectif commercial</div>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Objectifs</th>
                                    <th>Date</th>
                                    <th class="text-right no-print"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($objectifslist)) {
                                    ?>

                                    <?php
                                } else {
                                    $count = 1;
                                    foreach ($objectifslist as $objectif) {
                                        ?>
                                        <tr>


                                            <td class="mailbox-name"><?php echo $objectif['user_name'] ?></td>
                                            <td class="mailbox-name"><?php echo $objectif['target_amount'] ?></td>
                                            <td class="mailbox-name"><?php echo $objectif['date'] ?></td>


                                            <td class="mailbox-date pull-right no-print">
                                                <?php if ($this->rbac->hasPrivilege('clients', 'can_edit')) { ?>
                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/objectifs/edit/<?php echo $objectif['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } if ($this->rbac->hasPrivilege('clients', 'can_delete')) { ?>
                                                    <a href="<?= site_url('admin/objectifs/delete/' . $objectif['id']); ?>"
                                                       class="btn btn-default btn-xs"
                                                       data-toggle="tooltip"
                                                       title="Supprimer"
                                                       onclick="return confirm('Voulez-vous vraiment supprimer cet objectif ?');">
                                                        <i class="fa fa-trash text-danger"></i>
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




