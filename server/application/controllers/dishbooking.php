<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DishBooking extends CI_Controller {

	public function __construct(){
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('client/clientLoginAo','clientLoginAo');
		$this->load->library('argv','argv');
		$this->load->model('board/bookingAo','bookingAo');
    }

    /**
     * @view json
     * 查看预定
     */
    public function search(){
    	if($this->input->is_ajax_request()){
    		//检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];

			$dataLimit = $this->argv->checkGet(array(
				array('pageIndex','require'),
				array('pageSize','require'),
			));
			$dataWhere = $this->argv->checkGet(array(
                array('boardTypeId','option'),
                array('state','option'),
            ));
			return $this->bookingAo->search($userId,$dataWhere,$dataLimit);
    	}
    }

    /**
     * @view json
     * 预定
     */
    public function booking(){
    	if($this->input->is_ajax_request()){
    		//检查输入参数
            $data = $this->argv->checkGet(array(
                array('userId', 'require'),
            ));
            $userId = $data['userId'];
            // 检查权限
            $client = $this->clientLoginAo->checkMustLogin($userId);
            $clientId = $client['clientId'];
            $data   = $this->input->post();
            return $this->bookingAo->booking($userId,$clientId,$data);
    	}
    }

    /**
     * @view json
     * 删除
     */
    public function del(){
    	if($this->input->is_ajax_request()){
    		//检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $bookingId = $this->input->get('bookingId');
            return $this->bookingAo->del($userId,$bookingId);
    	}
    }

    /**
     * @view json
     * 受理
     */
    public function accept(){
    	if($this->input->is_ajax_request()){
    		//检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $bookingId = $this->input->get('bookingId');
            return $this->bookingAo->accept($userId,$bookingId);
    	}
    }

    /**
     * @view json
     * 检测拒绝资格
     */
    public function checkForbid(){
    	if($this->input->is_ajax_request()){
    		//检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $bookingId = $this->input->get('bookingId');
            return $this->bookingAo->checkForbid($userId,$bookingId);
    	}
    }

    /**
     * @view json
     * 拒绝
     */
    public function forbid(){
    	if($this->input->is_ajax_request()){
    		//检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $bookingId = $this->input->post('bookingId');
            $data = $this->input->post();
            return $this->bookingAo->forbid($userId,$bookingId,$data);
    	}
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
