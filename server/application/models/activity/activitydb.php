<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ActivityDb extends CI_Model {

	private $tableName = 't_activity';

	public function __construct(){
		parent::__construct();
	}

	//查看报名
	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if($key == 'userId'){
				$this->db->where($key,$value);
			}else{
				$this->db->like($key,$value,'both');
			}
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if($key == 'userId'){
				$this->db->where($key,$value);
			}else{
				$this->db->like($key,$value,'both');
			}
		}
			
		$this->db->order_by('createTime','DESC');
		
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);

		$query = $this->db->get($this->tableName)->result_array();
		return array(
			"count"=>$count,
			"data"=>$query
		);
	}

	//报名
	public function enList($data){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	//检测报名资格
	public function checkEnList($userId,$clientId){
		$this->db->where('userId',$userId);
		$this->db->where('clientId',$clientId);
		return $this->db->get($this->tableName)->result_array();
	}

	//获取报名信息
	public function enListed($userId){
		$this->db->where('userId',$userId);
		$this->db->select('name');
		$this->db->select('phone');
		$this->db->order_by('activityId','DESC');
		$this->db->limit(20,0);
		return $this->db->get($this->tableName)->result_array();
	}
}
