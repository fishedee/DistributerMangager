<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PointsOrderDb extends CI_Model{

	private $tableName = 't_points_order';

    public function __construct(){
        parent::__construct();
    }

    //兑换奖品
    public function add($data){
    	$this->db->insert($this->tableName,$data);
    	return $this->db->affected_rows();
    }

    //我的奖品
    public function myProduct($userId,$clientId){
    	$this->db->where('vender',$userId);
    	$this->db->where('clientId',$clientId);
    	return $this->db->get($this->tableName)->result_array();
    }

    //查看兑换记录
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
        if( isset($limit["pageIndex"]) && isset($limit["pageSize"]))
            $this->db->limit($limit["pageSize"],$limit["pageIndex"]);
        $this->db->order_by('state','DESC');
        $query = $this->db->get($this->tableName)->result_array();
        return array(
            "count"=>$count,
            "data"=>$query
        );
    }

    public function getOrder($orderId){
        $this->db->where('orderId',$orderId);
        return $this->db->get($this->tableName)->row_array();
    }

    public function mod($orderId,$data){
        $data['modifyTime'] = date('Y-m-d H:i:s');
        $this->db->where('orderId',$orderId);
        $this->db->update($this->tableName,$data);
        return $this->db->affected_rows();
    }
}