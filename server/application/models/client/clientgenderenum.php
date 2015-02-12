<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ClientGenderEnum extends CI_Model{
	public $enums = array(
		array(0,'UNKNOWN','未知'),
		array(1,'BOY','男性'),
		array(2,'GIRL','女性'),
	);
	public function __construct(){
		parent::__construct();
		$this->load->library('enum','','enum');
		$this->enum->setEnum($this,$this->enums);
	}
};