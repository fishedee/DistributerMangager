<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->library('argv','argv');
    }

	/**
	*@view json
	*/
	public function islogin()
	{
		return $this->loginAo->checkMustLogin();
	}
	
	/**
	* @view json
	*/
	public function checkout()
	{
		$this->loginAo->logout();
	}
	
	/**
	* @view json
	*/
	public function checkin()
	{
		//检查输入参数
		$data = $this->argv->checkPost(array(
			array('name','require'),
			array('password','require'),
		));
		
		//执行业务逻辑
		$this->loginAo->login(
			$data["name"],
			$data["password"]
		);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
