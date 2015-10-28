<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wxadvancedinterface extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userAppAo','userAppAo');
		$this->load->library('argv','argv');
	}
	
	/**
	 * @view json
	 * 获取自定义菜单
	 */
	
	public function getMenu()
	{

		//检查权限
		$userId = $this->loginAo->checkMustLogin();
		$userId =$userId['userId'];
		$userApp = $this->userAppAo->get($userId);
		$this->userAppAo->checkAppIdAppKey($userApp);
	
		//执行业务逻辑
		$this->load->model('weixin/wxMenuAo','wxMenuAo');
		return  $data=$this->wxMenuAo->getSetting($userId);
		
		//print_r($data);die();
	}
	
	/**
	 * @view json
	 * 设置自定义菜单
	 */
	public function setMenu(){
		//检查输入参数
		if (!empty($_POST['name1'])){$data[0]['name']=$this->input->post('name1');}
		if (!empty($_POST['url1'])){$data[0]['url']=$this->input->post('url1');}
		if (!empty($_POST['key1'])){$data[0]['key']=$this->input->post('key1');}
		if (!empty($_POST['name2'])){$data[1]['name']=$this->input->post('name2');}
		if (!empty($_POST['url2'])){$data[1]['url']=$this->input->post('url2');}
		if (!empty($_POST['key2'])){$data[1]['key']=$this->input->post('key2');}
		if (!empty($_POST['name3'])){$data[2]['name']=$this->input->post('name3');}
		if (!empty($_POST['url3'])){$data[2]['url']=$this->input->post('url3');}
		if (!empty($_POST['key3'])){$data[2]['key']=$this->input->post('key3');}
		if (!empty($_POST['sub_button1'])){$data[0]['sub_button'] = $this->input->post('sub_button1');}
 		if (!empty($_POST['sub_button2'])){$data[1]['sub_button'] = $this->input->post('sub_button2');}
 		if (!empty($_POST['sub_button3'])){$data[2]['sub_button'] = $this->input->post('sub_button3');}
 		
 		//存入数据库的数据
 		$mysqlData = $this->argv->checkPost(array(
 				array('name1','require'),
 				array('url1','require'),
 				array('name2','require'),
 				array('url2','require'),
 				array('name3','require'),
 				array('url3','require'),
 		));
 		
 		if (!empty($_POST['sub_button1'])){$mysqlData['sub_button1'] = $this->argv->checkPost(array(array('sub_button1','option',array())))['sub_button1'];}
		if (!empty($_POST['sub_button2'])){$mysqlData['sub_button2'] = $this->argv->checkPost(array(array('sub_button2','option',array())))['sub_button2'];}
		if (!empty($_POST['sub_button3'])){$mysqlData['sub_button3'] = $this->argv->checkPost(array(array('sub_button3','option',array())))['sub_button3'];}
		
 		//$data2=json_encode($data,JSON_UNESCAPED_UNICODE);//测试用的
 		//print_r($mysqlData);die();
 		
		//检查权限
		$userId = $this->loginAo->checkMustLogin();
		$userId =$userId['userId'];
		$userApp = $this->userAppAo->get($userId);
		$this->userAppAo->checkAppIdAppKey($userApp);
		
		//执行业务
		$this->load->model('weixin/wxMenuAo','wxMenuAo');
		$this->wxMenuAo->setSetting($userId,$data,$mysqlData);	
 		
	}
	
	/**
	 * @view json
	 * 添加客服人员
	 */
	public function customerServiceAdd(){
		$data = $this->argv->checkPost(array(
				array('kf_account','require'),
				array('nickname','require'),
				array('password','require'),
		));
		$data['password']=md5($data['password']);

		//检查权限
		$userId = $this->loginAo->checkMustLogin();
		$userId =$userId['userId'];
		$userApp = $this->userAppAo->get($userId);
		$this->userAppAo->checkAppIdAppKey($userApp);
		
		$this->load->model('weixin/wxCustomerServiceAo','wxCustomerServiceAo');
		$this->wxCustomerServiceAo->Add($userId,$data);
		
	}
	
	/**
	 * @view json
	 * 修改客服人员
	 */
	public function customerServiceMod(){
		$data = $this->argv->checkPost(array(
				array('kf_account','require'),
				array('kf_nick','require'),
				array('password','require'),
		));
		
		$data['nickname']=$data['kf_nick'];
		unset($data['kf_nick']);
		$data['password']=md5($data['password']);
	
		//检查权限
		$userId = $this->loginAo->checkMustLogin();
		$userId =$userId['userId'];
		$userApp = $this->userAppAo->get($userId);
		$this->userAppAo->checkAppIdAppKey($userApp);
	
		$this->load->model('weixin/wxCustomerServiceAo','wxCustomerServiceAo');
		$this->wxCustomerServiceAo->Mod($userId,$data);
	
	}
	
	/**
	 * @view json
	 * 删除客服人员
	 */
	public function customerServiceDel(){
		$data = $this->argv->checkPost(array(
				array('kf_account','require'),
		));
		
		//检查权限
		$userId = $this->loginAo->checkMustLogin();
		$userId =$userId['userId'];
		$userApp = $this->userAppAo->get($userId);
		$this->userAppAo->checkAppIdAppKey($userApp);
	
		$this->load->model('weixin/wxCustomerServiceAo','wxCustomerServiceAo');
		$this->wxCustomerServiceAo->Del($userId,$data);
	
	}
	
	/**
	 * @view json
	 * 上传客服头像
	 */
	public function customerServiceHeadPortrait(){
		$data = $this->argv->checkPost(array(
				array('kf_account','require'),
				array('media','require'),
		));
		
		//检查权限
		$userId = $this->loginAo->checkMustLogin();
		$userId =$userId['userId'];
		$userApp = $this->userAppAo->get($userId);
		$this->userAppAo->checkAppIdAppKey($userApp);
	
		$this->load->model('weixin/wxCustomerServiceAo','wxCustomerServiceAo');
		$this->wxCustomerServiceAo->customerServiceHeadPortrait($userId,$data);
	
	}
	
	/**
	 * @view json
	 * 获取数据库的客服列表
	 */
	public function customerServiceGet(){
		
		$dataLimit = $this->argv->checkGet(array(
				array('pageIndex','require'),
				array('pageSize','require'),
		));
		
		//检查权限
		$userId = $this->loginAo->checkMustLogin();
		$userId =$userId['userId'];
		$userApp = $this->userAppAo->get($userId);
		$this->userAppAo->checkAppIdAppKey($userApp);
		
		$this->load->model('weixin/wxCustomerServiceAo','wxCustomerServiceAo');
		return $this->wxCustomerServiceAo->search(array('userId'=>$userId),$dataLimit);
	}
	
	/**
	 * @view json
	 * 搜索客服
	 */
	public function customerServiceSearch(){
	
		$data = $this->argv->checkGet(array(
				array('kfId','require'),
		));
		
		//检查权限
		$userId = $this->loginAo->checkMustLogin();
		$userId =$userId['userId'];
		$userApp = $this->userAppAo->get($userId);
		$this->userAppAo->checkAppIdAppKey($userApp);
	
		$this->load->model('weixin/wxCustomerServiceAo','wxCustomerServiceAo');
		return $this->wxCustomerServiceAo->search($data,array())['data'][0];
	}
	
	/**
	 * @view json
	 * 获取微信服务器最新的客服列表
	 */
	public function customerServicePull(){

		//检查权限
		$userId = $this->loginAo->checkMustLogin();
		$userId =$userId['userId'];
		$userApp = $this->userAppAo->get($userId);
		$this->userAppAo->checkAppIdAppKey($userApp);

		$this->load->model('weixin/wxCustomerServiceAo','wxCustomerServiceAo');
		$this->wxCustomerServiceAo->Pull($userId);
		
	}
	
	/**
	 * @view json
	 * 获取模板信息开启状态
	 */
	public function getTemplateState(){
		
		//检查权限
		$userData = $this->loginAo->checkMustLogin();
		$userId =$userData['userId'];
	
		$this->load->model('weixin/wxTemplateAo','wxTemplateAo');
		return $this->wxTemplateAo->get($userId);
		
	}
	
	/**
	 * @view json
	 * 获取模板信息开启状态
	 */
	public function modTemplateState(){
	
		//检查权限
		$userData = $this->loginAo->checkMustLogin();
		$userId =$userData['userId'];
		
		$data = $this->argv->checkPost(array(
				array('openState','require'),
		));
	
		$this->load->model('weixin/wxTemplateAo','wxTemplateAo');
		return $this->wxTemplateAo->mod($userId,$data);
	
	}

	   /**
	     * @view json
	     * 关闭页面，发送客服消息。
	     */
	    public function autoCustomResponse(){
	        $this->load->model('client/clientAo','clientAo');
	        $this->load->model('user/userAppAo','userAppAo');
	        $this->load->library('argv','argv');
	        $this->load->library('http');
	        $data = $this->argv->checkGet(array(
	                        array('userId','require'),
	                ));
	        $userId = $data['userId'];
		    $clientId = $this->session->userdata('clientId');
		    $clientInfo = $this->clientAo->get($userId,$clientId);
		    $userInfo   = $this->userAppAo->get($userId);
		    $this->load->model('user/userAppAo','userAppAo');
		    
		    $userSet = $this->userAo->get($userId);
			$userFollowLink = $userSet['followLink'];

		    //如果没有关注该公众号
		     if(!$clientInfo['subscribe']){

		    	throw new CI_MyException(1,$userFollowLink );
		    	// return $userFollowLink;
		     }

		    $content= $userInfo['customService'];
		    $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$userInfo['appAccessToken'];
		    $arr['touser'] = $clientInfo['openId'];
		    $arr['msgtype']= 'text';
		    $arr['text']['content'] = urlencode($content);
		    $httpResponse = $this->http->ajax(array(
		                    'url'=>$url,
		                    'type'=>'post',
		                    'data'=>urldecode(json_encode($arr)),
		                    'dataType'=>'plain',
		                    'responseType'=>'json'
		            ));

	        // var_dump($httpResponse);
	        return $httpResponse['body']['errcode'];
	}


}