<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserPermissionEnum extends CI_Model{
	public $enums = array(
		array(1,'COMPANY_INTRODUCE','公司介绍'),
        array(2,'COMPANY_SHOP', '商城管理'),
        array(3,'COMPANY_DISTRIBUTION', '分成管理')
	);
	
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};
