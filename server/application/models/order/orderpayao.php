<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OrderPayAo extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user/userAppAo','userAppAo');
        $this->load->model('client/clientAo','clientAo');
        $this->load->model('order/orderWhen','orderWhen');
    }

    private function initWxSdk($userId){
    	$appInfo = $this->userAppAo->get($userId);

    	$this->userAppAo->check($appInfo);

		$this->load->library('wxSdk',array(
			'appId'=>$appInfo['appId'],
			'appKey'=>$appInfo['appKey'],
			'mchId'=>$appInfo['mchId'],
			'mchKey'=>$appInfo['mchKey'],
			'mchSslCert'=>$appInfo['mchSslCert'],
			'mchSslKey'=>$appInfo['mchSslKey']
		),'wxSdk');
    }

    private function getOpenId($userId,$clientId){
    	return 'oMhf-txr18KIBU1GZ0TXpxToaoH8';

    	$client = $this->clientAo->get($userId,$clientId);
    	return $client['openId'];
    }
	
	public function wxPay($userId,$clientId,$dealId,$dealDesc,$dealFee){
		//初始化sdk
		$this->initWxSdk($userId);

		//获取openId
		$openId = $this->getOpenId($userId,$clientId);

		//获取js的pay信息
		return $this->wxSdk->getOrderPayInfo(
			$openId,
			$dealId,
			$dealDesc,
			$dealFee,
			'http://'.$_SERVER['HTTP_HOST'].'/deal/wxpaycallback/'.$userId
		);
	}

	public function wxJsPay($userId,$prepayId){
		//初始化sdk
		$this->initWxSdk($userId);

		//获取js的pay信息
		return $this->wxSdk->getJsPayInfo($prepayId);
	}

	public function wxPayCallback($userId){
		//初始化sdk
		$this->initWxSdk($userId);

		//获取支付回调信息
		$payCallBackInfo = $this->wxSdk->getPayCallBackInfo();

		//通知订单支付成功了
		$this->orderWhen->whenOrderPay($payCallBackInfo['out_trade_no']);

		return $payCallBackInfo;
	}
}