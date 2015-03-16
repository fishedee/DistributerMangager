<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionOrderAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('distribution/distributionorderDb', 'distributionOrderDb');
        $this->load->model('distribution/distributionAo', 'distributionAo');
        $this->load->model('distribution/distributionCommodityAo', 
            'distributionCommodityAo');
        $this->load->model('order/orderAo', 'orderAo');
    }

    public function getFixedPrice($price){
        return sprintf("%.2f", $price/100);
    }

    public function search($userId, $where, $limit){
        $where['upUserId'] = $userId;
        $where['downUserId'] = $userId;

        $data = $this->distributionOrderDb->search($where, $limit);
        foreach($data['data'] as $key=>$value)
            $data['data'][$key]['priceShow'] = $this->getFixedPrice($value['price']);
        return $data;
    }

    public function get($distributionOrderId){
        $distributionOrder = $this->distributionOrderDb->get($distributionOrderId);
        $order = $this->orderAo->get($distributionOrder['shopOrderId']);
        $commodity = $this->distributionCommodityAo->get($distributionOrderId);
        $data = array(
            'distributionOrder'=>$distributionOrder,
            'shopOrder'=>$order,
            'commodity'=>$commodity
        );
        return $data;
    }


    public function check($data){
        if( isset($data['price']) ){
            if($data['price'] < 0)
        	    throw new CI_MyException(1, '价格不能少于0');       
        }

        if( isset($data['state']) ){
            if($data['state'] < 0 || $data['state'] > 3)
                throw new CI_MyException(1, '非法状态参数');
        }
    }

    public function add($upUserId, $downUserId, $data){
        $where = array(
            'upUserId'=>$upUserId,
            'downUserId'=>$downUserId
        );
        
        $response = $this->distributionAo->search($where, array());
        if($response['count'] == 0)
            throw new CI_MyException(1, '用户间不存在分成关系');
        $this->check($data);
	    $data['price'] = $data['price']*100;
        $this->distributionOrderDb->add($upUserId, $downUserId, $data);
    }

    public function mod($distributionOrderId, $data){
	if( isset($data['price']) ){
            $this->check($data);
        	$data['price'] = $data['price']*100;   
	}
        $this->distributionOrderDb->mod($distributionOrderId, $data);
    }

    public function payOrder($userId, $distributionOrderId){
        $order = $this->distributionOrderDb->get($distributionOrderId);
        if($order['state'] != $this->distributionOrderStateEnum->UN_PAY)
            throw new CI_MyException(1, '此分成订单不能设置付款中状态');
        if($userId != $order['upUserId'])
            throw new CI_MyException(1, '无权操作此分成订单');

        $commodity = $this->distributionCommodityAo->get($distributionOrderId);
        $price = 0;
        foreach($commodity as $value)
            $price += $value['price'];
        $data = array(
            'state'=>$this->distributionOrderStateEnum->IN_PAY,
            'price'=>$price
        );
        $this->mod($distributionOrderId, $data);
    }

    public function hasPayOrder($userId, $distributionOrderId){
        $order = $this->distributionOrderDb->get($distributionOrderId);
        if($order['state'] != $this->distributionOrderStateEnum->IN_PAY)
            throw new CI_MyException(1, '此分成订单不能设置已付款状态');
        if($userId != $order['upUserId'])
            throw new CI_MyException(1, '无权操作此分成订单');
        $data = array(
            'state'=>$this->distributionOrderStateEnum->HAS_PAY
        );
        $this->mod($distributionOrderId, $data);
    }

    public function confirm($userId, $distributionOrderId){
        $order = $this->distributionOrderDb->get($distributionOrderId);
        if($order['state'] != $this->distributionOrderStateEnum->HAS_PAY)
            throw new CI_MyException(1, '此分成不能设置确认付款状态');
        if($userId != $order['downUserId'])
            throw new CI_MyException(1, '无权操作此分成订单');
        $data = array(
            'state'=>$this->distributionOrderStateEnum->HAS_CONFIRM
        );
        $this->mod($distributionOrderId, $data);
    }
     
    public function del($distributionOrderId){
        $this->distributionOrderDb->del($distributionOrderId);
    }
}
