<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WxMenuAo extends CI_Model 
{
	public function __construct(){
		parent::__construct();
		$this->load->model('weixin/wxMenuDb','wxMenuDb');
	}

	private function addOnceSetting($userId){
		$redPacks = $this->wxMenuDb->getByUserId($userId);
		if( count($redPacks) == 0 ){
			$this->wxMenuDb->add(array(
				'userId'=>$userId,
			));
		}
	}
	
	public function getSetting($userId){
		$this->addOnceSetting($userId);

		$data = $this->wxMenuDb->getByUserId($userId)[0]['content'];
		$data=json_decode($data,true);
		if($data == null){
			return '';
		}else {
			return $data;
		}

	}

	public function setSetting($userId,$data='',$mysqlData){
		
		//存入数据库
		$this->addOnceSetting($userId);
		$jsonData=array('content'=>json_encode($mysqlData,JSON_UNESCAPED_UNICODE));
		$this->wxMenuDb->modByUserId($userId,$jsonData);
		
		//搜索素材模型
		$this->load->model('weixin/wxSubscribeAo','wxSubscribeAo');
		foreach ($data as $k=>$v){
			foreach ($v as $mainKey=>$mainValue){
				if($mainKey =='name' && empty($mainValue)){
					throw new CI_MyException(1,"第".($k+1)."栏主菜单的标题,不能为空");
				}
				
				if ($mainKey =='url' && !empty($mainValue) && $this->isUrl($mainValue) == false){
					//如果是素材ID
					if($this->SubscribeNum($mainValue,$userId) == false){
						throw new CI_MyException(1,"第".($k+1)."栏主菜单链接,请输入正确url参数,或正确的素材ID号");
					}else {
						$data[$k]['type']='click';
						$data[$k]['key'] = $data[$k]['url'];
						unset($data[$k]['url']);
					}
				}else{
					$data[$k]['type']='view';
				}
				
				
				if ($mainKey =='name' && !empty($mainValue) ){
					$data[$k]['type']='view';
				}
				if ( ($k >0 && empty($data[($k-1)]['name'])) || ($k >1 && empty($data[($k-2)]['name']))){
					throw new CI_MyException(1,"第".($k+1)."栏主菜单有内容，前面主栏目内容不能空内容。");
				}
				if ($mainKey =='sub_button' && !empty($mainValue) ){
					foreach ($mainValue as $sunKey=>$sunValue){
						if (empty($sunValue['name']) ){
							throw new CI_MyException(1,"第".($k+1)."栏主菜单的第".($sunKey+1)."子菜单的标题,不能为空");
						}
						if (!empty($sunValue['url']) && $this->isUrl($sunValue['url']) == false){
							if($this->SubscribeNum($sunValue['url'],$userId) == false){
								throw new CI_MyException(1,"第".($k+1)."栏主菜单的第".($sunKey+1)."子菜单的链接,请输入正确url参数,或正确的素材ID号");
							}else {
								$data[$k]['sub_button'][$sunKey]['type']='click';
								$data[$k]['sub_button'][$sunKey]['key'] = $data[$k]['sub_button'][$sunKey]['url'];
								unset($data[$k]['sub_button'][$sunKey]['url']);
							}
							
						}elseif($sunValue['key'] != 'undefined'){
							$data[$k]['sub_button'][$sunKey]['type']='click';
							$data[$k]['sub_button'][$sunKey]['key'] = $sunValue['key'];
						}else {
							$data[$k]['sub_button'][$sunKey]['type']='view';
						}
					}
					if (empty($data[$k]['name'])){
						throw new CI_MyException(1,"第".($k+1)."栏的子菜单有内容，主菜单标题不能为空");
					}
				}
			}
			if(!$v['url']){
				$data[$k]['type'] = 'click';
				$data[$k]['key']  = $v['key'];
			}
		}
		// var_dump($data);die;
		if(!empty($data))$data=array('button'=>$data);
		//获取access_token
		$this->load->model('user/userAppAo','userAppAo');
 		$appAccessToken = $this->userAppAo->getTokenAndTicket($userId)['appAccessToken'];

 		//初始化sdk
 		$this->load->library('wxSdk',array(),'wxSdk_Menu');
 		$this->wxSdk_Menu->setMenu($appAccessToken,$data);
	}

	private function isUrl($s){
		return preg_match('/^http[s]?:\/\/'.
			'(([0-9]{1,3}\.){3}[0-9]{1,3}'. // IP形式的URL- 199.194.52.184
			'|'. // 允许IP和DOMAIN（域名）
			'([0-9a-z_!~*\'()-]+\.)*'. // 域名- www.
			'([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.'. // 二级域名
			'[a-z]{2,6})'.  // first level domain- .com or .museum
			'(:[0-9]{1,4})?'.  // 端口- :80
			'((\/\?)|'.  // a slash isn't required if there is no file name
			'(\/[0-9a-zA-Z_!~\'\(\)\[\]\.;\?:@&=\+\$,%#-\/^\*\|]*)?)$/',
			trim($s)) == 1;
	}
	
	//寻找有没有该素材,也是否属于该用户
	private function SubscribeNum($z,$userId){
		if(preg_match('/^\d+$/',trim($z),$SubscribeId)){
			$Subscribe=$this->wxSubscribeAo->search($userId,array('weixinSubscribeId'=>$SubscribeId[0]),array());
			if(count($Subscribe['data']) ==0){
				//找不到返回false
				return false;
			}else {
				//找到返回true
				return true;
			}
		}
		
	}
}