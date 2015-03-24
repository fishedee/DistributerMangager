<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionCommodityDb extends CI_Model
{
    var $tableName = 't_distribution_commodity';

    public function __construct(){
        parent::__construct();
   }

    public function add($data){
        $this->db->insert($this->tableName, $data);
        return $this->db->insert_id();
    }

    public function mod($distributionCommodityId, $data){
        $this->db->where('distributionCommodityId', $distributionCommodityId);
        $this->db->update($this->tableName, $data);
    }

    public function modByDistributionOrderAndCommodity($distributionOrderId,$shopCommodityId, $data){
        $this->db->where('distributionOrderId', $distributionOrderId);
        $this->db->where('shopCommodityId', $shopCommodityId);
        $this->db->update($this->tableName, $data);
    }

    public function del($distributionCommodityId){
        $this->db->where('distributionCommodityId', $distributionCommodityId);
        $this->db->delete($this->tableName);
    }

    public function get($distributionCommodityId){
        $this->db->where('distributionCommodityId', $distributionCommodityId);
        $query = $this->db->get($this->tableName)->result_array();
        if( count($query) == 0)
            throw new CI_MyException(1, "不存在此分成商品");
        else
            return $query[0];
    }
    
    public function search($where, $limit){
        foreach($where as $key=>$value)
            $this->db->where($key, $value);
        $count = $this->db->count_all_results($this->tableName);

        foreach($where as $key=>$value)
            $this->db->where($key, $value);
        $this->db->order_by('createTime', 'desc');
        
        if(isset($limit['pageIndex']) && isset($limit['pageSize']))
            $this->db->limit($limit['pageSize'], $limit['pageIndex']);
        $query =$this->db->get($this->tableName)->result_array();

        return array(
            'count'=>$count,
            'data'=>$query
        );
    }
}
