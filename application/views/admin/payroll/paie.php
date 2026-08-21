<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

$moisEn = [
'January' => 'Janvier', 'February' => 'Février', 'March' => 'Mars',
'April' => 'Avril', 'May' => 'Mai', 'June' => 'Juin',
'July' => 'Juillet', 'August' => 'Août', 'September' => 'Septembre',
'October' => 'Octobre', 'November' => 'Novembre', 'December' => 'Décembre',
];

// CALCULS DU BULLETIN DE PAIE
$total_brute = $result["categorie_salaire"] + $result["sursalaire"] + $result["prime_anc"] +
$result["prime_trans"] + $result["forfait_hs"] + $result["prime_resp"] +
$result["prime_rend"] + $result["prime_risque"] + $result["prime_assi"] +
$result["prime_grati"] + $result["conge"];

$total_pourcentage = $total_brute * 0.1;
$primet = 30000;

// Prime transport (exonérée jusqu'à 30,000)
$final_trans = ($result["prime_trans"] > $primet) ? $result["prime_trans"] - $primet : 0;

// Prime rendement (exonérée jusqu'à 10% du total brut)
$final_rend = ($result["prime_rend"] > $total_pourcentage) ? $result["prime_rend"] - $total_pourcentage : 0;

// Prime risque (exonérée jusqu'à 10% du total brut)
$final_risq = ($result["prime_risque"] > $total_pourcentage) ? $result["prime_risque"] - $total_pourcentage : 0;

// Prime assiduité (exonérée jusqu'à 10% du total brut)
$final_assi = ($result["prime_assi"] > $total_pourcentage) ? $result["prime_assi"] - $total_pourcentage : 0;

// Forfait HS (exonéré jusqu'à 10% du total brut)
$final_forfait = ($result["forfait_hs"] > $total_pourcentage) ? $result["forfait_hs"] - $total_pourcentage : 0;

// Sursalaire (exonéré jusqu'à 10% du total brut)
$final_sura = ($result["sursalaire"] > $total_pourcentage) ? $result["sursalaire"] - $total_pourcentage : 0;

// TOTAL FISCAL (base imposable)
$total_fiscal = $result["categorie_salaire"] + $result["prime_anc"] + $final_trans +
$final_rend + $final_risq + $final_assi + $final_sura;

// TOTAL BRUT SOCIAL (base CNPS/CMU)
$total_social = $total_brute - $result["prime_trans"];

// CALCUL ITS (Impôt sur le Traitement des Salaires)
$impot = 0;
$salaire_restant = $total_fiscal;

if ($salaire_restant > 8000000) {
$impot += ($salaire_restant - 8000000) * 0.32;
$salaire_restant = 8000000;
}

if ($salaire_restant > 2400000) {
$impot += ($salaire_restant - 2400000) * 0.28;
$salaire_restant = 2400000;
}

if ($salaire_restant > 800000) {
$impot += ($salaire_restant - 800000) * 0.24;
$salaire_restant = 800000;
}

if ($salaire_restant > 240000) {
$impot += ($salaire_restant - 240000) * 0.21;
$salaire_restant = 240000;
}

if ($salaire_restant > 75000) {
$impot += ($salaire_restant - 75000) * 0.16;
}

// Réduction familiale
$coef = $result["part_igr"] ?? 1;
$reductions = [1=>0, 1.5=>5500, 2=>11000, 2.5=>16500, 3=>22000,
3.5=>27500, 4=>33000, 4.5=>38500, 5=>44000];

$reduction = $reductions[$coef] ?? 0;
$impot_net = max($impot - $reduction, 0);
$its = round($impot_net, 2);

// CALCUL DES COTISATIONS SOCIALES
$cnps = $total_social * 0.08;  // Patronale 8%
$cmu = $total_social * 0.05;   // CMU 5%

// TOTAL REVENU IMPOSABLE
$total_revenu = $its + $cmu + $cnps;

// SALAIRE NET
$net_salary = $total_brute - $cnps - $cmu - $its;
?>

