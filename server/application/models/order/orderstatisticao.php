<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OrderStaticAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('order/orderDb', 'orderDb');
        $this->load->model('order/orderStateEnum', 'orderStateEnum');
    }

    private function formatTime($timeStr){
        $time = strtotime($timeStr);
        return date('Y-m-d', $time);
    }

    public function getOrderDayStatistic($useId, $beginTime, $endTime){
        $where['$userId'] = $userId;
        if($beginTime == '' || $endTime == ''){
            $ret = $this->orderDb->search($where, array());    
            $orderPrice = 0;
            for($ret['data'] as $order)
                $orderPrice += $order['price'];

            $item = array(
                'day'=>'all',
                'orderNum'=>$ret['count'],
                'orderPrice'=>$orderPrice
            );
            $retData = array();
            $retData[] = $item;
            return $retData;
        } else {
            $where['beginTime'] = $beginTime;
            $where['endTime'] = $endTime;
            $ret = $this->orderDb->search($where, array());
            $data = $ret['data'];
            $item = array(
                'day'=>formatTime( $data[0]['createTime'] ),
                'orderNum'=>0,
                'orderPrice'=>0
            );
            $retData = array();
            for($data as $key=>$order){
                if (formatTime( $order['createTime'] ) == $item['day']){
                    $item['orderNum']++;
                    $item['orderPrice'] += $order['price'];
                }else{
                    $retData[] = $item;
                    $item['day'] = formatTime($order['createTime']);
                    $item['orderNum'] = 1;
                    $item['orderPrice'] = $order['price'];
                }
            }
            return $retData;
        }
    }
}
