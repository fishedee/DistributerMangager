<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class QuestionCollectDb extends CI_Model 
{
	var $tableName = "t_question_collect";

	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "question" )
				$this->db->like($key,$value);
			else if( $key == "userId" )
				$this->db->where($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if( $key == "question" )
				$this->db->like($key,$value);
			else if( $key == "userId" )
				$this->db->where($key,$value);
		}
		
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);

		$query = $this->db->get($this->tableName)->result_array();
		return array(
			"count"=>$count,
			"data"=>$query
		);
	}

	public function add($data){
		$data['createTime'] = date('Y-m-d H:i:s',time());
		$data['modifyTime'] = date('Y-m-d H:i:s',time());
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	public function getCollect($collectId){
		$this->db->where('collectId',$collectId);
		return $this->db->get($this->tableName)->row_array();
	}

	public function mod($collectId,$data){
		$data['modifyTime'] = date('Y-m-d H:i:s',time());
		$this->db->where('collectId',$collectId);
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}

	public function checkCollect($clientId){
		$this->db->where('clientId',$clientId);
		return $this->db->get($this->tableName)->result_array();
	}
}
