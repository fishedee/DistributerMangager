<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Troller extends CI_Controller
{
    public function __construct(){
        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('user/userPermissionEnum', 'userPermissionEnum');
        $this->load->model('shop/trollerAo', 'trollerAo');
        $this->load->model('shop/commodityAo', 'commodityAo');
        $this->load->library('argv', 'argv');
    }

	/**
	* @view json
	*/
    public function getByUserId(){
        //检查权限
        $user = $this->loginAo->checkMustLogin();    
        $userId = $user['userId'];
        $query = $this->trollerAo->getByUserId($userId);

        $commodityOfClient = array();
        foreach($query as $key=>$value){
            $commodityOfClient[ $value['clientId'] ] = array();
        }
        foreach($query as $key=>$value){
            $commodity = $this->$commodityAo->get($value['shopCommodityId']);  
            $commodityOfClient[ $value['clientId'] ][] = $commodity;
        }

        $data = array(
            'userId'=>$userId,
            'commodityOfClient'=>$commodityOfClient
        );

        return $data;
    }

	/**
	* @view json
	*/
    public function getByUserIdClientId(){
        //检查输入参数
        $data = $this->argv->checkGet(array(
            array('clientId', 'require')
        ));
        $clientId = $data['clientId'];

        //检查权限
        $user = $this->loginAo->checkMustLogin();
        $userId = $user['userId'];
        $query = $this->trollerAo->getByUserId($userId, $clientId);

        $commodityInTroller = array();
        foreach($query as $key=>$value){
            $commodity = $this->$commodityAo->get($value['shopCommodityId']);
            $commodityInTroller[] = $commodity;
        }

        return $commodityInTroller;
    }


	/**
	* @view json
	*/
    public function add(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('clientId', 'require'),
            array('shopCommodityId', 'require')
        ));

        //检查权限
        $user = $this->loginAo->checkMustLogin();
        $userId = $user['userId'];

        //执行业务逻辑
        return $this->trollerAo->add($userId, $clientId, $shopCommodityId);
    }

    
	/**
	* @view json
	*/
    public function del(){
        //检查输入参数
        $data = $this->argv->checkPost(array(
            array('shopTrollerId', 'require')
        ));
        $shopTrollerId = $data['shopTrollerId'];

        //检查权限
        $user = $this->loginAo->checkMustLogin();
        $userId = $user['userId'];

        //执行业务逻辑
        return $this->trollerAo->del($userId, $shopTrollerId);
    } 

        











    
}
