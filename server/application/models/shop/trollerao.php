<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TrollerAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('shop/trollerDb', 'trollerDb');
        $this->load->model('shop/commodityAo', 'commodityAo');
    }

    private function getQuantityInInventory($userId,$shopCommodityId,$quantity){
        $shopCommodity = $this->commodityAo->get($userId,$shopCommodityId);
        if($shopCommodity['inventory'] >= $quantity)
            return $quantity;
        else
            return $shopCommodity['inventory'];
    }
    private function change($userId,$clientId,$shopCommodityId,$quantity,$isAddOrigin){
        //获取购物车商品的原来信息
        $shopTroller = $this->trollerDb->search(array(
            'userId'=>$userId,
            'clientId'=>$clientId,
            'shopCommodityId'=>$shopCommodityId
        ),array())['data'];

        if( count($shopTroller) == 0 ){
            $oldQuantity = 0;
            $shopTrollerId = 0;  
        }else{
            $oldQuantity = $shopTroller[0]['quantity'];
            $shopTrollerId = $shopTroller[0]['shopTrollerId'];
        }
        //计算新的quantity
        $quantity = intval($quantity);
        if( $quantity <= 0 )
            $quantity = 0;
        if( $isAddOrigin)
            $quantity += $oldQuantity;
        else
            $quantity = $quantity;
        $quantity = $this->getQuantityInInventory($userId,$shopCommodityId,$quantity);
        $result = array(
            'quantity'=>$quantity,
            'oldQuantity'=>$oldQuantity
        );
        //计算quantity执行不同的操作
        if( $quantity == 0  ){
            //指定的quantity<=0
            if($shopTrollerId == 0)
                return $result;
            $this->trollerDb->del($shopTrollerId);
        }else{
            if( $shopTrollerId == 0 ){
                //指定的quantity>0且不存在原数据
                $this->trollerDb->add(array(
                    'userId'=>$userId,
                    'clientId'=>$clientId,
                    'shopCommodityId'=>$shopCommodityId,
                    'quantity'=>$quantity
                ));
            }else{
                //指定的quantity>0且存在原数据
                if($quantity == $oldQuantity)
                    return $result;
                $this->trollerDb->mod($shopTrollerId,array(
                    'quantity'=>$quantity
                ));
            }
        }
        return $result;
    }


    public function getAll($userId,$clientId){
        //取出所有数据
        $shopCommodity = $this->trollerDb->search(array(
            'userId'=>$userId,
            'clientId'=>$clientId,
        ),array())['data'];

        //根据库存限制恰当设置购物车的数量
        foreach($shopCommodity as $key=>$value){
            $shopCommodity[$key]['quantity'] = $this->change(
                $userId,
                $clientId,
                $shopCommodity[$key]['shopCommodityId'],
                $shopCommodity[$key]['quantity'],
                false
            )['quantity'];
        }

        //获取购物车中商品的信息
        $shopCommodityInfo = $this->commodityAo->getByIds($userId,array_map(function($single){
            return $single['shopCommodityId'];
        },$shopCommodity));
        $shopCommodity = array_map(function($single)use($shopCommodityInfo){
            return array_merge($single,$shopCommodityInfo[$single['shopCommodityId']]);
        },$shopCommodity);

        return $shopCommodity;
    }

    public function delAll($userId,$clientId){
       return $this->delByUserIdAndClientId($userId,$clientId);
    }

    public function setAll($userId,$clientId,$shopCommodity){
        $isChange = false;

        foreach($shopCommodity as $single){
            $quantity = $this->setCommodity($userId,$clientId,$single['shopCommodityId'],$single['quantity']);
            if( $quantity != $single['quantity'] )
                $isChange = true;
        }

        $shopCommodityId = array_map(function($single){
            return $single['shopCommodityId'];
        },$shopCommodity);
        $this->trollerDb->delByUserIdAndClientIdAndNotCommodityId($userId,$clientId,$shopCommodityId);
        return $isChange;
    }

    public function addCommodity($userId,$clientId,$shopCommodityId,$quantity){
        $result = $this->change($userId,$clientId,$shopCommodityId,$quantity,true);
        return ($result['quantity'] - $result['oldQuantity'] >= 0 )? ($result['quantity'] - $result['oldQuantity'] ): 0;
    }

    public function delCommodity($userId,$clientId,$shopCommodityId){
       return $this->change($userId,$clientId,$shopCommodityId,0,false)['quantity'];
    }

    public function setCommodity($userId,$clientId,$shopCommodityId,$quantity){
        return $this->change($userId,$clientId,$shopCommodityId,$quantity,false)['quantity'];
    }

}
