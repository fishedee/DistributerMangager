<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BoardDb extends CI_Model {

	private $tableName = 't_dish_board';

	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
			
		$this->db->order_by('boardNum','asc');
		
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);
		$this->db->where('userId',$where['userId']);
		if(isset($where['boardTypeId'])){
			$this->db->where('boardTypeId',$where['boardTypeId']);
		}
		$query = $this->db->get($this->tableName)->result_array();
		return array(
			"count"=>count($query),
			"data"=>$query
		);
	}

	//检测餐桌权限
	public function checkBoard($userId,$boardId){
		$condition['boardId'] = $boardId;
		$result = $this->db->select('userId')->from($this->tableName)->where($condition)->get()->result_array();
		if($result[0]['userId'] == $userId){
			return TRUE;
		}else{
			throw new CI_MyException(1,'您无权操作');
		}
	}

	//检测餐桌号
	public function checkBoardNum($userId,$boardNum){
		$condition['userId'] = $userId;
		$condition['boardNum'] = $boardNum;
		return $this->db->select('boardId')->from($this->tableName)->where($condition)->get()->result_array();
	}

	//检测餐桌管理人信息
	public function checkBoardUserId($boardId){
		return $this->db->select('userId')->from($this->tableName)->where(array('boardId'=>$boardId))->get()->result_array();
	}

	//获取餐桌信息
	public function getBoardInfo($boardId){
		$this->db->where('boardId',$boardId);
		return $this->db->get($this->tableName)->result_array();
	}

	//增加餐桌
	public function add($data){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	//删除餐桌
	public function del($userId,$boardId){
		if($this->checkBoard($userId,$boardId)){
			$this->db->where('boardId',$boardId);
			$this->db->delete($this->tableName);
		}
	}

	//修改
	public function mod($userId,$boardId,$data){
		if($this->checkBoard($userId,$boardId)){
			$this->db->where('boardId',$boardId);
			return $this->db->update($this->tableName,$data);
		}
	}

	//确认扫描
	public function scanConfirm($userId,$url){
		$this->db->where('userId',$userId);
		$this->db->where('url',$url);
		$this->db->select('boardId');
		return $this->db->get($this->tableName)->result_array();
	}

	//获取餐桌信息
	public function getBoard($boardId){
		$this->db->where('boardId',$boardId);
		return $this->db->get($this->tableName)->result_array();
	}
}
