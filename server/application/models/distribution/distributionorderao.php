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
        $this->load->model('user/userAo', 'userAo');
    }

    public function getFixedPrice($price){
        return sprintf("%.2f", $price);
    }

    private function getOrderDetailInfo($distributionOrder){
        //取出order信息
        $distributionOrderId = $distributionOrder['distributionOrderId'];
        $distributionOrder['price'] = $this->getFixedPrice($distributionOrder['price']/100);
        $distributionOrder['upUserCompany'] = $this->userAo->get($distributionOrder['upUserId'])['company'];
        $distributionOrder['downUserCompany'] = $this->userAo->get($distributionOrder['downUserId'])['company'];

        //拉出分成订单商品的信息
        $idToCommodity = array();
        $commodity = $this->distributionCommodityAo->get($distributionOrderId);
        foreach($commodity as $singleCommodity )
            $idToCommodity[$singleCommodity['shopCommodityId']] = $singleCommodity; 
        
        //拉出订单原始信息
        $distributionOrder['order'] = $this->orderAo->get($distributionOrder['shopOrderId']);
        foreach($distributionOrder['order']['commodity'] as $key=>$value){
            $shopCommodityId = $value['shopCommodityId'];
            $distributionOrder['order']['commodity'][$key]['distributionPrice'] = $this->getFixedPrice(
                $idToCommodity[$shopCommodityId]['price']
            ); 
            if( $distributionOrder['order']['commodity'][$key]['distributionPrice'] != 0 ){
                $distributionOrder['order']['commodity'] [$key]['distributionPrecent'] = $this->getFixedPrice(
                    $distributionOrder['order']['commodity'] [$key]['distributionPrice']/
                    ($distributionOrder['order']['commodity'] [$key]['priceShow']*
                    $distributionOrder['order']['commodity'] [$key]['quantity']) 
                );
            }else{
                $distributionOrder['order']['commodity'] [$key]['distributionPrecent'] = 0;
            }
        }
        $distributionOrder['shopOrderId'] = $distributionOrder['order']['shopOrderId'];
        $distributionOrder['shopOrderPrice'] = $distributionOrder['order']['priceShow'];
        return $distributionOrder;
    }

    public function search($where, $limit){
        //拉出订单信息
        $data = $this->distributionOrderDb->search($where, $limit);
        
        //拉出分成订单详细信息
        foreach($data['data'] as $key=>$distributionOrder)
            $data['data'][$key] = $this->getOrderDetailInfo($distributionOrder);

        return $data;
    }

    public function get($userId,$distributionOrderId){
        //拉出分成订单基础信息
        $distributionOrder = $this->distributionOrderDb->get($distributionOrderId);
        if($distributionOrder['upUserId'] != $userId && $distributionOrder['downUserId'] != $userId )
            throw new CI_MyException(1,'没有此权限查看此分成订单');

        //拉出分成订单详细信息
        return $this->getOrderDetailInfo($distributionOrder);
    }

    public function payOrder($userId, $distributionOrderId,$commodity){
        //校验订单信息
        $order = $this->distributionOrderDb->get($distributionOrderId);
        if($order['state'] != $this->distributionOrderStateEnum->UN_PAY)
            throw new CI_MyException(1, '只有“未付款”订单才能设置“付款中”状态');
        if($userId != $order['upUserId'])
            throw new CI_MyException(1, '无权操作此分成订单');

        //修改分成订单的各个商品的分成金额
        $totalPrice = 0;
        foreach($commodity as $singleCommodity){
            $this->distributionCommodityAo->mod(
                $distributionOrderId,
                $singleCommodity['shopCommodityId'],
                array('price'=>$singleCommodity['distributionPrice'])
            );
            $totalPrice += $singleCommodity['distributionPrice'];
        }

        //修改分成订单为支付中
        $totalPrice = $totalPrice * 100;
        $this->distributionOrderDb->mod($distributionOrderId, array(
            'state'=>$this->distributionOrderStateEnum->IN_PAY,
            'price'=>$totalPrice
        ));
    }

    public function hasPayOrder($userId, $distributionOrderId){
        //校验订单信息
        $order = $this->distributionOrderDb->get($distributionOrderId);
        if($order['state'] != $this->distributionOrderStateEnum->IN_PAY)
            throw new CI_MyException(1, '只有“付款中”订单才能设置“已付款”状态');
        if($userId != $order['upUserId'])
            throw new CI_MyException(1, '无权操作此分成订单');

        //修改分成订单为已支付
        $this->distributionOrderDb->mod($distributionOrderId, array(
            'state'=>$this->distributionOrderStateEnum->HAS_PAY
        ));
    }

    public function confirm($userId, $distributionOrderId){
        //校验订单信息
        $order = $this->distributionOrderDb->get($distributionOrderId);
        if($order['state'] != $this->distributionOrderStateEnum->HAS_PAY)
            throw new CI_MyException(1, '只有“已付款”订单才能设置“已收款”状态');
        if($userId != $order['downUserId'])
            throw new CI_MyException(1, '无权操作此分成订单');

        //修改分成订单为已确认收款
        $this->distributionOrderDb->mod($distributionOrderId, array(
            'state'=>$this->distributionOrderStateEnum->HAS_CONFIRM
        ));
    }
}
