<h3>Facture #<?= $facture['id']; ?></h3>
<p>Client : <?= esc($facture['client_nom']); ?></p>
<p>Montant : <?= number_format($facture['montant_total'], 2, ',', ' '); ?> FCFA</p>

<button id="sendToFne" class="btn btn-primary">Certifier via FNE</button>

<script>
    document.getElementById('sendToFne').addEventListener('click', () => {
        fetch('<?= base_url("fne/sendToFne/" . $facture['id']); ?>')
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if(data.status === 'success') location.reload();
            });
    });
</script>
