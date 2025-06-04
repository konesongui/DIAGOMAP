<style type="text/css">
    @media print
    {
        .no-print, .no-print *
        {
            display: none !important;
        }
    }
</style>

<div class="content-wrapper" style="min-height: 946px;">  
    <section class="content-header">
        <h1><i class="fa fa-sitemap"></i> <?php echo $this->lang->line('human_resource'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">   
            <?php if (($this->rbac->hasPrivilege('sanction', 'can_add')) || ($this->rbac->hasPrivilege('sanction', 'can_edit'))) { ?>
                <div class="col-md-4">     
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Ajouter une sanction</h3>
                        </div> 
                        <form id="form1" action="<?php echo site_url('admin/sanction/sanction') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8"  enctype="multipart/form-data">
                            <div class="box-body">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php echo $this->session->flashdata('msg') ?>
                                <?php } ?>        
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="form-group">
                                    <label>Role</label><small class="req"> *</small>
                                    <select name="role" id="role"  class="form-control" onchange="getEmployeeName(this.value)">
                                        <option value="" ><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($staffrole as $rolekey => $rolevalue) {
                                            ?>
                                            <option value="<?php echo $rolevalue["id"] ?>"><?php echo $rolevalue["type"] ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('empname'); ?></span>



                                </div>
                                <div class="form-group">
                                    <label>Nom de l'employee</label><small class="req"> *</small>
                                    <select name="empname" id="empname" value=""onchange="   getLeaveTypeDDL(this.value)"  class="form-control">
                                        <option value="" selected><?php echo $this->lang->line('select') ?></option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('empname'); ?></span>
                                </div>

                                <span class="text-danger"><?php echo form_error('empname'); ?></span>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Titre</label><small class="req"> *</small>
                                    <input autofocus="" id="type"  name="type" placeholder="" type="text" class="form-control"  value="<?php
                                    if (isset($result)) {
                                        echo $result["designation"];
                                    }
                                    ?>" />
                                    <span class="text-danger"><?php echo form_error('type'); ?></span>

                                    <input autofocus="" id="type"  name="designationid" placeholder="" type="hidden" class="form-control"  value="<?php
                                    if (isset($result)) {
                                        echo $result["id"];
                                    }
                                    ?>" />
                                </div>
                                <div class="form-group">
                                    <label>Action</label><small class="req"> *</small>
                                    <select name="action" id="action" value="<?php if (isset($result)) { echo $result["action"]; } ?>" class="form-control">
                                        <option value="" selected><?php echo $this->lang->line('select') ?></option>
                                    <option value="Avertissement">Avertissement</option>
                                    <option value="Mise à pied disciplinaire">Mise à pied disciplinaire</option>
                                        <option value="Licenciement">Licenciement</option>
                                        <option value="Rétrogadation">Rétrogadation</option>
                                        <option value="Suspension">Suspension</option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('action'); ?></span>
                                </div>

                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('date'); ?></label><small class="req"> *</small>
                                        <input id="date" required name="date" placeholder="" type="date" class="form-control"  value="<?php echo set_value('date') ?>" />
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>

                                <div class="form-group">
                                    <label><?php echo $this->lang->line('reason'); ?></label><br/>
                                    <textarea name="reason" id="reason" style="resize: none;" rows="4" class="form-control"></textarea>

                                </div>
                                <div class="form-group" hidden>
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('active'); ?> <?php echo $this->lang->line('status'); ?></label>
                                <br/>
                                <label class="radio-inline">
                                     <input type="radio" checked value="yes" <?php
                                if ((isset($result)) && ($result["is_active"] == "yes")) {
                                    echo "checked";
                                }
                                ?> name="status"><?php echo $this->lang->line('yes'); ?>
                                 </label>
                                <label class="radio-inline">
                                <input type="radio" value="no" <?php
                                if ((isset($result)) && ($result["is_active"] == "no")) {
                                    echo "checked";
                                }
                                ?> name="status"><?php echo $this->lang->line('no'); ?>
                            </label>
                              </div>

                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                                <button type="reset" class="btn btn-secondary bg-red">Annuler</button>
                            </div>
                        </form>
                    </div>   
                </div> 
            <?php } ?> 
            <div class="col-md-<?php
            if (($this->rbac->hasPrivilege('sanction', 'can_add')) || ($this->rbac->hasPrivilege('sanction', 'can_edit'))) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">              
                <div class="box box-primary" id="tachelist">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Liste des sanctions disciplinaires</h3>
                    </div>
                    <div class="box-body">
                        <div class="mailbox-controls">
                        </div>
                        <div class="table-responsive mailbox-messages">
                            <div class="download_label">Liste des sanctions disciplinaires</div>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>

                                       <!-- <th>Role</th>-->
                                        <th>Nom</th>
                                        <th>Titre</th>
                                        <th>Action</th>
                                        <th>Details</th>
                                        <th>Date</th>
                                       <!--<th><?php echo $this->lang->line('active'); ?> <?php echo $this->lang->line('status'); ?></th>-->
                                        <th class="text-right no-print"><?php echo $this->lang->line('action'); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($designation as $value) {
                                        $status = "";

                                        if ($value["is_active"] == "yes") {

                                            $status = "Active";
                                        } else {
                                            $status = "Inactive";
                                        }
                                        ?>
                                        <tr>

                                            <!--<td class="mailbox-name"> <?php echo $value['role'] ?></td>-->
                                            <td class="mailbox-name"> <?php echo $value['empname']; ?></td>
                                            <td class="mailbox-name"> <?php echo $value['designation'] ?></td>
                                            <td class="mailbox-name"> <?php echo $value['action'] ?></td>
                                            <td class="mailbox-name"> <?php echo $value['reason'] ?></td>
                                            <td class="mailbox-name"> <?php echo $value['date'] ?></td>

                                          <!-- <td><?php echo $this->lang->line($value['is_active']) ?></td>-->
                                            <td class="mailbox-date pull-right no-print">
                                                <?php if ($this->rbac->hasPrivilege('subject_group', 'can_edit')) { ?>
                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/sanction/sanctionedit/<?php echo $value['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } if ($this->rbac->hasPrivilege('subject_group', 'can_delete')) { ?>
                                                    <a data-placement="left" href="<?php echo base_url(); ?>admin/sanction/sanctiondelete/<?php echo $value['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>')";>
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $count++;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="">
                        <div class="mailbox-controls">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>


<script>
    function getEmployeeName(role) {
        var ne = "";
        var base_url = '<?php echo base_url() ?>';
        $("#empname").html('<option value=><?php echo $this->lang->line('select') ?></option>');
        var div_data = "";
        $.ajax({
            type: "POST",
            url: base_url + "admin/staff/getEmployeeByRole",
            data: {'role': role},
            dataType: "json",
            success: function (data) {
                $.each(data, function (i, obj)
                {
                    div_data += "<option value='" + obj.name +  "' >" + obj.name + " " + obj.surname + " " + "(" + obj.employee_id + ")</option>";
                });

                $('#empname').append(div_data);
            }
        });
    }
</script>