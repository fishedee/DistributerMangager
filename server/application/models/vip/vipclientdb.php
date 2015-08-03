<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class VipClientDb extends CI_Model 
{
	var $tableName = "t_vip_client";
	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "name" || $key == "phone")
				$this->db->like($key,$value);
			else if( $key == "userId" )
				$this->db->where($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if( $key == "name" || $key == "phone")
				$this->db->like($key,$value);
			else if( $key == "userId" )
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

	public function getByUserAndClient($userId,$clientId)
	{
		$this->db->where("userId",$userId);
		$this->db->where("clientId",$clientId);
		return $this->db->get($this->tableName)->result_array();
	}

	public function add( $data )
	{
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	public function modByUserAndClient($userId,$clientId,$data)
	{
		$this->db->where("userId",$userId);
		$this->db->where("clientId",$clientId);
		return $this->db->update($this->tableName,$data);
		// $data['userId'] = $userId;
		// $data['clientId'] = $clientId;
		// $this->db->insert($this->tableName,$data);
	}

	public function modCardInfo($userId,$clientId,$data){
		$this->db->where("userId",$userId);
		$this->db->where("clientId",$clientId);
		return $this->db->update($this->tableName,$data);
	}

	//增加会员
	public function addMember($userId,$UserCardCode,$clientId,$CardId){
		$condition['userId'] = $userId;
		$condition['clientId'] = $clientId;
		$data['userCardCode'] = $UserCardCode;
		$data['createTime'] = date('Y-m-d H:i:s',time());
		$data['card_id'] = $CardId;
		// $this->db->insert($this->tableName,$data);
		$this->db->where($condition);
		$this->db->update($this->tableName,$data);
	}

	//判断有无会员卡
	public function judge($userId,$clientId){
		$this->load->model('member/memberCardDb','memberCardDb');
		$condition['userId'] = $userId;
		$condition['clientId'] = $clientId;
		$result = $this->db->select('UserCardCode')->from($this->tableName)->where($condition)->get()->result_array();
		if($result[0]['UserCardCode']){
			return 0;
		}else{
			//无卡券
			return $this->memberCardDb->getDefaultCard($userId);
		}
	}

	//判断会员卡是否激活
	public function judgeActive($userId,$clientId){
		$condition['userId'] = $userId;
		$condition['clientId'] = $clientId;
		$result = $this->db->select('active')->from($this->tableName)->where($condition)->get()->result_array();
		return $result[0]['active'];
	}

	//激活会员卡
	public function activeMember($userId,$clientId,$score){
		$this->db->where('userId',$userId);
		$this->db->where('clientId',$clientId);
		$data['score'] = $score;
		$data['active']= 1;
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}

	//判断有无填写手机和姓名
	public function judgeMobilName($userId,$clientId){
		// $condition['userId'] = $userId;
		// $condition['clientId'] = $clientId;
		$result = $this->db->select('name,phone')->from($this->tableName)->where(array('clientId'=>$clientId,'userId'=>$userId))->get()->result_array();
		return $result;
	}

}
