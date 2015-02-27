<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ClientAo extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->model('client/clientDb','clientDb');
	}

	public function search($userId,$where,$limit){
		$where['userId'] = $userId;
		return $this->clientDb->search($where,$limit);
	}

	public function get($userId,$clientId){
		$client =  $this->clientDb->get($clientId);
		if( $client['userId'] != $userId )
			throw new CI_MyException(1,'非本商城用户无权限操作');

		return $client;
	}
	
	public function mod($userId,$clientId,$data){
		$client =  $this->clientDb->get($clientId);
		if( $client['userId'] != $userId )
			throw new CI_MyException(1,'非本商城用户无权限操作');

		$this->clientDb->mod($clientId,$data);
	}
	
	public function addOnce($data){
		$clients = $this->clientDb->search(array(
			'userId'=>$data['userId'],
			'type'=>$data['type'],
			'openId'=>$data['openId']
		),array());
		if( $clients['count'] != 0 )
			return $clients['data'][0]['clientId'];
		
		return $this->clientDb->add($data);
	}

}
