<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserDb extends CI_Model
{
    var $tableName = 't_address';

    public function __construct(){
        parent::__construct();
    }

    public function search($where, $limit){
        foreach($where as $key=>$value){
            if($key == "name" || $key == "province" || $key == "city" ||
                $key == "district" || $key == "address" || $key == "phone")
                $this->db->like($key, $value);
            else if($key == "userId" || $key == "payment"){
                $this->db->where($key, $value);
            }
        }

        $count = $this->db->count_all_results($this->tableName);

        foreach($where as $key=>$value){
            if($key == "name" || $key == "province" || $key == "city" ||
                $key == "district" || $key == "address" || $key == "phone")
                $this->db->like($key, $value);
            else if($key == "userId" || $key == "payment"){
                $this->db->where($key, $value);
            }
        }

        $this->db->order_by('createTime', 'desc');

        if(isset($limit["pageIndex"] && isset($limit["pageSize"]))
            $this->db->limit($limit["pageSize"], $limit["pageIndex"]);

        $query = $this->db->get($this->tableName)->result_array();
        return array(
            "count"=>$count,
            "data"=>$query
        );
    }

    public function get($addressId){
        $this->db->where("addressId", $addressId);
        $query = $this->db->get($this->tableName)->result_array();
        if(count($query) == 0)
            throw new CI_MyException('不存在此地址信息');
        return $query[0];
    }

    public function del($addressId){
        $this->db->where('addressId', $addressId);
        $this->db->delete($this->tableName);
    }

    public function add($data){
        $this->db->insert($this->tableName, $data);
        return $this->db->insert_id();
    }

    public function mod($addressId, $data){
        $this->db->where("addressId", $addressId);
        $this->db->update($this->tableName, $data);
    }

    public function getByUserId($userId){
        $this->db->where("userId", $userId);
        $query = $this->db->get($this->tableName)->result_array();
        if(count($query) == 0)
            throw new CI_MyException('不存在此用户的地址信息');
        return $query[0];
    } 

}
