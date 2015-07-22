<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CouponsAo extends CI_Model {

    public function __construct(){
        parent::__construct();
        $this->load->model('user/userAppAo','userAppAo');
        $this->load->model('coupons/couponsDb','couponsDb');
		$this->load->library('http');
    }

    //获取门店列表
    public function getPoiId($userId){
    	//获取access_token
    	$info = $this->userAppAo->getTokenAndTicket($userId);
		$access_token = $info['appAccessToken'];
		// 获取门店信息
		$data['begin'] = 0;
		$data['limit'] = 10;
		$url = "https://api.weixin.qq.com/cgi-bin/poi/getpoilist?access_token=".$access_token;
		$httpResponse = $this->http->ajax(array(
			'url'=>$url,
			'type'=>'post',
			'data'=>json_encode($data),
			'dataType'=>'plain',
			'responseType'=>'json'
		));
		$listInfo = $httpResponse['body']['business_list'];
		foreach ($listInfo as $key => $value) {
			$arr[$value['base_info']['poi_id']] = $value['base_info']['business_name'];
		}
		return $arr;
    }

    // 创建门券
    public function create($userId,$data){
    	//检测输入参数
    	foreach ($data as $key => $value) {
    		//判断是否代金券
    		if($data['card_type'] == 'CASH'){
    			if(!$value){
    				throw new CI_MyException(1,'请确保参数都填写完毕');
    			}
    			if($key == 'least_cost' || $key == 'reduce_cost'){
    				if(!is_numeric($data['least_cost']) || !is_numeric($data['reduce_cost']) || $data['reduce_cost'] < 1 || $data['least_cost'] < 1){
    					throw new CI_MyException(1,'启用金额或减免金额错误');
    				}
    			}
    		}else{
    			if($key != 'least_cost' || $key != 'reduce_cost'){
    				continue;
    			}
    		}
    	}
    	//检测时间
    	if(strtotime($data['end_time']) < strtotime($data['start_time']) || time() > strtotime($data['end_time'])){
    		throw new CI_MyException(1,'时间参数错误');
    	}

    	/*上传卡券logo*/
		$result = dirname(__FILE__).'/../../../..'.$data['logo_url'];
		$size   = filesize($result);
		if($size > 1048576){
			throw new CI_MyException(1,'上传图片不能大于1M');
		}
		$url  = "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=".$access_token;
		$curlfile = new CURLFile($result);

    	$info = $this->userAppAo->getTokenAndTicket($userId);
		$access_token = $info['appAccessToken'];

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

		/*开始创建卡券*/
		$title = $data['title'];
		$card['base_info']['logo_url'] = $logo_url;
		$card['base_info']['brand_name'] = $data['brand_name'];
		$card['base_info']['code_type']  = $data['code_type'];
		$card['base_info']['title']      = $data['title'];
		$card['base_info']['sub_title']  = $data['sub_title'];
		$card['base_info']['color']      = 'Color010';
		$card['base_info']['notice']     = $data['notice'];
		$card['base_info']['service_phone'] = $data['service_phone'];
		$card['base_info']['description']= $data['description'];
		$card['base_info']['date_info']  = array(
			'type' => 'DATE_TYPE_FIX_TIME_RANGE',
			'begin_timestamp' => strtotime($data['start_time']),
			'end_timestamp'   => strtotime($data['end_time'])
			);
		$card['base_info']['sku'] = array(
			'quantity' => $data['quantity']
			);
		$card['base_info']['location_id_list'] = $data['poi_id'];
		$card['base_info']['get_limit'] = $data['get_limit'];
		$card['base_info']['can_give_friend'] = $data['can_give_friend'];
		$card['base_info']['can_share'] = $data['can_share'];
		$card['base_info']['bind_openid'] = $data['bind_openid'];

		$arr['card']['card_type'] = $data['card_type'];
		switch ($data['card_type']) {
			case 'GROUPON':
				$card['deal_detail'] = urlencode($data['detail']);
				$arr['card'][strtolower($data['card_type'])]['deal_detail'] = $card['deal_detail'];
				break;
			case 'CASH':
				$card['least_cost'] = $data['least_cost'];
				$card['reduce_cost']= $data['reduce_cost'];
				$arr['card'][strtolower($data['card_type'])]['least_cost'] = $card['least_cost'];
				$arr['card'][strtolower($data['card_type'])]['reduce_cost']= $card['reduce_cost'];
				break;
			case 'DISCOUNT':
				if(!is_numeric($data['detail'])){
					throw new CI_MyException(1,'请输入数字');
				}
				$card['discount'] = $data['detail'];
				$arr['card'][strtolower($data['card_type'])]['discount'] = $card['discount'];
				break;
			case 'GIFT':
				$card['gift'] = urlencode($data['detail']);
				$arr['card'][strtolower($data['card_type'])]['gift'] = $card['gift'];
				break;
			case 'GENERAL_COUPON':
				$card['default_detail'] = urlencode($data['detail']);
				$arr['card'][strtolower($data['card_type'])]['default_detail'] = $card['default_detail'];
				break;
		}
		$arr['card'][strtolower($data['card_type'])]['base_info'] = $card['base_info'];

		foreach ($arr['card'][strtolower($data['card_type'])]['base_info'] as $key => $value) {
			if(!is_array($value)){
				@$arr['card'][strtolower($data['card_type'])]['base_info'][$key] = urlencode($value);
			}
		}
		if($arr['card'][strtolower($data['card_type'])]['base_info']['can_give_friend'] === 'true'){
			$arr['card'][strtolower($data['card_type'])]['base_info']['can_give_friend'] = TRUE;
		}else{
			$arr['card'][strtolower($data['card_type'])]['base_info']['can_give_friend'] = FALSE;
		}
		if($arr['card'][strtolower($data['card_type'])]['base_info']['can_share'] == 'true'){
			$arr['card'][strtolower($data['card_type'])]['base_info']['can_share'] = TRUE;
		}else{
			$arr['card'][strtolower($data['card_type'])]['base_info']['can_share'] = FALSE;
		}
		if($arr['card'][strtolower($data['card_type'])]['base_info']['bind_openid'] == 'true'){
			$arr['card'][strtolower($data['card_type'])]['base_info']['bind_openid'] = TRUE;
		}else{
			$arr['card'][strtolower($data['card_type'])]['base_info']['bind_openid'] = FALSE;
		}
		$url = "https://api.weixin.qq.com/card/create?access_token=".$access_token;
		$result = $this->http->ajax(array(
				'url'=>$url,
				'type'=>'post',
				'data'=>urldecode(json_encode($arr)),
				'dataType'=>'plain',
				'responseType'=>'json'
		));
		// var_dump($result);die;
		if($result['body']['errcode'] == 0){
			return $this->couponsDb->addCoupons($userId,$result['body']['card_id'],$title);
		}else{
			throw new CI_MyException(1,'卡券创建失败');
		}
    }

    //批量查询卡券列表
    public function search($userId){
    	//获取access_token
    	$info = $this->userAppAo->getTokenAndTicket($userId);
    	$access_token = $info['appAccessToken'];
    	//获取卡券id列表
    	$url  = 'https://api.weixin.qq.com/card/batchget?access_token='.$access_token;
    	$data['offset'] = 0;
    	$data['count']  = 50;
    	$result = $this->http->ajax(array(
			'url'=>$url,
			'type'=>'post',
			'data'=>json_encode($data),
			'dataType'=>'plain',
			'responseType'=>'json'
		));
		$card_id_list = $result['body']['card_id_list'];
		//根据card_is_list 遍历 查询card详细信息
		$data = array();
		$arr  = array();
		foreach ($card_id_list as $key => $value) {
			$data['card_id'] = $value;
			$url = "https://api.weixin.qq.com/card/get?access_token=".$access_token;
			$httpResponse = $this->http->ajax(array(
				'url'=>$url,
				'type'=>'post',
				'data'=>json_encode($data),
				'dataType'=>'plain',
				'responseType'=>'json'
			));
			if($httpResponse['body']['card'][strtolower($httpResponse['body']['card']['card_type'])]['base_info']['status'] === 'CARD_STATUS_DELETE' || $httpResponse['body']['card'][strtolower($httpResponse['body']['card']['card_type'])]['base_info']['status'] === 'CARD_STATUS_USER_DELETE'){
				continue;
			}
			$card['card_id'] = $httpResponse['body']['card'][strtolower($httpResponse['body']['card']['card_type'])]['base_info']['id'];
			$card['logo_url']= $httpResponse['body']['card'][strtolower($httpResponse['body']['card']['card_type'])]['base_info']['logo_url'];
			$card['card_type'] = $httpResponse['body']['card']['card_type'];
			$card['title']   = $httpResponse['body']['card'][strtolower($httpResponse['body']['card']['card_type'])]['base_info']['title'];
			$card['num']     = $httpResponse['body']['card'][strtolower($httpResponse['body']['card']['card_type'])]['base_info']['sku']['total_quantity'];
			$card['status']  = $httpResponse['body']['card'][strtolower($httpResponse['body']['card']['card_type'])]['base_info']['status'];
			$card['now_num']     = $httpResponse['body']['card'][strtolower($httpResponse['body']['card']['card_type'])]['base_info']['sku']['quantity'];
			if(@$httpResponse['body']['card'][strtolower($httpResponse['body']['card']['card_type'])]['base_info']['date_info']['end_timestamp']){
				$card['time']    = date('Y-m-d H:i:s',$httpResponse['body']['card'][strtolower($httpResponse['body']['card']['card_type'])]['base_info']['date_info']['end_timestamp']);
			}else{
				$card['time'] = null;
			}
			$arr[] = $card;
		}
		return array(
			'count' => count($arr),
			'data'  => $arr
 			);
    }

    //删除卡券
    public function delCoupons($userId,$card_id){
    	//获取access_token
    	$info = $this->userAppAo->getTokenAndTicket($userId);
    	$access_token = $info['appAccessToken'];
    	$url = 'https://api.weixin.qq.com/card/delete?access_token='.$access_token;
    	$data['card_id'] = $card_id;
    	$httpResponse = $this->http->ajax(array(
			'url'=>$url,
			'type'=>'post',
			'data'=>json_encode($data),
			'dataType'=>'plain',
			'responseType'=>'json'
		));
		if($httpResponse['body']['errcode'] == 0){
			return 1;
		}else{
			return 0;
		}
    }

    //验证code码
    public function checkCode($userId,$code){
    	//获取access_token
    	$info = $this->userAppAo->getTokenAndTicket($userId);
    	$access_token = $info['appAccessToken'];
    	$url = 'https://api.weixin.qq.com/card/code/get?access_token='.$access_token;
    	$data['code'] = $code;
    	$httpResponse = $this->http->ajax(array(
			'url'=>$url,
			'type'=>'post',
			'data'=>json_encode($data),
			'dataType'=>'plain',
			'responseType'=>'json'
		));
		//判断能否顺利获取
		if($httpResponse['body']['errcode'] != 0){
			throw new CI_MyException(1,"获取卡券信息失败,可能code码错误!");
		}
		//开始核销卡券
		$url = 'https://api.weixin.qq.com/card/code/consume?access_token='.$access_token;
		$data= array();
		$data['code'] = $code;
		$httpResponse = $this->http->ajax(array(
			'url'=>$url,
			'type'=>'post',
			'data'=>json_encode($data),
			'dataType'=>'plain',
			'responseType'=>'json'
		));
		return $httpResponse['body']['errmsg'];
    }

    public function getCoupons($userId){
    	return $this->couponsDb->getCoupons($userId);
    }
}