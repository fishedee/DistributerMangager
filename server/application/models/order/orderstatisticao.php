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
        $str = var_dump($this->orderStateEnum);
        log_message('error', $str);

        $where['$userId'] = $userId;
        if($beginTime != '' && $endTime != '')
            $where['beginTime'] = $beginTime;
            $where['endTime'] = $endTime;
        }

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

    public function getOrderTotalStatistic($userId, $limit){
            $where = array(
                'userId'=>$userId
            );    
            $data = array();
            //foreach($this->orderStateEnum->names as $singleEnum){
            //    $where['state'] = ;
            //    $retData = $this->orderDb->search($where, $limit);
            //    $orderPrice = 0;
            //    foreach($retData['data'] as $order)
            //        $orderPrice += $order['price'];
            //    $data[] = array(
            //        'state'=>$this->orderStateEnum->NO_PAY,
            //        'stateName'=>,
            //        'num'=>$retData['count'],
            //        'price'=>$orderPrice
            //    );
            //}

            return $data;
    }

}
