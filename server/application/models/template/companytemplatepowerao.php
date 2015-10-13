<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CompanyTemplatePowerAo extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->model('user/userAo','userAo');
		$this->load->model('template/companyTemplatePowerDb','companyTemplatePowerDb');
	}

	//获取用户模板权限信息
	public function getAll($companyTemplateId,$dataLimit){
		$info     = $this->userAo->search(array(),$dataLimit);
		$userInfo = $info['data'];
		foreach ($userInfo as $key => $value) {
			$data = $this->checkPower($companyTemplateId,$value['userId']);
			if(count($data)){
				$userInfo[$key]['check'] = "<input type='checkbox' userId='".$value['userId']."' name='power[]' checked='true'/>";
			}else{
				$userInfo[$key]['check'] = "<input type='checkbox' userId='".$value['userId']."' name='power[]'/>";
			}
		}
		$info['data'] = $userInfo;
		return $info;
	}

	//获取有无权限
	public function checkPower($companyTemplateId,$userId){
		return $this->companyTemplatePowerDb->checkPower($companyTemplateId,$userId);
	}

	//更改权限
	public function changePower($userId,$companyTemplateId,$status){
		if($status == 1){
			//增加权限
			return $this->companyTemplatePowerDb->addPower($userId,$companyTemplateId);
		}else{
			//删除权限
			return $this->companyTemplatePowerDb->delPower($userId,$companyTemplateId);
		}
	}

	//更具用户获取可选模板
	public function getTemplate($userId){
		return $this->companyTemplatePowerDb->getTemplate($userId);
	}

	public function del($companyTemplateId){
		$this->companyTemplatePowerDb->del($companyTemplateId);
	}
}
