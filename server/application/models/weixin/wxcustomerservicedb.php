<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WxCustomerServiceDb extends CI_Model 
{
	var $tableName = "t_weixin_kf";
	
	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "userId" || $key == "kfId")
				$this->db->where($key,$value);
		}
	
		$count = $this->db->count_all_results($this->tableName);
	
		foreach( $where as $key=>$value ){
			if( $key == "userId" || $key == "kfId")
				$this->db->where($key,$value);
		}
			
		$this->db->order_by('kf_id','asc');
	
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);
	
		$query = $this->db->get($this->tableName)->result_array();
		return array(
				"count"=>$count,
				"data"=>$query
		);
	}
	
	public function add($data){
		$this->db->insert($this->tableName,$data);
	}
	
	public function addBatch($data,$userId){
		if( count($data) == 0 )
			return;
		
		$kfData = $this->search(array('userId'=>$userId),array());
		if(!count($kfData['data']) == 0)
			$this->db->delete($this->tableName, array('userId' => $userId));
		
		$this->db->insert_batch($this->tableName,$data);
		
	}
	
	public function del($userId){
		$this->db->delete($this->tableName, array('userId' => $userId));
	}
	

}
