<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vip extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('client/clientLoginAo','clientLoginAo');
		$this->load->model('vip/vipAo','vipAo');
		$this->load->library('argv','argv');
    }
	
	/**
	* @view json
	*/
	public function searchCard()
	{
		//检查输入参数		
		$dataWhere = $this->argv->checkGet(array(
			array('name','option'),
			array('phone','option'),
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
		return $this->vipAo->searchCard($userId,$dataWhere,$dataLimit);
	}
	
	/**
	* @view json
	*/
	public function getCard()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userId','require'),
		));
		$userId = $data['userId'];
		
		//检查权限
		$client = $this->clientLoginAo->checkMustLogin(
			$data['userId']
		);
		$clientId = $client['clientId'];
		
		//执行业务逻辑
		return $this->vipAo->getCard($userId,$clientId);
	}

	/**
	* @view json
	*/
	public function getCardDetail()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('clientId','require'),
		));
		$clientId = $data['clientId'];
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
		
		//执行业务逻辑
		return $this->vipAo->getCard($userId,$clientId);
	}
	
	/**
	* @view json
	*/
	public function modCard()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userId','require'),
		));
		$userId = $data['userId'];

		$data = $this->argv->checkPost(array(
			array('name','require'),
			array('phone','require'),
		));
		
		//检查权限
		$client = $this->clientLoginAo->checkMustLogin(
			$data['userId']
		);
		$clientId = $client['clientId'];
		
		//执行业务逻辑
		return $this->vipAo->modCard($userId,$clientId,$data);
	}

	/**
	* @view json
	*/
	public function modCardScore()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('clientId','require'),
		));
		$clientId = $data['clientId'];

		$data = $this->argv->checkPost(array(
			array('score','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
		
		//执行业务逻辑
		return $this->vipAo->modCard($userId,$clientId,$data);
	}
	
	/**
	* @view json
	*/
	public function getSetting()
	{
		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
		
		//执行业务逻辑
		return $this->vipAo->getSetting($userId);
	}
	
	/**
	* @view json
	*/
	public function setSetting()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('cardImage','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
		
		//执行业务逻辑
		return $this->vipAo->setSetting($userId,$data);
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
