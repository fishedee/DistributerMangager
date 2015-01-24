<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CompanyTemplateAo extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->model('template/companyTemplateDb','companyTemplateDb');
		$this->load->model('template/userCompanyTemplateDb','userCompanyTemplateDb');
	}
	
	public function search($dataWhere,$dataLimit){
		return $this->companyTemplateDb->search($dataWhere,$dataLimit);
	}
	
	public function getByUserId($userId){
		$data = $this->userCompanyTemplateDb->getByUserId($userId);
		if( count($data) == 0 )
			return null;
		$companyTemplateId = $data[0]['companyTemplateId'];
		return $this->companyTemplateDb->get($companyTemplateId);
	}
	
	public function modByUserId($userId,$companyTemplateId){
		$this->userCompanyTemplateDb->delByUserId($userId);
		$this->userCompanyTemplateDb->add(array(
			'userId'=>$userId,
			'companyTemplateId'=>$companyTemplateId
		));
	}
	
	public function get($companyTemplateId){
		return $this->companyTemplateDb->get($companyTemplateId);
	}
	
	public function del($companyTemplateId){
		$this->companyTemplateDb->del($companyTemplateId);
	}
	
	public function add($data){
		$this->companyTemplateDb->add($data);
	}
	
	public function mod($companyTemplateId,$data){
		$this->companyTemplateDb->mod($companyTemplateId , $data);
	}
}
