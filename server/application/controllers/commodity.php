<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Commodity extends CI_Constroller 
{
    public function __construct(){
        parent::__construct();
        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('user/userPermissionEnum', 'userPermissionEnum');
        $this->load->model('shop/CommodityAo', 'commodityAo');
        $this->load->library('argv', 'argv');
    }

	/**
	* @view json
	*/
    public function search(){
        //检查输入参数
        $dataWhere = $this->argv->checkGet(array(
            array('title', 'option'),
            array('detail', 'option'),
        ));
        $dataLimit = $this->argv->checkGet(array(
            array('pageIndex', 'require'),
            array('pageSize', 'require')
        ));

        return $this->commodityAo->search($dataWhere, $dataLimit);
    }

    public function getByClassifyId(){
        //检查输入参数
        $dataWhere = $this->argv->checkGet(array(
            array('shopCommodityClassifyId', 'require'),
            array('userId', 'require')
        ));

        $dataLimit = $this->argv->checkGet(array(
            array('pageIndex', 'require'),
            array('pageSize', 'require')
        ));

        //执行业务逻辑
        return $this->commodityAo->search($dataWhere, $dataLimit);
    }

	/**
	* @view json
	*/
    public function get(){
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('commodityId', 'require'),
        ));

        return $this->commodityAo->get($data);
    }

	/**
	* @view json
	*/
    public function add(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('commodityClassifyId', 'require'),
            array('title', 'require'),
            array('introduction', 'require'),
            array('detail', 'require'),
            array('icon', 'require'),
            array('price', 'require'),
            array('inventory', 'require'),
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
            array('commodityId', 'require')   
        ));
        $commodityId = $data['commodityId'];

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermission->COMMODITY_MANAGE
        );
        $userId = $user['userId'];

        //执行业务逻辑
        $this->commodityAo->del($userId, $commodityID);
    }
    
	/**
	* @view json
	*/
    public function mod(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('commodityId', 'require')
        ));
        $commodityId = $data['commodityId'];

        $data = $this->argv->checkPost(array(
            array('shopCommodityClassifyId', 'require'),
            array('title', 'require'),
            array('icon', 'require'),
            array('introduction', 'require'),
            array('detail', 'require'),
            array('price', 'require'),
            array('inventory', 'require')
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMMODITY_MANANGE
        );
        $userId = $user['userId'];

        //执行业务逻辑
        $this->commodityAo->mod($userId, $commodityId, $data);
    }
}

/* End of file commodity.php */
/* Location: ./application/controllers/commodity.php */
