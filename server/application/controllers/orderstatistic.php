<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OrderStatistic extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('order/orderStatisticAo', 'orderStatisticAo');
        $this->load->library('argv', 'argv');
    }


    /**
    * @view json
    */
    public function getOrderDayStatistic(){
        //检查输入参数
        $dataWhere = $this->argv->checkGet(array(
            array('beginTime', 'option'),
            array('endTime', 'option')
        ));

        if( isset($dataWhere['beginTime']) )
            $beginTime = $dataWhere['beginTime'];
        else
            $beginTime = '';

        if( isset($dataWhere['endTime']) )
            $endTime = $dataWhere['endTime'];
        else
            $endTime = '';

        //检查权限
        $user = $this->loginAo->checkMustLogin();
        $userId = $user['userId'];

        //业务逻辑
        return $this->orderStatisticAo->getOrderDayStatistic($userId,
            $beginTime, $endTime, array());
    }
}

