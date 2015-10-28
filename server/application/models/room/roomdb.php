<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class RoomDb extends CI_Model {

	private $tableName = 't_dish_room';

	public function __construct(){
		parent::__construct();
	}

	public function getRoomInfo($userId){
		$this->db->where('userId',$userId);
		$query = $this->db->get($this->tableName)->result_array();
		if($query){
			return $query[0];
		}else{
			return array();
		}
	}

	public function amod($userId,$data){
		$condition['userId'] = $userId;
		$result = $this->db->select('roomId')->from($this->tableName)->where($condition)->get()->result_array();
		if($result){
			$this->db->where('roomId',$result[0]['roomId']);
			$this->db->update($this->tableName,$data);
			return $this->db->affected_rows();
		}else{
			$data['userId'] = $userId;
			$this->db->insert($this->tableName,$data);
			return $this->db->insert_id();
		}
	}
}
