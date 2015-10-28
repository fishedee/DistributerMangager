<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BoardType extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
		$this->load->model('user/loginAo','loginAo');
		$this->load->model('user/userAo','userAo');
		$this->load->model('user/userAppAo','userAppAo');
		$this->load->model('user/userPermissionEnum','userPermissionEnum');
		$this->load->model('user/userTypeEnum','userTypeEnum');
		$this->load->library('argv','argv');
        $this->load->model('board/boardTypeAo','boardTypeAo');
    }

    /**
     * @view json
     * 查询餐桌 
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
			$dataWhere = array();
			return $this->boardTypeAo->search($userId,$dataWhere,$dataLimit);
    	}
    }

    /**
     * @view json
     * 增加餐桌类型
     */
    public function add(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $data   = $this->input->post('data');
            return $this->boardTypeAo->add($userId,$data);
        }
    }

    /**
     * @view json
     * 获取餐桌类型
     */
    public function getType(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $boardTypeId = $this->input->post('boardTypeId');
            return $this->boardTypeAo->getType($userId,$boardTypeId);
        }
    }

    /**
     * @view json
     * 修改
     */
    public function mod(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $data   = $this->input->post('data');
            $boardTypeId = $this->input->post('boardTypeId');
            return $this->boardTypeAo->mod($userId,$boardTypeId,$data);
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
            $boardTypeId = $this->input->get('boardTypeId');
            return $this->boardTypeAo->del($userId,$boardTypeId);
        }
    }

    /**
     * @view json
     * 获取全部类型
     */
    public function getAllType(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            return $this->boardTypeAo->getAllType($userId);
        }
    }

    /**
     * @view json
     * 前台获取类型
     */
    public function fontGetAllType(){
        if($this->input->is_ajax_request()){
            // 检查权限
            $data = $this->argv->checkGet(array(
                array('userId','require')
            ));
            $userId   = $data['userId'];
            return $this->boardTypeAo->fontGetAllType($userId);
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
