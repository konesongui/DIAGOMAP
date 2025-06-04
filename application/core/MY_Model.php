<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->library('user_agent');
    }

    public function log($message = null, $record_id = null, $action = null) {
        $user_id = $this->customlib->getStaffID();

        $ip = $this->input->ip_address();

        if ($this->agent->is_browser()) {
            $agent = $this->agent->browser() . ' ' . $this->agent->version();
        } elseif ($this->agent->is_robot()) {
            $agent = $this->agent->robot();
        } elseif ($this->agent->is_mobile()) {

            $agent = $this->agent->mobile();
        } else {
            $agent = 'Unidentified User Agent';
        }

        $platform = $this->agent->platform(); // Platform info (Windows, Linux, Mac, etc.)

        $insert = array(
            'message' => $message,
            'user_id' => $user_id,
            'record_id' => $record_id,
            'ip_address' => $ip,
            'platform' => $platform,
            'agent' => $agent,
            'action' => $action,
            'time' => date('Y-m-d H:i:s'),
        );

        $this->db->insert('logs', $insert);
    }



    function query($sql, $data = array()) {
        $query = $this->db->query($sql, $data)->result();
        return $query;
    }

    function queryFirst($sql, $data = array()) {
            $query = $this->db->query($sql, $data)->result();
            return current($query);
    }

    function queryCount($sql, $data = array()) {
            $query = $this->db->query($sql, $data)->num_rows();
            return $query;
            $query->free_result();
    }
        
    //Insère une nouvelle ligne dans la base de données.
    public function createP ($options_echappees = array(), $options_non_echappees = array())
    {
        // Vérification des données à insérer
        if(empty($options_echappees) AND empty($options_non_echappees))
        {
            return false;
        }
        $this->db->set($options_echappees)
            ->set($options_non_echappees, null, false)
            ->insert($this->ma_table);
        return $this->db->insert_id();
    }

    // Récupère des données dans la base de données.
    public function read($select = '*', $where = array())
    {
        
        return  $this->db->select($select)
                ->from($this->ma_table)
                ->where($where)
                ->get()
                ->row();		
    }

    // Récupère des données dans la base de données.
    //public function readJoin($select = '*', $table = array(), $where = array())
    public function readJoin($select, $join, $where = array())
    {
        
        return  $this->db->select($select)
                ->from($this->ma_table)
                ->join($join['table'], $join['condition'], $join['type'])
                ->where($where)
                ->get()
                ->result();		
    }

    //Modifie une ou plusieurs lignes dans la base de données.
    public function updateP ($where, $options_echappees = array(), $options_non_echappees = array())
    {
        // Vérification des données à mettre à jour
        if(empty($options_echappees) AND empty($options_non_echappees))
        {
            return false;
        }

        // var_dump($this->ma_table);
        // exit;
        
        return (bool) $this->db->set($options_echappees)
                    ->set($options_non_echappees,null,false)
                    ->where($where)
                    ->update($this->ma_table);
    }

    //Supprime une ou plusieurs lignes de la base de données.
    public function deleteP($where)
    {
        return (bool) $this->db->where($where)
                    ->delete($this->ma_table);
    }

    // Retourne le nombre de résultats.
    public function compter($champ = array(),$valeur = null) 
    // Si $champ est un array, la variable $valeur sera ignorée par la méthode where()
    {
        return (int) $this->db->where($champ, $valeur)
                    ->from($this->ma_table)
                    ->count_all_results();
    }

    public function compter_distinct($distinct = null) 
    {
        return $this->db->select('count(distinct '.$distinct.') as nb')
                ->from($this->ma_table)
                ->get()
                ->result();		
    }

    public function readLimit($select ='*', $where = array(), $limit = 1, $order) 
    {
        return  $this->db->select($select)
                    ->from($this->ma_table)
                    ->where($where)
                    ->order_by($order)
                    ->limit($limit)
                    ->get()
                    ->result();	
    }

    public function readLimitJoin($select ='*', $join, $where = array(), $limit = 1, $order) 
    {
        return    $this->db->select($select)
                    ->from($this->ma_table)
                    ->join($join['table'], $join['condition'], $join['type'])
                    ->where($where)
                    ->order_by($order)
                    ->limit($limit)
                    ->get()
                    ->result();	
    }

    public function readOrder($select ='*', $where = array(), $order) 
    {
        return    $this->db->select($select)
                    ->from($this->ma_table)
                    ->where($where)
                    ->order_by($order)
                    ->get()
                    ->result();	
    }

    //------------------------------------------------------------------------------
    // READ DATA FROM THE DATABASE IN A TABLE JOIN ON ONE OR MULTIPLE OTHERS TABLES
    //------------------------------------------------------------------------------
    // Author:      Cédric LIADE
    // Created on:  04/12/2017 11:30
    // Updated on:  
    //------------------------------------------------------------------------------

    public function readJoins($select = '*', $joins = array(), $orWhere = array(), $where = array(), $limit = 0, $order = array())
    {
        
        $tJoin    = '';
        $tOrder   = '';
        $tOrWhere = "";
        $cOrWhere = 0;
        $vOrWhere = ' OR ';
        $signsWhere   = array('!', '=', '<', '>');
        $tWhere   = '';
        
        for ($i = 0; $i < count($joins); $i++) {
            $tJoin   .= '$this->db->join(\''.$joins['join'.$i]['table'].'\', \''.$joins['join'.$i]['condition'].'\', \''.$joins['join'.$i]['type'].'\'); ';
        }
        foreach ($orWhere as $key => $value) {
            
            $replace    = str_replace($signsWhere, '', $key, $counted);
            $sign   = ($counted > 0) ? ' ' : ' = ';
            
            if ($cOrWhere > 0) {
                $tOrWhere   .= "$vOrWhere$key$sign\'$value\'";
            } else {
                $tOrWhere   .= "$key$sign\'$value\'";
            }
            $cOrWhere++;
        }
        
        foreach ($where as $key => $value) {
            $tWhere     .= '$this->db->where(\''.$key.'\', \''.$value.'\'); ';
        }
        foreach ($order as $key => $value) {
            $tOrder     .= '$this->db->order_by(\''.$key.'\', \''.$value.'\'); ';
        }
        
        $this->db->select($select);
        eval($tJoin);
        if (!empty($orWhere)) {
        eval('$this->db->where(\'('.$tOrWhere.')\');');
        }
        eval($tWhere);
        eval($tOrder);
        $this->db->limit($limit);
        // $result = $this->db;
        $result = $this->db->get($this->ma_table)->result();
        
        return    $result;
        
    }



}
