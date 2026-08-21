<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style type="text/css">
    /* ============ STYLES EXISTANTS (inchangés) ============ */
    .borderwhite{border-top-color:#fff!important;}
    .box-header>.box-tools{display:none;}
    .sidebar-collapse #barChart,.sidebar-collapse #lineChart{height:100%!important;}
    .dashboard-card{border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);border:none;margin-bottom:20px;transition:transform .3s ease,box-shadow .3s ease;}
    .dashboard-card:hover{transform:translateY(-5px);box-shadow:0 8px 25px rgba(0,0,0,0.15);}
    .card-header{background:linear-gradient(135deg,#fff,#fff);color:#000;border-radius:12px 12px 0 0!important;border:none;padding:15px 20px;display:flex;justify-content:space-between;align-items:center;}
    .card-header h3{margin:0;font-weight:600;font-size:16px;}
    .stat-card{background:#fff;border-radius:10px;padding:20px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,0.08);transition:all .3s ease;}
    .stat-card:hover{transform:translateY(-3px);box-shadow:0 5px 15px rgba(0,0,0,0.1);}
    .stat-icon{width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 15px;font-size:24px;}
    .stat-number{font-size:23px;font-weight:700;margin:10px 0;background:linear-gradient(135deg,#000,#000);-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
    .stat-text{color:#6c757d;font-weight:500;font-size:14px;}
    .progress-modern{height:8px;border-radius:10px;background:#f0f0f0;overflow:hidden;}
    .progress-bar-modern{border-radius:10px;}
    .chart-container{position:relative;height:300px;padding:20px;}
    .info-box{min-height:90px;background:#fff;border-radius:8px;margin-bottom:15px;box-shadow:0 2px 8px rgba(0,0,0,0.1);transition:all .3s ease;}
    .info-box:hover{transform:translateY(-3px);box-shadow:0 5px 15px rgba(0,0,0,0.15);}
    .info-box-icon{border-radius:8px 0 0 8px;display:flex;align-items:center;justify-content:center;font-size:28px;width:70px;}
    .info-box-content{padding:15px;}
    .info-box-text{font-weight:600;font-size:12px;color:#333;}
    .info-box-number{font-size:20px;font-weight:700;color:#3B82F6;}
    .date-range-group{display:inline-flex;gap:10px;align-items:center;margin-right:15px;}
    .date-range-group input{padding:6px 12px;border-radius:20px;border:1px solid #ddd;font-size:14px;}
    .btn-export{margin-left:1px;border-radius:20px;}
    .badge-rh{padding:5px 10px;border-radius:20px;font-size:12px;font-weight:600;color:#fff;text-transform:uppercase;letter-spacing:0.5px;}
    .table-hover tbody tr:hover{transform:translateX(5px);transition:transform .3s ease;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
    .pyramid-container{height:350px;}
    /* Couleurs pour icônes */
    .icon-blue{color:#2196F3;}
    .icon-green{color:#4CAF50;}
    .icon-red{color:#F44336;}
    .icon-orange{color:#FF9800;}
    .icon-purple{color:#9C27B0;}
    .icon-teal{color:#009688;}
</style>

<?php
// ==================== CONNEXION ET CONFIGURATION ====================
$CI = &get_instance();
$conn = new mysqli($CI->db->hostname, $CI->db->username, $CI->db->password, $CI->db->database);
if ($conn->connect_error) die("Erreur de connexion: " . $conn->connect_error);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==================== FILTRE PÉRIODE (financier) ====================
$date_debut = $_GET['date_debut'] ?? null;
$date_fin   = $_GET['date_fin'] ?? null;
if (!$date_debut && !$date_fin) { $date_debut = date('Y-m-01'); $date_fin = date('Y-m-t'); }
if ($date_debut && $date_fin && strtotime($date_debut) > strtotime($date_fin)) { $tmp = $date_debut; $date_debut = $date_fin; $date_fin = $tmp; }
if ($date_debut && $date_fin) { $date_condition = "date BETWEEN '$date_debut' AND '$date_fin'"; $period_label = "Du ".date('d/m/Y',strtotime($date_debut))." au ".date('d/m/Y',strtotime($date_fin)); }
elseif ($date_debut) { $date_condition = "date >= '$date_debut'"; $period_label = "À partir du ".date('d/m/Y',strtotime($date_debut)); }
elseif ($date_fin) { $date_condition = "date <= '$date_fin'"; $period_label = "Jusqu'au ".date('d/m/Y',strtotime($date_fin)); }
else { $date_condition = "1=1"; $period_label = "Toutes périodes"; }

// ==================== DONNÉES FINANCIÈRES (utiles pour l'export Excel) ====================
$sql_income = "SELECT COALESCE(SUM(amount),0) as total FROM income WHERE $date_condition AND deleted=1 AND est_actif=1";
$revenus_creation = $conn->query($sql_income)->fetch_object()->total ?? 0;
$sql_reappro = "SELECT COALESCE(SUM(amount),0) as total FROM income_processing WHERE $date_condition AND deleted=1";
$revenus_reappro = $conn->query($sql_reappro)->fetch_object()->total ?? 0;
$sql_entrees = "SELECT COALESCE(SUM(montant),0) as total FROM operation_caisse WHERE $date_condition AND (est_actif=1 OR est_actif IS NULL) AND deleted=0 AND type_operation='ENTREE'";
$revenus_ops = $conn->query($sql_entrees)->fetch_object()->total ?? 0;
$montant_revenus = $revenus_ops;
$check_col = $conn->query("SHOW COLUMNS FROM operation_caisse LIKE 'est_active'");
$use_est_active = ($check_col && $check_col->num_rows > 0);
if ($use_est_active) $sql_dep = "SELECT COALESCE(SUM(montant),0) as total FROM operation_caisse WHERE $date_condition AND (est_active=1 OR est_active IS NULL) AND deleted=0 AND type_operation='SORTIE'";
else $sql_dep = "SELECT COALESCE(SUM(montant),0) as total FROM operation_caisse WHERE $date_condition AND est_actif=1 AND deleted=0 AND type_operation='SORTIE'";
$total_expenses = $conn->query($sql_dep)->fetch_object()->total ?? 0;
$check_exp = $conn->query("SHOW TABLES LIKE 'expenses'");
if ($check_exp && $check_exp->num_rows > 0) {
    $sql_old = "SELECT COALESCE(SUM(amount),0) as total FROM expenses WHERE $date_condition AND deleted=0";
    $total_expenses += $conn->query($sql_old)->fetch_object()->total ?? 0;
}
$solde_actuel = $conn->query("SELECT COALESCE(SUM(amount_re),0) as total FROM income WHERE deleted=1 AND est_actif=1")->fetch_object()->total ?? 0;
$sql_trans = "SELECT COUNT(*) as total FROM (SELECT id FROM operation_caisse WHERE $date_condition AND (est_active=1 OR est_actif=1 OR est_active IS NULL) AND deleted=0 UNION ALL SELECT id FROM income WHERE $date_condition AND est_actif=1 AND deleted=1 UNION ALL SELECT income_id FROM income_processing WHERE $date_condition AND deleted=1) t";
$nb_trans = $conn->query($sql_trans)->fetch_object()->total ?? 0;

// Dépenses par catégorie (plus utilisé dans l'affichage mais conservé pour l'export)
$sql_cat = "SELECT COALESCE(eh.exp_category, oc.category, 'Non catégorisé') as cat, SUM(oc.montant) as total FROM operation_caisse oc LEFT JOIN expense_head eh ON oc.exp_head_id = eh.id WHERE $date_condition AND ".($use_est_active?"(oc.est_active=1 OR oc.est_active IS NULL)":"oc.est_actif=1")." AND oc.deleted=0 AND oc.type_operation='SORTIE' GROUP BY cat ORDER BY total DESC LIMIT 10";
$res_cat = $conn->query($sql_cat);
$labels_cat = []; $data_cat = [];
while ($row = $res_cat->fetch_assoc()) { $labels_cat[] = $row['cat']; $data_cat[] = floatval($row['total']); }
if (empty($labels_cat)) { $labels_cat = ['Aucune dépense']; $data_cat = [0]; }

// ==================== DONNÉES RH ====================
// Récupérer les employés actifs
$employes = $conn->query("SELECT id, name, gender, dob, date_of_joining, department, designation, contract_type, nationalite, categorie_salaire, is_active FROM staff WHERE is_active=1 AND deleted=1 AND name != 'Super Admin'");
$total_employes = $employes->num_rows;

// Âge médian et ancienneté
$ages = []; $anciennetes = [];
while($e = $employes->fetch_assoc()) {
    if($e['dob']) $ages[] = date_diff(date_create($e['dob']), date_create('today'))->y;
    if($e['date_of_joining']) $anciennetes[] = date_diff(date_create($e['date_of_joining']), date_create('today'))->y;
}
sort($ages); $mediane_age = count($ages) ? (count($ages)%2 ? $ages[floor(count($ages)/2)] : ($ages[count($ages)/2-1]+$ages[count($ages)/2])/2) : 0;
$anciennete_moyenne = count($anciennetes) ? round(array_sum($anciennetes)/count($anciennetes),1) : 0;

// Turnover : sorties par mois
$turnover_mois = array_fill(0,12,0);
$sql_turn = "SELECT MONTH(date) as m, COUNT(*) as c FROM enquiry WHERE YEAR(date)=YEAR(CURDATE()) AND (reference='demission' OR reference LIKE '%demission%') GROUP BY MONTH(date)";
$res_turn = $conn->query($sql_turn);
if($res_turn) while($row=$res_turn->fetch_assoc()) $turnover_mois[$row['m']-1] = $row['c'];
$sql_leave = "SELECT MONTH(date_of_leaving) as m, COUNT(*) as c FROM staff WHERE YEAR(date_of_leaving)=YEAR(CURDATE()) AND date_of_leaving IS NOT NULL AND date_of_leaving != '0000-00-00' GROUP BY MONTH(date_of_leaving)";
$res_leave = $conn->query($sql_leave);
if($res_leave) while($row=$res_leave->fetch_assoc()) $turnover_mois[$row['m']-1] += $row['c'];

// Pyramide des âges
$tranches = ['18-24','25-29','30-34','35-39','40-44','45-49','50-54','55-59','60+'];
$pyramide = array_fill(0, count($tranches), ['H'=>0,'F'=>0]);
$employes_age = $conn->query("SELECT gender, dob FROM staff WHERE is_active=1 AND deleted=1 AND name!='Super Admin' AND dob IS NOT NULL");
while($e = $employes_age->fetch_assoc()) {
    $age = date_diff(date_create($e['dob']), date_create('today'))->y;
    $idx = $age>=60 ? 8 : ($age>=55 ? 7 : ($age>=50 ? 6 : ($age>=45 ? 5 : ($age>=40 ? 4 : ($age>=35 ? 3 : ($age>=30 ? 2 : ($age>=25 ? 1 : 0)))))));
    if($e['gender']=='Male') $pyramide[$idx]['H']++;
    else $pyramide[$idx]['F']++;
}
$pyramid_labels = $tranches;
$pyramid_hommes = array_column($pyramide,'H');
$pyramid_femmes = array_column($pyramide,'F');

// Répartition par contrat
$contrat_data = ['CDI'=>0,'CDD'=>0,'Stage'=>0,'Intérim'=>0,'Autre'=>0];
$sql_contrat = "SELECT contract_type, COUNT(*) as nb FROM staff WHERE is_active=1 AND deleted=1 AND name!='Super Admin' GROUP BY contract_type";
$res_contrat = $conn->query($sql_contrat);
while($row=$res_contrat->fetch_assoc()) {
    $ct = strtolower($row['contract_type']);
    if($ct=='cdi') $contrat_data['CDI'] = $row['nb'];
    elseif($ct=='cdd') $contrat_data['CDD'] = $row['nb'];
    elseif($ct=='stage') $contrat_data['Stage'] = $row['nb'];
    elseif($ct=='interim') $contrat_data['Intérim'] = $row['nb'];
    else $contrat_data['Autre'] += $row['nb'];
}
$contrat_labels = array_keys($contrat_data);
$contrat_values = array_values($contrat_data);

// Répartition par nationalité
$nationalites = [];
$sql_nat = "SELECT nationalite, COUNT(*) as nb FROM staff WHERE is_active=1 AND deleted=1 AND name!='Super Admin' AND nationalite IS NOT NULL AND nationalite!='' GROUP BY nationalite ORDER BY nb DESC";
$res_nat = $conn->query($sql_nat);
while($row=$res_nat->fetch_assoc()) $nationalites[$row['nationalite']] = $row['nb'];

// Répartition par catégorie professionnelle
$cat_pro = [];
$sql_cp = "SELECT categorie_salaire, COUNT(*) as nb FROM staff WHERE is_active=1 AND deleted=1 AND name!='Super Admin' AND categorie_salaire IS NOT NULL AND categorie_salaire!='' GROUP BY categorie_salaire";
$res_cp = $conn->query($sql_cp);
while($row=$res_cp->fetch_assoc()) $cat_pro[$row['categorie_salaire']] = $row['nb'];
if(empty($cat_pro)) {
    $sql_des = "SELECT sd.designation, COUNT(*) as nb FROM staff s LEFT JOIN staff_designation sd ON s.designation=sd.id WHERE s.is_active=1 AND s.deleted=1 AND s.name!='Super Admin' GROUP BY sd.designation";
    $res_des = $conn->query($sql_des);
    while($row=$res_des->fetch_assoc()) if($row['designation']) $cat_pro[$row['designation']] = $row['nb'];
}
$catpro_labels = array_keys($cat_pro);
$catpro_values = array_values($cat_pro);

// Masse salariale nette (dernier bulletin)
$sql_last = "SELECT sp.staff_id, sp.net_salary FROM staff_payslip sp INNER JOIN (SELECT staff_id, MAX(STR_TO_DATE(CONCAT(year,'-',month,'-01'),'%Y-%M-%d')) as maxd FROM staff_payslip GROUP BY staff_id) l ON sp.staff_id=l.staff_id AND STR_TO_DATE(CONCAT(sp.year,'-',sp.month,'-01'),'%Y-%M-%d')=l.maxd WHERE sp.status='paid'";
$res_last = $conn->query($sql_last);
$masse_salariale = 0; $nb_salaires = 0;
while($row=$res_last->fetch_assoc()) { $masse_salariale += $row['net_salary']; $nb_salaires++; }
$salaire_net_moyen = $nb_salaires ? round($masse_salariale/$nb_salaires) : 0;

// Salaire moyen par département
$dept_names = []; $dept_sal_moy = [];
$sql_dept = "SELECT d.department_name, AVG(sp.net_salary) as sal_moy FROM staff s LEFT JOIN department d ON s.department=d.id INNER JOIN (SELECT staff_id, net_salary FROM staff_payslip sp1 WHERE sp1.status='paid' AND STR_TO_DATE(CONCAT(sp1.year,'-',sp1.month,'-01'),'%Y-%M-%d')=(SELECT MAX(STR_TO_DATE(CONCAT(sp2.year,'-',sp2.month,'-01'),'%Y-%M-%d')) FROM staff_payslip sp2 WHERE sp2.staff_id=sp1.staff_id)) sp ON s.id=sp.staff_id WHERE s.is_active=1 AND s.deleted=1 GROUP BY d.department_name";
$res_dept = $conn->query($sql_dept);
while($row=$res_dept->fetch_assoc()) { $dept_names[]=$row['department_name']??'Non défini'; $dept_sal_moy[]=round($row['sal_moy']); }

// Évolution salaire net moyen (12 mois)
$evol_periodes = []; $evol_salaires = [];
$sql_evol = "SELECT CONCAT(month,' ',year) as periode, AVG(net_salary) as sal_moy FROM staff_payslip WHERE status='paid' AND STR_TO_DATE(CONCAT(year,'-',month,'-01'),'%Y-%M-%d') >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) GROUP BY year, month ORDER BY STR_TO_DATE(CONCAT(year,'-',month,'-01'),'%Y-%M-%d')";
$res_evol = $conn->query($sql_evol);
while($row=$res_evol->fetch_assoc()) { $evol_periodes[]=$row['periode']; $evol_salaires[]=round($row['sal_moy']); }

// ==================== DONNÉES CONGÉS / ABSENCES / ARRÊTS ====================
$selected_year = date('Y', strtotime($date_fin));
$mois_labels = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'];
$conges_data = array_fill(0,12,0);
$absences_data = array_fill(0,12,0);
$arrets_data = array_fill(0,12,0);

$sql_conges = "SELECT MONTH(leave_from) as m, COUNT(*) as c FROM staff_leave_request WHERE YEAR(leave_from) = $selected_year GROUP BY MONTH(leave_from)";
$res_conges = $conn->query($sql_conges);
if($res_conges) while($row=$res_conges->fetch_assoc()) { $idx=$row['m']-1; if($idx>=0 && $idx<12) $conges_data[$idx]=$row['c']; }

$sql_abs = "SELECT MONTH(date) as m, COUNT(*) as c FROM enquiry WHERE YEAR(date)=$selected_year AND (reference='Permission' OR source LIKE '%maladie%' OR source LIKE '%voyage%') GROUP BY MONTH(date)";
$res_abs = $conn->query($sql_abs);
if($res_abs) while($row=$res_abs->fetch_assoc()) { $idx=$row['m']-1; if($idx>=0 && $idx<12) $absences_data[$idx]=$row['c']; }
$sql_att_abs = "SELECT MONTH(date) as m, COUNT(*) as c FROM staff_attendance WHERE YEAR(date)=$selected_year AND staff_attendance_type_id=4 AND is_active=0 GROUP BY MONTH(date)";
$res_att_abs = $conn->query($sql_att_abs);
if($res_att_abs) while($row=$res_att_abs->fetch_assoc()) { $idx=$row['m']-1; if($idx>=0 && $idx<12) $absences_data[$idx]+=$row['c']; }

$sql_arr = "SELECT MONTH(date) as m, COUNT(*) as c FROM enquiry WHERE YEAR(date)=$selected_year AND (reference='demission' OR reference LIKE '%demission%') GROUP BY MONTH(date)";
$res_arr = $conn->query($sql_arr);
if($res_arr) while($row=$res_arr->fetch_assoc()) { $idx=$row['m']-1; if($idx>=0 && $idx<12) $arrets_data[$idx]=$row['c']; }

$conn->close();
?>

<div class="content-wrapper">
    <section class="content">
        <!-- Filtre période -->
        <div class="row mb-3">
            <?php if ($this->rbac->hasPrivilege('fees_collection_and_expense_monthly_chart', 'can_view')) : ?>
                <div class="col-md-12">
                    <div class="dashboard-card" style="padding:15px;">
                        <div class="row align-items-center">
                            <div class="col-md-6"><h4><i class="fa fa-calendar icon-blue"></i> Période : <strong><?php echo $period_label; ?></strong></h4></div>
                            <div class="col-md-6">
                                <form method="GET" class="form-inline float-right">
                                    <div class="date-range-group">
                                        <input type="date" name="date_debut" class="form-control" value="<?php echo htmlspecialchars($date_debut); ?>">
                                        <span>à</span>
                                        <input type="date" name="date_fin" class="form-control" value="<?php echo htmlspecialchars($date_fin); ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-export"><i class="fa fa-filter"></i> Filtrer</button>
                                    <button type="button" id="exportExcelBtn" class="btn btn-success btn-export"><i class="fa fa-file-excel-o"></i> Excel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Indicateurs RH clés -->
        <div class="row mt-4">
            <?php if ($this->rbac->hasPrivilege('staff_present_today_widegts', 'can_view')) : ?>
                <div class="col-lg-3"><div class="stat-card"><div class="stat-icon" style="background:#E8EAF6;"><i class="fa fa-users icon-purple"></i></div><div class="stat-number"><?php echo $total_employes; ?></div><div class="stat-text">Effectif total</div></div></div>
                <div class="col-lg-3"><div class="stat-card"><div class="stat-icon" style="background:#E0F7FA;"><i class="fa fa-calendar icon-teal"></i></div><div class="stat-number"><?php echo $mediane_age; ?> ans</div><div class="stat-text">Âge médian</div></div></div>
                <div class="col-lg-3"><div class="stat-card"><div class="stat-icon" style="background:#FFF8E1;"><i class="fa fa-clock-o icon-orange"></i></div><div class="stat-number"><?php echo $anciennete_moyenne; ?> ans</div><div class="stat-text">Ancienneté moyenne</div></div></div>
                <div class="col-lg-3"><div class="stat-card"><div class="stat-icon" style="background:#E8F5E9;"><i class="fa fa-money icon-green"></i></div><div class="stat-number"><?php echo number_format($salaire_net_moyen,0,',',' '); ?> FCFA</div><div class="stat-text">Salaire net moyen</div></div></div>
            <?php endif; ?>
        </div><br>

        <!-- Pyramide des âges -->
        <div class="row mt-4">
            <?php if ($this->rbac->hasPrivilege('staff_present_today_widegts', 'can_view')) : ?>
                <div class="col-lg-12">
                    <div class="dashboard-card">
                        <div class="card-header"><h3><i class="fa fa-bar-chart icon-blue"></i> Pyramide des âges (effectifs par tranche)</h3></div>
                        <div class="chart-container pyramid-container">
                            <canvas id="pyramidChart"></canvas>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div><br>

        <!-- Turnover mensuel et évolution salaire -->
        <div class="row mt-4">
            <?php if ($this->rbac->hasPrivilege('staff_present_today_widegts', 'can_view')) : ?>
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <div class="card-header"><h3><i class="fa fa-sign-out icon-red"></i> Turnover mensuel (sorties) - <?php echo date('Y'); ?></h3></div>
                        <div class="chart-container"><canvas id="turnoverChart"></canvas></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <div class="card-header"><h3><i class="fa fa-line-chart icon-green"></i> Évolution salaire net moyen (12 mois)</h3></div>
                        <div class="chart-container"><canvas id="evolSalaryChart"></canvas></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Répartition par contrat, nationalité, catégorie pro -->
        <div class="row mt-4">
            <?php if ($this->rbac->hasPrivilege('staff_present_today_widegts', 'can_view')) : ?>
                <div class="col-lg-4"><div class="dashboard-card"><div class="card-header"><h3><i class="fa fa-file-contract icon-orange"></i> Contrats</h3></div><div class="chart-container"><canvas id="contratChart"></canvas></div></div></div>
                <div class="col-lg-4"><div class="dashboard-card"><div class="card-header"><h3><i class="fa fa-flag icon-blue"></i> Nationalité</h3></div><div class="chart-container"><canvas id="nationaliteChart"></canvas></div></div></div>
                <div class="col-lg-4"><div class="dashboard-card"><div class="card-header"><h3><i class="fa fa-briefcase icon-purple"></i> Catégorie professionnelle</h3></div><div class="chart-container"><canvas id="catproChart"></canvas></div></div></div>
            <?php endif; ?>
        </div>

        <!-- Salaire moyen par département -->
        <div class="row" style="margin-bottom: 60px; clear: both;">
            <?php if ($this->rbac->hasPrivilege('staff_present_today_widegts', 'can_view')) : ?>
                <div class="col-md-12">
                    <div class="dashboard-card">
                        <div class="card-header"><h3><i class="fa fa-building icon-teal"></i> Salaire net moyen par département</h3></div>
                        <div class="chart-container" style="height: 350px; position: relative;">
                            <canvas id="salaryDeptChart" style="height: 100%; width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Graphique Évolution congés, absences, arrêts -->
        <div class="row" style="margin-top: 60px; clear: both;">
            <?php if ($this->rbac->hasPrivilege('staff_present_today_widegts', 'can_view')) : ?>
                <div class="col-md-12">
                    <div class="dashboard-card">
                        <div class="card-header"><h3><i class="fa fa-calendar-alt icon-red"></i> Évolution congés, absences, arrêts - <?php echo $selected_year; ?></h3></div>
                        <div class="chart-container" style="height: 350px; position: relative;">
                            <canvas id="leaveChart" style="height: 100%; width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    // Données PHP vers JS
    var pyramidLabels = <?php echo json_encode($pyramid_labels); ?>;
    var pyramidHommes = <?php echo json_encode($pyramid_hommes); ?>;
    var pyramidFemmes = <?php echo json_encode($pyramid_femmes); ?>;
    var turnoverMois = <?php echo json_encode($turnover_mois); ?>;
    var moisLabels = <?php echo json_encode($mois_labels); ?>;
    var contratLabels = <?php echo json_encode($contrat_labels); ?>;
    var contratValues = <?php echo json_encode($contrat_values); ?>;
    var natLabels = <?php echo json_encode(array_keys($nationalites)); ?>;
    var natValues = <?php echo json_encode(array_values($nationalites)); ?>;
    var catproLabels = <?php echo json_encode($catpro_labels); ?>;
    var catproValues = <?php echo json_encode($catpro_values); ?>;
    var evolPeriodes = <?php echo json_encode($evol_periodes); ?>;
    var evolSalaires = <?php echo json_encode($evol_salaires); ?>;
    var deptNames = <?php echo json_encode($dept_names); ?>;
    var deptSalMoy = <?php echo json_encode($dept_sal_moy); ?>;
    var congesData = <?php echo json_encode($conges_data); ?>;
    var absencesData = <?php echo json_encode($absences_data); ?>;
    var arretsData = <?php echo json_encode($arrets_data); ?>;

    // Pyramide des âges
    new Chart(document.getElementById('pyramidChart'), {
        type: 'horizontalBar', data: { labels: pyramidLabels, datasets: [
                { label: 'Hommes', data: pyramidHommes, backgroundColor: '#3B82F6', borderWidth: 0 },
                { label: 'Femmes', data: pyramidFemmes.map(v=>-v), backgroundColor: '#EC4899', borderWidth: 0 }
            ]}, options: { responsive: true, maintainAspectRatio: false, scales: { xAxes: [{ ticks: { callback: v=>Math.abs(v), stepSize: 1 } }], yAxes: [{ stacked: true }] }, tooltips: { callbacks: { label: (item,data)=>data.datasets[item.datasetIndex].label+': '+Math.abs(item.xLabel) } } }
    });

    // Turnover
    new Chart(document.getElementById('turnoverChart'), { type: 'line', data: { labels: moisLabels, datasets: [{ label: 'Sorties', data: turnoverMois, borderColor: '#F97316', fill: false }] }, options: { responsive: true } });

    // Contrats
    new Chart(document.getElementById('contratChart'), { type: 'pie', data: { labels: contratLabels, datasets: [{ data: contratValues, backgroundColor: ['#FF9800','#9C27B0','#00BCD4','#607D8B','#795548'] }] } });

    // Nationalités
    if(natLabels.length) new Chart(document.getElementById('nationaliteChart'), { type: 'pie', data: { labels: natLabels, datasets: [{ data: natValues, backgroundColor: '#10B981' }] } });
    else document.getElementById('nationaliteChart').parentNode.innerHTML = '<div class="alert alert-info">Aucune nationalité renseignée</div>';

    // Catégorie professionnelle
    if(catproLabels.length) new Chart(document.getElementById('catproChart'), { type: 'bar', data: { labels: catproLabels, datasets: [{ label: 'Effectif', data: catproValues, backgroundColor: '#8B5CF6' }] }, options: { responsive: true } });

    // Évolution salaire
    if(evolPeriodes.length) new Chart(document.getElementById('evolSalaryChart'), { type: 'line', data: { labels: evolPeriodes, datasets: [{ label: 'Salaire net moyen (FCFA)', data: evolSalaires, borderColor: '#10B981', fill: false }] }, options: { responsive: true, scales: { yAxes: [{ ticks: { callback: v=>v.toLocaleString() } }] } } });

    // Salaire par département
    new Chart(document.getElementById('salaryDeptChart'), { type: 'bar', data: { labels: deptNames, datasets: [{ label: 'Salaire net moyen (FCFA)', data: deptSalMoy, backgroundColor: '#F59E0B' }] }, options: { responsive: true, scales: { yAxes: [{ ticks: { callback: v=>v.toLocaleString() } }] } } });

    // Congés/absences/arrêts
    new Chart(document.getElementById('leaveChart'), { type: 'line', data: { labels: moisLabels, datasets: [
                { label: 'Congés', data: congesData, borderColor: '#2196F3', fill: false },
                { label: 'Absences', data: absencesData, borderColor: '#F44336', fill: false },
                { label: 'Arrêts', data: arretsData, borderColor: '#FF9800', fill: false }
            ] }, options: { responsive: true, maintainAspectRatio: false, scales: { yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }] } } });

    // Export Excel (inchangé)
    document.getElementById('exportExcelBtn').addEventListener('click', function(){
        var wb = XLSX.utils.book_new();
        var dataSyn = [['Indicateur','Valeur'],['Période','<?php echo addslashes($period_label); ?>'],['Revenus',<?php echo $montant_revenus; ?>],['Dépenses',<?php echo $total_expenses; ?>],['Solde',<?php echo $solde_actuel; ?>],['Transactions',<?php echo $nb_trans; ?>],['Effectif total',<?php echo $total_employes; ?>],['Âge médian',<?php echo $mediane_age; ?>],['Ancienneté moyenne',<?php echo $anciennete_moyenne; ?>],['Salaire net moyen',<?php echo $salaire_net_moyen; ?>]];
        XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(dataSyn), 'Synthèse');
        var dataPyramid = [['Tranche','Hommes','Femmes']]; pyramidLabels.forEach((l,i)=>dataPyramid.push([l,pyramidHommes[i],pyramidFemmes[i]]));
        XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(dataPyramid), 'Pyramide âges');
        var dataTurn = [['Mois','Sorties']]; moisLabels.forEach((m,i)=>dataTurn.push([m,turnoverMois[i]]));
        XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(dataTurn), 'Turnover');
        var dataLeave = [['Mois','Congés','Absences','Arrêts']]; moisLabels.forEach((m,i)=>dataLeave.push([m,congesData[i],absencesData[i],arretsData[i]]));
        XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(dataLeave), 'Congés-Absences-Arrêts');
        XLSX.writeFile(wb, 'dashboard_RH_complet_<?php echo date('Y-m-d'); ?>.xlsx');
    });
</script>