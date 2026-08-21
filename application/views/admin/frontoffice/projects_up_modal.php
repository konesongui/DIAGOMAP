<div class="row row-eq">
    <?php
    //print_r($enquiry_data);
    $admin = $this->customlib->getLoggedInUserData();
    // print_r($admin);
    ?>
    <div class="col-lg-8 col-md-8 col-sm-8 paddlr">
        <!-- general form elements -->

        <form id="projects_up_data" method="post" class="ptt10">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="form-group">
                        <label for="pwd">Titre de la tâche</label><small class="req"> *</small>
                        <input type="text" id="titre" name="titre"class="form-control" value="<?php echo set_value('titre') ?>">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="form-group">
                        <label for="pwd">Assigné A</label><small class="req"> *</small>
                        <select name="assigne[]" id="assigne" class="form-control select2" multiple>
                            <?php foreach ($stff_list as $stff_list_value) { ?>
                                <option value=" <?php echo $stff_list_value['name'].' '.$stff_list_value['surname']; ?>">
                                    <?php echo $stff_list_value['name'].' '.$stff_list_value['surname']; ?>
                                </option>
                            <?php } ?>
                        </select>
                        <small class="text-muted">
                            Vous pouvez rechercher et sélectionner plusieurs employés
                        </small>
                    </div>
                </div>


                <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    <label for="start_date">Date de début</label>
                    <input type="date" class="form-control" name="start_date" id="start_date">
                </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    <label for="due_date">Date de fin</label>
                    <input type="date" class="form-control" name="due_date" id="due_date">
                </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="form-group">
                    <label for="priority">Priorité</label>
                    <select name="priority" class="form-control">
                        <option value="Basse">Basse</option>
                        <option value="Normale" selected>Normale</option>
                        <option value="Haute">Haute</option>
                    </select>
                </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6" hidden>
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo $this->lang->line('follow_up_date'); ?></label><small class="req"> *</small>

                        <input type="hidden" id="enquiry_id" name="enquiry_id" value="<?php echo $projects_data['projet'] ?>">
                        <input type="hidden" id="chef_projet" name="chef_projet" value="<?php echo $projects_data['chef_projet'] ?>">
                        <input type="hidden" id="statut" name="statut" value="<?php echo $projects_data['status'] ?>">
                        <input type="hidden" id="client" name="client" value="<?php echo $projects_data['client'] ?>">
                        <input type="hidden" id="montant" name="montant" value="<?php echo $projects_data['montant'] ?>">

                        <input type="hidden" id="enquiry_status" name="enquiry_status" value="<?php echo $projects_data['status'] ?>">
                        <input type="text" id="follow_date" name="date" class="form-control date" value="<?php echo set_value('follow_up_date', date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($projects_data['date']))); ?>" readonly="">
                        <span class="text-danger" id="date_error"></span>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6" hidden>
                    <div class="form-group">
                        <label for="pwd"><?php echo $this->lang->line('next_follow_up_date'); ?></label><small class="req"> *</small>
                        <input type="text" id="follow_date_of_call" name="follow_up_date"class="form-control date" value="<?php echo set_value('follow_up_date') ?>" readonly="">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6" hidden>
                    <div class="form-group">
                        <label for="pwd">Objectif</label><small class="req"> *</small>
                        <textarea name="response" id="response" class="form-control" ><?php echo set_value('response'); ?></textarea>   
                        <span class="text-danger" id="responce_error"></span>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="form-group">
                        <label for="pwd">Objectif</label>
                        <textarea name="note" id="note" class="form-control" ><?php echo set_value('note'); ?></textarea>
                    </div>
                </div>
            </div><!-- /.box-body --> 
            <div class="box-footer pr0">
                <?php
                if ($this->rbac->hasPrivilege('follow_up_admission_enquiry', 'can_add')) {
                    ?>
                    <a onclick="follow_save()" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></a>
                    <?php
                }
                ?>

            </div>

        </form>

        <!-- <div class="ptbnull">
            <h4 class="box-title titlefix pb5"><?php echo $this->lang->line('follow_up'); ?> (<?php print_r($projects_data['projet']); ?>)</h4>
            <div class="box-tools pull-right">
            </div>
    </div>
    <div class="pt20">

        <div class="tab-pane active" id="timeline">


        </div>
    </div>-->
       <!-- /.box-body -->
    </div><!--/.col (left) -->
    <div class="col-lg-4 col-md-4 col-sm-4 col-eq">
        <div class="taskside">
            <?php //print_r($enquiry_data); ?>
            <h4><?php echo $this->lang->line('summary'); ?>
                <div style="font-size: 15px;" class="box-tools pull-right">
                    <label><?php echo $this->lang->line('status'); ?></label>
                    <div class="form-group">
                        <select class="form-control" id="status_data" onchange="changeStatus(this.value, '<?php echo $projects_data['id'] ?>')">

                            <?php
                            foreach ($enquiry_status as $enkey => $envalue) {
                                ?>
                                <option <?php
                                if ($projects_data["status"] == $enkey) {
                                    echo "selected";
                                }
                                ?> value="<?php echo $enkey ?>"><?php echo $envalue ?></option>
                                <?php }
                                ?>
                        </select>
                    </div>
                </div>
            </h4>
            <!-- /.box-tools -->
            <h5 class="pt0 task-info-created">
                <small class="text-dark"><?php echo $this->lang->line('created_by'); ?>: <span class="text-dark"><?php echo $admin['username']; ?></span></small>

            </h5>

            <hr class="taskseparator" />
            <div class="task-info task-single-inline-wrap task-info-start-date">
                <h5><i class="fa task-info-icon fa-fw fa-lg fa-calendar-plus-o pull-left fa-margin"></i>
                    <?php echo $this->lang->line('enquiry'); ?> <?php echo $this->lang->line('date'); ?>: <?php print_r(date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($projects_data['date']))); ?>
                </h5>
            </div>

            <div class="task-info task-single-inline-wrap task-info-start-date">
                <h5><i class="fa task-info-icon fa-fw fa-lg fa-calendar-plus-o pull-left fa-margin"></i>
                    <?php echo $this->lang->line('last_follow_up_date'); ?>: <?php
                    if (!empty($next_date)) {
                        echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($next_date[0]['date']));
                    }
                    ?>                                      
                </h5>
            </div>
            <div class="task-info task-single-inline-wrap task-info-start-date">
                <h5><i class="fa task-info-icon fa-fw fa-lg fa-calendar-plus-o pull-left fa-margin"></i>
                    <?php echo $this->lang->line('next_follow_up_date'); ?>: <?php
                    if (!empty($next_date)) {
                        echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($next_date[0]['next_date']));
                    } else {
                        echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($projects_data['follow_up_date']));
                    }
                    ?>
                </h5>
            </div>
            <div class="task-info task-single-inline-wrap ptt10">

                <label>Nom du projet: <?php echo $projects_data['projet']; ?></label>
                <label>Budget: <?php echo $projects_data['montant']; ?></label>
                <label>Client: <?php echo $projects_data['client']; ?></label>

                <label>Chef de projet: <?php echo $projects_data['chef_projet']; ?></label>
                <label>Date de début: <?php echo $projects_data['start_date']; ?></label>
                <label>Date de fin: <?php echo $projects_data['end_date']; ?></label>
                <!--<label><?php echo $this->lang->line('source'); ?>: <?php echo $projects_data['source']; ?></label>
                <label><?php echo $this->lang->line('email'); ?>: <?php echo $projects_data['email']; ?></label>
                <?php echo $this->lang->line('class'); ?>: <?php echo $projects_data['classname']; ?></label>
                <label><?php echo $this->lang->line('number_of_child'); ?>: <?php echo $projects_data['no_of_child']; ?></label>-->

                <label><?php echo $this->lang->line('assigned'); ?>: <?php echo $projects_data['assigned']; ?></label>
                <label>Statut: <?php echo $projects_data['status']; ?></label>
                </div>
        </div>
    </div>  
