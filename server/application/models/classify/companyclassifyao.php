<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CompanyClassifyAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('classify/companyClassifyDb','companyClassifyDb');
		$this->load->model('article/companyArticleWhen','companyArticleWhen');
	}
	
	public function search($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		return $this->companyClassifyDb->search($dataWhere,$dataLimit);
	}
	
	public function get($userId,$userCompanyClassifyId){
		$classify = $this->companyClassifyDb->get($userCompanyClassifyId);
		if($classify['userId'] != $userId)
			throw new CI_MyException(1,'非本商城用户无此权限操作');
		
		return $classify;
	}
	
	public function del($userId,$userCompanyClassifyId){
		$classify = $this->companyClassifyDb->get($userCompanyClassifyId);
		if($classify['userId'] != $userId)
			throw new CI_MyException(1,'非本商城用户无此权限操作');
			
		$this->companyClassifyDb->del($userCompanyClassifyId);
		
		//通知文章挂载的相关分类被删除了
		$this->companyArticleWhen->whenClassifyDelete($userCompanyClassifyId);
	}
	
	public function add($userId,$data){
		$data['userId'] = $userId;
		$this->companyClassifyDb->add($data);
	}
	
	public function mod($userId,$userCompanyClassifyId,$data){
		$classify = $this->companyClassifyDb->get($userCompanyClassifyId);
		if($classify['userId'] != $userId)
			throw new CI_MyException(1,'非本商城用户无此权限操作');
		
		$this->companyClassifyDb->mod($userCompanyClassifyId,$data);
	}
}
