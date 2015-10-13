<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserPermissionDb extends CI_Model 
{
	var $tableName = "t_user_permission";

	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "permissionId" )
				$this->db->where_in($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if( $key == "permissionId" )
				$this->db->where_in($key,$value);
		}
			
		$this->db->order_by('createTime','desc');
		
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);

		$query = $this->db->get($this->tableName)->result_array();
		return array(
			"count"=>$count,
			"data"=>$query
		);
	}

	public function getByUser($userId){
		$this->db->where("userId",$userId);
		return $this->db->get($this->tableName)->result_array();
	}

	public function delByUser( $userId ){
		$this->db->where("userId",$userId);
		$this->db->delete($this->tableName);
	}
	
	public function addBatch( $data ){
		if( count($data) != 0 )
			$query = $this->db->insert_batch($this->tableName,$data);
	}

	public function checkPermissionId($userId,$permissionId){
		$this->db->where('userId',$userId);
		$this->db->where('permissionId',$permissionId);
		$this->db->select('userPermissionId');
		return $this->db->get($this->tableName)->result_array();
	}

	public function checkPermissionId2($userId,$condition){
		$this->db->where('userId',$userId);
		$this->db->where($condition);
		return $this->db->get($this->tableName)->result_array();
	}

}
