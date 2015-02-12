<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ClientAo extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->model('client/clientDb','clientDb');
	}

	public function search($where,$limit){
		return $this->clientDb->search($where,$limit);
	}

	public function get($clientId){
		return $this->clientDb->get($clientId);
	}
	
	public function getByIds($clientId){
		return $this->clientDb->getByIds($clientId);
	}
	
	public function mod($clientId,$data){
		return $this->clientDb->mod($clientId,$data);
	}
	
	public function addOnce($data){
		$clients = $this->clientDb->getByTypeAndOpenId(
			$data['type'],
			$data['openId']
		);
		if( count($clients) != 0 )
			return $clients[0]['clientId'];
		
		return $this->clientDb->add($data);
	}

}
