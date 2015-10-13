<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class RedPackStateEnum extends CI_Model{
	public $enums = array(
		array(1,'CLOSE','暂停开放'),
        array(2,'OPEN', '开放进行中'),
	);
	
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};
