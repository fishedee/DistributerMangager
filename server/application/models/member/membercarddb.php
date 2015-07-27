<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MemberCardDb extends CI_Model {

	private $tableName = 't_member_card';

	public function __construct(){
		parent::__construct();
	}

	public function addMember($userId,$card_id,$title,$num){
		$data['userId'] = $userId;
		$data['card_id']= $card_id;
		$data['title']  = $title;
		$data['num']    = $num;
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	public function apiUpdateMemberCard($userId,$card_id,$num){
		$this->db->where('userId',$userId);
		$this->db->where('card_id',$card_id);
		$data['num']    = $num;
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}

	public function getMemberCard($userId,$limit,$dataWhere){
		$this->db->where('userId',$userId);
		$cardInfo = $this->db->get($this->tableName)->result_array();
		$count = count($cardInfo);
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"])){
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);
		}
		if($dataWhere){
			$this->db->like('title',$dataWhere['title'],'both');
		}
		$cardInfo = $this->db->get($this->tableName)->result_array();
		return array(
			'count'=>$count,
			'data' =>$cardInfo
			);
	}

	//更新会员卡数据
	public function updateMemberCard($data){
		foreach ($data as $key => $value) {
			$this->db->where('memberCardId',$value['memberId']);
			unset($value['memberId']);
			$this->db->update($this->tableName,$value);
		}
	}

	//设置默认会员卡
	public function defaultCard($userId,$card_id){
		//将其他defaul 设为0
		$this->db->where('userId',$userId);
		$data['defaultCard'] = 0;
		$this->db->update($this->tableName,$data);
		$condition['userId'] = $userId;
		$condition['card_id']= $card_id;
		$this->db->where($condition);
		$this->db->update($this->tableName,array('defaultCard'=>1));
		return $this->db->affected_rows();
	}

	//获取默认信息
	public function getDefaultCard($userId){
		$condition['userId'] = $userId;
		$condition['defaultCard'] = 1;
		$result = $this->db->select('card_id')->from($this->tableName)->where($condition)->get()->result_array();
		return $result[0]['card_id'];
	}

	public function testAdd(){
		$data['code'] = 1;
		$this->db->insert('t_member',$data);
	}
}
