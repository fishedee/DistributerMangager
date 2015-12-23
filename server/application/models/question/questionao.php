<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class QuestionAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('question/questionDb','questionDb');
		$this->load->model('question/questionCollectDb','questionCollectDb');
		$this->load->model('client/clientAo','clientAo');
	}
	
	public function search($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		return $this->questionDb->search($dataWhere,$dataLimit);
	}

	public function seachCollect($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		$data = $this->questionCollectDb->search($dataWhere,$dataLimit);
		foreach ($data['data'] as $key => $value) {
			$clientInfo = $this->clientAo->get($userId,$value['clientId']);
			$data['data'][$key]['nickName'] = base64_decode($clientInfo['nickName']);
			$data['data'][$key]['headImgUrl'] = $clientInfo['headImgUrl'];
		}
		return $data;
	}

	private function checkState($userId,$clientId){
		return $this->questionDb->checkState($userId,$clientId);
	}

	//提交申请
	public function add($userId,$data){
		if(!$data['question']){
			throw new CI_MyException(1,'问题不能为空');
		}
		$data['userId'] = $userId;
		return $this->questionDb->add($data);
	}

	public function get($userId,$questionId){
		$info = $this->questionDb->get($questionId);
		if($info){
			if($info['userId'] != $userId){
				throw new CI_MyException(1,'无效操作');
			}
			return $info;
		}else{
			throw new CI_MyException(1,'无效申请id');
		}
	}

	public function mod($userId,$questionId,$data){
		if(!$data['question']){
			throw new CI_MyException(1,'问题不能为空');
		}
		$this->get($userId,$questionId);
		return $this->questionDb->mod($questionId,$data);
	}

	public function del($userId,$questionId){
		$this->get($userId,$questionId);
		return $this->questionDb->del($questionId);
	}

	public function getQuestion($userId){
		return $this->questionDb->getQuestion($userId);
	}

	public function collect($userId,$clientId,$data){
		//检测提交
		$result = $this->questionCollectDb->checkCollect($clientId);
		if($result){
			throw new CI_MyException(1,'您已经评价过了');
		}
		$arr = array();
		foreach ($data as $key => $value) {
			$questionInfo = $this->get($userId,$value['questionId']);
			$arr1['question'] = $questionInfo['question'];
			$arr1['degree']   = $value['degree'];
			$arr[] = $arr1;
		}
		$data = array();
		$data['userId'] = $userId;
		$data['clientId'] = $clientId;
		$data['question'] = json_encode($arr);
		return $this->questionCollectDb->add($data);
	}

	public function getCollect($userId,$collectId){
		$collect = $this->questionCollectDb->getCollect($collectId);
		if($collect){
			if($collect['userId'] != $userId){
				throw new CI_MyException(1,'非法查看');
			}
			$collect['question'] = json_decode($collect['question'],true);
			$clientInfo = $this->clientAo->get($userId,$collect['clientId']);
			$collect['nickName'] = base64_decode($clientInfo['nickName']);
			$collect['headImgUrl'] = $clientInfo['headImgUrl'];
			return $collect;
		}else{
			throw new CI_MyException(1,'无效评论id');
		}
	}

	public function modCollect($collectId,$data){
		$data['question'] = json_encode($data['question']);
		unset($data['nickName']);
		unset($data['headImgUrl']);
		return $this->questionCollectDb->mod($collectId,$data);
	}
}
