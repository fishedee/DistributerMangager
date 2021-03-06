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
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
        //date:2015.12.12
        $album  = $this->input->get('album') ? $this->input->get('album') : 0;
        //业务逻辑
        return $this->commodityAo->search($userId,$dataWhere, $dataLimit,$album);
    }
    
	/**
	* @view json
	*/
    public function searchAll(){
        //检查输入参数
        $dataWhere = $this->argv->checkGet(array(
            array('title', 'option'),
            array('introduction', 'option'),
            array('state', 'option'),
            array('appName', 'option'),
            array('shopCommodityClassifyId', 'option'),
        ));
        $dataLimit = $this->argv->checkGet(array(
            array('pageIndex', 'require'),
            array('pageSize', 'require')
        ));

        //业务逻辑
        $dataWhere['isLink'] = 0;
        return $this->commodityAo->searchAll($dataWhere, $dataLimit);
    }

    /**
    * @view json
    */
    public function searchByKeyword(){
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('keyword', 'option')
        ));
        $keyword = $data['keyword'];

        //业务逻辑
        return $this->commodityAo->searchByKeyword($keyword);
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
            $this->userPermissionEnum->COMPANY_SHOP
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
            array('userId', 'require'),
        ));

        $shopCommodityClassifyId = $data['shopCommodityClassifyId'];
        $userId = $data['userId'];
        $rank   = $this->input->get('rank') ? $this->input->get('rank') : '';
        //检查权限
        $client = $this->clientLoginAo->checkMustLogin($userId);

        //业务逻辑
        return $this->commodityAo->getOnStoreByClassify($userId,$shopCommodityClassifyId,$rank);
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
            array('detail', 'require|noxss'),
            array('priceShow', 'require'),
            array('oldPriceShow', 'require'),
            array('inventory', 'require'),
            array('state', 'require'),
            array('remark', 'require'),
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];

        if( $this->loginAo->hasCompanyShopPro($user) == false && 
            $this->commodityAo->getNormalCommodityNum($userId) >= 3 ){
            throw new CI_MyException(1,'普通商城权限自行上传最多3个产品');
        }

        //执行业务逻辑
        $this->commodityAo->add($userId, $data);
    }

    /**
     * @view json
     */
    public function addLink(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('shopLinkCommodityId', 'require'),
            array('shopCommodityClassifyId', 'require'),
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];

        //执行业务逻辑
        $this->commodityAo->addLink($userId, $data['shopLinkCommodityId'],
            $data['shopCommodityClassifyId']);
    }

	/**
	* @view json
	*/
    public function modLink(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('shopCommodityId', 'require'),
            array('shopLinkCommodityId', 'require'),
            array('shopCommodityClassifyId', 'require'),
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];

        //执行业务逻辑
        $this->commodityAo->modLink($userId, $data['shopCommodityId'], 
            $data['shopLinkCommodityId'], $data['shopCommodityClassifyId']);
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
            $this->userPermissionEnum->COMPANY_SHOP
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
            array('detail', 'require|noxss'),
            array('priceShow', 'require'),
            array('oldPriceShow', 'require'),
            array('inventory', 'require'),
            array('state', 'require'),
            array('remark', 'require'),
        ));
        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];

        //执行业务逻辑
        $this->commodityAo->mod($userId, $shopCommodityId, $data);
    }

    /**
    * @view json
    */
    public function moveUp()
    {
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('shopCommodityId','require'),
        ));
        $shopCommodityId = $data['shopCommodityId'];
        
        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
        
        //执行业务逻辑
        $this->commodityAo->move($userId,$shopCommodityId,'up');
    }
    
    /**
    * @view json
    */
    public function moveDown()
    {
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('shopCommodityId','require'),
        ));
        $shopCommodityId = $data['shopCommodityId'];
        
        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];
        
        //执行业务逻辑
        $this->commodityAo->move($userId,$shopCommodityId,'down');
    }

    /**
     * @view json
     * 获取头部信息
     */
    public function getHeaderInfo(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('userId','require'),
        ));
        $userId = $data['userId'];
        return $this->commodityAo->getHeaderInfo($userId);
    }

    /**
     * @view json
     *
     */
    public function mobileGet(){
        if($this->input->is_ajax_request()){
            //检查输入参数
            $data = $this->argv->checkPost(array(
                array('userId','require'),
            ));
            $userId = $data['userId'];
            $type   = $this->input->get('type') ? $this->input->get('type') : 'default';
            $classifyId = $this->input->get('classifyId') ? $this->input->get('classifyId') : '';
            return $this->commodityAo->mobileGet($userId,$type,$classifyId);
        }
    }

    /**
     * @view json
     * 添加相册
     * date:2015.12.07
     */
    public function addAlbum(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('shopCommodityClassifyId', 'require'),
            array('title', 'require'),
            array('icon', 'require'),
            // array('detail', 'require|noxss'),
            array('state', 'require'),
            array('thumbicon','require'),
            // array('thumb','require'),
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];

        if( $this->loginAo->hasCompanyShopPro($user) == false && 
            $this->commodityAo->getNormalCommodityNum($userId) >= 3 ){
            throw new CI_MyException(1,'普通商城权限自行上传最多3个产品');
        }

        //执行业务逻辑
        $this->commodityAo->addAlbum($userId, $data);
    }

    /**
     * @view json
     * 前端获取相册
     * date:2015.12.12
     */
    public function getAlbum(){
        if($this->input->is_ajax_request()){
            //检查输入参数
            $data = $this->argv->checkPost(array(
                array('userId','require'),
            ));
            $userId = $data['userId'];
            $shopCommodityClassifyId = $this->input->post('shopCommodityClassifyId');
            // var_dump($shopCommodityClassifyId);die;
            //检查权限
            $client = $this->clientLoginAo->checkMustLogin($userId);
            return $this->commodityAo->getAlbum($userId,$shopCommodityClassifyId);
        }
    }

    /**
     * @view json
     */
    public function modAlbum(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('shopCommodityId', 'require')
        ));
        $shopCommodityId = $data['shopCommodityId'];

        $data = $this->argv->checkPost(array(
            array('shopCommodityClassifyId', 'require'),
            array('title', 'require'),
            array('icon', 'require'),
            array('thumbicon','require'),
            array('state', 'require'),
            array('remark', 'require'),
        ));
        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_SHOP
        );
        $userId = $user['userId'];

        //执行业务逻辑
        $this->commodityAo->modAlbum($userId, $shopCommodityId, $data);
    }

}

/* End of file commodity.php */
/* Location: ./application/controllers/commodity.php */
