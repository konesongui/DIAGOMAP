<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-ioxhost"></i> <?php echo $this->lang->line('front_office'); ?></h1>
    </section>
    <section class="content">
        <div class="row">
            <?php if ($this->rbac->hasPrivilege('categorie_salaire', 'can_add')) {?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Ajouter une catégorie salariale</h3>
                        </div><!-- /.box-header -->
                        <form id="form1" action="<?php echo site_url('admin/categorie') ?>"   method="post" accept-charset="utf-8" enctype="multipart/form-data" >
                            <div class="box-body">
                                <?php echo $this->session->flashdata('msg') ?>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Catégorie</label><small class="req"> *</small>

                                    <!--  <select name="purpose" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?> </option>
                                        <?php foreach ($Purpose as $key => $value) {?>

                                            <option value="<?php print_r($value['visitors_purpose']);?>"<?php if (set_value('purpose') == $value['visitors_purpose']) {?>selected=""<?php }?>><?php print_r($value['visitors_purpose']);?></option>
                                        <?php }?>

                                    </select>-->
                                    <input type="text" class="form-control" value="<?php echo set_value('categorie'); ?>" name="categorie">

                                    <span class="text-danger"><?php echo form_error('categorie'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="pwd">Salaire</label>  <small class="req"> *</small>
                                    <input type="text" class="form-control" value="<?php echo set_value('salaire'); ?>" name="salaire">
                                    <span class="text-danger"><?php echo form_error('salaire'); ?></span>
                                </div>
                                <div class="form-group" hidden>
                                    <label for="pwd">Nom du candidat</label>  <small class="req"> *</small>
                                    <input type="text" class="form-control" value="<?php echo set_value('name'); ?>" name="name">
                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                </div>

                                <div class="form-group" hidden>
                                    <label for="pwd"><?php echo $this->lang->line('phone'); ?></label>
                                    <input type="text" class="form-control" value="<?php echo set_value('contact'); ?>" name="contact">

                                </div>
                                <div class="form-group" hidden>
                                    <label for="pwd"><?php echo $this->lang->line('icard'); ?></label>
                                    <input type="text" class="form-control" value="<?php echo set_value('id_proof'); ?>" name="id_proof">

                                </div>
                                <div class="form-group" hidden>
                                    <label for="email"><?php echo $this->lang->line('number_of_person'); ?></label>
                                    <input type="text" class="form-control" value="<?php echo set_value('pepples'); ?>" name="pepples">
                                </div>
                                <div class="form-group" hidden>
                                    <div class="form-group">
                                        <label for="pwd"><?php echo $this->lang->line('date'); ?></label><input type="text" id="date" class="form-control date" value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>"  name="date" readonly="">
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                </div>
                                <div class="form-group" hidden>
                                    <label for="pwd">Status du dossier</label>
                                    <div class="bootstrap-timepicker">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <select name="status" class="form-control">
                                                    <option value=""><?php echo $this->lang->line('select'); ?> </option>
                                                    <option value="En attente">En attente </option>
                                                    <option value="Accepter">Accepter </option>
                                                    <option value="Refuser">Refuser</option>
                                                </select>
                                                <!--<input type="text" name="status" class="form-control" id="status" value="<?php echo set_value('status'); ?>">-->
                                                <div class="input-group-addon">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-danger"><?php echo form_error('time'); ?></span>
                                </div>
                                <div class="form-group" hidden>
                                    <label for="pwd">Heure</label>
                                    <div class="bootstrap-timepicker">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="in_time" class="form-control timepicker" id="stime_" value="<?php echo set_value('in_time'); ?>">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-clock-o"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-danger"><?php echo form_error('out_time'); ?></span>
                                </div>
                                <div class="form-group" hidden>
                                    <label for="pwd"><?php echo $this->lang->line('note'); ?></label>
                                    <textarea class="form-control" id="description" name="note" name="note" rows="3"><?php echo set_value('note'); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('date'); ?></span>
                                </div>
                                <div class="form-group" hidden>
                                    <label for="exampleInputFile"><?php echo $this->lang->line('attach_document'); ?></label>
                                    <div><input class="filestyle form-control" type='file' name='file'  />
                                    </div>
                                    <span class="text-danger"><?php echo form_error('file'); ?></span></div>
                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                <button type="reset" class="btn btn-secondary bg-red">Annuler</button>
                            </div>
                        </form>
                    </div>

                </div><!--/.col (right) -->
                <!-- left column -->
            <?php }?>

            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('categorie_salaire', 'can_add')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Liste des catégories</h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('visitor'); ?> <?php echo $this->lang->line('list'); ?></div>
                        <div class="mailbox-messages table-responsive">
                            <table class="table table-hover table-striped table-bordered example">
                                <thead>
                                <tr>
                                    <th>Catégorie</th>
                                    <th>Salaire</th>
                                    <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (empty($categorie_list)) {
                                    ?>

                                    <?php
                                } else {
                                    foreach ($categorie_list as $key => $value) {

                                        ?>
                                        <tr>
                                            <td class="mailbox-name"><?php echo $value['categorie']; ?></td>
                                            <td class="mailbox-name"><?php echo $value['salaire']; ?></td>



                                            <!--<td class="mailbox-name"> <?php echo $value['status']; ?></td>-->

                                            <td class="mailbox-date pull-right white-space-nowrap">
                                                <!-- <a data-placement="left"  onclick="getRecord(<?php echo $value['id']; ?>)" class="btn btn-default btn-xs" data-target="#visitordetails" data-toggle="modal"  title="<?php echo $this->lang->line('view'); ?>"><i class="fa fa-reorder"></i></a>-->
                                                <?php if ($value['image'] !== "") {?>
                                                    <!--<a data-placement="left" href="<?php echo base_url(); ?>admin/categorie/download/<?php echo $value['image']; ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('download'); ?>">
                                                        <i class="fa fa-download"></i>
                                                    </a>-->  <?php }?>
                                                <?php if ($this->rbac->hasPrivilege('categorie_salaire', 'can_edit')) {?>
                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/categorie/edit/<?php echo $value['id']; ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php }
                                                ?>

                                                <?php if ($this->rbac->hasPrivilege('categorie_salaire', 'can_delete')) {
                                                    ?>
                                                    <?php if ($value['image'] !== "") {?><a data-placement="left" href="<?php echo base_url(); ?>admin/categorie/imagesdelete/<?php echo $value['id']; ?>/<?php echo $value['image']; ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');" data-original-title="<?php echo $this->lang->line('delete'); ?>">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                    <?php } else {?>
                                                        <a data-placement="left" href="<?php echo base_url(); ?>admin/categorie/delete/<?php echo $value['id']; ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');" data-original-title="<?php echo $this->lang->line('delete'); ?>">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </td>

                                        </tr>
                                        <?php
                                    }
                                }
                                ?>

                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) col-8 end-->
            <!-- right column -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- new END -->
<div id="visitordetails" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('details'); ?></h4>
            </div>
            <div class="modal-body" id="getdetails">

            </div>
        </div>
    </div>
</div>
</div><!-- /.content-wrapper -->
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.js"></script>

<script type="text/javascript">

    $(function () {

        $(".timepicker").timepicker({

        });
    });

    function getRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/files/details/' + id,
            success: function (result) {

                $('#getdetails').html(result);
            }

        });
    }

</script>
