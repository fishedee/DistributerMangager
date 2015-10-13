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

    public function search($where, $limit,$vender=0){

        foreach($where as $key=>$value){
            if($key == 'upUserId' || $key == 'downUserId')
                $this->db->where($key, $value);
            else if($key == 'state')
                $this->db->where($key, $value);
        }
        $count = $this->db->count_all_results($this->tableName);

        if($vender){
            $this->db->where('vender',$vender);
        }else{
            foreach($where as $key=>$value){
                if($key == 'upUserId' || $key == 'downUserId')
                    $this->db->where($key, $value);
                else if($key == 'state')
                    $this->db->where($key, $value);
            }
        }
        $query = $this->db->get($this->tableName)->result_array();
        $count = count($query);
        if(isset($limit['pageIndex']) && isset($limit['pageSize']))
            $this->db->limit($limit['pageSize'], $limit['pageIndex']);
        if($count){
            $this->db->order_by('createTime', 'desc');
        }
        
        return array(
            'count'=>$count,
            'data'=>$query
        );
    }

    //获取分销分成
    public function getDistributionPrice($vender,$myUserId){
        $this->db->where('downUserId',$myUserId);
        $this->db->where('vender',$vender);
        return $this->db->get($this->tableName)->result_array();
    }

    //获取应付佣金
    public function getNeedPay($vender){
        $this->db->where('vender',$vender);
        $this->db->where('state','1');
        $this->db->select('price');
        return $this->db->get($this->tableName)->result_array();
    }
}

