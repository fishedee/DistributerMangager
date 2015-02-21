<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ClientWxLoginAo extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user/userAppAo','userAppAo');
		$this->load->model('client/clientAo','clientAo');
		$this->load->model('client/clientLoginAo','clientLoginAo');
		$this->load->model('client/clientTypeEnum','clientTypeEnum');
    }

    private function initWxSdk($userId){
    	$appInfo = $this->userAppAo->get($userId);

    	$wxAppId = $appInfo['appId'];
		$wxAppKey = $appInfo['appKey'];
		
		if( $wxAppId == '' || $wxAppKey == '')
			throw new CI_MyException(1,'未设置appId或appKey');
		$this->load->library('wxSdk',array(
			'appId'=>$wxAppId,
			'appKey'=>$wxAppKey
		),'wxSdk');
    }
	
	public function login($userId,$callback){
		//初始化sdk
		$this->initWxSdk($userId);

		//获取跳转url
		$loginUrl = $this->wxSdk->getLoginUrl(
			'http://'.$_SERVER['HTTP_HOST'].'/clientlogin/wxlogincallback?userId='.$userId,
			$callback,
			'snsapi_base'
		);
		
		header('Location: '.$loginUrl);
	}
	
	public function loginCallback(){
		//初始化sdk
		$userId = $_GET['userId'];

		$this->initWxSdk($userId);

		//调用QQ接口获取登录信息
		$callback = $this->wxSdk->getLoginCallBackInfo();

		$accessToken = $this->wxSdk->getAccessTokenAndOpenId();
		
		//第一次登录更新用户信息
		$clientId = $this->clientAo->addOnce(array(
			'userId'=>$userId,
			'openId'=>$accessToken['openid'],
			'type'=>$this->clientTypeEnum->WX,
		));
		
		//设置登录态
		$this->clientLoginAo->login($userId,$clientId);
		
		header('Location: '.$callback);
	}
}