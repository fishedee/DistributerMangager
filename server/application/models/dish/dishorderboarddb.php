<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DishOrderBoardDb extends CI_Model{

    private $tableName = 't_dish_order_board';

    public function __construct(){
        parent::__construct();
    }

    public function addDishOrderBoard($data){
        $this->db->insert($this->tableName,$data);
        return $this->db->insert_id();
    }

    //获取订单号信息
    public function getOrderNoInfo($boardId){
        $condition['boardId'] = $boardId;
        $condition['start']   = 1;
        return  $this->db->select('orderNo')->from($this->tableName)->where($condition)->get()->result_array();
    }

    //更新
    public function mod($boardId,$data){
        $this->db->where('boardId',$boardId);
        $this->db->update($this->tableName,$data);
        return $this->db->affected_rows();
    }
}
