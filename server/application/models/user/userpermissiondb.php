<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserPermissionDb extends CI_Model 
{
	var $tableName = "t_user_permission";

	public function __construct(){
		parent::__construct();
	}

	public function getByUser($userId){
		$this->db->where("userId",$userId);
		$query = $this->db->get($this->tableName)->result_array();
		return array(
				"code"=>0,
				"msg"=>"",
				"data"=>$query
			    );
	}

	public function delByUser( $userId ){
		$this->db->where("userId",$userId);
		$query = $this->db->delete($this->tableName);
		return array(
			"code"=>0,
			"msg"=>"",
			"data"=>""
			);
	}
	
	public function addBatch( $data ){
		if( count($data) != 0 )
			$query = $this->db->insert_batch($this->tableName,$data);
		return array(
			"code"=>0,
			"msg"=>"",
			"data"=>""
			);
	}

}
