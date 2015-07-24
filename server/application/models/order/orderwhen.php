<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OrderWhen extends CI_Model 
{

	public function __construct(){
		parent::__construct();
		$this->load->model('order/orderDb','orderDb');
		$this->load->model('order/orderStateEnum','orderStateEnum');
	}

	public function whenOrderPay($shopOrderId){
		//计算出订单基本信息
		$shopOrder = $this->orderDb->get($shopOrderId);
		if($shopOrder['state'] != $this->orderStateEnum->NO_PAY)
			return;

		$this->orderDb->mod(
			$shopOrderId,
			array('state'=>$this->orderStateEnum->NO_SEND)
		);
		
		$this->load->model('shop/commodityAo','commodityAo');
	 	$priceShow = $this->commodityAo->getFixedPrice($shopOrder['price']);
		
		//发送消息模板,模板标题：订单支付成功，TM00015
//		$sendData['touser']='oMhf-twCKWCdxt_He9BT50_6N3dg';
// 		$sendData['template_id']=$template_id;
		$sendData['url']='http://'.$_SERVER['HTTP_HOST'].'/'.$shopOrder['userId'].'/deal.html';
		$sendData['topcolor']='#FF0000';
		$sendData['data']['first']['value']='我们已收到您的货款，开始为您打包商品，请耐心等待 :)';
		$sendData['data']['first']['color']='#173177';
		$sendData['data']['orderMoneySum']['value']=$priceShow;
		$sendData['data']['orderMoneySum']['color']='#173177';
		$sendData['data']['orderProductName']['value']=$shopOrder['description'];
		$sendData['data']['orderProductName']['color']='#173177';
		$sendData['data']['remark']['value']='欢迎再次购买！';
		$sendData['data']['remark']['color']='#173177';
		
		$this->load->model('weixin/wxTemplateAo','wxTemplateAo');
		$this->wxTemplateAo->notice($shopOrder['userId'],$shopOrder['clientId'],$sendData,'TM00015');
	}
}
