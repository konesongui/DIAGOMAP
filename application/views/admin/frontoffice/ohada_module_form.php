<style>
    .ohada-form-shell { padding: 20px 15px 35px; }
    .ohada-form-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .ohada-form-header {
        background: linear-gradient(135deg, #1d4f91 0%, #273772 100%);
        color: #fff;
        padding: 22px;
    }
    .ohada-form-header h2 { margin: 0 0 8px; font-size: 26px; font-weight: 700; }
    .ohada-form-header p { margin: 0; color: rgba(255,255,255,0.82); }
    .ohada-form-body { padding: 22px; }
    .ohada-form-actions { margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap; }
</style>

<div class="content-wrapper">
    <section class="content ohada-form-shell">
        <div class="ohada-form-card">
            <div class="ohada-form-header">
                <h2><?php echo html_escape($title); ?></h2>
                <p><?php echo html_escape($subtitle); ?></p>
            </div>

            <div class="ohada-form-body">
                <?php if (!empty($this->session->flashdata('msg'))) : ?>
                    <?php echo $this->session->flashdata('msg'); ?>
                <?php endif; ?>

                <?php if ($info_message !== '') : ?>
                    <div class="alert alert-info"><?php echo $info_message; ?></div>
                <?php endif; ?>

                <?php echo form_open($form_action); ?>
                    <?php echo $fields_html; ?>

                    <div class="ohada-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> <?php echo html_escape($submit_label); ?>
                        </button>
                        <a href="<?php echo html_escape($cancel_url); ?>" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Retour
                        </a>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </section>
</div>
