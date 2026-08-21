<style>
    .fc-event-title {
        font-weight: bold;
        font-size: 12px;
    }
    .fc-daygrid-day-events {
        min-height: 30px;
    }
    .fc-daygrid-day-number {
        font-size: 14px;
        font-weight: bold;
    }
    .fc-toolbar-title {
        font-size: 18px !important;
    }
    .fc-event-main {
        padding: 2px 4px;
    }
    .fc-day-today {
        background-color: #f0f8ff !important;
    }
    .calendar-filters {
        margin-bottom: 20px;
        background: #f9f9f9;
        padding: 15px;
        border-radius: 5px;
        border: 1px solid #e3e3e3;
    }
    .calendar-filters .form-group {
        margin-right: 10px;
    }
    .legend {
        display: flex;
        gap: 20px;
        margin-top: 10px;
        flex-wrap: wrap;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 13px;
    }
    .legend-color {
        width: 20px;
        height: 12px;
        border-radius: 3px;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-calendar"></i> Calendrier des congés</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-calendar-check-o"></i> Vue d'ensemble</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-success btn-sm" id="refreshCalendar">
                                <i class="fa fa-refresh"></i> Rafraîchir
                            </button>
                            <a href="<?= base_url('admin/leaverequest/leaverequest') ?>" class="btn btn-primary btn-sm">
                                <i class="fa fa-list"></i> Voir toutes les demandes
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <!-- Filtres -->
                        <div class="calendar-filters row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Employé</label>
                                    <select id="filterStaff" class="form-control">
                                        <option value="">Tous</option>
                                        <?php foreach ($staff_list as $staff): ?>
                                            <option value="<?= $staff['id'] ?>">
                                                <?= $staff['name'] . ' ' . $staff['surname'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Type de congé</label>
                                    <select id="filterLeaveType" class="form-control">
                                        <option value="">Tous</option>
                                        <?php foreach ($leave_types as $type): ?>
                                            <option value="<?= $type['id'] ?>">
                                                <?= $type['type'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Statut</label>
                                    <select id="filterStatus" class="form-control">
                                        <option value="">Tous (sauf refusés)</option>
                                        <option value="approve">Approuvés</option>
                                        <option value="pending">En attente</option>
                                        <option value="disapprove">Refusés</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button id="applyFilters" class="btn btn-primary btn-block">
                                        <i class="fa fa-filter"></i> Appliquer
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Légende -->
                        <div class="legend">
                            <div class="legend-item">
                                <span class="legend-color" style="background:#28a745;"></span> Approuvé
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background:#ffc107;"></span> En attente
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background:#dc3545;"></span> Refusé
                            </div>
                        </div>

                        <!-- Le calendrier -->
                        <div id="calendar" style="margin-top: 20px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Inclusion de FullCalendar (CDN) -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'fr',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek,listWeek'
            },
            initialView: 'dayGridMonth',
            firstDay: 1, // Lundi
            height: 'auto',
            eventSources: [{
                url: '<?= base_url('admin/leaverequest/get_calendar_events') ?>',
                method: 'GET',
                extraParams: function() {
                    return {
                        staff_id: $('#filterStaff').val(),
                        leave_type_id: $('#filterLeaveType').val(),
                        status: $('#filterStatus').val()
                    };
                },
                failure: function() {
                    alert('Erreur de chargement des événements');
                }
            }],
            eventClick: function(info) {
                // Afficher les détails du congé dans une popup
                var props = info.event.extendedProps;
                var statusLabel = '';
                if (props.status == 'approve') statusLabel = '✅ Approuvé';
                else if (props.status == 'pending') statusLabel = '⏳ En attente';
                else if (props.status == 'disapprove') statusLabel = '❌ Refusé';

                var modalContent = `
                <div class="modal fade" id="eventModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Détails du congé</h4>
                            </div>
                            <div class="modal-body">
                                <p><strong>Employé :</strong> ${info.event.title}</p>
                                <p><strong>Du :</strong> ${info.event.start.toLocaleDateString('fr-FR')}</p>
                                <p><strong>Au :</strong> ${info.event.end ? new Date(info.event.end - 86400000).toLocaleDateString('fr-FR') : '...'}</p>
                                <p><strong>Durée :</strong> ${props.leave_days || 'N/A'} jours</p>
                                <p><strong>Statut :</strong> ${statusLabel}</p>
                                ${props.employee_remark ? `<p><strong>Remarque employé :</strong> ${props.employee_remark}</p>` : ''}
                                ${props.admin_remark ? `<p><strong>Remarque admin :</strong> ${props.admin_remark}</p>` : ''}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                // Supprimer une éventuelle modale précédente
                $('#eventModal').remove();
                $('body').append(modalContent);
                $('#eventModal').modal('show');
            },
            loading: function(isLoading) {
                if (isLoading) {
                    $('#calendar').append('<div class="loading-overlay"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
                } else {
                    $('.loading-overlay').remove();
                }
            }
        });
        calendar.render();

        // Appliquer les filtres
        $('#applyFilters').click(function() {
            calendar.refetchEvents();
        });

        // Rafraîchir manuellement
        $('#refreshCalendar').click(function() {
            calendar.refetchEvents();
        });

        // Réinitialiser les filtres (optionnel)
        $('#resetFilters').click(function() {
            $('#filterStaff').val('');
            $('#filterLeaveType').val('');
            $('#filterStatus').val('');
            calendar.refetchEvents();
        });
    });
</script>

<!-- Petit CSS pour le loader -->
<style>
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999;
    }
    .fc {
        position: relative;
    }
</style>