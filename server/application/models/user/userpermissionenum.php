<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserPermissionEnum extends CI_Model{
	public $enums = array(
		array(1,'COMPANY_ARTICLE','文章管理'),
		array(2,'COMPANY_BANNER','广告管理'),
		array(3,'COMPANY_TEMPLATE','模板管理')
	);
	
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};