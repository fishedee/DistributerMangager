<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserPermissionEnum extends CI_Model{
	public $enums = array(
		array(1,'COMPANY_INTRODUCE','公司介绍'),
        array(2,'COMPANY_SHOP', '普通商城管理'),
        array(3,'COMPANY_DISTRIBUTION', '普通分成管理'),
        array(4,'COMPANY_SHOP_PRO', '高级商城管理'),
        array(5,'COMPANY_DISTRIBUTION_PRO', '高级分成管理'),
        array(6,'COMPANY_CHIPS','众筹管理'),
        array(7,'ORDER_DINNER','订餐系统'),
        array(9,'VENDER','厂家')
	);
	
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};
