<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OrderStatisticAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('order/orderDb', 'orderDb');
        $this->load->model('order/orderStateEnum', 'orderStateEnum');
	$this->load->model('shop/commodityAo', 'commodityAo');
    }

    private function formatTime($timeStr){
        $time = strtotime($timeStr);
        return date('Y-m-d', $time);
    }

    public function getOrderDayStatistic($userId, $beginTime, $endTime){
        $where['$userId'] = $userId;
        if($beginTime == '' || $endTime == ''){
            $ret = $this->orderDb->search($where, array());    
            $orderPrice = 0;
            foreach($ret['data'] as $order)
                $orderPrice += $order['price'];

            $item = array(
                'day'=>'all',
                'orderNum'=>$ret['count'],
                'orderPrice'=>$this->commodityAo->getFixedPrice($orderPrice)
            );
            $retData = array();
            $retData[] = $item;
            return $retData;
        } else {
            $where['beginTime'] = $beginTime;
            $where['endTime'] = $endTime;
            $ret = $this->orderDb->search($where, array());
            $data = $ret['data'];
            $retData = array();
            foreach($data as $order){
		        if( !isset($retData[ $this->formatTime($order['createTime']) ])){
			        $retData[ $this->formatTime($order['createTime']) ] = array(
				        'day'=>$this->formatTime($order['createTime']),
				        'orderNum'=>1,
				        'orderPrice'=>$this->commodityAo->getFixedPrice($order['price'])
			    );
		        }else{
			        $retData[ $this->formatTime($order['createTime']) ]['orderNum'] ++;
			        $retData[ $this->formatTime($order['createTime']) ]['orderPrice'] += $this->commodityAo->getFixedPrice($order['price']);
		        }
	        }
            return array_values($retData);
        }
    }

    public function getOrderTotalStatistic($userId, $limit){
            $where = array(
                'userId'=>$userId
            );    
            $data = array();
            $where['state'] = $this->orderStateEnum->NO_PAY;
            $retData = $this->orderDb->search($where, $limit);
            $orderPrice = 0;
            foreach($retData['data'] as $order)
                $orderPrice += $order['price'];
            $data[] = array(
                'state'=>$this->orderStateEnum->NO_PAY,
                'stateName'=>'未付款',
                'num'=>$retData['count'],
                'price'=>$orderPrice
            );

            $where['state'] = $this->orderStateEnum->NO_SEND;
            $retData = $this->orderDb->search($where, $limit);
            $orderPrice = 0;
            foreach($retData['data'] as $order)
                $orderPrice += $order['price'];
            $data[] = array(
                'state'=>$this->orderStateEnum->NO_SEND,
                'stateName'=>'已付款',
                'num'=>$retData['count'],
                'price'=>$orderPrice
            );

            $where['state'] = $this->orderStateEnum->HAS_SEND;
            $retData = $this->orderDb->search($where, $limit);
            $orderPrice = 0;
            foreach($retData['data'] as $order)
                $orderPrice += $order['price'];
            $data[] = array(
                'state'=>$this->orderStateEnum->HAS_SEND,
                'stateName'=>'已发货',
                'num'=>$retData['count'],
                'price'=>$orderPrice
            );

            return $data;
    }

}
