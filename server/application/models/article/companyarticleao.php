<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CompanyArticleAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('article/companyArticleDb','companyArticleDb');
	}
	
	public function search($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		return $this->companyArticleDb->search($dataWhere,$dataLimit);
	}
	
	public function get($userId,$userCompanyArticleId){
		$article = $this->companyArticleDb->get($userCompanyArticleId);
		if($article['userId'] != $userId)
			throw new CI_MyException(1,'非本商城用户无此权限操作');
		
		return $article;
	}
	
	public function del($userId,$userCompanyArticleId){
		$article = $this->companyArticleDb->get($userCompanyArticleId);
		if($article['userId'] != $userId)
			throw new CI_MyException(1,'非本商城用户无此权限操作');
			
		$this->companyArticleDb->del($userCompanyArticleId);
	}
	
	public function add($userId,$data){
		$data['userId'] = $userId;
		$this->companyArticleDb->add($data);
	}
	
	public function mod($userId,$userCompanyArticleId,$data){
		$article = $this->companyArticleDb->get($userCompanyArticleId);
		if($article['userId'] != $userId)
			throw new CI_MyException(1,'非本商城用户无此权限操作');
		
		$this->companyArticleDb->mod($userCompanyArticleId,$data);
	}
}
