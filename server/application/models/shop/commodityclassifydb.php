<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityClassifyDb extends CI_Model
{
    var $tableName = "t_shop_commodity_classify";

    public function __construct(){
        parent::__construct();
    }

    public function search($where, $limit){
        foreach( $where as $key=>$value ){
            $this->db->where($key, $value);
        }

        $count = $this->db->count_all_result($this->tableName);

        foreach( $where as $key=>$value ){
            $this->db->where($key, $value);
        }

        $this->db->order_by('sort', 'asc');

        if( isset($limit["pageIndex"]) && isset($limit["pageSize"]) )
            $this->db->limit($limit["pageSize"], $limit["pageIndex"]);

        $query = $this->db->get($this->tableNome)->result_array();
        return array(
            "count"=>$count,
            "data"=> $query
        );
    }

    public function get($shopCommodityClassifyId){
        $this->db->where("shopCommodityClassifyId", $shopCommodityClassifyId);
        $query = $this->db->get($this->tableName)->result_array();
        if( count($query) == 0)
            throw new CI_MyException('不存在此商品分类');
        return $query[0];
    }

    public function getMaxSortByUser($userId){
        $this->db->select_max('sort');
        $this->db->where('userId', $userId);
        $result = $this->db->get($this->tableName)->result_array();   
        return $result[0]['sort'];
    }

    public function del($shopCommodityClassifyId){
        $this->db->where("shopCommodityClassifyId", $shopCommodityClassify);
        $this->db->delete($this->tableName);
    }

    public function add($data){
        $this->db->insert($this->tableName, $data);
        return $this->db->insert_id();
    }

    public function mod($shopCommodityClassifyId, $data){
        $this->db->where("shopCommodityClassifyId", $shopCommodityClassifyId);
        $this->db->update($this->tableName, $data);
    }
}
