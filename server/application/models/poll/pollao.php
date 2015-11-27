<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PollAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('client/clientAo','clientAo');
		$this->load->model('user/userAppAo','userAppAo');
		$this->load->model('poll/pollDb','pollDb');
		$this->load->model('poll/recordAo','recordAo');
		$this->load->library('http');
	}

	//检测是否已经报名
	private function checkPoll($userId,$clientId,$openId){
		$result = $this->pollDb->checkPoll($userId,$clientId,$openId);
		return $result;
	}

	//海报
	public function poster($userId,$openId,$pollId){
		$this->load->model('user/userDb','userDb');
		$clientId = $this->clientAo->getClientId($openId);
		//厂家信息
		$venderInfo = $this->userDb->get($userId);
		$venderAppInfo = $this->userAppAo->get($userId);
		//用户信息
		$clientInfo = $this->clientAo->get($userId,$clientId);
		$head = $clientInfo['headImgUrl'];
		$nickName = base64_decode($clientInfo['nickName']);
		header('Content-type: image/jpg');
		$save = dirname(__FILE__).'/../../../../data/upload/';
		$path = dirname(__FILE__).'/../../../..'.$venderAppInfo['poster'];
		$img  = imagecreatefromjpeg($path);   //海报的资源
		$fonts= dirname(__FILE__).'/../../../../data/upload/fonts/volate.ttf'; //字体
		$color= imagecolorallocate($img, 159, 192, 79); //颜色
		$text = '赶快投我票吧';
		$text2= '我的编号是'.$pollId;
		imagefttext($img, 18, 0, 240, 255, $color, $fonts, $text);
		imagefttext($img, 18, 0, 240, 285, $color, $fonts, $text2);
		// var_dump(1);die;
		// imagejpeg($img);die;
		//头像
		$url = $head;
		$headPath = $save.$clientId.'_head.jpg';  //头像的路径
		$this->download($url,$headPath); //头像保存到本地
		$this->thumb($headPath,90,88,$headPath,0);
		$img2 = imagecreatefromjpeg($headPath);
		imagecopy($img, $img2, 139, 236, 0, 0, 90 ,88);
		imagedestroy($img2);
		// imagejpeg($img);die;

		$resultPath = $save.$clientId.'_result.jpg'; //最后的结果图
		imagejpeg($img,$resultPath);
		unlink($headPath);

		//上传图片
		$info = $this->userAppAo->getTokenAndTicket($userId);
        $access_token = $info['appAccessToken'];
        // var_dump($access_token);die;
        $curlfile = new CURLFile($resultPath);
        $type = "image";
		$post = array("filename"=>$curlfile);
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=$type";
		//CURL
		//1、初始化一个CURL会话
		$ch = curl_init();
		//2、设置CURL选项
		curl_setopt($ch,CURLOPT_URL,$url);

		//模拟POST
		curl_setopt($ch,CURLOPT_POST,1);

		//设置POST请求的内容
		curl_setopt($ch,CURLOPT_POSTFIELDS,$post);

		//将请求的结果以文件流的形式返回
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

		//3、执行一个CURL会话
		$optout = curl_exec($ch);
		$optoutArr = json_decode($optout,true);
		$media_id = $optoutArr['media_id'];
		// var_dump($media_id);die;
		//4、关闭CURL会话
		curl_close($ch);
		// var_dump($media_id);die;
		unlink($resultPath);
		return $media_id;
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

	//缩略
	private function thumb($img,$width,$height,$fileName,$white=1){
		//源图的路径，可以是本地文件，也可以是远程图片
		$src_path = $img;
		//最终保存图片的宽
		$width = $width;
		//最终保存图片的高
		$height = $height;
		 
		//源图对象
		$src_image = imagecreatefromstring(file_get_contents($src_path));
		$src_width = imagesx($src_image);
		$src_height = imagesy($src_image);
		 
		//生成等比例的缩略图
		$tmp_image_width = 0;
		$tmp_image_height = 0;
		if ($src_width / $src_height >= $width / $height) {
		    $tmp_image_width = $width - 10;
		    $tmp_image_height = round($tmp_image_width * $src_height / $src_width);
		} else {
		    $tmp_image_height = $height - 10;
		    $tmp_image_width = round($tmp_image_height * $src_width / $src_height);
		}
		if($white == 1){
			$tmp_image_width = $width - 5;
			$tmp_image_height= $height - 5;
		}else{
			$tmp_image_width = $width;
			$tmp_image_height= $height;
		}
		 
		$tmpImage = imagecreatetruecolor($tmp_image_width, $tmp_image_height);
		imagecopyresampled($tmpImage, $src_image, 0, 0, 0, 0, $tmp_image_width, $tmp_image_height, $src_width, $src_height);
		 
		//添加白边
		$final_image = imagecreatetruecolor($width, $height);
		$color = imagecolorallocate($final_image, 255, 255, 255);
		$alpha = imagecolorallocatealpha($final_image, 255, 0, 0, 100);
		imagefill($final_image, 0, 0, $color);
		 
		$x = round(($width - $tmp_image_width) / 2);
		$y = round(($height - $tmp_image_height) / 2);
		 
		imagecopy($final_image, $tmpImage, $x, $y, 0, 0, $tmp_image_width, $tmp_image_height);
		 
		imagejpeg($final_image,$fileName);
	}

	//报名
	public function poll($userId,$openId){
		$data['openId'] = $openId;
        $data['userId'] = $userId;
        $data['type']   = 2;
        $clientId = $this->clientAo->addOnce($data);
        $result = $this->checkPoll($userId,$clientId,$openId);
        $count  = count($result);
        if($count){
        	$media_id = $this->poster($userId,$openId,$result[0]['pollId']);
        	return array(
        		'code'=>2,
        		'msg' =>$media_id
        		);
        }else{
        	//获取用户信息
			$info = $this->userAppAo->getTokenAndTicket($userId);
	        $access_token = $info['appAccessToken'];
	        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openId."&lang=zh_CN";
	        $yonghu = $this->http->ajax(array(
				'url'=>$url,
				'type'=>'post',
				'data'=>array(),
				'dataType'=>'plain',
				'responseType'=>'json'
			));
			if(isset($yonghu['body']['errcode'])){
				return $yonghu['body']['errmsg'];
			}
			$yonghu = $yonghu['body'];
			if($yonghu['subscribe']){
				$nickName   = base64_encode($yonghu['nickname']);
	    		$headImgUrl = $yonghu['headimgurl'];
	    	}else{
	    		$nickName 	= '用户没关注';
	    		$headImgUrl = 'null';
	    	}
	    	//报名
	    	$data = array();
	    	$data['userId'] = $userId;
	    	$data['clientId'] = $clientId;
	    	$data['openId'] = $openId;
	    	$pollId = $this->pollDb->add($data);
	    	if($pollId){
	    		//报名成功 更改client信息
	    		$data = array();
	    		$data['nickName'] = $nickName;
	    		$data['headImgUrl'] = $headImgUrl;
	    		$this->clientAo->mod($userId,$clientId,$data);

	    		//推送客服消息
	    		$data = array();
	    		$data['touser'] = (string)$openId;
	    		$data['msgtype']= 'text';
	    		$data['text']['content'] = urlencode('您已经报名成功,您的活动编号是'.$pollId.',海报正在生成,请稍等片刻。生成海报后赶快叫你的朋友帮您投票赢取电影票吧。');
	    		$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
	    		$result = $this->http->ajax(array(
					'url'=>$url,
					'type'=>'post',
					'data'=>urldecode(json_encode($data)),
					'dataType'=>'plain',
					'responseType'=>'json'
				));
				if($result['body']['errcode'] == 0){
					$media_id = $this->poster($userId,$openId,$pollId);
		    		return array(
		    			'code'=>2,
		    			'msg' =>$media_id
		    			);
				}else{
					return array(
						'code'=>1,
						'msg' =>'推送客服消息失败'
						);
				}
	    	}else{
	    		return array(
	    			'code'=>1,
	    			'msg' =>'报名失败'
	    			);
	    	}
        }
	}

	public function getClientId($userId,$openId){
		$data['openId'] = $openId;
        $data['userId'] = $userId;
        $data['type']   = 2;
        $clientId = $this->clientAo->addOnce($data);
        return $clientId;
	}

	//获取报名人数
	public function getPoll($userId){
		$info = $this->pollDb->getPoll($userId);
		foreach ($info as $key => $value) {
			$info[$key]['nickName'] = base64_decode($info[$key]['nickName']);
		}
		return $info;
	}

	public function getDetail($userId,$pollId){
		$info = $this->pollDb->getDetail($pollId);
		if($info){
			$info = $info[0];
			if($info['userId'] != $userId){
				throw new CI_MyException(1,'无效用户id');
			}
			return $info;
		}else{
			throw new CI_MyException(1,'无效id');
		}
	}

	//投票
	public function vote($userId,$clientId,$pollId){
		$result = $this->recordAo->checkVote($userId,$clientId);
		if($result){
			throw new CI_MyException(1,'您已经投票了');
		}
		//检测clientId的正确性
		$client = $this->clientAo->get($userId,$clientId);
		$info = $this->getDetail($userId,$pollId);
		$data['num'] = $info['num'] + 1;
		$result = $this->pollDb->mod($pollId,$data);
		if($result){
			return $this->recordAo->vote($userId,$clientId);
		}else{
			throw new CI_MyException(1,'投票失败');
		}
	}
}
