<div class="content-wrapper">
    <section class="content-header">
        <div class="header-content">
            <h1>
                <i class="fa fa-shield-alt"></i>
                <?php echo $this->lang->line('system_settings'); ?>
            </h1>
            <div class="header-actions">
                <span class="role-badge">
                    <i class="fa fa-user-tag"></i>
                    <?php echo $role['name'] ?>
                </span>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-permissions">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fa fa-lock-open mr-2"></i>
                                <?php echo $this->lang->line('assign_permission'); ?>
                            </h3>
                            <!--<div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </div>-->
                        </div>

                        <form id="form1" action="<?php echo site_url('admin/roles/permission/' . $role['id']) ?>" method="post" accept-charset="utf-8">
                            <div class="card-body">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <input type="hidden" name="role_id" value="<?php echo $role['id'] ?>"/>

                                <!-- Quick Actions -->
                                <div class="quick-actions mb-4">
                                    <span class="quick-actions-label">Actions rapides :</span>
                                    <button type="button" class="btn btn-sm btn-outline-success" id="selectAll">
                                        <i class="fa fa-check-double"></i> Tout sélectionner
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="deselectAll">
                                        <i class="fa fa-times"></i> Tout désélectionner
                                    </button>
                                </div>

                                <!-- Permissions Table -->
                                <div class="permissions-container">
                                    <table class="table permissions-table">
                                        <thead>
                                        <tr>
                                            <th width="25%"><?php echo $this->lang->line('module'); ?></th>
                                            <th width="30%"><?php echo $this->lang->line('feature'); ?></th>
                                            <th width="11%" class="text-center">
                                                <div class="permission-header">
                                                    <i class="fa fa-eye"></i>
                                                    <span><?php echo $this->lang->line('view'); ?></span>
                                                </div>
                                            </th>
                                            <th width="11%" class="text-center">
                                                <div class="permission-header">
                                                    <i class="fa fa-plus-circle"></i>
                                                    <span><?php echo $this->lang->line('add'); ?></span>
                                                </div>
                                            </th>
                                            <th width="11%" class="text-center">
                                                <div class="permission-header">
                                                    <i class="fa fa-edit"></i>
                                                    <span><?php echo $this->lang->line('edit'); ?></span>
                                                </div>
                                            </th>

                                            <th width="11%" class="text-center">
                                                <div class="permission-header">
                                                    <i class="fa fa-validate"></i>
                                                    <span>Valider</span>
                                                </div>
                                            </th>

                                            <th width="11%" class="text-center">
                                                <div class="permission-header">
                                                    <i class="fa fa-trash-alt"></i>
                                                    <span><?php echo $this->lang->line('delete'); ?></span>
                                                </div>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($role_permission as $key => $value) { ?>
                                            <!-- Module Row -->
                                            <tr class="module-row">
                                                <td class="module-name">
                                                    <div class="module-title">
                                                        <i class="fa fa-cubes"></i>
                                                        <?php echo $value->name ?>
                                                    </div>
                                                </td>

                                                <?php if (!empty($value->permission_category)) { ?>
                                                    <!-- First Feature -->
                                                    <td class="feature-name">
                                                        <input type="hidden" name="per_cat[]" value="<?php echo $value->permission_category[0]->id; ?>" />
                                                        <input type="hidden" name="<?php echo "roles_permissions_id_" . $value->permission_category[0]->id; ?>" value="<?php echo $value->permission_category[0]->roles_permissions_id; ?>" />
                                                        <span class="feature-label">
                                                                <i class="fa fa-angle-right"></i>
                                                                <?php echo $value->permission_category[0]->name ?>
                                                            </span>
                                                    </td>

                                                    <!-- Permission Checkboxes -->
                                                    <td class="text-center">
                                                        <?php if ($value->permission_category[0]->enable_view == 1) { ?>
                                                            <label class="permission-checkbox">
                                                                <input type="checkbox"
                                                                       name="<?php echo "can_view-perm_" . $value->permission_category[0]->id; ?>"
                                                                       value="<?php echo $value->permission_category[0]->id; ?>"
                                                                    <?php echo set_checkbox("can_view-perm_" . $value->permission_category[0]->id, $value->permission_category[0]->id, ($value->permission_category[0]->can_view == 1) ? TRUE : FALSE); ?>>
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        <?php } ?>
                                                    </td>

                                                    <td class="text-center">
                                                        <?php if ($value->permission_category[0]->enable_add == 1) { ?>
                                                            <label class="permission-checkbox">
                                                                <input type="checkbox"
                                                                       name="<?php echo "can_add-perm_" . $value->permission_category[0]->id; ?>"
                                                                       value="<?php echo $value->permission_category[0]->id; ?>"
                                                                    <?php echo set_checkbox("can_add-perm_" . $value->permission_category[0]->id, $value->permission_category[0]->id, ($value->permission_category[0]->can_add == 1) ? TRUE : FALSE); ?>>
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        <?php } ?>
                                                    </td>

                                                    <td class="text-center">
                                                        <?php if ($value->permission_category[0]->enable_edit == 1) { ?>
                                                            <label class="permission-checkbox">
                                                                <input type="checkbox"
                                                                       name="<?php echo "can_edit-perm_" . $value->permission_category[0]->id; ?>"
                                                                       value="<?php echo $value->permission_category[0]->id; ?>"
                                                                    <?php echo set_checkbox("can_edit-perm_" . $value->permission_category[0]->id, $value->permission_category[0]->id, ($value->permission_category[0]->can_edit == 1) ? TRUE : FALSE); ?>>
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        <?php } ?>
                                                    </td>

                                                    <td class="text-center">
                                                        <?php if ($value->permission_category[0]->enable_validate == 1) { ?>
                                                            <label class="permission-checkbox">
                                                                <input type="checkbox"
                                                                       name="<?php echo "can_validate-perm_" . $value->permission_category[0]->id; ?>"
                                                                       value="<?php echo $value->permission_category[0]->id; ?>"
                                                                    <?php echo set_checkbox("can_validate-perm_" . $value->permission_category[0]->id, $value->permission_category[0]->id, ($value->permission_category[0]->can_validate == 1) ? TRUE : FALSE); ?>>
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        <?php } ?>
                                                    </td>

                                                    <td class="text-center">
                                                        <?php if ($value->permission_category[0]->enable_delete == 1) { ?>
                                                            <label class="permission-checkbox">
                                                                <input type="checkbox"
                                                                       name="<?php echo "can_delete-perm_" . $value->permission_category[0]->id; ?>"
                                                                       value="<?php echo $value->permission_category[0]->id; ?>"
                                                                    <?php echo set_checkbox("can_delete-perm_" . $value->permission_category[0]->id, $value->permission_category[0]->id, ($value->permission_category[0]->can_delete == 1) ? TRUE : FALSE); ?>>
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        <?php } ?>
                                                    </td>
                                                <?php } else { ?>
                                                    <td colspan="5" class="text-muted text-center">Aucune permission disponible</td>
                                                <?php } ?>
                                            </tr>

                                            <!-- Additional Features -->
                                            <?php if (!empty($value->permission_category) && count($value->permission_category) > 1) {
                                                unset($value->permission_category[0]);
                                                foreach ($value->permission_category as $new_feature_key => $new_feature_value) { ?>
                                                    <tr class="feature-row">
                                                        <td></td>
                                                        <td class="feature-name sub-feature">
                                                            <input type="hidden" name="per_cat[]" value="<?php echo $new_feature_value->id; ?>" />
                                                            <input type="hidden" name="<?php echo "roles_permissions_id_" . $new_feature_value->id; ?>" value="<?php echo $new_feature_value->roles_permissions_id; ?>" />
                                                            <span class="feature-label">
                                                                    <i class="fa fa-angle-right"></i>
                                                                    <?php echo $new_feature_value->name ?>
                                                                </span>
                                                        </td>

                                                        <td class="text-center">
                                                            <?php if ($new_feature_value->enable_view == 1) { ?>
                                                                <label class="permission-checkbox">
                                                                    <input type="checkbox"
                                                                           name="<?php echo "can_view-perm_" . $new_feature_value->id; ?>"
                                                                           value="<?php echo $new_feature_value->id; ?>"
                                                                        <?php echo set_checkbox("can_view-perm_" . $new_feature_value->id, $new_feature_value->id, ($new_feature_value->can_view == 1) ? TRUE : FALSE); ?>>
                                                                    <span class="checkmark"></span>
                                                                </label>
                                                            <?php } ?>
                                                        </td>

                                                        <td class="text-center">
                                                            <?php if ($new_feature_value->enable_add == 1) { ?>
                                                                <label class="permission-checkbox">
                                                                    <input type="checkbox"
                                                                           name="<?php echo "can_add-perm_" . $new_feature_value->id; ?>"
                                                                           value="<?php echo $new_feature_value->id; ?>"
                                                                        <?php echo set_checkbox("can_add-perm_" . $new_feature_value->id, $new_feature_value->id, ($new_feature_value->can_add == 1) ? TRUE : FALSE); ?>>
                                                                    <span class="checkmark"></span>
                                                                </label>
                                                            <?php } ?>
                                                        </td>

                                                        <td class="text-center">
                                                            <?php if ($new_feature_value->enable_edit == 1) { ?>
                                                                <label class="permission-checkbox">
                                                                    <input type="checkbox"
                                                                           name="<?php echo "can_edit-perm_" . $new_feature_value->id; ?>"
                                                                           value="<?php echo $new_feature_value->id; ?>"
                                                                        <?php echo set_checkbox("can_edit-perm_" . $new_feature_value->id, $new_feature_value->id, ($new_feature_value->can_edit == 1) ? TRUE : FALSE); ?>>
                                                                    <span class="checkmark"></span>
                                                                </label>
                                                            <?php } ?>
                                                        </td>

                                                        <td class="text-center">
                                                            <?php if ($new_feature_value->enable_validate == 1) { ?>
                                                                <label class="permission-checkbox">
                                                                    <input type="checkbox"
                                                                           name="<?php echo "can_validate-perm_" . $new_feature_value->id; ?>"
                                                                           value="<?php echo $new_feature_value->id; ?>"
                                                                        <?php echo set_checkbox("can_validate-perm_" . $new_feature_value->id, $new_feature_value->id, ($new_feature_value->can_validate == 1) ? TRUE : FALSE); ?>>
                                                                    <span class="checkmark"></span>
                                                                </label>
                                                            <?php } ?>
                                                        </td>

                                                        <td class="text-center">
                                                            <?php if ($new_feature_value->enable_delete == 1) { ?>
                                                                <label class="permission-checkbox">
                                                                    <input type="checkbox"
                                                                           name="<?php echo "can_delete-perm_" . $new_feature_value->id; ?>"
                                                                           value="<?php echo $new_feature_value->id; ?>"
                                                                        <?php echo set_checkbox("can_delete-perm_" . $new_feature_value->id, $new_feature_value->id, ($new_feature_value->can_delete == 1) ? TRUE : FALSE); ?>>
                                                                    <span class="checkmark"></span>
                                                                </label>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save mr-2"></i>
                                    <?php echo $this->lang->line('save'); ?>
                                </button>
                                <a href="<?php echo site_url('admin/roles') ?>" class="btn btn-secondary">
                                    <i class="fa fa-times mr-2"></i>
                                    Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- CSS personnalisé -->
<style>
    :root {
        --primary-color: #3498db;
        --success-color: #2ecc71;
        --danger-color: #e74c3c;
        --warning-color: #f39c12;
        --dark-color: #2c3e50;
        --light-color: #ecf0f1;
        --border-radius: 8px;
        --box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .content-header {
        background: linear-gradient(135deg, var(--dark-color), #34495e);
        padding: 20px 25px;
        margin-bottom: 25px;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-content h1 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 300;
        color: white;
    }

    .header-content h1 i {
        margin-right: 10px;
        color: var(--warning-color);
    }

    .role-badge {
        background: rgba(255,255,255,0.15);
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 0.95rem;
        font-weight: 500;
        color: white;
        border: 1px solid rgba(255,255,255,0.3);
    }

    .role-badge i {
        margin-right: 8px;
        color: var(--warning-color);
    }

    .card-permissions {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        overflow: hidden;
    }

    .card-permissions .card-header {
        background: white;
        border-bottom: 2px solid var(--light-color);
        padding: 15px 25px;
    }

    .card-permissions .card-header h3 {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 500;
        color: var(--dark-color);
    }

    .card-permissions .card-header h3 i {
        color: var(--primary-color);
    }

    .quick-actions {
        background: #f8f9fa;
        padding: 12px 20px;
        border-radius: var(--border-radius);
        border: 1px solid #dee2e6;
    }

    .quick-actions-label {
        font-weight: 500;
        color: var(--dark-color);
        margin-right: 15px;
    }

    .permissions-container {
        border: 1px solid #dee2e6;
        border-radius: var(--border-radius);
        overflow: auto;
        max-height: 600px;
    }

    .permissions-table {
        margin-bottom: 0;
        min-width: 800px;
    }

    .permissions-table thead th {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        padding: 15px 12px;
        font-weight: 600;
        color: var(--dark-color);
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .permission-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
    }

    .permission-header i {
        font-size: 1.1rem;
        color: var(--primary-color);
    }

    .module-row {
        background: #f8f9fa;
    }

    .module-row .module-name {
        font-weight: 600;
        color: var(--dark-color);
    }

    .module-title i {
        margin-right: 8px;
        color: var(--warning-color);
    }

    .feature-row {
        background: white;
    }

    .feature-row:hover {
        background: #f8f9fa;
    }

    .feature-name {
        padding-left: 35px !important;
        color: #495057;
    }

    .sub-feature {
        padding-left: 50px !important;
        color: #6c757d;
    }

    .feature-label i {
        margin-right: 8px;
        color: #adb5bd;
        font-size: 0.9rem;
    }

    .permission-checkbox {
        display: inline-block;
        position: relative;
        cursor: pointer;
        margin: 0;
        width: 24px;
        height: 24px;
    }

    .permission-checkbox input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 22px;
        width: 22px;
        background-color: #fff;
        border: 2px solid #dee2e6;
        border-radius: 5px;
        transition: all 0.2s ease;
    }

    .permission-checkbox:hover input ~ .checkmark {
        border-color: var(--primary-color);
        background-color: #f0f7ff;
    }

    .permission-checkbox input:checked ~ .checkmark {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    .permission-checkbox input:checked ~ .checkmark:after {
        display: block;
    }

    .permission-checkbox .checkmark:after {
        left: 7px;
        top: 3px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .card-footer {
        background: white;
        border-top: 2px solid var(--light-color);
        padding: 15px 25px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), #2980b9);
        border: none;
        padding: 10px 25px;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
    }

    .btn-secondary {
        background: #95a5a6;
        border: none;
        padding: 10px 25px;
        border-radius: 50px;
        font-weight: 500;
        color: white;
        margin-left: 10px;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
        color: white;
    }

    /* Scrollbar personnalisée */
    .permissions-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .permissions-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .permissions-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .permissions-container::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .quick-actions {
            text-align: center;
        }

        .quick-actions .btn {
            margin: 5px;
        }
    }
</style>

<!-- JavaScript pour les actions rapides -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllBtn = document.getElementById('selectAll');
        const deselectAllBtn = document.getElementById('deselectAll');
        const checkboxes = document.querySelectorAll('.permission-checkbox input[type="checkbox"]');

        selectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        });

        deselectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        });
    });
</script>