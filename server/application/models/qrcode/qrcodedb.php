<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class QrCodeDb extends CI_Model {

	private $tableName = 't_qrcode';

	public function __construct(){
		parent::__construct();
	}

	//获取信息
	public function getQrcodeInfo($clientId){
		$this->db->where('clientId',$clientId);
		$info = $this->db->get($this->tableName)->result_array();
		return $info;
	}

	public function getInfo($qrcodeId){
		$this->db->where('qrcodeId',$qrcodeId);
		$result = $this->db->get($this->tableName)->result_array();
		return $result[0];
	}

	public function addOrMod($clientId,$data,$mobileRequest){
		$result = $this->getQrcodeInfo($clientId);
		if($mobileRequest == 1){
			if($result){
				//有记录 更新
				$this->db->where('clientId',$clientId);
				$this->db->update($this->tableName,$data);
			}else{
				//没记录 插入
				$data['clientId'] = $clientId;
				$this->db->insert($this->tableName,$data);
			}
			return $this->db->affected_rows();
		}else{
			$data['clientId'] = 1;
			$this->db->insert($this->tableName,$data);
			return $this->db->insert_id();
		}
	}

	public function getAllInfo($userId,$dataWhere,$dataLimit){
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"])){
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);
		}
		$count = $this->db->where('userId',$userId)->count_all_results($this->tableName);
		if(count($dataWhere) > 1){
			$this->db->where('userId',$userId);
			$this->db->like('name',$dataWhere['username'],'both');
		}else{
			$this->db->where('userId',$userId);
		}
		$qrcodeInfo = $this->db->get($this->tableName)->result_array();
		return array(
			'count'=>$count,
			'data' => $qrcodeInfo
			);
	}
}
