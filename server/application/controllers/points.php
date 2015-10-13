<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Points extends CI_Controller {

	public function __construct(){
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userAo','userAo');
		$this->load->model('client/clientLoginAo','clientLoginAo');
		$this->load->library('argv','argv');
		$this->load->model('points/pointsAo','pointsAo');
    }

    /**
     * @view json
     * 查看我的积分兑换奖品
     */
    public function search(){
    	if($this->input->is_ajax_request()){
    		//检查输入参数		
			$dataWhere = $this->argv->checkGet(array(
				array('state','option'),
			));

			// var_dump($dataWhere);die;
			
			$dataLimit = $this->argv->checkGet(array(
				array('pageIndex','require'),
				array('pageSize','require'),
			));
			
			//检查权限
	        $userInfo = $this->loginAo->checkMustClient(
	            $this->userPermissionEnum->COMPANY_DISTRIBUTION
	        );
			$vender   = $userInfo['userId'];
			return $this->pointsAo->search($vender,$dataWhere,$dataLimit);
    	}
    }

    /**
     * @view json
     * 获取商品详情
     */
    public function getProductInfo(){
    	if($this->input->is_ajax_request()){
    		//检查权限
	        $user = $this->loginAo->checkMustClient(
	            $this->userPermissionEnum->COMPANY_DISTRIBUTION
	        );
	        $vender = $user['userId'];
	        $productId = $this->input->post('productId');
	        return $this->pointsAo->getProductInfo($vender,$productId);
    	}
    }

    /**
     * @view json
     * 增加商品
     */
    public function add(){
    	if($this->input->is_ajax_request()){
    		//检查权限
	        $user = $this->loginAo->checkMustClient(
	            $this->userPermissionEnum->COMPANY_DISTRIBUTION
	        );
	        $vender = $user['userId'];
	        $data = $this->input->post('data');
	        return $this->pointsAo->add($vender,$data);
    	}
    }

    /**
     * @view json
     * 编辑商品
     */
    public function mod(){
    	if($this->input->is_ajax_request()){
    		//检查权限
	        $user = $this->loginAo->checkMustClient(
	            $this->userPermissionEnum->COMPANY_DISTRIBUTION
	        );
	        $vender = $user['userId'];
	        $data   = $this->input->post('data');
	        $productId = $this->input->post('productId');
	        return $this->pointsAo->mod($vender,$productId,$data);
    	}
    }

    /**
     * @view json
     * 更改上下架状态
     */
    public function change(){
    	if($this->input->is_ajax_request()){
    		//检查权限
	        $user = $this->loginAo->checkMustClient(
	            $this->userPermissionEnum->COMPANY_DISTRIBUTION
	        );
	        $vender = $user['userId'];
	        $productId = $this->input->get('productId');
	        return $this->pointsAo->change($vender,$productId);
    	}
    }

    /**
     * @view json
     * 前端获取信息
     */
    public function fontGetProductInfo(){
    	if($this->input->is_ajax_request()){
    		//检查参数
            $data = $this->argv->checkGet(array(
                array('userId', 'require'),
            ));
            $userId = $data['userId'];
            return $this->pointsAo->fontGetProductInfo($userId);
    	}
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
