<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Classify extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('classify/companyClassifyAo','companyClassifyAo');
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
			array('remark','option')
		));
		
		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','option'),
			array('pageSize','option'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustIntroduce();
		$userId = $user['userId'];
		
		//执行业务逻辑
		return $this->companyClassifyAo->search($userId,$dataWhere,$dataLimit);
	}
	
	/**
	*@view json
	*/
	public function getByUserId()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userId','require'),
		));
		$userId = $data['userId'];
		
		//执行业务逻辑
		return $this->companyClassifyAo->search($userId,array(),array());
	}
	
	/**
	* @view json
	*/
	public function get()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userCompanyClassifyId','require'),
		));
		$userCompanyClassifyId = $data['userCompanyClassifyId'];
		
		//执行业务逻辑
		return $this->companyClassifyAo->get($userCompanyClassifyId);
	}
	
	/**
	* @view json
	*/
	public function add()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('title','require'),
			array('remark','require'),
			array('icon','require'),
			array('link','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustIntroduce();
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->companyClassifyAo->add($userId,$data);
	}
	
	/**
	* @view json
	*/
	public function del()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userCompanyClassifyId','require'),
		));
		$userCompanyClassifyId = $data['userCompanyClassifyId'];
		
		//检查权限
		$user = $this->loginAo->checkMustIntroduce();
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->companyClassifyAo->del($userId,$userCompanyClassifyId);
	}
	
	/**
	* @view json
	*/
	public function mod()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userCompanyClassifyId','require'),
		));
		$userCompanyClassifyId = $data['userCompanyClassifyId'];
		
		$data = $this->argv->checkPost(array(
			array('title','require'),
			array('remark','require'),
			array('icon','require'),
			array('link','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustIntroduce();
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->companyClassifyAo->mod($userId,$userCompanyClassifyId,$data);
	}
	
	/**
	* @view json
	*/
	public function moveUp()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userCompanyClassifyId','require'),
		));
		$userCompanyClassifyId = $data['userCompanyClassifyId'];
		
		//检查权限
		$user = $this->loginAo->checkMustIntroduce();
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->companyClassifyAo->move($userId,$userCompanyClassifyId,'up');
	}
	
	/**
	* @view json
	*/
	public function moveDown()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userCompanyClassifyId','require'),
		));
		$userCompanyClassifyId = $data['userCompanyClassifyId'];
		
		//检查权限
		$user = $this->loginAo->checkMustIntroduce();
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->companyClassifyAo->move($userId,$userCompanyClassifyId,'down');
	}
}

