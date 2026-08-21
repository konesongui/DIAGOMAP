<?php
$total_amount = $totaux_generaux->total_amount ?? 0;
$total_amount_re = $totaux_generaux->total_amount_re ?? 0;
$total_entrees_all = $totaux_generaux->total_entrees_all ?? 0;
$total_sorties_all = $totaux_generaux->total_sorties_all ?? 0;
$nb_caisses = $totaux_generaux->nb_caisses ?? 0;
?>

<!-- Votre code HTML pour les totaux, identique à la partie dans votre vue principale -->
<div class="row">
    <div class="col-md-3 text-center">
        <div class="stat-box" style="width: 300px;">
            <div class="stat-value text-success" style="font-size: 18px; color: black">
                <?php echo number_format($total_amount, 0, ',', ' '); ?> FCFA
            </div>
            <div class="stat-label" style="font-size: 12px;">
                <i class="fa fa-bank"></i> Montant Initial Total
            </div>
        </div>
    </div>
    <!-- ... autres colonnes ... -->
</div>