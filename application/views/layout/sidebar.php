<?php

// Récupérer l'état de l'installation
$is_installed = false;
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($user_agent, 'Diagoma') !== false) {
        $is_installed = true;
    }
}

$file   = "";
$result = $this->customlib->getUserData();

$image = $result["image"];
$role  = $result["user_type"];
$id    = $result["id"];
$admin_session = $this->session->userdata('admin');
$can_manage_succursales = !empty($admin_session['can_manage_succursales']) && (int) $admin_session['can_manage_succursales'] === 1;
$is_head_office = empty($admin_session['type_structure']) || $admin_session['type_structure'] === 'siege';
// Note: $com_active n'était défini nulle part dans le fichier d'origine (toujours "undefined").
// On le déclare ici à vide pour conserver EXACTEMENT le même comportement d'affichage
// tout en évitant les notices PHP "undefined variable".
$com_active = '';

if (!empty($image)) {
    $file = "uploads/staff_images/" . $image;
} else {
    if ($result['gender'] == 'Female') {
        $file = "uploads/staff_images/default_female.jpg";
    } else {
        $file = "uploads/staff_images/default_male.jpg";
    }
}
?>

<style>
    /* ==========================================
       THEME SIDEBAR — palette unifiée
       ========================================== */
    :root {
        --sb-bg-1: #f8fbff;
        --sb-bg-2: #eef4ff;
        --sb-line: #d8e4f4;
        --sb-primary: #1b4f80;
        --sb-primary-dark: #143a5f;
        --sb-primary-soft: #e8f2ff;
        --sb-text: #1c2e43;
        --sb-muted: #6f8199;
        --sb-active: #1b4f80;
        --sb-accent: #10B981;
    }

    .family { font-family: Roboto-Bold; }

    /* ===== Conteneur sidebar ===== */
    .main-sidebar {
        background: linear-gradient(180deg, var(--sb-bg-1) 0%, var(--sb-bg-2) 100%) !important;
        border-right: 1px solid var(--sb-line);
        box-shadow: 6px 0 18px rgba(18, 53, 87, 0.08);
    }

    .main-sidebar,
    .main-sidebar a,
    .main-sidebar .treeview-menu li a,
    .main-sidebar .sidebar-menu > li > a {
        color: var(--sb-text) !important;
        font-weight: 500;
    }

    .sidebar { padding-top: 6px; }
    .sidebar-menu { padding: 0 8px 12px; }

    /* ===== Barre de recherche ===== */
    .search-form2 {
        background: #FFFFFF;
        padding: 10px;
        border-radius: 12px;
        margin: 10px;
        border: 1px solid #d6e4f5;
        box-shadow: 0 6px 12px rgba(20, 62, 104, 0.06);
    }

    .search-form2 .form-control {
        background: #FFFFFF;
        border: 1px solid #d3e2f3;
        color: var(--sb-text);
        border-radius: 30px 0 0 30px;
        height: 36px;
    }

    .search-form2 .form-control:focus {
        border-color: var(--sb-primary);
        box-shadow: 0 0 0 2px rgba(27, 79, 128, 0.12);
        outline: none;
    }

    .search-form2 .input-group-btn .btn {
        background: linear-gradient(135deg, var(--sb-primary) 0%, #2d6ea8 100%) !important;
        color: #fff !important;
        border: 1px solid var(--sb-primary) !important;
        border-radius: 0 30px 30px 0 !important;
        height: 36px;
        padding: 0 16px;
    }

    .search-form2 .input-group-btn .btn:hover {
        filter: brightness(0.92);
    }

    /* ===== En-tête de section (sidebar-header) ===== */
    .sidebar-header {
        color: var(--sb-muted) !important;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #dfe9f7;
        margin: 4px 10px 8px;
        padding: 12px 0 8px 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .sidebar-header i { font-size: 12px; color: #94a3b8; }

    /* ===== Éléments de menu principal ===== */
    .sidebar-menu > li {
        margin-bottom: 4px;
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .sidebar-menu > li > a {
        border-radius: 10px;
        padding: 8px 12px;
        color: var(--sb-text) !important;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
    }

    .sidebar-menu > li > a:hover {
        background: #ffffff !important;
        border-color: #d9e7f8 !important;
        color: var(--sb-primary) !important;
        box-shadow: 0 4px 10px rgba(24, 76, 122, 0.08);
    }

    .sidebar-menu > li > a:hover i,
    .sidebar-menu > li > a:hover .ftlayer {
        color: var(--sb-primary) !important;
        -webkit-text-fill-color: var(--sb-primary) !important;
    }

    /* ===== Élément actif (lien direct ou avec sous-menu) ===== */
    .sidebar-menu > li.active > a,
    .sidebar-menu > li.active > a[href]:not([href="#"]) {
        background: linear-gradient(135deg, var(--sb-active) 0%, #2f79b8 100%) !important;
        border-color: transparent !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        box-shadow: 0 8px 16px rgba(31, 95, 150, 0.22);
    }

    .sidebar-menu > li.active > a i,
    .sidebar-menu > li.active > a .ftlayer,
    .sidebar-menu > li.active > a span,
    .sidebar-menu > li.active > a .fa-angle-left,
    .sidebar-menu > li.active > a[href]:not([href="#"]) i,
    .sidebar-menu > li.active > a[href]:not([href="#"]) span {
        color: #ffffff !important;
        -webkit-text-fill-color: #ffffff !important;
    }

    /* ===== Sous-menu ouvert (treeview) ===== */
    .sidebar-menu .treeview.menu-open > a {
        background: var(--sb-primary-soft) !important;
        border-left: 4px solid var(--sb-primary) !important;
        color: var(--sb-primary) !important;
        font-weight: 600 !important;
    }

    .sidebar-menu .treeview.menu-open > a i,
    .sidebar-menu .treeview.menu-open > a .ftlayer,
    .sidebar-menu .treeview.menu-open > a span {
        color: var(--sb-primary) !important;
        -webkit-text-fill-color: var(--sb-primary) !important;
    }

    /* ===== Libellés du menu principal — typographie premium ===== */
    .sidebar-menu > li > a span:not(.pull-right-container) {
        font-size: 13.5px;
        font-weight: 700;
        letter-spacing: 0.3px;
        color: var(--sb-text);
        transition: color 0.2s ease, letter-spacing 0.2s ease;
    }

    .sidebar-menu > li > a:hover span:not(.pull-right-container) {
        color: var(--sb-primary);
        letter-spacing: 0.5px;
    }

    .sidebar-menu > li.active > a span:not(.pull-right-container) {
        color: #ffffff !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.18);
        letter-spacing: 0.4px;
    }

    .sidebar-menu .treeview.menu-open > a span:not(.pull-right-container) {
        color: var(--sb-primary) !important;
        letter-spacing: 0.4px;
    }

    /* Libellés des sous-menus : plus sobres, hiérarchie claire */
    .treeview-menu li a {
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: 0.1px;
    }
    .menu-icon-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        min-width: 30px;
        border-radius: 9px;
        margin-right: 12px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.18), inset 0 1px 0 rgba(255,255,255,0.25);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .menu-icon-badge i {
        color: #ffffff !important;
        -webkit-text-fill-color: #ffffff !important;
        font-size: 14px;
        width: auto !important;
    }

    .sidebar-menu > li > a:hover .menu-icon-badge,
    .treeview.menu-open > a .menu-icon-badge {
        transform: translateY(-1px) scale(1.05);
    }

    .sidebar-menu > li.active > a .menu-icon-badge {
        box-shadow: 0 4px 10px rgba(0,0,0,0.28), inset 0 1px 0 rgba(255,255,255,0.35);
    }

    /* Palette de badges par catégorie */
    .badge-blue   { background: linear-gradient(135deg, #2563eb, #38bdf8); }
    .badge-orange { background: linear-gradient(135deg, #f59e0b, #fb923c); }
    .badge-teal   { background: linear-gradient(135deg, #0ea5a3, #22d3ee); }
    .badge-purple { background: linear-gradient(135deg, #7c3aed, #a855f7); }
    .badge-pink   { background: linear-gradient(135deg, #db2777, #f472b6); }
    .badge-green  { background: linear-gradient(135deg, #16a34a, #4ade80); }
    .badge-gold   { background: linear-gradient(135deg, #ca8a04, #facc15); }
    .badge-gray   { background: linear-gradient(135deg, #475569, #94a3b8); }
    .badge-red    { background: linear-gradient(135deg, #dc2626, #f87171); }

    /* Icônes des sous-menus : gardent un style simple et discret */
    .treeview-menu li a i.fa {
        width: 16px;
        text-align: center;
        color: #94a3b8;
        font-size: 12px;
    }

    /* ===== Sous-menus (treeview-menu) ===== */
    .treeview-menu {
        margin: 2px 0 8px;
        background: #ffffff !important;
        border: 1px solid #e2ebf7;
        border-left: 2px solid #E2E8F0;
        border-radius: 10px;
        box-shadow: 0 6px 14px rgba(20, 62, 104, 0.08);
        padding: 4px 0;
    }

    .treeview-menu li { border-bottom: 1px solid #F1F5F9; }
    .treeview-menu li:last-child { border-bottom: none; }

    .treeview-menu li a {
        background-color: transparent !important;
        color: #3a516c !important;
        padding: 8px 16px 8px 30px !important;
        font-size: 12.5px;
        transition: all 0.2s ease;
        border-left: 3px solid transparent !important;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .treeview-menu li a:hover {
        background: #f4f9ff !important;
        border-left: 3px solid #7ba9d3 !important;
        color: #1f4e7b !important;
        padding-left: 27px !important;
    }

    .treeview-menu li.active > a {
        background: #eaf4ff !important;
        border-left: 3px solid var(--sb-primary) !important;
        color: #1b4a76 !important;
        font-weight: 600 !important;
        padding-left: 27px !important;
    }

    .treeview-menu .bullet {
        font-size: 6px;
        color: #87a2bf;
        vertical-align: middle;
        margin-right: 8px;
        transition: color 0.2s ease;
        flex-shrink: 0;
    }

    .treeview-menu li.active i.fa {
        color: var(--sb-primary) !important;
        -webkit-text-fill-color: var(--sb-primary) !important;
    }

    /* ===== Sous-menu niveau 2 ===== */
    .treeview-menu .treeview-menu {
        border-left: 2px solid #E2E8F0;
        border-top: 1px solid #E2E8F0;
        margin: 4px 0;
        background: #FAFBFC !important;
        box-shadow: none;
    }

    .treeview-menu .treeview-menu li a {
        padding-left: 50px !important;
        font-size: 12px;
    }

    .treeview-menu .treeview-menu li.active > a,
    .treeview-menu .treeview-menu li a:hover {
        padding-left: 47px !important;
    }

    .treeview-menu .treeview-menu .bullet {
        font-size: 4px;
        color: #94a3b8;
    }

    /* ===== Chevrons / collapse ===== */
    .main-sidebar .fa-angle-left,
    .main-sidebar .fa-angle-double-right {
        color: #64748B !important;
        transition: transform 0.3s ease;
    }

    .treeview.menu-open > a .fa-angle-left { transform: rotate(-90deg); }

    /* ===== Badge notification ===== */
    .pull-right-container {
        background: #EF4444;
        color: #FFFFFF;
        border-radius: 12px;
        padding: 1px 8px;
        font-size: 11px;
        font-weight: 600;
        margin-left: auto;
    }

    /* ===== Bouton "Installer l'application" (mis en avant) ===== */
    #installAppSidebar a {
        background: linear-gradient(90deg, rgba(16, 185, 129, 0.15), transparent) !important;
        border-radius: 10px;
        border-left: 4px solid var(--sb-accent) !important;
        box-shadow: 0 8px 16px rgba(16, 185, 129, 0.14);
        transition: all 0.3s ease !important;
    }

    #installAppSidebar a:hover {
        background: linear-gradient(90deg, rgba(16, 185, 129, 0.25), transparent) !important;
        transform: translateX(5px);
    }

    #installAppSidebar .fa-download {
        color: var(--sb-accent) !important;
        animation: bounceDown 2s infinite;
    }

    @keyframes bounceDown {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(3px); }
    }

    /* ===== Animations ===== */
    .sidebar-menu > li,
    .treeview-menu li a {
        transition: all 0.2s ease;
    }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .main-sidebar {
            background: linear-gradient(180deg, var(--sb-bg-1) 0%, var(--sb-bg-2) 100%) !important;
        }
        .sidebar-menu > li.active > a::after { display: none; }
    }
</style>

<aside class="main-sidebar" id="alert2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <?php if ($this->rbac->hasPrivilege('student', 'can_view')) { ?>
        <form class="navbar-form navbar-left search-form2" role="search" action="<?php echo site_url('admin/admin/search'); ?>" method="POST">
            <?php echo $this->customlib->getCSRF(); ?>
            <div class="input-group">
                <input type="text" name="search_text" class="form-control search-form" placeholder="<?php echo $this->lang->line('search_by_student_name'); ?>">
                <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </form>
    <?php } ?>

    <section class="sidebar" id="sibe-box">
        <?php $this->load->view('layout/top_sidemenu'); ?>

        <ul class="sidebar-menu verttop">
            <!-- ===== TABLEAU DE BORD ===== -->
            <li class="nav-item mb-2 treeview <?php echo ($this->uri->segment(1) == 'admin' && $this->uri->segment(2) == 'admin' && $this->uri->segment(3) == 'dashboard') ? 'active' : ''; ?>">
                <a href="<?php echo base_url('admin/admin/dashboard'); ?>"
                   class="nav-link family d-flex align-items-center text-left"
                   style="width: 100%;">
                    <span class="menu-icon-badge badge-blue"><i class="fas fa-chart-pie"></i></span>
                    <span>Tableau de bord</span>
                </a>
            </li>

            <!-- ========== ADMINISTRATION ========== -->
            <?php if ($this->module_lib->hasActive('receptioniste')) {
                if (($this->rbac->hasPrivilege('visiteurs', 'can_view') ||
                    $this->rbac->hasPrivilege('journal_appels', 'can_view') ||
                    $this->rbac->hasPrivilege('courier_envoyer', 'can_view') ||
                    $this->rbac->hasPrivilege('courier_reçu', 'can_view') ||
                    $this->rbac->hasPrivilege('reclamation', 'can_view') ||
                    $this->rbac->hasPrivilege('parametre', 'can_view'))) { ?>
                    <li class="treeview family <?php echo ($this->uri->segment(1) == 'admin' && $this->uri->segment(2) == 'admin' && $this->uri->segment(3) == 'hub') ? 'active' : ''; ?>">
                        <a href="<?php echo base_url(); ?>admin/admin/hub">
                            <span class="menu-icon-badge badge-gray"><i class="fas fa-city"></i></span>
                            <span>Administration</span>
                        </a>
                    </li>
                <?php } } ?>

            <!-- ========== COMPTABILITÉ ========== -->
            <?php if ($this->module_lib->hasActive('caisse')) {
                if (($this->rbac->hasPrivilege('caisse', 'can_view') ||
                    $this->rbac->hasPrivilege('recherche_caisse', 'can_view') ||
                    $this->rbac->hasPrivilege('categorie_caisse', 'can_view'))) { ?>
                    <li class="treeview family <?php echo ($this->uri->segment(1) == 'admin' && $this->uri->segment(2) == 'admin' && $this->uri->segment(3) == 'comptabilite') ? 'active' : ''; ?>">
                        <a href="<?php echo base_url(); ?>admin/admin/comptabilite">
                            <span class="menu-icon-badge badge-teal"><i class="fas fa-coins"></i></span>
                            <span>Comptabilité</span>
                        </a>
                    </li>
                <?php } } ?>

            <!-- ========== RESSOURCES HUMAINES ========== -->
            <?php if ($this->module_lib->hasActive('human_resource')) {
                if (($this->rbac->hasPrivilege('staff', 'can_view') ||
                    $this->rbac->hasPrivilege('approve_leave_request', 'can_view') ||
                    $this->rbac->hasPrivilege('apply_leave', 'can_view') ||
                    $this->rbac->hasPrivilege('leave_types', 'can_view') ||
                    $this->rbac->hasPrivilege('recrutement', 'can_view') ||
                    $this->rbac->hasPrivilege('categorie_salaire', 'can_view') ||
                    $this->rbac->hasPrivilege('training', 'can_view') ||
                    $this->rbac->hasPrivilege('formations', 'can_view') ||
                    $this->rbac->hasPrivilege('admission_enquiry', 'can_view') ||
                    $this->rbac->hasPrivilege('teachers_rating', 'can_view') ||
                    $this->rbac->hasPrivilege('department', 'can_view') ||
                    $this->rbac->hasPrivilege('designation', 'can_view') ||
                    $this->rbac->hasPrivilege('sanction', 'can_view') ||
                    $this->rbac->hasPrivilege('disable_staff', 'can_view'))) {

                    $current_controller = $this->router->fetch_class();
                    $hr_controllers = ['staff', 'staffattendance', 'payroll', 'leaverequest', 'leavetypes', 'recrutements', 'candidatures', 'candidats', 'categorie', 'enquiry', 'training_request', 'training', 'sanction', 'tableau_rh'];
                    $hr_active = in_array($current_controller, $hr_controllers) ? 'active' : '';
                    ?>
                    <li class="treeview family <?php echo ($this->uri->segment(1) == 'admin' && $this->uri->segment(2) == 'admin' && $this->uri->segment(3) == 'rh') || $hr_active ? 'active' : ''; ?>">
                        <a href="<?php echo base_url(); ?>admin/admin/rh">
                            <span class="menu-icon-badge badge-pink"><i class="fas fa-users-gear"></i></span>
                            <span>RH & Paie</span>
                        </a>
                    </li>
                <?php } } ?>

            <!-- ========== COMMERCIAL ========== -->
            <li class="treeview family <?php echo ($this->uri->segment(1) == 'admin' && $this->uri->segment(2) == 'admin' && $this->uri->segment(3) == 'commercial') || $com_active ? 'active' : ''; ?>">
                <a href="<?php echo base_url(); ?>admin/admin/commercial">
                    <span class="menu-icon-badge badge-orange"><i class="fas fa-store"></i></span>
                    <span>Commercial</span>
                </a>
            </li>

            <!-- ========== ÉGLISES ========== -->
            <li class="treeview family <?php echo ($this->uri->segment(1) == 'admin' && $this->uri->segment(2) == 'admin' && $this->uri->segment(3) == 'church') || $com_active ? 'active' : ''; ?>">
                <a href="<?php echo base_url(); ?>admin/admin/church">
                    <span class="menu-icon-badge badge-purple"><i class="fas fa-church"></i></span>
                    <span>Églises</span>
                </a>
            </li>

            <!-- ========== ASSOCIATIONS (désactivé) ========== -->
            <!-- <li class="treeview family <?php echo ($this->uri->segment(1) == 'admin' && $this->uri->segment(2) == 'admin' && $this->uri->segment(3) == 'associations') || $com_active ? 'active' : ''; ?>">
                <a href="<?php echo base_url(); ?>admin/admin/associations">
                    <i class="fas fa-handshake ftlayer"></i>
                    <span>Espace Associations</span>
                </a>
            </li> -->

            <!-- ========== TICKETS (désactivé) ========== -->
            <!--<li class="treeview family <?php echo ($this->uri->segment(1) == 'admin' && $this->uri->segment(2) == 'admin' && $this->uri->segment(3) == 'tickets') || $com_active ? 'active' : ''; ?>">
                <a href="<?php echo base_url(); ?>admin/admin/tickets">
                    <i class="fas fa-ticket ftlayer"></i>
                    <span>Espace Tickets</span>
                </a>
            </li> -->

            <!-- ========== CERTIFICATS ========== -->
            <?php if ($this->module_lib->hasActive('certificate')) {
                if (($this->rbac->hasPrivilege('student_certificate', 'can_view') ||
                    $this->rbac->hasPrivilege('generate_certificate', 'can_view') ||
                    $this->rbac->hasPrivilege('student_id_card', 'can_view') ||
                    $this->rbac->hasPrivilege('generate_id_card', 'can_view') ||
                    $this->rbac->hasPrivilege('staff_id_card', 'can_view') ||
                    $this->rbac->hasPrivilege('generate_staff_id_card', 'can_view'))) { ?>
                    <li class="treeview <?php echo (set_Topmenu('Certificate') || $this->uri->segment(2) == 'staffcertificate' || $this->uri->segment(2) == 'staffcertificatelist' || $this->uri->segment(2) == 'generatestaffcertificate') ? 'active' : ''; ?>">
                        <a href="#">
                            <span class="menu-icon-badge badge-orange"><i class="fas fa-certificate"></i></span>
                            <span><?php echo $this->lang->line('certificate'); ?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if ($this->rbac->hasPrivilege('staff_id_card', 'can_view')) { ?>
                                <li class="<?php echo ($this->uri->segment(2) == 'staffcertificate') ? 'active' : ''; ?>">
                                    <a href="<?php echo base_url('admin/staffcertificate/'); ?>">
                                        <i class="fa fa-circle bullet"></i> Produire un certificat
                                    </a>
                                </li>
                                <li class="<?php echo ($this->uri->segment(2) == 'staffcertificatelist') ? 'active' : ''; ?>">
                                    <a href="<?php echo base_url('admin/staffcertificatelist/'); ?>">
                                        <i class="fa fa-circle bullet"></i> Base de donnée
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ($this->rbac->hasPrivilege('generate_staff_id_card', 'can_view')) { ?>
                                <li class="<?php echo ($this->uri->segment(2) == 'generatestaffcertificate') ? 'active' : ''; ?>">
                                    <a href="<?php echo base_url('admin/generatestaffcertificate/'); ?>">
                                        <i class="fa fa-circle bullet"></i> Générer un certificat
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } } ?>

            <!-- ========== FRONT CMS ========== -->
            <?php if ($this->module_lib->hasActive('front_cms')) {
                if (($this->rbac->hasPrivilege('event', 'can_view') ||
                    $this->rbac->hasPrivilege('gallery', 'can_view') ||
                    $this->rbac->hasPrivilege('notice', 'can_view') ||
                    $this->rbac->hasPrivilege('media_manager', 'can_view') ||
                    $this->rbac->hasPrivilege('pages', 'can_view') ||
                    $this->rbac->hasPrivilege('menus', 'can_view') ||
                    $this->rbac->hasPrivilege('banner_images', 'can_view'))) { ?>
                    <li class="treeview <?php echo (set_Topmenu('Front CMS') || ($this->uri->segment(2) == 'front' && $this->uri->segment(3) == 'notice')) ? 'active' : ''; ?>">
                        <a href="#">
                            <span class="menu-icon-badge badge-teal"><i class="fas fa-globe"></i></span>
                            <span><?php echo $this->lang->line('front_cms'); ?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if ($this->rbac->hasPrivilege('notice', 'can_view')) { ?>
                                <li class="<?php echo ($this->uri->segment(2) == 'front' && $this->uri->segment(3) == 'notice') ? 'active' : ''; ?>">
                                    <a href="<?php echo base_url(); ?>admin/front/notice">
                                        <i class="fa fa-circle bullet"></i> <?php echo $this->lang->line('notice'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } } ?>

            <?php if ($this->rbac->hasPrivilege('superadmin', 'can_view')) { ?>
                <li class="treeview family <?php echo ($this->uri->segment(2) == 'demorequests') ? 'active' : ''; ?>">
                    <a href="<?php echo base_url(); ?>admin/demorequests">
                        <span class="menu-icon-badge badge-red"><i class="fas fa-bullhorn"></i></span>
                        <span>Demandes de demo</span>
                    </a>
                </li>
                <!--<li class="treeview family <?php echo ($this->uri->segment(2) == 'sitecontent') ? 'active' : ''; ?>">
                    <a href="<?php echo base_url(); ?>admin/sitecontent">
                        <i class="fas fa-globe ftlayer"></i>
                        <span>Gestion du site</span>
                    </a>
                </li>-->
            <?php } ?>

            <!-- ========== ALUMNI ========== -->
            <?php if ($this->module_lib->hasActive('alumni')) {
                if (($this->rbac->hasPrivilege('manage_alumni', 'can_view')) || ($this->rbac->hasPrivilege('events', 'can_view'))) { ?>
                    <li class="treeview <?php echo (set_Topmenu('alumni') || ($this->uri->segment(2) == 'alumni' && $this->uri->segment(3) == 'events')) ? 'active' : ''; ?>">
                        <a href="#">
                            <span class="menu-icon-badge badge-purple"><i class="fas fa-user-graduate"></i></span>
                            <span><?php echo $this->lang->line('alumni'); ?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <?php if ($this->rbac->hasPrivilege('events', 'can_view')) { ?>
                                <li class="<?php echo ($this->uri->segment(2) == 'alumni' && $this->uri->segment(3) == 'events') ? 'active' : ''; ?>">
                                    <a href="<?php echo base_url(); ?>admin/alumni/events">
                                        <i class="fa fa-circle bullet"></i> <?php echo $this->lang->line('events'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } } ?>

            <!-- ========== SOUSCRIPTION ========== -->
            <?php if ($this->module_lib->hasActive('comptes')) {
                if ($this->rbac->hasPrivilege('superadmin', 'can_view')) { ?>
                    <li class="treeview <?php echo (set_Topmenu('Comptes') || $this->uri->segment(2) == 'comptes') ? 'active' : ''; ?>">
                        <a href="#">
                            <span class="menu-icon-badge badge-gold"><i class="fas fa-credit-card"></i></span>
                            <span>Souscription</span>
                            <i class="fa fa-angle-right pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li class="<?php echo ($this->uri->segment(2) == 'comptes' && $this->uri->segment(3) == '') ? 'active' : ''; ?>">
                                <a href="<?php echo base_url(); ?>admin/comptes">
                                    <i class="fa fa-circle bullet"></i> Comptes
                                </a>
                            </li>
                            <li class="<?php echo ($this->uri->segment(2) == 'comptes' && $this->uri->segment(3) == 'dashboard') ? 'active' : ''; ?>">
                                <a href="<?php echo base_url(); ?>admin/comptes/dashboard">
                                    <i class="fa fa-circle bullet"></i> Tableau de bord
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } } ?>

            <?php if ($this->module_lib->hasActive('comptes') && $can_manage_succursales && $is_head_office) { ?>
                <li class="treeview <?php echo (set_Topmenu('Succursales') || ($this->uri->segment(2) == 'comptes' && in_array($this->uri->segment(3), array('succursales', 'create_succursale'), true))) ? 'active' : ''; ?>">
                    <a href="#">
                        <span class="menu-icon-badge badge-gray"><i class="fas fa-sitemap"></i></span>
                        <span>Succursales</span>
                        <i class="fa fa-angle-right pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li class="<?php echo ($this->uri->segment(2) == 'comptes' && $this->uri->segment(3) == 'succursales') ? 'active' : ''; ?>">
                            <a href="<?php echo base_url(); ?>admin/comptes/succursales">
                                <i class="fa fa-circle bullet"></i> Liste des succursales
                            </a>
                        </li>
                        <li class="<?php echo ($this->uri->segment(2) == 'comptes' && $this->uri->segment(3) == 'create_succursale') ? 'active' : ''; ?>">
                            <a href="<?php echo base_url(); ?>admin/comptes/create_succursale">
                                <i class="fa fa-circle bullet"></i> Nouvelle succursale
                            </a>
                        </li>
                    </ul>
                </li>
            <?php } ?>

            <!-- ========== PARAMÈTRES ========== -->
            <?php if ($this->module_lib->hasActive('system_settings')) {
                if (($this->rbac->hasPrivilege('general_setting', 'can_edit') ||
                    $this->rbac->hasPrivilege('session_setting', 'can_view') ||
                    $this->rbac->hasPrivilege('notification_setting', 'can_edit') ||
                    $this->rbac->hasPrivilege('sms_setting', 'can_edit') ||
                    $this->rbac->hasPrivilege('email_setting', 'can_edit') ||
                    $this->rbac->hasPrivilege('payment_methods', 'can_edit') ||
                    $this->rbac->hasPrivilege('languages', 'can_view') ||
                    $this->rbac->hasPrivilege('languages', 'can_add') ||
                    $this->rbac->hasPrivilege('backup_restore', 'can_view') ||
                    $this->rbac->hasPrivilege('front_cms_setting', 'can_edit'))) { ?>
                    <li class="treeview family <?php echo (set_Topmenu('System Settings') || $this->uri->segment(2) == 'schsettings' || $this->uri->segment(2) == 'emailconfig' || $this->uri->segment(2) == 'users' || $this->uri->segment(2) == 'roles' || $this->uri->segment(2) == 'module' || $this->uri->segment(2) == 'admin' && $this->uri->segment(3) == 'setting') ? 'active' : ''; ?>">
                        <a href="<?php echo base_url(); ?>admin/admin/setting">
                            <span class="menu-icon-badge badge-gray"><i class="fas fa-gears"></i></span>
                            <span>Paramètre</span>
                        </a>
                    </li>
                <?php } } ?>

            <!-- ========== INSTALLER L'APPLICATION ========== -->
            <li class="treeview" id="installAppSidebar">
                <a href="#" onclick="installDiagomaApp(); return false;">
                    <span class="menu-icon-badge badge-green"><i class="fa fa-download"></i></span>
                    <span style="font-weight: 600;">Application</span>
                </a>
            </li>
        </ul>
    </section>
</aside>

<script>
    // =============================================
    // INSTALLATION FORCÉE DE L'APPLICATION
    // =============================================

   function installDiagomaApp() {
    console.log('🔧 Installation lancée...');
    
    // 1. Vérifier si le pop-up d'installation est disponible (PRIORITAIRE)
    if (window.deferredPrompt) {
        console.log('✅ Pop-up disponible, affichage...');
        window.deferredPrompt.prompt();
        window.deferredPrompt.userChoice.then(function(choiceResult) {
            if (choiceResult.outcome === 'accepted') {
                console.log('✅ Application installée avec succès');
                var installItem = document.getElementById('installAppSidebar');
                if (installItem) {
                    installItem.innerHTML = `
                        <a href="#">
                            <span class="menu-icon-badge badge-green"><i class="fa fa-check-circle"></i></span>
                            <span style="font-weight: 600;">Application installée ✓</span>
                            <span class="pull-right-container">
                                <i class="fa fa-check-circle"></i>
                            </span>
                        </a>
                    `;
                }
                if (typeof successMsg !== 'undefined') {
                    successMsg('🎉 Application installée avec succès !');
                } else {
                    alert('🎉 Application installée avec succès !');
                }
            } else {
                console.log('❌ Installation refusée');
            }
            window.deferredPrompt = null;
        });
        return;
    }

    // 2. Vérifier si l'application est déjà installée (iOS)
    if (window.navigator && window.navigator.standalone) {
        alert('✅ L\'application est déjà installée sur votre appareil !');
        return;
    }

    // 3. Détection du navigateur
    var ua = navigator.userAgent.toLowerCase();
    var isChrome = ua.indexOf('chrome') > -1 && ua.indexOf('edg') === -1;
    var isEdge = ua.indexOf('edg') > -1;
    var isSafari = ua.indexOf('safari') > -1 && ua.indexOf('chrome') === -1;
    var isFirefox = ua.indexOf('firefox') > -1;
    var isIOS = /iphone|ipad|ipod/.test(ua);
    var isAndroid = /android/.test(ua);

    // 4. iOS (iPhone/iPad)
    if (isIOS) {
        alert(
            '📱 Pour installer Diagoma sur votre iPhone/iPad :\n\n' +
            '1️⃣ Appuyez sur le bouton "Partager" (carré avec flèche vers le haut)\n' +
            '2️⃣ Faites défiler vers le bas et appuyez sur "Ajouter à l\'écran d\'accueil"\n' +
            '3️⃣ Appuyez sur "Ajouter" en haut à droite\n\n' +
            '✅ L\'application apparaîtra sur votre écran d\'accueil !'
        );
        return;
    }

    // 5. Android (Chrome)
    if (isAndroid && isChrome) {
        if (window.deferredPrompt) {
            window.deferredPrompt.prompt();
        } else {
            alert(
                '📱 Pour installer Diagoma sur Android :\n\n' +
                '1️⃣ Ouvrez le menu Chrome (⋮) en haut à droite\n' +
                '2️⃣ Sélectionnez "Installer l\'application"\n' +
                '3️⃣ Appuyez sur "Installer"\n\n' +
                '✅ L\'application apparaîtra sur votre écran d\'accueil !'
            );
        }
        return;
    }

    // 6. Chrome/Edge Desktop
    if (isChrome || isEdge) {
        // Vérifier si le pop-up est disponible mais pas déclenché
        if (window.deferredPrompt) {
            // Essayer de déclencher le pop-up
            window.deferredPrompt.prompt();
            window.deferredPrompt.userChoice.then(function(choiceResult) {
                if (choiceResult.outcome === 'accepted') {
                    console.log('✅ Application installée avec succès');
                    var installItem = document.getElementById('installAppSidebar');
                    if (installItem) {
                        installItem.innerHTML = `
                            <a href="#">
                                <span class="menu-icon-badge badge-green"><i class="fa fa-check-circle"></i></span>
                                <span style="font-weight: 600;">Application installée ✓</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-check-circle"></i>
                                </span>
                            </a>
                        `;
                    }
                }
                window.deferredPrompt = null;
            });
            return;
        }

        // Sinon, afficher les instructions
        var msg = 
            '💻 Pour installer Diagoma sur votre ordinateur :\n\n' +
            '1️⃣ Cliquez sur l\'icône 📱 dans la barre d\'adresse\n' +
            '2️⃣ Ou cliquez sur le menu (⋮) → "Installer l\'application"\n' +
            '3️⃣ Confirmez l\'installation\n\n' +
            '✅ L\'application apparaîtra dans votre menu Démarrer !';
        
        alert(msg);
        return;
    }

    // 7. Firefox
    if (isFirefox) {
        alert(
            '⚠️ L\'installation n\'est pas disponible sur Firefox.\n\n' +
            '📌 Utilisez Google Chrome ou Microsoft Edge pour installer Diagoma.'
        );
        return;
    }

    // 8. Autres navigateurs
    alert(
        '⚠️ L\'installation n\'est pas disponible sur ce navigateur.\n\n' +
        '📌 Utilisez Google Chrome ou Microsoft Edge pour installer Diagoma.\n\n' +
        '📱 Sur mobile, utilisez le menu du navigateur pour "Ajouter à l\'écran d\'accueil".'
    );
}

    // =============================================
    // DÉTECTION D'INSTALLATION
    // =============================================

    // Détecter quand l'application est installée
    window.addEventListener('appinstalled', function() {
        console.log('✅ Application installée !');
        var installItem = document.getElementById('installAppSidebar');
        if (installItem) {
            installItem.innerHTML = `
            <a href="#">
                <span class="menu-icon-badge badge-green"><i class="fa fa-check-circle"></i></span>
                <span style="font-weight: 600;">Application installée ✓</span>
                <span class="pull-right-container">
                    <i class="fa fa-check-circle"></i>
                </span>
            </a>
        `;
        }
        if (typeof successMsg !== 'undefined') {
            successMsg('🎉 Application installée avec succès !');
        }
    });

    // Détecter iOS en mode standalone (application installée)
    if (window.navigator && window.navigator.standalone) {
        var installItem = document.getElementById('installAppSidebar');
        if (installItem) {
            installItem.innerHTML = `
            <a href="#">
                <span class="menu-icon-badge badge-green"><i class="fa fa-check-circle"></i></span>
                <span style="font-weight: 600;">Application installée ✓</span>
                <span class="pull-right-container">
                    <i class="fa fa-check-circle"></i>
                </span>
            </a>
        `;
        }
    }

        document.head.appendChild(style);
    
</script>