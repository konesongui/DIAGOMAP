<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-book"></i> <?php echo $this->lang->line('library'); ?> </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Modifier la formation</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->

                    <form id="form1" action="<?php echo site_url('admin/training/edit/' . $id) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
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
                            <input  type="hidden" name="id" value="<?php echo set_value('id', $editbook['id']); ?>" >
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Nom de la formation <small class="req"> *</small></label>
                                <input autofocus="" id="book_title" name="book_title" placeholder="" type="text" class="form-control"  value="<?php echo set_value('book_title', $editbook['book_title']); ?>" />
                                <span class="text-danger"><?php echo form_error('book_title'); ?></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Type de formation</label>
                                <input id="book_no" name="book_no" placeholder="" type="text" class="form-control"  value="<?php echo set_value('book_no', $editbook['book_no']); ?>" />
                                <span class="text-danger"><?php echo form_error('book_no'); ?></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Département</label>
                                <input id="dept" name="dept" placeholder="" type="text" class="form-control"  value="<?php echo set_value('dept', $editbook['dept']); ?>" />
                                <span class="text-danger"><?php echo form_error('dept'); ?></span>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Date de début</label>
                                <input id="isbn_no" name="isbn_no" placeholder="" type="text" class="form-control date"  value="<?php echo set_value('isbn_no', $editbook['isbn_no']); ?>" />
                                <span class="text-danger"><?php echo form_error('isbn_no'); ?></span>
                            </div>


                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Date de fin</label>

                                <input id="amount" name="publish" placeholder="" type="text" class="form-control date"  value="<?php echo set_value('publish', $editbook['publish']); ?>" />
                                <span class="text-danger"><?php echo form_error('publish'); ?></span>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Nombre de canditat</label>
                                <input id="amount" name="author" placeholder="" type="number" class="form-control"  value="<?php echo set_value('author', $editbook['author']); ?>" />
                                <span class="text-danger"><?php echo form_error('author'); ?></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Liste de canditats</label>
                                <textarea class="form-control" id="subject" name="subject" placeholder="" rows="3" placeholder=""><?php echo set_value('subject', $editbook['subject']); ?></textarea>

                                <!--<input id="subject" name="subject" placeholder="" type="text" class="form-control"  value="<?php echo set_value('subject', $editbook['subject']); ?>" />-->
                                <span class="text-danger"><?php echo form_error('subject'); ?></span>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Nom de la structure</label>
                                <input id="rack_no" name="rack_no" placeholder="" type="text" class="form-control"  value="<?php echo set_value('rack_no', $editbook['rack_no']); ?>" />
                                <span class="text-danger"><?php echo form_error('rack_no'); ?></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Nom du formateur</label>
                                <input id="respo" name="respo" placeholder="" type="text" class="form-control"  value="<?php echo set_value('respo', $editbook['respo']); ?>" />
                                <span class="text-danger"><?php echo form_error('respo'); ?></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Qualification</label>
                                <input id="quali" name="quali" placeholder="" type="text" class="form-control"  value="<?php echo set_value('quali', $editbook['quali']); ?>" />
                                <span class="text-danger"><?php echo form_error('quali'); ?></span>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Téléphone</label>
                                <input id="contact" name="contact" placeholder="" type="text" class="form-control"  value="<?php echo set_value('contact', $editbook['contact']); ?>" />
                                <span class="text-danger"><?php echo form_error('contact'); ?></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Adresse</label>


                                <input id="adresse" name="adresse"  placeholder="" type="text" class="form-control"  value="<?php echo set_value('adresse', $editbook['adresse']); ?>" />
                                <span class="text-danger"><?php echo form_error('adresse'); ?></span>
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Email</label>


                                <input id="postdate" name="postdate"  placeholder="" type="text" class="form-control date"  value="<?php echo set_value('postdate', $this->customlib->dateformat($editbook['postdate'])); ?>" />
                                <span class="text-danger"><?php echo form_error('postdate'); ?></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                                <textarea class="form-control" id="description" name="description" placeholder="" rows="3" placeholder=""><?php echo set_value('description', $editbook['description']); ?></textarea>
                                <span class="text-danger"><?php echo form_error('description'); ?></span>
                            </div>
                        </div><!-- /.box-body -->

                        <div class="box-footer">

                            <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </form>
                </div>

            </div><!--/.col (right) -->


        </div>
        <div class="row">
            <!-- left column -->
            <!-- right column -->
            <div class="col-md-12">
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script>
    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });
</script>