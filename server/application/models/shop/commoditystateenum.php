<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityStateEnum extends CI_Model{
	public $enums = array(
		array(1,'ON_STORAGE','已上架'),
        array(2,'DOWN_STORAGE', '已下架'),
	);
	
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};
