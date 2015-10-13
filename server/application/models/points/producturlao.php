<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ProductUrlAo extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->model('points/productUrlDb','productUrlDb');
    }

    public function search($where,$limit){
        return $this->productUrlDb->search($where,$limit);
    }

    public function getUrlInfo($userId,$urlId){
    	$info = $this->productUrlDb->getUrlInfo($userId,$urlId);
    	if($info){
    		$info = $info[0];
    		if($info['userId'] != $userId){
    			throw new CI_MyException(1,'无效查看');
    		}
    		return $info;
    	}else{	
    		throw new CI_MyException(1,'无效urlid');
    	}
    }

    public function addOnce($userId,$data){
  		$this->load->library('QrCode','qrcode');
    	$result = $this->productUrlDb->checkUserId($userId);
    	//设置上传的图片
		$dirName = dirName(__FILE__).'/../../../../data/upload/qrcode';
		if(!is_dir($dirName)){
			mkdir($dirName,0777,true);
		}
    	$info = $data['url'];
		$errorCorrectionLevel = "H";
		$matrixPointSize = "6";
		$margin = 5;
		$qr = time().'_'.mt_rand(0,999).'qrcode.jpg';
		$qrFileName = $dirName.'/'.$qr;
		$this->qrcode->createCodeToFile($info , $qrFileName , $errorCorrectionLevel,$matrixPointSize,$margin);
		$qr = '/data/upload/qrcode/'.$qr;
		$data['qrcode'] = $qr;
		$data['userId'] = $userId;
    	if($result){
    		$urlId = $result[0]['urlId'];
    		return $this->productUrlDb->mod($urlId,$data);
    	}else{
    		return $this->productUrlDb->add($data);
    	}
    }
}