<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WifiAo extends CI_Model {
	
	public function __construct(){
		parent::__construct();
		$this->load->library('http');
		$this->load->model('user/userAppAo','userAppAo');
	}

	public function get($userId,$dataLimit){
		
		//获取access_token
		$appAccessToken = $this->userAppAo->getTokenAndTicket($userId)['appAccessToken'];

		//curl数据
		$httpResponse = $this->http->ajax(array(
				'url'=>'https://api.weixin.qq.com/bizwifi/device/list?access_token='.$appAccessToken,
				'type'=>'post',
				'data'=>array(),
				'dataType'=>'json_origin',
				'responseType'=>'json'
		));
		
		if(!$httpResponse['body']['errcode'] == 0)
			throw new CI_MyException(1,'出错了，'.$httpResponse['body']['errcode'].':'.$httpResponse['body']['errmsg']);
			
		$data['data']=$httpResponse['body']['data']['records'];
		$data['count']=$httpResponse['body']['data']['recordcount'];
		
		return $data;
	
	}
	
	public function add($userId,$data){
	
		//获取access_token
		$appAccessToken = $this->userAppAo->getTokenAndTicket($userId)['appAccessToken'];
	
		//curl数据
		$httpResponse = $this->http->ajax(array(
				'url'=>'https://api.weixin.qq.com/bizwifi/device/add?access_token='.$appAccessToken,
				'type'=>'post',
				'data'=>$data,
				'dataType'=>'json_origin',
				'responseType'=>'json'
		));
	
		if(!$httpResponse['body']['errcode'] == 0)
			throw new CI_MyException(1,'出错了，'.$httpResponse['body']['errcode'].':'.$httpResponse['body']['errmsg']);

		return $data;
	
	}

	public function del($userId,$bssid){
		//获取access_token
		$appAccessToken = $this->userAppAo->getTokenAndTicket($userId)['appAccessToken'];
		
		//curl数据
		$httpResponse = $this->http->ajax(array(
				'url'=>'https://api.weixin.qq.com/bizwifi/device/delete?access_token='.$appAccessToken,
				'type'=>'post',
				'data'=>array('bssid'=>$bssid),
				'dataType'=>'json_origin',
				'responseType'=>'json'
		));
	
		if(!$httpResponse['body']['errcode'] == 0)
			throw new CI_MyException(1,'出错了，'.$httpResponse['body']['errcode'].':'.$httpResponse['body']['errmsg']);
		
		
		return '';
	
	}
	
	public function download($userId,$shop_id){
		//获取access_token
		$appAccessToken = $this->userAppAo->getTokenAndTicket($userId)['appAccessToken'];

		$postData=array('shop_id'=>$shop_id,'img_id'=>1);

		//curl数据
		$httpResponse = $this->http->ajax(array(
				'url'=>'https://api.weixin.qq.com/bizwifi/qrcode/get?access_token='.$appAccessToken,
				'type'=>'post',
				'data'=>$postData,
				'dataType'=>'json_origin',
				'responseType'=>'json'
		));
	
		if(!$httpResponse['body']['errcode'] == 0)
			throw new CI_MyException(1,'出错了，'.$httpResponse['body']['errcode'].':'.$httpResponse['body']['errmsg']);
	
	print_r($httpResponse['body']);die;
		return $httpResponse['body'];
	
	}
	
}
?>