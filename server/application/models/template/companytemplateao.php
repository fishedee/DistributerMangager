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
	
	public function getByUserIdAndType($userId,$type){
		$data = $this->userCompanyTemplateDb->getByUserIdAndType($userId,$type);
		if( count($data) == 0 )
			return 0;
		return $data[0]['companyTemplateId'];
	}
	
	public function modByUserIdAndType($userId,$type,$companyTemplateId){
		$this->userCompanyTemplateDb->delByUserIdAndType($userId,$type);
		if( $companyTemplateId == 0 )
			return;
		$this->userCompanyTemplateDb->add(array(
			'userId'=>$userId,
			'type'=>$type,
			'companyTemplateId'=>$companyTemplateId
		));
	}
	
	public function get($companyTemplateId){
		return $this->companyTemplateDb->get($companyTemplateId);
	}
	
	public function del($companyTemplateId){
		$this->companyTemplateDb->del($companyTemplateId);
		$this->userCompanyTemplateDb->delByCompanyTemplateId($companyTemplateId);
	}
	
	public function add($data){
		$this->companyTemplateDb->add($data);
	}
	
	public function mod($companyTemplateId,$data){
		$this->companyTemplateDb->mod($companyTemplateId , $data);
	}
}
