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
			array('tt','option'),
		));
		$userId = $data['userId'];
		$entranceUserId = $data['entranceUserId'];
		$shopTroller = $data['shopTroller'];
		$address = $data['address'];
		$clientId = $data['clientId'];
		$tt = $data['tt'];

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
			$address,
			$tt
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
	 * 判断地址
	 */
	public function leading(){
		if($this->input->is_ajax_request()){
			$data = $this->argv->checkGet(array(
				array('userId', 'require'),
			));
			$userId = $data['userId'];
			//检查权限
			$client = $this->clientLoginAo->checkMustLogin($userId);
			$clientId = $client['clientId'];
			$leadingUserId = $this->input->post('leadingUserId');
			if($leadingUserId != $userId){
				$this->session->set_userdata('leadingUserId',$userId);
				$this->session->set_userdata('leadingClientId',$clientId);
				return $userId;
			}else{
				return 0;
			}
		}
	}

	public function test(){
		$shopOrderId = '201511051445391211141384';
		$shopOrder = $this->orderDb->get($shopOrderId);
		$this->load->model('distribution/distributionOrderAo','distributionOrderAo');
		$this->load->model('distribution/distributionAo','distributionAo');
		$this->load->model('user/userAo','userAo');
		$this->load->model('distribution/distributionOrderDb','distributionOrderDb');
		$this->load->model('order/orderAo','orderAo');
		$this->load->model('client/clientAo');
		$distributionOrder = $this->distributionOrderAo->getDistributionOrder($shopOrderId);
		// var_dump($distributionOrder);die;
		foreach ($distributionOrder as $key => $value) {
			$info = $this->distributionAo->get($value['vender'],$value['distributionId']);
			$distributionOrderId = $value['distributionOrderId'];
			$data['state'] = 1;
			$data['price'] = intval($shopOrder['price'] * $info['distributionPercent'] * 0.01);
			// $result = $this->distributionOrderAo->mods($distributionOrderId,$data);

			//同步用户信息
			$downUserId   = $info['downUserId'];
			$downUserInfo = $this->userAo->get($downUserId);
			$clientId     = $downUserInfo['clientId'];
			$infos = $this->distributionOrderDb->getDistributionPrice($value['vender'],$downUserId);
	        $sales= 0;
	        $fall = 0;
	        var_dump($infos);die;
	        foreach ($infos as $k => $v) {
	            $shopOrderInfo = $this->orderDb->get($v['shopOrderId']);
	            $sales += $shopOrderInfo['price'];
	            $fall  += $v['price'];
	        }
	        $data = array();
	        $data['sales'] = $sales;
	        $data['fall']  = $fall;
	        $this->clientAo->mod($value['vender'],$clientId,$data);

		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
