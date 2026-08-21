<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style>
    /* Rendre les checkboxes visibles */

    input[type="checkbox"].minimal {
        opacity: 1 !important;
        position: relative !important;
        left: auto !important;
        width: 18px !important;
        height: 18px !important;
        margin: 0 auto !important;
        display: inline-block !important;
        cursor: pointer !important;
        accent-color: #007bff;
        transform: scale(1.2);
        pointer-events: auto !important;
        visibility: visible !important;
        z-index: 10;
    }

    /* Style spécifique pour la checkbox d'en-tête */
    #selectAll {
        accent-color: #007bff;
        width: 20px !important;
        height: 20px !important;
        transform: scale(1.3);
    }

    /* Checkboxes individuelles (vertes quand disponibles) */
    .payslip-checkbox {
        accent-color: #28a745 !important;
    }

    /* Checkboxes désactivées */
    input[type="checkbox"].minimal:disabled {
        accent-color: #6c757d;
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Centrage des checkboxes dans les cellules */
    td, th {
        vertical-align: middle !important;
        text-align: center;
    }

    /* Style pour l'en-tête du tableau */
    th {
        background-color: #f4f4f4;
        font-weight: bold;
    }

    /* Désactiver complètement iCheck pour éviter qu'il cache nos checkboxes */
    .icheckbox_minimal-blue,
    .iradio_minimal-blue,
    .icheckbox_minimal,
    .iradio_minimal {
        display: none !important;
    }

    /* Forcer l'affichage des checkboxes natives */
    input[type="checkbox"] {
        -webkit-appearance: checkbox !important;
        -moz-appearance: checkbox !important;
        appearance: checkbox !important;
    }

    /* Ajustement pour le survol */
    input[type="checkbox"]:hover {
        cursor: pointer;
        transform: scale(1.3);
    }

    /* Style pour le compteur */
    #selectedCount {
        font-weight: bold;
        color: #fff;
    }
</style>

