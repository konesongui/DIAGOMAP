<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <!-- Horizontal Form -->
        <form  action="<?php echo site_url('admin/projects') ?>" id="myForm1"  method="post"  class="ptt10">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="pwd">Nom du projet</label><small class="req"> *</small>
                        <input type="text" class="form-control" id="projet" value="<?php echo set_value('projet', $projects_data['projet']); ?>" name="projet">
                        <span class="text-danger" id="projet"></span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="pwd">Objectif</label><small class="req"> *</small>
                        <input id="text" name="objet" placeholder="" type="text" class="form-control"  value="<?php echo set_value('objet', $projects_data['objet']); ?>" />
                        <span class="text-danger"><?php echo form_error('objet'); ?></span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Référence</label>
                        <input type="text" value="<?php echo set_value('code', $projects_data['code']); ?>" name="code" class="form-control">
                        <span class="text-danger"><?php echo form_error('code'); ?></span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="pwd">Date début</label>
                        <input type="text" id="date_edit" name="start_date" class="form-control date" value="<?php
                        if (!empty($projects_data['start_date'])) {
                            echo set_value('start_date', date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($projects_data['start_date'])));
                        }
                        ?>" readonly="">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="pwd">Date de fin</label>
                        <input type="text" id="date_edit" name="end_date" class="form-control date" value="<?php
                        if (!empty($projects_data['end_date'])) {
                            echo set_value('end_date', date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($projects_data['end_date'])));
                        }
                        ?>" readonly="">
                    </div>
                </div>
                <div class="col-sm-4" hidden>
                    <div class="form-group">
                        <label for="email"><?php echo $this->lang->line('address'); ?></label> 
                        <textarea name="address" class="form-control"><?php echo set_value('address', trim($projects_data['address'])) ?></textarea>
                        <span class="text-danger"><?php echo form_error('address'); ?></span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="email"><?php echo $this->lang->line('description'); ?></label>
                        <textarea name="description" class="form-control" ><?php echo set_value('description', $projects_data['description']); ?></textarea>
                    </div>
                </div>
                <div class="col-sm-4" hidden>
                    <div class="form-group">
                        <label for="pwd"><?php echo $this->lang->line('note'); ?></label> 
                        <textarea name="note" class="form-control" ><?php echo set_value('note', $projects_data['note']); ?></textarea>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="pwd"><?php echo $this->lang->line('date'); ?></label>
                        <input type="text" id="date_edit" name="date" class="form-control date" value="<?php
                        if (!empty($projects_data['date'])) {
                            echo set_value('date', date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($projects_data['date'])));
                        }
                        ?>" readonly="">
                    </div>
                </div>
                <div class="col-lg-4" hidden>
                    <div class="form-group">
                        <label for="pwd"><?php echo $this->lang->line('next_follow_up_date'); ?></label>
                        <input type="text" id="date_of_call_edit" name="follow_up_date"class="form-control date" value="<?php
                        if (!empty($projects_data['follow_up_date'])) {
                            echo set_value('follow_up_date', date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($projects_data['follow_up_date'])));
                        }
                        ?>" readonly="">
                    </div>
                </div>
                <div class="col-sm-4" hidden>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('assigned'); ?></label>
						<select name="assigned" class="form-control">
                            <option value=""><?php echo $this->lang->line('select') ?></option>  
                            <?php foreach ($stff_list as $key => $stff_list_value) { ?>
                                 <option value="<?php echo $stff_list_value['name'].' '.$stff_list_value['surname']; ?>" <?php if ($stff_list_value['name'].' '.$stff_list_value['surname'] == $enquiry_data['assigned']) { ?>selected=""<?php } ?> ><?php echo $stff_list_value['name'].' '.$stff_list_value['surname']; ?></option>    
                            <?php }   ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3" hidden>
                    <div class="form-group">
                        <label for="pwd"><?php echo $this->lang->line('reference'); ?></label>   
                        <select name="reference" class="form-control">
                            <option value=""><?php echo $this->lang->line('select') ?></option>
                            <?php foreach ($Reference as $key => $value) { ?>
                                <option value="<?php echo $value['reference']; ?>" <?php if (set_value('reference', $projects_data['reference']) == $value['reference']) { ?>selected=""<?php } ?>><?php echo $value['reference']; ?></option>
                            <?php }
                            ?>
                        </select>
                        <span class="text-danger"><?php echo form_error('reference'); ?></span>
                    </div>
                </div>    
                <div class="col-sm-3" hidden>
                    <div class="form-group">
                        <label for="pwd">Type de permission</label><small class="req"> *</small>
                        <input type="text" value="<?php echo set_value('source', $projects_data['source']); ?>" name="source" class="form-control">
                        <!--<select name="source" class="form-control">
                            <option value=""><?php echo $this->lang->line('select') ?></option>
                            <?php foreach ($source as $key => $value) { ?>
                                <option value="<?php echo $value['source']; ?>"<?php
                            if ($enquiry_data['source'] == $value['source']) {
                                echo "selected";
                            }
                            ?>><?php echo $value['source']; ?></option>
<?php }
                        ?>
                        </select>-->
                    </div>
                </div>      
                <div class="col-sm-3" hidden>
                    <div class="form-group">
                        <label for="pwd"><?php echo $this->lang->line('class'); ?></label> 
                        <select name="class" class="form-control"  >
                            <option value="" selected=""><?php echo $this->lang->line('select') ?></option>
                            <?php
                            foreach ($class_list as $key => $value) {
                                ?>

                                <option value="<?php echo $value['id'] ?>" <?php if (set_value('class', $projects_data['class']) == $value['id']) { ?> selected="" <?php } ?>><?php echo $value['class'] ?></option>

                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3" hidden>
                    <div class="form-group">
                        <label for="pwd"><?php echo $this->lang->line('number_of_child'); ?></label> 
                        <input type="number" class="form-control" min="1" value="<?php echo set_value('no_of_child', $projects_data['no_of_child']); ?>" name="no_of_child">
                        <span class="text-danger"><?php echo form_error('no_of_child'); ?></span>
                    </div>
                </div>                    
                <div class="row">    
                    <div class="box-footer col-md-12">
                        <a onclick="postRecord(<?php echo $projects_data['id'] ?>)" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></a>
                    </div>
                </div>  
            </div><!--./row--> 
        </form>
    </div><!--./col-md-12-->
</div><!--./row--> 