<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OrderDb extends CI_Model
{
    var $tableName = 't_shop_order';

    public function __construct(){
        parent::__construct();
    }

    public function search($where, $limit){
        foreach($where as $key=>$value){
            if($key == 'shopOrderNo')
                $this->db->like($key, $value); 
            else
                $this->db->where($key, $value);
        }   
        $this->db->order_by('shopOderNo', 'asc');
        
        if(isset($limit['pageIndex']) && isset($limit['pageSize']))
            $this->db->limit($limit['pageSize'], $limit['pageIndex']);
        $query = $this->db->get($this->tableName)->result_array();
        return $query;
    }

    public function getByOrderNo($shopOrderNo){
        $this->db->where('shopOrderNo', $shopOrderNo);
        $query = $this->db->get($this->tableName)->result_array();
        if(count($query) == 0)
            throw new CI_MyException(1, '不存在此订单');
        return $query;
    }

    public function getByUserId($userId){
        $this->db->where('userId', $userId);
        $query = $this->db->get($this->tableName)->result_array();
        if(count($query) == 0)
            throw new CI_MyException(1, '用户不拥有订单');
        return $query;
    }

    public function del($shopOrderNo){
        $this->db->where('shopOrderNo', $shopOrderNo);
        $this->db->delete($this->tableName);
    }

    public function getMaxNoByUser($userId){
        $this->db_select_max('shopOrderNo');
        $this->db->where('userId', $userId);
        $result = $this->db->get($this->tableName)->result_array();
        return $result[0]['shopOrderNo'];
    }
}

