<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ClientAo extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->model('client/clientDb','clientDb');
		$this->load->model('user/UserAppAo', 'userAppAo');
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

	public function getClient($userId,$limit,$chips_id){
                $this->load->model('chips/chipsPowerDb','chipsPowerDb');
		$this->load->library('http');
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"])){
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);
		}
		$this->load->helper('sendget');
		$clientInfo = $this->clientDb->clientInfo($userId);
		// 获取access_token
		$this->load->model('user/userAppAo','userAppAo');
		$info = $this->userAppAo->getTokenAndTicket($userId);
		$access_token = $info['appAccessToken'];

        //根据clientInfo 获取用户基本信息
        foreach ($clientInfo as $key => $value) {
        	$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$value['openId']."&lang=zh_CN";
        	$yonghu = $this->http->ajax(array(
				'url'=>$url,
				'type'=>'post',
				'data'=>array(),
				'dataType'=>'plain',
				'responseType'=>'json'
			));
			$yonghu = $yonghu['body'];
        	if($yonghu['subscribe']){
        		$clientInfo[$key]['nickname'] 	= $yonghu['nickname'];
        		$clientInfo[$key]['headimgurl'] = $yonghu['headimgurl'];
        	}else{
        		$clientInfo[$key]['nickname'] 	= '用户没关注';
        		$clientInfo[$key]['headimgurl'] = 'null';
        	}
        	$condition['clientId'] = $value['clientId'];
        	$condition['chips_id'] = $chips_id;
        	$power_result = $this->chipsPowerDb->powerResult($condition);
        	if(count($power_result)){
        		$clientInfo[$key]['check'] = "<input type='checkbox' clientId='".$value['clientId']."' name='power[]' checked='true'/>";
        	}else{
        		$clientInfo[$key]['check'] = "<input type='checkbox' clientId='".$value['clientId']."' name='power[]'/>";
        	}
        }
        $count = $this->clientDb->clientCount($userId);
        return array(
        	'count' => $count,
        	'data'  => $clientInfo
        	);
	}

}
