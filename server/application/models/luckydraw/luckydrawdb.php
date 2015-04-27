<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LuckyDrawDb extends CI_Model 
{
	var $tableName = "t_lucky_draw";
	var $field = array(
		'userId'=>'',
		'title'=>'',
		'summary'=>'',
		'state'=>1,
		'beginTime'=>'',
		'endTime'=>''
	);
	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "title" || $key == "summay")
				$this->db->like($key,$value);
			else if( $key == "userId" || $key == "state")
				$this->db->where($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if( $key == "title" || $key == "summay")
				$this->db->like($key,$value);
			else if( $key == "userId" || $key == "state")
				$this->db->where($key,$value);
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

	public function get($luckyDrawId){
		$this->db->where("luckyDrawId",$luckyDrawId);
		$query = $this->db->get($this->tableName)->result_array();
		if( count($query) == 0 )
			throw new CI_MyException(1,'不存在此抽奖');
		return $query[0];
	}

	public function del( $luckyDrawId ){
		$this->db->where("luckyDrawId",$luckyDrawId);
		$this->db->delete($this->tableName);
	}
	
	public function add( $data ){

		$data = array_intersect_key($data,$this->field);
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	public function mod( $luckyDrawId , $data )
	{
		$data = array_intersect_key($data,$this->field);
		$this->db->where("luckyDrawId",$luckyDrawId);
		$this->db->update($this->tableName,$data);
	}

}
