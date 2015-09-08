<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TemplatePower extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userTypeEnum','userTypeEnum');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('template/companyTemplateTypeEnum','companyTemplateTypeEnum');
		$this->load->library('argv','argv');
		$this->load->model('template/companyTemplatePowerAo','companyTemplatePowerAo');
    }

	/**
	 * @view json
	 *
	 */
	public function getAll(){
		//检查输入参数
		$data = $this->argv->checkGet(array(
			array('companyTemplateId','require'),
		));

		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','require'),
			array('pageSize','require'),
		));

		$companyTemplateId = $data['companyTemplateId'];
		
		//检查权限
		$user = $this->loginAo->checkMustLogin();

		return $this->companyTemplatePowerAo->getAll($companyTemplateId,$dataLimit);
	}

	/**
	 * @view json
	 * 更改权限
	 */
	public function changePower(){
		if($this->input->is_ajax_request()){
			//检查权限
			$user = $this->loginAo->checkMustLogin();
			$userId = $this->input->post('userId');
			$companyTemplateId = $this->input->post('companyTemplateId');
			$status = $this->input->post('status');
			return $this->companyTemplatePowerAo->changePower($userId,$companyTemplateId,$status);
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
