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

		return $this->clientDb->mod($clientId,$data);
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
		if(isset($yonghu['body']['errcode'])){
			throw new CI_MyException(1,$yonghu['body']['errmsg']);
		}
		$yonghu = $yonghu['body'];
    	if($yonghu['subscribe']){
			$nickname   = base64_encode($yonghu['nickname']);
			$headimgurl = $yonghu['headimgurl'];
			$data['nickName'] = $nickname;
	    	$data['headImgUrl'] = $headimgurl;
	    	$data['subscribe']  = $yonghu['subscribe'];
    	}else{
    		$nickname 	= '用户没关注';
    		$headimgurl = 'null';
    		$data['subscribe']  = $yonghu['subscribe'];
    	}
    	$this->clientDb->refreshUserInfo($clientId,$data);
	}

	public function getClientId($openId){
		return $this->clientDb->getClientId($openId);
	}

	public function getClientAndAdd($userId,$openId){
		$this->load->model('client/clientWxLoginAo','clientWxLoginAo');
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

	//获取用户信息
	public function getUserInfo($dataWhere,$dataLimit){
		$result = $this->clientDb->getUserInfo($dataWhere,$dataLimit);
		foreach ($result['data'] as $key => $value) {
			$nickName = $value['nickName'];
			$result['data'][$key]['nickName'] = base64_decode($nickName);
		}
		return $result;
	}


	public function scan($userId,$clientId,$boardNum){
		return $this->clientDb->scan($userId,$clientId,$boardNum);
	}

	public function scanInfo($ToUserName,$openId){
        $userId = $this->userAppAo->getUserId($ToUserName);
        $data['openId'] = $openId;
        $data['userId'] = $userId;
        $data['type']   = 2;
        $clientId = $this->addOnce($data);
        return $this->clientDb->scanInfo($clientId);
	}

	public function checkCache($ToUserName,$openId){
		$userId = $this->userAppAo->getUserId($ToUserName);
        $data['openId'] = $openId;
        $data['userId'] = $userId;
        $data['type']   = 2;
        $clientId = $this->addOnce($data);
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
        // return $this->cache->get('s'.$cientId);
        if($this->cache->get('s'.$cientId)){
        	return 1;
        }else{
        	return 0;
        }
	}

	//积分排行榜
	public function rankingList($userId){
		$info = $this->clientDb->rankingList($userId);
		foreach ($info as $key => $value) {
			$info[$key]['nickName'] = base64_decode($value['nickName']);
		}
		return $info;
	}

	//刷新微信用户
	public function ref($userId,$openId){
		$this->load->library('http');
		$userInfo   = $this->userAppAo->getTokenAndTicket($userId);
		$access_token = $userInfo['appAccessToken'];
		$data['userId'] = $userId;
		$data['openId'] = $openId;
		$data['type']   = 2;
		$clientId = $this->addOnce($data);
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openId."&lang=zh_CN";
    	$yonghu = $this->http->ajax(array(
			'url'=>$url,
			'type'=>'post',
			'data'=>array(),
			'dataType'=>'plain',
			'responseType'=>'json'
		));
		$yonghu = $yonghu['body'];
		$nickName   = base64_encode($yonghu['nickname']);
		$headImgUrl = $yonghu['headimgurl'];
		$data = array();
		$data['nickName'] = $nickName;
		$data['headImgUrl'] = $headImgUrl;
		$this->mod($userId,$clientId,$data);
	}

	public function tt($userId,$openId){
		$this->load->library('http');
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
		var_dump($yonghu);die;
	}
}
