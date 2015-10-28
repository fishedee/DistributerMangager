<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BoardTypeAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('board/boardTypeDb','boardTypeDb');
		$this->load->model('user/userAppAo','userAppAo');
		$this->load->library('http');
	}

	public function search($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		return $this->boardTypeDb->search($dataWhere,$dataLimit);
	}

	//增加餐桌类型
	public function add($userId,$data){
		foreach ($data as $key => $value) {
			if(!$value){
				throw new CI_MyException(1,'请输入餐桌类型');
			}
		}
		$data['userId'] = $userId;
		return $this->boardTypeDb->add($data);
	}

	//获取餐桌类型
	public function getType($userId,$boardTypeId){
		$info = $this->boardTypeDb->getType($boardTypeId);
		if($info){
			if($info[0]['userId'] != $userId){
				throw new CI_MyException(1,'无效查看');
			}else{
				return $info[0];
			}
		}else{
			throw new CI_MyException(1,'无效餐桌类型id');
		}
	}

	//修改
	public function mod($userId,$boardTypeId,$data){
		foreach ($data as $key => $value) {
			if(!$value){
				throw new CI_MyException(1,'请输入餐桌类型');
			}
		}
		$info = $this->getType($userId,$boardTypeId);
		return $this->boardTypeDb->mod($boardTypeId,$data);
	}

	//删除
	public function del($userId,$boardTypeId){
		$info = $this->getType($userId,$boardTypeId);
		return $this->boardTypeDb->del($boardTypeId);
	}

	//获取全部餐桌类型
	public function getAllType($userId){
		$info = $this->boardTypeDb->getAllType($userId);
		$arr  = array();
		if($info){
			foreach ($info as $key => $value) {
				$arr[$value['boardTypeId']] = $value['typeName'];
			}
			return $arr;
		}else{
			return $arr;
		}
	}

	public function fontGetAllType($userId){
		return $this->boardTypeDb->getAllType($userId);
	}
}
