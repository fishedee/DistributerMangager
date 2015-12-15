<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionConfig extends CI_Controller {

	public function __construct(){
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('distribution/distributionConfigAo','distributionConfigAo');
        $this->load->model('distribution/distributionConfigEnum','distributionConfigEnum');
        $this->load->library('argv','argv');
    }

    /**
     * @view json
     * 获取分销配置状态
     */

    public function getConfigState(){
        return $this->distributionConfigEnum->names;
    }

    /**
     * @view json
     * 检测分销配置
     */
    public function checkConfig(){
        if($this->input->is_ajax_request()){
            //检查输入参数
            $data = $this->argv->checkGet(array(
                array('userId','require'),
            ));
            $userId = $data['userId'];
            return $this->distributionConfigAo->getConfig($userId);
        }
    }

    /**
     * @view json
     * 获取分销配置
     */
    public function getConfig(){
    	if($this->input->is_ajax_request()){
    		//检查权限
	        $user = $this->loginAo->checkMustClient(
	            $this->userPermissionEnum->COMPANY_DISTRIBUTION
	        );
	        $userId = $user['userId'];
	        return $this->distributionConfigAo->getConfig($userId);
    	}
    }

    /**
     * @view json
     * 提交
     */
    public function sub(){
    	if($this->input->is_ajax_request()){
    		//检查权限
	        $user = $this->loginAo->checkMustClient(
	            $this->userPermissionEnum->COMPANY_DISTRIBUTION
	        );
	        $userId = $user['userId'];
	        $data   = $this->input->post('data');
	        return $this->distributionConfigAo->sub($userId,$data);
    	}
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
