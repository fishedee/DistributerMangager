<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionConfigDb extends CI_Model {

	private $tableName = 't_distribution_config';

	public function __construct(){
		parent::__construct();
	}

	//获取分销配置
	public function getConfig($userId){
		$this->db->where('userId',$userId);
		return $this->db->get($this->tableName)->result_array();
	}

	//更新
	public function mod($userId,$data){
		$this->db->where('userId',$userId);
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}

	//新增
	public function add($data){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}
}
