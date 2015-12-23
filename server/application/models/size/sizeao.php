<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SizeAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('size/sizeDb','sizeDb');
	}
	
	public function search($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		return $this->sizeDb->search($dataWhere,$dataLimit);
	}

	private function checkState($userId,$clientId){
		return $this->sizeDb->checkState($userId,$clientId);
	}

	//提交申请
	public function add($userId,$clientId,$data){
		$result = $this->checkState($userId,$clientId);
		if($result){
			throw new CI_MyException(1,'您的申请已经提交,受理之前不能重复提交,请耐心等待');
		}
		foreach ($data as $key => $value) {
			if(!$value){
				throw new CI_MyException(1,'参数均不能为空');
			}
		}
		$data['userId'] = $userId;
		$data['clientId'] = $clientId;
		return $this->sizeDb->add($data);
	}

	public function get($userId,$sizeId){
		$info = $this->sizeDb->get($sizeId);
		if($info){
			if($info['userId'] != $userId){
				throw new CI_MyException(1,'无效操作');
			}
			return $info;
		}else{
			throw new CI_MyException(1,'无效申请id');
		}
	}

	public function checkAccept($userId,$sizeId){
		$info = $this->get($userId,$sizeId);
		if($info['state'] == 1){
			throw new CI_MyException(1,'该申请已经受理');
		}
		return 0;
	}

	public function accept($userId,$sizeId){
		$info = $this->get($userId,$sizeId);
		if($info['state'] == 1){
			throw new CI_MyException(1,'该申请已经受理');
		}
		$data['state'] = 1;
		$result = $this->sizeDb->mod($sizeId,$data);
		if($result){
			//推送客服消息
			$clientId = $info['clientId'];
			$this->load->model('user/userAppAo','userAppAo');
			$content = '我们已经收到您的申请,会尽快为您上门量尺寸,请耐心等待';
			$this->userAppAo->sendCustomMsg($userId,$clientId,$content);
		}else{
			throw new CI_MyException(1,'受理失败');
		}
	}

	public function del($userId,$sizeId){
		$this->get($userId,$sizeId);
		return $this->sizeDb->del($sizeId);
	}
}
