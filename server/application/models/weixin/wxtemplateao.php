<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WxTemplateAo extends CI_Model 
{
	public function __construct(){
		parent::__construct();
		$this->load->model('weixin/wxTemplateDb','wxTemplateDb');
	}

	private function addOnceSetting($userId){
		$redPacks = $this->wxTemplateDb->getByUserId($userId);
		if( count($redPacks) == 0 ){
			$this->wxTemplateDb->add(array(
				'userId'=>$userId,
			));
		}
	}
	
	public function get($userId){
		$this->addOnceSetting($userId);
		return $data = $this->wxTemplateDb->getByUserId($userId);
	}

	public function mod($userId,$data){
		return $data = $this->wxTemplateDb->modByUserId($userId,$data);
	}
	


	public function notice($userId,$clientId,$sendData,$template_id_short){
 		$this->load->library('http');
 		$TemplateData = $this->get($userId);
 		
 		//关闭模板消息状态，不发送
 		if($TemplateData['openState'] != 1)
 			return '';
		
 		//获取access_token
 		$this->load->model('user/userAppAo','userAppAo');
 		$appAccessToken = $this->userAppAo->getTokenAndTicket($userId)['appAccessToken'];
 		
 		//如果数据库没有这列，就抛出
 		$template_id = '';
 		if(!array_key_exists($template_id_short,$TemplateData))
 			throw new CI_MyException(1,'没有这个模板号码'.$template_id_short);
 				
		//获取template_id
 		if($TemplateData[$template_id_short] == ''){
			$httpResponse = $this->http->ajax(array(
					'url'=>'https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token='.$appAccessToken,
					'type'=>'post',
					'data'=>array('template_id_short'=>$template_id_short),
					'dataType'=>'json_origin',
					'responseType'=>'json'
			));

			if ($httpResponse['body']['errmsg'] != 'ok' )
				throw new CI_MyException(1,'获得模板ID功能错误,userId:'.$userId);
			
			$template_id = $httpResponse['body']['template_id'];
			
			$this->mod($userId,array($template_id_short =>$template_id));
		}else {
			$template_id = $TemplateData[$template_id_short];
		}	

		//找用户openId
		$this->load->model('client/clientDb','clientDb');
		$openId =  $this->clientDb->get($clientId)['openId'];
		
// 		$this->load->model('shop/commodityAo','commodityAo');
// 		$priceShow = $this->commodityAo->getFixedPrice($shopOrder['price']);
		
// 			//创建发送过去的消息
// 			$sendData['touser']='oMhf-twCKWCdxt_He9BT50_6N3dg';
// 			$sendData['template_id']=$template_id;
// 			$sendData['url']=$url;
// 			$sendData['topcolor']='#FF0000';
// 			$sendData['data']['first']['value']='我们已收到您的货款，开始为您打包商品，请耐心等待 :)';
// 			$sendData['data']['first']['color']='#173177';
// 			$sendData['data']['orderMoneySum']['value']=$priceShow;
// 			$sendData['data']['orderMoneySum']['color']='#173177';
// 			$sendData['data']['orderProductName']['value']=$shopOrder['description'];
// 			$sendData['data']['orderProductName']['color']='#173177';
// 			$sendData['data']['remark']['value']='欢迎再次购买！';
// 			$sendData['data']['remark']['color']='#173177';

			$sendData['touser']=$openId;
 			$sendData['template_id']=$template_id;
			
			$httpResponse2 = $this->http->ajax(array(
					'url'=>'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$appAccessToken,
					'type'=>'post',
					'data'=>$sendData,
					'dataType'=>'json_origin',
					'responseType'=>'json'
			));

			if ($httpResponse2['body']['errmsg'] != 'ok' )
				throw new CI_MyException(1,'发送模板消息,userId:'.$userId);
			
			
	}


}



