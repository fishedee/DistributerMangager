<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contact extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('contact/contactAo','contactAo');
		$this->load->library('argv','argv');
    }
	
	/**
	* @view json
	*/
	public function getByUserId()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userId','require'),
		));
		$userId = $data['userId'];
		
		//执行业务逻辑
		return $this->contactAo->get($userId);
	}
	
	/**
	* @view json
	*/
	public function get()
	{	
		//检查权限
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_INTRODUCE
		);
		$userId = $user['userId'];
		
		//执行业务逻辑
		return $this->contactAo->get($userId);
	}
	
	/**
	* @view json
	*/
	public function add()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('content','require|noxss'),
			array('latitude','require'),
			array('longitude','require'),
			array('name','require'),
			array('address','require'),
			array('scale','require'),
			array('infoUrl','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_INTRODUCE
		);
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->contactAo->add($userId,$data);
	}
	
	/**
	* @view json
	*/
	public function mod()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('content','require|noxss'),
			array('latitude','require'),
			array('longitude','require'),
			array('name','require'),
			array('address','require'),
			array('scale','require'),
			array('infoUrl','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_INTRODUCE
		);
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->contactAo->mod($userId,$data);
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
