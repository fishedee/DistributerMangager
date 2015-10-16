<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MoneyLogDb extends CI_Model{

	private $tableName = 't_money_log';

    public function __construct(){
        parent::__construct();
    }

    public function add($data){
    	$this->db->insert($this->tableName,$data);
    	return $this->db->insert_id();
    }

    //获取账户明细
    public function getMoneyLog($vender,$clientId){
    	$this->db->where('vender',$vender);
    	$this->db->where('clientId',$clientId);
    	return $this->db->get($this->tableName)->result_array();
    }
}