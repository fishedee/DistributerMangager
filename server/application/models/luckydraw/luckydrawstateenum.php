<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LuckyDrawStateEnum extends CI_Model{
	public $enums = array(
		array(1,'OFF_STORAGE','未上架'),
        array(2,'ON_STORAGE', '已上架'),
	);
	
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};