<style type="text/css">
    /* VOTRE CSS EXISTANT... */

    /* NOUVEAUX STYLES POUR LA DATATABLE */
    #livre-paie-table {
        border-collapse: collapse;
        width: 100%;
        font-size: 14px;
    }

    #livre-paie-table thead th {
        background-color: #f8f9fa;
        color: #2c3e50;
        font-weight: 600;
        padding: 12px 8px;
        border-bottom: 2px solid #dee2e6;
        text-transform: uppercase;
        font-size: 13px;
        position: relative;
    }

    #livre-paie-table thead th:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0;
        top: 20%;
        height: 60%;
        width: 1px;
        background-color: #dee2e6;
    }

    #livre-paie-table tbody td {
        padding: 10px 8px;
        border-bottom: 1px solid #e9ecef;
        color: black;
        position: relative;
    }

    /* Traits verticaux entre les colonnes */
    #livre-paie-table tbody td:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0;
        top: 15%;
        height: 70%;
        width: 1px;
        background-color: #f0f0f0;
    }

    /* Alternance des couleurs de lignes */
    #livre-paie-table tbody tr:nth-child(even) {
        background-color: #fafafa;
    }

    #livre-paie-table tbody tr:nth-child(odd) {
        background-color: #ffffff;
    }

    #livre-paie-table tbody tr:hover {
        background-color: #f1f8ff;
        transition: background-color 0.2s ease;
    }

    /* Style pour la ligne des totaux */
    #livre-paie-table tbody tr.total-bg td {
        background-color: #4a90e2 !important;
        color: white !important;
        font-weight: bold;
        border-top: 2px solid #4a90e2;
        border-bottom: none;
    }

    #livre-paie-table tbody tr.total-bg td::after {
        background-color: #34495e !important;
    }

    /* Style pour les nombres - alignement à droite */
    #livre-paie-table tbody td:not(:first-child):not(:nth-child(2)) {
        text-align: right;
        font-family: 'Courier New', monospace;
        font-weight: 500;
    }

    /* Style pour l'en-tête des nombres */
    #livre-paie-table thead th:not(:first-child):not(:nth-child(2)) {
        text-align: right;
    }

    /* Bordures renforcées */
    #livre-paie-table {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        overflow: hidden;
    }

    /* Style pour la recherche */
    #search-employee {
        border: 1px solid #ced4da;
        padding: 5px 12px;
        border-radius: 4px 0 0 4px;
    }

    #search-employee:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
    }

    /* Responsive */
    @media (max-width: 768px) {
        #livre-paie-table {
            font-size: 12px;
        }

        #livre-paie-table thead th,
        #livre-paie-table tbody td {
            padding: 6px 4px;
        }
    }
