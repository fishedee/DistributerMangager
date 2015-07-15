<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wifi extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('user/loginAo','loginAo');
 		$this->load->model('weixin/wifiAo','wifiAo');
		$this->load->library('argv','argv');
	}
	
	/**
	 * @view json
	 */
	public function get(){
		
		//检查输入参数
		$dataLimit = $this->argv->checkGet(array(
				array('pageIndex','option'),
				array('pageSize','option'),
		));
	
		//检查权限
		$user = $this->loginAo->checkMustLogin();
		$userId =$user['userId'];
	
		//执行业务逻辑
		return $this->wifiAo->get(
				$userId,
				$dataLimit
		);
	}
	
	/**
	 * @view json
	 */
	public function add()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
				array('shop_id','require'),
				array('ssid','require'),
				array('password','require'),
				array('bssid','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustLogin();
		$userId =$user['userId'];
	
		//执行业务逻辑
		return $this->wifiAo->add($userId,$data);
	}

	/**
	 * @view json
	 */
	public function del()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
				array('bssid','require'),
		));
	
		//检查权限
		$user = $this->loginAo->checkMustLogin();
		$userId =$user['userId'];
	
		//执行业务逻辑
		return $this->wifiAo->del($userId,$data['bssid']);
	}	
	
	/**
	 * @view json
	 */	
	public function download()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
				array('shop_id','require'),
		));
	
		//检查权限
		$user = $this->loginAo->checkMustLogin();
		$userId =$user['userId'];
	
		//执行业务逻辑
		return $this->wifiAo->download($userId,$data['shop_id']);
	}
}

?>