<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Income_model extends My_Model
{
    // Specify the table targeted
    protected $ma_table = 'income';

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }
    public function GetIncomeValueId($id){
        $sql = "SELECT `income`.*,
        `income_head`.*
        FROM `income`
        LEFT JOIN `income_head` ON `income`.`inc_head_id`=`income_head`.`income_category`
        WHERE `income   `.`id`='$id'";
        $query=$this->db->query($sql);
        $result = $query->row();
        return $result;
    }

    public function getIncomeQty($logid){
        $sql = "SELECT * FROM `income` WHERE `id`='$logid'";
        $query=$this->db->query($sql);
        $result = $query->row();
        return $result;
    }


    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */

    public function search($text = null, $start_date = null, $end_date = null)
    {

        if (!empty($text)) {

            $this->datatables
                ->select('income.id,income.date,income.name,income.user,income.invoice_no,income.amount,income.amount_re,income.documents,income.note,income_head.income_category,income.status,income.inc_head_id')
                ->searchable('income.name,income.status,income.invoice_no,income.date,income_head.income_category,income.amount,income.amount_re')
                ->orderable('income.name,income.invoice_no,income.status,income.date,income.amount_re,income_head.income_category,income.amount')
                ->join("income_head", "income.inc_head_id = income_head.id")
                ->like('income.name', $text)
                ->from('income');

        } else {

            $this->datatables
                ->select('income.id,income.date,income.name,income.status,income.user,income.invoice_no,income.amount,income.amount_re,income.documents,income.note,income_head.income_category,income.inc_head_id')
                ->searchable('income.name,income.status,income.invoice_no,income.date,income_head.income_category,income.amount,income.amount_re')
                ->orderable('income.name,income.user,income.status,income.invoice_no,income.date,income_head.income_category,income.amount,income.amount_re')
                ->join("income_head", "income.inc_head_id = income_head.id")
                ->where('income.date <=', $end_date)
                ->where('income.date >=', $start_date)
                ->from('income');
        }


        return $this->datatables->generate('json');
    }

    public function GetCaissesQty($id){
        $sql = "SELECT * FROM `income` WHERE `id`='$id'";
        $query=$this->db->query($sql);
        $result = $query->row();
        return $result;
    }

    public function Update_Caisses($id,$data){
        $this->db->where('id',$id);
        $this->db->update('income',$data);
    }

    public function searchincomegroup($start_date = null, $end_date = null, $head_id = null)
    {

        $this->datatables
            ->select('GROUP_CONCAT(income.id,"@",income.name,"@",income.invoice_no,"@",income.date,"@",income.amount,income.amount_re,income.user,income.status,income.amount_re) as income, income_head.income_category,sum(income.amount) as total_amount')
            ->searchable('income_head.income_category,income.id,income.name,income.user,income.date,income.invoice_no,income.status,income.amount,income.amount_re')
            ->orderable('income_head.income_category,income.id,income.name,income.user,income.date,income.invoice_no,income.status,income.amount_re')
            ->join('income_head', 'income.inc_head_id = income_head.id')
            ->where('income.date >=', $start_date)
            ->where('income.date <=', $end_date)
            ->from('income');
        if ($head_id != null) {
            $this->datatables->where('income.inc_head_id', $head_id);
        }
        $this->datatables->group_by('income.inc_head_id');
        return $this->datatables->generate('json');
    }

    public function getIncomeHeadsData($start_date, $end_date)
    {

        $condition = "date_format(date,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";

        $this->db->select('sum(amount) as total,income_category')->from('income');
        $this->db->join('income_head', 'income.inc_head_id = income_head.id');
        $this->db->where($condition)->group_by('income_head.id');
        $r = $this->db->get()->result_array();
        return $r;
    }

    public function getfilesHeadsData($start_date, $end_date)
    {

        $condition = "date_format(date,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";

        $this->db->select('sum(amount) as total,income_category')->from('files');
        $this->db->join('income_head', 'income.inc_head_id = income_head.id');
        $this->db->where($condition)->group_by('income_head.id');
        $r = $this->db->get()->result_array();
        return $r;
    }



    public function get($id = null)
    {
        $this->db->select('income.id,income.date,income.name,income.est_actif,income.user,income.invoice_no,income.status,income.amount,income.amount_re,income.documents,income.note,income_head.income_category,income.inc_head_id');
        $this->db->where('income.est_actif', '1')
            ->from('income');
        $this->db->join('income_head', 'income.inc_head_id = income_head.id');
        if ($id != null) {
            $this->db->where('income.id', $id);
        } else {
            $this->db->order_by('income.id', 'DESC');
        }

        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function got($id = null)
    {
        $this->db->select('income.id,income.date,income.name,income.est_actif,income.user,income.invoice_no,income.status,income.amount,income.amount_re,income.documents,income.note,income_head.income_category,income.inc_head_id')

            ->from('income');
        $this->db->join('income_head', 'income.inc_head_id = income_head.id');
        if ($id != null) {
            $this->db->where('income.id', $id);
        } else {
            $this->db->order_by('income.id', 'DESC');
        }

        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function gets($id = null)
    {
        $this->db->select('income.id,income.date,income.name,income.invoice_no,income.amount,income.amount_re,income.est_actif,income.documents,income.note,income_head.income_category,income.inc_head_id')->from('files');
        $this->db->join('income_head', 'income.inc_head_id = income_head.id');
        if ($id != null) {
            $this->db->where('income.id', $id);
        } else {
            $this->db->order_by('income.id', 'DESC');
        }

        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    /**
     * This function is used to get income list by using datatable
     */
    public function getincomelist()
    {
        $this->datatables
            ->select('income.id,income.date,income.name,income.est_actif,income.user,income.invoice_no,income.status,income.amount,income.amount_re,income.documents,income.note,income_head.income_category,income.inc_head_id')
            ->searchable('income.name,income,income.user,income.invoice_no,income.status,income.date,income_head.income_category,income.amount,income.amount_re,income.note')
            ->orderable('income.name,income.user,income.note,income.invoice_no,income.status,income.date,income_head.income_category,income.amount,income.amount_re')
            ->join("income_head", "income.inc_head_id = income_head.id")
            ->sort('income.id', 'desc');

        $this->db->where('income.deleted', 1)
            ->from('income');
        return $this->datatables->generate('json');
    }

    public function getfileslist()
    {
        $this->datatables
            ->select('income.id,income.date,income.name,income.user,income.status,income.invoice_no,income.amount,income.amount_re,income.documents,income.note,income_head.income_category,income.inc_head_id')
            ->searchable('income.name,income.user,income.invoice_no,income.status,income.date,income_head.income_category,income.amount,income.amount_re,income.note')
            ->orderable('income.name,income.user,income.note,income.status,income.invoice_no,income.date,income_head.income_category,income.amount,income.amount_re')
            ->join("income_head", "income.inc_head_id = income_head.id")
            ->sort('income.id', 'desc')
            ->from('files');
        return $this->datatables->generate('json');
    }

    public function getIncomesQty($logid){
        $sql = "SELECT * FROM `income` WHERE `id`='$logid'";
        $query=$this->db->query($sql);
        $result = $query->row();
        return $result;
    }

    public function getFilesQty($logid){
        $sql = "SELECT * FROM `files` WHERE `id`='$logid'";
        $query=$this->db->query($sql);
        $result = $query->row();
        return $result;
    }
    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id)
    {

        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id)
            ->set('deleted', '0');
        $this->db->update('income');

        $message   = DELETE_RECORD_CONSTANT . " On  Income   id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {

            return $id;
        }
    }

    /**
     * This function will take the post data passed from the controller
     * If id is present, then it will do an update
     * else an insert. One function doing both add and edit.
     * @param $data
     */
    public function add($data)
    {

        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {

            $this->db->where('id', $data['id']);
            $this->db->update('income', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  Income   id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
        } else {
            $this->db->insert('income', $data);
            $return_value = $this->db->insert_id();
            $message      = INSERT_RECORD_CONSTANT . " On  Income   id " . $return_value;
            $action       = "Insert";
            $record_id    = $return_value;
        }

        $this->log($message, $record_id, $action);

        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {

            return $record_id;
        }
    }

    public function check_Exits_group($data)
    {
        $this->db->select('*');
        $this->db->from('income');
        $this->db->where('session_id', $this->current_session);
        $this->db->where('feetype_id', $data['feetype_id']);
        $this->db->where('class_id', $data['class_id']);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return false;
        } else {
            return true;
        }
    }

    public function getTypeByFeecategory($type, $class_id)
    {
        $this->db->select('income.id,income.session_id,income.amount,income.invoice_no,income.amount_re,income.documents,income.note,income_head.class,feetype.type')->from('income');
        $this->db->join('income_head', 'income.class_id = income_head.id');
        $this->db->join('feetype', 'income.feetype_id = feetype.id');
        $this->db->where('income.class_id', $class_id);
        $this->db->where('income.feetype_id', $type);
        $this->db->where('income.session_id', $this->current_session);
        $this->db->order_by('income.id');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getTotalExpenseBydate($date)
    {
        $query = 'SELECT sum(amount) as `amount` FROM `income` where date=' . $this->db->escape($date);
        $query = $this->db->query($query);
        return $query->row();
    }

    public function getTotalExpenseBwdate($date_from, $date_to)
    {
        $query = 'SELECT sum(amount) as `amount` FROM `income` where date between ' . $this->db->escape($date_from) . ' and ' . $this->db->escape($date_to);

        $query = $this->db->query($query);
        return $query->row();
    }


    public function getTotalIncome(){
        $query = 'SELECT sum(amount_re) as `amount` FROM `income`';
        $query = $this->db->query($query);
        return $query->row();
    }
    public function getIncomeWithReappro($start_date, $end_date)
    {
        $this->db->select('income.*, income_head.income_category');
        $this->db->where('income.deleted', 1);
        $this->db->where('income.est_actif', 1);
        $this->db->from('income');
        $this->db->join('income_head', 'income.inc_head_id = income_head.id');
        $this->db->where('income.date >=', $start_date);
        $this->db->where('income.date <=', $end_date);

        $query = $this->db->get();
        $incomes = array();

        // Parcourez chaque revenu
        foreach ($query->result() as $row) {
            $incomeData = [
                'income' => $row,
                'income_processing' => [], // Initialise la liste des réapprovisionnements
            ];

            // var_dump($row->id);

            // Obtenez les réapprovisionnements pour cet ID
            $reapprovisionnements = $this->getReapprovisionnements($row->id);

            // Ajoutez les réapprovisionnements à cet income
            foreach ($reapprovisionnements as $reappro) {
                $incomeData['income_processing'][] = $reappro;
            }

            // Ajoutez l'entrée complète au tableau final
            $incomes[] = $incomeData;
        }

        // exit;

        // var_dump($incomes);
        // exit;

        return $incomes;
    }

    public function getReapprovisionnements($incomeID)
    {
        $this->db->select('*');
        $this->db->where('income_processing.deleted', 1);
        $this->db->from('income_processing');
        $this->db->where('income_id', $incomeID);
        $this->db->where('amount >', 0); // Filtrer les montants positifs
        $query = $this->db->get();
        return $query->result();
    }




}