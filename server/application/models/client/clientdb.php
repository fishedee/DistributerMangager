<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ClientDb extends CI_Model 
{
	var $tableName = "t_client";

	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "name" )
				$this->db->like($key,$value);
			else if( $key == "type" || $key == 'gender' || $key == 'userId' || $key == 'openId')
				$this->db->where($key,$value);
			else if( $key == 'clientId')
				$this->db->where_in($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if( $key == "name" )
				$this->db->like($key,$value);
			else if( $key == "type" || $key == 'gender' || $key == 'userId' || $key == 'openId')
				$this->db->where($key,$value);
			else if( $key == 'clientId')
				$this->db->where_in($key,$value);
		}
			
		$this->db->order_by('createTime','desc');
		
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);

		$query = $this->db->get($this->tableName);
		return array(
			"count"=>$count,
			"data"=>$query->result_array()
		);
	}

	public function get($clientId){
		$this->db->where("clientId",$clientId);
		$query = $this->db->get($this->tableName)->result_array();
		if( count($query) == 0 )
			throw new CI_MyException(1,'找不到此用户');
		return $query[0];
	}

	public function add( $data ){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	public function mod( $clientId , $data ){
		$this->db->where("clientId",$clientId);
		$this->db->update($this->tableName,$data);
	}
}
