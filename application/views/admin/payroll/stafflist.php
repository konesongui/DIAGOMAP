<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

// Récupération des caisses et banques pour le paiement
$CI = &get_instance();
$conn = new mysqli($CI->db->hostname, $CI->db->username, $CI->db->password, $CI->db->database);
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

// Caisses actives
$sqlCaisses = "SELECT id, name, amount_re as solde_actuel 
               FROM income 
               WHERE est_actif = 1 AND is_deleted = 'no' 
               ORDER BY name ASC";
$resultCaisses = $conn->query($sqlCaisses);
$caisses = [];
if ($resultCaisses && $resultCaisses->num_rows > 0) {
    while($caisse = $resultCaisses->fetch_assoc()) {
        $caisses[] = $caisse;
    }
}

// Banques actives
$sqlBanques = "SELECT id, name as nom, balance as solde 
               FROM banks 
               WHERE status = 1 
               ORDER BY name ASC";
$resultBanques = $conn->query($sqlBanques);
$banques = [];
if ($resultBanques && $resultBanques->num_rows > 0) {
    while($banque = $resultBanques->fetch_assoc()) {
        $banques[] = $banque;
    }
}
$conn->close();
?>

<style>
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
    #selectAll { accent-color: #007bff; width: 20px !important; height: 20px !important; transform: scale(1.3); }
    .payslip-checkbox { accent-color: #28a745 !important; }
    input[type="checkbox"].minimal:disabled { accent-color: #6c757d; opacity: 0.5; cursor: not-allowed; }
    td, th { vertical-align: middle !important; text-align: center; }
    th { background-color: #f4f4f4; font-weight: bold; }
    .icheckbox_minimal-blue, .iradio_minimal-blue, .icheckbox_minimal, .iradio_minimal { display: none !important; }
    input[type="checkbox"] { -webkit-appearance: checkbox !important; -moz-appearance: checkbox !important; appearance: checkbox !important; }
    input[type="checkbox"]:hover { cursor: pointer; transform: scale(1.3); }
    #selectedCount { font-weight: bold; color: #fff; }
</style>

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
                    <form id='form1' action="<?php echo site_url('admin/payroll') ?>" method="post">
                        <div class="box-body">
                            <div class="row">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line("role"); ?></label>
                                        <select name="role" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($classlist as $class) { ?>
                                                <option value="<?php echo $class["type"] ?>" <?php if (isset($_POST["role"]) && $_POST["role"] == $class["type"]) echo "selected"; ?>><?php echo $class["type"] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('month') ?></label>
                                        <select name="month" class="form-control">
                                            <option value="select"><?php echo $this->lang->line('select'); ?></option>
                                            <?php $month_selected = isset($month) ? date("F", strtotime($month)) : date("F", strtotime("-1 month")); ?>
                                            <?php foreach ($monthlist as $m_key => $month_value) { ?>
                                                <option value="<?php echo $m_key ?>" <?php if ($month_selected == $m_key) echo "selected"; ?>><?php echo $month_value; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('year'); ?></label>
                                        <select name="year" class="form-control">
                                            <option value="select"><?php echo $this->lang->line('select'); ?></option>
                                            <option <?php if ($year == date("Y", strtotime("-1 year"))) echo "selected"; ?> value="<?php echo date("Y", strtotime("-1 year")) ?>"><?php echo date("Y", strtotime("-1 year")) ?></option>
                                            <option <?php if ($year == date("Y")) echo "selected"; ?> value="<?php echo date("Y") ?>"><?php echo date("Y") ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" name="search" value="search" class="btn btn-primary btn-sm pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <?php if (isset($resultlist)) { ?>
                    <div class="box-header ptbnull"></div>
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('staff'); ?> <?php echo $this->lang->line('list'); ?></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-success btn-sm" id="selectAllBtn"><i class="fa fa-check-square-o"></i> Tout sélectionner</button>
                            <button type="button" class="btn btn-warning btn-sm" id="deselectAllBtn"><i class="fa fa-square-o"></i> Tout désélectionner</button>
                            <button type="button" class="btn btn-info btn-sm" id="printSelectedBtn" disabled><i class="fa fa-print"></i> Imprimer la sélection (<span id="selectedCount">0</span>)</button>
                        </div>
                    </div>
                    <div class="box-body table-responsive">
                        <form id="printMultipleForm" action="<?php echo site_url('admin/payroll/printMultiplePayslips'); ?>" method="post" target="_blank">
                            <input type="hidden" name="selected_payslips" id="selectedPayslipsInput" value="">
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                <tr>
                                    <th width="60px"><input type="checkbox" id="selectAll" class="minimal"></th>
                                    <th><?php echo $this->lang->line('staff_id'); ?></th>
                                    <th><?php echo $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('role'); ?></th>
                                    <?php if ($sch_setting->staff_department) { ?><th><?php echo $this->lang->line('department'); ?></th><?php } ?>
                                    <?php if ($sch_setting->staff_designation) { ?><th><?php echo $this->lang->line('designation'); ?></th><?php } ?>
                                    <?php if ($sch_setting->staff_phone) { ?><th><?php echo $this->lang->line('phone'); ?></th><?php } ?>
                                    <th><?php echo $this->lang->line('status'); ?></th>
                                    <th class="text-right no-print"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($resultlist as $staff) {
                                    $status = $staff["status"];
                                    if ($status == "paid") { $label = "class='label label-success'"; $wstatus = $payroll_status[$staff["status"]]; }
                                    else if ($status == "generated") { $label = "class='label label-warning'"; $wstatus = $payroll_status[$staff["status"]]; }
                                    else { $label = "class='label label-default'"; $wstatus = $payroll_status["not_generate"]; }
                                    $hasPayslip = ($staff["payslip_id"] > 0 && $staff["status"] == "paid");
                                    ?>
                                    <tr>
                                        <td>
                                            <?php if ($hasPayslip) { ?>
                                                <input type="checkbox" name="payslip_ids[]" value="<?php echo $staff['payslip_id']; ?>" class="minimal payslip-checkbox">
                                            <?php } else { ?>
                                                <input type="checkbox" disabled>
                                            <?php } ?>
                                        </td>
                                        <td style="text-align: left;"><?php echo $staff['employee_id']; ?></td>
                                        <td style="text-align: left;"><?php echo $staff['name'] . " " . $staff['surname']; ?></td>
                                        <td style="text-align: left;"><?php echo $staff['user_type']; ?></td>
                                        <?php if ($sch_setting->staff_department) { ?><td style="text-align: left;"><?php echo $staff['department']; ?></td><?php } ?>
                                        <?php if ($sch_setting->staff_designation) { ?><td style="text-align: left;"><?php echo $staff['designation']; ?></td><?php } ?>
                                        <?php if ($sch_setting->staff_phone) { ?><td style="text-align: left;"><?php echo $staff['contact_no']; ?></td><?php } ?>
                                        <td style="text-align: left;"><small <?php echo $label; ?>><?php echo $wstatus; ?></small></td>
                                        <td class="text-right no-print">
                                            <?php if ($status == "paid") { ?>
                                                <a class="btn btn-default btn-xs" onclick="return confirm('<?php echo $this->lang->line("are_you_sure_you_want_to_revert_this_record")?>')" href="<?php echo base_url() . "admin/payroll/revertpayroll/" . $staff["payslip_id"] . "/" . $month_selected . "/" . date("Y") . "/" . ($_POST['role']??'') ?>" title="Revert"><i class="fa fa-undo"></i></a>
                                                <a href="javascript:void" onclick="getPayslip('<?php echo $staff["payslip_id"]; ?>')" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i></a>
                                                <a href="javascript:void(0)" onclick="sendPayslipByEmail('<?php echo $staff["payslip_id"]; ?>')" class="btn btn-info btn-xs"><i class="fa fa-envelope"></i></a>
                                            <?php } ?>
                                            <?php if ($status == "generated") { ?>
                                                <a href="<?php echo base_url() ?>admin/payroll/deletepayroll/<?php echo $staff["payslip_id"] . "/" . $month_selected . "/" . date("Y") . "/" . ($_POST['role']??'') ?>" class="btn btn-default btn-xs" onclick="return confirm('<?php echo $this->lang->line("are_you_sure_you_want_to_revert_this_record")?>')"><i class="fa fa-undo"></i></a>
                                                <a href="#" onclick="getRecord('<?php echo $staff["id"] ?>', '<?php echo $year ?>')" class="btn btn-primary btn-xs"><i class="fa fa-money"></i></a>
                                            <?php } ?>
                                            <?php if ($staff["payslip_id"] == 0) { ?>
                                                <a class="btn btn-primary btn-xs" href="<?php echo base_url() . "admin/payroll/create/" . $month_selected . "/" . $year . "/" . $staff["id"] ?>"><i class="fa fa-file-text"></i></a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>

<!-- Modal affichage bulletin -->
<div id="payslipview" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('details'); ?> <span id="print1"></span></h4>
            </div>
            <div class="modal-body" id="testdata"></div>
        </div>
    </div>
</div>

<!-- Modal paiement avec sélection caisse/banque -->
<div id="proceedtopay" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('proceed_to_pay'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form role="form" id="schsetting_form" action="<?php echo site_url('admin/payroll/paymentSuccess') ?>" method="post">
                        <input type="hidden" name="source_type" id="source_type" value="caisse">
                        <div class="form-group col-md-6">
                            <label><?php echo $this->lang->line('staff'); ?> <?php echo $this->lang->line('Name'); ?></label>
                            <input type="text" name="emp_name" readonly class="form-control" id="emp_name">
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php echo $this->lang->line('payment'); ?> <?php echo $this->lang->line('amount'); ?></label>
                            <input type="text" name="amount" readonly class="form-control" id="amount">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Période</label>
                            <input id="monthid" name="month" readonly class="form-control" />
                            <input name="paymentmonth" type="hidden" />
                            <input name="paymentyear" type="hidden" />
                            <input name="paymentid" type="hidden" />
                            <input name="date_from" type="hidden" id="date_from" />
                            <input name="date_to" type="hidden" id="date_to" />
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php echo $this->lang->line('payment'); ?> <?php echo $this->lang->line('mode'); ?></label><small class="req"> *</small>
                            <select name="payment_mode" id="payment_mode" class="form-control" required>
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                <option value="cash">Espèces</option>
                                <option value="cheque">Chèque</option>
                                <option value="transfer">Virement</option>
                                <option value="card">Carte bancaire</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php echo $this->lang->line('payment'); ?> <?php echo $this->lang->line('date'); ?></label>
                            <input type="text" name="payment_date" id="payment_date" class="form-control" value="<?php echo date("m/d/Y") ?>" required>
                        </div>
                        <!-- Caisse -->
                        <div class="form-group col-md-6" id="caisse_group">
                            <label for="caisse_id">Caisse <span class="text-danger">*</span></label>
                            <select class="form-control" id="caisse_id" name="caisse_id">
                                <option value="">Sélectionner une caisse...</option>
                                <?php foreach ($caisses as $caisse): ?>
                                    <option value="<?= $caisse['id'] ?>" data-balance="<?= $caisse['solde_actuel'] ?>">
                                        <?= htmlspecialchars($caisse['name']) ?> (Solde: <?= number_format($caisse['solde_actuel'], 0, ',', ' ') ?> FCFA)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Banque -->
                        <div class="form-group col-md-6" id="banque_group" style="display: none;">
                            <label for="banque_id">Banque <span class="text-danger">*</span></label>
                            <select class="form-control" id="banque_id" name="banque_id">
                                <option value="">Sélectionner une banque...</option>
                                <?php foreach ($banques as $banque): ?>
                                    <option value="<?= $banque['id'] ?>" data-balance="<?= $banque['solde'] ?>">
                                        <?= htmlspecialchars($banque['nom']) ?> (Solde: <?= number_format($banque['solde'], 0, ',', ' ') ?> FCFA)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Information source -->
                        <div class="col-md-12" id="source_info" style="display: none;">
                            <div class="alert alert-info"><i class="fa fa-info-circle"></i> <span id="selected_source_info"></span></div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary submit_schsetting pull-right"><?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function getRecord(id, year) {
        $('input[name="amount"]').val('');
        $('input[name="emp_name"]').val('');
        $('input[name="paymentid"]').val('');
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
                if (result.result.date_from) $('#date_from').val(result.result.date_from);
                if (result.result.date_to) $('#date_to').val(result.result.date_to);
            }
        });
        $('#proceedtopay').modal({ show: true, backdrop: 'static', keyboard: false });
    }

    function getPayslip(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/payroll/payslipView',
            type: 'POST',
            data: {payslipid: id},
            success: function (result) {
                $("#print1").html("<a href='#' class='pull-right' onclick='printData(" + id + ")'><i class='fa fa-print'></i></a>");
                $("#testdata").html(result);
            }
        });
        $('#payslipview').modal('show');
    }

    function printData(id) {
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + 'admin/payroll/payslipView',
            type: 'POST',
            data: {payslipid: id},
            success: function (result) {
                popup(result);
            }
        });
    }

    function popup(data) {
        var base_url = '<?php echo base_url() ?>';
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        frame1.css({"position": "absolute", "top": "-1000000px"});
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow || frame1[0].contentDocument;
        if (frameDoc.document) frameDoc = frameDoc.document;
        frameDoc.open();
        frameDoc.write('<html><head><title></title>');
        frameDoc.write('<link rel="stylesheet" href="' + base_url + 'backend/bootstrap/css/bootstrap.min.css">');
        frameDoc.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/AdminLTE.min.css">');
        frameDoc.write('</head><body>');
        frameDoc.write(data);
        frameDoc.write('</body></html>');
        frameDoc.close();
        setTimeout(function () { window.frames["frame1"].focus(); window.frames["frame1"].print(); frame1.remove(); }, 500);
        return true;
    }

    function sendPayslipByEmail(payslipId) {
        if (!payslipId) { alert('ID invalide'); return; }
        if (confirm('Envoyer ce bulletin par email ?')) {
            var btn = event.target;
            var originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Envoi...';
            btn.disabled = true;
            $.ajax({
                url: '<?php echo site_url("admin/payroll/sendPayslipEmail"); ?>',
                type: 'POST',
                data: { id: payslipId },
                dataType: 'json',
                success: function(res) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    alert(res.status === 'success' ? '✅ ' + res.message : '❌ ' + res.message);
                },
                error: function() { btn.innerHTML = originalText; btn.disabled = false; alert('Erreur réseau'); }
            });
        }
    }

    function updateSelectedCount() {
        var count = $('.payslip-checkbox:checked').length;
        $('#selectedCount').text(count);
        $('#printSelectedBtn').prop('disabled', count === 0);
    }

    function printSelectedPayslips() {
        var selectedIds = [];
        $('.payslip-checkbox:checked').each(function() { selectedIds.push($(this).val()); });
        if (selectedIds.length === 0) { alert('Sélectionnez au moins un bulletin'); return; }
        var form = $('<form method="post" action="<?php echo site_url("admin/payroll/printMultiplePayslips"); ?>" target="_blank"></form>');
        form.append($('<input type="hidden" name="selected_payslips" value="' + selectedIds.join(',') + '">'));
        $('body').append(form); form.submit(); form.remove();
    }

    $(document).ready(function() {
        // Désactiver iCheck
        if ($.fn.iCheck) {
            $('input[type="checkbox"].minimal').each(function() { if ($(this).data('icheck')) $(this).iCheck('destroy'); });
        }
        $('input[type="checkbox"]').css({ opacity: 1, position: 'relative', width: '18px', height: '18px', margin: '0 auto', display: 'inline-block' });

        $('#selectAll').on('change', function() { $('.payslip-checkbox').prop('checked', $(this).prop('checked')); updateSelectedCount(); });
        $('.payslip-checkbox').on('change', function() { updateSelectedCount(); $('#selectAll').prop('checked', $('.payslip-checkbox:checked').length === $('.payslip-checkbox').length); });
        $('#selectAllBtn').click(function() { $('.payslip-checkbox').prop('checked', true); $('#selectAll').prop('checked', true); updateSelectedCount(); });
        $('#deselectAllBtn').click(function() { $('.payslip-checkbox').prop('checked', false); $('#selectAll').prop('checked', false); updateSelectedCount(); });
        $('#printSelectedBtn').click(printSelectedPayslips);
        updateSelectedCount();

        // Gestion dynamique caisse/banque selon le mode de paiement
        $(document).on('change', '#payment_mode', function() {
            var mode = $(this).val();
            if (mode === 'cash') {
                $('#caisse_group').show();
                $('#banque_group').hide();
                $('#caisse_id').prop('required', true);
                $('#banque_id').prop('required', false);
                $('#source_type').val('caisse');
            } else if (mode === 'cheque' || mode === 'transfer' || mode === 'card') {
                $('#caisse_group').hide();
                $('#banque_group').show();
                $('#caisse_id').prop('required', false);
                $('#banque_id').prop('required', true);
                $('#source_type').val('banque');
            } else {
                $('#caisse_group, #banque_group').hide();
                $('#caisse_id, #banque_id').prop('required', false);
                $('#source_info').hide();
            }
            updateSourceInfo();
        });

        function updateSourceInfo() {
            var mode = $('#payment_mode').val();
            var text = '';
            if (mode === 'cash' && $('#caisse_id').val()) {
                text = 'Caisse : ' + $('#caisse_id option:selected').text();
                $('#source_info').fadeIn();
            } else if ((mode === 'cheque' || mode === 'transfer' || mode === 'card') && $('#banque_id').val()) {
                text = 'Banque : ' + $('#banque_id option:selected').text();
                $('#source_info').fadeIn();
            } else {
                $('#source_info').fadeOut();
            }
            $('#selected_source_info').text(text);
        }

        $(document).on('change', '#caisse_id, #banque_id', updateSourceInfo);
        $('#proceedtopay').on('shown.bs.modal', function() { $('#payment_mode').trigger('change'); });
        $('#payment_mode').trigger('change');

        // Soumission du formulaire
        $(document).on('click', '.submit_schsetting', function() {
            var $btn = $(this);
            $btn.button('loading');

            var mode = $('#payment_mode').val();
            if (!mode) { errorMsg('Veuillez sélectionner un mode de paiement'); $btn.button('reset'); return; }

            var sourceType = '', sourceId = '';
            if (mode === 'cash') {
                sourceType = 'caisse';
                sourceId = $('#caisse_id').val();
                if (!sourceId) { errorMsg('Veuillez sélectionner une caisse'); $btn.button('reset'); return; }
            } else if (mode === 'cheque' || mode === 'transfer' || mode === 'card') {
                sourceType = 'banque';
                sourceId = $('#banque_id').val();
                if (!sourceId) { errorMsg('Veuillez sélectionner une banque'); $btn.button('reset'); return; }
            } else {
                errorMsg('Mode de paiement invalide');
                $btn.button('reset');
                return;
            }

            var dataToSend = {
                payment_mode: mode,
                payment_date: $('#payment_date').val(),
                remarks: $('textarea[name="remarks"]').val(),
                paymentid: $('input[name="paymentid"]').val(),
                emp_name: $('#emp_name').val(),
                amount: $('#amount').val(),
                month: $('input[name="month"]').val(),
                paymentmonth: $('input[name="paymentmonth"]').val(),
                paymentyear: $('input[name="paymentyear"]').val(),
                date_from: $('#date_from').val(),
                date_to: $('#date_to').val(),
                source_type: sourceType,
                source_id: sourceId
            };
            if (sourceType === 'caisse') dataToSend.caisse_id = sourceId;
            else dataToSend.banque_id = sourceId;

            $.ajax({
                url: $('#schsetting_form').attr('action'),
                type: 'POST',
                data: dataToSend,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        successMsg(res.message);
                        window.location.reload(true);
                    } else {
                        errorMsg(res.message || (res.error ? Object.values(res.error).join('\n') : 'Erreur inconnue'));
                    }
                    $btn.button('reset');
                },
                error: function() {
                    errorMsg('Erreur réseau');
                    $btn.button('reset');
                }
            });
        });
    });
</script>