<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PointsOrder extends CI_Controller {

	public function __construct(){
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userAo','userAo');
		$this->load->model('client/clientLoginAo','clientLoginAo');
		$this->load->library('argv','argv');
		$this->load->model('points/pointsOrderAo','pointsOrderAo');
    }

    /**
     * @view json
     * @trans true
     * 兑换
     */
    public function exchange(){
        if($this->input->is_ajax_request()){
            //检查参数
            $data = $this->argv->checkGet(array(
                array('userId', 'require'),
            ));
            $userId = $data['userId'];
            $productId = $this->input->post('productId');
            $client = $this->clientLoginAo->checkMustLogin($userId);
            $clientId = $client['clientId'];
            return $this->pointsOrderAo->exchange($userId,$productId,$clientId);
        }
    }

    /**
     * @view json
     * 我的奖品
     */
    public function myProduct(){
        if($this->input->is_ajax_request()){
            //检查参数
            $data = $this->argv->checkGet(array(
                array('userId', 'require'),
            ));
            $userId = $data['userId'];
            $client = $this->clientLoginAo->checkMustLogin($userId);
            $clientId = $client['clientId'];
            return $this->pointsOrderAo->myProduct($userId,$clientId);
        }
    }

    /**
     * @view json
     * 查看兑换记录
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
            return $this->pointsOrderAo->search($vender,$dataWhere,$dataLimit);
        }
    }

    /**
     * @view json
     * 受理
     */
    public function accept(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkMustClient(
                $this->userPermissionEnum->COMPANY_DISTRIBUTION
            );
            $vender = $user['userId'];
            $orderId = $this->input->post('orderId');
            return $this->pointsOrderAo->accept($vender,$orderId);
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
