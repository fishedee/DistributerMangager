<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class StoresAo extends CI_Model {
	
	public function __construct(){
		parent::__construct();
		$this->load->library('http');
		$this->load->model('user/userAppAo','userAppAo');
	}

	public function get($userId,$dataLimit){
		//获取access_token
		$appAccessToken = $this->userAppAo->getTokenAndTicket($userId)['appAccessToken'];
	
		$postData=array(
				"begin"=>$dataLimit['pageIndex'],
				"limit"=>$dataLimit['pageSize']
		);
	
		//curl数据
		$httpResponse = $this->http->ajax(array(
				'url'=>'https://api.weixin.qq.com/cgi-bin/poi/getpoilist?access_token='.$appAccessToken,
				'type'=>'post',
				'data'=>$postData,
				'dataType'=>'json_origin',
				'responseType'=>'json'
		));
	
		if(!$httpResponse['body']['errcode'] == 0)
			throw new CI_MyException(1,'出错了，'.$httpResponse['body']['errcode'].':'.$httpResponse['body']['errmsg']);
			
		foreach ($httpResponse['body']['business_list'] as $k=>$v){
			$temp=$httpResponse['body']['business_list'][$k]['base_info'];
			$temp['categories']=$temp['categories']['0'];
			$data['data'][$k] = array_intersect_key($temp,array(
					'poi_id'=>'',
					'business_name'=>'',
					'branch_name'=>'',
					'categories'=>'',
					'available_state'=>'',
					'update_status'=>'',
			));
		}
			
		$data['count']=$httpResponse['body']['total_count'];
			
		return $data;
	
	}
	

	public function search($userId,$data){
		//获取access_token
		$appAccessToken = $this->userAppAo->getTokenAndTicket($userId)['appAccessToken'];
		
		//curl数据
		$httpResponse = $this->http->ajax(array(
				'url'=>'http://api.weixin.qq.com/cgi-bin/poi/getpoi?access_token='.$appAccessToken,
				'type'=>'post',
				'data'=>$data,
				'dataType'=>'json_origin',
				'responseType'=>'json'
		));
	
		if(!$httpResponse['body']['errcode'] == 0)
			throw new CI_MyException(1,'出错了，'.$httpResponse['body']['errcode'].':'.$httpResponse['body']['errmsg']);
		
		$data =$httpResponse['body']['business']['base_info'];
		$data['categories']=$data['categories']['0'];
		return $data;
	
	}
	
	
}
?>