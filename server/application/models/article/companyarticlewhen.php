<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CompanyArticleWhen extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('article/companyArticleDb','companyArticleDb');
	}
	
	public function whenClassifyDelete($userCompanyClassifyId){
		$this->companyArticleDb->modByUserCompanyClassifyId($userCompanyClassifyId,array(
			'userCompanyClassifyId'=>0
		));
	}
}
