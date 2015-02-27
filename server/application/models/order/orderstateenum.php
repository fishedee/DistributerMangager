<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OrderStateEnum extends CI_Model{
	public $enums = array(
		array(1,'NO_PAY','未支付'),
        array(2,'NO_SEND', '未发货'),
        array(3,'HAS_SEND', '已发货'),
        array(4, 'FINISH', '交易完成'),
	);
	
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};
