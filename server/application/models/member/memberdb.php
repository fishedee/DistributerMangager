<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MemberDb extends CI_Model {

	private $tableName = 't_member';

	public function __construct(){
		parent::__construct();
	}

	//增加领取会员的信息
	public function addMember($userId,$UserCardCode,$openid,$CreateTime,$CardId){
		$data['userId'] = $userId;
		$data['UserCardCode'] = $UserCardCode;
		$data['openId'] = $openid;
		$data['CreateTime'] = $CreateTime;
		$data['card_id'] = $CardId;
		$this->db->insert($this->tableName,$data);
	}

	//查询会员信息
	public function getMemberInfo($openId){
		$this->db->where('openId',$openId);
		$memberInfo = $this->db->get($this->tableName)->result_array();
		return $memberInfo[0];
	}


	//判断有无会员卡
	public function judge($userId,$openId){
		$this->load->model('member/memberCardDb','memberCardDb');
		$condition['userId'] = $userId;
		$condition['openId'] = $openId;
		$this->db->where($condition);
		$result = $this->db->get($this->tableName)->result_array();
		if(count($result)){
			return 0;
		}else{
			//无卡券
			return $this->memberCardDb->getDefaultCard($userId);
		}
	}

	//更新会员信息
	public function updateMember($data){
		$this->db->where('userCardCode',$data['code']);
		$this->db->update($this->tableName,array('bonus'=>$data['init_bonus']));
		return $this->db->affected_rows();
	}
}