</div>
<script>
    function follow_save() {
       
        var id = $('#projects_projet').val();
        var status = $('#enquiry_status').val();
        var responce = $('#response').val();
        var follow_date = $('#follow_date').val();       

        $.ajax({
            url: '<?php echo base_url(); ?>admin/projects/projects_up_insert',
            type: 'POST',
            dataType: 'json',
            data: $("#projects_up_data").serialize(),
            success: function (data) {               

                if (data.status == "fail") {

                    var message = "";
                    $.each(data.error, function (index, value) {

                        message += value;
                    });
                    errorMsg(message);
                } else {

                    successMsg(data.message);
                    follow_up_new(id, status);
                }
            },

            error: function () {
                alert("Fail")
            }
        });
    }

    function follow_up_new(id, status) {

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

    function changeStatus(status, id) {      

        $.ajax({
            url: '<?php echo base_url(); ?>admin/projects/change_status/',
            type: 'POST',
            dataType: 'json',
            data: {status: status, id: id},
            success: function (data) {
                if (data.status == "fail") {
                    errorMsg(data.message);
                } else {
                    successMsg(data.message);
                    follow_up_new(id, status);
                }
            }

        })
    }
</script>

<!-- CSS de Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- JS de Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#assigne').select2({
            placeholder: "Sélectionnez un ou plusieurs employés",
            allowClear: true
        });
    });
</script>
