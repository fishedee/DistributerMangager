<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Size extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('client/clientLoginAo','clientLoginAo');
		$this->load->model('size/sizeAo','sizeAo');
		$this->load->library('argv','argv');
    }
	
	/**
	* @view json
	*/
	public function search(){
		//检查输入参数		
		$dataWhere = $this->argv->checkGet(array(
			array('name','option'),
		));
		
		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','require'),
			array('pageSize','require'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustLogin();
		$userId = $user['userId'];
		
		//执行业务逻辑
		return $this->sizeAo->search($userId,$dataWhere,$dataLimit);
	}

	/**
	 * @view json
	 * 提交申请
	 */
	public function add(){
		if($this->input->is_ajax_request()){
			//检查输入参数
	        $data = $this->argv->checkGet(array(
	            array('userId', 'require'),
	        ));
	        $userId = $data['userId'];
			//检查权限
        	$client = $this->clientLoginAo->checkMustLogin($userId);
        	$data   = array();
        	$name   = $this->input->post('name');
        	$contact= $this->input->post('contact');
        	$address= $this->input->post('address');
        	$data['name'] = $name;
        	$data['contact'] = $contact;
        	$data['address'] = $address;
        	$clientId = $client['clientId'];
        	return $this->sizeAo->add($userId,$clientId,$data);
		}
	}

	/**
	 * @view json
	 */
	public function checkAccept(){
		if($this->input->is_ajax_request()){
			//检查权限
			$user = $this->loginAo->checkMustLogin();
			$userId = $user['userId'];
			$sizeId = $this->input->get('sizeId');
			return $this->sizeAo->checkAccept($userId,$sizeId);
		}
	}

	/**
	 * @view json
	 * 受理
	 */
	public function accept(){
		if($this->input->is_ajax_request()){
			//检查权限
			$user = $this->loginAo->checkMustLogin();
			$userId = $user['userId'];
			$sizeId = $this->input->get('sizeId');
			return $this->sizeAo->accept($userId,$sizeId);
		}
	}

	/**
	 * @view json
	 * 删除
	 */
	public function del(){
		if($this->input->is_ajax_request()){
			//检查权限
			$user = $this->loginAo->checkMustLogin();
			$userId = $user['userId'];
			$sizeId = $this->input->get('sizeId');
			return $this->sizeAo->del($userId,$sizeId);
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
