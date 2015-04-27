<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LuckyDraw extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('client/clientLoginAo','clientLoginAo');
		$this->load->model('luckydraw/luckyDrawAo','luckyDrawAo');
		$this->load->model('luckydraw/luckyDrawStateEnum','luckyDrawStateEnum');
		$this->load->model('luckydraw/luckyDrawTypeEnum','luckyDrawTypeEnum');
		$this->load->library('argv','argv');
    }
	
	/**
	* @view json
	*/
	public function getAllType()
	{
		return $this->luckyDrawTypeEnum->names;
	}

	/**
	* @view json
	*/
	public function getAllState()
	{
		return $this->luckyDrawStateEnum->names;
	}
	
	/**
	* @view json
	*/
	public function search()
	{
		//检查输入参数		
		$dataWhere = $this->argv->checkGet(array(
			array('title','option'),
			array('summary','option'),
			array('state','option'),
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
		return $this->luckyDrawAo->search($userId,$dataWhere,$dataLimit);
	}
	
	/**
	* @view json
	*/
	public function get()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('luckyDrawId','require'),
		));
		$luckyDrawId = $data['luckyDrawId'];
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
		
		//执行业务逻辑
		return $this->luckyDrawAo->get($userId,$luckyDrawId);
	}
	
	/**
	* @view json
	*/
	public function add()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('title','require'),
			array('summary','require'),
			array('state','require'),
			array('beginTime','require'),
			array('endTime','require'),
			array('commodity','option',array()),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
		
		//执行业务逻辑
		$this->luckyDrawAo->add($userId,$data);
	}
	
	/**
	* @view json
	*/
	public function del()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('luckyDrawId','require'),
		));
		$luckyDrawId = $data['luckyDrawId'];
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
		
		//执行业务逻辑
		$this->luckyDrawAo->del($userId,$luckyDrawId);
	}
	
	/**
	* @view json
	*/
	public function mod()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('luckyDrawId','require'),
		));
		$luckyDrawId = $data['luckyDrawId'];
		
		$data = $this->argv->checkPost(array(
			array('title','require'),
			array('summary','require'),
			array('state','require'),
			array('beginTime','require'),
			array('endTime','require'),
			array('commodity','option',array()),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
		
		//执行业务逻辑
		$this->luckyDrawAo->mod($userId,$luckyDrawId,$data);
	}

	/**
	* @view json
	*/
	public function getResult()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('luckyDrawId','require'),
		));
		$luckyDrawId = $data['luckyDrawId'];
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
		
		//执行业务逻辑
		return $this->luckyDrawAo->getResult($userId,$luckyDrawId);
	}
	
	/**
	* @view json
	*/
	public function draw()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userId','require'),
			array('luckyDrawId','require'),
			array('name','require'),
			array('phone','require'),
		));
		
		//检查权限
		$client = $this->clientLoginAo->checkMustLogin($data['userId']);
		
		//执行业务逻辑
		return $this->luckyDrawAo->luckyDraw(
			$data['userId'],
			$client['clientId'],
			$data['luckyDrawId'],
			$data['name'],
			$data['phone']
		);
	}

	/**
	* @view json
	*/
	public function getClientResult()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userId','require'),
			array('luckyDrawId','require'),
		));
		
		//检查权限
		$client = $this->clientLoginAo->checkMustLogin($data['userId']);
		
		//执行业务逻辑
		return $this->luckyDrawAo->getClientResult(
			$data['userId'],
			$client['clientId'],
			$data['luckyDrawId']
		);
	}

	/**
	* @view json
	*/
	public function getClientAllResult()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userId','require'),
		));
		
		//检查权限
		$client = $this->clientLoginAo->checkMustLogin($data['userId']);
		
		//执行业务逻辑
		$this->luckyDrawAo->getClientAllResult(
			$data['userId'],
			$client['clientId']
		);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
