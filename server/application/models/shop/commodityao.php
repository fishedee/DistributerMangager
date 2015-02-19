<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('shop/commodityDb', 'commodityDb');
        $this->load->model('shop/commodityStateEnum', 'commodityStateEnum');
    }
    private function getFixedPrice($price){
        return sprintf("%.2f", $price/100);
    }

    public function search($userId,$dataWhere, $dataLimit){
        $dataWhere['userId'] = $userId;
        $data =  $this->commodityDb->search($dataWhere, $dataLimit);
        foreach($data['data'] as $key=>$value ){
            $data['data'][$key]['price'] = $this->getFixedPrice($data['data'][$key]['price']);
            $data['data'][$key]['oldPrice'] = $this->getFixedPrice($data['data'][$key]['oldPrice']);
        }
        return $data;
    }

    public function get($userId,$shopCommodityId){
        $shopCommodity = $this->commodityDb->get($shopCommodityId);
        if( $shopCommodity['userId'] != $userId)
            throw new CI_MyException(1,'非本商城用户无此权限');
        $shopCommodity['price'] = $this->getFixedPrice($shopCommodity['price']);;
        $shopCommodity['oldPrice'] = $this->getFixedPrice($shopCommodity['oldPrice']);;
        return $shopCommodity;
    }

    public function getByIds($userId,$shopCommodityId){
        $data = $this->search($userId,array('shopCommodityId'=>$shopCommodityId),array())['data'];
        $map = array();
        foreach($data as $singleData){
            $map[$singleData['shopCommodityId']] = $singleData;
        }
        return $map;
    }

    public function getOnStoreByClassify($userId,$shopCommodityClassifyId){
        $dataWhere = array();
        $dataWhere['userId'] = $userId;
        $dataWhere['shopCommodityClassifyId'] = $shopCommodityClassifyId;
        $dataWhere['state'] = $this->commodityStateEnum->ON_STORAGE;
        $data =  $this->commodityDb->search($dataWhere, array())['data'];
        foreach($data as $key=>$value ){
            $data[$key]['price'] = $this->getFixedPrice($data[$key]['price']);
            $data[$key]['oldPrice'] = $this->getFixedPrice($data[$key]['oldPrice']);
        }
        return $data;
    }

    public function del($userId, $shopCommodityId){
        $shopCommodity = $this->commodityDb->get($shopCommodityId);
        if($shopCommodity['userId'] != $userId)
            throw new CI_MyException(1, '非本商城用户无此权限');

        $this->commodityDb->del($shopCommodityId);
    }

    public function add($userId, $data){
        $data['price'] *= 100;
        $data['oldPrice'] *= 100;
        $data['userId'] = $userId;
        $this->commodityDb->add($data);
    }

    public function mod($userId, $shopCommodityId, $data){
        $data['price'] *= 100;
        $data['oldPrice'] *= 100;
        $shopCommodity = $this->commodityDb->get($shopCommodityId);
        if($shopCommodity['userId'] != $userId)
            throw new CI_MyException(1, '非本商城用户无权限操作');

        $this->commodityDb->mod($shopCommodityId, $data);
    }
}























