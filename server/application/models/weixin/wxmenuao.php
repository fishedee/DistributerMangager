<?php use Qiniu\json_decode;
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

		return $data;
	}

	public function setSetting($userId,$data='',$mysqlData){
		
		//存入数据库
		$this->addOnceSetting($userId);
		$jsonData=array('content'=>json_encode($mysqlData,JSON_UNESCAPED_UNICODE));
		$this->wxMenuDb->modByUserId($userId,$jsonData);

		foreach ($data as $k=>$v){
			foreach ($v as $mainKey=>$mainValue){
				if ($mainKey =='name' && !empty($mainValue) ){
					$data[$k]['type']='view';
				}
				if($mainKey =='name' && empty($mainValue)){
					throw new CI_MyException(1,"第".($k+1)."栏主菜单的标题,不能为空");
				}
				
				if ($mainKey =='url' && !empty($mainValue) && ($this->isUrl($mainValue) == false)){
					throw new CI_MyException(1,"第".($k+1)."栏主菜单链接,请输入正确url参数");
				}
				if ( ($k >0 && empty($data[($k-1)]['name'])) || ($k >1 && empty($data[($k-2)]['name']))){
					throw new CI_MyException(1,"第".($k+1)."栏主菜单有内容，前面主栏目内容不能空内容。");
				}
				if ($mainKey =='sub_button' && !empty($mainValue) ){
					foreach ($mainValue as $sunKey=>$sunValue){
						if (!empty($sunValue['name']) ){
							$data[$k]['sub_button'][$sunKey]['type']='view';
						}
						if (!empty($sunValue['url']) && $this->isUrl($sunValue['url']) == false){
							throw new CI_MyException(1,"第".($k+1)."栏主菜单的第".($sunKey+1)."子菜单的链接,请输入正确url参数");
						}
					}
					if (empty($data[$k]['name'])){
						throw new CI_MyException(1,"第".($k+1)."栏的子菜单有内容，主菜单标题不能为空");
					}
				}
			}
		}
			
		
		if(!empty($data))$data=array('button'=>$data);

		//获取access_token
 		$appAccessToken = $this->userAppAo->getTokenAndTicket($userId)['appAccessToken'];
 		
 		//初始化sdk
 		$this->load->library('wxSdk',array(),'wxSdk');
 		$this->wxSdk->setMenu($appAccessToken,$data);
	}

	public function isUrl($s){
		return preg_match('/^http[s]?:\/\/'.
			'(([0-9]{1,3}\.){3}[0-9]{1,3}'. // IP形式的URL- 199.194.52.184
			'|'. // 允许IP和DOMAIN（域名）
			'([0-9a-z_!~*\'()-]+\.)*'. // 域名- www.
			'([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.'. // 二级域名
			'[a-z]{2,6})'.  // first level domain- .com or .museum
			'(:[0-9]{1,4})?'.  // 端口- :80
			'((\/\?)|'.  // a slash isn't required if there is no file name
			'(\/[0-9a-zA-Z_!~\'\(\)\[\]\.;\?:@&=\+\$,%#-\/^\*\|]*)?)$/',
			$s) == 1;
	}
}
