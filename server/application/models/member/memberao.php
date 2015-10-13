<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MemberAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->library('http');
		$this->load->library('argv', 'argv');
		$this->load->model('user/userAppAo','userAppAo');
		$this->load->model('member/memberDb','memberDb');
		$this->load->model('client/clientAo','clientAo');
	}

	//添加会员信息
	public function addMember($UserCardCode,$openid,$CreateTime,$ToUserName,$CardId){
		$userId = $this->userAppAo->getUserId($ToUserName);
		$this->memberDb->addMember($userId,$UserCardCode,$openid,$CreateTime,$CardId);
	}

	//激活会员
	public function activeMember($userId,$clientId){
		$this->load->model('vip/vipAo','vipAo');
		$active = $this->vipAo->activeMember($userId,$clientId);
		if($active){
			$url = 'http://'.$userId.'.'.$_SERVER['HTTP_HOST'].'/'.$userId.'/vip.html';
			echo '<script>window.location.href='.$url.'</script>';
		}

		// $clientInfo = $this->clientAo->get($userId,$clientId);
		// $openId = $clientInfo['openId'];
		// //获取会员信息
		// $memberInfo = $this->memberDb->getMemberInfo($openId);
		// //获取token
		// $info = $this->userAppAo->getTokenAndTicket($userId);
		// $access_token = $info['appAccessToken'];
		// //开始激活
		// $url = 'https://api.weixin.qq.com/card/membercard/activate?access_token='.$access_token;
		// $data['init_bonus'] = 100; //初始化积分
		// $data['membership_number'] = $memberInfo['userCardCode'];  //会员编号
		// $data['code'] = $memberInfo['userCardCode'];
		// $data['card_id'] = $memberInfo['card_id'];
		// $httpResponse = $this->http->ajax(array(
		// 	'url'=>$url,
		// 	'type'=>'post',
		// 	'data'=>json_encode($data),
		// 	'dataType'=>'plain',
		// 	'responseType'=>'json'
		// ));
		// $this->memberDb->updateMember($data);
	}

	//判断有无会员卡
	public function judge($userId,$clientId){
		$clientInfo = $this->clientAo->get($userId,$clientId);
		$openId = $clientInfo['openId'];
		return $this->memberDb->judge($userId,$openId);
	}
}
