<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PollDb extends CI_Model {

	private $tableName = 't_poll';

	public function __construct(){
		parent::__construct();
	}

	//检测是否已经报名
	public function checkPoll($userId,$clientId,$openId){
		$this->db->where('userId',$userId);
		$this->db->where('clientId',$clientId);
		$this->db->where('openId',$openId);
		$this->db->select('pollId');
		return $this->db->get($this->tableName)->result_array();
	}

	//报名
	public function add($data){
		$data['createTime'] = date('Y-m-d H:i:s',time());
		$data['modifyTime'] = date('Y-m-d H:i:s',time());
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	public function getDetail($pollId){
		$this->db->where('pollId',$pollId);
		return $this->db->get($this->tableName)->result_array();
	}

	//获取报名人数
	public function getPoll($userId){
		$sql = "SELECT a.*,b.nickName,b.headImgUrl FROM t_poll_list AS a INNER JOIN t_client AS b ON a.clientId = b.clientId WHERE a.userId={$userId} ORDER BY a.num DESC";
		return $this->db->query($sql)->result_array();
	}

	public function mod($pollId,$data){
		$this->db->where('pollId',$pollId);
		$data['modifyTime'] = date('Y-m-d H:i:s',time());
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}
}
