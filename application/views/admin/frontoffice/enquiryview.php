<div class="content-wrapper">
    <!-- En-tête avec style moderne -->
    <section class="content-header">
        <div class="header-container">
            <h1 class="page-title">
                <i class="fa fa-sitemap title-icon"></i>
                <?php echo $this->lang->line('human_resource'); ?>
                <span class="title-badge">Gestion</span>
            </h1>
            <div class="header-actions">
                <span class="current-date"><i class="fa fa-calendar"></i> <?php echo date('d/m/Y'); ?></span>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Carte de recherche élégante -->
                <div class="card card-primary card-search">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-search card-icon"></i>
                            <?php echo $this->lang->line('admission_enquiry'); ?>
                        </h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>


                    </div>

                    <?php if ($this->session->flashdata('msg')): ?>
                        <div class="col-md-12">
                            <?php echo $this->session->flashdata('msg'); ?>
                        </div>
                    <?php endif; ?>

                    <form role="form" action="<?php echo site_url('admin/enquiry'); ?>" method="post">
                        <div class="card-body">
                            <?php echo $this->customlib->getCSRF(); ?>

                            <div class="search-grid">
                                <div class="search-item" hidden>
                                    <div class="form-group">
                                        <label><i class="fa fa-tag"></i> <?php echo $this->lang->line('source'); ?></label>
                                        <select id="source" name="source" class="form-control custom-select">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($sourcelist as $value): ?>
                                                <option value="<?php echo $value['source']; ?>" <?php echo ($value['source'] == $source_select) ? 'selected' : ''; ?>>
                                                    <?php echo $value['source']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('source'); ?></span>
                                    </div>
                                </div>

                                <div class="search-item">
                                    <div class="form-group date-group">
                                        <label><i class="fa fa-calendar-plus-o"></i> <?php echo $this->lang->line('enquiry') . ' ' . $this->lang->line('from') . ' ' . $this->lang->line('date'); ?></label>
                                        <div class="date-input-wrapper">
                                            <input type="text"
                                                   autocomplete="off"
                                                   name="from_date"
                                                   class="form-control datepicker-modern"
                                                   value="<?php echo set_value('from_date'); ?>"
                                                   placeholder="Sélectionner une date"
                                                   id="from_date"
                                                   readonly>
                                            <span class="date-icon"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('from_date'); ?></span>
                                    </div>
                                </div>

                                <div class="search-item">
                                    <div class="form-group date-group">
                                        <label><i class="fa fa-calendar-minus-o"></i> <?php echo $this->lang->line('enquiry') . ' ' . $this->lang->line('to') . ' ' . $this->lang->line('date'); ?></label>
                                        <div class="date-input-wrapper">
                                            <input type="text"
                                                   autocomplete="off"
                                                   name="to_date"
                                                   class="form-control datepicker-modern"
                                                   value="<?php echo set_value('to_date'); ?>"
                                                   placeholder="Sélectionner une date"
                                                   id="to_date"
                                                   readonly>
                                            <span class="date-icon"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <span class="text-danger"><?php echo form_error('to_date'); ?></span>
                                    </div>
                                </div>

                                <div class="search-item">
                                    <div class="form-group">
                                        <label><i class="fa fa-info-circle"></i> <?php echo $this->lang->line('status'); ?></label>
                                        <select id="status" name="status" class="form-control custom-select">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <option value="all" <?php echo ($status == 'all') ? 'selected' : ''; ?>>
                                                <?php echo $this->lang->line('all'); ?>
                                            </option>
                                            <?php foreach ($enquiry_status as $enkey => $envalue): ?>
                                                <option value="<?php echo $enkey; ?>" <?php echo ($enkey == $status) ? 'selected' : ''; ?>>
                                                    <?php echo $envalue; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('status'); ?></span>
                                    </div>
                                </div>

                                <div class="search-item search-button">
                                    <div class="form-group">
                                        <button type="submitsubmit"
                                                name="search"
                                                value="search_filter"
                                                class="btn btn-search">
                                            <i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?>
                                        </button>
                                    </div>
                                    <div class="form-group">
                                        <?php if ($this->rbac->hasPrivilege('permission_enquiry', 'can_add')): ?>
                                            <button type="button"
                                                    class="btn btn-add"
                                                    data-toggle="modal"
                                                    data-target="#myModal">
                                                <i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </form>

                    <!-- Tableau des demandes -->
                    <div class="table-container">

                        <div class="table-body">
                            <div class="download_label">
                                <?php echo $this->lang->line('admission_enquiry') . ' ' . $this->lang->line('list'); ?>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-modern" id="enquirytable">
                                    <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <!--<th> <?php echo $this->lang->line('phone'); ?></th>-->
                                        <th> Type de demande</th>
                                        <th> Motif</th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th> Date début</th>
                                        <th>Date fin</th>
                                        <th> Dernier suivi</th>
                                       <!-- <th> Prochain suivi</th>-->
                                        <th><?php echo $this->lang->line('status'); ?></th>
                                        <th class="text-center"><i class="fa fa-cog"></i> <?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($enquiry_list)): ?>
                                        <tr>
                                            <td colspan="11" class="text-center empty-state">
                                                <div class="empty-state-content">
                                                    <i class="fa fa-inbox empty-icon"></i>
                                                    <p>Aucune demande trouvée</p>
                                                    <span class="empty-hint">Essayez de modifier vos critères de recherche</span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($enquiry_list as $value): ?>
                                            <?php
                                            $current_date = date("Y-m-d");
                                            $next_date = !empty($value["next_date"]) ? $value["next_date"] : (!empty($value["follow_up_date"]) ? $value["follow_up_date"] : '');
                                            $row_class = (!empty($next_date) && $next_date < $current_date) ? 'row-warning' : '';

                                            // Configuration des statuts
                                            $status_styles = [
                                                'pending' => ['label' => 'En attente', 'color' => '#f39c12', 'icon' => 'fa-hourglass-1'],
                                                'approve' => ['label' => 'Approuvé', 'color' => '#27ae60', 'icon' => 'fa-check-circle'],
                                                'completed' => ['label' => 'Terminé', 'color' => '#3498db', 'icon' => 'fa-check-circle'],
                                                'disapprove' => ['label' => 'Refusé', 'color' => '#e74c3c', 'icon' => 'fa-times-circle'],
                                                'in_progress' => ['label' => 'En cours', 'color' => '#9b59b6', 'icon' => 'fa-spinner'],
                                                'on_hold' => ['label' => 'En pause', 'color' => '#f39c12', 'icon' => 'fa-pause-circle'],
                                                'cancelled' => ['label' => 'Annulé', 'color' => '#95a5a6', 'icon' => 'fa-ban'],
                                                'review' => ['label' => 'En révision', 'color' => '#1abc9c', 'icon' => 'fa-eye'],
                                                'draft' => ['label' => 'Brouillon', 'color' => '#95a5a6', 'icon' => 'fa-file-o'],
                                                'archived' => ['label' => 'Archivé', 'color' => '#7f8c8d', 'icon' => 'fa-archive'],
                                                'default' => ['label' => ucfirst($value['status']), 'color' => '#95a5a6', 'icon' => 'fa-circle']
                                            ];

                                            $status_data = isset($status_styles[$value['status']]) ? $status_styles[$value['status']] : $status_styles['default'];
                                            ?>

                                            <tr class="<?php echo $row_class; ?>">
                                                <!-- Nom avec avatar -->
                                                <td>

                                                    <span class="name-text"><?php echo $value['name']; ?></span>
                                                </td>

                                                <!-- Téléphone -->
                                               <!-- <td>
                                                    <?php if (!empty($value['contact'])): ?>
                                                        <a href="tel:<?php echo $value['contact']; ?>" class="phone-link">
                                                            <?php echo $value['contact']; ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>-->

                                                <!-- Référence -->
                                                <td>
                                                    <?php if (!empty($value['reference'])): ?>
                                                        <span class="reference-tag"><?php echo $value['reference']; ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- Motif/Source -->
                                                <td>
                                                    <?php if (!empty($value['source'])): ?>
                                                        <span class="source-text"><?php echo $value['source']; ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- Date de la demande -->
                                                <td>
                                                    <?php if (!empty($value['date'])): ?>
                                                        <span class="date-badge">

                                    <?php
                                    $date_timestamp = $this->customlib->dateyyyymmddTodateformat($value['date']);
                                    if ($date_timestamp) {
                                        echo date($this->customlib->getSchoolDateFormat(), $date_timestamp);
                                    } else {
                                        echo date($this->customlib->getSchoolDateFormat(), strtotime($value['date']));
                                    }
                                    ?>
                                </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- Date début -->
                                                <td>
                                                    <?php if (!empty($value['date_start'])): ?>
                                                        <span class="date-badge date-start">

                                    <?php
                                    $date_start_timestamp = $this->customlib->dateyyyymmddTodateformat($value['date_start']);
                                    if ($date_start_timestamp) {
                                        echo date($this->customlib->getSchoolDateFormat(), $date_start_timestamp);
                                    } else {
                                        echo date($this->customlib->getSchoolDateFormat(), strtotime($value['date_start']));
                                    }
                                    ?>
                                </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- Date fin -->
                                                <td>
                                                    <?php if (!empty($value['date_end'])): ?>
                                                        <span class="date-badge date-end">

                                    <?php
                                    $date_end_timestamp = $this->customlib->dateyyyymmddTodateformat($value['date_end']);
                                    if ($date_end_timestamp) {
                                        echo date($this->customlib->getSchoolDateFormat(), $date_end_timestamp);
                                    } else {
                                        echo date($this->customlib->getSchoolDateFormat(), strtotime($value['date_end']));
                                    }
                                    ?>
                                </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- Dernier suivi -->
                                                <td>
                                                    <?php if (!empty($value['followupdate'])): ?>
                                                        <span class="date-badge">

                                    <?php
                                    $followupdate_timestamp = $this->customlib->dateyyyymmddTodateformat($value['followupdate']);
                                    if ($followupdate_timestamp) {
                                        echo date($this->customlib->getSchoolDateFormat(), $followupdate_timestamp);
                                    } else {
                                        echo date($this->customlib->getSchoolDateFormat(), strtotime($value['followupdate']));
                                    }
                                    ?>
                                </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- Prochain suivi -->
                                                <!--<td>
                                                    <?php if (!empty($next_date)): ?>
                                                        <span class="next-date <?php echo ($next_date < $current_date) ? 'overdue' : ''; ?>">

                                    <?php
                                    $next_date_timestamp = $this->customlib->dateyyyymmddTodateformat($next_date);
                                    if ($next_date_timestamp) {
                                        echo date($this->customlib->getSchoolDateFormat(), $next_date_timestamp);
                                    } else {
                                        echo date($this->customlib->getSchoolDateFormat(), strtotime($next_date));
                                    }
                                    ?>
                                                            <?php if ($next_date < $current_date): ?>
                                                                <span class="overdue-badge">(En retard)</span>
                                                            <?php endif; ?>
                                </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>-->

                                                <!-- Statut -->
                                                <td>
                            <span class="status-badge" style="background-color: <?php echo $status_data['color']; ?>; color: white;">
                                <i class="fa <?php echo $status_data['icon']; ?>"></i>
                                <?php echo $status_data['label']; ?>
                            </span>
                                                </td>

                                                <!-- Actions -->
                                                <td class="text-center">
                                                    <div class="action-group">
                                                        <?php if ($this->rbac->hasPrivilege('follow_up_permission_enquiry', 'can_view')): ?>
                                                            <button class="btn-action btn-followup"
                                                                    onclick="follow_up('<?php echo $value['id']; ?>', '<?php echo $value['status']; ?>');"
                                                                    data-target="#follow_up"
                                                                    data-toggle="modal"
                                                                    title="<?php echo $this->lang->line('follow_up_admission_enquiry'); ?>">
                                                                <i class="fa fa-reorder"></i>
                                                            </button>
                                                        <?php endif; ?>

                                                        <?php if ($value['status'] == 'approve' && $this->rbac->hasPrivilege('permission_enquiry', 'can_view')): ?>
                                                            <!--<button class="btn-action btn-print"
                                                                    onclick="printPermission('<?php echo $value['id']; ?>')"
                                                                    title="Imprimer le document d'acceptation">
                                                                <i class="fa fa-print"></i>
                                                            </button>-->
                                                        <?php endif; ?>

                                                        <?php if ($this->rbac->hasPrivilege('permission_enquiry', 'can_edit')): ?>
                                                            <button class="btn-action btn-edit"
                                                                    onclick="getRecord('<?php echo $value['id']; ?>', '<?php echo $value['status']; ?>')"
                                                                    data-target="#myModaledit"
                                                                    data-toggle="modal"
                                                                    title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </button>
                                                        <?php endif; ?>

                                                        <?php if ($this->rbac->hasPrivilege('permission_enquiry', 'can_delete')): ?>
                                                            <button class="btn-action btn-delete"
                                                                    onclick="delete_enquiry('<?php echo $value['id']; ?>')"
                                                                    title="<?php echo $this->lang->line('delete'); ?>">
                                                                <i class="fa fa-remove"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal d'ajout -->
    <div class="modal fade modal-modern" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-modern">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        <i class="fa fa-plus-circle"></i>
                        <?php echo $this->lang->line('admission_enquiry'); ?>
                    </h4>
                </div>

                <div class="modal-body">
                    <form id="formadd" method="post" class="form-modern">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="section-title">
                                    <h5><i class="fa fa-user-circle text-primary"></i> Informations personnelles</h5>
                                </div>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('name'); ?> <span class="required">*</span></label>
                                    <input type="text"
                                           id="name_add"
                                           autocomplete="off"
                                           class="form-control"
                                           value="<?php echo $this->customlib->getAdminSessionUserName(); ?>"
                                           name="name"
                                           readonly>
                                    <span id="name_add_error" class="text-danger"></span>
                                </div>

                                <div class="form-group">
                                    <label><?php echo $this->lang->line('phone'); ?> <span class="required">*</span></label>
                                    <input id="number"
                                           autocomplete="off"
                                           name="contact"
                                           type="text"
                                           class="form-control"
                                           value="<?php echo set_value('contact'); ?>">
                                </div>

                                <div class="form-group" hidden>
                                    <label><?php echo $this->lang->line('email'); ?></label>
                                    <input type="email"
                                           value="<?php echo set_value('email'); ?>"
                                           name="email"
                                           class="form-control">
                                </div>

                                <div class="form-group" hidden>
                                    <label><?php echo $this->lang->line('address'); ?></label>
                                    <textarea name="address" class="form-control" rows="2"><?php echo set_value('address'); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label><?php echo $this->lang->line('reference'); ?></label>
                                    <select name="reference" class="form-control custom-select">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($Reference as $value): ?>
                                            <option value="<?php echo $value['reference']; ?>" <?php echo set_select('reference', $value['reference']); ?>>
                                                <?php echo $value['reference']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label><?php echo $this->lang->line('assigned'); ?></label>
                                    <select name="assigned" class="form-control custom-select">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($stff_list as $staff): ?>
                                            <option value="<?php echo $staff['name'] . ' ' . $staff['surname']; ?>">
                                                <?php echo $staff['name'] . ' ' . $staff['surname']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>


                            </div>

                            <div class="col-md-6">
                                <div class="section-title">
                                    <h5><i class="fa fa-info-circle text-primary"></i> Détails de la demande</h5>
                                </div>

                                <div class="form-group date-group" hidden>
                                    <label>Date de la demande <span class="required">*</span></label>
                                    <div class="date-input-wrapper">
                                        <input type="text"
                                               id="date"
                                               name="date"
                                               class="form-control datepicker-modern"
                                               value="<?php echo set_value('date', date('Y-m-d')); ?>"
                                               placeholder="Sélectionner une date">
                                        <span class="date-icon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                    <span id="date_add_error" class="text-danger"></span>
                                </div>

                                <div class="form-group date-group">
                                    <label>Date de début</label>
                                    <div class="date-input-wrapper">
                                        <input type="text"
                                               id="date_start"
                                               name="date_start"
                                               class="form-control datepicker-modern"
                                               value="<?php echo set_value('date_start', date('Y-m-d')); ?>"
                                               placeholder="Sélectionner une date">
                                        <span class="date-icon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>

                                <div class="form-group date-group">
                                    <label>Date de fin</label>
                                    <div class="date-input-wrapper">
                                        <input type="text"
                                               id="date_end"
                                               name="date_end"
                                               class="form-control datepicker-modern"
                                               value="<?php echo set_value('date_end', date('Y-m-d')); ?>"
                                               placeholder="Sélectionner une date">
                                        <span class="date-icon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Titre référence <span class="required">*</span></label>
                                    <select name="source" class="form-control custom-select">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($sourcelist as $value): ?>
                                            <option value="<?php echo $value['source']; ?>">
                                                <?php echo $value['source']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group date-group" hidden>
                                    <label>Date de suivi</label>
                                    <div class="date-input-wrapper">
                                        <input type="text"
                                               id="follow_up_date"
                                               name="follow_up_date"
                                               class="form-control datepicker-modern"
                                               value="<?php echo set_value('follow_up_date', date('Y-m-d')); ?>"
                                               placeholder="Sélectionner une date">
                                        <span class="date-icon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="section-title">
                                    <h5><i class="fa fa-comment text-primary"></i> Informations complémentaires</h5>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('description'); ?></label>
                                            <textarea name="description" class="form-control" rows="3"><?php echo set_value('description'); ?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('note'); ?></label>
                                            <textarea name="note" class="form-control" rows="3"><?php echo set_value('note'); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Champs cachés pour compatibilité -->
                            <div class="col-sm-3" hidden>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('class'); ?></label>
                                    <select name="class" class="form-control custom-select">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($class_list as $class): ?>
                                            <option value="<?php echo $class['id']; ?>" <?php echo set_select('class', $class['id']); ?>>
                                                <?php echo $class['class']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3" hidden>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('number_of_child'); ?></label>
                                    <input type="number"
                                           class="form-control"
                                           min="1"
                                           value="<?php echo set_value('no_of_child'); ?>"
                                           name="no_of_child">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer modal-footer-modern">
                    <button type="button" class="btn btn-cancel" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</button>
                    <button onclick="saveEnquiry()" class="btn btn-save">
                        <i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'édition -->
    <div class="modal fade modal-modern" id="myModaledit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-modern">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        <i class="fa fa-edit"></i>
                        <?php echo $this->lang->line('edit_admission_enquiry'); ?>
                    </h4>
                </div>
                <div class="modal-body" id="getdetails"></div>
            </div>
        </div>
    </div>

    <!-- Modal de suivi -->
    <div class="modal fade modal-modern" id="follow_up" tabindex="-1" role="dialog" aria-labelledby="follow_up">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-modern">
                    <button type="button" class="close" onclick="update()" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        <i class="fa fa-clock-o"></i>
                        <?php echo $this->lang->line('follow_up_admission_enquiry'); ?>
                    </h4>
                </div>
                <div class="modal-body" id="getdetails_follow_up"></div>
            </div>
        </div>
    </div>
