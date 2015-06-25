<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shopbanner extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('shop/bannerAo','bannerAo');
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
		));
		
		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','require'),
			array('pageSize','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_SHOP
		);
		$userId = $user['userId'];
		
		//执行业务逻辑
		return $this->bannerAo->search($userId,$dataWhere,$dataLimit);
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
		return $this->bannerAo->search($userId,array(),array());
	}
	
	/**
	* @view json
	*/
	public function get()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userShopBannerId','require'),
		));
		$userShopBannerId = $data['userShopBannerId'];
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_SHOP
		);
		$userId = $user['userId'];
		
		//执行业务逻辑
		return $this->bannerAo->get($userId,$userShopBannerId);
	}
	
	/**
	* @view json
	*/
	public function add()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('title','require'),
			array('icon','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_SHOP
		);
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->bannerAo->add($userId,$data);
	}
	
	/**
	* @view json
	*/
	public function del()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userShopBannerId','require'),
		));
		$userShopBannerId = $data['userShopBannerId'];
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_SHOP
		);
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->bannerAo->del($userId,$userShopBannerId);
	}
	
	/**
	* @view json
	*/
	public function mod()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userShopBannerId','require'),
		));
		$userShopBannerId = $data['userShopBannerId'];
		
		$data = $this->argv->checkPost(array(
			array('title','require'),
			array('icon','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_SHOP
		);
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->bannerAo->mod($userId,$userShopBannerId,$data);
	}
	
	/**
	* @view json
	*/
	public function moveUp()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userShopBannerId','require'),
		));
		$userShopBannerId = $data['userShopBannerId'];
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_SHOP
		);
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->bannerAo->move($userId,$userShopBannerId,'up');
	}
	
	/**
	* @view json
	*/
	public function moveDown()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userShopBannerId','require'),
		));
		$userShopBannerId = $data['userShopBannerId'];
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_SHOP
		);
		$userId = $user['userId'];
		
		//执行业务逻辑
		$this->bannerAo->move($userId,$userShopBannerId,'down');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
