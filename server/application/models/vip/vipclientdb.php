<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class VipClientDb extends CI_Model 
{
	var $tableName = "t_vip_client";
	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "name" || $key == "phone")
				$this->db->like($key,$value);
			else if( $key == "userId" )
				$this->db->where($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if( $key == "name" || $key == "phone")
				$this->db->like($key,$value);
			else if( $key == "userId" )
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

	public function getByUserAndClient($userId,$clientId)
	{
		$this->db->where("userId",$userId);
		$this->db->where("clientId",$clientId);
		return $this->db->get($this->tableName)->result_array();
	}

	public function add( $data )
	{
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	public function modByUserAndClient($userId,$clientId,$data)
	{
		$this->db->where("userId",$userId);
		$this->db->where("clientId",$clientId);
		return $this->db->update($this->tableName,$data);
	}

}
