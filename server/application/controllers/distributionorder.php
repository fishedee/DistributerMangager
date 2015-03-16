<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionOrder extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('distribution/distributionorderAo', 'distributionOrderAo');
        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('distribution/distributionOrderStateEnum', 'distributionOrderStateEnum');
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
	    array('direction', 'require'),
            array('state', 'option')
        ));

        $dataLimit = $this->argv->checkGet(array(
            array('pageSize', 'require'),
            array('pageIndex', 'require')
        ));

        $user = $this->loginAo->checkMustLogin();
	$userId = $user['userId'];

	if($dataWhere['direction'] == 'up')
	     $dataWhere['downUserId'] = $userId;
        else if($dataWhere['direction'] == 'down')
	    $dataWhere['upUserId'] = $userId;
        else
	    throw new CI_MyException(1, "无效的direction参数");
	unset($dataWhere['direction']);

        return $this->distributionOrderAo->search($userId, $dataWhere, $dataLimit);
    }

    /**
     * @view json
     */
    public function get(){
        $dataWhere = $this->argv->checkGet(array(
            array('distributionOrderId', 'require')
        ));
        $distributionOrder = $this->distributionOrderAo->get($dataWhere['distributionOrderId']);
   	return $distributionOrder;     
    }

    /**
     * @view json
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
            array('distributionOrderId', 'require'),
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
}

