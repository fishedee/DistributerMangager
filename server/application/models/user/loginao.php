<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LoginAo extends CI_Model {

    public function __construct()
    {
        parent::__construct();
		$this->load->model('user/userAo','userAo');
		$this->load->model('user/userTypeEnum','userTypeEnum');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
    }
	
	public function checkMustLocalRequest(){
		if( $_SERVER['REMOTE_ADDR'] != '127.0.0.1')
			throw new CI_MyException(1,'非本地请求');
	}
	
	public function islogin(){
		$userId = $this->session->userdata('userId');
		if( $userId >= 10000 ){
			return $this->userAo->get($userId);
		}else{
			return false;
		}
	}
	
	public function checkMustLogin(){
		$user = $this->islogin();
		if( $user === false )
			throw new CI_MyException(1,'未登录');
		
		return $user;
	}
	
	public function checkMustAdmin(){
		$user = $this->checkMustLogin();
		
		if( $user['type'] != $this->userTypeEnum->ADMIN )
			throw new CI_MyException(1,'非管理员无法执行此操作');
		
		return $user;
	}
	
	public function checkMustAgent(){
		$user = $this->checkMustLogin();
		
		if( $user['type'] != $this->userTypeEnum->AGENT )
			throw new CI_MyException(1,'非代理商无法执行此操作');
		
		return $user;
	}
	public function checkMustClient($permission){
		$user = $this->checkMustLogin();
		
		if( $user['type'] != $this->userTypeEnum->CLIENT )
			throw new CI_MyException(1,'非商城用户无法执行此操作');

		if( $permission == $this->userPermissionEnum->COMPANY_SHOP ){
			//校验商城管理权限
			if(in_array($this->userPermissionEnum->COMPANY_SHOP,$user['permission']) == false &&
			in_array($this->userPermissionEnum->COMPANY_SHOP_PRO,$user['permission']) == false)
				throw new CI_MyException(1,'需要普通商城管理或高级商城管理的权限');
		}else if( $permission == $this->userPermissionEnum->COMPANY_DISTRIBUTION ){
			//校验分成管理权限
			if(in_array($this->userPermissionEnum->COMPANY_DISTRIBUTION,$user['permission']) == false &&
			in_array($this->userPermissionEnum->COMPANY_DISTRIBUTION_PRO,$user['permission']) == false)
				throw new CI_MyException(1,'需要普通分成管理或高级分成管理的权限');
		}else{
			if( in_array($permission,$user['permission']) == false )
				throw new CI_MyException(1,'没有'.$this->userPermissionEnum->names[$permission].'权限');
		} 
			
		return $user;
	}

	public function hasCompanyShopPro($user){
		return in_array($this->userPermissionEnum->COMPANY_SHOP_PRO,$user['permission']);
	}

	public function hasCompanyDistributionPro($user){
		return in_array($this->userPermissionEnum->COMPANY_DISTRIBUTION_PRO,$user['permission']);
	}
	
	public function logout(){
		$this->session->unset_userdata('userId');
	}
	
	public function login( $name , $password ){
		$user = $this->userAo->getByName($name);
		if( count($user) == 0 )
			throw new CI_MyException(1,'帐号或密码错误');
		$this->userAo->checkMustVaildPassword($password,$user[0]['password']);
		
		$this->session->set_userdata('userId',$user[0]['userId']);
	}
}