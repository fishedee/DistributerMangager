<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserAppAo extends CI_Model{

	public function __construct(){
		parent::__construct();
		$this->load->model('user/userAppDb','userAppDb');
	}

	private function addOnce($userId){
		$userapp = $this->userAppDb->getByUser($userId);
		if( count($userapp) == 0 ){
			$this->userAppDb->add(array('userId'=>$userId));
		}
	}
	public function get($userId){
		$this->addOnce($userId);
		
		$user = $this->userAppDb->getByUser($userId)[0];

		return $user;
	}

	public function mod($userId,$data){
		$this->addOnce($userId);

		return $this->userAppDb->modByUser($userId,$data);
	}
}