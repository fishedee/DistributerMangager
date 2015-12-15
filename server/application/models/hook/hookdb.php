<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class HookDb extends CI_Model{

	private $tableName = 't_hook';

    public function __construct(){
        parent::__construct();
    }

    public function search($dataWhere,$limit){
        if( isset($limit["pageIndex"]) && isset($limit["pageSize"]) ){
            $this->db->limit($limit["pageSize"], $limit["pageIndex"]);
        }
        foreach ($dataWhere as $key => $value) {
        	if($key != 'userId'){
        		$this->db->like($key,$value,'both');
        	}
        }
        $query = $this->db->get($this->tableName)->result_array();
        return array(
            'count'=>count($query),
            'data' =>$query
        );
    }

    //增加
    public function add($data){
        $this->db->insert($this->tableName,$data);
        return $this->db->insert_id();
    }

    //修改
    public function mod($hookId,$data){
        $data['modifyTime'] = date('Y-m-d H:i:s',time());
        $this->db->where('hookId',$hookId);
        $this->db->update($this->tableName,$data);
        return $this->db->affected_rows();
    }

    //获取插件详情
    public function getHook($hookId){
        $this->db->where('hookId',$hookId);
        return $this->db->get($this->tableName)->result_array();
    }

    //检测权限
    public function checkPower($hookId,$userId){
        $arr[] = $userId;
        $arr   = json_encode($arr);
        $sql = "SELECT hookId FROM t_hook WHERE hookId = '{$hookId}' AND (hookPower = '{$arr}' OR hookPower LIKE '%{$userId}%')";
        return $this->db->query($sql)->result_array();
    }

    public function getUserHook($userId){
        $this->db->like('hookPower',$userId,'both');
        return $this->db->get($this->tableName)->result_array();
    }
}
