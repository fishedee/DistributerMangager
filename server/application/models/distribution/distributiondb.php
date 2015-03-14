<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityDb extends CI_Model
{
    var $tableName = 't_distribution';

    public function __construct(){
        parrent::__construct();
    }

    public function add($upUserId, $downUserId, $state){
        $data = array(
            'upUserId'=>$upUserId,
            'downUserId'=>$downUserId,
            'state'=>$state
        );

        $this->db->insert($this->tableName, $data);
        return $this->db->insert_id();
    }

    public function mod($distributionId, $data){
        $this->db->where('distributionId', $distritionId);
        $this->db->update($this->tableName, $data);
    }

    public function del($distributionId){
        $this->db->where('distributionId', $distributionId);
        $this->db->delete($this->tableName);
    }

    public function get($distributionId){
        $this->db->where('distributionId', $distributionId);
        $query = $this->db->get($this->tableName)->result_array();
        if(count($query) == 0)
            throw new CI_MyException(1, "不存在此条关系");
        else
            return $query[0];
    }

    public function getDownUser($userId){
        $this->db->where('upUserId', $userId);
        $query = $this->db->get($this->tableName)->result_array();
        return $query;
    }

    public function getUpUser($userId){
        $this->db->where('downUserId', $userId);
        $query = $this->db->get($this->tableName)->result_array();
        return $query;
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
            if($key == 'upUserId' || $key == 'downUserId')
                $this->db->where($key, $value);
            else if($key == 'state')
                $this->db->where($key, $value);
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
}
