<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class RedPackDb extends CI_Model 
{
	var $tableName = "t_red_pack";
	public function __construct(){
		parent::__construct();
	}

	public function getByUserId($userId){
		$this->db->where("userId",$userId);
		return $this->db->get($this->tableName)->result_array();
	}

	public function add( $data ){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	public function modByUserId( $userId , $data ){
		$this->db->where("userId",$userId);
		$this->db->update($this->tableName,$data);
	}

}