</div>

<!-- Inclusion des ressources nécessaires -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.fr.min.js"></script>

<script>
    $(document).ready(function () {
        // ===== INITIALISATION DES DATEPICKERS MODERNES =====
        var dateOptions = {
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            language: 'fr',
            clearBtn: true,
            orientation: 'bottom auto',
            daysOfWeekHighlighted: "0,6",
            todayBtn: true,
            templates: {
                leftArrow: '<i class="fa fa-chevron-left"></i>',
                rightArrow: '<i class="fa fa-chevron-right"></i>'
            }
        };

        // Initialiser tous les datepickers modernes
        $('.datepicker-modern').datepicker(dateOptions).on('changeDate', function(e) {
            // Mettre à jour la valeur affichée
            $(this).val(e.format('yyyy-mm-dd'));
            $(this).trigger('change');
        });

        // Synchronisation des dates
        $('#from_date').on('changeDate', function(e) {
            var startDate = e.date;
            $('#to_date').datepicker('setStartDate', startDate);
            // Mettre à jour le placeholder
            if (startDate) {
                $('#to_date').attr('placeholder', 'À partir du ' + e.format('dd/mm/yyyy'));
            }
        });

        $('#to_date').on('changeDate', function(e) {
            var endDate = e.date;
            $('#from_date').datepicker('setEndDate', endDate);
            if (endDate) {
                $('#from_date').attr('placeholder', 'Jusqu\'au ' + e.format('dd/mm/yyyy'));
            }
        });

        // ===== RACCOURCIS DE DATES =====
        $('.shortcut-btn').on('click', function() {
            var days = $(this).data('days');
            var fromDate = new Date();
            var toDate = new Date();

            if (days) {
                fromDate.setDate(fromDate.getDate() - days);
                // Formater les dates
                var fromStr = fromDate.getFullYear() + '-' +
                    String(fromDate.getMonth() + 1).padStart(2, '0') + '-' +
                    String(fromDate.getDate()).padStart(2, '0');
                var toStr = toDate.getFullYear() + '-' +
                    String(toDate.getMonth() + 1).padStart(2, '0') + '-' +
                    String(toDate.getDate()).padStart(2, '0');

                $('#from_date').val(fromStr).datepicker('update', fromStr);
                $('#to_date').val(toStr).datepicker('update', toStr);

                // Mettre à jour les placeholders
                var fromDisplay = fromDate.getDate() + '/' + String(fromDate.getMonth() + 1).padStart(2, '0') + '/' + fromDate.getFullYear();
                var toDisplay = toDate.getDate() + '/' + String(toDate.getMonth() + 1).padStart(2, '0') + '/' + toDate.getFullYear();
                $('#from_date').attr('placeholder', 'Du ' + fromDisplay);
                $('#to_date').attr('placeholder', 'Au ' + toDisplay);
            }
        });

        // Bouton Effacer
        $('.shortcut-clear').on('click', function() {
            $('#from_date').val('').datepicker('update', '').attr('placeholder', 'Sélectionner une date');
            $('#to_date').val('').datepicker('update', '').attr('placeholder', 'Sélectionner une date');
            $('#from_date').datepicker('setStartDate', null);
            $('#to_date').datepicker('setEndDate', null);
        });

        // ===== INITIALISATION DATATABLE =====
        if ($.fn.DataTable) {
            $("#enquirytable").DataTable({
                searching: true,
                paging: true,
                bSort: true,
                info: true,
                language: {
                    search: "Rechercher:",
                    lengthMenu: "Afficher _MENU_ entrées",
                    info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                    infoEmpty: "Affichage 0 à 0 sur 0 entrées",
                    infoFiltered: "(filtré de _MAX_ entrées totales)",
                    paginate: {
                        first: "Premier",
                        last: "Dernier",
                        next: "Suivant",
                        previous: "Précédent"
                    },
                    emptyTable: "Aucune donnée disponible",
                    zeroRecords: "Aucun enregistrement correspondant trouvé"
                },
                dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                    "<'row'<'col-sm-12'B>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [
                    {
                        extend: 'copyHtml5',
                        text: '<i class="fa fa-files-o"></i> Copier',
                        titleAttr: 'Copier',
                        title: $('.download_label').html(),
                        exportOptions: { columns: ':visible' },
                        className: 'btn btn-sm btn-default'
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: $('.download_label').html(),
                        exportOptions: { columns: ':visible' },
                        className: 'btn btn-sm btn-success'
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fa fa-file-text-o"></i> CSV',
                        titleAttr: 'CSV',
                        title: $('.download_label').html(),
                        exportOptions: { columns: ':visible' },
                        className: 'btn btn-sm btn-info'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa fa-file-pdf-o"></i> PDF',
                        titleAttr: 'PDF',
                        title: $('.download_label').html(),
                        exportOptions: { columns: ':visible' },
                        className: 'btn btn-sm btn-danger',
                        orientation: 'landscape',
                        pageSize: 'A4'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> Imprimer',
                        titleAttr: 'Imprimer',
                        title: $('.download_label').html(),
                        customize: function (win) {
                            $(win.document.body)
                                .css('font-size', '10pt')
                                .find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');

                            $(win.document.body).prepend(
                                '<div style="text-align: center; margin-bottom: 20px;">' +
                                '<h2>Liste des demandes</h2>' +
                                '<p>Généré le ' + new Date().toLocaleDateString('fr-FR') + '</p>' +
                                '</div>'
                            );
                        },
                        exportOptions: { columns: ':visible' },
                        className: 'btn btn-sm btn-primary'
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns"></i> Colonnes',
                        titleAttr: 'Colonnes',
                        postfixButtons: ['colvisRestore'],
                        className: 'btn btn-sm btn-warning'
                    }
                ],

            });
        }
    });

    // ===== FONCTIONS UTILITAIRES =====
    function getRecord(id, status) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/enquiry/details/' + id + '/' + status,
            success: function (result) {
                $('#getdetails').html(result);
                $('.modal').on('shown.bs.modal', function() {
                    $('.datepicker-modern').datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true,
                        language: 'fr',
                        clearBtn: true,
                        templates: {
                            leftArrow: '<i class="fa fa-chevron-left"></i>',
                            rightArrow: '<i class="fa fa-chevron-right"></i>'
                        }
                    });
                });
            },
            error: function () {
                alert('Erreur lors du chargement des détails');
            }
        });
    }

    function postRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/enquiry/editpost/' + id,
            type: 'POST',
            data: $("#myForm1").serialize(),
            dataType: 'json',
            success: function (data) {
                if (data.status == "fail") {
                    var message = Object.values(data.error).join('<br>');
                    alert(message);
                } else {
                    alert(data.message);
                    window.location.reload(true);
                }
            },
            error: function () {
                alert('Erreur lors de la mise à jour');
            }
        });
    }

    function saveEnquiry() {
        // Récupérer les valeurs des dates
        var date = $('#date').val();
        var date_start = $('#date_start').val();
        var date_end = $('#date_end').val();
        var follow_up_date = $('#follow_up_date').val();

        // Validation simple des dates
        var dateRegex = /^\d{4}-\d{2}-\d{2}$/;

        if (!dateRegex.test(date)) {
            alert('La date de la demande n\'est pas valide. Format attendu: AAAA-MM-JJ');
            return;
        }

        if (date_start && !dateRegex.test(date_start)) {
            alert('La date de début n\'est pas valide. Format attendu: AAAA-MM-JJ');
            return;
        }

        if (date_end && !dateRegex.test(date_end)) {
            alert('La date de fin n\'est pas valide. Format attendu: AAAA-MM-JJ');
            return;
        }

        if (follow_up_date && !dateRegex.test(follow_up_date)) {
            alert('La date de suivi n\'est pas valide. Format attendu: AAAA-MM-JJ');
            return;
        }

        // Vérifier que la date de début <= date de fin
        if (date_start && date_end && date_start > date_end) {
            alert('La date de début doit être antérieure ou égale à la date de fin');
            return;
        }

        $.ajax({
            url: '<?php echo base_url(); ?>admin/enquiry/add/',
            type: 'POST',
            dataType: 'json',
            data: $("#formadd").serialize(),
            success: function (data) {
                if (data.status == "fail") {
                    var message = '';
                    if (data.error) {
                        message = Object.values(data.error).join('<br>');
                    }
                    alert(message || 'Erreur de validation');
                } else {
                    alert(data.message || 'Enregistrement réussi');
                    window.location.reload(true);
                }
            },
            error: function (xhr, status, error) {
                console.error('Erreur AJAX:', error);
                alert('Erreur lors de l\'enregistrement. Veuillez réessayer.');
            }
        });
    }

    function delete_enquiry(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/enquiry/delete/' + id,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    if (data.status == "fail") {
                        var message = Object.values(data.error).join('<br>');
                        alert(message);
                    } else {
                        alert(data.message);
                        window.location.reload(true);
                    }
                },
                error: function () {
                    alert('Erreur lors de la suppression');
                }
            });
        }
    }

    function follow_up(id, status) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/enquiry/follow_up/' + id + '/' + status,
            success: function (data) {
                $('#getdetails_follow_up').html(data);
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/enquiry/follow_up_list/' + id,
                    success: function (data) {
                        $('#timeline').html(data);
                    },
                    error: function () {
                        alert('Erreur lors du chargement de l\'historique');
                    }
                });
            },
            error: function () {
                alert('Erreur lors du chargement du suivi');
            }
        });
    }

    function update() {
        window.location.reload(true);
    }

    function printPermission(id) {
        // Test avec console.log pour vérifier que la fonction est appelée
        console.log('Impression pour ID:', id);

        // Option 1: Nouvelle fenêtre (recommandée)
        var url = '<?php echo base_url(); ?>admin/enquiry/print_permission/' + id;
        var printWindow = window.open(url, '_blank', 'width=1000,height=800');

        if (printWindow) {
            printWindow.focus();
        } else {
            alert('Veuillez autoriser les popups pour cette application.');
            // Alternative: ouvrir dans la même fenêtre
            // window.location.href = url;
        }
    }

