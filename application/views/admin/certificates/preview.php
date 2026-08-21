<?php
$certificate = $preview_data['certificate'];
$data = $preview_data['preview_data'];

// Remplacer les variables dans le contenu
$content = $certificate->content_body;
foreach ($data as $key => $value) {
    $content = str_replace('{' . $key . '}', $value, $content);
}
?>

<div class="certificate-preview">
    <style>
        .certificate-container {
            background: white;
            border: 10px solid <?php echo $certificate->header_color; ?>;
            padding: 40px;
            position: relative;
            min-height: 500px;
        }

        .certificate-header {
            text-align: center;
            border-bottom: 2px solid <?php echo $certificate->header_color; ?>;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .certificate-logo {
            max-width: 120px;
            margin-bottom: 15px;
        }

        .certificate-title {
            font-size: 28px;
            font-weight: bold;
            color: <?php echo $certificate->header_color; ?>;
            margin: 20px 0;
        }

        .certificate-content {
            font-size: 16px;
            line-height: 1.8;
            text-align: justify;
            margin: 40px 0;
        }

        .certificate-signature {
            margin-top: 50px;
            text-align: right;
        }

        .certificate-signature-img {
            max-width: 200px;
            margin-top: 10px;
        }

        .certificate-code {
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 11px;
            color: #999;
        }
    </style>

    <div class="certificate-container">
        <?php if ($certificate->logo_path): ?>
            <div class="certificate-header">
                <img src="<?php echo base_url($certificate->logo_path); ?>" class="certificate-logo">
                <h2><?php echo $certificate->title; ?></h2>
            </div>
        <?php else: ?>
            <div class="certificate-header">
                <h2><?php echo $certificate->title; ?></h2>
            </div>
        <?php endif; ?>

        <div class="certificate-content">
            <?php echo nl2br($content); ?>
        </div>

        <div class="certificate-signature">
            <?php if ($certificate->signature_path): ?>
                <img src="<?php echo base_url($certificate->signature_path); ?>" class="certificate-signature-img">
            <?php endif; ?>
            <?php if ($certificate->signature_text): ?>
                <div><?php echo $certificate->signature_text; ?></div>
            <?php endif; ?>
        </div>

        <div class="certificate-code">
            Code: <?php echo $certificate->generated_code; ?>
        </div>
    </div>
</div>