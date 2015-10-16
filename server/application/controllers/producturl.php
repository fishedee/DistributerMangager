<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ProductUrl extends CI_Controller {

	public function __construct(){
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userAo','userAo');
		$this->load->library('argv','argv');
        $this->load->model('points/productUrlAo','productUrlAo');
    }

    /**
     * @view json
     * 兑换地址
     */
    public function search(){
        if($this->input->is_ajax_request()){
            //检查输入参数    
            $dataWhere = $this->argv->checkGet(array(
                array('name','option'),
                array('state','option'),
            ));

            $dataLimit = $this->argv->checkGet(array(
                array('pageIndex','require'),
                array('pageSize','require'),
            ));

            //检查权限
            $user = $this->loginAo->checkMustClient(
                $this->userPermissionEnum->COMPANY_DISTRIBUTION
            );
            $dataWhere['userId'] = $user['userId'];

            //执行业务逻辑
            return $this->productUrlAo->search($dataWhere,$dataLimit);
        }
    }

    /**
     * @view json
     */
    public function getUrlInfo(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkMustClient(
                $this->userPermissionEnum->COMPANY_DISTRIBUTION
            );
            $userId = $user['userId'];
            $urlId  = $this->input->get('urlId');
            return $this->productUrlAo->getUrlInfo($userId,$urlId);
        }
    }

    /**
     * @view json
     */
    public function addOnce(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkMustClient(
                $this->userPermissionEnum->COMPANY_DISTRIBUTION
            );
            $userId = $user['userId'];
            $data   = $this->input->post('data');
            return $this->productUrlAo->addOnce($userId,$data);
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