</style>
<style>
    /* Amélioration de l'apparence générale */
    .box.removeboxmius {
        border: none;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        border-radius: 8px;
        overflow: hidden;
    }

    .box-header {
        background: linear-gradient(135deg, #4a90e2 0%, #4a90e2 100%);
        color: white;
        border-bottom: none;
        padding: 15px 20px;
    }

    .box-header h3 {
        color: white;
        margin: 0;
        font-weight: 600;
    }

    .box-header .box-tools {
        top: 15px;
    }

    /* Style des boutons */
    .btn-primary {
        background: linear-gradient(to right, #4a90e2, #4a90e2);
        border: none;
        border-radius: 4px;
    }

    .btn-success {
        background: linear-gradient(to right, #38a169, #48bb78);
        border: none;
        border-radius: 4px;
    }

    /* Style du tableau amélioré */
    .dataTables_wrapper {
        padding: 0 10px;
    }

    .dataTables_length,
    .dataTables_filter {
        margin-bottom: 15px;
    }

    /* Hover effect amélioré */
    #livre-paie-table tbody tr:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        position: relative;
        z-index: 1;
    }

    /* Animation */
    #livre-paie-table tbody tr {
        transition: all 0.3s ease;
    }
</style>
<div class="content-wrapper" style="min-height: 946px;">
    <section class="content">


        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">

                    <!-- En-tête avec titre, filtre et boutons d'exportation -->
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-money"></i> Livre de paie</h3>
                        <div class="pull-right box-tools">
                            <!-- Formulaire de recherche par nom ou matricule -->
                            <div class="form-inline" style="display: inline-block; margin-right: 15px;">
                                <div class="input-group input-group-sm">
                                    <input type="text" id="search-employee" class="form-control"
                                           placeholder="Rechercher par nom ou matricule..."
                                           style="width: 250px;">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default" onclick="performSearch()">
                                            <i class="fa fa-search"></i>
                                        </button>
                                        <button type="button" class="btn btn-default" onclick="clearSearch()" title="Effacer la recherche">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <form role="form" action="<?php echo site_url('admin/payroll/paie') ?>" method="post" class="form-inline" style="display: inline-block;">
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="form-group">
                                    <select class="form-control input-sm" name="search_type" onchange="showdate(this.value)">
                                        <?php foreach ($searchlist as $key => $search) { ?>
                                            <option value="<?php echo $key ?>" <?php echo (isset($search_type) && $search_type == $key) ? "selected" : ""; ?>>
                                                <?php echo $search ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group" id='date_result'></div>

                                <div class="form-group">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm">
                                        <i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?>
                                    </button>
                                </div>
                            </form>

                            <!-- Boutons d'exportation -->
                            <div class="btn-group" style="margin-left: 10px;">
                                <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-download"></i> Exporter
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="#" onclick="exportToPDF()">
                                            <i class="fa fa-file-pdf-o"></i> Export PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="exportToExcel()">
                                            <i class="fa fa-file-excel-o"></i> Export Excel
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Corps du tableau -->
                    <div class="box-body table-responsive">
                        <div class="download_label">

                            <br/><br/><br/><br/> <br/><br/><br/><br/>
                            <?php
                            echo "Livre de paie <br/><br/>";
                            echo "Période: ";
                            $this->customlib->get_postmessage();
                            ?>
                        </div>

                        <!-- Tableau du livre de paie -->
                        <table class="table table-striped table-bordered table-hover example" id="livre-paie-table">
                            <thead>
                            <tr>
                                <th class="text text-left text-primary">Matricule</th>
                                <th class="text text-left text-primary">Nom et prénom</th>
                                <th class="text text-left text-primary">Total Brute</th>
                                <th class="text text-left text-primary">Total Social</th>
                                <th class="text text-left text-primary">Total Fiscal</th>
                                <th class="text text-left text-primary">Salaire de base</th>
                                <th class="text text-left text-primary">Cnps</th>
                                <th class="text text-left text-primary">Cmu</th>
                                <th class="text text-left text-primary">ITS</th>
                                <th class="text text-left text-primary">Total retenu</th>
                                <th class="text text-left text-primary">Salaire net</th>
                            </tr>
                            </thead>
                            <tbody id="table-body">
                            <?php
                            foreach ($payrollList as $value) {

                                // Total brut recalculé (sans prendre gross_salary depuis la base)
                                $total_brute = $value['categorie_salaire'] + $value['sursalaire'] + $value['prime_anc'] +
                                    $value['prime_trans'] + $value['forfait_hs'] + $value['prime_resp'] +
                                    $value['prime_rend'] + $value['prime_risque'] + $value['prime_assi'] +
                                    $value['prime_grati'] + $value['conge'] + $value['autre_reve'];

                                // Total social (CNPS/CMU) = total brut - prime de transport
                                $total_social = $total_brute - $value['prime_trans'];
                                $total_revenu = $its + $cmu + $cnps;

                                // CNPS avec plafond retraite
                                // =============================
                                $plafond_cnps = 3375000; // 45 * 75 000

                                if ($total_social > $plafond_cnps) {
                                    $cnps_retraite_base = $plafond_cnps;
                                } else {
                                    $cnps_retraite_base = $total_social;
                                }

                                $etraite = $cnps_retraite_base;

                                // Cotisation CNPS sur base plafonnée
                                $cnps = $etraite * 0.063;  // 8%


                                // CNPS et CMU recalculés
                                //$cnps = $total_social * 0.08;  // 8%
                                $cmu  = 500;  // 5%


                                // Si epf_no est vide ou nul, on considère au moins 1 personne
                                $epf_no = !empty($result["epf_no"]) ? $result["epf_no"] : 1;

                                // Calcul du CMU total
                                $cmu_total = $epf_no * $cmu;

                                // =============================
                                // CALCUL DU TOTAL FISCAL
                                // =============================
                                $total_pourcentage = $total_brute * 0.1;
                                $primet = 30000;

                                // Prime transport
                                if ($value["prime_trans"] > $primet) {
                                    $trans = $value["prime_trans"] - $primet;
                                } else {
                                    $trans = 0;
                                }
                                $final_trans = $trans;

                                // Prime ancienneté
                                $prime_anc = isset($value["prime_anc"]) ? floatval($value["prime_anc"]) : 0;
                                if ($prime_anc > $total_pourcentage) {
                                    $ancien = $prime_anc - $total_pourcentage;
                                } else {
                                    $ancien = 0;
                                }
                                $final_anc = $ancien;

                                // Prime rendement
                                if ($value["prime_rend"] > $total_pourcentage) {
                                    $rends = $value["prime_rend"] - $total_pourcentage;
                                } else {
                                    $rends = 0;
                                }
                                $final_rend = $rends;

                                // Prime responsabilité
                                if ($value["prime_resp"] > $total_pourcentage) {
                                    $respo = $value["prime_resp"] - $total_pourcentage;
                                } else {
                                    $respo = 0;
                                }
                                $final_respo = $respo;

                                // Prime risque
                                if ($value["prime_risque"] > $total_pourcentage) {
                                    $risq = $value["prime_risque"] - $total_pourcentage;
                                } else {
                                    $risq = 0;
                                }
                                $final_risq = $risq;

                                // Autres revenus
                                if ($value["autre_reve"] > $total_pourcentage) {
                                    $autre = $value["autre_reve"] - $total_pourcentage;
                                } else {
                                    $autre = 0;
                                }
                                $final_autres = $autre;

                                // Prime assiduité
                                if ($value["prime_assi"] > $total_pourcentage) {
                                    $assi = $value["prime_assi"] - $total_pourcentage;
                                } else {
                                    $assi = 0;
                                }
                                $final_assi = $assi;

                                // Forfait heures sup
                                if ($value["forfait_hs"] > $total_pourcentage) {
                                    $forfait = $value["forfait_hs"] - $total_pourcentage;
                                } else {
                                    $forfait = 0;
                                }
                                $final_forfait = $forfait;

                                // Sursalaire
                                if ($value["sursalaire"] > $total_pourcentage) {
                                    $sura = $value["sursalaire"] - $total_pourcentage;
                                } else {
                                    $sura = 0;
                                }
                                $final_sura = $sura;

                                // Total fiscal (base imposable)
                                $total_fiscal = $value["categorie_salaire"] + $final_trans + $prime_anc + $final_rend +
                                    $final_risq + $final_respo + $final_assi + $value["sursalaire"] + $final_autres;

                                // =============================
                                // FIN CALCUL FISCAL

                                // =============================
                                // ITS avec barème progressif
                                // =============================
                                $impot = 0;
                                $categorie_salaire = $total_fiscal;

                                if ($categorie_salaire > 8000000) {
                                    $impot += ($categorie_salaire - 8000000) * 0.32;
                                    $categorie_salaire = 8000000;
                                }

                                if ($categorie_salaire > 2400000) {
                                    $impot += ($categorie_salaire - 2400000) * 0.28;
                                    $categorie_salaire = 2400000;
                                }

                                if ($categorie_salaire > 800000) {
                                    $impot += ($categorie_salaire - 800000) * 0.24;
                                    $categorie_salaire = 800000;
                                }

                                if ($categorie_salaire > 240000) {
                                    $impot += ($categorie_salaire - 240000) * 0.21;
                                    $categorie_salaire = 240000;
                                }

                                if ($categorie_salaire > 75000) {
                                    $impot += ($categorie_salaire - 75000) * 0.16;
                                    $categorie_salaire = 75000;
                                }

                                // Réduction selon nombre de parts
                                $coef = isset($value["part_igr"]) ? $value["part_igr"] : 1;
                                $reduction = 0;

                                switch (true) {
                                    case ($coef >= 5):
                                        $reduction = 44000;
                                        break;
                                    case ($coef == 4.5):
                                        $reduction = 38500;
                                        break;
                                    case ($coef == 4):
                                        $reduction = 33000;
                                        break;
                                    case ($coef == 3.5):
                                        $reduction = 27500;
                                        break;
                                    case ($coef == 3):
                                        $reduction = 22000;
                                        break;
                                    case ($coef == 2.5):
                                        $reduction = 16500;
                                        break;
                                    case ($coef == 2):
                                        $reduction = 11000;
                                        break;
                                    case ($coef == 1.5):
                                        $reduction = 5500;
                                        break;
                                    case ($coef == 1):
                                    default:
                                        $reduction = 0;
                                        break;
                                }

                                // ITS net
                                $impot_net = max($impot - $reduction, 0);
                                $its = round($impot_net, 2);

                                // Ici tu peux garder ton calcul ITS existant si nécessaire
                                // $its = $value['its']; // ou recalculer comme plus haut

                                // Salaire net
                                $net_salary = $total_brute - $cnps - $cmu - $its ;


                                // Totaux généraux
                                $totals['brute']  += $total_brute;
                                $totals['social'] += $total_social;
                                $totals['fiscal'] += $total_fiscal;
                                $totals['base']   += $value['categorie_salaire'];
                                $totals['cnps']   += $cnps;
                                $totals['cmu']    += $cmu_total;
                                $totals['its']    += $its;
                                $totals['revenu'] += $cnps + $cmu + $its; // revenu imposable
                                $totals['net']    += $net_salary;
                                ?>
                                <tr class="employee-row"
                                    data-matricule="<?php echo htmlspecialchars($value['employee_id']); ?>"
                                    data-nom="<?php echo htmlspecialchars($value['name'] . ' ' . $value['surname']); ?>">
                                    <td><?php echo $value['employee_id']; ?></td>
                                    <td><?php echo $value['name'] . " " . $value['surname']; ?></td>
                                    <td><?php echo number_format($total_brute, 0, ',', ' '); ?></td>
                                    <td><?php echo number_format($total_social, 0, ',', ' '); ?></td>
                                    <td><?php echo number_format($total_fiscal, 0, ',', ' '); ?></td>

                                    <td><?php echo number_format($value['categorie_salaire'], 0, ',', ' '); ?></td>
                                    <td><?php echo number_format($cnps, 0, ',', ' '); ?></td>
                                    <td><?php echo number_format($cmu, 0, ',', ' '); ?></td>
                                    <td><?php echo number_format($its, 0, ',', ' '); ?></td>
                                    <td><?php echo number_format($totals['revenu'], 0, ',', ' '); ?></td>
                                    <td><?php echo number_format($net_salary, 0, ',', ' '); ?></td>
                                </tr>
                                <?php
                            }
                            ?>

                            <!-- Ligne des totaux -->
                            <tr class="box box-solid total-bg">
                                <td colspan="2" class="text text-left text-primary"><strong>GRAND TOTAL</strong></td>
                                <td><strong><?php echo number_format($totals['brute'], 0, ',', ' '); ?></strong></td>
                                <td><strong><?php echo number_format($totals['social'], 0, ',', ' '); ?></strong></td>
                                <td><strong><?php echo number_format($totals['fiscal'], 0, ',', ' '); ?></strong></td>

                                <td><strong><?php echo number_format($totals['base'], 0, ',', ' '); ?></strong></td>
                                <td><strong><?php echo number_format($totals['cnps'], 0, ',', ' '); ?></strong></td>
                                <td><strong><?php echo number_format($totals['cmu'], 0, ',', ' '); ?></strong></td>
                                <td><strong><?php echo number_format($totals['its'], 0, ',', ' '); ?></strong></td>
                                <td><strong><?php echo number_format($totals['revenu'], 0, ',', ' '); ?></strong></td>
                                <td><strong><?php echo number_format($totals['net'], 0, ',', ' '); ?></strong></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Scripts pour l'exportation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    <?php if ($search_type == 'period') { ?>
    $(document).ready(function () {
        showdate('period');
    });
    <?php } ?>

    // Fonction de recherche par nom ou matricule
    function performSearch() {
        var searchTerm = document.getElementById('search-employee').value.toLowerCase().trim();

        if (searchTerm === '') {
            // Si le champ est vide, afficher toutes les lignes
            var rows = document.querySelectorAll('#table-body tr.employee-row');
            rows.forEach(function(row) {
                row.style.display = '';
            });
            return;
        }

        var rows = document.querySelectorAll('#table-body tr.employee-row');
        var found = false;

        rows.forEach(function(row) {
            var matricule = row.getAttribute('data-matricule').toLowerCase();
            var nom = row.getAttribute('data-nom').toLowerCase();

            // Recherche dans le matricule ou le nom
            if (matricule.includes(searchTerm) || nom.includes(searchTerm)) {
                row.style.display = '';
                found = true;
            } else {
                row.style.display = 'none';
            }
        });

        // Afficher un message si aucun résultat trouvé
        if (!found) {
            alert('Aucun employé trouvé pour "' + searchTerm + '"');
        }
    }

    // Fonction pour effacer la recherche
    function clearSearch() {
        document.getElementById('search-employee').value = '';
        performSearch();
    }

    // Permettre la recherche avec la touche Entrée
    document.getElementById('search-employee').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    // Fonction d'exportation PDF
    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        // Ajouter le logo - IMPORTANT: récupérer l'URL complète du logo
        const logoUrl = '<?= base_url() . "/uploads/school_content/admin_logo/" . $sch_setting->admin_logo ?>';

        // Titre avec logo
        doc.setFontSize(18);
        doc.setTextColor(44, 62, 80);
        doc.text('LIVRE DE PAIE', 105, 20, { align: 'center' });

        // Logo (positionné à gauche)
        if (logoUrl) {
            try {
                // Note: jsPDF nécessite une image valide, vous pouvez utiliser une URL dataURI
                // Pour une solution simple, on peut ajouter l'image via base64 si disponible
                // Sinon, on peut l'omettre si trop complexe
                doc.addImage(logoUrl, 'PNG', 14, 10, 30, 15);
            } catch(e) {
                console.log("Erreur lors du chargement du logo:", e);
            }
        }

        // Titre du document
        doc.setFontSize(16);
        //doc.text('LIVRE DE PAIE', 105, 15, { align: 'center' });

        // Période
        doc.setFontSize(10);
        doc.text('Période: <?php echo $this->customlib->get_postmessage(); ?>', 14, 25);

        // Date d'exportation
        const exportDate = new Date().toLocaleDateString('fr-FR');
        doc.text('Exporté le: ' + exportDate, 14, 32);

        // Préparation des données du tableau
        const table = document.getElementById('livre-paie-table');
        const headers = [];
        const rows = [];

        // Récupération des en-têtes
        table.querySelectorAll('thead th').forEach(th => {
            headers.push(th.textContent.trim());
        });

        // Récupération des données
        table.querySelectorAll('tbody tr').forEach(tr => {
            const row = [];
            tr.querySelectorAll('td').forEach(td => {
                row.push(td.textContent.trim());
            });
            if (row.length > 0) {
                rows.push(row);
            }
        });

        // Génération du tableau PDF
        doc.autoTable({
            head: [headers],
            body: rows,
            startY: 40,
            styles: { fontSize: 8 },
            headStyles: { fillColor: [66, 139, 202] }
        });

        // Sauvegarde du PDF
        doc.save('livre_paie_<?php echo date('Y-m-d'); ?>.pdf');
    }

    // Fonction d'exportation Excel
    function exportToExcel() {
        // Préparation des données
        const data = [];
        const headers = [];

        // Récupération des en-têtes
        document.querySelectorAll('#livre-paie-table thead th').forEach(th => {
            headers.push(th.textContent.trim());
        });
        data.push(headers);

        // Récupération des données
        document.querySelectorAll('#livre-paie-table tbody tr').forEach(tr => {
            const row = [];
            tr.querySelectorAll('td').forEach(td => {
                row.push(td.textContent.trim());
            });
            if (row.length > 0) {
                data.push(row);
            }
        });

        // Création du workbook
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Livre de Paie');

        // Style pour les en-têtes
        if (!ws['!cols']) ws['!cols'] = [];
        headers.forEach((_, i) => {
            ws['!cols'][i] = { width: 15 };
        });

        // Sauvegarde du fichier Excel
        XLSX.writeFile(wb, 'livre_paie_<?php echo date('Y-m-d'); ?>.xlsx');
    }

    // Alternative simple pour Excel (méthode tableau HTML)
    function exportToExcelSimple() {
        const table = document.getElementById('livre-paie-table');
        const html = table.outerHTML;

        // Création d'un blob et téléchargement
        const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'livre_paie_<?php echo date('Y-m-d'); ?>.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }
</script>

<style>
    .btn-group {
        margin-left: 10px;
    }
    .dropdown-menu li a {
        cursor: pointer;
    }
    .download_label {
        display: none; /* Masquer pour l'export */
    }
    @media print {
        .box-header, .btn-group {
            display: none !important;
        }
    }
    /* Style pour le champ de recherche */
    #search-employee {
        transition: all 0.3s ease;
    }
    #search-employee:focus {
        border-color: #66afe9;
        box-shadow: 0 0 8px rgba(102, 175, 233, 0.6);
    }
    .input-group-btn .btn {
        border-radius: 0 4px 4px 0;
    }
</style>