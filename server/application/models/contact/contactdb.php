<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ContactDb extends CI_Model 
{
	var $tableName = "t_user_company_contact";

	public function __construct(){
		parent::__construct();
	}

	public function get($userId){
		$this->db->where("userId",$userId);
		$query = $this->db->get($this->tableName)->result_array();
		if (empty($query))  {
			return '';
		}
		if( count($query) == 0 )
			throw new CI_MyException('不存在此信息');
		return $query[0];
	}

	public function del( $userId ){
		$this->db->where("userId",$userId);
		$this->db->delete($this->tableName);
	}
	
	public function add( $data ){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	public function mod( $userId , $data )
	{
		$this->db->where("userId",$userId);
		$this->db->update($this->tableName,$data);
	}

}
