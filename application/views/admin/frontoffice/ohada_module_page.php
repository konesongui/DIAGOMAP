<style>
    .ohada-page-shell { padding: 20px 15px 35px; }
    .ohada-page-hero {
        background: linear-gradient(135deg, #1d4f91 0%, #273772 100%);
        color: #fff;
        border-radius: 18px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 12px 28px rgba(39, 55, 114, 0.16);
    }
    .ohada-page-hero h2 { margin: 0 0 8px; font-size: 28px; font-weight: 700; }
    .ohada-page-hero p { margin: 0; color: rgba(255,255,255,0.82); font-size: 14px; line-height: 1.6; }
    .ohada-card-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-top: 18px; }
    .ohada-stat-card {
        background: rgba(255,255,255,0.12);
        border-radius: 14px;
        padding: 14px 16px;
        min-height: 88px;
    }
    .ohada-stat-card .value { display: block; font-size: 24px; font-weight: 700; margin-bottom: 8px; }
    .ohada-stat-card .label { display: block; font-size: 12px; text-transform: uppercase; color: rgba(255,255,255,0.7); }
    .ohada-module-panel {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .ohada-panel-toolbar {
        padding: 18px 20px;
        border-bottom: 1px solid #eef2f7;
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: flex-start;
        flex-wrap: wrap;
    }
    .ohada-panel-toolbar .toolbar-text { flex: 1; min-width: 260px; }
    .ohada-panel-toolbar .toolbar-text .info { color: #64748b; font-size: 13px; line-height: 1.6; }
    .ohada-toolbar-actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .ohada-panel-body { padding: 18px 20px 22px; }
    .ohada-panel-body .table > thead > tr > th { background: #f8fafc; color: #334155; border-bottom: none; }
    .ohada-empty-state {
        padding: 32px 18px;
        text-align: center;
        color: #64748b;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        background: #f8fafc;
    }
    .ohada-filter-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 18px;
    }
    @media (max-width: 991px) {
        .ohada-card-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 767px) {
        .ohada-page-hero, .ohada-panel-toolbar, .ohada-panel-body { padding: 16px; }
        .ohada-card-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="content-wrapper">
    <section class="content ohada-page-shell">
        <div class="ohada-page-hero">
            <h2><?php echo html_escape($title); ?></h2>
            <p><?php echo html_escape($subtitle); ?></p>

            <?php if (!empty($cards)) : ?>
                <div class="ohada-card-grid">
                    <?php foreach ($cards as $card) : ?>
                        <div class="ohada-stat-card">
                            <span class="value"><?php echo html_escape($card['value']); ?></span>
                            <span class="label"><?php echo html_escape($card['label']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="ohada-module-panel">
            <div class="ohada-panel-toolbar">
                <div class="toolbar-text">
                    <?php if (!empty($this->session->flashdata('msg'))) : ?>
                        <?php echo $this->session->flashdata('msg'); ?>
                    <?php endif; ?>
                    <?php if ($info_message !== '') : ?>
                        <div class="info"><?php echo $info_message; ?></div>
                    <?php endif; ?>
                </div>
                <?php if ($actions_html !== '') : ?>
                    <div class="ohada-toolbar-actions"><?php echo $actions_html; ?></div>
                <?php endif; ?>
            </div>

            <div class="ohada-panel-body">
                <?php if ($filters_html !== '') : ?>
                    <div class="ohada-filter-box"><?php echo $filters_html; ?></div>
                <?php endif; ?>

                <?php if (!empty($table_rows)) : ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <?php foreach ($table_headers as $header) : ?>
                                        <th><?php echo html_escape($header); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($table_rows as $row) : ?>
                                    <tr>
                                        <?php foreach ($row as $cell) : ?>
                                            <td><?php echo $cell; ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <div class="ohada-empty-state">
                        <i class="fa fa-database" style="font-size:36px; margin-bottom:10px; display:block;"></i>
                        <?php echo html_escape($empty_message); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
