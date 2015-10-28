<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DishComment extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('argv','argv');
		$this->load->model('user/loginAo', 'loginAo');
		$this->load->model('client/clientLoginAo', 'clientLoginAo');
        $this->load->model('dish/dishCommentAo','dishCommentAo');
  	}

    /**
     * @view json
     * 检测评论资格
     */
    public function checkComment(){
        if($this->input->is_ajax_request()){
            //检查输入参数    
            $user = $this->argv->checkGet(array(
                array('userId','option'),
            ));
            $userId = $user['userId'];

            //检查权限
            $client = $this->clientLoginAo->checkMustLogin($userId);
            $clientId = $client['clientId'];

            $orderNo = $this->input->get('orderNo');
            return $this->dishCommentAo->checkComment($userId,$clientId,$orderNo);
        }
    }

    /**
     * @view json
     * 发表评论
     */
    public function publish(){
        if($this->input->is_ajax_request()){
            //检查输入参数    
            $user = $this->argv->checkGet(array(
                array('userId','option'),
            ));
            $userId = $user['userId'];

            //检查权限
            $client = $this->clientLoginAo->checkMustLogin($userId);
            $clientId = $client['clientId'];
            // echo $clientId;die;

            $dishId = $this->input->post('dishId');
            $content= $this->input->post('content');
            $orderNo= $this->input->post('orderNo');
            $degree = $this->input->post('degree');
            $orderDetailId = $this->input->post('orderDetailId');

            return $this->dishCommentAo->publish($userId,$clientId,$dishId,$content,$orderNo,$degree,$orderDetailId);
        }
    }

    /**
     * @view json
     * 查看评论
     */
    public function search(){
        if($this->input->is_ajax_request()){
            //检查输入参数    
            $dataWhere = $this->argv->checkGet(array(
                array('dishId','require'),
            ));

            $dataLimit = $this->argv->checkGet(array(
                array('pageIndex','option'),
                array('pageSize','option'),
            ));
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            return $this->dishCommentAo->search($userId,$dataWhere,$dataLimit);
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */