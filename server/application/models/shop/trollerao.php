<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TrollerAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('shop/trollerDb', 'trollerDb');
        $this->load->model('shop/commodityAo', 'commodityAo');
    }

    private function search($userId,$dataWhere,$dataLimit){
        //取出所有数据
        $dataWhere['userId'] = $userId;
        $data = $this->trollerDb->search($dataWhere,$dataLimit);
        foreach($data['data'] as $key=>$value ){
            $data['data'][$key]['priceShow'] = $this->commodityAo->getFixedPrice($data['data'][$key]['price']);
            $data['data'][$key]['oldPriceShow'] = $this->commodityAo->getFixedPrice($data['data'][$key]['oldPrice']);
        }
        return $data;
    }

    public function getByIds($userId,$clientId,$shopTrollerId){
        //取出所有数据
        $shopTroller = $this->search($userId,array(
            'clientId'=>$clientId,
            'shopTrollerId'=>$shopTrollerId
        ),array())['data'];

        return $shopTroller;
    }

    public function delByIds($userId,$clientId,$shopTrollerId){
        $this->trollerDb->delByUserIdAndClientIdAndShopTrollerId(array(
            'userId'=>$userId,
            'clientId'=>$clientId,
            'shopTrollerId'=>$shopTrollerId
        ));
    }

    public function getAll($userId,$clientId){
        //取出所有数据
        $shopTroller = $this->search($userId,array(
            'clientId'=>$clientId,
        ),array())['data'];

        return $shopTroller;
    }

    public function checkAll($userId,$clientId){
        $shopTroller = $this->getAll($userId,$clientId);
        foreach($shopTroller as $singleShopTroller){
            $this->check($singleShopTroller);
        }
    }

    public function refreshAll($userId,$clientId){
        $shopTroller = $this->getAll($userId,$clientId);
        foreach($shopTroller as $singleShopTroller){
            $this->refresh($singleShopTroller);
        }
    }

    public function setAll($userId,$clientId,$shopTroller){
        foreach($shopTroller as $singleShopTroller){
            $this->setCommodity(
                $userId,
                $clientId,
                $singleShopTroller['shopCommodityId'],
                $singleShopTroller['quantity']
            );
        }

        $shopCommodityId = array_map(function($singleShopTroller){
            return $singleShopTroller['shopCommodityId'];
        },$shopTroller);

        $this->delByUserIdAndClientIdAndNotCommodityId(
            $userId,
            $clientId,
            $shopCommodityId
        );
    }

    public function check($shopTroller){
        $commodity = $this->commodityAo->get(
            $shopTroller['userId'],
            $shopTroller['shopCommodityId']
        );
        if($commodity['title'] != $shopTroller['title']
            || $commodity['icon'] != $shopTroller['icon'] 
            || $commodity['introduction'] != $shopTroller['introduction'] )
            throw new CI_MyException (1,'商品信息发生变更');

        if($commodity['price'] != $shopTroller['price'] )
            throw new CI_MyException (1,'商品价格发生变更');

        if($commodity['inventory'] < $shopTroller['quantity'] )
            throw new CI_MyException (1,'商品库存不足');

        $this->commodityAo->check($shopTroller);
    }

    public function refresh($shopTroller){
        $commodity = $this->commodityAo->get(
            $shopTroller['userId'],
            $shopTroller['shopCommodityId']
        );
        if($commodity['inventory'] < $shopTroller['quantity'])
            $shopTroller['quantity'] = $commodity['inventory'];
        $this->trollerDb->mod($shopTroller['shopTrollerId'],array(
            'userId'=>$commodity['userId'],
            'clientId'=>$commodity['clientId'],
            'shopCommodityId'=>$commodity['shopCommodityId'],
            'title'=>$commodity['title'],
            'icon'=>$commodity['icon'],
            'introduction'=>$commodity['introduction'],
            'price'=>$commodity['price'],
            'oldPrice'=>$commodity['oldPrice'],
            'quantity'=>$shopTroller['quantity']
        ));
    }

    public function addCommodity($userId,$clientId,$shopCommodityId,$quantity){
        $troller =  $this->trollerDb->getByUserIdAndClientIdAndCommodityId(
            $userId,
            $clientId,
            $shopCommodityId
        );
        if( count($troller) == 0 ){
            $commodity = $this->commodityAo->get(
                $userId,
                $shopCommodityId
            );
            $this->trollerDb->add(array(
                'userId'=>$commodity['userId'],
                'clientId'=>$commodity['clientId'],
                'shopCommodityId'=>$commodity['shopCommodityId'],
                'title'=>$commodity['title'],
                'icon'=>$commodity['icon'],
                'introduction'=>$commodity['introduction'],
                'price'=>$commodity['price'],
                'oldPrice'=>$commodity['oldPrice'],
                'quantity'=>$quantity
            ));
        }else{
            $this->trollerDb->modByUserIdAndClientIdAndCommodityId(
                $userId,
                $clientId,
                $shopCommodityId,
                array('quantity'=>$quantity+$troller[0]['quantity'])
            );
        }
    }

    public function delCommodity($userId,$clientId,$shopCommodityId){
        $this->trollerDb->delByUserIdAndClientIdAndCommodityId(
            $userId,
            $clientId,
            $shopCommodityId
        );
    }

    public function setCommodity($userId,$clientId,$shopCommodityId,$quantity){
        $this->trollerDb->modByUserIdAndClientIdAndCommodityId(
            $userId,
            $clientId,
            $shopCommodityId,
            array('quantity'=>$quantity)
        );
    }

}
