<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class QuestionDb extends CI_Model 
{
	var $tableName = "t_question";

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


	//提交申请
	public function add($data){
		$data['createTime'] = date('Y-m-d H:i:s',time());
		$data['modifyTime'] = date('Y-m-d H:i:s',time());
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	public function get($questionId){
		$this->db->where('questionId',$questionId);
		return $this->db->get($this->tableName)->row_array();
	}

	public function mod($questionId,$data){
		$data['modifyTime'] = date('Y-m-d H:i:s',time());
		$this->db->where('questionId',$questionId);
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}

	public function del($questionId){
		$this->db->where('questionId',$questionId);
		$this->db->delete($this->tableName);
		return $this->db->affected_rows();
	}

	public function getQuestion($userId){
		$this->db->where('userId',$userId);
		return $this->db->get($this->tableName)->result_array();
	}
}
