<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OrderAddressDb extends CI_Model 
{
	var $tableName = "t_shop_order_address";

	public function __construct(){
		parent::__construct();
	}

	public function getByShopOrderId($shopOrderId){
		$this->db->where("shopOrderId",$shopOrderId);
		$query = $this->db->get($this->tableName)->result_array();
		return $query;
	}

	public function add( $data ){
		$this->db->insert($this->tableName,$data);
	}
}
