<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class VipAo extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->model('vip/vipDb','vipDb');
		$this->load->model('vip/vipClientDb','vipClientDb');
		$this->load->model('user/userAppAo','userAppAo');
		$this->load->model('client/clientAo','clientAo');
	}

	//VIP会员卡相关设定
	private function addSettingOnce($userId){
		$vipSetting = $this->vipDb->getByUserId($userId);
		if( count($vipSetting) == 0 ){
			$this->vipDb->add(array(
				'userId'=>$userId,
				'cardImage'=>''
			));
		}
	}

	public function getSetting($userId){
		$this->addSettingOnce($userId);

		return $this->vipDb->getByUserId($userId)[0];
	}

	public function setSetting($userId,$data){
		$this->addSettingOnce($userId);

		return $this->vipDb->modByUserId($userId,$data);
	}

	//VIP会员卡列表
	public function searchCard($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		return $this->vipClientDb->search($dataWhere,$dataLimit);
	}

	private function addOnceCard($userId,$clientId){
		$vipCard = $this->vipClientDb->getByUserAndClient($userId,$clientId);
		if( count($vipCard) == 0 ){
			$this->vipClientDb->add(array(
				'userId'=>$userId,
				'clientId'=>$clientId,
				'name'=>'',
				'phone'=>'',
				'score'=>0
			));
		}
	}

	public function getCard($userId,$clientId){
		$this->addOnceCard($userId,$clientId);

		$card = $this->vipClientDb->getByUserAndClient($userId,$clientId)[0];

		$card['cardImage'] = $this->getSetting($userId)['cardImage'];

		return $card;
	}

	public function modCard($userId,$clientId,$data){
		// $originData = $this->getCard($userId,$clientId);

		if( strlen($data['name']) == 0 )
			throw new CI_MyException(1,'请输入您的会员卡名字');

		if( preg_match_all('/^\d{11}$/',$data['phone']) == 0 )
            throw new CI_MyException(1,'请输入您的11位数字的会员卡手机号码');

        if( isset($data['score']) && $data['score'] <= 0 )
        	throw new CI_MyException(1,'请输入大于或等于0的积分制');

    	$samePhoneCard = $this->vipClientDb->search(array(
        	'userId'=>$userId,
        	'phone'=>$data['phone']
        ),array());
        foreach( $samePhoneCard['data'] as $singlePhoneCard ){
        	if( $singlePhoneCard['clientId'] != $clientId )
        		throw new CI_MyException(1,'该手机号码已经被注册过了，请勿更换一个手机号码');
        }	
       
        return $this->vipClientDb->modByUserAndClient($userId,$clientId,$data);
	}

	public function modCardInfo($userId,$clientId,$data){
		if( strlen($data['name']) == 0 )
			throw new CI_MyException(1,'请输入您的会员卡名字');

		if( preg_match_all('/^\d{11}$/',$data['phone']) == 0 )
            throw new CI_MyException(1,'请输入您的11位数字的会员卡手机号码');

        if( isset($data['score']) && $data['score'] <= 0 )
        	throw new CI_MyException(1,'请输入大于或等于0的积分制');

    	$samePhoneCard = $this->vipClientDb->search(array(
        	'userId'=>$userId,
        	'phone'=>$data['phone']
        ),array());
        foreach( $samePhoneCard['data'] as $singlePhoneCard ){
        	if( $singlePhoneCard['clientId'] != $clientId )
        		throw new CI_MyException(1,'该手机号码已经被注册过了，请勿更换一个手机号码');
        }	
       
        return $this->vipClientDb->modCardInfo($userId,$clientId,$data);
	}

	//添加会员信息
	public function addMember($UserCardCode,$openid,$ToUserName,$CardId){
		$userId = $this->userAppAo->getUserId($ToUserName);
		//根据openid获取clientId
		$clientId = $this->clientAo->getClientId($openid);
		$this->vipClientDb->addMember($userId,$UserCardCode,$clientId,$CardId);
	}

	//判断有无会员卡
	public function judge($userId,$clientId){
		return $this->vipClientDb->judge($userId,$clientId);
	}

	//判断会员卡是否激活
	public function judgeActive($userId,$clientId){
		return $this->vipClientDb->judgeActive($userId,$clientId);
	}

	//激活会员卡
	public function activeMember($userId,$clientId){
		//获取token
		$info = $this->userAppAo->getTokenAndTicket($userId);
		$access_token = $info['appAccessToken'];
		//获取会员信息
		$memberInfo = $this->vipClientDb->getByUserAndClient($userId,$clientId);
		$memberInfo = $memberInfo[0];
		//开始激活
		$url = 'https://api.weixin.qq.com/card/membercard/activate?access_token='.$access_token;
		$data['init_bonus'] = 100; //初始化积分
		$data['membership_number'] = $memberInfo['userCardCode'];  //会员编号
		$data['code'] = $memberInfo['userCardCode'];
		$data['card_id'] = $memberInfo['card_id'];
		$httpResponse = $this->http->ajax(array(
			'url'=>$url,
			'type'=>'post',
			'data'=>json_encode($data),
			'dataType'=>'plain',
			'responseType'=>'json'
		));
		return $this->vipClientDb->activeMember($userId,$clientId,$data['init_bonus']);
	}

	//判断有无填写手机号码和姓名
	public function judgeMobilName($userId,$clientId){
		return $this->vipClientDb->judgeMobilName($userId,$clientId);
	}

	//获取会员卡信息
	public function getVipInfo($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		$result = $this->vipClientDb->search($dataWhere,$dataLimit);
		$data = $result['data'];
		foreach ($data as $key => $value) {
			$client = $this->clientAo->get($userId,$value['clientId']);
			$data[$key]['openId'] = $client['openId'];
			$data[$key]['nickName'] = $client['nickName'];
			$data[$key]['headImgUrl'] = $client['headImgUrl'];
		}
		$result['data'] = $data;
		return $result;
	}
}