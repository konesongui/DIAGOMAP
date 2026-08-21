<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-object-group"></i> Gestion des objectifs</h1>
    </section>

    <section class="content">
        <div class="row">
            <!-- Formulaire objectif annuel (directeur) -->
            <?php if ($this->rbac->hasPrivilege('clients', 'can_add')) { ?>
                <div class="col-md-4">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Ajouter un objectif annuel</h3>
                        </div>
                        <form action="<?php echo site_url('admin/objectifs/create'); ?>" method="post">
                            <div class="box-body">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="form-group">
                                    <label>Montant (FCFA) <small class="req">*</small></label>
                                    <input type="number" name="amount" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Date <small class="req">*</small></label>
                                    <input type="date" name="date" class="form-control" required>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>

            <!-- Liste des objectifs annuels -->
            <div class="col-md-<?php echo ($this->rbac->hasPrivilege('clients', 'can_add')) ? '8' : '12'; ?>">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title">Objectifs annuels</h3>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>Montant</th>
                                    <th>Date</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($annual_objectives as $ao) { ?>
                                    <tr>
                                        <td><?php echo number_format($ao['amount'], 0, ',', ' '); ?> FCFA</td>
                                        <td><?php echo $ao['date']; ?></td>
                                        <td class="text-right">
                                            <!-- Bouton Attribution (accessible au responsable commercial) -->
                                            <button type="button" class="btn btn-primary btn-xs"
                                                    onclick="openAttributionModal(<?php echo $ao['id']; ?>, '<?php echo $ao['amount']; ?>')">
                                                <i class="fa fa-users"></i> Attribuer
                                            </button>
                                            <?php if ($this->rbac->hasPrivilege('clients', 'can_edit')) { ?>
                                                <a href="<?php echo site_url('admin/objectifs/edit/'.$ao['id']); ?>" class="btn btn-default btn-xs">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            <?php } ?>
                                            <?php if ($this->rbac->hasPrivilege('clients', 'can_delete')) { ?>
                                                <a href="<?php echo site_url('admin/objectifs/delete/'.$ao['id']); ?>" class="btn btn-default btn-xs" onclick="return confirm('Supprimer cet objectif annuel ?')">
                                                    <i class="fa fa-trash text-danger"></i>
                                                </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- MODAL : Attribution des objectifs -->
<!-- MODAL : Attribution des objectifs -->
<div class="modal fade" id="attributionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Attribuer l'objectif annuel</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="current_annual_id" value="">
                <div class="alert alert-info" id="annualAmountDisplay"></div> <!-- Affichage montant annuel -->
                <!-- Formulaire d'ajout / modification d'une attribution -->
                <div class="well">
                    <h4>Nouvelle attribution</h4>
                    <form id="assignmentForm">
                        <input type="hidden" id="assignment_id" name="assignment_id">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Commercial *</label>
                                <select id="commercial_name" name="commercial_name" class="form-control" required>
                                    <option value="">Sélectionner</option>
                                    <?php foreach ($stff_list as $staff) { ?>
                                        <option value="<?php echo $staff['name'].' '.$staff['surname']; ?>">
                                            <?php echo $staff['name'].' '.$staff['surname']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Montant *</label>
                                <input type="number" id="amount" name="amount" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label>Période du *</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label>Au *</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-success">Enregistrer l'attribution</button>
                        <button type="button" class="btn btn-default" onclick="resetAssignmentForm()">Annuler</button>
                    </form>
                </div>

                <!-- Liste des attributions existantes -->
                <h4>Attributions déjà effectuées</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="assignmentsTable">
                        <thead>
                        <tr><th>Commercial</th><th>Montant</th><th>Période du</th><th>Au</th><th>Actions</th></tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
    var currentAnnualId = null;
    var currentAnnualAmount = 0;

    function openAttributionModal(annualId, annualAmount) {
        currentAnnualId = annualId;
        currentAnnualAmount = parseFloat(annualAmount);
        $('#current_annual_id').val(annualId);
        $('#amount').val('');
        $('#annualAmountDisplay').html('Objectif annuel : <strong>' + parseInt(annualAmount).toLocaleString() + ' FCFA</strong>');
        $('#amount').attr('max', annualAmount); // limitation simple
        resetAssignmentForm();
        loadAssignments(annualId);
        $('#attributionModal').modal('show');
    }

    function loadAssignments(annualId) {
        $.get('<?php echo site_url("admin/objectifs/get_assignments/"); ?>' + annualId, function(data) {
            var tbody = $('#assignmentsTable tbody');
            tbody.empty();
            $.each(data, function(i, ass) {
                var row = '<tr>' +
                    '<td>' + ass.commercial_name + '</td>' +
                    '<td>' + parseInt(ass.amount).toLocaleString() + ' FCFA</td>' +
                    '<td>' + ass.start_date + '</td>' +
                    '<td>' + ass.end_date + '</td>' +
                    '<td>' +
                    '<button class="btn btn-info btn-xs" onclick="editAssignment(' + ass.id + ', \'' + ass.commercial_name + '\', ' + ass.amount + ', \'' + ass.start_date + '\', \'' + ass.end_date + '\')"><i class="fa fa-edit"></i> Modifier</button> ' +
                    '<button class="btn btn-danger btn-xs" onclick="deleteAssignment(' + ass.id + ')"><i class="fa fa-trash"></i> Supprimer</button>' +
                    '</td>' +
                    '</tr>';
                tbody.append(row);
            });
        }, 'json');
    }

    function resetAssignmentForm() {
        $('#assignment_id').val('');
        $('#commercial_name').val('');
        $('#amount').val('');
        $('#start_date').val('');
        $('#end_date').val('');
    }

    function editAssignment(id, name, amount, start, end) {
        $('#assignment_id').val(id);
        $('#commercial_name').val(name);
        $('#amount').val(amount);
        $('#start_date').val(start);
        $('#end_date').val(end);
    }

    function deleteAssignment(id) {
        if (confirm('Supprimer cette attribution ?')) {
            $.post('<?php echo site_url("admin/objectifs/delete_assignment/"); ?>' + id, function(res) {
                if (res.status === 'success') {
                    loadAssignments(currentAnnualId);
                    resetAssignmentForm();
                } else {
                    alert('Erreur lors de la suppression');
                }
            }, 'json');
        }
    }

    $('#assignmentForm').on('submit', function(e) {
        e.preventDefault();
        var newAmount = parseFloat($('#amount').val());
        if (newAmount > currentAnnualAmount) {
            alert('Le montant attribué ne peut pas dépasser l\'objectif annuel (' + currentAnnualAmount.toLocaleString() + ' FCFA).');
            return;
        }
        var assignmentId = $('#assignment_id').val();
        var url = assignmentId === ''
            ? '<?php echo site_url("admin/objectifs/add_assignment"); ?>'
            : '<?php echo site_url("admin/objectifs/update_assignment"); ?>';
        var data = {
            annual_objective_id: currentAnnualId,
            commercial_name: $('#commercial_name').val(),
            amount: $('#amount').val(),
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val()
        };
        if (assignmentId !== '') {
            data.id = assignmentId;
        }
        $.post(url, data, function(res) {
            if (res.status === 'success') {
                loadAssignments(currentAnnualId);
                resetAssignmentForm();
            } else {
                alert(res.message || "Erreur lors de l'enregistrement");
            }
        }, 'json');
    });
</script>

