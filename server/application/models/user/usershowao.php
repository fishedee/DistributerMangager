<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserShowAo extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->model('user/userShowDb','userShowDb');
		$this->load->model('client/clientAo','clientAo');
		$this->load->model('user/userAppAo','userAppAo');
	}

	//增加
	public function add($userId,$clientId,$data){
		//检测client的合法性
		$clientInfo = $this->clientAo->get($userId,$clientId);
		//miediaid 用于下载
		$mediaId = $data['mediaId'];
		//获取access_token
		$info = $this->userAppAo->getTokenAndTicket($userId);
        $access_token = $info['appAccessToken'];
        //保存目录
        $save = dirname(__FILE__).'/../../../../data/upload/image/show/';
        $fileName = $clientId.'_'.mt_rand(0,999).'.jpg';
        $path = $save.$fileName;
        //下载路径
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$access_token.'&media_id='.$mediaId;
        //下载
        $this->download($url,$path);
        $data['img'] = '/data/upload/image/show/'.$fileName;
		$data['userId'] = $userId;
		$data['clientId'] = $clientId;
		return $this->userShowDb->add($data);
	}

	//获取买家秀上传的图片
	public function getShowPic($userId,$clientId){
		return $this->userShowDb->getShowPic($userId,$clientId);
	}

	public function test(){
		$this->load->model('user/userAppAo','userAppAo');
    	$userId = 10081;
    	$mediaId= 'RJX9ZYWO6kxjbkDUFve1jTGQog4EuvIEcKYHX6O7OoxfE2TMGm-aNOmhrSRf1luB';
    	$info = $this->userAppAo->getTokenAndTicket($userId);
        $access_token = $info['appAccessToken'];
        $save = dirname(__FILE__).'/../../../../data/upload/image/show/';
        $path = $save.'1.jpg';
    	$url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$access_token.'&media_id='.$mediaId;
    	$this->download($url,$path);
	}

	//下载
	private function download($url,$save_to){
		// $url = $path2;
		$curl = curl_init( $url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true);
		$res = curl_exec( $curl );
		curl_close( $curl );
		file_put_contents( $save_to, $res);
		return $save_to;
	}

	//获取图片的详细信息
	public function getDetail($showId){
		$info = $this->userShowDb->getDetail($showId);
		if($info){
			return $info;
		}else{
			throw new CI_MyException(1,'无效图片id');
		}
	}

	//删减图片
	public function del($userId,$clientId,$showId){
		$info = $this->getDetail($showId);
		if($info['userId'] != $userId || $info['clientId'] != $clientId){
			throw new CI_MyException(1,'非法操作');
		}
		return $this->userShowDb->del($showId);
	}
}
