<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ContactAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('contact/contactDb','contactDb');
	}
	
	
	public function get($userId){
		return $this->contactDb->get($userId);
	}
	
	public function del($userId){
		$this->contactDb->del($userId);
	}
	
	public function add($userId,$data){
		$data['userId'] = $userId;
		$this->contactDb->add($data);
	}
	
	public function mod($userId,$data){
		$this->contactDb->mod($userId,$data);
	}
	

}
