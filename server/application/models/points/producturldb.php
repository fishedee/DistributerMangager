<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ProductUrlDb extends CI_Model{

	private $tableName = 't_points_product_url';

    public function __construct(){
        parent::__construct();
    }

    public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "state" )
				$this->db->where($key,$value);
			else if( $key == "userId" )
				$this->db->where($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if( $key == "state" )
				$this->db->where($key,$value);
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

	public function getUrlInfo($userId,$urlId){
		$this->db->where('urlId',$urlId);
		return $this->db->get($this->tableName)->result_array();
	}

	public function checkUserId($userId){
		$this->db->where('userId',$userId);
		return $this->db->get($this->tableName)->result_array();
	}

	public function add($data){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	public function mod($urlId,$data){
		$this->db->where('urlId',$urlId);
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}
}