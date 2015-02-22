<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Deal extends CI_Controller {

	public function __construct()
  	{
		parent::__construct();
		$this->load->model('order/orderPayAo','orderPayAo');
		$this->load->model('order/orderAo','orderAo');
		$this->load->model('client/clientLoginAo', 'clientLoginAo');
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->library('argv','argv');
  	}

	/**
	* @view json
	*/
	public function testpay()
	{
			//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userId', 'require'),
			array('dealId', 'require'),
			array('dealDesc', 'require'),
			array('dealFee', 'require'),
		));
		$userId = $data['userId'];
		$dealId = $data['dealId'];
		$dealDesc = $data['dealDesc'];
		$dealFee = $data['dealFee'];

		//检查权限
		$client = $this->clientLoginAo->checkMustLogin($userId);
		$clientId = $client['clientId'];
		$openId = $client['openId'];

		//业务逻辑
		return $this->orderPayAo->wxJsPay(
			$userId,
			'oMhf-txr18KIBU1GZ0TXpxToaoH8',
			$dealId,
			$dealDesc,
			$dealFee
		);
	}

	/**
	* @view json
	*/
	public function search()
	{
		//检查输入参数    
		$dataWhere = $this->argv->checkGet(array(
			array('name','option'),
			array('state','option'),
		));

		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','require'),
			array('pageSize','require'),
		));

		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMMODITY_MANAGE
        );
        $userId = $user['userId'];

		//执行业务逻辑
		return $this->orderAo->search($userId,$dataWhere,$dataLimit);
	}

	/**
	* @view json
	*/
	public function get()
	{
		//检查输入参数    
		$data = $this->argv->checkGet(array(
			array('shopOrderId','option'),
		));
		$shopOrderId = $data['shopOrderId'];

		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMMODITY_MANAGE
        );
        $userId = $user['userId'];

		//执行业务逻辑
		return $this->orderAo->get($userId,$shopOrderId);
	}

	/**
	* @view json
	*/
	public function getMyOrderCount()
	{
		//检查输入参数    
		$data = $this->argv->checkGet(array(
			array('userId','require'),
		));
		$userId = $data['userId'];

		//检查权限
		$client = $this->clientLoginAo->checkMustLogin($userId);
		$clientId = $client['clientId'];

		//执行业务逻辑
		return $this->orderAo->getClientOrder($userId,$clientId);
	}

	/**
	* @view json
	*/
	public function getMyOrderList()
	{
		//检查输入参数    
		$data = $this->argv->checkGet(array(
			array('userId','require'),
			array('state','require'),
		));
		$userId = $data['userId'];
		$state = $data['state'];

		//检查权限
		$client = $this->clientLoginAo->checkMustLogin($userId);
		$clientId = $client['clientId'];

		//执行业务逻辑
		return $this->orderAo->getClientOrderDetail($userId,$clientId,$state);
	}

	/**
	* @view json
	*/
	public function getMyOrderDetail()
	{
		//检查输入参数    
		$data = $this->argv->checkGet(array(
			array('userId','require'),
			array('shopOrderId','require'),
		));
		$userId = $data['userId'];
		$shopOrderId = $data['shopOrderId'];

		//检查权限
		$client = $this->clientLoginAo->checkMustLogin($userId);
		$clientId = $client['clientId'];

		//执行业务逻辑
		return $this->orderAo->get($userId,$shopOrderId);
	}


	/**
	* @view json
	* @trans true
	*/
	public function order(){
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userId', 'require'),
			array('shopTroller', 'option',array()),
			array('address', 'option',array()),
		));
		$userId = $data['userId'];
		$shopTroller = $data['shopTroller'];
		$address = $data['address'];

		//检查权限
		$client = $this->clientLoginAo->checkMustLogin($userId);
		$clientId = $client['clientId'];
	   
		//业务逻辑
		return $this->orderAo->add(
			$userId,
			$clientId,
			$shopTroller,
			$address
		);
	}

	/**
	* @view json
	*/
    public function modState(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('shopOrderId', 'require'),
            array('newState', 'require'),
        ));
        $shopOrderId == $data['shopOrderId'];

		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMMODITY_MANAGE
        );
        $userId = $user['userId'];

        //业务逻辑
        $this->orderAo->modState($userId, $shopOrderId, $data); 
    }

	/**
	* @view json
	*/
	public function wxjspay(){
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userId', 'require'),
			array('shopOrderId', 'require'),
		));
		$userId = $data['userId'];
		$shopOrderId = $data['shopOrderId'];

		//检查权限
		$client = $this->clientLoginAo->checkMustLogin($userId);
		$clientId = $client['clientId'];
	   
		//业务逻辑
		return $this->orderAo->wxJsPay(
			$userId,
			$clientId,
			$shopOrderId
		);
	}

	/**
	* @view json
	*/
	public function wxpaycallback($userId=0)
	{
		//业务逻辑
		$data = $this->orderPayAo->wxPayCallback($userId);

		log_message('info','wxpaycallback:'.json_encode($data));
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
