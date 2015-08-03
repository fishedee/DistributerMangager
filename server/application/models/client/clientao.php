<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ClientAo extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->model('client/clientDb','clientDb');
		$this->load->model('user/UserAppAo', 'userAppAo');
		$this->load->model('chips/chipsPowerDb','chipsPowerDb');
	}

	public function search($userId,$where,$limit){
		$this->userAppAo->checkByUserId($userId);
		
		$where['userId'] = $userId;
		return $this->clientDb->search($where,$limit);
	}

	public function get($userId,$clientId){
		$client =  $this->clientDb->get($clientId);
		if( $client['userId'] != $userId )
			throw new CI_MyException(1,'非本商城用户无权限操作');

		return $client;
	}
	
	public function mod($userId,$clientId,$data){
		$client =  $this->clientDb->get($clientId);
		if( $client['userId'] != $userId )
			throw new CI_MyException(1,'非本商城用户无权限操作');

		$this->clientDb->mod($clientId,$data);
	}
	
	public function addOnce($data){
		$clients = $this->clientDb->search(array(
			'userId'=>$data['userId'],
			'type'=>$data['type'],
			'openId'=>$data['openId']
		),array());
		if( $clients['count'] != 0 )
			return $clients['data'][0]['clientId'];
		
		return $this->clientDb->add($data);
	}

	public function getClient($dataWhere,$dataLimit,$chips_id){
		return $this->clientDb->getClient($dataWhere,$dataLimit,$chips_id);
	}

	public function refreshUserInfo($userId,$client){
		$this->load->library('http');
		$openId     = $client['openId'];
		$clientId   = $client['clientId'];
		$userInfo   = $this->userAppAo->getTokenAndTicket($userId);
		$access_token = $userInfo['appAccessToken'];
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openId."&lang=zh_CN";
    	$yonghu = $this->http->ajax(array(
			'url'=>$url,
			'type'=>'post',
			'data'=>array(),
			'dataType'=>'plain',
			'responseType'=>'json'
		));
		$yonghu = $yonghu['body'];
    	if($yonghu['subscribe']){
    		$nickname 	= $yonghu['nickname'];
    		$headimgurl = $yonghu['headimgurl'];
    	}else{
    		$nickname 	= '用户没关注';
    		$headimgurl = 'null';
    	}
    	$data['nickName'] = $nickname;
    	$data['headImgUrl'] = $headimgurl;
    	$data['subscribe']  = $yonghu['subscribe'];
    	$this->clientDb->refreshUserInfo($clientId,$data);
	}

	public function getClientId($openId){
		return $this->clientDb->getClientId($openId);
	}

	//判断有无关注
	public function judgeSub($userId,$clientId){
		$clientInfo = $this->get($userId,$clientId);
		if($clientInfo['subscribe'] == 1){
			return true;
		}else{
			return false;
		}
	}

}
