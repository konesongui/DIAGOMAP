
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-sitemap"></i> <?php echo $this->lang->line('human_resource'); ?></h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <div class="col-md-12">
                        <?php echo $this->session->flashdata('msg') ?>
                    </div>
                    <form role="form" action="<?php echo site_url('admin/training_request') ?>" method="post" class="">
                        <div class="box-body row">
                            <?php echo $this->customlib->getCSRF(); ?>                     

                            <div class="col-sm-3 col-md-3" hidden>
                                <div class="form-group">  
                                    <label><?php echo $this->lang->line('source'); ?></label>

                                    <!--<input type="text" autocomplete="off" name="source" class="form-control"  value="<?php  echo set_value('source') ?>">-->
                                  <select  id="source" name="source" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($sourcelist as $key => $value) { ?>
                                            <option <?php
                                      if ($value["source"] == $source_select) {
                                          echo "selected";
                                      }
                                      ?> value="<?php echo $value["source"] ?>"><?php echo $value["source"] ?></option>
                                            <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('source'); ?></span>
                                </div>  
                            </div>
                             <div class="col-sm-3 col-md-3" hidden>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('enquiry')." ".$this->lang->line('from'); ?> <?php echo $this->lang->line('date'); ?></label>

                                        <input type="text" autocomplete="off" name="from_date" class="form-control  date"  value="<?php  echo set_value('from_date') ?>">
                                    </div><span class="text-danger"><?php echo form_error('from_date'); ?></span>
                            </div>
                            <div class="col-sm-3 col-md-3" hidden>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('enquiry')." ".$this->lang->line('to'); ?> <?php echo $this->lang->line('date'); ?></label>
                                        <input type="text" autocomplete="off" name="to_date" class="form-control  date"  value="<?php  echo set_value('to_date') ?>">
                                    </div><span class="text-danger"><?php echo form_error('to_date'); ?></span>
                            </div>
                            <div class="col-sm-3 col-md-3">
                                <div class="form-group">  
                                    <label><?php echo $this->lang->line('status'); ?></label>
                                    <select  id="status" name="status" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <option value="all" <?php
                                        if ($status == "all") {
                                            echo "selected";
                                        }
                                        ?>><?php echo $this->lang->line('all') ?></option>
                                                <?php foreach ($enquiry_status as $enkey => $envalue) {
                                                    ?>
                                            <option <?php
                                            if ($enkey == $status) {
                                                echo "selected";
                                            }
                                            ?> value="<?php echo $enkey ?>"><?php echo $envalue ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('status'); ?></span>
                                </div>  
                            </div>
                            <div class="form-group">
                                <div class="col-sm-0 col-md-2">
                                    <button type="submit" name="search" value="search_filter" style="margin-top: 21px" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>

                                </div>
                            </div>
                        </div>     
                    </form>
                    <div class="ptt10">
                        <div class="bordertop">
                            <div class="box-header with-border">
                                <h3 class="box-title titlefix"> Liste de demande de formation</h3>
                                <div class="box-tools pull-right">
                                    <?php if ($this->rbac->hasPrivilege('training_enquiry', 'can_add')) { ?>
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></button> 
                                    <?php } ?>      
                                </div><!-- /.box-tools -->
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="download_label"> Liste de demande de formation</div>
                                <div class="mailbox-messages">
                                    <div class="table-responsive">  
                                        <table class="table table-hover table-striped table-bordered" id="enquirytable">
                                            <thead>
                                                <tr>
                                                    <th>Employée</th>
                                                    <th>Intitulé de la formation</th>
                                                    <th>Objectifs de la formation</th>
                                                    <th>Responsable</th>

                                                    <th><?php echo $this->lang->line('date'); ?></th>


                                                    <th><?php echo $this->lang->line('status'); ?></th>
                                                    <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                if (empty($trainingreq_list)) {
                                                    ?>
                                                    <?php
                                                } else {
                                                    foreach ($trainingreq_list as $key => $value) {
                                                        $current_date = date("Y-m-d");
                                                        $next_date = $value["next_date"];
                                                        if (empty($next_date)) {

                                                            $next_date = $value["follow_up_date"];
                                                        }

                                                        if ($next_date < $current_date) {
                                                            $class = "class='dange'";
                                                        } else {
                                                            $class = "";
                                                        }
                                                        ?>
                                                        <tr <?php echo $class ?>>

                                                            <td class="mailbox-name"><?php echo $value['name']; ?></td>
                                                            <td class="mailbox-name"><?php echo $value['training_name']; ?> </td>
                                                            <td class="mailbox-name"><?php echo $value['objectifs']; ?> </td>
                                                            <td class="mailbox-name"><?php echo $value['assigned']; ?></td>

                                                            <td class="mailbox-name"> <?php
                                                                if (!empty($value["date"])) {
                                                                    echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value['date']));
                                                                }
                                                                ?></td>

                                                            <!--<td> <?php echo $value["status"] ?></td>-->
                                                            <td>
                                                                <?php
                                                                $status = $value["status"];

                                                                // Définition du style et du libellé selon le status
                                                                switch ($status) {
                                                                    case 'pending':
                                                                        $label = "En attente";
                                                                        $color = "#ff9801"; // orange
                                                                        break;
                                                                    case 'active':
                                                                        $label = "Approuvé";
                                                                        $color = "#4caf50"; // vert
                                                                        break;
                                                                    case 'passive':
                                                                        $label = "Rejeté";
                                                                        $color = "#f44337"; // rouge
                                                                        break;
                                                                    default:
                                                                        $label = ucfirst($status);
                                                                        $color = "#9e9e9e"; // gris
                                                                        break;
                                                                }

                                                                // Affichage HTML
                                                                echo "<h6><span class='label' style='background-color: {$color}; border-radius: 2px; color: white; padding: 3px 8px;'>{$label}</span></h6>";
                                                                ?>
                                                            </td>


                                                            <td class="mailbox-date text-right white-space-nowrap">
                                                                <?php if ($this->rbac->hasPrivilege('training_follow', 'can_view')) { ?>
                                                                    <a class="btn btn-default btn-xs" onclick="follow_up('<?php echo $value['id']; ?>', '<?php echo $value['status']; ?>');"  data-target="#follow_up" data-toggle="modal"  title="Action">
                                                                        <i class="fa fa-reorder"></i>
                                                                    </a>
                                                                <?php }
                                                                ?>
                                                                <?php if ($this->rbac->hasPrivilege('training_enquiry', 'can_edit')) { ?>
                                                                    <a  onclick="getRecord('<?php echo $value['id']; ?>', '<?php echo $value['status']; ?>')" class="btn btn-default btn-xs" data-target="#myModaledit" data-toggle="modal"   title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i>
                                                                    </a>
                                                                <?php }
                                                                ?>
                                                                <?php if ($this->rbac->hasPrivilege('training_enquiry', 'can_delete')) { ?>
                                                                    <a data-placement="left" href="#" class="btn btn-default btn-xs" data-toggle="tooltip" title="" onclick="delete_enquiry('<?php echo $value["id"] ?>')" data-original-title="<?php echo $this->lang->line('delete'); ?>">
                                                                        <i class="fa fa-remove"></i>
                                                                    </a>
                                                                <?php }
                                                                ?>
                                                            </td>
														</tr>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table><!-- /.table -->
                                    </div>  
                                </div><!-- /.mail-box-messages -->
                            </div><!-- /.box-body -->
                        </div>
                    </div>
                </div>  
            </div>
    </section>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-media-content">
                <div class="modal-header modal-media-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="box-title"> Formulaire de demande de formation</h4>
                </div>

                <div class="modal-body pt0 pb0">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <form id="formadd" method="post" class="ptt10">
                                <div class="row">
                                    <div class="col-sm-4" hidden>
                                        <div class="form-group">
                                            <label for="pwd"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>  
                                            <input type="text" readonly id="name_add" autocomplete="off" class="form-control" value="<?php echo $this->customlib->getAdminSessionUserName(); ?>" name="name">
                                            <span id="name_add_error" class="text-danger"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4" hidden>
                                        <div class="form-group">
                                            <label for="pwd"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                                            <input type="text" readonly id="created_by" autocomplete="off" class="form-control" value="<?php echo $this->customlib->getAdminSessionUserName(); ?>" name="created_by">
                                            <span id="created_by" class="text-danger"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="pwd">Intitulé de la formation</label><small class="req"> *</small>
                                            <input id="training_name" autocomplete="off" name="training_name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('training_name'); ?>" />
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Responsable hiérachie</label>
                                            <select name="assigned" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php foreach ($stff_list as $key => $stff_list_value) { ?>
                                                    <option value="<?php echo $stff_list_value['name'].' '.$stff_list_value['surname']; ?>" ><?php echo $stff_list_value['name'].' '.$stff_list_value['surname']; ?></option>
                                                <?php }
                                                ?>
                                            </select>
                                        </div><!--./form-group-->
                                    </div>


                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="pwd">Date souhaitée</label>
                                            <input type="text" id="date" name="date" class="form-control date" value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>" readonly="">
                                            <span id="date_add_error" class="text-danger"></span>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                        <label for="objectifs" class="form-label">Objectifs de la formation</label>
                                        <textarea class="form-control" id="objectifs" name="objectifs" rows="2" required></textarea>
                                    </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="commentaires" class="form-label">Commentaires supplémentaires</label>
                                            <textarea class="form-control" id="commentaires" name="commentaires" rows="2"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-sm-4" hidden>
                                        <div class="form-group">
                                        <label for="poste" class="form-label">Poste actuel</label>
                                        <input type="text" class="form-control" id="poste" name="poste" required>
                                    </div>
                                    </div>

                                    <div class="col-sm-4" hidden>
                                        <div class="form-group">
                                        <label for="departement" class="form-label">Département</label>
                                        <input type="text" class="form-control" id="departement" name="departement" required>
                                    </div>
                                    </div>









                                </div><!--./row-->    
                            </form>                       
                        </div><!--./col-md-12-->  
                    </div><!--./row--> 
                    <div class="row">    
                        <div class="box-footer col-md-12">
                            <a  onclick="saveEnquiry()" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    </div>
    <div class="modal fade" id="myModaledit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-media-content">
                <div class="modal-header modal-media-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="box-title"><?php echo $this->lang->line('edit_admission_enquiry'); ?></h4>

                </div>
                <div class="modal-body pt0 pb0" id="getdetails">
                    <div id="alert_message">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="follow_up" tabindex="-1" role="dialog" aria-labelledby="follow_up">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-media-content">
                <div class="modal-header modal-media-header">
                    <button type="button" class="close" onclick="update()" data-dismiss="modal">&times;</button>
                    <h4 class="box-title">Formulaire de demande</h4>
                </div>
                <div class="modal-body pt0 pb0" id="getdetails_follow_up">
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        
      moment.lang('en', {
          week: { dow: start_week }
        });
     $('#enquiry_date').daterangepicker(
        {
           
            locale: {
                    format: calendar_date_time_format
                }
        });
    });

    function getRecord(id, status) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/training_request/detailstraining_request/' + id + '/' + status,
            success: function (result) {
                $('#getdetails').html(result);
            }
        });
    }
	
    function postRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/training_request/editpost/' + id,
            type: 'POST',
            data: $("#myForm1").serialize(),
            dataType: 'json',
            success: function (data) {
                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
                    window.location.reload(true);
                }
            },
            error: function () {
                alert("Fail")
            }
        });
    }

    function saveEnquiry() {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/training_request/add/',
            type: 'POST',
            dataType: 'json',
            data: $("#formadd").serialize(),
            success: function (data) {
                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
                    window.location.reload(true);
                }
            },
            error: function () {
                alert("Fail")
            }
        });
    }

    function delete_enquiry(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm') ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/training_request/delete/' + id,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                }
            })
        }
    }

    function follow_up(id, status) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/training_request/follow_up/' + id + '/' + status,
            success: function (data) {
                $('#getdetails_follow_up').html(data);
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/training_request/follow_up_list/' + id,
                    success: function (data) {
                        $('#timeline').html(data);
                    },
                    error: function () {
                        alert("Fail")
                    }
                });
            },
            error: function () {
                alert("Fail")
            }
        });
    }

    function update() {
        window.location.reload(true);
    }
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#enquirytable").DataTable({
            searching: true,
            paging: true,
            bSort: true,
            info: false,
            dom: "Bfrtip",
            buttons: [

                {
                    extend: 'copyHtml5',
                    text: '<i class="fa fa-files-o"></i>',
                    titleAttr: 'Copy',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-text-o"></i>',
                    titleAttr: 'CSV',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i>',
                    titleAttr: 'PDF',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'

                    }
                },

                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i>',
                    titleAttr: 'Print',
                    title: $('.download_label').html(),
                    customize: function (win) {
                        $(win.document.body)
                                .css('font-size', '10pt');

                        $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                    },
                    exportOptions: {
                        columns: ':visible'
                    }
                },

                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i>',
                    titleAttr: 'Columns',
                    title: $('.download_label').html(),
                    postfixButtons: ['colvisRestore']
                },
            ]
        });
    });
</script>