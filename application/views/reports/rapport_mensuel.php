<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$conn = new mysqli("localhost","root","","diago");

// Récupération des filtres de période
$date_debut = isset($_POST['date_debut']) ? $_POST['date_debut'] : '';
$date_fin   = isset($_POST['date_fin'])   ? $_POST['date_fin']   : '';

$where = "";
if (!empty($date_debut) && !empty($date_fin)) {
    $where = " WHERE jc.date_operation BETWEEN '$date_debut' AND '$date_fin' ";
}

// SQL avec jointure pour récupérer le nom de la catégorie
$sql = "
    SELECT 
        COALESCE(eh.exp_category, jc.category) as nom_category,
        MONTH(jc.date_operation) as mois,
        SUM(jc.montant) as total
    FROM journal_comptable jc
    LEFT JOIN expense_head eh ON jc.category = eh.id
    $where
    GROUP BY jc.category, mois
    ORDER BY nom_category, mois
";

$result = $conn->query($sql);

// Construire un tableau simple sans classification par type
$donnees_par_categorie = [];

foreach ($result as $row) {
    $nom_category = $row['nom_category'];
    $mois = (int)$row['mois'];
    $total = $row['total'];

    if (isset($donnees_par_categorie[$nom_category][$mois])) {
        $donnees_par_categorie[$nom_category][$mois] += $total;
    } else {
        $donnees_par_categorie[$nom_category][$mois] = $total;
    }
}

// Fonction pour calculer les totaux par mois d'un groupe de catégories
function calculer_totaux_groupe($categories, $donnees_par_categorie) {
    $totaux_mois = array_fill(1, 12, 0);

    foreach ($categories as $categorie) {
        if (isset($donnees_par_categorie[$categorie])) {
            foreach ($donnees_par_categorie[$categorie] as $mois => $montant) {
                $totaux_mois[$mois] += $montant;
            }
        }
    }

    return $totaux_mois;
}

// Fonction pour afficher une ligne
function afficher_ligne($libelle, $donnees_mois, $is_bold = false, $is_section = false, $is_total = false, $indentation = 0) {
    $class = '';
    if ($is_bold) $class .= ' font-weight-bold';
    if ($is_section) $class .= ' table-section-header';
    if ($is_total) $class .= ' table-total-general';

    $style_indentation = '';
    if ($indentation > 0) {
        $style_indentation = 'style="padding-left: ' . ($indentation * 20) . 'px;"';
    }

    $total_ligne = 0;
    ?>
    <tr class="<?= $class ?>">
        <td class="text-left" <?= $style_indentation ?>><?= $libelle ?></td>
        <?php for ($i=1; $i<=12; $i++):
            $val = isset($donnees_mois[$i]) ? $donnees_mois[$i] : 0;
            $total_ligne += $val;
            ?>
            <td class="text-right"><?= $val != 0 ? number_format($val, 0, ',', ' ') : '-' ?></td>
        <?php endfor; ?>
        <td class="text-right <?= $is_bold ? 'font-weight-bold' : '' ?>"><?= number_format($total_ligne, 0, ',', ' ') ?></td>
    </tr>
    <?php
    return $total_ligne;
}
?>

