<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Troller extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('shop/trollerAo', 'trollerAo');
        $this->load->model('client/clientLoginAo', 'clientLoginAo');
        $this->load->library('argv', 'argv');
    }

	/**
	* @view json
	*/
    public function get(){
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('userId', 'require')
        ));
        $userId = $data['userId'];

         //检查权限
        $client = $this->clientLoginAo->checkMustLogin($userId);
        $clientId = $client['clientId'];

        //业务逻辑
        return $this->trollerAo->getAll($clientId);
    }

    /**
    * @view json
    */
    public function check(){
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('userId', 'require')
        ));
        $userId = $data['userId'];

         //检查权限
        $client = $this->clientLoginAo->checkMustLogin($userId);
        $clientId = $client['clientId'];

        //业务逻辑
        return $this->trollerAo->checkAll($clientId);
    }

    /**
    * @view json
    */
    public function refresh(){
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('userId', 'require')
        ));
        $userId = $data['userId'];

         //检查权限
        $client = $this->clientLoginAo->checkMustLogin($userId);
        $clientId = $client['clientId'];

        //业务逻辑
        return $this->trollerAo->refreshAll($clientId);
    }

	/**
    * @view json
    */
    public function add(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('userId', 'require'),
            array('shopCommodityId', 'require'),
            array('quantity', 'require'),
        ));
        $userId = $data['userId'];
        $shopCommodityId = $data['shopCommodityId'];
        $quantity = $data['quantity'];

         //检查权限
        $client = $this->clientLoginAo->checkMustLogin($userId);
        $clientId = $client['clientId'];

        //业务逻辑
        return $this->trollerAo->addCommodity($clientId,$shopCommodityId,$quantity);
    }

    /**
    * @view json
    */
    public function set(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('userId', 'require'),
            array('shopCommodity', 'option',array()),
        ));
        $userId = $data['userId'];
        $shopCommodity = $data['shopCommodity'];

        //检查权限
        $client = $this->clientLoginAo->checkMustLogin($userId);
        $clientId = $client['clientId'];

        //业务逻辑
        return $this->trollerAo->setAll($clientId,$shopCommodity);
    }

    /**
     * @view json
     * 购物车根据id进行操作 不更新整张表
     */
    public function set2(){
        if($this->input->is_ajax_request()){
            $data = $this->argv->checkPost(array(
                array('userId', 'require'),
            ));
            $userId = $data['userId'];
            //检查权限
            $client = $this->clientLoginAo->checkMustLogin($userId);
            $clientId = $client['clientId'];

            $shopTrollerId = $this->input->post('shopTrollerId');
            $quantity = $this->input->post('quantity');
            return $this->trollerAo->set2($clientId,$shopTrollerId,$quantity);
        }
    }

    /**
     * @view json
     * 计算积分
     * date:2015.12.03
     */
    public function calScore(){
        $data = $this->argv->checkPost(array(
            array('userId', 'require'),
        ));
        $userId = $data['userId'];
        //检查权限
        $client = $this->clientLoginAo->checkMustLogin($userId);
        $clientId = $client['clientId'];
        return $this->trollerAo->calScore($userId,$clientId);
    }
}
