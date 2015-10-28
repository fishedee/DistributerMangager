<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LuckyDrawClientDb extends CI_Model 
{
	var $tableName = "t_lucky_draw_client";

	public function __construct(){
		parent::__construct();
	}

	public function getByLuckyDrawId($luckyDrawId,$limit){
		$this->db->where("luckyDrawId",$luckyDrawId);
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);
		$query = $this->db->get($this->tableName)->result_array();
		return array(
			'count'=>count($query),
			'data' =>$query
			);
	}

	public function getByLuckyDrawAndClientId($luckyDrawId,$clientId){
		$this->db->where("luckyDrawId",$luckyDrawId);
		$this->db->where("clientId",$clientId);
		return $this->db->get($this->tableName)->result_array();
	}

	public function getByClientId($clientId){
		$this->db->where("clientId",$clientId);
		return $this->db->get($this->tableName)->result_array();
	}
	
	public function add( $data ){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	//注销
	public function withDraw($list_id){
		$this->db->where('luckyDrawClientId',$list_id);
		$data['status'] = 0;
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}

	//判断合理性
	public function judge($list_id){
		$statusInfo = $this->db->select('status')->from($this->tableName)->where('luckyDrawClientId',$list_id)->get()->result_array();
		return $statusInfo[0]['status'];
	}


	//统计抽奖次数
	public function drawCount($luckyDrawId){
		$this->db->where('luckyDrawId',$luckyDrawId);
		return $this->db->get($this->tableName)->num_rows();
	}


	//修改用户信息
	public function modUserInfo($luckyDrawId,$data,$clientId){
		$this->db->where("luckyDrawId",$luckyDrawId);
		$this->db->where("clientId",$clientId);
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}

}
