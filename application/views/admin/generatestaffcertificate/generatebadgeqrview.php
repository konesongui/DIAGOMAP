<div class="content-wrapper">
    <section class="content">
        <?php if ($this->session->flashdata('msg')) { ?>
            <?php echo $this->session->flashdata('msg') ?>
        <?php } ?>

        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-id-badge"></i> Generateur de badges QR</h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo site_url('admin/generatestaffcertificate'); ?>" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> Vue classique
                            </a>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="alert alert-info" style="margin-bottom: 20px;">
                            Selectionnez un role, choisissez les employes, puis cliquez sur "Imprimer badges QR design".
                        </div>

                        <form role="form" action="<?php echo site_url('admin/generatestaffcertificate/searchbadgeqr'); ?>" method="post">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('role'); ?> <small class="req">*</small></label>
                                        <select id="role_id" name="role_id" class="form-control" required>
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php if (!empty($staffRolelist)) { ?>
                                                <?php foreach ($staffRolelist as $role_item) { ?>
                                                    <option value="<?php echo $role_item['id']; ?>" <?php echo (set_value('role_id', isset($role_id) ? $role_id : '') == $role_item['id']) ? 'selected' : ''; ?>>
                                                        <?php echo $role_item['type']; ?>
                                                    </option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('role_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group" style="padding-top: 24px;">
                                        <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm">
                                            <i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <?php if (isset($resultlist)) { ?>
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-users"></i> Liste des employes</h3>
                            <button class="btn btn-success btn-sm pull-right" type="button" id="printDesignBadges">
                                <i class="fa fa-print"></i> Imprimer badges QR design
                            </button>
                        </div>

                        <div class="box-body table-responsive">
                            <table class="table table-striped table-bordered table-hover" width="100%">
                                <thead>
                                    <tr>
                                        <th width="40"><input type="checkbox" id="select_all_badge_qr" /></th>
                                        <th><?php echo $this->lang->line('staff'); ?> ID</th>
                                        <th><?php echo $this->lang->line('staff'); ?> <?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('role'); ?></th>
                                        <th><?php echo $this->lang->line('designation'); ?></th>
                                        <th><?php echo $this->lang->line('department'); ?></th>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($resultlist)) { ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Aucun employe trouve.</td>
                                        </tr>
                                    <?php } else { ?>
                                        <?php foreach ($resultlist as $staff_value) { ?>
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" class="badge-qr-checkbox" data-staff_id="<?php echo $staff_value['id']; ?>" name="check_badge_qr" value="<?php echo $staff_value['id']; ?>">
                                                </td>
                                                <td><?php echo $staff_value['employee_id']; ?></td>
                                                <td>
                                                    <a href="<?php echo base_url('admin/staff/profile/' . $staff_value['id']); ?>">
                                                        <?php echo $staff_value['name'] . ' ' . $staff_value['surname']; ?>
                                                    </a>
                                                </td>
                                                <td><?php echo $staff_value['user_type']; ?></td>
                                                <td><?php echo $staff_value['designation']; ?></td>
                                                <td><?php echo $staff_value['department']; ?></td>
                                                <td><?php echo $staff_value['contact_no']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#select_all_badge_qr').on('click', function () {
            $('.badge-qr-checkbox').prop('checked', this.checked);
        });

        $('.badge-qr-checkbox').on('click', function () {
            $('#select_all_badge_qr').prop('checked', $('.badge-qr-checkbox:checked').length === $('.badge-qr-checkbox').length);
        });

        $('#printDesignBadges').on('click', function () {
            var selected = [];
            $.each($('.badge-qr-checkbox:checked'), function () {
                selected.push({staff_id: $(this).data('staff_id')});
            });

            if (selected.length === 0) {
                alert('<?php echo $this->lang->line('no_record_selected'); ?>');
                return;
            }

            $.ajax({
                url: '<?php echo site_url('admin/generatestaffcertificate/generatebadgeqr'); ?>',
                type: 'post',
                dataType: 'html',
                data: {
                    data: JSON.stringify(selected)
                },
                success: function (response) {
                    printBadgePreview(response);
                }
            });
        });
    });

    function printBadgePreview(data)
    {
        var frame = $('<iframe />');
        frame[0].name = 'badgeFrame';
        $('body').append(frame);

        var frameDoc = frame[0].contentWindow ? frame[0].contentWindow : frame[0].contentDocument.document ? frame[0].contentDocument.document : frame[0].contentDocument;
        frameDoc.document.open();
        frameDoc.document.write('<html><head><title>Badges QR</title></head><body>');
        frameDoc.document.write(data);
        frameDoc.document.write('</body></html>');
        frameDoc.document.close();

        setTimeout(function () {
            window.frames['badgeFrame'].focus();
            window.frames['badgeFrame'].print();
            frame.remove();
        }, 500);
    }
</script>
