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

		$user['shop'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.$userId.'/item.html';
		$user['logincallback'] = $_SERVER['HTTP_HOST'];
		$user['paycallback'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.$userId;
		return $user;
	}

	public function check($userApp){
		if($userApp['appId'] == '')
			throw new CI_MyException(1,'后台未设置appId，微信商城将不能正常使用');
		if($userApp['appKey'] == '')
			throw new CI_MyException(1,'后台未设置appId，微信商城将不能正常使用');
		if($userApp['mchId'] == '')
			throw new CI_MyException(1,'后台未设置mchId，微信支付将不能正常使用');
		if($userApp['mchKey'] == '')
			throw new CI_MyException(1,'后台未设置mchKey，微信支付将不能正常使用');
	}

	public function checkByUserId($userId){
		$userApp = $this->get($userId);
		$this->check($userApp);
	}

	public function mod($userId,$data){
		$this->addOnce($userId);

		return $this->userAppDb->modByUser($userId,$data);
	}
}