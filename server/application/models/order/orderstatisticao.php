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

    public function getOrderDayStatistic($userId, $where){
        //取出原始数据
        $where['userId'] = $userId;
        $ret = $this->orderDb->search($where, array());
        $data = $ret['data'];

        //根据日期聚合数据
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

         
        if( count($retData) == 0)
            return array();

        //填充空日子的数据
        $ret = array();
        $endTime = max(array_keys($retData));
        $beginTime = min(array_keys($retData));

        $time = $endTime;
        while($time >= $beginTime){
            if( isset($retData[$time]) ){
                $ret[] = $retData[$time];
            }else{
                $ret[] = array(
                    'day'=>$time,
                    'orderNum'=>0,
                    'orderPrice'=>0
                );
            }
            $time = date('Y-m-d', strtotime($time . ' -1 day'));
        }

        return $ret;
    }

    public function getOrderTotalStatistic($userId, $where){
            $where['userId'] = $userId; 
            $data = array();
            foreach($this->orderStateEnum->enums as $singleEnum){
                $where['state'] = $singleEnum[0];
                $retData = $this->orderDb->search($where, array());
                $orderPrice = 0.00;
                foreach($retData['data'] as $order)
                    $orderPrice += $order['price'];
                $data[] = array(
                    'state'=>$singleEnum[0],
                    'stateName'=>$singleEnum[2],
                    'num'=>$retData['count'],
                    'price'=>$this->commodityAo->getFixedPrice($orderPrice)
                );
            }
            return $data;
    }

}
