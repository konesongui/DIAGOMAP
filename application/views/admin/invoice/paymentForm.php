<?php
    $formID = 'paymentForm';
    $submitID = 'paymentSubmit';

    $remaining = (float) str_replace(',', '.', str_replace(' ', '', $remaining));

    // var_dump($remaining);
    // exit;
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="paymentModalLabel">Ajouter un paiement</h4>
</div>
<form id="<?= $formID ?>">
    <div class="modal-body">
        <input type="hidden" name="invoice_id" id="payment_invoice_id" value="<?= $rowID ?>">
        
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
                    <input type="text" class="form-control date" id="payment_date" name="payment_date" required>
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
                        <option value="check">Chèque</option>
                        <option value="bank_transfer">Virement</option>
                        <option value="card">Carte bancaire</option>
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
        
        <!-- Champ Notes en pleine largeur -->
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <button type="submit" id="<?= $submitID ?>" class="btn btn-primary">Enregistrer</button>
    </div>
</form>