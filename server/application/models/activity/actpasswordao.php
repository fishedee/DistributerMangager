<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ActPasswordAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('activity/actPasswordDb','actPasswordDb');
	}

	//获取密码
	public function getPassword($userId){
		if(!$userId){
			throw new CI_MyException(1,'请先登陆');
		}
		return $this->actPasswordDb->getPassword($userId);
	}

	//更改密码
	public function changePassword($userId,$password){
		if(!$userId){
			throw new CI_MyException(1,'请先登陆');
		}
		$result = $this->getPassword($userId);
		if($result){
			$option = 1; //update
		}else{  
			$option = 2; //add
		}
		return $this->actPasswordDb->changePassword($userId,$password,$option);
	}
}
