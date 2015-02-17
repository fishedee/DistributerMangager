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

    private function getUserIdFromUrl($url){
    	preg_match('/^http:\/\/[^\/]*\/(\d+)/i',$url,$matches);
    	if( isset($matches[1]) == false )
    		throw new CI_MyException(1,'不合法的跳转url'.$url);

    	return $matches[1];
    }

    private function initWxSdk($userId){
    	$appInfo = $this->userAppAo->get($userId);

    	$wxAppId = $appInfo['appId'];
		$wxAppKey = $appInfo['appKey'];
		$wxScope = 'snsapi_base';
		$wxCallback = 'http://'.$_SERVER['HTTP_HOST'].'/clientlogin/wxlogincallback';
		
		if( $wxAppId == '' || $wxAppKey == '')
			throw new CI_MyException(1,'未设置appId或appKey');
		$this->load->library('wxSdk',array(
			'appId'=>$wxAppId,
			'appKey'=>$wxAppKey,
			'callback'=>$wxCallback,
			'scope'=>$wxScope
		),'wxSdk');
    }
	
	public function login($callback){
		//初始化sdk
		$userId = $this->getUserIdFromUrl($callback);

		$this->initWxSdk($userId);

		//获取跳转url
		$loginUrl = $this->wxSdk->getLoginUrl($callback);
		
		header('Location: '.$loginUrl);
	}
	
	public function loginCallback(){
		//初始化sdk
		$userId = $this->getUserIdFromUrl($_GET['state']);

		$this->initWxSdk($userId);

		//调用QQ接口获取登录信息
		$accessToken = $this->wxSdk->getAccessTokenAndOpenId();
		
		$callback = $this->wxSdk->getLoginInfo();
		
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