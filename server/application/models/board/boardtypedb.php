<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BoardTypeDb extends CI_Model {

	private $tableName = 't_dish_board_type';

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

	//增加餐桌类型
	public function add($data){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	//获取餐桌类型
	public function getType($boardTypeId){
		$this->db->where('boardTypeId',$boardTypeId);
		return $this->db->get($this->tableName)->result_array();
	}

	//修改餐桌类型
	public function mod($boardTypeId,$data){
		$this->db->where('boardTypeId',$boardTypeId);
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}

	//删除餐桌类型
	public function del($boardTypeId){
		$this->db->where('boardTypeId',$boardTypeId);
		$this->db->delete($this->tableName);
		return $this->db->affected_rows();
	}

	//获取全部餐桌类型
	public function getAllType($userId){
		$this->db->where('userId',$userId);
		return $this->db->get($this->tableName)->result_array();
	}
}
