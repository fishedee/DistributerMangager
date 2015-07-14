<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stores extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('weixin/storesAo','storesAo');
		$this->load->library('argv','argv');
	}
	
	/**
	 * @view json
	 */
	public function get()
	{
		//检查输入参数
		$dataLimit = $this->argv->checkGet(array(
				array('pageIndex','option'),
				array('pageSize','option'),
		));
	
		//检查权限
		$user = $this->loginAo->checkMustLogin();
		$userId =$user['userId'];
	
		//执行业务逻辑
		return $this->storesAo->get(
				$userId,
				$dataLimit
		);
	}

	/**
	 * @view json
	 */
	public function search()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
				array('poi_id','require'),
		));
	
		//检查权限
		$user = $this->loginAo->checkMustLogin();
		$userId =$user['userId'];
	
		//执行业务逻辑
		return $this->storesAo->search($userId,$data);
	}	
	
	
}

?>