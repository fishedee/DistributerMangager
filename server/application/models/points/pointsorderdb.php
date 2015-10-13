<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PointsOrderDb extends CI_Model{

	private $tableName = 't_points_order';

    public function __construct(){
        parent::__construct();
    }

    //兑换奖品
    public function add($data){
    	$this->db->insert($this->tableName,$data);
    	return $this->db->affected_rows();
    }

    //我的奖品
    public function myProduct($userId,$clientId){
    	$this->db->where('vender',$userId);
    	$this->db->where('clientId',$clientId);
    	return $this->db->get($this->tableName)->result_array();
    }
}