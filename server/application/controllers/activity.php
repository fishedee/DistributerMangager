<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Activity extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('activity/activityAo','activityAo');
		$this->load->model('client/clientLoginAo','clientLoginAo');
		$this->load->library('argv','argv');
    }

    /**
     * @view json
     * 查看报名
     */
    public function search(){
    	if($this->input->is_ajax_request()){
    		//检查权限
			$userId = $this->session->userdata('userId');
    		//检查输入参数		
			$dataWhere = $this->argv->checkGet(array(
				array('phone','option'),
				array('name','option'),
				array('address','option')
			));
			
			$dataLimit = $this->argv->checkGet(array(
				array('pageIndex','require'),
				array('pageSize','require'),
			));
			//执行业务逻辑
			return $this->activityAo->search($userId,$dataWhere,$dataLimit);
    	}
    }

    /**
     * @view json
     * 活动报名
     */
    public function enList(){
    	if($this->input->is_ajax_request()){
    		//检查输入参数
	        $data = $this->argv->checkPost(array(
	            array('userId','require'),
	        ));
	        $userId = $data['userId'];
	        //检查权限
        	// $client = $this->clientLoginAo->checkMustLogin($userId);
        	// $clientId = $client['clientId'];
        	$clientId = 0;
        	$data = $this->input->post();
        	return $this->activityAo->enList($userId,$clientId,$data);
    	}
    }

    /**
     * @view json
     * 检测是否报名
     */
    public function checkEnList(){
    	//检查输入参数
        $data = $this->argv->checkPost(array(
            array('userId','require'),
        ));
        $userId = $data['userId'];
        //检查权限
    	// $client = $this->clientLoginAo->checkMustLogin($userId);
    	// $clientId = $client['clientId'];
    	$clientId = 1;
    	$result = $this->activityAo->checkEnList($userId,$clientId);
    	if($result){
    		return 1;
    	}else{
    		return 0;
    	}
    }

    /**
     * @view json
     * 获取已报名的信息
     */
    public function enListed(){
    	if($this->input->is_ajax_request()){
    		//检查输入参数
	        $data = $this->argv->checkPost(array(
	            array('userId','require'),
	        ));
	        $userId = $data['userId'];
	        //检查权限
        	// $client = $this->clientLoginAo->checkMustLogin($userId);
        	return $this->activityAo->enListed($userId);
    	}
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
