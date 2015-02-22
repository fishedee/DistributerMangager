<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Commodity extends CI_Controller 
{
    public function __construct(){
        parent::__construct();
        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('client/clientLoginAo', 'clientLoginAo');
        $this->load->model('user/userPermissionEnum', 'userPermissionEnum');
        $this->load->model('shop/commodityAo', 'commodityAo');
        $this->load->model('shop/commodityStateEnum', 'commodityStateEnum');
        $this->load->library('argv', 'argv');
    }

    /**
    * @view json
    */
    public function getState(){
        return $this->commodityStateEnum->names;
    }

	/**
	* @view json
	*/
    public function search(){
        //检查输入参数
        $dataWhere = $this->argv->checkGet(array(
            array('title', 'option'),
            array('introduction', 'option'),
            array('state', 'option'),
            array('shopCommodityClassifyId', 'option'),
        ));
        $dataLimit = $this->argv->checkGet(array(
            array('pageIndex', 'require'),
            array('pageSize', 'require')
        ));

         //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMMODITY_MANAGE
        );
        $userId = $user['userId'];

        //业务逻辑
        return $this->commodityAo->search($userId,$dataWhere, $dataLimit);
    }

    /**
    * @view json
    */
    public function get(){
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('shopCommodityId', 'require'),
        ));

        $shopCommodityId = $data['shopCommodityId'];

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMMODITY_MANAGE
        );
        $userId = $user['userId'];

        //业务逻辑
        return $this->commodityAo->get($userId,$shopCommodityId);
    }

    /**
    * @view json
    */
    public function getByCommodityClassify(){
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('shopCommodityClassifyId', 'require'),
            array('userId', 'require')
        ));

        $shopCommodityClassifyId = $data['shopCommodityClassifyId'];
        $userId = $data['userId'];

        //检查权限
        $client = $this->clientLoginAo->checkMustLogin($userId);

        //业务逻辑
        return $this->commodityAo->getOnStoreByClassify($userId,$shopCommodityClassifyId);
    }

	/**
	* @view json
	*/
    public function getDetail(){
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('shopCommodityId', 'require'),
            array('userId', 'require')
        ));

        $shopCommodityId = $data['shopCommodityId'];
        $userId = $data['userId'];

        //检查权限
        $client = $this->clientLoginAo->checkMustLogin($userId);

        //业务逻辑
        return $this->commodityAo->get($userId,$shopCommodityId);
    }

	/**
	* @view json
	*/
    public function add(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('shopCommodityClassifyId', 'require'),
            array('title', 'require'),
            array('icon', 'require'),
            array('introduction', 'require'),
            array('detail', 'require'),
            array('priceShow', 'require'),
            array('oldPriceShow', 'require'),
            array('inventory', 'require'),
            array('state', 'require'),
            array('remark', 'require'),
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMMODITY_MANAGE
        );
        $userId = $user['userId'];

        //执行业务逻辑
        $this->commodityAo->add($userId, $data);
    }

	/**
	* @view json
	*/
    public function del(){
        $data = $this->argv->checkPost(array(
            array('shopCommodityId', 'require')   
        ));
        $shopCommodityId = $data['shopCommodityId'];

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMMODITY_MANAGE
        );
        $userId = $user['userId'];

        //执行业务逻辑
        $this->commodityAo->del($userId, $shopCommodityId);
    }
    
	/**
	* @view json
	*/
    public function mod(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('shopCommodityId', 'require')
        ));
        $shopCommodityId = $data['shopCommodityId'];

        $data = $this->argv->checkPost(array(
            array('shopCommodityClassifyId', 'require'),
            array('title', 'require'),
            array('icon', 'require'),
            array('introduction', 'require'),
            array('detail', 'require'),
            array('priceShow', 'require'),
            array('oldPriceShow', 'require'),
            array('inventory', 'require'),
            array('state', 'require'),
            array('remark', 'require'),
        ));
        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMMODITY_MANAGE
        );
        $userId = $user['userId'];

        //执行业务逻辑
        $this->commodityAo->mod($userId, $shopCommodityId, $data);
    }

}

/* End of file commodity.php */
/* Location: ./application/controllers/commodity.php */
