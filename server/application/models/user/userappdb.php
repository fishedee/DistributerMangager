<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserAppDb extends CI_Model 
{
	var $tableName = "t_user_app";
	var $tableField = array(
		'userId'=>0,
		'appBg' => '',
		'appLogo'=> '',
		'appName'=>'',
		'weixinNum'=>'',
		'appId'=>'',
		'appKey'=>'',
		'mchId'=>'',
		'mchKey'=>'',
		'mchSslCert'=>'',
		'mchSslKey'=>'',
		'appAccessToken'=>'',
		'appAccessTokenExpire'=>'',
		'appJsApiTicket'=>'',
		'appJsApiTicketExpire'=>'',
		'remark'=>''
	);
	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "remark" )
				$this->db->like($key,$value);
			else if( $key == "userId" || $key == 'appId' || $key == 'mchId' || $key == 'weixinNum')
				$this->db->where($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if( $key == "remark" )
				$this->db->like($key,$value);
			else if( $key == "userId" || $key == 'appId' || $key == 'mchId' || $key == 'weixinNum')
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

	public function getByUser($userId){
		$this->db->where("userId",$userId);
		$query = $this->db->get($this->tableName)->result_array();
		return $query;
	}
	
	public function add($data){
		$data = array_merge($this->tableField,array_intersect_key($data,$this->tableField));
		return $this->db->insert($this->tableName,$data);
	}

	public function modByUser( $userId , $data ){
		$data = array_intersect_key($data,$this->tableField);
		if( count($data) == 0 )
			return;
		$this->db->where("userId",$userId);
		$this->db->update($this->tableName,$data);
	}

	public function getTicket($userId){
		return $this->db->select('cardTicket,cardTicketExpire')->from($this->tableName)->where('userId',$userId)->get()->result_array();
	}

	public function updateTicket($userId,$data){
		$this->db->where('userId',$userId);
		$this->db->update($this->tableName,$data);
	}

	//根据微信号获取userId
	public function getUserId($ToUserName){
		$userAppInfo = $this->db->select('userId')->from($this->tableName)->where('weixinNum',$ToUserName)->get()->result_array();
		return $userAppInfo[0]['userId'];
	}

}
