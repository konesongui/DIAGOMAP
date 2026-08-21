<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

?>

<?php
$conn = new mysqli("localhost","root","","diago");


$sql = "
    SELECT
        'TVA collectée' AS type_tva,
        COALESCE(SUM(ae.credit - ae.debit), 0) AS montant
    FROM accounting_entries ae
    WHERE ae.account = '44571'

    UNION ALL

    SELECT
        'TVA déductible' AS type_tva,
        COALESCE(SUM(ae.debit - ae.credit), 0) AS montant
    FROM accounting_entries ae
    WHERE ae.account = '44566'
";
$result = $this->db->query($sql);
$data = $result->result_array();
echo json_encode($data);

?>
<?php
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
<?php



?>
<style type="text/css">
    /*REQUIRED*/
    .carousel-row {
        margin-bottom: 10px;
    }
    .text-primary{
        color: black;
        text-transform: uppercase;
    }
    .slide-row {
        padding: 0;
        background-color: #ffffff;
        min-height: 150px;
        border: 1px solid #e7e7e7;
        overflow: hidden;
        height: auto;
        position: relative;
    }
    .slide-carousel {
        width: 20%;
        float: left;
        display: inline-block;
    }
    .slide-carousel .carousel-indicators {
        margin-bottom: 0;
        bottom: 0;
        background: rgba(0, 0, 0, .5);
    }
    .slide-carousel .carousel-indicators li {
        border-radius: 0;
        width: 20px;
        height: 6px;
    }
    .slide-carousel .carousel-indicators .active {
        margin: 1px;
    }
    .slide-content {
        position: absolute;
        top: 0;
        left: 20%;
        display: block;
        float: left;
        width: 80%;
        max-height: 76%;
        padding: 1.5% 2% 2% 2%;
        overflow-y: auto;
    }
    .slide-content h4 {
        margin-bottom: 3px;
        margin-top: 0;
    }
    .slide-footer {
        position: absolute;
        bottom: 0;
        left: 20%;
        width: 78%;
        height: 20%;
        margin: 1%;
    }
    /* Scrollbars */
    .slide-content::-webkit-scrollbar {
        width: 5px;
    }
    .slide-content::-webkit-scrollbar-thumb:vertical {
        margin: 5px;
        background-color: #999;
        -webkit-border-radius: 5px;
    }
    .slide-content::-webkit-scrollbar-button:start:decrement,
    .slide-content::-webkit-scrollbar-button:end:increment {
        height: 5px;
        display: block;
    }
</style>

<div class="content-wrapper" style="min-height: 946px;">

    <section class="content-header">
        <h1>
            <i class="fa fa-bus"></i> <?php echo $this->lang->line('transport'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('reports/_finance'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>

                    <form role="form" action="<?php echo site_url('report/tva') ?>" method="post" class="">
                        <div class="box-body row">

                            <?php echo $this->customlib->getCSRF(); ?>

                            <div class="col-sm-6 col-md-3" >
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('search') . " " . $this->lang->line('type'); ?></label>
                                    <select class="form-control" name="search_type" onchange="showdate(this.value)">

                                        <?php foreach ($searchlist as $key => $search) {
                                            ?>
                                            <option value="<?php echo $key ?>" <?php
                                            if ((isset($search_type)) && ($search_type == $key)) {

                                                echo "selected";
                                            }
                                            ?>><?php echo $search ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('search_type'); ?></span>
                                </div>
                            </div>

                            <div id='date_result'>

                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>


                    <div class="">
                        <div class="box-header ptbnull"></div>
                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><i class="fa fa-money"></i><b style="color: black">Déclaration de TVA</b></h3>
                        </div>
                        <div class="box-body table-responsive">
                            <div class="download_label">
                                <div class="col-md-4 col-xs-2 col-sm-6">
                                    <img style="width: 150px; height: 70px !important;" src="<?= base_url() . "/uploads/school_content/admin_logo/" . $sch_setting->admin_logo ?>" alt="Image banniere" />
                                </div>
                                <br/><br/><br/><br/>
                                <?php
                                echo "Déclaration de TVA <br/><br/>";
                                echo "period:"; $this->customlib->get_postmessage();
                                ;
                                ?></div>


                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Type TVA</th>
                                    <th>Montant (FCFA)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $total_collectee = 0;
                                $total_deductible = 0;

                                if ($result && $result->num_rows() > 0):
                                    foreach ($result->result_array() as $row):
                                        if ($row['type_tva'] == 'TVA collectée') $total_collectee = $row['montant'];
                                        if ($row['type_tva'] == 'TVA déductible') $total_deductible = $row['montant'];
                                        ?>
                                        <tr>
                                            <td><?= $row['type_tva'] ?></td>
                                            <td><?= number_format($row['montant'], 2, ',', ' ') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="fw-bold">
                                        <td>TVA à payer / Crédit TVA</td>
                                        <td><?= number_format(($total_collectee - $total_deductible), 2, ',', ' ') ?></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2">Aucune donnée disponible</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>



                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
</section>
</div>

<script>
    <?php
    if ($search_type == 'period') {
    ?>

    $(document).ready(function () {
        showdate('period');
    });

    <?php
    }
    ?>

</script>

