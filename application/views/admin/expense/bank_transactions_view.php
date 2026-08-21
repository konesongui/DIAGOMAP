<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

// Vérification des variables pour éviter les erreurs
$bank = $bank ?? null;
$transactions = $transactions ?? [];
$total_credit = $total_credit ?? 0;
$total_debit = $total_debit ?? 0;
$total_balance = $total_balance ?? 0;
?>
<div class="container-fluid">
    <!-- Informations de la banque -->
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                <h4 style="margin-top: 0;">
                    <i class="fa fa-bank"></i>
                    <?php echo $bank ? htmlspecialchars($bank->name) : 'Banque inconnue'; ?>
                    <small><?php echo $bank ? '(' . htmlspecialchars($bank->code) . ')' : ''; ?></small>
                </h4>
                <p>
                    <?php if ($bank): ?>
                        <strong>Numéro de compte:</strong>
                        <?php echo !empty($bank->account_number) ? htmlspecialchars($bank->account_number) : 'Non spécifié'; ?> |
                        <strong>Solde actuel:</strong>
                        <span class="badge <?php echo ($bank->balance >= 0) ? 'badge-success' : 'badge-danger'; ?>">
                            <?php echo number_format($bank->balance, 2, ',', ' ') . ' ' . $currency_symbol; ?>
                        </span>
                    <?php else: ?>
                        <em class="text-warning">Informations de la banque non disponibles</em>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row">
        <div class="col-md-4">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?php echo number_format($total_credit, 2, ',', ' ') . ' ' . $currency_symbol; ?></h3>
                    <p>Total Crédits</p>
                </div>
                <div class="icon">
                    <i class="fa fa-arrow-up"></i> <!-- ✅ CORRIGÉ : flèche vers le haut pour crédits -->
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?php echo number_format($total_debit, 2, ',', ' ') . ' ' . $currency_symbol; ?></h3>
                    <p>Total Débits</p>
                </div>
                <div class="icon">
                    <i class="fa fa-arrow-down"></i> <!-- ✅ CORRIGÉ : flèche vers le bas pour débits -->
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3><?php echo number_format($total_balance, 2, ',', ' ') . ' ' . $currency_symbol; ?></h3>
                    <p>Solde Final</p>
                </div>
                <div class="icon">
                    <i class="fa fa-balance-scale"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des transactions -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Liste des Transactions</h3>
                    <div class="box-tools">
                        <span class="badge bg-blue"><?php echo count($transactions); ?> transaction(s)</span>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <?php if (!empty($transactions)): ?>
                        <table class="table table-striped table-bordered table-hover" style="width: 100%;">
                            <thead>
                            <tr>
                                <th width="10%">Date</th>
                                <th width="20%">Libellé</th>
                                <th width="15%">Type</th>
                                <th width="10%">Désignation</th>
                                <th width="15%" class="text-right">Montant</th>
                                <th width="15%">Référence</th>
                                <th width="10%">Mode Paiement</th>
                                <th width="5%">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($transactions as $transaction): ?>
                                <?php
                                // Déterminer les classes CSS
                                $is_credit = ($transaction->designation == 'Crédit') ||
                                    in_array($transaction->transaction_type, ['Dépôt', 'Virement entrant', 'Crédit']);
                                $type_class = $is_credit ? 'label-success' : 'label-danger';
                                $amount_class = $is_credit ? 'text-success' : 'text-danger';
                                ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($transaction->date)); ?></td>
                                    <td><?php echo htmlspecialchars($transaction->name); ?></td>
                                    <td>
                                        <span class="label <?php echo $type_class; ?>">
                                            <?php echo htmlspecialchars($transaction->transaction_type); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="label <?php echo $amount_class; ?>">
                                            <?php echo htmlspecialchars($transaction->designation); ?>
                                        </span>
                                    </td>
                                    <td class="text-right <?php echo $amount_class; ?>">
                                        <strong>
                                            <?php echo number_format($transaction->amount, 2, ',', ' ') . ' ' . $currency_symbol; ?>
                                        </strong>
                                    </td>
                                    <td><?php echo !empty($transaction->reference) ? htmlspecialchars($transaction->reference) : '-'; ?></td>
                                    <td><?php echo !empty($transaction->payment_mode) ? htmlspecialchars($transaction->payment_mode) : '-'; ?></td>
                                    <td>
                                        <button class="btn btn-xs btn-info view-transaction-details"
                                                data-id="<?php echo $transaction->id; ?>"
                                                title="Voir détails">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                            <tr class="bg-gray">
                                <td colspan="4" class="text-right"><strong>Totaux :</strong></td>
                                <td class="text-right">
                                    <div class="text-success">
                                        <strong>Crédits: <?php echo number_format($total_credit, 2, ',', ' ') . ' ' . $currency_symbol; ?></strong>
                                    </div>
                                    <div class="text-danger">
                                        <strong>Débits: <?php echo number_format($total_debit, 2, ',', ' ') . ' ' . $currency_symbol; ?></strong>
                                    </div>
                                    <div class="text-primary">
                                        <strong>Solde: <?php echo number_format($total_balance, 2, ',', ' ') . ' ' . $currency_symbol; ?></strong>
                                    </div>
                                </td>
                                <td colspan="3"></td>
                            </tr>
                            </tfoot>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning text-center">
                            <i class="fa fa-info-circle"></i> Aucune transaction trouvée pour cette banque
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Bouton pour voir les détails d'une transaction
        $('.view-transaction-details').click(function(e) {
            e.stopPropagation(); // Empêcher la propagation de l'événement
            var transactionId = $(this).data('id');

            // Afficher un indicateur de chargement
            $('#transactionDetailsContent').html(
                '<div class="text-center p-4">' +
                '<div class="spinner-border text-primary" role="status">' +
                '<span class="sr-only">Chargement...</span>' +
                '</div>' +
                '<p class="mt-2">Chargement des détails...</p>' +
                '</div>'
            );

            $('#transactionDetailsModal').modal('show');

            $.ajax({
                url: '<?php echo base_url(); ?>admin/expense/get_bank_transaction/' + transactionId,
                type: 'GET',
                success: function(response) {
                    $('#transactionDetailsContent').html(response);
                },
                error: function(xhr, status, error) {
                    $('#transactionDetailsContent').html(
                        '<div class="alert alert-danger m-3">' +
                        '<i class="fa fa-exclamation-circle"></i> ' +
                        'Erreur lors du chargement des détails: ' + error +
                        '</div>'
                    );
                }
            });
        });

        // Fermer le modal quand on clique en dehors
        $('#transactionDetailsModal').on('hidden.bs.modal', function () {
            $('#transactionDetailsContent').html(''); // Vider le contenu
        });
    });
</script>

<!-- Modal pour les détails de transaction -->
<div class="modal fade" id="transactionDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-info-circle"></i> Détails de la Transaction
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="transactionDetailsContent">
                <!-- Contenu chargé par AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Fermer
                </button>
            </div>
        </div>
    </div>
</div>