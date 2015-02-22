<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->library('argv','','argv');
        $this->load->model('shop/commodityDb', 'commodityDb');
        $this->load->model('shop/commodityStateEnum', 'commodityStateEnum');
    }
    
    public function getFixedPrice($price){
        return sprintf("%.2f", $price/100);
    }

    public function search($userId,$dataWhere, $dataLimit){
        $dataWhere['userId'] = $userId;
        $data =  $this->commodityDb->search($dataWhere, $dataLimit);
        foreach($data['data'] as $key=>$value ){
            $data['data'][$key]['priceShow'] = $this->getFixedPrice($data['data'][$key]['price']);
            $data['data'][$key]['oldPriceShow'] = $this->getFixedPrice($data['data'][$key]['oldPrice']);
        }
        return $data;
    }

    public function check($shopCommodity){
        if($shopCommodity['title'] <= 0 )
            throw new CI_MyException(1,'价格不能少于或等于0');
        if($shopCommodity['price'] <= 0 )
            throw new CI_MyException(1,'价格不能少于或等于0');
        if($shopCommodity['oldPrice'] <= 0 )
            throw new CI_MyException(1,'原价格不能少于或等于0');
    }

    public function get($userId,$shopCommodityId){
        $shopCommodity = $this->commodityDb->get($shopCommodityId);
        if( $shopCommodity['userId'] != $userId)
            throw new CI_MyException(1,'非本商城用户无此权限');
        $shopCommodity['priceShow'] = $this->getFixedPrice($shopCommodity['price']);;
        $shopCommodity['oldPriceShow'] = $this->getFixedPrice($shopCommodity['oldPrice']);;
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
        return $this->search($userId,array(
            'shopCommodityClassifyId'=>$shopCommodityClassifyId,
            'state'=>$this->commodityStateEnum->ON_STORAGE
            ),
            array()
        )['data'];
        return $data;
    }

    public function del($userId, $shopCommodityId){
        $shopCommodity = $this->commodityDb->get($shopCommodityId);
        if($shopCommodity['userId'] != $userId)
            throw new CI_MyException(1, '非本商城用户无此权限');

        $this->commodityDb->del($shopCommodityId);
    }

    public function add($userId, $data){
        $data['price'] = $data['priceShow']*100;
        $data['oldPrice'] = $data['oldPriceShow']*100;
        $data['userId'] = $userId;
        unset($data['priceShow']);
        unset($data['oldPriceShow']);
        $this->check($data);
        $this->commodityDb->add($data);
    }

    public function mod($userId, $shopCommodityId, $data){
        $data['price'] = $data['priceShow']*100;
        $data['oldPrice'] = $data['oldPriceShow']*100;
        unset($data['priceShow']);
        unset($data['oldPriceShow']);
        $this->check($data);
        $shopCommodity = $this->commodityDb->get($shopCommodityId);
        if($shopCommodity['userId'] != $userId)
            throw new CI_MyException(1, '非本商城用户无权限操作');

        $this->commodityDb->mod($shopCommodityId, $data);
    }

    public function reduceStock($userId, $shopCommodityId, $quantity){
        $shopCommodity = $this->commodityDb->get($shopCommodityId);
        if($shopCommodity['userId'] != $userId)
            throw new CI_MyException(1, '非本商城用户无此权限');

        $this->commodityDb->reduceStock($shopCommodityId, $quantity);
    }
}























