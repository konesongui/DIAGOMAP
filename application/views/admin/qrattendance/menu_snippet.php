<!-- 
    SNIPPET OPTIONNEL : Menu raccourci QR Attendance
    À ajouter dans votre sidebar ou menu principal
    
    Couleurs :
    - Bleu : #28669e
    - Jaune : #fec32e
-->

<div class="qrcode-menu-section">
    <div class="menu-title">
        <i class="fa fa-qrcode"></i> Pointage QR Code
    </div>
    <ul class="menu-items">
        <li class="menu-item">
            <a href="<?php echo base_url('admin/qrattendance/display_qr'); ?>">
                <i class="fa fa-desktop"></i>
                <span>Afficher QR Code</span>
                <small>Pour l'affichage à l'entrée</small>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?php echo base_url('admin/qrattendance/today_attendance'); ?>">
                <i class="fa fa-clock-o"></i>
                <span>Présences du jour</span>
                <small>Arrivées et départs en temps réel</small>
            </a>
        </li>
        <li class="menu-item">
            <a href="<?php echo base_url('admin/qrattendance/attendance_report'); ?>">
                <i class="fa fa-bar-chart"></i>
                <span>Rapport de présence</span>
                <small>Statistiques et filtres avancés</small>
            </a>
        </li>
    </ul>
</div>

<style>
    .qrcode-menu-section {
        background: linear-gradient(135deg, #28669e 0%, #1a3f5c 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin: 15px 0;
    }

    .qrcode-menu-section .menu-title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 10px;
        border-bottom: 2px solid rgba(254, 195, 46, 0.3);
    }

    .qrcode-menu-section .menu-items {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .qrcode-menu-section .menu-item {
        margin: 8px 0;
    }

    .qrcode-menu-section .menu-item a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .qrcode-menu-section .menu-item a:hover {
        background: rgba(254, 195, 46, 0.2);
        transform: translateX(5px);
    }

    .qrcode-menu-section .menu-item a i {
        font-size: 18px;
        width: 24px;
        color: #fec32e;
    }

    .qrcode-menu-section .menu-item a span {
        font-weight: 500;
    }

    .qrcode-menu-section .menu-item a small {
        display: block;
        font-size: 12px;
        opacity: 0.7;
        margin-top: 2px;
    }
</style>
