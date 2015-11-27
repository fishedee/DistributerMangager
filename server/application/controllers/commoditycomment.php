<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CommodityComment extends CI_Controller
{
	public function __construct(){
		parent::__construct();
		$this->load->model('user/loginAo', 'loginAo');
        $this->load->model('client/clientLoginAo', 'clientLoginAo');
        $this->load->model('shop/commodityCommentAo','commodityCommentAo');
	}

	/**
	 * @view json
	 * 查看评论
	 */
	public function search(){
		if($this->input->is_ajax_request()){
			$shopCommodityId = $this->input->get('shopCommodityId');
			//检查输入参数
	        $dataWhere = $this->argv->checkGet(array(
	            array('nickName', 'option'),
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
	        return $this->commodityCommentAo->search($userId,$shopCommodityId,$dataWhere, $dataLimit);
		}
	}

	/**
	 * @view json
	 * 获取评论
	 */
	public function getComment(){
		//检查输入参数
        $data = $this->argv->checkGet(array(
            array('userId', 'require'),
        ));
        $userId = $data['userId'];

        //检查权限
    	$client = $this->clientLoginAo->checkMustLogin($userId);
    	$clientId = $client['clientId'];

    	$shopCommodityId = $this->input->get('shopCommodityId');
    	return $this->commodityCommentAo->getComment($shopCommodityId);
	}

	/**
	 * @view json
	 * @trans true
	 * 评论
	 */
	public function comment(){
		if($this->input->is_ajax_request()){
			//检查输入参数
	        $data = $this->argv->checkGet(array(
	            array('userId', 'require'),
	        ));
	        $userId = $data['userId'];

	        //检查权限
        	$client = $this->clientLoginAo->checkMustLogin($userId);
        	$clientId = $client['clientId'];

        	$shopOrderId = $this->input->post('shopOrderId') ? $this->input->post('shopOrderId') : '201511211142531142332800'; //订单流水号
        	$shopOrderCommodityId = $this->input->post('shopOrderCommodityId') ? $this->input->post('shopOrderCommodityId') : '10144'; //订单明细号
        	$content = $this->input->post('content') ? $this->input->post('content') : '蓝莓好蓝莓';
        	return $this->commodityCommentAo->comment($userId,$clientId,$shopOrderId,$shopOrderCommodityId,$content);
		}
	}

	/**
	 * @view json
	 * 删除评价
	 */
	public function del(){
		if($this->input->is_ajax_request()){
			//检查权限
	        $user = $this->loginAo->checkMustClient(
	            $this->userPermissionEnum->COMPANY_SHOP
	        );
	        $userId = $user['userId'];
	        $commentId = $this->input->get('commentId');
	        return $this->commodityCommentAo->del($userId,$commentId);
		}
	}

}
 ?>
