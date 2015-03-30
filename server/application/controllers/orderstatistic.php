<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OrderStatistic extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('user/userPermissionEnum', 'userPermissionEnum');
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

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];

        //业务逻辑
        return $this->orderStatisticAo->getOrderDayStatistic($userId,$dataWhere);
    }

    /**
    * @view json
    */
    public function getOrderTotalStatistic(){
        //检查输入参数
        $dataWhere = $this->argv->checkGet(array(
            array('beginTime', 'option'),
            array('endTime', 'option')
        ));
        
        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
        
        //业务逻辑
        return $this->orderStatisticAo->getOrderTotalStatistic($userId,$dataWhere);
    }
}

