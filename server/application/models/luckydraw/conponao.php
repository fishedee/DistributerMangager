<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ConponAo extends CI_Model 
{
	public function __construct(){
		parent::__construct();
		$this->load->model('user/userAppAo','userAppAo');
	}
	
	//查询代金券批次信息
	public function queryCouponStock($userId,$Conpons){
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
		
		$ConponMsg=$this->wxSdk0->getConponInfo($Conpons['coupon_id']);
		
		if ($ConponMsg['return_code'] == 'SUCCESS'){
			if ($Conpons['quantity'] > $ConponMsg['coupon_total'])
				throw new CI_MyException(1,'代金券批次ID'.$Conpons['coupon_id'].'的设置数量不能大于微信支付设置代金券的数量');
		}
		
	}
	
	
	
}