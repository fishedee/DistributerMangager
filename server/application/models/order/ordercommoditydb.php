<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OrderCommodityDb extends CI_Model 
{
	var $tableName = "t_shop_order_commodity";

	public function __construct(){
		parent::__construct();
	}

	public function getByShopOrderId($shopOrderId){
		$this->db->where("shopOrderId",$shopOrderId);
		$query = $this->db->get($this->tableName)->result_array();
		return $query;
	}

	public function addBatch( $data ){
		$this->db->insert_batch($this->tableName,$data);
	}
}
