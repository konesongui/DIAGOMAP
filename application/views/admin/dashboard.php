<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style type="text/css">
    @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap');

    :root {
        --dash-primary: #1b4f80;
        --dash-primary-dark: #153a5d;
        --dash-primary-soft: #eef6fc;
        --dash-accent: #e5a823;
        --dash-bg: #f4f6f9;
        --dash-text: #1f2d3d;
        --dash-muted: #5f6f82;
        --dash-card: #ffffff;
    }

    /* ===== FOND GÉNÉRAL LIGHT ===== */
    body, .content-wrapper {
        background: radial-gradient(1200px 600px at 15% -10%, #e8f1fb 0%, #f6f8fb 45%, #f3f5f7 100%) !important;
        font-family: 'Manrope', sans-serif;
        color: var(--dash-text);
    }

    .content-dashboard {
        max-width: 1540px;
        margin: 0 auto;
    }

    .dashboard-hero {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        padding: 26px 28px;
        margin-bottom: 22px;
        color: #fff;
        background: linear-gradient(130deg, #FFB900, #005FAB);
        box-shadow: 0 16px 40px rgba(0, 60, 110, 0.18);
    }

    .dashboard-hero:before,
    .dashboard-hero:after {
        content: "";
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
    }

    .dashboard-hero:before {
        width: 270px;
        height: 270px;
        right: -85px;
        top: -130px;
        background: rgba(255, 255, 255, 0.12);
    }

    .dashboard-hero:after {
        width: 180px;
        height: 180px;
        right: 130px;
        bottom: -110px;
        background: rgba(255, 255, 255, 0.08);
    }

    .hero-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .hero-title {
        margin: 0;
        font-size: 30px;
        font-weight: 800;
        letter-spacing: -0.4px;
        display: flex;
        align-items: center;
        gap: 12px;
        white-space: nowrap;
    }

    .hero-title i {
        font-size: 26px;
        background: rgba(255,255,255,0.2);
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .hero-subtitle {
        margin: 10px 0 0;
        font-size: 14px;
        opacity: 0.94;
    }

    .hero-metrics {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 10px;
        flex: 1 1 auto;
    }

    .hero-chip {
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.28);
        border-radius: 12px;
        padding: 12px 16px;
        min-width: 130px;
        backdrop-filter: blur(4px);
        transition: transform .2s ease, background .2s ease;
    }
    .hero-chip:hover {
        transform: translateY(-3px);
        background: rgba(255, 255, 255, 0.26);
    }

    .hero-chip .k {
        display: block;
        font-size: 20px;
        font-weight: 800;
        line-height: 1.1;
    }

    .hero-chip .l {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        opacity: 0.85;
        margin-top: 3px;
    }

    /* ===== TITRES DE SECTION — style sobre par défaut ===== */
    .section-title {
        margin: 26px 0 16px 0;
        padding: 12px 20px;
        background: #fff;
        border: 1px solid #dbe8f5;
        border-left: 4px solid var(--dash-primary);
        border-radius: 12px;
        color: var(--dash-primary-dark);
        font-size: 15px;
        font-weight: 700;
        display: flex;
        align-items: center;
        letter-spacing: 1px;
        box-shadow: 0 4px 14px rgba(15, 40, 65, 0.06);
        text-transform: uppercase;
        width: 100%;
    }
    .section-title i {
        margin-right: 12px;
        font-size: 16px;
        background: var(--dash-primary-soft);
        color: var(--dash-primary);
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* ===== Variante pleine couleur — réservée au 1er bandeau (Trésorerie) ===== */
    .section-title.section-title--primary {
        background: linear-gradient(90deg, var(--dash-primary) 0%, var(--dash-primary-dark) 100%);
        border: none;
        color: #fff;
        box-shadow: 0 10px 24px rgba(15, 40, 65, 0.14);
    }
    .section-title.section-title--primary i {
        background: rgba(255,255,255,0.16);
        color: #fff;
    }

    .sub-section-title {
        margin: 4px 0 14px 0;
        font-size: 15px;
        font-weight: 700;
        color: #273772;
        padding: 8px 14px;
        border-left: 4px solid #273772;
        background: rgba(39,55,114,0.05);
        border-radius: 0 8px 8px 0;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        display: inline-block;
    }
    .sub-section-title i {
        margin-right: 8px;
        color: #273772;
    }

    /* ===== FONDS POUR GRAPHIQUES (sauf le donut) ===== */
    .chart-container:not(.chic-donut-container) {
        position: relative;
        height: 300px;
        padding: 20px;
        background: linear-gradient(145deg, #ffffff, #f2f7fc);
        border-radius: 16px;
        box-shadow: inset 0 2px 8px rgba(0,0,0,0.02), 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #e2eaf3;
        transition: all 0.3s ease;
    }
    .chart-container:not(.chic-donut-container):hover {
        box-shadow: inset 0 2px 8px rgba(0,0,0,0.03), 0 6px 30px rgba(0,0,0,0.08);
    }

    /* === Donut : design original conservé === */
    .chic-donut-container {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        background: transparent;
        padding: 20px;
    }
    .donut-center-text {
        position: absolute;
        text-align: center;
        pointer-events: none;
        background: white;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        z-index: 2;
    }
    .donut-center-text .total-label {
        font-size: 12px;
        color: #6c757d;
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    .donut-center-text .total-amount {
        font-size: 20px;
        font-weight: 800;
        background: linear-gradient(135deg, #2c3e50, #1a252f);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        line-height: 1.2;
    }
    .donut-center-text .total-currency {
        font-size: 11px;
        font-weight: 600;
        color: #95a5a6;
    }

    /* ===== CARTES ET STATISTIQUES ===== */
    .dashboard-card {
        border-radius: 16px;
        box-shadow: 0 10px 24px rgba(25, 55, 85, 0.09);
        border: 1px solid #dbe8f5;
        margin-bottom: 25px;
        transition: all 0.3s ease;
        background: rgba(255,255,255,0.96);
        backdrop-filter: blur(4px);
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.08);
    }
    .card-header {
        background: transparent !important;
        border: none !important;
        padding: 16px 20px 7px 20px !important;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: var(--dash-text);
    }
    .card-header h3 {
        font-weight: 600;
        font-size: 16px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .stat-card {
        background: var(--dash-card);
        backdrop-filter: blur(4px);
        border-radius: 16px;
        padding: 22px 16px;
        text-align: center;
        box-shadow: 0 8px 20px rgba(20, 49, 77, 0.08);
        transition: all 0.3s ease;
        border: 1px solid #d9e7f4;
        border-top: 4px solid #c8dbef;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        font-size: 24px;
        background: rgba(255,255,255,0.2);
    }
    .stat-number {
        font-size: 24px;
        font-weight: 700;
        background: linear-gradient(135deg, #173c60, #0f2d49);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 5px 0;
    }
    .stat-text {
        font-size: 13px;
        font-weight: 500;
        color: #64748b;
    }

    .stat-row { margin-bottom: 24px; }

    /* ===== FILTRE ===== */
    .date-range-group input {
        border-radius: 12px;
        border: 1px solid #cfe0f0;
        padding: 9px 12px;
        font-size: 14px;
        background: #fff;
        transition: all 0.3s ease;
    }
    .date-range-group input:focus {
        border-color: #273772;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.2);
        outline: none;
    }
    .btn-export {
        border-radius: 12px;
        padding: 9px 16px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .filter-shell {
        padding: 16px;
        border-radius: 14px;
        background: linear-gradient(180deg, #ffffff 0%, #f6f9fd 100%);
        border: 1px solid #dbe8f3;
    }

    .filter-shell .date-range-group {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: nowrap;
    }

    .filter-shell .dashboard-filter-form {
        display: flex;
        justify-content: flex-end;
        width: 100%;
    }

    .filter-shell .filter-actions {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-left: 6px;
    }

    .filter-shell .period-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        color: #264765;
        background: var(--dash-primary-soft);
        border: 1px solid #d2e3f3;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .btn-export i {
        margin-right: 5px;
    }

    /* ===== AUTRES ===== */
    .borderwhite{border-top-color:#fff!important;}
    .box-header>.box-tools{display:none;}
    .sidebar-collapse #barChart,.sidebar-collapse #lineChart{height:100%!important;}
    .progress-modern{height:8px;border-radius:10px;background:#f0f0f0;overflow:hidden;}
    .progress-bar-modern{border-radius:10px;}
    .info-box{min-height:90px;background:#fff;border-radius:8px;margin-bottom:15px;box-shadow:0 2px 8px rgba(0,0,0,0.1);transition:all .3s ease;}
    .info-box:hover{transform:translateY(-3px);box-shadow:0 5px 15px rgba(0,0,0,0.15);}
    .info-box-icon{border-radius:8px 0 0 8px;display:flex;align-items:center;justify-content:center;font-size:28px;width:70px;}
    .info-box-content{padding:15px;}
    .info-box-text{font-weight:600;font-size:12px;color:#333;}
    .info-box-number{font-size:20px;font-weight:700;color:#273772;}
    .badge-rh{padding:5px 10px;border-radius:20px;font-size:12px;font-weight:600;color:#fff;text-transform:uppercase;letter-spacing:0.5px;}
    .table-hover tbody tr:hover{transform:translateX(5px);transition:transform .3s ease;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
    .pyramid-container{height:350px;}
    .icon-blue{color:#273772;}
    .icon-green{color:#4CAF50;}
    .icon-red{color:#F44336;}
    .icon-orange{color:#FF9800;}
    .icon-purple{color:#2563eb;}
    .icon-teal{color:#009688;}

    @media (max-width: 991px) {
        .hero-top {
            flex-direction: column;
            align-items: flex-start;
        }

        .hero-metrics {
            justify-content: flex-start;
        }

        .filter-shell .date-range-group {
            justify-content: flex-start;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .filter-shell .dashboard-filter-form {
            justify-content: flex-start;
        }

        .filter-shell .filter-actions {
            margin-left: 0;
        }
    }
</style>

<?php
// ==================== CONNEXION ET CONFIGURATION ====================
$CI = &get_instance();
$conn = new mysqli($CI->db->hostname, $CI->db->username, $CI->db->password, $CI->db->database);
if ($conn->connect_error) die("Erreur de connexion: " . $conn->connect_error);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==================== RÉCUPÉRATION DE L'ENTREPRISE CONNECTÉE ====================
$userdata = $this->customlib->getUserData();
$entreprise_id = $userdata['entreprise_id'] ?? 0;
$filtre_entreprise = ($entreprise_id > 0) ? "AND entreprise_id = $entreprise_id" : ""; // on garde mais on va remplacer par des préfixes

// ==================== FILTRE PÉRIODE ====================
$date_debut = $_GET['date_debut'] ?? null;
$date_fin   = $_GET['date_fin'] ?? null;
if (!$date_debut && !$date_fin) { $date_debut = date('Y-m-01'); $date_fin = date('Y-m-t'); }
if ($date_debut && $date_fin && strtotime($date_debut) > strtotime($date_fin)) { $tmp = $date_debut; $date_debut = $date_fin; $date_fin = $tmp; }
if ($date_debut && $date_fin) { $date_condition = "date BETWEEN '$date_debut' AND '$date_fin'"; $period_label = "Du ".date('d/m/Y',strtotime($date_debut))." au ".date('d/m/Y',strtotime($date_fin)); }
elseif ($date_debut) { $date_condition = "date >= '$date_debut'"; $period_label = "À partir du ".date('d/m/Y',strtotime($date_debut)); }
elseif ($date_fin) { $date_condition = "date <= '$date_fin'"; $period_label = "Jusqu'au ".date('d/m/Y',strtotime($date_fin)); }
else { $date_condition = "1=1"; $period_label = "Toutes périodes"; }

// ==================== DONNÉES TRÉSORERIE ====================
$sql_entrees = "SELECT COALESCE(SUM(montant),0) as total FROM operation_caisse WHERE $date_condition AND (est_actif=1 OR est_actif IS NULL) AND deleted=0 AND type_operation='ENTREE'";
if ($entreprise_id > 0) $sql_entrees .= " AND operation_caisse.entreprise_id = $entreprise_id";
$revenus_ops = $conn->query($sql_entrees)->fetch_object()->total ?? 0;

$check_col = $conn->query("SHOW COLUMNS FROM operation_caisse LIKE 'est_active'");
$use_est_active = ($check_col && $check_col->num_rows > 0);
if ($use_est_active) $sql_dep = "SELECT COALESCE(SUM(montant),0) as total FROM operation_caisse WHERE $date_condition AND (est_active=1 OR est_active IS NULL) AND deleted=0 AND type_operation='SORTIE'";
else $sql_dep = "SELECT COALESCE(SUM(montant),0) as total FROM operation_caisse WHERE $date_condition AND est_actif=1 AND deleted=0 AND type_operation='SORTIE'";
if ($entreprise_id > 0) $sql_dep .= " AND operation_caisse.entreprise_id = $entreprise_id";
$depenses_ops = $conn->query($sql_dep)->fetch_object()->total ?? 0;

$sql_solde = "SELECT COALESCE(SUM(amount_re),0) as total FROM income WHERE deleted=1 AND est_actif=1";
if ($entreprise_id > 0) $sql_solde .= " AND income.entreprise_id = $entreprise_id";
$solde_actuel = $conn->query($sql_solde)->fetch_object()->total ?? 0;

$sql_trans = "SELECT COUNT(*) as total FROM (
    SELECT id FROM operation_caisse WHERE $date_condition AND (est_active=1 OR est_actif=1 OR est_active IS NULL) AND deleted=0";
if ($entreprise_id > 0) $sql_trans .= " AND operation_caisse.entreprise_id = $entreprise_id";
$sql_trans .= " UNION ALL
    SELECT id FROM income WHERE $date_condition AND est_actif=1 AND deleted=1";
if ($entreprise_id > 0) $sql_trans .= " AND income.entreprise_id = $entreprise_id";
$sql_trans .= " UNION ALL
    SELECT income_id FROM income_processing WHERE $date_condition AND deleted=1";
if ($entreprise_id > 0) $sql_trans .= " AND income_processing.entreprise_id = $entreprise_id";
$sql_trans .= ") t";
$nb_trans = $conn->query($sql_trans)->fetch_object()->total ?? 0;

// ==================== DONNÉES COMPTABILITÉ ====================
$sql_income = "SELECT COALESCE(SUM(amount),0) as total FROM income WHERE $date_condition AND deleted=1 AND est_actif=1";
if ($entreprise_id > 0) $sql_income .= " AND income.entreprise_id = $entreprise_id";
$revenus_income = $conn->query($sql_income)->fetch_object()->total ?? 0;

$sql_reappro = "SELECT COALESCE(SUM(amount),0) as total FROM income_processing WHERE $date_condition AND deleted=1";
if ($entreprise_id > 0) $sql_reappro .= " AND income_processing.entreprise_id = $entreprise_id";
$revenus_reappro = $conn->query($sql_reappro)->fetch_object()->total ?? 0;

$sql_bank_in = "SELECT COALESCE(SUM(amount),0) as total FROM bank WHERE $date_condition AND transaction_type = 'Virement entrant' AND reference NOT LIKE 'TRF%' AND is_active='yes'";
if ($entreprise_id > 0) $sql_bank_in .= " AND bank.entreprise_id = $entreprise_id";
$revenus_bank = $conn->query($sql_bank_in)->fetch_object()->total ?? 0;

$montant_revenus = $revenus_ops + $revenus_income + $revenus_reappro + $revenus_bank;

$sql_bank_out = "SELECT COALESCE(SUM(amount),0) as total FROM bank WHERE $date_condition AND transaction_type = 'Virement sortant' AND reference NOT LIKE 'TRF%' AND is_active='yes'";
if ($entreprise_id > 0) $sql_bank_out .= " AND bank.entreprise_id = $entreprise_id";
$depenses_bank = $conn->query($sql_bank_out)->fetch_object()->total ?? 0;

$check_exp = $conn->query("SHOW TABLES LIKE 'expenses'");
$depenses_old = 0;
if ($check_exp && $check_exp->num_rows > 0) {
    $sql_old = "SELECT COALESCE(SUM(amount),0) as total FROM expenses WHERE $date_condition AND deleted=0";
    if ($entreprise_id > 0) $sql_old .= " AND expenses.entreprise_id = $entreprise_id";
    $depenses_old = $conn->query($sql_old)->fetch_object()->total ?? 0;
}
$total_expenses = $depenses_ops + $depenses_bank + $depenses_old;

// Dépenses par catégorie
$sql_cat = "SELECT COALESCE(eh.exp_category, oc.category, 'Non catégorisé') as cat, SUM(oc.montant) as total 
            FROM operation_caisse oc 
            LEFT JOIN expense_head eh ON oc.exp_head_id = eh.id 
            WHERE $date_condition AND ".($use_est_active?"(oc.est_active=1 OR oc.est_active IS NULL)":"oc.est_actif=1")." 
            AND oc.deleted=0 AND oc.type_operation='SORTIE'";
if ($entreprise_id > 0) $sql_cat .= " AND oc.entreprise_id = $entreprise_id";
$sql_cat .= " GROUP BY cat ORDER BY total DESC LIMIT 10";
$res_cat = $conn->query($sql_cat);
$labels_cat = []; $data_cat = [];
while ($row = $res_cat->fetch_assoc()) {
    $labels_cat[] = $row['cat'];
    $data_cat[] = floatval($row['total']);
}
if ($depenses_bank > 0) {
    $found = false;
    foreach ($labels_cat as $key => $label) {
        if (strtolower($label) == 'banque') {
            $data_cat[$key] += $depenses_bank;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $labels_cat[] = 'Banque';
        $data_cat[] = $depenses_bank;
    }
}
if (empty($labels_cat)) { $labels_cat = ['Aucune dépense']; $data_cat = [0]; }

// Graphique revenus vs dépenses
$diff_days = (strtotime($date_fin)-strtotime($date_debut))/(60*60*24);
$rev_graph = []; $dep_graph = []; $labels_graph = [];
$cond_actif = $use_est_active ? "(est_active=1 OR est_active IS NULL)" : "est_actif=1";

if ($diff_days <= 31) {
    $rev_map = []; $dep_map = [];
    $cur = strtotime($date_debut); $end = strtotime($date_fin);
    while ($cur <= $end) {
        $jour = date('Y-m-d',$cur);
        $labels_graph[] = date('d/m',$cur);
        $rev_map[$jour]=0;
        $dep_map[$jour]=0;
        $cur = strtotime('+1 day',$cur);
    }
    $sql_r = "SELECT DATE(date) as j, SUM(amount) as tot FROM income WHERE $date_condition AND deleted=1 AND est_actif=1";
    if ($entreprise_id > 0) $sql_r .= " AND income.entreprise_id = $entreprise_id";
    $sql_r .= " GROUP BY DATE(date) UNION ALL 
               SELECT DATE(date), SUM(amount) FROM income_processing WHERE $date_condition AND deleted=1";
    if ($entreprise_id > 0) $sql_r .= " AND income_processing.entreprise_id = $entreprise_id";
    $sql_r .= " GROUP BY DATE(date) UNION ALL 
               SELECT DATE(date), SUM(montant) FROM operation_caisse WHERE $date_condition AND type_operation='ENTREE' AND deleted=0 AND $cond_actif";
    if ($entreprise_id > 0) $sql_r .= " AND operation_caisse.entreprise_id = $entreprise_id";
    $sql_r .= " GROUP BY DATE(date) UNION ALL 
               SELECT DATE(date), SUM(amount) FROM bank WHERE $date_condition AND transaction_type = 'Virement entrant' AND reference NOT LIKE 'TRF%' AND is_active='yes'";
    if ($entreprise_id > 0) $sql_r .= " AND bank.entreprise_id = $entreprise_id";
    $sql_r .= " GROUP BY DATE(date)";
    $res_r = $conn->query($sql_r);
    while($rw=$res_r->fetch_assoc()) if(isset($rev_map[$rw['j']])) $rev_map[$rw['j']] += $rw['tot'];

    $sql_d = "SELECT DATE(date) as j, SUM(montant) as tot FROM operation_caisse WHERE $date_condition AND type_operation='SORTIE' AND deleted=0 AND $cond_actif";
    if ($entreprise_id > 0) $sql_d .= " AND operation_caisse.entreprise_id = $entreprise_id";
    $sql_d .= " GROUP BY DATE(date) UNION ALL 
               SELECT DATE(date), SUM(amount) FROM bank WHERE $date_condition AND transaction_type = 'Virement sortant' AND reference NOT LIKE 'TRF%' AND is_active='yes'";
    if ($entreprise_id > 0) $sql_d .= " AND bank.entreprise_id = $entreprise_id";
    $sql_d .= " GROUP BY DATE(date)";
    $res_d = $conn->query($sql_d);
    while($rw=$res_d->fetch_assoc()) if(isset($dep_map[$rw['j']])) $dep_map[$rw['j']] += $rw['tot'];

    $rev_graph = array_values($rev_map);
    $dep_graph = array_values($dep_map);
} else {
    $rev_map = []; $dep_map = [];
    $start_week = date('W',strtotime($date_debut));
    $end_week = date('W',strtotime($date_fin));
    for($w=$start_week;$w<=$end_week;$w++) {
        $labels_graph[]="Semaine $w";
        $rev_map[$w]=0;
        $dep_map[$w]=0;
    }
    $sql_r = "SELECT WEEK(date,1) as w, SUM(amount) as tot FROM income WHERE $date_condition AND deleted=1 AND est_actif=1";
    if ($entreprise_id > 0) $sql_r .= " AND income.entreprise_id = $entreprise_id";
    $sql_r .= " GROUP BY WEEK(date,1) UNION ALL 
               SELECT WEEK(date,1), SUM(amount) FROM income_processing WHERE $date_condition AND deleted=1";
    if ($entreprise_id > 0) $sql_r .= " AND income_processing.entreprise_id = $entreprise_id";
    $sql_r .= " GROUP BY WEEK(date,1) UNION ALL 
               SELECT WEEK(date,1), SUM(montant) FROM operation_caisse WHERE $date_condition AND type_operation='ENTREE' AND deleted=0 AND $cond_actif";
    if ($entreprise_id > 0) $sql_r .= " AND operation_caisse.entreprise_id = $entreprise_id";
    $sql_r .= " GROUP BY WEEK(date,1) UNION ALL 
               SELECT WEEK(date,1), SUM(amount) FROM bank WHERE $date_condition AND transaction_type = 'Virement entrant' AND reference NOT LIKE 'TRF%' AND is_active='yes'";
    if ($entreprise_id > 0) $sql_r .= " AND bank.entreprise_id = $entreprise_id";
    $sql_r .= " GROUP BY WEEK(date,1)";
    $res_r = $conn->query($sql_r);
    while($rw=$res_r->fetch_assoc()) if(isset($rev_map[$rw['w']])) $rev_map[$rw['w']] += $rw['tot'];

    $sql_d = "SELECT WEEK(date,1) as w, SUM(montant) as tot FROM operation_caisse WHERE $date_condition AND type_operation='SORTIE' AND deleted=0 AND $cond_actif";
    if ($entreprise_id > 0) $sql_d .= " AND operation_caisse.entreprise_id = $entreprise_id";
    $sql_d .= " GROUP BY WEEK(date,1) UNION ALL 
               SELECT WEEK(date,1), SUM(amount) FROM bank WHERE $date_condition AND transaction_type = 'Virement sortant' AND reference NOT LIKE 'TRF%' AND is_active='yes'";
    if ($entreprise_id > 0) $sql_d .= " AND bank.entreprise_id = $entreprise_id";
    $sql_d .= " GROUP BY WEEK(date,1)";
    $res_d = $conn->query($sql_d);
    while($rw=$res_d->fetch_assoc()) if(isset($dep_map[$rw['w']])) $dep_map[$rw['w']] += $rw['tot'];

    $rev_graph = array_values($rev_map);
    $dep_graph = array_values($dep_map);
}

// ==================== DONNÉES COMMERCIALES ====================
$sql_ventes = "SELECT 
    COALESCE(SUM(CAST(total_ttc AS DECIMAL(15,2))),0) as total_ventes,
    COUNT(*) as nb_factures,
    COALESCE(SUM(CAST(remaining_amount AS DECIMAL(15,2))),0) as total_impaye
    FROM invoices 
    WHERE invoice_date BETWEEN '$date_debut' AND '$date_fin' 
    AND cancelled_at IS NULL";
if ($entreprise_id > 0) $sql_ventes .= " AND invoices.entreprise_id = $entreprise_id";
$res_ventes = $conn->query($sql_ventes);
if ($res_ventes) {
    $ventes = $res_ventes->fetch_assoc();
    $total_ventes = $ventes['total_ventes'];
    $nb_ventes = $ventes['nb_factures'];
    $total_impaye_ventes = $ventes['total_impaye'];
} else {
    $total_ventes = 0; $nb_ventes = 0; $total_impaye_ventes = 0;
}

$sql_achats = "SELECT 
    COALESCE(SUM(CAST(total_ttc AS DECIMAL(15,2))),0) as total_achats,
    COUNT(*) as nb_factures,
    COALESCE(SUM(CAST(remaining_amount AS DECIMAL(15,2))),0) as total_impaye
    FROM invoices_supplier 
    WHERE invoice_date BETWEEN '$date_debut' AND '$date_fin' 
    AND cancelled_at IS NULL";
if ($entreprise_id > 0) $sql_achats .= " AND invoices_supplier.entreprise_id = $entreprise_id";
$res_achats = $conn->query($sql_achats);
if ($res_achats) {
    $achats = $res_achats->fetch_assoc();
    $total_achats = $achats['total_achats'];
    $nb_achats = $achats['nb_factures'];
    $total_impaye_achats = $achats['total_impaye'];
} else {
    $total_achats = 0; $nb_achats = 0; $total_impaye_achats = 0;
}

// ==================== DONNÉES RH ====================
$sql_employes = "SELECT id, name, gender, dob, date_of_joining, department, designation, contract_type, nationalite, categorie_salaire, is_active 
                 FROM staff 
                 WHERE is_active=1 AND deleted=1 AND name != 'Super Admin'";
if ($entreprise_id > 0) $sql_employes .= " AND staff.entreprise_id = $entreprise_id";
$employes = $conn->query($sql_employes);
$total_employes = $employes->num_rows;

$ages = []; $anciennetes = [];
while($e = $employes->fetch_assoc()) {
    if($e['dob']) $ages[] = date_diff(date_create($e['dob']), date_create('today'))->y;
    if($e['date_of_joining']) $anciennetes[] = date_diff(date_create($e['date_of_joining']), date_create('today'))->y;
}
sort($ages); $mediane_age = count($ages) ? (count($ages)%2 ? $ages[floor(count($ages)/2)] : ($ages[count($ages)/2-1]+$ages[count($ages)/2])/2) : 0;
$anciennete_moyenne = count($anciennetes) ? round(array_sum($anciennetes)/count($anciennetes),1) : 0;

$turnover_mois = array_fill(0,12,0);
$sql_turn = "SELECT MONTH(date) as m, COUNT(*) as c FROM enquiry WHERE YEAR(date)=YEAR(CURDATE()) AND (reference='demission' OR reference LIKE '%demission%')";
if ($entreprise_id > 0) $sql_turn .= " AND enquiry.entreprise_id = $entreprise_id";
$sql_turn .= " GROUP BY MONTH(date)";
$res_turn = $conn->query($sql_turn);
if($res_turn) while($row=$res_turn->fetch_assoc()) $turnover_mois[$row['m']-1] = $row['c'];

$sql_leave = "SELECT MONTH(date_of_leaving) as m, COUNT(*) as c FROM staff WHERE YEAR(date_of_leaving)=YEAR(CURDATE()) AND date_of_leaving IS NOT NULL AND date_of_leaving != '0000-00-00'";
if ($entreprise_id > 0) $sql_leave .= " AND staff.entreprise_id = $entreprise_id";
$sql_leave .= " GROUP BY MONTH(date_of_leaving)";
$res_leave = $conn->query($sql_leave);
if($res_leave) while($row=$res_leave->fetch_assoc()) $turnover_mois[$row['m']-1] += $row['c'];

$tranches = ['18-24','25-29','30-34','35-39','40-44','45-49','50-54','55-59','60+'];
$pyramide = array_fill(0, count($tranches), ['H'=>0,'F'=>0]);
$sql_age = "SELECT gender, dob FROM staff WHERE is_active=1 AND deleted=1 AND name!='Super Admin' AND dob IS NOT NULL";
if ($entreprise_id > 0) $sql_age .= " AND staff.entreprise_id = $entreprise_id";
$employes_age = $conn->query($sql_age);
while($e = $employes_age->fetch_assoc()) {
    $age = date_diff(date_create($e['dob']), date_create('today'))->y;
    $idx = $age>=60 ? 8 : ($age>=55 ? 7 : ($age>=50 ? 6 : ($age>=45 ? 5 : ($age>=40 ? 4 : ($age>=35 ? 3 : ($age>=30 ? 2 : ($age>=25 ? 1 : 0)))))));
    if($e['gender']=='Male') $pyramide[$idx]['H']++;
    else $pyramide[$idx]['F']++;
}
$pyramid_labels = $tranches;
$pyramid_hommes = array_column($pyramide,'H');
$pyramid_femmes = array_column($pyramide,'F');

$contrat_data = ['CDI'=>0,'CDD'=>0,'Stage'=>0,'Intérim'=>0,'Autre'=>0];
$sql_contrat = "SELECT contract_type, COUNT(*) as nb FROM staff WHERE is_active=1 AND deleted=1 AND name!='Super Admin'";
if ($entreprise_id > 0) $sql_contrat .= " AND staff.entreprise_id = $entreprise_id";
$sql_contrat .= " GROUP BY contract_type";
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

$nationalites = [];
$sql_nat = "SELECT nationalite, COUNT(*) as nb FROM staff WHERE is_active=1 AND deleted=1 AND name!='Super Admin' AND nationalite IS NOT NULL AND nationalite!=''";
if ($entreprise_id > 0) $sql_nat .= " AND staff.entreprise_id = $entreprise_id";
$sql_nat .= " GROUP BY nationalite ORDER BY nb DESC";
$res_nat = $conn->query($sql_nat);
while($row=$res_nat->fetch_assoc()) $nationalites[$row['nationalite']] = $row['nb'];

$cat_pro = [];
$sql_cp = "SELECT categorie_salaire, COUNT(*) as nb FROM staff WHERE is_active=1 AND deleted=1 AND name!='Super Admin' AND categorie_salaire IS NOT NULL AND categorie_salaire!=''";
if ($entreprise_id > 0) $sql_cp .= " AND staff.entreprise_id = $entreprise_id";
$sql_cp .= " GROUP BY categorie_salaire";
$res_cp = $conn->query($sql_cp);
while($row=$res_cp->fetch_assoc()) $cat_pro[$row['categorie_salaire']] = $row['nb'];

if(empty($cat_pro)) {
    $sql_des = "SELECT sd.designation, COUNT(*) as nb FROM staff s LEFT JOIN staff_designation sd ON s.designation=sd.id WHERE s.is_active=1 AND s.deleted=1 AND s.name!='Super Admin'";
    if ($entreprise_id > 0) $sql_des .= " AND s.entreprise_id = $entreprise_id";
    $sql_des .= " GROUP BY sd.designation";
    $res_des = $conn->query($sql_des);
    while($row=$res_des->fetch_assoc()) if($row['designation']) $cat_pro[$row['designation']] = $row['nb'];
}
$catpro_labels = array_keys($cat_pro);
$catpro_values = array_values($cat_pro);

// Salaire net moyen
$sql_last = "SELECT sp.staff_id, sp.net_salary FROM staff_payslip sp 
             INNER JOIN (
                 SELECT staff_id, MAX(STR_TO_DATE(CONCAT(year,'-',month,'-01'),'%Y-%M-%d')) as maxd 
                 FROM staff_payslip 
                 WHERE 1=1";
if ($entreprise_id > 0) $sql_last .= " AND staff_payslip.entreprise_id = $entreprise_id";
$sql_last .= " GROUP BY staff_id
             ) l ON sp.staff_id=l.staff_id AND STR_TO_DATE(CONCAT(sp.year,'-',sp.month,'-01'),'%Y-%M-%d')=l.maxd 
             WHERE sp.status='paid'";
if ($entreprise_id > 0) $sql_last .= " AND sp.entreprise_id = $entreprise_id";
$res_last = $conn->query($sql_last);
$masse_salariale = 0; $nb_salaires = 0;
while($row=$res_last->fetch_assoc()) { $masse_salariale += $row['net_salary']; $nb_salaires++; }
$salaire_net_moyen = $nb_salaires ? round($masse_salariale/$nb_salaires) : 0;

// Salaire moyen par département
$dept_names = []; $dept_sal_moy = [];
$sql_dept = "SELECT d.department_name, AVG(sp.net_salary) as sal_moy 
             FROM staff s 
             LEFT JOIN department d ON s.department=d.id 
             INNER JOIN (
                 SELECT staff_id, net_salary FROM staff_payslip sp1 
                 WHERE sp1.status='paid'";
if ($entreprise_id > 0) $sql_dept .= " AND sp1.entreprise_id = $entreprise_id";
$sql_dept .= " AND STR_TO_DATE(CONCAT(sp1.year,'-',sp1.month,'-01'),'%Y-%M-%d')=(
                     SELECT MAX(STR_TO_DATE(CONCAT(sp2.year,'-',sp2.month,'-01'),'%Y-%M-%d')) 
                     FROM staff_payslip sp2 
                     WHERE sp2.staff_id=sp1.staff_id";
if ($entreprise_id > 0) $sql_dept .= " AND sp2.entreprise_id = $entreprise_id";
$sql_dept .= "
                 )
             ) sp ON s.id=sp.staff_id 
             WHERE s.is_active=1 AND s.deleted=1";
if ($entreprise_id > 0) $sql_dept .= " AND s.entreprise_id = $entreprise_id";
$sql_dept .= " GROUP BY d.department_name";
$res_dept = $conn->query($sql_dept);
while($row=$res_dept->fetch_assoc()) { $dept_names[]=$row['department_name']??'Non défini'; $dept_sal_moy[]=round($row['sal_moy']); }

// Évolution salaire 12 mois
$evol_periodes = []; $evol_salaires = [];
$sql_evol = "SELECT CONCAT(month,' ',year) as periode, AVG(net_salary) as sal_moy 
             FROM staff_payslip 
             WHERE status='paid'";
if ($entreprise_id > 0) $sql_evol .= " AND staff_payslip.entreprise_id = $entreprise_id";
$sql_evol .= " AND STR_TO_DATE(CONCAT(year,'-',month,'-01'),'%Y-%M-%d') >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) 
             GROUP BY year, month 
             ORDER BY STR_TO_DATE(CONCAT(year,'-',month,'-01'),'%Y-%M-%d')";
$res_evol = $conn->query($sql_evol);
while($row=$res_evol->fetch_assoc()) { $evol_periodes[]=$row['periode']; $evol_salaires[]=round($row['sal_moy']); }

$selected_year = date('Y', strtotime($date_fin));
$mois_labels = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'];
$conges_data = array_fill(0,12,0);
$absences_data = array_fill(0,12,0);
$arrets_data = array_fill(0,12,0);

$sql_conges = "SELECT MONTH(leave_from) as m, COUNT(*) as c FROM staff_leave_request WHERE YEAR(leave_from) = $selected_year";
if ($entreprise_id > 0) $sql_conges .= " AND staff_leave_request.entreprise_id = $entreprise_id";
$sql_conges .= " GROUP BY MONTH(leave_from)";
$res_conges = $conn->query($sql_conges);
if($res_conges) while($row=$res_conges->fetch_assoc()) { $idx=$row['m']-1; if($idx>=0 && $idx<12) $conges_data[$idx]=$row['c']; }

$sql_abs = "SELECT MONTH(date) as m, COUNT(*) as c FROM enquiry WHERE YEAR(date)=$selected_year AND (reference='Permission' OR source LIKE '%maladie%' OR source LIKE '%voyage%')";
if ($entreprise_id > 0) $sql_abs .= " AND enquiry.entreprise_id = $entreprise_id";
$sql_abs .= " GROUP BY MONTH(date)";
$res_abs = $conn->query($sql_abs);
if($res_abs) while($row=$res_abs->fetch_assoc()) { $idx=$row['m']-1; if($idx>=0 && $idx<12) $absences_data[$idx]=$row['c']; }

$sql_att_abs = "SELECT MONTH(date) as m, COUNT(*) as c FROM staff_attendance WHERE YEAR(date)=$selected_year AND staff_attendance_type_id=4 AND is_active=0";
if ($entreprise_id > 0) $sql_att_abs .= " AND staff_attendance.entreprise_id = $entreprise_id";
$sql_att_abs .= " GROUP BY MONTH(date)";
$res_att_abs = $conn->query($sql_att_abs);
if($res_att_abs) while($row=$res_att_abs->fetch_assoc()) { $idx=$row['m']-1; if($idx>=0 && $idx<12) $absences_data[$idx]+=$row['c']; }

$sql_arr = "SELECT MONTH(date) as m, COUNT(*) as c FROM enquiry WHERE YEAR(date)=$selected_year AND (reference='demission' OR reference LIKE '%demission%')";
if ($entreprise_id > 0) $sql_arr .= " AND enquiry.entreprise_id = $entreprise_id";
$sql_arr .= " GROUP BY MONTH(date)";
$res_arr = $conn->query($sql_arr);
if($res_arr) while($row=$res_arr->fetch_assoc()) { $idx=$row['m']-1; if($idx>=0 && $idx<12) $arrets_data[$idx]=$row['c']; }

$conn->close();
?>

<div class="content-wrapper">
    <section class="content" style="background-color:white; padding-top: 18px;">
        <div class="content-dashboard">
            <div class="dashboard-hero">
                <div class="hero-top">
                    <h2 class="hero-title"><i class="fa fa-tachometer"></i> Pilotage Global</h2>
                  <!--  <div class="hero-metrics">
                        <div class="hero-chip"><span class="k"><?php echo number_format($nb_trans,0,',',' '); ?></span><span class="l">Transactions</span></div>
                        <div class="hero-chip"><span class="k"><?php echo number_format($montant_revenus,0,',',' '); ?></span><span class="l">Revenus FCFA</span></div>
                        <div class="hero-chip"><span class="k"><?php echo number_format($total_employes,0,',',' '); ?></span><span class="l">Employés</span></div>
                    </div>-->
                </div>
                <!--<p class="hero-subtitle">Vue consolidée de la trésorerie, de la performance commerciale et des ressources humaines.</p>-->
            </div>

        <!-- Filtre période -->
        <div class="row mb-3">
            <?php if ($this->rbac->hasPrivilege('fees_collection_and_expense_monthly_chart', 'can_view')) : ?>
                <div class="col-md-12">
                    <div class="dashboard-card filter-shell">
                        <div class="row align-items-center">
                            <div class="col-md-6"><div class="period-chip"><i class="fa fa-calendar"></i> <?php echo $period_label; ?></div></div>
                            <div class="col-md-6">
                                <form method="GET" class="form-inline dashboard-filter-form">
                                    <div class="date-range-group">
                                        <input type="date" name="date_debut" class="form-control" value="<?php echo htmlspecialchars($date_debut); ?>">
                                        <span>à</span>
                                        <input type="date" name="date_fin" class="form-control" value="<?php echo htmlspecialchars($date_fin); ?>">
                                        <div class="filter-actions">
                                            <button type="submit" class="btn btn-primary btn-export"><i class="fa fa-filter"></i> Filtrer</button>
                                            <button type="button" id="exportExcelBtn" class="btn btn-success btn-export"><i class="fa fa-file-excel-o"></i> Excel</button>
                                        </div>
                                    </div>
                                </form>           
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- ==================== SECTION TRÉSORERIE ==================== -->
        <div class="section-title"><i class="fa fa-university"></i> Trésorerie</div>
        <div class="row stat-row">
            <?php if ($this->rbac->hasPrivilege('Monthly fees_collection_widget', 'can_view')) : ?>
                <div class="col-lg-3 col-md-6"><div class="stat-card"><div class="stat-icon" style="background:#E8F5E9;"><i class="fa fa-money icon-green"></i></div><div class="stat-number"><?php echo number_format($revenus_ops,0,',',' '); ?> FCFA</div><div class="stat-text">Recettes totales</div></div></div>
            <?php endif; ?>
            <?php if ($this->rbac->hasPrivilege('fees_collection_and_expense_monthly_chart', 'can_view')) : ?>
                <div class="col-lg-3 col-md-6"><div class="stat-card"><div class="stat-icon" style="background:#FFEBEE;"><i class="fa fa-credit-card icon-red"></i></div><div class="stat-number"><?php echo number_format($depenses_ops,0,',',' '); ?> FCFA</div><div class="stat-text">Sorties de caisse</div></div></div>
            <?php endif; ?>
            <?php if ($this->rbac->hasPrivilege('Monthly fees_collection_widget', 'can_view')) : ?>
                <div class="col-lg-3 col-md-6"><div class="stat-card"><div class="stat-icon" style="background:#E3F2FD;"><i class="fa fa-balance-scale icon-blue"></i></div><div class="stat-number"><?php echo number_format($solde_actuel,0,',',' '); ?> FCFA</div><div class="stat-text">Solde actuel</div></div></div>
                <div class="col-lg-3 col-md-6"><div class="stat-card"><div class="stat-icon" style="background:#FFF3E0;"><i class="fa fa-exchange icon-orange"></i></div><div class="stat-number"><?php echo $nb_trans; ?></div><div class="stat-text">Transactions</div></div></div>
            <?php endif; ?>
        </div>

        <!-- ==================== SECTION COMPTABILITÉ ==================== -->
        <div class="section-title"><i class="fa fa-calculator"></i> Comptabilité</div>
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('fees_collection_and_expense_monthly_chart', 'can_view')) : ?>
                <div class="col-lg-8"><div class="dashboard-card"><div class="card-header"><h3><i class="fa fa-bar-chart icon-blue"></i> Revenus vs Dépenses</h3></div><div class="chart-container"><canvas id="revExpChart"></canvas></div></div></div>
                <div class="col-lg-4">
                    <div class="dashboard-card">
                        <div class="card-header"><h3><i class="fa fa-pie-chart icon-purple"></i> Dépenses par catégorie</h3></div>
                        <div class="chart-container chic-donut-container" style="position: relative;">
                            <canvas id="chicDonutChart" width="260" height="200"></canvas>
                            <div class="donut-center-text">
                                <div class="total-label">Total dépenses</div>
                                <div class="total-amount"><?php echo number_format($total_expenses,0,',',' '); ?></div>
                                <div class="total-currency">FCFA</div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- ==================== SECTION COMMERCIAL ==================== -->
        <div class="section-title"><i class="fa fa-handshake-o"></i> Commercial</div>

        <div class="sub-section-title"><i class="fa fa-arrow-up"></i> Ventes</div>
        <div class="row stat-row">
            <div class="col-md-4"><div class="stat-card"><div class="stat-icon" style="background:#E8F5E9;"><i class="fa fa-money icon-green"></i></div><div class="stat-number"><?php echo number_format($total_ventes,0,',',' '); ?> FCFA</div><div class="stat-text">Chiffre d'affaires</div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="stat-icon" style="background:#E3F2FD;"><i class="fa fa-file-text icon-blue"></i></div><div class="stat-number"><?php echo $nb_ventes; ?></div><div class="stat-text">Factures émises</div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="stat-icon" style="background:#FFEBEE;"><i class="fa fa-clock-o icon-red"></i></div><div class="stat-number"><?php echo number_format($total_impaye_ventes,0,',',' '); ?> FCFA</div><div class="stat-text">Créances impayées</div></div></div>
        </div>

        <div class="sub-section-title"><i class="fa fa-arrow-down"></i> Achats</div>
        <div class="row stat-row">
            <div class="col-md-4"><div class="stat-card"><div class="stat-icon" style="background:#FFF3E0;"><i class="fa fa-arrow-down icon-orange"></i></div><div class="stat-number"><?php echo number_format($total_achats,0,',',' '); ?> FCFA</div><div class="stat-text">Total achats</div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="stat-icon" style="background:#E0F7FA;"><i class="fa fa-file-text-o icon-teal"></i></div><div class="stat-number"><?php echo $nb_achats; ?></div><div class="stat-text">Factures reçues</div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="stat-icon" style="background:#FFEBEE;"><i class="fa fa-exclamation-triangle icon-red"></i></div><div class="stat-number"><?php echo number_format($total_impaye_achats,0,',',' '); ?> FCFA</div><div class="stat-text">Dettes fournisseurs</div></div></div>
        </div>

        <!-- ==================== SECTION RESSOURCE HUMAINE ==================== -->
        <div class="section-title"><i class="fa fa-users"></i> <span style="white-space: nowrap;">Ressources humaines</span></div>
        <div class="row stat-row">
            <?php if ($this->rbac->hasPrivilege('staff_present_today_widegts', 'can_view')) : ?>
                <div class="col-lg-3"><div class="stat-card"><div class="stat-icon" style="background:#E8EAF6;"><i class="fa fa-users icon-purple"></i></div><div class="stat-number"><?php echo $total_employes; ?></div><div class="stat-text">Effectif total</div></div></div>
                <div class="col-lg-3"><div class="stat-card"><div class="stat-icon" style="background:#E0F7FA;"><i class="fa fa-calendar icon-teal"></i></div><div class="stat-number"><?php echo $mediane_age; ?> ans</div><div class="stat-text">Âge médian</div></div></div>
                <div class="col-lg-3"><div class="stat-card"><div class="stat-icon" style="background:#FFF8E1;"><i class="fa fa-clock-o icon-orange"></i></div><div class="stat-number"><?php echo $anciennete_moyenne; ?> ans</div><div class="stat-text">Ancienneté moyenne</div></div></div>
                <div class="col-lg-3"><div class="stat-card"><div class="stat-icon" style="background:#E8F5E9;"><i class="fa fa-money icon-green"></i></div><div class="stat-number"><?php echo number_format($salaire_net_moyen,0,',',' '); ?> FCFA</div><div class="stat-text">Salaire net moyen</div></div></div>
            <?php endif; ?>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="dashboard-card">
                    <div class="card-header"><h3><i class="fa fa-bar-chart icon-blue"></i> Pyramide des âges (effectifs par tranche)</h3></div>
                    <div class="chart-container pyramid-container">
                        <canvas id="pyramidChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
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
        </div>

        <div class="row">
            <div class="col-lg-4"><div class="dashboard-card"><div class="card-header"><h3><i class="fa fa-file-text icon-orange"></i> Contrats</h3></div><div class="chart-container"><canvas id="contratChart"></canvas></div></div></div>
            <div class="col-lg-4"><div class="dashboard-card"><div class="card-header"><h3><i class="fa fa-flag icon-blue"></i> Nationalité</h3></div><div class="chart-container"><canvas id="nationaliteChart"></canvas></div></div></div>
            <div class="col-lg-4"><div class="dashboard-card"><div class="card-header"><h3><i class="fa fa-briefcase icon-purple"></i> Catégorie professionnelle</h3></div><div class="chart-container"><canvas id="catproChart"></canvas></div></div></div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="dashboard-card">
                    <div class="card-header"><h3><i class="fa fa-building icon-teal"></i> Salaire net moyen par département</h3></div>
                    <div class="chart-container" style="height: 350px; position: relative;">
                        <canvas id="salaryDeptChart" style="height: 100%; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="dashboard-card">
                    <div class="card-header"><h3><i class="fa fa-calendar icon-red"></i> Évolution congés, absences, arrêts - <?php echo $selected_year; ?></h3></div>
                    <div class="chart-container" style="height: 350px; position: relative;">
                        <canvas id="leaveChart" style="height: 100%; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </section>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    // Données PHP vers JS
    var labelsGraph = <?php echo json_encode($labels_graph); ?>;
    var revenusGraph = <?php echo json_encode($rev_graph); ?>;
    var depensesGraph = <?php echo json_encode($dep_graph); ?>;
    var labelsCat = <?php echo json_encode($labels_cat); ?>;
    var dataCat = <?php echo json_encode($data_cat); ?>;
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

    // Graphique revenus vs dépenses
    new Chart(document.getElementById('revExpChart'), {
        type: 'bar', data: { labels: labelsGraph, datasets: [
                { label: 'Revenus', backgroundColor: 'rgba(76,175,80,0.6)', data: revenusGraph },
                { label: 'Dépenses', backgroundColor: 'rgba(244,67,54,0.6)', data: depensesGraph }
            ]}, options: { responsive: true, maintainAspectRatio: false, scales: { yAxes: [{ ticks: { callback: v=>v.toLocaleString()+' FCFA' } }] } }
    });

    // Donut dépenses par catégorie
    var ctxDonut = document.getElementById('chicDonutChart').getContext('2d');
    new Chart(ctxDonut, {
        type: 'doughnut',
        data: { labels: labelsCat, datasets: [{ data: dataCat, backgroundColor: ['#273772','#D32F2F','#388E3C','#FBC02D','#7B1FA2','#F57C00','#0288D1','#C2185B','#009688','#607D8B'], borderWidth: 0, hoverOffset: 8 }] },
        options: { responsive: true, maintainAspectRatio: true, cutoutPercentage: 70, legend: { display: false }, tooltips: { callbacks: { label: function(t,i) { var d = i.datasets[t.datasetIndex]; var total = d.data.reduce((a,b)=>a+b,0); var val = d.data[t.index]; var pct = ((val/total)*100).toFixed(1); return i.labels[t.index]+': '+val.toLocaleString()+' FCFA ('+pct+'%)'; } } }, animation: { animateRotate: true, animateScale: true, duration: 1200, easing: 'easeOutCubic' } }
    });

    // Pyramide des âges
    new Chart(document.getElementById('pyramidChart'), {
        type: 'horizontalBar', data: { labels: pyramidLabels, datasets: [
                { label: 'Hommes', data: pyramidHommes, backgroundColor: '#273772', borderWidth: 0 },
                { label: 'Femmes', data: pyramidFemmes.map(v=>-v), backgroundColor: '#EC4899', borderWidth: 0 }
            ]}, options: { responsive: true, maintainAspectRatio: false, scales: { xAxes: [{ ticks: { callback: v=>Math.abs(v), stepSize: 1 } }], yAxes: [{ stacked: true }] }, tooltips: { callbacks: { label: (item,data)=>data.datasets[item.datasetIndex].label+': '+Math.abs(item.xLabel) } } }
    });

    // Turnover
    new Chart(document.getElementById('turnoverChart'), { type: 'line', data: { labels: moisLabels, datasets: [{ label: 'Sorties', data: turnoverMois, borderColor: '#F97316', fill: false }] }, options: { responsive: true } });
    // Contrats
    new Chart(document.getElementById('contratChart'), { type: 'pie', data: { labels: contratLabels, datasets: [{ data: contratValues, backgroundColor: ['#FADF2F','#9C27B0','#273772','#607D8B','#795548'] }] } });
    // Nationalités
    if(natLabels.length) new Chart(document.getElementById('nationaliteChart'), { type: 'pie', data: { labels: natLabels, datasets: [{ data: natValues, backgroundColor: '#10B981' }] } });
    else document.getElementById('nationaliteChart').parentNode.innerHTML = '<div class="alert alert-info">Aucune nationalité renseignée</div>';
    // Catégorie pro
    if(catproLabels.length) new Chart(document.getElementById('catproChart'), { type: 'bar', data: { labels: catproLabels, datasets: [{ label: 'Effectif', data: catproValues, backgroundColor: '#8B5CF6' }] }, options: { responsive: true } });
    // Évolution salaire
    if(evolPeriodes.length) new Chart(document.getElementById('evolSalaryChart'), { type: 'line', data: { labels: evolPeriodes, datasets: [{ label: 'Salaire net moyen (FCFA)', data: evolSalaires, borderColor: '#10B981', fill: false }] }, options: { responsive: true, scales: { yAxes: [{ ticks: { callback: v=>v.toLocaleString() } }] } } });
    // Salaire par département
    new Chart(document.getElementById('salaryDeptChart'), { type: 'bar', data: { labels: deptNames, datasets: [{ label: 'Salaire net moyen (FCFA)', data: deptSalMoy, backgroundColor: '#F59E0B' }] }, options: { responsive: true, scales: { yAxes: [{ ticks: { callback: v=>v.toLocaleString() } }] } } });
    // Congés/absences/arrêts
    new Chart(document.getElementById('leaveChart'), { type: 'line', data: { labels: moisLabels, datasets: [
                { label: 'Congés', data: congesData, borderColor: '#273772', fill: false },
                { label: 'Absences', data: absencesData, borderColor: '#F44336', fill: false },
                { label: 'Arrêts', data: arretsData, borderColor: '#FF9800', fill: false }
            ] }, options: { responsive: true, maintainAspectRatio: false, scales: { yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }] } } });

    // Export Excel
    document.getElementById('exportExcelBtn').addEventListener('click', function(){
        var wb = XLSX.utils.book_new();
        var dataSyn = [
            ['Indicateur','Valeur'],
            ['Période','<?php echo addslashes($period_label); ?>'],
            ['Revenus',<?php echo $montant_revenus; ?>],
            ['Dépenses',<?php echo $total_expenses; ?>],
            ['Solde',<?php echo $solde_actuel; ?>],
            ['Transactions',<?php echo $nb_trans; ?>],
            ['Ventes (CA)',<?php echo $total_ventes; ?>],
            ['Factures ventes',<?php echo $nb_ventes; ?>],
            ['Créances impayées',<?php echo $total_impaye_ventes; ?>],
            ['Achats',<?php echo $total_achats; ?>],
            ['Factures achats',<?php echo $nb_achats; ?>],
            ['Dettes fournisseurs',<?php echo $total_impaye_achats; ?>],
            ['Effectif total',<?php echo $total_employes; ?>],
            ['Âge médian',<?php echo $mediane_age; ?>],
            ['Ancienneté moyenne',<?php echo $anciennete_moyenne; ?>],
            ['Salaire net moyen',<?php echo $salaire_net_moyen; ?>]
        ];
        XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(dataSyn), 'Synthèse');
        var dataPyramid = [['Tranche','Hommes','Femmes']]; pyramidLabels.forEach((l,i)=>dataPyramid.push([l,pyramidHommes[i],pyramidFemmes[i]]));
        XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(dataPyramid), 'Pyramide âges');
        var dataTurn = [['Mois','Sorties']]; moisLabels.forEach((m,i)=>dataTurn.push([m,turnoverMois[i]]));
        XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(dataTurn), 'Turnover');
        var dataLeave = [['Mois','Congés','Absences','Arrêts']]; moisLabels.forEach((m,i)=>dataLeave.push([m,congesData[i],absencesData[i],arretsData[i]]));
        XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(dataLeave), 'Congés-Absences-Arrêts');
        XLSX.writeFile(wb, 'dashboard_complet_<?php echo date('Y-m-d'); ?>.xlsx');
    });
</script>