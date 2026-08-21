<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$language = $this->customlib->getLanguage();
$language_name = $language["short_code"];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Ajout des bibliothèques nécessaires pour PDF et Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body>
<style type="text/css">

    @media print {
        .no-print {
            display: none !important;
        }

        /* Masquer la colonne actions dans l'impression */
        th.no-print, td.no-print {
            display: none !important;
        }

        /* Cacher les modals */
        .modal {
            display: none !important;
        }
    }
    /* Ajouter ces styles */
    .btn-group-xs > .btn, .btn-xs {
        padding: 1px 5px;
        font-size: 12px;
        line-height: 1.5;
        border-radius: 3px;
    }

    .btn-group .btn {
        margin-right: 2px;
    }

    .btn-group .btn:last-child {
        margin-right: 0;
    }

    /* Style pour les actions dans le tableau */
    .action-buttons {
        white-space: nowrap;
    }
</style>
<style type="text/css">
    @media print {
        .no-print {
            visibility: hidden !important;
            display: none !important;
        }

        /* Cacher tout sauf le tableau */
        body * {
            visibility: hidden !important;
        }

        #livre-caisse-table, #livre-caisse-table * {
            visibility: visible !important;
        }

        #livre-caisse-table {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            font-size: 12px !important;
        }

        /* Améliorer l'apparence pour l'impression */
        .table-livre-caisse {
            border-collapse: collapse !important;
            width: 100% !important;
        }

        .table-livre-caisse th,
        .table-livre-caisse td {
            border: 1px solid #000 !important;
            padding: 4px !important;
        }

        .table-livre-caisse th {
            background-color: #f0f0f0 !important;
            font-weight: bold !important;
        }

        /* Masquer les boutons et autres éléments */
        .box-tools, .filter-form, .modal, .caisse-card, .total-centralisation {
            display: none !important;
        }

        /* Style spécial pour les totaux dans l'impression */
        .print-totals-row {
            background-color: #e9ecef !important;
            font-weight: bold !important;
            border-top: 3px double #000 !important;
        }

        .print-solde-final {
            background-color: #d4edda !important;
            font-weight: bold !important;
            border-top: 3px double #000 !important;
            border-bottom: 3px double #000 !important;
        }
    }

    /* Styles pour le livre de caisse */
    .table-livre-caisse {
        font-size: 13px;
    }
    .table-livre-caisse th {
        background-color: #f5f5f5;
        font-weight: bold;
        text-align: center;
    }
    .text-entree {
        color: #28a745;
        font-weight: bold;
        text-align: right;
    }
    .text-sortie {
        color: #dc3545;
        font-weight: bold;
        text-align: right;
    }
    .solde-total {
        background-color: #e9ecef;
        font-weight: bold;
        font-size: 14px;
    }
    .table-totals {
        background-color: #f8f9fa;
        border-top: 2px solid #dee2e6;
    }
    .caisse-card {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f9f9f9;
    }
    .caisse-active {
        border-left: 5px solid #28a745;
    }
    .caisse-inactive {
        border-left: 5px solid #dc3545;
    }
    .montant-caisse {
        font-size: 18px;
        font-weight: bold;
        color: #2c3e50;
    }
    .total-centralisation {
        background-color: #2c3e50;
        color: white;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 18px;
        text-align: center;
    }
    .badge-solde {
        font-size: 12px;
        padding: 5px 10px;
    }
    .badge-solde-positif {
        background-color: #28a745;
        color: white;
    }
    .badge-solde-negatif {
        background-color: #dc3545;
        color: white;
    }
    .filter-form {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    /* Style pour le PDF preview */
    .pdf-preview {
        background-color: white;
        padding: 20px;
        border: 1px solid #ddd;
        margin-bottom: 20px;
    }

    .pdf-header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
    }

    .pdf-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .pdf-period {
        font-size: 14px;
        margin-bottom: 10px;
    }

    .pdf-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }

    .pdf-table th {
        background-color: #f2f2f2;
        border: 1px solid #000;
        padding: 5px;
        text-align: center;
        font-weight: bold;
    }

    .pdf-table td {
        border: 1px solid #000;
        padding: 5px;
    }

    .pdf-totals {
        font-weight: bold;
        background-color: #e9ecef;
        border-top: 2px solid #000 !important;
    }

    .pdf-solde-final {
        font-weight: bold;
        background-color: #d4edda;
        border-top: 2px solid #000 !important;
        border-bottom: 2px solid #000 !important;
    }

    .pdf-footer {
        text-align: center;
        margin-top: 20px;
        font-size: 10px;
        color: #666;
    }

    /* Styles pour les boutons d'export */
    .export-buttons {
        margin-top: 10px;
    }

    .export-buttons .btn {
        margin-right: 5px;
        margin-bottom: 5px;
    }

    /* Style spécial pour les totaux dans la vue */
    .total-row {
        background-color: #f8f9fa !important;
        font-weight: bold;
        border-top: 2px solid #dee2e6;
    }

    .total-entrees {
        color: #28a745;
        font-weight: bold;
    }

    .total-sorties {
        color: #dc3545;
        font-weight: bold;
    }

    .solde-final-row {
        background-color: #e9ecef !important;
        font-weight: bold;
        border-top: 2px solid #dee2e6;
        border-bottom: 2px solid #dee2e6;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content-header">
        <h1>
            <i class="fa fa-usd"></i> <?php echo $this->lang->line('income'); ?>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Section des caisses -->
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-briefcase"></i> Caisses</h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('caisse', 'can_add')) { ?>
                                <button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#addCaisseModal">
                                    <i class="fa fa-plus"></i> Nouvelle caisse
                                </button>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <!-- Total de centralisation -->
                        <?php if (isset($total_centralisation)): ?>
                            <div class="total-centralisation">
                                <i class="fa fa-money"></i> TOTAL CENTRALISATION:
                                <strong><?php echo number_format($total_centralisation, 2, ',', ' '); ?> FCFA</strong>
                            </div>
                        <?php endif; ?>

                        <!-- Liste des caisses -->
                        <?php if (!empty($caisses)): ?>
                            <?php foreach ($caisses as $caisse): ?>
                                <div class="caisse-card <?php echo $caisse['est_actif'] == '1' ? 'caisse-active' : 'caisse-inactive'; ?>">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h4 style="margin-top: 0; margin-bottom: 5px;">
                                                <strong><?php echo htmlspecialchars($caisse['name']); ?></strong>
                                                <?php if ($caisse['est_actif'] == '1'): ?>
                                                    <span class="label label-success" style="font-size: 10px;">ACTIVE</span>
                                                <?php else: ?>
                                                    <span class="label label-danger" style="font-size: 10px;">INACTIVE</span>
                                                <?php endif; ?>
                                            </h4>
                                            <p style="margin-bottom: 5px; font-size: 12px; color: #666;">
                                                Créée le: <?php echo date('d/m/Y', strtotime($caisse['date'])); ?>
                                            </p>
                                            <?php if (!empty($caisse['note'])): ?>
                                                <p style="margin-bottom: 5px; font-size: 12px; color: #666;">
                                                    <?php echo htmlspecialchars($caisse['note']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <div class="montant-caisse">
                                                <?php
                                                $solde_caisse = isset($soldes_caisses[$caisse['id']]) ? $soldes_caisses[$caisse['id']] : 0;
                                                echo number_format($solde_caisse, 0, ',', ' ');
                                                ?>
                                            </div>
                                            <span class="badge badge-solde <?php echo $solde_caisse >= 0 ? 'badge-solde-positif' : 'badge-solde-negatif'; ?>">
                                            <?php echo $solde_caisse >= 0 ? '+' : ''; ?>
                                                <?php echo number_format($solde_caisse, 0, ',', ' '); ?> FCFA
                                        </span>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="col-md-12">
                                            <div class="btn-group btn-group-xs">
                                                <a href="<?php echo base_url('admin/income?caisse_id=' . $caisse['id']); ?>"
                                                   class="btn btn-info">
                                                    <i class="fa fa-eye"></i> Voir
                                                </a>

                                                <a href="<?php echo base_url('admin/income?caisse_id=' . $caisse['id']); ?>"
                                                   class="btn btn-info">
                                                    <i class="fa fa-eye"></i> Voir
                                                </a>

                                                <?php if ($this->rbac->hasPrivilege('caisse', 'can_edit')): ?>
                                                    <a href="<?php echo base_url('admin/income/edit/' . $caisse['id']); ?>"
                                                       class="btn btn-warning">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($this->rbac->hasPrivilege('caisse', 'can_delete')): ?>
                                                    <a href="<?php echo base_url('admin/income/delete/' . $caisse['id']); ?>"
                                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette caisse ?')"
                                                       class="btn btn-danger">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <button class="btn btn-primary toggle-status"
                                                        data-id="<?php echo $caisse['id']; ?>"
                                                        data-status="<?php echo $caisse['est_actif']; ?>">
                                                    <?php if ($caisse['est_actif'] == '1'): ?>
                                                        <i class="fa fa-pause"></i> Désactiver
                                                    <?php else: ?>
                                                        <i class="fa fa-play"></i> Activer
                                                    <?php endif; ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Aucune caisse créée. Créez votre première caisse.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Section du livre de caisse -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title">
                            <?php if (isset($caisse_selectionnee)): ?>
                                Livre de Caisse: <strong><?php echo htmlspecialchars($caisse_selectionnee['name']); ?></strong>
                            <?php else: ?>
                                Livre de Caisse - Toutes les caisses
                            <?php endif; ?>
                        </h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('caisse', 'can_add')) { ?>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#addOperationModal">
                                    <i class="fa fa-plus"></i> Nouvelle opération
                                </button>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Formulaire de filtre -->
                    <div class="box-body">
                        <div class="filter-form">
                            <form method="get" action="<?php echo base_url('admin/income') ?>" class="form-inline">
                                <div class="form-group">
                                    <label for="caisse_id">Caisse: </label>
                                    <select name="caisse_id" id="caisse_id" class="form-control input-sm" style="width: 200px;">
                                        <option value="">Toutes les caisses</option>
                                        <?php if (!empty($caisses)): ?>
                                            <?php foreach ($caisses as $caisse): ?>
                                                <option value="<?php echo $caisse['id']; ?>"
                                                    <?php echo isset($_GET['caisse_id']) && $_GET['caisse_id'] == $caisse['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($caisse['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="form-group" style="margin-left: 15px;">
                                    <label for="date_debut">Du: </label>
                                    <input type="date" name="date_debut" id="date_debut" class="form-control input-sm"
                                           value="<?php echo isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-01'); ?>">
                                </div>

                                <div class="form-group" style="margin-left: 15px;">
                                    <label for="date_fin">Au: </label>
                                    <input type="date" name="date_fin" id="date_fin" class="form-control input-sm"
                                           value="<?php echo isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-d'); ?>">
                                </div>

                                <div class="form-group" style="margin-left: 15px;">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-filter"></i> Filtrer
                                    </button>
                                    <a href="<?php echo base_url('admin/income'); ?>" class="btn btn-default btn-sm">
                                        <i class="fa fa-refresh"></i> Réinitialiser
                                    </a>
                                    <div class="export-buttons" style="display: inline-block;">
                                        <button type="button" class="btn btn-success btn-sm" onclick="imprimerLivreCaisse()">
                                            <i class="fa fa-print"></i> Imprimer
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="genererPDF()">
                                            <i class="fa fa-file-pdf-o"></i> PDF
                                        </button>
                                        <button type="button" class="btn btn-info btn-sm" onclick="exporterExcel()">
                                            <i class="fa fa-file-excel-o"></i> Excel
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm" onclick="previewPDF()">
                                            <i class="fa fa-eye"></i> Aperçu
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="box-body">
                        <?php if ($this->session->flashdata('msg')) { ?>
                            <?php echo $this->session->flashdata('msg') ?>
                        <?php } ?>
                        <?php if (isset($error_message)) { echo "<div class='alert alert-danger'>" . $error_message . "</div>"; } ?>

                        <!-- Dans la section du livre de caisse, modifier le tableau en ajoutant la colonne ACTIONS -->
                        <table class="table table-striped table-bordered table-hover table-livre-caisse"
                               id="livre-caisse-table">
                            <thead>
                            <tr>
                                <th width="10%">RÉFÉRENCE</th>
                                <th width="12%">DATE</th>
                                <th width="30%">DÉSIGNATIONS</th>
                                <th width="12%">ENTRÉE</th>
                                <th width="12%">SORTIE</th>
                                <th width="16%">SOLDE</th>
                                <?php if ($this->rbac->hasPrivilege('caisse', 'can_edit') || $this->rbac->hasPrivilege('caisse', 'can_delete')): ?>
                                    <th width="8%" class="no-print">ACTIONS</th>
                                <?php endif; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            // Variables pour les totaux
                            $total_entrees = 0;
                            $total_sorties = 0;
                            $solde_courant = 0;

                            // Afficher le solde initial si disponible
                            if (isset($solde_initial) && $solde_initial != 0) {
                                $solde_courant = $solde_initial;
                                ?>
                                <tr style="background-color: #f8f9fa;">
                                    <td colspan="<?php echo ($this->rbac->hasPrivilege('caisse', 'can_edit') || $this->rbac->hasPrivilege('caisse', 'can_delete')) ? '7' : '6'; ?>" class="text-right">
                                        <strong>SOLDE INITIAL:</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong><?php echo number_format($solde_initial, 2, ',', ' '); ?></strong>
                                    </td>
                                </tr>
                                <?php
                            }

                            // Afficher les opérations
                            if (!empty($operations)) {
                                foreach ($operations as $operation) {
                                    $entree = floatval($operation['entree']);
                                    $sortie = floatval($operation['sortie']);
                                    $solde_courant += $entree - $sortie;
                                    ?>
                                    <tr id="operation-<?php echo $operation['id']; ?>">
                                        <td><?php echo htmlspecialchars($operation['reference']); ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($operation['date'])); ?></td>
                                        <td><?php echo htmlspecialchars($operation['designation']); ?></td>
                                        <td class="text-entree">
                                            <?php if ($entree > 0): ?>
                                                <?php echo number_format($entree, 2, ',', ' '); ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-sortie">
                                            <?php if ($sortie > 0): ?>
                                                <?php echo number_format($sortie, 2, ',', ' '); ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                    <span class="<?php echo $solde_courant >= 0 ? 'text-success' : 'text-danger'; ?>">
                        <?php echo number_format($solde_courant, 2, ',', ' '); ?>
                    </span>
                                        </td>
                                        <?php if ($this->rbac->hasPrivilege('caisse', 'can_edit') || $this->rbac->hasPrivilege('caisse', 'can_delete')): ?>
                                            <td class="text-center no-print">
                                                <div class="btn-group">
                                                    <?php if ($this->rbac->hasPrivilege('caisse', 'can_edit')): ?>
                                                        <button type="button" class="btn btn-xs btn-warning edit-operation"
                                                                data-id="<?php echo $operation['id']; ?>"
                                                                data-toggle="modal" data-target="#editOperationModal"
                                                                title="Modifier">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if ($this->rbac->hasPrivilege('caisse', 'can_delete')): ?>
                                                        <button type="button" class="btn btn-xs btn-danger delete-operation"
                                                                data-id="<?php echo $operation['id']; ?>"
                                                                data-reference="<?php echo htmlspecialchars($operation['reference']); ?>"
                                                                data-designation="<?php echo htmlspecialchars($operation['designation']); ?>"
                                                                title="Supprimer">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php
                                    $total_entrees += $entree;
                                    $total_sorties += $sortie;
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="<?php echo ($this->rbac->hasPrivilege('caisse', 'can_edit') || $this->rbac->hasPrivilege('caisse', 'can_delete')) ? '7' : '6'; ?>" class="text-center">
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> Aucune opération trouvée pour cette période.
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                            <tfoot>
                            <tr class="total-row">
                                <td colspan="<?php echo ($this->rbac->hasPrivilege('caisse', 'can_edit') || $this->rbac->hasPrivilege('caisse', 'can_delete')) ? '4' : '3'; ?>" class="text-right">
                                    <strong>TOTAUX:</strong>
                                </td>
                                <td class="text-entree">
                                    <strong class="total-entrees"><?php echo number_format($total_entrees, 2, ',', ' '); ?> FCFA</strong>
                                </td>
                                <td class="text-sortie">
                                    <strong class="total-sorties"><?php echo number_format($total_sorties, 2, ',', ' '); ?> FCFA</strong>
                                </td>
                                <td></td>
                                <?php if ($this->rbac->hasPrivilege('caisse', 'can_edit') || $this->rbac->hasPrivilege('caisse', 'can_delete')): ?>
                                    <td></td>
                                <?php endif; ?>
                            </tr>
                            <tr class="solde-final-row">
                                <td colspan="<?php echo ($this->rbac->hasPrivilege('caisse', 'can_edit') || $this->rbac->hasPrivilege('caisse', 'can_delete')) ? '4' : '3'; ?>" class="text-right">
                                    <strong>SOLDE FINAL:</strong>
                                </td>
                                <td colspan="2" class="text-center">
                                    <small>Entrées: <?php echo number_format($total_entrees, 2, ',', ' '); ?> FCFA |
                                        Sorties: <?php echo number_format($total_sorties, 2, ',', ' '); ?> FCFA</small>
                                </td>
                                <td class="text-right">
                                    <strong>
                <span class="<?php echo $solde_courant >= 0 ? 'text-success' : 'text-danger'; ?>">
                    <?php echo number_format($solde_courant, 2, ',', ' '); ?> FCFA
                </span>
                                    </strong>
                                </td>
                                <?php if ($this->rbac->hasPrivilege('caisse', 'can_edit') || $this->rbac->hasPrivilege('caisse', 'can_delete')): ?>
                                    <td></td>
                                <?php endif; ?>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<!-- Modal pour éditer une opération -->
<div class="modal fade" id="editOperationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier l'opération</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditOperation" action="<?php echo base_url('admin/income/update_operation') ?>" method="post">
                <div class="modal-body">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <input type="hidden" id="edit_operation_id" name="operation_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_caisse_id">Caisse *</label>
                                <select class="form-control" id="edit_caisse_id" name="caisse_id" required>
                                    <option value="">Sélectionner une caisse</option>
                                    <?php if (!empty($caisses)): ?>
                                        <?php foreach ($caisses as $caisse): ?>
                                            <?php if ($caisse['est_actif'] == '1'): ?>
                                                <option value="<?php echo $caisse['id']; ?>">
                                                    <?php echo htmlspecialchars($caisse['name']); ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_date">Date *</label>
                                <input type="date" class="form-control" id="edit_date" name="date" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_type">Type d'opération *</label>
                        <select class="form-control" id="edit_type" name="type" required>
                            <option value="entree">Entrée (Recette)</option>
                            <option value="sortie">Sortie (Dépense)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_designation">Désignation *</label>
                        <textarea class="form-control" id="edit_designation" name="designation" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="edit_exp_head_id"><?php echo $this->lang->line('expense_head'); ?></label> <small class="req">*</small>
                        <select id="edit_exp_head_id" name="exp_head_id" class="form-control" required>
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($expheadlist as $exphead): ?>
                                <option value="<?php echo $exphead['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($exphead['exp_category'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($exphead['exp_category'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="exp_category_name" id="edit_exp_category_name">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_montant">Montant *</label>
                                <input type="number" class="form-control" id="edit_montant" name="montant"
                                       step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_reference">Référence</label>
                                <input type="text" class="form-control" id="edit_reference" name="reference"
                                       placeholder="Ex: RECU-001, FACT-001">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_mode_paiement">Mode de paiement</label>
                        <select class="form-control" id="edit_mode_paiement" name="mode_paiement">
                            <option value="espèces">Espèces</option>
                            <option value="chèque">Chèque</option>
                            <option value="virement">Virement</option>
                            <option value="carte">Carte bancaire</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmation pour suppression -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="fa fa-exclamation-triangle"></i> Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette opération ?</p>
                <p class="font-weight-bold" id="delete-operation-info"></p>
                <p class="text-danger"><small>Cette action est irréversible.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Supprimer</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal pour nouvelle caisse -->
<div class="modal fade" id="addCaisseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une caisse</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCaisse" action="<?php echo base_url() ?>admin/income/create" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <?php if ($this->session->flashdata('msg')) { ?>
                        <?php echo $this->session->flashdata('msg') ?>
                    <?php } ?>
                    <?php if (isset($error_message)) { echo "<div class='alert alert-danger'>" . $error_message . "</div>"; } ?>
                    <?php echo $this->customlib->getCSRF(); ?>

                    <!-- Tous les champs originaux gardés -->
                    <div class="form-group" hidden>
                        <label for="inc_head_id"><?php echo $this->lang->line('income_head'); ?></label><small class="req"> *</small>
                        <select id="inc_head_id" name="inc_head_id" class="form-control">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($incheadlist as $inchead) { ?>
                                <option value="<?php echo $inchead['id'] ?>"<?php if (set_value('inc_head_id') == $inchead['id']) { echo "selected"; } ?>>
                                    <?php echo $inchead['income_category'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group" hidden>
                        <label>User</label>
                        <input id="user" name="user" type="text" class="form-control"
                               value="<?php echo $this->customlib->getAdminSessionUserName(); ?>" />
                    </div>

                    <div class="form-group">
                        <label for="name"><?php echo $this->lang->line('name'); ?><small class="req"> *</small></label>
                        <input id="name" name="name" type="text" class="form-control"
                               value="<?php echo set_value('name'); ?>" />
                    </div>

                    <div class="form-group" hidden>
                        <label><?php echo $this->lang->line('invoice_no'); ?></label>
                        <input id="invoice_no" name="invoice_no" type="text" class="form-control"
                               value="<?php echo set_value('invoice_no'); ?>" />
                    </div>

                    <div class="form-group">
                        <label for="date"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                        <input id="date" name="date" type="text" class="form-control date"
                               value="<?php echo set_value('date'); ?>" readonly="readonly" />
                    </div>

                    <div class="form-group">
                        <label for="amount"><?php echo $this->lang->line('amount'); ?><small class="req"> *</small></label>
                        <input id="amount" name="amount" type="number" class="form-control"
                               value="<?php echo set_value('amount'); ?>" />
                    </div>

                    <div class="form-group">
                        <label for="description">Description (Optionnel)</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="est_actif">Statut</label>
                        <select id="est_actif" name="est_actif" class="form-control">
                            <option value="1" <?php echo set_value('est_actif') == '1' ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo set_value('est_actif') == '0' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="form-group" hidden>
                        <label><?php echo $this->lang->line('attach_document'); ?></label>
                        <input id="documents" name="documents" type="file" class="filestyle form-control"
                               data-height="40" value="<?php echo set_value('documents'); ?>" />
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour nouvelle opération -->
<div class="modal fade" id="addOperationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle Opération</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formOperation" action="<?php echo base_url('admin/income/create_operation') ?>" method="post">
                <div class="modal-body">
                    <?php echo $this->customlib->getCSRF(); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="caisse_id">Caisse *</label>
                                <select class="form-control" id="caisse_id" name="caisse_id" required>
                                    <option value="">Sélectionner une caisse</option>
                                    <?php if (!empty($caisses)): ?>
                                        <?php foreach ($caisses as $caisse): ?>
                                            <?php if ($caisse['est_actif'] == '1'): ?>
                                                <option value="<?php echo $caisse['id']; ?>">
                                                    <?php echo htmlspecialchars($caisse['name']); ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Date *</label>
                                <input type="date" class="form-control" id="date" name="date"
                                       value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="type">Type d'opération *</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="entree">Entrée (Recette)</option>
                            <option value="sortie">Sortie (Dépense)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="designation">Désignation *</label>
                        <textarea class="form-control" id="designation" name="designation" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo $this->lang->line('expense_head'); ?></label> <small class="req">*</small>


                        <select id="exp_head_id" name="exp_head_id" class="form-control" required>
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($expheadlist as $exphead): ?>
                                <option
                                        value="<?php echo $exphead['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($exphead['exp_category'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($exphead['exp_category'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="exp_category_name" id="exp_category_name">
                        <span class="text-danger"><?php echo form_error('exp_head_id'); ?></span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="montant">Montant *</label>
                                <input type="number" class="form-control" id="montant" name="montant"
                                       step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reference">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference"
                                       placeholder="Ex: RECU-001, FACT-001">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="mode_paiement">Mode de paiement</label>
                        <select class="form-control" id="mode_paiement" name="mode_paiement">
                            <option value="espèces">Espèces</option>
                            <option value="chèque">Chèque</option>
                            <option value="virement">Virement</option>
                            <option value="carte">Carte bancaire</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer l'opération</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- increase form modal -->
<?php
if ($this->rbac->hasPrivilege('caisse', 'can_add')) {
    ?>
    <div id="increaseForm" class="modal fade" data-backdrop="false">
        <div class="modal-dialog">
            <div class="modal-content" id="increaseFormContent">

            </div>
        </div>
    </div>

    <div id="viewIncreaseList" class="modal fade" data-backdrop="false">
        <div class="modal-dialog">
            <div class="modal-content" id="ViewIncreaseContent">

            </div>
        </div>
    </div>

<?php } ?>


<!-- increase form modal -->
<!-- Modal pour prévisualisation PDF -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aperçu PDF - Livre de Caisse</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                <!-- Le contenu PDF sera inséré ici -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="genererPDF()">
                    <i class="fa fa-download"></i> Télécharger PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    ( function ( $ ) {
        'use strict';
        $(document).ready(function () {
            initDatatable('income-list','admin/income/getincomelist',[],[],10);
        });
    } ( jQuery ) )

    var base_url = '<?php echo base_url() ?>';

    /*
    ALL ACTIONS BUTTONS ABOUT PERMISSIONS DATATABLE
    */
    // Function to set a increase
    function form_increase(id) {
        $.ajax({
            'url'   : base_url + 'Income/form_increase', // controller link
            'type'  : 'GET', // method used to send data
            'data'  : {
                'id'        : id, // row id
            },
            'success': function (data) {
                var increase_form_content = $('#increase_form_content');
                if (data) {
                    increase_form_content.html(data);
                }
            }
        });
    }

    // Function to load on (edit or add) button click
    $(document).on('click', `.increaseAmount`, function(e) {
        e.preventDefault();
        var rowID = $(this).attr('data-row-id');

        $.ajax({
            url: base_url + '/admin/income/formIncrease',
            type: "POST",
            data: {
                'rowID': rowID,
            },
            success: function(data) {
                if(data) {
                    $(`#increaseForm #increaseFormContent`).html(data);
                }
            }
        });
    });

    /**
     * PROCESS CLICK FORM
     * On click on the 'submit' button
     */
    $(document).on("click", `#submitBTN`, function (e) {
        e.preventDefault();
        initPostAjaxRequest();
    });

    // Function to post the form data to the server
    let initPostAjaxRequest = () => {
        var formElement = $('#increaseFormID'),
            formData = new FormData(formElement[0]);

        $.ajax({
            type: "POST",
            url: base_url + 'admin/income/setIncrease',
            processData: false,
            contentType: false,
            data: formData,
            success: function(data) {
                let serverResponse = JSON.parse(data);

                if(serverResponse.type === 'success') {
                    $(`#increaseForm`).modal("hide");
                    toastr.success(serverResponse.message);
                    let incomeTable = $('.income-list').DataTable();
                    incomeTable.ajax.reload(null, false);
                    location.reload(true);
                } else if(serverResponse.type === 'warning') {
                    toastr.warning(serverResponse.message);
                } else {
                    toastr.error(serverResponse.message);
                }
            }
        });
    }

    // Function to load on (edit or add) button click
    $(document).on('click', `.viewIncrease`, function(e) {
        e.preventDefault();
        var rowID = $(this).attr('data-row-id');

        $.ajax({
            url: base_url + '/admin/income/listIncrease',
            type: "POST",
            data: {
                'rowID': rowID,
            },
            success: function(data) {
                if(data) {
                    $(`#viewIncreaseList #ViewIncreaseContent`).html(data);
                }
            }
        });
    });

    $(document).on('click', '.toggle-status', function () {
        const caisseId = $(this).data('id');
        const currentStatus = $(this).data('status');

        if (confirm("Changer le statut de la caisse ?")) {
            $.ajax({
                url: "<?php echo site_url('admin/income/toggle_status'); ?>",
                type: "POST",
                dataType: "json",
                data: {
                    id: caisseId,
                    status: currentStatus
                },
                success: function (res) {
                    alert(res.message);
                    location.reload();
                },
                error: function () {
                    alert("Erreur serveur.");
                }
            });
        }
    });

    // ==============================================
    // FONCTIONS D'EXPORT ET D'IMPRESSION AMÉLIORÉES
    // ==============================================

    // Fonction d'impression améliorée avec totaux embelli
    function imprimerLivreCaisse() {
        // Créer une nouvelle fenêtre pour l'impression
        var printWindow = window.open('', '_blank');
        var title = "LIVRE DE CAISSE";
        var periode = "";

        // Récupérer les dates du filtre
        var dateDebut = document.getElementById('date_debut').value;
        var dateFin = document.getElementById('date_fin').value;

        if (dateDebut && dateFin) {
            periode = "Période du " + formatDate(dateDebut) + " au " + formatDate(dateFin);
        }

        // Récupérer le nom de la caisse si sélectionnée
        var caisseSelect = document.getElementById('caisse_id');
        var caisseNom = "";
        if (caisseSelect && caisseSelect.value) {
            caisseNom = caisseSelect.options[caisseSelect.selectedIndex].text;
        }

        // Récupérer les totaux depuis le tableau
        var totalEntrees = document.querySelector('.total-entrees') ? document.querySelector('.total-entrees').innerText : "0 FCFA";
        var totalSorties = document.querySelector('.total-sorties') ? document.querySelector('.total-sorties').innerText : "0 FCFA";
        var soldeFinalElement = document.querySelector('.solde-final-row strong span');
        var soldeFinal = soldeFinalElement ? soldeFinalElement.innerText : "0 FCFA";
        var soldeClass = soldeFinalElement ? soldeFinalElement.className : "text-success";

        // HTML pour l'impression
        var printContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>${title}</title>
                <style>
                    @page {
                        size: A4 landscape;
                        margin: 10mm;
                    }
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 11px;
                        margin: 0;
                        padding: 0;
                    }
                    .print-header {
                        text-align: center;
                        margin-bottom: 20px;
                        border-bottom: 2px solid #000;
                        padding-bottom: 10px;
                    }
                    .print-title {
                        font-size: 18px;
                        font-weight: bold;
                        margin-bottom: 5px;
                    }
                    .print-subtitle {
                        font-size: 14px;
                        margin-bottom: 5px;
                    }
                    .print-period {
                        font-size: 12px;
                        margin-bottom: 10px;
                    }
                    .print-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 10px;
                    }
                    .print-table th {
                        background-color: #f2f2f2;
                        border: 1px solid #000;
                        padding: 6px;
                        text-align: center;
                        font-weight: bold;
                    }
                    .print-table td {
                        border: 1px solid #000;
                        padding: 5px;
                    }
                    .print-table .text-entree {
                        color: #28a745;
                        font-weight: bold;
                        text-align: right;
                    }
                    .print-table .text-sortie {
                        color: #dc3545;
                        font-weight: bold;
                        text-align: right;
                    }
                    .print-totals-row {
                        font-weight: bold;
                        background-color: #e9ecef !important;
                        border-top: 3px double #000 !important;
                    }
                    .print-totals-row td {
                        font-weight: bold;
                    }
                    .print-solde-final {
                        font-weight: bold;
                        background-color: #d4edda !important;
                        border-top: 3px double #000 !important;
                        border-bottom: 3px double #000 !important;
                    }
                    .print-summary {
                        background-color: #f0f8ff;
                        text-align: center;
                        padding: 8px;
                        margin-top: 10px;
                        border: 1px solid #000;
                        font-weight: bold;
                    }
                    .print-summary .entrees {
                        color: #28a745;
                    }
                    .print-summary .sorties {
                        color: #dc3545;
                    }
                    .print-footer {
                        text-align: center;
                        margin-top: 20px;
                        font-size: 10px;
                        color: #666;
                        border-top: 1px solid #000;
                        padding-top: 10px;
                    }
                    .no-print {
                        display: none;
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <div class="print-title">${title}</div>
                    ${caisseNom ? `<div class="print-subtitle">Caisse: ${caisseNom}</div>` : ''}
                    ${periode ? `<div class="print-period">${periode}</div>` : ''}
                    <div class="print-period">Date d'édition: ${new Date().toLocaleDateString('fr-FR')}</div>
                </div>
        `;

        // Copier le tableau sans le tfoot (on va le recréer)
        var table = document.getElementById('livre-caisse-table').cloneNode(true);

        // Supprimer le tfoot existant
        var tfoot = table.querySelector('tfoot');
        if (tfoot) {
            tfoot.parentNode.removeChild(tfoot);
        }

        // Nettoyer le tableau pour l'impression
        var buttons = table.querySelectorAll('button, a, .alert');
        buttons.forEach(function(btn) {
            btn.parentNode.removeChild(btn);
        });

        // Ajouter la classe pour l'impression
        table.className = 'print-table';

        // Ajouter le tableau au contenu
        printContent += table.outerHTML;

        // Ajouter les totaux embelli
        printContent += `
            <table class="print-table">
                <tr class="print-totals-row">
                    <td colspan="3" style="text-align: right; font-weight: bold;">TOTAUX:</td>
                    <td class="text-entree" style="font-weight: bold;">${totalEntrees}</td>
                    <td class="text-sortie" style="font-weight: bold;">${totalSorties}</td>
                    <td></td>
                </tr>
                <tr class="print-solde-final">
                    <td colspan="3" style="text-align: right; font-weight: bold;">SOLDE FINAL:</td>
                    <td colspan="2" style="text-align: center; font-size: 10px;">
                        Entrées: ${totalEntrees} | Sorties: ${totalSorties}
                    </td>
                    <td style="text-align: right; font-weight: bold;" class="${soldeClass.includes('success') ? 'text-success' : 'text-danger'}">
                        ${soldeFinal}
                    </td>
                </tr>
            </table>

            <div class="print-summary">
                <strong>RÉCAPITULATIF:</strong>
                Total Entrées: <span class="entrees">${totalEntrees}</span> |
                Total Sorties: <span class="sorties">${totalSorties}</span> |
                Solde Final: <span class="${soldeClass.includes('success') ? 'entrees' : 'sorties'}">${soldeFinal}</span>
            </div>
        `;

        // Ajouter le footer
        printContent += `
                <div class="print-footer">
                    Document généré le ${new Date().toLocaleString('fr-FR')} | ${window.location.href}
                </div>
            </body>
            </html>
        `;

        printWindow.document.write(printContent);
        printWindow.document.close();

        // Attendre que le contenu soit chargé avant d'imprimer
        printWindow.onload = function() {
            printWindow.focus();
            printWindow.print();
        };
    }

    // Fonction pour générer PDF avec totaux embelli
    function genererPDF() {
        // Vérifier si jsPDF est disponible
        if (typeof jsPDF === 'undefined') {
            alert("La fonction PDF nécessite la bibliothèque jsPDF. Veuillez patienter pendant le chargement...");
            location.reload();
            return;
        }

        var doc = new jsPDF('landscape');
        var title = "LIVRE DE CAISSE";
        var periode = "";

        // Récupérer les dates du filtre
        var dateDebut = document.getElementById('date_debut').value;
        var dateFin = document.getElementById('date_fin').value;

        if (dateDebut && dateFin) {
            periode = "Période du " + formatDate(dateDebut) + " au " + formatDate(dateFin);
        }

        // Récupérer le nom de la caisse si sélectionnée
        var caisseSelect = document.getElementById('caisse_id');
        var caisseNom = "";
        if (caisseSelect && caisseSelect.value) {
            caisseNom = caisseSelect.options[caisseSelect.selectedIndex].text;
        }

        // Récupérer les totaux
        var totalEntrees = document.querySelector('.total-entrees') ? document.querySelector('.total-entrees').innerText : "0 FCFA";
        var totalSorties = document.querySelector('.total-sorties') ? document.querySelector('.total-sorties').innerText : "0 FCFA";
        var soldeFinalElement = document.querySelector('.solde-final-row strong span');
        var soldeFinal = soldeFinalElement ? soldeFinalElement.innerText : "0 FCFA";

        // En-tête du PDF
        doc.setFontSize(16);
        doc.text(title, 140, 15, null, null, 'center');

        doc.setFontSize(12);
        if (caisseNom) {
            doc.text("Caisse: " + caisseNom, 140, 22, null, null, 'center');
        }

        if (periode) {
            doc.text(periode, 140, 29, null, null, 'center');
        }

        doc.text("Date d'édition: " + new Date().toLocaleDateString('fr-FR'), 140, 36, null, null, 'center');

        // Préparer les données du tableau (sans les totaux)
        var table = document.getElementById('livre-caisse-table');
        var data = [];

        // Récupérer les en-têtes
        var headers = [];
        table.querySelectorAll('thead th').forEach(function(th) {
            headers.push(th.innerText);
        });

        // Récupérer les données du tbody seulement
        table.querySelectorAll('tbody tr').forEach(function(row) {
            var rowData = [];
            row.querySelectorAll('td').forEach(function(td) {
                rowData.push(td.innerText);
            });
            if (rowData.length > 0) {
                data.push(rowData);
            }
        });

        // Générer le tableau principal
        doc.autoTable({
            head: [headers],
            body: data,
            startY: 45,
            styles: {
                fontSize: 8,
                cellPadding: 2
            },
            headStyles: {
                fillColor: [242, 242, 242],
                textColor: 0,
                fontStyle: 'bold'
            },
            alternateRowStyles: {
                fillColor: [245, 245, 245]
            },
            columnStyles: {
                3: { halign: 'right', textColor: [40, 167, 69] },
                4: { halign: 'right', textColor: [220, 53, 69] },
                5: { halign: 'right' }
            },
            didDrawCell: function(data) {
                // Ajouter le style bold pour les totaux si on les ajoute plus tard
            },
            margin: { top: 45 }
        });

        // Récupérer la position Y après le tableau
        var finalY = doc.lastAutoTable.finalY + 10;

        // Ajouter les totaux embelli
        doc.setFontSize(10);
        doc.setFont(undefined, 'bold');

        // Ligne des totaux
        doc.autoTable({
            body: [
                ['TOTAUX:', '', '', totalEntrees, totalSorties, '']
            ],
            startY: finalY,
            styles: {
                fontSize: 9,
                cellPadding: 3,
                fillColor: [233, 236, 239]
            },
            columnStyles: {
                0: { fontStyle: 'bold', halign: 'right' },
                3: { halign: 'right', fontStyle: 'bold', textColor: [40, 167, 69] },
                4: { halign: 'right', fontStyle: 'bold', textColor: [220, 53, 69] }
            },
            margin: { left: 14 }
        });

        finalY = doc.lastAutoTable.finalY;

        // Ligne du solde final
        doc.autoTable({
            body: [
                ['SOLDE FINAL:', '', '', '', '', soldeFinal]
            ],
            startY: finalY,
            styles: {
                fontSize: 9,
                cellPadding: 3,
                fillColor: [212, 237, 218]
            },
            columnStyles: {
                0: { fontStyle: 'bold', halign: 'right' },
                5: { halign: 'right', fontStyle: 'bold', textColor: soldeFinal.includes('-') ? [220, 53, 69] : [40, 167, 69] }
            },
            margin: { left: 14 }
        });

        finalY = doc.lastAutoTable.finalY + 10;

        // Ajouter un récapitulatif
        doc.setFontSize(9);
        doc.setFont(undefined, 'bold');
        doc.text("RÉCAPITULATIF:", 14, finalY);

        doc.setFontSize(8);
        doc.setFont(undefined, 'normal');
        doc.text(`Total Entrées: ${totalEntrees}`, 14, finalY + 5);
        doc.setTextColor(40, 167, 69);
        doc.text(totalEntrees, 60, finalY + 5);

        doc.setTextColor(0, 0, 0);
        doc.text(`Total Sorties: ${totalSorties}`, 100, finalY + 5);
        doc.setTextColor(220, 53, 69);
        doc.text(totalSorties, 146, finalY + 5);

        doc.setTextColor(0, 0, 0);
        doc.text(`Solde Final: ${soldeFinal}`, 180, finalY + 5);
        doc.setTextColor(soldeFinal.includes('-') ? 220 : 40, soldeFinal.includes('-') ? 53 : 167, 69);
        doc.text(soldeFinal, 220, finalY + 5);

        // Footer
        doc.setFontSize(8);
        doc.setTextColor(100, 100, 100);
        doc.text("Document généré le " + new Date().toLocaleString('fr-FR'), 140, doc.internal.pageSize.height - 10, null, null, 'center');

        // Télécharger le PDF
        var filename = 'livre_caisse_' + new Date().toISOString().slice(0,10);
        if (caisseNom) {
            filename += '_' + caisseNom.replace(/[^a-z0-9]/gi, '_');
        }
        doc.save(filename + '.pdf');
    }

    // Fonction pour exporter en Excel avec totaux embelli
    function exporterExcel() {
        // Vérifier si XLSX est disponible
        if (typeof XLSX === 'undefined') {
            alert("La fonction Excel nécessite la bibliothèque SheetJS. Veuillez patienter pendant le chargement...");
            location.reload();
            return;
        }

        var table = document.getElementById('livre-caisse-table');
        var title = "LIVRE DE CAISSE";
        var periode = "";

        // Récupérer les dates du filtre
        var dateDebut = document.getElementById('date_debut').value;
        var dateFin = document.getElementById('date_fin').value;

        if (dateDebut && dateFin) {
            periode = "Période du " + formatDate(dateDebut) + " au " + formatDate(dateFin);
        }

        // Récupérer le nom de la caisse si sélectionnée
        var caisseSelect = document.getElementById('caisse_id');
        var caisseNom = "";
        if (caisseSelect && caisseSelect.value) {
            caisseNom = caisseSelect.options[caisseSelect.selectedIndex].text;
        }

        // Récupérer les totaux
        var totalEntrees = document.querySelector('.total-entrees') ? document.querySelector('.total-entrees').innerText : "0 FCFA";
        var totalSorties = document.querySelector('.total-sorties') ? document.querySelector('.total-sorties').innerText : "0 FCFA";
        var soldeFinalElement = document.querySelector('.solde-final-row strong span');
        var soldeFinal = soldeFinalElement ? soldeFinalElement.innerText : "0 FCFA";

        // Créer un nouveau workbook
        var wb = XLSX.utils.book_new();

        // Préparer les données
        var data = [];

        // Ligne 1: Titre
        data.push([title]);

        // Ligne 2: Caisse
        if (caisseNom) {
            data.push(["Caisse:", caisseNom]);
        }

        // Ligne 3: Période
        if (periode) {
            data.push(["Période:", periode]);
        }

        // Ligne 4: Date d'édition
        data.push(["Date d'édition:", new Date().toLocaleDateString('fr-FR')]);

        // Ligne vide
        data.push([]);

        // Récupérer les en-têtes
        var headers = [];
        table.querySelectorAll('thead th').forEach(function(th) {
            headers.push(th.innerText);
        });
        data.push(headers);

        // Récupérer les données du tbody
        table.querySelectorAll('tbody tr').forEach(function(row) {
            var rowData = [];
            row.querySelectorAll('td').forEach(function(td) {
                rowData.push(td.innerText);
            });
            if (rowData.length > 0) {
                data.push(rowData);
            }
        });

        // Ligne vide avant les totaux
        data.push([]);

        // Ajouter les totaux avec style
        data.push(["", "", "TOTAUX:", totalEntrees, totalSorties, ""]);
        data.push(["", "", "SOLDE FINAL:", "", "", soldeFinal]);

        // Ligne vide
        data.push([]);

        // Ajouter un récapitulatif
        data.push(["RÉCAPITULATIF:", "", "", "", "", ""]);
        data.push(["Total Entrées:", totalEntrees, "", "Total Sorties:", totalSorties, ""]);
        data.push(["Solde Final:", soldeFinal, "", "", "", ""]);

        // Créer la feuille Excel
        var ws = XLSX.utils.aoa_to_sheet(data);

        // Définir les styles des cellules
        var range = XLSX.utils.decode_range(ws['!ref']);

        // Style pour le titre (ligne 1)
        if (ws['A1']) {
            ws['A1'].s = {
                font: { sz: 16, bold: true },
                alignment: { horizontal: 'center' }
            };
            // Fusionner les cellules pour le titre
            ws['!merges'] = ws['!merges'] || [];
            ws['!merges'].push({ s: { r: 0, c: 0 }, e: { r: 0, c: 5 } });
        }

        // Style pour les en-têtes de colonnes
        var headerRow = caisseNom ? 5 : 4; // Ajuster selon le nombre de lignes d'en-tête
        for (var C = 0; C <= 5; ++C) {
            var cell = XLSX.utils.encode_cell({r: headerRow, c: C});
            if (ws[cell]) {
                ws[cell].s = {
                    font: { bold: true, color: { rgb: "FFFFFF" } },
                    fill: { fgColor: { rgb: "4F81BD" } },
                    alignment: { horizontal: 'center' }
                };
            }
        }

        // Style pour la ligne des totaux
        var totalRow = range.e.r - 5; // Avant-dernière ligne
        for (var C = 0; C <= 5; ++C) {
            var cell = XLSX.utils.encode_cell({r: totalRow, c: C});
            if (ws[cell]) {
                ws[cell].s = {
                    font: { bold: true },
                    fill: { fgColor: { rgb: "E9ECEF" } }
                };
            }
        }

        // Style pour la ligne du solde final
        var soldeRow = totalRow + 1;
        for (var C = 0; C <= 5; ++C) {
            var cell = XLSX.utils.encode_cell({r: soldeRow, c: C});
            if (ws[cell]) {
                ws[cell].s = {
                    font: { bold: true },
                    fill: { fgColor: { rgb: "D4EDDA" } }
                };
            }
        }

        // Couleurs pour les montants
        for (var R = headerRow + 1; R < totalRow; ++R) {
            var entreeCell = XLSX.utils.encode_cell({r: R, c: 3});
            var sortieCell = XLSX.utils.encode_cell({r: R, c: 4});

            if (ws[entreeCell] && ws[entreeCell].v && parseFloat(ws[entreeCell].v) > 0) {
                ws[entreeCell].s = { font: { color: { rgb: "28A745" }, bold: true } };
            }

            if (ws[sortieCell] && ws[sortieCell].v && parseFloat(ws[sortieCell].v) > 0) {
                ws[sortieCell].s = { font: { color: { rgb: "DC3545" }, bold: true } };
            }
        }

        // Style des colonnes
        var wscols = [
            {wch: 15}, // Référence
            {wch: 12}, // Date
            {wch: 40}, // Désignation
            {wch: 15}, // Entrée
            {wch: 15}, // Sortie
            {wch: 15}  // Solde
        ];
        ws['!cols'] = wscols;

        // Ajouter la feuille au workbook
        XLSX.utils.book_append_sheet(wb, ws, "Livre de Caisse");

        // Générer et télécharger le fichier Excel
        var filename = 'livre_caisse_' + new Date().toISOString().slice(0,10);
        if (caisseNom) {
            filename += '_' + caisseNom.replace(/[^a-z0-9]/gi, '_');
        }
        XLSX.writeFile(wb, filename + '.xlsx');
    }

    // Fonction pour afficher un aperçu PDF avec totaux embelli
    function previewPDF() {
        var title = "LIVRE DE CAISSE";
        var periode = "";

        // Récupérer les dates du filtre
        var dateDebut = document.getElementById('date_debut').value;
        var dateFin = document.getElementById('date_fin').value;

        if (dateDebut && dateFin) {
            periode = "Période du " + formatDate(dateDebut) + " au " + formatDate(dateFin);
        }

        // Récupérer le nom de la caisse si sélectionnée
        var caisseSelect = document.getElementById('caisse_id');
        var caisseNom = "";
        if (caisseSelect && caisseSelect.value) {
            caisseNom = caisseSelect.options[caisseSelect.selectedIndex].text;
        }

        // Récupérer les totaux
        var totalEntrees = document.querySelector('.total-entrees') ? document.querySelector('.total-entrees').innerText : "0 FCFA";
        var totalSorties = document.querySelector('.total-sorties') ? document.querySelector('.total-sorties').innerText : "0 FCFA";
        var soldeFinalElement = document.querySelector('.solde-final-row strong span');
        var soldeFinal = soldeFinalElement ? soldeFinalElement.innerText : "0 FCFA";
        var soldeClass = soldeFinalElement ? soldeFinalElement.className : "text-success";

        // Créer le contenu de l'aperçu
        var previewContent = `
            <div class="pdf-preview">
                <div class="pdf-header">
                    <div class="pdf-title">${title}</div>
                    ${caisseNom ? `<div class="pdf-period"><strong>Caisse:</strong> ${caisseNom}</div>` : ''}
                    ${periode ? `<div class="pdf-period"><strong>Période:</strong> ${periode}</div>` : ''}
                    <div class="pdf-period"><strong>Date d'édition:</strong> ${new Date().toLocaleDateString('fr-FR')}</div>
                </div>
        `;

        // Copier seulement le tbody du tableau
        var table = document.getElementById('livre-caisse-table').cloneNode(true);
        table.className = 'pdf-table';

        // Nettoyer le tableau pour l'aperçu
        var buttons = table.querySelectorAll('button, a, .alert');
        buttons.forEach(function(btn) {
            btn.parentNode.removeChild(btn);
        });

        // Supprimer le tfoot existant
        var tfoot = table.querySelector('tfoot');
        if (tfoot) {
            tfoot.parentNode.removeChild(tfoot);
        }

        previewContent += table.outerHTML;

        // Ajouter les totaux embelli
        previewContent += `
            <table class="pdf-table" style="margin-top: 10px;">
                <tr class="pdf-totals">
                    <td colspan="3" style="text-align: right; font-weight: bold;">TOTAUX:</td>
                    <td class="text-entree" style="font-weight: bold;">${totalEntrees}</td>
                    <td class="text-sortie" style="font-weight: bold;">${totalSorties}</td>
                    <td></td>
                </tr>
                <tr class="pdf-solde-final">
                    <td colspan="3" style="text-align: right; font-weight: bold;">SOLDE FINAL:</td>
                    <td colspan="2" style="text-align: center; font-size: 10px;">
                        <strong>Entrées:</strong> ${totalEntrees} | <strong>Sorties:</strong> ${totalSorties}
                    </td>
                    <td style="text-align: right; font-weight: bold;" class="${soldeClass}">
                        ${soldeFinal}
                    </td>
                </tr>
            </table>

            <div style="background-color: #f0f8ff; padding: 10px; margin-top: 15px; border: 1px solid #000; text-align: center;">
                <strong>RÉCAPITULATIF COMPLET</strong><br>
                <div style="margin-top: 5px;">
                    <span style="color: #28a745; font-weight: bold;">Total Entrées: ${totalEntrees}</span> |
                    <span style="color: #dc3545; font-weight: bold;">Total Sorties: ${totalSorties}</span> |
                    <span class="${soldeClass}" style="font-weight: bold;">Solde Final: ${soldeFinal}</span>
                </div>
            </div>
        `;

        // Ajouter le footer
        previewContent += `
                <div class="pdf-footer">
                    Document généré le ${new Date().toLocaleString('fr-FR')} | ${window.location.href}
                </div>
            </div>
        `;

        // Afficher dans la modal
        $('#pdfPreviewModal .modal-body').html(previewContent);
        $('#pdfPreviewModal').modal('show');
    }

    // Fonction utilitaire pour formater les dates
    function formatDate(dateString) {
        if (!dateString) return '';
        var date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    // Gérer le changement de type d'opération
    $('#type').change(function() {
        var montantInput = $('#montant');
        var currentVal = montantInput.val();
        if (currentVal && currentVal > 0) {
            montantInput.val(Math.abs(currentVal));
        }
    });

    // Formater les montants dans les cartes de caisse
    $('.montant-caisse').each(function() {
        var montant = parseFloat($(this).text().replace(/ /g, ''));
        if (montant >= 1000000) {
            $(this).addClass('text-primary');
        } else if (montant >= 0) {
            $(this).addClass('text-success');
        } else {
            $(this).addClass('text-danger');
        }
    });

    // Vérifier que les bibliothèques sont chargées
    $(document).ready(function() {
        // Vérifier après un délai si les bibliothèques sont chargées
        setTimeout(function() {
            if (typeof jsPDF !== 'undefined' && typeof XLSX !== 'undefined') {
                console.log("Bibliothèques PDF et Excel chargées avec succès");
            } else {
                console.warn("Certaines bibliothèques ne sont pas chargées");
            }
        }, 2000);
    });
</script>
<script>
    // Variables globales
    var operationToDelete = null;

    // Gérer le clic sur le bouton d'édition
    $(document).on('click', '.edit-operation', function() {
        var operationId = $(this).data('id');

        // Récupérer les données de l'opération via AJAX
        $.ajax({
            url: '<?php echo base_url('admin/income/get_operation/'); ?>' + operationId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var operation = response.operation;

                    // Remplir le formulaire d'édition
                    $('#edit_operation_id').val(operation.id);
                    $('#edit_caisse_id').val(operation.caisse_id);
                    $('#edit_date').val(operation.date);
                    $('#edit_type').val(operation.type);
                    $('#edit_designation').val(operation.designation);
                    $('#edit_exp_head_id').val(operation.exp_head_id);
                    $('#edit_exp_category_name').val(operation.exp_category_name);
                    $('#edit_montant').val(operation.montant);
                    $('#edit_reference').val(operation.reference);
                    $('#edit_mode_paiement').val(operation.mode_paiement);

                    // Déclencher le changement pour les événements liés
                    $('#edit_type').trigger('change');
                } else {
                    alert('Erreur lors du chargement des données');
                }
            },
            error: function() {
                alert('Erreur de connexion au serveur');
            }
        });
    });

    // Gérer le clic sur le bouton de suppression
    $(document).on('click', '.delete-operation', function() {
        var operationId = $(this).data('id');
        var reference = $(this).data('reference');
        var designation = $(this).data('designation');

        operationToDelete = operationId;

        // Afficher les informations dans la modal
        $('#delete-operation-info').html(
            '<strong>Référence:</strong> ' + reference + '<br>' +
            '<strong>Désignation:</strong> ' + designation
        );

        // Afficher la modal de confirmation
        $('#deleteConfirmModal').modal('show');
    });

    // Gérer la confirmation de suppression
    $('#confirm-delete').click(function() {
        if (operationToDelete) {
            // Envoyer la requête de suppression
            $.ajax({
                url: '<?php echo base_url('admin/income/delete_operation/'); ?>' + operationToDelete,
                type: 'POST',
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    $('#deleteConfirmModal').modal('hide');

                    if (response.success) {
                        // Supprimer la ligne du tableau
                        $('#operation-' + operationToDelete).fadeOut(300, function() {
                            $(this).remove();
                            toastr.success(response.message);
                            // Recharger la page pour recalculer les soldes
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        });
                    } else {
                        toastr.error(response.message || 'Erreur lors de la suppression');
                    }
                },
                error: function() {
                    $('#deleteConfirmModal').modal('hide');
                    toastr.error('Erreur de connexion au serveur');
                }
            });
        }
    });

    // Soumission du formulaire d'édition
    $('#formEditOperation').submit(function(e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editOperationModal').modal('hide');
                    toastr.success(response.message);
                    // Recharger la page pour mettre à jour les données
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    // Afficher les erreurs de validation
                    if (response.errors) {
                        var errorHtml = '<div class="alert alert-danger"><ul>';
                        $.each(response.errors, function(key, value) {
                            errorHtml += '<li>' + value + '</li>';
                        });
                        errorHtml += '</ul></div>';
                        $('#editOperationModal .modal-body').prepend(errorHtml);
                    } else {
                        toastr.error(response.message || 'Erreur lors de la modification');
                    }
                }
            },
            error: function() {
                toastr.error('Erreur de connexion au serveur');
            }
        });
    });

    // Nettoyer les erreurs lorsque la modal d'édition est fermée
    $('#editOperationModal').on('hidden.bs.modal', function() {
        $(this).find('.alert').remove();
    });

    // Gérer le changement de type dans l'édition
    $('#edit_type').change(function() {
        var montantInput = $('#edit_montant');
        var currentVal = montantInput.val();
        if (currentVal && currentVal > 0) {
            montantInput.val(Math.abs(currentVal));
        }
    });

    // Gérer la sélection de catégorie de dépense dans l'édition
    $('#edit_exp_head_id').change(function() {
        var selectedOption = $(this).find(':selected');
        var categoryName = selectedOption.data('name');
        $('#edit_exp_category_name').val(categoryName);
    });

    // S'assurer que le mode d'impression masque les boutons d'actions
    function updatePrintStyles() {
        var style = document.createElement('style');
        style.innerHTML = '@media print { .no-print { display: none !important; } }';
        document.head.appendChild(style);
    }

    // Initialiser au chargement de la page
    $(document).ready(function() {
        updatePrintStyles();

        // Gérer aussi la sélection de catégorie dans le formulaire d'ajout
        $('#exp_head_id').change(function() {
            var selectedOption = $(this).find(':selected');
            var categoryName = selectedOption.data('name');
            $('#exp_category_name').val(categoryName);
        });
    });
</script>

</body>
</html>