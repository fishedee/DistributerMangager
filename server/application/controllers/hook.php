<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hook extends CI_Controller {

	public function __construct(){
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->library('argv','argv');
		$this->load->model('hook/hookAo','hookAo');
    }

    /**
     * @view json
     * 查看插件列表
     */
    public function search(){
    	//检查输入参数		
		$dataWhere = $this->argv->checkGet(array(
			array('hookName','option'),
		));
		
		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','option'),
			array('pageSize','option'),
		));
		
		//检查权限
		$user = $this->loginAo->checkMustLogin();
		$userId = $user['userId'];
		return $this->hookAo->search($userId,$dataWhere,$dataLimit);
    }

    /**
     * @view json
     * 增加插件
     */
    public function add(){
    	if($this->input->is_ajax_request()){
    		$data = $this->input->post('data');
    		//检查权限
			$user = $this->loginAo->checkMustLogin();
			$userId = $user['userId'];
			return $this->hookAo->add($userId,$data);
    	}
    }

    /**
     * @view json
     * 获取插件详情
     */
    public function getHook(){
    	if($this->input->is_ajax_request()){
    		$hookId = $this->input->get('hookId');
    		//检查权限
			$user = $this->loginAo->checkMustLogin();
			return $this->hookAo->getHook($hookId);
    	}
    }

    /**
     * @view json
     * 修改插件详情
     */
    public function mod(){
    	if($this->input->is_ajax_request()){
    		$hookId = $this->input->post('hookId');
    		$data   = $this->input->post('data');
    		return $this->hookAo->mod($hookId,$data);
    	}
    }

    /**
     * @view json
     * 权限
     */
    public function power(){
    	//检查输入参数		
		$dataWhere = $this->argv->checkGet(array(
			array('name','option'),
			array('type','option'),
			array('company','option'),
			array('phone','option'),
			array('permissionId','option'),
		));
		
		$dataLimit = $this->argv->checkGet(array(
			array('pageIndex','require'),
			array('pageSize','require'),
		));

		$hookId = $this->input->get('hookId');
		
		//检查权限
		$this->loginAo->checkMustLogin();

		return $this->hookAo->power($dataWhere,$dataLimit,$hookId);
    }

    /**
     * @view json
     * 更改权限
     */
    public function changePower(){
    	if($this->input->is_ajax_request()){
    		//检查权限
			$this->loginAo->checkMustLogin();
			$userId = $this->input->get('userId');
			$hookId = $this->input->get('hookId');
			$status = $this->input->get('status');
			return $this->hookAo->changePower($userId,$hookId,$status);
    	}
    }

    /**
     * @view json
     * 获取权限列表
     */
    public function getUserHook(){
        if($this->input->is_ajax_request()){
            //检查权限
            $this->loginAo->checkMustLogin();
            $userId = $this->session->userdata('userId');
            return $this->hookAo->getUserHook($userId);
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
