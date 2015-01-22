<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserDb extends CI_Model 
{
	var $tableName = "t_user";

	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "name" || $key == "company" || $key == "phone")
				$this->db->like($key,$value);
			else if( $key == "type" )
				$this->db->where($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if( $key == "name" || $key == "company" || $key == "phone")
				$this->db->like($key,$value);
			else if( $key == "type" )
				$this->db->where($key,$value);
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

	public function get($userId){
		$this->db->where("userId",$userId);
		$query = $this->db->get($this->tableName)->result_array();
		if( count($query) == 0 )
			throw new CI_MyException('不存在此用户');
		return $query[0];
	}
	
	public function getByIds($userId){
		if( count($userId) == 0 )
			return array();
		$this->db->where_in("userId",$userId);
		return $this->db->get($this->tableName)->result_array();
	}

	public function del( $userId ){
		$this->db->where("userId",$userId);
		$this->db->delete($this->tableName);
	}
	
	public function add( $data ){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	public function mod( $userId , $data )
	{
		$this->db->where("userId",$userId);
		$this->db->update($this->tableName,$data);
	}

	public function getByIdAndPass($userId,$password){
		$this->db->where("userId",$userId);
		$this->db->where("password",$password);
		return $this->db->get($this->tableName)->result_array();
	}
	
	public function getByNameAndPass($name,$password){
		$this->db->where("name",$name);
		$this->db->where("password",$password);
		return $this->db->get($this->tableName)->result_array();
	}

	public function getByName($name){
		$this->db->where("name",$name);
		return $this->db->get($this->tableName)->result_array();
	}

}
