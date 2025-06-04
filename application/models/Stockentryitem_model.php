<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Stockentryitem_model extends MY_Model
{

  public function __construct()
  {
      parent::__construct();
      $this->current_session = $this->setting_model->getCurrentSession();
  }


}
