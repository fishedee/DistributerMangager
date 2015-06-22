<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserCompanyTemplateDb extends CI_Model 
{
	var $tableName = "t_user_company_template";

	public function __construct(){
		parent::__construct();
	}

	public function getByUserIdAndType($userId,$type){
		$this->db->where("userId",$userId);
		$this->db->where("type",$type);
		return $this->db->get($this->tableName)->result_array();
	}

	public function delByUserIdAndType( $userId,$type){
		$this->db->where("userId",$userId);
		$this->db->where("type",$type);
		return $this->db->delete($this->tableName);
	}

	public function delByCompanyTemplateId($companyTemplateId){
		$this->db->where("companyTemplateId",$companyTemplateId);
		return $this->db->delete($this->tableName);
	}
	
	public function add( $data ){
		$this->db->insert($this->tableName,$data);
	}

}
