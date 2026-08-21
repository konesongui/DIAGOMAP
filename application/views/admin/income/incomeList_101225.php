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

                <!-- left column -->
            <?php } ?>

            <div class="col-md-12<?php

            if ($this->rbac->hasPrivilege('caisse', 'can_add')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title">Caisses</h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('caisse', 'can_add')) { ?>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#addCaisseModal">
                                    <i class="fa fa-plus"></i> Ajouter une caisse
                                </button>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- /.box-header -->
                <!-- general form elements -->
                <!--<div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title">Caisses</h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>-->

                   <!-- /.box-header -->
                    <div class="box-body">
                        <?php if ($this->session->flashdata('msg')) { ?>
                            <?php echo $this->session->flashdata('msg') ?>
                        <?php } ?>
                        <?php if (isset($error_message)) { echo "<div class='alert alert-danger'>" . $error_message . "</div>"; } ?>


                        <div class="table-responsive mailbox-messages">
                            <table class="table table-striped table-bordered table-hover income-list" data-export-title="<?php echo $this->lang->line('income_list'); ?>">
                                <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('date'); ?></th>
                                    <th><?php echo $this->lang->line('amount'); ?></th>
                                    <th>Solde restant</th>
                                    <th>Status</th>
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
<!-- Modal Formulaire -->
<div class="modal fade" id="addCaisseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Ajouter une caisse</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formCaisse" action="<?php echo base_url() ?>admin/income" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <?php if ($this->session->flashdata('msg')) { ?>
                        <?php echo $this->session->flashdata('msg') ?>
                    <?php } ?>
                    <?php if (isset($error_message)) { echo "<div class='alert alert-danger'>" . $error_message . "</div>"; } ?>
                    <?php echo $this->customlib->getCSRF(); ?>

                    <!-- Tous les champs originaux gardés -->
                    <div class="form-group" hidden>
                        <label for="inc_head_id"><?php echo $this->lang->line('income_head'); ?></label><small class="req"> *</small>
                        <select id="inc_head_id" name="inc_head_id" class="form-control">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($incheadlist as $inchead) { ?>
                                <option value="<?php echo $inchead['id'] ?>"<?php if (set_value('inc_head_id') == $inchead['id']) { echo "selected"; } ?>>
                                    <?php echo $inchead['income_category'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group" hidden>
                        <label>User</label>
                        <input id="user" name="user" type="text" class="form-control"
                               value="<?php echo $this->customlib->getAdminSessionUserName(); ?>" />
                    </div>

                    <div class="form-group">
                        <label for="name"><?php echo $this->lang->line('name'); ?><small class="req"> *</small></label>
                        <input id="name" name="name" type="text" class="form-control"
                               value="<?php echo set_value('name'); ?>" />
                    </div>

                    <div class="form-group" hidden>
                        <label><?php echo $this->lang->line('invoice_no'); ?></label>
                        <input id="invoice_no" name="invoice_no" type="text" class="form-control"
                               value="<?php echo set_value('invoice_no'); ?>" />
                    </div>

                    <div class="form-group">
                        <label for="date"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                        <input id="date" name="date" type="text" class="form-control date"
                               value="<?php echo set_value('date'); ?>" readonly="readonly" />
                    </div>

                    <div class="form-group">
                        <label for="amount"><?php echo $this->lang->line('amount'); ?><small class="req"> *</small></label>
                        <input id="amount" name="amount" type="number" class="form-control"
                               value="<?php echo set_value('amount'); ?>" />
                    </div>

                    <div class="form-group" hidden>
                        <label><?php echo $this->lang->line('attach_document'); ?></label>
                        <input id="documents" name="documents" type="file" class="filestyle form-control"
                               data-height="40" value="<?php echo set_value('documents'); ?>" />
                    </div>

                    <div class="form-group" hidden>
                        <label><?php echo $this->lang->line('description'); ?></label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    ( function ( $ ) {
        'use strict';
        $(document).ready(function () {
            initDatatable('income-list','admin/income/getincomelist',[],[],10);
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

<script>
    $(document).on('click', '.toggle-status', function () {
        const caisseId = $(this).data('id');
        const currentStatus = $(this).data('status');

        if (confirm("Changer le statut de la caisse ?")) {
            $.ajax({
                url: "<?php echo site_url('admin/income/toggle_status'); ?>",
                type: "POST",
                dataType: "json",
                data: {
                    id: caisseId,
                    status: currentStatus
                },
                success: function (res) {
                    alert(res.message);
                    location.reload(); // ou $('#example').DataTable().ajax.reload();
                },
                error: function () {
                    alert("Erreur serveur.");
                }
            });
        }
    });
</script>
