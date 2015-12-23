<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CompanyShop extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('contact/companyShopAo','companyShopAo');
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
		$user = $this->loginAo->checkMustClient(
			$this->userPermissionEnum->COMPANY_INTRODUCE
		);
		$userId = $user['userId'];
		
		//执行业务逻辑
		return $this->companyShopAo->search($userId,$dataWhere,$dataLimit);
	}

	/**
	 * @view json
	 * 获取门店信息
	 */
	public function get(){
		if($this->input->is_ajax_request()){
			//检查权限
			$user = $this->loginAo->checkMustClient(
				$this->userPermissionEnum->COMPANY_INTRODUCE
			);
			$userId = $user['userId'];
			$companyShopId = $this->input->get('companyShopId');
			return $this->companyShopAo->get($userId,$companyShopId);
		}
	}

	/**
	 * @view json
	 * 增加门店信息
	 */
	public function add(){
		if($this->input->is_ajax_request()){
			//检查权限
			$user = $this->loginAo->checkMustClient(
				$this->userPermissionEnum->COMPANY_INTRODUCE
			);
			$userId = $user['userId'];
			$data   = $this->input->post('data');
			return $this->companyShopAo->add($userId,$data);
		}
	}

	/**
	 * @view json
	 * 修改门店信息
	 */
	public function mod(){
		if($this->input->is_ajax_request()){
			//检查权限
			$user = $this->loginAo->checkMustClient(
				$this->userPermissionEnum->COMPANY_INTRODUCE
			);
			$userId = $user['userId'];
			$companyShopId = $this->input->post('companyShopId');
			$data   = $this->input->post('data');
			return $this->companyShopAo->mod($userId,$companyShopId,$data);
		}
	}

	/**
	 * @view json
	 * 删除门店信息
	 */
	public function del(){
		if($this->input->is_ajax_request()){
			//检查权限
			$user = $this->loginAo->checkMustClient(
				$this->userPermissionEnum->COMPANY_INTRODUCE
			);
			$userId = $user['userId'];
			$companyShopId = $this->input->post('companyShopId');
			return $this->companyShopAo->del($userId,$companyShopId);
		}
	}

	/**
	 * @view json
	 * 前端获取门店细信息
	 */
	public function getShop(){
		if($this->input->is_ajax_request()){
			//检查输入参数
			$data = $this->argv->checkGet(array(
				array('userId','require'),
			));
			$userId = $data['userId'];
			return $this->companyShopAo->getShop($userId);
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
