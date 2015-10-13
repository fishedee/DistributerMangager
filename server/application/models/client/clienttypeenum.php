<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ClientTypeEnum extends CI_Model{
	public $enums = array(
		array(0,'UNKNOWN','未知'),
		array(1,'QQ','QQ用户'),
		array(2,'WX','微信用户'),
	);
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};