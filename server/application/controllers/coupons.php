<?php 
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Coupons extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->library('argv', 'argv');
			$this->load->model('user/userAppAo','userAppAo');
			$this->load->model('coupons/couponsAo','couponsAo');
			$this->load->library('http');
			$this->load->library('argv', 'argv');
		}

		/**
		 * @view json
		 */
		public function fenxiang(){
			$arr['title'] = '测试分享朋友圈标题';
			$arr['link']  = '10007.shop.tongyinyang.com/10007/put.html';
			$arr['imgUrl']= 'http:www.xx.com';
			return $arr;
		}

		/**
		 * @view json
		 * 获取门店id
		 */
		public function getPoiId(){
			$userId = $this->session->userdata('userId');
			return $this->couponsAo->getPoiId($userId);
		}
		/**
		 * @view json
		 * 创建卡券
		 */
		public function add(){
			if($this->input->is_ajax_request()){
				$userId = $this->session->userdata('userId');
				$data   = $this->input->post('data');
				return $this->couponsAo->create($userId,$data);
			}
		}

		/**
		 * @view json
		 * 获取cardext
		 */
		public function getCardExt(){
			//检查输入参数
			$this->load->library('argv','argv');
			$data = $this->argv->checkGet(array(
				array('userId','require'),
			));
			$ticket = $this->userAppAo->getTicket($data['userId']);
			// $ticket = 'E0o2-at6NcC2OsJiQTlwlOku_uUaZ_w_Tic8iC9dSNFX--8oPzeX8PGp3SOqybvpNcStbaU4u2MGuRRstEDyIQ';
			// var_dump($ticket);die;
			// $ticket = 'E0o2-at6NcC2OsJiQTlwlOku_uUaZ_w_Tic8iC9dSNEDHfoSVStCasR--e4LF6MqLv23jl2Ai6sKAcYa8DKYjA';
			// $ticket  = 'E0o2-at6NcC2OsJiQTlwlOku_uUaZ_w_Tic8iC9dSNEIepMloQRzS3e8oeREMxaGEcHR8X2kTWy3n-IQxqx5gg';
			// $card_id = 'pMhf-tyg8GDWvPMIRq3HEJz4YoUI';
			$card_id = $this->input->post('card_id');

			$arr['code'] = '';
			$arr['openid'] = '';
			$arr['timestamp'] = time();
			$arr['nonce_str']   = $this->createNonceStr();
			$arr['api_ticket'] = $ticket;
			$arr['card_id']= $card_id;

			$arr2['timestamp'] = ord($arr['timestamp']);
			$arr2['api_ticket']= ord($arr['api_ticket']);
			$arr2['nonce_str'] = ord($arr['nonce_str']);
			$arr2['card_id']   = ord($arr['card_id']);

			asort($arr2);
			$sig = '';
			foreach ($arr2 as $key => $value) {
				$sig .= $arr[$key];
			}
			// var_dump($sig);die;
			$arr['signature'] = sha1($sig);
			unset($arr['api_ticket']);
			unset($arr['card_id']);
			return json_encode($arr);
		}

	  	/**
	  	 * @view json
	  	 * 获取卡券详细信息
	  	 */
	  	public function cardInfo(){
	  		//检查输入参数
			$userId = $this->session->userdata('userId');
			//获取access_token
	    	$info = $this->userAppAo->getTokenAndTicket($userId);
			$access_token = $info['appAccessToken'];
			$data['card_id'] = $this->input->post('card_id');
			$url = "https://api.weixin.qq.com/card/get?access_token=".$access_token;
			$httpResponse = $this->http->ajax(array(
				'url'=>$url,
				'type'=>'post',
				'data'=>json_encode($data),
				'dataType'=>'plain',
				'responseType'=>'json'
			));
			return $httpResponse['body'];
	  	}

	  	/**
	  	 * @view json
	  	 * 权限验证
	  	 */
	  	public function getPower(){
	  		//检查输入参数
			$this->load->library('argv','argv');
			$data = $this->argv->checkGet(array(
				array('userId','require'),
			));
			$userId = $data['userId'];
	  		$info 	= $this->userAppAo->getTokenAndTicket($userId);

	  		$arr['timestamp'] = time();
	  		$arr['nonceStr']  = $this->createNonceStr();
	  		$arr['jsapi_ticket'] = $info['appJsApiTicket'];

	  		// 注意 URL 一定要动态获取，不能 hardcode.
		    $url = 'http://10007.shop.tongyinyang.com/10007/sweep.html';

			$jsapiTicket = $arr['jsapi_ticket'];
			$nonceStr    = $arr['nonceStr'];
			$timestamp   = $arr['timestamp'];

			$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

			$data = array();
			$data['appId'] 	   = $info['appId'];
			$data['timestamp'] = $arr['timestamp'];
			$data['nonceStr']  = $arr['nonceStr'];
			$data['signature'] = sha1($string);

			return $data;
	  	}

	  	//生成随机的签名串
	  	private function createNonceStr($length = 16) {
	    	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		    $str = "";
		    for ($i = 0; $i < $length; $i++) {
		      	$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		    }
		    return $str;
	  	}

	  	/**
	  	 * @view json
	  	 * 查看卡券
	  	 */
	  	public function search(){
	  		$userId = $this->session->userdata('userId');
	  		$result = $this->couponsAo->search($userId);
	  		// var_dump($result);die;
	  		return $result;
	  		// var_dump($result);die;
	  	}

	  	/**
	  	 * @view json
	  	 * 查看卡券
	  	 */
	  	public function delCoupons(){
	  		$userId = $this->session->userdata('userId');
	  		$card_id= $this->input->post('card_id');
	  		return $this->couponsAo->delCoupons($userId,$card_id);
	  	}

	  	/**
	  	 * @view json
	  	 * 前台扫一扫核销卡卷
	  	 */
	  	public function calcelCoupons(){
	  		if($this->input->is_ajax_request()){
	  			//检查输入参数
				$this->load->library('argv','argv');
				$data = $this->argv->checkGet(array(
					array('userId','require'),
				));
				$userId = $data['userId'];
	  			//验证code码
	  			$code = $this->input->post('code');
	  			if(strstr($code, ',')){
	  				$code = substr($code, strpos($code, ',')+1);
	  			}
	  			//验证、核销code码
	  			return $this->couponsAo->checkCode($userId,$code);
	  		}
	  	}

	  	/**
	  	 * @view json
	  	 * 后台核销卡券
	  	 */
	  	public function calcelCouponsBack(){
	  		if($this->input->is_ajax_request()){
	  			$code = $this->input->post('code');
	  			$userId = $this->session->userdata('userId');
	  			return $this->couponsAo->checkCode($userId,$code);
	  		}
	  	}

	  	/**
	  	 * @view json
	  	 * 获取coupons
	  	 */
	  	public function getCoupons(){
	  		$userId = $this->session->userdata('userId');
	  		$couponsInfo = $this->couponsAo->getCoupons($userId);
	  		$arr[''] = '请选择';
	  		foreach ($couponsInfo as $key => $value) {
	  			$arr[$value['card_id']] = $value['title'];
	  		}
	  		return $arr;
	  	}
	}
 ?>