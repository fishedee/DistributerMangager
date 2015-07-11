 <?php 
/**
 * author:zzh
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class ChipsOrderPayDb extends CI_Model{
		private $tableName = 't_chips_order';

		public function __construct(){
			parent::__construct();
			$this->load->model('user/user_model','usermodel');
			$this->load->model('order/orderWhen','orderWhen');
		}

		//初始化微信方法
		public function initWxSdk123($userId){
			$userInfo = $this->usermodel->getAppInfo($userId);  // 获取用户微信相关配置参数
			$parameter['appId'] = $userInfo['appId'];
			$parameter['appKey']= $userInfo['appKey'];
			$parameter['mchId'] = $userInfo['mchId'];
			$parameter['mchSslCert'] = $userInfo['mchSslCert'];
			$parameter['mchSslKey']  = $userInfo['mchSslKey']; // 初始化的相关参数
			$this->load->library('wxSdk',$parameter,'wxsdk');
		}

		public function initWxSdk($userId){
	    	$appInfo = $this->usermodel->getAppInfo($userId);
			$this->load->library('wxSdk',array(
				'appId'=>$appInfo['appId'],
				'appKey'=>$appInfo['appKey'],
				'mchId'=>$appInfo['mchId'],
				'mchKey'=>$appInfo['mchKey'],
				'mchSslCert'=>$appInfo['mchSslCert'],
				'mchSslKey'=>$appInfo['mchSslKey']
			),'wxsdk');
	    }

		//微信支付
		public function wxPay($userId,$clientId,$out_trade_no,$body,$total_free){

			//初始化sdk
			$this->initWxSdk($userId);

			//查询client中的openid
			$openid_arr = $this->db->select('openId')->from('t_client')->where('clientId',$clientId)->get()->result_array();
			$openid = $openid_arr[0]['openId'];

			//获取JSAPI的支付信息
			return $this->wxsdk->getOrderPayInfo($openid,$out_trade_no,$body,$total_free,'http://'.$_SERVER['HTTP_HOST'].'/chips_order/wxpaycallback/'.$userId);
		}
		//支付预付
		public function wxJsPay($userId,$prepayId){
			//初始化sdk
			$this->initWxSdk($userId);
			//获取js的pay信息
			return $this->wxsdk->getJsPayInfo($prepayId);
		}

		public function wxPayCallback($userId){
			//初始化sdk
			$this->initWxSdk($userId);

			//获取支付回调信息
			$payCallBackInfo = $this->wxsdk->getPayCallBackInfo();

			//通知订单支付成功了
			$this->orderWhen->whenOrderPay($payCallBackInfo['out_trade_no']);

			return $payCallBackInfo;
		}
	}
 ?>