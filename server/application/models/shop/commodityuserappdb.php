<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityUserAppDb extends CI_Model {
	var $tableName = "t_shop_commodity";
	var $tableName2 = "t_user_app";
	var $tableName2Join = "t_shop_commodity.userId = t_user_app.userId";
	
	public function __construct(){
		parent::__construct();
	}

	public function get($shopCommodityId){
		$result = $this->search(array('shopCommodityId'=>array($shopCommodityId)),array())['data'];
		if(count($result) == 0 )
			throw new CI_MyException(1,'找不到该商品');
		return $result[0];
	}

	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "title" || $key == "introduction" || $key == "remark" || $key == 'appName')
				$this->db->like($key,$value);
			else if($key == 'shopCommodityClassifyId' || $key == 'state')
                $this->db->where($key, $value);
           	else if($key == 'userId')
           		$this->db->where('t_shop_commodity.userId', $value);
            else if($key == 'shopCommodityId')
                $this->db->where_in($key, $value);
		}
		$this->db->from($this->tableName);
		$this->db->join($this->tableName2,$this->tableName2Join);
		
		$count = $this->db->count_all_results();
		
		foreach( $where as $key=>$value ){
			if( $key == "title" || $key == "introduction" || $key == "remark" || $key == 'appName')
				$this->db->like($key,$value);
			else if($key == 'shopCommodityClassifyId' || $key == 'state')
                $this->db->where($key, $value);
           	else if($key == 'userId')
           		$this->db->where('t_shop_commodity.userId', $value);
            else if($key == 'shopCommodityId')
                $this->db->where_in($key, $value);
		}
		$this->db->order_by('createTime','desc');
		
		$this->db->from($this->tableName);
		$this->db->join($this->tableName2,$this->tableName2Join);
		$this->db->select('shopCommodityId,
			t_shop_commodity.userId as userId,
			isLink,
			detail,
			shopLinkCommodityId,
			shopCommodityClassifyId,
			title,
			icon,
			introduction,
			price,
			oldPrice,
			inventory,
			state,
			appName as userAppName, 
			t_shop_commodity.remark as remark,
			t_shop_commodity.createTime as createTime,
			t_shop_commodity.modifyTime as modifyTime');
		
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);

		$query = $this->db->get();
		return array(
			"count"=>$count,
			"data"=>$query->result_array()
		);
	}

}
