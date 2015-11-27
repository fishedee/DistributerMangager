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
        if(isset($limit['pageIndex']) && isset($limit['pageSize']))
            $this->db->limit($limit['pageSize'], $limit['pageIndex']);
        foreach($where as $key=>$value){
            if($key == 'upUserId' || $key == 'downUserId')
                $this->db->where($key, $value);
            else if($key == 'state')
                $this->db->where($key, $value);
        }
        $count = $this->db->count_all_results($this->tableName);

        if($vender){
            $this->db->where('vender',$vender);
            foreach ($where as $key => $value) {
                if($key == 'state'){
                   $this->db->where($key, $value); 
                }
            }
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
        
        if($count){
            $this->db->order_by('createTime', 'desc');
        }
        // var_dump($query);die;
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

    public function getDistributionOrder($shopOrderId){
        $this->db->where('shopOrderId',$shopOrderId);
        return $this->db->get($this->tableName)->result_array();
    }

    public function mods($distributionOrderId,$data){
        $this->db->where('distributionOrderId',$distributionOrderId);
        $this->db->update($this->tableName,$data);
        return $this->db->affected_rows();
    }

    public function getAllSales($vender){
        // $sql = "SELECT price FROM t_distribution_order WHERE vender='{$vender}' AND state != 0";
        $sql = "SELECT a.price AS distribution_price,b.price AS order_price FROM t_distribution_order AS a INNER JOIN t_shop_order as b ON a.shopOrderId=b.shopOrderId WHERE a.vender = '{$vender}' AND a.state != 0";
        return $this->db->query($sql)->result_array();
    }

    public function getFall($downUserId){
        // $this->db->where('downUserId',$downUserId);
        // $this->db->select('price');
        $sql = "SELECT price FROM t_distribution_order WHERE downUserId = '{$downUserId}' AND state != 0";
        return $this->db->query($sql)->result_array();
    }
}

