<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityDb extends CI_Model
{
    var $tableName = "t_shop_commodity";

    public function __construct(){
        parent::__construct();
    }

    public function search($where, $limit){
        foreach($where as $key=>$value){
            if($key == 'title' || $key == 'introduction' ||
                $key == 'detail' || $key == 'detail')
                $this->db->like($key, $value);
            else if($key == 'userId' || $key == 'commodityClassifyId' ||
                $key == 'inventory')
                $this->db->where($key, $value);
        }
        $count = $this->db->count_all_result($this->tableName);

        foreach($where as $key=>$value){
            if($key == 'title' || $key == 'introduction' ||
                $key == 'detail' || $key == 'detail')
                $this->db->like($key, $value);
            else if($key == 'userId' || $key == 'commodityClassifyId' ||
                $key == 'inventory')
                $this->db->where($key, $value);
        }
        $this->db->order_by('sort', 'asc');  

        if(isset($limit['pageIndex'] && isset(limit['pageSize']))
            $this->db->limit($limit['pageSize'], $limit['pageIndex']);
        $query = $this->db->get($this->tableName)->result_array();
        return array(
            'count'=>$count,
            'data'=>$query
        );
    }

    public function get($commodityId){
        $this->db->where("commodityId", $commodityId);    
        $query = $this->db->get($this->tableName)->result_array();
        if(count($query) == 0)
            throw new CI_MyException('不存在此商品');
        return $query[0];
    }

    public function del($commodityId){
        $this->db->where("commodityId", $commodityId);
        $this->db->delete($this->tableName);
    }

    public function add($data){
        $this->db->insert($this->tableName, $data);
        return $this->db->insert_id();
    }

    public function mod($commodityId, $data){
        $this->db->where('commodityId', $commodityId);
        $this->db->update($this->tableName, $data);
    }
}
