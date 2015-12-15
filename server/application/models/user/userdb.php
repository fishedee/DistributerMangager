<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserDb extends CI_Model 
{
	var $tableName = "t_user";

	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "name" || $key == "company" || $key == "phone")
				$this->db->like($key,$value);
			else if( $key == "type" )
				$this->db->where($key,$value);
			else if( $key == "userId" )
				$this->db->where_in($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if( $key == "name" || $key == "company" || $key == "phone")
				$this->db->like($key,$value);
			else if( $key == "type" )
				$this->db->where($key,$value);
			else if( $key == "userId" )
				$this->db->where_in($key,$value);
		}
			
		$this->db->order_by('createTime','desc');
		
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);

		$query = $this->db->get($this->tableName)->result_array();
		//过滤显示密码
		foreach ($query as $k=>$v){
			unset($query[$k]['password']);
		}
		return array(
			"count"=>$count,
			"data"=>$query
		);
	}

	public function get($userId){
		$this->db->where("userId",$userId);
		$query = $this->db->get($this->tableName)->result_array();
		if( count($query) == 0 )
			throw new CI_MyException(1,'不存在此用户');
		return $query[0];
	}
	
	public function getByIds($userId){
		if( count($userId) == 0 )
			return array();
		$this->db->where_in("userId",$userId);
		return $this->db->get($this->tableName)->result_array();
	}

	public function del( $userId ){
		$this->db->where("userId",$userId);
		$this->db->delete($this->tableName);
	}
	
	public function add( $data ){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	public function mod( $userId , $data )
	{
		$this->db->where("userId",$userId);
		$this->db->update($this->tableName,$data);
	}

	public function getByName($name){
		$this->db->where("name",$name);
		return $this->db->get($this->tableName)->result_array();
	}

	/**
	 * @author:zzh
	 * 2015.8.5
	 */

	//判断openid有无绑定用户名密码
	public function searchOpenId($openId){
		$this->db->where('openId',$openId);
		$result = $this->db->get($this->tableName)->result_array();
		if($result){
			return $result[0]['userId'];
		}else{
			return FALSE;
		}
	}

	//绑定openId 和 userId
	public function bind($userId,$openId){
		$this->db->where('userId',$userId);
		$this->db->update($this->tableName,array('openId'=>$openId));
		if($this->db->affected_rows()){
			return $userId;
		}else{
			throw new CI_MyException(1,'绑定失败');
		}
	}

	//解绑
	public function unBind($userId){
		$this->db->where('userId',$userId);
		$this->db->update($this->tableName,array('openId'=>NULL));
		if($this->db->affected_rows()){
			return $userId;
		}else{
			throw new CI_MyException(1,'解绑失败');
		}
	}

	// 检测登陆信息
	public function checkLoginInfo($username,$hasPassword,$password){
		$condition['name'] = $username;
		$this->db->where($condition);
		$userInfo = $this->db->get($this->tableName)->result_array();
		if(!$userInfo){
			throw new CI_MyException(1,'不存在此用户');
		}
		if(password_verify($password,$userInfo[0]['password']) == FALSE){
			throw new CI_MyException(1,'账号或密码错误');
		}else{
			return $userInfo[0]['userId'];
		}
	}

	//检测电子邮箱
	public function checkEmail($email){
		$this->db->where('email',$email);
		$userInfo = $this->db->get($this->tableName)->result_array();
		return $userInfo;
	}

	//检测userId 跟 clientId 的绑定
	public function checkClientId($userId,$clientId){
		$this->db->where('clientId',$clientId);
		$this->db->select('userId');
		return $this->db->get($this->tableName)->result_array();
	}

	//获取用户名
	public function getUserName($userId){
		$this->db->where('userId',$userId);
		$this->db->select('name');
		return $this->db->get($this->tableName)->result_array();
	}

	public function modInfo($userId,$data){
		$this->db->where('userId',$userId);
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}

	//我的二维码
	public function myQrCode($clientId){
		$condition['clientId'] = $clientId;
		$result = $this->db->select('userId,qrcode,qrcodeCreateTime,qrcodeLimit,qrcodeLimitTime')->from($this->tableName)->where($condition)->get()->row_array();
		return $result;
	}

	//获取我的二维码
	public function getMyQrCode($userId){
		$this->db->select('qrcode');
		$this->db->select('clientId');
		$this->db->where('userId',$userId);
		return $this->db->get($this->tableName)->result_array();
	}

	//根据clientId查询userId
	public function checkUserClientId($clientId){
		$this->db->where('clientId',$clientId);
		$this->db->select('userId');
		return $this->db->get($this->tableName)->result_array();
	}

	//获取clientId
	public function getClientIdFromUser($userId){
		$this->db->where('userId',$userId);
		$this->db->select('clientId');
		return $this->db->get($this->tableName)->result_array();
	}

	//补全用户信息
	public function complete($userId,$data){
		$this->db->where('userId',$userId);
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}
}
