<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MemberCardAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->library('http');
		$this->load->model('user/userAppAo','userAppAo');
		$this->load->model('member/memberCardDb','memberCardDb');
	}

	//创建会员卡
	public function create($userId,$data){

    	//检测输入参数
    	foreach ($data as $key => $value) {
    		//判断是否代金券
    		if(!$value){
    			throw new CI_MyException(1,'请确保参数都填写完毕');
    		}
    	}
    	//检测时间
    	if(strtotime($data['end_time']) < strtotime($data['start_time']) || time() > strtotime($data['end_time'])){
    		throw new CI_MyException(1,'时间参数错误');
    	}

    	$info = $this->userAppAo->getTokenAndTicket($userId);
		$access_token = $info['appAccessToken'];

        /*上传卡券logo*/
		$result = dirname(__FILE__).'/../../../..'.$data['logo_url'];
		$url  = "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=".$access_token;
		$curlfile = new CURLFile($result);

		$httpResponse = $this->http->ajax(array(
			'url'=>$url,
			'type'=>'post',
			'data'=>array('media'=>$curlfile),
			'dataType'=>'plain',
			'responseType'=>'json'
		));
		if(isset($httpResponse['body']['errcode'])){
			throw new CI_MyException(1,$httpResponse['body']['errmsg']);
		}
		$logo_url = $httpResponse['body']['url'];

		/*卡券颜色*/

		/*开始创建会员卡*/
		$title = $data['title'];
		$card['base_info']['logo_url'] = $logo_url;
		$card['base_info']['brand_name'] = $data['brand_name'];
		$card['base_info']['code_type']  = $data['code_type'];
		$card['base_info']['title']      = $data['title'];
		$card['base_info']['sub_title']  = $data['sub_title'];
		$card['base_info']['color']      = $data['color'];
		$card['base_info']['notice']     = $data['notice'];
		$card['base_info']['service_phone'] = $data['service_phone'];
		$card['base_info']['description']= $data['description'];
		$card['base_info']['date_info']  = array(
			'type' => 'DATE_TYPE_FIX_TIME_RANGE',
			'begin_timestamp' => strtotime($data['start_time']),
			'end_timestamp'   => strtotime($data['end_time'])
			);
		$card['base_info']['sku'] = array(
			'quantity' => 10000000
			);
		$card['base_info']['location_id_list'] = $data['poi_id'];
		//商户自定义入口
		$card['base_info']['custom_url_name']  = '微商城';
		$card['base_info']['custom_url']= 'http://'.$userId.'.'.$_SERVER['HTTP_HOST'].'/'.$userId.'/item.html';
		$card['base_info']['custom_url_sub_title'] = '立即进入';
		//营销入口
		$card['base_info']['promotion_url_name'] = '完善信息';
		$card['base_info']['promotion_url'] = 'http://'.$userId.'.'.$_SERVER['HTTP_HOST'].'/'.$userId.'/vipinfo.html';
		$card['base_info']['promotion_url_sub_title'] = '手机姓名';

		$card['base_info']['get_limit'] = 1;

		$arr['card']['card_type'] = 'MEMBER_CARD';
		$arr['card']['member_card']['base_info'] = $card['base_info'];
		$arr['card']['member_card']['prerogative'] = urlencode($data['prerogative']); //特权说明
		$arr['card']['member_card']['supply_bonus'] = $data['supply_bonus'];  //积分
		$arr['card']['member_card']['bonus_cleared']= urlencode($data['bonus_cleared']);
		$arr['card']['member_card']['bonus_rules'] = urlencode($data['bonus_rules']);
		$arr['card']['member_card']['activate_url']= 'http://'.$userId.'.'.$_SERVER['HTTP_HOST'].'/vip/activeMember';     //激活会员卡URL
		//第一个自定义入口
		$arr['card']['member_card']['custom_cell1']['name'] = urlencode($data['cell_name']);
		$arr['card']['member_card']['custom_cell1']['tips'] = urlencode($data['cell_tips']);
		$arr['card']['member_card']['custom_cell1']['url']  = $data['cell_url'];
 
		foreach ($arr['card']['member_card']['base_info'] as $key => $value) {
			if(!is_array($value)){
				@$arr['card']['member_card']['base_info'][$key] = urlencode($value);
			}
		}
		if($arr['card']['member_card']['supply_bonus'] == 'true'){
			$arr['card']['member_card']['supply_bonus'] = TRUE;
		}else{
			$arr['card']['member_card']['supply_bonus'] = FALSE;
		}

		$arr['card']['member_card']['supply_balance'] = FALSE;
		$arr['card']['member_card']['base_info']['can_share'] = FALSE;
		$arr['card']['member_card']['base_info']['can_give_friend'] = FALSE;
		$arr['card']['member_card']['base_info']['bind_openid'] = FALSE;

		$url = "https://api.weixin.qq.com/card/create?access_token=".$access_token;
		$result = $this->http->ajax(array(
				'url'=>$url,
				'type'=>'post',
				'data'=>urldecode(json_encode($arr)),
				'dataType'=>'plain',
				'responseType'=>'json'
		));
		if($result['body']['errcode'] == 0){
			return $this->memberCardDb->addMember($userId,$result['body']['card_id'],$title,$arr['card']['member_card']['base_info']['sku']['quantity']);
		}else{
			throw new CI_MyException(1,'会员卡创建失败');
		}
    }

    //api方式更新
    public function apiUpdateMemberCard($userId,$data){
    	//检测输入参数
    	foreach ($data as $key => $value) {
    		//判断是否代金券
    		if($key == 'logo_url'){
    			continue;
    		}else{
    			if(!$value){
	    			throw new CI_MyException(1,'请确保参数都填写完毕');
	    		}
    		}
    	}
    	//检测时间
    	if(strtotime($data['end_time']) < strtotime($data['start_time']) || time() > strtotime($data['end_time'])){
    		throw new CI_MyException(1,'时间参数错误');
    	}

    	$info = $this->userAppAo->getTokenAndTicket($userId);
		$access_token = $info['appAccessToken'];

    	if($data['logo_url']){
	        /*上传卡券logo*/
			$result = dirname(__FILE__).'/../../../..'.$data['logo_url'];
			$url  = "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=".$access_token;
			$curlfile = new CURLFile($result);

			$httpResponse = $this->http->ajax(array(
				'url'=>$url,
				'type'=>'post',
				'data'=>array('media'=>$curlfile),
				'dataType'=>'plain',
				'responseType'=>'json'
			));
			if(isset($httpResponse['body']['errcode'])){
				throw new CI_MyException(1,$httpResponse['body']['errmsg']);
			}
			$logo_url = $httpResponse['body']['url'];
			$card['member_card']['base_info']['logo_url'] = $logo_url;
    	}

		/*卡券颜色*/

		/*开始创建会员卡*/
		$card['card_id'] = $data['card_id'];
		$card['member_card']['base_info']['color']      = $data['color'];
		$card['member_card']['base_info']['notice']     = $data['notice'];
		$card['member_card']['base_info']['service_phone'] = $data['service_phone'];
		$card['member_card']['base_info']['description']= $data['description'];
		$card['member_card']['base_info']['date_info']  = array(
			'type' => 'DATE_TYPE_FIX_TIME_RANGE',
			'begin_timestamp' => strtotime($data['start_time']),
			'end_timestamp'   => strtotime($data['end_time'])
			);
		$card['member_card']['base_info']['location_id_list'] = $data['poi_id'];
 
		foreach ($card['member_card']['base_info'] as $key => $value) {
			if(!is_array($value)){
				@$card['member_card']['base_info'][$key] = urlencode($value);
			}
		}

		// var_dump($card);die;

		$url = "https://api.weixin.qq.com/card/update?access_token=".$access_token;
		$result = $this->http->ajax(array(
				'url'=>$url,
				'type'=>'post',
				'data'=>urldecode(json_encode($card)),
				'dataType'=>'plain',
				'responseType'=>'json'
		));
		if($result['body']['errcode'] == 0){
			return $this->memberCardDb->apiUpdateMemberCard($userId,$data['card_id'],$data['quantity']);
		}else{
			var_dump($result);die;
			throw new CI_MyException(1,'会员卡创建失败');
		}
    }

    //获取会员卡信息
    public function getMemberCard($userId,$dataLimit,$dataWhere){
    	return $this->memberCardDb->getMemberCard($userId,$dataLimit,$dataWhere);
    }

    //更新会员卡信息
    public function updateMemberCard($userId){
    	$cardInfo = $this->getMemberCard($userId,array());
    	foreach ($cardInfo['data'] as $key => $value) {
    		$card_id[] = $value['card_id'];
    		$member_id[] = $value['memberCardId'];
    	}
    	//根据card_id 获取信息
    	foreach ($card_id as $key => $value) {
    		$cardInfo = $this->getMemberCardInfo($userId,$value);
    		$card['memberId']= $member_id[$key];
    		$card['card_id'] = $value;
    		$card['title'] = $cardInfo['member_card']['base_info']['title'];
    		$card['status']= $cardInfo['member_card']['base_info']['status'];
    		$card['num']   = $cardInfo['member_card']['base_info']['sku']['quantity'];
    		$member[] = $card;
    	}
    	//开始更新member
    	return $this->memberCardDb->updateMemberCard($member);
    }

    //获取会员卡信息
    public function getMemberCardInfo($userId,$card_id){
    	$info = $this->userAppAo->getTokenAndTicket($userId);
		$access_token = $info['appAccessToken'];
		$url = 'https://api.weixin.qq.com/card/get?access_token='.$access_token;
		// $data['card_id'] = 'pMhf-t-S2nyyXU38J9hCFZ-Be6BI';
		$data['card_id'] = $card_id;
		$result = $this->http->ajax(array(
			'url'=>$url,
			'type'=>'post',
			'data'=>json_encode($data),
			'dataType'=>'plain',
			'responseType'=>'json'
		));
		if($result['body']['errcode'] == 0){
			return $result['body']['card'];
		}else{
			throw new CI_MyException(1,$result['body']['errmsg']);
		}
    }

    public function memberCardDetailInfo($userId,$card_id){
    	$card = $this->getMemberCardInfo($userId,$card_id);
    	$arr['code_type'] = $card['member_card']['base_info']['code_type'];
    	$arr['brand_name']= $card['member_card']['base_info']['brand_name'];
    	$arr['title']     = $card['member_card']['base_info']['title'];
    	$arr['sub_title'] = $card['member_card']['base_info']['sub_title'];
    	$arr['color']     = $card['member_card']['base_info']['color'];
    	$arr['notice']    = $card['member_card']['base_info']['notice'];
    	$arr['description'] = $card['member_card']['base_info']['description'];
    	$arr['quantity'] = $card['member_card']['base_info']['sku']['quantity'];
    	$arr['start_time'] = date('Y-m-d',$card['member_card']['base_info']['date_info']['begin_timestamp']);
    	$arr['end_time']   = date('Y-m-d',$card['member_card']['base_info']['date_info']['end_timestamp']);
    	$arr['service_phone'] = $card['member_card']['base_info']['service_phone'];
    	$arr['poi_id'] = $card['member_card']['base_info']['location_id_list'];
    	$arr['prerogative'] = $card['member_card']['prerogative'];
    	$arr['supply_bonus']= 1 ? $card['member_card']['supply_bonus'] : 0;
    	$arr['bonus_cleared'] = $card['member_card']['bonus_cleared'];
    	$arr['bonus_rules'] = $card['member_card']['bonus_rules'];
    	$arr['cell_name']   = $card['member_card']['custom_cell1']['name'];
    	$arr['cell_tips']   = $card['member_card']['custom_cell1']['tips'];
    	$arr['cell_url']    = $card['member_card']['custom_cell1']['url'];
    	return $arr;
    }

    //设置默认会员卡
    public function defaultCard($userId,$card_id){
    	$cardInfo = $this->getMemberCardInfo($userId,$card_id);
    	$status   = $cardInfo['member_card']['base_info']['status'];
    	if($status != 'CARD_STATUS_VERIFY_OK' && $status != 'CARD_STATUS_USER_DISPATCH'){
    		throw new CI_MyException(1,'会员卡没通过审核');
    	}
    	return $this->memberCardDb->defaultCard($userId,$card_id);
    }

    //设置测试白名单
    public function white($userId){
    	$info = $this->userAppAo->getTokenAndTicket($userId);
		$access_token = $info['appAccessToken'];
    	$url = 'https://api.weixin.qq.com/card/testwhitelist/set?access_token='.$access_token;
    	$data['openid'] = 'oMhf-tzyuDPAjSi_orkDIy2WH0c0';
    	$data['username'] = 'zhengzhihaoya';
    	$result = $this->http->ajax(array(
			'url'=>$url,
			'type'=>'post',
			'data'=>json_encode($data),
			'dataType'=>'plain',
			'responseType'=>'json'
		));
		var_dump($result);die;
    }

    public function testAdd(){
    	$this->memberCardDb->testAdd();
    }
}
