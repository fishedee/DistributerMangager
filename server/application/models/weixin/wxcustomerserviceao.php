<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WxCustomerServiceAo extends CI_Model 
{
	public function __construct(){
		parent::__construct();
		$this->load->model('weixin/wxCustomerServiceDb','wxCustomerServiceDb');
	}
	
	public function search($whereData,$dataLimit){
		$Kfdata = $this->wxCustomerServiceDb->search($whereData,$dataLimit);
		if ($Kfdata == null){
			return '';
		}else {
			return $Kfdata;
		}
	}
	
	public function Add($userId,$data){
		//获取access_token
		$this->load->model('user/userAppAo','userAppAo');
 		$appAccessToken = $this->userAppAo->getTokenAndTicket($userId)['appAccessToken'];

		$this->load->library('http');
		$httpResponse = $this->http->ajax(array(
				'url'=>'https://api.weixin.qq.com/customservice/kfaccount/add?access_token='.$appAccessToken,
				'type'=>'post',
				'data'=>$data,
				'dataType'=>'json_origin',
				'responseType'=>'json'
		));
		
		//添加到数据库里面
		$this->Pull($userId);
		
		if($httpResponse['body']['errcode'] == 0){
			return '';
		}else {
			throw new CI_MyException(1,'出错了，'.$httpResponse['body']['errcode'].':'.$httpResponse['body']['errmsg']);
		}

	}
	
	public function Mod($userId,$data){
		//获取access_token
		$this->load->model('user/userAppAo','userAppAo');
		$appAccessToken = $this->userAppAo->getTokenAndTicket($userId)['appAccessToken'];
	
		$this->load->library('http');
		$httpResponse = $this->http->ajax(array(
				'url'=>'https://api.weixin.qq.com/customservice/kfaccount/update?access_token='.$appAccessToken,
				'type'=>'post',
				'data'=>$data,
				'dataType'=>'json_origin',
				'responseType'=>'json'
		));
	
		//添加到数据库里面
		$this->Pull($userId);
	
		if($httpResponse['body']['errcode'] == 0){
			return '';
		}else {
			throw new CI_MyException(1,'出错了，'.$httpResponse['body']['errcode'].':'.$httpResponse['body']['errmsg']);
		}
	
	}

	public function Del($userId,$data){
		//获取access_token
		$this->load->model('user/userAppAo','userAppAo');
		$appAccessToken = $this->userAppAo->getTokenAndTicket($userId)['appAccessToken'];
	
		$this->load->library('http');
		$httpResponse = $this->http->ajax(array(
				'url'=>'https://api.weixin.qq.com/customservice/kfaccount/del?access_token='.$appAccessToken.'&kf_account='.$data['kf_account'],
				'type'=>'get',
				'data'=>array(),
				'dataType'=>'',
				'responseType'=>'json'
		));
	
		//添加到数据库里面
		$this->Pull($userId);
	
		if($httpResponse['body']['errcode'] == 0){
			return '';
		}else {
			throw new CI_MyException(1,'出错了，'.$httpResponse['body']['errcode'].':'.$httpResponse['body']['errmsg']);
		}
	
	}
	
	public function customerServiceHeadPortrait($userId,$data){
		//获取access_token
		$this->load->model('user/userAppAo','userAppAo');
		$appAccessToken = $this->userAppAo->getTokenAndTicket($userId)['appAccessToken'];
	
		$this->load->library('http');
		$httpResponse = $this->http->ajax(array(
				'url'=>'https://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token='.$appAccessToken.'&kf_account='.$data['kf_account'],
				'type'=>'post',
				'data'=>array("media"=>new CURLFile(dirname(__FILE__).'/../../../..'.$data['media'])),
				'dataType'=>'plain',
				'responseType'=>'json'
		));
	
		//POST提交内容
// 		$post = array("media"=>new CURLFile(dirname(__FILE__).'/../../../..'.$data['media']));
// 		$url = 'https://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token='.$appAccessToken.'&kf_account='.$data['kf_account'];//上传地址
// 		$ch = curl_init();
// 		curl_setopt($ch,CURLOPT_URL,$url);
// 		curl_setopt($ch,CURLOPT_POST,1);//模拟POST
// 		curl_setopt($ch,CURLOPT_POSTFIELDS,$post);//POST内容
// 		$data2 = curl_exec($ch);
// 		curl_close($ch);
		
// 		print_r($data2);die();
		
		@unlink(dirname(__FILE__).'/../../../..'.$data['PicUrl']);
		
		//添加到数据库里面
		$this->Pull($userId);
	
		if($httpResponse['body']['errcode'] == 0){
			return '';
		}else {
			throw new CI_MyException(1,'出错了，'.$httpResponse['body']['errcode'].':'.$httpResponse['body']['errmsg']);
		}
	
	}
	
	public function Pull($userId){
		//获取access_token
		$this->load->model('user/userAppAo','userAppAo');
		$appAccessToken = $this->userAppAo->getTokenAndTicket($userId)['appAccessToken'];
		
		//curl数据
		$this->load->library('http');
		$httpResponse = $this->http->ajax(array(
				'url'=>'https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token='.$appAccessToken,
				'type'=>'get',
				'data'=>array(),
				'dataType'=>'',
				'responseType'=>'json'
		));
		
		
		if (isset($httpResponse['body']['errcode']) && !$httpResponse['body']['errcode'] == 0)
			throw new CI_MyException(1,'出错了，'.$httpResponse['body']['errcode'].':'.$httpResponse['body']['errmsg']);
		
		//插入数据库
		if(!count($httpResponse['body']['kf_list']) == 0){
			$data=$httpResponse['body']['kf_list'];
			foreach ($data as $k=>$v){
				$data[$k]['userId']=$userId;
			}
		}else {
			return '';
		}
		
		$this->wxCustomerServiceDb->addBatch($data,$userId);
		
		return '';
		
	}
}