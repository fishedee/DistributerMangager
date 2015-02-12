<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ClientLoginAo extends CI_Model {
	
    public function __construct()
    {
        parent::__construct();
		$this->load->model('client/clientAo','clientAo');
    }
	
	public function islogin(){
		$clientId = $this->session->userdata('clientId');
		if( $clientId >= 10000 ){
			return $this->clientAo->get($clientId);
		}else{
			return false;
		}
	}
	
	public function logout(){
		$this->session->unset_userdata('clientId');
	}
	
	public function login($clientId){
		$this->session->set_userdata('clientId',$clientId);
	}
	
	public function checkMustLogin(){
		$client = $this->islogin();
		if( $client === false )
			throw new CI_MyException(1,'帐号未登录');
		return $client;
	}
}