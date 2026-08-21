<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-book"></i> <?php echo $this->lang->line('staff'); ?> <small><?php echo $this->lang->line('student1'); ?></small></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info" style="padding:5px;">
                    <div class="box-header with-border">
                        <h3 class="box-title">Importer les clients</h3>
                        <div class="pull-right box-tools">
                            <a href="<?php echo site_url('admin/clients/exportformat') ?>">
                                <button class="btn btn-primary btn-sm"><i class="fa fa-download"></i> <?php echo $this->lang->line('dl_sample_import'); ?></button>
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if ($this->session->flashdata('msg')) { ?> <div>  <?php echo $this->session->flashdata('msg') ?> </div> <?php } ?>
                        <br/>
                        1. <?php echo $this->lang->line('import_staff_step1'); ?><br/>
                        2. <?php echo $this->lang->line('import_staff_step2'); ?><br/>

                        <hr/></div>
                    <div class="box-body table-responsive" style="overflow-x:auto;">
                        <table class="table table-striped table-bordered table-hover" id="sampledata">
                            <thead>
                                <tr>
                                    <?php
                                    foreach ($field as $key => $value) {
                                        if ($value == 'staff_id' || $value == 'first_name' || $value == 'email' || $value == 'gender' || $value == 'date_of_birth') {
                                            $req = "<span class='text-red'>*</span>";
                                        } else {
                                            $req = "";
                                        }
                                        ?>
                                        <th><?php echo "<span>" . $this->lang->line($value) . "</span>" . $req; ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php foreach ($field as $key => $value) {
                                        ?>
                                        <td><?php echo "XYZ" ?></td>
                                    <?php } ?>
                                </tr>
                            </tbody>

                        </table>
                    </div>
                    <hr/>
                    <form action="<?php echo site_url('admin/clients/import') ?>"  id="employeeform" name="employeeform" method="post" enctype="multipart/form-data">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>

                            <div class="row">
                                <label for="file">Importer fichier Excel :</label>
                                <input type="file" class="filestyle form-control" name="file" accept=".xls,.xlsx" required>

                                <div class="col-md-6 pt20">
                                    <button type="submit" class="btn btn-info pull-right"> <?php echo $this->lang->line('staff') . " " . $this->lang->line('import'); ?></button>
                                </div>

                            </div>
                        </div>


                    </form>

                    <div>



                    </div>
                </div>
                </section>
            </div>

