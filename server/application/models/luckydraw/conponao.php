<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ConponAo extends CI_Model 
{
	public function __construct(){
		parent::__construct();
		$this->load->model('user/userAppAo','userAppAo');
	}
	
	//查询代金券批次信息
	public function queryCouponStock($userId,$conpons){
		//初始化userApp
		$appInfo = $this->userAppAo->get($userId);

    	$this->userAppAo->checkAll($appInfo);

		$this->load->library('wxSdk',array(
			'appId'=>$appInfo['appId'],
			'appKey'=>$appInfo['appKey'],
			'mchId'=>$appInfo['mchId'],
			'mchKey'=>$appInfo['mchKey'],
			'mchSslCert'=>$appInfo['mchSslCert'],
			'mchSslKey'=>$appInfo['mchSslKey']
		),'wxSdk0');
		
		$ConponMsg=$this->wxSdk0->getCoupon($conpons['coupon_id']);
		
		if ($ConponMsg['return_code'] == 'SUCCESS'){
			if ($conpons['quantity'] > $ConponMsg['coupon_total'])
				throw new CI_MyException(1,'代金券批次ID'.$conpons['coupon_id'].'的设置数量不能大于微信支付设置代金券的数量');
		}
		
	}
	
	//发放代金券
	public function sendToCoupon($userId,$clientId,$luckyDrawClientId,$coupon_id){
		//初始化userApp
		$appInfo = $this->userAppAo->get($userId);
		
		$this->userAppAo->checkAll($appInfo);
		$this->load->model('client/clientDb','clientDb');
		$openid=$this->clientDb->search(array('clientId'=>$clientId),'')['data']['0']['openId'];

		$this->load->library('wxSdk',array(
				'appId'=>$appInfo['appId'],
				'appKey'=>$appInfo['appKey'],
				'mchId'=>$appInfo['mchId'],
				'mchKey'=>$appInfo['mchKey'],
				'mchSslCert'=>$appInfo['mchSslCert'],
				'mchSslKey'=>$appInfo['mchSslKey']
		),'wxSdk0');
		$couponData=array(
			'coupon_stock_id'=>$coupon_id,
			'openid'=>$openid,
		);
		
		//发送优惠券给用户
		$this->wxSdk0->sendCoupon($couponData);
		
	}

	
}