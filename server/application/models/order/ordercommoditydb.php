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

	//获取订单明细信息
	public function getOrderCommodity($shopOrderCommodityId){
		$this->db->where('shopOrderCommodityId',$shopOrderCommodityId);
		return $this->db->get($this->tableName)->result_array();
	}

	//更改订单明细信息
	public function mod($shopOrderCommodityId,$data){
		$data['modifyTime'] = date('Y-m-d H:i:s',time());
		$this->db->where('shopOrderCommodityId',$shopOrderCommodityId);
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}

	//检测评论条数
	public function checkComment($shopOrderId){
		$this->db->where('comment','0');
		$this->db->where('shopOrderId',$shopOrderId);
		return $this->db->get($this->tableName)->result_array();
	}
}
