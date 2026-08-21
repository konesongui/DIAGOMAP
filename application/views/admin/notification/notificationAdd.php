<!-- ============================================================
     PAGE : Formulaire de notification
     DESCRIPTION : Interface moderne pour la création de notifications
     ============================================================ -->

<style>
    :root {
        --primary-dark: #273772;
        --primary-light: #3b82f6;
        --primary-gradient: linear-gradient(135deg, #273772 0%, #1a2558 100%);
        --bg-light: #f8fafc;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --border-light: #e2e8f0;
        --shadow-soft: 0 8px 30px rgba(0, 0, 0, 0.06);
        --shadow-hover: 0 12px 40px rgba(0, 0, 0, 0.1);
        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 8px;
        --transition: all 0.25s ease;
    }

    .content-wrapper {
        background: #f1f5f9;
        padding: 20px 15px;
        min-height: 100vh;
    }

    /* ========================================== */
    /* CARTE PRINCIPALE                           */
    /* ========================================== */
    .card-modern {
        border: none;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        background: #ffffff;
        margin-bottom: 30px;
        overflow: hidden;
    }

    .card-modern .card-header {
        background: var(--primary-gradient);
        padding: 18px 24px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .card-modern .card-header h3 {
        color: #ffffff;
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-modern .card-header h3 i {
        color: #60a5fa;
    }

    .card-modern .card-body {
        padding: 24px;
        background: #fafcff;
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 30px;
        padding: 5px 16px;
        font-size: 13px;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.5);
        text-decoration: none;
    }

    /* ========================================== */
    /* FORMULAIRE                                 */
    /* ========================================== */
    .form-modern .form-group {
        margin-bottom: 20px;
        position: relative;
    }

    .form-modern .form-group label {
        font-weight: 600;
        font-size: 13px;
        color: var(--text-dark);
        margin-bottom: 6px;
        display: block;
        letter-spacing: 0.3px;
    }

    .form-modern .form-group label .text-danger {
        color: #ef4444;
        font-weight: 700;
        margin-left: 2px;
    }

    .form-modern .form-group label .field-icon {
        margin-right: 6px;
        color: var(--primary-light);
        font-size: 14px;
        width: 18px;
        display: inline-block;
    }

    .form-modern .form-control {
        border: 2px solid #e2e8f0;
        border-radius: var(--radius-sm);
        padding: 10px 16px;
        font-size: 14px;
        transition: var(--transition);
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        height: 44px;
        color: var(--text-dark);
        width: 100%;
    }

    .form-modern .form-control:focus {
        border-color: var(--primary-light);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
        outline: none;
    }

    .form-modern .form-control::placeholder {
        color: #a0aec0;
        font-size: 13px;
    }

    .form-modern textarea.form-control {
        height: auto;
        min-height: 200px;
        resize: vertical;
    }

    .form-modern .text-danger {
        font-size: 12px;
        margin-top: 4px;
        display: block;
    }

    /* ========================================== */
    /* CHECKBOX MODERN                            */
    /* ========================================== */
    .checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding-top: 4px;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        padding: 8px 16px;
        background: #f8fafc;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        transition: var(--transition);
        user-select: none;
        flex: 1 1 auto;
        min-width: 120px;
    }

    .checkbox-item:hover {
        border-color: #94a3b8;
        background: #f1f5f9;
    }

    .checkbox-item.active {
        border-color: var(--primary-light);
        background: #eff6ff;
    }

    .checkbox-item input[type="checkbox"] {
        display: none;
    }

    .checkbox-item .checkmark {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
        flex-shrink: 0;
        background: #ffffff;
    }

    .checkbox-item .checkmark i {
        font-size: 12px;
        color: #ffffff;
        opacity: 0;
        transition: var(--transition);
    }

    .checkbox-item.active .checkmark {
        background: var(--primary-light);
        border-color: var(--primary-light);
    }

    .checkbox-item.active .checkmark i {
        opacity: 1;
    }

    .checkbox-item .label-text {
        font-size: 13px;
        font-weight: 500;
        color: var(--text-dark);
    }

    .checkbox-item .label-text .badge-role {
        display: inline-block;
        padding: 1px 10px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 500;
        margin-left: 4px;
    }
    .badge-role.Admin { background: #f3e8ff; color: #7c3aed; }
    .badge-role.Teacher { background: #fef3c7; color: #d97706; }
    .badge-role.Student { background: #dbeafe; color: #1d4ed8; }
    .badge-role.Parent { background: #d1fae5; color: #059669; }
    .badge-role.Accountant { background: #fce4ec; color: #dc2626; }
    .badge-role.Librarian { background: #e0f7fa; color: #0284c7; }

    /* ========================================== */
    /* SECTIONS                                   */
    /* ========================================== */
    .section-box {
        background: #f8fafc;
        padding: 18px;
        border-radius: var(--radius-sm);
        border: 1px solid #eef2f6;
        margin-bottom: 20px;
    }

    .section-box .section-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0 0 16px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-box .section-title i {
        color: var(--primary-light);
    }

    .section-box.blue {
        border-left: 4px solid #3b82f6;
        background: #eff6ff;
    }

    .section-box.green {
        border-left: 4px solid #10b981;
        background: #f0fdf4;
    }

    .section-box.purple {
        border-left: 4px solid #8b5cf6;
        background: #f5f3ff;
    }

    /* ========================================== */
    /* BOUTONS                                    */
    /* ========================================== */
    .btn-modern {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        border-radius: var(--radius-sm);
        font-weight: 500;
        font-size: 14px;
        transition: var(--transition);
        border: none;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-modern.btn-primary {
        background: var(--primary-gradient);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(39, 55, 114, 0.25);
    }

    .btn-modern.btn-primary:hover {
        box-shadow: 0 6px 20px rgba(39, 55, 114, 0.35);
        transform: translateY(-2px);
    }

    .btn-modern.btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-modern.btn-secondary:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }

    .btn-modern.btn-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .btn-modern.btn-warning:hover {
        background: #fde68a;
        transform: translateY(-2px);
    }

    .btn-modern:active {
        transform: scale(0.97);
    }

    /* ========================================== */
    /* RESPONSIVE                                 */
    /* ========================================== */
    @media (max-width: 768px) {
        .card-modern .card-header {
            flex-direction: column;
            align-items: stretch;
        }

        .checkbox-group {
            flex-direction: column;
        }

        .checkbox-item {
            width: 100%;
            min-width: unset;
        }

        .btn-modern {
            width: 100%;
            justify-content: center;
        }

        .form-modern .form-group {
            margin-bottom: 16px;
        }
    }

    @media (max-width: 480px) {
        .card-modern .card-body {
            padding: 16px;
        }
        .section-box {
            padding: 14px;
        }
        .form-modern .form-control {
            font-size: 13px;
            padding: 8px 12px;
            height: 38px;
        }
    }

    /* ========================================== */
    /* ANIMATIONS                                 */
    /* ========================================== */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card-modern {
        animation: fadeInUp 0.4s ease forwards;
    }

    /* ========================================== */
    /* WYSIWYG EDITOR                             */
    /* ========================================== */
    .bootstrap-wysihtml5-insert-link-modal .modal-dialog {
        z-index: 1050;
    }

    .wysihtml5-sandbox {
        border-radius: 0 0 var(--radius-sm) var(--radius-sm) !important;
        border: 2px solid #e2e8f0 !important;
        border-top: none !important;
        min-height: 200px;
    }

    .wysihtml5-toolbar {
        background: #f8fafc;
        border-radius: var(--radius-sm) var(--radius-sm) 0 0;
        border: 2px solid #e2e8f0;
        border-bottom: none;
        padding: 8px 12px;
    }

    .wysihtml5-toolbar .btn {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 4px 10px;
        font-size: 12px;
        color: #475569;
        transition: var(--transition);
    }

    .wysihtml5-toolbar .btn:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    .wysihtml5-toolbar .btn.active {
        background: #eff6ff;
        border-color: var(--primary-light);
        color: var(--primary-light);
    }

    .bootstrap-wysihtml5-insert-link-modal .modal-content {
        border-radius: 12px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .bootstrap-wysihtml5-insert-link-modal .modal-header {
        background: linear-gradient(135deg, #1e293b, #0f172a);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 16px 20px;
    }

    .bootstrap-wysihtml5-insert-link-modal .modal-header .close {
        color: white;
        opacity: 0.8;
    }

    .bootstrap-wysihtml5-insert-link-modal .modal-header .close:hover {
        opacity: 1;
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">

                <!-- ========================================== -->
                <!-- CARTE PRINCIPALE                           -->
                <!-- ========================================== -->
                <div class="card-modern">
                    <div class="card-header">
                        <h3>
                            <i class="fa fa-bullhorn"></i>
                            <?php echo isset($edit_id) ? 'Modifier la notification' : 'Nouvelle notification'; ?>
                        </h3>
                        <div class="d-flex align-items-center gap-3" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url(); ?>admin/notification" class="btn-back" title="Retour à la liste">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- ===== MESSAGES FLASH ===== -->
                        <?php if ($this->session->flashdata('msg')) : ?>
                            <!--<div class="alert alert-success alert-dismissible fade in">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <i class="fa fa-check-circle"></i> <?php echo $this->session->flashdata('msg'); ?>
                            </div>-->
                        <?php endif; ?>
                        <?php if ($this->session->flashdata('error')) : ?>
                            <div class="alert alert-danger alert-dismissible fade in">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <i class="fa fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
                            </div>
                        <?php endif; ?>

                        <!-- ===== FORMULAIRE ===== -->
                        <form id="form1" action="<?php echo base_url(); ?>admin/notification/add" method="post" accept-charset="utf-8" class="form-modern">
                            <?php echo $this->customlib->getCSRF(); ?>

                            <div class="row">
                                <!-- ===== COLONNE GAUCHE ===== -->
                                <div class="col-md-8">
                                    <!-- Section: Informations principales -->
                                    <div class="section-box">
                                        <div class="section-title">
                                            <i class="fa fa-info-circle"></i>
                                            Informations de la notification
                                        </div>

                                        <div class="form-group">
                                            <label for="title">
                                                <i class="fa fa-tag field-icon"></i>
                                                <?php echo $this->lang->line('title'); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input autofocus id="title" name="title" type="text" class="form-control"
                                                   placeholder="Saisissez le titre de la notification..."
                                                   value="<?php echo set_value('title', isset($notification['title']) ? $notification['title'] : ''); ?>" />
                                            <span class="text-danger"><?php echo form_error('title'); ?></span>
                                        </div>

                                        <div class="form-group">
                                            <label for="compose-textarea">
                                                <i class="fa fa-file-text-o field-icon"></i>
                                                <?php echo $this->lang->line('message'); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <textarea id="compose-textarea" name="message" class="form-control" style="min-height: 250px;">
                                                <?php echo set_value('message', isset($notification['message']) ? $notification['message'] : ''); ?>
                                            </textarea>
                                            <span class="text-danger"><?php echo form_error('message'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- ===== COLONNE DROITE ===== -->
                                <div class="col-md-4">
                                    <!-- Section: Dates -->
                                    <div class="section-box blue">
                                        <div class="section-title">
                                            <i class="fa fa-calendar"></i>
                                            Planification
                                        </div>

                                        <div class="form-group">
                                            <label for="date">
                                                <i class="fa fa-calendar field-icon"></i>
                                                Date de la notification
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input id="date" name="date" type="text" class="form-control date"
                                                   placeholder="JJ/MM/AAAA"
                                                   value="<?php echo set_value('date', isset($notification['date']) ? date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($notification['date'])) : ''); ?>" />
                                            <span class="text-danger"><?php echo form_error('date'); ?></span>
                                        </div>

                                        <div class="form-group">
                                            <label for="publish_date">
                                                <i class="fa fa-clock-o field-icon"></i>
                                                Date de publication
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input id="publish_date" name="publish_date" type="text" class="form-control date"
                                                   placeholder="JJ/MM/AAAA"
                                                   value="<?php echo set_value('publish_date', isset($notification['publish_date']) ? date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($notification['publish_date'])) : ''); ?>" />
                                            <span class="text-danger"><?php echo form_error('publish_date'); ?></span>
                                        </div>
                                    </div>

                                    <!-- Section: Destinataires -->
                                    <div class="section-box green">
                                        <div class="section-title">
                                            <i class="fa fa-users"></i>
                                            Destinataires
                                            <span class="text-danger">*</span>
                                        </div>

                                        <div class="form-group" style="margin-bottom: 0;">
                                            <div class="checkbox-group">
                                                <?php foreach ($roles as $role_key => $role_value) :
                                                    $userdata = $this->customlib->getUserData();
                                                    $role_id = $userdata["role_id"];
                                                    $checked = false;
                                                    if (isset($notification['visible_roles']) && is_array($notification['visible_roles'])) {
                                                        $checked = in_array($role_value['id'], $notification['visible_roles']);
                                                    } elseif ($role_value["id"] == $role_id) {
                                                        $checked = true;
                                                    }
                                                    ?>
                                                    <div class="checkbox-item <?php echo $checked ? 'active' : ''; ?>"
                                                         onclick="toggleCheckbox(this)">
                                                        <input type="checkbox" name="visible[]" value="<?php echo $role_value['id']; ?>"
                                                            <?php echo $checked ? 'checked' : ''; ?>>
                                                        <span class="checkmark"><i class="fa fa-check"></i></span>
                                                        <span class="label-text">
                                                        <?php echo htmlspecialchars($role_value['name']); ?>
                                                        <span class="badge-role <?php echo htmlspecialchars($role_value['name']); ?>">
                                                            <?php echo htmlspecialchars($role_value['name']); ?>
                                                        </span>
                                                    </span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <span class="text-danger"><?php echo form_error('visible[]'); ?></span>
                                        </div>
                                    </div>

                                    <!-- Section: Actions -->
                                    <div class="section-box purple" style="margin-bottom: 0;">
                                        <div class="section-title">
                                            <i class="fa fa-cog"></i>
                                            Actions
                                        </div>

                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <button type="submit" class="btn-modern btn-primary">
                                                <i class="fa fa-send"></i>
                                                <?php echo isset($edit_id) ? 'Mettre à jour' : 'Publier'; ?>
                                            </button>
                                            <button type="reset" class="btn-modern btn-warning" onclick="resetForm()">
                                                <i class="fa fa-refresh"></i> Réinitialiser
                                            </button>
                                            <a href="<?php echo base_url(); ?>admin/notification" class="btn-modern btn-secondary">
                                                <i class="fa fa-times"></i> Annuler
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- ========================================== -->
<!-- SCRIPTS                                    -->
<!-- ========================================== -->
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>

<script type="text/javascript">
    // ========================================== //
    // TOGGLE CHECKBOX                            //
    // ========================================== //
    function toggleCheckbox(element) {
        // Trouver l'input checkbox à l'intérieur de l'élément
        var checkbox = element.querySelector('input[type="checkbox"]');
        if (checkbox) {
            // Inverser l'état
            checkbox.checked = !checkbox.checked;
            element.classList.toggle('active');

            // Déclencher l'événement change pour la validation
            var evt = document.createEvent('HTMLEvents');
            evt.initEvent('change', false, true);
            checkbox.dispatchEvent(evt);
        }
    }

    // ========================================== //
    // RESET FORM                                 //
    // ========================================== //
    function resetForm() {
        document.getElementById('form1').reset();
        // Désactiver tous les checkbox
        document.querySelectorAll('.checkbox-item').forEach(function(item) {
            item.classList.remove('active');
            var checkbox = item.querySelector('input[type="checkbox"]');
            if (checkbox) {
                checkbox.checked = false;
            }
        });
    }

    // ========================================== //
    // DOCUMENT READY                             //
    // ========================================== //
    $(document).ready(function() {
        // Initialisation de WYSIWYG
        $("#compose-textarea").wysihtml5({
            "font-styles": true,
            "emphasis": true,
            "lists": true,
            "html": false,
            "link": true,
            "image": false,
            "color": false,
            "blockquote": false,
            "size": "sm"
        });

        // Initialisation des datepickers
        $('.date').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });

        // Activation des checkbox au chargement (pour l'édition)
        $('.checkbox-item input[type="checkbox"]:checked').each(function() {
            $(this).closest('.checkbox-item').addClass('active');
        });

        // Empêcher le clic sur le checkbox de se propager
        $('.checkbox-item input[type="checkbox"]').on('click', function(e) {
            e.stopPropagation();
        });

        // Gestion du clic sur le bouton reset
        $("#btnreset").click(function(e) {
            e.preventDefault();
            resetForm();
        });
    });
</script>

<!-- ========================================== -->
<!-- STYLES SUPPLÉMENTAIRES                     -->
<!-- ========================================== -->
<style>
    /* Correction pour le modal de lien WYSIWYG */
    .bootstrap-wysihtml5-insert-link-modal .modal-dialog {
        z-index: 1050;
    }

    .bootstrap-wysihtml5-insert-link-modal .modal-content {
        border-radius: 12px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .bootstrap-wysihtml5-insert-link-modal .modal-header {
        background: linear-gradient(135deg, #1e293b, #0f172a);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 16px 20px;
    }

    .bootstrap-wysihtml5-insert-link-modal .modal-header .close {
        color: white;
        opacity: 0.8;
    }

    .bootstrap-wysihtml5-insert-link-modal .modal-header .close:hover {
        opacity: 1;
    }

    /* Animation pour les checkbox */
    .checkbox-item {
        transition: all 0.3s ease;
    }

    .checkbox-item .checkmark {
        transition: all 0.3s ease;
    }

    .checkbox-item .checkmark i {
        transition: all 0.3s ease;
    }

    .checkbox-item.active .checkmark {
        transform: scale(1.05);
    }

    /* Style pour le bouton reset */
    .btn-modern.btn-warning {
        background: #fef3c7;
        color: #92400e;
        border: none;
    }

    .btn-modern.btn-warning:hover {
        background: #fde68a;
        transform: translateY(-2px);
    }
</style>