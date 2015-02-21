<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WxPay extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user/userAppAo','userAppAo');
    }

    private function initWxSdk($userId){
    	$appInfo = $this->userAppAo->get($userId);

    	$appId = $appInfo['appId'];
		$appKey = $appInfo['appKey'];
		$mchId = $appInfo['mchId'];
		$mchKey = $appInfo['mchKey'];
		$mchSslCert = $appInfo['mchSslCert'];
		$mchSslKey = $appInfo['mchSslKey'];
		
		if( $appId == '' || $appKey == '')
			throw new CI_MyException(1,'未设置appId或appKey');

		if( $mchId == '' || $mchKey == '')
			throw new CI_MyException(1,'未设置mchId或mchKey');

		$this->load->library('wxSdk',array(
			'appId'=>$appId,
			'appKey'=>$appKey,
			'mchId'=>$mchId,
			'mchKey'=>$mchKey,
			'mchSslCert'=>$mchSslCert,
			'mchSslKey'=>$mchSslKey
		),'wxSdk');
    }
	
	public function jsPay($userId,$openId,$dealId,$dealDesc,$dealFee){
		//初始化sdk
		$this->initWxSdk($userId);

		//获取js的pay信息
		return $this->wxSdk->getJsPayInfo(
			$openId,
			$dealId,
			$dealDesc,
			$dealFee,
			'http://'.$_SERVER['HTTP_HOST'].'/pay/wxpaycallback/'.$userId
		);
	}

	public function payCallback($userId){
		//初始化sdk
		$this->initWxSdk($userId);

		//获取支付回调信息
		$payCallBackInfo = $this->wxSdk->getPayCallBackInfo();

		return array_merge($payCallBackInfo,array('userId'=>$userId));
	}
}