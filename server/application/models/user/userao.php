<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('user/userDb','userDb');
		$this->load->model('user/userClientDb','userClientDb');
		$this->load->model('user/userPermissionDb','userPermissionDb');
	}
	
	public function checkMustVaildPassword($password,$passwordHash){
		if( password_verify($password,$passwordHash) == false )
			throw new CI_MyException(1,'密码不正确');
	}

	public function getPasswordHash($password){
		return password_hash($password,PASSWORD_BCRYPT);
	}

	public function getByName($name){
		return $this->userDb->getByName($name);
	}


	public function search($dataWhere,$dataLimit){
		if( isset($dataWhere['permissionId'])){
			// $users = $this->userPermissionDb->search(
				// array('permissionId'=>$dataWhere['permissionId']),
				// array()
			// );
			$users = $this->userPermissionDb->search(
				array('permissionId'=>9),
				array()
			);
			if( $users['count'] == 0 )
				return array('count'=>0,'data'=>array());
			$userIds = array_map(function($single){
				return $single['userId'];
			},$users['data']);
			$dataWhere['userId'] = $userIds;
		}
		return $this->userDb->search($dataWhere,$dataLimit);
	}
	
	public function get($userId){
		$user = $this->userDb->get($userId);
		
		$userPermission = $this->userPermissionDb->getByUser($userId);
		$user['permission'] = array_map(function($single){
			return $single['permissionId'];
		},$userPermission);
		
		$userClient = $this->userClientDb->getByUser($userId);
		$userClientIds = array_map(function($single){
			return $single['clientUserId'];
		},$userClient);
		$user['client'] = $this->userDb->getByIds($userClientIds);
		$user['url'] = 'http://'.$userId.'.'.$_SERVER['HTTP_HOST'].'/'.$userId;
		return $user;
	}
	
	public function del($userId){
		throw new CI_MyException(1,'禁止删掉用户，删掉用户非常容易导致严重的数据不一致问题，你可以禁止他的权限来屏蔽他的使用');

		$this->userDb->del($userId);
			
		$this->userPermissionDb->delByUser($userId);
		
		$this->userClientDb->delByUser($userId);
	}

	private function check($data){
		if( isset($data['name'])){
			$data['name'] = trim($data['name']);
			if( preg_match('/^[0-9A-Za-z]+$/',$data['name']) == 0 )
				throw new CI_MyException(1,'请输入数字或英文字母组成的名字');
		}

		if( isset($data['password'])){
			$data['password'] = trim($data['password']);
			if( $data['password'] == '')
				throw new CI_MyException(1,'请输入密码');
		}

		if(isset($data['password']) && isset($data['password2'])){
			$data['password'] = trim($data['password']);
			$data['password2']= trim($data['password2']);
			if($data['password'] != $data['password2'])
				throw new CI_MyException(1,'两次密码不一致');
			unset($data['password2']);
		}

		if( isset($data['company'])){
			$data['company'] = trim($data['company']);
			if( $data['company'] == '')
				throw new CI_MyException(1,'请输入公司名称');
		}
		
		if( isset($data['phone'])){
			$data['phone'] = trim($data['phone']);
			if( preg_match('/^[0-9]{11}$/',$data['phone']) == 0 )
				throw new CI_MyException(1,'请输入11位的联系人手机号码');
		}

		if( isset($data['telephone'])){
			$data['telephone'] = trim($data['telephone']);
			if( preg_match('/^[0-9-]+$/',$data['telephone']) == 0 )
				throw new CI_MyException(1,'请输入只包含数字的电话号码');
		}

		if( isset($data['email'])){
			$data['email'] = trim($data['email']);
			if( preg_match('/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i',$data['email']) == 0)
				throw new CI_MyException(1,'输入的电子邮箱非法');
			$result = $this->userDb->checkEmail($data['email']);
			if($result){
				if(count($result) > 1 && $result[0]['userId'] != $data['userId']){
					throw new CI_MyException(1,'该邮箱已经被占用');
				}
			}
		}

		return $data;
	}
	
	public function add($data){
		//校验数据
		$data = $this->check($data);

		//检查是否有重名
		$user = $this->userDb->getByName($data['name']);
		if( count($user) != 0 )
			throw new CI_MyException(1,'存在重复的用户名');
		
		//添加用户基本信息
		$userBaseInfo = $data;
		unset($userBaseInfo['permission']);
		unset($userBaseInfo['client']);
		$userBaseInfo['password'] = $this->getPasswordHash($userBaseInfo['password']);
		$userId = $this->userDb->add($userBaseInfo);
		
		//添加用户权限
		if( isset($data['permission']) ){
			$userPermissionInfo = array_map(function($single)use($userId){
				return array(
					'userId'=>$userId,
					'permissionId'=>$single
				);
			},$data['permission']);
			$this->userPermissionDb->addBatch($userPermissionInfo);
		}
		
		//添加用户客户列表
		if( isset($data['client']) ){
			$userClientInfo = array_map(function($single)use($userId){
				return array(
					'userId'=>$userId,
					'clientUserId'=>$single['userId']
				);
			},$data['client']);
			$this->userClientDb->addBatch($userClientInfo);
		}

		return $userId;
	}
	
	public function mod($userId,$data){
		//校验数据
		$data = $this->check($data);
		
		//修改用户基本信息
		$userBaseInfo = $data;
		unset($userBaseInfo['permission']);
		unset($userBaseInfo['client']);
		$this->userDb->mod($userId,$userBaseInfo);
			
		//修改用户权限
		if( isset($data['permission']) ){
			$userPermissionInfo = array_map(function($single)use($userId){
				return array(
					'userId'=>$userId,
					'permissionId'=>$single
				);
			},$data['permission']);
			$this->userPermissionDb->delByUser($userId);
			$this->userPermissionDb->addBatch($userPermissionInfo);
		}
			
		//修改用户客户列表
		if( isset($data['client']) ){
			$userClientInfo = array_map(function($single)use($userId){
				return array(
					'userId'=>$userId,
					'clientUserId'=>$single['userId']
				);
			},$data['client']);
			$this->userClientDb->delByUser($userId);
			$this->userClientDb->addBatch($userClientInfo);
		}
	}
	
	public function modPassword($userId,$password){
		$data = array();
		$data['password'] = $this->getPasswordHash($password);
		$this->userDb->mod($userId,$data);
	}
	
	public function modPasswordByOld($userId,$oldPassword,$newPassword){
		//检查是否有重名
		$user = $this->userDb->get($userId);
		$this->checkMustVaildPassword($oldPassword,$user['password']);
		
		//修改密码
		$this->modPassword($userId,$newPassword);
	}

	/**
	 * @author:zzh
	 * 2015.8.5
	 */

	public function searchOpenId($openId){
		return $this->userDb->searchOpenId($openId);
	}

	//绑定userId 和 openId
	public function bind($userId,$openId){
		return $this->userDb->bind($userId,$openId);
	}

	//解绑
	public function unBind($userId){
		return $this->userDb->unBind($userId);
	}

	//检测登录名和密码
	public function checkLoginInfo($username,$hasPassword,$password){
		return $this->userDb->checkLoginInfo($username,$hasPassword,$password);
	}

	//检测clientId 跟 userId 的绑定
	public function checkClientId($userId,$clientId){
		return $this->userDb->checkClientId($userId,$clientId);
	}

	//获取用户名
	public function getUserName($userId){
		$result = $this->userDb->getUserName($userId);
		if($result){
			return $result[0]['name'];
		}else{
			throw new CI_MyException(1,'无效用户id');
		}
	}

	//创建我的二维码
	public function createQrCode($userId,$distribution){
		//创建下线的二维码
		$this->load->library('http');
        $this->load->model('user/userAppAo','userAppAo');
        $info = $this->userAppAo->getTokenAndTicket($userId);
        $access_token = $info['appAccessToken'];
        //创建永久二维码请求地址
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
        //开始处理上传二维码
        $arr['action_name'] = 'QR_SCENE';
        $arr['expire_seconds'] = 604800;
        // $arr['action_name']  = 'QR_LIMIT_SCENE';
        $distributionData[]  = $userId;
        $distributionData[]  = $distribution['upUserId'];
        $distributionData[]  = $distribution['line'];
        // $arr['action_info']['scene']['scene_str'] = 'upUserId';
        $arr['action_info']['scene']['scene_id'] = 123;
        //发送请求
        $httpResponse = $this->http->ajax(array(
            'url'=>$url,
            'type'=>'post',
            'data'=>json_encode($arr),
            'dataType'=>'plain',
            'responseType'=>'json'
        ));
        if(isset($httpResponse['body']['errcode'])){
            throw new CI_MyException(1,$httpResponse['body']['errmsg']);
        }
        if($httpResponse['body']['ticket']){
            $ticket = $httpResponse['body']['ticket'];
            $qr = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket);
        }
        $data['qrcode'] = $qr;
        $data['qrcodeCreateTime'] = time();
        $this->modInfo($distribution['downUserId'],$data);
	}

	public function modInfo($userId,$data){
		return $this->userDb->modInfo($userId,$data);
	}

	//我的二维码
	public function myQrCode($userId,$openId){
		$this->load->model('client/clientAo','clientAo');
		$clientId = $this->clientAo->getClientId($openId);
		$userInfo = $this->userDb->myQrCode($clientId);
		$myUserId = $userInfo['userId'];
		$qrcode = $userInfo['qrcode'];
		$content['Title'] = '我的二维码';
		$content['Description'] = '扫描发展下线';
		$content['PicUrl'] = $qrcode;
		$content['Url'] = 'http://'.$userId.'.'.$_SERVER[HTTP_HOST].'/'.$myUserId.'/distribution/myqrcode.html?myUserId='.$myUserId;
		$arr[] = $content;
		return $arr;
	}

	//获取我的二维码
	public function getMyQrCode($userId){
		$result = $this->userDb->getMyQrCode($userId);
		if($result){
			return $result[0]['qrcode'];
		}else{
			throw new CI_MyException(1,'您还没有生成二维码');
		}
	}

	//根据clientId查询用户
	public function checkUserClientId($clientId){
		return $this->userDb->checkUserClientId($clientId);
	}

	//获取clientId
	public function getClientIdFromUser($userId){
		$result = $this->userDb->getClientIdFromUser($userId);
		if($result){
			return $result[0]['clientId'];
		}else{
			throw new CI_MyException(1,'无效clientId');
		}
	}

	//获取用户信息
	public function getUserInfo($clientId){
		$result = $this->checkUserClientId($clientId);
		if(!$result){
			throw new CI_MyException(1,'无效用户');
		}
		$userId = $result[0]['userId'];
		$userInfo = $this->get($userId);
		unset($userInfo['password']);
		$userInfo['year'] = date('Y',strtotime($userInfo['birthday']));
		$userInfo['month'] = date('m',strtotime($userInfo['birthday']));
		$userInfo['day'] = date('d',strtotime($userInfo['birthday']));
		return $userInfo;
	}

	//修改信息
	public function myInfo($clientId,$data){
		//检测输入参数
		foreach ($data as $key => $value) {
			if(!$value && $key != 'sex'){
				throw new CI_MyException(1,'请检测各参数');
			}
		}
		$result = $this->checkUserClientId($clientId);
		if(!$result){
			throw new CI_MyException(1,'无效用户');
		}
		$userId = $result[0]['userId'];
		$data['userId'] = $userId;
		$data   = $this->check($data);
		$data['birthday'] = $data['year'].'-'.$data['month'].'-'.$data['day'];
		unset($data['year']);
		unset($data['month']);
		unset($data['day']);
		unset($data['userId']);
		return $this->userDb->mod($userId,$data);
	}

	public function getInfo($myUserId){
		$userInfo = $this->get($myUserId);
		if(!$userInfo){
			throw new CI_MyException(1,'无效用户id');
		}
		unset($userInfo['password']);
		return $userInfo;
	}

	//补全信息
	public function complete($upUserId,$data){
		$this->load->model('distribution/distributionAo','distributionAo');
		$result = $this->distributionAo->checkMyDegree($upUserId,$data['myUserId']);
		if($result == 0){
			throw new CI_MyException(1,'您已经建立了分成关系,不能再申请了');
		}
		if(!isset($data['myUserId']) || !$data['myUserId']){
			throw new CI_MyException(1,'用户id不能为空');
		}
		$userId = $data['myUserId'];
		unset($data['myUserId']);
		unset($data['userId']);
		$data = $this->check($data);
		$result = $this->userDb->complete($userId,$data);
		if($result){
			$result = $this->distributionAo->request($upUserId,$userId);
			if($result){
				return $result;
			}else{
				throw new CI_MyException(1,'申请分销失败');
			}
		}else{
			throw new CI_MyException(1,'补全用户信息失败');
		}
	}

	//检测充值资格
	public function checkRecharge($userId){
		//检测是否为厂家
		$result = $this->userPermissionDb->checkPermissionId($userId,$this->userPermissionEnum->VENDER);
		if($result){
			return $result;
		}else{
			throw new CI_MyException(1,'充值只能面对厂家');
		}
	}

	//充值
	public function recharge($userId,$score){
		if(!$score){
			throw new CI_MyException(1,'充值积分不能为空');
		}
		if(!is_numeric($score)){
			throw new CI_MyException(1,'积分必须为数字');
		}
		if($score <= 0){
			throw new CI_MyException(1,'充值积分必须大于0');
		}
		$userInfo = $this->get($userId);
		$data['score'] = $userInfo['score'] + $score;
		$result = $this->mod($userId,$data);
		//取消预警记录
		$this->load->model('client/remindDb','remindDb');
		$this->remindDb->del($userId);
		//积分日志
		$this->load->model('client/scoreDb','scoreDb');
		$data = array();
		$data['clientId'] = '0';
		$data['event']    = 7;
		$data['createTime'] = date('Y-m-d H:i:s',time());
		$data['remark']   = '充值';
		$data['score']    = $score;
		$data['vender']   = $userId;
		$this->scoreDb->checkIn($data);
	}

	//获取手机验证码
	public function getPhoneCode($phone){
		$phone = trim($phone);
		if( preg_match('/^[0-9]{11}$/',$phone) == 0 )
			throw new CI_MyException(1,'请输入11位的手机号码');

		//随机生成验证码
		$code = '';
		for ($i=0; $i < 4; $i++) { 
			$code .= mt_rand(0,9);
		}
		$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
		$smsConf = array(
		    'key'   => '296b7022d952229e9ec0b64a4d227420', //您申请的APPKEY
		    'mobile'    => $phone, //接受短信的用户手机号码
		    'tpl_id'    => '6395', //您申请的短信模板ID，根据实际情况修改
		    'tpl_value' =>'#code#='.$code.'&#company#=微易点' //您设置的模板变量，根据实际情况修改
		);

		$content = $this->juhecurl($sendUrl,$smsConf,1);
		if($content){
			$result = json_decode($content,true);
			$error_code = $result['error_code'];
			if($error_code == 0){
				$this->session->set_userdata('phone_code', $code);
				return 1;
			}else{
				throw new CI_MyException(1,'短信发送失败'.$msg);
			}
		}else{
			throw new CI_MyException(1,'请求发送短信失败');
		}
	}

	private function juhecurl($url,$params=false,$ispost=0){
	    $httpInfo = array();
	    $ch = curl_init();
	 
	    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
	    curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
	    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
	    curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
	    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
	    if( $ispost )
	    {
	        curl_setopt( $ch , CURLOPT_POST , true );
	        curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
	        curl_setopt( $ch , CURLOPT_URL , $url );
	    }
	    else
	    {
	        if($params){
	            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
	        }else{
	            curl_setopt( $ch , CURLOPT_URL , $url);
	        }
	    }
	    $response = curl_exec( $ch );
	    if ($response === FALSE) {
	        //echo "cURL Error: " . curl_error($ch);
	        return false;
	    }
	    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
	    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
	    curl_close( $ch );
	    return $response;
	}

}
