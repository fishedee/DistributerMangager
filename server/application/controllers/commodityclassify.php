<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityClassify extends CI_Constroller
{
    public function __construct(){
        parent::__construct();

        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('user/userPermissionEnum', 'userPermissionEnum');
        $this->load->model('shop/commodityClassifyAo', 'commodityClassifyAo');
        $this->load->model('argv', 'argv');
    }
    
    /**
     * @view json
     */
    public function search(){
        //检查输入参数
        $dataWhere = $this->argv->checkGet(array(
            //一级分类, 值为0, 二级分类，值为所属一级分类的ID
            array('parent', 'option')         
        ));

        $dataLimit = $this->argv->checkGet(array(
            array('pageIndex', 'option'),
            array('pageSize', 'option')
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_INTRODUCE
        );
        $userId = $user['userId'];

        //执行业务逻辑
        return $this->commodityClassifyAo->search($userId, $dataWhere, $dataLimit);
    }

	/**
	*@view json
	*/
    public function getByUserId(){
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('userId', 'require')
        ));
        $dataWhere = $this->argv-checkGet(array(
            array('parent'), 'require')
        ));

        $userId = $data['userId'];

        //执行业务逻辑
        return $this->commodityClassifyAo->search($userId, $dataWhere, array());
    }


	/**
	* @view json
	*/
    public function get(){
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('shopCommodityClassifyId', 'require')
        ));
        $shopCommodityClassifyid = $data['shopCommodityClassifyId'];

        //执行业务逻辑
        return $this->companyClassifyAo->get($shopCommodityClassifyId);
    }

	/**
	* @view json
	*/
    public function add(){
        //检查输入参数 
        $data = $this->argv->checkPost(array(
            array('title', 'require'),
            array('icon', 'require'),
            array('parent', 'require')
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMMODITY_CLASSIFY
        );
        $userId = $user['userId'];

        //执行业务逻辑
        $this->commodityClassifyAo->add($userId, $data);
    }

	/**
	* @view json
	*/
    public function del(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('shopCommodityClassifyId', 'require')
        ));
        $shopCommodityClassifyId = $data['shopCommodityClassifyId'];

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMMODITY_CLASSIFY
        );
        $userId = $user['userId'];

        //执行业务逻辑
        $this->commodityClassifyAo->del($userId, $shopCommodityClassifyId);
    }

    public function mod(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('shopCommodityClassifyId', 'require')
        ));
        $shopCommodityClassifyId = $data['shopCommodityClassifyId'];

        $data = $this->argv->checkPost(array(
            array('title', 'require'),
            array('icon', 'require'),
            array('parent', 'require'),
            array('remark', 'require')
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMMODITY_CALSSIFY
        );
        $userId = $user['userId'];
        
        //执行业务逻辑
        $this->commodityClassifyAo->mod($userId, $shopCommodityClassifyId, $data);
    }
}

/* End of file commodity_classify.php */
/* location ./application/controllers/commodity_classify.php */













}

