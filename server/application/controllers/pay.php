<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pay extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('pay/wxPay','wxPay');
		$this->load->model('client/clientLoginAo', 'clientLoginAo');
		$this->load->library('argv','argv');
    }

	/**
	* @view json
	*/
	public function wxpay()
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
		return $this->wxPay->jsPay(
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
	public function wxpaycallback($userId=0)
	{
		//业务逻辑
		$data = $this->wxPay->payCallback($userId);

		log_message('error','temp:'.json_encode($data));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
