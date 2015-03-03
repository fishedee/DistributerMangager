<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommonErrorEnum extends CI_Model{
	public $enums = array(
		array(0,'UNKNOWN','未知'),
		array(1,'NORMAL','普通错误'),
		//购物车错误
		array(20001,'SHOP_CART_CHECK_ERROR','购物车校验错误'),
	);
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};