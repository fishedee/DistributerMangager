<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ClientDb extends CI_Model 
{
	var $tableName = "t_client";

	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if(  $key == 'openId'|| $key == 'type' || $key == 'userId')
				$this->db->where($key,$value);
			else if( $key == 'clientId')
				$this->db->where_in($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if(  $key == 'openId'|| $key == 'type' || $key == 'userId')
				$this->db->where($key,$value);
			else if( $key == 'clientId')
				$this->db->where_in($key,$value);
		}
			
		$this->db->order_by('createTime','desc');
		
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);

		$query = $this->db->get($this->tableName);
		return array(
			"count"=>$count,
			"data"=>$query->result_array()
		);
	}

	public function get($clientId){
		$this->db->where("clientId",$clientId);
		$query = $this->db->get($this->tableName)->result_array();
		if( count($query) == 0 )
			throw new CI_MyException(1,'找不到此用户');
		return $query[0];
	}

	public function add( $data ){
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	public function mod( $clientId , $data ){
		$this->db->where("clientId",$clientId);
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}

	public function clientInfo($userId){
		return $this->db->select('clientId,openId')->from($this->tableName)->where('userId',$userId)->get()->result_array();
	}

	public function clientCount($userId){
		return $this->db->where('userId',$userId)->count_all_results($this->tableName);
	}

	public function refreshUserInfo($clientId,$data){
		$this->db->where('clientId',$clientId);
		$this->db->update($this->tableName,$data);
	}

	public function getClient($dataWhere,$limit,$chips_id){
		$this->load->model('chips/chipsPowerDb','chipsPowerDb');
		$count = $this->db->where($dataWhere)->count_all_results($this->tableName);
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"])){
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);
		}
		if(count($dataWhere) > 1){
			$this->db->where('userId',$dataWhere['userId']);
			$this->db->like('nickName',$dataWhere['nickName'],'both');
		}else{
			$this->db->where('userId',$dataWhere['userId']);
		}
		$clientInfo = $this->db->get($this->tableName)->result_array();
		foreach ($clientInfo as $key => $value) {
			$condition['clientId'] = $value['clientId'];
        	$condition['chips_id'] = $chips_id;
        	$power_result = $this->chipsPowerDb->powerResult($condition);
        	if(count($power_result)){
        		$clientInfo[$key]['check'] = "<input type='checkbox' clientId='".$value['clientId']."' name='power[]' checked='true'/>";
        	}else{
        		$clientInfo[$key]['check'] = "<input type='checkbox' clientId='".$value['clientId']."' name='power[]'/>";
        	}
		}
		return array(
			'count' => $count,
			'data'  => $clientInfo
		);
	}

	public function getUserInfo($dataWhere,$limit){
		$this->load->model('chips/chipsPowerDb','chipsPowerDb');
		$count = $this->db->where($dataWhere)->count_all_results($this->tableName);
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"])){
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);
		}
		if(count($dataWhere) > 1){
			$this->db->where('userId',$dataWhere['userId']);
			$this->db->like('nickName',$dataWhere['nickName'],'both');
		}else{
			$this->db->where('userId',$dataWhere['userId']);
		}
		$clientInfo = $this->db->get($this->tableName)->result_array();
		return array(
			'count' => $count,
			'data'  => $clientInfo
			);
	}

	//根据openid获取clientid
	public function getClientId($openId){
		$openIdInfo = $this->db->select('clientId')->from($this->tableName)->where('openId',$openId)->get()->result_array();
		if($openIdInfo){
			return $openIdInfo[0]['clientId'];
		}else{
			return false;
		}
	}

	public function scanInfo($clientId){

		//扫面时效性写入缓存
		$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
		$scan = 1;
		$this->cache->save($clientId, $scan, 30);
	}

	public function scan($userId,$clientId,$boardNum){
		$this->db->where('clientId',$clientId);
		$clientInfo = $this->get($clientId);
		if($clientInfo['scan'] == 1){
			//判断时间是否过期
			if(strtotime($clientInfo['scanTime']) + 30 > time()){
				$this->db->update($this->tableName,array('scanTime'=>date('Y-m-d H:i:s',time())));
			}
		}else{
			$this->db->update($this->tableName,array('scan'=>1,'scanTime'=>date('Y-m-d H:i:s',time())));
		}
		$url = 'http://'.$_SERVER['HTTP_HOST'].'/'.$userId.'/boarda.html?boardNum='.$boardNum . '&userId=' . $userId;

		header('Location:'.$url);
	}

	//积分排行榜
	public function rankingList($userId,$select=array()){
		if($select){
			foreach ($select as $key => $value) {
				$this->db->select($value);
			}
		}
		// var_dump($userId);die;
		$this->db->where('userId',$userId);
		$this->db->where('score>',0);
		$this->db->where('subscribe',1);
		$this->db->limit(50);
		$this->db->order_by('score','desc');
		return $this->db->get($this->tableName)->result_array();
		// $sql = "SELECT * FROM t_ranklist WHERE userId={$userId} AND score>0 AND subscribe=1 ORDER BY score DESC LIMIT 50";
		// return $this->db->query($sql)->result_array();
	}

	public function ref($userId){
		$this->db->where('userId',$userId);
		return $this->db->get($this->tableName)->result_array();
	}
}
