<?php
// process_payment.php
session_start();
require_once '../system/database.php'; // Ajustez selon votre structure

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Méthode non autorisée']));
}

// Récupérer les données
$invoice_id   = isset($_POST['invoice_id']) ? intval($_POST['invoice_id']) : 0;
$amount       = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
$payment_date = isset($_POST['payment_date']) ? $_POST['payment_date'] : date('Y-m-d');
$method       = isset($_POST['method']) ? $_POST['method'] : '';
$reference    = isset($_POST['reference']) ? $_POST['reference'] : '';
$source_type  = isset($_POST['source_type']) ? $_POST['source_type'] : 'caisse';
$source_id    = isset($_POST['source_id']) ? intval($_POST['source_id']) : 0;
$notes        = isset($_POST['notes']) ? $_POST['notes'] : '';

// Validation
if ($invoice_id <= 0 || $amount <= 0 || empty($method) || empty($source_type) || $source_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

// Connexion à la base de données
$db = new Database(); // Ajustez selon votre connexion

try {
    // Vérifier la facture
    $invoice = $db->query("SELECT * FROM invoices WHERE id = ?", [$invoice_id])->fetch();
    if (!$invoice) {
        throw new Exception('Facture introuvable');
    }

    // Vérifier le montant restant
    if ($amount > $invoice['remaining_amount']) {
        throw new Exception('Montant supérieur au reste à payer');
    }

    // Démarrer la transaction
    $db->beginTransaction();

    // 1. Ajouter le paiement
    $payment_query = "INSERT INTO invoice_payments 
                     (invoice_id, amount, payment_date, method, reference, source_type, source_id, notes, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $db->query($payment_query, [
        $invoice_id, $amount, $payment_date, $method, $reference,
        $source_type, $source_id, $notes
    ]);
    $payment_id = $db->lastInsertId();

    // 2. Mettre à jour la source selon le type
    if ($source_type === 'caisse') {
        // Vérifier la caisse
        $caisse = $db->query("SELECT * FROM income WHERE id = ? AND est_actif = 1", [$source_id])->fetch();
        if (!$caisse) {
            throw new Exception('Caisse introuvable ou inactive');
        }

        // Ajouter à operation_caisse
        $operation_query = "INSERT INTO operation_caisse 
                           (reference, type_operation, montant, designation, caisse_id, date, 
                            entree, sortie, note, est_actif, created_at) 
                           VALUES (?, 'ENTREE', ?, ?, ?, ?, ?, 0, ?, 1, NOW())";

        $db->query($operation_query, [
            $reference ?: 'FACT-' . $invoice['invoice_number'],
            $amount,
            'Paiement facture #' . $invoice['invoice_number'],
            $source_id,
            $payment_date,
            $notes
        ]);
        $operation_id = $db->lastInsertId();

        // Mettre à jour le solde de la caisse
        $db->query("UPDATE income SET amount_re = amount_re + ? WHERE id = ?", [$amount, $source_id]);

        // Récupérer le nouveau solde
        $new_balance = $db->query("SELECT amount_re FROM income WHERE id = ?", [$source_id])->fetch()['amount_re'];

    } else if ($source_type === 'banque') {
        // Vérifier la banque
        $banque = $db->query("SELECT * FROM banks WHERE id = ? AND status = 1", [$source_id])->fetch();
        if (!$banque) {
            throw new Exception('Banque introuvable ou inactive');
        }

        // Ajouter à la table bank
        $bank_query = "INSERT INTO bank 
                      (bank_id, date, transaction_type, designation, name, amount, 
                       reference, payment_mode, note, created_at) 
                      VALUES (?, ?, 'CREDIT', 'Dépôt', ?, ?, ?, ?, ?, NOW())";

        $db->query($bank_query, [
            $source_id,
            $payment_date,
            'Paiement facture #' . $invoice['invoice_number'],
            $amount,
            $reference,
            $method,
            $notes
        ]);
        $bank_transaction_id = $db->lastInsertId();

        // Mettre à jour le solde de la banque
        $db->query("UPDATE banks SET balance = balance + ? WHERE id = ?", [$amount, $source_id]);

        // Récupérer le nouveau solde
        $new_balance = $db->query("SELECT balance FROM banks WHERE id = ?", [$source_id])->fetch()['balance'];
    }

    // 3. Mettre à jour la facture
    $new_remaining = $invoice['remaining_amount'] - $amount;
    $status = ($new_remaining <= 0) ? 'paid' : 'pending';

    $update_invoice = "UPDATE invoices 
                      SET amount_paid = amount_paid + ?, 
                          remaining_amount = ?,
                          status = ?,
                          updated_at = NOW()
                      WHERE id = ?";

    $db->query($update_invoice, [$amount, $new_remaining, $status, $invoice_id]);

    // 4. Journalisation dans mouvements
    $mouvement_query = "INSERT INTO mouvements 
                       (type_mouvement, montant, reference_piece, category, date_operation, 
                        mode_paiement, source_type, source_id, source_name, 
                        solde_apres_operation, created_at) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $db->query($mouvement_query, [
        'entree',
        $amount,
        $reference,
        'Vente de produit',
        $payment_date,
        $method,
        $source_type,
        $source_id,
        $source_type === 'caisse' ? $caisse['name'] : $banque['name'],
        $new_balance
    ]);

    // Valider la transaction
    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Paiement enregistré avec succès',
        'data' => [
            'payment_id' => $payment_id,
            'invoice_id' => $invoice_id,
            'remaining' => $new_remaining,
            'source_type' => $source_type,
            'new_balance' => $new_balance
        ]
    ]);

} catch (Exception $e) {
    // Annuler en cas d'erreur
    if (isset($db)) {
        $db->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
?>