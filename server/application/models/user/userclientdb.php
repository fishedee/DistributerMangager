<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserClientDb extends CI_Model 
{
	var $tableName = "t_user_client";

	public function __construct(){
		parent::__construct();
	}

	public function getByUser($userId){
		$this->db->where("userId",$userId);
		return $this->db->get($this->tableName)->result_array();
	}

	public function delByUser( $userId ){
		$this->db->where("userId",$userId);
		return $this->db->delete($this->tableName);
	}
	
	public function addBatch( $data ){
		if( count($data) != 0 )
			$query = $this->db->insert_batch($this->tableName,$data);
	}

}
