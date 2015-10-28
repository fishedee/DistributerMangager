<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DishType extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('dish/dishTypeAo','dishTypeAo');
        $this->load->model('dish/dishAo','dishAo');
        $this->load->library('argv','argv');
    }

    /**
     * @view json
     * 查看分类
     */
    public function search(){
        if($this->input->is_ajax_request()){
            //检查输入参数
            $dataWhere = $this->argv->checkGet(array(
                array('title', 'option'),
            ));
            $dataLimit = $this->argv->checkGet(array(
                array('pageIndex', 'require'),
                array('pageSize', 'require')
            ));

             //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            return $this->dishTypeAo->search($userId,$dataWhere,$dataLimit);
        }
    }

    /**
     * @view json
     * 选择上级分类
     */
    public function getAllType(){
        if($this->input->is_ajax_request()){
            $dishTypeId = $this->input->post('dishTypeId');
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            return $this->dishTypeAo->getAllType($userId,$dishTypeId);
        }
    }

    /**
     * @view json
     * 获取详细信息
     */
    public function getDetail(){
        if($this->input->is_ajax_request()){
            $dishTypeId = $this->input->post('dishTypeId');
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            return $this->dishTypeAo->getDetail($userId,$dishTypeId);
        }
    }

    /**
     * @view json
     * 增加分类
     */
    public function addType(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $data = $this->input->post('data');
            return $this->dishTypeAo->addType($userId,$data);
        }
    }

    /**
     * @view json
     * 编辑分类
     */
    public function modType(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $data = $this->input->post('data');
            $dishTypeId = $this->input->post('dishTypeId');
            return $this->dishTypeAo->modType($userId,$dishTypeId,$data);
        }
    }

    /**
     * @view json
     * 删除分类
     */
    public function del(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            $dishTypeId = $this->input->post('dishTypeId');
            return $this->dishTypeAo->del($userId,$dishTypeId);
        }
    }

    /**
     * @view json
     * 获取分类信息
     */
    public function getTypeInfo(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkOrder(
                $this->userPermissionEnum->ORDER_DINNER
            );
            $userId = $user['userId'];
            return $this->dishTypeAo->getTypeInfo($userId);
        }
    }

    /**
     * @view json
     * 前台获取菜单分类
     */
    public function getMenuType(){
        if($this->input->is_ajax_request()){
            //检查输入参数
            $data = $this->argv->checkGet(array(
                array('userId', 'require'),
            ));
            $userId = $data['userId'];
            return $this->dishTypeAo->getMenuType($userId);
        }
    }

}
