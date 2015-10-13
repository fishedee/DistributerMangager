<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ClientLogin extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('client/clientLoginAo','clientLoginAo');
		$this->load->model('client/clientWxLoginAo','clientWxLoginAo');
		$this->load->library('argv','argv');
    }
	
	/**
	* @view json
	*/
	public function islogin()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userId','require')
		));
		return $this->clientLoginAo->checkMustLogin($data['userId'])['clientId'];
	}

	/**
	* @view json
	*/
	public function testlogin()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('userId','require'),
			array('clientId','require')
		));

		$this->clientLoginAo->login($data['userId'],$data['clientId']);
	}
	
	/**
	* @view json
	*/
	public function logout()
	{
		$this->clientLoginAo->logout();
	}
	
	/**
	* @view json
	*/
	public function wxlogin()
	{
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('callback','require'),
			array('userId','userId')
		));
		
		//业务逻辑
		$this->clientWxLoginAo->login($data['userId'],$data['callback']);
	}
	
	/**
	* @view json
	*/
	public function wxlogincallback($userId=0)
	{
		//业务逻辑
		$this->clientWxLoginAo->loginCallback($userId);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
