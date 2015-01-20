<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LoginAo extends CI_Model {

    public function __construct()
    {
        parent::__construct();
		$this->load->model('user/userDb','userDb');
		$this->load->model('user/userTypeEnum','userTypeEnum');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
    }
	
	public function isLocalRequest(){
		if( $_SERVER['REMOTE_ADDR'] != '127.0.0.1')
			return array(
				'code'=>1,
				'msg'=>'非本地请求',
				'data'=>''
			);
		return array(
			"code"=>0,
			"msg"=>"",
			"data"=>''
		);
	}
	
	public function islogin(){
		$userId = $this->session->userdata('userId');
		if( $userId >= 10000 ){
			$result = $this->userDb->get($userId);
			if( $result["code"] != 0 )
				return $result;
				
			return array(
				"code"=>0,
				"msg"=>'',
				"data"=>$result['data']
			);
		}else{
			return array(
				"code"=>1,
				"msg"=>"帐号未登录",
				"data"=>'',
			);
		}
	}
	
	public function isAdmin(){
		$result = $this->islogin();
		if( $result['code'] != 0 )
			return $result;
		
		if( $result['data']['type'] != $this->userTypeEnum->ADMIN )
			return array(
				'code'=>1,
				'msg'=>'非管理员无法执行此操作',
				'data'=>'',
			);
		
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>$result['data'],
		);
	}
	
	public function isAgent(){
		$result = $this->islogin();
		if( $result['code'] != 0 )
			return $result;
		
		if( $result['data']['type'] != $this->userTypeEnum->AGENT )
			return array(
				'code'=>1,
				'msg'=>'非代理商无法执行此操作',
				'data'=>'',
			);
		
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>$result['data'],
		);
	}
	public function isClient($permission){
		$result = $this->islogin();
		if( $result['code'] != 0 )
			return $result;
		
		if( $result['data']['type'] != $this->userTypeEnum->CLIENT )
			return array(
				'code'=>1,
				'msg'=>'非商城用户无法执行此操作',
				'data'=>'',
			);
		
		if( isset($result['data']['permission'][$permission]) == false )
			return array(
				'code'=>1,
				'msg'=>'没有'.$this->userPermissionEnum->names[$permission].'权限',
				'data'=>'',
			);
		
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>$result['data'],
		);
	}
	
	public function logout(){
		$this->session->unset_userdata('userId');
		return array(
				"code"=>0,
				"msg"=>"",
				"data"=>""
			);
	}
	
	public function login( $name , $password ){
		
		$result = $this->userDb->getByNameAndPass($name,sha1($password));
		if( $result["code"] != 0 )
			return $result;
		$user = $result["data"];
		if( count($user) == 0 )
			return array(
				'code'=>1,
				'msg'=>'帐号或密码错误',
				'data'=>''
			);
		
		$this->session->set_userdata('userId',$user[0]['userId']);
		return array(
				"code"=>0,
				"msg"=>"",
				"data"=>""
			);
	}
}