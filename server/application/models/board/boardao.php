<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BoardAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('board/boardDb','boardDb');
		$this->load->model('user/userAppAo','userAppAo');
		$this->load->model('board/boardTypeAo','boardTypeAo');
		$this->load->library('http');
	}

	public function search($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		$result = $this->boardDb->search($dataWhere,$dataLimit);
		// $info   = $result['data'];
		// foreach ($info as $key => $value) {
		// 	if($value['boardTypeId'] == 0){
		// 		$info[$key]['boardTypeId'] = '暂无分类';
		// 	}else{
		// 		$arr = $this->boardTypeAo->getType($value['userId'],$value['boardTypeId']);
		// 		$info[$key]['boardTypeId'] = $arr['typeName'];
		// 	}
		// }
		// $result['data'] = $info;
		return $result;
	}

	//检测餐桌号
	public function checkBoardNum($userId,$boardNum){
		$result = $this->boardDb->checkBoardNum($userId,$boardNum);
		if($result){
			return FALSE;
		}else{
			return TRUE;
		}
	}

	//获取餐桌id
	public function getBoardId($userId,$boardNum){
		$result = $this->boardDb->checkBoardNum($userId,$boardNum);
		if($result){
			return $result[0]['boardId'];
		}else{
			throw new CI_MyException(1,'无效餐桌号');
		}
	}

	//检测餐桌管理人
	public function checkBoardUserId($userId,$boardId){
		$result = $this->boardDb->checkBoardUserId($boardId);
		if($result){
			if($result['userId'] != $userId){
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			throw new CI_MyException(1,'无效餐桌Id');
		}
	}

	//获取餐桌信息
	public function getBoardInfo($userId,$boardId){
		$boardInfo = $this->boardDb->getBoardInfo($boardId);
		if($boardInfo){
			$boardInfo = $boardInfo[0];
			if($userId == $boardInfo['userId']){
				return $boardInfo;
			}else{
				throw new CI_MyException(1,'非法查看');
			}
		}else{
			throw new CI_MyException(1,'无效餐桌Id');
		}
	}

	//增加餐桌
	public function add($userId,$data){
		if(!$data['boardNum']){
			throw new CI_MyException(1,'请输入餐桌号');
		}
		if(!is_numeric($data['boardNum'])){
			throw new CI_MyException(1,'餐桌号非法');
		}
		if(!$this->checkBoardNum($userId,$data['boardNum'])){
			throw new CI_MyException(1,'该餐桌号已经存在');
		}
		//组合URL
		// $url = 'http://'.$userId.'.'.$_SERVER['HTTP_HOST'].'/'.$userId.'/board.html?boardNum='.$data['boardNum'];
		$url    = $url = 'http://'.$userId.'.'.$_SERVER['HTTP_HOST'].'/board/scan?boardNum='.$data['boardNum'];
		$data['boardUrl'] = $url;
		//生成二维码图片
		$errorCorrectionLevel = "L";
		$matrixPointSize = "5";
		$margin = 5;
		//设置上传目录
		$dirName = dirName(__FILE__).'/../../../../data/upload/board';
		if(!is_dir($dirName)){
			mkdir($dirName,0777,true);
		}
		//二维码图片
		$this->load->library('QrCode','qrcode');
		$qr = time().'_'.mt_rand(0,999).'board_qrcode.jpg';
		$qrFileName = $dirName.'/'.$qr;
		$this->qrcode->createCodeToFile($url , $qrFileName , $errorCorrectionLevel,$matrixPointSize,$margin);
		$data['boardQr'] = '/data/upload/board/'.$qr;

		$data['userId']   = $userId;
		$data['createTime'] = date('Y-m-d H:i:s',time());
		$data['modifyTime'] = date('Y-m-d H:i:s',time());

		$boardId = $this->boardDb->add($data);
		if($boardId){
			//获取access_token
	    	$info = $this->userAppAo->getTokenAndTicket($userId);
			$access_token = $info['appAccessToken'];
			//创建永久二维码请求地址
			$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
			//开始处理上传二维码
			$arr['action_name'] = 'QR_LIMIT_STR_SCENE';
			$arr['action_info']['scene']['scene_str'] = 'board'.$data['boardNum'];
			//发送请求
			$httpResponse = $this->http->ajax(array(
				'url'=>$url,
				'type'=>'post',
				'data'=>json_encode($arr),
				'dataType'=>'plain',
				'responseType'=>'json'
			));
			if($httpResponse['body']['ticket']){
				$ticket = $httpResponse['body']['ticket'];
				$data   = array();
				$data['boardTicket'] = $httpResponse['body']['ticket'];
				$data['url']         = $httpResponse['body']['url'];
				$data['showQr']      = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket);
				return $this->boardDb->mod($userId,$boardId,$data);
			}else{	
				//删除餐桌
				$this->boardDb->del($userId,$boardId);
				throw new CI_MyException(1,'微信创建二维码失败');
			}
		}else{
			throw new CI_MyException(1,'增加餐桌失败');
		}
	}

	//修改餐桌信息
	public function mod($userId,$boardId,$data){
		return $this->boardDb->mod($userId,$boardId,$data);
	}

	public function modBack($userId,$boardId,$data){
		$info = $this->getBoardInfo($userId,$boardId);
		if(isset($data['boardNum'])){
			unset($data['boardNum']);
		}
		return $this->boardDb->mod($userId,$boardId,$data);
	}

	public function demo(){
		//获取access_token
    	$info = $this->userAppAo->getTokenAndTicket($userId);
		$access_token = $info['appAccessToken'];
		//创建永久二维码请求地址
		$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
		//开始处理上传二维码
		$arr['action_name'] = 'QR_LIMIT_STR_SCENE';
		$arr['action_info']['scene']['scene_str'] = $data['boardNum'];
		//发送请求
		$httpResponse = $this->http->ajax(array(
			'url'=>$url,
			'type'=>'post',
			'data'=>json_encode($arr),
			'dataType'=>'plain',
			'responseType'=>'json'
		));
		if($httpResponse['body']['ticket']){
			$ticket = $httpResponse['body']['ticket'];
			$data   = array();
			$data['boardTicket'] = $httpResponse['body']['ticket'];
			$data['url']         = $httpResponse['body']['url'];
			$data['showQr']      = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket);
			return $this->boardDb->mod($userId,$boardId,$data);
		}else{	
			//删除餐桌
			$this->boardDb->del($userId,$boardId);
			throw new CI_MyException(1,'微信创建二维码失败');
		}
	}

	//确认扫描
	public function scanConfirm($userId,$url){
		if(!$url){
			throw new CI_MyException(1,'url参数错误');
		}
		$result = $this->boardDb->scanConfirm($userId,$url);
		if($result){
			return $result[0]['boardId'];
		}else{
			throw new CI_MyException(1,'不存在该餐桌,请重新扫描二维码');
		}
	}
}
