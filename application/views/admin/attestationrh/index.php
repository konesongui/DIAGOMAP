<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card" style="border:none; border-radius:18px; box-shadow:0 18px 45px rgba(15,23,42,0.08); overflow:hidden;">
            <div class="card-header" style="background:linear-gradient(135deg, #1b4f80 0%, #2d6ea8 100%); color:#fff; border-bottom:none; padding:20px 25px;">
                <div class="pull-left">
                    <h3 style="margin:0; font-weight:700;">
                        <i class="fa fa-file-text-o"></i> Attestations RH
                    </h3>
                </div>
                <div class="pull-right">
                    <a href="<?php echo base_url('admin/admin/rh'); ?>" class="btn btn-default" style="border-radius:30px; background:rgba(255,255,255,0.12); color:#fff; border:1px solid rgba(255,255,255,0.25);">
                        <i class="fa fa-arrow-left"></i> Retour RH
                    </a>
                </div>
            </div>
            <div class="card-body" style="padding:25px; background:#f8fbff;">
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success" style="margin-bottom:20px;">
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger" style="margin-bottom:20px;">
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <div class="row" style="margin-bottom:22px;">
                    <div class="col-md-12">
                        <form id="generate-attestation-form" method="get" action="javascript:void(0);" target="_blank" style="display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; background:#fff; border:1px solid #dfe7f3; border-radius:14px; padding:18px; box-shadow:0 8px 22px rgba(15,23,42,0.04);">
                            <div class="form-group" style="flex:1 1 260px; margin-bottom:0;">
                                <label for="staff_id" style="display:block; margin-bottom:8px; font-weight:600; color:#1b4f80;">Employé</label>
                                <select id="staff_id" name="staff_id" class="form-control" required style="min-height:42px; border-radius:10px; border-color:#dfe7f3;">
                                    <option value="">Sélectionner un employé</option>
                                    <?php if (!empty($staffs)) : ?>
                                        <?php foreach ($staffs as $staff) : ?>
                                            <?php $name = trim((string) (($staff['name'] ?? '') . ' ' . ($staff['surname'] ?? ''))); ?>
                                            <?php $name = $name !== '' ? $name : ($staff['name'] ?? 'Employé'); ?>
                                            <option value="<?php echo (int) ($staff['id'] ?? 0); ?>"><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($staff['employee_id'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>)</option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group" style="flex:1 1 260px; margin-bottom:0;">
                                <label for="type" style="display:block; margin-bottom:8px; font-weight:600; color:#1b4f80;">Type d'attestation</label>
                                <select id="type" name="type" class="form-control" required style="min-height:42px; border-radius:10px; border-color:#dfe7f3;">
                                    <?php foreach ($document_types as $code => $doc) : ?>
                                        <option value="<?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($doc['label'], ENT_QUOTES, 'UTF-8'); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" style="border-radius:12px; padding:11px 20px; min-width:160px; font-weight:700;">
                                <i class="fa fa-file-text-o"></i> Générer
                            </button>
                        </form>

                        <script>
                        (function () {
                            var form = document.getElementById('generate-attestation-form');
                            if (!form) {
                                return;
                            }

                            form.addEventListener('submit', function (event) {
                                event.preventDefault();

                                var staffId = document.getElementById('staff_id');
                                var typeEl = document.getElementById('type');
                                if (!staffId || !typeEl) {
                                    return;
                                }

                                var selectedStaff = parseInt(staffId.value, 10);
                                var selectedType = typeEl.value;
                                if (!selectedStaff || !selectedType) {
                                    alert('Veuillez sélectionner un employé et un type d’attestation.');
                                    return;
                                }

                                var baseUrl = '<?php echo base_url('admin/attestationrh/print_document'); ?>';
                                var url = baseUrl + '/' + selectedStaff + '/' + selectedType;
                                var popup = window.open(url, 'attestation_rh_popup', 'width=1200,height=900,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes');
                                if (popup) {
                                    popup.focus();
                                }
                            });
                        })();
                        </script>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" style="background:#fff; border-radius:12px; overflow:hidden;">
                        <thead style="background:#eef4ff;">
                            <tr>
                                <th>#</th>
                                <th>Employé</th>
                                <th>Matricule</th>
                                <th>Département</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($staffs)) : ?>
                                <?php foreach ($staffs as $index => $staff) : ?>
                                    <?php $name = trim((string) (($staff['name'] ?? '') . ' ' . ($staff['surname'] ?? ''))); ?>
                                    <?php $name = $name !== '' ? $name : ($staff['name'] ?? 'Employé'); ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></strong>
                                            <?php if (!empty($staff['designation'])) : ?><br><small><?php echo htmlspecialchars($staff['designation'], ENT_QUOTES, 'UTF-8'); ?></small><?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($staff['employee_id'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($staff['department'] ?? ($staff['department_name'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($staff['email'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <div class="btn-group" role="group" aria-label="Attestations" style="display:flex; flex-wrap:wrap; gap:6px;">
                                                <?php foreach ($document_types as $code => $doc) : ?>
                                                    <a href="<?php echo base_url('admin/attestationrh/print_document/' . ($staff['id'] ?? 0) . '/' . $code); ?>" target="_blank" class="btn btn-sm btn-primary" title="Imprimer <?php echo $doc['label']; ?>">
                                                        <i class="fa <?php echo $doc['icon']; ?>"></i>
                                                    </a>
                                                    <a href="<?php echo base_url('admin/attestationrh/send_mail/' . ($staff['id'] ?? 0) . '/' . $code); ?>" class="btn btn-sm btn-success" title="Envoyer par mail <?php echo $doc['label']; ?>" onclick="return confirm('Envoyer <?php echo $doc['label']; ?> par e-mail à <?php echo htmlspecialchars($staff['email'] ?? 'cet employé', ENT_QUOTES, 'UTF-8'); ?> ?');">
                                                        <i class="fa fa-envelope"></i>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="text-center" style="padding:30px; color:#6c757d;">
                                        Aucun employé actif trouvé.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
