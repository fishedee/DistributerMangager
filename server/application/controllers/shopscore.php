<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ShopScore extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('client/clientLoginAo','clientLoginAo');
		$this->load->model('client/shopScoreAo','shopScoreAo');
		$this->load->library('argv','argv');
    }
	
	/**
	* @view json
	*/
	public function search(){
		//检查输入参数		
		$dataWhere = $this->argv->checkGet(array(
			array('question','option'),
		));
		
		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','require'),
			array('pageSize','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustLogin();
		$userId = $user['userId'];
		
		//执行业务逻辑
		return $this->shopScoreAo->search($userId,$dataWhere,$dataLimit);
	}

	/**
	 * @view json
	 * 获取信息
	 */
	public function getInfo(){
		if($this->input->is_ajax_request()){
			//检查权限
			$user = $this->loginAo->checkMustLogin();
			$userId = $user['userId'];
			$data = $this->input->post('data');
			return $this->shopScoreAo->getInfo($userId,$data);
		}
	}

	/**
	 * @view json
	 * @trans true
	 * 兑换积分
	 */
	public function add(){
		if($this->input->is_ajax_request()){
			//检查权限
			$user = $this->loginAo->checkMustLogin();
			$userId = $user['userId'];
			$data = $this->input->post('data');
			return $this->shopScoreAo->add($userId,$data);
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
