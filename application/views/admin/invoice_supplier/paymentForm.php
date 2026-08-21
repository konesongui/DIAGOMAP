<style>
    /* Style pour les sélecteurs */
    #caisse_id option, #banque_id option {
        padding: 8px;
        border-bottom: 1px solid #eee;
    }

    #caisse_id option:last-child, #banque_id option:last-child {
        border-bottom: none;
    }

    /* Style pour l'alerte d'information */
    #source_info .alert {
        margin-top: 10px;
        border-radius: 4px;
        border-left: 4px solid #3498db;
    }

    /* Style pour les groupes de champs */
    .form-group {
        margin-bottom: 15px;
    }

    /* Indicateur visuel pour les champs requis */
    .text-danger {
        color: #e74c3c;
    }
</style>

<?php
$formID = 'paymentForm';
$submitID = 'paymentSubmit';
$remaining = (float) str_replace(',', '.', str_replace(' ', '', $remaining));

$CI = &get_instance();

// Connexion avec les paramètres de CodeIgniter
$conn = new mysqli($CI->db->hostname, $CI->db->username, $CI->db->password, $CI->db->database);

// Vérification
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

// Activer les erreurs (optionnel)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Récupérer les CAISSES actives et non fermées
$sqlCaisses = "SELECT id, name, amount_re as solde_actuel 
               FROM income 
               WHERE est_actif = 1 AND is_deleted = 'no' 
               ORDER BY name ASC";
$resultCaisses = $conn->query($sqlCaisses);
$caisses = [];
if ($resultCaisses && $resultCaisses->num_rows > 0) {
    while($caisse = $resultCaisses->fetch_assoc()) {
        $caisses[] = $caisse;
    }
}

// Récupérer les BANQUES actives
$sqlBanques = "SELECT id, name as nom, balance as solde 
               FROM banks 
               WHERE status = 1 
               ORDER BY name ASC";
$resultBanques = $conn->query($sqlBanques);
$banques = [];
if ($resultBanques && $resultBanques->num_rows > 0) {
    while($banque = $resultBanques->fetch_assoc()) {
        $banques[] = $banque;
    }
}

$conn->close();
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="paymentModalLabel">Ajouter un paiement</h4>
</div>
<form id="<?= $formID ?>">
    <div class="modal-body">
        <input type="hidden" name="invoice_id" id="payment_invoice_id" value="<?= $rowID ?>">
        <input type="hidden" id="source_type" name="source_type" value="caisse">

        <div class="row">
            <!-- Première ligne - Montant et Date -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="amount">Montant <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="amount" name="amount" value="<?= $remaining ;?>" step="0.01" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="payment_date">Date de paiement <span class="text-danger">*</span></label>
                    <input type="text" class="form-control datepicker" id="payment_date" name="payment_date" required>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Deuxième ligne - Méthode et Référence -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="method">Méthode de paiement <span class="text-danger">*</span></label>
                    <select class="form-control" id="method" name="method" required>
                        <option value="">Sélectionner...</option>
                        <option value="cash">Espèces</option>
                        <!--<option value="check">Chèque</option>
                        <option value="bank_transfer">Virement</option>
                        <option value="card">Carte bancaire</option>-->
                        <option value="bank">Paiement bancaire</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="reference">Référence</label>
                    <input type="text" class="form-control" id="reference" name="reference">
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Troisième ligne - Source de paiement (Caisse OU Banque) -->
            <div class="col-md-6">
                <!-- Champ CAISSE -->
                <div class="form-group" id="caisse_group">
                    <label for="caisse_id">Caisse <span class="text-danger">*</span></label>
                    <select class="form-control" id="caisse_id" name="caisse_id">
                        <option value="">Sélectionner une caisse...</option>
                        <?php if (!empty($caisses)): ?>
                            <?php foreach ($caisses as $caisse): ?>
                                <option value="<?= $caisse['id'] ?>" data-type="caisse" data-balance="<?= $caisse['solde_actuel'] ?>">
                                    <?= htmlspecialchars($caisse['name']) ?>
                                    (Solde: <?= number_format($caisse['solde_actuel'], 0, ',', ' ') ?> FCFA)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">Aucune caisse active disponible</option>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Champ BANQUE -->
                <div class="form-group" id="banque_group" style="display: none;">
                    <label for="banque_id">Banque <span class="text-danger">*</span></label>
                    <select class="form-control" id="banque_id" name="banque_id">
                        <option value="">Sélectionner une banque...</option>
                        <?php if (!empty($banques)): ?>
                            <?php foreach ($banques as $banque): ?>
                                <option value="<?= $banque['id'] ?>" data-type="banque" data-balance="<?= $banque['solde'] ?>">
                                    <?= htmlspecialchars($banque['nom']) ?>
                                    (Solde: <?= number_format($banque['solde'], 0, ',', ' ') ?> FCFA)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">Aucune banque active disponible</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
            </div>
        </div>

        <!-- Information de la source sélectionnée -->
        <div class="row" id="source_info" style="display: none;">
            <div class="col-12">
                <div class="alert alert-info" style="padding: 10px; margin-bottom: 0;">
                    <i class="fa fa-info-circle"></i>
                    <span id="selected_source_info"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <button type="submit" id="<?= $submitID ?>" class="btn btn-primary">
            Enregistrer le paiement
        </button>
    </div>
