<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AddressPaymentEnum extends CI_Model{
	public $enums = array(
		array(0,'UNKNOWN','未知'),
		array(1,'WXPAY','微信支付'),
		array(2,'CODPAY','货到付款'),
	);
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};