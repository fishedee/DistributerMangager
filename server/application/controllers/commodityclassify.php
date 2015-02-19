<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityClassify extends CI_Controller
{
    public function __construct(){
        parent::__construct();

        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('client/clientLoginAo', 'clientLoginAo');
        $this->load->model('user/userPermissionEnum', 'userPermissionEnum');
        $this->load->model('shop/commodityClassifyAo', 'commodityClassifyAo');
        $this->load->library('argv', 'argv');
    }
    
    /**
     * @view json
     */
    public function search(){
        //检查输入参数
        $dataWhere = $this->argv->checkGet(array(
            //一级分类, 值为0, 二级分类，值为所属一级分类的ID
            array('title', 'option'),
            array('remark', 'option'),
            array('parent', 'option')         
        ));

        $dataLimit = $this->argv->checkGet(array(
            array('pageIndex', 'option'),
            array('pageSize', 'option')
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMMODITY_CLASSIFY
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

        $userId = $data['userId'];

        //执行业务逻辑
        return $this->commodityClassifyAo->search($userId, array(), array());
    }

    /**
    * @view json
    */
    public function getPrimaryClassify(){
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('userId', 'require')
        ));

        $userId = $data['userId'];

         //检查权限
        $client = $this->clientLoginAo->checkMustLogin($userId);

         //执行业务逻辑
        return $this->commodityClassifyAo->getPrimaryClassify($userId);
    }

    /**
    * @view json
    */
    public function getSecondaryClassify(){
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('userId', 'require'),
            array('shopCommodityClassifyId', 'require'),
        ));

        $userId = $data['userId'];
        $shopCommodityClassifyId = $data['shopCommodityClassifyId'];

         //检查权限
        $client = $this->clientLoginAo->checkMustLogin($userId);

         //执行业务逻辑
        return $this->commodityClassifyAo->getSecondaryClassify($userId,$shopCommodityClassifyId);
    }

	/**
	* @view json
	*/
    public function get(){
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('shopCommodityClassifyId', 'require')
        ));
        $shopCommodityClassifyId = $data['shopCommodityClassifyId'];

        //执行业务逻辑
        return $this->commodityClassifyAo->get($shopCommodityClassifyId);
    }

    /**
    * @view json
    */
    public function getDetail(){
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('userId', 'require'),
            array('shopCommodityClassifyId', 'require')
        ));
        $userId = $data['userId'];
        $shopCommodityClassifyId = $data['shopCommodityClassifyId'];

        //检查权限
        $client = $this->clientLoginAo->checkMustLogin($userId);

        //执行业务逻辑
        return $this->commodityClassifyAo->get($shopCommodityClassifyId);
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

    /**
    * @view json
    */
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
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMMODITY_CLASSIFY
        );
        $userId = $user['userId'];
        
        //执行业务逻辑
        $this->commodityClassifyAo->mod($userId, $shopCommodityClassifyId, $data);
    }

    /**
    * @view json
    */
    public function moveUp()
    {
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('shopCommodityClassifyId','require'),
        ));
        $shopCommodityClassifyId = $data['shopCommodityClassifyId'];
        
        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMMODITY_CLASSIFY
        );
        $userId = $user['userId'];
        
        //执行业务逻辑
        $this->commodityClassifyAo->move($userId,$shopCommodityClassifyId,'up');
    }
    
    /**
    * @view json
    */
    public function moveDown()
    {
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('shopCommodityClassifyId','require'),
        ));
        $shopCommodityClassifyId = $data['shopCommodityClassifyId'];
        
        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMMODITY_CLASSIFY
        );
        $userId = $user['userId'];
        
        //执行业务逻辑
        $this->commodityClassifyAo->move($userId,$shopCommodityClassifyId,'down');
    }
}

/* End of file commodity_classify.php */
/* location ./application/controllers/commodity_classify.php */


