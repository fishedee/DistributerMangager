<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CouponsDb extends CI_Model {
	
	private $tableName = 't_coupons';

    public function __construct(){
        parent::__construct();
    }

    public function addCoupons($userId,$card_id,$title){
    	$data['userId'] = $userId;
    	$data['card_id']= $card_id;
        $data['title']  = $title;
    	$this->db->insert($this->tableName,$data);
    	return $this->db->insert_id();
    }

    public function getCoupons($userId){
        $this->db->where('userId',$userId);
        return $this->db->get($this->tableName)->result_array();
    }
}