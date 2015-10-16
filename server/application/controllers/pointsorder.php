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
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
