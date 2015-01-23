<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CompanyTemplateClassifyDb extends CI_Model 
{
	var $tableName = "t_company_template_classify";

	public function __construct(){
		parent::__construct();
	}

	public function getByCompanyTemplateId($companyTemplateId){
		$this->db->where("companyTemplateId",$companyTemplateId);
		return $this->db->get($this->tableName)->result_array();
	}

	public function delByCompanyTemplateId( $companyTemplateId ){
		$this->db->where("companyTemplateId",$companyTemplateId);
		return $this->db->delete($this->tableName);
	}
	
	public function addBatch( $data ){
		if( count($data) != 0 )
			$query = $this->db->insert_batch($this->tableName,$data);
	}

}
