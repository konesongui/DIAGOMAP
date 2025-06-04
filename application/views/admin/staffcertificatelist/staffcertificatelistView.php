<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content" >
        <div class="row">
            <?php
            if ($this->rbac->hasPrivilege('staff_id_card', 'can_add')) {
                ?>
               <!--/.col (right) -->
                <!-- left column -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('staff_id_card', 'can_add')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->
                <div class="box box-primary" id="hroom" style="width: 1060px">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('staff'); ?> <?php echo $this->lang->line('icard'); ?> <?php echo $this->lang->line('list'); ?></h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="mailbox-messages">
                            <div class="download_label"><?php echo $this->lang->line('staff'); ?> <?php echo $this->lang->line('icard'); ?> <?php echo $this->lang->line('list'); ?></div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th>Utilisateurs</th>
                                        <th><?php echo $this->lang->line('icard'); ?> <?php echo $this->lang->line('title'); ?></th>
                                        <th>Informations</th>
                                        <th>Code d'identification</th>
                                        <th>Date</th>
                                        <!--<th><?php echo $this->lang->line('background_image'); ?></th>
                                         <th class="text text-center"><?php echo $this->lang->line('design').' '.$this->lang->line('type'); ?></th>-->
                                        <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($staffidcardlist)) {
                                        ?>
                                        <?php
                                    } else {
                                        $count = 1;
                                        foreach ($staffidcardlist as $staffidcard_value) {
                                            ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a style="cursor: pointer;" id="<?php echo $staffidcard_value->id ?>" data-toggle="popover" class="detail_popover" ><?php echo $staffidcard_value->username; ?></a>
                                                </td>
                                                <td class="mailbox-name">
                                                    <a style="cursor: pointer;" id="<?php echo $staffidcard_value->id ?>" data-toggle="popover" class="detail_popover" ><?php echo $staffidcard_value->school_name; ?></a>
                                                </td>
                                                <td class="mailbox-name">
                                                    <a style="cursor: pointer;" id="<?php echo $staffidcard_value->id ?>" data-toggle="popover" class="detail_popover" ><?php echo $staffidcard_value->school_address; ?></a>
                                                </td>
                                                <td class="mailbox-name">
                                                    <a style="cursor: pointer;" id="<?php echo $staffidcard_value->id ?>" data-toggle="popover" class="detail_popover" ><?php echo $staffidcard_value->generated_code; ?></a>
                                                </td>
                                                <td class="mailbox-name">
                                                    <a style="cursor: pointer;" id="<?php echo $staffidcard_value->id ?>" data-toggle="popover" class="detail_popover" ><?php echo $staffidcard_value->created_at; ?></a>
                                                </td>
                                               <!-- <td class="mailbox-name">
                                                    <?php if ($staffidcard_value->background != '' && !is_null($staffidcard_value->background)) { ?>
                                                        <img src="<?php echo base_url('uploads/staff_id_card/background/') ?><?php echo $staffidcard_value->background ?>" width="40">
                                                    <?php } else { ?>
                                                        <i class="fa fa-picture-o fa-3x" aria-hidden="true"></i>
                                                    <?php } ?>
                                                </td>
                                                    <td class="mailbox-name text text-center">
                                                    <?php echo ($staffidcard_value->enable_vertical_card) ? $this->lang->line('vertical') : $this->lang->line('horizontal') ?>
                                                </td>-->
                                                <td class="mailbox-date pull-right no-print white-space-nowrap">
                                                    <!--<a data-id="<?php echo $staffidcard_value->id ?>" class="btn btn-default btn-xs view_data"  data-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>">
                                                        <i class="fa fa-reorder"></i>
                                                    </a>-->
                                                    <?php
                                                    if ($this->rbac->hasPrivilege('staff_id_card', 'can_edit')) {
                                                        ?>
                                                        <a href="<?php echo base_url(); ?>admin/staffcertificate/edit/<?php echo $staffidcard_value->id ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <?php
                                                    }
                                                    if ($this->rbac->hasPrivilege('staff_id_card', 'can_delete')) {
                                                        ?>
                                                        <a href="<?php echo base_url(); ?>admin/staffcertificate/delete/<?php echo $staffidcard_value->id ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
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
                          </div>
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->
        </div>
        <div class="row">
            <div class="col-md-12">
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<!-- Modal -->
<div class="modal fade" id="certificateModal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('view'); ?> <?php echo $this->lang->line('icard'); ?></h4>
            </div>
            <div class="modal-body" id="certificate_detail">
            <div class="modal-inner-loader"></div>
            <div class="modal-inner-content">

            </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
          $("#header_color").colorpicker();
        $(document).on('click','.view_data',function(){

           $('#certificateModal').modal("show");
          var certificateid = $(this).data('id');
           $.ajax({
                url: "<?php echo base_url('admin/staffcertificate/view') ?>",
                method: "post",
                data: {certificateid: certificateid},
                 beforeSend: function() {

                  },
                success: function (data) {
                 $('#certificateModal .modal-inner-content').html(data);
                 $('#certificateModal .modal-inner-loader').addClass('displaynone');

                 },
                error: function(xhr) { // if error occured
                 alert("Error occured.please try again");
                },
                complete: function() {

                }
            });
        });

    });

    $('#certificateModal').on('hidden.bs.modal', function (e) {
        $('#certificateModal .modal-inner-content').html("");
        $('#certificateModal .modal-inner-loader').removeClass('displaynone');
     });
</script>