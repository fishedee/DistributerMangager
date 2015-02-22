<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityDb extends CI_Model
{
    var $tableName = "t_shop_commodity";

    public function __construct(){
        parent::__construct();
    }

    public function search($where, $limit){
        if( isset($where['shopCommodityId']) && count($where['shopCommodityId']) == 0 )
            return array(
                'count'=>0,
                'data'=>array()
            );

        foreach($where as $key=>$value){
            if($key == 'title' || $key == 'introduction' )
                $this->db->like($key, $value);
            else if($key == 'userId' || $key == 'shopCommodityClassifyId' || $key == 'state')
                $this->db->where($key, $value);
            else if($key == 'shopCommodityId')
                $this->db->where_in($key, $value);
                
        }
        $count = $this->db->count_all_results($this->tableName);

        foreach($where as $key=>$value){
            if($key == 'title' || $key == 'introduction' )
                $this->db->like($key, $value);
            else if($key == 'userId' || $key == 'shopCommodityClassifyId' || $key == 'state')
                $this->db->where($key, $value);
            else if($key == 'shopCommodityId')
                $this->db->where_in($key, $value);
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

    public function get($shopCommodityId){
        $this->db->where("shopCommodityId", $shopCommodityId);    
        $query = $this->db->get($this->tableName)->result_array();
        if(count($query) == 0)
            throw new CI_MyException(1, '不存在此商品');
        return $query[0];
    }

    public function del($shopCommodityId){
        $this->db->where("shopCommodityId", $shopCommodityId);
        $this->db->delete($this->tableName);
    }

    public function add($data){
        $this->db->insert($this->tableName, $data);
        return $this->db->insert_id();
    }

    public function mod($shopCommodityId, $data){
        $this->db->where('shopCommodityId', $shopCommodityId);
        $this->db->update($this->tableName, $data);
    }

    public function modByShopCommodityClassifyId($shopCommodityClassifyId, $data){
        $this->db->where('shopCommodityClassifyId', $shopCommodityClassifyId);
        $this->db->update($this->tableName, $data);
    }

    public function reduceStock($shopCommodityId, $quantity){
        $sql = 'update '.$this->tableName.
            'set inventory = inventory - '.$quantity.
            'where inventory >= '.$quantity.' and shopCommodityId = '.$shopCommodityId;
        $this->db->query($sql);
        if( $this->db->affected_rows() != 0 )
            throw new CI_MyException(1,'扣减库存失败');
    }
}
