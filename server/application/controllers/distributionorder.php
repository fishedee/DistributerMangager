<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('distribution/distributionorderAo', 'distributionOrderAo');
        $this->load->library('argv', 'argv');
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

        $this->distributionOrderAo->payOrder($data['distributionOrderId']);
    }

    /**
     * @view json
     */
    public function HasPayOrder(){
        $data = $this->argv->checkPost(array(
            array('distributionOrderId', 'require')
        ));

        $this->distributionOrderAo->HasPayOrder($data['distributionOrderId']);
    }

    /**
     * @view json
     */
    public function confirm(){
        $data = $this->argv->checkPost(array(
            array('distributionOrderId', 'require')
        ));  

        $this->distributionOrderAo->confirm($data['distributionOrderId']);
    }

    /**
     * @view json
     */
    public function del(){
        $data = $this->argv->checkPost(array(
            array('distributionOrderId', 'require')
        ));
        $this->distributionAo->del($data['distributionOrderId']);
    }
}

