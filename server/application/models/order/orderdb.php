<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OrderDb extends CI_Model 
{
	var $tableName = "t_shop_order";

	public function __construct(){
		parent::__construct();
	}

	public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if(  $key == 'userId'|| $key == 'clientId' || $key == 'state')
				$this->db->where($key,$value);
			else if(  $key == 'name' )
				$this->db->like($key,$value);
            else if( $key == 'beginTime' )
                $this->db->where('date(createTime) >=', $value);
            else if( $key == 'endTime' )
                $this->db->where('date(createTime) <=', $value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if(  $key == 'userId'|| $key == 'clientId' || $key == 'state')
				$this->db->where($key,$value);
			else if(  $key == 'name' )
				$this->db->like($key,$value);
            else if( $key == 'beginTime' )
                $this->db->where('createTime >=', $value);
            else if( $key == 'endTime' )
                $this->db->where('createTime <=', $value);
		}
			
		$this->db->order_by('createTime','desc');
		
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);

		$query = $this->db->get($this->tableName);
		return array(
			"count"=>$count,
			"data"=>$query->result_array()
		);
	}

	public function get($shopOrderId){
		$this->db->where("shopOrderId",$shopOrderId);
		$query = $this->db->get($this->tableName)->result_array();
		if( count($query) == 0 )
			throw new CI_MyException(1,'找不到此订单');
		return $query[0];
	}

	public function add( $shopOrderId ){
		$this->db->insert($this->tableName,$shopOrderId);
	}

	public function mod( $shopOrderId , $data ){
		$this->db->where("shopOrderId",$shopOrderId);
		$this->db->update($this->tableName,$data);
	}

	public function getCountByClientId($clientId){
		$sql = 'select count(*) as count,state '.
			'from '.$this->tableName.' '.
			'where clientId = ? '.
			'group by state';
		$argv = array($clientId);
		return $this->db->query($sql,$argv)->result_array();
	}
}
