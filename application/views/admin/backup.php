<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-gears"></i> <?php echo $this->lang->line('system_settings'); ?>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12-<?php
            if ($this->rbac->hasPrivilege('restore', 'can_view')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-database"></i> <?php echo $this->lang->line('backup_history'); ?></h3>
                        <div class="box-tools pull-right" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                            <span class="label label-info" style="padding:6px 10px; font-size:12px;">Sauvegarde auto : 23:59</span>
                            <form id="form1" action="<?php echo site_url('admin/admin/backup') ?>" method="post" accept-charset="utf-8" role="form" style="display:inline-block; margin:0;">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <?php if ($this->rbac->hasPrivilege('backup', 'can_add')) { ?>
                                    <button class="btn btn-primary btn-sm btn-info" type="submit" name="backup" value="backup"><i class="fa fa-plus-square-o"></i> Sauvegarde manuelle</button>
                                <?php } ?>
                            </form>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if ($this->session->flashdata('msg')) { ?>
                            <?php echo $this->session->flashdata('msg') ?>
                        <?php } ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive mailbox-messages">
                                    <table class="table table-hover table-striped" id="backup-table">
                                        <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('backup_files'); ?></th>
                                            <th class="text-right" colspan="4">
                                                <?php echo $this->lang->line('action'); ?>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $count = 1;
                                        foreach ($dbfileList as $data) {
                                            ?>
                                            <tr>
                                                <td width="80%" class="mailbox-name"><a href="#"> <?php echo $data; ?></a></td>
                                                <td class="mailbox-name">
                                                    <a href="<?php echo site_url('admin/admin/downloadbackup/' . $data) ?>" class="btn btn-success btn-xs" ><i class="fa fa-download"></i> <?php echo $this->lang->line('download'); ?></a>
                                                </td>
                                                <?php if ($this->rbac->hasPrivilege('restore', 'can_view')) { ?>
                                                    <td class="mailbox-name">
                                                        <form class="formrestore" action="<?php echo site_url('admin/admin/backup') ?>" method="post" accept-charset="utf-8" role="form">
                                                            <?php echo $this->customlib->getCSRF(); ?>
                                                            <input type="hidden" name="filename" value="<?php echo $data; ?>">
                                                            <button class="btn btn-primary btn-xs btn-warning" type="submit" name="backup" value="restore"><i class="fa fa-plus-square-o"></i>  <?php echo $this->lang->line('restore'); ?> </button>
                                                        </form>
                                                    </td>
                                                <?php } ?>
                                                <td class="mailbox-name">
                                                    <form class="formdelete" method="post" role="form" action="<?php echo site_url('admin/admin/dropbackup/' . $data); ?>" >
                                                        <?php echo $this->customlib->getCSRF(); ?>
                                                        <?php if ($this->rbac->hasPrivilege('backup', 'can_delete')) { ?>
                                                            <button class="btn btn-primary btn-xs btn-danger" type="submit" name="backup" value="restore"><i class="fa fa-trash"></i>  <?php echo $this->lang->line('delete'); ?></button>
                                                        <?php } ?>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php
                                            $count++;
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
            <?php if ($this->rbac->hasPrivilege('restore', 'can_view')) { ?>
                <div class="col-md-4 col-sm-4" hidden>
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('upload_from_local_directory'); ?></h3>
                        </div>
                        <form role="form" action="<?php echo site_url('admin/admin/backup') ?>" method="post" enctype="multipart/form-data">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="box-body">
                                <input class="filestyle form-control" data-height="30" type="file" name="file" id="exampleInputFile" >
                                <span class="text-danger"><?php echo form_error('file'); ?></span>
                            </div>
                            <div class="box-footer">
                                <button class="btn btn-primary btn-sm pull-right" type="submit" name="backup" value="upload"><i class="fa fa-upload"></i> <?php echo $this->lang->line('upload'); ?></button>
                            </div>
                        </form>
                    </div>

                    <!-- SECTION CRON - AMÉLIORÉE -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('cron_secret_key') ?></h3>
                            <div class="box-tools pull-right">
                                <a class="btn btn-primary btn-sm btn-info" href="<?php echo base_url() . "admin/admin/addCronsecretkey/" . $settinglist[0]['id'] ?>">
                                    <?php echo !empty($settinglist[0]['cron_secret_key']) ? $this->lang->line('regenerate') : $this->lang->line('generate'); ?>
                                </a>
                            </div>
                        </div>
                        <div class="box-body">
                            <?php if (!empty($settinglist[0]['cron_secret_key'])) { ?>
                                <div class="form-group">
                                    <label>URL à appeler par Cron (méthode GET) :</label>
                                    <code id="cron_url" style="display:block; word-break:break-all; background:#f5f5f5; padding:8px; border-radius:4px;">
                                        <?php echo base_url('admin/admin/auto_backup?key=' . $settinglist[0]['cron_secret_key']); ?>
                                    </code>
                                </div>
                                <div class="form-group">
                                    <label>Ligne Cron à ajouter (tous les jours à minuit) :</label>
                                    <code id="cron_line" style="display:block; word-break:break-all; background:#f5f5f5; padding:8px; border-radius:4px;">
                                        0 0 * * * wget -q -O /dev/null "<?php echo base_url('admin/admin/auto_backup?key=' . $settinglist[0]['cron_secret_key']); ?>"
                                    </code>
                                </div>
                                <button type="button" class="btn btn-default btn-sm" onclick="copyToClipboard()">
                                    <i class="fa fa-copy"></i> Copier la ligne Cron
                                </button>
                                <hr>
                                <div>
                                    <a class="hideeye" id="showbtn" onclick="showkey()" href="#"><i class="fa fa-eye"></i></a>
                                    <span id="cronkey" style="display:none;"><?php echo $settinglist[0]['cron_secret_key']; ?></span>
                                    <span style="margin-left:10px;">Clé secrète (masquée)</span>
                                </div>
                            <?php } else { ?>
                                <p class="text-muted">Aucune clé secrète générée. Cliquez sur "Générer" pour activer les sauvegardes automatiques.</p>
                            <?php } ?>
                        </div>
                    </div>
                    <!-- FIN SECTION CRON -->
                </div><!--./col-md-4-->
            <?php } ?>
        </div>
    </section>
</div>

<script type="text/javascript">
    // Initialisation du DataTable
    $(document).ready(function() {
        $('#backup-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
            },
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?php echo $this->lang->line('all'); ?>"]],
            "order": [[0, "desc"]],
            "columnDefs": [
                {
                    "orderable": false,
                    "targets": [1, 2, 3]
                },
                {
                    "className": "text-center",
                    "targets": [1, 2, 3]
                }
            ],
            "responsive": true,
            "autoWidth": false,
            "dom": '<"top"lf>rt<"bottom"ip><"clear">'
        });
    });

    $('#form1').submit(function () {
        var c = confirm("<?php echo $this->lang->line('are_you_sure_want_to_make_current_backup')?>");
        return c;
    });
    $('.formdelete').submit(function () {
        var c = confirm("<?php echo $this->lang->line('are_you_sure_want_to_delete_backup')?>");
        return c;
    });
    $('.formrestore').submit(function () {
        var c = confirm("<?php echo $this->lang->line('are_you_sure_want_to_restore_backup')?>");
        return c;
    });

    function showkey() {
        $("#cronkey").show();
        $("#showbtn").html("<i class='fa fa-eye-slash'></i>");
        $("#showbtn").attr("onclick", "hidekey()");
    }

    function hidekey() {
        $("#cronkey").hide();
        $("#showbtn").html("<i class='fa fa-eye'></i>");
        $("#showbtn").attr("onclick", "showkey()");
    }

    function copyToClipboard() {
        var cronLine = document.getElementById('cron_line').innerText;
        navigator.clipboard.writeText(cronLine).then(function() {
            alert("<?php echo $this->lang->line('cron_line_copied'); ?>");
        }, function(err) {
            console.error('Erreur de copie : ', err);
            alert("Impossible de copier automatiquement. Copiez manuellement.");
        });
    }
</script>