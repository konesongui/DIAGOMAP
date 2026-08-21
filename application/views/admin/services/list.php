<?php
$formID = 'serviceForm';
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<style>
    :root{
        --pf-ink:#273772;
        --pf-ink-soft:#334391;
        --pf-paper:#F6F7FA;
        --pf-card:#FFFFFF;
        --pf-line:#E3E6ED;
        --pf-green:#1F7A54;
        --pf-green-soft:#E1F3EA;
        --pf-red:#B23A32;
        --pf-red-soft:#FBE7E5;
        --pf-blue:#2D5FA6;
        --pf-blue-soft:#E5EDF9;
        --pf-amber:#B8892B;
        --pf-amber-soft:#FBF3E1;
        --pf-muted:#6B7793;
        --pf-sky: #2e9cdb;
        --pf-sky-dark:#227FB3;
        --font-display:'Sora', sans-serif;
        --font-body:'Inter', sans-serif;
        --font-mono:'JetBrains Mono', monospace;
    }

    .content-wrapper{ background:var(--pf-paper); font-family: var(--font-body); }
    .content-wrapper i.fa,
    .content-wrapper i[class*="fa-"] {
        font-family: "FontAwesome" !important;
        font-style: normal;
        font-weight: normal;
        speak: none;
    }
    .modal i.fa, .modal i[class*="fa-"]{ font-family:"FontAwesome" !important; font-style:normal; font-weight:normal; }

    /* ---- Header banner ---- */
    .content-header{
        position:relative;
        overflow:hidden;
        background:linear-gradient(135deg, var(--pf-ink) 0%, var(--pf-ink-soft) 100%);
        border-radius:14px;
        padding:26px 30px !important;
        margin:18px 18px 16px 18px;
        color:#fff;
    }
    .content-header::after{
        content:"";
        position:absolute;
        right:-40px; top:-40px;
        width:220px; height:220px;
        border:1.5px dashed rgba(255,255,255,0.14);
        border-radius:50%;
    }
    .content-header h1{
        font-family:var(--font-display);
        font-weight:700;
        font-size:24px;
        margin:0;
        letter-spacing:-0.01em;
        display:flex;
        align-items:center;
        gap:12px;
    }
    .content-header h1 i{
        width:38px; height:38px;
        border-radius:10px;
        background:rgba(255,255,255,0.10);
        border:1px solid rgba(255,255,255,0.16);
        display:inline-flex;
        align-items:center;
        justify-content:center;
        font-size:16px;
        color:#EAD9A8;
    }

    section.content{ padding-top:6px; }

    /* ---- Card ---- */
    .box.box-primary{
        border-top:none;
        border:1px solid var(--pf-line);
        border-radius:14px;
        box-shadow:0 1px 2px rgba(22,35,63,0.04);
        overflow:hidden;
    }
    .box.box-primary > .box-header.with-border{
        position:relative;

        background-color: #273772;
        border-bottom:1px solid var(--pf-line);
        padding:16px 20px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        flex-wrap:wrap;
        gap:10px;
        min-height:0;
    }
    .box.box-primary > .box-header .box-title{
        font-family:var(--font-display);
        font-weight:600;
        font-size:16px;
        color:white;
        float:none;
        margin:0;
    }
    .box.box-primary > .box-body{ padding:20px; background:var(--pf-card); }

    /* AdminLTE positions .box-tools absolutely by default, which is what pushes
       the button out of flow and up near the banner. Force it back into the
       normal flex flow so it sits on the same row as the title. */
    .box.box-primary > .box-header .box-tools{
        position:static !important;
        top:auto !important;
        right:auto !important;
        float:none !important;
        display:flex !important;
        align-items:center;
        margin-left:auto;
    }

    .box-tools .btn-primary{
        height:36px;
        display:inline-flex;
        align-items:center;
        gap:7px;
        padding:0 16px;
        border-radius:9px;
        font-size:13px;
        font-weight:600;
        background:white;
        border-color:var(--pf-sky) !important;
        color:#273772;
    }
    /*.box-tools .btn-primary:hover{ background:var(--pf-sky-dark) !important; border-color:var(--pf-sky-dark) !important; box-shadow:0 4px 12px rgba(46,156,219,0.3); }*/

    /* ---- DataTables buttons (Excel/PDF/Print) — bleu ciel uniforme ---- */
    .dt-buttons{ margin-bottom:15px; display:flex; gap:8px; flex-wrap:wrap; }
    .dt-buttons .btn{
        margin-right:0 !important;
        border-radius:9px !important;
        font-size:12.5px !important;
        font-weight:600 !important;
        padding:7px 14px !important;
        border:1px solid transparent !important;
        background:var(--pf-sky) !important;
        border-color:var(--pf-sky) !important;
        color:#fff !important;
    }
    .dt-buttons .btn:hover{ background:var(--pf-sky-dark) !important; border-color:var(--pf-sky-dark) !important; }

    /* ---- Table ---- */
    /* border-collapse: collapse + a border on every cell (not just border-top)
       keeps the row separator continuous across every column, even ones with
       no data (e.g. an empty Description cell no longer breaks the line). */
    table#services-table{
        border-collapse:collapse !important;
        width:100%;
    }
    table#services-table thead th{
        background:var(--pf-paper) !important;
        border:1px solid var(--pf-line) !important;
        font-family:var(--font-display);
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.07em;
        color:var(--pf-muted);
        font-weight:600;
        padding:12px 16px;
    }
    table#services-table tbody td{
        padding:12px 16px;
        font-size:13.5px;
        color:var(--pf-ink);
        border:1px solid var(--pf-line) !important;
        vertical-align:middle;
    }
    table#services-table tbody td:empty::after{
        content:"\00a0";
    }
    table#services-table tbody tr:hover{ background:#FAFBFD; }
    table#services-table tbody tr td:first-child{
        font-family:var(--font-mono);
        font-weight:500;
        color:var(--pf-ink-soft);
    }

    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select{
        border:1px solid var(--pf-line);
        border-radius:8px;
        padding:5px 10px;
        font-size:13px;
    }
    .dataTables_wrapper .dataTables_filter input:focus,
    .dataTables_wrapper .dataTables_length select:focus{
        outline:none;
        border-color:var(--pf-ink);
        box-shadow:0 0 0 3px var(--pf-amber-soft);
    }
    .dataTables_wrapper .paginate_button.current,
    .dataTables_wrapper .paginate_button.current:hover{
        background:var(--pf-sky) !important;
        border-color:var(--pf-sky) !important;
        color:#fff !important;
        border-radius:7px;
    }
    .dataTables_wrapper .paginate_button{ border-radius:7px !important; }

    /* ---- Action dropdown ---- */
    .action-dropdown .dropdown-toggle{
        background:#fff;
        border:1px solid var(--pf-line);
        padding:5px 10px;
        border-radius:8px;
        color:var(--pf-ink);
    }
    .action-dropdown .dropdown-toggle:hover{ background-color:var(--pf-paper); border-color:var(--pf-ink); }
    .action-dropdown .dropdown-menu{
        min-width:150px;
        padding:6px 0;
        border:1px solid var(--pf-line);
        border-radius:10px;
        box-shadow:0 8px 24px rgba(22,35,63,0.12);
    }
    .action-dropdown .dropdown-menu li a{
        padding:7px 14px;
        font-size:13px;
        cursor:pointer;
        color:var(--pf-ink);
    }
    .action-dropdown .dropdown-menu li a:hover{ background:var(--pf-paper); }
    .action-dropdown .dropdown-menu li a i{ margin-right:6px; width:16px; }
    .action-dropdown .dropdown-menu li a.text-danger{ color:var(--pf-red); }
    .action-dropdown .dropdown-menu li a.text-danger:hover{ background-color:var(--pf-red-soft); color:#8E2C25; }

    .btn-soft{ padding:2px 8px; font-size:12px; }

    /* ---- Modal ---- */
    .modal-lg{ max-width:800px; }
    .modal-content{
        border:none;
        border-radius:14px;
        overflow:hidden;
        box-shadow:0 20px 60px rgba(22,35,63,0.2);
    }
    .modal-header{
        background:linear-gradient(135deg, var(--pf-ink) 0%, var(--pf-ink-soft) 100%);
        border-bottom:none;
        padding:18px 24px;
    }
    .modal-header .modal-title{
        font-family:var(--font-display);
        font-weight:600;
        font-size:17px;
        color:#fff;
    }
    .modal-header .close{
        color:#fff;
        opacity:.8;
        text-shadow:none;
        font-size:22px;
    }
    .modal-header .close:hover{ opacity:1; }
    .modal-body{ padding:24px; background:var(--pf-card); }
    .modal-body .form-group label{
        font-size:11.5px;
        font-weight:600;
        text-transform:uppercase;
        letter-spacing:.05em;
        color:var(--pf-muted);
        margin-bottom:6px;
    }
    .modal-body .form-control{
        border:1px solid var(--pf-line);
        border-radius:8px;
        box-shadow:none;
        font-size:13.5px;
        color:var(--pf-ink);
    }
    .modal-body .form-control:focus{
        border-color:var(--pf-ink);
        box-shadow:0 0 0 3px var(--pf-amber-soft);
    }
    .modal-footer{
        background:var(--pf-paper);
        border-top:1px solid var(--pf-line);
        padding:16px 24px;
    }
    .modal-footer .btn-default{
        border:1px solid var(--pf-line);
        background:#fff;
        color:var(--pf-ink);
        border-radius:9px;
        font-weight:600;
        padding:8px 18px;
    }
    .modal-footer .btn-default:hover{ border-color:var(--pf-ink); color:var(--pf-ink); background:var(--pf-paper); }
    .modal-footer .btn-primary{
        background:var(--pf-ink);
        border-color:var(--pf-ink);
        border-radius:9px;
        font-weight:600;
        padding:8px 20px;
    }
    .modal-footer .btn-primary:hover{ background:var(--pf-ink-soft); border-color:var(--pf-ink-soft); }

    @media print {
        .dataTables_wrapper .dt-buttons {
            display: none;
        }
    }

    @media (max-width: 991px){
        .content-header{ margin:12px; padding:20px !important; }
    }

    /* Style pour les champs de prix */
    #unit_price {
        font-family: var(--font-mono);
        text-align: right;
        font-size: 14px;
    }

    /* Style pour les cellules de prix dans le tableau */
    table#services-table tbody td:nth-child(4) {
        font-family: var(--font-mono);
        text-align: right;
    }

    /* ---- Dashboard KPI cards ---- */
    .pf-kpi-row{
        display:flex;
        gap:16px;
        margin:0 18px 16px 18px;
        flex-wrap:wrap;
    }
    .pf-kpi-card{
        flex:1 1 220px;
        background:var(--pf-card);
        border:1px solid var(--pf-line);
        border-radius:14px;
        padding:18px 20px;
        display:flex;
        align-items:center;
        gap:14px;
        box-shadow:0 1px 2px rgba(22,35,63,0.04);
    }
    .pf-kpi-icon{
        width:46px; height:46px;
        border-radius:12px;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:19px;
        flex-shrink:0;
    }
    .pf-kpi-icon.pf-icon-amount{ background:var(--pf-green-soft); color:var(--pf-green); }
    .pf-kpi-icon.pf-icon-count{ background:var(--pf-blue-soft); color:var(--pf-blue); }
    .pf-kpi-icon.pf-icon-avg{ background:var(--pf-amber-soft); color:var(--pf-amber); }
    .pf-kpi-label{
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:var(--pf-muted);
        font-weight:600;
        margin-bottom:4px;
    }
    .pf-kpi-value{
        font-family:var(--font-display);
        font-size:22px;
        font-weight:700;
        color:var(--pf-ink);
        line-height:1.1;
    }
    .pf-kpi-sub{ font-size:11.5px; color:var(--pf-muted); margin-top:3px; }

    /* ---- Filter bar (période) ---- */
    .pf-filter-bar{
        background:var(--pf-card);
        border:1px solid var(--pf-line);
        border-radius:14px;
        margin:0 18px 18px 18px;
        padding:14px 20px;
        display:flex;
        align-items:center;
        gap:10px;
        flex-wrap:wrap;
    }
    .pf-filter-label{
        font-size:12px;
        font-weight:600;
        color:var(--pf-muted);
        text-transform:uppercase;
        letter-spacing:.05em;
        margin-right:4px;
        display:flex;
        align-items:center;
        gap:6px;
    }
    .pf-period-btn{
        border:1px solid var(--pf-line);
        background:#fff;
        color:var(--pf-ink);
        border-radius:8px;
        padding:6px 14px;
        font-size:12.5px;
        font-weight:600;
        cursor:pointer;
        transition:all .15s;
    }
    .pf-period-btn:hover{ border-color:var(--pf-ink); }
    .pf-period-btn.active{
        background:var(--pf-ink);
        border-color:var(--pf-ink);
        color:#fff;
    }
    .pf-custom-range{
        display:none;
        align-items:center;
        gap:8px;
    }
    .pf-custom-range.show{ display:flex; }
    .pf-custom-range input[type=date]{
        border:1px solid var(--pf-line);
        border-radius:8px;
        padding:6px 10px;
        font-size:12.5px;
        color:var(--pf-ink);
    }
    .pf-custom-range .pf-apply-btn{
        background:var(--pf-sky);
        border-color:var(--pf-sky);
        color:#fff;
        border-radius:8px;
        padding:6px 14px;
        font-size:12.5px;
        font-weight:600;
        border:1px solid var(--pf-sky);
        cursor:pointer;
    }
    .pf-custom-range .pf-apply-btn:hover{ background:var(--pf-sky-dark); border-color:var(--pf-sky-dark); }

    @media (max-width: 600px){
        .pf-kpi-row{ margin:0 12px 12px 12px; }
        .pf-filter-bar{ margin:0 12px 12px 12px; }
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-cogs"></i> Services</h1>
    </section>

    <!-- Tableau de bord : montant global -->
    <div class="pf-kpi-row">
        <div class="pf-kpi-card">
            <div class="pf-kpi-icon pf-icon-amount"><i class="fa fa-money"></i></div>
            <div>
                <div class="pf-kpi-label">Montant global</div>
                <div class="pf-kpi-value" id="kpi-total-amount">0 FCFA</div>
                <div class="pf-kpi-sub" id="kpi-period-label">Toutes les périodes</div>
            </div>
        </div>
        <div class="pf-kpi-card">
            <div class="pf-kpi-icon pf-icon-count"><i class="fa fa-list-ul"></i></div>
            <div>
                <div class="pf-kpi-label">Services</div>
                <div class="pf-kpi-value" id="kpi-total-count">0</div>
                <div class="pf-kpi-sub">Nombre de services</div>
            </div>
        </div>
       <!-- <div class="pf-kpi-card">
            <div class="pf-kpi-icon pf-icon-avg"><i class="fa fa-calculator"></i></div>
            <div>
                <div class="pf-kpi-label">Prix moyen</div>
                <div class="pf-kpi-value" id="kpi-avg-amount">0 FCFA</div>
                <div class="pf-kpi-sub">Par service</div>
            </div>
        </div>-->
    </div>

    <!-- Filtre par période -->
    <div class="pf-filter-bar">
        <span class="pf-filter-label"><i class="fa fa-filter"></i> Période</span>
        <button type="button" class="pf-period-btn active" data-period="all">Tout</button>
        <button type="button" class="pf-period-btn" data-period="today">Aujourd'hui</button>
        <button type="button" class="pf-period-btn" data-period="week">7 derniers jours</button>
        <button type="button" class="pf-period-btn" data-period="month">Ce mois</button>
        <button type="button" class="pf-period-btn" data-period="year">Cette année</button>
        <button type="button" class="pf-period-btn" data-period="custom">Personnalisé</button>
        <div class="pf-custom-range" id="pf-custom-range">
            <input type="date" id="pf-date-start">
            <span style="color:var(--pf-muted);font-size:12px;">au</span>
            <input type="date" id="pf-date-end">
            <button type="button" class="pf-apply-btn" id="pf-apply-custom">Appliquer</button>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Liste des services</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-primary btn-sm" id="btn-add-service">
                                <i class="fa fa-plus"></i> Ajouter un service
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="services-table" width="100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th style="width:207px">Nom</th>
                                <th>Description</th>
                                <th>Prix unitaire (FCFA)</th>
                                <th>Durée</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Ajouter / Modifier -->
<div class="modal fade" id="serviceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Service</h4>
            </div>
            <form id="<?= $formID ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="service_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nom <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Prix unitaire (FCFA) <span class="text-danger">*</span></label>
                                <input type="text" inputmode="decimal" name="unit_price" id="unit_price" class="form-control" required autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="created_at" id="created_at" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Durée (ex: 2 heures, 1 jour)</label>
                                <input type="text" name="duration" id="duration" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Styles DataTables Buttons -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">

<!-- Scripts DataTables et extensions -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap.min.css">

<!-- Buttons extensions -->
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    var base_url = '<?= base_url() ?>';

    // Fonction de formatage des nombres avec séparateur de milliers (espaces)
    function formatNumberWithSpaces(number) {
        if (number === null || number === undefined || number === '') return '0';
        var num = parseFloat(number);
        if (isNaN(num)) return '0';
        return new Intl.NumberFormat('fr-FR').format(num);
    }

    // Fonction pour nettoyer un nombre formaté (enlever les espaces)
    function cleanFormattedNumber(formatted) {
        if (!formatted) return '';
        return formatted.replace(/\s/g, '');
    }

    // Fonction pour n'afficher que la date (jj/mm/aaaa), sans l'heure,
    // quel que soit le format renvoyé par le backend (ISO, "YYYY-MM-DD HH:MM:SS", etc.)
    function formatDateOnly(value) {
        if (!value) return '';
        var datePart = String(value).split(' ')[0].split('T')[0]; // "YYYY-MM-DD"
        var parts = datePart.split('-');
        if (parts.length === 3) {
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }
        var d = new Date(value);
        if (!isNaN(d.getTime())) {
            return d.toLocaleDateString('fr-FR');
        }
        return datePart;
    }

    // Fonction pour convertir une date reçue du backend au format attendu
    // par un <input type="date"> (toujours "YYYY-MM-DD")
    function toDateInputValue(value) {
        if (!value) return '';
        return String(value).split(' ')[0].split('T')[0];
    }

    // Fonction pour formater en temps réel dans un champ input
    function formatPriceInput(input) {
        // Récupérer la position du curseur
        var cursorPos = input.selectionStart;
        var rawValue = input.value.replace(/\s/g, '');

        // Si la valeur est vide ou contient seulement des chiffres et un point décimal
        if (rawValue === '' || /^[0-9.]*$/.test(rawValue)) {
            var parts = rawValue.split('.');
            var integerPart = parts[0];
            var decimalPart = parts.length > 1 ? '.' + parts[1] : '';

            // Formater la partie entière avec espaces
            if (integerPart) {
                integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
            }

            // Mettre à jour la valeur affichée
            input.value = integerPart + decimalPart;

            // Restaurer la position du curseur
            var newCursorPos = cursorPos + (input.value.replace(/\s/g, '').length - rawValue.length);
            if (newCursorPos >= 0 && newCursorPos <= input.value.length) {
                input.setSelectionRange(newCursorPos, newCursorPos);
            }
        }
    }

    // ---- Tableau de bord : filtre par période & KPI ----
    var currentPeriod = 'all';
    var customStart = null;
    var customEnd = null;

    // Adaptez "created_at" au nom réel du champ date renvoyé par ajax_list
    // (ex: date_creation, created_at...). Si ce champ n'existe pas côté
    // backend, le filtre par période n'aura aucun effet et "Tout" restera
    // affiché en permanence.
    var SERVICE_DATE_FIELD = 'created_at';

    function isInPeriod(dateStr, period) {
        if (period === 'all') return true;
        if (!dateStr) return false;
        var d = new Date(dateStr);
        if (isNaN(d.getTime())) return false;
        var now = new Date();

        if (period === 'today') {
            return d.toDateString() === now.toDateString();
        }
        if (period === 'week') {
            var weekAgo = new Date();
            weekAgo.setDate(now.getDate() - 7);
            return d >= weekAgo && d <= now;
        }
        if (period === 'month') {
            return d.getFullYear() === now.getFullYear() && d.getMonth() === now.getMonth();
        }
        if (period === 'year') {
            return d.getFullYear() === now.getFullYear();
        }
        if (period === 'custom') {
            if (!customStart || !customEnd) return true;
            var s = new Date(customStart);
            var e = new Date(customEnd);
            e.setHours(23, 59, 59, 999);
            return d >= s && d <= e;
        }
        return true;
    }

    function updateKPIs(rows) {
        var total = 0;
        rows.forEach(function(r) {
            total += parseFloat(r.unit_price) || 0;
        });
        var count = rows.length;
        var avg = count ? total / count : 0;

        $('#kpi-total-amount').text(formatNumberWithSpaces(total) + ' FCFA');
        $('#kpi-total-count').text(count);
        $('#kpi-avg-amount').text(formatNumberWithSpaces(Math.round(avg)) + ' FCFA');
    }

    $(document).ready(function() {
        var table = $('#services-table').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": base_url + "admin/services/ajax_list",
                "type": "GET",
                "dataSrc": function(json) {
                    var filtered = json.filter(function(row) {
                        return isInPeriod(row[SERVICE_DATE_FIELD], currentPeriod);
                    });
                    updateKPIs(filtered);
                    return filtered;
                }
            },
            "columns": [
                { "data": "id" },
                {
                    "data": "created_at",
                    "render": function(data) {
                        return formatDateOnly(data);
                    }
                },
                { "data": "name" },
                { "data": "description" },
                {
                    "data": "unit_price",
                    "render": function(data) {
                        if (data === null || data === undefined || data === '') {
                            return '0 FCFA';
                        }
                        return formatNumberWithSpaces(data) + ' FCFA';
                    }
                },
                { "data": "duration" },
                {
                    "data": null,
                    "orderable": false,
                    "render": function(data, type, row) {
                        return `
                            <div class="dropdown action-dropdown">
                                <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a class="btn-edit" data-id="${row.id}"><i class="fa fa-edit"></i> Modifier</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a class="btn-delete text-danger" data-id="${row.id}"><i class="fa fa-trash"></i> Supprimer</a></li>
                                </ul>
                            </div>
                        `;
                    }
                }
            ],
            "language": {
                "url": base_url + "assets/js/french.json"
            },
            "dom": 'Bfrtip',
            "buttons": [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Liste des services',
                    exportOptions: {
                        columns: [0,1,2,3,4]
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        var row = sheet.getElementsByTagName('row');
                        if(row.length) {
                            var firstRow = row[0];
                            var cells = firstRow.getElementsByTagName('c');
                            for(var i = 0; i < cells.length; i++) {
                                cells[i].setAttribute('s', '2');
                            }
                        }
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Liste des services',
                    orientation: 'portrait',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0,1,2,3,4]
                    },
                    customize: function(doc) {
                        doc.content.splice(0, 1);

                        var currentDate = new Date().toLocaleDateString('fr-FR');
                        doc.content.unshift({
                            text: 'Liste des services',
                            style: 'header',
                            margin: [0, 0, 0, 12]
                        });
                        doc.content.unshift({
                            text: 'Généré le ' + currentDate,
                            style: 'subheader',
                            margin: [0, 0, 0, 20],
                            alignment: 'right'
                        });

                        doc.styles = {
                            header: {
                                fontSize: 18,
                                bold: true,
                                alignment: 'center',
                                color: '#1a4472'
                            },
                            subheader: {
                                fontSize: 10,
                                italic: true,
                                color: '#666'
                            },
                            tableHeader: {
                                bold: true,
                                fontSize: 11,
                                color: 'white',
                                fillColor: '#3c8dbc',
                                alignment: 'center'
                            },
                            tableBody: {
                                fontSize: 10
                            }
                        };

                        var table = doc.content.find(c => c.table);
                        if (table) {
                            table.table.widths = ['8%', '22%', '35%', '18%', '17%'];
                            table.table.headerRows = 1;
                            table.layout = {
                                hLineWidth: function(i, node) { return 0.5; },
                                vLineWidth: function(i, node) { return 0.5; },
                                hLineColor: function(i, node) { return '#aaa'; },
                                vLineColor: function(i, node) { return '#aaa'; },
                                paddingLeft: function(i, node) { return 4; },
                                paddingRight: function(i, node) { return 4; },
                                fillColor: function(rowIndex, node, columnIndex) {
                                    return (rowIndex === 0) ? '#3c8dbc' : (rowIndex % 2 === 0 ? '#f9f9f9' : null);
                                }
                            };

                            if (table.table.body && table.table.body[0]) {
                                table.table.body[0].forEach(cell => {
                                    cell.style = 'tableHeader';
                                });
                            }

                            if (table.table.body) {
                                for (var i = 1; i < table.table.body.length; i++) {
                                    if (table.table.body[i][3]) {
                                        // Formater le prix avec séparateur de milliers pour le PDF
                                        var priceText = table.table.body[i][3].text || '';
                                        var priceNum = parseFloat(priceText.replace(/[^0-9.]/g, ''));
                                        if (!isNaN(priceNum)) {
                                            table.table.body[i][3].text = formatNumberWithSpaces(priceNum) + ' FCFA';
                                        }
                                        table.table.body[i][3].alignment = 'right';
                                    }
                                    if (table.table.body[i][4]) table.table.body[i][4].alignment = 'center';
                                }
                            }
                        }
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Imprimer',
                    className: 'btn btn-info btn-sm',
                    exportOptions: {
                        columns: [0,1,2,3,4]
                    }
                }
            ]
        });

        // Boutons de période prédéfinie
        var periodLabels = {
            all: 'Toutes les périodes',
            today: "Aujourd'hui",
            week: '7 derniers jours',
            month: 'Ce mois',
            year: 'Cette année'
        };

        $('.pf-period-btn').on('click', function() {
            var period = $(this).data('period');
            $('.pf-period-btn').removeClass('active');
            $(this).addClass('active');

            if (period === 'custom') {
                $('#pf-custom-range').addClass('show');
                return; // on attend le clic sur "Appliquer"
            }

            $('#pf-custom-range').removeClass('show');
            currentPeriod = period;
            $('#kpi-period-label').text(periodLabels[period] || 'Toutes les périodes');
            table.ajax.reload();
        });

        // Filtre personnalisé
        $('#pf-apply-custom').on('click', function() {
            customStart = $('#pf-date-start').val();
            customEnd = $('#pf-date-end').val();
            if (!customStart || !customEnd) {
                Swal.fire('Attention', 'Veuillez sélectionner une date de début et une date de fin', 'warning');
                return;
            }
            if (customStart > customEnd) {
                Swal.fire('Attention', 'La date de début doit précéder la date de fin', 'warning');
                return;
            }
            currentPeriod = 'custom';
            $('#kpi-period-label').text('Du ' + customStart + ' au ' + customEnd);
            table.ajax.reload();
        });

        // Ouvrir modal pour ajouter
        $('#btn-add-service').click(function() {
            $('#service_id').val('');
            var todayStr = new Date().toISOString().split('T')[0];
            $('#created_at').val(todayStr);
            $('#name').val('');
            $('#description').val('');
            $('#unit_price').val('');
            $('#duration').val('');
            $('#serviceModal .modal-title').text('Ajouter un service');
            $('#serviceModal').modal('show');
        });

        // Ouvrir modal pour modifier
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            $.get(base_url + 'admin/services/ajax_edit/' + id, function(data) {
                $('#service_id').val(data.id);
                $('#created_at').val(toDateInputValue(data.created_at));
                $('#name').val(data.name);
                $('#description').val(data.description);
                // Formater le prix avec séparateur de milliers
                if (data.unit_price) {
                    $('#unit_price').val(formatNumberWithSpaces(data.unit_price));
                } else {
                    $('#unit_price').val('');
                }
                $('#duration').val(data.duration);
                $('#serviceModal .modal-title').text('Modifier le service');
                $('#serviceModal').modal('show');
            }, 'json');
        });

        // Gestion du formatage du prix dans le champ de saisie - événement input
        $('#unit_price').on('input', function() {
            formatPriceInput(this);
        });

        // Gestion du formatage du prix - événement keydown pour gérer les touches spéciales
        $('#unit_price').on('keydown', function(e) {
            // Autoriser les touches de contrôle, flèches, etc.
            var key = e.key;
            if (['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Tab', 'Home', 'End', 'Enter'].includes(key)) {
                return;
            }
            // Autoriser les chiffres et le point décimal
            if (!/^[0-9.]$/.test(key) && key !== ' ') {
                e.preventDefault();
            }
            // Empêcher plusieurs points décimaux
            if (key === '.') {
                var currentValue = e.target.value.replace(/\s/g, '');
                if (currentValue.includes('.')) {
                    e.preventDefault();
                }
            }
        });

        // Avant de soumettre le formulaire, nettoyer la valeur
        $('#serviceForm').on('submit', function(e) {
            e.preventDefault();
            var priceInput = $('#unit_price');
            // Enlever les espaces avant l'envoi
            var cleanValue = cleanFormattedNumber(priceInput.val());
            if (cleanValue === '' || isNaN(parseFloat(cleanValue))) {
                Swal.fire('Erreur', 'Veuillez saisir un montant valide', 'error');
                return;
            }
            priceInput.val(cleanValue);

            var id = $('#service_id').val();
            var url = id ? base_url + 'admin/services/ajax_update' : base_url + 'admin/services/ajax_add';
            var formData = $(this).serialize();

            $.post(url, formData, function(response) {
                if (response.status === 'success') {
                    Swal.fire('Succès', response.message, 'success');
                    $('#serviceModal').modal('hide');
                    table.ajax.reload();
                } else {
                    Swal.fire('Erreur', response.message, 'error');
                }
            }, 'json');
        });

        // Suppression
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Confirmation',
                text: 'Voulez-vous vraiment supprimer ce service ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(base_url + 'admin/services/ajax_delete/' + id, function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Succès', response.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Erreur', response.message, 'error');
                        }
                    }, 'json');
                }
            });
        });

        // Nettoyer le champ de prix à la fermeture du modal
        $('#serviceModal').on('hidden.bs.modal', function() {
            // Remettre le format si une valeur existe
            var priceVal = $('#unit_price').val();
            if (priceVal && !isNaN(parseFloat(priceVal.replace(/\s/g, '')))) {
                $('#unit_price').val(formatNumberWithSpaces(priceVal.replace(/\s/g, '')));
            }
        });
    });
</script>