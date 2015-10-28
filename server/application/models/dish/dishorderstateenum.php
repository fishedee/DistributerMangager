<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DishOrderStateEnum extends CI_Model{
	public $enums = array(
        array(0,'NOT','未受理'),
        array(1,'HAS','已受理'),
        array(2,'CLOSE','已关闭'),
        array(3,'CANCLE','已取消'),
        array(4,'CHECK','已结账'),
	);
	
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};
