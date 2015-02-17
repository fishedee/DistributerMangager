<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AddressDb extends CI_Model
{
    var $tableName = 't_address';
    var $tableField = array(
        'clientId'=>'',
        'name'=>'',
        'province'=>'',
        'city'=>'',
        'address'=>'',
        'phone'=>'',
        'payment'=>0,
    );
    public function __construct(){
        parent::__construct();
    }

    public function search($where, $limit){
        foreach($where as $key=>$value){
            if($key == "name" || $key == "province" || $key == "city" ||
                $key == "district" || $key == "address" || $key == "phone")
                $this->db->like($key, $value);
            else if($key == "clientId" || $key == "payment"){
                $this->db->where($key, $value);
            }
        }

        $count = $this->db->count_all_results($this->tableName);

        foreach($where as $key=>$value){
            if($key == "name" || $key == "province" || $key == "city" ||
                $key == "district" || $key == "address" || $key == "phone")
                $this->db->like($key, $value);
            else if($key == "clientId" || $key == "payment"){
                $this->db->where($key, $value);
            }
        }

        $this->db->order_by('createTime', 'desc');

        if(isset($limit["pageIndex"]) && isset($limit["pageSize"]))
            $this->db->limit($limit["pageSize"], $limit["pageIndex"]);

        $query = $this->db->get($this->tableName)->result_array();
        return array(
            "count"=>$count,
            "data"=>$query
        );
    }

    public function getByClientId($clientId){
        $this->db->where("clientId", $clientId);
        return $this->db->get($this->tableName)->result_array();
    }

    public function add($data){
        $data = array_merge($this->tableField,array_intersect_key($data, $this->tableField));
        $this->db->insert($this->tableName, $data);
        return $this->db->insert_id();
    }

    public function modByClientId($clientId,$data){
        $data = array_intersect_key($data, $this->tableField);
        if( count($data) == 0 )
            return;
        $this->db->where("clientId", $clientId);
        $this->db->update($this->tableName, $data);
    }

}
