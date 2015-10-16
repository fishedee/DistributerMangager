<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PointsDb extends CI_Model{

	private $tableName = 't_points_product';

    public function __construct(){
        parent::__construct();
    }

    //查看提现申请
    public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "state" )
				$this->db->where($key,$value);
			else if( $key == "vender" )
				$this->db->where($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if( $key == "state" )
				$this->db->where($key,$value);
			else if( $key == "vender" )
				$this->db->where($key,$value);
		}
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);
		$this->db->order_by('state','DESC');
		$query = $this->db->get($this->tableName)->result_array();
		return array(
			"count"=>$count,
			"data"=>$query
		);
	}

	//获取商品详情
	public function getProductInfo($productId){
		$this->db->where('productId',$productId);
		return $this->db->get($this->tableName)->result_array();
	}

	//增加商品
	public function add($data){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	//编辑商品
	public function mod($productId,$data){
		$this->db->where('productId',$productId);
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}

	//前端获取信息
	public function fontGetProductInfo($userId){
		$this->db->where('vender',$userId);
		$this->db->where('state',1);
		return $this->db->get($this->tableName)->result_array();
	}
}