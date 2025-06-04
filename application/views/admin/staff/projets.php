<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-sitemap"></i> <?php echo $this->lang->line('human_resource'); ?>
        </h1>
    </section>
    <!-- Main content -->

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Projets </h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('projet', 'can_add')) { ?>
                                <small class="pull-right"><a href="#addtraining" onclick="addTraining()" role="button" class="btn btn-primary btn-sm checkbox-toggle pull-right edit_setting" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing">Ajouter un projet</a></small>
                            <?php } ?>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="tab-pane active table-responsive no-padding">
                                    <div class="download_label"><?php echo $this->lang->line('leaves'); ?></div>
                                    <table class="table table-striped table-bordered table-hover example">
                                        <thead>
                                        <th>Nom du projet</th>
                                        <th>Montant</th>
                                        <th>Date début</th>
                                        <th>Date fin</th>
                                        <th>Description</th>
                                      <!--  <th><?php echo $this->lang->line('status'); ?></th>-->
                                        <th class="text-right no-print"><?php echo $this->lang->line('action'); ?></th>

                                        </thead>
                                        <tbody>
                                        <?php
                                        $i = 0;
                                        foreach ($projet as $key => $value) {
                                            ?>
                                            <tr>


                                                <td><?php echo $value["type"] ?></td>
                                                <td><?php echo $value["training"] ?></td>
                                                <td><?php echo $value["number_per"] ?></td>
                                                <td><?php echo $value["amount"] ?></td>
                                                <td><?php echo $value["start_date"] ?></td>
                                                <td><?php echo $value["end_date"] ?></td>

                                                <td><?php echo $value["resume"]; ?></td>

                                                <?php
                                                $can_delete = 1;
                                                if ($value["status"] == "approve") {
                                                    $can_delete = 0;
                                                    $label = "class='label label-success'";
                                                } else if ($value["status"] == "pending") {

                                                    $label = "class='label label-warning'";
                                                } else if ($value["status"] == "disapprove") {
                                                    $label = "class='label label-danger'";
                                                }
                                                ?>
                                               <!-- <td><span data-toggle="popover" class="detail_popover" data-original-title="" title=""><small <?php echo $label ?>><?php echo $status[$value["status"]]; ?></small></span>

                                                    <div class="fee_detail_popover" style="display: none"><?php echo $this->lang->line('submitted_by').": " . $value['applied_by']; ?></div></td>-->
                                                <td class="pull-right no-print"><a data-placement="left" href="#trainingdetails" onclick="getRecord('<?php echo $value["id"] ?>')" role="button" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>" ><i class="fa fa-reorder"></i></a>

                                                    <?php if ($can_delete == 1) { ?>
                                                        <a onclick="getDelete('<?php echo $value["id"] ?>')"  class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" ><i class="fa fa-remove"> </i></a>
                                                    <?php } ?>

                                                </td>

                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div id="trainingdetails" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('details'); ?></h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <form role="form" id="leavedetails_form" action="">
                        <div class="col-md-12 table-responsive">
                            <table class="table mb0 table-striped table-bordered ">
                                <tr>
                                    <th width="15%">Structure</th>
                                    <td width="35%"><span id='type'></span></td>
                                    <th width="15%">Formation</th>
                                    <td width="35%"><span id="training"></span>
                                        <span class="text-danger"><?php echo form_error('leave_request_id'); ?></span>
                                    </td>

                                </tr>
                                <tr>

                                    <th>Nombre de personne</th>
                                    <td><span id="number_per"></span></td>
                                    <th>Montant</th>

                                <tr>
                                    <th>Date début</th>
                                    <td><span id='start_date'></span> <span class="text-danger"><?php echo form_error('leave_from'); ?></span></td>
                                    <th>Date fin</th>
                                    <td><span id="end_date"></span></td>
                                </tr>

                                <tr>


                                    <th>Description</th>
                                    <td><span id="remark"> </span></td>

                                </tr>


                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="addtraining" class="modal fade " role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Ajouter un projet</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form role="form" id="addleave_form" method="post" enctype="multipart/form-data" action="">
                        <div class="form-group  col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label>Structure</label>
                            <input type="text" id="type" name="type" value="" class="form-control">
                        </div>
                        <div class="form-group  col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label>Formation</label>
                            <input type="text" id="training" name="training" value="" class="form-control">
                        </div>
                        <div class="form-group  col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label>Nombre de personne</label>
                            <input type="text" id="number_per" name="number_per" value="" class="form-control">
                        </div>
                        <div class="form-group  col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label>Montant</label>
                            <input type="text" id="amount" name="amount" value="" class="form-control">
                        </div>
                        <div class="form-group  col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label>Date de début</label>
                            <input type="date" id="start_date" name="start_date" value="" class="form-control">
                        </div>
                        <div class="form-group  col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label>Date de fin</label>
                            <input type="date" id="end_date" name="end_date" value="" class="form-control">
                        </div>
                        <div class="form-group  col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label>Détails</label>
                            <textarea name="resume" id="resume" style="resize: none;" rows="4" class="form-control"></textarea>
                        </div>

                        <div class="clearfix"></div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <button type="submit" class="btn btn-primary submit_addLeave pull-right" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing"> <?php echo $this->lang->line('save'); ?></button>
                            <input type="reset"  name="resetbutton" id="resetbutton" style="display:none">
                            <button type="button" style="display: none;" id="clearform" onclick="clearForm(this.form)" class="btn btn-primary submit_addLeave pull-right" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing"> <?php echo $this->lang->line('clear'); ?></button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    /*--dropify--*/
    $(document).ready(function () {
        // Basic
        $('.filestyle').dropify();
    });
    /*--end dropify--*/
</script>

<script type="text/javascript">
    function getDelete(id) {
        var result = confirm("<?php echo $this->lang->line('delete_confirm'); ?>");
        if (result) {
            $.ajax({
                url: "<?php echo base_url(); ?>admin/trainings/trainingdelete/" + id,
                type: "POST",

                success: function (res)
                {
                    successMsg('<?php echo $this->lang->line("delete_message"); ?>');

                    window.location.reload(true);

                },
                error: function (xhr) { // if error occured
                    alert("Error occured.please try again");

                },
                complete: function () {

                }

            });
        }
    }

    $(document).ready(function () {
        getLeaveTypeDDL('<?php echo $staff_id ?>', '');
        $('.detail_popover').popover({
            placement: 'right',
            title: '',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });

        var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';

        $('#leavefrom,#leaveto').datepicker({
            format: date_format,
            autoclose: true
        });
        $('#reservation').daterangepicker({
            timePickerIncrement: 5, locale: {
                format: calendar_date_time_format
            }});
    });

    function addTraining() {

        $('input[type=text]').val('');
        $('textarea[name="reason"]').text('');

        $("#resetbutton").click();
        $("#clearform").click();


        var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy',]) ?>';


        $('#reservation').daterangepicker({
            timePickerIncrement: 5, locale: {
                format: calendar_date_time_format
            }});
        var date = '<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>';
        $('input[type=text][name=applieddate]').val(date);

        $('#addtraining').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
    }


    function getRecord(id) {

        $("#download_file").html('');
        $('input:radio[name=status]').attr('checked', false);
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/leaverequest/leaveRecord',
            type: 'POST',
            data: {id: id},
            dataType: "json",
            success: function (result) {


                $('input[name="eave_request_id"]').val(result.id);
                $('#employee_id').html(result.employee_id);
                $('#name').html(result.name + ' ' + result.surname);
                $('#leave_from').html(result.leavefrom);
                $('#leave_to').html(result.leaveto);
                $('#leave_type').html(result.type);
                $('#days').html(result.leave_days + ' Days');
                $('#remark').html(result.employee_remark);
                $('#note').html(result.admin_remark);
                $('#applied_date').html(result.date);
                $('#appliedby').html(result.applied_by);
                $("#detailremark").text(result.admin_remark);
                console.log(result.document_file);
                if (result.document_file != "") {
                    var cl = "<i class='fa fa-download'></i>";
                    $("#download_file").html('<a href=' + base_url + 'admin/staff/download/' + result.staff_id + '/' + encodeURIComponent(result.document_file) + ' class=btn btn-default btn-xs  data-toggle=tooltip >' + cl + '</a>');
                }



            }
        });

        $('#trainingdetails').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
    }
    ;



    $(document).on('click', '.submit_schsetting', function (e) {
        var $this = $(this);
        $this.button('loading');
        $.ajax({
            url: '<?php echo site_url("admin/leaverequest/leaveStatus") ?>',
            type: 'post',
            data: $('#leavedetails_form').serialize(),
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

                $this.button('reset');
            }
        });
    });

    function checkStatus(status) {


        if (status == 'approve') {

            $("#reason").hide();
        } else if (status == 'pending') {

            $("#reason").hide();
        } else if (status == 'disapprove') {

            $("#reason").show();
        }

    }


    $(document).ready(function (e) {
        $("#addleave_form").on('submit', (function (e) {

            e.preventDefault();
            $.ajax({
                url: "<?php echo site_url("admin/trainings/createTrainings") ?>",
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data)
                {

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
            });
        }));


    });


    function getEmployeeName(role) {
        var ne = "";
        var base_url = '<?php echo base_url() ?>';
        $("#empname").html("<option value=''>Select</option>");
        var div_data = "";
        $.ajax({
            type: "POST",
            url: base_url + "admin/staff/getEmployeeByRole",
            data: {'role': role},
            dataType: "json",
            success: function (data) {
                $.each(data, function (i, obj)
                {


                    div_data += "<option value='" + obj.id + "' >" + obj.name + " " + obj.surname + " " + "(" + obj.employee_id + ")</option>";
                });

                $('#empname').append(div_data);
            }
        });
    }

    function setEmployeeName(role, id = '') {

        var ne = "";
        var base_url = '<?php echo base_url() ?>';
        $("#empname").html("<option value=''>Select</option>");
        var div_data = "";
        $.ajax({
            type: "POST",
            url: base_url + "admin/staff/getEmployeeByRole",
            data: {'role': role},
            dataType: "json",
            success: function (data) {
                $.each(data, function (i, obj)
                {
                    if (obj.employee_id == id) {
                        ne = 'selected';
                    } else {
                        ne = "";
                    }

                    div_data += "<option value='" + obj.id + "' " + ne + " >" + obj.name + " " + obj.surname + " " + "(" + obj.employee_id + ")</option>";
                });

                $('#empname').append(div_data);

            }
        });

    }

    function getLeaveTypeDDL(id, lid = '') {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/leaverequest/countLeave/' + id,
            type: 'POST',
            data: {lid: lid},
            //dataType: "json",
            success: function (result) {

                $("#leavetypeddl").html(result);

            }

        });
    }
    function editRecord(id) {

        var leave_from = '05/01/2018';
        var leave_to = '05/10/2018';
        $("#resetbutton").click();
        $('textarea[name="reason"]').text('');

        $('textarea[name="remark"]').text('');
        $('input:radio[name=addstatus]').attr('checked', false);

        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/leaverequest/leaveRecord',
            type: 'POST',
            data: {id: id},
            dataType: "json",
            success: function (result) {


                leave_from = result.leavefrom;
                leave_to = result.leaveto;


                setEmployeeName(result.user_type, result.employee_id);
                getLeaveTypeDDL(result.staff_id, result.lid);
                $('select[name="role"] option[value="' + result.user_type + '"]').attr("selected", "selected");
                $('input[name="applieddate"]').val(new Date(result.date).toString("MM/dd/yyyy"));
                $('input[name="leavefrom"]').val(new Date(result.leave_from).toString("MM/dd/yyyy"));
                $('input[name="filename"]').val(result.document_file);

                $('#type').val(result.type);
                $('#training').val(result.training);
                $('#number_per').val(result.number_per);

                $('input[name="amount"]').text(result.amount);
                $('textarea[name="start_date"]').text(result.start_date);
                $('textarea[name="end_date"]').text(result.end_date);

                if (result.status == 'approve') {

                    $('input:radio[name=addstatus]')[1].checked = true;

                } else if (result.status == 'pending') {
                    $('input:radio[name=addstatus]')[0].checked = true;

                } else if (result.status == 'disapprove') {
                    $('input:radio[name=addstatus]')[2].checked = true;

                }

                $('#reservation').daterangepicker({
                    startDate: leave_from,
                    endDate: leave_to
                });
            }
        });
        var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['m' => 'mm', 'd' => 'dd', 'Y' => 'yyyy',]) ?>';




        $('#addtraining').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
    }
    ;

    function clearForm(oForm) {

        var elements = oForm.elements;



        for (i = 0; i < elements.length; i++) {

            field_type = elements[i].type.toLowerCase();

            switch (field_type) {

                case "text":
                case "password":

                case "hidden":

                    elements[i].value = "";
                    break;

                case "select-one":
                case "select-multi":
                    elements[i].selectedIndex = "";
                    break;

                default:
                    break;
            }
        }
    }

</script>





