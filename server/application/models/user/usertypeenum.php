<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserTypeEnum extends CI_Model{
	public $enums = array(
		array(1,'ADMIN','管理员'),
		array(2,'AGENT','代理商'),
		array(3,'CLIENT','商城用户'),
		array(4,'ENTITY','实体店铺'),
	);
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};