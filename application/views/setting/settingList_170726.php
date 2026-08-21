<style type="text/css">
    .wrapper {overflow: visible;}

    /* Style créatif pour les champs */
    .form-group {
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .form-group:hover {
        transform: translateX(5px);
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: white;
        box-shadow: 0 0 0 3px rgba(60,141,188,0.1);
        transform: scale(1.02);
    }

    label {
        font-weight: 500;
        color: #555;
        margin-bottom: 0.5rem;
    }

    .req {
        color: #ff4d4d;
    }

    /* Style pour les sections */
    .section-title {
        background: linear-gradient(135deg, white 0%, #2c6e9e 100%);
        color: black;
        padding: 10px 15px;
        border-radius: 8px;
        margin: 20px 0;
        font-size: 16px;
        font-weight: 500;
    }

    .section-title i {
        margin-right: 8px;
    }

    /* Animation pour les boutons */
    .btn-primary {
        transition: all 0.3s ease;
        border-radius: 6px;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* Style pour les images de logo */
    .logo-box {
        margin-left: 310%;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }

    .logo-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .logo-box img {
        width: 80px;
        object-fit: cover;
    }

    /* Style pour les radio buttons */
    .radio-inline {
        margin-right: 20px;
        cursor: pointer;
    }

    .radio-inline input {
        margin-right: 5px;
    }

    /* Style pour les select */
    select.form-control {
        cursor: pointer;
        background-color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-group:hover {
            transform: none;
        }

        .col-md-4, .col-md-6 {
            margin-bottom: 15px;
        }
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content-header">
        <h1><i class="fa fa-gears"></i> <?php echo $this->lang->line('system_settings'); ?></h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">

            <div class="col-lg-12 col-md-9 col-sm-12">
                <!-- general form elements -->
                <div class="box box-primary">

                    <div class="">
                        <form role="form" id="schsetting_form" action="" class="" method="post" enctype="multipart/form-data">
                            <div class="box-body">

                                <!-- Informations de l'entreprise - 3 colonnes -->
                                <div class="section-title">
                                    <i class="fa fa-building"></i> Informations de l'entreprise
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Raison social <small class="req">*</small></label>
                                            <input type="text" class="form-control" id="name" name="sch_name" value="<?php echo $result->name; ?>" placeholder="Entrez la raison sociale">
                                            <span class="text-danger"><?php echo form_error('name'); ?></span>
                                            <input type="hidden" name="sch_id" value="<?php echo $result->id; ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Registre de commerce <small class="req"></small></label>
                                            <input type="text" class="form-control" id="registre_commerce" name="registre_commerce" value="<?php echo $result->registre_commerce; ?>" placeholder="Entrez le registre de commerce">
                                            <span class="text-danger"><?php echo form_error('registre_commerce'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Compte contribuable <small class="req"></small></label>
                                            <input type="text" class="form-control" id="compte_contribuable" name="compte_contribuable" value="<?php echo $result->compte_contribuable; ?>" placeholder="Entrez le compte contribuable">
                                            <span class="text-danger"><?php echo form_error('compte_contribuable'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Forme juridique <small class="req"></small></label>
                                            <input type="text" class="form-control" id="forme_jurique" name="forme_jurique" value="<?php echo $result->forme_jurique; ?>" placeholder="Ex: SARL, SA">
                                            <span class="text-danger"><?php echo form_error('forme_jurique'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Numéro CNPS <small class="req"></small></label>
                                            <input type="text" class="form-control" id="cnps_number" name="cnps_number" value="<?php echo $result->cnps_number; ?>" placeholder="Entrez le numéro CNPS">
                                            <span class="text-danger"><?php echo form_error('cnps_number'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Boîte Postale <small class="req"></small></label>
                                            <input type="text" class="form-control" id="boite_postal" name="boite_postal" value="<?php echo $result->boite_postal; ?>" placeholder="Ex: BP 123">
                                            <span class="text-danger"><?php echo form_error('boite_postal'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Adresse <small class="req">*</small></label>
                                            <input type="text" class="form-control" id="address" name="sch_address" value="<?php echo $result->address; ?>" placeholder="Adresse complète">
                                            <span class="text-danger"><?php echo form_error('address'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nom de la banque</label>
                                            <input type="text" class="form-control" id="bank" name="bank" value="<?php echo $result->bank; ?>" placeholder="Nom de la banque">
                                            <span class="text-danger"><?php echo form_error('bank'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Compte Bancaire</label>
                                            <input type="text" class="form-control" id="compt_bank" name="compt_bank" value="<?php echo $result->compt_bank; ?>" placeholder="Numéro de compte">
                                            <span class="text-danger"><?php echo form_error('compt_bank'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Centre des impôts</label>
                                            <input type="text" class="form-control" id="centre_impot" name="centre_impot" value="<?php echo $result->centre_impot; ?>" placeholder="Centre des impôts">
                                            <span class="text-danger"><?php echo form_error('centre_impot'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Régime d'imposition</label>
                                            <input type="text" class="form-control" id="regime_imposition" name="regime_imposition" value="<?php echo $result->regime_imposition; ?>" placeholder="Régime d'imposition">
                                            <span class="text-danger"><?php echo form_error('regime_imposition'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4" hidden>
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('school_code'); ?></label>
                                            <input type="text" class="form-control" id="dise_code" name="sch_dise_code" value="<?php echo $result->dise_code; ?>">
                                            <span class="text-danger"><?php echo form_error('dise_code'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact et identification - 3 colonnes -->
                                <div class="section-title">
                                    <i class="fa fa-phone"></i> Contact et identification
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Téléphone <small class="req">*</small></label>
                                            <input type="text" class="form-control" id="phone" name="sch_phone" value="<?php echo $result->phone; ?>" placeholder="Numéro de téléphone">
                                            <span class="text-danger"><?php echo form_error('phone'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>NCCM/RCCM <small class="req">*</small></label>
                                            <input type="text" class="form-control" id="rccm" name="rccm" value="<?php echo $result->rccm; ?>" placeholder="Numéro NCCM/RCCM">
                                            <span class="text-danger"><?php echo form_error('rccm'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Email <small class="req">*</small></label>
                                            <input type="email" class="form-control" id="email" name="sch_email" value="<?php echo $result->email; ?>" placeholder="Email de contact">
                                            <span class="text-danger"><?php echo form_error('email'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Langue <small class="req">*</small></label>
                                            <select id="language_id" name="sch_lang_id" class="form-control">
                                                <option value="">-- <?php echo $this->lang->line('select'); ?> --</option>
                                                <?php foreach ($languagelist as $language) { ?>
                                                    <option value="<?php echo $language['id']; ?>" <?php if ($language['id'] == $result->lang_id) echo "selected"; ?>>
                                                        <?php echo $language['language']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('language_id'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Session académique <small class="req">*</small></label>
                                            <select id="session_id" name="sch_session_id" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($sessionlist as $session) { ?>
                                                    <option value="<?php echo $session['id']; ?>" <?php if ($session['id'] == $result->session_id) echo "selected"; ?>>
                                                        <?php echo $session['session']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('session_id'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Format de date <small class="req">*</small></label>
                                            <select id="date_format" name="sch_date_format" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($dateFormatList as $key => $dateformat) { ?>
                                                    <option value="<?php echo $key; ?>" <?php if ($key == $result->date_format) echo "selected"; ?>>
                                                        <?php echo $dateformat; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('date_format'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Début de semaine <small class="req">*</small></label>
                                            <select id="start_week" name="sch_start_week" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($daysList as $day_key => $day_value) { ?>
                                                    <option value="<?php echo $day_key; ?>" <?php if ($day_key == $result->start_week) echo "selected"; ?>>
                                                        <?php echo $day_value; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('sch_start_week'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Site web <small class="req">*</small></label>
                                            <input type="url" class="form-control" id="site_web" name="site_web" value="<?php echo $result->site_web; ?>" placeholder="https://www.example.com">
                                            <span class="text-danger"><?php echo form_error('site_web'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Activité de l'entreprise <small class="req">*</small></label>
                                            <input type="text" class="form-control" id="company_activity" name="company_activity" value="<?php echo $result->company_activity; ?>" placeholder="Activité principale">
                                            <span class="text-danger"><?php echo form_error('company_activity'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nom du fournisseur <small class="req">*</small></label>
                                            <input type="text" class="form-control" id="company_supplier" name="company_supplier" value="<?php echo $result->company_supplier; ?>" placeholder="Nom du fournisseur">
                                            <span class="text-danger"><?php echo form_error('company_supplier'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nom du responsable <small class="req">*</small></label>
                                            <input type="text" class="form-control" id="director" name="director" value="<?php echo $result->director; ?>" placeholder="Nom du responsable">
                                            <span class="text-danger"><?php echo form_error('director'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Titre <small class="req">*</small></label>
                                            <input type="text" class="form-control" id="director_title" name="director_title" value="<?php echo $result->director_title; ?>" placeholder="Titre du reponsable">
                                            <span class="text-danger"><?php echo form_error('director_title'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4" hidden>
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('session_start_month'); ?><small class="req"> *</small></label>
                                            <select id="start_month" name="sch_start_month" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($monthList as $key => $month) { ?>
                                                    <option value="<?php echo $key; ?>" <?php if ($key == $result->start_month) echo "selected"; ?>>
                                                        <?php echo $month; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('start_month'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Attendence Type - Hidden -->
                                <div class="row" hidden>
                                    <div class="col-md-12" hidden>
                                        <div class="settinghr"></div>
                                        <h4 class="session-head"><?php echo $this->lang->line('attendence') . " " . $this->lang->line('type'); ?></h4>
                                    </div>
                                    <div class="col-md-6" hidden>
                                        <div class="form-group row">
                                            <label class="col-sm-6"><?php echo $this->lang->line('attendence'); ?></label>
                                            <div class="col-sm-6">
                                                <label class="radio-inline">
                                                    <input type="radio" name="attendence_type" value="0" <?php if (!$result->attendence_type) echo "checked"; ?> ><?php echo $this->lang->line('day') . " " . $this->lang->line('wise'); ?>
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="attendence_type" value="1" <?php if ($result->attendence_type) echo "checked"; ?>><?php echo $this->lang->line('period') . " " . $this->lang->line('wise'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" hidden>
                                        <div class="form-group row">
                                            <label class="col-sm-6"> <?php echo $this->lang->line('biometric') . " " . $this->lang->line('attendance'); ?></label>
                                            <div class="col-sm-6">
                                                <label class="radio-inline">
                                                    <input type="radio" name="biometric" value="0" <?php if (!$result->biometric) echo "checked"; ?> ><?php echo $this->lang->line('disabled'); ?>
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="biometric" value="1" <?php if ($result->biometric) echo "checked"; ?>><?php echo $this->lang->line('enabled'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" hidden>
                                        <div class="form-group">
                                            <label class="col-sm-3"> <?php echo $this->lang->line('devices') . " (" . $this->lang->line('seprate') . " " . $this->lang->line('by') . " " . $this->lang->line('coma') . ")"; ?> </label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="name" name="biometric_device" value="<?php echo $result->biometric_device; ?>">
                                                <span class="text-danger"><?php echo form_error('biometric_device'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Language RTL Mode - Hidden -->
                                <div class="row" hidden>
                                    <div class="col-md-6" hidden>
                                        <div class="form-group row">
                                            <label class="col-sm-6"><?php echo $this->lang->line('language_rtl_text_mode'); ?></label>
                                            <div class="col-sm-6">
                                                <label class="radio-inline">
                                                    <input type="radio" name="sch_is_rtl" value="disabled" <?php if ($result->is_rtl == "disabled") echo "checked"; ?> ><?php echo $this->lang->line('disabled'); ?>
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="sch_is_rtl" value="enabled" <?php if ($result->is_rtl == "enabled") echo "checked"; ?>><?php echo $this->lang->line('enabled'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Timezone - Hidden -->
                                <div class="row">
                                    <div class="col-md-4" hidden>
                                        <div class="form-group row" hidden>
                                            <label class="col-sm-4"><?php echo $this->lang->line('timezone'); ?><small class="req"> *</small></label>
                                            <div class="col-sm-8">
                                                <select id="language_id" name="sch_timezone" class="form-control">
                                                    <option value="">--<?php echo $this->lang->line('select') ?>--</option>
                                                    <?php foreach ($timezoneList as $key => $timezone) { ?>
                                                        <option value="<?php echo $key; ?>" <?php if ($key == $result->timezone) echo "selected"; ?>>
                                                            <?php echo $timezone; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('timezone'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Devise - 3 colonnes -->
                                <div class="section-title">
                                    <i class="fa fa-money"></i> Informations financières
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Devise <small class="req">*</small></label>
                                            <select id="currency" name="sch_currency" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($currencyList as $currency) { ?>
                                                    <option value="<?php echo $currency; ?>" <?php if ($currency == $result->currency) echo "selected"; ?>>
                                                        <?php echo $currency; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('currency'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Symbole de la devise <small class="req">*</small></label>
                                            <input type="text" class="form-control" id="currency_symbol" name="sch_currency_symbol" value="<?php echo $result->currency_symbol; ?>" placeholder="Ex: €, $, FCFA">
                                            <span class="text-danger"><?php echo form_error('currency_symbol'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-12 hidden">
                                        <div class="form-group row">
                                            <label class="col-sm-3"><?php echo $this->lang->line('currency_symbol') . " " . $this->lang->line('place') ?><small class="req"> *</small></label>
                                            <div class="col-sm-9">
                                                <?php foreach ($currencyPlace as $currency_place_k => $currency_place_v) { ?>
                                                    <label class="radio-inline hidden">
                                                        <input type="hidden" name="currency_place" value="<?php echo $currency_place_k; ?>" <?php if ($result->currency_place == $currency_place_k) echo "checked"; ?>>
                                                        <?php echo $currency_place_v; ?>
                                                    </label>
                                                <?php } ?>
                                            </div>
                                            <span class="text-danger"><?php echo form_error('currency_symbol'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Student Admission No Auto Generation - Hidden -->
                                <div class="row" hidden>
                                    <div class="col-md-12">
                                        <div class="settinghr"></div>
                                        <h4 class="session-head"><?php echo $this->lang->line('student_admission_no_auto_generation'); ?></h4>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('auto') . " " . $this->lang->line('admission') . " " . $this->lang->line('no'); ?></label>
                                            <div class="col-sm-8">
                                                <label class="radio-inline">
                                                    <input type="radio" name="adm_auto_insert" value="0" <?php if ($result->adm_auto_insert == 0) echo "checked"; ?>><?php echo $this->lang->line('disabled'); ?>
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="adm_auto_insert" value="1" <?php if ($result->adm_auto_insert == 1) echo "checked"; ?>><?php echo $this->lang->line('enabled'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('admission_no_prefix'); ?><small class="req"> *</small></label>
                                            <div class="col-sm-8">
                                                <input type="text" name="adm_prefix" id="adm_prefix" class="form-control" value="<?php echo $result->adm_prefix; ?>">
                                                <span class="text-danger"><?php echo form_error('adm_prefix'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('admission_no_digit'); ?><small class="req"> *</small></label>
                                            <div class="col-sm-8">
                                                <select id="adm_no_digit" name="adm_no_digit" class="form-control">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php foreach ($digitList as $digit) { ?>
                                                        <option value="<?php echo $digit; ?>" <?php if ($result->adm_no_digit == $digit) echo "selected"; ?>>
                                                            <?php echo $digit; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('adm_no_digit'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('admission') . " " . $this->lang->line('start') . " " . $this->lang->line('from') ?><small class="req"> *</small></label>
                                            <div class="col-sm-8">
                                                <input type="text" name="adm_start_from" id="adm_start_from" class="form-control" value="<?php echo $result->adm_start_from; ?>">
                                                <span class="text-danger"><?php echo form_error('adm_start_from'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Staff ID Auto Generation - 3 colonnes -->
                                <div class="section-title">
                                    <i class="fa fa-id-card"></i> Génération automatique ID Personnel
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Auto génération ID personnel</label>
                                            <div>
                                                <label class="radio-inline">
                                                    <input type="radio" name="staffid_auto_insert" value="0" <?php if ($result->staffid_auto_insert == 0) echo "checked"; ?>> Désactivé
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="staffid_auto_insert" value="1" <?php if ($result->staffid_auto_insert == 1) echo "checked"; ?>> Activé
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Préfixe ID personnel <small class="req">*</small></label>
                                            <input type="text" class="form-control" id="staffid_prefix" name="staffid_prefix" value="<?php echo $result->staffid_prefix; ?>" placeholder="Ex: EMP-">
                                            <span class="text-danger"><?php echo form_error('staffid_prefix'); ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nombre de chiffres ID <small class="req">*</small></label>
                                            <select id="staffid_no_digit" name="staffid_no_digit" class="form-control">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php foreach ($digitList as $digit) { ?>
                                                    <option value="<?php echo $digit; ?>" <?php if ($digit == $result->staffid_no_digit) echo "selected"; ?>>
                                                        <?php echo $digit; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('staffid_no_digit'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>ID personnel commence à <small class="req">*</small></label>
                                            <input type="number" class="form-control" id="staffid_start_from" name="staffid_start_from" value="<?php echo $result->staffid_start_from; ?>" placeholder="Ex: 1000">
                                            <span class="text-danger"><?php echo form_error('staffid_start_from'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Online Examination - Hidden -->
                                <div class="row" hidden>
                                    <div class="col-md-12" hidden>
                                        <div class="settinghr"></div>
                                        <h4 class="session-head"><?php echo $this->lang->line('online_examination'); ?></h4>
                                    </div>
                                    <div class="col-md-6" hidden>
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('show_me_only_my_question'); ?></label>
                                            <div class="col-sm-8">
                                                <label class="radio-inline">
                                                    <input type="radio" name="my_question" value="0" <?php if ($result->my_question == 0) echo "checked"; ?>><?php echo $this->lang->line('disabled'); ?>
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="my_question" value="1" <?php if ($result->my_question == 1) echo "checked"; ?>><?php echo $this->lang->line('enabled'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Miscellaneous - Hidden -->
                                <div class="row" hidden>
                                    <div class="col-md-12" hidden>
                                        <div class="settinghr"></div>
                                        <h4 class="session-head"><?php echo $this->lang->line('miscellaneous'); ?></h4>
                                    </div>
                                    <div class="col-md-6" hidden>
                                        <div class="form-group row">
                                            <label class="col-sm-4"> <?php echo $this->lang->line('duplicate') . " " . $this->lang->line('fees') . " " . $this->lang->line('invoice'); ?></label>
                                            <div class="col-sm-8" hidden>
                                                <label class="radio-inline">
                                                    <input type="radio" name="is_duplicate_fees_invoice" value="0" <?php if ($result->is_duplicate_fees_invoice == 0) echo "checked"; ?> ><?php echo $this->lang->line('disabled'); ?>
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="is_duplicate_fees_invoice" value="1" <?php if ($result->is_duplicate_fees_invoice == 1) echo "checked"; ?>><?php echo $this->lang->line('enabled'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" hidden>
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('fee_due_days'); ?><small class="req"> *</small></label>
                                            <div class="col-sm-8">
                                                <input type="number" name="fee_due_days" id="fee_due_days" class="form-control" value="<?php echo $result->fee_due_days; ?>">
                                                <span class="text-danger"><?php echo form_error('fee_due_days'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" hidden>
                                        <div class="form-group row">
                                            <label class="col-sm-4"><?php echo $this->lang->line('teacher_restricted_mode'); ?></label>
                                            <div class="col-sm-8">
                                                <label class="radio-inline">
                                                    <input type="radio" name="class_teacher" value="no" <?php if ($result->class_teacher == "no") echo "checked"; ?> ><?php echo $this->lang->line('disabled'); ?>
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="class_teacher" <?php if ($result->class_teacher == "yes") echo "checked"; ?> value="yes"><?php echo $this->lang->line('enabled'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mobile App - Hidden -->
                                <div class="row" hidden>
                                    <div class="col-md-12" hidden>
                                        <div class="settinghr"></div>
                                        <div class="relative">
                                            <h4 class="session-head"><?php echo $this->lang->line('mobile_app'); ?> <?php if ($app_response) { echo "<small class=' alert-success'>(".$this->lang->line('android_app_purchase_code_already_registered').")</small>"; } ?></h4>
                                            <?php if (!$app_response) { ?>
                                                <button type="button" class="btn btn-info btn-sm impbtntitle3" data-toggle="modal" data-target="#andappModal"><?php echo $this->lang->line('register_your_android_app')?></button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-md-12" hidden>
                                        <div class="form-group row">
                                            <label class="col-sm-2"> <?php echo $this->lang->line('mobile_app_api_url') ?></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="mobile_api_url" id="mobile_api_url" class="form-control" value="<?php echo $result->mobile_api_url; ?>">
                                                <span class="text-danger"><?php echo form_error('mobile_api_url'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" hidden>
                                        <div class="form-group row">
                                            <label class="col-sm-5"> <?php echo $this->lang->line('mobile_app_primary_color_code') ?></label>
                                            <div class="col-sm-7">
                                                <input type="text" name="app_primary_color_code" id="app_primary_color_code" class="form-control" value="<?php echo $result->app_primary_color_code; ?>">
                                                <span class="text-danger"><?php echo form_error('app_primary_color_code'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" hidden>
                                        <div class="form-group row">
                                            <label class="col-sm-6"> <?php echo $this->lang->line('mobile_app_secondary_color_code'); ?></label>
                                            <div class="col-sm-6">
                                                <input type="text" name="app_secondary_color_code" id="app_secondary_color_code" class="form-control" value="<?php echo $result->app_secondary_color_code; ?>">
                                                <span class="text-danger"><?php echo form_error('app_secondary_color_code'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Current Theme - Hidden -->
                                <div class="row" hidden>
                                    <div class="col-md-12">
                                        <div class="settinghr"></div>
                                        <h4 class="session-head"><?php echo $this->lang->line('current_theme'); ?></h4>
                                    </div>
                                    <div class="col-sm-12">
                                        <div id="input-type">
                                            <div class="row">
                                                <div class="col-sm-3 col-xs-6 col20">
                                                    <label class="radio-img">
                                                        <input name="theme" <?php if ($result->theme == "white.jpg") echo "checked"; ?> value="white.jpg" type="radio" />
                                                        <img src="<?php echo base_url(); ?>backend/images/white.jpg">
                                                        <span class="radiotext">white</span>
                                                    </label>
                                                </div>
                                                <div class="col-sm-3 col-xs-6 col20">
                                                    <label class="radio-img">
                                                        <input name="theme" <?php if ($result->theme == "default.jpg") echo "checked"; ?> value="default.jpg" type="radio" />
                                                        <img src="<?php echo base_url(); ?>backend/images/default.jpg">
                                                        <span class="radiotext">default</span>
                                                    </label>
                                                </div>
                                                <div class="col-sm-3 col-xs-6 col20">
                                                    <label class="radio-img">
                                                        <input name="theme" <?php if ($result->theme == "red.jpg") echo "checked"; ?> value="red.jpg" type="radio" />
                                                        <img src="<?php echo base_url(); ?>backend/images/red.jpg">
                                                        <span class="radiotext">red</span>
                                                    </label>
                                                </div>
                                                <div class="col-sm-3 col-xs-6 col20">
                                                    <label class="radio-img">
                                                        <input name="theme" <?php if ($result->theme == "blue.jpg") echo "checked"; ?> value="blue.jpg" type="radio" />
                                                        <img src="<?php echo base_url(); ?>backend/images/blue.jpg">
                                                        <span class="radiotext">blue</span>
                                                    </label>
                                                </div>
                                                <div class="col-sm-3 col-xs-6 col20">
                                                    <label class="radio-img">
                                                        <input name="theme" <?php if ($result->theme == "gray.jpg") echo "checked"; ?> value="gray.jpg" type="radio" />
                                                        <img src="<?php echo base_url(); ?>backend/images/gray.jpg">
                                                        <span class="radiotext">gray</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div><!-- /.box-body -->

                            <div class="box-footer">
                                <?php if ($this->rbac->hasPrivilege('general_setting', 'can_edit')) { ?>
                                    <button type="button" class="btn btn-primary submit_schsetting pull-right edit_setting" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Traitement...">
                                        <i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?>
                                    </button>
                                <?php } ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Colonne pour les logos -->
            <div class="col-lg-3 col-md-3 col-sm-12">
                <!-- Logo de facturation (Hidden - Commenté) -->
                <!--
                <div class="logo-box box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-image"></i> Logo de facturation</h3>
                    </div>
                    <div class="box-body text-center">
                        <?php if ($result->image == "") { ?>
                            <img src="<?php echo base_url(); ?>uploads/school_content/logo/images.png" class="img-responsive" alt="Logo">
                        <?php } else { ?>
                            <img src="<?php echo base_url(); ?>uploads/school_content/logo/<?php echo $result->image; ?>" class="img-responsive" alt="Logo">
                        <?php } ?>
                        <br>
                        <a href="#schsetting" role="button" class="btn btn-primary btn-sm upload_logo" data-toggle="tooltip" title="Modifier le logo">
                            <i class="fa fa-picture-o"></i> Modifier logo
                        </a>
                    </div>
                </div>
                -->

                <!-- Logo administration -->
                <div class="logo-box box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-image"></i> Logo administration</h3>
                    </div>
                    <div class="box-body text-center">
                        <?php if ($result->admin_logo == "") { ?>
                            <img src="<?php echo base_url(); ?>uploads/school_content/admin_logo/images.png" class="img-responsive" alt="Admin Logo">
                        <?php } else { ?>
                            <img src="<?php echo base_url(); ?>uploads/school_content/admin_logo/<?php echo $result->admin_logo; ?>" class="img-responsive" alt="Admin Logo">
                        <?php } ?>
                        <br>
                        <a href="#admin_logo" role="button" class="btn btn-primary btn-sm upload_admin_logo" data-toggle="tooltip" title="Modifier le logo">
                            <i class="fa fa-picture-o"></i> Modifier logo
                        </a>
                    </div>
                </div>

                <!-- Admin Small Logo - Commenté -->
                <!--
                <div class="logo-box box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-image"></i> Petit logo administration</h3>
                    </div>
                    <div class="box-body text-center">
                        <?php if ($result->admin_small_logo == "") { ?>
                            <img src="<?php echo base_url(); ?>uploads/school_content/logo/images.png" class="img-thumbnail" alt="">
                        <?php } else { ?>
                            <img src="<?php echo base_url(); ?>uploads/school_content/admin_small_logo/<?php echo $result->admin_small_logo; ?>" class="" alt="">
                        <?php } ?>
                        <br>
                        <a href="#admin_small_logo" role="button" class="btn btn-primary btn-sm upload_admin_small_logo" data-toggle="tooltip" title="Modifier le petit logo">
                            <i class="fa fa-picture-o"></i> Modifier petit logo
                        </a>
                    </div>
                </div>
                -->

                <!-- App Logo - Commenté -->
                <!--
                <div class="logo-box box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-image"></i> Logo Application</h3>
                    </div>
                    <div class="box-body text-center">
                        <?php if ($result->app_logo == "") { ?>
                            <img src="<?php echo base_url(); ?>uploads/school_content/logo/images.png" class="img-responsive" alt="">
                        <?php } else { ?>
                            <img src="<?php echo base_url(); ?>uploads/school_content/logo/app_logo/<?php echo $result->app_logo; ?>" class="img-responsive" alt="">
                        <?php } ?>
                        <br>
                        <a href="#app_logo" role="button" class="btn btn-primary btn-sm upload_app_logo" data-toggle="tooltip" title="Modifier le logo app">
                            <i class="fa fa-picture-o"></i> Modifier logo app
                        </a>
                    </div>
                </div>
                -->
            </div>

        </div>
    </section>
</div>

<!-- Modals -->
<div class="modal fade" id="modal-upload_admin_logo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('edit') . " " . $this->lang->line('admin') . " " . $this->lang->line('logo'); ?></h4>
            </div>
            <div class="modal-body upload_logo_body">
                <form class="box_upload boxupload has-advanced-upload" method="post" action="<?php echo site_url('schsettings/ajax_editlogo') ?>" enctype="multipart/form-data">
                    <input value="<?php echo $result->id ?>" type="hidden" name="id" id="id_logo_admin"/>
                    <input type="file" name="file" id="file_admin">
                    <div class="box__input upload-admin_area" id="uploadfile_admin">
                        <i class="fa fa-download box__icon"></i>
                        <label><strong><?php echo $this->lang->line('choose_a_file'); ?></strong><span class="box__dragndrop"> <?php echo $this->lang->line('or') ?> <span><?php echo $this->lang->line('drag') ?></span><?php echo $this->lang->line('it_here') ?></span>.</label>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-upload_app_logo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('edit_app_logo'); ?></h4>
            </div>
            <div class="modal-body upload_logo_body">
                <form class="box_upload boxupload has-advanced-upload" method="post" action="<?php echo site_url('schsettings/ajax_editlogo') ?>" enctype="multipart/form-data">
                    <input value="<?php echo $result->id ?>" type="hidden" name="id" id="id_app_logo"/>
                    <input type="file" name="file" id="file_applogo">
                    <div class="box__input upload-app_logo_area" id="uploadapp_logo">
                        <i class="fa fa-download box__icon"></i>
                        <label><strong><?php echo $this->lang->line('choose_a_file'); ?></strong><span class="box__dragndrop"> <?php echo $this->lang->line('or') ?> <span><?php echo $this->lang->line('drag') ?></span><?php echo $this->lang->line('it_here') ?></span>.</label>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-upload_admin_small_logo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('edit') . " " . $this->lang->line('admin') . " " . $this->lang->line('small') . " " . $this->lang->line('logo'); ?></h4>
            </div>
            <div class="modal-body upload_logo_body">
                <form class="box_upload boxupload has-advanced-upload" method="post" action="<?php echo site_url('schsettings/ajax_editlogo') ?>" enctype="multipart/form-data">
                    <input value="<?php echo $result->id ?>" type="hidden" name="id" id="id_logo_small"/>
                    <input type="file" name="file" id="file_small">
                    <div class="box__input upload-small_area" id="uploadfile_small">
                        <i class="fa fa-download box__icon"></i>
                        <label><strong><?php echo $this->lang->line('choose_a_file'); ?></strong><span class="box__dragndrop"> <?php echo $this->lang->line('or') ?> <span><?php echo $this->lang->line('drag') ?></span><?php echo $this->lang->line('it_here') ?></span>.</label>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-uploadfile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('edit_logo'); ?></h4>
            </div>
            <div class="modal-body upload_logo_body">
                <form class="box_upload boxupload has-advanced-upload" method="post" action="<?php echo site_url('schsettings/ajax_editlogo') ?>" enctype="multipart/form-data">
                    <input value="<?php echo $result->id ?>" type="hidden" name="id" id="id_logo"/>
                    <input type="file" name="file" id="file">
                    <div class="box__input upload-area" id="uploadfile">
                        <i class="fa fa-download box__icon"></i>
                        <label><strong><?php echo $this->lang->line('choose_a_file'); ?></strong><span class="box__dragndrop"> <?php echo $this->lang->line('or') ?> <span><?php echo $this->lang->line('drag') ?></span><?php echo $this->lang->line('it_here') ?></span>.</label>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="andappModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Register your Android App purchase code</h4>
            </div>
            <form action="<?php echo site_url('admin/admin/updateandappCode') ?>" method="POST" id="andapp_code">
                <div class="modal-body andapp_modal-body">
                    <div class="error_message"></div>
                    <div class="form-group">
                        <label class="ainline"><span>Envato Market Purchase Code for Smart School Android App ( <a target="_blank" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-"> How to find it?</a> )</span></label>
                        <input type="text" class="form-control" id="input-app-envato_market_purchase_code" name="app-envato_market_purchase_code">
                        <div id="error" class="input-error text text-danger"></div>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Your Email registered with Envato</label>
                        <input type="text" class="form-control" id="input-app-email" name="app-email">
                        <div id="error" class="input-error text text-danger"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Saving...">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var base_url = '<?php echo base_url(); ?>';
    var logo_type = "logo";
    $('.upload_logo').on('click', function (e) {
        e.preventDefault();
        var $this = $(this);
        logo_type = $this.data('logo_type');
        $this.button('loading');
        $('#modal-uploadfile').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
    });

    $('#modal-uploadfile').on('shown.bs.modal', function () {
        $('.upload_logo').button('reset');
    });

    $('.upload_admin_logo').on('click', function (e) {
        e.preventDefault();
        var $this = $(this);
        logo_type = $this.data('logo_type');
        $this.button('loading');
        $('#modal-upload_admin_logo').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
    });

    $('#modal-upload_admin_logo').on('shown.bs.modal', function () {
        $('.upload_admin_logo').button('reset');
    });

    $('.upload_admin_small_logo').on('click', function (e) {
        e.preventDefault();
        var $this = $(this);
        logo_type = $this.data('logo_type');
        $this.button('loading');
        $('#modal-upload_admin_small_logo').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
    });

    $('#modal-upload_admin_small_logo').on('shown.bs.modal', function () {
        $('.upload_admin_small_logo').button('reset');
    });

    $(".edit_setting").on('click', function (e) {
        var $this = $(this);
        $this.button('loading');
        $.ajax({
            url: '<?php echo site_url("schsettings/ajax_schedit") ?>',
            type: 'POST',
            data: $('#schsetting_form').serialize(),
            dataType: 'json',
            success: function (data) {
                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
                    window.location.reload(true);
                }
                $this.button('reset');
            }
        });
    });
</script>

<script type="text/javascript">
    $(function () {
        $("#online_admission_amount").attr('readonly','true');

        $('.upload-area').on('dragenter', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $("h1").text("Drop");
        });

        $('.upload-area').on('dragover', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $("h1").text("Drop");
        });

        $('.upload-area').on('drop', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $("h1").text("Upload");
            var file = e.originalEvent.dataTransfer.files;
            var fd = new FormData();
            fd.append('file', file[0]);
            fd.append("id", $('#id_logo').val());
            fd.append("logo_type", logo_type);
            uploadData(fd);
        });

        $("#uploadfile").click(function () {
            $("#file").click();
        });

        $("#file").change(function () {
            var fd = new FormData();
            var files = $('#file')[0].files[0];
            fd.append('file', files);
            fd.append("id", $('#id_logo').val());
            fd.append("logo_type", logo_type);
            uploadData(fd);
        });
    });

    function uploadData(formdata) {
        $.ajax({
            url: '<?php echo site_url('schsettings/ajax_editlogo') ?>',
            type: 'post',
            data: formdata,
            contentType: false,
            processData: false,
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                $('#modal-uploadfile').addClass('modal_loading');
            },
            success: function (response) {
                if (response.success) {
                    successMsg(response.message);
                    window.location.reload(true);
                } else {
                    errorMsg(response.error.file);
                }
            },
            error: function (xhr) { },
            complete: function () {
                $('#modal-uploadfile').removeClass('modal_loading');
            }
        });
    }

    $(function () {
        $('.upload-small_area').on('dragenter', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $("h1").text("Drop");
        });

        $('.upload-small_area').on('dragover', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $("h1").text("Drop");
        });

        $('.upload-small_area').on('drop', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $("h1").text("Upload");
            var file = e.originalEvent.dataTransfer.files;
            var fd = new FormData();
            fd.append('file', file[0]);
            fd.append("id", $('#id_logo_small').val());
            fd.append("logo_type", logo_type);
            uploadSmallData(fd);
        });

        $("#uploadfile_small").click(function () {
            $("#file_small").click();
        });

        $("#file_small").change(function () {
            var fd = new FormData();
            var files = $('#file_small')[0].files[0];
            fd.append('file', files);
            fd.append("id", $('#id_logo_small').val());
            fd.append("logo_type", logo_type);
            uploadSmallData(fd);
        });
    });

    function uploadSmallData(formdata) {
        $.ajax({
            url: '<?php echo site_url('schsettings/ajax_editadmin_smalllogo') ?>',
            type: 'post',
            data: formdata,
            contentType: false,
            processData: false,
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                $('#modal-upload_admin_small_logo').addClass('modal_loading');
            },
            success: function (response) {
                if (response.success) {
                    successMsg(response.message);
                    window.location.reload(true);
                } else {
                    errorMsg(response.error.file);
                }
            },
            error: function (xhr) { },
            complete: function () {
                $('#modal-upload_admin_small_logo').removeClass('modal_loading');
            }
        });
    }

    $(function () {
        $('.upload-admin_area').on('dragenter', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $("h1").text("Drop");
        });

        $('.upload-admin_area').on('dragover', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $("h1").text("Drop");
        });

        $('.upload-admin_area').on('drop', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $("h1").text("Upload");
            var file = e.originalEvent.dataTransfer.files;
            var fd = new FormData();
            fd.append('file', file[0]);
            fd.append("id", $('#id_logo_small').val());
            fd.append("logo_type", logo_type);
            uploadadminlData(fd);
        });

        $("#uploadfile_admin").click(function () {
            $("#file_admin").click();
        });

        $("#file_admin").change(function () {
            var fd = new FormData();
            var files = $('#file_admin')[0].files[0];
            fd.append('file', files);
            fd.append("id", $('#id_logo_small').val());
            fd.append("logo_type", logo_type);
            uploadadminData(fd);
        });
    });

    function uploadadminData(formdata) {
        $.ajax({
            url: '<?php echo site_url('schsettings/ajax_editadmin_adminlogo') ?>',
            type: 'post',
            data: formdata,
            contentType: false,
            processData: false,
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                $('#modal-upload_admin_logo').addClass('modal_loading');
            },
            success: function (response) {
                if (response.success) {
                    successMsg(response.message);
                    window.location.reload(true);
                } else {
                    errorMsg(response.error.file);
                }
            },
            error: function (xhr) { },
            complete: function () {
                $('#modal-upload_admin_logo').removeClass('modal_loading');
            }
        });
    }
</script>

<script type="text/javascript">
    $('.upload_app_logo').on('click', function (e) {
        e.preventDefault();
        var $this = $(this);
        logo_type = $this.data('logo_type');
        $this.button('loading');
        $('#modal-upload_app_logo').modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });
    });

    $('#modal-upload_app_logo').on('shown.bs.modal', function () {
        $('.upload_app_logo').button('reset');
    });

    $(function () {
        $('.upload-app_logo_area').on('dragenter', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $("h1").text("Drop");
        });

        $('.upload-app_logo_area').on('dragover', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $("h1").text("Drop");
        });

        $('.upload-app_logo_area').on('drop', function (e) {
            e.stopPropagation();
            e.preventDefault();
            $("h1").text("Upload");
            var file = e.originalEvent.dataTransfer.files;
            var fd = new FormData();
            fd.append('file', file[0]);
            fd.append("id", $('#id_app_logo').val());
            uploadSmallData(fd);
        });

        $("#uploadapp_logo").click(function () {
            $("#file_applogo").click();
        });

        $("#file_applogo").change(function () {
            var fd = new FormData();
            var files = $('#file_applogo')[0].files[0];
            fd.append('file', files);
            fd.append("id", $('#id_app_logo').val());
            uploadAppData(fd);
        });
    });

    function uploadAppData(formdata) {
        $.ajax({
            url: '<?php echo site_url('schsettings/ajax_applogo') ?>',
            type: 'post',
            data: formdata,
            contentType: false,
            processData: false,
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                $('#modal-upload_app_logo').addClass('modal_loading');
            },
            success: function (response) {
                if (response.success) {
                    successMsg(response.message);
                    window.location.reload(true);
                } else {
                    errorMsg(response.error.file);
                }
            },
            error: function (xhr) { },
            complete: function () {
                $('#modal-upload_app_logo').removeClass('modal_loading');
            }
        });
    }
</script>

<script>
    $(".amountenable").click(function () {
        var status=$(this). val();
        if(status=='yes'){
            $("#online_admission_amount").removeAttr('readonly','false');
        }else{
            $("#online_admission_amount").attr('readonly','true');
        }
    });
</script>