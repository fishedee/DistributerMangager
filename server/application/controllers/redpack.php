<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class RedPack extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('client/clientLoginAo','clientLoginAo');
		$this->load->model('redpack/redPackAo','redPackAo');
		$this->load->model('redpack/redPackStateEnum','redPackStateEnum');
		$this->load->model('user/userAppAo','userAppAo');
		$this->load->library('argv','argv');
    }
	
	/**
	* @view json
	*/
	public function getAllState(){
		return $this->redPackStateEnum->names;
	}

	/**
	* @view json
	*/
	public function searchRedPack()
	{
		//检查输入参数
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
		return $this->redPackAo->searchRedPack($userId,$dataLimit);
	}
	
	/**
	* @view json
	*/
	public function getRedPack()
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
		return $this->redPackAo->getRedPack($userId,$clientId);
	}

	/**
	* @view json
	* @trans true
	*/
	public function tryRedPack()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userId','require'),
			array('url','require')
		));
		$userId = $data['userId'];
		$url = $data['url'];
		
		//检查权限
		$client = $this->clientLoginAo->checkMustLogin(
			$data['userId']
		);
		$clientId = $client['clientId'];
		
		//执行业务逻辑
		$this->redPackAo->tryRedPack($userId,$clientId);

		return $this->userAppAo->getJsConfig($userId,$url);
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
		return $this->redPackAo->getSetting($userId);
	}
	
	/**
	* @view json
	*/
	public function setSetting()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('nickName','require'),
			array('minMoneyShow','require'),
			array('maxMoneyShow','require'),
			array('wishing','require'),
			array('actName','require'),
			array('remark','require'),
			array('state','require'),
			array('maxPackNum','require'),
			array('redPackRuleImage','require')
		));
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
		
		//执行业务逻辑
		return $this->redPackAo->setSetting($userId,$data);
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
