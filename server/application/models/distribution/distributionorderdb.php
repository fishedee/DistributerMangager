<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionOrderDb extends CI_Model
{
    var $tableName = 't_distribution_order';

    public function __construct(){
        parent::__construct();
    }

    public function add($upUserId, $downUserId, $data){
        $data['upUserId'] = $upUserId;
        $data['downUserId'] = $downUserId;

        $this->db->insert($this->tableName, $data);
        return $this->db->insert_id();
    }

    public function mod($distributionOrderId, $data){
        $this->db->where('distributionOrderId', $distributionOrderId);
        $this->db->update($this->tableName, $data);
    }

    public function del($distributionOrderId){
        $this->db->where('distributionOrderId', $distributionOrderId);
        $this->db->delete($this->tableName);
    }

    public function get($distributionOrderId){
        $this->db->where('distributionOrderId', $distributionOrderId);
        $query = $this->db->get($this->tableName)->result_array();
        if(count($query) == 0)
            throw new CI_MyException(1, '不存在此分成订单');
        else
            return $query[0];
    }

    public function search($where, $limit){
        if( isset($where['upUserId']) && count($where['upUserId']) == 0)
            return array(
                'count'=>0,
                'data'=>array()
            );
        if( isset($where['downUserId']) && count($where['downUserId']) == 0)
            return array(
                'count'=>0,
                'data'=>array()
            );

        foreach($where as $key=>$value){
            if($key == 'upUserId' || $key == 'downUserId' || $key == 'state')
                $this->db->where($key, $value);
            else if($key == 'shopOrderId' || $key == 'distributionOrderId')
                $this->db->where_in($key, $value)
        }
        $count = $this->db->count_all_results($this->tableName);

        foreach($where as $key=>$value){
            if($key == 'upUserId' || $key == 'downUserId' || $key == 'state')
                $this->db->where($key, $value);
            else if($key == 'shopOrderId' || $key == 'distributionOrderId')
                $this->db->where_in($key, $value)
        }
        if(isset($limit['pageIndex']) && isset($limit['pageSize']))
            $this->db->limit($limit['pageSize'], $limit['pageIndex']);
        $query = $this->db->get($this->tableName)->result_array();

        return array(
            'count'=>$count,
            'data'=>$query
        );
    }
}

