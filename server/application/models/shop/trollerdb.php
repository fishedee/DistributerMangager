<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TrollerDb extends CI_Model
{
    var $tableName = 't_shop_troller';

    public function __construct(){
        parent::__construct();
    }   

    public function search($where, $limit){
        foreach($where as $key=>$value){
            if($key == 'userId' || $key == 'clientId' )
                $this->db->where($key, $value);
            else if($key == 'shopTrollerId' || $key == 'shopCommodityId' )
                $this->db->where_in($key,$value);
        }
        $count = $this->db->count_all_results($this->tableName);

        foreach($where as $key=>$value){
            if($key == 'userId' || $key == 'clientId' )
                $this->db->where($key, $value);
            else if($key == 'shopTrollerId' || $key == 'shopCommodityId')
                $this->db->where_in($key,$value);
        }
        $this->db->order_by('createTime', 'desc');  

        if(isset($limit['pageIndex']) && isset($limit['pageSize']))
            $this->db->limit($limit['pageSize'], $limit['pageIndex']);
        $query = $this->db->get($this->tableName)->result_array();
        return array(
            'count'=>$count,
            'data'=>$query
        );
    }

    public function get($shopTrollerId){
        $this->db->where("shopTrollerId", $shopTrollerId);    
        $query = $this->db->get($this->tableName)->result_array();
        if(count($query) == 0)
            throw new CI_MyException(1, '不存在此商品');
        return $query[0];
    }

    public function getByUserIdAndClientIdAndCommodityId($userId,$clientId,$shopCommodityId){
        $this->db->where("userId", $userId);
        $this->db->where("clientId", $clientId);
        $this->db->where("shopCommodityId", $shopCommodityId);
        return $this->db->get($this->tableName)->result_array();
    }

    public function del($shopTrollerId){
        $this->db->where("shopTrollerId", $shopTrollerId);
        $this->db->delete($this->tableName);
    }

    public function delByUserIdAndClientIdAndCommodityId($userId,$clientId,$shopCommodityId){
        $this->db->where("userId", $userId);
        $this->db->where("clientId", $clientId);
        $this->db->where("shopCommodityId", $shopCommodityId);
        $this->db->delete($this->tableName);
    }

    public function delByUserIdAndClientIdAndNotCommodityId($userId,$clientId,$shopCommodityId){
        $this->db->where("userId", $userId);
        $this->db->where("clientId", $clientId);
        if( count($shopCommodityId) != 0 )
            $this->db->where_not_in("shopCommodityId", $shopCommodityId);
        $this->db->delete($this->tableName);
    }

    public function delByUserIdAndClientIdAndShopTrollerId($userId,$clientId,$shopTrollerId){
        $this->db->where("userId", $userId);
        $this->db->where("clientId", $clientId);
        $this->db->where_in("shopTrollerId", $shopTrollerId);
        $this->db->delete($this->tableName);
    }

    public function add($data){
        $this->db->insert($this->tableName, $data);
        return $this->db->insert_id();
    }

    public function mod($shopTrollerId, $data){
        $this->db->where('shopTrollerId', $shopTrollerId);
        $this->db->update($this->tableName, $data);
    }

    public function modByUserIdAndClientIdAndCommodityId($userId,$clientId,$shopCommodityId, $data){
        $this->db->where("userId", $userId);
        $this->db->where("clientId", $clientId);
        $this->db->where("shopCommodityId", $shopCommodityId);
        $this->db->update($this->tableName, $data);
    }
}
