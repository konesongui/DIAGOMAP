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
                        <div class="calendar-filters">
                            <!-- Première ligne : employé, type, statut -->
                            <div class="row">
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

                            <!-- Nouvelle ligne : Période (dates) -->
                            <div class="row" style="margin-top:10px;">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Date de début</label>
                                        <input type="date" id="filterStartDate" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Date de fin</label>
                                        <input type="date" id="filterEndDate" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button id="resetFilters" class="btn btn-default btn-block">
                                            <i class="fa fa-undo"></i> Réinitialiser
                                        </button>
                                    </div>
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
                            <div class="legend-item">
                                <span class="legend-color" style="background:#ff4d4f;"></span> Chevauchement (alerte)
                            </div>
                        </div>

                        <!-- Zone d'alerte pour chevauchements -->
                        <div id="overlapAlerts" style="margin-top:10px; display:none;"></div>

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
        // Mapping staff id -> display name (utilisé pour les alertes de chevauchement)
        var staffNames = {};
        <?php foreach ($staff_list as $s): ?>
        staffNames['<?= $s['id'] ?>'] = "<?= addslashes($s['name'] . ' ' . $s['surname']) ?>";
        <?php endforeach; ?>

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
                    // Envoyer uniquement nos filtres; FullCalendar ajoute 'start' et 'end' automatiquement pour la plage visible.
                    var params = {
                        staff_id: $('#filterStaff').val(),
                        leave_type_id: $('#filterLeaveType').val(),
                        status: $('#filterStatus').val()
                    };
                    // Si l'utilisateur a renseigné une période via les champs de filtre, transmettre ces dates en tant que 'start' et 'end'
                    if ($('#filterStartDate').val()) params.start = $('#filterStartDate').val();
                    if ($('#filterEndDate').val()) params.end = $('#filterEndDate').val();
                    return params;
                },

                failure: function() {
                    alert('Erreur de chargement des événements');
                }
            }],

            // Quand FullCalendar a appliqué les événements, analyser les chevauchements
            eventsSet: function(events) {
                // Cacher et vider l'alerte
                $('#overlapAlerts').hide().empty();

                // Préparer une représentation plus facile des intervalles (dates inclusives)
                var evs = events.map(function(ev) {
                    var s = ev.start ? new Date(ev.start) : null;
                    var e = ev.end ? new Date(ev.end) : new Date(ev.start);
                    // FullCalendar utilise end exclusif; rendre end inclusif
                    if (ev.end) e.setDate(e.getDate() - 1);
                    return { id: ev.id, start: s, end: e, staff_id: ev.extendedProps ? ev.extendedProps.staff_id : null, title: ev.title, obj: ev };
                });

                var conflicts = [];
                for (var i = 0; i < evs.length; i++) {
                    for (var j = i + 1; j < evs.length; j++) {
                        var a = evs[i], b = evs[j];
                        if (!a.start || !b.start) continue;
                        if (!a.staff_id || !b.staff_id) continue;
                        if (a.staff_id == b.staff_id) continue; // même personne -> pas d'alerte

                        // Vérifier chevauchement (a.start <= b.end && b.start <= a.end)
                        if (a.start <= b.end && b.start <= a.end) {
                            conflicts.push({ a: a, b: b });
                        }
                    }
                }

                if (conflicts.length > 0) {
                    $('#overlapAlerts').show().html('<div class="alert alert-danger"><strong>Alerte :</strong> chevauchements détectés.</div>');
                    var ul = $('<ul></ul>');
                    conflicts.forEach(function(c) {
                        var rangeStart = new Date(Math.max(c.a.start.getTime(), c.b.start.getTime()));
                        var rangeEnd = new Date(Math.min(c.a.end.getTime(), c.b.end.getTime()));
                        var rangeStr = rangeStart.toLocaleDateString('fr-FR') + ' — ' + rangeEnd.toLocaleDateString('fr-FR');
                        var aName = staffNames[c.a.staff_id] || ('ID ' + c.a.staff_id);
                        var bName = staffNames[c.b.staff_id] || ('ID ' + c.b.staff_id);
                        var li = $('<li></li>').text(rangeStr + ': ' + aName + ' ↔ ' + bName + ' (' + c.a.title + ' / ' + c.b.title + ')');
                        ul.append(li);

                        // Mettre en surbrillance les événements en conflit
                        try {
                            c.a.obj.setProp('borderColor', '#ff4d4f');
                            c.a.obj.setProp('backgroundColor', '#ffcccc');
                            c.b.obj.setProp('borderColor', '#ff4d4f');
                            c.b.obj.setProp('backgroundColor', '#ffcccc');
                        } catch (e) {
                            // certain environnements peuvent empêcher setProp sur EventApi; ignorer
                        }
                    });
                    $('#overlapAlerts').append(ul);
                } else {
                    // Aucun conflit : restituer les couleurs selon le statut
                    events.forEach(function(ev) {
                        var props = ev.extendedProps || {};
                        var color = '#28a745';
                        var textColor = '#ffffff';
                        if (props.status == 'pending') { color = '#ffc107'; textColor = '#000000'; }
                        else if (props.status == 'disapprove') { color = '#dc3545'; textColor = '#ffffff'; }
                        try {
                            ev.setProp('backgroundColor', color);
                            ev.setProp('borderColor', color);
                            ev.setProp('textColor', textColor);
                        } catch (e) {}
                    });
                }
            },

            eventClick: function(info) {
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

        // Réinitialiser tous les filtres (incluant la période)
        $('#resetFilters').click(function() {
            $('#filterStaff').val('');
            $('#filterLeaveType').val('');
            $('#filterStatus').val('');
            $('#filterStartDate').val('');
            $('#filterEndDate').val('');
            calendar.refetchEvents();
        });
    });
</script>