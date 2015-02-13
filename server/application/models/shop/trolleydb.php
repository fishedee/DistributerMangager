<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TrollerDb extend CI_Model
{
    var $tableName = 't_shop_troller';

    public function __construct(){
        parent::__construct();
    }   

    public function getByUserId($userId){
        $this->db->where('userId', $userId);
        $query = $this->db->get($this->tableName)->result_array();
        if(count($query) == 0)
            throw new CI_MyException(1, '购物车为空');
        return $query;
    }

    public function getByUserIdClientId($userId, $clientId){
        $this->db->where('userId', $userId);
        $this->db->where('clientId', $clientId);
        $query= $this->db->get($this->tableName)->result_array();
        if(count($query) == 0)
            throw new CI_MyException(1, '购物车为空');
        return $query;
    }

    public function del($shopTrollerId){
        $this->db->where('shopTrollerId', $shopTrollerId);
        $this->db->delete($this->tableName);
    }

    public function add($data){
        $this->db->insert($this->tableName, $data);
        return $this->db->insert_id();
    }
}
