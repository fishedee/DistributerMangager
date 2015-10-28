<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Room extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('room/roomAo','roomAo');
        $this->load->library('argv','argv');
        $this->load->model('user/loginAo','loginAo');
    }

    /**
     * @view json
     * 获取餐厅信息
     */
    public function getRoomInfo(){
        //检查权限
        $user = $this->loginAo->checkOrder(
            $this->userPermissionEnum->ORDER_DINNER
        );
        $userId = $user['userId'];
        return $this->roomAo->getRoomInfo($userId);
    }

    /**
     * @view json
     * 新增或修改餐厅信息
     */
    public function amod(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $data   = $this->input->post('data');
            return $this->roomAo->amod($userId,$data);
        }
    }
}
