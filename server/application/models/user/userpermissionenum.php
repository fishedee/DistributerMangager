<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserPermissionEnum extends CI_Model{
	public $enums = array(
		array(1,'COMPANY_INTRODUCE','公司介绍'),
        array(2,'COMMODITY_CLASSIFY', '商品分类'),
        array(3,'USER_ADDRESS', '用户地址管理'),
        array(4,'COMMODITY_MANAGE', '商品管理')
	);
	
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};
