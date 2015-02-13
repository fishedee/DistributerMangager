<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TrollerAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('shop/trollerDb', 'trollerDb');
        $this->load->model('shop/commodityAo', 'commodityAo');
    }

    public function getByUserId($userId){
        return $this->trollerDb->getByUserId($userId);
    }

    public function getByUserIdClientId($userId, $clientId){
        return $this->trollerDb->getByUserIdClientId($userId, $clientId);
    }

    public function del($userId, $shopTrollerId){
        $troller = $this->trollerDb->get($shopTrollerId);
        if($troller['userId'] != $userId)
            throw new CI_MyException(1, '此用户无权限删除此商品');

        return $this->trollerDb->del($shopTrollerId);    
    }

    public function add($userId, $clientId, $shopCommodityId){
        $data['userId'] = $userId;
        $data['clientId'] = $clientId;
        $data['shopCommodityId'] = $shopCommodityId;

        $commodity = $this->commodityAo->get($shopCommodityId);
        if($commodity['userId'] != $clientId)
            throw new CI_MyException(1, '非法商品信息无法购买');

        return $this->trollerDb->add($data);
    }
}
