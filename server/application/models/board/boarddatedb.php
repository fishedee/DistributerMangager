<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BoardDateDb extends CI_Model {

	private $tableName = 't_dish_board_date';

	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
			
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);
		$this->db->where('userId',$where['userId']);
		$query = $this->db->get($this->tableName)->result_array();
		return array(
			"count"=>count($query),
			"data"=>$query
		);
	}

	//增加预定时间
	public function add($data){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	//获取预定时间信息
	public function getDate($dateId){
		$this->db->where('dateId',$dateId);
		return $this->db->get($this->tableName)->result_array();
	}

	//修改
	public function mod($dateId,$data){
		$this->db->where('dateId',$dateId);
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}

	//删除
	public function del($dateId){
		$this->db->where('dateId',$dateId);
		$this->db->delete($this->tableName);
		return $this->db->affected_rows();
	}

	//获取时间
	public function getOrderTime($userId,$day = 0){
		$this->db->where('userId',$userId);
		$this->db->where('day',$day);
		$this->db->select('dateId');
		$this->db->select('time');
		return $this->db->get($this->tableName)->result_array();
	}
}