</form>
<script>
    $(document).ready(function() {
        // Initialiser le datepicker
        $('#payment_date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            language: 'fr'
        }).datepicker('setDate', new Date());

        // Gérer la sélection de la méthode de paiement
        $('#method').change(function() {
            var method = $(this).val();

            // Montrer/masquer les champs appropriés
            if (method === 'cash' || method === 'check' || method === 'card') {
                // Paiement via caisse
                $('#caisse_group').show();
                $('#banque_group').hide();
                $('#caisse_id').prop('required', true);
                $('#banque_id').prop('required', false);
                $('#source_type').val('caisse');

                // Sélectionner automatiquement la première caisse si disponible
                if (method === 'cash' && $('#caisse_id option').length > 1) {
                    $('#caisse_id').val($('#caisse_id option:eq(1)').val()).trigger('change');
                }
            } else if (method === 'bank_transfer' || method === 'bank') {
                // Paiement via banque
                $('#caisse_group').hide();
                $('#banque_group').show();
                $('#caisse_id').prop('required', false);
                $('#banque_id').prop('required', true);
                $('#source_type').val('banque');

                // Sélectionner automatiquement la première banque si disponible
                if ($('#banque_id option').length > 1) {
                    $('#banque_id').val($('#banque_id option:eq(1)').val()).trigger('change');
                }
            } else {
                // Masquer les deux
                $('#caisse_group').hide();
                $('#banque_group').hide();
                $('#caisse_id').prop('required', false);
                $('#banque_id').prop('required', false);
            }

            // Gérer le champ référence
            if (method === 'cash') {
                $('#reference').closest('.form-group').fadeOut();
            } else {
                $('#reference').closest('.form-group').fadeIn();
            }

            // Mettre à jour l'info de source
            updateSourceInfo();
        });

        // Mettre à jour l'information de la source sélectionnée
        function updateSourceInfo() {
            var method = $('#method').val();
            var sourceText = '';

            if (method === 'cash' || method === 'check' || method === 'card') {
                var selectedCaisse = $('#caisse_id option:selected').text();
                if ($('#caisse_id').val()) {
                    sourceText = 'Source : ' + selectedCaisse;
                    $('#source_info').fadeIn();
                } else {
                    $('#source_info').fadeOut();
                }
            } else if (method === 'bank_transfer' || method === 'bank') {
                var selectedBanque = $('#banque_id option:selected').text();
                if ($('#banque_id').val()) {
                    sourceText = 'Source : ' + selectedBanque;
                    $('#source_info').fadeIn();
                } else {
                    $('#source_info').fadeOut();
                }
            } else {
                $('#source_info').fadeOut();
            }

            $('#selected_source_info').text(sourceText);
        }

        // Écouter les changements sur les sélecteurs
        $('#caisse_id, #banque_id').change(updateSourceInfo);

        // Initialiser l'affichage
        $('#method').trigger('change');

        // Soumission du formulaire
        $('#<?= $formID ?>').submit(function(e) {
            e.preventDefault();

            var formData = $(this).serialize();
            var submitBtn = $('#<?= $submitID ?>');
            var originalText = submitBtn.html();

            // Validation
            var method = $('#method').val();
            var isValid = true;
            var errorMessage = '';

            if (!method) {
                errorMessage = 'Veuillez sélectionner une méthode de paiement';
                isValid = false;
            } else if ((method === 'cash' || method === 'check' || method === 'card') && !$('#caisse_id').val()) {
                errorMessage = 'Veuillez sélectionner une caisse';
                isValid = false;
            } else if ((method === 'bank_transfer' || method === 'bank') && !$('#banque_id').val()) {
                errorMessage = 'Veuillez sélectionner une banque';
                isValid = false;
            }

            if (!isValid) {
                alert(errorMessage);
                return false;
            }

            // Désactiver le bouton pendant l'envoi
            submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Enregistrement...');
            submitBtn.prop('disabled', true);

            // Préparer les données pour l'API
            var dataToSend = {
                invoice_id: $('#payment_invoice_id').val(),
                amount: $('#amount').val(),
                payment_date: $('#payment_date').val(),
                method: method,
                reference: $('#reference').val(),
                notes: $('#notes').val(),
                source_type: $('#source_type').val()
            };

            // Ajouter l'ID de la source selon le type
            if (dataToSend.source_type === 'caisse') {
                dataToSend.source_id = $('#caisse_id').val();
            } else {
                dataToSend.source_id = $('#banque_id').val();
            }

            $.ajax({
                url: 'process_payment.php', // Fichier qui traitera le paiement
                type: 'POST',
                data: dataToSend,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Afficher un message de succès
                        toastr.success(response.message || 'Paiement enregistré avec succès');

                        // Fermer le modal après un délai
                        setTimeout(function() {
                            $('#modal-lg').modal('hide');

                            // Recharger la page ou mettre à jour les données
                            if (typeof reloadTable === 'function') {
                                reloadTable();
                            } else {
                                location.reload();
                            }
                        }, 1500);
                    } else {
                        // Afficher l'erreur
                        toastr.error(response.message || 'Erreur lors de l\'enregistrement');
                        submitBtn.html(originalText);
                        submitBtn.prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Erreur réseau: ' + error);
                    submitBtn.html(originalText);
                    submitBtn.prop('disabled', false);
                }
            });
        });
    });

    // Fonction pour basculer entre caisse et banque
    function toggleSourceFields() {
        var method = $('#method').val();

        if (method === 'cash' || method === 'check' || method === 'card') {
            // Montrer caisse, cacher banque
            $('#caisse_group').show();
            $('#banque_group').hide();
            $('#caisse_id').prop('required', true);
            $('#banque_id').prop('required', false);
            $('#source_type').val('caisse');
        } else if (method === 'bank_transfer' || method === 'bank') {
            // Montrer banque, cacher caisse
            $('#caisse_group').hide();
            $('#banque_group').show();
            $('#caisse_id').prop('required', false);
            $('#banque_id').prop('required', true);
            $('#source_type').val('banque');
        }
    }
</script>

