<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BoardDateAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('board/boardDateDb','boardDateDb');
		$this->load->model('board/bookingRemindDb','bookingRemindDb');
		$this->load->model('user/userAppAo','userAppAo');
		$this->load->library('http');
	}

	public function search($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		return $this->boardDateDb->search($dataWhere,$dataLimit);
	}

	//检测参数
	public function checkData($data){
		if($data['day']){
			if(!is_numeric($data['day'])){
				throw new CI_MyException(1,'星期几请输入数字');
			}
			if($data['day'] <= 0){
				$data['day'] = 1;
			}
			if($data['day'] > 7){
				$data['day'] = 7;
			}
		}
		if(!is_numeric($data['time']) && !strtotime($data['time'])){
			throw new CI_MyException(1,'时间格式非法');
		}
		return $data;
	}

	//增加预定时间
	public function add($userId,$data){
		$data = $this->checkData($data);
		$data['userId'] = $userId;
		return $this->boardDateDb->add($data);
	}

	//获取预定时间信息
	public function getDate($userId,$dateId){
		$info = $this->boardDateDb->getDate($dateId);
		if($info){
			if($info[0]['userId'] != $userId){
				throw new CI_MyException(1,'无效查看');
			}
			return $info[0];
		}else{
			throw new CI_MyException(1,'无效预定信息');
		}
	}

	//修改
	public function mod($userId,$dateId,$data){
		$info = $this->getDate($userId,$dateId);
		$data = $this->checkData($data);
		return $this->boardDateDb->mod($dateId,$data);
	}

	//删除
	public function del($userId,$dateId){
		$info = $this->getdate($userId,$dateId);
		return $this->boardDateDb->del($dateId);
	}

	//前台获取可预定的时间
	public function getOrderTime($userId,$arr){
		$defaultTime = $this->boardDateDb->getOrderTime($userId);
		foreach ($arr as $key => $value) {
			$otherTime = $this->boardDateDb->getOrderTime($userId,$value['week']);
			// if($value['week']==6){
			// 	var_dump($otherTime);die;
			// }
			$newArray = array_merge($defaultTime,$otherTime);
			$len = count($newArray);
			for ($i=1; $i < $len; $i++) { 
				for ($k=0; $k < $len-$i; $k++) { 
					if(strtotime($newArray[$k]['time']) > strtotime($newArray[$k+1]['time'])){
						$tmp = $newArray[$k+1];
						$newArray[$k+1] = $newArray[$k];
						$newArray[$k] = $tmp;
					}
				}
			}
			$arr[$key]['time'] = $newArray;
		}
		return $arr;
	}

	// 检测用户是否需要验证
	public function checkVerify($userId,$clientId){
		$result = $this->bookingRemindDb->checkVerify($userId,$clientId);
		if($result){
			return 1;
		}else{
			return 0;
		}
	}
}