<style>
    .text-primary { color: black; text-transform: uppercase; }
    .table th, .table td { text-align: left; vertical-align: middle; }
    .table th.text-right, .table td.text-right { text-align: right; }
    .table-warning { background-color: #f9f9c5; }
    .table-section-header {
        background-color: #e9ecef;
        font-weight: bold;
        font-size: 1.1em;
    }
    .table-sous-total {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    .table-total-general {
        background-color: #d4edda;
        font-weight: bold;
        font-size: 1.1em;
    }
    .table-negative {
        background-color: #f8d7da;
    }
    .negative { color: #dc3545; }
    .positive { color: #28a745; }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-money"></i> Rapport financier par catégories</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">

                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> Filtrer par période</h3>
                    </div>

                    <!-- Formulaire filtre -->
                    <form role="form" action="" method="post" class="form-inline p-3">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="form-group mr-2">
                            <label for="date_debut">Date début : </label>
                            <input type="date" class="form-control" name="date_debut" value="<?= htmlspecialchars($date_debut) ?>">
                        </div>
                        <div class="form-group mr-2">
                            <label for="date_fin">Date fin : </label>
                            <input type="date" class="form-control" name="date_fin" value="<?= htmlspecialchars($date_fin) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Rechercher</button>
                        <button type="button" class="btn btn-success" onclick="window.print()"><i class="fa fa-print"></i> Imprimer</button>
                        <a href="<?php echo base_url() ?>report/finance" type="button" class="btn btn-primary btn-xs" style="width: 99px;height: 23px">
                            <i class="fa fa-arrow-left"></i> </a>
                    </form>

                    <!-- Résultat -->
                    <div class="box-body table-responsive">
                        <h3 class="box-title titlefix"><i class="fa fa-bar-chart"></i> Rapport par catégories</h3>
                        <table class="table table-bordered table-hover table-striped example">
                            <thead>
                            <tr>
                                <th>Catégories</th>
                                <?php
                                $moisTexte = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Déc'];
                                foreach($moisTexte as $m) echo "<th class='text-center'>$m</th>";
                                ?>
                                <th class="text-center">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $totaux_mois_globaux = array_fill(1, 12, 0);
                            $total_general_categories = 0;

                            // SECTION 1: FRAIS DE COMMUNICATION
                            afficher_ligne("FRAIS DE COMMUNICATION", [], true, true);

                            // Afficher les catégories qui contiennent "communication", "facture", "frais" etc.
                            $categories_communication = [];
                            foreach ($donnees_par_categorie as $categorie => $donnees_mois) {
                                if (stripos($categorie, 'communication') !== false ||
                                    stripos($categorie, 'facture') !== false ||
                                    stripos($categorie, 'frais') !== false ||
                                    stripos($categorie, 'sodeci') !== false ||
                                    stripos($categorie, 'cie') !== false) {
                                    $categories_communication[] = $categorie;
                                    afficher_ligne($categorie, $donnees_mois, false, false, false, 1);
                                    // Ajouter aux totaux globaux
                                    foreach ($donnees_mois as $mois => $montant) {
                                        $totaux_mois_globaux[$mois] += $montant;
                                        $total_general_categories += $montant;
                                    }
                                }
                            }

                            $total_communication = calculer_totaux_groupe($categories_communication, $donnees_par_categorie);
                            afficher_ligne("Sous total", $total_communication, true, false, true);

                            // SECTION 1: FRAIS DE COMMUNICATION
                            afficher_ligne("FRAIS DE TRANSPORT", [], true, true);

                            $categories_transport = [];
                            foreach ($donnees_par_categorie as $categorie => $donnees_mois) {
                                if (stripos($categorie, 'transport') !== false ||
                                    stripos($categorie, 'transport banque') !== false ||
                                    stripos($categorie, 'transport cnps') !== false ||
                                    stripos($categorie, 'transport prospection') !== false ||
                                    stripos($categorie, 'transport impot') !== false) {
                                    $categories_transport[] = $categorie;
                                    afficher_ligne($categorie, $donnees_mois, false, false, false, 1);
                                    // Ajouter aux totaux globaux
                                    foreach ($donnees_mois as $mois => $montant) {
                                        $totaux_mois_globaux[$mois] += $montant;
                                        $total_general_categories += $montant;
                                    }
                                }
                            }

                            $total_transport = calculer_totaux_groupe($categories_transport, $donnees_par_categorie);
                            afficher_ligne("Sous total", $total_transport, true, false, true);

                            // SECTION 2: CHARGES DU PERSONNEL
                            afficher_ligne("CHARGES DU PERSONNEL", [], true, true);

                            $categories_personnel = [];
                            foreach ($donnees_par_categorie as $categorie => $donnees_mois) {
                                if (stripos($categorie, 'personnel') !== false ||
                                    stripos($categorie, 'salaire') !== false ||
                                    stripos($categorie, 'charge') !== false) {
                                    $categories_personnel[] = $categorie;
                                    afficher_ligne($categorie, $donnees_mois, false, false, false, 1);
                                    // Ajouter aux totaux globaux
                                    foreach ($donnees_mois as $mois => $montant) {
                                        $totaux_mois_globaux[$mois] += $montant;
                                        $total_general_categories += $montant;
                                    }
                                }
                            }

                            $total_personnel = calculer_totaux_groupe($categories_personnel, $donnees_par_categorie);
                            afficher_ligne("Sous total", $total_personnel, true, false, true);

                            // SECTION 3: ACHAT DE PRODUIT
                            afficher_ligne("ACHAT DE PRODUIT", [], true, true);

                            $categories_produit_achat = [];
                            foreach ($donnees_par_categorie as $categorie => $donnees_mois) {
                                if (stripos($categorie, 'achat de produit') !== false ||
                                    stripos($categorie, 'achat de marchadise') !== false ||
                                    stripos($categorie, 'achat produit') !== false ||
                                    stripos($categorie, 'achat de produits') !== false) {
                                    $categories_produit_achat[] = $categorie;
                                    afficher_ligne($categorie, $donnees_mois, false, false, false, 1);
                                    // Ajouter aux totaux globaux
                                    foreach ($donnees_mois as $mois => $montant) {
                                        $totaux_mois_globaux[$mois] += $montant;
                                        $total_general_categories += $montant;
                                    }
                                }
                            }

                            $total_produit_achat = calculer_totaux_groupe($categories_produit_achat, $donnees_par_categorie);


                            // SECTION 3: VENTE DE PRODUIT
                            afficher_ligne("VENTE DE PRODUIT", [], true, true);

                            $categories_produit = [];
                            foreach ($donnees_par_categorie as $categorie => $donnees_mois) {
                                if (stripos($categorie, 'vente de produit') !== false ||
                                    stripos($categorie, 'vente de marchadise') !== false ||
                                    stripos($categorie, '') !== false ||
                                    stripos($categorie, 'vente de produits') !== false) {
                                    $categories_produit[] = $categorie;
                                    afficher_ligne($categorie, $donnees_mois, false, false, false, 1);
                                    // Ajouter aux totaux globaux
                                    foreach ($donnees_mois as $mois => $montant) {
                                        $totaux_mois_globaux[$mois] += $montant;
                                        $total_general_categories += $montant;
                                    }
                                }
                            }

                            $total_produit = calculer_totaux_groupe($categories_produit, $donnees_par_categorie);


                            // SECTION 3: IMPOTS ET TAXES
                            afficher_ligne("IMPOTS ET TAXES", [], true, true);

                            $categories_impots = [];
                            foreach ($donnees_par_categorie as $categorie => $donnees_mois) {
                                if (stripos($categorie, 'impôt') !== false ||
                                    stripos($categorie, 'impot') !== false ||
                                    stripos($categorie, 'taxe') !== false ||
                                    stripos($categorie, 'fiscal') !== false) {
                                    $categories_impots[] = $categorie;
                                    afficher_ligne($categorie, $donnees_mois, false, false, false, 1);
                                    // Ajouter aux totaux globaux
                                    foreach ($donnees_mois as $mois => $montant) {
                                        $totaux_mois_globaux[$mois] += $montant;
                                        $total_general_categories += $montant;
                                    }
                                }
                            }

                            $total_impots = calculer_totaux_groupe($categories_impots, $donnees_par_categorie);
                            afficher_ligne("Sous total", $total_impots, true, false, true);

                            // SECTION 4: AUTRES CATEGORIES (ce qui n'a pas été classé)
                            afficher_ligne("AUTRES CATEGORIES", [], true, true);

                            $categories_deja_affichees = array_merge($categories_communication,$categories_produit,$categories_produit_achat,$categories_transport, $categories_personnel, $categories_impots);
                            $categories_autres = [];

                            foreach ($donnees_par_categorie as $categorie => $donnees_mois) {
                                if (!in_array($categorie, $categories_deja_affichees)) {
                                    $categories_autres[] = $categorie;
                                    afficher_ligne($categorie, $donnees_mois, false, false, false, 1);
                                    // Ajouter aux totaux globaux
                                    foreach ($donnees_mois as $mois => $montant) {
                                        $totaux_mois_globaux[$mois] += $montant;
                                        $total_general_categories += $montant;
                                    }
                                }
                            }

                            $total_autres = calculer_totaux_groupe($categories_autres, $donnees_par_categorie);
                            afficher_ligne("Sous total", $total_autres, true, false, true);

                            // TOTAL GENERAL
                            ?>
                            <tr class="table-total-general">
                                <td class="font-weight-bold">TOTAL GENERAL</td>
                                <?php for ($i=1; $i<=12; $i++): ?>
                                    <td class="text-right font-weight-bold"><?= $totaux_mois_globaux[$i] != 0 ? number_format($totaux_mois_globaux[$i], 0, ',', ' ') : '-' ?></td>
                                <?php endfor; ?>
                                <td class="text-right font-weight-bold"><?= number_format($total_general_categories, 0, ',', ' ') ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>