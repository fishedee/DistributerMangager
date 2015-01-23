<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserCompanyTemplateDb extends CI_Model 
{
	var $tableName = "t_user_company_template";

	public function __construct(){
		parent::__construct();
	}

	public function getByUserId($userId){
		$this->db->where("userId",$userId);
		return $this->db->get($this->tableName)->result_array();
	}

	public function delByUserId( $userId ){
		$this->db->where("userId",$userId);
		return $this->db->delete($this->tableName);
	}
	
	public function add( $data ){
		$this->db->insert($this->tableName,$data);
	}

}
