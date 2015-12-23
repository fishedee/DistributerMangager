<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CompanyShopDb extends CI_Model 
{
	var $tableName = "t_user_company_shop";

	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "name" )
				$this->db->like($key,$value);
			else if( $key == "userId" )
				$this->db->where($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if( $key == "name" )
				$this->db->like($key,$value);
			else if( $key == "userId" )
				$this->db->where($key,$value);
		}
		
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);

		$query = $this->db->get($this->tableName)->result_array();
		return array(
			"count"=>$count,
			"data"=>$query
		);
	}

	//获取门店信息
	public function get($companyShopId){
		$this->db->where('companyShopId',$companyShopId);
		return $this->db->get($this->tableName)->row_array();
	}

	//增加门店信息
	public function add($data){
		$data['createTime'] = date('Y-m-d H:i:s',time());
		$data['modifyTime'] = date('Y-m-d H:i:s',time());
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	//修改门店信息
	public function mod($companyShopId,$data){
		$data['modifyTime'] = date('Y-m-d H:i:s',time());
		$this->db->where('companyShopId',$companyShopId);
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}

	//删除门店信息
	public function del($companyShopId){
		$this->db->where('companyShopId',$companyShopId);
		$this->db->delete($this->tableName);
		return $this->db->affected_rows();
	}

	//前端获取门店信息
	public function getShop($userId){
		$this->db->where('userId',$userId);
		return $this->db->get($this->tableName)->result_array();
	}
}
