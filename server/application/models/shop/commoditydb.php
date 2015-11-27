<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityDb extends CI_Model
{
    var $tableName = "t_shop_commodity";

    public function __construct(){
        parent::__construct();
    }

    public function search($where, $limit,$rank){
        if( isset($where['shopCommodityId']) && count($where['shopCommodityId']) == 0 )
            return array(
                'count'=>0,
                'data'=>array()
            );

        foreach($where as $key=>$value){
            if($key == 'title' || $key == 'introduction' )
                $this->db->like($key, $value);
            else if($key == 'userId' || $key == 'shopCommodityClassifyId' || $key == 'state' || $key == 'isLink' || $key == 'shopLinkCommodityId')
                $this->db->where($key, $value);
            else if($key == 'shopCommodityId')
                $this->db->where_in($key, $value);
                
        }
        $count = $this->db->count_all_results($this->tableName);

        foreach($where as $key=>$value){
            if($key == 'title' || $key == 'introduction' )
                $this->db->like($key, $value);
            else if($key == 'userId' || $key == 'shopCommodityClassifyId' || $key == 'state' || $key == 'isLink' || $key == 'shopLinkCommodityId')
                $this->db->where($key, $value);
            else if($key == 'shopCommodityId')
                $this->db->where_in($key, $value);
        }
        

        if(isset($limit['pageIndex']) && isset($limit['pageSize']))
            $this->db->limit($limit['pageSize'], $limit['pageIndex']);
        if($rank){
            if($rank == 'timeDown'){
                $this->db->order_by('createTime','DESC');
            }
            if($rank == 'timeUp'){
                $this->db->order_by('createTime','ASC');
            }
            if($rank == 'priceUp'){
                $this->db->order_by('price','ASC');
            }
            if($rank == 'priceDown'){
                $this->db->order_by('price','DESC');
            }
        }
        $this->db->order_by('sort', 'asc');
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

    public function getByShopLinkCommodityId($shopLinkCommodityId){
        $this->db->where("shopLinkCommodityId", $shopLinkCommodityId);    
        $query = $this->db->get($this->tableName)->result_array();
        return $query;
    }

    public function getMaxSortByUser($userId){
        $this->db->select_max('sort');
        $this->db->where("userId",$userId);
        $result = $this->db->get($this->tableName)->result_array();
        return $result[0]['sort'];
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

    public function modBatch($data){
        $this->db->update_batch($this->tableName, $data,'shopCommodityId');
    }

    public function modByShopCommodityClassifyId($shopCommodityClassifyId, $data){
        $this->db->where('shopCommodityClassifyId', $shopCommodityClassifyId);
        $this->db->update($this->tableName, $data);
    }

    public function reduceStock($shopCommodityId, $quantity){
        $sql = 'update '.$this->tableName.' '.
            'set inventory = inventory - '.$quantity.' '.
            'where inventory >= '.$quantity.' and shopCommodityId = '.$shopCommodityId;
        $this->db->query($sql);
        if( $this->db->affected_rows() == 0 )
            throw new CI_MyException(1,'扣减库存失败');
    }

    public function getHeaderInfo($userId){
        $this->db->where('userId',$userId);
        $this->db->where('state',1);
        $info = $this->db->get($this->tableName)->result_array();
        $num  = count($info);
        $arr  = array();
        foreach ($info as $key => $value) {
            $createTime = strtotime($value['createTime']);
            if(time() - $createTime < 86400 * 7){
                $arr[] = $value['shopCommodityId'];
            }
        }
        return array(
            'num'=>$num,
            'new'=>count($arr)
            );
    }

    public function mobileGet($userId,$type,$classifyId){
        $this->db->where('state','1');
        $this->db->where('userId',$userId);
        if($classifyId){
            $this->db->where('shopCommodityClassifyId',$classifyId);
        }
        if($type == 'price'){
            $this->db->order_by('price','asc');
        }elseif($type == 'newest'){
            $this->db->order_by('createTime','asc');
        }else{
            $this->db->order_by('sort','asc');
        }
        return $this->db->get($this->tableName)->result_array();
    }
}
