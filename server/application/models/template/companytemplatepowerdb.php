<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CompanyTemplatePowerDb extends CI_Model {

	private $tableName = 't_company_template_power';

	public function __construct(){
		parent::__construct();
	}

	//检查是否有权限
	public function checkPower($companyTemplateId,$userId){
		$this->db->where('companyTemplateId',$companyTemplateId);
		$this->db->where('userId',$userId);
		$condition['companyTemplateId'] = $companyTemplateId;
		$condition['userId'] = $userId;
		return $this->db->select('powerId')->from($this->tableName)->where($condition)->get()->result_array();
	}

	//增加权限
	public function addPower($userId,$companyTemplateId){
		$data['userId'] = $userId;
		$data['companyTemplateId'] = $companyTemplateId;
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	//删除权限
	public function delPower($userId,$companyTemplateId){
		$this->db->where('userId',$userId);
		$this->db->where('companyTemplateId',$companyTemplateId);
		$this->db->delete($this->tableName);
		return $this->db->affected_rows();
	}

	//更具用户获取可选模板
	public function getTemplate($userId){
		$result = $this->db->select('companyTemplateId')->from($this->tableName)->where('userId',$userId)->get()->result_array();
		return $result;
	}

	public function del($companyTemplateId){
		$this->db->where('companyTemplateId',$companyTemplateId);
		$this->db->delete($this->tableName);
	}
}