<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1><i class="fa fa-sitemap"></i> <?php echo $this->lang->line('human_resource'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <form id='form1' action="<?php echo site_url('admin/payroll') ?>"  method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <div class="row">

                                <?php
                                if ($this->session->flashdata('msg')) {
                                    echo $this->session->flashdata('msg');
                                }
                                ?>

                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">
                                            <?php echo $this->lang->line("role"); ?>
                                        </label>
                                        <select autofocus="" onchange="getEmployeeName(this.value)" id="role" name="role" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($classlist as $key => $class) {

                                                if (isset($_POST["role"])) {
                                                    $role_selected = $_POST["role"];
                                                } else {
                                                    $role_selected = '';
                                                }
                                                ?>
                                                <option value="<?php echo $class["type"] ?>"
                                                    <?php
                                                    if ($class["type"] == $role_selected) {
                                                        echo "selected";
                                                    }
                                                    ?> ><?php print_r($class["type"]) ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('role'); ?></span>
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('month') ?></label>

                                        <select autofocus="" id="class_id" name="month" class="form-control" >
                                            <option value="select"><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            if (isset($month)) {
                                                $month_selected = date("F", strtotime($month));
                                            } else {
                                                $month_selected = date("F", strtotime("-1 month"));
                                            }
                                            foreach ($monthlist as $m_key => $month_value) {
                                                ?>
                                                <option value="<?php echo $m_key ?>" <?php
                                                if ($month_selected == $m_key) {
                                                    echo "selected =selected";
                                                }
                                                ?>><?php echo $month_value; ?></option>
                                                <?php
                                                $count++;
                                            }
                                            ?>

                                        </select>
                                        <span class="text-danger"><?php echo form_error('month'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('year'); ?></label>

                                        <select autofocus="" id="class_id" name="year" class="form-control" >
                                            <option value="select"><?php echo $this->lang->line('select'); ?></option>
                                            <option <?php
                                            if ($year == date("Y", strtotime("-1 year"))) {
                                                echo "selected";
                                            }
                                            ?>  value="<?php echo date("Y", strtotime("-1 year")) ?>"><?php echo date("Y", strtotime("-1 year")) ?></option>
                                            <option <?php
                                            if ($year == date("Y")) {
                                                echo "selected";
                                            }
                                            ?>  value="<?php echo date("Y") ?>"><?php echo date("Y") ?></option>
                                        </select>

                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" name="search" value="search" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                    </div>
                                </div>
                            </div>


                        </div>


                    </form>

                    <?php
                    if (isset($resultlist)) {
                    ?>
                    <div class="box-header ptbnull"></div>

                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('staff'); ?> <?php echo $this->lang->line('list'); ?>
                            </i></h3>
                        <div class="box-tools pull-right">
                            <!-- Boutons d'action multiple -->

                                <button type="button" class="btn btn-success btn-sm" id="selectAllBtn">
                                    <i class="fa fa-check-square-o"></i> Tout sélectionner
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" id="deselectAllBtn">
                                    <i class="fa fa-square-o"></i> Tout désélectionner
                                </button>

                            <button type="button" class="btn btn-info btn-sm" id="printSelectedBtn" disabled>
                                <i class="fa fa-print"></i> Imprimer la sélection (<span id="selectedCount">0</span>)
                            </button>
                        </div>
                    </div>
                    <div class="box-body table-responsive">

                        <div class="download_label"><?php echo $this->lang->line('staff'); ?> <?php echo $this->lang->line('list'); ?></div>

                        <!-- Formulaire pour l'impression multiple -->
                        <form id="printMultipleForm" action="<?php echo site_url('admin/payroll/printMultiplePayslips'); ?>" method="post" target="_blank">
                            <input type="hidden" name="selected_payslips" id="selectedPayslipsInput" value="">

                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                <tr>
                                    <!-- CHECKBOX D'EN-TÊTE VISIBLE -->
                                    <th width="60px" style="text-align: end; background-color: #e9ecef;">
                                        <input type="checkbox" id="selectAll" class="minimal"
                                               style="transform: scale(1.4); margin: 0; cursor: pointer;
                                                      width: 10px; height: 20px; accent-color: #007bff;">
                                    </th>
                                    <th><?php echo $this->lang->line('staff_id'); ?></th>
                                    <th><?php echo $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('role'); ?></th>
                                    <?php if ($sch_setting->staff_department) { ?>
                                        <th><?php echo $this->lang->line('department'); ?></th>
                                    <?php } if ($sch_setting->staff_designation) { ?>
                                        <th><?php echo $this->lang->line('designation'); ?></th>
                                    <?php } if ($sch_setting->staff_phone) { ?>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('status'); ?></th>
                                    <th class="text-right no-print"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $count = 1;
                                foreach ($resultlist as $staff) {
                                    $status = $staff["status"];

                                    if ($staff["status"] == "paid") {
                                        $label = "class='label label-success'";
                                        $wstatus = $payroll_status[$staff["status"]];
                                    } else if ($staff["status"] == "generated") {
                                        $label = "class='label label-warning'";
                                        $wstatus = $payroll_status[$staff["status"]];
                                    } else {
                                        $label = "class='label label-default'";
                                        $wstatus = $payroll_status["not_generate"];
                                    }

                                    // Vérifier si l'employé a un bulletin de paie
                                    $hasPayslip = ($staff["payslip_id"] > 0 && $staff["status"] == "paid");
                                    $disabled = !$hasPayslip ? 'disabled' : '';
                                    ?>
                                    <tr>
                                        <!-- CASE À COCHER INDIVIDUELLE VISIBLE -->
                                        <td style="text-align: center; vertical-align: middle;">
                                            <?php if ($hasPayslip) { ?>
                                                <input type="checkbox" name="payslip_ids[]"
                                                       value="<?php echo $staff['payslip_id']; ?>"
                                                       class="minimal payslip-checkbox"
                                                       style="transform: scale(1.3); margin: 0; cursor: pointer;
                                                              width: 18px; height: 18px; accent-color: #28a745;">
                                            <?php } else { ?>
                                                <input type="checkbox" disabled class="minimal"
                                                       style="transform: scale(1.3); margin: 0; cursor: not-allowed;
                                                              width: 18px; height: 18px; opacity: 0.5;">
                                            <?php } ?>
                                        </td>

                                        <td style="text-align: left;"><?php echo $staff['employee_id']; ?></td>
                                        <td style="text-align: left;"><?php echo $staff['name'] . " " . $staff['surname']; ?></td>
                                        <td style="text-align: left;"><?php echo $staff['user_type']; ?></td>
                                        <?php if ($sch_setting->staff_department) { ?>
                                            <td style="text-align: left;"><?php echo $staff['department']; ?></td>
                                        <?php } if ($sch_setting->staff_designation) { ?>
                                            <td style="text-align: left;"><?php echo $staff['designation']; ?></td>
                                        <?php } if ($sch_setting->staff_phone) { ?>
                                            <td style="text-align: left;"><?php echo $staff['contact_no']; ?></td>
                                        <?php } ?>
                                        <td style="text-align: left;"><small <?php echo $label; ?>><?php echo $wstatus; ?></small></td>

                                        <td class="text-right no-print">
                                            <?php if ($status == "paid") { ?>
                                                <?php if ($this->rbac->hasPrivilege('staff_payroll', 'can_add')) { ?>
                                                    <a class="btn btn-default btn-xs" onclick="return confirm('<?php echo $this->lang->line("are_you_sure_you_want_to_revert_this_record")?>')" href="<?php echo base_url() . "admin/payroll/revertpayroll/" . $staff["payslip_id"] . "/" . $month_selected . "/" . date("Y") . "/" . $role_selected ?>" title="Revert">
                                                        <i class="fa fa-undo"></i>
                                                    </a>
                                                <?php } ?>
                                                <a href="javascript:void" onclick="getPayslip('<?php echo $staff["payslip_id"]; ?>')" role="button" class="btn btn-primary btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('Payslip View'); ?>">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <!-- Bouton envoyer par email -->
                                                <a href="javascript:void(0)" onclick="sendPayslipByEmail('<?php echo $staff["payslip_id"]; ?>')" role="button" class="btn btn-info btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('send_by_email'); ?>">
                                                    <i class="fa fa-envelope"></i>
                                                </a>
                                            <?php } ?>

                                            <?php if ($status == "generated") { ?>
                                                <?php if ($this->rbac->hasPrivilege('staff_payroll', 'can_delete')) { ?>
                                                    <a href="<?php echo base_url() ?>admin/payroll/deletepayroll/<?php echo $staff["payslip_id"] . "/" . $month_selected . "/" . date("Y") . "/" . $role_selected ?>" class="btn btn-default btn-xs" onclick="return confirm('<?php echo $this->lang->line("are_you_sure_you_want_to_revert_this_record")?>')" title="Revert">
                                                        <i class="fa fa-undo"></i>
                                                    </a>
                                                <?php } ?>
                                                <?php if ($this->rbac->hasPrivilege('staff_payroll', 'can_add')) { ?>
                                                    <a href="#" onclick="getRecord('<?php echo $staff["id"] ?>', '<?php echo $year ?>')" role="button" class="btn btn-primary btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('Proceed to payment'); ?>">
                                                        <i class="fa fa-money"></i>
                                                    </a>
                                                <?php } ?>
                                            <?php } ?>

                                            <?php if ($staff["payslip_id"] == 0) { ?>
                                                <?php if ($this->rbac->hasPrivilege('staff_payroll', 'can_add')) { ?>
                                                    <a class="btn btn-primary btn-xs" role="button" href="<?php echo base_url() . "admin/payroll/create/" . $month_selected . "/" . $year . "/" . $staff["id"] ?>">
                                                        <i class="fa fa-file-text"></i>
                                                    </a>
                                                <?php } ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                $count++;
                                ?>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <?php
                }
                ?>
            </div>
            <form action="<?php echo base_url('admin/payroll/create') ?>" method="post" id="formsubmit">
                <input type="hidden" name="month" id="month">
                <input type="hidden" name="year" id="year">
                <input type="hidden" name="staffid" id="staffid">
            </form>
        </div>

    </section>
</div>

<!-- Modals existants -->
<div id="payslipview" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('details'); ?> <span id="print1"></span></h4>
            </div>
            <div class="modal-body" id="testdata">
            </div>
        </div>
    </div>
</div>

<div id="proceedtopay" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('proceed_to_pay'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form role="form" id="schsetting_form" action="<?php echo site_url('admin/payroll/paymentSuccess') ?>">
                        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('staff'); ?> <?php echo $this->lang->line('Name'); ?></label>
                            <input type="text" name="emp_name" readonly class="form-control" id="emp_name">
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('payment'); ?> <?php echo $this->lang->line('amount'); ?></label>
                            <input type="text" name="amount" readonly class="form-control" id="amount">
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label for="exampleInputEmail1">Période</label>
                            <input id="monthid" name="month" readonly placeholder="" type="text" class="form-control" />
                            <input name="paymentmonth" placeholder="" type="hidden" class="form-control" />
                            <input name="paymentyear" placeholder="" type="hidden" class="form-control" />
                            <input name="paymentid" placeholder="" type="hidden" class="form-control" />
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('payment'); ?> <?php echo $this->lang->line('mode'); ?></label><small class="req"> *</small>
                            <select name="payment_mode" id="payment_mode" class="form-control">
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                <?php foreach ($payment_mode as $pkey => $pvalue) { ?>
                                    <option value="<?php echo $pkey ?>"><?php echo $pvalue ?></option>
                                <?php } ?>
                            </select>
                            <span class="text-danger"><?php echo form_error('payment_mode'); ?></span>
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('payment'); ?> <?php echo $this->lang->line('date'); ?></label>
                            <input type="text" name="payment_date" id="payment_date" class="form-control" value="<?php echo date("m/d/Y") ?>">
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-6" hidden>
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('note'); ?></label>
                            <textarea name="remarks" class="form-control"></textarea>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <button type="button" class="btn btn-primary submit_schsetting pull-right" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing"> <?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Fonctions existantes
    function getRecord(id, year) {
        $('input[name="amount"]').val('');
        $('input[name="emp_name"]').val('');
        $('input[name="paymentid"]').val('');
        $('input[name="paymentmonth"]').val('');
        $('input[name="paymentyear"]').val('');
        $('#monthid').val('');
        var month = '<?php echo $month_selected ?>';
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/payroll/paymentRecord',
            type: 'POST',
            data: {staffid: id, month: month, year: year},
            dataType: "json",
            success: function (result) {
                $('input[name="amount"]').val(result.result.gross_salary);
                $('input[name="emp_name"]').val(result.result.name + ' ' + result.result.surname + ' (' + result.result.employee_id + ')');
                $('input[name="paymentid"]').val(result.result.id);
                $('input[name="paymentmonth"]').val(month);
                $('input[name="paymentyear"]').val(year);
                $('#monthid').val(month + '-' + year);
            }
        });
        $('#proceedtopay').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
    }

    function popup(data) {
        var base_url = '<?php echo base_url() ?>';
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        frame1.css({"position": "", "top": "-1000000px"});
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();
        frameDoc.document.write('<html>');
        frameDoc.document.write('<head>');
        frameDoc.document.write('<title></title>');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/bootstrap/css/bootstrap.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/font-awesome.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/ionicons.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/AdminLTE.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/skins/_all-skins.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/iCheck/flat/blue.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/morris/morris.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/datepicker/datepicker3.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/daterangepicker/daterangepicker-bs3.css">');
        frameDoc.document.write('</head>');
        frameDoc.document.write('<body>');
        frameDoc.document.write(data);
        frameDoc.document.write('</body>');
        frameDoc.document.write('</html>');
        frameDoc.document.close();
        setTimeout(function () {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
        }, 500);
        return true;
    }

    function getPayslip(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/payroll/payslipView',
            type: 'POST',
            data: {payslipid: id},
            success: function (result) {
                $("#print1").html("<a href='#' class='pull-right modal-title moprintblack' onclick='printData(" + id + ")' title='Print' ><i class='fa fa-print'></i></a>");
                $("#testdata").html(result);
            }
        });
        $('#payslipview').modal({
            show: true,
            keyboard: false
        });
    }

    function printData(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/payroll/payslipView',
            type: 'POST',
            data: {payslipid: id},
            success: function (result) {
                $("#testdata").html(result);
                popup(result);
            }
        });
    }

    function getEmployeeName(role) {
        var base_url = '<?php echo base_url() ?>';
        $("#name").html("<option value=''>select</option>");
        var div_data = "";
        $.ajax({
            type: "POST",
            url: base_url + "admin/staff/getEmployeeByRole",
            data: {'role': role},
            dataType: "json",
            success: function (data) {
                $.each(data, function (i, obj) {
                    div_data += "<option value='" + obj.name + "'>" + obj.name + "</option>";
                });
                $('#name').append(div_data);
            }
        });
    }

    function create(id) {
        var month = '<?php if (isset($_POST["month"])) { echo $_POST["month"]; } ?>';
        var year = '<?php if (isset($_POST["year"])) { echo $_POST["year"]; } ?>';
        $("#month").val(month);
        $("#year").val(year);
        $("#staffid").val(id);
        $("#formsubmit").submit();
    }

    function sendPayslipByEmail(payslipId) {
        if (!payslipId || payslipId == 0) {
            alert('ID du bulletin invalide');
            return false;
        }
        if (confirm('Êtes-vous sûr de vouloir envoyer ce bulletin de paie par email ?')) {
            var btn = event.target;
            var originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Envoi...';
            btn.disabled = true;
            $.ajax({
                url: '<?php echo site_url("admin/payroll/sendPayslipEmail"); ?>',
                type: 'POST',
                data: { id: payslipId },
                dataType: 'json',
                success: function(response) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    if (response.status === "success") {
                        alert('✅ ' + response.message);
                    } else {
                        alert('❌ ' + (response.message || 'Erreur inconnue'));
                    }
                },
                error: function(xhr, status, error) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    alert('❌ Erreur de connexion: ' + error);
                }
            });
        }
        return false;
    }

    // ⭐⭐ FONCTIONS POUR LA SÉLECTION MULTIPLE AVEC CHECKBOXES NATIVES ⭐⭐
    function updateSelectedCount() {
        var count = $('.payslip-checkbox:checked').length;
        $('#selectedCount').text(count);
        if (count > 0) {
            $('#printSelectedBtn').prop('disabled', false);
        } else {
            $('#printSelectedBtn').prop('disabled', true);
        }
    }

    function printSelectedPayslips() {
        var selectedIds = [];
        $('.payslip-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            alert('Veuillez sélectionner au moins un bulletin de paie');
            return false;
        }

        // Créer un formulaire temporaire pour l'envoi en POST
        var form = $('<form></form>');
        form.attr('method', 'post');
        form.attr('action', '<?php echo site_url("admin/payroll/printMultiplePayslips"); ?>');
        form.attr('target', '_blank');

        // Ajouter le champ avec les IDs
        var input = $('<input></input>');
        input.attr('type', 'hidden');
        input.attr('name', 'selected_payslips');
        input.attr('value', selectedIds.join(','));

        form.append(input);

        // Ajouter le formulaire au body, le soumettre, puis le supprimer
        $('body').append(form);
        form.submit();
        form.remove();

        return false;
    }

    $(document).on('click', '.submit_schsetting', function (e) {
        var $this = $(this);
        $this.button('loading');
        $.ajax({
            url: '<?php echo site_url("admin/payroll/paymentSuccess") ?>',
            type: 'post',
            data: $('#schsetting_form').serialize(),
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

    function getSectionByClass(class_id, section_id) {
        if (class_id != "" && section_id != "") {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj) {
                        var sel = "";
                        if (section_id == obj.section_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        }
    }

    $(document).ready(function () {
        console.log('=== DOCUMENT READY ===');
        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id') ?>';
        getSectionByClass(class_id, section_id);

        $(document).on('change', '#class_id', function (e) {
            $('#section_id').html("");
            var class_id = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj) {
                        div_data += "<option value=" + obj.section_id + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        });

        // ⭐⭐ GESTION DES CASES À COCHER NATIVES ⭐⭐
        // Désactiver complètement iCheck pour nos checkboxes
        if ($.fn.iCheck) {
            // Détruire iCheck sur nos éléments spécifiques
            $('input[type="checkbox"].minimal').each(function() {
                if ($(this).data('icheck')) {
                    $(this).iCheck('destroy');
                }
            });
        }

        // Rendre toutes les checkboxes visibles
        $('input[type="checkbox"]').css({
            'opacity': '1',
            'position': 'relative',
            'width': '18px',
            'height': '18px',
            'margin': '0 auto',
            'display': 'inline-block'
        });

        // Sélectionner/Déselectionner toutes les cases
        $('#selectAll').on('change', function() {
            $('.payslip-checkbox').prop('checked', $(this).prop('checked'));
            updateSelectedCount();
        });

        // Mettre à jour le compteur quand une case est cochée/décochée
        $('.payslip-checkbox').on('change', function() {
            updateSelectedCount();

            // Mettre à jour la case "Tout sélectionner"
            var totalCheckboxes = $('.payslip-checkbox').length;
            var checkedCheckboxes = $('.payslip-checkbox:checked').length;

            if (totalCheckboxes == checkedCheckboxes && totalCheckboxes > 0) {
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
        });

        // Bouton "Tout sélectionner"
        $('#selectAllBtn').click(function() {
            $('.payslip-checkbox').prop('checked', true);
            $('#selectAll').prop('checked', true);
            updateSelectedCount();
        });

        // Bouton "Tout désélectionner"
        $('#deselectAllBtn').click(function() {
            $('.payslip-checkbox').prop('checked', false);
            $('#selectAll').prop('checked', false);
            updateSelectedCount();
        });

        // Bouton d'impression multiple
        $('#printSelectedBtn').click(function() {
            printSelectedPayslips();
        });

        // Mettre à jour le compteur initial
        updateSelectedCount();

        console.log('=== INITIALISATION TERMINÉE ===');
    });
</script>