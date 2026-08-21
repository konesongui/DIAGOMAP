<style>
    .file-input-wrapper {
        position: relative;
    }

    .file-preview {
        margin-top: 5px;
        padding: 5px;
        background-color: #f8f9fa;
        border-radius: 4px;
        border-left: 4px solid #28a745;
    }

    .status-cell {
        text-align: center;
        vertical-align: middle;
    }

    .status-icon {
        font-size: 20px;
        margin-right: 5px;
    }

    .status-text {
        font-size: 12px;
        font-weight: bold;
    }

    .upload-success {
        color: #28a745;
    }

    .upload-pending {
        color: #ffc107;
    }

    .upload-error {
        color: #dc3545;
    }

    .view-document {
        opacity: 1;
        transition: all 0.3s ease;
    }

    .view-document:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .document-uploaded {
        background-color: #f8fff9;
        border-left: 4px solid #28a745;
    }

    .document-pending {
        background-color: #fffbf8;
        border-left: 4px solid #ffc107;
    }

    #upload-summary {
        margin-top: 15px;
    }

    .progress-bar-container {
        margin-top: 10px;
    }

    .upload-progress {
        height: 6px;
        margin-top: 5px;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-sitemap"></i> <?php echo $this->lang->line('human_resource'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">

                    <form id="form1" action="<?php echo site_url('admin/staff/create') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                        <div class="box-body">
                            <div class="alert alert-info">
                                Staff email is their login username, password is generated automatically and send to staff email. Superadmin can change staff password on their staff profile page.

                            </div>
                            <div class="tshadow mb25 bozero">
                                <div class="box-tools pull-right pt3">
                                    <a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>admin/staff/import" autocomplete="off"><i class="fa fa-plus"></i> <?php echo $this->lang->line('import') . " " . $this->lang->line('staff') ?></a>
                                    <a href="<?php echo base_url() ?>admin/staff" type="button" class="btn btn-primary btn-xs" style="width: 99px;height: 23px">
                                        <i class="fa fa-arrow-left"></i> </a>

                                </div>
                                <h4 class="pagetitleh2"><?php echo $this->lang->line('basic_information'); ?> </h4>



                                <div class="around10">

                                    <?php if ($this->session->flashdata('msg')) { ?>
                                        <?php echo $this->session->flashdata('msg') ?>
                                    <?php } ?>
                                    <?php if (!empty($error_message)) { ?>
                                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                                    <?php } ?>
                                    <?php echo $this->customlib->getCSRF(); ?>

                                    <div class="row">
                                        <?php
                                        if (!$staffid_auto_insert) {
                                            ?>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('staff_id'); ?></label><small class="req"> *</small>
                                                    <input autofocus="" id="employee_id" name="employee_id"  placeholder="" type="text" class="form-control"  value="<?php echo set_value('employee_id') ?>" />
                                                    <span class="text-danger"><?php echo form_error('employee_id'); ?></span>
                                                </div>
                                            </div>

                                            <?php
                                        }
                                        ?>
                                        <div class="col-md-3">

                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Profil</label><small class="req"> *</small>
                                                <select id="role" name="role" class="form-control">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php
                                                    foreach ($roles as $key => $role) {
                                                        // 🔒 On saute complètement le rôle Super Admin
                                                        if (isset($role['is_superadmin']) && $role['is_superadmin']) {
                                                            continue;
                                                        }
                                                        ?>
                                                        <option value="<?php echo $role['id']; ?>"
                                                            <?php echo set_select('role', $role['id'], set_value('role')); ?>>
                                                            <?php echo $role["name"]; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>

                                                <span class="text-danger"><?php echo form_error('role'); ?></span>
                                            </div>
                                        </div>
                                        <?php if ($sch_setting->staff_designation) { ?>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('designation'); ?></label>

                                                    <select id="designation" name="designation" placeholder="" type="text" class="form-control" >
                                                        <option value="select"><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($designation as $key => $value) {
                                                            ?>
                                                            <option value="<?php echo $value["id"] ?>" <?php echo set_select('designation', $value['id'], set_value('designation')); ?> ><?php echo $value["designation"] ?></option>
                                                        <?php }
                                                        ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('designation'); ?></span>
                                                </div>
                                            </div>
                                        <?php } if ($sch_setting->staff_department) { ?>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('department'); ?></label>
                                                    <select id="department" name="department" placeholder="" type="text" class="form-control" >
                                                        <option value="select"><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($department as $key => $value) {
                                                            ?>
                                                            <option value="<?php echo $value["id"] ?>" <?php echo set_select('department', $value['id'], set_value('department')); ?>><?php echo $value["department_name"] ?></option>
                                                        <?php }
                                                        ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('department'); ?></span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Catégorie salariale</label>
                                                <select id="categorie_salaire" name="categorie_salaire" class="form-control">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php foreach ($categorie as $value): ?>
                                                        <option value="<?php echo $value["salaire"]; ?>"
                                                            <?php echo set_select('categorie_salaire', $value["salaire"], set_value('categorie_salaire') == $value["salaire"]); ?>>
                                                            <?php echo $value["categorie"] . " (" . number_format($value["salaire"], 3, ',', ' ') . ")"; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <input type="hidden" name="categorie_lettre">
                                                <span class="text-danger"><?php echo form_error('categorie_salaire'); ?></span>
                                            </div>
                                        </div>


                                        <div class="col-md-3" hidden>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Salaire de base</label>
                                                <input id="salaire_base" name="salaire_base" placeholder="" type="text" class="form-control"  value="<?php echo set_value('salaire_base') ?>" />
                                                <span class="text-danger"><?php echo form_error('salaire_base'); ?></span>
                                            </div>
                                        </div>


                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Numéro Cnps employé</label>
                                                <input id="cnps_no" name="cnps_no" placeholder="" type="text" class="form-control"  value="<?php echo set_value('cnps_no') ?>" />
                                                <span class="text-danger"><?php echo form_error('cnps_no'); ?></span>
                                            </div>
                                        </div>

                                        <div class="col-md-3" hidden>
                                            <div class="form-group">
                                                <label>Responsable hiérachie</label>
                                                <select name="responsable" class="form-control">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>

                                                    <?php foreach ($stff_list as $staff) {
                                                        $full_name = $staff['name'].' '.$staff['surname'];
                                                        ?>
                                                        <option value="<?php echo $staff['id']; ?>"
                                                            <?php echo set_select('responsable', $staff['id'], set_value('responsable')); ?>>
                                                            <?php echo $full_name; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>

                                            </div><!--./form-group-->
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Nom</label><small class="req"> *</small>
                                                <input id="name" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name') ?>" />
                                                <span class="text-danger"><?php echo form_error('name'); ?></span>
                                            </div>
                                        </div>
                                        <?php if ($sch_setting->staff_last_name) { ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Prénom</label>
                                                <input id="surname" name="surname" placeholder="" type="text" class="form-control"  value="<?php echo set_value('surname') ?>" />
                                                <span class="text-danger"><?php echo form_error('surname'); ?></span>
                                            </div>
                                        </div>
                                        <?php } if ($sch_setting->staff_father_name) { ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('father_name'); ?></label>
                                                <input id="father_name"  name="father_name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('father_name') ?>" />
                                                <span class="text-danger"><?php echo form_error('father_name'); ?></span>
                                            </div>
                                        </div>
                                        <?php } if ($sch_setting->staff_mother_name) { ?>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('mother_name'); ?></label>
                                                    <input id="mother_name" name="mother_name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('mother_name') ?>" />
                                                    <span class="text-danger"><?php echo form_error('mother_name'); ?></span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('email'); ?> (<?php echo $this->lang->line('login') . " " . $this->lang->line('username'); ?>)</label><small class="req"> *</small>
                                                <input id="email" name="email" placeholder="" type="text" class="form-control"  value="<?php echo set_value('email') ?>" />
                                                <span class="text-danger"><?php echo form_error('email'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputFile"> <?php echo $this->lang->line('gender'); ?></label><small class="req"> *</small>
                                                <select class="form-control" name="gender">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php
                                                    foreach ($genderList as $key => $value) {
                                                        ?>
                                                        <option value="<?php echo $key; ?>" <?php echo set_select('gender', $key, set_value('gender')); ?>><?php echo $value; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('gender'); ?></span>
                                            </div>
                                        </div>
                                        <?php if (!empty($sch_setting->staff_contract_type)) { ?>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="contract_type"><?php echo $this->lang->line('contract_type'); ?></label>
                                                    <select class="form-control" name="contract_type" id="contract_type">
                                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                        <?php
                                                        $selected_value = set_value('contract_type');
                                                        foreach ($contract_type as $key => $value) { ?>
                                                            <option value="<?php echo htmlspecialchars($key); ?>"
                                                                <?php echo ($selected_value == $key) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($value); ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                    <span class="text-danger"><?php echo form_error('contract_type'); ?></span>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Nationalité</label>
                                                <input id="nationalite" name="nationalite" placeholder="" type="text" class="form-control"  value="<?php echo set_value('nationalite') ?>" />
                                                <span class="text-danger"><?php echo form_error('nationalite'); ?></span>
                                            </div>
                                        </div>


                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('date_of_birth'); ?></label><small class="req"> *</small>
                                                <input id="dob" name="dob" placeholder="" type="text" class="form-control date"  value="<?php echo set_value('dob') ?>" />
                                                <span class="text-danger"><?php echo form_error('dob'); ?></span>
                                            </div>
                                        </div>
                                        <?php if ($sch_setting->staff_date_of_joining) { ?>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('date_of_joining'); ?></label><small class="req"> *</small>
                                                    <input id="date_of_joining" required name="date_of_joining" placeholder="" type="text" class="form-control date"  value="<?php echo set_value('date_of_joining') ?>" />
                                                    <span class="text-danger"><?php echo form_error('date_of_joining'); ?></span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Date de fin de contrat</label><small class="req"></small>
                                                <input id="date_of_leaving" name="date_of_leaving" placeholder="" type="text" class="form-control date"  value="<?php echo set_value('date_of_leaving') ?>" />
                                                <span class="text-danger"><?php echo form_error('date_of_leaving'); ?></span>
                                            </div>
                                        </div>


                                        <?php if ($sch_setting->staff_phone) { ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('phone'); ?></label>
                                                <input id="mobileno" name="contactno" placeholder="" type="text" class="form-control"  value="<?php echo set_value('contactno') ?>" />
                                                <span class="text-danger"><?php echo form_error('contactno'); ?></span>
                                            </div>
                                        </div>

                                        <?php } if ($sch_setting->staff_emergency_contact) { ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('emergency_contact_number'); ?></label>
                                                <input id="mobileno" name="emergency_no" placeholder="" type="text" class="form-control"  value="<?php echo set_value('emergency_no') ?>" />
                                                <span class="text-danger"><?php echo form_error('emergency_no'); ?></span>
                                            </div>
                                        </div>

                                        <?php } if ($sch_setting->staff_marital_status) { ?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1"><?php echo $this->lang->line('marital_status'); ?></label>
                                                <select class="form-control" name="marital_status">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php foreach ($marital_status as $makey => $mavalue) {
                                                        ?>
                                                        <option value="<?php echo $mavalue ?>" <?php echo set_select('marital_status', $mavalue, set_value('marital_status')); ?>><?php echo $mavalue; ?></option>

                                                    <?php } ?>

                                                </select>
                                                <span class="text-danger"><?php echo form_error('marital_status'); ?></span>
                                            </div>
                                        </div>


                                        <?php } if ($sch_setting->staff_photo) { ?>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="exampleInputFile"><?php echo $this->lang->line('photo'); ?></label>
                                                    <div><input class="filestyle form-control" type='file' name='file' id="file" size='20' />
                                                    </div>
                                                    <span class="text-danger"><?php echo form_error('file'); ?></span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="row">
                                        <?php if ($sch_setting->staff_current_address) { ?>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputFile"><?php echo $this->lang->line('current'); ?> <?php echo $this->lang->line('address'); ?></label>
                                                    <div><textarea name="address" class="form-control"><?php echo set_value('address'); ?></textarea>
                                                    </div>
                                                    <span class="text-danger"></span></div>
                                            </div>
                                        <?php } if ($sch_setting->staff_permanent_address) { ?>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputFile"><?php echo $this->lang->line('permanent_address'); ?></label>
                                                    <div><textarea name="permanent_address" class="form-control"><?php echo set_value('permanent_address'); ?></textarea>
                                                    </div>
                                                    <span class="text-danger"></span></div>
                                            </div>
                                        <?php } if ($sch_setting->staff_qualification) { ?>
                                            <div class="col-md-3">

                                                <div class="form-group">
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('qualification'); ?></label>
                                                    <textarea id="qualification" name="qualification" placeholder=""  class="form-control" ><?php echo set_value('qualification') ?></textarea>
                                                    <span class="text-danger"><?php echo form_error('qualification'); ?></span>
                                                </div>
                                            </div>
                                        <?php } if ($sch_setting->staff_work_experience) { ?>
                                            <div class="col-md-3">

                                                <div class="form-group">
                                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('work_experience'); ?></label>
                                                    <textarea id="work_exp" name="work_exp" placeholder="" class="form-control"><?php echo set_value('work_exp') ?></textarea>
                                                    <span class="text-danger"><?php echo form_error('work_exp'); ?></span>
                                                </div>
                                            </div>
                                        <?php } if ($sch_setting->staff_note) { ?>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputFile"><?php echo $this->lang->line('note'); ?></label>
                                                    <div><textarea name="note" class="form-control"><?php echo set_value('note'); ?></textarea>
                                                    </div>
                                                    <span class="text-danger"></span></div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="row">
                                        <?php
                                        echo display_custom_fields('staff');
                                        ?>
                                    </div>

                                </div>
                            </div>

                            <div class="box-group collapsed-box">
                                <div class="panel box box-success collapsed-box">
                                    <div class="box-header with-border">
                                        <a data-widget="collapse" data-original-title="Collapse" class="collapsed btn boxplus">
                                            <i class="fa fa-fw fa-plus"></i><?php echo $this->lang->line('add_more_details'); ?>
                                        </a>
                                    </div>

                                    <div class="box-body">

                                        <div class="tshadow mb25 bozero">
                                            <h4 class="pagetitleh2"><?php echo $this->lang->line('payroll'); ?>
                                            </h4>

                                            <div class="row around10">
                                                <?php if ($sch_setting->staff_epf_no) { ?>
                                                    <div class="col-md-4" hidden>
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('epf_no'); ?></label>
                                                            <input id="epf_no" name="epf_no" placeholder="" type="text" class="form-control"  value="<?php echo set_value('epf_no') ?>"  />
                                                            <span class="text-danger"><?php echo form_error('epf_no'); ?></span>
                                                        </div>
                                                    </div>
                                                <?php } if ($sch_setting->staff_basic_salary) { ?>
                                                    <div class="col-md-4" hidden>
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('basic_salary'); ?></label>
                                                            <input type="text" class="form-control" name="basic_salary" value="<?php echo set_value('basic_salary') ?>" >
                                                        </div>
                                                    </div>
                                                <?php }  if ($sch_setting->staff_work_shift) { ?>
                                                    <div class="col-md-4" hidden>
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('work_shift'); ?></label>
                                                            <input id="shift" name="shift" placeholder="" type="text" class="form-control"  value="<?php echo set_value('shift') ?>" />
                                                            <span class="text-danger"><?php echo form_error('shift'); ?></span>
                                                        </div>
                                                    </div>
                                                <?php } if ($sch_setting->staff_work_location) { ?>
                                                    <div class="col-md-4" hidden>
                                                        <div class="form-group">

                                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('work_location'); ?></label>
                                                            <input id="location" name="location" placeholder="" type="text" class="form-control"  value="<?php echo set_value('location') ?>" />
                                                            <span class="text-danger"><?php echo form_error('location'); ?></span>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                              <!--  <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Prime d'ancienété</label>
                                                        <input id="prime_anc" name="prime_anc" placeholder="" type="text" class="form-control"  value="<?php echo set_value('prime_anc') ?>" />
                                                        <span class="text-danger"><?php echo form_error('prime_anc'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Sursalaire</label>
                                                        <input id="sursalaire" name="sursalaire" placeholder="" type="text" class="form-control"  value="<?php echo set_value('sursalaire') ?>" />
                                                        <span class="text-danger"><?php echo form_error('sursalaire'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Prime de transport</label>
                                                        <input id="prime_trans" name="prime_trans" placeholder="" type="text" class="form-control"  value="<?php echo set_value('prime_trans') ?>" />
                                                        <span class="text-danger"><?php echo form_error('prime_trans'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Forfait d'heure supplémentaire</label>
                                                        <input id="location" name="forfait_hs" placeholder="" type="text" class="form-control"  value="<?php echo set_value('forfait_hs') ?>" />
                                                        <span class="text-danger"><?php echo form_error('forfait_hs'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Prime de responsabilité</label>
                                                        <input id="prime_resp" name="prime_resp" placeholder="" type="text" class="form-control"  value="<?php echo set_value('prime_resp') ?>" />
                                                        <span class="text-danger"><?php echo form_error('prime_resp'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Bonus</label>
                                                        <input id="bonus" name="bonus" placeholder="" type="text" class="form-control"  value="<?php echo set_value('bonus') ?>" />
                                                        <span class="text-danger"><?php echo form_error('bonus'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Prime de rendement</label>
                                                        <input id="prime_rend" name="prime_rend" placeholder="" type="text" class="form-control"  value="<?php echo set_value('prime_rend') ?>" />
                                                        <span class="text-danger"><?php echo form_error('prime_rend'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Prime de risque</label>
                                                        <input id="prime_risque" name="prime_risque" placeholder="" type="text" class="form-control"  value="<?php echo set_value('prime_risque') ?>" />
                                                        <span class="text-danger"><?php echo form_error('prime_risque'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">prime d'assiduité</label>
                                                        <input id="prime_assi" name="prime_assi" placeholder="" type="text" class="form-control"  value="<?php echo set_value('prime_assi') ?>" />
                                                        <span class="text-danger"><?php echo form_error('prime_assi'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Prime Gratification</label>
                                                        <input id="prime_grati" name="prime_grati" placeholder="" type="text" class="form-control"  value="<?php echo set_value('prime_grati') ?>" />
                                                        <span class="text-danger"><?php echo form_error('prime_grati'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Prime de congé</label>
                                                        <input id="conge" name="conge" placeholder="" type="text" class="form-control"  value="<?php echo set_value('conge') ?>" />
                                                        <span class="text-danger"><?php echo form_error('conge'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Imp sur Trait. et Sal (IS)</label>
                                                        <input id="imp_sal" name="imp_sal" placeholder="" type="text" class="form-control"  value="<?php echo set_value('location') ?>" />
                                                        <span class="text-danger"><?php echo form_error(''); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Contribution National</label>
                                                        <input id="location" name="location" placeholder="" type="text" class="form-control"  value="<?php echo set_value('location') ?>" />
                                                        <span class="text-danger"><?php echo form_error('location'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Imp Gén. sur revenu</label>
                                                        <input id="location" name="location" placeholder="" type="text" class="form-control"  value="<?php echo set_value('location') ?>" />
                                                        <span class="text-danger"><?php echo form_error('location'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Cont. Recons. Nat</label>
                                                        <input id="location" name="location" placeholder="" type="text" class="form-control"  value="<?php echo set_value('location') ?>" />
                                                        <span class="text-danger"><?php echo form_error('location'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">CMU</label>
                                                        <input id="location" name="location" placeholder="" type="text" class="form-control"  value="<?php echo set_value('location') ?>" />
                                                        <span class="text-danger"><?php echo form_error('location'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Cnps, Regime de Retraite</label>
                                                        <input id="location" name="location" placeholder="" type="text" class="form-control"  value="<?php echo set_value('location') ?>" />
                                                        <span class="text-danger"><?php echo form_error('location'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Cnps Accident Travail</label>
                                                        <input id="location" name="location" placeholder="" type="text" class="form-control"  value="<?php echo set_value('location') ?>" />
                                                        <span class="text-danger"><?php echo form_error('location'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Cnps Prest. Famil</label>
                                                        <input id="location" name="location" placeholder="" type="text" class="form-control"  value="<?php echo set_value('location') ?>" />
                                                        <span class="text-danger"><?php echo form_error('location'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">FDFP, Taxe Apprentissage</label>
                                                        <input id="location" name="location" placeholder="" type="text" class="form-control"  value="<?php echo set_value('location') ?>" />
                                                        <span class="text-danger"><?php echo form_error('location'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">FDFP, Form. Prof. Continue</label>
                                                        <input id="location" name="location" placeholder="" type="text" class="form-control"  value="<?php echo set_value('location') ?>" />
                                                        <span class="text-danger"><?php echo form_error('location'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Avance/Acompte</label>
                                                        <input id="location" name="location" placeholder="" type="text" class="form-control"  value="<?php echo set_value('location') ?>" />
                                                        <span class="text-danger"><?php echo form_error('location'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <label for="exampleInputEmail1">Autres retenues</label>
                                                        <input id="location" name="location" placeholder="" type="text" class="form-control"  value="<?php echo set_value('location') ?>" />
                                                        <span class="text-danger"><?php echo form_error('location'); ?></span>
                                                    </div>
                                                </div>-->

                                                             </div>

                                        </div>
                                        <?php if ($sch_setting->staff_leaves) { ?>
                                            <div class="tshadow mb25 bozero">
                                                <h4 class="pagetitleh2"><?php echo $this->lang->line('leaves'); ?>
                                                </h4>

                                                <div class="row around10" >
                                                    <?php
                                                    foreach ($leavetypeList as $key => $leave) {
                                                        ?>

                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1"><?php echo $leave["type"]; ?></label>

                                                                <input  name="leave_type[]" type="hidden" readonly class="form-control" value="<?php echo $leave['id'] ?>" />
                                                                <input  name="alloted_leave_<?php echo $leave['id'] ?>" readonly placeholder="<?php echo $this->lang->line('number_of_leaves'); ?>" type="text" class="form-control" value="<?php echo isset($leave['ndays']) ? $leave['ndays'] : ''; ?>" />

                                                                <span class="text-danger"><?php echo form_error('alloted_leave_' . $leave['id']); ?></span>
                                                            </div>
                                                        </div>



                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } if ($sch_setting->staff_account_details) { ?>
                                            <div class="tshadow mb25 bozero">
                                                <h4 class="pagetitleh2"><?php echo $this->lang->line('bank_account_details'); ?>
                                                </h4>

                                                <div class="row around10">

                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('account_title'); ?></label>
                                                            <input id="account_title" name="account_title" placeholder="" type="text" class="form-control"  value="<?php echo set_value('account_title') ?>" />
                                                            <span class="text-danger"><?php echo form_error('account_title'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('bank_account_no'); ?></label>
                                                            <input id="bank_account_no" name="bank_account_no" placeholder="" type="text" class="form-control"  value="<?php echo set_value('bank_account_no') ?>" />
                                                            <span class="text-danger"><?php echo form_error('bank_account_no'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('bank_name'); ?></label>
                                                            <input id="bank_name" name="bank_name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('bank_name') ?>" />
                                                            <span class="text-danger"><?php echo form_error('bank_name'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('ifsc_code'); ?></label>
                                                            <input id="ifsc_code" name="ifsc_code" placeholder="" type="text" class="form-control"  value="<?php echo set_value('ifsc_code') ?>" />
                                                            <span class="text-danger"><?php echo form_error('ifsc_code'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('bank_branch_name'); ?></label>
                                                            <input id="bank_branch" name="bank_branch" placeholder="" type="text" class="form-control"  value="<?php echo set_value('bank_branch') ?>" />
                                                            <span class="text-danger"><?php echo form_error('bank_branch'); ?></span>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                        <?php } if ($sch_setting->staff_social_media) { ?>
                                            <div class="tshadow mb25 bozero">
                                                <h4 class="pagetitleh2"><?php echo $this->lang->line('social_media'); ?>
                                                </h4>

                                                <div class="row around10">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('facebook_url'); ?></label>
                                                            <input id="bank_account_no" name="facebook" placeholder="" type="text" class="form-control"  value="<?php echo set_value('facebook') ?>" />
                                                            <span class="text-danger"><?php echo form_error('facebook'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('twitter_url'); ?></label>
                                                            <input id="bank_account_no" name="twitter" placeholder="" type="text" class="form-control"  value="<?php echo set_value('twitter') ?>" />
                                                            <span class="text-danger"><?php echo form_error('twitter_profile'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('linkedin_url'); ?></label>
                                                            <input id="bank_name" name="linkedin" placeholder="" type="text" class="form-control"  value="<?php echo set_value('linkedin') ?>" />
                                                            <span class="text-danger"><?php echo form_error('linkedin'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('instagram_url'); ?></label>
                                                            <input id="instagram" name="instagram" placeholder="" type="text" class="form-control"  value="<?php echo set_value('instagram') ?>" />

                                                        </div>
                                                    </div>

                                                </div>


                                            </div>
                                        <?php } if ($sch_setting->staff_upload_documents) { ?>
                                            <div id='upload_documents_hide_show'>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="tshadow bozero">
                                                            <h4 class="pagetitleh2"><?php echo $this->lang->line('upload_documents'); ?>
                                                                <button type="button" class="btn btn-primary btn-sm pull-right" id="add-document-btn">
                                                                    <i class="fa fa-plus"></i> <?php echo $this->lang->line('add_document'); ?>
                                                                </button>
                                                                <small class="text-muted pull-right" style="margin-right: 10px; margin-top: 5px;">
                                                                    <i class="fa fa-check-circle text-success"></i>
                                                                    <span id="uploaded-count">0</span>/<span id="total-documents">3</span>
                                                                    documents uploadés
                                                                </small>
                                                            </h4>

                                                            <div class="row around10">
                                                                <div class="col-md-12">
                                                                    <table class="table" id="documents-table">
                                                                        <thead>
                                                                        <tr>
                                                                            <th style="width: 10px">#</th>
                                                                            <th><?php echo $this->lang->line('title'); ?></th>
                                                                            <th><?php echo $this->lang->line('documents'); ?></th>
                                                                            <th style="width: 100px;"><?php echo $this->lang->line('status'); ?></th>
                                                                            <th style="width: 80px;"><?php echo $this->lang->line('action'); ?></th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody id="documents-tbody">
                                                                        <!-- Documents fixes existants -->
                                                                        <tr>
                                                                            <td>1.</td>
                                                                            <td>
                                                                                <input type="text" name="document_titles[]" class="form-control"
                                                                                       value="<?php echo $this->lang->line('resume'); ?>" readonly>
                                                                            </td>
                                                                            <td>
                                                                                <div class="file-input-wrapper">
                                                                                    <input class="filestyle form-control document-file" type='file'
                                                                                           name='documents[]' accept=".pdf,.doc,.docx,.jpg,.png" data-index="0">
                                                                                    <div class="file-preview" id="preview-0" style="display: none;">
                                                                                        <small class="file-info text-success"></small>
                                                                                    </div>
                                                                                </div>
                                                                                <span class="text-danger"><?php echo form_error('documents[]'); ?></span>
                                                                            </td>
                                                                            <td class="status-cell">
                                                                                <i class="fa fa-times-circle text-danger status-icon" id="status-0"></i>
                                                                                <span class="status-text text-danger">En attente</span>
                                                                            </td>
                                                                            <td>
                                                                                <button type="button" class="btn btn-info btn-sm view-document" style="display: none;" disabled>
                                                                                    <i class="fa fa-eye"></i>
                                                                                </button>
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>2.</td>
                                                                            <td>
                                                                                <input type="text" name="document_titles[]" class="form-control"
                                                                                       value="<?php echo $this->lang->line('joining_letter'); ?>" readonly>
                                                                            </td>
                                                                            <td>
                                                                                <div class="file-input-wrapper">
                                                                                    <input class="filestyle form-control document-file" type='file'
                                                                                           name='documents[]' accept=".pdf,.doc,.docx,.jpg,.png" data-index="1">
                                                                                    <div class="file-preview" id="preview-1" style="display: none;">
                                                                                        <small class="file-info text-success"></small>
                                                                                    </div>
                                                                                </div>
                                                                                <span class="text-danger"><?php echo form_error('documents[]'); ?></span>
                                                                            </td>
                                                                            <td class="status-cell">
                                                                                <i class="fa fa-times-circle text-danger status-icon" id="status-1"></i>
                                                                                <span class="status-text text-danger">En attente</span>
                                                                            </td>
                                                                            <td>
                                                                                <button type="button" class="btn btn-info btn-sm view-document" style="display: none;" disabled>
                                                                                    <i class="fa fa-eye"></i>
                                                                                </button>
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>3.</td>
                                                                            <td>
                                                                                <input type="text" name="document_titles[]" class="form-control"
                                                                                       value="<?php echo $this->lang->line('other_documents'); ?>" readonly>
                                                                            </td>
                                                                            <td>
                                                                                <div class="file-input-wrapper">
                                                                                    <input class="filestyle form-control document-file" type='file'
                                                                                           name='documents[]' accept=".pdf,.doc,.docx,.jpg,.png" data-index="2">
                                                                                    <div class="file-preview" id="preview-2" style="display: none;">
                                                                                        <small class="file-info text-success"></small>
                                                                                    </div>
                                                                                </div>
                                                                                <span class="text-danger"><?php echo form_error('documents[]'); ?></span>
                                                                            </td>
                                                                            <td class="status-cell">
                                                                                <i class="fa fa-times-circle text-danger status-icon" id="status-2"></i>
                                                                                <span class="status-text text-danger">En attente</span>
                                                                            </td>
                                                                            <td>
                                                                                <button type="button" class="btn btn-info btn-sm view-document" style="display: none;" disabled>
                                                                                    <i class="fa fa-eye"></i>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                        <!-- Documents dynamiques ajoutés ici -->
                                                                        </tbody>
                                                                    </table>

                                                                    <!-- Bandeau de confirmation -->
                                                                    <div id="upload-summary" class="alert alert-info" style="display: none;">
                                                                        <i class="fa fa-info-circle"></i>
                                                                        <span id="summary-text"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</div>
<!-- Modal pour la prévisualisation des documents -->
<div class="modal fade" id="documentPreviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aperçu du Document</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="documentPreviewImage" src="" class="img-fluid" style="max-height: 70vh;">
                <div id="documentPreviewInfo" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="downloadPreview">Télécharger</button>
            </div>
        </div>
    </div>
</div>
</section>
</div>

<!-- Template pour les nouvelles lignes de document -->
<script type="text/template" id="document-row-template">
    <tr class="dynamic-document">
        <td><span class="document-number"></span></td>
        <td>
            <input type="text" name="document_titles[]" class="form-control"
                   placeholder="<?php echo $this->lang->line('enter_document_title'); ?>" required>
        </td>
        <td>
            <div class="file-input-wrapper">
                <input class="filestyle form-control document-file" type='file' name='documents[]'
                       accept=".pdf,.doc,.docx,.jpg,.png" required data-index="{index}">
                <div class="file-preview" id="preview-{index}" style="display: none;">
                    <small class="file-info text-success"></small>
                </div>
            </div>
            <span class="text-danger"></span>
        </td>
        <td class="status-cell">
            <i class="fa fa-times-circle text-danger status-icon" id="status-{index}"></i>
            <span class="status-text text-danger">En attente</span>
        </td>
        <td>
            <button type="button" class="btn btn-info btn-sm view-document" style="display: none;" disabled>
                <i class="fa fa-eye"></i>
            </button>
            <button type="button" class="btn btn-danger btn-sm remove-document" title="<?php echo $this->lang->line('remove'); ?>">
                <i class="fa fa-trash"></i>
            </button>
        </td>
    </tr>
</script>

<script>
    $(document).ready(function() {
        let documentCounter = 3;
        let uploadedDocuments = 0;

        // Mettre à jour le compteur
        function updateUploadCounter() {
            const totalDocs = $('#documents-tbody tr').length;
            $('#total-documents').text(totalDocs);
            $('#uploaded-count').text(uploadedDocuments);

            // Mettre à jour le bandeau de résumé
            updateSummaryBanner();

            // Vérifier si on peut soumettre le formulaire
            checkFormReadiness();
        }

        // Mettre à jour le bandeau d'information
        function updateSummaryBanner() {
            const total = $('#documents-tbody tr').length;
            const uploaded = uploadedDocuments;

            if (uploaded === 0) {
                $('#upload-summary').hide();
            } else {
                let summaryText = '';
                if (uploaded === total) {
                    summaryText = `<strong>Parfait !</strong> Tous les documents (${uploaded}/${total}) sont prêts.`;
                    $('#upload-summary').removeClass('alert-info alert-warning').addClass('alert-success');
                } else {
                    summaryText = `${uploaded} document(s) sur ${total} uploadé(s). `;
                    summaryText += `<strong>${total - uploaded} document(s) manquant(s).</strong>`;
                    $('#upload-summary').removeClass('alert-success alert-info').addClass('alert-warning');
                }
                $('#summary-text').html(summaryText);
                $('#upload-summary').show();
            }
        }

        // Vérifier si le formulaire est prêt
        function checkFormReadiness() {
            const total = $('#documents-tbody tr').length;
            const submitBtn = $('#submit-btn'); // Votre bouton d'enregistrement

            if (uploadedDocuments > 0) {
                submitBtn.prop('disabled', false).removeClass('btn-danger').addClass('btn-success');
            } else {
                submitBtn.prop('disabled', true).removeClass('btn-success').addClass('btn-danger');
            }
        }

        // Mettre à jour le statut d'un document
        function updateDocumentStatus(index, file, isValid = true) {
            const $statusIcon = $('#status-' + index);
            const $statusText = $statusIcon.siblings('.status-text');
            const $viewBtn = $statusIcon.closest('tr').find('.view-document');
            const $preview = $('#preview-' + index);

            if (file && isValid) {
                // Document uploadé avec succès
                $statusIcon.removeClass('fa-times-circle text-danger')
                    .addClass('fa-check-circle text-success');
                $statusText.removeClass('text-danger').addClass('text-success').text('Uploadé');

                $viewBtn.show().prop('disabled', false);
                $preview.show().find('.file-info').html(
                    `<i class="fa fa-file"></i> ${file.name} (${formatFileSize(file.size)})`
                );

                $statusIcon.closest('tr').addClass('document-uploaded');
                uploadedDocuments++;

            } else if (file && !isValid) {
                // Erreur de validation
                $statusIcon.removeClass('fa-check-circle text-success')
                    .addClass('fa-exclamation-circle text-warning');
                $statusText.removeClass('text-success').addClass('text-warning').text('Erreur');

                $viewBtn.hide();
                $preview.hide();
                $statusIcon.closest('tr').addClass('document-error');

            } else {
                // Aucun document
                $statusIcon.removeClass('fa-check-circle text-success fa-exclamation-circle text-warning')
                    .addClass('fa-times-circle text-danger');
                $statusText.removeClass('text-success text-warning').addClass('text-danger').text('En attente');

                $viewBtn.hide();
                $preview.hide();
                $statusIcon.closest('tr').removeClass('document-uploaded document-error');
            }

            updateUploadCounter();
        }

        // Formater la taille du fichier
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Valider le fichier
        function validateFile(file) {
            const allowedExtensions = /(\.pdf|\.doc|\.docx|\.jpg|\.jpeg|\.png)$/i;
            const maxSize = 5 * 1024 * 1024; // 5MB

            if (!allowedExtensions.exec(file.name)) {
                alert('Type de fichier non autorisé. Formats acceptés: PDF, DOC, DOCX, JPG, PNG');
                return false;
            }

            if (file.size > maxSize) {
                alert('Fichier trop volumineux. Taille maximum: 5MB');
                return false;
            }

            return true;
        }

        // Gérer le changement de fichier
        $(document).on('change', '.document-file', function() {
            const index = $(this).data('index');
            const file = this.files[0];

            if (file) {
                const isValid = validateFile(file);
                updateDocumentStatus(index, file, isValid);

                if (!isValid) {
                    $(this).val('');
                }
            } else {
                updateDocumentStatus(index, null);
            }
        });

        // Visualiser le document (prévisualisation)
        $(document).on('click', '.view-document:not(:disabled)', function() {
            const $fileInput = $(this).closest('tr').find('.document-file')[0];
            const file = $fileInput.files[0];

            if (file) {
                // Créer une URL temporaire pour la prévisualisation
                const fileURL = URL.createObjectURL(file);

                // Ouvrir dans un nouvel onglet ou afficher dans une modal
                if (file.type.includes('image')) {
                    // Pour les images
                    $('#documentPreviewImage').attr('src', fileURL);
                    $('#documentPreviewModal').modal('show');
                } else if (file.type.includes('pdf')) {
                    // Pour les PDF (ouvrir dans nouvel onglet)
                    window.open(fileURL, '_blank');
                } else {
                    // Pour les autres types, téléchargement
                    const a = document.createElement('a');
                    a.href = fileURL;
                    a.download = file.name;
                    a.click();
                }
            }
        });

        // Ajouter un nouveau document
        $('#add-document-btn').click(function() {
            const template = $('#document-row-template').html();
            const $newRow = $(template.replace(/{index}/g, documentCounter));

            $newRow.find('.document-number').text((documentCounter + 1) + '.');
            $('#documents-tbody').append($newRow);

            // Initialiser le filestyle
            $newRow.find('.filestyle').filestyle({
                buttonText: '<?php echo $this->lang->line("choose_file"); ?>',
                placeholder: '<?php echo $this->lang->line("no_file_chosen"); ?>'
            });

            documentCounter++;
            updateUploadCounter();
            updateRowNumbers();
        });

        // Supprimer un document
        $(document).on('click', '.remove-document', function() {
            const $row = $(this).closest('tr');
            const hasFile = $row.find('.document-file')[0].files.length > 0;

            if (hasFile) {
                uploadedDocuments--;
            }

            $row.remove();
            updateUploadCounter();
            updateRowNumbers();
            documentCounter--;
        });

        // Mettre à jour les numéros de ligne
        function updateRowNumbers() {
            $('#documents-tbody tr').each(function(index) {
                $(this).find('.document-number').text((index + 1) + '.');
            });
        }

        // Initialiser le compteur
        updateUploadCounter();
    });
</script>
<script type="text/javascript">






</script>
<script type="text/javascript" src="<?php echo base_url(); ?>backend/dist/js/savemode.js"></script>