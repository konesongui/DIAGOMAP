<?php
    $formID     = 'invoiceEditForm';
    $submitBtn  = 'submitEditBtn';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
      <h1>
          <?php echo $title; ?>
          <small><?php echo $title_list; ?></small>
      </h1>
      <ol class="breadcrumb">
          <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-dashboard"></i> Accueil</a></li>
          <li><a href="<?php echo base_url(); ?>admin/invoice">Factures</a></li>
          <li class="active"><?php echo $title; ?></li>
      </ol>
  </section>

  <!-- Main content -->
  <section class="content">
      <div class="row">
          <div class="col-md-12">
              <div class="box box-primary">
                  <div class="box-header with-border">
                      <h3 class="box-title"><?php echo $title; ?></h3>
                  </div>
                  <form id="<?= $formID ?>" method="post" action="<?php echo base_url(); ?>admin/invoice/update">
                      <input type="hidden" name="id" value="<?php echo $invoice['id']; ?>">
                      <div class="box-body">
                          <div class="row">
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label for="customer">Client <span class="text-danger">*</span></label>
                                      <select class="form-control" id="customer" name="customer" required>
                                          <option value="">Sélectionner un client...</option>
                                          <?php foreach ($clients as $client) { ?>
                                              <option value="<?php echo $client['id']; ?>" <?php echo ($client['id'] == $invoice['customer_id']) ? 'selected' : ''; ?>><?php echo $client['item_supplier'] .' ' . $client['lastname']. ' (' . $client['phone'] . ')'; ?></option>
                                          <?php } ?>
                                      </select>
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="form-group">
                                      <label for="invoice_date">Date de facture <span class="text-danger">*</span></label>
                                      <input type="text" class="form-control date" id="invoice_date" name="invoice_date" value="<?php echo date('d/m/Y', strtotime($invoice['invoice_date'])); ?>" required>
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="form-group">
                                      <label for="due_date">Date d'échéance <span class="text-danger">*</span></label>
                                      <input type="text" class="form-control date" id="due_date" name="due_date" value="<?php echo date('d/m/Y', strtotime($invoice['due_date'])); ?>" required>
                                  </div>
                              </div>
                          </div>

                          <div class="row">
                            <div class="col-md-12">
                                <h4>Choisissez les articles</h4>
                                <div id="items-container">
                                    <?php foreach ($invoice['items'] as $item) { ?>
                                        <div class="repeater-item">
                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <label>Catégorie <small class="req">*</small></label>
                                                    <select name="item_category_id[]" class="form-control item-category" required>
                                                        <option value="">Sélectionner</option>
                                                        <?php foreach ($itemcatlist as $category) { ?>
                                                            <option value="<?php echo $category['id']; ?>" <?php echo ((int)$category['id'] == (int)$item['category_id']) ? 'selected' : ''; ?>>
                                                                <?php echo $category['item_category']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label>Article <small class="req">*</small></label>
                                                    <select name="item_id[]" class="form-control item-select" required>
                                                        <?php foreach ($itemList as $list) { ?>
                                                            <option value="<?php echo $list['id']; ?>" <?php echo ((int)$list['id'] == (int)$item['item_id']) ? 'selected' : ''; ?>>
                                                                <?php echo $list['name']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Unité</label>
                                                    <input type="text" name="unit[]" class="form-control unit" value="<?php echo $item['unit']; ?>" readonly>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Quantité <small class="req">*</small></label>
                                                    <input type="number" name="quantity[]" class="form-control quantity" min="1" value="<?php echo $item['quantity']; ?>" required>
                                                    <div class="availability">
                                                        Stock disponible: <span class="available-qty">0</span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Prix unitaire</label>
                                                    <input type="number" name="price[]" class="form-control price" min="0" step="0.01" value="<?php echo $item['unit_price']; ?>">
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Total</label>
                                                    <div class="total-price"><?php echo number_format($item['line_total'], 2); ?></div>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>&nbsp;</label>
                                                    <button type="button" class="btn btn-danger btn-sm remove-item">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <button type="button" id="add-item" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Ajouter un article
                                </button>
                            </div>
                          </div>

                          <div class="row">
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label for="payment_method">Méthode de paiement</label>
                                      <select class="form-control select2" id="payment_method" name="payment_method">
                                          <option value="">Sélectionner...</option>
                                          <option value="cash" <?php echo ($invoice['payment_method'] == 'cash') ? 'selected' : ''; ?>>Espèces</option>
                                          <option value="check" <?php echo ($invoice['payment_method'] == 'check') ? 'selected' : ''; ?>>Chèque</option>
                                          <option value="bank_transfer" <?php echo ($invoice['payment_method'] == 'bank_transfer') ? 'selected' : ''; ?>>Virement</option>
                                          <option value="card" <?php echo ($invoice['payment_method'] == 'card') ? 'selected' : ''; ?>>Carte bancaire</option>
                                      </select>
                                  </div>
                                  <div class="form-group">
                                      <label for="notes">Notes</label>
                                      <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo $invoice['notes']; ?></textarea>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <div class="checkbox">
                                          <label>
                                              <input type="checkbox" id="apply_tva" name="apply_tva" value="1" <?php echo $invoice['apply_tva'] ? 'checked' : ''; ?>> Appliquer la TVA
                                          </label>
                                      </div>
                                  </div>
                                  <div class="form-group">
                                      <label for="tva_rate">Taux de TVA (%)</label>
                                      <input type="number" class="form-control" id="tva_rate" name="tva_rate" value="<?php echo $invoice['tva_rate']; ?>" min="0" max="100" step="0.1">
                                  </div>
                                  <div class="row">
                                      <div class="col-md-6">
                                          <div class="form-group">
                                              <label>Total HT</label>
                                              <input type="text" class="form-control" id="total_ht" value="<?php echo number_format($invoice['total_ht'], 2, ',', ' '); ?> €" readonly>
                                          </div>
                                      </div>
                                      <div class="col-md-6">
                                          <div class="form-group">
                                              <label>TVA</label>
                                              <input type="text" class="form-control" id="tva_amount" value="<?php echo number_format($invoice['tva_amount'], 2, ',', ' '); ?> €" readonly>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="form-group">
                                      <label>Total TTC</label>
                                      <input type="text" class="form-control" id="total_ttc" value="<?php echo number_format($invoice['total_ttc'], 2, ',', ' '); ?> €" readonly>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="box-footer">
                          <a href="<?php echo base_url('admin/invoiceitem'); ?>" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> Retour à la liste
                          </a>
                          <button type="submit" id="<?= $submitBtn ?>" class="btn btn-primary">Enregistrer</button>
                          <a href="<?php echo base_url(); ?>admin/invoice/view/<?php echo $invoice['id']; ?>" class="btn btn-default">Annuler</a>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </section>
</div>


<script src="<?= base_url('assets/js/invoice/actions.js') ?>"></script>