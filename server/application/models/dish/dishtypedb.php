<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class dishTypeDb extends CI_Model{

    private $tableName = 't_dish_type';

    public function __construct(){
        parent::__construct();
    }

    //查看菜品分类
    public function search($dataWhere,$limit){
        // $this->db->where($dataWhere);
        // $count = $this->db->count_all_results($this->tableName);
        if( isset($limit["pageIndex"]) && isset($limit["pageSize"]) ){
            $this->db->limit($limit["pageSize"], $limit["pageIndex"]);
        }
        if(isset($dataWhere['title'])){
            $this->db->like('title',$dataWhere['title'],'both');
        }
        $this->db->where('userId',$dataWhere['userId']);
        $query = $this->db->get($this->tableName)->result_array();
        return array(
            'count'=>count($query),
            'data' =>$query
            );
    }

    public function getAllType($userId,$dishTypeId){
        if($dishTypeId){
            return $this->db->query("SELECT dishTypeId,title FROM t_dish_type WHERE userId={$userId} AND dishTypeId!={$dishTypeId}")->result_array();
        }else{
            return $this->db->query("SELECT dishTypeId,title FROM t_dish_type WHERE userId={$userId}")->result_array();
        }
    }

    //获取分类详细信息
    public function getDetail($dishTypeId){
        $this->db->where('dishTypeId',$dishTypeId);
        return $this->db->get($this->tableName)->result_array()[0];
    }

    //增加菜品分类
    public function addType($userId,$data){
        $data['userId'] = $userId;
        $this->db->insert($this->tableName,$data);
        return $this->db->insert_id();
    }

    //修改菜品分类
    public function modType($dishTypeId,$data){
        $this->db->where('dishTypeId',$dishTypeId);
        $this->db->update($this->tableName,$data);
        return $this->db->affected_rows();
    }

    //删除分类
    public function del($userId,$dishTypeId){
        $this->db->delete($this->tableName,array('dishTypeId'=>$dishTypeId));
        return $this->db->affected_rows();
    }

    //获取分类信息
    public function getTypeInfo($userId){
        $condition['userId'] = $userId;
        $info = $this->db->select('title')->from($this->tableName)->where($condition)->get()->result_array();
        return $info;
    }

    //获取第一个分类
    public function getFirstType($userId){
        $this->db->where('userId',$userId);
        $this->db->order_by('dishTypeId','asc');
        $this->db->select('dishTypeId');
        $this->db->limit(1);
        return $this->db->get($this->tableName)->result_array();
    }

    //前台获取第一个分类
    public function getMenuType($userId){
        $this->db->where('userId',$userId);
        $this->db->order_by('dishTypeId','asc');
        return $this->db->get($this->tableName)->result_array();
    }

    //获取子分类
    public function getChild($parent_id){
        $this->db->where('parent_id',$parent_id);
        return $this->db->get($this->tableName)->result_array();
    }
}
