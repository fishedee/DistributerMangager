<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionOrder extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('distribution/distributionorderAo', 'distributionOrderAo');
        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('distribution/distributionOrderStateEnum', 'distributionOrderStateEnum');
        $this->load->model('distribution/distributionCommodityAo', 'distributionCommodityAo');
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
        $dataWhere = $this->argv->checkGet(array(
            array('distributionOrderId', 'option'),
            array('upUserId', 'option'),
            array('downUserId', 'option'),
            array('price', 'option'),
            array('state', 'option')
        ));

        $dataLimit = $this->argv->checkGet(array(
            array('pageSize', 'require'),
            array('pageIndex', 'require')
        ));

        return $this->distributionOrderAo->search($dataWhere, $dataLimit);
    }

    /**
     * @view json
     */
    public function get(){
        $dataWhere = $this->argv->checkGet(array(
            array('distributionOrderId', 'require')
        );
        $distributionOrder = $this->distributionOrderAo->get($dataWhere['distributionOrderId']);
        $shopOrder = $this->orderAo->get($distributionOrder['shopOrderId']);
        $distributionCommodity = $this->distributionCommodityAo->get($dataWhere['distributionOrderId']);
        foreach($distributionCommodity as $key=>$value){
            foreach($shopOrder['commodity'] as $commodity){
                if($commodity['shopCommodityId'] == $value['shopCommodityId'])
                    $distributionCommodity[$key]['shopCommodityId'] = $commodity['shopCommodityId'];
                    $distributionCommodity[$key]['title'] = $commodity['shopCommodityId'];
                    $distributionCommodity[$key]['icon'] = $commodity['icon'];
                    $distributionCommodity[$key]['priceShow'] = $commodity['priceSho'];
                    $distributionCommodity[$key]['quantity'] = $commodity['quantity'];
            }  
        }

        return array(
            'distributionOrder'=>$distributionOrder,
            'distributionCommodity'=>$distributionCommodity
        )
    }

    /**
     * @view json
     * @test
     */
    public function add(){
        //检查参数
        $data = $this->argv->checkPost(array(
            array('upUserId', 'require'),
            array('downUserId', 'require'),
            array('shopOrderId', 'require'),
            array('price', 'require'),
            array('state', 'require')
        ));
            
        $upUserId = $data['upUserId'];
        $downUserId = $data['downUserId'];
        unset($data['upUserId']);
        unset($data['downUserId']);
        $this->distributionOrderAo->add($upUserId, $downUserId, $data);
    }

    /**
     * @view json
     */
    public function mod(){
        $data = $this->argv->checkPost(array(
            array('distributionOrderId', 'require'),
            array('shopOrderId', 'require'),
            array('price', 'require')
        ));

        $distributionOrderId = $data['distributionOrderId'];
        $this->distributionOrderAo->mod($distributionOrderId, $data);
    }

    /**
     * @view json
     */
    public function payOrder(){
        $data = $this->argv->checkPost(array(
            array('distributionOrderId', 'require')
        ));  
        $user = $this->loginAo->checkMustLogin();

        $this->distributionOrderAo->payOrder($user['userId'], $data['distributionOrderId']);
    }

    /**
     * @view json
     */
    public function hasPayOrder(){
        $data = $this->argv->checkPost(array(
            array('distributionOrderId', 'require')
        ));

        $user = $this->loginAo->checkMustLogin();
        $this->distributionOrderAo->HasPayOrder($user['userId'], $data['distributionOrderId']);
    }

    /**
     * @view json
     */
    public function confirm(){
        $data = $this->argv->checkPost(array(
            array('distributionOrderId', 'require')
        ));  

        $user = $this->loginAo->checkMustLogin();
        $this->distributionOrderAo->confirm($user['userId'], $data['distributionOrderId']);
    }

    /**
     * @view json
     */
    //public function del(){
    //    $data = $this->argv->checkPost(array(
    //        array('distributionOrderId', 'require')
    //    ));
    //    $this->distributionAo->del($data['distributionOrderId']);
    //}
}

