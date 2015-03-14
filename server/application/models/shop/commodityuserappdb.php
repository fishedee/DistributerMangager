<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityUserAppDb extends CI_Model {
	var $tableName = "t_shop_commodity";
	var $tableName2 = "t_user_app";
	var $tableName2Join = "t_shop_commodity.userId = t_user_app.userId";
	
	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "title" || $key == "summary" || $key == "remark" || $key == 'appName')
				$this->db->like($key,$value);
			else if( $key == 'shopCommodityClassifyId' || $key == 'state')
				$this->db->where($key,$value);
		}
		$this->db->from($this->tableName);
		$this->db->join($this->tableName2,$this->tableName2Join);
		
		$count = $this->db->count_all_results();
		
		foreach( $where as $key=>$value ){
			if( $key == "title" || $key == "summary" || $key == "remark" || $key == 'appName')
				$this->db->like($key,$value);
			else if( $key == 'shopCommodityClassifyId' || $key == 'state')
				$this->db->where($key,$value);
		}
		$this->db->order_by('createTime','desc');
		
		$this->db->from($this->tableName);
		$this->db->join($this->tableName2,$this->tableName2Join);
		$this->db->select('shopCommodityId,appName,isLink,shopLinkCommodityId,shopCommodityClassifyId,title,icon,introduction,price,oldPrice,inventory,state,t_shop_commodity.userId as userId,t_shop_commodity.createTime as createTime,t_shop_commodity.modifyTime as modifyTime');
		
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);

		$query = $this->db->get();
		return array(
			"count"=>$count,
			"data"=>$query->result_array()
		);
	}

}
