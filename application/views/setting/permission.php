<style type="text/css">
    .table .pull-right {text-align: initial; width: auto; float: right !important;}

    .modules-page {
        background: linear-gradient(180deg, #f5f8fc 0%, #eef3fb 100%);
        border-radius: 16px;
        padding: 18px;
        border: 1px solid #e3eaf5;
    }

    .modules-hero {
        background: linear-gradient(135deg, #0f2a4a 0%, #1e4a75 55%, #25689a 100%);
        color: #fff;
        border-radius: 14px;
        padding: 18px 20px;
        margin-bottom: 14px;
        box-shadow: 0 14px 28px rgba(15, 42, 74, 0.2);
    }

    .modules-hero h3 {
        margin: 0;
        font-weight: 700;
        letter-spacing: .2px;
    }

    .modules-hero p {
        margin: 6px 0 0;
        opacity: .92;
    }

    .module-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 14px;
    }

    .module-stat {
        background: #fff;
        border: 1px solid #dbe6f4;
        border-radius: 12px;
        padding: 12px;
        box-shadow: 0 6px 16px rgba(30, 74, 117, 0.08);
    }

    .module-stat .k {
        display: block;
        color: #6e7f97;
        font-size: 12px;
        margin-bottom: 2px;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .module-stat .v {
        display: block;
        color: #12395f;
        font-size: 24px;
        font-weight: 700;
        line-height: 1.1;
    }

    .modules-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #dbe6f4;
        overflow: hidden;
        box-shadow: 0 12px 24px rgba(30, 74, 117, 0.08);
    }

    .modules-card-header {
        padding: 12px 16px;
        border-bottom: 1px solid #e7eef8;
        background: #f8fbff;
        font-weight: 700;
        color: #193a5a;
    }

    .modules-table {
        margin-bottom: 0;
    }

    .modules-table thead th {
        background: #f0f6ff;
        color: #1a3c5d;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .45px;
        border-bottom: 1px solid #d7e5f7;
    }

    .modules-table tbody tr td {
        vertical-align: middle;
        border-top: 1px solid #edf3fb;
    }

    .modules-table tbody tr:hover {
        background: #f8fbff;
    }

    .module-name {
        font-weight: 600;
        color: #1d3e5e;
    }

    .state-pill {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 999px;
        margin-right: 10px;
    }

    .state-pill.on {
        background: #e7f8ee;
        color: #177a46;
        border: 1px solid #a5dfbc;
    }

    .state-pill.off {
        background: #fff1f1;
        color: #b73a3a;
        border: 1px solid #f2bcbc;
    }

    .switch-wrap {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
    }

    @media (max-width: 991px) {
        .module-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php
$total_modules = !empty($permissionList) ? count($permissionList) : 0;
$active_modules = 0;
if (!empty($permissionList)) {
    foreach ($permissionList as $m) {
        if (isset($m['is_active']) && (int)$m['is_active'] === 1) {
            $active_modules++;
        }
    }
}
$inactive_modules = $total_modules - $active_modules;
?>

<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="modules-page">
                    <div class="modules-hero">
                        <h3>Gestion des modules</h3>
                        <p>Activez ou desactivez les fonctionnalites du logiciel selon votre organisation.</p>
                    </div>

                    <div class="module-stats">
                        <div class="module-stat">
                            <span class="k">Total modules</span>
                            <span class="v"><?php echo (int)$total_modules; ?></span>
                        </div>
                        <div class="module-stat">
                            <span class="k">Modules actifs</span>
                            <span class="v"><?php echo (int)$active_modules; ?></span>
                        </div>
                        <div class="module-stat">
                            <span class="k">Modules inactifs</span>
                            <span class="v"><?php echo (int)$inactive_modules; ?></span>
                        </div>
                    </div>

                    <div class="nav-tabs-custom theme-shadow modules-card">
                    <ul class="nav nav-tabs pull-right">

                        <!--<li><a href="#tab_parent" data-toggle="tab"><?php echo $this->lang->line('parent') ?></a></li>
                        <li><a href="#tab_students" data-toggle="tab"><?php echo $this->lang->line('student') ?></a></li>-->
                        <li class="active"><a href="#tab_system" data-toggle="tab"><?php echo $this->lang->line('system') ?></a></li>

                        <li class="pull-left header"> <?php echo $this->lang->line('modules'); ?></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane table-responsive active" id="tab_system">
                            <div class="modules-card-header">Liste des modules systeme</div>
                            <div class="download_label"><?php echo $this->lang->line('modules'); ?></div>
                            <table class="table table-striped table-bordered table-hover example modules-table" cellspacing="0" width="100%">
                                 <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('name'); ?></th>

                                            <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                    </thead>
                                <tbody>
                                     <?php
if (!empty($permissionList)) {
    $count = 1;
    foreach ($permissionList as $system) {
        ?>
                                            <tr>
                                                <td><span class="module-name"><?php echo $system['name']; ?></span></td>
                                                <td class="relative">
                                                    <div class="switch-wrap">
                                                        <span class="state-pill <?php echo ((int)$system['is_active'] === 1) ? 'on' : 'off'; ?>">
                                                            <?php echo ((int)$system['is_active'] === 1) ? 'Actif' : 'Inactif'; ?>
                                                        </span>
                                                        <div class="material-switch pull-right">

                                                        <input id="system<?php echo $system['id'] ?>" name="someSwitchOption001" type="checkbox" data-role="system" class="chk" data-rowid="<?php echo $system['id'] ?>" value="checked" <?php if ($system['is_active'] == 1) {
            echo "checked='checked'";
        }
        ?> />
                                                        <label for="system<?php echo $system['id'] ?>" class="label-success"></label>
                                                        </div>
                                                    </div>

                                                </td>
                                            </tr>
                                            <?php
$count++;
    }
}
?>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane table-responsive" id="tab_students">
                            <div class="download_label"><?php echo $this->lang->line('users'); ?></div>
                            <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                                 <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('name') ?></th>

                                            <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                    </thead>
                                <tbody>
                                     <?php
if (!empty($studentpermissionList)) {
    $count = 1;
    foreach ($studentpermissionList as $student) {
        ?>
                                            <tr>
                                                <td><?php echo $student['name']; ?></td>
                                                <td class="relative">
                                                    <div class="material-switch pull-right">

                                                        <input id="student<?php echo $student['id'] ?>" name="someSwitchOption001" type="checkbox" data-role="student" class="chk" data-rowid="<?php echo $student['id'] ?>" value="checked" <?php if ($student['student'] == 1) {
            echo "checked='checked'";
        }
        ?> />
                                                        <label for="student<?php echo $student['id'] ?>" class="label-success"></label>
                                                    </div>

                                                </td>
                                            </tr>
                                            <?php
$count++;
    }
}
?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane table-responsive" id="tab_parent">
                            <div class="download_label"><?php echo $this->lang->line('users'); ?></div>
                           <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                                 <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('name') ?></th>
                                            <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                    </thead>
                                <tbody>
                                     <?php
if (!empty($parentpermissionList)) {
    $count = 1;
    foreach ($parentpermissionList as $parent) {
        ?>
                                            <tr>
                                                <td><?php echo $parent['name']; ?></td>
                                                <td class="relative">
                                                    <div class="material-switch pull-right">

                                                        <input id="parent<?php echo $parent['id'] ?>" name="someSwitchOption001" type="checkbox" data-role="parent" class="chk" data-rowid="<?php echo $parent['id'] ?>" value="checked" <?php if ($parent['parent'] == 1) {
            echo "checked='checked'";
        }
        ?> />
                                                        <label for="parent<?php echo $parent['id'] ?>" class="label-success"></label>
                                                    </div>

                                                </td>
                                            </tr>
                                            <?php
$count++;
    }
}
?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>
            </div>
        </div>
    </section>
</div>


<script type="text/javascript">
    $(document).ready(function () {

        $(document).on('click', '.chk', function () {
            var checked = $(this).is(':checked');
            var rowid = $(this).data('rowid');
            var role = $(this).data('role');

            if (checked) {
                if (!confirm('<?php echo $this->lang->line('are_you_sure'); ?>')) {
                    $(this).removeAttr('checked');

                } else {
                    var status = "1";
                    if(role=='system'){
                         changeStatus(rowid, status, role);

                    }else if(role=='parent'){

                        changeParentStatus(rowid, status, role);

                    }else if(role=='student'){

                        changeStudentStatus(rowid, status, role);

                    }


                }

            } else if (!confirm('<?php echo $this->lang->line('are_you_sure'); ?>')) {
                $(this).prop("checked", true);
            } else {
                var status = "0";
                if(role=='system'){
                         changeStatus(rowid, status, role);

                    }else if(role=='parent'){

                        changeParentStatus(rowid, status, role);

                    }else if(role=='student'){

                        changeStudentStatus(rowid, status, role);

                    }
            }
        });
    });

     function changeStatus(rowid, status, role) {

        var base_url = '<?php echo base_url() ?>';

        $.ajax({
            type: "POST",
            url: base_url + "admin/module/changeStatus",
            data: {'id': rowid, 'status': status, 'role': role},
            dataType: "json",
            success: function (data) {
                successMsg(data.msg);
                window.location.reload();
            }
        });
    }

function changeStudentStatus(rowid, status, role) {

        var base_url = '<?php echo base_url() ?>';

        $.ajax({
            type: "POST",
            url: base_url + "admin/module/changeStudentStatus",
            data: {'id': rowid, 'status': status, 'role': role},
            dataType: "json",
            success: function (data) {
                successMsg(data.msg);
                window.location.reload();
            }
        });
    }

    function changeParentStatus(rowid, status, role) {

        var base_url = '<?php echo base_url() ?>';

        $.ajax({
            type: "POST",
            url: base_url + "admin/module/changeStudentStatus",
            data: {'id': rowid, 'status': status, 'role': role},
            dataType: "json",
            success: function (data) {
                successMsg(data.msg);
                window.location.reload();
            }
        });
    }

</script>