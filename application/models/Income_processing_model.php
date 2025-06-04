<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Income_processing_model extends My_Model
{
    // Specify the table targeted
	protected $ma_table = 'income_processing';

    public function __construct()
    {
        parent::__construct();
    }
    

}
