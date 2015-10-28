<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class RoomAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('room/roomDb','roomDb');
	}

	//获取餐厅信息
	public function getRoomInfo($userId,$select = array()){
		if($select){
			foreach ($select as $key => $value) {
				$this->db->select($value);
			}
		}
		return $this->roomDb->getRoomInfo($userId);
	}

	//新增或修改餐厅信息
	public function amod($userId,$data){
		return $this->roomDb->amod($userId,$data);
	}
}
