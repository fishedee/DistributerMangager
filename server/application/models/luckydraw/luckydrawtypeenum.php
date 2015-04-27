<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LuckyDrawTypeEnum extends CI_Model{
	public $enums = array(
		array(1,'COMMODITY','商品'),
        array(2,'THANKYOU', '谢谢参与'),
	);
	
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};
