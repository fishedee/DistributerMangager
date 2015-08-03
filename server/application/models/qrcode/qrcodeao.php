<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class QrCodeAo extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->model('qrcode/qrCodeDb','qrCodeDb');
		$this->load->model('client/clientAo','clientAo');
	}

	//获取信息
	public function getQrcodeInfo($clientId){
		return $this->qrCodeDb->getQrcodeInfo($clientId);
	}

	public function getInfo($qrcodeId){
		return $this->qrCodeDb->getInfo($qrcodeId);
	}

	public function addOrMod($clientId,$data,$mobileRequest){
		return $this->qrCodeDb->addOrMod($clientId,$data,$mobileRequest);
	}

	public function getAllInfo($userId,$dataWhere,$dataLimit){
		if(!$userId){
			throw CI_MyException(1,'非法操作');
		}
		$info = $this->qrCodeDb->getAllInfo($userId,$dataWhere,$dataLimit);
		$qrcodeInfo = $info['data'];
		foreach ($qrcodeInfo as $key => $value) {
			$clientInfo = $this->clientAo->get($userId,$value['clientId']);
			$qrcodeInfo[$key]['nickName'] = $clientInfo['nickName'];
		}
		$info['data'] = $qrcodeInfo;
		return $info;
	}
}
