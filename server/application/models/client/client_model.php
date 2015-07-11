<?php 
/**
 * @author:zzh
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Client_model extends CI_Model{
		private $tableName = 't_client';

		public function __construct(){
			parent::__construct();
		}

		public function getClient($userId,$limit,$chips_id){
			if( isset($limit["pageIndex"]) && isset($limit["pageSize"])){
				$this->db->limit($limit["pageSize"],$limit["pageIndex"]);
			}
			$this->load->helper('sendget');
			$clientInfo = $this->db->select('clientId,openId')->from($this->tableName)->where('userId',$userId)->get()->result_array();
			// 获取access_token
			$userInfo = $this->db->select('appid,appkey')->from('t_user_app')->where('userId',$userId)->get()->result_array();
			$appid = $userInfo[0]['appid'];
			$appkey= $userInfo[0]['appkey'];

	        $result = Get("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appkey);
	        $info = json_decode($result,true);
	        $access_token = $info['access_token'];

	        //根据clientInfo 获取用户基本信息
	        foreach ($clientInfo as $key => $value) {
	        	$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$value['openId']."&lang=zh_CN";
	        	$yonghu = json_decode(GET($url));
	        	if($yonghu->subscribe == 1){
	        		$clientInfo[$key]['nickname'] 	= $yonghu->nickname;
	        		$clientInfo[$key]['headimgurl'] = $yonghu->headimgurl;
	        	}else{
	        		$clientInfo[$key]['nickname'] 	= '用户没关注';
	        		$clientInfo[$key]['headimgurl'] = 'null';
	        	}
	        	$condition['clientId'] = $value['clientId'];
	        	$condition['chips_id'] = $chips_id;
	        	$power_result = $this->db->where($condition)->get('t_chips_power')->result_array();
	        	if(count($power_result)){
	        		$clientInfo[$key]['check'] = "<input type='checkbox' clientId='".$value['clientId']."' name='power[]' checked='true'/>";
	        	}else{
	        		$clientInfo[$key]['check'] = "<input type='checkbox' clientId='".$value['clientId']."' name='power[]'/>";
	        	}
	        }
	        $count = $this->db->where('userId',$userId)->count_all_results($this->tableName);
	        return array(
	        	'count' => $count,
	        	'data'  => $clientInfo
	        	);
		}
	}
 ?>