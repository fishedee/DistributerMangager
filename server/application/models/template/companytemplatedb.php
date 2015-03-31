<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CompanyTemplateDb extends CI_Model 
{
	var $tableName = "t_company_template";

	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "title" || $key == "remark")
				$this->db->like($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if( $key == "title" || $key == "remark")
				$this->db->like($key,$value);
		}
			
		$this->db->order_by('createTime','desc');
		
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);

		$query = $this->db->get($this->tableName)->result_array();
		return array(
			"count"=>$count,
			"data"=>$query
		);
	}

	public function get($companyTemplateId){
		$this->db->where("companyTemplateId",$companyTemplateId);
		$query = $this->db->get($this->tableName)->result_array();
		if( count($query) == 0 )
			throw new CI_MyException(1,'不存在此公司模板');
		return $query[0];
	}

	public function del( $companyTemplateId ){
		$this->db->where("companyTemplateId",$companyTemplateId);
		$this->db->delete($this->tableName);
	}
	
	public function add( $data ){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	public function mod( $companyTemplateId , $data )
	{
		$this->db->where("companyTemplateId",$companyTemplateId);
		$this->db->update($this->tableName,$data);
	}

}
