<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class VipAo extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->model('vip/vipDb','vipDb');
		$this->load->model('vip/vipClientDb','vipClientDb');
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

		return $this->vipClientDb->getByUserAndClient($userId,$clientId);
	}

	public function modCard($userId,$clientId,$data){
		$this->addOnceCard($userId,$clientId);

		if( isset($data['name']) && strlen($data['name']) == 0 )
			throw new CI_MyException(1,'请输入您的会员卡名字');

		if( isset($data['phone']) && preg_match_all('/^\d{11}$/',$data['phone'] == 0 ))
            throw new CI_MyException(1,'请输入您的11位数字的会员卡手机号码');

        if( isset($data['score']) && $data['score'] <= 0 )
        	throw new CI_MyException(1,'请输入大于或等于0的积分制');

        return $this->vipClientDb->modByUserAndClient($userId,$clientId,$data);
	}
}