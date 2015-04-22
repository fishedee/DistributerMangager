<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userAo','userAo');
		$this->load->model('user/userAppAo','userAppAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('user/userTypeEnum','userTypeEnum');
		$this->load->library('argv','argv');
    }
	
	/**
	* @view json
	*/
	public function getAllType()
	{
		return $this->userTypeEnum->names;
	}

	/**
	* @view json
	*/
	public function getAppInfo()
	{
		//检查权限
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_SHOP
		);
		$userId = $user['userId'];

		//业务逻辑
		return $this->userAppAo->get($userId);
	}

	/**
	* @view json
	*/
	public function getHasCompany(){
		//检查参数
		$data = $this->argv->checkGet(array(
			array('userId','require'),
		));

		//检查是否有公司介绍权限
		$user = $this->userAo->get($data['userId']);
		return in_array(
			$this->userPermissionEnum->COMPANY_INTRODUCE,
			$user['permission']
		);
	}

	/**
	* @view json
	*/
	public function modAppInfo()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('appName','require'),
			array('appId','require'),
			array('appKey','require'),
			array('mchId','require'),
			array('mchKey','require'),
			array('mchSslCert','option'),
			array('mchSslKey','option'),
			array('remark','require'),
		));

		//检查权限
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_SHOP
		);
		$userId = $user['userId'];

		//业务逻辑
		return $this->userAppAo->mod($userId,$data);
	}
	
	
	/**
	* @view json
	*/
	public function getAllPermission()
	{ 
		return $this->userPermissionEnum->names;
	}
	
	/**
	* @view json
	*/
	public function search()
	{
		//检查输入参数		
		$dataWhere = $this->argv->checkGet(array(
			array('name','option'),
			array('type','option'),
			array('company','option'),
			array('phone','option'),
			array('permissionId','option'),
		));
		
		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','require'),
			array('pageSize','require'),
		));
		
		//检查权限
		$this->loginAo->checkMustLogin();
		
		//执行业务逻辑
		return $this->userAo->search($dataWhere,$dataLimit);
	}
	
	/**
	* @view json
	*/
	public function get()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userId','require'),
		));
		$userId = $data['userId'];
		
		//检查权限
		$this->loginAo->checkMustLogin();
		
		//执行业务逻辑
		return $this->userAo->get($userId);
	}
	
	/**
	* @view json
	*/
	public function add()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('name','require'),
			array('password','option','123456'),
			array('type','option',$this->userTypeEnum->CLIENT),
			array('phone','require'),
			array('company','require'),
			array('downDistributionNum','option',0),
			array('permission','option',array()),
			array('client','option',array()),
		));
		
		//检查权限
		$loginUser = $this->loginAo->islogin();
		if( $loginUser !== false ){
			//登录用户
			if( $loginUser['type'] == $this->userTypeEnum->ADMIN ){
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
		$this->userAo->add($data);
	}
	
	/**
	* @view json
	*/
	public function del()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userId','require'),
		));
		$userId = $data['userId'];
		
		//检查权限
		$this->loginAo->checkMustAdmin();
		
		//执行业务逻辑
		$this->userAo->del($userId);
	}
	
	/**
	* @view json
	*/
	public function mod()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userId','require'),
		));
		$userId = $data['userId'];
		
		$data = $this->argv->checkPost(array(
			array('type','option'),
			array('phone','require'),
			array('company','require'),
			array('downDistributionNum','option'),
			array('permission','option',array()),
			array('client','option',array()),
		));
		
		//检查权限
		$loginUser = $this->loginAo->checkMustLogin();
		if( $loginUser['type'] == $this->userTypeEnum->ADMIN ){
			//管理员可以为所欲为
		}else{
			//非管理员不能设置类型，权限和手下，并且只能设置自己的信息
			unset($data['type']);
			unset($data['permission']);
			unset($data['client']);
			unset($data['downDistributionNum']);
			$userId = $loginUser['userId'];
		}
		
		//执行业务逻辑
		$this->userAo->mod($userId,$data);
	}
	
	/**
	* @view json
	*/
	public function modPassword()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userId','require'),
			array('password','require'),
		));
		
		//检查权限
		$this->loginAo->checkMustAdmin();
		
		//执行业务逻辑
		$this->userAo->modPassword(
			$data['userId'],
			$data['password']
		);
	}
	
	/**
	* @view json
	*/
	public function modMyPassword()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('oldPassword','require'),
			array('newPassword','require')
		));
		
		//检查权限
		$loginUser = $this->loginAo->checkMustLogin();
		
		//执行业务逻辑
		$this->userAo->modPasswordByOld(
			$loginUser['userId'],
			$data['oldPassword'],
			$data['newPassword']
		);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
