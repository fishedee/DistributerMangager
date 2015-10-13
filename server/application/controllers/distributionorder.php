<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionOrder extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('distribution/distributionorderAo', 'distributionOrderAo');
        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('distribution/distributionOrderStateEnum', 'distributionOrderStateEnum');
        $this->load->model('user/userPermissionEnum', 'userPermissionEnum');
        $this->load->model('order/orderAo', 'orderAo');
        $this->load->library('argv', 'argv');
    }

    /**
     * @view json
     */
    public function getAllState(){
        return $this->distributionOrderStateEnum->names;
    }

    /**
     * @view json
     */
    public function search(){
        //校验参数
        $dataWhere = $this->argv->checkGet(array(
            array('distributionOrderId', 'option'),
            array('direction', 'require'),
            array('state', 'option')
        ));

        $dataLimit = $this->argv->checkGet(array(
            array('pageSize', 'require'),
            array('pageIndex', 'require')
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION
        );
        $userId = $user['userId'];

        if($dataWhere['direction'] == 'up')
            $dataWhere['downUserId'] = $userId;
        else if($dataWhere['direction'] == 'down')
            $dataWhere['upUserId'] = $userId;
        else
            throw new CI_MyException(1, "无效的direction参数");
        unset($dataWhere['direction']);
        $dataWhere['userId'] = $userId;
        //业务逻辑
        return $this->distributionOrderAo->search($dataWhere, $dataLimit);
    }

    /**
     * @view json
     */
    public function get(){
        //校验参数
        $dataWhere = $this->argv->checkGet(array(
            array('distributionOrderId', 'require')
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION
        );
        $userId = $user['userId'];

        //业务逻辑
        return  $this->distributionOrderAo->get($userId,$dataWhere['distributionOrderId']);
    }

    /**
     * @view json
     */
    public function payOrder(){
        //校验参数
        $data = $this->argv->checkPost(array(
            array('distributionOrderId', 'require'),
            array('commodity','option',array())
        ));  

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION
        );

        //业务逻辑
        $this->distributionOrderAo->payOrder($user['userId'], $data['distributionOrderId'],$data['commodity']);
    }

    /**
     * @view json
     */
    public function hasPayOrder(){
        //校验参数
        $data = $this->argv->checkPost(array(
            array('distributionOrderId', 'require')
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION
        );

        //业务逻辑
        $this->distributionOrderAo->HasPayOrder($user['userId'], $data['distributionOrderId']);
    }

    /**
     * @view json
     */
    public function confirm(){
        //校验参数
        $data = $this->argv->checkPost(array(
            array('distributionOrderId', 'require')
        ));  

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION
        );

        //业务逻辑
        $this->distributionOrderAo->confirm($user['userId'], $data['distributionOrderId']);
    }

    /**
     * @view json
     * 获取分销分成
     */
    public function getDistributionPrice(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkMustClient(
                $this->userPermissionEnum->COMPANY_DISTRIBUTION
            );
            $vender = $user['userId'];
            $myUserId = $this->input->get('myUserId');
            return $this->distributionOrderAo->getDistributionPrice($vender,$myUserId);
        }
    }
}

