<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionQrCodeDb extends CI_Model
{
    var $tableName = 't_distribution_qrcode';

    public function __construct(){
        parent::__construct();
    }

    //我的二维码
    public function qrCode($userId,$limit){
    	if(isset($limit['pageIndex']) && isset($limit['pageSize'])){
    		$this->db->limit($limit['pageSize'], $limit['pageIndex']);
    	}
    	$this->db->where('userId',$userId);
    	$query = $this->db->get($this->tableName)->result_array();
    	return array(
    		'count'=>count($query),
    		'data' =>$query
    		);
    }

    //生成二维码
    public function createQrCode($userId,$qr){
    	$this->db->where('userId',$userId);
    	$result = $this->db->get($this->tableName)->result_array();
    	if($result){
    		$this->db->where('userId',$userId);
    		$data['qrcode'] = $qr;
    		$this->db->update($this->tableName,$data);
    		return $this->db->affected_rows();
    	}else{
    		$data['userId'] = $userId;
    		$data['qrcode'] = $qr;
    		$this->db->insert($this->tableName,$data);
    		return $this->db->insert_id();
    	}
    }

    //获取二维码
    public function getQrCode($userId){
    	$this->db->select('qrcode');
    	$this->db->where('userId',$userId);
    	$result = $this->db->get($this->tableName)->result_array();
    	return $result;
    }

}
