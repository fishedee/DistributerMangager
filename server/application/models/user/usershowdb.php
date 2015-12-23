<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserShowDb extends CI_Model {

	private $tableName = 't_user_show';

	public function __construct(){
		parent::__construct();
	}

	//增加
	public function add($data){
		$data['createTime'] = date('Y-m-d H:i:s',time());
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	//获取买家秀的图片
	public function getShowPic($userId,$clientId){
		$this->db->where('userId',$userId);
		$this->db->where('clientId',$clientId);
		return $this->db->get($this->tableName)->result_array();
	}

	public function getDetail($showId){
		$this->db->where('showId',$showId);
		return $this->db->get($this->tableName)->row_array();
	}

	//删除图片
	public function del($showId){
		$this->db->where('showId',$showId);
		$this->db->delete($this->tableName);
		return $this->db->affected_rows();
	}
}
