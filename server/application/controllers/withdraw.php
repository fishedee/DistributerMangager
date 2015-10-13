<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WithDraw extends CI_Controller {

	public function __construct(){
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userAo','userAo');
		$this->load->model('client/clientLoginAo','clientLoginAo');
		$this->load->library('argv','argv');
		$this->load->model('withdraw/withDrawAo','withDrawAo');
    }

    /**
     * @view json
     * 查看提现申请
     */
    public function search(){
    	//检查输入参数		
		$dataWhere = $this->argv->checkGet(array(
			array('state','option'),
		));

		// var_dump($dataWhere);die;
		
		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','require'),
			array('pageSize','require'),
		));
		
		//检查权限
		$userInfo = $this->loginAo->checkMustLogin();
		$vender   = $userInfo['userId'];
		return $this->withDrawAo->search($vender,$dataWhere,$dataLimit);
    }

    /**
     * @view json
     * 受理
     */
    public function accept(){
    	if($this->input->is_ajax_request()){
    		//检查权限
			$userInfo = $this->loginAo->checkMustLogin();
			$vender   = $userInfo['userId'];
			$withDrawId = $this->input->get('withDrawId');
			return $this->withDrawAo->accept($vender,$withDrawId);
    	}
    }

    /**
     * @view json
     * 拒绝
     */
    public function forbid(){
    	if($this->input->is_ajax_request()){
    		//检查权限
			$userInfo = $this->loginAo->checkMustLogin();
			$vender   = $userInfo['userId'];
			$withDrawId = $this->input->get('withDrawId');
			return $this->withDrawAo->forbid($vender,$withDrawId);
    	}
    }

    /**
     * @view json
     * 申请提现
     */
    public function draw(){
    	if($this->input->is_ajax_request()){
    		//检查输入参数
			$data = $this->input->post();
			$client = $this->clientLoginAo->checkMustLogin($data['userId']);
            $clientId = $client['clientId'];
            return $this->withDrawAo->draw($data,$clientId);
    	}
    }

    /**
     * @view json
     * 获取提现日志
     */
    public function getLog(){
        if($this->input->is_ajax_request()){
            //检查权限
            $userInfo = $this->loginAo->checkMustLogin();
            $vender   = $userInfo['userId'];
            $client = $this->clientLoginAo->checkMustLogin($vender);
            $clientId = $client['clientId'];
            return $this->withDrawAo->getLog($vender,$clientId);
        }
    }

    /**
     * @view json
     * 前台获取账户明细
     */
    public function getMoneyLog(){
        if($this->input->is_ajax_request()){
            //检查权限
            $userInfo = $this->loginAo->checkMustLogin();
            $vender   = $userInfo['userId'];
            $client = $this->clientLoginAo->checkMustLogin($vender);
            $clientId = $client['clientId'];
            return $this->withDrawAo->getMoneyLog($vender,$clientId);
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
