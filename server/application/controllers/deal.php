<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Deal extends CI_Controller {

	public function __construct()
  	{
		parent::__construct();
		$this->load->model('order/orderPayAo','orderPayAo');
		$this->load->model('order/orderStateEnum','orderStateEnum');
		$this->load->model('order/orderAo','orderAo');
		$this->load->model('client/clientLoginAo', 'clientLoginAo');
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->library('argv','argv');
  	}

  	/**
	* @view json
	*/
	public function getState()
	{
		return $this->orderStateEnum->names;
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
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $dataWhere['userId'] = $user['userId'];

		//执行业务逻辑
		return $this->orderAo->search($dataWhere,$dataLimit);
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
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];

		//执行业务逻辑
		return $this->orderAo->get($shopOrderId);
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
		return $this->orderAo->getClientOrder($clientId);
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
		return $this->orderAo->getClientOrderDetail($clientId,$state);
	}

	/**
	 * @view json
	 * 获取订单列表包括订单明细
	 */
	public function getMyOrderList2(){
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
		return $this->orderAo->getClientOrderDetail2($clientId,$state);
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
		return $this->orderAo->get($shopOrderId);
	}


	/**
	* @view json
	* @trans true
	*/
	public function order(){
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userId', 'require'),
			array('entranceUserId', 'require'),
			array('shopTroller', 'option',array()),
			array('address', 'option',array()),
			array('clientId', 'option',0),
		));
		$userId = $data['userId'];
		$entranceUserId = $data['entranceUserId'];
		$shopTroller = $data['shopTroller'];
		$address = $data['address'];
		$clientId = $data['clientId'];
		//检查权限
		$client = $this->clientLoginAo->checkMustLogin($userId);
		$loginClientId = $client['clientId'];
		if($clientId == 0 )
			$clientId = $loginClientId;
	
		//业务逻辑
		return $this->orderAo->add(
			$entranceUserId,
			$clientId,
			$loginClientId,
			$shopTroller,
			$address
		);
	}

	/**
	* @view json
	* 订单过期，重新下单
	*/
	public function againOrder(){
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userId', 'require'),
			array('shopOrderId', 'require'),
			array('clientId', 'option'),
		));
		$userId = $data['userId'];
		$shopOrderId = $data['shopOrderId'];
		$clientId = $data['clientId'];

		//检查权限
		$client = $this->clientLoginAo->checkMustLogin($userId);
		$loginClientId = $client['clientId'];
		if($clientId == 0 )
			$clientId = $loginClientId;

		return $this->orderAo->againOrder($shopOrderId);
	
	}

	/**
	 * @view json
	 * @trans true
	 * 新模板的下单方式 不需要传递地址信息
	 */
	public function order2(){
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userId', 'require'),
			array('entranceUserId', 'require'),
			array('shopTroller', 'option',array()),
			array('address', 'option',array()),
			array('clientId', 'option',0),
		));
		$userId = $data['userId'];
		$entranceUserId = $data['entranceUserId'];
		$shopTroller = $data['shopTroller'];
		$clientId = $data['clientId'];
		//检查权限
		$client = $this->clientLoginAo->checkMustLogin($userId);
		$loginClientId = $client['clientId'];

		$selectJiFen = $this->input->post('selectJiFen');

		if($clientId == 0 )
			$clientId = $loginClientId;
	    // var_dump($shopTroller);die;
		//业务逻辑
		return $this->orderAo->add2(
			$entranceUserId,
			$clientId,
			$loginClientId,
			$shopTroller,
			$selectJiFen
		);
	}

	/**
	* @view json
	*/
    public function modhassend(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('shopOrderId', 'require'),
        ));
        $shopOrderId = $data['shopOrderId'];

		//检查权限
		$user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];

        //业务逻辑
        $this->orderAo->modHasSend($shopOrderId); 
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
			$clientId,
			$shopOrderId
		);
	}

	public function wxpaycallback($userId=0)
	{
		//业务逻辑
		$data = $this->orderPayAo->wxPayCallback($userId);

		log_message('info','wxpaycallback:'.json_encode($data));
		
		echo 
		'<xml>
		  <return_code><![CDATA[SUCCESS]]></return_code>
		  <return_msg><![CDATA[OK]]></return_msg>
		</xml>';
	}
	
	/**
	 * @view json
	 * 获取快递公司名称
	 */
	public function modExp(){
		$shopOrderId = $this->input->post('shopOrderId');
		$data['expressageName'] = $this->input->post('expressageName');
		$data['expressageNum'] = $this->input->post('expressageNum');
		if ($data['expressageName'] == 0)
			throw new CI_MyException(1,"请选择快递公司");

		if ($data['expressageNum'] == 0)
			throw new CI_MyException(1,"请填写快递单号");
		
		$this->load->model('order/orderDb','orderDb');
		$this->orderDb->mod($shopOrderId,$data);
	}

	/**
	 * @view json
	 * 获取订单数量
	 */
	public function getOrderNum(){
		if($this->input->is_ajax_request()){
			$userId   = $this->input->get('userId');
			$myUserId = $this->input->get('myUserId');
			$this->load->model('order/orderDb','orderDb');
			return $this->orderDb->getOrderNum($userId,$myUserId);
		}
	}

	/**
	 * @view json
	 * 确认收货
	 */
	public function confirmReceive(){
		if($this->input->is_ajax_request()){
			//检查输入参数
			$data = $this->argv->checkGet(array(
				array('userId', 'require'),
			));
			$userId = $data['userId'];
			$shopOrderId = $this->input->get('shopOrderId');
			$shopOrderCommodityId = $this->input->get('shopOrderCommodityId');
			//检查权限
			$client = $this->clientLoginAo->checkMustLogin($userId);
			$clientId = $client['clientId'];
			return $this->orderAo->confirmReceive($clientId,$shopOrderId,$shopOrderCommodityId);
		}
	}

	/**
	 * @view json
	 * 检测厂家id 和 进入的id
	 * date:2015.12.16
	 */
	public function checkUserAndEntrance(){
		if($this->input->is_ajax_request()){
			//检查输入参数
			$data = $this->argv->checkGet(array(
				array('userId', 'require'),
			));
			$userId = $data['userId']; //厂家id
			$entranceUserId = $this->input->get('entranceUserId');
			if($userId != $entranceUserId){
				$url = 'http://'.$_SERVER['HTTP_HOST'].'/'.$userId.'/shopcart.html';
				return array(
					'url'=>$url,
					'entranceUserId'=>$entranceUserId
					);
			}else{
				return 1;
			}
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
