<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DishOrderDetailDb extends CI_Model{

    private $tableName = 't_dish_order_detail';

    public function __construct(){
        parent::__construct();
    }

    //增加订单明细
    public function addOrderDetail($data){
        $arr = array();
        foreach ($data as $key => $value) {
            $this->db->insert($this->tableName,$value);
            if($this->db->insert_id()){
                $arr[] = $this->db->insert_id();
            }
        }
        return count($arr);
    }

    //获取订单明细信息
    public function getOrderDetailInfo($orderNo){
        $sql = "SELECT t_dish_order_detail.*,t_dishes.dishName,t_dishes.dishPrice from t_dish_order_detail INNER JOIN t_dishes ON t_dish_order_detail.dishId = t_dishes.dishId WHERE orderNo = {$orderNo}";
        // $this->db->where('orderNo',$orderNo);
        // return $this->db->get($this->tableName)->result_array();
        return $this->db->query($sql)->result_array();
    }

    //修改订单明细
    public function mod($orderDetailId,$data){
        $this->db->where('orderDetailId',$orderDetailId);
        $this->db->update($this->tableName,$data);
        return $this->db->affected_rows();
    }

    //检测订单明细
    public function checkOrderDetail($orderNo){
        $this->db->where('orderNo',$orderNo);
        $this->db->where('comment',1);
        return $this->db->get($this->tableName)->result_array();
    }
}
