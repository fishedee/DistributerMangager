<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ClientLoginAo extends CI_Model {
	
    public function __construct()
    {
        parent::__construct();
		$this->load->model('client/clientAo','clientAo');
    }
	
	public function islogin($userId){
		$clientId = $this->session->userdata('clientId');
		$inUserId = $this->session->userdata('userId');

		if( $clientId >= 10000 && $inUserId == $userId ){
			return $this->clientAo->get($userId,$clientId);
		}else{
			return false;
		}
	}
	
	public function logout(){
		$this->session->unset_userdata('clientId');
		$this->session->unset_userdata('userId');
	}
	
	public function login($userId,$clientId){
		$this->session->set_userdata('userId',$userId);
		$this->session->set_userdata('clientId',$clientId);
	}
	
	public function checkMustLogin($userId){
		$client = $this->islogin($userId);
		if( $client === false ){
			throw new CI_MyException(1,'手机帐号未登录');
		}else{
			//更新用户信息
			$this->clientAo->refreshUserInfo($userId,$client);
		}
		return $client;
	}
}