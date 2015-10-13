<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ScoreDb extends CI_Model {

	private $tableName = 't_score_log';

	public function __construct(){
		parent::__construct();
	}

	//判断今日是否签到
	public function checkInToday($clientId,$event){
		$nowTime = date('Y-m-d',time());
		$this->db->where('event',$event);
		$this->db->where('clientId',$clientId);
		$this->db->like('createTime',$nowTime,'botn');
		$this->db->select('scoreId');
		return $this->db->get($this->tableName)->result_array();
	}

	//判断今日分享页面             
	public function checkEnjoyShareToday($clientId,$url,$event){
		$nowTime = date('Y-m-d',time());
		$this->db->where('event',$event);
		$this->db->where('clientId',$clientId);
		$this->db->where('enjoyUrl',$url);
		$this->db->like('createTime',$nowTime,'both');
		$this->db->select('scoreId');
		return $this->db->get($this->tableName)->result_array();
	}

	//判断今日分享给朋友页面
	public function checkEnjoyFriendToday($clientId,$url,$event){
		$nowTime = date('Y-m-d',time());
		$this->db->where('event',$event);
		$this->db->where('clientId',$clientId);
		$this->db->where('enjoyUrl',$url);
		$this->db->like('createTime',$nowTime,'both');
		$this->db->select('scoreId');
		return $this->db->get($this->tableName)->result_array();
	}

	//签到
	public function checkIn($data){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	//获取积分日志
	public function getLog($clientId){
		$this->db->where('clientId',$clientId);
		return $this->db->get($this->tableName)->result_array();
	}
}
