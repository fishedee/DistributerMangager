<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userAo','userAo');
		$this->load->model('client/clientLoginAo','clientLoginAo');
		$this->load->model('user/userAppAo','userAppAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('user/userTypeEnum','userTypeEnum');
		$this->load->library('argv','argv');
    }
	
	/**
	* @view json
	*/
	public function getAllType()
	{
		return $this->userTypeEnum->names;
	}

	/**
	* @view json
	*/
	public function getAppInfo()
	{
		//检查权限
		$userId = $this->loginAo->checkMustLogin();
		$userId = $userId['userId'];

		//业务逻辑
		return $this->userAppAo->get($userId,true);
	}

	/**
	* @view json
	*/
	public function getAppMsg()
	{
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('userId', 'require')
        ));

        $userId = $data['userId'];

         //检查权限
        $client = $this->clientLoginAo->checkMustLogin($userId);
		$userId = $client['userId'];

		//业务逻辑
		$retureData=$this->userAppAo->get($userId,true);
		$filter=array(	
			'appBg' => '',
			'appLogo'=> '',
			'appName'=>'',
			);
		return array_merge($filter,array_intersect_key($retureData,$filter));
	}

	/**
	* @view json
	*/
	public function getHasCompany(){
		//检查参数
		$data = $this->argv->checkGet(array(
			array('userId','require'),
		));

		//检查是否有公司介绍权限
		$user = $this->userAo->get($data['userId']);
		return in_array(
			$this->userPermissionEnum->COMPANY_INTRODUCE,
			$user['permission']
		);
	}

	/**
	 * @view json
	 */
	public function getHasChips(){
		//检查参数
		$data = $this->argv->checkGet(array(
			array('userId','require'),
		));

		//检查是否有众筹权限
		$user = $this->userAo->get($data['userId']);
		return in_array(
			$this->userPermissionEnum->COMPANY_CHIPS,
			$user['permission']
		);
	}

	/**
	* @view json
	*/
	public function getHasShop(){
		//检查参数
		$data = $this->argv->checkGet(array(
			array('userId','require'),
		));

		//检查是否有公司介绍权限
		$user = $this->userAo->get($data['userId']);
		return in_array(
			$this->userPermissionEnum->COMPANY_SHOP,
			$user['permission']
		) || in_array(
			$this->userPermissionEnum->COMPANY_SHOP_PRO,
			$user['permission']
		);
	}

	/**
	* @view json
	*/
	public function modAppInfo()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('appName','require'),
			array('appBg','option'),
			array('appLogo','option'),
			array('poster','option'),
			array('weixinNum','require'),
			array('appId','require'),
			array('appKey','require'),
			array('mchId','require'),
			array('mchKey','require'),
			array('mchSslCert','require'),
			array('mchSslKey','require'),
			array('remark','require'),
			array('customService','option'),
		));
		//检查权限
		$userId = $this->loginAo->checkMustLogin();
		$userId = $userId['userId'];
		//业务逻辑
		return $this->userAppAo->mod($userId,$data);
	}
	
	
	/**
	* @view json
	*/
	public function getAllPermission()
	{ 
		return $this->userPermissionEnum->names;
	}
	
	/**
	* @view json
	*/
	public function search()
	{
		//检查输入参数		
		$dataWhere = $this->argv->checkGet(array(
			array('name','option'),
			array('type','option'),
			array('company','option'),
			array('phone','option'),
			array('permissionId','option'),
		));

		// var_dump($dataWhere);die;
		
		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','require'),
			array('pageSize','require'),
		));
		
		//检查权限
		$this->loginAo->checkMustLogin();
		
		//执行业务逻辑
		return $this->userAo->search($dataWhere,$dataLimit);
	}
	
	/**
	* @view json
	*/
	public function get()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userId','require'),
		));
		$userId = $data['userId'];
		
		//检查权限
		$this->loginAo->checkMustLogin();
		
		//执行业务逻辑
		$result = $this->userAo->get($userId);
		if($this->input->get('distribution') == 1){
			$appName = $this->userAppAo->getAppName($userId);
			return array(
				'telephone'=>$result['telephone'],
				'appName'  =>$appName
				);
		}else{
			return $result;
		}
	}
	
	/**
	* @view json
	*/
	public function add()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('name','require'),
			array('password','option','123456'),
			array('type','option',$this->userTypeEnum->CLIENT),
			array('phone','require'),
			array('telephone','require'),
			array('company','require'),
			array('email','require'),
			array('followLink','option|noxss'),
			array('downDistributionNum','option',0),
			array('permission','option',array()),
			array('client','option',array()),
		));
		
		//检查权限
		$loginUser = $this->loginAo->islogin();
		if( $loginUser !== false ){
			//登录用户
			if( $loginUser['type'] == $this->userTypeEnum->ADMIN ){
				//管理员可以为所欲为
			}else{
				//非管理员只能添加商城用户
				$data['type'] = $this->userTypeEnum->CLIENT;
				unset($data['permission']);
				unset($data['client']);
			}
		}else{
			//非登录用户只能添加商城用户
			$data['type'] = $this->userTypeEnum->CLIENT;
			unset($data['permission']);
			//非登陆用户，注册账号有普通商城和普通分销权限
			$data['permission']=array(2,3);
			unset($data['client']);
		}
		
		//执行业务逻辑
		$this->userAo->add($data);
	}
	
	/**
	* @view json
	*/
	public function del()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userId','require'),
		));
		$userId = $data['userId'];
		
		//检查权限
		$this->loginAo->checkMustAdmin();
		
		//执行业务逻辑
		$this->userAo->del($userId);
	}
	
	/**
	* @view json
	*/
	public function mod()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userId','require'),
		));
		$userId = $data['userId'];
		
		$data = $this->argv->checkPost(array(
			array('type','option'),
			array('phone','require'),
			array('telephone','require'),
			array('company','require'),
			array('followLink','require|noxss'),
			array('downDistributionNum','option'),
			array('permission','option',array()),
			array('client','option',array()),
		));
		
		//检查权限
		$loginUser = $this->loginAo->checkMustLogin();
		if( $loginUser['type'] == $this->userTypeEnum->ADMIN ){
			//管理员可以为所欲为
		}else{
			//非管理员不能设置类型，权限和手下，并且只能设置自己的信息
			unset($data['type']);
			unset($data['permission']);
			unset($data['client']);
			unset($data['downDistributionNum']);
			$userId = $loginUser['userId'];
		}
		
		//执行业务逻辑
		$this->userAo->mod($userId,$data);
	}
	
	/**
	* @view json
	*/
	public function modPassword()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userId','require'),
			array('password','require'),
		));
		
		//检查权限
		$this->loginAo->checkMustAdmin();
		
		//执行业务逻辑
		$this->userAo->modPassword(
			$data['userId'],
			$data['password']
		);
	}
	
	/**
	* @view json
	*/
	public function modMyPassword()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('oldPassword','require'),
			array('newPassword','require')
		));
		
		//检查权限
		$loginUser = $this->loginAo->checkMustLogin();
		
		//执行业务逻辑
		$this->userAo->modPasswordByOld(
			$loginUser['userId'],
			$data['oldPassword'],
			$data['newPassword']
		);
	}

	/**
	 * @view json
	 * 修改密码
	 */
	public function modMyPassword2(){
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('oldPassword','require'),
			array('newPassword','require')
		));
		$myUserId = $this->input->post('myUserId');
		return $this->userAo->modPasswordByOld($myUserId,$data['oldPassword'],$data['newPassword']);
	}
	
	/**
	 * @view json
	 * 进入该会员账号
	 */
	public function comeuser()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('userId','require'),
		));
		$userId = $data['userId'];
	
		//检查权限
		$loginUser = $this->loginAo->checkMustLogin();
		if(!($loginUser['type'] == $this->userTypeEnum->ADMIN))
			throw new CI_MyException(1,"只有管理员才能进入会员账号。");
	
		//执行业务逻辑
		$this->session->unset_userdata('userId');
		$this->session->set_userdata('userId',$userId);
	}

	/**
	 * @view json
	 * 检测系统是否分配账号密码
	 */
	public function checkClientId(){
		if($this->input->is_ajax_request()){
			//检查输入参数
			$data = $this->argv->checkPost(array(
				array('userId','require'),
			));
			$userId = $data['userId'];

			//检查权限
			$client = $this->clientLoginAo->checkMustLogin($userId);
			$clientId = $client['clientId'];
			return $this->userAo->checkClientId($userId,$clientId);
		}
	}

	/**
	 * @view json
	 * 获取用户账号
	 */
	public function getUserName(){
		if($this->input->is_ajax_request()){
			$userId = $this->input->get('userIds');
			return $this->userAo->getUserName($userId);
		}
	}

	/**
	 * @view json
	 * 获取我的二维码
	 */
	public function getMyQrCode(){
		if($this->input->is_ajax_request()){
			$myUserId = $this->input->get('myUserId');
			return $this->userAo->getMyQrCode($myUserId);
		}
	}

	/**
	 * @view json
	 * 获取用户信息
	 */
	public function getUserInfo(){
		if($this->input->is_ajax_request()){
			//检查输入参数
			$data = $this->argv->checkPost(array(
				array('userId','require'),
			));
			$userId = $data['userId'];
			//检查权限
			$client = $this->clientLoginAo->checkMustLogin($userId);
			$clientId = $client['clientId'];
			return $this->userAo->getUserInfo($clientId);
		}
	}

	/**
	 * @view json
	 * 修改信息
	 */
	public function myInfo(){
		if($this->input->is_ajax_request()){
			//检查输入参数
			$data = $this->argv->checkPost(array(
				array('userId','require'),
			));
			$userId = $data['userId'];
			//检查权限
			$client = $this->clientLoginAo->checkMustLogin($userId);
			$clientId = $client['clientId'];
			$data = $this->input->post();
			return $this->userAo->myInfo($clientId,$data);
		}
	}

	/**
	 * @view json
	 * 修改店铺名
	 */
	public function modAppName(){
		if($this->input->is_ajax_request()){
			$myUserId = $this->input->post('myUserId');
			$appName  = $this->input->post('appName');
			return $this->userAppAo->modAppName($myUserId,$appName);
		}
	}

	/**
	 * @view json
	 * 获取店铺名
	 */
	public function getAppName(){
		if($this->input->is_ajax_request()){
			$myUserId = $this->input->get('myUserId');
			return $this->userAppAo->getAppName($myUserId);
		}
	}

	/**
	 * @view json
	 * 获取部分信息 
	 */
	public function getInfo(){
		if($this->input->is_ajax_request()){
			$myUserId = $this->input->post('myUserId');
			return $this->userAo->getInfo($myUserId);
		}
	}

	/**
	 * @view json
	 * 补全信息
	 */
	public function complete(){
		if($this->input->is_ajax_request()){
			//检查输入参数
			$data = $this->argv->checkPost(array(
				array('userId','require'),
			));
			$upUserId = $data['userId'];
			$data = $this->input->post();
			// var_dump($data);die;
			return $this->userAo->complete($upUserId,$data);
		}
	}

	/**
	 * @view json
	 * 获取海报
	 */
	public function getPoster(){
		if($this->input->is_ajax_request()){
			//检查输入参数
			$data = $this->argv->checkPost(array(
				array('userId','require'),
			));
			$userId = $data['userId'];
			return $this->userAppAo->getPoster($userId);
		}
	}

	/**
	 * @view json
	 * 检测充值积分
	 */
	public function checkRecharge(){
		if($this->input->is_ajax_request()){
			//检查权限
			$this->loginAo->checkMustAdmin();
			$userId = $this->input->get('userId');
			return $this->userAo->checkRecharge($userId);
		}
	}

	/**
	 * @view json
	 * 充值
	 */
	public function recharge(){
		if($this->input->is_ajax_request()){
			//检查权限
			$this->loginAo->checkMustAdmin();
			$data = $this->input->post('data');
			$userId = $this->input->post('userId');
			return $this->userAo->recharge($userId,$data['score']);
		}
	}

	/**
	 * @view json
	 * 获取手机验证码
	 */
	public function getPhoneCode(){
        if($this->input->is_ajax_request()){
            $phone = $this->input->get('phone');
            return $this->userAo->getPhoneCode($phone);
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
