<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CompanyTemplateTypeEnum extends CI_Model{
	public $enums = array(
		array(1,'INTRDOUCE','公司介绍'),
		array(2,'SHOP','公司商城'),
	);
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};