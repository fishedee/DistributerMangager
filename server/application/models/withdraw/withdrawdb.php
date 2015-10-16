<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WithDrawDb extends CI_Model{

	private $tableName = 't_withdraw';

    public function __construct(){
        parent::__construct();
    }

    //查看提现申请
    public function search($where,$limit){
		foreach( $where as $key=>$value ){
			if( $key == "state" )
				$this->db->where($key,$value);
			else if( $key == "vender" )
				$this->db->where($key,$value);
		}
		
		$count = $this->db->count_all_results($this->tableName);
		
		foreach( $where as $key=>$value ){
			if( $key == "state" )
				$this->db->where($key,$value);
			else if( $key == "vender" )
				$this->db->where($key,$value);
		}
			
		$this->db->order_by('withDrawId','desc');
		
		if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
			$this->db->limit($limit["pageSize"],$limit["pageIndex"]);

		$query = $this->db->get($this->tableName)->result_array();
		return array(
			"count"=>$count,
			"data"=>$query
		);
	}

	//获取详情
	public function getDrawInfo($withDrawId){
		$this->db->where('withDrawId',$withDrawId);
		return $this->db->get($this->tableName)->result_array();
	}

	//更新
	public function mod($withDrawId,$data){
		$data['modifyTime'] = date('Y-m-d H:i:s',time());
		$this->db->where('withDrawId',$withDrawId);
		$this->db->update($this->tableName,$data);
		return $this->db->affected_rows();
	}

    //提现
    public function add($data){
    	$this->db->insert($this->tableName,$data);
    	return $this->db->insert_id();
    }

    //获取提现日志
    public function getLog($vender,$clientId){
    	$this->db->where('vender',$vender);
    	$this->db->where('clientId',$clientId);
    	return $this->db->get($this->tableName)->result_array();
    }

    //获取需要处理的提现数量
    public function getNeedHandleNum($vender,$state){
    	$this->db->where('vender',$vender);
    	$this->db->where('state',$state);
    	return $this->db->get($this->tableName)->num_rows();
    }
}