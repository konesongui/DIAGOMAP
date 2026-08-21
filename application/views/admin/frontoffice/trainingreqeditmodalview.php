<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <!-- Horizontal Form -->
        <form  action="<?php echo site_url('admin/training_request') ?>" id="myForm1"  method="post"  class="ptt10">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="pwd"><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>  
                        <input type="text" class="form-control" id="name_value" value="<?php echo set_value('name', $training_data['name']); ?>" name="name">
                        <span class="text-danger" id="name"></span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="pwd">Intitulé de la formation</label><small class="req"> *</small>
                        <input id="text" name="training_name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('training_name', $training_data['training_name']); ?>" />
                        <span class="text-danger"><?php echo form_error('training_name'); ?></span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Responsable hiérachie</label>
                        <select name="assigned" class="form-control">
                            <option value=""><?php echo $this->lang->line('select') ?></option>
                            <?php foreach ($stff_list as $key => $stff_list_value) { ?>
                                <option value="<?php echo $stff_list_value['name'].' '.$stff_list_value['surname']; ?>" <?php if ($stff_list_value['name'].' '.$stff_list_value['surname'] == $training_data['assigned']) { ?>selected=""<?php } ?> ><?php echo $stff_list_value['name'].' '.$stff_list_value['surname']; ?></option>
                            <?php }   ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="pwd">Date souhaitée</label>
                        <input type="text" id="date_edit" name="date" class="form-control date" value="<?php
                        if (!empty($training_data['date'])) {
                            echo set_value('date', date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($training_data['date'])));
                        }
                        ?>" readonly="">
                    </div>
                </div>


                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="email">Objectifs de la formation</label>
                        <textarea name="objectifs" class="form-control" ><?php echo set_value('objectifs', $training_data['objectifs']); ?></textarea>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="pwd">Commentaires supplémentaires</label>
                        <textarea name="commentaires" class="form-control" ><?php echo set_value('commentaires', $training_data['commentaires']); ?></textarea>
                    </div>
                </div>

                <div class="col-lg-4" hidden>
                    <div class="form-group">
                        <label for="pwd"><?php echo $this->lang->line('next_follow_up_date'); ?></label>
                        <input type="text" id="date_of_call_edit" name="follow_up_date"class="form-control date" value="<?php
                        if (!empty($training_data['follow_up_date'])) {
                            echo set_value('follow_up_date', date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($training_data['follow_up_date'])));
                        }
                        ?>" readonly="">
                    </div>
                </div>

                <div class="col-sm-3" hidden>
                    <div class="form-group">
                        <label for="pwd"><?php echo $this->lang->line('reference'); ?></label>   
                        <select name="reference" class="form-control">
                            <option value=""><?php echo $this->lang->line('select') ?></option>
                            <?php foreach ($Reference as $key => $value) { ?>
                                <option value="<?php echo $value['reference']; ?>" <?php if (set_value('reference', $training_data['reference']) == $value['reference']) { ?>selected=""<?php } ?>><?php echo $value['reference']; ?></option>
                            <?php }
                            ?>
                        </select>
                        <span class="text-danger"><?php echo form_error('reference'); ?></span>
                    </div>
                </div>    
                <div class="col-sm-3" hidden>
                    <div class="form-group">
                        <label for="pwd">Type de permission</label><small class="req"> *</small>
                        <input type="text" value="<?php echo set_value('source', $training_data['source']); ?>" name="source" class="form-control">
                        <!--<select name="source" class="form-control">
                            <option value=""><?php echo $this->lang->line('select') ?></option>
                            <?php foreach ($source as $key => $value) { ?>
                                <option value="<?php echo $value['source']; ?>"<?php
                            if ($training_data['source'] == $value['source']) {
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

                                <option value="<?php echo $value['id'] ?>" <?php if (set_value('class', $training_data['class']) == $value['id']) { ?> selected="" <?php } ?>><?php echo $value['class'] ?></option>

                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3" hidden>
                    <div class="form-group">
                        <label for="pwd"><?php echo $this->lang->line('number_of_child'); ?></label> 
                        <input type="number" class="form-control" min="1" value="<?php echo set_value('no_of_child', $training_data['no_of_child']); ?>" name="no_of_child">
                        <span class="text-danger"><?php echo form_error('no_of_child'); ?></span>
                    </div>
                </div>                    
                <div class="row">    
                    <div class="box-footer col-md-12">
                        <a onclick="postRecord(<?php echo $training_data['id'] ?>)" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></a>
                    </div>
                </div>  
            </div><!--./row--> 
        </form>
    </div><!--./col-md-12-->
</div><!--./row--> 