<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WxTemplateDb extends CI_Model
{
	var $tableName = "t_weixin_template";
	public function __construct(){
		parent::__construct();
	}

	public function getByUserId($userId){
		$this->db->where("userId",$userId);
		$sqlData = $this->db->get($this->tableName)->result_array();
		return $sqlData[0];
	}

	public function add( $data ){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	public function modByUserId( $userId , $data ){
		$this->db->where("userId",$userId);
		$this->db->update($this->tableName,$data);
	}

}
