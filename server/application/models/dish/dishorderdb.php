<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DishOrderDb extends CI_Model{

    private $tableName = 't_dish_order';

    public function __construct(){
        $this->load->model('chips/chipsOrderPayDb','pay');
        $this->load->model('dish/dishOrderStateEnum','dishOrderStateEnum');
        parent::__construct();
    }

    //查看订餐订单
    public function search($dataWhere,$limit,$type){
        foreach ($dataWhere as $key => $value) {
            if($key == 'userId' || $key == 'boardId' || $key == '$boardTypeId' || $key == 'state'){
                $this->db->where($key,$value);
            }else{
                $this->db->like($key,$value,'both');
            }
        }
        $this->db->where('type',$type);
        $query = $this->db->get($this->tableName)->result_array();
        $count = count($query);
        foreach ($dataWhere as $key => $value) {
            if($key == 'userId' || $key == 'boardId' || $key == '$boardTypeId' || $key == 'state'){
                $this->db->where($key,$value);
            }else{
                $this->db->like($key,$value,'both');
            }
        }
        if( isset($limit["pageIndex"]) && isset($limit["pageSize"]) ){
            $this->db->limit($limit["pageSize"], $limit["pageIndex"]);
        }
        $this->db->where('type',$type);
        $this->db->order_by('orderNo','DESC');
        $query = $this->db->get($this->tableName)->result_array();
        // $count = count($query);
        return array(
            'count'=>$count,
            'data' =>$query
            );
    }

    //查询当前就餐的订单
    // public function searchNow($orderNoInfo,$limit){
    //     if( isset($limit["pageIndex"]) && isset($limit["pageSize"]) ){
    //         $this->db->limit($limit["pageSize"], $limit["pageIndex"]);
    //     }
    //     $this->db->where_in('orderNo',$orderNoInfo);
    //     $query = $this->db->get($this->tableName)->result_array();
    //     return array(
    //         'count'=>count($query),
    //         'data' =>$query
    //         );
    // }

    //查询餐桌就餐情况
    public function searchBoard($dataWhere,$limit){
        $arr[] = 0;
        $arr[] = 1;
        $stateSelect = 0;
        $stateArr[] = 0;
        $stateArr[] = 1;
        if(isset($dataWhere['states'])){
            $stateSelect = $dataWhere['states'];
        }
        foreach ($dataWhere as $key => $value) {
            if($key == 'userId' || $key == 'boardId'){
                $this->db->where($key,$value);
            }else{
                $stateSelect = $value;
            }
        }
        if($stateSelect == 1){
            $this->db->where_in('state',$arr);
        }else{
        }
        $this->db->where_in('state',$stateArr);
        $count = $this->db->get($this->tableName)->num_rows();
        if( isset($limit["pageIndex"]) && isset($limit["pageSize"]) ){
            $this->db->limit($limit["pageSize"], $limit["pageIndex"]);
        }
        foreach ($dataWhere as $key => $value) {
            if($key == 'userId' || $key == 'boardId'){
                $this->db->where($key,$value);
            }else{
                $stateSelect = $value;
            }
        }
        if($stateSelect == 1){
            $this->db->where_in('state',$arr);
        }else{
        }
        $this->db->where_in('state',$stateArr);
        $query = $this->db->get($this->tableName)->result_array();
        foreach ($query as $key => $value) {
            $query[$key]['price'] = sprintf('%.2f',$value['price']/100);
            if($value['state'] == 0 || $value['state'] == 1){
                $query[$key]['states'] = 1;
            }else{
                $query[$key]['states'] = $stateSelect;
            }
        }
        return array(
            'count'=>$count,
            'data' =>$query,
            );
    }

    //计算总价
    public function cal($dataWhere,$limit,$states){
        $sum = 0;
        $pageSum = 0;
        $nowSum = 0;
        //计算总的价格
        $condition['boardId'] = $dataWhere['boardId'];
        $condition['userId']  = $dataWhere['userId'];
        $query = $this->db->select('price,state')->from($this->tableName)->where($condition)->get()->result_array();
        foreach ($query as $key => $value) {
            if($value['state'] == $this->dishOrderStateEnum->HAS || $value['state'] == $this->dishOrderStateEnum->CLOSE || $value['state'] == $this->dishOrderStateEnum->CHECK){
                $sum += $value['price'];
                if($value['state'] == $this->dishOrderStateEnum->HAS){
                    $nowSum += $value['price'];
                }
            }
        }
        //计算当前页面的价格
        if( isset($limit["pageIndex"]) && isset($limit["pageSize"]) ){
            $this->db->limit($limit["pageSize"], $limit["pageIndex"]);
        }
        $this->db->where('userId',$dataWhere['userId']);
        $this->db->where('boardId',$dataWhere['boardId']);
        $arr[] = $this->dishOrderStateEnum->NOT;
        $arr[] = $this->dishOrderStateEnum->HAS;
        if($states == 1){
            $this->db->where_in('state',$arr);
        }
        $this->db->select('price,state');
        $query = $this->db->get($this->tableName)->result_array();
        foreach ($query as $key => $value) {
            if($value['state'] == $this->dishOrderStateEnum->HAS || $value['state'] == $this->dishOrderStateEnum->CLOSE || $value['state'] == $this->dishOrderStateEnum->CHECK){
                $pageSum += $value['price'];
            }
        }
        return array(
            'sum' => sprintf('%.2f',$sum/100),
            'pageSum' => sprintf('%.2f',$pageSum/100),
            'nowSum' => sprintf('%.2f',$nowSum/100)
            );
    }

    //订单编号的唯一性
    public function checkOrderNo($orderNo){
        $this->db->where('orderNo',$orderNo);
        $result = $this->db->get($this->tableName)->result_array();
        if($result){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    //获取订单信息
    public function getOrderInfo($orderNo){
        $result = $this->checkOrderNo($orderNo);
        if($result == FALSE){
            $this->db->where('orderNo',$orderNo);
            $orderInfo = $this->db->get($this->tableName)->result_array();
            return $orderInfo[0];
        }else{
            throw new CI_MyException(1,'无效订单号');
        }
    }

    //插入订单
    public function addOrderOnce($data){
        $this->db->insert($this->tableName,$data);
        return $data['orderNo'];
    }

    //更新订单
    public function mod($orderNo,$data){
        $data['modifyTime'] = date('Y-m-d H:i:s',time());
        $this->db->where('orderNo',$orderNo);
        $this->db->update($this->tableName,$data);
        return $this->db->affected_rows();
    }

    //微信统一下单
    public function addWxOrder($userId,$clientId,$orderInfo){
        //提交到微信的orderno
        $orderNo = $orderInfo['orderNo'];
        $total_fee = $orderInfo['price'];
        $wxorderInfo = $this->pay->wxPay($userId,$clientId,$orderNo,'buy_some',$total_fee);
        $data['wxPrePayId'] = $wxorderInfo['prepay_id'];
        $this->mod($userId,$data);
        return $wxorderInfo['prepay_id'];
    }

    //餐桌结账
    public function closeAccounts($userId,$boardId){
        $this->db->where('userId',$userId);
        $this->db->where('boardId',$boardId);
        $this->db->where('state',$this->dishOrderStateEnum->HAS);
        $data['state'] = $this->dishOrderStateEnum->CHECK;
        $this->db->update($this->tableName,$data);
        return $this->db->affected_rows();
    }

    //我的订单
    public function myOrder($userId,$clientId){
        $this->db->where('userId',$userId);
        $this->db->where('clientId',$clientId);
        $this->db->order_by('createTime','DESC');
        return $this->db->get($this->tableName)->result_array();
    }

    //获取该餐桌的订单信息
    public function getBoardOrderInfo($userId,$boardId){
        $this->db->where('userId',$userId);
        $this->db->where('boardId',$boardId);
        return $this->db->get($this->tableName)->result_array();
    }
}
