<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class RemindDb extends CI_Model {

	private $tableName = 't_remind';

	public function __construct(){
		parent::__construct();
	}

	public function checkScore($userId){
		$this->db->where("userId",$userId);
		return $this->db->get($this->tableName)->result_array();
	}

	public function add($data){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	public function del($userId){
		$this->db->where('userId',$userId);
		$this->db->delete($this->tableName);
		return $this->db->affected_rows();
	}
}
