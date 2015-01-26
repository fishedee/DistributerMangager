<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Template extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userTypeEnum','userTypeEnum');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('template/companyTemplateAo','companyTemplateAo');
		$this->load->library('argv','argv');
    }
	
	/**
	* @view json
	*/
	public function search()
	{
		//检查输入参数		
		$dataWhere = $this->argv->checkGet(array(
			array('title','option'),
			array('remark','option'),
		));
		
		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','option'),
			array('pageSize','option'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustLogin();
		if( $user['type'] != $this->userTypeEnum->ADMIN ){
			$this->loginAo->checkMustClient(
				$this->userPermissionEnum->COMPANY_INTRODUCE
			);
		}
		
		//执行业务逻辑
		return $this->companyTemplateAo->search($dataWhere,$dataLimit);
	}
	
	/**
	* @view json
	*/
	public function get()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('companyTemplateId','require'),
		));
		$companyTemplateId = $data['companyTemplateId'];
		
		//检查权限
		$this->loginAo->checkMustAdmin();
		
		//执行业务逻辑
		return $this->companyTemplateAo->get($companyTemplateId);
	}
	
	/**
	* @view json
	*/
	public function add()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('title','require'),
			array('url','require'),
			array('remark','require'),
		));
		
		//检查权限
		$this->loginAo->checkMustAdmin();
		
		//执行业务逻辑
		$this->companyTemplateAo->add($data);
	}
	
	/**
	* @view json
	*/
	public function del()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('companyTemplateId','require'),
		));
		$companyTemplateId = $data['companyTemplateId'];
		
		//检查权限
		$this->loginAo->checkMustAdmin();
		
		//执行业务逻辑
		$this->companyTemplateAo->del($companyTemplateId);
	}
	
	/**
	* @view json
	*/
	public function mod()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('companyTemplateId','require'),
		));
		$companyTemplateId = $data['companyTemplateId'];
		
		$data = $this->argv->checkPost(array(
			array('title','require'),
			array('url','require'),
			array('remark','require'),
		));
		
		//检查权限
		$this->loginAo->checkMustAdmin();
		
		//执行业务逻辑
		$this->companyTemplateAo->mod($companyTemplateId,$data);
	}
	
	
	/**
	* @view json
	*/
	public function getMyTemplate()
	{
		//检查权限
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_INTRODUCE
		);
		$userId = $user['userId'];
		
		//执行业务逻辑
		return $this->companyTemplateAo->getByUserId($userId);
	}
	
	/**
	* @view json
	*/
	public function modMyTemplate()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('companyTemplateId','require'),
		));
		$companyTemplateId = $data['companyTemplateId'];
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_INTRODUCE
		);
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->companyTemplateAo->modByUserId($userId,$companyTemplateId);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
