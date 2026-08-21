<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

// Connexion BDD
$conn = new mysqli("localhost","root","","diago");

// Requête pour le compte de résultat
$sql = "
    SELECT
        CASE
            WHEN ae.account LIKE '6%' THEN 'Charges'
            WHEN ae.account LIKE '7%' THEN 'Produits'
            ELSE 'Autres'
        END AS type_compte,
        ae.account,
        SUM(ae.debit) AS total_debit,
        SUM(ae.credit) AS total_credit,
        SUM(ae.credit - ae.debit) AS resultat_ligne
    FROM accounting_entries ae
    WHERE ae.account LIKE '6%' OR ae.account LIKE '7%'
    GROUP BY type_compte, ae.account
    ORDER BY type_compte, ae.account
";
$result = $conn->query($sql);

// Traduction des mois
$moisEn = [
    'January' => 'Janvier',
    'February' => 'Février',
    'March' => 'Mars',
    'April' => 'Avril',
    'May' => 'Mai',
    'June' => 'Juin',
    'July' => 'Juillet',
    'August' => 'Août',
    'September' => 'Septembre',
    'October' => 'Octobre',
    'November' => 'Novembre',
    'December' => 'Décembre',
];
?>

<style type="text/css">
    .text-primary { color: black; text-transform: uppercase; }
    .box-title b { color: black; }
</style>

<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1><i class="fa fa-money"></i> Compte de Résultat</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('reports/_finance'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>

                    <!-- Formulaire recherche -->
                    <form role="form" action="<?php echo site_url('report/compte_resultat') ?>" method="post">
                        <div class="box-body row">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('search') . " " . $this->lang->line('type'); ?></label>
                                    <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                        <?php foreach ($searchlist as $key => $search) { ?>
                                            <option value="<?php echo $key ?>" <?php echo (isset($search_type) && $search_type == $key) ? "selected" : ""; ?>>
                                                <?php echo $search ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('search_type'); ?></span>
                                </div>
                            </div>
                            <div id="date_result"></div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm pull-right">
                                        <i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Résultats -->
                    <div class="">
                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix">
                                <i class="fa fa-money"></i> <b>COMPTE DE RESULTAT</b>
                            </h3>
                        </div>
                        <div class="box-body table-responsive">
                            <div class="download_label">
                                <div class="col-md-4 col-xs-2 col-sm-6">
                                    <img style="width: 150px; height: 70px;"
                                         src="<?= base_url() . "/uploads/school_content/admin_logo/" . $sch_setting->admin_logo ?>"
                                         alt="Logo" />
                                </div>
                                <br/><br/><br/><br/>
                                <?php
                                echo "COMPTE DE RESULTAT<br/><br/>";
                                echo "Période : "; $this->customlib->get_postmessage();
                                ?>
                            </div>

                            <!-- Tableau -->
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Compte</th>
                                    <th>Total Débit</th>
                                    <th>Total Crédit</th>
                                    <th>Résultat (ligne)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $total_charges = 0;
                                $total_produits = 0;

                                if($result && $result->num_rows > 0):
                                    while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['type_compte'] ?></td>
                                            <td><?= $row['account'] ?></td>
                                            <td><?= number_format($row['total_debit'], 2, ',', ' ') ?></td>
                                            <td><?= number_format($row['total_credit'], 2, ',', ' ') ?></td>
                                            <td><?= number_format($row['resultat_ligne'], 2, ',', ' ') ?></td>
                                        </tr>
                                        <?php
                                        if($row['type_compte'] == 'Charges'){
                                            $total_charges += $row['total_debit'] - $row['total_credit'];
                                        } elseif($row['type_compte'] == 'Produits'){
                                            $total_produits += $row['total_credit'] - $row['total_debit'];
                                        }
                                    endwhile;
                                    ?>
                                    <!-- Totaux -->
                                    <tr style="font-weight:bold; background:#f9f9f9;">
                                        <td colspan="2">Total Charges</td>
                                        <td colspan="3"><?= number_format($total_charges, 2, ',', ' ') ?></td>
                                    </tr>
                                    <tr style="font-weight:bold; background:#f9f9f9;">
                                        <td colspan="2">Total Produits</td>
                                        <td colspan="3"><?= number_format($total_produits, 2, ',', ' ') ?></td>
                                    </tr>
                                    <tr style="font-weight:bold; background:#d9edf7;">
                                        <td colspan="2">Résultat Net</td>
                                        <td colspan="3">
                                            <?= number_format($total_produits - $total_charges, 2, ',', ' ') ?>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5">Aucune écriture comptable disponible</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    <?php if ($search_type == 'period') { ?>
    $(document).ready(function () {
        showdate('period');
    });
    <?php } ?>
</script>
