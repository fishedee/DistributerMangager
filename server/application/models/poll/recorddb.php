<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class RecordDb extends CI_Model {

	private $tableName = 't_poll_record';

	public function __construct(){
		parent::__construct();
	}

	//检测报名
	public function checkVote($userId,$clientId){
		$this->db->where('userId',$userId);
		$this->db->where('clientId',$clientId);
		return $this->db->get($this->tableName)->result_array();
	}

	//报名
	public function add($data){
		$data['createTime'] = date('Y-m-d H:i:s',time());
		$data['modifyTime'] = date('Y-m-d H:i:s',time());
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}
}
