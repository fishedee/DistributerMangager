<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Address extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('user/userPermissionEnum', 'userPermissionEnum');
        $this->load->model('user/addressAo', 'addressAo');
        $this->load->library('argv', 'argv');
    }

	/**
	* @view json
	*/
    public function search(){
        //检查输入参数
        $dataWhere = $this->argv->checkGet(array(
            array('name', 'option'),
            array('province', 'option'),
            array('city', 'option'),
            array('district', 'option'),
            array('address', 'option'),
            array('userId', 'option'),
            array('payment', 'option'),
        ));

        $dataLimit = $this->argv->checkGet(array(
            array('pageIndex', 'require'),
            array('pageSize', 'required'),
        ));

        //检查权限
        $user = $this->loginAo->checkMustAdmin();

        //执行业务逻辑
        return $this->addressAo->search($dataWhere, $dataLimit);
    }

	/**
	* @view json
	*/
    public function  getByUserId(){
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('userId', 'require')
        ));
        $userId = $data['userId'];

        //执行业务逻辑
        return $this->addressAo->getByUserId($userId);
    }

	/**
	* @view json
	*/
    public function add(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('name', 'option'),
            array('province', 'option'),
            array('city', 'option'),
            array('district', 'option'),
            array('address', 'option'),
            array('userId', 'option'),
            array('payment', 'option'),
        ));

        //检查权限
        $user = $this->loginAo->checkMustLogin();
        data["userId"] = $user['userId'];

        //执行业务逻辑
        $this->addressAo->add($data);
    }

	/**
	* @view json
	*/
    public function mod(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
           array('userId', 'require')
    }

}
