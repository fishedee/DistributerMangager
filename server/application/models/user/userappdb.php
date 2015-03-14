<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserAppDb extends CI_Model 
{
	var $tableName = "t_user_app";
	var $tableField = array(
		'userId'=>0,
		'appName'=>'',
		'appId'=>'',
		'appKey'=>'',
		'mchId'=>'',
		'mchKey'=>'',
		'mchSslCert'=>'',
		'mchSslKey'=>'',
		'remark'=>''
	);
	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "remark" )
				$this->db->like($key,$value);
			else if( $key == "userId" )
				$this->db->where($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if( $key == "remark" )
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

	public function getByUser($userId){
		$this->db->where("userId",$userId);
		$query = $this->db->get($this->tableName)->result_array();
		return $query;
	}
	
	public function add($data){
		$data = array_merge($this->tableField,array_intersect_key($data,$this->tableField));
		return $this->db->insert($this->tableName,$data);
	}

	public function modByUser( $userId , $data ){
		$data = array_intersect_key($data,$this->tableField);
		if( count($data) == 0 )
			return;
		$this->db->where("userId",$userId);
		$this->db->update($this->tableName,$data);
	}

}
