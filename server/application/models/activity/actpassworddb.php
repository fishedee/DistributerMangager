<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ActPasswordDb extends CI_Model {

	private $tableName = 't_activity_password';

	public function __construct(){
		parent::__construct();
	}

	//获取密码
	public function getPassword($userId){
		$this->db->where('userId',$userId);
		return $this->db->get($this->tableName)->result_array();
	}

	//更改密码
	public function changePassword($userId,$password,$option){
		if($option == 1){
			//更新
			$this->db->where('userId',$userId);
			$data['password'] = $password;
			$this->db->update($this->tableName,$data);
			return $this->db->affected_rows();
		}elseif($option == 2){
			//增加
			$data['userId'] = $userId;
			$data['password'] = $password;
			$this->db->insert($this->tableName,$data);
			return $this->db->insert_id();
		}
	}
}
