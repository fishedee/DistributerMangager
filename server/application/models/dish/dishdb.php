<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class dishDb extends CI_Model{

	private $tableName = 't_dishes';

    private $state     = array(
        'ON' => 1,
        'OFF'=> 0
        );

    public function __construct(){
        parent::__construct();
    }

    //查询菜品
    public function search($dataWhere,$dataLimit){
        if( isset($limit["pageIndex"]) && isset($limit["pageSize"]) ){
            $this->db->limit($limit["pageSize"], $limit["pageIndex"]);
        }
        foreach ($dataWhere as $key => $value) {
        	if($key == 'userId'){
        		$this->db->where('userId',$dataWhere['userId']);
        	}else{
        		$this->db->like($key,$value,'both');
        	}
        }
        $query = $this->db->get($this->tableName)->result_array();
        return array(
            'count'=>count($query),
            'data' =>$query
            );
    }

    //增加菜品
    public function add($userId,$data){
    	$data['userId'] = $userId;
    	$this->db->insert($this->tableName,$data);
    	return $this->db->insert_id();
    }

    //编辑菜品
    public function mod($dishId,$data){
        $data['modifyTime'] = date('Y-m-d H:i:s',time());
    	$this->db->where('dishId',$dishId);
    	$this->db->update($this->tableName,$data);
    	return $this->db->affected_rows();
    }

    //检测userId
    public function checkUserId($dishId){
    	return $this->db->select('userId')->from($this->tableName)->where('dishId',$dishId)->get()->result_array();
    }

    //获取菜品信息
    public function getDish($dishId){
    	$this->db->where('dishId',$dishId);
    	$dishInfo = $this->db->get($this->tableName)->result_array();
    	return $dishInfo;
    }

    //首页获取菜品信息
    public function getIndexDish($userId,$dishId){
        $this->db->select('dishId,dishName,dishPrice,thumb_icon')->from($this->tableName)->where(array('dishId'=>$dishId))->get()->result_array();
        return $dishInfo;
    }

    //更改菜品的上下架状态
    public function modState($dishId,$data){
    	$this->db->where('dishId',$dishId);
    	$this->db->update($this->tableName,$data);
    	return $this->db->affected_rows();
    }

    //删除菜品
    public function del($dishId){
    	$this->db->where('dishId',$dishId);
    	$this->db->delete($this->tableName);
    	return $this->db->affected_rows();
    }

    //前台获取菜单
    public function getMenu($userId,$dish_type_id){
        $this->db->where('userId',$userId);
        $this->db->where('dishTypeId',$dish_type_id);
        $this->db->where('state',$this->state['ON']);
        $this->db->order_by('dishId','asc');
        $this->db->select('dishId,dishPrice,dishName,thumb_icon');
        return $this->db->get($this->tableName)->result_array();
    }

    public function commentGetDish($dishId){
        $this->db->where('dishId',$dishId);
        $this->db->select('dishName');
        $this->db->select('thumb_icon');
        return $this->db->get($this->tableName)->result_array();
    }
}
