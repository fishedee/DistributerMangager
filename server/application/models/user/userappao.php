<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserAppAo extends CI_Model{

	public function __construct(){
		parent::__construct();
		$this->load->model('user/userAppDb','userAppDb');
	}

	private function addOnce($userId){
		$userapp = $this->userAppDb->getByUser($userId);
		if( count($userapp) == 0 ){
			$this->userAppDb->add(array('userId'=>$userId));
		}
	}
	public function get($userId,$originSsl=false){
		$this->addOnce($userId);
		
		$user = $this->userAppDb->getByUser($userId)[0];

		$user['shop'] = 'http://'.$userId.'.'.$_SERVER['HTTP_HOST'].'/'.$userId.'/item.html';
		$user['logincallback'] = $userId.'.'.$_SERVER['HTTP_HOST'];
		$user['paycallback'] = 'http://'.$userId.'.'.$_SERVER['HTTP_HOST'].'/'.$userId.'/';
		$user['Token']='weiyd';
		$user['serverurl'] = 'http://'.$_SERVER['HTTP_HOST'].'/weixin/index';
		if( $originSsl == false ){
			$user['mchSslCert'] = dirname(__FILE__).'/../../../../'.$user['mchSslCert'];
			$user['mchSslKey'] = dirname(__FILE__).'/../../../../'.$user['mchSslKey'];
		}
		return $user;
	}

	public function getTokenAndTicket($userId){
		//获取userApp
		$userApp = $this->get($userId);
		$this->check($userApp);

		//初始化wxSdk
		$this->load->library('wxSdk',array(
			'appId'=>$userApp['appId'],
			'appKey'=>$userApp['appKey'],
			'appToken'=>''
		),'wxSdk3');

		//刷新accessToken
		if( trim($userApp['appAccessToken']) == '' 
			|| strtotime($userApp['appAccessTokenExpire']) - time() < 60 ){
			$accessToken = $this->wxSdk3->getAccessToken();

			$this->userAppDb->modByUser($userId,array(
				'appAccessToken'=>$accessToken['access_token'],
				'appAccessTokenExpire'=>date('Y-m-d H:i:s',$accessToken['expires_in'] + time())
			));

			$userApp['appAccessToken'] = $accessToken['access_token'];
		}

		//刷新jsApiTicket
		if( trim($userApp['appJsApiTicket']) == '' 
			|| strtotime($userApp['appJsApiTicketExpire']) - time() < 60 ){
			$jsApiTicket = $this->wxSdk3->getJsApiTicket($userApp['appAccessToken']);

			$this->userAppDb->modByUser($userId,array(
				'appJsApiTicket'=>$jsApiTicket['ticket'],
				'appJsApiTicketExpire'=>date('Y-m-d H:i:s',$jsApiTicket['expires_in'] + time())
			));

			$userApp['appJsApiTicket'] = $jsApiTicket['ticket'];
		}

		return $userApp;
	}

	public function getJsConfig($userId,$url){
		$tokenAndTicket = $this->getTokenAndTicket($userId);

		return $this->wxSdk3->getJsConfig($tokenAndTicket['appJsApiTicket'],$url);
	}

	public function check($userApp){
		if($userApp['appId'] == '')
			throw new CI_MyException(1,'后台未设置appId，微信商城将不能正常使用');
		if($userApp['appKey'] == '')
			throw new CI_MyException(1,'后台未设置appKey，微信商城将不能正常使用');
		if($userApp['mchId'] == '')
			throw new CI_MyException(1,'后台未设置mchId，微信支付将不能正常使用');
		if($userApp['mchKey'] == '')
			throw new CI_MyException(1,'后台未设置mchKey，微信支付将不能正常使用');
	}

	public function checkAll($userApp){
		$this->check($userApp);
		if($userApp['mchSslCert'] == '')
			throw new CI_MyException(1,'后台未设置mchSslCert，微信红包将不能正常使用');
		if($userApp['mchSslKey'] == '')
			throw new CI_MyException(1,'后台未设置mchSslKey，微信红包将不能正常使用');
	}
	
	public function checkWeixinNum($userApp){
		if($userApp['appId'] == '')
			throw new CI_MyException(1,'后台未设置appId，自定义菜单等功能将不能正常使用');
		if($userApp['appKey'] == '')
			throw new CI_MyException(1,'后台未设置appKey，自定义菜单等功能不能正常使用');
	}
	
	public function checkAppIdAppKey($userApp){
		if($userApp['weixinNum'] == '')
			throw new CI_MyException(1,'后台未设置微信原始ID，该微信功能将不能正常使用');
	}

	public function checkByUserId($userId){
		$userApp = $this->get($userId);
		$this->check($userApp);
	}

	public function mod($userId,$data){
		$this->addOnce($userId);

		if(isset($data['appId']) && trim($data['appId']) != ''){
			$user = $this->userAppDb->search(array(
				'appId'=>$data['appId']
			),array());
			if( $user['count'] != 0 && $user['data'][0]['userId'] != $userId)
				throw new CI_MyException(1,'该公众号的appId已经被其它商城占用了，请重新设置其它的appId');
		}

		if(isset($data['mchId']) && trim($data['mchId']) != ''){
			$user = $this->userAppDb->search(array(
				'mchId'=>$data['mchId']
			),array());
			if( $user['count'] != 0 && $user['data'][0]['userId'] != $userId)
				throw new CI_MyException(1,'该公众号的mchId已经被其它商城占用了，请重新设置其它的mchId');
		}
		
		if(isset($data['weixinNum']) && trim($data['weixinNum']) != ''){
			$user = $this->userAppDb->search(array(
				'weixinNum'=>$data['weixinNum']
			),array());
			if( $user['count'] != 0 && $user['data'][0]['userId'] != $userId)
				throw new CI_MyException(1,'该微信号已经被其它商城占用了，请重新设置其它的mchId');
		}

		return $this->userAppDb->modByUser($userId,$data);
	}
}