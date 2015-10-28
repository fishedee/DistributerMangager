<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BookingRemindDb extends CI_Model {

	private $tableName = 't_dish_booking_remind';

	public function __construct(){
		parent::__construct();
	}

	//检测用户是否需要验证
	public function checkVerify($userId,$clientId){
		$this->db->where('userId',$userId);
		$this->db->where('clientId',$clientId);
		return $this->db->get($this->tableName)->result_array();
	}

	//插入
	public function add($data){
		$data['createTime'] = date('Y-m-d H:i:s',time());
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}
}