<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ExpressageEnum extends CI_Model{
	public $enums = array(
		array(1,'ems','EMS'),
        array(2,'ems', '中国邮政'),
        array(3,'shentong', '申通快递'),
        array(4,'yuantong', '圆通速递'),
        array(5,'shunfeng', '顺丰速运'),
        array(6,'tiantian', '天天快递'),
        array(7,'yunda', '韵达快递'),
        array(8,'zhongtong', '中通速递'),
        array(9,'longbanwuliu', '龙邦物流'),
        array(10,'zhaijisong', '宅急送'),
        array(11,'quanyikuaidi', '全一快递'),
        array(12,'huitongkuaidi', '汇通速递'),
	);
	
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};
