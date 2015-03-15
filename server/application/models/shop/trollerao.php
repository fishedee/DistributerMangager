<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TrollerAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('shop/trollerDb', 'trollerDb');
        $this->load->model('shop/commodityAo', 'commodityAo');
        $this->load->model('common/commonErrorEnum', 'commonErrorEnum');
        $this->load->model('user/userAppAo','userAppAo');
    }

    private function search($dataWhere,$dataLimit){
        //取出所有数据
        $data = $this->trollerDb->search($dataWhere,$dataLimit);
        foreach($data['data'] as $key=>$value ){
            $data['data'][$key]['priceShow'] = $this->commodityAo->getFixedPrice($data['data'][$key]['price']);
            $data['data'][$key]['oldPriceShow'] = $this->commodityAo->getFixedPrice($data['data'][$key]['oldPrice']);
        }
        return $data;
    }

    public function getByIds($clientId,$shopTrollerId){
        //取出所有数据
        $shopTroller = $this->search(array(
            'clientId'=>$clientId,
            'shopTrollerId'=>$shopTrollerId
        ),array())['data'];

        return $shopTroller;
    }

    public function delByIds($clientId,$shopTrollerId){
        $this->trollerDb->delByClientIdAndShopTrollerId(
            $clientId,
            $shopTrollerId
        );
    }

    public function getAll($clientId){
        //取出所有数据
        $shopTroller = $this->search(array(
            'clientId'=>$clientId,
        ),array())['data'];

        //取出库存信息
        foreach( $shopTroller as $key=>$singleShopTroller ){
            $commodity = $this->commodityAo->getByOnlyId(
                $singleShopTroller['shopCommodityId']
            );
            $userApp = $this->userAppAo->get(
                $commodity['userId']
            );
            $shopTroller[$key]['inventory'] = $commodity['inventory'];
            $shopTroller[$key]['appName'] = $userApp['appName'];
            $shopTroller[$key]['userId'] = $userApp['userId'];
        }
        
        return $shopTroller;
    }

    public function checkAll($clientId){
        $shopTroller = $this->getAll($clientId);
        foreach($shopTroller as $singleShopTroller){
            $this->check($singleShopTroller);
        }
    }

    public function refreshAll($clientId){
        $shopTroller = $this->getAll($clientId);
        foreach($shopTroller as $singleShopTroller){
            $this->refresh($singleShopTroller);
        }
    }

    public function setAll($clientId,$shopTroller){
        foreach($shopTroller as $singleShopTroller){
            $this->setCommodity(
                $clientId,
                $singleShopTroller['shopCommodityId'],
                $singleShopTroller['quantity']
            );
        }

        $shopCommodityId = array_map(function($singleShopTroller){
            return $singleShopTroller['shopCommodityId'];
        },$shopTroller);

        $this->trollerDb->delByClientIdAndNotCommodityId(
            $clientId,
            $shopCommodityId
        );
    }

    public function check($shopTroller){
        $commodity = $this->commodityAo->getByOnlyId(
            $shopTroller['shopCommodityId']
        );
        if($commodity['title'] != $shopTroller['title']
            || $commodity['icon'] != $shopTroller['icon'] 
            || $commodity['introduction'] != $shopTroller['introduction'] )
            throw new CI_MyException ($this->commonErrorEnum->SHOP_CART_CHECK_ERROR,'商品信息发生变更');

        if($commodity['price'] != $shopTroller['price'] )
            throw new CI_MyException ($this->commonErrorEnum->SHOP_CART_CHECK_ERROR,'商品价格发生变更');

        if($commodity['oldPrice'] != $shopTroller['oldPrice'] )
            throw new CI_MyException ($this->commonErrorEnum->SHOP_CART_CHECK_ERROR,'商品原价格发生变更');

        if($commodity['inventory'] < $shopTroller['quantity'] )
            throw new CI_MyException ($this->commonErrorEnum->SHOP_CART_CHECK_ERROR,'商品库存不足');

        $this->commodityAo->check($shopTroller);
    }

    public function refresh($shopTroller){
        $commodity = $this->commodityAo->getByOnlyId(
            $shopTroller['shopCommodityId']
        );
        if($commodity['inventory'] < $shopTroller['quantity'])
            $shopTroller['quantity'] = $commodity['inventory'];
        $this->trollerDb->mod($shopTroller['shopTrollerId'],array(
            'shopCommodityId'=>$commodity['shopCommodityId'],
            'title'=>$commodity['title'],
            'icon'=>$commodity['icon'],
            'introduction'=>$commodity['introduction'],
            'price'=>$commodity['price'],
            'oldPrice'=>$commodity['oldPrice'],
            'quantity'=>$shopTroller['quantity']
        ));
    }

    public function addCommodity($clientId,$shopCommodityId,$quantity){
        //拉出真正的商品
        $commodity = $this->commodityAo->getByOnlyId(
            $shopCommodityId
        );
        $shopCommodityId = $commodity['originShopCommodityId'];

        //在真正的商品上操作
        $troller =  $this->trollerDb->getByClientIdAndCommodityId(
            $clientId,
            $shopCommodityId
        );
        if( count($troller) == 0 ){
            $this->trollerDb->add(array(
                'clientId'=>$clientId,
                'shopCommodityId'=>$shopCommodityId,
                'title'=>$commodity['title'],
                'icon'=>$commodity['icon'],
                'introduction'=>$commodity['introduction'],
                'price'=>$commodity['price'],
                'oldPrice'=>$commodity['oldPrice'],
                'quantity'=>$quantity
            ));
        }else{
            $this->trollerDb->modByClientIdAndCommodityId(
                $clientId,
                $shopCommodityId,
                array('quantity'=>$quantity+$troller[0]['quantity'])
            );
        }
    }

    public function delCommodity($clientId,$shopCommodityId){
        $this->trollerDb->delByClientIdAndCommodityId(
            $clientId,
            $shopCommodityId
        );
    }

    public function setCommodity($clientId,$shopCommodityId,$quantity){
        $this->trollerDb->modByClientIdAndCommodityId(
            $clientId,
            $shopCommodityId,
            array('quantity'=>$quantity)
        );
    }

}