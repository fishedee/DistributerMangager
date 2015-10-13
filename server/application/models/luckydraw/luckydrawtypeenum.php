<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LuckyDrawTypeEnum extends CI_Model{
	public $enums = array(
		array(1,'COMMODITY','商品'),
        array(2,'THANKYOU', '谢谢参与'),
		array(3,'COUPON','代金券'),
		array(4,'CARD','卡券'),
	);
	
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};
