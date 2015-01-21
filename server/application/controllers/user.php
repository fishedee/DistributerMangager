<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/userAo','userAo');
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('user/userTypeEnum','userTypeEnum');
		$this->load->library('argv','argv');
    }
	
	public function getAllType()
	{
		$this->load->view('json',array(
			'code'=>0,
			'msg'=>'',
			'data'=>$this->userTypeEnum->names
		));
	}
	
	public function getAllPermission()
	{ 
		$this->load->view('json',array(
			'code'=>0,
			'msg'=>'',
			'data'=>$this->userPermissionEnum->names
		));
	}
	
	public function search()
	{
		//检查输入参数		
		$result = $this->argv->checkGet(array(
			array('name','option'),
			array('type','option'),
			array('company','option'),
			array('phone','option'),
		));
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return;
		}
		$dataWhere = $result["data"];
		
		$result = $this->argv->checkGet(array(
			array('pageIndex','require'),
			array('pageSize','require'),
		));
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return;
		}
		$dataLimit = $result["data"];
		
		//检查权限
		$result = $this->loginAo->islogin();
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		
		//执行业务逻辑
		$result = $this->userAo->search($dataWhere,$dataLimit);
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return;
		}
		$this->load->view('json',$result);
	}
	
	public function get()
	{
		//检查输入参数
		$result = $this->argv->checkGet(array(
			array('userId','require'),
		));
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return;
		}
		$userId = $result["data"]['userId'];
		
		//检查权限
		$result = $this->loginAo->islogin();
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		
		//执行业务逻辑
		$data = $this->userAo->get(
			$userId
		);
		$this->load->view('json',$data);
	}
	
	public function add()
	{
		//检查输入参数
		$result = $this->argv->checkPost(array(
			array('name','require'),
			array('password','require'),
			array('type','option',$this->userTypeEnum->CLIENT),
			array('phone','require'),
			array('company','require'),
			array('permission','option',array()),
			array('client','option',array()),
		));
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return;
		}
		$data = $result['data'];
		
		//检查权限
		$result = $this->loginAo->islogin();
		if( $result["code"] == 0 ){
			//登录用户
			$loginUserInfo = $result['data'];
			if( $loginUserInfo['type'] == $this->userTypeEnum->ADMIN ){
				//管理员可以为所欲为
			}else{
				//非管理员只能添加商城用户
				$data['type'] = $this->userTypeEnum->CLIENT;
				unset($data['permission']);
				unset($data['client']);
			}
		}else{
			//非登录用户只能添加商城用户
			$data['type'] = $this->userTypeEnum->CLIENT;
			unset($data['permission']);
			unset($data['client']);
		}
		
		//执行业务逻辑
		$result = $this->userAo->add(
			$data
		);
		$this->load->view('json',$result);
	}
	
	public function del()
	{
		//检查输入参数
		$result = $this->argv->checkPost(array(
			array('userId','require'),
		));
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return;
		}
		$userId = $result["data"]['userId'];
		
		//检查权限
		$result = $this->loginAo->isAdmin();
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		
		//执行业务逻辑
		$result = $this->userAo->del(
			$userId
		);
		$this->load->view('json',$result);
	}
	
	public function mod()
	{
		//检查输入参数
		$result = $this->argv->checkPost(array(
			array('userId','option',0),
		));
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return;
		}
		$userId = $result["data"]['userId'];
		
		$result = $this->argv->checkPost(array(
			array('type','require'),
			array('phone','require'),
			array('company','require'),
			array('permission','option',array()),
			array('client','option',array()),
		));
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return;
		}
		$data = $result["data"];
		
		//检查权限
		$result = $this->loginAo->islogin();
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		$loginUserInfo = $result['data'];
		
		if( $loginUserInfo['type'] == $this->userTypeEnum->ADMIN ){
			//管理员可以为所欲为
		}else{
			//非管理员不能设置类型，权限和手下，并且只能设置自己的信息
			unset($data['type']);
			unset($data['permission']);
			unset($data['client']);
			$userId = $loginUserInfo['userId'];
		}
		
		//执行业务逻辑
		$result = $this->userAo->mod(
			$userId,
			$data
		);
		$this->load->view('json',$result);
	}
	
	public function modPassword()
	{
		//检查输入参数
		$result = $this->argv->checkPost(array(
			array('userId','require'),
			array('password','require'),
		));
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		$userId = $result['data']['userId'];
		$password = $result['data']['password'];
		
		//检查权限
		$result = $this->loginAo->isAdmin();
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		
		//执行业务逻辑
		$result = $this->userAo->modPassword(
			$userId,
			$password
		);
		$this->load->view('json',$result);
	}
	
	public function modMyPassword()
	{
		//检查输入参数
		$result = $this->argv->checkPost(array(
			array('userId','require'),
			array('oldPassword','require'),
			array('newPassword','require')
		));
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		$userId = $result['data']['userId'];
		$oldPassword = $result['data']['oldPassword'];
		$newPassword = $result['data']['newPassword'];
		
		//检查权限
		$result = $this->loginAo->islogin();
		if( $result["code"] != 0 ){
			$this->load->view('json',$result);
			return $result;
		}
		$userId = $result['data']['userId'];
		
		//执行业务逻辑
		$result = $this->userAo->modPasswordByOld(
			$userId,
			$oldPassword,
			$newPassword
		);
		$this->load->view('json',$result);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
