<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Address extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('client/clientLoginAo', 'clientLoginAo');
        $this->load->model('address/addressAo', 'addressAo');
        $this->load->library('argv', 'argv');
    }

	/**
	* @view json
	*/
    public function  getMyAddress(){
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('userId', 'require')
        ));
        $userId = $data['userId'];

        //检查权限
        $client = $this->clientLoginAo->checkMustLogin($userId);

        //执行业务逻辑
        return $this->addressAo->get($client['clientId']);
    }

	/**
	* @view json
	*/
    public function modMyAddress(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('userId', 'require')
        ));
        $userId = $data['userId'];

        $data = $this->argv->checkPost(array(
            array('name', 'require'),
            array('province', 'require'),
            array('city', 'require'),
            array('address', 'require'),
            array('phone', 'require'),
            array('payment', 'require'),
        ));

        //检查权限
        $client = $this->clientLoginAo->checkMustLogin($userId);

        //执行业务逻辑
        $this->addressAo->mod($client['clientId'],$data);
    }

}
