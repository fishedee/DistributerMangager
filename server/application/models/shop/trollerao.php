<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TrollerAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('shop/trollerDb', 'trollerDb');
        $this->load->model('shop/commodityAo', 'commodityAo');
        $this->load->model('common/commonErrorEnum', 'commonErrorEnum');
        $this->load->model('user/userAppAo','userAppAo');
        //date:2015.12.03
        $this->load->model('distribution/distributionConfigAo','distributionConfigAo');
        $this->load->model('client/clientAo','clientAo');
    }

    private function search($dataWhere,$dataLimit){
        //取出所有数据
        $data = $this->trollerDb->search($dataWhere,$dataLimit);
        foreach($data['data'] as $key=>$value ){
            $data['data'][$key]['priceShow'] = $this->commodityAo->getFixedPrice($data['data'][$key]['price']);
            $data['data'][$key]['oldPriceShow'] = $this->commodityAo->getFixedPrice($data['data'][$key]['oldPrice']);
        }

        //取出库存等信息
        foreach( $data['data'] as $key=>$singleShopTroller ){
            $commodity = $this->commodityAo->getByOnlyId(
                $singleShopTroller['shopCommodityId']
            );
            $userApp = $this->userAppAo->get(
                $commodity['userId']
            );
            $data['data'][$key]['inventory'] = $commodity['inventory'];
            $data['data'][$key]['appName'] = $userApp['appName'];
            $data['data'][$key]['userId'] = $userApp['userId'];
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

    public function getCartInfo($clientId,$shopTrollerId){
        $data = $this->trollerDb->getCartInfo($clientId,$shopTrollerId);
        foreach($data as $key=>$value ){
            $data[$key]['priceShow'] = $this->commodityAo->getFixedPrice($data[$key]['price']);
            $data[$key]['oldPriceShow'] = $this->commodityAo->getFixedPrice($data[$key]['oldPrice']);
        }

        //取出库存等信息
        foreach( $data as $key=>$singleShopTroller ){
            $commodity = $this->commodityAo->getByOnlyId(
                $singleShopTroller['shopCommodityId']
            );
            $userApp = $this->userAppAo->get(
                $commodity['userId']
            );
            $data[$key]['inventory'] = $commodity['inventory'];
            $data[$key]['appName'] = $userApp['appName'];
            $data[$key]['userId'] = $userApp['userId'];
        }
        
        return $data;
    }

    public function delByIds($clientId,$shopTrollerId){
        return $this->trollerDb->delByClientIdAndShopTrollerId(
            $clientId,
            $shopTrollerId
        );
    }

    public function getAll($clientId){
        //取出所有数据
        $shopTroller = $this->search(array(
            'clientId'=>$clientId,
        ),array())['data'];

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
            throw new CI_MyException (1,'商品信息发生变更',$this->commonErrorEnum->SHOP_CART_CHECK_ERROR);

        if($commodity['price'] != $shopTroller['price'] )
            throw new CI_MyException (1,'商品价格发生变更,请重新选择',$this->commonErrorEnum->SHOP_CART_CHECK_ERROR);

        if($commodity['oldPrice'] != $shopTroller['oldPrice'] )
            throw new CI_MyException (1,'商品原价格发生变更',$this->commonErrorEnum->SHOP_CART_CHECK_ERROR);

        if($commodity['inventory'] < $shopTroller['quantity'] )
            throw new CI_MyException (1,'商品库存不足',$this->commonErrorEnum->SHOP_CART_CHECK_ERROR);

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

    public function set2($clientId,$shopTrollerId,$quantity){
        $shopTroller = $this->getByIds($clientId,$shopTrollerId);
        $shopTroller = $shopTroller[0];
        if($shopTroller['clientId'] != $clientId){
            throw new CI_MyException(1,'您无权操作');
        }
        if($quantity <= 0){
            return $this->delByIds($clientId,$shopTrollerId);
        }
        $shopTroller['quantity'] = $quantity;
        $this->check($shopTroller);
        $data['quantity'] = $quantity;
        return $this->trollerDb->mod($shopTrollerId,$data);
    }

    /**
     * @计算积分
     * date:2015.12.03
     */
    public function calScore($userId,$clientId){
        $shopTroller = $this->getAll($clientId);
        $config = $this->distributionConfigAo->getConfig($userId);
        if($config != 0 && $config['scorePrice']){
            $clientInfo = $this->clientAo->get($userId,$clientId);
            $score = $clientInfo['score'];           //用户目前积分
            $scorePrice = $config['scorePrice'];     //抵消1元所需积分
            $scorePercent = $config['scorePercent']; //积分抵消百分比
            $sum = 0;
            foreach ($shopTroller as $key => $value) {
                $sum += $value['priceShow'] * $value['quantity'];
            }
            $maxPrice = floor($sum * $scorePercent * 0.01); //系统允许最大的抵消金额
            $maxScore= floor($maxPrice * $scorePrice);
            if($score >= $maxScore){
                $data['remark'] = '您可以使用'.$maxScore.'积分抵消'.$maxPrice.'元';
            }else{
                $needScore = $score;
                $disPrice  = floor($needScore/$scorePrice);
                $data['remark'] = '您可以使用'.$needScore.'积分抵消'.$disPrice.'元';
            }
            $data['score'] = 1;
        }else{
            $data['remark']= '商家不支持积分兑换';
            $data['score'] = 0;
        }
        return $data;
    }

}