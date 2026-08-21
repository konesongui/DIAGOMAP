<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<style>
    /* Staff Profile Card */
    .staff-profile-card {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .staff-profile-card:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .staff-avatar {
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .staff-name {
        font-size: 22px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 15px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #3498db;
        display: inline-block;
    }

    .staff-details-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px 25px;
    }

    .detail-item {
        display: flex;
        align-items: baseline;
        gap: 10px;
        flex-wrap: wrap;
    }

    .detail-label {
        font-size: 12px;
        font-weight: 600;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #f0f2f5;
        padding: 3px 10px;
        border-radius: 4px;
    }

    .detail-value {
        font-size: 14px;
        font-weight: 500;
        color: #2c3e50;
    }

    /* Separator */
    .separator {
        background: linear-gradient(90deg, #e0e0e0 0%, #bdc3c7 50%, #e0e0e0 100%);
        height: 1px;
        width: 100%;
        margin: 20px 0 10px 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .staff-details-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .staff-name {
            font-size: 18px;
            margin-top: 15px;
            text-align: center;
        }

        .col-md-2 {
            text-align: center;
            margin-bottom: 15px;
        }

        .staff-profile-card {
            padding: 20px;
        }

        .detail-item {
            justify-content: space-between;
        }
    }
</style>
<div class="content-wrapper" style="min-height: 393px;">
    <section class="content-header">
        <h1><i class="fa fa-sitemap"></i> <?php echo $this->lang->line('human_resource'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h3 class="box-title">Détails du personnel</h3>
                            </div>
                            <div class="col-md-8 ">
                                <div class="btn-group pull-right">
                                    <a href="<?php echo base_url() ?>admin/payroll" type="button" class="btn btn-primary btn-xs">
                                        <i class="fa fa-arrow-left"></i> </a>
                                </div>
                            </div>
                        </div>
                    </div><!--./box-header-->
                    <div class="box-body" style="padding-top:0;">
                        <div class="row">
                            <!-- Section Profil Staff -->
                            <div class="col-md-12 col-sm-12">
                                <div class="staff-profile-card">
                                    <div class="row">
                                        <div class="col-md-2 text-center">
                                            <?php
                                            $image = $result['image'];
                                            if (!empty($image)) {
                                                $file = $result['image'];
                                            } else {
                                                $file = "no_image.png";
                                            }
                                            ?>
                                            <img width="115" height="115" class="staff-avatar" src="<?php echo base_url() . "uploads/staff_images/" . $file ?>" alt="Photo du staff">
                                        </div>

                                        <div class="col-md-10">
                                            <div class="staff-info">
                                                <h4 class="staff-name"><?php echo $result["name"] . " " . $result["surname"] ?></h4>
                                                <div class="staff-details-grid">
                                                    <div class="detail-item">
                                                        <span class="detail-label">Matricule</span>
                                                        <span class="detail-value"><?php echo $result["employee_id"] ?></span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Email</span>
                                                        <span class="detail-value"><?php echo $result["email"] ?></span>
                                                    </div>
                                                    <?php if ($sch_setting->staff_phone): ?>
                                                        <div class="detail-item">
                                                            <span class="detail-label">Téléphone</span>
                                                            <span class="detail-value"><?php echo $result["contact_no"] ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Rôle</span>
                                                        <span class="detail-value"><?php echo $result["user_type"] ?></span>
                                                    </div>
                                                    <?php if ($sch_setting->staff_epf_no): ?>
                                                        <div class="detail-item">
                                                            <span class="detail-label">Nb d'enfants</span>
                                                            <span class="detail-value"><?php echo $result["epf_no"] ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if ($sch_setting->staff_department): ?>
                                                        <div class="detail-item">
                                                            <span class="detail-label">Département</span>
                                                            <span class="detail-value"><?php echo $result["department"] ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if ($sch_setting->staff_designation): ?>
                                                        <div class="detail-item">
                                                            <span class="detail-label">Fonction</span>
                                                            <span class="detail-value"><?php echo $result["designation"] ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Séparateur -->
                            <div class="col-md-12">
                                <div class="separator"></div>
                            </div>
                        </div>
                    </div>


                    <!-- /.box-body -->
                    <form class="form-horizontal" action="<?php echo site_url('admin/payroll/payslip') ?>" method="post" id="employeeform">
                        <div class="box-header">
                            <div class="row display-flex">
                                <div class="col-md-4 col-sm-4">
                                    <div class="box-header-with-button">
                                        <h3 class="box-title"><?php echo $this->lang->line('payroll'); ?> <?php echo $this->lang->line('summary'); ?>(<?php echo $currency_symbol ?>)</h3>
                                        <button type="button" onclick="add_allowance()" class="btn btn-primary btn-sm calculator-btn">
                                            <i class="fa fa-calculator"></i> <?php echo $this->lang->line('calculate'); ?>
                                        </button>
                                    </div>

                                    <div class="sameheight">
                                        <div class="payrollbox feebox scrollable-form">
                                            <!-- Section 1: Informations de base -->
                                            <div class="form-section">

                                                <div class="form-group" hidden>
                                                    <h4 class="section-title">Salaire de Base</h4>
                                                    <label class="col-sm-4 control-label"><?php echo $this->lang->line('basic_salary'); ?></label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="basic" value="<?php echo !empty($result["salaire_base"]) ? $result["salaire_base"] : '0'; ?>" id="basic" type="text" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Catégorie salariale</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="categorie_salaire" value="<?php echo !empty($result["categorie_salaire"]) ? $result["categorie_salaire"] : '0'; ?>" id="salaire" type="text" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Catégorie lettre</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="categorie_lettre" value="<?php echo !empty($result["categorie_lettre"]) ? $result["categorie_lettre"] : '0'; ?>" id="salaire_lettre" type="text" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Sursalaire</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="sursalaire" value="<?php echo !empty($result["sursalaire"]) ? $result["sursalaire"] : '0'; ?>" id="sursalaire" type="text" />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Section 2: Primes principales -->
                                            <div class="form-section">
                                                <h4 class="section-title">Primes Principales</h4>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Prime Ancien</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="prime_anc" value="<?php echo number_format($prime_anciennete, 0, '', ' '); ?>" id="prime_anc" type="text" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Prime Imdémités</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="taxes" value="<?php echo !empty($result["taxes"]) ? $result["taxes"] : '0'; ?>" id="taxes" type="text" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Allocations</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="allocance" value="<?php echo !empty($result["allocance"]) ? $result["allocance"] : '0'; ?>" id="allocance" type="text" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label"><?php echo $this->lang->line('earning'); ?></label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" value="<?php echo !empty($result["total_allowance"]) ? $result["total_allowance"] : '0'; ?>" name="total_allowance" id="total_allowance" type="text" readonly />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Prime Transport</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="prime_trans" value="<?php echo !empty($result["prime_trans"]) ? $result["prime_trans"] : '0'; ?>" id="prime_trans" type="text" />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Section 3: Primes supplémentaires -->
                                            <div class="form-section">
                                                <h4 class="section-title">Primes Supplémentaires</h4>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Forfait Hs</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="forfait_hs" value="<?php echo !empty($result["forfait_hs"]) ? $result["forfait_hs"] : '0'; ?>" id="forfait_hs" type="text" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Prime Resp</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="prime_resp" value="<?php echo !empty($result["prime_resp"]) ? $result["prime_resp"] : '0'; ?>" id="prime_resp" type="text" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Prime Rend</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="prime_rend" value="<?php echo !empty($result["prime_rend"]) ? $result["prime_rend"] : '0'; ?>" id="prime_rend" type="text" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Prime Risque</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="prime_risque" value="<?php echo !empty($result["prime_risque"]) ? $result["prime_risque"] : '0'; ?>" id="prime_risque" type="text" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Prime Assi</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="prime_assi" value="<?php echo !empty($result["prime_assi"]) ? $result["prime_assi"] : '0'; ?>" id="prime_assi" type="text" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Prime Grati</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="prime_grati" value="<?php echo !empty($result["prime_grati"]) ? $result["prime_grati"] : '0'; ?>" id="prime_grati" type="text" />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Section 4: Autres revenus -->
                                            <div class="form-section">
                                                <h4 class="section-title">Autres Revenus</h4>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Congé</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="conge" value="<?php echo !empty($result["conge"]) ? $result["conge"] : '0'; ?>" id="conge" type="text" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">PRIME T</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="primet" value="<?php echo !empty($result["conge"]) ? $result["conge"] : '0'; ?>" id="prime" type="text" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Prime de fonction</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="autre_reve" id="autre_reve" value="<?php echo !empty($result["autre_reve"]) ? $result["autre_reve"] : '0'; ?>" type="text" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Bonus</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="bonus" id="bonus" value="<?php echo !empty($result["bonus"]) ? $result["bonus"] : '0'; ?>" type="text" />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Section 5: Cotisations sociales -->
                                            <div class="form-section">
                                                <h4 class="section-title">Cotisations Sociales</h4>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Part IGR</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="part_igr" value="<?php echo !empty($result["part_igr"]) ? $result["part_igr"] : '0'; ?>" id="part_igr" type="text" />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Cmu</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="cmu" id="cmu_s" value="0" type="text" readonly />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Nombre d'enfant</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="epf_no" id="epf_no" value="<?php echo !empty($result["epf_no"]) ? $result["epf_no"] : '0'; ?>" type="text" readonly />
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">CNPS, Regime Retraite</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="cnps_regim" id="etraite" value="0" type="text" readonly />
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">ITS</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="its" id="its_igr" value="0" type="text" readonly />
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Prestations Familiales</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="prest_famille" id="prest_famille" value="0" type="text" readonly />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Taxe Apprentissage</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="taxe_apprend" id="taxe_apprend" value="0" type="text" readonly />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Section 6: Totaux et résultats -->
                                            <div class="form-section results-section">
                                                <h4 class="section-title">Résultats du Calcul</h4>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Salaire Brut</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="gross_salary" id="gross_salary" type="text" readonly />
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Total Fiscal</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="total_fiscal" id="total_fiscal" type="text" readonly />
                                                    </div>
                                                </div>


                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Prime ancien</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="prime_ancien" id="prime_anciennete" type="text" readonly />
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Base Sociale</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="gross_social" id="total_social" value="0" type="text" readonly />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">Total Revenu</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="total_revenu" id="total_revenu" value="0" type="text" readonly />
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label">CNPS</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" name="total" id="cnps" value="0" type="text" readonly />
                                                        <p id="result" class="tax-result"></p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-4 control-label"><?php echo $this->lang->line('tax'); ?></label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control deduction-input" name="tax" id="tax" value="0" type="text" readonly />
                                                    </div>
                                                </div>
                                                <div class="form-group net-salary-group">
                                                    <label class="col-sm-4 control-label"><?php echo $this->lang->line('net_salary'); ?></label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control net-salary-input" name="net_salary" id="net_salary" type="text" readonly />
                                                        <span class="text-danger" id="err"><?php echo form_error('net_salary'); ?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Champs cachés -->
                                            <input type="hidden" name="staff_id" value="<?php echo $result["id"]; ?>" />
                                            <input type="hidden" name="month" value="<?php echo $month; ?>" />
                                            <input type="hidden" name="year" value="<?php echo $year; ?>" />
                                            <input type="hidden" name="status" value="generated" />
                                        </div>
                                    </div>
                                </div><!--./col-md-4-->

                                <div class="col-md-12 col-sm-12">
                                    <button type="submit" id="contact_submit" class="btn btn-success btn-lg pull-right">
                                        <i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?>
                                    </button>
                                </div><!--./col-md-12-->
                            </div>
                        </div>
                    </form>

                    <script type="text/javascript">
                        function add_allowance() {
                            // Définition du plafond CNPS (45 * 75000)
                            var plafond_cnps = 45 * 75000; // 3 375 000 FCFA

                            // Récupération des valeurs
                            var categorie_salaire = parseFloat($("#salaire").val()) || 0;
                            var sursalaire = parseFloat($("#sursalaire").val()) || 0;
                            var prime_trans = parseFloat($("#prime_trans").val()) || 0;
                            var autre_reve = parseFloat($("#autre_reve").val()) || 0;
                            var forfait_hs = parseFloat($("#forfait_hs").val()) || 0;
                            var prime_resp = parseFloat($("#prime_resp").val()) || 0;
                            var prime_rend = parseFloat($("#prime_rend").val()) || 0;
                            var prime_risque = parseFloat($("#prime_risque").val()) || 0;
                            var prime_assi = parseFloat($("#prime_assi").val()) || 0;
                            var prime_grati = parseFloat($("#prime_grati").val()) || 0;
                            var conge = parseFloat($("#conge").val()) || 0;
                            var part_igr = parseFloat($("#part_igr").val()) || 1;
                            var epf_no = parseFloat($("#epf_no").val()) || 1;

                            // === CALCUL DE LA PRIME D'ANCIENNETÉ ===
                            var prime_anciennete = 0;
                            var date_embauche_str = "<?php echo $result['date_of_joining']; ?>";

                            if (date_embauche_str) {
                                var date_embauche = new Date(date_embauche_str);
                                var today = new Date();
                                var anciennete = today.getFullYear() - date_embauche.getFullYear();
                                if (today.getMonth() < date_embauche.getMonth() ||
                                    (today.getMonth() === date_embauche.getMonth() && today.getDate() < date_embauche.getDate())) {
                                    anciennete--;
                                }

                                var taux_prime = 0;
                                if (anciennete >= 3 && anciennete <= 5) taux_prime = 0.05;
                                else if (anciennete >= 6 && anciennete <= 10) taux_prime = 0.10;
                                else if (anciennete >= 11 && anciennete <= 15) taux_prime = 0.15;
                                else if (anciennete > 15) taux_prime = 0.20;

                                prime_anciennete = categorie_salaire * taux_prime;
                            }

                            // === CALCUL DU SALAIRE BRUT TOTAL ===
                            var total_brute = categorie_salaire + sursalaire + prime_anciennete + prime_trans +
                                autre_reve + forfait_hs + prime_resp + prime_rend +
                                prime_risque + prime_assi + prime_grati + conge;

                            // === PLAFONNEMENT ===
                            var total_po = total_brute * 0.1;
                            var primet = 30000;

                            var final_trans = (prime_trans > primet) ? (prime_trans - primet) : 0;
                            var final_anc = (prime_anciennete > total_po) ? (prime_anciennete - total_po) : 0;
                            var final_rend = (prime_rend > total_po) ? (prime_rend - total_po) : 0;
                            var final_resp = (prime_resp > total_po) ? (prime_resp - total_po) : 0;
                            var final_risq = (prime_risque > total_po) ? (prime_risque - total_po) : 0;
                            var final_autres = (autre_reve > total_po) ? (autre_reve - total_po) : 0;
                            var final_assi = (prime_assi > total_po) ? (prime_assi - total_po) : 0;

                            // === BASE FISCALE ===
                            var total_fiscal = categorie_salaire + final_trans + final_anc + final_rend +
                                final_risq + final_resp + final_assi + sursalaire + final_autres;

                            // === BASE SOCIALE ===
                            var total_social = total_brute - prime_trans;

                            // === CALCUL ITS IDENTIQUE AU PHP ===
                            var impot = 0;
                            var salaire_imposable = total_fiscal; // IMPORTANT: utiliser total_fiscal comme base

                            // Barème progressif IDENTIQUE au PHP
                            if (salaire_imposable > 8000000) {
                                impot += (salaire_imposable - 8000000) * 0.32;
                                salaire_imposable = 8000000;
                            }

                            if (salaire_imposable > 2400000) {
                                impot += (salaire_imposable - 2400000) * 0.28;
                                salaire_imposable = 2400000;
                            }

                            if (salaire_imposable > 800000) {
                                impot += (salaire_imposable - 800000) * 0.24;
                                salaire_imposable = 800000;
                            }

                            if (salaire_imposable > 240000) {
                                impot += (salaire_imposable - 240000) * 0.21;
                                salaire_imposable = 240000;
                            }

                            if (salaire_imposable > 75000) {
                                impot += (salaire_imposable - 75000) * 0.16;
                                salaire_imposable = 75000;
                            }

                            // Réduction selon nombre de parts IDENTIQUE au PHP
                            var reduction = 0;

                            // Réduction plafonnée à 5 parts - IDENTIQUE au PHP
                            if (part_igr >= 5) {
                                reduction = 44000;
                            } else if (part_igr == 4.5) {
                                reduction = 38500;
                            } else if (part_igr == 4) {
                                reduction = 33000;
                            } else if (part_igr == 3.5) {
                                reduction = 27500;
                            } else if (part_igr == 3) {
                                reduction = 22000;
                            } else if (part_igr == 2.5) {
                                reduction = 16500;
                            } else if (part_igr == 2) {
                                reduction = 11000;
                            } else if (part_igr == 1.5) {
                                reduction = 5500;
                            } else {
                                reduction = 0;
                            }

                            // Calcul de l'ITS (impôt net) IDENTIQUE au PHP
                            var impot_net = Math.max(impot - reduction, 0);
                            var its = Math.round(impot_net); // Arrondi comme le PHP

                            // === COTISATIONS SOCIALES ===

                            // CNPS Retraite (plafonné)
                            var cnps_retraite_base = (total_social < plafond_cnps) ? total_social : plafond_cnps;
                            var retrai_regime = cnps_retraite_base * 0.063;

                            // CMU
                            var cmu_unit = 500;
                            var cmu_total = epf_no * cmu_unit;

                            // CNPS Accident (3% patronal)
                            var travail = categorie_salaire * 0.03;

                            // Prestations familiales (5.75% patronal)
                            var famille = categorie_salaire * 0.0575;

                            // FDFP Taxe apprentissage (0.04% patronal)
                            var taxe = total_fiscal * 0.004;

                            // FDFP Formation (0.06% patronal)
                            var fdfp_formation = total_fiscal * 0.006;

                            // ITS Patronal (1.2%)
                            var its_patronal = total_fiscal * 0.012;

                            // CNPS Retraite patronal (7.7%)
                            var retrait = cnps_retraite_base * 0.077;

                            // === CALCUL DES TOTAUX ===

                            // Total retenues salariales
                            var tota_retenus = retrai_regime + its + cmu_total;
                            var total_brute = total_brute;
                            var total_fiscal = total_fiscal;

                            // Total retenues patronales
                            var tota_retenues = its_patronal + retrait + cmu_total + travail + famille + taxe + fdfp_formation;

                            // === SALAIRE NET ===
                            var salaire_net = total_brute - tota_retenus;

                            // === MISE À JOUR DE L'INTERFACE ===
                            $("#gross_salary").val(total_brute.toFixed(0));
                            $("#total_social").val(total_social.toFixed(0));
                            $("#total_fiscal").val(total_fiscal.toFixed(0));
                            $("#total_revenu").val(tota_retenus.toFixed(0));
                            $("#tax").val(its.toFixed(0));
                            $("#cnps").val(retrai_regime.toFixed(0));
                            $("#cmu_s").val(cmu_total.toFixed(0));
                            $("#etraite").val(retrai_regime.toFixed(0));
                            $("#prest_famille").val(famille.toFixed(0));
                            $("#taxe_apprend").val(taxe.toFixed(0));
                            $("#fdfp_form").val(fdfp_formation.toFixed(0));
                            $("#prime_anciennete").val(prime_anciennete.toFixed(0));
                            $("#net_salary").val(salaire_net.toFixed(0));

                            // Affichage détaillé du calcul ITS
                            $("#result").html("Base ITS: " + total_fiscal.toFixed(0) + "<br>" +
                                "Impôt avant réduction: " + impot.toFixed(0) + "<br>" +
                                "Réduction (" + part_igr + " parts): " + reduction.toFixed(0) + "<br>" +
                                "ITS final: " + its.toFixed(0));

                            console.log("=== CALCUL ITS DÉTAILLÉ ===");
                            console.log("Base fiscale pour ITS:", total_fiscal.toFixed(0));
                            console.log("Impôt avant réduction:", impot.toFixed(0));
                            console.log("Parts fiscales:", part_igr);
                            console.log("Réduction:", reduction.toFixed(0));
                            console.log("ITS final:", its.toFixed(0));
                            console.log("Salaire brut:", total_brute.toFixed(0));
                            console.log("Retenues salariales:", tota_retenus.toFixed(0));
                            console.log("Salaire net:", salaire_net.toFixed(0));
                        }
                    </script>
                    <style>
                        /* Styles pour l'amélioration visuelle */
                        .box-header-with-button {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            margin-bottom: 15px;
                        }

                        .calculator-btn {
                            margin-left: 10px;
                        }

                        .scrollable-form {
                            max-height: 600px;
                            overflow-y: auto;
                            padding-right: 10px;
                        }

                        .form-section {
                            margin-bottom: 20px;
                            padding: 15px;
                            background: #f9f9f9;
                            border-radius: 8px;
                            border-left: 4px solid #3c8dbc;
                        }

                        .section-title {
                            color: #3c8dbc;
                            font-weight: bold;
                            margin-bottom: 15px;
                            padding-bottom: 5px;
                            border-bottom: 2px solid #eee;
                        }

                        .results-section {
                            background: #e8f5e8;
                            border-left-color: #4caf50;
                        }

                        .results-section .section-title {
                            color: #4caf50;
                        }

                        .net-salary-group {
                            background: #dff0d8;
                            padding: 15px;
                            border-radius: 6px;
                            margin-top: 20px;
                        }

                        .net-salary-input {
                            font-weight: bold;
                            font-size: 18px;
                            color: #3c763d;
                            background: #fff;
                        }

                        .deduction-input {
                            color: #d9534f;
                            font-weight: bold;
                        }

                        .tax-result {
                            margin: 5px 0 0 0;
                            font-style: italic;
                            color: #666;
                        }

                        .form-group {
                            margin-bottom: 12px;
                            padding: 8px;
                            background: white;
                            border-radius: 4px;
                            border: 1px solid #eee;
                        }

                        .form-group:hover {
                            background: #f8f9fa;
                            border-color: #ddd;
                        }

                        .control-label {
                            font-weight: 600;
                            color: #555;
                        }

                        /* Scrollbar personnalisée */
                        .scrollable-form::-webkit-scrollbar {
                            width: 8px;
                        }

                        .scrollable-form::-webkit-scrollbar-track {
                            background: #f1f1f1;
                            border-radius: 4px;
                        }

                        .scrollable-form::-webkit-scrollbar-thumb {
                            background: #c1c1c1;
                            border-radius: 4px;
                        }

                        .scrollable-form::-webkit-scrollbar-thumb:hover {
                            background: #a8a8a8;
                        }

                        /* Responsive */
                        @media (max-width: 768px) {
                            .scrollable-form {
                                max-height: none;
                                overflow-y: visible;
                            }

                            .form-section {
                                padding: 10px;
                            }

                            .box-header-with-button {
                                flex-direction: column;
                                align-items: flex-start;
                            }

                            .calculator-btn {
                                margin: 10px 0 0 0;
                            }
                        }
                    </style>