</script>

<style>
    /* ========== STYLES MODERNES ========== */

    /* Variables */
    :root {
        --primary: #273772;
        --primary-dark: #273772;
        --primary-light: #273772;
        --secondary: #2D3436;
        --success: #00B894;
        --danger: #FF6B6B;
        --warning: #FDCB6E;
        --info: #74B9FF;
        --light-bg: #F8F9FA;
        --border-radius: 12px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        --shadow-hover: 0 15px 40px rgba(108, 99, 255, 0.15);
    }

    /* ===== EN-TÊTE ===== */
    .content-header {
        padding: 20px 0 15px 0;
        border-bottom: none;
    }

    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        background: white;
        padding: 20px 30px;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
    }

    .page-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--secondary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .title-icon {
        color: var(--primary);
        font-size: 28px;
    }

    .title-badge {
        background-color: #273772;
        color: white;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 12px;
        border-radius: 20px;
        margin-left: 8px;
        letter-spacing: 0.5px;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .current-date {
        color: #7f8c8d;
        font-size: 14px;
        background: var(--light-bg);
        padding: 6px 15px;
        border-radius: 20px;
    }

    .current-date i {
        margin-right: 6px;
        color: var(--primary);
    }

    /* ===== CARTE DE RECHERCHE ===== */
    .card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        transition: var(--transition);
    }

    .card:hover {
        box-shadow: var(--shadow-hover);
    }

    .card-primary {
        border-top: 4px solid var(--primary);
    }

    .card-header {
        background: white;
        border-bottom: 1px solid #f0f0f0;
        padding: 18px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--secondary);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-icon {
        color: #273772;
        font-size: 18px;
    }

    .card-tools .btn-tool {
        color: #95a5a6;
        padding: 4px 8px;
        font-size: 16px;
        transition: var(--transition);
    }

    .card-tools .btn-tool:hover {
        color: #273772;
        transform: rotate(90deg);
    }

    .card-body {
        padding: 25px 25px 10px 25px;
        background: #fafbfc;
    }

    /* ===== GRILLE DE RECHERCHE ===== */
    .search-grid {

        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr auto;
        gap: 15px;
        align-items: end;
    }

    .search-item {
        min-width: 0;
    }

    .search-item .form-group {
        margin-bottom: 15px;
    }

    .search-item label {
        font-weight: 500;
        font-size: 13px;
        color: #555;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .search-item label i {
        color: var(--primary);
        font-size: 14px;
    }

    /* ===== DATE PICKER MODERNE ===== */
    .date-group {
        position: relative;
    }

    .date-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .date-input-wrapper .form-control {
        padding-right: 42px;
        cursor: pointer;
        background: white;
        transition: var(--transition);
    }

    .date-input-wrapper .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.1);
    }

    .date-input-wrapper .form-control:hover {
        border-color: var(--primary-light);
    }

    .date-icon {
        position: absolute;
        right: 14px;
        color: #95a5a6;
        pointer-events: none;
        font-size: 16px;
        transition: var(--transition);
    }

    .date-input-wrapper .form-control:focus + .date-icon,
    .date-input-wrapper .form-control:hover + .date-icon {
        color: var(--primary);
    }

    .form-control, .custom-select {
        border: 2px solid #e8ecf1;
        border-radius: 8px;
        padding: 9px 14px;
        font-size: 14px;
        transition: var(--transition);
        height: 42px;
        background: white;
    }

    .form-control:focus, .custom-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.1);
    }

    .custom-datepicker {
        cursor: pointer;
    }

    /* ===== RACCOURCIS DE DATES ===== */
    .date-shortcuts {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        padding: 10px 0 5px 0;
        border-top: 1px solid #e8ecf1;
        margin-top: 10px;
    }

    .shortcut-label {
        font-size: 12px;
        font-weight: 600;
        color: #7f8c8d;
        margin-right: 5px;
    }

    .shortcut-btn {
        padding: 4px 14px;
        border: 1.5px solid #e8ecf1;
        border-radius: 20px;
        background: white;
        color: #555;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
    }

    .shortcut-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: rgba(108, 99, 255, 0.05);
    }

    .shortcut-clear {
        border-color: #ff6b6b;
        color: #ff6b6b;
    }

    .shortcut-clear:hover {
        background: #fff5f5;
        border-color: #ff6b6b;
        color: #ff6b6b;
    }

    .search-button {
        display: flex;
        align-items: end;
        padding-bottom: 0;
    }

    .btn-search {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        padding: 10px 30px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        transition: var(--transition);
        height: 42px;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .btn-search:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(108, 99, 255, 0.3);
        color: white;
    }

    .btn-search i {
        font-size: 16px;
    }

    /* ===== TABLEAU ===== */
    .table-container {
        padding: 0 25px 25px 25px;
        background: white;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 0 15px 0;
        flex-wrap: wrap;
        gap: 15px;
        border-bottom: 2px solid #f0f0f0;
    }

    .table-title-section {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .table-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--secondary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .table-title i {
        color: var(--primary);
    }

    .record-count {
        color: #7f8c8d;
        font-size: 13px;
        background: var(--light-bg);
        padding: 2px 12px;
        border-radius: 12px;
    }

    .table-actions {
        display: flex;
        gap: 10px;
    }

    .btn-add {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        margin-left: 9px;
        border: none;
        padding: 12px 26px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 13px;
        transition: var(--transition);
    }

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(108, 99, 255, 0.3);
        color: white;
    }

    .table-body {
        padding-top: 15px;
    }

    /* ===== TABLEAU MODERNE ===== */
    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 6px;
        margin-bottom: 0;
    }

    .table-modern thead tr {
        background: #f8f9fa;
        border-radius: var(--border-radius);
    }

    .table-modern thead th {
        padding: 12px 16px;
        font-weight: 600;
        font-size: 12px;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        border-bottom: 2px solid #e8ecf1;
    }

    .table-modern thead th i {
        margin-right: 6px;
        font-size: 13px;
        color: var(--primary);
    }

    .table-modern tbody tr {
        background: white;
        border-radius: var(--border-radius);
        transition: var(--transition);
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .table-modern tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .table-modern tbody td {
        padding: 14px 16px;
        border: none;
        vertical-align: middle;
        font-size: 14px;
        color: var(--secondary);
    }

    .table-modern tbody tr.row-warning {
        background: #fff9f0;
        border-left: 4px solid var(--warning);
    }

    /* ===== CELLULES SPÉCIALES ===== */
    .name-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 13px;
        flex-shrink: 0;
    }

    .phone-link {
        color: var(--secondary);
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
    }

    .phone-link:hover {
        color: var(--primary);
    }

    .reference-tag {
        background: var(--light-bg);
        padding: 3px 12px;
        border-radius: 12px;
        font-size: 12px;
        color: #555;
        font-weight: 500;
    }

    .date-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
    }

    .date-badge i {
        color: var(--primary);
    }

    .next-date {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        background: #e8f5e9;
        color: #2e7d32;
    }

    .next-date.overdue {
        background: #ffebee;
        color: #c62828;
    }

    /* ===== BADGE DE STATUT ===== */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        transition: var(--transition);
    }

    .status-badge i {
        font-size: 12px;
    }

    /* ===== GROUPE D'ACTIONS ===== */
    .action-group {
        display: flex;
        gap: 4px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: var(--transition);
        cursor: pointer;
        background: var(--light-bg);
        color: #7f8c8d;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }

    .btn-followup:hover {
        background: #e3f2fd;
        color: #1976d2;
    }

    .btn-print:hover {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .btn-edit:hover {
        background: #fff3e0;
        color: #e65100;
    }

    .btn-delete:hover {
        background: #ffebee;
        color: #c62828;
    }

    /* ===== ÉTAT VIDE ===== */
    .empty-state {
        padding: 50px 0 !important;
    }

    .empty-state-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .empty-icon {
        font-size: 48px;
        color: #dce0e5;
    }

    .empty-state-content p {
        font-size: 18px;
        color: #7f8c8d;
        margin: 0;
    }

    .empty-hint {
        font-size: 13px;
        color: #bdc3c7;
    }

    /* ===== MODALS MODERNES ===== */
    .modal-modern .modal-content {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }

    .modal-header-modern {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        border-radius: var(--border-radius) var(--border-radius) 0 0;
        padding: 20px 25px;
    }

    .modal-header-modern .close {
        color: white;
        opacity: 0.7;
        font-size: 28px;
        transition: var(--transition);
    }

    .modal-header-modern .close:hover {
        opacity: 1;
        transform: rotate(90deg);
    }

    .modal-header-modern .modal-title {
        font-weight: 600;
        font-size: 18px;
    }

    .modal-header-modern .modal-title i {
        margin-right: 10px;
    }

    .modal-footer-modern {
        border: none;
        padding: 15px 25px 20px 25px;
        gap: 10px;
    }

    .btn-cancel {
        background: #f5f5f5;
        color: #555;
        border: none;
        padding: 8px 22px;
        border-radius: 8px;
        font-weight: 500;
        transition: var(--transition);
    }

    .btn-cancel:hover {
        background: #e8e8e8;
    }

    .btn-save {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        padding: 8px 25px;
        border-radius: 8px;
        font-weight: 500;
        transition: var(--transition);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(108, 99, 255, 0.3);
        color: white;
    }

    /* ===== FORMULAIRES DANS LES MODALS ===== */
    .section-title {
        margin: 0 0 15px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid #f0f0f0;
    }

    .section-title h5 {
        font-weight: 600;
        font-size: 15px;
        color: var(--secondary);
        margin: 0;
    }

    .section-title h5 i {
        margin-right: 8px;
        color: var(--primary);
    }

    .form-modern .form-group {
        margin-bottom: 18px;
    }

    .form-modern label {
        font-weight: 500;
        font-size: 13px;
        color: #555;
        margin-bottom: 5px;
    }

    .required {
        color: var(--danger);
        font-weight: 700;
    }

    /* ===== OVERRIDE DATEPICKER BOOTSTRAP ===== */
    .datepicker {
        border-radius: var(--border-radius);
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        padding: 10px;
        border: none;
    }

    .datepicker table tr td,
    .datepicker table tr th {
        border-radius: 6px;
        transition: var(--transition);
    }

    .datepicker table tr td.active,
    .datepicker table tr td.active:hover {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important;
        color: white !important;
        border: none;
    }

    .datepicker table tr td:hover {
        background: rgba(108, 99, 255, 0.08);
    }

    .datepicker table tr td.today {
        background: rgba(108, 99, 255, 0.12);
        color: var(--primary);
    }

    .datepicker .datepicker-switch:hover,
    .datepicker .prev:hover,
    .datepicker .next:hover {
        background: rgba(108, 99, 255, 0.08);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .search-grid {
            grid-template-columns: 1fr 1fr;
        }

        .search-button {
            grid-column: span 2;
        }
    }

    @media (max-width: 768px) {
        .search-grid {
            grid-template-columns: 1fr;
        }

        .search-button {
            grid-column: span 1;
        }

        .header-container {
            flex-direction: column;
            align-items: flex-start;
            padding: 15px 20px;
        }

        .table-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .table-actions {
            width: 100%;
        }

        .btn-add {
            width: 100%;
            justify-content: center;
        }

        .action-group {
            gap: 2px;
        }

        .btn-action {
            width: 28px;
            height: 28px;
            font-size: 12px;
        }

        .modal-dialog {
            margin: 10px;
        }

        .date-shortcuts {
            gap: 5px;
        }

        .shortcut-btn {
            font-size: 11px;
            padding: 3px 10px;
        }
    }

    @media (max-width: 576px) {
        .page-title {
            font-size: 18px;
            flex-wrap: wrap;
        }

        .title-badge {
            font-size: 10px;
        }

        .current-date {
            font-size: 12px;
            padding: 4px 12px;
        }

        .card-body {
            padding: 15px;
        }

        .table-container {
            padding: 0 15px 15px 15px;
        }
    }

    /* ===== ANIMATIONS ===== */
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

    .card {
        animation: fadeInUp 0.5s ease forwards;
    }

    .table-modern tbody tr {
        animation: fadeInUp 0.4s ease forwards;
        opacity: 0;
    }

    .table-modern tbody tr:nth-child(1) { animation-delay: 0.05s; }
    .table-modern tbody tr:nth-child(2) { animation-delay: 0.10s; }
    .table-modern tbody tr:nth-child(3) { animation-delay: 0.15s; }
    .table-modern tbody tr:nth-child(4) { animation-delay: 0.20s; }
    .table-modern tbody tr:nth-child(5) { animation-delay: 0.25s; }
    .table-modern tbody tr:nth-child(6) { animation-delay: 0.30s; }
    .table-modern tbody tr:nth-child(7) { animation-delay: 0.35s; }
    .table-modern tbody tr:nth-child(8) { animation-delay: 0.40s; }
    .table-modern tbody tr:nth-child(9) { animation-delay: 0.45s; }
    .table-modern tbody tr:nth-child(10) { animation-delay: 0.50s; }
</style>