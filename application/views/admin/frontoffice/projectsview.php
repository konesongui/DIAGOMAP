<style>
    .badge.bg-success {
        background-color: #28a745 !important; /* Vert */
        color: white !important;
    }
    .badge.bg-warning {
        background-color: #ffc107 !important; /* Jaune */
        color: black !important;
    }
    .badge.bg-danger {
        background-color: #dc3545 !important; /* Rouge */
        color: white !important;
    }
    .badge.bg-secondary {
        background-color: #6c757d !important; /* Gris */
        color: white !important;
    }

</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-sitemap"></i> Liste des projets</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> Liste des projets</h3>
                    </div>
                    <div class="col-md-12">
                        <?php echo $this->session->flashdata('msg') ?>
                    </div>
                    <form role="form" action="<?php echo site_url('admin/projects') ?>" method="post" class="">
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
                            <div class="col-sm-3 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('enquiry')." ".$this->lang->line('from'); ?> <?php echo $this->lang->line('date'); ?></label>

                                    <input type="text" autocomplete="off" name="from_date" class="form-control  date"  value="<?php  echo set_value('from_date') ?>">
                                </div><span class="text-danger"><?php echo form_error('from_date'); ?></span>
                            </div>
                            <div class="col-sm-3 col-md-3">
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
                                <div class="col-sm-12">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>

                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="ptt10">
                        <div class="bordertop">
                            <div class="box-header with-border">
                                <h3 class="box-title titlefix"> Liste des projets</h3>
                                <div class="box-tools pull-right">
                                    <?php if ($this->rbac->hasPrivilege('projet', 'can_add')) { ?>
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?> à nouveau projet</button>
                                    <?php } ?>
                                </div><!-- /.box-tools -->
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="download_label"><?php echo $this->lang->line('admission_enquiry'); ?> <?php echo $this->lang->line('list'); ?></div>
                                <div class="mailbox-messages">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped table-bordered" id="enquirytable">
                                            <thead>
                                            <tr>
                                                <th>Nom du projet</th>
                                               <!-- <th>Objectif</th>-->
                                                <th>Budget</th>
                                                <th>Client</th>
                                                <th>Chef de projet</th>
                                                <th>Date du début</th>
                                                <th>Date de fin</th>

                                                <th><?php echo $this->lang->line('status'); ?></th>
                                                <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                            </tr>
                                            </thead>
                                            <?php
                                            // Définition des statuts avec traduction
                                            $config['enquiry_status'] = array(
                                                'approve'       => lang('approve'),
                                                'disapprove'    => lang('disapprove'),
                                                'pending'       => lang('pending'),
                                                'in_progress'   => lang('in_progress'),
                                                'on_hold'       => lang('on_hold'),
                                                'completed'     => lang('completed'),
                                                'cancelled'     => lang('cancelled'),
                                                'review'        => lang('review'),
                                                'draft'         => lang('draft'),
                                                'archived'      => lang('archived'),
                                            );

                                            // Définition des couleurs associées aux statuts
                                            $enquiry_status_colors = array(
                                                'approve'       => 'success',   // vert
                                                'disapprove'    => 'danger',    // rouge
                                                'pending'       => 'warning',   // jaune
                                                'in_progress'   => 'primary',   // bleu
                                                'on_hold'       => 'secondary', // gris
                                                'completed'     => 'success',   // vert
                                                'cancelled'     => 'danger',    // rouge
                                                'review'        => 'info',      // bleu clair
                                                'draft'         => 'dark',      // noir/gris foncé
                                                'archived'      => 'secondary', // gris
                                            );
                                            ?>

                                            <tbody>
                                            <?php if (!empty($projects_list)): ?>
                                                <?php foreach ($projects_list as $value): ?>
                                                    <?php
                                                    $status_key = $value["status"];
                                                    $status_label = isset($config['status'][$status_key]) ? $config['status'][$status_key] : $status_key;
                                                    $status_class = isset($status_colors[$status_key]) ? $status_colors[$status_key] : 'secondary';
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $value['projet']; ?></td>
                                                        <td><?php echo $value['montant']; ?></td>
                                                        <td><?php echo $value['client']; ?></td>
                                                        <td><?php echo $value['chef_projet']; ?></td>
                                                        <td><?php echo $value['start_date']; ?></td>
                                                        <td><?php echo $value['end_date']; ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $status_class; ?>">
                                                                <?php echo $status_label; ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-right">
                                                            <a class="btn btn-default btn-xs" onclick="projects_up('<?php echo $value['id']; ?>', '<?php echo $value['status']; ?>');" data-target="#projects_up" data-toggle="modal" title="Suivi du projet">
                                                                <i class="fa fa-plus"></i>
                                                            </a>
                                                            <a onclick="getRecord('<?php echo $value['id']; ?>', '<?php echo $value['status']; ?>')" class="btn btn-default btn-xs" data-target="#myModaledit" data-toggle="modal" title="Modifier">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                            <a href="#" class="btn btn-default btn-xs" onclick="delete_enquiry('<?php echo $value["id"] ?>')" title="Supprimer">
                                                                <i class="fa fa-remove"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
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
                    <h4 class="box-title"> Ajouter un nouveau projet</h4>
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
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="pwd">Nom du projet</label><small class="req"> *</small>
                                            <input id="projet" autocomplete="off" name="projet" placeholder="" type="text" class="form-control"  value="<?php echo set_value('projet'); ?>" />
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="pwd">Budget</label><small class="req"> *</small>
                                            <input id="text" autocomplete="off" name="montant" placeholder="" type="text" class="form-control"  value="<?php echo set_value('montant'); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="pwd">Client</label><small class="req"> *</small>
                                            <input id="text" autocomplete="off" name="client" placeholder="" type="text" class="form-control"  value="<?php echo set_value('client'); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="pwd">Chef de projet</label><small class="req"> *</small>
                                            <input id="text" autocomplete="off" name="chef_projet" placeholder="" type="text" class="form-control"  value="<?php echo set_value('chef_projet'); ?>" />
                                        </div>
                                    </div>


                                    <div class="col-sm-4" hidden>
                                        <div class="form-group">
                                            <label for="pwd"><?php echo $this->lang->line('phone'); ?></label><small class="req"> *</small>
                                            <input id="number" autocomplete="off" name="contact" placeholder="" type="text" class="form-control"  value="<?php echo set_value('contact'); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4" hidden>
                                        <div class="form-group">
                                            <label>Objectif</label>
                                            <input type="text" value="<?php echo set_value('objet'); ?>" name="objet" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-4" hidden>
                                        <div class="form-group">
                                            <label for="email"><?php echo $this->lang->line('address'); ?></label>
                                            <textarea name="address" class="form-control" ><?php echo set_value('address'); ?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-sm-4" hidden>
                                        <div class="form-group">
                                            <label for="pwd"><?php echo $this->lang->line('note'); ?></label>
                                            <textarea name="note" class="form-control" ><?php echo set_value('note'); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="pwd">Date de début</label>
                                            <input id="date" autocomplete="" name="start_date" placeholder="" type="date" class="form-control"  value="<?php echo set_value('start_date', date($this->customlib->getSchoolDateFormat())); ?>" />
                                            <span id="start_date" class="text-danger"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="pwd">Date de fin</label>
                                            <!--<label for="pwd"><?php echo $this->lang->line('next_follow_up_date'); ?></label>-->
                                            <input id="date" autocomplete="" name="end_date" placeholder="" type="date" class="form-control"  value="<?php echo set_value('end_date', date($this->customlib->getSchoolDateFormat())); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4" hidden>
                                        <div class="form-group">
                                            <label for="pwd">Dernière date mise a jour</label>
                                            <!--<label for="pwd"><?php echo $this->lang->line('next_follow_up_date'); ?></label>-->
                                            <input type="text" id="date_of_call" name="follow_up_date"class="form-control date" value="<?php echo set_value('follow_up_date', date($this->customlib->getSchoolDateFormat())); ?>" readonly="">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('assigned'); ?></label>
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
                                            <label for="pwd">Date</label><small class="req"> *</small>
                                            <input id="date" autocomplete="" name="date" placeholder="" type="date" class="form-control"  value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="email"><?php echo $this->lang->line('description'); ?></label>
                                            <textarea name="description" class="form-control" ><?php echo set_value('description'); ?></textarea>
                                        </div>
                                    </div>


                                    <div class="col-sm-3" hidden>
                                        <div class="form-group">
                                            <label for="pwd"><?php echo $this->lang->line('reference'); ?></label>
                                            <select name="reference" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php foreach ($Reference as $key => $value) { ?>
                                                    <option value="<?php echo $value['reference']; ?>" <?php if (set_value('reference') == $value['reference']) { ?>selected=""<?php } ?>><?php echo $value['reference']; ?></option>
                                                <?php }
                                                ?>
                                            </select>

                                        </div><!--./form-group-->
                                    </div>
                                    <div class="col-sm-3" hidden>
                                        <div class="form-group">
                                            <label for="pwd">Titre référence</label> <small class="req"> *</small>
                                            <!--<input type="text" autocomplete="off" name="source" class="form-control"  value="<?php  echo set_value('source') ?>">-->

                                            <select name="source" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php foreach ($sourcelist as $key => $value) { ?>
                                                    <option value="<?php echo $value['source']; ?>"><?php echo $value['source']; ?></option>
                                                <?php }
                                                ?>
                                            </select>
                                        </div><!--./form-group-->
                                    </div>
                                    <div class="col-sm-3" hidden>
                                        <div class="form-group">
                                            <label for="pwd"><?php echo $this->lang->line('class'); ?></label>
                                            <select name="class" class="form-control"  >
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php
                                                foreach ($class_list as $key => $value) {
                                                    ?>
                                                    <option value="<?php echo $value['id'] ?>" <?php if (set_value('class') == $value['id']) { ?> selected="" <?php } ?>><?php echo $value['class'] ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div><!--./form-group-->
                                    </div>
                                    <div class="col-sm-3" hidden>
                                        <div class="form-group">
                                            <label for="pwd"><?php echo $this->lang->line('number_of_child'); ?></label>
                                            <input type="number" class="form-control" min="1" value="<?php echo set_value('no_of_child'); ?>" name="no_of_child">
                                        </div><!--./form-group-->
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
                    <h4 class="box-title">Editer projet</h4>

                </div>
                <div class="modal-body pt0 pb0" id="getdetails">
                    <div id="alert_message">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="projects_up" tabindex="-1" role="dialog" aria-labelledby="projects_up">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-media-content">
                <div class="modal-header modal-media-header">
                    <button type="button" class="close" onclick="update()" data-dismiss="modal">&times;</button>
                    <h4 class="box-title">Suivi de projet</h4>
                </div>
                <div class="modal-body pt0 pb0" id="getdetails_projects_up">
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
            url: '<?php echo base_url(); ?>admin/projects/details/' + id + '/' + status,
            success: function (result) {
                $('#getdetails').html(result);
            }
        });
    }

    function postRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/projects/editpost/' + id,
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
            url: '<?php echo base_url(); ?>admin/projects/add/',
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
                url: '<?php echo base_url(); ?>admin/projects/delete/' + id,
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

    function projects_up(id, status) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/projects/projects_up/' + id + '/' + status,
            success: function (data) {
                $('#getdetails_projects_up').html(data);
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/projects/projects_up_list/' + id,
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