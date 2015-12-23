<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ShopScoreAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('client/shopScoreDb','shopScoreDb');
		$this->load->model('client/clientAo','clientAo');
		$this->load->model('distribution/distributionConfigAo','distributionConfigAo');
		$this->load->model('user/userAo','userAo');
		$this->load->model('distribution/distributionAo','distributionAo');
	}
	
	public function search($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		$data = $this->shopScoreDb->search($dataWhere,$dataLimit);
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['price'] = sprintf('%.2f',$value['price']/100);
			$clientInfo = $this->clientAo->get($userId,$value['clientId']);
			$data['data'][$key]['nickName'] = base64_decode($clientInfo['nickName']);
			$data['data'][$key]['headImgUrl'] = $clientInfo['headImgUrl'];
		}
		return $data;
	}

	//检测参数正确性
	private function checkData($userId,$data){
		if(isset($data['clientId'])){
			$clientInfo = $this->clientAo->get($userId,$data['clientId']);
		}
		if(isset($data['price'])){
			if(!is_numeric($data['price'])){
				throw new CI_MyException(1,'价格必须为数字');
			}
			if($data['price'] <= 0){
				throw new CI_MyException(1,'价格必须大于0');
			}
		}
		$arr['clientId'] = $data['clientId'];
		$arr['price']    = $data['price'] * 100;
		$arr['client']   = $clientInfo;
		return $arr;
	}

	//计算兑换的积分
	private function calScore($userId,$price){
		//查找分销配置
        $config = $this->distributionConfigAo->getConfig($userId);
        $unit   = 0;
        if($config == 0){
        	$unit = 1;
        }else{
        	$unit = $config['shop'];
        }
        return floor($unit*$price);
	}

	public function getInfo($userId,$data){
		$price= $data['price'];
		$data = $this->checkData($userId,$data);
		$score= $this->calScore($userId,$price);
		return array(
			'clientId'=>$data['clientId'],
			'nickName'=>base64_decode($data['client']['nickName']),
			'score'   =>$score,
			'price'   =>sprintf('%.2f',$price),
			'client'  =>$data['client'],
			);
	}

	public function add($userId,$data){
		$price = $data['price'];
		$data  = $this->getInfo($userId,$data);
		//增加商城积分记录的信息
		$arr['price'] = $price * 100;
		$arr['userId']= $userId;
		$arr['clientId'] = $data['clientId'];
		$arr['score']  = $data['score'];
		$arr['remark'] = "消费".$price."元，获取".$data['score']."分";
		$result = $this->shopScoreDb->add($arr);
		if(!$result){
			throw new CI_MyException(1,'插入商城积分记录失败');
		}
		//更新用户信息
		$client['shopScore'] = $data['client']['shopScore'] + $data['score'];
		$result = $this->clientAo->mod($userId,$data['clientId'],$client);
		if(!$result){
			throw new CI_MyException(1,'更新用户商城积分失败');
		}
		//查找上级
		// $userInfo = $this->userAo->getUserInfo($data['clientId']);
		$userInfo = $this->userAo->getUserInfoNotThrow($data['clientId']);
		if($userInfo == 0){
			return 1;
		}else{
			$distribution = $this->distributionAo->getDistributionUser($userId,$userInfo['userId']);
			if($distribution['scort'] > 1){
				$upUserId = $distribution['upUserId'];
				$upUserInfo = $this->userAo->get($upUserId);
				$upClinetId = $upUserInfo['clientId'];
				$arr['clientId'] = $upClinetId;
				$arr['remark']   = "下级消费".$price."元，获取".($data['score']/2)."分";
				$arr['score']    = $data['score']/2;
				$result = $this->shopScoreDb->add($arr);
				if(!$result){
					throw new CI_MyException(1,'插入商城积分记录失败');
				}
				//更新上级信息
				$upClinetInfo = $this->clientAo->get($userId,$upClinetId);
				$client['shopScore'] = $upClinetInfo['shopScore'] + ($data['score']/2);
				return $this->clientAo->mod($userId,$upClinetId,$client);
			}else{
				return 1;
			}
		}
	}
}
