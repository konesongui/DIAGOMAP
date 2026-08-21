<style type="text/css">
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: "Trebuchet MS", "Segoe UI", Arial, sans-serif;
        background: #f2f5f8;
    }

    .badge-sheet {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        padding: 14px;
        justify-content: flex-start;
    }

    .badge-card {
        width: 340px;
        min-height: 214px;
        border-radius: 16px;
        overflow: hidden;
        background: #ffffff;
        border: 1px solid #cfd9e5;
        box-shadow: 0 10px 24px rgba(5, 31, 58, 0.16);
        position: relative;
        page-break-inside: avoid;
    }

    .badge-top {
        position: relative;
        padding: 12px 14px;
        color: #ffffff;
        background: linear-gradient(120deg, #0f4c81 0%, #0aa3b8 50%, #59c174 100%);
    }

    .badge-top:after {
        content: "";
        position: absolute;
        right: -35px;
        top: -24px;
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.16);
    }

    .org-name {
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        line-height: 1.2;
        position: relative;
        z-index: 1;
    }

    .org-sub {
        font-size: 10px;
        opacity: 0.95;
        margin-top: 3px;
        position: relative;
        z-index: 1;
    }

    .badge-body {
        display: flex;
        padding: 12px;
        gap: 10px;
        align-items: stretch;
    }

    .profile-box {
        flex: 1;
        min-width: 0;
    }

    .badge-label {
        font-size: 10px;
        color: #53728f;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        margin-bottom: 4px;
    }

    .badge-name {
        font-size: 18px;
        font-weight: 800;
        color: #0d2a45;
        margin-bottom: 6px;
        line-height: 1.1;
        text-transform: uppercase;
    }

    .badge-role {
        font-size: 11px;
        font-weight: 700;
        color: #0f4c81;
        margin-bottom: 8px;
    }

    .meta-line {
        font-size: 11px;
        color: #304f6a;
        margin-bottom: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .meta-line strong {
        color: #0b3559;
    }

    .qr-box {
        width: 105px;
        min-width: 105px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #f5fbff;
        border: 1px solid #cbe7ff;
        border-radius: 10px;
        padding: 7px;
    }

    .qr-box img {
        width: 85px;
        height: 85px;
        object-fit: contain;
        background: #fff;
        border-radius: 6px;
        border: 1px solid #dbe8f5;
    }

    .qr-caption {
        margin-top: 5px;
        font-size: 10px;
        font-weight: 700;
        color: #175681;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        text-align: center;
    }

    .qr-manual-code {
        margin-top: 4px;
        font-size: 11px;
        font-weight: 800;
        color: #0d2a45;
        background: #ffffff;
        border: 1px dashed #9fc3df;
        border-radius: 6px;
        padding: 2px 6px;
        letter-spacing: 1px;
    }

    .badge-footer {
        padding: 8px 12px 10px;
        border-top: 1px dashed #d3dfec;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 10px;
        color: #486581;
        background: #fbfdff;
    }

    .security-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #21a453;
        margin-right: 5px;
        vertical-align: middle;
    }

    @media print {
        body {
            background: #fff;
        }

        .badge-sheet {
            gap: 8px;
            padding: 4px;
        }

        .badge-card {
            box-shadow: none;
            border: 1px solid #bfcde0;
        }
    }
</style>

<div class="badge-sheet">
    <?php if (!empty($staffs)) { ?>
        <?php foreach ($staffs as $staff) { ?>
            <?php
            $full_name = trim($staff->name . ' ' . $staff->surname);
            $qr_image = isset($qr_codes[$staff->id]) ? $qr_codes[$staff->id] : '';
            $manual_code = str_pad((string)$staff->id, 6, '0', STR_PAD_LEFT);
            ?>
            <div class="badge-card">
                <div class="badge-top">
                    <div class="org-name"><?php echo !empty($sch_setting->name) ? strtoupper($sch_setting->name) : 'DIAGOMA'; ?></div>
                    <div class="org-sub"><?php echo !empty($sch_setting->address) ? strtoupper($sch_setting->address) : 'BADGE EMPLOYE'; ?></div>
                </div>

                <div class="badge-body">
                    <div class="profile-box">
                        <div class="badge-label">Employe</div>
                        <div class="badge-name"><?php echo strtoupper($full_name); ?></div>
                        <div class="badge-role"><?php echo !empty($staff->user_type) ? strtoupper($staff->user_type) : '-'; ?></div>
                        <div class="meta-line"><strong>ID:</strong> <?php echo $staff->employee_id; ?></div>
                        <div class="meta-line"><strong>Poste:</strong> <?php echo !empty($staff->designation) ? $staff->designation : '-'; ?></div>
                        <div class="meta-line"><strong>Service:</strong> <?php echo !empty($staff->department) ? $staff->department : '-'; ?></div>
                    </div>

                    <div class="qr-box">
                        <?php if (!empty($qr_image)) { ?>
                            <img src="<?php echo $qr_image; ?>" alt="QR Presence" />
                        <?php } else { ?>
                            <div style="font-size:10px; text-align:center; color:#a21f1f;">QR indisponible</div>
                        <?php } ?>
                        <div class="qr-caption">Scan Presence</div>
                        <div class="qr-manual-code"><?php echo $manual_code; ?></div>
                    </div>
                </div>

                <div class="badge-footer">
                    <span><span class="security-dot"></span>Badge actif</span>
                    <span><?php echo date('d/m/Y H:i'); ?></span>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div style="padding: 20px; font-family: Arial;">Aucun employe a imprimer.</div>
    <?php } ?>
</div>
