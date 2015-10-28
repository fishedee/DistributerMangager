<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BookingDb extends CI_Model {

	private $tableName = 't_dish_booking';

	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
			
		$this->db->order_by('createTime','asc');
		
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);
		$this->db->where('userId',$where['userId']);
		if(isset($where['boardTypeId'])){
			$this->db->where('boardTypeId',$where['boardTypeId']);
		}
		if(isset($where['state']))
			$this->db->where('state',$where['state']);
		$query = $this->db->get($this->tableName)->result_array();
		return array(
			"count"=>count($query),
			"data"=>$query
		);
	}

	//获取预定详情
	public function getBookingInfo($bookingId){
		$this->db->where('bookingId',$bookingId);
		return $this->db->get($this->tableName)->result_array();
	}

	//预定
	public function add($data){
		$data['createTime'] = date('Y-m-d H:i:s',time());
		$data['modifyTime'] = date('Y-m-d H:i:s',time());
		$this->db->insert($this->tableName,$data);
		return $this->db->insert_id();
	}

	//删除
	public function del($bookingId){
		$this->db->where('bookingId',$bookingId);
		$this->db->delete($this->tableName);
		return $this->db->affected_rows();
	}

	//更新
	public function mod($bookingId,$data){
		$data['modifyTime'] = date('Y-m-d H:i:s',time());
		$this->db->where('bookingId',$bookingId);
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}
}
