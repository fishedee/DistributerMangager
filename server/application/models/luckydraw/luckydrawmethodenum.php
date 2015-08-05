<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LuckyDrawMethodEnum extends CI_Model{
	public $enums = array(
		array(1,'TURNTABLE','大转盘'),
        array(2,'SHAKE', '摇一摇'),
	);
	
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};
