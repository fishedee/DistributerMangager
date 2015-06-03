<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WxSubscribeStateEnum extends CI_Model{
	public $enums = array(
		array(1,'NOTRELEASE','未发布'),
        array(2,'PUBLISHED', '已发布'),
	);
	
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};
