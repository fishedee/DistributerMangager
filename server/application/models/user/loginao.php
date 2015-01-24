<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LoginAo extends CI_Model {

    public function __construct()
    {
        parent::__construct();
		$this->load->model('user/userDb','userDb');
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
		$user = $this->islogin();
		
		if( $user['type'] != $this->userTypeEnum->ADMIN )
			throw new CI_MyException(1,'非管理员无法执行此操作');
		
		return $user;
	}
	
	public function checkMustAgent(){
		$user = $this->islogin();
		
		if( $user['type'] != $this->userTypeEnum->AGENT )
			throw new CI_MyException(1,'非代理商无法执行此操作');
		
		return $user;
	}
	public function checkMustClient($permission){
		$user = $this->islogin();
		
		if( $user['type'] != $this->userTypeEnum->CLIENT )
			throw new CI_MyException(1,'非商城用户无法执行此操作');
		
		if( in_array($permission,$user['permission']) == false )
			throw new CI_MyException(1,'没有'.$this->userPermissionEnum->names[$permission].'权限');
			
		return $user;
	}
	
	public function logout(){
		$this->session->unset_userdata('userId');
	}
	
	public function login( $name , $password ){
		$user = $this->userDb->getByNameAndPass($name,sha1($password));
		if( count($user) == 0 )
			throw new CI_MyException(1,'帐号或密码错误');
		
		$this->session->set_userdata('userId',$user[0]['userId']);
	}
}