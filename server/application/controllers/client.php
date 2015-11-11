<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Client extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('client/clientAo','clientAo');
		$this->load->model('client/clientTypeEnum','clientTypeEnum');
		$this->load->model('user/loginAo','loginAo');
		$this->load->library('argv','argv');
		$this->load->model('client/clientLoginAo','clientLoginAo');
    }
	
	/**
	* @view json
	*/
	public function getType()
	{
		return $this->clientTypeEnum->names;
	}
	
	/**
	* @view json
	*/
	public function search()
	{
		//检查输入参数
		$dataWhere = $this->argv->checkGet(array(
			array('type','option'),
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
		return $this->clientAo->search($userId,$dataWhere,$dataLimit);
	}
	
	/**
	* @view json
	*/
	public function get()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('clientId','require'),
		));
		$clientId = $data["clientId"];
		
		//检查权限
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_SHOP
		);
		$userId = $user['userId'];
		
		//执行业务逻辑
		return $this->clientAo->get(
			$userId,
			$clientId
		);
	}

	/**
	 * @view json
	 * getUserInfo
	 */
	public function getUserInfo(){
		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','require'),
			array('pageSize','require'),
		));
		$dataWhere = $this->argv->checkGet(array(
			array('nickName','option'),
		));
		$dataWhere['userId'] = $this->session->userdata('userId');
		$result = $this->clientAo->getUserInfo($dataWhere,$dataLimit);
		return $result;
	}

	/**
	 * @view json
	 * 获取用户信息
	 */
	public function getClientInfo(){
		if($this->input->is_ajax_request()){
			//检查输入参数
			$data = $this->argv->checkPost(array(
				array('userId','require'),
			));
			$userId = $data['userId'];

			$myUserId = $this->input->get('userIds');

			//检查权限
			$client = $this->clientLoginAo->checkMustLogin($userId);
			$clientId = $client['clientId'];

			$this->load->model('distribution/distributionOrderAo','distributionOrderAo');
			//获取累计销售
			// $info = $this->distributionOrderAo->getAllSales($userId);
			// $sales= 0;
			// foreach ($info as $key => $value) {
			// 	$sales += $value['order_price'];
			// }

			$result = $this->clientAo->get($userId,$clientId);
			$result['nickName'] = base64_decode($result['nickName']);
			$result['createTime'] = date('Y-m-d',strtotime($result['createTime']));
			$result['fall'] = sprintf('%.2f',$result['fall']/100);
			$result['sales'] = sprintf('%.2f',$result['sales']/100);
			// $result['sales'] = sprintf('%.2f',$sales/100);
			return $result;
		}
	}

	/**
	 * @view json
	 * 积分排行榜
	 */
	public function rankingList(){
		if($this->input->is_ajax_request()){
			//检查输入参数
			$data = $this->argv->checkPost(array(
				array('userId','require'),
			));
			$userId = $data['userId'];
			return $this->clientAo->rankingList($userId);
		}
	}

	//刷新微信用户
	public function ref(){
		$userId = 10088;
		$this->clientAo->ref($userId);
	}

	public function tt(){
		$userId = 10088;
		$openId = 'opj2Dv1R6pHWupmOXKULxAMrNGNs';
		$this->clientAo->tt($userId,$openId);
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
