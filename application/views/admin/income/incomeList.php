<?php
$conn = new mysqli("localhost","root","","diao");
$sql = "SELECT SUM(amount) AS total_amount  FROM income_processing Where amount > 0 ";

$result = $conn->query($sql);


?>

<?php
$conn = new mysqli("localhost","root","","diao");
$sql1 = "SELECT SUM(amount) AS total_amount  FROM income Where deleted = 1 ";

$result1 = $conn->query($sql1);


?>
<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$language = $this->customlib->getLanguage();
$language_name = $language["short_code"];
?>
<style type="text/css">

    @media print {
        .no-print {
            visibility: hidden !important;
            display:none !important;
        }
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content-header">
        <h1>
            <i class="fa fa-usd"></i> <?php echo $this->lang->line('income'); ?></h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php
            if ($this->rbac->hasPrivilege('caisse', 'can_add')) {
                ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('add_income'); ?></h3>
                        </div><!-- /.box-header -->

                        <form id="form1" action="<?php echo base_url() ?>admin/income"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                            <div class="box-body">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php echo $this->session->flashdata('msg') ?>
                                <?php } ?>
                                <?php
                                if (isset($error_message)) {
                                    echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                                }
                                ?>
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('income_head'); ?></label><small class="req"> *</small>

                                    <select autofocus="" id="inc_head_id" name="inc_head_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($incheadlist as $inchead) {
                                            ?>
                                            <option value="<?php echo $inchead['id'] ?>"<?php
                                            if (set_value('inc_head_id') == $inchead['id']) {
                                                echo "selected = selected";
                                            }
                                            ?>><?php echo $inchead['income_category'] ?></option>

                                            <?php
                                            //$count++;
                                        }
                                        ?>
                                    </select><span class="text-danger"><?php echo form_error('inc_head_id'); ?></span>

                                </div>
                                <div class="form-group" hidden>
                                    <label for="exampleInputEmail1">User<small class="req"> *</small></label>
                                    <input id="name" name="user" placeholder="" type="text" class="form-control"  value="<?php echo $this->customlib->getAdminSessionUserName(); ?>" />
                                    <span class="text-danger"><?php echo form_error('user'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('name'); ?><small class="req"> *</small></label>
                                    <input id="name" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" />
                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                </div>

                                <div class="form-group" hidden>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('invoice_no'); ?></label>
                                    <input id="invoice_no" name="invoice_no" placeholder="" type="text" class="form-control"  value="<?php echo set_value('invoice_no'); ?>" />
                                    <span class="text-danger"><?php echo form_error('invoice_no'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                                    <input id="date" name="date" placeholder="" type="text" class="form-control date"  value="<?php echo set_value('date'); ?>" readonly="readonly" />
                                    <span class="text-danger"><?php echo form_error('date'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('amount'); ?><small class="req"> *</small></label>
                                    <input id="amount" name="amount" placeholder="" type="text" class="form-control"  value="<?php echo set_value('amount'); ?>" />
                                    <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                </div>
                                <div class="form-group" hidden>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('attach_document'); ?></label>
                                    <input id="documents" name="documents" placeholder="" type="file" class="filestyle form-control" data-height="40"  value="<?php echo set_value('documents'); ?>" />
                                    <span class="text-danger"><?php echo form_error('documents'); ?></span>
                                </div>
                                <div class="form-group" hidden>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea class="form-control" id="description" name="description" placeholder="" rows="3" placeholder="Enter ..."><?php echo set_value('description'); ?></textarea>
                                    <span class="text-danger"></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Status</label><small class="req"> *</small>

                                    <select autofocus="" id="status" name="status" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <option value="Ouvert">Ouvert</option>
                                        <option value="Fermer">Fermer</option>

                                    </select><span class="text-danger"><?php echo form_error('status'); ?></span>

                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Type du journal</label><small class="req"> *</small>

                                    <select autofocus="" id="journal_id" name="journal_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($journal as $type) {
                                            ?>
                                            <option value="<?php echo $type['id'] ?>"<?php
                                            if (set_value('type_journal') == $type['type_journal']) {
                                                echo "selected = selected";
                                            }
                                            ?>><?php echo $type['type_journal'] ?></option>

                                            <?php
                                            //$count++;
                                        }
                                        ?>
                                    </select><span class="text-danger"><?php echo form_error('inc_head_id'); ?></span>

                                </div>
                            </div>
                            <!-- /.box-body -->

                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                <button type="reset" class="btn btn-secondary bg-red">Annuler</button>
                            </div>
                        </form>
                    </div>

                </div><!--/.col (right) -->
                <!-- left column -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('caisse', 'can_add')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title"><?php echo $this->lang->line('income_list'); ?></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>

                    <!--<div class="box-header ptbnull">

                        <?php while ($row1 = $result1->fetch_object()): ?>
                            <h3 class="box-title titlefix"> <?php echo $this->lang->line(''); ?>  <b>Caisse Géneral : <?php echo $row1->total_amount ?>  FCFA </b></h3>
                        <?php endwhile; ?>
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line(''); ?>  <b> CAISSE GENERALE : <?= number_format(floatval($incomeTotal->amount??0), 0, ',', ' ') ;?>  FCFA </b></h3>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php while ($row = $result->fetch_object()): ?>
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line(''); ?>  <b> Total Réappro : <?php echo $row->total_amount ?>  FCFA </b></h3>
                        <?php endwhile; ?>
                        <div class="box-tools pull-right">
                        </div>
                    </div>--><!-- /.box-header -->
                    <div class="box-body">


                        <div class="table-responsive mailbox-messages">
                            <table class="table table-striped table-bordered table-hover income-list" data-export-title="<?php echo $this->lang->line('income_list'); ?>">
                                <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('name'); ?>
                                    </th>

                                    <th><?php echo $this->lang->line('date'); ?>
                                    </th>
                                    <th><?php echo $this->lang->line('income_head'); ?>
                                    </th>
                                    <th><?php echo $this->lang->line('amount'); ?>
                                    </th>
                                    <th>Solde restant
                                    </th>

                                    <!-- <th>Users
                                     </th>-->
                                    <th>Status
                                    </th>
                                    <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>

                <!-- increase form modal -->
                <?php
                if ($this->rbac->hasPrivilege('caisse', 'can_add')) {
                    ?>
                    <div id="increaseForm" class="modal fade" data-backdrop="false">
                        <div class="modal-dialog">
                            <div class="modal-content" id="increaseFormContent">

                            </div>
                        </div>
                    </div>

                    <div id="viewIncreaseList" class="modal fade" data-backdrop="false">
                        <div class="modal-dialog">
                            <div class="modal-content" id="ViewIncreaseContent">

                            </div>
                        </div>
                    </div>

                <?php } ?>


                <!-- increase form modal -->


            </div><!--/.col (left) -->
            <!-- right column -->

        </div>

    </section><!-- /.content -->
</div><!--/.content-wrapper-->

<script>
    ( function ( $ ) {
        'use strict';
        $(document).ready(function () {
            initDatatable('income-list','admin/income/getincomelist',[],[],50);
        });
    } ( jQuery ) )


    var base_url = '<?php echo base_url() ?>';


    /*
    ALL ACTIONS BUTTONS ABOUT PERMISSIONS DATATABLE
    */
    // Function to set a increase
    function form_increase(id) {


        console

        $.ajax({
            'url'   : base_url + 'Income/form_increase', // controller link
            'type'  : 'GET', // method used to send data
            'data'  : {
                'id'        : id, // row id
            },
            'success': function (data) { //probably this request will return anything, it'll be put in var "data"

                // Get the html container where to display the loaded form
                var increase_form_content = $('#increase_form_content'); //jquery selector (get element by id)

                // Process only if any data has been loaded
                if (data) {

                    // Display the loaded data
                    increase_form_content.html(data);

                } // Fin si

            } // End success event
        });
    } // End function



    // Function to load on (edit or add) button click
    $(document).on('click', `.increaseAmount`, function(e) {

        // Desable default event
        e.preventDefault();

        // Get the selected row id
        var rowID = $(this).attr('data-row-id');

        // console.log(base_url);

        // AJAX function to load the form data to display
        $.ajax({
            // AJAX Call options
            url: base_url + '/admin/income/formIncrease',
            type: "POST",
            data: {
                'rowID': rowID,
            },
            // On 'Success' Event
            success: function(data) {

                // Process only if any data has been loaded
                if(data) {
                    // Display the loaded data
                    $(`#increaseForm #increaseFormContent`).html(data);
                } // End if

            }, // End success event

        });

    });


    /**
     * PROCESS CLICK FORM
     * On click on the 'submit' button
     *
     * @param formData
     *
     * @return toast
     */
    $(document).on("click", `#submitBTN`, function (e) {
        // Cancel default event
        e.preventDefault();
        // Call insert function
        initPostAjaxRequest();
    });


    // Function to post the form data to the server
    let initPostAjaxRequest = () => {
        // Get all the required data
        var formElement = $('#increaseFormID'),
            formData = new FormData(formElement[0]);

        // AJAX Function to post the form data to database
        $.ajax({
            type: "POST",
            url: base_url + 'admin/income/setIncrease',
            processData: false,
            contentType: false,
            data: formData,

            // On 'Success' Event
            success: function(data) {
                // Get the data value
                let serverResponse = JSON.parse(data);

                // Check the response type
                if(serverResponse.type === 'success')
                {   // Dismiss the form modal
                    $(`#increaseForm`).modal("hide");

                    // Push the server response as toast
                    toastr.success(serverResponse.message);

                    // Reload the datatable
                    let incomeTable = $('.income-list').DataTable(); // Assurez-vous que la table utilise DataTables
                    incomeTable.ajax.reload(null, false); // Recharge les données sans réinitialiser la pagination

                    location.reload(true);
                } // End if
                else if(serverResponse.type === 'warning')
                {
                    // Push the server response as toast
                    toastr.warning(serverResponse.message);
                } // End else if
                else
                {   // Push the server response as toast
                    toastr.error(serverResponse.message);
                } // End else

            }, // End Success Event
        });
    }


    // Function to load on (edit or add) button click
    $(document).on('click', `.viewIncrease`, function(e) {

        // Desable default event
        e.preventDefault();

        // Get the selected row id
        var rowID = $(this).attr('data-row-id');

        // console.log(base_url);

        // AJAX function to load the form data to display
        $.ajax({
            // AJAX Call options
            url: base_url + '/admin/income/listIncrease',
            type: "POST",
            data: {
                'rowID': rowID,
            },
            // On 'Success' Event
            success: function(data) {

                // Process only if any data has been loaded
                if(data) {
                    // Display the loaded data
                    $(`#viewIncreaseList #ViewIncreaseContent`).html(data);
                } // End if

            }, // End success event

        });

    });


</script>