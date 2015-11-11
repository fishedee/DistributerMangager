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
        // var_dump($distributionOrder);die;
        return $distributionOrder;
    }

    public function search($where, $limit){
        if(isset($where['userId'])){
            $userId = $where['userId'];
            unset($where['userId']);
            //判断是否为厂家
            $result = $this->userPermissionDb->checkPermissionId($userId,$this->userPermissionEnum->VENDER);
            if($result){
                $vender = $userId;
            }else{
                $vender = 0;
            }
            //拉出订单信息
            $data = $this->distributionOrderDb->search($where, $limit,$vender);
        }else{
            //拉出订单信息
            $data = $this->distributionOrderDb->search($where, $limit);
        }
        
        //拉出分成订单详细信息
        foreach($data['data'] as $key=>$distributionOrder){
            $data['data'][$key] = $this->getOrderDetailInfo($distributionOrder);
            $distribution = $this->distributionAo->get($distributionOrder['vender'],$distributionOrder['distributionId']);
            $data['data'][$key]['distributionPercent'] = $this->getFixedPrice($distributionOrder['price']/$data['data'][$key]['shopOrderPrice']);
        }
        // var_dump($data);die;
        return $data;
    }

    public function get($userId,$distributionOrderId){
        //拉出分成订单基础信息
        $distributionOrder = $this->distributionOrderDb->get($distributionOrderId);
        if($userId != $distributionOrder['vender']){
            if($distributionOrder['upUserId'] != $userId && $distributionOrder['downUserId'] != $userId )
                throw new CI_MyException(1,'没有此权限查看此分成订单');
        }

        //拉出分成订单详细信息
        return $this->getOrderDetailInfo($distributionOrder);
    }

    public function payOrder($userId, $distributionOrderId,$commodity){
        //校验订单信息
        $order = $this->distributionOrderDb->get($distributionOrderId);
        if($order['state'] != $this->distributionOrderStateEnum->UN_PAY)
            throw new CI_MyException(1, '只有“未付款”订单才能设置“付款中”状态');
        if($userId != $order['vender']){
            if($userId != $order['upUserId'])
                throw new CI_MyException(1, '无权操作此分成订单');
        }

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

        //更新用户信息下的用户余额
        $downUserId = $order['downUserId'];
        $info = $this->distributionOrderDb->getDistributionPrice($userId,$downUserId);
        $sales= 0;
        $fall = 0;
        foreach ($info as $key => $value) {
            $shopOrder = $this->orderAo->get($value['shopOrderId']);
            $sales += $shopOrder['price'];
            $fall  += $value['price'];
        }
        $downUserInfo = $this->userAo->get($downUserId);
        $clientId     = $downUserInfo['clientId'];
        $this->load->model('client/clientAo');
        $data['sales'] = $sales;
        $data['fall']  = $fall;
        $this->clientAo->mod($userId,$clientId,$data);

        //写入账户明细
        $this->load->model('withdraw/moneyLogDb','moneyLogDb');
        //写入账户明细
        $data = array();
        $data['vender'] = $userId;
        $data['money']  = $totalPrice;
        $data['dis']    = 1;
        $data['clientId'] = $clientId;
        $data['remark'] = '分销分成';
        $data['createTime'] = date('Y-m-d H:i:s',time());
        $this->moneyLogDb->add($data);
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

    //获取分销分成
    public function getDistributionPrice($vender,$myUserId){
        $info = $this->distributionOrderDb->getDistributionPrice($vender,$myUserId);
        $sales= 0;
        $fall = 0;
        foreach ($info as $key => $value) {
            $shopOrder = $this->orderAo->get($value['shopOrderId']);
            $sales += $shopOrder['price'];
            $fall  += $value['price'];
        }
        return array(
            'sales'=>sprintf('%.2f',($sales/100)),
            'fall' =>sprintf('%.2f',($fall/100))
            );
    }

    //获取应付佣金
    public function getNeedPay($vender){
        $info = $this->distributionOrderDb->getNeedPay($vender);
        $sum  = 0;
        foreach ($info as $key => $value) {
            $sum += $value['price'];
        }
        return $sum;
    }

    //根据订单号 查询分成订单
    public function getDistributionOrder($shopOrderId){
        return $this->distributionOrderDb->getDistributionOrder($shopOrderId);
    }

    //更新
    public function mods($distributionOrderId,$data){
        return $this->distributionOrderDb->mods($distributionOrderId,$data);
    }

    //获取全部
    public function getAllSales($vender){
        $info = $this->distributionOrderDb->getAllSales($vender);
        return $info;
    }

    public function getFall($downUserId){
        return $this->distributionOrderDb->getFall($downUserId);
    }
}
