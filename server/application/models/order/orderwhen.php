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
		$this->orderDb->mod(
			$shopOrderId,
			$this->orderStateEnum->NO_SEND
		);
	}
}
